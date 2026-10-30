<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * moved from public\partials\template\view-default.php lines 46-77 (WP1.A, no behaviour change).
 */
class Logo_Slider_WP_Source_Logo_Library implements Logo_Slider_WP_Source_Provider {

    public function is_available() {
        return true;
    }

    /**
     * returns a WP_Query object whose posts the template loops with have_posts() and the_post(); does not call the_post() and does not call wp_reset_postdata().
     */
    public function get_items( $showcase_settings ) {
        $lgx_item_sort_order    = (isset($showcase_settings['lgx_item_sort_order']) ? $showcase_settings['lgx_item_sort_order'] : 'ASC');
        $lgx_item_sort_order_by = (isset($showcase_settings['lgx_item_sort_order_by']) ? $showcase_settings['lgx_item_sort_order_by'] : 'menu_order');

        // After (v5.5) — no front-end limit in free version:
        $lgx_logo_limit = (($showcase_settings['lgx_item_limit'] <= 0) ? -1 : $showcase_settings['lgx_item_limit']);

        $lgx_from_category   = $showcase_settings['lgx_from_category'];

        $lgx_logo_slider_args = array(
            'post_type'         => array( 'logosliderwp' ),
            'post_status'       => array( 'publish' ),
            'order'             => $lgx_item_sort_order,
            'orderby'           => $lgx_item_sort_order_by,
            'posts_per_page'    => $lgx_logo_limit
        );


        // Category to Array Convert
        if( !empty($lgx_from_category) && $lgx_from_category != '' && $lgx_from_category != 'all'  ){
            $lgx_from_category = trim($lgx_from_category);
            $lgx_from_category_arr   = explode(',', $lgx_from_category);

            if(is_array($lgx_from_category_arr) && sizeof($lgx_from_category_arr) > 0){
                $lgx_logo_slider_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'logosliderwpcat',
                        //  'field'    => 'slug',
                        'field'    => 'id',
                        'terms'    => $lgx_from_category_arr
                    )
                );
            }
        }

        // The  Query
        return new WP_Query( $lgx_logo_slider_args );
    }
}
