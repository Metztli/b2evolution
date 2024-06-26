<?php
/**
 * This file display the form to create sample collections for testing moderation
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

global $perm_management, $allow_access;

$Form = new Form(null, 'create_comments', 'post', 'compact');

$Form->global_icon(TB_('Cancel') . '!', 'close', regenerate_url('action'));

$Form->begin_form('fform', TB_('Create sample collections for testing'));

$Form->add_crumb('tools');
$Form->hidden('ctrl', 'tools');
$Form->hidden('action', 'create_sample_collections');
$Form->hidden('tab3', get_param('tab3'));

$Form->text_input('num_collections', 30, 6, TB_('How many collections'), '', [
    'maxlength' => 6,
    'required' => true,
]);

$Form->checklist(
    [
        ['perm_management[]', 'simple', TB_('Simple permissions'), is_null($perm_management) || in_array('simple', $perm_management)],
        ['perm_management[]', 'advanced', TB_('Advanced permissions'), is_null($perm_management) || in_array('advanced', $perm_management)],
    ],
    'perm_management',
    TB_('Permission management'),
    'mark_only',
    false,
    [
        'note' => TB_('Note: For each collection it creates, the tool will randomly select between the allowed (checked) options above'),
    ]
);

$Form->checklist(
    [
        ['allow_access[]', 'public', TB_('Everyone (Public Blog)'), is_null($allow_access) || in_array('public', $allow_access)],
        ['allow_access[]', 'users', TB_('Logged in users'), is_null($allow_access) || in_array('users', $allow_access)],
        ['allow_access[]', 'members', TB_('Owner / Member only'), is_null($allow_access) || in_array('members', $allow_access)],
    ],
    'allow_access',
    TB_('Allow access to'),
    'mark_only',
    false,
    [
        'note' => TB_('Note: For each collection it creates, the tool will randomly select between the allowed (checked) options above'),
    ]
);

$Form->end_form([['submit', 'submit', TB_('Create'), 'SaveButton']]);
