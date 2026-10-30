<div class="lgx_row">

    <div class="lgx_col_6">
        <div class="lgx_logo_slider_info_box">
            <h3 class="lgx_logo_slider_header"><?php esc_html_e( 'Shortcode', 'logo-slider-wp' ); ?> <span style="font-size: 13px; font-weight: normal; color: #646970;">(ID: <strong><?php echo absint( $post->ID ); ?></strong>)</span></h3>
            <p><?php esc_html_e( 'Copy and paste this shortcode into your posts or pages', 'logo-slider-wp' ); ?></p>
            <div class="lgx-copy-group" style="display: flex; justify-content: space-between; gap: 10px; align-items: center;">
                <input type="text" aria-label="<?php esc_attr_e( 'Shortcode to copy', 'logo-slider-wp' ); ?>" class="lgx-input lgx-copy-input lgx_logo_slider_short_code selectable" value="<?php echo esc_attr( '[lgxlogoslider id="' . $post->ID . '"]' ); ?>" readonly style="flex: 1 1 auto; min-width: 0; box-sizing: border-box;">
                <button type="button" class="button lgx-btn lgx-btn-secondary lgx-copy-btn" style="flex-shrink: 0;" aria-label="<?php esc_attr_e( 'Copy shortcode', 'logo-slider-wp' ); ?>">
                    <i class="lgxicon lgx-icon-copy"></i> <?php esc_html_e( 'Copy', 'logo-slider-wp' ); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="lgx_col_6">
        <div class="lgx_logo_slider_info_box">
            <h3 class="lgx_logo_slider_header"><?php esc_html_e( 'Theme or Plugin', 'logo-slider-wp' ); ?></h3>
            <p><?php esc_html_e( 'Copy and paste this PHP code into your Theme or Plugin file', 'logo-slider-wp' ); ?></p>
            <div class="lgx-copy-group" style="display: flex; justify-content: space-between; gap: 10px; align-items: center;">
                <input type="text" aria-label="<?php esc_attr_e( 'PHP template code to copy', 'logo-slider-wp' ); ?>" class="lgx-input lgx-copy-input lgx_logo_slider_short_code selectable" value="<?php echo esc_attr( '<?php echo do_shortcode( \'[lgxlogoslider id="' . $post->ID . '"]\' ); ?>' ); ?>" readonly style="flex: 1 1 auto; min-width: 0; box-sizing: border-box;">
                <button type="button" class="button lgx-btn lgx-btn-secondary lgx-copy-btn" style="flex-shrink: 0;" aria-label="<?php esc_attr_e( 'Copy PHP template code', 'logo-slider-wp' ); ?>">
                    <i class="lgxicon lgx-icon-copy"></i> <?php esc_html_e( 'Copy', 'logo-slider-wp' ); ?>
                </button>
            </div>
        </div>
    </div>

</div><!-- ./lgx_row -->