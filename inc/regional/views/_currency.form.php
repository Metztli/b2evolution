<?php
/**
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2016 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var Currency
 */
global $edited_Currency;

// Determine if we are creating or updating...
global $action;
$creating = is_create_action($action);

$Form = new Form(null, 'currency_checkchanges', 'post', 'compact');

$Form->global_icon(TB_('Delete this currency!'), 'delete', regenerate_url('action', 'action=delete&amp;' . url_crumb('currency')));
$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action'));

$Form->begin_form('fform', ($creating ? TB_('New currency') : TB_('Currency')) . get_manual_link('currencies-editing'));

$Form->add_crumb('currency');
$Form->hiddens_by_key(get_memorized('action' . ($creating ? ',curr_ID' : ''))); // (this allows to come back to the right list order & page)

$Form->text_input('curr_code', $edited_Currency->code, 3, TB_('Code'), '', [
    'maxlength' => 3,
    'required' => true,
]);

$Form->text_input('curr_shortcut', $edited_Currency->shortcut, 8, TB_('Shortcut'), '', [
    'maxlength' => 8,
    'required' => true,
]);

$Form->text_input('curr_name', $edited_Currency->name, 40, TB_('Name'), '', [
    'maxlength' => 40,
    'required' => true,
]);

if ($creating) {
    $Form->end_form([['submit', 'actionArray[create]', TB_('Record'), 'SaveButton'],
        ['submit', 'actionArray[create_new]', TB_('Record, then Create New'), 'SaveButton'],
        ['submit', 'actionArray[create_copy]', TB_('Record, then Create Similar'), 'SaveButton']]);
} else {
    $Form->end_form([['submit', 'actionArray[update]', TB_('Save Changes!'), 'SaveButton']]);
}
