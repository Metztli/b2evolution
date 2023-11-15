<?php
/**
 * This file implements the LinkMessage class, which is a wrapper class for Message class to handle linked files.
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
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

/**
 * LinkMessage Class
 *
 * @package evocore
 */
class LinkMessage extends LinkOwner
{
    /**
     * @var Message
     */
    public $Message;

    /**
     * Constructor
     *
     * @param object Message
     * @param integer ID of temporary object from table T_temporary_ID (used for uploads on new messages)
     */
    public function __construct($Message, $tmp_ID = null)
    {
        // call parent contsructor
        parent::__construct($Message, 'message', 'msg_ID', $tmp_ID);
        $this->Message = &$this->link_Object;

        $this->_trans = [
            'Link this image to your xxx' => NT_('Link this image to your message.'),
            'Link this file to your xxx' => NT_('Link this file to your message.'),
            'The file will be linked for download at the end of the xxx' => NT_('The file will be appended for linked at the end of the message.'),
            'Insert the following code snippet into your xxx' => NT_('Insert the following code snippet into your message.'),
            'View this xxx...' => NT_('View this message...'),
            'Edit this xxx...' => NT_('Edit this message...'),
            'Link files to current xxx' => NT_('Link files to current message'),
            'Selected files have been linked to xxx.' => NT_('Selected files have been linked to message.'),
            'Link has been deleted from $xxx$.' => NT_('Link has been deleted from message.'),
            'Cannot delete Link from $xxx$.' => NT_('Cannot delete Link from message.'),
        ];
    }

    /**
     * Check current User has an access to work with attachments of the link Message
     *
     * @param string Permission level
     * @param boolean TRUE to assert if user dosn't have the required permission
     * @param object File Root to check permission to add/upload new files
     * @return boolean
     */
    public function check_perm($permlevel, $assert = false, $FileRoot = null)
    {
        if (! is_logged_in()) {	// User must be logged in:
            if ($assert) {	// Halt the denied access:
                debug_die('You have no permission for message attachments!');
            }
            return false;
        }

        if ($permlevel == 'add') {	// Check permission to add/upload new files:
            return check_user_perm('files', $permlevel, $assert, $FileRoot);
        }

        return check_user_perm('perm_messaging', 'reply', $assert);
    }

    /**
     * Get all positions ( key, display ) pairs where link can be displayed
     *
     * @param integer File ID
     * @return array
     */
    public function get_positions($file_ID = null)
    {
        return [
            'inline' => T_('Inline'),
        ];
    }

    /**
     * Get default position for a new link
     *
     * @param integer File ID
     * @return string Position
     */
    public function get_default_position($file_ID)
    {
        return 'inline';
    }

    /**
     * Load all links of owner Message if it was not loaded yet
     */
    public function load_Links()
    {
        if (is_null($this->Links)) {	// Links have not been loaded yet:
            $LinkCache = &get_LinkCache();
            if ($this->is_temp()) {
                $this->Links = $LinkCache->get_by_temporary_ID($this->get_ID());
            } else {
                $this->Links = $LinkCache->get_by_message_ID($this->get_ID());
            }
        }
    }

    /**
     * Add new link to owner Message
     *
     * @param integer file ID
     * @param integer link position 'inline'
     * @param int order of the link
     * @param boolean true to update owner last touched timestamp after link was created, false otherwise
     * @return integer|boolean Link ID on success, false otherwise
     */
    public function add_link($file_ID, $position = null, $order = 1, $update_owner = true)
    {
        if (is_null($position)) {	// Use default link position:
            $position = $this->get_default_position($file_ID);
        }

        $edited_Link = new Link();
        $edited_Link->set($this->get_ID_field_name(), $this->get_ID());
        $edited_Link->set('file_ID', $file_ID);
        $edited_Link->set('position', $position);
        $edited_Link->set('order', $order);
        if ($edited_Link->dbinsert()) {
            $FileCache = &get_FileCache();
            $File = $FileCache->get_by_ID($file_ID, false, false);
            $file_name = empty($File) ? '' : $File->get_name();
            $file_dir = $File->dir_or_file('Directory', 'File');
            syslog_insert(sprintf('%s %s was linked to %s with ID=%s', $file_dir, '[[' . $file_name . ']]', $this->type, $this->get_ID()), 'info', 'file', $file_ID);

            // Reset the Links:
            $this->Links = null;
            $this->load_Links();

            return $edited_Link->ID;
        }

        return false;
    }

    public function load_Blog()
    {
        // Message has no collection
    }

    /**
     * Get Message parameter
     *
     * @param string parameter name to get
     */
    public function get($parname)
    {
        switch ($parname) {
            case 'name':
                return 'message';
        }
        return parent::get($parname);
    }

    /**
     * Get Message edit url
     *
     * @param string Delimiter to use for multiple params (typically '&amp;' or '&')
     * @param string URL type: 'frontoffice', 'backoffice'
     * @return string URL
     */
    public function get_edit_url($glue = '&amp;', $url_type = null)
    {
        return $this->get_view_url($glue, $url_type);
    }

    /**
     * Get Message view url
     *
     * @param string Delimiter to use for multiple params (typically '&amp;' or '&')
     * @param string URL type: 'frontoffice', 'backoffice'
     * @return string URL
     */
    public function get_view_url($glue = '&amp;', $url_type = null)
    {
        global $admin_url;

        $view_url = '';
        if (! empty($this->Message) && ($this->Message instanceof Message) && $Thread = &$this->Message->get_Thread()) {
            $view_url = $admin_url . '?ctrl=messages' . $glue . 'thrd_ID=' . $Thread->ID;
        }

        return $view_url;
    }
}
