<?php
/**
 * Backup - This is a LINEAR controller
 *
 * This file is part of b2evolution - {@link http://b2evolution.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2016 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @package maintenance
 */

if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


// Check minimum permission:
check_user_perm('admin', 'normal', true);
check_user_perm('maintenance', 'backup', true);

// Load Backup class (PHP4):
load_class('maintenance/model/_backup.class.php', 'Backup');

switch ($action) {
    case 'delete':
        // Delete backup folder:

        // Check that this action request is not a CSRF hacked request:
        $Session->assert_received_crumb('backup');

        $folder = param('folder', 'string');

        if (strpos($folder, '/') !== false || strpos($folder, '\\') !== false) {	// Don't support slash chars in the folder name to avoid a hack:
            debug_die('Wrong folder name "' . $folder . '"!');
        }

        $deleting_folder_path = $backup_path . $folder;

        if (! file_exists($deleting_folder_path) || ! is_dir($deleting_folder_path)) {	// Display error message if the requested folder doesn't exist:
            $Messages->add(sprintf(TB_('The directory &laquo;%s&raquo; does not exist.'), '<code>' . $deleting_folder_path . '</code>'), 'error');
        } elseif (rmdir_r($deleting_folder_path)) {	// Display a message after successful deleting:
            $Messages->add(sprintf(TB_('The directory &laquo;%s&raquo; has been deleted.'), '<code>' . $deleting_folder_path . '</code>'), 'success');
        } else {	// Display error message if the requested folder could not be deleted:
            $Messages->add(sprintf(TB_('Could not delete directory: %s'), '<code>' . $deleting_folder_path . '</code>'), 'error');
        }

        header_redirect($admin_url . '?ctrl=backup');
        break;
}

// Set options path:
$AdminUI->set_path('options', 'misc', 'backup');

// Get action parameter from request:
param_action('start');

// Create instance of Backup class
$current_Backup = new Backup();

// Load backup settings from request
if ($action == 'backup' && ! $current_Backup->load_from_Request()) {
    $action = 'new';
}


$AdminUI->breadcrumbpath_init(false);  // fp> I'm playing with the idea of keeping the current blog in the path here...
$AdminUI->breadcrumbpath_add(TB_('System'), $admin_url . '?ctrl=system');
$AdminUI->breadcrumbpath_add(TB_('Maintenance'), $admin_url . '?ctrl=tools');
$AdminUI->breadcrumbpath_add(TB_('Backup'), $admin_url . '?ctrl=backup');

// Set an url for manual page:
$AdminUI->set_page_manual_link('backup-tab');

// Display <html><head>...</head> section! (Note: should be done early if actions do not redirect)
$AdminUI->disp_html_head();

// Display title, menu, messages, etc. (Note: messages MUST be displayed AFTER the actions)
$AdminUI->disp_body_top();

$AdminUI->disp_payload_begin();

/**
 * Display payload:
 */
switch ($action) {
    case 'start':
        // Display backup settings form
        $AdminUI->disp_view('maintenance/views/_backup.form.php');
        break;

    case 'backup':
        if ($demo_mode) {
            $Messages->clear();
            $Messages->add(TB_('This feature is disabled on the demo server.'), 'error');
            $Messages->display();
            break;
        }

        // Check that this action request is not a CSRF hacked request:
        $Session->assert_received_crumb('backup');

        $Form = new Form(null, 'backup_progress', 'post');

        // Interactive / flush() backup should start here
        $Form->begin_form('fform', TB_('System backup is in progress...'));

        evo_flush();

        // Lock b2evolution while backing up
        $success = true;
        $lock_type = param('bk_lock_type', 'string');
        switch ($lock_type) {
            case 'maintenance_lock':
                // Enable maintenance lock
                $success = switch_maintenance_lock(true);
                // Make sure we disable the maintenance lock if PHP dies
                register_shutdown_function('switch_maintenance_lock', false);
                break;

            case 'maintenance_mode':
                // Enable maintenance mode
                $success = switch_maintenance_mode(true, 'all', TB_('System backup is in progress. Please reload this page in a few minutes.'));
                // Make sure we exit the maintenance mode if PHP dies
                register_shutdown_function('switch_maintenance_mode', false, '', true);
                break;

            case 'open': // Don't lock the site
                break;

            default:
                debug_die('Invalid system lock type received!');
                break;
        }

        if ($success) {	// We can start backup
            set_max_execution_time(1800); // 30 minutes
            $current_Backup->start_backup();
        }

        // Unlock b2evolution
        switch ($lock_type) {
            case 'maintenance_lock': // Disable maintenance lock
                switch_maintenance_lock(false);
                break;

            case 'maintenance_mode': // Disable maintenance mode
                switch_maintenance_mode(false, 'all');
                break;

            default: // Nothing to do because the b2evoltuion was not locked
                break;
        }

        $Form->end_form();
        break;
}

$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();
