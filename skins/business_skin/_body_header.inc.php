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
