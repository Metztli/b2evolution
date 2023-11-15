<?php

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
?><!-- =================================== START OF FOOTER =================================== -->
<div id="footer">

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

		Design by <a href="http://hoodiaremedy.com" target="_blank">Diet Pill</a> and Skinned by <a href="http://primeherbal.com" target="_blank">Herbal</a>
		<?php
// Display additional credits:
// If you can add your own credits without removing the defaults, you'll be very cool :))
// Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
credits([
    'list_start' => ' | ' . T_('Credits') . ': ',
    'list_end' => ' ',
    'separator' => '|',
    'item_start' => ' ',
    'item_end' => ' ',
]);
?>
</div>

