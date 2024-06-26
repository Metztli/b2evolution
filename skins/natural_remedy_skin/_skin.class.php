<?php
/**
 * This file implements a class derived of the generic Skin class in order to provide custom code for
 * the skin in this folder.
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class natural_remedy_Skin extends Skin
{
    public $version = '1.2.1';

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Natural Remedy';
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'normal';
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
            'colorbox' => [
                'label' => T_('Colorbox Image Zoom'),
                'note' => T_('Check to enable javascript zooming on images (using the colorbox script)'),
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
        parent::display_init();

        // Add CSS:
        require_css('basic_styles.css', 'blog'); // the REAL basic styles
        require_css('basic.css', 'blog'); // Basic styles
        require_css('blog_base.css', 'blog'); // Default styles for the blog navigation
        require_css('item_base.css', 'blog'); // Default styles for the post CONTENT
        require_css('style.css', 'relative');

        // Add custom CSS:
        $custom_css = '';

        // Colorbox (a lightweight Lightbox alternative) allows to zoom on images and do slideshows with groups of images:
        if ($this->get_setting("colorbox")) {
            require_js_helper('colorbox', 'blog');
        }
    }
}
