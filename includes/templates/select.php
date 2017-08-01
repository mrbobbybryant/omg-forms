<label>
    <?php echo esc_html( $label ); ?>
    <select name="<?php echo esc_attr( $name ) ?>" <?php echo $required ?>>
        <option value=""></option>
        <?php foreach( $options as $option ) : ?>
            <option value="<?php echo esc_attr( $option[ 'value' ] ) ?>">
                <?php echo esc_html( $option[ 'label' ] ); ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>