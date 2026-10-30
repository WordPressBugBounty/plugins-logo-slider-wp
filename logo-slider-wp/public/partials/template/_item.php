<?php
if (!defined('WPINC')) {
    die;
}

//Basic
$lgx_from_category   = $lgx_shortcodes_meta['lgx_from_category'];
$lgx_brand_name_en   = $lgx_shortcodes_meta['lgx_brand_name_en'];
$lgx_brand_desc_en   = $lgx_shortcodes_meta['lgx_brand_desc_en'];
$lgx_company_url_en  = $lgx_shortcodes_meta['lgx_company_url_en'];
$lgx_target_type     = $lgx_shortcodes_meta['lgx_target_type'];
$lgx_nofollow_en     = $lgx_shortcodes_meta['lgx_nofollow_en'];

//Style
$lgx_item_hover_anim            = $lgx_shortcodes_meta['lgx_item_hover_anim'];


// Tooltip
$lgx_tooltip_content_type   = $lgx_shortcodes_meta['lgx_tooltip_content_type'];

//Post data
$lgx_has_item_context = ( isset( $lgx_item_context ) && is_array( $lgx_item_context ) );
if ( $lgx_has_item_context ) {
    // Brand item (WP1.7): the item array set by the template is the source of every value; a missing or non-text key counts as empty.
    $lgx_ctx_name    = ( isset( $lgx_item_context['name'] ) && is_string( $lgx_item_context['name'] ) ) ? $lgx_item_context['name'] : '';
    $lgx_ctx_url     = ( isset( $lgx_item_context['url'] ) && is_string( $lgx_item_context['url'] ) ) ? $lgx_item_context['url'] : '';
    $lgx_ctx_tooltip = ( isset( $lgx_item_context['tooltip'] ) && is_string( $lgx_item_context['tooltip'] ) ) ? $lgx_item_context['tooltip'] : '';
    $lgx_ctx_alt     = ( isset( $lgx_item_context['alt'] ) && is_string( $lgx_item_context['alt'] ) ) ? $lgx_item_context['alt'] : '';
    $lgx_ctx_desc    = ( isset( $lgx_item_context['desc'] ) && is_string( $lgx_item_context['desc'] ) ) ? $lgx_item_context['desc'] : '';
    $lgx_ctx_image   = ( isset( $lgx_item_context['image_url'] ) && is_string( $lgx_item_context['image_url'] ) ) ? $lgx_item_context['image_url'] : '';

    $company_name       = $lgx_ctx_name;
    $company_url        = ( '' !== $lgx_ctx_url ) ? $lgx_ctx_url : '#';
    $tooltip_text       = ( '' !== $lgx_ctx_tooltip ) ? $lgx_ctx_tooltip : $lgx_ctx_name;
    $alt_text           = ( '' !== $lgx_ctx_alt ) ? $lgx_ctx_alt : $lgx_ctx_name;
    $company_desc       = $lgx_ctx_desc;
    $thumb_url          = $lgx_ctx_image;
    $lgx_has_image      = ( isset( $lgx_item_context['has_logo'] ) && true === $lgx_item_context['has_logo'] && '' !== $lgx_ctx_image );
    $lgx_name_as_text   = ( ! $lgx_has_image && isset( $lgx_item_context['name_as_text'] ) && true === $lgx_item_context['name_as_text'] && '' !== $company_name );
} else {
    $post_id            = get_the_ID();
    $post_title         = get_the_title( $post_id );
    $metavalues         = get_post_meta( $post_id, '_logosliderwpmeta', true );

    $company_name       = ( (!empty($metavalues['company_name'])) ? $metavalues['company_name'] : $post_title );
    $company_url        = ( (isset($metavalues['company_url'])) ? $metavalues['company_url'] : '#' );
    $tooltip_text       = ( (!empty($metavalues['tooltip_text'])) ? $metavalues['tooltip_text'] : $post_title );
    $alt_text           = ( (!empty($metavalues['alt_text'])) ? $metavalues['alt_text'] : $company_name );
    $company_desc        = (!empty($metavalues['company_desc']) ? $metavalues['company_desc'] : '');
    $thumb_url          = '';
    if (has_post_thumbnail( $post_id )) {

        $thumb_url          = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id), true );
        $thumb_url          = $thumb_url[0];
    }
    $lgx_has_image      = has_post_thumbnail( $post_id );
    $lgx_name_as_text   = false;
}

// Condition Content
$tooltip_content = (($lgx_tooltip_content_type == 'brand_name') ? $company_name : $tooltip_text);
$lgx_item_url    = ((('yes' == $lgx_company_url_en) && ( !empty($company_url)) && ('#' !== $company_url) )? esc_url($company_url) : '');
$lgx_url_rel     = (('yes' == $lgx_nofollow_en) ? 'rel="nofollow" ' : '');

?>
<div class="lgx_app_item  <?php echo (('carousel' == $lgx_showcase_type) ? 'swiper-slide' : '');?> lgx_app_hover_effect_<?php echo esc_attr( $lgx_shortcodes_meta['lgx_item_hover_effect'] );?>">
    <div class="lgx_app_item_inner <?php echo (('yes' == $lgx_tooltip_en) ? 'lgx_app_item_tooltip' : '');?>" <?php echo ((('yes' == $lgx_tooltip_en) && (!empty($tooltip_content)) ) ? 'title="'.esc_attr( $tooltip_content ).'"' : '');?>>
        <?php if ($lgx_has_image):  ?>
                    <div class="lgx_app_item_figure lgx_img_hover_anim__<?php echo esc_attr( $lgx_item_hover_anim );?>">
                    <a class="lgx_app_item_link" <?php echo wp_kses_post( $lgx_url_rel );?> <?php echo (!empty($lgx_item_url) ? 'href="' . esc_url( $lgx_item_url ) . '"' : ''); ?>  target="<?php echo esc_attr( $lgx_target_type );?>" >
                    <img class="lgx_app_item_img <?php echo((('carousel' == $lgx_showcase_type) && ('yes' == $lgx_carousel_lazy_load_en)) ? ' swiper-lazy' : '');?>" src="<?php echo esc_url( $thumb_url ); ?>"  alt="<?php echo esc_attr( $alt_text ); ?>" />
                    <?php echo((('carousel' == $lgx_showcase_type) && ('yes' == $lgx_carousel_lazy_load_en)) ? '<div class="swiper-lazy-preloader '.(('white' == $lgx_shortcodes_meta['lgx_carousel_lazy_spinner_color']) ? 'swiper-lazy-preloader-white' : '').'"></div>' : '');?>
                </a>
            </div> <!--//.FIGURE -->
        <?php elseif ($lgx_name_as_text): ?>
                    <div class="lgx_app_item_figure lgx_img_hover_anim__<?php echo esc_attr( $lgx_item_hover_anim );?>">
                    <a class="lgx_app_item_link" <?php echo wp_kses_post( $lgx_url_rel );?> <?php echo (!empty($lgx_item_url) ? 'href="' . esc_url( $lgx_item_url ) . '"' : ''); ?>  target="<?php echo esc_attr( $lgx_target_type );?>" ><span class="lgx_app_item_name_text"><?php echo esc_html( $company_name ); ?></span></a>
            </div> <!--//.FIGURE -->
        <?php endif; ?>
<div class="lgx_app_item_info">
            <?php echo((('yes' == $lgx_brand_name_en) && ! $lgx_name_as_text) ? '<h4 class="lgx_app_item_title">'.esc_html( $company_name ).'</h4>' : '');?>
            <?php echo(('yes' == $lgx_brand_desc_en) ? '<div class="lgx_app_item_desc">'.esc_html( $company_desc ).'</div>' : '');?>

        </div>

        <?php if(($lgx_shortcodes_meta['lgx_item_hover_effect'] == 'overlay_link') || ($lgx_shortcodes_meta['lgx_item_hover_effect'] == 'overlay_title') || ($lgx_shortcodes_meta['lgx_item_hover_effect'] == 'box_shadow_overlay_link')) : ?>
            <div class="lgx_app_item_overlay_wrap">
                <div class="lgx_app_item_overlay">
                    <?php echo(( ($lgx_shortcodes_meta['lgx_item_hover_effect'] == 'overlay_title')) ? '<h4 class="lgx_app_item_title lgx_app_item_overlay_title ">'.esc_html( $company_name ).'</h4>' : '');?>
                    <a class="lgx_app_item_overlay_link " <?php echo wp_kses_post( $lgx_url_rel );?> <?php echo (!empty($lgx_item_url) ? 'href="' . esc_url( $lgx_item_url ) . '"' : ''); ?>  target="<?php echo esc_attr( $lgx_target_type );?>"><span><i class="fa fa-link" aria-hidden="true"></i></span></a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>