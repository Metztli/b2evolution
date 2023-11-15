<?php
/**
 * This file provides icon definitions through a function.
 *
 * Will resolve translations at runtime and consume less memory than a table.
 */
if (! defined('EVO_CONFIG_LOADED')) {
    die('Please, do not access this page directly.');
}


/**
 * Get icon according to an item.
 *
 * @param string icon name/key
 * @return array array( 'file' (relative to $rsc_path/$rsc_url), 'alt', 'size', 'class', 'rollover' )
 */
function get_icon_info($name)
{
    /*
     * dh> Idea:
    * fp> does not make sense to me. Plugins should do their own icons without a bloated event. Also if we allow something to replace existing icons it should be a skin (either front or admin skin) and some overloaded/overloadable get_skin_icon()/get_admin_icon() should be provided there.
    global $Plugins;
    if( $r = $Plugins->trigger_event_first_return('GetIconInfo', array('name'=>$name)) )
    {
        return $r['plugin_return'];
    }
    */

    switch ($name) {
        case 'pixel': return [
            'alt' => '',
            'size' => [1, 1],
            'xy' => [0, 0],
        ];

        case 'folder': return [
            // icon for folders
            'alt' => T_('Folder'),
            'size' => [16, 15],
            'xy' => [0, 16],
            'glyph' => 'folder-open',
            'fa' => 'folder-open',
        ];
        case 'file_unknown': return [
            // icon for unknown files
            'alt' => T_('Unknown file'),
            'size' => [16, 16],
            'xy' => [16, 16],
            'glyph' => 'file',
            'fa' => 'file-o',
        ];
        case 'file_empty': return [
            // empty file
            'alt' => T_('Empty file'),
            'size' => [16, 16],
            'xy' => [32, 16],
            'glyph' => 'file',
            'fa' => 'file-o',
        ];
        case 'folder_parent': return [
            // go to parent directory
            'alt' => T_('Parent folder'),
            'size' => [16, 15],
            'xy' => [48, 16],
            'fa' => 'level-up fa-flip-horizontal',
        ];
        case 'file_copy': return [
            // copy a file/folder
            'alt' => T_('Copy'),
            'size' => [16, 16],
            'xy' => [96, 16],
            'glyph' => 'plus-sign',
            'fa' => 'copy',
        ];
        case 'file_move': return [
            // move a file/folder
            'alt' => T_('Move'),
            'size' => [16, 16],
            'xy' => [112, 16],
            'glyph' => 'circle-arrow-right',
            'fa' => 'arrow-right',
        ];
        case 'file_delete': return [
            // delete a file/folder
            'alt' => T_('Del'),
            'legend' => T_('Delete'),
            'size' => [16, 16],
            'xy' => [128, 16],
            'glyph' => 'trash',
            'fa' => 'trash-o',
        ];

        case 'ascending': return [
            // ascending sort order
            'alt' => /* TRANS: Short (alt tag) for "Ascending" */ T_('A'),
            'size' => [15, 15],
            'xy' => [64, 0],
            'glyph' => 'chevron-up',
            'fa' => 'sort-amount-asc',
        ];
        case 'descending': return [
            // descending sort order
            'alt' => /* TRANS: Short (alt tag) for "Descending" */ T_('D'),
            'size' => [15, 15],
            'xy' => [80, 0],
            'glyph' => 'chevron-down',
            'fa' => 'sort-amount-desc',
        ];

        case 'sort_desc_on': return [
            'alt' => T_('Descending order'),
            'size' => [12, 11],
            'xy' => [64, 208],
            'fa' => 'caret-down',
            'color' => '#000',
        ];
        case 'sort_asc_on': return [
            'alt' => T_('Ascending order'),
            'size' => [12, 11],
            'xy' => [80, 208],
            'fa' => 'caret-up',
            'color' => '#000',
        ];
        case 'sort_desc_off': return [
            'alt' => T_('Descending order'),
            'size' => [12, 11],
            'xy' => [96, 208],
            'fa' => 'caret-down',
            'color' => '#999',
        ];
        case 'sort_asc_off': return [
            'alt' => T_('Ascending order'),
            'size' => [12, 11],
            'xy' => [112, 208],
            'fa' => 'caret-up',
            'color' => '#999',
        ];

        case 'window_new': return [
            // open in a new window
            'alt' => T_('New window'),
            'size' => [15, 13],
            'xy' => [144, 0],
            'fa' => 'folder-o',
        ];

        case 'file_image': return [
            'ext' => '\.(gif|png|jpe?g)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [16, 32],
            'fa' => 'file-image-o',
        ];
        case 'file_document': return [
            'ext' => '\.(txt)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [32, 48],
            'fa' => 'file-text-o',
        ];
        case 'file_www': return [
            'ext' => '\.html?',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [32, 32],
            'fa' => 'file-code-o',
        ];
        case 'file_log': return [
            'ext' => '\.log',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [48, 32],
            'fa' => 'file-text-o',
        ];
        case 'file_sound': return [
            'ext' => '\.(mp3|ogg|wav)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [64, 32],
            'fa' => 'file-sound-o',
        ];
        case 'file_video': return [
            'ext' => '\.(mpe?g|avi)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [80, 32],
            'fa' => 'file-video-o',
        ];
        case 'file_message': return [
            'ext' => '\.msg',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [96, 32],
            'fa' => 'file-text-o',
        ];
        case 'file_pdf': return [
            'ext' => '\.pdf',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [112, 32],
            'fa' => 'file-pdf-o',
        ];
        case 'file_php': return [
            'ext' => '\.php[34]?',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [128, 32],
            'fa' => 'file-code-o',
        ];
        case 'file_encrypted': return [
            'ext' => '\.(pgp|gpg)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [144, 32],
            'fa' => 'file-text-o',
        ];
        case 'file_tar': return [
            'ext' => '\.tar',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [0, 48],
            'fa' => 'file-archive-o',
        ];
        case 'file_tgz': return [
            'ext' => '\.tgz',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [16, 48],
            'fa' => 'file-archive-o',
        ];
        case 'file_pk': return [
            'ext' => '\.(zip|rar)',
            'alt' => '',
            'size' => [16, 16],
            'xy' => [48, 48],
            'fa' => 'file-archive-o',
        ];
        case 'file_doc': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [64, 48],
            'fa' => 'file-word-o',
        ];
        case 'file_xls': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [80, 48],
            'fa' => 'file-excel-o',
        ];
        case 'file_ppt': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [96, 48],
            'fa' => 'file-powerpoint-o',
        ];
        case 'file_pps': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [112, 48],
            'fa' => 'file-powerpoint-o',
        ];
        case 'file_zip': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [128, 48],
            'fa' => 'file-zip-o',
        ];

        case 'expand': return [
            'alt' => '+',
            'legend' => T_('Expand'),
            'size' => [15, 15],
            'xy' => [96, 0],
            'glyph' => 'expand',
            'toggle-glyph' => 'collapse-down',
            'size-glyph' => [10],
            'fa' => 'caret-right',
            'toggle-fa' => 'caret-down',
            'size-fa' => [8],
        ];
        case 'collapse': return [
            'alt' => '-',
            'legend' => T_('Collapse'),
            'size' => [15, 15],
            'xy' => [112, 0],
            'glyph' => 'collapse-down',
            'toggle-glyph' => 'expand',
            'size-glyph' => [10],
            'fa' => 'caret-down',
            'toggle-fa' => 'caret-right',
            'size-fa' => [8],
        ];

        case 'filters_show': return [
            'alt' => T_('Expand'),
            'size' => [15, 15],
            'xy' => [64, 16],
            'glyph' => 'expand',
            'toggle-glyph' => 'collapse-down',
            'fa' => 'caret-right',
            'toggle-fa' => 'caret-down',
        ];
        case 'filters_hide': return [
            'alt' => T_('Collapse'),
            'size' => [15, 15],
            'xy' => [80, 16],
            'glyph' => 'collapse-down',
            'toggle-glyph' => 'expand',
            'fa' => 'caret-down',
            'toggle-fa' => 'caret-right',
        ];

        case 'refresh': return [
            'alt' => T_('Refresh'),
            'size' => [16, 16],
            'xy' => [128, 208],
            'glyph' => 'refresh',
            'fa' => 'refresh',
        ];
        case 'reload': return [
            'alt' => T_('Reload'),
            'size' => [15, 15],
            'xy' => [144, 208],
            'glyph' => 'repeat',
            'fa' => 'repeat',
        ];

        case 'download': return [
            'alt' => T_('Download'),
            'size' => [16, 16],
            'xy' => [128, 0],
            'glyph' => 'download-alt',
            'fa' => 'download',
        ];
        case 'import': return [
            'alt' => T_('Import'),
            'size' => [16, 16],
            'xy' => [128, 0],
            'glyph' => 'upload',
            'fa' => 'upload',
        ];

        case 'warning': return [
            // TODO: not really transparent at its borders
            'alt' => T_('Warning'),
            'size' => [16, 16],
            'xy' => [64, 176],
            'glyph' => 'exclamation-sign',
            'fa' => 'exclamation-circle',
            'color' => '#d9534f',
        ];
        case 'warning_yellow': return [
            'alt' => T_('Warning'),
            'size' => [16, 16],
            'xy' => [48, 176],
            'glyph' => 'warning-sign',
            'fa' => 'warning',
            'color' => '#F90',
        ];

        case 'info': return [
            'alt' => T_('Info'),
            'size' => [16, 16],
            'xy' => [80, 176],
            'glyph' => 'info-sign',
            'fa' => 'info-circle',
        ];
        case 'email': return [
            'alt' => T_('Email'),
            'size' => [16, 12],
            'xy' => [32, 176],
            'glyph' => 'envelope',
            'fa' => 'envelope',
        ];
        case 'www': return [
            /* user's web site, plugin's help url */
            'alt' => T_('WWW'),
            'legend' => T_('Website'),
            'size' => [32, 16],
            'xy' => [128, 128],
            'glyph' => 'home',
            'fa' => 'home',
        ];

        case 'puzzle': return [
            'rollover' => true,
            'alt' => T_('New'),
            'size' => [16, 15],
            'xy' => [0, 64],
            'glyph' => 'plus',
            'fa' => 'puzzle-piece',
        ];
        case 'new': return [
            'rollover' => true,
            'alt' => T_('New'),
            'size' => [16, 15],
            'xy' => [0, 64],
            'glyph' => 'plus',
            'fa' => 'plus-square',
        ];
        case 'compose_new': return [
            // for composing a new message or text
            'rollover' => true,
            'alt' => T_('New'),
            'size' => [16, 15],
            'xy' => [0, 64],
            'glyph' => 'pencil',			// May need something else
            'fa' => 'pencil',
        ];
        case 'contacts': return [
            'rollover' => true,
            'alt' => T_('Contacts'),
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'user',
            'fa' => 'users',
        ];
        case 'user': return [
            'rollover' => true,
            'alt' => T_('User'),
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'user',
            'fa' => 'user',
        ];
        case 'copy': return [
            'alt' => T_('Copy'),
            'size' => [14, 15],
            'xy' => [32, 64],
            'glyph' => 'share',
            'fa' => 'copy',
        ];
        case 'duplicate': return [
            'alt' => T_('Duplicate'),
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'clone',
            'fa' => 'clone',
        ];
        case 'clipboard-copy': return [
            'alt' => T_('Copy'),
            'size' => [14, 15],
            'xy' => [32, 64],
            'glyph' => 'share',
            'fa' => 'copy',
        ];
        case 'choose': return [
            'alt' => T_('Choose'),
            'size' => [16, 13],
            'xy' => [64, 64],
            'glyph' => 'hand-up',
            'fa' => 'hand-o-up',
        ];
        case 'edit': return [
            'alt' => T_('Edit'),
            'size' => [16, 15],
            'xy' => [48, 64],
            'glyph' => 'edit',
            'fa' => 'edit',
        ];
        case 'edit_button': return [
            'alt' => T_('Edit'),
            'size' => [16, 15],
            'xy' => [48, 64],
            'glyph' => 'pencil',
            'fa' => 'pencil',
        ];
        case 'properties': return [
            'alt' => T_('Properties'),
            'size' => [16, 13],
            'xy' => [64, 64],
            'glyph' => 'pencil',
            'fa' => 'edit',
        ];
        case 'publish': return [
            'alt' => T_('Publish'),
            'size' => [12, 15],
            'xy' => [80, 64],
            'glyph' => 'file',
            'fa' => 'file',
            'color' => '#0C0',
        ];
        case 'deprecate': return [
            'alt' => T_('Deprecate'),
            'size' => [12, 15],
            'xy' => [96, 64],
            'glyph' => 'file',
            'fa' => 'file',
            'color' => '#666',
        ];
        case 'locate': return [
            'alt' => T_('Locate'),
            'size' => [15, 15],
            'xy' => [112, 64],
            'glyph' => 'screenshot',
            'fa' => 'bullseye',
        ];
        case 'recycle': return [
            'alt' => T_('Recycle'),
            'legend' => T_('Recycle'),
            'size' => [15, 15],
            'xy' => [128, 64],
            'glyph' => 'remove',
            'fa' => 'recycle fa-x-rollover-red-light',
        ];
        case 'delete': return [
            'alt' => /* TRANS: Delete */ T_('Del'),
            'legend' => T_('Delete'),
            'size' => [15, 15],
            'xy' => [128, 64],
            'glyph' => 'remove',
            'color' => '#F00',
            'fa' => 'trash-o',
        ];
        case 'remove': return [
            'alt' => /* TRANS: Remove */ T_('Rem'),
            'size' => [13, 13],
            'xy' => [144, 64],
            'glyph' => 'remove-sign',
            'fa' => 'times-circle',
            'color' => '#F00',
        ];
        case 'cleanup': return [
            'alt' => T_('Cleanup'),
            'size' => [15, 15],
            'xy' => [128, 64],
            'glyph' => 'wrench',
            'fa' => 'wrench',
        ];
        case 'xross': return [
            // Do NOT use for actions. Use only to indicate Mismatch
            'alt' => 'x',
            'size' => [13, 13],
            'xy' => [144, 64],
            'glyph' => 'remove-sign',
            'fa' => 'times-circle',
            'color' => '#F00',
        ];
        case 'close': return [
            'rollover' => true,
            'alt' => T_('Close'),
            'size' => [14, 14],
            'xy' => [0, 224],
            'glyph' => 'remove', // Looks like "X"
            'fa' => 'close fa-x-rollover-red-light',
        ];

        case 'bullet_black':
        case 'bullet_full': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [96, 176],
            'fa' => 'circle',
            'color' => '#000',
        ];
        case 'bullet_empty': return [
            'alt' => '&nbsp;',
            'size' => [9, 9],
            'xy' => [112, 176],
            'fa' => 'circle-thin',
            'color' => '#000',
        ];
        case 'bullet_empty_grey': return [
            'alt' => '&nbsp;',
            'size' => [9, 9],
            'xy' => [112, 176],
            'fa' => 'circle-thin',
            'color' => '#999',
        ];
        case 'bullet_blue': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [32, 192],
            'fa' => 'circle',
            'color' => '#00F',
        ];
        case 'bullet_light_blue': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [32, 192],
            'fa' => 'circle',
            'color' => '#5bc0de',
        ];
        case 'bullet_dark_blue': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [32, 192],
            'fa' => 'circle',
            'color' => '#337ab7',
        ];
        case 'bullet_cyan': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [32, 192],
            'fa' => 'circle',
            'color' => '#00FFFF',
        ];
        case 'bullet_red': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [48, 192],
            'fa' => 'circle',
            'color' => '#F00',
        ];
        case 'bullet_orange': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [64, 192],
            'fa' => 'circle',
            'color' => '#F60',
        ];
        case 'bullet_redorange': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [64, 192],
            'fa' => 'circle',
            'color' => '#FF8000',
        ];
        case 'bullet_green': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [80, 192],
            'fa' => 'circle',
            'color' => '#5cb85c',
        ];
        case 'bullet_yellow': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [96, 192],
            'fa' => 'circle',
            'color' => '#FFF000',
        ];
        case 'bullet_brown': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [112, 192],
            'fa' => 'circle',
            'color' => '#900',
        ];
        case 'bullet_white': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [0, 192],
            'fa' => 'circle-thin',
            'color' => '#CCC',
        ];
        case 'bullet_gray': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [0, 192],
            'fa' => 'circle',
            'color' => '#BBB',
        ];
        case 'bullet_magenta': return [
            'alt' => '&bull;',
            'size' => [9, 9],
            'xy' => [16, 192],
            'fa' => 'circle',
            'color' => '#c90dc9',
        ];

        case 'activate': return [
            'alt' => /* TRANS: Short for "Activate(d)" */ T_('Act.'),
            'legend' => T_('Activate'),
            'size' => [16, 16],
            'xy' => [64, 96],
            'fa' => 'toggle-off',
        ];
        case 'deactivate': return [
            'alt' => /* TRANS: Short for "Deactivate(d)" */ T_('Deact.'),
            'legend' => T_('Deactivate'),
            'size' => [16, 16],
            'xy' => [80, 96],
            'fa' => 'toggle-on',
        ];
        case 'enabled': return [
            'alt' => /* TRANS: Short for "Activate(d)" */ T_('Act.'),
            'legend' => T_('Activated'),
            'size' => [9, 9],
            'xy' => [96, 176],
            'fa' => 'circle',
            'color' => '#000',
        ];
        case 'disabled': return [
            'alt' => /* TRANS: Short for "Deactivate(d)" */ T_('Deact.'),
            'legend' => T_('Deactivated'),
            'size' => [9, 9],
            'xy' => [112, 176],
            'fa' => 'circle-thin',
            'color' => '#000',
        ];

        case 'link': return [
            /* TRANS: Link + space => verb (not noun) */ 'alt' => T_('Link '),
            'size' => [14, 14],
            'xy' => [96, 96],
            'glyph' => 'paperclip',
            'fa' => 'paperclip',
        ];
        case 'unlink': return [
            'alt' => T_('Unlink'),
            'size' => [14, 14],
            'xy' => [112, 96],
            'glyph' => 'resize-full',
            'fa' => 'unlink',
            'color' => '#F00',
        ];

        case 'help': return [
            'alt' => T_('Help'),
            'size' => [16, 16],
            'xy' => [32, 128],
            'glyph' => 'question-sign',
            'fa' => 'question-circle',
        ];
        case 'question': return [
            'size' => [16, 16],
            'xy' => [32, 128],
            'glyph' => 'question-sign',
            'fa' => 'question-circle',
            'color' => '#F90',
        ];
        case 'manual': return [
            'rollover' => true,
            'alt' => T_('Help'),
            'legend' => T_('Online manual'),
            'size' => [16, 15],
            'xy' => [128, 96],
            'glyph' => 'book',
            'fa' => 'book fa-x-rollover-orange',
        ];
        case 'permalink': return [
            'alt' => T_('Permalink'),
            'size' => [11, 13],
            'xy' => [0, 128],
            'glyph' => 'file',
            'fa' => 'external-link-square',
        ];
        case 'history': return [
            'alt' => T_('History'),
            'size' => [15, 15],
            'xy' => [144, 48],
            'glyph' => 'time',
            'fa' => 'clock-o',
        ];

        case 'file_allowed': return [
            'alt' => T_('Allowed for all users'),
            'size' => [16, 14],
            'xy' => [96, 112],
            'glyph' => 'lock',
            'fa' => 'unlock',
            'color' => '#0F0',
        ];
        case 'file_allowed_registered': return [
            'alt' => T_('Allowed for registered users'),
            'size' => [12, 16],
            'xy' => [112, 112],
            'glyph' => 'lock',
            'fa' => 'lock',
            'color' => '#ffc634',
        ];
        case 'file_not_allowed': return [
            'alt' => T_('Allowed only for administrators'),
            'size' => [11, 14],
            'xy' => [128, 112],
            'glyph' => 'lock',
            'fa' => 'lock',
            'color' => '#F00',
        ];

        case 'comments': return [
            'alt' => T_('Comments'),
            'size' => [15, 16],
            'xy' => [0, 112],
            'glyph' => 'comment',
            'fa' => 'comment',
        ];
        case 'nocomment': return [
            'alt' => T_('No comment'),
            'size' => [15, 16],
            'xy' => [16, 112],
            'glyph' => 'comment',
            'fa' => 'comment-o',
            'color' => '#CCC',
            'color-fa' => 'default',
        ];

        case 'move_up_blue':
        case 'move_up': return [
            'rollover' => true,
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [96, 80],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up fa-x-rollover-orange',
            'color' => '#468cd0',
        ];
        case 'move_down_blue':
        case 'move_down': return [
            'rollover' => true,
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [64, 80],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down fa-x-rollover-orange',
            'color' => '#468cd0',
        ];
        case 'nomove_up': return [
            'alt' => T_('Sort by order'),
            'size' => [12, 13],
            'xy' => [144, 80],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up',
            'color' => '#8d8985',
        ];
        case 'nomove_down': return [
            'alt' => T_('Sort by order'),
            'size' => [12, 13],
            'xy' => [128, 80],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down',
            'color' => '#8d8985',
        ];
        case 'nomove': return [
            'size' => [12, 13],
            'xy' => [0, 0],
        ];
        case 'move_left': return [
            'rollover' => true,
            'alt' => T_('Left'),
            'size' => [13, 12],
            'xy' => [0, 96],
            'glyph' => 'arrow-left',
            'fa' => 'arrow-left fa-x-rollover-orange',
            'color' => '#468cd0',
        ];
        case 'move_right': return [
            'rollover' => true,
            'alt' => T_('Right'),
            'size' => [13, 12],
            'xy' => [32, 96],
            'glyph' => 'arrow-right',
            'fa' => 'arrow-right fa-x-rollover-orange',
            'color' => '#468cd0',
        ];
        case 'move_down_orange': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [80, 80],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down',
            'color' => '#ff9e00',
        ];
        case 'move_up_orange': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [112, 80],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up',
            'color' => '#ff9e00',
        ];
        case 'move_down_green': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [64, 240],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down',
            'color' => '#5eef27',
        ];
        case 'move_up_green': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [80, 240],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up',
            'color' => '#5eef27',
        ];
        case 'move_down_magenta': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [96, 240],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down',
            'color' => '#ee009d',
        ];
        case 'move_up_magenta': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [112, 240],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up',
            'color' => '#ee009d',
        ];
        case 'move_down_grey': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [128, 240],
            'glyph' => 'arrow-down',
            'fa' => 'arrow-down',
            'color' => '#303030',
        ];
        case 'move_up_grey': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [144, 240],
            'glyph' => 'arrow-up',
            'fa' => 'arrow-up',
            'color' => '#303030',
        ];

        case 'check_all': return [
            'alt' => T_('Check all'),
            'size' => [16, 16],
            'xy' => [32, 112],
            'glyph' => 'check',
            'fa' => 'check-square-o',
        ];
        case 'uncheck_all': return [
            'alt' => T_('Uncheck all'),
            'size' => [16, 16],
            'xy' => [48, 112],
            'glyph' => 'unchecked',
            'fa' => 'square-o',
        ];

        case 'filter': return [
            'alt' => T_('Filter'),
            'size' => [1, 1],
            'xy' => [0, 0],
            'glyph' => 'filter',
            'fa' => 'filter',
        ];
        case 'reset_filters': return [
            'alt' => T_('Reset all filters'),
            'size' => [16, 16],
            'xy' => [144, 112],
            'glyph' => 'filter',
            'fa' => 'filter',
        ];

        case 'allowback': return [
            'alt' => T_('Allow back'),
            'size' => [13, 13],
            'xy' => [48, 128],
            'glyph' => 'ok',
            'fa' => 'check',
            'color' => '#0C0',
        ];
        case 'ban': return [
            'alt' => /* TRANS: Abbrev. */ T_('Ban'),
            'size' => [13, 13],
            'xy' => [112, 128],
            'glyph' => 'ban-circle',
            'fa' => 'ban fa-x-rollover-grey',
            'color' => '#C00',
        ];
        case 'ban_disabled': return [
            'rollover' => true,
            'alt' => T_('Ban'),
            'size' => [13, 13],
            'xy' => [96, 128],
            'glyph' => 'ban-circle',
            'fa' => 'ban fa-x-rollover-red',
            'color' => '#7e7e7e',
        ];
        case 'play': return [
            // used to write an e-mail, visit site or contact through IM
            'alt' => '&gt;',
            'size' => [14, 14],
            'xy' => [80, 128],
            'glyph' => 'play',
            'fa' => 'play',
        ];
        case 'pause': return [
            // used to pause automation
            'alt' => '||',
            'size' => [14, 14],
            'xy' => [64, 128],
            'glyph' => 'pause',
            'fa' => 'pause',
        ];
        case 'rewind': return [
            // Used to resend email campaign ( rewind user back to previous state )
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'rewind',
            'fa' => 'backward',
        ];
        case 'forward': return [
            // Used to skip email campaign for user
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'forward',
            'fa' => 'forward',
        ];

        case 'feed': return [
            'alt' => T_('XML Feed'),
            'size' => [16, 16],
            'xy' => [0, 176],
            'fa' => 'rss-square',
            'color' => '#F90',
        ];

        case 'recycle_full': return [
            'alt' => T_('Open recycle bin'),
            'size' => [16, 16],
            'xy' => [64, 112],
            'glyph' => 'trash',
            'fa' => 'trash-o',
            'color-fa' => '#F00',
        ];
        case 'recycle_empty': return [
            'alt' => T_('Empty recycle bin'),
            'size' => [16, 16],
            'xy' => [80, 112],
            'glyph' => 'trash',
            'fa' => 'trash-o',
            'color' => '#CCC',
            'color-fa' => '#000',
        ];

        case 'vote_spam': return [
            'alt' => T_('Cast a spam vote!'),
            'size' => [15, 15],
            'xy' => [16, 144],
            'fa' => 'thumbs-o-down',
            'color' => '#C00',
        ];
        case 'vote_spam_disabled': return [
            'alt' => T_('Cast a spam vote!'),
            'size' => [15, 15],
            'xy' => [0, 144],
            'fa' => 'thumbs-o-down fa-x-rollover-red',
            'color' => '#333',
        ];
        case 'vote_notsure': return [
            'alt' => T_('Cast a "not sure" vote!'),
            'size' => [15, 15],
            'xy' => [48, 144],
            'fa' => 'question-circle',
            'color' => '#000',
        ];
        case 'vote_notsure_disabled': return [
            'alt' => T_('Cast a "not sure" vote!'),
            'size' => [15, 15],
            'xy' => [32, 144],
            'fa' => 'question-circle fa-x-rollover-black',
            'color' => '#666',
        ];
        case 'vote_ok': return [
            'alt' => T_('Cast an OK vote!'),
            'size' => [15, 15],
            'xy' => [80, 144],
            'fa' => 'thumbs-o-up',
            'color' => '#0C0',
        ];
        case 'vote_ok_disabled': return [
            'alt' => T_('Cast an OK vote!'),
            'size' => [15, 15],
            'xy' => [64, 144],
            'fa' => 'thumbs-o-up fa-x-rollover-green',
            'color' => '#333',
        ];

        case 'thumb_up': return [
            'alt' => T_('Thumb Up'),
            'size' => [15, 15],
            'xy' => [112, 144],
            'glyph' => 'thumbs-up',
            'fa' => 'thumbs-up fa-x-rollover-grey',
            'color' => '#0C0',
        ];
        case 'thumb_up_disabled': return [
            'rollover' => true,
            'alt' => T_('Thumb Up'),
            'size' => [15, 15],
            'xy' => [96, 144],
            'glyph' => 'thumbs-up',
            'fa' => 'thumbs-up fa-x-rollover-green',
            'color' => '#7f7f7f',
        ];
        case 'thumb_down': return [
            'alt' => T_('Thumb Down'),
            'size' => [15, 15],
            'xy' => [144, 144],
            'glyph' => 'thumbs-down',
            'fa' => 'thumbs-down fa-x-rollover-grey',
            'color' => '#ee2a2a',
        ];
        case 'thumb_down_disabled': return [
            'rollover' => true,
            'alt' => T_('Thumb Down'),
            'size' => [15, 15],
            'xy' => [128, 144],
            'glyph' => 'thumbs-down',
            'fa' => 'thumbs-down fa-x-rollover-red-light',
            'color' => '#7f7f7f',
        ];

        case 'thumb_arrow_up': return [
            'alt' => T_('Thumb Up'),
            'size' => [12, 13],
            'xy' => [80, 240],
            'glyph' => 'arrow-up',
            'fa' => 'caret-up fa-x-rollover-grey',
            'color' => '#0C0',
        ];
        case 'thumb_arrow_up_disabled': return [
            'alt' => T_('Thumb Up'),
            'size' => [12, 13],
            'xy' => [144, 240],
            'glyph' => 'arrow-up',
            'fa' => 'caret-up fa-x-rollover-green',
            'color' => '#7f7f7f',
        ];
        case 'thumb_arrow_down': return [
            'alt' => T_('Thumb Down'),
            'size' => [12, 13],
            'xy' => [80, 80],
            'glyph' => 'arrow-down',
            'fa' => 'caret-down fa-x-rollover-grey',
            'color' => '#ee2a2a',
        ];
        case 'thumb_arrow_down_disabled': return [
            'alt' => T_('Thumb Down'),
            'size' => [12, 13],
            'xy' => [128, 240],
            'glyph' => 'arrow-down',
            'fa' => 'caret-down fa-x-rollover-red-light',
            'color' => '#7f7f7f',
        ];

        case 'flag_on': return [
            'rollover' => true,
            'alt' => '',
            'size' => [16, 16],
            'xy' => [0, 208],
            'glyph' => 'flag',
            'fa' => 'flag fa-x--hover',
            'color' => '#FAA72D',
        ];
        case 'flag_off': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [16, 208],
            'glyph' => 'flag',
            'fa' => 'flag-o fa-x--hover',
            'color' => '#7f7f7f',
        ];

        case 'magnifier': return [
            'alt' => T_('Log as a search instead'),
            'size' => [14, 13],
            'xy' => [16, 176],
            'glyph' => 'search',
            'fa' => 'search',
        ];

        case 'add': return [
            'alt' => T_('Add'),
            'size' => [16, 16],
            'xy' => [32, 224],
            'glyph' => 'plus-sign',
            'fa' => 'plus-circle',
            'color' => '#0c0',
        ];
        case 'add__yellow': return [
            'alt' => T_('Add'),
            'size' => [16, 16],
            'xy' => [32, 224],
            'glyph' => 'plus-sign',
            'fa' => 'plus-circle',
            'color' => '#fc0',
        ];
        case 'add__blue': return [
            'alt' => T_('Add'),
            'size' => [16, 16],
            'xy' => [32, 224],
            'glyph' => 'plus-sign',
            'fa' => 'plus-circle',
            'color' => '#337ab7',
        ];
        case 'add__cyan': return [
            'alt' => T_('Add'),
            'size' => [16, 16],
            'xy' => [32, 224],
            'glyph' => 'plus-sign',
            'fa' => 'plus-circle',
            'color' => '#60b9e1',
        ];

        case 'add__yellow': return [
            'alt' => T_('Add'),
            'size' => [16, 16],
            'xy' => [32, 224],
            'glyph' => 'plus-sign',
            'fa' => 'plus-circle',
            'color' => '#fc0',
        ];

        case 'minus': return [
            'alt' => T_('Remove'),
            'size' => [16, 16],
            'xy' => [48, 224],
            'glyph' => 'minus-sign',
            'fa' => 'minus-circle',
            'color' => '#C00',
        ];

        case 'multi_action': return [
            'alt' => T_('Action for selected elements'),
            'size' => [16, 16],
            'xy' => [112, 224],
            'fa' => 'level-up fa-rotate-90',
        ];

        case 'rotate_right': return [
            'alt' => T_('Rotate this picture 90&deg; to the right'),
            'size' => [15, 16],
            'xy' => [64, 224],
            'fa' => 'share',
        ];
        case 'rotate_left': return [
            'alt' => T_('Rotate this picture 90&deg; to the left'),
            'size' => [15, 16],
            'xy' => [80, 224],
            'fa' => 'reply',
        ];
        case 'rotate_180': return [
            'alt' => T_('Rotate this picture 180&deg;'),
            'size' => [14, 16],
            'xy' => [96, 224],
            'fa' => 'undo fa-flip-horizontal',
        ];
        case 'flip_horizontal': return [
            'alt' => T_('Flip this picture horizontally'),
            'size' => [15, 15],
            'xy' => [125, 254],
            'fa' => 'sort fa-rotate-90',
        ];
        case 'flip_vertical': return [
            'alt' => T_('Flip this picture vertically'),
            'size' => [15, 15],
            'xy' => [144, 255],
            'fa' => 'sort',
        ];
        case 'crop': return [
            'alt' => T_('Crop this picture'),
            'size' => [16, 16],
            'xy' => [0, 80],
            'fa' => 'crop',
        ];

        case 'notification': return [
            'alt' => T_('Email notification'),
            'size' => [15, 12],
            'xy' => [16, 0],
            'glyph' => 'envelope',
            'fa' => 'envelope-square',
        ];

        case 'post': return [
            'alt' => T_('Post'),
            'size' => [15, 15],
            'xy' => [144, 16],
            'glyph' => 'file',
            'fa' => 'file',
        ];

        case 'stop': return [
            'alt' => T_('Stop'),
            'size' => [16, 16],
            'xy' => [64, 128],
            'fa' => 'hand-paper-o',
            'color' => '#C00',
        ];

        case 'stop_square': return [
            'alt' => T_('Stop'),
            'size' => [16, 16],
            'xy' => [64, 128],
            'glyph' => 'stop',
            'fa' => 'stop',
            'color' => '#C00',
        ];

        case 'lightning': return [
            'alt' => T_('Kill spam'),
            'size' => [10, 16],
            'xy' => [0, 32],
            'fa' => 'flash',
            'color' => '#ff9900',
        ];

        case 'page_cache_on': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [128, 32],
            'fa' => 'file-code-o',
            'color' => '#F90',
        ];
        case 'page_cache_off': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [128, 32],
            'fa' => 'bolt',
            'color' => '#000',
        ];

        case 'block_cache_on': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [128, 32],
            'fa' => 'cube',
            'color' => '#F90',
        ];
        case 'block_cache_off': return [
            'alt' => '',
            'size' => [10, 16],
            'xy' => [0, 32],
            'fa' => 'bolt',
            'color' => '#000',
        ];
        case 'block_cache_disabled': return [
            'alt' => '',
            'size' => [10, 16],
            'xy' => [0, 32],
            'fa' => 'bolt',
            'color' => '#CCC',
        ];
        case 'block_cache_denied': return [
            'alt' => '',
            'size' => [10, 16],
            'xy' => [0, 32],
            'fa' => 'bolt',
            'color' => '#ff9900',
        ];

        case 'star_on': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [0, 208],
            'fa' => 'star',
            'color' => '#FC0',
        ];
        case 'star_off': return [
            'alt' => '',
            'size' => [16, 16],
            'xy' => [16, 208],
            'fa' => 'star-o',
            'color' => '#999',
        ];

        case 'elevate': return [
            // Elevate comment into a post
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'fa' => 'newspaper-o',
        ];

        case 'coll_default': return [
            // Default collection to display
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'fa' => 'compass',
            'color' => '#F90',
        ];
        case 'coll_info': return [
            // Collection used for info pages
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'fa' => 'info-circle',
            'color' => '#F90',
        ];
        case 'coll_login': return [
            // Collection used for login
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'fa' => 'check-circle',
            'color' => '#F90',
        ];
        case 'coll_message': return [
            // Collection used for messaging
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'fa' => 'comments',
            'color' => '#F90',
        ];

        case 'merge': return [
            'alt' => T_('Merge '),
            'size' => [14, 14],
            'xy' => [96, 96],
            'glyph' => 'link',
            'fa' => 'link',
        ];

        case 'tag': return [
            'alt' => T_('Tag'),
            'size' => [11, 13],
            'xy' => [0, 128],
            'glyph' => 'tag',
            'fa' => 'tag',
        ];

        case 'designer_widget_up': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [96, 80],
            'glyph' => 'circle-arrow-up',
            'fa' => 'angle-up',
        ];
        case 'designer_widget_top': return [
            'alt' => T_('Up'),
            'size' => [12, 13],
            'xy' => [96, 80],
            'glyph' => 'circle-arrow-up',
            'fa' => 'angle-double-up',
        ];
        case 'designer_widget_down': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [64, 80],
            'glyph' => 'circle-arrow-down',
            'fa' => 'angle-down',
        ];
        case 'designer_widget_bottom': return [
            'alt' => T_('Down'),
            'size' => [12, 13],
            'xy' => [64, 80],
            'glyph' => 'circle-arrow-down',
            'fa' => 'angle-double-down',
        ];
        case 'designer_widget_list': return [
            'alt' => T_('List'),
            'size' => [15, 15],
            'xy' => [144, 16],
            'glyph' => 'list',
            'fa' => 'list',
        ];

        case 'asterisk': return [
            'alt' => '',
            'size' => [0, 0],
            'xy' => [0, 0],
            'glyph' => 'asterisk',
            'fa' => 'asterisk',
        ];
    }
}
