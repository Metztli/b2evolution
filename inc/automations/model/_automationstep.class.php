<?php
/**
 * This file implements the automation step class.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
*
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 *
 * @package evocore
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

load_class('_core/model/dataobjects/_dataobject.class.php', 'DataObject');
load_funcs('automations/model/_automation.funcs.php');


/**
 * AutomationStep Class
 *
 * @package evocore
 */
class AutomationStep extends DataObject
{
    public $autm_ID;

    public $order;

    public $label;

    public $type;

    public $info;

    public $yes_next_step_ID;

    public $yes_next_step_delay;

    public $no_next_step_ID;

    public $no_next_step_delay;

    public $error_next_step_ID;

    public $error_next_step_delay;

    public $diagram;

    public $Automation = null;

    public $yes_next_AutomationStep = null;

    public $no_next_AutomationStep = null;

    public $error_next_AutomationStep = null;

    /**
     * Constructor
     *
     * @param object table Database row
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct('T_automation__step', 'step_', 'step_ID');

        if ($db_row !== null) {
            $this->ID = $db_row->step_ID;
            $this->autm_ID = $db_row->step_autm_ID;
            $this->order = $db_row->step_order;
            $this->label = $db_row->step_label;
            $this->type = $db_row->step_type;
            $this->info = $db_row->step_info;
            $this->yes_next_step_ID = $db_row->step_yes_next_step_ID;
            $this->yes_next_step_delay = $db_row->step_yes_next_step_delay;
            $this->no_next_step_ID = $db_row->step_no_next_step_ID;
            $this->no_next_step_delay = $db_row->step_no_next_step_delay;
            $this->error_next_step_ID = $db_row->step_error_next_step_ID;
            $this->error_next_step_delay = $db_row->step_error_next_step_delay;
            $this->diagram = $db_row->step_diagram;
        }
    }

    /**
     * Get delete restriction settings
     *
     * @return array
     */
    public static function get_delete_restrictions()
    {
        return [
            [
                'table' => 'T_automation__user_state',
                'fk' => 'aust_next_step_ID',
                'msg' => TB_('%d states of User in Automation'),
            ],
            [
                'table' => 'T_automation__step',
                'fk' => 'step_yes_next_step_ID',
                'and_condition' => 'step_yes_next_step_ID != step_ID',
                'msg' => TB_('Step is used as Next Step %d times') . ' ' . TB_('("YES" column)'),
            ],
            [
                'table' => 'T_automation__step',
                'fk' => 'step_no_next_step_ID',
                'and_condition' => 'step_no_next_step_ID != step_ID',
                'msg' => TB_('Step is used as Next Step %d times') . ' ' . TB_('("NO" column)'),
            ],
            [
                'table' => 'T_automation__step',
                'fk' => 'step_error_next_step_ID',
                'and_condition' => 'step_error_next_step_ID != step_ID',
                'msg' => TB_('Step is used as Next Step %d times') . ' ' . TB_('("ERROR" column)'),
            ],
        ];
    }

    /**
     * Insert object into DB based on previously recorded changes.
     *
     * @param boolean TRUE to check if step can be inserted e.g. when automation is not paused
     * @return boolean true on success
     */
    public function dbinsert($check_restriction = true)
    {
        if ($check_restriction && ! $this->can_be_modified()) {	// If this step cannnot be modified
            return false;
        }

        if ($r = parent::dbinsert()) {
            // Update next steps with selected option "Loop" to ID of this new inserted Step:
            $next_steps = [
                'yes_next_step_ID',
                'no_next_step_ID',
                'error_next_step_ID',
            ];
            foreach ($next_steps as $next_step_ID_name) {
                if (get_param('step_' . $next_step_ID_name) == 'loop') {
                    $this->set($next_step_ID_name, $this->ID); // Loop
                }
            }
            $this->dbupdate();
        }

        return $r;
    }

    /**
     * Update the DB based on previously recorded changes
     *
     * @return boolean true on success, false on failure to update, NULL if no update necessary
     */
    public function dbupdate()
    {
        if (! $this->can_be_modified()) {	// If this step cannnot be modified
            return false;
        }

        return parent::dbupdate();
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
            case 'if_condition_js_object':
                // Format values(like dates) of the field "IF Condition" from MySQL DB format to current locale format:
                return param_format_condition($this->get('info'), 'js');
        }

        return parent::get($parname);
    }

    /**
     * Load data from Request form fields.
     *
     * @return boolean true if loaded data seems valid.
     */
    public function load_from_Request()
    {
        global $DB, $admin_url;

        if (empty($this->ID)) {	// Set Automation only for new creating Step:
            param('autm_ID', 'integer', true);
            $this->set_from_Request('autm_ID', 'autm_ID');
        }

        if (! $this->can_be_modified() && ! param('confirm_pause', 'integer')) {	// Don't allow to edit step of active automation without confirmation:
            global $Messages;
            $Messages->add(empty($this->ID)
                ? TB_('You must pause the automation before creating new step.')
                : TB_('You must pause the automation before changing step.'), 'error');
        }

        // Order:
        $step_order = param('step_order', 'integer', null);
        if ($this->ID > 0) {	// Order is required for edited step:
            param_string_not_empty('step_order', TB_('Please enter a step order number.'));
        } elseif ($step_order === null) {	// Set order for new creating step automatically:
            $max_order_SQL = new SQL('Get max step order for Automation #' . $this->get('autm_ID'));
            $max_order_SQL->SELECT('MAX( step_order )');
            $max_order_SQL->FROM('T_automation__step');
            $max_order_SQL->WHERE('step_autm_ID = ' . $this->get('autm_ID'));
            set_param('step_order', $DB->get_var($max_order_SQL) + 1);
        }
        $this->set_from_Request('order');
        if ($this->get('order') > 0) {	// Check for unique order per Automation:
            $check_order_SQL = new SQL('Check unique step order for Automation #' . $this->get('autm_ID'));
            $check_order_SQL->SELECT('step_ID');
            $check_order_SQL->FROM('T_automation__step');
            $check_order_SQL->WHERE('step_autm_ID = ' . $this->get('autm_ID'));
            $check_order_SQL->WHERE_and('step_order = ' . $this->get('order'));
            if ($this->ID > 0) {	// Exclude this Step:
                $check_order_SQL->WHERE_and('step_ID != ' . $this->ID);
            }
            if ($existing_step_ID = $DB->get_var($check_order_SQL)) {	// Display error because of duplicated order in the same Automation:
                global $admin_url;
                param_error(
                    'step_order',
                    sprintf(
                        TB_('Another step with the same order number already exists in the current automation. Do you want to <a %s>edit that step</a>?'),
                        'href="' . $admin_url . '?ctrl=automations&amp;action=edit_step&amp;step_ID=' . $existing_step_ID . '"'
                    )
                );
            }
        }
        param_check_range('step_order', -2147483646, 2147483647, sprintf(TB_('Step order must be numeric (%d - %d).'), -2147483646, 2147483647));

        // Type:
        param_string_not_empty('step_type', 'Please select a step type.');
        $this->set_from_Request('type');
        // Save additional info depending on step type:
        switch ($this->get('type')) {
            case 'if_condition':
                // IF Condition:
                param_condition('step_if_condition');
                param_string_not_empty('step_if_condition', TB_('Please set a condition.'));
                $this->set('info', get_param('step_if_condition'));
                break;

            case 'send_campaign':
                // Email campaign:
                param('step_email_campaign', 'integer', null);
                param_check_number('step_email_campaign', TB_('Please select an email campaign.'), true);
                $this->set('info', get_param('step_email_campaign'));
                break;

            case 'notify_owner':
                // Notify owner:
                param('step_notification_message', 'text');
                param_check_not_empty('step_notification_message', TB_('Please enter a notification message.'));
                $this->set('info', get_param('step_notification_message'));
                break;

            case 'add_usertag':
            case 'remove_usertag':
                // Add/Remove usertag:
                param_string_not_empty('step_usertag', TB_('Please enter an user tag.'));
                if (preg_match('/(^-|[;,])/', get_param('step_usertag'))) {	// If usertag has a not allowed char:
                    param_error('step_usertag', sprintf(TB_('Usertag cannot start with %s and contain chars %s'), '<code>-</code>', '<code>;,</code>'));
                }
                $this->set('info', get_param('step_usertag'));
                break;

            case 'subscribe':
            case 'unsubscribe':
                // Subscribe/Unsubscribe:
                param('step_newsletter', 'integer', true);
                param_check_not_empty('step_newsletter', TB_('Please select a list.'));
                $this->set('info', get_param('step_newsletter'));
                break;

            case 'start_automation':
                // Start new automation:
                param('step_automation', 'integer', true);
                param_check_not_empty('step_automation', TB_('Please select an automation.'));
                $this->set('info', get_param('step_automation'));
                break;

            case 'user_status':
                // Change user account status:
                param('user_status', 'string', true);
                param_check_not_empty('user_status', /* Do NOT translate because this error is impossible for normal form */ 'Please select an account status.');
                $this->set('info', get_param('user_status'));
                break;

            default:
                $this->set('info', null, true);
        }

        // Next steps:
        $next_steps = [
            'yes_next_step_ID' => 'yes_next_step_delay',
            'no_next_step_ID' => 'no_next_step_delay',
            'error_next_step_ID' => 'error_next_step_delay',
        ];
        foreach ($next_steps as $next_step_ID_name => $next_step_delay_name) {
            if (($this->get('type') == 'notify_owner' && $next_step_ID_name == 'no_next_step_ID') ||
                (in_array($this->get('type'), ['add_usertag', 'remove_usertag']) && $next_step_ID_name == 'error_next_step_ID')) {	// Some next steps are not used depending on step type:
                $this->set($next_step_ID_name, null, true);
                $this->set($next_step_delay_name, null, true);
            } else {
                $this->set($next_step_ID_name, intval(param('step_' . $next_step_ID_name, 'string')));
                $this->set($next_step_delay_name, param_duration('step_' . $next_step_delay_name));
            }
        }

        // Label:
        $this->set_label();

        return ! param_errors_detected();
    }

    /**
     * Get Automation object of this step
     *
     * @return object Automation
     */
    public function &get_Automation()
    {
        if ($this->Automation === null) {	// Initialize Automation object only first time and store in cache:
            $AutomationCache = &get_AutomationCache();
            $this->Automation = &$AutomationCache->get_by_ID($this->get('autm_ID'), false, false);
        }

        return $this->Automation;
    }

    /**
     * Get next Step object of this Step by step ID
     *
     * @param integer Step type: 'yes', 'no', 'error'
     * @return object|boolean Next Automation Step OR
     *                        FALSE - if automation should be stopped after this Step
     *                                because either it is configured for STOP action
     *                                            or it is the latest step of the automation
     */
    public function &get_next_AutomationStep_by_type($next_step_type)
    {
        switch ($next_step_type) {
            case 'YES':
                $next_step_ID = $this->get('yes_next_step_ID');
                break;
            case 'NO':
                $next_step_ID = $this->get('no_next_step_ID');
                break;
            case 'ERROR':
                $next_step_ID = $this->get('error_next_step_ID');
                break;
            default:
                debug_die('Invalid automation next step type "' . $next_step_type . '"');
        }

        $next_AutomationStep = false;

        $possible_step_type_results = step_get_result_labels();
        if (empty($possible_step_type_results[$this->get('type')][$next_step_type])) {	// If the requested result(YES, NO or ERROR) is not supported by current step type:
            return $next_AutomationStep;
        }

        $next_step_ID = intval($next_step_ID);

        $AutomationStepCache = &get_AutomationStepCache();
        if ($next_step_ID > 0) {	// Get a next Step by defined ID:
            $next_AutomationStep = &$AutomationStepCache->get_by_ID($next_step_ID, false, false);
        }

        if ($next_step_ID == -1) {	// Stop workflow when option is selected to "STOP":
            $next_AutomationStep = false;
        } elseif ($next_step_ID == 0 || ! $next_AutomationStep) {	// Get next ordered Step when option is selected to "Continue" OR Step cannot be found by ID in DB:
            $next_AutomationStep = &$AutomationStepCache->get_by_ID($this->get_next_ordered_step_ID(), false, false);
            if (empty($next_AutomationStep)) {	// If it is the latest Step of the Automation:
                $next_AutomationStep = false;
            }
        }

        return $next_AutomationStep;
    }

    /**
     * Get ID of the next ordered Step after this Step
     *
     * @return integer|null Step ID or NULL if this is the latest
     */
    public function get_next_ordered_step_ID()
    {
        if (empty($this->ID)) {	// New creating step is the latest by default:
            return null;
        }

        global $DB;

        $next_ordered_step_SQL = new SQL('Get next ordered Step after current Step #' . $this->ID);
        $next_ordered_step_SQL->SELECT('step_ID');
        $next_ordered_step_SQL->FROM('T_automation__step');
        $next_ordered_step_SQL->WHERE('step_autm_ID = ' . $DB->quote($this->get('autm_ID')));
        $next_ordered_step_SQL->WHERE_and('step_order > ' . $DB->quote($this->get('order')));
        $next_ordered_step_SQL->ORDER_BY('step_order ASC');
        $next_ordered_step_SQL->LIMIT(1);

        return $DB->get_var($next_ordered_step_SQL);
    }

    /**
     * Get YES next Step object of this Step
     *
     * @return object|boolean Next Automation Step OR
     *                        FALSE - if automation should be stopped after this Step
     *                                because either it is configured for STOP action
     *                                            or it is the latest step of the automation
     */
    public function &get_yes_next_AutomationStep()
    {
        if ($this->yes_next_AutomationStep === null) {	// Load next Step into cache object:
            $this->yes_next_AutomationStep = &$this->get_next_AutomationStep_by_type('YES');
        }

        return $this->yes_next_AutomationStep;
    }

    /**
     * Get NO next Step object of this Step
     *
     * @return object|boolean Next Automation Step OR
     *                        FALSE - if automation should be stopped after this Step
     *                                because either it is configured for STOP action
     *                                            or it is the latest step of the automation
     */
    public function &get_no_next_AutomationStep()
    {
        if ($this->no_next_AutomationStep === null) {	// Load next Step into cache object:
            $this->no_next_AutomationStep = &$this->get_next_AutomationStep_by_type('NO');
        }

        return $this->no_next_AutomationStep;
    }

    /**
     * Get ERROR next Step object of this Step
     *
     * @return object|boolean Next Automation Step OR
     *                        FALSE - if automation should be stopped after this Step
     *                                because either it is configured for STOP action
     *                                            or it is the latest step of the automation
     */
    public function &get_error_next_AutomationStep()
    {
        if ($this->error_next_AutomationStep === null) {	// Load next Step into cache object:
            $this->error_next_AutomationStep = &$this->get_next_AutomationStep_by_type('ERROR');
        }

        return $this->error_next_AutomationStep;
    }

    /**
     * Execute action for this step
     *
     * @param integer User ID
     * @return string A process log
     */
    public function execute_action($user_ID)
    {
        global $DB, $servertimenow, $mail_log_message, $executed_automation_steps;

        // Initialize array to store executed steps per user in order to avoid infinite loops:
        if (! isset($executed_automation_steps)) {
            $executed_automation_steps = [];
        }
        if (! isset($executed_automation_steps[$user_ID])) {
            $executed_automation_steps[$user_ID] = [];
        }

        $Automation = &$this->get_Automation();

        $log_nl = "\n";
        $log_point = ' - ';
        $log_bold_start = '<b>';
        $log_bold_end = '</b>';

        $UserCache = &get_UserCache();
        $step_User = &$UserCache->get_by_ID($user_ID, false, false);

        // Log:
        $process_log = 'Executing ' . $log_bold_start . 'Step #' . $this->get('order') . $log_bold_end
            . '(' . step_get_type_title($this->get('type')) . ': ' . $this->get('label') . ')'
            . ' of ' . $log_bold_start . 'Automation: #' . $Automation->ID . $log_bold_end . '(' . $Automation->get('name') . ')'
            . ' for ' . $log_bold_start . 'User #' . $user_ID . $log_bold_end . ($step_User ? '(' . $step_User->get('login') . ')' : '') . '...' . $log_nl;

        // Retrun ERROR result by default for all unknown cases:
        $step_result = 'ERROR';
        $additional_result_message = '';

        if ($step_User) {	// Allow to execute action only if User is detected in DB:
            switch ($this->get('type')) {
                case 'if_condition':
                    if ($this->check_if_condition($step_User, $if_condition_log)) {	// The user is matched to condition of this step:
                        $step_result = 'YES';
                    } else {	// The user is NOT matched to condition of this step:
                        $step_result = 'NO';
                    }
                    // Log:
                    $process_log .= $log_point . 'Log: ' . $if_condition_log . $log_nl;
                    break;

                case 'send_campaign':
                    // Send email campaign
                    $EmailCampaignCache = &get_EmailCampaignCache();
                    if ($step_EmailCampaign = &$EmailCampaignCache->get_by_ID($this->get('info'), false, false)) {
                        if (in_array($user_ID, $step_EmailCampaign->get_recipients('full_receive'))) {	// If user already received this email:
                            $step_result = 'NO';
                            $additional_result_message = 'Email was ALREADY sent';
                        } elseif (in_array($user_ID, $step_EmailCampaign->get_recipients('full_skipped'))) {	// If user is marked to be manually skipped:
                            $step_result = 'NO';
                            $additional_result_message = 'Manually skipped';
                        } elseif (in_array($user_ID, $step_EmailCampaign->get_recipients('full_skipped_tag'))) {	// If user has user tag that should be skipped:
                            $step_result = 'NO';
                            $additional_result_message = 'User has skipped user tag';
                        } elseif (($user_subscribed_newsletter_ID = $Automation->is_user_subscribed($user_ID)) &&
                                $step_EmailCampaign->send_email($user_ID, '', '', 'auto', $user_subscribed_newsletter_ID, $Automation->ID)) {	// If user is subscribed to at least one newsletter of this Automation AND email has been sent to user successfully now:
                            $step_result = 'YES';
                        } else {	// Some error on sending of email to user:
                            // - problem with php mail function;
                            // - user cannot receive such email because of day limit;
                            // - user is not activated yet.
                            $step_result = 'ERROR';
                            if ($user_subscribed_newsletter_ID) {	// If user is subscribed but some error on email sending:
                                $additional_result_message = empty($mail_log_message) ? 'Unknown error' : $mail_log_message;
                            } else {	// If user just doesn't wait this email:
                                $additional_result_message = 'User #' . $step_User->ID . '(' . $step_User->get('login') . ') is not subscribed to email lists of the Automation';
                            }
                        }
                    } else {	// Wrong stored email campaign for this step:
                        $step_result = 'ERROR';
                        $additional_result_message = 'Email Campaign #' . $this->get('info') . ' is not found in DB.';
                    }
                    break;

                case 'notify_owner':
                    // Notify owner of automation:
                    if (! ($owner_User = &$Automation->get_owner_User())) {	// If owner User is not detected in DB:
                        $step_result = 'ERROR';
                        $additional_result_message = 'Owner User #' . $this->get('owner_user_ID') . ' is not found in DB.';
                        break;
                    }

                    $notification_message = str_replace(
                        [
                            '$step_number$',
                            '$step_ID$',
                            '$automation_name$',
                            '$automation_ID$',
                        ],
                        [
                            $this->get('order'),
                            $this->ID,
                            '"' . $Automation->get('name') . '"',
                            $Automation->ID,
                        ],
                        $this->get('info')
                    );

                    $step_user_login_html = $step_User->get_colored_login([
                        'mask' => '$avatar$ $login$',
                        'use_style' => true,
                        'protocol' => 'http:',
                    ]);

                    $email_template_params = [
                        'message_html' => nl2br(str_replace('$login$', $step_user_login_html, $notification_message)),
                        'message_text' => str_replace('$login$', $step_User->get('login'), $notification_message),
                    ];

                    if (send_mail_to_User($owner_User->ID, sprintf(TB_('Notification of automation %s'), '"' . $Automation->get('name') . '"'), 'automation_owner_notification', $email_template_params)) {	// If email has been sent to user successfully now:
                        $step_result = 'YES';
                    } else {	// Some error on sending of email to user:
                        // - problem with php mail function;
                        // - user cannot receive such email because of day limit;
                        // - user is not activated yet.
                        $step_result = 'ERROR';
                        $additional_result_message = empty($mail_log_message) ? 'Unknown error' : $mail_log_message;
                    }
                    break;

                case 'add_usertag':
                    // Add usertag:
                    $usertags = $step_User->get_usertags();
                    $new_usertag = $this->get('info');
                    if (in_array($new_usertag, $usertags)) {	// If step User was already tagged:
                        $step_result = 'NO';
                    } else {	// Add new usertag:
                        $step_User->add_usertags($new_usertag);
                        $step_User->dbupdate();
                        $step_result = 'YES';
                    }
                    // Display tag name in log:
                    $additional_result_message = $new_usertag;
                    break;

                case 'remove_usertag':
                    // Remove usertag:
                    $usertags = $step_User->get_usertags();
                    $del_usertag = $this->get('info');
                    if (! in_array($del_usertag, $usertags)) {	// if step User didn't have that tag:
                        $step_result = 'NO';
                    } else {	// Remove usertag:
                        $step_result = 'YES';
                        $step_User->remove_usertags($del_usertag);
                        $step_User->dbupdate();
                    }
                    // Display tag name in log:
                    $additional_result_message = $del_usertag;
                    break;

                case 'subscribe':
                case 'unsubscribe':
                    // Subscribe/Unsubscribe User to List:
                    $NewsletterCache = &get_NewsletterCache();
                    if ($Newsletter = &$NewsletterCache->get_by_ID($this->get('info'), false, false)) {	// If List/Newsletter exists:
                        if ($this->get('type') == 'subscribe') {	// Subscribe:
                            $affected_subscriprions_num = $step_User->subscribe($Newsletter->ID);

                            // Send notification to owners of lists where user was subscribed:
                            $step_User->send_list_owner_notifications('subscribe');
                        } else {	// Unsubscribe:
                            $affected_subscriprions_num = $step_User->unsubscribe($Newsletter->ID);

                            // Send notification to owners of lists where user was unsubscribed:
                            $step_User->send_list_owner_notifications('unsubscribe');
                        }
                        $step_result = ($affected_subscriprions_num ? 'YES' : 'NO');
                        // Display newsletter name in log:
                        $additional_result_message = $Newsletter->get('name');
                    } else {	// If List/Newsletter does not exist:
                        $step_result = 'ERROR';
                        $additional_result_message = 'List #' . $this->get('info') . ' is not found in DB.';
                    }
                    break;

                case 'start_automation':
                    // Start new Automation:
                    $AutomationCache = &get_AutomationCache();
                    if ($new_Automation = &$AutomationCache->get_by_ID($this->get('info'), false, false)) {	// If Automation exists:
                        $added_users_num = $new_Automation->add_users([$step_User->ID], [
                            'users_no_subs' => 'add',    // Add anyway users who are not subscribed to Newsletter of the Automation
                            'users_automated' => 'ignore', // Ignore users who are already in the Automation
                            'users_new' => 'add',    // Add new users
                        ]);
                        $step_result = ($added_users_num ? 'YES' : 'NO');
                        // Display newsletter name in log:
                        $additional_result_message = $new_Automation->get('name');
                    } else {	// If List/Newsletter does not exist:
                        $step_result = 'ERROR';
                        $additional_result_message = 'Automation #' . $this->get('info') . ' is not found in DB.';
                    }
                    break;

                case 'user_status':
                    // Change user account status:
                    $current_status = $step_User->get('status');
                    $new_status = $this->get('info');
                    if ($step_User->ID == 1) {	// Don't allow to change status of the Admin user:
                        $step_result = 'ERROR';
                        $additional_result_message = 'Status of admin user account cannot be changed';
                    } elseif ($current_status == $new_status) {	// If step User's account is already in the desired status:
                        $step_result = 'NO';
                        // Display status title in log:
                        $user_statuses = get_user_statuses();
                        $additional_result_message = (isset($user_statuses[$new_status]) ? $user_statuses[$new_status] : $new_status);
                    } elseif ($current_status == 'closed') {	// Don't allow to change a closed status:
                        $step_result = 'ERROR';
                        $additional_result_message = 'The closed user account cannot be changed to any other status';
                    } else {	// Change user account to another status:
                        $step_User->set('status', $new_status);
                        if ($step_User->dbupdate()) {	// Successful user updating:
                            $step_result = 'YES';
                            // Display status title in log:
                            $user_statuses = get_user_statuses();
                            $additional_result_message = (isset($user_statuses[$new_status]) ? $user_statuses[$new_status] : $new_status);
                        } else {	// Unknown error on user updating:
                            $step_result = 'ERROR';
                        }
                    }
                    break;

                default:
                    // Log:
                    $process_log .= $log_point . 'No implemented action' . $log_nl;
                    break;
            }
        } else {	// Wrong user:
            $additional_result_message = $log_bold_start . 'User #' . $user_ID . $log_bold_end . ' is not found in DB.';
        }

        // Log:
        if ($step_result == 'ERROR' && empty($additional_result_message)) {	// Set default additional error message:
            $additional_result_message = 'Unknown error';
        }
        $process_log .= $log_point . 'Result: ' . $this->get_result_title($step_result, $additional_result_message) . '.' . $log_nl;

        // Get data for next step:
        switch ($step_result) {
            case 'YES':
                $next_AutomationStep = &$this->get_yes_next_AutomationStep();
                $next_delay = $this->get('yes_next_step_delay');
                break;

            case 'NO':
                $next_AutomationStep = &$this->get_no_next_AutomationStep();
                $next_delay = $this->get('no_next_step_delay');
                break;

            case 'ERROR':
                $next_AutomationStep = &$this->get_error_next_AutomationStep();
                $next_delay = $this->get('error_next_step_delay');
                break;
        }

        if ($next_AutomationStep) {	// Use data for next step if it is defined:
            $next_step_ID = $next_AutomationStep->ID;
            if ($next_delay == 0 && in_array($next_AutomationStep->ID, $executed_automation_steps[$user_ID])) {	// Force a delay of infinite loop to 4 hour:
                $next_exec_ts = date2mysql($servertimenow + (3600 * 4));
            } else {	// Use normal delay of next step:
                $next_exec_ts = date2mysql($servertimenow + $next_delay);
            }
        } else {	// This was the end Step of the Automation:
            $next_step_ID = null;
            $next_exec_ts = null;
        }
        // Update data for next step or finish it:
        $DB->query(
            'UPDATE T_automation__user_state
			  SET aust_next_step_ID = ' . $DB->quote($next_step_ID) . ',
			      aust_next_exec_ts = ' . $DB->quote($next_exec_ts) . '
			WHERE aust_autm_ID = ' . $DB->quote($Automation->ID) . '
			  AND aust_user_ID = ' . $DB->quote($user_ID),
            'Update data for next Step after executing Step #' . $this->ID
        );

        // Log:
        $process_log .= ($next_AutomationStep
                ? $log_point . 'Next step: #' . $next_AutomationStep->get('order')
                    . '(' . step_get_type_title($next_AutomationStep->get('type')) . ($next_AutomationStep->get('label') == '' ? '' : ' "' . $next_AutomationStep->get('label') . '"') . ')'
                    . ' delay: ' . seconds_to_period($next_delay) . ', ' . $next_exec_ts
                : $log_point . 'There is no next step configured.');

        if ($next_delay == 0 && $next_AutomationStep) {	// If delay for next step is 0 seconds then we must execute such step right now:
            if (in_array($next_AutomationStep->ID, $executed_automation_steps[$user_ID])) {	// Don't run this next step because it was already executed for the user:
                $process_log .= $log_point . $log_bold_start . 'Next step rescheduled with a 4 hour delay to avoid infinite loop!' . $log_bold_end . $log_nl;
            } else {	// Run next step because it is not executed yet for the user:
                $executed_automation_steps[$user_ID][] = $this->ID;
                $process_log .= $log_nl . $log_point . $log_bold_start . 'Run next step immediately:' . $log_nl . $log_bold_end;
                $process_log .= $next_AutomationStep->execute_action($user_ID);
            }
        }

        return $process_log;
    }

    /**
     * Get name of automation step, it is used for `<select>` with $AutomationStepCache
     *
     * @return string
     */
    public function get_name()
    {
        $step_label = utf8_substr(utf8_trim($this->get('label')), 0, 100);
        return '#' . $this->get('order') . ' - '
            . (step_get_type_title($this->get('type')) . ': ' . $step_label);
    }

    /**
     * Get result title depending on step type
     *
     * @param string Result: YES, NO, ERROR
     * @param string Additional message, for example: some error message
     * @return string Result title
     */
    public function get_result_title($result, $additional_message = '')
    {
        $result_titles = step_get_result_titles();

        $result_title = isset($result_titles[$this->get('type')][$result]) ? $result_titles[$this->get('type')][$result] : $result;

        if (strpos($result_title, '%s') !== false) {	// Replace mask with additional message like error:
            $result_title = sprintf($result_title, '"' . $additional_message . '"');
        }

        return $result_title;
    }

    /**
     * Check result of "IF Condition"
     *
     * @param object|null User, NULL to get only log as scheme of current condition without checking
     * @param string Log process into this param
     * @return boolean TRUE if condition is matched for given user, otherwise FALSE
     */
    public function check_if_condition($step_User, &$process_log)
    {
        if ($this->get('type') != 'if_condition') {	// This is allowed only for step type "IF Condition":
            return false;
        }

        $json_object = json_decode($this->get('info'));

        if ($json_object === null || ! isset($json_object->valid) || $json_object->valid !== true) {	// Wrong object, Return false:
            return false;
        }

        return $this->check_if_condition_object($json_object, $step_User, $process_log);
    }

    /**
     * Check result of "IF Condition" object(one group of rules)
     * Used recursively to find all sub grouped conditions
     *
     * @param object JSON object of step type "IF Condition"
     * @param object|null User, NULL to get only log as scheme of current condition without checking
     * @param string Log process into this param
     * @return boolean TRUE if condition is matched for given user, otherwise FALSE
     */
    public function check_if_condition_object($json_object, $step_User, &$process_log)
    {
        if (! isset($json_object->condition) || ! in_array($json_object->condition, ['AND', 'OR']) || empty($json_object->rules)) {	// Wrong json object params, Skip it:
            return false;
        }

        // If user is not given we cannot do a checking,
        // Used to autogenerate step label:
        $check_result = ($step_User !== null);

        // Log:
        $process_log .= ' (' . ($check_result ? $json_object->condition : ' ');
        // Array to convert operator names to log format:
        $log_operators = [
            'equal' => '=',
            'not_equal' => '&#8800;',
            'less' => '<',
            'less_or_equal' => '&#8804;',
            'greater' => '>',
            'greater_or_equal' => '&#8805;',
            'between' => ['&#8805;', 'AND &#8804;'],
            'not_between' => ['<', 'OR >'],
        ];
        $log_fields = [
            'user_tag' => 'User tag',
            'user_status' => 'User Account status',
            'date' => 'Current date',
            'time' => 'Current time',
            'day' => 'Current day of the week',
            'month' => 'Current month',
            'days_before_birthday' => 'Days before birthday',
            'listsend_last_sent_to_user' => 'Last sent list to user',
            'listsend_last_opened_by_user' => 'Last opened list by user',
            'listsend_last_clicked_by_user' => 'Last clicked list by user',
        ];
        $log_values = [
            'day' => [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',
            ],
            'month' => [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ],
            'user_status' => get_user_statuses(),
        ];
        $log_bold_start = '<b>';
        $log_bold_end = '</b>';
        $log_rule_separator = ', ';

        if ($json_object->condition == 'AND') {	// Default result for group with operator 'AND':
            $conditions_result = true;
            $stop_result = false;
        } else {	// Default result for group with operator 'OR':
            $conditions_result = false;
            $stop_result = true;
        }

        foreach ($json_object->rules as $r => $rule) {
            if (! $check_result) {
                $log_rule_separator = $r > 0 ? ' ' . $json_object->condition . ' ' : '';
            }

            if ($check_result && $conditions_result == $stop_result) {	// Skip this rule because previous rules already returned the end result for current condition(AND|OR):
                $process_log .= $log_rule_separator . $log_bold_start . 'ignored' . $log_bold_end;
                continue;
            }

            if (isset($rule->rules) && is_array($rule->rules)) {	// This is a group of conditions, Run this function recursively:
                $process_log .= $log_rule_separator;
                $rule_result = $this->check_if_condition_object($rule, $step_User, $process_log);
            } else {	// This is a single field:
                if ($check_result) {
                    $rule_result = $this->check_if_condition_rule($rule, $step_User, $process_log);
                }
                // Log:
                $process_log .= $log_rule_separator . (isset($log_fields[$rule->field]) ? $log_fields[$rule->field] : $rule->field);
                if (in_array($rule->field, ['listsend_last_sent_to_user', 'listsend_last_opened_by_user', 'listsend_last_clicked_by_user'])) {	// Special value for list send fields:
                    $value = explode(':', $rule->value);
                    $period = (isset($value[0]) ? intval($value[0]) : '0')
                        . (isset($value[1]) ? ' ' . $value[1] . 's' : '') . ' ago';
                    $rule_newsletter_ID = isset($value[2]) ? intval($value[2]) : 0;
                    $newsletter = ' for ';
                    if ($rule_newsletter_ID > 0) {	// Specific newsletter is selected:
                        $NewsletterCache = &get_NewsletterCache();
                        if ($rule_Newsletter = &$NewsletterCache->get_by_ID($rule_newsletter_ID, false, false)) {	// Display a name of the selected newsletter:
                            $newsletter .= 'List: ' . $rule_Newsletter->get('name') . '';
                        } else {	// If newsletter was deleted from DB:
                            $newsletter .= 'List: Error: NOT FOUND IN DB!';
                        }
                    } elseif ($rule_newsletter_ID == -1) {	// Any newsletter should be used for this condition rule:
                        $newsletter .= 'any list';
                    } else {	// Any tied newsletter should be used for this condition rule:
                        $newsletter .= 'any list tied to step automation';
                    }
                    $process_log .= ' ' . $log_operators[$rule->operator] . ' "' . $period . $newsletter . '"';
                } elseif (is_array($log_operators[$rule->operator])) {	// Multiple operator and values:
                    foreach ($log_operators[$rule->operator] as $o => $operator) {
                        $process_log .= ' ' . $operator . ' "' . (isset($log_values[$rule->field][$rule->value[$o]]) ? $log_values[$rule->field][$rule->value[$o]] : $rule->value[$o]) . '"';
                    }
                } else {	// Single operator and value:
                    $process_log .= ' ' . $log_operators[$rule->operator] . ' "' . (isset($log_values[$rule->field][$rule->value]) ? $log_values[$rule->field][$rule->value] : $rule->value) . '"';
                }
                if ($check_result) {
                    $process_log .= ': ' . $log_bold_start . ($rule_result ? 'TRUE' : 'FALSE') . $log_bold_end;
                }
            }

            // Append current result with previous results:
            if ($json_object->condition == 'AND') {	// AND condition:
                $conditions_result = $check_result ? ($conditions_result && $rule_result) : true;
            } else {	// OR condition:
                $conditions_result = $check_result ? ($conditions_result || $rule_result) : false;
            }
        }

        // Log:
        $process_log .= ($check_result ? '' : ' ') . ')';
        if ($check_result) {
            $process_log .= ' : ' . $log_bold_start . ($conditions_result ? 'TRUE' : 'FALSE') . $log_bold_end;
        }

        return $conditions_result;
    }

    /**
     * Check rule of "IF Condition" for given User
     *
     * @param object Rule, object with properties: field, value, operator
     * @param object User
     * @return boolean TRUE if condition is matched for given user, otherwise FALSE
     */
    public function check_if_condition_rule($rule, $step_User)
    {
        switch ($rule->field) {
            case 'user_tag':
                // Check if User has a tag:
                $user_tags = $step_User->get_usertags();
                switch ($rule->operator) {
                    case 'equal':
                        return in_array($rule->value, $user_tags);
                    case 'not_equal':
                        return ! in_array($rule->value, $user_tags);
                }
                break;

            case 'user_status':
                // Check User status:
                switch ($rule->operator) {
                    case 'equal':
                        return $step_User->get('status') == $rule->value;
                    case 'not_equal':
                        return $step_User->get('status') != $rule->value;
                }
                break;

            case 'date':
                // Check current date:
                return $this->check_if_condition_rule_date_value($rule, 'Y-m-d');

            case 'time':
                // Check current time:
                return $this->check_if_condition_rule_date_value($rule, 'H:i');

            case 'day':
                // Check current day of week:
                return $this->check_if_condition_rule_date_value($rule, 'w');

            case 'month':
                // Check current month:
                return $this->check_if_condition_rule_date_value($rule, 'm');

            case 'days_before_birthday':
                // Check number of days before birthday:
                global $localtimenow;

                $localdatenow = strtotime(date('Y-m-d', $localtimenow));
                $birthday_month = $step_User->get('birthday_month');
                $birthday_day = $step_User->get('birthday_day');

                if ($birthday_month && $birthday_day) {
                    $birthday = strtotime(date('Y', $localtimenow) . '-' . $birthday_month . '-' . $birthday_day);
                    if ($birthday < $localdatenow) {	// Birthday for current year has already passed, use birthday next year:
                        $birthday = strtotime(((int) date('Y', $localtimenow) + 1) . '-' . $birthday_month . '-' . $birthday_day);
                    }
                    $datediff = $birthday - $localdatenow;
                    $days = (int) round($datediff / (60 * 60 * 24));

                    switch ($rule->operator) {
                        case 'equal':
                            return $days == $rule->value;
                        case 'not_equal':
                            return $days != $rule->value;
                        case 'less':
                            return $days < $rule->value;
                        case 'less_or_equal':
                            return $days <= $rule->value;
                        case 'greater':
                            return $days > $rule->value;
                        case 'greater_or_equal':
                            return $days >= $rule->value;
                        case 'between':
                            return $days >= $rule->value[0] && $days <= $rule->value[1];
                        case 'not_between':
                            return $days < $rule->value[0] || $days > $rule->value[1];
                    }
                }

                return false;

            case 'listsend_last_sent_to_user':
                // Check last sent list to user:
                return $this->check_if_condition_rule_listsend_value($rule, $step_User->ID, 'enls_last_sent_manual_ts') ||
                             $this->check_if_condition_rule_listsend_value($rule, $step_User->ID, 'enls_last_sent_auto_ts');

            case 'listsend_last_opened_by_user':
                // Check last opened list by user:
                return $this->check_if_condition_rule_listsend_value($rule, $step_User->ID, 'enls_last_open_ts');

            case 'listsend_last_clicked_by_user':
                // Check last clicked list by user:
                return $this->check_if_condition_rule_listsend_value($rule, $step_User->ID, 'enls_last_click_ts');
        }

        // Unknown field or operator:
        return false;
    }

    /**
     * Check rule of "IF Condition" for date value
     *
     * @param object Rule, object with properties: field, value, operator
     * @param string Date format like Y-m-d, H:i, w, m
     * @return boolean TRUE if condition is matched for current date, otherwise FALSE
     */
    public function check_if_condition_rule_date_value($rule, $date_format)
    {
        global $localtimenow;

        $date_value = date($date_format, $localtimenow);
        if ($date_format == 'w' && $date_value === '0') {	// Use 7 for Sunday:
            $date_value = '7';
        }

        switch ($rule->operator) {
            case 'equal':
                return $date_value == $rule->value;
            case 'not_equal':
                return $date_value != $rule->value;
            case 'less':
                return $date_value < $rule->value;
            case 'less_or_equal':
                return $date_value <= $rule->value;
            case 'greater':
                return $date_value > $rule->value;
            case 'greater_or_equal':
                return $date_value >= $rule->value;
            case 'between':
                return $date_value >= $rule->value[0] && $date_value <= $rule->value[1];
            case 'not_between':
                return $date_value < $rule->value[0] || $date_value > $rule->value[1];
        }

        return false;
    }

    /**
     * Check rule of "IF Condition" for list send value
     *
     * @param object Rule, object with properties: field, value, operator
     * @param integer Step User ID
     * @param string DB field name for checking: 'enls_last_sent_manual_ts', 'enls_last_open_ts', 'enls_last_click_ts'
     * @return boolean TRUE if condition is matched for current date, otherwise FALSE
     */
    public function check_if_condition_rule_listsend_value($rule, $step_user_ID, $check_db_field_name)
    {
        $value = explode(':', $rule->value);
        $period_value = (isset($value[0]) ? intval($value[0]) : 0);

        if ($period_value > 0) {	// Check this condition only if period > 0 seconds:
            global $DB, $servertimenow;

            // Calculate a time ago depending on period:
            $periods = [
                'second' => 1,        // 1 second
                'minute' => 60,       // 60 seconds
                'hour' => 3600,     // 60 minutes
                'day' => 86400,    // 24 hours
                'month' => 2592000,  // 30 days
                'year' => 31536000, // 365 days
            ];
            $period_name = (isset($value[1]) ? $value[1] : false);
            if ($period_name && isset($periods[$period_name])) {
                $period_value *= $periods[$period_name];
            }
            $rule_value_time = $servertimenow - $period_value;

            $rule_newsletter_ID = (isset($value[2]) ? intval($value[2]) : 0);
            if ($rule_newsletter_ID > 0) {	// Check for a selected list:
                $NewsletterCache = &get_NewsletterCache();
                if ($rule_Newsletter = &$NewsletterCache->get_by_ID($rule_newsletter_ID, false, false)) {	// Check only for a selected list:
                    $rule_newsletters = $rule_Newsletter->ID;
                } else {	// If a selected list has been removed from DB:
                    $rule_newsletters = -1;
                }
            } elseif ($rule_newsletter_ID == -1) {	// Check for ALL lists:
                $rule_newsletters = false;
            } else {	// Check any list tied to step automation:
                $step_Automation = &$this->get_Automation();
                $rule_newsletters = $step_Automation->get_newsletter_IDs();
            }

            $SQL = new SQL('Get last time for IF Condition "Last sent/opened/clicked list" (' . $check_db_field_name . ')');
            $SQL->SELECT($check_db_field_name);
            $SQL->FROM('T_email__newsletter_subscription');
            $SQL->WHERE('enls_user_ID = ' . $DB->quote($step_user_ID));
            if ($rule_newsletters !== false) {	// Check only for the selected rule lists:
                $SQL->WHERE_and('enls_enlt_ID IN ( ' . $DB->quote($rule_newsletters) . ' )');
            }
            $SQL->ORDER_BY($check_db_field_name);
            $SQL->LIMIT(1);
            $last_time = strtotime($DB->get_var($SQL));

            switch ($rule->operator) {
                case 'less':
                    return $rule_value_time < $last_time;
                case 'less_or_equal':
                    return $rule_value_time <= $last_time;
                case 'greater':
                    return $rule_value_time > $last_time;
                case 'greater_or_equal':
                    return $rule_value_time >= $last_time;
            }
        } else {	// No reason to check if period is 0 seconds:
            return true;
        }
    }

    /**
     * Set label generated automatically
     *
     * @param string Label
     */
    public function set_label()
    {
        $label = '';

        switch ($this->get('type')) {
            case 'if_condition':
                // Get log of conditions without results:
                $this->check_if_condition(null, $label);
                break;

            case 'send_campaign':
                $EmailCampaignCache = &get_EmailCampaignCache();
                if ($EmailCampaign = &$EmailCampaignCache->get_by_ID($this->get('info'), false, false)) {	// Use name of Email Campaign:
                    $label = $EmailCampaign->get('name');
                }
                break;

            case 'notify_owner':
                if (($step_Automation = &$this->get_Automation()) &&
                    ($automation_owner_User = &$step_Automation->get_owner_User())) {	// User login of owner:
                    $label = $automation_owner_User->get('login');
                }
                break;

            case 'subscribe':
            case 'unsubscribe':
                $NewsletterCache = &get_NewsletterCache();
                if ($Newsletter = &$NewsletterCache->get_by_ID($this->get('info'), false, false)) {	// Use name of Newsletter/List:
                    $label = $Newsletter->get('name');
                }
                break;

            case 'start_automation':
                $AutomationCache = &get_AutomationCache();
                if ($Automation = &$AutomationCache->get_by_ID($this->get('info'), false, false)) {	// Use name of Automation:
                    $label = $Automation->get('name');
                }
                break;

            case 'user_status':
                $user_statuses = get_user_statuses();
                if (isset($user_statuses[$this->get('info')])) {	// Get status title from status key:
                    $label = $user_statuses[$this->get('info')];
                }
                break;

            case 'add_usertag':
            case 'remove_usertag':
            default:
                $label = $this->get('info');
                break;
        }

        $this->set('label', utf8_substr(utf8_trim($label), 0, 500));
    }

    /**
     * Check if this automation step can be modified(added/edited/deleted) currently
     *
     * @param boolean
     */
    public function can_be_modified()
    {
        if (($step_Automation = &$this->get_Automation()) &&
            $step_Automation->get('status') == 'paused') {	// Automation of this step must be paused in order to edit steps:
            return true;
        }

        return false;
    }

    /**
     * Pause automation by confirmation from request
     *
     * @return boolean
     */
    public function pause_automation()
    {
        if ($this->can_be_modified()) {	// If step automation is already paused
            return true;
        }

        if (! param('confirm_pause', 'integer')) {	// If action is not confirmed
            return false;
        }

        // Try to pause the step's automation:
        $step_Automation = &$this->get_Automation();
        $step_Automation->set('status', 'paused');

        if ($step_Automation->dbupdate()) {	// Display a message if automation has been paused:
            global $Messages;
            $Messages->add(TB_('Automation has been paused.'), 'success');
            return true;
        } else {	// If automation could not paused
            return false;
        }
    }
}
