<?php
/**
 * This is the BODY header include template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * This is meant to be included in a page template.
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>

<div class="container-fluid">
<div class="row">

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

</div>
</div>