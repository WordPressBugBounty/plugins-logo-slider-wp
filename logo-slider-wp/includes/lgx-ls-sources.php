<?php
/**
 * Shared helper: the one content source registry (id, label, icon, availability).
 *
 * @since 6.1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'lgx_ls_is_source_available' ) ) {
	/**
	 * Tells whether a registered content source can be used right now.
	 *
	 * The Logo Library is always available. The WooCommerce Brand source is
	 * available when the product_brand taxonomy exists. The taxonomy is
	 * registered on init, so call this after init, never earlier.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $id A source id.
	 * @return bool False for a non-string and for any id that is not registered.
	 */
	function lgx_ls_is_source_available( $id ) {
		if ( ! is_string( $id ) ) {
			return false;
		}
		if ( 'logo_library' === $id ) {
			return true;
		}
		if ( 'woo_product_brand' === $id ) {
			return (bool) taxonomy_exists( 'product_brand' );
		}
		return false;
	}
}

if ( ! function_exists( 'lgx_ls_get_registered_sources' ) ) {
	/**
	 * Returns the registered content sources, keyed by id, in display order.
	 *
	 * Each entry has the keys label (translated now, plain text), icon (a
	 * dashicon class) and available (bool). Call after init.
	 *
	 * @since 6.1.0
	 *
	 * @return array
	 */
	function lgx_ls_get_registered_sources() {
		return array(
			'logo_library'      => array(
				'label'     => __( 'Logo Library', 'logo-slider-wp' ),
				'icon'      => 'dashicons-format-gallery',
				'available' => lgx_ls_is_source_available( 'logo_library' ),
			),
			'woo_product_brand' => array(
				'label'     => __( 'WooCommerce Brands', 'logo-slider-wp' ),
				'icon'      => 'dashicons-cart',
				'available' => lgx_ls_is_source_available( 'woo_product_brand' ),
			),
		);
	}
}

if ( ! function_exists( 'lgx_ls_is_registered_source' ) ) {
	/**
	 * Tells whether a value is exactly a registered source id.
	 *
	 * Case-sensitive, no trimming. Does not depend on availability.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $value A submitted or stored value.
	 * @return bool
	 */
	function lgx_ls_is_registered_source( $value ) {
		if ( ! is_string( $value ) ) {
			return false;
		}
		return in_array( $value, array_keys( lgx_ls_get_registered_sources() ), true );
	}
}

if ( ! function_exists( 'lgx_ls_describe_source' ) ) {
	/**
	 * Describes any stored source value for display.
	 *
	 * Null, an empty string and a whitespace-only string read as logo_library.
	 * A registered id returns its registry entry. Any other string, and any
	 * non-string, is unknown: never shown as the Logo Library, never changed.
	 * The label is plain text; the caller escapes it. Call after init.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $stored A stored source value.
	 * @return array Keys: id (string), label (string), icon (string), available (bool), known (bool), shown (string, only for an unknown source).
	 */
	function lgx_ls_describe_source( $stored ) {
		$warning = 'dashicons-warning';
		if ( null === $stored || ( is_string( $stored ) && '' === trim( $stored ) ) ) {
			$stored = 'logo_library';
		}
		if ( ! is_string( $stored ) ) {
			return array(
				'id'        => '',
				/* translators: %s: the stored source value, cut to 40 characters. */
				'label'     => sprintf( __( 'Unknown source: %s', 'logo-slider-wp' ), __( '(not text)', 'logo-slider-wp' ) ),
				'icon'      => $warning,
				'available' => false,
				'known'     => false,
				/* translators: shown in place of a stored source value that is not text (for example a number). */
				'shown'     => __( '(not text)', 'logo-slider-wp' ),
			);
		}
		$registry = lgx_ls_get_registered_sources();
		if ( array_key_exists( $stored, $registry ) ) {
			return array(
				'id'        => $stored,
				'label'     => $registry[ $stored ]['label'],
				'icon'      => $registry[ $stored ]['icon'],
				'available' => $registry[ $stored ]['available'],
				'known'     => true,
			);
		}
		$shown = $stored;
		if ( mb_strlen( $stored, 'UTF-8' ) > 40 ) {
			$shown = mb_substr( $stored, 0, 40, 'UTF-8' ) . "\xE2\x80\xA6";
		}
		return array(
			'id'        => $stored,
			/* translators: %s: the stored source value, cut to 40 characters. */
			'label'     => sprintf( __( 'Unknown source: %s', 'logo-slider-wp' ), $shown ),
			'icon'      => $warning,
			'available' => false,
			'known'     => false,
			'shown'     => $shown,
		);
	}
}

if ( ! function_exists( 'lgx_ls_resolve_source_provider' ) ) {
	/**
	 * Resolves the stored source value to the provider that serves the showcase items (read side).
	 *
	 * Uses lgx_ls_describe_source() for the decision. Missing, null, empty, whitespace and
	 * `logo_library` give a new Logo Library provider. Every other value gives null, and the caller
	 * shows the controlled empty state: never a fallback to the Logo Library, nothing is written.
	 * `woo_product_brand` gives a new Brand provider when the product_brand taxonomy exists, otherwise null.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $stored_value The stored value of lgx_lswp_content_source (any type).
	 * @return Logo_Slider_WP_Source_Provider|null A new provider object, or null for the empty state.
	 */
	function lgx_ls_resolve_source_provider( $stored_value ) {
		$described = lgx_ls_describe_source( $stored_value );
		if ( true === $described['known'] && 'logo_library' === $described['id'] ) {
			return new Logo_Slider_WP_Source_Logo_Library();
		}
		if ( true === $described['known'] && 'woo_product_brand' === $described['id'] && true === $described['available'] ) {
			return new Logo_Slider_WP_Source_Brand();
		}
		return null;
	}
}

if ( ! function_exists( 'lgx_ls_get_empty_state' ) ) {
	/**
	 * Returns the empty-state visitor sentence and editor hint for a stored source value.
	 *
	 * Decides with lgx_ls_describe_source(). An unknown source and a missing Brand taxonomy
	 * produce their specific visitor text and (when $for_editor is true) editor hint.
	 * Every other case returns the generic neutral sentence and no hint.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $stored_value The stored value of lgx_lswp_content_source (any type).
	 * @param bool  $for_editor   Whether the viewer has permission to edit the showcase.
	 * @param mixed $reason       Optional. The empty reason of the Brand provider result (no_brands or every_brand_hidden). Any other value is ignored.
	 * @return array Keys: state (string), visitor (string), hint (string).
	 */
	function lgx_ls_get_empty_state( $stored_value, $for_editor, $reason = '' ) {
		$described = lgx_ls_describe_source( $stored_value );

		if ( ! $described['known'] ) {
			$hint = '';
			if ( true === $for_editor ) {
				/* translators: %s: the unknown source value, cut to 40 characters. */
				$hint = sprintf( __( 'This showcase uses a source this version does not know ("%s"). Choose a source in the builder.', 'logo-slider-wp' ), $described['shown'] );
			}
			return array(
				'state'   => 'unknown_source',
				'visitor' => __( 'This content is not available right now.', 'logo-slider-wp' ),
				'hint'    => $hint,
			);
		}

		if ( 'woo_product_brand' === $described['id'] && ! $described['available'] ) {
			$hint = '';
			if ( true === $for_editor ) {
				$hint = __( 'WooCommerce Product Brands is not available on this site. Make sure WooCommerce is active and up to date so that Products → Brands exists, or choose another source for this showcase.', 'logo-slider-wp' );
				if ( taxonomy_exists( 'pwb-brand' ) || taxonomy_exists( 'yith_product_brand' ) || taxonomy_exists( 'berocket_brand' ) ) {
					$hint .= ' ' . __( 'Another brand plugin is active. Logo Slider reads WooCommerce Product Brands (Products → Brands), not that plugin\'s brands.', 'logo-slider-wp' );
				}
			}
			return array(
				'state'   => 'brand_taxonomy_missing',
				'visitor' => __( 'Brands are not available right now.', 'logo-slider-wp' ),
				'hint'    => $hint,
			);
		}

		if ( 'woo_product_brand' === $described['id'] && is_string( $reason ) && 'no_brands' === $reason ) {
			$hint = '';
			if ( true === $for_editor ) {
				$hint = __( 'No brands to show: WooCommerce has no brands yet, or this showcase\'s brand selection matches none of them.', 'logo-slider-wp' );
			}
			return array(
				'state'   => 'brand_no_brands',
				'visitor' => __( 'There are no brands to show yet.', 'logo-slider-wp' ),
				'hint'    => $hint,
			);
		}

		if ( 'woo_product_brand' === $described['id'] && is_string( $reason ) && 'every_brand_hidden' === $reason ) {
			$hint = '';
			if ( true === $for_editor ) {
				$hint = __( 'Every brand is hidden by this showcase\'s settings (brands without a logo or without products). Change "Brands without a logo" or "Brands without products" to show them.', 'logo-slider-wp' );
			}
			return array(
				'state'   => 'brand_every_hidden',
				'visitor' => __( 'There are no brands to show yet.', 'logo-slider-wp' ),
				'hint'    => $hint,
			);
		}

		return array(
			'state'   => 'generic',
			'visitor' => __( 'This content is not available right now.', 'logo-slider-wp' ),
			'hint'    => '',
		);
	}
}
