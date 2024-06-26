<?php
/**
 * This file implements the form to add user to organization.
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

$Form = new Form(null, 'orguser_checkchanges');

$Form->begin_form('fform');

$Form->add_crumb('organization');

$Form->hiddens_by_key(get_memorized('action')); // (this allows to come back to the right list order & page)

$User = null;
$Form->username('user_login', $User, TB_('Username'), '', '', [
    'required' => true,
]);

$Form->radio(
    'accepted',
    '1',
    [
        ['1', TB_('Accepted')],
        ['0', TB_('Not Accepted')],
    ],
    TB_('Membership'),
    true
);

$Form->text_input('role', '', 32, TB_('Role'), '', [
    'maxlength' => 255,
]);

$Form->text_input('priority', '', 32, TB_('Order'), '', [
    'maxlength' => 255,
    'type' => 'number',
]);

$buttons = [];
if (check_user_perm('orgs', 'edit', false, $edited_Organization)) {	// Display a button to update the poll question only if current user has a permission:
    $buttons[] = ['submit', 'actionArray[link_user]', TB_('Add'), 'SaveButton'];
}
$Form->end_form($buttons);
