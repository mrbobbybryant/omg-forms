<?php
namespace OMGForms\Core;

use OMGForms\Template;

function get_field_template( $field_type, $settings ) {
	$field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}

function validate_form_fields( $fields ) {
	foreach( $fields as $field ) {
		if ( ! isset( $field[ 'slug' ] ) || ! isset( $field[ 'type' ] ) ) {
			trigger_error( 'Invalid field. A field must include a slug and type property.', E_USER_ERROR );
		}

		if ( ! isset( $field[ 'label' ] ) ) {
			trigger_error( 'While it will work. All fields should have a label.', E_USER_NOTICE );
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