<?php
/**
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2014 by Emin Ozlen - {@link http://eodepo.com/}
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Specific code for this skin.
 *
 * ATTENTION: if you make a new skin you have to change the class name below accordingly
 */
class pinit_Skin extends Skin
{
    public $version = '1.2.2';

    /**
     * Get default name for the skin.
     * Note: the admin can customize it.
     */
    public function get_default_name()
    {
        return 'Pinit';
    }

    /**
     * Get default type for the skin.
     */
    public function get_default_type()
    {
        return 'normal';
    }

    /*	function eo_more_link() {
            global $Item;
            echo "moore";
            var_dump($Item);
        }*/
    public function eo_remove_headline($type, $file)
    {
        global $headlines,$rsc_url,$app_version;
        switch ($type) {
            case 'js':
                $file_uri = url_add_param($rsc_url . 'js/' . $file . '.js', 'v=' . $app_version);
                $line = '<script type="text/javascript" src="' . $file_uri . '"></script>';
                if (($k = array_search($line, $headlines)) !== false) {
                    unset($headlines[$k]);
                }
                break;
            case 'css':
                $file_uri = url_add_param($rsc_url . 'css/' . $file . '.css', 'v=' . $app_version);
                $line = '<link rel="stylesheet" type="text/css" href="' . $file_uri . '" />';
                if (($k = array_search($line, $headlines)) !== false) {
                    unset($headlines[$k]);
                }
                break;
        }
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param local params like 'for_editing' => true
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'col_type' => [
                'label' => T_('Columns'),
                'note' => 'Choose # of columns for Large - Medium - Small Devices',
                'defaultvalue' => 'colt1',
                'options' => [
                    'colt1' => $this->T_('4-3-2'),
                    'colt2' => $this->T_('3-2-2'),
                    'colt3' => $this->T_('2-2-1'),
                ],
                'type' => 'select',
            ],
            /*
                'hide_sb_arc' => array(
                    'label' => T_('Hide Sidebar on posts display'),
                    'note' => T_('Load more posts automatically via ajax'),
                    'defaultvalue' => 1,
                    'type'	=>	'checkbox',
                ),*/
            /*'link_color' => array(
                    'label' => T_('Link Color'),
                    'note' => T_('Ex: #ff0000 for red'),
                    'defaultvalue' => '#ff0000',
                    'valid_pattern' => array( 'pattern'=>'¤^(#([a-f0-9]{3}){1,2})?$¤i'),
                    'error'=>T_('Invalid color code.'),
                ),*/
            'inf_scroll' => [
                'label' => T_('Infinite Scroll'),
                'note' => T_('Load more posts automatically via ajax'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
            'inf_scr_mode' => [
                'label' => T_('Infinite Scroll Mode'),
                'note' => 'Choose to load more posts automatically or on demand',
                'defaultvalue' => 'manual',
                'options' => [
                    'auto' => $this->T_('Auto'),
                    'manual' => $this->T_('Manual'),
                ],
                'type' => 'select',
            ],
            /*'no_foot' => array(
                    'label' => T_('Disable Footer'),
                    'note' => T_('Check to disable footer (you may particularly want to disable footer if you use infinite scroll)'),
                    'defaultvalue' => 0,
                    'type'	=>	'checkbox',
                ),*/
            /*'colorbox' => array(
                    'label' => T_('Colorbox Image Zoom'),
                    'note' => T_('Check to enable javascript zooming on images (using the colorbox script)'),
                    'defaultvalue' => 1,
                    'type'	=>	'checkbox',
                ),*/
            'disp_b2_credits' => [
                'label' => T_('b2evo credits'),
                'note' => T_('Display b2evolution credit links in footer'),
                'defaultvalue' => 1,
                'type' => 'checkbox',
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    public function eo_get_tinyurl($pid = null)
    {
        global $Item;
        $tinyurl = $Item->get_tinyurl();
        return $tinyurl;
    }

    public function eo_get_featured_image_url($size = null)
    {
        global $Item;
        // Get list of attached files
        $feat_img_uri = false;
        $params = [
            'before' => '',
            'before_image' => '',
            'before_image_legend' => '',
            'after_image_legend' => '',
            'after_image' => '',
            'after' => '',
            'limit' => 1,
            'image_link_to' => 'original',
            'before_gallery' => '',
            'after_gallery' => '',
            // Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
            'restrict_to_image_position' => 'teaser',
        ];
        if (isset($size)) {
            $params['image_size'] = $size;
        }
        $post_images = $Item->get_images($params, 'raw');
        if (! empty($post_images)) {
            preg_match('/(src)=("[^"]*")/', $post_images, $img_parts);
            $feat_img_uri = str_replace(['src="', '"', '&amp;'], ['', '', '&'], $img_parts[0]);
        }
        return $feat_img_uri;

        /*if( $app_version < 5 ) {
            if( ! $FileList = $Item->get_attachment_FileList(1, 'teaser') )
            {
                return '';
            }

            $r = '';
            $File = NULL;
            while( $File = & $FileList->get_next() )
            {
                if( $File->is_image() ) {
                    $feat_img_uri = $File->_FileRoot->ads_url . $File->_rdfp_rel_path;
            //		var_dump($feat_img_uri);
                }
            }
        }*/
    }

    /**
     * Get ready for displaying the skin.
     *
     * This may register some CSS or JS...
     */
    public function display_init()
    {
        global $Blog, $disp, $Item, $baseurl, $skins_url, $Chapter, $ReqURI,$app_version,$Skin,$skin ,$xmlsrv_url, $htsrv_url, $MainList, $paged,$rsc_url, $app_version;
        //  var_dump($headlines);
        //	$thumbnail_sizes['fit-440x600'] = array('fit',440,600,90);
        // call parent:

        // Request some common features that the parent function (Skin::display_init()) knows how to provide:
        parent::display_init([
            'jquery',                  // Load jQuery
            'font_awesome',            // Load Font Awesome (and use its icons as a priority over the Bootstrap glyphicons)
        ]);

        require_css('css/bootstrap.min.css', 'relative');
        require_css('css/bootstrap-theme.min.css', 'relative');
        require_css('css/font-awesome.min.css', 'relative');
        require_css('css/style.css', 'relative');
        //require_css( 'css/options.php', 'relative' );
        require_css('css/override.css', 'relative');

        // Skin specific initializations:

        //require_css( 'css/options.php', 'relative' );
        require_css('css/override.css', 'relative');

        add_js_for_toolbar('blog');		// Registers all the javascripts needed by the toolbar menu

        $ajax_js = $rsc_url . 'js/ajax.js';
        if (file_exists($ajax_js)) {
            require_js('ajax.js', 'blog');
        }	// Functions to work with AJAX response data
        //init_bubbletip_js( 'blog' ); // Add jQuery bubbletip plugin
        // CSS for IE9
        add_headline('<!--[if IE 9 ]>');
        require_css('ie9.css', 'rsc_url');
        add_headline('<![endif]-->');

        add_headline("<link href='http://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>");

        $Skin->eo_remove_headline('js', 'jquery.min');

        $Blog->disp('blog_css', 'raw');
        $Blog->disp('user_css', 'raw');
        if (method_exists($Blog, 'disp_setting')) {
            $Blog->disp_setting('head_includes', 'raw');
        }

        parent::display_init();
        $Skin->arc_disp_arr = ['posts', 'front', 'search'];
        $Skin->feat_post_typs = [
            'Intro-Main' => 1500,
            'Intro-Cat' => 1520,
            'Intro-Tag' => 1530,
            'Intro-Sub' => 1570,
            'Intro-All' => 1600,
        ];

        $Skin->skinuri = $skins_url . $skin . '/';
        $this->curruri = $_SERVER['HTTP_HOST'] . $ReqURI;
        //		var_dump($Skin);

        //	$is4x = version_compare( $app_version, '5', '<' ) && version_compare($app_version, '4', ">=");
        $is5x = version_compare($app_version, '5', '>');
        $isless5x = version_compare($app_version, '5', '<');
        //	var_dump($Blog);
        //	($is5x) ? $blogdesc = $Blog->longdesc : $blogdesc = $Blog->description;
        $blogdesc = $Blog->longdesc;
        if ($isless5x) {
            require_css('basic.css');
        }
        $og_uri = $_SERVER['HTTP_HOST'] . $ReqURI;
        $this->og_img = false;
        if (isset($Item) && ! empty($Item)) {
            $id = $Item->ID;
            $og_uri = $baseurl . 'index.php?p=' . $id . '&amp;blog=' . $Blog->ID . '&amp;redir=no';
            $og_desc = $Item->excerpt;
            $og_title = $Item->title;
            $this->og_img = $this->eo_get_featured_image_url();
        } elseif (isset($Chapter) && ! empty($Chapter)) {
            //if we are on category
            $og_desc = $Chapter->description;
            $og_title = $Chapter->name;
        } else {
            // Just in case, for everything else.
            $og_desc = $blogdesc;
            $og_title = $Blog->name;
        }
        //	$id = $Item->ID;
        add_headline('<meta property="og:type" content="article">');
        add_headline('<meta property="og:description" content="' . $og_desc . '">');
        add_headline('<meta property="og:url" content="' . $og_uri . '">');
        add_headline('<meta property="og:title" content="' . $og_title . '">');
        if ($this->og_img) {
            add_headline('<meta property="og:image" content="' . $this->og_img . '">');
        }

        // Add CSS:
        require_css('basic_styles.css', 'blog'); // the REAL basic styles
        require_css('basic.css', 'blog'); // Basic styles
        require_css('blog_base.css', 'blog'); // Default styles for the blog navigation
        require_css('item_base.css', 'blog'); // Default styles for the post CONTENT

        // Colorbox (a lightweight Lightbox alternative) allows to zoom on images and do slideshows with groups of images:

        require_js_helper('colorbox');
    }
}

/*
    Line 1844: 				$disp_param = 'posts';
    Line 1848: 				$disp_param = 'comments';
    Line 1852: 				$disp_param = 'search';
    Line 1856: 				$disp_param = 'arcdir';
    Line 1860: 				$disp_param = 'catdir';
    Line 1864: 				$disp_param = 'postidx';
    Line 1868: 				$disp_param = 'mediaidx';
    Line 1872: 				$disp_param = 'sitemap';
    Line 1876: 				$disp_param = 'msgform';
    Line 1883: 				$disp_param = 'users';
    Line 1893: 				$disp_param = 'threads';
    Line 1897: 				$disp_param = 'contacts';
    Line 1907: 					$disp_param = 'help';
    Line 1985: 		if( !empty( $disp_param ) )
    Line 1987: 			if( $this->get_setting( 'front_disp' ) == $disp_param )
    Line 1993: 				return url_add_param( $this->gen_blogurl(), 'disp='.$disp_param );*/
