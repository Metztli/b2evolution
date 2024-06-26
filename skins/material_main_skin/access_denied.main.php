<?php
/**
 * This file is the template that displays "access denied" for non-members.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * @package evoskins
 * @subpackage bootstrap_main
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


// Check if current page has a big picture as background
$is_pictured_page = true;

// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'html_tag' => '<!DOCTYPE html>' . "\r\n"
                 . '<html lang="' . locale_lang(false) . '">',
    'viewport_tag' => '#responsive#',
    'body_class' => ($is_pictured_page ? 'pictured' : ''),
]);
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------

if ($is_pictured_page) { // Display a picture from skin setting as background image
    global $media_path, $media_url;
    $bg_image = $Skin->get_setting('front_bg_image');
    echo '<div id="bg_picture">';
    if (! empty($bg_image) && file_exists($media_path . $bg_image)) { // If it exists in media folder
        echo '<img src="' . $media_url . $bg_image . '" />';
    }
    echo '</div>';
}
?>

<div class="container body">

	<div class="row">
		<div class="col-md-12">

	<?php
        // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
        widget_container('page_top', [
            // The following params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            'container_start' => '<div class="evo_container $wico_class$">',
            'container_end' => '</div>',
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            'block_display_title' => false,
            'list_start' => '<ul>',
            'list_end' => '</ul>',
            'item_start' => '<li>',
            'item_end' => '</li>',
        ]);
// ----------------------------- END OF "Page Top" CONTAINER -----------------------------
?>

	<?php
        // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
    widget_container('header', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<div class="evo_container $wico_class$">',
        'container_end' => '</div>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h1>',
        'block_title_end' => '</h1>',
    ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>

		</div>
	</div>

<!-- =================================== START OF MAIN AREA =================================== -->
	<div class="row">
		<div class="col-md-12">

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
        'title_before' => '<h2>',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

	<?php
        // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$');
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

		</div>
	</div>
</div>

<!-- End of skin_wrapper -->
</div>

<!-- =================================== START OF FOOTER =================================== -->
<div class="evo_container evo_container__footer">
	<div class="container">
		<div class="row">
			<div class="col-md-12 center">
	<?php
        // ------------------------- "Footer" CONTAINER EMBEDDED HERE --------------------------
    widget_container('footer', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<div class="evo_container $wico_class$">',
        'container_end' => '</div>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
    ]);
// ----------------------------- END OF "Footer" CONTAINER -----------------------------
?>
	<p>
		<?php
            // Display footer text (text can be edited in Blog Settings):
        $Blog->footer_text([
            'before' => '',
            'after' => ' &bull; ',
        ]);

// TODO: dh> provide a default class for pTyp, too. Should be a name and not the ityp_ID though..?!
?>

		<?php
            // Display a link to contact the owner of this blog (if owner accepts messages):
    $Blog->contact_link([
        'before' => '',
        'after' => ' &bull; ',
        'text' => T_('Contact'),
        'title' => T_('Send a message to the owner of this blog...'),
    ]);
// Display a link to help page:
$Blog->help_link([
    'before' => ' ',
    'after' => ' ',
    'text' => T_('Help'),
]);
?>

		<?php
    // Display additional credits:
    // If you can add your own credits without removing the defaults, you'll be very cool :))
    // Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
    credits([
        'list_start' => '&bull;',
        'list_end' => ' ',
        'separator' => '&bull;',
        'item_start' => ' ',
        'item_end' => ' ',
    ]);
?>
	</p>

	<?php
// Please help us promote b2evolution and leave this logo on your blog:
powered_by([
    'block_start' => '<div class="powered_by">',
    'block_end' => '</div>',
    // Check /rsc/img/ for other possible images -- Don't forget to change or remove width & height too
    'img_url' => '$rsc$img/powered-by-b2evolution-120t.gif',
    'img_width' => 120,
    'img_height' => 32,
]);
?>
			</div>
		</div>
	</div>
</div>

<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>