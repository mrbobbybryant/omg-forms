<?php
namespace OMGForms\Sanitize;

use OMGForms\Core;
use OMGForms\Helpers;

function sanitize_form_data( $fields, $form ) {
	return array_reduce( array_keys( $fields ), function ( $acc, $field ) use ( $form , $fields) {
		$form_field = Core\get_field( $form['name'], Helpers\normalize_field_name( $field ) );

		if ( empty( $form_field ) ) {
			return $acc;
		}

		$sanitize = get_sanitization_cb( $form_field );

		if ( is_array( $fields[ $field ] ) ) {
			/**
			 * Since this field is an array of values we need to loop over and sanitize them each.
			 */
			$acc[ $field ] = array_map( function( $item ) use ( $sanitize ) {
				return call_user_func_array( $sanitize, [ $item ] );
			}, $fields[ $field ] );
		} else {
			$acc[ $field ] = call_user_func_array( $sanitize, [ $fields[ $field ] ] );
		}

		return $acc;

	}, [] );
}

function get_sanitization_cb( $form_field ) {
	if ( isset( $form_field['sanitize_cb'] ) ) {
		return $form_field[ 'sanitize_cb' ];
	} else {
		return get_field_sanitize_type( $form_field['type'] );
	}
}

function get_field_sanitize_type( $type ) {
	switch ( $type ) {
		case 'text':
			return 'sanitize_text_field';
		case 'email':
			return 'sanitize_email';
		case 'textarea':
			return 'sanitize_textarea_field';
		case 'number':
			return 'absint';
		default:
			return 'sanitize_text_field';
	}
}