<?php
namespace OMGForms\Shortcodes;

use OMGForms\Core;

function setup() {
	add_shortcode( 'omgform', __NAMESPACE__ . '\omg_forms_shortcode_handler' );
}

function omg_forms_shortcode_handler( $atts, $content = null ) {
	return Core\display_form( $atts['form'] );
}
