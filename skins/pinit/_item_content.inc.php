<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $disp_detail;
global $more;
global $shutdown_count_item_views;
// Default params:
$params = array_merge([
    'content_mode' => 'auto', // Can be 'excerpt', 'normal' or 'full'. 'auto' will auto select depending on backoffice SEO settings for $disp-detail
    'intro_mode' => 'auto', // Same as above. This will typically be forced to "normal" when displaying an intro section so that intro posts always display as normal there
    'force_more' => false, // This will be set to true id 'content_mode' resolves to 'full'.

    'content_display_full' => true, // Do we want to display post content at all, false to display images/attachments only
    'content_start_excerpt' => '<div class="content_inner_wrap content_excerpt">',
    'content_end_excerpt' => '</div>',
    'content_start_full' => '<div class="content_inner_wrap content_full row">',
    'content_end_full' => '</div>',
    'excerpt_before_text' => '<div class="excerpt">',
    'excerpt_after_text' => '</div>',
    'excerpt_before_more' => ' <span class="excerpt_more">',
    'excerpt_after_more' => '</span>',
    'excerpt_more_text' => T_('more') . ' &raquo;',
    'before_content_teaser' => '',
    'after_content_teaser' => '',
    'before_content_extension' => '',
    'after_content_extension' => '',

    'before_images' => '<div class="bImages">',
    'before_image' => '<div class="image_block img-responsive">',
    'before_image_legend' => '<div class="image_legend">',
    'after_image_legend' => '</div>',
    'after_image' => '</div>',
    'after_images' => '</div>',
    'image_size' => 'fit-720x500',
    'image_limit' => 1000,
    'image_link_to' => 'single', // Can be 'original', 'single' or empty
    //	'excerpt_image_size'       => 'fit-80x80',
    'excerpt_image_size' => 'fit-400x320',
    'excerpt_image_limit' => 1,
    'excerpt_image_link_to' => 'single',

    'before_gallery' => '<div class="bGallery">',
    'after_gallery' => '</div>',
    'gallery_image_size' => 'crop-80x80',
    'gallery_image_limit' => 1000,
    'gallery_colls' => 5,
    'gallery_order' => '', // Can be 'ASC', 'DESC', 'RAND' or empty

    'before_url_link' => '<p class="post_link"><i class="fa fa-external-link"></i> ' . T_('Link:') . ' ',
    'after_url_link' => '</p>',
    'url_link_text_template' => '$url$', // If evaluates to empty, nothing will be displayed (except player if podcast)
    'url_link_url_template' => '$url$', // $url$ will be replaced with saved URL address
    'url_link_target' => '', // Link target attribute e.g. '_blank'
    'before_more_link' => '<p class="bMore"><span class="gr-overlay">&nbsp;</span>',
    'after_more_link' => '</p>',
    'more_link_text' => '#',
    'more_link_to' => 'single#anchor', // Can be 'single' or 'single#anchor' which is permalink + "#more55" where 55 is item ID
    'anchor_text' => '<p class="bMore">...</p>', // Text to display as the more anchor (once the more link has been clicked, '#' defaults to "Follow up:")

    'limit_attach' => 1000,
    'attach_list_start' => '<div class="attachments"><h3>' . T_('Attachments') . ':</h3><ul class="bFiles">',
    'attach_list_end' => '</ul></div>',
    'attach_start' => '<li>',
    'attach_end' => '</li>',
    'before_attach_size' => ' <span class="file_size">',
    'after_attach_size' => '</span>',

    'page_links_start' => '<p class="right">' . T_('Pages:') . ' ',
    'page_links_end' => '</p>',
    'page_links_separator' => '&middot; ',
    'page_links_single' => '',
    'page_links_current_page' => '#',
    'page_links_pagelink' => '%d',
    'page_links_url' => '',

    'footer_text_mode' => '#', // 'single', 'xml' or empty. Will detect 'single' from $disp automatically.
    'footer_text_start' => '<div class="item_footer">',
    'footer_text_end' => '</div>',
], $params);

if ($disp_detail == 'single') {
    //echo "test";

    $post_images = $Item->get_images([
        'before' => '',
        'before_image' => '',
        'before_image_legend' => '',
        'after_image_legend' => '',
        'after_image' => '',
        'after' => '',
        'limit' => 1,
        'image_link_to' => 'original',
        'before_gallery' => '',
        'after_gallery' => '',
        // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
        'restrict_to_image_position' => 'teaser',
    ], 'raw');

    if (! empty($post_images)) {
        /*		echo "<pre>";
                var_dump($post_images);
                echo "</pre>";*/
        //		var_dump($post_images);
        /*
                        preg_match('/(src)=("[^"]*")/',$post_images, $img_parts);
                    //	preg_match('/(href)=("[^"]*")/',$post_images, $a_parts);
                        $a_part = preg_replace('#<a.*?>.*?</a>#i', '', $post_images);
                    //	echo "<pre>";
                    //	var_dump($a_part);
                    //	var_dump($img_parts);
                        $postpic = str_replace(array('src="','"'),array('',''),$img_parts[0]);
                    //	var_dump($postpic);
                        $pic_sizes = getimagesize($postpic);
                        $is_horz = ($pic_sizes[0] > $pic_sizes[1] ? true : false);*/

        /*		var_dump($pic_sizes);
            var_dump($postpic);
echo "</pre>";*/
    }


    $params['image_link_to'] = 'original';
    /*	if($is_horz) {
            $params['before_images'] = '<div class="bImages cbinl col-xs-12">';
        }
        else {
            $params['before_images'] = '<div class="bImages cbinl col-sm-6 col-lg-4">';
        }*/
    //$params['before_images'] = '<div class="bImages cbinl col-sm-4 col-lg-4">';
    $params['before_images'] = '<div class="bImages cbinl">';
}


// Determine content mode to use..
if ($Item->is_intro()) {
    $content_mode = $params['intro_mode'];
} else {
    $content_mode = $params['content_mode'];
}
if ($content_mode == 'auto') {
    // echo $disp_detail;
    switch ($disp_detail) {
        case 'posts-cat':
        case 'posts-subcat':
            $content_mode = $Blog->get_setting('chapter_content');
            break;

        case 'posts-tag':
            $content_mode = $Blog->get_setting('tag_content');
            break;

        case 'posts-date':
            $content_mode = $Blog->get_setting('archive_content');
            break;

        case 'posts-filtered':
        case 'search':
            $content_mode = $Blog->get_setting('filtered_content');
            break;

        case 'single':
        case 'page':
            $content_mode = 'full';
            break;

        case 'posts-default':  // home page 1
        case 'posts-next':     // next page 2, 3, etc
        default:
            $content_mode = $Blog->get_setting('main_content');
    }
}

switch ($content_mode) {
    case 'excerpt':
        // Reduced display:
        echo $params['content_start_excerpt'];

        if (! empty($params['excerpt_image_size'])) {
            // Display images that are linked to this post:
            $Item->images([
                'before' => $params['before_images'],
                'before_image' => $params['before_image'],
                'before_image_legend' => $params['before_image_legend'],
                'after_image_legend' => $params['after_image_legend'],
                'after_image' => $params['after_image'],
                'after' => $params['after_images'],
                'image_size' => $params['excerpt_image_size'],
                'limit' => $params['excerpt_image_limit'],
                'image_link_to' => $params['excerpt_image_link_to'],
                'before_gallery' => $params['before_gallery'],
                'after_gallery' => $params['after_gallery'],
                'gallery_image_size' => $params['gallery_image_size'],
                'gallery_image_limit' => $params['gallery_image_limit'],
                'gallery_colls' => $params['gallery_colls'],
                'gallery_order' => $params['gallery_order'],
                'restrict_to_image_position' => 'teaser', // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
            ]);
        }

        $Item->excerpt([
            'before' => $params['excerpt_before_text'],
            'after' => $params['excerpt_after_text'],
            'excerpt_before_more' => $params['excerpt_before_more'],
            'excerpt_after_more' => $params['excerpt_after_more'],
            'excerpt_more_text' => $params['excerpt_more_text'],
        ]);

        echo $params['content_end_excerpt'];
        break;

    case 'full':
        $params['force_more'] = true;
        $params['anchor_text'] = '';
        /* continue down */
        // no break
    case 'normal':
    default:
        // Full dislpay:
        echo $params['content_start_full'];

        // Increment view count of first post on page:
        $shutdown_count_item_views[] = $Item->ID;

        if (! empty($params['image_size'])) {
            // Display featured image (First image in teaser):
            if (! in_array($disp, $Skin->arc_disp_arr)) {
                $params['image_link_to'] = 'original';
            }
            if (in_array($disp, $Skin->arc_disp_arr)) {
                $params['image_limit'] = 1;
            }
            if ($disp !== 'single') {
                $Item->images([
                    'before' => $params['before_images'],
                    'before_image' => $params['before_image'],
                    'before_image_legend' => $params['before_image_legend'],
                    'after_image_legend' => $params['after_image_legend'],
                    'after_image' => $params['after_image'],
                    'after' => $params['after_images'],
                    'image_size' => $params['image_size'],
                    'limit' => $params['image_limit'],
                    'image_link_to' => $params['image_link_to'],
                    'before_gallery' => $params['before_gallery'],
                    'after_gallery' => $params['after_gallery'],
                    'gallery_image_size' => $params['gallery_image_size'],
                    'gallery_image_limit' => $params['gallery_image_limit'],
                    'gallery_colls' => $params['gallery_colls'],
                    'gallery_order' => $params['gallery_order'],
                    // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
                    'restrict_to_image_position' => 'teaser',
                ]);
            } else {
                // Replace original image display with link
                $linktoo = $Item->url;
                if ($Skin->eo_get_featured_image_url()) {
                    $feat_img = '<a class="thumbnail cboxElement cbinl col-sm-12 col-md-5 col-lg-4" rel="lightbox[p' . $Item->ID . ']" href="' . $Skin->eo_get_featured_image_url() . '"><img alt="" class="feat-thumb img-responsive" src="' . $Skin->eo_get_featured_image_url('fit-400x320') . '"></a>';
                    echo $feat_img;
                }
            }
        }

        if ($params['content_display_full']) {
            if ($disp_detail == 'single' || $disp_detail == 'page') { ?>


            
            <?php } ?>
            <?php
            if ($disp_detail == 'single') {
                //	echo '<div class="bText col-sm-8 col-lg-8">';
                echo '<div class="bText padme">';
                ?>
            <h1 class="evo_post_title single-title"><?php
                $Item->title([
    'before' => '<i class="fa fa-bookmark"></i> ',
                    'link_type' => 'permalink',
]);
                ?></h1>
            <?php
            } else {
                echo '<div class="bText padme">';
            }
            ?>
            	   <?php if (in_array($disp, $Skin->arc_disp_arr)) { ?>
        <h2 class="evo_post_title"><?php
            $Item->title([
    'before' => '<i class="fa fa-dot-circle-o"></i> ',
                'link_type' => 'permalink',
]);
            	       ?></h2>
    <?php } ?>		            
    <?php $Item->page_links(); ?>
            <?php
            	           if ($disp_detail == 'single' || $disp_detail == 'page') { ?>
			<?php

            	               // URL link, if the post has one:
            	               $Item->url_link([
                            'before' => $params['before_url_link'],
            	                   'after' => $params['after_url_link'],
            	                   'text_template' => $params['url_link_text_template'],
            	                   'url_template' => $params['url_link_url_template'],
            	                   'target' => $params['url_link_target'],
            	                   'podcast' => '#', // Auto display mp3 player if post type is podcast (=> false, to disable)
                        ]);
            	           }
            	           // Display CONTENT:
            	           $Item->content_teaser([
                        'before' => $params['before_content_teaser'],
            	               'after' => $params['after_content_teaser'],
            	               'before_image' => $params['before_image'],
            	               'before_image_legend' => $params['before_image_legend'],
            	               'after_image_legend' => $params['after_image_legend'],
            	               'after_image' => $params['after_image'],
            	               'image_size' => $params['image_size'],
            	               'limit' => $params['image_limit'],
            	               'image_link_to' => $params['image_link_to'],
                    ]);
            /*
                        $Item->more_link( array(
                                'force_more'  => $params['force_more'],
                                'before'      => $params['before_more_link'],
                                'after'       => $params['after_more_link'],
                                'link_text'   => $params['more_link_text'],
                                'anchor_text' => $params['anchor_text'],
                                'link_to'     => $params['more_link_to'],
                            ) );*/
            if ($disp_detail == 'single') {
                // display images eventhough they are explicitly set to teaser mode. We dont want to leave anything out.
                $Item->images([
                    'before' => '<div class="even_more_images sq_thumbs">',
                    'after' => '</div>',
                    'before_image' => '<span class="image_block thumbnail">',
                    'after_image' => '</span>',
                    'before_image_legend' => $params['before_image_legend'],
                    'after_image_legend' => $params['after_image_legend'],
                    'limit' => $params['image_limit'],
                    'image_link_to' => $params['image_link_to'],
                    'image_size' => 'crop-80x80',
                    'before_gallery' => $params['before_gallery'],
                    'after_gallery' => $params['after_gallery'],
                    'gallery_image_size' => $params['gallery_image_size'],
                    'gallery_image_limit' => $params['gallery_image_limit'],
                    'gallery_colls' => $params['gallery_colls'],
                    'gallery_order' => $params['gallery_order'],
                    // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
                    'restrict_to_image_position' => 'teaser',
                ]);
            }


            if (! empty($params['image_size']) && $more && $Item->has_content_parts($params) /* only if not displayed all images already */) {
                // Display images that are linked to this post:


                $Item->images([
                    'before' => '<div class="more_images">',
                    'before_image' => '<div class="image_block col-sm-4 col-md-3 img-responsive">',
                    'before_image_legend' => $params['before_image_legend'],
                    'after_image_legend' => $params['after_image_legend'],
                    'after_image' => $params['after_image_legend'],
                    'after' => $params['after_images'],
                    'limit' => $params['image_limit'],
                    'image_link_to' => $params['image_link_to'],
                    'before_gallery' => $params['before_gallery'],
                    'after_gallery' => $params['after_gallery'],
                    'gallery_image_size' => $params['gallery_image_size'],
                    'gallery_image_limit' => $params['gallery_image_limit'],
                    'gallery_colls' => $params['gallery_colls'],
                    'gallery_order' => $params['gallery_order'],
                    // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
                    'restrict_to_image_position' => 'aftermore',
                ]);
            }

            $Item->content_extension([
                'before' => $params['before_content_extension'],
                'after' => $params['after_content_extension'],
                'force_more' => $params['force_more'],
            ]);

            // Links to post pages (for multipage posts):
            if (in_array($disp, $Skin->arc_disp_arr)) {
                echo '<span class="gr-overlay">&nbsp;</span>';
            }


            // Display Item footer text (text can be edited in Blog Settings):
            $Item->footer([
                'mode' => $params['footer_text_mode'], // Will detect 'single' from $disp automatically
                'block_start' => $params['footer_text_start'],
                'block_end' => $params['footer_text_end'],
            ]);

            echo '</div>';
        }

        if (! empty($params['limit_attach'])
            && ($more || ! $Item->has_content_parts($params))) {	// Display attachments/files that are linked to this post:
            $Item->files([
                'before' => $params['attach_list_start'],
                'before_attach' => $params['attach_start'],
                'before_attach_size' => $params['before_attach_size'],
                'after_attach_size' => $params['after_attach_size'],
                'after_attach' => $params['attach_end'],
                'after' => $params['attach_list_end'],
                'limit_attach' => $params['limit_attach'],
            ]);
        }

        // Display location info
        if (property_exists($Item, 'location')) {
            $Item->location('<div class="item_location"><strong>' . T_('Location') . ': </strong>', '</div>');
        }

        if ($disp == 'single' && method_exists($Item, 'custom_fields')) {	// Display custom fields
            $Item->custom_fields();
        }

        echo $params['content_end_full'];
}
?>