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
<header id="header">
            <?php
            // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
            // Display container and contents:
            widget_container('header', [
                // The following params will be used as defaults for widgets included in this container:
                'container_display_if_empty' => false, // If no widget, don't display container at all
                'container_start' => '<div class="main_header"><div class="evo_container $wico_class$ brand">',
                'container_end' => '</div></div>',
                'block_start' => '<div class="evo_widget $wi_class$">',
                'block_end' => '</div>',
                'block_title_start' => '<h1>',
                'block_title_end' => '</h1>',

                // Search Custome
                'search_class' => 'compact_search_form',
                'search_input_before' => '<div class="input-group">',
                'search_input_after' => '',
                'search_submit_before' => '<span class="input-group-btn">',
                'search_submit_after' => '</span></div>',
            ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>

    <nav class="main_navigation">
        <div class="container">
            <div class="row evo_nav">
                <div class="mobile_nav">
                    <button id="main_nav" class="menu_hamburger">
                        <span>Menu</span>
                        <div class="hamburger">
                            <div class="menui top-menu"></div>
                            <div class="menui mid-menu"></div>
                            <div class="menui bottom-menu"></div>
                        </div>
                    </button>
                </div>

                    <div class="mobile_nav_close">
                        <button class="menu_hamburger close_nav">
                            <span>Menu</span>
                            <div class="hamburger">
                                <div class="menui top-menu"></div>
                                <div class="menui mid-menu"></div>
                                <div class="menui bottom-menu"></div>
                            </div>
                        </button>
                    </div>
                    <?php
                    // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
        $hover_style = '';
$hover_nav = $Skin->get_setting('nav_hover_style');
switch ($hover_nav) {
    case $hover_nav:
        $hover_style = 'hover-' . $hover_nav;
        break;
}

// Display container and contents:
// Note: this container is designed to be a single <ul> list
widget_container('menu', [
    // The following params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => false, // If no widget, don't display container at all
    'container_start' => '<ul class="nav nav-tabs evo_container $wico_class$">',
    'container_end' => '</ul>',
    'block_start' => '',
    'block_end' => '',
    'block_display_title' => false,
    'list_start' => '',
    'list_end' => '',
    'item_start' => '<li class="evo_widget $wi_class$ ' . $hover_style . '">',
    'item_end' => '</li>',
    'item_selected_start' => '<li class="active evo_widget $wi_class$ ' . $hover_style . '">',
    'item_selected_end' => '</li>',
    'item_title_before' => '',
    'item_title_after' => '',
]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>
            </div>
        </div><!-- /.container -->

    </nav><!-- /#main_navigation -->
</header><!-- /#Header -->
