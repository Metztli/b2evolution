<?php
/**
 * This is the sidebar include template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * This is meant to be included in a page template.
 *
 * @package evoskins
 * @subpackage postlog
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

?>
<div id="sidebar_wrap">
<div class="notes-top">&nbsp;<!-- insert a blank space for validation & emulate the img through css--></div>
<div class="sidebar">

	<?php
        // ------------------------- "Sidebar" CONTAINER EMBEDDED HERE --------------------------
        // Display container contents:
        skin_container(NT_('Sidebar 2'), [
            // The following (optional) params will be used as defaults for widgets included in this container:
            // This will enclose each widget in a block:
            'block_start' => '<div class="bSideItem $wi_class$">',
            'block_end' => '</div>',
            // This will enclose the title of each widget:
            'block_title_start' => '<h3>',
            'block_title_end' => '</h3>',
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
        ]);
// ----------------------------- END OF "Sidebar" CONTAINER -----------------------------
?>

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
</div><div class="notes-bottom">&nbsp;<!-- insert a blank space for validation & emulate the img through css--></div>

</div><!-- end of sidebar_Wrap-->