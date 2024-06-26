<?php
/**
 * Form to edit settings of a plugin.
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
 * @global Plugin
 */
global $edit_Plugin;

/**
 * @global Plugins_admin
 */
global $admin_Plugins;

global $edited_plugin_name, $edited_plugin_shortdesc, $edited_plugin_priority, $edited_plugin_code;
global $admin_url;


$Form = new Form(null, 'pluginsettings_checkchanges');

// Restore defaults button:
$Form->global_icon(
    TB_('Restore defaults'),
    'reload',
    regenerate_url('action,plugin_class', 'action=default_settings&amp;plugin_ID=' . $edit_Plugin->ID . '&amp;crumb_plugin=' . get_crumb('plugin')),
    TB_('Restore defaults'),
    5,
    4,
    [
        'onclick' => 'if (!confirm(\'' . TS_('Are you sure you want to restore the default settings? This cannot be undone!') . '\')) { cancelClick(event); }',
    ]
);

// Info button:
$Form->global_icon(TB_('Display info'), 'info', regenerate_url('action,plugin_class', 'action=info&amp;plugin_class=' . $edit_Plugin->classname));

// Close button:
$Form->global_icon(TB_('Cancel edit!'), 'close', regenerate_url());

$Form->begin_form(
    'fform',
    '',
    // enable all form elements on submit (so values get sent):
    [
        'onsubmit' => 'var es=this.elements; for( var i=0; i < es.length; i++ ) { es[i].disabled=false; };',
    ]
);

$Form->add_crumb('plugin');
$Form->hidden_ctrl();
$Form->hidden('plugin_ID', $edit_Plugin->ID);


// --------------------------- INFO ---------------------------
$Form->begin_fieldset(TB_('Plugin info') . get_manual_link('plugins-editing'), [
    'class' => 'clear',
]);
// Name:
$Form->text_input('edited_plugin_name', $edited_plugin_name, 25, TB_('Name'), '', [
    'maxlength' => 255,
]);
// Desc:
$Form->text_input('edited_plugin_shortdesc', $edited_plugin_shortdesc, 50, TB_('Short desc'), '', [
    'maxlength' => 255,
]);
// Links to external manual (dh> has been removed from form's global_icons before by fp, but is very useful IMHO):
if ($edit_Plugin->get_help_link('$help_url')) {
    $Form->info(TB_('Help'), $edit_Plugin->get_help_link('$help_url'));
}
$Form->end_fieldset();


// --------------------------- SETTINGS ---------------------------
if ($edit_Plugin->Settings) { // NOTE: this triggers PHP5 autoloading through Plugin::__get() and therefor the 'isset($this->Settings)' hack in Plugin::GetDefaultSettings() still works, which is good.
    // We use output buffers here to only display the fieldset if there's content in there
    // (either from PluginSettings or PluginSettingsEditDisplayAfter).
    ob_start();
    $tmp_params = [
        'for_editing' => true,
    ];
    foreach ($edit_Plugin->GetDefaultSettings($tmp_params) as $l_name => $l_meta) {
        // Display form field for this setting:
        autoform_display_field($l_name, $l_meta, $Form, 'Settings', $edit_Plugin);
    }

    // This can be used add custom input fields or display custom output (e.g. a test link):
    $tmp_params = [
        'Form' => &$Form,
    ];
    $admin_Plugins->call_method($edit_Plugin->ID, 'PluginSettingsEditDisplayAfter', $tmp_params);

    $setting_contents = ob_get_contents();
    ob_end_clean();

    if ($setting_contents) {
        $Form->begin_fieldset(TB_('Plugin settings'), [
            'class' => 'clear',
        ]);
        echo $setting_contents;
        $Form->end_fieldset();
    }
}


// --------------------------- VARIABLES ---------------------------
$Form->begin_fieldset(TB_('Plugin variables') . ' (' . TB_('Advanced') . ')', [
    'class' => 'clear',
    'fold' => true,
    'id' => 'plugin_vars',
]);
$Form->text_input('edited_plugin_code', $edited_plugin_code, 15, TB_('Code'), TB_('The code to call the plugin by code. This is also used to link renderer plugins to items.'), [
    'maxlength' => 32,
]);
$Form->text_input('edited_plugin_priority', $edited_plugin_priority, 4, TB_('Priority'), '', [
    'maxlength' => 4,
]);
$Form->end_fieldset();


// --------------------------- EVENTS ---------------------------
$Form->begin_fieldset(TB_('Plugin events') . ' (' . TB_('Advanced') . ') ', [
    'fold' => true,
    'id' => 'plugin_events',
]);

if ($edit_Plugin->status != 'enabled') {
    echo '<p class="notes">' . TB_('Note: the plugin is not enabled.') . '</p>';
}

echo '<p>' . TB_('Warning: by disabling plugin events you change the behaviour of the plugin! Only change this, if you know what you are doing.') . '</p>';

$enabled_events = $admin_Plugins->get_enabled_events($edit_Plugin->ID);
$supported_events = $admin_Plugins->get_supported_events();
$registered_events = $admin_Plugins->get_registered_events($edit_Plugin);
$count = 0;
foreach (array_keys($supported_events) as $l_event) {
    if (! in_array($l_event, $registered_events)) {
        continue;
    }
    $Form->hidden('edited_plugin_displayed_events[]', $l_event); // to consider only displayed ones on update
    $Form->checkbox_input('edited_plugin_events[' . $l_event . ']', in_array($l_event, $enabled_events), $l_event, [
        'note' => $supported_events[$l_event],
    ]);
    $count++;
}
if (! $count) {
    echo TB_('This plugin has no registered events.');
}

$Form->end_fieldset();

if (check_user_perm('options', 'edit', false)) {
    $Form->buttons_input([
        [
            'type' => 'submit',
            'name' => 'actionArray[update_settings]',
            'value' => TB_('Save Changes!'),
            'class' => 'SaveButton',
            'data-shortcut' => 'ctrl+enter,command+enter',
        ],
        [
            'type' => 'submit',
            'name' => 'actionArray[update_edit_settings]',
            'value' => TB_('Save and continue editing...'),
            'data-shortcut' => 'ctrl+s,command+s',
        ],
    ]);
}
$Form->end_form();

// Fieldset folding
echo_fieldset_folding_js();
