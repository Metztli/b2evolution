<?php
/**
 * This file implements the Item class.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2004-2006 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * Includes:
 */
load_funcs('items/model/_item.funcs.php');
load_class('slugs/model/_slug.class.php', 'Slug');
load_class('links/model/_linkowner.class.php', 'LinkOwner');
load_class('links/model/_linkitem.class.php', 'LinkItem');

/**
 * Item Class
 *
 * @package evocore
 */
class Item extends ItemLight
{
    /**
     * Creation date (timestamp)
     * @var integer
     */
    public $datecreated;

    /**
     * The User who has created the Item (lazy-filled).
     * @see Item::get_creator_User()
     * @see Item::set_creator_User()
     * @var User
     * @access protected
     */
    public $creator_User;

    /**
     * The User who has edited the Item last time (lazy-filled).
     * @see Item::get_lastedit_User()
     * @var User
     * @access protected
     */
    public $lastedit_User;

    /**
     * ID of the user who has edited the Item last time
     * @var integer
     */
    public $lastedit_user_ID;

    /**
     * Date when comments or links were added/edited/deleted for this Item last time (timestamp)
     * @see Item::update_last_touched_date()
     * @var integer
     */
    public $last_touched_ts;

    /**
     * Date when contents were updated for this Item last time (timestamp)
     * @see Item::update_last_touched_date()
     * @var integer
     */
    public $contents_last_updated_ts;

    /**
     * The latest Comment on this Item (lazy-filled).
     * @see Item::get_latest_Comment()
     * @var Comment
     * @access protected
     */
    public $latest_Comment;

    /**
     * @deprecated by {@link $creator_User}
     * @var User
     */
    public $Author;

    /**
     * ID of the user that created the item
     * @var integer
     */
    public $creator_user_ID;

    /**
     * Login of the user that created the item (lazy-filled)
     * @var string
     */
    public $creator_user_login;

    /**
     * The assigned User to the item.
     * Can be NULL
     * @see Item::get_assigned_User()
     * @see Item::assign_to()
     *
     * @var User
     * @access protected
     */
    public $assigned_User;

    /**
     * ID of the User assigned to the item
     * Can be NULL
     *
     * @var integer
     */
    public $assigned_user_ID;

    /**
     * Flag to know if item is assigned to a new user
     * Used to determine if assignment notification should be sent
     * @var boolean
     */
    public $assigned_to_new_user;

    /**
     * The visibility status of the item.
     *
     * 'published', 'community', 'deprecated', 'protected', 'private', 'review' or 'draft'
     *
     * @var string
     */
    public $status;

    /**
     * Item previous visibility status. It will be set only if the item status was changed.
     * @var string
     */
    public $previous_status;

    /**
     * Locale code for the Item content.
     *
     * Examples: en-US, zh-CN-utf-8
     *
     * @var string
     */
    public $locale;

    /**
     * Display the Item in list depending on navigation locale
     *
     * 0 - Always show
     * 1 - Show only if matching navigation locale
     *
     * @var integer|boolean
     */
    public $locale_visibility = 'always';

    public $content;

    /**
     * Flag to know if content was updated during current request
     * Used to update excerpt
     * @var boolean
     */
    public $content_is_updated;

    public $titletag;

    /**
     * Lazy filled, use split_page()
     */
    public $content_pages = null;

    public $wordcount;

    /**
     * The list of renderers, imploded by '.'.
     * @var string
     * @access protected
     */
    public $renderers;

    /**
     * Comments status
     *
     * "open", "disabled" or "closed
     *
     * @var string
     */
    public $comment_status;

    public $pst_ID;

    public $datedeadline = '';

    public $priority;

    /**
     * @var array Item orders per category
     */
    public $orders;

    /**
     * @var boolean
     */
    public $featured;

    /**
     * Have post processing notifications been handled?
     * @var string
     */
    public $notifications_status;

    /**
     * Which cron task is responsible for handling notifications?
     * @var integer
     */
    public $notifications_ctsk_ID;

    /**
     * What have been notified?
     * Possible values, separated by comma: 'moderators_notified,members_notified,community_notified,pings_sent'
     * @var string
     */
    public $notifications_flags;

    /**
     * array of IDs or NULL if we don't know...
     *
     * @var array
     */
    public $extra_cat_IDs = null;

    /**
     * Has the publish date been explicitly set?
     *
     * @var integer
     */
    public $dateset = 1;

    public $priorities;

    /**
     * @access protected
     * @see Item::get_excerpt()
     * @var string
     */
    public $excerpt;

    /**
     * Is the excerpt autogenerated?
     * This will become false when we receive an excerpt that was manually changed in the edit form.
     * @access protected
     * @var boolean
     */
    public $excerpt_autogenerated = 1;

    /**
     * Location IDs
     * @var integer
     */
    public $ctry_ID = null;

    public $rgn_ID = null;

    public $subrg_ID = null;

    public $city_ID = null;

    /**
     * ID of parent Item.
     * @var integer
     */
    public $parent_ID = null;

    /**
     * Parent Item.
     * @var object
     */
    public $parent_Item = null;

    /**
     * Item Group ID
     * @var integer
     */
    public $igrp_ID = null;

    /**
     * Other versions of this Item, linked by igrp_ID
     * @var array
     */
    public $other_version_items;

    /**
     * Additional settings for the items.  lazy filled.
     *
     * @see Item::get_setting()
     * @see Item::set_setting()
     * @see Item::load_ItemSettings()
     * Any non vital params should go into there.
     *
     * @var ItemSettings
     */
    public $ItemSettings;

    /**
     * Current User read status on this post content ( Only about the post content and not about the post's comments ).
     * This value is not saved into the db. Lazy filled.
     *
     * @var string ( read, new, updated )
     */
    public $content_read_status = null;

    /**
     * The Type of the Item (lazy filled, use {@link get_ItemType()} to access it.
     * @access protected
     * @var object ItemType
     */
    public $ItemType;

    /**
     * Voting result of all votes
     *
     * @var integer
     */
    public $addvotes;

    /**
     * A count of all votes
     *
     * @var integer
     */
    public $countvotes;

    /**
     * Lazy filled, use {@link get_social_media_image()} to access it.
     */
    public $social_media_image_File = null;

    /**
     * Current revision
     *
     * @var integer
     */
    public $revision;

    /**
     * Cached revisions in order to don't load them twice
     *
     * @var array
     */
    public $revisions;

    /**
     * Constructor
     *
     * @param object table Database row
     * @param string
     * @param string
     * @param string
     * @param string for derived classes
     * @param string datetime field name
     * @param string datetime field name
     * @param string User ID field name
     * @param string User ID field name
     */
    public function __construct(
        $db_row = null,
        $dbtable = 'T_items__item',
        $dbprefix = 'post_',
        $dbIDname = 'post_ID',
        $objtype = 'Item',
        $datecreated_field = 'datecreated',
        $datemodified_field = 'datemodified',
        $creator_field = 'creator_user_ID',
        $lasteditor_field = 'lastedit_user_ID'
    ) {
        global $localtimenow, $default_locale, $current_User;

        // Call parent constructor:
        parent::__construct(
            $db_row,
            $dbtable,
            $dbprefix,
            $dbIDname,
            $objtype,
            $datecreated_field,
            $datemodified_field,
            $creator_field,
            $lasteditor_field
        );

        if (is_null($db_row)) { // New item:
            global $Collection, $Blog;

            if (isset($current_User)) { // use current user as default, if available (which won't be the case during install)
                $this->creator_user_login = $current_User->login;
                $this->set_creator_User($current_User);
            }
            $this->set('dateset', 0);	// Date not explicitly set yet
            $this->set('notifications_status', 'noreq');
            // Set the renderer list to 'default' will trigger all 'opt-out' renderers:
            $this->set('renderers', ['default']);
            $this->set('priority', 3);
            if (! empty($Blog)) { // Get default post type from blog setting
                $default_post_type = $Blog->get_setting('default_post_type');
            }
            $this->set('ityp_ID', (empty($default_post_type) ? 1 /* Post */ : $default_post_type));
            // Set default locale for new item:
            if (! empty($Blog)) {	// Set locale depending on collection setting:
                switch ($Blog->get_setting('new_item_locale_source')) {
                    case 'select_coll':
                        // Use locale of current collection by default:
                        $new_item_locale = $Blog->get('locale');
                        break;
                    case 'select_user':
                        // Use locale of current user by default:
                        if (is_logged_in()) {	// If current user is logged in
                            $new_item_locale = $current_User->get('locale');
                        }
                        break;
                }
            }
            $this->set('locale', (isset($new_item_locale) ? $new_item_locale : $default_locale));
            $this->set('status', 'draft');
        } else {
            $this->datecreated = $db_row->post_datecreated;           // When Item was created in the system

            // post_last_touched_ts : When Item received last visible change (edit, comment, etc.)
            // Used for:
            //   - Sorting posts if configured this way in collection features.
            // Updated when:
            //   - ANY item field is updated,
            //   - link, unlink an attachment, update an attached file, change a link order
            //   - any child COMMENT of the post is added/updated/deleted,
            //   - link, unlink an attachment, update an attached file, change a link order on any comment
            $this->last_touched_ts = $db_row->post_last_touched_ts;

            // post_contents_last_updated_ts : When Item received last content change
            // Used for:
            //   - Knowing if current user has seen the updates on the post
            //   - Sorting forums (by default; can be changed in collection features)
            // Updated only when:
            //   - at least ONE of the fields: title, content, url is updated --> Especially: don't update on status change, workflow change, because it doesn't affect whether users have seen latest content changes or not
            //   - link, unlink an attachment, update an attached file (note: link order changes are not recorded because it doesn't affect whether users have seen lastest content changes)
            //   - a child COMMENT of the post that can be seen in the front-office is added or updated (only Content or Rating fields, or front-office visibility is changed from NOT front-office visibility) (but don't update on deleted comments or invisible comments -- When deleting a comment we actually recompute an OLDER timestamp based on last remaining comment, Also we recompute this when move a front-office visibility latest comment to other post OR when the latest comment becomes invisible for front-office)
            //   - link, unlink an attachment, update an attached file on child comments that may be seen in front office (note: link order changes are not recorded because it doesn't affect whether users have seen latest content changes)
            $this->contents_last_updated_ts = $db_row->post_contents_last_updated_ts;

            $this->creator_user_ID = $db_row->post_creator_user_ID;   // Needed for history display
            $this->lastedit_user_ID = $db_row->post_lastedit_user_ID; // Needed for history display
            $this->assigned_user_ID = $db_row->post_assigned_user_ID;
            $this->dateset = $db_row->post_dateset;
            $this->status = $db_row->post_status;
            $this->content = $db_row->post_content;
            $this->titletag = $db_row->post_titletag;
            $this->pst_ID = $db_row->post_pst_ID;
            $this->datedeadline = $db_row->post_datedeadline;
            $this->priority = $db_row->post_priority;
            $this->locale = $db_row->post_locale;
            $this->locale_visibility = $db_row->post_locale_visibility;
            $this->wordcount = $db_row->post_wordcount;
            $this->notifications_status = $db_row->post_notifications_status;
            $this->notifications_ctsk_ID = $db_row->post_notifications_ctsk_ID;
            $this->notifications_flags = $db_row->post_notifications_flags;
            $this->comment_status = $db_row->post_comment_status;			// Comments status
            $this->featured = $db_row->post_featured;
            $this->parent_ID = $db_row->post_parent_ID === null ? null : intval($db_row->post_parent_ID);
            $this->igrp_ID = $db_row->post_igrp_ID;

            // echo 'renderers=', $db_row->post_renderers;
            $this->renderers = $db_row->post_renderers;

            $this->excerpt = $db_row->post_excerpt;
            $this->excerpt_autogenerated = $db_row->post_excerpt_autogenerated;

            // Location

            if (! empty($db_row->post_ctry_ID)) {
                $this->ctry_ID = $db_row->post_ctry_ID;
            }

            if (! empty($db_row->post_rgn_ID)) {
                $this->rgn_ID = $db_row->post_rgn_ID;
            }

            if (! empty($db_row->post_subrg_ID)) {
                $this->subrg_ID = $db_row->post_subrg_ID;
            }

            if (! empty($db_row->post_city_ID)) {
                $this->city_ID = $db_row->post_city_ID;
            }

            // Voting fields:
            $this->addvotes = $db_row->post_addvotes;
            $this->countvotes = $db_row->post_countvotes;
        }

        modules_call_method('constructor_item', [
            'Item' => &$this,
        ]);
    }

    /**
     * Compare two Items based on the title
     *
     * @param Item A
     * @param Item B
     * @return number -1 if A->title < B->title, 1 if A->title > B->title, 0 if A->title == B->title
     */
    public static function compare_items_by_title($a_Item, $b_Item)
    {
        if ($a_Item == null || $b_Item == null) {
            debug_die('Invalid item objects received to compare.');
        }

        return strcmp($a_Item->title, $b_Item->title);
    }

    /**
     * Compare two Items based on the short title if this field is not empty otherwise use title to compare
     *
     * @param Item A
     * @param Item B
     * @return number -1 if A->short_title < B->short_title, 1 if A->short_title > B->short_title, 0 if A->short_title == B->short_title
     */
    public static function compare_items_by_short_title($a_Item, $b_Item)
    {
        if ($a_Item == null || $b_Item == null) {
            debug_die('Invalid item objects received to compare.');
        }

        if (! empty($a_Item->short_title) && $a_Item->get_type_setting('use_short_title') == 'optional') {	// Use short title only if it is not empty and allowed by item type:
            $a_title_value = $a_Item->short_title;
        } else {	// Otherwise use title:
            $a_title_value = $a_Item->title;
        }

        if (! empty($b_Item->short_title) && $b_Item->get_type_setting('use_short_title') == 'optional') {	// Use short title only if it is not empty and allowed by item type:
            $b_title_value = $b_Item->short_title;
        } else {	// Otherwise use title:
            $b_title_value = $b_Item->title;
        }

        return strcmp($a_title_value, $b_title_value);
    }

    /**
     * Compare two Items based on the order field
     *
     * @param Item A
     * @param Item B
     * @return number -1 if A->order < B->order, 1 if A->order > B->order, 0 if A->order == B->order
     */
    public static function compare_items_by_order($a_Item, $b_Item)
    {
        if ($a_Item == null || $b_Item == null) {
            debug_die('Invalid item objects received to compare.');
        }

        // Get item orders depending on current category:
        $a_item_order = isset($a_Item->sort_current_cat_ID) ? $a_Item->sort_current_cat_ID : null;
        $b_item_order = isset($b_Item->sort_current_cat_ID) ? $b_Item->sort_current_cat_ID : null;

        if ($a_Item->get_order($a_item_order) == null) {
            return $b_Item->get_order($b_item_order) == null ? 0 : 1;
        } elseif ($b_Item->get_order($b_item_order) == null) {
            return -1;
        }

        return ($a_Item->get_order($a_item_order) < $b_Item->get_order($b_item_order)) ? -1 : (($a_Item->get_order($a_item_order) > $b_Item->get_order($b_item_order)) ? 1 : 0);
    }

    /**
     * Set creator user
     *
     * @param string login
     */
    public function set_creator_by_login($login)
    {
        $UserCache = &get_UserCache();
        if (($creator_User = &$UserCache->get_by_login($login)) !== false) {
            $this->set($this->creator_field, $creator_User->ID);
        }
    }

    /**
     * Assign user to the item by user ID or by user login
     *
     * @todo use extended dbchange instead of set_param...
     * @todo Normalize to set_assigned_User!?
     *
     * @param integer User ID
     * @param string User login
     * @param boolean TRUE to update DB
     * @return boolean TRUE on success, FALSE if user cannot be assigned on this item
     */
    public function assign_to($user_ID, $user_login = '', $dbupdate = true /* BLOAT!? */)
    {
        global $Messages;

        if (! empty($user_ID)) { // Get an user by ID to check perms
            $UserCache = &get_UserCache();
            $assigned_User = &$UserCache->get_by_ID($user_ID, false, false);
        } elseif (! empty($user_login)) { // If an assigned user ID is empty find it by user login
            $UserCache = &get_UserCache();
            $assigned_User = &$UserCache->get_by_login($user_login);
            if (empty($assigned_User)) { // Invalid user login was entered
                $Messages->add(sprintf(T_('User %s doesn\'t exist!'), '<b>' . $user_login . '</b>'), 'error');
                return false;
            }
        }

        if (! empty($assigned_User)) { // Check if the selected user can be assigned to this item
            $this->load_Blog();
            if ($assigned_User->check_perm('blog_can_be_assignee', 'edit', false, $this->Blog->ID)) { // User exists and has permission to be assigned user to items of the blog
                $user_ID = $assigned_User->ID;
            } else { // No permission to be assigned
                $Messages->add(sprintf(T_('User %s cannot be assigned to the items of this blog!'), '<b>' . $assigned_User->login . '</b>'), 'error');
                return false;
            }
        }

        if (! empty($user_ID)) {
            if ($dbupdate) { // Record ID for DB:
                $this->set_param('assigned_user_ID', 'number', $user_ID, true);
            } else {
                $this->assigned_user_ID = $user_ID;
            }
            $UserCache = &get_UserCache();
            $this->assigned_User = &$UserCache->get_by_ID($user_ID);
        } else {
            // fp>> DO NOT set (to null) immediately OR it may KILL the current User object (big problem if it's the Current User)
            unset($this->assigned_User);
            if ($dbupdate) { // Record ID for DB:
                $this->set_param('assigned_user_ID', 'number', null, true);
            } else {
                $this->assigned_User = null;
            }
            $this->assigned_user_ID = null;
        }

        return true;
    }

    /**
     * Template function: display author/creator of item
     */
    public function author($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'link_text' => 'preferredname', // auto | avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
            'thumb_size' => 'crop-top-32x32',
            'thumb_class' => '',
            'thumb_zoomable' => false,
            'login_mask' => '', // example: 'text $login$ text'
            'display_bubbletip' => true,
            'nowrap' => true,
        ], $params);

        // Load User
        $this->get_creator_User();

        $r = $this->creator_User->get_identity_link($params);

        echo $params['before'] . $r . $params['after'];
    }

    /**
     * Template function: display user who edited the item last time
     */
    public function lastedit_user($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'profile_tab' => 'user',
            'before' => ' ',
            'after' => ' ',
            'format' => 'htmlbody',
            'link_to' => 'userpage',
            'link_text' => 'preferredname', // avatar_name | avatar_login | only_avatar | name | login | nickname | firstname | lastname | fullname | preferredname
            'link_rel' => '',
            'link_class' => '',
            'thumb_size' => 'crop-top-32x32',
            'thumb_class' => '',
            'thumb_zoomable' => false,
        ], $params);

        // Load User
        $this->get_lastedit_User();

        if ($this->lastedit_User) {	// Get a link to user profile page
            $r = $this->lastedit_User->get_identity_link($params);
        } else {	// User was deleted
            $r = T_('(deleted user)');
        }

        echo $params['before'] . $r . $params['after'];
    }

    /**
     * Load data from Request form fields.
     *
     * This requires the blog (e.g. {@link $blog_ID} or {@link $main_cat_ID} to be set).
     *
     * @param boolean true if we are returning to edit mode (new, switchtab...)
     * @return boolean true if loaded data seems valid.
     */
    public function load_from_Request($editing = false, $creating = false)
    {
        global $default_locale, $localtimenow, $Blog, $Plugins;
        global $item_typ_ID;

        // LOCALE:
        if (param('post_locale', 'string', null) !== null) {
            $this->set_from_Request('locale');
        }

        // LOCALE VISIBILITY:
        if (param('post_locale_visibility', 'string', null) !== null) {
            $this->set_from_Request('locale_visibility');
        }

        if (param('source_version_item_ID', 'integer', null) !== null) {	// Temp flag to know this is a new version of this Item:
            $this->source_version_item_ID = get_param('source_version_item_ID');
        }

        // POST TYPE:
        $item_typ_ID = get_param('item_typ_ID');
        if (empty($item_typ_ID)) { // Try to get this from request if it has been not initialized by controller:
            $item_typ_ID = param('item_typ_ID', 'integer', null);
        }
        if (! empty($item_typ_ID)) { // Set new post type ID only if it is defined on request:
            $this->set('ityp_ID', $item_typ_ID);
        }

        // Check if this Item type usage is not content block in order to hide several fields below:
        $is_not_content_block = ($this->get_type_setting('usage') != 'content-block');

        // URL associated with Item:
        $post_url = param('post_url', 'string', null);
        if ($post_url !== null) {
            param_check_url('post_url', 'http-https');
            $this->set_from_Request('url');
        }
        if (empty($post_url) && $this->get_type_setting('use_url') == 'required') { // URL must be entered
            param_check_not_empty('post_url', T_('Please provide a "Link To" URL.'), '');
        }
        if (is_pro()) {	// Only PRO feature for using of post link URL as an External Canonical URL:
            $this->set_setting('external_canonical_url', param('post_external_canonical_url', 'integer', 0));
        }

        // Item parent ID:
        $post_parent_ID = param('post_parent_ID', 'integer', null);
        if ($post_parent_ID !== null) {	// If item parent ID is entered:
            $ItemCache = &get_ItemCache();
            if ($ItemCache->get_by_ID($post_parent_ID, false, false)) {	// Save only ID of existing item:
                $this->set_from_Request('parent_ID');
            } else {	// Display an error of the entered item parent ID is incorrect:
                param_error('post_parent_ID', T_('The parent ID is not a correct Item ID.'));
            }
        }
        if (empty($post_parent_ID)) {	// If empty parent ID is entered:
            if ($this->get_type_setting('use_parent') == 'required') {	// Item parent ID must be entered:
                param_check_not_empty('post_parent_ID', T_('Please provide a parent ID.'), '');
            } else {	// Remove parent ID:
                $this->set_from_Request('parent_ID');
            }
        }

        // Single/page view:
        if (check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID) &&
            ($single_view = param('post_single_view', 'string', null)) !== null) {	// If user has a permission to edit advanced properties of items:
            if ($this->get('status') == 'redirected') {	// Single view of "Redirected" item can be only redirected as well:
                $single_view = 'redirected';
            } elseif ($this->previous_status == 'redirected' && $single_view == 'redirected') {	// Set single view to normal mode when item status is updating from redirected to another status:
                $single_view = 'normal';
            }
            $this->set('single_view', $single_view);
        }

        if (($this->status == 'redirected' || $this->get('single_view') == 'redirected') && empty($this->url)) { // Note: post_url is not part of the simple form, so this message can be a little bit awkward there
            param_error(
                'post_url',
                T_('If you want to redirect this post, you must specify an URL!') . ' (' . T_('Advanced properties panel') . ')',
                T_('If you want to redirect this post, you must specify an URL!')
            );
        }

        // ISSUE DATE / TIMESTAMP:
        $this->load_Blog();
        if (check_user_perm('admin', 'restricted') &&
            check_user_perm('blog_edit_ts', 'edit', false, $this->Blog->ID)) { // Allow to update timestamp fields only if user has a permission to edit such fields
            //    and also if user has an access to back-office
            $item_dateset = param('item_dateset', 'integer', null);
            if ($item_dateset !== null) {
                $this->set('dateset', $item_dateset);

                if ($editing || $this->dateset == 1) { // We can use user date:
                    if (param_date('item_issue_date', sprintf(T_('Please enter a valid issue date using the following format: %s'), '<code>' . locale_input_datefmt() . '</code>'), true)
                        && param_time('item_issue_time')) { // only set it, if a (valid) date and time was given:
                        $this->set('issue_date', form_date(get_param('item_issue_date'), get_param('item_issue_time'))); // TODO: cleanup...
                    }
                } elseif ($this->dateset == 0) { // Set date to NOW:
                    $this->set('issue_date', date('Y-m-d H:i:s', $localtimenow));
                }
            }
        }

        // SLUG:
        if (param('post_urltitle', 'string', null) !== null) {
            // Replace special chars/umlauts:
            load_funcs('locales/_charset.funcs.php');
            // Split slug url titles with comma because the function replace_special_chars()
            // converts `,` to `-`, so separate slugs are concatenated in single, that's wrong:
            $post_urltitle = explode(',', get_param('post_urltitle'));
            foreach ($post_urltitle as $u => $slug_urltitle) {
                $post_urltitle[$u] = replace_special_chars($slug_urltitle, $this->get('locale'));
                if (empty($post_urltitle[$u])) {	// Unset empty slug in order to create auto slug:
                    unset($post_urltitle[$u]);
                    continue;
                }
                // Added in May 2017; but old slugs are not converted yet.
                if (preg_match('#^[^a-z0-9]*[0-9]*[^a-z0-9]*$#i', $post_urltitle[$u])) {	// Display error if one of item slugs doesn't contain at least 1 non-numeric character:
                    param_error('post_urltitle', T_('All slugs must contain at least 1 non-numeric character.'));
                }
            }
            // Append old slugs at the end because they are not deleted on updating of the Item,
            // and update array of the cached slugs from DB in order to display proper slugs after submit the forms with errors:
            $this->get_slugs();
            $this->slugs = array_unique(array_merge($post_urltitle, $this->slugs));
            // Set new post urltitle:
            $this->set('urltitle', implode(', ', $post_urltitle));
        }

        if ($is_not_content_block) {	// Save title tag, meta description and meta keywords for item with type usage except of content block:
            // <title> TAG:
            $titletag = param('titletag', 'string', null);
            if ($titletag !== null) {
                $this->set_from_Request('titletag', 'titletag');
            }
            if (empty($titletag) && $this->get_type_setting('use_title_tag') == 'required') { // Title tag must be entered
                param_check_not_empty('titletag', T_('Please provide a title tag.'), '');
            }

            // <meta> DESC:
            $metadesc = param('metadesc', 'string', null);
            if ($metadesc !== null) {
                $this->set_setting('metadesc', get_param('metadesc'));
            }
            if (empty($metadesc) && $this->get_type_setting('use_meta_desc') == 'required') { // Meta description must be entered
                param_check_not_empty('metadesc', T_('Please provide a meta description.'), '');
            }

            // <meta> KEYWORDS:
            $metakeywords = param('metakeywords', 'string', null);
            if ($metakeywords !== null) {
                $this->set_setting('metakeywords', get_param('metakeywords'));
            }
            if (empty($metakeywords) && $this->get_type_setting('use_meta_keywds') == 'required') { // Meta keywords must be entered
                param_check_not_empty('metakeywords', T_('Please provide the meta keywords.'), '');
            }
        }

        // TAGS:
        $item_tags = param('item_tags', 'string', null);
        if ($item_tags !== null) {
            $this->set_tags_from_string(get_param('item_tags'));
            // Update setting 'suggest_item_tags' of the current User
            global $UserSettings;
            $UserSettings->set('suggest_item_tags', param('suggest_item_tags', 'integer', 0));
            $UserSettings->dbupdate();
        }
        if (empty($item_tags) && $this->get_type_setting('use_tags') == 'required') { // Tags must be entered
            param_check_not_empty('item_tags', T_('Please provide at least one tag.'), '');
        }

        // WORKFLOW stuff:
        $this->load_workflow_from_Request();

        // FEATURED checkbox:
        if (check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID)) {	// If user has a permission to edit advanced properties of items:
            $this->set('featured', param('item_featured', 'integer', 0), false);
        }

        // MUST READ checkbox:
        if (is_pro() &&
            ($item_Blog = &$this->get_Blog()) &&
            $item_Blog->get_setting('track_unread_content')) {	// Update only for PRO version and when tracking of unread content is enabled for collection:
            $this->set_setting('mustread', param('item_mustread', 'integer', 0));
        }

        if ($is_not_content_block) {	// Save "hide teaser" and goal for item with type usage except of content block:
            // HIDE TEASER checkbox:
            $this->set_setting('hide_teaser', param('item_hideteaser', 'integer', 0));

            // User Tagging:
            if (param('user_tags', 'string', null) !== null) {
                $this->set_setting('user_tags', trim(get_param('user_tags'), ' ,'));
            }

            // Goal ID:
            if (check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID)) {	// If user has a permission to edit advanced properties of items:
                $goal_ID = param('goal_ID', 'integer', null);
                if ($goal_ID !== null) {	// Save only if it is provided:
                    $this->set_setting('goal_ID', $goal_ID, true);
                }
            }
        }

        if ($this->get_type_setting('allow_switchable')) {	// Includes switchable content:
            $this->set_setting('switchable', param('item_switchable', 'integer', 0));
            $this->set_setting('switchable_params', param('item_switchable_params', 'string'));
        }

        // OWNER:
        $this->creator_user_login = param('item_owner_login', 'string', null);
        if (check_user_perm('users', 'edit') && param('item_owner_login_displayed', 'string', null) !== null) {	// only admins can change the owner..
            if (param_check_not_empty('item_owner_login', T_('Please enter valid owner login.'))) {	// If valid user login is entered:
                if (param('item_create_user', 'integer', 0)) {	// Try to create new user if it is checked on the edit item form:
                    $UserCache = &get_UserCache();

                    // Convert new entered login to proper login format:
                    $this->creator_user_login = preg_replace('/[^a-z0-9_\-\. ]/i', '', $this->creator_user_login);
                    $this->creator_user_login = str_replace(' ', '_', $this->creator_user_login);
                    $this->creator_user_login = utf8_substr($this->creator_user_login, 0, 20);
                    set_param('item_owner_login', $this->creator_user_login);

                    if (($creator_User = &$UserCache->get_by_login($this->creator_user_login)) !== false) {	// Display error if user already exists:
                        param_error('item_owner_login', sprintf(T_('User "%s" already exists.'), $this->creator_user_login));
                    } else {	// Create new user:
                        $item_new_User = new User();
                        $item_new_User->set('login', $this->creator_user_login);
                        $item_new_User->set('email', $this->creator_user_login . '@dummy.null');
                        $item_new_User->set('source', 'created alongside post');
                        $item_new_User->set('pass', '');
                        $item_new_User->set('salt', '');
                        $item_new_User->set('pass_driver', 'nopass');
                        $item_new_User->dbinsert();
                        // Update user login cache with new created User:
                        $UserCache->cache_login[$this->creator_user_login] = $item_new_User;
                        // Uncheck the checkbox to don't suggest create new user on next form updating because the user already has been created with requested login:
                        set_param('item_create_user', 0);
                    }
                }

                if (param_check_login('item_owner_login', true)) {	// Update item's owner if the user is detected in DB by the entered login:
                    $this->set_creator_by_login($this->creator_user_login);
                }
            }
        }

        // LOCATION COORDINATES:
        if ($this->get_type_setting('use_coordinates') != 'never') { // location coordinates are enabled, save map settings
            param('item_latitude', 'double', null); // get par value
            $this->set_setting('latitude', get_param('item_latitude'), true);
            param('item_longitude', 'double', null); // get par value
            $this->set_setting('longitude', get_param('item_longitude'), true);
            param('google_map_zoom', 'integer', null); // get par value
            $this->set_setting('map_zoom', get_param('google_map_zoom'), true);
            param('google_map_type', 'string', null); // get par value
            $this->set_setting('map_type', get_param('google_map_type'), true);
            if ($this->get_type_setting('use_coordinates') == 'required') { // The location coordinates are required
                param_check_not_empty('item_latitude', T_('Please provide a latitude.'), '');
                param_check_not_empty('item_longitude', T_('Please provide a longitude.'), '');
            }
        }

        // CUSTOM FIELDS:
        $this->load_custom_fields_from_Request();

        // COMMENTS:
        if ($this->allow_comment_statuses()) { // Save status of "Allow comments for this item" (only if comments are allowed in this blog, and by current post type
            $post_comment_status = param('post_comment_status', 'string', 'open');
            if (! empty($post_comment_status)) { // 'open' or 'closed' or ...
                $this->set_from_Request('comment_status');
            }
        }

        // MESSAGE BEFORE COMMENT FORM:
        if ($this->get_type_setting('allow_comment_form_msg')) {	// Save a mesage before comment form only if it is allowed by item type:
            $comment_form_msg = param('comment_form_msg', 'text', null);
            $this->set_setting('comment_form_msg', $comment_form_msg, true);
        }

        // EXPIRY DELAY:
        if (check_user_perm('blog_edit_ts', 'edit', false, $Blog->ID)) {	// If user has a permission to edit advanced properties of items:
            $expiry_delay = param_duration('expiry_delay');
            if (empty($expiry_delay)) { // Check if we have 'expiry_delay' param set as string from simple or mass form
                $expiry_delay = param('expiry_delay', 'string', null);
            }
            if (empty($expiry_delay) && $this->get_type_setting('use_comment_expiration') == 'required') { // Comment expiration must be entered
                param_check_not_empty('expiry_delay', T_('Please provide a comment expiration delay.'), '');
            }
            $this->set_setting('comment_expiry_delay', $expiry_delay, true);
        }

        // EXTRA PARAMS FROM MODULES:
        modules_call_method('update_item_settings', [
            'edited_Item' => $this,
        ]);

        // RENDERERS:
        $item_Blog = &$this->get_Blog();
        if (is_admin_page() || $item_Blog->get_setting('in_skin_editing_renderers')) {	// If text renderers are allowed to update from front-office:
            if (param('renderers_displayed', 'integer', 0)) {	// Use "renderers" value only if it has been displayed (may be empty):
                $renderers = $Plugins->validate_renderer_list(param('renderers', 'array:string', []), [
                    'Item' => &$this,
                ]);
                $this->set('renderers', $renderers);
            } else {
                $renderers = $this->get_renderers_validated();
            }
        } else {	// Don't allow to update the text renderers:
            $renderers = $this->get_renderers();
        }

        if ($this->get_type_setting('use_short_title') == 'optional') {	// Short title:
            $post_short_title = param('post_short_title', 'htmlspecialchars', null);
            $this->set_from_Request('short_title', 'post_short_title', true);
        }

        // CONTENT + TITLE:
        if ($this->get_type_setting('allow_html')) {	// HTML is allowed for this post, we'll accept HTML tags:
            $text_format = 'html';
        } else {	// HTML is disallowed for this post, we'll encode all special chars:
            $text_format = 'htmlspecialchars';
        }

        $editor_code = param('editor_code', 'string', null);
        if ($editor_code) { // Update item editor code if it was explicitly set
            $this->set_setting('editor_code', $editor_code);
        }

        // Never allow html content on post titles:  (fp> probably so as to not mess up backoffice and all sorts of tools)
        param('post_title', 'htmlspecialchars', null);
        // Title checking:
        if ((! $editing || $creating) && $this->get_type_setting('use_title') == 'required') { // creating is important, when the action is create_edit
            param_check_not_empty('post_title', T_('Please provide a title.'), '');
        }

        $content = param('content', $text_format, null);
        if ($content !== null) {
            // Do some optional filtering on the content
            // Typically stuff that will help the content to validate
            // Useful for code display.
            // Will probably be used for validation also.
            // + APPLY RENDERING from Rendering Plugins:
            $Plugins_admin = &get_Plugins_admin();
            $params = [
                'object_type' => 'Item',
                'object' => &$this,
                'object_Blog' => &$this->Blog,
            ];
            $Plugins_admin->filter_contents($GLOBALS['post_title'] /* by ref */, $GLOBALS['content'] /* by ref */, $renderers, $params /* by ref */);

            // Format raw HTML input to cleaned up and validated HTML:
            param_check_html('content', T_('Invalid content.'));
            $content = prepare_item_content(get_param('content'));

            $this->set('content', $content);
        }
        if (empty($content) && $this->get_type_setting('use_text') == 'required') { // Content must be entered
            param_check_not_empty('content', T_('Please enter some text.'), '');
        }

        // Set title only here because it may be filtered by plugins above:
        $this->set('title', get_param('post_title'), true);

        if ($is_not_content_block) {	// Save excerpt for item with type usage except of content block:
            // EXCERPT: (must come after content (in order to handle excerpt_autogenerated))
            $post_excerpt = param('post_excerpt', 'text', null);
            if ($post_excerpt !== null) {	// The form has sent an excerpt field:
                $post_excerpt_autogenerated = param('post_excerpt_autogenerated', 'integer', 0);
                $this->set_from_Request('excerpt_autogenerated');
                if (! $this->get('excerpt_autogenerated')) {	// The post excerpt must be no longer auto-generated:
                    // NOTE: if the new excerpt is empty, set() will switch back to autogeneration:
                    $this->set_from_Request('excerpt');
                }
            }

            if (empty($post_excerpt) && $this->get_type_setting('use_excerpt') == 'required') { // Content must be entered (this should happen even if no excerpt field was submitted)
                param_check_not_empty('post_excerpt', T_('Please provide an excerpt.'), '');
            }
        }

        // LOCATION (COUNTRY -> CITY):
        load_funcs('regional/model/_regional.funcs.php');
        if ($this->country_visible()) { // Save country
            $country_ID = param('item_ctry_ID', 'integer', 0);
            $country_is_required = $this->get_type_setting('use_country') == 'required'
                    && countries_exist();
            param_check_number('item_ctry_ID', T_('Please select a country'), $country_is_required);
            $this->set_from_Request('ctry_ID', 'item_ctry_ID', true);
        }

        if ($this->region_visible()) { // Save region
            $region_ID = param('item_rgn_ID', 'integer', 0);
            $region_is_required = $this->get_type_setting('use_region') == 'required'
                    && regions_exist($country_ID);
            param_check_number('item_rgn_ID', T_('Please select a region'), $region_is_required);
            $this->set_from_Request('rgn_ID', 'item_rgn_ID', true);
        }

        if ($this->subregion_visible()) { // Save subregion
            $subregion_ID = param('item_subrg_ID', 'integer', 0);
            $subregion_is_required = $this->get_type_setting('use_sub_region') == 'required'
                    && subregions_exist($region_ID);
            param_check_number('item_subrg_ID', T_('Please select a sub-region'), $subregion_is_required);
            $this->set_from_Request('subrg_ID', 'item_subrg_ID', true);
        }

        if ($this->city_visible()) { // Save city
            param('item_city_ID', 'integer', 0);
            $city_is_required = $this->get_type_setting('use_city') == 'required'
                    && cities_exist($country_ID, $region_ID, $subregion_ID);
            param_check_number('item_city_ID', T_('Please select a city'), $city_is_required);
            $this->set_from_Request('city_ID', 'item_city_ID', true);
        }

        if (is_admin_page() || $Blog->get_setting('in_skin_editing_category_order')) {	// Item orders per category:
            $post_cat_orders = param('post_cat_orders', 'array:string');
            $this->orders = [];
            foreach ($post_cat_orders as $post_cat_ID => $post_cat_order) {
                if (isset($this->extra_cat_IDs) &&
                        is_array($this->extra_cat_IDs) &&
                        in_array($post_cat_ID, $this->extra_cat_IDs)) {	// Set order only for selected category:
                    $this->orders[$post_cat_ID] = ($post_cat_order === '' ? null : floatval($post_cat_order));
                }
            }
        }

        // Call plugins events to load additional Item fields:
        $Plugins->trigger_event('ItemLoadFromRequest', $params = [
            'Item' => &$this,
        ]);

        return ! param_errors_detected();
    }

    /**
     * Load workflow properties from Request form fields.
     *
     * @return boolean TRUE if loaded data seems valid, FALSE if some errors or workflow is not allowed or no any property has been changed
     */
    public function load_workflow_from_Request()
    {
        // Get Item's Collection for settings and permissions validation:
        $item_Blog = &$this->get_Blog();

        if (($this->get_type_setting('usage') != 'content-block') && // Item types "Content Block" cannot have the workflow properties
            $this->can_edit_workflow()) { // Current User has a permission to edit at least one workflow property
            // Update workflow properties only when all conditions above is true:
            if ($this->can_edit_workflow('status') &&
                param('item_st_ID', 'integer', null) !== null) {	// Task status:
                $ItemTypeCache = &get_ItemTypeCache();
                $current_ItemType = $ItemTypeCache->get_by_ID($this->get('ityp_ID'));
                if (get_param('item_st_ID') === 0) {	// Store NULL value instead of 0 in DB:
                    set_param('item_st_ID', null);
                }
                if (in_array(get_param('item_st_ID'), $current_ItemType->get_applicable_post_status()) || get_param('item_st_ID') === null) {	// Save only task status which is allowed for item's type:
                    $this->set_from_Request('pst_ID', 'item_st_ID', true);
                } else {	// If the submitted task status is not allowed for item's type:
                    param_error('item_st_ID', sprintf(T_('Invalid task status for post type %s'), $current_ItemType->get_name()));
                }
            }

            if ($this->can_edit_workflow('user') &&
                param('item_assigned_user_ID', 'integer', null) !== null) {	// Assigned to:
                $item_assigned_user_ID = get_param('item_assigned_user_ID');
                $item_assigned_user_login = param('item_assigned_user_login', 'string', null);
                $this->assign_to($item_assigned_user_ID, $item_assigned_user_login);
            }

            if ($this->can_edit_workflow('priority') &&
                param('item_priority', 'integer', null) !== null) {	// Priority:
                if (get_param('item_priority') === 0) {	// Store NULL value instead of 0 in DB:
                    set_param('item_priority', null);
                }
                $this->set_from_Request('priority', 'item_priority', true);
            }

            if ($this->can_edit_workflow('deadline') &&
                param_date('item_deadline', T_('Please enter a valid deadline.'), false, null) !== null) {	// Deadline:
                param_time('item_deadline_time', '', false, false, true, true);
                $item_deadline_time = get_param('item_deadline') != '' ? substr(get_param('item_deadline_time'), 0, 5) : '';
                $item_deadline_datetime = trim(form_date(get_param('item_deadline'), $item_deadline_time));
                if (! empty($item_deadline_datetime)) {	// Append seconds because they are not entered on the form but they are stored in the DB field "post_datedeadline" as date format "YYYY-mm-dd HH:ii:ss":
                    $item_deadline_datetime .= ':00';
                }
                $this->set('datedeadline', $item_deadline_datetime, true);
            }

            // Return TRUE when no errors and at least one workflow property has been changed:
            return ! param_errors_detected() && (
                isset($this->dbchanges['post_assigned_user_ID']) ||
                isset($this->dbchanges['post_priority']) ||
                isset($this->dbchanges['post_pst_ID']) ||
                isset($this->dbchanges['post_datedeadline'])
            );
        }

        // If workflow properties are not allowed to be stored for this Item by current User:
        return false;
    }

    /**
     * Load custom fields values from Request form fields
     *
     * @param boolean TRUE to load only custom fields which are allowed to be updated with internal comment
     * @return boolean TRUE if loaded data seems valid, FALSE if some errors or no any property has been changed
     */
    public function load_custom_fields_from_Request($meta = null)
    {
        $custom_fields = $this->get_type_custom_fields();
        $this->dbchanges_custom_fields = [];
        $custom_fields_changed = false;
        foreach ($custom_fields as $custom_field) { // update each custom field
            if ($meta === true && ! $custom_field['meta']) {	// Skip not meta custom field when it is requested:
                continue;
            }
            $param_name = 'item_cf_' . $custom_field['name'];
            $param_error = false;
            if (isset_param($param_name)) { // param is set
                switch ($custom_field['type']) {
                    case 'double':
                        $param_type = 'double';
                        $field_value = param($param_name, 'string', null);
                        if (! empty($field_value) && ! preg_match('/^(\+|-)?[0-9 \'.,]+([.,][0-9]+)?$/', $field_value)) { // we could have used is_numeric here but this is how "double" type is checked in the param.funcs.php
                            param_error($param_name, sprintf(T_('Custom "%s" field must be a number'), $custom_field['label']));
                            $param_error = true;
                        }
                        break;
                    case 'html':
                    case 'text': // Keep html tags for text fields, they will be escaped at display
                        $param_type = 'html';
                        break;
                    case 'url':
                        $param_type = 'url';
                        $field_value = param($param_name, 'string', null);
                        $url_error = validate_url($field_value, 'http-https');
                        if ($url_error !== false) {
                            param_error($param_name, $url_error);
                            $param_error = true;
                        }
                        break;
                    case 'image':
                        $param_type = 'integer';
                        $field_value = param($param_name, 'string', null);
                        if (! empty($field_value) && ! is_number($field_value)) {
                            param_error($param_name, sprintf(T_('Custom "%s" field must be a number'), $custom_field['label']));
                            $param_error = true;
                        }
                        break;
                    case 'varchar':
                    default:
                        $param_type = 'string';
                        break;
                }
                if (! $param_error) {
                    param($param_name, $param_type, null); // get par value
                }
                if ($custom_field['required'] && ($custom_field['public'] || is_admin_page())) {	// Check required field only when it is public:
                    param_check_not_empty($param_name, sprintf(T_('Custom "%s" cannot be empty.'), $custom_field['label']));
                }
                $custom_field_make_null = $custom_field['type'] != 'double'; // store '0' values in DB for numeric fields

                $custom_field_value = $this->get_custom_field_value($custom_field['name']);
                if ($custom_field_value !== null && $custom_field_value !== false) {	// Store previous value in order to save this in archived version:
                    $this->dbchanges_custom_fields[$custom_field['name']] = $custom_field_value;
                    // Flag to know custom fields were changed:
                    $this->dbchanges_flags['custom_fields'] = true;
                }
                $this->set_custom_field($custom_field['name'], get_param($param_name), 'value', $custom_field_make_null);
                if (! $custom_fields_changed && $custom_field_value != get_param($param_name)) {	// Mark that at least one custom field was changed:
                    $custom_fields_changed = true;
                }
            }
        }
        foreach ($custom_fields as $custom_field) {	// Update computed custom fields after when all fields we updated above:
            if ($custom_field['type'] == 'computed') {	// Set a value by special function because we don't submit value for such fields and compute a value by formula automatically:
                $this->set_custom_field($custom_field['name'], $this->get_custom_field_computed($custom_field['name']));
            }
        }

        // Return TRUE when no errors and ata least one custom field has been changed:
        return ! param_errors_detected() && $custom_fields_changed;
    }

    /**
     * Link attachments from temporary object to new created Item
     */
    public function link_from_Request()
    {
        global $DB;

        if ($this->ID == 0) {	// The item must be stored in DB:
            return;
        }

        $temp_link_owner_ID = param('temp_link_owner_ID', 'integer', 0);

        $TemporaryIDCache = &get_TemporaryIDCache();
        if (! ($TemporaryID = &$TemporaryIDCache->get_by_ID($temp_link_owner_ID, false, false))) {	// No temporary object of attachments:
            return;
        }

        if ($TemporaryID->type != 'item') {	// Wrong temporary object:
            return;
        }

        // Load all links:
        $LinkOwner = new LinkItem(new Item(), $TemporaryID->ID);
        $LinkOwner->load_Links();

        if (empty($LinkOwner->Links)) {	// No links:
            return;
        }

        // Change link owner from temporary to message object:
        $DB->query('UPDATE T_links
			  SET link_itm_ID = ' . $this->ID . ',
			      link_tmp_ID = NULL
			WHERE link_tmp_ID = ' . $TemporaryID->ID);

        $item_slug_folder_name = 'quick-uploads/' . $this->get('urltitle') . '/';

        // Move all temporary files to folder of new created message:
        foreach ($LinkOwner->Links as $item_Link) {
            if ($item_File = &$item_Link->get_File() &&
                $item_FileRoot = &$item_File->get_FileRoot() &&
                $item_Link->is_single_linked_file()) {	// If file is not linked to any other object:
                if (! file_exists($item_FileRoot->ads_path . $item_slug_folder_name)) {	// Create if folder doesn't exist for files of new created message:
                    mkdir_r($item_FileRoot->ads_path . $item_slug_folder_name);
                }
                $item_File->move_to($item_FileRoot->type, $item_FileRoot->in_type_ID, $item_slug_folder_name . $item_File->get_name(), true);
            }
        }

        if ($item_FileRoot && file_exists($item_FileRoot->ads_path . 'quick-uploads/tmp' . $TemporaryID->ID . '/')) {	// Remove temp folder from disk completely:
            rmdir_r($item_FileRoot->ads_path . 'quick-uploads/tmp' . $TemporaryID->ID . '/');
        }

        // Delete temporary object from DB:
        $TemporaryID->dbdelete();
    }

    /**
     * Template function: display anchor for permalinks to refer to.
     */
    public function anchor()
    {
        global $Settings;

        echo '<a id="' . $this->get_anchor_id() . '"></a>';
    }

    /**
     * @return string
     */
    public function get_anchor_id()
    {
        // In case you have old cafelog permalinks, uncomment the following line:
        // return preg_replace( '/[^a-zA-Z0-9_\.-]/', '_', $this->title );

        return 'item_' . $this->ID;
    }

    /**
     * Template tag
     */
    public function anchor_id()
    {
        echo $this->get_anchor_id();
    }

    /**
     * Template function: display assignee of item
     *
     * @param string
     * @param string
     * @param string Output format, see {@link format_to_output()}
     */
    public function assigned_to($before = '', $after = '', $format = 'htmlbody')
    {
        if ($this->get_assigned_User()) {
            echo $before;
            echo $this->assigned_User->get_identity_link([
                'format' => $format,
                'link_text' => 'name',
            ]);
            echo $after;
        }
    }

    /**
     * Template function: display assignee of item with configurable params
     *
     * @params array
     */
    public function assigned_to2($params = [])
    {
        if ($this->get_assigned_User()) {
            $params = array_merge([
                'before' => '',
                'after' => '',
                'format' => 'htmlbody',
                'link_text' => 'only_avatar',
            ], $params);
            echo $params['before'] . $this->assigned_User->get_identity_link($params) . $params['after'];
        }
    }

    /**
     * Get list of assigned user options
     *
     * @uses UserCache::get_blog_member_option_list()
     * @return string HTML select options list
     */
    public function get_assigned_user_options()
    {
        $UserCache = &get_UserCache();
        $UserCache->clear();
        return $UserCache->get_blog_member_option_list(
            $this->get_blog_ID(),
            $this->assigned_user_ID,
            true,
            ($this->ID != 0) /* if this Item is already serialized we'll load the default anyway */
        );
    }

    /**
     * Check if user can see comments on this post, which he cannot if they
     * are disabled for the Item or never allowed for the blog.
     *
     * @param boolean true will display why user can't see comments
     * @return boolean
     */
    public function can_see_comments($display = false)
    {
        global $Settings, $disp;

        if ($disp == 'terms') {	// Don't display the comments on page with terms & conditions:
            return false;
        }

        if (! $this->get_type_setting('use_comments')) { // Comments are not allowed on this post by post type
            return false;
        }

        if ($this->get_type_setting('allow_disabling_comments') && ($this->comment_status == 'disabled')) { // Comments are disabled on this post
            return false;
        }

        if ($this->check_blog_settings('allow_view_comments')) { // User is allowed to see comments
            return true;
        }

        if (! $display) {
            return false;
        }

        $this->load_Blog();
        $number_of_comments = $this->get_number_of_comments('published');
        $allow_view_comments = $this->Blog->get_setting('allow_view_comments');
        $user_can_be_validated = check_user_status('can_be_validated');

        if (($allow_view_comments != 'any') && ($user_can_be_validated)) { // change allow view comments to activated, because user is logged in but the account is not activated, and anomnymous users can't see comments
            $allow_view_comments = 'active_users';
        }

        // Set display text
        switch ($allow_view_comments) {
            case 'active_users':
                // users must activate their accounts before they can see the comments
                if ($number_of_comments == 0) {
                    $display_text = T_('You must activate your account to see the comments.');
                } elseif ($number_of_comments == 1) {
                    $display_text = T_('There is <b>one comment</b> on this post but you must activate your account to see the comments.');
                } else {
                    $display_text = sprintf(T_('There are <b>%s comments</b> on this post but you must activate your account to see the comments.'), $number_of_comments);
                }
                break;

            case 'registered':
                // only registered users can see this post's comments
                if ($number_of_comments == 0) {
                    $display_text = T_('You must be logged in to see the comments.');
                } elseif ($number_of_comments == 1) {
                    $display_text = T_('There is <b>one comment</b> on this post but you must be logged in to see the comments.');
                } else {
                    $display_text = sprintf(T_('There are <b>%s comments</b> on this post but you must be logged in to see the comments.'), $number_of_comments);
                }
                break;

            case 'member':
                // only members can see this post's comments
                if ($number_of_comments == 0) {
                    $display_text = T_('You must be a member of this blog to see the comments.');
                } elseif ($number_of_comments == 1) {
                    $display_text = T_('There is one comment on this post but you must be a member of this blog to see the comments.');
                } else {
                    $display_text = sprintf(T_('There are %s comments on this post but you must be a member of this blog to see the comments.'), $number_of_comments);
                }
                break;

            default:
                // any is already handled, moderators shouldn't get any message
                return false;
        }

        echo '<div class="comment_posting_disabled_msg">';

        if (! is_logged_in()) { // user is not logged in at all
            $redirect_to = $this->get_permanent_url() . '#comments';
            $login_link = '<a href="' . get_login_url('cannot see comments', $redirect_to) . '">' . T_('Log in now!') . '</a>';
            echo '<p>' . $display_text . ' ' . $login_link . '</p>';
            if ($Settings->get('newusers_canregister') == 'yes' && $Settings->get('registration_is_public')) { // needs to display register link
                echo '<p>' . sprintf(
                    T_('If you have no account yet, you can <a href="%s">register now</a>...<br />(It only takes a few seconds!)'),
                    get_user_register_url($redirect_to, 'reg to see comments')
                ) . '</p>';
            }
        } elseif ($user_can_be_validated) { // user is logged in but not activated
            $activateinfo_link = '<a href="' . get_activate_info_url($this->get_permanent_url() . '#comments', '&amp;') . '">' . T_('More info &raquo;') . '</a>';
            echo '<p>' . $display_text . ' ' . $activateinfo_link . '</p>';
        } else { // user is activated, but not allowed to view comments
            echo $display_text;
        }

        echo '</div>';

        return false;
    }

    /**
     * Template function: Check if user can leave comment on this post or display error
     *
     * @param string|null string to display before any error message; NULL to not display anything, but just return boolean
     * @param string string to display after any error message
     * @param string error message for non published posts, '#' for default
     * @param string error message for closed comments posts, '#' for default
     * @param string section title
     * @param array Skin params
     * @return boolean true if user can post, false if s/he cannot
     */
    public function can_comment($before_error = '<p><em>', $after_error = '</em></p>', $non_published_msg = '#', $closed_msg = '#', $section_title = '', $params = [], $comment_type = 'comment')
    {
        global $disp;

        if ($comment_type == 'meta' && $this->can_meta_comment()) {	// Meta comment are always allowed!
            return true;
        }

        if ($disp == 'terms') {	// Don't allow comment a page with terms & conditions:
            return false;
        }

        $display = (! is_null($before_error));

        if ($display) { // display a comment form section even if comment form won't be displayed, "add new comment" links should point to this section
            $comment_form_anchor = empty($params['comment_form_anchor']) ? 'form_p' : $params['comment_form_anchor'];
            echo '<a id="' . format_to_output($comment_form_anchor . $this->ID, 'htmlattr') . '"></a>';
        }

        if (! $this->get_type_setting('use_comments')) { // Comments are not allowed on this post by post type
            return false;
        }

        if ($this->check_blog_settings('allow_comments')) {
            if ($this->get_type_setting('allow_disabling_comments') && ($this->comment_status == 'disabled')) { // Comments are disabled on this post
                return false;
            }

            if ($this->comment_status == 'closed' || $this->is_locked()) { // Comments are closed on this post
                if ($display) {
                    if ($closed_msg == '#') {
                        $closed_msg = T_('Comments are closed for this post.');
                    }

                    echo $before_error;
                    echo $closed_msg;
                    echo $after_error;
                }

                return false;
            }

            if (($this->status == 'draft') || ($this->status == 'deprecated') || ($this->status == 'redirected')) { // Post is not published
                if ($display) {
                    if ($non_published_msg == '#') {
                        $non_published_msg = T_('This post is not published. You cannot leave comments.');
                    }

                    echo $before_error;
                    echo $non_published_msg;
                    echo $after_error;
                }

                return false;
            }

            if (is_logged_in() && ($this->Blog->get('advanced_perms')) && ! check_user_perm('blog_comment_statuses', 'create', false, $this->Blog->ID)) { // User doesn't have permission to create comments and advanced perms are enabled
                if ($display) {
                    echo $before_error;
                    echo T_('You don\'t have permission to reply on this post.');
                    echo $after_error;
                }
                return false;
            }
            return true; // OK, user can comment!
        }

        if (($this->Blog->get_setting('allow_comments') != 'never') && $display) {
            if ($this->comment_status == 'closed' || $this->comment_status == 'disabled') {	// Don't display the disabled comment form because we cannot create the comments for this post
                return false;
            }
            echo $section_title;
            // set item_url for redirect after login, if login required
            $item_url = $this->get_permanent_url() . '#form_p' . $this->ID;
            // display disabled comment form
            echo_disabled_comments($this->Blog->get_setting('allow_comments'), $item_url, $params);
        }

        // Current user not allowed to comment in this blog
        return false;
    }

    /**
     * Check if current User can see internal comments on this Item
     *
     * @return boolean
     */
    public function can_see_meta_comments()
    {
        if (! is_logged_in()) {	// User must be logged in
            return false;
        }

        if (! is_admin_page()) {	// Check visibility of internal comments on front-office:
            $item_Blog = &$this->get_Blog();
            if (! $item_Blog || ! $item_Blog->get_setting('meta_comments_frontoffice')) {	// Internal comments are disabled to be displayed on front-office for this Item's collection:
                return false;
            }
        }

        return check_user_perm('meta_comment', 'view', false, $this->get_blog_ID());
    }

    /**
     * Check if current User can leave internal comment on this Item
     *
     * @return boolean
     */
    public function can_meta_comment()
    {
        return check_user_perm('meta_comment', 'add', false, $this->get_blog_ID());
    }

    /**
     * Check if current user is allowed for several action in this post's blog
     *
     * @private function
     *
     * @param string blog settings name. Param value can be 'allow_comments', 'allow_attachments','allow_rating_items'
     * @return boolean  true if user is allowed for the corresponding action
     */
    public function check_blog_settings($settings_name, $settings_object = null)
    {
        $this->load_Blog();

        if (($settings_name == 'allow_attachments')
                && isset($settings_object)
                && ($settings_object instanceof Comment)
                && $settings_object->is_meta()
                && $this->can_meta_comment()) {	// Always allow attachments for meta Comments:
            return true;
        }

        switch ($this->Blog->get_setting($settings_name)) {
            case 'never':
                return false;
            case 'any':
                return true;
            case 'registered':
                return is_logged_in(false);
            case 'member':
                return check_user_perm('blog_ismember', 'view', false, $this->get_blog_ID(), false);
            case 'moderator':
                return check_user_perm('blog_comments', 'edit', false, $this->get_blog_ID(), false);
            default:
                debug_die('Invalid blog ' . $settings_name . ' settings!');
        }

        return false;
    }

    /**
     * Template function: Check if user can attach files to this post comments
     *
     * @param boolean|integer ID of Temporary object to count also temporary attached files to new creating comment,
     *                        FALSE to count ONLY attachments of the created comments
     * @param string Comment type
     * @return boolean true if user can attach files to this post comments, false if s/he cannot
     */
    public function can_attach($link_tmp_ID = false, $comment_type = 'comment')
    {
        global $Settings;

        $attachments_quota_is_full = false;
        if (is_logged_in()) {	// We can check the attachments quota only for registered users
            $this->load_Blog();

            if ($comment_type == 'meta' && $this->can_meta_comment()) {	// Always allow attachments for meta Comments:
                return true;
            }

            $max_attachments = (int) $this->Blog->get_setting('max_attachments');
            if ($max_attachments > 0) {	// Check attachments quota only when Blog setting "Max # of attachments" is defined
                global $DB, $Session;

                // Get a number of attachments for current user on this post
                $link_tmp_ID = false;
                $attachments_count = $this->get_attachments_number(null, $link_tmp_ID);

                // Get the attachments from preview comment
                global $checked_attachments;
                if (! empty($checked_attachments)) {	// Calculate also the attachments in the PREVIEW mode
                    $attachments_count += count(explode(',', $checked_attachments));
                }

                if ($attachments_count >= $max_attachments) {	// Current user already has max number of attachments on this post
                    $attachments_quota_is_full = true;
                }
            }
        }

        return ! $attachments_quota_is_full && $this->check_blog_settings('allow_attachments') && $Settings->get('upload_enabled');
    }

    /**
     * Check if the post contains inline file placeholders without corresponding attachment file.
     * Removes the invalid inline file placeholders from the item content.
     *
     * @param string Content
     * @return string Prepared content
     */
    public function check_and_clear_inline_files($content)
    {
        preg_match_all('/\[(image|file|inline|video|audio|thumbnail|folder):(\d+):?[^\]]*\]/i', $content, $inline_images);

        if (empty($inline_images[1])) { // There are no inline image placeholders in the post content
            return $content;
        }

        // There are inline image placeholders in the item's content:
        global $DB;
        $links_SQL = new SQL('Get item links IDs of the inline files');
        $links_SQL->SELECT('link_ID');
        $links_SQL->FROM('T_links');
        if (empty($this->ID)) {	// Preview mode for new creating item:
            $links_SQL->WHERE('link_tmp_ID = ' . $DB->quote(param('temp_link_owner_ID', 'integer', 0)));
        } else {	// Normal mode for existing Item in DB:
            $links_SQL->WHERE('link_itm_ID = ' . $DB->quote($this->ID));
        }
        $inline_links_IDs = $DB->get_col($links_SQL);

        $unused_inline_images = [];
        foreach ($inline_images[2] as $i => $inline_link_ID) {
            if (! in_array($inline_link_ID, $inline_links_IDs)) { // This inline image must be removed from content
                $unused_inline_images[] = $inline_images[0][$i];
            }
        }

        // Clear the unused inline images from content:
        if (count($unused_inline_images)) {	// Remove all unused inline images from the content:
            global $Messages;
            $unused_inline_images = array_unique($unused_inline_images);
            $content = replace_outside_code_tags($unused_inline_images, '', $content, 'replace_content', 'str');
            $Messages->add(T_('Invalid inline file placeholders won\'t be displayed.'), 'note');
        }

        return $content;
    }

    /**
     * Get a number of attachments on this post
     *
     * @param object User
     * @param boolean|integer ID of Temporary object to count also temporary attached files to new creating comment,
     *                        FALSE to count ONLY attachments of the created comments
     * @return integer Number of attachments
     */
    public function get_attachments_number($User = null, $link_tmp_ID = false)
    {
        global $DB, $cache_item_attachments_number;

        if (is_null($User)) {	// Use current user by default
            global $current_User;
            $User = $current_User;
        }

        if (! isset($cache_item_attachments_number)) {	// Init cache variable at first time
            $cache_item_attachments_number = [];
        }

        if (isset($cache_item_attachments_number[$User->ID][$link_tmp_ID])) {	// Get a number of attachments from cache variable:
            return $cache_item_attachments_number[$User->ID][$link_tmp_ID];
        }

        // Get a number of attachments from DB:
        $SQL = new SQL('Get a number of comment attachments per item #' . $this->ID . ' by user #' . $User->ID);
        $SQL->SELECT('COUNT( link_ID )');
        $SQL->FROM('T_links');
        $SQL->FROM_add('LEFT JOIN T_comments ON comment_ID = link_cmt_ID');
        $SQL->WHERE('link_creator_user_ID = ' . $DB->quote($User->ID));
        $sql_where = 'comment_item_ID = ' . $DB->quote($this->ID);
        if ($link_tmp_ID) {	// Count also the attached files to new creating comment:
            $sql_where .= ' OR ( comment_item_ID IS NULL AND link_tmp_ID = ' . $DB->quote($link_tmp_ID) . ' )';
        }
        $SQL->WHERE_and($sql_where);
        // Do not include meta comments in the count:
        $SQL->WHERE_and('comment_type != "meta"');
        $cache_item_attachments_number[$User->ID][$link_tmp_ID] = intval($DB->get_var($SQL->get(), 0, null, $SQL->title));

        return $cache_item_attachments_number[$User->ID][$link_tmp_ID];
    }

    /**
     * Get how much files user can attach on this post yet
     *
     * @param object User
     * @return integer|string Number of files which current user can attach to this post | 'unlimit'
     */
    public function get_attachments_limit($User = null)
    {
        if (is_logged_in()) {	// We can check the attachments quota only for registered users
            $this->load_Blog();
            $max_attachments = (int) $this->Blog->get_setting('max_attachments');
            if ($max_attachments > 0) {	// Get a limit only when Blog setting "Max # of attachments" is defined
                return $max_attachments - $this->get_attachments_number($User);
            }
        }

        return 'unlimit';
    }

    /**
     * Duplicate attachments from another Item
     *
     * @param integer Item ID
     */
    public function duplicate_attachments($item_ID)
    {
        global $DB;

        // Initiliaze Link Owner where we create a temporary object for new creating Item:
        $LinkOwner = new LinkItem($this, param('temp_link_owner_ID', 'integer', 0));

        if (! empty($LinkOwner->link_Object->tmp_ID)) {	// If temporaty object has been created for new Item:
            $DB->query('INSERT INTO T_links ( link_datecreated, link_datemodified, link_creator_user_ID, link_lastedit_user_ID, link_tmp_ID, link_file_ID, link_position, link_order )
				SELECT link_datecreated, link_datemodified, link_creator_user_ID, link_lastedit_user_ID, ' . $DB->quote($LinkOwner->link_Object->tmp_ID) . ', link_file_ID, link_position, link_order
				  FROM T_links
				WHERE link_itm_ID = ' . $DB->quote($item_ID));
        }
    }

    /**
     * Template function: Check if user can rate this post
     *
     * @return boolean true if user can post, false if s/he cannot
     */
    public function can_rate()
    {
        return $this->check_blog_settings('allow_rating_items');
    }

    /**
     * Get the prerendered content. If it has not been generated yet, it will.
     *
     * NOTE: This calls {@link Item::dbupdate()}, if renderers get changed (from Plugin hook).
     *       (not for preview though)
     *
     * @param string Format, see {@link format_to_output()}.
     *        Only "htmlbody", "entityencoded", "xml" and "text" get cached.
     * @return string
     */
    public function get_prerendered_content($format)
    {
        global $Plugins;
        global $preview;

        if ($preview) {
            $this->update_renderers_from_Plugins();
            $post_renderers = $this->get_renderers_validated();

            // Call RENDERER plugins:
            $r = $this->content;
            $Plugins->render($r /* by ref */, $post_renderers, $format, [
                'Item' => $this,
            ], 'Render');

            // Check and clear inline files, to avoid to have placeholders without corresponding attachment
            $r = $this->check_and_clear_inline_files($r);

            if ($this->is_intro() || ! $this->get_type_setting('allow_breaks')) {	// Don't use the content separators for intro items and if it is disabled by item type:
                $r = replace_outside_code_tags(['[teaserbreak]', '[pagebreak]'], '', $r, 'replace_content', 'str');
            }

            return $r;
        }

        $r = null;

        $post_renderers = $this->get_renderers_validated();
        $cache_key = $format . '/' . implode('.', $post_renderers); // logic gets used below, for setting cache, too.

        $use_cache = $this->ID && in_array($format, ['htmlbody', 'entityencoded', 'xml', 'text'])
                && ! $this->is_revision(); // do not use cache when viewing historical revision

        // $use_cache = false;

        if ($use_cache) { // the format/item can be cached:
            $ItemPrerenderingCache = &get_ItemPrerenderingCache();

            if (isset($ItemPrerenderingCache[$format][$this->ID][$cache_key])) { // already in PHP cache.
                $r = $ItemPrerenderingCache[$format][$this->ID][$cache_key];
                // Save memory, typically only accessed once.
                unset($ItemPrerenderingCache[$format][$this->ID][$cache_key]);
            } else {	// Try loading from DB cache, including all items in MainList/ItemList.
                global $DB;

                if (! isset($ItemPrerenderingCache[$format])) { // only do the prefetch loading once.
                    $prefetch_IDs = $this->get_prefetch_itemlist_IDs();

                    // Load prerendered content for all items in MainList/ItemList.
                    // We load the current $format only, since it's most likely that only one gets used.
                    $ItemPrerenderingCache[$format] = [];

                    $rows = $DB->get_results(
                        "
						SELECT itpr_itm_ID, itpr_format, itpr_renderers, itpr_content_prerendered
							FROM T_items__prerendering
						 WHERE itpr_itm_ID IN (" . $DB->quote($prefetch_IDs) . ")
							 AND itpr_format = '" . $format . "'",
                        OBJECT,
                        'Preload prerendered item content for MainList/ItemList (' . $format . ')'
                    );
                    foreach ($rows as $row) {
                        $row_cache_key = $row->itpr_format . '/' . $row->itpr_renderers;

                        if (! isset($ItemPrerenderingCache[$format][$row->itpr_itm_ID])) { // init list
                            $ItemPrerenderingCache[$format][$row->itpr_itm_ID] = [];
                        }

                        $ItemPrerenderingCache[$format][$row->itpr_itm_ID][$row_cache_key] = $row->itpr_content_prerendered;
                    }

                    // Set the value for current Item.
                    if (isset($ItemPrerenderingCache[$format][$this->ID][$cache_key])) {
                        $r = $ItemPrerenderingCache[$format][$this->ID][$cache_key];
                        // Save memory, typically only accessed once.
                        unset($ItemPrerenderingCache[$format][$this->ID][$cache_key]);
                    }
                } else { // This item has not been fetched by the initial prefetch query; only get this item.
                    // dh> This is quite unlikely to happen, but you never know.
                    // This gets not added to ItemPrerenderingCache, since it would only waste
                    // memory - an item gets typically only accessed once per page, and even if
                    // it would get accessed more often, there is a cache higher in the chain
                    // ($this->content_pages).
                    $cache = $DB->get_var("
						SELECT itpr_content_prerendered
							FROM T_items__prerendering
						 WHERE itpr_itm_ID = " . $this->ID . "
							 AND itpr_format = '" . $format . "'
							 AND itpr_renderers = '" . implode('.', $post_renderers) . "'", 0, 0, 'Check prerendered item content');
                    if ($cache !== null) { // may be empty string
                        // Retrieved from cache:
                        // echo ' retrieved from prerendered cache';
                        $r = $cache;
                    }
                }
            }
        }

        if (! isset($r)) { // Not cached yet:
            global $Debuglog;

            if ($this->update_renderers_from_Plugins()) {
                $post_renderers = $this->get_renderers_validated(); // might have changed from call above
                $cache_key = $format . '/' . implode('.', $post_renderers);

                // Save new renderers with item:
                $this->dbupdate();
            }

            // Call RENDERER plugins:
            $r = $this->get('content');
            $Plugins->render($r /* by ref */, $post_renderers, $format, [
                'Item' => $this,
            ], 'Render');

            // Check and clear inline files, to avoid to have placeholders without corresponding attachment
            $r = $this->check_and_clear_inline_files($r);

            if ($this->is_intro() || ! $this->get_type_setting('allow_breaks')) {	// Don't use the content separators for intro items and if it is disabled by item type:
                $r = replace_outside_code_tags(['[teaserbreak]', '[pagebreak]'], '', $r, 'replace_content', 'str');
            }

            $Debuglog->add('Generated pre-rendered content [' . $cache_key . '] for item #' . $this->ID, 'items');

            if ($use_cache) { // save into DB (using REPLACE INTO because it may have been pre-rendered by another thread since the SELECT above)
                global $servertimenow;
                $DB->query('REPLACE INTO T_items__prerendering ( itpr_itm_ID, itpr_format, itpr_renderers, itpr_content_prerendered, itpr_datemodified )
					VALUES ( ' . $this->ID . ', ' . $DB->quote($format) . ', ' . $DB->quote(implode('.', $post_renderers)) . ', ' . $DB->quote($r) . ', ' . $DB->quote(date2mysql($servertimenow)) . ' )', 'Cache prerendered item content');
            }
        }

        return $r;
    }

    /**
     * Unset any prerendered content for this item (in PHP cache).
     */
    public function delete_prerendered_content()
    {
        global $DB;

        // Delete DB rows.
        $DB->query('DELETE FROM T_items__prerendering WHERE itpr_itm_ID = ' . $this->ID);

        // Delete cache.
        $ItemPrerenderingCache = &get_ItemPrerenderingCache();
        foreach (array_keys($ItemPrerenderingCache) as $format) {
            unset($ItemPrerenderingCache[$format][$this->ID]);
        }

        // Delete derived properties.
        unset($this->content_pages);
    }

    /**
     * Trigger {@link Plugin::ItemApplyAsRenderer()} event and adjust renderers according
     * to return value.
     * @return boolean True if renderers got changed.
     */
    public function update_renderers_from_Plugins()
    {
        global $Plugins;

        $r = false;

        if (! isset($Plugins)) {	// This can happen in maintenance modules running with minimal init, during install, or in tests.
            return $r;
        }

        foreach ($Plugins->get_list_by_event('ItemApplyAsRenderer') as $Plugin) {
            if (empty($Plugin->code)) {
                continue;
            }

            $tmp_params = [
                'Item' => &$this,
            ];
            $plugin_r = $Plugin->ItemApplyAsRenderer($tmp_params);

            if (is_bool($plugin_r)) {
                if ($plugin_r) {
                    $r = $this->add_renderer($Plugin->code) || $r;
                } else {
                    $r = $this->remove_renderer($Plugin->code) || $r;
                }
            }
        }

        return $r;
    }

    /**
     * Display excerpt of an item.
     * @param array Associative list of params
     *   - before
     *   - after
     *   - excerpt_before_more
     *   - excerpt_after_more
     *   - excerpt_more_text
     *   - format
     */
    public function excerpt($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '<div class="excerpt">',
            'after' => '</div>',
            'excerpt_before_more' => ' <span class="excerpt_more">',
            'excerpt_after_more' => '</span>',
            'excerpt_more_text' => '#more+arrow', 	// possible special values: ...
            'excerpt_more_class' => 'nowrap',
            'format' => 'htmlbody',
        ], $params);

        $r = $this->get_excerpt($params['format']);

        if (! empty($r)) {
            echo $params['before'];

            if (isset($params['max_words'])) {
                // to stop displaying double hellip
                $params['avoid_end_hellip'] = true;

                echo excerpt_words($r, $params['max_words'], $params);
            } else {
                echo $r;
            }

            if (! isset($params['excerpt_no_more_link'])) {
                $this->permanent_link([
                    'before' => $params['excerpt_before_more'],
                    'after' => $params['excerpt_after_more'],
                    'text' => $params['excerpt_more_text'],
                    'title' => '#',
                    'class' => $params['excerpt_more_class'],
                ]);
            }

            echo $params['after'];
        }
    }

    /**
     * Template tag: get excerpt 2 (Full version)
     * This full version may auto-generate an excerpt if it is found to be empty.
     *
     * @deprecated Use $this->get_excerpt() instead.
     *
     * @param array DEPRECATED: Associative list of params
     *   - allow_empty: DEPRECATED force generation if excert is empty (Default: false)
     *   - update_db: DEPRECATED update the DB if we generated an excerpt (Default: true)
     * @return string
     */
    public function get_excerpt2($params = [])
    {
        return $this->get_excerpt();
    }

    /**
     * Make sure, the pages have been obtained (and split up_ from prerendered cache.
     *
     * @param string Format, used to retrieve the matching cache; see {@link format_to_output()}
     */
    public function split_pages($format = 'htmlbody')
    {
        if (! isset($this->content_pages[$format])) {
            // SPLIT PAGES:
            $this->content_pages[$format] = split_outcode('[pagebreak]', $this->get_prerendered_content($format));

            // Balance HTML tags
            $this->content_pages[$format] = array_map('balance_tags', $this->content_pages[$format]);

            $this->pages = count($this->content_pages[$format]);
        }
    }

    /**
     * Get a specific page to display (from the prerendered cache)
     *
     * @param integer Page number, NULL/"#" for current
     * @param string Format, used to retrieve the matching cache; see {@link format_to_output()}
     */
    public function get_content_page($page = null, $format = 'htmlbody')
    {
        global $preview;

        // Get requested content page:
        if (! isset($page) || $page === '#') { // We want to display the page requested by the user:
            $page = isset($GLOBALS['page']) ? $GLOBALS['page'] : 1;
        }

        // Make sure, the pages are split up:
        $this->split_pages($format);

        $content_page = '';

        if ($preview && $this->pages > 1 && ! $this->ID) { // This is a preview of an unsaved  multipage item
            foreach ($this->content_pages[$format] as $page => $page_content) {
                if ($page !== 0) {
                    $content_page .= '<span class="badge badge-info">Page ' . ($page + 1) . '</span>';
                }

                $content_page .= $page_content;
            }
        } else {
            if ($page < 1) {
                $page = 1;
            }

            if ($page > $this->pages) {
                $page = $this->pages;
            }

            $content_page = $this->content_pages[$format][$page - 1];
        }

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this)) {	// Clean up rendering errors from content if current User has no permission to edit this Item:
            $content_page = clear_rendering_errors($content_page);
        }

        return $content_page;
    }

    /**
     * Display content teaser of item (will stop at "[teaserbreak]"
     */
    public function content_teaser($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'disppage' => '#',
            'stripteaser' => '#',
            'format' => 'htmlbody',
        ], $params);

        $r = $this->get_content_teaser($params['disppage'], $params['stripteaser'], $params['format'], $params);

        if (! empty($r)) {
            echo $params['before'];
            echo $r;
            echo $params['after'];
        }
    }

    /**
     * Template function: get content teaser of item (will stop at "[teaserbreak]")
     *
     * @param mixed page number to display specific page, # for url parameter
     * @param boolean # if you don't want to repeat teaser after more link was pressed and <-- noteaser --> has been found
     * @param string filename to use to display more
     * @param array Params
     * @return string
     */
    public function get_content_teaser($disppage = '#', $stripteaser = '#', $format = 'htmlbody', $params = [])
    {
        global $more;

        $params = array_merge($params, [
            'disppage' => $disppage,
            'dispmore' => ($more != 0),
            'format' => $format,
        ]);

        $params['view_type'] = 'full';
        if ($this->has_content_parts($params)) { // This is an extended post (has a more section):
            if ($stripteaser === '#') {
                // If we're in "more" mode and we want to strip the teaser, we'll strip:
                $stripteaser = ($more && $this->get_setting('hide_teaser'));
            }

            if ($stripteaser) {
                return null;
            }
            $params['view_type'] = 'teaser';
        }

        $content_parts = $this->get_content_parts($params);
        $output = array_shift($content_parts);

        // Render content by plugins and inline short tags at display time:
        $output = $this->get_rendered_content($output, $params);

        return $output;
    }

    /**
     * Get rendered content by plugins and inline short tags at display time
     *
     * @param string Source content
     * @param array Params
     * @return string Rendered content
     */
    public function get_rendered_content($content, $params = [])
    {
        global $Plugins, $preview;

        $params = array_merge([
            'format' => 'htmlbody',
            'dispmore' => false,
            'view_type' => 'full',
        ], $params);

        // Render all inline tags to HTML code:
        $output = $this->render_inline_tags($content, $params);

        // Render switchable content:
        $output = $this->render_switchable_content($output);

        // Trigger Display plugins FOR THE STUFF THAT WOULD NOT BE PRERENDERED:
        $output = $Plugins->render($output, $this->get_renderers_validated(), $params['format'], [
            'Item' => $this,
            'preview' => $preview,
            'dispmore' => $params['dispmore'],
            'view_type' => $params['view_type'],
        ], 'Display');

        // Character conversions:
        if (stristr($output, '<script') !== false) {	// Format content on everything outside <script>:
            // E.g.: to avoid replacing of condition operator from & to &amp;
            $output = callback_on_non_matching_blocks(
                $output,
                '~<(script)[^>]*>.*?</\1>~is',
                'format_to_output',
                [$params['format']]
            );
        }

        return $output;
    }

    /**
     * Get content parts (split by "[teaserbreak]").
     *
     * @param array 'disppage', 'format'
     * @return array Array of content parts
     */
    public function get_content_parts($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'disppage' => '#',
            'format' => 'htmlbody',
        ], $params);

        $content_page = $this->get_content_page($params['disppage'], $params['format']); // cannot include format_to_output() because of the magic below.. eg '[teaserbreak]' will get stripped in "xml"

        $content_parts = split_outcode('[teaserbreak]', $content_page);

        // Balance HTML tags
        $content_parts = array_map('balance_tags', $content_parts);

        return $content_parts;
    }

    /**
     * Get full content with teaser and extension and all pages
     *
     * @param string Format
     * @param array Params
     * @return string Content
     */
    public function get_full_content($format = 'htmlbody', $params = [])
    {
        $params = array_merge($params, [
            'dispmore' => true,
            'view_type' => 'full',
            'format' => $format,
        ]);

        $output = '';
        $this->split_pages($format);
        foreach ($this->content_pages[$format] as $p => $content_page) {
            $content_parts = $this->get_content_parts(array_merge($params, [
                'disppage' => $p + 1,
            ]));

            $output .= implode("\n\n", $content_parts);
        }

        // Render content by plugins and inline short tags at display time:
        $output = $this->get_rendered_content($output, $params);

        return $output;
    }

    /**
     * DEPRECATED
     */
    public function content()
    {
        // ---------------------- POST CONTENT INCLUDED HERE ----------------------
        skin_include('_item_content.inc.php', [
            'image_size' => 'fit-400x320',
        ]);
        // Note: You can customize the default item feedback by copying the generic
        // /skins/_item_feedback.inc.php file into the current skin folder.
        // -------------------------- END OF POST CONTENT -------------------------
    }

    /**
     * Display content extension of item (part after "[teaserbreak]")
     */
    public function content_extension($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'disppage' => '#',
            'format' => 'htmlbody',
            'force_more' => false,
            'image_size' => 'fit-400x320',
        ], $params);

        $r = $this->get_content_extension($params['disppage'], $params['force_more'], $params['format'], $params);

        if (! empty($r)) {
            echo $params['before'];
            echo $r;
            echo $params['after'];
        }
    }

    /**
     * Template function: get content extension of item (part after "[teaserbreak]")
     *
     * @param mixed page number to display specific page, # for url parameter
     * @param boolean
     * @param string filename to use to display more
     * @param array additional params passthrough
     * @return string
     */
    public function get_content_extension($disppage = '#', $force_more = false, $format = 'htmlbody', $params = [])
    {
        global $more;

        if (! $more && ! $force_more) {	// NOT in more mode:
            return null;
        }

        // Set default params
        $params = array_merge([
            'image_size' => 'fit-400x320',
        ], $params);

        // Don't rewrite these params from array $params, Use them from separate params of this function
        $params = array_merge($params, [
            'disppage' => $disppage,
            'dispmore' => true,
            'view_type' => 'extension',
            'format' => $format,
        ]);

        if (! $this->has_content_parts($params)) { // This is NOT an extended post
            return null;
        }

        $content_parts = $this->get_content_parts($params);

        // Output everything after [teaserbreak]:
        array_shift($content_parts);
        $output = implode('', $content_parts);

        // Render content by plugins and inline short tags at display time:
        $output = $this->get_rendered_content($output, $params);

        return $output;
    }

    /**
     * Increase view counter
     *
     * @deprecated since 5.1.0-beta
     */
    public function count_view($params = [])
    {
        // We always return false, since counting views feature was removed.
        return false;
    }

    /**
     * Get all custom fields definitions of this Item with once loading them into cache array $this->custom_fields
     *
     * @return array Custom fields, each array item is array with keys: ID, ityp_ID, label, name, type, order, note, value.
     */
    public function get_custom_fields_defs()
    {
        if (! isset($this->custom_fields) ||
            ! isset($this->custom_fields_loaded_ityp_ID) ||
            $this->custom_fields_loaded_ityp_ID != $this->get('ityp_ID')) {	// Load item custom fields only once if Item Type was not changed:
            global $DB;

            $SQL = new SQL('Load all custom fields definitions of Item Type #' . $this->get('ityp_ID') . ' with values for Item #' . $this->ID);
            $SQL->SELECT('T_items__type_custom_field.*');
            $SQL->FROM('T_items__type_custom_field');
            if ($DB->get_var('SHOW TABLES LIKE "T_items__item_custom_field"') !== null) {	// New version:
                $SQL->SELECT_add(', icfv_value, IFNULL( icfv_parent_sync, 1 ) AS icfv_parent_sync');
                $SQL->FROM_add('LEFT JOIN T_items__item_custom_field ON itcf_name = icfv_itcf_name AND icfv_item_ID = ' . $this->ID);
            } else {	// Old version < 15280, used on upgrade blocks by function Item->insert():
                $SQL->SELECT_add(', iset_value');
                $SQL->FROM_add('LEFT JOIN T_items__item_settings ON iset_name = CONCAT( "custom:", itcf_name ) AND iset_item_ID = ' . $this->ID);
            }
            $SQL->WHERE_and('itcf_ityp_ID = ' . $DB->quote($this->get('ityp_ID')));
            $SQL->ORDER_BY('itcf_order, itcf_ID');
            $custom_fields = $DB->get_results($SQL, ARRAY_A);

            $this->custom_fields = [];
            foreach ($custom_fields as $custom_field) {	// Use field name/code as key/index of array:
                $this->custom_fields[$custom_field['itcf_name']] = [];
                foreach ($custom_field as $custom_field_key => $custom_field_value) {
                    $this->custom_fields[$custom_field['itcf_name']][substr($custom_field_key, 5)] = $custom_field_value;
                }
            }
            // Store current Item Type in order to reload the custom fields when Item Type was changed:
            $this->custom_fields_loaded_ityp_ID = $this->get('ityp_ID');
        }

        return $this->custom_fields;
    }

    /**
     * Set item custom field value or parent_sync
     *
     * @param string Field name
     * @param string New value
     * @param string Value key: 'value', 'parent_sync'
     * @param boolean TRUE to set to NULL if empty value
     */
    public function set_custom_field($field_name, $new_value, $value_key = 'value', $make_null = true)
    {
        if ($value_key != 'value' && $value_key != 'parent_sync') {	// Skip unknown column in the table T_items__type_custom_field:
            return;
        }

        // Load all custom fields for this item:
        $this->get_custom_fields_defs();

        if (! isset($this->custom_fields[$field_name])) {	// Set new array for custom field data, Used for new creating Item:
            $this->custom_fields[$field_name] = [];
        }

        if ($value_key == 'value' && $make_null && empty($new_value)) {	// Set NULL for empty value:
            $new_value = null;
        }

        // Set new value for the field:
        $this->custom_fields[$field_name][$value_key] = $new_value;
    }

    /**
     * Update custom fields
     *
     * @return boolean TRUE if custom fields were updated
     */
    public function update_custom_fields()
    {
        global $DB;

        if (empty($this->ID)) {	// Item must be stored in DB
            return false;
        }

        if ($DB->get_var('SHOW TABLES LIKE "T_items__item_custom_field"') === null) {	// Skip because T_items__item_custom_field doesn't exist in DB on old versions < 15280:
            return false;
        }

        // Get all custom fields:
        $custom_fields = $this->get_custom_fields_defs();

        // Remove old values from DB:
        $deleted_cf_num = $DB->query(
            'DELETE FROM T_items__item_custom_field
			WHERE icfv_item_ID = ' . $this->ID,
            'Delete old custom field values before insert new values for Item #' . $this->ID
        );

        if (empty($custom_fields)) {	// No new custom fields to update:
            return ($deleted_cf_num > 0);
        }

        // Insert new values:
        $cf_insert_data = [];
        foreach ($custom_fields as $custom_field_name => $custom_field) {
            $cf_insert_data[] = '( ' . $this->ID . ', '
                . $DB->quote($custom_field_name) . ', '
                . $DB->quote($custom_field['value']) . ', '
                . $DB->quote(isset($custom_field['parent_sync']) && $custom_field['parent_sync'] !== null ? $custom_field['parent_sync'] : 1) . ' )';
        }
        $inserted_cf_num = $DB->query(
            'INSERT INTO T_items__item_custom_field ( icfv_item_ID, icfv_itcf_name, icfv_value, icfv_parent_sync )
			VALUES ' . implode(', ', $cf_insert_data),
            'Insert new custom field values for Item #' . $this->ID
        );

        return ($deleted_cf_num > 0 || $inserted_cf_num > 0);
    }

    /**
     * Get item custom field label/title by field name
     *
     * @param string Field name, see {@link get_custom_fields_defs()}
     * @return string|boolean FALSE if the field doesn't exist
     */
    public function get_custom_field_title($field_name)
    {
        // Get all custom fields by item ID:
        $custom_fields = $this->get_custom_fields_defs();

        if (! isset($custom_fields[$field_name])) {	// The requested field is not detected:
            return false;
        }

        return $custom_fields[$field_name]['label'];
    }

    /**
     * Get item custom field value by field name
     *
     * @param string Field name, see {@link load_custom_field_value()}
     * @param string Restring field by type, FALSE - to don't restrict
     * @return mixed false if the field doesn't exist Double/String otherwise depending from the custom field type
     */
    public function get_custom_field_value($field_name, $restrict_type = false)
    {
        // Get all custom fields by item ID:
        $custom_fields = $this->get_custom_fields_defs();

        if (! isset($custom_fields[$field_name])) {	// The requested field is not detected:
            return false;
        }

        if ($restrict_type !== false && $custom_fields[$field_name]['type'] != $restrict_type) {	// The requested field is detected but it has another type:
            return false;
        }

        // Get custom item field value:
        if ($this->is_revision()) {	// from current revision if it is active for this Item:
            return $this->get_revision_custom_field_value($field_name);
        } else {	// from the item setting:
            return $custom_fields[$field_name]['value'];
        }
    }

    /**
     * Get formatted item custom field value by field name
     *
     * @param string Field name, see {@link get_custom_fields_defs()}
     * @param array Params
     * @return string|boolean FALSE if the field doesn't exist
     */
    public function get_custom_field_formatted($field_name, $params = [])
    {
        $params = array_merge([
            'field_value_format' => '', // Format for custom field, Leave empty to use a format from DB
            'field_restrict_type' => false, // Restrict field by type(double, varchar, html, text, url, image, computed, separator), FALSE - to don't restrict
            'expansion' => 'default', // 'default': || = '<br />', | | = space; 'vertical': both = '<br />'; 'horizontal': both = space.
        ], $params);

        // Try to get an original value of the requested custom field:
        $custom_field_value = $this->get_custom_field_value($field_name, $params['field_restrict_type']);

        if ($custom_field_value === false) {	// The requested field is not found for the item type:
            return false;
        }

        $orig_custom_field_value = $custom_field_value;

        // Get custom field:
        $custom_fields = $this->get_custom_fields_defs();
        $custom_field = $custom_fields[$field_name];

        if (($custom_field_value === '' || $custom_field_value === null) && // don't format empty value
            ! in_array($custom_field['type'], ['double', 'computed', 'url'])) { // double, computed and url fields may have a special format even for empty value
            // Don't format value in such cases:
            return $custom_field_value;
        }

        if ($params['field_value_format'] === '') {	// Use a format from DB:
            $format = $custom_field['format'];
        } else {	// Use a format from params:
            $format = $params['field_value_format'];
        }

        switch ($custom_field['type']) {
            case 'double':
            case 'computed':
                // Format double/computed field value:
                if ($format === null || $format === '') {	// No format:
                    break;
                }

                $formats = explode(';', $format);

                if (count($formats) > 4) {	// Check formats like 123=text:
                    for ($f = 4; $f < count($formats); $f++) {
                        if (strpos($formats[$f], '=') !== false) {	// If format contains the equal sign
                            $cur_format = explode('=', $formats[$f], 2);
                            if ($cur_format[0] == $custom_field_value) {	// Use the searched format for given value:
                                $custom_field_value = isset($cur_format[1]) ? $cur_format[1] : $custom_field_value;
                                // Stop here to don't apply other format:
                                break 2;
                            }
                        }
                    }
                }

                if ($custom_field_value === '' || $custom_field_value === null) {	// If value is empty string or NULL
                    if (empty($formats[3])) {	// Use default for empty values:
                        $custom_field_value = /* TRANS: "Not Available" */ '{' . T_('N/A') . '}';
                    } else {	// Use a special format for empty values:
                        $custom_field_value = $formats[3];
                    }
                    // Stop here to don't apply other format:
                    break;
                }

                if ($custom_field_value == 0 && isset($formats[2])) {	// If value == 0
                    $custom_field_value = $formats[2];
                    // Stop here to don't apply other format:
                    break;
                }

                // Format all other values which are not related to the formats above:
                $format = $formats[0];
                if ($custom_field_value < 0 && ! empty($formats[1])) {	// Use a format for negative values:
                    $custom_field_value = abs($custom_field_value);
                    $format = $formats[1];
                }

                if (in_array($format, ['#yes#', '(yes)', '#no#', '(no)', '(+)', '(-)', '(!)', '||', '| |']) ||
                    strpos($format, '#stars') !== false ||
                    ($format !== '' && ! preg_match('/\d/', $format))) {	// Use special formats:
                    $custom_field_value = $format;
                    break;
                }

                // Format number:
                if (preg_match('#^(.+?)\[\.([a-z0-9\-_\.]+)\]$#i', $format, $format_class)) {	// Format has a class:
                    $format = $format_class[1];
                    $format_class = str_replace('.', ' ', $format_class[2]);
                } else {	// No class for the format:
                    $format_class = '';
                }
                $format = preg_split('#(\d+)#', $format, -1, PREG_SPLIT_DELIM_CAPTURE);
                $f_num = count($format);
                $format_decimals = 0;
                $format_dec_point = '.';
                $format_thousands_sep = '';
                $format_prefix = isset($format[0]) ? $format[0] : '';
                $format_suffix = $f_num > 1 ? $format[$f_num - 1] : '';
                if ($f_num > 2) {	// Extract data for number fomatting:
                    if (in_array($format_suffix, ['.', ',']) &&
                        isset($format[2]) &&
                        in_array($format[2], ['.', ','])) {	// If last char is decimal char (dot or comma) then this format has no decimal part:
                        $format_suffix = '';
                        $format_decimals = 0;
                        $thousands_sep_pos = 3;
                    } elseif (in_array($format[$f_num - 3], ['.', ','])) {	// Allow only chars '.' and ',' as decimal separator:
                        if ($f_num > 3 && preg_match('#^\d+$#', $format[$f_num - 2])) {	// Get a number of digits after dot:
                            $format_decimals = strlen($format[$f_num - 2]);
                        }
                        if ($f_num > 4 && preg_match('#^[^\d]+$#', $format[$f_num - 3])) {	// Get a decimal point:
                            $format_dec_point = $format[$f_num - 3];
                        }
                        $thousands_sep_pos = 5;
                    } else {	// If format has no decimal part:
                        $format_decimals = 0;
                        $thousands_sep_pos = 3;
                    }
                    if ($f_num > $thousands_sep_pos + 1 && preg_match('#^[^\d]+$#', $format[$f_num - $thousands_sep_pos])) {	// Get a thousands separator:
                        $format_thousands_sep = $format[$f_num - $thousands_sep_pos];
                    }
                    // Format number with extracted data:
                    $custom_field_value = number_format(floatval($custom_field_value), $format_decimals, $format_dec_point, $format_thousands_sep);
                }
                // Add prefix and suffix:
                $custom_field_value = $format_prefix . $custom_field_value . $format_suffix;
                if ($format_class !== '') {	// Apply class for the format:
                    $custom_field_value = '<span class="' . format_to_output($format_class, 'htmlattr') . '">' . $custom_field_value . '</span>';
                }
                break;

            case 'text':
                // Escape html tags and convert new lines to html <br> for text fields:
                $custom_field_value = nl2br(utf8_trim(utf8_strip_tags($custom_field_value)));
                break;

            case 'image':
                // Display image fields as thumbnail:
                $LinkCache = &get_LinkCache();
                if ($Link = &$LinkCache->get_by_ID($custom_field_value, false, false)) {
                    $custom_field_value = $Link->get_tag([
                        'image_link_to' => false,
                        'image_size' => $format,
                    ]);
                } else {	// Display an error if Link is not found in DB:
                    $custom_field_value = get_rendering_error(T_('Invalid link ID:') . ' ' . $custom_field_value, 'span');
                }
                break;

            case 'url':
                // Format URL field value:
                if ($format === null || $format === '') {	// No format:
                    break;
                }

                $formats = explode(';', $format);

                if ($custom_field_value === '' || $custom_field_value === null) {	// Use second format option for empty url:
                    if (! isset($formats[1]) || $formats[1] === '') {	// No format for empty URL:
                        return $custom_field_value;
                    } else {	// Set a format for empty URL:
                        $format_value = $formats[1];
                    }
                } else {	// Use first format option for not empty url:
                    $format_value = $formats[0];
                }

                if ($format_value != '#url#') {	// Use specific text for link from format if it is not requested to use original url as link text:
                    $custom_field_value = $format_value;
                }
                break;
        }

        // Render special masks like #yes#, (+), #stars/3# and etc. in value with template:
        $params['stars_value'] = $orig_custom_field_value;
        $custom_field_value = render_custom_field($custom_field_value, $params);

        // Apply setting "Link to":
        if ($custom_field['link'] != 'nolink' && ! empty($custom_field_value)) {
            $link_fallbacks = [
                'linkpermzoom' => ['link', 'perm', 'zoom'],
                'permzoom' => ['perm', 'zoom'],
                'linkperm' => ['link', 'perm'],
                'linkto' => ['link'],
                'permalink' => ['perm'],
                'zoom' => ['zoom'],
                'fieldurl' => ['url'],
                'fieldurlblank' => ['urlblank'],
            ];

            if (isset($link_fallbacks[$custom_field['link']])) {
                $fallback_count = count($link_fallbacks[$custom_field['link']]);
                $link_class = trim($custom_field['link_class']);
                $link_class_attr = ($link_class === '' ? '' : ' class="' . format_to_output($link_class, 'htmlattr') . '"');
                $nofollow_attr = $custom_field['link_nofollow'] ? ' rel="nofollow"' : '';
                foreach ($link_fallbacks[$custom_field['link']] as $l => $link_fallback) {
                    switch ($link_fallback) {
                        case 'link':
                            // Link to "URL":
                            if ($this->get('url') != '') {	// If this post has a specified setting "Link to url":
                                $custom_field_value = '<a href="' . $this->get('url') . '" target="_blank"' . $nofollow_attr . $link_class_attr . '>' . $custom_field_value . '</a>';
                                break 2;
                            }
                            // else fallback to other points:
                            break;

                        case 'perm':
                            // Permalink:
                            global $disp, $Item;
                            if (($disp != 'single' && $disp != 'page') ||
                                ! ($Item instanceof Item) ||
                                $Item->ID != $this->ID ||
                                $fallback_count == $l + 1) {	// Use permalink if it is not last point and we don't view this current post:
                                $custom_field_value = $this->get_permanent_link($custom_field_value, '#', $link_class, '', '', null, [], [
                                    'nofollow' => $custom_field['link_nofollow'],
                                ]);
                                break 2;
                            }
                            // else fallback to other points:
                            break;

                        case 'zoom':
                            // Link to zoom image:
                            if ($custom_field['type'] == 'image' &&
                                $LinkCache = &get_LinkCache() &&
                                $Link = &$LinkCache->get_by_ID($orig_custom_field_value, false, false) &&
                                $File = &$Link->get_File()) {	// Link to original file:
                                $custom_field_value = '<a href="' . $File->get_url() . '"' . ($File->is_image() ? ' rel="lightbox[p' . $this->ID . ']"' : '') . $link_class_attr . '>' . $custom_field_value . '</a>';
                            }
                            // else fallback to other points:
                            break;

                        case 'url':
                        case 'urlblank':
                            // Use value of url fields as URL to the link:
                            if (! empty($orig_custom_field_value)) {	// Format URL to link only with not empty URL otherwise display URL as simple text if special text is defined in format for empty URL:
                                $url_link_class = $custom_field['link_class'];
                                if ($custom_field_value == $orig_custom_field_value) {	// Use word-break style only when original URL is used for link text because URL may contains very long single word:
                                    $url_link_class .= ' linebreak';
                                }
                                $custom_field_value = '<a href="' . $orig_custom_field_value . '"'
                                    . $nofollow_attr
                                    . ' class="' . format_to_output(trim($url_link_class), 'htmlattr') . '"'
                                    . ($link_fallback == 'urlblank' ? ' target="_blank"' : '') . '>'
                                        . $custom_field_value
                                    . '</a>';
                            }
                            break 2;
                    }
                }
            }
        }

        return $custom_field_value;
    }

    /**
     * Get computed item custom field value by field name
     *
     * @param string Field name, see {@link get_custom_fields_defs()}
     * @return string|boolean|null FALSE if the field doesn't exist, NULL if formula is invalid
     */
    private function get_custom_field_computed($field_name)
    {
        // Get all custom fields by item ID:
        $custom_fields = $this->get_custom_fields_defs();

        if (! isset($custom_fields[$field_name])) {	// The requested field is not detected:
            return false;
        }

        if ($custom_fields[$field_name]['type'] == 'double') {	// This case may be called by computing of the formula:
            // Use floatval() in order to consider empty value as 0
            return floatval($this->get_custom_field_value($field_name));
        }

        if ($custom_fields[$field_name]['type'] != 'computed') {	// The requested field is detected but it is not computed field:
            return false;
        }

        // Compute value by formula:
        $formula = $custom_fields[$field_name]['formula'];
        if (empty($formula)) {	// Use NULL value because formula is empty:
            return null;
        }

        // Use NULL value for all cases below when formula is invalid or it cannot be computed by some unknown reason:
        $custom_field_value = null;

        if (! isset($this->cache_computed_custom_fields)) {	// Store in this array all computed fields to avoid recursion:
            $this->cache_computed_custom_fields = [];
        }
        if (in_array($field_name, $this->cache_computed_custom_fields)) {	// Stop here because of recursion:
            return null;
        }
        $this->cache_computed_custom_fields[] = $field_name;

        // Try to use a formula:
        $formula_is_valid = true;
        if (preg_match_all('#\$([^$]+)\$#', $formula, $formula_match)) {
            foreach ($formula_match[1] as $formula_field_index) {
                if (! isset($custom_fields[$formula_field_index]) ||
                        ! in_array($custom_fields[$formula_field_index]['type'], ['double', 'computed']) ||
                        ($formula_field_value = $this->get_custom_field_computed($formula_field_index)) === false ||
                        ! is_numeric($formula_field_value)) {	// Formula must use only custom fields with type "double"/"computed" and value must be a numeric:
                    $formula_is_valid = false;
                    // Stop here to don't check other fields because formula is already invalid:
                    break;
                }
            }
        }

        if ($formula_is_valid) {	// Check functions in formula because all functions are forbidden in formula:
            $formula_is_valid = ! preg_match('#[a-z0-9_]+\s*\(.*?\)#i', $formula);
        }

        if ($formula_is_valid) {	// Try to compute a value if formula is valid:
            $formula = preg_replace('#\$([^$]+)\$#', '$this->get_custom_field_computed( \'$1\' )', $formula);
            try {	// Compute value:
                ob_start();
                $custom_field_value = eval("return $formula;");
                $formula_code_output = ob_get_clean();
                if (($formula_code_output !== '' && $formula_code_output !== false) ||
                        ! is_numeric($custom_field_value)) {	// If output buffer contains some text it means there is some error;
                    // Don't allow to use not numeric value for the "computed" custom field:
                    $custom_field_value = null;
                }
            } catch (Error $e) {	// Set NULL value for wrong formula:
                $custom_field_value = null;
            } catch (ParseError $e) {	// Set NULL value for wrong formula:
                $custom_field_value = null;
            }
        }

        // Unset temp array at the end of recursion:
        unset($this->cache_computed_custom_fields);

        return $custom_field_value;
    }

    /**
     * TEMPLATE TAG: Display custom field
     *
     * @param array Params
     */
    public function custom($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            // required: 'field'
            'what' => 'formatted_value', // 'label' - to display label of the custom field
            'before' => ' ',
            'after' => ' ',
        ], $params);

        if (empty($params['field'])) {
            return;
        }

        // Load custom field by index:
        $custom_fields = $this->get_custom_fields_defs();
        $field_name = $params['field'];
        if (! isset($custom_fields[$field_name])) { // Custom field with this index doesn't exist
            display_rendering_error(sprintf(T_('The custom field %s does not exist!'), '<b>' . $field_name . '</b>'), 'span');
            return;
        }

        switch ($params['what']) {
            case 'label':
                $r = $this->get_custom_field_title($params['field']);
                break;

            default: // formatted_value
                $r = $this->get_custom_field_formatted($field_name, $params);
                break;
        }

        if (is_string($r) && $r !== '') {	// Print if value is not empty string:
            echo $params['before'] . $r . $params['after'];
        }
    }

    /**
     * Display all custom fields of current Item
     *
     * @param array Params
     */
    public function custom_fields($params = [])
    {
        echo $this->get_custom_fields($params);
    }

    /**
     * Get all custom fields of this Item as HTML code
     *
     * @param array Params
     * @return string
     */
    public function get_custom_fields($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'fields' => '', // Empty string to display ALL fields, OR fields names separated by comma to show/hide only the requested fields in order what you want
            'fields_source' => 'include', // 'all' - All item's fields, 'exclude' - All except fields listed in the param 'fields', 'include' - Only fields listed in the param 'fields'
            // See default template params in the widget function item_fields_compare_Widget->display():
        ], $params);

        // Convert fields separator to widget format:
        $custom_fields = str_replace(',', "\n", trim($params['fields'], ', '));

        // Call widget with params only when content is not generated yet above:
        ob_start();
        skin_widget(array_merge($params, [
            'widget' => 'item_custom_fields',
            'fields_source' => empty($custom_fields) ? 'all' : $params['fields_source'],
            'fields' => $custom_fields,
            'items' => $this->ID,
        ]));
        $widget_html = ob_get_contents();
        ob_end_clean();

        return $widget_html;
    }

    /**
     * Convert all inline tags to HTML code
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_inline_tags($content, $params = [])
    {
        $params = array_merge([
            'check_code_block' => true, // TRUE to find inline tags only outside of codeblocks
            // render_content_blocks():
            'render_tag_include' => true,
            'render_tag_cblock' => true,
            // render_inline_widgets():
            'render_tag_item_subscribe' => true,
            'render_tag_item_emailcapture' => true,
            'render_tag_item_compare' => true,
            'render_tag_item_fields' => true,
            // render_block_widgets():
            'render_tag_switcher' => true,
            // render_inline_files():
            'render_tag_image' => true,
            'render_tag_file' => true,
            'render_tag_inline' => true,
            'render_tag_video' => true,
            'render_tag_audio' => true,
            'render_tag_thumbnail' => true,
            'render_tag_folder' => true,
            // render_link_data():
            'render_tag_item_link' => true,
            // render_custom_fields():
            'render_tag_field' => true,
            // render_other_item_data():
            'render_tag_item_field' => true,
            'render_tag_item_titlelink' => true,
            'render_tag_item_url' => true,
            // render_collection_data():
            'render_tag_coll_name' => true,
            'render_tag_coll_shortname' => true,
            // render_switchable_blocks():
            'render_tag_switchable_div' => true,
            // render_templates():
            'render_tag_template' => true,
        ], $params);

        // Remove block level short tags inside <p> blocks and move them before the paragraph:
        $content = move_short_tags($content);

        // Render Content block tags like [include:123], [include:item-slug], [cblock:123], [cblock:item-slug]:
        $content = $this->render_content_blocks($content, $params);

        // Render widget tags (subscribe, emailcapture, compare, fields):
        $content = $this->render_inline_widgets($content, $params);

        // Render widget tags (switcher):
        $content = $this->render_block_widgets($content, $params);

        // Render inline file tags like [image:123:caption] or [file:123:caption]:
        $content = render_inline_files($content, $this, array_merge($params, [
            'clear_paragraph' => false, // Don't clear paragraph twice
        ]));

        // Render Collection Data [link:url_field], [link:url_field]title[/link] and etc.:
        $content = $this->render_link_data($content, $params);

        // Render single value of Custom Fields [field:first_string_field]:
        $content = $this->render_custom_fields($content, $params);

        // Render parent/other item data [parent:titlelink], [parent:url], [parent:field:first_string_field], [item:123:titlelink], [item:slug:titlelink] and etc.:
        $content = $this->render_other_item_data($content, $params);

        // Render Collection Data [coll:name], [coll:shortname]:
        $content = $this->render_collection_data($content, $params);

        // Render switchable block tags like [div::view=detailed]Multiline Content Text[/div]:
        $content = $this->render_switchable_blocks($content, $params);

        // Render template tags like [template:template_code|param1=value1|param2=value2]:
        $content = $this->render_templates($content, $params);

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this)) {	// Clean up rendering errors from content if current User has no permission to edit this Item:
            $content = clear_rendering_errors($content);
        }

        return $content;
    }

    /**
     * Convert inline widget tags like [subscribe], [emailcapture], [compare], [fields], [parent:fields], [item:123:fields], [item:slug:fields] into HTML tags
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_inline_widgets($content, $params)
    {
        global $Settings;

        $params = array_merge([
            'render_tag_item_subscribe' => true,
            'render_tag_item_emailcapture' => true,
            'render_tag_item_compare' => true,
            'render_tag_item_fields' => true,
        ], $params);

        $render_tags = [];
        if ($params['render_tag_item_subscribe']) {	// Render short tag [subscribe:]
            $render_tags[] = 'subscribe';
        }
        if ($params['render_tag_item_emailcapture']) {	// Render short tag [emailcapture:]
            $render_tags[] = 'emailcapture';
        }
        if ($params['render_tag_item_compare']) {	// Render short tag [compare:]
            $render_tags[] = 'compare';
        }
        if ($params['render_tag_item_fields']) {	// Render short tag [fields:]
            $render_tags[] = 'fields';
        }

        if (empty($render_tags)) {	// No tags for rendering:
            return $content;
        }

        load_funcs('skins/_skin.funcs.php');
        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_collection_data() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_inline_widgets'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of widgets:
        preg_match_all('/\[(parent:|item:[^:\]]+:)?(' . implode('|', $render_tags) . '):?([^\]]*)\]/i', $content, $tags);

        if (count($tags[0]) > 0) {	// If at least one widget tag is found in content:
            foreach ($tags[0] as $t => $source_tag) {	// Render URL custom field as html:
                $field_Item = $this;
                $widget_params = false;
                $widget_html = false;
                $tag_prefix = $tags[1][$t];
                $widget_name = $tags[2][$t];
                $tag_params = explode(':', $tags[3][$t]);
                switch ($widget_name) {
                    case 'subscribe':
                        // Widget "Newsletter/Email list subscription":
                        $button_notsubscribed = '';
                        $button_subscribed = '';
                        $button_notloggedin = '';

                        preg_match('/(\d+)(?:\/(.*))?/', $tag_params[0], $newsletter_ID_tags);
                        $newsletter_ID = intval($newsletter_ID_tags[1]);
                        if (isset($newsletter_ID_tags[2])) {
                            $user_tags = $newsletter_ID_tags[2];
                        }

                        if (isset($tag_params[1])) {
                            $button_notsubscribed = $tag_params[1];
                        }

                        if (isset($tag_params[2])) {
                            $button_subscribed = $tag_params[2];
                        }

                        if (isset($tag_params[3])) {
                            $button_notloggedin = $tag_params[3];
                        }

                        $widget_params = [
                            'widget' => 'newsletter_subscription',
                            'title' => '',
                            'intro' => '',
                            'bottom' => '',
                            'title_subscribed' => '',
                            'intro_subscribed' => '',
                            'bottom_subscribed' => '',
                            'enlt_ID' => $newsletter_ID,
                            'button_notsubscribed_class' => 'btn-danger',
                            'button_subscribed_class' => 'btn-success',
                            'inline' => 1,
                        ];
                        if (! empty($button_notsubscribed)) {
                            $widget_params['button_notsubscribed'] = $button_notsubscribed;
                        }
                        if (! empty($button_subscribed)) {
                            $widget_params['button_subscribed'] = $button_subscribed;
                        }
                        if (! empty($user_tags)) {
                            $widget_params['usertags'] = $user_tags;
                            $widget_params['unsubscribed_if_not_tagged'] = true;
                        }

                        if (! empty($button_notloggedin) && ! is_logged_in()) { // Email capture widget does not display if user is not logged in
                            $redirect_to = regenerate_url('', '', '', '&');
                            $widget_html = '<div class="center">';
                            $widget_html .= '<a href="' . get_login_url('inline subscribe', $redirect_to) . '" class="btn btn-primary">' . $button_notloggedin . '</a>';
                            $widget_html .= '</div>';
                        }
                        break;

                    case 'emailcapture':
                        // Widget "Email capture / Quick registration":
                        preg_match('/(\d+)?(?:\/(.*))?/', $tag_params[0], $newsletter_ID_tags);
                        $newsletter_ID = isset($newsletter_ID_tags[1]) ? intval($newsletter_ID_tags[1]) : null;
                        $user_tags = isset($newsletter_ID_tags[2]) ? $newsletter_ID_tags[2] : null;
                        $fields_to_display = isset($tag_params[1]) ? explode('+', $tag_params[1]) : [];
                        $button_text = isset($tag_params[2]) ? $tag_params[2] : '';

                        $widget_params = [
                            'widget' => 'user_register_quick',
                            'title' => '',
                            'intro' => '',
                            'ask_firstname' => in_array('firstname', $fields_to_display) ? 'required' : 'no',
                            'ask_lastname' => in_array('lastname', $fields_to_display) ? 'required' : 'no',
                            'ask_country' => in_array('country', $fields_to_display) ? 'required' : 'no',
                            'source' => 'Page: ' . $this->get('urltitle'),
                            'usertags' => $user_tags,
                            'subscribe' => [
                                'post' => 0,
                                'comment' => 0,
                            ],
                            'button_class' => 'btn-primary',
                            'inline' => 1,
                        ];

                        $NewsletterCache = &get_NewsletterCache();
                        $load_where = 'enlt_active = 1';
                        $NewsletterCache->load_where($load_where);
                        // Initialize checkbox options for param "Newsletter":
                        $newsletters_options = [];
                        $def_newsletters = explode(',', $Settings->get('def_newsletters'));
                        foreach ($NewsletterCache->cache as $Newsletter) {
                            $newsletters_options[] = [
                                $Newsletter->ID,
                                $Newsletter->get('name') . ': ' . $Newsletter->get('label'),
                                $Newsletter->ID == $newsletter_ID ? 1 : 0, // checked if specified newsletter ID
                            ];
                        }
                        $newsletters_options[] = [
                            'default',
                            T_('Also subscribe user to all default newsletters for new users.'),
                            empty($newsletter_ID) ? 1 : 0, // checked if no specific newsletter ID specified
                        ];
                        $widget_params['newsletters'] = $newsletters_options;

                        if (! empty($button_text)) {
                            $widget_params['button'] = $button_text;
                        }
                        break;

                    case 'compare':
                        // Widget "Compare Item Fields":
                        // Set item IDs to compare:
                        $compare_items = isset($tag_params[0]) ? trim($tag_params[0], ', ') : '';
                        if (empty($compare_items)) {	// Skip a compare tag without item IDs:
                            break;
                        }
                        // Set fields to compare:
                        $compare_fields = isset($tag_params[1]) ? str_replace(',', "\n", trim($tag_params[1], ', ')) : '';
                        // Set widget params to display:
                        $widget_params = [
                            'widget' => 'item_fields_compare',
                            'items_source' => 'list',
                            'items' => $compare_items,
                            'fields_source' => empty($compare_fields) ? 'all' : 'include',
                            'fields' => $compare_fields,
                        ];
                        break;

                    case 'fields':
                        // Widget "Item Custom Fields":
                        if ($tag_prefix == 'parent:') {	// Use parent item:
                            if (! ($widget_Item = &$this->get_parent_Item())) {	// Display error message if parent doesn't exist:
                                $widget_html = get_rendering_error(T_('This Item has no parent.'), 'span');
                                break;
                            }
                            $widget_item_ID = $widget_Item->ID;
                        } elseif (strpos($tag_prefix, 'item:') === 0) {	// Use other item by ID or slug:
                            $widget_item_ID_slug = substr($tag_prefix, 5, -1);
                            $widget_item_data_is_number = is_number($widget_item_ID_slug);
                            $ItemCache = &get_ItemCache();
                            if (! ($widget_item_data_is_number && $widget_Item = &$ItemCache->get_by_ID($widget_item_ID_slug, false, false)) &&
                                    ! (! $widget_item_data_is_number && $widget_Item = &$ItemCache->get_by_urltitle($widget_item_ID_slug, false, false))) {	// Display error message if other item is not found by ID and slug:
                                $widget_html = get_rendering_error(sprintf(T_('The Item %s doesn\'t exist.'), '<code>' . $widget_item_ID_slug . '</code>'), 'span');
                                break;
                            }
                            $widget_item_ID = $widget_Item->ID;
                        } else {	// Use current Item:
                            $widget_item_ID = $this->ID;
                            $widget_Item = $this;
                        }

                        $custom_fields = $widget_Item->get_custom_fields_defs();
                        if (! $custom_fields) {	// Fields don't exist for this Item:
                            $widget_html = get_rendering_error(T_('The Item has no custom fields.'), 'span');
                            break;
                        }

                        // Set fields to display:
                        $custom_fields = isset($tag_params[0]) ? str_replace(',', "\n", trim($tag_params[0], ', ')) : '';
                        // Set widget params to display:
                        $widget_params = [
                            'widget' => 'item_custom_fields',
                            'fields_source' => empty($custom_fields) ? 'all' : 'include',
                            'fields' => $custom_fields,
                            'items' => $widget_item_ID,
                        ];
                        break;
                }

                // If widget display params are initialized for the inline tag:
                if ($widget_params !== false && $widget_html === false) {	// Call widget with params only when content is not generated yet above:
                    ob_start();
                    skin_widget(array_merge($params, $widget_params));
                    $widget_html = ob_get_contents();
                    ob_end_clean();
                }
                if ($widget_html !== false) {	// Replace inline widget tag with content generated by requested widget:
                    $content = substr_replace($content, $widget_html, strpos($content, $source_tag), strlen($source_tag));
                }
            }
        }

        return $content;
    }

    /**
     * Convert block widget tags like [switcher:param_name][option:value]Text[/option][/switcher] into HTML tags
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_block_widgets($content, $params)
    {
        global $Settings;

        $params = array_merge([
            'render_tag_switcher' => true,
        ], $params);

        if (! $params['render_tag_switcher']) {	// No tags for rendering:
            return $content;
        }

        load_funcs('skins/_skin.funcs.php');
        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_collection_data() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_block_widgets'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of widgets:
        if (! preg_match_all('#\[(switcher)(:.+?)?\](.*?)\[/\1\]#is', $content, $tags)) {	// No found tags:
            return $content;
        }

        foreach ($tags[0] as $t => $source_tag) {
            $widget_params = false;
            $widget_html = false;
            $widget_name = $tags[1][$t];
            $tag_params = explode(':', trim($tags[2][$t], ':'));
            switch ($widget_name) {
                case 'switcher':
                    // Widget "Param Switcher":s
                    if (! isset($tag_params[0]) || $tag_params[0] === '') {	// Skip wrong configured tag:
                        $widget_html = get_rendering_error(T_('Param code must be defined for switcher tag!'), 'span');
                        break;
                    }

                    $widget_buttons = [];
                    if (preg_match_all('#\[(option):(.+?)\](.+?)\[/\1\]#is', $tags[3][$t], $tag_options)) {	// Initialize buttons for widget "Param Switcher":
                        foreach ($tag_options[2] as $o => $tag_option_value) {
                            $widget_buttons[] = [
                                'value' => $tag_option_value,
                                'text' => $tag_options[3][$o],
                            ];
                        }
                    }
                    if (empty($widget_buttons)) {	// Don't try to render widget without buttons:
                        $widget_html = get_rendering_error(T_('At least one button must be defined for switcher tag!'), 'span');
                        break;
                    }

                    // Set widget params to display:
                    $widget_params = [
                        'widget' => 'param_switcher',
                        'param_code' => $tag_params[0],
                        'buttons' => $widget_buttons,
                    ];

                    if (isset($tag_params[1]) && in_array($tag_params[1], ['auto', 'list', 'buttons'])) {	// Set a display mode:
                        $widget_params['display_mode'] = $tag_params[1];
                    }
                    break;
            }

            // If widget display params are initialized for the inline tag:
            if ($widget_params !== false && $widget_html === false) {	// Call widget with params only when content is not generated yet above:
                ob_start();
                skin_widget(array_merge($params, $widget_params));
                $widget_html = ob_get_contents();
                ob_end_clean();
            }
            if ($widget_html !== false) {	// Replace inline widget tag with content generated by requested widget:
                $content = substr_replace($content, $widget_html, strpos($content, $source_tag), strlen($source_tag));
            }
        }

        return $content;
    }

    /**
     * Convert inline custom field tags like [field:first_string_field] into HTML tags
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_custom_fields($content, $params = [])
    {
        $params = array_merge([
            'render_tag_field' => true,
        ], $params);

        if (! $params['render_tag_field']) {	// No tags for rendering:
            return $content;
        }

        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_custom_fields() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_custom_fields'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of custom fields:
        preg_match_all('/\[field:([^\]]*)?\]/i', $content, $tags);

        foreach ($tags[0] as $t => $source_tag) {
            // Render single field as text:
            $field_name = trim($tags[1][$t]);
            $field_value = $this->get_custom_field_formatted($field_name, $params);
            if ($field_value === false) {	// Wrong field request, display error:
                $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The field "%s" does not exist.'), $field_name), 'span'), $content);
            } else {	// Display field value:
                $custom_fields = $this->get_custom_fields_defs();
                if ($custom_fields[$field_name]['public']) {	// Display value only if custom field is public:
                    $content = str_replace($source_tag, $field_value, $content);
                } else {	// Display an error for not public custom field:
                    $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The field "%s" is not public.'), $field_name), 'span'), $content);
                }
            }
        }

        return $content;
    }

    /**
     * Convert inline parent/other item tags into HTML tags like:
     *    [parent:titlelink]
     *    [parent:url]
     *    [parent:field:first_string_field]
     *    [item:123:titlelink]
     *    [item:123:url]
     *    [item:123:field:first_string_field]
     *    [item:slug:titlelink]
     *    [item:slug:url]
     *    [item:slug:field:first_string_field]
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_other_item_data($content, $params = [])
    {
        $params = array_merge([
            'render_tag_item_field' => true,
            'render_tag_item_titlelink' => true,
            'render_tag_item_url' => true,
        ], $params);

        $render_tags = [];
        if ($params['render_tag_item_field']) {	// Render short tag [item:123:field:]
            $render_tags[] = 'field';
        }
        if ($params['render_tag_item_titlelink']) {	// Render short tag [item:123:titlelink]
            $render_tags[] = 'titlelink';
        }
        if ($params['render_tag_item_url']) {	// Render short tag [item:123:url]
            $render_tags[] = 'url';
        }

        if (empty($render_tags)) {	// No tags for rendering:
            return $content;
        }

        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_other_item_data() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_other_item_data'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of parent data:
        preg_match_all('/\[(parent|item:[^:]+):(' . implode('|', $render_tags) . '):?([^\]]*)?\]/i', $content, $tags);

        if (count($tags[0]) > 0) {	// If at least one other item tag is found in content:
            foreach ($tags[0] as $t => $source_tag) {
                if ($tags[1][$t] == 'parent') {	// Get data of item parent:
                    if (! ($other_Item = &$this->get_parent_Item())) {	// Display error message if parent doesn't exist:
                        $content = str_replace($tags[0][$t], get_rendering_error(T_('This Item has no parent.'), 'span'), $content);
                        continue;
                    }
                } else {	// Try to use other item by ID or slug:
                    $other_item_ID_slug = substr($tags[1][$t], 5);
                    $other_item_data_is_number = is_number($other_item_ID_slug);
                    $ItemCache = &get_ItemCache();
                    if (! ($other_item_data_is_number && $other_Item = &$ItemCache->get_by_ID($other_item_ID_slug, false, false)) &&
                        ! (! $other_item_data_is_number && $other_Item = &$ItemCache->get_by_urltitle($other_item_ID_slug, false, false))) {	// Display error message if other item is not found by ID and slug:
                        $content = str_replace($tags[0][$t], get_rendering_error(sprintf(T_('The Item %s doesn\'t exist.'), '<code>' . $other_item_ID_slug . '</code>'), 'span'), $content);
                        continue;
                    }
                }

                switch ($tags[2][$t]) {
                    case 'field':
                        // Render single parent custom field as text:
                        $field_name = trim($tags[3][$t]);
                        $field_value = $other_Item->get_custom_field_formatted($field_name, $params);
                        if ($field_value === false) {	// Wrong field request, display error:
                            $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The field "%s" does not exist.'), $field_name), 'span'), $content);
                        } else {	// Display field value:
                            $custom_fields = $other_Item->get_custom_fields_defs();
                            if ($custom_fields[$field_name]['public']) {	// Display value only if custom field is public:
                                $content = str_replace($source_tag, $field_value, $content);
                            } else {	// Display an error for not public custom field:
                                $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The field "%s" is not public.'), $field_name), 'span'), $content);
                            }
                        }
                        break;

                    case 'titlelink':
                        // Render parent title with link:
                        $content = str_replace($source_tag, $other_Item->get_title(), $content);
                        break;

                    case 'url':
                        // Render parent URL:
                        $content = str_replace($source_tag, $other_Item->get_permanent_url(), $content);
                        break;
                }
            }
        }

        return $content;
    }

    /**
     * Convert inline collection tags into HTML tags like:
     *    [coll:name]
     *    [coll:shortname]
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_collection_data($content, $params = [])
    {
        $params = array_merge([
            'render_tag_coll_name' => true,
            'render_tag_coll_shortname' => true,
        ], $params);

        $render_tags = [];
        if ($params['render_tag_coll_name']) {	// Render short tag [coll:name]
            $render_tags[] = 'name';
        }
        if ($params['render_tag_coll_shortname']) {	// Render short tag [coll:shortname]
            $render_tags[] = 'shortname';
        }

        if (empty($render_tags)) {	// No tags for rendering:
            return $content;
        }

        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_collection_data() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_collection_data'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of collection data:
        preg_match_all('/\[coll:(' . implode('|', $render_tags) . ')\]/i', $content, $tags);

        if (count($tags[0]) > 0) {	// If at least one collection tag is found in content:
            $item_Blog = &$this->get_Blog();

            foreach ($tags[0] as $t => $source_tag) {
                switch ($tags[1][$t]) {
                    case 'name':
                        // Render collection name:
                        $content = str_replace($source_tag, $item_Blog->get('name'), $content);
                        break;

                    case 'shortname':
                        // Render collection short name:
                        $content = str_replace($source_tag, $item_Blog->get('shortname'), $content);
                        break;
                }
            }
        }

        return $content;
    }

    /**
     * Convert inline link tags into HTML tags like:
     *    [link:url_field]
     *    [link:url_field]title[/link]
     *    [link:url_field:.class1.class2]title[/link]
     * url_field is code of custom item field with type "URL"
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_link_data($content, $params = [])
    {
        $params = array_merge([
            'render_tag_item_link' => true,
        ], $params);

        if (! $params['render_tag_item_link']) {	// No tags for rendering:
            return $content;
        }

        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_link_data() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_link_data'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of link data:
        preg_match_all('/\[(parent:|item:[^:]+:)?link:([^\]]+)\]((.*?)\[\/link\])?/i', $content, $tags);

        if (count($tags[0]) > 0) {	// If at least one link tag is found in content:
            foreach ($tags[0] as $t => $source_tag) {	// Render URL custom field as html:
                if ($tags[1][$t] == 'parent:') {	// Try to use parent:
                    if (! ($other_Item = &$this->get_parent_Item())) {	// Display error message if parent doesn't exist:
                        $content = substr_replace($content, get_rendering_error(T_('This Item has no parent.'), 'span'), strpos($content, $source_tag), strlen($source_tag));
                        continue;
                    }
                } elseif (! empty($tags[1][$t])) {	// Try to use other item by ID or slug:
                    $other_item_ID_slug = rtrim(substr($tags[1][$t], 5), ':');
                    $other_item_data_is_number = is_number($other_item_ID_slug);
                    $ItemCache = &get_ItemCache();
                    if (! ($other_item_data_is_number && $other_Item = &$ItemCache->get_by_ID($other_item_ID_slug, false, false)) &&
                        ! (! $other_item_data_is_number && $other_Item = &$ItemCache->get_by_urltitle($other_item_ID_slug, false, false))) {	// Display error message if other item is not found by ID and slug:
                        $content = str_replace($tags[0][$t], get_rendering_error(sprintf(T_('The Item %s doesn\'t exist.'), '<code>' . $other_item_ID_slug . '</code>'), 'span'), $content);
                        continue;
                    }
                } else {	// Use this Item:
                    $other_Item = $this;
                }

                $link_data = explode(':', $tags[2][$t]);

                // Get field code:
                $url_field_code = trim($link_data[0]);

                $custom_fields = $other_Item->get_custom_fields_defs();
                $field_value = $other_Item->get_custom_field_value($url_field_code, 'url');
                if ($field_value === false) {	// Wrong field request, display error:
                    $link_html = get_rendering_error(sprintf(T_('The field "%s" does not exist.'), $url_field_code), 'span');
                } elseif (! $custom_fields[$url_field_code]['public']) {	// Display an error for not public custom field:
                    $link_html = get_rendering_error(sprintf(T_('The field "%s" is not public.'), $url_field_code), 'span');
                } elseif ($field_value === '') {	// Empty field value, display error:
                    $link_html = get_rendering_error(sprintf(T_('Referenced URL field is empty.'), $url_field_code), 'span');
                } else {	// Display URL field as html link:
                    $link_class = empty($link_data[1]) ? '' : str_replace('.', ' ', $link_data[1]);
                    if (empty($tags[4][$t])) {	// Add style class to break long urls:
                        $link_class .= ' linebreak';
                    }
                    $link_class = ' class="' . trim($link_class) . '"';
                    $link_text = empty($tags[4][$t]) ? $field_value : $tags[4][$t];
                    $link_html = '<a href="' . $field_value . '"' . $link_class . '>' . $link_text . '</a>';
                }
                $content = substr_replace($content, $link_html, strpos($content, $source_tag), strlen($source_tag));
            }
        }

        return $content;
    }

    /**
     * Convert inline content block tags like [include:123], [include:item-slug], [cblock:123], [cblock:item-slug] into item/post content
     *
     * @param string Source content
     * @param array Params
     * @return string Content
     */
    public function render_content_blocks($content, $params = [])
    {
        global $content_block_items;

        $params = array_merge([
            'render_tag_include' => true,
            'render_tag_cblock' => true,
        ], $params);

        $render_tags = [];
        if ($params['render_tag_include']) {	// Render short tag [include:]
            $render_tags[] = 'include';
        }
        if ($params['render_tag_cblock']) {	// Render short tag [cblock:]
            $render_tags[] = 'cblock';
        }

        if (empty($render_tags)) {	// No tags for rendering:
            return $content;
        }

        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->render_content_blocks() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_content_blocks'],
                [$params]
            );
            return $content;
        }

        // Find all matches with tags of content block posts:
        preg_match_all('/\[(' . implode('|', $render_tags) . '):?([^\]]*)?\]/i', $content, $tags);

        $ItemCache = &get_ItemCache();

        $item_Blog = &$this->get_Blog();

        foreach ($tags[0] as $t => $source_tag) {
            $tag_options = explode(':', $tags[2][$t]);

            $item_ID_slug = trim($tag_options[0]);

            if ($item_ID_slug === '') {	// Don't render inline content block tag without specified item:
                continue;
            }

            if (! ($content_Item = &$ItemCache->get_by_ID($item_ID_slug, false, false))) {	// Try to get item by slug if it is not found by ID:
                $content_Item = &$ItemCache->get_by_urltitle($item_ID_slug, false, false);
            }

            if (! $content_Item || $content_Item->get_type_setting('usage') != 'content-block') {	// Item is not found by ID and slug or it is not a content block:
                if ($content_Item) {	// It is not a content block:
                    $wrong_item_info = '#' . $content_Item->ID . ' ' . $content_Item->get('title');
                } else {	// Item is not found:
                    $wrong_item_info = '<code>' . $item_ID_slug . '</code>';
                }
                // Replace inline content block tag with error message about wrong referenced item:
                $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The referenced Item (%s) is not a Content Block.'), utf8_trim($wrong_item_info))), $content);
                continue;
            } elseif (get_status_permvalue($this->get('status')) > get_status_permvalue($content_Item->get('status'))) {	// Deny to display content block Item with lower status than parent Item:
                // It means visibility status of content block Item cannot be higher than visibility status of the current/parent Item,
                // See below the ordered list of visibility statuses by weight:
                // - Redirected
                // - Public
                // - Community
                // - Deprecated
                // - Protected
                // - Private
                // - Draft
                // - Review
                // For example, if content block Item has a status "Public" but current/parent Item has a status "Community",
                //              then such content block Item cannot be included into the current/parent Item.
                $content = str_replace($source_tag, get_rendering_error(sprintf(T_('The visibility level of the content block "%s" is not sufficient.'), '#' . $content_Item->ID . ' ' . $content_Item->get('urltitle'))), $content);
                continue;
            } elseif ($content_Item->get('creator_user_ID') != $this->get('creator_user_ID') &&
                    (! $item_Blog || $content_Item->get('creator_user_ID') != $item_Blog->get('owner_user_ID')) &&
                    (! $item_Blog || $content_Item->get_blog_ID() != $item_Blog->ID) &&
                  (! ($info_Blog = &get_setting_Blog('info_blog_ID')) || $content_Item->get_blog_ID() != $info_Blog->ID)
            ) {	// We can display a content block item with at least one condition:
                //  1. Content block Item has same owner as owner of parent Item,
                //  2. Content block Item has same owner as owner of parent Item's collection,
                //  3. Content block Item is in same collection as parent Item,
                //  4. Content block Item from collection for shared content blocks:
                $content_Blog = &$content_Item->get_Blog();
                $content = str_replace($source_tag, get_rendering_error(sprintf(
                    T_('Content block #%d %s (Coll #%d) (Owner: %s) cannot be included here. It must be in the same collection as including Item (Coll #%d) or the info pages collection (Coll #%d)') . '; ' .
                    T_('in any other case, it must have the same owner as the including Item (Item #%d) (Owner: %s) or the same owner as the including Item\'s collection (Owner: %s).'),
                    $content_Item->ID,
                    '<code>' . $content_Item->get('urltitle') . '</code>', // Content block #%d %s
                    $content_Item->get_blog_ID(), // (Coll #%d)
                    get_user_identity_link(null, $content_Item->get('creator_user_ID')), // (Owner: %s)
                    $this->get_blog_ID(), // as including Item (Coll #%d)
                    ($info_Blog = &get_setting_Blog('info_blog_ID')) ? $info_Blog->ID : 0, // the info pages collection (Coll #%d)
                    $this->ID,
                    get_user_identity_link(null, $this->get('creator_user_ID')), // the including Item (Item #%d) (Owner: %s)
                    $item_Blog ? get_user_identity_link(null, $item_Blog->get('owner_user_ID')) : '<code>' . T_('No collection found') . '</code>' // the including Item\'s collection (Owner: %s)
                )), $content);
                continue;
            }

            if (! isset($content_block_items)) {	// Initialize global array to avoid recursion:
                $content_block_items = [];
            }

            if (in_array($content_Item->ID, $content_block_items)) {	// Replace inline content block tag with error message about recursion:
                $content = str_replace($source_tag, get_rendering_error(sprintf(T_('Content inclusion loop detected. Not including "%s".'), '#' . $content_Item->ID . ' ' . $content_Item->get('title'))), $content);
                continue;
            }

            // Store current item in global array to avoid recursion:
            array_unshift($content_block_items, $content_Item->ID);

            $option_index = 1;
            if (isset($tag_options[$option_index]) &&
                substr($tag_options[$option_index], 0, 1) != '.') {	// Use easy template from short tag options:
                $tag_template = $tag_options[$option_index];
                $option_index++;
            } else {	// Use default easy template:
                $tag_template = 'cblock_clearfix';
            }

            $tag_class = isset($tag_options[$option_index]) ? trim($tag_options[$option_index]) : '';
            if ($tag_class !== '') {	// If tag has an option with style class
                $content_block_class = trim(str_replace(['.*', '.'], ['.' . $item_ID_slug, ' '], $tag_class));
            } else {	// Tag has no class:
                $content_block_class = '';
            }

            // Get item content:
            $current_tag_item_content = $content_Item->get_content_block(array_merge($params, [
                'template_code' => $tag_template,
                'content_block_class' => $content_block_class,
            ]));

            // Update level inline tags like [---fields:] into [--fields:] in order to make them render by top caller level Item:
            $current_tag_item_content = $this->update_level_inline_tags($current_tag_item_content);

            if (get_param('preview') === 1 && get_param('preview_block') === 1) {	// Display orange debug wrapper around included content-block Item:
                // Item debug info with Title + Slug:
                $title_debug_info = '<b>' . $content_Item->get('title') . '</b> (' . $content_Item->get('urltitle') . ')';
                if ($item_edit_url = $content_Item->get_edit_url()) {	// Link to edit Item if current User has a permission:
                    $title_debug_info = '<a href="' . $item_edit_url . '">' . $title_debug_info . '</a>';
                }
                // Content Template debug info with Name + Code:
                $TemplateCache = &get_TemplateCache();
                if ($content_Template = &$TemplateCache->get_localized_by_code($tag_template, false, false)) {	// Display template info:
                    $template_debug_info = '<b>' . $content_Template->get('name') . '</b> (' . $content_Template->get('code') . ')';
                    if (check_user_perm('options', 'edit')) {	// Link to edit Template if current User has a permission:
                        $template_debug_info = '<a href="' . get_admin_url('ctrl=templates&amp;action=edit&amp;tpl_ID=' . $content_Template->ID) . '">' . $template_debug_info . '</a>';
                    }
                    $title_debug_info .= ' / ' . $template_debug_info;
                }
                $current_tag_item_content = '<div class="dev-blocks dev-blocks--content-block">' . "\n"
                    . '<div class="dev-blocks-name">'
                        . $title_debug_info
                    . '</div>' . "\n"
                    . $current_tag_item_content . "\n"
                . '</div>';
            }

            // Replace inline content block tag with item content:
            $content = str_replace($source_tag, $current_tag_item_content, $content);

            // Remove current item from global array which is used to avoid recursion:
            array_shift($content_block_items);
        }

        return $content;
    }

    /**
     * Get content of Item with Item Type usage 'content-block'
     *
     * @param array Params
     * @return string
     */
    public function get_content_block($params = [])
    {
        if ($this->get_type_setting('usage') != 'content-block') {	// Exclude no content block Item:
            return '';
        }

        // Load for get_skin_setting():
        load_funcs('skins/_skin.funcs.php');

        $params = array_merge([
            'template_code' => 'cblock_clearfix',
            'image_class' => 'img-responsive',
            'image_size' => get_skin_setting('main_content_image_size', 'fit-1280x720'),
            'image_limit' => 1000,
            'image_link_to' => 'original', // Can be 'original' (image fiel URL), 'single', URL or empty
            'content_block_class' => '',
        ], $params);

        return render_template_code($params['template_code'], $params, [
            'Item' => $this,
        ]);
    }

    /**
     * Update level inline tags like [---fields:] into [--fields:] in order to make them render by top caller level Item:
     *
     * @param string Content
     * @param array Params
     * @return string Content
     */
    public function update_level_inline_tags($content, $params = [])
    {
        if (isset($params['check_code_block']) && $params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call $this->update_level_inline_tags() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'update_level_inline_tags'],
                [$params]
            );
            return $content;
        }

        // Remove one char '-' in order to allow to render the inline tag on top caller Item:
        $content = preg_replace('#\[-([\-a-z:]+.*?\])#i', '[$1', $content);

        return $content;
    }

    /**
     * Render templates from [template:template_code|param1=value1|param2=value2]
     *
     * @param string Content
     * @param array Params
     * @return string Content
     */
    public function render_templates($content, $params = [])
    {
        $params = array_merge([
            'check_code_block' => true,
            'render_tag_template' => true,
        ], $params);

        if (! $params['render_tag_template']) {	// No tags for rendering:
            return $content;
        }

        if ($params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call render_templates() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_templates'],
                [$params]
            );
            return $content;
        }

        $content = preg_replace_callback('#\[template:(.+?)\]#is', [$this, 'render_templates_callback'], $content);

        return $content;
    }

    /**
     * Callback function to render templates
     *
     * @param array Match
     */
    public function render_templates_callback($m)
    {
        $params = explode('|', $m[1], 2);

        $TemplateCache = &get_TemplateCache();

        if (! ($Template = &$TemplateCache->get_by_code($params[0], false, false))) {	// Template is not found:
            return get_rendering_error('Template "' . $params[0] . '" is not found for <code>' . $m[0] . '</code>', 'span');
        }

        if (isset($params[1])) {	// Decode params from tag like |param1=value1|param2=value2:
            $short_tag_params = get_template_tag_params_from_string($params[1]);
        } else {	// No params are provided for the short tag:
            $short_tag_params = [];
        }

        // Render template by code:
        return render_template_code($params[0], $short_tag_params);
    }

    /**
     * Render switchable blocks
     *   from [div:.optional.classnames:view=detailed&size=middle]Multiline Content Text[/div]
     *   to <div class="optional classnames" data-display-condition="view=detailed&size=middle" style="display:none">Multiline Content Text</div>
     *
     * @param string Content
     * @param array Params
     * @return string Content
     */
    public function render_switchable_blocks($content, $params = [])
    {
        $params = array_merge([
            'check_code_block' => true,
            'render_tag_switchable_div' => true,
        ], $params);

        if (! $params['render_tag_switchable_div']) {	// No tags for rendering:
            return $content;
        }

        if ($params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call render_switchable_content() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_switchable_blocks'],
                [$params]
            );
            return $content;
        }

        $content = preg_replace_callback('#(<p>)?\[div:(.+?)\](.*?)\[/div\](</p>)?#is', [$this, 'render_switchable_blocks_callback'], $content);

        return $content;
    }

    /**
     * Callback function to render switchable content
     *
     * @param array Match
     */
    public function render_switchable_blocks_callback($m)
    {
        $params = explode(':', $m[2]);

        $div_attrs = [];

        if (isset($params[0])) {	// Optional classes:
            $classes = trim(str_replace('.', ' ', $params[0]));
            if ($classes !== '') {	// Use only provided classes:
                $div_attrs['class'] = $classes;
            }
        }

        if (isset($params[1])) {	// If switchable conditions are provided:
            $visibility_conditions = $params[1];
            $div_attrs['data-display-condition'] = $visibility_conditions;
            // Check visibility conditions:
            if (! $this->check_switchable_visibility($visibility_conditions)) {
                $div_attrs['style'] = 'display:none';
            }
        }

        // Fix content which may be wrong rendered by plugin like Auto-P and Markdown because shorttag [div:] is not HTML tag:
        // Trim <br /> tags from begin and end:
        $div_content = preg_replace('#^(<br[\s/]*>)?(.+?)(<br[\s/]*>)$#is', '$2', $m[3]);
        // Balance <p> and </p> tags by moving them from outside [div:] to inside it:
        $div_content = $m[1] . $div_content . (isset($m[4]) ? $m[4] : '');

        return '<div' . get_field_attribs_as_string($div_attrs) . '>' . $div_content . '</div>';
    }

    /**
     * Render switchable content
     *
     * @param string Content
     * @param array Params
     * @return string Content
     */
    public function render_switchable_content($content, $params = [])
    {
        if (! $this->get_type_setting('allow_switchable') ||
            ! $this->get_setting('switchable')) {	// Don't render switchable content if it is not allowed by Item Type and disabled for this Item:
            return $content;
        }

        $params = array_merge([
            'check_code_block' => true,
        ], $params);

        if ($params['check_code_block'] && ((stristr($content, '<code') !== false) || (stristr($content, '<pre') !== false))) {	// Call render_switchable_content() on everything outside code/pre:
            $params['check_code_block'] = false;
            $content = callback_on_non_matching_blocks(
                $content,
                '~<(code|pre)[^>]*>.*?</\1>~is',
                [$this, 'render_switchable_content'],
                [$params]
            );
            return $content;
        }

        $content = preg_replace_callback('#(<[a-z]+.+?)(data-display-condition="(.+?)")(.*?>)#i', [$this, 'render_switchable_content_callback'], $content);

        return $content;
    }

    /**
     * Callback function to render switchable content
     *
     * @param array Match
     */
    public function render_switchable_content_callback($m)
    {
        if (preg_match('#(^.+ style=")(.+?)(".+)$#i', $m[0], $style_match) &&
            stripos($style_match[2], 'display:') !== false) {	// Skip already rendered content, probably by render_switchable_blocks() from short tags [div:]Content[/div]:
            return $m[0];
        }

        if ($this->check_switchable_visibility($m[3])) {	// This switchable block should be visible on load current page:
            return $m[0];
        }
        // Otherwise hide this switchable block:
        if (empty($style_match)) {	// Add new style attribute:
            return $m[1] . $m[2] . ' style="display:none;"' . $m[4];
        } else {	// Append style property to existing attribute:
            return $style_match[1] . trim($style_match[2], '; ') . ';display:none;"' . $style_match[3];
        }
    }

    /**
     * Check if block/row/field can be visible by requested conditions
     *
     * @param string Conditions, e.g. view=detailed&size=middle
     * @return boolean TRUE if block/row/field can be visible, FALSE if it must be hidden
     */
    public function check_switchable_visibility($conditions)
    {
        $disp_conditions = explode('&', str_replace(['&amp;amp;', '&amp;'], '&', $conditions));

        foreach ($disp_conditions as $disp_condition) {
            $disp_condition = explode('=', $disp_condition);
            // Get all allowed value by the condition of the custom field:
            $disp_condition_values = explode('|', $disp_condition[1]);
            // Get current value of the param from $_GET or $_POST:
            $param_value = param($disp_condition[0], 'string');
            // Check if we should hide the custom field by condition:
            if (($param_value === '' && ! in_array($this->get_switchable_param($disp_condition[0]), $disp_condition_values)) || // current param value is empty but condition doesn't allow empty values
                ! preg_match('/^[a-z0-9_\-]*$/', $param_value) || // wrong param value
                ($param_value !== '' && ! in_array($param_value, $disp_condition_values))) { // current param value is not allowed by the condition of the custom field
                // Hide custom field if at least one param is not allowed by condition of the custom field:
                return false;
            }
        }

        return true;
    }

    /**
     * Load switchable params
     */
    public function load_switchable_params()
    {
        if (! $this->get_type_setting('allow_switchable') ||
            ! $this->get_setting('switchable')) {	// Don't render switchable content if it is not allowed by Item Type and disabled for this Item:
            $this->switchable_params = [];
            return;
        }

        if (isset($this->switchable_params)) {	// Don't initialize params twice:
            return;
        }

        $this->switchable_params = [];

        // Keep additional param codes in the URL:
        $url_param_codes = $this->get_setting('switchable_params');
        if (! empty($url_param_codes)) {
            $url_param_codes = explode(',', $url_param_codes);
            foreach ($url_param_codes as $url_param_code) {
                $url_param_code = explode('=', trim($url_param_code));
                if (! empty($url_param_code[0])) {	// Memorize additional param to regenerate proper URL below:
                    $default_value = (isset($url_param_code[1]) ? $url_param_code[1] : '');
                    $url_param_value = param($url_param_code[0], 'string', '', true);
                    if ($url_param_value === '') {	// Memorize and set default value as default:
                        memorize_param($url_param_code[0], 'string', '', $default_value);
                        set_param($url_param_code[0], $default_value);
                    }
                    $this->switchable_params[$url_param_code[0]] = $default_value;
                }
            }
        }
    }

    /**
     * Get switchable params
     *
     * @return array Switchable params: Key - param code, Value - default param value
     */
    public function get_switchable_params()
    {
        $this->load_switchable_params();

        return $this->switchable_params;
    }

    /**
     * Get switchable param by code
     *
     * @param string Param code
     * @return string|null Param value
     */
    public function get_switchable_param($param_code)
    {
        $this->load_switchable_params();

        return (isset($this->switchable_params[$param_code]) ? $this->switchable_params[$param_code] : null);
    }

    /**
     * Display "more" link to "After more" or follow-up anchor
     */
    public function more_link($params = [])
    {
        echo $this->get_more_link($params);
    }

    /**
     * Get "more" link to "After more" or follow-up anchor
     */
    public function get_more_link($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'force_more' => false,
            'before' => '<p class="bMore">',
            'after' => '</p>',
            'link_text' => '#',		// text to display as the more link
            'anchor_text' => '#',		// text to display as the more anchor (once the more link has been clicked, # defaults to "Follow up:")
            'link_to' => 'single#anchor',	// target URL for more link, 'single' or 'single#anchor'
            'disppage' => '#',		// page number to display specific page, # for url parameter
            'format' => 'htmlbody',
            'link_class' => '', // class name of the link
            'force_hide_teaser' => false, // Force an item setting 'hide_teaser'
        ], $params);

        global $more;

        if (! $this->has_content_parts($params)) { // This is NOT an extended post, no "read more" is needed:
            return '';
        }

        /* fp 2020-02-22: obsolete code
        if( ( $more == 0 ) && ( $params[ 'link_to' ] == false ) )
        { // Don't display "After more" content
            if( !empty( $params[ 'link_text' ] ) )
            {
                return format_to_output( $params[ 'before'].$params[ 'link_text' ].$params[ 'after'] );
            }
            return '';
        }
        */

        $content_parts = $this->get_content_parts($params);

        // Init an attribute for class
        $class_attr = empty($params['link_class']) ? '' : ' class="' . $params['link_class'] . '"';

        if (! $more && ! $params['force_more']) {	// We're NOT in "more" mode:
            if ($params['link_text'] == '#') { // TRANS: this is the default text for the extended post "more" link
                $params['link_text'] = T_('Read more') . ' &raquo;';
                // Dummy in order to keep previous translation in the loop:
                $dummy = T_('Full story');
            }

            switch ($params['link_to']) {
                case 'single':
                    $params['link_to'] = $this->get_permanent_url();
                    break;

                case 'single#anchor':
                    $params['link_to'] = $this->get_permanent_url() . '#more' . $this->ID;
                    break;
            }

            return format_to_output($params['before']
                        . '<a href="' . $params['link_to'] . '"' . $class_attr . '>'
                        . $params['link_text'] . '</a>'
                        . $params['after'], $params['format']);
        } elseif (! $params['force_hide_teaser'] && ! $this->get_setting('hide_teaser')) {	// We are in more mode and we're not hiding the teaser:
            // (if we're hiding the teaser we display this as a normal page ie: no anchor)
            if ($params['anchor_text'] == '#') { // TRANS: this is the default text displayed once the more link has been activated
                $params['anchor_text'] = '<p class="bMore">' . T_('Follow up:') . '</p>';
            }

            return format_to_output('<a id="more' . $this->ID . '" name="more' . $this->ID . '"' . $class_attr . '></a>'
                            . $params['anchor_text'], $params['format']);
        }
    }

    /**
     * Does the post have different content parts (teaser/extension, divided by "[teaserbreak]")?
     * This is also true for posts that have images with "aftermore" position.
     *
     * @todo fp> This is a heavy operation! We should probably store the presence of `[teaserbreak]` in a var so that future cals do not rexecute again.
     *           BUT first we need to know why we're interested in $params['disppage'], $params['format']  (or better said: in wgat case are we using different values for this?)
     *           ALSO we should probably store the position of [teaserbreak] for even better performance
     *           ALSO we may want to store that at UPDATE time, into the DB, so we have super fast access to it.
     *
     * @access public
     * @return boolean
     */
    public function has_content_parts($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'disppage' => '#',
            'format' => 'htmlbody',
        ], $params);

        if (! isset($this->cache_has_content_parts)) {	// Initialize an array for cache results:
            $this->cache_has_content_parts = [];
        }

        if (! isset($this->cache_has_content_parts[$params['disppage'] . $params['format']])) {	// Initialize result only first time and store in cache in order to don't execute a heavy operation twice:
            $content_page = $this->get_content_page($params['disppage'], $params['format']);

            // Replace <code> and <pre> blocks from content because we're not interested in [teaserbreak] in there
            $content_page = preg_replace('~<(code|pre)[^>]*>.*?</\1>~is', '*', $content_page);

            // Store result in cache for requested page and format:
            $this->cache_has_content_parts[$params['disppage'] . $params['format']] =
                   strpos($content_page, '[teaserbreak]') !== false
                || $this->get_images([
                    'restrict_to_image_position' => 'aftermore',
                ]);
        }

        // Get a result from cache or from recently initialized var above:
        return $this->cache_has_content_parts[$params['disppage'] . $params['format']];
    }

    /**
     * Template function: display deadline date (datetime) of Item
     *
     * @param string date/time format: leave empty to use locale default date format
     * @param boolean true if you want GMT
     */
    public function deadline_date($format = '', $useGM = false)
    {
        if (empty($format)) {
            echo mysql2date(locale_datefmt(), $this->datedeadline, $useGM);
        } else {
            echo mysql2date($format, $this->datedeadline, $useGM);
        }
    }

    /**
     * Template function: display deadline time (datetime) of Item
     *
     * @param string date/time format: leave empty to use locale default time format
     * @param boolean true if you want GMT
     */
    public function deadline_time($format = '', $useGM = false)
    {
        if (empty($format)) {
            echo mysql2date(locale_shorttimefmt(), $this->datedeadline, $useGM);
        } else {
            echo mysql2date($format, $this->datedeadline, $useGM);
        }
    }

    /**
     * Get the title for the <title> tag
     *
     * If it's not specifically entered, use the regular post title instead
     */
    public function get_titletag()
    {
        if (empty($this->titletag)) {
            return $this->get('title');
        }

        return $this->titletag;
    }

    /**
     * Get the meta description tag
     */
    public function get_metadesc()
    {
        return $this->get_setting('metadesc');
    }

    /**
     * Get the meta keyword tag
     */
    public function get_metakeywords()
    {
        return $this->get_setting('metakeywords');
    }

    /**
     * Split tags by comma or semicolon
     *
     * @param string The tags, separated by comma or semicolon
     */
    public function set_tags_from_string($tags)
    {
        // Mark that tags has been updated, even if it is not sure because we do not want to execute extra db query
        $this->dbchanges_flags['tags'] = true;

        if ($tags === '') {
            $this->tags = [];
            return;
        }

        $this->tags = preg_split('/\s*[;,]+\s*/', $tags, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($this->tags as $t => $tag) {
            if (substr($tag, 0, 1) == '-') { // Prevent chars '-' in first position
                $tag = preg_replace('/^-+/', '', $tag);
            }
            if (empty($tag)) { // Don't save empty tag
                unset($this->tags[$t]);
            } else { // Save the modifications for each tag
                $this->tags[$t] = $tag;
            }
        }

        // Remove the duplicate tags
        $this->tags = array_unique($this->tags);
    }

    /**
     * Template function: Provide link to message form for this Item's author.
     *
     * @param string url of the message form
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @return boolean true, if a link was displayed; false if there's no email address for the Item's author.
     */
    public function msgform_link($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#',
            'title' => '#',
            'class' => '',
            'format' => 'htmlbody',
            'form_url' => '#current_blog#',
        ], $params);

        if ($params['form_url'] == '#current_blog#') {	// Get
            global $Collection, $Blog;
            $params['form_url'] = $Blog->get('msgformurl');
        }

        $this->get_creator_User();
        $redirect_to = url_add_param($params['form_url'], 'post_id=' . $this->ID . '&recipient_id=' . $this->creator_User->ID, '&');
        $params['form_url'] = $this->creator_User->get_msgform_url(url_add_param($params['form_url'], 'post_id=' . $this->ID), $redirect_to);

        if (empty($params['form_url'])) {
            return false;
        }

        if ($params['title'] == '#') {
            if ($this->creator_User->get_msgform_possibility() == 'email') {
                $params['title'] = T_('Send email to post author');
            } else {
                $params['title'] = T_('Send message to post author');
            }
        }
        if ($params['text'] == '#') {
            $params['text'] = get_icon('email', 'imgtag', [
                'class' => 'middle',
                'title' => $params['title'],
            ]);
        }

        echo $params['before'];
        echo '<a href="' . $params['form_url'] . '" title="' . $params['title'] . '"';
        if (! empty($params['class'])) {
            echo ' class="' . $params['class'] . '"';
        }
        echo ' rel="nofollow">' . $params['text'] . '</a>';
        echo $params['after'];

        return true;
    }

    /**
     * Template function: Provide link to message form for this Item's assigned User.
     *
     * @param string url of the message form
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @return boolean true, if a link was displayed; false if there's no email address for the assigned User.
     */
    public function msgform_link_assigned($form_url, $before = ' ', $after = ' ', $text = '#', $title = '#', $class = '')
    {
        if (! $this->get_assigned_User() || empty($this->assigned_User->email)) { // We have no email for this Author :(
            return false;
        }

        $form_url = url_add_param($form_url, 'recipient_id=' . $this->assigned_User->ID);
        $form_url = url_add_param($form_url, 'post_id=' . $this->ID);

        if ($title == '#') {
            $title = T_('Send email to assigned user');
        }
        if ($text == '#') {
            $text = get_icon('email', 'imgtag', [
                'class' => 'middle',
                'title' => $title,
            ]);
        }

        echo $before;
        echo '<a href="' . $form_url . '" title="' . $title . '"';
        if (! empty($class)) {
            echo ' class="' . $class . '"';
        }
        echo ' rel="nofollow">' . $text . '</a>';
        echo $after;

        return true;
    }

    /**
     * Display a link to pages for multi-page items
     *
     * @param array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function page_links()
    {
        global $preview;

        $num_args = func_num_args();
        $args = func_get_args();

        if ($num_args == 1 && is_array($args[0])) {
            $params = $args[0];
            if (! isset($params['format'])) {
                $params['format'] = 'htmlbody';
            }
        } else {	// Deprecated since v5, left for compatibility with old skins
            $params['before'] = isset($args[0]) ? $args[0] : '<p class="evo_post_pagination">' . T_('Pages') . ': ';
            $params['after'] = isset($args[1]) ? $args[1] : '</p>';
            $params['separator'] = isset($args[2]) ? $args[2] : ' ';
            $params['single'] = isset($args[3]) ? $args[3] : '';
            $params['current_page'] = isset($args[4]) ? $args[4] : '#';
            $params['pagelink'] = isset($args[5]) ? $args[5] : '%d';
            $params['url'] = isset($args[6]) ? $args[6] : '';
            $params['format'] = 'htmlbody';
        }

        if ($preview && $this->pages > 1 && ! $this->ID) { // Do not render page links if it is a preview of a unsaved multipage item
            echo '';
        } else {
            echo $this->get_page_links($params, $params['format']);
        }
    }

    /**
     * Get a link to pages for multi-page items
     *
     * @param array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function get_page_links($params = [], $format = 'htmlbody')
    {
        $params = array_merge([
            'before' => '<p class="evo_post_pagination">' . T_('Pages') . ': ',
            'after' => '</p>',
            'separator' => ' ',
            'single' => '',
            'current_page' => '#',
            'pagelink' => '%d',
            'url' => '',
        ], $params);

        global $disp;

        // Make sure, the pages are split up:
        $this->split_pages();

        if ($this->pages <= 1) { // Single page:
            return $params['single'];
        }

        if ($params['separator'] == null) { // Don't display pages
            if ($params['before'] !== null) {
                return format_to_output($params['before'] . $params['after'], $format);
            }
            return;
        }

        if ($params['current_page'] == '#') {
            global $page;
            $params['current_page'] = $page;
        }

        if (empty($params['url'])) {
            $params['url'] = $this->get_permanent_url('', '', '&amp;');
        }

        $page_links = [];

        $page_prev_i = $params['current_page'] - 1;
        $page_next_i = $params['current_page'] + 1;
        for ($i = 1; $i <= $this->pages; $i++) {
            $text = str_replace('%d', $i, $params['pagelink']);

            if ($i != $params['current_page']) {
                $attr_rel = '';
                if ($disp == 'single' || $disp == 'page') { // Add rel="prev|next" only on single post view
                    if ($page_prev_i == $i) {
                        $attr_rel = ' rel="prev"';
                    } elseif ($page_next_i == $i) {
                        $attr_rel = ' rel="next"';
                    }
                }

                if ($i == 1) {	// First page special:
                    $page_links[] = '<a href="' . $params['url'] . '"' . $attr_rel . '>' . $text . '</a>';
                } else {
                    $page_links[] = '<a href="' . url_add_param($params['url'], 'page=' . $i) . '"' . $attr_rel . '>' . $text . '</a>';
                }
            } else {
                $page_links[] = $text;
            }
        }

        $r = $params['before'] . implode($params['separator'], $page_links) . $params['after'];

        return format_to_output($r, $format);
    }

    /**
     * Get an attached image tag with lightbox reference
     *
     * @private function
     *
     * @param object the attached image Link
     * @param array params
     * @return string the attached image tag
     */
    public function get_attached_image_tag($Link, $params)
    {
        // Make sure $link_to is set
        $link_to = isset($params['image_link_to']) ? $params['image_link_to'] : 'original';

        // Force url of image for link positions: 'teaserperm' & 'teaserlink'
        switch ($Link->get('position')) {
            case 'teaserlink':
                // Teaser-Ext Link
                if ($this->get('url') != '') { // Only when post field 'Link to url' is defined
                    $link_to = 'url';
                    break;
                }
                // If post url is empty then use this link position as 'teaserperm':

                // no break
            case 'teaserperm':
            case 'cover':
            case 'background':
                // Teaser-Permalink or Cover
                global $disp;
                if (isset($disp) && $disp == 'single') { // Force link to image url and use colorbox only when we already on permalink page
                    $link_to = 'original';
                } else { // Force link to permalink of this post
                    $link_to = 'single';
                }
                break;
        }

        if ($link_to == 'single' || $link_to == 'url') { // We're linking to the post (displayed on a single post page):
            if ($link_to == 'url' && $this->get('url') != '') { // Link to url from the post field 'Link to url'
                $link_to = $this->get('url');
            } else { // Link to Item url:
                $params = array_merge([
                    'target_blog' => '',
                    'post_navigation' => '',
                    'nav_target' => null,
                ], $params);

                $link_to = $this->get_item_url($params['target_blog'], $params['post_navigation'], $params['nav_target']);
            }
            $link_title = '#desc#';
            $link_rel = isset($params['image_link_rel']) ? $params['image_link_rel'] : '';
        } else { // We're linking to the original image, let lightbox (or clone) kick in:
            $link_title = (empty($params['image_link_title']) && ! isset($params['hide_image_link_title'])) ? '#desc#' : $params['image_link_title'];	// This title will be used by lightbox (colorbox for instance)
            $link_rel = isset($params['image_link_rel']) ? $params['image_link_rel'] : 'lightbox[p' . $this->ID . ']';	// Make one "gallery" per post.
        }

        if (empty($params['image_alt'])) {	// Override image alt text by current Item title only when it is not passed e.g. from inline/short tag `[image:123::Custom Alt Text]`:
            $params['image_alt'] = $this->get('title');
        }

        // Generate the IMG tag with all the alt, title and desc if available
        return $Link->get_tag(array_merge($params, [
            'image_link_to' => $link_to,   // can be URL, can be empty
            'image_link_title' => $link_title,
            'image_link_rel' => $link_rel,
        ]));
    }

    /**
     * Display the images linked to the current Item
     *
     * @param array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function images($params = [], $format = 'htmlbody')
    {
        echo $this->get_images($params, $format);
    }

    /**
     * Get block of images linked to the current Item
     *
     * @param array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function get_images($params = [], $format = 'htmlbody')
    {
        global $Plugins;

        $r = '';

        $params = array_merge([
            'before' => '<div>',
            'before_image' => '<div class="image_block">',
            'before_image_classes' => '', // Allow injecting additional classes into 'before image'
            'before_image_legend' => '<div class="image_legend">',
            'after_image_legend' => '</div>',
            'after_image' => '</div>',
            'after' => '</div>',
            'image_size' => 'fit-720x500',
            'image_size_x' => 1, // Use '2' to build 2x sized thumbnail that can be used for Retina display
            'image_sizes' => null, // Simplified "sizes=" attribute for browser to select correct size from "srcset=".
            // Must be set DIFFERENTLY depending on WIDGET/CONTAINER/SKIN LAYOUT. Each time we must estimate the size the image will have on screen.
            // Sample value: (max-width: 430px) 400px, (max-width: 670px) 640px, (max-width: 991px) 720px, (max-width: 1199px) 698px, 848px
            'image_link_to' => 'original', // Can be 'original' (image file URL), 'single' (this post), can be URL, can be EMPTY
            // In case of 'single' link:
            'target_blog' => '',
            'post_navigation' => '',
            'nav_target' => null,
            'before_gallery' => '<div class="bGallery">',
            'after_gallery' => '</div>',
            'gallery_image_size' => 'crop-80x80',
            'gallery_image_limit' => 1000,
            'gallery_colls' => 5,
            'gallery_order' => '', // 'ASC', 'DESC', 'RAND'
            'gallery_link_rel' => 'lightbox[p' . $this->ID . ']',
            'restrict_to_image_position' => 'teaser,teaserperm,teaserlink,aftermore',
            // 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'|'background',
            // '#teaser_all' => 'teaser,teaserperm,teaserlink',
            // '#cover_and_teaser_all' => 'cover,background,teaser,teaserperm,teaserlink'
            'limit' => 1000, // Max # of images displayed
            'placeholder' => '',		// HTML to be displayed if no image; possible codes: #folder_icon
            'data' => &$r,
            'get_rendered_attachments' => true,
            'links_sql_select' => '',
            'links_sql_orderby' => 'link_order',
        ], $params);

        if (! empty($params['before_image_classes'])) {	// Inject additional classes into 'before image':
            $params['before_image'] = update_html_tag_attribs($params['before_image'], [
                'class' => $params['before_image_classes'],
            ]);
        }

        // Get list of ALL attached files
        $links_params = [
            'sql_select_add' => $params['links_sql_select'],
            'sql_order_by' => $params['links_sql_orderby'],
        ];

        // Set image positions from possible predefined values:
        switch ($params['restrict_to_image_position']) {
            case '#teaser_all':
                $params['restrict_to_image_position'] = 'teaser,teaserperm,teaserlink';
                break;
            case '#cover_and_teaser_all':
                $params['restrict_to_image_position'] = 'cover,background,teaser,teaserperm,teaserlink';
                break;
        }

        if (empty($this->ID)) {	// Preview mode for new creating item:
            $tmp_object_ID = param('temp_link_owner_ID', 'integer', 0);
        } else {	// Normal mode for existing Item in DB:
            $tmp_object_ID = null;
        }

        // GET list of images to display:
        $LinkOwner = new LinkItem($this, $tmp_object_ID);
        if (! $LinkList = $LinkOwner->get_attachment_LinkList(1000, $params['restrict_to_image_position'], null, $links_params)) {	// No images match requested positions:
            // Display PLACEHOLDER:
            $placeholder_html = $params['placeholder'];
            switch ($placeholder_html) {
                case '#file_text_icon':
                    $placeholder_html = '<div class="evo_image_block evo_img_placeholder"><a href="$url$" class="evo_img_placeholder"><i class="fa fa-file-text-o"></i></a></div>';
                    break;
                case '#file_thumbnail_text_icon':
                    $placeholder_html = '<div class="evo_thumblist_placeholder" style="width:80px;height:80px"><a href="$url$"></a></div>';
                    break;
            }
            return str_replace('$url$', $this->get_item_url($params['target_blog'], $params['post_navigation'], $params['nav_target']), $placeholder_html);
        }

        // LOOP through images:
        $galleries = [];
        $image_counter = 0;
        $plugin_render_attachments = false;
        while ($image_counter < $params['limit'] && $Link = &$LinkList->get_next()) {
            if (! ($File = &$Link->get_File())) { // No File object:
                global $Debuglog;
                $log_message = sprintf('Link ID#%d of item #%d does not have a file object!', $Link->ID, $this->ID);
                if ($this->is_revision()) {	// Display the log message only for revision preview mode:
                    $r .= '<div class="red">' . $log_message . '</div>';
                }
                $Debuglog->add($log_message, ['error', 'files']);
                continue;
            }

            if (! $File->exists()) { // File doesn't exist:
                global $Debuglog;
                $log_message = sprintf('File linked to item #%d does not exist (%s)!', $this->ID, $File->get_full_path());
                if ($this->is_revision()) {	// Display the log message only for revision preview mode:
                    $r .= '<div class="red">' . $log_message . '</div>';
                }

                // Still generate the IMG tag but this should display a black thumbnail with the appropriate error message:
                $r .= $this->get_attached_image_tag($Link, $params);

                $image_counter++;

                $Debuglog->add($log_message, ['error', 'files']);
                continue;
            }

            $params['File'] = $File;
            $params['Link'] = $Link;
            $params['Item'] = $this;

            if ($File->is_dir() && $params['gallery_image_limit'] > 0) { // This is a directory/gallery:
                if (($gallery = $File->get_gallery($params)) != '') { // Got gallery code
                    $galleries[] = $gallery;
                }
                continue;
            }

            if (! $params['get_rendered_attachments']) { // Save $r to temp var in order not to get the rendered data from plugins
                $temp_r = $r;
            }

            $temp_params = $params;
            foreach ($params as $param_key => $param_value) {	// Pass all params by reference, in order to give possibility to modify them by plugin
                // So plugins can add some data before/after image tags (E.g. used by infodots plugin)
                $params[$param_key] = &$params[$param_key];
            }

            // Prepare params before rendering item attachment:
            $Plugins->trigger_event_first_true_with_params('PrepareForRenderItemAttachment', $params);

            if (count($Plugins->trigger_event_first_true('RenderItemAttachment', $params)) != 0) {	// This attachment has been rendered by a plugin (to $params['data']), Skip this from core rendering:
                if (! $params['get_rendered_attachments']) { // Restore $r value and mark this item has the rendered attachments
                    $r = $temp_r;
                    $plugin_render_attachments = true;
                }
                continue;
            }

            if (! $File->is_image()) {	// Skip anything that is not an image:
                //$r .= $this->attachment_files($params);
                continue;
            }

            // GENERATE the IMG tag with all the alt, title and desc if available:
            $r .= $this->get_attached_image_tag($Link, $params);

            $image_counter++;
            $params = $temp_params;
        }

        if (empty($r) && $plugin_render_attachments) { // This item doesn't contain the images but it has the rendered attachments by plugins
            $r .= 'plugin_render_attachments';
        }

        if (! empty($r)) {
            $r = $params['before'] . $r . $params['after'];

            // Character conversions
            $r = format_to_output($r, $format);
        }

        if (! empty($galleries)) { // Append galleries
            // sam2kb> It's done like that only until we figure out a better way to display galleries.

            /*
            sam2kb> TODO: use shortcode [gallery option1="value1" option2="value2"]
                'columns' - table columns
                'limit' - a number of images,
                'size' - selected/large image size
                'thumbsize' - thumbnails image size
                'order' - files order ASC/DESC/RAND
            */

            // Character conversions
            $r .= "\n" . format_to_output(implode("\n", $galleries), $format);
        }

        return $r;
    }

    /**
     * Get File of a first found image by positions
     *
     * @param array Parameters
     * @return object|null File
     */
    public function &get_image_File($params = [])
    {
        $params = array_merge([
            'position' => '#cover_and_teaser_all',
        ], $params);

        // Set image positions from possible predefined values:
        switch ($params['position']) {
            case '#teaser_all':
                $params['position'] = 'teaser,teaserperm,teaserlink';
                break;
            case '#cover_and_teaser_all':
                $params['position'] = 'cover,background,teaser,teaserperm,teaserlink';
                break;
        }

        $LinkOwner = new LinkItem($this);
        if (! ($LinkList = $LinkOwner->get_attachment_LinkList(1, $params['position'])) ||
            ! ($Link = &$LinkList->get_next())) {	// No image
            $r = null;
            return $r;
        }

        if (! ($File = &$Link->get_File())) {	// No File object
            global $Debuglog;
            $Debuglog->add(sprintf('Link ID#%d of item #%d does not have a file object!', $Link->ID, $this->ID), ['error', 'files']);
            $r = null;
            return $r;
        }

        if (! $File->exists()) {	// File doesn't exist
            global $Debuglog;
            $Debuglog->add(sprintf('File linked to item #%d does not exist (%s)!', $this->ID, $File->get_full_path()), ['error', 'files']);
            $r = null;
            return $r;
        }

        if (! $File->is_image()) {	// Skip anything that is not an image
            $r = null;
            return $r;
        }

        return $File;
    }

    /**
     * Get URL of a first found image by positions
     *
     * @param array Parameters
     * @return string|null Image URL or NULL if it doesn't exist
     */
    public function get_image_url($params = [])
    {
        $params = array_merge([
            'position' => '#cover_and_teaser_all',
            'size' => 'original',
        ], $params);

        if (! ($image_File = &$this->get_image_File($params))) {	// Wrong image file:
            return null;
        }

        // Get image URL for requested size:
        $img_attribs = $image_File->get_img_attribs($params['size']);

        return $img_attribs['src'];
    }

    /**
     * Get URL of a first cover image
     *
     * @param string Restrict to files/images linked to a specific position.
     *               Position can be 'cover'|'background'|'teaser'|'aftermore'|'inline'
     *               Use comma as separator
     * @return string|null cover URL or NULL if it doesn't exist
     */
    public function get_cover_image_url($position = 'cover')
    {
        return $this->get_image_url([
            'position' => $position,
        ]);
    }

    /**
     * Get CSS property for background with image of this Item
     *
     * @param array Params
     * @return string
     */
    public function get_background_image_css($params = [])
    {
        $params = array_merge([
            'position' => '#cover_and_teaser_all',
            'size' => 'fit-1280x720',
            'size_2x' => 'fit-2560x1440',
        ], $params);

        if (! ($image_File = &$this->get_image_File($params))) {	// Don't provide css for wrong image file:
            return '';
        }

        return $image_File->get_background_image_css($params);
    }

    /**
     * Get a number of images linked to the current Item
     *
     * @param string Restrict to files/images linked to a specific position.
     *               Position can be 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'|'background'
     *               Use comma as separator
     * @param integer Number of images
     */
    public function get_number_of_images($image_position = null)
    {
        // Get list of attached files
        $LinkOwner = new LinkItem($this);
        if (! $LinkList = $LinkOwner->get_attachment_LinkList(1000, $image_position)) {
            return 0;
        }

        return $LinkList->result_num_rows;
    }

    /**
     * Display the attachments/files linked to the current Item
     *
     * @param array Array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function files($params = [], $format = 'htmlbody')
    {
        echo $this->get_files($params, $format);
    }

    /**
     * Get block of attachments/files linked to the current Item
     *
     * @param array Array of params
     * @param string Output format, see {@link format_to_output()}
     * @return string HTML
     */
    public function get_files($params = [], $format = 'htmlbody')
    {
        global $Plugins;
        $params = array_merge([
            'before' => '<div class="item_attachments"><h3>' . T_('Attachments') . ':</h3><ul class="bFiles">',
            'before_attach' => '<li>',
            'before_attach_size' => '<span class="file_size">(',
            'after_attach_size' => ')</span>',
            'after_attach' => '</li>',
            'after' => '</ul></div>',
            // fp> TODO: we should only have one limit param. Or is there a good reason for having two?
            // sam2kb> It's needed only for flexibility, in the meantime if user attaches 200 files he expects to see all of them in skin, I think.
            'limit_attach' => 1000, // Max # of files displayed
            'limit' => 1000,
            // Optionally restrict to files/images linked to specific position: 'teaser'|'teaserperm'|'teaserlink'|'aftermore'|'inline'|'cover'|'background'
            'restrict_to_image_position' => 'cover,background,teaser,teaserperm,teaserlink,aftermore,attachment',
            'data' => '',
            'attach_format' => '$icon_link$ $file_link$ $file_size$ $file_desc$', // $icon_link$ $icon$ $file_link$ $file_size$ $file_desc$
            'file_link_format' => '$file_name$', // $icon$ $file_name$ $file_size$ $file_desc$
            'file_link_class' => '',
            'file_link_text' => 'filename', // 'filename' - Always display Filename, 'title' - Display Title if available
            'download_link_icon' => 'download',
            'download_link_title' => T_('Download file'),
            'display_download_icon' => true,
            'display_file_size' => true,
            'display_file_desc' => false,
            'before_file_desc' => '<span class="evo_file_description">',
            'after_file_desc' => '</span>',
        ], $params);

        // Get list of attached files
        $LinkOwner = new LinkItem($this);
        if (! $LinkList = $LinkOwner->get_attachment_LinkList($params['limit'], $params['restrict_to_image_position'])) {
            return '';
        }

        load_funcs('files/model/_file.funcs.php');

        $r = '';
        $i = 0;
        $r_file = [];
        /**
         * @var File
         */
        $File = null;
        while (($Link = &$LinkList->get_next()) && $params['limit_attach'] > $i) {
            if ($Link->get('position') != 'attachment') {	// Skip not "attachment" links:
                continue;
            }

            if (! ($File = &$Link->get_File())) { // No File object
                global $Debuglog;
                $log_message = sprintf('Link ID#%d of item #%d does not have a file object!', $Link->ID, $this->ID);
                if ($this->is_revision()) {	// Display the log message only for revision preview mode:
                    $r_file[$i] = $params['before_attach'] . '<div class="red">' . $log_message . '</div>' . $params['after_attach'];
                }
                $Debuglog->add($log_message, ['error', 'files']);
                continue;
            }

            if (! $File->exists()) { // File doesn't exist
                global $Debuglog;
                $log_message = sprintf('File linked to item #%d does not exist (%s)!', $this->ID, $File->get_full_path());
                if ($this->is_revision()) {	// Display the log message only for revision preview mode:
                    $r_file[$i] = $params['before_attach'] . '<div class="red">' . $log_message . '</div>' . $params['after_attach'];
                }
                $Debuglog->add($log_message, ['error', 'files']);
                continue;
            }

            $params['File'] = $File;
            $params['Item'] = $this;

            $temp_params = $params;
            foreach ($params as $param_key => $param_value) { // Pass all params by reference, in order to give possibility to modify them by plugin
                // So plugins can add some data before/after image tags (E.g. used by infodots plugin)
                $params[$param_key] = &$params[$param_key];
            }

            // Prepare params before rendering item attachment:
            $Plugins->trigger_event_first_true_with_params('PrepareForRenderItemAttachment', $params);

            if (count($Plugins->trigger_event_first_true('RenderItemAttachment', $params)) != 0) {	// This attachment has been rendered by a plugin (to $params['data']), Skip this from core rendering:
                continue;
            }

            if (! isset($params['image_attachment']) && $File->is_image()) { // Skip images (except those in the attachment position) because these are displayed inline already
                // fp> TODO: have a setting for each linked file to decide whether it should be displayed inline or as an attachment
                continue;
            } elseif ($File->is_dir()) { // Skip directories/galleries
                continue;
            }

            // A link to download a file:

            // Just icon with download icon:
            $icon = ($params['display_download_icon'] && $File->exists() && strpos($params['attach_format'] . $params['file_link_format'], '$icon$') !== false) ?
                    get_icon($params['download_link_icon'], 'imgtag', [
                        'title' => $params['download_link_title'],
                    ]) : '';

            // A link with icon to download:
            $icon_link = ($params['display_download_icon'] && $File->exists() && strpos($params['attach_format'], '$icon_link$') !== false) ?
                    action_icon($params['download_link_title'], $params['download_link_icon'], $Link->get_download_url(), '', 5) : '';

            // File size info:
            $file_size = ($params['display_file_size'] && $File->exists() && strpos($params['attach_format'] . $params['file_link_format'], '$file_size$') !== false) ?
                    $params['before_attach_size'] . bytesreadable($File->get_size(), false, false) . $params['after_attach_size'] : '';

            // File description:
            $file_desc = '';
            if ($params['display_file_desc'] && $File->exists() && strpos($params['attach_format'] . $params['file_link_format'], '$file_desc$') !== false) {	// If description should be displayed:
                $file_desc = nl2br(trim($File->get('desc')));
                if ($file_desc !== '') {	// If file has a filled description:
                    $params['before_file_desc'] . $file_desc . $params['after_file_desc'];
                }
            }

            // A link with file name or file title to download:
            $file_link_format = str_replace(
                ['$icon$', '$file_name$', '$file_size$'],
                [$icon, '$text$', $file_size],
                $params['file_link_format']
            );
            if ($params['file_link_text'] == 'filename' || trim($File->get('title')) === '') {	// Use file name for link text:
                $file_link_text = $File->get_name();
            } else {	// Use file title only if it filled:
                $file_link_text = $File->get('title');
            }
            if ($File->exists()) {	// Get file link to download if file exists:
                $file_download_url = $this->get_coll_setting('download_enable') ? $Link->get_download_url() : null;
                $file_link = (strpos($params['attach_format'], '$file_link$') !== false) ?
                        $File->get_view_link($file_link_text, null, null, $file_link_format, $params['file_link_class'], $file_download_url) : '';
            } else {	// File doesn't exist, We cannot display a link, Display only file name and warning:
                $file_link = (strpos($params['attach_format'], '$file_link$') !== false) ?
                        $file_link_text . ' - <span class="red nowrap">' . get_icon('warning_yellow') . ' ' . T_('Missing attachment!') . '</span>' : '';
            }

            $r_file[$i] = $params['before_attach'];
            $r_file[$i] .= str_replace(
                ['$icon$', '$icon_link$', '$file_link$', '$file_size$', '$file_desc$'],
                [$icon, $icon_link, $file_link, $file_size, $file_desc],
                $params['attach_format']
            );
            $r_file[$i] .= $params['after_attach'];

            $i++;
            $params = $temp_params;
        }

        if (! empty($r_file)) {
            $r = $params['before'] . implode("\n", $r_file) . $params['after'];

            // Character conversions
            $r = format_to_output($r, $format);
        }

        return $r;
    }

    /**
     * Get array of the Files that are used as "Fallback" for the selected File
     *
     * @param object File
     * @return array Fallback Files
     */
    public function get_fallback_files($File)
    {
        $fallback_files = [];

        if (empty($File)) { // No File for fallbacks
            return $fallback_files;
        }

        if (! isset($this->fallback_FileList)) { // Get list of attached fallback files
            $LinkOwner = new LinkItem($this);
            if (! $this->fallback_FileList = $LinkOwner->get_attachment_FileList(1000, 'fallback')) { // No fallback files
                return $fallback_files;
            }
        }

        // Get file name without extension
        $file_name_without_ext = preg_replace('#^(.+)\.[^\.]+$#', '$1', $File->get_name());

        // Rewind internal index to first position
        $this->fallback_FileList->current_idx = 0;

        while ($fallback_File = &$this->fallback_FileList->get_next()) {
            if ($File->get_name() != $fallback_File->get_name() &&
                preg_match('#^' . $file_name_without_ext . '\.[^\.]+$#', $fallback_File->get_name())) { // Fallback is a file with same name but with different extension
                $fallback_files[] = $fallback_File;
            }
        }

        return $fallback_files;
    }

    /**
     * Get placeholder image File that has the same name as the current video File
     *
     * @param object File
     * @return object placeholder File
     */
    public function &get_placeholder_File($video_File)
    {
        $r = null;

        if (empty($video_File)) { // No File for placeholder
            return $r;
        }

        if (! isset($this->placeholder_FileList)) { // Get list of attached fallback files
            $LinkOwner = new LinkItem($this);
            $attachment_FileList = $LinkOwner->get_attachment_FileList(1000);
            if (! $this->placeholder_FileList = &$attachment_FileList) { // No attached files
                return $r;
            }
        }

        // Get file name without extension
        $video_file_name_without_ext = preg_replace('#^(.+)\.[^\.]+$#', '$1', $video_File->get_name());

        // Rewind internal index to first position
        $this->placeholder_FileList->current_idx = 0;

        while ($attached_File = &$this->placeholder_FileList->get_next()) {
            if ($video_File->get_name() != $attached_File->get_name() &&
                preg_match('#^' . $video_file_name_without_ext . '\.(jpg|jpeg|png|gif|webp)+$#', $attached_File->get_name())) { // It is a file with same name but with image extension
                return $attached_File;
            }
        }

        return $r;
    }

    /**
     * @param array Associative array of parameters
     * @return string Output
     */
    public function attachment_files(&$params/* = array()*/)
    {
        global $Plugins;

        $r = '';

        $ItemAttachment_plugins = $Plugins->get_list_by_event('RenderItemAttachment');

        $params['Item'] = $this;

        $temp_params = $params;
        foreach ($params as $param_key => $param_value) { // Pass all params by reference, in order to give possibility to modify them by plugin
            // So plugins can add some data before/after image tags (E.g. used by infodots plugin)
            $params[$param_key] = &$params[$param_key];
        }

        $Plugins->trigger_event_first_true('RenderItemAttachment', $params);

        $params = $temp_params;

        return $r;
    }

    /**
     * Template function: Displays link to the feed for comments on this item
     *
     * @param string Type of feedback to link to (rss2/atom)
     * @param string String to display before the link (if comments are to be displayed)
     * @param string String to display after the link (if comments are to be displayed)
     * @param string Link title
     */
    public function feedback_feed_link($skin = '_rss2', $before = '', $after = '', $title = '#')
    {
        if (! $this->can_see_comments()) {	// Comments disabled
            return;
        }

        $this->load_Blog();

        if ($this->Blog->get_setting('comment_feed_content') == 'none') {	// Comment feeds disabled
            return;
        }

        if (! ($url = $this->get_feedback_feed_url($skin))) {	// Don't display feed link when no feed skin is installed in system:
            return;
        }

        if ($title == '#') {
            $title = get_icon('feed') . ' ' . T_('Comment feed for this post');
        }

        echo $before;
        echo '<a href="' . $url . '">' . format_to_output($title) . '</a>';
        echo $after;
    }

    /**
     * Get URL to display the post comments in an XML feed.
     *
     * @param string Skin folder name
     * @return string|false URL or FALSE if none feed skin is not installed in system
     */
    public function get_feedback_feed_url($skin_folder_name)
    {
        $item_Blog = &$this->get_Blog();
        $comment_feed_url = $item_Blog->get_comment_feed_url($skin_folder_name);
        return ($comment_feed_url ? url_add_param($comment_feed_url, 'p=' . $this->ID) : false);
    }

    /**
     * Get URL to display the post comments.
     *
     * @return string
     */
    public function get_feedback_url($popup = false, $glue = '&amp;', $blog_ID = null)
    {
        if ($blog_ID !== null &&
                ($BlogCache = &get_BlogCache()) &&
                ($Blog = &$BlogCache->get_by_ID($blog_ID, false, false))) {
            $blog_url = $Blog->get('url');
        } else {
            $blog_url = '';
        }

        $url = $this->get_single_url('auto', $blog_url, $glue, $blog_ID);
        if ($popup) {
            $url = url_add_param($url, 'disp=feedback-popup', $glue);
        }

        return $url;
    }

    /**
     * Template function: Displays link to feedback page (under some conditions)
     *
     * @param array
     */
    public function feedback_link($params)
    {
        echo $this->get_feedback_link($params);
    }

    /**
     * Get a link to feedback page (under some conditions)
     *
     * @param array
     */
    public function get_feedback_link($params = [])
    {
        global $ReqURL, $Blog, $Settings;

        if (! $this->can_see_comments()) {	// Comments disabled
            return;
        }

        $params = array_merge([
            'type' => 'feedbacks',		// Kind of feedbacks to count
            'status' => '#',	// Statuses of feedbacks to count, can be a string for one status or array for several. '#' - active front-office comment statuses, '#moderation#' - "require moderation" statuses.
            'link_before' => '',
            'link_after' => '',
            'link_text_zero' => '#',
            'link_text_one' => '#',
            'link_text_more' => '#',
            'link_anchor_zero' => '#',
            'link_anchor_one' => '#',
            'link_anchor_more' => '#',
            'link_title' => '#',
            'link_class' => '',
            'show_in_single_mode' => false,		// Do we want to show this link even if we are viewing the current post in single view mode
            'url' => '#',
            'stay_in_same_collection' => 'auto', // 'auto' - follow 'allow_crosspost_urls' if we are cross posted, true - always stay in same collection if we are cross posted, false - always go to permalink if we are cross posted
        ], $params);

        if ($params['show_in_single_mode'] == false && is_single_page($this->ID)) {	// We are viewing the single page for this Item, which (typically) contains comments, so we don't want to display this link
            return;
        }

        if (isset($Blog) &&
            (
                $params['stay_in_same_collection'] === true || // always stay in current collection
              ($params['stay_in_same_collection'] == 'auto' && ($item_Blog = &$this->get_Blog()) && $item_Blog->get_setting('allow_crosspost_urls')) // follow 'allow_crosspost_urls' to stay in current collection
            )) {	// Use current collection if this Item is cross posted and has at least one category from current collection:
            $current_blog_ID = $Blog->ID;
        } else {	// Use main collection of this Item:
            $current_blog_ID = null;
        }

        // dh> TODO:	Add plugin hook, where a Pingback plugin could hook and provide "pingbacks"
        switch ($params['type']) {
            case 'feedbacks':
                if ($params['link_title'] == '#') {
                    $params['link_title'] = T_('Display feedback / Leave a comment');
                }
                if ($params['link_text_zero'] == '#') {
                    $params['link_text_zero'] = T_('Send feedback') . ' &raquo;';
                }
                if ($params['link_text_one'] == '#') {
                    $params['link_text_one'] = T_('1 feedback') . ' &raquo;';
                }
                if ($params['link_text_more'] == '#') {
                    $params['link_text_more'] = T_('%d feedbacks') . ' &raquo;';
                }
                break;

            case 'comments':
                if ($params['link_title'] == '#') {
                    $params['link_title'] = T_('Display comments / Leave a comment');
                }
                if ($params['link_text_zero'] == '#') {
                    if ($this->can_comment(null)) { // NULL, because we do not want to display errors here!
                        $params['link_text_zero'] = T_('Leave a comment') . ' &raquo;';
                    } else {
                        $params['link_text_zero'] = '';
                    }
                }
                if ($params['link_text_one'] == '#') {
                    $params['link_text_one'] = T_('1 comment') . ' &raquo;';
                }
                if ($params['link_text_more'] == '#') {
                    $params['link_text_more'] = T_('%d comments') . ' &raquo;';
                }
                break;

            case 'trackbacks':
                $this->get_Blog();
                if (! $this->can_receive_pings()) { // Trackbacks not allowed on this blog:
                    return;
                }
                if ($params['link_title'] == '#') {
                    $params['link_title'] = T_('Display trackbacks / Get trackback address for this post');
                }
                if ($params['link_text_zero'] == '#') {
                    $params['link_text_zero'] = T_('Send a trackback') . ' &raquo;';
                }
                if ($params['link_text_one'] == '#') {
                    $params['link_text_one'] = T_('1 trackback') . ' &raquo;';
                }
                if ($params['link_text_more'] == '#') {
                    $params['link_text_more'] = T_('%d trackbacks') . ' &raquo;';
                }
                break;

            case 'pingbacks':
                // Obsolete, but left for skin compatibility
                $this->get_Blog();
                if (! $this->can_receive_pings()) { // Trackbacks not allowed on this blog:
                    // We'll consider pingbacks to follow the same restriction
                    return;
                }
                if ($params['link_title'] == '#') {
                    $params['link_title'] = T_('Display pingbacks');
                }
                if ($params['link_text_zero'] == '#') {
                    $params['link_text_zero'] = T_('No pingback yet') . ' &raquo;';
                }
                if ($params['link_text_one'] == '#') {
                    $params['link_text_one'] = T_('1 pingback') . ' &raquo;';
                }
                if ($params['link_text_more'] == '#') {
                    $params['link_text_more'] = T_('%d pingbacks') . ' &raquo;';
                }
                break;

            case 'webmentions':
                if (! $this->can_receive_webmentions()) { // Webmentions not allowed on this collection:
                    // We'll consider webmentions to follow the same restriction
                    return;
                }
                if ($params['link_title'] == '#') {
                    $params['link_title'] = T_('Display webmentions');
                }
                if ($params['link_text_zero'] == '#') {
                    $params['link_text_zero'] = T_('No webmention yet') . ' &raquo;';
                }
                if ($params['link_text_one'] == '#') {
                    $params['link_text_one'] = T_('1 webmention') . ' &raquo;';
                }
                if ($params['link_text_more'] == '#') {
                    $params['link_text_more'] = T_('%d webmentions') . ' &raquo;';
                }
                break;

            default:
                debug_die("Unknown feedback type [{$params['type']}]");
        }

        $link_text = $this->get_feedback_title($params['type'], $params['link_text_zero'], $params['link_text_one'], $params['link_text_more'], $params['status']);

        if (empty($link_text)) {	// No link, no display...
            return false;
        }

        if ($params['url'] == '#') { // We want a link to single post:
            $params['url'] = $this->get_feedback_url(false, '&amp;', $current_blog_ID);
        }

        // Anchor position
        $number = generic_ctp_number($this->ID, $params['type'], $params['status']);

        if ($number == 0) {
            $anchor = $params['link_anchor_zero'];
        } elseif ($number == 1) {
            $anchor = $params['link_anchor_one'];
        } elseif ($number > 1) {
            $anchor = $params['link_anchor_more'];
        }
        if ($anchor == '#') {
            $anchor = '#' . $params['type'];
        }

        $r = $params['link_before'];

        if (! empty($params['url'])) {
            $r .= '<a href="' . $params['url'] . $anchor . '" class="' . format_to_output($params['link_class'], 'htmlattr') . '" ';	// Position on feedback
            $r .= 'title="' . format_to_output($params['link_title'], 'htmlattr') . '"';
            $r .= '>';
            $r .= $link_text;
            $r .= '</a>';
        } else {
            $r .= $link_text;
        }

        $r .= $params['link_after'];

        return $r;
    }

    /**
     * Return true if there is any feedback of given type.
     *
     * @param array
     * @return boolean
     */
    public function has_feedback($params)
    {
        $params = array_merge([
            'type' => 'feedbacks',
            'status' => 'published',
        ], $params);

        // Check is a given type is allowed
        switch ($params['type']) {
            case 'feedbacks':
            case 'comments':
            case 'trackbacks':
            case 'pingbacks':
            case 'webmentions':
                break;
            default:
                debug_die("Unknown feedback type [{$params['type']}]");
        }

        $number = generic_ctp_number($this->ID, $params['type'], $params['status']);

        return $number > 0;
    }

    /**
     * Return true if trackbacks and pingbacks are allowed
     *
     * @return boolean
     */
    public function can_receive_pings()
    {
        $this->load_Blog();
        return $this->Blog->get('allowtrackbacks') && $this->can_comment(null);
    }

    /**
     * Return true if webmentions are allowed
     *
     * @return boolean
     */
    public function can_receive_webmentions()
    {
        $this->load_Blog();
        return $this->Blog->get_setting('webmentions') && $this->can_comment(null);
    }

    /**
     * Get text depending on number of comments
     *
     * @param string Type of feedback to link to (feedbacks (all)/comments/trackbacks/pingbacks/webmentions)
     * @param string Link text to display when there are 0 comments
     * @param string Link text to display when there is 1 comment
     * @param string Link text to display when there are >1 comments (include %d for # of comments)
     * @param string|array Statuses of feedbacks to count, a string for one status, an array for several statuses,
     *                     '#' - to use currently active front-office comment statuses of the item's collection
     *                     '#moderation#' - to use all comment statuses which require moderation on front-office for the item's collection
     */
    public function get_feedback_title($type = 'feedbacks', $zero = '#', $one = '#', $more = '#', $statuses = '#', $filter_by_perm = true)
    {
        if (! $this->can_see_comments()) {	// Comments disabled
            return null;
        }

        // dh> TODO:	Add plugin hook, where a Pingback plugin could hook and provide "pingbacks"
        switch ($type) {
            case 'feedbacks':
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 feedback');
                }
                if ($more == '#') {
                    $more = T_('%d feedbacks');
                }
                break;

            case 'comments':
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 comment');
                }
                if ($more == '#') {
                    $more = T_('%d comments');
                }
                break;

            case 'trackbacks':
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 trackback');
                }
                if ($more == '#') {
                    $more = T_('%d trackbacks');
                }
                break;

            case 'pingbacks':
                // Obsolete, but left for skin compatibility
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 pingback');
                }
                if ($more == '#') {
                    $more = T_('%d pingbacks');
                }
                break;

            case 'metas':
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 internal comment');
                }
                if ($more == '#') {
                    $more = T_('%d internal comments');
                }
                break;

            case 'webmentions':
                if ($zero == '#') {
                    $zero = '';
                }
                if ($one == '#') {
                    $one = T_('1 webmention');
                }
                if ($more == '#') {
                    $more = T_('%d webmentions');
                }
                break;

            default:
                debug_die("Unknown feedback type [$type]");
        }

        if ($statuses == '#') {	// Get all comment statuses which are actived on front-office for the item's collection:
            $this->load_Blog();
            $statuses = explode(',', $this->Blog->get_setting('comment_inskin_statuses'));
        } elseif ($statuses == '#moderation#') {	// Get all comment statuses which require moderation on front-office for the item's collection:
            $this->load_Blog();
            $statuses = explode(',', $this->Blog->get_setting('moderation_statuses'));
        }

        $number = generic_ctp_number($this->ID, $type, $statuses, false, $filter_by_perm);
        if (! $filter_by_perm) { // This is the case when we are only counting comments awaiting moderation, return only not visible feedbacks number
            // count feedbacks with the same statuses where user has permission
            $visible_number = generic_ctp_number($this->ID, $type, $statuses, false, true);
            $number = $number - $visible_number;
        }

        if ($number == 0) {
            return $zero;
        } elseif ($number == 1) {
            return $one;
        } elseif ($number > 1) {
            return str_replace('%d', $number, $more);
        }
    }

    /**
     * Get table from ratings data
     *
     * @param array ratings data
     * @param array params
     */
    public function get_rating_table($ratings, $params)
    {
        $ratings_count = $ratings['all_ratings'];
        $average_real = ($ratings_count > 0) ? number_format($ratings["summary"] / $ratings_count, 1, ".", "") : 0;
        $average = ceil(($average_real) / 5 * 100);

        $table = '<table class="rating_summary" cellspacing="1">';
        foreach ($ratings as $r => $count) {	// Print a row for each star with formed data
            if (! is_int($r)) {
                continue;
            }

            $star_average = ($ratings_count > 0) ? ceil(($count / $ratings_count) * 100) : 0;
            switch ($params['rating_summary_star_totals']) {
                case 'count':
                    $star_value = '(' . $count . ')';
                    break;
                case 'percent':
                    $star_value = '(' . $star_average . '%)';
                    break;
                case 'none':
                default:
                    $star_value = "";
                    break;
            }
            $table .= '<tr><th>' . $r . ' ' . T_('star') . ':</th>
				<td class="progress"><div style="width:' . $star_average . '%">&nbsp;</div></td>
				<td>' . $star_value . '</td><tr>';
        }
        $table .= '</table>';

        $table .= '<div class="rating_summary_total">
			' . $ratings_count . ' ' . ($ratings_count > 1 ? T_('ratings') : T_('rating')) . '
			<div class="average_rating">' . T_('Average user rating') . ':<br />
			' . get_star_rating($average_real) . '<span class="average_rating_score">(' . $average_real . ')</span>
			</div></div><div class="clear"></div>';

        return $table;
    }

    /**
     * Get table with rating summary
     *
     * @param array of params
     */
    public function get_rating_summary($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'rating_summary_star_totals' => 'count', // Possible values: 'count', 'percent' and 'none'
        ], $params);

        $item_Blog = &$this->get_Blog();
        if (! $item_Blog->get_setting('display_rating_summary')) { // Don't display a rating summary
            return;
        }

        // get ratings and active ratings ( active ratings are younger then comment_expiry_delay )
        list($ratings, $active_ratings) = $this->get_ratings();
        $ratings_count = $ratings['all_ratings'];
        $active_ratings_count = $active_ratings['all_ratings'];
        if ($ratings_count == 0) { // No Comments
            return;
        }

        $average_real = number_format($ratings["summary"] / $ratings_count, 1, ".", "");
        $active_average_real = ($active_ratings_count == 0) ? 0 : (number_format($active_ratings["summary"] / $active_ratings_count, 1, ".", ""));

        $result = '';
        $expiry_delay = $this->get_setting('comment_expiry_delay');
        if (empty($expiry_delay)) {
            $all_ratings_title = T_('User ratings');
        } else {
            $all_ratings_title = T_('Overall user ratings');
            $result .= '<div class="ratings_table">';
            $result .= '<div><strong>' . get_duration_title($expiry_delay) . '</strong></div>';
            $result .= $this->get_rating_table($active_ratings, $params);
            $result .= '</div>';
        }

        $result .= '<div class="ratings_table">';
        $result .= '<div><strong>' . $all_ratings_title . '</strong></div>';
        $result .= $this->get_rating_table($ratings, $params);
        $result .= '</div>';

        return $result;
    }

    /**
     * Template function: Displays feeback moderation info
     *
     * @param string Type of feedback to link to (feedbacks (all)/comments/trackbacks/pingbacks/webmentions)
     * @param string String to display before the link (if comments are to be displayed)
     * @param string String to display after the link (if comments are to be displayed)
     * @param string Link text to display when there are 0 comments
     * @param string Link text to display when there is 1 comment
     * @param string Link text to display when there are >1 comments (include %d for # of comments)
     * @param string Link
     * @param boolean true to hide if no feedback
     */
    public function feedback_moderation(
        $type = 'feedbacks',
        $before = '',
        $after = '',
        $zero = '',
        $one = '#',
        $more = '#',
        $edit_comments_link = '#',
        $params = []
    ) {
        /**
         * @var User
         */
        global $current_User;

        /* TODO: finish this...
        $params = array_merge( array(
                                    'type' => 'feedbacks',
                                    'block_before' => '',
                                    'blo_after' => '',
                                    'link_text_zero' => '#',
                                    'link_text_one' => '#',
                                    'link_text_more' => '#',
                                    'link_title' => '#',
                                    'url' => '#',
                                    'type' => 'feedbacks',
                                ), $params );
        */

        if (isset($current_User) && check_user_perm('blog_comment!draft', 'moderate', false, $this->get_blog_ID())) {	// We have permission to edit comments:
            if ($edit_comments_link == '#') {	// Use default link:
                global $admin_url;
                $edit_comments_link = '<a href="' . $admin_url . '?ctrl=items&amp;blog=' . $this->get_blog_ID() . '&amp;p=' . $this->ID . '#comments" title="' . T_('Moderate these feedbacks') . '">' . get_icon('edit') . ' ' . T_('Moderate') . '...</a>';
            }
        } else { // User has no right to edit comments:
            $edit_comments_link = '';
        }

        // Inject Edit/moderate link as relevant:
        $zero = str_replace('%s', $edit_comments_link, $zero);
        $one = str_replace('%s', $edit_comments_link, $one);
        $more = str_replace('%s', $edit_comments_link, $more);

        $r = $this->get_feedback_title($type, $zero, $one, $more, '#moderation#', false);

        if (! empty($r)) {
            echo $before . $r . $after;
        }
    }

    /**
     * Template tag: display footer for the current Item.
     *
     * @param array
     * @return boolean true if something has been displayed
     */
    public function footer($params)
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'mode' => '#',				// Will detect 'single' from $disp automatically
            'block_start' => '<div class="item_footer">',
            'block_end' => '</div>',
            'format' => 'htmlbody',
        ], $params);

        if ($params['mode'] == '#') {
            global $disp;
            $params['mode'] = $disp;
        }

        // pre_dump( $params['mode'] );

        $this->get_Blog();
        switch ($params['mode']) {
            case 'xml':
                $text = $this->Blog->get_setting('xml_item_footer_text');
                break;

            case 'single':
                $text = $this->Blog->get_setting('single_item_footer_text');
                break;

            default:
                // Do NOT display!
                $text = '';
        }

        $text = preg_replace_callback('#\$([a-z_]+)\$#', [$this, 'replace_callback'], $text);

        if (empty($text)) {
            return false;
        }

        echo format_to_output($params['block_start'] . $text . $params['block_end'], $params['format']);

        return true;
    }

    /**
     * Gets button for deleting the Item if user has proper rights
     *
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @param boolean true to make this a button instead of a link
     * @param string page url for the delete action
     * @param string confirmation text
     */
    public function get_delete_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $button = false, $actionurl = '#', $confirm_text = '#', $redirect_to = '')
    {
        global $admin_url;

        if (! check_user_perm('item_post!CURSTATUS', 'delete', false, $this, false)) {	// User has no rights to delete this Item:
            return false;
        }

        if ($text == '#') {
            if (! $button) {
                $text = get_icon('delete', 'imgtag') . ' ' . T_('Delete!');
            } else {
                $text = T_('Delete!');
            }
        }

        if ($title == '#') {
            $title = T_('Delete this post');
        }

        if ($actionurl == '#') {
            $actionurl = $admin_url . '?ctrl=items&amp;action=delete&amp;post_ID=';
        }
        $url = $actionurl . $this->ID . '&amp;' . url_crumb('item');

        if (! empty($redirect_to)) {
            $url = $url . '&amp;redirect_to=' . rawurlencode($redirect_to);
        }

        if ($confirm_text == '#') {
            $confirm_text = TS_('You are about to delete this post!\\nThis cannot be undone!');
        }

        $r = $before;
        if ($button) { // Display as button
            $r .= '<input type="button"';
            $r .= ' value="' . $text . '" title="' . $title . '" onclick="if ( confirm(\'';
            $r .= $confirm_text;
            $r .= '\') ) { document.location.href=\'' . $url . '\' }"';
            if (! empty($class)) {
                $r .= ' class="' . $class . '"';
            }
            $r .= '/>';
        } else { // Display as link
            $r .= '<a href="' . $url . '" title="' . $title . '" onclick="return confirm(\'';
            $r .= $confirm_text;
            $r .= '\')"';
            if (! empty($class)) {
                $r .= ' class="' . $class . '"';
            }
            $r .= '>' . $text . '</a>';
        }
        $r .= $after;

        return $r;
    }

    /**
     * Displays button for deleting the Item if user has proper rights
     *
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @param boolean true to make this a button instead of a link
     * @param string page url for the delete action
     * @param string confirmation text
     */
    public function delete_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $button = false, $actionurl = '#', $confirm_text = '#', $redirect_to = '')
    {
        echo $this->get_delete_link($before, $after, $text, $title, $class, $button, $actionurl, $confirm_text, $redirect_to);
    }

    /**
     * Provide link to copy a post if user has edit rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     *  - 'save_context': redirect to current URL?
     */
    public function get_copy_link($params = [])
    {
        global $admin_url;

        $actionurl = $this->get_copy_url($params);
        if (! $actionurl) {
            return false;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#', // '#' - icon + text, '#icon#' - only icon, '#text#' - only text
            'title' => '#',
            'class' => '',
            'save_context' => true,
        ], $params);

        switch ($params['text']) {
            case '#':
                $params['text'] = get_icon('copy', 'imgtag', [
                    'title' => T_('Duplicate this post...'),
                ]) . ' ' . T_('Duplicate...');
                break;

            case '#icon#':
                $params['text'] = get_icon('copy', 'imgtag', [
                    'title' => T_('Duplicate this post...'),
                ]);
                break;

            case '#text#':
                $params['text'] = T_('Duplicate...');
                break;
        }

        if ($params['title'] == '#') {
            $params['title'] = T_('Duplicate this post...');
        }

        $r = $params['before'];
        $r .= '<a href="' . $actionurl;
        $r .= '" title="' . $params['title'] . '"';
        if (! empty($params['class'])) {
            $r .= ' class="' . $params['class'] . '"';
        }
        $r .= '>' . $params['text'] . '</a>';
        $r .= $params['after'];

        return $r;
    }

    /**
     * Get URL to copy a post if user has edit rights.
     *
     * @param array Params:
     *  - 'save_context': redirect to current URL?
     */
    public function get_copy_url($params = [])
    {
        global $admin_url;

        if (! is_logged_in(false)) {
            return false;
        }

        if (! $this->ID) { // preview..
            return false;
        }

        $this->load_Blog();
        $write_item_url = $this->Blog->get_write_item_url();
        if (empty($write_item_url)) { // User has no right to copy this post
            return false;
        }

        // default params
        $params += [
            'save_context' => true,
        ];

        $url = false;
        if ($this->Blog->get_setting('in_skin_editing') && ! is_admin_page()) {	// We have a mode 'In-skin editing' for the current Blog
            if (check_item_perm_edit(0, false)) {	// Current user can copy this post from Front-office
                $url = url_add_param($this->Blog->get('url'), 'disp=edit&cp=' . $this->ID);
            } elseif (check_user_perm('admin', 'restricted')) {	// Current user can copy this post from Back-office
                $url = $admin_url . '?ctrl=items&amp;action=copy&amp;blog=' . $this->Blog->ID . '&amp;p=' . $this->ID;
            }
        } elseif (check_user_perm('admin', 'restricted')) {	// Copy a post from Back-office
            $url = $admin_url . '?ctrl=items&amp;action=copy&amp;blog=' . $this->Blog->ID . '&amp;p=' . $this->ID;
            if ($params['save_context']) {
                $url .= '&amp;redirect_to=' . rawurlencode(regenerate_url('', '', '', '&') . '#' . $this->get_anchor_id());
            }
        }
        return $url;
    }

    /**
     * Template tag
     * @see Item::get_copy_link()
     */
    public function copy_link($params = [])
    {
        echo $this->get_copy_link($params);
    }

    /**
     * Provide a link to add a new version of this post if user has rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     * @return string
     */
    public function get_add_version_link($params = [])
    {
        if (! $this->can_link_version(true)) {	// New item version cannot be added by some restriction:
            return false;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'text' => '#text#', // '#' - icon + text, '#icon#' - only icon, '#text#' - only text
            'title' => '#', // '#' - Add version...
            'class' => '',
        ], $params);

        switch ($params['text']) {
            case '#text#':
                $params['text'] = T_('Add version') . '...';
                break;

            case '#':
                $params['text'] = get_icon('add', 'imgtag', [
                    'title' => T_('Add version') . '...',
                ]) . ' ' . T_('Add version') . '...';
                break;

            case '#icon#':
                $params['text'] = get_icon('add', 'imgtag', [
                    'title' => T_('Add version') . '...',
                ]);
                break;
        }

        if ($params['title'] == '#') {
            $params['title'] = T_('Add version') . '...';
        }

        $r = $params['before'];

        $r .= '<a href="#" onclick="return evo_add_version_load_window( ' . $this->ID . ' )"'
                . 'title="' . format_to_output($params['title'], 'htmlattr') . '"'
                . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"')
            . '>' . format_to_output($params['text'], 'htmlbody') . '</a>';

        $r .= $params['after'];

        return $r;
    }

    /**
     * Provide a link to link a new version of this post if user has rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     * @return string
     */
    public function get_link_version_link($params = [])
    {
        if (! $this->can_link_version(true)) {	// New item version cannot be linked by some restriction:
            return false;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'text' => '#text#', // '#' - icon + text, '#icon#' - only icon, '#text#' - only text
            'title' => '#', // '#' - Link version...
            'class' => '',
        ], $params);

        switch ($params['text']) {
            case '#text#':
                $params['text'] = T_('Link version') . '...';
                break;

            case '#':
                $params['text'] = get_icon('link', 'imgtag', [
                    'title' => T_('Link version') . '...',
                ]) . ' ' . T_('Link version') . '...';
                break;

            case '#icon#':
                $params['text'] = get_icon('link', 'imgtag', [
                    'title' => T_('Link version') . '...',
                ]);
                break;
        }

        if ($params['title'] == '#') {
            $params['title'] = T_('Link version') . '...';
        }

        $r = $params['before'];

        $item_Blog = &$this->get_Blog();
        $r .= '<a href="#" onclick="return evo_link_version_load_window( ' . $this->ID . ', \'' . $item_Blog->get('urlname') . '\' )"'
                . 'title="' . format_to_output($params['title'], 'htmlattr') . '"'
                . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"')
            . '>' . format_to_output($params['text'], 'htmlbody') . '</a>';

        $r .= $params['after'];

        return $r;
    }

    /**
     * Get URL to unlink a post if user has a permission
     *
     * @param array Params:
     *  - 'unlink_item_ID': What item to unlink, NULL - to unlink this Item
     */
    public function get_unlink_version_url($params = [])
    {
        global $admin_url;

        if (! $this->can_link_version()) {	// Item version cannot be unlinked by some restriction:
            return false;
        }

        // Default params:
        $params = array_merge([
            'unlink_item_ID' => null,
        ], $params);

        if ($params['unlink_item_ID'] !== null) {
            $ItemCache = &get_ItemCache();
            if (! ($unlink_item = &$ItemCache->get_by_ID($params['unlink_item_ID'], false, false)) ||
                $unlink_item->get('igrp_ID') != $this->get('igrp_ID')) {	// If the requested Item to unlink is from different group:
                return false;
            }
        }

        return $admin_url . '?ctrl=items&amp;action=unlink_version&amp;blog=' . $this->Blog->ID
            . '&amp;post_ID=' . $this->ID
            . (empty($unlink_item) ? '' : '&amp;unlink_item_ID=' . $unlink_item->ID)
            . '&amp;' . url_crumb('item');
    }

    /**
     * Provide a link to unlink a new version of this post if user has rights
     *
     * @param array Params:
     *  - 'unlink_item_ID': What item to unlink, NULL - to unlink this Item
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     * @return string
     */
    public function get_unlink_version_link($params = [])
    {
        if (! ($unlink_version_url = $this->get_unlink_version_url($params))) {	// Unlink action is not allowed for current User and this Item
            return false;
        }

        // Default params:
        $params = array_merge([
            'unlink_item_ID' => null,
            'before' => ' ',
            'after' => ' ',
            'text' => '#icon#', // '#' - icon + text, '#icon#' - only icon, '#text#' - only text
            'title' => '#', // '#' - Unlink version...
            'class' => '',
        ], $params);

        switch ($params['text']) {
            case '#text#':
                $params['text'] = T_('Unlink version') . '...';
                break;

            case '#':
                $params['text'] = get_icon('unlink', 'imgtag', [
                    'title' => T_('Unlink version') . '...',
                ]) . ' ' . T_('Link version') . '...';
                break;

            case '#icon#':
                $params['text'] = get_icon('unlink', 'imgtag', [
                    'title' => T_('Unlink version') . '...',
                ]);
                break;
        }

        if ($params['title'] == '#') {
            $params['title'] = T_('Unlink version') . '...';
        }

        $ItemCache = &get_ItemCache();
        if (! ($unlink_item = &$ItemCache->get_by_ID($params['unlink_item_ID'], false, false)) ||
            $unlink_item->get('igrp_ID') != $this->get('igrp_ID')) {	// Use current Item if the requested Item doesn't exist or it is from another group:
            $unlink_item = $this;
        }

        $r = $params['before'];

        $item_Blog = &$this->get_Blog();
        $r .= '<a href="' . $unlink_version_url . '" '
                . 'onclick="return confirm( \'' . format_to_output(sprintf(TS_('Are you sure want to unlink the Item "%s" (%s)?'), $unlink_item->get('title'), $unlink_item->get('locale')), 'htmlattr') . '\' )"'
                . 'title="' . format_to_output($params['title'], 'htmlattr') . '"'
                . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"')
            . '>' . format_to_output($params['text'], 'htmlbody') . '</a>';

        $r .= $params['after'];

        return $r;
    }

    /**
     * Check to add/link a new version of this post if user has rights
     *
     * @return boolean
     */
    public function can_link_version($allow_new_item = false)
    {
        if (! $allow_new_item && ! $this->ID) {	// Item must be saved in DB:
            return false;
        }

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this, false)) {	// User has no rights to edit this Item
            return false;
        }

        if (! is_admin_page() || ! check_user_perm('admin', 'restricted')) {	// This feature is allowed only for back-office yet
            return false;
        }

        return true;
    }

    /**
     * Provide link to edit a post if user has edit rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     *  - 'save_context': redirect to current URL?
     */
    public function get_edit_link($params = [])
    {
        global $admin_url;

        $actionurl = $this->get_edit_url($params);
        if (! $actionurl) {
            return false;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#',
            'title' => '#',
            'class' => '',
            'save_context' => true,
        ], $params);

        if ($params['text'] == '#') {
            $params['text'] = get_icon('edit') . ' ' . T_('Edit...');
        }
        if ($params['text'] == '#icon#') {
            $params['text'] = get_icon('edit');
        }
        if ($params['title'] == '#') {
            $params['title'] = T_('Edit this post...');
        }

        $r = $params['before'];
        $r .= '<a href="' . $actionurl;
        $r .= '" title="' . $params['title'] . '"';
        if (! empty($params['class'])) {
            $r .= ' class="' . $params['class'] . '"';
        }
        $r .= '>' . $params['text'] . '</a>';
        $r .= $params['after'];

        return $r;
    }

    /**
     * Check if current User can edit this Item
     *
     * @return boolean
     */
    public function can_be_edited()
    {
        // Item must be stored in DB:
        return ! empty($this->ID) &&
            // User must be logged in and activated and has a permission to edit this Item:
            check_user_perm('item_post!CURSTATUS', 'edit', false, $this, false);
    }

    /**
     * Get URL to edit a post if user has edit rights.
     *
     * @param array Params:
     *  - 'save_context': redirect to current URL?
     */
    public function get_edit_url($params = [])
    {
        global $admin_url;

        // default params
        $params += [
            'save_context' => true,
            'glue' => '&amp;',
            'force_in_skin_editing' => false,
            'force_backoffice_editing' => false,
            'check_perm' => true, // FALSE - if this link must be displayed even if current has no permission to view item history page
        ];

        if (empty($this->ID) || ($params['check_perm'] && ! $this->can_be_edited())) {	// Don't allow to edit this Item if it is not created yet or if this Item cannot be edited by current User:
            return false;
        }

        $this->load_Blog();
        $url = false;
        if ($this->Blog->get_setting('in_skin_editing') &&
            (! $params['force_backoffice_editing'] || ! check_user_perm('admin', 'restricted')) &&
            (! is_admin_page() || $params['force_in_skin_editing'])) {	// We have a mode 'In-skin editing' for the current Blog
            if (! $params['check_perm'] || check_item_perm_edit($this->ID, false)) {	// Current user can edit this post
                $url = url_add_param($this->Blog->get('url'), 'disp=edit&p=' . $this->ID);
            }
        } elseif (! $params['check_perm'] || check_user_perm('admin', 'restricted')) {	// Edit a post from Back-office
            $url = $admin_url . '?ctrl=items' . $params['glue'] . 'action=edit' . $params['glue'] . 'p=' . $this->ID . $params['glue'] . 'blog=' . $this->Blog->ID;
            if ($params['save_context']) {
                $url .= $params['glue'] . 'redirect_to=' . rawurlencode(regenerate_url('', '', '', '&') . '#' . $this->get_anchor_id());
            }
        }
        return $url;
    }

    /**
     * Template tag
     * @see Item::get_edit_link()
     */
    public function edit_link($params = [])
    {
        echo $this->get_edit_link($params);
    }

    /**
     * Provide link to propose change a post if user has edit rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     *  - 'save_context': redirect to current URL?
     */
    public function get_propose_change_link($params = [])
    {
        $actionurl = $this->get_propose_change_url($params);
        if (! $actionurl) {	// Don't display the propose change button if current user has no rights:
            return false;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#',
            'title' => '#',
            'class' => '',
            'save_context' => true,
        ], $params);

        if ($params['text'] == '#') {	// Default text:
            $params['text'] = get_icon('edit_button') . ' ' . T_('Propose change');
        } elseif ($params['text'] == '#icon#') {	// Default text as single icon:
            $params['text'] = get_icon('edit_button');
        }
        if ($params['title'] == '#') {	// Default title:
            $params['title'] = T_('Propose change');
        }

        return $params['before'] .
            '<a href="' . $actionurl . '"' .
                    ' title="' . format_to_output($params['title'], 'htmlattr') . '"' .
                    (empty($params['class']) ? '' : ' class="' . format_to_output($params['class'], 'htmlattr') . '"') . '>' .
                $params['text'] .
            '</a>';
        $params['after'];
    }

    /**
     * Get URL to propose a change for a post if user has edit rights.
     *
     * @param array Params:
     *  - 'save_context': redirect to current URL?
     */
    public function get_propose_change_url($params = [])
    {
        global $admin_url;

        if (! $this->ID) {	// Don't display this button in preview mode:
            return false;
        }

        if (! check_user_perm('blog_item_propose', 'edit', false, $this->get_blog_ID(), false)) {	// User has no right to propose a change for this Item:
            return false;
        }

        // default params:
        $params += [
            'save_context' => true,
        ];

        $this->load_Blog();
        $url = false;
        if (! is_admin_page() && $this->Blog->get_setting('in_skin_change_proposal')) {	// We have a mode 'In-skin editing' for the current Blog
            $url = url_add_param($this->Blog->get('url'), 'disp=proposechange&p=' . $this->ID);
        } elseif (check_user_perm('admin', 'restricted')) {	// Edit a post from Back-office:
            $url = $admin_url . '?ctrl=items&amp;action=propose&amp;p=' . $this->ID . '&amp;blog=' . $this->Blog->ID;
            if ($params['save_context']) {
                $url .= '&amp;redirect_to=' . rawurlencode(regenerate_url('', '', '', '&') . '#' . $this->get_anchor_id());
            }
        }

        return $url;
    }

    /**
     * Template tag
     * @see Item::get_edit_link()
     */
    public function propose_change_link($params = [])
    {
        echo $this->get_propose_change_link($params);
    }

    /**
     * Get a link to view changes of Item for current User
     *
     * @return string A link to history
     */
    public function get_changes_link($params = [])
    {
        $params = array_merge([
            'before' => '',
            'after' => '',
            'link_text' => '#', // Use a mask $icon$ or some other text
            'class' => '',
        ], $params);

        if (($changes_url = $this->get_changes_url()) === false) {	// No url available for current user, Don't display a link:
            return;
        }

        if ($params['link_text'] == '#') {	// Default link text:
            $params['link_text'] = '$icon$ ' . T_('View changes');
        }

        // Replace all masks with values
        $link_text = str_replace('$icon$', $this->history_info_icon(), $params['link_text']);

        return $params['before']
            . '<a href="' . $changes_url . '"' . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"') . '>' . $link_text . '</a>'
            . $params['after'];
    }

    /**
     * Get URL to view changes of Item for current User
     *
     * @param string Glue between url params
     * @return string|boolean URL to history OR False when user cannot see a history
     */
    public function get_changes_url($glue = '&amp;')
    {
        global $admin_url;

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this)) {	// Current user cannot see item changes:
            return false;
        }

        if (! $this->get_Blog()->get_setting('track_unread_content')) {	// Tracking of unread content must be enabled to know last seen timestamp by current User:
            return false;
        }

        if ($this->get_read_status() != 'updated') {	// Don't allow URL when no new changes for current User:
            return false;
        }

        return $admin_url . '?ctrl=items' . $glue . 'action=history_lastseen' . $glue . 'p=' . $this->ID;
    }

    /**
     * Template tag
     * @see Item::get_changes_link()
     */
    public function changes_link($params = [])
    {
        echo $this->get_changes_link($params);
    }

    /**
     * Get JavaScript code for onclick event of merge link
     *
     * @return boolean|string
     */
    public function get_merge_click_js()
    {
        if (! $this->ID) {	// Item must be stored in DB:
            return false;
        }

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this, false)) {	// User has no right to edit this Item:
            return false;
        }

        return 'return evo_merge_load_window( ' . $this->ID . ' )';
    }

    /**
     * Provide link to merge a post if user has edit rights
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after':    to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': CSS class name
     */
    public function get_merge_link($params = [])
    {
        $merge_click_js = $this->get_merge_click_js($params);
        if (! $merge_click_js) {	// Don't display the propose change button if current user has no rights:
            return false;
        }

        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#',
            'title' => '#',
            'class' => '',
        ], $params);

        if ($params['text'] == '#') {
            $params['text'] = get_icon('merge') . ' ' . T_('Merge with...');
        } elseif ($params['text'] == '#icon#') {
            $params['text'] = get_icon('merge');
        }
        if ($params['title'] == '#') {
            $params['title'] = T_('Merge with...');
        }

        $r = $params['before'];
        $r .= '<a href="#" onclick="' . $merge_click_js . '"'
                    . ' title="' . $params['title'] . '"'
                    . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"') . '>'
                . $params['text']
            . '</a>';
        $r .= $params['after'];

        return $r;
    }

    /**
     * Template tag
     * @see Item::get_merge_link()
     */
    public function merge_link($params = [])
    {
        $merge_link = $this->get_merge_link($params);

        if (! empty($merge_link)) {
            echo_item_merge_js();
            echo $merge_link;
        }
    }

    /**
     * Get next status to publish/restrict to this item
     * TODO: asimo>Refactor this with Comment->get_next_status()
     *
     * @param boolean true to get next publish status, and false to get next restrict status
     * @return mixed false if user has no permission | array( status, status_text, icon_color ) otherwise
     */
    public function get_next_status($publish)
    {
        if (! is_logged_in(false)) {
            return false;
        }

        $status_order = get_visibility_statuses('ordered-array');
        $status_index = get_visibility_statuses('ordered-index');

        $curr_index = $status_index[$this->status];
        if ((! $publish) && ($curr_index == 0) && ($this->status != 'deprecated')) {
            $curr_index = $curr_index + 1;
        }
        $has_perm = false;
        while (! $has_perm && ($publish ? ($curr_index < 4) : ($curr_index > 0))) {
            $curr_index = $publish ? ($curr_index + 1) : ($curr_index - 1);
            $has_perm = check_user_perm('item_post!' . $status_order[$curr_index][0], 'moderate', false, $this);
        }
        if ($has_perm) {
            $label_index = $publish ? 1 : 2;
            return [$status_order[$curr_index][0], $status_order[$curr_index][$label_index], $status_order[$curr_index][3]];
        }
        return false;
    }

    /**
     * Provide link to publish a post if user has edit rights
     *
     * Note: publishing date will be updated
     *
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @param string glue between url params
     */
    public function get_publish_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $glue = '&amp;', $save_context = true)
    {
        global $admin_url;

        if ($this->status != 'draft') {
            return false;
        }

        if (! check_user_perm('item_post!published', 'edit', false, $this, false) ||
            ! check_user_perm('blog_edit_ts', 'edit', false, $this->get_blog_ID(), false)) { // User has no right to publish this post now:
            return false;
        }

        if ($text == '#') {
            $text = get_icon('post', 'imgtag') . ' ' . T_('Publish NOW!');
        }
        if ($title == '#') {
            $title = T_('Publish now using current date and time.');
        }

        $r = $before;
        $r .= '<a href="' . $admin_url . '?ctrl=items' . $glue . 'action=publish_now' . $glue . 'post_ID=' . $this->ID . $glue . url_crumb('item');
        if ($save_context) {
            $r .= $glue . 'redirect_to=' . rawurlencode(regenerate_url('', '', '', '&'));
        }
        $r .= '" title="' . $title . '"';
        if (! empty($class)) {
            $r .= ' class="' . $class . '"';
        }
        $r .= '>' . $text . '</a>';
        $r .= $after;

        return $r;
    }

    /**
     * Provide link to publish a post to the highest available public status for the current User
     *
     * @return boolean true if link was displayed false otherwise
     */
    public function highest_publish_link($params = [])
    {
        global $admin_url;

        if (! is_logged_in(false)) {
            return false;
        }

        $params = array_merge([
            'before' => '',
            'after' => '',
            'text' => '#',
            'before_text' => '',
            'after_text' => '',
            'title' => '',
            'class' => '',
            'glue' => '&amp;',
            'save_context' => true,
            'redirect_to' => '',
        ], $params);

        $curr_status_permvalue = get_status_permvalue($this->status);
        // get the current User highest publish status for this item Blog
        list($highest_status, $publish_text) = get_highest_publish_status('post', $this->get_blog_ID(), true, '', $this);
        // Get binary value of the highest available status
        $highest_status_permvalue = get_status_permvalue($highest_status);
        if ($curr_status_permvalue >= $highest_status_permvalue || ($highest_status_permvalue <= get_status_permvalue('private'))) { // Current User has no permission to change this comment status to a more public status
            return false;
        }

        if (! (check_user_perm('item_post!' . $highest_status, 'edit', false, $this))) { // User has no right to edit this post
            return false;
        }

        $glue = $params['glue'];
        $text = ($params['text'] == '#') ? $publish_text : $params['text'];

        $r = $params['before'];
        $r .= '<a href="' . $admin_url . '?ctrl=items' . $glue . 'action=publish' . $glue . 'post_status=' . $highest_status . $glue . 'post_ID=' . $this->ID . $glue . url_crumb('item');
        if ($params['redirect_to']) {
            $r .= $glue . 'redirect_to=' . rawurlencode($params['redirect_to']);
        } elseif ($params['save_context']) {
            $r .= $glue . 'redirect_to=' . rawurlencode(regenerate_url('', '', '', '&'));
        }
        $r .= '" title="' . $params['title'] . '"';
        if (! empty($params['class'])) {
            $r .= ' class="' . $params['class'] . '"';
        }
        $r .= '>' . $params['before_text'] . $text . $params['after_text'] . '</a>';
        $r .= $params['after'];

        echo $r;
        return true;
    }

    /**
     * Provide link to publish a post if user has permission
     *
     * @return boolean true if link was displayed false otherwise
     */
    public function publish_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $glue = '&amp;', $save_context = true)
    {
        $publish_link = $this->get_publish_link($before, $after, $text, $title, $class, $glue, $save_context);

        if ($publish_link === false) {	// The publish link is unavailable for current user and for this item
            return false;
        }

        // Display the publish link
        echo $publish_link;

        return true;
    }

    /**
     * Display next Publish/Restrict to link
     *
     * @param array link params
     * @param boolean true to display next publish status, and false to display next restrict status link
     * @return boolean true if link was displayed | false otherwise
     */
    public function next_status_link($params, $publish)
    {
        global $admin_url;

        $params = array_merge([
            'before' => '',
            'after' => '',
            'before_text' => '',
            'after_text' => '',
            'text' => '#',
            'title' => '',
            'class' => '',
            'glue' => '&amp;',
            'redirect_to' => '',
            'post_navigation' => 'same_blog',
            'nav_target' => null,
        ], $params);

        if ($publish) {
            $next_status_in_row = $this->get_next_status(true);
            $action = 'publish';
            $button_default_icon = isset($next_status_in_row[2]) ? 'move_up_' . $next_status_in_row[2] : 'move_up_';
        } else {
            $next_status_in_row = $this->get_next_status(false);
            $action = 'restrict';
            $button_default_icon = isset($next_status_in_row[2]) ? 'move_down_' . $next_status_in_row[2] : 'move_down_';
        }

        if ($next_status_in_row === false) { // Next status is not allowed for current user
            return false;
        }

        $next_status = $next_status_in_row[0];
        $next_status_label = $next_status_in_row[1];

        if (isset($params['text_' . $next_status])) { // Set text from params for next status
            $text = $params['text_' . $next_status];
        } elseif ($params['text'] != '#') { // Set text from params for any atatus
            $text = $params['text'];
        } else { // Default text
            $text = get_icon($button_default_icon, 'imgtag', [
                'title' => '',
            ]) . ' ' . $next_status_label;
        }

        if (empty($params['title'])) {
            $status_title = get_visibility_statuses('moderation-titles');
            $params['title'] = $status_title[$next_status];
        }
        $glue = $params['glue'];

        $r = $params['before'];
        $r .= '<a href="' . $admin_url . '?ctrl=items' . $glue . 'action=' . $action . $glue . 'post_status=' . $next_status . $glue . 'post_ID=' . $this->ID . $glue . url_crumb('item');

        $redirect_to = $params['redirect_to'];
        if (empty($redirect_to) && (! is_admin_page())) { // we are in front office
            if ($next_status == 'deprecated') {
                if ($params['post_navigation'] == 'same_category') {
                    $redirect_to = get_caturl($params['nav_target']);
                } else {
                    $this->get_Blog();
                    $redirect_to = $this->Blog->gen_blogurl();
                }
            } else {
                $redirect_to = $this->add_navigation_param($this->get_permanent_url(), $params['post_navigation'], $params['nav_target']);
            }
        }
        if (! empty($redirect_to)) {
            $r .= $glue . 'redirect_to=' . rawurlencode($redirect_to);
        }

        $r .= '" title="' . $params['title'] . '"';
        if (empty($params['class_' . $next_status])) { // Set class for all statuses
            $class = empty($params['class']) ? '' : $params['class'];
        } else { // Set special class for next status
            $class = $params['class_' . $next_status];
        }
        if (! empty($class)) {
            $r .= ' class="' . $class . '"';
        }
        $r .= '>' . $params['before_text'] . $text . $params['after_text'] . '</a>';
        $r .= $params['after'];

        echo $r;
        return true;
    }

    /**
     * Provide link to deprecate a post if user has edit rights
     *
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @param string glue between url params
     */
    public function get_deprecate_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $glue = '&amp;', $redirect_to = '')
    {
        global $admin_url;

        if ($this->status == 'deprecated' || // Already deprecated!
            ! check_user_perm('item_post!deprecated', 'edit', false, $this, false)) { // User has no right to deprecated this post:
            return false;
        }

        if ($text == '#') {
            $text = get_icon('deprecate', 'imgtag') . ' ' . T_('Deprecate') . '!';
        }
        if ($title == '#') {
            $title = T_('Deprecate this post!');
        }

        if (! empty($redirect_to)) {
            $redirect_to = $glue . 'redirect_to=' . rawurlencode($redirect_to);
        }

        $r = $before;
        $r .= '<a href="' . $admin_url . '?ctrl=items' . $glue . 'action=deprecate' . $glue . 'post_ID=' . $this->ID . $glue . url_crumb('item') . $redirect_to;
        $r .= '" title="' . $title . '"';
        if (! empty($class)) {
            $r .= ' class="' . $class . '"';
        }
        $r .= '>' . $text . '</a>';
        $r .= $after;

        return $r;
    }

    /**
     * Display link to deprecate a post if user has edit rights
     *
     * @param string to display before link
     * @param string to display after link
     * @param string link text
     * @param string link title
     * @param string class name
     * @param string glue between url params
     */
    public function deprecate_link($before = ' ', $after = ' ', $text = '#', $title = '#', $class = '', $glue = '&amp;', $redirect_to = '')
    {
        $deprecate_link = $this->get_deprecate_link($before, $after, $text, $title, $class, $glue, $redirect_to);

        if ($deprecate_link === false) {	// The deprecate link is unavailable for current user and for this item
            return false;
        }

        // Display the deprecate link
        echo $deprecate_link;

        return true;
    }

    /**
     * Template function: display priority of item
     *
     * @param string
     * @param string
     */
    public function priority($before = '', $after = '')
    {
        if (isset($this->priority)) {
            echo $before;
            echo $this->priority;
            echo $after;
        }
    }

    /**
     * Get checkable list of renderers
     *
     * @param array|null If given, assume these renderers to be checked.
     * @return string Renderer checkboxes
     */
    public function get_renderer_checkboxes($item_renderers = null)
    {
        global $Plugins;

        if (is_null($item_renderers)) {
            $item_renderers = $this->get_renderers();
        }

        return $Plugins->get_renderer_checkboxes($item_renderers, [
            'Item' => &$this,
        ]);
    }

    /**
     * Template function: display checkable list of renderers
     *
     * @param array|null If given, assume these renderers to be checked.
     */
    public function renderer_checkboxes($item_renderers = null)
    {
        echo $this->get_renderer_checkboxes($item_renderers);
    }

    /**
     * Get status of item
     *
     * Statuses:
     * - published
     * - deprecated
     * - protected
     * - private
     * - draft
     *
     * @param array Params
     */
    public function get_status($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'format' => 'htmlbody', // DO NOT USE 'styled' ->DEPRECATED! INSTEAD: Valid values: see {@link format_to_output()}
            'class' => '',			// DEPRECATED
        ], $params);

        $r = $params['before'];

        switch ($params['format']) {
            case 'raw':
                $r .= $this->get_status_raw();
                break;

            case 'styled':
                // DEPRECATED: instead use something like: $Item->format_status( array(	'template' => '<div class="evo_status__banner evo_status__$status$">$status_title$</div>' ) );
                $r .= get_styled_status($this->status, $this->get('t_status'), $params['class']);
                break;

            default: // other formats
                $r .= format_to_output($this->get('t_status'), $params['format']);
                break;
        }

        $r .= $params['after'];

        return $r;
    }

    /**
     * Template function: display status of item
     *
     * Statuses:
     * - published
     * - deprecated
     * - protected
     * - private
     * - draft
     *
     * @param array Params
     */
    public function status($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'format' => 'htmlbody', // Output format, see {@link format_to_output()}
            'class' => '',
        ], $params);

        echo $this->get_status($params);
    }

    /**
     * Get status of item in a formatted way, following a provided template
     *
     * There are 3 possible variables:
     * - $status$ = the raw status
     * - $status_title$ = the human readable text version of the status (translated to current language)
     * - $tooltip_title$ = the human readable text version of the status for the tooltip
     *
     * @param array Params
     * @return string
     */
    public function get_format_status($params = [])
    {
        $params = array_merge([
            'template' => '<div class="evo_status evo_status_$status$" data-toggle="tooltip" data-placement="top" title="$tooltip_title$">$status_title$</div>',
            'format' => 'htmlbody', // Output format, see {@link format_to_output()}
        ], $params);

        $r = str_replace('$status$', $this->get('status'), $params['template']);
        $r = str_replace('$status_title$', $this->get('t_status'), $r);
        $r = str_replace('$tooltip_title$', get_status_tooltip_title($this->get('status')), $r);

        return format_to_output($r, $params['format']);
    }

    /**
     * Display status of item in a formatted way, following a provided template
     *
     * There are 2 possible variables:
     * - $status$ = the raw status
     * - $status_title$ = the human readable text version of the status (translated to current language)
     *
     * @param array Params
     */
    public function format_status($params = [])
    {
        echo $this->get_format_status($params);
    }

    /**
     * Output classes for the Item <div>
     */
    public function div_classes($params = [], $output = true)
    {
        global $disp;

        // Make sure we are not missing any param:
        $params = array_merge([
            'item_class' => 'bPost',
            'item_type_class' => 'bPost_ptyp',
            'item_status_class' => 'bPost',
        ], $params);

        $classes = [$params['item_class'],
            $params['item_type_class'] . $this->ityp_ID,
            $params['item_status_class'] . $this->status,
        ];

        $r = implode(' ', $classes);

        if (! $output) {
            return $r;
        }

        echo $r;
    }

    /**
     * Get raw status
     *
     * @return string Status
     */
    public function get_status_raw()
    {
        return $this->status;
    }

    /**
     * Output raw status.
     */
    public function status_raw()
    {
        echo $this->get_status_raw();
    }

    /**
     * Template function: display extra status of item
     *
     * @param string
     * @param string
     * @param string Output format, see {@link format_to_output()}
     */
    public function extra_status($before = '', $after = '', $format = 'htmlbody')
    {
        if ($format == 'raw') {
            $this->disp($this->get('t_extra_status'), 'raw');
        } elseif ($extra_status = $this->get('t_extra_status')) {
            echo $before . format_to_output($extra_status, $format) . $after;
        }
    }

    /**
     * Display tags for Item
     *
     * @param array of params
     * @param string Output format, see {@link format_to_output()}
     */
    public function tags($params = [])
    {
        global $evo_charset;

        $params = array_merge([
            'before' => '<div>' . T_('Tags') . ': ',
            'after' => '</div>',
            'separator' => ', ',
            'links' => true,
            'before_tag' => '',
            'after_tag' => '',
        ], $params);

        $tags = $this->get_tags();

        if (! empty($tags)) {
            echo $params['before'];

            if ($links = $params['links']) {
                $this->get_Blog();
            }

            $i = 0;
            foreach ($tags as $tag_ID => $tag_name) {
                if ($i++ > 0) {
                    echo $params['separator'];
                }

                echo str_replace('$tag_ID$', $tag_ID, $params['before_tag']);
                if ($links) {	// We want links
                    echo $this->Blog->get_tag_link($tag_name);
                } else {
                    echo htmlspecialchars($tag_name, null, $evo_charset);
                }
                echo $params['after_tag'];
            }

            echo $params['after'];
        }
    }

    /**
     * Template function: Displays trackback autodiscovery information
     *
     * TODO: build into headers
     */
    public function trackback_rdf()
    {
        $this->get_Blog();
        if (! $this->can_receive_pings()) { // Trackbacks not allowed on this blog:
            return;
        }

        echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" ' . "\n";
        echo '  xmlns:dc="http://purl.org/dc/elements/1.1/"' . "\n";
        echo '  xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">' . "\n";
        echo '<rdf:Description' . "\n";
        echo '  rdf:about="';
        $this->permanent_url('single');
        echo '"' . "\n";
        echo '  dc:identifier="';
        $this->permanent_url('single');
        echo '"' . "\n";
        $this->title([
            'before' => ' dc:title="',
            'after' => '"' . "\n",
            'link_type' => 'none',
            'format' => 'xmlattr',
        ]);
        echo '  trackback:ping="';
        $this->trackback_url();
        echo '" />' . "\n";
        echo '</rdf:RDF>';
    }

    /**
     * Template function: displays url to use to trackback this item
     */
    public function trackback_url()
    {
        echo $this->get_trackback_url();
    }

    /**
     * Template function: get url to use to trackback this item
     * @return string
     */
    public function get_trackback_url()
    {
        // fp> TODO: get a clean (per blog) setting for this
        //	return get_htsrv_url().'trackback.php/'.$this->ID;

        return get_htsrv_url() . 'trackback.php?tb_id=' . $this->ID;
    }

    /**
     * Get HTML code to display video/audio player for playback of a given URL
     *
     * @param string The URL of video/audio file
     * @return string The HTML code
     */
    public function get_player($url)
    {
        global $Plugins;

        $params = [
            'url' => $url,
            'data' => '',
        ];

        $temp_params = $params;
        foreach ($params as $param_key => $param_value) { // Pass all params by reference, in order to give possibility to modify them by plugin
            // So plugins can add some data before/after image tags (E.g. used by infodots plugin)
            $params[$param_key] = &$params[$param_key];
        }

        $params = $Plugins->trigger_event_first_true('RenderURL', $params);
        if (count($params) != 0) {	// Display a rendered url, for example as video/audio player:
            return $params['data'];
        }

        // Display URL as simple link:
        return '<a href="' . $url . '">' . $url . '</a>';
    }

    /**
     * Template function: Display link to item related url.
     *
     * By default the link is displayed as a link.
     * Optionally some smart stuff may happen.
     */
    public function url_link($params = [])
    {
        if (empty($this->url)) {
            return;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text_template' => '$url$',		// If evaluates to empty, nothing will be displayed (except player if podcast)
            'url_template' => '$url$',
            'target' => '',
            'format' => 'htmlbody',
            'podcast' => '#',						// handle as podcast. # means depending on post type
            'before_podplayer' => '<div class="podplayer">',
            'after_podplayer' => '</div>',
            'link_class' => '',
        ], $params);

        if ($params['podcast'] == '#') {	// Check if this post is a podcast:
            $params['podcast'] = $this->get_type_setting('podcast');
        }

        if ($params['podcast'] && $params['format'] == 'htmlbody') {	// We want podcast display:
            echo $params['before_podplayer'];

            echo $this->get_player($this->url);

            echo $params['after_podplayer'];
        } else { // Not displaying podcast player:
            $text = str_replace('$url$', $this->url, $params['text_template']);
            if (empty($text)) {	// Nothing to display
                return;
            }

            $r = $params['before'];

            $r .= '<a href="' . str_replace('$url$', $this->url, $params['url_template']) . '"';

            if (! empty($params['link_class'])) {
                $r .= ' class="' . $params['link_class'] . '"';
            }

            if (! empty($params['target'])) {
                $r .= ' target="' . $params['target'] . '"';
            }

            $r .= '>' . $text . '</a>';

            $r .= $params['after'];

            echo format_to_output($r, $params['format']);
        }
    }

    /**
     * Template function: Display link to parent of this item.
     *
     * @param array
     */
    public function parent_link($params = [])
    {
        if (empty($this->parent_ID)) {	// No parent
            return;
        }

        if ($this->get_type_setting('use_parent') == 'never') {	// This item cannot has a parent item, because of item type settings
            return;
        }

        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'not_found_text' => '',
            'format' => 'htmlbody',
        ], $params);

        // Get parent Item:
        $parent_Item = $this->get_parent_Item();

        $r = $params['before'];

        if (! empty($parent_Item)) {	// Display a parent post title as link to permanent url
            $r .= $parent_Item->get_title();
        } else {	// No parent post found, Display a text to inform about this:
            $r .= $params['not_found_text'];
        }

        $r .= $params['after'];

        echo format_to_output($r, $params['format']);
    }

    /**
     * Template function: Display the number of words in the post
     */
    public function wordcount()
    {
        echo (int) $this->wordcount; // may have been saved as NULL until 1.9
    }

    /**
     * Template function: Display the number of times the Item has been viewed
     *
     * Note: viewcount is incremented whenever the Item's content is displayed with "MORE"
     * (i-e full content), see {@link Item::content()}.
     *
     * Viewcount is NOT incremented on page reloads and other special cases, see {@link Hit::is_new_view()}
     *
     * %d gets replaced in all params by the number of views.
     *
     * @deprecated Deprecated
     * @param string Link text to display when there are 0 views
     * @param string Link text to display when there is 1 views
     * @param string Link text to display when there are >1 views
     * @return string The phrase about the number of views.
     */
    public function get_views($zero = '#', $one = '#', $more = '#')
    {
        // Deprecated feature, Display nothing:
        return '';
    }

    /**
     * Template function: Display a phrase about the number of Item views.
     *
     * @deprecated Deprecated
     * @param string Link text to display when there are 0 views
     * @param string Link text to display when there is 1 views
     * @param string Link text to display when there are >1 views (include %d for # of views)
     * @return integer Number of views.
     */
    public function views($zero = '#', $one = '#', $more = '#')
    {
        // Deprecated feature, Display nothing:
        return 0;
    }

    /**
     * Set param value
     *
     * By default, all values will be considered strings
     *
     * @todo extra_cat_IDs recording
     *
     * @param string parameter name
     * @param mixed parameter value
     * @param boolean true to set to NULL if empty value
     * @return boolean true, if a value has been set; false if it has not changed
     */
    public function set($parname, $parvalue, $make_null = false)
    {
        switch ($parname) {
            case 'pst_ID':
            case 'priority':
                return $this->set_param($parname, 'number', $parvalue, true);

            case 'content':
                $this->content_is_updated = $this->set_param('content', 'string', $parvalue, $make_null);
                // Update wordcount as well:
                $wordcount_is_updated = $this->set_param('wordcount', 'number', bpost_count_words($this->content), false);
                return ($this->content_is_updated || $wordcount_is_updated);
                // fp>: I think we should remove return above and add the following code:
                // BUT we cannot do that because generating the except requires to execute renderers, which requires to have a main cat ID/coll ID set, which we may not have at this point
                /*				if( $this->excerpt_autogenerated )
                                {	// As far as we know, we are still auto-generating excerpts for this Item at this moment,
                                    //		so let's make sure the excerpt stays in sync:
                                    $r3 = $this->set_param( 'excerpt', 'string', $this->get_autogenerated_excerpt() )
                                }
                                return ( $this->content_is_updated || $wordcount_is_updated || $r3 );
                */

            case 'wordcount':
            case 'featured':
                return $this->set_param($parname, 'number', $parvalue, false);

            case 'datedeadline':
                return $this->set_param('datedeadline', 'date', $parvalue, true);

            case 'order':
                // Field 'post_order' is deprecated,
                // but we can set it per each category:
                if (is_array($this->extra_cat_IDs)) {	// Update order per each item category:
                    $this->orders = [];
                    foreach ($this->extra_cat_IDs as $extra_cat_ID) {
                        $this->orders[$extra_cat_ID] = $parvalue;
                    }
                }
                return false;

            case 'renderers': // deprecated
                return $this->set_renderers($parvalue);

            case 'excerpt':
                if (empty($parvalue)) {	// We are trying to make the excerpt empty.
                    // This means we should to go back to autogenerated excerpt:
                    // fp>TODO (LATER): maybe we should do this only if $this->get_type_setting( 'use_excerpt' ) == 'required' ???
                    $this->set('excerpt_autogenerated', 1);
                    $parvalue = $this->get_autogenerated_excerpt();
                }
                return parent::set('excerpt', $parvalue, $make_null);

            case 'notifications_flags':
                $notifications_flags = $this->get('notifications_flags');
                if (! is_array($parvalue)) {	// Convert string to array:
                    $parvalue = [$parvalue];
                }
                $notifications_flags = array_merge($notifications_flags, $parvalue);
                $notifications_flags = array_unique($notifications_flags);
                return $this->set_param('notifications_flags', 'string', implode(',', $notifications_flags), $make_null);

            case 'status':
                // We need to set a reminder here to later check if the new status is allowed at dbinsert or dbupdate time ( $this->restrict_status( true ) )
                // We cannot check immediately because we may be setting the status before having set a main cat_ID -> a collection ID to check the status possibilities
                // Save previous status as a reminder (it can be useful to compare later. The Comment class uses this).
                $this->previous_status = $this->get('status');
                return parent::set('status', $parvalue, $make_null);

            case 'revision':
                if (! isset($this->revision) || $this->revision != $parvalue) {	// When revision is changed:
                    if (isset($this->custom_fields)) {	// Reset custom fields:
                        unset($this->custom_fields);
                    }
                    // Clear Links/Attachments to load them with data from other revision:
                    $LinkCache = &get_LinkCache();
                    $LinkCache->clear();
                }
                if ($parvalue == 'c') {	// Don't set revision if required to current version:
                    if ($this->is_revision()) {	// Unset previous active revision:
                        unset($this->revision);
                    }
                    return false;
                }
                // Don't use parent::set() because post has no column "revision" in DB:
                $this->revision = $parvalue;
                return true;

            case 'urltitle':
                if (! isset($this->previous_urltitle)) {	// Save previous urltitle, may be used to rename folder of attachments:
                    $this->previous_urltitle = $this->get('urltitle');
                }
                return parent::set($parname, $parvalue, $make_null);

            default:
                return parent::set($parname, $parvalue, $make_null);
        }
    }

    /**
     * Set the renderers of the Item.
     *
     * @param array List of renderer codes.
     * @return boolean true, if it has been set; false if it has not changed
     */
    public function set_renderers($renderers)
    {
        return $this->set_param('renderers', 'string', implode('.', $renderers));
    }

    /**
     * Set the Author of the Item.
     *
     * @param User (Do NOT set to NULL or you may kill the current_User)
     * @return boolean true, if it has been set; false if it has not changed
     */
    public function set_creator_User(&$creator_User)
    {
        $this->creator_User = &$creator_User;
        $this->Author = &$this->creator_User; // deprecated  fp> TODO: Test and see if this line can be put once and for all in the constructor
        return $this->set($this->creator_field, $creator_User->ID);
    }

    /**
     * Set the Item location from the current user. Use to create a new post.
     *
     * @param string Location (country | region | subregion | city)
     */
    public function set_creator_location($location)
    {
        global $current_User;

        if (! is_logged_in()) {	// No logged in user
            return;
        }

        $locations = [
            'country' => 'ctry_ID',
            'region' => 'rgn_ID',
            'subregion' => 'subrg_ID',
            'city' => 'city_ID',
        ];

        $field_ID = $locations[$location];

        $this->load_Blog();
        if ($this->{$location . '_visible'}()) {	// Location is visible
            if (empty($this->$field_ID)) {	// Set default location
                $this->set($field_ID, $current_User->$field_ID);
            }
        }
    }

    /**
     * Create a new Item/Post and insert it into the DB
     *
     * This function has to handle all needed DB dependencies!
     *
     * @deprecated Use set() + dbinsert() instead
     */
    public function insert(
        $author_user_ID,              // Author
        $post_title,
        $post_content,
        $post_timestamp,              // 'Y-m-d H:i:s'
        $main_cat_ID = 1,             // Main cat ID
        $extra_cat_IDs = [],     // Table of extra cats
        $post_status = 'published',   // Use first char '!' before status name to force this status without restriction by max allowed status of the collection
        $post_locale = '#',
        $post_urltitle = '',
        $post_url = '',
        $post_comment_status = 'open',
        $post_renderers = ['default'],
        $item_type_name_or_ID_or_template = '#', // Use 'Page', 'Post' and etc. OR '#' to use default post type OR integer to use post type by ID OR $template_name$
        $item_st_ID = null,
        $postcat_order = null,
        $display_restrict_status_messages = true
    ) {
        global $DB, $query, $UserCache;
        global $default_locale;

        if ($item_type_name_or_ID_or_template == '#') {	// Try to set default post type ID from blog setting:
            $ChapterCache = &get_ChapterCache();
            if ($Chapter = &$ChapterCache->get_by_ID($main_cat_ID, false, false) &&
                $Collection = $Blog = &$Chapter->get_Blog()) {	// Use default post type what used for the blog:
                $item_typ_ID = $Blog->get_setting('default_post_type');
            }
        } else {	// Try to get item type by requested name:
            $ItemTypeCache = &get_ItemTypeCache();
            if (is_int($item_type_name_or_ID_or_template) &&
                ($ItemType = &$ItemTypeCache->get_by_ID($item_type_name_or_ID_or_template, false, false))) {	// Item type exists in DB by requested ID, Use it:
                $item_typ_ID = $ItemType->ID;
            } elseif (preg_match('/^\$(.+)\$$/', $item_type_name_or_ID_or_template, $ityp_match) &&
                    ($ItemType = &$ItemTypeCache->get_by_template($ityp_match[1], false, false))) {	// Item type exists in DB by requested template, Use it:
                $item_typ_ID = $ItemType->ID;
            } elseif ($ItemType = &$ItemTypeCache->get_by_name($item_type_name_or_ID_or_template, false, false)) {	// Item type exists in DB by requested name, Use it:
                $item_typ_ID = $ItemType->ID;
            }
        }

        if ($post_comment_status == 'closed' || $post_comment_status == 'disabled') {	// Check if item type allows these options:
            $ItemTypeCache = &get_ItemTypeCache();
            $ItemType = &$ItemTypeCache->get_by_ID($item_typ_ID);
            if ($post_comment_status == 'closed' && ! $ItemType->get('allow_closing_comments')) {
                debug_die('Item type "' . $ItemType->get_name() . '" doesn\'t support closing comments, please set another comment status for item "' . $post_title . '"');
            } elseif ($post_comment_status == 'disabled' && ! $ItemType->get('allow_disabling_comments')) {
                debug_die('Item type "' . $ItemType->get_name() . '" doesn\'t support disabling comments, please set another comment status for item "' . $post_title . '"');
            }
        }

        if (empty($item_typ_ID)) {	// Use first item type by default for wrong request:
            $item_typ_ID = 1;
        }

        // Set Item Type here in order to get item type settings below:
        $this->set('ityp_ID', $item_typ_ID);

        if (! $this->get_type_setting('allow_html')) {	// Strip HTML tags from content if HTML is not allowed for Item Type of this Item:
            $post_content = utf8_strip_tags($post_content);
        }

        if ($post_locale == '#') {
            $post_locale = $default_locale;
        }

        // echo 'INSERTING NEW POST ';

        if (isset($UserCache)) {	// DIRTY HACK
            // If not in install procedure...
            $this->set_creator_User($UserCache->get_by_ID($author_user_ID));
        } else {
            $this->set($this->creator_field, $author_user_ID);
        }
        $this->set($this->lasteditor_field, $this->{$this->creator_field});
        $this->set('title', $post_title);
        $this->set('urltitle', $post_urltitle);
        $this->set('content', $post_content);
        //$this->set( 'datestart', $post_timestamp );
        $this->set('datestart', date('Y-m-d H:i:s')); // Use current time temporarily, we'll update this later

        $this->set('main_cat_ID', $main_cat_ID);
        $this->set('extra_cat_IDs', $extra_cat_IDs);
        if (substr($post_status, 0, 1) == '!') {	// Force the requested status to ignore restriction by collection settings:
            $post_status = substr($post_status, 1);
            $force_status = true;
        }
        $this->set('status', $post_status);
        if (! empty($force_status)) {	// Unset flag to don't restrict the status:
            unset($this->previous_status);
        }
        $this->set('locale', $post_locale);
        $this->set('url', $post_url);
        $this->set('comment_status', $post_comment_status);
        $this->set_renderers($post_renderers);
        $this->set('pst_ID', $item_st_ID);
        $this->set('order', $postcat_order);

        if ($this->get('ityp_ID') > 0 && isset($this->custom_fields)) {	// Reinitialize custom fields definitions if they were created to set new values before set item type for this Item, e-g on install default Items:
            $old_custom_fields = $this->custom_fields;
            $this->custom_fields = $this->get_custom_fields_defs();
            foreach ($this->custom_fields as $custom_field_name => $custom_field) {
                if (isset($old_custom_fields[$custom_field_name]['value'])) {
                    $custom_field['value'] = $old_custom_fields[$custom_field_name]['value'];
                }
                $this->custom_fields[$custom_field_name] = $custom_field;
            }
            unset($old_custom_fields);
        }

        // Update the computed custom fields if this Item has them:
        $custom_fields = $this->get_custom_fields_defs();
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['type'] == 'computed') {	// Set a value by special function because we don't submit value for such fields and compute a value by formula automatically:
                $this->set_custom_field($custom_field['name'], $this->get_custom_field_computed($custom_field['name']), 'value', true);
            }
        }

        // INSERT INTO DB:
        $this->dbinsert($display_restrict_status_messages);

        // Update post_datestart using FROM_UNIXTIME to prevent invalid datetime values during DST spring forward - fall back
        $DB->query('UPDATE T_items__item SET post_datestart = FROM_UNIXTIME(' . strtotime($post_timestamp) . ') WHERE post_ID = ' . $DB->quote($this->ID));

        return $this->ID;
    }

    /**
     * Insert object into DB based on previously recorded changes
     *
     * @param boolean Display restrict status messages
     * @return boolean true on success
     */
    public function dbinsert($display_restrict_status_messages = true)
    {
        global $DB, $current_User, $Plugins;

        $DB->begin('SERIALIZABLE');

        if (isset($this->previous_status)) {	// Restrict Item status by Collection access restriction AND by CURRENT USER write perm:
            // (ONLY if current request is updating item status)
            $this->restrict_status(true, $display_restrict_status_messages);
        }

        if ($this->status != 'draft') {	// The post is getting published in some form, set the publish date so it doesn't get auto updated in the future:
            $this->set('dateset', 1);
        }

        if (empty($this->creator_user_ID)) { // No creator assigned yet, use current user:
            $this->set_creator_User($current_User);
        }

        // Validate urltitle/slug:
        $orig_urltitle = $this->urltitle;
        $urltitles = explode(',', $this->urltitle);
        foreach ($urltitles as $u => $urltitle_value) {
            $urltitles[$u] = utf8_trim($urltitle_value);
        }
        $orig_urltitle = implode(',', array_unique($urltitles));
        $this->set('urltitle', urltitle_validate($urltitles[0], $this->title, $this->ID, false, 'slug_title', 'slug_itm_ID', 'T_slug', $this->locale, 'T_items__item'));

        $this->update_renderers_from_Plugins();

        if (isset($this->content_is_updated)) {	// Autogenerate new excerpt ONLY when content has been updated during current request:
            $this->update_autogenerated_excerpt();
        }

        if (isset($Plugins)) {	// Note: Plugins may not be available during maintenance, install or test cases
            // TODO: allow a plugin to cancel update here (by returning false)?
            $Plugins->trigger_event('PrependItemInsertTransact', $params = [
                'Item' => &$this,
            ]);
        }

        $this->set_last_touched_ts();
        $this->set_contents_last_updated_ts();

        // Check if item is assigned to a user
        if (isset($this->dbchanges['post_assigned_user_ID'])) {
            $this->assigned_to_new_user = true;
        }

        $dbchanges = $this->dbchanges; // we'll save this for passing it to the plugin hook

        if ($result = parent::dbinsert()) { // We could insert the item object..
            if (! empty($this->source_version_item_ID)) {	// Set group ID if this Item is creating as version of another Item:
                $this->set_group_ID($this->source_version_item_ID);
            }

            // Link attachments from temporary object to new created Item:
            $this->link_from_Request();

            // Let's handle the extracats:
            $result = $this->insert_update_extracats('insert');

            if ($result) { // Let's handle the tags:
                $this->insert_update_tags('insert');
            }

            // save Item settings
            if ($result && isset($this->ItemSettings) && isset($this->ItemSettings->cache[0])) {
                // update item ID in the ItemSettings cache
                $this->ItemSettings->cache[$this->ID] = $this->ItemSettings->cache[0];
                unset($this->ItemSettings->cache[0]);

                $this->ItemSettings->dbupdate();
            }

            // Update custom fields:
            $this->update_custom_fields();

            if ($result) {
                modules_call_method('update_item_after_insert', [
                    'edited_Item' => $this,
                ]);
            }

            // Let's handle the slugs:
            $new_slugs = $this->update_slugs($orig_urltitle);

            if ($result && ! empty($new_slugs)) {	// If we have new created slugs, we have to insert it into the database:
                foreach ($new_slugs as $s => $new_Slug) {
                    if ($new_Slug->ID == 0) {	// Insert only new created slugs:
                        if (! $new_Slug->dbinsert()) {
                            $result = false;
                        } elseif ($s == 0) {
                            $new_canonical_Slug = $new_slugs[0];
                        }
                    }
                }
                // Clear cached slugs in order to display new unique updated on the edit form:
                $this->slugs = null;
            }

            // Create tiny slug:
            $new_tiny_Slug = new Slug();
            load_funcs('slugs/model/_slug.funcs.php');
            $tinyurl = getnext_tinyurl();
            $new_tiny_Slug->set('title', $tinyurl);
            $new_tiny_Slug->set('type', 'item');
            $new_tiny_Slug->set('itm_ID', $this->ID);

            if ($result && ($result = (isset($new_canonical_Slug) && $new_tiny_Slug->dbinsert()))) {
                $this->set('canonical_slug_ID', $new_canonical_Slug->ID);
                $this->set('tiny_slug_ID', $new_tiny_Slug->ID);
                if ($result = parent::dbupdate()) {
                    $DB->commit();

                    // save the last tinyurl
                    global $Settings;
                    $Settings->set('tinyurl', $tinyurl);
                    $Settings->dbupdate();

                    if (isset($Plugins)) {	// Note: Plugins may not be available during maintenance, install or test cases
                        $Plugins->trigger_event('AfterItemInsert', $params = [
                            'Item' => &$this,
                            'dbchanges' => $dbchanges,
                        ]);
                    }
                }
            }

            // Update last touched date of this Item and also all categories of this Item
            $this->update_last_touched_date(false, false);
        }

        if (! $result) { // Rollback current transaction
            $DB->rollback();
        }

        return $result;
    }

    /**
     * Insert new item in test mode, Use this function only in test tool to create very much items at one time
     *
     * @return boolean true on success
     */
    public function dbinsert_test()
    {
        global $DB, $localtimenow;

        $this->set_param('last_touched_ts', 'date', date('Y-m-d H:i:s', $localtimenow));
        $this->set_param('contents_last_updated_ts', 'date', date('Y-m-d H:i:s', $localtimenow));

        $DB->begin('SERIALIZABLE');

        if ($result = parent::dbinsert()) { // We could insert the item object..
            if (! is_null($this->extra_cat_IDs)) { // Insert new extracats:
                $query = 'INSERT INTO T_postcats ( postcat_post_ID, postcat_cat_ID ) VALUES ';
                foreach ($this->extra_cat_IDs as $extra_cat_ID) {
                    $query .= '( ' . $this->ID . ', ' . $extra_cat_ID . ' ),';
                }
                $query = substr($query, 0, strlen($query) - 1);
                $DB->query($query, 'insert new extracats');
            }

            // Create canonical slug with urltitle
            $canonical_Slug = new Slug();
            $canonical_Slug->set('title', $this->urltitle);
            $canonical_Slug->set('type', 'item');
            $canonical_Slug->set('itm_ID', $this->ID);

            // Create tiny slug:
            $tiny_Slug = new Slug();
            load_funcs('slugs/model/_slug.funcs.php');
            $tinyurl = getnext_tinyurl();
            $tiny_Slug->set('title', $tinyurl);
            $tiny_Slug->set('type', 'item');
            $tiny_Slug->set('itm_ID', $this->ID);

            if ($result = ($canonical_Slug->dbinsert() && $tiny_Slug->dbinsert())) {
                $this->set('canonical_slug_ID', $canonical_Slug->ID);
                $this->set('tiny_slug_ID', $tiny_Slug->ID);
                if ($result = parent::dbupdate()) { // save the last tinyurl
                    global $Settings;
                    $Settings->set('tinyurl', $tinyurl);
                    $Settings->dbupdate();
                }
            }
        }

        if ($result) { // The post and all related object was successfully created
            $DB->commit();
        } else { // Some error occured the transaction needs to be rollbacked
            $DB->rollback();
        }

        return $result;
    }

    /**
     * Update the DB based on previously recorded changes
     *
     * @param boolean do we want to auto track the mod date?
     * @param boolean Update slug? - We want to PREVENT updating slug when item dbupdate is called,
     * 	because of the item canonical url title was changed on the slugs edit form, so slug update is already done.
     *  If slug update wasn't done already, then this param has to be true.
     * @param boolean Update custom fields of child posts?
     * @param boolean|string TRUE - Force to create revision, FALSE - Auto create revision depending on last edit time, 'no' - Force to do NOT create revision
     * @return boolean true on success
     */
    public function dbupdate($auto_track_modification = true, $update_slug = true, $update_child_custom_fields = true, $create_revision = false)
    {
        global $DB, $Plugins, $Messages;

        $DB->begin('SERIALIZABLE');

        if (isset($this->previous_status)) {	// Restrict Item status by Collection access restriction AND by CURRENT USER write perm:
            // (ONLY if current request is updating item status)
            $this->restrict_status(true);
        }

        if ($this->status != 'draft') {	// The post is getting published in some form, set the publish date so it doesn't get auto updated in the future:
            $this->set('dateset', 1);
        }

        // Check if item is assigned to a user
        if (isset($this->dbchanges['post_assigned_user_ID'])) {
            $this->assigned_to_new_user = true;
        }

        $dbchanges = $this->dbchanges; // we'll save this for passing it to the plugin hook

        // Check whether any db change has been executed
        $db_changed = false;

        // save Item settings
        if (isset($this->ItemSettings)) {
            if ($this->get_setting('last_import_hash') !== null &&
                ! isset($this->ItemSettings->changes['last_import_hash'])) {	// Clear the setting if it is a manual updating and not import updating:
                $this->delete_setting('last_import_hash');
            }

            $item_settings_changed = $this->ItemSettings->dbupdate();
            $db_changed = $item_settings_changed || $db_changed;

            if ($item_settings_changed) {	// Update post modified date when at least one setting of this post updated, e.g. when custom field value has been updated:
                global $localtimenow;
                $this->set_param($this->datemodified_field, 'date', date('Y-m-d H:i:s', $localtimenow));
            }
        }

        // Update custom fields:
        $db_changed = $this->update_custom_fields() || $db_changed;

        if ($update_child_custom_fields) {	// Update custom fields of all child posts of this post:
            $custom_fields = $this->get_type_custom_fields();
            if (! empty($custom_fields)) {	// If this post has at least one custom field
                if (! isset($this->recursive_updated_items)) {	// Store in this array all updated items in order to avoid inifitie loop updating:
                    $this->recursive_updated_items = [];
                    $this->recursive_updated_messages = [];
                }
                $this->recursive_updated_items[] = $this->ID;

                $ItemCache = &get_ItemCache();
                $ItemCache->clear();
                $item_cache_SQL = $ItemCache->get_SQL_object();
                $item_cache_SQL->FROM_add('INNER JOIN T_items__type ON ityp_ID = post_ityp_ID');
                $item_cache_SQL->WHERE_and('post_parent_ID = ' . $this->ID);
                $item_cache_SQL->WHERE_and('ityp_use_parent != "never"');
                $child_items = $ItemCache->load_by_sql($item_cache_SQL);
                foreach ($child_items as $child_Item) {
                    if (in_array($child_Item->ID, $this->recursive_updated_items)) {	// Display error to inform about infinite loop:
                        if (! isset($this->recursive_updated_messages['nogroup'])) {
                            $this->recursive_updated_messages['nogroup'] = [];
                        }
                        $this->recursive_updated_messages['nogroup'][] = [
                            'text' => sprintf(T_('Recursive update has stopped because of infinite loop. Item #%d has child #%d which was already updated.'), intval($this->ID), intval($child_Item->ID)),
                            'type' => 'error',
                        ];
                        // Stop here to avoid infinite loop:
                        continue;
                    }
                    $child_custom_fields = $child_Item->get_custom_fields_defs();
                    if (! empty($child_custom_fields)) {	// If child post has at least one custom field:
                        $update_child_custom_field = false;
                        foreach ($custom_fields as $custom_field_code => $custom_field) {
                            if (isset($child_custom_fields[$custom_field_code]) &&
                                $child_custom_fields[$custom_field_code]['type'] == $custom_field['type'] &&
                                $child_custom_fields[$custom_field_code]['parent_sync'] &&
                                $custom_field['type'] != 'computed') { // NOTE: we must NOT copy the computed values from parent because child custom field may has a different formula!
                                // If child post has a custom field with same code and type:
                                $custom_field_make_null = $custom_field['type'] != 'double'; // store '0' values in DB for numeric fields
                                $child_Item->set_custom_field($custom_field['name'], $this->get_custom_field_value($custom_field_code, $custom_field['type']), 'value', $custom_field_make_null);
                                // Mark to know custom fields of the child post must be updated from parent:
                                $update_child_custom_field = true;
                            }
                        }
                        // NOTE: we must recompute values of the "computed" fields because child custom field may has a different formula than parent!
                        foreach ($child_custom_fields as $child_custom_field) {	// Update computed custom fields after when all fields we updated above:
                            if ($child_custom_field['type'] == 'computed') {	// Set a value by special function because we don't submit value for such fields and compute a value by formula automatically:
                                $child_Item->set_custom_field($child_custom_field['name'], $child_Item->get_custom_field_computed($child_custom_field['name']));
                                // Mark to know custom fields of the child post must be updated from parent:
                                $update_child_custom_field = true;
                            }
                        }
                        if ($update_child_custom_field) {	// Update child post custom fields if at least one field has been detected with same code and type as parent:
                            $child_Item->recursive_updated_items = &$this->recursive_updated_items;
                            $child_Item->recursive_updated_messages = &$this->recursive_updated_messages;
                            if ($child_Item->dbupdate()) {	// Display a message to inform about updated child posts:
                                $child_item_Blog = $child_Item->get_Blog();
                                $child_item_edit_link = $child_Item->get_edit_link([
                                    'text' => $child_Item->ID,
                                    'before' => '',
                                    'after' => '',
                                ]);
                                if (! $child_item_edit_link) {	// If current user has no permission to edit the child Item display the ID as text:
                                    $child_item_edit_link = $child_Item->ID;
                                }
                                $msg_group = T_('Custom fields have been replicated to the following child posts') . ':';
                                if (! isset($this->recursive_updated_messages[$msg_group])) {
                                    $this->recursive_updated_messages[$msg_group] = [];
                                }
                                $this->recursive_updated_messages[$msg_group][] = [
                                    'text' => $child_Item->get_title() . ' (' . $child_item_edit_link . ') ' . T_('in') . ' ' . $child_item_Blog->get('shortname'),
                                    'type' => 'note',
                                ];
                            }
                        }
                    }
                }

                // Display messages from recursion:
                if ($this->ID == $this->recursive_updated_items[0] &&
                    ! empty($this->recursive_updated_messages)) {	// If we have at least one message during recursive updating of the child posts and this is end of the recursion:
                    foreach ($this->recursive_updated_messages as $msg_group => $messages) {	// Reverse message to display in proper way and not as it is returned by recursion:
                        $messages = array_reverse($messages);
                        foreach ($messages as $message) {
                            if ($msg_group == 'nogroup') {	// Single message:
                                $Messages->add($message['text'], $message['type']);
                            } else {	// Grouped message:
                                $Messages->add_to_group($message['text'], $message['type'], $msg_group);
                            }
                        }
                    }
                    unset($this->recursive_updated_items);
                    unset($this->recursive_updated_messages);
                }
            }
        }

        // validate url title / slug
        if ($update_slug) { // item canonical slug wasn't updated outside from this call, if it was changed or it wasn't set yet, we must update the slugs
            if (empty($this->urltitle) || isset($this->dbchanges['post_urltitle'])) { // Url title has changed or is empty, we do need to update the slug:
                $edited_slugs = $this->update_slugs();
                $db_changed = true;
            }
        }

        $db_changed = $this->update_renderers_from_Plugins() || $db_changed;

        if (isset($this->content_is_updated)) {	// Autogenerate new excerpt ONLY when content has been updated during current request:
            $this->update_autogenerated_excerpt();
        }

        // TODO: dh> allow a plugin to cancel update here (by returning false)?
        $Plugins->trigger_event('PrependItemUpdateTransact', $params = [
            'Item' => &$this,
        ]);

        $result = true;
        // fp> note that dbchanges isn't actually 100% accurate. At this time it does include variables that actually haven't changed.
        if (isset($this->dbchanges['post_status'])
            || isset($this->dbchanges['post_title'])
            || isset($this->dbchanges['post_content'])
            || isset($this->dbchanges_flags['custom_fields'])) { // One of the fields we track in the revision history has changed:
            // Save the "current" (soon to be "old") data as a version before overwriting it in parent::dbupdate:
            // fp> TODO: actually, only the fields that have been changed should be copied to the version, the other should be left as NULL

            global $localtimenow;
            if ($create_revision !== 'no' && ($create_revision || $localtimenow - strtotime($this->last_touched_ts) > 10)) {	// Create new revision:
                $result = $this->create_revision();
            }

            $db_changed = true;
        }

        if ($auto_track_modification && (count($dbchanges) > 0 || ! empty($this->dbchanges_custom_fields))) {
            if (! isset($dbchanges['last_touched_ts'])) {	// Update last_touched_ts field only if it wasn't updated yet and the datemodified will be updated for sure:
                $this->set_last_touched_ts();
            }
            if (! isset($dbchanges['contents_last_updated_ts']) &&
              (isset($dbchanges['post_title']) ||
                isset($dbchanges['post_content']) ||
                isset($dbchanges['post_url']))) {	// If at least one of those fields has been updated then it means a content of this item has been updated:
                $this->set_contents_last_updated_ts();
            }
        }

        // Unset the following otherwise a new revision will be created if another dbupdate is called
        $this->dbchanges_custom_fields = [];
        unset($this->dbchanges_flags['custom_fields']);

        // Let's handle the slugs:
        // TODO: dh> $result handling here feels wrong: when it's true already, it should not become false (add "|| $result"?)
        // asimo>dh The result handling is in a transaction. If somehow the new slug creation fails, then the item insertion should rollback as well
        if ($result && ! empty($edited_slugs)) {	// if we have new created $edited_slugs, we have to insert it into the database:
            foreach ($edited_slugs as $edited_Slug) {
                if ($edited_Slug->ID == 0) {	// Insert only new created slugs:
                    $edited_Slug->dbinsert();
                }
            }
            if (isset($edited_slugs[0]) && $edited_slugs[0]->ID > 0) {	// Make first slug from list as main slug for this item:
                $this->set('canonical_slug_ID', $edited_slugs[0]->ID);
                $this->set('urltitle', $edited_slugs[0]->get('title'));
            }
            // Clear cached slugs in order to display new unique updated on the edit form:
            $this->slugs = null;
        }

        $parent_update = $this->dbupdate_worker($auto_track_modification);
        if ($result && ($parent_update !== false)) { // We could update the item object:
            $db_changed = $db_changed || ($parent_update !== null);

            if (isset($this->dbchanges_flags['extra_cat_IDs'])) { // Let's handle the extracats:
                $result = $this->insert_update_extracats('update');
                $db_changed = true;
            }

            if ($result && isset($this->dbchanges_flags['tags'])) { // Let's handle the tags:
                $this->insert_update_tags('update');
                $db_changed = true;
            }

            // Update last touched date of this Item and also all categories of this Item
            $this->update_last_touched_date(false, false);
        }

        // Check if there were failed nested transaction
        $result = $result && (! $DB->has_failed_transaction());
        if ($result === false) { // Update failed
            $DB->rollback();
            $db_changed = false;
        } else { // Update was successful
            if ($db_changed && ! empty($dbchanges)) {	// There were some db modification for item's content and related settings
                // (Don't clear prerendered cache and comments when for example only extra cats or tags were updated (see $this->dbchanges_flags) )

                // Delete prerendered content:
                $this->delete_prerendered_content();

                // Update comments of this Item:
                $this->update_comments();
            }

            $DB->commit();

            if (empty($this->AfterItemUpdate_is_executed)) {	// Execute this event once per request:
                $Plugins->trigger_event('AfterItemUpdate', $params = [
                    'Item' => &$this,
                    'dbchanges' => $dbchanges,
                ]);
                // Set flag to know we have already executed this plugin event:
                $this->AfterItemUpdate_is_executed = true;
            }
        }

        if ($db_changed) { // There were db modificaitons, needs cache invalidation
            // Load the blog we're in:
            $Collection = $Blog = &$this->get_Blog();

            if ($this->get_type_setting('usage') == 'content-block' &&
                empty($this->content_block_invalidate_reported)) {	// Display warning on updating of content block item:
                global $admin_url;

                // Get items where currently updated content block is included:
                $invalidated_items = $this->get_included_item_IDs($this->ID . '|' . $this->get_slugs('|'));
                $invalidated_items_num = count($invalidated_items);
                if ($invalidated_items_num > 0) {	// Delete pre-rendered cache of the found items:
                    $invalidated_items_num = $DB->query(
                        'DELETE FROM T_items__prerendering
						WHERE itpr_itm_ID IN ( ' . $DB->quote($invalidated_items) . ' )',
                        'Delete pre-rendered cache on updating content-block Item #' . $this->ID
                    );
                }

                // Display info message about invalidated cache:
                $invalidate_message = TB_('INFO: you edited a content block.') . ' '
                    . sprintf(TB_('We invalidated %d pre-rendered Items that include the content block.'), $invalidated_items_num) . ' ';
                if (check_user_perm('admin', 'normal') &&
                    check_user_perm('options', 'view')) {	// If current user has a permission to the clear tool:
                    $Messages->add($invalidate_message . sprintf(TB_('You may <a %s>invalidate the <b>complete</b> pre-rendering cache NOW</a>.'), 'href="' . $admin_url . '?ctrl=tools&amp;action=del_itemprecache&amp;' . url_crumb('tools') . '" target="_blank"'), 'note');
                } else {	// If current user has no permission to the clear tool:
                    $Messages->add($invalidate_message . TB_('Please ask administrator to invalidate the pre-rendering cache.'), 'note');
                }

                $this->content_block_invalidate_reported = true;
            }

            // BLOCK CACHE INVALIDATION:
            BlockCache::invalidate_key('cont_coll_ID', $Blog->ID); // Content has changed
            BlockCache::invalidate_key('item_ID', $this->ID); // Item has changed
            BlockCache::invalidate_key('item_' . $this->ID, 1); // Item has changed (useful for compare widget which needs to check several item_IDs, including from different collections)

            if ($this->is_intro() || $this->is_featured()) { // Content of intro or featured post has changed
                BlockCache::invalidate_key('intro_feat_coll_ID', $Blog->ID);
            }
        }

        // set_coll_ID // Settings have not changed

        return $result;
    }

    /**
     * Get IDs of items where this content-block is included
     * Used to invalidate pre-rendered content
     *
     * @param string Slugs separated by |
     * @return array
     */
    public function get_included_item_IDs($slugs)
    {
        global $DB;

        $slugs = trim($slugs, '|');
        if ($slugs === '') {	// Wrong request without slugs:
            return [];
        }

        // Get items where currently updated content block is included:
        $SQL = new SQL('Get items with included Item #' . $this->ID . ' in order to invalidate pre-rendered content');
        $SQL->SELECT('post_ID, ityp_usage, IF( ityp_usage = "content-block", GROUP_CONCAT( slug_title SEPARATOR "|" ), NULL ) AS slugs');
        $SQL->FROM('T_items__item');
        $SQL->FROM_add('INNER JOIN T_items__prerendering ON post_ID = itpr_itm_ID');
        $SQL->FROM_add('INNER JOIN T_items__type ON post_ityp_ID = ityp_ID');
        $SQL->FROM_add('INNER JOIN T_slug ON post_ID = slug_itm_ID AND slug_ID != post_tiny_slug_ID');
        $SQL->WHERE('post_content REGEXP ' . $DB->quote('\[(include|cblock):(' . $slugs . ')(:[^]]+)?\]'));
        $SQL->GROUP_BY('post_ID');
        $content_items = $DB->get_results($SQL);

        $included_items = [];
        foreach ($content_items as $content_item) {
            $included_items[] = $content_item->post_ID;
            if ($content_item->ityp_usage == 'content-block' &&
                ! empty($content_item->slugs)) {	// Try to find recursively where the content-block Item is included yet:
                $block_items = $this->get_included_item_IDs($content_item->post_ID . '|' . $content_item->slugs);
                $included_items = array_merge($included_items, $block_items);
            }
        }

        return array_unique($included_items);
    }

    /**
     * Create new slugs with validated title
     * !!!private!!! This function should be called only from Item dbupdate() function
     * @private
     * @return array Slug objects
     */
    public function update_slugs($urltitle = null)
    {
        $SlugCache = &get_SlugCache();

        if (! isset($urltitle)) {
            $urltitle = $this->urltitle;
        }

        // Split slugs by comma
        $urltitles = explode(',', $urltitle);

        $edited_slugs = [];
        foreach ($urltitles as $urltitle) {
            $urltitle = trim($urltitle);

            // create new slug
            $new_Slug = new Slug();
            // urltitle_validate may modify the urltitle !!!
            $new_Slug->set('title', urltitle_validate($urltitle, $this->title, $this->ID, false, $new_Slug->dbprefix . 'title', $new_Slug->dbprefix . 'itm_ID', $new_Slug->dbtablename, $this->locale));
            $new_Slug->set('type', 'item');
            $new_Slug->set('itm_ID', $this->ID);

            if (($urltitle != $new_Slug->get('title')) &&
                (strtolower($urltitle) == $new_Slug->get('title')) &&
                ($prev_Slug = $SlugCache->get_by_name($urltitle, false, false))) {	// Allow to use uppercase chars in slug title only if this is a single difference between requested slug title and result of urltitle_validate(),
                // and only if such slug title alredy exists in DB:
                // (such case possible after item merging when all sulgs were merged and also tiny slugs which have a format like aC8)
                $new_Slug->set('title', $urltitle);
            }

            // Check if this slug was already used by this item or not.
            // We need this check, because urltitle_validate() function will modify an existing urltitle only if it belongs to a different object
            $prev_Slug = $SlugCache->get_by_name($new_Slug->get('title'), false, false);
            if ($prev_Slug) { // A slug with this title already exists. It must belong to the same item!
                if ($prev_Slug->get('itm_ID') == $new_Slug->get('itm_ID')) {
                    $edited_slugs[] = $prev_Slug;
                    continue;
                } else { // This case should never happen, because urltitle validate check this case. It is only an extra check.
                    debug_die('The slugs table is broken');
                }
            } else { // No slug with such urltitle in DB, we can add this new one
                $edited_slugs[] = $new_Slug;
            }
        }

        return $edited_slugs;
    }

    /**
     * Update comments of this Item
     */
    public function update_comments()
    {
        global $DB;

        if (empty($this->ID)) {	// This function can works only with existing Item:
            return;
        }

        if (isset($this->previous_status)) {	// Restrict comments status by this Item status if it has been changed:
            $max_allowed_comment_status = $this->get('status');
            if ($max_allowed_comment_status == 'redirected') {	// Comments cannot have a status "Redirected", so reduce them only to "Deprecated":
                $max_allowed_comment_status = 'deprecated';
            }

            $ordered_statuses = get_visibility_statuses('ordered-index');
            $reduce_comment_status = false;
            $reduced_statuses = [];
            foreach ($ordered_statuses as $status_key => $status_order) {
                if ($status_key == $max_allowed_comment_status) {	// This status is max allowed for item's comments, Reduce all next higher statuses:
                    $reduce_comment_status = true;
                    continue;
                }
                if ($reduce_comment_status) {	// This comment status must be reduced to current status of this Item:
                    $reduced_statuses[] = $status_key;
                }
            }

            if (! empty($reduced_statuses)) {	// Reduce statuses of item's comments to current status of this Item:
                $DB->query(
                    'UPDATE T_comments
					  SET comment_status = ' . $DB->quote($max_allowed_comment_status) . '
					WHERE comment_item_ID = ' . $this->ID . '
					  AND comment_status IN ( ' . $DB->quote($reduced_statuses) . ' )',
                    'Reduce comments statutes to status of Item #' . $this->ID
                );
            }
        }
    }

    /**
     * Trigger event AfterItemDelete after calling parent method.
     *
     * @todo fp> delete related stuff: comments, cats, file links...
     *
     * @return boolean true on success
     */
    public function dbdelete()
    {
        global $DB, $Plugins;

        // remember ID, because parent method resets it to 0
        $old_ID = $this->ID;

        // Load the blog
        $Collection = $Blog = &$this->get_Blog();

        $DB->begin();

        if ($r = parent::dbdelete()) {
            // re-set the ID for the Plugin event & for a deleting of the prerendered content
            $this->ID = $old_ID;

            $DB->commit();

            $Plugins->trigger_event('AfterItemDelete', $params = [
                'Item' => &$this,
            ]);

            $this->ID = 0;

            // BLOCK CACHE INVALIDATION:
            BlockCache::invalidate_key('cont_coll_ID', $Blog->ID); // Content has changed
            BlockCache::invalidate_key('item_ID', $old_ID); // Item has deleted
            BlockCache::invalidate_key('item_' . $old_ID, 1); // Item has deleted (useful for compare widget which needs to check several item_IDs, including from different collections)

            if ($this->is_intro() || $this->is_featured()) { // Content of intro or featured post has changed
                BlockCache::invalidate_key('intro_feat_coll_ID', $Blog->ID);
            }

            // set_coll_ID // Settings have not changed
        } else {
            $DB->rollback();
        }

        return $r;
    }

    /**
     * Update excerpt but ONLY if it is autogenerated
     *
     * This can be executed if $this->set( 'content', ... ) has been called before
     */
    public function update_autogenerated_excerpt()
    {
        if ($this->get('excerpt_autogenerated')) {	// We want to auto-generate excerpts for this Item:
            if (! empty($this->content_is_updated)) {	// Clear prerendered content to generate excerpt from new content because the content has been really changed during current request:
                $this->delete_prerendered_content();
            }

            $this->set_param('excerpt', 'string', $this->get_autogenerated_excerpt());
        }
    }

    /**
     * Get autogenerated excerpt, derived from {@link Item::$content}.
     *
     * @param int Maximum length
     * @param string Tail to use, when string gets cropped. Its length gets
     *               substracted from the total length (with HTML entities
     *               being decoded). Default is "&hellip;" (HTML entity)
     * @return string
     */
    // fp>yura: please check why this function is very different from get_content_excerpt() and use best code for both
    public function get_autogenerated_excerpt($maxlen = 254, $tail = '&hellip;')
    {
        // Autogenerated excerpts should NEVER show anything after [teaserbreak] or after [pagebreak]
        $content_parts = $this->get_content_parts([
            'disppage' => 1,
        ]);
        $first_content_part = array_shift($content_parts);

        // Render inline tags to HTML code, except of inline file tags because they are removed below:
        $first_content_part = $this->render_inline_tags($first_content_part, [
            'render_tag_image' => false,
            'render_tag_file' => false,
            'render_tag_inline' => false,
            'render_tag_video' => false,
            'render_tag_audio' => false,
            'render_tag_thumbnail' => false,
            'render_tag_folder' => false,
            'render_tag_item_link' => false,
            'render_tag_item_field' => false,
            'render_tag_item_titlelink' => false,
            'render_tag_item_url' => false,
            'render_tag_item_subscribe' => false,
            'render_tag_item_emailcapture' => false,
            'render_tag_item_compare' => false,
            'render_tag_item_fields' => false,
            'render_tag_switcher' => false,
            'render_tag_switchable_div' => false,
        ]);

        // Remove shorttags from excerpt // [image:123:caption:.class] [file:123:caption:.class] [inline:123:.class] etc:
        $first_content_part = preg_replace('/\[[a-z]+:[^\]`]*\]/i', '', $first_content_part);

        // Clean up rendering errors from autogenerated excerpt:
        $first_content_part = clear_rendering_errors($first_content_part);

        return excerpt($first_content_part, $maxlen, $tail);
    }

    /**
     * Insert/Update post extracats
     *
     * @param string 'insert' | 'update'
     * @return boolean true on success | false one failure
     */
    public function insert_update_extracats($mode)
    {
        global $DB, $Messages, $Settings, $Blog;

        if (! is_null($this->extra_cat_IDs)) { // Okay the extra cats are defined:
            $DB->begin('SERIALIZABLE');

            $meta_count = $DB->get_var('SELECT count( cat_ID ) FROM T_categories WHERE cat_meta = 1 AND cat_ID IN (' . implode(',', $this->extra_cat_IDs) . ')');
            if ($meta_count > 0) {
                $DB->rollback();
                $Messages->add(T_('Could not set the selected categories!'), 'error');
                return false;
            }

            // Load item orders per categories before delete them from DB:
            $this->load_orders();

            if ($mode == 'update') {
                // delete previous extracats:
                $DB->query('DELETE FROM T_postcats WHERE postcat_post_ID = ' . $this->ID, 'Delete previous extracats of Item #' . $this->ID);
            }

            // Allow to use field "postcat_order" only since 12972 DB version:
            $postcat_order_field = ($Settings->get('db_version') >= 12972 ? ', postcat_order' : '');

            // insert new extracats:
            $query = 'INSERT INTO T_postcats ( postcat_post_ID, postcat_cat_ID' . $postcat_order_field . ' ) VALUES ';
            foreach ($this->extra_cat_IDs as $extra_cat_ID) {
                $query .= '( ' . $this->ID . ', ' . $extra_cat_ID;
                if (! empty($postcat_order_field)) {	// Insert item order per category only when this field exists in DB:
                    $query .= ', ' . $DB->quote($this->get_order($extra_cat_ID));
                }
                $query .= ' ),';
            }
            $query = substr($query, 0, strlen($query) - 1);
            $DB->query($query, 'Insert new extracats for Item #' . $this->ID);

            $DB->commit();
        }

        return true;
    }

    /**
     * Save tags to DB
     *
     * @param string 'insert' | 'update'
     */
    public function insert_update_tags($mode)
    {
        global $DB;

        if (isset($this->tags)) { // Okay the tags are defined:
            $DB->begin();

            if ($mode == 'update') {	// delete previous tag associations:
                // Note: actual tags never get deleted
                $DB->query('DELETE FROM T_items__itemtag
											WHERE itag_itm_ID = ' . $this->ID, 'delete previous tags');
            }

            if (! empty($this->tags)) {
                // Find the tags that are already in the DB
                $query = 'SELECT tag_name
										FROM T_items__tag
									 WHERE tag_name IN (' . $DB->quote($this->tags) . ')';
                $existing_tags = $DB->get_col($query, 0, 'Find existing tags');

                $new_tags = array_diff($this->tags, $existing_tags);

                if (! empty($new_tags)) { // insert new tags:
                    $query = "INSERT INTO T_items__tag( tag_name ) VALUES ";
                    foreach ($new_tags as $tag) {
                        $query .= '( ' . $DB->quote($tag) . ' ),';
                    }
                    $query = substr($query, 0, strlen($query) - 1);
                    $DB->query($query, 'insert new tags');
                }

                // ASSOC:
                $query = 'INSERT INTO T_items__itemtag( itag_itm_ID, itag_tag_ID )
								  SELECT ' . $this->ID . ', tag_ID
									  FROM T_items__tag
									 WHERE tag_name IN (' . $DB->quote($this->tags) . ')';
                $DB->query($query, 'Make tag associations!');
            }

            $DB->commit();
        }
    }

    /**
     * Get the User who is assigned to the Item.
     *
     * @return User|null NULL if no user is assigned.
     */
    public function get_assigned_User()
    {
        if (! isset($this->assigned_User) && isset($this->assigned_user_ID)) {
            $UserCache = &get_UserCache();
            $this->assigned_User = &$UserCache->get_by_ID($this->assigned_user_ID);
        }

        return $this->assigned_User;
    }

    /**
     * Get the User who edited the Item last time.
     *
     * @return User
     */
    public function &get_lastedit_User()
    {
        if (is_null($this->lastedit_User)) {
            $UserCache = &get_UserCache();
            $this->lastedit_User = &$UserCache->get_by_ID($this->lastedit_user_ID, false, false);
        }

        return $this->lastedit_User;
    }

    /**
     * Get the User who created the Item.
     *
     * @return User
     */
    public function &get_creator_User()
    {
        if (is_null($this->creator_User)) {
            $UserCache = &get_UserCache();
            $this->creator_User = &$UserCache->get_by_ID($this->creator_user_ID);
            $this->Author = &$this->creator_User;  // deprecated
        }

        return $this->creator_User;
    }

    /**
     * Get login of the User who created the Item.
     *
     * @return string login
     */
    public function get_creator_login()
    {
        $this->get_creator_User();
        if (is_null($this->creator_user_login) && ! is_null($this->creator_User)) {
            $this->creator_user_login = $this->creator_User->login;
        }
        return $this->creator_user_login;
    }

    /**
     * Execute or schedule various notifications:
     * - notifications for moderators
     * - notifications for subscribers
     * - pings
     *
     * @param integer User ID who executed the action which will be notified, or NULL if it was executed by current logged in User
     * @param boolean TRUE if it is notification about new item, FALSE - for edited item
     * @param boolean|string Force sending notifications for members:
     *                       false   - Auto mode depending on current item statuses
     *                       'skip'  - Skip notifications
     *                       'force' - Force notifications
     *                       'mark'  - Change DB flag to "notified/sent" but do NOT actually send notifications
     * @param boolean|string Force sending notifications for community (use same values of second param)
     * @param boolean|string Force sending outbound pings (use same values of second param)
     * @return boolean TRUE on success
     */
    public function handle_notifications($executed_by_userid = null, $is_new_item = false, $force_members = false, $force_community = false, $force_pings = false)
    {
        global $Settings, $Messages, $localtimenow, $Debuglog, $DB;

        if (empty($this->ID)) {	// Don't send notifications for not created Item:
            $Debuglog->add('Item->handle_notifications() : Item is NOT saved in DB', 'notifications');
            return false;
        }

        // Immediate notifications? Asynchronous? Off?
        $notifications_mode = $Settings->get('outbound_notifications_mode');

        if ($notifications_mode == 'off') {	// Don't send any notifications nor pings:
            $Debuglog->add('Item->handle_notifications() : Notifications are turned OFF!', 'notifications');
            return false;
        }

        if ($executed_by_userid === null && is_logged_in()) {	// Use current user by default:
            global $current_User;
            $executed_by_userid = $current_User->ID;
        }

        // FIRST: Moderators need to be notified immediately, even if the post is a draft/review and/or has an issue_date in the future.
        // fp> NOTE: for simplicity, for now, we will NOT make a scheduled job for this (but we will probably do so in the future)
        $Debuglog->add('Item->handle_notifications() : Moderator notifications will always be immediate (never scheduled)', 'notifications');
        // Send email notifications to users who can moderate this item:
        $already_notified_user_IDs = $this->send_moderation_emails($executed_by_userid, $is_new_item);

        // SECOND: Send email notification to assigned user
        $Blog = &$this->get_Blog();
        if ($Blog->get_setting('use_workflow') && $this->assigned_to_new_user && ! empty($this->assigned_user_ID)) {
            $already_notified_user_IDs = array_merge($already_notified_user_IDs, $this->send_assignment_notification($executed_by_userid));
        }

        // THIRD: Subscribers may be notified asynchornously... and that is a even a requirement if the post has an issue_date in the future.

        $notified_flags = [];
        if ($force_members == 'mark') {	// Only change DB flag to "members_notified" but do NOT actually send notifications:
            $force_members = false;
            $notified_flags[] = 'members_notified';
            $this->display_notification_message(T_('Marking email notifications for members as sent.'));
        }
        if ($force_community == 'mark') {	// Only change DB flag to "community_notified" but do NOT actually send notifications:
            $force_community = false;
            $notified_flags[] = 'community_notified';
            $this->display_notification_message(T_('Marking email notifications for community as sent.'));
        }
        if ($force_pings == 'mark') {	// Only change DB flag to "pings_sent" but do NOT actually send pings:
            $force_pings = false;
            $notified_flags[] = 'pings_sent';
            $this->display_notification_message(T_('Marking pings as sent.'));
        }
        if (! empty($notified_flags)) {	// Save the marked processing status to DB:
            $this->set('notifications_flags', $notified_flags);
            $this->dbupdate(false, false, false);
        }

        // Instead of the above we now check the flags:
        if (($force_members != 'force' && $force_community != 'force' && $force_pings != 'force') &&
            $this->check_notifications_flags(['members_notified', 'community_notified', 'pings_sent'])) {	// All possible notifications have already been sent and no forcing for any notification:
            $this->display_notification_message(T_('All possible notifications have already been sent: skipping notifications...'));
            $Debuglog->add('Item->handle_notifications() : All possible notifications have already been sent: skipping notifications...', 'notifications');
            return false;
        }

        // IMMEDIATE vs ASYNCHRONOUS sending:
        $DB->begin('SERIALIZABLE');

        if ($notifications_mode == 'immediate' && strtotime($this->issue_date) <= $localtimenow) {	// We want to send the notifications immediately (can only be done if post does not have an issue_date in the future):
            $Debuglog->add('Item->handle_notifications() : Sending immediate Pings & Subscriber notifications', 'notifications');

            // Send outbound pings: (will only do something if visibility is 'public')
            $this->send_outbound_pings($force_pings);

            // Send email notifications to users who want to receive them for the collection of this item: (will be different recipients depending on visibility)
            $notified_flags = $this->send_email_notifications($executed_by_userid, $is_new_item, $already_notified_user_IDs, $force_members, $force_community);

            // Record that we have just notified the members and/or community:
            $this->set('notifications_flags', $notified_flags);

            // Record that processing has been done:
            $this->set('notifications_status', 'finished');
        } elseif ($this->get('notifications_status') != 'todo' && $this->get('notifications_status') != 'started') {	// We want asynchronous post processing. (This automatically applies to posts with issue_date in the future):
            if ($notifications_mode == 'immediate') {	// We ended up here because the issue_date is in the future BUT notifications are not sent to asynchronoys...
                // This means we will schedule a job but it will never get executed until the admin turns on async notifications:
                $Messages->add(sprintf(
                    T_('You just published a post in the future. You must set your notifications to <a %s>Asynchronous</a> so that b2evolution can send out notification when this post goes live.'),
                    'href="http://b2evolution.net/man/after-each-post-settings" target="_blank"'
                ), 'warning');
            }

            // CREATE CRON JOB OBJECT:

            // Note: in case of successive edits of a post we may create many cron jobs for it.
            // It will be the responsibility of the cron jobs to detect if another one is already running and not execute twice or more times concurrently.

            $Debuglog->add('Item->handle_notifications() : Scheduling notifications through a cron job', 'notifications');

            load_class('/cron/model/_cronjob.class.php', 'Cronjob');
            $item_Cronjob = new Cronjob();

            // start datetime. We do not want to ping before the post is effectively published:
            $item_Cronjob->set('start_datetime', $this->issue_date);

            // no repeat.

            // key:
            $item_Cronjob->set('key', 'send-post-notifications');

            // params: specify which post this job is supposed to send notifications for:
            $item_Cronjob->set('params', [
                'item_ID' => $this->ID,
                'executed_by_userid' => $executed_by_userid,
                'is_new_item' => $is_new_item,
                'already_notified_user_IDs' => $already_notified_user_IDs,
                'force_members' => $force_members,
                'force_community' => $force_community,
                'force_pings' => $force_pings,
            ]);

            // Save cronjob to DB:
            if ($item_Cronjob->dbinsert()) {
                $this->display_notification_message(T_('Scheduling Pings & Subscriber email notifications.'));

                // Memorize the cron job ID which is going to handle this post:
                $this->set('notifications_ctsk_ID', $item_Cronjob->ID);

                // Record that processing has been scheduled:
                $this->set('notifications_status', 'todo');
            }
        }

        // Save the new processing status to DB, but do not update last edited by user, slug or child custom fields:
        if ($this->dbupdate(false, false, false)) {
            $DB->commit();
            return true;
        } else {
            $DB->rollback();
            return false;
        }
    }

    /**
     * Get item moderators
     *
     * @param string Setting name of notification: 'notify_post_moderation', 'notify_edit_pst_moderation', 'notify_post_proposed'
     * @param integer User ID who executed the action which will be notified, or NULL if it was executed by current logged in User
     * @param string|false Additional check with collection subscription: 'sub_items', 'sub_items_mod', 'sub_comments'
     * @return array Key - User ID, Value - perm level
     */
    public function get_moderators($notify_setting_name, $executed_by_userid = null, $check_coll_subscription = false)
    {
        global $Settings, $DB;

        if ($executed_by_userid === null && is_logged_in()) {	// Use current user by default:
            global $current_User;
            $executed_by_userid = $current_User->ID;
        }

        $notify_condition = 'uset_value IS NOT NULL AND uset_value <> "0"';
        if ($Settings->get('def_' . $notify_setting_name)) {
            $notify_condition = '( uset_value IS NULL OR ( ' . $notify_condition . ' ) )';
        }
        if ($check_coll_subscription !== false) {	// Notify moderators which selected to be notified per collection (if it is enabled by collection setting):
            $notify_condition = '( ' . $check_coll_subscription . ' = 1 OR ( ' . $notify_condition . ' ) )';
        }

        // Select user_ids with the corresponding item edit permission on this item's blog
        $SQL = new SQL('Get moderators "' . $notify_setting_name . '" for Item #' . $this->ID);
        $SQL->SELECT('user_ID, IF( grp_perm_blogs = "editall" OR user_ID = blog_owner_user_ID, "all", IF( IFNULL( bloguser_perm_edit + 0, 0 ) > IFNULL( bloggroup_perm_edit + 0, 0 ), bloguser_perm_edit, bloggroup_perm_edit ) ) as perm');
        $SQL->FROM('T_users');
        $SQL->FROM_add('LEFT JOIN T_blogs ON ( blog_ID = ' . $this->get_blog_ID() . ' )');
        $SQL->FROM_add('LEFT JOIN T_coll_user_perms ON (blog_advanced_perms <> 0 AND user_ID = bloguser_user_ID AND bloguser_blog_ID = ' . $this->get_blog_ID() . ' )');
        $SQL->FROM_add('LEFT JOIN T_coll_group_perms ON (blog_advanced_perms <> 0 AND user_grp_ID = bloggroup_group_ID AND bloggroup_blog_ID = ' . $this->get_blog_ID() . ' )');
        $SQL->FROM_add('LEFT JOIN T_users__usersettings ON uset_user_ID = user_ID AND uset_name = "' . $notify_setting_name . '"');
        $SQL->FROM_add('LEFT JOIN T_groups ON grp_ID = user_grp_ID');
        $SQL->FROM_add('LEFT JOIN T_subscriptions ON sub_coll_ID = blog_ID AND sub_user_ID = user_ID');
        $SQL->WHERE($notify_condition);
        $SQL->WHERE_and('user_status IN ( "activated", "autoactivated", "manualactivated" )');
        $SQL->WHERE_and('( bloguser_perm_edit IS NOT NULL AND bloguser_perm_edit <> "no" AND bloguser_perm_edit <> "own" )
				OR ( bloggroup_perm_edit IS NOT NULL AND bloggroup_perm_edit <> "no" AND bloggroup_perm_edit <> "own" )
				OR ( grp_perm_blogs = "editall" ) OR ( user_ID = blog_owner_user_ID )');
        if ($executed_by_userid !== null) {	// Don't notify the user who just created/updated this post:
            $SQL->WHERE_and('user_ID != ' . $DB->quote($executed_by_userid));
        }

        return $DB->get_assoc($SQL);
    }

    /**
     * Send "post may need moderation" notifications for those users who have permission to moderate this post and would like to receive these notifications.
     *
     * @param integer User ID who executed the action which will be notified, or NULL if it was executed by current logged in User
     * @param boolean TRUE if it is notification about new item, FALSE - for edited item
     * @return array the notified user ids
     */
    public function send_moderation_emails($executed_by_userid = null, $is_new_item = false)
    {
        global $Messages;

        $notify_moderation_setting_name = ($is_new_item ? 'notify_post_moderation' : 'notify_edit_pst_moderation');
        // Notify moderators which selected to be notified per collection (if it is enabled by collection setting):
        $check_coll_subscription = (! $is_new_item && $this->get_Blog()->get_setting('allow_item_mod_subscriptions') ? 'sub_items_mod' : false);

        // Get all users who are post moderators in this Item's blog:
        $post_moderators = $this->get_moderators($notify_moderation_setting_name, $executed_by_userid, $check_coll_subscription);

        $post_creator_User = &$this->get_creator_User();
        if (isset($post_moderators[$post_creator_User->ID])) {	// Don't notify the user who just created this Item:
            unset($post_moderators[$post_creator_User->ID]);
        }

        // Collect all notified User IDs in this array:
        $notified_user_IDs = [];

        if (empty($post_moderators)) { // There are no moderator users who would like to receive notificaitons
            return $notified_user_IDs;
        }

        $post_creator_level = $post_creator_User->level;
        $UserCache = &get_UserCache();
        $UserCache->load_list(array_keys($post_moderators));

        foreach ($post_moderators as $moderator_ID => $perm) {
            $moderator_User = $UserCache->get_by_ID($moderator_ID);
            if (($perm == 'lt') && ($moderator_User->level <= $post_creator_level)) { // User has no permission moderate this post
                continue;
            }
            if (($perm == 'le') && ($moderator_User->level < $post_creator_level)) { // User has no permission moderate this post
                continue;
            }

            $moderator_user_Group = $moderator_User->get_Group();
            $notify_full = $moderator_user_Group->check_perm('post_moderation_notif', 'full');

            $email_template_params = [
                'locale' => $moderator_User->locale,
                'notify_full' => $notify_full,
                'Item' => $this,
                'recipient_User' => $moderator_User,
                'notify_type' => 'moderator',
                'is_new_item' => $is_new_item,
            ];

            locale_temp_switch($moderator_User->locale);

            if ($this->status == 'draft' || $this->status == 'review') {
                /* TRANS: Subject of the mail to send on new posts to moderators. First %s is blog name, the second %s is the item's title. */
                $subject = T_('[%s] New post awaiting moderation: "%s"');
            } else {
                /* TRANS: Subject of the mail to send on new posts to moderators. First %s is blog name, the second %s is the item's title. */
                $subject = T_('[%s] New post may need moderation: "%s"');
            }
            $subject = sprintf($subject, $this->Blog->get('shortname'), $this->get('title'));

            // Send the email:
            if (send_mail_to_User($moderator_ID, $subject, 'post_new', $email_template_params)) {	// A send notification email request to the user with $moderator_ID ID was processed:
                $notified_user_IDs[] = $moderator_ID;
            }

            locale_restore_previous();
        }

        // Record that we have notified the moderators (for info only):
        $this->set('notifications_flags', 'moderators_notified');
        // Save the new processing status to DB, but do not update last edited by user, slug or child custom fields:
        $this->dbupdate(false, false, false);

        $this->display_notification_message(sprintf(T_('Sending %d email notifications to moderators.'), count($notified_user_IDs)));

        return $notified_user_IDs;
    }

    /**
     * Send "post proposed change" notifications for those users who have permission to moderate this post and would like to receive these notifications.
     *
     * @param integer Version ID of new proposed change
     */
    public function send_proposed_change_notification($iver_ID)
    {
        global $Messages, $current_User;

        if (! is_logged_in()) {	// Only logged in user can propose a change
            return;
        }

        // Get all users who are post moderators in this Item's blog:
        $post_moderators = $this->get_moderators('notify_post_proposed');

        if (empty($post_moderators)) { // There are no moderator users who would like to receive notificaitons
            return;
        }

        // Clear revision in order to use current data in the email message:
        $this->clear_revision();

        // Collect all notified User IDs in this array:
        $notified_users_num = 0;

        $post_creator_User = &$this->get_creator_User();
        $post_creator_level = $post_creator_User->level;
        $UserCache = &get_UserCache();
        $UserCache->load_list(array_keys($post_moderators));

        foreach ($post_moderators as $moderator_ID => $perm) {
            $moderator_User = $UserCache->get_by_ID($moderator_ID);
            if (($perm == 'lt') && ($moderator_User->level <= $post_creator_level)) { // User has no permission moderate this post
                continue;
            }
            if (($perm == 'le') && ($moderator_User->level < $post_creator_level)) { // User has no permission moderate this post
                continue;
            }

            $moderator_user_Group = $moderator_User->get_Group();

            $email_template_params = [
                'iver_ID' => $iver_ID,
                'Item' => $this,
                'recipient_User' => $moderator_User,
                'proposer_User' => $current_User,
            ];

            locale_temp_switch($moderator_User->locale);

            // TRANS: Subject of the mail to send on a post proposed change to moderators. First %s is blog name, the second %s is the item's title.
            $subject = sprintf(T_('[%s] New change was proposed on: "%s"'), $this->get_Blog()->get('shortname'), $this->get('title'));

            // Send the email:
            if (send_mail_to_User($moderator_ID, $subject, 'post_proposed_change', $email_template_params)) {	// A send notification email request to the user with $moderator_ID ID was processed:
                $notified_users_num++;
            }

            locale_restore_previous();
        }

        $this->display_notification_message(sprintf(T_('Sending %d email notifications to moderators.'), $notified_users_num));
    }

    /**
     * Send "post assignment" notifications for user who have been assigned to this post and would like to receive these notifications.
     *
     * @param integer User ID who executed the action which will be notified, or NULL if it was executed by current logged in User
     * @return array the notified user ids
     */
    public function send_assignment_notification($executed_by_userid = null)
    {
        global $Messages, $UserSettings;

        $notified_user_IDs = [];

        if ($executed_by_userid === null && is_logged_in()) {	// Use current user by default:
            global $current_User;
            $executed_by_userid = $current_User->ID;
        }

        if ($this->assigned_user_ID && $executed_by_userid != $this->assigned_user_ID) { // Item has assigned user and the assigned user is not the one who created/updated this post:
            $UserCache = &get_UserCache();
            $principal_User = $UserCache->get_by_ID($executed_by_userid, false, false);
            $assigned_User = $this->get_assigned_User();

            if ($assigned_User &&
                    $UserSettings->get('notify_post_assignment', $assigned_User->ID) &&
                    $assigned_User->check_perm('blog_can_be_assignee', 'view', false, $this->get_blog_ID())) {	// Assigned user wants to receive post assignment notifications and can be assigned to items of this Item's collection:
                $user_Group = $assigned_User->get_Group();
                $notify_full = $user_Group->check_perm('post_assignment_notif', 'full');

                $email_template_params = [
                    'locale' => $assigned_User->locale,
                    'notify_full' => $notify_full,
                    'Item' => $this,
                    'principal_User' => $principal_User,
                    'recipient_User' => $assigned_User,
                ];

                locale_temp_switch($assigned_User->locale);

                /* TRANS: Subject of the mail to send on assignment of posts to a user. First %s is blog name, the second %s is the item's title. */
                $subject = T_('[%s] Post assignment: "%s"');
                $subject = sprintf($subject, $this->Blog->get('shortname'), $this->get('title'));

                // Send the email:
                if (send_mail_to_User($assigned_User->ID, $subject, 'post_assignment', $email_template_params)) {	// A send notification email request to the assigned user was processed:
                    $notified_user_IDs[] = $assigned_User->ID;
                    $this->display_notification_message(T_('Sending email notification to assigned user.'));
                }

                locale_restore_previous();
            }
        }

        return $notified_user_IDs;
    }

    /**
     * Send email notifications to subscribed users
     *
     * @todo fp>> shall we notify suscribers of blog were this is in extra-cat? blueyed>> IMHO yes.
     *
     * @param integer User ID who executed the action which will be notified, or NULL if it was executed by current logged in User
     * @param boolean TRUE if it is notification about new item, FALSE - for edited item
     * @param array Already notified user ids, or NULL if it is not the case
     * @param boolean|string Force sending notifications for members:
     *                       false - Auto mode depending on current item statuses
     *                       'skip' - Skip notifications
     *                       'force' - Force notifications
     * @param boolean|string Force sending notifications for community (use same values of third param)
     * @param boolean|string 'cron_job' - to log messages for cron job, FALSE - to don't log
     * @return array Notified flags: 'members_notified', 'community_notified'
     */
    public function send_email_notifications($executed_by_userid = null, $is_new_item = false, $already_notified_user_IDs = null, $force_members = false, $force_community = false, $log_messages = false)
    {
        global $DB, $debug, $Messages, $Debuglog;

        if ($executed_by_userid === null && is_logged_in()) {	// Use current user by default:
            global $current_User;
            $executed_by_userid = $current_User->ID;
        }

        $edited_Blog = &$this->get_Blog();

        if (! $edited_Blog->get_setting('allow_subscriptions')) {	// Subscriptions not enabled!
            $this->display_notification_message(T_('Skipping email notifications to subscribers because subscriptions are turned Off for this collection.'), $log_messages);
            return [];
        }

        if (! $this->notifications_allowed()) {	// Don't send notifications about some post/usages like "special":
            // Note: this is a safety but this case should never happen, so don't make translators work on this:
            $this->display_notification_message('This post type/usage cannot support notifications: skipping notifications...', $log_messages);
            return [];
        }

        if (! in_array($this->get('status'), ['protected', 'community', 'published'])) {	// Don't send notifications about items with not allowed status:
            $status_titles = get_visibility_statuses('', []);
            $status_title = isset($status_titles[$this->get('status')]) ? $status_titles[$this->get('status')] : $this->get('status');
            $this->display_notification_message(sprintf(T_('Skipping email notifications to subscribers because status is still: %s.'), $status_title), $log_messages);
            return [];
        }

        if ($force_members == 'skip' && $force_community == 'skip') {	// Skip subscriber notifications because of it is forced by param:
            $this->display_notification_message(T_('Skipping email notifications to subscribers.'), $log_messages);
            return [];
        }

        if ($force_members == 'force' && $force_community == 'force') {	// Force to members and community:
            $this->display_notification_message(T_('Force sending email notifications to subscribers...'), $log_messages);
        } elseif ($force_members == 'force') {	// Force to members only:
            $this->display_notification_message(T_('Force sending email notifications to subscribed members...'), $log_messages);
        } elseif ($force_community == 'force') {	// Force to community only:
            $this->display_notification_message(T_('Force sending email notifications to other subscribers...'), $log_messages);
        } else {	// Check if email notifications can be sent for this item currently:
            // Some post usages should not trigger notifications to subscribers (moderators are notified earlier in the process, so they will be notified)
            // fp> I think the only usage that makes sense to send automatic notifications to subscribers is "Post"
            if ($this->get_type_setting('usage') != 'post') {	// Don't send outbound pings for items that are not regular posts:
                $this->display_notification_message(T_('This post type/usage doesn\'t need notifications by default: skipping notifications...'), $log_messages);
                return [];
            }
        }

        $notify_members = false;
        $notify_community = false;

        if ($this->get('status') == 'protected') {	// If the post is visible for members only...
            if ($force_members == 'force' || (! $this->check_notifications_flags('members_notified') && $this->get_type_setting('usage') == 'post')) {	// Members have not been notified yet OR Force sending, do so:
                $notify_members = true;
            }
        } elseif ($this->get('status') == 'community' || $this->get('status') == 'published') {	// If the post is visible to the community or is public...
            if ($force_members == 'force' || (! $this->check_notifications_flags('members_notified') && $this->get_type_setting('usage') == 'post')) {	// Members have not been notified yet OR Force sending, do so:
                $notify_members = true;
            }
            if ($force_community == 'force' || (! $this->check_notifications_flags('community_notified') && $this->get_type_setting('usage') == 'post')) {	// Community have not been notified yet OR Force sending, do so:
                $notify_community = true;
            }
        }

        if (! $notify_members && ! $notify_community) {	// Everyone has already been notified, nothing to do:
            $this->display_notification_message(T_('Skipping email notifications to subscribers because they were already notified.'), $log_messages);
            return [];
        }

        if ($notify_members && $force_members == 'skip') {	// Skip email notifications to members because it is forced by param:
            $this->display_notification_message(T_('Skipping email notifications to subscribed members.'), $log_messages);
            $notify_members = false;
        }
        if ($notify_community && $force_community == 'skip') {	// Skip email notifications to community because it is forced by param:
            $this->display_notification_message(T_('Skipping email notifications to other subscribers.'), $log_messages);
            $notify_community = false;
        }

        // Set flags what really users will be notified below:
        $notified_flags = [];
        if ($notify_members) {	// If members should be notified:
            $notified_flags[] = 'members_notified';
        }
        if ($notify_community) {	// If community should be notified:
            $notified_flags[] = 'community_notified';
        }

        if (! $notify_members && ! $notify_community) {	// All notifications are skipped by requested params:
            return $notified_flags;
        }

        $Debuglog->add('Ready to send notifications to members? : ' . ($notify_members ? 'Yes' : 'No'), 'notifications');
        $Debuglog->add('Ready to send notifications to community? : ' . ($notify_community ? 'Yes' : 'No'), 'notifications');

        $notify_users = [];

        // Get list of users who want to be notified when his login is mentioned in the item content by @user's_login:
        $mentioned_user_IDs = get_mentioned_user_IDs('item', $this->get('content'), $already_notified_user_IDs);
        foreach ($mentioned_user_IDs as $mentioned_user_ID) {
            $notify_users[$mentioned_user_ID] = 'post_mentioned';
        }

        // Get list of users who want to be notified:
        // TODO: also use extra cats/blogs??
        $sql = 'SELECT user_ID
				FROM (
					SELECT DISTINCT sub_user_ID AS user_ID
					FROM T_subscriptions
					INNER JOIN T_users ON user_ID = sub_user_ID
					WHERE sub_coll_ID = ' . $this->get_blog_ID() . '
					AND sub_items <> 0
					AND user_status IN ( "activated", "autoactivated", "manualactivated" )

					UNION

					SELECT user_ID
					FROM T_coll_settings AS opt
					INNER JOIN T_blogs ON ( blog_ID = opt.cset_coll_ID AND blog_advanced_perms = 1 )
					INNER JOIN T_coll_settings AS sub ON ( sub.cset_coll_ID = opt.cset_coll_ID AND sub.cset_name = "allow_subscriptions" AND sub.cset_value = 1 )
					LEFT JOIN T_coll_group_perms ON ( bloggroup_blog_ID = opt.cset_coll_ID AND bloggroup_ismember = 1 )
					LEFT JOIN T_users ON ( user_grp_ID = bloggroup_group_ID )
					LEFT JOIN T_subscriptions ON ( sub_coll_ID = opt.cset_coll_ID AND sub_user_ID = user_ID )
					WHERE opt.cset_coll_ID = ' . $this->get_blog_ID() . '
						AND opt.cset_name = "opt_out_subscription"
						AND opt.cset_value = 1
						AND NOT user_ID IS NULL
						AND ( ( sub_items IS NULL OR sub_items = 1 ) )
						AND user_status IN ( "activated", "autoactivated", "manualactivated" )

					UNION

					SELECT sug_user_ID
					FROM T_coll_settings AS opt
					INNER JOIN T_blogs ON ( blog_ID = opt.cset_coll_ID AND blog_advanced_perms = 1 )
					INNER JOIN T_coll_settings AS sub ON ( sub.cset_coll_ID = opt.cset_coll_ID AND sub.cset_name = "allow_subscriptions" AND sub.cset_value = 1 )
					LEFT JOIN T_coll_group_perms ON ( bloggroup_blog_ID = opt.cset_coll_ID AND bloggroup_ismember = 1 )
					LEFT JOIN T_users__secondary_user_groups ON ( sug_grp_ID = bloggroup_group_ID )
					LEFT JOIN T_subscriptions ON ( sub_coll_ID = opt.cset_coll_ID AND sub_user_ID = sug_user_ID )
					LEFT JOIN T_users ON ( user_ID = sug_user_ID )
					WHERE opt.cset_coll_ID = ' . $this->get_blog_ID() . '
						AND opt.cset_name = "opt_out_subscription"
						AND opt.cset_value = 1
						AND NOT sug_user_ID IS NULL
						AND ( ( sub_items IS NULL OR sub_items = 1 ) )
						AND user_status IN ( "activated", "autoactivated", "manualactivated" )

					UNION

					SELECT bloguser_user_ID
					FROM T_coll_settings AS opt
					INNER JOIN T_blogs ON ( blog_ID = opt.cset_coll_ID AND blog_advanced_perms = 1 )
					INNER JOIN T_coll_settings AS sub ON ( sub.cset_coll_ID = opt.cset_coll_ID AND sub.cset_name = "allow_subscriptions" AND sub.cset_value = 1 )
					LEFT JOIN T_coll_user_perms ON ( bloguser_blog_ID = opt.cset_coll_ID AND bloguser_ismember = 1 )
					LEFT JOIN T_subscriptions ON ( sub_coll_ID = opt.cset_coll_ID AND sub_user_ID = bloguser_user_ID )
					LEFT JOIN T_users ON ( user_ID = sub_user_ID )
					WHERE opt.cset_coll_ID = ' . $this->get_blog_ID() . '
						AND opt.cset_name = "opt_out_subscription"
						AND opt.cset_value = 1
						AND NOT bloguser_user_ID IS NULL
						AND ( ( sub_items IS NULL OR sub_items = 1 ) )
						AND user_status IN ( "activated", "autoactivated", "manualactivated" )
				) AS users
				WHERE NOT user_ID IS NULL';

        if (! empty($already_notified_user_IDs)) {
            $sql .= ' AND user_ID NOT IN ( ' . implode(',', $already_notified_user_IDs) . ' )';
        }
        if ($executed_by_userid !== null) {
            $sql .= ' AND user_ID != ' . $DB->quote($executed_by_userid);
        }

        $notify_list = $DB->get_col($sql, 0, 'Get list of users who want to be notified (and have not yet been notified) about new items on collection #' . $this->get_blog_ID());

        // Preprocess list:
        foreach ($notify_list as $notify_user_ID) {
            if (! isset($notify_users[$notify_user_ID])) {	// Don't rewrite a notify type if user already is notified by other type before:
                $notify_users[$notify_user_ID] = 'subscription';
            }
        }

        $notify_user_IDs = array_keys($notify_users);

        $Debuglog->add('Number of users who want to be notified (and have not yet been notified) about new items on collection #' . $this->get_blog_ID() . ' = ' . count($notify_users), 'notifications');
        $Debuglog->add('First 10 user IDs: ' . implode(',', array_slice($notify_user_IDs, 0, 10)), 'notifications');

        // Load all users who will be notified:
        $UserCache = &get_UserCache();
        $UserCache->load_list($notify_user_IDs);

        $members_count = 0;
        $community_count = 0;
        foreach ($notify_users as $user_ID => $notify_type) {	// Check for each subscribed User, if we can send a notification to him depending on current request and Item settings:
            if (! ($notify_User = &$UserCache->get_by_ID($user_ID, false, false))) {	// Invalid User, Skip it:
                $Debuglog->add('User #' . $user_ID . ' is invalid.', 'notifications');
                unset($notify_users[$user_ID]);
                continue;
            }

            // Check if the User is member of the collection:
            $is_member = $notify_User->check_perm('blog_ismember', 'view', false, $this->get_blog_ID());

            if ($notify_members && $notify_community) {	// We can notify all subscribed users:
                if ($is_member) {	// Count subscribed member:
                    $members_count++;
                } else {	// Count other subscriber:
                    $community_count++;
                }
            } elseif ($notify_members) {	// We should notify only members:
                if ($is_member) {	// Count subscribed member:
                    $members_count++;
                } else {	// Skip not member:
                    $Debuglog->add('User #' . $user_ID . ' is a not a member but at this time, we only want to notify members.', 'notifications');
                    unset($notify_users[$user_ID]);
                }
            } else {	// We should notify only community users:
                if (! $is_member) {	// Count subscribed community user:
                    $community_count++;
                } else {	// Skip member:
                    $Debuglog->add('User #' . $user_ID . ' is a member but we at this time, we only want to notify community.', 'notifications');
                    unset($notify_users[$user_ID]);
                }
            }
        }

        $Debuglog->add('Number of users who are allowed to be notified about new items on collection #' . $this->get_blog_ID() . ' = ' . count($notify_users), 'notifications');
        $Debuglog->add('First 10 user IDs: ' . implode(',', array_slice($notify_user_IDs, 0, 10)), 'notifications');

        if ($notify_members) {	// Display a message to know how many members are notified:
            $this->display_notification_message(sprintf(T_('Sending %d email notifications to subscribed members.'), $members_count), $log_messages);
        }
        if ($notify_community) {	// Display a message to know how many community users are notified:
            $this->display_notification_message(sprintf(T_('Sending %d email notifications to other subscribers.'), $community_count), $log_messages);
        }

        if (empty($notify_users)) {	// No-one to notify:
            return $notified_flags;
        }

        /*
         * We have a list of User IDs to notify:
         */
        $this->get_creator_User();

        // Load a list with the blocked emails in cache:
        load_blocked_emails($notify_user_IDs);

        // Send emails:
        $notified_users_num = 0;
        $cache_by_locale = [];
        foreach ($notify_users as $user_ID => $notify_type) {
            $notify_User = &$UserCache->get_by_ID($user_ID, false, false);
            if (empty($notify_User)) {	// skip invalid users:
                continue;
            }

            $notify_email = $notify_User->get('email');
            if (empty($notify_email)) {	// skip users with empty email address:
                continue;
            }
            $notify_locale = $notify_User->get('locale');
            $notify_user_Group = $notify_User->get_Group();

            $notify_full = $notify_user_Group->check_perm('post_subscription_notif', 'full');
            if (! isset($cache_by_locale[$notify_locale])) {	// No message for this locale generated yet:
                locale_temp_switch($notify_locale);

                /* TRANS: Subject of the mail to send on new posts to subscribed users. First %s is blog name, the second %s is the item's title. */
                $cache_by_locale[$notify_locale]['subject'] = sprintf(T_('[%s] New post: "%s"'), $edited_Blog->get('shortname'), $this->get('title'));

                locale_restore_previous();
            }

            $email_template_params = [
                'locale' => $notify_locale,
                'notify_full' => $notify_full,
                'Item' => $this,
                'recipient_User' => $notify_User,
                'notify_type' => $notify_type,
                'is_new_item' => $is_new_item,
            ];

            if ($debug >= 2) {
                $message_content = mail_template('post_new', 'txt', $email_template_params);
                echo "<p>Sending notification to $notify_email:<pre>$message_content</pre>";
            }

            if (send_mail_to_User($user_ID, $cache_by_locale[$notify_locale]['subject'], 'post_new', $email_template_params)) {
                $notified_users_num++;
                if ($log_messages == 'cron_job') {	// Log success mail sending for cron job:
                    cron_log_action_end('User ' . $notify_User->get_identity_link() . ' has been notified');
                }
            } elseif ($log_messages == 'cron_job') {	// Log failed mail sending for cron job:
                global $mail_log_message;
                cron_log_action_end('User ' . $notify_User->get_identity_link() . ' could not be notified because of error: '
                    . '"' . (empty($mail_log_message) ? 'Unknown Error' : $mail_log_message) . '"', 'warning');
            }

            blocked_emails_memorize($notify_User->email);
        }

        blocked_emails_display($log_messages);

        if ($log_messages == 'cron_job') {	// Log how much users were really notified:
            cron_log_append(sprintf('%d of %d users have been notified!', $notified_users_num, count($notify_users)));
        }

        return $notified_flags;
    }

    /**
     * Send outbound pings for a post
     *
     * @param boolean|string Force sending outbound pings:
     *                       false - Auto mode depending on current item statuses
     *                       'skip' - Skip notifications
     *                       'force' - Force notifications
     * @param boolean|string 'cron_job' - to log messages for cron job, FALSE - to don't log
     * @return boolean TRUE on success
     */
    public function send_outbound_pings($force_pings = false, $log_messages = false)
    {
        global $Plugins, $baseurl, $Messages, $evonetsrv_host, $allow_post_pings_on_localhost;

        if (! $this->notifications_allowed()) {	// Don't send pings about some post/usages like "special":
            // Note: this is a safety but this case should never happen, so don't make translators work on this:
            $this->display_notification_message('This post type/usage cannot support pings: skipping pings...', $log_messages);
            return false;
        }

        if ($this->get('status') != 'published') {	// Don't send pings if item is not 'public':
            $this->display_notification_message(T_('Skipping outbound pings because item is not published yet.'), $log_messages);
            return false;
        }

        if ($force_pings == 'skip') {	// Skip pings because it is forced by param:
            $this->display_notification_message(T_('Skipping outbound pings.'), $log_messages);
            return false;
        }

        if ($force_pings == 'force') {	// Force pings:
            $this->display_notification_message(T_('Force sending outbound pings...'), $log_messages);
        } else {	// Check if pings can be sent for this item currently:
            if ($this->check_notifications_flags('pings_sent')) {	// Don't send pings if they have already been sent:
                $this->display_notification_message(T_('Skipping outbound pings because they were already sent.'), $log_messages);
                return false;
            }

            // Some post usages should not trigger notifications to subscribers (moderators are notified earlier in the process, so they will be notified)
            // fp> I think the only usage that makes sense to send automatic notifications to subscribers is "Post"
            if ($this->get_type_setting('usage') != 'post') {	// Don't send outbound pings for items that are not regular posts:
                $this->display_notification_message(T_('This post type/usage doesn\'t need pings by default: skipping pings...'), $log_messages);
                return false;
            }
        }

        // init result
        $r = true;

        if (empty($allow_post_pings_on_localhost) &&
            $evonetsrv_host != 'localhost' && // OK if we are pinging locally anyway ;)
            (preg_match('#^http://localhost[/:]#', $baseurl) ||
              preg_match('~^\w+://[^/]+\.local/~', $baseurl))) { /* domain ending in ".local" */
            // Don't send pings from localhost:
            $this->display_notification_message(T_('Skipping pings (Running on localhost).'), $log_messages);
            return false;
        } else {	// Send pings:
            $this->display_notification_message(T_('Trying to find plugins for sending outbound pings...'), $log_messages);

            load_funcs('xmlrpc/model/_xmlrpc.funcs.php');

            $this->load_Blog();
            $ping_plugins = trim($this->Blog->get_setting('ping_plugins'));
            $ping_plugins = empty($ping_plugins) ? [] : array_unique(explode(',', $this->Blog->get_setting('ping_plugins')));

            foreach ($ping_plugins as $plugin_code) {
                $Plugin = &$Plugins->get_by_code($plugin_code);

                if ($Plugin) {
                    $ping_messages = [];
                    $ping_messages[] = [
                        'message' => isset($Plugin->ping_service_process_message) ? $Plugin->ping_service_process_message : sprintf( /* TRANS: %s is a ping service name */ T_('Pinging %s...'), $Plugin->ping_service_name),
                        'type' => 'note',
                    ];
                    $params = [
                        'Item' => &$this,
                        'xmlrpcresp' => null,
                        'display' => false,
                    ];

                    $r = $Plugin->ItemSendPing($params) && $r;

                    if (! empty($params['xmlrpcresp'])) {
                        if ($params['xmlrpcresp'] instanceof xmlrpcresp) {
                            // dh> TODO: let xmlrpc_displayresult() handle $Messages (e.g. "error", but should be connected/after the "Pinging %s..." from above)
                            ob_start();
                            xmlrpc_displayresult($params['xmlrpcresp'], true);
                            $ping_messages[] = [
                                'message' => ob_get_contents(),
                                'type' => 'note',
                            ];
                            ob_end_clean();
                        } elseif (is_array($params['xmlrpcresp'])) {
                            $ping_messages = array_merge($ping_messages, $params['xmlrpcresp']);
                        } else {
                            $ping_messages[] = $params['xmlrpcresp'];
                        }
                    }

                    $current_type = null;
                    $current_title = null;
                    $current_message = null;

                    foreach ($ping_messages as $message) {
                        if (is_array($message)) {
                            $loop_type = empty($message['type']) ? 'note' : $message['type'];
                            $loop_title = empty($message['title']) ? T_('Sending notifications:') : $message['title'];
                            $loop_message = $message['message'];
                        } else {
                            $loop_type = 'note';
                            $loop_title = T_('Sending notifications:');
                            $loop_message = $message;
                        }

                        if (empty($current_type)) {
                            $current_type = $loop_type;
                        }
                        if (empty($current_title)) {
                            $current_title = $loop_title;
                        }

                        if ($loop_type == $current_type && $loop_title == $current_title) {
                            if (empty($current_message)) {
                                $current_message = $loop_message;
                            } else {
                                $current_message .= '<br>' . $loop_message;
                            }
                        } else {
                            if ($log_messages == 'cron_job') {
                                cron_log_append($current_message . "\n", $current_type);
                            } else {
                                $Messages->add_to_group($current_message, $current_type, $current_title);
                            }
                            $current_message = $loop_message;
                            $current_type = $loop_type;
                            $current_title = $loop_title;
                        }
                    }

                    if (! empty($current_message)) {	// Display last message:
                        $this->display_notification_message($current_message, $log_messages, $current_type, $current_title);
                    }
                }
            }
        }

        // Record that we have just pinged:
        $this->set('notifications_flags', 'pings_sent');

        return $r;
    }

    /**
     * Callback user for footer()
     */
    public function replace_callback($matches)
    {
        switch ($matches[1]) {
            case 'perm_url':
            case 'item_perm_url':
                return $this->get_permanent_url();

            case 'title':
            case 'item_title':
                return $this->get('title');

            case 'excerpt':
                return $this->get_excerpt();

            case 'author':
                return $this->get('t_author');

            case 'author_login':
                return $this->get_creator_login();

            default:
                return $matches[1];
        }
    }

    /**
     * Get a member param by its name
     *
     * @param mixed Name of parameter
     * @return mixed Value of parameter
     */
    public function get($parname)
    {
        switch ($parname) {
            case 't_author':
                // Text: author
                $this->get_creator_User();
                return $this->creator_User->get('preferredname');

            case 't_assigned_to':
                // Text: assignee
                if (! $this->get_assigned_User()) {
                    return '';
                }
                return $this->assigned_User->get('preferredname');

            case 't_status':
                // Text status:
                $post_statuses = get_visibility_statuses();
                return $post_statuses[$this->get_from_revision('status')];

            case 't_extra_status':
                $ItemStatusCache = &get_ItemStatusCache();
                if (! ($Element = &$ItemStatusCache->get_by_ID($this->pst_ID, true, false))) { // No status:
                    return T_('No status');
                }
                return $Element->get_name();

            case 't_type':
                // Post type (name):
                if (empty($this->ityp_ID)) {
                    return '';
                }

                $ItemTypeCache = &get_ItemTypeCache();
                $type_Element = &$ItemTypeCache->get_by_ID($this->ityp_ID);
                return $type_Element->get_name();

            case 't_priority':
                return $this->priorities[$this->priority];

            case 'pingsdone':
                // Have pings been sent? (should only happen once the post has visibility 'public')
                // return ($this->post_notifications_status == 'finished'); // Deprecated by fp 2006-08-21 -- TODO: this should now become an alias of "pings_sent"
                return $this->check_notifications_flags('pings_sent');

            case 'excerpt':
                return $this->get_excerpt();

            case 'notifications_flags':
                return empty($this->notifications_flags) ? [] : explode(',', $this->notifications_flags);

            case 'order':
                // Get item order in main category:
                return $this->get_order();

            case 'title':
            case 'content':
            case 'status':
                return $this->get_from_revision($parname);
        }

        return parent::get($parname);
    }

    /**
     * Load item orders per categories
     */
    public function load_orders()
    {
        if (! isset($this->orders) && ($this->ID > 0 || isset($this->parent_item_ID))) {	// Initialize item orders in all assigned categories:
            $item_ID = ($this->ID > 0) ? $this->ID : $this->parent_item_ID;
            global $DB;
            $SQL = new SQL('Get all orders per categories of Item #' . $item_ID);
            $SQL->SELECT('cat_ID, cat_blog_ID, postcat_order');
            $SQL->FROM('T_postcats');
            $SQL->FROM_add('INNER JOIN T_categories ON cat_ID = postcat_cat_ID');
            $SQL->WHERE('postcat_post_ID = ' . $item_ID);
            $orders = $DB->get_results($SQL);
            $this->orders = [];
            $this->orders_per_coll = [];
            foreach ($orders as $order) {
                $this->orders[$order->cat_ID] = $order->postcat_order;
                // Initialize categories per collection, useful in cross-posted mode:
                if (! isset($this->orders_per_coll[$order->cat_blog_ID])) {
                    $this->orders_per_coll[$order->cat_blog_ID] = [];
                }
                $this->orders_per_coll[$order->cat_blog_ID][$order->cat_ID] = $order->postcat_order;
            }
        }
    }

    /**
     * Get item order per category
     *
     * @param integer Category ID, NULL - for main category
     * @return double|null Order or NULL if an order is not defined for requested category
     */
    public function get_order($cat_ID = null)
    {
        $this->load_orders();

        if ($cat_ID === null) {	// Use main category:
            global $Blog;
            if (isset($Blog) &&
                ! empty($this->orders_per_coll[$Blog->ID]) &&
                $Blog->ID != $this->get_blog_ID()) {	// Use sum of post orders from categories of cross-posted collection,
                // if current collection is a collection of extra category of this Item:
                $orders_sum = null;
                foreach ($this->orders_per_coll[$Blog->ID] as $extra_cat_order) {
                    if ($extra_cat_order !== null) {
                        $orders_sum += $extra_cat_order;
                    }
                }
                return $orders_sum;
            } else {	// Use order of main category,
                // If current collection is same as collection of main category:
                $cat_ID = $this->get('main_cat_ID');
            }
        }

        return isset($this->orders[$cat_ID]) ? $this->orders[$cat_ID] : null;
    }

    /**
     * Get item order per category by requested collection ID
     *
     * @param integer Collection ID
     * @param boolean TRUE to exclude NULL orders from result
     * @return array Array of orders (Key - Category ID, Value - Item's order)
     */
    public function get_orders_by_coll_ID($coll_ID, $exclude_null_orders = false)
    {
        $this->load_orders();

        if (isset($this->orders_per_coll[$coll_ID])) {
            $orders_per_coll = $this->orders_per_coll[$coll_ID];
            if ($exclude_null_orders) {	// Exclude NULL orders:
                foreach ($orders_per_coll as $order_cat_ID => $order) {
                    if ($order === null) {
                        unset($orders_per_coll[$order_cat_ID]);
                    }
                }
            }
        } else {	// No orders for the requested collection:
            $orders_per_coll = [];
        }

        return $orders_per_coll;
    }

    /**
     * Update item order per category
     *
     * @param double New order value
     * @param integer Category ID, NULL - for main category or for extra category from provided Collection ID
     * @param integer Collection ID - to use extra category when $cat_ID is NULL
     * @return boolean
     */
    public function update_order($order, $cat_ID = null, $coll_ID = null)
    {
        global $DB;

        if (empty($this->ID)) {	// Item must be created:
            return false;
        }

        if ($cat_ID === null) {	// Find what category to use for updating of order:
            if (empty($coll_ID) || $this->get_blog_ID() == $coll_ID) {	// Use main category:
                $cat_ID = $this->get('main_cat_ID');
            } elseif (count($this->get_orders_by_coll_ID($coll_ID)) == 1) {	// Use extra category if it is single category per Collection for this Item:
                $extra_cats = array_keys($this->orders_per_coll[$coll_ID]);
                $cat_ID = $extra_cats[0];
            }
        }

        if (empty($cat_ID)) {	// Don't try to update without provided and detected Category:
            return false;
        }

        // Change order to correct value:
        $order = ($order === '' || $order === null ? null : floatval($order));

        // Insert/Update order per category:
        $r = $DB->query('REPLACE INTO T_postcats ( postcat_post_ID, postcat_cat_ID, postcat_order )
			VALUES ( ' . $this->ID . ', ' . intval($cat_ID) . ', ' . $DB->quote($order) . ' ) ');

        if ($r) {	// Update last touched date:
            $this->update_last_touched_date();
        }

        return $r;
    }

    /**
     * Get param value from current revision
     *
     * @param mixed Name of parameter
     * @return mixed Value of parameter
     */
    public function get_from_revision($parname)
    {
        if ($this->is_revision() && // If revision is active currently for this Item
            ($Revision = $this->get_revision()) && // If current revision is detected for this Item
                isset($Revision->{'iver_' . $parname})) { // If revision really has the requested field
            // Get value from current revision of this Item:
            return $Revision->{'iver_' . $parname};
        }

        // Get value from this Item:
        return parent::get($parname);
    }

    /**
     * Assign the item to the first category we find in the requested collection
     *
     * @param integer $collection_ID
     */
    public function assign_to_first_cat_for_collection($collection_ID)
    {
        global $DB;

        // Get the first category ID for the collection ID param
        $cat_ID = $DB->get_var('
				SELECT cat_ID
					FROM T_categories
				 WHERE cat_blog_ID = ' . $collection_ID . '
				 ORDER BY cat_ID ASC
				 LIMIT 1');

        // Set to the item the first category we got
        $this->set('main_cat_ID', $cat_ID);
    }

    /**
     * Get the list of renderers for this Item.
     * @return array
     */
    public function get_renderers()
    {
        return explode('.', $this->renderers);
    }

    /**
     * Get the list of validated renderers for this Item. This includes stealth plugins etc.
     * @return array List of validated renderer codes
     */
    public function get_renderers_validated()
    {
        if (! isset($this->renderers_validated)) {
            global $Plugins;
            $this->renderers_validated = $Plugins->validate_renderer_list($this->get_renderers(), [
                'Item' => &$this,
            ]);
        }
        return $this->renderers_validated;
    }

    /**
     * Add a renderer (by code) to the Item.
     * @param string Renderer code to add for this item
     * @return boolean True if renderers have changed
     */
    public function add_renderer($renderer_code)
    {
        $renderers = $this->get_renderers();
        if (in_array($renderer_code, $renderers)) {
            return false;
        }

        $renderers[] = $renderer_code;
        $this->set_renderers($renderers);

        $this->renderers_validated = null;
        return true;
    }

    /**
     * Remove a renderer (by code) from the Item.
     * @param string Renderer code to remove for this item
     * @return boolean True if renderers have changed
     */
    public function remove_renderer($renderer_code)
    {
        $r = false;
        $renderers = $this->get_renderers();
        while (($key = array_search($renderer_code, $renderers)) !== false) {
            $r = true;
            unset($renderers[$key]);
        }

        if ($r) {
            $this->set_renderers($renderers);
            $this->renderers_validated = null;
            //echo 'Removed renderer '.$renderer_code;
        }
        return $r;
    }

    /**
     * Get the item tinyslug. If not exists -> create new
     *
     * @return string|boolean tinyslug on success, false otherwise
     */
    public function get_tinyslug()
    {
        global $preview;

        $tinyslug_ID = $this->tiny_slug_ID;
        if ($tinyslug_ID != null) { // the tiny slug for this item was already created
            $SlugCache = &get_SlugCache();
            $Slug = &$SlugCache->get_by_ID($tinyslug_ID, false, false);
            return $Slug === false ? false : $Slug->get('title');
        } elseif (($this->ID > 0) && (! $preview)) { // create new tiny Slug for this item
            // Note: This may happen only in case of posts created before the tiny slug was introduced
            global $DB;
            load_funcs('slugs/model/_slug.funcs.php');

            $Slug = new Slug();
            $Slug->set('title', getnext_tinyurl());
            $Slug->set('itm_ID', $this->ID);
            $Slug->set('type', 'item');
            $DB->begin();
            if (! $Slug->dbinsert()) { // Slug dbinsert failed
                $DB->rollback();
                return false;
            }
            $this->set('tiny_slug_ID', $Slug->ID);

            // Update Item preserving mod date:
            if (! $this->dbupdate(false)) { // Item dbupdate failed
                $DB->rollback();
                return false;
            }
            $DB->commit();

            // update last tinyurl value on database
            // Note: This doesn't have to be part of the above transaction, no problem if it doesn't succeed to update, or if override a previously updated value.
            global $Settings;
            $Settings->set('tinyurl', $Slug->get('title'));
            $Settings->dbupdate();

            return $Slug->get('title');
        }

        return false;
    }

    /**
     * Get all slugs of this Item, except of tiny slug
     *
     * @param string|null Separator, NULL - to return array
     * @return string Slugs list
     */
    public function get_slugs($separator = ', ')
    {
        if (! isset($this->slugs)) {	// Initialize item slugs:
            if (empty($this->ID)) {	// Get creating Item:
                //return $this->get( 'urltitle' );
                $this->slugs = [];
            } else {	// Load slugs from DB once:
                global $DB;
                $SQL = new SQL('Get slugs of the Item #' . $this->ID);
                $SQL->SELECT('slug_title, IF( slug_ID = ' . intval($this->canonical_slug_ID) . ', 0, slug_ID ) AS slug_order_num');
                $SQL->FROM('T_slug');
                $SQL->WHERE('slug_itm_ID = ' . $DB->quote($this->ID));
                if (! empty($this->tiny_slug_ID)) {	// Exclude tiny slug from list:
                    $SQL->WHERE_and('slug_ID != ' . $DB->quote($this->tiny_slug_ID));
                }
                $SQL->ORDER_BY('slug_order_num');
                $this->slugs = $DB->get_col($SQL);
            }
        }

        return $separator === null ? $this->slugs : implode($separator, $this->slugs);
    }

    /**
     * Get the item tiny url
     * @return string the tiny url on success, empty string otherwise
     */
    public function get_tinyurl($use_tinyslug = true)
    {
        if ($use_tinyslug) {
            if (($slug = $this->get_tinyslug()) == false) {
                return '';
            }
        } else {
            $slug = $this->urltitle;
        }

        $Collection = $Blog = &$this->get_Blog();
        if (($Blog->get_setting('tinyurl_type') == 'advanced') && ($tinyurl_domain = $Blog->get_setting('tinyurl_domain'))) {
            return url_add_tail($tinyurl_domain, '/' . $slug);
        } else {
            return url_add_tail($Blog->get('url'), '/' . $slug);
        }
    }

    /**
     * Create and return the item tinyurl link.
     *
     * @param array Params:
     *  - 'before': to display before link
     *  - 'after': to display after link
     *  - 'text': link text
     *  - 'title': link title
     *  - 'class': class name
     *  - 'style': link style
     * @return string the tinyurl link on success, empty string otherwise
     */
    public function get_tinyurl_link($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => ' ',
            'after' => ' ',
            'text' => '#',
            'title' => '#',
            'class' => '',
            'style' => '',
            'use_tinyslug' => true,
        ], $params);

        if ($params['use_tinyslug']) {
            if (($slug = $this->get_tinyslug()) == false) {
                return '';
            }
        } else {
            $slug = $this->urltitle;
        }

        if (! $this->ID) { // preview..
            return false;
        }

        if ($params['title'] == '#') {
            $params['title'] = T_('This is a tinyurl you can copy/paste into twitter, emails and other places where you need a short link to this post');
        }
        if ($params['text'] == '#') {
            $params['text'] = $slug;
        }

        $actionurl = $this->get_tinyurl($params['use_tinyslug']);

        $r = $params['before'];
        $r .= '<a href="' . $actionurl;
        $r .= '" title="' . $params['title'] . '"';
        if (! empty($params['class'])) {
            $r .= ' class="' . $params['class'] . '"';
        }
        if (! empty($params['style'])) {
            $r .= ' style="' . $params['style'] . '"';
        }
        $r .= '>' . $params['text'] . '</a>';
        $r .= $params['after'];

        return $r;
    }

    /**
     * Display the item tinyurl link
     */
    public function tinyurl_link($params = [])
    {
        echo $this->get_tinyurl_link($params);
    }

    /**
     * Get an url to this item
     * @param string values:
     * 		- 'admin_view': url to this item admin interface view
     * 		- 'public_view': url to this item public interface view (permanent url)
     * 		- 'edit': url to this item edit screen
     * @return string the url if exists, empty string otherwise
     */
    public function get_url($type)
    {
        global $admin_url;
        switch ($type) {
            case 'admin_view':
                return $admin_url . '?ctrl=items&amp;blog=' . $this->get_blog_ID() . '&amp;p=' . $this->ID;
            case 'public_view':
                return $this->get_permanent_url();
            case 'edit':
                return $this->get_edit_url();
            default:
                return '';
        }
    }

    /**
     * Get the number of comments on this item
     *
     * @param string the status of counted comments
     * @return integer the number of comments
     */
    public function get_number_of_comments($status = null)
    {
        global $DB;

        $sql = 'SELECT count( comment_ID )
				FROM T_comments
				WHERE comment_item_ID = ' . $this->ID;

        if ($status != null) {
            $sql .= ' AND comment_status = "' . $status . '"';
        }

        return $DB->get_var($sql);
    }

    /**
     * Get the latest Comment on this Item
     *
     * @param array|null Restrict comments selection with statuses, NULL - to select only allowed statuses for current User
     * @param string Type of the latest comment: NULL|'date' - latest added comment, 'last_touched_ts' - latest touched comment
     * @return Comment
     */
    public function &get_latest_Comment($statuses = null, $order_date_type = null)
    {
        global $DB;

        if ($this->latest_Comment === null) {
            if (empty($this->ID)) {	// New item has no comments:
                $this->latest_Comment = false;
                return $this->latest_Comment;
            }

            $SQL = new SQL('Get the latest Comment on the Item #' . $this->ID);
            $SQL->SELECT('comment_ID');
            $SQL->FROM('T_comments');
            $SQL->WHERE('comment_item_ID = ' . $DB->quote($this->ID));
            $SQL->WHERE_and('comment_type != "meta"');
            if ($statuses === null) {	// Restrict with comment statuses which are allowed for current User:
                $SQL->WHERE_and(statuses_where_clause(get_inskin_statuses($this->get_blog_ID(), 'comment'), 'comment_', $this->get_blog_ID(), 'blog_comment!', true));
            } elseif (is_array($statuses) && count($statuses)) {	// Restrict with given comment statuses:
                $SQL->WHERE_and('comment_status IN ( ' . $DB->quote($statuses) . ' )');
            }
            if ($order_date_type == 'last_touched_ts') {	// Get the latest touched comment:
                $SQL->ORDER_BY('comment_last_touched_ts DESC, comment_ID DESC');
            } else {	// Get the latest added comment:
                $SQL->ORDER_BY('comment_date DESC, comment_ID DESC');
            }
            $SQL->LIMIT('1');

            if ($comment_ID = $DB->get_var($SQL)) {	// Load the latest Comment in cache:
                $CommentCache = &get_CommentCache();
                // WARNING: Do NOT get this object by reference because it may rewrites current updating Comment:
                $this->latest_Comment = $CommentCache->get_by_ID($comment_ID);
            } else {	// Set FALSE to don't call SQL query twice when the item has no comments yet:
                $this->latest_Comment = false;
            }
        }

        return $this->latest_Comment;
    }

    /**
     * Get the ratings of comments on this item
     *
     * @retrun array of [ ratings, active ratings ] for this comment
     */
    public function get_ratings()
    {
        global $DB, $localtimenow;

        $this->load_Blog();

        // Count each published comments rating grouped by active/expired status and by rating value:
        $SQL = new SQL('Count each published comments rating grouped by active/expired status and by rating value');
        $SQL->SELECT('comment_rating, count( comment_ID ) AS cnt,');
        $SQL->SELECT_add('IF( iset_value IS NULL OR iset_value = "" OR TIMESTAMPDIFF(SECOND, comment_date, ' . $DB->quote(date2mysql($localtimenow)) . ') < iset_value, "active", "expired" ) as expiry_status');
        $SQL->FROM('T_comments');
        $SQL->FROM_add('LEFT JOIN T_items__item_settings ON iset_item_ID = comment_item_ID AND iset_name = "comment_expiry_delay"');
        $SQL->WHERE('comment_item_ID = ' . $this->ID);
        $SQL->WHERE_and(statuses_where_clause(get_inskin_statuses($this->Blog->ID, 'comment'), 'comment_', $this->Blog->ID, 'blog_comment!'));
        $SQL->GROUP_BY('expiry_status, comment_rating');
        $SQL->ORDER_BY('comment_rating DESC');
        $results = $DB->get_results($SQL);

        // init rating arrays
        $ratings = [];
        $ratings['total'] = 0;
        $ratings['summary'] = 0;
        $ratings['unrated'] = 0;
        $active_ratings = [];
        $active_ratings['total'] = 0;
        $active_ratings['summary'] = 0;
        $active_ratings['unrated'] = 0;

        if (empty($results)) { // No rating at all
            $ratings['all_ratings'] = 0;
            $active_ratings['all_ratings'] = 0;
            return [$ratings, $active_ratings];
        }

        // Init all ratings count to 0
        for ($i = 5; $i >= 1; $i--) {
            $ratings[$i] = 0;
            $active_ratings[$i] = 0;
        }

        // Count active and overall rating values
        foreach ($results as $rating) {
            $index = ($rating->comment_rating == 0) ? 'unrated' : $rating->comment_rating;
            $ratings[$index] += $rating->cnt;
            $ratings['total'] += $rating->cnt;
            $ratings['summary'] += ($rating->cnt * $rating->comment_rating);
            if ($rating->expiry_status == 'active') { // this rating is not expired yet
                $active_ratings[$index] = $rating->cnt;
                $active_ratings['total'] += $rating->cnt;
                $active_ratings['summary'] += ($rating->cnt * $rating->comment_rating);
            }
        }

        $ratings['all_ratings'] = $ratings['total'] - $ratings['unrated'];
        $active_ratings['all_ratings'] = $active_ratings['total'] - $active_ratings['unrated'];

        return [$ratings, $active_ratings];
    }

    /**
     * Get a setting.
     *
     * @return string|false|null value as string on success; NULL if not found; false in case of error
     */
    public function get_setting($parname)
    {
        $this->load_ItemSettings();

        return $this->ItemSettings->get($this->ID, $parname);
    }

    /**
     * Set a setting.
     *
     * @return boolean true, if the value has been set, false if it has not changed.
     */
    public function set_setting($parname, $value, $make_null = false)
    {
        // Make sure item settings are loaded
        $this->load_ItemSettings();

        if ($make_null && empty($value)) {
            $value = null;
        }

        return $this->ItemSettings->set($this->ID, $parname, $value);
    }

    /**
     * Delete a setting.
     *
     * @return boolean true, if the value has been set, false if it has not changed.
     */
    public function delete_setting($parname)
    {
        // Make sure item settings are loaded
        $this->load_ItemSettings();

        return $this->ItemSettings->delete($this->ID, $parname);
    }

    /**
     * Make sure item settings are loaded.
     */
    public function load_ItemSettings()
    {
        if (! isset($this->ItemSettings)) {
            load_class('items/model/_itemsettings.class.php', 'ItemSettings');
            $this->ItemSettings = new ItemSettings();
        }
    }

    /**
     * Get location of current Item
     *
     * @param string Text before location
     * @param string Text after location
     * @param string Separator
     */
    public function get_location($before, $after, $separator = ', ')
    {
        $location = [];
        $location[] = $this->get_city();
        $location[] = $this->get_subregion();
        $location[] = $this->get_region();
        $location[] = $this->get_country();

        // Delete empty elements
        $location = array_filter($location);

        $r = '';

        if (! empty($location)) {	// Display location
            $r .= $before;

            $r .= implode($separator, $location);

            $r .= $after;
        }

        return $r;
    }

    /**
     * Display location of current Item
     *
     * @param string Text before location
     * @param string Text after location
     * @param string Separator
     */
    public function location($before, $after, $separator = ', ')
    {
        echo $this->get_location($before, $after, $separator);
    }

    /**
     * Get country of current Item
     *
     * @param array params
     * @return string Country name
     */
    public function get_country($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
        ], $params);

        $this->load_Blog();
        if ($this->ctry_ID == 0 || ! $this->country_visible()) {	// Country is not defined for current Item OR Counries are hidden
            return;
        }

        load_class('regional/model/_country.class.php', 'Country');
        $CountryCache = &get_CountryCache();

        if ($Country = $CountryCache->get_by_ID($this->ctry_ID, false, false)) {	// Display country name
            $result = $params['before'];

            $result .= $Country->get_name();

            $result .= $params['after'];

            return $result;
        }
    }

    /**
     * Get region of current Item
     *
     * @param array params
     * @return string Region name
     */
    public function get_region($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
        ], $params);

        $this->load_Blog();
        if ($this->rgn_ID == 0 || ! $this->region_visible()) {	// Region is not defined for current Item
            return;
        }

        load_class('regional/model/_region.class.php', 'Region');
        $RegionCache = &get_RegionCache();

        if ($Region = $RegionCache->get_by_ID($this->rgn_ID, false, false)) {	// Display region name
            $result = $params['before'];

            $result .= $Region->get_name();

            $result .= $params['after'];

            return $result;
        }
    }

    /**
     * Get subregion of current Item
     *
     * @param array params
     * @return string Subregion name
     */
    public function get_subregion($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
        ], $params);

        $this->load_Blog();
        if ($this->subrg_ID == 0 || ! $this->subregion_visible()) {	// Subregion is not defined for current Item
            return;
        }

        load_class('regional/model/_subregion.class.php', 'Subregion');
        $SubregionCache = &get_SubregionCache();

        if ($Subregion = $SubregionCache->get_by_ID($this->subrg_ID, false, false)) {	// Display subregion name
            $result = $params['before'];

            $result .= $Subregion->get_name();

            $result .= $params['after'];

            return $result;
        }
    }

    /**
     * Get city of current Item
     *
     * @param array params
     * @return string City name + postcode
     */
    public function get_city($params = [])
    {
        // Make sure we are not missing any param:
        $params = array_merge([
            'before' => '',
            'after' => '',
            'template' => '$name$ ($postcode$)', // $name$ - City name; $postcode$ - City postcode
        ], $params);

        $this->load_Blog();
        if ($this->city_ID == 0 || ! $this->city_visible()) {	// City is not defined for current Item
            return;
        }

        load_class('regional/model/_city.class.php', 'City');
        $CityCache = &get_CityCache();

        if ($City = $CityCache->get_by_ID($this->city_ID, false, false)) {	// Display city info
            $result = $params['before'];

            $city_tamplates = ['$name$', '$postcode$'];
            $city_data = [$City->get_name(), $City->get_postcode()];
            $result .= str_replace($city_tamplates, $city_data, $params['template']);

            $result .= $params['after'];

            return $result;
        }
    }

    /**
     * Get item revision
     *
     * @param string Revision ID with prefix as first char: 'a'(or digit) - archived version, 'c' - current version, 'p' - proposed change, NULL - to use current revision
     * @return object Revision
     */
    public function &get_revision($iver_ID = null)
    {
        global $DB;

        if ($iver_ID === null && $this->is_revision()) {	// Use current revision
            $iver_ID = $this->revision;
        }

        if (empty($this->ID) || empty($iver_ID)) {	// Item must be stored in DB to get revisions and Revision ID must be defined:
            $r = false;
            return $r;
        }

        if (isset($this->revisions[$iver_ID])) {	// Get revision data from cached array:
            return $this->revisions[$iver_ID];
        }

        // Save original ID to use this for storing in cache:
        $orig_iver_ID = $iver_ID;

        if ($iver_ID == 'last_archived' || $iver_ID == 'last_proposed') {	// Get last archived version or last proposed change:
            $iver_type = substr($iver_ID, 5);
            $revision_SQL = new SQL('Get ' . str_replace('_', ' ', $iver_ID) . ' version of the Item #' . $this->ID);
            $revision_SQL->SELECT('*, CONCAT( "' . ($iver_type == 'archived' ? 'a' : 'p') . '", iver_ID ) AS param_ID');
            $revision_SQL->FROM('T_items__version');
            $revision_SQL->WHERE('iver_itm_ID = ' . $DB->quote($this->ID));
            $revision_SQL->WHERE_and('iver_type = ' . $DB->quote($iver_type));
            $revision_SQL->ORDER_BY('iver_ID DESC');
            $revision_SQL->LIMIT('1');
            $this->revisions[$orig_iver_ID] = $DB->get_row($revision_SQL);

            return $this->revisions[$orig_iver_ID];
        }

        // Get version type from first char:
        $iver_ID_len = strlen($iver_ID);
        $iver_type = $iver_ID_len > 0 ? substr($iver_ID, 0, 1) : 'c';

        if (intval($iver_type) == 0) {	// Extract version ID:
            $iver_ID = intval(substr($iver_ID, 1));
        } else {	// Use the provided integer ID and decide this as archived version:
            $iver_type = 'a';
        }
        $iver_ID = intval($iver_ID);
        if ($iver_ID == 0) {	// Force to get current version for request without ID:
            $iver_type = 'c';
        }

        switch ($iver_type) {
            case 'a':
            case 'p':
                // Archived version or Proposed change:
                $revision_SQL = new SQL('Get ' . ($iver_type == 'a' ? 'an archived version' : 'a proposed change') . ' #' . $this->ID . ' for Item #' . $this->ID);
                $revision_SQL->SELECT('*, CONCAT( "' . $iver_type . '", iver_ID ) AS param_ID');
                $revision_SQL->FROM('T_items__version');
                $revision_SQL->WHERE('iver_ID = ' . $DB->quote($iver_ID));
                $revision_SQL->WHERE_and('iver_itm_ID = ' . $DB->quote($this->ID));
                $revision_SQL->WHERE_and('iver_type = "' . ($iver_type == 'a' ? 'archived' : 'proposed') . '"');
                $this->revisions[$orig_iver_ID] = $DB->get_row($revision_SQL->get(), OBJECT, null, $revision_SQL->title);
                break;

            case 'c':
            default:
                // Current version:
                $this->revisions[$orig_iver_ID] = (object) [
                    'param_ID' => 'c',
                    'iver_ID' => 0,
                    'iver_type' => 'current',
                    'iver_itm_ID' => $this->ID,
                    'iver_edit_last_touched_ts' => $this->contents_last_updated_ts,
                    'iver_edit_user_ID' => $this->lastedit_user_ID,
                    'iver_status' => $this->status,
                    'iver_title' => $this->title,
                    'iver_content' => $this->content,
                ];
                break;
        }

        return $this->revisions[$orig_iver_ID];
    }

    /**
     * Clear revision and item's data to current version
     */
    public function clear_revision()
    {
        // Reset this Item in order to use current title, content and other data:
        $ItemCache = get_ItemCache();
        unset($ItemCache->cache[$this->ID]);
        if ($current_Item = $ItemCache->get_by_ID($this->ID, false, false)) {	// Revert fields to current values:
            $revision_fields = ['title', 'content', 'status'];
            foreach ($revision_fields as $revision_field) {
                $this->set($revision_field, $current_Item->get($revision_field));
            }
        }

        if (isset($this->revision)) {	// Reset to use data from current Item:
            unset($this->revision);
        }
    }

    /**
     * Get item custom field value by index from current revision
     *
     * @param string Field name, see {@link load_custom_field_value()}
     * @return mixed false if the field doesn't exist Double/String otherwise depending from the custom field type
     */
    public function get_revision_custom_field_value($field_name)
    {
        if (! $this->is_revision()) {	// Revision is not active:
            return null;
        }

        if (! ($Revision = &$this->get_revision())) {	// Revision cannot be detected:
            return null;
        }

        if (! isset($Revision->custom_fields)) {	// Load custom fields from DB and store in cache:
            global $DB;
            $SQL = new SQL('Get custom fields values of Item #' . $this->ID . ' for revision #' . $Revision->iver_ID . '(' . $Revision->iver_type . ')');
            $SQL->SELECT('IFNULL( itcf_name, CONCAT( "!deleted_", ivcf_itcf_ID ) ), ivcf_value');
            $SQL->FROM('T_items__version_custom_field');
            $SQL->FROM_add('LEFT JOIN T_items__type_custom_field ON ivcf_itcf_ID = itcf_ID');
            $SQL->WHERE_and('ivcf_iver_ID = ' . $DB->quote($Revision->iver_ID));
            $SQL->WHERE_and('ivcf_iver_type = ' . $DB->quote($Revision->iver_type));
            $SQL->WHERE_and('ivcf_iver_itm_ID = ' . $DB->quote($Revision->iver_itm_ID));
            $Revision->custom_fields = $DB->get_assoc($SQL);
        }

        if (isset($Revision->custom_fields[$field_name])) {	// If the revision has a requested custom field:
            return $Revision->custom_fields[$field_name];
        } else {	// If the revision has no requested custom field:
            return null;
        }
    }

    /**
     * Update this item from revision
     *
     * @param string Revision ID with prefix as first char: 'a'(or digit) - archived version, 'c' - current version, 'p' - proposed change, NULL - to use current revision
     * @return boolean TRUE on success, FALSE on failed
     */
    public function update_from_revision($iver_ID = null)
    {
        if (! ($Revision = &$this->get_revision($iver_ID))) {	// If revision is not found:
            return false;
        }

        global $DB;

        $DB->begin('SERIALIZABLE');

        // Update main fields:
        $this->set('status', $Revision->iver_status);
        $this->set('title', $Revision->iver_title);
        $this->set('content', $Revision->iver_content);

        $custom_fields = $this->get_type_custom_fields();
        if (count($custom_fields)) {	// Update custom fields if item type has them:
            // Switch to the requested revision:
            $this->set('revision', $Revision->param_ID);
            foreach ($custom_fields as $custom_field) {
                $this->set_custom_field($custom_field['name'], $this->get_custom_field_value($custom_field['name']));
            }
        }

        if ($Revision->iver_type == 'proposed') {	// Update last updated data from proposed change:
            $this->set('lastedit_user_ID', $Revision->iver_edit_user_ID);
            $this->set('datemodified', $Revision->iver_edit_last_touched_ts);
            $this->set('contents_last_updated_ts', $Revision->iver_edit_last_touched_ts);
            $this->set_last_touched_ts();
            // Don't auto track date fields on accepting of proposed change:
            $auto_track_modification = false;
            // Force to create new revision even if no 90 seconds after last changing:
            $force_create_revision = true;
        } else {	// Auto track date fields on restoring from history:
            $auto_track_modification = true;
            // Don't force creating of new revision:
            $force_create_revision = false;
        }

        $r = $this->dbupdate($auto_track_modification, true, true, $force_create_revision);

        // Update attachments:
        $current_links_SQL = new SQL('Get current links of Item #' . $this->ID . ' before updating from revision');
        $current_links_SQL->SELECT('*');
        $current_links_SQL->FROM('T_links');
        $current_links_SQL->WHERE('link_itm_ID = ' . $this->ID);
        $current_links = $DB->get_results($current_links_SQL, OBJECT, '', 'link_ID');

        $revision_links_SQL = new SQL('Get attachments of Item #' . $this->ID . ' for revision #' . $Revision->iver_ID . '(' . $Revision->iver_type . ') before updating from revision');
        $revision_links_SQL->SELECT('T_items__version_link.*');
        $revision_links_SQL->FROM('T_items__version_link');
        $revision_links_SQL->FROM_add('INNER JOIN T_files ON ivl_file_ID = file_ID');
        $revision_links_SQL->WHERE('ivl_iver_ID = ' . $Revision->iver_ID);
        $revision_links_SQL->WHERE_and('ivl_iver_itm_ID = ' . $this->ID);
        $revision_links_SQL->WHERE_and('ivl_iver_type = ' . $DB->quote($Revision->iver_type));
        $revision_links = $DB->get_results($revision_links_SQL, OBJECT, '', 'ivl_link_ID');

        // Use this array to check for uniqueness links by order:
        $link_orders = [];

        $LinkOwner = new LinkItem($this);
        $LinkCache = &get_LinkCache();
        foreach ($current_links as $current_link_ID => $current_link) {
            if (! isset($revision_links[$current_link_ID])) {	// This link doesn't exist in revision, Delete it:
                if ($deleted_Link = &$LinkCache->get_by_ID($current_link_ID, false, false) &&
                    $LinkOwner->remove_link($deleted_Link, true)) {
                    $LinkOwner->after_unlink_action($current_link_ID);
                }
            } else {	// This link exists in revision, we should keep it:
                // Also store an order in the array to check for uniqueness them by order:
                $link_orders[$current_link_ID] = $current_link->link_order;
            }
        }

        if (count($revision_links)) {	// It revision has at least one link/attachment:
            $updated_links = [];
            $delete_updated_links = [];
            foreach ($revision_links as $revision_link_ID => $revision_link) {	// Add links of revision to array to check for uniqueness them by order:
                $link_orders[$revision_link_ID] = $revision_link->ivl_order;
                if (isset($current_links[$revision_link_ID])) {	// Store here what links of current version must be updated from revision:
                    $updated_links[$revision_link_ID] = $current_links[$revision_link_ID];
                    $delete_updated_links[] = $revision_link_ID;
                }
            }

            if (count($delete_updated_links)) {	// Delete the links(which must be updated) rows temporary from DB in order insert them later with new data to avoid duplicate entry error:
                $DB->query(
                    'DELETE FROM T_links
					WHERE link_ID IN ( ' . $DB->quote($delete_updated_links) . ' )
						AND link_itm_ID = ' . $DB->quote($this->ID),
                    'Temporary deleting of links rows which must be updated from revision'
                );
            }

            // Sort links by order and check for duplicate orders:
            asort($link_orders);
            $prev_order = -1;
            foreach ($link_orders as $link_ID => $link_order) {
                if ($prev_order >= $link_order) {	// If previous link has same order then increase it to avoid a duplicate error on insert links in DB:
                    $link_orders[$link_ID]++;
                }
                $prev_order = $link_order;
            }

            $insert_links_sql = [];
            foreach ($revision_links as $revision_link_ID => $revision_link) {
                if (isset($updated_links[$revision_link_ID])) {	// This link exists in current version, Update it:
                    $updated_link = $updated_links[$revision_link_ID];
                }
                {	// This link doesn't exist in current version, Insert it:
                    $updated_link = false;
                }
                $insert_links_sql[] = '( ' . $DB->quote([
                    $revision_link_ID,
                    $updated_link ? $updated_link->link_datecreated : $Revision->iver_edit_last_touched_ts,
                    $updated_link ? $updated_link->link_datemodified : $Revision->iver_edit_last_touched_ts,
                    $updated_link ? $updated_link->link_creator_user_ID : $Revision->iver_edit_user_ID,
                    $updated_link ? $updated_link->link_lastedit_user_ID : $Revision->iver_edit_user_ID,
                    $this->ID,
                    $revision_link->ivl_file_ID,
                    $revision_link->ivl_position,
                    $link_orders[$revision_link_ID],
                ]) . ' )';
            }

            // Insert/Update links with data from revision:
            $DB->query(
                'INSERT INTO T_links ( link_ID, link_datecreated, link_datemodified, link_creator_user_ID, link_lastedit_user_ID, link_itm_ID, link_file_ID, link_position, link_order )
				VALUES ' . implode(', ', $insert_links_sql),
                'Insert/Update item links with data from revision #' . $Revision->iver_ID . '(' . $Revision->iver_type . ')'
            );
        }

        $DB->commit();

        return $r;
    }

    /**
     * Delete proposed changes of this Item from DB
     *
     * @param string Action 'accept', 'reject'
     * @param integer|string ID of the proposed change, 'last' to get last proposed change
     */
    public function clear_proposed_changes($action = 'accept', $iver_ID = 'last')
    {
        global $DB;

        if (empty($this->ID)) {	// Item must be stored in nDB:
            return;
        }

        if ($iver_ID === 'last') {	// Try to get ID of the last proposed change:
            if ($last_proposed_Revision = $this->get_revision('last_proposed')) {	// If this Item has at least one proposed change:
                $iver_ID = $last_proposed_Revision->iver_ID;
            } else {	// This Item has no proposed changes:
                return;
            }
        }

        if (strpos($action, 'accept') !== false) {	// Accept(delete) all previous proposed changes:
            $delete_direction = '<=';
        } elseif (strpos($action, 'reject') !== false) {	// Reject(delete) all newer proposed changes:
            $delete_direction = '>=';
        } else {
            debug_die('Unhandled action "' . $action . '" to clear proposed changes!');
        }

        // Clear proposed changes:
        $DB->query('DELETE FROM T_items__version
			WHERE iver_itm_ID = ' . $DB->quote($this->ID) . '
			  AND iver_type = "proposed"
			  AND iver_ID ' . $delete_direction . ' ' . $DB->quote($iver_ID));
        // Clear custom fields of the proposed changes:
        $DB->query('DELETE FROM T_items__version_custom_field
			WHERE ivcf_iver_itm_ID = ' . $DB->quote($this->ID) . '
			  AND ivcf_iver_type = "proposed"
			  AND ivcf_iver_ID ' . $delete_direction . ' ' . $DB->quote($iver_ID));
        // Clear links/attachments of the proposed changes:
        $DB->query('DELETE FROM T_items__version_link
			WHERE ivl_iver_itm_ID = ' . $DB->quote($this->ID) . '
			  AND ivl_iver_type = "proposed"
			  AND ivl_iver_ID ' . $delete_direction . ' ' . $DB->quote($iver_ID));
    }

    /**
     * Check if item is locked
     *
     * @return boolean TRUE - if item is locked
     */
    public function is_locked()
    {
        if (isset($this->is_locked)) { // item lock status was already set
            return $this->is_locked;
        }

        // Get item chapters to check lock status, but use cached chapters array instead of db query
        $item_chapters = $this->get_Chapters();

        if (count($item_chapters)) { // Presuppose that all category is locked, we will change this value if only one category is not locked
            $this->is_locked = true;
            foreach ($item_chapters as $item_Chapter) { // Check if all item categories is locked
                if (! $item_Chapter->lock) { // This category is not locked so the item is not locked either
                    $this->is_locked = false;
                    break;
                }
            }
        } else { // If no category was set yet ( e.g. in case of new item create ), the Item can't be locked
            $this->is_locked = false;
        }

        return $this->is_locked;
    }

    /**
     * Set field last_touched_ts
     */
    public function set_last_touched_ts()
    {
        global $localtimenow;

        if (is_logged_in()) {
            $this->load_content_read_status();
        }

        $this->set_param('last_touched_ts', 'date', date2mysql($localtimenow));
    }

    /**
     * Set field contents_last_updated_ts
     */
    public function set_contents_last_updated_ts()
    {
        global $localtimenow;

        $this->set_param('contents_last_updated_ts', 'date', date2mysql($localtimenow));
    }

    /**
     * Update field last_touched_ts and parent categories
     *
     * @param boolean Use transaction
     * @param boolean Use TRUE to update item field last_touched_ts
     * @param boolean Use TRUE to update item field contents_last_updated_ts
     * @param boolean do we want to auto track the mod date?
     */
    public function update_last_touched_date($use_transaction = true, $update_last_touched_ts = true, $update_contents_last_updated_ts = false, $auto_track_modification = false)
    {
        if ($use_transaction) {
            global $DB;
            $DB->begin();
        }

        if ($update_last_touched_ts || $update_contents_last_updated_ts) {	// If at least one date field should be updated
            if ($update_last_touched_ts) {	// Update field last_touched_ts:
                $this->set_last_touched_ts();
            }
            if ($update_contents_last_updated_ts) {	// Update field contents_last_updated_ts:
                $this->set_contents_last_updated_ts();
            }
            $this->dbupdate($auto_track_modification, false, false);
        }

        // Also update last touched date of all categories of this Item
        $chapters = $this->get_Chapters();
        if (count($chapters) > 0) {
            foreach ($chapters as $Chapter) {
                $Chapter->update_last_touched_date();
                while ($Chapter) { // Update all parent chapters recursively
                    $Chapter = $Chapter->get_parent_Chapter();
                    if (! empty($Chapter)) {
                        $Chapter->update_last_touched_date();
                    }
                }
            }
        }

        if ($use_transaction) {
            $DB->commit();
        }
    }

    /**
     * Update field itud_read_item_ts for current User
     *
     * @param boolean TRUE to update a post read timestamp
     * @param boolean TRUE to update a comments read timestamp
     */
    public function update_read_timestamps($read_post = true, $read_comments = true)
    {
        if (! $read_post && ! $read_comments) { // Nothing to update
            return;
        }

        if ($this->ID == 0) { // Item is not saved in DB
            return;
        }

        if (! is_logged_in()) { // User is not logged in
            return;
        }

        $this->load_Blog();
        if (! $this->Blog->get_setting('track_unread_content')) {	// The tracking of unread content is turned off for the collection
            return;
        }

        global $DB, $current_User, $localtimenow;

        $timestamp = date2mysql($localtimenow);

        $read_date = $this->get_user_data('item_date');

        if ($timestamp == $read_date) {	// The read status is already updated, Don't repeat it:
            return;
        }

        if (! is_null($read_date)) {	// Update the read status:
            $update_fields = '';
            if ($read_post) {	// Update a post read timestamp:
                $update_fields = 'itud_read_item_ts = ' . $DB->quote($timestamp);
            }
            if ($read_comments) {	// Update a comments read timestamp:
                if ($read_post) {
                    $update_fields .= ', ';
                }
                $update_fields .= 'itud_read_comments_ts = ' . $DB->quote($timestamp);
            }
            $DB->query('UPDATE T_items__user_data
				  SET ' . $update_fields . '
				WHERE itud_user_ID = ' . $DB->quote($current_User->ID) . '
				  AND itud_item_ID = ' . $DB->quote($this->ID));
        } else {	// Insert new read status:
            $insert_fields = '';
            $insert_values = '';
            if ($read_post) {	// Update a post read timestamp:
                $insert_fields = 'itud_read_item_ts';
                $insert_values = $DB->quote($timestamp);
            }
            if ($read_comments) {	// Update a comments read timestamp:
                if ($read_post) {
                    $insert_fields .= ', ';
                    $insert_values .= ', ';
                }
                $insert_fields .= 'itud_read_comments_ts';
                $insert_values .= $DB->quote($timestamp);
            }
            $DB->query('INSERT INTO T_items__user_data ( itud_user_ID, itud_item_ID, ' . $insert_fields . ' )
				VALUES ( ' . $DB->quote($current_User->ID) . ', ' . $DB->quote($this->ID) . ', ' . $insert_values . ' )');
        }

        // Update the cached item date:
        $this->set_user_data('item_date', $timestamp);
    }

    /**
     * Load timestamp when this post content was read by the current User
     */
    public function load_content_read_status()
    {
        if (! is_logged_in()) {
            return;
        }

        if (! empty($this->content_read_status)) {
            return;
        }

        $this->content_read_status = $this->get_read_status();
    }

    /**
     * Get the read status of this post and its comments for current User
     *
     * @return string 'read' - when current User already read this post
     *                'updated' - current user didn't read some new changes
     *                'new' - the post is new for current user
     */
    public function get_read_status()
    {
        if ($this->ID == 0) { // Item is not saved in DB
            return 'read';
        }

        if (! is_logged_in()) { // User is not logged in
            return 'read';
        }

        $this->load_Blog();
        if (! $this->Blog->get_setting('track_unread_content')) {	// The tracking of unread content is turned off for the collection
            return 'read';
        }

        global $DB;

        $read_date = $this->get_user_data('item_date');

        if (empty($read_date)) {	// This post is recent for current user
            return 'new';
        }

        // In theory, it would be more safe to use this comparison:
        // if( $read_date > $this->contents_last_updated_ts )
        // But until we have milli- or micro-second precision on timestamps, we decided it was a better trade-off to never see our own edits as unread. So we use:
        if ($read_date >= $this->contents_last_updated_ts) {	// This post was read by current user
            return 'read';
        }

        // This post is Unread by current user
        return 'updated';
    }

    /**
     * Get the user data from DB or from Cached array of this post for current User
     *
     * @param string|null Field name: 'item_date', 'comments_date', 'item_flag', NULL - to get all fields as array
     * @return array|string Array of all fields OR value of single field
     */
    public function get_user_data($field = null)
    {
        global $DB, $current_User, $cache_items_user_data;

        if (! is_array($cache_items_user_data)) {	// Init array first time:
            $cache_items_user_data = [];
        }

        if (! isset($cache_items_user_data[$this->ID])) { // Get the read post date only one time from DB and store it in cache array
            $SQL = new SQL('Get the data of item #' . $this->ID . ' for user #' . $current_User->ID);
            $SQL->SELECT('IFNULL( itud_read_item_ts, 0 ) AS item_date, IFNULL( itud_read_comments_ts, 0 ) AS comments_date, itud_flagged_item AS item_flag');
            $SQL->FROM('T_items__user_data');
            $SQL->WHERE('itud_user_ID = ' . $DB->quote($current_User->ID));
            $SQL->WHERE_and('itud_item_ID = ' . $DB->quote($this->ID));
            $cache_items_user_data[$this->ID] = $DB->get_row($SQL, ARRAY_A);
        }

        if (isset($cache_items_user_data[$this->ID]) && is_array($cache_items_user_data[$this->ID]) && empty($cache_items_user_data[$this->ID])) {	// Init empty user item data:
            $cache_items_user_data[$this->ID] = null;
        }

        if ($field === null) {	// Return all fields as array:
            return $cache_items_user_data[$this->ID];
        } else {	// Return a value of single field:
            return isset($cache_items_user_data[$this->ID][$field]) ? $cache_items_user_data[$this->ID][$field] : null;
        }
    }

    /**
     * Set the user data to global cache array of this post for current User
     *
     * @param string Field name: 'item_date', 'comments_date', 'item_flag'
     * @param string Value
     */
    public function set_user_data($field, $value)
    {
        global $cache_items_user_data;

        if (! isset($cache_items_user_data[$this->ID]) || ! is_array($cache_items_user_data[$this->ID])) {	// Initialize array:
            $cache_items_user_data[$this->ID] = [];
        }

        $cache_items_user_data[$this->ID][$field] = $value;
    }

    /**
     * Get a color read status icon if this post is unread by current User
     *
     * @param array Params
     * @return string
     */
    public function get_unread_status($params = [])
    {
        $r = '';

        $this->load_Blog();
        if (! $this->Blog->get_setting('track_unread_content')) {	// The tracking of unread content is turned off for the collection
            return $r;
        }

        // Set titles by Blog type:
        $this->get_ItemType();
        $title_new = $this->ItemType->get_item_denomination('title_new');
        $title_updated = $this->ItemType->get_item_denomination('title_updated');

        // Merge params
        $params = array_merge([
            'before' => ' ',
            'after' => '',
            'class' => 'track_content',
            'style' => 'icon', // 'text'
            'title_new' => $title_new,
            'title_updated' => $title_updated,
            'title_read' => T_('Read'),
            'text_new' => T_('New'),
            'text_updated' => T_('Updated'),
            'text_read' => T_('Read'),
            'class_new' => 'label label-warning',
            'class_updated' => 'label label-danger',
            'class_read' => 'label label-success',
        ], $params);

        switch ($this->get_read_status()) {
            case 'new':
                // This post is new for the current User, it was never opened
                $r .= $params['before'];
                if ($params['style'] == 'text') {	// Text style:
                    $r .= '<span'
                        . (empty($params['class_new']) ? '' : ' class="' . $params['class_new'] . '"')
                        . (empty($params['title_new']) ? '' : ' class="' . $params['title_new'] . '"') . '>'
                            . $params['text_new']
                        . '</span>';
                } else {	// Icon style:
                    $r .= get_icon('bullet_orange', 'imgtag', [
                        'title' => $params['title_new'],
                        'class' => $params['class'],
                    ]);
                }
                $r .= $params['after'];
                break;

            case 'updated':
                // The last updates of this post was not read by the current User
                $r .= $params['before'];
                if ($params['style'] == 'text') {	// Text style:
                    $r .= '<span'
                        . (empty($params['class_updated']) ? '' : ' class="' . $params['class_updated'] . '"')
                        . (empty($params['title_updated']) ? '' : ' class="' . $params['title_updated'] . '"') . '>'
                            . $params['text_updated']
                        . '</span>';
                } else {	// Icon style:
                    $r .= get_icon('bullet_brown', 'imgtag', [
                        'title' => $params['title_updated'],
                        'class' => $params['class'],
                    ]);
                }
                $r .= $params['after'];
                break;

            case 'read':
            default:
                // Don't display status icons if user already have read this post
                if ($params['style'] == 'text') {	// Text style:
                    $r .= $params['before'];
                    $r .= '<span'
                        . (empty($params['class_read']) ? '' : ' class="' . $params['class_read'] . '"')
                        . (empty($params['title_read']) ? '' : ' class="' . $params['title_read'] . '"') . '>'
                            . $params['text_read']
                        . '</span>';
                    $r .= $params['after'];
                }
                // No icon for read status.
                break;
        }

        return $r;
    }

    /**
     * Display a color read status icon if this post is unread by current User
     *
     * @param array Params
     */
    public function display_unread_status($params = [])
    {
        echo $this->get_unread_status($params);
    }

    /**
     * Check if item has a goal to insert a hit into DB
     *
     * @return boolean TRUE if goal hit was inser
     */
    public function check_goal()
    {
        $goal_ID = $this->get_setting('goal_ID');

        if (empty($goal_ID)) { // Item has no goal ID
            return false;
        }

        $GoalCache = &get_GoalCache();
        if (($Goal = $GoalCache->get_by_ID($goal_ID, false, false)) === false) { // Goal ID is incorrect
            return false;
        }

        global $Hit, $DB;

        // We need to log the HIT now! Because we need the hit ID!
        $Hit->log();

        // Record a goal hit:
        return $DB->query(
            'INSERT INTO T_track__goalhit
			       ( ghit_goal_ID, ghit_hit_ID, ghit_params )
			VALUES ( ' . $Goal->ID . ', ' . $Hit->ID . ', ' . $DB->quote('item_ID=' . $this->ID) . ' )',
            'Record goal hit of item #' . $this->ID
        );
    }

    /**
     * Get link to edit post type
     *
     * @param string What attibute to return:
     *                    'link' - html tag <a>
     *                    'url' - URL
     *                    'onclick' - javascript event onclick
     * @param string Link text
     * @param string Link title
     * @return string
     */
    public function get_type_edit_link($attr = 'link', $link_text = '', $link_title = '')
    {
        global $admin_url;

        // Check if current user can edit the type of this item
        $has_perm_edit = check_user_perm('item_post!CURSTATUS', 'edit', false, $this);

        if ($has_perm_edit) { // Initialize url params only when current user has a permission to edit this
            if ($attr != 'onclick') { // Init an url
                if ($this->ID > 0) {	// URL when item is editing:
                    $attr_href = $admin_url . '?ctrl=items&amp;action=edit_type&amp;post_ID=' . $this->ID;
                } elseif (get_param('p') > 0) {	// URL when item is duplicating:
                    $attr_href = $admin_url . '?ctrl=items&amp;action=new_type&amp;p=' . get_param('p');
                } else {	// URL when item is creating:
                    $attr_href = $admin_url . '?ctrl=items&amp;action=new_type';
                }
            }

            if ($attr != 'url') { // Init an event 'onclick'
                $attr_onclick = 'return b2edit_type( \'' . TS_('Do you want to save your changes before changing the Post Type?') . '\','
                    . ' \'' . $admin_url . '?ctrl=items&amp;blog=' . $this->get_blog_ID() . '\','
                    . ' \'' . ($this->ID > 0 ? 'edit_type' : 'new_type') . '\' );';
            }
        }

        switch ($attr) {
            case 'link':
                if (empty($attr_href)) { // No perm to edit item type
                    return $link_text;
                } else { // Current user can edit this item
                    return '<a href="' . $attr_href . '" onclick="' . $attr_onclick . '" title="' . $link_title . '" class="post_type_link">' . $link_text . '</a>';
                }
                break;

            case 'onclick':
                return empty($attr_onclick) ? '' : $attr_onclick;
                break;

            case 'url':
                return empty($attr_href) ? '' : $attr_href;
                break;
        }
    }

    /**
     * Get custom fields of post type
     *
     * @param string Type(s) of custom field: 'all', 'varchar', 'double', 'text', 'html', 'url', 'image', 'computed', 'separator'. Use comma separator to get several types
     * @param boolean TRUE to force use custom fields of current version instead of revision
     * @return array
     */
    public function get_type_custom_fields($type = 'all', $force_current_fields = false)
    {
        if (! $force_current_fields && $this->is_revision()) {	// Get custom fields of current active revision:
            if (! isset($this->custom_fields)) {	// Initialize an array only first time:
                $this->custom_fields = [];
                if (! empty($this->ID) &&
                    ($Revision = &$this->get_revision())) {	// Get the custom fields from DB:
                    global $DB;
                    $SQL = new SQL('Get custom fields of revision #' . $Revision->iver_ID . '(' . $Revision->iver_type . ') for Item #' . $this->ID);
                    $SQL->SELECT('ivcf_itcf_ID AS ID, itcf_ityp_ID AS ityp_ID, ivcf_itcf_label AS label, IFNULL( itcf_name, CONCAT( "!deleted_", ivcf_itcf_ID ) ) AS name, itcf_type AS type, IFNULL( itcf_order, 999999999 ) AS `order`, itcf_note AS note, ');
                    $SQL->SELECT_add('itcf_required AS required, itcf_meta AS meta, itcf_public AS public, itcf_format AS format, itcf_formula AS formula, itcf_disp_condition AS disp_condition, itcf_header_class AS header_class, itcf_cell_class AS cell_class, ');
                    $SQL->SELECT_add('itcf_link AS link, itcf_link_nofollow AS link_nofollow, itcf_link_class AS link_class, ');
                    $SQL->SELECT_add('itcf_line_highlight AS line_highlight, itcf_green_highlight AS green_highlight, itcf_red_highlight AS red_highlight, itcf_description AS description, itcf_merge AS merge');
                    $SQL->FROM('T_items__version_custom_field');
                    $SQL->FROM_add('LEFT JOIN T_items__type_custom_field ON ivcf_itcf_ID = itcf_ID');
                    $SQL->WHERE_and('ivcf_iver_ID = ' . $DB->quote($Revision->iver_ID));
                    $SQL->WHERE_and('ivcf_iver_type = ' . $DB->quote($Revision->iver_type));
                    $SQL->WHERE_and('ivcf_iver_itm_ID = ' . $DB->quote($Revision->iver_itm_ID));
                    $SQL->ORDER_BY('`order`, ivcf_itcf_ID');
                    $custom_fields = $DB->get_results($SQL, ARRAY_A);
                    foreach ($custom_fields as $custom_field) {
                        $this->custom_fields[$custom_field['name']] = $custom_field;
                    }
                }
            }

            return $this->custom_fields;
        } else {	// Get custom fields of current item type:
            if (! $this->get_ItemType()) { // Unknown post type
                return [];
            }

            return $this->ItemType->get_custom_fields($type);
        }
    }

    /**
     * Check if post type is enabled for the post collection
     *
     * @return boolean
     */
    public function is_type_enabled()
    {
        $ityp_ID = intval($this->get('ityp_ID'));

        if (empty($ityp_ID)) {
            return false;
        }

        $item_Blog = &$this->get_Blog();

        return $item_Blog->is_item_type_enabled($ityp_ID);
    }

    /**
     * Check if item allows the statuses for the comments (closed or disabled)
     *
     * @return boolean TRUE when item can has the comment status different of 'opened'
     */
    public function allow_comment_statuses()
    {
        if (! $this->get_type_setting('use_comments')) { // The comments are not allowed for this post type
            return false;
        }

        if (! $this->get_type_setting('allow_closing_comments') && ! $this->get_type_setting('allow_disabling_comments')) { // The statuses 'closed' & 'disabled' are not allowed for comments of this post type
            return false;
        }

        $this->load_Blog();
        if ($this->Blog->get_setting('allow_comments') == 'never') { // The comments are not allowed by Blog
            return false;
        }

        return true;
    }

    /**
     * Country is visible for defining
     *
     * @return boolean TRUE if users can define a country for posts of current blog
     */
    public function country_visible()
    {
        return $this->get_type_setting('use_country') != 'never' || $this->region_visible();
    }

    /**
     * Region is visible for defining
     *
     * @return boolean TRUE if users can define a region for this post
     */
    public function region_visible()
    {
        return $this->get_type_setting('use_region') != 'never' || $this->subregion_visible();
    }

    /**
     * Subregion is visible for defining
     *
     * @return boolean TRUE if users can define a subregion for this post
     */
    public function subregion_visible()
    {
        return $this->get_type_setting('use_sub_region') != 'never' || $this->city_visible();
    }

    /**
     * City is visible for defining
     *
     * @return boolean TRUE if users can define a city for this post
     */
    public function city_visible()
    {
        return $this->get_type_setting('use_city') != 'never';
    }

    /**
     * Get the parent Item
     *
     * @return object Item
     */
    public function &get_parent_Item()
    {
        if (! empty($this->parent_Item) &&
            $this->parent_Item->ID == $this->parent_ID) {	// Return the initialized parent Item and if it is really parent of this Item:
            return $this->parent_Item;
        }

        if (empty($this->parent_ID)) {	// No defined parent Item
            $this->parent_Item = null;
            return $this->parent_Item;
        }

        if ($this->get_type_setting('use_parent') == 'never') {	// Parent Item is not allowed for current item type
            $this->parent_Item = null;
            return $this->parent_Item;
        }

        $ItemCache = &get_ItemCache();
        $this->parent_Item = &$ItemCache->get_by_ID($this->parent_ID, false, false);

        return $this->parent_Item;
    }

    /**
     * Extract all possible tags from item contents
     *
     * @return array Tags
     */
    public function search_tags_by_content()
    {
        global $DB;

        // Concatenate all text item fields:
        $search_string = $this->get('title') . ' '
            . $this->get('content') . ' '
            . $this->get('excerpt') . ' '
            . $this->get('titletag') . ' '
            . $this->get_setting('metadesc') . ' '
            . $this->get_setting('metakeywords') . ' ';
        // + all text custom fields:
        $text_custom_fields = $this->get_type_custom_fields('varchar,text,html');
        foreach ($text_custom_fields as $field_name => $text_custom_field) {
            $search_string .= $this->get_custom_field_value($field_name) . ' ';
        }

        // Clear spaces:
        $search_string = utf8_trim($search_string);

        if (empty($search_string)) {	// This item has no content, so don't try to run a searching:
            return [];
        }

        // Get all possible tags that are not related to this item:
        $other_tags_SQL = new SQL('Get all possible tags that are not related to this item');
        $other_tags_SQL->SELECT('tag_name');
        $other_tags_SQL->FROM('T_items__tag');
        // Get all current tags to exclude from searching:
        $item_tags = $this->get_tags();
        if (count($item_tags)) {	// If this item has at least one tag, Exclude them:
            $other_tags_SQL->WHERE('tag_name NOT IN ( ' . $DB->quote($item_tags) . ' )');
        }
        $other_tags = $DB->get_col($other_tags_SQL);

        if (count($other_tags) == 0) {	// No tags for searching, Exit here:
            return [];
        }

        // Try to find each tag in content as separate word:
        foreach ($other_tags as $i => $other_tag) {
            if (! preg_match('/\b' . $other_tag . '\b/i', $search_string)) {	// This tag is not found, Exclude it:
                unset($other_tags[$i]);
            }
        }

        return $other_tags;
    }

    /**
     * Restrict Item status by Collection access restriction AND by CURRENT USER write perm
     *
     * @param boolean TRUE to update status
     * @param boolean TRUE to display messages
     */
    public function restrict_status($update_status = false, $display_messages = true)
    {
        $item_Blog = &$this->get_Blog();

        // Store current status to display a warning:
        $current_status = $this->get('status');

        // Checks if the requested item status can be used by current user and if not, get max allowed item status of the collection
        $restricted_status = $item_Blog->get_allowed_item_status($current_status, $this);

        if ($update_status) {	// Update status to new restricted value:
            $this->set('status', $restricted_status);
        } else {	// Only change status to update it on the edit forms:
            $this->status = $restricted_status;
        }

        if ($current_status != $this->get('status') && $display_messages) {	// If current item status cannot be used for item collection
            global $Messages;

            $visibility_statuses = get_visibility_statuses();
            if ($item_Blog->get_setting('allow_access') == 'members') {	// The collection is restricted for members or only for owner
                if (! $item_Blog->get('advanced_perms')) {	// If advanced permissions are NOT enabled then only owner has an access for the collection
                    $Messages->add(sprintf(T_('Since this collection is "Private", the visibility of this post will be restricted to "%s".'), $visibility_statuses[$this->status]), 'warning');
                } else {	// Otherwise all members of this collection have an access for the collection
                    $Messages->add(sprintf(T_('Since this collection is "Members only", the visibility of this post will be restricted to "%s".'), $visibility_statuses[$this->status]), 'warning');
                }
            } elseif ($item_Blog->get_setting('allow_access') == 'users') {	// The collection is restricted for logged-in users only:
                $Messages->add(sprintf(T_('Since this collection is "Community only", the visibility of this post will be restricted to "%s".'), $visibility_statuses[$this->status]), 'warning');
            }
        }
    }

    /**
     * Check what were already notified on this item
     *
     * @param array|string Flags, possible values: 'moderators_notified', 'members_notified', 'community_notified', 'pings_sent'
     */
    public function check_notifications_flags($flags)
    {
        if (! is_array($flags)) {	// Convert string to array:
            $flags = [$flags];
        }

        // TRUE if all requested flags are in current item notifications flags:
        return (count(array_diff($flags, $this->get('notifications_flags'))) == 0);
    }

    /**
     * Check if notifications are allowed for this Item
     * (some item types have no permanent URL and thus cannot be sent out with a permalink)
     *
     * @return boolean TRUE if allowed
     */
    public function notifications_allowed()
    {
        return ($this->get_type_setting('usage') != 'special');
    }

    /**
     * Check if this item can be displayed for current user on front-office
     *
     * @return boolean
     */
    public function can_be_displayed()
    {
        if (empty($this->ID)) {	// Item is not created yet, so it cannot be displayed:
            return false;
        }

        // Check if this Item can be displayed with current status:
        return can_be_displayed_with_status($this->get('status'), 'post', $this->get_blog_ID(), $this->creator_user_ID);
    }

    /*
     * Check if user can flag this item
     *
     * @return boolean
     */
    public function can_flag()
    {
        if (empty($this->ID)) {	// Item is not created yet:
            return false;
        }

        if (! is_logged_in()) {	// If user is NOT logged in:
            return false;
        }

        if ($this->get_type_setting('usage') != 'post') {	// Only "Post" items can be flagged:
            return false;
        }

        return true;
    }

    /**
     * Display button to flag item
     *
     * @param array Params
     */
    public function flag($params = [])
    {
        echo $this->get_flag($params);
    }

    /**
     * Get button to flag item
     *
     * @param array Params
     * @return string HTML of the button
     */
    public function get_flag($params = [])
    {
        $params = array_merge([
            'before' => '',
            'after' => '',
            'title_flag' => T_('You have flagged this. Click to remove flag.'),
            'title_unflag' => T_('Click to flag this.'),
            'only_flagged' => false, // Display the flag button only when this item is already flagged by current User
            'allow_toggle' => true, // Allow to toggle flag state by AJAX
        ], $params);

        if (! $this->can_flag()) {	// Don't display the flag button if it is not allowed by some reason:
            return '';
        }

        $item_Blog = &$this->get_Blog();

        // Get current state of flag:
        $is_flagged = $this->get_user_data('item_flag');

        if ($params['only_flagged'] && ! $is_flagged) {	// Don't display the button because of request to display it only for the flagged items by current User:
            return '';
        }

        $r = $params['before'];

        if ($params['allow_toggle']) {	// Allow to toggle:
            $r .= '<a href="#" data-id="' . $this->ID . '" data-coll="' . $item_Blog->get('urlname') . '" class="action_icon evo_post_flag_btn">'
                . get_icon('flag_on', 'imgtag', [
                    'title' => $params['title_flag'],
                    'style' => $is_flagged ? '' : 'display:none',
                ])
                . get_icon('flag_off', 'imgtag', [
                    'title' => $params['title_unflag'],
                    'style' => $is_flagged ? 'display:none' : '',
                ])
            . '</a>';
        } else {	// Display only current flag state as icon:
            $r .= '<span class="action_icon evo_post_flag_btn">'
                . get_icon(($is_flagged ? 'flag_on' : 'flag_off'), 'imgtag', [
                    'title' => ($is_flagged ? $params['title_flag'] : $params['title_unflag']),
                ])
                . '</span>';
        }

        $r .= $params['after'];

        return $r;
    }

    /**
     * Flag or unflag item for current user
     *
     * @param string Vote value (positive, neutral, negative)
     * @access protected
     */
    public function update_flag()
    {
        global $DB, $current_User, $servertimenow;

        if (! $this->can_flag()) {	// Don't display the flag button if it is not allowed by some reason:
            return;
        }

        $DB->begin();

        // Get current state of flag:
        $is_flagged = $this->get_user_data('item_flag');

        $new_flag_value = ($is_flagged ? 0 : 1);

        if (is_null($is_flagged)) {	// Flag item for current user:
            $DB->query(
                'REPLACE INTO T_items__user_data
				       ( itud_user_ID, itud_item_ID, itud_flagged_item )
				VALUES ( ' . $DB->quote($current_User->ID) . ', ' . $DB->quote($this->ID) . ', ' . $new_flag_value . ' )',
                'Insert user item data row to flag item #' . $this->ID
            );
        } else {	// Update flag of this item for current user:
            $DB->query(
                'UPDATE T_items__user_data
				  SET itud_flagged_item = ' . $new_flag_value . '
				WHERE itud_user_ID = ' . $DB->quote($current_User->ID) . '
				  AND itud_item_ID = ' . $DB->quote($this->ID),
                'Update user item data row to flag item #' . $this->ID
            );
        }

        $this->set_user_data('item_flag', $new_flag_value);

        // Invalidate key for the Item data per current User:
        BlockCache::invalidate_key('item_user_flag_' . $this->ID, $current_User->ID);

        $DB->commit();
    }

    /**
     * Check if user can vote on this item
     *
     * @return boolean
     */
    public function can_vote()
    {
        if (empty($this->ID)) {	// Item is not created yet:
            return false;
        }

        if (! is_logged_in(false)) {	// If user is NOT logged in:
            return false;
        }

        $item_Blog = &$this->get_Blog();

        if (empty($item_Blog) || ! $item_Blog->get_setting('voting_positive')) {	// If current collection doesn't allow a voting on items:
            return false;
        }

        return true;
    }

    /**
     * Display buttons to vote on item if user is logged
     *
     * @param array Params
     */
    public function display_voting_panel($params = [])
    {
        global $current_User;

        $params = array_merge([
            'before' => '',
            'after' => '',
            'class' => '',
            'widget_ID' => 0,
            'skin_ID' => 0,
            'label_text' => T_('My vote:'),
            'title_like' => T_('Cast a positive vote!'),
            'title_like_voted' => T_('You sent a positive vote.'),
            'title_noopinion' => T_('Cast a neutral vote!'),
            'title_noopinion_voted' => T_('You sent a neutral vote.'),
            'title_dontlike' => T_('Cast a negative vote!'),
            'title_dontlike_voted' => T_('You sent a negative vote.'),
            'title_empty' => T_('No user votes yet.'),
            'title_own' => T_('You cannot vote on own Item.'),
            'display_summary' => 'replace', // 'no' - Don't display, 'replace' - Replace label after vote, 'always' - Always display after icons
            'display_summary_author' => true, // Display summary for author
            'display_wrapper' => true, // Use FALSE when you update this from AJAX request
            'display_score' => false,
            'display_noactive' => false, // Display not active icons, when current User is owner of this Item
            'display_like' => true,
            'display_noopinion' => true,
            'display_dontlike' => true,
            'icon_like_active' => 'thumb_up',
            'icon_like_noactive' => 'thumb_up_disabled',
            'icon_noopinion_active' => 'ban',
            'icon_noopinion_noactive' => 'ban_disabled',
            'icon_dontlike_active' => 'thumb_down',
            'icon_dontlike_noactive' => 'thumb_down_disabled',
        ], $params);

        if (! $this->can_vote()) {	// Don't display the voting panel if a voting on this item is not allowed by some reason:
            return;
        }

        echo $params['before'];

        if ($params['display_wrapper']) {	// Display wrapper:
            echo '<span id="vote_item_' . $this->ID . '" class="evo_voting_panel ' . (empty($params['class']) ? '' : ' ' . $params['class']) . '">';
        }

        if ($current_User->ID == $this->creator_user_ID) {	// Display only vote summary/score for users on their own items:
            if ($params['display_noactive'] && $params['display_like']) {	// Display disabled 'Like' icon:
                echo get_icon($params['icon_like_noactive'], 'imgtag', [
                    'title' => $params['title_own'],
                ]);
            }

            if ($params['display_noactive'] && $params['display_noopinion']) {	// Display disabled 'No opinion' icon:
                echo get_icon($params['icon_noopinion_noactive'], 'imgtag', [
                    'title' => $params['title_own'],
                ]);
            }

            if ($params['display_score']) {	// Display score:
                echo '<span class="vote_score">' . $this->get('addvotes') . '</span>';
            } elseif ($params['display_summary_author']) {	// Display summary:
                $params['result_title_undecided'] = T_('Voting:');
                $params['after_result'] = '.';
                $result_summary = $this->get_vote_summary($params);
                echo(! empty($result_summary) ? $result_summary : $params['title_empty']);
            }

            if ($params['display_noactive'] && $params['display_dontlike']) {	// Display disabled 'Don't like' icon:
                echo get_icon($params['icon_dontlike_noactive'], 'imgtag', [
                    'title' => $params['title_own'],
                ]);
            }
        } else {	// Display form to vote:
            $title_text = $params['label_text'];
            $after_voting_form = '';

            if ($params['display_summary'] != 'no') {	// If we should display summary:
                $vote_result = $this->get_vote_disabled();

                if ($vote_result['is_voted'] && $params['display_summary'] == 'replace') {	// Replace title with vote summary if user already voted on this item:
                    $title_text = $this->get_vote_summary($params);
                }

                if ($params['display_summary'] == 'always') {	// Always display vote summary after icons:
                    $after_voting_form = $this->get_vote_summary($params);
                }
            }

            $item_Blog = &$this->get_Blog();

            display_voting_form(array_merge([
                'vote_type' => 'item',
                'vote_ID' => $this->ID,
                'display_like' => $item_Blog->get_setting('voting_positive'),
                'display_noopinion' => $item_Blog->get_setting('voting_neutral'),
                'display_dontlike' => $item_Blog->get_setting('voting_negative'),
                'display_inappropriate' => false,
                'display_spam' => false,
                'title_text' => $title_text . ' ',
            ], $params));

            echo $after_voting_form;
        }

        if ($params['display_wrapper']) {	// Display wrapper:
            echo '</span>';
        }

        echo $params['after'];
    }

    /**
     * Set the vote, as a number.
     *
     * @param string Vote value (positive, neutral, negative)
     * @access protected
     */
    public function set_vote($vote_value)
    {
        global $DB, $current_User, $servertimenow;

        if (! $this->can_vote()) {	// A voting on this item is not allowed by some reason:
            return;
        }

        switch ($vote_value) {	// Set a value for voting:
            case 'positive':
                $vote = '1';
                break;
            case 'neutral':
                $vote = '0';
                break;
            case 'negative':
                $vote = '-1';
                break;
            default:
                // $vote_value is not correct from ajax request
                return;
        }

        $DB->begin();

        $SQL = new SQL('Check if current user already voted on item #' . $this->ID);
        $SQL->SELECT('itvt_updown');
        $SQL->FROM('T_items__votes');
        $SQL->WHERE('itvt_item_ID = ' . $DB->quote($this->ID));
        $SQL->WHERE_and('itvt_user_ID = ' . $DB->quote($current_User->ID));
        $existing_vote = $DB->get_var($SQL);

        if ($existing_vote === null) {	// Add a new vote for first time:
            // Use a replace into to avoid duplicate key conflict in case when user clicks two times fast one after the other:
            $DB->query(
                'REPLACE INTO T_items__votes
				       ( itvt_item_ID, itvt_user_ID, itvt_updown, itvt_ts )
				VALUES ( ' . $DB->quote($this->ID) . ', ' . $DB->quote($current_User->ID) . ', ' . $DB->quote($vote) . ', ' . $DB->quote(date2mysql($servertimenow)) . ' )',
                'Add new vote on item #' . $this->ID
            );
        } else {	// Update a vote:
            if ($existing_vote == $vote) {	// Undo previous vote:
                $DB->query(
                    'DELETE FROM T_items__votes
					WHERE itvt_item_ID = ' . $DB->quote($this->ID) . '
						AND itvt_user_ID = ' . $DB->quote($current_User->ID),
                    'Undo previous vote on item #' . $this->ID
                );
            } else {	// Set new vote:
                $DB->query(
                    'UPDATE T_items__votes
						SET itvt_updown = ' . $DB->quote($vote) . '
					WHERE itvt_item_ID = ' . $DB->quote($this->ID) . '
						AND itvt_user_ID = ' . $DB->quote($current_User->ID),
                    'Update a vote on item #' . $this->ID
                );
            }
        }

        $vote_SQL = new SQL('Get voting results of item #' . $this->ID);
        $vote_SQL->SELECT('COUNT( itvt_updown ) AS votes_count, SUM( itvt_updown ) AS votes_sum');
        $vote_SQL->FROM('T_items__votes');
        $vote_SQL->WHERE('itvt_item_ID = ' . $DB->quote($this->ID));
        $vote_SQL->WHERE_and('itvt_updown IS NOT NULL');
        $vote = $DB->get_row($vote_SQL);

        // These values must be number and not NULL:
        $vote->votes_sum = intval($vote->votes_sum);
        $vote->votes_count = intval($vote->votes_count);

        // Update fields with vote counters for this item:
        $DB->query(
            'UPDATE T_items__item
			  SET post_addvotes = ' . $DB->quote($vote->votes_sum) . ',
			      post_countvotes = ' . $DB->quote($vote->votes_count) . '
			WHERE post_ID = ' . $DB->quote($this->ID),
            'Update fields with vote counters for item #' . $this->ID
        );
        $this->addvotes = $vote->votes_sum;
        $this->countvotes = $vote->votes_count;

        $DB->commit();

        return;
    }

    /**
     * Get the vote helpful type disabled, as array.
     *
     * @return array Result:
     *               'is_voted' - TRUE if current user already voted on this comment
     *               'icons_statuses': array( 'yes', 'no' )
     */
    public function get_vote_disabled()
    {
        global $DB, $current_User;

        $result = [
            'is_voted' => false,
            'icons_statuses' => [
                'yes' => '',
                'no' => '',
            ],
        ];

        if (! $this->can_vote()) {	// A voting on this item is not allowed by some reason:
            $result;
        }

        $SQL = new SQL('Get a vote result for current for item #' . $this->ID);
        $SQL->SELECT('itvt_updown');
        $SQL->FROM('T_items__votes');
        $SQL->WHERE('itvt_item_ID = ' . $DB->quote($this->ID));
        $SQL->WHERE_and('itvt_user_ID = ' . $DB->quote($current_User->ID));
        $SQL->WHERE_and('itvt_updown IS NOT NULL');

        if ($vote = $DB->get_row($SQL)) {	// Get a vote for current user and this item:
            $result['is_voted'] = true;
            $class_disabled = 'disabled';
            $class_voted = 'voted';
            switch ($vote->itvt_updown) {
                case '1': //
                    $result['icons_statuses']['yes'] = $class_voted;
                    $result['icons_statuses']['no'] = $class_disabled;
                    break;
                case '-1': // NO
                    $result['icons_statuses']['no'] = $class_voted;
                    $result['icons_statuses']['yes'] = $class_disabled;
                    break;
            }
        }

        return $result;
    }

    /**
     * Get the vote summary, as a string.
     *
     * @param type Vote type (spam, helpful)
     * @param srray Params
     * @return string
     */
    public function get_vote_summary($params = [])
    {
        $params = array_merge([
            'result_title' => '',
            'result_title_undecided' => '',
            'after_result' => '',
        ], $params);

        if (! $this->can_vote()) {	// A voting on this item is not allowed by some reason:
            return '';
        }

        if ($this->countvotes == 0) {	// No votes for current comment:
            return '';
        }

        $item_Blog = &$this->get_Blog();

        if ($item_Blog->get_setting('voting_positive') &&
            ! $item_Blog->get_setting('voting_neutral') &&
            ! $item_Blog->get_setting('voting_negative')) {	// Only the likes are enabled, Display a count of them:
            $summary = ($this->countvotes > 0) ? sprintf(T_('%s Likes'), $this->countvotes) : T_('No likes');
        } else {	// Calculate vote summary in percents:
            $summary = ceil($this->addvotes / $this->countvotes * 100);

            if ($summary < -20) {	// Item is positive
                $summary = abs($summary) . '% ' . T_('Negative');
            } elseif ($summary >= -20 && $summary <= 20) {	// Item is UNDECIDED:
                $summary = T_('UNDECIDED');
                if (! empty($params['result_title_undecided'])) {	// Display title before undecided results:
                    $summary = $params['result_title_undecided'] . ' ' . $summary;
                }
            } elseif ($summary > 20) {	// Item is negative:
                $summary .= '% ' . T_('Positive');
            }
        }

        if (! empty($params['result_title'])) {	// Display title before results:
            $summary = $params['result_title'] . ' ' . $summary;
        }

        return $summary . $params['after_result'] . ' ';
    }

    /**
     * Get a message to display before comment form
     *
     * @return string
     */
    public function get_comment_form_msg()
    {
        if ($this->get_type_setting('allow_comment_form_msg')) {	// If custom message is allowed by Item Type:
            $item_msg = trim($this->get_setting('comment_form_msg'));
            if (! empty($item_msg)) {	// Use custom message of this item:
                return $item_msg;
            }
        }

        // Try to use a message from Collection setting:
        $item_Blog = &$this->get_Blog();
        $collection_msg = trim($item_Blog->get_setting('comment_form_msg'));
        if (! empty($collection_msg)) {	// Use a message of the item type:
            return $collection_msg;
        }

        // Try to use a message from Item Type setting:
        $item_type_msg = trim($this->get_type_setting('comment_form_msg'));
        if (! empty($item_type_msg)) {	// Use a message of the item type:
            return $item_type_msg;
        }
    }

    /**
     * Display a message before comment form
     *
     * @param array Params
     */
    public function display_comment_form_msg($params = [])
    {
        $params = array_merge([
            'before' => '<div class="alert alert-warning">',
            'after' => '</div>',
        ], $params);

        // Get a message:
        $comment_form_msg = $this->get_comment_form_msg();

        if (empty($comment_form_msg)) {	// No message to display before comment form, Exit here:
            return;
        }

        // Display a message:
        echo $params['before'];
        echo nl2br($comment_form_msg);
        echo $params['after'];
    }

    /**
     * Check if current User has a permission to refresh a contents last updated date of this Item
     *
     * @return boolean
     */
    public function can_refresh_contents_last_updated()
    {
        if (! $this->ID) {	// If this Item is not saved in DB yet:
            return false;
        }

        if (! check_user_perm('item_post!CURSTATUS', 'edit', false, $this, false)) {	// If user has no perm to edit this Item:
            return false;
        }

        // No restriction, Current User has a permission to refresh a contents last updated date of this Item:
        return true;
    }

    /*
     * Get URL to refresh a contents last updated date of this Item if user has refresh rights
     *
     * @param array Params
     * @return string|boolean URL or FALSE if current user has no perm
     */
    public function get_refresh_contents_last_updated_url($params = [])
    {
        if (! $this->can_refresh_contents_last_updated()) {	// If current User has no perm to refresh:
            return false;
        }

        $params = array_merge([
            'glue' => '&amp;',
            'type' => 'touch', // What comment date use to update: 'touch' - 'comment_last_touched_ts', 'create' - 'comment_date'
        ], $params);

        $url = get_htsrv_url() . 'action.php?mname=collections' . $params['glue']
            . 'action=refresh_contents_last_updated' . $params['glue']
            . 'item_ID=' . $this->ID . $params['glue']
            . ($params['type'] != 'touch' ? 'type=' . $params['type'] . $params['glue'] : '')
            . url_crumb('collections_refresh_contents_last_updated');

        return $url;
    }

    /**
     * Get a link to refresh a contents last updated date of this Item if user has refresh rights
     *
     * @param array Params
     */
    public function get_refresh_contents_last_updated_link($params = [])
    {
        $params = array_merge([
            'before' => ' ',
            'after' => '',
            'text' => '#icon#',
            'title' => '#',
            'class' => '',
        ], $params);

        $refresh_url = $this->get_refresh_contents_last_updated_url($params);
        if (! $refresh_url) {	// If current user has no perm to refesh contents last updated date of this Item:
            return;
        }

        if ($params['title'] == '#') {	// Use default title
            $params['title'] = T_('Reset the "contents last updated" date to the latest content change on this thread');
        }

        $params['text'] = utf8_trim($params['text']);
        $params['title'] = utf8_trim($params['title']);
        $params['class'] = utf8_trim($params['class']);

        $r = $params['before'];

        $r .= '<a href="' . $refresh_url . '"'
                . (empty($params['title']) ? '' : ' title="' . format_to_output($params['title'], 'htmlattr') . '"')
                . (empty($params['class']) ? '' : ' class="' . $params['class'] . '"')
            . '>'
                . str_replace('#icon#', get_icon('refresh', 'imgtag', [
                    'title' => $params['title'],
                ]), $params['text'])
            . '</a>';

        $r .= $params['after'];

        return $r;
    }

    /**
     * Refresh contents last updated ts with date of the latest Comment
     *
     * @param boolean TRUE to display messages
     * @param string Field name(without prefix "comment_") of the latest comment which should be used to refresh the post date column: 'date', 'last_touched_ts'
     * @param string What post and comment date fields use to refresh:
                       'created' - 'post_datestart', 'comment_date'
     * @return boolean TRUE of success
     */
    public function refresh_contents_last_updated_ts($display_messages = false, $date_type = 'touched')
    {
        if (! $this->can_refresh_contents_last_updated()) {	// If current User has no permission to refresh a contents last updated date of the requested Item:
            return false;
        }

        global $DB, $Messages;

        // Clear latest Comment from previous calling before Comment updating:
        $this->latest_Comment = null;

        if ($date_type == 'created') {	// Use dates for 'created' mode:
            $post_date_field = 'datestart';
            $comment_date_field = 'date';
        } else {	// Use dates for 'touched' mode:
            $post_date_field = 'datemodified';
            $comment_date_field = 'last_touched_ts';
        }

        if ($latest_Comment = &$this->get_latest_Comment(get_inskin_statuses($this->get_blog_ID(), 'comment'), $comment_date_field)) {	// Use date from the latest public Comment:
            $new_contents_last_updated_ts = $latest_Comment->get($comment_date_field);
            if ($display_messages) {	// Display message:
                $Messages->add(sprintf(
                    (
                        $date_type == 'created'
                        ? T_('"Contents last updated" timestamp has been refreshed using <a %s>most recently added comment</a> date = %s.')
                        : T_('"Contents last updated" timestamp has been refreshed using <a %s>most recently touched comment</a> date = %s.')
                    ),
                    'href="' . $latest_Comment->get_permanent_url() . '"',
                    mysql2localedatetime($new_contents_last_updated_ts)
                ), 'success');
            }
        } else {	// Use date from issue date of this Item when it has no comments yet:
            $new_contents_last_updated_ts = $this->get($post_date_field);
            if ($display_messages) {	// Display message:
                $Messages->add(sprintf(
                    (
                        $date_type == 'created'
                        ? T_('"Contents last updated" timestamp has been refreshed using post issue date = %s.')
                        : T_('"Contents last updated" timestamp has been refreshed using post modified date = %s.')
                    ),
                    mysql2localedatetime($new_contents_last_updated_ts)
                ), 'success');
            }
        }

        $DB->query('UPDATE T_items__item
					SET post_contents_last_updated_ts = ' . $DB->quote($new_contents_last_updated_ts) . '
				WHERE post_ID = ' . $this->ID);

        return true;
    }

    /**
     * Get image file used for social media
     *
     * @param boolean Use category social media boiler plate or category image as fallback
     * @param boolean Use site social media boiler plate or site logo as fallback
     * @return object Image File or Link object
     */
    public function get_social_media_image($use_category_fallback = false, $use_site_fallback = false, $return_as_link = false)
    {
        if (! empty($this->social_media_image_File) && ! $return_as_link) {
            return $this->social_media_image_File;
        }

        $LinkOwner = new LinkItem($this);
        if ($LinkList = $LinkOwner->get_attachment_LinkList(1000, 'cover,background,teaser,teaserperm,teaserlink,inline', 'image', [
            'sql_select_add' => ', CASE WHEN link_position = "cover" THEN 1 WHEN link_position IN ( "teaser", "teaserperm", "teaserlink" ) THEN 2 ELSE 3 END AS link_priority',
            'sql_order_by' => 'link_priority ASC, link_order ASC',
        ])) { // Item has linked files
            while ($Link = &$LinkList->get_next()) {
                if (! ($File = &$Link->get_File())) { // No File object
                    global $Debuglog;
                    $Debuglog->add(sprintf('Link ID#%d of item #%d does not have a file object!', $Link->ID, $this->ID), ['error', 'files']);
                    continue;
                }

                if (! $File->exists()) { // File doesn't exist
                    global $Debuglog;
                    $Debuglog->add(sprintf('File linked to item #%d does not exist (%s)!', $this->ID, $File->get_full_path()), ['error', 'files']);
                    continue;
                }

                if ($File->is_image()) { // Use only image files for og:image tag
                    $this->social_media_image_File = $File;
                    if ($return_as_link) {
                        return $Link;
                    }
                    break;
                }
            }
        }

        if (empty($this->social_media_image_File) && $use_category_fallback) {
            $FileCache = &get_FileCache();
            if ($default_Chapter = &$this->get_main_Chapter()) { // Try social media boilerplate image
                $social_media_image_file_ID = $default_Chapter->get('social_media_image_file_ID', false);
                if ($social_media_image_file_ID > 0 && ($File = &$FileCache->get_by_ID($social_media_image_file_ID)) && $File->is_image()) {
                    $this->social_media_image_File = $File;
                } else { // Try category image
                    $cat_image_file_ID = $default_Chapter->get('image_file_ID', false);
                    if ($cat_image_file_ID > 0 && ($File = &$FileCache->get_by_ID($cat_image_file_ID)) && $File->is_image()) {
                        $this->social_media_image_File = $File;
                    }
                }
            }
        }

        if (empty($this->social_media_image_File) && $use_site_fallback) { // Use social media boilerplate logo if configured
            global $Settings;

            $FileCache = &get_FileCache();
            $social_media_image_file_ID = intval($Settings->get('social_media_image_file_ID'));
            if ($social_media_image_file_ID > 0 && ($File = $FileCache->get_by_ID($social_media_image_file_ID, false)) && $File->is_image()) {
                $this->social_media_image_File = $File;
            } else { // Use site logo as fallback if configured
                $notification_logo_file_ID = intval($Settings->get('notification_logo_file_ID'));
                if ($notification_logo_file_ID > 0 && ($File = $FileCache->get_by_ID($notification_logo_file_ID, false)) && $File->is_image()) {
                    $this->social_media_image_File = $File;
                }
            }
        }

        return $this->social_media_image_File;
    }

    /**
     * Add tags to current User
     */
    public function tag_user()
    {
        if (empty($this->ID)) {	// Item is not saved in DB
            return;
        }

        if (! is_logged_in()) {	// User is not logged in
            return;
        }

        $item_user_tags = trim($this->get_setting('user_tags'), ' ,');
        if (empty($item_user_tags)) {	// This Item has no tags for users:
            return;
        }

        global $current_User;

        // Add tags to current User:
        $current_User->add_usertags($item_user_tags);
        $current_User->dbupdate();
    }

    /**
     * Get ID of next version
     *
     * @param string Version type: 'archived', 'proposed'
     * @param integer
     */
    public function get_next_version_ID($type = 'archived')
    {
        global $DB;

        // Get next version ID:
        $iver_SQL = new SQL();
        $iver_SQL->SELECT('MAX( iver_ID )');
        $iver_SQL->FROM('T_items__version');
        $iver_SQL->WHERE('iver_itm_ID = ' . $this->ID);
        $iver_SQL->WHERE_and('iver_type = ' . $DB->quote($type));

        return intval($DB->get_var($iver_SQL->get())) + 1;
    }

    /**
     * Create a new item revision
     *
     * @return integer/boolean ID of created item revision if successful, otherwise False
     */
    public function create_revision()
    {
        global $DB;

        if (empty($this->ID)) {	// Don't try to create revision when Item is not created yet:
            return false;
        }

        // Get next version ID:
        $iver_ID = $this->get_next_version_ID('archived');

        $DB->begin('SERIALIZABLE');

        $result = $DB->query(
            'INSERT INTO T_items__version
				( iver_ID, iver_itm_ID, iver_edit_user_ID, iver_edit_last_touched_ts, iver_status, iver_title, iver_content )
				SELECT ' . $iver_ID . ' AS iver_ID, post_ID, post_lastedit_user_ID, post_last_touched_ts, post_status, post_title, post_content
				  FROM T_items__item
				  WHERE post_ID = ' . $this->ID,
            'Save a version of the Item'
        ) !== false;

        if ($result) {	// Create a revision for custom fields:
            $custom_fields = $this->get_type_custom_fields();
            if (count($custom_fields)) {	// If at least one custom field has been updated:
                $custom_field_values = [];
                foreach ($custom_fields as $custom_field_name => $custom_field) {
                    if (isset($this->dbchanges_custom_fields[$custom_field_name])) {
                        $custom_field_values[] = '(' . $iver_ID . ',' . $DB->quote($this->ID) . ',' . $custom_field['ID'] . ',' . $DB->quote($custom_field['label']) . ',' . $DB->quote($this->dbchanges_custom_fields[$custom_field_name]) . ')';
                    }
                }
                if (count($custom_field_values)) {
                    $result = $DB->query(
                        'INSERT INTO T_items__version_custom_field
							( ivcf_iver_ID, ivcf_iver_itm_ID, ivcf_itcf_ID, ivcf_itcf_label, ivcf_value )
							VALUES ' . implode(',', $custom_field_values),
                        'Save a version of the custom fields'
                    ) !== false;
                }
            }
        }

        if ($result) {	// Create a revision for links/attachments:
            $LinkOwner = new LinkItem($this);
            $existing_Links = &$LinkOwner->get_Links();

            if (! empty($existing_Links)) {
                $link_values = [];
                foreach ($existing_Links as $loop_Link) {
                    $link_values[] = '(' . $iver_ID . ',' . $this->ID . ',' . $loop_Link->ID . ',' . $loop_Link->file_ID . ',' . $DB->quote($loop_Link->position) . ',' . $loop_Link->order . ')';
                }
                $result = $DB->query(
                    'INSERT INTO T_items__version_link
						( ivl_iver_ID, ivl_iver_itm_ID, ivl_link_ID, ivl_file_ID, ivl_position, ivl_order )
						VALUES ' . implode(',', $link_values),
                    'Save a version of attachments'
                ) !== false;
            }
        }

        if ($result) {
            $DB->commit();
        } else {
            $DB->rollback();
        }

        return $result ? $iver_ID : false;
    }

    /**
     * Check if this Item has at least one proposed change
     *
     * @param boolean
     */
    public function has_proposed_change()
    {
        if (! isset($this->has_proposed_change)) {	// Check if this Item has a proposed change and save the result in cache var:
            if (empty($this->ID)) {	// Item must be created to have the proposed changes:
                return false;
            }

            global $DB;

            $SQL = new SQL('Check if Item #' . $this->ID . ' has at least one proposed change');
            $SQL->SELECT('iver_ID');
            $SQL->FROM('T_items__version');
            $SQL->WHERE('iver_itm_ID = ' . $this->ID);
            $SQL->WHERE_and('iver_type = "proposed"');

            $this->has_proposed_change = (bool) $DB->get_var($SQL->get(), 0, null, $SQL->title);
        }

        return $this->has_proposed_change;
    }

    /**
     * Check if current user can create new proposed change
     *
     * @param boolean TRUE to redirect back if current user cannot create a proposed change
     * @return boolean
     */
    public function can_propose_change($redirect = false)
    {
        global $current_User, $Messages;

        if ($redirect) {	// Set a redirect URL:
            $redirect_to = get_returnto_url();
            $inskin_edit_script = '/item_edit.php';
            if (strpos($redirect_to, $inskin_edit_script) == strlen($redirect_to) - strlen($inskin_edit_script) ||
                strpos($redirect_to, '?disp=proposechange') !== false) {	// Fix a redirect page to correct in-skin editing page
                if (! ($redirect_to = $this->get_edit_url([
                    'force_in_skin_editing' => true,
                ]))) {
                    $redirect_to = $this->get_permanent_url('', '', '');
                }
            }
        }

        if (! is_logged_in()) {	// User must be logged in:
            if ($redirect) {	// Redirect back to previous page
                header_redirect($redirect_to);
            }
            return false;
        }

        if (! check_user_perm('blog_item_propose', 'edit', false, $this->get_blog_ID())) {	// User has no right to propose a change for this Item:
            // Display a message:
            // NOTE: Do NOT translate this because it should not be displayed from normal UI:
            $Messages->add('You don\'t have a permission to propose a change for the Item.', 'error');

            if ($redirect) {	// Redirect back to previous page
                header_redirect($redirect_to);
            }
            return false;
        }

        if (($last_proposed_Revision = $this->get_revision('last_proposed')) &&
            $last_proposed_Revision->iver_edit_user_ID != $current_User->ID) {	// Don't allow to propose when previous proposition was created by another user:
            $UserCache = &get_UserCache();
            $User = &$UserCache->get_by_ID($last_proposed_Revision->iver_edit_user_ID, false, false);

            // Display a message:
            $Messages->add(sprintf(
                T_('You cannot currently propose a change because previous changes by %s are pending review.'),
                ($User ? $User->get_identity_link() : '<span class="user deleted">' . T_('Deleted user') . '</span>')
            ), 'error');

            if ($redirect) {	// Redirect back to previous page
                header_redirect($redirect_to);
            }
            return false;
        }

        return true;
    }

    /**
     * Create a new proposed change
     *
     * @return integer/boolean ID of created item revision if successful, otherwise False
     */
    public function create_proposed_change()
    {
        global $DB, $current_User, $Plugins_admin, $localtimenow;

        if (empty($this->ID) || ! is_logged_in()) {	// Item must be created and current user must be logged in:
            return false;
        }

        if ($this->get_type_setting('allow_html')) {	// HTML is allowed for this post, we'll accept HTML tags:
            $text_format = 'html';
        } else {	// HTML is disallowed for this post, we'll encode all special chars:
            $text_format = 'htmlspecialchars';
        }

        // Never allow html content on post titles:  (fp> probably so as to not mess up backoffice and all sorts of tools)
        param('post_title', 'htmlspecialchars', null);

        param('content', $text_format, null);

        // Do some optional filtering on the content
        // Typically stuff that will help the content to validate
        // Useful for code display.
        // Will probably be used for validation also.
        // + APPLY RENDERING from Rendering Plugins:
        $Plugins_admin = &get_Plugins_admin();
        $params = [
            'object_type' => 'Item',
            'object' => &$this,
            'object_Blog' => &$this->Blog,
        ];
        $Plugins_admin->filter_contents($GLOBALS['post_title'] /* by ref */, $GLOBALS['content'] /* by ref */, $this->get_renderers(), $params /* by ref */);

        // Title checking:
        if ($this->get_type_setting('use_title') == 'required') {
            param_check_not_empty('post_title', T_('Please provide a title.'), '');
        }

        // Format raw HTML input to cleaned up and validated HTML:
        param_check_html('content', T_('Invalid content.'));
        $this->set('content', prepare_item_content(get_param('content')));

        $this->set('title', get_param('post_title'));

        if (empty($iver_content) && $this->get_type_setting('use_text') == 'required') {	// Content must be entered:
            param_check_not_empty('content', T_('Please enter some text.'), '');
        }

        // Load values of custom fields:
        $this->load_custom_fields_from_Request();

        if (param_errors_detected()) {	// Exit here if some errors on the submitted form:
            return false;
        }
        // END of loading the proposed change this this Item from request.

        $DB->begin('SERIALIZABLE');

        // Get next version ID:
        $iver_ID = $this->get_next_version_ID('proposed');

        $result = $DB->query(
            'INSERT INTO T_items__version ( iver_ID, iver_type, iver_itm_ID, iver_edit_user_ID, iver_edit_last_touched_ts, iver_status, iver_title, iver_content )
			VALUES ( ' . $iver_ID . ', '
                . '"proposed", '
                . $this->ID . ', '
                . $current_User->ID . ', '
                . $DB->quote(date2mysql($localtimenow)) . ','
                . $DB->quote($this->get('status')) . ','
                . $DB->quote($this->get('title')) . ','
                . $DB->quote($this->get('content')) . ' )',
            'Save a proposed change of the Item #' . $this->ID
        ) !== false;

        if ($result && ($custom_fields = $this->get_type_custom_fields())) {	// Save custom fields of the proposition:
            $custom_fields_insert_sql = [];
            foreach ($custom_fields as $custom_field) {
                $custom_fields_insert_sql[] = '( ' . $iver_ID . ', '
                    . '"proposed", '
                    . $this->ID . ', '
                    . $DB->quote($custom_field['ID']) . ','
                    . $DB->quote($custom_field['label']) . ','
                    . $DB->quote($this->get_custom_field_value($custom_field['name'])) . ' )';
            }
            $result = $DB->query(
                'INSERT INTO T_items__version_custom_field ( ivcf_iver_ID, ivcf_iver_type, ivcf_iver_itm_ID, ivcf_itcf_ID, ivcf_itcf_label, ivcf_value )
				VALUES ' . implode(', ', $custom_fields_insert_sql),
                'Save custom fields for a proposed change of the Item #' . $this->ID
            ) !== false;
        }

        if ($result) {	// Save links/attachments of current version to new created proposed change:
            // TODO: This is a temporary solution. We must allow to edit links/attachements for a proposed change and store them here!
            $result = $DB->query(
                'INSERT INTO T_items__version_link
					( ivl_iver_ID, ivl_iver_type, ivl_iver_itm_ID, ivl_link_ID, ivl_file_ID, ivl_position, ivl_order )
					SELECT ' . $iver_ID . ', "proposed", link_itm_ID, link_ID, link_file_ID, link_position, link_order
					  FROM T_links
					 WHERE link_itm_ID = ' . $this->ID,
                'Save custom fields for a proposed change of the Item #' . $this->ID
            ) !== false;
        }

        if ($result) {
            $DB->commit();
            // Send email notification to moderators about new proposed change:
            $this->send_proposed_change_notification($iver_ID);
        } else {
            $DB->rollback();
        }

        return $result;
    }

    /**
     * Check if this item can be updated depending on proposed changes
     *
     * @param boolean|string FALSE to don't display message of restriction, Message type: 'error', 'warning', 'note', 'success'
     * @return boolean
     */
    public function check_proposed_change_restriction($restriction_message_type = false)
    {
        if (! isset($this->check_proposed_change_restriction)) {	// Check and save result in cache var:
            if (empty($this->ID)) {	// Item is not created yet, so it can be updated, i.e. insert new record without restriction:
                $this->check_proposed_change_restriction = true;
            } elseif ($last_proposed_Revision = $this->get_revision('last_proposed')) {	// Don't allow to edit this Item if it has at least one proposed change:
                if ($restriction_message_type !== false) {	// Display a message to inform user about this restriction:
                    global $Messages, $admin_url;

                    $UserCache = &get_UserCache();
                    $User = &$UserCache->get_by_ID($last_proposed_Revision->iver_edit_user_ID, false, false);

                    $Messages->add(sprintf(
                        T_('The content below includes <a %s>proposed changes</a> submitted by %s. If you edit the post, the changes will be considered accepted and you will save a new version with your own changes.'),
                        'href="' . $admin_url . '?ctrl=items&amp;action=history&amp;p=' . $this->ID . '"',
                        ($User ? $User->get_identity_link() : '<span class="user deleted">' . T_('Deleted user') . '</span>')
                    ), $restriction_message_type);
                }
                $this->check_proposed_change_restriction = false;
            } else {	// Item can be updated:
                $this->check_proposed_change_restriction = true;
            }
        }

        return $this->check_proposed_change_restriction;
    }

    /**
     * Update folder of Item's attachments
     *
     * @return boolean TRUE if at least one attachment was moved to current slug folder
     */
    public function update_attachments_folder()
    {
        global $DB, $Debuglog;

        if (empty($this->ID)) {	// Item must be saved in DB:
            return false;
        }

        $FileRootCache = &get_FileRootCache();
        if (! ($item_FileRoot = &$FileRootCache->get_by_type_and_ID('collection', $this->get_blog_ID(), true))) {	// Unknown file root:
            return false;
        }

        $LinkOwner = new LinkItem($this);

        if (! $LinkList = $LinkOwner->get_attachment_LinkList()) {	// Item has no attachments:
            return false;
        }

        // Folder with current/new slug:
        $item_new_slug_folder_name = 'quick-uploads/' . $this->get('urltitle') . '/';
        $item_new_slug_folder_path = $item_FileRoot->ads_path . $item_new_slug_folder_name;

        $result = false;
        $folders_of_moved_files = [];
        while ($Link = &$LinkList->get_next()) {
            if (! ($File = &$Link->get_File())) {	// No File object
                $Debuglog->add(sprintf('Link ID#%d of item #%d does not have a file object!', $Link->ID, $this->ID), ['error', 'files']);
                continue;
            }

            if (! $File->exists()) {	// File doesn't exist
                $Debuglog->add(sprintf('File linked to item #%d does not exist (%s)!', $this->ID, $File->get_full_path()), ['error', 'files']);
                continue;
            }

            if (strpos($File->get_rdfp_rel_path(), 'quick-uploads/') !== 0 ||
                (strpos($File->get_rdfp_rel_path(), $item_new_slug_folder_name) === 0 &&
                  ($file_FileRoot = &$File->get_FileRoot()) &&
                  $item_FileRoot->ID == $file_FileRoot->ID)) {	// Skip if File is not located in the folder "quick-uploads/"
                //      or File is already located in the current slug folder of this Item:
                continue;
            }

            $SQL = new SQL('Check File #' . $File->ID . ' for muplitle Links before moving to slug folder of Item #' . $this->ID);
            $SQL->SELECT('COUNT( link_ID )');
            $SQL->FROM('T_links');
            $SQL->WHERE('link_file_ID = ' . $DB->quote($File->ID));
            if ($DB->get_var($SQL) > 1) {	// Don't move if File is linked with several Items:
                continue;
            }

            if (! file_exists($item_new_slug_folder_path)) {	// Try to create a folder for new item slug:
                if (! mkdir_r($item_new_slug_folder_path)) {	// Stop trying to move other files when no file rights to create new folder on the disc:
                    $log_message = 'No file rights to create a folder %s before moving Item\'s files to slug folder!';
                    $Debuglog->add(sprintf($log_message, '"' . $item_new_slug_folder_path . '"'), ['error', 'files']);
                    syslog_insert(sprintf($log_message, '[[' . $item_new_slug_folder_path . ']]'), 'error', 'item', $this->ID);
                    return false;
                }
            }

            // Save file folder before moving:
            $old_file_dir = isset($File->_dir) ? $File->_dir : false;

            // Move File to the folder with name as current Item's slug:
            if ($File->move_to($item_FileRoot->type, $item_FileRoot->in_type_ID, $item_new_slug_folder_name . $File->get_name(), true)) {	// If File was moved successfully
                $result = true;
                if ($old_file_dir && ! in_array($old_file_dir, $folders_of_moved_files)) {	// Collect a folder in order to check and remove if it is empty after moving all files from the folder:
                    $folders_of_moved_files[] = $old_file_dir;
                }
            }
        }

        // Delete folders which are empty after moving all files:
        foreach ($folders_of_moved_files as $folder_of_moved_files) {
            if (file_exists($folder_of_moved_files) &&
                is_empty_directory($folder_of_moved_files) &&
                ! rmdir_r($folder_of_moved_files)) {	// Log error:
                $log_message = 'No file rights to delete an empty folder %s after moving Item\'s files to slug folder!';
                $Debuglog->add(sprintf($log_message, '"' . $folder_of_moved_files . '"'), ['error', 'files']);
                syslog_insert(sprintf($log_message, '[[' . $folder_of_moved_files . ']]'), 'warning', 'item', $this->ID);
            }
        }

        return $result;
    }

    /**
     * Display or log notification message
     *
     * @param string Message
     * @param boolean|string 'cron_job' - to log messages for cron job, FALSE - to don't log
     * @param string Message type
     * @param string Message group title
     */
    public function display_notification_message($message, $log_messages = false, $message_type = 'note', $message_group = null)
    {
        global $Messages;

        if ($log_messages == 'cron_job') {	// Log message for cron job:
            cron_log_append($message . "\n", $message_type);
        } elseif (! empty($this->ID) && // Item must be stored in DB
            // User must be logged in and activated and be a collection admin
            check_user_perm('blog_admin', 'edit', false, $this->get_blog_ID(), false)) {	// Display notification message only for collection admin:
            if ($message_group === null) {	// Set default group title:
                $message_group = T_('Sending notifications:');
            }
            $Messages->add_to_group($message, $message_type, $message_group);
        }
    }

    /**
     * Get available locales
     *
     * @param string Type of locales:
     *        - 'locale' - locales of the Item's collection,
     *        - 'coll' - locales as links to other collections,
     *        - 'all' - all locales of this and links with other collections.
     * @return array
     */
    public function get_available_locales($type = 'locale')
    {
        return ($item_Blog = &$this->get_Blog() ? $item_Blog->get_locales($type) : []);
    }

    /**
     * Get locale options for selector on edit page
     *
     * @param string Type of locales:
     *        - 'locale' - locales of the Item's collection,
     *        - 'coll' - locales as links to other collections,
     *        - 'all' - all locales of this and links with other collections.
     * @param boolean Exclude locales that are already used in the group of this Item
     * @return string
     */
    public function get_locale_options($type = 'locale', $exclude_used = false)
    {
        global $locales;

        $r = '';

        $available_locales = $this->get_available_locales($type);

        if ($exclude_used) {	// Exclude locales that are already used in the group of this Item:
            $other_version_items = $this->get_other_version_items();
            foreach ($other_version_items as $other_version_Item) {
                if (isset($available_locales[$other_version_Item->get('locale')])) {
                    unset($available_locales[$other_version_Item->get('locale')]);
                }
            }
        }

        if (empty($available_locales)) {	// No available locales:
            return $r;
        }

        $BlogCache = &get_BlogCache();

        foreach ($available_locales as $locale_key => $linked_coll_ID) {
            if ((isset($locales[$locale_key]) && $locales[$locale_key]['enabled']) ||
                $locale_key == $this->get('locale')) {	// Allow enabled locales or if it is already selected for this Item:
                if (! empty($linked_coll_ID)) {	// This is a linked locale from different collection:
                    $locale_Blog = &$BlogCache->get_by_ID($linked_coll_ID, false, false);
                } else {	// Use collection of this Item:
                    $locale_Blog = &$this->get_Blog();
                }
                if (! $locale_Blog) {	// Skip wrong locale:
                    continue;
                }
                $r .= '<option value="' . $locale_key . '"';
                if ($locale_key == $this->get('locale')) {	// This is a selected locale
                    $r .= ' selected="selected"';
                }
                $r .= ' data-coll-id="' . $locale_Blog->ID . '"';
                $r .= ' data-coll-name="' . format_to_output($locale_Blog->get('name'), 'htmlattr') . '"';
                $r .= '>' . (isset($locales[$locale_key]) ? T_($locales[$locale_key]['name']) : $locale_key) . '</option>' . "\n";
            }
        }

        return $r;
    }

    /**
     * Set group ID from another Item
     *
     * @param integer ID of parent/source Item
     */
    public function set_group_ID($parent_item_ID)
    {
        $ItemCache = &get_ItemCache();
        if (! ($parent_Item = &$ItemCache->get_by_ID($parent_item_ID, false, false))) {	// Wrong source Item ID:
            return;
        }

        if (! $parent_Item->get('igrp_ID')) {	// Create new Item Group if it wasn't created yet:
            global $DB;
            if ($DB->query('INSERT INTO T_items__itemgroup () VALUES ()')) {
                $parent_Item->set('igrp_ID', $DB->insert_id);
                $parent_Item->dbupdate();
            }
        }

        // Use the same Item Group as source Item has:
        $this->set('igrp_ID', $parent_Item->get('igrp_ID'));
    }

    /**
     * Get other version Items from the same group
     *
     * @param integer Include additional Item by ID, e.g. ID of source Item on adding new version
     * @return array
     */
    public function get_other_version_items($source_item_ID = null)
    {
        if (! isset($this->other_version_items)) {	// Try to load other version Items from DB:
            if ($source_item_ID == $this->ID) {	// Don't include the same Item:
                $source_item_ID = null;
            }
            $this->other_version_items = [];
            if (! $this->get('igrp_ID')) {	// No group for this Item yet:
                if (! empty($source_item_ID)) {	// Include source Item:
                    $ItemCache = &get_ItemCache();
                    $ItemCache->clear();
                    if ($Item = &$ItemCache->get_by_ID($source_item_ID, false, false)) {
                        $this->other_version_items[$Item->ID] = &$Item;
                    }
                }
            } else {	// Load from DB:
                global $DB;
                $SQL = new SQL();
                $SQL->SELECT('post_ID');
                $SQL->FROM('T_items__item');
                $SQL->WHERE('(post_igrp_ID = ' . $this->get('igrp_ID') . (empty($source_item_ID) ? '' : ' OR post_ID = ' . $source_item_ID) . ')');
                if ($this->ID > 0) {	// Exclude this Item:
                    $SQL->WHERE_and('post_ID != ' . $this->ID);
                }
                $group_item_IDs = $DB->get_col($SQL);
                if (count($group_item_IDs)) {
                    $ItemCache = &get_ItemCache();
                    $ItemCache->clear();
                    $ItemCache->load_where('post_ID IN ( ' . $DB->quote($group_item_IDs) . ' ) ');
                    foreach ($group_item_IDs as $group_item_ID) {
                        $this->other_version_items[$group_item_ID] = &$ItemCache->get_by_ID($group_item_ID);
                    }
                }
            }
        }

        return $this->other_version_items;
    }

    /**
     * Get version Item by locale
     *
     * @param string Locale
     * @param boolean TRUE to check if the Item can be displayed for current User
     * @return object|null Item object
     */
    public function &get_version_Item($locale, $check_visibility = true)
    {
        $version_items = $this->get_other_version_items();
        array_unshift($version_items, $this);

        foreach ($version_items as $version_Item) {
            if ($version_Item->get('locale') == $locale &&
                (! $check_visibility || $version_Item->can_be_displayed())) {	// Use first detected Item with requested locale and visible for current User:
                return $version_Item;
            }
        }

        $r = null;
        return $r;
    }

    /**
     * Check permission of current User to edit workflow properties
     *
     * @param string Permission name:
     *               - 'any' - Check to edit at least one workflow property
     *               - 'status', 'user', 'priority', 'deadline' - Check to edit one of these workflow properties
     * @param boolean Execution will halt if this is !0 and permission is denied
     */
    public function can_edit_workflow($permname = 'any', $assert = false)
    {
        $perm =
            // Main Category must be defined for this Item in order to check permission in Collection of the Category:
            ! empty($this->main_cat_ID) &&
            // Workflow must be enabled for current Collection:
            $this->get_coll_setting('use_workflow') &&
            // Current User must has a permission to be assigned for tasks of the current Collection:
            check_user_perm('blog_can_be_assignee', 'edit', $assert, $this->get_blog_ID());

        if ($perm) {	// Additional checking for several permissions when main checking is true:
            switch ($permname) {
                case 'any':
                    // Check if current User can edit at least one workflow property:
                    $perm = check_user_perm('blog_workflow_status', 'edit', false, $this->get_blog_ID()) ||
                        check_user_perm('blog_workflow_user', 'edit', false, $this->get_blog_ID()) ||
                        check_user_perm('blog_workflow_priority', 'edit', false, $this->get_blog_ID());
                    break;
                case 'deadline':
                    // Deadline has additional collection setting to be enabled:
                    $perm = $this->get_coll_setting('use_deadline');
            }
        }

        if (! $perm) {	// No permission:
            if ($assert) {	// We can't let this go on!
                global $app_name;
                debug_die(sprintf( /* %s is the application name, usually "b2evolution" */ T_('Group/user permission denied by %s!'), $app_name) . ' (Item#' . $this->ID . ':workflow:' . ($permname === null ? 'MAIN' : $permname) . ')');
            }
            return $perm;
        }

        switch ($permname) {
            case 'any':
                // Check if current User can edit at least one workflow property:
                return $perm;
            case 'status':
                // Check if current User can edit the workflow status:
                return check_user_perm('blog_workflow_status', 'edit', $assert, $this->get_blog_ID());
            case 'user':
                // Check if current User can edit the workflow user:
                return check_user_perm('blog_workflow_user', 'edit', $assert, $this->get_blog_ID());
            case 'priority':
            case 'deadline':
                // Check if current User can edit the workflow priority or deadline:
                return check_user_perm('blog_workflow_priority', 'edit', $assert, $this->get_blog_ID());
            default:
                // Wrong request:
                debug_die('Unhandled Item workflow permission name "' . $permname . '"');
        }
    }

    /**
     * Display workflow field to edit
     *
     * @param string Field key: 'status', 'user', 'priority', 'deadline'
     * @param object Form
     * @param array Additional parameters
     */
    public function display_workflow_field($field, &$Form, $params = [])
    {
        if (! $this->can_edit_workflow($field)) {	// Current User has no permission to edit the requested workflow property:
            return;
        }

        switch ($field) {
            case 'status':
                $ItemStatusCache = &get_ItemStatusCache();
                $ItemStatusCache->load_all();
                $ItemTypeCache = &get_ItemTypeCache();
                $current_ItemType = &$this->get_ItemType();
                $Form->select_input_options('item_st_ID', $ItemStatusCache->get_option_list($this->get('pst_ID'), true, 'get_name', $current_ItemType->get_ignored_post_status()), T_('Task status'), '', $params);
                break;

            case 'user':
                // Load current blog members into cache:
                $UserCache = &get_UserCache();
                // Load only first 21 users to know when we should display an input box instead of full users list
                $UserCache->load_blogmembers($this->get_blog_ID(), 21, false);

                if (count($UserCache->cache) > 20) {
                    $params = array_merge([
                        'size' => 10,
                    ], $params);
                    $assigned_User = &$UserCache->get_by_ID($this->get('assigned_user_ID'), false, false);
                    $Form->username('item_assigned_user_login', $assigned_User, T_('Assigned to'), '', 'only_assignees', $params);
                } else {
                    $params = array_merge([
                        'note' => '',
                        'allow_none' => true,
                        'class' => '',
                        'object_callback' => 'get_assigned_user_options',
                    ], $params);
                    $Form->select_input_object('item_assigned_user_ID', null, $this, T_('Assigned to'), $params);
                }
                break;

            case 'priority':
                $params = array_merge([
                    'force_keys_as_values' => true,
                ], $params);
                $Form->select_input_array('item_priority', $this->get('priority'), item_priority_titles(), T_('Priority'), '', $params);
                break;

            case 'deadline':
                if ($this->get_coll_setting('use_deadline')) {	// Display deadline fields only if it is enabled for collection:
                    $is_inline = isset($Form->is_lined_fields) && $Form->is_lined_fields;
                    if (! $is_inline) {
                        $Form->begin_line(T_('Deadline'), 'item_deadline', '', $params);
                    }
                    $date_params = array_merge([
                        'input_suffix' => '&nbsp;' . T_('at') . '&nbsp;',
                        'placeholder' => locale_input_datefmt(),
                    ], $params);
                    $datedeadline = $this->get('datedeadline');
                    $Form->date_input('item_deadline', $datedeadline, '', $date_params);

                    $datedeadline_time = empty($datedeadline) ? '' : date('Y-m-d H:i', strtotime($datedeadline));
                    $time_params = array_merge([
                        'time_format' => 'hh:mm',
                        'placeholder' => 'hh:mm',
                        'note' => '',
                    ], $params);
                    $Form->time_input('item_deadline_time', $datedeadline_time, T_('at'), $time_params);
                    if (! $is_inline) {
                        $Form->end_line();
                    }
                }
                break;
        }
    }

    /**
     * Get ordered array of fields which can be edited on front-office
     *
     * @param array
     */
    public function get_front_edit_fields()
    {
        $fields = [];

        // Item fields which may be displayed on front-office:
        $item_fields = [
            'title',
            'short_title',
            'instruction',
            'attachments',
            'workflow',
            'text',
            'tags',
            'excerpt',
            'url',
            'location',
        ];
        foreach ($item_fields as $item_field) {
            $fields[] = [
                'name' => $item_field,
                'order' => $this->get_type_setting('front_order_' . $item_field),
                'type' => 'item',
            ];
        }

        // Custom fields:
        $custom_fields = $this->get_custom_fields_defs();
        foreach ($custom_fields as $custom_field) {
            $fields[] = [
                'name' => $custom_field['name'],
                'public' => $custom_field['public'],
                'order' => $custom_field['order'],
                'type' => 'custom',
                'value' => $custom_field['value'],
            ];
        }

        // Sort fields by order value:
        usort($fields, [$this, 'sort_front_edit_fields_callback']);

        return $fields;
    }

    /**
     * Callback function to sort front edit fields by order value
     *
     * @param array Field data
     * @param array Field data
     * @return boolean
     */
    public function sort_front_edit_fields_callback($a, $b)
    {
        if ($a['order'] == $b['order']) {	// Sort by field type or name:
            if ($a['type'] == $b['type']) {	// Sort by name when type is same:
                return $a['name'] > $b['name'] ? 1 : -1;
            }
            // Make item fields above custom fields:
            return ($a['type'] == 'custom' ? 1 : -1);
        }

        return ($a['order'] > $b['order'] ? 1 : -1);
    }

    /**
     * Do 302 redirect from tiny URL to canonical URL of this Item
     *
     * @param string Slug
     * @param string Slug extra term
     */
    public function tinyurl_redirect($slug = null, $slug_extra_term = null)
    {
        // Get item's canonical URL for redirect from tiny URL:
        $redirect_to = $this->get_permanent_url('', '', '&');

        if (is_pro()) {	// Load extra code for extra processing:
            load_funcs('_core/_pro_features.funcs.php');

            // Get Collection of this Item:
            $item_Blog = &$this->get_Blog();

            // Add params for PRO version:
            $redirect_to = pro_tinyurl_redirect_add_params($redirect_to, $item_Blog, $slug, $slug_extra_term);
        }

        // Keep ONLY allowed params from current URL in the canonical URL by configs AND Item's switchable params:
        $redirect_to = url_keep_canonicals_params($redirect_to, '&', array_keys($this->get_switchable_params()));

        header_redirect($redirect_to, 302);  // 302 is easier for debugging; TODO: setting to choose type of redirect
    }

    /**
     * Get info for form field selector
     *
     * @return string
     */
    public function get_form_selector_info()
    {
        $r = '';

        $status_icons = get_visibility_statuses('icons');
        if (isset($status_icons[$this->get('status')])) {	// Status colored icon:
            $r .= $status_icons[$this->get('status')];
        }
        // Title with link to permament url:
        $r .= ' ' . $this->get_title([
            'link_type' => 'permalink',
        ]);
        // Icon to edit if current User has a permission:
        $r .= ' ' . $this->get_edit_link([
            'text' => '#icon#',
        ]);

        return $r;
    }
    // Jose/Metztli IT 01-20-2024
    public function __toString()
    {
        return "_item.class.php: This is a string representation of the object";
    }
}
