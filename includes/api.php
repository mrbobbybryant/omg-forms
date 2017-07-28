<?php
namespace OMGForms\Plugin\API;

use OMGForms\Plugin\IA;

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

		$required = $this->check_required_forms( $parameters['fields'] );

		if ( is_wp_error( $required ) ) {
			return $required;
		}

		$data = $this->sanitize_form_data( $parameters['fields'] );

		$data = apply_filters( 'omg_forms_sanitize_data', $data, $parameters['form'] );

		$form = $this->get_form( $parameters['form'] );

		if ( is_wp_error( $form ) ) {
			return $form;
		}

		$entry_id = wp_insert_post( [
			'post_title' => sprintf( '%s: %s', $this->get_form_name( $parameters['form'] ), $data[0]['value'] ),
			'post_status' => 'publish',
			'post_type' =>  IA\get_type_entries()
		], true );

		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}

		$this->save_field_data( $entry_id, $data );
		$this->set_form_relationship( $entry_id, $form );

		return true;
	}

	protected function check_required_forms( $fields ) {
		$validation = array_reduce( $fields, function( $acc, $field ) {
			if ( true === (bool) absint( $field[ 'required' ] ) && empty( $field[ 'value' ] ) ) {
				$acc = array_merge( $acc, [ $field[ 'name' ] ] );
			}
			return $acc;
		}, [] );

		return ( empty( $validation ) ) ? true : new \WP_Error(
			'omg_form_validation_fail',
			'Missing required form fields.',
			array( 'status' => 400, 'fields' => $validation )
		);

	}

	protected function sanitize_form_data( $fields ) {
		return array_map( function( $field ) {
			$sanitize = $this->get_field_sanitize_type( $field[ 'type' ] );
			$field[ 'value' ] = call_user_func_array( $sanitize, [ $field[ 'value' ] ] );
			return $field;
		}, $fields );
	}

	public function get_form( $slug ) {
		$form = get_term_by( 'slug', $slug, IA\get_tax_forms() );

		if ( empty( $form ) ) {
			$form = $this->create_form( $slug );
		}

		return $form;
	}

	public function create_form( $slug ) {
		$name = $this->get_form_name( $slug );

		$name = apply_filters( 'omg_forms_pre_form_create', $name, $slug );

		return wp_insert_term( $name, IA\get_tax_forms(), [ 'slug' => $slug ] );
	}

	protected function get_form_name( $slug ) {
		return str_replace( '-', ' ', $slug );
	}

	protected function get_field_sanitize_type( $type ) {
		switch ( $type ) {
			case 'text':
				return 'sanitize_text_field';
			case 'email':
				return 'sanitize_email';
			case 'textarea':
				return 'wp_kses_post';
			default:
				return 'sanitize_text_field';
		}
	}

	protected function save_field_data( $entry_id , $data ) {
		foreach( $data as $field ) {
			update_post_meta( $entry_id, $field['name'], $field['value'] );
		}
	}

	protected function set_form_relationship( $entry_id, $form ) {
		wp_set_object_terms( $entry_id, $form->term_id, IA\get_tax_forms() );
	}
}