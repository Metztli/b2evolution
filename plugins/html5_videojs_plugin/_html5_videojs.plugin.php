<?php
/**
 * This file implements the HTML 5 VideoJS Player plugin for b2evolution
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @author fplanque: Francois PLANQUE.
 *
 * @package plugins
 * @version $Id: _html5_videojs.plugin.php 198 2011-11-05 21:34:08Z fplanque $
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * @package plugins
 */
class html5_videojs_plugin extends Plugin
{
    public $code = 'b2evH5VJSP';

    public $name = 'HTML 5 VideoJS Player';

    public $priority = 80;

    public $version = '7.2.5';

    public $group = 'files';

    public $number_of_installs = 1;

    public $allow_ext = ['flv', 'm4v', 'f4v', 'mp4', 'ogv', 'webm'];

    public function PluginInit(&$params)
    {
        $this->short_desc = sprintf(
            T_('Media player for the these file formats: %s. Note: iOS supports only: %s; Android supports only: %s.'),
            implode(', ', $this->allow_ext),
            'mp4',
            'mp4, webm'
        );

        $this->long_desc = $this->short_desc . ' '
            . sprintf(
                T_('This player can display a placeholder image of the same name as the video file with the following extensions: %s.'),
                'jpg, jpeg, png, gif, webp'
            );
    }

    /**
     * Event handler: Called at the beginning of the skin's HTML HEAD section.
     *
     * Use this to add any HTML HEAD lines (like CSS styles or links to resource files (CSS, JavaScript, ..)).
     *
     * @param array Associative array of parameters
     */
    public function SkinBeginHtmlHead(&$params)
    {
        global $Collection, $Blog;

        require_css('#videojs_css#', 'blog');
        require_js_async('#videojs#', 'blog');
        $this->require_skin();

        add_css_headline('.video-js{ max-width: 100% !important; margin: auto; }
.videojs_block {
	margin: 0 auto 1em;
}
.videojs_block .videojs_text {
	font-size: 84%;
	text-align: center;
	margin: 4px 0;
}');
    }

    /**
     * Event handler: Called when ending the admin html head section.
     *
     * @param array Associative array of parameters
     * @return boolean did we do something?
     */
    public function AdminEndHtmlHead(&$params)
    {
        $this->SkinBeginHtmlHead($params);
    }

    /**
     * Define here default collection/blog settings that are to be made available in the backoffice.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::get_coll_setting_definitions()}.
     */
    public function get_coll_setting_definitions(&$params)
    {
        return array_merge(
            parent::get_coll_setting_definitions($params),
            [
                'use_for' => [
                    'label' => T_('Use for'),
                    'type' => 'checklist',
                    'options' => [
                        ['posts', T_('videos attached to posts'), 1],
                        ['comments', T_('videos attached to comments'), 1],
                    ],
                ],
                'skin' => [
                    'label' => T_('Skin'),
                    'type' => 'select',
                    'options' => $this->get_skins_list(),
                    'defaultvalue' => 'vjs-default-skin',
                ],
                'width' => [
                    'label' => T_('Video width (px)'),
                    'note' => T_('100% width if left empty or 0'),
                ],
                'height' => [
                    'label' => T_('Video height (px)'),
                    'type' => 'integer',
                    'allow_empty' => true,
                    'valid_range' => [
                        'min' => 1,
                    ],
                    'note' => T_('auto height if left empty'),
                ],
                'allow_download' => [
                    'label' => T_('Display Download Link'),
                    'note' => T_('Check to display a "Download this video" link under the video.'),
                    'type' => 'checkbox',
                    'defaultvalue' => 0,
                ],
                'disp_caption' => [
                    'label' => T_('Display caption'),
                    'note' => T_('Check to display the video file caption under the video player.'),
                    'type' => 'checkbox',
                    'defaultvalue' => 0,
                ],
            ]
        );
    }

    /**
     * Check a file for correct extension
     *
     * @param File
     * @return boolean true if extension of file supported by plugin
     */
    public function is_flp_video($File)
    {
        return in_array(strtolower($File->get_ext()), $this->allow_ext);
    }

    /**
     * Event handler: Called when displaying item attachment.
     *
     * @param array Associative array of parameters. $params['File'] - attachment, $params['data'] - output
     * @param boolean TRUE - when render in comments
     * @return boolean true if plugin rendered this attachment
     */
    public function RenderItemAttachment(&$params, $in_comments = false)
    {
        $File = $params['File'];

        if (! $this->is_flp_video($File)) { // This file cannot be played with this player
            return false;
        }

        $Item = &$params['Item'];
        $item_Blog = $Item->get_Blog();

        if ((! $in_comments && ! $this->get_checklist_setting('use_for', 'posts', 'coll', $item_Blog)) ||
            ($in_comments && ! $this->get_checklist_setting('use_for', 'comments', 'coll', $item_Blog))) { // Plugin is disabled for post/comment videos on this Blog
            return false;
        }

        if ($File->exists()) {
            /**
             * @var integer A number to assign each video player new id attribute
             */
            global $html5_videojs_number;
            $html5_videojs_number++;

            if ($in_comments) {
                $params['data'] .= '<div style="clear: both; height: 0px; font-size: 0px"></div>';
            }

            /**
             * Video options:
             *
             * controls:  true // The controls option sets whether or not the player has controls that the user can interact with.
             * autoplay:  true // If autoplay is true, the video will start playing as soon as page is loaded (without any interaction from the user). NOT SUPPORTED BY APPLE iOS DEVICES
             * preload:   'auto'|'metadata'|'none' // The preload attribute informs the browser whether or not the video data should begin downloading as soon as the video tag is loaded.
             * poster:    'myPoster.jpg' // The poster attribute sets the image that displays before the video begins playing.
             * loop:      true // The loop attribute causes the video to start over as soon as it ends.
             */
            $video_options = [];
            $video_options['controls'] = true;
            $video_options['preload'] = 'auto';

            // Set a video size:
            $width = trim($this->get_coll_setting('width', $item_Blog));
            $height = trim($this->get_coll_setting('height', $item_Blog));
            if (empty($width) && empty($height)) {	// Use auto width and height:
                $video_options['fluid'] = true;
            } else {	// Use fixed sizes:
                $width = intval($width);
                if (! empty($width)) {	// Use fixed width:
                    $video_options['width'] = $width;
                }
                $height = intval($height);
                if (! empty($height)) {	// Use fixed height:
                    $video_options['height'] = $height;
                }
            }

            if ($placeholder_File = &$Item->get_placeholder_File($File)) { // Display placeholder/poster when image file is linked to the Item with same name as current video File
                $video_placeholder_attr = ' poster="' . $placeholder_File->get_url() . '"';
            } else { // No placeholder for current video File
                $video_placeholder_attr = '';
            }

            $params['data'] .= '<div class="videojs_block">';

            $params['data'] .= '<video id="html5_videojs_' . $html5_videojs_number . '" class="video-js ' . $this->get_coll_setting('skin', $item_Blog) . '" data-setup=\'' . evo_json_encode($video_options) . '\'' . $video_placeholder_attr . '>' .
                '<source src="' . $File->get_url() . '" type="' . $this->get_video_mimetype($File) . '" />' .
                '</video>';

            if ($File->get('desc') != '' && $this->get_coll_setting('disp_caption', $item_Blog)) { // Display caption
                $params['data'] .= '<div class="videojs_text">' . $File->get('desc') . '</div>';
            }

            if ($this->get_coll_setting('allow_download', $item_Blog)) { // Allow to download the video files
                $params['data'] .= '<div class="videojs_text"><a href="' . $File->get_url() . '">' . T_('Download this video') . '</a></div>';
            }

            $params['data'] .= '</div>';

            return true;
        }

        return false;
    }

    /**
     * Event handler: Called when displaying comment attachment.
     *
     * @param array Associative array of parameters. $params['File'] - attachment, $params['data'] - output
     * @return boolean true if plugin rendered this attachment
     */
    public function RenderCommentAttachment(&$params)
    {
        $Comment = &$params['Comment'];
        $params['Item'] = &$Comment->get_Item();

        return $this->RenderItemAttachment($params, true);
    }

    /**
     * Get a list of the skins
     *
     * @return array Skins
     */
    public function get_skins_list()
    {
        $skins_path = dirname($this->classfile_path) . '/skins';

        $skins = [];
        // Set this skin permanently, because it is a default skin name
        $skins['vjs-default-skin'] = 'vjs-default-skin';

        $files = scandir($skins_path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($skins_path . '/' . $file)) {	// Use folder name as skin name
                $skins[$file] = $file;
            }
        }

        return $skins;
    }

    /**
     * Require css file of current skin
     */
    public function require_skin()
    {
        global $Collection, $Blog;

        $skin = $this->get_coll_setting('skin', $Blog);
        if (! empty($skin) && $skin != 'vjs-default-skin') {
            $skins_path = dirname($this->classfile_path) . '/skins';
            if (file_exists($skins_path . '/' . $skin . '/style.min.css')) {	// Require css file only if it exists:
                $this->require_css('skins/' . $skin . '/style.min.css');
            }
        }
    }

    /**
     * Get video mimetype
     *
     * @param object File
     * @return string Mimetype
     */
    public function get_video_mimetype($File)
    {
        switch ($File->get_ext()) {
            case 'flv':
            case 'f4v':
                $mimetype = 'video/flv';
                break;

            case 'm4v':
                $mimetype = 'video/m4v';
                break;

            case 'ogv':
                $mimetype = 'video/ogg';
                break;

            case 'webm':
                $mimetype = 'video/webm';
                break;

            case 'mp4':
            default:
                $mimetype = 'video/mp4';
                break;
        }

        return $mimetype;
    }
}
