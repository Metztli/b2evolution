<?php
/**
 * This file implements the coll_avatar_Widget class.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2008 by Daniel HAHLER - {@link http://daniel.hahler.de/}.
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('widgets/model/_widget.class.php', 'ComponentWidget');

/**
 * coll_avatar_Widget Class.
 *
 * This displays the blog owner's avatar.
 *
 * @package evocore
 */
class coll_avatar_Widget extends ComponentWidget
{
    public $icon = 'user-circle';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct($db_row, 'core', 'coll_avatar');
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('profile-picture-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('Profile picture (Avatar)');
    }

    /**
     * Get short description
     */
    public function get_desc()
    {
        return T_('Display the profile picture of the blog owner.');
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param array local params
     *  - 'size': Size definition, see {@link $thumbnail_sizes}. E.g. 'fit-160x160'.
     */
    public function get_param_definitions($params)
    {
        load_funcs('files/model/_image.funcs.php');

        $r = array_merge([
            'thumb_size' => [
                'type' => 'select',
                'label' => T_('Image size'),
                'options' => get_available_thumb_sizes(),
                'note' => sprintf( /* TRANS: %s is a config variable name */ T_('List of available image sizes is defined in %s.'), '$thumbnail_sizes'),
                'defaultvalue' => 'fit-160x160',
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
        global $cat_modifier;
        global $Collection, $Blog;

        $this->init_display($params);

        $owner_User = &$Blog->get_owner_User();

        // START DISPLAY:
        echo $this->disp_params['block_start'];

        // Display title if requested
        $this->disp_title();

        echo $this->disp_params['block_body_start'];

        echo $owner_User->get_link([
            'link_to' => 'userpage',  // TODO: make configurable $this->disp_params['link_to']
            'link_text' => 'avatar',
            'thumb_size' => $this->disp_params['thumb_size'],
        ]);

        echo $this->disp_params['block_body_end'];

        echo $this->disp_params['block_end'];

        return true;
    }

    /**
     * Maybe be overriden by some widgets, depending on what THEY depend on..
     *
     * @return array of keys this widget depends on
     */
    public function get_cache_keys()
    {
        global $Collection, $Blog;

        $owner_User = &$Blog->get_owner_User();

        return [
            'wi_ID' => $this->ID,					// Have the widget settings changed ?
            'set_coll_ID' => $Blog->ID,			// Have the settings of the blog changed ? (ex: new owner, new skin)
            'user_ID' => $owner_User->ID, 	// Has the owner User changed? (name, avatar, etc..)
        ];
    }
}
