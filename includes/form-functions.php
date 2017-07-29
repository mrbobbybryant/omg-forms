<?php
namespace OMGForms\Plugin\Core;

use OMGForms\Plugin\Template;

function register_form( $args ) {
	ob_start(); ?>

	<form action="" name="<?php echo esc_attr( $args['name'] ) ?>">
		<?php foreach( $args['fields'] as $field ) :
            echo get_field( Template\get_template_name( $field[ 'type' ] ), $field );
		endforeach; ?>
		<input type="submit" name="omg-form-submit-btn" class="omg-form-submit-btn" />
	</form>

	<?php return ob_get_clean();
}

function format_field( $field ) {
	$field[ 'name' ] = sprintf( 'omg-forms-%s', $field[ 'slug' ]  );
	$field[ 'required' ] = ( true === $field['required'] ) ? 'data-required="1"' : 'data-required="0"';
	return $field;
}

function get_field( $field_type, $settings ) {
    $field_settings = format_field( $settings );
	return Template\get_template_part( $field_type, $field_settings );
}
