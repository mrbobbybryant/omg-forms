<?php $checkbox_name = sprintf( '%[]', $name ); ?>
<fieldset>
	<legend><?php echo esc_html( $label ); ?></legend>
	<?php foreach( $options as $option ) : ?>
		<label>
			<?php echo esc_html( $option['label'] ); ?>
			<input type="checkbox" name="<?php echo $checkbox_name; ?>" value="<?php echo esc_attr( $option['value'] ) ?>" <?php echo $required ?>/>
		</label>

	<?php endforeach ?>
</fieldset>
