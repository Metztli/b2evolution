<?php
/**
 * This is the install file for the collections module
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evocore
 */
if (! defined('EVO_CONFIG_LOADED')) {
    die('Please, do not access this page directly.');
}


global $db_storage_charset;


/**
 * The b2evo database scheme.
 *
 * This gets updated through {@link db_delta()} which generates the queries needed to get
 * to this scheme.
 *
 * Please see {@link db_delta()} for things to take care of.
 */
$schema_queries = array_merge($schema_queries, [
    'T_skins__skin' => [
        'Creating table for installed skins',
        "CREATE TABLE T_skins__skin (
				skin_ID      int(10) unsigned NOT NULL auto_increment,
				skin_class   varchar(32) COLLATE ascii_general_ci NOT NULL,
				skin_name    varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
				skin_type    enum('normal','feed','sitemap','mobile','tablet','alt','rwd') COLLATE ascii_general_ci NOT NULL default 'normal',
				skin_folder  varchar(32) NOT NULL,
				PRIMARY KEY skin_ID (skin_ID),
				UNIQUE skin_folder( skin_folder ),
				UNIQUE skin_class( skin_class ),
				KEY skin_name( skin_name )
			) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_blogs' => [
        'Creating table for Blogs',
        "CREATE TABLE T_blogs (
			blog_ID              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			blog_sec_ID          INT(10) UNSIGNED NOT NULL DEFAULT 1,
			blog_shortname       varchar(255) COLLATE utf8mb4_unicode_ci NULL default '',
			blog_name            varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL default '',
			blog_owner_user_ID   int(10) unsigned NOT NULL default 1,
			blog_advanced_perms  TINYINT(1) NOT NULL default 0,
			blog_tagline         varchar(250) COLLATE utf8mb4_unicode_ci NULL default '',
			blog_shortdesc       varchar(250) COLLATE utf8mb4_unicode_ci NULL default '',
			blog_longdesc        TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			blog_locale          VARCHAR(20) COLLATE ascii_general_ci NOT NULL DEFAULT 'en-EU',
			blog_access_type     VARCHAR(10) COLLATE ascii_general_ci NOT NULL DEFAULT 'extrapath',
			blog_siteurl         varchar(120) NOT NULL default '',
			blog_urlname         VARCHAR(255) COLLATE ascii_general_ci NOT NULL DEFAULT 'urlname',
			blog_notes           TEXT COLLATE utf8mb4_unicode_ci NULL,
			blog_keywords        tinytext COLLATE utf8mb4_unicode_ci,
			blog_allowtrackbacks TINYINT(1) NOT NULL default 0,
			blog_allowblogcss    TINYINT(1) NOT NULL default 1,
			blog_allowusercss    TINYINT(1) NOT NULL default 1,
			blog_in_bloglist     ENUM( 'public', 'logged', 'member', 'never' ) COLLATE ascii_general_ci DEFAULT 'public' NOT NULL,
			blog_links_blog_ID   INT(10) UNSIGNED NULL DEFAULT NULL,
			blog_media_location  ENUM( 'default', 'subdir', 'custom', 'none' ) COLLATE ascii_general_ci DEFAULT 'default' NOT NULL,
			blog_media_subdir    VARCHAR( 255 ) NULL,
			blog_media_fullpath  VARCHAR( 255 ) NULL,
			blog_media_url       VARCHAR( 255 ) NULL,
			blog_type            VARCHAR( 16 ) COLLATE ascii_general_ci DEFAULT 'std' NOT NULL,
			blog_order           int(11) NULL DEFAULT NULL,
			blog_normal_skin_ID  int(10) unsigned NULL,
			blog_mobile_skin_ID  int(10) unsigned NULL,
			blog_tablet_skin_ID  int(10) unsigned NULL,
			blog_alt_skin_ID     int(10) unsigned NULL,
			PRIMARY KEY blog_ID (blog_ID),
			UNIQUE KEY blog_urlname (blog_urlname)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_section' => [
        'Creating sections table',
        "CREATE TABLE T_section (
			sec_ID            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			sec_name          VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			sec_order         INT(11) NOT NULL,
			sec_owner_user_ID INT(10) UNSIGNED NOT NULL default 1,
			PRIMARY KEY ( sec_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_coll_url_aliases' => [
        'Creating collection URL aliases table',
        "CREATE TABLE T_coll_url_aliases (
			cua_coll_ID   INT(10) UNSIGNED NOT NULL,
			cua_url_alias VARCHAR(255) COLLATE ascii_general_ci NOT NULL,
			PRIMARY KEY ( cua_url_alias ),
			INDEX cua_coll_ID ( cua_coll_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_coll_settings' => [
        'Creating collection settings table',
        "CREATE TABLE T_coll_settings (
			cset_coll_ID INT(10) UNSIGNED NOT NULL,
			cset_name    VARCHAR( 50 ) COLLATE ascii_general_ci NOT NULL,
			cset_value   VARCHAR( 10000 ) COLLATE utf8mb4_unicode_ci NULL COMMENT 'The AdSense plugin wants to store very long snippets of HTML',
			PRIMARY KEY ( cset_coll_ID, cset_name )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_coll_locales' => [
        'Creating table for collection extra locales and linking with other collections',
        "CREATE TABLE T_coll_locales (
			cl_coll_ID        INT(10) UNSIGNED NOT NULL,
			cl_locale         VARCHAR(20) COLLATE ascii_general_ci NOT NULL,
			cl_linked_coll_ID INT(10) UNSIGNED NULL,
			PRIMARY KEY cl_coll_loc_pk (cl_coll_ID, cl_locale)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_widget__container' => [
        'Creating components container table',
        "CREATE TABLE T_widget__container (
			wico_ID        INT(10) UNSIGNED auto_increment,
			wico_code      VARCHAR(128) COLLATE ascii_general_ci NULL DEFAULT NULL,
			wico_skin_type ENUM( 'normal', 'mobile', 'tablet', 'alt' ) COLLATE ascii_general_ci NOT NULL DEFAULT 'normal',
			wico_name      VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL,
			wico_coll_ID   INT(10) NULL DEFAULT NULL,
			wico_order     INT(10) NOT NULL,
			wico_main      TINYINT(1) NOT NULL DEFAULT 0,
			wico_item_ID   INT(10) UNSIGNED NULL DEFAULT NULL,
			PRIMARY KEY    ( wico_ID ),
			UNIQUE wico_coll_ID_code_skin_type ( wico_coll_ID, wico_code, wico_skin_type )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_widget__widget' => [
        'Creating components table',
        "CREATE TABLE T_widget__widget (
			wi_ID         INT(10) UNSIGNED auto_increment,
			wi_wico_ID    INT(10) UNSIGNED NOT NULL,
			wi_order      INT(10) NOT NULL,
			wi_enabled    TINYINT(1) NOT NULL DEFAULT 1,
			wi_type       ENUM( 'core', 'plugin' ) COLLATE ascii_general_ci NOT NULL DEFAULT 'core',
			wi_code       VARCHAR(32) COLLATE ascii_general_ci NOT NULL,
			wi_params     TEXT COLLATE utf8mb4_unicode_ci NULL,
			PRIMARY KEY ( wi_ID ),
			UNIQUE wi_order( wi_wico_ID, wi_order )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_categories' => [
        'Creating table for Categories',
        "CREATE TABLE T_categories (
			cat_ID              int(10) unsigned NOT NULL auto_increment,
			cat_parent_ID       int(10) unsigned NULL,
			cat_name            varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			cat_urlname         varchar(255) COLLATE ascii_general_ci NOT NULL,
			cat_blog_ID         int(10) unsigned NOT NULL default 2,
			cat_image_file_ID   int(10) unsigned NULL,
			cat_social_media_image_file_ID int(10) unsigned NULL,
			cat_description     varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			cat_order           int(11) NULL DEFAULT NULL,
			cat_subcat_ordering enum('parent', 'alpha', 'manual') COLLATE ascii_general_ci NULL DEFAULT NULL,
			cat_meta            tinyint(1) NOT NULL DEFAULT 0,
			cat_lock            tinyint(1) NOT NULL DEFAULT 0,
			cat_last_touched_ts TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			cat_ityp_ID         INT UNSIGNED NULL,
			PRIMARY KEY cat_ID (cat_ID),
			UNIQUE cat_urlname( cat_urlname ),
			KEY cat_blog_ID (cat_blog_ID),
			KEY cat_parent_ID (cat_parent_ID),
			KEY cat_order (cat_order)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__item' => [
        'Creating table for Posts',
        "CREATE TABLE T_items__item (
			post_ID                     int(10) unsigned NOT NULL auto_increment,
			post_parent_ID              int(10) unsigned NULL,
			post_creator_user_ID        int(10) unsigned NOT NULL,
			post_lastedit_user_ID       int(10) unsigned NULL,
			post_assigned_user_ID       int(10) unsigned NULL,
			post_dateset                tinyint(1) NOT NULL DEFAULT 1,
			post_datestart              TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			post_datedeadline           TIMESTAMP NULL,
			post_datecreated            TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			post_datemodified           TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			post_last_touched_ts        TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			post_contents_last_updated_ts TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			post_status                 ENUM('published','community','deprecated','protected','private','review','draft','redirected') COLLATE ascii_general_ci NOT NULL DEFAULT 'draft',
			post_single_view            ENUM('normal','404','redirected') COLLATE ascii_general_ci NOT NULL DEFAULT 'normal',
			post_pst_ID                 int(10) unsigned NULL,
			post_ityp_ID                int(10) unsigned NOT NULL DEFAULT 1,
			post_igrp_ID                INT(10) UNSIGNED NULL,
			post_locale                 VARCHAR(20) COLLATE ascii_general_ci NOT NULL DEFAULT 'en-EU',
			post_locale_visibility      ENUM( 'always', 'follow-nav-locale' ) COLLATE ascii_general_ci NOT NULL DEFAULT 'always',
			post_content                MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL,
			post_excerpt                text COLLATE utf8mb4_unicode_ci NULL,
			post_excerpt_autogenerated  TINYINT(1) NOT NULL DEFAULT 1,
			post_short_title            VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL,
			post_title                  VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			post_urltitle               VARCHAR(210) COLLATE ascii_general_ci NOT NULL,
			post_canonical_slug_ID      int(10) unsigned NULL DEFAULT NULL,
			post_tiny_slug_ID           int(10) unsigned NULL DEFAULT NULL,
			post_titletag               VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			post_url                    VARCHAR(255) NULL DEFAULT NULL,
			post_main_cat_ID            int(10) unsigned NOT NULL,
			post_notifications_status   ENUM('noreq','todo','started','finished') COLLATE ascii_general_ci NOT NULL DEFAULT 'noreq',
			post_notifications_ctsk_ID  INT(10) unsigned NULL DEFAULT NULL,
			post_notifications_flags    SET('moderators_notified','members_notified','community_notified','pings_sent') COLLATE ascii_general_ci NOT NULL DEFAULT '',
			post_wordcount              int(11) default NULL,
			post_comment_status         ENUM('disabled', 'open', 'closed') COLLATE ascii_general_ci NOT NULL DEFAULT 'open',
			post_renderers              VARCHAR(4000) COLLATE ascii_general_ci NOT NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			post_priority               int(11) unsigned null COMMENT 'Task priority in workflow',
			post_featured               tinyint(1) NOT NULL DEFAULT 0,
			post_ctry_ID                INT(10) UNSIGNED NULL,
			post_rgn_ID                 INT(10) UNSIGNED NULL,
			post_subrg_ID               INT(10) UNSIGNED NULL,
			post_city_ID                INT(10) UNSIGNED NULL,
			post_addvotes               INT NOT NULL DEFAULT 0,
			post_countvotes             INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY post_ID( post_ID ),
			UNIQUE post_urltitle( post_urltitle ),
			INDEX post_datestart( post_datestart ),
			INDEX post_main_cat_ID( post_main_cat_ID ),
			INDEX post_creator_user_ID( post_creator_user_ID ),
			INDEX post_status( post_status ),
			INDEX post_parent_ID( post_parent_ID ),
			INDEX post_assigned_user_ID( post_assigned_user_ID ),
			INDEX post_ityp_ID( post_ityp_ID ),
			INDEX post_pst_ID( post_pst_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_postcats' => [
        'Creating table for Categories-to-Posts relationships',
        "CREATE TABLE T_postcats (
			postcat_post_ID int(10) unsigned NOT NULL,
			postcat_cat_ID int(10) unsigned NOT NULL,
			postcat_order DOUBLE NULL,
			PRIMARY KEY postcat_pk (postcat_post_ID,postcat_cat_ID),
			UNIQUE catpost ( postcat_cat_ID, postcat_post_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_comments' => [	// Note: pingbacks no longer supported, but previous pingbacks are to be preserved in the DB
        'Creating table for Comments',
        "CREATE TABLE T_comments (
			comment_ID                 int(10) unsigned NOT NULL auto_increment,
			comment_item_ID            int(10) unsigned NOT NULL default 0,
			comment_type               enum('comment','linkback','trackback','pingback','meta','webmention') COLLATE ascii_general_ci NOT NULL default 'comment',
			comment_status             ENUM('published','community','deprecated','protected','private','review','draft','trash') COLLATE ascii_general_ci DEFAULT 'draft' NOT NULL,
			comment_in_reply_to_cmt_ID INT(10) unsigned NULL,
			comment_author_user_ID     int unsigned NULL default NULL,
			comment_author             varchar(100) COLLATE utf8mb4_unicode_ci NULL,
			comment_author_email       varchar(255) COLLATE ascii_general_ci NULL,
			comment_author_url         varchar(255) NULL,
			comment_author_IP          varchar(45) COLLATE ascii_general_ci NOT NULL default '',"/* IPv4 mapped IPv6 addresses maximum length is 45 chars: ex. ABCD:ABCD:ABCD:ABCD:ABCD:ABCD:192.168.158.190 */ . "
			comment_IP_ctry_ID         int(10) unsigned NULL,
			comment_date               TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			comment_last_touched_ts    TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			comment_content            text COLLATE utf8mb4_unicode_ci NOT NULL,
			comment_renderers          VARCHAR(4000) COLLATE ascii_general_ci NOT NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			comment_rating             TINYINT(1) NULL DEFAULT NULL,
			comment_featured           TINYINT(1) NOT NULL DEFAULT 0,
			comment_author_url_nofollow  TINYINT(1) NOT NULL DEFAULT 1,
			comment_author_url_ugc       TINYINT(1) NOT NULL DEFAULT 1,
			comment_author_url_sponsored TINYINT(1) NOT NULL DEFAULT 0,
			comment_helpful_addvotes   INT NOT NULL default 0,
			comment_helpful_countvotes INT unsigned NOT NULL default 0,
			comment_spam_addvotes      INT NOT NULL default 0,
			comment_spam_countvotes    INT unsigned NOT NULL default 0,
			comment_karma              INT(11) NOT NULL DEFAULT 0,
			comment_spam_karma         TINYINT NULL,
			comment_allow_msgform      TINYINT NOT NULL DEFAULT 0,
			comment_anon_notify        TINYINT(1) NOT NULL DEFAULT 0,
			comment_anon_notify_last   VARCHAR(16) COLLATE ascii_general_ci NULL DEFAULT NULL,
			comment_secret             CHAR(32) COLLATE ascii_general_ci NULL default NULL,
			comment_notif_status       ENUM('noreq','todo','started','finished') COLLATE ascii_general_ci NOT NULL DEFAULT 'noreq' COMMENT 'Have notifications been sent for this comment? How far are we in the process?',
			comment_notif_ctsk_ID      INT(10) unsigned NULL DEFAULT NULL COMMENT 'When notifications for this comment are sent through a scheduled job, what is the job ID?',
			comment_notif_flags        SET('moderators_notified','members_notified','community_notified') COLLATE ascii_general_ci NOT NULL DEFAULT '',
			PRIMARY KEY comment_ID (comment_ID),
			KEY comment_item_ID (comment_item_ID),
			KEY comment_date (comment_date),
			KEY comment_type (comment_type),
			KEY comment_status(comment_status)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_comments__votes' => [
        'Creating table for Comments Votes',
        "CREATE TABLE T_comments__votes (
			cmvt_cmt_ID  int(10) unsigned NOT NULL,
			cmvt_user_ID int(10) unsigned NOT NULL,
			cmvt_helpful TINYINT(1) NULL DEFAULT NULL,
			cmvt_spam    TINYINT(1) NULL DEFAULT NULL,
			PRIMARY KEY (cmvt_cmt_ID, cmvt_user_ID),
			KEY cmvt_cmt_ID (cmvt_cmt_ID),
			KEY cmvt_user_ID (cmvt_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__prerendering' => [
        'Creating item prerendering cache table',
        "CREATE TABLE T_items__prerendering(
			itpr_itm_ID                   INT(10) UNSIGNED NOT NULL,
			itpr_format                   ENUM('htmlbody','entityencoded','xml','text') COLLATE ascii_general_ci NOT NULL,
			itpr_renderers                VARCHAR(4000) COLLATE ascii_general_ci NOT NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			itpr_content_prerendered      MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL,
			itpr_datemodified             TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			PRIMARY KEY (itpr_itm_ID, itpr_format)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_comments__prerendering' => [
        'Creating comment prerendering cache table',
        "CREATE TABLE T_comments__prerendering(
			cmpr_cmt_ID                   INT(10) UNSIGNED NOT NULL,
			cmpr_format                   ENUM('htmlbody','entityencoded','xml','text') COLLATE ascii_general_ci NOT NULL,
			cmpr_renderers                VARCHAR(4000) COLLATE ascii_general_ci NOT NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			cmpr_content_prerendered      MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL,
			cmpr_datemodified             TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			PRIMARY KEY (cmpr_cmt_ID, cmpr_format)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__version' => [	// fp> made iver_edit_user_ID NULL because of INSERT INTO SELECT statement that can try to write NULL
        'Creating item versions table',
        "CREATE TABLE T_items__version (
			iver_ID            INT UNSIGNED NOT NULL,
			iver_type          ENUM('archived','proposed') COLLATE ascii_general_ci NOT NULL DEFAULT 'archived',
			iver_itm_ID        INT UNSIGNED NOT NULL,
			iver_edit_user_ID  INT UNSIGNED NULL,
			iver_edit_last_touched_ts TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			iver_status        ENUM('published','community','deprecated','protected','private','review','draft','redirected') COLLATE ascii_general_ci NULL,
			iver_title         VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,"/* Do NOT change this field back to TEXT without a very good reason. */ . "
			iver_content       MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL,
			PRIMARY KEY        ( iver_ID , iver_type, iver_itm_ID ),
			INDEX iver_edit_user_ID ( iver_edit_user_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__version_custom_field' => [
        'Creating item version custom fields table',
        "CREATE TABLE T_items__version_custom_field (
			ivcf_iver_ID     INT UNSIGNED NOT NULL,
			ivcf_iver_type   ENUM('archived','proposed') COLLATE ascii_general_ci NOT NULL DEFAULT 'archived',
			ivcf_iver_itm_ID INT UNSIGNED NOT NULL,
			ivcf_itcf_ID     INT UNSIGNED NOT NULL,
			ivcf_itcf_label  VARCHAR(255) NOT NULL,
			ivcf_value       VARCHAR( 10000 ) NULL,
			PRIMARY KEY      ( ivcf_iver_ID, ivcf_iver_type, ivcf_iver_itm_ID, ivcf_itcf_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__version_link' => [
        'Creating item version links table',
        "CREATE TABLE T_items__version_link (
			ivl_iver_ID     INT UNSIGNED NOT NULL,
			ivl_iver_type   ENUM('archived','proposed') COLLATE ascii_general_ci NOT NULL DEFAULT 'archived',
			ivl_iver_itm_ID INT UNSIGNED NOT NULL,
			ivl_link_ID     INT(11) UNSIGNED NOT NULL,
			ivl_file_ID     INT(11) UNSIGNED NULL,
			ivl_position    VARCHAR(10) COLLATE ascii_general_ci NOT NULL,
			ivl_order       INT(11) UNSIGNED NOT NULL,
			PRIMARY KEY     ( ivl_iver_ID, ivl_iver_type, ivl_iver_itm_ID, ivl_link_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__itemgroup' => [
        'Creating table for Post Groups',
        "CREATE TABLE T_items__itemgroup (
			igrp_ID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (igrp_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__status' => [
        'Creating table for Post Statuses',
        "CREATE TABLE T_items__status (
			pst_ID   int(10) unsigned not null AUTO_INCREMENT,
			pst_name varchar(30) COLLATE utf8mb4_unicode_ci not null,
			pst_order   int(11) NULL DEFAULT NULL,
			primary key ( pst_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__type' => [
        'Creating table for Post Types',
        "CREATE TABLE T_items__type (
			ityp_ID                INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			ityp_name              VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
			ityp_description       TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_usage             VARCHAR(20) COLLATE ascii_general_ci NOT NULL DEFAULT 'post',
			ityp_template_excerpt  VARCHAR(128) COLLATE ascii_general_ci NULL DEFAULT NULL,
			ityp_template_normal   VARCHAR(128) COLLATE ascii_general_ci NULL DEFAULT NULL,
			ityp_template_full     VARCHAR(128) COLLATE ascii_general_ci NULL DEFAULT NULL,
			ityp_template_name     VARCHAR(40) NULL DEFAULT NULL,
			ityp_schema            ENUM( 'Article', 'WebPage', 'BlogPosting', 'ImageGallery', 'DiscussionForumPosting', 'TechArticle', 'Product', 'Review' ) COLLATE ascii_general_ci NULL DEFAULT NULL,
			ityp_add_aggregate_rating TINYINT DEFAULT 1,
			ityp_back_instruction  TINYINT DEFAULT 0,
			ityp_instruction       TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_text_template     TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_use_short_title   ENUM( 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_title         ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'required',
			ityp_use_url           ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_podcast           TINYINT(1) DEFAULT 0,
			ityp_use_parent        ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_text          ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_allow_html        TINYINT DEFAULT 1,
			ityp_allow_breaks      TINYINT DEFAULT 1,
			ityp_allow_attachments TINYINT DEFAULT 1,
			ityp_use_excerpt       ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_use_title_tag     ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_use_meta_desc     ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_use_meta_keywds   ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_use_tags          ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_allow_featured    TINYINT DEFAULT 1,
			ityp_allow_switchable  TINYINT DEFAULT 1,
			ityp_use_country       ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_region        ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_sub_region    ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_city          ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_coordinates   ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'never',
			ityp_use_comments      TINYINT DEFAULT 1,
			ityp_comment_form_msg         TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_allow_comment_form_msg   TINYINT DEFAULT 0,
			ityp_allow_closing_comments   TINYINT DEFAULT 1,
			ityp_allow_disabling_comments TINYINT DEFAULT 0,
			ityp_use_comment_expiration   ENUM( 'required', 'optional', 'never' ) COLLATE ascii_general_ci DEFAULT 'optional',
			ityp_perm_level               ENUM( 'standard', 'restricted', 'admin' ) COLLATE ascii_general_ci NOT NULL default 'standard',
			ityp_evobar_link_text         VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_skin_btn_text            VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			ityp_short_title_maxlen       SMALLINT UNSIGNED DEFAULT 30,
			ityp_title_maxlen             SMALLINT UNSIGNED DEFAULT 100,
			ityp_front_order_title        SMALLINT NULL,
			ityp_front_order_short_title  SMALLINT NULL,
			ityp_front_order_instruction  SMALLINT NULL,
			ityp_front_order_attachments  SMALLINT NULL,
			ityp_front_order_workflow     SMALLINT NULL,
			ityp_front_order_text         SMALLINT NULL,
			ityp_front_order_tags         SMALLINT NULL,
			ityp_front_order_excerpt      SMALLINT NULL,
			ityp_front_order_url          SMALLINT NULL,
			ityp_front_order_location     SMALLINT NULL,
			PRIMARY KEY ( ityp_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__type_custom_field' => [
        'Creating table for custom fields of Post Types',
        "CREATE TABLE T_items__type_custom_field (
			itcf_ID              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			itcf_ityp_ID         INT(10) UNSIGNED NOT NULL,
			itcf_label           VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			itcf_name            VARCHAR(255) COLLATE ascii_general_ci NOT NULL,
			itcf_schema_prop     VARCHAR(255) COLLATE ascii_general_ci NULL,
			itcf_type            ENUM( 'double', 'varchar', 'text', 'html', 'url', 'image', 'computed', 'separator' ) COLLATE ascii_general_ci NOT NULL,
			itcf_order           INT NULL,
			itcf_note            VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
			itcf_required        TINYINT DEFAULT 0,
			itcf_meta            TINYINT DEFAULT 0,
			itcf_public          TINYINT DEFAULT 1,
			itcf_format          VARCHAR(2000) COLLATE utf8mb4_unicode_ci NULL,
			itcf_formula         VARCHAR(2000) COLLATE ascii_general_ci NULL,
			itcf_disp_condition  VARCHAR(2000) COLLATE utf8mb4_unicode_ci NULL,
			itcf_header_class    VARCHAR(255) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_cell_class      VARCHAR(255) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_link            ENUM( 'nolink', 'linkto', 'permalink', 'zoom', 'linkpermzoom', 'permzoom', 'linkperm', 'fieldurl', 'fieldurlblank' ) COLLATE ascii_general_ci NOT NULL default 'nolink',
			itcf_link_nofollow   TINYINT NULL DEFAULT 0,
			itcf_link_class      VARCHAR(255) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_line_highlight  ENUM( 'never', 'differences', 'always' ) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_green_highlight ENUM( 'never', 'lowest', 'highest' ) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_red_highlight   ENUM( 'never', 'lowest', 'highest' ) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itcf_description     TEXT NULL,
			itcf_merge           TINYINT DEFAULT 0,
			PRIMARY KEY ( itcf_ID ),
			UNIQUE itcf_ityp_ID_name( itcf_ityp_ID, itcf_name )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__type_coll' => [
        'Creating table for PostType-to-Collection relationships',
        "CREATE TABLE T_items__type_coll (
			itc_ityp_ID int(10) unsigned NOT NULL,
			itc_coll_ID int(10) unsigned NOT NULL,
			PRIMARY KEY (itc_ityp_ID, itc_coll_ID),
			UNIQUE itemtypecoll ( itc_ityp_ID, itc_coll_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__status_type' => [
        'Creating table for PostType-to-Status relationships',
        "CREATE TABLE T_items__status_type (
			its_pst_ID INT(10) UNSIGNED NOT NULL,
			its_ityp_ID INT(10) UNSIGNED NOT NULL,
			PRIMARY KEY ( its_ityp_ID, its_pst_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__tag' => [
        'Creating table for Tags',
        "CREATE TABLE T_items__tag (
			tag_ID   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			tag_name VARCHAR(50) COLLATE utf8mb4_bin NOT NULL,
			PRIMARY KEY (tag_ID),
			UNIQUE tag_name( tag_name )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__itemtag' => [
        'Creating table for Post-to-Tag relationships',
        "CREATE TABLE T_items__itemtag (
			itag_itm_ID int(10) unsigned NOT NULL,
			itag_tag_ID int(10) unsigned NOT NULL,
			PRIMARY KEY (itag_itm_ID, itag_tag_ID),
			UNIQUE tagitem ( itag_tag_ID, itag_itm_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__subscriptions' => [
        'Creating table for subscriptions to individual blog posts',
        "CREATE TABLE T_items__subscriptions (
			isub_item_ID    int(10) unsigned NOT NULL,
			isub_user_ID    int(10) unsigned NOT NULL,
			isub_comments   tinyint(1) NOT NULL DEFAULT 0 COMMENT 'The user wants to receive notifications for new comments on this post',
			PRIMARY KEY (isub_item_ID, isub_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__item_settings' => [
        'Creating item settings table',
        "CREATE TABLE T_items__item_settings (
			iset_item_ID  int(10) unsigned NOT NULL,
			iset_name     varchar( 50 ) COLLATE ascii_general_ci NOT NULL,
			iset_value    varchar( 10000 ) COLLATE utf8mb4_unicode_ci NULL,
			PRIMARY KEY ( iset_item_ID, iset_name )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__item_custom_field' => [
        'Creating item custom field values table',
        "CREATE TABLE T_items__item_custom_field (
			icfv_item_ID     INT UNSIGNED NOT NULL,
			icfv_itcf_name   VARCHAR(255) COLLATE ascii_general_ci NOT NULL,
			icfv_value       VARCHAR( 10000 ) COLLATE utf8mb4_unicode_ci NULL,
			icfv_parent_sync TINYINT(1) NOT NULL DEFAULT 1,
			PRIMARY KEY      ( icfv_item_ID, icfv_itcf_name )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__user_data' => [
        'Creating table for user post data',
        "CREATE TABLE T_items__user_data (
			itud_user_ID          INT(10) UNSIGNED NOT NULL,
			itud_item_ID          INT(10) UNSIGNED NOT NULL,
			itud_read_item_ts     TIMESTAMP NULL DEFAULT NULL,
			itud_read_comments_ts TIMESTAMP NULL DEFAULT NULL,
			itud_flagged_item     TINYINT(1) NOT NULL DEFAULT 0,
			PRIMARY KEY ( itud_user_ID, itud_item_ID )
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__votes' => [
        'Creating table for Items Votes',
        "CREATE TABLE T_items__votes (
			itvt_item_ID INT UNSIGNED NOT NULL,
			itvt_user_ID INT UNSIGNED NOT NULL,
			itvt_updown  TINYINT(1) NULL DEFAULT NULL,
			itvt_report  ENUM( 'clean', 'rated', 'adult', 'inappropriate', 'spam' ) COLLATE ascii_general_ci NULL DEFAULT NULL,
			itvt_ts      TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
			PRIMARY KEY (itvt_item_ID, itvt_user_ID),
			KEY itvt_item_ID (itvt_item_ID),
			KEY itvt_user_ID (itvt_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_items__checklist_lines' => [
        'Creating table for checklists',
        "CREATE TABLE T_items__checklist_lines (
			check_ID      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			check_item_ID INT(10) UNSIGNED NOT NULL,
			check_checked TINYINT(1) NOT NULL DEFAULT 0,
			check_label   VARCHAR( 10000 ) COLLATE utf8mb4_unicode_ci NOT NULL,
			check_order   INT(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (check_ID),
			KEY check_item_ID (check_item_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_subscriptions' => [
        'Creating table for subscriptions',
        "CREATE TABLE T_subscriptions (
			sub_coll_ID     int(10) unsigned    not null,
			sub_user_ID     int(10) unsigned    not null,
			sub_items       tinyint(1)          not null,
			sub_items_mod   TINYINT(1)          NOT NULL,
			sub_comments    tinyint(1)          not null,
			primary key (sub_coll_ID, sub_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    // Important if you change the perm_poststatuses or perm_cmtstatuses set content or order you must change the get_statuse_permvalue() content respectively
    'T_coll_user_perms' => [
        'Creating table for Blog-User permissions',
        "CREATE TABLE T_coll_user_perms (
			bloguser_blog_ID              int(10) unsigned NOT NULL default 0,
			bloguser_user_ID              int(10) unsigned NOT NULL default 0,
			bloguser_ismember             tinyint NOT NULL default 0,
			bloguser_can_be_assignee      tinyint NOT NULL default 0,
			bloguser_workflow_status      tinyint NOT NULL default 0,
			bloguser_workflow_user        tinyint NOT NULL default 0,
			bloguser_workflow_priority    tinyint NOT NULL default 0,
			bloguser_perm_item_propose    tinyint NOT NULL default 0,
			bloguser_perm_poststatuses    set('review','draft','private','protected','deprecated','community','published','redirected') COLLATE ascii_general_ci NOT NULL default '',
			bloguser_perm_item_type       ENUM('standard','restricted','admin') COLLATE ascii_general_ci NOT NULL default 'standard',
			bloguser_perm_edit            ENUM('no','own','lt','le','all') COLLATE ascii_general_ci NOT NULL default 'no',
			bloguser_perm_delpost         tinyint NOT NULL default 0,
			bloguser_perm_edit_ts         tinyint NOT NULL default 0,
			bloguser_perm_delcmts         tinyint NOT NULL default 0,
			bloguser_perm_recycle_owncmts tinyint NOT NULL default 0,
			bloguser_perm_vote_spam_cmts  tinyint NOT NULL default 0,
			bloguser_perm_cmtstatuses     set('review','draft','private','protected','deprecated','community','published') COLLATE ascii_general_ci NOT NULL default '',
			bloguser_perm_edit_cmt        ENUM('no','own','anon','lt','le','all') COLLATE ascii_general_ci NOT NULL default 'no',
			bloguser_perm_meta_comment    tinyint NOT NULL default 0,
			bloguser_perm_cats            tinyint NOT NULL default 0,
			bloguser_perm_properties      tinyint NOT NULL default 0,
			bloguser_perm_admin           tinyint NOT NULL default 0,
			bloguser_perm_media_upload    tinyint NOT NULL default 0,
			bloguser_perm_media_browse    tinyint NOT NULL default 0,
			bloguser_perm_media_change    tinyint NOT NULL default 0,
			bloguser_perm_analytics       tinyint NOT NULL default 0,
			PRIMARY KEY bloguser_pk (bloguser_blog_ID,bloguser_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    // Important if you change the perm_poststatuses or perm_cmtstatuses set content or order you must change the get_status_permvalue() content respectively
    'T_coll_group_perms' => [
        'Creating table for blog-group permissions',
        "CREATE TABLE T_coll_group_perms (
			bloggroup_blog_ID              int(10) unsigned NOT NULL default 0,
			bloggroup_group_ID             int(10) unsigned NOT NULL default 0,
			bloggroup_ismember             tinyint NOT NULL default 0,
			bloggroup_can_be_assignee      tinyint NOT NULL default 0,
			bloggroup_workflow_status      tinyint NOT NULL default 0,
			bloggroup_workflow_user        tinyint NOT NULL default 0,
			bloggroup_workflow_priority    tinyint NOT NULL default 0,
			bloggroup_perm_item_propose    tinyint NOT NULL default 0,
			bloggroup_perm_poststatuses    set('review','draft','private','protected','deprecated','community','published','redirected') COLLATE ascii_general_ci NOT NULL default '',
			bloggroup_perm_item_type       ENUM('standard','restricted','admin') COLLATE ascii_general_ci NOT NULL default 'standard',
			bloggroup_perm_edit            ENUM('no','own','lt','le','all') COLLATE ascii_general_ci NOT NULL default 'no',
			bloggroup_perm_delpost         tinyint NOT NULL default 0,
			bloggroup_perm_edit_ts         tinyint NOT NULL default 0,
			bloggroup_perm_delcmts         tinyint NOT NULL default 0,
			bloggroup_perm_recycle_owncmts tinyint NOT NULL default 0,
			bloggroup_perm_vote_spam_cmts  tinyint NOT NULL default 0,
			bloggroup_perm_cmtstatuses     set('review','draft','private','protected','deprecated','community','published') COLLATE ascii_general_ci NOT NULL default '',
			bloggroup_perm_edit_cmt        ENUM('no','own','anon','lt','le','all') COLLATE ascii_general_ci NOT NULL default 'no',
			bloggroup_perm_meta_comment    tinyint NOT NULL default 0,
			bloggroup_perm_cats            tinyint NOT NULL default 0,
			bloggroup_perm_properties      tinyint NOT NULL default 0,
			bloggroup_perm_admin           tinyint NOT NULL default 0,
			bloggroup_perm_media_upload    tinyint NOT NULL default 0,
			bloggroup_perm_media_browse    tinyint NOT NULL default 0,
			bloggroup_perm_media_change    tinyint NOT NULL default 0,
			bloggroup_perm_analytics       tinyint NOT NULL default 0,
			PRIMARY KEY bloggroup_pk (bloggroup_blog_ID,bloggroup_group_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_coll_user_favs' => [
        'Creating table for user favorite collections',
        "CREATE TABLE T_coll_user_favs (
			cufv_user_ID    int(10) unsigned NOT NULL,
			cufv_blog_ID    int(10) unsigned NOT NULL,
			PRIMARY KEY cufv_pk (cufv_user_ID, cufv_blog_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_links' => [
        'Creating table for Links',
        "CREATE TABLE T_links (
			link_ID               int(10) unsigned  not null AUTO_INCREMENT,
			link_datecreated      TIMESTAMP         NOT NULL DEFAULT '2000-01-01 00:00:00',
			link_datemodified     TIMESTAMP         NOT NULL DEFAULT '2000-01-01 00:00:00',
			link_creator_user_ID  int(10) unsigned  NULL,
			link_lastedit_user_ID int(10) unsigned  NULL,
			link_itm_ID           int(10) unsigned  NULL,
			link_cmt_ID           int(10) unsigned  NULL COMMENT 'Used for linking files to comments (comment attachments)',
			link_usr_ID           int(10) unsigned  NULL COMMENT 'Used for linking files to users (user profile picture)',
			link_ecmp_ID          int(10) unsigned  NULL COMMENT 'Used for linking files to email campaign',
			link_msg_ID           int(10) unsigned  NULL COMMENT 'Used for linking files to private message',
			link_tmp_ID           int(10) unsigned  NULL COMMENT 'Used for linking files to new creating object',
			link_file_ID          int(10) unsigned  NULL,
			link_position         varchar(10) COLLATE ascii_general_ci NOT NULL,
			link_order            int(11) unsigned  NOT NULL,
			PRIMARY KEY (link_ID),
			UNIQUE link_itm_ID_order (link_itm_ID, link_order),
			INDEX link_itm_ID( link_itm_ID ),
			INDEX link_cmt_ID( link_cmt_ID ),
			INDEX link_usr_ID( link_usr_ID ),
			INDEX link_file_ID (link_file_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_temporary_ID' => [
        'Creating table for temporary IDs (used for uploads on new posts or messages)',
        "CREATE TABLE T_temporary_ID (
			tmp_ID      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			tmp_type    VARCHAR(32) COLLATE ascii_general_ci NOT NULL,
			tmp_coll_ID INT(10) UNSIGNED NULL,
			tmp_item_ID INT(11) UNSIGNED NULL COMMENT 'Link to parent Item of Comment in order to enable permission checks',
			PRIMARY KEY (tmp_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],

    'T_links__vote' => [
        'Creating table for File Links Votes',
        "CREATE TABLE T_links__vote (
			lvot_link_ID       int(10) UNSIGNED NOT NULL,
			lvot_user_ID       int(10) UNSIGNED NOT NULL,
			lvot_like          tinyint(1),
			lvot_inappropriate tinyint(1),
			lvot_spam          tinyint(1),
			primary key (lvot_link_ID, lvot_user_ID)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset"],
]);
