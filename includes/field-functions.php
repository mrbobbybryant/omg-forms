<?php
namespace OMGForms\Core;

use OMGForms\Template;
use OMGForms\Helpers;

function get_field_template( $field_type, $settings ) {
	$field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}

function validate_form_fields( $fields ) {
	foreach( $fields as $field ) {
		if ( ! isset( $field[ 'slug' ] ) || ! isset( $field[ 'type' ] ) ) {
			trigger_error( 'Invalid field. A field must include a slug and type property.', E_USER_ERROR );
		}
	}
}

function format_field( $field ) {

	if ( empty( $field ) ) {
		return false;
	}

	$field[ 'name' ] = sprintf( 'omg-forms-%s', $field[ 'slug' ]  );

	if ( ! isset( $field[ 'required' ] ) ) {
		$field[ 'required' ] = false;
	}

	if ( ! isset( $field[ 'placeholder' ] ) ) {
		$field[ 'placeholder' ] = '';
	}

	if ( ! isset( $field[ 'classname' ] ) ) {
		$field[ 'classname' ] = '';
	}

	$field = apply_filters( 'omg-form-filter-field-args', $field );

	return $field;
}

function get_field( $form, $field_name ) {
	global $omg_forms;

	$slug = strtolower( $form );

	if ( ! isset( $omg_forms[ strtolower( $slug ) ] ) || empty( $omg_forms[ $slug ]['fields'] ) ) {
		return false;
	}

	$field = array_values( array_filter( $omg_forms[ $slug ]['fields'], function( $field ) use ( $field_name ) {
		return $field_name === $field['slug'];
	} ) );

	return ! empty( $field ) ? $field[0] : false;

}

function get_fields( $form ) {
	global $omg_forms;
	$form_name = strtolower( $form );

	if ( empty( $omg_forms ) || ! isset( $omg_forms[ $form_name ] ) ) {
		return false;
	}

	return $omg_forms[ $form_name ]['fields'];
}

function register_supplementary_field( $form_type, $field_args, $form_args ) {
	if ( Helpers\is_form_type( $form_type, $form_args ) ) {

		if ( ! isset( $field_args[ 'group' ] ) && isset( $form_args[ 'groups' ] ) ) {
			$last_group = count( $form_args[ 'groups' ] ) - 1;
			$field_args[ 'group' ] = $form_args[ 'groups' ][ $last_group ][ 'id' ];
		}

		$form_args['fields'][] = $field_args;
	}

	return $form_args;
}

function states_list() {
	$states = array(
		'Alabama'=>'AL',
		'Alaska'=>'AK',
		'Arizona'=>'AZ',
		'Arkansas'=>'AR',
		'California'=>'CA',
		'Colorado'=>'CO',
		'Connecticut'=>'CT',
		'Delaware'=>'DE',
		'Florida'=>'FL',
		'Georgia'=>'GA',
		'Hawaii'=>'HI',
		'Idaho'=>'ID',
		'Illinois'=>'IL',
		'Indiana'=>'IN',
		'Iowa'=>'IA',
		'Kansas'=>'KS',
		'Kentucky'=>'KY',
		'Louisiana'=>'LA',
		'Maine'=>'ME',
		'Maryland'=>'MD',
		'Massachusetts'=>'MA',
		'Michigan'=>'MI',
		'Minnesota'=>'MN',
		'Mississippi'=>'MS',
		'Missouri'=>'MO',
		'Montana'=>'MT',
		'Nebraska'=>'NE',
		'Nevada'=>'NV',
		'New Hampshire'=>'NH',
		'New Jersey'=>'NJ',
		'New Mexico'=>'NM',
		'New York'=>'NY',
		'North Carolina'=>'NC',
		'North Dakota'=>'ND',
		'Ohio'=>'OH',
		'Oklahoma'=>'OK',
		'Oregon'=>'OR',
		'Pennsylvania'=>'PA',
		'Rhode Island'=>'RI',
		'South Carolina'=>'SC',
		'South Dakota'=>'SD',
		'Tennessee'=>'TN',
		'Texas'=>'TX',
		'Utah'=>'UT',
		'Vermont'=>'VT',
		'Virginia'=>'VA',
		'Washington'=>'WA',
		'West Virginia'=>'WV',
		'Wisconsin'=>'WI',
		'Wyoming'=>'WY'
	);

	return array_map( function( $short, $long ) {
		return [
			'value' => $short,
			'label' => $long
		];
	},  array_keys($states), $states);

}

function months_list() {
	$months = array(
		'01' => 'January',
		'02' => 'February',
		'03' => 'March',
		'04' => 'April',
		'05' => 'May',
		'06' => 'June',
		'07' => 'July ',
		'08' => 'August',
		'09' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);

	return array_map( function( $short, $long ) {
		return [
			'value' => $short,
			'label' => $long
		];
	},  array_keys($months), $months);

}

function years_list() {
	$years = range( date("Y"),date("Y",strtotime("+20 year") ) );

	return array_map( function( $date ) {
		return [
			'value' => $date,
			'label' => $date
		];
	},  $years);

}