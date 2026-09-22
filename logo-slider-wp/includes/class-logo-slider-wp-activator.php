<?php

/**
 * Fired during plugin activation
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 * @author     LogicHunt <info.logichunt@gmail.com>
 */
if ( ! class_exists( 'Logo_Slider_WP_Activator' ) ) {
class Logo_Slider_WP_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Phase E: Only set dashboard redirect flag on fresh installs (no existing logos)
		$existing_logos = get_posts( array(
			'post_type'      => 'logosliderwp',
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		) );

		if ( empty( $existing_logos ) ) {
			add_option( '_logosliderwp_activation_redirect', 'yes' );
		}

		self::maybe_flag_demo_seed();
	}

	/**
	 * B87: flag a genuinely fresh install for demo-content seeding.
	 *
	 * Deliberately a separate option from `_logosliderwp_activation_redirect`
	 * above — the redirect and the seed are two concerns and must never share
	 * one switch. The actual seeding happens later, on an admin_init load, in
	 * Logo_Slider_WP_Demo_Content::maybe_seed_demo_content().
	 *
	 * The status list is explicit rather than the `'any'` shorthand used by the
	 * redirect check above: `'any'` silently excludes `trash`, so a site whose
	 * only logo sits in the trash would look fresh and get seeded over. The
	 * redirect check keeps `'any'` unchanged on purpose — altering it would
	 * change already-shipped redirect behaviour, which is out of scope here.
	 *
	 * @since 6.0.0
	 */
	private static function maybe_flag_demo_seed() {
		$statuses = array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' );

		$logos = get_posts( array(
			'post_type'      => 'logosliderwp',
			'posts_per_page' => 1,
			'post_status'    => $statuses,
			'fields'         => 'ids',
		) );

		if ( ! empty( $logos ) ) {
			return;
		}

		$showcases = get_posts( array(
			'post_type'      => 'lgx_lsp_shortcodes',
			'posts_per_page' => 1,
			'post_status'    => $statuses,
			'fields'         => 'ids',
		) );

		if ( ! empty( $showcases ) ) {
			return;
		}

		add_option( '_logosliderwp_demo_seed_pending', 'yes' );
	}

}
}
