<?php
/**
 * Shared helper: read the Brand options of a showcase with safe defaults.
 *
 * @since 6.1.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! function_exists( 'lgx_ls_read_brand_option' ) ) {
    /**
     * Reads a single Brand setting key from showcase settings with strict validation and safe defaults.
     *
     * @since 6.1.0
     *
     * @param mixed  $showcase_settings The showcase shortcode/block settings array.
     * @param string $key               The settings key to read.
     * @return mixed The validated setting value, default value, or null for unknown keys.
     */
    function lgx_ls_read_brand_option( $showcase_settings, $key ) {
        if ( ! is_string( $key ) ) {
            return null;
        }

        if ( ! is_array( $showcase_settings ) ) {
            $showcase_settings = array();
        }

        if ( 'lgx_woo_product_brand_sort_order_by' === $key ) {
            if ( isset( $showcase_settings['lgx_woo_product_brand_sort_order_by'] ) && is_string( $showcase_settings['lgx_woo_product_brand_sort_order_by'] ) ) {
                $val = $showcase_settings['lgx_woo_product_brand_sort_order_by'];
                if ( 'name' === $val || 'added' === $val || 'rand' === $val ) {
                    return $val;
                }
            }
            return 'name';
        }

        if ( 'lgx_woo_product_brand_no_logo' === $key ) {
            if ( isset( $showcase_settings['lgx_woo_product_brand_no_logo'] ) && is_string( $showcase_settings['lgx_woo_product_brand_no_logo'] ) ) {
                $val = $showcase_settings['lgx_woo_product_brand_no_logo'];
                if ( 'hide' === $val || 'name' === $val ) {
                    return $val;
                }
            }
            return 'hide';
        }

        if ( 'lgx_woo_product_brand_empty' === $key ) {
            if ( isset( $showcase_settings['lgx_woo_product_brand_empty'] ) && is_string( $showcase_settings['lgx_woo_product_brand_empty'] ) ) {
                $val = $showcase_settings['lgx_woo_product_brand_empty'];
                if ( 'hide' === $val || 'show' === $val ) {
                    return $val;
                }
            }
            return 'hide';
        }

        if ( 'lgx_woo_product_brand_scope' === $key ) {
            if ( isset( $showcase_settings['lgx_woo_product_brand_scope'] ) && is_string( $showcase_settings['lgx_woo_product_brand_scope'] ) ) {
                $val = $showcase_settings['lgx_woo_product_brand_scope'];
                if ( 'all' === $val || 'include' === $val || 'exclude' === $val ) {
                    return $val;
                }
            }
            return 'all';
        }

        if ( 'lgx_woo_product_brand_term_ids' === $key ) {
            if ( isset( $showcase_settings['lgx_woo_product_brand_term_ids'] ) ) {
                return lgx_ls_normalise_term_ids( $showcase_settings['lgx_woo_product_brand_term_ids'] );
            }
            return '';
        }

        return null;
    }
}
