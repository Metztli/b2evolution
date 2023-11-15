<?php
/**
 * This file implements the email log class.
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


/**
 * Email Address Class
 *
 * @package evocore
 */
class EmailLog extends DataObject
{
    public $key;

    public $timestamp;

    public $user_ID;

    public $to;

    public $result;

    public $subject;

    public $headers;

    public $message;

    public $last_open_ts;

    public $last_click_ts;

    public $camp_ID; // Used to reference the campaign when there is no associated campaign_send or the previously associated campaign updated its csnd_emlog_ID

    public $autm_ID;

    /**
     * Constructor
     *
     * @param object table Database row
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct('T_email__log', 'emlog_', 'emlog_ID');

        if ($db_row != null) {
            $this->ID = $db_row->emlog_ID;
            $this->key = $db_row->emlog_key;
            $this->timestamp = $db_row->emlog_timestamp;
            $this->user_ID = $db_row->emlog_user_ID;
            $this->to = $db_row->emlog_to;
            $this->result = $db_row->emlog_result; // Result type: 'ok', 'error', 'blocked', 'simulated', 'ready_to_send'
            $this->subject = $db_row->emlog_subject;
            $this->headers = $db_row->emlog_headers;
            $this->message = $db_row->emlog_message;
            $this->last_open_ts = $db_row->emlog_last_open_ts;
            $this->last_click_ts = $db_row->emlog_last_click_ts;
            $this->camp_ID = $db_row->emlog_camp_ID;
            $this->autm_ID = $db_row->emlog_autm_ID;
        }
    }
}
