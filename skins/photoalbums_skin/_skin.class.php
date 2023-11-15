<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 *
 * This file is part of the b2evolution project - {@link http://b2evolution.net/}
 *
 * @package skins
 * @subpackage photoalbums
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class photoalbums_Skin extends Skin
{
    /**
     * Skin version
     * @var string
     */
    public $version = '7.1.2';

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Photo Albums';
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'normal';
    }

    /**
     * Get the container codes of the skin main containers
     *
     * This should NOT be protected. It should be used INSTEAD of file parsing.
     * File parsing should only be used if this function is not defined
     *
     * @return array Array which overrides default containers; Empty array means to use all default containers.
     */
    public function get_declared_containers()
    {
        // Array to override default containers from function get_skin_default_containers():
        // - Key is widget container code;
        // - Value: array( 0 - container name, 1 - container order ),
        //          NULL - means don't use the container, WARNING: it(only empty/without widgets) will be deleted from DB on changing of collection skin or on reload container definitions.
        return [
            'header' => null,
            'front_page_secondary_area' => null,
            'item_list' => null,
            'item_in_list' => null,
            'item_single_header' => null,
            'item_page' => null,
            'sidebar_2' => null,
            'footer' => null,
            'user_profile_left' => null,
            'user_profile_right' => null,
        ];
    }

    /**
     * What evoSkins API does has this skin been designed with?
     *
     * This determines where we get the fallback templates from (skins_fallback_v*)
     * (allows to use new markup in new b2evolution versions)
     */
    public function get_api_version()
    {
        return 5;
    }

    /**
     * Get supported collection kinds.
     *
     * This should be overloaded in skins.
     *
     * For each kind the answer could be:
     * - 'yes' : this skin does support that collection kind (the result will be was is expected)
     * - 'partial' : this skin is not a primary choice for this collection kind (but still produces an output that makes sense)
     * - 'maybe' : this skin has not been tested with this collection kind
     * - 'no' : this skin does not support that collection kind (the result would not be what is expected)
     * There may be more possible answers in the future...
     */
    public function get_supported_coll_kinds()
    {
        $supported_kinds = [
            'main' => 'no',
            'std' => 'no',		// Blog
            'photo' => 'yes',
            'forum' => 'no',
            'manual' => 'no',
            'group' => 'no',  // Tracker
            // Any kind that is not listed should be considered as "maybe" supported
        ];

        return $supported_kinds;
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        // Load to use function get_available_thumb_sizes()
        load_funcs('files/model/_image.funcs.php');

        $r = array_merge([
            'menu_bg_color' => [
                'label' => T_('Menu background color'),
                'defaultvalue' => '#333333',
                'type' => 'color',
            ],
            'menu_text_color' => [
                'label' => T_('Menu text color'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'page_bg_color' => [
                'label' => T_('Page background color'),
                'defaultvalue' => '#666666',
                'type' => 'color',
            ],
            'page_text_color' => [
                'label' => T_('Page text color'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'post_bg_color' => [
                'label' => T_('Post info background color'),
                'defaultvalue' => '#555555',
                'type' => 'color',
            ],
            'post_text_color' => [
                'label' => T_('Post info text color'),
                'defaultvalue' => '#AAAAAA',
                'type' => 'color',
            ],
            'colorbox' => [
                'label' => T_('Colorbox Image Zoom'),
                'note' => T_('Check to enable javascript zooming on images (using the colorbox script)'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_post' => [
                'label' => T_('Voting on Post Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_post_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_comment' => [
                'label' => T_('Voting on Comment Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_comment_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_user' => [
                'label' => T_('Voting on User Images'),
                'note' => T_('Check this to enable AJAX voting buttons in the colorbox zoom view'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'colorbox_vote_user_numbers' => [
                'label' => T_('Display Votes'),
                'note' => T_('Check to display number of likes and dislikes'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'gender_colored' => [
                'label' => T_('Display gender'),
                'note' => T_('Use colored usernames to differentiate men & women.'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
            ],
            'bubbletip' => [
                'label' => T_('Username bubble tips'),
                'note' => T_('Check to enable bubble tips on usernames'),
                'defaultvalue' => 0,
                'type' => 'checkbox',
            ],
            'autocomplete_usernames' => [
                'label' => T_('Autocomplete usernames'),
                'note' => T_('Check to enable auto-completion of usernames entered after a "@" sign in the comment forms'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'banner_public' => [
                'label' => T_('"Public" banner'),
                'note' => T_('Display banner for "Public" posts (posts & comments)'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'posts_thumb_size' => [
                'label' => T_('Thumbnail size in post list'),
                'note' => '',
                'defaultvalue' => 'crop-192x192',
                'options' => get_available_thumb_sizes(),
                'type' => 'select',
            ],
            'single_thumb_size' => [
                'label' => T_('Thumbnail size in single page'),
                'note' => '',
                'defaultvalue' => 'fit-256x256',
                'options' => get_available_thumb_sizes(),
                'type' => 'select',
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    /**
     * Get ready for displaying the skin.
     *
     * This may register some CSS or JS...
     */
    public function display_init()
    {
        // call parent:
        parent::display_init();		// We pass NO params. This gives up the default Skins API v5 behavior.

        // Add custom CSS:
        $custom_css = '';

        // Custom menu styles:
        $custom_styles = [];
        if ($bg_color = $this->get_setting('menu_bg_color')) { // Background color:
            $custom_styles[] = 'background-color: ' . $bg_color;
        }
        if ($text_color = $this->get_setting('menu_text_color')) { // Text color:
            $custom_styles[] = 'color: ' . $text_color;
        }
        if (! empty($custom_styles)) {
            $custom_css .= '	div.pageHeader { ' . implode(';', $custom_styles) . " }\n";
        }

        // Custom page styles:
        $custom_styles = [];
        if ($bg_color = $this->get_setting('page_bg_color')) { // Background color:
            $custom_styles[] = 'background-color: ' . $bg_color;
        }
        if ($text_color = $this->get_setting('page_text_color')) { // Text color:
            $custom_styles[] = 'color: ' . $text_color;
        }
        if (! empty($custom_styles)) {
            $custom_css .= '	body { ' . implode(';', $custom_styles) . " }\n";
        }

        // Custom post area styles:
        $custom_styles = [];
        if ($bg_color = $this->get_setting('post_bg_color')) { // Background color:
            $custom_styles[] = 'background-color: ' . $bg_color;
        }
        if ($text_color = $this->get_setting('post_text_color')) { // Text color:
            $custom_styles[] = 'color: ' . $text_color;
        }
        if (! empty($custom_styles)) {
            $custom_css .= '	div.bDetails { ' . implode(';', $custom_styles) . " }\n";
        }

        global $thumbnail_sizes;
        $posts_thumb_size = $this->get_setting('posts_thumb_size');
        if (isset($thumbnail_sizes[$posts_thumb_size])) {
            // Make the width of image block as fixed to don't expand it by long post title text
            $custom_css .= '	.posts_list .bPost { max-width:' . $thumbnail_sizes[$posts_thumb_size][1] . "px }\n";
            // Set width & height for block with text "No pictures yet"
            $custom_css .= '	.posts_list .bPost b { width:' . ($thumbnail_sizes[$posts_thumb_size][1] - 20) . 'px;'
                . 'height:' . ($thumbnail_sizes[$posts_thumb_size][2] - 20) . 'px' . " }\n";
        }

        $single_thumb_size = $this->get_setting('single_thumb_size');
        if (isset($thumbnail_sizes[$single_thumb_size])) {
            // Make the width of image block as fixed to don't expand it by long post title text
            $custom_css .= '	.post_images .image_block .image_legend { width:' . $thumbnail_sizes[$single_thumb_size][1] . 'px; max-width:' . $thumbnail_sizes[$single_thumb_size][1] . "px }\n";
            // Set width & height for block with text "No pictures yet"
            /*$custom_css .= '	.posts_list .bPost b { width:'.( $thumbnail_sizes[ $single_thumb_size ][1] - 20 ).'px;'
                .'height:'.( $thumbnail_sizes[ $single_thumb_size ][2] - 20 ).'px'." }\n";*/
        }

        if (! empty($custom_css)) {
            $custom_css = '<style type="text/css">
	<!--
' . $custom_css . '	-->
	</style>';
            add_headline($custom_css);
        }
    }

    /**
     * Determine to display status banner or to don't display
     *
     * @param string Status of Item or Comment
     * @return boolean TRUE if we can display status banner for given status
     */
    public function enabled_status_banner($status)
    {
        if ($status != 'published') { // Display status banner everytime when status is not 'published'
            return true;
        }

        if (is_logged_in() && $this->get_setting('banner_public')) { // Also display status banner if status is 'published'
            //   AND current user is logged in
            //   AND this feature is enabled in skin settings
            return true;
        }

        // Don't display status banner
        return false;
    }
}
