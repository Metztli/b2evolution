<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage material_main
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class material_main_Skin extends Skin
{
    public $version = '7.2.2';

    /**
     * Do we want to use style.min.css instead of style.css ?
     */
    public $use_min_css = true;  // true|false|'check' Set this to true for better optimization

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Material Main';
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
     * @return array
     */
    public function get_declared_containers()
    {
        // Note: second param below is the ORDER
        return [
            'header' => [NT_('Header'), 10],
            'front_page_main_area' => [NT_('Front Page Main Area'), 40],
            'item_single_header' => [NT_('Item Single Header'), 50],
            'item_single' => [NT_('Item Single'), 51],
            'item_page' => [NT_('Item Page'), 55],
            'contact_page_main_area' => [NT_('Contact Page Main Area'), 60],
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
            '1_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Front Page Settings'),
            ],
            'front_bg_image' => [
                'label' => T_('Image section background image'),
                'defaultvalue' => 'shared/global/sunset/sunset.jpg',
                'type' => 'text',
                'size' => '50',
            ],
            'front_bg_color' => [
                'label' => T_('Image section background color'),
                'note' => T_('Click to select the background color of the image section, if the background image is not set or does not exist.'),
                'defaultvalue' => '#F1F1F1',
                'type' => 'color',
            ],
            'site_info_color' => [
                'label' => T_('Site info color'),
                'note' => T_('Click to select color of site title and tagline.'),
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            // 'front_text_color' => array(
            // 'label' => T_('Front page text color'),
            // 'note' => T_('Click to select a color.'),
            // 'defaultvalue' => '#666',
            // 'type' => 'color',
            // ),
            'front_link_color' => [
                'label' => T_('Primary color'),
                'note' => T_('Click to select a color.'),
                'defaultvalue' => '#448AFF',
                'type' => 'color',
            ],
            'front_icon_color' => [
                'label' => T_('Inverse icon color'),
                'note' => T_('Click to select a color.'),
                'defaultvalue' => '#FFFFFF',
                'type' => 'color',
            ],
            // 'front_bg_opacity' => array(
            // 'label' => T_('Front page main area widget opacity'),
            // 'note' => '%.',
            // 'size' => '7',
            // 'maxlength' => '3',
            // 'defaultvalue' => '100',
            // 'type' => 'integer',
            // 'valid_range' => array(
            // 'min' => 0, // from 0%
            // 'max' => 100, // to 100%
            // ),
            // ),
            '1_end' => [
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

        if (in_array($disp, ['front', 'login', 'register', 'lostpassword', 'activateinfo', 'access_denied', 'access_requires_login'])) {
            global $media_url, $media_path;

            // Add custom CSS:
            $custom_css = '';

            $bg_image = $this->get_setting('front_bg_image');
            if (! empty($bg_image) && file_exists($media_path . $bg_image)) { // Custom body background image:
                $custom_css .= '#bg_picture { background-image: url(' . $media_url . $bg_image . ") }\n";
            }

            if ($color = $this->get_setting('front_bg_color')) { // Custom body background color:
                $custom_css .= '#bg_picture { background-color: ' . $color . " }\n";
            }

            if ($color = $this->get_setting('site_info_color')) { // Custom title color:
                $custom_css .= 'body.pictured .widget_core_coll_title h2 a, .evo_widget.widget_core_coll_tagline { color: ' . $color . " }\n";
            }

            // if( $color = $this->get_setting( 'front_text_color' ) )
            // { // Custom text color:
            // $custom_css .= '.content p, .content li, .content td, .content dd, .secondary_area .evo_widget,'
            // . ' .evo_widget, .evo_widget .user_group, .evo_widget .user_level,'
            // . '#skin_wrapper { color: '.$color." }\n";
            // }

            $link_color = $this->get_setting('front_link_color');
            $icon_color = $this->get_setting('front_icon_color');
            if ($link_color) { // Custom link color:
                $custom_css .= ' .main_top .widget_core_coll_tag_cloud h2, .main_top .widget_core_coll_xml_feeds h2, .main_top .widget_plugin_evo_Arch h2, .main_top .widget_core_content_hierarchy h2, .main_top .widget_core_coll_longdesc h2, .main_top .widget_plugin_evo_WhosOnline h2,'
                                        . ' .main_top .widget_core_colls_list_public h2, .main_top .widget_core_colls_list_owner h2, .main_top .widget_core_user_login h2, .main_top .widget_core_user_tools h2, .main_top .widget_core_free_html h2, .main_top .widget_core_menu_link h2,'
                                        . ' .main_top .widget_core_msg_menu_link h2, .main_top .widget_core_coll_current_filters h2, .main_top .widget_core_linkblog h2, '

                                        . ' .secondary_area .widget_core_coll_tag_cloud h2, .secondary_area .widget_core_coll_xml_feeds h2, .secondary_area .widget_plugin_evo_Arch h2, .secondary_area .widget_core_content_hierarchy h2, .secondary_area .widget_plugin_evo_WhosOnline h2,'
                                        . ' .secondary_area .widget_core_colls_list_public h2, .secondary_area .widget_core_colls_list_owner h2, .secondary_area .widget_core_user_login h2, .secondary_area .widget_core_user_tools h2, .secondary_area .widget_core_menu_link h2,'
                                        . ' .secondary_area .widget_core_msg_menu_link h2, .secondary_area .widget_core_coll_current_filters h2, .secondary_area .widget_core_linkblog h2, .secondary_area .widget_core_online_users h2, .secondary_area .widget_core_user_avatars h2,'

                                        . ' .widget_core_coll_current_filters h2, .widget_core_linkblog h2, .view-container .widget_list h2, .widget_core_coll_media_index h2, '
                                        . ' .widget_plugin_evo_Calr table.bCalendarTable caption a, .widget_plugin_evo_Calr table.bCalendarTable caption, .widget_plugin_evo_Calr table.bCalendarTable tbody .bCalendarLinkPost a,'
                                        . ' .main .content a:hover, .content a:hover,'
                                        . ' .widget_core_org_members .col-lg-4 h3, .widget_core_coll_search_form h1, .widget_core_coll_search_form h2, .secondary_area .widget_core_coll_search_form h1, .secondary_area .widget_core_coll_search_form h2,'
                                        . ' .widget_plugin_evo_Calr table.bCalendarTable tfoot #next a:hover::before, .widget_plugin_evo_Calr table.bCalendarTable tfoot #next a:hover::after,'
                                        . ' .widget_plugin_evo_Calr table.bCalendarTable tfoot #prev a:hover::before, .widget_plugin_evo_Calr table.bCalendarTable tfoot #prev a:hover::after,'
                                        . ' .front_main_content .widget_list > h2, button, input[type="button"], input[type="reset"], input[type="submit"],'
                                        . ' .view-container .widget_list h4, .view-container .widget_list h4 a,'
                                        . ' .widget_core_user_login strong a, .widget_core_user_login strong .panel-default input,'

                                        . ' #skin_wrapper .profile_column_left h1, #skin_wrapper .profile_column_left .btn-primary, #skin_wrapper .profile_column_left .btn-primary button,'
                                        . ' .profile_column_right .panel-default .panel-heading, .profile_column_right .panel-default .panel-body .form-group .control-label span, '
                                        . ' .secondary_area .evo_widget[class$="list"] h2, .secondary_area .evo_widget[class$="list"] h4, .secondary_area .evo_widget[class$="list"] h4 a,'
                                        . ' .secondary_area .widget_core_coll_longdesc h2, .secondary_area .widget_core_user_tools h2, '

                                        . ' .main .pagination>li>a:hover, .main .pagination>li>span:hover, .main .pagination>li>.current, .main .pager li>a, .main .pager li>span,'
                    . ' .profile_column_right .panel-default .panel-heading, #skin_wrapper .profile_column_left h1,'
                    . '
					.evo_widget > h3
						
				       
					'
                    . '{ color: ' . $link_color . " }\n";

                $custom_css .= '.secondary_area .evo_widget h2, #bCalendarToday { background: ' . $link_color . " }\n";

                $custom_css .= 'input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus,'
                         . ' input[type="password"]:focus, input[type="search"]:focus, textarea:focus'
                         . ' { border-bottom: 1px solid ' . $link_color . " }\n";

                $custom_css .= '.widget_core_coll_search_form form input[type=text]:focus { border-bottom: 2px solid ' . $link_color . " }\n";

                $custom_css .= '.widget_plugin_evo_Calr table.bCalendarTable tbody #bCalendarToday { border: 1px solid ' . $link_color . " }\n";
            }
            if ($link_color && $icon_color) { // Custom icon color:
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a[class*="ufld__hoverbgcolor"] span { color: ' . $icon_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a[class*="ufld__bgcolor"] span { color: ' . $icon_color . " }\n";

                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:hover { color: ' . $icon_color . " }\n";

                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:not([class*="ufld__textcolor"]):not(:hover) { color: ' . $icon_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:not([class*="ufld__bgcolor"]):not(:hover) { background-color: ' . $link_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:hover:not([class*="ufld__hovertextcolor"]) { color: ' . $link_color . " }\n";
                $custom_css .= 'body.pictured .front_main_content .ufld_icon_links a:hover:not([class*="ufld__hoverbgcolor"]) { background-color: ' . $icon_color . " }\n";
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
     * Those templates are used for example by the messaging screens.
     */
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
                    'head_title' => '<div class="panel-heading">$title$<span class="pull-right">$global_icons$</span></div>' . "\n",
                    'filters_start' => '<div class="filters panel-body form-inline">',
                    'filters_end' => '</div>',
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
                    'page_current_template' => '<span><b>$page_num$</b></span>',
                    'page_item_before' => '<li>',
                    'page_item_after' => '</li>',
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
                // Default Form settings:
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
                    'labelempty' => '<label class="control-label"></label>',
                    'inputstart' => '<div class="controls col-sm-9">',
                    'inputend' => "</div>\n",
                    'infostart' => '<div class="controls col-sm-9"><div class="form-control-static">',
                    'infoend' => "</div></div>\n",
                    'buttonsstart' => '<div class="form-group"><div class="control-buttons">',
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
}
