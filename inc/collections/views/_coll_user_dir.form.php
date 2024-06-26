<?php
/**
 * This file implements the UI view for the Collection features user directory properties.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var Blog
 */
global $edited_Blog;


$Form = new Form(null, 'coll_other_checkchanges');

$Form->begin_form('fform');

$Form->add_crumb('collection');
$Form->hidden_ctrl();
$Form->hidden('action', 'update');
$Form->hidden('tab', 'userdir');
$Form->hidden('blog', $edited_Blog->ID);

$Form->begin_fieldset(TB_('User directory') . get_manual_link('user-directory-other'));

$Form->checkbox('userdir_enable', $edited_Blog->get_setting('userdir_enable'), TB_('Enable User directory'));

$Form->checklist([
    ['userdir_filter_restrict_to_members', 1, TB_('Restrict to members'), $edited_Blog->get_setting('userdir_filter_restrict_to_members')],
    ['userdir_filter_name', 1, TB_('Name') . ' / ' . TB_('Username'), $edited_Blog->get_setting('userdir_filter_name')],
    ['userdir_filter_email', 1, TB_('Email'), $edited_Blog->get_setting('userdir_filter_email')],
    ['userdir_filter_country', 1, TB_('Country'), $edited_Blog->get_setting('userdir_filter_country')],
    ['userdir_filter_region', 1, TB_('Region'), $edited_Blog->get_setting('userdir_filter_region')],
    ['userdir_filter_subregion', 1, TB_('Subregion'), $edited_Blog->get_setting('userdir_filter_subregion')],
    ['userdir_filter_city', 1, TB_('City'), $edited_Blog->get_setting('userdir_filter_city')],
    ['userdir_filter_age_group', 1, TB_('Age group'), $edited_Blog->get_setting('userdir_filter_age_group')],
], 'userdir_filters', TB_('Enabled filters'));

if (isset($GLOBALS['files_Module'])) {
    load_funcs('files/model/_image.funcs.php');

    $Form->begin_line(TB_('Profile picture'), 'userdir_picture');
    $Form->checkbox('userdir_picture', $edited_Blog->get_setting('userdir_picture'), '');
    $Form->select_input_array('image_size_user_list', $edited_Blog->get_setting('image_size_user_list'), get_available_thumb_sizes(), '', '', [
        'force_keys_as_values' => true,
    ]);
    $Form->end_line();
}

$Form->checkbox('userdir_login', $edited_Blog->get_setting('userdir_login'), /* TRANS: noun */ TB_('Login'));
$Form->checkbox('userdir_firstname', $edited_Blog->get_setting('userdir_firstname'), TB_('First name'));
$Form->checkbox('userdir_lastname', $edited_Blog->get_setting('userdir_lastname'), TB_('Last name'));
$Form->checkbox('userdir_nickname', $edited_Blog->get_setting('userdir_nickname'), TB_('Nickname'));
$Form->checkbox('userdir_fullname', $edited_Blog->get_setting('userdir_fullname'), TB_('Full name'));

$Form->begin_line(TB_('Country'), 'userdir_country');
$Form->checkbox('userdir_country', $edited_Blog->get_setting('userdir_country'), '');
$Form->select_input_array('userdir_country_type', $edited_Blog->get_setting('userdir_country_type'), [
    'flag' => TB_('Flag'),
    'name' => TB_('Name'),
    'both' => TB_('Both'),
], '', '', [
    'force_keys_as_values' => true,
]);
$Form->end_line();
$Form->checkbox('userdir_region', $edited_Blog->get_setting('userdir_region'), TB_('Region'));
$Form->checkbox('userdir_subregion', $edited_Blog->get_setting('userdir_subregion'), TB_('Sub-region'));
$Form->checkbox('userdir_city', $edited_Blog->get_setting('userdir_city'), TB_('City'));

$Form->checkbox('userdir_phone', $edited_Blog->get_setting('userdir_phone'), TB_('Phone'));
$Form->checkbox('userdir_soclinks', $edited_Blog->get_setting('userdir_soclinks'), TB_('Social links'));
$Form->begin_line(TB_('Last seen date'), 'userdir_lastseen');
$Form->checkbox('userdir_lastseen', $edited_Blog->get_setting('userdir_lastseen'), '');
$Form->select_input_array('userdir_lastseen_view', $edited_Blog->get_setting('userdir_lastseen_view'), [
    'exact_date' => TB_('exact date'),
    'blurred_date' => TB_('blurred date'),
], '', '', [
    'force_keys_as_values' => true,
]);
$Form->text_input('userdir_lastseen_cheat', $edited_Blog->Get_setting('userdir_lastseen_cheat'), 4, TB_('Cheat by'), 'days');
$Form->end_line();

$Form->end_fieldset();

$Form->end_form([['submit', 'submit', TB_('Save Changes!'), 'SaveButton']]);
?>
<script>
	var selLastSeenView = jQuery( 'select#userdir_lastseen_view' );
	var selLastSeenCheat = jQuery( 'input#userdir_lastseen_cheat' );

	var checkLastSeen = function()
			{
				if( selLastSeenView.val() == 'blurred_date' )
				{
					selLastSeenCheat.removeAttr( 'disabled' );
				}
				else
				{
					selLastSeenCheat.attr( 'disabled', 'disabled' );
				}
			};

	selLastSeenView.on( 'change', checkLastSeen );

	checkLastSeen();
</script>