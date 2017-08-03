<?php

if ( !defined( 'OMG_FORMS_DIR' ) ) {
	define( 'OMG_FORMS_DIR', dirname( __FILE__ ) );
}
if ( !defined( 'OMG_FORMS_FILE' ) ) {
	define( 'OMG_FORMS_FILE', __FILE__ );
}
if ( !defined( 'OMG_FORMS_VERSION' ) ) {
	define( 'OMG_FORMS_VERSION', '0.0.1' );
}

require_once OMG_FORMS_DIR . '/includes/core.php';
require_once OMG_FORMS_DIR . '/includes/information-architecture/index.php';
require_once OMG_FORMS_DIR . '/includes/api.php';
require_once OMG_FORMS_DIR . '/includes/form-functions.php';
require_once OMG_FORMS_DIR . '/includes/template.php';

\OMGForms\IA\setup();
\OMGForms\Core\setup();

function install() {
	\OMGForms\IA\register_entries_cpt();
	flush_rewrite_rules();
}

/**
 * Bootstrap Initial Forms Setup
 */
$version = get_option( 'omg_forms_version', OMG_FORMS_VERSION );

if ( empty( $version ) ) {
	install();
	update_option( 'omg_forms_version', OMG_FORMS_VERSION );
}
