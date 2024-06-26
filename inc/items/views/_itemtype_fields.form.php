<?php
/**
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2018 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * @package evocore
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('items/model/_itemtype.class.php', 'ItemType');

global $edited_Itemtype, $custom_fields;

$Form = new Form(null, 'itemtype_select_fields');

$Form->begin_form('fform');

$source_custom_fields = $edited_Itemtype->get_custom_fields();

$custom_field_type_titles = [
    'double' => TB_('Numeric'),
    'computed' => TB_('Computed'),
    'varchar' => TB_('String'),
    'text' => TB_('Text'),
    'html' => 'HTML',
    'url' => TB_('URL'),
    'image' => TB_('Image'),
    'separator' => TB_('Separator'),
];

$custom_field_options = [];
foreach ($source_custom_fields as $source_custom_field) {
    $source_custom_field_data = [];
    foreach ($source_custom_field as $col_key => $col_value) {
        if (! in_array($col_key, ['ID', 'ityp_ID'])) {
            $source_custom_field_data['data-' . $col_key] = ($col_value === null ? '' : $col_value);
        }
    }
    $custom_field_options[] = ['custom_field', $source_custom_field['name'],
        '<b>' . $source_custom_field['label'] . '</b> ' .
        '<code>' . $source_custom_field['name'] . '</code> ' .
        '(' . $custom_field_type_titles[$source_custom_field['type']] . ')' .
        '<input type="hidden" name="cf_data"' . get_field_attribs_as_string($source_custom_field_data) . ' />',
        ! in_array($source_custom_field['name'], $custom_fields), // check automatically only fields which is not added on the requested form yet
        false,
        ($source_custom_field['public'] ? TB_('Public') : TB_('Private'))];
}

$Form->checklist($custom_field_options, '', TB_('Select fields'), false, false, [
    'input_prefix' =>
        '<span class="btn-group">' .
        '<input type="button" class="btn btn-default btn-xs" value="' . TB_('Check all') . '" onclick="jQuery( this ).closest( \'form\' ).find( \'input[type=checkbox]\' ).prop( \'checked\', true )" /> ' .
        '<input type="button" class="btn btn-default btn-xs" value="' . TB_('Uncheck all') . '" onclick="jQuery( this ).closest( \'form\' ).find( \'input[type=checkbox]\' ).prop( \'checked\', false )" /> ' .
        '<input type="button" class="btn btn-default btn-xs" value="' . TB_('Reverse') . '" onclick="jQuery( this ).closest( \'form\' ).find( \'input[type=checkbox]\' ).each( function() { jQuery( this ).prop( \'checked\', ! jQuery( this ).prop( \'checked\' ) ) } );"  />' .
        '</span>',
]);

$Form->end_form([['submit', 'actionArray[select_custom_fields]', TB_('Add fields now!'), 'SaveButton']]);
