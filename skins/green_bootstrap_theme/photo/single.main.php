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

global $Skin;
// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);
// TODO: move to Skin::display_init
require_js_defer('functions.js', 'blog');	// for opening popup window (comments)
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'arcdir_text' => T_('Index'),
    'catdir_text' => T_('Galleries'),
    'category_text' => T_('Gallery') . ': ',
    'categories_text' => T_('Galleries') . ': ',
]);
// -------------------------------- END OF HEADER --------------------------------
// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>


<div class="container">

<header class="row">

	<div class="col-xs-12 col-sm-12 col-md-4 col-md-push-8">
		<?php
            // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
            // Display container and contents:
            widget_container('page_top', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => true, // Display container anyway even if no widget
                'block_start' => '<div class="evo_widget $wi_class$">',
                'block_end' => '</div>',
                'block_display_title' => false,
                'list_start' => '<ul>',
                'list_end' => '</ul>',
                'item_start' => '<li>',
                'item_end' => '</li>',
            ]);
// ----------------------------- END OF "Page Top" CONTAINER -----------------------------
?>

		<?php
    skin_widget([
    // CODE for the widget:
        'widget' => 'coll_member_count',
        // Optional display params
        'block_start' => '<span>',
        'block_end' => '</span>',
        'before' => '(',
        'after' => ')',
]);
?>
	</div><!-- .col -->

		<?php
    // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
    // Display container and contents:
    widget_container('header', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => true, // Display container anyway even if no widget
        'container_start' => '<div class="col-xs-12 col-sm-12 col-md-8 col-md-pull-4"><div class="evo_container $wico_class$">',
        'container_end' => '</div></div>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h1>',
        'block_title_end' => '</h1>',
    ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>

</header><!-- .row -->

		<?php
            // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
            // Display container and contents:
            // Note: this container is designed to be a single <ul> list
    widget_container('menu', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<nav class="row"><div class="col-xs-12"><ul class="nav nav-tabs evo_container $wico_class$">',
        'container_end' => '</ul></div></nav>',
        'block_start' => '',
        'block_end' => '',
        'block_display_title' => false,
        'list_start' => '',
        'list_end' => '',
        'item_start' => '<li class="evo_widget $wi_class$">',
        'item_end' => '</li>',
        'item_selected_start' => '<li class="active evo_widget $wi_class$">',
        'item_selected_end' => '</li>',
        'item_title_before' => '',
        'item_title_after' => '',
    ]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>

<main><!-- This is were a link like "Jump to main content" would land -->

	<!-- =================================== START OF POST TITLE BAR =================================== -->

	<?php
if ($single_Item = &mainlist_get_item()) { // Get Item here, because it can be not defined yet, e.g. in Preview mode
    ?>
		<div class="row">
			<div class="col-xs-12">
			<nav class="nav_album">

			<a href="<?php $Blog->disp('url', 'raw') ?>" title="<?php echo format_to_output(T_('All Albums'), 'htmlattr'); ?>" class="all_albums">All Albums</a>

			<span class="nav_album_title">
				<?php
                $single_Item->title([
    'link_type' => 'permalink',
                    'before' => '',
                    'after' => '',
]);
    ?>
				<?php
        if ($Skin->enabled_status_banner($single_Item->status)) { // Status banner
            $single_Item->format_status([
                'template' => '<div class="evo_status evo_status__$status$ badge" data-toggle="tooltip" data-placement="top" title="$tooltip_title$">$status_title$</div>',
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
			</span><!-- .nav_album_title -->

			<?php
    // ------------------- PREV/NEXT POST LINKS (SINGLE POST MODE) -------------------
    item_prevnext_links([
        'template' => '$prev$$next$',
        'block_start' => '<ul class="pager hidden-xs">',
        'next_class' => 'next',
        'next_start' => '<li class="next">',
        'next_text' => 'Next',
        'next_no_item' => '',
        'next_end' => '</li>',
        'prev_class' => 'previous',
        'prev_start' => '<li class="previous">',
        'prev_text' => 'Previous',
        'prev_no_item' => '',
        'prev_end' => '',
        'block_end' => '</ul>',
    ]);
    // ------------------------- END OF PREV/NEXT POST LINKS -------------------------
    ?>

			<div class="clear"></div>

			</nav><!-- .nav_album -->
			</div><!-- .col -->
		</div><!-- .row -->
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

	<article class="row">

	<?php
    $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
?>

	<div class="post_images col-xl-9 col-lg-9 col-md-8 col-sm-7">
		<?php
            // Display images that are linked to this post:
        $Item->images([
            'before' => '',
            'before_image' => '<figure class="single-image col-xl-4 col-lg-4 col-md-6 col-sm-12">',
            'before_image_legend' => '<figcaption class="evo_image_legend">',
            'after_image_legend' => '</figcaption>',
            'after_image' => '</figure>',
            'after' => '<div class="clear"></div>',
            'image_size' => $Skin->get_setting('single_thumb_size'),
            'image_align' => 'middle',
            'image_class' => 'img-responsive',
            'before_gallery' => '<div class="evo_post_gallery">',
            'after_gallery' => '</div>',
            'gallery_table_start' => '',
            'gallery_table_end' => '',
            'gallery_row_start' => '',
            'gallery_row_end' => '',
            'gallery_cell_start' => '<div class="evo_post_gallery__image">',
            'gallery_cell_end' => '</div>',
        ]);
?>
	</div>

	<div class="evo_post_content col-xl-3 col-lg-3 col-md-4 col-sm-5">

		<div class="evo_details">

				<?php
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
            // Template params for "Item Link" widget
            'widget_item_link_before' => '<p class="evo_post_link">',
            'widget_item_link_after' => '</p>',
        ]);
// ----------------------------- END OF "Item Single" CONTAINER -----------------------------
?>

			<div class="item_comments">
				<?php
                    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', [
        'disp_comments' => true,
        'disp_comment_form' => true,
        'disp_trackbacks' => true,
        'disp_trackback_url' => true,
        'disp_pingbacks' => true,
        'disp_webmentions' => true,
        'disp_meta_comments' => false,
        'before_section_title' => '<h4>',
        'after_section_title' => '</h4>',
        'author_link_text' => 'auto',
        'comment_image_size' => 'fit-256x256',
        // Pagination:
        'pagination' => [
            'block_start' => '<div class="center"><ul class="pagination">',
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
if (evo_version_compare($app_version, '6.7') >= 0) {	// We are running at least b2evo 6.7, so we can include this file:
    // ------------------ INTERNAL COMMENTS INCLUDED HERE ------------------
    skin_include('_item_meta_comments.inc.php', [
        'comment_start' => '<article class="evo_comment evo_comment__meta panel panel-default">',
        'comment_end' => '</article>',
    ]);
    // ---------------------- END OF INTERNAL COMMENTS ---------------------
}
?>
			</div>

		</div>

	</div>

	<?php
        locale_restore_previous();	// Restore previous locale (Blog locale)
?>

	</article><!-- .row -->

</main>


<footer class="row">

	<!-- =================================== START OF FOOTER =================================== -->
	<div class="col-md-12">

		<?php
            // Display container and contents:
        widget_container('footer', [
            // The following params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            'container_start' => '<div class="evo_container $wico_class$ clearfix">', // Note: clearfix is because of Bootstraps' .cols
            'container_end' => '</div>',
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
        ]);
?>

		<p class="center">
			<?php
        // Display footer text (text can be edited in Blog Settings):
        $Blog->footer_text([
            'before' => '',
            'after' => ' &bull; ',
        ]);
?>

			<?php
    // Display a link to contact the owner of this blog (if owner accepts messages):
    $Blog->contact_link([
        'before' => '',
        'after' => ' &bull; ',
        'text' => T_('Contact'),
        'title' => T_('Send a message to the owner of this blog...'),
    ]);
// Display a link to help page:
$Blog->help_link([
    'before' => ' ',
    'after' => ' ',
    'text' => T_('Help'),
]);
?>

			<?php
    // Display additional credits:
    // If you can add your own credits without removing the defaults, you'll be very cool :))
    // Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
    credits([
        'list_start' => '&bull;',
        'list_end' => ' ',
        'separator' => '&bull;',
        'item_start' => ' ',
        'item_end' => ' ',
    ]);
?>
		</p>

		<?php
// Please help us promote b2evolution and leave this logo on your blog:
powered_by([
    'block_start' => '<div class="powered_by">',
    'block_end' => '</div>',
    // Check /rsc/img/ for other possible images -- Don't forget to change or remove width & height too
    'img_url' => '$rsc$img/powered-by-b2evolution-120t.gif',
    'img_width' => 120,
    'img_height' => 32,
]);
?>
	</div><!-- .col -->

</footer><!-- .row -->


</div><!-- .container -->


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>
