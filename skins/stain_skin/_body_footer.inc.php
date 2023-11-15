<?php
/**
 * This is the footer include template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://b2evolution.net/man/skin-development-primer}
 *
 * This is meant to be included in a page template.
 *
 * @package evoskins
 * @subpackage bootstrap_gallery_skin
 */
if(! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
global $Skin;

?>
<!-- =================================== START OF FOOTER =================================== -->
<footer id="footer">
    <div class="container">
        <div class="footer__content">

            <?php if ($Skin->get_setting('footer_widget')) :
                $wic = $Skin->get_setting('footer_widget_column');
                $column = '';
                switch ($wic) {
                    case '2':
                        $column = 'col-md-6';
                        break;

                    case '3':
                        $column = 'col-md-4';
                        break;

                    case '4':
                        $column = 'col-md-3';
                        break;

                    default:
                        $column = 'col-md-12';
                        break;
                }

                // ------------------------- "Footer" CONTAINER EMBEDDED HERE --------------------------
                widget_container('footer', [
                    // The following params will be used as defaults for widgets included in this container:
                    'container_display_if_empty' => false, // If no widget, don't display container at all
                    'container_start' => '<div class="evo_container $wico_class$ row footer__widgets clearfix">',
                    'container_end' => '</div>',
                    'block_start' => '<div class="evo_widget $wi_class$ ' . $column . ' col-sm-6 col-xs-12">',
                    'block_end' => '</div>',
                    'block_title_start' => '<h3 class="widget_title">',
                    'block_title_end' => '</h3>',
                    // If a widget displays a list, this will enclose that list:
                    'list_start' => '<ul>',
                    'list_end' => '</ul>',
                    // This will enclose each item in a list:
                    'item_start' => '<li>',
                    'item_end' => '</li>',

                    // Search Custome
                    'search_class' => 'compact_search_form',
                    'search_input_before' => '<div class="input-group">',
                    'search_input_after' => '',
                    'search_submit_before' => '<span class="input-group-btn">',
                    'search_submit_after' => '</span></div>',
                ]);
                // ----------------------------- END OF "Footer" CONTAINER -----------------------------
            endif; ?>

            <div class="footer__bottom">
                <?php
                if ($Skin->get_setting('footer_social') == 1) {
                    skin_widget([
                        'widget' => 'user_links',
                        'block_start' => '<div class="footer__social float-right">',
                        'block_end' => '</div>',
                        'list_start' => '<li>',
                        'list_end' => '</li>',
                        'item_start' => '<li>',
                        'item_end' => '</li>',
                    ]);
                }
?>

                <p class="copyright float-left">
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
            </div><!-- .footer__bottom -->

        </div><!-- .footer__content -->
    </div><!-- /.container -->
</footer><!-- /footer -->

<?php if ($Skin->get_setting('back_to_top') == 1) { ?>
<a href="#0" class="cd_top"><i class="fa fa-angle-up"></i></a>
<?php } ?>

<?php
// ---------------------------- SITE FOOTER INCLUDED HERE ----------------------------
// If site footers are enabled, they will be included here:
siteskin_include('_site_body_footer.inc.php');
// ------------------------------- END OF SITE FOOTER --------------------------------
