<?php
/**
 * This is the template that displays the media index for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display the archive directory, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=arcdir
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

global $thumbnail_sizes, $Skin, $Item;

if (empty($params)) { // Initialize array with params
    $params = [];
}

// Merge the params from current skin
$params = array_merge([
    'mediaidx_thumb_size' => $Skin->get_setting('mediaidx_thumb_size'),
], $params);

$photocell_styles = '';
if (isset($thumbnail_sizes[$params['mediaidx_thumb_size']])) {
    $photocell_styles = ' style="width:' . $thumbnail_sizes[$params['mediaidx_thumb_size']][1] . 'px;'
        . 'height:' . $thumbnail_sizes[$params['mediaidx_thumb_size']][2] . 'px"';
}


// Variable Option Disp Mediaidx
// ======================================================================== /
if ($Skin->get_setting('mediaidx_title') == 1) {
    $title_image = true;
} else {
    $title_image = false;
}

$mediaidx_grid = $Skin->get_setting('mediaidx_grid');
$grid = "";
if ($mediaidx_grid == 'one_column') {
    $grid = "one";
} elseif ($mediaidx_grid == 'three_column') {
    $grid = 'three';
}

$style = '';
if ($Skin->get_setting('mediaidx_style') == 'box') {
    $style = 'box ';
}

$show_title = '';
if ($Skin->get_setting('mediaidx_title') == 1) {
    $show_title = "title ";
}

$title_overlay = '';
if ($Skin->get_setting('mediaidx_title_style') == 'hover' && $Skin->get_setting('mediaidx_title') == 1) {
    $title_overlay = 'title_overlay';
}


// --------------------------------- START OF MEDIA INDEX --------------------------------
skin_widget([
    // CODE for the widget:
    'widget' => 'coll_media_index',
    // Optional display params
    'block_start' => '<div class="evo_widget $wi_class$">',
    'block_end' => '</div>',
    'block_display_title' => false,
    'thumb_size' => $params['mediaidx_thumb_size'],
    'thumb_layout' => 'list',
    'disp_image_title' => $title_image,
    'list_start' => '<ul class="evo_image_index">',
    'list_end' => '</ul>',
    // 'item_start'         => '<li><figure'.$photocell_styles.'>',
    'item_start' => '<li class="grid-item ' . $grid . '"><figure class="' . $style . $show_title . $title_overlay . '">',
    'item_end' => '</figure></li>',
    'order_by' => $Blog->get_setting('orderby'),
    'order_dir' => $Blog->get_setting('orderdir'),
    'limit' => 100,
]);
// ---------------------------------- END OF MEDIA INDEX ---------------------------------
