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
skin_include('_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------

?>

<main id="content"><!-- This is were a link like "Jump to main content" would land -->
	<div class="container">
		<div class="main_content">
			<!-- =================================== START OF POST TITLE BAR =================================== -->
			<?php
            // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
            messages([
                'block_start' => '<div class="row"><div class="col-xs-12 action_messages">',
                'block_end' => '</div></div>',
            ]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>

			<div class="evo_post_content">
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
			</div><!-- .evo_post_content -->

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
