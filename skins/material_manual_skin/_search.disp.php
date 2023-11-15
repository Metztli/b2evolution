<?php
/**
 * This is the template that displays the search form for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2015 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

$params = array_merge([
    'pagination' => [
        'block_start' => '<div class="center"><ul class="pagination">',
        'block_end' => '</ul></div>',
        'page_current_template' => '<span class="current">$page_num$</span>',
        'page_item_before' => '<li>',
        'page_item_after' => '</li>',
        'page_item_current_before' => '<li class="active">',
        'page_item_current_after' => '</li>',
        'prev_text' => '<i class="fa fa-angle-left"></i>',
        'next_text' => '<i class="fa fa-angle-right"></i>',
    ],
    'search_class' => 'extended_search_form',
    'search_input_before' => '<div class="input-group">',
    'search_input_after' => '',
    'search_submit_before' => '<span class="input-group-btn">',
    'search_submit_after' => '</span></div>',
    'search_use_editor' => false,
    'search_author_format' => 'avatar_name',
    'search_cell_author_start' => '<div class="search_info dimmed">',
    'search_cell_author_end' => '</div>',
    'search_date_format' => locale_datefmt(),
], $params);

// ------------------------ START OF SEARCH FORM WIDGET ------------------------
skin_widget([
    // CODE for the widget:
    'widget' => 'coll_search_form',
    // Optional display params
    'block_start' => '<div class="evo_widget $wi_class$">',
    'block_end' => '</div>',
    'block_display_title' => false,
    'disp_search_options' => 0,
    'search_class' => $params['search_class'],
    'search_input_before' => $params['search_input_before'],
    'search_input_after' => $params['search_input_after'],
    'search_submit_before' => $params['search_submit_before'],
    'search_submit_after' => $params['search_submit_after'],
    'use_search_disp' => 1,
]);
// ------------------------- END OF SEARCH FORM WIDGET -------------------------

// Perform search (after having displayed the first part of the page) & display results:
search_result_block([
    'pagination' => $params['pagination'],
    'use_editor' => $params['search_use_editor'],
    'author_format' => $params['search_author_format'],
    'cell_author_start' => $params['search_cell_author_start'],
    'cell_author_end' => $params['search_cell_author_end'],
    'date_format' => $params['search_date_format'],
]);
