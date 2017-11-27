<?php
namespace OMGForms\Core;

use OMGForms\Template;
use OMGForms\IA;
use OMGForms\Helpers;

function register_form( $args ) {
	global $omg_forms;

	/**
	 * Filters the form arguments passed when a new form is registered.
	 *
	 * Allows you an opportunity to modify the arguments prior to validation.
	 *
	 * @since 0.2.0
	 *
	 * @param array         $args An array of all the form arguments submitted
	 *                            when calling register_form.
	 */
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

	$form_name = strtolower( $args['name'] );

    create_form( $form_name, $args );

	$omg_forms[ $form_name ] = $args;
}

function display_form( $slug ) {
    $args = get_form( $slug );

	if ( empty( $args ) ) {
		trigger_error( 'A form with that name does not exist.', E_USER_ERROR );
	}

	if ( ! isset( $args[ 'fields' ] ) ) {
		trigger_error( 'You must register at least of field for a form to be valid.', E_USER_ERROR );
	}

    $redirect = Helpers\get_redirect_attribute( $args );
    $rest_type = Helpers\get_form_rest_attribute( $args );
    $form_type = Helpers\get_form_type_attribute( $args );
    $form_classname = ( isset( $args[ 'classname' ] ) ) ? sprintf( 'omg-form %s', $args[ 'classname' ] ) : 'omg-form';
	$fields = order_fields_by_group( $args['fields'], $args );

	ob_start(); ?>
    <div class="omg-form-wrapper" <?php echo esc_attr( $redirect ); ?> <?php echo esc_attr( $rest_type ); ?> <?php echo esc_attr( $form_type ); ?>>

		<?php
		/**
	 	 * Fires before the HTML Form tag and after the Form Wrapper element.
		 *
		 * Hook is useful if you need to prepend some form of HTML before the form element.
		 *
		 * @since 0.2.0
		 *
		 */
		?>
        <?php do_action( 'omg_form_before_form' ); ?>

        <?php if ( isset( $args['success_message'] ) ) : ?>
         <p class="omg-success">
             <?php echo esc_html( $args['success_message'] ) ?>
         </p>
        <?php endif; ?>
        <form class="<?php echo esc_attr( $form_classname ); ?>" action="" id="<?php echo esc_attr( $args['name'] ) ?>">
	        <?php if ( isset( $args[ 'groups' ] ) && ! empty( $args[ 'groups' ] ) ) {
                echo build_form_groups( $fields );
	        } else {
		        foreach( $fields as $field ) :
			        echo get_field_template( Template\get_template_name( $field[ 'type' ] ), $field );
		        endforeach;
	        }

			/**
			 * Fires before the Form Submit button and error message section.
			 *
			 * Hook is useful if you need to add additional form fields to a form of a certain type.
			 *
			 * @since 0.2.0
			 *
			 * @param string $slug      The current form name.
 		 	 * @param array $args       An array of all the form arguments submitted
			 *                          when calling register_form..
			 */

		    do_action( 'omg_form_before_form_submit', $slug, $args ); ?>

	        <p id="omg-form-level-error" class="omg-form-error"></p>
            <input type="checkbox" name="omg-forms-contact_by_mail" value="1" style="display:none !important" tabindex="-1" autocomplete="off">

		    <?php echo get_field_template( Template\get_template_name( 'submit' ), [] ); ?>
        </form>

		<?php
		/**
		 * Fires after the HTML Form Element.
		 *
		 * Hook is useful if you need to add additional HTML after the form
		 * but before the closing of the OMG Form wraper element.
		 *
		 * @since 0.2.0
		 *
		 * @param string $slug      The current form name.
		 * @param array $args       An array of all the form arguments submitted
		 *                          when calling register_form..
		 */
		?>
        <?php do_action( 'omg_form_after_form', $slug, $args ); ?>
    </div>


	<?php return ob_get_clean();
}

function get_form( $slug ) {
	global $omg_forms;

	$slug = strtolower( $slug );

	if ( empty( $omg_forms ) || ! isset( $omg_forms[ $slug ] ) ) {
		return false;
	}

	$form = apply_filters( 'omg_forms_get_form', $omg_forms[ $slug ], $slug );

	if ( ! empty( $omg_forms ) && isset( $omg_forms[ $slug ] ) ) {
		return $form;
	}
    //TODO This needs be changed. This function needs to return the whole form object.
	return [ 'ID' => $form->term_id ];
}

function create_form( $slug, $args ) {
    $slug = strtolower( $slug );
	$name = Helpers\get_form_name( $slug );

	/**
	 * Check to see if this form already exists. If it does, then just return the existing
     * forms term_id.
	 */
	$form = get_form( $name );

	if ( ! empty( $form ) ) {
        return true;
    }

	do_action( 'omg_forms_create_form', $slug, $args );

}

function order_fields_by_group( $fields, $form ) {
	if ( ! isset( $form[ 'groups' ] ) || empty( $form[ 'groups' ] ) ) {
		return $fields;
	}

	$groups = array_reduce( $fields, function( $prev, $cur ) use ( $form ) {
		if ( ! isset( $cur[ 'group' ] ) ) {
			return $prev;
		}

		$group = Helpers\get_form_group( $form['groups'], $cur[ 'group' ] );

		if ( ! empty( $group ) ) {
			$prev[ $cur[ 'group' ] ]['fields'][] = $cur;
			$prev[ $cur[ 'group' ] ] = wp_parse_args( $group, $prev[ $cur[ 'group' ] ] );
		}

		return $prev;
	}, [] );

	if ( ! empty( $groups ) ) {
		uasort( $groups, __NAMESPACE__ . '\order_groups' );
	}

	return $groups;

}

function order_groups( $group_a, $group_b ) {
	if ( $group_a[ 'order' ] === $group_b[ 'order' ] ) {
		return 0;
	}

	return $group_a[ 'order' ] < $group_b[ 'order' ] ? -1 : 1;
}

function build_form_groups( $fields ) {
    ob_start();

    foreach( $fields as $group ) : ?>
        <fieldset class="<?php echo ( isset( $group[ 'class' ] ) )  ? esc_attr( $group[ 'class' ] ) : ''; ?>">
            <?php if ( isset( $group[ 'title' ] ) ) : ?>
                <legend><?php echo esc_html( $group['title'] ); ?></legend>
            <?php endif;
            foreach( $group[ 'fields' ] as $field ) :
                echo get_field_template( Template\get_template_name( $field[ 'type' ] ), $field );
            endforeach; ?>
        </fieldset>
	<?php endforeach;

	return ob_get_clean();
}
