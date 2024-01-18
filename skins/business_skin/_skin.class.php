<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage starter_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class business_Skin extends Skin
{
    public $version = '7.3.5';

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
        return 'Business Blog Skin';
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

    /*
     * What CSS framework does has this skin been designed with?
     *
     * This may impact default markup returned by Skin::get_template() for example
     */
    public function get_css_framework()
    {
        return 'bootstrap';
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
            'main' => 'partial',
            'std' => 'yes',		// Blog
            'photo' => 'Yes',
            'forum' => 'no',
            'manual' => 'maybe',
            'group' => 'maybe',  // Tracker
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
            'page_top' => [NT_('Page Top'), 2],
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
        // Load to use function get_available_thumb_sizes()
        load_funcs('files/model/_image.funcs.php');

        $r = array_merge([
            'section_general_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('General Settings'),
            ],
            'layout' => [
                'label' => T_('Layout Settings'),
                'note' => '',
                'type' => 'select',
                'options' => [
                    'single_column' => T_('Single Column Large'),
                    'single_column_normal' => T_('Single Column'),
                    'single_column_narrow' => T_('Single Column Narrow'),
                    'single_column_extra_narrow' => T_('Single Column Extra Narrow'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'defaultvalue' => 'right_sidebar',
            ],
            'max_image_height' => [
                'label' => T_('Max Image Height'),
                'note' => 'px',
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'color_schemes' => [
                'label' => T_('Color Scheme'),
                'note' => T_('Default color scheme is') . ' #1DC6DF.',
                'defaultvalue' => '#1dc6df',
                'type' => 'color',
            ],
            'background_disp' => [
                'label' => T_('Background Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'single_bg' => [
                'label' => T_('Background color for') . ' disp=single ' . T_('and') . ' disp=page',
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'comments_bg' => [
                'label' => T_('Background color for') . ' disp=comments',
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'arcdir_bg' => [
                'label' => T_('Background color for') . ' disp=archive',
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'catdir_bg' => [
                'label' => T_('Background color for') . ' disp=category',
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'postidx_bg' => [
                'label' => T_('Background color for') . ' disp=postidx',
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'tags_bg' => [
                'label' => T_('Background color for') . ' disp=tags',
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'back_to_top' => [
                'label' => T_('Display Back To Top'),
                'note' => T_('Check to display back to top button.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            /*'bgimg_text_color' => array(
                        'label' => T_('Text Color on Background Image'),
                        'note' => T_('E-g: #00ff00 for green'),
                        'defaultvalue' => '#ffffff',
                        'type' => 'color',
                    ),
                    'bgimg_link_color' => array(
                        'label' => T_('Link Color on Background Image'),
                        'note' => T_('E-g: #00ff00 for green'),
                        'defaultvalue' => '#ffffff',
                        'type' => 'color',
                    ),
                    'bgimg_hover_link_color' => array(
                        'label' => T_('Hover Link Color on Background Image'),
                        'note' => T_('E-g: #00ff00 for green'),
                        'defaultvalue' => '#ffffff',
                        'type' => 'color',
                    ),*/
            'section_general_end' => [
                'layout' => 'end_fieldset',
            ],

            /**
             * ============================================================================
             * Section Typograpy
             * ============================================================================
             */
            'section_typograpy_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Typograpy Settings'),
            ],
            'typograpy_fz' => [
                'label' => T_('Font Size'),
                'note' => '',
                'type' => 'radio',
                'options' => [
                    ['small', T_('Small')],
                    ['normal', T_('Normal')],
                    ['large', T_('Large')],
                ],
                'defaultvalue' => 'small',
            ],
            'color_content' => [
                'label' => T_('Page Text Color'),
                'note' => T_('Change page text color here. This field is left blank by default.'),
                'defaultvalue' => '',
                'allow_empty' => true,
                'type' => 'color',
            ],
            'section_typograpy_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Typograpy

            /**
             * ============================================================================
             * Section Header Top Options
             * ============================================================================
             */
            'section_header_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Header Top Settings'),
            ],
            'ht_show' => [
                'label' => T_('Display Header Top'),
                'note' => T_('Check to display special header section.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'ht_contact_info' => [
                'label' => T_('Header Top Section Text'),
                'defaultvalue' => 'Contact Us on 0800 123 4567 or info@example.com',
                'note' => '<br />' . T_('Add your contact info'),
                'type' => 'text',
                'size' => '60',
            ],
            'header_top_color' => [
                'label' => T_('Header Top Section Color'),
                'note' => T_('Default color is') . ' #777777.',
                'defaultvalue' => '#777777',
                'type' => 'color',
            ],
            'header_top_bg' => [
                'label' => T_('Header Top Section Background Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'section_header_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Header Options

            /**
             * ============================================================================
             * Section MainHeader Options
             * ============================================================================
             */
            'section_main_header_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Main Header Settings'),
            ],
            'header_sticky' => [
                'label' => T_('Activate Sticky Main Header'),
                'note' => T_('Check to activate sticky main header'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'site_tite_color' => [
                'label' => T_('Site Title Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'menu_link_color' => [
                'label' => T_('Menu Links Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'main_header_bg' => [
                'label' => T_('Main Header Background Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'section_main_header_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Main Header Options

            /**
             * ============================================================================
             * Options Disp Front
             * ============================================================================
             */
            'section_disp_front_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Front Page Settings'),
            ],
            'layout_front' => [
                'label' => T_('Layout Settings'),
                'note' => '',
                'type' => 'select',
                'options' => [
                    'single_column' => T_('Single Column Large'),
                    'single_column_normal' => T_('Single Column'),
                    'single_column_narrow' => T_('Single Column Narrow'),
                    'single_column_extra_narrow' => T_('Single Column Extra Narrow'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'defaultvalue' => 'single_column',
            ],
            'front_bg' => [
                'label' => T_('Background color for') . ' disp=front',
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'section_disp_front_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Typograpy

            /**
             * ============================================================================
             * Options Disp Posts
             * ============================================================================
             */
            'section_disp_post_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Posts Page Settings'),
            ],
            'layout_posts' => [
                'label' => T_('Posts Page Layout'),
                'note' => '',
                'type' => 'select',
                'options' => [
                    'regular' => T_('Regular'),
                    'mini_blog' => T_('Mini Blog Layout'),
                    'masonry' => T_('Masonry Layout'),
                ],
                'defaultvalue' => 'regular',
            ],
            'posts_masonry_column' => [
                'label' => T_('Masonry Layout Columns'),
                'note' => T_('Select the number of column for Masonry layout.'),
                'type' => 'select',
                'options' => [
                    'two_columns' => '2 ' . T_('Columns'),
                    'three_columns' => '3 ' . T_('Columns'),
                    'four_columns' => '4 ' . T_('Columns'),
                ],
                'defaultvalue' => 'three_column',
            ],
            'regular_post_bg' => [
                'label' => T_('Background Color for Regular Layout'),
                'note' => T_('Default background color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'mini_blog_bg' => [
                'label' => T_('Background Color for Mini Blog'),
                'note' => T_('Default background color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'post_info_color' => [
                'label' => T_('Post Info Content Color'),
                'note' => T_('Default color is') . ' #999999.',
                'defaultvalue' => '#999999',
                'type' => 'color',
            ],
            'post_info_link' => [
                'label' => T_('Post Info Link Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            // 'pagination_top_show' => array(
            // 	'label'        => T_('Show Pagination Top'),
            // 	'note'         => T_('Check to display Pagination top'),
            // 	'defaultvalue' => 0,
            // 	'type'         => 'checkbox',
            // ),
            'pagination_bottom_show' => [
                'label' => T_('Show Bottom Pagination'),
                'note' => T_('Check to display Bottom Pagination.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'pagination_align' => [
                'label' => T_('Pagination Alignment'),
                'note' => T_('Select left, right or centered alignment for the pagination.'),
                'defaultvalue' => 'center',
                'type' => 'select',
                'options' => [
                    'left' => T_('Left'),
                    'center' => T_('Center'),
                    'right' => T_('Right'),
                ],
            ],
            'section_disp_post_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Options Disp Posts

            /**
             * ============================================================================
             * Tags Layout
             * ============================================================================
             */
            'section_tags_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Tags Page Settings'),
            ],
            'tags_color' => [
                'label' => T_('Post Tags Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'tags_bg_color' => [
                'label' => T_('Post Tags Background Color'),
                'note' => T_('Default color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'tags_bdr_color' => [
                'label' => T_('Post Tags Border Color'),
                'note' => T_('Default color is') . ' #E4E4E4.',
                'defaultvalue' => '#E4E4E4',
                'type' => 'color',
            ],
            'section_tags_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Single Disp

            /**
             * ============================================================================
             * Disp Single and Page Options
             * ============================================================================
             */
            // 'section_single_start' => array(
            //    'layout' => 'begin_fieldset',
            //    'label'  => T_('Disp Single and Page Options')
            // ),
            //
            // 'section_single_end' => array(
            //    'layout' => 'end_fieldset',
            // ),
            // End Single Disp

            /**
             * ============================================================================
             * Sidebar Widget Options
             * ============================================================================
             */
            'section_sidebar_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Sidebar Settings (All pages)'),
            ],
            'sidebar_title_widget' => [
                'label' => T_('Widget Title Color'),
                'note' => T_('Default color is') . ' #000000.',
                'defaultvalue' => '#000000',
                'type' => 'color',
            ],
            'sidebar_color_content' => [
                'label' => T_('Widget Content Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'sidebar_color_link' => [
                'label' => T_('Widget Link Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'sidebar_border_widget' => [
                'label' => T_('Widget Border Color'),
                'note' => T_('Default color is') . ' #EEEEEE.',
                'defaultvalue' => '#EEEEEE',
                'type' => 'color',
            ],
            'section_sidebar_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Sidebar Widget Options

            /**
             * ============================================================================
             * Footer Options
             * ============================================================================
             */
            'section_footer_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Footer Settings'),
            ],
            'footer_dispay' => [
                'label' => T_('Display Footer Widget'),
                'note' => T_('Check to display footer widget area with 4 columns.'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
            ],
            'footer_title_color' => [
                'label' => T_('Footer Widgets Title Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'footer_text_content' => [
                'label' => T_('Footer Content Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'footer_link_color' => [
                'label' => T_('Footer Links Color'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'footer_border_widget' => [
                'label' => T_('Footer Widgets Border Color'),
                'note' => T_('Default color is') . ' #333333.',
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'copyright_color' => [
                'label' => T_('Copyright Content Color'),
                'note' => T_('Default color is') . ' #999999.',
                'defaultvalue' => '#999999',
                'type' => 'color',
            ],
            'footer_bg' => [
                'label' => T_('Footer Background Color'),
                'note' => T_('Default color is') . ' #222222.',
                'defaultvalue' => '#222222',
                'type' => 'color',
            ],
            'section_footer_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Section Footer Top

            /**
             * ============================================================================
             * Photo Index Options
             * ============================================================================
             */
            'section_mediaidx_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Media Post Settings') . ' (disp=mediaidx)',
            ],
            'mediaidx_layout' => [
                'label' => T_('Layout settings'),
                'note' => '',
                'type' => 'select',
                'options' => [
                    'no_sidebar' => T_('No Sidebar'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'defaultvalue' => 'no_sidebar',
            ],
            'mediaidx_thumb_size' => [
                'label' => T_('Thumbnail Size for Media Index'),
                'note' => '',
                'defaultvalue' => 'fit-1280x720',
                'options' => get_available_thumb_sizes(),
                'type' => 'select',
            ],
            'mediaidx_grid' => [
                'label' => T_('Column Count'),
                'note' => '',
                'defaultvalue' => 'two_column',
                'type' => 'select',
                'options' => [
                    'one_column' => '1 ' . T_('Column'),
                    'two_column' => '2 ' . T_('Columns'),
                    'three_column' => '3 ' . T_('Columns'),
                ],
            ],
            'mediaidx_style' => [
                'label' => T_('Media Items Layout'),
                'note' => '',
                'defaultvalue' => 'default',
                'type' => 'select',
                'options' => [
                    'default' => T_('Default'),
                    'box' => T_('Box Layout'),
                ],
            ],
            'padding_column' => [
                'label' => T_('Image Padding'),
                'note' => 'px.' . T_('efault padding is') . ' 15px',
                'defaultvalue' => '15',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'mediaidx_title' => [
                'label' => T_('Display Image Title'),
                'note' => T_('Check to display title of the image.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'mediaidx_title_style' => [
                'label' => T_('Title Style'),
                'note' => T_('Select the title style for Photo Index Page Items.'),
                'defaultvalue' => 'default',
                'type' => 'select',
                'options' => [
                    'default' => T_('Default'),
                    'hover' => T_('Hover Style'),
                ],
            ],
            'mediaidx_bg' => [
                'label' => T_('Background Color for') . ' disp=mediaidx',
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'mediaidx_bg_content' => [
                'label' => T_('Background Color for Media Index Page Content'),
                'note' => T_('Default color is') . ' #FFFFFF.',
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            'mediaidx_title_color' => [
                'label' => T_('Media Index Page Title Color'),
                'note' => T_('Default color is') . ' #222222.' . T_(' Activated when you use box style and display image title.'),
                'defaultvalue' => '#222222',
                'type' => 'color',
            ],
            'section_mediaidx_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Photo Index Disp

            /**
             * ============================================================================
             * Search Disp
             * ============================================================================
             */
            'section_search_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Search Page Settings') . ' (disp=search)',
            ],
            'search_title' => [
                'label' => T_('Search Box Title'),
                'defaultvalue' => T_('Search Results'),
                'note' => T_('Change the title of the Search Box.'),
                'type' => 'text',
                'size' => '30',
            ],
            'search_button_text' => [
                'label' => T_('Button Text'),
                'defaultvalue' => T_('Search'),
                'note' => T_('Change the text of the search button.'),
                'type' => 'text',
                'size' => '20',
            ],
            'search_field' => [
                'label' => T_('Show Search Field'),
                'note' => T_('Check to display search field.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'search_text_info' => [
                'label' => T_('Search Info Text Color'),
                'note' => T_('Default color is') . ' #999999.',
                'defaultvalue' => '#999999',
                'type' => 'color',
            ],
            'search_bg' => [
                'label' => T_('Background Color'),
                'note' => T_('Default background color is') . ' #F7F7F7.',
                'defaultvalue' => '#F7F7F7',
                'type' => 'color',
            ],
            'section_search_end' => [
                'layout' => 'end_fieldset',
            ],
            // End Search Disp

            /**
             * ============================================================================
             * Color Box
             * ============================================================================
             */
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
            // End Color Box

            /**
             * ============================================================================
             * Username Option
             * ============================================================================
             */
            'section_username_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Username Settings'),
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
            // End Username Options

            'section_access_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('When access is denied or requires login... (disp=access_denied and disp=access_requires_login)'),
            ],
            'access_login_containers' => [
                'label' => T_('Display on login screen'),
                'note' => '',
                'type' => 'checklist',
                'options' => [
                    ['header',   sprintf(T_('"%s" container'), NT_('Header')),   1],
                    ['sidebar',  sprintf(T_('"%s" container'), NT_('Sidebar')),  0],
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

        //Include script and styles for Sticky Menu
        require_js('assets/js/jquery.sticky.js', 'relative');

        if ($disp == 'mediadix' || $this->get_setting('layout_posts') == 'masonry') {
            require_js('assets/js/masonry.pkgd.min.js', 'relative');
            require_js('assets/js/imagesloaded.pkgd.min.js', 'relative');
        }

        // Include Masonry Grind for MediaIdx
        if ($disp == 'mediaidx') {
            add_js_headline("
				jQuery( document ).ready( function($) {
					$('.evo_image_index').imagesLoaded().done( function( instance ) {
						$('.evo_image_index').masonry({
							// options
							itemSelector: '.grid-item',
						});
					});
				});
			");
        }

        require_js('assets/js/scripts.js', 'relative');

        // Skin specific initializations:
        // Add Custome CSS
        $custom_css = '';

        /**
         * ============================================================================
         * This is Title
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('background_disp')) {
            $custom_css .= '
			#skin_wrapper
			{ background-color: ' . $bg_color . ' }
			';
        }

        /**
         * ============================================================================
         * General Settings
         * ============================================================================
         */
        if ($color = $this->get_setting('color_schemes')) {
            // Disp Posts
            $custom_css .= '
			a, a:hover, a:focus, a:active,
			#main-header .primary-nav .nav a:hover, #main-header .primary-nav .nav a:active, #main-header .primary-nav .nav a:focus,
			#main-header .primary-nav .nav > li.active a,
			#main-content .evo_post_title h1 a:hover, #mini-blog .evo_post_title h1 a:hover, #main-content .evo_post_title h2 a:hover, #mini-blog .evo_post_title h2 a:hover, #main-content .evo_post_title h3 a:hover, #mini-blog .evo_post_title h3 a:hover, #main-content .evo_post_title h1 a:active, #mini-blog .evo_post_title h1 a:active, #main-content .evo_post_title h2 a:active, #mini-blog .evo_post_title h2 a:active, #main-content .evo_post_title h3 a:active, #mini-blog .evo_post_title h3 a:active, #main-content .evo_post_title h1 a:focus, #mini-blog .evo_post_title h1 a:focus, #main-content .evo_post_title h2 a:focus, #mini-blog .evo_post_title h2 a:focus, #main-content .evo_post_title h3 a:focus, #mini-blog .evo_post_title h3 a:focus,
			#main-content .evo_post .small.text-muted a:hover, #mini-blog .evo_post .small.text-muted a:hover, #main-content .evo_post .small.text-muted a .glyphicon:hover, #mini-blog .evo_post .small.text-muted a .glyphicon:hover,
			.pagination li a,

			#main-sidebar .evo_widget a:hover, #main-sidebar .evo_widget a:active, #main-sidebar .evo_widget a:focus,
			#main-sidebar .widget_plugin_evo_Calr .bCalendarTable td a,

			#main-footer .widget_footer .evo_widget a:hover, #main-footer .widget_footer .evo_widget a:active, #main-footer .widget_footer .evo_widget a:focus,
			#main-footer .widget_footer .widget_plugin_evo_Calr .bCalendarTable tbody a, #main-footer .widget_footer .widget_plugin_evo_Calr tfoot a,

			.disp_catdir #main-content .widget_core_coll_category_list a:hover, .disp_catdir #main-content .widget_core_coll_category_list a:active, .disp_catdir #main-content .widget_core_coll_category_list a:focus,
			.disp_arcdir #main-content .widget_plugin_achive a:hover, .disp_arcdir #main-content .widget_plugin_achive a:focus, .disp_arcdir #main-content .widget_plugin_achive a:active,
			.disp_postidx #main-content .widget_core_coll_post_list a:hover, .disp_postidx #main-content .widget_core_coll_post_list a:focus, .disp_postidx #main-content .widget_core_coll_post_list a:active,

			.disp_sitemap .content_sitemap .evo_widget a:hover, .disp_sitemap .content_sitemap .evo_widget a:active, .disp_sitemap .content_sitemap .evo_widget a:focus,

			.disp_posts .evo_featured_post header .small.text-muted a:hover, .disp_posts .evo_featured_post header .small.text-muted a:active, .disp_posts .evo_featured_post header .small.text-muted a:focus,
			.widget_core_coll_comment_list ul li:hover a::after
			{ color: ' . $color . ' }

			#main-header .primary-nav .nav a::after,
			.disp_posts .evo_intro_post,
			.disp_posts #main-content .posts_date,
			.disp_posts #main-content .timeline,
			#main-content .evo_post .evo_image_block a::before, #mini-blog .evo_post .evo_image_block a::before, #main-content .evo_post .evo_post_gallery__image a::before, #mini-blog .evo_post .evo_post_gallery__image a::before,
			#main-content .evo_post .evo_post__full_text .evo_post_more_link a:hover, #mini-blog .evo_post .evo_post__full_text .evo_post_more_link a:hover, #main-content .evo_post .evo_post__excerpt_text .evo_post_more_link a:hover, #mini-blog .evo_post .evo_post__excerpt_text .evo_post_more_link a:hover,
			.pagination .active span, .pagination .active span:hover,
			.pagination li a:hover, .pagination li span:hover, .pagination li a:focus, .pagination li span:focus,
			#main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, #main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, #main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus,

			#main-sidebar .widget_core_coll_search_form .compact_search_form .search_submit,
			#main-sidebar .widget_core_coll_media_index .widget_flow_blocks > div a::before,
			#main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:hover, #main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:active, #main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:focus,
			#main-sidebar .widget_plugin_evo_Calr .bCalendarTable #bCalendarToday,

			#main-footer .widget_footer .widget_core_coll_tag_cloud .tag_cloud a:hover, #main-footer .widget_footer .widget_core_coll_tag_cloud .tag_cloud a:active, #main-footer .widget_footer .widget_core_coll_tag_cloud .tag_cloud a:focus,
			#main-footer .widget_footer .widget_plugin_evo_Calr .bCalendarTable #bCalendarToday,
			.widget_core_coll_search_form .compact_search_form .search_submit,

			.widget_core_coll_media_index .widget_flow_blocks > div a::before,

			.close-menu, .cd-top,

			.disp_tags #main-content .tag_cloud a:hover, .disp_tags #main-content .tag_cloud a:active, .disp_tags #main-content .tag_cloud a:focus,
			.disp_sitemap .content_sitemap .title_widgets::after,

			.posts_mini_layout #mini-blog .msg_nothing,

			.disp_front #main-content .widget_core_coll_featured_intro .jumbotron,
			.disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, .disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, .disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus,

			#main-content .post_tags a:hover, #mini-blog .post_tags a:hover, #main-content .post_tags a:active, #mini-blog .post_tags a:active, #main-content .post_tags a:focus, #mini-blog .post_tags a:focus,

			.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index li figure.box.title.title_overlay .note,
			.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index .title_overlay .note,

			.disp_posts .evo_featured_post,
			.posts_mini_layout #mini-blog .evo_featured_post .post_tags a:hover,
			.posts_mini_layout #mini-blog .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover,

			.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover, .evo_form__thread .control-buttons .btn-primary,

			.skin-form .panel-heading, .evo_panel__login .btn.btn-success, .evo_panel__lostpass .btn.btn-success, .evo_panel__register .btn.btn-success, .evo_panel__activation .btn.btn-success,
			.evo_form .submit, .detail_threads .results .panel-heading, .detail_messages .evo_private_messages_list .panel .panel-heading, .detail_messages .evo_private_messages_list .panel .SaveButton.btn-primary, .detail_contacts .results .panel-heading, .detail_contacts .form_send_contacts .btn-default:hover, .detail_contacts .form_send_contacts .btn-default:active, .detail_contacts .form_send_contacts .btn-default:focus, .detail_contacts .form_add_contacts .SaveButton
			{ background-color: ' . $color . '; }

			.pagination .active span, .pagination .active span:hover,
			.pagination li a:hover, .pagination li span:hover, .pagination li a:focus, .pagination li span:focus,
			.posts_mini_layout #mini-blog .pagination li a, .posts_mini_layout #mini-blog .pagination li span,
			#main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, #main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, #main-content .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus, #mini-blog .evo_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus,

			#main-sidebar .widget_core_coll_search_form .compact_search_form .search_submit,
			#main-sidebar .widget_core_coll_search_form .compact_search_form .search_field,
			#main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:hover, #main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:active, #main-sidebar .widget_core_coll_tag_cloud .tag_cloud a:focus,

			#main-footer .widget_footer .widget_plugin_evo_Calr .bCalendarTable #bCalendarToday,
			.widget_core_coll_search_form .compact_search_form .search_submit,
			#main-sidebar input[type="email"]:focus, #main-sidebar input[type="number"]:focus, #main-sidebar input[type="password"]:focus, #main-sidebar input[type="tel"]:focus, #main-sidebar input[type="url"]:focus, #main-sidebar input[type="text"]:focus,

			.disp_tags #main-content .tag_cloud a:hover, .disp_tags #main-content .tag_cloud a:active, .disp_tags #main-content .tag_cloud a:focus,

			.disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:hover, .disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:active, .disp_posts .evo_featured_post .evo_post__excerpt_text .evo_post__excerpt_more_link a:focus,

			#main-content .post_tags a:hover, #mini-blog .post_tags a:hover, #main-content .post_tags a:active, #mini-blog .post_tags a:active, #main-content .post_tags a:focus, #mini-blog .post_tags a:focus,

			.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover,
			.skin-form, .evo_panel__login .form_text_input, .evo_panel__lostpass .form_text_input, .evo_panel__register .form_text_input, .evo_panel__activation .form_text_input, .evo_panel__login .btn.btn-success, .evo_panel__lostpass .btn.btn-success, .evo_panel__register .btn.btn-success, .evo_panel__activation .btn.btn-success,
			#login_form input:focus:invalid:focus, #login_form select:focus:invalid:focus, #login_form textarea:focus:invalid:focus,
			.evo_panel__login .form_text_input:focus, .evo_panel__lostpass .form_text_input:focus, .evo_panel__register .form_text_input:focus, .evo_panel__activation .form_text_input:focus,
			.evo_form__thread .control-buttons .btn-primary, .evo_form__thread:hover,

			.evo_form .form_text_input:hover, .evo_form .form_textarea_input:hover, .evo_form .form_text_input:active, .evo_form .form_textarea_input:active, .evo_form .form_text_input:focus, .evo_form .form_textarea_input:focus,
			.evo_form .submit,
			.evo_form__thread .form-control:hover, .evo_form__thread .token-input-list-facebook:hover, .evo_form__thread .form-control:focus, .evo_form__thread .token-input-list-facebook:focus, .evo_form__thread .form-control:active, .evo_form__thread .token-input-list-facebook:active, .detail_threads .results,
			.detail_threads .results .form_text_input:hover, .detail_threads .results .form_text_input:active, .detail_threads .results .form_text_input:focus, .detail_messages .evo_private_messages_list .panel, .detail_messages .evo_private_messages_list .panel .SaveButton.btn-primary, .detail_messages .evo_private_messages_list .results .form_text_input:focus, .detail_messages .evo_private_messages_list .results .form_text_input:hover, .detail_messages .evo_private_messages_list .results .form_text_input:active, .detail_contacts .results, .detail_contacts .results .form_text_input:hover, .detail_contacts .results .form_text_input:active, .detail_contacts .results .form_text_input:focus, .detail_contacts .form_send_contacts .btn-default, .detail_contacts .form_add_contacts .input-sm, .detail_contacts .form_add_contacts .SaveButton
			{ border-color: ' . $color . '; }

			.page_title,
			.disp_sitemap .content_sitemap .title_widgets
			{ border-bottom-color: ' . $color . '; }
			';

            // Disp Single
            $custom_css .= '
			.disp_single #main-content .pager .previous a:hover, .disp_page #main-content .pager .previous a:hover, .disp_single #main-content .pager .next a:hover, .disp_page #main-content .pager .next a:hover, .disp_single #main-content .pager .previous a:active, .disp_page #main-content .pager .previous a:active, .disp_single #main-content .pager .next a:active, .disp_page #main-content .pager .next a:active, .disp_single #main-content .pager .previous a:focus, .disp_page #main-content .pager .previous a:focus, .disp_single #main-content .pager .next a:focus, .disp_page #main-content .pager .next a:focus,
			.disp_single #main-content .evo_post .single_tags a:hover, .disp_page #main-content .evo_post .single_tags a:hover, .disp_single #main-content .evo_post .single_tags a:active, .disp_page #main-content .evo_post .single_tags a:active, .disp_single #main-content .evo_post .single_tags a:focus, .disp_page #main-content .evo_post .single_tags a:focus,
			.disp_single #main-content .evo_post #feedbacks .evo_comment .panel-heading .evo_comment_title a, .disp_page #main-content .evo_post #feedbacks .evo_comment .panel-heading .evo_comment_title a,
			.disp_single #main-content .evo_post .evo_post_comment_notification a:hover, .disp_page #main-content .evo_post .evo_post_comment_notification a:hover, .disp_single #main-content .evo_post .evo_post_comment_notification a:active, .disp_page #main-content .evo_post .evo_post_comment_notification a:active, .disp_single #main-content .evo_post .evo_post_comment_notification a:focus, .disp_page #main-content .evo_post .evo_post_comment_notification a:focus,
			.disp_single #main-content .evo_post .evo_comment .panel-heading .evo_comment_title a, .disp_page #main-content .evo_post .evo_comment .panel-heading .evo_comment_title a, .disp_single #main-content .evo_post .evo_comment__preview .panel-heading .evo_comment_title a, .disp_page #main-content .evo_post .evo_comment__preview .panel-heading .evo_comment_title a
			{ color: ' . $color . '; }

			.disp_single #main-content .evo_post .evo_image_block a::before, .disp_page #main-content .evo_post .evo_image_block a::before, .disp_single #main-content .evo_post .evo_post_gallery__image a::before, .disp_page #main-content .evo_post .evo_post_gallery__image a::before,
			.disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:hover, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:hover, .disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:focus, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:focus, .disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:active, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:active,
			.disp_single #main-content .evo_post .evo_form .submit, .disp_page #main-content .evo_post .evo_form .submit,
			.disp_single #main-content .evo_post .evo_form .submit:hover, .disp_page #main-content .evo_post .evo_form .submit:hover, .disp_single #main-content .evo_post .evo_form .submit:focus, .disp_page #main-content .evo_post .evo_form .submit:focus, .disp_single #main-content .evo_post .evo_form .submit:active, .disp_page #main-content .evo_post .evo_form .submit:active,
			.disp_single #main-content .evo_post #feedbacks .evo_comment .evo_comment_footer .permalink_right, .disp_page #main-content .evo_post #feedbacks .evo_comment .evo_comment_footer .permalink_right,
			.disp_single #main-content .evo_post .evo_post_comment_notification a.btn:hover, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:hover, .disp_single #main-content .evo_post .evo_post_comment_notification a.btn:active, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:active, .disp_single #main-content .evo_post .evo_post_comment_notification a.btn:focus, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:focus, .profile_content .panel .panel-heading,

			.disp_profile .main_content .profile_tabs li.active a, .disp_avatar .main_content .profile_tabs li.active a, .disp_pwdchange .main_content .profile_tabs li.active a, .disp_userprefs .main_content .profile_tabs li.active a, .disp_subs .main_content .profile_tabs li.active a,
			.disp_profile .main_content .profile_tabs li a:hover, .disp_avatar .main_content .profile_tabs li a:hover, .disp_pwdchange .main_content .profile_tabs li a:hover, .disp_userprefs .main_content .profile_tabs li a:hover, .disp_subs .main_content .profile_tabs li a:hover, .disp_profile .main_content .profile_tabs li a:active, .disp_avatar .main_content .profile_tabs li a:active, .disp_pwdchange .main_content .profile_tabs li a:active, .disp_userprefs .main_content .profile_tabs li a:active, .disp_subs .main_content .profile_tabs li a:active, .disp_profile .main_content .profile_tabs li a:focus, .disp_avatar .main_content .profile_tabs li a:focus, .disp_pwdchange .main_content .profile_tabs li a:focus, .disp_userprefs .main_content .profile_tabs li a:focus, .disp_subs .main_content .profile_tabs li a:focus,
			.disp_profile .main_content .panel-heading, .disp_avatar .main_content .panel-heading, .disp_pwdchange .main_content .panel-heading, .disp_userprefs .main_content .panel-heading, .disp_subs .main_content .panel-heading, .disp_profile .main_content .btn-primary, .disp_avatar .main_content .btn-primary, .disp_pwdchange .main_content .btn-primary, .disp_userprefs .main_content .btn-primary, .disp_subs .main_content .btn-primary, .disp_profile .main_content #ffield_edited_user_email .help-inline .btn, .disp_avatar .main_content #ffield_edited_user_email .help-inline .btn, .disp_pwdchange .main_content #ffield_edited_user_email .help-inline .btn, .disp_userprefs .main_content #ffield_edited_user_email .help-inline .btn, .disp_subs .main_content #ffield_edited_user_email .help-inline .btn
			{ background-color: ' . $color . '; }

			.disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:hover, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:hover, .disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:focus, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:focus, .disp_single #main-content .evo_post #feedbacks .evo_comment__meta_info a:active, .disp_page #main-content .evo_post #feedbacks .evo_comment__meta_info a:active,
			.disp_single #main-content .evo_post .evo_form .submit, .disp_page #main-content .evo_post .evo_form .submit,
			.disp_single #main-content .evo_post .evo_form .submit:hover, .disp_page #main-content .evo_post .evo_form .submit:hover, .disp_single #main-content .evo_post .evo_form .submit:focus, .disp_page #main-content .evo_post .evo_form .submit:focus, .disp_single #main-content .evo_post .evo_form .submit:active, .disp_page #main-content .evo_post .evo_form .submit:active,
			.disp_single #main-content .evo_post .evo_form .form_textarea_input:hover, .disp_page #main-content .evo_post .evo_form .form_textarea_input:hover, .disp_single #main-content .evo_post .evo_form .form_textarea_input:focus, .disp_page #main-content .evo_post .evo_form .form_textarea_input:focus, .disp_single #main-content .evo_post .evo_form .form_textarea_input:active, .disp_page #main-content .evo_post .evo_form .form_textarea_input:active,
			.disp_single #main-content .evo_post .evo_post_comment_notification a.btn:hover, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:hover, .disp_single #main-content .evo_post .evo_post_comment_notification a.btn:active, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:active, .disp_single #main-content .evo_post .evo_post_comment_notification a.btn:focus, .disp_page #main-content .evo_post .evo_post_comment_notification a.btn:focus,

			.disp_single #main-content .evo_post .evo_form .form_text_input:hover, .disp_page #main-content .evo_post .evo_form .form_text_input:hover, .disp_single #main-content .evo_post .evo_form .form_textarea_input:hover, .disp_page #main-content .evo_post .evo_form .form_textarea_input:hover, .disp_single #main-content .evo_post .evo_form .form_text_input:focus, .disp_page #main-content .evo_post .evo_form .form_text_input:focus, .disp_single #main-content .evo_post .evo_form .form_textarea_input:focus, .disp_page #main-content .evo_post .evo_form .form_textarea_input:focus, .disp_single #main-content .evo_post .evo_form .form_text_input:active, .disp_page #main-content .evo_post .evo_form .form_text_input:active, .disp_single #main-content .evo_post .evo_form .form_textarea_input:active, .disp_page #main-content .evo_post .evo_form .form_textarea_input:active, .profile_content .panel,

			.disp_profile .main_content .profile_tabs li.active a, .disp_avatar .main_content .profile_tabs li.active a, .disp_pwdchange .main_content .profile_tabs li.active a, .disp_userprefs .main_content .profile_tabs li.active a, .disp_subs .main_content .profile_tabs li.active a,
			.disp_profile .main_content .profile_tabs li a:hover, .disp_avatar .main_content .profile_tabs li a:hover, .disp_pwdchange .main_content .profile_tabs li a:hover, .disp_userprefs .main_content .profile_tabs li a:hover, .disp_subs .main_content .profile_tabs li a:hover, .disp_profile .main_content .profile_tabs li a:active, .disp_avatar .main_content .profile_tabs li a:active, .disp_pwdchange .main_content .profile_tabs li a:active, .disp_userprefs .main_content .profile_tabs li a:active, .disp_subs .main_content .profile_tabs li a:active, .disp_profile .main_content .profile_tabs li a:focus, .disp_avatar .main_content .profile_tabs li a:focus, .disp_pwdchange .main_content .profile_tabs li a:focus, .disp_userprefs .main_content .profile_tabs li a:focus, .disp_subs .main_content .profile_tabs li a:focus,
			.disp_profile .main_content .panel, .disp_avatar .main_content .panel, .disp_pwdchange .main_content .panel, .disp_userprefs .main_content .panel, .disp_subs .main_content .panel, .disp_profile .main_content .btn-primary, .disp_avatar .main_content .btn-primary, .disp_pwdchange .main_content .btn-primary, .disp_userprefs .main_content .btn-primary, .disp_subs .main_content .btn-primary, .disp_profile .main_content #ffield_edited_user_email .help-inline .btn, .disp_avatar .main_content #ffield_edited_user_email .help-inline .btn, .disp_pwdchange .main_content #ffield_edited_user_email .help-inline .btn, .disp_userprefs .main_content #ffield_edited_user_email .help-inline .btn, .disp_subs .main_content #ffield_edited_user_email .help-inline .btn
			{ border-color: ' . $color . '; }

			.disp_single #main-content .evo_post > header .cat-links a, .disp_page #main-content .evo_post > header .cat-links a,
			.disp_single #main-content .evo_post .single_tags a, .disp_page #main-content .evo_post .single_tags a,

			.disp_mediaidx #main-mediaidx .title_mediaidx,
			.disp_profile .main_content .profile_tabs, .disp_avatar .main_content .profile_tabs, .disp_pwdchange .main_content .profile_tabs, .disp_userprefs .main_content .profile_tabs, .disp_subs .main_content .profile_tabs
			{ border-bottom-color: ' . $color . '; }

			#main-content .evo_post .evo_post__full_text blockquote, .disp_page #main-content .evo_post .evo_post__full_text blockquote, blockquote
			{ border-left-color: ' . $color . '; }
			';

            // Disp Search
            $custom_css .= '
			.disp_search .search_result .search_content_wrap .search_title a:hover, .disp_search .search_result .search_content_wrap .search_title a:active, .disp_search .search_result .search_content_wrap .search_title a:focus,
			.disp_search .search_result .search_content_wrap .search_info a:hover, .disp_search .search_result .search_content_wrap .search_info a:active, .disp_search .search_result .search_content_wrap .search_info a:focus
			{ color: ' . $color . '; }

			.disp_search .search-box .widget_core_coll_search_form .compact_search_form .search_submit,
			.disp_search .search_result .search_result_score.dimmed,
			.disp_search .widget_core_coll_search_form .compact_search_form .search_submit,
			.disp_search .search_result .search_result_score.dimmed
			{ background-color: ' . $color . '; }

			.disp_search .search-box .widget_core_coll_search_form .compact_search_form .search_field, .disp_search .search-box .widget_core_coll_search_form .compact_search_form .search_submit,
			.disp_search .search-box .widget_core_coll_search_form .compact_search_form .search_field:focus, .disp_search .search-box .widget_core_coll_search_form .compact_search_form .search_field:active,
			.disp_search .widget_core_coll_search_form .compact_search_form .search_field, .disp_search .widget_core_coll_search_form .compact_search_form .search_submit,
			.disp_search .widget_core_coll_search_form .compact_search_form .search_field:focus, .disp_search .widget_core_coll_search_form .compact_search_form .search_field:active
			{ border-color: ' . $color . '; }

			.disp_search .search_result .search_content_wrap .search_info a
			{ border-bottom-color: ' . $color . '; }
			';

            // Disp Front
            $custom_css .= '
			.disp_front .evo_container__front_page_main_area .evo_widget a:hover, .disp_front .evo_container__front_page_main_area .evo_widget a:active, .disp_front .evo_container__front_page_main_area .evo_widget a:focus,
			.disp_front .evo_container__front_page_main_area .evo_widget.widget_plugin_evo_Calr .bCalendarTable td a
			{ color: ' . $color . '; }

			.disp_front .evo_container__front_page_main_area .evo_widget h3::before,
			.disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:hover, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:active, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:focus,
			.disp_front .evo_container__front_page_main_area .evo_widget.widget_plugin_evo_Calr .bCalendarTable #bCalendarToday
			{ background-color: ' . $color . '; }

			.evo_container__front_page_main_area .widget_core_coll_search_form .compact_search_form .search_field,
			.disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:hover, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:active, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_tag_cloud .tag_cloud a:focus,
			.disp_front .evo_container__front_page_main_area .evo_widget.widget_core_user_login .input_text:focus, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_user_register .input_text:focus
			{ border-color: ' . $color . '; }
			';

            // Disp Comments
            $custom_css .= '
			.disp_comments .page_title
			{ border-bottom-color: ' . $color . '; }

			.disp_comments .evo_comment .panel-heading .evo_comment_title a, .disp_comments .evo_comment .panel-heading .panel-title a,
			.disp_comments .evo_comment a
			{ color: ' . $color . '; }

			.disp_comments .evo_comment .evo_comment_info a:hover
			{ background-color: ' . $color . '; }
			';
        }

        /**
         * ============================================================================
         * Color Content
         * ============================================================================
         */
        if ($color = $this->get_setting('color_content')) {
            $custom_css .= '
			#main-content .evo_post_title h1 a, #mini-blog .evo_post_title h1 a, #main-content .evo_post_title h2 a, #mini-blog .evo_post_title h2 a, #main-content .evo_post_title h3 a, #mini-blog .evo_post_title h3 a,
			#main-content .evo_post_title h3 a,
			.evo_post__full_text, .evo_post__full_text a, .evo_post__excerpt_text, .evo_post__excerpt_text a,
			.disp_single #main-content .evo_post #feedbacks .evo_comment .evo_comment_text, .disp_page #main-content .evo_post #feedbacks .evo_comment,
			#main-content .post_tags h3, #mini-blog .post_tags h3, .evo_comment_text, .disp_posts .evo_intro_post a,
			.disp_comments .evo_comment .evo_comment_text,
			.disp_catdir #main-content .widget_core_coll_category_list a,
			.disp_front .evo_container__front_page_main_area .evo_widget a,
			.disp_arcdir #main-content .widget_plugin_achive a,

			.evo_post__excerpt_text .evo_post__excerpt_more_link a,
			.disp_comments .evo_comment .panel-heading .evo_comment_title, .disp_comments .evo_comment .panel-heading .panel-title,
			.disp_postidx #main-content .widget_core_coll_post_list a,
			.disp_sitemap .content_sitemap .evo_widget a,

			#main-content .evo_post .evo_post__full_text blockquote, #mini-blog .evo_post .evo_post__full_text blockquote,
			#main-content .evo_post .evo_post__full_text pre, #mini-blog .evo_post .evo_post__full_text pre,
			.disp_single #main-content .evo_post .panel .panel-heading .panel-title, .disp_page #main-content .evo_post .panel .panel-heading .panel-title,
			.evo_comment_text pre, blockquote, .disp_single #main-content .evo_post #feedbacks .evo_comment .evo_comment_footer small, .disp_page #main-content .evo_post #feedbacks .evo_comment .evo_comment_footer small,

			.disp_single #main-content .pager .previous a, .disp_page #main-content .pager .previous a, .disp_single #main-content .pager .next a, .disp_page #main-content .pager .next a, .btn-default,

			.disp_search .search_result .search_content_wrap .search_title,
			.disp_search .search_result .search_content_wrap .search_title a,
			.disp_search .search_result .search_content_wrap .search_info a
			{ color: ' . $color . ' }';
            $custom_css .= '#skin_wrapper, main{ color: ' . $color . ' !important }';
        }

        /**
         * ============================================================================
         * Typograpy
         * ============================================================================
         */
        $font_size = $this->get_setting('typograpy_fz');
        switch ($font_size) {
            case 'normal': // When regular layout is chosen, nothing happens, since regular is default
                $custom_css .= '#skin_wrapper
			{ font-size: 11.42855px; }

			@media screen and (max-width: 480px) {
				#skin_wrapper { font-size: 10.5px; }
			}';
                break;

            case 'large':
                $custom_css .= '#skin_wrapper
			{ font-size: 12.85715px; }

			@media screen and (max-width: 480px) {
				#skin_wrapper { font-size: 11px; }
			}';
                break;
        }

        /**
         * ============================================================================
         * Tags Layout
         * ============================================================================
         */
        if ($color = $this->get_setting('tags_color')) {
            $custom_css .= "#main-content .post_tags a, #mini-blog .post_tags a, .widget_core_coll_tag_cloud .tag_cloud a { color: $color; }\n";
        }

        if ($bg_color = $this->get_setting('tags_bg_color')) {
            $custom_css .= "#main-content .post_tags a, #mini-blog .post_tags a, .widget_core_coll_tag_cloud .tag_cloud a { background-color: $bg_color; }\n";
        }

        if ($bdr_color = $this->get_setting('tags_bdr_color')) {
            $custom_css .= "#main-content .post_tags a, #mini-blog .post_tags a, .widget_core_coll_tag_cloud .tag_cloud a { border-color: $bdr_color; }\n";
        }

        /**
         * ============================================================================
         * Special cover image position on intro posts
         * ============================================================================
         */
        /*if( $color = $this->get_setting( 'bgimg_text_color' ) )
        {	// Custom text color on background image:
            $custom_css .= '.widget_core_coll_featured_intro div.jumbotron.evo_hasbgimg { color: '.$color." }\n";
        }
        if( $color = $this->get_setting( 'bgimg_link_color' ) )
        {	// Custom link color on background image:
            $custom_css .= '.widget_core_coll_featured_intro div.jumbotron.evo_hasbgimg a { color: '.$color." }\n";
        }
        if( $color = $this->get_setting( 'bgimg_hover_link_color' ) )
        {	// Custom link hover color on background image:
            $custom_css .= '.widget_core_coll_featured_intro div.jumbotron.evo_hasbgimg a:hover { color: '.$color." !important }\n";
        }*/

        /**
         * ============================================================================
         * Header Custom Style
         * ============================================================================
         */
        if ($color = $this->get_setting('header_top_color')) {
            $custom_css .= '#header-top .header-contact-info, #header-top .widget_core_user_links .ufld_icon_links a { color: ' . $color . ';}';
        }
        if ($bg_color = $this->get_setting('header_top_bg')) {
            $custom_css .= '#header-top{ background-color: ' . $bg_color . '; }';
        }

        if ($this->get_setting('header_sticky') == 0) {
            $custom_css .= '#main-header{ position: relative !important;} #skin_wrapper.loggedin #main-header{ top: 0 !important; }';
        }
        if ($color = $this->get_setting('site_tite_color')) {
            $custom_css .= '#main-header .widget_core_coll_title h1 a, #main-header .widget_core_coll_logo h1 a,
         .page_title
         { color: ' . $color . ' }
         ';
        }
        if ($color = $this->get_setting('menu_link_color')) {
            $custom_css .= '#main-header .primary-nav .nav a { color: ' . $color . ';}';
        }
        if ($bg_color = $this->get_setting('main_header_bg')) {
            $custom_css .= '#main-header{ background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Posts Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('regular_post_bg')) {
            $custom_css .= '
         .post_regular #skin_wrapper,
         .post_regular #main-sidebar .widget_plugin_evo_Calr .bCalendarTable caption a
         { background-color: ' . $bg_color . '; }
         ';
        }
        if ($bg_color = $this->get_setting('mini_blog_bg')) {
            $custom_css .= '.posts_mini_layout #skin_wrapper { background-color: ' . $bg_color . '; }';
        }
        if ($color = $this->get_setting('post_info_color')) {
            $custom_css .= '#main-content .evo_post .small.text-muted, #mini-blog .evo_post .small.text-muted
         { color: ' . $color . ' };
         ';
        }
        if ($color = $this->get_setting('post_info_link')) {
            $custom_css .= '.disp_single #main-content .evo_post > header .cat-links a, .disp_page #main-content .evo_post > header .cat-links a
         { color: ' . $color . ' }
         ';
        }

        /**
         * ============================================================================
         * Single and Page Disp Custom Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('single_bg')) {
            $custom_css .= '
         .disp_single #skin_wrapper, .disp_page #skin_wrapper,
         .disp_single .pager li>a, .disp_single .pager li>span,
         .disp_single .evo_post .panel-default,
         .disp_single #main-content .evo_post .panel .panel-heading .panel-title, .disp_page #main-content .evo_post .panel .panel-heading .panel-title,
         .disp_single #main-content .evo_post #feedbacks .evo_comment .panel-heading, .disp_page #main-content .evo_post #feedbacks .evo_comment .panel-heading,
         .disp_single #main-content .evo_post .panel .panel-heading, .disp_page #main-content .evo_post .panel .panel-heading
         { background-color: ' . $bg_color . '; }
         ';
        }

        /**
         * ============================================================================
         * Sidebar Custom Style
         * ============================================================================
         */
        if ($border = $this->get_setting('sidebar_border_widget')) {
            $custom_css .= '
         .widget_core_coll_category_list ul > li, .widget_core_content_hierarchy ul > li, .widget_core_coll_common_links ul > li, .widget_core_coll_post_list ul > li, .widget_core_coll_page_list ul > li, .widget_core_coll_related_post_list ul > li, .widget_plugin_evo_Arch ul > li, .widget_core_linkblog ul > li, .widget_core_coll_item_list.evo_noexcerpt ul > li, .widget_core_coll_comment_list ul > li, .widget_core_coll_xml_feeds ul > li, .widget_core_colls_list_public ul > li, .widget_core_user_tools ul > li
         { border-color: ' . $border . '; }
         ';
            $custom_css .= '
         #main-sidebar .panel-heading::after
         { background-color: ' . $border . '; }
         ';
        }
        if ($color = $this->get_setting('sidebar_title_widget')) {
            $custom_css .= '
         #main-sidebar .panel-heading .panel-title,
         .disp_front .evo_container__front_page_main_area .evo_widget h3,
		 .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_post_list.evo_withexcerpt .item_title, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_related_post_list.evo_withexcerpt .item_title, .disp_front .evo_container__front_page_main_area .evo_widget.widget_core_coll_item_list.evo_withexcerpt .item_title
         { color: ' . $color . '; }
         ';
        }
        if ($color = $this->get_setting('sidebar_color_content')) {
            $custom_css .= '
         #main-sidebar .evo_widget
         { color: ' . $color . '; }
         ';
        }
        if ($color = $this->get_setting('sidebar_color_link')) {
            $custom_css .= '
         #main-sidebar .evo_widget a,
         #main-sidebar .evo_widget ul li strong a
         { color: ' . $color . '; }
         ';
        }

        /**
         * ============================================================================
         * Front Disp Custom Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('front_bg')) {
            $custom_css .= '
         .disp_front #skin_wrapper,
         .disp_front .evo_container__front_page_main_area .widget_core_user_login
         { background-color: ' . $bg_color . '; }
         ';
        }

        /**
         * ============================================================================
         * Comments Disp Custom Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('comments_bg')) {
            $custom_css .= '
         .disp_comments #skin_wrapper
         { background-color: ' . $bg_color . '; }
         ';
        }

        /**
         * ============================================================================
         * Mediaidx Custom Style
         * ============================================================================
         */
        if ($padding = $this->get_setting('padding_column')) {
            $custom_css .= '.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index li{ padding: ' . $padding . 'px }';
            $custom_css .= '.disp_mediaidx #main-mediaidx .title_mediaidx { margin-left: ' . $padding . 'px; margin-right: ' . $padding . 'px; }';
        }
        if ($color = $this->get_setting('mediaidx_bg')) {
            $custom_css .= '.disp_mediaidx #skin_wrapper,
		 .disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index .note {
            background-color: ' . $color . '; }';
        }
        if ($color = $this->get_setting('mediaidx_bg_content')) {
            $custom_css .= '.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index li figure.box,
         .disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index li figure.box .note {
            background-color: ' . $color . ';}';
        }
        if ($color = $this->get_setting('mediaidx_title_color')) {
            $custom_css .= '.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index .note {
            color: ' . $color . ';}';
        }
        if ($bg_color = $this->get_setting('mediaidx_title_color')) {
            $custom_css .= '.disp_mediaidx #main-mediaidx .widget_core_coll_media_index .evo_image_index li figure.box.title a::after {
            background-color: ' . $color . ';}';
        }

        /**
         * ============================================================================
         * Disp Search Custome Style
         * ============================================================================
         */
        if ($color = $this->get_setting('search_text_info')) {
            $custom_css .= '.disp_search .search_result .search_content_wrap .search_info
         { color: ' . $color . ' }';
        }
        if ($bg_color = $this->get_setting('search_bg')) {
            $custom_css .= '.disp_search #skin_wrapper { background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Disp Archir Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('arcdir_bg')) {
            $custom_css .= '.disp_arcdir #skin_wrapper { background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Disp Catdir Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('catdir_bg')) {
            $custom_css .= '.disp_catdir #skin_wrapper { background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Disp Postidx Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('postidx_bg')) {
            $custom_css .= '.disp_postidx #skin_wrapper { background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Disp Tags Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('tags_bg')) {
            $custom_css .= '.disp_tags #skin_wrapper { background-color: ' . $bg_color . '; }';
        }

        /**
         * ============================================================================
         * Footer Custome Style
         * ============================================================================
         */
        if ($bg_color = $this->get_setting('footer_bg')) {
            $custom_css .= '
         #main-footer, #main-footer .widget_footer .widget_plugin_evo_Calr .bCalendarTable caption a
         { background-color: ' . $bg_color . '; }
         ';
        }
        if ($border = $this->get_setting('footer_border_widget')) {
            $custom_css .= '#main-footer .widget_footer .widget_core_coll_category_list ul > li, #main-footer .widget_footer .widget_core_content_hierarchy ul > li, #main-footer .widget_footer .widget_core_coll_common_links ul > li, #main-footer .widget_footer .widget_core_coll_post_list ul > li, #main-footer .widget_footer .widget_core_coll_page_list ul > li, #main-footer .widget_footer .widget_core_coll_related_post_list ul > li, #main-footer .widget_footer .widget_plugin_evo_Arch ul > li, #main-footer .widget_footer .widget_core_linkblog ul > li, #main-footer .widget_footer .widget_core_coll_item_list.evo_noexcerpt ul > li, #main-footer .widget_footer .widget_core_coll_comment_list ul > li, #main-footer .widget_footer .widget_core_coll_xml_feeds ul > li, #main-footer .widget_footer .widget_core_colls_list_public ul > li, #main-footer .widget_footer .widget_core_user_tools ul > li
         { border-color: ' . $border . '; }';

            $custom_css .= '#main-footer .copyright { border-top-color: ' . $border . ' }';
            $custom_css .= '#main-footer .widget_footer .widget_core_coll_category_list ul > li, #main-footer .widget_footer .widget_core_content_hierarchy ul > li, #main-footer .widget_footer .widget_core_coll_common_links ul > li, #main-footer .widget_footer .widget_core_coll_post_list ul > li, #main-footer .widget_footer .widget_core_coll_page_list ul > li, #main-footer .widget_footer .widget_core_coll_related_post_list ul > li, #main-footer .widget_footer .widget_plugin_evo_Arch ul > li, #main-footer .widget_footer .widget_core_linkblog ul > li, #main-footer .widget_footer .widget_core_coll_item_list.evo_noexcerpt ul > li, #main-footer .widget_footer .widget_core_coll_comment_list ul > li, #main-footer .widget_footer .widget_core_coll_xml_feeds ul > li, #main-footer .widget_footer .widget_core_colls_list_public ul > li, #main-footer .widget_footer .widget_core_user_tools ul > li, #main-footer .widget_footer .widget_core_coll_link_list ul > li, #main-footer .widget_footer .widget_core_colls_list_owner ul > li
         { border-bottom-color: ' . $border . ' }';
        }
        if ($color = $this->get_setting('footer_title_color')) {
            $custom_css .= '#main-footer .widget_footer .evo_widget .widget_title { color: ' . $color . ' }';
        }
        if ($color = $this->get_setting('footer_text_content')) {
            $custom_css .= '#main-footer { color: ' . $color . '; }';
        }
        if ($color = $this->get_setting('footer_link_color')) {
            $custom_css .= '#main-footer a, #main-footer .widget_footer .evo_widget a,
         #main-footer .copyright a
         { color: ' . $color . ' }';
        }
        if ($color = $this->get_setting('copyright_color')) {
            $custom_css .= '#main-footer .copyright p{ color: ' . $color . ' }';
        }

        if ($disp == 'posts') {
            $custom_css .= ".disp_posts .well { padding: 0; }\n";
            $custom_css .= ".disp_posts .well header, .disp_posts .well section, .disp_posts .well footer { padding: 0 30px; }\n";
            $custom_css .= ".disp_posts .well footer { padding-bottom: 20px; }\n";
        }

        if ($this->get_setting('ht_show') == 1) {
            $custom_css .= ".sitewide_header { margin-bottom: 0 !important; }";
        }

        // Custom CSS Output
        if (! empty($custom_css)) {
            add_css_headline($custom_css);
        }

        // Limit images by max height:
        $max_image_height = intval($this->get_setting('max_image_height'));
        if ($max_image_height > 0) {
            add_css_headline('.evo_image_block img { max-height: ' . $max_image_height . 'px; width: auto; }');
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
                // Default Form settings (Used for any form on front-office):
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

            case 'fixed_form':
                // Form with fixed label width (Used for form on disp=user):
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
     * Check if we can display a widget container
     *
     * @param string Widget container key: 'header', 'menu', 'sidebar', 'footer'
     * @param string Skin setting name
     * @return boolean TRUE to display
     */
    public function is_visible_container($container_key, $setting_name = 'access_login_containers')
    {
        $access = $this->get_setting($setting_name);

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
            return ($this->is_visible_container('sidebar') || $this->is_visible_container('sidebar2'));
        } else { // We should not check the visibility of the sidebar containers for this case
            return true;
        }
    }

    public function is_visible_sidebar_front($check_containers = false)
    {
        $layout = $this->get_setting('layout_front');

        if ($layout != 'left_sidebar' && $layout != 'right_sidebar') { // Sidebar is not displayed for selected skin layout
            return false;
        }

        if ($check_containers) { // Check if at least one sidebar container is visible
            return ($this->is_visible_container('sidebar') || $this->is_visible_container('sidebar2'));
        } else { // We should not check the visibility of the sidebar containers for this case
            return true;
        }
    }

    /**
     * Get value for attbiute "class" of column block
     * depending on skin setting "Layout"
     *
     * @return string
     */
    public function get_column_class()
    {
        switch ($this->get_setting('layout')) {
            case 'single_column':
                // Single Column Large
                return 'col-md-12';

            case 'single_column_normal':
                // Single Column
                return 'col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1';

            case 'single_column_narrow':
                // Single Column Narrow
                return 'col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2';

            case 'single_column_extra_narrow':
                // Single Column Extra Narrow
                return 'col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3';

            case 'left_sidebar':
                // Left Sidebar
                return 'col-xs-12 col-sm-12 col-md-8 pull-right';

            case 'right_sidebar':
                // Right Sidebar
            default:
                return 'col-xs-12 col-sm-12 col-md-8';
        }
    }

    public function get_column_class_front()
    {
        switch ($this->get_setting('layout_front')) {
            case 'single_column':
                // Single Column Large
                return 'col-md-12';

            case 'single_column_normal':
                // Single Column
                return 'col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1';

            case 'single_column_narrow':
                // Single Column Narrow
                return 'col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2';

            case 'single_column_extra_narrow':
                // Single Column Extra Narrow
                return 'col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3';

            case 'left_sidebar':
                // Left Sidebar
                return 'col-xs-12 col-sm-12 col-md-8 pull-right';

            case 'right_sidebar':
                // Right Sidebar
            default:
                return 'col-xs-12 col-sm-12 col-md-8';
        }
    }

    public function get_column_cover_image()
    {
        switch ($this->get_setting('layout')) {
            case 'single_column':
                // Single Column Large
                return 'col-md-12';

            case 'single_column_normal':
                // Single Column
                return 'col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1';

            case 'single_column_narrow':
                // Single Column Narrow
                return 'col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2';

            case 'single_column_extra_narrow':
                // Single Column Extra Narrow
                return 'col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3';

            case 'left_sidebar':
                // Left Sidebar
                return 'col-md-12';

            case 'right_sidebar':
                // Right Sidebar
            default:
                return 'col-md-12';
        }
    }

    /**
     * ============================================================================
     * Layout Post Setting
     * ============================================================================
     */
    public function layout_posts_setting()
    {
        if ($this->get_setting('layout_posts') == 'mini_blog') {
            echo "mini-blog";
        } else {
            echo "main-content";
        }
    }

    /**
     * ============================================================================
     * Layout Mediaidx Settings
     * ============================================================================
     */
    public function layout_mediaidx_setting()
    {
        switch ($this->get_setting('mediaidx_layout')) {
            case 'no_sidebar':
                // Single Column Large
                return 'col-md-12';

            case 'left_sidebar':
                // Left Sidebar
                return 'col-xs-12 col-sm-12 col-md-8 pull-right';

            case 'right_sidebar':
                // Right Sidebar
            default:
                return 'col-xs-12 col-sm-12 col-md-8';
        }
    }

    /**
     * ============================================================================
     * Check if post have a Images and Attachment for Mini Blog Layout
     * ============================================================================
     */
    public function have_posts_image()
    {
        global $Item;

        $have_image = '';

        $item_first_image = $Item->get_images([
            'restrict_to_image_position' => 'teaser,aftermore,inline',
            'get_rendered_attachments' => false,
        ]);

        if (! empty($item_first_image)) {
            $have_image = 'have_image';
        }

        return $have_image;
    }

    /* CHANGE CLASS
     * ========================================================================== */
    public function change_class($id)
    {
        $id = $this->get_setting($id);
        if ($id == $id) {
            return $id;
        }
    }
}
