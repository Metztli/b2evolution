<?php
/**
 * This is the BODY header include template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * This is meant to be included in a page template.
 *
 * @package evoskins
 * @subpackage kubrick
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

?>

<div id="outer_wrap">
<div id="wrap">


<div id="header" onclick="location.href='<?php $Blog->disp('url') ?>';" style="cursor: pointer;">
	      <?php
      // START OF BLOG LIST
      skin_widget([
        'widget' => 'colls_list_public',
          'block_start' => '',
          'block_end' => '',
          'block_display_title' => false,
          'list_start' => '<ul>',
          'list_end' => '</ul>',
          'item_start' => '<li>',
          'item_end' => '</li>',
          'item_selected_start' => '<li><span class="selected">',
          'item_selected_end' => '</span></li>',
    ]);
?>
	<?php
  // Display container and contents:
  skin_container(NT_('Page Top'), [
    // The following params will be used as defaults for widgets included in this container:
      'block_start' => '<div class="$wi_class$">',
      'block_end' => '</div>',
      'block_display_title' => false,
      'list_start' => '<ul>',
      'list_end' => '</ul>',
      'item_start' => '<li>',
      'item_end' => '</li>',
]);
?>

	<?php
    // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
    // Display container and contents:
    skin_container(NT_('Header'), [
        // The following params will be used as defaults for widgets included in this container:
        'block_start' => '<div class="$wi_class$">',
        'block_end' => '</div>',
        'block_title_start' => '<h1>',
        'block_title_end' => '</h1>',
    ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>
</div>

<div id="nav">
	<ul class="nav">
	<?php
        // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
    skin_container(NT_('Menu'), [
        // The following params will be used as defaults for widgets included in this container:
        'block_start' => '',
        'block_end' => '',
        'block_display_title' => false,
        'list_start' => '',
        'list_end' => '',
        'item_start' => '<li>',
        'item_end' => '</li>',
    ]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>
	</ul>
</div>
