<?php
/**
 * This is the template that displays the item block
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2015 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Item, $Skin, $app_version;

// Default params:
$params = array_merge([
    'disp_title' => true,
    'feature_block' => false,
    'content_mode' => 'auto',		// 'auto' will auto select depending on $disp-detail
    'item_class' => 'evo_post',
    'item_type_class' => 'evo_post__ptyp_',
    'item_status_class' => 'evo_post__',
    'item_disp_class' => null,
    'image_class' => 'img-responsive',
    'image_size' => 'fit-1280x720',
    'author_link_text' => 'preferredname',
], $params);

echo '<div id="styled_content_block">'; // Beginning of post display
?>

<div id="<?php $Item->anchor_id() ?>" class="post <?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>">

	<?php
        $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)

if ($params['disp_title'] && $disp != 'single' && $disp != 'page') { // Don't display this on disp=single because there is already title header in h2
    $title_before = '<h2 class="title">';
    $title_after = '</h2>';
    if ($Item->is_intro()) { // Display a link to edit the post only for intro post, because for all other posts it is displayed below under title
        $title_before = '<div class="post_title"><h2 class="title">';
        $title_after = '</h2>' . $Item->get_edit_link([
            'before' => '<div class="' . button_class('group') . '">',
            'after' => '</div>',
            'text' => $Item->is_intro() ? get_icon('edit') . ' ' . T_('Edit Intro') : '#',
            'class' => button_class('text'),
        ]) . '</div>';
    }

    $Item->title([
        'before' => $title_before,
        'after' => $title_after,
        'link_type' => 'permalink',
    ]);
}
?>

	<?php
if (! $Item->is_intro()) { // Don't display these data for intro posts
    ?>
	<div class="small text-muted color-hover">
	<?php
        if ($Item->status != 'published') {
            $Item->status([
                'format' => 'styled',
            ]);
        }
        // Permalink:
        $Item->permanent_link([
            'before' => '<span>',
            'after' => '</span>',
            'text' => '#icon#',
        ]);

    // We want to display the post time:
    $Item->issue_time([
        'before' => '<span class="time"><i class="fa fa-clock-o"></i>',
        'after' => '</span>',
        'time_format' => 'M j, Y',
    ]);



    // Author
    $Item->author([
        'before' => '<span class="author"><i class="fa fa-user"></i>',
        'after' => '</span>',
        'link_text' => $params['author_link_text'],
    ]);

    // Categories
    $Item->categories([
        'before' => '<span class="category"><i class="fa fa-archive"></i> ',
        'after' => '</span>',
        'include_main' => true,
        'include_other' => true,
        'include_external' => true,
        'link_categories' => true,
    ]);

    // Link for editing
    $Item->edit_link([
        'before' => '<span class="edit"><i class="fa fa-pencil"></i> ',
        'after' => '</span>',
        'text' => 'Edit',
    ]);
    ?>
	</div>
	<?php
}
?>

	<?php
if ($disp == 'single') {
    ?>
		<div class="evo_container evo_container__item_single">
		<?php
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
        // Template params for "Item Tags" widget
        'widget_item_tags_before' => '<div class="small">' . T_('Tags') . ': ',
        'widget_item_tags_after' => '</div>',
        // Params for skin file "_item_content.inc.php"
        'widget_item_content_params' => $params,
    ]);
    // ----------------------------- END OF "Item Single" CONTAINER -----------------------------
    ?>
		</div>
		<?php
} else {
    // this will create a <section>
    // ---------------------- POST CONTENT INCLUDED HERE ----------------------
    skin_include('_item_content.inc.php', $params);
    // Note: You can customize the default item content by copying the generic
    // /skins/_item_content.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
    // this will end a </section>


    if (! $Item->is_intro()) { // Don't display these data for intro posts
        // List all tags attached to this post:
        $Item->tags([
            'before' => '<div class="small">' . T_('Tags') . ': ',
            'after' => '</div>',
            'separator' => ', ',
        ]);
    }
    ?>

	<div class="small color-hover">
		<?php
            // Link to comments, trackbacks, etc.:
            $Item->feedback_link([
                'type' => 'comments',
                'link_before' => '<span><i class="fa fa-comments-o"></i>',
                'link_after' => '</span>',
                'link_text_zero' => '#',
                'link_text_one' => '#',
                'link_text_more' => '#',
                'link_title' => '#',
            ]);

    // Link to comments, trackbacks, etc.:
    $Item->feedback_link([
        'type' => 'trackbacks',
        'link_before' => '<span>',
        'link_after' => '</span>',
        'link_text_zero' => '#',
        'link_text_one' => '#',
        'link_text_more' => '#',
        'link_title' => '#',
    ]);
    ?>
	</div>
	<?php } ?>

	<?php
    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', array_merge([
        'before_section_title' => '<div class="clearfix"></div><h2 class="comments_title">',
        'after_section_title' => '</h2>',
        'author_link_text' => $params['author_link_text'],
    ], $params));
// Note: You can customize the default item feedback by copying the generic
// /skins/_item_feedback.inc.php file into the current skin folder.
// ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
?>

	<?php
if (evo_version_compare($app_version, '6.7') >= 0) {	// We are running at least b2evo 6.7, so we can include this file:
    // ------------------ WORKFLOW PROPERTIES INCLUDED HERE ------------------
    skin_include('_item_workflow.inc.php');
    // ---------------------- END OF WORKFLOW PROPERTIES ---------------------
}
?>

	<?php
if (evo_version_compare($app_version, '6.7') >= 0) {	// We are running at least b2evo 6.7, so we can include this file:
    // ------------------ META COMMENTS INCLUDED HERE ------------------
    skin_include('_item_meta_comments.inc.php', [
        'comment_start' => '<article class="evo_comment evo_comment__meta panel panel-default">',
        'comment_end' => '</article>',
    ]);
    // ---------------------- END OF META COMMENTS ---------------------
}
?>

	<?php
    locale_restore_previous();	// Restore previous locale (Blog locale)
?>
</div>
<?php echo '</div>'; // End of post display?>