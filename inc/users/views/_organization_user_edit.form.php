<?php
/**
 * This file implements the form to edit user in organization.
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

$org_ID = get_param('org_ID');
$UserCache = &get_UserCache();
$edited_User = &$UserCache->get_by_ID(param('user_ID', 'integer'));
$org_data = $edited_User->get_organizations_data();

$Form = new Form(null, 'orguser_editmembership');

$Form->begin_form('fform');

$Form->add_crumb('organization');

$Form->hiddens_by_key(get_memorized('action')); // (this allows to come back to the right list order & page)
$Form->hidden('edit_mode', true); // this allows the controller to determine if it an edit to the membership information

$Form->info_field(TB_('Username'), $edited_User->get('login'));
$Form->hidden('user_login', $edited_User->get('login'));
$Form->radio(
    'accepted',
    $org_data[$org_ID]['accepted'],
    [
        ['1', TB_('Accepted')],
        ['0', TB_('Not Accepted')],
    ],
    TB_('Membership'),
    true
);

if (($edited_Organization->owner_user_ID == $current_User->ID) || ($edited_Organization->perm_role == 'owner and member' && $org_data[$org_ID]['accepted'])) {	// Display edit field if current user has a permission to edit role:
    $Form->text_input('role', $org_data[$org_ID]['role'], 32, TB_('Role'), '', [
        'maxlength' => 255,
    ]);
} else {	// Otherwise display info field with role value:
    $Form->info_field(TB_('Role'), $org_data[$org_ID]['role']);
}

if ($edited_Organization->owner_user_ID == $current_User->ID || check_user_perm('orgs', 'edit', false, $edited_Organization)) {	// Display edit field if current user has a permission to edit order:
    $Form->text_input('priority', $org_data[$org_ID]['priority'], 10, TB_('Order'), '', [
        'type' => 'number',
        'min' => -2147483648,
        'max' => 2147483647,
    ]);
} else {	// Otherwise display info field with role value:
    $Form->info_field(TB_('Order'), $org_data[$org_ID]['priority']);
}

$buttons = [];
if (check_user_perm('orgs', 'edit', false, $edited_Organization)) {	// Display a button to update the poll question only if current user has a permission:
    $buttons[] = ['submit', 'actionArray[link_user]', TB_('Edit'), 'SaveButton'];
}
$Form->end_form($buttons);
