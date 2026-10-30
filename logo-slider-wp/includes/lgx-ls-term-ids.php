<?php
/**
 * Shared helper: normalise a list of WooCommerce Brand term ids.
 *
 * @since 6.1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'lgx_ls_normalise_term_ids' ) ) {
	/**
	 * Turns a stored or submitted list of term ids into the clean stored form:
	 * positive whole numbers, no duplicates, ascending, comma-joined.
	 *
	 * 1. A string is split at commas; an integer is one entry; an array is a
	 *    list of entries (an element is not split again).
	 * 2. Any other input gives an empty string; array elements that are
	 *    neither integer nor string are dropped.
	 * 3. A string entry is trimmed and kept only when it is made of the ASCII
	 *    digits 0-9; an integer entry is kept only when it is greater than 0.
	 * 4. Leading zeros are removed; 0 and numbers beyond the integer range
	 *    are dropped.
	 * 5. Duplicates are removed, the rest sorted ascending as numbers.
	 * 6. No cap on the number of ids; no side effects; the result is always
	 *    a string and normalising it again changes nothing.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $value A comma-separated string, an integer or an array.
	 * @return string The ids joined with a comma, or an empty string for none.
	 */
	function lgx_ls_normalise_term_ids( $value ) {
		if ( is_string( $value ) ) {
			$entries = explode( ',', $value );
		} elseif ( is_int( $value ) ) {
			$entries = array( $value );
		} elseif ( is_array( $value ) ) {
			$entries = $value;
		} else {
			return '';
		}

		$ids = array();
		foreach ( $entries as $entry ) {
			if ( is_int( $entry ) ) {
				if ( $entry > 0 ) {
					$ids[ $entry ] = $entry;
				}
				continue;
			}
			if ( ! is_string( $entry ) ) {
				continue;
			}

			$entry = trim( $entry, " \t\n\r" );
			if ( 1 !== preg_match( '/\A[0-9]+\z/', $entry ) ) {
				continue;
			}

			$entry = ltrim( $entry, '0' );
			if ( '' === $entry || (string) (int) $entry !== $entry ) {
				continue;
			}

			$ids[ (int) $entry ] = (int) $entry;
		}

		$ids = array_values( $ids );
		sort( $ids, SORT_NUMERIC );

		return implode( ',', $ids );
	}
}
