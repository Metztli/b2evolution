<?php
/**
 * This is the template that displays the item block: title, author, content (sub-template), tags, comments (sub-template)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
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

global $Item, $Skin, $app_version;

// Default params:
$params = array_merge([
    'feature_block' => false,			// fp>yura: what is this for??
    // Classes for the <article> tag:
    'item_class' => 'evo_post evo_content_block',
    'item_type_class' => 'evo_post__ptyp_',
    'item_status_class' => 'evo_post__',
    // Controlling the title:
    'disp_title' => true,
    'item_title_line_before' => '<div class="evo_post_title">',	// Note: we use an extra class because it facilitates styling
    'item_title_before' => '<h3>',
    'item_title_after' => '</h3>',
    'item_title_single_before' => '<h1>',	// This replaces the above in case of disp=single or disp=page
    'item_title_single_after' => '</h1>',
    'item_title_line_after' => '</div>',
    // Controlling the content:
    'content_mode' => 'auto',
    // excerpt|full|normal|auto -- auto will auto select depending on $disp-detail
    'image_class' => 'img-responsive',
    'image_size' => 'fit-1280x720',
    'author_link_text' => 'preferredname',
], $params);

$column = '';
$post_item = '';
if($Skin->get_setting('layout_posts') == 'masonry' && $disp == 'posts' && ! $Item->is_intro()) {
    $column = ' ' . $Skin->change_class('posts_masonry_column');
    $post_item = 'post_items';
}

echo '<div class="evo_content_block ' . $post_item . $column . '">'; // Beginning of post display

?>

<?php if($disp == 'posts' && $Skin->get_setting('layout_posts') == 'regular' || $Skin->get_setting('layout_posts') == 'masonry') : ?>
	<div class="<?php echo $Item->is_intro() ? 'timeline evo_intro_post' : 'timeline'; ?>"></div>
<?php endif; ?>

<article id="<?php $Item->anchor_id() ?>" class="<?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>">

	<?php
        if($disp == 'posts') {	// Display images that are linked to this post:
            $Item->images([
                'before_images' => '<div class="evo_post_images">',
                'before_image' => '<div class="evo_post_images"><figure class="evo_image_block special_cover_image_masonry">',
                'before_image_legend' => '<figcaption class="evo_image_legend">',
                'after_image_legend' => '</figcaption>',
                'after_image' => '</figure></div>',
                'after_images' => '</div>',
                'image_class' => 'img-responsive',
                'image_size' => 'fit-1280x720',
                'image_limit' => 1,
                'image_link_to' => 'original', // Can be 'original', 'single' or empty

                // We DO NOT want to display galleries here, only one cover image
                'gallery_image_limit' => 0,
                'gallery_colls' => 0,

                // We want ONLY cover image to display here
                'restrict_to_image_position' => 'cover',
            ]);
        }
?>

	<header>
		<?php
    $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)

// ------- Title -------
if($params['disp_title']) {
    if($disp == 'single' || $disp == 'page') {
        $title_before = $params['item_title_single_before'];
        $title_after = $params['item_title_single_after'];
    } else {
        $title_before = $params['item_title_before'];
        $title_after = $params['item_title_after'];
    }

    // EDIT LINK:
    $edit_link = '';
    if($Item->is_intro()) { // Display edit link only for intro posts, because for all other posts the link is displayed on the info line.
        ob_start();
        $Item->edit_link([
            'before' => '<div class="' . button_class('group') . '">',
            'after' => '</div>',
            'text' => $Item->is_intro() ? get_icon('edit') . ' ' . T_('Edit Intro') : '#',
            'class' => button_class('text'),
        ]);
        $edit_link = ob_get_contents();
        ob_clean();
    }
}
?>

	<?php
if(! $Item->is_intro()) { // Don't display the following for intro posts
    if($disp != 'page') {
        // ------------------------- "Item Single - Header" CONTAINER EMBEDDED HERE --------------------------
        // Display container contents:
        widget_container('item_single_header', [
            'widget_context' => 'item',	// Signal that we are displaying within an Item
            // The following (optional) params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            // This will enclose each widget in a block:
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            // This will enclose the title of each widget:
            'block_title_start' => '<h3>',
            'block_title_end' => '</h3>',
            // Template params for "Item Next/Previous" widget:
            'widget_item_next_previous_params' => [
                'block_start' => '<ul class="pager">',
                'prev_start' => '<li class="previous">',
                'prev_end' => '</li>',
                'next_start' => '<li class="next">',
                'next_end' => '</li>',
                'block_end' => '</ul>',
            ],
            // Template params for "Item Title" widget:
            'widget_item_title_params' => [
                'before' => '<div class="evo_post_title">' . $title_before,
                'after' => $title_after . '</div>',
                'link_type' => '#',
            ],
            // Template params for "Item Visibility Badge" widget:
            'widget_item_visibility_badge_display' => (! $Item->is_intro() && $Item->status != 'published'),
            'widget_item_visibility_badge_template' => '<div class="evo_status evo_status__$status$ badge pull-right" data-toggle="tooltip" data-placement="top" title="$tooltip_title$">$status_title$</div>',


            'author_link_text' => $params['author_link_text'],
        ]);
        // ----------------------------- END OF "Item Single - Header" CONTAINER -----------------------------
    }
}
?>
	</header>

	<?php
if($disp == 'single') {
    // ------------------------- "Item Single" CONTAINER EMBEDDED HERE --------------------------
    // Display container contents:
    widget_container('item_single', [
        'widget_context' => 'item',	// Signal that we are displaying within an Item
        // The following (optional) params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        // This will enclose each widget in a block:
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // Template params for "Item Link" widget
        'widget_item_link_before' => '<p class="evo_post_link">',
        'widget_item_link_after' => '</p>',
        // Template params for "Item Tags" widget
        'widget_item_tags_before' => '<div class="post_tags">',
        'widget_item_tags_after' => '</div>',
        'widget_item_tags_separator' => '',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
        // Template params for "Item Attachments" widget:
        'widget_item_attachments_params' => [
            'limit_attach' => 1000,
            'before' => '<div class="evo_post_attachments"><h3>' . T_('Attachments') . ':</h3><ul class="evo_files">',
            'after' => '</ul></div>',
            'before_attach' => '<li class="evo_file">',
            'after_attach' => '</li>',
            'before_attach_size' => ' <span class="evo_file_size">(',
            'after_attach_size' => ')</span>',
        ],
    ]);
    // ----------------------------- END OF "Item Single" CONTAINER -----------------------------
} elseif($disp == 'page') {
    // ------------------------- "Item Page" CONTAINER EMBEDDED HERE --------------------------
    // Display container contents:
    widget_container('item_page', [
        'widget_context' => 'item',	// Signal that we are displaying within an Item
        // The following (optional) params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        // This will enclose each widget in a block:
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // Template params for "Item Link" widget
        'widget_item_link_before' => '<p class="evo_post_link">',
        'widget_item_link_after' => '</p>',
        // Template params for "Item Tags" widget
        'widget_item_tags_before' => '<div class="post_tags">',
        'widget_item_tags_after' => '</div>',
        'widget_item_tags_separator' => '',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
        // Template params for "Item Attachments" widget:
        'widget_item_attachments_params' => [
            'limit_attach' => 1000,
            'before' => '<div class="evo_post_attachments"><h3>' . T_('Attachments') . ':</h3><ul class="evo_files">',
            'after' => '</ul></div>',
            'before_attach' => '<li class="evo_file">',
            'after_attach' => '</li>',
            'before_attach_size' => ' <span class="evo_file_size">(',
            'after_attach_size' => ')</span>',
        ],
    ]);
    // ----------------------------- END OF "Item Page" CONTAINER -----------------------------
} else {
    // this will create a <section>
    // ---------------------- POST CONTENT INCLUDED HERE ----------------------
    skin_include('_item_content.inc.php', $params);
    // Note: You can customize the default item content by copying the generic
    // /skins/_item_content.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
    // this will end a </section>
}
?>

	<footer>

		<?php
        if(! $Item->is_intro()) { // Do NOT apply tags, comments and feedback on intro posts
            ?>

		<nav class="post_comments_link">
			<?php
            // Link to comments, trackbacks, etc.:
            $Item->feedback_link([
                'type' => 'comments',
                'link_before' => '',
                'link_after' => '',
                'link_text_zero' => '#',
                'link_text_one' => '#',
                'link_text_more' => '#',
                'link_title' => '#',
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
            ?>
		</nav>
		<?php } ?>
	</footer>

	<?php
        // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
        skin_include('_item_feedback.inc.php', array_merge([
            'before_section_title' => '<div class="clearfix"></div><h3 class="evo_comment__list_title">',
            'after_section_title' => '</h3>',
            'before_images' => '<div class="evo_post_images">',
            'before_image' => '<figure class="evo_image_block">',
            'after_image' => '</figure>',
            'after_images' => '</div>',
        ], $params));
// Note: You can customize the default item feedback by copying the generic
// /skins/_item_feedback.inc.php file into the current skin folder.
// ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
?>

	<?php
if(evo_version_compare($app_version, '6.7') >= 0) {    // We are running at least b2evo 6.7, so we can include this file:
    // ------------------ WORKFLOW PROPERTIES INCLUDED HERE ------------------
    skin_include('_item_workflow.inc.php');
    // ---------------------- END OF WORKFLOW PROPERTIES ---------------------
}
?>

	<?php if ($disp == 'single' || $disp == 'page') { ?>
		<div class="item_meta_comments">
			<?php
        if(evo_version_compare($app_version, '6.7') >= 0) {    // We are running at least b2evo 6.7, so we can include this file:
            // ------------------ META COMMENTS INCLUDED HERE ------------------
            skin_include('_item_meta_comments.inc.php', [
                'comment_start' => '<article class="evo_comment evo_comment__meta panel panel-default">',
                'comment_end' => '</article>',
                'comment_post_before' => '<h3 class="evo_comment_post_title">',
                'comment_post_after' => '</h3>',
                'comment_title_before' => '<div class="panel-heading"><h4 class="evo_comment_title panel-title">',
                'comment_title_after' => '</h4></div><div class="panel-body">',
            ]);
            // ---------------------- END OF META COMMENTS ---------------------
        }
	    ?>
		</div>
	<?php } ?>

	<?php
	    locale_restore_previous();	// Restore previous locale (Blog locale)
?>

</article>

<?php echo '</div>'; // End of post display?>
