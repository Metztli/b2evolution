<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage bootstrap_main
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class horizon_main_Skin extends Skin
{
    /**
     * Skin version
     * @var string
     */
    public $version = '7.1.3';

    /**
     * Do we want to use style.min.css instead of style.css ?
     */
    public $use_min_css = 'true';  // true|false|'check' Set this to true for better optimization
    // Note: we leave this on "check" so it's easier for beginners to kjust delete the .min.css file

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Horizon Main';
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'rwd';
    }

    /**
     * What evoSkins API does has this skin been designed with?
     *
     * This determines where we get the fallback templates from (skins_fallback_v*)
     * (allows to use new markup in new b2evolution versions)
     */
    public function get_api_version()
    {
        return 7;
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
            'main' => 'yes',
            'std' => 'no',		// Blog
            'photo' => 'no',
            'forum' => 'no',
            'manual' => 'no',
            'group' => 'no',  // Tracker
            // Any kind that is not listed should be considered as "maybe" supported
        ];

        return $supported_kinds;
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
        // Note: second param below is the ORDER
        return [
            'page_top' => [NT_('Page Top'), 2],
            'header' => [NT_('Header'), 10],
            'front_page_main_area' => [NT_('Front Page Main Area'), 40],
            'front_page_secondary_area' => [NT_('Front Page Secondary Area'), 45],
            'item_single_header' => [NT_('Item Single Header'), 50],
            'item_single' => [NT_('Item Single'), 51],
            'item_page' => [NT_('Item Page'), 55],
            'contact_page_main_area' => [NT_('Contact Page Main Area'), 60],
            'footer' => [NT_('Footer'), 100],
            'user_profile_left' => [NT_('User Profile - Left'), 110],
            'user_profile_right' => [NT_('User Profile - Right'), 120],
            '404_page' => [NT_('404 Page'), 130],
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
            '1_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Image Section Settings'),
            ],
            'front_bg_image' => [
                'label' => T_('Background image'),
                'defaultvalue' => '../skins/horizon_main_skin/images/bg-picture.jpg',
                'type' => 'text',
                'size' => '50',
            ],
            'pict_text_color' => [
                'label' => T_('Text color'),
                'note' => T_('Default value is') . ' #333.',
                'defaultvalue' => '#333',
                'type' => 'color',
            ],
            'pict_link_color' => [
                'label' => T_('Link color'),
                'note' => T_('Default value is') . ' #697b94.',
                'defaultvalue' => '#697b94',
                'type' => 'color',
            ],
            'pict_muted_color' => [
                'label' => T_('Muted text color'),
                'note' => T_('Default value is') . ' #F0F0F0.',
                'defaultvalue' => '#F0F0F0',
                'type' => 'color',
            ],
            '1_end' => [
                'layout' => 'end_fieldset',
            ],
            '2_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Front Page Main Area Settings'),
            ],
            'front_top_margin' => [
                'label' => T_('Top margin'),
                'note' => '%. ' . T_('Separate content from the top of the page.'),
                'size' => '7',
                'defaultvalue' => '22',
            ],
            'front_width' => [
                'label' => T_('Width'),
                'note' => T_('Set width of the Main Area section.'),
                'size' => '7',
                'defaultvalue' => '700px',
            ],
            'front_position' => [
                'label' => T_('Position'),
                'note' => T_('Choose position of the Main Area section.'),
                'defaultvalue' => 'middle',
                'options' => [
                    'left' => T_('Left'),
                    'middle' => T_('Middle'),
                    'right' => T_('Right'),
                ],
                'type' => 'select',
            ],
            'front_bg_color' => [
                'label' => T_('Background color'),
                'note' => T_('Default value is') . ' #000000.',
                'defaultvalue' => '#000000',
                'type' => 'color',
            ],
            'front_bg_opacity' => [
                'label' => T_('Background opacity'),
                'note' => '%. ' . T_('Set the percentage of Main Area opacity.'),
                'size' => '7',
                'maxlength' => '3',
                'defaultvalue' => '10',
                'type' => 'integer',
                'valid_range' => [
                    'min' => 0, // from 0%
                    'max' => 100, // to 100%
                ],
            ],
            'pict_title_color' => [
                'label' => T_('Title color'),
                'note' => T_('Default value is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'front_text_color' => [
                'label' => T_('Text color'),
                'note' => T_('Default value is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'front_link_color' => [
                'label' => T_('Link color'),
                'note' => T_('Default value is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'front_icon_color' => [
                'label' => T_('Inverse icon color'),
                'note' => T_('Default value is') . ' #CCCCCC.',
                'defaultvalue' => '#CCCCCC',
                'type' => 'color',
            ],
            'page_footer_color' => [
                'label' => T_('Footer color'),
                'note' => T_('Default value is') . ' #F2F2F2.',
                'defaultvalue' => '#F2F2F2',
                'type' => 'color',
            ],
            '2_end' => [
                'layout' => 'end_fieldset',
            ],
            '3_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Colorbox Image Zoom Settings'),
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
            '3_end' => [
                'layout' => 'end_fieldset',
            ],
            '4_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Username Settings'),
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
            '4_end' => [
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
                    ['header',   sprintf(T_('"%s" container'), NT_('Header')),   1],
                    ['page_top', sprintf(T_('"%s" container'), NT_('Page Top')), 1],
                    ['menu',     sprintf(T_('"%s" container'), NT_('Menu')),     0],
                    ['footer',   sprintf(T_('"%s" container'), NT_('Footer')),   1],
                ],
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
        global $Messages, $disp, $debug;

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

        if (in_array($disp, ['front', 'login', 'register', 'lostpassword', 'activateinfo', 'access_denied', 'access_requires_login'])) {
            global $media_url, $media_path;

            // Add custom CSS:
            $custom_css = '';

            $bg_image = $this->get_setting('front_bg_image');
            if (! empty($bg_image) && file_exists($media_path . $bg_image)) { // Custom body background image:
                $custom_css .= '#bg_picture { background-image: url(' . $media_url . $bg_image . ") }\n";
            }

            if ($color = $this->get_setting('pict_title_color')) { // Custom title color:
                $custom_css .= 'body.pictured .main_page_wrapper .widget_core_coll_title h1 a { color: ' . $color . " }\n";
            }

            if ($color = $this->get_setting('pict_text_color')) { // Custom text color:
                $custom_css .= 'body.pictured, body.pictured .footer-wrapper p, body.pictured .evo_widget p { color: ' . $color . " }\n";
            }

            if ($color = $this->get_setting('pict_link_color')) { // Custom link color:
                $custom_css .= 'body.pictured .main_page_wrapper a:not([class*=btn]), .evo_container__footer a:not([class*="ufld__*"]):not(:hover) { color: ' . $color . " }\n";
            }

            if ($color = $this->get_setting('pict_muted_color')) { // Custom muted text color:
                $custom_css .= 'body.pictured .main_page_wrapper .text-muted { color: ' . $color . " }\n";
            }

            if ($color = $this->get_setting('front_bg_color')) { // Custom body background color:
                $color_transparency = floatval($this->get_setting('front_bg_opacity') / 100);
                $color = substr($color, 1);
                if (strlen($color) == '6') { // Color value in format #FFFFFF
                    $color = str_split($color, 2);
                } else { // Color value in format #FFF
                    $color = str_split($color, 1);
                    foreach ($color as $c => $v) {
                        $color[$c] = $v . $v;
                    }
                }
                $custom_css .= '.front_main_content { background-color: rgba(' . implode(',', array_map('hexdec', $color)) . ',' . $color_transparency . ')' . " }\n";
            }

            if ($color = $this->get_setting('front_text_color')) { // Custom text color:
                $custom_css .= 'body.pictured .front_main_content, body.pictured .front_main_content h1 small { color: ' . $color . " !important }\n";
            }

            $link_color = $this->get_setting('front_link_color');
            $icon_color = $this->get_setting('front_icon_color');
            if ($link_color) { // Custom link color:
                $custom_css .= 'body.pictured .main_page_wrapper .front_main_area a { color: ' . $link_color . " }\n";
            }
            if ($link_color && $icon_color) { // Custom icon color:
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:not([class*="ufld__textcolor"]):not(:hover) { color: ' . $icon_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:not([class*="ufld__bgcolor"]):not(:hover) { background-color: ' . $link_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:hover:not([class*="ufld__hovertextcolor"]) { color: ' . $link_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:hover:not([class*="ufld__hoverbgcolor"]) { background-color: ' . $icon_color . " }\n";
            }

            if ($front_top_margin = $this->get_setting('front_top_margin')) { // Custom title color:
                $custom_css .= 'div.front_main_area { margin-top: ' . $front_top_margin . "% }\n";
            }

            if ($width = $this->get_setting('front_width')) { // Custom width for front main area:
                $custom_css .= 'div.front_main_area { max-width: ' . $width . " }\n";
            }

            if ($position = $this->get_setting('front_position')) { // Custom width for front main area:
                if ($position == 'middle') {
                    $custom_css .= 'div.front_main_area { float: none; margin-left: auto; margin-right: auto; margin-bottom: 20px;' . " }\n";
                } elseif ($position == 'right') {
                    $custom_css .= 'div.front_main_area { float: right;' . " }\n";
                }
            }

            if ($color = $this->get_setting('page_footer_color')) { // Custom footer background color:
                $custom_css .= '.footer-wrapper { background-color: ' . $color . " }\n";
            }

            if (! empty($custom_css)) {
                if ($disp == 'front') { // Use standard bootstrap style on width <= 640px only for disp=front
                    $custom_css = '@media only screen and (min-width: 641px)
						{
							' . $custom_css . '
						}';
                }
                $custom_css = '<style type="text/css">
	<!--
		' . $custom_css . '
	-->
	</style>';
                add_headline($custom_css);
            }
        }
    }

    /**
     * Check if we can display a widget container
     *
     * @param string Widget container key: 'header', 'page_top', 'menu', 'sidebar', 'sidebar2', 'footer'
     * @param string Skin setting name
     * @return boolean TRUE to display
     */
    public function is_visible_container($container_key, $setting_name = 'access_login_containers')
    {
        $access = $this->get_setting($setting_name);

        return (! empty($access) && ! empty($access[$container_key]));
    }
}
