<?php $checkbox_name = $name . '[]'; ?>
<fieldset id="<?php echo esc_attr( $name ); ?>" class="omg-forms-input">
    <span class="omg-error"><?php echo esc_html( $error ) ?></span>
	<legend><?php echo esc_html( $label ); ?></legend>
	<?php foreach( $options as $option ) : ?>
		<label>
			<?php echo esc_html( $option['label'] ); ?>
			<input
                type="checkbox"
                name="<?php echo $checkbox_name; ?>"
                value="<?php echo esc_attr( $option['value'] ) ?>"
				<?php echo \OMGForms\Helpers\maybe_required( $required ); ?> />
		</label>

	<?php endforeach ?>
</fieldset>
