<?php
/**
 * This file implements the Image Widget class.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('widgets/model/_widget.class.php', 'ComponentWidget');

/**
 * iamge_Widget Class
 *
 * A ComponentWidget is a displayable entity that can be placed into a Container on a web page.
 *
 * @package evocore
 */
class image_Widget extends ComponentWidget
{
    public $icon = 'image';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct($db_row, 'core', 'image');
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('image-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('Image');
    }

    /**
     * Get a very short desc. Used in the widget list.
     *
     * MAY be overriden by core widgets. Example: menu link widget.
     */
    public function get_short_desc()
    {
        $this->load_param_array();
        if (! empty($this->param_array['image_file'])) {
            return $this->param_array['image_file'];
        } else {
            return $this->get_name();
        }
    }

    /**
     * Get short description
     */
    public function get_desc()
    {
        return T_('Display an image of your choice.');
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'image_file_ID' => [
                'label' => T_('Image'),
                'defaultvalue' => '',
                'type' => 'fileselect',
                'thumbnail_size' => 'fit-320x320',
            ],
            'image_source' => [
                'label' => T_('Fallback image source'),
                'note' => '',
                'type' => 'radio',
                'options' => [
                    ['skin', T_('Skin folder')],
                    ['coll', T_('Collection File Root')],
                    ['shared', T_('Shared File Root')]],
                'defaultvalue' => 'skin',
            ],
            'image_file' => [
                'label' => T_('Fallback image filename'),
                'note' => T_('If no file was selected. Relative to the root of the selected source.'),
                'defaultvalue' => 'logo.png',
                'valid_pattern' => [
                    'pattern' => '~^$|^[a-z0-9_\-/][a-z0-9_.\-/]*$~i',
                    'error' => T_('Invalid filename.'),
                ],
                // the following is necessary to catch user input value of "<". Otherwise, "<" and succeeding characters
                // will translate to an empty string and pass the regex pattern below
                'type' => 'html_input',
            ],
            'size_begin_line' => [
                'type' => 'begin_line',
                'label' => T_('Image size'),
                'note' => T_('Leave blank for auto.'),
            ],
            'width' => [
                'note' => '',
                'defaultvalue' => '',
                'allow_empty' => true,
                'size' => 4,
                'valid_pattern' => [
                    'pattern' => '~^(\d+(px|%)?)?$~i',
                    'error' => sprintf(T_('Invalid image size, it must be specified in px or %%.')),
                ],
            ],
            'size_separator' => [
                'label' => ' x ',
                'type' => 'string',
            ],
            'height' => [
                'note' => T_('Leave blank for auto.'),
                'defaultvalue' => '',
                'allow_empty' => true,
                'size' => 4,
                'valid_pattern' => [
                    'pattern' => '~^(\d+(px|%)?)?$~i',
                    'error' => sprintf(T_('Invalid image size, it must be specified in px or %%.')),
                ],
            ],
            'size_end_line' => [
                'type' => 'end_line',
            ],
            'max_size_begin_line' => [
                'type' => 'begin_line',
                'label' => T_('Max size'),
            ],
            'max_width' => [
                'note' => '',
                'defaultvalue' => '',
                'allow_empty' => true,
                'size' => 4,
                'valid_pattern' => [
                    'pattern' => '~^(\d+(px|%)?)?$~i',
                    'error' => sprintf(T_('Invalid max size, it must be specified in px or %%.')),
                ],
            ],
            'max_size_separator' => [
                'label' => ' x ',
                'type' => 'string',
            ],
            'max_height' => [
                'note' => T_('Leave blank for auto.'),
                'defaultvalue' => '',
                'allow_empty' => true,
                'size' => 4,
                'valid_pattern' => [
                    'pattern' => '~^(\d+(px|%)?)?$~i',
                    'error' => sprintf(T_('Invalid max size, it must be specified in px or %%.')),
                ],
            ],
            'max_size_end_line' => [
                'type' => 'end_line',
            ],
            'alt' => [
                'label' => T_('Image Alt text'),
                'note' => '',
                'defaultvalue' => '',
                'size' => 128,
            ],
            'check_file' => [
                'label' => T_('Check file'),
                'note' => T_('Check if file exists. If not, no IMG tag will be created.'),
                'type' => 'checkbox',
                'defaultvalue' => true,
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    /**
     * Display the widget!
     *
     * @param array MUST contain at least the basic display params
     */
    public function display($params)
    {
        global $Collection, $Blog;

        $file_ID = $this->disp_params['image_file_ID'];
        $FileCache = &get_FileCache();
        $File = false;

        if (! empty($file_ID)) {
            $File = &$FileCache->get_by_ID($file_ID, false);
        }

        switch ($this->disp_params['image_source']) {
            case 'skin':
                global $skins_url, $skins_path;
                $skin_folder = $Blog->get_skin_folder();
                $image_url = $skins_url . $skin_folder . '/';
                $image_path = $skins_path . $skin_folder . '/';
                break;

            case 'shared':
                global $media_url, $media_path;
                $image_url = $media_url . 'shared/';
                $image_path = $media_path . 'shared/';
                break;

            case 'coll':
            default:
                $image_url = $Blog->get_media_url();
                $image_path = $Blog->get_media_dir();
                break;
        }

        if (! empty($File) && file_exists($File->get_full_path())) {
            $image_url = $File->get_url();
        } elseif (! empty($this->disp_params['image_file']) && file_exists($image_path . $this->disp_params['image_file'])) {
            $image_url .= $this->disp_params['image_file'];
        } else {
            $image_url = '';
        }

        if ($this->disp_params['check_file'] && empty($image_url)) {	// Logo file doesn't exist, Exit here because of widget setting requires this:
            $this->display_debug_message('Widget "' . $this->get_name() . '" is hidden because there is no image to display.');
            return false;
        }

        $this->init_display($params);

        echo $this->disp_params['block_start'];

        $image_attrs = [
            'src' => $image_url,
            'alt' => $this->disp_params['alt'],
        ];

        // Initialize image attributes:
        // Image width:
        $image_attrs['style'] = 'width:' . (empty($this->disp_params['width']) ? 'auto' : format_to_output($this->disp_params['width'], 'htmlattr')) . ';';
        // Image height:
        $image_attrs['style'] .= 'height:' . (empty($this->disp_params['height']) ? 'auto' : format_to_output($this->disp_params['height'], 'htmlattr')) . ';';
        if (! empty($this->disp_params['max_width'])) {	// Max width:
            $image_attrs['style'] .= 'max-width:' . format_to_output($this->disp_params['max_width'], 'htmlattr') . ';';
        }
        if (! empty($this->disp_params['max_height'])) {	// Max height:
            $image_attrs['style'] .= 'max-height:' . format_to_output($this->disp_params['max_height'], 'htmlattr') . ';';
        }
        // If no unit is specified in a size, consider the unit to be px:
        $image_attrs['style'] = preg_replace('/(\d+);/', '$1px;', $image_attrs['style']);

        // Print out image html tag with link to current collection front page:
        echo '<a href="' . $Blog->get('url') . '"><img' . get_field_attribs_as_string($image_attrs) . ' /></a>';

        echo $this->disp_params['block_end'];

        return true;
    }

    /**
     * Display debug message e-g on designer mode when we need to show widget when nothing to display currently
     *
     * @param string Message
     */
    public function display_debug_message($message = null)
    {
        if ($this->mode == 'designer') {	// Display message on designer mode:
            echo $this->disp_params['block_start'];
            echo $message;
            echo $this->disp_params['block_end'];
        }
    }
}
