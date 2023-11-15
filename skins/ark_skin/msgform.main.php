<?php
/**
 * This is the template that displays the message user form
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 * To display a feedback, you should call a stub AND pass the right parameters
 * For example: /blogs/index.php?disp=msgform&recipient_id=n
 * Note: don't code this URL by hand, use the template functions to generate it!
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 * @subpackage bootstrap_blog_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $app_version, $disp, $Collection, $Blog;

if (evo_version_compare($app_version, '6.4') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 6.4 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);

// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php');
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>

<div class="container-fluid">
<div class="row">

<?php
if ($Skin->show_container_when_access_denied('Header')) { // Display 'Page Top' widget container
    ?>
		
	<div class="headpicture">

		<div class="headipic_section <?php
                                            if ($Skin->get_setting('header_content_pos') == 'center_pos') {
                                                echo 'center';
                                            } elseif ($Skin->get_setting('header_content_pos') == 'left_pos') {
                                                echo 'left';
                                            } elseif ($Skin->get_setting('header_content_pos') == 'right_pos') {
                                                echo 'right';
                                            }
    ?>">
			<?php
                widget_container('header', [
    // The following params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => false, // If no widget, don't display container at all
                    'container_start' => '<div class="evo_container $wico_class$' . ($Skin->get_setting('header_content_pos') == 'column_pos' ? ' container' : '') . '">',
                    'container_end' => '</div>',
                    'block_start' => '<div class="evo_widget $wi_class$">',
                    'block_end' => '</div>',
]);
    ?>

		</div>
		
	</div>
	
<?php } ?>

<?php
if ($Skin->show_container_when_access_denied('Menu')) { // Display 'Page Top' widget container
    ?>

<nav class="top-menu container-fluid">
	<div class="row">
		<!-- Brand and toggle get grouped for better mobile display -->

<?php if ($Skin->get_setting('top_menu_position') == 'menu_inline') {
    echo '<div class="container menu_inline_container">';
} ?>

		<div class="navbar-header<?php if ($Skin->get_setting('top_menu_position') == 'menu_center') {
		    echo ' navbar-header-center';
		} ?>">
			<button type="button" class="navbar-toggle navbar-toggle-hamb collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
				<?php
		        if ($Skin->get_setting('top_menu_brand')) {
		            // ------------------------- "Menu" Collection title --------------------------
		            skin_widget([
                // CODE for the widget:
		                'widget' => 'coll_title',
		                // Optional display params
		                'block_start' => '<div class="navbar-brand">',
		                'block_end' => '</div>',
		                'item_class' => 'navbar-brand',
            ]);
		            // ------------------------- "Menu" Collection logo --------------------------
		        }
    ?>
		</div><!-- /.navbar-header -->
		
		<!-- Collect the nav links, forms, and other content for toggling -->
				<?php
        // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
        // Note: this container is designed to be a single <ul> list
        widget_container('menu', [
            // The following params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            'container_start' => '<div class="collapse navbar-collapse' . ($Skin->get_setting('top_menu_position') == 'menu_center' ? ' menu_center' : '') . '" id="navbar-collapse-1"><ul class="navbar-nav evo_container $wico_class$" id="menu">',
            'container_end' => '</ul></div>',
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
		
<?php if ($Skin->get_setting('top_menu_position') == 'menu_inline') {
    echo '</div><!-- .container -->';
} ?>
		
	</div><!-- .row -->
</nav><!-- .top-menu -->

<?php } ?>

</div>
</div>

<div class="container">

<!-- =================================== START OF MAIN AREA =================================== -->
	<div class="row">
		<div class="<?php echo($Skin->get_setting('layout') == 'single_column' ? 'col-md-12' : 'col-md-8'); ?>"<?php
                echo($Skin->get_setting('layout') == 'left_sidebar' ? ' style="float:right;"' : ''); ?>>
				
		<main><!-- This is were a link like "Jump to main content" would land -->

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
        'title_before' => '<h2 class="page_title">',
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

		</main>

	</div><!-- .col -->

	<?php
    if ($Skin->get_setting('layout') != 'single_column') {
        ?>
	<div class="col-md-4 sidebar"<?php echo($Skin->get_setting('layout') == 'left_sidebar' ? ' style="float:left;"' : ''); ?>>
		<?php
            if ($Skin->show_container_when_access_denied('sidebar')) {	// Display 'Sidebar' widget container:
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
                    'search_input_before' => '<div class="input-group">',
                    'search_input_after' => '',
                    'search_submit_before' => '<span class="input-group-btn">',
                    'search_submit_after' => '</span></div>',
                ]);
                // ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
            } ?>

		<?php
            if ($Skin->show_container_when_access_denied('sidebar2')) {	// Display 'Sidebar 2' widget container:
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
            } ?>

	</div><!-- .col -->
	<?php } ?>

</div><!-- .row -->
</div>

<?php
if ($Skin->show_container_when_access_denied('footer')) { // Display 'Footer' widget container
    ?>

<!-- =================================== START OF FOOTER =================================== -->
<footer class="footer">
	<div class='container'>
	<div class="row">
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
	</div>
</footer>

<?php } ?>


<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------


// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// ------------------------------- END OF FOOTER --------------------------------
?>