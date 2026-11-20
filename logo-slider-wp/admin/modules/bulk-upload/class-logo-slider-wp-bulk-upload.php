<?php
/**
 * The Bulk Upload module controller.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/bulk-upload
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Slider_WP_Bulk_Upload' ) ) {
class Logo_Slider_WP_Bulk_Upload {

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
	 * Initialize the class and set its properties.
	 *
	 * @param Logo_Slider_WP_Admin $plugin_admin Reference to the admin orchestrator.
	 * @param string               $plugin_name  Plugin name identifier.
	 * @param string               $version      Plugin version.
	 */
	public function __construct( $plugin_admin, $plugin_name, $version ) {
		$this->plugin_admin = $plugin_admin;
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
	}

	/**
	 * Retrieve and filter the bulk upload configuration array.
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
		return apply_filters( 'logosliderwp_bulk_upload_config', $config );
	}

	/**
	 * Whether the current user has the capabilities required for Bulk Upload.
	 *
	 * @return bool
	 */
	private function current_user_can_bulk_upload() {
		$config          = $this->get_config();
		$capability      = isset( $config['capability'] ) ? $config['capability'] : 'upload_files';
		$edit_capability = isset( $config['edit_capability'] ) ? $config['edit_capability'] : 'edit_posts';

		return current_user_can( $capability ) && current_user_can( $edit_capability );
	}

	/**
	 * Build localized script settings for the Bulk Upload page client.
	 *
	 * Reads the edition in the places that need it (2 places: client settings and server-side create enforcement; ADR-023 Decision 6).
	 *
	 * @param array|null $config Optional pre-loaded module config.
	 * @return array
	 */
	public function get_js_settings( $config = null ) {
		if ( ! is_array( $config ) ) {
			$config = $this->get_config();
		}

		$nonce_action       = isset( $config['nonce_action'] ) ? $config['nonce_action'] : 'lgx_ls_bulk_upload';
		$check_cap          = isset( $config['check_cap'] ) ? (int) $config['check_cap'] : 100;
		$batch_size         = isset( $config['batch_size'] ) ? (int) $config['batch_size'] : 5;
		$publish_capability = isset( $config['publish_capability'] ) ? $config['publish_capability'] : 'publish_posts';
		$check_action       = isset( $config['check_action'] ) ? $config['check_action'] : 'lgx_ls_bulk_check';
		$create_action      = isset( $config['create_action'] ) ? $config['create_action'] : 'lgx_ls_bulk_create';
		$is_pro             = class_exists( 'Lgx_Pro_Locking' ) && Lgx_Pro_Locking::is_pro_ready();

		return array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'nonce'                => wp_create_nonce( $nonce_action ),
			'check_cap'            => $check_cap,
			'batch_size'           => $batch_size,
			'can_publish'          => (bool) current_user_can( $publish_capability ),
			'check_action'         => $check_action,
			'create_action'        => $create_action,
			'is_pro'               => (bool) $is_pro,
			'col_brand_name'       => isset( $config['col_brand_name'] ) ? $config['col_brand_name'] : __( 'Brand name', 'logo-slider-wp' ),
			'details_add'          => isset( $config['details_add'] ) ? $config['details_add'] : __( 'Add details', 'logo-slider-wp' ),
			'details_hide'         => isset( $config['details_hide'] ) ? $config['details_hide'] : __( 'Hide details', 'logo-slider-wp' ),
			/* translators: %d: number of filled detail fields */
			'details_count'        => isset( $config['details_count'] ) ? $config['details_count'] : __( 'Details (%d)', 'logo-slider-wp' ),
			'detail_url_label'           => isset( $config['detail_url_label'] ) ? $config['detail_url_label'] : __( 'Website URL', 'logo-slider-wp' ),
			'detail_name_label'          => isset( $config['detail_name_label'] ) ? $config['detail_name_label'] : __( 'Custom Brand Name', 'logo-slider-wp' ),
			'detail_tooltip_label'       => isset( $config['detail_tooltip_label'] ) ? $config['detail_tooltip_label'] : __( 'Tooltip', 'logo-slider-wp' ),
			'detail_alt_label'           => isset( $config['detail_alt_label'] ) ? $config['detail_alt_label'] : __( 'Alt Text', 'logo-slider-wp' ),
			'detail_desc_label'          => isset( $config['detail_desc_label'] ) ? $config['detail_desc_label'] : __( 'Description', 'logo-slider-wp' ),
			'detail_url_placeholder'     => isset( $config['detail_url_placeholder'] ) ? $config['detail_url_placeholder'] : __( 'https://...', 'logo-slider-wp' ),
			'detail_name_placeholder'    => isset( $config['detail_name_placeholder'] ) ? $config['detail_name_placeholder'] : __( 'Leave empty to use the Brand Name', 'logo-slider-wp' ),
			'detail_tooltip_placeholder' => isset( $config['detail_tooltip_placeholder'] ) ? $config['detail_tooltip_placeholder'] : __( 'Uses Brand Name by default', 'logo-slider-wp' ),
			'detail_alt_placeholder'     => isset( $config['detail_alt_placeholder'] ) ? $config['detail_alt_placeholder'] : __( 'Uses Brand Name by default', 'logo-slider-wp' ),
			'detail_desc_placeholder'    => isset( $config['detail_desc_placeholder'] ) ? $config['detail_desc_placeholder'] : __( 'Add a short description about this brand...', 'logo-slider-wp' ),
			'detail_caption_prefix'      => isset( $config['detail_caption_prefix'] ) ? $config['detail_caption_prefix'] : __( 'Details for', 'logo-slider-wp' ),
			'detail_caption_note'        => isset( $config['detail_caption_note'] ) ? $config['detail_caption_note'] : __( 'Tooltip and Alt Text follow the Brand Name until you change them.', 'logo-slider-wp' ),
			'item_details_warning'       => isset( $config['item_details_warning'] ) ? $config['item_details_warning'] : __( 'Created, but the details could not be saved. Add them in the logo editor.', 'logo-slider-wp' ),
			'remove_aria_label'          => isset( $config['remove_aria_label'] ) ? $config['remove_aria_label'] : __( 'Remove %s', 'logo-slider-wp' ),
			'remove_confirm_title'       => isset( $config['remove_confirm_title'] ) ? $config['remove_confirm_title'] : __( 'Remove this logo?', 'logo-slider-wp' ),
			'remove_confirm_text'        => isset( $config['remove_confirm_text'] ) ? $config['remove_confirm_text'] : __( '%s will not be created.', 'logo-slider-wp' ),
			'remove_confirm_keep'        => isset( $config['remove_confirm_keep'] ) ? $config['remove_confirm_keep'] : __( 'Keep it', 'logo-slider-wp' ),
			'remove_confirm_remove'      => isset( $config['remove_confirm_remove'] ) ? $config['remove_confirm_remove'] : __( 'Remove', 'logo-slider-wp' ),
			'remove_announced'           => isset( $config['remove_announced'] ) ? $config['remove_announced'] : __( 'Logo removed.', 'logo-slider-wp' ),
			'locked_details_title'       => isset( $config['locked_details_title'] ) ? $config['locked_details_title'] : __( 'Filling in details for Bulk Upload is a Pro Feature', 'logo-slider-wp' ),
			'locked_details_text'        => isset( $config['locked_details_text'] ) ? $config['locked_details_text'] : __( 'Website URL and Description are still free. You can add them to each logo after it is created.', 'logo-slider-wp' ),
			'locked_show_how'            => isset( $config['locked_show_how'] ) ? $config['locked_show_how'] : __( 'Show me how', 'logo-slider-wp' ),
			'locked_step_1'              => isset( $config['locked_step_1'] ) ? $config['locked_step_1'] : __( 'Click Create to add your logos.', 'logo-slider-wp' ),
			'locked_step_2'              => isset( $config['locked_step_2'] ) ? $config['locked_step_2'] : __( 'On the next screen, click %s next to a logo. It opens in a new tab.', 'logo-slider-wp' ),
			'locked_step_2_bold'         => isset( $config['locked_step_2_bold'] ) ? $config['locked_step_2_bold'] : __( 'Edit in single view', 'logo-slider-wp' ),
			'locked_step_3'              => isset( $config['locked_step_3'] ) ? $config['locked_step_3'] : __( 'Add the Website URL and Description, then click Update.', 'logo-slider-wp' ),
			'locked_tip_prefix'          => isset( $config['locked_tip_prefix'] ) ? $config['locked_tip_prefix'] : __( 'Tip:', 'logo-slider-wp' ),
			'locked_tip_text'            => isset( $config['locked_tip_text'] ) ? $config['locked_tip_text'] : __( 'Adding only a few logos? %s lets you add each logo and its details on one page.', 'logo-slider-wp' ),
			'locked_tip_link'            => isset( $config['locked_tip_link'] ) ? $config['locked_tip_link'] : __( 'Add New Logo', 'logo-slider-wp' ),
			'locked_upgrade_label'       => isset( $config['locked_upgrade_label'] ) ? $config['locked_upgrade_label'] : __( 'Upgrade to Pro', 'logo-slider-wp' ),
			'locked_upgrade_url'         => isset( $config['locked_upgrade_url'] ) ? $config['locked_upgrade_url'] : 'https://logichunt.com/product/wordpress-logo-slider/',
			'add_new_logo_url'           => admin_url( 'post-new.php?post_type=logosliderwp' ),
			'result_edit_link_free'      => isset( $config['result_edit_link_free'] ) ? $config['result_edit_link_free'] : __( 'Edit in single view', 'logo-slider-wp' ),
			'result_edit_aria_free'      => isset( $config['result_edit_aria_free'] ) ? $config['result_edit_aria_free'] : __( 'Edit %s in single view', 'logo-slider-wp' ),
			'result_free_note'           => isset( $config['result_free_note'] ) ? $config['result_free_note'] : __( 'Logos without a Website URL are not clickable. Add one in the single edit view.', 'logo-slider-wp' ),
			'duplicate_no_title'         => isset( $config['duplicate_no_title'] ) ? $config['duplicate_no_title'] : __( 'Already used by another logo. It will be skipped.', 'logo-slider-wp' ),
		);
	}

	/**
	 * Get the canonical admin URL for the Bulk Upload page.
	 *
	 * @return string
	 */
	public function get_page_url() {
		$config    = $this->get_config();
		$page_slug = isset( $config['page_slug'] ) ? $config['page_slug'] : 'logosliderwp-bulk-upload';
		$url       = function_exists( 'menu_page_url' ) ? menu_page_url( $page_slug, false ) : '';

		if ( empty( $url ) ) {
			$url = admin_url( 'edit.php?post_type=logosliderwp&page=' . rawurlencode( $page_slug ) );
		}

		return $url;
	}

	/**
	 * Register the hidden Bulk Upload submenu page under the CPT parent menu.
	 */
	public function register_bulk_upload_page() {
		$config      = $this->get_config();
		$parent_slug = isset( $config['parent_slug'] ) ? $config['parent_slug'] : 'edit.php?post_type=logosliderwp';
		$page_title  = isset( $config['page_title'] ) ? $config['page_title'] : __( 'Bulk Upload Logos', 'logo-slider-wp' );
		$menu_title  = isset( $config['menu_title'] ) ? $config['menu_title'] : __( 'Bulk Upload Logos', 'logo-slider-wp' );
		$capability  = isset( $config['capability'] ) ? $config['capability'] : 'upload_files';
		$page_slug   = isset( $config['page_slug'] ) ? $config['page_slug'] : 'logosliderwp-bulk-upload';

		$this->screen_hook_suffix = add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$page_slug,
			array( $this, 'render_bulk_upload_page' )
		);
	}

	/**
	 * Remove the submenu row at admin_head so the page keeps its admin title
	 * and its own capability check while not appearing in the menu.
	 */
	public function hide_bulk_upload_submenu() {
		$config      = $this->get_config();
		$parent_slug = isset( $config['parent_slug'] ) ? $config['parent_slug'] : 'edit.php?post_type=logosliderwp';
		$page_slug   = isset( $config['page_slug'] ) ? $config['page_slug'] : 'logosliderwp-bulk-upload';

		remove_submenu_page( $parent_slug, $page_slug );
	}

	/**
	 * Highlight "Logos" as the current submenu item when viewing Bulk Upload.
	 *
	 * @param string|null $submenu_file The current submenu file.
	 * @return string|null
	 */
	public function filter_submenu_file( $submenu_file ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && ! empty( $this->screen_hook_suffix ) && $screen->id === $this->screen_hook_suffix ) {
			$config = $this->get_config();
			return isset( $config['parent_slug'] ) ? $config['parent_slug'] : 'edit.php?post_type=logosliderwp';
		}

		return $submenu_file;
	}

	/**
	 * Enqueue styles and scripts on the Bulk Upload page and the E1 button
	 * script on the All Logos list screen.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_bulk_upload_assets( $hook ) {
		$config       = $this->get_config();
		$button_label = isset( $config['button_label'] ) ? $config['button_label'] : __( 'Bulk Upload', 'logo-slider-wp' );

		if ( ! empty( $this->screen_hook_suffix ) && $hook === $this->screen_hook_suffix ) {
			if ( ! $this->current_user_can_bulk_upload() ) {
				return;
			}

			wp_enqueue_media();

			wp_enqueue_style(
				'logosliderwp-bulk-upload-css',
				plugins_url( 'assets/css/bulk-upload.css', __FILE__ ),
				array(),
				$this->version
			);

			wp_enqueue_style(
				'logosliderwp-bulk-upload-page-css',
				plugins_url( 'assets/css/bulk-upload-page.css', __FILE__ ),
				array( 'logosliderwp-bulk-upload-css' ),
				$this->version
			);

			wp_enqueue_script(
				'logosliderwp-bulk-upload-page-js',
				plugins_url( 'assets/js/bulk-upload-page.js', __FILE__ ),
				array( 'jquery', 'jquery-ui-sortable', 'wp-i18n' ),
				$this->version,
				true
			);

			$languages_path = ( defined( 'LGX_LS_PLUGIN_ROOT_PATH' ) ? LGX_LS_PLUGIN_ROOT_PATH : plugin_dir_path( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . 'languages';
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'logosliderwp-bulk-upload-page-js', 'logo-slider-wp', $languages_path );
			}

			wp_localize_script(
				'logosliderwp-bulk-upload-page-js',
				'lgxBulkUploadConfig',
				$this->get_js_settings( $config )
			);

			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! $this->current_user_can_bulk_upload() ) {
			return;
		}

		// Exception E1: "Add New Logo" + "Bulk Upload" action buttons on the All Logos list screen only.
		if ( 'edit-logosliderwp' === $screen->id ) {
			wp_enqueue_style(
				'logosliderwp-bulk-upload-css',
				plugins_url( 'assets/css/bulk-upload.css', __FILE__ ),
				array(),
				$this->version
			);

			wp_enqueue_script(
				'logosliderwp-bulk-upload-js',
				plugins_url( 'assets/js/bulk-upload.js', __FILE__ ),
				array( 'jquery' ),
				$this->version,
				true
			);

			wp_localize_script(
				'logosliderwp-bulk-upload-js',
				'lgxBulkUpload',
				array(
					'page_url'     => $this->get_page_url(),
					'button_label' => $button_label,
				)
			);

			return;
		}

		// Exception E2: Callout strip styles on the Add New Logo screen only.
		if ( 'logosliderwp' === $screen->id && 'logosliderwp' === $screen->post_type && 'add' === $screen->action ) {
			wp_enqueue_style(
				'logosliderwp-bulk-upload-css',
				plugins_url( 'assets/css/bulk-upload.css', __FILE__ ),
				array(),
				$this->version
			);
		}
	}

	/**
	 * Exception E2: Render the "Adding many logos? Use Bulk Upload." link on
	 * the Add New Logo screen only, for users with the required capabilities.
	 *
	 * @param WP_Post|null $post Current post object.
	 */
	public function render_add_new_link( $post = null ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'logosliderwp' !== $screen->post_type || 'add' !== $screen->action ) {
			return;
		}

		if ( is_object( $post ) && isset( $post->post_type ) && 'logosliderwp' !== $post->post_type ) {
			return;
		}

		if ( ! $this->current_user_can_bulk_upload() ) {
			return;
		}

		$config         = $this->get_config();
		$add_new_prompt = isset( $config['add_new_prompt'] ) ? $config['add_new_prompt'] : __( 'Adding many logos?', 'logo-slider-wp' );
		$add_new_link   = isset( $config['add_new_link'] ) ? $config['add_new_link'] : __( 'Use Bulk Upload.', 'logo-slider-wp' );
		$page_url       = $this->get_page_url();
		$template_path  = plugin_dir_path( __FILE__ ) . 'templates/add-new-link.php';

		if ( file_exists( $template_path ) ) {
			ob_start();
			include $template_path;
			echo ob_get_clean();
		}
	}

	/**
	 * Render the Bulk Upload page canvas.
	 */
	public function render_bulk_upload_page() {
		$config = $this->get_config();

		if ( ! $this->current_user_can_bulk_upload() ) {
			$permission_denied = isset( $config['permission_denied'] )
				? $config['permission_denied']
				: __( 'You do not have permission to access this page.', 'logo-slider-wp' );
			wp_die( esc_html( $permission_denied ), '', array( 'response' => 403 ) );
		}

		$taxonomy           = isset( $config['taxonomy'] ) ? $config['taxonomy'] : 'logosliderwpcat';
		$publish_capability = isset( $config['publish_capability'] ) ? $config['publish_capability'] : 'publish_posts';
		$can_publish        = (bool) current_user_can( $publish_capability );

		$raw_terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		$categories = ( is_array( $raw_terms ) && ! is_wp_error( $raw_terms ) ) ? $raw_terms : array();

		$page_title              = isset( $config['page_title'] ) ? $config['page_title'] : __( 'Bulk Upload Logos', 'logo-slider-wp' );
		$step_indicator_label    = isset( $config['step_indicator_label'] ) ? $config['step_indicator_label'] : __( 'Bulk upload steps', 'logo-slider-wp' );
		$step_1_label            = isset( $config['step_1_label'] ) ? $config['step_1_label'] : __( 'Select images', 'logo-slider-wp' );
		$step_2_label            = isset( $config['step_2_label'] ) ? $config['step_2_label'] : __( 'Review', 'logo-slider-wp' );
		$step_3_label            = isset( $config['step_3_label'] ) ? $config['step_3_label'] : __( 'Create', 'logo-slider-wp' );
		$help_text               = isset( $config['help_text'] ) ? $config['help_text'] : __( 'Images uploaded in the Media window stay in the Media Library even if you cancel. Only image files can become logos. Nothing is created until you confirm.', 'logo-slider-wp' );
		$select_images_button    = isset( $config['select_images_button'] ) ? $config['select_images_button'] : __( 'Select images', 'logo-slider-wp' );
		$review_heading          = isset( $config['review_heading'] ) ? $config['review_heading'] : __( 'Review selected images', 'logo-slider-wp' );
		$col_image               = isset( $config['col_image'] ) ? $config['col_image'] : __( 'Image', 'logo-slider-wp' );
		$col_filename            = isset( $config['col_filename'] ) ? $config['col_filename'] : __( 'File name', 'logo-slider-wp' );
		$col_brand_name          = isset( $config['col_brand_name'] ) ? $config['col_brand_name'] : __( 'Brand name', 'logo-slider-wp' );
		$col_details             = isset( $config['col_details'] ) ? $config['col_details'] : __( 'Details', 'logo-slider-wp' );
		$col_remove              = isset( $config['col_remove'] ) ? $config['col_remove'] : __( 'Remove', 'logo-slider-wp' );
		$common_settings_heading = isset( $config['common_settings_heading'] ) ? $config['common_settings_heading'] : __( 'Common settings', 'logo-slider-wp' );
		$categories_label        = isset( $config['categories_label'] ) ? $config['categories_label'] : __( 'Categories', 'logo-slider-wp' );
		$categories_help         = isset( $config['categories_help'] ) ? $config['categories_help'] : __( 'Optional. Selected categories are assigned to every logo created in this run.', 'logo-slider-wp' );
		$categories_empty        = isset( $config['categories_empty'] ) ? $config['categories_empty'] : __( 'No logo categories exist yet.', 'logo-slider-wp' );
		$status_label            = isset( $config['status_label'] ) ? $config['status_label'] : __( 'Status', 'logo-slider-wp' );
		$status_draft_label      = isset( $config['status_draft_label'] ) ? $config['status_draft_label'] : __( 'Draft (recommended)', 'logo-slider-wp' );
		$status_publish_label    = isset( $config['status_publish_label'] ) ? $config['status_publish_label'] : __( 'Publish', 'logo-slider-wp' );
		$status_draft_only_note  = isset( $config['status_draft_only_note'] ) ? $config['status_draft_only_note'] : __( 'Your account can save logos as drafts only.', 'logo-slider-wp' );
		$create_logos_button     = isset( $config['create_logos_button'] ) ? $config['create_logos_button'] : __( 'Create logos', 'logo-slider-wp' );
		$stop_button             = isset( $config['stop_button'] ) ? $config['stop_button'] : __( 'Stop', 'logo-slider-wp' );
		$progress_label          = isset( $config['progress_label'] ) ? $config['progress_label'] : __( 'Bulk logo creation progress', 'logo-slider-wp' );
		$results_heading         = isset( $config['results_heading'] ) ? $config['results_heading'] : __( 'Results', 'logo-slider-wp' );
		$retry_failed_button     = isset( $config['retry_failed_button'] ) ? $config['retry_failed_button'] : __( 'Retry failed', 'logo-slider-wp' );
		$select_more_button      = isset( $config['select_more_button'] ) ? $config['select_more_button'] : __( 'Select more images', 'logo-slider-wp' );
		$view_all_logos_button   = isset( $config['view_all_logos_button'] ) ? $config['view_all_logos_button'] : __( 'View all logos', 'logo-slider-wp' );

		$template_path = plugin_dir_path( __FILE__ ) . 'templates/bulk-upload.php';

		if ( file_exists( $template_path ) ) {
			ob_start();
			include $template_path;
			echo ob_get_clean();
		}
	}

	/**
	 * Verify AJAX nonce and required capabilities for Bulk Upload endpoints.
	 * Sends a 403 JSON error response and exits on failure.
	 *
	 * @param array $config Module configuration array.
	 */
	private function verify_ajax_request( $config ) {
		$nonce_action = isset( $config['nonce_action'] ) ? $config['nonce_action'] : 'lgx_ls_bulk_upload';
		if ( ! check_ajax_referer( $nonce_action, 'nonce', false ) ) {
			$message = isset( $config['invalid_nonce'] )
				? $config['invalid_nonce']
				: __( 'Security check failed. Please reload the page and try again.', 'logo-slider-wp' );
			wp_send_json_error( array( 'message' => $message ), 403 );
		}

		if ( ! $this->current_user_can_bulk_upload() ) {
			$message = isset( $config['permission_denied'] )
				? $config['permission_denied']
				: __( 'You do not have permission to access this page.', 'logo-slider-wp' );
			wp_send_json_error( array( 'message' => $message ), 403 );
		}
	}

	/**
	 * Find existing non-trashed, non-auto-draft logosliderwp posts that use
	 * any of the given attachment IDs as their featured image (_thumbnail_id).
	 *
	 * @param array $attachment_ids Array of positive integer attachment IDs.
	 * @return array Map of attachment_id => array( 'post_id' => int, 'title' => string ).
	 */
	private function find_existing_logos_by_thumbnails( $attachment_ids ) {
		if ( empty( $attachment_ids ) || ! is_array( $attachment_ids ) ) {
			return array();
		}

		$config    = $this->get_config();
		$post_type = isset( $config['post_type'] ) ? $config['post_type'] : 'logosliderwp';
		$statuses  = array_values( array_diff( get_post_stati(), array( 'trash', 'auto-draft' ) ) );

		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'post_status'            => $statuses,
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'meta_query'             => array(
					array(
						'key'     => '_thumbnail_id',
						'value'   => $attachment_ids,
						'compare' => 'IN',
						'type'    => 'NUMERIC',
					),
				),
			)
		);

		$map = array();
		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $logo_id ) {
				$logo_id  = (int) $logo_id;
				$thumb_id = (int) get_post_thumbnail_id( $logo_id );
				if ( $thumb_id > 0 && ! isset( $map[ $thumb_id ] ) ) {
					$can_read = current_user_can( 'read_post', $logo_id );
					// get_the_title() is texturized and escaped; the client prints it as plain text via jQuery .text().
					// Decode all HTML entities so quotes, apostrophes, and punctuation display naturally.
					$title    = $can_read ? html_entity_decode( get_the_title( $logo_id ), ENT_QUOTES, 'UTF-8' ) : '';
					$map[ $thumb_id ] = array(
						'post_id'  => $logo_id,
						'title'    => $title,
						'can_read' => $can_read,
					);
				}
			}
		}

		return $map;
	}

	/**
	 * AJAX handler: pre-check selected attachment IDs for duplicates and demo tags.
	 * Action: wp_ajax_lgx_ls_bulk_check
	 */
	public function handle_bulk_check_ajax() {
		$config = $this->get_config();
		$this->verify_ajax_request( $config );

		$check_cap       = isset( $config['check_cap'] ) ? (int) $config['check_cap'] : 100;
		$invalid_ids_msg = isset( $config['invalid_ids'] )
			? $config['invalid_ids']
			: __( 'Please provide between 1 and 100 attachment IDs to check.', 'logo-slider-wp' );

		if ( ! isset( $_POST['ids'] ) || ! is_array( $_POST['ids'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_ajax_request().
			wp_send_json_error( array( 'message' => $invalid_ids_msg ), 400 );
		}

		$raw_ids = wp_unslash( $_POST['ids'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_ajax_request().
		if ( count( $raw_ids ) > $check_cap ) {
			wp_send_json_error( array( 'message' => $invalid_ids_msg ), 400 );
		}

		$clean_ids = array();
		foreach ( $raw_ids as $raw_id ) {
			if ( is_scalar( $raw_id ) ) {
				$id = absint( $raw_id );
				if ( $id > 0 ) {
					$clean_ids[] = $id;
				}
			}
		}
		$clean_ids = array_values( array_unique( $clean_ids ) );

		if ( empty( $clean_ids ) || count( $clean_ids ) > $check_cap ) {
			wp_send_json_error( array( 'message' => $invalid_ids_msg ), 400 );
		}

		$duplicates_map = $this->find_existing_logos_by_thumbnails( $clean_ids );
		$demo_meta_key  = isset( $config['demo_attachment_meta'] ) ? $config['demo_attachment_meta'] : '_lgx_demo_attachment';

		$items = array();
		foreach ( $clean_ids as $id ) {
			$is_attachment = ( 'attachment' === get_post_type( $id ) );
			$is_readable   = $is_attachment && current_user_can( 'read_post', $id );

			if ( ! $is_readable ) {
				$items[] = array(
					'attachment_id' => $id,
					'valid'         => false,
					'is_attachment' => false,
					'is_image'      => false,
					'duplicate'     => false,
					'demo'          => false,
				);
				continue;
			}

			$is_image  = wp_attachment_is_image( $id );
			$demo_val  = get_post_meta( $id, $demo_meta_key, true );
			$is_demo   = ( '' !== $demo_val && null !== $demo_val && false !== $demo_val );
			$duplicate = false;

			if ( isset( $duplicates_map[ $id ] ) ) {
				$dup_entry = $duplicates_map[ $id ];
				if ( ! empty( $dup_entry['can_read'] ) ) {
					$duplicate = array(
						'post_id' => (int) $dup_entry['post_id'],
						'title'   => (string) $dup_entry['title'],
					);
				} else {
					$duplicate = array(
						'post_id' => 0,
						'title'   => '',
					);
				}
			}

			$items[] = array(
				'attachment_id' => $id,
				'valid'         => ( $is_image ),
				'is_attachment' => true,
				'is_image'      => $is_image,
				'duplicate'     => $duplicate,
				'demo'          => $is_demo,
			);
		}

		wp_send_json_success( array( 'items' => $items ) );
	}

	/**
	 * AJAX handler: create up to 5 logo items in a sequential batch.
	 * Action: wp_ajax_lgx_ls_bulk_create
	 */
	public function handle_bulk_create_ajax() {
		$config = $this->get_config();
		$this->verify_ajax_request( $config );

		$post_type          = isset( $config['post_type'] ) ? $config['post_type'] : 'logosliderwp';
		$taxonomy           = isset( $config['taxonomy'] ) ? $config['taxonomy'] : 'logosliderwpcat';
		$batch_size         = isset( $config['batch_size'] ) ? (int) $config['batch_size'] : 5;
		$publish_capability = isset( $config['publish_capability'] ) ? $config['publish_capability'] : 'publish_posts';
		$demo_meta_key      = isset( $config['demo_attachment_meta'] ) ? $config['demo_attachment_meta'] : '_lgx_demo_attachment';

		$invalid_status_msg      = isset( $config['invalid_status'] ) ? $config['invalid_status'] : __( 'Invalid post status parameter.', 'logo-slider-wp' );
		$publish_denied_msg      = isset( $config['publish_denied'] ) ? $config['publish_denied'] : __( 'You do not have permission to publish logos.', 'logo-slider-wp' );
		$assign_terms_denied_msg = isset( $config['assign_terms_denied'] ) ? $config['assign_terms_denied'] : __( 'You do not have permission to assign logo categories.', 'logo-slider-wp' );
		$invalid_terms_msg       = isset( $config['invalid_terms'] ) ? $config['invalid_terms'] : __( 'One or more selected categories do not exist.', 'logo-slider-wp' );
		$invalid_items_msg       = isset( $config['invalid_items'] ) ? $config['invalid_items'] : __( 'Please provide between 1 and 5 valid logo items per request.', 'logo-slider-wp' );

		// 1. Validate status ('draft' or 'publish'; anything else scalar -> 'draft'; non-scalar -> 400).
		$status = 'draft';
		if ( isset( $_POST['status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_ajax_request().
			if ( ! is_scalar( $_POST['status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				wp_send_json_error( array( 'message' => $invalid_status_msg ), 400 );
			}
			$raw_status = sanitize_key( wp_unslash( $_POST['status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( 'publish' === $raw_status ) {
				$status = 'publish';
			}
		}

		if ( 'publish' === $status && ! current_user_can( $publish_capability ) ) {
			wp_send_json_error( array( 'message' => $publish_denied_msg ), 403 );
		}

		// 2. Validate terms (array of absint, drop 0; every term must exist in logosliderwpcat and user must have assign_terms).
		$term_ids = array();
		if ( isset( $_POST['terms'] ) && '' !== $_POST['terms'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! is_array( $_POST['terms'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				wp_send_json_error( array( 'message' => $invalid_terms_msg ), 400 );
			}
			$raw_terms = wp_unslash( $_POST['terms'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			foreach ( $raw_terms as $raw_term ) {
				if ( ! is_scalar( $raw_term ) ) {
					wp_send_json_error( array( 'message' => $invalid_terms_msg ), 400 );
				}
				$tid = absint( $raw_term );
				if ( $tid > 0 ) {
					$term_ids[] = $tid;
				}
			}
			$term_ids = array_values( array_unique( $term_ids ) );
		}

		if ( ! empty( $term_ids ) ) {
			$tax_obj    = get_taxonomy( $taxonomy );
			$assign_cap = ( $tax_obj && isset( $tax_obj->cap->assign_terms ) ) ? $tax_obj->cap->assign_terms : 'edit_posts';
			if ( ! current_user_can( $assign_cap ) ) {
				wp_send_json_error( array( 'message' => $assign_terms_denied_msg ), 403 );
			}

			foreach ( $term_ids as $tid ) {
				$term_obj = get_term( $tid, $taxonomy );
				if ( ! $term_obj || is_wp_error( $term_obj ) ) {
					wp_send_json_error( array( 'message' => $invalid_terms_msg ), 400 );
				}
			}
		}

		// 3. Validate items array (1 to 5 entries, each entry an array with scalar fields).
		if ( ! isset( $_POST['items'] ) || ! is_array( $_POST['items'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
		}

		$raw_items = $_POST['items']; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput -- unslashed per field below to avoid double-unslashing backslashes.
		if ( empty( $raw_items ) || count( $raw_items ) > $batch_size ) {
			wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
		}

		foreach ( $raw_items as $entry ) {
			if ( ! is_array( $entry ) ) {
				wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
			}
			if ( isset( $entry['attachment_id'] ) && ! is_scalar( $entry['attachment_id'] ) ) {
				wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
			}
			if ( isset( $entry['brand_name'] ) && ! is_scalar( $entry['brand_name'] ) ) {
				wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
			}
			if ( isset( $entry['force'] ) && ! is_scalar( $entry['force'] ) ) {
				wp_send_json_error( array( 'message' => $invalid_items_msg ), 400 );
			}
		}

		$item_invalid_id_msg        = isset( $config['item_invalid_id'] ) ? $config['item_invalid_id'] : __( 'Invalid or missing attachment ID.', 'logo-slider-wp' );
		$item_not_attachment_msg    = isset( $config['item_not_attachment'] ) ? $config['item_not_attachment'] : __( 'The selected item is not a Media Library attachment.', 'logo-slider-wp' );
		$item_not_image_msg         = isset( $config['item_not_image'] ) ? $config['item_not_image'] : __( 'The selected attachment is not a supported image.', 'logo-slider-wp' );
		$item_unreadable_msg        = isset( $config['item_unreadable'] ) ? $config['item_unreadable'] : __( 'You do not have permission to use this attachment.', 'logo-slider-wp' );
		$item_demo_skipped_msg      = isset( $config['item_demo_skipped'] ) ? $config['item_demo_skipped'] : __( 'This is a demo image. Upload your own logo.', 'logo-slider-wp' );
		$item_duplicate_skipped_msg = isset( $config['item_duplicate_skipped'] ) ? $config['item_duplicate_skipped'] : __( 'Skipped: already used by logo #%d.', 'logo-slider-wp' );
		$item_insert_failed_msg     = isset( $config['item_insert_failed'] ) ? $config['item_insert_failed'] : __( 'Could not create the logo post.', 'logo-slider-wp' );
		$item_thumbnail_failed_msg  = isset( $config['item_thumbnail_failed'] ) ? $config['item_thumbnail_failed'] : __( 'Could not set the logo image.', 'logo-slider-wp' );
		$item_details_warning_msg   = isset( $config['item_details_warning'] ) ? $config['item_details_warning'] : __( 'Created, but the details could not be saved. Add them in the logo editor.', 'logo-slider-wp' );
		$item_terms_warning_msg     = isset( $config['item_terms_warning'] ) ? $config['item_terms_warning'] : __( 'Logo created, but categories could not be assigned.', 'logo-slider-wp' );
		$item_created_msg           = isset( $config['item_created'] ) ? $config['item_created'] : __( 'Logo created.', 'logo-slider-wp' );

		$is_pro  = (bool) ( class_exists( 'Lgx_Pro_Locking' ) && Lgx_Pro_Locking::is_pro_ready() );
		$results = array();

		foreach ( $raw_items as $entry ) {
			$attachment_id  = isset( $entry['attachment_id'] ) ? absint( wp_unslash( $entry['attachment_id'] ) ) : 0;
			$raw_brand_name = isset( $entry['brand_name'] ) ? (string) $entry['brand_name'] : '';
			$raw_force      = isset( $entry['force'] ) ? wp_unslash( $entry['force'] ) : 0;
			$force          = ( 1 === (int) $raw_force || '1' === (string) $raw_force || true === $raw_force ) ? 1 : 0;

			try {
				// 1. Positive id, attachment post type, valid image, read_post capability.
				if ( $attachment_id <= 0 ) {
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => $item_invalid_id_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				if ( 'attachment' !== get_post_type( $attachment_id ) ) {
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => $item_not_attachment_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				if ( ! wp_attachment_is_image( $attachment_id ) ) {
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => $item_not_image_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				if ( ! current_user_can( 'read_post', $attachment_id ) ) {
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => $item_unreadable_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				// 2. Demo guard (D15).
				$demo_val = get_post_meta( $attachment_id, $demo_meta_key, true );
				if ( '' !== $demo_val && null !== $demo_val && false !== $demo_val ) {
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'skipped',
						'post_id'       => 0,
						'message'       => $item_demo_skipped_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				// 3. Duplicate guard (D3).
				$dup_map = $this->find_existing_logos_by_thumbnails( array( $attachment_id ) );
				if ( 0 === $force && isset( $dup_map[ $attachment_id ] ) ) {
					$existing_logo_id = (int) $dup_map[ $attachment_id ]['post_id'];
					$results[]        = array(
						'attachment_id' => $attachment_id,
						'result'        => 'skipped',
						'post_id'       => $existing_logo_id,
						'message'       => sprintf( $item_duplicate_skipped_msg, $existing_logo_id ),
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				// 4. Sanitize brand name and insert post.
				$name    = sanitize_text_field( wp_unslash( $raw_brand_name ) );
				$post_id = wp_insert_post(
					wp_slash(
						array(
							'post_type'   => $post_type,
							'post_title'  => $name,
							'post_status' => $status,
						)
					),
					true
				);

				if ( is_wp_error( $post_id ) || ! $post_id ) {
					$err_msg   = is_wp_error( $post_id ) ? $post_id->get_error_message() : $item_insert_failed_msg;
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => ! empty( $err_msg ) ? $err_msg : $item_insert_failed_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				$post_id = (int) $post_id;

				// 5. Link featured image; delete newly created post on failure (never delete attachment).
				$thumb_set = set_post_thumbnail( $post_id, $attachment_id );
				if ( ! $thumb_set ) {
					wp_delete_post( $post_id, true );
					$results[] = array(
						'attachment_id' => $attachment_id,
						'result'        => 'failed',
						'post_id'       => 0,
						'message'       => $item_thumbnail_failed_msg,
						'warning'       => '',
						'edit_link'     => '',
					);
					continue;
				}

				// 6. Save optional per-logo details in Pro when at least one detail is non-empty (ADR-023 Decision 6).
				$warning       = '';
				$raw_details   = ( isset( $entry['details'] ) && is_array( $entry['details'] ) ) ? wp_unslash( $entry['details'] ) : null;
				$clean_details = apply_filters( 'lgx_ls_bulk_upload_normalize_details', null, $raw_details );
				if ( ! Lgx_Pro_Locking::is_pro_ready() ) {
					$clean_details = null;
				} elseif ( is_array( $clean_details ) ) {
					$allowed_detail_keys = array( 'company_url', 'company_name', 'tooltip_text', 'alt_text', 'company_desc' );
					$filtered_details    = array();
					foreach ( $allowed_detail_keys as $detail_key ) {
						if ( array_key_exists( $detail_key, $clean_details ) && is_scalar( $clean_details[ $detail_key ] ) ) {
							$filtered_details[ $detail_key ] = $clean_details[ $detail_key ];
						}
					}
					$clean_details = ! empty( $filtered_details ) ? $filtered_details : null;
				}
				if ( is_array( $clean_details ) ) {
					$meta_saved = update_post_meta( $post_id, '_logosliderwpmeta', wp_slash( $clean_details ) );
					if ( false === $meta_saved ) {
						$warning = $item_details_warning_msg;
					}
				}

				// 7. Assign terms when provided.
				if ( ! empty( $term_ids ) ) {
					$term_res = wp_set_object_terms( $post_id, $term_ids, $taxonomy );
					if ( is_wp_error( $term_res ) ) {
						$warning = ( '' !== $warning ) ? ( $warning . ' ' . $item_terms_warning_msg ) : $item_terms_warning_msg;
					}
				}

				// 7. Created.
				$edit_link = get_edit_post_link( $post_id, 'raw' );
				$results[] = array(
					'attachment_id' => $attachment_id,
					'result'        => 'created',
					'post_id'       => $post_id,
					'message'       => $item_created_msg,
					'warning'       => $warning,
					'edit_link'     => is_string( $edit_link ) ? $edit_link : '',
				);
			} catch ( Exception $e ) {
				$results[] = array(
					'attachment_id' => $attachment_id,
					'result'        => 'failed',
					'post_id'       => 0,
					'message'       => $e->getMessage(),
					'warning'       => '',
					'edit_link'     => '',
				);
			}
		}

		wp_send_json_success( array( 'items' => $results ) );
	}
}
}


