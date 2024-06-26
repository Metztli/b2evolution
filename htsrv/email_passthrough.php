<?php
/**
 * This is the handler for email interaction tracking
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

/**
 * Initialize everything:
 */
require_once dirname(__FILE__) . '/../conf/_config.php';

require_once $inc_path . '_main.inc.php';

global $DB, $Session, $modules, $Messages;

load_funcs('email_campaigns/model/_emailcampaign.funcs.php');

param('type', 'string', true);
param('email_ID', 'integer', true);
param('email_key', 'string', true);
param('tag', 'integer', null);
param('redirect_to', 'url', '');

// erhsatingin > Is this acceptable? This seems like an ugly hack...
$redirect_to = str_replace('&amp;', '&', $redirect_to);

switch ($type) {
    case 'link':
        $email_log = $DB->get_row('SELECT * FROM T_email__log WHERE emlog_ID = ' . $DB->quote($email_ID) . ' AND emlog_key = ' . $DB->quote($email_key), ARRAY_A);

        if ($email_log) {
            $skip_click_tracking = false;
            $update_values = [];
            if (! empty($email_log['emlog_user_ID'])) {
                $ecmp_ID = $DB->get_var('SELECT csnd_camp_ID FROM T_email__campaign_send WHERE csnd_emlog_ID = ' . $DB->quote($email_ID));
                $EmailCampaignCache = &get_EmailCampaignCache();
                if (! empty($ecmp_ID) && $edited_EmailCampaign = &$EmailCampaignCache->get_by_ID($ecmp_ID, false)) {
                    $UserCache = &get_UserCache();
                    if ($email_User = &$UserCache->get_by_ID($email_log['emlog_user_ID'])) {
                        // Check if the mail is not yet opened
                        $unopened_mail = is_unopened_campaign_mail($email_ID, $send_data);
                        switch ($tag) {
                            case 1: // Add usertag
                                $assigned_user_tag = $edited_EmailCampaign->get('user_tag');
                                if (! empty($assigned_user_tag)) {
                                    $email_User->add_usertags($assigned_user_tag);
                                    $email_User->dbupdate();
                                }
                                break;

                            case 2: // Update clicked_unsubscribe
                            case 9: // Unsubscribe button
                                $result = $DB->query('UPDATE T_email__campaign_send
										SET csnd_clicked_unsubscribe = 1
										WHERE csnd_camp_ID = ' . $DB->quote($ecmp_ID) . ' AND csnd_user_ID = ' . $DB->quote($email_User->ID));

                                if ($result) {
                                    $update_values[] = 'ecmp_unsub_clicks = ecmp_unsub_clicks + 1';
                                }

                                // Add campaign ID as param to unsubscribe link:
                                if ($redirect_to && ! empty($email_log['emlog_camp_ID'])) {
                                    $redirect_to = url_add_param($redirect_to, [
                                        'ecmp_ID' => $email_log['emlog_camp_ID'],
                                    ], '&');
                                }

                                // Do not track click
                                $skip_click_tracking = true;
                                break;

                            case 3: // Vote like and add appropriate usertag
                                $result = $DB->query('UPDATE T_email__campaign_send
										SET csnd_like = 1
										WHERE csnd_camp_ID = ' . $DB->quote($ecmp_ID) . ' AND csnd_user_ID = ' . $DB->quote($email_User->ID) . ' AND ( csnd_like IS NULL OR csnd_like = -1 )');

                                if ($result) {
                                    $update_values[] = 'ecmp_like_count = ecmp_like_count + 1';
                                    if ($send_data['csnd_like'] == '-1') { // email previously disliked, we need to decrease the dislike count
                                        $update_values[] = 'ecmp_dislike_count = ecmp_dislike_count - 1';
                                    }
                                }

                                // Add tag for like and for "clicked content"
                                $assigned_user_tag = implode(',', [$edited_EmailCampaign->get('user_tag_like'), $edited_EmailCampaign->get('user_tag')]);
                                if (! empty($assigned_user_tag)) {
                                    $email_User->add_usertags($assigned_user_tag);
                                    $email_User->dbupdate();
                                }

                                // Add user to automation if it is defined in email campaign:
                                $edited_EmailCampaign->add_user_to_automation('like', $email_User->ID);

                                $Messages->add(T_('Your vote has been recorded, thank you!'), 'success');
                                break;

                            case 4: // Vote dislike and add appropriate usertag
                                $result = $DB->query('UPDATE T_email__campaign_send
										SET csnd_like = -1
										WHERE csnd_camp_ID = ' . $DB->quote($ecmp_ID) . ' AND csnd_user_ID = ' . $DB->quote($email_User->ID) . ' AND ( csnd_like IS NULL OR csnd_like = 1 )');

                                if ($result) {
                                    $update_values[] = 'ecmp_dislike_count = ecmp_dislike_count + 1';
                                    if ($send_data['csnd_like'] == '1') { // email previously liked, we need to decrease the like count
                                        $update_values[] = 'ecmp_like_count = ecmp_like_count - 1';
                                    }
                                }

                                // Add tag for dislike only
                                $assigned_user_tag = $edited_EmailCampaign->get('user_tag_dislike');
                                if (! empty($assigned_user_tag)) {
                                    $email_User->add_usertags($assigned_user_tag);
                                    $email_User->dbupdate();
                                }
                                // Do not track click
                                $skip_click_tracking = true;

                                // Add user to automation if it is defined in email campaign:
                                $edited_EmailCampaign->add_user_to_automation('dislike', $email_User->ID);

                                $Messages->add(T_('Your vote has been recorded, thank you!'), 'success');
                                break;

                            case 5: // Call to Action 1
                            case 6: // Call to Action 2
                            case 7: // Call to Action 3
                                $cta_num = (int) $tag - 4;

                                $result = $DB->query('UPDATE T_email__campaign_send
										SET csnd_cta' . $cta_num . ' = 1
										WHERE csnd_camp_ID = ' . $DB->quote($ecmp_ID) . ' AND csnd_user_ID = ' . $DB->quote($email_User->ID) . ' AND csnd_cta' . $cta_num . ' IS NULL');

                                if ($result) {
                                    $update_values[] = 'ecmp_cta' . $cta_num . '_clicks = ecmp_cta' . $cta_num . '_clicks + 1';
                                }

                                // Assign tag for CTA and for "clicked content"
                                $assigned_user_tag = implode(',', [$edited_EmailCampaign->get('user_tag_cta' . $cta_num), $edited_EmailCampaign->get('user_tag')]);
                                if (! empty($assigned_user_tag)) {
                                    $email_User->add_usertags($assigned_user_tag);
                                    $email_User->dbupdate();
                                }

                                // Add user to automation if it is defined in email campaign:
                                $edited_EmailCampaign->add_user_to_automation('cta' . $cta_num, $email_User->ID);
                                break;

                            case 8: // Activate account button
                                // Add tag for activate only
                                $assigned_user_tag = $edited_EmailCampaign->get('user_tag_activate');
                                if (! empty($assigned_user_tag)) {
                                    $email_User->add_usertags($assigned_user_tag);
                                    $email_User->dbupdate();
                                }

                                // Add user to automation if it is defined in email campaign:
                                $edited_EmailCampaign->add_user_to_automation('activate', $email_User->ID);
                                break;
                        }

                        // We are not using header_redirect below so we need to transfer Messages to the next page:
                        if ($Messages->count()) {	// Set Messages into user's session, so they get restored on the next page (after redirect):
                            $Session->set('Messages', $Messages);
                        }

                        $Session->dbsave();
                    }
                }
            }

            if (! empty($update_values)) { // Set campaign counters
                $DB->query('UPDATE T_email__campaign SET ' . implode(',', $update_values) .
                        ($unopened_mail ? ', ecmp_open_count = ecmp_open_count + 1' : '') . // unopened mail, increment open count
                        ' WHERE ecmp_ID = ' . $DB->quote($ecmp_ID));
            }

            if (! $skip_click_tracking) { // Update last click time for current email log and related tables like email campaign and newsletters:
                update_mail_log_time('click', $email_ID, $email_key);
            }
        }

        // Use message of already loaded email log above, otherwise set empty string in order to don't execute SQL query twice:
        $email_log_message = (isset($email_log['emlog_message']) ? $email_log['emlog_message'] : '');
        header_redirect_from_email($redirect_to, 303, $email_log_message);
        // We have EXITed already at this point!!
        break;

    case 'img':
        // Update last open time for current email log and related tables like email campaign and newsletters:
        update_mail_log_time('open', $email_ID, $email_key);

        if (! empty($redirect_to)) {	// Do redirect only when URL is provided:
            header_redirect_from_email($redirect_to, 302, null, $email_ID, $email_key);
            // We have EXITed already at this point!!
        }
        break;

    default:
        debug_die('Invalid email tracking type');
}
