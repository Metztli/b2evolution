<?php
/**
 * This file implements the Wide Scroll plugin for b2evolution
 *
 * This is Ron's remix!
 * Includes code from the WordPress team -
 *  http://sourceforge.net/project/memberlist.php?group_id=51422
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package plugins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @package plugins
 */
class widescroll_plugin extends Plugin
{
    public $code = 'evo_widescroll';

    public $name = 'Wide scroll';

    public $priority = 100;

    public $version = '7.2.5';

    public $group = 'rendering';

    public $number_of_installs = 1;

    /**
     * Init
     */
    public function PluginInit(&$params)
    {
        $this->short_desc = T_('Wide scroll');
        $this->long_desc = T_('This plugin allows to horizontally scroll through blocks of wide content.');
    }

    /**
     * Define here default collection/blog settings that are to be made available in the backoffice.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::GetDefaultSettings()}.
     */
    public function get_coll_setting_definitions(&$params)
    {
        $default_params = [
            'default_comment_rendering' => 'never',
        ];

        if (isset($params['blog_type'])) {	// Set the default settings depending on collection type:
            switch ($params['blog_type']) {
                case 'forum':
                case 'manual':
                    $default_params['default_post_rendering'] = 'never';
                    break;
            }
        }

        $tmp_params = array_merge($params, $default_params);
        return parent::get_coll_setting_definitions($tmp_params);
    }

    /**
     * Display Toolbar
     *
     * @param array Params
     */
    public function DisplayCodeToolbar($params = [])
    {
        global $Hit;

        if ($Hit->is_lynx()) { // let's deactivate toolbar on Lynx, because they don't work there.
            return false;
        }

        $params = array_merge([
            'js_prefix' => '', // Use different prefix if you use several toolbars on one page
        ], $params);

        // Load js to work with textarea
        require_js_defer('functions.js', 'blog', true);

        $js_config = [
            'plugin_code' => $this->code,
            'js_prefix' => $params['js_prefix'],
            'btn_title_teaserbreak' => T_('Wide scroll'),
            'toolbar_title' => T_('Wide scroll'),
            'toolbar_title_before' => $this->get_template('toolbar_title_before'),
            'toolbar_title_after' => $this->get_template('toolbar_title_after'),
            'toolbar_group_before' => $this->get_template('toolbar_group_before'),
            'toolbar_group_after' => $this->get_template('toolbar_group_after'),
            'toolbar_button_class' => $this->get_template('toolbar_button_class'),
        ];

        expose_var_to_js('widescroll_toolbar_' . $params['js_prefix'], $js_config, 'evo_init_widescroll_toolbar_config');

        echo $this->get_template('toolbar_before', [
            '$toolbar_class$' => $params['js_prefix'] . $this->code . '_toolbar',
        ]);
        echo $this->get_template('toolbar_after');

        return true;
    }

    /**
     * Event handler: Called when displaying editor toolbars on post/item form.
     *
     * This is for post/item edit forms only. Comments, PMs and emails use different events.
     *
     * @todo dh> This seems to be a lot of Javascript. Please try exporting it in a
     *       (dynamically created) .js src file. Then we could use cache headers
     *       to let the browser cache it.
     * @param array Associative array of parameters
     * @return boolean did we display a toolbar?
     */
    public function AdminDisplayToolbar(&$params)
    {
        $allow_HTML = false;

        if (! empty($params['Item'])) { // Item is set, get Blog from post
            $edited_Item = &$params['Item'];
            $Collection = $Blog = &$edited_Item->get_Blog();
            // We editing an Item, Check if HTML is allowed for the post type:
            $allow_HTML = $edited_Item->get_type_setting('allow_html');
        }

        if (empty($Blog)) { // Item is not set, try global Blog
            global $Collection, $Blog;
            if (empty($Blog)) { // We can't get a Blog, this way "apply_rendering" plugin collection setting is not available
                return false;
            }
        }

        if (! $allow_HTML) {	// Only when HTML is allowed in post
            return false;
        }

        $apply_rendering = $this->get_coll_setting('coll_apply_rendering', $Blog);
        if (empty($apply_rendering) || $apply_rendering == 'never') { // Plugin is not enabled for current case, so don't display a toolbar:
            return false;
        }

        // Append css styles for tinymce editor area
        global $tinymce_content_css, $app_version_long;
        if (empty($tinymce_content_css)) { // Initialize first time
            $tinymce_content_css = [];
        }
        $tinymce_content_css[] = get_require_url($this->get_plugin_url() . 'tinymce_editor.css', 'absolute', 'css', $this->version . '+' . $app_version_long);

        // Print toolbar on screen
        return $this->DisplayCodeToolbar($params);
    }

    /**
     * Event handler: Called when displaying editor toolbars on comment form.
     *
     * @param array Associative array of parameters
     * @return boolean did we display a toolbar?
     */
    public function DisplayCommentToolbar(&$params)
    {
        if (! empty($params['Comment'])) { // Comment is set, get Blog from comment
            $Comment = &$params['Comment'];
            if (! empty($Comment->item_ID)) {
                $comment_Item = &$Comment->get_Item();
                $Collection = $Blog = &$comment_Item->get_Blog();
            }
        }

        if (empty($Blog)) { // Comment is not set, try global Blog
            global $Collection, $Blog;
            if (empty($Blog)) { // We can't get a Blog, this way "apply_comment_rendering" plugin collection setting is not available
                return false;
            }
        }

        if (! $Blog->get_setting('allow_html_comment')) {	// Only when HTML is allowed in comment
            return false;
        }

        $apply_rendering = $this->get_coll_setting('coll_apply_comment_rendering', $Blog);
        if (empty($apply_rendering) || $apply_rendering == 'never') { // Plugin is not enabled for current case, so don't display a toolbar:
            return false;
        }

        // Print toolbar on screen
        return $this->DisplayCodeToolbar($params);
    }

    /**
     * Event handler: Called when displaying editor toolbars.
     *
     * @param array Associative array of parameters
     * @return boolean did we display a toolbar?
     */
    public function DisplayMessageToolbar(&$params)
    {
        global $Settings;

        if (! $Settings->get('allow_html_message')) {	// Only when HTML is allowed in messages
            return false;
        }

        $apply_rendering = $this->get_msg_setting('msg_apply_rendering');
        if (empty($apply_rendering) || $apply_rendering == 'never') {	// Plugin is not enabled for current case, so don't display a toolbar:
            return false;
        }

        // Print toolbar on screen:
        return $this->DisplayCodeToolbar($params);
    }

    /**
     * Event handler: Called when displaying editor toolbars.
     *
     * @param array Associative array of parameters
     * @return boolean did we display a toolbar?
     */
    public function DisplayEmailToolbar(&$params)
    {
        $apply_rendering = $this->get_email_setting('email_apply_rendering');
        if (empty($apply_rendering) || $apply_rendering == 'never') {	// Plugin is not enabled for current case, so don't display a toolbar:
            return false;
        }

        // Print toolbar on screen:
        return $this->DisplayCodeToolbar($params);
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

        if (! isset($Blog) || (
            $this->get_coll_setting('coll_apply_rendering', $Blog) == 'never' &&
            $this->get_coll_setting('coll_apply_comment_rendering', $Blog) == 'never'
        )) { // Don't load css/js files when plugin is not enabled
            return;
        }

        require_js_defer('#jquery#', 'blog');
        $this->require_js_defer('jquery.scrollwide.min.js');
        $this->require_css('jquery.scrollwide.css');
    }

    /**
     * Event handler: Called when ending the admin html head section.
     *
     * @param array Associative array of parameters
     * @return boolean did we do something?
     */
    public function AdminEndHtmlHead(&$params)
    {
        global $ctrl;

        if ($ctrl == 'campaigns' && get_param('tab') == 'send' && $this->get_email_setting('email_apply_rendering')) {	// Load this only on form to preview email campaign:
            require_js_defer('#jquery#', 'blog');
            $this->require_js_defer('jquery.scrollwide.min.js');
            $this->require_css('jquery.scrollwide.css');
        }
    }

    /**
     * Perform rendering
     *
     * @see Plugin::RenderItemAsHtml()
     */
    public function RenderItemAsHtml(&$params)
    {
        return true;
    }
}
