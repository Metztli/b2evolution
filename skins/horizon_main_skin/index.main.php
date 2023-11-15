<?php
/**
 * This is the main/default page template for the "bootstrap_main" skin.
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
 * @subpackage bootstrap_main
 *
     * @version $Id: index.main.php 8907 2015-05-08 18:16:32Z fplanque $
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


// Check if current page has a big picture as background
$is_pictured_page = in_array($disp, ['front', 'login', 'register', 'lostpassword', 'activateinfo', 'access_denied']);

// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'html_tag' => '<!DOCTYPE html>' . "\r\n"
                 . '<html lang="' . locale_lang(false) . '">',
    'viewport_tag' => '#responsive#',
    'body_class' => ($is_pictured_page ? 'pictured' : ''),
]);
// Include Google Fonts code inside ""+
echo "<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>";
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

<div class="container main_page_wrapper">

<?php
if ($disp != 'front') { // Don't display header on disp=front
    ?>
	<header class="row">
	<?php
        // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
            widget_container('page_top', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                'container_start' => '<div class="coll-xs-12"><div class="evo_container $wico_class$">',
                'container_end' => '</div></div>',
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
            'container_start' => '<div class="coll-xs-12"><div class="evo_container $wico_class$">',
            'container_end' => '</div></div>',
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            'block_title_start' => '<h1>',
            'block_title_end' => '</h1>',
        ]);
    // ----------------------------- END OF "Header" CONTAINER -----------------------------
    ?>
	</header>
<?php
}
?>
<!-- =================================== START OF MAIN AREA =================================== -->
	<div class="row">
		<main>
		<div class="col-md-12<?php echo $disp == 'front' ? ' front_main_area' : ''; ?>">

	<?php
    if (! in_array($disp, ['login', 'lostpassword', 'register', 'activateinfo'])) { // Don't display the messages here because they are displayed inside wrapper to have the same width as form
        // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
        messages([
            'block_start' => '<div class="action_messages">',
            'block_end' => '</div>',
        ]);
        // --------------------------------- END OF MESSAGES ---------------------------------
    }

    if ($disp == 'front') { // Start of wrapper for front page area, in order to have the $Messages outside this block
        echo '<div class="front_main_content">';
    }
?>

	<?php
    // ------------------- PREV/NEXT POST LINKS (SINGLE POST MODE) -------------------
    item_prevnext_links([
        'block_start' => '<ul class="pager">',
        'prev_start' => '<li class="previous">',
        'prev_end' => '</li>',
        'next_start' => '<li class="next">',
        'next_end' => '</li>',
        'block_end' => '</ul>',
    ]);
// ------------------------- END OF PREV/NEXT POST LINKS -------------------------
?>

	<?php
        // ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
    request_title([
        'title_before' => '<h2>',
        'title_after' => '</h2>',
        'title_none' => '',
        'glue' => ' - ',
        'title_single_disp' => true,
        'format' => 'htmlbody',
        'register_text' => '',
        'login_text' => '',
        'lostpassword_text' => '',
        'account_activation' => '',
        'msgform_text' => '',
        'user_text' => '',
        'users_text' => '',
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

		<?php
        // Go Grab the featured post:
    if (! in_array($disp, ['single', 'page']) && $Item = &get_featured_Item()) { // We have a featured/intro post to display:
        // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
        skin_include('_item_block.inc.php', [
            'feature_block' => true,
            'content_mode' => 'full', // We want regular "full" content, even in category browsing: i-e no excerpt or thumbnail
            'intro_mode' => 'normal',	// Intro posts will be displayed in normal mode
            'item_class' => ($Item->is_intro() ? 'well evo_intro_post' : 'well evo_featured_post'),
        ]);
        // ----------------------------END ITEM BLOCK  ----------------------------
    }
?>

	<?php
    if ($disp != 'front' && $disp != 'download' && $disp != 'search') {
        // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
        mainlist_page_links([
            'block_start' => '<div class="center"><ul class="pagination">',
            'block_end' => '</ul></div>',
            'page_current_template' => '<span><b>$page_num$</b></span>',
            'page_item_before' => '<li>',
            'page_item_after' => '</li>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
        ]);
        // ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
        ?>


	<?php
        // --------------------------------- START OF POSTS -------------------------------------
        // Display message if no post:
            display_if_empty();
        while ($Item = &mainlist_get_item()) { // For each blog post, do everything below up to the closing curly brace "}"
            // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
            skin_include('_item_block.inc.php', [
                'content_mode' => 'auto',		// 'auto' will auto select depending on $disp-detail
            ]);
            // ----------------------------END ITEM BLOCK  ----------------------------
        } // ---------------------------------- END OF POSTS ------------------------------------
        ?>

	<?php
        // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
            mainlist_page_links([
                'block_start' => '<div class="center"><ul class="pagination">',
                'block_end' => '</ul></div>',
                'page_current_template' => '<span><b>$page_num$</b></span>',
                'page_item_before' => '<li>',
                'page_item_after' => '</li>',
                'prev_text' => '<i class="fa fa-angle-double-left"></i>',
                'next_text' => '<i class="fa fa-angle-double-right"></i>',
            ]);
        // ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
    }
?>


	<?php
    // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'disp_posts' => '',		// We already handled this case above
        'disp_single' => '',		// We already handled this case above
        'disp_page' => '',		// We already handled this case above
        'author_link_text' => 'preferredname',
        // Profile tabs to switch between user edit forms
        'profile_tabs' => [
            'block_start' => '<ul class="nav nav-tabs profile_tabs">',
            'item_start' => '<li>',
            'item_end' => '</li>',
            'item_selected_start' => '<li class="active">',
            'item_selected_end' => '</li>',
            'block_end' => '</ul>',
        ],
        // Pagination
        'pagination' => [
            'block_start' => '<div class="center"><ul class="pagination">',
            'block_end' => '</ul></div>',
            'page_current_template' => '<span><b>$page_num$</b></span>',
            'page_item_before' => '<li>',
            'page_item_after' => '</li>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
        ],
        // Form params for the forms below: login, register, lostpassword, activateinfo and msgform
        'skin_form_before' => '<div class="panel panel-default skin-form">'
                                        . '<div class="panel-heading">'
                                                . '<h3 class="panel-title">$form_title$</h3>'
                                            . '</div>'
                                        . '<div class="panel-body">',
        'skin_form_after' => '</div></div>',
        // Login
        'display_form_messages' => true,
        'form_title_login' => T_('Log in to your account') . '$form_links$',
        'form_title_lostpass' => get_request_title() . '$form_links$',
        'lostpass_page_class' => 'evo_panel__lostpass',
        'login_form_inskin' => false,
        'login_page_class' => 'evo_panel__login',
        'login_page_before' => '<div class="$form_class$">',
        'login_page_after' => '</div>',
        'display_reg_link' => true,
        'abort_link_position' => 'form_title',
        'abort_link_text' => '<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
        // Register
        'register_page_before' => '<div class="evo_panel__register">',
        'register_page_after' => '</div>',
        'register_form_title' => T_('Register'),
        'register_links_attrs' => '',
        'register_use_placeholders' => true,
        'register_field_width' => 252,
        'register_disabled_page_before' => '<div class="evo_panel__register register-disabled">',
        'register_disabled_page_after' => '</div>',
        // Activate form
        'activate_form_title' => T_('Account activation'),
        'activate_page_before' => '<div class="evo_panel__activation">',
        'activate_page_after' => '</div>',
        // Search
        'search_input_before' => '<div class="input-group">',
        'search_input_after' => '',
        'search_submit_before' => '<span class="input-group-btn">',
        'search_submit_after' => '</span></div>',
        // Front page
        'front_block_first_title_start' => '<h1>',
        'front_block_first_title_end' => '</h1>',
        'front_block_title_start' => '<h2>',
        'front_block_title_end' => '</h2>',
        'featured_intro_before' => '<div class="jumbotron">',
        'featured_intro_after' => '</div>',
        // Form "Sending a message"
        'msgform_form_title' => T_('Sending a message'),
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------

if ($disp == 'front') { // End of wrapper for front page area, in order to have the $Messages outside this block
    echo '</div>'; // END OF <div class="front_main_content">
}
?>

		</div>
	</div>
	</main>
</div>

<!-- End of skin_wrapper -->
</div>

<!-- =================================== START OF FOOTER =================================== -->
<div class="evo_container evo_container__footer">
	<div class="container">
		<div class="row">
<?php
if ($disp == 'front') {
    // ------------------------- "Front Page Secondary Area" CONTAINER EMBEDDED HERE --------------------------
    // Display container and contents:
    widget_container('front_page_secondary_area', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<div class="col-md-12 evo_container $wico_class$">',
        'container_end' => '</div>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h2 class="page-header">',
        'block_title_end' => '</h2>',
    ]);
    // ----------------------------- END OF "Front Page Secondary Area" CONTAINER -----------------------------
}
?>
		</div>
	</div>
</div>
		<div class="col-md-12 center footer-wrapper">
			<div class="container">
		<?php
            // ------------------------- "Footer" CONTAINER EMBEDDED HERE --------------------------
            widget_container('footer', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                'container_start' => '<div class="evo_container $wico_class$">',
                'container_end' => '</div>',
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