<?php
/**
 * This is the template that displays the item block
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
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

global $Item;

// Default params:
$params = array_merge([
    'feature_block' => false,
    'item_class' => 'bPost',
    'item_status_class' => 'bPost',
    'content_mode' => 'full', // We want regular "full" content, even in category browsing: i-e no excerpt or thumbnail
    'image_size' => '', // Do not display images in content block - Image is handled separately
    'url_link_text_template' => '', // link will be displayed (except player if podcast)
], $params);

?>

<div id="<?php $Item->anchor_id() ?>" class="<?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>">

	<?php
        $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
?>

	<?php
        // Display images that are linked to this post:
    $Item->images([
        'before' => '<div class="bImages">',
        'before_image' => '<div class="image_block">',
        'before_image_legend' => '<div class="image_legend">',
        'after_image_legend' => '</div>',
        'after_image' => '</div>',
        'after' => '</div>',
        'image_size' => 'fit-720x500', // See the $thumbnail_sizes array in conf/_advanced.php.
        // Optionally restrict to files/images linked to specific position: 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'
        'restrict_to_image_position' => 'cover,teaser,teaserperm,teaserlink',
    ]);
?>


	<div class="bDetails">

		<div class="bSmallHead">

			<?php
            if ($Item->status != 'published') {
                $Item->format_status([
                    'template' => '<div class="floatright"><span class="note status_$status$" data-toggle="tooltip" data-placement="top" title="$tooltip_title$"><span>$status_title$</span></span></div>',
                ]);
            }
// Link to comments, trackbacks, etc.:
$Item->feedback_link([
    'type' => 'feedbacks',
    'link_before' => '<div class="action_right">',
    'link_after' => '</div>',
    'link_text_zero' => get_icon('nocomment'),
    'link_text_one' => get_icon('comments'),
    'link_text_more' => get_icon('comments'),
    'link_title' => '#',
]);

$Item->permanent_link([
    'before' => '<div class="action_right">',
    'after' => '</div>',
    'text' => T_('Permalink'),
]);
?>

			<?php
    $Item->edit_link([
        // Link to backoffice for editing
        'before' => '<div class="action_right">',
        'after' => '</div>',
        'text' => T_('Edit...'),
        'title' => T_('Edit title/description...'),
    ]);
?>

			<h3 class="bTitle linked"><?php
    $Item->title([
        'link_type' => 'permalink',
    ]);
?></h3>

			<?php
    $Item->issue_date([
        'before' => '<span class="timestamp">',
        'after' => '</span>',
        'date_format' => locale_datefmt() . ' ' . locale_shorttimefmt(),
    ]);
?>

		</div>

		<?php
// Display images that are linked as "after more" on this post:
// We are actually displaying them before more here, but this is a special photo skin.
$Item->images([
    'before' => '<div class="bImages">',
    'before_image' => '<div class="image_block">',
    'before_image_legend' => '<div class="image_legend">',
    'after_image_legend' => '</div>',
    'after_image' => '</div>',
    'after' => '</div>',
    'image_size' => 'fit-520x390',
    // Optionally restrict to files/images linked to specific position: 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'
    'restrict_to_image_position' => 'aftermore',
]);
?>

		<?php
if ($disp == 'single') {
    // ------------------------- "Item Single" CONTAINER EMBEDDED HERE --------------------------
    // Display container contents:
    skin_container( /* TRANS: Widget container name */ NT_('Item Single'), [
        'widget_context' => 'item',	// Signal that we are displaying within an Item
        // The following (optional) params will be used as defaults for widgets included in this container:
        // This will enclose each widget in a block:
        'block_start' => '<div class="$wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
        // Template params for "Item Link" widget
        'widget_item_link_before' => '<p class="evo_post_link">',
        'widget_item_link_after' => '</p>',
    ]);
    // ----------------------------- END OF "Item Single" CONTAINER -----------------------------
} else {
    // ---------------------- POST CONTENT INCLUDED HERE ----------------------
    // Note: at the top of this file, we set: 'image_size' =>	'', // Do not display images in content block - Image is handled separately
    skin_include('_item_content.inc.php', $params);
    // Note: You can customize the default item content by copying the generic
    // /skins/_item_content.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
}
?>

		<div class="bSmallPrint">
		<?php
    $Item->author([
        'before' => T_('By') . ' ',
        'after' => ' &bull; ',
        'link_text' => 'auto',
    ]);
?>

		<?php
    $Item->categories([
        'before' => T_('Galleries') . ': ',
        'after' => ' ',
        'include_main' => true,
        'include_other' => true,
        'include_external' => true,
        'link_categories' => true,
    ]);
?>

		<?php
    // List all tags attached to this post:
    $Item->tags([
        'before' => ' &bull; ' . T_('Tags') . ': ',
        'after' => ' ',
        'separator' => ', ',
    ]);
?>

		</div>
	</div>

	<?php
    if (is_single_page()) {	// Display comments only on single Item's page:
        // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
        skin_include('_item_feedback.inc.php', [
            'disp_comments' => true,
            'disp_comment_form' => true,
            'disp_trackbacks' => true,
            'disp_trackback_url' => true,
            'disp_pingbacks' => true,
            'disp_webmentions' => true,
            'before_section_title' => '<h4>',
            'after_section_title' => '</h4>',
            'author_link_text' => 'auto',
        ]);
        // Note: You can customize the default item feedback by copying the generic
        // /skins/_item_feedback.inc.php file into the current skin folder.
        // ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
    }
?>

	<?php
    locale_restore_previous();	// Restore previous locale (Blog locale)
?>

</div>