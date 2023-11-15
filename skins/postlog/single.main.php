<?php
/**
 * This is the main/default page template for the "postlog" skin.
 *
 * This skin only uses one single template which includes most of its features.
 * It will also rely on default includes for specific dispays (like the comment form).
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 * @subpackage postlog
 *
 * @version $Id: index.main.php,v 1.16 2008/04/15 21:53:31 fplanque Exp $
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (version_compare($app_version, '2.4.1') < 0) { // Older 2.x skins work on newer 2.x b2evo versions, but newer 2.x skins may not work on older 2.x b2evo versions.
    die('This skin is designed for b2evolution 2.4.1 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>


<div id="wrapper">

<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_body_header.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>
  <div id="innerwrapper">
  


<!-- =================================== START OF MAIN AREA =================================== -->

<div id="main">

<!-- =================================== START OF SIDEBAR =================================== -->


	<?php
        // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
        messages([
            'block_start' => '<div class="action_messages">',
            'block_end' => '</div>',
        ]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>
<div class="content_single">






	<?php
        // --------------------------------- START OF POSTS -------------------------------------
        // Display message if no post:
    display_if_empty();

while ($Item = &mainlist_get_item()) {	// For each blog post, do everything below up to the closing curly brace "}"
    ?>


<div class="postwrap_single">	

<div class="posthead">
<h2 class="ptitle"><?php $Item->title(); ?></h2>


<h3 class="spost_link">  <?php	$Item->url_link([
    'before' => 'Link:&nbsp;',
]); ?> </h3></div>
            
		<div id="<?php $Item->anchor_id() ?>" class="post_single status<?php $Item->status_raw() ?> catSID<?php echo $Item->main_cat_ID ?>" lang="<?php $Item->lang() ?>">

			<?php
            $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
    ?>




		

			<?php
                // ---------------------- POST CONTENT INCLUDED HERE ----------------------
        skin_include('_item_content.single.inc.php', [
            'image_size' => 'fit-800x640',
        ]);
    // Note: You can customize the default item feedback by copying the generic
    // /skins/_item_feedback.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
    ?>
<div class="post_details">
			<?php
                // List all tags attached to this post:
        $Item->tags([
            'before' => '<div class="bSmallPrint tags">' . T_('Tags') . ': ',
            'after' => '</div>',
            'separator' => ', ',
        ]);
    ?>
            
            <span class="sauthor"><?php
        $Item->author([
            'before' => '',
            'after' => '',
        ]);

    $Item->msgform_link();
    echo ', '; ?></span>
	<span class="stime">		<?php
    // Permalink:
    $Item->permanent_link([
        'text' => '#icon#',
    ]);

    $Item->issue_time([
        'before' => ' ',
        'after' => '',
        'type' => 'd,m',
    ]);

    ?></span>

			<br />

			<?php
        $Item->categories([
            'before' => T_('Categories') . ': ',
            'after' => ' ',
            'include_main' => true,
            'include_other' => false,
            'include_external' => false,
            'link_categories' => true,
        ]);
    ?>
			<div class="sSmallPrint">
				<?php
            // Permalink:
            $Item->permanent_link([
                'class' => 'permalink_right',
            ]);

    // Link to comments, trackbacks, etc.:
    $Item->feedback_link([
        'type' => 'comments',
        'link_before' => '',
        'link_after' => '',
        'link_text_zero' => '#',
        'link_text_one' => '#',
        'link_text_more' => '#',
        'link_title' => '#',
        'use_popup' => false,
    ]);

    // Link to comments, trackbacks, etc.:
    $Item->feedback_link([
        'type' => 'trackbacks',
        'link_before' => ' &bull; ',
        'link_after' => '',
        'link_text_zero' => '#',
        'link_text_one' => '#',
        'link_text_more' => '#',
        'link_title' => '#',
        'use_popup' => false,
    ]);

    $Item->edit_link([
        // Link to backoffice for editing
        'before' => ' &bull; ',
        'after' => '',
    ]);
    ?>
			</div>
       </div><!-- end of .postdetails -->

			<?php
    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', [
        'before_section_title' => '<h4>',
        'after_section_title' => '</h4>',
    ]);
    // Note: You can customize the default item feedback by copying the generic
    // /skins/_item_feedback.inc.php file into the current skin folder.
    // ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
    ?>

			<?php
        locale_restore_previous();	// Restore previous locale (Blog locale)
    ?>

		</div><!-- end of #item_ID-->
        </div><!-- end of .postwrap-->
		<?php
} // ---------------------------------- END OF POSTS ------------------------------------
?>

	<?php
        // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
    mainlist_page_links([
        'block_start' => '<div class="pagenumbers"><strong>',
        'block_end' => '</strong></div>',
        'prev_text' => '&lt;&lt;Previous&nbsp;Page<br/>',
        'next_text' => '<br/>Next&nbsp;Page&gt;&gt;',
    ]);
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
?>


	<?php
        // -------------- MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp) --------------
    skin_include('$disp$', [
        'disp_posts' => '',		// We already handled this case above
        'disp_single' => '',		// We already handled this case above
        'disp_page' => '',		// We already handled this case above
    ]);
// Note: you can customize any of the sub templates included here by
// copying the matching php file into your skin directory.
// ------------------------- END OF MAIN CONTENT TEMPLATE ---------------------------
?>

</div><!-- end of .content -->

<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_sidebar.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>
</div><!-- end of #main -->



<!-- =================================== START OF FOOTER =================================== -->



</div><!-- end of .innerwrapper -->

</div><!-- end of #wrapper-->

<?php
// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_body_footer.inc.php');
// Note: You can customize the default HTML header by copying the generic
// /skins/_html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>
<?php
// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
skin_include('_html_footer.inc.php');
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>