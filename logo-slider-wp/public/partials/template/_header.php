<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Values (safe fallback)
$lgx_brand_align     = $lgx_shortcodes_meta['lgx_header_align'] ?? '';
$lgx_header_title    = $lgx_shortcodes_meta['lgx_header_title'] ?? '';
$lgx_header_subtitle = $lgx_shortcodes_meta['lgx_header_subtitle'] ?? '';
?>

<div class="lgx_app_header lax_app_text_<?php echo esc_attr( $lgx_brand_align ); ?>">

    <?php if ( ! empty( $lgx_header_title ) ) : ?>
        <h2 class="lgx_app_header_title">
            <?php echo esc_html( $lgx_header_title ); ?>
        </h2>
    <?php endif; ?>

    <?php if ( ! empty( $lgx_header_subtitle ) ) : ?>
        <div class="lgx_app_header_subtitle">
            <?php echo esc_html( $lgx_header_subtitle ); ?>
        </div>
    <?php endif; ?>

</div><!-- //.HEADER END -->