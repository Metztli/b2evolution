<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $xmlsrv_url, $htsrv_url,$skin,$skins_url,$MainList,$paged,$Blog,$rsc_url,$app_version;


$params = array_merge([
    'auto_pilot' => 'seo_title',
], $params);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php locale_lang() ?>" lang="<?php locale_lang() ?>">
<head>
<?php
//require_css( 'style.css', 'relative' );
require_css('css/bootstrap.min.css', 'relative');
require_css('css/bootstrap-theme.min.css', 'relative');
require_css('css/font-awesome.min.css', 'relative');
require_css('css/override.css', 'relative');

add_js_for_toolbar('blog');		// Registers all the javascripts needed by the toolbar menu

$ajax_js = $rsc_url . 'js/ajax.js';
if (file_exists($ajax_js)) {
    require_js('ajax.js', 'blog');
}	// Functions to work with AJAX response data
//init_bubbletip_js( 'blog' ); // Add jQuery bubbletip plugin
// CSS for IE9
add_headline('<!--[if IE 9 ]>');
require_css('ie9.css', 'rsc_url');
add_headline('<![endif]-->'); ?>
	<?php skin_content_meta(); /* Charset for static pages */ ?>
	<?php skin_base_tag(); /* Base URL for this skin. You need this to fix relative links! */ ?>
    <?php	// infinite scroll fix
    if ($disp == 'posts' && $MainList->total_pages > 0 && $paged > $MainList->total_pages) {
        echo '</head>';
        echo '<h2>404 Not Found</h2>';
        exit;
    }
?>
	<?php $Plugins->trigger_event('SkinBeginHtmlHead'); ?>
	<title><?php
    // ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
    request_title($params);
// ------------------------------ END OF REQUEST TITLE -----------------------------
?></title>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>
	<?php skin_description_tag(); ?>
	<?php skin_keywords_tag(); ?>
	<?php robots_tag(); ?>
	<?php
$skinuri = $skins_url . $skin . '/';
$js_blog_id = "";
if (! empty($Blog)) { // Set global js var "blog_id"
    $js_blog_id = "\r\n		var blog_id = '" . $Blog->ID . "';";
}

if (function_exists('get_samedomain_htsrv_url')) {
    add_js_headline("// Paths used by JS functions:
			var htsrv_url = '" . get_samedomain_htsrv_url() . "';"
        . $js_blog_id);
}
?>
	<meta name="generator" content="b2evolution <?php app_version(); ?>" /> <!-- Please leave this for stats -->
	<?php
if ($Blog->get_setting('feed_content') != 'none') { // auto-discovery urls
    ?>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php $Blog->disp('rss2_url', 'raw') ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom" href="<?php $Blog->disp('atom_url', 'raw') ?>" />
		<?php
}
?>
 	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php echo $xmlsrv_url; ?>rsd.php?blog=<?php echo $Blog->ID; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <script type="text/javascript" src="<?php echo $skinuri; ?>js/jquery-latest.js"></script>
    <script type="text/javascript" src="<?php echo $skinuri; ?>js/jquery-migrate.js"></script>
    <?php include_headlines(); /* Add javascript and css files included by plugins and skin */ ?>

		<script type="text/javascript">
        // assign default jquery to jq load newer version
         var $j = jQuery.noConflict(true);  
        </script>
    <!-- <script type="text/javascript">	 // console.log($().jquery);      // console.log($j().jquery); 	 </script> -->

	<?php
    $Blog->disp('blog_css', 'raw');
$Blog->disp('user_css', 'raw');
if (method_exists($Blog, 'disp_setting')) {
    $Blog->disp_setting('head_includes', 'raw');
}
?>
     <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php
            require_js('colorbox/jquery.colorbox-min.js');
require_css('colorbox/colorbox.css');
?>

</head>

<body>

<?php
// ---------------------------- TOOLBAR INCLUDED HERE ----------------------------
require $skins_path . '_toolbar.inc.php';
// ------------------------------- END OF TOOLBAR --------------------------------

echo "\n";
(function_exists('show_toolbar')) ? $wrapper_check = show_toolbar() : $wrapper_check = is_logged_in();
if ($wrapper_check) {
    echo '<div id="skin_wrapper" class="skin_wrapper_loggedin ' . $disp . '">';
} else {
    echo '<div id="skin_wrapper" class="skin_wrapper_anonymous ' . $disp . '">';
}
echo "\n";
//var_dump( version_compare( $app_version, '5', '<' ) );
?>
<!-- Start of skin_wrapper -->
