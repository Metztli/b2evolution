<?php
/**
 * This file implements the Star renderer plugin for b2evolution
 *
 * Star formatting, like [stars:2.3/5]
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package plugins
 *
 * @version $
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * @package plugins
 */
class star_plugin extends Plugin
{
    public $code = 'b2evStar';

    public $name = 'Star renderer';

    public $priority = 55;

    public $version = '7.2.5';

    public $group = 'rendering';

    public $short_desc;

    public $long_desc;

    public $help_topic = 'star-plugin';

    public $number_of_installs = 1;

    /**
     * Init
     */
    public function PluginInit(&$params)
    {
        $this->short_desc = T_('Star formatting e-g [stars:2.3/5]');
        $this->long_desc = T_('This plugin allows to render star ratings inside blog posts and comments by using the syntax [stars:2.3/5] for example');
    }

    /**
     * Define here default email settings that are to be made available in the backoffice.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::GetDefaultSettings()}.
     */
    public function get_email_setting_definitions(&$params)
    {
        // Set empty array to disable this plugin for Email Campaign:
        return [];
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
     * Perform rendering
     *
     * @see Plugin::RenderItemAsHtml()
     */
    public function DisplayItemAsHtml(&$params)
    {
        $params['data'] = $this->render_stars($params['data']);

        return true;
    }

    /**
     * Do the same as for HTML.
     *
     * @see RenderItemAsHtml()
     */
    public function DisplayItemAsXml(&$params)
    {
        return $this->DisplayItemAsHtml($params);
    }

    /**
     * Render stars template from [[stars:3/7]
     *  to <span class="evo_stars_img" style="width:112px">
     *       <i>*</i>
     *       <i>*</i>
     *       <i class="evo_stars_img_empty"><i style="width:50%">%</i></i>
     *       <i class="evo_stars_img_empty">-</i>
     *       <i class="evo_stars_img_empty">-</i>
     *     </span>
     *
     * @param string Source content
     * @return string Rendered content
     */
    public function render_stars($content)
    {
        return replace_outside_code_tags('#\[stars:([\d\.]+)(/\d+)?\]#', [$this, 'get_stars_template'], $content, 'replace_content_callback');
    }

    /**
     * Get HTML template for stars
     *
     * @param array Matches
     * @return string HTML stars
     */
    public function get_stars_template($matches)
    {
        global $b2evo_icons_type;

        if (empty($matches)) { // No stars found
            return;
        }

        $active_stars = $matches[1];

        if (! empty($matches[2])) { // Get a number of stars from content
            $number_stars = intval(substr($matches[2], 1));
        }
        if (empty($number_stars)) { // Use 5 stars by default
            $number_stars = 5;
        }

        return get_star_rating($active_stars, $number_stars);
    }
}
