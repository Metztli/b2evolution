<?php
/**
 * This is the main/default page template for the "postlog" skin.
 *
 * This skin only uses one single template which includes most of its features.
 * It will also rely on default includes for specific dispays (like the comment form).
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 * @subpackage postlog
 *
 * @version $Id: index.main.php,v 1.16 2008/04/15 21:53:31 fplanque Exp $
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (version_compare($app_version, '2.4.1') < 0) { // Older 2.x skins work on newer 2.x b2evo versions, but newer 2.x skins may not work on older 2.x b2evo versions.
    die('This skin is designed for b2evolution 2.4.1 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>


<div id="wrapper">

<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_body_header.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>
  <div id="innerwrapper">
  


<!-- =================================== START OF MAIN AREA =================================== -->

<div id="main">

<!-- =================================== START OF SIDEBAR =================================== -->
<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_sidebar.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>


<div class="index_single">
<div class="index_content_wrap_single">
<div class="index_content_single">
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
        'disp_posts' => '',		// We already handled this case above
        'disp_single' => '',		// We already handled this case above
        'disp_page' => '',		// We already handled this case above
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>
    </div>
</div>
</div><!-- end of .content -->


</div><!-- end of #main -->



<!-- =================================== START OF FOOTER =================================== -->



</div><!-- end of .innerwrapper -->
</div><!-- end of #wrapper-->
<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_body_footer.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>

<?php
// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>