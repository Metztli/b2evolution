<?php
/**
 * This file implements the UI view for the skin selection when creating a blog.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $kind, $admin_url, $action, $AdminUI, $Session;

$kind_title = get_collection_kinds($kind);

echo '<h2 class="page-title">' . sprintf(T_('New [%s]'), $kind_title) . ':</h2>';

if ($action == 'new-selskin') {	// Select an existing skin:
    $coll_url_suffix = get_param('sec_ID') ? '&amp;sec_ID=' . get_param('sec_ID') : '';

    echo '<h3>' . sprintf(T_('Pick an existing skin below: (or <a %s>install a new one now</a>)'), 'href="' . $admin_url . '?ctrl=collections&amp;action=new-installskin&amp;kind=' . $kind . $coll_url_suffix . '&amp;skin_type=normal"') . '</h3>';

    $SkinCache = &get_SkinCache();
    $SkinCache->load_all();
    $SkinCache->rewind();

    // Group together skins based on how well they support the selected collection type
    $skins = [];
    while (($iterator_Skin = &$SkinCache->get_next()) != null) {
        if ($iterator_Skin->type != 'normal' && $iterator_Skin->type != 'rwd') {	// This skin cannot be used here...
            continue;
        }
        $skins[$iterator_Skin->supports_coll_kind($kind)][] = $iterator_Skin;
    }


    // Skins that fully support the selected collection type
    $block_item_Widget = new Widget('block_item');
    $block_item_Widget->title = sprintf(T_('Recommended skins for a "%s" collection'), $kind_title);
    $block_item_Widget->global_icon(
        T_('Abort creating new collection'),
        'close',
        $admin_url . '?ctrl=collections',
        ' ' . sprintf(T_('Abort new "%s" collection'), $kind_title),
        3,
        4,
        [
            'class' => 'action_icon btn-default',
        ]
    );

    $block_item_Widget->disp_template_replaced('block_start');
    echo '<div class="skin_selector_block">';

    if (check_user_perm('options', 'edit', false)) { // A link to install new skin:
        echo '<a href="' . $admin_url . '?ctrl=collections&amp;action=new-installskin&amp;kind=' . $kind . '&amp;skin_type=normal"
					class="skinshot skinshot_new">' . get_icon('new') . T_('Install New') . ' &raquo;' . '</a>';
    }

    if (isset($skins['yes'])) {
        foreach ($skins['yes'] as $iterator_Skin) {
            $select_url = '?ctrl=collections&amp;action=new-name&amp;kind=' . $kind . '&amp;skin_ID=' . $iterator_Skin->ID . $coll_url_suffix;
            $disp_params = [
                'function' => 'select',
                'select_url' => $select_url,
            ];
            // Display skinshot:
            Skin::disp_skinshot($iterator_Skin->folder, $iterator_Skin->name, $disp_params);
        }
    }

    echo '<div class="clear"></div>';
    echo '</div>';
    $block_item_Widget->disp_template_replaced('block_end');


    // Skins that partially support the selected collection type
    if (isset($skins['partial'])) {
        $block_item_Widget = new Widget('block_item');
        $block_item_Widget->title = sprintf(T_('Skins that are not optimal for a "%s" collection'), $kind_title);

        $block_item_Widget->disp_template_replaced('block_start');
        echo '<div class="skin_selector_block">';

        foreach ($skins['partial'] as $iterator_Skin) {
            $select_url = '?ctrl=collections&amp;action=new-name&amp;kind=' . $kind . '&amp;skin_ID=' . $iterator_Skin->ID . $coll_url_suffix;
            $disp_params = [
                'function' => 'select',
                'select_url' => $select_url,
            ];
            // Display skinshot:
            Skin::disp_skinshot($iterator_Skin->folder, $iterator_Skin->name, $disp_params);
        }

        echo '<div class="clear"></div>';
        echo '</div>';

        $block_item_Widget->disp_template_replaced('block_end');
    }


    // Skins that maybe support the selected collection type
    if (isset($skins['maybe'])) {
        $block_item_Widget = new Widget('block_item');
        $block_item_Widget->title = sprintf(T_('Other skins that might work for a "%s" collection'), $kind_title);

        $block_item_Widget->disp_template_replaced('block_start');
        echo '<div class="skin_selector_block">';

        foreach ($skins['maybe'] as $iterator_Skin) {
            $select_url = '?ctrl=collections&amp;action=new-name&amp;kind=' . $kind . '&amp;skin_ID=' . $iterator_Skin->ID . $coll_url_suffix;
            $disp_params = [
                'function' => 'select',
                'select_url' => $select_url,
            ];
            // Display skinshot:
            Skin::disp_skinshot($iterator_Skin->folder, $iterator_Skin->name, $disp_params);
        }

        echo '<div class="clear"></div>';
        echo '</div>';

        $block_item_Widget->disp_template_replaced('block_end');
    }
} elseif ($action == 'new-installskin') { // Display a form to install new skin
    set_param('redirect_to', $admin_url . '?ctrl=collections&action=new-selskin&kind=' . $kind);
    $AdminUI->disp_view('skins/views/_skin_list_available.view.php');
}
