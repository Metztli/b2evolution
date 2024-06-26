<?php
/**
 * This file implements the UI view for Emails > Campaigns > Edit > HTML
 *
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

global $admin_url, $tab;
global $edited_EmailCampaign, $Plugins, $UserSettings;

echo_image_insert_modal();

$Form = new Form(null, 'campaign_form');
$Form->begin_form('fform');

$Form->add_crumb('campaign');
$Form->hidden('ctrl', 'campaigns');
$Form->hidden('current_tab', $tab);
$Form->hidden('ecmp_ID', $edited_EmailCampaign->ID);

$Form->begin_fieldset(sprintf(TB_('Compose message for: %s'), $edited_EmailCampaign->dget('name')) . get_manual_link('campaign-compose-panel'));
$Form->text_input('ecmp_email_title', $edited_EmailCampaign->get('email_title'), 60, TB_('Email title'), TB_('as it will appear in your subscriber\'s inboxes'), [
    'maxlength' => 255,
    'required' => true,
]);
$Form->text_input('ecmp_email_defaultdest', $edited_EmailCampaign->get('email_defaultdest'), 60, TB_('Default destination'), '', [
    'maxlength' => 255,
]);

// Plugin toolbars:
ob_start();
echo '<div class="email_toolbars">';
// CALL PLUGINS NOW:
$Plugins->trigger_event('DisplayEmailToolbar', [
    'target_type' => 'EmailCampaign',
    'EmailCampaign' => &$edited_EmailCampaign,
]);
echo '</div>';
$email_toolbar = ob_get_clean();

// Plugin buttons:
ob_start();
echo '<div class="edit_actions">';
echo '<div class="pull-left" style="display: flex; flex-direction: row; align-items: center;">';
// CALL PLUGINS NOW:
$Plugins->trigger_event('AdminDisplayEditorButton', [
    'target_type' => 'EmailCampaign',
    'target_object' => $edited_EmailCampaign,
    'content_id' => 'ecmp_email_text',
    'edit_layout' => 'expert',
]);

echo '</div>';
echo '</div>';
$email_plugin_buttons = ob_get_clean();

$form_inputstart = $Form->inputstart;
$form_inputend = $Form->inputend;
$Form->inputstart .= $email_toolbar;
$Form->inputend = $email_plugin_buttons . $Form->inputend;
$Form->textarea_input('ecmp_email_text', $edited_EmailCampaign->get('email_text'), 20, TB_('HTML Message'), [
    'required' => true,
]);
$Form->inputstart = $form_inputstart;
$Form->inputend = $form_inputend;



// set b2evoCanvas for plugins:
echo '<script>var b2evoCanvas = document.getElementById( "ecmp_email_text" );</script>';

// Display renderers
$current_renderers = ! empty($edited_EmailCampaign) ? $edited_EmailCampaign->get_renderers_validated() : ['default'];
$email_renderer_checkboxes = $Plugins->get_renderer_checkboxes($current_renderers, [
    'setting_name' => 'email_apply_rendering',
]);
if (! empty($email_renderer_checkboxes)) {
    $Form->info(TB_('Text Renderers'), $email_renderer_checkboxes);
}
$Form->end_fieldset();


// ####################### ATTACHMENTS/LINKS #########################
$Form->attachments_fieldset($edited_EmailCampaign);


$buttons = [];
if (check_user_perm('emails', 'edit')) { // User must has a permission to edit emails
    $buttons[] = ['submit', 'actionArray[save]', TB_('Save & continue') . ' >>', 'SaveButton'];
}
$Form->end_form($buttons);

?>
<script>
function toggleWYSIWYGSwitch( val )
{
	if( val )
	{
		jQuery( 'p#active_wysiwyg_switch' ).show();
		jQuery( 'p#disable_wysiwyg_switch' ).hide();
	}
	else
	{
		jQuery( 'p#active_wysiwyg_switch' ).hide();
		jQuery( 'p#disable_wysiwyg_switch' ).show();
	}
}
</script>