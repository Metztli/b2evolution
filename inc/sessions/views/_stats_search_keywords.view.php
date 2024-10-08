<?php
/**
 * This file implements the UI view for the referering searches stats.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * View funcs
 */
require_once __DIR__ . '/_stats_view.funcs.php';

load_class('/sessions/model/_goal.class.php', 'Goal');
load_funcs('/cron/_cron.funcs.php');

global $blog, $sec_ID, $admin_url, $rsc_url, $goal_ID, $localtimenow;
global $datestartinput, $datestart, $datestopinput, $datestop, $keyword;

if (param_date('datestartinput', T_('Invalid date'), false, null) !== null) { // We have a user provided localized date:
    memorize_param('datestart', 'string', null, trim(form_date($datestartinput)));
    memorize_param('datestartinput', 'string', null, empty($datestartinput) ? null : date(locale_datefmt(), strtotime($datestartinput)));
} else { // We may have an automated param transmission date:
    param('datestart', 'string', '', true);
}
if (param_date('datestopinput', T_('Invalid date'), false, null) !== null) { // We have a user provided localized date:
    memorize_param('datestop', 'string', null, trim(form_date($datestopinput)));
    memorize_param('datestopinput', 'string', null, empty($datestopinput) ? null : date(locale_datefmt(), strtotime($datestopinput)));
} else { // We may have an automated param transmission date:
    param('datestop', 'string', '', true);
}

if (param('keyword', 'string', null) !== null) {	// We have a user provided keyword:
    memorize_param('keyword', 'string', null, get_param('keyword'));
}

if (check_user_perm('stats', 'view')) {	// Permission to view stats for ALL blogs:
    param('goal_ID', 'integer', 0, true);
    $goal_name = param('goal_name', 'string', null, true);
} else {
    $goal_ID = 0;
    $goal_name = null;
}

$split_engines = param('split_engines', 'integer', 0, true);

if (param_errors_detected()) {
    $sql = 'SELECT 0 AS count';
    $sql_count = 0;
    $total = 0;
} else {
    // Extract keyphrases from the hitlog:
    $extract_keyphrase_result = extract_keyphrase_from_hitlogs();
    if (is_string($extract_keyphrase_result)) {	// Could not execute the extract_keyphrase process, display a warning
        global $Messages;
        $Messages->clear();
        $Messages->add($extract_keyphrase_result, 'warning');
        $Messages->display();
    }

    $SQL = new SQL();
    if (empty($goal_ID) && empty($goal_name)) {	// We're not restricting to one or more Goals, get ALL possible keyphrases:
        $SQL->FROM('T_track__keyphrase INNER JOIN T_hitlog ON keyp_ID = hit_keyphrase_keyp_ID');
        // Date param applies to serach hit
        if (! empty($datestart)) {
            $SQL->WHERE_and('T_hitlog.hit_datetime >= ' . $DB->quote($datestart . ' 00:00:00'));
        }
        if (! empty($datestop)) {
            $SQL->WHERE_and('T_hitlog.hit_datetime <= ' . $DB->quote($datestop . ' 23:59:59'));
        }
    } else {	// We ARE restricting to a Goal, start off with IPs and Sessions IDs that hit that goal
        // then find marching hits
        // then keywords
        // fp> Note: so far we only join on remote IP because MySQL can only use a single index. Solution: probably UNION 2 results
        // INNER JOIN T_hitlog ON (goalhit_hit.hit_sess_ID = T_hitlog.hit_sess_ID OR goalhit_hit.hit_remote_addr = T_hitlog.hit_remote_addr )
        $SQL->FROM('T_track__goalhit INNER JOIN T_hitlog AS goalhit_hit ON ghit_hit_ID = goalhit_hit.hit_ID
								INNER JOIN T_hitlog ON goalhit_hit.hit_remote_addr = T_hitlog.hit_remote_addr
								INNER JOIN T_track__keyphrase ON T_hitlog.hit_keyphrase_keyp_ID = keyp_ID');
        if (! empty($goal_ID)) {
            $SQL->WHERE('ghit_goal_ID = ' . $goal_ID);
        } else {
            $SQL->FROM_add('INNER JOIN T_track__goal ON goal_ID = ghit_goal_ID');
            $SQL->WHERE_and('goal_name LIKE ' . $DB->quote($goal_name . '%'));
        }

        // Date param applies to goal hit
        if (! empty($datestart)) {
            $SQL->WHERE_and('goalhit_hit.hit_datetime >= ' . $DB->quote($datestart . ' 00:00:00'));
        }
        if (! empty($datestop)) {
            $SQL->WHERE_and('goalhit_hit.hit_datetime <= ' . $DB->quote($datestop . ' 23:59:59'));
        }
    }
    $SQL->WHERE_and('hit_agent_type = "browser"');
    if (! empty($keyword)) {	// Filter by keyword:
        $SQL->WHERE_and('keyp_phrase LIKE ' . $DB->quote('%' . $keyword . '%'));
    }
    if ($split_engines) {
        $SQL->GROUP_BY('keyp_ID, T_hitlog.hit_referer_dom_ID');
    } else {
        $SQL->GROUP_BY('keyp_ID');
    }

    if (! empty($sec_ID)) {	// Filter by section:
        $SQL->FROM_add('LEFT JOIN T_blogs ON T_hitlog.hit_coll_ID = blog_ID');
        $SQL->WHERE_and('blog_sec_ID = ' . $sec_ID);
    }
    if (! empty($blog)) {	// Filter by collection:
        $SQL->WHERE_and('T_hitlog.hit_coll_ID = ' . $blog);
    }

    // COUNT:
    $SQL->SELECT('keyp_ID');
    if (empty($goal_ID) && empty($goal_name)) {	// We're not restricting to a Goal
        $SQL->SELECT_add(', COUNT(DISTINCT hit_remote_addr) as count');
    } else { // We ARE retsrticting to a Goal
        $SQL->SELECT_add(', COUNT(DISTINCT goalhit_hit.hit_ID, T_hitlog.hit_remote_addr) as count');
    }
    $vars = $DB->get_row('SELECT COUNT(keyp_ID) AS count, SUM(count) AS total
													FROM (' . $SQL->get() . ') AS dummy', OBJECT, 0, 'Count rows + total for stats');
    $sql_count = (int) $vars->count;
    $total = (int) $vars->total;

    // DATA:
    $SQL->SELECT_add(', keyp_phrase');
    $SQL->SELECT_add(', keyp_count_refered_searches, keyp_count_internal_searches');

    if ($split_engines) {
        $SQL->SELECT_add(', dom_name, T_hitlog.hit_referer ');
        $SQL->FROM_add('LEFT JOIN T_basedomains ON dom_ID = T_hitlog.hit_referer_dom_ID');
        $SQL->ORDER_BY('*, keyp_phrase, dom_name');
    } else {
        $SQL->ORDER_BY('*, keyp_phrase');
    }
    $sql = $SQL->get();
}

// Create result set:
$Results = new Results($sql, 'keywords_', $split_engines ? '--D' : '-D', null, $sql_count);

$Results->title = T_('Keyphrases') . get_manual_link('search-keywords-list');

/**
 * Callback to add filters on top of the result set
 *
 * @param Form
 */
function filter_keyphrases(&$Form)
{
    global $datestart, $datestop, $keyword;

    $Form->date_input('datestartinput', $datestart, T_('From'));
    $Form->date_input('datestopinput', $datestop, T_('to'));

    $Form->text('keyword', $keyword, 50, T_('Contains keyword'), '', 255);

    if (check_user_perm('stats', 'view')) {	// Permission to view stats for ALL blogs:
        global $goal_ID;
        $GoalCache = &get_GoalCache();
        $GoalCache->load_all();
        $Form->select_object('goal_ID', $goal_ID, $GoalCache, T_('Goal'), '', true);
    }

    $Form->text_input('goal_name', get_param('goal_name'), 20, T_('Goal names starting with'), '', [
        'maxlength' => 50,
    ]);

    $Form->checkbox_basic_input('split_engines', get_param('split_engines'), /* TRANS: split search engines in results table */ T_('Split search engines'));
}

// Initialize params to filter by selected collection and/or group:
$section_params = empty($blog) ? '' : '&amp;blog=' . $blog;
$section_params .= empty($sec_ID) ? '' : '&amp;sec_ID=' . $sec_ID;

$today = date('Y-m-d', $localtimenow);
$Results->filter_area = [
    'callback' => 'filter_keyphrases',
    'url_ignore' => 'goal_ID,datestartinput,datestart,datestopinput,datestop,keyword,goal_name,split_engines',
];

$Results->register_filter_preset('all', T_('All'), '?ctrl=stats&amp;tab=refsearches&amp;tab3=keywords' . $section_params);
$Results->register_filter_preset('today', T_('Today'), '?ctrl=stats&amp;tab=refsearches&amp;tab3=keywords' . $section_params . '&amp;' . $Results->param_prefix . 'filter_preset=today&amp;datestart=' . $today . '&amp;datestop=' . $today);

if ($split_engines) {	// Search engine:
    $Results->cols[] = [
        'th' => T_('Search engine'),
        'order' => 'dom_name',
        'td_class' => 'nowrap',
        'td' => '<a href="$hit_referer$">$dom_name$</a>',
        'total' => T_('TOTAL'),
    ];
}

// Keywords:
$Results->cols[] = [
    'th' => T_('Search keywords'),
    'order' => 'keyp_phrase',
    'td' => '$keyp_phrase$',
    'total' => $sql_count . ' ' . T_('keyphrases'),
];

// Count:
if (empty($goal_ID)) {	// We're not restricting to a Goal
    $Results->cols[] = [
        'th' => T_('Unique IP hits'),
        'order' => 'count',
        'default_dir' => 'D',
        'td_class' => 'right',
        'td' => '$count$',
        'total_class' => 'right',
        'total' => $total,
    ];
} else { // We ARE retsrticting to a Goal
    $Results->cols[] = [
        'th' => T_('Goal hits'),
        'order' => 'count',
        'default_dir' => 'D',
        'td_class' => 'right',
        'td' => '$count$',
        'total_class' => 'right',
        'total' => $total,
    ];
}

$Results->cols[] = [
    'th' => T_('Refered searches'),
    'order' => 'keyp_count_refered_searches',
    'default_dir' => 'D',
    'td' => '$keyp_count_refered_searches$',
    'td_class' => 'nowrap right',
];

$Results->cols[] = [
    'th' => T_('Internal searches'),
    'order' => 'keyp_count_internal_searches',
    'default_dir' => 'D',
    'td' => '$keyp_count_internal_searches$',
    'td_class' => 'nowrap right',
];

$Results->cols[] = [
    'th' => '%',
    'order' => 'count',
    'default_dir' => 'D',
    'td_class' => 'right',
    'td' => '%percentage( #count#, ' . $total . ' )%',
    'total_class' => 'right',
    'total' => '100.0 %',
];

$Results->cols[] = [
    'th' => T_('Cumulative'),
    'td_class' => 'right',
    'td' => '%addup_percentage( #count#, ' . $total . ' )%',
];

$Results->global_icon(T_('Reset counters'), 'file_delete', regenerate_url('action', 'action=reset_counters'), T_('Reset counters') . ' &raquo;', 3, 4);
// Display results:
$Results->display();
