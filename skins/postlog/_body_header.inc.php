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
 * @subpackage skeleton_by_tilqi
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

?>    
<link rel="stylesheet" type="text/css" media="all" href="rsc/jScrollPane.css" />
 <script type="text/javascript" src="rsc/jquery142.js"></script>
 <script type="text/javascript" src="rsc/jquery-ui-personalized-1.js"></script>
<!--[if lt IE 8]>
<script src="rsc/IE8.js" type="text/javascript"></script>
<![endif]-->
<!--[if IE]>
<style type="text/css">
.content,
#main,
#innerwrapper,
#wrapper {
zoom:1;
}
</style>
<![endif]-->
<!--[if IE]><link rel="stylesheet" type="text/css" href="ie.css" /><![endif]-->


<div id="header">

<div class="headermiddle"><div class="rss2" onclick="location.href='<?php echo '' . $Blog->gen_blogurl() . '?tempskin=_rss2 ' ?>';" style="cursor: pointer;">&nbsp;</div>
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



<div class="rightlist">
<div class="topmostlist">
			<?php if (true /* change to false to hide the blog list */) { ?>
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
                  'item_selected_start' => '<li class="selected">',
                  'item_selected_end' => '</li>',
            ]);
			    ?>
			<?php } ?>
	<?php
        // ------------------------- "Page Top" CONTAINER EMBEDDED HERE --------------------------
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
// ----------------------------- END OF "Page Top" CONTAINER -----------------------------
?>
</div>


<div class="navlist">
	<ul>
	<?php
        // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
        // Display container and contents:
        // Note: this container is designed to be a single <ul> list
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
</div><!-- end of .rightlist-->

</div> <!-- end of #header -->
