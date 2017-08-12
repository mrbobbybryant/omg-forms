<?php
namespace OMGForms\Helpers;

function get_redirect_attribute( $args ) {
	return ( isset( $args['redirect_url'] ) && ! empty( $args['redirect_url'] ) )
		? sprintf( 'data-redirect=%s', $args['redirect_url'] )
		: '';
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

function maybe_required( $required ) {
	return ( ! empty( $required ) ? 'required' : '' );
}