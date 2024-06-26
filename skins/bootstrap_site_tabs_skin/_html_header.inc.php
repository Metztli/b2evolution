<?php
/**
 * This is the HTML header include template.
 *
 * This will be included ONLY for site skin pages (like a global 404).
 *
 * @package skins
 * @subpackage bootstrap_site_tabs_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $page_title;

?>
<html lang="<?php locale_lang() ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php
        // ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
        echo $page_title;
// ------------------------------ END OF REQUEST TITLE -----------------------------
?></title>
	<meta name="generator" content="b2evolution <?php app_version(); ?>" /> <!-- Please leave this for stats -->
	<?php include_headlines() /* Add javascript and css files included by plugins and skin */ ?>
</head>

<body<?php skin_body_attrs(); ?>>

<?php
/* In the future we probably want to include the toolbar here...

// ---------------------------- TOOLBAR INCLUDED HERE ----------------------------
require skin_fallback_path( '_toolbar.inc.php' );
// ------------------------------- END OF TOOLBAR --------------------------------

echo "\n";
if( show_toolbar() )
{
    echo '<div id="skin_wrapper" class="skin_wrapper_loggedin">';
}
else
{
    echo '<div id="skin_wrapper" class="skin_wrapper_anonymous">';
}
echo "\n";

*/
?>
<!-- Start of skin_wrapper -->
