<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package evoskins
 * @subpackage bootstrap_gallery_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class stain_Skin extends Skin
{
    public $version = '7.0.1';

    /**
     * Do we want to use style.min.css instead of style.css ?
     */
    public $use_min_css = 'check';  // true|false|'check' Set this to true for better optimization

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Stain Skin';
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
            'photo' => 'yes',
            'forum' => 'no',
            'manual' => 'maybe',
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
            'footer' => [NT_('Footer'), 100],
            'user_profile_left' => [NT_('User Profile - Left'), 110],
            'user_profile_right' => [NT_('User Profile - Right'), 120],
            '404_page' => [NT_('404 Page'), 130],
        ];
    }

    /**
     * Get the declarations of the widgets that the skin wants to use.
     *
     * @param string Collection type: 'std', 'main', 'photo', 'group', 'forum', 'manual'
     * @param string Skin type: 'normal' - Standard, 'mobile' - Phone, 'tablet' - Tablet
     * @param array Additional params. Example value 'init_as_blog_b' => true
     * @return array Array of default widgets:
     *          - Key - Container code,
     *          - Value - array of widget arrays OR SPECIAL VALUES:
     *             - 'coll_type': Include this container only for collection kinds separated by comma, first char "-" means to exclude,
     *             - 'type': Container type, empty - main container, other values: 'sub', 'page', 'shared', 'shared-sub',
     *             - 'name': Container name,
     *             - 'order': Container order,
     *             - widget data array():
     *                - 0: Widget order (*mandatory field*),
     *                - 1: Widget code (*mandatory field*),
     *                - 'params' - Widget params(array or serialized string),
     *                - 'type' - Widget type(default = 'core', another value - 'plugin'),
     *                - 'enabled' - Boolean value; default is TRUE; FALSE to install the widget as disabled,
     *                - 'coll_type': Include this widget only for collection types separated by comma, first char "-" means to exclude,
     *                - 'skin_type': Include this widget only for skin types separated by comma, first char "-" means to exclude,
     *                - 'install' - Boolean value; default is TRUE; FALSE to skip this widget on install.
     */
    public function get_default_widgets($coll_type = '', $skin_type = 'normal', $context = [])
    {
        global $DB;

        $context = array_merge([
            'current_coll_ID' => null,
            'coll_home_ID' => null,
            'coll_blog_a_ID' => null,
            'coll_photoblog_ID' => null,
            'init_as_home' => false,
            'init_as_blog_a' => false,
            'init_as_blog_b' => false,
            'init_as_forums' => false,
            'init_as_events' => false,
            'install_test_features' => false,
        ], $context);

        $default_widgets = [];

        /* Item in List */
        $default_widgets['item_in_list'] = [
            [10, 'item_title'],
        ];

        /* Item Single Header */
        $default_widgets['item_single_header'] = [
            [10, 'item_title'],
        ];

        /* Item Page */
        $default_widgets['item_page'] = [
            [10, 'item_content'],
        ];

        return $default_widgets;
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        global $Blog;

        // Load to use function get_available_thumb_sizes()
        load_funcs('files/model/_image.funcs.php');
        load_class('widgets/model/_widget.class.php', 'ComponentWidget');

        $r = array_merge([
            /* General Setting
            * ========================================================================== */
            'section_general_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('General Settings'),
            ],
            'color_scheme' => [
                'label' => T_('Color Scheme'),
                'note' => T_('This color will be used for links and clickable elements.') . ' ' . T_('Default value is') . ' <code>#18C54D</code>.',
                'type' => 'color',
                'defaultvalue' => '#18C54D',
            ],
            'body_color' => [
                'label' => T_('Content Color'),
                'note' => T_('Default value is') . ' <code>#555555</code>.',
                'type' => 'color',
                'defaultvalue' => '#555555',
            ],
            'body_background' => [
                'label' => T_('Background Color'),
                'note' => T_('Default value is') . ' <code>#f6f6f6</code>.',
                'type' => 'color',
                'defaultvalue' => '#F6F6F6',
            ],
            'max_image_height' => [
                'label' => T_('Max Image Height'),
                'note' => 'px. ' . T_('Set maximum height for post images.'),
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'banner_public' => [
                'label' => T_('Display status badges'),
                'note' => T_('Display status badges for posts and comments.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'back_to_top' => [
                'label' => T_('Back To Top Button'),
                'note' => T_('Check to display back to top button.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'section_general_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Header
            * ========================================================================== */
            'section_header_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Header Settings'),
            ],
            'header_height' => [
                'label' => T_('Minimum Height'),
                'note' => 'px. ' . T_('Set the minimum height of the header section. If the content is bigger, header height will adapt.'),
                'type' => 'integer',
                'defaultvalue' => '320',
                'size' => 3,
                'allow_empty' => false,
            ],
            'header_custom_bg' => [
                'label' => T_('Background Image'),
                'note' => T_('If this field is left empty, default image will be applied.'),
                'type' => 'fileselect',
                'initialize_with' => 'skins/stain_skin/assets/images/header/header-5.jpg',
                'thumbnail_size' => 'fit-320x320',
            ],
            'header_bg_position_x' => [
                'label' => T_('Horizontal Background Position'),
                'note' => '%. ' . T_('Horizontal position of the background image.') . ' ' . T_('Default value is') . ' <code>50%</code>.',
                'type' => 'integer',
                'defaultvalue' => '50',
                'size' => 3,
            ],
            'header_bg_position_y' => [
                'label' => T_('Vertical Background Position'),
                'note' => '%. ' . T_('Vertical position of the background image.') . ' ' . T_('Default value is') . ' <code>50%</code>.',
                'type' => 'integer',
                'defaultvalue' => '50',
                'size' => 3,
            ],
            'header_bg_attach' => [
                'label' => T_('Background Behavior'),
                'note' => '',
                'type' => 'radio',
                'defaultvalue' => 'initial',
                'options' => [
                    ['initial', T_('Default')],
                    ['fixed', T_('Fixed')],
                ],
            ],
            'header_overlay' => [
                'label' => T_('Header Overlay'),
                'note' => T_('Check if you want to display color overlay for header section.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'color_overlay' => [
                'label' => T_('Header Overlay Color'),
                'note' => T_('Default value is') . ' <code>#000000</code>.',
                'type' => 'color',
                'defaultvalue' => '#000000',
            ],
            'opcity_cv' => [
                'label' => T_('Header Overlay Color Opacity'),
                'note' => T_('Default value is') . ' <code>0.2</code>.',
                'type' => 'select',
                'options' => [
                    '0' => '0',
                    '0.1' => '0.1',
                    '0.15' => '0.15',
                    '0.2' => '0.2',
                    '0.25' => '0.25',
                    '0.3' => '0.3',
                    '0.35' => '0.35',
                    '0.4' => '0.4',
                    '0.45' => '0.45',
                    '0.5' => '0.5',
                    '0.55' => '0.55',
                    '0.6' => '0.6',
                    '0.65' => '0.65',
                    '0.7' => '0.7',
                    '0.75' => '0.75',
                    '0.8' => '0.8',
                    '0.85' => '0.85',
                    '0.9' => '0.9',
                    '0.95' => '0.95',
                    '1' => '1',
                ],
                'defaultvalue' => '0.2',
            ],

            /* HEADER CONTENT OPTIONS
             * ========================================================================== */
            'section_head_con_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Header Content Settings'),
            ],
            'header_content_top' => [
                'label' => T_('Content Padding Top'),
                'note' => 'px. ' . T_('Default value is') . ' <code>125px.</code>',
                'type' => 'integer',
                'allow_empty' => false,
                'defaultvalue' => '125',
                'size' => 5,
            ],
            'header_content_align' => [
                'label' => T_('Header Content Alignment'),
                'note' => T_('Set the content alignment in the header section.'),
                'type' => 'radio',
                'defaultvalue' => 'center',
                'options' => [
                    ['left', T_('Left')],
                    ['center', T_('Center')],
                    ['right', T_('Right')],
                ],
            ],
            'header_color_content' => [
                'label' => T_('Header Content Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#ffffff',
            ],
            'header_text_shadow_content' => [
                'label' => T_('Header Text Shadow'),
                'note' => T_('Check to display text-shadow on title and tagline in header.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'section_head_con_end' => [
                'layout' => 'end_fieldset',
            ],
            'section_header_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Main Navigation
            * ========================================================================== */
            'section_nav_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Main Navigation Menu Settings') . ' (' . T_('All Pages') . ')',
            ],
            'nav_bg' => [
                'label' => T_('Background Color'),
                'note' => T_('Default value is') . ' <code>#1B1B1B</code>.',
                'type' => 'color',
                'defaultvalue' => '#1B1B1B',
                'size' => 20,
            ],
            'nav_sticky' => [
                'label' => T_('Sticky Menu'),
                'note' => T_('Check to enable "sticky menu" ability.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'nav_sticky_shadow' => [
                'label' => T_('Sticky Menu Shadow'),
                'note' => T_('Check to display shadow below the sticky menu for easier distinguish from the content.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'nav_align' => [
                'label' => T_('Menu Alignment'),
                'note' => T_(''),
                'type' => 'radio',
                'defaultvalue' => 'center',
                'options' => [
                    ['left', T_('Left')],
                    ['center', T_('Center')],
                    ['right', T_('Right')],
                ],
            ],
            'nav_hover_style' => [
                'label' => T_('Menu Links Hover Animation'),
                'note' => T_('Choose the animation that appears when hovering menu links.'),
                'type' => 'select',
                'options' => [
                    '1' => T_('Hover animation 1'),
                    '2' => T_('Hover animation 2'),
                    '3' => T_('Hover animation 3'),
                    '4' => T_('Hover animation 4'),
                    '5' => T_('Hover animation 5'),
                    '6' => T_('Hover animation 6'),
                ],
                'defaultvalue' => '1',
            ],
            'nav_color' => [
                'label' => T_('Menu Links Color'),
                'note' => T_('Leave empty for default value.'),
                'type' => 'color',
                'defaultvalue' => '',
            ],
            'nav_color_hov' => [
                'label' => T_('Menu Links Hover Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>. ',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'section_nav_end' => [
                'layout' => 'end_fieldset',
            ],

            /* GALLERY SETTING
            * ========================================================================== */
            'section_gallery_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Category Page Settings'),
            ],
            'cat_heading_bgc' => [
                'label' => T_('Category Name Background Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'gallery_show' => [
                'label' => T_('Posts Per Row'),
                'note' => T_('Select the number of posts per row.'),
                'type' => 'select',
                'options' => [
                    'one_column' => '1 ' . T_('Post'),
                    'two_column' => '2 ' . T_('Posts'),
                    'three_column' => '3 ' . T_('Posts'),
                    'four_column' => '4 ' . T_('Posts'),
                ],
                'defaultvalue' => 'three_column',
            ],
            'gallery_gutter' => [
                'label' => T_('Space Between Posts'),
                'note' => 'px. ' . T_('Default value is') . ' <code>10px</code>.',
                'type' => 'integer',
                'defaultvalue' => '10',
                'size' => 5,
            ],
            'gallery_thumb_size' => [
                'label' => T_('Post Thumbnail Size'),
                'note' => '',
                'defaultvalue' => 'fit-1280x720',
                'type' => 'select',
                'options' => [
                    'original' => 'Original',
                    'fit-1280x720' => 'fit-1280x720',
                    'crop-480x320' => 'crop-480x320',
                ],
            ],
            'cat_post_style' => [
                'label' => T_('Post Layout'),
                'note' => '"' . T_('Default') . '" ' . T_('post layout places image above the title.') . ' "' . T_('Background Image') . '" ' . T_('post layout places image behind the post content.'),
                'type' => 'select',
                'defaultvalue' => 'default',
                'options' => [
                    'default' => T_('Default'),
                    'bg_img' => T_('Background Image'),
                ],
            ],
            'gallery_hover_style' => [
                'label' => T_('Post Image Hover Animation'),
                'note' => T_('Select the hover animation for post images.'),
                'type' => 'select',
                'defaultvalue' => 'opacity',
                'options' => [
                    'none' => T_('None'),
                    'opacity' => T_('Opacity change'),
                    'zoom' => T_('Zoom'),
                    'flip' => T_('Flip'),
                    'right_left' => T_('Right to Left'),
                    'left_right' => T_('Left to Right'),
                    'bt_top' => T_('Bottom to Top'),
                    'top_bt' => T_('Top to Bottom'),
                ],
            ],
            'cat_img_color_overlay' => [
                'label' => T_('Post Image Hover Overlay Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'cat_opcity_overlay' => [
                'label' => T_('Post Image Hover Overlay Color Opacity'),
                'note' => T_('Default value is') . ' <code>0.5</code>.',
                'type' => 'select',
                'options' => [
                    '0' => '0',
                    '0.1' => '0.1',
                    '0.15' => '0.15',
                    '0.2' => '0.2',
                    '0.25' => '0.25',
                    '0.3' => '0.3',
                    '0.35' => '0.35',
                    '0.4' => '0.4',
                    '0.45' => '0.45',
                    '0.5' => '0.5',
                    '0.55' => '0.55',
                    '0.6' => '0.6',
                    '0.65' => '0.65',
                    '0.7' => '0.7',
                    '0.75' => '0.75',
                    '0.8' => '0.8',
                    '0.85' => '0.85',
                    '0.9' => '0.9',
                    '0.95' => '0.95',
                    '1' => '1',
                ],
                'defaultvalue' => '0.5',
            ],
            'gallery_bg' => [
                'label' => T_('Post Card Background Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'cat_color_content' => [
                'label' => T_('Post Card Content Color'),
                'note' => T_('Default value is') . ' <code>#777777</code>.',
                'type' => 'color',
                'defaultvalue' => '#777777',
            ],
            'gallery_shadow' => [
                'label' => T_('Post Card Shadow'),
                'note' => T_('Check to enable shadow behind the post cards.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'cat_title_size' => [
                'label' => T_('Post Title Size'),
                'note' => 'px. ' . T_('Default value is') . ' <code>28px</code>.',
                'type' => 'integer',
                'defaultvalue' => '28',
                'size' => 3,
            ],
            'cat_post_excerpt' => [
                'label' => T_('Post Excerpt'),
                'note' => T_('Check to enable post excerpts.'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
            ],
            'cat_post_tags' => [
                'label' => T_('Post Tags'),
                'note' => T_('Check to enable post tags.'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
            ],
            'cat_post_comment' => [
                'label' => T_('Post Comments Count'),
                'note' => T_('Check to enable post comments count.'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
            ],
            'section_gallery_end' => [
                'layout' => 'end_fieldset',
            ],

            /* POST Settings
            * ========================================================================== */
            'section_posts_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Posts Page Settings'),
            ],
            'posts_full_width' => [
                'label' => T_('Full Width Container'),
                'note' => T_('Check to make post container have full screen width.'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
            ],
            'posts_thumb_size' => [
                'label' => T_('Thumbnail size'),
                'note' => '',
                'defaultvalue' => 'crop-480x320',
                'size' => 10,
                'type' => 'select',
                'options' => [
                    'fit-1280x720' => 'fit-1280x720',
                    'crop-480x320' => 'crop-480x320',
                ],
            ],
            'posts_show' => [
                'label' => T_('Posts Per Column'),
                'note' => T_(''),
                'type' => 'select',
                'options' => [
                    'one_column' => '1 ' . T_('Column'),
                    'two_column' => '2 ' . T_('Columns'),
                    'three_column' => '3 ' . T_('Columns'),
                ],
                'defaultvalue' => 'three_column',
            ],
            'posts_list_space' => [
                'label' => T_('Post Padding'),
                'note' => 'px. ' . T_('Default value is') . ' <code>0px</code>.',
                'type' => 'integer',
                'defaultvalue' => '4',
                'size' => 3,
                'allow_empty' => false,
            ],
            'posts_effect' => [
                'label' => T_('Post Load Animation'),
                'note' => T_('Select the animation type for loading posts.'),
                'type' => 'select',
                'options' => [
                    '0' => T_('None'),
                    '1' => T_('Opacity'),
                    '2' => T_('Move Up'),
                    '3' => T_('Scale Up'),
                    '4' => T_('Fall Perspective'),
                    '5' => T_('Fly'),
                    '6' => T_('Flip'),
                    '7' => T_('Helix'),
                    '8' => T_('Pop Up'),
                ],
                'defaultvalue' => '4',
            ],
            'section_posts_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Single Settings
             * ========================================================================== */
            'section_single_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Single Post Settings'),
            ],
            'single_image_style' => [
                'label' => T_('Style Image Gallery'),
                'note' => T_('Change the style layout image gallery.'),
                'type' => 'select',
                'defaultvalue' => 'grid',
                'options' => [
                    'grid' => T_('Grid'),
                    'masonry' => 'Masonry',
                ],
            ],
            'single_image_grid' => [
                'label' => T_('Image Gallery Column'),
                'note' => T_('Change the gallery column for single Image Gallery'),
                'type' => 'select',
                'options' => [
                    '12' => T_('1 Column'),
                    '6' => T_('2 Column'),
                    '4' => T_('3 Column'),
                    '3' => T_('4 Column'),
                ],
                'defaultvalue' => '4',
            ],
            'single_thumb_size' => [
                'label' => T_('Thumbnail size for Single Disp'),
                'note' => T_('If you select "Masonry" Style for image gallery, the recommended thumbnail size is') . ' <code>fit-1280x720</code>.',
                'defaultvalue' => 'crop-480x320',
                'options' => get_available_thumb_sizes(),
                'type' => 'select',
            ],
            'section_single_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Mediaidx
            * ========================================================================== */
            'section_media_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Photo Index Page Settings'),
            ],
            'max_mediaidx_height' => [
                'label' => T_('Max image height'),
                'note' => 'px. ' . T_('Set the maximum image height.'),
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'mediaidx_column' => [
                'label' => T_('Image columns count'),
                'note' => T_('Select the number of image columns.'),
                'type' => 'select',
                'options' => [
                    'one' => '1 ' . T_('Column'),
                    'two' => '2 ' . T_('Columns'),
                    'three' => '3 ' . T_('Columns'),
                    'four' => '4 ' . T_('Columns'),
                ],
                'defaultvalue' => 'three',
            ],
            'mediaidx_space' => [
                'label' => T_('Image padding'),
                'note' => 'px. ' . T_('Set the padding of images.') . T_('Default value is') . ' <code>10</code>. (' . T_('Also accepts value 0') . ')',
                'type' => 'integer',
                'defaultvalue' => '10',
                'size' => '5',
            ],
            'mediaidx_thumb_size' => [
                'label' => T_('Image size'),
                'note' => '',
                'type' => 'select',
                'defaultvalue' => 'fit-1280x720',
                'options' => [
                    'original' => 'Original',
                    'fit-1280x720' => 'fit-1280x720',
                    'fit-720x500' => 'fit-720x250',
                    'crop-480x320' => 'crop-480x320',
                    'crop-256x256' => 'crop-256x256',
                    'crop-192x192' => 'crop-192x192',
                    'crop-128x128' => 'crop-128x128',
                    'crop-top-320x320' => 'crop-top-320x320',
                    'crop-top-200x200' => 'crop-top-200x200',
                    'crop-top-160x160' => 'crop-top-160x160',
                ],
            ],
            'mediaidx_display' => [
                'label' => T_('Number of images'),
                'note' => T_('Set the number of images showing on this page.'),
                'type' => 'integer',
                'defaultvalue' => '20',
                'size' => 5,
            ],
            'mediaidx_effect' => [
                'label' => T_('Image appearance animation'),
                'note' => T_('Select your favorite animation for loading the images.'),
                'type' => 'select',
                'options' => [
                    '1' => T_('Opacity'),
                    '2' => T_('Move Up'),
                    '3' => T_('Scale Up'),
                    '4' => T_('Fall Perspective'),
                    '5' => T_('Fly'),
                    '6' => T_('Flip'),
                    '7' => T_('Helix'),
                    '8' => T_('Pop Up'),
                ],
                'defaultvalue' => '3',
            ],
            'mediaidx_hover_style' => [
                'label' => T_('Image hover animation'),
                'note' => T_('Select animation for hovering images.'),
                'type' => 'select',
                'defaultvalue' => 'flip',
                'options' => [
                    'none' => T_('None'),
                    'opacity' => T_('Opacity'),
                    'flip' => T_('Flip'),
                    'zoom' => T_('Zoom'),
                    'tb' => T_('Top to Bottom'),
                    'bt' => T_('Bottom to Top'),
                    'rl' => T_('Right to Left'),
                    'lr' => T_('Left to Right'),
                ],
            ],
            'mediaidx_view_btn' => [
                'label' => T_('Display "View" button'),
                'note' => T_('Check to display "View" button when hovering images.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'mediaidx_hover_bg' => [
                'label' => T_('Image hover overlay color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'mediaidx_overlay_opacity' => [
                'label' => T_('Image hover overlay color opacity'),
                'note' => T_('Default value is') . ' <code>0.5</code>.',
                'type' => 'select',
                'defaultvalue' => '0.5',
                'options' => [
                    '0' => '0',
                    '0.05' => '0.05',
                    '0.1' => '0.1',
                    '0.15' => '0.15',
                    '0.2' => '0.2',
                    '0.25' => '0.25',
                    '0.3' => '0.3',
                    '0.35' => '0.35',
                    '0.4' => '0.4',
                    '0.45' => '0.45',
                    '0.5' => '0.5',
                    '0.55' => '0.55',
                    '0.6' => '0.6',
                    '0.65' => '0.65',
                    '0.7' => '0.7',
                    '0.75' => '0.75',
                    '0.8' => '0.8',
                    '0.85' => '0.85',
                    '0.9' => '0.9',
                    '0.95' => '0.95',
                ],
            ],
            'mediaidx_title' => [
                'label' => T_('Display image title'),
                'note' => T_('Check to display image title.'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
            ],
            'mediaidx_title_bg' => [
                'label' => T_('Title Background Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#ffffff',
            ],
            'mediaidx_title_color' => [
                'label' => T_('Title Color'),
                'note' => T_('Default value is') . ' <code>#555555</code>.',
                'type' => 'color',
                'defaultvalue' => '#555555',
            ],
            'mediaidx_title_shadow' => [
                'label' => T_('Title section shadow'),
                'note' => T_('Check to display shadow on title section.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            // 'mediaidx_by' => array(
            // 'label'			=> T_( 'Order by' ),
            // 'note'			=> T_( 'How to order the images.' ),
            // 'type'			=> 'select',
            // 'options' 		=> get_available_sort_options(),
            // 'defaultvalue' 	=> 'datestart',
            // ),
            'mediaidx_dir' => [
                'label' => T_('Direction'),
                'note' => T_('Select the order of sorting the images.'),
                'type' => 'select',
                'options' => [
                    'ASC' => T_('Ascending'),
                    'DESC' => T_('Descending'),
                ],
                'defaultvalue' => 'ASC',
            ],
            'section_media_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Search Disp Settings
             * ========================================================================== */
            'section_search_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Search Page Settings'),
            ],
            'header_search_height' => [
                'label' => T_('Header Height'),
                'note' => 'px. ' . T_('Set the minimum height of the header section. If the content is bigger, header height will adapt.') . ' ' . T_('Default value is') . ' 520px.',
                'type' => 'integer',
                'defaultvalue' => '520',
                'size' => 6,
            ],
            'header_search_bg' => [
                'label' => T_('Background Image'),
                'type' => 'fileselect',
                'initialize_with' => 'skins/stain_skin/assets/images/header/header-8.jpg',
                'thumbnail_size' => 'fit-320x320',
            ],
            'header_search_bg_attach' => [
                'label' => T_('Background Behavior'),
                'note' => T_(''),
                'type' => 'radio',
                'defaultvalue' => 'initial',
                'options' => [
                    ['initial', T_('Default')],
                    ['fixed', T_('Fixed')],
                ],
            ],
            'header_search_heading' => [
                'label' => T_('Heading Text'),
                'note' => T_('Set the Heading Text.'),
                'type' => 'text',
                'size' => '60',
                'defaultvalue' => T_('Search anyting you want.'),
            ],
            'header_search_subhead' => [
                'label' => T_('Subheading Text'),
                'note' => T_('Set the Subheading text.'),
                'type' => 'text',
                'size' => '60',
                'defaultvalue' => T_('Just type any word in the search box.'),
            ],
            'header_btn_search' => [
                'label' => T_('Search Button Text'),
                'note' => '',
                'type' => 'text',
                'size' => '20',
                'defaultvalue' => T_('Search'),
            ],
            'search_align_pagination' => [
                'label' => T_('Pagination Alignment'),
                'note' => '',
                'type' => 'radio',
                'options' => [
                    ['left', T_('Left')],
                    ['center', T_('Center')],
                    ['right', T_('Right')],
                ],
                'defaultvalue' => 'center',
            ],
            'section_search_end' => [
                'layout' => 'end_fieldset',
            ],

            /* BACKGROUND CONTENT FOR LOGIN, LOSSPASWORD, REGISTER AND 404
             * ========================================================================== */
            'section_bg_content_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('User Control Pages') . ' ( disp=login | disp=lostpassword | disp=register | disp=404/403 )',
            ],
            'bgc_style' => [
                'label' => T_('Background Style'),
                'note' => T_('Select the background style for content box.'),
                'type' => 'select',
                'defaultvalue' => 'bg_img',
                'options' => [
                    'bg_img' => T_('Background Image'),
                    'bg_color' => T_('Background Color'),
                ],
            ],
            'bgc_img_custom' => [
                'label' => T_('Background Image'),
                'type' => 'fileselect',
                'initialize_with' => 'skins/stain_skin/assets/images/content/bgc-1.jpg',
                'thumbnail_size' => 'fit-320x320',
            ],
            'bgc_color' => [
                'label' => T_('Background Color'),
                'note' => T_('Default value is') . ' <code>#FFFFFF</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'section_bg_content_end' => [
                'layout' => 'end_fieldset',
            ],

            /* BACKGROUND CONTENT FOR 403 AND 404
             * ========================================================================== */
            'section_bg_content1_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Error and Not Found Pages') . ' (disp=404/403)',
            ],
            'bgc_img_overlay' => [
                'label' => T_('Color Overlay'),
                'note' => T_('Change color overlay if using image for content background.'),
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'bgc_overlay_opacity' => [
                'label' => T_('Opacity Color Overlay'),
                'note' => T_('Set opacity for color overlay.'),
                'type' => 'select',
                'defaultvalue' => '0.5',
                'options' => [
                    '0' => '0',
                    '0.05' => '0.05',
                    '0.1' => '0.1',
                    '0.15' => '0.15',
                    '0.2' => '0.2',
                    '0.25' => '0.25',
                    '0.3' => '0.3',
                    '0.35' => '0.35',
                    '0.4' => '0.4',
                    '0.45' => '0.45',
                    '0.5' => '0.5',
                    '0.55' => '0.55',
                    '0.6' => '0.6',
                    '0.65' => '0.65',
                    '0.7' => '0.7',
                    '0.75' => '0.75',
                    '0.8' => '0.8',
                    '0.85' => '0.85',
                    '0.9' => '0.9',
                    '0.95' => '0.95',
                ],
            ],
            'bgc_color_content' => [
                'label' => T_('Change Content Color'),
                'note' => T_('Set the color of the content box.') . ' ' . T_('Default color is') . ' <code>#555555</code>.',
                'type' => 'color',
                'defaultvalue' => '#555555',
            ],
            'section_bg_content1_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Special Widget Setting
             * ========================================================================== */
            'section_special_widget_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Special Widget Settings') . ' (All Disps)',
            ],
            'ltw_readmore' => [
                'label' => T_('List-type Widgets "Read more" button'),
                'note' => T_('Check to display the "Read more" button after content on all list-type widgets (Excerpt and Teaser)'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'rwd_bgc_widget' => [
                'label' => T_('RWD Widgets Background Color'),
                'note' => T_('Default value is') . ' <code>#FAFAFA</code>',
                'type' => 'color',
                'defaultvalue' => '#FAFAFA',
            ],
            'section_special_widget_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Footer Settings
            * ========================================================================== */
            'section_footer_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Footer Settings'),
            ],
            'footer_bg' => [
                'label' => T_('Background Color'),
                'note' => T_('Default value is') . ' <code>#0E1215</code>.',
                'type' => 'color',
                'defaultvalue' => '#0E1215',
            ],
            'footer_color_content' => [
                'label' => T_('Content Color'),
                'note' => T_('Default value is') . ' <code>#ffffff</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'footer_color_title' => [
                'label' => T_('Titles Color'),
                'note' => T_('Default value is') . ' <code>#ffffff</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'footer_color_link' => [
                'label' => T_('Links Color'),
                'note' => T_('Default value is') . ' <code>#ffffff</code>.',
                'type' => 'color',
                'defaultvalue' => '#FFFFFF',
            ],
            'footer_widget' => [
                'label' => T_('Enable Footer Widget'),
                'note' => T_('Check to Enable footer widget container.'),
                'type' => 'checkbox',
                'defaultvalue' => '0',
            ],
            'footer_widget_column' => [
                'label' => T_('Number of widget columns'),
                'note' => T_(''),
                'type' => 'select',
                'defaultvalue' => '3',
                'options' => [
                    '1' => '1 ' . T_('Column'),
                    '2' => '2 ' . T_('Columns'),
                    '3' => '3 ' . T_('Columns'),
                    '4' => '4 ' . T_('Columns'),
                ],
            ],
            'footer_bottom_align' => [
                'label' => T_('Footer Bottom Layout'),
                'note' => T_(''),
                'type' => 'select',
                'defaultvalue' => 'float',
                'options' => [
                    'float' => T_('Floated layout'),
                    'center' => T_('Centered layout'),
                ],
            ],
            'footer_social' => [
                'label' => T_('Enable Social Icon'),
                'note' => T_('Check to enable Social Icons on footer.'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
            ],
            'section_footer_end' => [
                'layout' => 'end_fieldset',
            ],

            /* Colorbox Image Zoom
            * ========================================================================== */
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

            /* Username Start
            * ========================================================================== */
            'section_username_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Username Options'),
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

            /* Acces Settings
            * ========================================================================== */
            // 'section_access_start' => array(
            // 	'layout' => 'begin_fieldset',
            // 	'label'  => T_('When access is denied or requires login').'... (disp=access_denied and disp=access_requires_login)',
            // ),
            // 	'access_login_containers' => array(
            // 		'label'   => T_('Display on login screen'),
            // 		'note'    => '',
            // 		'type'    => 'checklist',
            // 		'options' => array(
            // 			array( 'header',   sprintf( T_('"%s" container'), NT_('Header') ),    1 ),
            // 			array( 'sidebar',  sprintf( T_('"%s" container'), NT_('Sidebar') ),   0 ),
            // 			array( 'footer',   sprintf( T_('"%s" container'), NT_('Footer') ),    1 )
            // 		),
            // 	),
            // 'section_access_end' => array(
            // 	'layout' => 'end_fieldset',
            // ),

        ], parent::get_param_definitions($params));

        return $r;
    }

    /* Change Class
    * ========================================================================== */
    public function Change_class($id)
    {
        $id = $this->get_setting($id);
        if ($id == $id) {
            return $id;
        }
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

        require_js('assets/scripts/modernizr.custom.js', 'relative');

        // Skin specific initializations:
        // Include Script
        if ($this->get_setting('nav_sticky') == 1) {
            require_js('assets/scripts/jquery.sticky.js', 'relative');
        }

        if ($disp == 'search') {
            require_css('assets/css/slidebars.min.css', 'relative');
            require_js('assets/scripts/slidebars.min.js', 'relative');
        }

        require_js('assets/scripts/jquery.waypoints.min.js', 'relative');
        require_js('assets/scripts/masonry.pkgd.min.js', 'relative');
        require_js('assets/scripts/imagesloaded.pkgd.min.js', 'relative');
        require_js('assets/scripts/classie.js', 'relative');
        require_js('assets/scripts/AnimOnScroll.js', 'relative');
        require_js('assets/scripts/scripts.js', 'relative');

        // Add custom CSS:
        $custom_css = '';

        // Include Font
        // ======================================================================== /
        add_headline('<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto+Slab:300,400,700" rel="stylesheet">');

        /* General
        * ========================================================================== */
        if ($cs = $this->get_setting('color_scheme')) {
            $custom_css .= 'body a, body a:hover, body a:focus, body a:active, #footer .copyright a,
			/* Header */
			.main_navigation ul a
			{ color: ' . $cs . ' }
			.main_navigation ul li.hover-3 a::before, .main_navigation ul li.hover-3 a::after
			{background-color: ' . $cs . '}
			.main_navigation ul li.hover-4 a::before, .main_navigation ul li.hover-4 a::after
			{border-color: ' . $cs . '}
			/* Category | Posts | Global*/
			#content .feature_post .posts__info_author a, .posts_list .feature_post .main_content_gallery .posts__info_author .identity_link_username, .main_pagination .pagination a, .main_pagination .pagination span, .main_content .error_404_content h1, #content .evo_comment .permalink_right, #content .evo_comment .comment_reply, .disp_threads .main_content .form_send_contacts .btn, .disp_contacts .main_content .form_send_contacts .btn, .disp_threads .main_content .form_add_contacts .SaveButton, .disp_contacts .main_content .form_add_contacts .SaveButton, .SaveButton.btn-primary, .disp_messages .evo_private_messages_list .messages_list_actions .btn, .disp_msgform .main_content .evo_form .submit, .disp_search .main_content .pagination a, .disp_search .main_content .pagination span, .disp_help .tag_cloud a, .disp_tags .tag_cloud a, #content .nav_album .all_albums, #content .nav_album .nav_posts .previous a, #content .nav_album .nav_posts .next a, #content .evo_post_content .evo_post__full .gallery-single-content .panel-title a, #content .evo_post_content .evo_post__except .gallery-single-content .panel-title a, #content .item_comments .evo_comment__meta_info .btn, #content .item_comments .panel-heading .user, #footer .footer__widgets .evo_widget ul a:hover, #footer .footer__widgets .evo_widget ul a:active, #footer .footer__widgets .evo_widget ul a:focus, #main_result_content .search_result .search_title a:hover, #main_result_content .search_result .search_title a:active, #main_result_content .search_result .search_title a:focus, #content .cat_content .cats_list .evo_post_content .evo_post__text .excerpt_more a, .disp_usercomments .main_content .results .pagination a, .disp_useritems .main_content .results .pagination a, .single_pagination .pagination li a, .single_pagination .pagination li span
			{color: ' . $cs . '}
			.posts_gallery .main_content_gallery .btn_cat::after, #content .feature_post .posts__info_cat, #content .feature_post .posts__info_excerpt_link a:hover, #content .feature_post .posts__info_excerpt_link a:active, #content .feature_post .posts__info_excerpt_link a:focus, .posts_list .evo_posts .posts__info_cat a:hover, .posts_list .evo_posts .posts__info_cat a:active, .posts_list .evo_posts .posts__info_cat a:focus, .posts_list .evo_posts .posts_divider, .main_pagination .pagination li.active span, .main_pagination .pagination li.active a, .main_pagination .pagination a:hover, .main_pagination .pagination span:hover, .main_pagination .pagination a:active, .main_pagination .pagination span:active, .main_pagination .pagination a:focus, .main_pagination .pagination span:focus, .main_content .error_back .back_to_home, #content .evo_comment .permalink_right:hover, #content .evo_comment .comment_reply:hover, #content .evo_comment .permalink_right:active, #content .evo_comment .comment_reply:active, #content .evo_comment .permalink_right:focus, #content .evo_comment .comment_reply:focus, .disp_threads .main_content .form_send_contacts .btn:hover, .disp_contacts .main_content .form_send_contacts .btn:hover, .disp_threads .main_content .form_send_contacts .btn:active, .disp_contacts .main_content .form_send_contacts .btn:active, .disp_threads .main_content .form_send_contacts .btn:focus, .disp_contacts .main_content .form_send_contacts .btn:focus, .disp_threads .main_content .form_add_contacts .SaveButton:hover, .disp_contacts .main_content .form_add_contacts .SaveButton:hover, .disp_threads .main_content .form_add_contacts .SaveButton:active, .disp_contacts .main_content .form_add_contacts .SaveButton:active, .disp_threads .main_content .form_add_contacts .SaveButton:focus, .disp_contacts .main_content .form_add_contacts .SaveButton:focus, .disp_threads .main_content .results .panel-heading .btn-primary, .disp_contacts .main_content .results .panel-heading .btn-primary, .disp_messages .evo_private_messages_list .messages_navigation .floatleft:hover, .disp_messages .evo_private_messages_list .messages_navigation .floatright:hover, .disp_messages .evo_private_messages_list .messages_navigation .floatleft:active, .disp_messages .evo_private_messages_list .messages_navigation .floatright:active, .disp_messages .evo_private_messages_list .messages_navigation .floatleft:focus, .disp_messages .evo_private_messages_list .messages_navigation .floatright:focus, .SaveButton.btn-primary:hover, .SaveButton.btn-primary:active, .SaveButton.btn-primary:focus, .disp_messages .evo_private_messages_list .messages_list_actions .btn:hover, .disp_messages .evo_private_messages_list .messages_list_actions .btn:active, .disp_messages .evo_private_messages_list .messages_list_actions .btn:focus, .disp_msgform .main_content .evo_form .submit:hover, .disp_msgform .main_content .evo_form .submit:active, .disp_msgform .main_content .evo_form .submit:focus, .nav_search ul li.active, .nav_search ul a:hover, .nav_search ul a:active, .nav_search ul a:focus, .search_head .search_box .main_search_box .extended_search_form .search_submit, .disp_search .main_content .pagination li.active a, .disp_search .main_content .pagination li.active span, .disp_search .main_content .pagination a:hover, .disp_search .main_content .pagination span:hover, .disp_search .main_content .pagination a:active, .disp_search .main_content .pagination span:active, .disp_search .main_content .pagination a:focus, .disp_search .main_content .pagination span:focus, #main_result_content .search_result .search_result_score::before, #main_result_content .search_result  .search_result_score::after, .disp_help .tag_cloud a:hover, .disp_tags .tag_cloud a:hover, .disp_help .tag_cloud a:focus, .disp_tags .tag_cloud a:focus, .disp_help .tag_cloud a:active, .disp_tags .tag_cloud a:active, .disp_user .pager a:hover, .disp_user .pager a:active, .disp_user .pager a:focus, #content .nav_album .all_albums:hover, #content .nav_album .all_albums:focus, #content .nav_album .nav_posts .previous a:hover, #content .nav_album .nav_posts .next a:hover, #content .nav_album .nav_posts .previous a:active, #content .nav_album .nav_posts .next a:active, #content .nav_album .nav_posts .previous a:focus, #content .nav_album .nav_posts .next a:focus, #content .item_comments .evo_comment__meta_info .btn:hover, #content .item_comments .evo_comment__meta_info .btn:active, #content .item_comments .evo_comment__meta_info .btn:focus, #content .item_comments .panel-body .action_icon:hover, #content .item_comments .panel-body .action_icon:active, #content .item_comments .panel-body .action_icon:focus, .widget_core_coll_search_form .compact_search_form .search_submit, .widget_core_coll_tag_cloud .tag_cloud a:hover, .widget_core_coll_tag_cloud .tag_cloud a:active, .widget_core_coll_tag_cloud .tag_cloud a:focus, .widget_plugin_evo_Calr .bCalendarTable #bCalendarToday, .widget_core_poll .SaveButton, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:hover, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:active, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:focus, #footer .footer__widgets .evo_widget .panel .submit:hover, #footer .footer__widgets .evo_widget .panel .submit:active, #footer .footer__widgets .evo_widget .panel .submit:focus, .cd_top, .cd_top:hover, .cd_top:active, .cd_top:focus, #content .evo_post_content .post_tags a:hover, #content .evo_post_content .post_tags a:focus, #content .evo_post_content .post_tags a:active, #content .item_comments .evo_post_comment_notification .btn:hover, #content .item_comments .evo_post_comment_notification .btn:active, #content .item_comments .evo_post_comment_notification .btn:focus, .disp_profile .main_content .profile_tabs a:hover, .disp_avatar .main_content .profile_tabs a:hover, .disp_pwdchange .main_content .profile_tabs a:hover, .disp_userprefs .main_content .profile_tabs a:hover, .disp_subs .main_content .profile_tabs a:hover, .disp_profile .main_content .profile_tabs a:active, .disp_avatar .main_content .profile_tabs a:active, .disp_pwdchange .main_content .profile_tabs a:active, .disp_userprefs .main_content .profile_tabs a:active, .disp_subs .main_content .profile_tabs a:active, .disp_profile .main_content .profile_tabs a:focus, .disp_avatar .main_content .profile_tabs a:focus, .disp_pwdchange .main_content .profile_tabs a:focus, .disp_userprefs .main_content .profile_tabs a:focus, .disp_subs .main_content .profile_tabs a:focus, .disp_profile .main_content .panel .panel-body .help-inline .btn:hover, .disp_avatar .main_content .panel .panel-body .help-inline .btn:hover, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:hover, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:hover, .disp_subs .main_content .panel .panel-body .help-inline .btn:hover, .disp_profile .main_content .panel .panel-body .help-inline .btn:active, .disp_avatar .main_content .panel .panel-body .help-inline .btn:active, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:active, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:active, .disp_subs .main_content .panel .panel-body .help-inline .btn:active, .disp_profile .main_content .panel .panel-body .help-inline .btn:focus, .disp_avatar .main_content .panel .panel-body .help-inline .btn:focus, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:focus, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:focus, .disp_subs .main_content .panel .panel-body .help-inline .btn:focus, .disp_mediaidx .main_content .widget_core_coll_media_index .image_content a:after, #content .cat_content .cats_list .evo_post_content .evo_post__text .excerpt_more a:hover, #content .cat_content .cats_list .evo_post_content .evo_post__text .excerpt_more a:active, #content .cat_content .cats_list .evo_post_content .evo_post__text .excerpt_more a:focus, .disp_usercomments .main_content .results .pagination .active a, .disp_useritems .main_content .results .pagination .active a, .disp_usercomments .main_content .results .pagination a:hover, .disp_useritems .main_content .results .pagination a:hover, .disp_usercomments .main_content .results .pagination a:active, .disp_useritems .main_content .results .pagination a:active, .disp_usercomments .main_content .results .pagination a:focus, .disp_useritems .main_content .results .pagination a:focus, .single_pagination .pagination li.active a, .single_pagination .pagination li.active span, .single_pagination .pagination li a:hover, .single_pagination .pagination li span:hover, .single_pagination .pagination li a:active, .single_pagination .pagination li span:active, .single_pagination .pagination li a:focus, .single_pagination .pagination li span:focus
			{background-color: ' . $cs . '}
			.posts_list .evo_posts .posts__info_cat a:hover, .posts_list .evo_posts .posts__info_cat a:active, .posts_list .evo_posts .posts__info_cat a:focus, .main_pagination .pagination li.active span, .main_pagination .pagination li.active a, .main_pagination .pagination a, .main_pagination .pagination span, .main_pagination .pagination a:hover, .main_pagination .pagination span:hover, .main_pagination .pagination a:active, .main_pagination .pagination span:active, .main_pagination .pagination a:focus, .main_pagination .pagination span:focus, #content .evo_comment .permalink_right, #content .evo_comment .comment_reply, .disp_threads .main_content .form_send_contacts .btn, .disp_contacts .main_content .form_send_contacts .btn, .disp_threads .main_content .form_add_contacts .SaveButton, .disp_contacts .main_content .form_add_contacts .SaveButton, .disp_threads .main_content .results .panel-heading .btn-primary, .disp_contacts .main_content .results .panel-heading .btn-primary, .disp_messages .evo_private_messages_list .messages_navigation .floatleft, .disp_messages .evo_private_messages_list .messages_navigation .floatright, .SaveButton.btn-primary, .disp_messages .evo_private_messages_list .messages_list_actions .btn, .disp_threads .main_content .evo_form__thread .form_text_input:focus, .disp_contacts .main_content .evo_form__thread .form_text_input:focus, .disp_threads .main_content .evo_form__thread .form_textarea_input:focus, .disp_contacts .main_content .evo_form__thread .form_textarea_input:focus, .disp_msgform .main_content .evo_form .form_text_input:focus, .disp_msgform .main_content .evo_form .form_textarea_input:focus, .disp_msgform .main_content .evo_form .submit, .disp_postidx .widget_core_coll_post_list ul a:hover, .disp_postidx .widget_core_coll_post_list ul a:active, .disp_postidx .widget_core_coll_post_list ul a:focus, .search_head .search_box .main_search_box .extended_search_form .search_submit, #main_result_content .search_result .search_result_score, .disp_help .tag_cloud a, .disp_tags .tag_cloud a, .disp_user .pager a, #content .nav_album .all_albums, #content .nav_album .all_albums:hover, #content .nav_album .all_albums:focus, #content .nav_album .nav_posts .previous a, #content .nav_album .nav_posts .next a, #content .evo_post_content .widget_core_item_small_print, #content .evo_post_content .widget_core_item_tags, #content .evo_post_content .widget_core_item_about_author, #content .evo_post_content .panel-heading, #content .item_comments .evo_comment__meta_info .btn, #content .evo_form__comment .form_text_input, #content .evo_form__comment .form_textarea_input, #content .item_comments .panel-body .action_icon, #content .evo_post_content .evo_post__full .evo_post__full_text blockquote, #content .evo_post_content .evo_post__except .evo_post__full_text blockquote, #content .evo_post_content .evo_post__full .evo_post__except_text blockquote, #content .evo_post_content .evo_post__except .evo_post__except_text blockquote, .disp_front .evo_front_page .evo_widget.widget_core_coll_search_form .compact_search_form .search_field, .widget_core_coll_search_form .compact_search_form .search_submit, .widget_core_coll_tag_cloud .tag_cloud a, .widget_plugin_evo_Calr .bCalendarTable caption, .widget_plugin_evo_Calr .bCalendarTable #bCalendarToday, .widget_plugin_evo_Calr .bCalendarTable td, .widget_plugin_evo_Calr .bCalendarTable td:last-child, .widget_plugin_evo_Calr .bCalendarTable th, .widget_core_poll .SaveButton, .form_text_input:focus, .form_textarea_input:focus, #footer .footer__widgets .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:hover, #footer .footer__widgets .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:active, #footer .footer__widgets .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:focus, .disp_front .evo_front_page .evo_widget .form-group .form_text_input:hover, .disp_front .evo_front_page .evo_widget .form-group .form_text_input:active, .disp_front .evo_front_page .evo_widget .form-group .form_text_input:focus, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:hover, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:active, .disp_front .evo_front_page .evo_widget .panel .panel-body .submit:focus, #footer .footer__widgets .evo_widget .panel .submit:hover, #footer .footer__widgets .evo_widget .panel .submit:active, #footer .footer__widgets .evo_widget .panel .submit:focus, #content .evo_post_content .post_tags a, .disp_profile .main_content .profile_tabs, .disp_avatar .main_content .profile_tabs, .disp_pwdchange .main_content .profile_tabs, .disp_userprefs .main_content .profile_tabs, .disp_subs .main_content .profile_tabs, .disp_profile .main_content .profile_tabs a:hover, .disp_avatar .main_content .profile_tabs a:hover, .disp_pwdchange .main_content .profile_tabs a:hover, .disp_userprefs .main_content .profile_tabs a:hover, .disp_subs .main_content .profile_tabs a:hover, .disp_profile .main_content .profile_tabs a:active, .disp_avatar .main_content .profile_tabs a:active, .disp_pwdchange .main_content .profile_tabs a:active, .disp_userprefs .main_content .profile_tabs a:active, .disp_subs .main_content .profile_tabs a:active, .disp_profile .main_content .profile_tabs a:focus, .disp_avatar .main_content .profile_tabs a:focus, .disp_pwdchange .main_content .profile_tabs a:focus, .disp_userprefs .main_content .profile_tabs a:focus, .disp_subs .main_content .profile_tabs a:focus, .form_text_input:focus, .form_textarea_input:focus, .disp_profile .main_content .panel .panel-body .form_text_input:hover, .disp_avatar .main_content .panel .panel-body .form_text_input:hover, .disp_pwdchange .main_content .panel .panel-body .form_text_input:hover, .disp_userprefs .main_content .panel .panel-body .form_text_input:hover, .disp_subs .main_content .panel .panel-body .form_text_input:hover, .disp_profile .main_content .panel .panel-body .form_text_input:active, .disp_avatar .main_content .panel .panel-body .form_text_input:active, .disp_pwdchange .main_content .panel .panel-body .form_text_input:active, .disp_userprefs .main_content .panel .panel-body .form_text_input:active, .disp_subs .main_content .panel .panel-body .form_text_input:active, .disp_profile .main_content .panel .panel-body .form_text_input:focus, .disp_avatar .main_content .panel .panel-body .form_text_input:focus, .disp_pwdchange .main_content .panel .panel-body .form_text_input:focus, .disp_userprefs .main_content .panel .panel-body .form_text_input:focus, .disp_subs .main_content .panel .panel-body .form_text_input:focus, .disp_profile .main_content .panel .panel-body .help-inline .btn:hover, .disp_avatar .main_content .panel .panel-body .help-inline .btn:hover, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:hover, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:hover, .disp_subs .main_content .panel .panel-body .help-inline .btn:hover, .disp_profile .main_content .panel .panel-body .help-inline .btn:active, .disp_avatar .main_content .panel .panel-body .help-inline .btn:active, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:active, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:active, .disp_subs .main_content .panel .panel-body .help-inline .btn:active, .disp_profile .main_content .panel .panel-body .help-inline .btn:focus, .disp_avatar .main_content .panel .panel-body .help-inline .btn:focus, .disp_pwdchange .main_content .panel .panel-body .help-inline .btn:focus, .disp_userprefs .main_content .panel .panel-body .help-inline .btn:focus, .disp_subs .main_content .panel .panel-body .help-inline .btn:focus, #content .cat_content .cats_list .evo_post_content .evo_post__text .excerpt_more a, #content .evo_post_content .widget_core_item_small_print, #content .evo_post_content .evo_post_tags, #content .evo_post_content .widget_core_item_about_author, #content .evo_post_content .widget_core_item_vote, #content .evo_post_content .widget_core_item_attachments .evo_post_attachments, .disp_usercomments .main_content .results .pagination .active a, .disp_useritems .main_content .results .pagination .active a, .disp_usercomments .main_content .results .pagination a:hover, .disp_useritems .main_content .results .pagination a:hover, .disp_usercomments .main_content .results .pagination a:active, .disp_useritems .main_content .results .pagination a:active, .disp_usercomments .main_content .results .pagination a:focus, .disp_useritems .main_content .results .pagination a:focus
			{border-color: ' . $cs . '}
			.disp_profile .main_content .profile_tabs li.active a, .disp_avatar .main_content .profile_tabs li.active a, .disp_pwdchange .main_content .profile_tabs li.active a, .disp_userprefs .main_content .profile_tabs li.active a, .disp_subs .main_content .profile_tabs li.active a
			{border-top-color: ' . $cs . '; border-left-color: ' . $cs . '; border-right-color: ' . $cs . '}
			#content .evo_post_content .evo_post__full .evo_post__full_text .evo_print a, #content .evo_post_content .evo_post__except .evo_post__full_text .evo_print a, #content .evo_post_content .evo_post__full .evo_post__except_text .evo_print a, #content .evo_post_content .evo_post__except .evo_post__except_text .evo_print a
			{ color: ' . $cs . ' !important; }
			';
        }

        if ($cs_mediaquery = $this->get_setting('color_scheme')) {
            $custom_css .= '
			@media screen and (max-width: 480px) {
				.disp_profile .main_content .profile_tabs li.active a,
				.disp_avatar .main_content .profile_tabs li.active a,
				.disp_pwdchange .main_content .profile_tabs li.active a,
				.disp_userprefs .main_content .profile_tabs li.active a,
				.disp_subs .main_content .profile_tabs li.active a {
				border: 1px solid ' . $cs_mediaquery . ';
				background-color: ' . $cs_mediaquery . ';
				}
				}
			';
        }

        if ($color = $this->get_setting('body_color')) {
            $custom_css .= '#skin_wrapper { color: ' . $color . ' }';
        }

        if ($bg = $this->get_setting('body_background')) {
            $custom_css .= '#skin_wrapper,
			#sb-site, .sb-site-container
			{ background-color: ' . $bg . ' }';
        }

        // Limit images by max height:
        $max_image_height = intval($this->get_setting('max_image_height'));
        if ($max_image_height > 0) {
            $custom_css .= '.evo_image_block img, .single-image img { max-height: ' . $max_image_height . 'px; width: auto; }' . "\n";
        }

        /* Sitewide Custom
         * ========================================================================== */
        if ($bg = $this->get_setting('sitewide_background')) {
            $custom_css .= '.sitewide_header{ background: ' . $bg . ' !important }';
        }

        if ($color = $this->get_setting('sitewide_color_text')) {
            $custom_css .= '.sitewide_header a, .sitewide_footer a, .sitewide_header .swhead_item.user { color:' . $color . ' !important }';
        }

        if ($bg = $this->get_setting('sitewide_bg_active')) {
            $custom_css .= '.sitewide_header a.swhead_item_selected, .sitewide_header a.swhead_item:hover { background: ' . $bg . ' !important }';
        }

        if ($color = $this->get_setting('sitewide_color_text_active')) {
            $custom_css .= '.sitewide_header a.swhead_item_selected, .sitewide_header a.swhead_item:hover { color: ' . $color . ' !important }';
        }

        /* Header Settings
        * ========================================================================== */
        if ($height = $this->get_setting('header_height')) {
            $custom_css .= '.main_header{ min-height: ' . $height . 'px; }';
        }

        if ($this->get_setting('header_custom_bg')) {
            $FileCache = &get_FileCache();
            $bg_image_File = &$FileCache->get_by_ID($this->get_setting('header_custom_bg'), false, false);
            $custom_css .= ".main_header{ background-image: url('" . $bg_image_File->get_url() . "') }";
        }

        $bg_header_x = $this->get_setting('header_bg_position_x');
        $bg_header_y = $this->get_setting('header_bg_position_y');
        if (! empty($bg_header_x) || ! empty($bg_header_y)) {
            $custom_css .= '.main_header{ background-position: ' . $bg_header_x . '% ' . $bg_header_y . '%; }';
        }

        $header_bg_attach = $this->get_setting('header_bg_attach');
        switch ($header_bg_attach) {
            case $header_bg_attach:
                $custom_css .= '.main_header{ background-attachment: ' . $header_bg_attach . '; }';
                break;
        }

        // Color Overlay
        if ($this->get_setting('header_overlay') == 0) {
            $custom_css .= '.main_header::after{ display: none }';
        }
        if ($color = $this->get_setting('color_overlay')) {
            $custom_css .= '.main_header::after{ background-color: ' . $color . ' }';
        }
        $overlay_opacity = $this->get_setting('opcity_cv');
        switch ($overlay_opacity) {
            case $overlay_opacity:
                $custom_css .= '.main_header::after{ opacity: ' . $overlay_opacity . ' }';
                break;
        }

        // Content Header
        if ($top = $this->get_setting('header_content_top')) {
            $custom_css .= '.main_header .brand { padding-top: ' . $top . 'px }';
        }

        $align = $this->get_setting('header_content_align');
        switch ($align) {
            case $align:
                $custom_css .= '.main_header .brand{ text-align: ' . $align . '; }';
                break;
        }

        if ($color = $this->get_setting('header_color_content')) {
            $custom_css .= '.main_header .brand .evo_widget.widget_core_coll_title a, .main_header .brand .evo_widget.widget_core_coll_tagline { color: ' . $color . ' }';
        }

        if ($this->get_setting('header_text_shadow_content') == 0) {
            $custom_css .= '.main_header .brand .evo_widget.widget_core_coll_title a, .main_header .brand .evo_widget.widget_core_coll_tagline { text-shadow: none; }';
        }

        /* MAIN NAVIGATION
        * ========================================================================== */
        if ($bg = $this->get_setting('nav_bg')) {
            $custom_css .= '.main_navigation{ background-color: ' . $bg . ' }';
        }

        $nav_align = $this->get_setting('nav_align');
        switch ($nav_align) {
            case 'left':
                $custom_css .= '@media (min-width: 768px) {.main_navigation .nav-tabs{ text-align: left; }}';
                break;

            case 'right':
                $custom_css .= '@media (min-width: 768px) {.main_navigation .nav-tabs{ text-align: right; }}';
                break;
        }

        if ($this->get_setting('nav_sticky_shadow') == 0) {
            $custom_css .= '.sticky-wrapper.is-sticky .main_navigation { box-shadow: none; }';
        }

        if ($color = $this->get_setting('nav_color')) {
            $custom_css .= '.main_navigation .nav-tabs a{ color: ' . $color . ' }';
            $custom_css .= '.main_navigation ul li.hover-3 a::before, .main_navigation ul li.hover-3 a::after { background-color: ' . $color . ' }';
            $custom_css .= '.main_navigation ul li.hover-4 a::before, .main_navigation ul li.hover-4 a::after { border-color: ' . $color . ' }';
        }

        if ($color = $this->get_setting('nav_color_hov')) {
            $custom_css .= '
			.main_navigation .nav-tabs li:hover a, .main_navigation .nav-tabs li:active a, .main_navigation .nav-tabs li:focus a, .main_navigation .nav-tabs li.active a { color: ' . $color . ' !important;}';
            $custom_css .= '.main_navigation ul li.hover-1 a::before, .main_navigation ul li.hover-1 a::after, .main_navigation ul li.hover-2 a::after, .main_navigation ul li.hover-3 .selected::before, .main_navigation ul li.hover-3 .selected::after, .main_navigation ul li.hover-3:hover a::before, .main_navigation ul li.hover-3:active a::before, .main_navigation ul li.hover-3:focus a::before, .main_navigation ul li.hover-3:hover a::after, .main_navigation ul li.hover-3:active a::after, .main_navigation ul li.hover-3:focus a::after, .main_navigation ul li.hover-5 a::before, .main_navigation ul li.hover-5 a::after, .main_navigation ul li.hover-6 a::before, .main_navigation ul li.hover-6 a::after
			{ background-color: ' . $color . ' }';
            $custom_css .= '.main_navigation ul li.hover-4 a::after { border-color: ' . $color . ' }';
        }

        /* GALLERY OPTIONS
        * ========================================================================== */
        if ($bg = $this->get_setting('cat_heading_bgc')) {
            $custom_css .= '#content .cat_content .category_title { background-color: ' . $bg . ' }';
        }

        if ($bg = $this->get_setting('gallery_bg')) {
            // $custom_css .= '.posts_gallery .main_content_gallery .cat_title::after { background-color: '.$bg.' }';
            $custom_css .= '#content .cat_content .cats_list .evo_post_content { background-color: ' . $bg . ' }';
        }

        if ($color = $this->get_setting('cat_color_content')) {
            $custom_css .= '#content .cat_content .cats_list .evo_post_content .evo_post_title .date, #content .cat_content .cats_list .evo_post_content .evo_post__text { color: ' . $color . ' }';
        }

        if ($this->get_setting('cat_post_style') == 'bg_img') {
            $custom_css .= '#content .cat_content .cats_list .post_bg_image .evo_post_images img {  width: 100%; height: 100%; }';
        }

        if ($color = $this->get_setting('cat_img_color_overlay')) {
            $op = $this->Change_class('cat_opcity_overlay');
            $custom_css .= '#content .cat_content .cats_list .evo_post_images a:before, #content .cat_content .cats_list .no_image a:before { background-color: ' . $color . ';}';
            $custom_css .= '#content .cat_content .cats_list .evo_post:hover .evo_post_images a:before, #content .cat_content .cats_list .evo_post:focus .evo_post_images a:before, #content .cat_content .cats_list .evo_post:active .evo_post_images a:before, #content .cat_content .cats_list .evo_post:hover .no_image a:before, #content .cat_content .cats_list .evo_post:focus .no_image a:before, #content .cat_content .cats_list .evo_post:active .no_image a:before{ opacity: ' . $op . ' }';
        }

        if ($space = $this->get_setting('gallery_gutter')) {
            // $custom_css .= '.posts_gallery .evo_post, .posts_gallery .feature_post{ padding: '.$space.'px; }';
            $custom_css .= '#content .cat_content .cats_list .evo_post { padding: ' . $space . 'px }';
            $custom_css .= '#content .cat_content .cats_list { margin-left: -' . $space . 'px; margin-right: -' . $space . 'px; }';
        }

        if ($this->get_setting('gallery_shadow') == 0) {
            // $custom_css .= '.posts_gallery .main_content_gallery:hover, .posts_gallery .main_content_gallery:active, .posts_gallery .main_content_gallery:focus
            // { box-shadow: none; }';
            $custom_css .= '#content .cat_content .cats_list .evo_post_content, #content .cat_content .category_title { box-shadow: none !important; }';
        }

        if ($this->get_setting('gallery_hover_style') == 'none') {
            $custom_css .= '#content .cat_content .cats_list .evo_post:hover .evo_post_content, #content .cat_content .cats_list .evo_post:focus .evo_post_content, #content .cat_content .cats_list .evo_post:active .evo_post_content { box-shadow: 1px 2px 2px rgba(0, 0, 0, 0.05); transform: none; }';
        }

        if ($fz = $this->get_setting('cat_title_size')) {
            $custom_css .= '@media screen and ( min-width: 1024px ) { .posts_gallery .main_content_gallery .cat_title_link { font-size: ' . $fz . 'px; } }';
            $custom_css .= '@media screen and ( min-width: 1024px ) { #content .cat_content .cats_list .evo_post_content .evo_post_title h3 { font-size: ' . $fz . 'px; } }';
        }

        if ($color = $this->get_setting('cat_title_color')) {
            $custom_css .= '.posts_gallery .main_content_gallery .cat_title_link { color: ' . $color . '; }';
        }
        if ($bg = $this->get_setting('cat_view_bg')) {
            $custom_css .= '.posts_gallery .main_content_gallery .btn_cat:after { background-color: ' . $bg . ' }';
        }

        /* MEDIAIDX OPTION | PHOTOBLOG OPTIONS
         * ========================================================================== */
        $max_image_height = intval($this->get_setting('max_mediaidx_height'));
        if ($max_image_height > 0) {
            $custom_css .= '.disp_mediaidx .evo_image_index .image_content img { max-height: ' . $max_image_height . 'px; width: auto; }' . "\n";
        }

        if ($space = $this->get_setting('mediaidx_space')) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content { padding: ' . $space . 'px }';
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .evo_image_index { margin-left: -' . $space . 'px;  margin-right: -' . $space . 'px }';
        }

        if ($bg = $this->get_setting('mediaidx_title_bg')) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content .note{ background-color: ' . $bg . ' }';
        }
        if ($color = $this->get_setting('mediaidx_title_color')) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content .note{ color: ' . $color . ' }';
        }
        if ($this->get_setting('mediaidx_title_shadow') == false) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content .note{ box-shadow: none; }';
        }

        if ($this->get_setting('mediaidx_view_btn') == 0) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content a:after { display: none; }';
        }

        if ($color = $this->get_setting('mediaidx_hover_bg')) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content a:before { background-color: ' . $color . ' }';
        }
        if ($opacity = $this->Change_class('mediaidx_overlay_opacity')) {
            $custom_css .= '.disp_mediaidx .main_content .widget_core_coll_media_index .image_content:hover a:before, .disp_mediaidx .main_content .widget_core_coll_media_index .image_content:active a:before, .disp_mediaidx .main_content .widget_core_coll_media_index .image_content:focus a:before { opacity: ' . $opacity . ' }';
        }

        /* POST CUSTOM OPTIONS
         * ========================================================================== */
        if ($space = $this->get_setting('posts_list_space')) {
            $custom_css .= '.posts_list .evo_posts { padding: ' . $space . 'px; }';
            $custom_css .= '.posts_list { margin-left: -' . $space . 'px; margin-right: -' . $space . 'px; }';
        }

        /* SEARCH DISP OPTIONS
         * ========================================================================== */
        if ($this->get_setting('header_search_bg')) {
            $FileCache = &get_FileCache();
            $bg_image_File1 = &$FileCache->get_by_ID($this->get_setting('header_search_bg'), false, false);
            $custom_css .= ".search_head{ background-image: url('" . $bg_image_File1->get_url() . "') }";
        }
        if ($height = $this->get_setting('header_search_height')) {
            $custom_css .= '.search_head{ height: ' . $height . 'px }';
        }
        if ($bg_attach = $this->get_setting('header_search_bg_attach')) {
            $custom_css .= '.search_head{ background-attachment: ' . $bg_attach . ' }';
        }

        /* SPESIAL CUSTOM STYLE
         * ========================================================================== */
        if ($this->get_setting('ltw_readmore') == 0) {
            $custom_css .= '
			.widget_core_coll_post_list .item_content a,
			.widget_core_coll_featured_posts .item_content a,
			.widget_core_coll_related_post_list, .item_content a,
			.widget_core_coll_page_list .item_content a,
			.widget_core_coll_item_list .item_content a,
			.widget_core_coll_flagged_list .item_content a,
			.widget_core_coll_post_list .item_excerpt a,
			.widget_core_coll_featured_posts .item_excerpt a,
			.widget_core_coll_related_post_list, .item_excerpt a,
			.widget_core_coll_page_list .item_excerpt a,
			.widget_core_coll_item_list .item_excerpt a,
			.widget_core_coll_flagged_list .item_excerpt a
			{ display: none !important }
			';
        }
        if ($color = $this->get_setting('rwd_bgc_widget')) {
            $custom_css .= '.widget_rwd_content { background: ' . $color . " !important }\n";
        }

        /* FOOTER CUSTOM OPTIONS
        * ========================================================================== */
        if ($bg = $this->get_setting('footer_bg')) {
            $custom_css .= '#footer{ background-color: ' . $bg . ' }';
        }

        if ($color = $this->get_setting('footer_color_content')) {
            $custom_css .= '#footer .footer__widgets{ color: ' . $color . ' }';
        }

        if ($color = $this->get_setting('footer_color_title')) {
            $custom_css .= '#footer .footer__widgets .widget_title{ color: ' . $color . ' }';
        }

        if ($color = $this->get_setting('footer_color_link')) {
            $custom_css .= '#footer .footer__widgets a, #footer .footer__widgets .evo_widget ul a{ color: ' . $color . ' }';
            $custom_css .= 'div.widget_core_coll_item_list.evo_noexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_featured_posts.evo_noexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_post_list.evo_noexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_page_list.evo_noexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_related_post_list.evo_noexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_item_list.evo_withexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_featured_posts.evo_withexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_post_list.evo_withexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_page_list.evo_withexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_related_post_list.evo_withexcerpt.evo_withteaser div.item_content > a, div.widget_core_coll_item_list.evo_withexcerpt.evo_noteaser div.item_content > a, div.widget_core_coll_featured_posts.evo_withexcerpt.evo_noteaser div.item_content > a, div.widget_core_coll_post_list.evo_withexcerpt.evo_noteaser div.item_content > a, div.widget_core_coll_page_list.evo_withexcerpt.evo_noteaser div.item_content > a, div.widget_core_coll_related_post_list.evo_withexcerpt.evo_noteaser div.item_content > a, div.widget_core_coll_item_list.evo_noexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_featured_posts.evo_noexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_post_list.evo_noexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_page_list.evo_noexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_related_post_list.evo_noexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_item_list.evo_withexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_featured_posts.evo_withexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_post_list.evo_withexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_page_list.evo_withexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_related_post_list.evo_withexcerpt.evo_withteaser div.item_excerpt > a, div.widget_core_coll_item_list.evo_withexcerpt.evo_noteaser div.item_excerpt > a, div.widget_core_coll_featured_posts.evo_withexcerpt.evo_noteaser div.item_excerpt > a, div.widget_core_coll_post_list.evo_withexcerpt.evo_noteaser div.item_excerpt > a, div.widget_core_coll_page_list.evo_withexcerpt.evo_noteaser div.item_excerpt > a, div.widget_core_coll_related_post_list.evo_withexcerpt.evo_noteaser div.item_excerpt > a { border-color: ' . $color . ' }';
        }

        if ($this->get_setting('footer_bottom_align') == 'center') {
            $custom_css .= '#footer .copyright, .footer__social{ float: none; text-align: center; }';
        }

        /* BACKGROUND CONTENT
         * ========================================================================== */
        $bgc_value = $this->get_setting('bgc_style');
        $bgc_img_value = $this->get_setting('bgc_img');
        $bgc_img_custom_value = $this->get_setting('bgc_img_custom');
        $bgc_color_value = $this->get_setting('bgc_color');
        if ($bgc_value == 'bg_img' && $bgc_img_custom_value != null) {
            $FileCache = &get_FileCache();
            $bg_image_File2 = &$FileCache->get_by_ID($this->get_setting('bgc_img_custom'), false, false);
            $custom_css .= '.main_content .error_404, .disp_login .main_content, .disp_lostpassword .main_content, .disp_register .main_content, .disp_access_requires_login .main_content { background-image: url(' . $bg_image_File2->get_url() . ') }';
        } else {
            $custom_css .= '.main_content .error_404, .disp_login .main_content, .disp_lostpassword .main_content, .disp_register .main_content, .disp_access_requires_login .main_content { background-image: none; background: ' . $bgc_color_value . ' }';
        }

        if ($bgc_value == 'bg_img') {
            $bgc_overlay = $this->get_setting('bgc_img_overlay');
            $bgc_opacity = $this->get_setting('bgc_overlay_opacity');

            $custom_css .= '.main_content .error_404::after { background: ' . $bgc_overlay . ' }';
            $custom_css .= '.main_content .error_404::after { opacity: ' . $bgc_opacity . ' }';
        }

        if ($color = $this->get_setting('bgc_color_content')) {
            $custom_css .= '.main_content .error_404_content p {color: ' . $color . '}';
        }

        /* Output the custom CSS
        * ========================================================================== */
        if (! empty($custom_css)) {
            $custom_css = '
			<style type="text/css">
			' . $custom_css . '
			</style>';
            add_headline($custom_css);

            // Add Favicon
            // $favicon = $this->get_setting( 'favicon' );
            // add_headline( '<link rel="shortcut icon" href="'. $favicon .'"/>' );
        }
    }

    /**
     * Determine to display status banner or to don't display
     *
     * @param string Status of Item or Comment
     * @return boolean TRUE if we can display status banner for given status
     */
    public function enabled_status_banner($status)
    {
        if ($status != 'published') { // Display status banner everytime when status is not 'published'
            return true;
        }
        if (is_logged_in() && $this->get_setting('banner_public')) { // Also display status banner if status is 'published'
            //   AND current user is logged in
            //   AND this feature is enabled in skin settings
            return true;
        }
        // Don't display status banner
        return false;
    }

    public function get_template($name)
    {
        switch ($name) {
            case 'Results':
                // Results list:
                return [
                    'page_url' => '', // All generated links will refer to the current page
                    'before' => '<div class="results panel panel-default">',
                    'content_start' => '<div id="$prefix$ajax_content">',
                    'header_start' => '',
                    'header_text' => '<div class="center"><ul class="pagination">'
                                . '$prev$$first$$list_prev$$list$$list_next$$last$$next$'
                                . '</ul></div>',
                    'header_text_single' => '',
                    'header_end' => '',
                    'head_title' => '<div class="panel-heading fieldset_title"><span class="pull-right">$global_icons$</span><h3 class="panel-title">$title$</h3></div>' . "\n",
                    'global_icons_class' => 'btn btn-default btn-sm',
                    'filters_start' => '<div class="filters panel-body">',
                    'filters_end' => '</div>',
                    'filter_button_class' => 'btn-sm btn-info',
                    'filter_button_before' => '<div class="form-group pull-right">',
                    'filter_button_after' => '</div>',
                    'messages_start' => '<div class="messages form-inline">',
                    'messages_end' => '</div>',
                    'messages_separator' => '<br />',
                    'list_start' => '<div class="table_scroll">' . "\n"
                                            . '<table class="table table-striped table-bordered table-hover table-condensed" cellspacing="0">' . "\n",
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
                    'footer_text' => '<div class="center"><ul class="pagination">'
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
                // $disp=single contact form:
                return [
                    'layout' => 'fieldset',
                    'formclass' => 'form-horizontal',
                    'formstart' => '',
                    'formend' => '',
                    'title_fmt' => '<span style="float:right">$global_icons$</span><h2>$title$</h2>' . "\n",
                    'no_title_fmt' => '<span style="float:right">$global_icons$</span>' . "\n",
                    'fieldset_begin' => '<div class="clear"></div><div class="fieldset_wrapper $class$" id="fieldset_wrapper_$id$"><fieldset $fieldset_attribs$><div class="panel panel-default">' . "\n"
                                        . '<legend class="panel-heading" $title_attribs$>$fieldset_title$</legend><div class="panel-body $class$">' . "\n",
                    'fieldset_end' => '</div></div></fieldset></div>' . "\n",
                    'fieldstart' => '<div class="form-group" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => 'control-label col-sm-3',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label class="control-label col-sm-3"></label>',
                    'inputstart' => '<div class="controls col-sm-9">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class="controls col-sm-9"><div class="form-control-static">',
                    'infoend' => "</div></div>\n",
                    'buttonsstart' => '<div class="form-group"><div class="control-buttons col-sm-offset-3 col-sm-9">',
                    'buttonsend' => "</div></div>\n\n",
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

            case 'linespan_form':
                // Linespan form:
                return [
                    'layout' => 'linespan',
                    'formclass' => 'form-horizontal',
                    'formstart' => '',
                    'formend' => '',
                    'title_fmt' => '<span style="float:right">$global_icons$</span><h2>$title$</h2>' . "\n",
                    'no_title_fmt' => '<span style="float:right">$global_icons$</span>' . "\n",
                    'fieldset_begin' => '<div class="fieldset_wrapper $class$" id="fieldset_wrapper_$id$"><fieldset $fieldset_attribs$><div class="panel panel-default">' . "\n"
                                                            . '<legend class="panel-heading" $title_attribs$>$fieldset_title$</legend><div class="panel-body $class$">' . "\n",
                    'fieldset_end' => '</div></div></fieldset></div>' . "\n",
                    'fieldstart' => '<div class="form-group" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => '',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '',
                    'inputstart' => '<div class="controls">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class="controls"><div class="form-control-static">',
                    'infoend' => "</div></div>\n",
                    'buttonsstart' => '<div class="form-group"><div class="control-buttons">',
                    'buttonsend' => "</div></div>\n\n",
                    'customstart' => '<div class="custom_content">',
                    'customend' => "</div>\n",
                    'note_format' => ' <span class="help-inline">%s</span>',
                    // Additional params depending on field type:
                    // - checkbox
                    'inputclass_checkbox' => '',
                    'inputstart_checkbox' => '<div class="controls"><div class="checkbox"><label>',
                    'inputend_checkbox' => "</label></div></div>\n",
                    'checkbox_newline_start' => '<div class="checkbox">',
                    'checkbox_newline_end' => "</div>\n",
                    'checkbox_basic_start' => '<div class="checkbox"><label>',
                    'checkbox_basic_end' => "</label></div>\n",
                    // - radio
                    'fieldstart_radio' => '',
                    'fieldend_radio' => '',
                    'inputstart_radio' => '<div class="controls">',
                    'inputend_radio' => "</div>\n",
                    'inputclass_radio' => '',
                    'radio_label_format' => '$radio_option_label$',
                    'radio_newline_start' => '<div class="radio"><label>',
                    'radio_newline_end' => "</label></div>\n",
                    'radio_oneline_start' => '<label class="radio-inline">',
                    'radio_oneline_end' => "</label>\n",
                ];

            case 'fixed_form':
                // Form with fixed label width:
                return [
                    'layout' => 'fieldset',
                    'formclass' => 'form-horizontal',
                    'formstart' => '',
                    'formend' => '',
                    'title_fmt' => '<span style="float:right">$global_icons$</span><h2>$title$</h2>' . "\n",
                    'no_title_fmt' => '<span style="float:right">$global_icons$</span>' . "\n",
                    'fieldset_begin' => '<div class="fieldset_wrapper $class$" id="fieldset_wrapper_$id$"><fieldset $fieldset_attribs$><div class="panel panel-default">' . "\n"
                                            . '<legend class="panel-heading" $title_attribs$>$fieldset_title$</legend><div class="panel-body $class$">' . "\n",
                    'fieldset_end' => '</div></div></fieldset></div>' . "\n",
                    'fieldstart' => '<div class="form-group fixedform-group" $ID$>' . "\n",
                    'fieldend' => "</div>\n\n",
                    'labelclass' => 'control-label fixedform-label',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label class="control-label fixedform-label"></label>',
                    'inputstart' => '<div class="controls fixedform-controls">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class="controls fixedform-controls"><div class="form-control-static">',
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
                // The Prev/Next links of users
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
                // Button classes
                return [
                    'button' => 'btn btn-default btn-xs',
                    'button_red' => 'btn-danger',
                    'button_green' => 'btn-success',
                    'text' => 'btn btn-default btn-xs',
                    'group' => 'btn-group',
                ];

            case 'tooltip_plugin':
                // Plugin name for tooltips: 'bubbletip' or 'popover'
                return 'popover';
                break;

            case 'plugin_template':
                // Template for plugins
                return [
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
