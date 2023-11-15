<?php
/**
 * This file implements the FilterSQL class.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


load_class('_core/model/db/_sql.class.php', 'SQL');

/**
 * FilterSQL class: help constructing queries for filtering lists.
 */
class FilterSQL extends SQL
{
    /**
     * Array with joined tables,
     * Used to don't join same table twice in order to avoid error "Not unique table/alias"
     */
    public $joined_tables = [];

    /**
     * Use the preset filter query in it is not defined for function $this->filter_query( $query )
     * @var array
     */
    public $preset_filter_query;

    /**
     * Constructor.
     */
    public function __construct($title = null)
    {
        parent::__construct($title);
    }

    /**
     * Extends the FROM clause.
     *
     * @param string should typically start with INNER JOIN or LEFT JOIN
     */
    public function FROM_add($from_add)
    {
        if (preg_match('#JOIN (T_.+) ON#', $from_add, $m)) {	// If some table is joined
            if (in_array($m[1], $this->joined_tables)) {	// Skip this table joining because it was already done before:
                return;
            }
            // Store the joined table in this array to don't join it twice:
            $this->joined_tables[] = $m[1];
        }

        parent::FROM_add($from_add);
    }

    /**
     * Add a rule for a filter query
     *
     * @param string Field ID
     * @param string|array String for single value, Array for multiple values
     * @param string Operator
     * @param string Condition for grouped rules: 'AND', 'OR'
     * @param string Field type, 'string' by default, use 'date' for proper converting between mysql and locale date formats
     */
    public function add_filter_rule($field, $values, $operator = null, $group_condition = null, $type = null)
    {
        if (! isset($this->preset_filter_query)) {	// Initialize query array:
            $this->preset_filter_query = [
                // Use AND condition by default:
                'condition' => 'AND',
                // Decide this valid because it can be used only by developer:
                'valid' => true,
            ];
        }

        if (! isset($this->preset_filter_query['rules'])) {	// Initialize rules array:
            $this->preset_filter_query['rules'] = [];
        }

        // Convert operator alias to jQuery QueryBuilder format:
        $operator = get_querybuilder_operator($operator);

        if (is_array($values) && $group_condition !== null) {	// Append new grouped rules:
            $rule = [
                'condition' => $group_condition,
                'rules' => [],
            ];
            foreach ($values as $value) {	// Append new grouped rules:
                $group_rule = [
                    'id' => $field,
                    'value' => $value,
                ];
                if ($operator !== null) {
                    $group_rule['operator'] = $operator;
                }
                if ($type !== null) {
                    $group_rule['type'] = $type;
                }
                $rule['rules'][] = $group_rule;
            }
        } else {	// Append new rule:
            $rule = [
                'id' => $field,
                'value' => $values,
            ];
            if ($operator !== null) {
                $rule['operator'] = $operator;
            }
            if ($type !== null) {
                $rule['type'] = $type;
            }
        }

        $this->preset_filter_query['rules'][] = $rule;
    }

    /**
     * Restrict by query
     *
     * @param string Query in JSON format
     */
    public function filter_query($query)
    {
        if (isset($this->preset_filter_query)) {	// Use a preset filter query:
            if (empty($query)) {	// Use preset filter query completely if the requested filters are empty:
                $query = $this->preset_filter_query;
            } else {	// Merge preset filter query which are not defined in current query:
                $query = json_decode($query);
                $query_rules = [];
                if (isset($query->rules) && is_array($query->rules)) {
                    foreach ($query->rules as $query_rule) {
                        if (isset($query_rule->id)) {
                            $query_rules[] = $query_rule->id;
                        }
                    }
                } else {
                    $query->rules = [];
                }

                if (isset($this->preset_filter_query['rules']) && is_array($this->preset_filter_query['rules'])) {	// Find preset filters which are not in current query:
                    foreach ($this->preset_filter_query['rules'] as $preset_rule) {
                        if (isset($preset_rule['id']) && ! in_array($preset_rule['id'], $query_rules)) {	// Add preset filter rule to the current query:
                            $query->rules[] = (object) $preset_rule;
                        }
                    }
                }
            }

            $query = json_encode($query);
            set_param('filter_query', $query);
        }

        $json_query = json_decode($query);

        if ($json_query === null || ! isset($json_query->valid) || $json_query->valid !== true) {	// Wrong query, Stop here:
            return;
        }

        // Get SQL conditions from JSON query object:
        $sql_conditions = $this->get_filter_conditions($json_query);

        if (! empty($sql_conditions)) {	// Use only not empty conditions:
            $this->WHERE_and($sql_conditions);
        }
    }

    /**
     * Get filter conditions
     *
     * @param object Query in JSON format
     * @return string
     */
    public function get_filter_conditions($query)
    {
        if (! isset($query->condition, $query->rules) ||
            ! in_array($query->condition, ['AND', 'OR']) ||
            empty($query->rules)) {	// Wrong json query params, Skip it:
            return;
        }

        $sql_conditions = [];
        foreach ($query->rules as $r => $rule) {
            if (isset($rule->rules) && is_array($rule->rules)) {	// This is a group of conditions, Run this function recursively:
                $sql_condition = $this->get_filter_conditions($rule);
                if (! empty($sql_condition)) {	// Use only correct conditions:
                    $sql_conditions[] = '( ' . $sql_condition . ' )';
                }
            } else {	// This is a single condition:
                if (! isset($rule->operator)) {	// Use '=' as default operator:
                    $rule->operator = 'equal';
                }
                if (! isset($rule->id, $rule->value, $rule->operator) ||
                    ! method_exists($this, 'filter_field_' . $rule->id)) {	// Skip it if wrong rule or method doesn't exist for filterting by the rule field:
                    continue;
                }
                $sql_condition = $this->{'filter_field_' . $rule->id}($rule->value, $rule->operator);
                if (! empty($sql_condition)) {	// Use only correct conditions:
                    $sql_conditions[] = $sql_condition;
                }
            }
        }

        return empty($sql_conditions) ? '' : implode(' ' . $query->condition . ' ', $sql_conditions);
    }

    /**
     * Get SQL condition for "WHERE" clause
     *
     * @param string Field name in DB
     * @param string Value
     * @param string Operator in format of jQuery plugin QueryBuilder
     * @return string
     */
    public function get_where_condition($field_name, $value, $operator)
    {
        global $DB;

        $value_prefix = '';
        $value_suffix = '';

        switch ($operator) {
            case 'equal':
                $sql_operator = '=';
                break;
            case 'not_equal':
                $sql_operator = '!=';
                break;
            case 'less':
                $sql_operator = '<';
                break;
            case 'less_or_equal':
                $sql_operator = '<=';
                break;
            case 'greater':
                $sql_operator = '>';
                break;
            case 'greater_or_equal':
                $sql_operator = '>=';
                break;
            case 'between':
                $sql_operator = ['BETWEEN', 'AND'];
                break;
            case 'not_between':
                $sql_operator = ['NOT BETWEEN', 'AND'];
                break;
            case 'contains':
                $sql_operator = 'LIKE';
                $value_prefix = '%';
                $value_suffix = '%';
                break;
            case 'not_contains':
                $sql_operator = 'NOT LIKE';
                $value_prefix = '%';
                $value_suffix = '%';
                break;
            default:
                debug_die('Unknown filter condition operator "' . $operator . '" for the field "' . $field_name . '"');
        }

        // Build SQL condition from given operator and value:
        $sql_where_condition = $field_name;
        if (is_array($sql_operator)) {	// Multiple operators and values:
            foreach ($sql_operator as $i => $sql_operator_item) {
                $sql_where_condition .= ' ' . $sql_operator_item . ' ' . $DB->quote($value_prefix . $value[$i] . $value_suffix);
            }
        } else {	// Single operator and value:
            $sql_where_condition .= ' ' . $sql_operator . ' ' . $DB->quote($value_prefix . $value . $value_suffix);
        }

        if (in_array($sql_operator, ['!=', 'NOT LIKE'])) {	// Additional SQL fix for several operators:
            $sql_where_condition = '( ' . $field_name . ' IS NULL OR ' . $sql_where_condition . ' )';
        }

        return $sql_where_condition;
    }
}
