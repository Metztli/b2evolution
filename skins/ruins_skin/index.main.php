<?php
/**
 * This is the main/default template for the b2evo 2.4.2 skin: Ruins
 * design by Lori Piper, http://www.piperenterprises.com/b2evo.html
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 * @subpackage zeke
 *
 * @version $Id: index.main.php,v 1.2.2.1 2008/04/26 22:28:53 fplanque Exp $
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


<?php
// ------------------------- BODY HEADER INCLUDED HERE --------------------------
skin_include('_body_header.inc.php');
// Note: You can customize the default BODY header by copying the generic
// /skins/_body_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>

<div id="page">

	<div id="contentleft">

	<?php
    // ------------------------- SIDEBAR INCLUDED HERE --------------------------
    skin_include('_sidebar_left.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ----------------------------- END OF SIDEBAR -----------------------------
?>

	<div id="content">

	<?php
        // ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
    request_title([
        'title_before' => '<h2 class="sectionhead">',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
        'title_single_disp' => true,
        'format' => 'htmlbody',
    ]);
// ------------------------------ END OF REQUEST TITLE -----------------------------
?>


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
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>


	</div>

</div>

<?php
// ------------------------- SIDEBAR INCLUDED HERE --------------------------
skin_include('_sidebar_right.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ----------------------------- END OF SIDEBAR -----------------------------
?>

</div>

<?php
// ------------------------- BODY FOOTER INCLUDED HERE --------------------------
skin_include('_body_footer.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>


<?php
// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>
