<?php
/**
 * This file implements creation of DB tables
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2005 by Francois PLANQUE - {@link http://fplanque.net/}
 * Parts of this file are copyright (c)2004 by Vegar BERG GULDAL - {@link http://funky-m.com/}
 * Parts of this file are copyright (c)2005 by Jason EDGECOMBE
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 * {@internal
 * b2evolution is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * b2evolution is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with b2evolution; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * }}
 *
 * {@internal
 * Daniel HAHLER grants Francois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 *
 * Vegar BERG GULDAL grants Francois PLANQUE the right to license
 * Vegar BERG GULDAL's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 *
 * Jason EDGECOMBE grants Francois PLANQUE the right to license
 * Jason EDGECOMBE's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package install
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author blueyed: Daniel HAHLER.
 * @author fplanque: Francois PLANQUE.
 * @author vegarg: Vegar BERG GULDAL.
 * @author edgester: Jason EDGECOMBE.
 *
 * @version $Id$
 */
if( !defined('EVO_CONFIG_LOADED') ) die( 'Please, do not access this page directly.' );

/**
 * Create b2 tables.
 *
 * Used for fresh install + upgrade from b2
 *
 * {@internal create_b2evo_tables(-)}}
 */
function create_b2evo_tables()
{
	global $baseurl, $new_db_version, $DB;


	create_groups();


	echo 'Creating table for Settings... ';
	$query = "CREATE TABLE T_settings (
		set_name VARCHAR( 30 ) NOT NULL ,
		set_value VARCHAR( 255 ) NULL ,
		PRIMARY KEY ( set_name )
		)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Users... ';
	$query = "CREATE TABLE T_users (
		ID int(10) unsigned NOT NULL auto_increment,
		user_login varchar(20) NOT NULL,
		user_pass CHAR(32) NOT NULL,
		user_firstname varchar(50) NOT NULL,
		user_lastname varchar(50) NOT NULL,
		user_nickname varchar(50) NOT NULL,
		user_icq int(10) unsigned DEFAULT '0' NOT NULL,
		user_email varchar(100) NOT NULL,
		user_url varchar(100) NOT NULL,
		user_ip varchar(15) NOT NULL,
		user_domain varchar(200) NOT NULL,
		user_browser varchar(200) NOT NULL,
		dateYMDhour datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		user_level int unsigned DEFAULT 0 NOT NULL,
		user_aim varchar(50) NOT NULL,
		user_msn varchar(100) NOT NULL,
		user_yim varchar(50) NOT NULL,
		user_locale varchar(20) DEFAULT 'en-EU' NOT NULL,
		user_idmode varchar(20) NOT NULL DEFAULT 'login',
		user_notify tinyint(1) NOT NULL default 1,
		user_showonline tinyint(1) NOT NULL default 1,
		user_grp_ID int(4) NOT NULL default 1,
		PRIMARY KEY user_ID (ID),
		UNIQUE user_login (user_login),
		KEY user_grp_ID (user_grp_ID)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Blogs... ';
	$query = "CREATE TABLE T_blogs (
		blog_ID int(11) unsigned NOT NULL auto_increment,
		blog_shortname varchar(12) NULL default '',
		blog_name varchar(50) NOT NULL default '',
		blog_tagline varchar(250) NULL default '',
		blog_description varchar(250) NULL default '',
		blog_longdesc TEXT NULL DEFAULT NULL,
		blog_locale VARCHAR(20) NOT NULL DEFAULT 'en-EU',
		blog_access_type VARCHAR(10) NOT NULL DEFAULT 'index.php',
		blog_siteurl varchar(120) NOT NULL default '',
		blog_staticfilename varchar(30) NULL default NULL,
		blog_stub VARCHAR(255) NOT NULL DEFAULT 'stub',
		blog_urlname VARCHAR(255) NOT NULL DEFAULT 'urlname',
		blog_notes TEXT NULL,
		blog_keywords tinytext,
		blog_allowcomments VARCHAR(20) NOT NULL default 'post_by_post',
		blog_allowtrackbacks TINYINT(1) NOT NULL default 1,
		blog_allowpingbacks TINYINT(1) NOT NULL default 1,
		blog_allowblogcss TINYINT(1) NOT NULL default 1,
		blog_allowusercss TINYINT(1) NOT NULL default 1,
		blog_pingb2evonet TINYINT(1) NOT NULL default 0,
		blog_pingtechnorati TINYINT(1) NOT NULL default 0,
		blog_pingweblogs TINYINT(1) NOT NULL default 0,
		blog_pingblodotgs TINYINT(1) NOT NULL default 0,
		blog_default_skin VARCHAR(30) NOT NULL DEFAULT 'custom',
		blog_force_skin TINYINT(1) NOT NULL default 0,
		blog_disp_bloglist TINYINT(1) NOT NULL DEFAULT 1,
		blog_in_bloglist TINYINT(1) NOT NULL DEFAULT 1,
		blog_links_blog_ID INT(4) NOT NULL DEFAULT 0,
		blog_commentsexpire INT(4) NOT NULL DEFAULT 0,
		blog_media_location ENUM( 'default', 'subdir', 'custom' ) DEFAULT 'default' NOT NULL,
		blog_media_subdir VARCHAR( 255 ) NOT NULL,
		blog_media_fullpath VARCHAR( 255 ) NOT NULL,
		blog_media_url VARCHAR( 255 ) NOT NULL,
		blog_UID VARCHAR(20),
		PRIMARY KEY blog_ID (blog_ID),
		UNIQUE KEY blog_urlname (blog_urlname)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Categories... ';
	$query="CREATE TABLE T_categories (
		cat_ID int(11) unsigned NOT NULL auto_increment,
		cat_parent_ID int(11) unsigned NULL,
		cat_name tinytext NOT NULL,
		cat_blog_ID int(11) unsigned NOT NULL default 2,
		cat_description VARCHAR(250) NULL DEFAULT NULL,
		cat_longdesc TEXT NULL DEFAULT NULL,
		cat_icon VARCHAR(30) NULL DEFAULT NULL,
		PRIMARY KEY cat_ID (cat_ID),
		KEY cat_blog_ID (cat_blog_ID),
		KEY cat_parent_ID (cat_parent_ID)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Posts... ';
	$query = "CREATE TABLE T_posts (
		ID 										int(10) unsigned NOT NULL auto_increment,
		post_parent_ID				int(11) unsigned NULL,
		post_creator_user_ID	int(11) unsigned NOT NULL,
		post_lastedit_user_ID	int(11) unsigned NULL,
		post_assigned_user_ID	int(11) unsigned NULL,
		post_datestart				datetime NOT NULL,
		post_datedeadline 		datetime NULL,
		post_datecreated			datetime NULL,
		post_datemodified			datetime NOT NULL,
		post_status						enum('published','deprecated','protected','private','draft')
														NOT NULL default 'published',
		post_pst_ID						int(11) unsigned NULL,
		post_ptyp_ID					int(11) unsigned NULL,
		post_locale						VARCHAR(20) NOT NULL DEFAULT 'en-EU',
		post_content					text NOT NULL,
		post_title						text NOT NULL,
		post_urltitle					VARCHAR(50) NULL DEFAULT NULL,
		post_url							VARCHAR(250) NULL DEFAULT NULL,
		post_main_cat_ID			int(11) unsigned NOT NULL,
		post_flags						SET( 'pingsdone', 'imported'),
		post_views						INT(11) UNSIGNED NOT NULL DEFAULT 0,
		post_wordcount				int(11) default NULL,
		post_comments					ENUM('disabled', 'open', 'closed') NOT NULL DEFAULT 'open',
		post_commentsexpire 	DATETIME DEFAULT NULL,
		post_renderers 				VARCHAR(179) NOT NULL default 'default',
		post_priority					int(11) unsigned null,
		PRIMARY KEY post_ID( ID ),
		UNIQUE post_urltitle( post_urltitle ),
		INDEX post_datestart( post_datestart ),
		INDEX post_main_cat_ID( post_main_cat_ID ),
		INDEX post_creator_user_ID( post_creator_user_ID ),
		INDEX post_status( post_status ),
		INDEX post_parent_ID( post_parent_ID ),
		INDEX post_assigned_user_ID( post_assigned_user_ID ),
		INDEX post_ptyp_ID( post_ptyp_ID ),
		INDEX post_pst_ID( post_pst_ID )
	)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Categories-to-Posts relationships... ';
	$query = "CREATE TABLE T_postcats (
		postcat_post_ID int(11) unsigned NOT NULL,
		postcat_cat_ID int(11) unsigned NOT NULL,
		PRIMARY KEY postcat_pk (postcat_post_ID,postcat_cat_ID)
	)"; // We might want to add an index on cat_ID here...
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Comments... ';
	$query = "CREATE TABLE T_comments (
		comment_ID				int(11) unsigned NOT NULL auto_increment,
		comment_post_ID		int(11) unsigned NOT NULL default '0',
		comment_type enum('comment','linkback','trackback','pingback') NOT NULL default 'comment',
		comment_status ENUM('published', 'deprecated', 'protected', 'private', 'draft') DEFAULT 'published' NOT NULL,
		comment_author_ID int unsigned NULL default NULL,
		comment_author varchar(100) NULL,
		comment_author_email varchar(100) NULL,
		comment_author_url varchar(100) NULL,
		comment_author_IP varchar(23) NOT NULL default '',
		comment_date datetime NOT NULL default '0000-00-00 00:00:00',
		comment_content text NOT NULL,
		comment_karma int(11) NOT NULL default '0',
		PRIMARY KEY comment_ID (comment_ID),
		KEY comment_post_ID (comment_post_ID),
		KEY comment_date (comment_date),
		KEY comment_type (comment_type)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Hit-Logs... ';
	$query = "CREATE TABLE T_hitlog (
							hit_ID bigint(11) NOT NULL AUTO_INCREMENT,
							hit_sess_ID INT UNSIGNED,
							hit_datetime DATETIME NOT NULL,
							hit_uri VARCHAR(250) DEFAULT NULL,
							hit_agnt_ID INT UNSIGNED,
							hit_referer_type ENUM('search','blacklist','referer','direct','spam') NOT NULL,
							hit_referer VARCHAR(255) DEFAULT NULL,
							hit_referer_dom_ID INT UNSIGNED DEFAULT NULL,
							hit_blog_ID int(11) UNSIGNED NOT NULL DEFAULT '0',
							hit_remote_addr VARCHAR(40) DEFAULT NULL,
							PRIMARY KEY (hit_ID)
						)"; // TODO: indexes!
	$DB->query( $query );
	echo "OK.<br />\n";


	// Additionnal tables:
	create_antispam();
	create_locales();
	create_b2evo_tables_092();


	// Create relations:
	create_b2evo_relations();
}


/*
 * create_antispam(-)
 *
 * Used when creating full install and upgrading from earlier versions
 */
function create_antispam()
{
	global $DB;

	echo 'Creating table for Antispam Blackist... ';
	$query = "CREATE TABLE T_antispam (
		aspm_ID bigint(11) NOT NULL auto_increment,
		aspm_string varchar(80) NOT NULL,
		aspm_source enum( 'local','reported','central' ) NOT NULL default 'reported',
		PRIMARY KEY aspm_ID (aspm_ID),
		UNIQUE aspm_string (aspm_string)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";

	echo 'Creating default blacklist entries... ';
	$query = "INSERT INTO T_antispam(aspm_string) VALUES ".
	"('penis-enlargement'), ('online-casino'), ".
	"('order-viagra'), ('order-phentermine'), ('order-xenical'), ".
	"('order-prophecia'), ('sexy-lingerie'), ('-porn-'), ".
	"('-adult-'), ('-tits-'), ('buy-phentermine'), ".
	"('order-cheap-pills'), ('buy-xenadrine'),	('xxx'), ".
	"('paris-hilton'), ('parishilton'), ('camgirls'), ('adult-models')";
	$DB->query( $query );
	echo "OK.<br />\n";
}

/**
 * Create DB table for locales.
 *
 * Used when creating full install and upgrading from earlier versions
 */
function create_locales()
{
	global $DB;

	echo 'Creating table for Locales... ';
	$query = "CREATE TABLE T_locales (
		loc_locale varchar(20) NOT NULL default '',
		loc_charset varchar(15) NOT NULL default 'iso-8859-1',
		loc_datefmt varchar(10) NOT NULL default 'y-m-d',
		loc_timefmt varchar(10) NOT NULL default 'H:i:s',
		loc_startofweek TINYINT UNSIGNED NOT NULL DEFAULT 1,
		loc_name varchar(40) NOT NULL default '',
		loc_messages varchar(20) NOT NULL default '',
		loc_priority tinyint(4) UNSIGNED NOT NULL default '0',
		loc_enabled tinyint(4) NOT NULL default '1',
		PRIMARY KEY loc_locale( loc_locale )
	) COMMENT='saves available locales'";
	$DB->query( $query );
	echo "OK.<br />\n";

}


/*
 * create_groups(-)
 *
 * Create user permissions
 *
 * Used when creating full install and upgrading from earlier versions
 */
function create_groups()
{
	global $Group_Admins, $Group_Priviledged, $Group_Bloggers, $Group_Users;
	global $DB;

	echo 'Creating table for Groups... ';
	$query = "CREATE TABLE T_groups (
		grp_ID int(11) NOT NULL auto_increment,
		grp_name varchar(50) NOT NULL default '',
		grp_perm_blogs enum('user','viewall','editall') NOT NULL default 'user',
		grp_perm_stats enum('none','view','edit') NOT NULL default 'none',
		grp_perm_spamblacklist enum('none','view','edit') NOT NULL default 'none',
		grp_perm_options enum('none','view','edit') NOT NULL default 'none',
		grp_perm_users enum('none','view','edit') NOT NULL default 'none',
		grp_perm_templates TINYINT NOT NULL DEFAULT 0,
		PRIMARY KEY grp_ID (grp_ID)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";

	echo 'Creating default groups... ';
	$Group_Admins = new Group(); // COPY !
	$Group_Admins->set( 'name', 'Administrators' );
	$Group_Admins->set( 'perm_blogs', 'editall' );
	$Group_Admins->set( 'perm_stats', 'edit' );
	$Group_Admins->set( 'perm_spamblacklist', 'edit' );
	$Group_Admins->set( 'perm_options', 'edit' );
	$Group_Admins->set( 'perm_templates', 1 );
	$Group_Admins->set( 'perm_users', 'edit' );
	$Group_Admins->dbinsert();

	$Group_Priviledged = new Group(); // COPY !
	$Group_Priviledged->set( 'name', 'Priviledged Bloggers' );
	$Group_Priviledged->set( 'perm_blogs', 'viewall' );
	$Group_Priviledged->set( 'perm_stats', 'view' );
	$Group_Priviledged->set( 'perm_spamblacklist', 'edit' );
	$Group_Priviledged->set( 'perm_options', 'view' );
	$Group_Priviledged->set( 'perm_templates', 0 );
	$Group_Priviledged->set( 'perm_users', 'view' );
	$Group_Priviledged->dbinsert();

	$Group_Bloggers = new Group(); // COPY !
	$Group_Bloggers->set( 'name', 'Bloggers' );
	$Group_Bloggers->set( 'perm_blogs', 'user' );
	$Group_Bloggers->set( 'perm_stats', 'none' );
	$Group_Bloggers->set( 'perm_spamblacklist', 'view' );
	$Group_Bloggers->set( 'perm_options', 'none' );
	$Group_Bloggers->set( 'perm_templates', 0 );
	$Group_Bloggers->set( 'perm_users', 'none' );
	$Group_Bloggers->dbinsert();

	$Group_Users = new Group(); // COPY !
	$Group_Users->set( 'perm_blogs', 'user' );
	$Group_Users->set( 'name', 'Basic Users' );
	$Group_Users->set( 'perm_stats', 'none' );
	$Group_Users->set( 'perm_spamblacklist', 'none' );
	$Group_Users->set( 'perm_options', 'none' );
	$Group_Users->set( 'perm_templates', 0 );
	$Group_Users->set( 'perm_users', 'none' );
	$Group_Users->dbinsert();
	echo "OK.<br />\n";

	echo 'Creating table for Blog-User permissions... ';
	$query = "CREATE TABLE T_blogusers (
		bloguser_blog_ID int(11) unsigned NOT NULL default 0,
		bloguser_user_ID int(11) unsigned NOT NULL default 0,
		bloguser_ismember tinyint NOT NULL default 0,
		bloguser_perm_poststatuses set('published','deprecated','protected','private','draft') NOT NULL default '',
		bloguser_perm_delpost tinyint NOT NULL default 0,
		bloguser_perm_comments tinyint NOT NULL default 0,
		bloguser_perm_cats tinyint NOT NULL default 0,
		bloguser_perm_properties tinyint NOT NULL default 0,
		bloguser_perm_media_upload tinyint NOT NULL default 0,
		bloguser_perm_media_browse tinyint NOT NULL default 0,
		bloguser_perm_media_change tinyint NOT NULL default 0,
		PRIMARY KEY bloguser_pk (bloguser_blog_ID,bloguser_user_ID)
	)";
	$DB->query( $query );
	echo "OK.<br />\n";

}


/**
 * {@internal populate_linkblog(-)}}
 */
function populate_linkblog( & $now, $cat_linkblog_b2evo, $cat_linkblog_contrib)
{
	global $timestamp, $default_locale;

	echo 'Creating default linkblog entries... ';

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Topanga', 'Testing', $now, $cat_linkblog_contrib, array(), 'published',	 'nl-NL', '', 0, true, '', 'http://www.tenderfeelings.be/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Travis Swicegood', 'Hosting', $now, $cat_linkblog_contrib, array(), 'published',	 'en-US', '', 0, true, '', 'http://www.fromthecrossroads.ws/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Welby', 'Hosting', $now, $cat_linkblog_contrib, array(), 'published',	 'en-UK', '', 0, true, '', 'http://www.wheely-bin.co.uk/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Graham', 'Testing', $now, $cat_linkblog_contrib, array(), 'published',	'en-UK', '', 0, true, '', 'http://tin-men.net/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Isaac', 'Support', $now, $cat_linkblog_contrib, array(), 'published',	'en-UK', '', 0, true, '', 'http://isaacschlueter.com/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'dAniel', 'Development', $now, $cat_linkblog_contrib, array(), 'published',	'de-DE', '', 0, true, '', 'http://thequod.de/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'Fran�ois', 'Main dev', $now, $cat_linkblog_contrib, array(), 'published',	 'fr-FR', '', 0, true, '', 'http://fplanque.net/Blog/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, 'b2evolution', 'Project home', $now, $cat_linkblog_b2evo, array(), 'published',	'en-EU', '', 0, true, '', 'http://b2evolution.net/', 'disabled', array() );

	// Insert a post into linkblog:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('This is a sample linkblog entry'), T_("This is sample text describing the linkblog entry. In most cases however, you'll want to leave this blank, providing just a Title and an Url for your linkblog entries (favorite/related sites)."), $now, $cat_linkblog_b2evo, array(), 'published',	$default_locale, '', 0, true, '', 'http://b2evolution.net/', 'disabled', array() );

	echo "OK.<br />\n";
}


/**
 * Create default blogs.
 *
 * This is called for fresh installs and cafelog upgrade.
 *
 * {@internal create_default_blogs(-) }}
 * @param string
 * @param string
 * @param string
 */
function create_default_blogs( $blog_a_short = 'Blog A', $blog_a_long = '#', $blog_a_longdesc = '#' )
{
	global $default_locale, $query, $timestamp;
	global $blog_all_ID, $blog_a_ID, $blog_b_ID, $blog_linkblog_ID;

	$default_blog_longdesc = T_("This is the long description for the blog named '%s'. %s");

	echo "Creating default blogs... ";

	$blog_shortname = 'Blog All';
	$blog_stub = 'all';
	$blog_more_longdesc = "<br />
<br />
<strong>".T_("This blog (blog #1) is actually a very special blog! It automatically aggregates all posts from all other blogs. This allows you to easily track everything that is posted on this system. You can hide this blog from the public by unchecking 'Include in public blog list' in the blogs admin.")."</strong>";
	$blog_all_ID =	blog_create(
										sprintf( T_('%s Title'), $blog_shortname ),
										$blog_shortname,
										'',
										$blog_stub,
										$blog_stub.'.html',
										sprintf( T_('Tagline for %s'), $blog_shortname ),
										sprintf( T_('Short description for %s'), $blog_shortname ),
										sprintf( $default_blog_longdesc, $blog_shortname, $blog_more_longdesc ),
										$default_locale,
										sprintf( T_('Notes for %s'), $blog_shortname ),
										sprintf( T_('Keywords for %s'), $blog_shortname ),
										4 );

	$blog_shortname = $blog_a_short;
	if( $blog_a_long == '#' ) $blog_a_long = sprintf( T_('%s Title'), $blog_shortname );
	$blog_stub = 'a';
	$blog_a_ID =	blog_create(
										$blog_a_long,
										$blog_shortname,
										'',
										$blog_stub,
										$blog_stub.'.html',
										sprintf( T_('Tagline for %s'), $blog_shortname ),
										sprintf( T_('Short description for %s'), $blog_shortname ),
										sprintf(
	(($blog_a_longdesc == '#') ? $default_blog_longdesc : $blog_a_longdesc), $blog_shortname, '' ),
										$default_locale,
										sprintf( T_('Notes for %s'), $blog_shortname ),
										sprintf( T_('Keywords for %s'), $blog_shortname ),
										4 );

	$blog_shortname = 'Blog B';
	$blog_stub = 'b';
	$blog_b_ID =	blog_create(
										sprintf( T_('%s Title'), $blog_shortname ),
										$blog_shortname,
										'',
										$blog_stub,
										$blog_stub.'.html',
										sprintf( T_('Tagline for %s'), $blog_shortname ),
										sprintf( T_('Short description for %s'), $blog_shortname ),
										sprintf( $default_blog_longdesc, $blog_shortname, '' ),
										$default_locale,
										sprintf( T_('Notes for %s'), $blog_shortname ),
										sprintf( T_('Keywords for %s'), $blog_shortname ),
										4 );

	$blog_shortname = 'Linkblog';
	$blog_stub = 'links';
	$blog_more_longdesc = '<br />
<br />
<strong>'.T_("The main purpose for this blog is to be included as a side item to other blogs where it will display your favorite/related links.").'</strong>';
	$blog_linkblog_ID = blog_create(
										sprintf( T_('%s Title'), $blog_shortname ),
										$blog_shortname,
										'',
										$blog_stub,
										$blog_stub.'.html',
										sprintf( T_('Tagline for %s'), $blog_shortname ),
										sprintf( T_('Short description for %s'), $blog_shortname ),
										sprintf( $default_blog_longdesc, $blog_shortname, $blog_more_longdesc ),
										$default_locale,
										sprintf( T_('Notes for %s'), $blog_shortname ),
										sprintf( T_('Keywords for %s'), $blog_shortname ),
										0 /* no Link blog */ );

	echo "OK.<br />\n";

}

/**
 * Create default categories.
 *
 * This is called for fresh installs and cafelog upgrade.
 *
 * {@internal create_default_categories(-) }}
 * @param boolean
 */
function create_default_categories( $populate_blog_a = true )
{
	global $query, $timestamp;
	global $cat_ann_a, $cat_news, $cat_bg, $cat_ann_b, $cat_fun, $cat_life, $cat_web, $cat_sports, $cat_movies, $cat_music, $cat_b2evo, $cat_linkblog_b2evo, $cat_linkblog_contrib;

	echo 'Creating sample categories... ';

	if( $populate_blog_a )
	{
		// Create categories for blog A
		$cat_ann_a = cat_create( 'Announcements [A]', 'NULL', 2 );
		$cat_news = cat_create( 'News', 'NULL', 2 );
		$cat_bg = cat_create( 'Background', 'NULL', 2 );
	}

	// Create categories for blog B
	$cat_ann_b = cat_create( 'Announcements [B]', 'NULL', 3 );
	$cat_fun = cat_create( 'Fun', 'NULL', 3 );
	$cat_life = cat_create( 'In real life', $cat_fun, 3 );
	$cat_web = cat_create( 'On the web', $cat_fun, 3 );
	$cat_sports = cat_create( 'Sports', $cat_life, 3 );
	$cat_movies = cat_create( 'Movies', $cat_life, 3 );
	$cat_music = cat_create( 'Music', $cat_life, 3 );
	$cat_b2evo = cat_create( 'b2evolution Tips', 'NULL', 3 );

	// Create categories for linkblog
	$cat_linkblog_b2evo = cat_create( 'b2evolution', 'NULL', 4 );
	$cat_linkblog_contrib = cat_create( 'contributors', 'NULL', 4 );

	echo "OK.<br />\n";

}


/**
 * Create default contents.
 *
 * This is called for fresh installs and cafelog upgrade.
 *
 * {@internal create_default_contents(-) }}
 * @param boolean
 */
function create_default_contents( $populate_blog_a = true )
{
	global $query, $timestamp;
	global $cat_ann_a, $cat_news, $cat_bg, $cat_ann_b, $cat_fun, $cat_life, $cat_web, $cat_sports, $cat_movies, $cat_music, $cat_b2evo, $cat_linkblog_b2evo, $cat_linkblog_contrib;

	echo 'Creating sample posts... ';

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("Clean Permalinks!"), T_("b2evolution uses old-style permalinks and feedback links by default. This is to ensure maximum compatibility with various webserver configurations.

Nethertheless, once you feel comfortable with b2evolution, you should try activating clean permalinks in the Settings screen... (check 'Use extra-path info')"), $now, $cat_b2evo );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("Apache optimization..."), T_("In the <code>/blogs</code> folder as well as in <code>/blogs/admin</code> there are two files called [<code>sample.htaccess</code>]. You should try renaming those to [<code>.htaccess</code>].

This will optimize the way b2evolution is handled by the webserver (if you are using Apache). These files are not active by default because a few hosts would display an error right away when you try to use them. If this happens to you when you rename the files, just remove them and you'll be fine."), $now, $cat_b2evo );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("About evoSkins..."), T_("By default, b2evolution blogs are displayed using a default skin.

Readers can choose a new skin by using the skin switcher integrated in most skins.

You can change the default skin used for any blog by editing the blog parameters in the admin interface. You can also force the use of the default skin for everyone.

Otherwise, you can restrict available skins by deleting some of them from the /blogs/skins folder. You can also create new skins by duplicating, renaming and customizing any existing skin folder.

To start customizing a skin, open its '<code>_main.php</code>' file in an editor and read the comments in there. And, of course, read the manual on evoSkins!"), $now, $cat_b2evo );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("Skins, Stubs and Templates..."), T_("By default, all pre-installed blogs are displayed using a skin. (More on skins in another post.)

That means, blogs are accessed through '<code>index.php</code>', which loads default parameters from the database and then passes on the display job to a skin.

Alternatively, if you don't want to use the default DB parameters and want to, say, force a skin, a category or a specific linkblog, you can create a stub file like the provided '<code>a_stub.php</code>' and call your blog through this stub instead of index.php .

Finally, if you need to do some very specific customizations to your blog, you may use plain templates instead of skins. In this case, call your blog through a full template, like the provided '<code>a_noskin.php</code>'.

You will find more information in the stub/template files themselves. Open them in a text editor and read the comments in there.

Either way, make sure you go to the blogs admin and set the correct access method for your blog. When using a stub or a template, you must also set its filename in the 'Stub name' field. Otherwise, the permalinks will not function properly."), $now, $cat_b2evo );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("Multiple Blogs, new blogs, old blogs..."),
								T_("By default, b2evolution comes with 4 blogs, named 'Blog All', 'Blog A', 'Blog B' and 'Linkblog'.

Some of these blogs have a special role. Read about it on the corresponding page.

You can create additional blogs or delete unwanted blogs from the blogs admin."), $now, $cat_b2evo );


	// Create newbie posts:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('This is a multipage post'), T_('This is page 1 of a multipage post.

You can see the other pages by clicking on the links below the text.

<!--nextpage-->

This is page 2.

<!--nextpage-->

This is page 3.

<!--nextpage-->

This is page 4.

It is the last page.'), $now, $cat_b2evo, ( $populate_blog_a ? array( $cat_bg , $cat_b2evo ) : array ( $cat_b2evo ) ) );


	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('Extended post with no teaser'), T_('This is an extended post with no teaser. This means that you won\'t see this teaser any more when you click the "more" link.

<!--more--><!--noteaser-->

This is the extended text. You only see it when you have clicked the "more" link.'), $now, $cat_b2evo, ( $populate_blog_a ? array( $cat_bg , $cat_b2evo ) : array ( $cat_b2evo ) ) );


	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('Extended post'), T_('This is an extended post. This means you only see this small teaser by default and you must click on the link below to see more.

<!--more-->

This is the extended text. You only see it when you have clicked the "more" link.'), $now, $cat_b2evo, ( $populate_blog_a ? array( $cat_bg , $cat_b2evo ) : array ( $cat_b2evo ) ) );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_("Important information"), T_("Blog B contains a few posts in the 'b2evolution Tips' category.

All these entries are designed to help you so, as EdB would say: \"<em>read them all before you start hacking away!</em>\" ;)

If you wish, you can delete these posts one by one after you have read them. You could also change their status to 'deprecated' in order to visually keep track of what you have already read."), $now, $cat_b2evo, ( $populate_blog_a ? array( $cat_ann_a , $cat_ann_b ) : array ( $cat_ann_b ) ) );

	echo "OK.<br />\n";

}


/**
 * Insert default settings into T_settings.
 *
 * @param array associative array (settings name => value to use), allows
 *              overriding of defaults
 */
function create_default_settings( $override = array() )
{
	global $DB, $new_db_version, $default_locale, $Group_Users;

	$defaults = array(
								'db_version' => $new_db_version,
								'default_locale' => $default_locale,
								'posts_per_page' => '5',
								'what_to_show' => 'paged',
								'archive_mode' => 'monthly',
								'time_difference' => '0',
								'AutoBR' => '0',
								'antispam_last_update' => '2000-01-01 00:00:00',
								'newusers_grp_ID' => $Group_Users->get('ID'),
								'newusers_level' => '1',
								'newusers_canregister' => '0',
								'links_extrapath' => '0',
								'permalink_type' => 'urltitle',
								'user_minpwdlen' => '5',
								'reloadpage_timeout' => '300',
								'upload_enabled' => '1',
								'upload_allowedext' => 'jpg gif png',
								'upload_maxkb' => '96',
								'upload_minlevel' => '1',
							);

	$insertvalues = array();
	foreach( $defaults as $name => $defaultvalue )
	{
		$insertvalues[] = "('$name', '".$DB->escape( isset( $override[$name] ) ?
																									$override[$name] :
																									$defaultvalue )."')";
	}

	echo 'Creating default settings'.( count($override) ?
																			' (with existing values)' :
																			'' ).'... ';
	$DB->query( "INSERT INTO T_settings (set_name, set_value)
								VALUES ".implode( ', ', $insertvalues ) );
	echo "OK.<br />\n";
}


/**
 * This is called only for fresh installs and fills the tables with
 * demo/tutorial things.
 *
 * {@internal populate_main_tables(-)}}
 */
function populate_main_tables()
{
	global $baseurl, $new_db_version;
	global $random_password, $query;
	global $timestamp, $admin_email;
	global $Group_Admins, $Group_Priviledged, $Group_Bloggers, $Group_Users;
	global $blog_all_ID, $blog_a_ID, $blog_b_ID, $blog_linkblog_ID;
	global $cat_ann_a, $cat_news, $cat_bg, $cat_ann_b, $cat_fun, $cat_life, $cat_web, $cat_sports, $cat_movies, $cat_music, $cat_b2evo, $cat_linkblog_b2evo, $cat_linkblog_contrib;
	global $DB;
	global $default_locale, $install_password;

	create_default_blogs();

	create_default_categories();

	echo 'Creating default users... ';

	// USERS !
	$User_Admin = & new User();
	$User_Admin->set( 'login', 'admin' );
	if( !isset( $install_password ) )
	{
		$random_password = getRandomPassword();
	}
	else
	{
		$random_password = $install_password;
	}
	$User_Admin->set( 'pass', md5($random_password) );	// random
	$User_Admin->set( 'nickname', 'admin' );
	$User_Admin->set( 'email', $admin_email );
	$User_Admin->set( 'ip', '127.0.0.1' );
	$User_Admin->set( 'domain', 'localhost' );
	$User_Admin->set( 'level', 10 );
	$User_Admin->set( 'locale', $default_locale );
	$User_Admin->set_datecreated( $timestamp++ );
	// Note: NEVER use database time (may be out of sync + no TZ control)
	$User_Admin->setGroup( $Group_Admins );
	$User_Admin->dbinsert();

	$User_Demo = & new User();
	$User_Demo->set( 'login', 'demouser' );
	$User_Demo->set( 'pass', md5($random_password) ); // random
	$User_Demo->set( 'nickname', 'Mr. Demo' );
	$User_Demo->set( 'email', $admin_email );
	$User_Demo->set( 'ip', '127.0.0.1' );
	$User_Demo->set( 'domain', 'localhost' );
	$User_Demo->set( 'level', 0 );
	$User_Demo->set( 'locale', $default_locale );
	$User_Demo->set_datecreated( $timestamp++ );
	$User_Demo->setGroup( $Group_Users );
	$User_Demo->dbinsert();

	echo "OK.<br />\n";


	echo 'Creating sample posts for blog A... ';

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('First Post'), T_('<p>This is the first post.</p>

<p>It appears on both blog A and blog B.</p>'), $now, $cat_ann_a, array( $cat_ann_b ) );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('Second post'), T_('<p>This is the second post.</p>

<p>It appears on blog A only but in multiple categories.</p>'), $now, $cat_news, array( $cat_ann_a, $cat_bg ) );

	// Insert a post:
	$now = date('Y-m-d H:i:s',$timestamp++);
	$edited_Item = & new Item();
	$edited_Item->insert( 1, T_('Third post'), T_('<p>This is the third post.</p>

<p>It appears on blog B only and in a single category.</p>'), $now, $cat_fun );

	echo "OK.<br />\n";


	// POPULATE THE LINKBLOG:
	populate_linkblog( $now, $cat_linkblog_b2evo, $cat_linkblog_contrib );

	// Create blog B contents:
	create_default_contents();


	echo 'Creating sample comments... ';

	$now = date('Y-m-d H:i:s');
	$query = "INSERT INTO T_comments( comment_post_ID, comment_type, comment_author,
																				comment_author_email, comment_author_url, comment_author_IP,
																				comment_date, comment_content, comment_karma)
						VALUES( 1, 'comment', 'miss b2', 'missb2@example.com', 'http://example.com', '127.0.0.1',
									 '$now', '".
									 $DB->escape(T_('Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.')). "', 0)";
	$DB->query( $query );

	echo "OK.<br />\n";


	echo 'Creating user blog permissions... ';
	// Admin for blog A:
	$query = "INSERT INTO T_blogusers( bloguser_blog_ID, bloguser_user_ID, bloguser_ismember,
							bloguser_perm_poststatuses, bloguser_perm_delpost, bloguser_perm_comments,
							bloguser_perm_cats, bloguser_perm_properties,
							bloguser_perm_media_upload, bloguser_perm_media_browse, bloguser_perm_media_change )
						VALUES
							( $blog_all_ID, ".$User_Admin->ID.", 1,
							'published,deprecated,protected,private,draft', 1, 1, 1, 1, 1, 1, 1 ),
							( $blog_a_ID, ".$User_Admin->ID.", 1,
							'published,deprecated,protected,private,draft', 1, 1, 1, 1, 1, 1, 1 ),
							( $blog_b_ID, ".$User_Admin->ID.", 1,
							'published,deprecated,protected,private,draft', 1, 1, 1, 1, 1, 1, 1 ),
							( $blog_linkblog_ID, ".$User_Admin->ID.", 1,
							'published,deprecated,protected,private,draft', 1, 1, 1, 1, 1, 1, 1 ),
							( $blog_a_ID, ".$User_Demo->ID.", 1,
							'draft', 0, 0, 0, 0, 0, 0, 0 )";
	$DB->query( $query );

	echo "OK.<br />\n";


	create_default_settings();

}


/**
 * Create new tables for version 0.9.2
 */
function create_b2evo_tables_092()
{
	global $DB;

	echo 'Creating table for active sessions... ';
	$DB->query( "CREATE TABLE T_sessions (
									sess_ID INT(11) UNSIGNED NOT NULL,
									sess_lastseen DATETIME NOT NULL,
									sess_ipaddress VARCHAR(15) NOT NULL DEFAULT '',
									sess_user_ID INT(10) DEFAULT NULL,
									PRIMARY KEY( sess_ID ),
									UNIQUE KEY ip_user_ID ( sess_ipaddress, sess_user_ID )
								)" );
	echo "OK.<br />\n";


	echo 'Creating user settings table... ';
	$DB->query( "CREATE TABLE T_usersettings (
									uset_user_ID INT(11) UNSIGNED NOT NULL,
									uset_name VARCHAR( 30 ) NOT NULL,
									uset_value VARCHAR( 255 ) NULL,
									PRIMARY KEY ( uset_user_ID, uset_name )
								)");
	echo "OK.<br />\n";


	echo 'Creating plugins table... ';
	$DB->query( "CREATE TABLE T_plugins (
									plug_ID int NOT NULL auto_increment,
									plug_priority int NOT NULL default 50,
									plug_classname varchar(40) NOT NULL default '',
									PRIMARY KEY (plug_ID)
								)");
	echo "OK.<br />\n";


	echo 'Creating table for Post Statuses... ';
	$query="CREATE TABLE T_poststatuses (
									pst_ID                int(11) unsigned     not null AUTO_INCREMENT,
									pst_name              varchar(30)          not null,
									primary key (pst_ID)
								)";
	$DB->query( $query );
	echo "OK.<br />\n";


	echo 'Creating table for Post Types... ';
	$query="CREATE TABLE T_posttypes (
									ptyp_ID               int(11) unsigned     not null AUTO_INCREMENT,
									ptyp_name             varchar(30)          not null,
									primary key (ptyp_ID)
								)";
	$DB->query( $query );
	echo "OK.<br />\n";
	echo 'Creating default Post Types... ';
	$DB->query( "INSERT INTO T_posttypes ( ptyp_ID, ptyp_name )
										VALUES ( 1, 'Post' ),
													 ( 2, 'Link' )" );
	echo "OK.<br />\n";


	echo 'Creating table for File Meta Data... ';
	$DB->query( "CREATE TABLE T_files (
									file_ID         int(11) unsigned NOT NULL auto_increment,
									file_root_type  enum('user','group','collection') NOT NULL default 'user',
									file_root_ID    int(11) unsigned NOT NULL default '0',
									file_path       varchar(255) NOT NULL default '',
									file_caption    varchar(255) NOT NULL default '',
									PRIMARY KEY (file_ID),
									UNIQUE KEY file (file_root_type, file_root_ID, file_path)
								)" );
	echo "OK.<br />\n";


	echo 'Creating table for Post Links... ';
	$DB->query( "CREATE TABLE T_links (
							  link_ID                 int(11) unsigned  not null AUTO_INCREMENT,
							  link_datecreated        datetime          not null default '0000-00-00 00:00:00',
							  link_datemodified       datetime          not null default '0000-00-00 00:00:00',
							  link_creator_user_ID    int(11) unsigned  not null default 0,
							  link_lastedit_user_ID   int(11) unsigned  not null default 0,
								link_item_ID    		    int(11) unsigned  NOT NULL,
								link_dest_item_ID		    int(11) unsigned  NULL,
								link_file_ID				    int(11) unsigned  NULL,
								link_ltype_ID				    int(11) unsigned  NOT NULL default 1,
								link_external_url       VARCHAR(255)      NULL,
								link_title          		TEXT              NULL,
								PRIMARY KEY (link_ID),
								INDEX link_item_ID( link_item_ID ),
								INDEX link_dest_item_ID (link_dest_item_ID),
								INDEX link_file_ID (link_file_ID)
								)" );
	echo "OK.<br />\n";


	echo 'Creating table for base domains... ';
	$DB->query( "CREATE TABLE T_basedomains (
								dom_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
								dom_name VARCHAR(255) NOT NULL DEFAULT '',
								dom_status ENUM('unknown','whitelist','blacklist') NOT NULL DEFAULT 'unknown',
								PRIMARY KEY (dom_ID),
								UNIQUE (dom_name) )" );
	echo "OK.<br />\n";


	echo 'Creating table for user agents... ';
	$DB->query( 'CREATE TABLE T_useragents (
								agnt_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
								agnt_signature VARCHAR( 255 ) NOT NULL,
								agnt_type ENUM( "rss", "robot", "browser", "unknown" ) DEFAULT "unknown" NOT NULL ,
								PRIMARY KEY (agnt_ID) )' );
	echo "OK.<br />\n";


	/*
			evo_linktypes tables:
			-ltype_ID    INT     PK
			-ltype_desc    VARCHAR(50)

	 */
}


/**
 * Create relations
 */
function create_b2evo_relations()
{
	global $DB, $db_use_fkeys;

	if( !$db_use_fkeys )
		return false;

	echo 'Creating relations... ';

	$DB->query( 'alter table T_blogusers
								add constraint FK_bloguser_blog_ID
								 			foreign key (bloguser_blog_ID)
											references T_blogs (blog_ID)
											on delete restrict
											on update restrict,
								add constraint FK_bloguser_user_ID
											foreign key (bloguser_user_ID)
											references T_users (ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_categories
								add constraint FK_cat_blog_ID
											foreign key (cat_blog_ID)
											references T_blogs (blog_ID)
											on delete restrict
											on update restrict,
								add constraint FK_cat_parent_ID
											foreign key (cat_parent_ID)
											references T_categories (cat_ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_comments
								add constraint FK_comment_post_ID
											foreign key (comment_post_ID)
											references T_posts (ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_postcats
								add constraint FK_postcat_cat_ID
											foreign key (postcat_cat_ID)
											references T_categories (cat_ID)
											on delete restrict
											on update restrict,
								add constraint FK_postcat_post_ID
											foreign key (postcat_post_ID)
											references T_posts (ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_posts
								add constraint FK_post_assigned_user_ID
											foreign key (post_assigned_user_ID)
											references T_users (ID)
											on delete restrict
											on update restrict,
								add constraint FK_post_lastedit_user_ID
											foreign key (post_lastedit_user_ID)
      								references T_users (ID)
      								on delete restrict
      								on update restrict,
      					add constraint FK_post_creator_user_ID
											foreign key (post_creator_user_ID)
											references T_users (ID)
											on delete restrict
											on update restrict,
								add constraint FK_post_main_cat_ID
											foreign key (post_main_cat_ID)
											references T_categories (cat_ID)
											on delete restrict
											on update restrict,
								add constraint FK_post_parent_ID
											foreign key (post_parent_ID)
											references T_posts (ID)
											on delete restrict
											on update restrict,
								add constraint FK_post_pst_ID
											foreign key (post_pst_ID)
											references T_poststatuses (pst_ID)
											on delete restrict
											on update restrict,
								add constraint FK_post_ptyp_ID
											foreign key (post_ptyp_ID)
											references T_posttypes (ptyp_ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_links
								add constraint FK_link_dest_item_ID
											foreign key (link_dest_item_ID)
      								references T_posts (ID)
      								on delete restrict
      								on update restrict,
							 	add constraint FK_link_file_ID
							 				foreign key (link_file_ID)
								      references T_files (file_ID)
								      on delete restrict
								      on update restrict,
							 	add constraint FK_link_item_ID
							 				foreign key (link_item_ID)
								      references T_posts (ID)
								      on delete restrict
								      on update restrict' );

	$DB->query( 'alter table T_users
								add constraint FK_user_grp_ID
											foreign key (user_grp_ID)
											references T_groups (grp_ID)
											on delete restrict
											on update restrict' );

	$DB->query( 'alter table T_usersettings
								add constraint FK_uset_user_ID
											foreign key (uset_user_ID)
											references T_users (ID)
											on delete restrict
											on update restrict' );

	echo "OK.<br />\n";
}


?>