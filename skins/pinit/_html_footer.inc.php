<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}
?>
<!-- End of skin_wrapper -->

</div>

<?php
    modules_call_method('SkinEndHtmlBody');

// SkinEndHtmlBody hook -- could be used e.g. by a google_analytics plugin to add the javascript snippet here:
$Plugins->trigger_event('SkinEndHtmlBody');

if (method_exists($Blog, 'disp_setting')) {
    $Blog->disp_setting('footer_includes', 'raw');
}
?>

<script src="<?php echo $Skin->skinuri ?>js/bootstrap.min.js"></script>
<?php if (in_array($disp, $Skin->arc_disp_arr) && $Skin->get_setting('inf_scroll')) { ?>
<script src="<?php echo $Skin->skinuri ?>js/isotope2.js"></script>
<script src="<?php echo $Skin->skinuri ?>js/jquery.infinitescroll.min.js"></script>
<?php } ?>
<?php if (in_array($disp, $Skin->arc_disp_arr) && $Skin->get_setting('inf_scroll') && $Skin->get_setting('inf_scr_mode') == 'manual') {  ?>
<script src="<?php echo $Skin->skinuri ?>js/infscr-manual-trigger.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
	var $container = $('.evo_main_area');
	$container.imagesLoaded(function(){
	  $container.masonry({
		  itemSelector : '.itemp',
		  columnWidth: 1,
		  gutter : 0
		});
		
		 $container.infinitescroll({
        navSelector  : '.pagination-wrap',    // selector for the paged navigation 
        nextSelector : '.pagination .next_link a',  // selector for the NEXT link (to page 2)
        itemSelector : '#main .itemp',     // selector for all items you'll retrieve
		moreSelector : '#load_more',
		behavior: 'twitter',
        loading: {
            finishedMsg: "No more pages to load.",
            img: '<?php echo $Skin->skinuri ?>img/loading.gif'
          }
        },
        // call Isotope as a callback
        function( newElements ) {
	//		$('#infscr-loading').addClass('itemp').appendTo('.evo_main_area');
          $container.masonry( 'appended', $( newElements ) ).masonry('layout'); 
        }
      );
	});
		jQuery(window).unbind(".infscr"); 
	//	jQuery("#da_pagination").hide();
		jQuery("a#load_more").click(function(){
			$container.trigger("retrieve.infscr");
			return false;
		});


});
</script>
<?php } ?>

<?php if (in_array($disp, $Skin->arc_disp_arr) && $Skin->get_setting('inf_scroll') && $Skin->get_setting('inf_scr_mode') == 'auto') {  ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	
	var $container = $('.evo_main_area');
	$container.imagesLoaded(function(){
	  $container.masonry({
		  itemSelector : '.itemp',
		  columnWidth: 1,
		  gutter : 0
		});
		
		 $container.infinitescroll({
        navSelector  : '.pagination-wrap',    // selector for the paged navigation 
        nextSelector : '.pagination .next_link a',  // selector for the NEXT link (to page 2)
        itemSelector : '#main .itemp',     // selector for all items you'll retrieve
        loading: {
            finishedMsg: 'No more pages to load.',
		//	loadingText: '<table id="twrapper"><tr><td><img src="http://www.infinite-scroll.com/loading.gif" alt="" /></td></tr></table>',
            img: '<?php echo $Skin->skinuri ?>img/loading.gif'
          }
        },
        // call Isotope as a callback
        function( newElements ) {
	//		$('#infscr-loading').addClass('itemp').appendTo('.evo_main_area');
          $container.masonry( 'appended', $( newElements ) ).masonry('layout'); 
        }
      );
	});
	
});

</script>
<?php } ?>
<script type="text/javascript">
jQuery(document).ready(function() {
//	alert("eee");
	// check input & selects for default bootstrap3 class .form-control
	$("input:not([type='checkbox']),select,textarea").each(function(index, element) {
		if(!$(this).hasClass("form-control") ) {
			$(this).addClass("form-control");
		}
	});
	$("form").each(function(index, element) {
		if(!$(this).hasClass("form-inline") ) {
			$(this).addClass("form-inline");
		}
	});
});
</script>
</body>
</html>