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

global $block_item_Widget, $action_success, $unzip_success, $upgrade_file, $upgrade_dir;

if (isset($block_item_Widget)) {
    $block_item_Widget->disp_template_replaced('block_end');
}

$Form = new Form(null, 'upgrade_form', 'post');

$Form->add_crumb('upgrade_downloaded'); // In case we want to "Force Unzip" again
$Form->add_crumb('upgrade_is_ready'); // In case we want to continue
$Form->add_crumb('upgrade_started'); // In case we want to back to download package
$Form->hiddens_by_key(get_memorized('action'));

$Form->begin_form('fform');

// Display the form buttons
$Form->begin_fieldset(TB_('Actions'));

$form_buttons = [];
if ($action_success && $unzip_success) { // Init a button to next step
    $form_buttons[] = ['submit', 'actionArray[ready]', TB_('Continue'), 'SaveButton'];
} elseif ($unzip_success) { // Init the buttons to select next action
    $form_buttons[] = ['submit', 'actionArray[ready]', TB_('Skip Unzip'), 'SaveButton'];
    if (file_exists($upgrade_file) && check_user_perm('files', 'all')) {	// Allow to unzip only if current user has a permission to edit all files:
        $form_buttons[] = ['submit', 'actionArray[force_unzip]', TB_('Force New Unzip'), 'SaveButton btn-warning'];
    }
} else { // Init a button to back step
    if (file_exists($upgrade_dir)) {	// If zip file was already unzipped before:
        $form_buttons[] = ['submit', 'actionArray[ready]', TB_('Skip Unzip'), 'SaveButton'];
    }
    $form_buttons[] = ['submit', 'actionArray[' . (get_param('tab') == 'git' ? 'export_git' : 'download') . ']', TB_('Back to download package'), 'SaveButton'];
}

$Form->end_form($form_buttons);
