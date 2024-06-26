<?php
/**
 * This file implements the UI view for the user organization properties.
 *
 * Called by {@link b2users.php}
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2004-2006 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * @var Organization
 */
global $edited_Organization;

// Determine if we are creating or updating...
global $action;

$creating = is_create_action($action);

$Form = new Form(null, 'organization_checkchanges', 'post', 'compact');

if (! $creating) {
    $Form->global_icon(TB_('Delete this organization!'), 'delete', regenerate_url('action', 'action=delete&amp;' . url_crumb('organization')));
}
$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action,org_ID'));

$Form->begin_form('fform', ($creating ? TB_('New organization') : TB_('Organization')) . get_manual_link('organization-form'));

$Form->add_crumb('organization');

$Form->hiddens_by_key(get_memorized('action')); // (this allows to come back to the right list order & page)

if (check_user_perm('orgs', 'edit')) {	// Allow to change an owner if current user has a permission to edit all polls:
    $Form->username('org_owner_login', $edited_Organization->get_owner_User(), TB_('Owner'), '', '', [
        'required' => true,
    ]);
} else {	// Current user has no permission to edit a poll owner, Display the owner as info field:
    $Form->info(TB_('Owner'), get_user_identity_link(null, $edited_Organization->owner_user_ID));
}

$Form->text_input('org_name', $edited_Organization->name, 32, TB_('Name'), '', [
    'maxlength' => 255,
    'required' => true,
]);

$Form->text_input('org_url', $edited_Organization->url, 32, TB_('Url'), '', [
    'maxlength' => 2000,
]);

$Form->radio(
    'org_accept',
    $edited_Organization->get('accept'),
    [
        ['yes', TB_('Yes, accept immediately')],
        ['owner', TB_('Yes, owner must accept them')],
        ['no', TB_('No')],
    ],
    TB_('Let members join'),
    true
);

$Form->radio(
    'org_perm_role',
    $edited_Organization->get('perm_role'),
    [
        ['owner and member', TB_('can be edited by user and organization owner')],
        ['owner', TB_('can be edited by organization owner only')],
    ],
    TB_('Role in organization'),
    true
);

$buttons = [];
if (check_user_perm('orgs', 'edit', false, $edited_Organization)) {	// Display a button to update the poll question only if current user has a permission:
    if ($creating) {
        $buttons[] = ['submit', 'actionArray[create]', TB_('Record'), 'SaveButton'];
        $buttons[] = ['submit', 'actionArray[create_new]', TB_('Record, then Create New'), 'SaveButton'];
        $buttons[] = ['submit', 'actionArray[create_copy]', TB_('Record, then Create Similar'), 'SaveButton'];
    } else {
        $buttons[] = ['submit', 'actionArray[update]', TB_('Save Changes!'), 'SaveButton'];
    }
}
$Form->end_form($buttons);

if ($edited_Organization->ID > 0) {	// Display members of this organization:
    users_results_block([
        'org_ID' => $edited_Organization->ID,
        'filterset_name' => 'orgusr_' . $edited_Organization->ID,
        'results_param_prefix' => 'orgusr_',
        'results_title' => TB_('Members of this organization') . get_manual_link('organization-members'),
        'results_order' => '/uorg_accepted/D',
        'page_url' => get_dispctrl_url('organizations', 'action=edit&amp;org_ID=' . $edited_Organization->ID),
        'display_orgstatus' => true,
        'display_role' => true,
        'display_priority' => true,
        'display_ID' => false,
        'display_btn_adduser' => false,
        'display_btn_addgroup' => false,
        'display_btn_adduserorg' => true,
        'display_blogs' => false,
        'display_source' => false,
        'display_regdate' => false,
        'display_regcountry' => false,
        'display_update' => false,
        'display_lastvisit' => false,
        'display_contact' => false,
        'display_reported' => false,
        'display_group' => false,
        'display_level' => false,
        'display_status' => false,
        'display_actions' => false,
        'display_org_actions' => true,
        'display_newsletter' => false,
    ]);
}

// AJAX changing of an accept status of organizations for each user
echo_user_organization_js();
