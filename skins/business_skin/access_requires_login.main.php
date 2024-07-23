<?php
/**
 * This file is the template that displays "login required" for non logged-in users.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * @package evoskins
 * @subpackage bootstrap_blog
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


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', []);
// -------------------------------- END OF HEADER --------------------------------

// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------

?>
<?php if ($Skin->is_visible_container('header')) { ?>
	<?php if ($Skin->get_setting('ht_show') == true) { ?>
	<header id="header-top" class="clearfix">
		<div class="container">
			<div class="row">

			<?php
                // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
                // Display container and contents:
                widget_container('page_top', [
                    // The following params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => true, // Display container anyway even if no widget
                    'container_start' => '<div class="col-xs-12 col-sm-6 col-md-6 pull-right page-top"><div class="evo_container $wico_class$">',
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

	         <div class="col-xs-12 col-sm-6 col-md-6">
	      		<div class="evo_container">
	      		   <p class="header-contact-info">
	               <?php
	               // Display contact info, adding on your skin settings
	       if ($text = $Skin->get_setting('ht_contact_info')) {
	           echo $text;
	       };
	    ?>
	               </p>
	      		</div>
	      	</div><!-- .col -->

	      </div><!-- .row -->
	   </div><!-- .container -->
	</header><!-- #header-top -->
	<?php } else { ?>
	   <header id="header-top-hidden"></header>
	<?php } ?>

	<header id="main-header">
		<div class="container">
			<div class="row">

			<?php
	            // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
	            // Display container and contents:
	            widget_container('header', [
	                // The following params will be used as defaults for widgets included in this container:
	                'container_display_if_empty' => true, // Display container anyway even if no widget
	                'container_start' => '<div class="col-xs-9 col-sm-12 col-md-5"><div class="evo_container $wico_class$">',
	                'container_end' => '</div></div>',
	                'block_start' => '<div class="evo_widget $wi_class$">',
	                'block_end' => '</div>',
	                'block_title_start' => '<h1>',
	                'block_title_end' => '</h1>',
	            ]);
    // ----------------------------- END OF "Header" CONTAINER -----------------------------
    ?>

	      	<div class="col-xs-3 col-sm-12 col-md-7">
	            <nav class="primary-nav">
	               <!-- Toggle get grouped for better mobile display -->
	               <div class="navbar-header">
	                 <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#primary_nav">
	                   <span class="sr-only">Toggle navigation</span>
	                   <span class="icon-bar"></span>
	                   <span class="icon-bar"></span>
	                   <span class="icon-bar"></span>
	                </button>
	               </div>

							<?php
                                // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
                                // Display container and contents:
                                // Note: this container is designed to be a single <ul> list
                        widget_container('menu', [
                            // The following params will be used as defaults for widgets included in this container:
                            'container_display_if_empty' => false, // If no widget, don't display container at all
                            'container_start' => '<div class="collapse navbar-collapse" id="primary_nav"><ul class="nav nav-tabs evo_container $wico_class$">',
                            'container_end' => '</ul><button type="button" class="close-menu collapsed" data-toggle="collapse" data-target="#primary_nav"></button></div>',
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

	            </nav><!-- .primary-nav -->
	      	</div><!-- .col -->

	      </div><!-- .row -->
	   </div><!-- .container -->
	</header><!-- #main-header -->
<?php } //Close if the header checklist on options?>


<div id="main-content">
	<div class="container">
		<div class="row">
			<div class="<?php echo $Skin->is_visible_sidebar(true) ? $Skin->get_column_class() : 'col-md-12'; ?>">
				<!-- ================================= START OF MAIN AREA ================================== -->
				<?php
                    // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
                    skin_include('$disp$', [
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
                    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>
			</div><!-- .col -->
			<?php
if ($Skin->is_visible_container('sidebar')) {
    // ------------------------- SIDEBAR INCLUDED HERE --------------------------
    skin_include('_sidebar.inc.php');
    // Note: You can customize the sidebar by copying the
    // _sidebar.inc.php file into the current skin folder.
    // ----------------------------- END OF SIDEBAR -----------------------------
}
?>
		</div><!-- .row -->
	</div><!-- .container -->
</div><!-- .main-content -->

<footer id="main-footer">

	<!-- =================================== START OF FOOTER =================================== -->
	<?php if ($Skin->is_visible_container('footer')) {
	    // ------------------------- "Footer" CONTAINER EMBEDDED HERE --------------------------
	    widget_container('footer', [
	        // The following params will be used as defaults for widgets included in this container:
	        'container_display_if_empty' => false, // If no widget, don't display container at all
	        'container_start' => '<div class="widget_footer"><div class="container"><div class="row evo_container $wico_class$">',
	        'container_end' => '</div></div></div>',
	        'block_start' => '<div class="evo_widget $wi_class$ col-xs-12 col-sm-6 col-md-3">',
	        'block_end' => '</div>',
	        'block_title_start' => '<h4 class="widget_title">',
	        'block_title_end' => '</h4>',
	        // Search
	        'search_input_before' => '<div class="input-group">',
	        'search_input_after' => '',
	        'search_submit_before' => '<span class="input-group-btn">',
	        'search_submit_after' => '</span></div>',
	    ]);
	    // ----------------------------- END OF "Footer" CONTAINER -----------------------------
	} ?>

   <div class="copyright">
      <div class="container">
   		<p>
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

      <!-- Powered By place -->

	   </div><!-- .container -->
   </div><!-- .copyright -->
</footer><!-- #main-footer -->

<?php if ($Skin->get_setting('back_to_top') == 1) { ?>
<a href="#0" class="cd-top"><i class="fa fa-angle-up"></i></a>
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
