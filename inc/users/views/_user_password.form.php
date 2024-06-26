<?php
/**
 * This file implements the UI view for the user properties.
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
 * @var instance of GeneralSettings class
 */
global $Settings;
/**
 * @var instance of User class
 */
global $edited_User;
/**
 * @var current action
 */
global $action;
/**
 * @var user permission, if user is only allowed to edit his profile
 */
global $user_profile_only;
/**
 * @var the action destination of the form (NULL for pagenow)
 */
global $form_action;
/**
 * @var instance of User class
 */
global $current_User;

global $Session;

// check if reqID exists. If exists it means that this form is displayed because a password change request by email.
$reqID = param('reqID', 'string', '');

// Default params:
$default_params = [
    'skin_form_params' => [],
    'form_class_user_pass' => 'bComment',
    'display_abandon_link' => true,
    'button_class' => '',
    'form_button_action' => 'update',
    'form_hidden_crumb' => 'user',
    'form_hidden_reqID' => $reqID,
];

if (isset($params)) { // Merge with default params
    $params = array_merge($default_params, $params);
} else { // Use a default params
    $params = $default_params;
}

// ------------------- PREV/NEXT USER LINKS -------------------
user_prevnext_links([
    'user_tab' => 'pwdchange',
]);
// ------------- END OF PREV/NEXT USER LINKS -------------------

$Form = new Form($form_action, 'user_checkchanges');

$Form->switch_template_parts($params['skin_form_params']);

if (! $user_profile_only) {
    echo_user_actions($Form, $edited_User, $action);
}

$is_admin = is_admin_page();
if ($is_admin) {
    $form_text_title = '<span class="nowrap">' . TB_('Change password') . '</span>' . get_manual_link('user-password-tab'); // used for js confirmation message on leave the changed form
    $form_title = get_usertab_header($edited_User, 'pwdchange', $form_text_title);
    $form_class = 'fform';
    $Form->title_fmt = '$title$';
} else {
    $form_title = '';
    $form_class = $params['form_class_user_pass'];
}

$has_full_access = check_user_perm('users', 'edit');


$Form->begin_form($form_class, $form_title, [
    'title' => (isset($form_text_title) ? $form_text_title : $form_title),
]);

$Form->add_crumb($params['form_hidden_crumb']);
$Form->hidden_ctrl();
$Form->hidden('user_tab', 'pwdchange');
$Form->hidden('password_form', '1');
$Form->hidden('reqID', $reqID);

$Form->hidden('user_ID', $edited_User->ID);
$Form->hidden('edited_user_login', $edited_User->login);
if (isset($Blog)) {
    $Form->hidden('blog', $Blog->ID);
}

/***************  Password  **************/

$Form->begin_fieldset($is_admin ? TB_('Password') . get_manual_link('user-password-tab') : '', [
    'class' => 'fieldset clear',
]);

// current password is not required:
//   - password change requested by email
//   - password has not been set yet(email capture/quick registration)
if ((empty($reqID) || $reqID != $Session->get('core.changepwd.request_id')) &&
        ($edited_User->get('pass_driver') != 'nopass')) {
    if (! $has_full_access || $edited_User->ID == $current_User->ID) { // Current user has no full access or editing his own pasword
        $Form->password_input('current_user_pass', '', 20, TB_('Current password'), [
            'maxlength' => 50,
            'required' => ($edited_User->ID == 0),
            'autocomplete' => 'off',
            'style' => 'width:163px',
        ]);
    } else { // Ask password of current admin
        $Form->password_input('current_user_pass', '', 20, TB_('Enter your current password'), [
            'maxlength' => 50,
            'required' => ($edited_User->ID == 0),
            'autocomplete' => 'off',
            'style' => 'width:163px',
            'note' => sprintf(TB_('We ask for <b>your</b> (%s) <i>current</i> password as an additional security measure.'), $current_User->get('login')),
        ]);
    }
}
$Form->password_input('edited_user_pass1', '', 20, TB_('New password'), [
    'note' => sprintf(TB_('Minimum length: %d characters.'), $Settings->get('user_minpwdlen')),
    'maxlength' => 50,
    'required' => ($edited_User->ID == 0),
    'autocomplete' => 'off',
]);
$Form->password_input('edited_user_pass2', '', 20, TB_('Confirm new password'), [
    'maxlength' => 50,
    'required' => ($edited_User->ID == 0),
    'autocomplete' => 'off',
    'note' => '<span id="pass2_status" class="field_error"></span>',
]);

$Form->end_fieldset();

/***************  Buttons  **************/

if ($action != 'view') { // Edit buttons
    $Form->buttons([['', 'actionArray[' . $params['form_button_action'] . ']', TB_('Change password') . '!', 'SaveButton' . $params['button_class']]]);
}

if ($params['display_abandon_link']) {	// Display a link to go away from this form:
    $Form->info('', '<div><a href="' . get_user_settings_url('profile', $edited_User->ID) . '">' . TB_('Abandon password change') . '</a></div>');
}


$Form->end_form();

// Display javascript password strength indicator bar
display_password_indicator([
    'pass1_id' => 'edited_user_pass1',
    'pass2_id' => 'edited_user_pass2',
    'login_id' => 'edited_user_login',
    'field_width' => 165,
]);

// Display javascript code to edit password:
display_password_js_edit();
