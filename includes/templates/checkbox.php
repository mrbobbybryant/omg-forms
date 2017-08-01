<label>
    <?php echo esc_html( $label ); ?>
    <input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $slug ) ?>" <?php echo $required ?>/>
</label>