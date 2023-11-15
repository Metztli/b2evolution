<?php
/**
* This is the main/default page template.
*
* For a quick explanation of b2evo 2.0 skins, please start here:
* {@link http://b2evolution.net/man/skin-development-primer}
*
* It is used to display the blog when no specific page template is available to handle the request.
*
* @package evoskins
* @subpackage bootstrap_gallery_skin
*/
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (evo_version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

global $Skin, $Item;

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', []);
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
skin_include('_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------

?>

<main id="content"><!-- This is were a link like "Jump to main content" would land -->
	<div class="container">
		<div class="main_content">
			<!-- =================================== START OF POST TITLE BAR =================================== -->
			<?php

?>

			<?php
if ($single_Item = &mainlist_get_item()) {
    // Display images that are linked to this post:
    $Item->images([
        // Optionally restrict to files/images linked to specific position: 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'
        'restrict_to_image_position' => 'cover',
        'before' => '<div class="evo_post_images cover_image">',
        'before_image' => '',
        'before_image_legend' => '<figcaption class="evo_image_legend">',
        'after_image_legend' => '</figcaption>',
        'after_image' => '</figure>',
        'after' => '</div>',
        'image_class' => 'img-responsive',
        'image_size' => 'original',
        'limit' => 1,
        'image_link_to' => 'original', // Can be 'original', 'single' or empty
    ]);

    // Get Item here, because it can be not defined yet, e.g. in Preview mode?>
			<nav class="nav_album">
				<h3 class="nav_album_title">
					<?php
            $single_Item->title([
                'link_type' => 'none',
                'before' => '',
                'after' => '',
            ]);
    ?>
					<?php
    if ($Skin->enabled_status_banner($single_Item->status)) { // Status banner
        $single_Item->format_status([
            'template' => '<div class="evo_status evo_status__$status$ badge">$status_title$</div>',
        ]);
    }
    $single_Item->edit_link([
        // Link to backoffice for editing
        'before' => '',
        'after' => '',
        'text' => get_icon('edit'),
        'title' => T_('Edit title/description...'),
    ]);
    ?>
				</h3><!-- .nav_album_title -->
				<a href="<?php $Blog->disp('url', 'raw') ?>" title="<?php echo format_to_output(T_('All Albums'), 'htmlattr'); ?>" class="all_albums">All Albums</a>
				<?php
                // ------------------- PREV/NEXT POST LINKS (SINGLE POST MODE) -------------------
                item_prevnext_links([
                    'template' => '$prev$$next$',
                    'block_start' => '<ul class="nav_posts hidden-xs">',
                    'next_class' => 'next',
                    'next_start' => '<li class="next">',
                    'next_text' => T_('Next ') . '<i class="fa fa-angle-right"></i>',
                    'next_no_item' => '',
                    'next_end' => '</li>',
                    'prev_class' => 'previous',
                    'prev_start' => '<li class="previous">',
                    'prev_text' => '<i class="fa fa-angle-left"></i>' . T_(' Previous'),
                    'prev_no_item' => '',
                    'prev_end' => '</li>',
                    'block_end' => '</ul>',
                ]);
    // ------------------------- END OF PREV/NEXT POST LINKS -------------------------
    ?>
			</nav><!-- .nav_album -->
			<?php
} // ------------------- END OF NAVIGATION BAR FOR ALBUM(POST) -------------------
?>

			<?php
            // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
messages([
    'block_start' => '<div class="row"><div class="col-xs-12 action_messages">',
    'block_end' => '</div></div>',
]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>

			<article>
				<?php
        $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)

$column = $Skin->Change_class('single_image_grid');
$grid = '';
$masonry = '';

if ($Skin->get_setting('single_image_style') == 'masonry') {
    $masonry = 'single_masonry';
    switch ($Skin->get_setting('single_image_grid')) {
        case '12':
            $grid = 'one_column';
            break;
        case '6':
            $grid = 'two_column';
            break;
        case '4':
            $grid = 'three_column';
            break;
        case '3':
            $grid = 'four_column';
            break;
        default:
            $grid = 'three_column';
            break;
    }
} else {
    $grid = "col-lg-$column col-md-$column. col-sm-6 col-xs-6";
}

?>
				<div class="single__post_images <?php echo $masonry; ?>">
					<?php
    // Display images that are linked to this post:
    $Item->images([
        'before' => '',
        'before_image' => '<figure class="single-image ' . $grid . '">',
        'before_image_legend' => '<figcaption class="evo_image_legend">',
        'after_image_legend' => '</figcaption>',
        'after_image' => '</figure>',
        'after' => '<div class="clear"></div>',
        'image_size' => $Skin->get_setting('single_thumb_size'),
        'image_align' => 'middle',
        'image_class' => 'img-responsive',
        'before_gallery' => '<div class="evo_post_gallery single-image one_column">',
        'after_gallery' => '</div>',
        'gallery_table_start' => '',
        'gallery_table_end' => '',
        'gallery_row_start' => '',
        'gallery_row_end' => '',
        'gallery_cell_start' => '<div class="evo_post_gallery__image">',
        'gallery_cell_end' => '</div>',
        // 'links_sql_orderby'   => 'position_order, link_order',
    ]);
?>
				</div>

				<div class="evo_post_content">
					<div class="evo_details">
							<?php
        // ------------------------- "Item Single - Header" CONTAINER EMBEDDED HERE --------------------------
        // Display container contents:
        widget_container('item_single_header', [
            'widget_context' => 'item',	// Signal that we are displaying within an Item
            // The following (optional) params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            'container_start' => '<div class="evo_container $wico_class$ panel-heading">',
            'container_end' => '</div>',
            // This will enclose each widget in a block:
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            // This will enclose the title of each widget:
            'block_title_start' => '<h3>',
            'block_title_end' => '</h3>',

            // Controlling the title:
            'widget_item_title_params' => [
                'before' => '<h4 class="panel-title">',
                'after' => sprintf(T_(' (%s photos)'), $Item->get_number_of_images()) . '</h4>',
                'link_type' => 'permalink',
            ],
        ]);
// ----------------------------- END OF "Item Single - Header" CONTAINER -----------------------------

// ------------------------- "Item Single" CONTAINER EMBEDDED HERE --------------------------
// WARNING: EXPERIMENTAL -- NOT RECOMMENDED FOR PRODUCTION -- MAY CHANGE DRAMATICALLY BEFORE RELEASE.
// Display container contents:
widget_container('item_single', [
    'widget_context' => 'item',	// Signal that we are displaying within an Item
    // The following (optional) params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => false, // If no widget, don't display container at all
    'container_start' => '<div class="evo_container $wico_class$">',
    'container_end' => '</div>',
    // This will enclose each widget in a block:
    'block_start' => '<div class="$wi_class$">',
    'block_end' => '</div>',
    // This will enclose the title of each widget:
    'block_title_start' => '<h3>',
    'block_title_end' => '</h3>',
    // Params for skin file "_item_content.inc.php"
    'widget_item_content_params' => [
        'feature_block' => false,
        'item_class' => 'evo_post',
        'item_type_class' => 'evo_post__ptyp_',
        'item_status_class' => 'evo_post__',
        'content_mode' => 'full', // We want regular "full" content, even in category browsing: i-e no excerpt or thumbnail
        'image_size' => '', // Do not display images in content block - Image is handled separately
        'url_link_text_template' => '', // link will be displayed (except player if podcast)
    ],
    // Template params for "Item Tags" widget
    'widget_item_tags_before' => '<div class="evo_post_tags">',
    'widget_item_tags_after' => '</div>',
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
?>

						<div class="item_comments">
							<?php
                            // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
skin_include('_item_feedback.inc.php', [
    'before_section_title' => '<h4 class="tite_comment_status">',
    'after_section_title' => '</h4>',
    'author_link_text' => 'preferredname',
    // 'comment_image_size'   => 'fit-256x256',

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

					</div><!-- .evo_details -->
				</div><!-- .evo_post_content -->

				<?php
                locale_restore_previous();	// Restore previous locale (Blog locale)
?>

			</article><!-- .row -->
		</div><!-- .main_content -->
	</div><!-- .container -->
</main><!-- #main_content -->


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
skin_include('_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>
