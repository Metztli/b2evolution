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

// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', []);
// -------------------------------- END OF HEADER --------------------------------

// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------

?>

<header id="header">

	<div class="search_head">
		<div class="container">

			<!-- <div class="search_head_main">
				<div class="brand"> -->
					<?php
                    // ------------------------ START OF Brand FORM WIDGET ------------------------
                    // skin_widget( array(
                    // 	// CODE for the widget:
                    // 	'widget'   => 'coll_title',
                    // ) );
                    // ------------------------- END OF Brand FORM WIDGET -------------------------
?>
				<!-- </div>
				<div class="clearfix"></div>
			</div> -->

			<!-- Search Box -->
			<div class="search_box">
				<h3 class="search_box_title">
					<?php echo $Skin->get_setting('header_search_heading'); ?> <span><?php echo $Skin->get_setting('header_search_subhead') ?></span>
				</h3>
				<?php
                // ------------------------ START OF SEARCH FORM WIDGET ------------------------
                skin_widget([
                    // CODE for the widget:
                    'widget' => 'coll_search_form',
                    // Optional display params
                    'block_start' => '<div class="main_search_box $wi_class$">',
                    'block_end' => '</div>',
                    'block_display_title' => false,
                    'search_class' => 'extended_search_form clearfix',
                    'search_input_before' => '',
                    'search_input_after' => '',
                    'search_submit_before' => '',
                    'search_submit_after' => '',
                    'use_search_disp' => 1,
                    'button' => $Skin->get_setting('header_btn_search'),
                ]);
// ------------------------- END OF SEARCH FORM WIDGET -------------------------
?>
			</div>
		</div>
	</div>

    <nav class="main_navigation">
        <div class="container">
            <div class="row">
                <div class="mobile_nav">
                    <button id="main_nav" class="menu_hamburger">
                        <span>Menu</span>
                        <div class="hamburger">
                            <div class="menui top-menu"></div>
                            <div class="menui mid-menu"></div>
                            <div class="menui bottom-menu"></div>
                        </div>
                    </button>
                </div>
                    <?php
                    // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
    $hover_style = '';
$hover_nav = $Skin->get_setting('nav_hover_style');
switch ($hover_nav) {
    case $hover_nav:
        $hover_style = 'hover-' . $hover_nav;
        break;
}

// Display container and contents:
// Note: this container is designed to be a single <ul> list
widget_container('menu', [
    // The following params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => false, // If no widget, don't display container at all
    'container_start' => '<ul class="nav nav-tabs evo_container $wico_class$">',
    'container_end' => '</ul>',
    'block_start' => '',
    'block_end' => '',
    'block_display_title' => false,
    'list_start' => '',
    'list_end' => '',
    'item_start' => '<li class="evo_widget $wi_class$ ' . $hover_style . '">',
    'item_end' => '</li>',
    'item_selected_start' => '<li class="active evo_widget $wi_class$ ' . $hover_style . '">',
    'item_selected_end' => '</li>',
    'item_title_before' => '',
    'item_title_after' => '',
]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>
            </div>
        </div><!-- /.container -->

    </nav><!-- /#main_navigation -->
</header><!-- /#Header -->


<main id="content"><!-- This is were a link like "Jump to main content" would land -->
	<div class="container">
		<div class="main_content">
			<!-- ================================= START OF MAIN AREA ================================== -->
			<?php
                // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
                messages([
                    'block_start' => '<div class="action_messages">',
                    'block_end' => '</div>',
                ]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>

			<?php
                // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'mediaidx_thumb_size' => $Skin->get_setting('mediaidx_thumb_size'),
        'author_link_text' => 'preferredname',
        'item_class' => 'evo_post evo_content_block',
        'item_type_class' => 'evo_post__ptyp_',
        'item_status_class' => 'evo_post__',
        // Login
        'login_page_before' => '<div class="login_block"><div class="evo_details">',
        'login_page_after' => '</div></div>',
        // Register
        'register_page_before' => '<div class="login_block"><div class="evo_details">',
        'register_page_after' => '</div></div>',
        'display_abort_link' => ($Blog->get_setting('allow_access') == 'public'), // Display link to abort login only when it is really possible
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

		</div><!-- .main_content -->
	</div><!-- .container -->
</main>


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
skin_include('_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------

// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>
