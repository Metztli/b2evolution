<?php
/**
 * This is the template that displays the category directory for a blog
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display the archive directory, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=catdir
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if(! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Blog, $Item;

// Default params:
$params = array_merge([
    'item_class' => 'evo_post evo_content_block three_column',
    'item_type_class' => 'evo_post__ptyp_',
    'item_status_class' => 'evo_post__',
], $params);

// Column Class
$column = $Skin->Change_class('gallery_show');
$cat_post_style = $Skin->get_setting('cat_post_style');
$cp_bg_img = '';
if($cat_post_style == 'bg_img') {
    $cp_bg_img = 'post_bg_image';
}


// --------------------------------- START OF POSTS -------------------------------------
// Display message if no post:
$params_no_content = [
    'before' => '<div class="msg_nothing">',
    'after' => '</div>',
    'msg_empty_logged_in' => T_('Sorry, there is nothing to display...'),
    // This will display if the collection has not been made private. Otherwise we will be redirected to a login screen anyways
    'msg_empty_not_logged_in' => T_('This site has no public contents.'),
];

// Get only root categories of this blog
$ChapterCache = &get_ChapterCache();
$chapters = $ChapterCache->get_chapters($Blog->ID, 0, true);
// Boolean var to know when at least one post is displayed
$no_content_to_display = true;

?>
<div class="categories_list">
    <?php
    if(! empty($chapters)) { // Display the posts with chapters
        foreach($chapters as $Chapter) {
            // Get the posts of current category
            $ItemList = new ItemList2($Blog, $Blog->get_timestamp_min(), $Blog->get_timestamp_max());
            $ItemList->set_filters([
                'cat_array' => [$Chapter->ID], // Limit only by selected cat (exclude posts from child categories)
                'cat_modifier' => null,
                'unit' => 'all', // Display all items of this category, Don't limit by page
            ]);
            ?>

    <div class="cat_content">
        <?php
                    $ItemList->query();
            if($ItemList->result_num_rows > 0) {
                $no_content_to_display = false;
                ?>
    	<div class="category_title clear">
            <h2><a href="<?php echo $Chapter->get_permanent_url(); ?>"><?php echo $Chapter->get('name'); ?></a></h2>
        </div>

        <div id="cats_list_<?php echo $Chapter->ID; ?>" class="cats_list">
            <?php
                    while($Item = &$ItemList->get_item()) {
                        // For each blog post, do everything below up to the closing curly brace "}"
                        // Temporarily switch to post locale (useful for multilingual blogs)
                        $Item->locale_temp_switch();
                        ?>
            <div id="<?php $Item->anchor_id() ?>" class="<?php $Item->div_classes($params);
                        echo ' ' . $column; ?> " lang="<?php $Item->lang() ?>">
                <section class="evo_post_content <?php echo $cp_bg_img; ?>">
                <?php
                                $hover = $Skin->Change_class('gallery_hover_style');
                        // Display images that are linked to this post:
                        $item_first_image = $Item->get_images([
                            'before' => '<div class="evo_post_images ' . $hover . '">',
                            'before_image' => '<figure class="evo_image_block ">',
                            'before_image_legend' => '<figcaption class="evo_image_legend">',
                            'after_image_legend' => '</figcaption>',
                            'after_image' => '</figure>',
                            'after' => '</div>',
                            'image_class' => 'img-responsive',
                            'image_size' => $Skin->get_setting('gallery_thumb_size'),
                            'image_link_to' => 'single',
                            'image_desc' => '',
                            'gallery_image_limit' => 0, // Don't use images from attached folders.
                            'limit' => 1, // Get only first attached image depending on position priority, see param below:
                            'restrict_to_image_position' => 'teaser,aftermore,inline',
                            'get_rendered_attachments' => false,
                            // Sort the attachments to get firstly "Cover", then "Teaser", and "After more" as last order
                            'links_sql_select' => ', CASE '
                                    . 'WHEN link_position = "teaser"    THEN "1" '
                                    . 'WHEN link_position = "aftermore" THEN "2" '
                                    . 'WHEN link_position = "inline"    THEN "3" '
                                    // .'ELSE "99999999"' // Use this line only if you want to put the other position types at the end
                                . 'END AS position_order',
                            'links_sql_orderby' => 'position_order, link_order',
                        ]);
                        if(empty($item_first_image)) { // No images in this post, Display an empty block
                            $item_first_image = '<div class="no_image ' . $hover . '"><a href="' . $Item->get_permanent_url('album_nopic') . '"><img src="' . $Skin->get_url() . 'assets/images/blank_image.png"></a></div>';
                        } elseif($item_first_image == 'plugin_render_attachments') { // No images, but some attachments(e.g. videos) are rendered by plugins
                            $item_first_image = $Item->get_permanent_link('<b>' . T_('Click to see contents') . '</b>', '#', 'album_nopic');
                        }

                        // Print first image
                        // if ( $cat_post_style !== 'bg_img' ) {
                        echo $item_first_image;
                        // }

                        echo '<div class="avatar_post">';
                        // Author Avatar
                        $Item->author([
                            'before_user' => '',
                            'after_user' => '',
                            'link_text' => 'only_avatar', // avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
                            'link_class' => 'author_avatar',
                            'thumb_size' => 'crop-64x64',
                            'thumb_class' => '',
                        ]);

                        // Author Name
                        $Item->author([
                            // 'before'       => T_('By '),
                            'after' => '',
                            'before_user' => '',
                            'after_user' => '',
                            'link_text' => 'fullname', // avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
                            'link_class' => 'author_avatar',
                            'thumb_size' => 'crop-48x48',
                            'thumb_class' => '',
                        ]);
                        echo '</div>';

                        echo '<div class="evo_post_title">';
                        // Display a title
                        echo $Item->get_title([
                            'before' => '<h3>',
                            'after' => '</h3>',
                        ]);

                        // We want to display the post time:
                        $Item->issue_time([
                            'before' => '<time class="date">',
                            'after' => '</time>',
                            'time_format' => 'F j, Y',
                        ]);

                        // Link for editing
                        $Item->edit_link([
                            'before' => '<span class="edit_post">',
                            'after' => '</span>',
                        ]);
                        echo '</div>';
                        // Restore previous locale (Blog locale)
                        locale_restore_previous();

                        if($Item->status != 'published') {
                            $Item->format_status([
                                'template' => '<div class="evo_status evo_status__$status$ badge">$status_title$</div>',
                            ]);
                        }

                        if($Skin->get_setting('cat_post_excerpt')) :
                            echo '<div class="evo_post__text">';
                            // We want excerpt here - shrinked post
                            echo $Item->excerpt([
                                'before' => '',
                                'after' => '',
                                'excerpt_more_text' => T_('Read More') . '<span class="fa fa-angle-right"></span>',
                            ]);
                            echo '</div> <!-- evo_post_text -->';
                        endif;

                        ?>
            		<footer>
            			<?php // FOOTER OF THE POST
                                if(! $Item->is_intro()) { // Do NOT apply tags, comments and feedback on intro posts
                                    // List all tags attached to this post:
                                    if ($Skin->get_setting('cat_post_tags') == 1) :
                                        $Item->tags([
                                            'before' => '<nav class="post_tags">',
                                            'after' => '</nav>',
                                            'separator' => ' ',
                                        ]);
                                    endif;
                                    ?>

            			<?php
                                        if ($Skin->get_setting('cat_post_comment') == 1) :
                                            echo '<nav class="comment_info">';
                                            // Link to comments, trackbacks, etc.:
                                            $Item->feedback_link([
                                                'type' => 'comments',
                                                'link_before' => '',
                                                'link_after' => '',
                                                'link_text_zero' => '#',
                                                'link_text_one' => '#',
                                                'link_text_more' => '#',
                                                'link_title' => '#',
                                                'link_class' => 'comment_cat',
                                                // fp> WARNING: creates problem on home page: 'link_class' => 'btn btn-default btn-sm',
                                                // But why do we even have a comment link on the home page ? (only when logged in)
                                            ]);

                                            // Link to comments, trackbacks, etc.:
                                            $Item->feedback_link([
                                                'type' => 'trackbacks',
                                                'link_before' => ' &bull; ',
                                                'link_after' => '',
                                                'link_text_zero' => '#',
                                                'link_text_one' => '#',
                                                'link_text_more' => '#',
                                                'link_title' => '#',
                                            ]);
                                            echo '</nav>';
                                        endif;
                                    ?>
                	<?php } ?>
            		</footer>
	            </section>  <!-- ../content_end_full -->
            </div> <!-- .evo_post -->
        <?php }
                    echo '</div>';
            }
            echo '</div><!-- end main_evo_post -->';
        }
    } // ---------------------------------- END OF POSTS ------------------------------------

if($no_content_to_display) { // No category and no post in this blog
    echo $params_no_content['before']
        . (is_logged_in() ? $params_no_content['msg_empty_logged_in'] : $params_no_content['msg_empty_not_logged_in'])
        . $params_no_content['after'];
}
?>
