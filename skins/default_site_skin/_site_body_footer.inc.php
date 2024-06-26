<?php
/**
 * This is the site header include template.
 *
 * If enabled, this will be included at the bottom of all skins to provide site wide copyright info for example.
 * NOTE: each skin is ressponsible for calling siteskin_include( '_site_body_footer.inc.php' );
 *
 * @package skins
 * @subpackage default_site_skin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


// ------------------------- "Site Footer" CONTAINER EMBEDDED HERE --------------------------
widget_container('site_footer', [
    // The following params will be used as defaults for widgets included in this container:
    'container_display_if_empty' => true, // Display container anyway even if no widget
    'container_start' => '<footer id="evo_site_footer" class="evo_container $wico_class$">',
    'container_end' => '</footer>',
    'block_start' => '<span class="evo_widget $wi_class$">',
    'block_end' => '</span>',
    'block_display_title' => false,
]);
// ----------------------------- END OF "Site Footer" CONTAINER -----------------------------
