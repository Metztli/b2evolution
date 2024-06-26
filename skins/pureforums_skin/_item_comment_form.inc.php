<?php
/**
 * This is the template that displays the comment form for a post
 *
 * This file is not meant to be called directly.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 * @subpackage pureforums
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $cookie_name, $cookie_email, $cookie_url;
global $comment_allowed_tags;
global $comment_cookies, $comment_allow_msgform;
global $checked_attachments; // Set this var as global to use it in the method $Item->can_attach()
global $PageCache;
global $Blog, $dummy_fields;

// Default params:
$params = array_merge([
    'disp_comment_form' => true,
    'form_title_start' => '<h3>',
    'form_title_end' => '</h3>',
    'form_title_text' => T_('Leave a comment'),
    'form_comment_text' => T_('Comment text'),
    'form_submit_text' => T_('Send comment'),
    'form_params' => [], // Use to change a structre of form, i.e. fieldstart, fieldend and etc.
    'policy_text' => '',
    'textarea_lines' => 10,
    'default_text' => '',
    'preview_block_start' => '',
    'preview_start' => '<div id="comment_preview">',
    'comment_template' => '_item_comment.inc.php',	// The template used for displaying individual comments (including preview)
    'preview_end' => '</div>',
    'preview_block_end' => '',
    'before_comment_error' => '<p><em>',
    'comment_closed_text' => '#',
    'after_comment_error' => '</em></p>',
    'before_comment_form' => '',
    'after_comment_form' => '',
    'comment_mode' => '', // Can be 'quote' from GET request
], $params);

/* Yura: Dirty temp hack, because now function param() brakes html tags by validate function param_check_general_array() -> check_html_sanity() -> balance_tags() */
$params['form_params'] = [
    'formstart' => '<table class="forums_table topics_table" cellspacing="0" cellpadding="0"><tr class="table_title"><th colspan="2"><div class="form_title">' . T_('Post a reply') . '</div></th></tr>',
    'formend' => '</table>',
    'fieldset_begin' => '<tr><td colspan="2">',
    'fieldset_end' => '</td></tr>',
    'fieldstart' => '<tr>',
    'fieldend' => '</tr>',
    'labelstart' => '<td><strong>',
    'labelend' => '</strong></td>',
    'inputstart' => '<td>',
    'inputend' => '</td>',
    'infostart' => '<td>',
    'infoend' => '</td>',
];

$comment_reply_ID = param('reply_ID', 'integer', 0);

$email_is_detected = false; // Used when comment contains an email strings

// Consider comment attachments list empty
$comment_attachments = '';

// Default renderers:
$comment_renderers = ['default'];

/*
 * Comment form:
 */
$section_title = $params['form_title_start'] . $params['form_title_text'] . $params['form_title_end'];
if ($params['disp_comment_form'] && $Item->can_comment($params['before_comment_error'], $params['after_comment_error'], '#', $params['comment_closed_text'], $section_title, $params)) { // We want to display the comments form and the item can be commented on:
    echo $params['before_comment_form'];

    // INIT/PREVIEW:
    if ($Comment = get_comment_from_session('preview')) {	// We have a comment to preview
        if ($Comment->item_ID == $Item->ID) { // display PREVIEW:
            // We do not want the current rendered page to be cached!!
            if (! empty($PageCache)) {
                $PageCache->abort_collect();
            }

            if ($Comment->email_is_detected) {	// We set it to define a some styles below
                $email_is_detected = true;
            }

            if (empty($Comment->in_reply_to_cmt_ID)) { // Display the comment preview here only if this comment is not a reply, otherwise it was already displayed
                // ------------------ PREVIEW COMMENT INCLUDED HERE ------------------
                skin_include($params['comment_template'], [
                    'Comment' => &$Comment,
                    'comment_block_start' => $Comment->email_is_detected ? '' : $params['preview_block_start'],
                    'comment_start' => $Comment->email_is_detected ? $params['comment_error_start'] : $params['preview_start'],
                    'comment_end' => $Comment->email_is_detected ? $params['comment_error_end'] : $params['preview_end'],
                    'comment_block_end' => $Comment->email_is_detected ? '' : $params['preview_block_end'],
                ]);
                // Note: You can customize the default item feedback by copying the generic
                // /skins/_item_comment.inc.php file into the current skin folder.
                // ---------------------- END OF PREVIEW COMMENT ---------------------
            }

            // Form fields:
            $comment_content = $Comment->original_content;
            // comment_attachments contains all file IDs that have been attached
            $comment_attachments = $Comment->preview_attachments;
            // checked_attachments contains all attachment file IDs which checkbox was checked in
            $checked_attachments = $Comment->checked_attachments;
            // for visitors:
            $comment_author = $Comment->author;
            $comment_author_email = $Comment->author_email;
            $comment_author_url = $Comment->author_url;
            // Get what renderer checkboxes were selected on form:
            $comment_renderers = explode('.', $Comment->get('renderers'));

            // Display error messages again after preview of comment
            global $Messages;
            $Messages->display();
        }
    } else { // New comment:
        if (($Comment = get_comment_from_session()) == null) { // there is no saved Comment in Session
            $Comment = new Comment();
            if ((! empty($PageCache)) && ($PageCache->is_collecting)) {	// This page is going into the cache, we don't want personal data cached!!!
                // fp> These fields should be filled out locally with Javascript tapping directly into the cookies. Anyone JS savvy enough to do that?
                $comment_author = '';
                $comment_author_email = '';
                $comment_author_url = '';
            } else { // Try to get params from $_COOKIE through the param() function
                $comment_author = param_cookie($cookie_name, 'string', '');
                $comment_author_email = utf8_strtolower(param_cookie($cookie_email, 'string', ''));
                $comment_author_url = param_cookie($cookie_url, 'string', '');
            }
            if (empty($comment_author_url)) {	// Even if we have a blank cookie, let's reset this to remind the bozos what it's for
                $comment_author_url = 'http://';
            }

            $comment_content = $params['default_text'];
        } else { // set saved Comment attributes from Session
            $comment_content = $Comment->content;
            $comment_author = $Comment->author;
            $comment_author_email = $Comment->author_email;
            $comment_author_url = $Comment->author_url;
            // comment_attachments contains all file IDs that have been attached
            $comment_attachments = $Comment->preview_attachments;
            // checked_attachments contains all attachment file IDs which checkbox was checked in
            $checked_attachments = $Comment->checked_attachments;
        }

        if ($params['comment_mode'] == 'quote') {	// These params go from ajax form loading, Used to reply with quote
            set_param('mode', $params['comment_mode']);
            set_param('qc', $params['comment_qc']);
            set_param('qp', $params['comment_qp']);
            set_param($dummy_fields['content'], $params[$dummy_fields['content']]);
        }

        $mode = param('mode', 'string');
        if ($mode == 'quote') { // Quote for comment/post
            $comment_content = param($dummy_fields['content'], 'html');
            $quoted_comment_ID = param('qc', 'integer', 0);
            $quoted_post_ID = param('qp', 'integer', 0);
            if (! empty($quoted_comment_ID)) {
                $CommentCache = &get_CommentCache();
                $quoted_Comment = &$CommentCache->get_by_ID($quoted_comment_ID, false);
                $quoted_Item = $quoted_Comment->get_Item();
                if ($quoted_User = $quoted_Comment->get_author_User()) { // User is registered
                    $quoted_login = $quoted_User->login;
                } else { // Anonymous user
                    $quoted_login = $quoted_Comment->get_author_name();
                }
                $quoted_content = $quoted_Comment->get('content');
                $quoted_ID = 'c' . $quoted_Comment->ID;
            } elseif (! empty($quoted_post_ID)) {
                $ItemCache = &get_ItemCache();
                $quoted_Item = &$ItemCache->get_by_ID($quoted_post_ID, false);
                $quoted_login = $quoted_Item->get_creator_login();
                $quoted_content = $quoted_Item->get('content');
                $quoted_ID = 'p' . $quoted_Item->ID;
            }

            if (! empty($quoted_Item)) {	// Format content for editing, if we were not already in editing...
                $comment_title = '';
                $comment_content .= '[quote=@' . $quoted_login . '#' . $quoted_ID . ']' . strip_tags($quoted_content) . '[/quote]';

                $Plugins_admin = &get_Plugins_admin();
                $quoted_Item->load_Blog();
                $plugins_params = [
                    'object_type' => 'Comment',
                    'object_Blog' => &$quoted_Item->Blog,
                ];
                $Plugins_admin->unfilter_contents($comment_title /* by ref */, $comment_content /* by ref */, $quoted_Item->get_renderers_validated(), $plugins_params);
            }
        }
    }

    if ((! empty($PageCache)) && ($PageCache->is_collecting)) {	// This page is going into the cache, we don't want personal data cached!!!
        // fp> These fields should be filled out locally with Javascript tapping directly into the cookies. Anyone JS savvy enough to do that?
    } else {
        // Get values that may have been passed through after a preview
        param('comment_cookies', 'integer', null);
        param('comment_allow_msgform', 'integer', null); // checkbox

        if (is_null($comment_cookies)) { // "Remember me" checked, if remembered before:
            $comment_cookies = isset($_COOKIE[$cookie_name]) || isset($_COOKIE[$cookie_email]) || isset($_COOKIE[$cookie_url]);
        }
    }

    echo $params['form_title_start'];
    echo $params['form_title_text'];
    echo $params['form_title_end'];

    /*
        echo '<script type="text/javascript">
    /* <![CDATA[ *
    function validateCommentForm(form)
    {
        if( form.'.$dummy_fields['content'].'.value.replace(/^\s+|\s+$/g,"").length == 0 )
        {
            alert("'.TS_('Please do not send empty comments.').'");
            return false;
        }
    }
    /* ]]> *
    </script>';*/

    $Form = new Form(get_htsrv_url() . 'comment_post.php', 'bComment_form_id_' . $Item->ID, 'post', null, 'multipart/form-data');

    $Form->switch_template_parts($params['form_params']);

    $Form->begin_form('', '', [
        'target' => '_self', /*, 'onsubmit' => 'return validateCommentForm(this);'*/
    ]);

    // TODO: dh> a plugin hook would be useful here to add something to the top of the Form.
    //           Actually, the best would be, if the $Form object could be changed by a plugin
    //           before display!

    $Form->add_crumb('comment');
    $Form->hidden('comment_item_ID', $Item->ID);
    if (! empty($comment_reply_ID)) {
        $Form->hidden('reply_ID', $comment_reply_ID);

        // Link to scroll back up to replying comment
        echo '<a href="' . url_add_param($Item->get_permanent_url(), 'reply_ID=' . $comment_reply_ID . '&amp;redir=no') . '#c' . $comment_reply_ID . '" class="comment_reply_current" rel="' . $comment_reply_ID . '">' . T_('You are currently replying to a specific comment') . '</a>';
    }
    $Form->hidden(
        'redirect_to',
        // Make sure we get back to the right page (on the right domain)
        // fp> TODO: check if we can use the permalink instead but we must check that application wide,
        // that is to say: check with the comments in a pop-up etc...
        // url_rel_to_same_host(regenerate_url( '', '', $Blog->get('blogurl'), '&' ), get_htsrv_url())
        // fp> what we need is a regenerate_url that will work in permalinks
        // fp> below is a simpler approach:
        $Item->get_feedback_url($disp == 'feedback-popup', '&')
    );

    if (check_user_status('is_validated')) { // User is logged in and activated:
        $Form->info_field(T_('User'), '<strong>' . $current_User->get_identity_link([
            'link_text' => 'auto',
        ]) . '</strong>'
            . ' ' . get_user_profile_link(' [', ']', T_('Edit profile')));
    } else { // User is not logged in or not activated:
        if (is_logged_in() && empty($comment_author) && empty($comment_author_email)) {
            $comment_author = $current_User->login;
            $comment_author_email = $current_User->email;
        }
        // Note: we use funky field names to defeat the most basic guestbook spam bots
        $Form->text($dummy_fields['name'], $comment_author, 40, T_('Name'), '', 100, 'bComment');

        $Form->text($dummy_fields['email'], $comment_author_email, 40, T_('Email'), '<br />' . T_('Your email address will <strong>not</strong> be revealed on this site.'), 255, 'bComment');

        $Item->load_Blog();
        if ($Item->Blog->get_setting('allow_anon_url')) {
            $Form->text($dummy_fields['url'], $comment_author_url, 40, T_('Website'), '<br />' . T_('Your URL will be displayed.'), 255, 'bComment');
        }
    }

    if ($Item->can_rate()) {	// Comment rating:
        echo $Form->begin_field(null, T_('Your vote'), true);
        $Comment->rating_input([
            'item_ID' => $Item->ID,
        ]);
        echo $Form->end_field();
    }

    if (! empty($params['policy_text'])) {	// We have a policy text to display
        $Form->info_field('', $params['policy_text']);
    }

    ob_start();
    echo '<div class="comment_toolbars">';
    // CALL PLUGINS NOW:
    $Plugins->trigger_event('DisplayCommentToolbar', [
        'Comment' => &$Comment,
        'Item' => &$Item,
    ]);
    echo '</div>';
    $plugins_toolbar = ob_get_clean();

    $Form->switch_template_parts([
        'inputstart' => '<td class="form_input">' . $plugins_toolbar,
    ]);
    // Message field:
    $note = '';
    // $note = T_('Allowed XHTML tags').': '.htmlspecialchars(str_replace( '><',', ', $comment_allowed_tags));
    $Form->textarea($dummy_fields['content'], htmlspecialchars_decode($comment_content), $params['textarea_lines'], $params['form_comment_text'], $note, 38, 'bComment autocomplete_usernames');
    $Form->switch_template_parts($params['form_params']);

    // Display renderers
    $comment_renderer_checkboxes = $Plugins->get_renderer_checkboxes($comment_renderers, [
        'Blog' => &$Blog,
        'setting_name' => 'coll_apply_comment_rendering',
    ]);
    if (! empty($comment_renderer_checkboxes)) {
        $Form->info(T_('Text Renderers'), $comment_renderer_checkboxes);
    }

    // set b2evoCanvas for plugins
    echo '<script type="text/javascript">var b2evoCanvas = document.getElementById( "' . $dummy_fields['content'] . '" );</script>';

    // Attach files:
    if (! empty($comment_attachments)) {	// display already attached files checkboxes
        $FileCache = &get_FileCache();
        $attachments = explode(',', $comment_attachments);
        $final_attachments = explode(',', $checked_attachments);
        // create attachments checklist
        $list_options = [];
        foreach ($attachments as $attachment_ID) {
            $attachment_File = $FileCache->get_by_ID($attachment_ID, false);
            if ($attachment_File) {
                // checkbox should be checked only if the corresponding file id is in the final attachments array
                $checked = in_array($attachment_ID, $final_attachments);
                $list_options[] = ['preview_attachment' . $attachment_ID, 1, '', $checked, false, $attachment_File->get('name')];
            }
        }
        if (! empty($list_options)) {	// display list
            $Form->checklist($list_options, 'comment_attachments', T_('Attached files'));
        }
        // memorize all attachments ids
        $Form->hidden('preview_attachments', $comment_attachments);
    }
    if ($Item->can_attach()) {	// Display attach file input field
        $Form->input_field([
            'label' => T_('Attach files'),
            'note' => '<br />' . get_upload_restriction(),
            'name' => 'uploadfile[]',
            'type' => 'file',
            'size' => '30',
        ]);
    }

    $comment_options = [];

    if (! is_logged_in(false)) { // User is not logged in:
        $comment_options[] = '<label><input type="checkbox" class="checkbox" name="comment_cookies" tabindex="7"'
                                                    . ($comment_cookies ? ' checked="checked"' : '') . ' value="1" /> ' . T_('Remember me') . '</label>'
                                                    . ' <span class="note">(' . T_('For my next comment on this site') . ')</span>';
        // TODO: If we got info from cookies, Add a link called "Forget me now!" (without posting a comment).

        $msgform_class_start = '';
        $msgform_class_end = '';
        if ($email_is_detected) {	// Set a class when comment contains a email
            $msgform_class_start = '<div class="comment_recommended_option">';
            $msgform_class_end = '</div>';
        }

        $comment_options[] = $msgform_class_start .
                                                    '<label><input type="checkbox" class="checkbox" name="comment_allow_msgform" tabindex="8"'
                                                    . ($comment_allow_msgform ? ' checked="checked"' : '') . ' value="1" /> ' . T_('Allow message form') . '</label>'
                                                    . ' <span class="note">(' . T_('Allow users to contact me through a message form -- Your email will <strong>not</strong> be revealed!') . ')</span>' .
                                                    $msgform_class_end;
        // TODO: If we have an email in a cookie, Add links called "Add a contact icon to all my previous comments" and "Remove contact icon from all my previous comments".
    }

    if (! empty($comment_options)) {
        echo $Form->begin_field(null, T_('Options'), true);
        echo implode('<br />', $comment_options);
        echo $Form->end_field();
    }

    $Plugins->trigger_event('DisplayCommentFormFieldset', [
        'Form' => &$Form,
        'Item' => &$Item,
    ]);

    $Form->begin_fieldset();
    echo '<div class="center">';

    $preview_text = ($Item->can_attach()) ? T_('Preview/Add file') : T_('Preview');
    $Form->button_input([
        'name' => 'submit_comment_post_' . $Item->ID . '[preview]',
        'class' => 'preview btn-info',
        'value' => $preview_text,
        'tabindex' => 9,
    ]);
    $Form->button_input([
        'name' => 'submit_comment_post_' . $Item->ID . '[save]',
        'class' => 'submit SaveButton',
        'value' => $params['form_submit_text'],
        'tabindex' => 10,
    ]);

    $Plugins->trigger_event('DisplayCommentFormButton', [
        'Form' => &$Form,
        'Item' => &$Item,
    ]);

    echo '</div>';
    $Form->end_fieldset();
    ?>

	<div class="clear"></div>

	<?php
    $Form->end_form();

    echo $params['after_comment_form'];

    echo_comment_reply_js($Item);
}

echo_comment_moderate_js();
?>