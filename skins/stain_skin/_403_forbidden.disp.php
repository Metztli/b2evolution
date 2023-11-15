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
?>
<div class="error_404">
    <?php
    global $disp_detail, $baseurl, $app_name;

echo '<div class="evo_widget widget_core_page_404_not_found">';

echo '<h3>403 Forbidden</h3>';

echo '<p><a href="' . $baseurl . '">' . $app_name . '</a> cannot resolve the requested URL.</p>';

// You may use this to further customize this page:
// echo $disp_detail;

echo '</div>';


echo '<div class="error_additional_content">';
// --------------------------------- START OF CLOUD TAG --------------------------------
// Call the coll_search_form widget:
skin_widget([
    // CODE for the widget:
    'widget' => 'coll_tag_cloud',
    // Optional display params:
    'block_start' => '<div class="evo_widget $wi_class$">',
    'block_end' => '</div>',
    'block_title_start' => '<h2>',
    'block_title_end' => '</h2>',
]);
// ---------------------------------- END OF CLOUD TAG ---------------------------------
echo '</div>';
?>
</div>
