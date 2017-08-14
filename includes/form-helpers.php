<?php
namespace OMGForms\Helpers;

function get_redirect_attribute( $args ) {
	return ( isset( $args['redirect_url'] ) && ! empty( $args['redirect_url'] ) )
		? sprintf( 'data-redirect=%s', $args['redirect_url'] )
		: '';
}

function get_form_type_attribute( $args ) {
	return ( isset( $args['rest_api'] ) && ! empty( $args['rest_api'] ) )
		? 'data-rest=1'
		: 'data-rest=0';
}

function validate_form_options( $args ) {
	if ( ! isset( $args['name'] ) ) {
		throw new \Exception( 'You must provide a form name for this to be a valid form.' );
	}

	if ( isset( $args['redirect_url'] ) && isset( $args['success_message'] ) ) {
		throw new \Exception( 'You provided both a redirect_url and a success_message. You can only have one of these per form.' );
	}

	if( ! isset( $args['fields'] ) ) {
		throw new \Exception( 'You must provide at least one field for this to be a valid form.' );
	}

	if ( isset( $args['email'] ) && true === $args['email'] && ! isset( $args['email_to'] ) ) {
		throw new \Exception( 'You must pass an email_to argument in order for email to work.' );
	}

	if ( isset( $args['email_to'] ) && ! is_email( $args['email_to'] ) ) {
		throw new \Exception( 'You must provide a valid email address for the email_to argument.' );
	}

	do_action( 'omg_form_validation', $args );

}

function get_form_name( $slug ) {
	return str_replace( '-', ' ', $slug );
}

function maybe_required( $required ) {
	return ( ! empty( $required ) ? 'required' : '' );
}