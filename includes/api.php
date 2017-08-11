<?php
namespace OMGForms\API;

use OMGForms\IA;
use OMGForms\Core;

class OMG_Entries_Controller extends \WP_REST_Posts_Controller {
	public function create_item_permissions_check( $request ) {
		$allow_anonymous = apply_filters( 'rest_allow_anonymous_entries', true, $request );

		if ( ! $allow_anonymous ) {
			return new \WP_Error( 'omg_entries_login_required', esc_html__( 'Sorry, you must be logged in to submit a form entry.', 'omg-form' ), array( 'status' => 401 ) );
		}

		return true;
	}

	public function create_item( $request ) {
		$parameters = $request->get_params();
		$parameters = $this->format_params( $parameters );

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

		$required = $this->check_required_forms( $parameters['fields'], $form );

		if ( is_wp_error( $required ) ) {
			return $required;
		}

		$data = $this->sanitize_form_data( $parameters['fields'], $form );

		$data = apply_filters( 'omg_forms_sanitize_data', $data, $parameters['form'] );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$entry_id = wp_insert_post( [
			'post_title' => sprintf( '%s: Temp', Core\get_form_name( $parameters['form'] ) ),
			'post_status' => 'publish',
			'post_type' =>  IA\get_type_entries()
		], true );

		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}

		/**
		 * Update entry title to be a concatenation of Form Name and Entry post_id
		 */
		wp_update_post( [ 'ID' => $entry_id, 'post_title' => sprintf( '%s: %d', Core\get_form_name( $parameters['form'] ), $entry_id ) ] );

		$this->save_field_data( $entry_id, $data );
		$this->set_form_relationship( $entry_id, $form );

		if ( isset( $form[ 'email' ] ) && ! empty( $form[ 'email' ] ) ) {
			$this->send_email( $form, $entry_id );
		}

		return true;
	}

	protected function check_required_forms( $fields, $form ) {
		$validation = array_reduce( array_keys( $fields ), function( $acc, $field ) use( $fields, $form ) {
			$form_field = Core\get_field( $form['name'], $this->normalize_field_name( $field ) );
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

	protected function sanitize_form_data( $fields, $form ) {
		return array_reduce( array_keys( $fields ), function ( $acc, $field ) use ( $form , $fields) {
			$form_field = Core\get_field( $form['name'], $this->normalize_field_name( $field ) );

			if ( empty( $form_field ) ) {
				return $acc;
			}

			$sanitize = $this->get_sanitization_cb( $form_field );

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

	protected function get_sanitization_cb( $form_field ) {
		if ( isset( $form_field['sanitize_cb'] ) ) {
			return $form_field[ 'sanitize_cb' ];
		} else {
			return $this->get_field_sanitize_type( $form_field['type'] );
		}
	}

	protected function get_field_sanitize_type( $type ) {
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

	protected function normalize_field_name( $field ) {
		return str_replace( 'omg-forms-', '', $field );
	}

	protected function sanitize_phone( $value ) {
		return preg_match( '%^[+]?[0-9()/ -]*$%', $value );
	}

	protected function save_field_data( $entry_id , $data ) {
		foreach( $data as $key => $value ) {
			update_post_meta( $entry_id, $key, $value );
		}
	}

	protected function set_form_relationship( $entry_id, $form ) {
		wp_set_object_terms( $entry_id, $form['ID'], IA\get_tax_forms() );
	}

	protected function format_params( $params ) {
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

	protected function send_email( $form, $entry_id ) {
		$to      = $form['email_to'];
		$headers = array(
			'From: ' . get_bloginfo( 'admin_email' )
		);
		$subject = sprintf( '%s Submission Notification.', Core\get_form_name( $form['name'] ) );

		$message = 'Hello' . "\r\n\r\n";
		$message .= 'We have received a new form submission on ' . site_url() . ".\r\n\r\n";
		$message .= 'Please login to view this form submission.';

		$subject = apply_filters( 'omg_form_submitted_subject', $subject, $form, $entry_id );
		$headers = apply_filters( 'omg_form_submitted_headers', $headers, $form, $entry_id );
		$message = apply_filters( 'omg_form_submitted_message', $message, $form, $entry_id );

		$sent = wp_mail( $to, $subject, $message, $headers );

		if ( false === $sent ) {
			error_log( 'Email failed to send for entry ' . $entry_id );
		}
	}
}
