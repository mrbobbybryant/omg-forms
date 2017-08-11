<?php
namespace OMGForms\Core;

use OMGForms\Template;
use OMGForms\IA;
use OMGForms\Helpers;

function register_form( $args ) {
	global $omg_forms;

	/**
	 * Checks the submitted args to ensure they will result in a valid OMG Form.
	 */
	Helpers\validate_form_options( $args );

	/**
	 * Ensures our global variable is an array, since on startup it will not.
	 */
	if ( ! is_array( $omg_forms ) ) {
		$omg_forms = array();
	}

    $form_id = create_form( $args['name'] );

	$omg_forms[ $args['name'] ] = array_merge( [ 'ID' => $form_id ], $args );
}

function display_form( $slug ) {
    $args = get_form( $slug );

    if ( empty( $args ) ) {
        throw new \Exception( 'A form with that name does not exist.' );
    }

    if ( ! isset( $args[ 'fields' ] ) ) {
	    throw new \Exception( 'You must register at least of field for a form to be valid.' );
    }

    $redirect = Helpers\get_redirect_attribute( $args );

	ob_start(); ?>
    <div class="omg-form-wrapper" <?php echo esc_attr( $redirect ); ?>>

        <?php do_action( 'omg_form_before_form' ); ?>

        <?php if ( isset( $args['success_message'] ) ) : ?>
         <p class="omg-success">
             <?php echo esc_html( $args['success_message'] ) ?>
         </p>
        <?php endif; ?>
        <form class="omg-form" action="" id="<?php echo esc_attr( $args['name'] ) ?>">
		    <?php foreach( $args['fields'] as $field ) :
			    echo get_field_template( Template\get_template_name( $field[ 'type' ] ), $field );
		    endforeach;

		    do_action( 'omg_form_before_form_submit' );

		    echo get_field_template( Template\get_template_name( 'submit' ), [] ); ?>
        </form>
        <?php do_action( 'omg_form_after_form' ); ?>
    </div>


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

	return $field;
}

function get_field( $form, $field_name ) {
    global $omg_forms;

    if ( ! isset( $omg_forms[ $form ] ) || empty( $omg_forms[ $form ]['fields'] ) ) {
        return false;
    }

    $field = array_values( array_filter( $omg_forms[ $form ]['fields'], function( $field ) use ( $field_name ) {
        return $field_name === $field['slug'];
    } ) );

    return ! empty( $field ) ? $field[0] : false;

}

function get_field_template( $field_type, $settings ) {
    $field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}
