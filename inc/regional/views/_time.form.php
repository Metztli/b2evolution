<?php
/**
 * This file implements the UI view for the time settings.
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
 * @var GeneralSettings
 */
global $Settings;

global $rsc_subdir, $pagenow, $servertimenow, $localtimenow, $date_default_timezone;


// JavaScript function to calculate time difference: {{{
?>
<script>

var server_Date = new Date();
server_Date.setTime( <?php echo $servertimenow . '000' ?> ); // milliseconds
var user_Date = new Date();

function calc_TimeDifference(min_dif) {
	var ntd = user_Date.getTime() - server_Date.getTime();
	ntd = ntd / 1000; // to seconds

	ntd = ntd - 2; // assume that it takes 2 seconds from writing server_Date time into the source until the browser sets user_Date

	var neg = ( ntd < 0 );
	ntd = Math.abs(ntd);

	var hours = Math.floor(ntd/3600);
	var mins = Math.floor( (ntd%3600)/60 );
	//var secs = Math.round( (ntd%3600)%60 );

	//alert( server_Date+"\n"+user_Date+"\n"+ntd+"\nhours: "+hours+"\nmins: "+mins );

	if( mins == 0 )
	{
		ntd = hours;
	}
	else
	{
		ntd = hours+':'+mins;
	}

	if( neg && ntd != '0' ) ntd = '-'+ntd;

	// Apply the calculated time difference
	document.getElementById('newtime_difference').value = ntd;
}
</script>

<?php // }}}

$Form = new Form($pagenow, 'loc_checkchanges');

$Form->begin_form('fform');

$Form->add_crumb('time');
$Form->hidden('ctrl', 'time');
$Form->hidden('action', 'update');

$Form->begin_fieldset(TB_('Time settings') . get_manual_link('regional-time-tab'));

// Time difference:
$td_value = $Settings->get('time_difference');
$neg = ($td_value < 0);
$td_value = abs($td_value);
if ($td_value % 3600 != 0) { // we have minutes
    if ($td_value % 60 != 0) { // we have seconds (hh:mm:ss)
        $td_value = floor($td_value / 3600) . ':' . sprintf('%02d', ($td_value % 3600) / 60) . ':' . sprintf('%02d', ($td_value % 60));
    } else { // hh:mm
        $td_value = floor($td_value / 3600) . ':' . sprintf('%02d', ($td_value % 3600) / 60);
    }
} else { // just full hours:
    $td_value = $td_value / 3600;
}

if ($neg) {
    $td_value = '-' . $td_value;
}

$Form->info(TB_('Timezone from php.ini'), ini_get("date.timezone"), '(date.timezone)');
$Form->info(TB_('Timezone from /conf/_advanced.php'), empty($date_default_timezone) ? '-' : $date_default_timezone, '($date_default_timezone)');
$Form->info(TB_('Effective timezone'), date_default_timezone_get());
$Form->info(TB_('Current server time'), date_i18n(locale_timefmt(), $servertimenow), date_default_timezone_get());
$Form->text_input('newtime_difference', $td_value, 8 /* hh:mm:ss */, TB_('Time difference to apply'), '[' . TB_('in hours, e.g. "1", "1:30" or "-1.5"') . '] ' . TB_('If you\'re not on the timezone of your server.') . ' <a href="#" onclick="calc_TimeDifference(); return false;">' . TB_('Calculate time difference') . '</a>', [
    'maxlength' => 8,
    'required' => true,
]);
$Form->info(TB_('Local / corrected time'), date_i18n(locale_timefmt(), $localtimenow));

$Form->end_fieldset();


if (check_user_perm('options', 'edit')) {
    $Form->end_form([['submit', '', TB_('Save Changes!'), 'SaveButton']]);
}

?>
