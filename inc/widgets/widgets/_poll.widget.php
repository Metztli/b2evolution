<?php
/**
 * This file implements the Poll Widget class.
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

load_class('widgets/model/_widget.class.php', 'ComponentWidget');

/**
 * ComponentWidget Class
 *
 * A ComponentWidget is a displayable entity that can be placed into a Container on a web page.
 *
 * @package evocore
 */
class poll_Widget extends ComponentWidget
{
    public $icon = 'question-circle-o';

    /**
     * Constructor
     */
    public function __construct($db_row = null)
    {
        // Call parent constructor:
        parent::__construct($db_row, 'core', 'poll');
    }

    /**
     * Get help URL
     *
     * @return string URL
     */
    public function get_help_url()
    {
        return get_manual_url('poll-widget');
    }

    /**
     * Get name of widget
     */
    public function get_name()
    {
        return T_('Poll');
    }

    /**
     * Get short description
     */
    public function get_desc()
    {
        return T_('Display a poll.');
    }

    /**
     * Get definitions for editable params
     *
     * @see Plugin::GetDefaultSettings()
     * @param array local params
     */
    public function get_param_definitions($params)
    {
        $r = array_merge([
            'title' => [
                'label' => T_('Block title'),
                'note' => T_('Title to display in your skin.'),
                'size' => 40,
                'defaultvalue' => T_('Quick poll'),
            ],
            'poll_ID' => [
                'label' => T_('Poll ID'),
                'type' => 'integer',
                'size' => 11,
                'defaultvalue' => '',
            ],
        ], parent::get_param_definitions($params));

        return $r;
    }

    /**
     * Display the widget!
     *
     * @param array MUST contain at least the basic display params
     */
    public function display($params)
    {
        $this->init_display($params);

        $PollCache = &get_PollCache();
        $Poll = $PollCache->get_by_ID($this->disp_params['poll_ID'], false, false);

        if (! $Poll) {	// We cannot find a poll by the entered ID in widget settings:
            $this->display_error_message(sprintf(T_('Poll ID %s not found.'), '<b>' . format_to_output($this->disp_params['poll_ID'], 'text') . '</b>'));
            return false;
        }

        $poll_options = $Poll->get_poll_options();

        if (empty($poll_options)) {	// Display this red message to inform admin to create the poll options:
            $this->display_error_message(T_('This poll doesn\'t contain any answer.'));
            return false;
        }

        // START DISPLAY:
        echo $this->disp_params['block_start'];

        // Display title if requested
        $this->disp_title();

        echo $this->disp_params['block_body_start'];

        // Display a form for voting on poll:
        $poll_question = empty($this->disp_params['poll_question']) ? $Poll->get('question_text') : $this->disp_params['poll_question'];
        if ($poll_question !== '-') {	// Display a poll question only when it doesn't equal "-":
            echo '<p class="evo_poll__question">' . $poll_question . '</p>';
        }

        if ($Poll->get('max_answers') < count($poll_options)) {
            echo '<p class="note">' . sprintf(T_('Select up to %d answers below.'), $Poll->get('max_answers')) . '</p>';
        }

        // Display a form only if at least one poll option exists:
        if (is_logged_in()) {	// Set form action to vote if current user is logged in:
            $form_action = get_htsrv_url() . 'action.php?mname=polls';
        } else {	// Set form action to log in:
            $form_action = get_login_url('poll widget');
        }

        $Form = new Form($form_action);

        $Form->begin_form();

        if (is_logged_in()) {	// Set the hidden fields for voting only when user is logged in:
            $Form->add_crumb('polls');
            $Form->hidden('action', 'vote');
            $Form->hidden('poll_ID', $Poll->ID);
        }

        // Get the voted option IDs if current user already voted on this poll question:
        $user_votes = $Poll->get_user_vote();

        if ($user_votes !== false) {	// Get max percent:
            $max_poll_options_percent = $Poll->get_max_poll_options_percent();
        }

        echo '<table class="evo_poll__table"'
            // Set param to restrict user with max selected answers:
            . ($Poll->get('max_answers') > 0 ? ' data-max-answers="' . intval($Poll->get('max_answers')) . '"' : '') . '>';
        foreach ($poll_options as $poll_option) {
            echo '<tr>';
            if ($Poll->max_answers > 1) {
                $max_answer_reached = false;
                if ($user_votes !== false && count($user_votes) >= $Poll->max_answers) {
                    $max_answer_reached = true;
                }
                echo '<td class="evo_poll__selector"><input type="checkbox" id="poll_answer_' . $poll_option->ID . '"'
                        . ' name="poll_answer[]" value="' . $poll_option->ID . '"'
                        . ($user_votes !== false && in_array($poll_option->ID, $user_votes) ? ' checked="checked"' : ($max_answer_reached ? ' disabled="disabled"' : '')) . ' /></td>';
            } else {
                echo '<td class="evo_poll__selector"><input type="radio" id="poll_answer_' . $poll_option->ID . '"'
                        . ' name="poll_answer[]" value="' . $poll_option->ID . '"'
                        . ($user_votes !== false && in_array($poll_option->ID, $user_votes) ? ' checked="checked"' : '') . ' /></td>';
            }
            echo '<td class="evo_poll__title"><label for="poll_answer_' . $poll_option->ID . '">' . $poll_option->option_text . '</label></td>';
            if ($user_votes !== false) {	// If current user already voted on this poll, Display the voting results:
                // Calculate a percent for style relating on max percent:
                $style_percent = $max_poll_options_percent > 0 ? ceil($poll_option->percent / $max_poll_options_percent * 100) : 0;
                echo '<td class="evo_poll__percent_bar"><div><div style="width:' . $style_percent . '%"></div></div></td>';
                echo '<td class="evo_poll__percentage">' . $poll_option->percent . '%</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        // Set JS var to initialize code in evo_init_widget_poll.js:
        expose_var_to_js('evo_widget_poll_initialize', 'true');

        if (is_logged_in()) {	// Display a button to vote:
            $Form->button(['submit', 'submit',
                ($user_votes !== false ? T_('Change vote') : T_('Vote')),
                'SaveButton' . ($user_votes !== false ? ' btn-default' : '')]);
        } else {	// Display a button to log in:
            $Form->button(['submit', 'submit', T_('Log in'), 'SaveButton btn-success']);
        }

        $Form->end_form();

        echo $this->disp_params['block_body_end'];

        echo $this->disp_params['block_end'];

        return true;
    }
}
