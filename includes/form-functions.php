<?php
namespace OMGForms\Plugin\Core;



function register_form( $args ) {
	ob_start(); ?>

	<form action="" name="<?php echo esc_attr( $args['name'] ) ?>">
		<?php foreach( $args['fields'] as $field ) :
			if ( 'text' === $field['type'] ) {
				echo output_text_field( $field );
			}

			if ( 'email' === $field['type'] ) {
				echo output_email_field( $field );
			}

			if ( 'textarea' === $field['type'] ) {
				echo output_textarea( $field );
			}
		endforeach; ?>
		<input type="submit" name="omg-form-submit-btn" class="omg-form-submit-btn" />
	</form>

	<?php return ob_get_clean();
}

function omg_locate_template( $name ) {
    $child = sprintf( '%s/forms/%s' , get_stylesheet_directory(), $name );
    $parent = sprintf( '%s/forms/%s', get_template_directory(), $name );
    $core = sprintf( '%s/includes/templates/%s', OMG_PLUGIN_DIR, $name );

    if ( file_exists( $child ) ) {
        return $child;
    } else if ( $parent ) {
        return $parent;
    } else {
        return $core;
    }
}

function output_text_field( $settings ) {
    $file = omg_locate_template( $settings[ 'name' ] );
	$name = sprintf( 'omg-forms-%s', $settings[ 'name' ]  );
	$required = ( true === $settings['required'] ) ? 'data-required="1"' : 'data-required="0"';
	ob_start(); ?>

	<label>
		<?php echo esc_html ( $settings['label'] ); ?>
		<input type="text" name="<?php echo esc_attr( $name ) ?>" <?php echo $required ?>/>
	</label>

	<?php return ob_get_clean();
}

function output_email_field( $settings ) {
	$name = sprintf( 'omg-forms-%s', $settings[ 'name' ]  );
	$required = ( true === $settings['required'] ) ? 'data-required="1"' : 'data-required="0"';
	ob_start(); ?>

	<label>
		<?php echo esc_html ( $settings['label'] ); ?>
		<input type="email" name="<?php echo esc_attr( $name ) ?>" <?php echo $required; ?>/>
	</label>

	<?php return ob_get_clean();
}

function output_textarea( $settings ) {
	$name = sprintf( 'omg-forms-%s', $settings[ 'name' ]  );
	$required = ( true === $settings['required'] ) ? 'data-required="1"' : 'data-required="0"';
	ob_start(); ?>

	<label>
		<?php echo esc_html ( $settings['label'] ); ?>
		<textarea name="<?php echo esc_attr( $name ) ?>" <?php echo $required; ?>></textarea>
	</label>

	<?php return ob_get_clean();
}