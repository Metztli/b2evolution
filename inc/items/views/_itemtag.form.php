<?php
/**
 * This file display the tag form
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
 * @var ItemTag
 */
global $edited_ItemTag;

global $action, $admin_url, $display_merge_tags_form, $return_to;

if (! empty($edited_ItemTag->merge_tag_ID)) { // Display a for to confirm merge the tag to other one
    $Form = new Form(null, 'itemtagmerge_checkchanges', 'post', 'compact');

    $Form->begin_form('fform', TB_('Merge tags?'), [
        'formstart_class' => 'panel-danger',
    ]);
    $Form->hidden('tag_ID', $edited_ItemTag->merge_tag_ID);
    $Form->hidden('old_tag_ID', $edited_ItemTag->ID);
    $Form->add_crumb('tag');
    $Form->hiddens_by_key(get_memorized('action,tag_ID'));

    echo '<p>' . $edited_ItemTag->merge_message . '</p>';

    $Form->button(['submit', 'actionArray[merge_confirm]', TB_('Confirm'), 'SaveButton btn-danger']);
    $Form->button(['submit', 'actionArray[merge_cancel]', TB_('Cancel'), 'SaveButton btn-default']);

    $Form->end_form();
}

// Determine if we are creating or updating...
$creating = is_create_action($action);

$Form = new Form(null, 'itemtag_checkchanges', 'post', 'compact');

$Form->global_icon(TB_('Cancel editing') . '!', 'close', ($return_to ? $return_to : $admin_url . '?ctrl=itemtags'));

$Form->begin_form('fform', ($creating ? TB_('New Tag') : /* TRANS: noun */ TB_('Tag')) . get_manual_link('item-tag-form'));

$Form->add_crumb('tag');
$Form->hidden('action', $creating ? 'create' : 'update');
$Form->hiddens_by_key(get_memorized('action' . ($creating ? ',tag_ID' : '')));

$Form->text_input('tag_name', $edited_ItemTag->get('name'), 50, /* TRANS: noun */ TB_('Tag'), '', [
    'maxlength' => 255,
    'required' => true,
]);

$Form->end_form([['submit', 'submit', ($creating ? TB_('Record') : TB_('Save Changes!')), 'SaveButton']]);


// Item list with this tag:
if ($edited_ItemTag->ID > 0) {
    $SQL = new SQL();
    $SQL->SELECT('T_items__item.*, blog_shortname');
    $SQL->FROM('T_items__itemtag');
    $SQL->FROM_add('INNER JOIN T_items__item ON itag_itm_ID = post_ID');
    $SQL->FROM_add('INNER JOIN T_categories ON post_main_cat_ID = cat_ID');
    $SQL->FROM_add('INNER JOIN T_blogs ON cat_blog_ID = blog_ID');
    $SQL->WHERE('itag_tag_ID = ' . $DB->quote($edited_ItemTag->ID));

    // Create result set:
    $Results = new Results($SQL->get(), 'tagitem_', 'A');

    $Results->title = TB_('Posts that have this tag') . ' (' . $Results->get_total_rows() . ')';
    $Results->Cache = get_ItemCache();

    $Results->cols[] = [
        'th' => TB_('Post ID'),
        'th_class' => 'shrinkwrap',
        'td_class' => 'shrinkwrap',
        'order' => 'post_ID',
        'td' => '$post_ID$',
    ];

    $Results->cols[] = [
        'th' => TB_('Collection'),
        'order' => 'blog_shortname',
        'td' => '$blog_shortname$',
    ];

    $Results->cols[] = [
        'th' => TB_('Post title'),
        'order' => 'post_title',
        'td' => '<a href="@get_permanent_url()@">$post_title$</a>',
    ];

    function tagitem_edit_actions($Item)
    {
        global $edited_ItemTag;

        // Display the edit icon if current user has the rights:
        $r = $Item->get_edit_link([
            'before' => '',
            'after' => ' ',
            'text' => get_icon('edit'),
            'title' => '#',
            'class' => '',
        ]);

        if (check_user_perm('item_post!CURSTATUS', 'edit', false, $Item)) { // Display the unlink icon if current user has the rights:
            $r .= action_icon(
                TB_('Unlink this tag from post!'),
                'unlink',
                regenerate_url('tag_ID,action,tag_filter', 'tag_ID=' . $edited_ItemTag->ID . '&amp;item_ID=' . $Item->ID . '&amp;action=unlink&amp;return_to=' . urlencode(regenerate_url('action', '', '', '&')) . '&amp;' . url_crumb('tag')),
                null,
                null,
                null,
                [
                    'onclick' => 'return confirm(\'' . format_to_output(sprintf(
                        TS_('Are you sure you want to remove the tag "%s" from "%s"?'),
                        $edited_ItemTag->dget('name'),
                        $Item->dget('title')
                    ) . '\');', 'htmlattr'),
                ]
            );
        }

        return $r;
    }
    $Results->cols[] = [
        'th' => TB_('Actions'),
        'th_class' => 'shrinkwrap',
        'td_class' => 'shrinkwrap',
        'td' => '%tagitem_edit_actions( {Obj} )%',
    ];

    $Results->display();
}
