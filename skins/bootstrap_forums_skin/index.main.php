<?php
/**
 * This is the main/default page template for the "bootstrap_forums" skin.
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
 * @subpackage bootstrap_forums
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (evo_version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

global $Skin, $tag;


// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);

global $cat;
$posts_text = T_('Forum');
if ($disp == 'posts') {
    if (! empty($cat) && ($cat > 0)) { // Set category name when some forum is opened
        $ChapterCache = &get_ChapterCache();
        if ($Chapter = $ChapterCache->get_by_ID($cat)) {
            $posts_text .= ': ' . $Chapter->get('name');
        }
    } else { // Set title for ?disp=posts
        $posts_text = T_('Latest topics');
    }
}

// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', [
    'catdir_text' => T_('Forum'),
    'category_text' => T_('Forum') . ': ',
    'comments_text' => T_('Latest Replies'),
    'front_text' => T_('Forum'),
    // Display default title only for tag page without intro Item:
    'posts_text' => (isset($tag) && ! has_featured_Item() ? '#' : $posts_text),
    'useritems_text' => T_('User\'s topics'),
    'usercomments_text' => T_('User\'s replies'),
    'flagged_text' => T_('Flagged topics'),
    'mustread_text' => T_('Must Read topics'),
]);
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>


<div class="container">


<header class="row">

		<?php
            // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
            // Display container and contents:
            widget_container('page_top', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => true, // Display container anyway even if no widget
                'container_start' => '<div class="col-xs-12 col-sm-12 col-md-4 col-md-push-8"><div class="evo_container $wico_class$">',
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
        'container_display_if_empty' => true, // Display container anyway even if no widget
        'container_start' => '<div class="col-xs-12 col-sm-12 col-md-8 col-md-pull-4"><div class="evo_container $wico_class$">',
        'container_end' => '</div></div>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h1>',
        'block_title_end' => '</h1>',
    ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>

</header><!-- .row -->

		<?php
            // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
            // Display container and contents:
            // Note: this container is designed to be a single <ul> list
    widget_container('menu', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<nav class="row"><div class="col-md-12"><ul class="nav nav-tabs evo_container $wico_class$">',
        'container_end' => '</ul></div></nav>',
        'block_start' => '',
        'block_end' => '',
        'block_display_title' => false,
        'list_start' => '',
        'list_end' => '',
        'item_start' => '<li class="evo_widget $wi_class$">',
        'item_end' => '</li>',
        'item_selected_start' => '<li class="active evo_widget $wi_class$">',
        'item_selected_end' => '</li>',
        'item_title_before' => '',
        'item_title_after' => '',
    ]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>

<div class="row">

	<div class="evo_content_col <?php echo $Skin->get_column_class(); ?>">

		<main><!-- This is were a link like "Jump to main content" would land -->

		<!-- ================================= START OF MAIN AREA ================================== -->

		<?php
if (! in_array($disp, ['login', 'lostpassword', 'register', 'activateinfo', 'access_requires_login', 'content_requires_login'])) { // Don't display the messages here because they are displayed inside wrapper to have the same width as form
    // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
    messages([
        'block_start' => '<div class="action_messages">',
        'block_end' => '</div>',
    ]);
    // --------------------------------- END OF MESSAGES ---------------------------------
}
?>

		<?php
    if ($disp == 'edit') {	// Add or Edit a post
        // TODO: fp>yura : this MUST NOT be in the skin. It must be in the b2evolution core (somewhere where we determine $disp)
        $p = param('p', 'integer', 0); // Edit post from Front-office
    }

// ------------------------ TITLE FOR THE CURRENT REQUEST ------------------------
request_title([
    'title_before' => '<h2 class="page_title">',
    'title_after' => '</h2>',
    'title_single_disp' => false,
    'title_page_disp' => false,
    'title_widget_page_disp' => false,
    'format' => 'htmlbody',
    'category_text' => '',
    'categories_text' => '',
    'catdir_text' => '',
    'comments_text' => '',
    'search_text' => '',
    'front_text' => '',
    'posts_text' => '',
    'flagged_text' => '',
    'mustread_text' => '',
    'useritems_text' => T_('User\'s topics'),
    'usercomments_text' => T_('User\'s replies'),
    'register_text' => '',
    'login_text' => '',
    'lostpassword_text' => '',
    'account_activation' => '',
    'msgform_text' => '',
    'user_text' => '',
    'users_text' => '',
    'display_edit_links' => ($disp == 'edit'),
    'edit_links_template' => [
        'before' => '<span class="pull-right">',
        'after' => '</span>',
        'advanced_link_class' => 'btn btn-info btn-sm',
        'close_link_class' => 'btn btn-default btn-sm',
    ],
]);
// ----------------------------- END OF REQUEST TITLE ----------------------------
?>

		<?php
if (in_array($disp, ['front', 'single', 'page', 'posts'])) { // Widget 'Search form':
    skin_widget([
        // CODE for the widget:
        'widget' => 'coll_search_form',
        // Optional display params
        'block_display_title' => false,
        'search_class' => 'index_compact_search_form',
        'template' => 'search_form_simple',
    ]);
    // Display a button to view the Recent/New Topics:
    $Skin->display_button_recent_topics();
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
        'featured_intro_before' => '',
        'featured_intro_after' => '',
        'intro_class' => 'jumbotron',
        'featured_class' => 'featurepost',
        // Form "Sending a message"
        'msgform_form_title' => T_('Contact'),
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

		</main>

	</div><!-- .col -->


	<?php
    if ($Skin->is_visible_sidebar()) { // Display sidebar:
        ?>
	<aside class="evo_sidebar_col col-md-3<?php echo($Skin->get_setting_layout() == 'left_sidebar' ? ' pull-left-md' : ''); ?>">
		<!-- =================================== START OF SIDEBAR =================================== -->
		<div id="evo_container__sidebar">
		<?php
            // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
            // Display container contents:
                widget_container('sidebar', [
                    // The following (optional) params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => false, // If no widget, don't display container at all
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
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>

		<?php
            // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
            // Display container contents:
            widget_container('sidebar_2', [
                // The following (optional) params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
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
        // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
        ?>
		</div>
	</aside><!-- .col -->
	<?php } ?>

	<footer class="col-md-12 clear">
		<?php skin_include('_legend.inc.php'); ?>
	</footer>

</div><!-- .row -->

<?php
// ------------------------- "Forum Front Secondary Area" CONTAINER EMBEDDED HERE --------------------------
if ($disp == 'front') {
    // Display container and contents:
    widget_container('forum_front_secondary_area', [
        // The following params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        'container_start' => '<section class="secondary_area"><div class="evo_container $wico_class$">',
        'container_end' => '</div></section>',
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h2 class="page-header">',
        'block_title_end' => '</h2>',
    ]);
    // ----------------------------- END OF "Forum Front Secondary Area" CONTAINER -----------------------------
}
?>

<footer class="container-fluid">

	<!-- =================================== START OF FOOTER =================================== -->
	<div class="row">
   
		<div class="col-md-12">

				<?php
                // Display container and contents:
                widget_container('footer', [
                    // The following params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => false, // If no widget, don't display container at all
                    'container_start' => '<div class="evo_container $wico_class$ clearfix">', // Note: clearfix is because of Bootstraps' .cols
                    'container_end' => '</div>',
                    'block_start' => '<div class="evo_widget $wi_class$">',
                    'block_end' => '</div>',
                ]);
?>

			<p class="center">
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
		</div><!-- .col -->
		
	</div>

</footer><!-- .row -->


</div><!-- .container -->


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>