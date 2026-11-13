<?php
/**
 * Logo field Pro locking and server-side enforcement.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Lgx_Logo_Field_Lock
 *
 * Handles per-logo, per-field Pro locking, grandfathering, and server-side
 * enforcement for the logo item fields (company_name, tooltip_text, alt_text)
 * according to ADR-023.
 */
class Lgx_Logo_Field_Lock {

	/**
	 * The fixed list of logo fields subject to Pro locking.
	 *
	 * @var array
	 */
	const LOCKED_FIELDS = array(
		'company_name',
		'tooltip_text',
		'alt_text',
	);

	/**
	 * Determines whether a field is locked for a given saved value and edition.
	 *
	 * @param bool  $is_pro      Whether the current edition is Pro.
	 * @param mixed $saved_value The stored value for the field (null or string).
	 * @return bool True if the field is locked, false if open/editable.
	 */
	public static function is_locked( $is_pro, $saved_value ) {
		if ( ! empty( $is_pro ) ) {
			return false;
		}

		if ( null === $saved_value || ! is_string( $saved_value ) || '' === trim( $saved_value ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enforces Pro field locking and retain-on-reject semantics for logo metadata saves.
	 *
	 * @param array $savable  The sanitized metadata array prepared by the save routine.
	 * @param array $existing The existing stored metadata array (or empty array if none).
	 * @param array $posted   The raw unslashed submitted form array ($_POST['metaboxlogosliderwp']).
	 * @param bool  $is_pro   Whether the current edition is Pro.
	 * @return array Enforced metadata array ready for update_post_meta().
	 */
	public static function enforce( array $savable, array $existing, array $posted, $is_pro ) {
		if ( ! empty( $is_pro ) ) {
			return $savable;
		}

		foreach ( self::LOCKED_FIELDS as $key ) {
			$saved_value = array_key_exists( $key, $existing ) ? $existing[ $key ] : null;
			$locked      = self::is_locked( false, $saved_value );

			if ( $locked ) {
				// Saved value locked: set $savable[key] to the existing stored value if the key exists in $existing, otherwise ''.
				// The posted value is ignored.
				$savable[ $key ] = array_key_exists( $key, $existing ) ? $existing[ $key ] : '';
			} else {
				// Not locked (grandfathered):
				if ( array_key_exists( $key, $posted ) ) {
					// Key present in $posted: keep the value already sanitized in $savable (may be '', which re-locks next time).
				} else {
					// Key NOT present in $posted: set $savable[key] = $existing[key] (retain on reject, Invariant 9).
					$savable[ $key ] = $existing[ $key ];
				}
			}
		}

		return $savable;
	}
}
