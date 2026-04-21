<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Basic
$lgx_from_category   = $lgx_shortcodes_meta['lgx_from_category'];
$lgx_brand_name_en   = $lgx_shortcodes_meta['lgx_brand_name_en'];
$lgx_brand_desc_en   = $lgx_shortcodes_meta['lgx_brand_desc_en'];
$lgx_company_url_en  = $lgx_shortcodes_meta['lgx_company_url_en'];
$lgx_target_type     = $lgx_shortcodes_meta['lgx_target_type'];
$lgx_nofollow_en     = $lgx_shortcodes_meta['lgx_nofollow_en'];

// Style
$lgx_item_hover_anim = $lgx_shortcodes_meta['lgx_item_hover_anim'];

// Tooltip
$lgx_tooltip_content_type = $lgx_shortcodes_meta['lgx_tooltip_content_type'];

// Post data
$post_id    = get_the_ID();
$metavalues = get_post_meta( $post_id, '_logosliderwpmeta', true );

// ✅ FIXED: use $metavalues (not $fieldValues) and NO escaping here
$company_name = ! empty( $metavalues['company_name'] ) ? $metavalues['company_name'] : '';
$company_url  = ! empty( $metavalues['company_url'] ) ? $metavalues['company_url'] : '';
$tooltip_text = ! empty( $metavalues['tooltip_text'] ) ? $metavalues['tooltip_text'] : '';
$company_desc = ! empty( $metavalues['company_desc'] ) ? $metavalues['company_desc'] : '';

// Thumbnail
$thumb_url = '';
if ( has_post_thumbnail( $post_id ) ) {
    $thumb_data = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
    $thumb_url  = ! empty( $thumb_data[0] ) ? $thumb_data[0] : '';
}

// Condition Content
$tooltip_content = ( $lgx_tooltip_content_type === 'brand_name' ) ? $company_name : $tooltip_text;

// URL handling (keep safe)
$lgx_item_url = ( 'yes' === $lgx_company_url_en && ! empty( $company_url ) )
    ? $company_url
    : 'javascript:void(0);';

// rel attribute
$lgx_url_rel = ( 'yes' === $lgx_nofollow_en ) ? 'rel="nofollow"' : '';

?>

<div class="lgx_app_item <?php echo ( 'carousel' === $lgx_showcase_type ) ? 'swiper-slide' : ''; ?> lgx_app_hover_effect_<?php echo esc_attr( $lgx_shortcodes_meta['lgx_item_hover_effect'] ); ?>">

    <div 
        class="lgx_app_item_inner <?php echo ( 'yes' === $lgx_tooltip_en ) ? 'lgx_app_item_tooltip' : ''; ?>"
        <?php echo ( 'yes' === $lgx_tooltip_en ) ? 'title="' . esc_attr( $tooltip_content ) . '"' : ''; ?>
    >

        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <div class="lgx_app_item_figure lgx_img_hover_anim__<?php echo esc_attr( $lgx_item_hover_anim ); ?>">

                <a class="lgx_app_item_link"
                    <?php echo wp_kses_post( $lgx_url_rel ); ?>
                    href="<?php echo esc_url( $lgx_item_url ); ?>"
                    target="<?php echo esc_attr( $lgx_target_type ); ?>"
                >

                    <img 
                        class="lgx_app_item_img"
                        src="<?php echo esc_url( $thumb_url ); ?>"
                        alt="<?php echo esc_attr( $company_name ); ?>"
                    />

                </a>
            </div>
        <?php endif; ?>

        <div class="lgx_app_item_info">

            <?php if ( 'yes' === $lgx_brand_name_en && ! empty( $company_name ) ) : ?>
                <h4 class="lgx_app_item_title">
                    <?php echo esc_html( $company_name ); ?>
                </h4>
            <?php endif; ?>

            <?php if ( 'yes' === $lgx_brand_desc_en && ! empty( $company_desc ) ) : ?>
                <div class="lgx_app_item_desc">
                    <?php echo esc_html( $company_desc ); ?>
                </div>
            <?php endif; ?>

        </div>

        <?php if (
            $lgx_shortcodes_meta['lgx_item_hover_effect'] === 'overlay_link' ||
            $lgx_shortcodes_meta['lgx_item_hover_effect'] === 'overlay_title'
        ) : ?>
            <div class="lgx_app_item_overlay_wrap">
                <div class="lgx_app_item_overlay">

                    <?php if ( 'yes' === $lgx_brand_name_en && $lgx_shortcodes_meta['lgx_item_hover_effect'] === 'overlay_title' ) : ?>
                        <h4 class="lgx_app_item_title lgx_app_item_overlay_title">
                            <?php echo esc_html( $company_name ); ?>
                        </h4>
                    <?php endif; ?>

                    <a class="lgx_app_item_overlay_link"
                        <?php echo wp_kses_post( $lgx_url_rel ); ?>
                        href="<?php echo esc_url( $lgx_item_url ); ?>"
                        target="<?php echo esc_attr( $lgx_target_type ); ?>"
                    >
                        <span><i class="fa fa-link" aria-hidden="true"></i></span>
                    </a>

                </div>
            </div>
        <?php endif; ?>

    </div>
</div>