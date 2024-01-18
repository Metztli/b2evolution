<?php
/**
 * This file is the template that displays "access denied" for non-members.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * @package evoskins
 * @subpackage bootstrap_blog
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $app_version, $disp, $Blog;

if (version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
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
skin_include('_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>

<div id="main-content">
	<div class="container">
		<div class="row">
			<div class="<?php echo $Skin->get_column_class(); ?>">
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
                // ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
    request_title([
        'title_before' => '<h2 class="page_title">',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

			<?php
                // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('_access_denied.disp.php');
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

			</div><!-- .col -->
			<?php
                // ------------------------- SIDEBAR INCLUDED HERE --------------------------
    skin_include('_sidebar.inc.php');
// Note: You can customize the sidebar by copying the
// _sidebar.inc.php file into the current skin folder.
// ----------------------------- END OF SIDEBAR -----------------------------
?>
		</div><!-- .row -->
	</div><!-- .container -->
</div><!-- .main_content -->


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
// skin_include( '_body_footer.inc.php' );
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>
