<?php
namespace OMGForms\Plugin\Core;

use OMGForms\Plugin\Template;
use OMGForms\Plugin\IA;

function register_form( $args ) {
	global $omg_forms;

	if ( ! is_array( $omg_forms ) ) {
		$omg_forms = array();
	}

    $form_id = create_form( $args['name'] );

	$omg_forms[ $args['name'] ] = array_merge( [ 'ID' => $form_id ], $args );
}

function display_form( $slug ) {
    $args = get_form( $slug );
	ob_start(); ?>

    <form action="" id="<?php echo esc_attr( $args['ID'] ) ?>">
		<?php foreach( $args['fields'] as $field ) :
			echo get_field_template( Template\get_template_name( $field[ 'type' ] ), $field );
		endforeach;
		echo get_field_template( Template\get_template_name( 'submit' ), [] ); ?>
    </form>

	<?php return ob_get_clean();
}

function get_form( $slug ) {
    global $omg_forms;
	$form = get_term_by( 'slug', $slug, IA\get_tax_forms() );

	if ( empty( $form ) ) {
	    return false;
    }

    if ( ! empty( $omg_forms ) && isset( $omg_forms[ $slug ] ) ) {
	    return $omg_forms[ $slug ];
    }

	return [ 'ID' => $form->term_id ];
}

function create_form( $slug ) {
	$name = get_form_name( $slug );

	/**
	 * Check to see if this form already exists. If it does, then just return the existing
     * forms term_id.
	 */
	$form = get_form( $name );

	if ( ! empty( $form ) ) {
        return $form['ID'];
    }

	$name = apply_filters( 'omg_forms_pre_form_create', $name, $slug );

	/**
	 * Create a new form as a term.
	 */
	$form = wp_insert_term( $name, IA\get_tax_forms(), [ 'slug' => $slug ] );

    return $form['term_id'];
}

function get_form_name( $slug ) {
	return str_replace( '-', ' ', $slug );
}

function format_field( $field ) {

    if ( empty( $field ) ) {
        return false;
    }

	$field[ 'name' ] = sprintf( 'omg-forms-%s', $field[ 'slug' ]  );
	$field[ 'required' ] = ( isset( $field['required'] ) && true === $field['required'] ) ? 'data-required="1"' : 'data-required="0"';

	return $field;
}

function get_field( $form, $field_name ) {
    global $omg_forms;

    if ( ! isset( $omg_forms[ $form ] ) || empty( $omg_forms[ $form ]['fields'] ) ) {
        return false;
    }

    $field = array_filter( $omg_forms[ $form ]['fields'], function( $field ) use ( $field_name ) {
        return $field_name === $field['slug'];
    } );

    return ! empty( $field ) ? $field[0] : false;

}

function get_field_template( $field_type, $settings ) {
    $field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}
