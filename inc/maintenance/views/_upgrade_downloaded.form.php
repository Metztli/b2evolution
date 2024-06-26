<?php
/**
 * This file is part of b2evolution - {@link http://b2evolution.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @package maintenance
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $block_item_Widget, $action_success, $download_success;

if (isset($block_item_Widget)) {
    $block_item_Widget->disp_template_replaced('block_end');
}

$Form = new Form(null, 'upgrade_form', 'post');

$Form->add_crumb('upgrade_started'); // In case we want to "Force download" again
$Form->add_crumb('upgrade_downloaded'); // In case we want to "Unzip"
$Form->hiddens_by_key(get_memorized('action'));

$Form->begin_form('fform');

// Display the form buttons
$Form->begin_fieldset(TB_('Actions'));

$form_buttons = [];
if ($action_success && $download_success) { // Init a button to unzip
    $form_buttons[] = ['submit', 'actionArray[unzip]', TB_('Unzip package'), 'SaveButton'];
} elseif ($download_success) { // Init the buttons to select next action
    $form_buttons[] = ['submit', 'actionArray[unzip]', TB_('Skip Download'), 'SaveButton'];
    $form_buttons[] = ['submit', 'actionArray[force_download]', TB_('Force New Download'), 'SaveButton btn-warning'];
} else { // Init a button to back step
    $form_buttons[] = ['submit', 'actionArray[start]', TB_('Back to Package Selection'), 'SaveButton'];
}

$Form->end_form($form_buttons);
