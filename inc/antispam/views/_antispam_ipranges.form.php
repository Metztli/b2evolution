<?php
/**
 * This file display the Antispam IP ranges
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
 * @var Slug
 */
global $edited_IPRange;

// Determine if we are creating or updating...
global $action;
$creating = $action == 'iprange_new';

$Form = new Form(null, 'iprange_checkchanges', 'post', 'compact');

$Form->global_icon(TB_('Delete this IP range!'), 'delete', regenerate_url('iprange_ID,action', 'iprange_ID=' . $edited_IPRange->ID . '&amp;action=iprange_delete&amp;' . url_crumb('iprange')));
$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action,iprange_ID'));

$Form->begin_form('fform', ($creating ? TB_('New IP Range') : TB_('IP Range')) . get_manual_link('ip-range-editing'));

$Form->add_crumb('iprange');
$Form->hidden('action', $creating ? 'iprange_create' : 'iprange_update');
$Form->hidden_ctrl();
$Form->hidden('tab', get_param('tab'));
$Form->hidden('tab3', get_param('tab3'));
$Form->hidden('iprange_ID', param('iprange_ID', 'integer', 0));

$Form->select_input_array('aipr_status', $edited_IPRange->get('status'), aipr_status_titles(), TB_('Status'), '', [
    'force_keys_as_values' => true,
    'background_color' => aipr_status_colors(),
    'required' => true,
]);

$Form->text_input('aipr_IPv4start', int2ip($edited_IPRange->get('IPv4start')), 50, TB_('IP Range Start'), '', [
    'maxlength' => 15,
    'required' => true,
]);

$Form->text_input('aipr_IPv4end', int2ip($edited_IPRange->get('IPv4end')), 50, TB_('IP Range End'), '', [
    'maxlength' => 15,
    'required' => true,
]);

$Form->info(TB_('User count'), (int) $edited_IPRange->get('user_count'));

$Form->info(TB_('Block count'), (int) $edited_IPRange->get('block_count'));

$Form->end_form([[
    'submit',
    'save',
    ($creating ? /* TRANS: Verb */ TB_('Record') : TB_('Save Changes!')),
    'SaveButton',
    'data-shortcut' => 'ctrl+s,command+s,ctrl+enter,command+enter',
]]);

if ($edited_IPRange->ID > 0) {	// Display Users registered through this IP Range:
    users_results_block([
        'reg_ip_min' => int2ip($edited_IPRange->get('IPv4start')),
        'reg_ip_max' => int2ip($edited_IPRange->get('IPv4end')),
        'filterset_name' => 'iprange_' . $edited_IPRange->ID,
        'results_param_prefix' => 'ipruser_',
        'results_title' => TB_('Users registered through this IP Range') . get_manual_link('ip-range-users'),
        'results_order' => '/user_created_datetime/D',
        'page_url' => get_dispctrl_url('antispam', 'tab3=ipranges&action=iprange_edit&amp;iprange_ID=' . $edited_IPRange->ID),
        'display_ID' => false,
        'display_btn_adduser' => false,
        'display_btn_addgroup' => false,
        'display_blogs' => false,
        'display_source' => false,
        'display_regcountry' => false,
        'display_update' => false,
        'display_lastvisit' => false,
        'display_contact' => false,
        'display_reported' => false,
        'display_group' => false,
        'display_level' => false,
        'display_status' => false,
        'display_actions' => false,
        'display_newsletter' => false,
    ]);

    // Display Sessions connected through this IP Range:
    global $UserSettings, $Plugins, $admin_url;
    load_funcs('sessions/views/_stats_view.funcs.php');

    // Create result set:
    $SQL = new SQL();
    $SQL->SELECT('SQL_NO_CACHE sess_ID, user_login, TIMESTAMPDIFF( SECOND, sess_start_ts, sess_lastseen_ts ) as sess_length, sess_lastseen_ts, sess_ipaddress');
    $SQL->FROM('T_sessions LEFT JOIN T_users ON sess_user_ID = user_ID');

    $count_SQL = new SQL();
    $count_SQL->SELECT('SQL_NO_CACHE COUNT(sess_ID)');
    $count_SQL->FROM('T_sessions LEFT JOIN T_users ON sess_user_ID = user_ID');

    $SQL->WHERE('INET_ATON( sess_ipaddress ) >= ' . $DB->quote($edited_IPRange->get('IPv4start')));
    $SQL->WHERE_and('INET_ATON( sess_ipaddress ) <= ' . $DB->quote($edited_IPRange->get('IPv4end')));
    $count_SQL->WHERE('INET_ATON( sess_ipaddress ) >= ' . $DB->quote($edited_IPRange->get('IPv4start')));
    $count_SQL->WHERE_and('INET_ATON( sess_ipaddress ) <= ' . $DB->quote($edited_IPRange->get('IPv4end')));

    $Results = new Results($SQL->get(), 'ipsess_', '-D', $UserSettings->get('results_per_page'), $count_SQL->get());
    $Results->title = TB_('Sessions connected through this IP Range') . get_manual_link('ip-range-sessions');

    $Results->cols[] = [
        'th' => TB_('ID'),
        'order' => 'sess_ID',
        'default_dir' => 'D',
        'td_class' => 'shrinkwrap',
        'td' => '<a href="' . $admin_url . '?ctrl=stats&amp;tab=hits&amp;blog=0&amp;sess_ID=$sess_ID$">$sess_ID$</a>',
    ];

    $Results->cols[] = [
        'th' => TB_('Last seen'),
        'order' => 'sess_lastseen_ts',
        'default_dir' => 'D',
        'td_class' => 'timestamp',
        'td' => '%mysql2localedatetime_spans( #sess_lastseen_ts# )%',
    ];

    $Results->cols[] = [
        'th' => TB_('User login'),
        'order' => 'user_login',
        'td' => '%stat_session_login( #user_login# )%',
    ];

    $Results->cols[] = [
        'th' => TB_('Remote IP'),
        'order' => 'sess_ipaddress',
        'td' => '$sess_ipaddress$',
    ];

    // Get additional columns from the Plugins:
    $Plugins->trigger_event('GetAdditionalColumnsTable', [
        'table' => 'sessions',
        'column' => 'sess_ipaddress',
        'Results' => $Results,
    ]);

    function display_sess_length($sess_ID, $sess_length)
    {
        $result = '';
        $second = $sess_length % 60;
        $sess_length = ($sess_length - $second) / 60;
        $minute = $sess_length % 60;
        $sess_length = ($sess_length - $minute) / 60;
        $hour = $sess_length % 24;
        $day = ($sess_length - $hour) / 24;

        if ($day > 0) {
            $result = sprintf((($day > 1) ? TB_('%d days') : TB_('%d day')), $day) . ' ';
        }
        if ($hour < 10) {
            $hour = '0' . $hour;
        }
        if ($minute < 10) {
            $minute = '0' . $minute;
        }
        if ($second < 10) {
            $second = '0' . $second;
        }

        $result .= $hour . ':' . $minute . ':' . $second;
        return stat_session_hits($sess_ID, $result);
    }

    $Results->cols[] = [
        'th' => TB_('Session length'),
        'order' => 'sess_length',
        'td_class' => 'center',
        'total_class' => 'right',
        'td' => '%display_sess_length( #sess_ID#, #sess_length# )%',
    ];

    // Display results:
    $Results->display();
}

?>
<script>
jQuery( document ).ready( function()
{
	jQuery( '#delete_iprange_conflicts' ).click( function()
	{	// Submit form with deleting of all IP range conflicts:
		jQuery( 'form#iprange_checkchanges' )
			.append( '<input type="hidden" name="delete_conflicts" value="1" />' )
			.submit();
	} );
} );
</script>
