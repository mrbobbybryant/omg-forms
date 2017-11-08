<?php

if ( !defined( 'OMG_FORMS_DIR' ) ) {
	define( 'OMG_FORMS_DIR', dirname( __FILE__ ) );
}

if ( !defined( 'OMG_FORMS_URL' ) ) {
	define( 'OMG_FORMS_URL', get_stylesheet_directory_uri() . '/vendor' );
}

if ( !defined( 'OMG_FORMS_FILE' ) ) {
	define( 'OMG_FORMS_FILE', __FILE__ );
}

if ( !defined( 'OMG_FORMS_VERSION' ) ) {
	define( 'OMG_FORMS_VERSION', '0.6.0' );
}

require_once OMG_FORMS_DIR . '/includes/core.php';
require_once OMG_FORMS_DIR . '/includes/api.php';
require_once OMG_FORMS_DIR . '/includes/form-functions.php';
require_once OMG_FORMS_DIR . '/includes/field-functions.php';
require_once OMG_FORMS_DIR . '/includes/template.php';
require_once OMG_FORMS_DIR . '/includes/form-helpers.php';
require_once OMG_FORMS_DIR . '/includes/settings.php';
require_once OMG_FORMS_DIR . '/includes/shortcodes.php';

\OMGForms\Core\setup();
\OMGForms\API\setup();
\OMGForms\Settings\setup();
\OMGForms\Shortcodes\setup();
