<?php
/**
 * This file display the automation form
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


global $edited_Automation, $admin_url;

$step = param('step', 'string', '', true);

$SQL = new SQL('Get all users queued for automation #' . $edited_Automation->ID);
$SQL->SELECT('aust_autm_ID, aust_user_ID, aust_next_step_ID, aust_next_exec_ts, user_login, step_ID, IF( step_ID IS NULL, 2147483648, step_order ) AS step_order, step_label, step_type, step_info');
$SQL->FROM('T_automation__user_state');
$SQL->FROM_add('INNER JOIN T_users ON user_ID = aust_user_ID');
$SQL->FROM_add('LEFT JOIN T_automation__step ON step_ID = aust_next_step_ID');
$SQL->WHERE('aust_autm_ID = ' . $edited_Automation->ID);

$count_SQL = new SQL('Get a count of users queued for automation #' . $edited_Automation->ID);
$count_SQL->SELECT('COUNT( aust_user_ID )');
$count_SQL->FROM('T_automation__user_state');
$count_SQL->WHERE('aust_autm_ID = ' . $edited_Automation->ID);

// Filter by step:
if (strtolower($step) === 'finished') {	// Get only the users who finished this automation:
    $SQL->WHERE_and($filter_step = 'aust_next_step_ID IS NULL');
    $count_SQL->WHERE_and($filter_step);
} elseif (($step = intval($step)) > 0) {	// Filter by step:
    $SQL->WHERE_and($filter_step = 'step_order = ' . $DB->quote($step));
    $count_SQL->FROM_add('LEFT JOIN T_automation__step ON step_ID = aust_next_step_ID');
    $count_SQL->WHERE_and($filter_step);
    set_param('step', $step);
} elseif ($step !== '') {	// Unset wrong step values:
    set_param('step', '');
}

$Results = new Results($SQL->get(), 'aust_', '-A', null, $count_SQL->get());

$Results->global_icon(T_('Add users...'), 'new', $admin_url . '?ctrl=users', T_('Add users...'), 3, 4);

$Results->title = T_('Users queued') . get_manual_link('automation-users-queued');


/**
 * Callback to add filters on top of the result set
 *
 * @param Form
 */
function filter_automation_users(&$Form)
{
    $Form->text_input('step', get_param('step'), 10, T_('Step'), '');
}
$Results->filter_area = [
    'callback' => 'filter_automation_users',
    'url_ignore' => 'step,results_aust_page',
];

$Results->register_filter_preset('all', T_('All'), '?ctrl=automations&amp;action=edit&amp;tab=users&amp;autm_ID=' . $edited_Automation->ID);
$Results->register_filter_preset('finished', T_('Finished'), '?ctrl=automations&amp;action=edit&amp;tab=users&amp;autm_ID=' . $edited_Automation->ID . '&amp;step=finished');

$Results->cols[] = [
    'th' => T_('User'),
    'order' => 'aust_user_ID',
    'td' => '%get_user_identity_link( "", #aust_user_ID# )%',
    'th_class' => 'shrinkwrap',
];
$Results->cols[] = [
    'th' => T_('Step'),
    'order' => 'step_order, user_login',
    'td' => '%autm_td_users_step( #aust_next_step_ID#, #step_order#, #step_label#, #step_type#, #step_info# )%',
];

$Results->cols[] = [
    'th' => T_('Next execution time'),
    'order' => 'aust_next_exec_ts',
    'td' => '%mysql2localedatetime_spans( #aust_next_exec_ts# )%',
    'th_class' => 'shrinkwrap',
    'td_class' => 'timestamp',
];

$Results->cols[] = [
    'th' => T_('Actions'),
    'td' => '%autm_td_users_actions( #aust_autm_ID#, #aust_user_ID#, #user_login#, #step_ID# )%',
    'th_class' => 'shrinkwrap',
    'td_class' => 'shrinkwrap',
];

$Results->display(null, 'session');

// Init JS for form to requeue automation:
echo_requeue_automation_js();
