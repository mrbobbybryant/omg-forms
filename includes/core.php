<?php
namespace OMGForms\Core;

function setup() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\scripts' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\styles' );
}

function scripts() {
	wp_enqueue_script( 'omg-js', OMG_FORMS_URL . '/developwithwp/omg-forms/dist/frontend.bundle.js', array( 'closest-js', 'promise-js' ), OMG_FORMS_VERSION, true );
	wp_enqueue_script( 'closest-js', OMG_FORMS_URL . '/developwithwp/omg-forms/assets/js/vendor/closest.js', array(), OMG_FORMS_VERSION, true );
	wp_enqueue_script( 'promise-js', OMG_FORMS_URL . '/developwithwp/omg-forms/assets/js/vendor/promise.min.js', array(), OMG_FORMS_VERSION, true );

	wp_localize_script(
		'omg-js',
		'OMGForms',
		[
			'nonce'	=>	wp_create_nonce( 'wp_rest' ),
			'baseURL'	=>	site_url()
		]
	);
}

function styles() {
	wp_enqueue_style( 'omg-css', OMG_FORMS_URL . '/developwithwp/omg-forms/dist/frontend.bundle.css', array(), OMG_FORMS_VERSION );
}
