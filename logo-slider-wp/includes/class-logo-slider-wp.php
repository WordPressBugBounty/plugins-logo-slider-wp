<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes
 * @author     LogicHunt <info.logichunt@gmail.com>
 */
if ( ! class_exists( 'Logo_Slider_WP' ) ) {
class Logo_Slider_WP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Logo_Slider_WP_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @var Logo_Slider_WP_Public
	 */
	protected $plugin_public;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'logo-slider-wp';
		$this->version = defined( 'LGX_LS_PLUGIN_VERSION' ) ? LGX_LS_PLUGIN_VERSION : '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		/*
		 * The Gutenberg block is a core feature of Logo Slider WP and is
		 * PERMANENTLY available in Free. Do not re-introduce an edition
		 * condition here — see docs/architecture/invariants.md and
		 * docs/release/task-b97/. B97: a `lgx_ls_is_pro()` wrapper added here
		 * in 942b67c (2026-07-10, inside an unrelated 15-bug-fix commit)
		 * silently un-shipped the block from Free in every 5.5.x build, one
		 * day before readme.txt promised it in "both the Free and Pro
		 * versions" — including the 5.5.5 published on WordPress.org.
		 *
		 * The function_exists() guard below is unrelated and legitimate: it
		 * keeps the plugin safe on WordPress versions predating the block API.
		 */
		if ( function_exists( 'register_block_type' ) ) {
			$this->define_gutenberg_block_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Logo_Slider_WP_Loader. Orchestrates the hooks of the plugin.
	 * - Logo_Slider_WP_i18n. Defines internationalization functionality.
	 * - Logo_Slider_WP_Admin. Defines all hooks for the admin area.
	 * - Logo_Slider_WP_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logo-slider-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logo-slider-wp-i18n.php';

		/**
		 * The class responsible for defining settings functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-logo-slider-wp-setting.php';

		/**
		 * The class responsible for acting as the single source of truth for
		 * Pro feature locking and option gating.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-logo-slider-wp-pro-locking.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-logo-slider-wp-admin.php';

		/**
		 * The Showcase Builder Module controller.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/modules/showcase-builder/class-logo-slider-wp-showcase-builder.php';

		/**
		 * The Demo Content Module controller (BACKLOG B87).
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/modules/demo-content/class-logo-slider-wp-demo-content.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-logo-slider-wp-public.php';

		/**
		 * The class responsible for defining Gutenberg block integration.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/integrations/gutenberg/class-logo-slider-wp-gutenberg.php';


		$this->loader = new Logo_Slider_WP_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Logo_Slider_WP_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Logo_Slider_WP_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Logo_Slider_WP_Admin( $this->get_plugin_name(), $this->get_version() );
		$showcase_builder = new Logo_Slider_WP_Showcase_Builder( $this->get_plugin_name(), $this->get_version() );
		$demo_content = new Logo_Slider_WP_Demo_Content( $this->get_plugin_name(), $this->get_version() );

		

        // Enqueue admin assets
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Enqueue Showcase Builder assets
		$this->loader->add_action( 'admin_enqueue_scripts', $showcase_builder, 'enqueue_scripts' );

        //Register custom post tye for logo item and shortcodes
        $this->loader->add_action( 'init', $plugin_admin, 'register_post_type_for_logo_slider', 0 );
        $this->loader->add_action( 'init', $plugin_admin, 'register_post_type_for_lgx_logo_slider_shortcodes',0 );

        // Force the Showcase Builder edit screen to a permanent 1-column layout — see report 005.
        $this->loader->add_filter( 'get_user_option_screen_layout_lgx_lsp_shortcodes', $plugin_admin, 'force_screen_columns_for_lgx_lsp_shortcodes' );

        // Cap the same screen's Screen Options "Layout" choice to 1 column — see report 005.
        $this->loader->add_filter( 'screen_layout_columns', $plugin_admin, 'cap_screen_columns_for_lgx_lsp_shortcodes', 10, 2 );

        // Enforce Classic Editor for logosliderwp CPT
        $this->loader->add_filter( 'use_block_editor_for_post_type', $plugin_admin, 'disable_block_editor_for_logosliderwp', 10, 2 );

        // Custom Title Placeholder
        $this->loader->add_filter( 'enter_title_here', $plugin_admin, 'change_title_placeholder_text', 10, 2 );



        //Add Thumbnail Support
        $this->loader->add_action( 'init', $plugin_admin, 'add_thumbnail_support' );

        //Change Feature Image position
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'changing_meta_box_position_of_brand_logo' );

        //Add meta_box for logo slider custom post type. Pattern --> adding_meta_boxes_for_{post_type}.
        $this->loader->add_action( 'add_meta_boxes_logosliderwp', $plugin_admin, 'adding_meta_boxes_for_logosliderwp' );

        //Add custom css class to meta box panel. Pattern --> postbox_classes_{post_type}_{meta_box_id}.
        $this->loader->add_filter('postbox_classes_lgx_lsp_shortcodes_lgx_lsp_shortcodes_meta_box_panel', $plugin_admin, 'add_meta_box_css_class_for_lgx_lsp_shortcodes' );
        
        //Initialize Showcase Builder metaboxes (Replaces legacy metabox setup)
        $this->loader->add_action( 'add_meta_boxes_lgx_lsp_shortcodes', $showcase_builder, 'setup_metaboxes' );

        //Save metadata of post type. Pattern --> save_post_{post_type}.
        $this->loader->add_action( 'save_post_logosliderwp', $plugin_admin, 'save_post_metadata_of_logosliderwp', 10, 2 );

        //Save metadata of post type. Pattern --> save_post_{post_type}.
        $this->loader->add_action( 'save_post_lgx_lsp_shortcodes', $plugin_admin, 'save_post_metadata_of_lgx_lsp_shortcodes', 10, 2 );

        // Phase E: Save & Add Another Redirect
        $this->loader->add_filter( 'redirect_post_location', $plugin_admin, 'save_and_add_another_redirect', 10, 2 );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'save_and_add_another_success_notice' );

        // Phase E: Task 2.2 - Duplicate Logo
        $this->loader->add_filter( 'post_row_actions', $plugin_admin, 'add_duplicate_row_action', 10, 2 );
        $this->loader->add_action( 'admin_action_lgx_duplicate_logo', $plugin_admin, 'duplicate_logo_action' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'duplicate_logo_success_notice' );

        // Phase F: Auto-order new logos to top
        $this->loader->add_action( 'transition_post_status', $plugin_admin, 'set_new_logo_menu_order', 10, 3 );

        // Add admin menu
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'reorder_submenu_pages', 999 );

        // Suppress third-party admin notices on the Help & Resources screen only
        // (see the matching fix in the Dashboard block below for the full
        // rationale/hook order).
        $this->loader->add_action( 'in_admin_header', $plugin_admin, 'suppress_admin_notices_on_help_resources_page' );

        //Add new column head in post type listing page. Pattern --> manage_{post_type}_posts_columns.
        $this->loader->add_filter('manage_logosliderwp_posts_columns', $plugin_admin, 'add_new_column_head_for_logosliderwp' );

        //Define admin column value for column head in post type listing page. Pattern --> manage_{$post_type}_posts_custom_column.
        $this->loader->add_action( 'manage_logosliderwp_posts_custom_column', $plugin_admin, 'define_admin_column_value_for_logosliderwp', 10, 2 );

        // Phase E: Task 3.1B - Quick Edit Enhancements
        $this->loader->add_action( 'quick_edit_custom_box', $plugin_admin, 'display_quick_edit_custom_box', 10, 2 );

        // Phase E: Task 3.3 - Pagination Protection for Reordering
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'reorder_pagination_notice' );

        // Phase E: Task 4.1 - Register Dashboard Hooks via Loader
        if ( method_exists( $plugin_admin, 'get_dashboard' ) ) {
            $this->loader->add_action( 'admin_menu', $plugin_admin->get_dashboard(), 'register_dashboard_page' );
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin->get_dashboard(), 'enqueue_dashboard_assets' );

            // B57: mark the Getting Started card's Step 4 done (site-wide option).
            $this->loader->add_action( 'wp_ajax_lgx_ls_dismiss_onboarding_step', $plugin_admin->get_dashboard(), 'handle_dismiss_onboarding_step_ajax' );

            // Persist the onboarding card's explicit collapse/expand preference.
            $this->loader->add_action( 'wp_ajax_lgx_ls_toggle_onboarding_visibility', $plugin_admin->get_dashboard(), 'handle_toggle_onboarding_visibility_ajax' );

            // Suppress third-party admin notices on the Dashboard screen only. WP core
            // fires in_admin_header before network_admin_notices/user_admin_notices/
            // admin_notices/all_admin_notices (wp-admin/admin-header.php), so removing
            // those actions here stops any notice from ever being queued for this
            // screen -- replaces the non-functional B56 wp-header-end marker, which
            // used an id= attribute where WP core's own JS only recognizes class=.
            $this->loader->add_action( 'in_admin_header', $plugin_admin->get_dashboard(), 'suppress_admin_notices' );
        }

        // B87: Demo Content Module.
        // Graduation is registered first and unconditionally — it is the
        // safety guarantee behind "Remove Demo Content" and must be live
        // whenever a demo-tagged item could be edited.
        $this->loader->add_action( 'edit_post_logosliderwp', $demo_content, 'maybe_graduate_demo_logo' );

        // B90 §4: the same guarantee for the seeded demo showcase. Registered
        // alongside the logo graduation hook and for the same reason — it must
        // be live whenever a demo-tagged showcase could be edited.
        $this->loader->add_action( 'edit_post_lgx_lsp_shortcodes', $demo_content, 'maybe_graduate_demo_showcase' );

        // One-shot seed on the first admin load after a fresh activation.
        $this->loader->add_action( 'admin_init', $demo_content, 'maybe_seed_demo_content' );

        // Dashboard "Remove Demo Content" action.
        $this->loader->add_action( 'wp_ajax_lgx_ls_remove_demo_content', $demo_content, 'handle_remove_demo_content_ajax' );

        // Settings Module Hooks — menu entry intentionally not registered
        // for 6.0.0 (BACKLOG B63). The module itself (class, templates,
        // assets) is left fully intact under admin/modules/settings/ —
        // re-enable by restoring the three add_action() calls below.
        // if ( method_exists( $plugin_admin, 'get_settings' ) ) {
        //     $this->loader->add_action( 'admin_menu', $plugin_admin->get_settings(), 'register_settings_page' );
        //     $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin->get_settings(), 'enqueue_settings_assets' );
        //     // Suppress third-party admin notices on the Settings screen only
        //     // (see the matching Dashboard hook above for the full rationale).
        //     $this->loader->add_action( 'in_admin_header', $plugin_admin->get_settings(), 'suppress_admin_notices' );
        // }
        
        // UI Improvements: List Screen Enhancements
        $this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'render_category_filter' );
        $this->loader->add_action( 'manage_posts_extra_tablenav', $plugin_admin, 'render_empty_state_ux' );
        $this->loader->add_filter( 'post_row_actions', $plugin_admin, 'add_move_row_actions', 11, 2 );
        $this->loader->add_action( 'wp_ajax_lgx_ls_move_row', $plugin_admin, 'move_row_ajax_handler' );
        $this->loader->add_filter( 'bulk_actions-edit-logosliderwp', $plugin_admin, 'register_bulk_actions' );
        $this->loader->add_filter( 'handle_bulk_actions-edit-logosliderwp', $plugin_admin, 'handle_bulk_actions', 10, 3 );

        /*NEw*/

        //Add new column head in post type listing page. Pattern --> manage_{post_type}_posts_columns.
        $this->loader->add_filter('manage_lgx_lsp_shortcodes_posts_columns', $plugin_admin, 'add_new_column_head_for_lgx_logo_showcase' );

        //Define admin column value for column head in post type listing page. Pattern --> manage_{$post_type}_posts_custom_column.
        $this->loader->add_action( 'manage_lgx_lsp_shortcodes_posts_custom_column', $plugin_admin, 'define_admin_column_value_for_lgx_logo_showcase', 10, 2 );

        /*NEw*/


        // Add plugin row meta and actions links
        $this->loader->add_filter( 'plugin_action_links_' . LGX_LS_PLUGIN_BASE, $plugin_admin, 'add_links_admin_plugin_page_title' );

        //Add Support Link
        $this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'add_links_admin_plugin_page_description', 10, 2 );




        //Retrieve custom post type post and set their order as need
        $this->loader->add_action( 'pre_get_posts', $plugin_admin, 'modify_query_get_posts' );

        //Save post order by ajax on drag & drop
        $this->loader->add_action( 'wp_ajax_lgx_ls_admin_lswp_reorder', $plugin_admin, 'save_post_reorder_for_logosliderwp', 99 );


        //Deprecated : Adding the setting action


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->plugin_public = new Logo_Slider_WP_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_public = $this->plugin_public;

        // Enqueue Assets
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // Register Shortcode
        $this->loader->add_action( 'init', $plugin_public, 'register_lgx_logo_slider_shortcode', 0);

		//Add  Short Code
        add_shortcode('logo-slider-wp', array($plugin_public, 'logo_slider_wp_shortcode_function_dep' ));

        // Live Preview endpoint (Task 010 / ADR-009 Decision 5)
        $this->loader->add_filter( 'query_vars', $plugin_public, 'register_preview_query_vars' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'maybe_render_preview' );

        // Task 010 Phase 2 fix: must run on 'init' (before _wp_admin_bar_init(),
        // core-hooked to template_redirect priority 0) — see method docblock.
        $this->loader->add_action( 'init', $plugin_public, 'maybe_disable_admin_bar_for_preview' );

        // Task 011 Phase 1 (ADR-009 Decision A): dedicated AJAX action for the
        // real-time preview. Authenticated users only — no wp_ajax_nopriv_
        // counterpart; the Showcase Builder itself is admin-only.
        $this->loader->add_action( 'wp_ajax_lgx_preview_render', $plugin_public, 'handle_preview_render_ajax' );

		//add_theme_support( 'post-thumbnails', array('logosliderwp'));



	}

	/**
	 * Register all of the hooks related to the Gutenberg block integration
	 * of the plugin.
	 *
	 * Create date : 02.07.2026, By : @VibeLogic
	 *
	 * @since    4.1.0
	 * @access   private
	 */
	private function define_gutenberg_block_hooks() {

		$plugin_gutenberg = new Logo_Slider_WP_Gutenberg( $this->get_plugin_name(), $this->get_version(), $this->plugin_public );

		$this->loader->add_action( 'init', $plugin_gutenberg, 'register_block' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_gutenberg, 'enqueue_block_editor_assets' );
		$this->loader->add_action( 'enqueue_block_assets', $plugin_gutenberg, 'enqueue_block_assets' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Logo_Slider_WP_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
}
