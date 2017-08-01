<?php
namespace OMGForms\Plugin\IA;

function get_forms_model_keys( $key = false ) {
	$prefix = 'omg_form';
	$keys = [
		'redirect_url'     =>  [
			'key'   =>  sprintf( '%s_redirect_url', $prefix ),
			'label' =>  esc_html__( 'Redirect URL', 'omg-form' ),
		],
		'email_address'     =>  [
			'key'   =>  sprintf( '%s_email_address', $prefix ),
			'label' =>  esc_html__( 'Email Address', 'omg-form' ),
		],
		'email_subject'     =>  [
			'key'   =>  sprintf( '%s_email_subject', $prefix ),
			'label' =>  esc_html__( 'Email Subject', 'omg-form' ),
		],
		'success_message'     =>  [
			'key'   =>  sprintf( '%s_success_message', $prefix ),
			'label' =>  esc_html__( 'Success Message', 'omg-form' ),
		],
		'fields'     =>  [
			'key'   =>  sprintf( '%s_fields', $prefix ),
			'label' =>  esc_html__( 'Form Fields', 'omg-form' ),
		],
		'name'     =>  [
			'key'   =>  sprintf( '%s_name', $prefix ),
			'label' =>  esc_html__( 'Form Name', 'omg-form' ),
		]
	];

	return get_meta_key_value( $keys, $key );
}