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

global $Skin, $Item;
// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);
// TODO: move to Skin::display_init
require_js('functions.js', 'blog');	// for opening popup window (comments)
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'arcdir_text' => T_('Index'),
    'catdir_text' => T_('Galleries'),
    'category_text' => T_('Gallery') . ': ',
    'categories_text' => T_('Galleries') . ': ',
]);
// -------------------------------- END OF HEADER --------------------------------

// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
skin_include('_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>
<main id="content"><!-- This is were a link like "Jump to main content" would land -->
	<div class="container">
		<div class="main_content">
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
        'catdir_text' => '',
        'category_text' => T_('Gallery') . ': ',
        'categories_text' => T_('Galleries') . ': ',
        'messages_text' => T_('Sending a message'),
        'user_text' => '',
        'display_edit_links' => false,
    ]);
// ------------------------------ END OF REQUEST TITLE -----------------------------
?>

			<?php
                // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'mediaidx_thumb_size' => $Skin->get_setting('mediaidx_thumb_size'),
        'author_link_text' => 'preferredname',
        'item_class' => 'evo_post evo_content_block',
        'item_type_class' => 'evo_post__ptyp_',
        'item_status_class' => 'evo_post__',

        // Login
        'display_form_messages' => false,
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
        'register_page_before' => '<div class="login_block"><div class="evo_details">',
        'register_page_after' => '</div></div>',
        'display_abort_link' => ($Blog->get_setting('allow_access') == 'public'), // Display link to abort login only when it is really possible

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

        // Search Custome
        'search_class' => 'compact_search_form',
        'search_input_before' => '<div class="input-group">',
        'search_input_after' => '',
        'search_submit_before' => '<span class="input-group-btn">',
        'search_submit_after' => '</span></div>',

        // Pagination
        'pagination' => [
            'block_start' => '<div class="center"><ul class="pagination">',
            'block_end' => '</ul></div>',
            'page_current_template' => '<span>$page_num$</span>',
            'page_item_before' => '<li>',
            'page_item_after' => '</li>',
            'page_item_current_before' => '<li class="active">',
            'page_item_current_after' => '</li>',
            'prev_text' => '<i class="fa fa-angle-left"></i>',
            'next_text' => '<i class="fa fa-angle-right"></i>',
        ],

        // Profile tabs to switch between user edit forms
        'profile_tabs' => [
            'block_start' => '<nav><ul class="nav nav-tabs profile_tabs">',
            'item_start' => '<li>',
            'item_end' => '</li>',
            'item_selected_start' => '<li class="active">',
            'item_selected_end' => '</li>',
            'block_end' => '</ul></nav>',
        ],
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

		</div><!-- .main_content -->
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
