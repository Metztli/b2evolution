<?php
/**
 * This file display the form to create sample base domains for testing
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

$Form = new Form(null, 'create_basedomains', 'post', 'compact');

$Form->global_icon(TB_('Cancel!'), 'close', regenerate_url('action'));

$Form->begin_form('fform', TB_('Create sample base domains for testing moderation'));

$Form->add_crumb('tools');
$Form->hidden('ctrl', 'tools');
$Form->hidden('action', 'create_sample_basedomains');
$Form->hidden('tab3', get_param('tab3'));

$Form->text_input('num_basedomains', 100, 50, TB_('How many base domains'), '', [
    'maxlength' => 11,
    'required' => true,
]);

$Form->end_form([['submit', 'submit', TB_('Create'), 'SaveButton']]);
