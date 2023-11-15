<?php
/**
 * This skin only uses one single template which includes most of its features.
 * It will also rely on default includes for specific dispays (like the comment form).
 *
 * The main page template is used to display the blog when no specific page template is available
 * to handle the request (based on $disp).
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

if (version_compare($app_version, '3.0') < 0) {
    die('This skin is designed for b2evolution 3.0 and above. Please <a href="http://b2evolution.net/downloads/index.html">upgrade your b2evolution</a>.');
}

// Do inits depending on current $disp:
skin_init($disp);

require_js('#jquery#');
require_js('scripts.js', true);
require_css('print.css', true, '', 'print');

// HTML HEADER INCLUDED HERE
skin_include('_html_header.inc.php');

global $s;
?>

<div id="page_wrap">
  <div id="header">
    <div class="blog_list">
      <?php
      // START OF BLOG LIST
      skin_widget([
        'widget' => 'colls_list_public',
          'block_start' => '/&nbsp; ',
          'block_end' => '',
          'block_display_title' => false,
          'list_start' => '',
          'list_end' => '',
          'item_start' => '',
          'item_end' => ' &nbsp;/&nbsp; ',
          'item_selected_start' => '<span class="selected">',
          'item_selected_end' => '</span> &nbsp;/&nbsp; ',
    ]);
?>
    </div>
    <div class="clear"></div>
    <div class="blog_title">
      <?php
// "Header" CONTAINER EMBEDDED HERE
skin_container(NT_('Header'), [
    'block_start' => '',
    'block_end' => '',
    'block_title_start' => '<h1>',
    'block_title_end' => '</h1>',
]);
?>
    </div>
    <div id="search">
	  <?php
skin_widget([
    // CODE for the widget:
    'widget' => 'coll_search_form',
    // Optional display params
    'block_start' => '',
    'block_end' => '',
    'block_display_title' => false,
    'disp_search_options' => 0,
    'search_class' => 'extended_search_form',
    'use_search_disp' => 0,
]);
?>
    </div>
    <div class="clear"></div>
  </div>
  <div id="main_navi">
    <ul class="left">
      <?php
    // "Menu" CONTAINER EMBEDDED HERE
    skin_container(NT_('Menu'), [
        'block_start' => '',
        'block_end' => '',
        'block_display_title' => false,
        'list_start' => '',
        'list_end' => '',
    ]);
?>
    </ul>
    <ul class="right">
      <!--<li class="twitter"><a href="http://twitter.com/your_user_name">Twitter</a></li>-->
      <li class="feed"><a href="<?php $Blog->disp('rss2_url', 'raw') ?>">RSS Feed</a></li>
    </ul>
  </div>
  <div class="clear"></div>
  <div id="container">
    <div id="main">
      <?php
// MESSAGES GENERATED FROM ACTIONS
messages([
    'block_start' => '<div class="action_messages">',
    'block_end' => '</div>',
]);

// Display search results
global $ASearch_plugin;
if (is_object($ASearch_plugin)) {
    $ASearch_plugin->display_results();
}

// PREV/NEXT POST LINKS (SINGLE POST MODE)
item_prevnext_links([
    'block_start' => '<div class="navigation">',
    'prev_start' => '<div class="left">',
    'prev_end' => '</div>',
    'next_start' => '<div class="right">',
    'next_end' => '</div>',
    'block_end' => '</div>',
]);

// TITLE FOR THE CURRENT REQUEST
request_title([
    'title_before' => '<h2 class="request_title">',
    'title_after' => '</h2>',
    'title_none' => '',
    'glue' => ' - ',
    'title_single_disp' => false,
    'title_page_disp' => false,
    'format' => 'htmlbody',
]);

if ($Item = &get_featured_Item()) {	// We have a featured/intro post to display:
    // ITEM BLOCK INCLUDED HERE
    skin_include('_item_block.inc.php', [
        'feature_block' => true,
        'content_mode' => 'auto',
        'intro_mode' => 'normal',
        'item_class' => 'bPost bFeatured',
        'image_size' => 'fit-400x320',
    ]);
}

// START OF POSTS
// Display message if no post:
display_if_empty([
    'before' => '<div class="bPost display_empty">',
    'after' => '</div>',
]);

while ($Item = &mainlist_get_item()) {
    // ITEM BLOCK INCLUDED HERE
    skin_include('_item_block.inc.php', [
        'content_mode' => 'auto',
        'image_size' => 'fit-400x320',
    ]);
}

// PREV/NEXT PAGE LINKS (POST LIST MODE)
mainlist_page_links([
    'block_start' => '<div class="page_nav">',
    'block_end' => '</div>',
    'prev_text' => '&lt;&lt;',
    'next_text' => '&gt;&gt;',
]);

// MAIN CONTENT TEMPLATE INCLUDED HERE (Based on $disp)
skin_include('$disp$', [
    'disp_posts' => '',		// We already handled this case above
    'disp_single' => '',		// We already handled this case above
    'disp_page' => '',		// We already handled this case above
]);

// "After Posts" CONTAINER EMBEDDED HERE
skin_container(NT_('After Posts'), [
    'block_start' => '<div class="AfterPosts">',
    'block_end' => '</div>',
]);
?>
    </div>
    <div id="sidebar">
      <?php
// "Sidebar" CONTAINER EMBEDDED HERE
skin_container(NT_('Sidebar'), [
    // This will enclose each widget in a block:
    'block_start' => '<div class="bSideitem $wi_class$">',
    'block_end' => '</div>',
    // This will enclose the title of each widget:
    'block_title_start' => '<h2>',
    'block_title_end' => '</h2>',
    // If a widget displays a list, this will enclose that list:
    'list_start' => '<ul>',
    'list_end' => '</ul>',
    // This will enclose each item in a list:
    'item_start' => '<li>',
    'item_end' => '</li>',
    // This will enclose sub-lists in a list:
    'group_start' => '<ul>',
    'group_end' => '</ul>',
    // This will enclose (foot)notes:
    'notes_start' => '<div class="notes">',
    'notes_end' => '</div>',
]);
?>
    </div>
    <div class="clear"></div>
  </div>
</div>
<div id="footer">
  <div class="footer_wrapper">
    <div class="footer_left">Theme: <a href="http://imotta.cn">Pyrmont V1.2</a>, <a href="http://b2evo.sonorth.com/show.php/b2evolution-freelance-work">b2evolution freelance services</a> by Sonorth Corp.</div>
    <div>Powered by <a href="http://b2evolution.net/">b2evolution</a></div>
  </div>
</div>
<?php
// HTML FOOTER INCLUDED HERE
skin_include('_html_footer.inc.php');
?>