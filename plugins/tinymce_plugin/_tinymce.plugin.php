<?php
/**
 * This plugin replaces the textarea in the "Write" tab with {@link http://tinymce.moxiecode.com/ tinyMCE}.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright 2006 by Daniel HAHLER - {@link http://daniel.hahler.de/}.
 * @copyright 2009 by Francois Planque - {@link http://fplanque.com/}.
 *
 * @package plugins
 *
 * @author blueyed: Daniel HAHLER
 * @author fplanque: Francois Planque
 * @author PhiBo: Philipp Seidel (since version 0.6)
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


/**
 * The TinyMCE plugin.
 *
 * It provides replacing edit components with the JavaScript rich text editor TinyMCE.
 *
 * @todo Make sure settings get transformed from 0.6 to 0.7 and obsolete ones get dropped from the DB!
 * @todo dh> use require_js_async() and require_js_defer() and add_js_headline() for the JavaScript includes
 * @todo fp> see bbcode plugin for an example about how to convert [tag] to <tag> on the fly for editing purposes. May be used for [img:] tags in b2evo. May also be used for b2evo smilies display. ed.onBeforeSetContent ed.onPostProcess
 * @todo fp> lang.js files should be moved to the standard language packs. Maybe served by .php files outputting javascript.
 * @todo dh> This is a nice plugin to apply classes and IDs: http://www.bram.us/projects/tinymce-plugins/tinymce-classes-and-ids-plugin-bramus_cssextras/
 * @todo dh> Integrate our Filemanager via http://wiki.moxiecode.com/index.php/TinyMCE:Configuration/file_browser_callback
 */
class tinymce_plugin extends Plugin
{
    public $code = 'evo_TinyMCE';

    public $name = 'TinyMCE';

    public $priority = 10;

    public $version = '7.2.5';

    public $group = 'editor';

    public $number_of_installs = 1;

    public $collection = null;

    public $post_ID = null;

    public $blog_ID = null;

    public $target_type = null;

    public $target_ID = null;

    public $temp_ID = null;

    public function PluginInit(&$params)
    {
        $this->short_desc = $this->T_('Javascript WYSIWYG editor');
    }

    /**
     * Define here default collection/blog settings that are to be made available in the backoffice.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::GetDefaultSettings()}.
     */
    public function get_coll_setting_definitions(&$params)
    {
        //$default_params = array_merge( $params, array( 'default_comment_using' => 'disabled' ) );

        //return parent::get_coll_setting_definitions( $default_params );
        return $this->get_custom_setting_definitions($params);
    }

    /**
     * Define here default custom settings that are to be made available
     *     in the backoffice for collections, private messages and newsletters.
     *
     * @param array Associative array of parameters.
     * @return array See {@link Plugin::get_custom_setting_definitions()}.
     */
    public function get_custom_setting_definitions(&$params)
    {
        return [
            'back_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Back-office'),
            ],
            'coll_use_for_posts' => [
                'label' => T_('Use for posts'),
                'type' => 'checkbox',
                'note' => '',
                'defaultvalue' => 1,
            ],
            'coll_use_for_comments' => [
                'label' => T_('Use for comments'),
                'type' => 'checkbox',
                'note' => '',
                'defaultvalue' => 1,
            ],
            'back_layout_end' => [
                'layout' => 'end_fieldset',
            ],

            'front_layout_start' => [
                'layout' => 'begin_fieldset',
                'label' => T_('Front-office'),
            ],
            'coll_use_for_posts_front' => [
                'label' => T_('Use for posts'),
                'type' => 'checkbox',
                'note' => '',
                'defaultvalue' => 1,
            ],
            'coll_use_for_comments_front' => [
                'label' => T_('Use for comments'),
                'type' => 'checkbox',
                'note' => '',
                'defaultvalue' => 1,
            ],
            'front_layout_end' => [
                'layout' => 'end_fieldset',
            ],
        ];
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
        return [
            'default_use_tinymce' => [
                'label' => $this->T_('Use TinyMCE (Default)'),
                'type' => 'checkbox',
                'defaultvalue' => '1',
                'note' => $this->T_('This is the default, which users can override in their profile.'),
            ],
            'use_gzip_compressor' => [
                'label' => $this->T_('Use compressor'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
                'note' => $this->T_('Use the TinyMCE compressor, which improves loading time.'),
            ],
            /* Ugly
            'tmce_options_begin' => array(
                'label' => $this->T_('Advanced editor options'),
                'layout' => 'begin_fieldset'
            ),
            */

            'tmce_options_contextmenu' => [
                // fp> keep for now
                'label' => $this->T_('Context menu'),
                'type' => 'checkbox',
                'defaultvalue' => 0,
                'note' => $this->T_('Enable this to use an extra context menu in the editor.') . ' <span class="red">' . T_('Enabling this will prevent browser-side spelling correction.') . '</span>',
            ],

            'tmce_options_spellcheck' => [
                // fp> keep for now
                'label' => $this->T_('Browser spell checking'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
                'note' => $this->T_('Enable browser-based spell checking.'),
            ],

            'tmce_options_paste' => [
                // fp> keep for now
                'label' => $this->T_('Advanced paste support'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
                'note' => $this->T_('Enable this to add support for pasting easily word and plain text files'),
            ],
            'tmce_options_directionality' => [
                // keep for now
                'label' => $this->T_('Directionality support'),
                'type' => 'checkbox',
                'defaultvalue' => 1,
                'note' => $this->T_('Enable to add directionality icons to TinyMCE for better handling of right-to-left languages'),
            ],
            /* /Ugly
            'tmce_options_end' => array(
                    'layout' => 'end_fieldset'
            ),
            */
            'tmce_custom_conf' => [
                // fp> over-kill dh> I tend to leave this in, as it allows to configure it as-you-need, especially when a lot of the advanced stuff gets removed from the admin.
                'label' => $this->T_('Custom TinyMCE init'),
                'type' => 'textarea',
                'defaultvalue' => // Provide some sample:
                        'height : 240',
                'note' => sprintf($this->T_('Custom parameters to tinymce.init(). See the <a %s>TinyMCE manual</a>.'), 'href="http://wiki.moxiecode.com/index.php/TinyMCE:Configuration"'),
            ],
        ];
    }

    /**
     * Declare custom events that this plugin fires.
     *
     * The gallery2_plugin uses these.
     *
     * Plugins can set the "load_before_init" parameter with some javascript code
     * that will be executed before tinymce.init() is called. This is most useful
     * for inserting code to load an external tinymce plugin.
     *
     * Supported events are as follows:
     * tinymce_before_init: Allows other b2evo plugins to load tinymce plugins
     *                      before the tinymce init.
     * Example:
     * function tinymce_before_init( &$params ) {
     *   $mypluginurl = \$this->get_plugin_url()."myplugin/plugin.min.js";
     *   echo "tinymce.PluginManager.load('myplugin', '".$mypluginurl."');";
     * }
     *
     * tinymce_extend_plugins: Allows b2evo plugins to extend the plugin list.
     *                         TinyMCE often needs to be told not to load an
     *                         external plugin during it's load phase because it's
     *                         already been loaded. The plugin list is exposed
     *                         in the tinymce_plugins property in the params.
     * Example:
     * function tinymce_extend_plugins( &$params ) {
     *   array_push($params["tinymce_plugins"], "-myplugin");
     * }
     *
     * tinymce_extend_buttons: Allows b2evo plugins to extend the buttons in the
     *                         Third button panel.The buttons list is exposed
     *                         in the tinymce_buttons property in the params.
     * Example:
     * function tinymce_extend_buttons( &$params ) {
     *   array_push($params["tinymce_buttons"], "mypluginbutton");
     * }
     */
    public function GetExtraEvents()
    {
        return [
            "tinymce_before_init" => "Event that is called before tinymce is initialized",
            "tinymce_extend_plugins" => "Event called to allow other plugins to extend the plugin list",
            "tinymce_extend_buttons" => "Event called to allow other plugins to extend the button list",
        ];
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
        $r = [
            'use_tinymce' => [
                'label' => $this->T_('Use TinyMCE'),
                'type' => 'checkbox',
                'defaultvalue' => $this->Settings->get('default_use_tinymce'),
                'note' => $this->T_('Check this to enable the extended Javascript editor (TinyMCE).'),
            ],
        ];

        /* Ugly
        $r['tmce_options_begin'] = array(
                    'label' => $this->T_('Advanced editor options'),
                    'layout' => 'begin_fieldset' // fp> ugly
                );
        */

        $r['tmce_options_contextmenu'] = [
            // fp> keep for now
            'label' => $this->T_('Context menu'),
            'type' => 'checkbox',
            'defaultvalue' => $this->Settings->get('tmce_options_contextmenu'),
            'note' => $this->T_('Enable this to use an extra context menu in the editor.') . ' <span class="red">' . T_('Enabling this will prevent browser-side spelling correction.') . '</span>',
        ];
        $r['tmce_options_spellcheck'] = [
            'label' => $this->T_('Browser spell checking'),
            'type' => 'checkbox',
            'defaultvalue' => 1,
            'note' => $this->T_('Enable browser-based spell checking.'),
        ];
        $r['tmce_options_paste'] = [
            // fp> keep for now
            'label' => $this->T_('Advanced paste support'),
            'type' => 'checkbox',
            'defaultvalue' => $this->Settings->get('tmce_options_paste'),
            'note' => $this->T_('Enable this to add support for easily pasting word and plain text files'),
        ];
        $r['tmce_options_directionality'] = [
            'label' => $this->T_('Directionality support'),
            'type' => 'checkbox',
            'defaultvalue' => $this->Settings->get('tmce_options_directionality'),
            'note' => $this->T_('Enable to add directionality icons to TinyMCE that enables TinyMCE to better handle languages that is written from right to left.'),
        ];

        /* Ugly
        $r['tmce_options_end'] = array(
                    'layout' => 'end_fieldset'
                );
        */

        return $r;
    }

    /**
     * We require b2evo 3.3+
     */
    public function GetDependencies()
    {
        return [
            'requires' => [
                'api_min' => [3, 3], // obsolete, but required for b2evo 1.8 before 1.8.3
                'app_min' => '3.3.0-rc1',
            ],
        ];
    }

    /**
     * Init the TinyMCE object (in backoffice).
     *
     * This is done late, so that scriptaculous has been loaded before,
     * which got used by the youtube_plugin and caused problems with tinymce.
     *
     * @todo dh> use jQuery's document.ready wrapper
     *
     * ---
     *
     * Event handler: Called when displaying editor buttons (in back-office).
     *
     * This method, if implemented, should output the buttons (probably as html INPUT elements)
     * and return true, if button(s) have been displayed.
     *
     * You should provide an unique html ID with each button.
     *
     * @param array Associative array of parameters.
     *   - 'target_type': either 'Comment' or 'Item'.
     *   - 'edit_layout': "inskin", "expert", etc. (users, hackers, plugins, etc. may create their own layouts in addition to these)
     *                    NOTE: Please respect the "inskin" mode, which should display only the most simple things!
     * @return boolean did we display a button?
     */
    public function AdminDisplayEditorButton(&$params)
    {
        global $disable_tinymce_for_frontoffice_comment_form;

        if (empty($params['content_id'])) {	// Value of html attribute "id" of textarea where tinymce is applied
            // Don't allow empty id:
            return false;
        }

        if (empty($params['target_object'])) {	// Target object must be defined:
            return false;
        }

        $params = array_merge([
            'temp_ID' => null,  // Temporary LinkOwnerID
        ], $params);

        switch ($params['target_type']) {
            case 'Item':
                // Initialize settings for item:
                global $Collection, $Blog;

                $this->collection = $Blog->get('urlname');
                $edited_Item = &$params['target_object'];
                $this->target_type = 'Item';
                $this->target_ID = $edited_Item->ID;
                $this->temp_ID = $params['temp_ID'];

                if (! $edited_Item->get_type_setting('allow_html')) {	// Only when HTML is allowed in post:
                    return false;
                }

                if ($edited_Item->get_type_setting('use_text') == 'never') {	// Only when text is allowed for current item type:
                    return false;
                }

                $item_Blog = &$edited_Item->get_Blog();

                if ($params['edit_layout'] == 'inskin') {	// Front-office:
                    if (! $this->get_coll_setting('coll_use_for_posts_front', $item_Blog)) {
                        return false;
                    }
                } else {
                    if (! $this->get_coll_setting('coll_use_for_posts', $item_Blog)) {	// This plugin is disabled to use for posts:
                        return false;
                    }
                }

                $state_params = [
                    'type' => $params['target_type'],
                    'blog' => $Blog->ID,
                    'item' => $edited_Item->ID,
                ];
                break;

            case 'EmailCampaign':
                // Initialize settings for email campaign:
                $edited_EmailCampaign = &$params['target_object'];
                $this->target_type = 'EmailCampaign';
                $this->target_ID = $edited_EmailCampaign->ID;

                $state_params = [
                    'type' => $params['target_type'],
                    'email' => $edited_EmailCampaign->ID,
                ];
                break;

            case 'Comment':
                if (! is_admin_page() && $disable_tinymce_for_frontoffice_comment_form) {	// Disable TinyMCE until JS can be fixed to defer load:
                    return false;
                }

                // Initialize settings for item:
                global $Collection, $Blog;

                $edited_Comment = &$params['target_object'];
                $edited_Item = &$edited_Comment->get_Item();
                $this->target_type = 'Comment';
                $this->target_ID = $edited_Comment->ID;
                $this->temp_ID = $params['temp_ID'];

                if (! empty($Blog) && ! $Blog->get_setting('allow_html_comment')) {	// Only when HTML is allowed in comment:
                    return false;
                }

                $item_Blog = &$edited_Item->get_Blog();

                if ($edited_Comment->is_meta()) {	// Do not use TinyMCE for internal comments, never!
                    return false;
                }

                if ($params['edit_layout'] == 'inskin') {	// Front-office:
                    if (! $this->get_coll_setting('coll_use_for_comments_front', $item_Blog)) {
                        return false;
                    }
                } else {
                    if (! $this->get_coll_setting('coll_use_for_comments', $item_Blog)) {	// This plugin is disabled to use for comments:
                        return false;
                    }
                }

                // Currently shares the same editor state as Item above:
                $state_params = [
                    'type' => $params['target_type'],
                    'blog' => $Blog->ID,
                    'item' => $edited_Item->ID,
                ];
                break;

            case 'Message':
                // Initialize settings for email campaign:
                global $Settings;

                $edited_Message = &$params['target_object'];
                $this->target_type = 'Message';
                $this->target_ID = empty($edited_Message) ? null : $edited_Message->ID;
                $this->temp_ID = $params['temp_ID'];

                if (! $Settings->get('allow_html_message')) {	// Only when HTML is allowed for messages:
                    return false;
                }

                $state_params = [
                    'type' => $params['target_type'],
                    'message' => empty($edited_Message) ? null : $edited_Message->ID,
                ];
                break;

            default:
                // Don't allow this plugin for another things:
                return false;
        }

        // JS config:
        $tinymce_config = [
            'content_id' => $params['content_id'],
            'plugin_code' => $this->code,
        ];

        switch ($params['edit_layout']) {
            default:
                // Get init params, depending on edit mode: simple|expert
                $tmce_init = $this->get_tmce_init($params['edit_layout'], $params['content_id'], $params['target_type']);

                $toggle_editor_config = [
                    'save_state_html_url' => $this->get_htsrv_url('save_editor_state', array_merge($state_params, [
                        'on' => 0,
                    ]), '&'),
                    'save_state_wysiwyg_url' => $this->get_htsrv_url('save_editor_state', array_merge($state_params, [
                        'on' => 1,
                    ]), '&'),
                ];
                $tinymce_config['toggle_editor'] = $toggle_editor_config;
                ?>

				<div class="btn-group evo_tinymce_toggle_buttons" data-content-id="<?php echo format_to_output($params['content_id'], 'htmlattr'); ?>">
					<input id="tinymce_plugin_toggle_button_html" type="button" value="<?php echo format_to_output($this->T_('Markup'), 'htmlattr'); ?>" class="btn btn-default active" disabled="disabled"
						title="<?php echo format_to_output($this->T_('Toggle to the markup/pro editor.'), 'htmlattr'); ?>" />
					<input id="tinymce_plugin_toggle_button_wysiwyg" type="button" value="WYSIWYG" class="btn btn-default"
						title="<?php echo format_to_output($this->T_('Toggle to the WYSIWYG editor.'), 'htmlattr'); ?>" />
				</div>

				<?php
                // Load TinyMCE Javascript source file:
                require_js_defer('#tinymce#', 'blog', true);
                require_js_defer('#tinymce_jquery#', 'blog', true);
                $this->require_js_defer('js/evo_init_plugin_tinymce.js', true);
                $this->require_js_defer('js/evo_view_shortcodes.bmin.js', true);

                $use_tinymce = $this->get_editor_state($state_params);

                $tinymce_init_config = [
                    'use_tinymce' => $use_tinymce,
                    'tmce_init' => $tmce_init,
                    'display_error_msg' => sprintf($this->T_('TinyMCE javascript could not be loaded. Check the "%s" plugin setting.'), $this->T_('URL to TinyMCE')),
                    'update_content_url' => $this->get_htsrv_url('convert_content_to_wysiwyg', [], '&'),
                    'crumb_tinymce' => get_crumb('tinymce'),
                ];
                $tinymce_config['editor'] = $tinymce_init_config;

                if ($use_tinymce) {	// User used MCE last time, load MCE on document.ready:
                    $editor_code = $this->code;
                }

                // By default set the editor code to an empty string
                echo '<input type="hidden" name="editor_code" value="">';

                if (is_ajax_request()) {
                    ?>
					<script>
					jQuery( document ).ready( function() {
							evo_init_tinymce( <?php echo evo_json_encode($tinymce_config); ?> );
						} );
					</script>
					<?php
                } else {
                    expose_var_to_js('tinymce_' . $params['content_id'], $tinymce_config, 'evo_tinymce_config');
                }

                // We also want to save the 'last used/not-used' state: (if no NULLs, this won't change anything)
                $this->htsrv_save_editor_state(array_merge($state_params, [
                    'on' => $use_tinymce,
                ]));

                return true;
        }
    }

    /**
     * Init the TinyMCE object (in front office).
     *
     * Event handler: Called when displaying editor buttons (in front-office).
     *
     * This method, if implemented, should output the buttons (probably as html INPUT elements)
     * and return true, if button(s) have been displayed.
     *
     * You should provide an unique html ID with each button.
     *
     * @param array Associative array of parameters.
     *   - 'target_type': either 'Comment' or 'Item'.
     *   - 'edit_layout': "inskin", "expert", etc. (users, hackers, plugins, etc. may create their own layouts in addition to these)
     *                    NOTE: Please respect the "inskin" mode, which should display only the most simple things!
     * @return boolean did we display a button?
     */
    public function DisplayEditorButton(&$params)
    {
        return $this->AdminDisplayEditorButton($params);
    }

    /* PRIVATE */
    /**
     * Create Options for TinyMCE.init() (non-compressor) - not TinyMCE_GZ.init (compressor)!!
     *
     * @todo fp> valid_elements to try to generate less validation errors
     *
     * @param string simple|expert
     * @param string ID of the edited content (value of html attribure "id")
     * @param string Item | EmailCampaign | Comment
     * @return string|false
     */
    public function get_tmce_init($edit_layout, $content_id, $target_type)
    {
        global $Collection, $Blog;
        global $Plugins;
        global $localtimenow, $debug, $rsc_url, $rsc_path, $skins_path, $skins_url;
        global $UserSettings;
        global $ReqHost;

        global $baseurl;

        // Get URL of TinyMCE JS files:
        $tiny_mce_js_files_url = (is_admin_page() || empty($Blog) ? $rsc_url : $Blog->get_local_rsc_url()) . 'ext/tiny_mce/';

        $tmce_plugins_array = [
            'image',
            'importcss',
            'link',
            'pagebreak',
            'morebreak',
            'textcolor',
            'media',
            'nonbreaking',
            'charmap',
            'fullscreen',
            'table',
            'searchreplace',
            'autocomplete',
            'lists',
            'advlist',
            'evo_view',
            //'b2evo_shorttags',
            //'b2evo_attachments'
        ];

        if (function_exists('enchant_broker_init')) { // Requires Enchant spelling library
            $tmce_plugins_array[] = 'spellchecker';
        }

        $tmce_theme_advanced_buttons1_array = [];
        $tmce_theme_advanced_buttons2_array = [];
        $tmce_theme_advanced_buttons3_array = [];
        $tmce_theme_advanced_buttons4_array = [];

        if ($UserSettings->get('control_form_abortions')) {	// Activate bozo validator: autosave plugin in TinyMCE
            $tmce_plugins_array[] = 'autosave';
        }

        if ($this->UserSettings->get('tmce_options_contextmenu') == 1) {
            $tmce_plugins_array[] = 'contextmenu';
        }

        /* ----------- button row 1 : paragraph related styles ------------ */
        $tmce_theme_advanced_buttons1_array = [
            'formatselect',
            'alignleft aligncenter alignright alignjustify',
            'bullist numlist',
            'outdent indent',
        ];
        /* ----------- button row 2 : font related styles ------------ */
        $tmce_theme_advanced_buttons2_array = [
            'bold italic strikethrough forecolor backcolor',
            'subscript superscript',
            'fontselect fontsizeselect',
            'removeformat',
        ];
        /* ----------- button row 3 : tools + insert buttons ------------ */
        $image_media_buttons = (($target_type == 'Comment') && empty($this->target_ID) ? 'image media' : 'evo_image image media');
        $tmce_theme_advanced_buttons3_array = [
            'undo redo',
            'searchreplace',
            'fullscreen',

            $image_media_buttons,   // can evo_image work in front office?
            'link unlink',
            'nonbreaking charmap',
            'table',
        ];

        if ($target_type == 'Item') {
            $tmce_theme_advanced_buttons3_array[] = 'morebreak pagebreak';
        }

        if ($edit_layout != 'inskin') { // Additional toolbar for BACK-OFFICE only:
            /* ----------- button row 4 ------------ */
            $tmce_plugins_array[] = 'visualchars';
            $tmce_theme_advanced_buttons4_array = [
                'visualchars',
            ];

            if ($this->UserSettings->get('tmce_options_directionality') == 1) {
                $tmce_plugins_array[] = 'directionality';
                array_push($tmce_theme_advanced_buttons4_array, 'ltr rtl');
            }

            if ($this->UserSettings->get('tmce_options_paste') == 1) {
                $tmce_plugins_array[] = 'paste';
                $tmce_theme_advanced_buttons4_array[] = 'pastetext';
            }

            if (function_exists('enchant_broker_init')) { // Requires Enchant spelling library
                $tmce_theme_advanced_buttons4_array[] = 'spellchecker';
            }

            $tmce_plugins_array[] = 'code';
            $tmce_theme_advanced_buttons4_array[] = 'code';

            $tmce_theme_advanced_buttons4_array =
                $Plugins->get_trigger_event(
                    "tinymce_extend_buttons",
                    [
                        "tinymce_buttons" => $tmce_theme_advanced_buttons4_array,
                    ],
                    "tinymce_buttons"
                );
        }

        $tmce_theme_advanced_buttons1 = implode(' | ', $tmce_theme_advanced_buttons1_array);
        $tmce_theme_advanced_buttons2 = implode(' | ', $tmce_theme_advanced_buttons2_array);
        $tmce_theme_advanced_buttons3 = implode(' | ', $tmce_theme_advanced_buttons3_array);
        $tmce_theme_advanced_buttons4 = implode(' | ', $tmce_theme_advanced_buttons4_array);

        // PLUGIN EXTENSIONS:
        $tmce_plugins_array =
            $Plugins->get_trigger_event(
                "tinymce_extend_plugins",
                [
                    "tinymce_plugins" => $tmce_plugins_array,
                ],
                "tinymce_plugins"
            );

        $tmce_plugins = implode(',', $tmce_plugins_array);

        global $current_locale, $plugins_path;
        $tmce_language = substr($current_locale, 0, 2);
        // waltercruz> Fallback to english if there's no tinymce equivalent to the user locale
        // to avoid some strange screens like http://www.flickr.com/photos/waltercruz/3390729964/
        $lang_path = $rsc_path . 'ext/tiny_mce/langs/' . $tmce_language . '.js';
        if (! file_exists($lang_path)) {
            $tmce_language = 'en';
        }

        // Configuration: -- http://wiki.moxiecode.com/index.php/TinyMCE:Configuration
        $init_options = [];

        $init_options['blog_ID'] = ! empty($Blog) ? $Blog->ID : null;
        $init_options['cache_suffix'] = '?v=' . $this->version;
        $init_options['selector'] = 'textarea#' . $content_id;

        if ($this->Settings->get('use_gzip_compressor')) {	// Load script to use gzip compressor:
            $init_options['script_url'] = get_require_url('ext:tiny_mce/tinymce.gzip.php', 'blog', 'js');
        }

        // B2evo plugin options
        $init_options['collection'] = $this->collection;
        $insert_inline_modal_params = [
            'request_from' => is_admin_page() ? 'back' : 'front',
        ];

        $init_options['target_ID'] = isset($this->target_ID) ? $this->target_ID : null;
        $insert_inline_modal_params['target_ID'] = isset($this->target_ID) ? $this->target_ID : null;

        $init_options['temp_ID'] = isset($this->temp_ID) ? $this->temp_ID : null;

        $init_options['target_type'] = isset($this->target_type) ? $this->target_type : null;
        $insert_inline_modal_params['target_type'] = isset($this->target_type) ? $this->target_type : null;

        $init_options['rest_url'] = get_htsrv_url() . 'rest.php';
        $init_options['anon_async_url'] = get_htsrv_url() . 'anon_async.php';

        if ($Blog) {
            $insert_inline_modal_params['blog'] = $Blog->ID;
        }
        if (isset($this->temp_ID)) {
            $insert_inline_modal_params['temp_ID'] = $this->temp_ID;
        }

        $init_options['modal_url'] = $this->get_htsrv_url('insert_inline', $insert_inline_modal_params, '&');

        $init_options['fontsize_formats'] = '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt';

        $init_options['theme'] = 'modern';
        $init_options['menubar'] = false;

        $init_options['plugins'] = $tmce_plugins;
        $init_options['external_plugins'] = [
            'morebreak' => $tiny_mce_js_files_url . 'plugins/morebreak/plugin.min.js',
        ];
        $init_options['morebreak_separator'] = '[teaserbreak]';
        $init_options['pagebreak_separator'] = '[pagebreak]';

        // Toolbars:
        $init_options['toolbar1'] = $tmce_theme_advanced_buttons1;
        $init_options['toolbar2'] = $tmce_theme_advanced_buttons2;
        $init_options['toolbar3'] = $tmce_theme_advanced_buttons3;
        $init_options['toolbar4'] = $tmce_theme_advanced_buttons4;

        // Context menu:
        if ($this->Settings->get('tmce_options_contextmenu') == 1) {
            $init_options['contextmenu'] = 'cut copy paste | link image | inserttable';
        }

        if ($this->Settings->get('tmce_options_spellcheck') == 1) {
            $init_options['browser_spellcheck'] = true;
        } else {
            $init_options['browser_spellcheck'] = false;
        }

        // UI options:
        $init_options['block_formats'] = 'Paragraph=p;Preformatted=pre;Block Quote=blockquote;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Address=address;Definition Term=dt;Definition Description=dd;DIV=div';
        $init_options['resize'] = true;
        $init_options['language'] = $tmce_language;
        $init_options['language_url'] = $tiny_mce_js_files_url . 'langs/' . $tmce_language . '.js';

        if (function_exists('enchant_broker_init')) { // Requires Enchant spelling library
            $init_options['spellchecker_rpc_url'] = 'spellchecker.php';
        }
        // body_class : "my_class"
        // CSS used in the iframe/editable area: -- http://wiki.moxiecode.com/index.php/TinyMCE:Configuration/content_css
        // note: $version may not be needed below because of automatic suffix? not sure..
        // TODO: we don't want all of basic.css here

        // Prevent object resizing in editor
        $init_options['object_resizing'] = false;

        $init_options['extended_valid_elements'] = 'figure[class],figcaption[class]';

        // Options below should prevent insertion of <p> for every newline:
        //$init_options['force_p_newlines'] = false';
        //$init_options['forced_root_block'] = '';

        // Content CSS:
        $content_css = [];
        if (! empty($Blog)) {	// Load the appropriate ITEM/POST styles depending on the blog's skin:
            // Note: we are not aiming for perfect wysiwyg (too heavy), just for a relevant look & feel.
            $blog_skin_ID = $Blog->get_skin_ID();
            if (! empty($blog_skin_ID)) {
                $SkinCache = &get_SkinCache();
                /**
                 * @var Skin
                 */
                $Skin = $SkinCache->get_by_ID($blog_skin_ID);
                $item_css_path = $skins_path . $Skin->folder . '/style.css';
                $item_css_url = $skins_url . $Skin->folder . '/style.min.css';
                // else: $item_css_url = $rsc_url.'css/item_base.css';
                if (file_exists($item_css_path)) {
                    $content_css[] = $item_css_url;		// fp> TODO: this needs to be a param... "of course" -- if none: else item_default.css ?
                }

                // Load b2evo base css
                $content_css[] = $baseurl . 'rsc/build/bootstrap-b2evo_base.bmin.css';
            }
            // else item_default.css -- is it still possible to have no skin ?
        }

        // Load the content css files from 3rd party code, e.g. other plugins:
        global $tinymce_content_css, $app_version_long;

        $tinymce_content_css[] = get_require_url($this->get_plugin_url() . 'evo_view.css', 'absolute', 'css', $this->version . '+' . $app_version_long);
        $tinymce_content_css[] = get_require_url($this->get_plugin_url() . 'editor.css', 'absolute', 'css', $this->version . '+' . $app_version_long);

        if (is_array($tinymce_content_css) && count($tinymce_content_css)) {
            $content_css = implode(',', array_merge($content_css, $tinymce_content_css));
        }

        $init_options['content_css'] = $content_css;

        // Generated HTML code options:
        // Do not make the path relative to "document_base_url":
        //$init_options[] = 'relative_urls : false';
        $init_options['relative_urls'] = false;

        // Do not convert absolute urls to relative if url domain is the same as current page,
        // (we should keep urls as they were entered manually, because urls can be broken if collection has different domain than back-office; also an issue with RSS feeds):
        $init_options['convert_urls'] = false;
        $init_options['entity_encoding'] = 'raw';

        // Autocomplete options:
        $init_options['autocomplete_options'] = 'window.tinymce_autocomplete_static_options'; // Must be initialize before as string with usernames that are separated by comma
        $init_options['autocomplete_options_url'] = get_restapi_url() . 'users/autocomplete';

        // remove_linebreaks : false,
        // not documented:	auto_cleanup_word : true,

        // Prevent auto generated <p> that wrap around the views
        //$init_options['forced_root_block'] = '';

        // Enable advanced tab for images:
        $init_options['image_advtab'] = true;

        // Disable branding:
        $init_options['branding'] = false;

        // custom conf:
        if ($tmce_custom_conf = $this->Settings->get('tmce_custom_conf')) {
            $tmce_custom_conf = preg_split("/\r\n|\n|\r/", $tmce_custom_conf);
            foreach ($tmce_custom_conf as $row) {
                list($key, $value) = explode(':', $row);
                $init_options[trim($key)] = trim($value);
            }
        }

        return $init_options;
    }


    /**
     * Get URL of file to include as "content_css" for layout and classes in TinyMCE.
     *
     * @return array (path, url)
            /**
     * @var Skin
     *
            $Skin = $SkinCache->get_by_ID( $Blog->skin_ID );
            $item_css_path = $Skin->folder.'/item.css';		// fp> TODO: this needs to be a param... "of course" -- if none: else item_default.css ?
            // else: $item_css_path = 'css/item_base.css';

            $item_css_path = $Skin->folder.'/style.css';

            return array($skins_path.$item_css_path, $skins_url.$item_css_path);
        }
        // else item_default.css -- is it still possible to have no skin ?

        return array(NULL, NULL);
    }
     */

    /**
     * AJAX callback to save editor state (on or off).
     *
     * @param array Params
     */
    public function htsrv_save_editor_state($params)
    {
        if (! isset($params['on'])) {	// Wrong request:
            return;
        }

        switch ($params['type']) {
            case 'Item':
            case 'Comment':
                // Save an edit state for item edit form:

                if (! empty($params['blog'])) {	// This is in order to try & recall a specific state for each blog: (will be used for new posts especially)
                    $this->UserSettings->set('use_tinymce_coll' . intval($params['blog']), intval($params['on']));
                }
                $this->UserSettings->set('use_tinymce', intval($params['on']));
                $this->UserSettings->dbupdate();
                break;

            case 'EmailCampaign':
                // Save an edit state for email campaign edit form:
                $EmailCampaignCache = &get_EmailCampaignCache();
                if ($EmailCampaign = &$EmailCampaignCache->get_by_ID(intval($params['email']), false, false)) {
                    $EmailCampaign->set('use_wysiwyg', intval($params['on']));
                    $EmailCampaign->dbupdate();
                }
                break;
        }
    }

    /**
     * Opens modal to insert inline image tags
     *
     * @param array Params
     */
    public function htsrv_insert_inline($params)
    {
        insert_image_links_block($params);
    }

    /**
     * Get editor state
     *
     * @param array Params
     */
    public function get_editor_state($params)
    {
        switch ($params['type']) {
            case 'Item':
            case 'Comment':
                // Get an edit state for item edit form:

                $ItemCache = &get_ItemCache();
                $Item = &$ItemCache->get_by_ID($params['item'], false, false);

                $item_editor_code = (empty($Item) ? null : $Item->get_setting('editor_code'));

                if (! empty($item_editor_code)) {	// We have a preference for the current post, follow it:
                    // Use tinymce if code matched the code of the current plugin.
                    // fp> Note: this is a temporary solution; in the long term, this will be part of the API and the appropriate plugin will be selected.
                    $editor_state = ($item_editor_code == $this->code);
                } else {	// We have no pref, fall back to whatever current user has last used:
                    // Has the user used MCE last time he edited this particular blog?
                    $editor_state = $this->UserSettings->get('use_tinymce_coll' . $params['blog']);

                    if (is_null($editor_state)) {	// We don't know for this blog, check if he used MCE last time he edited anything:
                        $editor_state = $this->UserSettings->get('use_tinymce');
                    }
                }

                return $editor_state;

            case 'EmailCampaign':
                // Get an edit state for email campaign edit form:
                $EmailCampaignCache = &get_EmailCampaignCache();
                if ($EmailCampaign = &$EmailCampaignCache->get_by_ID(intval($params['email']), false, false)) {
                    return $EmailCampaign->get('use_wysiwyg');
                }
                break;

            case 'Message':
                // Check if MCE was used last time anything was edited:
                return $this->UserSettings->get('use_tinymce');
                break;
        }

        return 0;
    }


    /**
     * HtSrv callback to get the contents of the CSS file configured for "content_css".
     * This gets used when the CSS is not on the same domain and the browser would not
     * allow to handle the CSS cross domain (e.g. FF 3.5).
     *
     * @param array Params passed to the HtSrv call
     *              - "blog": selected blog
     * @return string
     *
    function htsrv_get_item_content_css($params)
    {
        $blog = $params['blog'];
        $BlogCache = get_BlogCache($blog);
        $Collection = $Blog = $BlogCache->get_by_ID($blog);
        $item_css_path_and_url = $this->get_item_css_path_and_url($Blog);
        $path = array_shift( $item_css_path_and_url );
        $r = file_get_contents($path);
        if( $r )
        {
            header('Content-Type: text/css');
            echo $r;
        }
        else
        {
            header('HTTP/1.0 404 Not Found');
        }
        exit;
    }
     */

    /**
     * AJAX callback to convert content for WYSIWYG mode
     *
     * @param array Params
     */
    public function htsrv_convert_content_to_wysiwyg($params)
    {
        global $Plugins, $Session, $debug, $debug_jslog;

        // Check that this action request is not a CSRF hacked request:
        $Session->assert_received_crumb('tinymce');

        // Do not append debug logs to response because
        // here we expect only converted content in WYISWYG edit form:
        $debug = false;
        $debug_jslog = false;

        $content = param('content', 'raw');

        if (isset($Plugins) &&
            ($Plugins instanceof Plugins) &&
            ($autop_Plugin = &$Plugins->get_by_classname('auto_p_plugin')) &&
            method_exists($autop_Plugin, 'render_autop')) {	// Convert new lines to <p> or <br> html tags by installed plugin "Auto P":
            $content = $autop_Plugin->render_autop($content);
        }

        echo $content;
    }

    /**
     * Return the list of Htsrv (HTTP-Services) provided by the plugin.
     *
     * This implements the plugin interface for the list of methods that are valid to
     * get called through htsrv/call_plugin.php.
     *
     * @return array
     */
    public function GetHtsrvMethods()
    {
        return ['save_editor_state', 'insert_inline'/*, 'get_item_content_css'*/, 'convert_content_to_wysiwyg'];
    }

    /**
     * Event handler: Called as action just before updating the {@link Plugin::$Settings plugin's settings}.
     *
     * @return false|null Return false to prevent the settings from being updated to DB.
     */
    public function PluginSettingsUpdateAction()
    {
        if ($this->Settings->get('use_gzip_compressor') == 1) { // Check if the cache folder is not writable
            global $cache_path;
            $cache_folder = $cache_path . 'plugins/tinymce'; // Cache path, this is where the .gz files will be stored

            if (! is_writable($cache_folder)) {
                global $Messages;
                $Messages->add(sprintf(T_('TinyMCE plugin cannot uses the compressor because folder %s is not writable'), '<b>' . $cache_folder . '</b>'), 'note');

                // Disable gzip compressor
                $this->Settings->set('use_gzip_compressor', 0);
            }
        }
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
        global $disp;

        if ($disp == 'edit' || $disp == 'single') {
            $this->require_css('toolbar.css');
        }
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

        if ($ctrl == 'items' || $ctrl == 'comments' || $ctrl == 'campaigns') {
            $this->require_css('toolbar.css');
        }
    }
}

?>
