<?php
/**
 * This is the template that displays the item/post form for anonymous user
 *
 * This file is not meant to be called directly.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2017 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $Blog, $Settings, $dummy_fields;

if (is_logged_in() || ! $Blog->get_setting('post_anonymous')) {	// This form is allowed only for anonymous users and only with enabled collection setting, Exit here:
    return;
}

$new_Item = get_session_Item(0, true);

$params = array_merge([
    'form_params' => [], // Use to change a structre of form, i.e. fieldstart, fieldend and etc.
    'item_new_form_start' => '<h3>' . sprintf(T_('New [%s]'), $new_Item->get_type_setting('name')) . '</h3>',
    'item_new_form_end' => '',
    'item_new_submit_text' => T_('Create post'),
], $params);

echo $params['item_new_form_start'];

$Form = new Form(get_htsrv_url() . 'action.php');

$Form->switch_template_parts($params['form_params']);

$Form->begin_form();

$Form->hidden('mname', 'collections');
$Form->add_crumb('collections_create_post');
$Form->hidden('blog', $Blog->ID);
$Form->hidden('cat', get_param('cat'));
$Form->hidden('item_typ_ID', $new_Item->ityp_ID);

$Form->switch_layout('fields_table');
$Form->begin_fieldset();

$Form->text_input($dummy_fields['name'], (isset($new_Item->temp_user_name) ? $new_Item->temp_user_name : ''), 40, T_('Name'), sprintf(T_('<a %s>Click here to log in</a> if you already have an account on this site.'), 'href="' . get_login_url('new item form', $Blog->get('url')) . '" style="font-weight:bold"'), [
    'maxlength' => 100,
    'required' => true,
    'style' => 'width:auto',
]);

$Form->email_input($dummy_fields['email'], (isset($new_Item->temp_user_email) ? $new_Item->temp_user_email : ''), 40, T_('Email'), [
    'maxlength' => 255,
    'required' => true,
    'style' => 'width:auto',
    'note' => T_('Your email address will <strong>not</strong> be revealed on this site.'),
]);

// Title input:
$use_title = $new_Item->get_type_setting('use_title');
if ($use_title != 'never') {
    $Form->text_input('post_title', $new_Item->get('title'), 20, T_('Title'), '', [
        'maxlength' => 255,
        'style' => 'width: 100%;',
        'required' => ($use_title == 'required'),
    ]);
}

// Display plugin captcha for item form before textarea:
$Plugins->display_captcha([
    'Form' => &$Form,
    'form_type' => 'item',
    'form_position' => 'before_textarea',
    'form_use_fieldset' => false,
]);

$Form->end_fieldset();
$Form->switch_layout(null);

if ($new_Item->get_type_setting('use_text') != 'never') {	// Display textarea for a post text:
    // --------------------------- TOOLBARS ------------------------------------
    echo '<div class="edit_toolbars">';
    // CALL PLUGINS NOW:
    $Plugins->trigger_event('AdminDisplayToolbar', [
        'edit_layout' => 'expert',
        'Item' => $new_Item,
    ]);
    echo '</div>';

    // ---------------------------- TEXTAREA -------------------------------------
    $Form->switch_layout('none');
    $Form->fieldstart = '<div class="edit_area">';
    $Form->fieldend = "</div>\n";
    $Form->textarea_input('content', $new_Item->get('content'), 16, null, [
        'cols' => 50,
        'id' => 'itemform_post_content',
        'class' => 'autocomplete_usernames link_attachment_dropzone',
    ]);
    $Form->switch_layout(null);
    ?>
	<script>
		<!--
		// This is for toolbar plugins
		var b2evoCanvas = document.getElementById('itemform_post_content');
		//-->
	</script>

	<?php
    echo '<div class="edit_plugin_actions">';
    // CALL PLUGINS NOW:
    $Plugins->trigger_event('DisplayEditorButton', [
        'target_type' => 'Item',
        'target_object' => $new_Item,
        'content_id' => 'itemform_post_content',
        'edit_layout' => 'inskin',
    ]);
    echo '</div>';

    // =================================== INSTRUCTION ====================================
    if ($new_Item->get_type_setting('front_order_instruction') && $new_Item->get_type_setting('instruction')) {
        echo '<br><div class="alert alert-info fade in evo_instruction">' . $new_Item->get_type_setting('instruction') . '</div>';
    }

    // Display renderers:
    $item_renderer_checkboxes = ($Blog->get_setting('in_skin_editing_renderers') ? $new_Item->get_renderer_checkboxes() : false);
    if (! empty($item_renderer_checkboxes)) {
        $Form->info(T_('Text Renderers'), $item_renderer_checkboxes);
    }
}

// Display additional fieldsets from active plugins:
$Plugins->trigger_event('DisplayItemFormFieldset', [
    'Form' => &$Form,
    'Item' => &$new_Item,
    'form_use_fieldset' => false,
]);

// Display plugin captcha for item form before submit button:
$Plugins->display_captcha([
    'Form' => &$Form,
    'form_type' => 'item',
    'form_position' => 'before_submit_button',
    'form_use_fieldset' => false,
]);

$Form->end_form([
    [
        'name' => 'actionArray[create_post]',
        'class' => 'submit SaveButton',
        'value' => $params['item_new_submit_text'],
    ],
]);

echo $params['item_new_form_end'];
?>
