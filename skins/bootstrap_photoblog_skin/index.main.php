<?php
/**
 * This is the main/default page template for the "bootstrap_blog" skin.
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
 * @subpackage bootstrap_blog
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (evo_version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
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


<header class="row">

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

	<div class="<?php echo $Skin->get_column_class(); ?>">

		<main><!-- This is were a link like "Jump to main content" would land -->

		<!-- ================================= START OF MAIN AREA ================================== -->

		<?php
if (! in_array($disp, ['login', 'lostpassword', 'register', 'activateinfo', 'access_requires_login', 'content_requires_login'])) { // Don't display the messages here because they are displayed inside wrapper to have the same width as form
    // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
    messages([
        'block_start' => '<div class="action_messages">',
        'block_end' => '</div>',
    ]);
    // --------------------------------- END OF MESSAGES ---------------------------------
}
?>

		<?php
    // ------------------- PREV/NEXT POST LINKS (SINGLE POST MODE) -------------------
    item_prevnext_links([
        'block_start' => '<nav><ul class="pager">',
        'prev_start' => '<li class="previous">',
        'prev_end' => '</li>',
        'next_start' => '<li class="next">',
        'next_end' => '</li>',
        'block_end' => '</ul></nav>',
    ]);
// ------------------------- END OF PREV/NEXT POST LINKS -------------------------
?>

		<?php
            // ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
    request_title([
        'title_before' => '<h2>',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
        'title_single_disp' => false,
        'title_page_disp' => false,
        'title_widget_page_disp' => false,
        'format' => 'htmlbody',
        'register_text' => '',
        'login_text' => '',
        'lostpassword_text' => '',
        'account_activation' => '',
        'msgform_text' => '',
        'user_text' => '',
        'users_text' => '',
        'comments_text' => '',
        'search_text' => '',
        'display_edit_links' => ($disp == 'edit'),
        'edit_links_template' => [
            'before' => '<span class="pull-right">',
            'after' => '</span>',
            'advanced_link_class' => 'btn btn-info btn-sm',
            'close_link_class' => 'btn btn-default btn-sm',
        ],
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

		<?php
        // Go Grab the featured post:
if (! in_array($disp, ['single', 'page']) && $Item = &get_featured_Item()) { // We have a featured/intro post to display:
    // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
    skin_include('_item_block.inc.php', [
        'feature_block' => true,
        'content_mode' => 'full', // We want regular "full" content, even in category browsing: i-e no excerpt or thumbnail
        'intro_mode' => 'normal',	// Intro posts will be displayed in normal mode
        'item_class' => ($Item->is_intro() ? 'well evo_intro_post' : 'well evo_featured_post'),
        'Item' => $Item,
    ]);
    // ----------------------------END ITEM BLOCK  ----------------------------
}
?>

		<?php
    // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'author_link_text' => 'auto',
        // Profile tabs to switch between user edit forms
        'profile_tabs' => [
            'block_start' => '<nav><ul class="nav nav-tabs profile_tabs">',
            'item_start' => '<li>',
            'item_end' => '</li>',
            'item_selected_start' => '<li class="active">',
            'item_selected_end' => '</li>',
            'block_end' => '</ul></nav>',
        ],
        // Pagination
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
        // Item content:
        // Form params for the forms below: login, register, lostpassword, activateinfo and msgform
        'skin_form_before' => '<div class="panel panel-default skin-form">'
                                                                    . '<div class="panel-heading">'
                                                                        . '<h3 class="panel-title">$form_title$</h3>'
                                                                    . '</div>'
                                                                    . '<div class="panel-body">',
        'skin_form_after' => '</div></div>',
        // Login
        'display_form_messages' => true,
        'form_title_login' => T_('Log in to your account') . '$form_links$',
        'form_title_lostpass' => get_request_title() . '$form_links$',
        'lostpass_page_class' => 'evo_panel__lostpass',
        'login_form_inskin' => false,
        'login_page_class' => 'evo_panel__login',
        'login_page_before' => '<div class="$form_class$">',
        'login_page_after' => '</div>',
        'display_reg_link' => true,
        'abort_link_position' => 'form_title',
        'abort_link_text' => '<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
        // Activate form
        'activate_form_title' => T_('Account activation'),
        'activate_page_before' => '<div class="evo_panel__activation">',
        'activate_page_after' => '</div>',
        // Search
        'search_input_before' => '<div class="input-group">',
        'search_input_after' => '',
        'search_submit_before' => '<span class="input-group-btn">',
        'search_submit_after' => '</span></div>',
        // Front page
        'featured_intro_before' => '',
        'featured_intro_after' => '',
        'intro_class' => 'jumbotron',
        'featured_class' => 'featurepost',
        // Form "Sending a message"
        'msgform_form_title' => T_('Contact'),
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>
		</main>

	</div><!-- .col -->

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
