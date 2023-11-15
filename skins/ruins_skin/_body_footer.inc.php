<?php
/**
 * This is the BODY footer include template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * This is meant to be included in a page template.
 *
 * @package evoskins
 * @subpackage kubrick
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
?>
<div id="footer">
	<p>
		<?php
            // Display footer text (text can be edited in Blog Settings):
            $Blog->footer_text([
                'before' => '',
                'after' => ' | ',
            ]);
?>

		<?php
// Display a link to contact the owner of this blog (if owner accepts messages):
$Blog->contact_link([
    'before' => '',
    'after' => ' | ',
    'text' => T_('Contact'),
    'title' => T_('Send a message to the owner of this blog...'),
]);
?>

		<a href="http://www.piperenterprises.com/b2evo.html" target="_blank">skin by Lori Piper</a>

		| Credits: <a href="http://www.piperenterprises.com" target="_blank">Web Design</a>
		<?php
// Display additional credits:
// If you can add your own credits without removing the defaults, you'll be very cool :))
// Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
credits([
    'list_start' => ' | ',
    'list_end' => ' ',
    'separator' => '|',
    'item_start' => ' ',
    'item_end' => ' ',
]);
?>
	</p>
</div>
</div>
</div>
