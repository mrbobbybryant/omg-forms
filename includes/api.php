<?php
namespace OMGForms\API;

use OMGForms\Core;
use OMGForms\Helpers;
use OMGForms\Sanitize;
use OMGForms\Email;

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

	if ( isset( $parameters[ 'omg-forms-contact_by_mail' ] ) ) {
		return Helpers\return_error( 'omg-forms-honeypot-error', 'This is not allowed', 400 );
	}

	$parameters = format_params( $parameters );

	if ( is_wp_error( $parameters ) ) {
		return $parameters;
	}

	$form = Core\get_form( $parameters['form'] );

	$form = apply_filters( 'omg_form_filter_form', $form, $parameters );

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

	$data = Sanitize\sanitize_form_data( $parameters['fields'], $form );

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$data = apply_filters( 'omg_forms_sanitize_data', $data, $form, $parameters['fields'] );

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$result = apply_filters( 'omg_forms_save_data', $data, $form );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( isset( $form['email'] ) && ! empty( $form['email'] ) ) {
		Email\send( $form, $result );
	}

	return $result;
}

function check_required_forms( $fields, $form ) {
	$validation = array_reduce( array_keys( $fields ), function( $acc, $field ) use( $fields, $form ) {
		$form_field = Core\get_field( $form['name'], Helpers\normalize_field_name( $field ) );
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

