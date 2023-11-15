<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $Item,$arc_disp_arr;

// Default params:
$params = array_merge([
    'feature_block' => false,
    'content_mode' => 'auto',		// 'auto' will auto select depending on $disp-detail
    'item_class' => 'evo_post',
    'image_size' => 'fit-400x320',
    'author_link_text' => 'preferredname',
], $params);

//echo '<div class="itemp styled_content_block">'; // Beginning of post display
?>
<?php

//$cols
//var_dump( $disp );
$col_type = $Skin->get_setting('col_type');
if ($col_type == 'colt2') {
    $cols = 'col-sm-6 col-md-6 col-lg-4';
} elseif ($col_type == 'colt3') {
    $cols = 'col-md-6';
} else {
    $cols = 'col-sm-6 col-md-4 col-lg-3';
}

(! in_array($disp, $Skin->arc_disp_arr)) ? $cols = '' : $cols = $cols . ' ';
//var_dump($disp,$arc_disp_arr);
?>

<div id="<?php $Item->anchor_id() ?>" class="<?php echo $cols?>itemp <?php $Item->div_classes($params) ?>" lang="<?php $Item->lang() ?>">
	<div class="content_wrap">
		<?php
            $Item->locale_temp_switch(); // Temporarily switch to post locale (useful for multilingual blogs)
?>

        <?php
            // ---------------------- POST CONTENT INCLUDED HERE ----------------------
    skin_include('_item_content.inc.php', $params);
// Note: You can customize the default item feedback by copying the generic
// /skins/_item_content.inc.php file into the current skin folder.
// -------------------------- END OF POST CONTENT -------------------------
?>
        <div class="evo_post_foot">
        <?php
            $morelink = '<a class="btn btn-info btn-xs morea" href="' . $Item->get_permanent_url() . '">' . T_('&raquo; More') . '</a>';
echo $morelink;
if ($Item->status != 'published') {
    $Item->status([
        'format' => 'styled',
    ]);
}

$Item->issue_date([
    'before' => ' | <i class="fa fa-calendar"></i> ',
    'after' => ' ',
    'date_format' => '#',
]);

$Item->issue_time([
    'after' => '',
    'time_format' => 'H:i',
]);

$Item->author([
    'before' => ' | <i class="fa fa-user"></i> ' . T_('by') . ' ',
    'after' => '',
    'link_text' => $params['author_link_text'],
]);

$Item->categories([
    'before' => ' | <i class="fa fa-folder-open"></i> ' . T_('Categories') . ': ',
    'after' => ' ',
    'include_main' => true,
    'include_other' => true,
    'include_external' => true,
    'link_categories' => true,
]);

$Item->permanent_link([
    'before' => ' | ',
    'text' => '<i class="fa fa-anchor"></i>',
]);

// List all tags attached to this post:
// $Item->tags( array(
// 		'before' =>         ', '.T_('Tags').': ',
// 		'after' =>          ' ',
// 		'separator' =>      ', ',
// 	) );
?>
        </div>

        <?php
    $Item->edit_link([
        // Link to backoffice for editing
        'before' => ' ',
        'after' => ' ',
        'class' => 'floatright editbtn btn btn-primary btn-sm',
        'text' => '<span class="glyphicon glyphicon-pencil"></span> Edit',
    ]);
?>   

        <?php
    // ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
    skin_include('_item_feedback.inc.php', [
        'before_section_title' => '<h4>',
        'after_section_title' => '</h4>',
        'author_link_text' => $params['author_link_text'],
    ]);
// Note: You can customize the default item feedback by copying the generic
// /skins/_item_feedback.inc.php file into the current skin folder.
// ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
?>
            
        <?php
    locale_restore_previous();	// Restore previous locale (Blog locale)
?>
        <?php //  var_dump($Item->get_tinyurl() )?>
        <?php // var_dump($Item->ptyp_ID )?>
        <?php // var_dump($params['item_class'] )?>
        <?php // var_dump(in_array($Item->ptyp_ID, $Skin->feat_post_typs) )?>
        <?php if ($Item->featured || strpos($params['item_class'], 'featured_post') !== false) { ?>
			       <div class="floatright ribwrap"><span class="note featrib"><span>Featured</span></span></div>
                   <?php } ?>

    </div>
</div>

<?php

//echo '</div>'; // End of post display

?>