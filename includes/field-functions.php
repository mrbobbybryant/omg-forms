<?php
namespace OMGForms\Core;

use OMGForms\Template;

function get_field_template( $field_type, $settings ) {
	$field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}

function validate_form_fields( $fields ) {
	foreach( $fields as $field ) {
		if ( ! isset( $field[ 'slug' ] ) || ! isset( $field[ 'label' ] ) || ! isset( $field[ 'type' ] ) ) {
			throw new \Exception( 'Invalid field. A field must have at least a slug, label, and type.' );
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

	return $field;
}

function get_field( $form, $field_name ) {
	global $omg_forms;

	if ( ! isset( $omg_forms[ $form ] ) || empty( $omg_forms[ $form ]['fields'] ) ) {
		return false;
	}

	$field = array_values( array_filter( $omg_forms[ $form ]['fields'], function( $field ) use ( $field_name ) {
		return $field_name === $field['slug'];
	} ) );

	return ! empty( $field ) ? $field[0] : false;

}