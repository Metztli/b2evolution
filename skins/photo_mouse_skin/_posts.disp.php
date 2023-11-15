<?php
/**
 * This is the template that displays the posts for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display the archive directory, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=posts
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

// -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
mainlist_page_links([
    'block_start' => '<div class="page_navigation center">',
    'next_text' => '<i class="fa fa-caret-right" title="' . T_('Next') . '"></i>',
    'prev_text' => '<i class="fa fa-caret-left" title="' . T_('Previous') . '"></i>',
    'block_end' => '</div>',
    'page_item_current_before' => '<span class="active">',
    'page_item_current_after' => '</span>',
]);
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------

// --------------------------------- START OF POSTS -------------------------------------
// Display message if no post:
display_if_empty();

while (mainlist_get_item()) { // For each blog post, do everything below up to the closing curly brace "}"
    // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
    skin_include('_item_block.inc.php', array_merge([
        'content_mode' => 'auto', // 'auto' will auto select depending on $disp-detail
    ], $params));
    // ----------------------------END ITEM BLOCK  ----------------------------
} // ---------------------------------- END OF POSTS ------------------------------------

// -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
mainlist_page_links([
    'block_start' => '<div class="page_navigation center">',
    'next_text' => '<i class="fa fa-caret-right" title="' . T_('Next') . '"></i>',
    'prev_text' => '<i class="fa fa-caret-left" title="' . T_('Previous') . '"></i>',
    'block_end' => '</div>',
    'page_item_current_before' => '<span class="active">',
    'page_item_current_after' => '</span>',
]);
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
