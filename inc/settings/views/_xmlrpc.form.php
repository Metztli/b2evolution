<?php
/**
 * This file implements the UI view for XML-RPC settings.
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


/**
 * @var GeneralSettings
 */
global $Settings;

global $baseurl;


$Form = new Form(null, 'remotepublish_checkchanges');

$Form->begin_form('fform');

$Form->add_crumb('globalsettings');
$Form->hidden('ctrl', 'remotepublish');
$Form->hidden('tab', 'xmlrpc');
$Form->hidden('action', 'update');

// fp> TODO: it would be awesome to be able to enable the different APIs individually
// that way you minimalize security/spam risks by enable just what you need.
$Form->begin_fieldset(TB_('Remote publishing') . get_manual_link('xml-rpc'));
$Form->checkbox_input('general_xmlrpc', $Settings->get('general_xmlrpc'), TB_('Enable XML-RPC'), [
    'note' => TB_('Enable the Movable Type, MetaWeblog, WordPress, Blogger and B2 XML-RPC publishing protocols.'),
]);
$Form->text_input('xmlrpc_default_title', $Settings->get('xmlrpc_default_title'), 50, TB_('Default title'), '<br />' . TB_('Default title for items created with a XML-RPC API that doesn\'t send a post title (e. g. the Blogger API).'), [
    'maxlength' => 255,
]);
$Form->end_fieldset();

if (check_user_perm('options', 'edit')) {
    $Form->end_form([['submit', '', TB_('Save Changes!'), 'SaveButton']]);
}
