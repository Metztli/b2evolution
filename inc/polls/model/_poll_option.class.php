<?php
/**
 * This file implements the PollOption class.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
*
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('_core/model/dataobjects/_dataobject.class.php', 'DataObject');


/**
 * PollOption Class
 *
 * @package evocore
 */
class PollOption extends DataObject
{
    public $pqst_ID;

    public $option_text;

    public $order;

    /**
     * Constructor
     *
     * @param object table Database row
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct('T_polls__option', 'popt_', 'popt_ID');

        if ($db_row != null) {	// Get poll option data from DB:
            $this->ID = $db_row->popt_ID;
            $this->pqst_ID = $db_row->popt_pqst_ID;
            $this->option_text = $db_row->popt_option_text;
            $this->order = $db_row->popt_order;
        } else {	// Set default poll option data for new poll:
        }
    }

    /**
     * Get delete cascade settings
     *
     * @return array
     */
    public static function get_delete_cascades()
    {
        return [
            [
                'table' => 'T_polls__answer',
                'fk' => 'pans_popt_ID',
                'msg' => T_('%d poll answers'),
            ],
        ];
    }

    /**
     * Load data from Request form fields.
     *
     * @return boolean true if loaded data seems valid.
     */
    public function load_from_Request($poll_question_ID = null)
    {
        if ($poll_question_ID !== null) {	// Poll question ID:
            $this->set('pqst_ID', $poll_question_ID);
        }

        // Option text:
        param('popt_option_text', 'string', '');
        param_check_not_empty('popt_option_text', T_('Please enter the text for the poll\'s option.'));
        $this->set_from_Request('option_text');

        // Order:
        param('popt_order', 'integer', '');
        if ($this->ID > 0) {	// Require an order only for existing poll options:
            param_check_not_empty('popt_order', T_('Please enter the order for the poll\'s option.'));
        }
        $this->set_from_Request('order');

        return ! param_errors_detected();
    }

    /**
     * Insert object into DB based on previously recorded changes.
     *
     * @return boolean true on success
     */
    public function dbinsert()
    {
        global $DB;

        $DB->begin('SERIALIZABLE');

        if (! $this->get('order')) {	// If order has not been defined on form:
            // Get max order of the poll options:
            $order_SQL = new SQL('Get max order of the poll options');
            $order_SQL->SELECT('MAX( popt_order )');
            $order_SQL->FROM('T_polls__option');
            $order_SQL->WHERE('popt_pqst_ID = ' . $this->get('pqst_ID'));
            $max_order = $DB->get_var($order_SQL);

            // Set default order as next after max:
            $this->set('order', intval($max_order) + 1);
        }

        if (parent::dbinsert()) { // The poll option was inserted successful:
            $DB->commit();
            return true;
        }

        // Could not insert the poll object:
        $DB->rollback();
        return false;
    }

    /**
     * Get name of the question. It is a string as first 200 chars of the option text:
     *
     * @return string
     */
    public function get_name()
    {
        return strmaxlen($this->get('option_text'), 200);
    }

    /**
     * Vote on this poll option by user
     *
     * @param integer User ID of specific user
     * @return boolean TRUE on successful voting
     */
    public function vote($user_ID = null)
    {
        global $current_User, $DB;

        if (! empty($user_ID)) {
            $UserCache = &get_UserCache();
            $user = &$UserCache->get_by_ID($user_ID, false, false);
        } elseif (is_logged_in()) {
            $user = $current_User;
        }

        if (empty($user)) {
            return false;
        }

        // Set new vote or update the previous vote for current user:
        $result = $DB->query('REPLACE INTO T_polls__answer ( pans_pqst_ID, pans_user_ID, pans_popt_ID )
				VALUES ( ' . $DB->quote($this->pqst_ID) . ', ' . $DB->quote($user->ID) . ', ' . $DB->quote($this->ID) . ' )');

        return $result ? true : false;
    }
}
