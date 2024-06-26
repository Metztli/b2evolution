<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage bootstrap_manual
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class material_manual_Skin extends Skin
{
    /**
     * Skin version
     * @var string
     */
    public $version = '6.2.1';

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
        return 'Material Manual Skin';
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
            'std' => 'no',		// Blog
            'photo' => 'no',
            'forum' => 'no',
            'manual' => 'yes',
            'group' => 'no',  // Tracker
            // Any kind that is not listed should be considered as "maybe" supported
        ];

        return $supported_kinds;
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
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'general_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('General Settings'),
            ],
            'bg_color' => [
                'label' => T_('Site background color'),
                'note' => T_('Default color is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'text_color' => [
                'label' => T_('Site text color'),
                'note' => T_('Default color is') . ' #666.',
                'defaultvalue' => '#666',
                'type' => 'color',
            ],
            'headings_color' => [
                'label' => T_('Site headings color'),
                'note' => T_('Default color is') . ' #444.',
                'defaultvalue' => '#444',
                'type' => 'color',
            ],
            'link_color' => [
                'label' => T_('Site link color'),
                'note' => T_('Default color is') . ' #212121.',
                'defaultvalue' => '#212121',
                'type' => 'color',
            ],
            'link_h_color' => [
                'label' => T_('Site link hover color'),
                'note' => T_('Default color is') . ' #448AFF.',
                'defaultvalue' => '#448AFF',
                'type' => 'color',
            ],
            'section_bg' => [
                'label' => T_('Items background color'),
                'note' => T_('This stands for menu links, buttons, featured/intro posts, etc. Default color is') . ' #F4F4F4.',
                'defaultvalue' => '#F4F4F4',
                'type' => 'color',
            ],
            'divider_color' => [
                'label' => T_('Divider color'),
                'note' => T_('Divider is a line on the bottom of every post. Default color is') . ' #E7E7E7.',
                'defaultvalue' => '#E7E7E7',
                'type' => 'color',
            ],
            'left_navigation' => [
                'label' => T_('Fixed sidebar'),
                'note' => T_('Check to enable the fixed sidebar.'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'general_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'header_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Header Settings'),
            ],
            'header_bg' => [
                'label' => T_('Header background color'),
                'note' => T_('Default color is') . ' #448AFF.',
                'defaultvalue' => '#448AFF',
                'type' => 'color',
            ],
            'header_color' => [
                'label' => T_('Header font color'),
                'note' => T_('Default color is') . ' #FFF.',
                'defaultvalue' => '#FFF',
                'type' => 'color',
            ],
            'header_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'section_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Layout Settings'),
            ],
            'max_image_height' => [
                'label' => T_('Max image height'),
                'note' => 'px',
                'defaultvalue' => '',
                'type' => 'integer',
                'allow_empty' => true,
            ],
            'page_navigation' => [
                'label' => T_('Page navigation'),
                'note' => T_('(EXPERIMENTAL)') . ' ' . T_('Check this to show previous/next page links to navigate inside the <b>current</b> chapter.'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
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
        add_headline('<link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">');

        // Add custom CSS:
        $custom_css = '';

        // Custom background color:
        if ($color = $this->get_setting('bg_color')) {
            $custom_css = '#skin_wrapper { background: ' . $color . " }\n";
        }
        // Custom text color:
        if ($color = $this->get_setting('text_color')) {
            $custom_css .= '.main, .content blockquote, .content address, .content p, .content li, .content td, .content dd, .result_content, .search_title, .main textarea { color: ' . $color . " }\n";
        }
        // Custom headings color:
        if ($color = $this->get_setting('headings_color')) {
            $custom_css .= '.content h1, .content h2, .content h3, .content h4, .content h5, .content h6, .content cite, .main .title { color: ' . $color . " }\n";
            $custom_css .= '.content cite { border-bottom: 2px solid ' . $color . " }\n";
        }
        // Custom link color:
        if ($color = $this->get_setting('link_color')) {
            $custom_css .= '.main a, .main .nav > li a, .main .pagination>li>a, .main .panel-default>.panel-heading a, .main .panel-default>.panel-heading .panel-icon, .main .panel-title, .main .evo_post_more_link, .main .evo_comment .panel-heading .evo_comment_title { color: ' . $color . " }\n";
        }
        // Custom link hover color:
        if ($color = $this->get_setting('link_h_color')) {
            $custom_css .= '.content a:hover, .color-hover a:hover, .main .nav > li a:hover, .main .pagination>li>a:hover, .main .pagination>li>span:hover, .main .pagination>li.active>span, .main .pager li>a, .main .pager li>span, .profile_column_right .panel-default .panel-heading, .main .panel-title a, .main .evo_post_more_link a, .main .evo_post__excerpt_more_link a, .main .evo_comment .panel-heading a:hover, .main .evo_comment .panel-heading a, profile_column_left h1, .profile_column_left .profile_buttons .btn-primary, .profile_column_left .profile_buttons .btn-primary button, .profile_column_left h1, .main button, .main input.submit, .main input.preview, .main input[type="reset"], .main input[type="submit"], .tabs > .selected a, .disp_profile .panel button.btn, .disp_subs .form-horizontal .controls span.help-inline a.btn { color: ' . $color . " }\n";
            $custom_css .= '#bCalendarToday { background: ' . $color . " }\n";
        }
        // Sections background color:
        if ($color = $this->get_setting('section_bg')) {
            $custom_css .= '.main .nav > li a, .main .pager li>a, .main .pager li>span, .featured_post, .main .panel-default>.panel-heading, .main .evo_post_more_link a, .main .evo_post__excerpt_more_link a, .evo_comment_footer small a { background: ' . $color . " }\n";
            $custom_css .= '.main .pagination>li>a, .main .pagination>li>span, .small >span, .profile_column_left .profile_buttons .btn-group a, .profile_column_left .profile_buttons p a button, .main .input.submit, .main input[type="button"]:focus, .main input[type="reset"]:focus, .main  input[type="submit"]:focus, .main button:active, .main input[type="button"]:active, .main input[type="reset"]:active, .main input[type="submit"]:active, .main input[type="submit"], .disp_mediaidx .widget_flow_blocks div, .disp_profile .panel button.btn, .disp_subs .form-horizontal .controls span.help-inline a.btn { background: ' . $color . " !important }\n";
            $custom_css .= '.main input[type="submit"], .disp_profile .panel button.btn { border: 2px solid ' . $color . " }\n";
        }
        // Divider color:
        if ($color = $this->get_setting('divider_color')) {
            $custom_css .= '.post, .main .panel-group .panel li, .content ul li, .main .evo_comment { border-bottom: 1px solid ' . $color . " }\n";
            $custom_css .= '.post, .main .panel-group .panel li ul, .content ul li ul { border-top: 1px solid ' . $color . " }\n";
            $custom_css .= 'input[type="text"], input[type="email"], input[type="url"], input[type="password"], input[type="search"], textarea, input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="password"]:focus, input[type="search"]:focus, textarea:focus { border: 2px solid ' . $color . " !important }\n";
        }

        // Custom link hover color:
        if ($color = $this->get_setting('header_bg')) {
            $custom_css .= '.masterhead, .disp_contacts .results .form-group.pull-right button, .disp_threads .results .form-group.pull-right button, .panel-heading .action_icon { background-color: ' . $color . " }\n";
        }
        // Custom link hover color:
        if ($color = $this->get_setting('header_color')) {
            $custom_css .= '.masterhead, .masterhead .widget_core_coll_title a, .masterhead .widget_core_coll_title a:hover, .disp_contacts .results .form-group.pull-right button, .disp_threads .results .form-group.pull-right button, .main .panel-default>.panel-heading .action_icon { color: ' . $color . "}\n";
        }

        // Limit images by max height:
        $max_image_height = intval($this->get_setting('max_image_height'));
        if ($max_image_height > 0) {
            add_css_headline('.evo_image_block img { max-height: ' . $max_image_height . 'px; width: auto; }');
        }

        // Initialize a template depending on current page
        switch ($disp) {
            case 'front':
                // Init star rating for intro posts:
                init_ratings_js('blog', true);
                break;

            case 'posts':
                global $cat, $bootstrap_manual_posts_text;

                // Init star rating for intro posts:
                init_ratings_js('blog', true);

                $bootstrap_manual_posts_text = T_('Posts');
                if (! empty($cat)) { // Init the <title> for categories page:
                    $ChapterCache = &get_ChapterCache();
                    if ($Chapter = &$ChapterCache->get_by_ID($cat, false)) {
                        $bootstrap_manual_posts_text = $Chapter->get('name');
                    }
                }
                break;
        }

        if ($this->is_left_navigation_visible() && $this->get_setting('left_navigation') == true) { // Include JS code for left navigation panel only when it is displayed:
            require_js('left_navigation.js', 'relative');
        }

        // Function for custom css
        if (! empty($custom_css)) {
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
                    'labelclass' => 'control-label col-md-12',
                    'labelstart' => '',
                    'labelend' => "\n",
                    'labelempty' => '<label class="control-label col-md-12"></label>',
                    'inputstart' => '<div class="controls col-md-12">',
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

            case 'disp_params':
                // Params for skin_include( '$disp$', array( ) )
                return [
                    'author_link_text' => 'preferredname',
                    // Profile tabs to switch between user edit forms
                    'profile_tabs' => [
                        'block_start' => '<nav><ul class="nav nav-tabs profile_tabs">',
                        'item_start' => '<li>',
                        'item_end' => '</li>',
                        'item_selected_start' => '<li class="active">',
                        'item_selected_end' => '</li>',
                        'block_end' => '</ul></nav>',
                    ],
                    // Pagination
                    'pagination' => [
                        'block_start' => '<div class="center"><ul class="pagination">',
                        'block_end' => '</ul></div>',
                        'page_current_template' => '<span>$page_num$</span>',
                        'page_item_before' => '<li>',
                        'page_item_after' => '</li>',
                        'page_item_current_before' => '<li class="active">',
                        'page_item_current_after' => '</li>',
                        'prev_text' => '<i class="fa fa-angle-double-left"></i>',
                        'next_text' => '<i class="fa fa-angle-double-right"></i>',
                    ],
                    // Form params for the forms below: login, register, lostpassword, activateinfo and msgform
                    'skin_form_before' => '<div class="panel panel-default skin-form">'
                                                                                . '<div class="panel-heading">'
                                                                                    . '<h3 class="panel-title">$form_title$</h3>'
                                                                                . '</div>'
                                                                                . '<div class="panel-body">',
                    'skin_form_after' => '</div></div>',
                    // Login
                    'display_form_messages' => true,
                    'form_title_login' => T_('Log in to your account') . '$form_links$',
                    'form_title_lostpass' => get_request_title() . '$form_links$',
                    'lostpass_page_class' => 'evo_panel__lostpass',
                    'login_form_inskin' => false,
                    'login_page_class' => 'evo_panel__login',
                    'login_page_before' => '<div class="$form_class$">',
                    'login_page_after' => '</div>',
                    'display_reg_link' => true,
                    'abort_link_position' => 'form_title',
                    'abort_link_text' => '<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
                    // Register
                    'register_page_before' => '<div class="evo_panel__register">',
                    'register_page_after' => '</div>',
                    'register_form_title' => T_('Register'),
                    'register_links_attrs' => '',
                    'register_use_placeholders' => true,
                    'register_field_width' => 252,
                    'register_disabled_page_before' => '<div class="evo_panel__register register-disabled">',
                    'register_disabled_page_after' => '</div>',
                    // Activate form
                    'activate_form_title' => T_('Account activation'),
                    'activate_page_before' => '<div class="evo_panel__activation">',
                    'activate_page_after' => '</div>',
                    // Search
                    'search_input_before' => '<div class="input-group">',
                    'search_input_after' => '',
                    'search_submit_before' => '<span class="input-group-btn">',
                    'search_submit_after' => '</span></div>',
                    'search_use_editor' => true,
                    'search_author_format' => 'login',
                    'search_cell_author_start' => '<p class="small text-muted">',
                    'search_cell_author_end' => '</p>',
                    'search_date_format' => 'F jS, Y',
                    // Front page
                    'featured_intro_before' => '<div class="jumbotron">',
                    'featured_intro_after' => '</div>',
                    // Form "Sending a message"
                    'msgform_form_title' => T_('Sending a message'),
                ];
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
     * Check if left navigation is visible for current page
     *
     * @return boolean TRUE
     */
    public function is_left_navigation_visible()
    {
        global $disp;

        if (in_array($disp, ['access_requires_login', 'access_denied'])) { // Display left navigation column on this page when at least one sidebar container is visible:
            return $this->is_visible_container('sidebar') || $this->is_visible_container('sidebar2');
        }

        // Display left navigation column only on these pages:
        return in_array($disp, ['front', 'posts', 'single', 'search', 'edit', 'edit_comment', 'catdir', 'search', '404']);
    }

    /**
     * Check if we can display a widget container
     *
     * @param string Widget container key: 'header', 'page_top', 'menu', 'sidebar', 'sidebar2', 'footer'
     * @return boolean TRUE to display
     */
    public function is_visible_container($container_key)
    {
        global $Collection, $Blog;

        if ($Blog->has_access()) {	// If current user has an access to this collection then don't restrict containers:
            return true;
        }

        // Get what containers are available for this skin when access is denied or requires login:
        $access = $this->get_setting('access_login_containers');

        return (! empty($access) && ! empty($access[$container_key]));
    }
}
