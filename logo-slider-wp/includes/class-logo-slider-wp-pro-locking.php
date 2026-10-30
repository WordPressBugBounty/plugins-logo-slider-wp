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

class Lgx_Pro_Locking {

    /**
     * Determine if an entire field is locked.
     *
     * @param string $field_key The unique field ID (e.g., 'lgx_item_limit').
     * @param string $status    The field's own config.php 'status' value (e.g., 'pro').
     * @return bool True if the field is locked in the current edition, false otherwise.
     */
    public static function is_field_locked( $field_key, $status = '' ) {
        // If the Pro edition is active, nothing is locked.
        if ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) {
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
        if ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) {
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
