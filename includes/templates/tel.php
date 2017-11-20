<label id="<?php echo esc_attr( $name ); ?>" class="omg-forms-input">
	<span class="omg-error"><?php echo esc_html( $error ); ?></span>
	<?php echo esc_html ( $label ); ?>
	<input
        type="tel"
        name="<?php echo esc_attr( $name ) ?>"
        placeholder="<?php echo esc_attr( $placeholder ); ?>"
		<?php echo \OMGForms\Helpers\maybe_required( $required ); ?> />
</label>