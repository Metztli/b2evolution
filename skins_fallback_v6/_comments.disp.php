<?php
/**
 * This is the template that displays the links to the latest comments for a blog (disp=comments)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display a feedback, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=comments
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

// Default params:
$params = array_merge([
    'comment_template' => '_item_comment.inc.php',	// The template used for displaying individual comments (including preview)
    'author_link_text' => 'auto', // avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
    'link_to' => 'userurl>userpage', // 'userpage' or 'userurl' or 'userurl>userpage' or 'userpage>userurl'
    'display_comment_avatar' => true,
    'comment_avatar_position' => 'before_title', // 'before_title', 'before_text'
    'comment_start' => '<article class="evo_comment evo_content_block panel panel-default">',
    'comment_end' => '</article>',
    'comment_post_display' => true,	// We want to display the title of the post we're referring to
    'comment_post_before' => '<div class="panel-heading"><h4 class="evo_comment_title panel-title pull-left">',
    'comment_post_after' => '</h4>',
    'comment_title_before' => '<h4 class="evo_comment_subtitle panel-title pull-right">',
    'comment_title_after' => '</h4><div class="clearfix"></div></div><div class="panel-body">',
    'comment_avatar_before' => '<div class="evo_comment_avatar">',
    'comment_avatar_after' => '</div>',
    'comment_rating_before' => '<div class="evo_comment_rating">',
    'comment_rating_after' => '</div>',
    'comment_text_before' => '<div class="evo_comment_text">',
    'comment_text_after' => '</div>',
    'comment_info_before' => '<footer class="evo_comment_info clear text-muted"><small>',
    'comment_info_after' => '</small></footer></div>',
    'comment_image_size' => 'fit-1280x720',
    'comment_image_class' => 'img-responsive',
], $params);


global $CommentList;

$CommentList = new CommentList2($Blog);

// Filter list:
$CommentList->set_filters([
    'types' => ['comment', 'trackback', 'pingback', 'webmention'],
    'statuses' => get_inskin_statuses($Blog->ID, 'comment'),
    'order' => 'DESC',
    'comments' => $Blog->get_setting('latest_comments_num'),
]);

// Get ready for display (runs the query):
$CommentList->display_init();

// ------------------------- "Comment List" CONTAINER EMBEDDED HERE --------------------------
// Display container contents:
widget_container('comment_list', [
    // The following (optional) params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => false, // If no widget, don't display container at all
    // This will enclose each widget in a block:
    'block_start' => '<div class="evo_widget $wi_class$">',
    'block_end' => '</div>',
    // This will enclose the title of each widget:
    'block_title_start' => '<h3>',
    'block_title_end' => '</h3>',
]);
// ----------------------------- END OF "Item List" CONTAINER -----------------------------

$CommentList->display_if_empty();

echo '<div class="evo_content_block">';
while ($Comment = &$CommentList->get_next()) { // Loop through comments:
    ?>
	<!-- ========== START of a COMMENT ========== -->
	<?php
            // ------------------ COMMENT INCLUDED HERE ------------------
            skin_include($params['comment_template'], [
                'Comment' => &$Comment,
                'comment_start' => $params['comment_start'],
                'comment_end' => $params['comment_end'],
                'comment_post_display' => $params['comment_post_display'],
                'comment_post_before' => $params['comment_post_before'],
                'comment_post_after' => $params['comment_post_after'],
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
                'image_class' => $params['comment_image_class'],
            ]);
    // Note: You can customize the default item comment by copying the generic
    // /skins/_item_comment.inc.php file into the current skin folder.
    // ---------------------- END OF COMMENT ---------------------

    ?>
	<!-- ========== END of a COMMENT ========== -->
	<?php
}	// End of comment loop.
echo '</div>';

echo_comment_moderate_js();
?>
