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
class photo_mouse_Skin extends Skin
{
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
        return 'Photo Mouse';
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
            'main' => 'partial',
            'std' => 'yes',		// Blog
            'photo' => 'yes',
            'forum' => 'no',
            'manual' => 'no',
            'group' => 'maybe',  // Tracker
            // Any kind that is not listed should be considered as "maybe" supported
        ];

        return $supported_kinds;
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
            // Layout settings
            'section_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Layout Settings'),
            ],
            'layout' => [
                'label' => T_('Layout'),
                'note' => '',
                'defaultvalue' => 'right_sidebar',
                'options' => [
                    'single_column' => T_('Single Column Large'),
                    'single_column_normal' => T_('Single Column'),
                    'single_column_narrow' => T_('Single Column Narrow'),
                    'single_column_extra_narrow' => T_('Single Column Extra Narrow'),
                    'left_sidebar' => T_('Left Sidebar'),
                    'right_sidebar' => T_('Right Sidebar'),
                ],
                'type' => 'select',
            ],
            'section_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            // Images layout
            'section_image_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Images Layout'),
            ],
            'max_image_height' => [
                'label' => T_('Max image height'),
                'note' => 'px',
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'mediaidx_thumb_size' => [
                'label' => T_('Thumbnail size for media index'),
                'note' => '',
                'defaultvalue' => 'fit-80x80',
                'options' => get_available_thumb_sizes(),
                'type' => 'select',
            ],
            'section_image_end' => [
                'layout' => 'end_fieldset',
            ],

            // Custom page styles
            'section_page_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Custom Page Styles'),
            ],
            'page_bg_color' => [
                'label' => T_('Page background color'),
                'note' => T_('E-g: #ff0000 for red'),
                'defaultvalue' => '#666666',
                'type' => 'color',
            ],
            'page_text_color' => [
                'label' => T_('Page text color'),
                'note' => T_('E-g: #00ff00 for green'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'menu_bg_color' => [
                'label' => T_('Menu background color'),
                'note' => T_('E-g: #0000ff for blue'),
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'menu_text_color' => [
                'label' => T_('Menu elements color'),
                'note' => T_('E-g: #ff6600 for orange'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'menu_links_hover' => [
                'label' => T_('Menu links hover color'),
                'note' => T_('E-g: #ff6600 for orange'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'post_bg_color' => [
                'label' => T_('Post info background color'),
                'note' => T_('E-g: #0000ff for blue'),
                'defaultvalue' => '#555555',
                'type' => 'color',
            ],
            'post_border_col' => [
                'label' => T_('Post border color'),
                'note' => T_('E-g: #0000ff for blue'),
                'defaultvalue' => '#aaa',
                'type' => 'color',
            ],
            'post_text_color' => [
                'label' => T_('Post info text color'),
                'note' => T_('E-g: #ff6600 for orange'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'main_link_color' => [
                'label' => T_('Main content links color'),
                'note' => T_('E-g: #fff for white'),
                'defaultvalue' => '#fff',
                'type' => 'color',
            ],
            'panel_titles' => [
                'label' => T_('Panel titles color'),
                'note' => T_('E-g: #fff for white'),
                'defaultvalue' => '#fff',
                'type' => 'color',
            ],
            'section_page_end' => [
                'layout' => 'end_fieldset',
            ],

            // Submit & preview styles
            'subm_prev_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Submit and Preview buttons styles'),
            ],
            'prev_bgd' => [
                'label' => T_('Panel titles color'),
                'defaultvalue' => '#aaa',
                'type' => 'color',
            ],
            'subm_bgd' => [
                'label' => T_('Panel titles color'),
                'defaultvalue' => '#333',
                'type' => 'color',
            ],
            'subm_prev_end' => [
                'layout' => 'end_fieldset',
            ],

            // Footer styles
            'footer_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Submit and Preview buttons styles'),
            ],
            'footer_bgd' => [
                'label' => T_('Footer background color'),
                'defaultvalue' => '#aaa',
                'type' => 'color',
            ],
            'footer_text' => [
                'label' => T_('Panel titles color'),
                'defaultvalue' => '#aaa',
                'type' => 'color',
            ],
            'footer_links' => [
                'label' => T_('Panel titles color'),
                'defaultvalue' => '#aaa',
                'type' => 'color',
            ],
            'footer_end' => [
                'layout' => 'end_fieldset',
            ],

            // Colorbox image zoom
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

            // Username options
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

            // Access layout
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

        if ($page_bg_color = $this->get_setting('page_bg_color')) { // Background color:
            $custom_css .= '#skin_wrapper { background-color: ' . $page_bg_color . " }\n";
        }

        if ($text_color = $this->get_setting('page_text_color')) { // Text color:
            $custom_css .= '#skin_wrapper { color: ' . $text_color . " }\n";
        }

        if ($menu_bg_color = $this->get_setting('menu_bg_color')) { // Menu background color:
            $custom_css .= 'div.pageHeader { background-color: ' . $menu_bg_color . " }\n";
        }

        if ($menu_t_color = $this->get_setting('menu_text_color')) { // Menu elements color:
            $custom_css .= '
			div.pageHeader,
			div.pageHeader a,
			div.pageHeader span { color: ' . $menu_t_color . " }\n";
        }

        if ($menu_a_hover = $this->get_setting('menu_links_hover')) { // Menu links hover color:
            $custom_css .= 'div.pageHeader a:hover { color: ' . $menu_a_hover . " }\n";
        }

        if ($post_bg_color = $this->get_setting('post_bg_color')) { // Post background color:
            $custom_css .= '
			div.evo_post_details,
			.panel,
			#comment_preview,
			.error_404,
			.error_additional_content,
			.disp_arcdir main,
			.disp_catdir main,
			div.widget_core_coll_item_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_featured_posts.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_post_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_page_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_related_post_list.evo_layout_rwd .widget_rwd_content,
			.search_result,
			.page_navigation a { background-color: ' . $post_bg_color . " }\n";
        }

        if ($post_border_col = $this->get_setting('post_border_col')) { // Post border color:
            $custom_css .= '
			.evo_post div.evo_post_details,
			.panel,
			.evo_featured_post div.evo_post_details,
			.featurepost div.evo_post_details,
			.error_404,
			.error_additional_content,
			.disp_arcdir main,
			.disp_catdir main,
			div.widget_core_coll_item_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_featured_posts.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_post_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_page_list.evo_layout_rwd .widget_rwd_content,
			div.widget_core_coll_related_post_list.evo_layout_rwd .widget_rwd_content,
			.search_result,
			.page_navigation a { border: 1px solid ' . $post_border_col . " }\n";
            $custom_css .= 'article.evo_intro_post .evo_post_details { border: 3px solid ' . $post_border_col . " }\n";
            $custom_css .= '.main_content_container .panel-heading, .evo_comment__meta_info a, .evo_post_comment_notification a, .evo_comment__meta_info a:hover, .evo_post_comment_notification a:hover { background-color: ' . $post_border_col . " !important }\n";
        }

        if ($post_text_color = $this->get_setting('post_text_color')) { // Post text color:
            $custom_css .= 'div.evo_post_details { color: ' . $post_text_color . " }\n";
        }

        if ($main_link_color = $this->get_setting('main_link_color')) { // Links color (main section):
            $custom_css .= 'div.main_content_container a { color: ' . $main_link_color . " }\n";
            $custom_css .= 'div.item_content a { color: ' . $main_link_color . " !important }\n";
            $custom_css .= '#bCalendarToday { background-color: ' . $main_link_color . " }\n";
        }

        if ($panel_titles = $this->get_setting('panel_titles')) { // Panel titles color:
            $custom_css .= '
			.main_content_container .panel-heading h4,
			.main_content_container .panel-heading h3,
			.main_content_container .panel-heading a,
			.main_content_container legend.panel-heading { color: ' . $panel_titles . " }\n";
        }

        if ($prev_bgd = $this->get_setting('prev_bgd')) { // Preview button background color:
            $custom_css .= '.main_content_container .preview { background-color: ' . $prev_bgd . " }\n";
        }

        if ($subm_bgd = $this->get_setting('subm_bgd')) { // Submit button background color:
            $custom_css .= '
			.main_content_container .submit,
			.main_content_container .filters button[type=submit] { background-color: ' . $subm_bgd . " }\n";
            $custom_css .= '
			.main_content_container .search_submit,
			.main_content_container .filters button[type=submit] { border-color: ' . $subm_bgd . " }\n";
        }

        if (! empty($custom_css)) {
            $custom_css = '<style type="text/css">
	<!--
' . $custom_css . '	-->
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
     * @param string Widget container key: 'header', 'page_top', 'menu', 'sidebar', 'sidebar2', 'footer'
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
                return 'col-md-9 pull-right';

            case 'right_sidebar':
                // Right Sidebar
            default:
                return 'col-md-9';
        }
    }
}
