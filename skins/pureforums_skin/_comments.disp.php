<?php
/**
 * This is the template that displays the links to the latest comments for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display a feedback, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=comments
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

$CommentList = new CommentList2($Blog);

// Filter list:
$CommentList->set_filters([
    'types' => ['comment', 'trackback', 'pingback'],
    'statuses' => get_inskin_statuses($Blog->ID, 'comment'),
    'order' => 'DESC',
    'comments' => $Blog->get_setting('latest_comments_num'),
    // fp> I don't think it's necessary to add a restriction here. (use case?)
    // 'timestamp_min' => $Blog->get_timestamp_min(),
    // 'timestamp_max' => $Blog->get_timestamp_max(),
]);

// Get ready for display (runs the query):
$CommentList->display_init();

$CommentList->display_if_empty([
    'msg_empty' => T_('No replies yet...'),
]);

if ($CommentList->result_num_rows > 0) {
    ?>
<table class="forums_table topics_table single_topic evo_content_block" cellspacing="0" cellpadding="0">
<?php
while ($Comment = &$CommentList->get_next()) { // Loop through comments:
    // Load comment's Item object:
    $Item = $Comment->get_Item();

    // ------------------ COMMENT INCLUDED HERE ------------------
    skin_include('_item_comment.inc.php', [
        'Comment' => &$Comment,
        'comment_start' => '<div class="bText">',
        'comment_end' => '</div>',
        'display_vote_helpful' => false,
    ]);
    // Note: You can customize the default item comment by copying the generic
    // /skins/_item_comment.inc.php file into the current skin folder.
    // ---------------------- END OF COMMENT ---------------------
}	// End of comment loop.
    ?>
</table>
<?php
}

echo_comment_moderate_js();
?>