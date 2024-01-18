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

$params = array_merge([
    'pagination' => [],
    'search_class' => 'compact_search_form',
    'search_input_before' => '',
    'search_input_after' => '',
    'search_submit_before' => '',
    'search_submit_after' => '',
    'search_use_editor' => true,
    'search_author_format' => 'name',
    'search_cell_author_start' => '<div class="search_info dimmed">',
    'search_cell_author_end' => '</div>',
    'search_date_format' => 'F j, Y',
], $params);

// Perform search (after having displayed the first part of the page) & display results:
search_result_block([
    'pagination' => $params['pagination'],
    'use_editor' => $params['search_use_editor'],
    'author_format' => $params['search_author_format'],
    'cell_author_start' => $params['search_cell_author_start'],
    'cell_author_end' => $params['search_cell_author_end'],
    'date_format' => $params['search_date_format'],
]);
