<?php
/**
 * This file implements the Flagged Item List (Flagged Items) Widget class.
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

load_class('widgets/widgets/_coll_item_list.widget.php', 'coll_item_list_Widget');

/**
 * Flagged Item List (Flagged Items) Widget Class
 *
 * A ComponentWidget is a displayable entity that can be placed into a Container on a web page.
 *
 * @package evocore
 */
class coll_flagged_list_Widget extends coll_item_list_Widget
{
    public $icon = 'flag';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        ComponentWidget::__construct($db_row, 'core', 'coll_flagged_list');
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        // This is derived from coll_item_list_Widget, so we DO NOT ADD ANY param here!
        $r = parent::get_param_definitions($params);
        // We only change the defaults and hide some params.
        $r['title']['defaultvalue'] = T_('Flagged Items');
        $r['flagged']['defaultvalue'] = 1;
        $r['flagged']['no_edit'] = true;

        // Hide the 2 last orderby fields with order direction:
        for ($order_index = 1; $order_index <= 2 /* The number of orderby fields - 1 */; $order_index++) {
            $r['orderby_' . $order_index . '_begin_line']['no_edit'] = true;
            $r['order_by_' . $order_index]['no_edit'] = true;
            $r['order_dir_' . $order_index]['no_edit'] = true;
            $r['orderby_' . $order_index . '_end_line']['no_edit'] = true;
        }

        return $r;
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('flagged-item-list-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('Flagged Item List');
    }

    /**
     * Get a very short desc. Used in the widget list.
     */
    public function get_short_desc()
    {
        return format_to_output($this->disp_params['title']);
    }

    /**
     * Get short description
     */
    public function get_desc()
    {
        return T_('Simplified Item list for listing flagged items.');
    }
}
