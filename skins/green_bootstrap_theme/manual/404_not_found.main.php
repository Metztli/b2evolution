<?php
/**
 * This is the 404 page template for the "bootstrap_manual" skin.
 *
 * This skin only uses one single template which includes most of its features.
 * It will also rely on default includes for specific dispays (like the comment form).
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 * @subpackage bootstrap_manual
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (evo_version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}


if (! empty($requested_404_title)) { // Initialize a prefilled search form
    set_param('s', str_replace('-', ' ', $requested_404_title));
    set_param('sentence', 'OR');
    set_param('title', ''); // Empty this param to exclude a filter by post_urltitle
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', []);
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>

<div class="container">

<header id="header" class="row<?php echo $Settings->get('site_skins_enabled') ? ' site_skins' : ''; ?>">

	<?php
        // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
        widget_container('page_top', [
            // The following params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => true, // Display container anyway even if no widget
            'container_start' => '<div class="col-xs-12 col-sm-12 col-md-4 col-md-push-8"><div class="evo_container $wico_class$">',
            'container_end' => '</div></div>',
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
    'container_start' => '<nav class="row"><div class="col-md-12"><ul class="nav nav-tabs evo_container $wico_class$">',
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

<div class="row">

	<div class="<?php echo $Skin->is_side_navigation_visible() ? 'col-xs-12 col-md-9 pull-right-md' : 'col-md-12' ?>">

		<main><!-- This is were a link like "Jump to main content" would land -->

		<!-- =================================== START OF MAIN AREA =================================== -->
		<?php
            if (! in_array($disp, ['login', 'lostpassword', 'register', 'activateinfo'])) { // Don't display the messages here because they are displayed inside wrapper to have the same width as form
                // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
                messages([
                    'block_start' => '<div class="action_messages">',
                    'block_end' => '</div>',
                ]);
                // --------------------------------- END OF MESSAGES ---------------------------------
            }

if (! empty($cat)) { // Display breadcrumbs if some category is selected
    skin_widget([
        // CODE for the widget:
        'widget' => 'breadcrumb_path',
        // Optional display params
        'block_start' => '<nav><ol class="breadcrumb">',
        'block_end' => '</ol></nav>',
        'separator' => '',
        'item_mask' => '<li><a href="$url$">$title$</a></li>',
        'item_logo_mask' => '<li>$logo$ <a href="$url$">$title$</a></li>',
        'item_active_logo_mask' => '<li class="active">$logo$ $title$</li>',
        'item_active_mask' => '<li class="active">$title$</li>',
    ]);
}
?>

		<?php
    // ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
    request_title([
        'title_before' => '<h1 class="page_title">',
        'title_after' => '</h1>',
        'title_single_disp' => false,
        'title_page_disp' => false,
        'format' => 'htmlbody',
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>


<?php
    // --------------------- 404 CONTENT TEMPLATE INCLUDED HERE ----------------------
    echo '<div class="error_404">';

echo '<h1>404 Not Found</h1>';

echo '<p>' . T_('The manual page you are requesting doesn\'t seem to exist (yet).') . '</p>';

$post_title = '';
$post_urltitle = '';
if (! empty($requested_404_title)) { // Set title & urltitle for new post
    $post_title = ucwords(str_replace('-', ' ', $requested_404_title));
    $post_urltitle = $requested_404_title;
}

// Button to create a new page
$write_new_post_url = $Blog->get_write_item_url(0, $post_title, $post_urltitle);
if (! empty($write_new_post_url)) { // Display button to write a new post
    echo '<a href="' . $write_new_post_url . '" class="roundbutton roundbutton_text_noicon">' . T_('Create this page now') . '</a>';
}

echo '<p>' . T_('You can search the manual below.') . '</p>';

echo '</div>';

if (! empty($requested_404_title)) { // Initialize a prefilled search form
    skin_include('_search.disp.php', $Skin->get_template('disp_params'));
    // Note: You can customize the default search by copying the generic
    // /skins/_search.disp.php file into the current skin folder.
} else { // Display a search form with TOC
    echo '<div class="error_additional_content">';
    // --------------------------------- START OF SEARCH FORM --------------------------------
    // Call the coll_search_form widget:
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_search_form',
        // Optional display params:
        'block_start' => '',
        'block_end' => '',
        'title' => T_('Search this manual:'),
        'disp_search_options' => 0,
        'search_class' => 'extended_search_form',
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        'search_class' => 'compact_search_form',
        'search_input_before' => '<div class="input-group">',
        'search_input_after' => '',
        'search_submit_before' => '<span class="input-group-btn">',
        'search_submit_after' => '</span></div>',
    ]);
    // ---------------------------------- END OF SEARCH FORM ---------------------------------

    echo '<p>' . T_('or you can browse the table of contents below:') . '</p>';

    // --------------------------------- START OF CONTENT HIERARCHY --------------------------------
    echo '<h2 class="table_contents">' . T_('Table of contents') . '</h2>';
    skin_widget([
        // CODE for the widget:
        'widget' => 'content_hierarchy',
        // Optional display params
        'display_blog_title' => false,
        'open_children_levels' => 20,
        'class_selected' => '',
        'item_before_opened' => get_icon('collapse'),
        'item_before_closed' => get_icon('expand'),
        'item_before_post' => get_icon('file_message'),
    ]);
    // ---------------------------------- END OF CONTENT HIERARCHY ---------------------------------

    echo '</div>';
}
// ----------------- END OF 404 CONTENT TEMPLATE INCLUDED HERE -------------------
?>


		</main>

	</div><!-- .col -->

	<?php
    if ($Skin->is_side_navigation_visible()) { // Display a left column with navigation only for several pages
        ?>
		<!-- =================================== START OF SIDEBAR =================================== -->
		<aside class="col-xs-12 col-md-3 pull-left-md">

			<div id="evo_container__sidebar">

				<?php
                    // <div data-spy="affix" data-offset-top="165" class="affix_block">
                    // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
                    // Display container and contents:
                    // Note: this container is designed to be a single <ul> list
                        widget_container('sidebar', [
                            // The following (optional) params will be used as defaults for widgets included in this container:
                            'container_display_if_empty' => false, // If no widget, don't display container at all
                            // This will enclose each widget in a block:
                            'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                            'block_end' => '</div>',
                            // This will enclose the title of each widget:
                            'block_title_start' => '<div class="panel-heading"><h4 class="panel-title">',
                            'block_title_end' => '</h4></div>',
                            // This will enclose the body of each widget:
                            'block_body_start' => '<div class="panel-body">',
                            'block_body_end' => '</div>',
                            // This will enclose (foot)notes:
                            'notes_start' => '<div class="small text-muted">',
                            'notes_end' => '</div>',
                            // Widget 'Search form':
                            'search_class' => 'compact_search_form',
                            'search_input_before' => '<div class="input-group">',
                            'search_input_after' => '',
                            'search_submit_before' => '<span class="input-group-btn">',
                            'search_submit_after' => '</span></div>',
                            // Widget 'Content Hierarchy':
                            'item_before_opened' => get_icon('collapse'),
                            'item_before_closed' => get_icon('expand'),
                            'item_before_post' => get_icon('file_message'),
                            'item_title_fields' => 'short_title,title',
                            'sorted' => true,
                        ]);
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>

				<div class="evo_container evo_container__sidebar2">
				<?php
                    // <div data-spy="affix" data-offset-top="165" class="affix_block">
                    // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
                    // Display container and contents:
                    // Note: this container is designed to be a single <ul> list
            widget_container('sidebar_2', [
                // The following (optional) params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                // This will enclose each widget in a block:
                'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                'block_end' => '</div>',
                // This will enclose the title of each widget:
                'block_title_start' => '<div class="panel-heading"><h4 class="panel-title">',
                'block_title_end' => '</h4></div>',
                // This will enclose the body of each widget:
                'block_body_start' => '<div class="panel-body">',
                'block_body_end' => '</div>',
                // This will enclose (foot)notes:
                'notes_start' => '<div class="small text-muted">',
                'notes_end' => '</div>',
                // Widget 'Search form':
                'search_class' => 'compact_search_form',
                'search_input_before' => '<div class="input-group">',
                'search_input_after' => '',
                'search_submit_before' => '<span class="input-group-btn">',
                'search_submit_after' => '</span></div>',
                // Widget 'Content Hierarchy':
                'item_before_opened' => get_icon('collapse'),
                'item_before_closed' => get_icon('expand'),
                'item_before_post' => get_icon('file_message'),
                'item_title_fields' => 'short_title,title',
                'sorted' => true,
            ]);
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>
				</div>

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

			</div><!-- DO WE NEED THIS DIV? -->

		</aside><!-- .col -->
	<?php } ?>

</div><!-- .row -->


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
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>
