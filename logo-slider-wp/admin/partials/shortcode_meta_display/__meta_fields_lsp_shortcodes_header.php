<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="lgx_logo_slider_card_header">

    <div class="lgx_logo_slider_logo_container">
        <img 
    src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . '../img/lgx-logo-metabox.png' ); ?>" 
    alt="<?php esc_attr_e( 'Logo Showcase', 'logo-slider-wp' ); ?>" 
    style="width: 185px;"
>
    </div>

    <div class="lgx_logo_slider_support_container">
        <a href="<?php echo esc_url('https://logichunt.com/support'); ?>" target="_blank">
            <i class="far fa-envelope-open"></i>
            <i class="lgxicon lgx-icon-envelope-open-o"></i>
            <span>
                <?php esc_html_e('Support','logo-slider-wp'); ?>
            </span>
        </a>
    </div>

</div><!-- ./lgx_logo_slider_card_header -->