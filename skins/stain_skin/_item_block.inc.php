<?php
/**
 * This is the template that displays the item block: title, author, content (sub-template), tags, comments (sub-template)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
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
    'item_title_before' => '<h2>',
    'item_title_after' => '</h2>',
    'item_title_single_before' => '<h1>',	// This replaces the above in case of disp=single or disp=page
    'item_title_single_after' => '</h1>',
    'item_title_line_after' => '</div>',
    // Controlling the content:
    'before_content_teaser' => '',
    'after_content_teaser' => '',
    'content_mode' => 'auto',		// excerpt|full|normal|auto -- auto will auto select depending on $disp-detail
    'image_class' => 'img-responsive',
    'image_size' => 'fit-1280x720',
    'image_link_to' => 'original', // Can be 'original', 'single' or empty
    'author_link_text' => 'preferredname',
    'before_images' => '<div class="evo_post_images">',
    'before_image' => '<figure class="evo_image_block">',
    'before_image_legend' => '<figcaption class="evo_image_legend">',
    'after_image_legend' => '</figcaption>',
    'after_image' => '</figure>',
    'after_images' => '</div>',
    'before_gallery' => '<div class="evo_post_gallery clearfix">',
    'after_gallery' => '</div>',
], $params);
?>

<article id="<?php $Item->anchor_id() ?>" class="<?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>">

    <?php
        if ($disp == 'page') {
            // Display images that are linked to this post:
            $Item->images([
                // Optionally restrict to files/images linked to specific position: 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'
                'restrict_to_image_position' => 'cover',
                'before' => '<div class="evo_post_images cover_image">',
                'before_image' => $params['before_image'],
                'before_image_legend' => $params['before_image_legend'],
                'after_image_legend' => $params['after_image_legend'],
                'after_image' => $params['after_image'],
                'after' => $params['after_images'],
                'image_class' => $params['image_class'],
                'image_size' => 'original',
                'limit' => 1,
                'image_link_to' => $params['image_link_to'],
            ]);
        }
?>

	<header class="evo_post_header">
    	<?php
        $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
// ------- Title -------
if ($params['disp_title']) {
    echo $params['item_title_line_before'];
    if ($disp == 'single' || $disp == 'page') {
        $title_before = $params['item_title_single_before'];
        $title_after = $params['item_title_single_after'];
    } else {
        $title_before = $params['item_title_before'];
        $title_after = $params['item_title_after'];
    }
    // POST TITLE:
    $Item->title([
        'before' => $title_before,
        'after' => $title_after,
        'link_type' => '#',
    ]);
    // EDIT LINK:
    if ($Item->is_intro()) { // Display edit link only for intro posts, because for all other posts the link is displayed on the info line.
        $Item->edit_link([
            'before' => '<div class="' . button_class('group') . '">',
            'after' => '</div>',
            'text' => $Item->is_intro() ? get_icon('edit') . ' ' . T_('Edit Intro') : '#',
            'class' => button_class('text'),
        ]);
    }
    echo $params['item_title_line_after'];
}
?>

    	<?php if (! $Item->is_intro()) { // Don't display the following for intro posts?>
    	<div class="evo_post_header_info">
        	<?php
        if ($Item->status != 'published') {
            $Item->format_status([
                'template' => '<div class="evo_status evo_status__$status$ badge pull-right">$status_title$</div>',
            ]);
        }
        // Permalink:
        $Item->permanent_link([
            'text' => '',
        ]);
    	    // We want to display the post time:
    	    $Item->issue_time([
    	        'before' => ' ' . T_('Posted on '),
    	        'after' => ' ',
    	        'time_format' => 'F j, Y',
    	    ]);
    	    // Author
    	    $Item->author([
    	        'before' => ' ' . T_('by') . ' ',
    	        'after' => ' ',
    	        'link_text' => $params['author_link_text'],
    	    ]);
    	    // Categories
    	    $Item->categories([
    	        'before' => T_('in') . ' ',
    	        'after' => ' ',
    	        'include_main' => true,
    	        'include_other' => true,
    	        'include_external' => true,
    	        'link_categories' => true,
    	    ]);
    	    // Link for editing
    	    $Item->edit_link([
    	        'before' => ' &bull; ',
    	        'after' => '',
    	    ]);
    	    ?>
    	</div>
    	<?php
    	} ?>
	</header>

	<?php
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
    	    // Params for skin file "_item_content.inc.php"
    	    'widget_item_content_params' => $params,

    	]);
// ----------------------------- END OF "Item Page" CONTAINER -----------------------------
?>

	<?php
if (! $Item->is_intro()) { // List all tags attached to this post:
    ?>
	<footer>

		<?php
        $Item->tags([
            'before' => '<nav class="small post_tags">',
            'after' => '</nav>',
            'separator' => ' ',
        ]);
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
	</footer>
	<?php } ?>

    <div class="item_comments">
        <?php
    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', [
        'before_section_title' => '<h4 class="tite_comment_status">',
        'after_section_title' => '</h4>',
        'author_link_text' => 'preferredname',
        'comment_image_size' => 'fit-256x256',

        // Pagination:
        'pagination' => [
            'block_start' => '<div class="single_pagination center"><ul class="pagination">',
            'block_end' => '</ul></div>',
            'page_current_template' => '<span>$page_num$</span>',
            'page_item_before' => '<li>',
            'page_item_after' => '</li>',
            'page_item_current_before' => '<li class="active">',
            'page_item_current_after' => '</li>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
        ],
    ]);
// Note: You can customize the default item feedback by copying the generic
// /skins/_item_feedback.inc.php file into the current skin folder.
// ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
?>

        <?php
if (evo_version_compare($app_version, '6.7') >= 0) {  // We are running at least b2evo 6.7, so we can include this file:
    // ------------------ WORKFLOW PROPERTIES INCLUDED HERE ------------------
    skin_include('_item_workflow.inc.php');
    // ---------------------- END OF WORKFLOW PROPERTIES ---------------------
}
?>

        <?php
if (evo_version_compare($app_version, '6.7') >= 0) {  // We are running at least b2evo 6.7, so we can include this file:
    // ------------------ META COMMENTS INCLUDED HERE ------------------
    skin_include('_item_meta_comments.inc.php', [
        'comment_start' => '<article class="evo_comment evo_comment__meta panel panel-default">',
        'comment_end' => '</article>',
    ]);
    // ---------------------- END OF META COMMENTS ---------------------
}
?>
    </div><!-- .item_comments -->

	<?php
locale_restore_previous();	// Restore previous locale (Blog locale)
?>
</article>
