<?php
/**
 * This file implements the UI view for the regional settings.
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

global $rsc_subdir, $conf_subdir, $pagenow, $locales_path, $locales, $action, $edit_locale, $loc_transinfo, $template, $allow_po_extraction;
global $localtimenow, $warning_message, $saved_params;

if ($action == 'edit') { // Edit a locale:
    $Form = new Form(null, 'loc_checkchanges', 'post', 'compact');

    $Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action,template'));

    $Form->begin_form('fform', TB_('Locale settings') . get_manual_link('locale-form'));

    $Form->add_crumb('locales');
    $Form->hidden('ctrl', 'locales');
    $Form->hidden('loc_transinfo', $loc_transinfo);
    $Form->hidden('action', ($edit_locale == '_new_') ? 'createlocale' : 'updatelocale');
    $Form->hidden('template', param('template', 'string', ''));
    $Form->hidden('edit_locale', $edit_locale);

    // read template
    if (isset($locales[$template])) { // An editing of existing locale
        $ltemplate = $locales[$template];
        $newlocale = $template;
    } elseif ($edit_locale != '_new_' && isset($locales[$edit_locale])) { // A creating of new locale that exists in $locales array
        $ltemplate = $locales[$edit_locale];
        $newlocale = $edit_locale;
    } else { // New unknown locale
        $newlocale = get_param('newloc_locale');
    }

    if (isset($ltemplate)) { // Set properly values when errors exist after form was submitted
        $newlocale = get_param('newloc_locale') != '' ? get_param('newloc_locale') : $newlocale;
        foreach ($ltemplate as $lt_key => $lt_value) {
            if (! is_null(get_param('newloc_' . $lt_key))) {
                if (get_param('newloc_' . $lt_key) == '') { // Empty value, use default value if available
                    if ($default_value = locale_get($lt_key, '#')) {
                        $ltemplate[$lt_key] = $default_value;
                    }
                } else { // Display what user has entered on previous form instead of what was saved in DB
                    $ltemplate[$lt_key] = get_param('newloc_' . $lt_key);
                }
            }
        }
    }

    if ($edit_locale != '_new_') { // we need to remember this for updating locale
        $Form->hidden('oldloc_locale', $newlocale);
    }
    $Form->hidden('newloc_transliteration_map', (isset($ltemplate['transliteration_map']) ? base64_encode(serialize($ltemplate['transliteration_map'])) : ''));

    // Locale
    $Form->text_input(
        'newloc_locale',
        $newlocale,
        20,
        TB_('Locale'),
        sprintf(
            TB_('The first two letters should be a <a %s>ISO 639 language code</a>. The last two letters should be a <a %s>ISO 3166 country code</a>.'),
            'href="http://www.gnu.org/software/gettext/manual/html_chapter/gettext_15.html#Language-Codes"',
            'href="http://www.gnu.org/software/gettext/manual/html_chapter/gettext_15.html#Country-Codes"'
        ),
        [
            'required' => true,
        ]
    );
    // Enabled
    $Form->checkbox(
        'newloc_enabled',
        (isset($ltemplate['enabled']) && $ltemplate['enabled'] ? 1 : get_param('newloc_enabled')),
        TB_('Enabled'),
        TB_('Should this locale be available to users?')
    );
    // Name
    $Form->text_input(
        'newloc_name',
        (isset($ltemplate['name']) ? $ltemplate['name'] : get_param('newloc_name')),
        40,
        TB_('Name'),
        TB_('name of the locale')
    );
    // Charset
    $Form->info(TB_('Charset'), 'utf-8');
    // Date format
    $Form->text_input(
        'newloc_datefmt',
        (isset($ltemplate['datefmt']) ? $ltemplate['datefmt'] : locale_get('datefmt', ($newlocale ? $newlocale : '#'))),
        20,
        TB_('Date format'),
        TB_('See below.'),
        [
            'required' => true,
        ]
    );
    // Long Date format
    $Form->text_input(
        'newloc_longdatefmt',
        (isset($ltemplate['longdatefmt']) ? $ltemplate['longdatefmt'] : get_param('newloc_longdatefmt')),
        20,
        TB_('Long date format'),
        TB_('See below.')
    );
    // Extended Date format
    $Form->text_input(
        'newloc_extdatefmt',
        (isset($ltemplate['extdatefmt']) ? $ltemplate['extdatefmt'] : get_param('newloc_extdatefmt')),
        20,
        TB_('Extended date format'),
        TB_('See below.')
    );
    // Input date format
    $Form->text_input(
        'newloc_input_datefmt',
        (isset($ltemplate['input_datefmt']) ? $ltemplate['input_datefmt'] : get_param('newloc_input_datefmt')),
        20,
        TB_('Input date format'),
        TB_('See below.'),
        [
            'required' => true,
        ]
    );
    // Time format
    $Form->text_input(
        'newloc_timefmt',
        (isset($ltemplate['timefmt']) ? $ltemplate['timefmt'] : locale_get('timefmt', ($newlocale ? $newlocale : '#'))),
        20,
        TB_('Time format'),
        TB_('See below.'),
        [
            'required' => true,
        ]
    );
    // Short time format
    $Form->text_input(
        'newloc_shorttimefmt',
        (isset($ltemplate['shorttimefmt']) ? $ltemplate['shorttimefmt'] : locale_get('shorttimefmt', ($newlocale ? $newlocale : '#'))),
        20,
        TB_('Short time format'),
        TB_('See below.')
    );
    // Input time format
    $Form->text_input(
        'newloc_input_timefmt',
        (isset($ltemplate['input_timefmt']) ? $ltemplate['input_timefmt'] : get_param('newloc_input_timefmt')),
        20,
        TB_('Input time format'),
        TB_('See below.'),
        [
            'required' => true,
        ]
    );
    // Start of week
    $Form->dayOfWeek(
        'newloc_startofweek',
        (isset($ltemplate['startofweek']) ? $ltemplate['startofweek'] : get_param('newloc_startofweek')),
        TB_('Start of week'),
        TB_('Day at the start of the week.')
    );
    // Lang file
    $Form->text(
        'newloc_messages',
        (isset($ltemplate['messages']) ? $ltemplate['messages'] : get_param('newloc_messages')),
        20,
        TB_('Lang file'),
        TB_('the lang file to use, from the <code>locales</code> subdirectory')
    );
    // Priority
    $Form->text_input(
        'newloc_priority',
        (isset($ltemplate['priority']) ? $ltemplate['priority'] : get_param('newloc_priority')),
        3,
        TB_('Priority'),
        TB_('1 is highest. Priority is important when selecting a locale from a language code and several locales match the same language; this can happen when detecting browser language. Priority also affects the order in which locales are displayed in dropdown boxes, etc.'),
        [
            'required' => true,
        ]
    );

    // TODO: Update this field onchange of datefmt/timefmt through AJAX:
    // fp> It would actually make more sense to have the preview at the exact place that says "see below"
    locale_temp_switch($newlocale);
    $Form->info_field(TB_('Date preview'), date_i18n(locale_datefmt() . ' ' . locale_timefmt(), $localtimenow));
    locale_restore_previous();

    // generate Javascript array of locales to warn in case of overwriting
    $l_warnfor = "'" . implode("', '", array_keys($locales)) . "'";
    if ($edit_locale != '_new_') { // remove the locale we want to edit from the generated array
        $l_warnfor = str_replace("'$newlocale'", "'thiswillneverevermatch'", $l_warnfor);
    }

    $Form->end_form([['submit', 'submit', ($edit_locale == '_new_') ? TB_('Create') : TB_('Save Changes!'), 'SaveButton']]);

    ?>
	<div class="panelinfo">
		<h3><?php echo TB_('Flags') ?></h3>
		<p><?php printf(TB_('The flags are stored in the file <code>%s</code>. The config for background-position is located in the file %s and defined by array $country_flags_bg.'), '/' . $rsc_subdir . 'icons/flags_sprite.png', '/' . $conf_subdir . '_locales.php'); ?></p>
		<h3><?php echo TB_('Date/Time Formats') ?></h3>
		<p><?php echo TB_('The following characters are recognized in the format strings:') ?></p>
		<p>
		<?php echo TB_('a - "am" or "pm"') ?><br />
		<?php echo TB_('A - "AM" or "PM"') ?><br />
		<?php echo TB_('B - Swatch Internet time') ?><br />
		<?php echo TB_('c - ISO 8601 date (Requires PHP 5); i.e. "2004-02-12T15:19:21+00:00"') ?><br />
		<?php echo TB_('d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"') ?><br />
		<?php echo TB_('D - day of the week, textual, 3 letters; i.e. "Fri"') ?><br />
		<?php echo TB_('e - day of the week, 1 letter; i.e. "F"') ?><br />
		<?php echo TB_('F - month, textual, long; i.e. "January"') ?><br />
		<?php echo TB_('g - hour, 12-hour format without leading zeros; i.e. "1" to "12"') ?><br />
		<?php echo TB_('G - hour, 24-hour format without leading zeros; i.e. "0" to "23"') ?><br />
		<?php echo TB_('h - hour, 12-hour format; i.e. "01" to "12"') ?><br />
		<?php echo TB_('H - hour, 24-hour format; i.e. "00" to "23"') ?><br />
		<?php echo TB_('i - minutes; i.e. "00" to "59"') ?><br />
		<?php echo TB_('I (capital i) - "1" if Daylight Savings Time, "0" otherwise.') ?><br />
		<?php echo TB_('j - day of the month without leading zeros; i.e. "1" to "31"') ?><br />
		<?php echo TB_('l (lowercase "L") - day of the week, textual, long; i.e. "Friday"') ?><br />
		<?php echo TB_('L - boolean for whether it is a leap year; i.e. "0" or "1"') ?><br />
		<?php echo TB_('m - month; i.e. "01" to "12"') ?><br />
		<?php echo TB_('M - month, textual, 3 letters; i.e. "Jan"') ?><br />
		<?php echo TB_('n - month without leading zeros; i.e. "1" to "12"') ?><br />
		<?php echo TB_('O - Difference to Greenwich time (GMT) in hours; i.e. "+0200"') ?><br />
		<?php echo TB_('r - RFC 822 formatted date; i.e. "Thu, 21 Dec 2000 16:01:07 +0200"') ?><br />
		<?php echo TB_('s - seconds; i.e. "00" to "59"') ?><br />
		<?php echo TB_('S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"') ?><br />
		<?php echo TB_('t - number of days in the given month; i.e. "28" to "31"') ?><br />
		<?php echo TB_('T - Timezone setting of this machine; i.e. "MDT"') ?><br />
		<?php echo TB_('U - seconds since the epoch') ?><br />
		<?php echo TB_('w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)') ?><br />
		<?php echo TB_('W - ISO-8601 week number of year, weeks starting on Monday; i.e. "42"') ?><br />
		<?php echo TB_('Y - year, 4 digits; i.e. "1999"') ?><br />
		<?php echo TB_('y - year, 2 digits; i.e. "99"') ?><br />
		<?php echo TB_('z - day of the year; i.e. "0" to "365"') ?><br />
		<?php echo TB_('Z - timezone offset in seconds (i.e. "-43200" to "43200"). The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.') ?>
		</p>
		<?php echo TB_('isoZ - full ISO 8601 format, equivalent to Y-m-d\TH:i:s\Z') ?><br />
		<p><?php echo TB_('Unrecognized characters in the format string will be printed as-is.<br />You can escape characters by preceding them with a \ to print them as-is.') ?></p>
	</div>
<?php
} elseif ($action == 'update' && (! empty($warning_message))) {
    $Form = new Form(null, 'loc_confirm');

    $Form->begin_form('fform');

    $Form->add_crumb('locales');
    $Form->hidden('ctrl', 'locales');
    $Form->hidden('newdefault_locale', $Settings->get('default_locale'));
    foreach ($saved_params as $key => $value) {
        $Form->hidden($key, $value);
    }

    $Form->begin_fieldset(TB_('Confirm update'));
    $Form->custom_content($warning_message);
    $Form->end_fieldset();

    $Form->end_form([
        ['', 'actionArray[confirm_update]', TB_('Confirm')],
        ['', 'actionArray[abort_update]', TB_('Abort')],
    ]);
} else { // show main form
    $Form = new Form($pagenow, 'loc_checkchanges');

    $Form->begin_form('fform');

    $Form->add_crumb('locales');
    $Form->hidden('ctrl', 'locales');
    $Form->hidden('action', 'update');
    $Form->hidden('loc_transinfo', $loc_transinfo);

    $Form->begin_fieldset(TB_('Regional settings') . get_manual_link('locales-tab'));

    if (! isset($locales[$Settings->get('default_locale')])
        || ! $locales[$Settings->get('default_locale')]['enabled']) { // default locale is not enabled
        param_error('newdefault_locale', TB_('Note: default locale is not enabled.'));
    }

    $locale_options = locale_options($Settings->get('default_locale'), false);
    $Form->select_input_options('newdefault_locale', $locale_options, TB_('Default locale'), TB_('Overridden by browser config, user locale or blog locale (in this order).'));
    // $Form->select( 'newdefault_locale', $Settings->get('default_locale'), 'locale_options_return', TB_('Default locale'), TB_('Overridden by browser config, user locale or blog locale (in this order).'));
    $Form->end_fieldset();


    $Form->begin_fieldset(TB_('Available locales') . get_manual_link('locales-tab'));

    echo '<p class="center">';
    if ($loc_transinfo) {
        global $messages_pot_file_info;
        $messages_pot_file_info['messages.pot'] = locale_file_po_info($locales_path . 'messages.pot');
        $messages_DB_info = $DB->get_var('SELECT COUNT(iost_ID) FROM T_i18n_original_string');

        echo '<a href="' . $pagenow . '?ctrl=locales&amp;loc_transinfo=0">' . TB_('Hide translation info'), '</a><br /><br />';

        $Form->output = false;
        $button_generate_POT_file = $Form->button(['submit', 'actionArray[generate_pot]', TB_('(Re)generate POT file'), 'SaveButton']);
        $button_import_POT_file = $Form->button(['submit', 'actionArray[import_pot]', TB_('(Re)import POT file'), 'SaveButton']);
        $Form->output = true;
        echo sprintf(
            TB_('# strings in .POT file: %s - # strings in DB: %s '),
            $messages_pot_file_info['messages.pot']['all'] . ' ' . $button_generate_POT_file,
            $messages_DB_info . ' ' . $button_import_POT_file
        ) . '<br />';
        if (check_user_perm('options', 'edit') && ! $allow_po_extraction) {
            echo '<span class="notes">';
            echo TB_('To allow the extraction of language files, please set $allow_po_extraction = 1; in conf/_locales.php.');
            echo '</span>';
        }
    } else {
        echo '<a href="' . $pagenow . '?ctrl=locales&amp;loc_transinfo=1">' . TB_('Show translation info'), '</a>';
    }
    echo '</p>';

    echo '<table class="grouped table table-striped table-bordered table-hover table-condensed" cellspacing="0">';

    ?>
	<tr>
		<th class="firstcol"<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Locale') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Enabled') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Name') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Charset') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Date fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Long date fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Extended date fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Input date fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Time fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Short time fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Input time fmt') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?> title="<?php echo TB_('Day at the start of the week: 0 for Sunday, 1 for Monday, 2 for Tuesday, etc');
    ?>"><?php echo TB_('Start of week') ?></th>
		<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Priority') ?></th>
		<?php if (check_user_perm('options', 'edit')) { ?>
			<th<?php echo $loc_transinfo ? ' rowspan="2"' : ''; ?>><?php echo TB_('Edit') ?></th>
			<?php
		}
    if ($loc_transinfo) {
        ?>
			<th colspan="2"><?php echo TB_('General') ?></th>
			<th colspan="2"><?php echo TB_('Back-office') ?></th>
			<th colspan="2"><?php echo TB_('Demo contents') ?></th>
			<?php
        if (check_user_perm('options', 'edit') && $allow_po_extraction) {
            echo '<th rowspan="2" class="lastcol">' . TB_('Extract') . '</th>';
        }
    } ?>
	</tr>
	<?php
    if ($loc_transinfo) {
        ?>
		<tr>
			<th><?php echo TB_('Strings') ?></th>
			<th><?php echo TB_('Translated') ?></th>
			<th><?php echo TB_('Strings') ?></th>
			<th><?php echo TB_('Translated') ?></th>
			<th><?php echo TB_('Strings') ?></th>
			<th><?php echo TB_('Translated') ?></th>
		</tr>
		<?php
    }


    $i = 0; // counter to distinguish POSTed locales later
    foreach ($locales as $lkey => $locale_data) {
        $i++;

        $ltemplate = $locale_data;
        foreach ($ltemplate as $lt_key => $lt_value) {
            if (! is_null(get_param('loc_' . $i . '_' . $lt_key))) {
                if (get_param('loc_' . $i . '_' . $lt_key) == '') { // Empty value, use default value if available
                    if ($default_value = locale_get($lt_key, '#')) {
                        $ltemplate[$lt_key] = $default_value;
                    }
                } else { // Display what user has entered on previous form instead of what was saved in DB
                    $ltemplate[$lt_key] = get_param('loc_' . $i . '_' . $lt_key);
                }
            }
        }

        $datefmt = isset($ltemplate['datefmt']) ? $ltemplate['datefmt'] : locale_get('datefmt', $lkey);
        $longdatefmt = isset($ltemplate['longdatefmt']) ? $ltemplate['longdatefmt'] : locale_get('longdatefmt', $lkey);
        $extdatefmt = isset($ltemplate['extdatefmt']) ? $ltemplate['extdatefmt'] : locale_get('extdatefmt', $lkey);
        $input_datefmt = isset($ltemplate['input_datefmt']) ? $ltemplate['input_datefmt'] : locale_get('input_datefmt', $lkey);
        $timefmt = isset($ltemplate['timefmt']) ? $ltemplate['timefmt'] : locale_get('timefmt', $lkey);
        $shorttimefmt = isset($ltemplate['shorttimefmt']) ? $ltemplate['shorttimefmt'] : locale_get('shorttimefmt', $lkey);
        $input_timefmt = isset($ltemplate['input_timefmt']) ? $ltemplate['input_timefmt'] : locale_get('input_timefmt', $lkey);

        // Generate preview of date/time-format:
        locale_temp_switch($lkey);
        $datefmt_preview = date_i18n($datefmt, $localtimenow);
        $longdatefmt_preview = date_i18n($longdatefmt, $localtimenow);
        $extdatefmt_preview = date_i18n($extdatefmt, $localtimenow);
        $input_datefmt_preview = date_i18n($input_datefmt, $localtimenow);
        $timefmt_preview = date_i18n($timefmt, $localtimenow);
        $shorttimefmt_preview = date_i18n($shorttimefmt, $localtimenow);
        $input_timefmt_preview = date_i18n($input_timefmt, $localtimenow);
        locale_restore_previous();

        $Form->output = false;
        $Form->switch_layout('none');
        $datefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_datefmt',
            'value' => $datefmt,
            'title' => sprintf(TB_('Preview: %s'), $datefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $longdatefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_longdatefmt',
            'value' => $longdatefmt,
            'title' => sprintf(TB_('Preview: %s'), $longdatefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $extdatefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_extdatefmt',
            'value' => $extdatefmt,
            'title' => sprintf(TB_('Preview: %s'), $extdatefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $input_datefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_input_datefmt',
            'value' => $input_datefmt,
            'title' => sprintf(TB_('Preview: %s'), $input_datefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $timefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_timefmt',
            'value' => $timefmt,
            'title' => sprintf(TB_('Preview: %s'), $timefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $shorttimefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_shorttimefmt',
            'value' => $shorttimefmt,
            'title' => sprintf(TB_('Preview: %s'), $shorttimefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $input_timefmt_input = $Form->get_input_element([
            'type' => 'text',
            'name' => 'loc_' . $i . '_input_timefmt',
            'value' => $input_timefmt,
            'title' => sprintf(TB_('Preview: %s'), $input_timefmt_preview),
            'class' => 'form-control input-sm',
            'maxlength' => 20,
            'size' => 6,
            'hide_label' => true,
        ]);
        $Form->switch_layout(null);
        $Form->output = true;

        ?>
		<tr class="<?php echo(($i % 2 == 1) ? 'odd' : 'even') ?>">
		<td class="firstcol left" title="<?php echo TB_('Priority') . ': ' . $locale_data['priority'] . ', ' . TB_('Charset') . ': ' . $locale_data['charset'] . ', ' . TB_('Lang file') . ': ' . $locale_data['messages'] ?>">
			<?php
            echo '<input type="hidden" name="loc_' . $i . '_locale" value="' . $lkey . '" />';

        $transliteration_map = '';
        if (isset($locale_data['transliteration_map']) && is_array($locale_data['transliteration_map'])) {
            $transliteration_map = base64_encode(serialize($locale_data['transliteration_map']));
        }
        echo '<input type="hidden" name="loc_' . $i . '_transliteration_map" value="' . $transliteration_map . '" />';

        locale_flag($lkey);
        echo '
			<strong>';
        if (check_user_perm('options', 'edit')) {
            echo '<a href="' . $pagenow . '?ctrl=locales&amp;action=edit&amp;edit_locale=' . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '" title="' . TB_('Edit locale') . '">';
        }
        echo $lkey;
        if (check_user_perm('options', 'edit')) {
            echo '</a>';
        }

        // TODO: Update title attribs for datefmt/timefmt onchange through AJAX  -- fp> all that complexity for an invisible tooltip... :/ Users should update the format on the detailed screen and get a dynamic preview there. Maybe the date and time should be editable on the list at all. There is no help here either. Users should be encouraged to go to the detailed screen )
        echo '</strong></td>
				<td class="center">
					<input type="checkbox" name="loc_' . $i . '_enabled" value="1"' . ($locale_data['enabled'] ? 'checked="checked"' : '') . ' />
				</td>
				<td>
					<input type="text" name="loc_' . $i . '_name" value="' . format_to_output($locale_data['name'], 'formvalue') . '" maxlength="40" size="17" class="form-control input-sm" />
				</td>
				<td' . ($locale_data['charset'] == 'utf-8' ? '' : ' class="red"') . '>
					' . $locale_data['charset'] . '
				</td>
				<td>' . $datefmt_input . '</td>
				<td>' . $longdatefmt_input . '</td>
				<td>' . $extdatefmt_input . '</td>
				<td>' . $input_datefmt_input . '</td>
				<td>' . $timefmt_input . '</td>
				<td>' . $shorttimefmt_input . '</td>
				<td>' . $input_timefmt_input . '</td>
				<td>';
        $Form->switch_layout('none');
        $Form->dayOfWeek('loc_' . $i . '_startofweek', $locale_data['startofweek'], '', '', 'input-sm');
        $Form->switch_layout(null); // Restore layout
        echo '</td>';

        echo '<td class="right">' . $locale_data['priority'] . '</td>';


        if (check_user_perm('options', 'edit')) {
            if ($loc_transinfo) {
                echo '<td class="shrinkwrap">';
            } else {
                echo '<td class="lastcol shrinkwrap">';
            }
            if ($i > 1) { // show "move prio up"
                echo action_icon(TB_('Move priority up'), 'move_up', '?ctrl=locales&amp;action=prioup&amp;edit_locale='
                                . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;' . url_crumb('locales'));
            } else {
                echo get_icon('nomove') . ' ';
            }

            if ($i < count($locales)) { // show "move prio down"
                echo action_icon(TB_('Move priority down'), 'move_down', '?ctrl=locales&amp;action=priodown&amp;edit_locale='
                                . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;' . url_crumb('locales'));
            } else {
                echo get_icon('nomove') . ' ';
            }

            echo action_icon(TB_('Copy locale'), 'copy', '?ctrl=locales&amp;action=edit&amp;edit_locale=_new_&amp;template=' . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : ''));

            echo action_icon(TB_('Edit locale'), 'edit', '?ctrl=locales&amp;action=edit&amp;edit_locale=_edit_&amp;template=' . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : ''));

            if (isset($locale_data['fromdb'])) { // allow to delete locales loaded from db
                $l_atleastonefromdb = 1;
                echo action_icon(TB_('Restore default locale settings'), 'reload', '?ctrl=locales&amp;action=resetlocale&amp;edit_locale='
                                . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;' . url_crumb('locales'));
                if (! $locale_data['enabled'] && $lkey != 'en-US') { // Only not enabled locale can be deleted and if it is not default locale 'en-US'
                    echo action_icon(TB_('Delete locale'), 'delete', '?ctrl=locales&amp;action=delete&amp;edit_locale='
                                    . $lkey . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;' . url_crumb('locales'));
                }
            }
            echo '</td>';
        }

        if ($loc_transinfo) { // Show translation info:
            // Get PO file for that locale:
            $po_files = [
                'global' => 'messages.po',
                'backoffice' => 'messages-backoffice.po',
                'demo_contents' => 'messages-demo-contents.po',
            ];

            foreach ($po_files as $key => $po_file) {
                $po_file = $locales_path . $locale_data['messages'] . '/LC_MESSAGES/' . $po_file;
                if (! is_file($po_file)) {
                    echo '<td class="lastcol center" colspan="' . (1 + (int) (check_user_perm('options', 'edit'))) . '"><a href="?ctrl=translation&edit_locale=' . $lkey . '">' . TB_('No PO file') . '</a></td>';
                } else { // File exists:
                    $po_file_info = locale_file_po_info($po_file, true);

                    // $all=$translated+$fuzzy+$untranslated;
                    echo "\n\t" . '<td class="center">' . $po_file_info['all'] . '</td>';

                    $percent_done = $po_file_info['percent'];
                    $color = sprintf('%02x%02x00', 255 - round($percent_done * 2.55), round($percent_done * 2.55));
                    echo "\n\t<td class=\"center\" style=\"background-color:#" . $color . "\"><a href=\"?ctrl=translation&edit_locale=" . $lkey . "\">" . sprintf(TB_('%d%% in PO'), $percent_done) . "</a></td>";
                }
            }

            if (check_user_perm('options', 'edit') && $allow_po_extraction) { // Translator options:
                if (is_file($po_file)) {
                    echo "\n\t" . '<td class="lastcol">[<a href="' . $pagenow . '?ctrl=locales&amp;action=extract&amp;edit_locale=' . $lkey
                    . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;' . url_crumb('locales') . '" title="' . TB_('Extract .po file into b2evo-format') . '">' . TB_('Extract') . '</a>]</td>';
                } else {
                    echo '<td></td>';
                }
            }
        } // show message file percentage/extraction

        echo '</tr>';
    }

    echo '</table>';

    if (check_user_perm('options', 'edit')) {
        echo '<p class="center"><a href="' . $pagenow . '?ctrl=locales&amp;action=edit' . ($loc_transinfo ? '&amp;loc_transinfo=1' : '') . '&amp;edit_locale=_new_">' . get_icon('new') . ' ' . TB_('Create new locale') . '</a></p>';

        if (isset($l_atleastonefromdb)) {
            echo '<p class="center"><a href="' . $pagenow . '?ctrl=locales&amp;action=reset' . ($loc_transinfo ? '&amp;loc_transinfo=1' : '')
                        . '&amp;' . url_crumb('locales') . '" onclick="return confirm(\'' . TS_('Are you sure you want to restore to default locales?\nAll custom locale definitions will be lost!') . '\')">' . get_icon('reload') . ' ' . TB_('Restore defaults') . '</a></p>';
        }
    }

    $Form->end_fieldset();

    if (check_user_perm('options', 'edit')) {
        $Form->end_form([['submit', 'submit', TB_('Save Changes!'), 'SaveButton']]);
    }
}

?>