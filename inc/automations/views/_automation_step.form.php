<?php
/**
 * This file display the automation form
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2018 by Francois Planque - {@link http://fplanque.com/}.
 * Parts of this file are copyright (c)2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @package admin
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


global $edited_AutomationStep, $action;

// Get Automation of the creating/editing Step:
$step_Automation = & $edited_AutomationStep->get_Automation();

// Determine if we are creating or updating:
$creating = is_create_action( $action );

$Form = new Form( NULL, 'automation_checkchanges', 'post', 'compact' );

$edit_automation_url = regenerate_url( 'action,step_ID', 'action=edit&amp;autm_ID='.$step_Automation->ID );

$Form->global_icon( T_('Cancel editing').'!', 'close', $edit_automation_url );

$Form->begin_form( 'fform', sprintf( $creating ? T_('New step') : T_('Step') ).get_manual_link( 'automation-step-form' ) );

$Form->add_crumb( 'automationstep' );
$Form->hidden( 'action', $creating ? 'create_step' : 'update_step' );
$Form->hidden( 'autm_ID', $step_Automation->ID );
$Form->hiddens_by_key( get_memorized( 'action'.( $creating ? ',step_ID' : '' ) ) );

$Form->info( T_('Automation'), '<a href="'.$edit_automation_url.'">'.$step_Automation->get( 'name' ).'</a>' );

if( $step_Automation->ID > 0 )
{
	$Form->info( T_('ID'), $step_Automation->ID );
}

$Form->text_input( 'step_order', $edited_AutomationStep->get( 'order' ), 10, T_('Order'), '', array( 'maxlength' => 11, 'required' => ! $creating, 'note' => $creating ? T_('Leave empty to set an order automatically.') : '' ) );

$Form->text_input( 'step_label', $edited_AutomationStep->get( 'label' ), 40, T_('Label'), '', array( 'maxlength' => 255 ) );

$Form->select_input_array( 'step_type', $edited_AutomationStep->get( 'type' ), step_get_type_titles(), T_('Type'), '', array( 'force_keys_as_values' => true, 'required' => true ) );

$Form->info_field( T_('IF Condition'), '<div id="step_if_condition"></div>', array( 'class' => 'ffield_step_if_condition' ) );
$Form->hidden( 'step_if_condition', '' );

$EmailCampaignCache = & get_EmailCampaignCache();
$EmailCampaignCache->load_all();
$Form->select_input_object( 'step_email_campaign',
	( $edited_AutomationStep->get( 'type' ) == 'send_campaign' ? $edited_AutomationStep->get( 'info' ) : '' ),
	$EmailCampaignCache, T_('Email Campaign'), array( 'allow_none' => true, 'required' => true ) );

// Load all steps of the edited step's automation excluding current step:
$AutomationStepCache = & get_AutomationStepCache();
$AutomationStepCache->clear();
$AutomationStepCache->load_where( 'step_autm_ID = '.$step_Automation->ID );
$next_step_prepend_options = array(
		'' => T_('Continue'),
		-1 => T_('STOP'),
	);

$Form->begin_line( '<span id="step_result_label_yes">'.T_( step_get_result_label( $edited_AutomationStep->get( 'type' ), 'YES' ) ).'</span>' );
	$Form->select_input_object( 'step_yes_next_step_ID', $edited_AutomationStep->get( 'yes_next_step_ID' ), $AutomationStepCache, '', array( 'prepend_options' => $next_step_prepend_options ) );
	$Form->duration_input( 'step_yes_next_step_delay', $edited_AutomationStep->get( 'yes_next_step_delay' ), T_('Delay') );
$Form->end_line();

$Form->begin_line( '<span id="step_result_label_no">'.T_( step_get_result_label( $edited_AutomationStep->get( 'type' ), 'NO' ) ).'</span>' );
	$Form->select_input_object( 'step_no_next_step_ID', $edited_AutomationStep->get( 'no_next_step_ID' ), $AutomationStepCache, '', array( 'prepend_options' => $next_step_prepend_options ) );
	$Form->duration_input( 'step_no_next_step_delay', $edited_AutomationStep->get( 'no_next_step_delay' ), T_('Delay') );
$Form->end_line();

$Form->begin_line( '<span id="step_result_label_error">'.T_( step_get_result_label( $edited_AutomationStep->get( 'type' ), 'ERROR' ) ).'</span>' );
	$Form->select_input_object( 'step_error_next_step_ID', $edited_AutomationStep->get( 'error_next_step_ID' ), $AutomationStepCache, '', array( 'prepend_options' => $next_step_prepend_options ) );
	$Form->duration_input( 'step_error_next_step_delay', $edited_AutomationStep->get( 'error_next_step_delay' ), T_('Delay') );
$Form->end_line();

$Form->end_form( array(
		array( 'submit', 'submit', ( $creating ? T_('Record') : T_('Save Changes!') ), 'SaveButton' )
	) );
?>
<script type="text/javascript">
// Update form depending on step type:
function step_type_update_info( step_type )
{
	jQuery( '#ffield_step_email_campaign, .ffield_step_if_condition' ).hide();

	switch( step_type )
	{
		case 'send_campaign':
			jQuery( '#ffield_step_email_campaign' ).show();
			jQuery( '#step_result_label_yes' ).html( '<?php echo TS_( step_get_result_label( 'send_campaign', 'YES' ) ); ?>' );
			jQuery( '#step_result_label_no' ).html( '<?php echo TS_( step_get_result_label( 'send_campaign', 'NO' ) ); ?>' );
			jQuery( '#step_result_label_error' ).html( '<?php echo TS_( step_get_result_label( 'send_campaign', 'ERROR' ) ); ?>' );
			break;

		case 'if_condition':
			jQuery( '.ffield_step_if_condition' ).show();

		default:
			jQuery( '#step_result_label_yes' ).html( '<?php echo TS_( step_get_result_label( 'if_condition', 'YES' ) ); ?>' );
			jQuery( '#step_result_label_no' ).html( '<?php echo TS_( step_get_result_label( 'if_condition', 'NO' ) ); ?>' );
			jQuery( '#step_result_label_error' ).html( '<?php echo TS_( step_get_result_label( 'if_condition', 'ERROR' ) ); ?>' );
			break;
	}
}
jQuery( '#step_type' ).change( function()
{
	step_type_update_info( jQuery( this ).val() );
} );
jQuery( document ).ready( function()
{
	step_type_update_info( jQuery( '#step_type' ).val() );

	// Initialize Query Builder for the field "IF Condition":
	jQuery( '#step_if_condition' ).queryBuilder(
	{
		plugins: ['bt-tooltip-errors'],
		icons: {
			add_group: 'fa fa-plus-circle',
			add_rule: 'fa fa-plus',
			remove_group: 'fa fa-close',
			remove_rule: 'fa fa-close',
			error: 'fa fa-warning',
		},
		lang: {
			operators: {
				equal: '=',
				not_equal: '&#8800;',
				less: '<',
				less_or_equal: '&#8804;',
				greater: '>',
				greater_or_equal: '&#8805;',
			}
		},

		filters: [
		{
			id: 'user_has_tag',
			label: '<?php echo TS_('User tag' ); ?>',
			type: 'string',
			operators: ['equal', 'not_equal'],
		},
		{
			id: 'date',
			label: '<?php echo TS_('Date' ); ?>',
			type: 'date',
			operators: ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between'],
			plugin: 'datepicker',
			plugin_config: {
				dateFormat: '<?php echo jquery_datepicker_datefmt(); ?>',
				monthNames: <?php echo jquery_datepicker_month_names(); ?>,
				dayNamesMin: <?php echo jquery_datepicker_day_names(); ?>,
				firstDay: '<?php echo locale_startofweek(); ?>',
			},
			validation: {
				format: '<?php echo strtoupper( jquery_datepicker_datefmt() ); ?>'
			},
		}
		],
		// Prefill the field "IF Condition" with stored data from DB:
		rules: <?php echo $edited_AutomationStep->get( 'if_condition_js_object' ); ?>
	} );
} );

// Prepare form before submitting:
jQuery( 'form' ).on( 'submit', function()
{
	if( jQuery( '#step_type' ).val() == 'if_condition' )
	{	// Convert "IF Condition" field to JSON format:
		var result = jQuery( '#step_if_condition' ).queryBuilder( 'getRules' );
		if( result === null )
		{	// Stop submitting on wrong SQL:
			return false;
		}
		else
		{	// Set query rules to hidden field before submitting:
			jQuery( 'input[name=step_if_condition]' ).val( JSON.stringify( result ) );
		}
	}
} );
</script>