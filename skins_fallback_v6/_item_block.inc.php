<?php
/**
 * This is the template that displays the item block: title, author, content (sub-template), tags, comments (sub-template)
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

global $Item, $Skin, $app_version;

// Default params:
$params = array_merge([
    'feature_block' => false,			// fp>yura: what is this for??
    // Classes for the <article> tag:
    'item_class' => 'evo_post evo_content_block',
    'item_type_class' => 'evo_post__ptyp_',
    'item_status_class' => 'evo_post__',
    'item_style' => '',
    // Controlling the title:
    'disp_title' => true,
    'item_title_line_before' => '<div class="evo_post_title">',	// Note: we use an extra class because it facilitates styling
    'item_title_before' => '<h2>',
    'item_title_after' => '</h2>',
    'item_title_single_before' => '<h1>',	// This replaces the above in case of disp=single or disp=page
    'item_title_single_after' => '</h1>',
    'item_link_type' => '#',
    'item_title_line_after' => '</div>',
    // Controlling the content:
    'content_mode' => 'auto',		// excerpt|full|normal|auto -- auto will auto select depending on $disp-detail
    'image_class' => 'img-responsive',
    'image_size' => get_skin_setting('main_content_image_size', 'fit-1280x720'),
    'author_link_text' => 'auto',
], $params);


echo '<div class="evo_content_block">'; // Beginning of post display
?>

<article id="<?php $Item->anchor_id() ?>" class="<?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>"<?php
    echo empty($params['item_style']) ? '' : ' style="' . format_to_output($params['item_style'], 'htmlattr') . '"' ?>>

	<header>
	<?php
        $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)

if ($disp == 'page' || $Item->is_intro()) {
    // ------- Title -------
    if ($params['disp_title']) {
        echo $params['item_title_line_before'];

        // POST TITLE:
        $Item->title([
            'before' => $params['item_title_before'],
            'after' => $params['item_title_after'],
            'link_type' => '#',
        ]);

        // EDIT LINK:
        if ($Item->is_intro()) { // Display edit link only for intro posts, because for all other posts the link is displayed on the info line.
            $Item->edit_link([
                'before' => '<div class="' . button_class('group') . '">',
                'after' => '</div>',
                'text' => $Item->is_intro() ? get_icon('edit') . ' ' . T_('Edit Intro') : '#',
                'class' => button_class('text'),
            ]);
        }

        echo $params['item_title_line_after'];
    }
}
?>

	<?php
if (! $Item->is_intro()) { // Don't display the following for intro posts
    if ($disp == 'posts') {
        // ------------------------- "Item in List" CONTAINER EMBEDDED HERE --------------------------
        // Display container contents:
        widget_container('item_in_list', [
            'widget_context' => 'item',	// Signal that we are displaying within an Item
            // The following (optional) params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            // This will enclose each widget in a block:
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            // This will enclose the title of each widget:
            'block_title_start' => '<h3>',
            'block_title_end' => '</h3>',

            'author_link_text' => $params['author_link_text'],

            // Controlling the title:
            'widget_item_title_params' => [
                'before' => $params['item_title_line_before'] . $params['item_title_before'],
                'after' => $params['item_title_after'] . $params['item_title_line_after'],
                'link_type' => $params['item_link_type'],
            ],
            // Item Visibility Badge widge template
            'widget_item_visibility_badge_display' => (! $Item->is_intro() && $Item->status != 'published'),
            'widget_item_visibility_badge_params' => [
                'template' => '<div class="evo_status evo_status__$status$ badge pull-right" data-toggle="tooltip" data-placement="top" title="$tooltip_title$">$status_title$</div>',
            ],
        ]);
        // ----------------------------- END OF "Item in List" CONTAINER -----------------------------
    } elseif ($disp != 'page') {
        // ------------------------- "Item Single - Header" CONTAINER EMBEDDED HERE --------------------------
        // Display container contents:
        widget_container('item_single_header', [
            'widget_context' => 'item',	// Signal that we are displaying within an Item
            // The following (optional) params will be used as defaults for widgets included in this container:
            'container_display_if_empty' => false, // If no widget, don't display container at all
            // This will enclose each widget in a block:
            'block_start' => '<div class="evo_widget $wi_class$">',
            'block_end' => '</div>',
            // This will enclose the title of each widget:
            'block_title_start' => '<h3>',
            'block_title_end' => '</h3>',

            'author_link_text' => $params['author_link_text'],

            // Controlling the title:
            'widget_item_title_params' => [
                'before' => $params['item_title_line_before'] . ($disp == 'single' ? $params['item_title_single_before'] : $params['item_title_before']),
                'after' => ($disp == 'single' ? $params['item_title_single_after'] : $params['item_title_after']) . $params['item_title_line_after'],
                'link_type' => $params['item_link_type'],
            ],
            // Item Previous Next widget
            'widget_item_next_previous_params' => [
            ],
            // Item Visibility Badge widge template
            'widget_item_visibility_badge_display' => (! $Item->is_intro() && $Item->status != 'published'),
            'widget_item_visibility_badge_params' => [
                'template' => '<div class="evo_status evo_status__$status$ badge pull-right" data-toggle="tooltip" data-placement="top" title="$tooltip_title$">$status_title$</div>',
            ],
        ]);
        // ----------------------------- END OF "Item Single - Header" CONTAINER -----------------------------
    }
    ?>
	<div class="small text-muted"></div>

	<?php
}
?>
	</header>

	<?php
if ($disp == 'single') {
    // ------------------------- "Item Single" CONTAINER EMBEDDED HERE --------------------------
    // Display container contents:
    widget_container('item_single', [
        'widget_context' => 'item',	// Signal that we are displaying within an Item
        // The following (optional) params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        // This will enclose each widget in a block:
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // Template params for "Item Link" widget
        'widget_item_link_before' => '<p class="evo_post_link">',
        'widget_item_link_after' => '</p>',
        // Template params for "Item Tags" widget
        'widget_item_tags_before' => '<nav class="small post_tags">',
        'widget_item_tags_after' => '</nav>',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
        // Template params for "Item Attachments" widget:
        'widget_item_attachments_params' => [
            'limit_attach' => 1000,
            'before' => '<div class="evo_post_attachments"><h3>' . T_('Attachments') . ':</h3><ul class="evo_files">',
            'after' => '</ul></div>',
            'before_attach' => '<li class="evo_file">',
            'after_attach' => '</li>',
            'before_attach_size' => ' <span class="evo_file_size">(',
            'after_attach_size' => ')</span>',
        ],
    ]);
    // ----------------------------- END OF "Item Single" CONTAINER -----------------------------
} elseif ($disp == 'page') {
    // ------------------------- "Item Page" CONTAINER EMBEDDED HERE --------------------------
    // Display container contents:
    widget_container('item_page', [
        'widget_context' => 'item',	// Signal that we are displaying within an Item
        // The following (optional) params will be used as defaults for widgets included in this container:
        'container_display_if_empty' => false, // If no widget, don't display container at all
        // This will enclose each widget in a block:
        'block_start' => '<div class="evo_widget $wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // Template params for "Item Link" widget
        'widget_item_link_before' => '<p class="evo_post_link">',
        'widget_item_link_after' => '</p>',
        // Template params for "Item Tags" widget
        'widget_item_tags_before' => '<nav class="small post_tags">' . T_('Tags') . ': ',
        'widget_item_tags_after' => '</nav>',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
        // Template params for "Item Attachments" widget:
        'widget_item_attachments_params' => [
            'limit_attach' => 1000,
            'before' => '<div class="evo_post_attachments"><h3>' . T_('Attachments') . ':</h3><ul class="evo_files">',
            'after' => '</ul></div>',
            'before_attach' => '<li class="evo_file">',
            'after_attach' => '</li>',
            'before_attach_size' => ' <span class="evo_file_size">(',
            'after_attach_size' => ')</span>',
        ],
    ]);
    // ----------------------------- END OF "Item Page" CONTAINER -----------------------------
} elseif ($Item->is_intro()) {	// Display item content only for intro items because for normal items we display content by widget in container "Item in List" above:
    // this will create a <section>
    // ---------------------- POST CONTENT INCLUDED HERE ----------------------
    skin_include('_item_content.inc.php', $params);
    // Note: You can customize the default item content by copying the generic
    // /skins/_item_content.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
    // this will end a </section>
}
?>

	<?php
if (is_single_page()) {	// Display comments only on single Item's page:
    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', array_merge([
        'disp_comments' => true,
        'disp_comment_form' => true,
        'disp_trackbacks' => true,
        'disp_trackback_url' => true,
        'disp_pingbacks' => true,
        'disp_webmentions' => true,
        'disp_meta_comments' => false,
        'before_section_title' => '<div class="clearfix"></div><h3 class="evo_comment__list_title">',
        'after_section_title' => '</h3>',
    ], $params));
    // Note: You can customize the default item feedback by copying the generic
    // /skins/_item_feedback.inc.php file into the current skin folder.
    // ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
}
?>

	<?php
if (evo_version_compare($app_version, '6.7') >= 0) {	// We are running at least b2evo 6.7, so we can include this file:
    // ------------------ INTERNAL COMMENTS INCLUDED HERE ------------------
    skin_include('_item_meta_comments.inc.php', [
        'comment_start' => '<article class="evo_comment evo_comment__meta panel panel-default">',
        'comment_end' => '</article>',
        'comment_post_display' => false,	// Do we want ot display the title of the post we're referring to?
        'comment_post_before' => '<h3 class="evo_comment_post_title">',
        'comment_post_after' => '</h3>',
        'comment_title_before' => '<div class="panel-heading"><h4 class="evo_comment_title panel-title">',
        'comment_title_after' => '</h4></div><div class="panel-body">',
        'comment_avatar_before' => '<span class="evo_comment_avatar">',
        'comment_avatar_after' => '</span>',
        'comment_rating_before' => '<div class="evo_comment_rating">',
        'comment_rating_after' => '</div>',
        'comment_text_before' => '<div class="evo_comment_text">',
        'comment_text_after' => '</div>',
        'comment_info_before' => '<footer class="evo_comment_footer clear text-muted"><small>',
        'comment_info_after' => '</small></footer></div>',
    ]);
    // ---------------------- END OF INTERNAL COMMENTS ---------------------
}
?>

	<?php
    locale_restore_previous();	// Restore previous locale (Blog locale)
?>
</article>

<?php echo '</div>'; // End of post display?>
