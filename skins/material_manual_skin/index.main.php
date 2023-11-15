<?php
/**
 * This is the main/default page template for the "material_manual" skin.
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

if (version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}


global $bootstrap_manual_posts_text;

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'front_text' => '',
    'posts_text' => isset($bootstrap_manual_posts_text) ? $bootstrap_manual_posts_text : '',
]);
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>

<div class="main">

	<div class="container">
		<div class="masterhead">
			<header id="header" class="row<?php echo $Settings->get('site_skins_enabled') ? ' site_skins' : ''; ?>">

				<div class="coll-xs-12 coll-sm-12 col-md-4 col-md-push-8">
					<div class="evo_container evo_container__page_top">
					<?php
                        // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
                        // Display container and contents:
                        skin_container(NT_('Page Top'), [
                            // The following params will be used as defaults for widgets included in this container:
                            'block_start' => '<div class="widget $wi_class$">',
                            'block_end' => '</div>',
                            'block_display_title' => false,
                            'list_start' => '<ul>',
                            'list_end' => '</ul>',
                            'item_start' => '<li>',
                            'item_end' => '</li>',
                        ]);
// ----------------------------- END OF "Page Top" CONTAINER -----------------------------
?>
					</div>
				</div><!-- .col -->

				<div class="coll-xs-12 col-sm-12 col-md-8 col-md-pull-4">
					<div class="evo_container evo_container__header">
					<?php
                        // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
                        // Display container and contents:
    skin_container(NT_('Header'), [
        // The following params will be used as defaults for widgets included in this container:
        'block_start' => '<div class="widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h1>',
        'block_title_end' => '</h1>',
    ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>
					</div>
				</div><!-- .col -->

			</header><!-- .row -->
		</div><!-- .masterhead -->

		<nav class="row">
			<div class="col-md-12">
				<ul class="nav color-hover circle-svg-a evo_container evo_container__menu">
				<?php
                    // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
                    // Display container and contents:
                    // Note: this container is designed to be a single <ul> list
skin_container(NT_('Menu'), [
    // The following params will be used as defaults for widgets included in this container:
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
				</ul>
			</div><!-- .col -->
		</nav><!-- .row -->

	<div class="row">

		<div class="content <?php echo $Skin->is_left_navigation_visible() ? 'col-xs-12 col-md-9 pull-right' : 'col-md-12' ?>">

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
        'category_text' => '',
        'categories_text' => '',
        'catdir_text' => '',
        'contacts_text' => '',
        'messages_text' => '',
        'front_text' => '',
        'posts_text' => '',
        'register_text' => '',
        'login_text' => '',
        'lostpassword_text' => '',
        'account_activation' => '',
        'msgform_text' => '',
        'user_text' => '',
        'users_text' => '',
        'display_edit_links' => false,
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>


	<?php
        // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        // Comment template
        'comment_start' => '<div class="evo_comment panel panel-default">',
        'comment_end' => '</div>',
        'comment_post_before' => '<span class="panel-title in-response">',
        'comment_post_after' => '</span>',
        'comment_title_before' => '<div class="panel-heading">',
        'comment_title_after' => '<div class="clearfix"></div></div><div class="panel-body">',
        'comment_avatar_before' => '<div class="evo_comment_avatar">',
        'comment_avatar_after' => '</div>',
        'comment_rating_before' => '<div class="evo_comment_rating">',
        'comment_rating_after' => '</div>',
        'comment_text_before' => '<div class="evo_comment_text">',
        'comment_text_after' => '</div>',
        'comment_info_before' => '<div class="evo_comment_footer clear text-muted"><small>',
        'comment_info_after' => '</small></div></div>',
        'comment_attach_info' => get_icon('help', 'imgtag', [
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-html' => 'true',
            'title' => htmlspecialchars(get_upload_restriction([
                'block_after' => '',
                'block_separator' => '<br /><br />',
            ])),
        ]),
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>


			</main>

		</div><!-- .col -->

		<?php
    if ($Skin->is_left_navigation_visible()) { // Display a left column with navigation only for several pages
        ?>
			<!-- =================================== START OF SIDEBAR =================================== -->
			<aside class="col-xs-12 col-md-3 pull-left">

				<div id="evo_container__sidebar">

					<div class="content panel-group evo_container evo_container__sidebar">
					<?php
                        // <div data-spy="affix" data-offset-top="165" class="affix_block">
                        // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
                        // Display container and contents:
                        // Note: this container is designed to be a single <ul> list
                        skin_container(NT_('Sidebar'), [
                            // The following (optional) params will be used as defaults for widgets included in this container:
                            // This will enclose each widget in a block:
                            'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                            'block_end' => '</div>',
                            // This will enclose the title of each widget:
                            'block_title_start' => '<div class="panel-heading circle-svg-a">'
                            . '<a onClick="return false;" class="panel-toggle" data-toggle="collapse" data-target=".pcollapse-0" href="#">'
                                        . '<span class="panel-icon"><i class="fa fa-angle-down"></i></span></a>'
                                        . '<span class="panel-title">',
                            'block_title_end' => '</span></div>',
                            // This will enclose the body of each widget:
                            'block_body_start' => '<div class="panel-collapse"><div class="panel-body">',
                            'block_body_end' => '</div></div>',
                            // If a widget displays a list, this will enclose that list:
                            'list_start' => '<ul>',
                            'list_end' => '</ul>',
                            // This will enclose each item in a list:
                            'item_start' => '<li>',
                            'item_end' => '</li>',
                            // This will enclose sub-lists in a list:
                            'group_start' => '<ul>',
                            'group_end' => '</ul>',
                            // This will enclose (foot)notes:
                            'notes_start' => '<div class="small">',
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
                            'expand_all' => false,
                            'sorted' => true,
                        ]);
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>
					</div>

					<div class="content panel-group evo_container evo_container__sidebar2">
					<?php
                        // <div data-spy="affix" data-offset-top="165" class="affix_block">
                        // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
                        // Display container and contents:
                        // Note: this container is designed to be a single <ul> list
            skin_container(NT_('Sidebar 2'), [
                // The following (optional) params will be used as defaults for widgets included in this container:
                // This will enclose each widget in a block:
                'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                'block_end' => '</div>',
                // This will enclose the title of each widget:
                'block_title_start' => '<div class="panel-heading circle-svg-a">'
                            . '<a onClick="return false;" class="panel-toggle" data-toggle="collapse" data-target=".pcollapse-0" href="#">'
                            . '<span class="panel-icon"><i class="fa fa-angle-down"></i></span></a>'
                            . '<span class="panel-title">',
                'block_title_end' => '</span></div>',

                // This will enclose the body of each widget:
                'block_body_start' => '<div class="panel-collapse"><div class="panel-body">',
                'block_body_end' => '</div></div>',
                // If a widget displays a list, this will enclose that list:
                'list_start' => '<ul>',
                'list_end' => '</ul>',
                // This will enclose each item in a list:
                'item_start' => '<li>',
                'item_end' => '</li>',
                // This will enclose sub-lists in a list:
                'group_start' => '<ul>',
                'group_end' => '</ul>',
                // This will enclose (foot)notes:
                'notes_start' => '<div class="notes">',
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
                'expand_all' => false,
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
		<div class="col-md-12 center">

			<div class="evo_container evo_container__footer">
			<?php
                // Display container and contents:
                skin_container(NT_("Footer"), [
                    // The following params will be used as defaults for widgets included in this container:
                    'block_start' => '<div class="evo_widget $wi_class$">',
                    'block_end' => '</div>',
                ]);
// Note: Double quotes have been used around "Footer" only for test purposes.
?>
			</div>

			<p>
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
</div><!-- .main -->

<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>