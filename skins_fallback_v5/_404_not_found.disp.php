<?php
/**
 * This is the template that displays the 404 disp content
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


// ------------------------- "404 Page" CONTAINER EMBEDDED HERE --------------------------
skin_container( /* TRANS: Widget container name */ NT_('404 Page'), [
    // The following params will be used as defaults for widgets included in this container:
    // This will enclose each widget in a block:
    'block_start' => '<div class="widget error_404 $wi_class$">',
    'block_end' => '</div>',
    // This will enclose the title of each widget:
    'block_title_start' => '<h3>',
    'block_title_end' => '</h3>',
]);
// ----------------------------- END OF "404 Page" CONTAINER -----------------------------
