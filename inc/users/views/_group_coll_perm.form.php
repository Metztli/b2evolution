<?php
/**
 * This file implements the UI view (+more :/) for the management of collection permissions for each group.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var Group
 */
global $edited_Group;

global $admin_url;

$Form = new Form(null, 'blogperm_checkchanges', 'post');
$Form->formclass = 'form-inline';

$title = ($action == 'edit' ? TB_('Editing group:') : TB_('Viewing group:'))
        . ' ' . $edited_Group->dget('name')
        . ' (' . TB_('ID') . ' ' . $edited_Group->ID . ')';

$Form->begin_form('fform', $title);

$Form->add_crumb('group');
$Form->hidden_ctrl();
$Form->hidden('tab', 'collection');
$Form->hidden('grp_ID', $edited_Group->ID);

$SQL = new SQL();
$SQL->SELECT($edited_Group->ID . ' AS grp_ID, blog_ID, blog_shortname, blog_advanced_perms, bloggroup_perm_item_propose, bloggroup_perm_poststatuses + 0 as perm_poststatuses, bloggroup_perm_item_type, bloggroup_perm_edit,'
    . 'bloggroup_can_be_assignee, bloggroup_workflow_status, bloggroup_workflow_user, bloggroup_workflow_priority,'
    . 'bloggroup_perm_delcmts, bloggroup_perm_recycle_owncmts, bloggroup_perm_vote_spam_cmts, bloggroup_perm_cmtstatuses + 0 as perm_cmtstatuses, bloggroup_perm_edit_cmt,'
    . 'bloggroup_perm_delpost, bloggroup_perm_edit_ts, bloggroup_perm_meta_comment, bloggroup_perm_cats,'
    . 'bloggroup_perm_properties, bloggroup_perm_admin, bloggroup_perm_media_upload,'
    . 'bloggroup_perm_media_browse, bloggroup_perm_media_change,'
    . 'IF( ( ' . ($edited_Group->get('perm_blogs') == 'viewall' || $edited_Group->get('perm_blogs') == 'editall' ? '1' : '0') . ' ), 1, bloggroup_ismember ) AS bloggroup_ismember');
$SQL->FROM('T_blogs');
$SQL->FROM_add('LEFT JOIN T_coll_group_perms ON ( blog_ID = bloggroup_blog_ID AND bloggroup_group_ID = ' . $edited_Group->ID . ' )');
$SQL->ORDER_BY('bloggroup_ismember DESC, blog_advanced_perms DESC, *, blog_name, blog_ID');

$keywords = param('keywords1', 'string', '', true);
if (! empty($keywords)) {
    $SQL->add_search_field('blog_name');
    $SQL->WHERE_kw_search($keywords, 'AND');
}

// Display wide layout:
?>

<div id="userlist_wide" class="clear">

<?php

$Results = new Results($SQL->get(), 'groupcoll_');

// Tell the Results class that we already have a form for this page:
$Results->Form = &$Form;

$Results->title = TB_('Collection permissions') . get_manual_link('group-collection-permissions');

$Results->filter_area = [
    'submit' => 'actionArray[filter]',
    'callback' => 'filter_collobjectlist',
    'url_ignore' => 'results_groupcoll_page,keywords1,keywords2',
];
$Results->register_filter_preset('all', TB_('All collections'), '?ctrl=groups&amp;action=edit&amp;tab=collection&amp;grp_ID=' . $edited_Group->ID);

// Initialize Results object:
colls_groups_perms_results($Results, [
    'type' => 'group',
    'object' => $edited_Group,
]);

$Results->display();

echo '</div>';

// Permission note:
// fp> TODO: link
echo '<p class="note center">' . TB_('Note: General group permissions may further restrict or extend any media folder permissions defined here.') . '</p>';

// Make a hidden list of all displayed users:
$BlogCache = &get_BlogCache();
$coll_IDs = [];
foreach ($Results->rows as $row) {
    $row_Blog = &$BlogCache->get_by_ID($row->blog_ID, false, false);
    if ($row_Blog && $row_Blog->get('advanced_perms')) {	// Only collections with enabled advanced permissions can be edited on this page:
        $coll_IDs[] = $row->blog_ID;
    }
}
$Form->hidden('coll_IDs', implode(',', $coll_IDs));

$Form->end_form([['submit', 'actionArray[update_perms]', TB_('Save Changes!'), 'SaveButton']]);

?>
