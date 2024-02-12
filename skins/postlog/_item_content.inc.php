<?php
/**
 * This is the template that displays the contents for a post
 * (images, teaser, more link, body, etc...)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2009 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

// Default params:
$params = array_merge([
    'before_images' => '<div class="bImages">',
    'before_image' => '<div class="image_block">',
    'before_image_legend' => '<div class="image_legend">',
    'after_image_legend' => '</div>',
    'after_image' => '</div>',
    'after_images' => '</div>',
    'image_size' => 'fit-1200x960',
    'before_url_link' => '<p class="post_link">' . T_('Link:') . ' ',
    'after_url_link' => '</p>',
    'url_link_text_template' => '$url$',
    'force_more' => false,
    'before_more_link' => '<p class="bMore">',
    'after_more_link' => '</p>',
    'more_link_text' => '#',
], (array)$params); // Jose/Metztli IT 02-12-2024 casted second argument $params to array as skin complained of being string.


if (! empty($params['image_size'])) {
    // Display images that are linked to this post:
    $Item->images([
        'before' => $params['before_images'],
        'before_image' => $params['before_image'],
        'before_image_legend' => $params['before_image_legend'],
        'after_image_legend' => $params['after_image_legend'],
        'after_image' => $params['after_image_legend'],
        'after' => $params['after_images'],
        'image_size' => $params['image_size'],
    ]);
}
?>

<div class="bText">
	<?php
        // Increment view count of first post on page:
        $Item->count_view([
            'allow_multiple_counts_per_page' => false,
        ]);

// URL link, if the post has one:
$Item->url_link([
    'before' => $params['before_url_link'],
    'after' => $params['after_url_link'],
    'text_template' => $params['url_link_text_template'],
    'url_template' => '$url$',
    'target' => '',
    'podcast' => '#',        // auto display mp3 player if post type is podcast (=> false, to disable)
]);

// Display CONTENT:
$Item->content_teaser([
    'before' => '',
    'after' => '',
]);
$Item->more_link([
    'force_more' => $params['force_more'],
    'before' => $params['before_more_link'],
    'after' => $params['after_more_link'],
    'link_text' => $params['more_link_text'],
]);
$Item->content_extension([
    'before' => '',
    'after' => '',
    'force_more' => $params['force_more'],
]);

// Links to post pages (for multipage posts):
$Item->page_links('<p class="right">' . T_('Pages:') . ' ', '</p>', ' &middot; ');

// Display Item footer text (text can be edited in Blog Settings):
$Item->footer([
    'mode' => '#',				// Will detect 'single' from $disp automatically
    'block_start' => '<div class="item_footer">',
    'block_end' => '</div>',
]);
?>
</div>

<?php

?>
