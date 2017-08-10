<?php
namespace OMGForms\Helpers;

function get_redirect_attribute( $args ) {
	return ( isset( $args['redirect_url'] ) && ! empty( $args['redirect_url'] ) )
		? sprintf( 'data-redirect=%s', $args['redirect_url'] )
		: '';
}

function validate_form_options( $args ) {
	if ( isset( $args['redirect_url'] ) && isset( $args['success_message'] ) ) {
		throw new \Exception( 'You provided both a redirect_url and a success_message. You can only have one of these per form.' );
	}

}