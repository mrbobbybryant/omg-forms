<?php

if ( !defined( 'OMG_FORMS_DIR' ) ) {
	define( 'OMG_FORMS_DIR', dirname( __FILE__ ) );
}
if ( !defined( 'OMG_PLUGIN_FILE' ) ) {
	define( 'OMG_PLUGIN_FILE', __FILE__ );
}
if ( !defined( 'OMG_PLUGIN_VERSION' ) ) {
	define( 'OMG_PLUGIN_VERSION', '0.0.1' );
}

require_once OMG_FORMS_DIR . '/includes/core.php';
require_once OMG_FORMS_DIR . '/includes/information-architecture/index.php';
require_once OMG_FORMS_DIR . '/includes/api.php';
require_once OMG_FORMS_DIR . '/includes/form-functions.php';
require_once OMG_FORMS_DIR . '/includes/template.php';

\OMGForms\Plugin\IA\setup();
\OMGForms\Plugin\Core\setup();

register_activation_hook( __FILE__, __NAMESPACE__ . '\install' );

function install() {
	\OMGForms\Plugin\IA\register_entries_cpt();
	flush_rewrite_rules();
}

/**
 * Bootstrap Initial Forms Setup
 */
$version = get_option( 'omg_forms_version', OMG_PLUGIN_VERSION );

if ( empty( $version ) ) {
	install();
	update_option( 'omg_forms_version', OMG_PLUGIN_VERSION );
}