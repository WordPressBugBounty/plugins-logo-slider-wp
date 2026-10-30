<?php
/**
 * The WooCommerce Brand source provider.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 * @since      6.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Logo_Slider_WP_Source_Brand
 *
 * Provider for the WooCommerce Brand content source.
 *
 * @since 6.1.0
 */
class Logo_Slider_WP_Source_Brand implements Logo_Slider_WP_Source_Provider {

    /**
     * Determines whether the WooCommerce Brand source is available in the current environment.
     *
     * @since 6.1.0
     *
     * @return bool True if the source is available, false otherwise.
     */
    public function is_available() {
        return lgx_ls_is_source_available( 'woo_product_brand' );
    }

    /**
     * Queries and returns brand items for the given showcase configuration.
     *
     * Returns a plain result array with 'items' and 'reason' keys.
     * Never calls the_post(), wp_reset_postdata(), or any global post data.
     *
     * @since 6.1.0
     *
     * @param array $showcase_settings The showcase shortcode/block settings array.
     * @return array Array with 'items' (list of item arrays) and 'reason' (empty string or empty-state code).
     */
    public function get_items( $showcase_settings ) {
        // Step 1: Availability check.
        if ( ! $this->is_available() ) {
            return array(
                'items'  => array(),
                'reason' => 'unavailable',
            );
        }

        // Step 2: Read options and parse term IDs.
        $sort_order_by = lgx_ls_read_brand_option( $showcase_settings, 'lgx_woo_product_brand_sort_order_by' );
        $no_logo       = lgx_ls_read_brand_option( $showcase_settings, 'lgx_woo_product_brand_no_logo' );
        $empty_option  = lgx_ls_read_brand_option( $showcase_settings, 'lgx_woo_product_brand_empty' );
        $scope         = lgx_ls_read_brand_option( $showcase_settings, 'lgx_woo_product_brand_scope' );
        $term_ids_str  = lgx_ls_read_brand_option( $showcase_settings, 'lgx_woo_product_brand_term_ids' );

        $id_list = array();
        if ( '' !== $term_ids_str ) {
            $parts = explode( ',', $term_ids_str );
            foreach ( $parts as $p ) {
                $p = trim( $p );
                if ( '' !== $p ) {
                    $id_list[] = (int) $p;
                }
            }
        }

        // Step 3: Scope 'include' with empty ID list returns 'no_brands' without querying.
        if ( 'include' === $scope && empty( $id_list ) ) {
            return array(
                'items'  => array(),
                'reason' => 'no_brands',
            );
        }

        // Step 4: Determine sort direction.
        $direction = 'ASC';
        if ( is_array( $showcase_settings ) && isset( $showcase_settings['lgx_item_sort_order'] ) && 'DESC' === $showcase_settings['lgx_item_sort_order'] ) {
            $direction = 'DESC';
        }

        // Step 5: Query terms with get_terms.
        $get_terms_args = array(
            'taxonomy'   => 'product_brand',
            'hide_empty' => false,
        );

        if ( 'added' === $sort_order_by ) {
            $get_terms_args['orderby'] = 'term_id';
            $get_terms_args['order']   = $direction;
        } elseif ( 'rand' === $sort_order_by ) {
            $get_terms_args['orderby'] = 'name';
            $get_terms_args['order']   = 'ASC';
        } else {
            $get_terms_args['orderby'] = 'name';
            $get_terms_args['order']   = $direction;
        }

        if ( 'include' === $scope ) {
            $get_terms_args['include'] = $id_list;
        } elseif ( 'exclude' === $scope && ! empty( $id_list ) ) {
            $get_terms_args['exclude'] = $id_list;
        }

        $terms = get_terms( $get_terms_args );

        // Step 6: Validate query result.
        if ( is_wp_error( $terms ) || ! is_array( $terms ) || empty( $terms ) ) {
            return array(
                'items'  => array(),
                'reason' => 'no_brands',
            );
        }

        // Step 7: Filter and construct items.
        // The item limit is read first: with a non-random order and a limit, the loop stops once the limit is reached
        // (the first N kept brands in the final order are exactly what the slice in step 10 would keep).
        $limit = 0;
        if ( is_array( $showcase_settings ) && isset( $showcase_settings['lgx_item_limit'] ) && is_numeric( $showcase_settings['lgx_item_limit'] ) ) {
            $limit = (int) $showcase_settings['lgx_item_limit'];
        }
        $stop_at = ( 'rand' !== $sort_order_by && $limit > 0 ) ? $limit : 0;

        $items = array();
        $pos   = 0;
        foreach ( $terms as $term ) {
            // Load the logo posts and their meta for the next 50 brands in one step, so the logo check below reads from cache.
            if ( 0 === $pos % 50 ) {
                $this->prime_logo_posts( array_slice( $terms, $pos, 50 ) );
            }
            $pos++;

            $term_id = (int) $term->term_id;

            // (a) Logo check.
            $thumb     = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );
            $has_logo  = false;
            $image_url = '';
            if ( $thumb >= 1 && wp_attachment_is_image( $thumb ) ) {
                $img_src = wp_get_attachment_image_src( $thumb, true );
                if ( is_array( $img_src ) && isset( $img_src[0] ) && is_string( $img_src[0] ) && '' !== $img_src[0] ) {
                    $has_logo  = true;
                    $image_url = $img_src[0];
                }
            }

            // (b) Skip if brand has no logo and no_logo setting is 'hide'.
            if ( ! $has_logo && 'hide' === $no_logo ) {
                continue;
            }

            // (c) Skip if empty setting is 'hide' and brand has no products.
            if ( 'hide' === $empty_option ) {
                $count_val = get_term_meta( $term_id, 'product_count_product_brand', true );
                if ( is_numeric( $count_val ) ) {
                    $has_products = ( (int) $count_val > 0 );
                } else {
                    $has_products = $this->has_live_products( $term_id );
                }

                if ( ! $has_products ) {
                    continue;
                }
            }

            // (d) Build item array.
            $term_name    = (string) $term->name;
            $link         = get_term_link( $term, 'product_brand' );
            $url          = is_string( $link ) ? $link : '';
            $desc         = trim( wp_strip_all_tags( (string) $term->description ) );
            $name_as_text = ( ! $has_logo && 'name' === $no_logo );

            $items[] = array(
                'term_id'      => $term_id,
                'name'         => $term_name,
                'url'          => $url,
                'tooltip'      => $term_name,
                'alt'          => $term_name,
                'desc'         => $desc,
                'image_url'    => $image_url,
                'has_logo'     => $has_logo,
                'name_as_text' => $name_as_text,
            );

            // (e) Non-random order with a limit: the first N kept brands are final, so stop checking further brands.
            if ( $stop_at > 0 && count( $items ) >= $stop_at ) {
                break;
            }
        }

        // Step 8: Check if all brands were filtered out.
        if ( empty( $items ) ) {
            return array(
                'items'  => array(),
                'reason' => 'every_brand_hidden',
            );
        }

        // Step 9: In-memory shuffle for random order.
        if ( 'rand' === $sort_order_by ) {
            shuffle( $items );
        }

        // Step 10: Apply item limit (read before the loop in step 7).
        if ( $limit > 0 ) {
            $items = array_slice( $items, 0, $limit );
        }

        // Step 11: Return items and empty reason.
        return array(
            'items'  => $items,
            'reason' => '',
        );
    }

    /**
     * Loads the logo posts and their post meta of the given brand terms into the object cache in one step.
     *
     * Only warms the cache; it changes no result. The thumbnail ids come from the term meta cache that get_terms() has already filled.
     *
     * @since 6.1.0
     *
     * @param array $terms Brand term objects.
     * @return void
     */
    private function prime_logo_posts( $terms ) {
        if ( ! function_exists( '_prime_post_caches' ) ) {
            return;
        }

        $thumb_ids = array();
        foreach ( $terms as $term ) {
            $thumb = absint( get_term_meta( (int) $term->term_id, 'thumbnail_id', true ) );
            if ( $thumb >= 1 ) {
                $thumb_ids[ $thumb ] = $thumb;
            }
        }

        if ( ! empty( $thumb_ids ) ) {
            _prime_post_caches( array_values( $thumb_ids ), false, true );
        }
    }

    /**
     * Determines whether a brand term has published products visible in the catalog.
     *
     * Used only as a fallback when product_count_product_brand meta is missing or non-numeric.
     *
     * @since 6.1.0
     *
     * @param int $term_id The brand term ID.
     * @return bool True if at least one visible product exists, false otherwise.
     */
    private function has_live_products( $term_id ) {
        $tax_query = array(
            'relation' => 'AND',
            array(
                'taxonomy'         => 'product_brand',
                'field'            => 'term_id',
                'terms'            => array( (int) $term_id ),
                'include_children' => true,
            ),
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => array( 'exclude-from-catalog' ),
                'operator' => 'NOT IN',
            ),
        );

        if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => array( 'outofstock' ),
                'operator' => 'NOT IN',
            );
        }

        $query = new WP_Query( array(
            'post_type'              => 'product',
            'post_status'            => 'publish',
            'posts_per_page'         => 1,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'tax_query'              => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        ) );

        return ! empty( $query->posts );
    }

    /**
     * Lists every product brand with a logo flag, for the builder's brand picker (T-215).
     *
     * One term query (all brands, empty ones included, ordered by name) plus the same
     * batch preload and the same logo check as get_items(), so there is no query per
     * brand. Read only; it changes no result of get_items().
     *
     * @since 6.1.0
     *
     * @return array|null A list of arrays with the keys term_id (int), name (string) and has_logo (bool),
     *                    or null when the brand taxonomy is not available or the query fails.
     */
    public static function get_brand_list_for_builder() {
        if ( ! lgx_ls_is_source_available( 'woo_product_brand' ) ) {
            return null;
        }

        $terms = get_terms( array(
            'taxonomy'   => 'product_brand',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );

        if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
            return null;
        }

        $provider = new self();
        $list     = array();
        $pos      = 0;
        foreach ( $terms as $term ) {
            // Same batch preload as get_items(): the logo posts and their meta for the next 50 brands in one step.
            if ( 0 === $pos % 50 ) {
                $provider->prime_logo_posts( array_slice( $terms, $pos, 50 ) );
            }
            $pos++;

            $term_id = (int) $term->term_id;

            // Same logo check as get_items() step 7 (a).
            $thumb    = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );
            $has_logo = false;
            if ( $thumb >= 1 && wp_attachment_is_image( $thumb ) ) {
                $img_src = wp_get_attachment_image_src( $thumb, true );
                if ( is_array( $img_src ) && isset( $img_src[0] ) && is_string( $img_src[0] ) && '' !== $img_src[0] ) {
                    $has_logo = true;
                }
            }

            $list[] = array(
                'term_id'  => $term_id,
                'name'     => (string) $term->name,
                'has_logo' => $has_logo,
            );
        }

        return $list;
    }
}
