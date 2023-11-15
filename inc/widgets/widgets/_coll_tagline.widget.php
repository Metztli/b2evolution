<?php
/**
 * This file implements the xyz Widget class.
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
 * ComponentWidget Class
 *
 * A ComponentWidget is a displayable entity that can be placed into a Container on a web page.
 *
 * @package evocore
 */
class coll_tagline_Widget extends ComponentWidget
{
    public $icon = 'window-minimize';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct($db_row, 'core', 'coll_tagline');
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('collection-tagline-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('Tagline');
    }

    /**
     * Get a very short desc. Used in the widget list.
     */
    public function get_short_desc()
    {
        global $Collection, $Blog;

        return $Blog->dget('tagline', 'htmlbody');
    }

    /**
     * Get short description
     */
    public function get_desc()
    {
        global $Collection, $Blog;
        return sprintf(
            T_('&laquo;%s&raquo; from the blog\'s <a %s>general settings</a>.'),
            '<strong>' . $Blog->dget('tagline') . '</strong>',
            'href="?ctrl=coll_settings&tab=general&blog=' . $Blog->ID . '"'
        );
    }

    /**
     * Display the widget!
     *
     * @param array MUST contain at least the basic display params
     */
    public function display($params)
    {
        global $Collection, $Blog;

        $this->init_display($params);

        // Collection tagline:
        echo $this->disp_params['block_start'];
        echo $this->disp_params['block_body_start'];
        // TODO: there appears to be no possibility to wrap the tagline in e.g. "<h2>%s</h2>"
        //       Should there be a widget param for this?  fp> probably yes
        $Blog->disp('tagline', 'htmlbody');
        echo $this->disp_params['block_body_end'];
        echo $this->disp_params['block_end'];

        return true;
    }
}
