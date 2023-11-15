<?php
/**
 * This is the 404 page template for the "Error 404" skin.
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
 * @subpackage bootstrap_manual
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Blog;

// ------------------------- "404 Page" CONTAINER EMBEDDED HERE --------------------------
widget_container('404_page', [
    // The following params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => false, // If no widget, don't display container at all
    'container_start' => '<div class="evo_container $wico_class$ error_404">',
    'container_end' => '</div>',
    // This will enclose each widget in a block:
    'block_start' => '<div class="evo_widget $wi_class$">',
    'block_end' => '</div>',
    // This will enclose the title of each widget:
    'block_title_start' => '<h3>',
    'block_title_end' => '</h3>',
    // Widget 'Search form':
    'search_input_before' => '<div class="input-group">',
    'search_input_after' => '',
    'search_submit_before' => '<span class="input-group-btn">',
    'search_submit_after' => '</span></div>',
]);
// ----------------------------- END OF "404 Page" CONTAINER -----------------------------
