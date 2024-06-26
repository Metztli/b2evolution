<?php
/**
 * This file implements the PLugin settings form.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package admin
 *
 * @author fplanque: Francois PLANQUE.
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * @var Blog
 */
global $Collection, $Blog;

/**
 * @var Plugins
 */
global $Plugins;

global $admin_url;
$plugin_group = param('plugin_group', 'string', 'rendering');

$Form = new Form(null, 'plugin_settings_checkchanges', 'post', 'accordion');

// PluginUserSettings
load_funcs('plugins/_plugin.funcs.php');

if (check_user_perm('options', 'edit', false)) {	// Display this message only if current user has permission to manage the plugins
    echo '<p class="alert alert-info">'
            . sprintf(
                TB_('Here you can configure some plugins individually for each blog. To manage your installed plugins go <a %s>here</a>.'),
                'href="' . $admin_url . '?ctrl=plugins"'
            )
        . '</p>';
}

$Form->begin_form('fform');

$Form->add_crumb('collection');
$Form->hidden_ctrl();
$Form->hidden('tab', 'plugins');
$Form->hidden('action', 'update');
$Form->hidden('blog', $Blog->ID);

echo '<div class="form-group">';
$Form->switch_layout('linespan');
$Form->switch_template_parts([
    'inputstart' => '<div class="control" style="display: inline-block;">',
    'inputend' => '</div>',
    'labelstart' => '<span style="padding-right: 15px;">',
    'labelend' => '</span>',
]);
$Form->select_input_array('plugin_group', $plugin_group, $Plugins->get_plugin_groups(), TB_('Show plugins from group'), []);
echo '</div>';

$Form->switch_layout(null);
?>
<script>
	jQuery( 'select[name="plugin_group"]' ).on( 'change', function()
		{
			window.location.replace( "<?php echo $admin_url . '?ctrl=coll_settings&tab=plugins&blog=' . $Blog->ID . '&plugin_group='; ?>" + $( this ).val() );
		} );
</script>
<?php

$have_plugins = false;
$Plugins->restart();

$Form->begin_group();

while ($loop_Plugin = &$Plugins->get_next()) {
    if ($loop_Plugin->group != $plugin_group) {
        continue;
    }

    // We use output buffers here to display the fieldset only if there's content in there
    ob_start();

    $priority_link = '<a href="' . $loop_Plugin->get_edit_settings_url() . '#ffield_edited_plugin_code">' . $loop_Plugin->priority . '</a>';
    $Form->begin_fieldset($loop_Plugin->name . ' ' . $loop_Plugin->get_help_link('$help_url') . ' <span class="text-muted text-normal">(' . TB_('Priority') . ': ' . $priority_link . ')</span>');

    ob_start();

    $tmp_params = [
        'for_editing' => true,
        'blog_ID' => $Blog->ID,
    ];
    $plugin_settings = $loop_Plugin->get_coll_setting_definitions($tmp_params);
    if (is_array($plugin_settings)) {
        $Form->switch_layout('fieldset');
        foreach ($plugin_settings as $l_name => $l_meta) {
            // Display form field for this setting:
            autoform_display_field($l_name, $l_meta, $Form, 'CollSettings', $loop_Plugin, $Blog);
        }
        $Form->switch_layout(null);
    }

    $has_contents = strlen(ob_get_contents());

    $Form->end_fieldset();

    if ($has_contents) {
        ob_end_flush();
        ob_end_flush();

        $have_plugins = true;
    } else { // No content, discard output buffers:
        ob_end_clean();
        ob_end_clean();
    }
}

$Form->end_group();

if ($have_plugins) {	// End form:
    $Form->end_form([['submit', 'submit', TB_('Save Changes!'), 'SaveButton']]);
} else {	// Display a message:
    echo '<p>', TB_('There are no plugins providing blog-specific settings.'), '</p>';
    $Form->end_form();
}

// Enable JS for fieldset folding:
echo_fieldset_folding_js();
?>