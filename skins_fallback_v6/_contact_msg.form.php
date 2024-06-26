<?php
/**
 * This is the template that displays the email message form
 *
 * This file is not meant to be called directly.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}

global $dummy_fields, $Plugins;

// Default params:
$default_params = [
    'skin_form_params' => [],
    'skin_form_before' => '',
    'skin_form_after' => '',
    'msgform_form_title' => '',
];

if (isset($params)) {	// Merge with default params
    $params = array_merge($default_params, $params);
} else {	// Use a default params
    $params = $default_params;
}


$submit_url = get_htsrv_url() . 'message_send.php';

if (($unsaved_message_params = get_message_params_from_session()) == null) { // set message default to empty string
    $message = '';
    $user_fields = [];
} else { // set saved message params
    $subject = $unsaved_message_params['subject'];
    $subject_other = $unsaved_message_params['subject_other'];
    $message = $unsaved_message_params['message'];
    $contact_method = $unsaved_message_params['contact_method'];
    $user_fields = $unsaved_message_params['user_fields'];
    $email_author = $unsaved_message_params['sender_name'];
    $email_author_address = $unsaved_message_params['sender_address'];
}

echo str_replace('$form_title$', $params['msgform_form_title'], $params['skin_form_before']);

$Form = new Form($submit_url);

$Form->switch_template_parts($params['skin_form_params']);

$Form->begin_form('evo_form');

$Form->add_crumb('newmessage');
if (isset($Blog)) {
    $Form->hidden('blog', $Blog->ID);
}
$Form->hidden('recipient_id', $recipient_id);
$Form->hidden('post_id', $post_id);
$Form->hidden('comment_id', $comment_id);
$Form->hidden('redirect_to', url_rel_to_same_host($redirect_to, get_htsrv_url()));

if ($Blog->get_setting('msgform_display_recipient')) {	// Display recipient:
    $recipient_label = utf8_trim($Blog->get_setting('msgform_recipient_label'));
    $Form->info_field((empty($recipient_label) ? T_('Message to') : $recipient_label), $Blog->get_msgform_recipient_link(), [
        'class' => 'evo_msgform_recipient',
    ]);
}

if (is_logged_in() &&
    ! empty($recipient_User) &&
    $recipient_User->get_msgform_possibility() == 'email') {	// Display email address of current User if recipient User can recieves messages only by email:
    $Form->info(T_('Reply to'), $current_User->get('email'));
}

if (is_logged_in()) {	// Name fields for current logged in user:
    $edited_user_perms = ['edited-user', 'edited-user-required'];
    switch ($Blog->get_setting('msgform_user_name')) {
        case 'fullname':
            $firstname_editing = $Settings->get('firstname_editing');
            if (in_array($firstname_editing, $edited_user_perms)) {	// First name:
                $Form->text_input('user_firstname', $current_User->get('firstname'), 20, T_('First name'), '', [
                    'maxlength' => 50,
                    'required' => ($firstname_editing == 'edited-user-required'),
                ]);
            }

            $lastname_editing = $Settings->get('lastname_editing');
            if (in_array($lastname_editing, $edited_user_perms)) {	// Last name:
                $Form->text_input('user_lastname', $current_User->get('lastname'), 20, T_('Last name'), '', [
                    'maxlength' => 50,
                    'required' => ($lastname_editing == 'edited-user-required'),
                ]);
            }
            break;

        case 'nickname':
            $nickname_editing = $Settings->get('nickname_editing');
            if (in_array($nickname_editing, $edited_user_perms)) {	// Nickname:
                $Form->text_input('user_nickname', $current_User->nickname, 20, T_('Nickname'), '', [
                    'maxlength' => 50,
                    'required' => ($nickname_editing == 'edited-user-required'),
                ]);
            }
            break;
    }
} else {	// Name and Email fields for anonymous user:
    // Note: we use funky field names in order to defeat the most basic guestbook spam bots:
    $Form->text_input($dummy_fields['name'], $email_author, 40, T_('From'), T_('Your name.'), [
        'maxlength' => 50,
        'class' => 'wide_input',
        'required' => $Blog->get_setting('msgform_require_name'),
    ]);
    $Form->email_input($dummy_fields['email'], $email_author_address, 40, T_('Email'), [
        'maxlength' => 150,
        'class' => 'wide_input',
        'required' => true,
        'note' => T_('Your email address'),
        'bottom_note' => T_('Your email address will <strong>not</strong> be displayed on this site BUT it will be sent to the person you are contacting, otherwise they would not be able to reply to you.'),
    ]);
}

if ($Blog->get_setting('msgform_display_subject')) {	// Display a field to enter or select a subject:
    $subject_options = $Blog->get_setting('msgform_subject_options');
    if (empty($subject_options)) {	// Display only a text input field for subject:
        $Form->text_input($dummy_fields['subject'], $subject, 255, T_('Subject'), '', [
            'maxlength' => 255,
            'required' => $Blog->get_setting('msgform_require_subject'),
        ]);
    } else {	// Display a select with text input field for subject:
        $subject_options = array_merge([
            '' => '---',
        ], $subject_options, [
            '_other' => T_('Other') . ':',
        ]);
        $Form->begin_line(T_('Subject'), null, '', [
            'required' => $Blog->get_setting('msgform_require_subject'),
        ]);
        $Form->select_input_array($dummy_fields['subject'], $subject, $subject_options, '');
        $Form->text_input($dummy_fields['subject'] . '_other', $subject_other, 50, '', '', [
            'maxlength' => 255,
            'style' => 'margin-left: 5px; display: none;',
        ]);
        $Form->end_line();
        ?>
			<script>
			jQuery( document ).ready( function() {
				var select_field = jQuery( '#<?php echo $dummy_fields['subject']; ?>' );
				var other_field = jQuery( '#<?php echo $dummy_fields['subject']; ?>_other' );

				function onSubjectChange( obj )
				{
					if( select_field.val() == '_other' )
					{
						other_field.show();
					}
					else
					{
						other_field.hide();
						other_field.val( null );
					}
				}

				onSubjectChange();
				select_field.on( 'change', onSubjectChange );
			} );
			</script>
			<?php
    }
}

// Display additional user fields:
$Blog->display_msgform_additional_fields($Form, $user_fields);

if ($Blog->get_setting('msgform_contact_method')) {	// Display a field to select a preferred contact method:
    $msgform_contact_methods = get_msgform_contact_methods(isset($recipient_User) ? $recipient_User : null);
    if (count($msgform_contact_methods) > 1) {	// Only when at least two methods are allowed:
        $Form->select_input_array('contact_method', $contact_method, $msgform_contact_methods, T_('Reply method'), T_('How would you prefer to receive a reply?'), [
            'force_keys_as_values' => true,
        ]);
    }
}

// Display plugin captcha for message form before textarea:
$Plugins->display_captcha([
    'Form' => &$Form,
    'form_type' => 'message',
    'form_position' => 'before_textarea',
    'form_use_fieldset' => false,
]);

if ($Blog->get_setting('msgform_display_message')) {	// Display a field to enter a message:
    $message_label = utf8_trim($Blog->get_setting('msgform_message_label'));
    $Form->textarea(
        $dummy_fields['content'],
        $message,
        15,
        ((empty($message_label) ? T_('Message') : $message_label)),
        T_('Plain text only.'),
        35,
        'wide_textarea',
        $Blog->get_setting('msgform_require_message')
    );
}

$Plugins->trigger_event('DisplayMessageFormFieldset', [
    'Form' => &$Form,
    'recipient_ID' => &$recipient_id,
    'item_ID' => $post_id,
    'comment_ID' => $comment_id,
    'form_use_fieldset' => false,
]);

// Display plugin captcha for message form before submit button:
$Plugins->display_captcha([
    'Form' => &$Form,
    'form_type' => 'message',
    'form_position' => 'before_submit_button',
    'form_use_fieldset' => false,
]);

// Form buttons:
echo $Form->begin_field(null, '');

// Standard button to send a message
$Form->button_input([
    'name' => 'submit_message_' . $recipient_id,
    'class' => 'submit btn-primary btn-lg',
    'value' => T_('Send message'),
]);

// Additional buttons from plugins
$Plugins->trigger_event('DisplayMessageFormButton', [
    'Form' => &$Form,
    'recipient_ID' => &$recipient_id,
    'item_ID' => $post_id,
    'comment_ID' => $comment_id,
]);

echo $Form->end_field();

$Form->end_form();

echo $params['skin_form_after'];

?>