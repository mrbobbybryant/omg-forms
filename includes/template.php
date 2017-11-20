<?php
namespace OMGForms\Template;

function locate_template( $name, $args ) {
	$template_paths = get_template_paths( $name, $args );

	foreach( $template_paths as $path ) {
		if ( file_exists( $path ) ) {
			return $path;
		}
	}

	return false;
}

function get_template_paths( $name, $args ) {
	$name = ( isset( $args[ 'template' ] ) ) ? $args[ 'template' ] : $name;

	return [
		sprintf( '%s/forms/%s' , get_stylesheet_directory(), $name ),
		sprintf( '%s/forms/%s', get_template_directory(), $name ),
		sprintf( '%s/includes/templates/%s', OMG_FORMS_DIR, $name )
	];
}

function get_template_part( $name, $args ) {
	$template_path = locate_template( $name, $args );

	if ( empty( $template_path ) ) {
		return false;
	}

	$args[ 'error' ] = format_template_error_message( $args );

	if ( is_array( $args ) ){
		extract( $args );
	}

	ob_start();
		include $template_path;
	return ob_get_clean();
}

function get_template_name( $field_type ) {
	return sprintf( '%s.php', $field_type );
}

function format_template_error_message( $args ) {
	return isset( $args['error'] ) ? $args['error'] : '';
}