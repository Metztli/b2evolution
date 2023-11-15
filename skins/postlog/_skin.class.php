<?php

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class postlog_Skin extends Skin
{
    public $version = '1.1';

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Postlog';
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

        // Make sure standard CSS is called ahead of custom CSS generated below:
        require_css('style.css', 'relative');

        // Colorbox (a lightweight Lightbox alternative) allows to zoom on images and do slideshows with groups of images:
        if ($this->get_setting("colorbox")) {
            require_js_helper('colorbox', 'blog');
        }
    }
}
