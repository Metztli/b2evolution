<?php
/**
 * This file implements the Wiki Tables plugin for b2evolution
 *
 * Wiki Tables
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package plugins
 * @ignore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * @package plugins
 */
class wikitables_plugin extends Plugin
{
    public $code = 'b2evWiTa';

    public $name = 'Wiki Tables';

    public $priority = 15;

    public $version = '7.2.5';

    public $group = 'rendering';

    public $short_desc;

    public $long_desc;

    public $help_topic = 'wiki-tables-plugin';

    public $number_of_installs = 1;

    /**
     * Init
     */
    public function PluginInit(&$params)
    {
        // Load the parsers for wiki tables
        require_once(dirname(__FILE__) . '/_sanitizer.inc.php');
        require_once(dirname(__FILE__) . '/_string_utils.inc.php');
        require_once(dirname(__FILE__) . '/_utf_normal_util.inc.php');

        $this->short_desc = T_('Wiki Tables converter');
        $this->long_desc = T_('You can create tables with accepted format:<br />
{| table start<br />
<br />
|- table row<br />
<br />
|} table end<br />
See manual for more.');
    }

    /**
     * Define here default collection/blog settings that are to be made available in the backoffice.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::get_coll_setting_definitions()}.
     */
    public function get_coll_setting_definitions(&$params)
    {
        $default_params = [
            'default_post_rendering' => 'opt-out',
        ];

        $tmp_params = array_merge($params, $default_params);
        return parent::get_coll_setting_definitions($tmp_params);
    }

    /**
     * Perform rendering
     *
     * @param array Associative array of parameters
     *   'data': the data (by reference). You probably want to modify this.
     *   'format': see {@link format_to_output()}. Only 'htmlbody' and 'entityencoded' will arrive here.
     * @return boolean true if we can render something for the required output format
     */
    public function RenderItemAsHtml(&$params)
    {
        $content = &$params['data'];

        // Parse wiki tables
        if (stristr($content, '<code') !== false || stristr($content, '<pre') !== false || strstr($content, '```') !== false) { // Call replace_content() on everything outside code/pre:
            $content = callback_on_non_matching_blocks(
                $content,
                '~(```|<(code|pre)[^>]*>).*?(\1|</\2>)~is',
                [$this, 'parse_tables']
            );
        } else { // No code/pre blocks, replace on the whole thing
            $content = $this->parse_tables($content);
        }

        return true;
    }

    /**
     * Parse tables
     *
     * @param string Content
     * @return string Content
     */
    public function parse_tables($content)
    {
        $lines = explode("\n", $content);
        $out = '';
        $td_history = []; // Is currently a td tag open?
        $last_tag_history = []; // Save history of last lag activated (td, th or caption)
        $tr_history = []; // Is currently a tr tag open?
        $tr_attributes = []; // history of tr attributes
        $has_opened_tr = []; // Did this table open a <tr> element?
        $indent_level = 0; // indent level of the table

        foreach ($lines as $outLine) {
            $line = trim($outLine);

            if ($line === '') { // empty line, go to next line
                $out .= $outLine . "\n";
                continue;
            }

            $first_character = $line[0];
            $matches = [];

            if (preg_match('/^(:*)\{\|(.*)$/', $line, $matches)) { // First check if we are starting a new table
                $indent_level = strlen($matches[1]);

                $attributes = $this->fix_tag_attributes($matches[2], 'table');

                $outLine = str_repeat('<dl><dd>', $indent_level) . "<table{$attributes}>";
                array_push($td_history, false);
                array_push($last_tag_history, '');
                array_push($tr_history, false);
                array_push($tr_attributes, '');
                array_push($has_opened_tr, false);
            } elseif (count($td_history) == 0) { // Don't do any of the following
                $out .= $outLine . "\n";
                continue;
            } elseif (substr($line, 0, 2) === '|}') { // We are ending a table
                $line = '</table>' . substr($line, 2);
                $last_tag = array_pop($last_tag_history);

                if (! array_pop($has_opened_tr)) {
                    $line = "<tr><td></td></tr>{$line}";
                }

                if (array_pop($tr_history)) {
                    $line = "</tr>{$line}";
                }

                if (array_pop($td_history)) {
                    $line = "</{$last_tag}>{$line}";
                }
                array_pop($tr_attributes);
                $outLine = $line . str_repeat('</dd></dl>', $indent_level);
            } elseif (substr($line, 0, 2) === '|-') { // Now we have a table row
                $line = preg_replace('#^\|-+#', '', $line);

                // Whats after the tag is now only attributes
                $attributes = $this->fix_tag_attributes($line, 'tr');
                array_pop($tr_attributes);
                array_push($tr_attributes, $attributes);

                $line = '';
                $last_tag = array_pop($last_tag_history);
                array_pop($has_opened_tr);
                array_push($has_opened_tr, true);

                if (array_pop($tr_history)) {
                    $line = '</tr>';
                }

                if (array_pop($td_history)) {
                    $line = "</{$last_tag}>{$line}";
                }

                $outLine = $line;
                array_push($tr_history, false);
                array_push($td_history, false);
                array_push($last_tag_history, '');
            } elseif ($first_character === '|' || $first_character === '!' || substr($line, 0, 2) === '|+') { // This might be cell elements, td, th or captions
                if (substr($line, 0, 2) === '|+') {
                    $first_character = '+';
                    $line = substr($line, 1);
                }

                $line = substr($line, 1);

                if ($first_character === '!') {
                    $line = str_replace('!!', '||', $line);
                }

                // Split up multiple cells on the same line.
                $cells = explode('||', $line);

                $outLine = '';

                // Loop through each table cell
                foreach ($cells as $cell) {
                    $previous = '';
                    if ($first_character !== '+') {
                        $tr_after = array_pop($tr_attributes);
                        if (! array_pop($tr_history)) {
                            $previous = "<tr{$tr_after}>\n";
                        }
                        array_push($tr_history, true);
                        array_push($tr_attributes, '');
                        array_pop($has_opened_tr);
                        array_push($has_opened_tr, true);
                    }

                    $last_tag = array_pop($last_tag_history);

                    if (array_pop($td_history)) {
                        $previous = "</{$last_tag}>\n{$previous}";
                    }

                    if ($first_character === '|') {
                        $last_tag = 'td';
                    } elseif ($first_character === '!') {
                        $last_tag = 'th';
                    } elseif ($first_character === '+') {
                        $last_tag = 'caption';
                    } else {
                        $last_tag = '';
                    }

                    array_push($last_tag_history, $last_tag);

                    // A cell could contain both parameters and data
                    $cell_data = explode('|', $cell, 2);

                    // Set attribute to allow render markdown inside tables:
                    $markdown_attribute = ' markdown="1"';

                    if (strpos($cell_data[0], '[[') !== false) {
                        $cell = "{$previous}<{$last_tag}{$markdown_attribute}>{$cell}";
                    } elseif (count($cell_data) == 1) {
                        $cell = "{$previous}<{$last_tag}{$markdown_attribute}>{$cell_data[0]}";
                    } else {
                        $attributes = $this->fix_tag_attributes($cell_data[0], $last_tag);
                        $cell = "{$previous}<{$last_tag}{$markdown_attribute}{$attributes}>{$cell_data[1]}";
                    }

                    $outLine .= $cell;
                    array_push($td_history, true);
                }
            }
            $out .= $outLine . "\n";
        }

        // Closing open td, tr && table
        while (count($td_history) > 0) {
            if (array_pop($td_history)) {
                $out .= "</td>\n";
            }
            if (array_pop($tr_history)) {
                $out .= "</tr>\n";
            }
            if (! array_pop($has_opened_tr)) {
                $out .= "<tr><td></td></tr>\n";
            }

            $out .= "</table>\n";
        }

        if (substr($out, -1) === "\n") { // Remove trailing line-ending (b/c)
            $out = substr($out, 0, -1);
        }

        if ($out === "<table>\n<tr><td></td></tr>\n</table>") { // special case: don't return empty table
            $out = '';
        }

        return $out;
    }

    /**
     * Event handler: Called at the beginning of the skin's HTML HEAD section.
     *
     * Use this to add any HTML HEAD lines (like CSS styles or links to resource files (CSS, JavaScript, ..)).
     *
     * @param array Associative array of parameters
     */
    public function SkinBeginHtmlHead(&$params)
    {
        global $Collection, $Blog;

        if (! isset($Blog) || (
            $this->get_coll_setting('coll_apply_rendering', $Blog) == 'never' &&
            $this->get_coll_setting('coll_apply_comment_rendering', $Blog) == 'never'
        )) {	// Don't load css/js files when plugin is not enabled
            return;
        }

        $this->require_css_async('wikitables.css', false, 'footerlines');
    }

    /**
     * Event handler: Called when ending the admin html head section.
     *
     * @param array Associative array of parameters
     * @return boolean did we do something?
     */
    public function AdminEndHtmlHead(&$params)
    {
        $this->SkinBeginHtmlHead($params);
    }

    /**
     * Fix tag attributes
     *
     * @param string Attributes, e.g. 'style="color:green;" data-display-condition="cur=eur"'
     * @param string Element name, e.g. 'tr', 'td', 'table'
     * @return string Fixed attributes
     */
    public function fix_tag_attributes($attributes, $element)
    {
        $extended_attributes = '';

        if ($attributes !== '' &&
            in_array($element, ['table', 'tr', 'td', 'th']) &&
            preg_match_all('#(^|\s)data-[^\s]+="[^"]*"+#', $attributes, $data_attributes)) {	// Allow extended attributes for several tags:
            $extended_attributes = implode('', $data_attributes[0]);
        }

        // Fix attributes by Sanitizer and append data-*
        return $extended_attributes . Sanitizer::fixTagAttributes($attributes, $element);
    }
}
