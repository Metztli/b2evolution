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

$container = '';
if ($Skin->get_setting('posts_full_width') == 1) {
    $container = 'class="full_widgth"';
}

?>
<main id="content" <?php echo $container; ?>><!-- This is were a link like "Jump to main content" would land -->
	<div class="container">
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
            // ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
    request_title([
        'title_before' => '<h2 class="title__content">',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
        'title_single_disp' => false,
        'format' => 'htmlbody',
        'user_text' => '',
        'display_edit_links' => false,
    ]);
// ------------------------------ END OF REQUEST TITLE -----------------------------
?>
		<?php
    $column = $Skin->Change_class('posts_show');
// -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
skin_include('$disp$', [
    // 'mediaidx_thumb_size'  => $Skin->get_setting( 'post_thumb_size' ),
    'mediaidx_thumb_size' => 'original',
    'author_link_text' => 'preferredname',
    'item_class' => 'evo_posts evo_content_block ' . $column,
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

		<?php
if ($disp != 'catdir') {	// Don't display the pages on disp=catdir because we don't have a limit by page there
    // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
    mainlist_page_links([
        'block_start' => '<div class="main_pagination"><ul class="pagination">',
        'block_end' => '</ul></div>',
        'page_item_before' => '<li>',
        'page_item_after' => '</li>',
        'page_item_current_before' => '<li class="active">',
        'page_item_current_after' => '</li>',
        'page_current_template' => '<span>$page_num$</span>',
        'prev_text' => '<i class="fa fa-angle-double-left"></i>' . T_(' Prev'),
        'next_text' => T_('Next ') . '<i class="fa fa-angle-double-right"></i>',
    ]);
    // ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
}
?>

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
