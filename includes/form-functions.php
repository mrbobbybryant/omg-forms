<?php
namespace OMGForms\Core;

use OMGForms\Template;
use OMGForms\IA;
use OMGForms\Helpers;

function register_form( $args ) {
	global $omg_forms;

	$args = apply_filters( 'omg_form_filter_register_args', $args );

	/**
	 * Checks the submitted args to ensure they will result in a valid OMG Form.
	 */
	Helpers\validate_form_options( $args );

	validate_form_fields( $args[ 'fields' ] );
	/**
	 * Ensures our global variable is an array, since on startup it will not.
	 */
	if ( ! is_array( $omg_forms ) ) {
		$omg_forms = array();
	}

    create_form( $args['name'] );

	$omg_forms[ $args['name'] ] = $args;
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
    $form_type = Helpers\get_form_type_attribute( $args );
    $form_classname = ( ! isset( $args[ 'classname' ] ) ) ? sprintf( 'omg-form %s', $args[ 'classname' ] ) : 'omg-form';

	ob_start(); ?>
    <div class="omg-form-wrapper" <?php echo esc_attr( $redirect ); ?> <?php echo esc_attr( $form_type ); ?>>

        <?php do_action( 'omg_form_before_form' ); ?>

        <?php if ( isset( $args['success_message'] ) ) : ?>
         <p class="omg-success">
             <?php echo esc_html( $args['success_message'] ) ?>
         </p>
        <?php endif; ?>
        <form class="<?php echo esc_attr( $form_classname ); ?>" action="" id="<?php echo esc_attr( $args['name'] ) ?>">
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

	if ( empty( $omg_forms ) || ! isset( $omg_forms[ $slug ] ) ) {
		return false;
	}

	$form = apply_filters( 'omg_forms_get_form', $omg_forms[ $slug ], $slug );

	if ( ! empty( $omg_forms ) && isset( $omg_forms[ $slug ] ) ) {
		return $form;
	}
    //Todo This needs be changed. This function needs to return the whole form object.
	return [ 'ID' => $form->term_id ];
}

function create_form( $slug ) {
	$name = Helpers\get_form_name( $slug );

	/**
	 * Check to see if this form already exists. If it does, then just return the existing
     * forms term_id.
	 */
	$form = get_form( $name );

	if ( ! empty( $form ) ) {
        return true;
    }

	do_action( 'omg_forms_create_form', $name, $slug );

}


