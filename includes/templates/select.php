<label id="<?php echo esc_attr( $name ); ?>">
    <span class="omg-error"><?php echo esc_html( $error ); ?></span>
    <?php echo esc_html( $label ); ?>
    <select name="<?php echo esc_attr( $name ) ?>" <?php echo \OMGForms\Helpers\maybe_required( $required ); ?>>
        <option value=""></option>
        <?php foreach( $options as $option ) : ?>
            <option value="<?php echo esc_attr( $option[ 'value' ] ) ?>">
                <?php echo esc_html( $option[ 'label' ] ); ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>