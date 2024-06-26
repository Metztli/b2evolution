<?php
/**
 * This file display the poll form
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2020 by Francois Planque - {@link http://fplanque.com/}.
 * Parts of this file are copyright (c)2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package admin
 */
if (! defined('EVO_MAIN_INIT')) {
    die('Please, do not access this page directly.');
}


global $edited_Poll, $action, $admin_url;

// Get permission of current user if he can edit the edited Poll:
$perm_poll_edit = check_user_perm('polls', 'edit', false, $edited_Poll);

// Determine if we are creating or updating:
$creating = is_create_action($action);

$Form = new Form(null, 'poll_checkchanges', 'post', 'compact');

$Form->global_icon(TB_('Cancel editing') . '!', 'close', regenerate_url('action,pqst_ID'));

$Form->begin_form('fform', ($creating ? TB_('New poll') : TB_('Poll')) . get_manual_link('poll-form'));

$Form->add_crumb('poll');
$Form->hidden('action', $creating ? 'create' : 'update');
$Form->hiddens_by_key(get_memorized('action' . ($creating ? ',pqst_ID' : '')));

if (check_user_perm('polls', 'edit')) {	// Allow to change an owner if current user has a permission to edit all polls:
    $Form->username('pqst_owner_login', $edited_Poll->get_owner_User(), TB_('Owner'), '', '', [
        'required' => true,
    ]);
} else {	// Current user has no permission to edit a poll owner, Display the owner as info field:
    $Form->info(TB_('Owner'), get_user_identity_link(null, $edited_Poll->owner_user_ID));
}

if ($perm_poll_edit) {
    $Form->text_input('pqst_question_text', $edited_Poll->get('question_text'), 10, TB_('Question'), '', [
        'maxlength' => 2000,
        'required' => true,
        'class' => 'large',
    ]);
} else {
    $Form->info(TB_('Question'), $edited_Poll->get('question_text'));
}

if ($perm_poll_edit) {
    $Form->select_input_array('pqst_max_answers', $edited_Poll->get('max_answers'), range(1, 10), TB_('Allowed answers per user'));
}

if ($creating) {	// Suggest to enter 10 answer options on creating new poll:
    $answer_options = param('answer_options', 'array:string', []);
    for ($i = 0; $i < 10; $i++) {
        $Form->text_input('answer_options[]', (isset($answer_options[$i]) ? $answer_options[$i] : ''), 10, ($i == 0 ? TB_('Answer options') : ''), '', [
            'maxlength' => 2000,
            'style' => 'width:50%',
        ]);
    }
}

$buttons = [];
if ($creating || $perm_poll_edit) {	// Display a button to update the poll question only if current user has a permission:
    $buttons[] = ['submit', 'submit', ($creating ? TB_('Record') : TB_('Save Changes!')), 'SaveButton'];
}

$Form->end_form($buttons);

// ---- Poll Answers - START ---- //
if ($edited_Poll->ID > 0) {	// Display the answers table only when poll question already exists in the DB:
    // Get numbers of votes and voters for the edited poll:
    $poll_vote_nums = $edited_Poll->get_vote_nums();

    // Get all options of the edited poll:
    $SQL = new SQL();
    $SQL->SELECT('popt_ID, popt_pqst_ID, popt_option_text, popt_order,');
    $SQL->SELECT_add('COUNT( pans_pqst_ID ) AS answers_count,');
    $SQL->SELECT_add('ROUND( COUNT( pans_pqst_ID ) / ' . ($poll_vote_nums['voters'] == 0 ? 1 : $poll_vote_nums['voters']) . ' * 100 ) AS answers_percent');
    $SQL->FROM('T_polls__option');
    $SQL->FROM_add('LEFT JOIN T_polls__answer ON pans_popt_ID = popt_ID');
    $SQL->WHERE('popt_pqst_ID = ' . $edited_Poll->ID);
    $SQL->GROUP_BY('popt_ID');

    // Get a count of all options for the edited poll:
    $count_SQL = new SQL();
    $count_SQL->SELECT('COUNT( popt_ID )');
    $count_SQL->FROM('T_polls__option');
    $count_SQL->WHERE('popt_pqst_ID = ' . $edited_Poll->ID);

    // Create result set:
    $Results = new Results($SQL->get(), 'pans_', 'A', null, $count_SQL->get());

    $Results->title = sprintf(TB_('%d votes from %d users on %d possible answers'), $poll_vote_nums['votes'], $poll_vote_nums['voters'], $Results->get_total_rows()) . get_manual_link('polls-answers-list');
    $Results->Cache = get_PollOptionCache();

    $Results->cols[] = [
        'th' => TB_('Order'),
        'th_class' => 'shrinkwrap',
        'td_class' => 'shrinkwrap',
        'order' => 'popt_order',
        'td' => '$popt_order$',
    ];

    /**
     * Get the Poll question as text or as link if current user has a perm to view it
     *
     * @param object Poll
     * @return string
     */
    function poll_option_td_option($PollOption)
    {
        global $edited_Poll, $admin_url;

        $r = $PollOption->get_name();

        if (check_user_perm('polls', 'edit', false, $edited_Poll)) {	// Display the option text as link to edit the option details:
            $r = '<a href="' . $admin_url . '?ctrl=polls&amp;pqst_ID=' . $edited_Poll->ID . '&amp;popt_ID=' . $PollOption->ID . '&amp;action=edit_option' . '">' . $r . '</a>';
        }

        return $r;
    }
    $Results->cols[] = [
        'td_class' => 'nowrap',
        'th' => TB_('Option'),
        'order' => 'popt_option_text',
        'td' => '%poll_option_td_option( {Obj} )%',
    ];

    /**
     * Get the Poll answer as link
     *
     * @param integer Poll option ID
     * @param integer Count of votes for this option
     * @return string
     */
    function poll_option_td_answers($option_ID, $answers_count)
    {
        global $edited_Poll, $admin_url;
        return '<a href="' . $admin_url . '?ctrl=polls&amp;pqst_ID=' . $edited_Poll->ID . '&amp;action=edit&amp;popt_ID=' . $option_ID . '">' . $answers_count . '</a>';
    }

    $Results->cols[] = [
        'th' => TB_('Answers'),
        'th_class' => 'shrinkwrap',
        'td_class' => 'right',
        'order' => 'answers_count',
        'td' => '%poll_option_td_answers( #popt_ID#, #answers_count# )%',
    ];

    /**
     * Get the Poll percent with bar
     *
     * @param integer Percent of the answer
     * @param integer Max percent
     * @return string
     */
    function poll_option_td_percent($poll_option_percent, $max_percent)
    {
        // Calculate a percent for style relating on max percent:
        $style_percent = $max_percent > 0 ? ceil($poll_option_percent / $max_percent * 100) : 0;

        return '<div><div style="width:' . $style_percent . '%">&nbsp;</div></div>';
    }
    $Results->cols[] = [
        'th' => '%',
        'th_class' => '',
        'td_class' => 'evo_poll__percent_bar',
        'order' => 'answers_percent',
        'td' => '%poll_option_td_percent( #answers_percent#, ' . $edited_Poll->get_max_poll_options_percent() . ' )%',
    ];
    $Results->cols[] = [
        'th' => '%',
        'th_class' => '',
        'td_class' => 'nowrap',
        'order' => 'answers_percent',
        'td' => '$answers_percent$%',
    ];

    if ($perm_poll_edit) {	// Display a columnt with edit/delete icons only if current user has a perm to edit the Poll
        $Results->cols[] = [
            'th' => TB_('Actions'),
            'th_class' => 'shrinkwrap',
            'td_class' => 'shrinkwrap',
            'td' => action_icon(TB_('Edit this poll option'), 'edit', $admin_url . '?ctrl=polls&amp;pqst_ID=' . $edited_Poll->ID . '&amp;popt_ID=$popt_ID$&amp;action=edit_option')
                    . action_icon(TB_('Delete this poll option!'), 'delete', regenerate_url('pqst_ID,action', 'pqst_ID=' . $edited_Poll->ID . '&amp;popt_ID=$popt_ID$&amp;action=delete_option&amp;' . url_crumb('poll'))),
        ];
    }

    $Results->global_icon(TB_('New poll option'), 'new', regenerate_url('action', 'action=new_option'), TB_('New poll option') . ' &raquo;', 3, 4, [
        'class' => 'action_icon btn-primary',
    ]);

    $Results->display();
}
// ---- Poll Answers - END ---- //

// ---- Detailed Poll Answers - START ---- //
if ($edited_Poll->ID > 0) {	// Display the detailed answers table only when poll question already exists in the DB:
    // Get all options of the edited poll:
    $option_IDs = $Results->Cache->get_ID_array();

    $popt_ID = param('popt_ID', 'integer', null);

    $r = [];
    $options = [];

    foreach ($option_IDs as $key => $option_ID) {
        $option = $Results->Cache->get_by_ID($option_ID);
        $r[] = 'SUM( IF( pans_popt_ID = ' . $option_ID . ', 1, 0 ) ) AS opt' . $key;
        $options[] = $option;
    }
    $r = implode(', ', $r);

    $answer_SQL = new SQL();
    $answer_SQL->SELECT('pans_user_ID, user_login,' . $r);
    $answer_SQL->FROM('T_polls__answer');
    $answer_SQL->FROM_add('LEFT JOIN T_users ON user_ID = pans_user_ID');
    $answer_SQL->WHERE('pans_pqst_ID = ' . $edited_Poll->ID);
    $answer_SQL->GROUP_BY('pans_user_ID, user_login');

    $answer_count_SQL = new SQL();
    $answer_count_SQL->SELECT('COUNT( DISTINCT pans_user_ID )');
    $answer_count_SQL->FROM('T_polls__answer');
    $answer_count_SQL->WHERE('pans_pqst_ID = ' . $edited_Poll->ID);
    if ($popt_ID) {
        $option = $Results->Cache->get_by_ID($popt_ID);
        $answer_SQL->WHERE('pans_popt_ID = ' . $DB->quote($popt_ID));
        $answer_count_SQL->WHERE('pans_popt_ID = ' . $DB->quote($popt_ID));
        $option_text = $option->get('option_text');
    }

    // Create result set:
    $answer_Results = new Results($answer_SQL->get(), 'dpans_', '-A', null, $answer_count_SQL->get());
    $answer_Results->title = TB_('Detailed answers') . (empty($option_text) ? '' : ' - ' . $option_text) . get_manual_link('poll-detailed-answers');

    $answer_Results->cols[] = [
        'th' => TB_('Picture'),
        'th_class' => 'shrinkwrap',
        'td_class' => 'shrinkwrap',
        'order' => 'pans_user_ID',
        'td' => '%user_td_avatar( #pans_user_ID# )%',
    ];

    $answer_Results->cols[] = [
        'td_class' => 'nowrap',
        'th' => TB_('Login'),
        'order' => 'user_login',
        'td' => '%get_user_identity_link( #user_login#, #pans_user_ID#, "profile", "login" )%',
    ];

    /**
     * Get the Poll answer as checkmark
     *
     * @param boolean Poll answer
     * @return string
     */
    function poll_answer_td_option($answer)
    {
        if ($answer) {
            return get_icon('allowback');
        }

        return null;
    }


    /**
     * Sort poll options based on order
     */
    function sort_poll_options($a, $b)
    {
        if ($a->get('order') == $b->get('order')) {
            return $a->ID < $b->ID ? -1 : 1;
        }

        return ($a->get('order') < $b->get('order') ? -1 : 1);
    }

    uasort($options, 'sort_poll_options'); // This ensures that the column order follows the order of the previous result
    foreach ($options as $key => $option) {
        $answer_Results->cols[] = [
            'th' => '<span title="' . $option->get('option_text') . '">' . $option->get('order') . '</span>',
            'th_class' => '',
            'td_class' => 'center',
            'order' => 'opt' . $key,
            'td' => '%poll_answer_td_option( #opt' . $key . '# )%',
        ];
    }

    $answer_Results->display();
}
// ---- Detailed Poll Answers - END ---- //
