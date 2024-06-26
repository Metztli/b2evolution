<?php
/**
 * -----------------------------------------------------------------------------------------
 * This file provides a skeleton to create a new {@link http://b2evolution.net/ b2evolution}
 * plugin quickly.
 * See also:
 *  - {@link http://b2evolution.net/man/creating-plugin}
 *  - {@link http://doc.b2evolution.net/stable/plugins/Plugin.html}
 * (Delete this first paragraph, of course)
 * -----------------------------------------------------------------------------------------
 *
 * This file implements the Foo Plugin for {@link http://b2evolution.net/}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2010 by Your NAME - {@link http://example.com/}.
 *
 * @package plugins
 *
 * @author Your NAME
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * Foo Plugin
 *
 * Your description
 *
 * @package plugins
 */
class pluginname_plugin extends Plugin
{
    /**
     * Variables below MUST be overriden by plugin implementations,
     * either in the subclass declaration or in the subclass constructor.
     */
    /**
     * Human readable plugin name.
     */
    public $name = 'Plugin Name';

    /**
     * Code, if this is a renderer or pingback plugin.
     */
    public $code = '';

    public $priority = 50;

    public $version = '0.1-dev';

    public $author = 'http://example.com/';

    public $help_url = '';

    /**
     * Group of the plugin, e.g. "widget", "rendering", "antispam"
     */
    public $group;

    /**
     * Init: This gets called after a plugin has been registered/instantiated.
     */
    public function PluginInit(&$params)
    {
        $this->short_desc = $this->T_('Short description');
        $this->long_desc = $this->T_('Longer description. You may also remove this.');
    }

    /**
     * Define the GLOBAL settings of the plugin here. These can then be edited in the backoffice in System > Plugins.
     *
     * @param array Associative array of parameters (since v1.9).
     *    'for_editing': true, if the settings get queried for editing;
     *                   false, if they get queried for instantiating {@link Plugin::$Settings}.
     * @return array see {@link Plugin::GetDefaultSettings()}.
     * The array to be returned should define the names of the settings as keys (max length is 30 chars)
     * and assign an array with the following keys to them (only 'label' is required):
     */
    public function GetDefaultSettings(&$params)
    {
        return [];
    }

    /**
     * Define the PER-USER settings of the plugin here. These can then be edited by each user.
     *
     * @see Plugin::GetDefaultSettings()
     * @param array Associative array of parameters.
     *    'for_editing': true, if the settings get queried for editing;
     *                   false, if they get queried for instantiating
     * @return array See {@link Plugin::GetDefaultSettings()}.
     */
    public function GetDefaultUserSettings(&$params)
    {
        return [];
    }

    /**
     * Param definitions when added as a widget.
     *
     * Plugins used as widget need to implement the SkinTag hook.
     *
     * @return array
     */
    public function get_widget_param_definitions($params)
    {
        return [];
    }


    // If you use hooks, that are not present in b2evo 1.8, you should also add
    // a GetDependencies() function and require the b2evo version your Plugin needs.
    // See http://doc.b2evolution.net/stable/plugins/Plugin.html#methodGetDependencies


    // Add the methods to hook into here...
    // See http://doc.b2evolution.net/stable/plugins/Plugin.html
}
