<?php
/**
 * The Settings module controller.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Slider_WP_Settings' ) ) {
class Logo_Slider_WP_Settings {

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
	 * Screen hook suffix returned by add_submenu_page.
	 *
	 * @var string
	 */
	private $screen_hook_suffix;

	/**
	 * Initialize the class and register admin hooks.
	 *
	 * @param Logo_Slider_WP_Admin $plugin_admin Reference to the admin orchestrator.
	 * @param string                   $plugin_name  Plugin name identifier.
	 * @param string                   $version      Plugin version.
	 */
	public function __construct( $plugin_admin, $plugin_name, $version ) {
		$this->plugin_admin = $plugin_admin;
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
	}

	/**
	 * Register the Settings submenu page under the CPT parent menu.
	 */
	public function register_settings_page() {
		$this->screen_hook_suffix = add_submenu_page(
			'edit.php?post_type=logosliderwp',
			__( 'Settings', 'logo-slider-wp' ),
			__( 'Settings', 'logo-slider-wp' ),
			'manage_options',
			'logosliderwp-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue styles and scripts only on the Settings admin page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_settings_assets( $hook ) {
		if ( $hook !== $this->screen_hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'logosliderwp-settings-css',
			plugins_url( 'assets/css/settings.css', __FILE__ ),
			array(),
			$this->version
		);
	}

	/**
	 * Suppress every admin-notice hook on the Settings screen only. Hooked
	 * to in_admin_header, which WP core fires before network_admin_notices/
	 * user_admin_notices/admin_notices/all_admin_notices (wp-admin/admin-
	 * header.php), so removing those actions here stops any notice -- ours
	 * or a third party's, present or future -- from ever being queued for
	 * this screen, rather than trying to reposition or hide output that has
	 * already printed.
	 */
	public function suppress_admin_notices() {
		$screen = get_current_screen();
		if ( ! $screen || $screen->id !== $this->screen_hook_suffix ) {
			return;
		}

		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Render the Settings page canvas.
	 */
	public function render_settings_page() {
		$version = $this->version;

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
        
		$tabs = array(
			'general'       => __( 'General', 'logo-slider-wp' ),
			'performance'   => __( 'Performance', 'logo-slider-wp' ),
			'import-export' => __( 'Import / Export', 'logo-slider-wp' ),
			'advanced'      => __( 'Advanced', 'logo-slider-wp' ),
			'pro'           => __( 'Pro', 'logo-slider-wp' ),
		);

		if ( ! array_key_exists( $active_tab, $tabs ) ) {
			$active_tab = 'general';
		}

		$template_path = plugin_dir_path( __FILE__ ) . 'templates/settings.php';

		if ( file_exists( $template_path ) ) {
			ob_start();
			include $template_path;
			echo ob_get_clean();
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Settings template file not found.', 'logo-slider-wp' ) . '</p></div>';
		}
	}
}
}
