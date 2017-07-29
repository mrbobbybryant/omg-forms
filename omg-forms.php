<?php
/*
Plugin Name: Oh My God Forms
Plugin URI:  https://github.com/mrbobbybryant/omg-forms
Description: Forms framework built by and for developers.
Version:     0.0.1
Author:      Bobby Bryant
Author URI:  https://developwithwp.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: omg-forms
Domain Path: /languages
*/

if ( !defined( 'OMG_PLUGIN_DIR' ) ) {
	define( 'OMG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'OMG_PLUGIN_URL' ) ) {
	define( 'OMG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'OMG_PLUGIN_FILE' ) ) {
	define( 'OMG_PLUGIN_FILE', __FILE__ );
}
if ( !defined( 'OMG_PLUGIN_VERSION' ) ) {
	define( 'OMG_PLUGIN_VERSION', '0.0.1' );
}

require_once OMG_PLUGIN_DIR . '/includes/core.php';
require_once OMG_PLUGIN_DIR . '/includes/information-architecture/index.php';
require_once OMG_PLUGIN_DIR . '/includes/api.php';
require_once OMG_PLUGIN_DIR . '/includes/form-functions.php';
require_once OMG_PLUGIN_DIR . '/includes/template.php';

\OMGForms\Plugin\IA\setup();
\OMGForms\Plugin\Core\setup();

register_activation_hook( __FILE__, __NAMESPACE__ . '\install' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );

function install() {
	\OMGForms\Plugin\IA\register_forms_cpt();
	\OMGForms\Plugin\IA\register_entries_cpt();
	flush_rewrite_rules();
}

function deactivate() {
	flush_rewrite_rules();
}