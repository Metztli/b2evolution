<?php
/**
 * This is the init file for the core module.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
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

/**
 * This is supposed to be overriden by sth more useful when a more useful module is loaded
 * Typically should be 'dashboard'
 */
$default_ctrl = 'settings';

/**
 * Minimum PHP version required for _core module to function properly.
 * This value can't be higher then the application required php version.
 */
$required_php_version['_core'] = '5.6';

/**
 * Minimum MYSQL version required for _core module to function properly.
 */
$required_mysql_version['_core'] = '5.1';

/**
 * Aliases for table names:
 *
 * (You should not need to change them.
 *  If you want to have multiple b2evo installations in a single database you should
 *  change {@link $tableprefix} in _basic_config.php)
 */
$db_config['aliases'] = [
    'T_antispam__keyword' => $tableprefix . 'antispam__keyword',
    'T_antispam__iprange' => $tableprefix . 'antispam__iprange',
    'T_cron__log' => $tableprefix . 'cron__log',
    'T_cron__task' => $tableprefix . 'cron__task',
    'T_regional__country' => $tableprefix . 'regional__country',
    'T_regional__region' => $tableprefix . 'regional__region',
    'T_regional__subregion' => $tableprefix . 'regional__subregion',
    'T_regional__city' => $tableprefix . 'regional__city',
    'T_regional__currency' => $tableprefix . 'regional__currency',
    'T_groups' => $tableprefix . 'groups',
    'T_groups__groupsettings' => $tableprefix . 'groups__groupsettings',
    'T_global__cache' => $tableprefix . 'global__cache',
    'T_i18n_original_string' => $tableprefix . 'i18n_original_string',
    'T_i18n_translated_string' => $tableprefix . 'i18n_translated_string',
    'T_locales' => $tableprefix . 'locales',
    'T_plugins' => $tableprefix . 'plugins',
    'T_pluginevents' => $tableprefix . 'pluginevents',
    'T_pluginsettings' => $tableprefix . 'pluginsettings',
    'T_pluginusersettings' => $tableprefix . 'pluginusersettings',
    'T_plugingroupsettings' => $tableprefix . 'plugingroupsettings',
    'T_settings' => $tableprefix . 'settings',
    'T_social__network' => $tableprefix . 'social__network',
    'T_users' => $tableprefix . 'users',
    'T_users__fielddefs' => $tableprefix . 'users__fielddefs',
    'T_users__fieldgroups' => $tableprefix . 'users__fieldgroups',
    'T_users__fields' => $tableprefix . 'users__fields',
    'T_users__invitation_code' => $tableprefix . 'users__invitation_code',
    'T_users__reports' => $tableprefix . 'users__reports',
    'T_users__usersettings' => $tableprefix . 'users__usersettings',
    'T_users__organization' => $tableprefix . 'users__organization',
    'T_users__user_org' => $tableprefix . 'users__user_org',
    'T_users__secondary_user_groups' => $tableprefix . 'users__secondary_user_groups',
    'T_users__profile_visits' => $tableprefix . 'users__profile_visits',
    'T_users__profile_visit_counters' => $tableprefix . 'users__profile_visit_counters',
    'T_users__tag' => $tableprefix . 'users__tag',
    'T_users__usertag' => $tableprefix . 'users__usertag',
    'T_users__social_network' => $tableprefix . 'users__social_network',
    'T_slug' => $tableprefix . 'slug',
    'T_email__log' => $tableprefix . 'email__log',
    'T_email__returns' => $tableprefix . 'email__returns',
    'T_email__address' => $tableprefix . 'email__address',
    'T_email__newsletter' => $tableprefix . 'email__newsletter',
    'T_email__newsletter_subscription' => $tableprefix . 'email__newsletter_subscription',
    'T_email__campaign' => $tableprefix . 'email__campaign',
    'T_email__campaign_send' => $tableprefix . 'email__campaign_send',
    'T_automation__automation' => $tableprefix . 'automation__automation',
    'T_automation__newsletter' => $tableprefix . 'automation__newsletter',
    'T_automation__step' => $tableprefix . 'automation__step',
    'T_automation__user_state' => $tableprefix . 'automation__user_state',
    'T_syslog' => $tableprefix . 'syslog',
];


/**
 * Controller mappings.
 *
 * For each controller name, we associate a controller file to be found in /inc/ .
 * The advantage of this indirection is that it is easy to reorganize the controllers into
 * subdirectories by modules. It is also easy to deactivate some controllers if you don't
 * want to provide this functionality on a given installation.
 *
 * Note: while the controller mappings might more or less follow the menu structure, we do not merge
 * the two tables since we could, at any time, decide to make a skin with a different menu structure.
 * The controllers however would most likely remain the same.
 *
 * @global array
 */
$ctrl_mappings = [
    'antispam' => 'antispam/antispam.ctrl.php',
    'crontab' => 'cron/cronjobs.ctrl.php',
    'regional' => 'regional/regional_dispatch.ctrl.php',
    'time' => 'regional/time.ctrl.php',
    'countries' => 'regional/countries.ctrl.php',
    'regions' => 'regional/regions.ctrl.php',
    'subregions' => 'regional/subregions.ctrl.php',
    'cities' => 'regional/cities.ctrl.php',
    'currencies' => 'regional/currencies.ctrl.php',
    'locales' => 'locales/locales.ctrl.php',
    'translation' => 'locales/translation.ctrl.php',
    'plugins' => 'plugins/plugins.ctrl.php',
    'remotepublish' => 'settings/remotepublish.ctrl.php',
    'stats' => 'sessions/stats.ctrl.php',
    'system' => 'tools/system.ctrl.php',
    'user' => 'users/user.ctrl.php',
    'users' => 'users/users.ctrl.php',
    'userfields' => 'users/userfields.ctrl.php',
    'userfieldsgroups' => 'users/userfieldsgroups.ctrl.php',
    'usersettings' => 'users/settings.ctrl.php',
    'usertags' => 'users/usertags.ctrl.php',
    'registration' => 'users/registration.ctrl.php',
    'invitations' => 'users/invitations.ctrl.php',
    'display' => 'users/display.ctrl.php',
    'groups' => 'users/groups.ctrl.php',
    'organizations' => 'users/organizations.ctrl.php',
    'accountclose' => 'users/account_close.ctrl.php',
    'slugs' => 'slugs/slugs.ctrl.php',
    'email' => 'tools/email.ctrl.php',
    'newsletters' => 'email_campaigns/newsletters.ctrl.php',
    'campaigns' => 'email_campaigns/campaigns.ctrl.php',
    'automations' => 'automations/automations.ctrl.php',
    'syslog' => 'tools/syslog.ctrl.php',
    'customize' => 'customize/customize.ctrl.php',
    'pro_only' => 'pro_only/pro_only.ctrl.php',
];


/**
 * Get the CountryCache
 *
 * @param string The text that gets used for the "None" option in the objects options list (Default: T_('Unknown')).
 * @return CountryCache
 */
function &get_CountryCache($allow_none_text = null)
{
    global $CountryCache;

    if (! isset($CountryCache)) {	// Cache doesn't exist yet:
        load_class('regional/model/_country.class.php', 'Country');
        if (! isset($allow_none_text)) {
            $allow_none_text = NT_('Unknown');
        }
        $CountryCache = new DataObjectCache('Country', true, 'T_regional__country', 'ctry_', 'ctry_ID', 'ctry_code', 'ctry_name', $allow_none_text);
    }

    return $CountryCache;
}


/**
 * Get the RegionCache
 *
 * @return RegionCache
 */
function &get_RegionCache()
{
    global $RegionCache;

    if (! isset($RegionCache)) {	// Cache doesn't exist yet:
        load_class('regional/model/_region.class.php', 'Region');
        $RegionCache = new DataObjectCache('Region', false, 'T_regional__region', 'rgn_', 'rgn_ID', 'rgn_name', 'rgn_name', NT_('Unknown'));
    }

    return $RegionCache;
}


/**
 * Get the SubregionCache
 *
 * @return SubregionCache
 */
function &get_SubregionCache()
{
    global $SubregionCache;

    if (! isset($SubregionCache)) {	// Cache doesn't exist yet:
        load_class('regional/model/_subregion.class.php', 'Subregion');
        $SubregionCache = new DataObjectCache('Subregion', false, 'T_regional__subregion', 'subrg_', 'subrg_ID', 'subrg_name', 'subrg_name', NT_('Unknown'));
    }

    return $SubregionCache;
}


/**
 * Get the CityCache
 *
 * @return CityCache
 */
function &get_CityCache()
{
    global $CityCache;

    if (! isset($CityCache)) {	// Cache doesn't exist yet:
        load_class('regional/model/_city.class.php', 'City');
        $CityCache = new DataObjectCache('City', false, 'T_regional__city', 'city_', 'city_ID', 'city_name', 'city_name', NT_('Unknown'));
    }

    return $CityCache;
}


/**
 * Get the CurrencyCache
 *
 * @return CurrencyCache
 */
function &get_CurrencyCache()
{
    global $CurrencyCache;

    if (! isset($CurrencyCache)) {	// Cache doesn't exist yet:
        load_class('regional/model/_currency.class.php', 'Currency');
        $CurrencyCache = new DataObjectCache('Currency', true, 'T_regional__currency', 'curr_', 'curr_ID', 'curr_code', 'curr_code');
    }

    return $CurrencyCache;
}


/**
 * Get the GroupCache
 *
 * @param boolean TRUE to ignore cached object and create new cache object
 * @param string The text that gets used for the "None" option in the objects options list (Default: NT_('No group')).
 * @return GroupCache
 */
function &get_GroupCache($force_cache = false, $allow_none_text = null)
{
    global $Plugins;
    global $GroupCache;

    if ($force_cache || ! isset($GroupCache)) { // Cache doesn't exist yet:
        if (is_null($allow_none_text)) { // Set default value for "None" option
            $allow_none_text = NT_('No group');
        }
        $Plugins->get_object_from_cacheplugin_or_create('GroupCache', 'new DataObjectCache( \'Group\', true, \'T_groups\', \'grp_\', \'grp_ID\', \'grp_name\', \'grp_level DESC, grp_name ASC\', \'' . str_replace("'", "\'", $allow_none_text) . '\' )');
    }

    return $GroupCache;
}


/**
 * Get the Plugins_admin
 *
 * @return Plugins_admin
 */
function &get_Plugins_admin()
{
    global $Plugins_admin;

    if (! isset($Plugins_admin)) {	// Cache doesn't exist yet:
        load_class('plugins/model/_plugins_admin.class.php', 'Plugins_admin');
        $Plugins_admin = new Plugins_admin(); // COPY (FUNC)
    }

    return $Plugins_admin;
}


/**
 * Get the Plugins
 *
 * @return object Plugins
 */
function &get_Plugins()
{
    global $Plugins;

    if (! is_object($Plugins)) {	// Cache doesn't exist yet:
        load_class('plugins/model/_plugins.class.php', 'Plugins');
        $Plugins = new Plugins();
    }

    return $Plugins;
}


/**
 * Get the UserCache
 *
 * @return UserCache
 */
function &get_UserCache()
{
    global $UserCache;

    if (! isset($UserCache)) {	// Cache doesn't exist yet:
        load_class('users/model/_usercache.class.php', 'UserCache');
        $UserCache = new UserCache(); // COPY (FUNC)
    }

    return $UserCache;
}


/**
 * Get the UserFieldCache
 *
 * @return UserFieldCache
 */
function &get_UserFieldCache()
{
    global $UserFieldCache;

    if (! isset($UserFieldCache)) {	// Cache doesn't exist yet:
        load_class('users/model/_userfield.class.php', 'Userfield');
        $UserFieldCache = new DataObjectCache('Userfield', false, 'T_users__fielddefs', 'ufdf_', 'ufdf_ID', 'ufdf_name', 'ufdf_name'); // COPY (FUNC)
    }

    return $UserFieldCache;
}


/**
 * Get the UserFieldGroupCache
 *
 * @return UserFieldGroupCache
 */
function &get_UserFieldGroupCache()
{
    global $UserFieldGroupCache;

    if (! isset($UserFieldGroupCache)) {	// Cache doesn't exist yet:
        $UserFieldGroupCache = new DataObjectCache('UserfieldGroup', false, 'T_users__fieldgroups', 'ufgp_', 'ufgp_ID', 'ufgp_name', 'ufgp_name'); // COPY (FUNC)
    }

    return $UserFieldGroupCache;
}


/**
 * Get the UserTagCache
 *
 * @return UserTagCache
 */
function &get_UserTagCache()
{
    global $UserTagCache;

    if (! isset($UserTagCache)) { // Cache doesn't exist yet
        load_class('users/model/_usertag.class.php', 'UserTag');
        $UserTagCache = new DataObjectCache('UserTag', false, 'T_users__tag', 'utag_', 'utag_ID', 'utag_name', 'utag_name'); // COPY (FUNC)
    }

    return $UserTagCache;
}


/**
 * Get the InvitationCache
 *
 * @return InvitationCache
 */
function &get_InvitationCache()
{
    global $InvitationCache;

    if (! isset($InvitationCache)) { // Cache doesn't exist yet:
        load_class('users/model/_invitation.class.php', 'Invitation');
        $InvitationCache = new DataObjectCache('Invitation', false, 'T_users__invitation_code', 'ivc_', 'ivc_ID', 'ivc_code', 'ivc_code'); // COPY (FUNC)
    }

    return $InvitationCache;
}


/**
 * Get the OrganizationCache
 *
 * @param string The text that gets used for the "None" option in the objects options list (Default: T_('Unknown')).
 * @return OrganizationCache
 */
function &get_OrganizationCache($allow_none_text = null)
{
    global $OrganizationCache;

    if (! isset($OrganizationCache)) { // Cache doesn't exist yet:
        load_class('users/model/_organization.class.php', 'Organization');
        $OrganizationCache = new DataObjectCache('Organization', false, 'T_users__organization', 'org_', 'org_ID', 'org_name', 'org_name', $allow_none_text); // COPY (FUNC)
    }

    return $OrganizationCache;
}


/**
 * Get the SlugCache
 *
 * @return SlugCache
 */
function &get_SlugCache()
{
    global $SlugCache;

    if (! isset($SlugCache)) {	// Cache doesn't exist yet:
        $SlugCache = new DataObjectCache('Slug', false, 'T_slug', 'slug_', 'slug_ID', 'slug_title', 'slug_title');
    }

    return $SlugCache;
}


/**
 * Get the IPRangeCache
 *
 * @return IPRangeCache
 */
function &get_IPRangeCache()
{
    global $IPRangeCache;

    if (! isset($IPRangeCache)) {	// Cache doesn't exist yet:
        load_class('antispam/model/_iprangecache.class.php', 'IPRangeCache');
        $IPRangeCache = new IPRangeCache();
    }

    return $IPRangeCache;
}


/**
 * Get the DomainCache
 *
 * @return DomainCache
 */
function &get_DomainCache()
{
    global $DomainCache;

    if (! isset($DomainCache)) { // Cache doesn't exist yet:
        load_class('sessions/model/_domain.class.php', 'Domain');
        $DomainCache = new DataObjectCache('Domain', false, 'T_basedomains', 'dom_', 'dom_ID', 'dom_name');
    }

    return $DomainCache;
}


/**
 * Get the EmailAddressCache
 *
 * @return EmailAddressCache
 */
function &get_EmailAddressCache()
{
    global $EmailAddressCache;

    if (! isset($EmailAddressCache)) {	// Cache doesn't exist yet:
        load_class('tools/model/_emailaddresscache.class.php', 'EmailAddressCache');
        $EmailAddressCache = new EmailAddressCache();
    }

    return $EmailAddressCache;
}


/**
 * Get the EmailLogCache
 *
 * @return EmailLogCache
 */
function &get_EmailLogCache()
{
    global $EmailLogCache;

    if (! isset($EmailLogCache)) { // Cache doesn't exist yet:
        load_class('tools/model/_emaillog.class.php', 'EmailLog');
        $EmailLogCache = new DataObjectCache('EmailLog', false, 'T_email__log', 'emlog_', 'emlog_ID');
    }

    return $EmailLogCache;
}


/**
 * Get the NewsletterCache
 *
 * @param string The text that gets used for the "None" option in the objects options list (Default: T_('Unknown')).
 * @return NewsletterCache
 */
function &get_NewsletterCache($allow_none_text = null)
{
    global $NewsletterCache;

    if (! isset($NewsletterCache)) {	// Cache doesn't exist yet:
        load_class('email_campaigns/model/_newsletter.class.php', 'Newsletter');
        $NewsletterCache = new DataObjectCache('Newsletter', false, 'T_email__newsletter', 'enlt_', 'enlt_ID', 'enlt_name', 'enlt_order', $allow_none_text);
    }

    return $NewsletterCache;
}


/**
 * Get the EmailCampaignCache
 *
 * @return EmailCampaignCache
 */
function &get_EmailCampaignCache()
{
    global $EmailCampaignCache;

    if (! isset($EmailCampaignCache)) { // Cache doesn't exist yet:
        load_class('email_campaigns/model/_emailcampaign.class.php', 'EmailCampaign');
        $EmailCampaignCache = new DataObjectCache('EmailCampaign', false, 'T_email__campaign', 'ecmp_', 'ecmp_ID');
    }

    return $EmailCampaignCache;
}


/**
 * Get the EmailCampaignPrerenderingCache
 *
 * @return EmailCampaignPrerenderingCache
 */
function &get_EmailCampaignPrerenderingCache()
{
    global $EmailCampaignPrerenderingCache;

    if (! isset($EmailCampaignPrerenderingCache)) {	// Cache doesn't exist yet:
        $EmailCampaignPrerenderingCache = [];
    }

    return $EmailCampaignPrerenderingCache;
}


/**
 * Get the AutomationCache
 *
 * @return AutomationCache
 */
function &get_AutomationCache()
{
    global $AutomationCache;

    if (! isset($AutomationCache)) {	// Cache doesn't exist yet:
        load_class('automations/model/_automation.class.php', 'Automation');
        $AutomationCache = new DataObjectCache('Automation', false, 'T_automation__automation', 'autm_', 'autm_ID');
    }

    return $AutomationCache;
}


/**
 * Get the AutomationStepCache
 *
 * @return AutomationStepCache
 */
function &get_AutomationStepCache()
{
    global $AutomationStepCache;

    if (! isset($AutomationStepCache)) {	// Cache doesn't exist yet:
        load_class('automations/model/_automationstep.class.php', 'AutomationStep');
        $AutomationStepCache = new DataObjectCache('AutomationStep', false, 'T_automation__step', 'step_', 'step_ID', null, 'step_order');
    }

    return $AutomationStepCache;
}


/**
 * Get the CronjobCache
 *
 * @return CronjobCache
 */
function &get_CronjobCache()
{
    global $CronjobCache;

    if (! isset($CronjobCache)) {	// Cache doesn't exist yet:
        load_class('cron/model/_cronjob.class.php', 'Cronjob');
        $CronjobCache = new DataObjectCache('Cronjob', false, 'T_cron__task', 'ctsk_', 'ctsk_ID', 'ctsk_name', 'ctsk_name', NT_('Unknown'));
    }

    return $CronjobCache;
}


/**
 * _core_Module definition
 */
class _core_Module extends Module
{
    /**
     * Do the initializations. Called from in _main.inc.php.
     * This is typically where classes matching DB tables for this module are registered/loaded.
     *
     * Note: this should only load/register things that are going to be needed application wide,
     * for example: for constructing menus.
     * Anything that is needed only in a specific controller should be loaded only there.
     * Anything that is needed only in a specific view should be loaded only there.
     */
    public function init()
    {
        $this->check_required_php_version('_core');

        load_class('_core/model/dataobjects/_dataobjectcache.class.php', 'DataObjectCache');
        load_funcs('users/model/_user.funcs.php');
        load_funcs('_core/_template.funcs.php');
        load_funcs('_core/ui/forms/_form.funcs.php');
        load_class('_core/ui/forms/_form.class.php', 'Form');
        load_class('_core/model/db/_sql.class.php', 'SQL');
        load_class('_core/ui/results/_results.class.php', 'Results');
        load_class('_core/model/_blockcache.class.php', 'BlockCache');
        load_class('slugs/model/_slug.class.php', 'Slug');
        load_class('antispam/model/_iprange.class.php', 'IPRange');
    }

    /**
     * Get default module permissions
     *
     * @param integer Group ID
     * @return array
     */
    public function get_default_group_permissions($grp_ID)
    {
        // Deny browse/contact users from other countries in case of suspect and spammer users.
        $cross_country_settings_default = ($grp_ID == 5 || $grp_ID == 6) ? 'denied' : 'allowed';
        switch ($grp_ID) {
            case 1:		// Administrators (group ID 1) have permission by default:
                $permadmin = 'normal'; // Access to Admin area
                $permusers = 'edit'; // Users & Groups
                $permoptions = 'edit'; // Global settings
                $permspam = 'edit'; // Antispam settings
                $permslugs = 'edit'; // Slug manager
                $permtemplates = 'allowed'; // Skin settings
                $permemails = 'edit'; // Email management
                $def_notification = 'full'; // Default notification type: short/full
                $permorgs = 'edit';
                break;

            case 2:		// Moderators (group ID 2) have permission by default:
                $permadmin = 'normal';
                $permusers = 'moderate';
                $permoptions = 'view';
                $permspam = 'edit';
                $permslugs = 'none';
                $permtemplates = 'denied';
                $permemails = 'view';
                $def_notification = 'short';
                $permorgs = 'create';
                break;

            case 3:		// Editors (group ID 3) have permission by default:
                $permadmin = 'restricted';
                $permusers = 'none';
                $permoptions = 'none';
                $permspam = 'view';
                $permslugs = 'none';
                $permtemplates = 'denied';
                $permemails = 'none';
                $def_notification = 'short';
                $permorgs = 'create';
                break;

            case 4: 	// Normal Users (group ID 4) have permission by default:
                $permadmin = 'no_toolbar';
                $permusers = 'none';
                $permoptions = 'none';
                $permspam = 'view';
                $permslugs = 'none';
                $permtemplates = 'denied';
                $permemails = 'none';
                $def_notification = 'short';
                $permorgs = 'none';
                break;

                // case 5:		// Misbehaving/Suspect users (group ID 5) have permission by default:
                // case 6:  // Spammers/restricted Users
            default:
                // Other groups have no permission by default
                $permadmin = 'no_toolbar';
                $permusers = 'none';
                $permoptions = 'none';
                $permspam = 'none';
                $permslugs = 'none';
                $permtemplates = 'denied';
                $permemails = 'none';
                $def_notification = 'short';
                $permorgs = 'none';
                break;
        }

        // We can return as many default permissions as we want:
        // e.g. array ( permission_name => permission_value, ... , ... )
        return $permissions = [
            'perm_admin' => $permadmin,
            'perm_users' => $permusers,
            'perm_options' => $permoptions,
            'perm_spamblacklist' => $permspam,
            'perm_slugs' => $permslugs,
            'perm_emails' => $permemails,
            'pm_notif' => $def_notification,
            'comment_subscription_notif' => $def_notification,
            'comment_moderation_notif' => $def_notification,
            'post_subscription_notif' => $def_notification,
            'post_moderation_notif' => $def_notification,
            'post_assignment_notif' => $def_notification,
            'cross_country_allow_profiles' => $cross_country_settings_default,
            'cross_country_allow_contact' => $cross_country_settings_default,
            'perm_orgs' => $permorgs,
        ];
    }

    /**
     * Get available group permissions
     *
     * @return array
     */
    public function get_available_group_permissions($grp_ID = null)
    {
        global $Settings;

        $none_option = ['none', T_('No Access'), ''];
        $view_option = ['view', T_('View only'), ''];
        $moderate_option = ['moderate', T_('Moderate'), ''];
        $full_option = ['edit', T_('Full Access'), ''];
        $view_details = ['view', T_('View details')];
        $edit_option = ['edit', T_('Edit/delete all')];
        // 'label' is used in the group form as label for radio buttons group
        // 'user_func' function used to check user permission. This function should be defined in Module.
        // 'group_func' function used to check group permission. This function should be defined in Module.
        // 'perm_block' group form block where this permissions will be displayed. Now available, the following blocks: additional, system
        // 'options' is permission options
        // 'perm_type' is used in the group form to decide to show radiobox or checkbox
        // 'field_lines' is used in the group form to decide to show radio options in multiple lines or not
        if ($grp_ID == 1) {
            $perm_admin_values = [
                'label' => T_('Access to Admin area'),
                'perm_block' => 'core_evobar',
                'perm_type' => 'info',
                'info' => T_('Visible link'),
            ];
            $perm_users_values = [
                'label' => T_('Users & Groups'),
                'perm_block' => 'core',
                'perm_type' => 'info',
                'info' => T_('Full Access') . get_admin_badge('user', '#', '#', T_('This group has User Admin permission.')),
            ];
        } else {
            $perm_admin_values = [
                'label' => T_('Evobar & Back-office'),
                'user_func' => 'check_admin_user_perm',
                'group_func' => 'check_admin_group_perm',
                'perm_block' => 'core_evobar',
                'options' => [
                    ['no_toolbar', T_('No Toolbar')],
                    ['none', T_('No Back-office Access')],
                    ['restricted', T_('Restricted Back-office Access')],
                    ['normal', T_('Normal Back-office Access')]],
                'perm_type' => 'radiobox',
                'field_lines' => true,
            ];
            $user_edit_option = $edit_option;
            $user_edit_option[1] .= get_admin_badge('user', '#', '#', T_('Select to give User Admin permission'));
            $perm_users_values = [
                'label' => T_('Users & Groups'),
                'user_func' => 'check_core_user_perm',
                'group_func' => 'check_core_group_perm',
                'perm_block' => 'core',
                'options' => [$none_option, $view_details, $moderate_option, $user_edit_option],
                'perm_type' => 'radiobox',
                'field_lines' => false,
            ];
        }

        $notification_options = [
            ['short', T_('Short')],
            ['full', T_('Full text')]];
        $notifications_array = [
            'group_func' => 'check_notification',
            'perm_block' => 'notifications',
            'options' => $notification_options,
            'perm_type' => 'radiobox',
            'field_note' => T_('Selecting "Full text" may generate email containing unwanted spam.'),
            'field_lines' => false,
        ];

        // Set additional note for cross country users restriction, if anonymous users can see the users list or users profiles
        $cross_country_note = '';
        if ($Settings->get('allow_anonymous_user_list') || $Settings->get('allow_anonymous_user_profiles')) {
            $cross_country_note = ' <span class="warning">' . T_('Browsing / Viewing users is currently allowed for anonymous users') . '</span>';
        }

        $permissions = [
            'perm_admin' => $perm_admin_values,
            'perm_users' => $perm_users_values,
            'perm_options' => [
                'label' => T_('Settings'),
                'user_func' => 'check_core_user_perm',
                'group_func' => 'check_core_group_perm',
                'perm_block' => 'core',
                'options' => [$none_option, $view_details, $edit_option],
                'perm_type' => 'radiobox',
                'field_lines' => false,
            ],
            'perm_spamblacklist' => [
                'label' => T_('Antispam'),
                'user_func' => 'check_core_user_perm',
                'group_func' => 'check_core_group_perm',
                'perm_block' => 'core2',
                'options' => [$none_option, $view_option, $full_option],
                'perm_type' => 'radiobox',
                'field_lines' => false,
            ],
            'perm_slugs' => [
                'label' => T_('Slug manager'),
                'user_func' => 'check_core_user_perm',
                'group_func' => 'check_core_group_perm',
                'perm_block' => 'core2',
                'options' => [$none_option, $view_option, $full_option],
                'perm_type' => 'radiobox',
                'field_lines' => false,
            ],
            'perm_emails' => [
                'label' => T_('Email management'),
                'user_func' => 'check_core_user_perm',
                'group_func' => 'check_core_group_perm',
                'perm_block' => 'core2',
                'options' => [$none_option, $view_details, $edit_option],
                'perm_type' => 'radiobox',
                'field_lines' => false,
            ],
            'pm_notif' => array_merge(
                [
                    'label' => T_('New Private Message notifications'),
                ],
                $notifications_array
            ),
            'comment_subscription_notif' => array_merge(
                [
                    'label' => T_('New Comment subscription notifications'),
                ],
                $notifications_array
            ),
            'comment_moderation_notif' => array_merge(
                [
                    'label' => T_('Comment moderation notifications'),
                ],
                $notifications_array
            ),
            'post_subscription_notif' => array_merge(
                [
                    'label' => T_('New Post subscription notifications'),
                ],
                $notifications_array
            ),
            'post_moderation_notif' => array_merge(
                [
                    'label' => T_('Post moderation notifications'),
                ],
                $notifications_array
            ),
            'post_assignment_notif' => array_merge(
                [
                    'label' => T_('Post assignment notifications'),
                ],
                $notifications_array
            ),
            'cross_country_allow_profiles' => [
                'label' => T_('Users'),
                'user_func' => 'check_cross_country_user_perm',
                'group_func' => 'check_cross_country_group_perm',
                'perm_block' => 'additional',
                'perm_type' => 'checkbox',
                'note' => T_('Allow to browse users from other countries') . $cross_country_note,
            ],
            'cross_country_allow_contact' => [
                'label' => T_('Messages'),
                'user_func' => 'check_cross_country_user_perm',
                'group_func' => 'check_cross_country_group_perm',
                'perm_block' => 'additional',
                'perm_type' => 'checkbox',
                'note' => T_('Allow to contact users from other countries'),
            ],
            'perm_orgs' => [
                'label' => T_('Organizations'),
                'user_func' => 'check_orgs_user_perm',
                'group_func' => 'check_orgs_group_perm',
                'perm_block' => 'additional',
                'options' => [
                    // format: array( radio_button_value, radio_button_label, radio_button_note )
                    ['none', T_('No Access')],
                    ['create', T_('Create & Edit owned organizations only')],
                    ['view', T_('Create & Edit owned organizations + View all')],
                    ['edit', T_('Full Access')],
                ],
                'perm_type' => 'radiobox',
                'field_lines' => true,
            ],
        ];
        return $permissions;
    }

    /**
     * Check admin permission for the group
     */
    public function check_admin_group_perm($permlevel, $permvalue, $permtarget)
    {
        $perm = false;
        switch ($permvalue) {
            case 'full':
            case 'normal':
                if ($permlevel == 'normal') {
                    $perm = true;
                    break;
                }

                // no break
            case 'restricted':
                if ($permlevel == 'restricted' || $permlevel == 'any') {
                    $perm = true;
                    break;
                }

                // no break
            case 'none':
                // display toolbar check
                if ($permlevel == 'toolbar') { // Even in case of No Access the toolbar must be displayed
                    $perm = true;
                    break;
                }
        }

        return $perm;
    }

    /**
     * Check a permission for the user. ( see 'user_func' in get_available_group_permissions() function  )
     *
     * @param string Requested permission level
     * @param string Permission value, this is the value on the database
     * @param mixed Permission target (blog ID, array of cat IDs...)
     * @return boolean True on success (permission is granted), false if permission is not granted
     */
    public function check_core_user_perm($permlevel, $permvalue, $permtarget)
    {
        return true;
    }

    /**
     * Check a permission for the group. ( see 'group_func' in get_available_group_permissions() function )
     *
     * @param string Requested permission level
     * @param string Permission value
     * @param mixed Permission target (blog ID, array of cat IDs...)
     * @return boolean True on success (permission is granted), false if permission is not granted
     */
    public function check_core_group_perm($permlevel, $permvalue, $permtarget)
    {
        $perm = false;

        switch ($permvalue) {
            case 'edit':
                // Users has edit perms
                if ($permlevel == 'edit') {
                    $perm = true;
                    break;
                }

                // no break
            case 'moderate':
                // Users has moderate perms
                if ($permlevel == 'moderate') {
                    $perm = true;
                    break;
                }

                // no break
            case 'view':
                // Users has view perms
                if ($permlevel == 'view') {
                    $perm = true;
                    break;
                }
        }

        return $perm;
    }

    /**
     * Check an user permission for the organization. ( see 'user_func' in get_available_group_permissions() function  )
     *
     * @param string Requested permission level
     * @param string Permission value, this is the value on the database
     * @param mixed Permission target (blog ID, array of cat IDs...)
     * @return boolean True on success (permission is granted), false if permission is not granted
     */
    public function check_orgs_user_perm($permlevel, $permvalue, $permtarget)
    {
        return true;
    }

    /**
     * Check a group permission for the organization. ( see 'group_func' in get_available_group_permissions() function )
     *
     * @param string Requested permission level
     * @param string Permission value
     * @param mixed Permission target (blog ID, array of cat IDs...)
     * @return boolean True on success (permission is granted), false if permission is not granted
     */
    public function check_orgs_group_perm($permlevel, $permvalue, $permtarget)
    {
        $perm = false;

        switch ($permvalue) {
            case 'edit':
                // Users has edit perms
                if ($permlevel == 'edit') {
                    $perm = true;
                    break;
                }

                // no break
            case 'view':
                // Users has view perms
                if ($permlevel == 'view') {
                    $perm = true;
                    break;
                }

                // no break
            case 'create':
                // Users has a create permisson:
                if ($permlevel == 'create') {
                    $perm = true;
                    break;
                }
        }

        if (! $perm && is_logged_in() && ! empty($permtarget)
            && ($permlevel == 'edit' || $permlevel == 'view')) {	// If this perm level is still not allowed, check if current user is owner of the requested Poll:
            global $current_User;
            if ($current_User->ID == $permtarget->owner_user_ID) {	// Current user is owner
                $perm = true;
            }
        }

        return $perm;
    }

    /**
     * Check permission for the group
     */
    public function check_template_group_perm($permlevel, $permvalue, $permtarget)
    {
        // Only 'allowed' value means group has permission
        return $permvalue == 'allowed';
    }

    /**
     * Check notification setting
     */
    public function check_notification($permlevel, $permvalue, $permtarget)
    {
        // Check if user should receive full text notification or not. In every other case short notificaiton must be sent.
        return $permvalue == 'full';
    }

    /**
     * Check permission for the group
     */
    public function check_cross_country_group_perm($permlevel, $permvalue, $permtarget)
    {
        // Check if browse/contact users from other countries is allowed
        return $permvalue == 'allowed';
    }

    /**
     * Build the evobar menu
     */
    public function build_evobar_menu()
    {
        /**
         * @var Menu
         */
        global $topleft_Menu, $topright_Menu;
        global $current_User;
        global $baseurl, $home_url, $admin_url, $debug, $debug_jslog, $dev_menu, $seo_page_type, $robots_index;
        global $Collection, $Blog, $blog, $activate_collection_toolbar;

        global $Settings;

        $perm_admin_normal = check_user_perm('admin', 'normal');
        $perm_admin_restricted = check_user_perm('admin', 'restricted');
        $perm_users_view = check_user_perm('users', 'view');
        $perm_options = check_user_perm('options', 'view');
        $perm_spam = check_user_perm('spamblacklist', 'view');
        $perm_emails = check_user_perm('emails', 'view');
        $perm_maintenance = check_user_perm('maintenance', 'upgrade');
        $entries = null;

        $working_blog = get_working_blog();
        if ($working_blog) { // Set collection url only when current user has an access to the working blog
            $BlogCache = &get_BlogCache();
            $working_Blog = &$BlogCache->get_by_ID($working_blog);
            if (is_admin_page()) { // Front page of the working blog
                $collection_url = $working_Blog->get('url');
            } else { // Dashboard of the working blog
                $collection_url = $admin_url . '?ctrl=coll_settings&amp;tab=dashboard&amp;blog=' . $working_blog;
            }

            $default_new_ItemType = $working_Blog->get_default_new_ItemType();
        }

        if ($perm_admin_normal || $perm_admin_restricted) { // Normal OR Restricted Access to Admin:
            $entries = [];

            // ---- START OF "Site" MENU ----:
            $entries['site'] = [
                'text' => T_('Site'),
                'href' => is_admin_page() ? $baseurl : $admin_url,
                'title' => is_admin_page() ? T_('Go to the site home page (Front-office)') : T_('Go to the site dashboard (Back-office)'),
                'entries' => [
                    'front' => [
                        'text' => T_('Site Front Page'),
                        'href' => $baseurl,
                        'title' => T_('Go to the site home page (Front-office)'),
                    ],
                    'dashboard' => [
                        'text' => T_('Site Dashboard'),
                        'href' => $admin_url,
                        'title' => T_('Go to the site dashboard (Back-office)'),
                    ],
                ],
            ];

            if ($perm_admin_normal && check_user_perm('options', 'view')) {	// If current User has an access to backoffice and can view settings:
                $entries['site']['entries'][] = [
                    'separator' => true,
                ];
                $entries['site']['entries']['settings'] = [
                    'text' => T_('Site Settings') . '&hellip;',
                    'href' => $admin_url . '?ctrl=collections&amp;tab=site_settings',
                ];
                if ($Settings->get('site_skins_enabled')) {	// Display menu item of site skin only when it is enabled:
                    $entries['site']['entries']['skin'] = [
                        'text' => T_('Site skin') . '&hellip;',
                        'href' => $admin_url . '?ctrl=collections&amp;tab=site_skin',
                    ];
                }
            }

            // More site options:
            if ($perm_users_view) {
                $entries['site']['entries'][] = [
                    'separator' => true,
                ];
                $entries['site']['entries']['users'] = [
                    'text' => T_('Users') . '&hellip;',
                    'href' => $admin_url . '?ctrl=users',
                ];
            }

            // PLACE HOLDER FOR MESSAGING MODULE:
            $entries['site']['entries']['messaging'] = null;

            // PLACE HOLDER FOR FILES MODULE:
            $entries['site']['entries']['files'] = null;

            if ($perm_admin_normal && $perm_options) {
                $entries['site']['entries'][] = [
                    'separator' => true,
                ];

                if ($perm_emails) {	// Emails:
                    $entries['site']['entries']['email'] = [
                        'text' => T_('Emails'),
                        'href' => $admin_url . '?ctrl=newsletters',
                        'entries' => [
                            'newsletters' => [
                                'text' => T_('Lists') . '&hellip;',
                                'href' => $admin_url . '?ctrl=newsletters',
                            ],
                            'campaigns' => [
                                'text' => T_('Campaigns') . '&hellip;',
                                'href' => $admin_url . '?ctrl=campaigns',
                            ],
                        ],
                    ];

                    if ($perm_options) {	// If current user has a permissions to view options:
                        $entries['site']['entries']['email']['entries'] += [
                            'automations' => [
                                'text' => T_('Automations') . '&hellip;',
                                'href' => $admin_url . '?ctrl=automations',
                            ],
                        ];
                    }

                    $entries['site']['entries']['email']['entries'] += [
                        'sent' => [
                            'text' => T_('Sent') . '&hellip;',
                            'href' => $admin_url . '?ctrl=email&amp;tab=sent',
                        ],
                        'return' => [
                            'text' => T_('Returned') . '&hellip;',
                            'href' => $admin_url . '?ctrl=email&amp;tab=return',
                        ],
                        'blocked' => [
                            'text' => T_('Addresses') . '&hellip;',
                            'href' => $admin_url . '?ctrl=email',
                        ],
                    ];
                }

                // System:
                $entries['site']['entries']['system'] = [
                    'text' => T_('System'),
                    'href' => $admin_url . '?ctrl=system',
                    'entries' => [
                        'status' => [
                            'text' => T_('Status') . '&hellip;',
                            'href' => $admin_url . '?ctrl=system',
                        ],
                        'crontab' => [
                            'text' => T_('Scheduler') . '&hellip;',
                            'href' => $admin_url . '?ctrl=crontab',
                        ],
                    ],
                ];

                if ($perm_spam) {
                    $entries['site']['entries']['system']['entries']['antispam'] = [
                        'text' => T_('Antispam') . '&hellip;',
                        'href' => $admin_url . '?ctrl=antispam',
                    ];
                }

                $entries['site']['entries']['system']['entries']['regional'] = [
                    'text' => T_('Regional') . '&hellip;',
                    'href' => $admin_url . '?ctrl=regional',
                ];
                $entries['site']['entries']['system']['entries']['skins'] = [
                    'text' => T_('Skins') . '&hellip;',
                    'href' => $admin_url . '?ctrl=skins&amp;tab=system',
                ];
                $entries['site']['entries']['system']['entries']['plugins'] = [
                    'text' => T_('Plugins') . '&hellip;',
                    'href' => $admin_url . '?ctrl=plugins',
                ];
                $entries['site']['entries']['system']['entries']['remote'] = [
                    'text' => T_('Remote publishing') . '&hellip;',
                    'href' => $admin_url . '?ctrl=remotepublish',
                ];
                $entries['site']['entries']['system']['entries']['maintenance'] = [
                    'text' => T_('Maintenance') . '&hellip;',
                    'href' => $admin_url . '?ctrl=tools',
                ];
                $entries['site']['entries']['system']['entries']['auto_upgrade'] = [
                    'text' => T_('Auto Upgrade') . '&hellip;',
                    'href' => $admin_url . '?ctrl=upgrade',
                ];
                $entries['site']['entries']['system']['entries']['syslog'] = [
                    'text' => T_('System log'),
                    'href' => $admin_url . '?ctrl=syslog',
                ];
            }

            // PLACE HOLDER FOR SESSIONS MODULE:
            $entries['site']['entries']['stats_separator'] = null;
            $entries['site']['entries']['stats'] = null;

            // b2evolution info links:
            $entries['site']['entries'][] = [
                'separator' => true,
            ];
            $entries['site']['entries']['b2evo'] = [
                'text' => 'b2evolution',
                'href' => $home_url,
                'entries' => [
                    'b2evonet' => [
                        'text' => T_('Open b2evolution.net'),
                        'href' => 'http://b2evolution.net/',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ],
                    'forums' => [
                        'text' => T_('Open Support forums'),
                        'href' => 'http://forums.b2evolution.net/',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ],
                    'manual' => [
                        'text' => T_('Open Online manual'),
                        'href' => get_manual_url(null),
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ],
                    'sep' => [
                        'separator' => true,
                    ],
                    'twitter' => [
                        'text' => T_('b2evolution on twitter'),
                        'href' => 'http://twitter.com/b2evolution',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ],
                    'facebook' => [
                        'text' => T_('b2evolution on facebook'),
                        'href' => 'http://www.facebook.com/b2evolution',
                        'target' => '_blank',
                        'rel' => 'noopener',
                    ],
                ],
            ];
            // ---- END OF "Site" MENU ----

            // ---- "Collection" MENU ----
            if ($working_blog) {	// Display a link to manage first available collection:
                $entries['blog'] = [
                    'text' => T_('Collection'),
                    'href' => $collection_url,
                ];
            }

            if (! is_admin_page()) {	// ---- "Page" MENU ----
                $entries['page'] = [
                    'text' => T_('Page'),
                    'entries' => [
                        // PLACE HOLDER FOR ENTRIES "Edit in Front-Office", "Edit in Back-Office", "View in Back-Office":
                        'edit_front' => null,
                        'edit_back' => null,
                        'edit_widgets' => null,
                        'propose' => null,
                        'view_back' => null,
                        'view_history' => null,
                        // PLACE HOLDERS FOR SESSIONS MODULE:
                        'stats_sep' => null,
                        'stats_page' => null,
                    ],
                ];
            }
        }

        if (! empty($default_new_ItemType)) {	// ---- "+ Post" MENU ----
            $default_item_denomination = /* TRANS: noun */ T_('Post');
            $entries['post'] = [
                'text' => get_icon('new') . ' ' . $default_new_ItemType->get_item_denomination('evobar_new', /* TRANS: noun */ T_('Post')),
                //'title' => T_('No blog is currently selected'),
                'disabled' => true,
                'entry_class' => 'rwdhide evobar-entry-new-post',
            ];
        }

        if ((! is_admin_page() || ! empty($activate_collection_toolbar)) && ! empty($Blog)) { // A collection is currently selected AND we can activate toolbar items for selected collection:
            if (check_user_perm('blog_post_statuses', 'edit', false, $Blog->ID) ||
                check_user_perm('blog_item_propose', 'edit', false, $Blog->ID)) { // We have permission to add a post with at least one status:
                global $disp, $ctrl, $action, $Item, $edited_Item;
                if (($disp == 'edit' || $disp == 'proposechange' || $ctrl == 'items') &&
                    isset($edited_Item) &&
                    $edited_Item->ID > 0 &&
                    $view_item_url = $edited_Item->get_permanent_url()) {	// If current user has a permission to edit the post currently viewed:
                    $entries['permalink'] = [
                        'text' => get_icon('permalink') . ' ' . T_('Permalink'),
                        'href' => $view_item_url,
                        'title' => T_('Permanent link to full entry'),
                        'entry_class' => 'rwdhide',
                    ];
                }
                if (! is_admin_page() &&
                    in_array($disp, ['single', 'page', 'edit', 'proposechange', 'widget_page']) &&
                    $perm_admin_restricted) {	// If current user has a permission to edit a current editing/viewing/proposing post:
                    if ($disp != 'edit' &&
                        $Blog->get_setting('in_skin_editing') &&
                        ! empty($Item) &&
                        $edit_item_url = $Item->get_edit_url()) {	// Display menu entry to edit the post in front-office:
                        $entries['page']['entries']['edit_front'] = [
                            'text' => sprintf(T_('Edit "%s" in Front-Office'), $Item->get_type_setting('name')) . '&hellip;',
                            'href' => $edit_item_url,
                            'shortcut' => 'f2',
                            'shortcut-top' => 'f2',
                        ];
                    }
                    if (! empty($Item) || (! empty($edited_Item) && $edited_Item->ID > 0)) {	// Display menu entries to edit and view the post in back-office:
                        $menu_Item = empty($Item) ? $edited_Item : $Item;
                        if ($perm_admin_restricted && check_user_perm('item_post!CURSTATUS', 'edit', false, $menu_Item)) {	// Menu item to edit post in back-office:
                            $entries['page']['entries']['edit_back'] = [
                                'text' => sprintf(T_('Edit "%s" in Back-Office'), $menu_Item->get_type_setting('name')) . '&hellip;',
                                'href' => $admin_url . '?ctrl=items&amp;action=edit&amp;p=' . $menu_Item->ID . '&amp;blog=' . $Blog->ID,
                                'shortcut' => ($Blog->get_setting('in_skin_editing') || ($disp == 'widget_page')) ? 'ctrl+f2' : 'f2,ctrl+f2',
                                'shortcut-top' => ($Blog->get_setting('in_skin_editing') || ($disp == 'widget_page')) ? 'ctrl+f2' : 'f2,ctrl+f2',
                            ];

                            if ($disp == 'widget_page') {
                                $entries['page']['entries']['edit_widgets'] = [
                                    'text' => T_('Edit widgets in Back-Office') . '&hellip;',
                                    'href' => $admin_url . '?ctrl=widgets&amp;blog=' . $Blog->ID,
                                    'shortcut' => 'f2',
                                    'shortcut-top' => 'f2',
                                ];
                            }
                        }
                        if ($perm_admin_restricted && check_user_perm('blog_post_statuses', 'edit', false, $Blog->ID)) {	// Menu item to view post in back-office:
                            $entries['page']['entries']['view_back'] = [
                                'text' => T_('View in Back-Office') . '&hellip;',
                                'href' => $admin_url . '?ctrl=items&amp;p=' . $menu_Item->ID . '&amp;blog=' . $Blog->ID,
                            ];
                        }
                        if ($perm_admin_restricted && ($item_history_url = $menu_Item->get_history_url())) {
                            $entries['page']['entries']['view_history'] = [
                                'text' => T_('View Change History') . '&hellip;',
                                'href' => $item_history_url,
                            ];
                        }
                        if ($disp != 'proposechange' && ($propose_change_item_url = $menu_Item->get_propose_change_url())) {	// If current User has a permission to propose a change for the Item:
                            $entries['page']['entries']['propose'] = [
                                'text' => T_('Propose change') . '&hellip;',
                                'href' => $propose_change_item_url,
                            ];
                        }
                    }

                    if (isset($entries['page'])) {	// Set a title when at least one menu item is allowed for current User:
                        $entries['page']['text'] = T_('Page');
                    }
                } elseif (! is_admin_page() &&
                    $perm_admin_restricted &&
                    (($disp == 'posts' && has_featured_Item('posts')) || ($disp == 'front' && has_featured_Item('front')))) {
                    // Get Featured/Intro Item:
                    $featured_intro_Item = &get_featured_Item($disp, null, true);

                    if ($Blog->get_setting('in_skin_editing') &&
                        $edit_item_url = $featured_intro_Item->get_edit_url()) {	// Display menu entry to edit the post in front-office:
                        $entries['page']['entries']['edit_front'] = [
                            'text' => sprintf(T_('Edit "%s" in Front-Office'), $featured_intro_Item->get_type_setting('name')) . '&hellip;',
                            'href' => $edit_item_url,
                            'shortcut' => 'f2',
                            'shortcut-top' => 'f2',
                        ];
                    }
                    if ($featured_intro_Item->ID > 0) {	// Display menu entries to edit and view the post in back-office:
                        if ($perm_admin_restricted && check_user_perm('item_post!CURSTATUS', 'edit', false, $featured_intro_Item)) {	// Menu item to edit post in back-office:
                            $entries['page']['entries']['edit_back'] = [
                                'text' => sprintf(T_('Edit "%s" in Back-Office'), $featured_intro_Item->get_type_setting('name')) . '&hellip;',
                                'href' => $admin_url . '?ctrl=items&amp;action=edit&amp;p=' . $featured_intro_Item->ID . '&amp;blog=' . $Blog->ID,
                                'shortcut' => $Blog->get_setting('in_skin_editing') ? 'ctrl+f2' : 'f2,ctrl+f2',
                                'shortcut-top' => $Blog->get_setting('in_skin_editing') ? 'ctrl+f2' : 'f2,ctrl+f2',
                            ];
                        }
                        if ($perm_admin_restricted && check_user_perm('blog_post_statuses', 'edit', false, $Blog->ID)) {	// Menu item to view post in back-office:
                            $entries['page']['entries']['view_back'] = [
                                'text' => T_('View in Back-Office') . '&hellip;',
                                'href' => $admin_url . '?ctrl=items&amp;p=' . $featured_intro_Item->ID . '&amp;blog=' . $Blog->ID,
                            ];
                        }
                        if ($perm_admin_restricted && ($item_history_url = $featured_intro_Item->get_history_url())) {
                            $entries['page']['entries']['view_history'] = [
                                'text' => T_('View Change History') . '&hellip;',
                                'href' => $item_history_url,
                            ];
                        }
                        if ($disp != 'proposechange' && ($propose_change_item_url = $featured_intro_Item->get_propose_change_url())) {	// If current User has a permission to propose a change for the Item:
                            $entries['page']['entries']['propose'] = [
                                'text' => T_('Propose change') . '&hellip;',
                                'href' => $propose_change_item_url,
                            ];
                        }
                    }
                }
                if (isset($entries['post']) && $write_item_url = $Blog->get_write_item_url()) {	// Enable menu to create new item if current User has a permission in current collection:
                    if (! empty($default_new_ItemType)) {	// The get_write_url() function above does not allow specifying the item type ID we'll manually add it:
                        $write_item_url = url_add_param($write_item_url, 'item_typ_ID=' . $default_new_ItemType->ID);
                    }
                    $entries['post']['href'] = $write_item_url;
                    $entries['post']['disabled'] = false;
                    $entries['post']['title'] = T_('Write a new post into this blog');
                }
            }

            if ($perm_admin_restricted && $working_blog) {
                // BLOG MENU:
                $entries['blog'] = [
                    'text' => T_('Collection'),
                    'title' => T_('Manage this blog'),
                    'href' => $collection_url,
                ];

                $display_separator = false;
                if (check_user_perm('blog_ismember', 'view', false, $Blog->ID)) { // Check if current user has an access to post lists
                    $items_url = $admin_url . '?ctrl=items&amp;blog=' . $Blog->ID . '&amp;filter=restore';

                    // Collection front page
                    $entries['blog']['entries']['coll_front'] = [
                        'text' => /* TRANS: %s is collection short name */ sprintf(T_('%s Front Page'), $Blog->get('shortname')) . '&hellip;',
                        'href' => $Blog->get('url'),
                    ];

                    // Collection dashboard
                    $entries['blog']['entries']['coll_dashboard'] = [
                        'text' => /* TRANS: %s is collection short name */ sprintf(T_('%s Dashboard'), $Blog->get('shortname')) . '&hellip;',
                        'href' => $admin_url . '?ctrl=coll_settings&amp;tab=dashboard&amp;blog=' . $Blog->ID,
                    ];

                    $entries['blog']['entries'][] = [
                        'separator' => true,
                    ];

                    $contents_submenu = [];

                    if ($Blog->get_setting('use_workflow') && check_user_perm('blog_can_be_assignee', 'edit', false, $Blog->ID)) { // Workflow view
                        $contents_submenu['workflow'] = [
                            'text' => T_('Workflow view') . '&hellip;',
                            'href' => $items_url . '&amp;tab=tracker',
                        ];
                    }

                    if ($Blog->get('type') == 'manual') { // Manual view
                        global $cat, $Item;
                        if (! empty($Item) &&
                                $Item->ID > 0 &&
                                ($item_Chapter = &$Item->get_main_Chapter())) {	// Set category param from current selected item/post:
                            $manual_view_cat_param = '&amp;cat_ID=' . $item_Chapter->ID . '&amp;highlight_id=' . $Item->ID;
                        } elseif (! empty($cat) && is_number($cat)) {	// Set category param from current selected category:
                            $manual_view_cat_param = '&amp;cat_ID=' . $cat . '&amp;highlight_cat_id=' . $cat;
                        } else {	// No selected category and item/post:
                            $manual_view_cat_param = '';
                        }
                        $contents_submenu['manual'] = [
                            'text' => T_('Manual view') . '&hellip;',
                            'href' => $items_url . '&amp;tab=manual' . $manual_view_cat_param,
                        ];
                    }

                    $contents_submenu['full'] = [
                        'text' => T_('All') . '&hellip;',
                        'href' => $admin_url . '?ctrl=items&amp;tab=full&amp;filter=restore&amp;blog=' . $Blog->ID,
                    ];

                    $contents_submenu['summary'] = [
                        'text' => T_('Summary') . '&hellip;',
                        'href' => $admin_url . '?ctrl=items&amp;tab=summary&amp;filter=restore&amp;blog=' . $Blog->ID,
                    ];

                    $type_tabs = get_item_type_tabs();
                    foreach ($type_tabs as $type_tab => $type_tab_name) {
                        $type_tab_key = 'type_' . str_replace(' ', '_', utf8_strtolower($type_tab));
                        $contents_submenu[$type_tab_key] = [
                            'text' => T_($type_tab_name) . '&hellip;',
                            'href' => $admin_url . '?ctrl=items&amp;tab=type&amp;tab_type=' . urlencode($type_tab) . '&amp;filter=restore&amp;blog=' . $Blog->ID,
                        ];
                    }

                    $entries['blog']['entries']['posts'] = [
                        'text' => T_('Contents'),
                        'href' => $items_url,
                        'entries' => $contents_submenu,
                    ];
                    $display_separator = true;
                }

                $perm_comments = check_user_perm('blog_comments', 'view', false, $Blog->ID);
                if ($perm_comments || check_user_perm('meta_comment', 'view', false, $Blog->ID)) {	// Initialize comments menu tab if user can view normal or internal comments of the collection:
                    $entries['blog']['entries']['comments'] = [
                        'text' => T_('Comments') . '&hellip;',
                        'href' => $admin_url . '?ctrl=comments&amp;blog=' . $Blog->ID . '&amp;filter=restore'
                            // Set url to internal comments page if user has a perm to view only internal comments:
                            . ($perm_comments ? '' : '&amp;tab3=meta'),
                    ];
                    $display_separator = true;
                }

                // Chapters / Categories:
                if (check_user_perm('blog_cats', 'edit', false, $Blog->ID)) { // Either permission for a specific blog or the global permission:
                    $entries['blog']['entries']['chapters'] = [
                        'text' => T_('Categories') . '&hellip;',
                        'href' => $admin_url . '?ctrl=chapters&amp;blog=' . $Blog->ID,
                    ];
                    $display_separator = true;
                }

                if ($display_separator) {
                    $entries['blog']['entries'][] = [
                        'separator' => true,
                    ];
                }

                // PLACEHOLDER FOR FILES MODULE:
                $entries['blog']['entries']['files'] = null;

                // BLOG SETTINGS:
                if (check_user_perm('blog_properties', 'edit', false, $Blog->ID)) { // We have permission to edit blog properties:
                    $blog_param = '&amp;blog=' . $Blog->ID;

                    $entries['blog']['entries']['features'] = [
                        'text' => T_('Features'),
                        'href' => $admin_url . '?ctrl=coll_settings&amp;tab=home' . $blog_param,
                        'entries' => [
                            'front' => [
                                'text' => T_('Front page') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=home' . $blog_param,
                            ],
                            'posts' => [
                                'text' => T_('Posts') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=features' . $blog_param,
                            ],
                            'comments' => [
                                'text' => T_('Comments') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=comments' . $blog_param,
                            ],
                            'contact' => [
                                'text' => T_('Contact form') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=contact' . $blog_param,
                            ],
                            'userdir' => [
                                'text' => T_('User directory') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=userdir' . $blog_param,
                            ],
                            'search' => [
                                'text' => T_('Search') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=search' . $blog_param,
                            ],
                            'other' => [
                                'text' => T_('Other displays') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=other' . $blog_param,
                            ],
                            'popup' => [
                                'text' => T_('Popups') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=popup' . $blog_param,
                            ],
                            'metadata' => [
                                'text' => T_('Meta data') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=metadata' . $blog_param,
                            ],
                            'more' => [
                                'text' => T_('More') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=more' . $blog_param,
                            ],
                        ],
                    ];
                    $entries['blog']['entries']['skin'] = [
                        'text' => T_('Skin') . '&hellip;',
                        'href' => $admin_url . '?ctrl=coll_settings&amp;tab=skin' . $blog_param,
                    ];
                    $entries['blog']['entries']['widgets'] = [
                        'text' => T_('Widgets') . '&hellip;',
                        'href' => $admin_url . '?ctrl=widgets' . $blog_param,
                    ];

                    if (! is_admin_page()) { // Display an option to turn on/off containers display:
                        global $ReqURI, $Session;

                        if ($Session->get('display_containers_' . $Blog->ID) == 1) { // To hide the debug containers
                            $entries['blog']['entries']['containers'] = [
                                'text' => T_('Hide containers'),
                                'href' => url_add_param(regenerate_url('display_containers'), 'display_containers=hide'),
                            ];
                        } else { // To show the debug containers
                            $entries['blog']['entries']['containers'] = [
                                'text' => T_('Show containers'),
                                'href' => url_add_param(regenerate_url('display_containers'), 'display_containers=show'),
                            ];
                        }
                    }

                    $entries['blog']['entries']['general'] = [
                        'text' => T_('Settings'),
                        'href' => $admin_url . '?ctrl=coll_settings' . $blog_param,
                        'entries' => [
                            'general' => [
                                'text' => T_('General') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=general' . $blog_param,
                            ],
                            'urls' => [
                                'text' => T_('URLs') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=urls' . $blog_param,
                            ],
                            'seo' => [
                                'text' => T_('SEO') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=seo' . $blog_param,
                            ],
                            'plugins' => [
                                'text' => T_('Plugins') . '&hellip;',
                                'href' => $admin_url . '?ctrl=coll_settings&amp;tab=plugins' . $blog_param,
                            ],
                        ],
                    ];

                    if (check_user_perm('options', 'view', false, $Blog->ID)) { // Post Types & Statuses
                        $entries['blog']['entries']['general']['entries']['item_types'] = [
                            'text' => T_('Item Types') . '&hellip;',
                            'href' => $admin_url . '?ctrl=itemtypes&amp;tab=settings&amp;tab3=types' . $blog_param,
                        ];
                        $entries['blog']['entries']['general']['entries']['item_statuses'] = [
                            'text' => T_('Item Statuses') . '&hellip;',
                            'href' => $admin_url . '?ctrl=itemstatuses&amp;tab=settings&amp;tab3=statuses' . $blog_param,
                        ];
                    }

                    $entries['blog']['entries']['general']['entries']['advanced'] = [
                        'text' => T_('Advanced') . '&hellip;',
                        'href' => $admin_url . '?ctrl=coll_settings&amp;tab=advanced' . $blog_param,
                    ];

                    if ($Blog && $Blog->advanced_perms) {
                        $entries['blog']['entries']['general']['entries']['userperms'] = [
                            'text' => T_('User perms') . '&hellip;',
                            'href' => $admin_url . '?ctrl=coll_settings&amp;tab=perm' . $blog_param,
                        ];
                        $entries['blog']['entries']['general']['entries']['groupperms'] = [
                            'text' => T_('Group perms') . '&hellip;',
                            'href' => $admin_url . '?ctrl=coll_settings&amp;tab=permgroup' . $blog_param,
                        ];
                    }

                    if (check_user_perm('options', 'view')) { // Check if current user has a permission to view the common settings of the blogs
                        $entries['blog']['entries']['general']['entries']['common_settings'] = [
                            'text' => T_('Common Settings') . '&hellip;',
                            'href' => $admin_url . '?ctrl=collections&amp;tab=blog_settings',
                        ];
                    }
                }
            }
        }

        if (! is_admin_page() && ! empty($Blog)) {	// Only front-office collection pages:
            if ($perm_admin_restricted &&
                (($Settings->get('site_skins_enabled') && check_user_perm('options', 'edit')) ||
                  check_user_perm('blog_properties', 'edit', false, $Blog->ID))
            ) {	// If current user has an access to back-office and to edit site or collection properties:
                global $Session;
                $customizer_mode = $Session->get('customizer_mode_' . $Blog->ID);
                $entries['skin'] = [
                    'text' => '<span class="fa fa-sliders"></span> ' . T_('Customize'),
                    'href' => $Blog->get('customizer_url', ($customizer_mode ? [
                        'mode' => 'disable',
                    ] : [])),
                    'entry_class' => 'rwdhide',
                    'class' => 'evo_customizer__toggler' . ($customizer_mode ? ' active' : ''),
                    'shortcut' => 'f4',
                    'shortcut-top' => 'f4',
                ];
            }

            if ($perm_admin_restricted && check_user_perm('blog_properties', 'edit', false, $Blog->ID)) {	// If current user has an access to back-office and to edit collection properties:
                // Display menu item "Features" with depending on $disp:
                global $disp, $disp_detail;
                switch ($disp) {
                    case 'front':
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=home&amp;blog=' . $Blog->ID;
                        break;
                    case 'posts':
                    case 'single':
                    case 'page':
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=features&amp;blog=' . $Blog->ID;
                        break;
                    case 'comments':
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=comments&amp;blog=' . $Blog->ID;
                        break;
                    case 'msgform':
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=contact&amp;blog=' . $Blog->ID;
                        break;
                    case 'users':
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=userdir&amp;blog=' . $Blog->ID;
                        break;
                    default:
                        $coll_features_url = $admin_url . '?ctrl=coll_settings&amp;tab=other&amp;blog=' . $Blog->ID;
                        break;
                }
                $entries['features'] = [
                    'text' => '<span class="fa fa-cog"></span> ' . T_('Features'),
                    'href' => $coll_features_url,
                    'entry_class' => 'rwdhide',
                ];
            }
        }

        if ($perm_admin_restricted) {
            // DEV MENU:
            $dev_entries = [];
            if ($dev_menu || $debug || $debug_jslog) {
                if (isset($Blog)) {
                    $dev_entries['coll'] = [
                        'text' => 'Collection = ' . $Blog->shortname,
                        'disabled' => true,
                    ];
                }

                global $disp, $is_front;
                if (! empty($disp)) {
                    $dev_entries['disp'] = [
                        'text' => '$disp = ' . $disp,
                        'disabled' => true,
                    ];
                }

                global $disp_detail;
                if (! empty($disp_detail)) {
                    $dev_entries['disp_detail'] = [
                        'text' => '$disp_detail = ' . $disp_detail,
                        'disabled' => true,
                    ];
                }

                if (! empty($seo_page_type)) { // Set in skin_init()
                    $dev_entries['seo_page_type'] = [
                        'text' => '> ' . $seo_page_type,
                        'disabled' => true,
                    ];
                }

                global $is_front;
                if (! empty($is_front)) {
                    $dev_entries['front'] = [
                        'text' => 'This is the FRONT page',
                        'disabled' => true,
                    ];
                }

                global $http_response_code;
                if (! empty($http_response_code)) {
                    $dev_entries['http_response_code'] = [
                        'text' => 'HTTP Resp: ' . $http_response_code,
                        'disabled' => true,
                    ];
                }

                if ($robots_index === false) {
                    $debug_text = 'NO INDEX';
                } else {
                    $debug_text = 'do index';
                }

                $dev_entries['noindex'] = [
                    'text' => $debug_text,
                    'disabled' => true,
                ];

                $dev_entries['defer'] = [
                    'text' => use_defer() ? 'Using Deferred loading' : 'Using Normal loading',
                    'disabled' => true,
                ];
            }

            if (! is_admin_page() && ! empty($dev_entries)) {	// Use the same dev entries in "Page" menu:
                if (empty($entries['page']['entries'])) {
                    $entries['page']['entries'] = [];
                    $page_dev_entries = $dev_entries;
                } else {
                    $page_dev_entries = array_merge([[
                        'separator' => true,
                    ]], $dev_entries);
                }
                if (isset($page_dev_entries['coll'])) {	// Don't display collection debug info in "Page" menu:
                    unset($page_dev_entries['coll']);
                }
                $entries['page']['entries'] = array_merge($entries['page']['entries'], $page_dev_entries);
            }

            if (($dev_menu || $debug) && ! is_admin_page() && ! empty($Blog)) { // Display a menu to turn on/off the debug containers
                global $ReqURI, $Session;

                $dev_entries[] = [
                    'separator' => true,
                ];

                if ($Session->get('display_containers_' . $Blog->ID) == 1) { // To hide the debug containers
                    $dev_entries['containers'] = [
                        'text' => T_('Hide containers'),
                        'href' => url_add_param(regenerate_url('display_containers'), 'display_containers=hide'),
                    ];
                } else { // To show the debug containers
                    $dev_entries['containers'] = [
                        'text' => T_('Show containers'),
                        'href' => url_add_param(regenerate_url('display_containers'), 'display_containers=show'),
                    ];
                }

                if ($Session->get('display_includes_' . $Blog->ID) == 1) { // To hide the debug includes
                    $dev_entries['includes'] = [
                        'text' => T_('Hide includes'),
                        'href' => url_add_param(regenerate_url('display_containers'), 'display_includes=hide'),
                    ];
                } else { // To show the debug includes
                    $dev_entries['includes'] = [
                        'text' => T_('Show includes'),
                        'href' => url_add_param(regenerate_url('display_containers'), 'display_includes=show'),
                    ];
                }
            }
        }

        if ($entries !== null) {
            $topleft_Menu->add_menu_entries(null, $entries);
        }

        // ---------------------------------------------------------------------------

        /*
         * RIGHT MENU
         */
        global $localtimenow, $is_admin_page;

        $entries = [];

        // Dev menu:
        global $debug_jslog;
        if ($debug || $debug_jslog) { // Show JS log menu if debug is enabled
            $dev_entries[] = [
                'separator' => true,
            ];

            $dev_entries['jslog'] = [
                'text' => T_('JS log'),
                'title' => T_('JS log'),
                'class' => 'jslog_switcher',
            ];
        }

        // Collection locales:
        if (isset($Blog) && count($coll_locales = $Blog->get_locales())) {
            global $locales, $current_locale;

            $dev_entries[] = [
                'separator' => true,
            ];

            $current_coll_locale = get_param('coll_locale') != '' ? get_param('coll_locale') : $current_locale;
            foreach ($coll_locales as $coll_locale_key => $linked_coll_ID) {
                if (! isset($locales[$coll_locale_key]) || ! $locales[$coll_locale_key]['enabled']) {	// Skip wrong or disabled locale:
                    continue;
                }

                $is_selected = ($current_coll_locale == $coll_locale_key);
                $dev_entries[] = [
                    'text' => ($is_selected ? '&#10003; ' : '') . $locales[$coll_locale_key]['name'],
                    'href' => $is_selected ?
                        // Url to edit collection locales list:
                        $admin_url . '?ctrl=coll_settings&amp;tab=general&amp;blog=' . $Blog->ID :
                        // Url to change locale of the current collection:
                        url_add_param(regenerate_url('coll_locale'), 'coll_locale=' . urlencode($coll_locale_key)),
                ];
            }
        }

        if (! empty($dev_entries)) { // Add Dev menu if at least one entry is should be displayed
            $entries['dev'] = [
                'href' => $admin_url . '#',
                'text' => '<span class="fa fa-wrench"></span> Dev',
                'entries' => $dev_entries,
                'class' => 'debug_dev_button',
            ];
        }

        // User menu:
        $current_user_Group = $current_User->get_Group();
        $userprefs_entries = [
            'name' => [
                'text' => $current_User->get_avatar_imgtag('crop-top-32x32', '', 'left') . '&nbsp;'
                                    . $current_User->get('login')
                                    . '<br />&nbsp;<span class="note">' . $current_user_Group->get_name() . '</span>',
                'href' => get_user_profile_url(),
            ],
        ];

        $userprefs_entries[] = [
            'separator' => true,
        ];

        $user_profile_url = get_user_profile_url();
        if (! empty($user_profile_url)) { // Display this menu item only when url is available to current user
            $userprefs_entries['profile'] = [
                'text' => T_('Edit your profile') . '&hellip;',
                'href' => $user_profile_url,
            ];
        }
        $user_avatar_url = get_user_avatar_url();
        if (! empty($user_avatar_url)) { // Display this menu item only when url is available to current user
            $userprefs_entries['avatar'] = [
                'text' => T_('Your profile picture') . '&hellip;',
                'href' => $user_avatar_url,
            ];
        }
        $user_pwdchange_url = get_user_pwdchange_url();
        if (! empty($user_pwdchange_url)) { // Display this menu item only when url is available to current user
            $userprefs_entries['pwdchange'] = [
                'text' => T_('Change password') . '&hellip;',
                'href' => $user_pwdchange_url,
            ];
        }
        $user_preferences_url = get_user_preferences_url();
        if (! empty($user_preferences_url)) { // Display this menu item only when url is available to current user
            $userprefs_entries['userprefs'] = [
                'text' => T_('Preferences') . '&hellip;',
                'href' => $user_preferences_url,
            ];
        }
        $user_subs_url = get_user_subs_url();
        if (! empty($user_subs_url)) { // Display this menu item only when url is available to current user
            $userprefs_entries['subs'] = [
                'text' => T_('Emails') . '&hellip;',
                'href' => $user_subs_url,
            ];
        }

        $entries['userprefs'] = [
            'text' => '<strong>' . $current_User->get_colored_login() . '</strong>',
            'href' => get_user_profile_url(),
            'entries' => $userprefs_entries,
        ];
        $entries['time'] = [
            'text' => date(locale_shorttimefmt(), $localtimenow),
            'disabled' => true,
            'entry_class' => 'rwdhide',
        ];

        if (check_user_perm('admin', 'normal') && check_user_perm('options', 'view')) { // Make time as link to Timezone settings if permission
            $entries['time']['disabled'] = false;
            $entries['time']['href'] = $admin_url . '?ctrl=time';
        }

        /*
         * We currently support only one backoffice skin, so we don't need a system for selecting the backoffice skin.
        // ADMIN SKINS:
        if( $is_admin_page )
        {
            $admin_skins = get_admin_skins();
            if( count( $admin_skins ) > 1 )
            {	// We have several admin skins available: display switcher:
                $entries['userprefs']['entries']['admskins'] = array(
                        'text' => T_('Admin skin'),
                    );
                $redirect_to = rawurlencode(regenerate_url('', '', '', '&'));
                foreach( $admin_skins as $admin_skin )
                {
                    $entries['userprefs']['entries']['admskins']['entries'][$admin_skin] = array(
                            'text' => $admin_skin,
                            'href' => $admin_url.'?ctrl=users&amp;action=change_admin_skin&amp;new_admin_skin='.rawurlencode($admin_skin)
                                .'&amp;redirect_to='.$redirect_to
                        );
                }
            }
        }
         */

        $entries['userprefs']['entries'][] = [
            'separator' => true,
        ];

        $entries['userprefs']['entries']['logout'] = [
            'text' => T_('Log out') . '!',
            'href' => get_user_logout_url(),
        ];

        $topright_Menu->add_menu_entries(null, $entries);
    }

    /**
     * Builds the 3rd half of the menu. This is the one with the configuration features
     *
     * At some point this might be displayed differently than the 1st half.
     */
    public function build_menu_3()
    {
        global $blog, $loc_transinfo, $ctrl, $admin_url, $Settings;
        /**
         * @var User
         */
        global $current_User;
        global $Collection, $Blog;
        /**
         * @var AdminUI_general
         */
        global $AdminUI;

        $perm_admin_normal = check_user_perm('admin', 'normal');
        $perm_options = check_user_perm('options', 'view');
        $perm_users = check_user_perm('users', 'view');

        /**** Users | My profile ****/
        if ($perm_admin_normal && $perm_users) { // Permission to view users:
            $users_entries = [
                'text' => T_('Users'),
                'title' => T_('User management'),
                'href' => '?ctrl=users',
            ];

            $user_ID = param('user_ID', 'integer', null);
        } else {
            $user_ID = $current_User->ID;
            // Only perm to view his own profile:
            $users_entries = [
                'text' => T_('My profile'),
                'title' => T_('User profile'),
                'href' => '?ctrl=user&amp;user_tab=profile&amp;user_ID=' . $user_ID,
            ];
        }

        if ($perm_admin_normal && $perm_users) { // Has permission for viewing all users
            // fp> the following submenu needs even further breakdown.
            $users_entries['entries'] = [
                'users' => [
                    'text' => T_('Users'),
                    'href' => '?ctrl=users',
                ],
                'groups' => [
                    'text' => T_('Groups'),
                    'href' => '?ctrl=groups',
                ],
                'stats' => [
                    'text' => T_('Stats'),
                    'href' => '?ctrl=users&amp;tab=stats',
                ],
                'usersettings' => [
                    'text' => T_('Settings'),
                    'href' => '?ctrl=usersettings',
                    'entries' => [
                        'usersettings' => [
                            'text' => T_('Profiles'),
                            'href' => '?ctrl=usersettings',
                        ],
                        'registration' => [
                            'text' => T_('Registration & Login'),
                            'href' => '?ctrl=registration',
                        ],
                        'invitations' => [
                            'text' => T_('Invitations'),
                            'href' => '?ctrl=invitations',
                        ],
                        'display' => [
                            'text' => T_('Display'),
                            'href' => '?ctrl=display',
                        ],
                        'userfields' => [
                            'text' => T_('User fields'),
                            'href' => '?ctrl=userfields',
                        ],
                        'accountclose' => [
                            'text' => T_('Account closing'),
                            'href' => '?ctrl=accountclose',
                        ],
                    ],
                ],
                'usertags' => [
                    'text' => T_('User Tags'),
                    'href' => '?ctrl=usertags',
                ],
            ];
        }

        $AdminUI->add_menu_entries(null, [
            'users' => $users_entries,
        ]);

        if (check_user_perm('orgs', 'create')) {	// Display a menu item for organizations if user has a perm at least to create own organization:
            $AdminUI->add_menu_entries(['users'], [
                'organizations' => [
                    'text' => T_('Organizations'),
                    'href' => '?ctrl=organizations',
                ],
            ], 'groups');
        }

        /**** Emails ****/
        $perm_emails = check_user_perm('emails', 'view');
        if ($perm_admin_normal && $perm_options && $perm_emails) { // Permission to view email management:
            $AdminUI->add_menu_entries(null, [
                'email' => [
                    'text' => T_('Emails'),
                    'href' => '?ctrl=newsletters',
                    'entries' => [
                        'newsletters' => [
                            'text' => T_('Lists'),
                            'href' => '?ctrl=newsletters',
                        ],
                        'campaigns' => [
                            'text' => T_('Campaigns'),
                            'href' => '?ctrl=campaigns',
                            'entries' => [
                                'list' => [
                                    'text' => T_('List'),
                                    'href' => '?ctrl=campaigns',
                                ],
                                'plugins' => [
                                    'text' => T_('Plugins'),
                                    'href' => '?ctrl=campaigns&amp;tab=plugins',
                                ],
                            ],
                        ],
                        'sent' => [
                            'text' => T_('Sent'),
                            'href' => '?ctrl=email&amp;tab=sent',
                            'entries' => [
                                'log' => [
                                    'text' => T_('Send Log'),
                                    'href' => '?ctrl=email&amp;tab=sent',
                                ],
                                'stats' => [
                                    'text' => T_('Stats'),
                                    'href' => '?ctrl=email&amp;tab=sent&amp;tab3=stats',
                                ],
                                'envelope' => [
                                    'text' => T_('Envelope'),
                                    'href' => '?ctrl=email&amp;tab=sent&amp;tab3=envelope',
                                ],
                                'throttling' => [
                                    'text' => T_('Throttling'),
                                    'href' => '?ctrl=email&amp;tab=sent&amp;tab3=throttling',
                                ],
                                'smtp' => [
                                    'text' => T_('SMTP gateway'),
                                    'href' => '?ctrl=email&amp;tab=sent&amp;tab3=smtp',
                                ],
                            ],
                        ],
                        'return' => [
                            'text' => T_('Returned'),
                            'href' => '?ctrl=email&amp;tab=return',
                            'entries' => [
                                'log' => [
                                    'text' => T_('Return Log'),
                                    'href' => '?ctrl=email&amp;tab=return&amp;tab3=log',
                                ],
                                'settings' => [
                                    'text' => T_('POP/IMAP Settings'),
                                    'href' => '?ctrl=email&amp;tab=return&amp;tab3=settings',
                                ],
                            ],
                        ],
                        'addresses' => [
                            'text' => T_('Addresses'),
                            'href' => '?ctrl=email',
                        ],
                    ],
                ],
            ]);

            if ($perm_options) {	// If current user has a permissions to view options:
                $AdminUI->add_menu_entries('email', [
                    'automations' => [
                        'text' => T_('Automations'),
                        'href' => '?ctrl=automations',
                    ],
                ], 'campaigns');
            }

            if (check_user_perm('emails', 'edit')) {	// Allow to test a returned email and smtp sending only if user has a permission to edit email settings:
                $AdminUI->add_menu_entries(['email', 'return'], [
                    'test' => [
                        'text' => T_('Test'),
                        'href' => '?ctrl=email&amp;tab=return&amp;tab3=test',
                    ],
                ]);
                $AdminUI->add_menu_entries(['email', 'sent'], [
                    'test' => [
                        'text' => T_('Test'),
                        'href' => '?ctrl=email&amp;tab=sent&amp;tab3=test',
                    ],
                ]);
            }
        }

        /**** System ****/
        if ($perm_admin_normal && $perm_options) { // Permission to view settings:
            $AdminUI->add_menu_entries(null, [
                'options' => [
                    'text' => T_('System'),
                    'href' => $admin_url . '?ctrl=system',
                ],
            ]);

            $perm_spam = check_user_perm('spamblacklist', 'view');

            if ($perm_admin_normal && ($perm_options || $perm_spam)) { // Permission to view tools or antispam.
                if ($perm_options) { // Permission to view settings:
                    // FP> This assumes that we don't let regular users access the tools, including plugin tools.
                    $AdminUI->add_menu_entries('options', [
                        'system' => [
                            'text' => T_('Status'),
                            'href' => $admin_url . '?ctrl=system',
                        ],
                        'cron' => [
                            'text' => T_('Scheduler'),
                            'href' => $admin_url . '?ctrl=crontab',
                            'entries' => [
                                'list' => [
                                    'text' => T_('List'),
                                    'href' => $admin_url . '?ctrl=crontab',
                                ],
                                'settings' => [
                                    'text' => T_('Settings'),
                                    'href' => $admin_url . '?ctrl=crontab&amp;tab=settings',
                                ],
                                'test' => [
                                    'text' => T_('Test'),
                                    'href' => $admin_url . '?ctrl=crontab&amp;tab=test',
                                ],
                            ],
                        ],
                    ]);
                }
                if ($perm_spam) { // Permission to view antispam:
                    $AdminUI->add_menu_entries('options', [
                        'antispam' => [
                            'text' => T_('Antispam'),
                            'href' => '?ctrl=antispam',
                            'entries' => [
                                'blacklist' => [
                                    'text' => T_('Blacklist'),
                                    'href' => '?ctrl=antispam',
                                ],
                            ],
                        ],
                    ]);

                    if ($perm_options) { // If we have access to options, then we add a submenu:
                        $AdminUI->add_menu_entries(['options', 'antispam'], [
                            'ipranges' => [
                                'text' => T_('IP Ranges'),
                                'href' => '?ctrl=antispam&amp;tab3=ipranges',
                            ],
                        ]);

                        $AdminUI->add_menu_entries(['options', 'antispam'], [
                            'countries' => [
                                'text' => T_('Countries'),
                                'href' => '?ctrl=antispam&amp;tab3=countries',
                            ],
                        ]);

                        if (check_user_perm('stats', 'list')) {
                            $AdminUI->add_menu_entries(['options', 'antispam'], [
                                'domains' => [
                                    'text' => T_('Referring domains'),
                                    'href' => '?ctrl=antispam&amp;tab3=domains',
                                ],
                            ]);
                        }

                        $AdminUI->add_menu_entries(['options', 'antispam'], [
                            'settings' => [
                                'text' => T_('Settings'),
                                'href' => '?ctrl=antispam&amp;tab3=settings',
                            ],
                        ]);

                        if (check_user_perm('options', 'edit')) {
                            $AdminUI->add_menu_entries(['options', 'antispam'], [
                                'tools' => [
                                    'text' => T_('Tools'),
                                    'href' => '?ctrl=antispam&amp;tab3=tools',
                                ],
                            ]);
                        }
                    }
                }
            }

            $AdminUI->add_menu_entries('options', [
                'regional' => [
                    'text' => T_('Regional'),
                    'href' => '?ctrl=regional',
                    'entries' => [
                        'locales' => [
                            'text' => T_('Locales'),
                            'href' => '?ctrl=locales' . ((isset($loc_transinfo) && $loc_transinfo) ? '&amp;loc_transinfo=1' : ''),
                        ],
                        'time' => [
                            'text' => T_('Time'),
                            'href' => '?ctrl=time',
                        ],
                        'countries' => [
                            'text' => T_('Countries'),
                            'href' => '?ctrl=countries',
                        ],
                        'regions' => [
                            'text' => T_('Regions'),
                            'href' => '?ctrl=regions',
                        ],
                        'subregions' => [
                            'text' => T_('Sub-regions'),
                            'href' => '?ctrl=subregions',
                        ],
                        'cities' => [
                            'text' => T_('Cities'),
                            'href' => '?ctrl=cities',
                        ],
                        'currencies' => [
                            'text' => T_('Currencies'),
                            'href' => '?ctrl=currencies',
                        ],
                    ],
                ],
                'skins' => [
                    'text' => T_('Skins'),
                    'href' => '?ctrl=skins&amp;tab=system',
                ],
                'plugins' => [
                    'text' => T_('Plugins'),
                    'href' => '?ctrl=plugins',
                    'entries' => [
                        'general' => [
                            'text' => T_('General settings'),
                            'href' => '?ctrl=plugins',
                        ],
                        'shared' => [
                            'text' => T_('Settings for shared containers'),
                            'href' => '?ctrl=plugins&amp;tab=shared',
                        ],
                    ],
                ],
                'remotepublish' => [
                    'text' => T_('Remote Publishing'),
                    'href' => '?ctrl=remotepublish',
                    'entries' => [
                        'eblog' => [
                            'text' => T_('Post by Email'),
                            'href' => '?ctrl=remotepublish&amp;tab=eblog',
                        ],
                        'xmlrpc' => [
                            'text' => T_('XML-RPC'),
                            'href' => '?ctrl=remotepublish&amp;tab=xmlrpc',
                        ],
                    ],
                ],
            ]);

            if (check_user_perm('options', 'edit')) {
                $AdminUI->add_menu_entries('options', [
                    'syslog' => [
                        'text' => T_('System log'),
                        'href' => '?ctrl=syslog',
                    ],
                ]);
            }
        }
    }

    /**
     * Get the core module cron jobs
     *
     * @see Module::get_cron_jobs()
     */
    public function get_cron_jobs()
    {
        return [
            'poll-antispam-blacklist' => [
                'name' => T_('Poll the antispam blacklist'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_antispam_poll.job.php',
                'params' => null,
            ],
            'process-return-path-inbox' => [
                'name' => T_('Process the return path inbox'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_decode_returned_emails.job.php',
                'params' => null,
            ],
            'manage-email-statuses' => [
                'name' => T_('Manage email address statuses'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_manage_email_statuses.job.php',
                'params' => null,
            ],
            'send-non-activated-account-reminders' => [
                'name' => T_('Send reminders about non-activated accounts'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_activate_account_reminder.job.php',
                'params' => null,
            ],
            'send-inactive-account-reminders' => [
                'name' => T_('Send reminders about inactive accounts'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_inactive_account_reminder.job.php',
                'params' => null,
            ],
            'execute-automations' => [
                'name' => T_('Execute automations'),
                'help' => '#',
                'ctrl' => 'cron/jobs/_execute_automations.job.php',
                'params' => null,
            ],
        ];
    }
}

$_core_Module = new _core_Module();
