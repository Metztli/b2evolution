<?php
/**
 * This file implements the UI view to add/remove tags to/from users list.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $admin_url;

$Form = new Form(null, 'users_automation_checkchanges');

$Form->switch_template_parts([
    'labelclass' => 'control-label col-sm-6',
    'inputstart' => '<div class="controls col-sm-6">',
    'inputstart_radio' => '<div class="controls col-sm-6">',
    'infostart' => '<div class="controls col-sm-6"><div class="form-control-static">',
]);

$Form->title_fmt = '<span style="float:right">$global_icons$</span><div>$title$</div>' . "\n";

$Form->begin_form('fform');

$Form->add_crumb('users');
$Form->hidden_ctrl();

// A link to close popup window:
$close_icon = action_icon(TB_('Close this window'), 'close', '', '', 0, 0, [
    'id' => 'close_button',
    'class' => 'floatright',
]);

$Form->begin_fieldset(TB_('Add/Remove tags...') . get_manual_link('add-remove-user-tags') . $close_icon);

$Form->usertag_input('add_user_tags', '', 40, TB_('Tags to add to each user'), '', [
    'style' => 'width:100%',
]);

$Form->usertag_input('remove_user_tags', '', 40, TB_('Tags to remove from each user'), '', [
    'style' => 'width:100%',
]);

$Form->end_fieldset();

$Form->button(['', 'actionArray[update_tags]', TB_('Make changes now!'), 'SaveButton']);

$Form->end_form();
