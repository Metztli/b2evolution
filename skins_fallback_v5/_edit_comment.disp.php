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

$Form->begin_form('bComment');

$Form->add_crumb('comment');
$Form->hidden('blog', $Blog->ID);
$Form->hidden('mname', 'collections');
$Form->hidden('action_type', 'comment');
$Form->hidden('comment_ID', $edited_Comment->ID);
$Form->hidden('redirect_to', $edited_Comment->get_permanent_url());

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

ob_start();
echo '<div class="comment_toolbars">';
// CALL PLUGINS NOW:
$Plugins->trigger_event('DisplayCommentToolbar', [
    'Comment' => &$edited_Comment,
    'Item' => &$comment_Item,
]);
echo '</div>';
$comment_toolbar = ob_get_clean();

// Message field:
$form_inputstart = $Form->inputstart;
$Form->inputstart .= $comment_toolbar;
$Form->textarea_input('content', $comment_content, $display_params['textarea_lines'], $display_params['form_comment_text'], [
    'cols' => 38,
    'class' => 'bComment' . (check_autocomplete_usernames($edited_Comment) ? ' autocomplete_usernames' : ''),
    'id' => $dummy_fields['content'],
    'maxlength' => $Blog->get_setting('comment_maxlen'),
]);
$Form->inputstart = $form_inputstart;

// set b2evoCanvas for plugins
echo '<script>var b2evoCanvas = document.getElementById( "' . $dummy_fields['content'] . '" );</script>';

if (check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID)) { // ------------------------------------ TIME STAMP -------------------------------------
    $Form->begin_fieldset('', [
        'id' => 'comment_date_field',
    ]);
    echo $Form->begin_field(null, T_('Comment date'));
    $Form->switch_layout('blockspan');
    $Form->date_input('comment_issue_date', $edited_Comment->date, '', [
        'size' => "10",
    ]);
    $Form->time_input('comment_issue_time', $edited_Comment->date, '', [
        'size' => "10",
    ]);
    $Form->switch_layout(null);
    $Form->end_fieldset();
}

if ($comment_Item->can_rate() || ! empty($edited_Comment->rating)) { // Rating is editable
    $edited_Comment->rating_input([
        'before' => $Form->begin_field('comment_rating_field', T_('Rating'), true),
        'after' => $Form->inputend . $Form->fieldend,
    ]);
}

$comment_Item = &$edited_Comment->get_Item();
// Comment status cannot be more than post status, restrict it:
$restrict_max_allowed_status = ($comment_Item ? $comment_Item->status : '');

// Get those statuses which are not allowed for the current User to create comments in this blog
$exclude_statuses = array_merge(get_restricted_statuses($Blog->ID, 'blog_comment!', 'edit', $edited_Comment->status, $restrict_max_allowed_status), ['redirected', 'trash']);
// Get allowed visibility statuses
$sharing_options = get_visibility_statuses('radio-options', $exclude_statuses);
if (count($sharing_options) == 1) { // Only one visibility status is available, don't show radio but set hidden field
    $Form->hidden('comment_status', $sharing_options[0][0]);
} else { // Display visibiliy options
    $Form->radio('comment_status', $edited_Comment->status, $sharing_options, T_('Visibility'), true);
}

// Display renderers checkboxes ( Note: This contains inputs )
$comment_renderer_checkboxes = $edited_Comment->renderer_checkboxes(null, false);
if (! empty($comment_renderer_checkboxes)) {
    $Form->info(T_('Text Renderers'), $comment_renderer_checkboxes);
}

// Display comment attachments
$LinkOwner = new LinkComment($edited_Comment);
if ($LinkOwner->count_links()) { // there are attachments to display
    if (check_user_perm('files', 'view') && check_user_perm('admin', 'restricted')) {
        $Form->begin_fieldset(T_('Attachments'));
        display_attachments($LinkOwner);
        $Form->end_fieldset();
    } else {
        $Form->info(T_('Attachments'), T_('You do not have permission to edit file attachments for this comment'));
    }
}

echo '<div class="center margin2ex">';
$Form->submit(['actionArray[update]', T_('Save Changes!'), 'SaveButton', '']);
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

?>
