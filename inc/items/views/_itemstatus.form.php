<?php
/**
 * This file display the item status form
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
 * @var ItemStatus
 */
global $edited_ItemStatus;

global $action, $admin_url;

$ityp_usage = param('ityp_usage', 'string');

// Determine if we are creating or updating...
$creating = is_create_action($action);

$Form = new Form(null, 'itemstatus_checkchanges', 'post');

$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action'));

$Form->begin_form('fform', ($creating ? TB_('New post status') : TB_('Post status')) . get_manual_link('managing-item-statuses-form'));

$Form->add_crumb('itemstatus');
$Form->hiddens_by_key(get_memorized('action' . ($creating ? ',pst_ID' : '')));

$Form->begin_fieldset(TB_('General'));
$Form->text_input('pst_name', $edited_ItemStatus->get('name'), 30, TB_('Name'), '', [
    'required' => true,
]);
$Form->text_input('pst_order', $edited_ItemStatus->get('order'), 30, TB_('Order'), '', [
    'type' => 'number',
]);
$Form->end_fieldset();

/**
 * Callback to add filters on top of the result set
 *
 * @param Form
 */
function filter_itemtypes_results_block(&$Form)
{
    $ityp_usage = param('ityp_usage', 'string');

    $Form->switch_layout('blockspan');
    echo '<div class="form-inline">';

    $ItemTypeCache = &get_ItemTypeCache();
    $item_usage_options = [
        TB_('All') => '',
    ] + $ItemTypeCache->get_usage_option_array();

    $options_str = '';
    foreach ($item_usage_options as $usage_group => $rows) {
        $group_key = str_replace(' ', '_', strtolower($usage_group));
        $options_str .= '<option style="font-weight: bold; font-style: italic;" value="' . $group_key . '"' .
                ($ityp_usage == $group_key ? ' selected="selected"' : '') . '>' . $usage_group . '</option>';
        if (! empty($rows)) {
            foreach ($rows as $key => $value) {
                $options_str .= '<option value="' . $key . '"' . ($ityp_usage == $key ? ' selected="selected"' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;' . $value . '</option>';
            }
        }
    }

    $Form->select_input_options('ityp_usage', $options_str, TB_('Usage'));
    echo '</div>';
    $Form->switch_layout(null);
}

$SQL = new SQL();
if ($edited_ItemStatus->ID) {
    $SQL->SELECT('ityp_ID, ityp_name, its_pst_ID');
    $SQL->FROM('T_items__type');
    $SQL->FROM_add('JOIN T_items__status');
    $SQL->FROM_add('LEFT JOIN T_items__status_type ON its_ityp_ID = ityp_ID AND its_pst_ID = pst_ID');
    $SQL->WHERE_or('pst_ID = ' . $edited_ItemStatus->ID);
} else {
    $SQL->SELECT('ityp_ID, ityp_name, NULL AS its_pst_ID');
    $SQL->FROM('T_items__type');
}
if (! empty($ityp_usage)) {
    $ItemTypeCache = &get_ItemTypeCache();
    $item_usage_options = [
        TB_('All') => '',
    ] + $ItemTypeCache->get_usage_option_array();

    $options = [];
    foreach ($item_usage_options as $usage_group => $rows) {
        $group_key = str_replace(' ', '_', strtolower($usage_group));
        $options[$group_key] = [];
        if (! empty($rows)) {
            foreach ($rows as $key => $value) {
                $options[$group_key][] = $key;
            }
        }
    }

    if (array_key_exists($ityp_usage, $options)) {
        $usage = $options[$ityp_usage];
    } else {
        $usage = $ityp_usage;
    }

    if (! empty($usage)) {
        if (is_array($usage)) {
            $SQL->WHERE_and('ityp_usage IN (' . $DB->quote($usage) . ')');
        } else {
            $SQL->WHERE_and('ityp_usage = ' . $DB->quote($usage));
        }
    }
}

$Results = new Results($SQL->get(), 'ityp_');
$Results->title = TB_('Item Types allowed for this Item Status') . get_manual_link('item-types-allowed-per-item-status');
$Results->Form = $Form;

$Results->filter_area = [
    'callback' => 'filter_itemtypes_results_block',
];


$Results->cols[] = [
    'th' => TB_('ID'),
    'th_class' => 'shrinkwrap',
    'td' => '$ityp_ID$',
    'td_class' => 'center',
];

function item_status_type_checkbox($row)
{
    $title = $row->ityp_name;
    $r = '<input type="checkbox"';
    $r .= ' name="type_' . $row->ityp_ID . '"';

    if (isset($row->its_pst_ID) && ! empty($row->its_pst_ID)) {
        $r .= ' checked="checked"';
    }

    $r .= ' class="checkbox" value="1" title="' . $title . '" />';

    return $r;
}

$Results->cols[] = [
    'th' => TB_('Allowed Item Type'),
    'th_class' => 'shrinkwrap',
    'td' => '%item_status_type_checkbox( {row} )%',
    'td_class' => 'center',
];

function get_name_for_itemtype($id, $name)
{
    if (check_user_perm('options', 'edit')) { // Not reserved id AND current User has permission to edit the global settings
        $ret_name = '<a href="' . regenerate_url('ctrl,action,ID,pst_ID', 'ctrl=itemtypes&amp;ityp_ID=' . $id . '&amp;action=edit') . '">' . $name . '</a>';
    } else {
        $ret_name = $name;
    }

    return '<strong>' . $ret_name . '</strong>';
}

$Results->cols[] = [
    'th' => TB_('Name'),
    'td' => '%get_name_for_itemtype( #ityp_ID#, #ityp_name# )%',
];

$Results->display_init();

$display_params = [
    'page_url' => $admin_url . '?ctrl=itemstatuses&pst_ID=' . $edited_ItemStatus->ID . '&action=edit',
];

$Results->checkbox_toggle_selectors = 'input[name^=type_]:checkbox';
$Results->display($display_params);


$item_type_IDs = [];
foreach ($Results->rows as $row) {
    $item_type_IDs[] = $row->ityp_ID;
}
$Form->hidden('action', 'edit'); // This parameter will be overriden by actionArray parameter below
$Form->hidden('item_type_IDs', implode(',', $item_type_IDs));

if ($creating) {
    $Form->end_form([['submit', 'actionArray[create]', TB_('Record'), 'SaveButton'],
        ['submit', 'actionArray[create_new]', TB_('Record, then Create New'), 'SaveButton'],
        ['submit', 'actionArray[create_copy]', TB_('Record, then Create Similar'), 'SaveButton']]);
} else {
    $Form->end_form([['submit', 'actionArray[update]', TB_('Save Changes!'), 'SaveButton']]);
}
