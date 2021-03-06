<?php
namespace OMGForms\Helpers;

function get_redirect_attribute( $args ) {
	return ( isset( $args['redirect_url'] ) && ! empty( $args['redirect_url'] ) )
		? sprintf( 'data-redirect=%s', $args['redirect_url'] )
		: '';
}

function get_form_rest_attribute( $args ) {
	return ( isset( $args['rest_api'] ) && ! empty( $args['rest_api'] ) )
		? 'data-rest=1'
		: 'data-rest=0';
}

function get_form_type_attribute( $args ) {
	if ( is_array( $args[ 'form_type' ] ) ) {
		return sprintf( 'data-formtype=%s', json_encode( $args[ 'form_type' ] ) );
	}

	return sprintf( 'data-formtype=%s', json_encode( [ $args[ 'form_type' ] ] ) );
}

function validate_form_options( $args ) {
	if ( ! isset( $args['name'] ) ) {
		trigger_error( 'You must provide a form name for this to be a valid form.', E_USER_ERROR );
	}

	if ( isset( $args['redirect_url'] ) && isset( $args['success_message'] ) ) {
		trigger_error( 'You provided both a redirect_url and a success_message. You can only have one of these per form.',  E_USER_WARNING );
	}

	if( ! isset( $args['fields'] ) ) {
		trigger_error( 'You must provide at least one field for this to be a valid form.',  E_USER_WARNING );
	}

	if( ! isset( $args[ 'form_type' ] ) ) {
		trigger_error( 'You must provide a form type for this to be a valid form.', E_USER_ERROR );
	}

	if ( isset( $args['email'] ) && true === $args['email'] && ! isset( $args['email_to'] ) ) {
		trigger_error( 'You must provide a valid email address for the email_to argument.', E_USER_WARNING );
	}

	if ( isset( $args['email_to'] ) && ! is_email( $args['email_to'] ) ) {
		trigger_error( 'You must provide a valid email address for the email_to argument.', E_USER_WARNING );
	}

	if ( isset( $args[ 'groups' ] ) && ! empty( $args[ 'groups' ] ) ) {
		$groups = array_keys( $args[ 'groups' ] );
		$results = array_filter( $args[ 'fields' ], function( $field ) use( $groups ) {
			return ( ! isset( $field[ 'group' ] ) || ! in_array( $field[ 'group' ], $groups ) );
		}  );
		if ( ! empty( $results ) ) {
			trigger_error( 'One or more of your form fields has not been assigned to a group.', E_USER_WARNING );
		}

		$group_check = array_filter( $args[ 'groups' ], function( $group ) use ( $args ) {
			if  ( 1 < count( $args[ 'groups' ] ) && ! isset( $group[ 'order' ] ) ) {
				return $group;
			}

			if ( ! isset( $group[ 'id' ] ) ) {
				return $group;
			}
		} );

		if ( ! empty( $group_check ) ) {
			trigger_error( 'One or more of your form groups is missing a required parameter.', E_USER_WARNING );
		}

	}

	if ( ! isset( $args[ 'success_message' ] ) && ! isset( $args[ 'redirect_url' ] ) ) {
		trigger_error( 'Your form must have either a redirect_url or a success_message to work properly.', E_USER_WARNING );
	}

	do_action( 'omg_form_validation', $args );

}

function get_form_name( $slug ) {
	return str_replace( '-', ' ', $slug );
}

function maybe_required( $required ) {
	return ( ! empty( $required ) ? 'required' : '' );
}

function get_form_group( $groups, $group_id ) {
	$result = array_values( array_filter( $groups, function( $group ) use ( $group_id ) {
		return ( $group['id'] === $group_id );
	} ) );

	return ! empty( $result ) ? $result[0] : false;
}

function is_form_type( $type, $form ) {
	if ( is_array( $form['form_type'] ) ) {
		return in_array( $type, $form['form_type'] );
	}

	return $type === $form['form_type'];
}

function return_form_level_error( $message ) {
	return new \WP_Error(
		'omg-form-submission-error',
		$message,
		array( 'status' => 400 )
	);
}

function return_field_level_error( $message, $fields ) {
	if ( ! is_array( $fields ) ) {
		return return_error(
			'omg-form-developer-error',
			'return_field_level_error expects the $fields argument to be an array.',
			400
		);
	}

	return new \WP_Error(
		'omg-form-field-error',
		$message,
		array( 'status' => 400, 'fields' => $fields )
	);
}

function return_error( $code, $message, $status, $data = false ) {
	if ( $data ) {
		return new \WP_Error(
			$code,
			$message,
			array( 'status' => $status, $data )
		);
	}

	return new \WP_Error(
		$code,
		$message,
		array( 'status' => $status )
	);
}

function normalize_field_name( $field ) {
	return str_replace( 'omg-forms-', '', $field );
}