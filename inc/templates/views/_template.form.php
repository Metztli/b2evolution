<?php
/**
 * This file display the template form
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 *
 * @package templates
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $edited_Template, $locales, $AdminUI;

// Determine if we are creating or updating...
global $action;
$creating = is_create_action($action);

$Form = new Form(null, 'template_checkchanges', 'post', 'compact');

$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action,tpl_ID,blog'));

if ($action == 'copy') {
    $fieldset_title = TB_('Duplicate template') . get_manual_link('template-form');
} else {
    $fieldset_title = $creating ? TB_('New Template') . get_manual_link('template-form') : TB_('Template') . get_manual_link('template-form');
}

$Form->begin_form('fform', $fieldset_title);

$Form->hidden('ctrl', 'templates');
$Form->add_crumb('template');
if ($action == 'copy') {
    $Form->hidden('action', 'duplicate');
    $Form->hidden('tpl_ID', $edited_Template->ID);
} else {
    $Form->hidden('action', $creating ? 'create' : 'update');
    if (! $creating) {
        $Form->hidden('tpl_ID', $edited_Template->ID);
    }
}

// Template name:
$Form->text_input('tpl_name', $edited_Template->get('name'), 50, TB_('Name'), '', [
    'maxlength' => 128,
    'required' => true,
]);

// Template code:
$Form->text_input('tpl_code', $edited_Template->get('code'), 25, TB_('Code'), '', [
    'maxlength' => 128,
]);

// Context:
$Form->select_input_array('tpl_context', $edited_Template->get('context'), get_template_contexts('raw'), TB_('Context'));

// Owner:
$GroupCache = &get_GroupCache();
$Form->select_object('tpl_owner_grp_ID', $edited_Template->get('owner_grp_ID'), $GroupCache, TB_('Owned by'));

// Base template ID:
$base_template_options = [
    null => '(' . TB_('None') . ')',
];
$SQL = new SQL('Get possible base templates');
$SQL->SELECT('tpl_ID, tpl_name');
$SQL->FROM('T_templates');
$SQL->WHERE('tpl_translates_tpl_ID IS NULL');
if ($action != 'copy') {
    $SQL->WHERE_and('NOT tpl_ID =' . $DB->quote($edited_Template->ID));
}
$SQL->ORDER_BY('tpl_name ASC');
$base_template_options += $DB->get_assoc($SQL->get());
$Form->select_input_array('tpl_translates_tpl_ID', $edited_Template->get('translates_tpl_ID'), $base_template_options, TB_('Translation of'), null, [
    'force_keys_as_values' => true,
]);

// Locale:
$locales_options = [];
foreach ($locales as $locale_key => $locale_data) {
    if ($locale_data['enabled'] || $locale_key == $edited_Template->get('locale')) {
        $locales_options[$locale_key] = $locale_key;
    }
}
$Form->select_input_array('tpl_locale', $edited_Template->get('locale'), $locales_options, TB_('Locale'));

// Template code:
$Form->textarea('tpl_template_code', $edited_Template->get('template_code'), 20, TB_('Template code'), '', 80, '', true);

$buttons = [];
if (check_user_perm('options', 'edit')) {	// Allow to save template if current User has a permission:
    if ($action == 'copy') {
        $buttons = [
            ['submit', 'actionArray[create]', TB_('Duplicate Template!'), 'SaveButton'],
            ['submit', 'actionArray[create_edit]', TB_('Duplicate and continue editing...'), 'SaveButton'],
        ];
    } else {
        $buttons = [
            ['submit', 'actionArray[' . ($creating ? 'create' : 'update') . ']', TB_('Save!'), 'SaveButton'],
            ['submit', 'actionArray[' . ($creating ? 'create' : 'update') . '_edit]', TB_('Save and continue editing...'), 'SaveButton'],
        ];
    }
}

$Form->end_form($buttons);
