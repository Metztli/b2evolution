<?php
/**
 * This is sent to ((Moderators)) to notify them that a comment has been voted as SPAM.
 *
 * For more info about email skins, see: http://b2evolution.net/man/themes-templates-skins/email-skins/
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

// ---------------------------- EMAIL HEADER INCLUDED HERE ----------------------------
emailskin_include('_email_header.inc.html.php', $params);
// ------------------------------- END OF EMAIL HEADER --------------------------------

global $admin_url, $Collection, $Blog, $Session;

// Default params:
$params = array_merge([
    'notify_full' => false,
    'Comment' => null,
    'Blog' => null,
    'Item' => null,
    'voter_ID' => null,
], $params);


$Comment = $params['Comment'];
$Collection = $Blog = $params['Blog'];
$Item = $params['Item'];
$recipient_User = &$params['recipient_User'];

$UserCache = &get_UserCache();
$voter_User = &$UserCache->get_by_ID($params['voter_ID']);
$voter_name = get_user_colored_login_link($voter_User->get('login'), [
    'use_style' => true,
    'protocol' => 'http:',
    'login_text' => 'name',
]);

$notify_message = '<p' . emailskin_style('.p') . '>' . sprintf(T_('%s reported comment as spam on %s in %s.'), '<b>' . $voter_name . '</b>', '<b>' . get_link_tag($Item->get_permanent_url('', '', '&'), $Item->get('title'), '.a') . '</b>', '<b>' . $Blog->get('shortname') . '</b>') . "</p>\n";

if ($params['notify_full']) {	// Long format notification:
    if (! empty($recipient_User) && $recipient_User->check_perm('stats', 'view')) {
        $session_ID = '<a href="' . $admin_url . '?ctrl=stats&amp;tab=hits&amp;blog=0&amp;sess_ID=' . $Session->ID . '">' . $Session->ID . '</a>';
    } else {
        $session_ID = $Session->ID;
    }

    if (! $Comment->get_author_User()) {	// Comment from visitor:
        $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Session ID') . ': ' . $session_ID . "</p>\n";
        $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Email') . ': ' . $Comment->author_email . "</p>\n";
        $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Url') . ': ' . get_link_tag($Comment->author_url, '', '.a') . "</p>\n";
    }

    if (! empty($Comment->rating)) {
        $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Rating') . ': ' . $Comment->rating . '/5' . "</p>\n";
    }

    $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Status') . ': ' . $Comment->get('t_status') . "</p>\n";

    // Content:
    $notify_message .= '<div class="email_ugc"' . emailskin_style('div.email_ugc') . '>' . "\n";
    $notify_message .= '<p' . emailskin_style('.p') . '>' . nl2br($Comment->get('content')) . '</p>';
    $notify_message .= "</div>\n";

    // Attachments:
    $LinkCache = &get_LinkCache();
    $comment_links = $LinkCache->get_by_comment_ID($Comment->ID);
    if (! empty($comment_links)) {
        $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Attachments') . ':<ul>' . "\n";
        foreach ($comment_links as $Link) {
            if ($File = $Link->get_File()) {
                $notify_message .= '<li><a href="' . $File->get_url() . '"' . emailskin_style('.a') . '>';
                if ($File->is_image()) { // Display an image
                    $notify_message .= $File->get_thumb_imgtag('fit-80x80', '', 'middle') . ' ';
                }
                $notify_message .= $File->get_name() . '</a></li>' . "\n";
            }
        }
        $notify_message .= "</ul></p>\n";
    }
} else {	// Short format notification:
    $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Author') . ': <b>' . ($Comment->get_author_User() ? get_user_colored_login_link($Comment->author_User->get('login'), [
        'use_style' => true,
        'protocol' => 'http:',
        'login_text' => 'name',
    ]) : $Comment->author) . "</b></p>\n";
    $notify_message .= '<p' . emailskin_style('.p') . '>' . T_('Status') . ': <b>' . $Comment->get('t_status') . "</b></p>\n";

    $notify_message .= '<div class="email_ugc"' . emailskin_style('div.email_ugc') . '>' . "\n";
    $notify_message .= '<p' . emailskin_style('.p') . '><i' . emailskin_style('.note') . '>' . T_('This is a short form notification. To make these emails more useful, ask the administrator to send you long form notifications instead.') . '</i></p>';
    $notify_message .= "</div>\n";
}

echo $notify_message;

// Buttons:

echo '<div' . emailskin_style('div.buttons') . '>' . "\n";

echo get_link_tag($Comment->get_permanent_url('&', '#comments'), T_('Read full comment'), 'div.buttons a+a.btn-primary') . "\n";

echo get_link_tag($admin_url . '?ctrl=comments&action=edit&comment_ID=' . $Comment->ID, T_('Edit comment'), 'div.buttons a+a.btn-default') . "\n";

echo "</div>\n";


// add unsubscribe and edit links
$params['unsubscribe_text'] = T_('You are a moderator of this blog and you are receiving notifications when a comment may need moderation.') . '<br />'
    . T_('If you don\'t want to receive any more notifications about moderating spam comments, click here') . ': '
    . get_link_tag(get_htsrv_url() . 'quick_unsubscribe.php?type=comment_moderator_spam&user_ID=$user_ID$&key=$unsubscribe_key$', T_('instant unsubscribe'), '.a');

// ---------------------------- EMAIL FOOTER INCLUDED HERE ----------------------------
emailskin_include('_email_footer.inc.html.php', $params);
// ------------------------------- END OF EMAIL FOOTER --------------------------------
