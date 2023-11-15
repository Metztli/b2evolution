<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (version_compare($app_version, '3.0') < 0) { // Older skins (versions 2.x and above) should work on newer b2evo versions, but newer skins may not work on older b2evo versions.
    die('This skin is designed for b2evolution 3.0 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);
global $baseurl,$skin,$skins_url,$ReqURI,$Item,$Chapter;
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php');
// Note: You can customize the default HTML header by copying the
// _html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
$skinuri = $skins_url . $skin . '/';
//var_dump($ReqURI,$Item,$Chapter);
//var_dump($arc_disp_arr);
?>

<div class="navbar navbar-default nav_zone" role="navigation">
    <div class="container inner_wrapper">
    	<div class="navbar-header col-sm-2 col-md-2 col-lg-2">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
           <a href="<?php echo $Blog->gen_blogurl() ?>" title="<?php echo $Blog->name ?>" id="logo_lg" class="navbar-brand logo logo_img">
                <img alt="<?php echo $Blog->name ?>" class="img-responsive" src="<?php echo $Skin->skinuri . 'logo.png' ?>" />
           </a>
        </div>
        <div id="topmenuwrap" class="col-sm-6 col-md-6"> 
            <div class="top_menu navbar-collapse collapse">
                <ul class="nav navbar-nav">
                <?php
                    // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
                    // Display container and contents:
                    // Note: this container is designed to be a single <ul> list
                    skin_container(NT_('Menu'), [
                        // The following params will be used as defaults for widgets included in this container:
                        'block_start' => '<li class="$wi_class$">',
                        'block_end' => '</li>',
                        'block_display_title' => false,
                        'list_start' => '',
                        'list_end' => '',
                        'item_start' => '',
                        'item_end' => '',
                    ]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>
                </ul>
                &nbsp;
            </div>
        </div>
        <div class="searchwrap searchf_mlg col-sm-4 col-md-4">
            <?php
            $share_uri = $_SERVER['HTTP_HOST'] . $ReqURI;
if (isset($Item) && ! empty($Item)) {
    //			var_dump($Item);
    // If we are on a post / page
    $share_excerpt = $Item->excerpt;
    $share_title = $Item->title;
    $tinyurl = $Skin->eo_get_tinyurl();
} elseif (isset($Chapter) && ! empty($Chapter)) {
    //if we are on category
    $share_excerpt = urlencode($Chapter->description);
    $share_title = urlencode($Chapter->name);
} else {
    $is5x = version_compare($app_version, '5', '>');
    //		($is5x) ? $blogdesc = $Blog->longdesc : $blogdesc = $Blog->description;
    $blogdesc = $Blog->longdesc;
    $share_excerpt = $blogdesc;
    $share_title = $Blog->name;
}

if (strlen($share_title > 100)) {
    $twitext = substr($share_title, 0, 84) . '..';
} else {
    $tw_short_title = substr($share_title, 0, 34) . '..';
    $tw_short_desc = substr($share_excerpt, 0, 52) . '..';
    $twitext = $tw_short_title . ' | ' . $tw_short_desc;
}
if (isset($tinyurl)) {
    $twitext = $twitext . $tinyurl;
} else {
    $twitext = $twitext . $Skin->curruri;
}

?>
            <?php // var_dump($Skin->og_img);?>
            <ul class="socicons">
                <li><a class="asharer" href="javascript: void(0)" onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo urlencode($share_title); ?>&amp;p[summary]=<?php echo urlencode($share_excerpt); ?>&amp;p[url]=<?php echo $share_uri ?><?php if (isset($og_img) && ! empty($og_img)) {
                    echo '&amp;&amp;p[images][0]=' . $og_img;
                }?>','sharer','toolbar=0,status=0,width=548,height=325');"><i class="fa fa-facebook-square fa-2x"></i></a></li>
               <li><a class="asharer" target="_blank" href="https://plus.google.com/share?url=<?php echo $share_uri; ?>"><i class="fa fa-google-plus-square fa-2x"></i></a></li>
                <li><a class="asharer" target="_blank" href="http://twitter.com/share?url=<?php echo $share_uri ?>&text=<?php echo urlencode($twitext) ?>"><i class="fa fa-twitter-square fa-2x"></i></a></li>
            </ul>


            			<?php
                echo '<form class="search navbar-form navbar-right form-inline" role="search" id="searchformtop" method="get" action="' . $Blog->gen_blogurl() . '">';
echo '<div class="input-group">';
$s = get_param('s');
echo '<input type="text" id="topsearchinput" placeholder="Search" name="s" size="25" value="' . htmlspecialchars($s) . '" class="search_field SearchField search-query form-control s_exp" />';
echo '<input type="hidden" name="disp" value="search" />';
//	echo '<input type="submit" name="submit" class="search_submit submit" value="'.format_to_output( $this->disp_params['button'], 'htmlattr' ).'" />';
echo '<div class="input-group-btn">
                                                   <button class="btn btn-info">
                                                   <span class="glyphicon glyphicon-search"></span>
                                                   </button>
												</div>';
echo '</div>';
echo '</form>';
?>
		</div>
	</div>
</div>
<?php // var_dump($disp);?>
<?php (! in_array($disp, $Skin->arc_disp_arr)) ? $maincols = ' col-xs-12 col-sm-8 col-lg-9' : $maincols = '';

?>
<div class="main_zone container">
	<?php
        // ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
        request_title([
            'title_before' => '<h2 class="evo_req_title h2">',
            'title_after' => '</h2>',
            'title_none' => '',
            'glue' => ' - ',
            'title_single_disp' => false,
            'format' => 'htmlbody',
        ]);
// ------------------------------ END OF REQUEST TITLE -----------------------------
?>
<div class="inner_wrapper row" id="main">
<div class="messages_notices row">
	<?php
        // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
    messages([
        'block_start' => '<div class="action_messages">',
        'block_end' => '</div>',
    ]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>
</div>
<div class="clearfix postshold evo_main_area<?php echo $maincols ?>">

	<!-- =================================== START OF MAIN AREA =================================== -->

	<?php // ------------------------------------ START OF POSTS ----------------------------------------
        // Go Grab the featured post:
    if ($Item = &get_featured_Item()) {	// We have a featured/intro post to display:
        // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
        skin_include('_item_block.inc.php', [
            'feature_block' => true,
            'content_mode' => 'auto',		// 'auto' will auto select depending on $disp-detail
            'intro_mode' => 'normal',	// Intro posts will be displayed in normal mode
            'item_class' => 'featured_post',
            //	'image_size'   => 'fit-440x600',
        ]);
        // ----------------------------END ITEM BLOCK  ----------------------------
    }

    // Display message if no post:
    display_if_empty();

while ($Item = &mainlist_get_item()) {	// For each blog post:
    // ---------------------- ITEM BLOCK INCLUDED HERE ------------------------
    skin_include('_item_block.inc.php', [
        'content_mode' => 'auto',		// 'auto' will auto select depending on $disp-detail
        //		'image_size'	 =>	'fit-440x600',
    ]);
    // ----------------------------END ITEM BLOCK  ----------------------------
}
?>


	<?php
    // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'disp_posts' => '',		// We already handled this case above
        'disp_single' => '',		// We already handled this case above
        'disp_page' => '',		// We already handled this case above
        'author_link_text' => 'preferredname',
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

</div>
<?php if (! in_array($disp, $Skin->arc_disp_arr)) { ?>
<!-- =================================== START OF  SIDEBAR =================================== -->
<div id="sidebar" class="evo_sidebar col-xs-12 col-sm-4 col-lg-3 fluid-sidebar sidebar bSideBar bSideBar_right">
<?php //var_dump($disp)?>

	<?php
        // Display container contents:
    skin_container(NT_('Sidebar'), [
        // The following (optional) params will be used as defaults for widgets included in this container:
        // This will enclose each widget in a block:
        'block_start' => '<div class="evo_side_item $wi_class$">',
        'block_end' => '</div>',
        // This will enclose the title of each widget:
        'block_title_start' => '<h3>',
        'block_title_end' => '</h3>',
        // If a widget displays a list, this will enclose that list:
        'list_start' => '<ul>',
        'list_end' => '</ul>',
        // This will enclose each item in a list:
        'item_start' => '<li>',
        'item_end' => '</li>',
        // This will enclose sub-lists in a list:
        'group_start' => '<ul>',
        'group_end' => '</ul>',
    ]);
    ?>

</div><!-- sidebar -->
<?php } ?>

<?php if (isset($MainList->total_pages) && $MainList->total_pages > 1 && in_array($disp, $Skin->arc_disp_arr) && $Skin->get_setting('inf_scr_mode') == 'manual') { ?>
<a href="#" class="btn btn-danger btn-lg" id="load_more"><i class="fa fa-download"></i> Load More</span></a>
<?php }?>
		<?php

        // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
        mainlist_page_links([

            'block_start' => '<div id="da_pagination" class="pagination-wrap row box_wrap"><span class="pagination">',
            'block_end' => '</span></div>',
            'next_text' => '&gt;&gt',
            'links_format' => '$prev$ $first$ $list_prev$ $list$ $list_next$ $last$ $next$',

            //			'links_format' => '$prev$$list$$next$',
        ]);

// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------

?>

<div class="clear"><?php echo get_icon('pixel'); ?></div>

</div>
</div>

<?php
// ------------------------- BODY FOOTER INCLUDED HERE --------------------------
skin_include('_body_footer.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>

<?php
// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>