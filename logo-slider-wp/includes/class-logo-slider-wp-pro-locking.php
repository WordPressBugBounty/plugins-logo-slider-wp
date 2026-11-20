<?php
/**
 * Pro Locking Framework Mechanism
 * 
 * This class serves as the single swappable resolver for all locking decisions 
 * in the admin UI. The rendering layer must call these methods rather than 
 * making direct `$status === 'pro'` checks.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 * @since      6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Lgx_Pro_Locking' ) ) {

    class Lgx_Pro_Locking {

        /**
         * Whether `logicex/loader.php` in this plugin directory has loaded.
         *
         * @var bool
         */
        private static $pro_ready = false;

        /**
         * Mark the Pro execution engine as loaded.
         *
         * Accepts only the `logicex/loader.php` path inside this plugin's own
         * directory (`dirname( __DIR__ )`), never a shared constant or external path.
         *
         * @param string $loader_file Absolute path passed as `__FILE__` from `logicex/loader.php`.
         * @return void
         */
        public static function mark_logicex_loaded( $loader_file ) {
            if ( ! is_string( $loader_file ) || '' === $loader_file ) {
                return;
            }

            $expected = str_replace( '\\', '/', dirname( __DIR__ ) . '/logicex/loader.php' );
            $actual   = str_replace( '\\', '/', $loader_file );

            if ( $actual === $expected && is_readable( $loader_file ) ) {
                self::$pro_ready = true;
            }
        }

        /**
         * Determine whether the Pro execution engine is ready for this plugin folder.
         *
         * Ignores `LGX_LS_IS_PRO` and any externally pre-defined `lgx_ls_is_pro()`
         * function; returns true only when `logicex/loader.php` of this plugin
         * folder has loaded and remains readable.
         *
         * @return bool True when Pro `logicex/` engine is active, false otherwise.
         */
        public static function is_pro_ready() {
            $loader_file = dirname( __DIR__ ) . '/logicex/loader.php';
            return self::$pro_ready && is_readable( $loader_file );
        }

        /**
         * Determine if an entire field is locked.
         *
         * @param string $field_key The unique field ID (e.g., 'lgx_item_limit').
         * @param string $status    The field's own config.php 'status' value (e.g., 'pro').
         * @return bool True if the field is locked in the current edition, false otherwise.
         */
        public static function is_field_locked( $field_key, $status = '' ) {
            // If the Pro execution engine is ready, nothing is locked.
            if ( self::is_pro_ready() ) {
                return false;
            }

            return 'pro' === $status;
        }

        /**
         * Determine if a specific option within a field is locked.
         *
         * Checked in order: (1) the option is unconditionally free via the field's
         * 'free_options' config.php key, (2) Model A — the field is Pro and this
         * option isn't the current saved/default value, (3) the option itself is
         * marked Pro independent of the field's own status (forward-compatible,
         * unused by any field today).
         *
         * @param string $field_key     The unique field ID (e.g., 'lgx_item_sort_order_by').
         * @param string $option_value  The value of the specific option being checked.
         * @param string $field_status  The field's own config.php 'status' value (e.g., 'pro').
         * @param mixed  $current_value The field's current saved-or-default value (Model A's exempt value).
         * @param bool   $option_is_pro Whether this specific option is Pro independent of $field_status.
         * @param array  $free_options  Option values that stay selectable unconditionally.
         * @return bool True if the option is locked, false otherwise.
         */
        public static function is_option_locked( $field_key, $option_value, $field_status = '', $current_value = null, $option_is_pro = false, $free_options = array() ) {
            if ( self::is_pro_ready() ) {
                return false;
            }

            if ( in_array( $option_value, $free_options, true ) ) {
                return false;   // always-free, checked first, unconditional
            }

            if ( 'pro' === $field_status && $option_value !== $current_value ) {
                return true;   // Model A
            }

            return (bool) $option_is_pro;   // forward-compatible hook, still unused by any field
        }
    }

}
