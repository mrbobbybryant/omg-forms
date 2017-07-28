<?php
namespace OMGForms\Plugin\Core;

function setup() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts' );
}

function scripts() {
	wp_enqueue_script( 'omg-js', OMG_PLUGIN_URL . '/dist/frontend.bundle.js', array(), OMG_PLUGIN_VERSION, true );

	wp_localize_script(
		'omg-js',
		'OMGForms',
		[
			'nonce'	=>	wp_create_nonce( 'wp_rest' ),
			'baseURL'	=>	site_url()
		]
	);
}
