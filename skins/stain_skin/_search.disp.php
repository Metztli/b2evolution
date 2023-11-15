<?php
/**
 * This is the template that displays the search form for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
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

$pagi_align = $Skin->get_setting('search_align_pagination');
$aling_class = '';
switch ($pagi_align) {
    case $pagi_align:
        $align_class = $pagi_align;
        break;
}

$params = array_merge([
    // Result Content
    'row_start' => '<li class="search_result">',
    'row_end' => '</li>',
    'cell_content_start' => '<div class="result_content">',
    'cell_content_end' => '</div>',
    'search_use_editor' => false,
    'search_author_format' => 'avatar_name',
    'search_cell_author_start' => '<div class="search_info author dimmed">',
    'search_cell_author_end' => '</div>',
    'search_date_format' => 'F j, Y',

    'pagination' => [
        'block_start' => '<div class="search_pagination ' . $align_class . '"><ul class="pagination clearfix">',
        'block_end' => '</ul></div>',
        'page_current_template' => '<span>$page_num$</span>',
        'page_item_before' => '<li>',
        'page_item_after' => '</li>',
        'page_item_current_before' => '<li class="active">',
        'page_item_current_after' => '</li>',
        'prev_text' => '<i class="fa fa-angle-left"></i>',
        'next_text' => '<i class="fa fa-angle-right"></i>',
    ],
    'no_match_message' => '<p class="alert alert-info msg_nothing">' . T_('Sorry, we could not find anything matching your request, please try to broaden your search.') . '<p>',
], $params);


// Perform search (after having displayed the first part of the page) & display results:
search_result_block([
    'block_start' => '<ul id="main_result_content">',
    'block_end' => '</ul>',

    'row_start' => $params['row_start'],
    'row_end' => $params['row_end'],

    'cell_content_start' => $params['cell_content_start'],
    'cell_content_end' => $params['cell_content_end'],

    'use_editor' => $params['search_use_editor'],

    'cell_chapter_start' => '<div id="chapter" class="search_info chapter dimmed">',
    'cell_chapter_end' => '</div>',

    'cell_author_start' => $params['search_cell_author_start'],
    'author_format' => $params['search_author_format'],
    'cell_author_end' => $params['search_cell_author_end'],
    'date_format' => $params['search_date_format'],

    'title_suffix_post' => '<span class="post">' . T_('Posts') . '</span>',
    'title_suffix_comment' => '<span class="comment">' . T_('Comment') . '</span>',
    'title_suffix_category' => '<span class="category">' . T_('Category') . '</span>',
    'title_suffix_tag' => '<span class="tag">' . T_('Tag') . '</span>',

    'pagination' => $params['pagination'],
    'no_match_message' => $params['no_match_message'],
]);
