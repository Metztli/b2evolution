<?php
/**
 * This file implements automation functions.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2018 by Francois Planque - {@link http://fplanque.com/}.
 * Parts of this file are copyright (c)2004-2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package evocore
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * Get array of status titles for automation
 *
 * @return array Status titles
 */
function autm_get_status_titles()
{
	return array(
		'paused' => T_('Paused'),
		'active' => T_('Active'),
	);
}


/**
 * Get status title of automation by status value
 *
 * @param string Status value
 * @return string Status title
 */
function autm_get_status_title( $status )
{
	$statuses = autm_get_status_titles();

	return isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;
}


/**
 * Get array of type titles for automation step
 *
 * @return array Type titles
 */
function step_get_type_titles()
{
	return array(
		'if_condition'  => T_('IF Condition'),
		'send_campaign' => T_('Send Campaign'),
	);
}


/**
 * Get type title of automation step by type value
 *
 * @param string Type value
 * @return string Type title
 */
function step_get_type_title( $type )
{
	$types = step_get_type_titles();

	return isset( $types[ $type ] ) ? $types[ $type ] : $type;
}


/**
 * Get array of result titles for automation step
 *
 * @return array Result titles per step type
 */
function step_get_result_titles()
{
	return array(
		'if_condition' => array(
			'YES'   => NT_('YES'),
			'NO'    => NT_('NO'),
			'ERROR' => NT_('ERROR'),
		),
		'send_campaign' => array(
			'YES'   => NT_('Email SENT'),
			'NO'    => NT_('Email was ALREADY sent'),
			'ERROR' => NT_('ERROR: Email cannot be sent'),
		),
	);
}


/**
 * Get result title of automation step by step type and result value
 *
 * NOTE! Return string is not translatable, Use funcs T_(), TS_() and etc. in that place where you use this func.
 *
 * @param string Step type: 'if_condition', 'send_campaign
 * @param string Step result: 'YES', 'NO', 'ERROR'
 * @return string Result title
 */
function step_get_result_title( $type, $result )
{
	$results = step_get_result_titles();

	return isset( $results[ $type ][ $result ] ) ? $results[ $type ][ $result ] : $result;
}


/**
 * Get array of result labels for automation step
 *
 * @return array Result labels per step type
 */
function step_get_result_labels()
{
	return array(
		'if_condition' => array(
			'YES'   => NT_('Next step if YES'),
			'NO'    => NT_('Next step if NO'),
			'ERROR' => NT_('Next step if ERROR'),
		),
		'send_campaign' => array(
			'YES'   => NT_('Next step if Email SENT'),
			'NO'    => NT_('Next step if Email was ALREADY sent'),
			'ERROR' => NT_('Next step if ERROR: Email cannot be sent'),
		),
	);
}


/**
 * Get result label of automation step by step type and result value
 *
 * NOTE! Return string is not translatable, Use funcs T_(), TS_() and etc. in that place where you use this func.
 *
 * @param string Step type: 'if_condition', 'send_campaign
 * @param string Step result: 'YES', 'NO', 'ERROR'
 * @return string Result label
 */
function step_get_result_label( $type, $result )
{
	$results = step_get_result_labels();

	return isset( $results[ $type ][ $result ] ) ? $results[ $type ][ $result ] : $result;
}


/**
 * Helper function to display step info on Results table
 *
 * @param string Step label
 * @param string Step type
 * @return string
 */
function step_td_label( $step_label, $step_type )
{
	return ( empty( $step_label ) ? step_get_type_title( $step_type ) : $step_label );
}


/**
 * Helper function to display next step info on Results table
 *
 * @param integer Step ID
 * @param integer Next step ID
 * @param integer Next step order
 * @param integer Next step delay
 * @return string
 */
function step_td_next_step( $step_ID, $next_step_ID, $next_step_order, $next_step_delay )
{
	if( empty( $next_step_ID ) )
	{	// Next ordered step:
		return '<span class="green">'.T_('Continue').' ('.seconds_to_period( $next_step_delay ).')</span>';
	}
	elseif( $next_step_ID == '-1' )
	{	// Stop workflow:
		return '<span class="red">'.T_('STOP').'</span>';
	}
	elseif( $next_step_ID == $step_ID )
	{	// Loop:
		return '<span class="orange">'.T_('Loop').' ('.seconds_to_period( $next_step_delay ).')</span>';
	}

	return sprintf( T_('Go to step %d'), intval( $next_step_order ) ).' ('.seconds_to_period( $next_step_delay ).')';
}


/**
 * Helper function to display step actions on Results table
 *
 * @param integer Step ID
 * @param boolean Is first step?
 * @param boolean Is last step?
 * @return string
 */
function step_td_actions( $step_ID, $is_first_step, $is_last_step )
{
	global $admin_url;

	$r = '';

	if( $is_first_step )
	{	// First step cannot be moved up, print out blank icon:
		$r .= get_icon( 'move_up', 'noimg' );
	}
	else
	{	// Display action icon to move step up:
		$r .= action_icon( T_('Move up'), 'move_up', regenerate_url( 'step_ID,action', 'step_ID='.$step_ID.'&amp;action=move_step_up&amp;'.url_crumb( 'automationstep' ) ) );
	}

	if( $is_last_step )
	{	// Last step cannot be moved down, print out blank icon:
		$r .= get_icon( 'move_down', 'noimg' );
	}
	else
	{	// Display action icon to move step down:
		$r .= action_icon( T_('Move down'), 'move_down', regenerate_url( 'step_ID,action', 'step_ID='.$step_ID.'&amp;action=move_step_down&amp;'.url_crumb( 'automationstep' ) ) );
	}

	$r .= action_icon( T_('Edit this step'), 'edit', $admin_url.'?ctrl=automations&amp;action=edit_step&amp;step_ID='.$step_ID );

	$r .= action_icon( T_('Delete this step!'), 'delete', regenerate_url( 'step_ID,action', 'step_ID='.$step_ID.'&amp;action=delete_step&amp;'.url_crumb( 'automationstep' ) ) );

	return $r;
}
?>