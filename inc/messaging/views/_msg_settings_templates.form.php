<?php
/**
 * This file is part of b2evolution - {@link http://b2evolution.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2016 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @package messaging
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $Settings;

$Form = new Form(null, 'msg_settings_templates');

$Form->begin_form('fform', '');

$Form->add_crumb('msgsettings');
$Form->hidden('ctrl', 'msgsettings');
$Form->hidden('action', 'update');
$Form->hidden('tab', get_param('tab'));

$Form->begin_fieldset(TB_('Welcome message after account activation') . get_manual_link('messages-welcome-after-account-activation'));

$Form->checkbox_input('welcomepm_enabled', $Settings->get('welcomepm_enabled'), TB_('Send Welcome PM'), [
    'note' => TB_('Check to automatically send a welcome message to users when they activate their account.'),
]);

$Form->checkbox_input('welcomepm_notag', $Settings->get('welcomepm_notag'), TB_('Only if no User Tag'), [
    'note' => TB_('Don\'t send the welcome message if the User Account already has any User Tag.'),
]);

$UserCache = &get_UserCache();
$User = $UserCache->get_by_login($Settings->get('welcomepm_from'));
if (! $User) {	// Use login of the current user if user login is incorrect:
    $User = $current_User;
}
$Form->username('welcomepm_from', $User, TB_('From'), TB_('User login') . '.');

$Form->text_input('welcomepm_title', $Settings->get('welcomepm_title'), 58, TB_('Title'), '', [
    'maxlength' => 5000,
]);

$Form->textarea_input('welcomepm_message', $Settings->get('welcomepm_message'), 15, TB_('Message'), [
    'cols' => 45,
]);

$Form->end_fieldset();

$Form->begin_fieldset(TB_('Info message to reporters after account deletion') . get_manual_link('messages-info-reporters-after-deletion'));

$Form->checkbox_input('reportpm_enabled', $Settings->get('reportpm_enabled'), /* TRANS: Send a Private Message to reporters when an account is deleted by a moderator */ TB_('Send delete notification'), [
    'note' => TB_('Check to allow sending a message to users who have reported an account whenever that account is deleted by a moderator.'),
]);

$User = $UserCache->get_by_login($Settings->get('reportpm_from'));
if (! $User) {	// Use login of the current user if user login is incorrect:
    $User = $current_User;
}
$Form->username('reportpm_from', $User, TB_('From'), TB_('User login') . '.');

$Form->text_input('reportpm_title', $Settings->get('reportpm_title'), 58, TB_('Title'), '', [
    'maxlength' => 5000,
]);

$Form->textarea_input('reportpm_message', $Settings->get('reportpm_message'), 15, TB_('Message'), [
    'cols' => 45,
]);

$Form->end_fieldset();

$Form->buttons([['submit', 'submit', TB_('Save Changes!'), 'SaveButton']]);

$Form->end_form();
