<?php

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var instance of GeneralSettings class
 */
global $Settings;

check_user_perm('users', 'view', true);

$Form = new Form(null, 'usersettings_checkchanges');

$Form->begin_form('fform', '');

$Form->add_crumb('usersettings');
$Form->hidden('ctrl', 'usersettings');
$Form->hidden('action', 'update');

$Form->begin_fieldset(TB_('Session Settings') . get_manual_link('session-settings'));

$Form->text_input('redirect_to_after_login', $Settings->get('redirect_to_after_login'), 60, TB_('After login, redirect to'), TB_('Users will be redirected there upon successful login, unless they are in process of doing something.'), [
    'maxlength' => null,
]);

// fp>TODO: enhance UI with a general Form method for Days:Hours:Minutes:Seconds

$Form->duration_input(
    'timeout_sessions',
    $Settings->get('timeout_sessions'),
    TB_('Session timeout'),
    'months',
    'seconds',
    [
        'minutes_step' => 1,
        'required' => true,
        'note' => TB_('Logged-in users won\'t have to log in again for this long. Note: this sets the duration of the Session Cookie + the Session will stay in the DB for this long.'),
    ]
);
// $Form->text_input( 'timeout_sessions', $Settings->get('timeout_sessions'), 9, TB_('Session timeout'), TB_('seconds. How long can a user stay inactive before automatic logout?'), array( 'required'=>true) );

// fp>TODO: It may make sense to have a different (smaller) timeout for sessions with no logged user.
// fp>This might reduce the size of the Sessions table. But this needs to be checked against the hit logging feature.

$Form->duration_input(
    'timeout_online',
    $Settings->get('timeout_online'),
    TB_('Online/Offline timeout'),
    'hours',
    'seconds',
    [
        'minutes_step' => 1,
        'required' => true,
        'note' => TB_('If the user stays inactive for this long, we will no longer display him as "online" and we will start sending him email notifications when things happen while he is away.'),
    ]
);
$Form->end_fieldset();

$Form->begin_fieldset(TB_('Visit Tracking') . get_manual_link('visit-tracking'));

$Form->checkbox_input('enable_visit_tracking', $Settings->get('enable_visit_tracking', false), TB_('Enable visit tracking'), [
    'note' => TB_('Check this to enable "Who visited my profle?"'),
]);

$Form->end_fieldset();

$Form->begin_fieldset(TB_('User latitude') . get_manual_link('user-profile-latitude-settings'));

$Form->checkbox_input('allow_avatars', $Settings->get('allow_avatars', true), TB_('Allow profile pictures'), [
    'note' => TB_('Allow users to upload profile pictures.'),
]);

$Form->text_input('uset_min_picture_size', $Settings->get('min_picture_size'), 5, TB_('Minimum picture size'), '', [
    'note' => TB_('pixels (width and height)'),
]);

$name_editing_options = [
    ['edited-user', TB_('Can be edited by user')],
    ['edited-user-required', TB_('Can be edited by user + required')],
    ['edited-admin', TB_('Can be edited by admins only')],
    ['hidden', TB_('Hidden')],
];

$Form->radio('uset_nickname_editing', $Settings->get('nickname_editing'), $name_editing_options, TB_('Nickname'), true);

$Form->radio('uset_firstname_editing', $Settings->get('firstname_editing'), $name_editing_options, TB_('First name'), true);

$Form->radio('uset_lastname_editing', $Settings->get('lastname_editing'), $name_editing_options, TB_('Last name'), true);

$location_options = [
    ['required', TB_('Required')],
    ['optional', TB_('Optional')],
    ['hidden', TB_('Hidden')],
];

$Form->radio('uset_location_country', $Settings->get('location_country'), $location_options, TB_('Country'));

$Form->radio('uset_location_region', $Settings->get('location_region'), $location_options, TB_('Region'));

$Form->radio('uset_location_subregion', $Settings->get('location_subregion'), $location_options, TB_('Sub-region'));

$Form->radio('uset_location_city', $Settings->get('location_city'), $location_options, TB_('City'));

$birthday_options = [
    ['required', TB_('Required')],
    ['optional', TB_('Optional')],
    ['hidden', TB_('Hidden')],
];

$Form->radio('uset_birthday_year', $Settings->get('birthday_year'), $birthday_options, TB_('Birthday Year'));

$Form->radio('uset_birthday_month', $Settings->get('birthday_month'), $birthday_options, TB_('Birthday Month'));

$Form->radio('uset_birthday_day', $Settings->get('birthday_day'), $birthday_options, TB_('Birthday Day'));

$Form->radio('uset_self_selected_age_group', $Settings->get('self_selected_age_group'), $birthday_options, TB_('Self-selected Age Group'));

$Form->text_input('uset_minimum_age', $Settings->get('minimum_age'), 3, TB_('Minimum age'), '', [
    'input_suffix' => ' ' . TB_('years old.'),
]);

$Form->radio('uset_multiple_sessions', $Settings->get('multiple_sessions'), [
    ['never', TB_('Never allow')],
    ['adminset_default_no', TB_('Let admins decide for each user, default to "no" for new users')],
    ['userset_default_no', TB_('Let users decide, default to "no" for new users')],
    ['userset_default_yes', TB_('Let users decide, default to "yes" for new users')],
    ['adminset_default_yes', TB_('Let admins decide for each user, default to "yes" for new users')],
    ['always', TB_('Always allow')],
], TB_('Multiple sessions'), true);

$Form->radio('uset_emails_msgform', $Settings->get('emails_msgform'), [
    ['never', TB_('Never allow')],
    ['adminset', TB_('Let admins decide for each user, default set on Registration tab')],
    ['userset', TB_('Let users decide, default set on Registration tab')],
], TB_('Receiving emails through a message form'), true);

$Form->end_fieldset();

if (check_user_perm('users', 'edit')) {
    $Form->buttons([['submit', 'submit', TB_('Save Changes!'), 'SaveButton']]);
}

$Form->end_form();


load_funcs('regional/model/_regional.funcs.php');
echo_regional_required_js('uset_location_');
