<?php
/**
 * This file implements the UI view for Emails > Campaigns > Edit > Plain-text version
 *
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

global $admin_url, $tab;
global $edited_EmailCampaign, $Plugins, $UserSettings;

$Form = new Form(null, 'campaign_form');
$Form->begin_form('fform');

$Form->add_crumb('campaign');
$Form->hidden('ctrl', 'campaigns');
$Form->hidden('current_tab', $tab);
$Form->hidden('ecmp_ID', $edited_EmailCampaign->ID);

$Form->begin_fieldset(sprintf(TB_('Plain-text for: %s'), $edited_EmailCampaign->dget('name')) . get_manual_link('campaign-plaintext-panel'));

$Form->text_input('ecmp_email_title', $edited_EmailCampaign->get('email_title'), 60, TB_('Email title'), TB_('as it will appear in your subscriber\'s inboxes'), [
    'maxlength' => 255,
    'required' => true,
]);

$Form->textarea_input('ecmp_email_plaintext', $edited_EmailCampaign->get('email_plaintext'), 20, TB_('Plain-text message'), [
    'required' => true,
]);

$Form->end_fieldset();

$buttons = [];
if (check_user_perm('emails', 'edit')) {	// User must has a permission to edit emails:
    $buttons[] = ['submit', 'actionArray[save]', TB_('Save & continue') . ' >>', 'SaveButton'];
    $buttons[] = ['submit', 'actionArray[resync]', TB_('Resync from HTML'), 'SaveButton btn-info', 'return confirm( \'' . TS_('WARNING: if you continue, all manual edits you made to the plain-text version will be lost.') . '\' )'];
}
$Form->end_form($buttons);
