<?php
/**
 * This file display the slugs list
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 * Parts of this file are copyright (c)2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var Slug
 */
global $Sug, $admin_url;

$SQL = new SQL();

$SQL->SELECT('*, post_title AS target_title'); // select target_title for sorting
$SQL->FROM('T_slug LEFT OUTER JOIN T_items__item ON slug_itm_ID = post_ID');

// filters
$list_is_filtered = false;
if (get_param('slug_filter')) { // add slug_title filter
    $like = $DB->escape(strtolower(get_param('slug_filter')));
    $SQL->WHERE_and('(
		LOWER(slug_title) LIKE "%' . $like . '%"
		OR LOWER(post_title) LIKE "%' . $like . '%")');
    $list_is_filtered = true;
}
if ($filter_type = get_param('slug_type')) { // add filter for post type
    $SQL->WHERE_and('slug_type = "' . $DB->escape(get_param('slug_ftype')) . '"');
    $list_is_filtered = true;
}
if ($filter_item_ID = get_param('slug_item_ID')) { // add filter for item ID
    if (is_number($filter_item_ID)) {
        $SQL->WHERE_and('slug_itm_ID = ' . $DB->quote($filter_item_ID));
        $list_is_filtered = true;
    }
}

// Create result set:
$Results = new Results($SQL->get(), 'slug_', 'A');

$Results->title = T_('Slugs') . ' (' . $Results->get_total_rows() . ')' . get_manual_link('slugs-list');
$Results->Cache = get_SlugCache();

/**
 * Callback to add filters on top of the result set
 *
 * @param Form
 */
function filter_slugs(&$Form)
{
    $Form->text_input('slug_filter', get_param('slug_filter'), 24, T_('Slug'), '', [
        'maxlength' => 253,
    ]);

    $item_ID_filter_note = '';
    if ($filter_item_ID = get_param('slug_item_ID')) { // check item_Id filter. It must be a number
        if (! is_number($filter_item_ID)) { // It is not a number
            $item_ID_filter_note = T_('Must be a number');
        }
    }
    $Form->text_input('slug_item_ID', $filter_item_ID, 9, T_('Item ID'), $item_ID_filter_note, [
        'maxlength' => 9,
    ]);
}
$Results->filter_area = [
    'callback' => 'filter_slugs',
    'url_ignore' => 'slug_filter,results_slug_page',
];
$Results->register_filter_preset('all', T_('All'), '?ctrl=slugs');

function get_slug_link($Slug)
{
    global $admin_url;
    if (check_user_perm('slugs', 'edit')) {
        return '<strong><a href="' . $admin_url . '?ctrl=slugs&amp;slug_ID=' . $Slug->ID . '&amp;action=edit">' . $Slug->get('title') . '</a></strong>';
    } else {
        return '<strong>' . $Slug->get('title') . '</strong>';
    }
}
$Results->cols[] = [
    'th' => T_('Slug'),
    'th_class' => 'small',
    'td_class' => 'small',
    'order' => 'slug_title',
    'td' => '%get_slug_link({Obj})%',
];

/** Get TinyURL
 *
 * @param Slug Slug object
 * @return string
 */
function get_slug_type($Slug)
{
    switch ($Slug->type) {
        case 'item':
            // case other: (add here)
            $target = &$Slug->get_object();
            if (empty($target)) {	// The Item was not found... (it has probably been deleted):
                return '<i>' . T_('(missing)') . '</i>';
            }

            if ($target->urltitle == $Slug->title) {
                return TB_('Canonical');
            } elseif ($target->tiny_slug_ID == $Slug->ID) {
                return TB_('Tiny');
            } else {
                $slugs = explode(',', $target->get_slugs(','));
                if (in_array($Slug->title, $slugs)) {
                    return TB_('Extra');
                }
            }
            break;

        default:
            return /* TRANS: "Not Available" */ T_('N/A');
    }
}
$Results->cols[] = [
    'th' => TB_('Slug type'),
    'th_class' => 'shrinkwrap',
    'td_class' => 'shrinkwrap',
    'td' => '%get_slug_type({Obj})%',
];


/** Get TinyURL
 *
 * @param Slug Slug object
 * @return string
 */
function get_tinyurl($Slug)
{
    switch ($Slug->type) {
        case 'item':
            // case other: (add here)
            $target = &$Slug->get_object();
            if (empty($target)) {	// The Item was not found... (it has probably been deleted):
                return '<i>' . T_('(missing)') . '</i>';
            }

            $use_tinyslug = $target->tiny_slug_ID == $Slug->ID;
            $Collection = $Blog = &$target->get_Blog();

            if ($Blog->get_setting('tinyurl_type') == 'advanced') {
                $tinyurl = url_add_tail($Blog->get_setting('tinyurl_domain'), '/' . $Slug->title);
            } else {
                $tinyurl = url_add_tail($Blog->get('url'), '/' . $Slug->title);
            }

            $title = T_('This is a tinyurl you can copy/paste into twitter, emails and other places where you need a short link to this post');

            return sprintf('<a href="%s" title="%s">%s</a>', $tinyurl, $title, $tinyurl);

        default:
            return /* TRANS: "Not Available" */ T_('N/A');
    }
}
$Results->cols[] = [
    'th' => T_('Tiny URL'),
    'th_class' => 'small',
    'td_class' => 'small',
    'td' => '%get_tinyurl({Obj})%',
];


$Results->cols[] = [
    'th' => T_('Type'),
    'th_class' => 'small',
    'order' => 'slug_type',
    'td' => '$slug_type$',
    'td_class' => 'shrinkwrap small',
];


/**
 * Get a link to the target object
 *
 * @param Slug Slug object
 * @return string
 */
function get_target_ID($Slug)
{
    switch ($Slug->type) {
        case 'item':
            $r = $Slug->itm_ID;
            break;

        default:
            $r = /* TRANS: "Not Available" */ T_('N/A');
    }

    if (is_null($r)) {
        $r = 'null';
    }

    return $r;
}
$Results->cols[] = [
    'th' => T_('Target'),
    'th_class' => 'small',
    'order' => 'target_title',
    'td' => '%get_target_ID({Obj})%',
    'td_class' => 'shrinkwrap small',
];


/**
 * Get a link to the target object
 *
 * @param Slug Slug object
 * @return string target link if exists, target title otherwise
 */
function get_target_coll($Slug)
{
    switch ($Slug->type) {
        case 'item':
            // case other: (add here)
            $target = &$Slug->get_object();
            if (empty($target)) {	// The Item was not found... (it has probably been deleted):
                return '<i>' . T_('(missing)') . '</i>';
            }

            $allow_edit = false;
            $allow_view = false;
            switch ($Slug->get('type')) {
                case 'item':
                    $allow_edit = check_user_perm('item_post!CURSTATUS', 'edit', false, $target);
                    $allow_view = check_user_perm('item_post!CURSTATUS', 'view', false, $target);
                    break;
                    // Other types permission check write here
            }

            // permanent link to object
            $coll = action_icon(T_('Permanent link to full entry'), 'permalink', $Slug->get_url_to_object('public_view'));

            if ($allow_edit) { // edit object link
                $coll .= ' ' . action_icon(
                    sprintf(T_('Edit this %s...'), $Slug->get('type')),
                    'properties',
                    $Slug->get_url_to_object('edit')
                );
            }
            if ($allow_view) { // view object link
                $coll .= ' ' . $Slug->get_link_to_object();
            } else { // Display just the title (If there is no object title need to change this)
                $coll .= ' ' . $target->get('title');
            }
            return $coll;

        default:
            return /* TRANS: "Not Available" */ T_('N/A');
    }
}
$Results->cols[] = [
    'th' => T_('Target'),
    'th_class' => 'small',
    'order' => 'target_title',
    'td' => '%get_target_coll({Obj})%',
    'td_class' => 'small left',
];

if (check_user_perm('slugs', 'edit')) {
    $Results->cols[] = [
        'th' => T_('Actions'),
        'th_class' => 'shrinkwrap small',
        'td_class' => 'shrinkwrap',
        'td' => action_icon(
            TS_('Edit this slug...'),
            'properties',
            $admin_url . '?ctrl=slugs&amp;slug_ID=$slug_ID$&amp;action=edit'
        )
                 . action_icon(
                     T_('Delete this slug!'),
                     'delete',
                     regenerate_url('slug_ID,action,slug_filter', 'slug_ID=$slug_ID$&amp;action=delete&amp;' . url_crumb('slug'))
                 ),
    ];

    $Results->global_icon(T_('Add a new slug...'), 'new', regenerate_url('action', 'action=new'), T_('New slug') . ' &raquo;', 3, 4, [
        'class' => 'action_icon btn-primary',
    ]);
}

$Results->display();
