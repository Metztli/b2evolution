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
 * @subpackage postlog
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
?>

<div id="footer">
<span class="footerdummyheader">&nbsp;</span>
<div class="footer-content">
	<?php
        // Display container and contents:
        skin_container(NT_("Footer"), [
            // The following params will be used as defaults for widgets included in this container:
        ]);
// Note: Double quotes have been used around "Footer" only for test purposes.
?>
	<p class="footerline">
		<?php
            // Display footer text (text can be edited in Blog Settings):
        $Blog->footer_text([
            'before' => '',
            'after' => ' &bull; ',
        ]);
?>

		<?php
    // Display a link to contact the owner of this blog (if owner accepts messages):
    $Blog->contact_link([
        'before' => '',
        'after' => ' &bull; ',
        'text' => T_('Contact'),
        'title' => T_('Send a message to the owner of this blog...'),
    ]);
?>

		Design by <a href="http://b2.tilqi.com">Emin &Ouml;zlem</a>&nbsp;aka&nbsp;(<a href="http://tinyurl.com/b2tilqipr">tilqicom</a>) &gt; postlog_skin 0.9.1

	<?php // So called CREDITS(!) REMOVED. RE-ENABLE BY REMOVING THE "/*" and the "*/" if you d like to reactivate
    // Display additional credits:
    // If you can add your own credits without removing the defaults, you'll be very cool :))
    // Please leave this at the bottom of the page to make sure your blog gets listed on b2evolution.net
/*	credits( array(
            'list_start'  => ' &bull; '.T_('Credits').': ',
            'list_end'    => ' ',
            'separator'   => '|',
            'item_start'  => ' ',
            'item_end'    => ' ',
        ) );*/
?>

	</p>
    <div class="shiny_bars">
        <ul>
        <li><a href="http://validator.w3.org/check?uri=referer"><img src="img/footer/vxhtml.gif" alt="validxhtml" title="Valid Xhtml" /></a></li>
        <li><a href="http://jigsaw.w3.org/css-validator/check?uri=referer"><img src="img/footer/vcss.gif" alt="validcss" title="Valid Css" /></a></li>
<?php
echo '<li><a href="http://validator.w3.org/feed/check.cgi?url=' . $Blog->gen_blogurl() . '?tempskin=_rss2"><img src="img/footer/vrss.gif" alt="validrss" title="Valid Feed" /></a></li> '; ?>
         <li><?php
powered_by([
    'block_start' => '',
    'block_end' => '',
    // Check /rsc/img/ for other possible images -- Don't forget to change or remove width & height too
    'img_url' => '$rsc$img/b2evolution_button.png',
    'img_width' => 80,
    'img_height' => 15,
]);
?>  </li>       
                         <li><a href="<?php $Blog->disp('url') ?>"><img src="img/footer/allbrowsers.gif" alt="crossbrowser" title="cross-browser" /></a></li>
                                                  <li><a href="<?php $Blog->disp('url') ?>"><img src="img/footer/anyres.gif" alt="anyres" title="any resolution, fluid" /></a></li>
                </ul></div>
</div>
</div>
 <script type="text/javascript" src="rsc/savecookies.js"></script>
<script type="text/javascript" src="rsc/jquery.cookie.js"></script>

<script type="text/javascript" src="rsc/jquery.mousewheel.js"></script>
<script type="text/javascript" src="rsc/jScrollPane.js"></script>
<script type="text/javascript">
			$(function()
			{				
				$('.bText').jScrollPane({scrollbarWidth:13, scrollbarMargin:10});
			});
		</script>