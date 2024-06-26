<?php
/**
 * This page displays an error message when we cannot resolve the extra path.
 *
 * This happens when you request an invalid tracking code on track.php for example
 *
 * @package skins
 * @subpackage default_site_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

header_http_response('404 Not Found');
header('Content-Type: text/html; charset=utf-8'); // no translation

$page_title = '404 Not Found';
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
siteskin_include('_html_header.inc.php');
// -------------------------------- END OF HEADER --------------------------------

?>
<h1>404 Not Found</h1>
<p>The page you requested doesn't seem to exist on <a href="<?php echo $baseurl ?>">this system</a>.</p>
<?php

// -------------------------- HTML FOOTER INCLUDED HERE --------------------------
siteskin_include('_html_footer.inc.php');
// -------------------------------- END OF FOOTER --------------------------------
?>