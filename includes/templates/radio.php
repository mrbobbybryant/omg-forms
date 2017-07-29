<fieldset>
    <legend><?php echo esc_html( $label ); ?></legend>
    <?php foreach( $options as $option ) : ?>
        <label>
            <?php echo esc_html( $option['label'] ); ?>
            <input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $option['value'] ) ?>"/>
        </label>

    <?php endforeach ?>
</fieldset>