<?php
namespace OMGForms\Core;

function setup() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts' );
}

function scripts() {
	wp_enqueue_script( 'omg-js', OMG_FORMS_URL . '/omg-forms/dist/frontend.bundle.js', array(), OMG_FORMS_VERSION, true );

	wp_localize_script(
		'omg-js',
		'OMGForms',
		[
			'nonce'	=>	wp_create_nonce( 'wp_rest' ),
			'baseURL'	=>	site_url()
		]
	);
}
