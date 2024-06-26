<?php
/**
 * This file is part of b2evolution - {@link http://b2evolution.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2016 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @package maintenance
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $UserSettings;

$Form = new Form(null, 'upgrade_form', 'post', 'compact');

$Form->add_crumb('upgrade_started');
$Form->hiddens_by_key(get_memorized('action'), ['git_url', 'git_branch', 'git_user', 'git_password']);

$Form->begin_form('fform', TB_('Upgrade from Git') . get_manual_link('upgrade-from-git'));

$Form->text_input('git_url', $UserSettings->get('git_upgrade_url'), 80, TB_('URL of repository'), sprintf(TB_('E.g. %s'), '<code>https://github.com/b2evolution/b2evolution.git</code>'), [
    'maxlength' => 300,
    'required' => true,
]);
$Form->text_input('git_branch', $UserSettings->get('git_upgrade_branch'), 80, TB_('Branch'), sprintf(TB_('E.g. %s'), '<code>develop</code>'), [
    'maxlength' => 300,
]);

$Form->end_form([['submit', 'actionArray[export_git]', TB_('Export from Git...'), 'SaveButton']]);
