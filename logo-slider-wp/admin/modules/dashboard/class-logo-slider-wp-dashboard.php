<?php
/**
 * The Dashboard module controller.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Slider_WP_Dashboard' ) ) {
class Logo_Slider_WP_Dashboard {

	/**
	 * Site option storing whether the "Display on your website" onboarding
	 * step (Step 4) has been manually marked done. Site-wide, matching how
	 * Steps 1-3 are computed from site-wide post counts (BACKLOG B57).
	 */
	const ONBOARDING_STEP_DISPLAY_OPTION = '_logosliderwp_onboarding_step_display_dismissed';

	/**
	 * User meta key storing the onboarding card's explicit collapse/expand
	 * preference. Per-user (a personal UI preference, unlike the site-wide
	 * step-completion facts above). Presence/absence, not just its value,
	 * matters: absent means "no explicit preference yet" so the computed
	 * default (collapsed only once 100% complete) applies; 'yes'/'no' means
	 * the user has explicitly overridden that default.
	 */
	const ONBOARDING_VISIBILITY_META_KEY = '_lgx_ls_dashboard_onboarding_collapsed';

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
	 * Register the Dashboard submenu page under the CPT parent menu.
	 */
	public function register_dashboard_page() {
		$this->screen_hook_suffix = add_submenu_page(
			'edit.php?post_type=logosliderwp',
			__( 'Dashboard', 'logo-slider-wp' ),
			__( 'Dashboard', 'logo-slider-wp' ),
			'manage_options',
			'logosliderwp-dashboard',
			array( $this, 'render_dashboard_page' )
		);
	}

	/**
	 * Enqueue styles and scripts only on the Dashboard admin page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_dashboard_assets( $hook ) {
		if ( $hook !== $this->screen_hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'logosliderwp-dashboard-css',
			plugins_url( 'assets/css/dashboard.css', __FILE__ ),
			array(),
			$this->version
		);

		wp_enqueue_script(
			'logosliderwp-dashboard-js',
			plugins_url( 'assets/js/dashboard.js', __FILE__ ),
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			'logosliderwp-dashboard-js',
			'lgxDashboardOnboarding',
			array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'lgx_ls_dismiss_onboarding_step' ),
				'visibility_nonce' => wp_create_nonce( 'lgx_ls_toggle_onboarding_visibility' ),
				'completed_label'  => __( 'Completed', 'logo-slider-wp' ),
				'marking_label'    => __( 'Marking...', 'logo-slider-wp' ),
				'error_message'    => __( 'Something went wrong. Please try again.', 'logo-slider-wp' ),
				'show_steps_label' => __( 'Show steps', 'logo-slider-wp' ),
				'hide_steps_label' => __( 'Hide steps', 'logo-slider-wp' ),
				'shortcode_copied_label' => __( 'Shortcode copied to clipboard.', 'logo-slider-wp' ),
			)
		);

		// B87: "Remove Demo Content" action. Separate nonce and object from the
		// onboarding one above — different capability-checked action.
		wp_localize_script(
			'logosliderwp-dashboard-js',
			'lgxDashboardDemoContent',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'lgx_ls_remove_demo_content' ),
				'confirm_message' => __( 'Remove the demo logos, their images, and the demo showcase? Anything you have edited will be kept.', 'logo-slider-wp' ),
				'removing_label' => __( 'Removing...', 'logo-slider-wp' ),
				'error_message'  => __( 'Something went wrong. Please try again.', 'logo-slider-wp' ),
			)
		);
	}

	/**
	 * Suppress every admin-notice hook on the Dashboard screen only. Hooked
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
	 * Retrieve and filter the dashboard configuration array.
	 *
	 * @return array
	 */
	private function get_config() {
		$config_path = plugin_dir_path( __FILE__ ) . 'config/config.php';
		if ( file_exists( $config_path ) ) {
			$config = require $config_path;
		} else {
			$config = array();
		}
		return apply_filters( 'logosliderwp_dashboard_config', $config );
	}

	/**
	 * Get logo post counts.
	 *
	 * @return array
	 */
	private function get_logo_counts() {
		$count_posts = wp_count_posts( 'logosliderwp' );
		$publish     = isset( $count_posts->publish ) ? (int) $count_posts->publish : 0;
		$draft       = isset( $count_posts->draft ) ? (int) $count_posts->draft : 0;
		return array(
			'publish' => $publish,
			'draft'   => $draft,
			'total'   => $publish + $draft,
		);
	}

	/**
	 * Get the total number of published showcase shortcodes.
	 *
	 * @return int
	 */
	private function get_total_showcases() {
		$count_posts = wp_count_posts( 'lgx_lsp_shortcodes' );
		return isset( $count_posts->publish ) ? (int) $count_posts->publish : 0;
	}

	/**
	 * Retrieve environment diagnostic status parameters.
	 *
	 * @return array
	 */
	private function get_system_diagnostics() {
		$wp_version = get_bloginfo( 'version' );
		$upload_dir = wp_upload_dir();
		$uploads_writable = wp_is_writable( $upload_dir['basedir'] );

		return array(
			'php' => array(
				'label'    => __( 'PHP Version', 'logo-slider-wp' ),
				'current'  => sprintf( __( 'PHP %s (Supported)', 'logo-slider-wp' ), PHP_VERSION ),
				'required' => '7.4',
				'status'   => version_compare( PHP_VERSION, '7.4', '>=' ) ? 'success' : 'error',
			),
			'wordpress' => array(
				'label'    => __( 'WordPress Version', 'logo-slider-wp' ),
				'current'  => sprintf( __( 'WordPress %s', 'logo-slider-wp' ), $wp_version ),
				'required' => '5.8',
				'status'   => version_compare( $wp_version, '5.8', '>=' ) ? 'success' : 'error',
			),
			'uploads' => array(
				'label'    => __( 'Uploads Directory', 'logo-slider-wp' ),
				'current'  => $uploads_writable ? __( 'Writable', 'logo-slider-wp' ) : __( 'Not Writable', 'logo-slider-wp' ),
				'status'   => $uploads_writable ? 'success' : 'warning',
			),
			'plugin_version' => array(
				'label'    => __( 'Plugin Version', 'logo-slider-wp' ),
				'current'  => sprintf( __( 'v%s', 'logo-slider-wp' ), $this->version ),
				'status'   => 'success',
			),
		);
	}

	/**
	 * Calculate the onboarding status and progress.
	 *
	 * @param int   $logos_total     Total number of logos.
	 * @param int   $showcases_count Total number of showcases.
	 * @param array $config          Dashboard configuration.
	 * @return array Onboarding steps and percentage.
	 */
	private function get_onboarding_status( $logos_total, $showcases_count, $config ) {
		$steps = isset( $config['getting_started'] ) ? $config['getting_started'] : array();
		if ( empty( $steps ) ) {
			return array(
				'steps'      => array(),
				'percentage' => 0,
			);
		}

		$completed_steps = 0;
		$total_steps     = count( $steps );

		// Step 1: Logos
		if ( $logos_total > 0 ) {
			$steps[0]['status'] = 'completed';
			$completed_steps++;
		} else {
			$steps[0]['status'] = 'current';
		}

		// Step 2: Showcases
		if ( $showcases_count > 0 ) {
			$steps[1]['status'] = 'completed';
			$completed_steps++;
		} elseif ( $logos_total > 0 ) {
			$steps[1]['status'] = 'current';
		} else {
			$steps[1]['status'] = 'pending';
		}

		// Step 3: Copy Shortcode
		if ( $showcases_count > 0 ) {
			$steps[2]['status'] = 'completed';
			$completed_steps++;
		} else {
			$steps[2]['status'] = 'pending';
		}

		// Step 4: Display Slider
		if ( 'yes' === get_option( self::ONBOARDING_STEP_DISPLAY_OPTION ) ) {
			$steps[3]['status'] = 'completed';
			$completed_steps++;
		} elseif ( $showcases_count > 0 ) {
			$steps[3]['status'] = 'current';
		} else {
			$steps[3]['status'] = 'pending';
		}

		$percentage = ( $total_steps > 0 ) ? ( $completed_steps / $total_steps ) * 100 : 0;

		return array(
			'steps'      => $steps,
			'percentage' => round( $percentage ),
		);
	}

	/**
	 * Round 3b: a compact, state-aware summary shown only while the
	 * onboarding steps are collapsed (an empty-looking box otherwise gave
	 * no clue what the card was for). Derives its completed/total counts
	 * directly from the same $onboarding_steps array get_onboarding_status()
	 * already computed -- no new completion system, no change to how
	 * completion itself is determined.
	 *
	 * @param array $onboarding_steps Steps array from get_onboarding_status().
	 * @param int   $percentage       Rounded completion percentage from the same call.
	 * @return string
	 */
	private function get_onboarding_status_message( $onboarding_steps, $percentage ) {
		$total_steps = count( $onboarding_steps );

		if ( 0 === $total_steps || 0 === (int) $percentage ) {
			return __( 'Get started by completing the setup steps below. Click "Show steps" to begin.', 'logo-slider-wp' );
		}

		if ( (int) $percentage >= 100 ) {
			return __( 'All setup steps are complete. Click "Show steps" to review your progress.', 'logo-slider-wp' );
		}

		$completed_steps = count(
			array_filter(
				$onboarding_steps,
				function ( $step ) {
					return isset( $step['status'] ) && 'completed' === $step['status'];
				}
			)
		);

		return sprintf(
			/* translators: 1: number of completed onboarding steps, 2: total number of onboarding steps */
			__( 'You\'re almost there — %1$d of %2$d setup steps are complete. Click "Show steps" to continue.', 'logo-slider-wp' ),
			$completed_steps,
			$total_steps
		);
	}

	/**
	 * AJAX handler: mark the "Display on your website" onboarding step
	 * (Step 4) done. Site-wide (a single option, not per-user), matching
	 * how Steps 1-3 are already computed from site-wide post counts.
	 */
	public function handle_dismiss_onboarding_step_ajax() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'lgx_ls_dismiss_onboarding_step' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'logo-slider-wp' ) ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'logo-slider-wp' ) ) );
		}

		update_option( self::ONBOARDING_STEP_DISPLAY_OPTION, 'yes' );

		$config          = $this->get_config();
		$logo_counts     = $this->get_logo_counts();
		$showcases_count = $this->get_total_showcases();
		$onboarding      = $this->get_onboarding_status( $logo_counts['total'], $showcases_count, $config );

		wp_send_json_success(
			array(
				'percentage' => $onboarding['percentage'],
			)
		);
	}

	/**
	 * AJAX handler: persist the onboarding card's explicit collapse/expand
	 * preference for the current user. Mirrors handle_dismiss_onboarding_step_ajax()'s
	 * nonce/capability shape.
	 */
	public function handle_toggle_onboarding_visibility_ajax() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'lgx_ls_toggle_onboarding_visibility' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'logo-slider-wp' ) ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'logo-slider-wp' ) ) );
		}

		$collapsed = ! empty( $_POST['collapsed'] ) && 'yes' === $_POST['collapsed'];

		update_user_meta( get_current_user_id(), self::ONBOARDING_VISIBILITY_META_KEY, $collapsed ? 'yes' : 'no' );

		wp_send_json_success( array( 'collapsed' => $collapsed ) );
	}

	/**
	 * Whether the onboarding card should render collapsed. Completion state
	 * and visibility state are independent (brief §6): a stored explicit
	 * user preference always wins; only in its absence does the completion
	 * percentage decide the default (collapsed once 100% complete).
	 *
	 * @param int $percentage Current onboarding completion percentage.
	 * @return bool
	 */
	private function is_onboarding_collapsed( $percentage ) {
		$stored = get_user_meta( get_current_user_id(), self::ONBOARDING_VISIBILITY_META_KEY, true );

		if ( 'yes' === $stored ) {
			return true;
		}

		if ( 'no' === $stored ) {
			return false;
		}

		return 100 <= (int) $percentage;
	}

	/**
	 * Retrieve the shared Recommendations list (config's 'recommendations'
	 * key) — the single source for both Help & Resources' Recommendations
	 * section and the Dashboard's Quick Tips card (brief §8: Quick Tips is
	 * a curated subset, never independently authored).
	 *
	 * @return array
	 */
	public function get_recommendations() {
		$config = $this->get_config();
		return isset( $config['recommendations'] ) ? $config['recommendations'] : array();
	}

	/**
	 * Retrieve the video-tutorials list (already gated behind
	 * LGX_LS_VIDEOS_READY in config.php — see that file). Shared accessor so
	 * Help & Resources' Get Help section doesn't re-derive or duplicate this
	 * list.
	 *
	 * @return array
	 */
	public function get_video_tutorials() {
		$config = $this->get_config();
		return isset( $config['video_tutorials'] ) ? $config['video_tutorials'] : array();
	}

	/**
	 * Retrieve the most recent logos. Published-only (Round 3, Owner
	 * request) — draft/pending/private/trash/auto-draft logos must not
	 * appear on the Dashboard summary.
	 *
	 * @return array
	 */
	private function get_recent_logos() {
		$args = array(
			'post_type'      => 'logosliderwp',
			'post_status'    => array( 'publish' ),
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$recent_posts = get_posts( $args );
		$logos        = array();

		foreach ( $recent_posts as $post ) {
			$thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
			$logos[] = array(
				'id'            => $post->ID,
				'title'         => $post->post_title ? $post->post_title : __( '(no title)', 'logo-slider-wp' ),
				'edit_url'      => get_edit_post_link( $post->ID, 'raw' ),
				'status'        => $post->post_status,
				'date'          => get_the_time( get_option( 'date_format' ), $post ),
				'thumbnail_url' => $thumbnail_url,
			);
		}
		
		wp_reset_postdata();

		return $logos;
	}

	/**
	 * Retrieve the most recent showcases.
	 *
	 * @return array
	 */
	private function get_recent_showcases() {
		$args = array(
			'post_type'      => 'lgx_lsp_shortcodes',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$recent_posts = get_posts( $args );
		$showcases    = array();

		foreach ( $recent_posts as $post ) {
			$showcases[] = array(
				'id'            => $post->ID,
				'title'         => $post->post_title ? $post->post_title : __( '(no title)', 'logo-slider-wp' ),
				'shortcode'     => '[lgxlogoslider id="' . esc_attr( $post->ID ) . '"]',
				'edit_url'      => get_edit_post_link( $post->ID, 'raw' ),
				'status'        => $post->post_status,
				'modified_date' => get_the_modified_time( get_option( 'date_format' ), $post ),
			);
		}

		wp_reset_postdata();

		return $showcases;
	}

	/**
	 * Render the Dashboard page canvas.
	 */
	public function render_dashboard_page() {
		$config          = $this->get_config();
		$logo_counts     = $this->get_logo_counts();
		$logos_total     = $logo_counts['total'];
		$logos_published = $logo_counts['publish'];
		$logos_draft     = $logo_counts['draft'];
		$showcases_count = $this->get_total_showcases();
		$diagnostics     = $this->get_system_diagnostics();
		$version         = $this->version;

		$onboarding             = $this->get_onboarding_status( $logos_total, $showcases_count, $config );
		$onboarding_steps       = $onboarding['steps'];
		$onboarding_percentage  = $onboarding['percentage'];
		$onboarding_collapsed   = $this->is_onboarding_collapsed( $onboarding_percentage );
		$onboarding_status_message = $this->get_onboarding_status_message( $onboarding_steps, $onboarding_percentage );

		$recent_logos           = $this->get_recent_logos();
		$recent_showcases       = $this->get_recent_showcases();

		// B87: the "Remove Demo Content" section renders only while at least
		// one still-untouched seeded demo logo OR the seeded demo showcase
		// remains (B90 §7.2 — logos alone left the card hidden while a tagged
		// showcase was still present and removable).
		$has_demo_content = class_exists( 'Logo_Slider_WP_Demo_Content' )
			? Logo_Slider_WP_Demo_Content::has_demo_content()
			: false;

		$template_path = plugin_dir_path( __FILE__ ) . 'templates/dashboard.php';

		if ( file_exists( $template_path ) ) {
			ob_start();
			include $template_path;
			echo ob_get_clean();
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Dashboard template file not found.', 'logo-slider-wp' ) . '</p></div>';
		}
	}
}
}
