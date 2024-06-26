<?php
/**
 * This is the template that displays the site map (the real one, not the XML thing) for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display the archive directory, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=postidx
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

// Note: this is a very imperfect sitemap, but it's a start :)
?>

<div class="widget_common_links">
    <h3 class="title_widget_sitemap">Common links</h3>
    <?php
    // --------------------------------- START OF COMMON LINKS --------------------------------
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_common_links',
        // Optional display params
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_display_title' => false,
        'show_recently' => 1,
        'show_postidx' => 0,
        'show_archives' => 1,
        'show_categories' => 0,
        'show_mediaidx' => 1,
        'show_latestcomments' => 1,
        'show_owneruserinfo' => 1,
        'show_ownercontact' => 1,
        'show_sitemap' => 0,
    ]);
// ---------------------------------- END OF COMMON LINKS ---------------------------------
?>
</div>

<div class="cateory_list">
    <h3 class="title_widget_sitemap">Categories</h3>
    <?php
        // --------------------------------- START OF CATEGORY LIST --------------------------------
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_category_list',
        // Optional display params
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_display_title' => false,
    ]);
// ---------------------------------- END OF CATEGORY LIST ---------------------------------
?>
</div>

<div class="post_list">
    <h3 class="title_widget_sitemap">Posts</h3>
    <?php
        // --------------------------------- START OF POST LIST --------------------------------
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_post_list',
        // Optional display params
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_display_title' => false,
        'order_by' => 'title',
        'order_dir' => 'ASC',
        'limit' => null,
    ]);
// ---------------------------------- END OF POST LIST ---------------------------------
?>
</div>

<div class="widge_page_list">
    <h3 class="title_widget_sitemap">Pages</h3>
    <?php
        // --------------------------------- START OF PAGE LIST --------------------------------
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_page_list',
        // Optional display params
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_display_title' => false,
        'order_by' => 'title',
        'order_dir' => 'ASC',
        'limit' => null,
    ]);
// ---------------------------------- END OF PAGE LIST ---------------------------------
?>
</div>
