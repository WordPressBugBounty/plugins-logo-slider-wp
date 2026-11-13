<?php
/**
 * Pure normalization helper for per-logo details in Bulk Upload (ADR-023 Decision 6).
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/bulk-upload/includes
 * @since      6.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Lgx_Bulk_Upload_Details' ) ) {
class Lgx_Bulk_Upload_Details {

	/**
	 * Maximum character lengths per detail key (cut after sanitizing, multibyte safe).
	 *
	 * @return array
	 */
	public static function get_max_lengths() {
		return array(
			'company_url'  => 2000,
			'company_name' => 255,
			'tooltip_text' => 255,
			'alt_text'     => 255,
			'company_desc' => 5000,
		);
	}

	/**
	 * Normalize raw per-item detail fields into either null or the full five-key
	 * _logosliderwpmeta array.
	 *
	 * - When $is_pro is false, always returns null (details ignored in Free).
	 * - When $raw is not an array, returns null.
	 * - Only the five keys (company_url, company_name, tooltip_text, alt_text,
	 *   company_desc) are read, and only when the value is a string.
	 * - Sanitizes each field with the same sanitizer as the single logo edit screen,
	 *   trims whitespace, and enforces multibyte-safe character length caps.
	 * - Never copies Brand Name (post title) into company_name or any other field.
	 * - Returns null when all five sanitized values are empty strings.
	 *
	 * @param mixed $raw    Raw details payload for a single item.
	 * @param bool  $is_pro Whether the active build is the Pro edition.
	 * @return array|null Five-key meta array or null when nothing should be written.
	 */
	public static function normalize( $raw, $is_pro ) {
		if ( true !== $is_pro || ! is_array( $raw ) ) {
			return null;
		}

		$caps = self::get_max_lengths();
		$out  = array(
			'company_url'  => '',
			'company_name' => '',
			'tooltip_text' => '',
			'alt_text'     => '',
			'company_desc' => '',
		);

		$has_non_empty = false;

		foreach ( $caps as $key => $max_len ) {
			if ( ! isset( $raw[ $key ] ) || ! is_string( $raw[ $key ] ) ) {
				continue;
			}

			$trimmed_raw = trim( $raw[ $key ] );
			if ( '' === $trimmed_raw ) {
				continue;
			}

			if ( 'company_url' === $key ) {
				$sanitized = esc_url_raw( $trimmed_raw );
			} elseif ( 'company_desc' === $key ) {
				$sanitized = sanitize_textarea_field( $trimmed_raw );
			} else {
				$sanitized = sanitize_text_field( $trimmed_raw );
			}

			$sanitized = is_string( $sanitized ) ? trim( $sanitized ) : '';
			if ( '' === $sanitized ) {
				continue;
			}

			if ( function_exists( 'mb_substr' ) ) {
				$sanitized = mb_substr( $sanitized, 0, $max_len, 'UTF-8' );
			} elseif ( preg_match( '/^.{0,' . (int) $max_len . '}/us', $sanitized, $matches ) ) {
				$sanitized = $matches[0];
			} else {
				$sanitized = substr( $sanitized, 0, $max_len );
			}

			$sanitized = trim( $sanitized );
			if ( '' !== $sanitized ) {
				$out[ $key ]   = $sanitized;
				$has_non_empty = true;
			}
		}

		if ( ! $has_non_empty ) {
			return null;
		}

		return $out;
	}
}
}
