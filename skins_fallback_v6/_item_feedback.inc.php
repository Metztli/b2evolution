<?php
/**
 * This is the template that displays the feedback for a post (comments, trackback, pingback, webmention...)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display a feedback, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?p=1&more=1
 * Note: don't code this URL by hand, use the template functions to generate it!
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

?>
<!-- ===================== START OF FEEDBACK ===================== -->
<?php

// Default params:
$params = array_merge([
    'Item' => null,
    'disp_comments' => is_single_page(),
    'disp_comment_form' => is_single_page(),
    'disp_trackbacks' => is_single_page(),
    'disp_trackback_url' => is_single_page(),
    'disp_pingbacks' => is_single_page(),
    'disp_webmentions' => is_single_page(),
    'disp_meta_comments' => false,
    'disp_section_title' => true,
    'disp_meta_comment_info' => true,
    'disp_rating_summary' => true,
    'before_section_title' => '<div class="clearfix"></div><h3 class="evo_comment__list_title">',
    'after_section_title' => '</h3>',
    'comments_title_text' => '',
    'comment_list_start' => "\n\n",
    'comment_list_end' => "\n\n",
    'comment_start' => '<article class="evo_comment panel panel-default">',
    'comment_end' => '</article>',
    'comment_post_display' => false,	// Do we want ot display the title of the post we're referring to?
    'comment_post_before' => '<h3 class="evo_comment_post_title">',
    'comment_post_after' => '</h3>',
    'comment_title_before' => '<div class="panel-heading"><h4 class="evo_comment_title panel-title">',
    'comment_title_after' => '</h4></div><div class="panel-body">',
    'comment_avatar_before' => '<span class="evo_comment_avatar">',
    'comment_avatar_after' => '</span>',
    'comment_rating_before' => '<div class="evo_comment_rating">',
    'comment_rating_after' => '</div>',
    'comment_text_before' => '<div class="evo_comment_text">',
    'comment_text_after' => '</div>',
    'comment_info_before' => '<footer class="evo_comment_footer clear text-muted"><small>',
    'comment_info_after' => '</small></footer></div>',
    'preview_start' => '<article class="evo_comment evo_comment__preview panel panel-warning" id="comment_preview">',
    'preview_end' => '</article>',
    'comment_error_start' => '<article class="evo_comment evo_comment__error panel panel-default" id="comment_error">',
    'comment_error_end' => '</article>',
    'comment_template' => '_item_comment.inc.php',	// The template used for displaying individual comments (including preview)
    'comment_image_size' => 'fit-1280x720',
    'author_link_text' => 'auto', // avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
    'link_to' => 'userurl>userpage',		    // 'userpage' or 'userurl' or 'userurl>userpage' or 'userpage>userurl'
    // Comment notification functions:
    'disp_notification' => true,
    'notification_before' => '<nav class="evo_post_comment_notification">',
    'notification_text' => T_('This is your post. You are receiving notifications when anyone comments on your posts.'),
    'notification_text2' => T_('You will be notified by email when someone comments here.'),
    'notification_text3' => T_('Notify me by email when someone comments here.'),
    'notification_after' => '</nav>',
    'feed_title' => '#',
    'disp_nav_top' => true,
    'disp_nav_bottom' => true,
    'nav_top_inside' => false, // TRUE to display it after start of comments list (inside), FALSE to display a page navigation before comments list
    'nav_bottom_inside' => false, // TRUE to display it before end of comments list (inside), FALSE to display a page navigation after comments list
    'nav_block_start' => '<div class="text-center"><ul class="pagination">',
    'nav_block_end' => '</ul></div>',
    'nav_prev_text' => '<i class="fa fa-angle-double-left"></i>',
    'nav_next_text' => '<i class="fa fa-angle-double-right"></i>',
    'nav_prev_class' => '',
    'nav_next_class' => '',
    'nav_page_item_before' => '<li>',
    'nav_page_item_after' => '</li>',
    'nav_page_current_template' => '<span><b>$page_num$</b></span>',
    'comments_per_page' => null, // Used instead of blog setting "comments_per_page"
    'pagination' => [],
    'comment_type' => 'comment',
], $params);


global $redir;

if (! empty($params['Item']) && is_object($params['Item'])) {	// Set Item object from params
    $Item = $params['Item'];
    // Unset params Item object because the params array should be json encodable and we must avoid recursions. We already have the Item for further use.
    unset($params['Item']);
}

// ----------------- MODULES "Before Comments" EVENT -----------------
modules_call_method('before_comments', $params);
// -------------------- END OF MODULES EVENT ---------------------

// Check if user is allowed to see comments, display corresponding message if not allowed
if (($params['disp_meta_comments'] && $Item->can_see_meta_comments())
    || $Item->can_see_comments(true)) { // user is allowed to see comments
    if (! $Item->can_receive_pings()) {	// Trackbacks are not allowed
        $params['disp_trackbacks'] = false;				// DO NOT Display the trackbacks if not allowed
        $params['disp_trackback_url'] = false;		// DO NOT Display the trackback URL if not allowed
    }

    if (! ($params['disp_comments'] || $params['disp_comment_form'] || $params['disp_trackbacks'] || $params['disp_trackback_url'] || $params['disp_pingbacks'] || $params['disp_meta_comments'] || $params['disp_webmentions'])) {	// Nothing more to do....
        return false;
    }

    echo '<section id="feedbacks">';

    $type_list = [];
    $disp_title = [];
    $rating_summary = '';

    if ($params['disp_comments']) {	// We requested to display comments
        if ($Item->can_see_comments()) {	// User can see a comments
            $type_list[] = 'comment';
            if (! empty($params['comments_title_text'])) {
                $disp_title[] = $params['comments_title_text'];
            } elseif ($title = $Item->get_feedback_title('comments')) {
                $disp_title[] = $title;
            }

            if ($params['disp_rating_summary']) {	// We requested to display rating summary
                $rating_summary = $Item->get_rating_summary($params);
            }
        } else {	// User cannot see comments
            $params['disp_comments'] = false;
        }
        echo '<a id="comments"></a>';
    }

    if ($params['disp_trackbacks']) {
        $type_list[] = 'trackback';
        if ($title = $Item->get_feedback_title('trackbacks')) {
            $disp_title[] = $title;
        }
        echo '<a id="trackbacks"></a>';
    }

    if ($params['disp_pingbacks']) {
        $type_list[] = 'pingback';
        if ($title = $Item->get_feedback_title('pingbacks')) {
            $disp_title[] = $title;
        }
        echo '<a id="pingbacks"></a>';
    }

    if ($params['disp_webmentions']) {
        $type_list[] = 'webmention';
        if ($title = $Item->get_feedback_title('webmentions')) {
            $disp_title[] = $title;
        }
        echo '<a id="webmentions"></a>';
    }

    if ($params['disp_trackback_url']) { // We want to display the trackback URL:
        echo $params['before_section_title'];
        echo T_('Trackback address for this post');
        echo $params['after_section_title'];

        /*
         * Trigger plugin event, which could display a captcha form, before generating a whitelisted URL:
         */
        if (! $Plugins->trigger_event_first_true('DisplayTrackbackAddr', [
            'Item' => &$Item,
            'template' => '<code>%url%</code>',
        ])) { // No plugin displayed a payload, so we just display the default:
            echo '<p class="trackback_url"><a href="' . $Item->get_trackback_url() . '">' . T_('Trackback URL (right click and copy shortcut/link location)') . '</a></p>';
        }
    }

    if ($params['disp_meta_comments']) {	// We requested to display internal comments
        if ($Item->can_see_meta_comments()) {	// User can see internal comments
            $type_list[] = 'meta';
            if (! empty($params['comments_title_text'])) {
                $disp_title[] = $params['comments_title_text'];
            } elseif ($title = $Item->get_feedback_title('comments')) {
                $disp_title[] = $title;
            }
        } else {	// User cannot see internal comments
            $params['disp_meta_comments'] = false;
        }
        echo '<a id="comments"></a>';
    }


    if ($params['disp_comments'] || $params['disp_trackbacks'] || $params['disp_pingbacks'] || $params['disp_meta_comments'] || $params['disp_webmentions']) {
        if (empty($disp_title)) {	// No title yet
            if ($title = $Item->get_feedback_title('feedbacks', '', T_('Feedback awaiting moderation'), T_('Feedback awaiting moderation'), '#moderation#', false)) { // We have some feedback awaiting moderation: we'll want to show that in the title
                $disp_title[] = $title;
            }
        }

        if (empty($disp_title)) {	// Still no title
            $disp_title[] = T_('No feedback yet');
        }

        if ($params['disp_section_title']) {	// Display title
            echo $params['before_section_title'];
            echo implode(', ', $disp_title);
            echo $params['after_section_title'];
        }

        // // Display the internal comments info ?
        if ($params['disp_meta_comment_info'] // If we want it
            && ! $params['disp_meta_comments']  // If we're not displaying the full list of internal comments anyways
            && $Item->can_see_meta_comments()) { // If we have permission to view internal comment of the collection
            // Display the internal comments info:
            global $admin_url;
            echo '<div class="evo_comment__meta_info">';
            $meta_comments_count = generic_ctp_number($Item->ID, 'metas', 'total');
            $meta_comments_url = $admin_url . '?ctrl=items&amp;p=' . $Item->ID . '&amp;comment_type=meta&amp;blog=' . $Blog->ID . '#comments';
            if ($meta_comments_count > 0) {	// Display a badge with internal comments count if at least one exists for this Item:
                echo '<a href="' . $meta_comments_url . '" class="badge badge-meta">' . sprintf(T_('%d internal comments'), $meta_comments_count) . '</a>';
            } elseif ($Item->can_meta_comment()) {	// No internal comments yet, Display a button to add new internal comment:
                echo '<a href="' . $meta_comments_url . '" class="btn btn-default btn-sm">' . T_('Add internal comment') . '</a>';
            }
            echo '</div>';
        }

        echo '<div class="clearfix"></div>';

        // Display rating summary:
        echo $rating_summary;

        if ($params['comments_per_page'] === null) { // Use blog setting:
            $comments_per_page = ! $Blog->get_setting('threaded_comments') ? $Blog->get_setting('comments_per_page') : 1000;
        } else { // Use from params:
            $comments_per_page = $params['comments_per_page'];
        }

        global $CommentList;

        $CommentList = new CommentList2($Blog, $comments_per_page, 'CommentCache', $params['disp_meta_comments'] ? 'mc_' : 'c_');

        // Filter list:
        $CommentList->set_default_filters([
            'types' => $type_list,
            'statuses' => get_inskin_statuses($Blog->ID, 'comment'),
            'post_ID' => $Item->ID,
            'order' => $params['disp_meta_comments'] ? 'DESC' : $Blog->get_setting('comments_orderdir'),
            'threaded_comments' => $params['disp_meta_comments'] ? false : $Blog->get_setting('threaded_comments'),
        ]);

        $CommentList->load_from_Request();

        // Get ready for display (runs the query):
        $CommentList->display_init([
            'init_order_numbers_mode' => ($params['disp_meta_comments'] ? 'date' : 'list'),
        ]);

        // Set redir=no in order to open comment pages
        memorize_param('redir', 'string', '', 'no');

        if ($params['nav_top_inside']) { // To use comments page navigation inside list (Useful for table markup)
            echo $params['comment_list_start'];
        }

        if ($params['disp_nav_top'] && ($Blog->get_setting('paged_comments') || $params['comments_per_page'] !== null)) { // Prev/Next page navigation
            $CommentList->page_links(array_merge([
                'page_url' => url_add_tail($Item->get_permanent_url(), '#comments'),
                'block_start' => $params['nav_block_start'],
                'block_end' => $params['nav_block_end'],
                'prev_text' => $params['nav_prev_text'],
                'next_text' => $params['nav_next_text'],
                'prev_class' => $params['nav_prev_class'],
                'next_class' => $params['nav_next_class'],
                'page_item_before' => $params['nav_page_item_before'],
                'page_item_after' => $params['nav_page_item_after'],
                'page_current_template' => $params['nav_page_current_template'],
            ], $params['pagination']));
        }


        if ($Blog->get_setting('threaded_comments')) {	// Array to store the comment replies
            global $CommentReplies;
            $CommentReplies = [];

            if ($Comment = get_comment_from_session('preview', $params['comment_type'])) {	// Init PREVIEW comment
                if ($Comment->item_ID == $Item->ID) {
                    $CommentReplies[$Comment->in_reply_to_cmt_ID] = [$Comment];
                }
            }
        }

        if (! $params['nav_top_inside']) { // To use comments page navigation before list
            echo $params['comment_list_start'];
        }

        /**
         * @var Comment
         */
        while ($Comment = &$CommentList->get_next()) {	// Loop through comments:
            if ($Blog->get_setting('threaded_comments') && $Comment->in_reply_to_cmt_ID > 0) {	// Store the replies in a special array
                if (! isset($CommentReplies[$Comment->in_reply_to_cmt_ID])) {
                    $CommentReplies[$Comment->in_reply_to_cmt_ID] = [];
                }
                $CommentReplies[$Comment->in_reply_to_cmt_ID][] = $Comment;
                continue; // Skip dispay a comment reply here in order to dispay it after parent comment by function display_comment_replies()
            }

            // ------------------ COMMENT INCLUDED HERE ------------------
            skin_include($params['comment_template'], [
                'Comment' => &$Comment,
                'comment_start' => $params['comment_start'],
                'comment_end' => $params['comment_end'],
                'comment_title_before' => $params['comment_title_before'],
                'comment_title_after' => $params['comment_title_after'],
                'comment_avatar_before' => $params['comment_avatar_before'],
                'comment_avatar_after' => $params['comment_avatar_after'],
                'comment_rating_before' => $params['comment_rating_before'],
                'comment_rating_after' => $params['comment_rating_after'],
                'comment_text_before' => $params['comment_text_before'],
                'comment_text_after' => $params['comment_text_after'],
                'comment_info_before' => $params['comment_info_before'],
                'comment_info_after' => $params['comment_info_after'],
                'author_link_text' => $params['author_link_text'],
                'link_to' => $params['link_to'],		// 'userpage' or 'userurl' or 'userurl>userpage' or 'userpage>userurl'
                'author_link_text' => $params['author_link_text'],
                'image_size' => $params['comment_image_size'],
            ]);
            // Note: You can customize the default item comment by copying the generic
            // /skins/_item_comment.inc.php file into the current skin folder.
            // ---------------------- END OF COMMENT ---------------------

            if ($Blog->get_setting('threaded_comments')) {	// Display the comment replies
                display_comment_replies($Comment->ID, $params);
            }
        }	// End of comment list loop.

        if (! $params['nav_bottom_inside']) { // To use comments page navigation after list
            echo $params['comment_list_end'];
        }

        if ($params['disp_nav_bottom'] && ($Blog->get_setting('paged_comments') || $params['comments_per_page'] !== null)) { // Prev/Next page navigation
            $CommentList->page_links(array_merge([
                'page_url' => url_add_tail($Item->get_permanent_url(), '#comments'),
                'block_start' => $params['nav_block_start'],
                'block_end' => $params['nav_block_end'],
                'prev_text' => $params['nav_prev_text'],
                'next_text' => $params['nav_next_text'],
                'prev_class' => $params['nav_prev_class'],
                'next_class' => $params['nav_next_class'],
                'page_item_before' => $params['nav_page_item_before'],
                'page_item_after' => $params['nav_page_item_after'],
                'page_current_template' => $params['nav_page_current_template'],
            ], $params['pagination']));
        }

        if ($params['nav_bottom_inside']) { // To use comments page navigation inside list (Useful for table markup)
            echo $params['comment_list_end'];
        }

        // Restore "redir" param
        forget_param('redir');

        if (! $params['disp_meta_comments']) { // Only normal(not meta) comments can be moderated
            // _______________________________________________________________
            // Display count of comments to be moderated:
            $Item->feedback_moderation(
                'feedbacks',
                '<p class="alert alert-info">',
                '</p>',
                '',
                T_('This post has 1 feedback awaiting moderation... %s'),
                T_('This post has %d feedbacks awaiting moderation... %s')
            );
            // _______________________________________________________________
        }
    }

    echo '</section>';
}

// ------------------ COMMENT FORM INCLUDED HERE ------------------
if ($params['disp_comment_form'] && // if enabled by skin param
    $Blog->get_setting('allow_comments') != 'never' && // if enabled by collection setting
    $Item->get_type_setting('use_comments')) { // if enabled by item type setting
    // Display a comment form only if it is enabled:
    if ($Blog->get_ajax_form_enabled()) {
        // The following params will be piped through the AJAX request...
        $json_params = [
            'action' => 'get_comment_form',
            'p' => $Item->ID,
            'blog' => $Blog->ID,
            'reply_ID' => param('reply_ID', 'integer', 0),
            'quote_post' => param('quote_post', 'integer', 0),
            'quote_comment' => param('quote_comment', 'integer', 0),
            'disp' => $disp,
            'params' => $params,
        ];
        display_ajax_form($json_params);
    } else {
        skin_include('_item_comment_form.inc.php', $params);
    }
    // Note: You can customize the default item comment form by copying the generic
    // /skins/_item_comment_form.inc.php file into the current skin folder.
}
// ---------------------- END OF COMMENT FORM ---------------------

echo_comment_moderate_js();
?>
