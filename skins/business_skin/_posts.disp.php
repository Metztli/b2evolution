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
 * @copyright (c)2003-2015 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if(! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Skin;

/* Pagination Align
   ========================================================================== */
$align = '';
$pagination_align = $Skin->get_setting('pagination_align');

if ($pagination_align == 'left') {
    $align = 'left';
} elseif ($pagination_align == 'right') {
    $align = 'right';
} else {
    $align = 'center';
}

// -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
// if ( $Skin->get_setting( 'pagination_top_show' ) == 1 ) :
//     mainlist_page_links( array(
//     	'block_start'              => '<div class="'. $align .' top_pagin"><ul class="pagination">',
//     	'block_end'                => '</ul></div>',
//     	'page_item_before'         => '<li>',
//     	'page_item_after'          => '</li>',
//     	'page_item_current_before' => '<li class="active">',
//     	'page_item_current_after'  => '</li>',
//     	'page_current_template'    => '<span>$page_num$</span>',
//     	'prev_text'                => '<i class="fa fa-angle-left"></i>',
//     	'next_text'                => '<i class="fa fa-angle-right"></i>',
//     ) );
// endif;
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------

if($Skin->get_setting('layout_posts') == 'masonry') {
    echo '<div class="post_masonry">';
}
// --------------------------------- START OF POSTS -------------------------------------
// Display message if no post:
display_if_empty();
while(mainlist_get_item()) { // For each blog post, do everything below up to the closing curly brace "}"
    // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
    skin_include('_item_block.inc.php', [
        'content_mode' => 'auto', // 'auto' will auto select depending on $disp-detail
    ]);
    // ----------------------------END ITEM BLOCK  ----------------------------
} // ---------------------------------- END OF POSTS ------------------------------------

if($Skin->get_setting('layout_posts') == 'masonry') {
    echo '</div>';
}

// -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
if ($Skin->get_setting('pagination_bottom_show') == 1) :
    mainlist_page_links([
        'block_start' => '<div class="' . $align . ' bottom_pagin"><ul class="pagination">',
        'block_end' => '</ul></div>',
        'page_current_template' => '<span>$page_num$</span>',
        'page_item_before' => '<li>',
        'page_item_after' => '</li>',
        'page_item_current_before' => '<li class="active">',
        'page_item_current_after' => '</li>',
        'prev_text' => '<i class="fa fa-angle-left"></i>',
        'next_text' => '<i class="fa fa-angle-right"></i>',
    ]);
endif;
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
