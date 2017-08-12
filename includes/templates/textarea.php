<label id="<?php echo esc_attr( $name ); ?>">
    <span class="omg-error"><?php echo esc_html( $error ); ?></span>
	<?php echo esc_html ( $label ); ?>
	<textarea
        name="<?php echo esc_attr( $name ) ?>"
        placeholder="<?php echo esc_attr( $placeholder ); ?>"
        <?php echo \OMGForms\Helpers\maybe_required( $required ); ?>></textarea>
</label>