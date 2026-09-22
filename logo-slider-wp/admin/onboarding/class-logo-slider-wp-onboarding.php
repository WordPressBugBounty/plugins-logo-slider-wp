<?php
/**
 * The Onboarding module controller.
 *
 * Handles first-run redirects, upgrades, setup wizards, and dismissible notices.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/onboarding
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Slider_WP_Onboarding' ) ) {
class Logo_Slider_WP_Onboarding {

	/**
	 * Reference to the admin orchestrator class.
	 *
	 * @var Logo_Slider_WP_Admin
	 */
	private $plugin_admin;

	/**
	 * Unique identifier of the plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Version of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Initialize the onboarding class.
	 *
	 * @param Logo_Slider_WP_Admin $plugin_admin Reference to the admin orchestrator.
	 * @param string                   $plugin_name  Plugin name identifier.
	 * @param string                   $version      Plugin version.
	 */
	public function __construct( $plugin_admin, $plugin_name, $version ) {
		$this->plugin_admin = $plugin_admin;
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;

		add_action( 'admin_init', array( $this, 'maybe_redirect_to_dashboard' ) );
	}

	/**
	 * Check for activation flags and safely redirect to the dashboard.
	 */
	public function maybe_redirect_to_dashboard() {
		// 1. Verify if the redirect flag option is present in the database
		if ( 'yes' !== get_option( '_logosliderwp_activation_redirect' ) ) {
			return;
		}

		// 2. Consume the option immediately to prevent loops
		delete_option( '_logosliderwp_activation_redirect' );

		// 3. Skip redirects during bulk activation or network activation
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// 4. Verify user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// 5. Skip during AJAX, REST requests, XML-RPC, and WP-CLI execution
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}

		// 6. Safe redirect to the Dashboard page
		wp_safe_redirect( admin_url( 'edit.php?post_type=logosliderwp&page=logosliderwp-dashboard' ) );
		exit;
	}
}
}
