<?php
/**
 * This is the main/default page template for the "Ark" skin.
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
 * @subpackage bootstrap
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
<div class="container">

<!-- =================================== START OF MAIN AREA =================================== -->
	<div class="row">
		<div class="<?php echo($Skin->get_setting('layout') == 'single_column' ? 'col-md-12' : 'col-md-8'); ?>"<?php
                echo($Skin->get_setting('layout') == 'left_sidebar' ? ' style="float:right;"' : ''); ?>>
				
		<main><!-- This is were a link like "Jump to main content" would land -->

		<!-- ================================= START OF MAIN AREA ================================== -->

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
    // ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
    request_title([
        'title_before' => '<h2>',
        'title_after' => '</h2>',
        'title_none' => '',
        'title_single_disp' => false,
        'title_page_disp' => false,
    ]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

		<?php
        // Go Grab the featured post:
if (! in_array($disp, ['single', 'page']) && $Item = &get_featured_Item()) {	// We have a featured/intro post to display:
    $intro_item_style = '';
    $LinkOwner = new LinkItem($Item);
    $LinkList = $LinkOwner->get_attachment_LinkList(1, 'cover');
    if (! empty($LinkList) &&
            $Link = &$LinkList->get_next() &&
            $File = &$Link->get_File() &&
            $File->exists() &&
            $File->is_image()) {	// Use cover image of intro-post as background:
        $intro_item_style = 'background-image: url("' . $File->get_url() . '")';
    }
    // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
    skin_include('_item_block.inc.php', [
        'feature_block' => true,
        'content_mode' => 'full', // We want regular "full" content, even in category browsing: i-e no excerpt or thumbnail
        'intro_mode' => 'normal',	// Intro posts will be displayed in normal mode
        'item_class' => ($Item->is_intro() ? 'well evo_intro_post' : 'well evo_featured_post') . (empty($intro_item_style) ? '' : ' evo_hasbgimg'),
        'item_style' => $intro_item_style,
    ]);
    // ----------------------------END ITEM BLOCK  ----------------------------
}
?>

		<?php
    // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'author_link_text' => 'auto',
        // Profile tabs to switch between user edit forms
        'profile_tabs' => [
            'block_start' => '<nav><ul class="nav nav-tabs profile_tabs">',
            'item_start' => '<li>',
            'item_end' => '</li>',
            'item_selected_start' => '<li class="active">',
            'item_selected_end' => '</li>',
            'block_end' => '</ul></nav>',
        ],
        // Pagination
        'pagination' => [
            'block_start' => '<div class="center"><ul class="pagination">',
            'block_end' => '</ul></div>',
            'page_current_template' => '<span>$page_num$</span>',
            'page_item_before' => '<li>',
            'page_item_after' => '</li>',
            'page_item_current_before' => '<li class="active">',
            'page_item_current_after' => '</li>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
        ],
        // Item content:
        'url_link_position' => 'top',
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
        'featured_intro_before' => '<div class="jumbotron">',
        'featured_intro_after' => '</div>',
        // Form "Sending a message"
        'msgform_form_title' => T_('Sending a message'),
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>
		</main>

	</div><!-- .col -->

<!-- =================================== START OF SIDEBAR =================================== -->
	<?php
    if ($Skin->get_setting('layout') != 'single_column') {
        ?>
	<div class="col-md-4 sidebar"<?php echo($Skin->get_setting('layout') == 'left_sidebar' ? ' style="float:left;"' : ''); ?>>
		<!-- =================================== START OF SIDEBAR =================================== -->
		<?php
                // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
                widget_container('sidebar', [
                    // The following (optional) params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => false, // If no widget, don't display container at all
                    'container_start' => '<div class="evo_container $wico_class$">',
                    'container_end' => '</div>',
                    // This will enclose each widget in a block:
                    'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                    'block_end' => '</div>',
                    // This will enclose the title of each widget:
                    'block_title_start' => '<div class="panel-heading"><h4 class="panel-title">',
                    'block_title_end' => '</h4></div>',
                    // This will enclose the body of each widget:
                    'block_body_start' => '<div class="panel-body">',
                    'block_body_end' => '</div>',
                    // If a widget displays a list, this will enclose that list:
                    'list_start' => '<ul>',
                    'list_end' => '</ul>',
                    // This will enclose each item in a list:
                    'item_start' => '<li>',
                    'item_end' => '</li>',
                    // This will enclose sub-lists in a list:
                    'group_start' => '<ul>',
                    'group_end' => '</ul>',
                    // This will enclose (foot)notes:
                    'notes_start' => '<div class="notes">',
                    'notes_end' => '</div>',
                    // Widget 'Search form':
                    'search_class' => 'compact_search_form',
                    'search_input_before' => '',
                    'search_input_after' => '',
                    'search_submit_before' => '',
                    'search_submit_after' => '',
                ]);
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>

		<?php
            // ------------------------- "Sidebar 2" CONTAINER EMBEDDED HERE --------------------------
            widget_container('sidebar_2', [
                // The following (optional) params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                'container_start' => '<div class="evo_container $wico_class$">',
                'container_end' => '</div>',
                // This will enclose each widget in a block:
                'block_start' => '<div class="panel panel-default evo_widget $wi_class$">',
                'block_end' => '</div>',
                // This will enclose the title of each widget:
                'block_title_start' => '<div class="panel-heading"><h4 class="panel-title">',
                'block_title_end' => '</h4></div>',
                // This will enclose the body of each widget:
                'block_body_start' => '<div class="panel-body">',
                'block_body_end' => '</div>',
                // If a widget displays a list, this will enclose that list:
                'list_start' => '<ul>',
                'list_end' => '</ul>',
                // This will enclose each item in a list:
                'item_start' => '<li>',
                'item_end' => '</li>',
                // This will enclose sub-lists in a list:
                'group_start' => '<ul>',
                'group_end' => '</ul>',
                // This will enclose (foot)notes:
                'notes_start' => '<div class="notes">',
                'notes_end' => '</div>',
                // Widget 'Search form':
                'search_class' => 'compact_search_form',
                'search_input_before' => '<div class="input-group">',
                'search_input_after' => '',
                'search_submit_before' => '<span class="input-group-btn">',
                'search_submit_after' => '</span></div>',
            ]);
        // ----------------------------- END OF "Sidebar 2" CONTAINER -----------------------------
        ?>
		</div>
	<?php } ?>
	</div>
</div>

<!-- =================================== START OF FOOTER =================================== -->
<footer class="footer">
	<div class='container'>
		<?php
            widget_container('footer', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                'container_start' => '<div class="evo_container $wico_class$">',
                'container_end' => '</div>',
                'block_start' => '<div class="evo_widget $wi_class$">',
                'block_end' => '</div>',
                'block_title_start' => '<div class="panel-heading"><h4 class="panel-title">',
                'block_title_end' => '</h4></div>',
                'block_body_start' => '<div class="panel-body">',
                'block_body_end' => '</div>',
            ]);
// ----------------------------- END OF "Footer" CONTAINER -----------------------------
?>
		<div class="footer_note__wrapper clear">
			<p class="footer_note">
				<?php
                    // Display footer text (text can be edited in Blog Settings):
            $Blog->footer_text([
                'before' => '',
                'after' => ' &bull; ',
            ]);
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
    if ($Skin->get_setting('b2evo_credits') == true) {
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
    }
?>
			</p>
			<?php
            if ($Skin->get_setting('footer_links') == true) {
                skin_widget([
                    // CODE for the widget:
                    'widget' => 'user_links',
                ]);
            }
?>
		</div>
	</div>
</footer>

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