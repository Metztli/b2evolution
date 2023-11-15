<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
?>

<!-- =================================== START OF FOOTER =================================== -->
<div class="foot_zone" id="footer">
	<div class="inner_wrapper container box_wrap">
		<p class="footer_line">
			<?php
                // Display footer text (text can be edited in Blog Settings):
                $Blog->footer_text([
                    'before' => '',
                    'after' => ' | ',
                ]);

// Display a link to contact the owner of this blog (if owner accepts messages):
$Blog->contact_link([
    'before' => '',
    'after' => ' | ',
    'text' => T_('Contact'),
    'title' => T_('Send a message to the owner of this blog...'),
]);
// Display a link to help page:
$Blog->help_link([
    'before' => ' ',
    'after' => '',
    'text' => T_('Help'),
]);
?>
	        <span class="theme_credit"><a target="_blank" href="http://eodepo.com/category/b2/b2-themes/pinit/">Pinit Skin</a> by <a href="http://www.eminozlem.com/" target="_blank" title="Emin &Ouml;zlem">eo</a>
	        <a title="Free Bootstrap Theme for Wordpress and b2evolution" href="https://wordpress.org/themes/bootstrap-ultimate"><img src="<?php echo $Skin->skinuri . '/img/eofeb.png'?>" alt="Bootstrap Theme for Wordpress and b2evolution" /></a>
	        </span>
		</p>
		<p class="baseline2">
			<?php
if ($Skin->get_setting('disp_b2_credits')) {
    // Display additional credits:
    // If you can add your own credits without removing the defaults, you'll be very cool :))
    // Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
    credits([
        'list_start' => '',
        'list_end' => ' ',
        'separator' => '|',
        'item_start' => ' ',
        'item_end' => ' ',
    ]);

    // Please help us promote b2evolution and leave this logo on your blog:
    powered_by([
        'block_start' => '<div class="powered_by">',
        'block_end' => '</div>',
        // Check /rsc/img/ for other possible images -- Don't forget to change or remove width & height too
        'img_url' => '$rsc$img/powered-by-b2evolution-120t.gif',
        'img_width' => 120,
        'img_height' => 32,
    ]);
}
?>
		</p>
	</div>
</div>