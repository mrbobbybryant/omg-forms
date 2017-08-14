<?php
namespace OMGForms\API;

use OMGForms\IA;
use OMGForms\Core;

function setup() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_endpoint' );
}

function register_rest_endpoint() {
	register_rest_route( 'omg/v1', '/forms', array(
		'methods'               => \WP_REST_Server::EDITABLE,
		'callback'              => __NAMESPACE__ . '\submit_form_data',
		'permission_callback'   =>  __NAMESPACE__ . '\create_item_permissions_check'
	) );
}


function create_item_permissions_check( $request ) {
	$allow_anonymous = apply_filters( 'rest_allow_anonymous_entries', true, $request );

	if ( ! $allow_anonymous ) {
		return new \WP_Error( 'omg_entries_login_required', esc_html__( 'Sorry, you must be logged in to submit a form entry.', 'omg-form' ), array( 'status' => 401 ) );
	}

	return true;
}

function submit_form_data( $request ) {
	$parameters = $request->get_params();
	$parameters = format_params( $parameters );

	if ( is_wp_error( $parameters ) ) {
		return $parameters;
	}

	$form = Core\get_form( $parameters['form'] );

	if ( empty( $form ) ) {
		return new \WP_Error(
			'omg_form_validation_fail',
			'This is not a valid form id.',
			array( 'status' => 400 )
		);
	}

	$required = check_required_forms( $parameters['fields'], $form );

	if ( is_wp_error( $required ) ) {
		return $required;
	}

	$data = sanitize_form_data( $parameters['fields'], $form );

	$data = apply_filters( 'omg_forms_sanitize_data', $data, $parameters['form'] );

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	do_action( 'omg_forms_save_data', $data, $form );

	return true;
}

function check_required_forms( $fields, $form ) {
	$validation = array_reduce( array_keys( $fields ), function( $acc, $field ) use( $fields, $form ) {
		$form_field = Core\get_field( $form['name'], normalize_field_name( $field ) );
		if ( isset( $form_field['required'] ) && true === $form_field['required'] && empty( $fields[ $field] ) ) {
			$acc = array_merge( $acc, [ $field ] );
		}
		return $acc;
	}, [] );

	return ( empty( $validation ) ) ? true : new \WP_Error(
		'omg_form_validation_fail',
		'Missing required form fields.',
		array( 'status' => 400, 'fields' => $validation )
	);

}

function sanitize_form_data( $fields, $form ) {
	return array_reduce( array_keys( $fields ), function ( $acc, $field ) use ( $form , $fields) {
		$form_field = Core\get_field( $form['name'], normalize_field_name( $field ) );

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
			return 'wp_kses_post';
		case 'number':
			return 'absint';
		default:
			return 'sanitize_text_field';
	}
}

function normalize_field_name( $field ) {
	return str_replace( 'omg-forms-', '', $field );
}

function sanitize_phone( $value ) {
	return preg_match( '%^[+]?[0-9()/ -]*$%', $value );
}

function format_params( $params ) {
	if ( ! isset( $params['formId'] ) || empty( $params['formId'] ) ) {
		return new \WP_Error(
			'omg_form_validation_fail',
			'You must pass the form id as part of your FormData.',
			array( 'status' => 400 )
		);
	}

	$form = $params['formId'];
	unset( $params['formId'] );

	return [
		'form' => $form,
		'fields' => $params
	];
}

