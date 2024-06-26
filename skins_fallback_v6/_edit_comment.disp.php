<?php
/**
 * This is the template that displays the edit comment form. It gets POSTed to /htsrv/action.php.
 *
 * Note: don't code this URL by hand, use the template functions to generate it!
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Collection, $Blog, $edited_Comment, $comment_Item, $comment_content;
global $display_params, $admin_url, $dummy_fields;


if (empty($comment_Item)) {
    $comment_Item = &$edited_Comment->get_Item();
}

$form_action = url_add_param($admin_url, 'ctrl=comments');

$display_params = array_merge($display_params, [
    'textarea_lines' => 16,
    'form_comment_text' => T_('Comment content'),
]);

$Form = new Form($form_action, 'comment_edit', 'post');

$Form->begin_form('evo_comment');

$Form->add_crumb('comment');
$Form->hidden('blog', $Blog->ID);
$Form->hidden('mname', 'collections');
$Form->hidden('action_type', 'comment');
$Form->hidden('comment_ID', $edited_Comment->ID);
$Form->hidden('redirect_to', $edited_Comment->get_permanent_url());

if (check_user_perm('admin', 'restricted') &&
    check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID)) { // ------------------------------------ TIME STAMP -------------------------------------
    $Form->hidden('comment_issue_date', mysql2localedate($edited_Comment->get('date')));
    $Form->hidden('comment_issue_time', substr($edited_Comment->get('date'), 11));
}

$Form->output = false;
$edit_links = $Form->begin_fieldset(get_request_title(array_merge([
    'edit_links_template' => [
        'before' => '<span class="pull-right panel_heading_action_icons">',
        'after' => '</span>',
        'advanced_link_class' => 'btn btn-info btn-sm',
        'close_link_class' => 'btn btn-default btn-sm',
    ],
], $params)));
$Form->output = true;
$advanced_edit_text = T_('Advanced editing');
$edit_links = preg_replace('/ ' . $advanced_edit_text . '/', '<span class="hidden-xs">$0</span>', $edit_links);
$cancel_text = T_('Cancel editing');
$edit_links = preg_replace('/ ' . $cancel_text . '/', '<span class="hidden-xs">$0</span>', $edit_links);
echo $edit_links;

$Form->info(T_('In response to'), $comment_Item->get_title());

if ($Blog->get_setting('threaded_comments')) { // Display a reply comment ID only when this feature is enabled in blog settings
    $Form->text_input('in_reply_to_cmt_ID', $edited_Comment->in_reply_to_cmt_ID, 10, T_('In reply to comment ID'), T_('(leave blank for normal comments)'));
}

if ($edited_Comment->get_author_User()) {
    $Form->info(T_('Author'), $edited_Comment->get_author());
} else {
    $Form->text_input('newcomment_author', $edited_Comment->author, 20, T_('Author'), '', [
        'maxlength' => 100,
        'style' => 'width: 100%;',
    ]);
    $Form->email_input('newcomment_author_email', $edited_Comment->author_email, 20, T_('Email'), [
        'maxlength' => 255,
        'style' => 'width: 100%;',
    ]);
    $Form->text_input('newcomment_author_url', $edited_Comment->author_url, 20, T_('Website URL'), '', [
        'maxlength' => 255,
        'style' => 'width: 100%;',
    ]);
}

if ($comment_Item->can_rate() || ! empty($edited_Comment->rating)) { // Rating is editable
    $edited_Comment->rating_input([
        'before' => $Form->begin_field('comment_rating_field', T_('Rating'), true),
        'after' => $Form->inputend . $Form->fieldend,
    ]);
}

ob_start();
echo '<div class="comment_toolbars">';
// CALL PLUGINS NOW:
$Plugins->trigger_event('DisplayCommentToolbar', [
    'Comment' => &$edited_Comment,
    'Item' => &$comment_Item,
]);
echo '</div>';
$comment_toolbar = ob_get_clean();

// CALL PLUGINS NOW:
ob_start();
$Plugins->trigger_event('DisplayEditorButton', [
    'target_type' => 'Comment',
    'target_object' => $edited_Comment,
    'content_id' => $dummy_fields['content'],
    'edit_layout' => 'inskin',
]);
$quick_setting_switch = ob_get_clean();

// Message field:
$form_inputstart = $Form->inputstart;
$form_inputend = $Form->inputend;
$Form->inputstart .= $comment_toolbar;
$Form->inputend = $quick_setting_switch . $Form->inputend;
$Form->textarea_input('content', $comment_content, $display_params['textarea_lines'], $display_params['form_comment_text'], [
    'cols' => 38,
    'rows' => 11,
    'class' => 'evo_comment_field' . (check_autocomplete_usernames($edited_Comment) ? ' autocomplete_usernames' : ''),
    'id' => $dummy_fields['content'],
    'maxlength' => $Blog->get_setting('comment_maxlen'),
]);
$Form->inputstart = $form_inputstart;
$Form->inputend = $form_inputend;

// set b2evoCanvas for plugins
echo '<script>var b2evoCanvas = document.getElementById( "' . $dummy_fields['content'] . '" );</script>';


// Display renderers checkboxes ( Note: This contains inputs )
$comment_renderer_checkboxes = $edited_Comment->renderer_checkboxes(null, false);
if (! empty($comment_renderer_checkboxes)) {
    $Form->info(T_('Text Renderers'), $comment_renderer_checkboxes);
}

$Form->end_fieldset();

// Display attachments fieldset:
$Form->attachments_fieldset($edited_Comment);

echo '<div class="edit_actions form-group text-center">';
echo_comment_status_buttons($Form, $edited_Comment);
echo '</div>';
$Form->end_form();

?>
<script>
	function switch_edit_view()
	{
		var form = document.getElementById('comment_edit');
		if( form )
		{
			jQuery(form).append( '<input type="hidden" name="action" value="switch_view" />');
			form.submit();
		}
		return false;
	}
</script>
<?php

// JS code for status dropdown submit button
echo_status_dropdown_button_js('comment');

// Insert image modal window:
echo_image_insert_modal();
?>
