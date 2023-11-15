<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage bootstrap
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class ark_Skin extends Skin
{
    public $version = '7.3.1';

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
        return 'Ark Skin';
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
     * Get the container codes of the skin main containers
     *
     * This should NOT be protected. It should be used INSTEAD of file parsing.
     * File parsing should only be used if this function is not defined
     *
     * @return array
     */
    public function get_declared_containers()
    {
        // Note: second param below is the ORDER
        return [
            'header' => [NT_('Header'), 10],
            'menu' => [NT_('Menu'), 15],
            'front_page_main_area' => [NT_('Front Page Main Area'), 40],
            'item_single_header' => [NT_('Item Single Header'), 50],
            'item_single' => [NT_('Item Single'), 51],
            'item_page' => [NT_('Item Page'), 55],
            'contact_page_main_area' => [NT_('Contact Page Main Area'), 60],
            'sidebar' => [NT_('Sidebar'), 80],
            'sidebar_2' => [NT_('Sidebar_2'), 81],
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
            'general_settings_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('General settings'),
            ],
            'layout' => [
                'label' => T_('Page layout'),
                'note' => T_('Set skin layout.'),
                'defaultvalue' => 'right_sidebar',
                'options' => [
                    'single_column' => T_('Single column'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'type' => 'select',
            ],
            'site_background_color' => [
                'label' => T_('Site background color'),
                'note' => T_('Set the background color of the skin pages.') . T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'site_text_color' => [
                'label' => T_('Site text color'),
                'note' => T_('Default value is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            // General links color
            'site_link_color' => [
                'label' => T_('Site links color'),
                'note' => T_('Default value is') . ' #5CBDE0.',
                'defaultvalue' => '#5CBDE0',
                'type' => 'color',
            ],
            'site_link_color_hover' => [
                'label' => T_('Site links color (hover)'),
                'note' => T_('Default value is') . ' #4DB6DC.',
                'defaultvalue' => '#4DB6DC',
                'type' => 'color',
            ],
            'general_settings_end' => [
                'layout' => 'end_fieldset',
            ],

            'header_settings_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Header settings'),
            ],
            'front_bg_image' => [
                'label' => T_('Header background image'),
                'note' => T_('Leave blank if you want background color instead.'),
                'defaultvalue' => 'images/bg-image.jpg',
                'type' => 'text',
                'size' => '50',
            ],
            'header_height' => [
                'label' => T_('Header height'),
                'note' => 'px. ' . T_('Input numbers only.') . ' ' . T_('Default value is') . ' 300.',
                'defaultvalue' => '300',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'headpicture_bg_col' => [
                'label' => T_('Header background color'),
                'note' => T_("Set the background color of the header section."),
                'defaultvalue' => '#333',
                'type' => 'color',
            ],
            'header_content_pos' => [
                'label' => T_('Header content position'),
                'note' => T_('Align header content'),
                'defaultvalue' => 'center_pos',
                'options' => [
                    'center_pos' => T_('Center'),
                    'left_pos' => T_('Left'),
                    'right_pos' => T_('Right'),
                    'column_pos' => T_('With content column'),
                ],
                'type' => 'select',
            ],
            'site_title_color' => [
                'label' => T_('Header title color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'site_tagline_color' => [
                'label' => T_('Header tagline color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'header_content_color' => [
                'label' => T_('Header content color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'header_links_color' => [
                'label' => T_('Header links color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'header_settings_end' => [
                'layout' => 'end_fieldset',
            ],

            'top_menu_settings_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Navigation Menu Settings'),
            ],
            'top_menu_brand' => [
                'label' => T_('Top menu collection title'),
                'note' => T_('Check to display collection title as the first item in the top menu.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'top_menu_brand_col' => [
                'label' => T_('Top menu collection title color'),
                'note' => T_('Select color of the collection title in the top menu.') . T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'top_menu_position' => [
                'label' => T_('Top menu content position'),
                'note' => ' (' . T_('Set top menu content alignment position') . ')',
                'type' => 'radio',
                'options' => [
                    ['menu_left', T_('Left')],
                    ['menu_inline', T_('With content column')],
                    ['menu_center', T_('Center')],
                ],
                'defaultvalue' => 'menu_left',
            ],
            'top_menu_hamburger' => [
                'label' => T_('Top menu hamburger layout'),
                'note' => 'px. ' . T_('Set the exact screen width in pixels (NUMBERS ONLY) to break menu layout to hamburger menu. For example if you write 815, you will get hamburger menu until screen size reaches 816th pixel width.'),
                'defaultvalue' => '815',
                'type' => 'integer',
                'size' => '7',
            ],
            'top_menu_hamburger_color' => [
                'label' => T_('Top menu hamburger color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'top_menu_background_color' => [
                'label' => T_('Top menu background color'),
                'note' => T_('Default value is') . ' #282828.',
                'defaultvalue' => '#282828',
                'type' => 'color',
            ],
            'top_menu_links_color' => [
                'label' => T_('Top menu links color'),
                'note' => T_('Default value is') . ' #999999.',
                'defaultvalue' => '#999999',
                'type' => 'color',
            ],
            'top_menu_links_hover_color' => [
                'label' => T_('Top menu links hover color'),
                'note' => T_('Default value is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'top_menu_ac_link_bgcol' => [
                'label' => T_('Top menu active link background color'),
                'note' => T_('Default value is') . ' #000.',
                'defaultvalue' => '#000',
                'type' => 'color',
            ],
            'top_menu_settings_end' => [
                'layout' => 'end_fieldset',
            ],

            'pagination_settings_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Site pagination settings'),
            ],
            'pag_links_color' => [
                'label' => T_('Site pagination links color'),
                'note' => T_('Default value is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'pag_links_bg_color' => [
                'label' => T_('Site pagination links background color'),
                'note' => T_('Default value is') . ' #DDD.',
                'defaultvalue' => '#DDD',
                'type' => 'color',
            ],
            'pag_active_bg_color' => [
                'label' => T_('Site pagination active link background color'),
                'note' => T_(' NOTE: Unactive pagination links have this background color on hover!'),
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'pagination_settings_end' => [
                'layout' => 'end_fieldset',
            ],

            'posts_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Posts layout'),
            ],
            /*
                    'post_layout' => array(
                        'label' => T_('Posts teaser image layout'),
                        'note' => T_(''),
                        'defaultvalue' => 'regular',
                        'options' => array(
                                'regular' => T_('Regular'),
                                'thumbnail'  => T_('Thumbnail'),
                            ),
                        'type' => 'select',
                    ),
                    */
            'post_title_link_color' => [
                'label' => T_('Post title link color'),
                'note' => T_('Default value is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'post_title_link_color_hover' => [
                'label' => T_('Post title link color (hover)'),
                'note' => T_('Default value is') . ' #000000.',
                'defaultvalue' => '#000000',
                'type' => 'color',
            ],
            'posts_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'prevnext_but_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Post/Preview comment buttons layout'),
            ],
            // Post button
            'post_but_border_color' => [
                'label' => T_('Post button border color'),
                'note' => T_('Default value is') . ' #269ABC.',
                'defaultvalue' => '#269ABC',
                'type' => 'color',
            ],
            'post_but_bg_color' => [
                'label' => T_('Post button background color'),
                'note' => T_('Default value is') . ' #31B0D5.',
                'defaultvalue' => '#31B0D5',
                'type' => 'color',
            ],
            'post_but_text_color' => [
                'label' => T_('Post button text color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            // Preview button
            'prev_but_bor_color' => [
                'label' => T_('Preview button border color'),
                'note' => T_('Default value is') . ' #204D74.',
                'defaultvalue' => '#204D74',
                'type' => 'color',
            ],
            'prev_but_bg_color' => [
                'label' => T_('Preview button background color'),
                'note' => T_('Default value is') . ' #286090.',
                'defaultvalue' => '#286090',
                'type' => 'color',
            ],
            'prev_but_tx_color' => [
                'label' => T_('Preview button text color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'prevnext_but_end' => [
                'layout' => 'end_fieldset',
            ],

            'tags_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Tags layout'),
            ],
            'post_tags_color_and_border_color' => [
                'label' => T_('Post tags color and border color'),
                'note' => T_('Default value is') . ' #888888.',
                'defaultvalue' => '#888888',
                'type' => 'color',
            ],
            'post_tags_background_color_on_hover' => [
                'label' => T_('Post tags background color (on hover)'),
                'note' => T_('Default value is') . ' #EEF.',
                'defaultvalue' => '#EEF',
                'type' => 'color',
            ],
            'post_bottom_border_color' => [
                'label' => T_('Post border bottom color'),
                'note' => T_('Default value is') . ' #EEE.',
                'defaultvalue' => '#EEE',
                'type' => 'color',
            ],
            'tags_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'comments_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Comments layout'),
            ],
            'comments_panel_background_color' => [
                'label' => T_('Comments panel color'),
                'note' => T_('Default value is') . ' #EEE.',
                'defaultvalue' => '#EEE',
                'type' => 'color',
            ],
            'comments_border_color' => [
                'label' => T_('Comments border color'),
                'note' => T_('Default value is') . ' #DDD.',
                'defaultvalue' => '#DDD',
                'type' => 'color',
            ],
            'comments_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'footer_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Footer layout'),
            ],
            'b2evo_credits' => [
                'label' => T_('b2evolution credits'),
                'note' => T_('Please help us promote b2evolution and leave b2evolution credits on your website.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'footer_links' => [
                'label' => T_('Footer User Links'),
                'note' => T_('Check to display user links in the footer'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'footer_title_color' => [
                'label' => T_('Footer title color'),
                'note' => T_('Default value is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'footer_text_color' => [
                'label' => T_('Footer text color'),
                'note' => T_('Default value is') . ' #888.',
                'defaultvalue' => '#888',
                'type' => 'color',
            ],
            'footer_background_color' => [
                'label' => T_('Footer background color'),
                'note' => T_('Default value is') . ' #333.',
                'defaultvalue' => '#333',
                'type' => 'color',
            ],
            'footer_link_color' => [
                'label' => T_('Footer link color'),
                'note' => T_('Default value is') . ' #bbb.',
                'defaultvalue' => '#bbb',
                'type' => 'color',
            ],
            'footer_link_color_hover' => [
                'label' => T_('Footer link color (hover)'),
                'note' => T_('Default value is') . ' #bbb.',
                'defaultvalue' => '#bbb',
                'type' => 'color',
            ],
            'footer_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'custom_settings_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Custom settings'),
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
            'custom_settings_end' => [
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
                    ['menu',     sprintf(T_('"%s" container'), NT_('Menu')),      1],
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
        global $Messages, $debug, $disp;

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
        // Add custom CSS:
        $custom_css = '';

        add_headline("<link href='https://fonts.googleapis.com/css?family=Raleway:400,300,800,700' rel='stylesheet' type='text/css'>");

        // Only change post teaser image for "front" and "posts"
        if (in_array($disp, ['front', 'posts'])) {
            $post_t_images = $this->get_setting('post_teaser_image');
            switch ($post_t_images) {
                case 'regular': // When regular layout is chosen, nothing happens, since regular is default
                    $custom_css = '';
                    break;

                case 'thumbnail':// When thumbnail layout is chosen, apply these styles
                    $custom_css = '.evo_post_images{ float: left; width: 200px;' . " }\n";
                    $custom_css .= '.evo_post_images .evo_image_block { margin: 0px 15px 15px 0px;' . " }\n";
                    $custom_css .= '.evo_post__full .evo_post_gallery { margin-bottom: 25px' . " }\n";
                    $custom_css .= '@media only screen and (max-width: 767px) { .evo_post_images{ float: none; width: 100% } .evo_post_images .evo_image_block {margin: 15px 0px; } .evo_post__full .evo_post_gallery { margin-bottom: 0px}' . " }\n";
                    break;
            }
        };
        if ($front_bg_image = $this->get_setting('front_bg_image')) { // If image input
            $custom_css .= '.headpicture { background: url(' . $front_bg_image . ") no-repeat center center;background-size: cover; }\n";
        }
        if ($color = $this->get_setting('headpicture_bg_col')) { // Site header color:
            $custom_css .= '.headpicture { background-color: ' . $color . " }\n";
        };
        if ($header_height = $this->get_setting('header_height')) { // If image input
            $custom_css .= '.headpicture { min-height:' . $header_height . "px }\n";
        }
        // Site title color:
        if ($color = $this->get_setting('site_title_color')) {
            $custom_css .= '#skin_wrapper .headpicture .widget_core_coll_title h3 a { color: ' . $color . " }\n";
        };
        // Site tagline color:
        if ($color = $this->get_setting('site_tagline_color')) {
            $custom_css .= '#skin_wrapper .headpicture .widget_core_coll_tagline { color: ' . $color . " }\n";
        };
        // Header content color:
        if ($header_content_color = $this->get_setting('header_content_color')) {
            $custom_css .= '#skin_wrapper .headpicture { color: ' . $header_content_color . " }\n";
        };
        // Header links color:
        if ($header_links_color = $this->get_setting('header_links_color')) {
            $custom_css .= '#skin_wrapper .headpicture a, #skin_wrapper .headpicture a:hover { color: ' . $header_links_color . " }\n";
        };
        // Site background color:
        if ($color = $this->get_setting('site_background_color')) {
            $custom_css .= '#skin_wrapper { background-color: ' . $color . " }\n";
        };
        // Site text color:
        if ($color = $this->get_setting('site_text_color')) {
            $custom_css .= '#skin_wrapper, .widget .panel-heading h4, .evo_widget .panel-heading h4, .styled_content_block .panel-heading, .bCalendarRow .bCalendarHeaderCell, h4.evo_comment_title { color: ' . $color . " }\n";
        };
        // Site link color:
        if ($color = $this->get_setting('site_link_color')) {
            $custom_css .= 'a, #bCalendarToday, .search_title a, h4.evo_comment_title a { color: ' . $color . " }\n";
            $custom_css .= '#bCalendarToday { border: 1px solid ' . $color . " }\n";
        };
        // Site link color hover:
        if ($color = $this->get_setting('site_link_color_hover')) {
            $custom_css .= 'a:hover, .search_title a:hover { color: ' . $color . " }\n";
        };
        // Top menu hamburger layout:
        if ($width = $this->get_setting('top_menu_hamburger')) {
            $custom_css .= '@media only screen and (max-width: ' . $width . "px) {
				   .navbar-header {float: none}.navbar-left,.navbar-right {float: none !important}.navbar-toggle {display: block}.navbar-collapse {border-top: 1px solid transparent;/*box-shadow: inset 0 1px 0 rgba(255,255,255,0.1)*/}.navbar-fixed-top {top: 0;border-width: 0 0 1px;}.navbar-collapse.collapse {display: none!important}.navbar-nav {float: none!important;margin-top: 7.5px;overflow: hidden}.navbar-nav>li {float: none}.navbar-nav>li>a {padding-top: 10px;padding-bottom: 10px;}.collapse.in{display:block !important;}.evo_container__menu .header-search-toggle{display: none}.header-search-toggle{position: relative !important}.menu-social-toggle{display: none}.navbar-toggle-hamb{padding-right: 15px}.top-menu ul li a{display:block}.top-menu ul li{padding:0}.menu_inline_container{width:100%;}.top-menu .menu_center > ul{display:block}.top-menu .navbar-header-center {text-align: left}.top-menu .navbar-header-center .navbar-brand {float: left;}
				}\n";
        };
        // Top menu hambuger color:
        if ($color = $this->get_setting('top_menu_hamburger_color')) {
            $custom_css .= '.navbar-toggle-hamb span.icon-bar { background-color: ' . $color . " }\n";
        };
        // Top menu background color:
        if ($color = $this->get_setting('top_menu_background_color')) {
            $custom_css .= '.top-menu { background-color: ' . $color . " }\n";
        };
        // Top menu links color:
        if ($color = $this->get_setting('top_menu_links_color')) {
            $custom_css .= '.top-menu ul li a, .navbar-brand a { color: ' . $color . " }\n";
        };
        // Top menu links hover color:
        if ($color = $this->get_setting('top_menu_links_hover_color')) {
            $custom_css .= '.top-menu ul li a:hover, .navbar-brand a:hover { color: ' . $color . " }\n";
        };
        // Top menu active link background color:
        if ($color = $this->get_setting('top_menu_ac_link_bgcol')) {
            $custom_css .= '.top-menu ul li.active, .navbar-brand a.active { background-color: ' . $color . " }\n";
        };
        // Top menu brand color:
        if ($color = $this->get_setting('top_menu_brand_col')) {
            $custom_css .= '.top-menu .navbar-brand h3 a { color: ' . $color . " }\n";
        };

        // Post title link color:
        if ($color = $this->get_setting('post_title_link_color')) {
            $custom_css .= '.evo_post h2 a { color: ' . $color . " }\n";
        };
        // Post title link color (hover):
        if ($color = $this->get_setting('post_title_link_color_hover')) {
            $custom_css .= '.evo_post h2 a:hover { color: ' . $color . " }\n";
        };
        // Post tags color and border-color:
        if ($color = $this->get_setting('post_tags_color_and_border_color')) {
            $custom_css .= '.evo_post .tags a, .widget_core_coll_tag_cloud .tag_cloud a, .well .tags a { color: ' . $color . '; border: 1px solid ' . $color . " }\n";
        };
        // Post tags background color (on hover):
        if ($color = $this->get_setting('post_tags_background_color_on_hover')) {
            $custom_css .= '.evo_post .tags a:hover, .widget_core_coll_tag_cloud .tag_cloud a:hover, .well .tags a:hover { background-color: ' . $color . " }\n";
        };
        // Post bottom border color:
        if ($color = $this->get_setting('post_bottom_border_color')) {
            if ($disp != 'single') {
                $custom_css .= '.evo_post, .search_result { border-bottom: 1px dotted ' . $color . " }\n";
            } else {
                $custom_css .= '.disp_single .pager, .disp_page .pager { border-top: 1px dotted ' . $color . " }\n";
            }
        };

        // Comments background color:
        if ($color = $this->get_setting('comments_panel_background_color')) {
            $custom_css .= '.evo_comment { background-color: ' . $color . " !important }\n";
        };
        // Comments border color:
        if ($color = $this->get_setting('comments_border_color')) {
            $custom_css .= '.evo_comment { border: 1px solid ' . $color . " }\n";
        };

        // Site Pagination links color:
        if ($color = $this->get_setting('pag_links_color')) {
            $custom_css .= '.site_pagination li span, .site_pagination li a, .site_pagination > a, .site_pagination > a:hover,
								.pagination li span, .pagination li a, .pagination > a, .pagination > a:hover
				{ color: ' . $color . " !important }\n";
        };
        // Site Pagination links color:
        if ($color = $this->get_setting('pag_links_bg_color')) {
            $custom_css .= '.site_pagination li a, .pagination li a { background-color: ' . $color . " }\n";
        };
        // Site Pagination active link color:
        if ($color = $this->get_setting('pag_active_bg_color')) {
            $custom_css .= '.site_pagination li span, .site_pagination li a:hover, .site_pagination li.active a,
								.pagination li span, .pagination li a:hover, .pagination li.active a
				{ background-color: ' . $color . " !important }\n";
        };

        // Footer title color:
        if ($color = $this->get_setting('footer_title_color')) {
            $custom_css .= 'footer .container .panel-heading, footer .container .panel-title { color: ' . $color . " }\n";
        };
        // Footer text color:
        if ($color = $this->get_setting('footer_text_color')) {
            $custom_css .= 'footer.footer { color: ' . $color . " }\n";
        };
        // Footer background color:
        if ($color = $this->get_setting('footer_background_color')) {
            $custom_css .= 'footer.footer, .ufld_icon_links a span, .ufld_icon_links a span:hover { background-color: ' . $color . " }\n";
        };
        // Footer link color:
        if ($color = $this->get_setting('footer_link_color')) {
            $custom_css .= 'footer.footer a { color: ' . $color . " }\n";
        };
        // Footer link color (hover):
        if ($color = $this->get_setting('footer_link_color_hover')) {
            $custom_css .= 'footer.footer a:hover { color: ' . $color . " }\n";
        };

        // Post button border color:
        if ($color = $this->get_setting('post_but_border_color')) {
            $custom_css .= '.comment-form .control-buttons .submit, .evo_form__login .btn-success,
				.disp_threads main .btn-info { border: 1px solid ' . $color . " }\n";
        };
        // Post button background color:
        if ($color = $this->get_setting('post_but_bg_color')) {
            $custom_css .= '.comment-form .control-buttons .submit, .evo_form__login .btn-success,
				.disp_threads main .btn-info { background-color: ' . $color . " }\n";
        };
        // Post button text color:
        if ($color = $this->get_setting('post_but_text_color')) {
            $custom_css .= '.comment-form .control-buttons .submit, .evo_form__login .btn-success,
				.disp_threads main .btn-info { color: ' . $color . " }\n";
        };
        // Preview button border color:
        if ($color = $this->get_setting('prev_but_bor_color')) {
            $custom_css .= '.comment-form .control-buttons .preview, .evo_form__login .btn-primary, .evo_form__register .btn-primary,
				.disp_threads main .btn-primary, .disp_msgform main .btn-primary { border: 1px solid ' . $color . " }\n";
        };
        // Preview button background color:
        if ($color = $this->get_setting('prev_but_bg_color')) {
            $custom_css .= '.comment-form .control-buttons .preview, .evo_form__login .btn-primary, .evo_form__register .btn-primary,
				.disp_threads main .btn-primary, .disp_msgform main .btn-primary { background-color: ' . $color . " }\n";
        };
        // Preview button text color:
        if ($color = $this->get_setting('prev_but_tx_color')) {
            $custom_css .= '.comment-form .control-buttons .preview, .evo_form__login .btn-primary, .evo_form__register .btn-primary,
				.disp_threads main .btn-primary, .disp_msgform main .btn-primary { color: ' . $color . " }\n";
        };

        // Custom disps styles
        /*if ( $disp == 'search' ) {
                $custom_css .='.site_pagination>a, .site_pagination li { margin-right: 4px'." }\n";
        }*/
        if ($disp == 'page') {
            $custom_css .= '#feedbacks h3 { padding-top: 40px;' . " }\n";
        } else {
            $custom_css .= '#feedbacks h3 { padding-top: 10px;' . " }\n";
        }
        if (! empty($custom_css)) { // Function for custom_css:
            $custom_css = '<style type="text/css">
<!--
' . $custom_css . '
-->
		</style>';
            add_headline($custom_css);
        }
    }

    /**
     * Those templates are used for example by the messaging screens.
     */
    public function get_template($name)
    {
        switch ($name) {
            case 'Results':
                // Results list (Used to view the lists of the users, messages, contacts and etc.):
                return [
                    'page_url' => '', // All generated links will refer to the current page
                    'before' => '<div class="results">',
                    'content_start' => '<div id="$prefix$ajax_content">',
                    'header_start' => '',
                    'header_text' => '<div class="center"><ul class="site_pagination">'
                            . '$prev$$first$$list_prev$$list$$list_next$$last$$next$'
                        . '</ul></div>',
                    'header_text_single' => '',
                    'header_end' => '',
                    'head_title' => '<h2>$title$</h2><div class="fieldset_title"><div class="form-group">$global_icons$</div></div>' . "\n",
                    'global_icons_class' => 'btn btn-sm',
                    'filters_start' => '<div class="filters">',
                    'filters_end' => '</div>',
                    'filter_button_class' => 'btn-sm btn-info filter-submit',
                    'filter_button_before' => '<div class="form-group floatright filter-button-wrapper">',
                    'filter_button_after' => '</div>',
                    'messages_start' => '<div class="messages form-inline">',
                    'messages_end' => '</div>',
                    'messages_separator' => '<br />',
                    'list_start' => '<div class="table_scroll">' . "\n"
                                   . '<table class="table table-condensed" cellspacing="0">' . "\n",
                    'head_start' => "<thead>\n",
                    'line_start_head' => '<tr>',  // TODO: fusionner avec colhead_start_first; mettre a jour admin_UI_general; utiliser colspan="$headspan$"
                    'colhead_start' => '<th $class_attrib$>',
                    'colhead_start_first' => '<th class="firstcol $class$">',
                    'colhead_start_last' => '<th class="lastcol $class$">',
                    'colhead_end' => "</th>\n",
                    'sort_asc_off' => get_icon('sort_asc_off'),
                    'sort_asc_on' => get_icon('sort_asc_on'),
                    'sort_desc_off' => get_icon('sort_desc_off'),
                    'sort_desc_on' => get_icon('sort_desc_on'),
                    'basic_sort_off' => '',
                    'basic_sort_asc' => get_icon('ascending'),
                    'basic_sort_desc' => get_icon('descending'),
                    'head_end' => "</thead>\n\n",
                    'tfoot_start' => "<tfoot>\n",
                    'tfoot_end' => "</tfoot>\n\n",
                    'body_start' => "<tbody>\n",
                    'line_start' => '<tr class="even">' . "\n",
                    'line_start_odd' => '<tr class="odd">' . "\n",
                    'line_start_last' => '<tr class="even lastline">' . "\n",
                    'line_start_odd_last' => '<tr class="odd lastline">' . "\n",
                    'col_start' => '<td $class_attrib$>',
                    'col_start_first' => '<td class="firstcol $class$">',
                    'col_start_last' => '<td class="lastcol $class$">',
                    'col_end' => "</td>\n",
                    'line_end' => "</tr>\n\n",
                    'grp_line_start' => '<tr class="group">' . "\n",
                    'grp_line_start_odd' => '<tr class="odd">' . "\n",
                    'grp_line_start_last' => '<tr class="lastline">' . "\n",
                    'grp_line_start_odd_last' => '<tr class="odd lastline">' . "\n",
                    'grp_col_start' => '<td $class_attrib$ $colspan_attrib$>',
                    'grp_col_start_first' => '<td class="firstcol $class$" $colspan_attrib$>',
                    'grp_col_start_last' => '<td class="lastcol $class$" $colspan_attrib$>',
                    'grp_col_end' => "</td>\n",
                    'grp_line_end' => "</tr>\n\n",
                    'body_end' => "</tbody>\n\n",
                    'total_line_start' => '<tr class="total">' . "\n",
                    'total_col_start' => '<td $class_attrib$>',
                    'total_col_start_first' => '<td class="firstcol $class$">',
                    'total_col_start_last' => '<td class="lastcol $class$">',
                    'total_col_end' => "</td>\n",
                    'total_line_end' => "</tr>\n\n",
                    'list_end' => "</table></div>\n\n",
                    'footer_start' => '',
                    'footer_text' => '<div class="center"><ul class="site_pagination">'
                            . '$prev$$first$$list_prev$$list$$list_next$$last$$next$'
                        . '</ul></div><div class="center">$page_size$</div>'
                    /* T_('Page $scroll_list$ out of $total_pages$   $prev$ | $next$<br />'. */
                    /* '<strong>$total_pages$ Pages</strong> : $prev$ $list$ $next$' */
                    /* .' <br />$first$  $list_prev$  $list$  $list_next$  $last$ :: $prev$ | $next$') */,
                    'footer_text_single' => '<div class="center">$page_size$</div>',
                    'footer_text_no_limit' => '', // Text if theres no LIMIT and therefor only one page anyway
                    'page_current_template' => '<span>$page_num$</span>',
                    'page_item_before' => '<li>',
                    'page_item_after' => '</li>',
                    'page_item_current_before' => '<li class="active">',
                    'page_item_current_after' => '</li>',
                    'prev_text' => T_('Previous'),
                    'next_text' => T_('Next'),
                    'no_prev_text' => '',
                    'no_next_text' => '',
                    'list_prev_text' => T_('...'),
                    'list_next_text' => T_('...'),
                    'list_span' => 11,
                    'scroll_list_range' => 5,
                    'footer_end' => "\n\n",
                    'no_results_start' => '<div class="panel-footer">' . "\n",
                    'no_results_end' => '$no_results$</div>' . "\n\n",
                    'content_end' => '</div>',
                    'after' => '</div>',
                    'sort_type' => 'basic',
                ];
                break;

            case 'blockspan_form':
                // Form settings for filter area:
                return [
                    'layout' => 'blockspan',
                    'formclass' => 'form-inline',
                    'formstart' => '',
                    'formend' => '',
                    'title_fmt' => '$title$' . "\n",
                    'no_title_fmt' => '',
                    'fieldset_begin' => '<fieldset $fieldset_attribs$>' . "\n"
                                                                . '<legend $title_attribs$>$fieldset_title$</legend>' . "\n",
                    'fieldset_end' => '</fieldset>' . "\n",
                    'fieldstart' => '<div class="form-group form-group-sm" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => 'control-label',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label></label>',
                    'inputstart' => '',
                    'inputend' => "\n",
                    'infostart' => '<div class="form-control-static">',
                    'infoend' => "</div>\n",
                    'buttonsstart' => '<div class="form-group form-group-sm">',
                    'buttonsend' => "</div>\n\n",
                    'customstart' => '<div class="custom_content">',
                    'customend' => "</div>\n",
                    'note_format' => ' <span class="help-inline">%s</span>',
                    // Additional params depending on field type:
                    // - checkbox
                    'fieldstart_checkbox' => '<div class="form-group form-group-sm checkbox" $ID$>' . "\n",
                    'fieldend_checkbox' => "</div>\n\n",
                    'inputclass_checkbox' => '',
                    'inputstart_checkbox' => '',
                    'inputend_checkbox' => "\n",
                    'checkbox_newline_start' => '',
                    'checkbox_newline_end' => "\n",
                    // - radio
                    'inputclass_radio' => '',
                    'radio_label_format' => '$radio_option_label$',
                    'radio_newline_start' => '',
                    'radio_newline_end' => "\n",
                    'radio_oneline_start' => '',
                    'radio_oneline_end' => "\n",
                ];

            case 'compact_form':
            case 'Form':
                // Default Form settings (Used for any form on front-office):
                return [
                    'layout' => 'fieldset',
                    'formclass' => 'form-horizontal',
                    'formstart' => '<div class="comment-form">',
                    'formend' => '</div>',
                    'title_fmt' => '<span style="float:right">$global_icons$</span><h2>$title$</h2>' . "\n",
                    'no_title_fmt' => '<span style="float:right">$global_icons$</span>' . "\n",
                    'fieldset_begin' => '<div class="fieldset_wrapper $class$" id="submit_preview_buttons_wrapper"><fieldset $fieldset_attribs$><div>' . "\n"
                                                            . '<legend class="panel-heading" $title_attribs$>$fieldset_title$</legend><div class="panel-body $class$">' . "\n",
                    'fieldset_end' => '</div></div></fieldset></div>' . "\n",
                    'fieldstart' => '<div class="form-group" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => 'control-label',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label class="control-label"></label>',
                    'inputstart' => '<div class="controls">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class="controls"><div class="form-control-static">',
                    'infoend' => "</div></div>\n",
                    'buttonsstart' => '<div class="control-buttons">',
                    'buttonsend' => "</div>\n\n",
                    'customstart' => '<div class="custom_content">',
                    'customend' => "</div>\n",
                    'note_format' => ' <span class="help-inline">%s</span>',
                    // Additional params depending on field type:
                    // - checkbox
                    'inputclass_checkbox' => '',
                    'inputstart_checkbox' => '<div class="controls col-sm-9"><div class="checkbox"><label>',
                    'inputend_checkbox' => "</label></div></div>\n",
                    'checkbox_newline_start' => '<div class="checkbox">',
                    'checkbox_newline_end' => "</div>\n",
                    // - radio
                    'fieldstart_radio' => '<div class="form-group radio-group" $ID$>' . "\n",
                    'fieldend_radio' => "</div>\n\n",
                    'inputclass_radio' => '',
                    'radio_label_format' => '$radio_option_label$',
                    'radio_newline_start' => '<div class="radio"><label>',
                    'radio_newline_end' => "</label></div>\n",
                    'radio_oneline_start' => '<label class="radio-inline">',
                    'radio_oneline_end' => "</label>\n",
                ];

            case 'fixed_form':
                // Form with fixed label width (Used for form on disp=user):
                return [
                    'layout' => 'fieldset',
                    'formclass' => 'form-horizontal',
                    'formstart' => '',
                    'formend' => '',
                    'title_fmt' => '<span style="float:right">$global_icons$</span><h2>$title$</h2>' . "\n",
                    'no_title_fmt' => '<span style="float:right">$global_icons$</span>' . "\n",
                    'fieldset_begin' => '<div class="fieldset_wrapper $class$" id="fieldset_wrapper_$id$"><fieldset $fieldset_attribs$><div class="">' . "\n"
                                                            . '<legend class="panel-title" $title_attribs$>$fieldset_title$</legend><div class="$class$">' . "\n",
                    'fieldset_end' => '</div></div></fieldset></div>' . "\n",
                    'fieldstart' => '<div class="form-group fixedform-group" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => '',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label class="control-label fixedform-label"></label>',
                    'inputstart' => '<div class="">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class=""><div class="">',
                    'infoend' => "</div></div>\n",
                    'buttonsstart' => '<div class="form-group"><div class="control-buttons fixedform-controls">',
                    'buttonsend' => "</div></div>\n\n",
                    'customstart' => '<div class="custom_content">',
                    'customend' => "</div>\n",
                    'note_format' => ' <span class="help-inline">%s</span>',
                    // Additional params depending on field type:
                    // - checkbox
                    'inputclass_checkbox' => '',
                    'inputstart_checkbox' => '<div class="controls fixedform-controls"><div class="checkbox"><label>',
                    'inputend_checkbox' => "</label></div></div>\n",
                    'checkbox_newline_start' => '<div class="checkbox">',
                    'checkbox_newline_end' => "</div>\n",
                    // - radio
                    'fieldstart_radio' => '<div class="form-group radio-group" $ID$>' . "\n",
                    'fieldend_radio' => "</div>\n\n",
                    'inputclass_radio' => '',
                    'radio_label_format' => '$radio_option_label$',
                    'radio_newline_start' => '<div class="radio"><label>',
                    'radio_newline_end' => "</label></div>\n",
                    'radio_oneline_start' => '<label class="radio-inline">',
                    'radio_oneline_end' => "</label>\n",
                ];

            case 'user_navigation':
                // The Prev/Next links of users (Used on disp=user to navigate between users):
                return [
                    'block_start' => '<ul class="pager">',
                    'prev_start' => '<li class="previous">',
                    'prev_end' => '</li>',
                    'prev_no_user' => '',
                    'back_start' => '<li>',
                    'back_end' => '</li>',
                    'next_start' => '<li class="next">',
                    'next_end' => '</li>',
                    'next_no_user' => '',
                    'block_end' => '</ul>',
                ];

            case 'button_classes':
                // Button classes (Used to initialize classes for action buttons like buttons to spam vote, or edit an intro post):
                return [
                    'button' => 'btn btn-default btn-xs',
                    'button_red' => 'btn-danger',
                    'button_green' => 'btn-success',
                    'text' => 'btn btn-default btn-xs',
                    'group' => 'btn-group',
                ];

            case 'tooltip_plugin':
                // Plugin name for tooltips: 'bubbletip' or 'popover'
                // We should use 'popover' tooltip plugin for bootstrap skins
                // This tooltips appear on mouse over user logins or on plugin help icons
                return 'popover';
                break;

            case 'plugin_template':
                // Template for plugins:
                return [
                    // This template is used to build a plugin toolbar with action buttons above edit item/comment area:
                    'toolbar_before' => '<div class="btn-toolbar $toolbar_class$" role="toolbar">',
                    'toolbar_after' => '</div>',
                    'toolbar_title_before' => '<div class="btn-toolbar-title">',
                    'toolbar_title_after' => '</div>',
                    'toolbar_group_before' => '<div class="btn-group btn-group-xs" role="group">',
                    'toolbar_group_after' => '</div>',
                    'toolbar_button_class' => 'btn btn-default',
                ];

            case 'modal_window_js_func':
                // JavaScript function to initialize Modal windows, @see echo_user_ajaxwindow_js()
                return 'echo_modalwindow_js_bootstrap';
                break;

            default:
                // Delegate to parent class:
                return parent::get_template($name);
        }
    }

    /**
     * Check if we can display a widget container when access is denied to collection by current user
     *
     * @param string Widget container key: 'header', 'page_top', 'menu', 'sidebar', 'sidebar2', 'footer'
     * @return boolean TRUE to display
     */
    public function show_container_when_access_denied($container_key)
    {
        global $Collection, $Blog;

        if ($Blog->has_access()) {	// If current user has an access to this collection then don't restrict containers:
            return true;
        }

        // Get what containers are available for this skin when access is denied or requires login:
        $access = $this->get_setting('access_login_containers');

        return (! empty($access) && ! empty($access[$container_key]));
    }

    /**
     * Check if we can display a sidebar for the current layout
     *
     * @param boolean TRUE to check if at least one sidebar container is visible
     * @return boolean TRUE to display a sidebar
     */
    public function is_visible_sidebar($check_containers = false)
    {
        $layout = $this->get_setting('layout');

        if ($layout != 'left_sidebar' && $layout != 'right_sidebar') { // Sidebar is not displayed for selected skin layout
            return false;
        }

        if ($check_containers) { // Check if at least one sidebar container is visible
            return ($this->show_container_when_access_denied('sidebar') || $this->show_container_when_access_denied('sidebar2'));
        } else { // We should not check the visibility of the sidebar containers for this case
            return true;
        }
    }
}
