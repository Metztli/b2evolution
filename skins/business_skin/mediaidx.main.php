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

<main id="main-mediaidx"><!-- This is were a link like "Jump to main content" would land -->
   <div class="container">
      <div class="row">

         <!-- ================================= START OF MAIN AREA ================================== -->
         <div class="<?php echo $Skin->layout_mediaidx_setting(); ?>">

            <?php
            if (! in_array($disp, ['login', 'lostpassword', 'register', 'activateinfo', 'access_requires_login'])) { // Don't display the messages here because they are displayed inside wrapper to have the same width as form
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
       'title_before' => '<h2 class="title_mediaidx">',
       'title_after' => '</h2>',
       'title_none' => '',
       'glue' => ' - ',
       'title_single_disp' => false,
       'title_page_disp' => false,
       'format' => 'htmlbody',
       'register_text' => '',
       'login_text' => '',
       'lostpassword_text' => '',
       'account_activation' => '',
       'msgform_text' => '',
       'user_text' => '',
       'users_text' => '',
       'display_edit_links' => false,
       'arcdir_text' => T_('Index'),
       'catdir_text' => '',
       'category_text' => T_('Gallery') . ': ',
       'categories_text' => T_('Galleries') . ': ',
   ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>


      		<?php
                  // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
      skin_include('$disp$', [
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

         </div><!-- .col -->

         <?php
            if ($Skin->get_setting('mediaidx_layout') !== 'no_sidebar') { // Display Sidebar for Mediaidx
                // ------------------------- SIDEBAR INCLUDED HERE --------------------------
                skin_include('_sidebar.inc.php');
                // Note: You can customize the sidebar by copying the
                // _sidebar.inc.php file into the current skin folder.
                // ----------------------------- END OF SIDEBAR -----------------------------
            }
?>

   	</div><!-- .row -->


   	<?php
    if ($disp != 'catdir') {	// Don't display the pages on disp=catdir because we don't have a limit by page there
        // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
        mainlist_page_links([
            'block_start' => '<div class="nav_pages">',
            'block_end' => '</div>',
            'prev_text' => '&lt;&lt;',
            'next_text' => '&gt;&gt;',
        ]);
        // ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
    }
?>

   </div><!-- end .container -->
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
