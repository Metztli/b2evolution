<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage touch
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class touch_Skin extends Skin
{
    /**
     * Skin version
     * @var string
     */
    public $version = '7.1.2';

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Touch';
    }

    /**
     * What evoSkins API does has this skin been designed with?
     *
     * This determines where we get the fallback templates from (skins_fallback_v*)
     * (allows to use new markup in new b2evolution versions)
     */
    public function get_api_version()
    {
        return 5;
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'mobile';
    }

    /**
     * Get the container codes of the skin main containers
     *
     * This should NOT be protected. It should be used INSTEAD of file parsing.
     * File parsing should only be used if this function is not defined
     *
     * @return array Array which overrides default containers; Empty array means to use all default containers.
     */
    public function get_declared_containers()
    {
        // Array to override default containers from function get_skin_default_containers():
        // - Key is widget container code;
        // - Value: array( 0 - container name, 1 - container order ),
        //          NULL - means don't use the container, WARNING: it(only empty/without widgets) will be deleted from DB on changing of collection skin or on reload container definitions.
        return [
            'page_top' => null,
            'header' => null,
            'menu' => null,
            'front_page_secondary_area' => null,
            'item_list' => null,
            'item_in_list' => null,
            'item_single_header' => null,
            'item_page' => null,
            'sidebar' => null,
            'sidebar_2' => null,
            'footer' => null,
            'user_profile_left' => null,
            'user_profile_right' => null,
            'mobile_footer' => [NT_('Mobile: Footer'), 110],
            'mobile_navigation_menu' => [NT_('Mobile: Navigation Menu'), 120],
            'mobile_tools_menu' => [NT_('Mobile: Tools Menu'), 130],
        ];
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'display_post_date' => [
                'label' => T_('Post date'),
                'note' => T_('Display the date of each post'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox' => [
                'label' => T_('Colorbox Image Zoom'),
                'note' => T_('Check to enable javascript zooming on images (using the colorbox script)'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_post' => [
                'label' => T_('Voting on Post Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_post_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_comment' => [
                'label' => T_('Voting on Comment Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_comment_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_user' => [
                'label' => T_('Voting on User Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_user_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'gender_colored' => [
                'label' => T_('Display gender'),
                'note' => T_('Use colored usernames to differentiate men & women.'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
            ],
            'bubbletip' => [
                'label' => T_('Username bubble tips'),
                'note' => T_('Check to enable bubble tips on usernames'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
            ],
            'autocomplete_usernames' => [
                'label' => T_('Autocomplete usernames'),
                'note' => T_('Check to enable auto-completion of usernames entered after a "@" sign in the comment forms'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    /**
     * Get ready for displaying the skin.
     *
     * This may register some CSS or JS...
     */
    public function display_init()
    {
        // call parent:
        parent::display_init();		// We pass NO params. This gives up the default Skins API v5 behavior.

        add_js_headline('var touch_skin_switch_confirm_text = "' . TS_('Switch to regular view? \n \n You can switch back again in the footer.') . '";');
        $this->require_js('js/core.js');
        require_js('navigation.js', 'blog');
    }
}
