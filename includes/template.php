<?php
namespace OMGForms\Plugin\Template;

function locate_template( $name ) {
	$template_paths = get_template_paths( $name );

	foreach( $template_paths as $path ) {
		if ( file_exists( $path ) ) {
			return $path;
		}
	}

	return false;
}

function get_template_paths( $name ) {
	return [
		sprintf( '%s/forms/%s' , get_stylesheet_directory(), $name ),
		sprintf( '%s/forms/%s', get_template_directory(), $name ),
		sprintf( '%sincludes/templates/%s', OMG_FORMS_DIR, $name )
	];
}

function get_template_part( $name, $args ) {
	$template_path = locate_template( $name );

	if ( empty( $template_path ) ) {
		return false;
	}

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