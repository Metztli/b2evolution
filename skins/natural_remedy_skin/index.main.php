<?php
/**
 * This is the main/default page template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 * @subpackage kubrick
 *
 * @version $Id: index.main.php,v 1.3 2007/10/01 01:06:31 fplanque Exp $
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init($disp);


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
skin_include('_html_header.inc.php', []);
// -------------------------------- END OF HEADER --------------------------------


// ---------------------------- SITE HEADER INCLUDED HERE ----------------------------
// If site headers are enabled, they will be included here:
siteskin_include('_site_body_header.inc.php');
// ------------------------------- END OF SITE HEADER --------------------------------
?>
<div id="bodywrapper">
<div id="wrapper">
<div id="wrap">
<div id="header">
	<div id="title">
		<?php
                // ------------------------- "Header" CONTAINER EMBEDDED HERE --------------------------
                // Display container and contents:
                skin_container(NT_('Header'), [
                    // The following params will be used as defaults for widgets included in this container:
                    'block_start' => '<div class="description">',
                    'block_end' => '</div>',
                    'block_title_start' => '<h1>',
                    'block_title_end' => '</h1>',
                ]);
// ----------------------------- END OF "Header" CONTAINER -----------------------------
?>
	</div>
<div id="pagenav">
  <ul>
        <?php
                    // ------------------------- "Menu" CONTAINER EMBEDDED HERE --------------------------
                    // Display container and contents:
                    // Note: this container is designed to be a single <ul> list
        skin_container(NT_('Menu'), [
            // The following params will be used as defaults for widgets included in this container:
            'block_start' => '',
            'block_end' => '',
            'block_display_title' => false,
            'list_start' => '',
            'list_end' => '',
            'item_start' => '<li>',
            'item_end' => '</li>',
        ]);
// ----------------------------- END OF "Menu" CONTAINER -----------------------------
?>
  </ul>
  </div>
</div><!-- end of header -->
<div class="clear">&nbsp;</div>
<?php
// ------------------------- SIDEBAR INCLUDED HERE --------------------------
skin_include('_sidebar.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ----------------------------- END OF SIDEBAR -----------------------------
?>
<div id="content">
	<?php
    // ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
    messages([
        'block_start' => '<div class="action_messages">',
        'block_end' => '</div>',
    ]);
// --------------------------------- END OF MESSAGES ---------------------------------
?>
    
	<?php	// ---------------------------------- START OF POSTS --------------------------------------
    // Display message if no post:
display_if_empty();
while ($Item = &mainlist_get_item()) {	// For each blog post, do everything below up to the closing curly brace "}"
    ?>
    
    <div id="<?php $Item->anchor_id() ?>" lang="<?php $Item->lang() ?>">
		<?php
            $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
    ?>
        <h1><a href="<?php $Item->permanent_url() ?>" title="<?php echo T_('Permanent link to full entry') ?>"></a><?php $Item->title(); ?></h1>
    
        <?php
            // ---------------------- POST CONTENT INCLUDED HERE ----------------------
        skin_include('_item_content.inc.php');
    // Note: You can customize the default item feedback by copying the generic
    // /skins/_item_feedback.inc.php file into the current skin folder.
    // -------------------------- END OF POST CONTENT -------------------------
    ?>
	<div class="postmetadata">
    	<?php
        // List all tags attached to this post:
    $Item->tags([
        'before' => ' ' . T_('Tags') . ': ',
        'after' => ' ',
        'separator' => ', ',
    ]);
    ?>

		<?php
    $Item->categories([
        'before' => ' ' . T_('Posted Under') . ' ',
        'after' => '',
        'include_main' => true,
        'include_other' => true,
        'include_external' => true,
        'link_categories' => true,
    ]);
    ?>
		on
		<?php
    $Item->issue_time([
        'time_format' => 'm.j.Y',
    ]);
    ?>
		<!-- You can follow any responses to this entry through the RSS feed. -->
		<?php
        $Item->edit_link([
            // Link to backoffice for editing
            'before' => '',
            'after' => '',
        ]);
    ?>
	</div>
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
</div>

<?php
    locale_restore_previous();	// Restore previous locale (Blog locale)
    ?>

<?php } // --------------------------------- END OF POSTS -----------------------------------?>

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
<div align="center">
	<?php
    // -------------------- PREV/NEXT PAGE LINKS (POST LIST MODE) --------------------
    mainlist_page_links([
        'block_start' => '<p class="center">',
        'block_end' => '</p>',
        'links_format' => '$prev$ :: $next$',
        'prev_text' => '&lt;&lt; ' . T_('Previous'),
        'next_text' => T_('Next') . ' &gt;&gt;',
    ]);
// ------------------------- END OF PREV/NEXT PAGE LINKS -------------------------
?>
</div>
</div>
<div class="clear">&nbsp;</div>
<?php
// ------------------------- BODY FOOTER INCLUDED HERE --------------------------
skin_include('_body_footer.inc.php');
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>
<?php

$Hit->log();	// log the hit on this page

debug_info(); // output debug info if requested

?>
</div><!-- end of wrap --></div>
</div><!-- end of wrapper --></div>
</body>
</html>
