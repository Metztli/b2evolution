<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage bootstrap_blog
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class reporter_Skin extends Skin
{
    /**
     * Skin version
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Do we want to use style.min.css instead of style.css ?
     */
    public $use_min_css = 'check';  // true|false|'check' Set this to true for better optimization
    // Note: we leave this on "check" in the bootstrap_blog_skin so it's easier for beginners to just delete the .min.css file
    // But for best performance, you should set it to true.

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Reporter';
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'normal';
    }

    /**
     * What evoSkins API does has this skin been designed with?
     *
     * This determines where we get the fallback templates from (skins_fallback_v*)
     * (allows to use new markup in new b2evolution versions)
     */
    public function get_api_version()
    {
        return 6;
    }

    /**
     * Get supported collection kinds.
     *
     * This should be overloaded in skins.
     *
     * For each kind the answer could be:
     * - 'yes' : this skin does support that collection kind (the result will be was is expected)
     * - 'partial' : this skin is not a primary choice for this collection kind (but still produces an output that makes sense)
     * - 'maybe' : this skin has not been tested with this collection kind
     * - 'no' : this skin does not support that collection kind (the result would not be what is expected)
     * There may be more possible answers in the future...
     */
    public function get_supported_coll_kinds()
    {
        $supported_kinds = [
            'main' => 'no',
            'std' => 'yes',		// Blog
            'photo' => 'no',
            'forum' => 'no',
            'manual' => 'no',
            'group' => 'no',  // Tracker
            // Any kind that is not listed should be considered as "maybe" supported
        ];

        return $supported_kinds;
    }

    /**
     * What CSS framework does has this skin been designed with?
     *
     * This may impact default markup returned by Skin::get_template() for example
     */
    public function get_css_framework()
    {
        return 'bootstrap';
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     * @return array
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'section_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Layout Settings'),
            ],
            'layout' => [
                'label' => T_('Layout'),
                'note' => T_('Select skin layout.'),
                'defaultvalue' => 'right_sidebar',
                'options' => [
                    'single_column' => T_('Single column'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'type' => 'select',
            ],
            'max_image_height' => [
                'label' => T_('Max image height'),
                'note' => 'px. ' . T_('Set maximum height for post images.'),
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'section_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'section_colorbox_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Colorbox Image Zoom'),
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
            'section_colorbox_end' => [
                'layout' => 'end_fieldset',
            ],

            'section_username_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Username options'),
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
            'section_username_end' => [
                'layout' => 'end_fieldset',
            ],

            'section_access_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('When access is denied or requires login...'),
            ],
            'access_login_containers' => [
                'label' => T_('Display on login screen'),
                'note' => '',
                'type' => 'checklist',
                'options' => [
                    ['header',   sprintf(T_('"%s" container'), NT_('Header')),    1],
                    ['page_top', sprintf(T_('"%s" container'), NT_('Page Top')),  1],
                    ['menu',     sprintf(T_('"%s" container'), NT_('Menu')),      0],
                    ['sidebar',  sprintf(T_('"%s" container'), NT_('Sidebar')),   0],
                    ['sidebar2', sprintf(T_('"%s" container'), NT_('Sidebar 2')), 0],
                    ['footer',   sprintf(T_('"%s" container'), NT_('Footer')),    1]],
            ],
            'section_access_end' => [
                'layout' => 'end_fieldset',
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
        global $Messages, $debug;

        // Request some common features that the parent function (Skin::display_init()) knows how to provide:
        parent::display_init([
            'jquery',                  // Load jQuery
            'font_awesome',            // Load Font Awesome (and use its icons as a priority over the Bootstrap glyphicons)
            'bootstrap',               // Load Bootstrap (without 'bootstrap_theme_css')
            'bootstrap_evo_css',       // Load the b2evo_base styles for Bootstrap (instead of the old b2evo_base styles)
            'bootstrap_messages',      // Initialize $Messages Class to use Bootstrap styles
            'style_css',               // Load the style.css file of the current skin
            'colorbox',                // Load Colorbox (a lightweight Lightbox alternative + customizations for b2evo)
            'bootstrap_init_tooltips', // Inline JS to init Bootstrap tooltips (E.g. on comment form for allowed file extensions)
            'disp_auto',               // Automatically include additional CSS and/or JS required by certain disps (replace with 'disp_off' to disable this)
        ]);

        // Skin specific initializations:

        // Limit images by max height:
        $max_image_height = intval($this->get_setting('max_image_height'));
        if ($max_image_height > 0) {
            add_css_headline('.evo_image_block img { max-height: ' . $max_image_height . 'px; width: auto; }');
        }

        // Add custom CSS:
        $custom_css = '';
    }
}
