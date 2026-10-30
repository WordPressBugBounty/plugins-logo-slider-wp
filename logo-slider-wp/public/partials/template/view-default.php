<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Create date : 29.06.2026, By : @VibeLogic
 * Security hardening: added ABSPATH guard, output escaping, wp_kses_post, esc_html_e
 */
?>

<div id="lgx_logo_slider_app_<?php echo esc_attr($lgx_app_id);?>" class="lgx_logo_slider_app">

    <?php  echo(('yes'==$lgx_shortcodes_meta['lgx_preloader_en']) ? '<div id="lgx_lsw_preloader_'.esc_attr($lgx_app_id).'" class="lgx_lsw_preloader"> <img src="'.esc_url((!empty($lgx_shortcodes_meta['lgx_preloader_icon'])) ? $lgx_shortcodes_meta['lgx_preloader_icon']: $lgx_lsw_loading_icon).'" /></div>' : ''); ?>

    <div class="lgx_logo_slider lgx_logo_slider_<?php echo lgx_ls_is_pro() ? 'pro' : 'free'; ?>">
        <div class="lgx_app_inner lgx_app_layout_<?php echo esc_attr($lgx_showcase_type);?> ">
            <div class="lgx_app_<?php echo esc_attr($lgx_shortcodes_meta['lgx_section_container']);?> <?php echo (('grid' == $lgx_showcase_type) ? 'lgx_grid_style_'.esc_attr($lgx_grid_style) : '' );?>">

                <?php (($lgx_shortcodes_meta['lgx_header_en']=='yes') ? include '_header.php' : ''); ?>
             
                <div class="lgx_app_content_area lgx_logo_position_<?php echo esc_attr($lgx_logo_position);?> lgx_logo_info_align_<?php echo esc_attr($lgx_logo_info_align);?> ">
                    <div id="lgx_app_content_wrap_<?php echo esc_attr($lgx_app_id. rand ( 100, 999 ));?>" class="lgx_app_content_wrapper  <?php echo (('carousel' == $lgx_showcase_type) ? 'swiper lgx_logo_carousel' : '');?>"  <?php echo (('carousel' == $lgx_showcase_type) ? wp_kses_post($carouselDataAttr_str) : '');?>  <?php echo (('yes' == $carousel_rtl_en) ? 'dir="rtl"' : '');?> >

                        <?php echo ((('top_center' == $lgx_shortcodes_meta['lgx_carousel_nav_position']) || ('top_left' == $lgx_shortcodes_meta['lgx_carousel_nav_position']) || ('top_right' == $lgx_shortcodes_meta['lgx_carousel_nav_position'])) ? wp_kses_post($carousel_navigation) : '');?>

                        <div class="lgx_app_item_row  <?php echo (('yes' == $lgx_carousel_ticker_en) ? 'lgx_swiper_wrapper_ticker' : '');?> <?php echo (('carousel' == $lgx_showcase_type) ? 'swiper-wrapper' : '');?> <?php echo ((('mid' == $lgx_shortcodes_meta['lgx_carousel_item_vertical_align']) && ('carousel' == $lgx_showcase_type) ) ? 'lgx_s_w_vertical_mid' : '');?>" <?php echo (('yes' == $lgx_tooltip_en) ? wp_kses_post($tooltipDataAttr_str) : '');?> >
                            <?php

                

                       //$lgx_logo_order      =  ( isset($lgx_item_sort_order) ? $lgx_item_sort_order : 'ASC');
                        //$lgx_logo_order_by    = 'title';
                      

                                // Before (v5.3) — free version was capped at 20 logos:
                        //
                        // $lgx_logo_limit = ( ( $lgx_shortcodes_meta['lgx_item_limit'] <= 0 ) ? -1 : $lgx_shortcodes_meta['lgx_item_limit'] );
                        //
                        // if ( LGX_LS_WP_PLUGIN !== 'logo-slider-wp-pro' ) {
                        //     if ( $lgx_logo_limit === -1 || $lgx_logo_limit >= 20 ) {
                        //         $lgx_logo_limit = 20;
                        //     }
                        // }

                        // After (v5.5) — no fixed front-end cap in the free version (the logo limit is a showcase setting):
                        // WP1.6 / WP1.7 / WP1.8: the stored source is resolved once. The Logo Library (or a missing value) gives a WP_Query,
                        // the Brand source (when the taxonomy exists) gives an array of items; any other case shows the neutral visitor sentence,
                        // plus an editor hint if permitted. Nothing is written.
                        $lgx_source_value    = isset( $lgx_shortcodes_meta['lgx_lswp_content_source'] ) ? $lgx_shortcodes_meta['lgx_lswp_content_source'] : null;
                        unset( $lgx_item_context );
                        $lgx_source_provider = lgx_ls_resolve_source_provider( $lgx_source_value );
                        $lgx_source_result   = ( null === $lgx_source_provider ) ? null : $lgx_source_provider->get_items( $lgx_shortcodes_meta );

                        if ( $lgx_source_result instanceof WP_Query ) {
                            $lgx_logo_slider_loop = $lgx_source_result;

                            if ( $lgx_logo_slider_loop->have_posts() ){
                                while ( $lgx_logo_slider_loop->have_posts() ) : $lgx_logo_slider_loop->the_post();


                                    // Add Item
                                    include('_item.php');


                                endwhile;
                                wp_reset_postdata();// Restore original Post Data
                            } // Check post exist
                            else{
                                esc_html_e('There are no logo item. Please add some logo Item', 'logo-slider-wp');
                            }
                        } elseif ( is_array( $lgx_source_result ) && isset( $lgx_source_result['items'] ) && is_array( $lgx_source_result['items'] ) && ! empty( $lgx_source_result['items'] ) ) {
                            // Brand source: the item array is the item context of the card; no global post is touched.
                            foreach ( $lgx_source_result['items'] as $lgx_item_context ) {
                                include('_item.php');
                            }
                            unset( $lgx_item_context );
                        } else {
                            $lgx_empty_reason = ( is_array( $lgx_source_result ) && isset( $lgx_source_result['reason'] ) && is_string( $lgx_source_result['reason'] ) ) ? $lgx_source_result['reason'] : '';
                            $lgx_showcase_id  = absint( $lgx_app_id );
                            $lgx_is_editor    = ( $lgx_showcase_id > 0 && current_user_can( 'edit_post', $lgx_showcase_id ) );
                            $lgx_empty_state  = lgx_ls_get_empty_state( $lgx_source_value, $lgx_is_editor, $lgx_empty_reason );
                            echo '<div class="lgx-ls-source-state"><p class="lgx-ls-source-empty">' . esc_html( $lgx_empty_state['visitor'] ) . '</p>';
                            if ( '' !== $lgx_empty_state['hint'] ) {
                                echo '<p class="lgx-ls-source-hint">' . esc_html( $lgx_empty_state['hint'] ) . '</p>';
                            }
                            echo '</div>';
                        } // Source resolved
                            ?>
                        </div> <!--//.APP CONTENT INNER END-->

                        <?php  echo ((('bottom_center' == $lgx_shortcodes_meta['lgx_carousel_nav_position']) || ('bottom_left' == $lgx_shortcodes_meta['lgx_carousel_nav_position']) || ('bottom_right' == $lgx_shortcodes_meta['lgx_carousel_nav_position'])) ? wp_kses_post($carousel_navigation) : '');?>

                        <?php echo wp_kses_post($carousel_pagination)?>

                        <?php echo wp_kses_post($carousel_ticker_pause_control)?>

                    </div> <!-- //.CONTENT WRAP END-->
                
                <?php  echo ((('vertical_center' == $lgx_shortcodes_meta['lgx_carousel_nav_position'])) ? wp_kses_post($carousel_navigation) : '');?>

            </div> <!-- //.CONTENT AREA END-->

            </div><!--//.APP CONTAINER END-->
        </div> <!--//.INNER END-->
    </div> <!-- APP CONTAINER END -->

</div> <!--//.APP END-->