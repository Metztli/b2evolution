<?php
/**
 * This file implements the user_links_Widget class.
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

load_class('widgets/model/_widget.class.php', 'ComponentWidget');

/**
 * ComponentWidget Class
 *
 * A ComponentWidget is a displayable entity that can be placed into a Container on a web page.
 *
 * @package evocore
 */
class user_links_Widget extends ComponentWidget
{
    public $icon = 'users';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct($db_row, 'core', 'user_links');
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('user-links-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('User Social Links');
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
        return T_('Display social links for a specific User.');
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        load_funcs('files/model/_image.funcs.php');

        $r = array_merge([
            'title' => [
                'label' => T_('Block title'),
                'note' => T_('Title to display in your skin.'),
                'size' => 40,
                'defaultvalue' => '',
            ],
            'login' => [
                'label' => T_('User login'),
                'note' => T_('leave blank to use author of current post or current collection.'),
                'size' => 20,
                'defaultvalue' => '',
            ],
            'icon_colors' => [
                'label' => T_('Icon color'),
                'type' => 'checklist',
                'options' => [
                    ['text',      T_('Use for normal text'), 0],
                    ['bg',        T_('Use for normal background'), 0],
                    ['hovertext', T_('Use for hover text'), 0],
                    ['hoverbg',   T_('Use for hover background'), 1/* default checked */],
                ],
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    /**
     * Display the widget!
     *
     * @param array MUST contain at least the basic display params
     */
    public function display($params)
    {
        global $DB, $Item, $Collection, $Blog;

        $this->init_display($params);

        // Initialise css classes for icons depending on widget setting
        $icon_colors_classes = '';
        if (! empty($this->disp_params['icon_colors'])) { // If at least one color status is selected
            foreach ($this->disp_params['icon_colors'] as $class_name => $is_selected) {
                if (! empty($is_selected)) {
                    $icon_colors_classes .= ' ufld__' . $class_name . 'color';
                }
            }
        }

        $r = '';

        $target_User = &$this->get_target_User();
        if (empty($target_User)) { // No user detected
            $r .= get_rendering_error(sprintf(T_('User %s not found.'), '<b>' . format_to_output($this->disp_params['login'], 'text') . '</b>'));
        }

        if (! empty($target_User)) { // If we really have found user
            // Get all user extra field values with type "url"
            $url_fields = $target_User->userfields_by_type('url');
            if (count($url_fields)) {
                $r .= '<div class="ufld_icon_links">';
                foreach ($url_fields as $field) {
                    $r .= '<a href="' . $field->uf_varchar . '"' . (empty($icon_colors_classes) ? '' : ' class="ufld_' . $field->ufdf_code . $icon_colors_classes . '"') . '>'
                            . '<span class="' . $field->ufdf_icon_name . '"></span>'
                        . '</a>';
                }
                $r .= '</div>';
            }
        }

        if (empty($r)) {	// Nothing to display
            $this->display_debug_message();
            return true;
        }

        echo $this->disp_params['block_start'];

        $this->disp_title();

        echo $this->disp_params['block_body_start'];

        echo $r;

        echo $this->disp_params['block_body_end'];

        echo $this->disp_params['block_end'];

        return true;
    }

    /**
     * Get User that should be used for this widget now
     *
     * @return object User
     */
    public function &get_target_User()
    {
        if ($this->target_User === null) {	// Initialize target User only first time:
            if (empty($this->disp_params['login'])) {	// No defined user in widget settings:
                $this->target_User = &parent::get_target_User();
            } else {	// Try to get user by login from DB:
                $UserCache = &get_UserCache();
                $this->target_User = &$UserCache->get_by_login($this->disp_params['login']);
            }
        }

        return $this->target_User;
    }

    /**
     * Maybe be overriden by some widgets, depending on what THEY depend on..
     *
     * @return array of keys this widget depends on
     */
    public function get_cache_keys()
    {
        global $Collection, $Blog;

        $cache_keys = [
            'wi_ID' => $this->ID, // Have the widget settings changed ?
            'set_coll_ID' => $Blog->ID, // Have the settings of the blog changed ? (ex: new owner, new skin)
        ];

        if ($target_User = &$this->get_target_User()) {
            $cache_keys['user_ID'] = $target_User->ID; // Has the target User changed? (name, avatar, etc..)
        }

        return $cache_keys;
    }
}
