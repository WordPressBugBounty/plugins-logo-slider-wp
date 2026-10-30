<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin
 * @author     LogicHunt <info.logichunt@gmail.com>
 */
if ( ! class_exists( 'Logo_Slider_WP_Admin' ) ) {
class Logo_Slider_WP_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The hook suffix assigned by add_submenu_page()
     *
     * @since    1.0.0
     * @access   public
     * @var      string
     */
    public $plugin_screen_hook_suffix;

    /**
     * The dashboard module controller.
     *
     * @since    1.0.0
     * @access   private
     * @var      Logo_Slider_WP_Dashboard    $dashboard
     */
    private $dashboard;

    /**
     * The settings module controller.
     *
     * @since    1.0.0
     * @access   private
     * @var      Logo_Slider_WP_Settings    $settings
     */
    private $settings;

    /**
     * The onboarding module controller.
     *
     * @since    1.0.0
     * @access   private
     * @var      Logo_Slider_WP_Onboarding   $onboarding
     */
    private $onboarding;

    /**
     * @var Lgx_Carousel_Settings_API
     */
    private $settings_api;

    /**
     * The plugin plugin_base_file of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string plugin_base_file The plugin plugin_base_file of the plugin.
     */
    protected $plugin_base_file;


    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */

    private $version;


    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $meta_form;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */

    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings_api = new Lgx_Carousel_Settings_API($plugin_name, $version);

        $this->init_meta_form();

        //For Sidebar
        // Edition-aware: resolves to the Free or Pro bootstrap file automatically.
        $this->plugin_base_file = LGX_LS_PLUGIN_BASE;

        // Load admin modules
        $this->load_modules();

    }


    /**
     * Load and initialize admin modules.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_modules() {
        // Dashboard Module
        require_once plugin_dir_path( __FILE__ ) . 'modules/dashboard/class-logo-slider-wp-dashboard.php';
        $this->dashboard = new Logo_Slider_WP_Dashboard( $this, $this->plugin_name, $this->version );

        // Settings Module
        require_once plugin_dir_path( __FILE__ ) . 'modules/settings/class-logo-slider-wp-settings.php';
        $this->settings = new Logo_Slider_WP_Settings( $this, $this->plugin_name, $this->version );

        // Onboarding Module
        require_once plugin_dir_path( __FILE__ ) . 'onboarding/class-logo-slider-wp-onboarding.php';
        $this->onboarding = new Logo_Slider_WP_Onboarding( $this, $this->plugin_name, $this->version );
    }

    /**
     * Get the dashboard module controller.
     *
     * @return Logo_Slider_WP_Dashboard
     */
    public function get_dashboard() {
        return $this->dashboard;
    }

    /**
     * Get the settings module controller.
     *
     * @return Logo_Slider_WP_Settings
     */
    public function get_settings() {
        return $this->settings;
    }


    /**
     *
     * Initialized Meta field
     *
     */
    private function init_meta_form() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-logo-slider-wp-meta-form.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/LgxMetaForm.php';
        $this->meta_form = new Lgx_Meta_Form();
    }




    /**
     * Declare Custom Post Type For Carousal
     * @since 1.0.0
     *  previous: logosliderwp_initialize
     * New:
     *
     */

    public function register_post_type_for_logo_slider() {

        //custom post type labels
        $labels_logosliderwp = array(
            'name'               => _x('All Logos', 'Logo Slider', 'logo-slider-wp'),
            'singular_name'      => _x('Logo Item', 'Slider Items', 'logo-slider-wp'),
            'menu_name'          => __('Logo Slider', 'logo-slider-wp'),
            'all_items'          => __('Logos', 'logo-slider-wp'),
            'view_item'          => __('View Item', 'logo-slider-wp'),
            'add_new_item'       => __('Add New Logo', 'logo-slider-wp'),
            'add_new'            => __('Add New Logo', 'logo-slider-wp'),
            'edit_item'          => __('Edit Logo', 'logo-slider-wp'),
            'update_item'        => __('Update Logo', 'logo-slider-wp'),
            'search_items'       => __('Search Logo', 'logo-slider-wp'),
            'not_found'          => __('No logos found. Create your first logo!', 'logo-slider-wp'),
            'not_found_in_trash' => __('No logos found in trash.', 'logo-slider-wp'),
            'set_featured_image' => __('Upload Logo', 'logo-slider-wp'),
            'remove_featured_image' => __('Remove Logo', 'logo-slider-wp'),
            'use_featured_image' => __('Use as Logo', 'logo-slider-wp')
        );

        $args_logosliderwp   = array(
            'label'               => __('Logo Slider', 'logo-slider-wp'),
            'description'         => __('Logo Slider WP Post Type', 'logo-slider-wp'),
            'labels'              => $labels_logosliderwp,
            'supports'            => array( 'title', 'thumbnail'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 34, // OLD 15, 80, 5, 21
            'menu_icon'   			=> 'dashicons-images-alt2',
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,// set false from v3.2.6
            'capability_type'     => 'post',
        );


        //declare custom post type logosliderwp
        register_post_type( 'logosliderwp', $args_logosliderwp);


        // Register Taxonomy
        $logosliderwp_cat_args = array(
            'hierarchical'   => true,
            'labels'         => array(
                'name'              => __('Logo Categories', 'logo-slider-wp'),
                'singular_name'     => __('Category', 'logo-slider-wp'),
                'search_items'      => __('Search Categories', 'logo-slider-wp'),
                'all_items'         => __('All Categories', 'logo-slider-wp'),
                'parent_item'       => __('Parent Category', 'logo-slider-wp'),
                'parent_item_colon' => __('Parent Category:', 'logo-slider-wp'),
                'edit_item'         => __('Edit Category', 'logo-slider-wp'),
                'update_item'       => __('Update Category', 'logo-slider-wp'),
                'add_new_item'      => __('Add New Category', 'logo-slider-wp'),
                'new_item_name'     => __('New Category Name', 'logo-slider-wp'),
                'menu_name'         => __('Categories', 'logo-slider-wp'),
                'not_found'         => __('No categories found. Create your first category!', 'logo-slider-wp'),
            ),
            'show_ui'        => true,
            'query_var'      => true,
        );
        register_taxonomy('logosliderwpcat', array('logosliderwp'), $logosliderwp_cat_args);
    }


    /**
     * Add metabox for custom post type
     *
     * @since    1.0.0
     */
    public function adding_meta_boxes_for_logosliderwp() {

        //v_logo_metabox_logosliderwp //metabox_logosliderwp_display
        add_meta_box(
            'lgx_logosliderwp_metabox_postbox', __( 'Additional Details', 'logo-slider-wp' ), array(
            $this,
            'meta_fields_display_for_logosliderwp'
        ), 'logosliderwp', 'normal', 'high'
        );

    }

    /**
     * Enforce Classic Editor for plugin CPTs
     */
    public function disable_block_editor_for_logosliderwp( $use_block_editor, $post_type ) {
        if ( in_array( $post_type, array( 'logosliderwp', 'lgx_lsp_shortcodes' ), true ) ) {
            return false;
        }
        return $use_block_editor;
    }

    /**
     * Change Title Placeholder Text
     */
    public function change_title_placeholder_text( $title, $post = null ) {
        if ( isset( $post->post_type ) && $post->post_type === 'logosliderwp' ) {
            $title = __( 'Enter Brand Name...', 'logo-slider-wp' );
        }
        return $title;
    }


    /**
     * Render Metabox under logosliderwp
     *
     * logosliderwp meta field
     *
     * @param $post
     *
     * @since 1.0
     *
     */

    public function meta_fields_display_for_logosliderwp( $post ) {

        require_once plugin_dir_path( __FILE__ ) . 'partials/meta_fields_display_for_logosliderwp.php';

    }


    /**
     * Determines whether or not the current user has the ability to save meta data associated with this post.
     *
     * Save portfoliopro Meta Field
     *
     * @param        int $post_id //The ID of the post being save
     * @param         bool //Whether or not the user has the ability to save this post.
     */
    public function save_post_metadata_of_logosliderwp( $post_id, $post ) {

        $post_type = 'logosliderwp';

        // If this isn't a 'book' post, don't update it.
        if ( $post_type != $post->post_type ) {
            return;
        }

        if ( empty( $post->post_title ) && 'auto-draft' !== $post->post_status ) {
            remove_action( 'save_post_logosliderwp', array( $this, 'save_post_metadata_of_logosliderwp' ) );
            $new_title = 'Logo #' . $post_id;
            wp_update_post( array(
                'ID'         => $post_id,
                'post_title' => $new_title,
                'post_name'  => sanitize_title( $new_title )
            ) );
            add_action( 'save_post_logosliderwp', array( $this, 'save_post_metadata_of_logosliderwp' ), 10, 2 );
        }

        if ( ! empty( $_POST['metaboxlogosliderwp'] ) ) {

            $postData = wp_unslash( $_POST['metaboxlogosliderwp'] );

            $savable_Data = array();

            if ( $this->user_can_save_for_logo_slider_meta( $post_id, 'metaboxlogosliderwp', $postData['nonce'] ) ) {

                $existing_meta = get_post_meta( $post_id, '_logosliderwpmeta', true );
                $savable_Data  = is_array( $existing_meta ) ? $existing_meta : array();

                // Update date : 29.06.2026, By : @VibeLogic
                if ( isset( $postData['company_url'] ) ) {
                    $savable_Data['company_url']   = esc_url_raw( $postData['company_url'] );
                }
                if ( isset( $postData['company_name'] ) ) {
                    $savable_Data['company_name']  = sanitize_text_field( $postData['company_name'] );
                } else {
                    $savable_Data['company_name']  = '';
                }
                if ( isset( $postData['tooltip_text'] ) ) {
                    $savable_Data['tooltip_text']  = sanitize_text_field( $postData['tooltip_text'] );
                }
                if ( isset( $postData['alt_text'] ) ) {
                    $savable_Data['alt_text'] = sanitize_text_field( $postData['alt_text'] );
                }
                if ( isset( $postData['company_desc'] ) ) {
                    $savable_Data['company_desc']  = sanitize_textarea_field( $postData['company_desc'] );
                }

                update_post_meta( $post_id, '_logosliderwpmeta', $savable_Data );
            }
        }
    }// End  Meta Save


    /**
     * Phase E: Redirect back to "Add New" screen if Save & Add Another was clicked.
     */
    public function save_and_add_another_redirect( $location, $post_id ) {
        if ( isset( $_POST['lgx_save_and_add_another_flag'] ) && $_POST['lgx_save_and_add_another_flag'] === '1' ) {
            if ( get_post_type( $post_id ) === 'logosliderwp' ) {
                $location = admin_url( 'post-new.php?post_type=logosliderwp&lgx_saved=1' );
            }
        }
        return $location;
    }

    /**
     * Phase E: Display success notice after Save & Add Another redirect.
     */
    public function save_and_add_another_success_notice() {
        if ( isset( $_GET['lgx_saved'] ) && $_GET['lgx_saved'] == '1' ) {
            $current_screen = get_current_screen();
            if ( is_object( $current_screen ) && $current_screen->post_type === 'logosliderwp' && $current_screen->action === 'add' ) {
                $class = 'notice notice-success is-dismissible';
                $message = __( 'Logo saved successfully. You can add another one.', 'logo-slider-wp' );
                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
            }
        }
    }

    /**
     * Phase E: Task 2.2 - Add Duplicate Logo row action immediately after Edit
     */
    public function add_duplicate_row_action( $actions, $post ) {
        if ( ! in_array( $post->post_type, array( 'logosliderwp', 'lgx_lsp_shortcodes' ), true ) ) {
            return $actions;
        }

        if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_post', $post->ID ) ) {
            return $actions;
        }

        $duplicate_nonce = wp_create_nonce( 'lgx_duplicate_logo_nonce_' . $post->ID );
        $duplicate_url = admin_url( 'admin.php?action=lgx_duplicate_logo&post=' . $post->ID . '&_wpnonce=' . $duplicate_nonce );
        $duplicate_link = '<a href="' . esc_url( $duplicate_url ) . '" aria-label="' . esc_attr__( 'Duplicate', 'logo-slider-wp' ) . '">' . esc_html__( 'Duplicate', 'logo-slider-wp' ) . '</a>';

        // Insert immediately after 'edit' if it exists, otherwise append
        if ( isset( $actions['edit'] ) ) {
            $new_actions = array();
            foreach ( $actions as $key => $action ) {
                $new_actions[ $key ] = $action;
                if ( 'edit' === $key ) {
                    $new_actions['duplicate'] = $duplicate_link;
                }
            }
            $actions = $new_actions;
        } else {
            $actions['duplicate'] = $duplicate_link;
        }

        return $actions;
    }

    /**
     * Phase E: Task 2.2 - Handle Duplicate Logo action
     */
    public function duplicate_logo_action() {
        if ( empty( $_GET['post'] ) || empty( $_GET['_wpnonce'] ) ) {
            wp_die( esc_html__( 'No post to duplicate has been supplied!', 'logo-slider-wp' ) );
        }

        $post_id = absint( $_GET['post'] );
        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'lgx_duplicate_logo_nonce_' . $post_id ) ) {
            wp_die( esc_html__( 'Security check failed!', 'logo-slider-wp' ) );
        }

        if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_die( esc_html__( 'You do not have permission to duplicate this logo.', 'logo-slider-wp' ) );
        }

        $post = get_post( $post_id );
        if ( ! $post || $post->post_type !== 'logosliderwp' ) {
            wp_die( esc_html__( 'Logo creation failed, could not find original logo:', 'logo-slider-wp' ) . ' ' . esc_html( $post_id ) );
        }

        // Generate unique title
        global $wpdb;
        $title = $post->post_title;
        $new_title = $title . ' ' . __( '(Copy)', 'logo-slider-wp' );

        $copy_number = 2;
        $max_copies = 100;
        while ( $copy_number <= $max_copies ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1", $new_title, 'logosliderwp' ) );
            if ( ! $exists ) {
                break;
            }
            $new_title = $title . ' ' . sprintf( __( '(Copy %d)', 'logo-slider-wp' ), $copy_number );
            $copy_number++;
        }

        $args = array(
            'post_type'      => $post->post_type,
            'post_title'     => $new_title,
            'post_status'    => 'draft',
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_password'  => $post->post_password,
            'ping_status'    => $post->ping_status,
            'comment_status' => $post->comment_status,
            'post_author'    => get_current_user_id(),
            'menu_order'     => 0, // Explicitly reset menu order
        );

        $new_post_id = wp_insert_post( $args );

        if ( is_wp_error( $new_post_id ) ) {
            wp_die( esc_html__( 'Error creating duplicate logo.', 'logo-slider-wp' ) );
        }

        // Duplicate Custom Meta
        $post_meta = get_post_meta( $post_id, '_logosliderwpmeta', true );
        if ( ! empty( $post_meta ) && is_array( $post_meta ) ) {
            update_post_meta( $new_post_id, '_logosliderwpmeta', $post_meta );
        }

        // Duplicate Featured Image
        $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
        if ( ! empty( $thumbnail_id ) ) {
            update_post_meta( $new_post_id, '_thumbnail_id', $thumbnail_id );
        }

        // Duplicate Taxonomies
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
            if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
                wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
            }
        }

        // Redirect to edit screen
        $redirect_url = admin_url( 'post.php?action=edit&post=' . $new_post_id . '&lgx_duplicated=1' );
        wp_redirect( $redirect_url );
        exit;
    }

    /**
     * Phase E: Task 2.2 - Display success notice after Duplicate Logo redirect
     */
    public function duplicate_logo_success_notice() {
        if ( isset( $_GET['lgx_duplicated'] ) && $_GET['lgx_duplicated'] == '1' ) {
            $current_screen = get_current_screen();
            if ( is_object( $current_screen ) && $current_screen->post_type === 'logosliderwp' && $current_screen->action === 'edit' ) {
                $class = 'notice notice-success is-dismissible';
                $message = __( 'Logo duplicated successfully. Review the copied logo and publish it when ready.', 'logo-slider-wp' );
                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
            }
        }
    }

    /**
     * Phase E: Task 3.3 - Display warning if trying to sort on paginated list
     */
    public function reorder_pagination_notice() {
        $current_screen = get_current_screen();
        if ( is_object( $current_screen ) && $current_screen->id === 'edit-logosliderwp' && $current_screen->post_type === 'logosliderwp' ) {
            if ( isset( $_GET['paged'] ) && $_GET['paged'] > 1 ) {
                $transient_key = 'lgx_reorder_notice_' . get_current_user_id();
                if ( ! get_transient( $transient_key ) ) {
                    set_transient( $transient_key, 1, 12 * HOUR_IN_SECONDS );
                    echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__( 'Drag & drop ordering is available only on the first page. To reorder all logos, increase the number of items displayed using the Screen Options tab.', 'logo-slider-wp' ) . '</p></div>';
                }
            }
        }
    }


    /**
     * Determines whether or not the current user has the ability to save meta data associated with this post.
     *
     * user_can_save_for_logo_slider_meta
     *
     * @param        int $post_id // The ID of the post being save
     * @param        bool /Whether or not the user has the ability to save this post.
     *
     * @since 1.0
     */
    public function user_can_save_for_logo_slider_meta( $post_id, $action, $nonce ) {

        $is_autosave    = wp_is_post_autosave( $post_id );
        $is_revision    = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $nonce ) && wp_verify_nonce( $nonce, $action ) );

        // Return true if the user is able to save; otherwise, false.
        return ! ( $is_autosave || $is_revision ) && $is_valid_nonce && current_user_can( 'edit_post', $post_id );

    }




    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        //  $this->plugin_screen_hook_suffix  = add_submenu_page('edit.php?post_type=logosliderwp', __('Logo Slider Default Settings', 'logo-slider-wp'), __('Default Settings', 'logo-slider-wp'), 'manage_options', 'logosliderwpsettings', array($this, 'display_plugin_admin_settings_legacy'));
        $this->plugin_screen_hook_suffix  = add_submenu_page('edit.php?post_type=logosliderwp', __('Help & Resources', 'logo-slider-wp'), __('Help & Resources', 'logo-slider-wp'), 'manage_options', 'logosliderwp-help-resources', array($this, 'display_plugin_help_resources_page'));

    }

    /**
     * Reorder the submenu items to ensure Showcase Builder is after Categories.
     */
    public function reorder_submenu_pages() {
        global $submenu;

        remove_submenu_page( 'edit.php?post_type=logosliderwp', 'post-new.php?post_type=logosliderwp' );
        remove_submenu_page( 'edit.php?post_type=logosliderwp', 'post-new.php?post_type=lgx_lsp_shortcodes' );

        $parent_slug = 'edit.php?post_type=logosliderwp';

        if ( ! isset( $submenu[ $parent_slug ] ) ) {
            return;
        }

        $ordered = array();

        $desired_order = array(
            'logosliderwp-dashboard',
            'edit.php?post_type=logosliderwp',
            'edit.php?post_type=lgx_lsp_shortcodes',
            'edit-tags.php?taxonomy=logosliderwpcat&post_type=logosliderwp',
            'logosliderwp-settings',
            'logosliderwp-help-resources'
        );

        $index = 10;
        foreach ( $desired_order as $slug ) {
            foreach ( $submenu[ $parent_slug ] as $key => $item ) {
                $item_slug = str_replace('&amp;', '&', $item[2]);
                if ( $item_slug === $slug ) {
                    $ordered[$index] = $item;
                    $index += 10;
                    unset( $submenu[ $parent_slug ][ $key ] );
                }
            }
        }

        // Append any remaining items that were not specifically ordered
        foreach ( $submenu[ $parent_slug ] as $item ) {
            $ordered[$index] = $item;
            $index += 10;
        }

        $submenu[ $parent_slug ] = $ordered;
    }

    /**
     * Change Feature image input Position
     * old: logo_slider_wp_img_box
     * new: changing_meta_box_position_of_brand_logo
     */
    public  function changing_meta_box_position_of_brand_logo(){
        remove_meta_box( 'postimagediv', 'logosliderwp', 'side' );
        add_meta_box(
            'postimagediv',
            __('Logo Image', 'logo-slider-wp'),
            'post_thumbnail_meta_box',
            'logosliderwp',
            'normal',
            'high'
        );
    }



    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_links_admin_plugin_page_title( $links ) {
        return array_merge( array(
            'create' => '<a href="' . admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ) . '" >' . esc_html__( 'Create Showcase', 'logo-slider-wp') . '</a>',
            'docs'    => '<a style="font-weight: bold;" href="' .esc_url('https://docs.logichunt.com/logo-slider-wp/') . '" target="_blank">' . esc_html__( 'Docs', 'logo-slider-wp') . '</a>',
            'support' => '<a style="color:#00a500;  font-weight: bold;" href="' .esc_url('https://logichunt.com/support/') . '" target="_blank">' . esc_html__( 'Support', 'logo-slider-wp') . '</a>',

        ), $links );

    }//end plugin_listing_setting_link



    /**
     * Add support link to plugin description in /wp-admin/plugins.php
     *
     * @param  array  $plugin_meta
     * @param  string $plugin_file
     *
     * @return array
     */
    public function add_links_admin_plugin_page_description($plugin_meta, $plugin_file) {

        if ($this->plugin_base_file == $plugin_file) {
            $plugin_meta[] = sprintf(
                '<a href="%s">%s</a>', 'https://logichunt.com/support/', __('Get Support', 'logo-slider-wp')
            );
        }

        return $plugin_meta;
    }



    function display_plugin_help_resources_page() {
        $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->plugin_base_file );

        include('partials/admin-help-resources.php');
    }

    /**
     * Suppress every admin-notice hook on the Help & Resources screen only.
     * Hooked to in_admin_header, which WP core fires before
     * network_admin_notices/user_admin_notices/admin_notices/
     * all_admin_notices (wp-admin/admin-header.php), so removing those
     * actions here stops any notice -- ours or a third party's, present or
     * future -- from ever being queued for this screen, rather than trying
     * to reposition or hide output that has already printed.
     */
    public function suppress_admin_notices_on_help_resources_page() {
        $screen = get_current_screen();
        if ( ! $screen || $screen->id !== $this->plugin_screen_hook_suffix ) {
            return;
        }

        remove_all_actions( 'network_admin_notices' );
        remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );
    }


    /**
     *
     */

    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public function display_plugin_admin_settings_legacy() {
        $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->plugin_base_file );


        include('partials/admin-settings-display.php');
    }

    /**
     * Settings init
     */
    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public function logo_slider_wp_setting_init_legacy() {
        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections_legacy());
        $this->settings_api->set_fields($this->get_settings_fields_legacy());

        //initialize settings
        $this->settings_api->admin_init();

        //$role = get_role('administrator');
    }
    



    /**
     * Ensure post thumbnail support is turned on.
     */
    public function add_thumbnail_support() {
        if ( ! current_theme_supports( 'post-thumbnails' ) ) {
            add_theme_support( 'post-thumbnails' );
        }
        add_post_type_support( 'logosliderwp', 'thumbnail' );
    }


    /**
     * Setings Sections
     * @return array|mixed|void
     */

    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public function get_settings_sections_legacy() {

        $sections = array(
            array(
                'id'    => 'logosliderwp_basic',
                'title' => __('Basic Settings', 'logo-slider-wp'),
            ),
            array(
                'id'    => 'logosliderwp_style',
                'title' => __('Style Settings', 'logo-slider-wp'),
            ),

            array(
                'id'    => 'logosliderwp_responsive',
                'title' => __('Responsive Control', 'logo-slider-wp'),
            ),
            array(
                'id'    => 'logosliderwp_config',
                'title' => __('Slider Options', 'logo-slider-wp'),
            ),

            array(
                'id'    => 'logosliderwp_adv',
                'title' => __('Advanced Settings', 'logo-slider-wp'),
            ),


        );

        $sections = apply_filters('logo_slider_settings_sections', $sections);

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     *
     *  Note : will be deprecated v 4.0.0
     */
    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public  function get_settings_fields_legacy() {

        include('legacy/settings-fields.php');

        $settings_fields = apply_filters('logo_slider_settings_fields', $settings_fields);

        return $settings_fields;
    }


    /**
     * Modified get post for post type order
     *
     */
    public function modify_query_get_posts($query) {

        if ( ! is_admin() && ( isset( $query->query_vars['post_type'] ) &&  ( is_array( $query->query_vars['post_type'] ) && in_array( 'logosliderwp', $query->query_vars['post_type'] ) ) ) ) {

           // $query->set( 'orderby', 'menu_order' ); // hided from v3.2.0
           // $query->set( 'order' , 'ASC' ); // hided from v3.2.0

        } elseif ( is_admin() ) {
            if ( $query->is_main_query() ) {
                $currentScreen = get_current_screen();
                if ( is_object( $currentScreen ) && $currentScreen->id == 'edit-logosliderwp' && $currentScreen->post_type == 'logosliderwp' ) {
                    $query->set( 'post_type', 'logosliderwp' );
                    $query->set( 'orderby', 'menu_order' );
                    $query->set( 'order' , 'ASC' );
                }
            }
        }
    }



    /**
     * Filters the columns displayed in the Posts list table for a specific post type.
     *
     * apply_filters( "manage_{$post_type}_posts_columns", string[] $post_columns )
     * Deafult Value : cb, title, taxonomy-logosliderwpcat, date
     * @param $default_columns
     */
    public function add_new_column_head_for_logosliderwp($default_columns) {

        $columns = array();
        if ( isset( $default_columns['cb'] ) ) {
            $columns['cb'] = $default_columns['cb'];
        }
        $columns['lgx_ls_reorder'] = '<span class="screen-reader-text">' . __( 'Reorder', 'logo-slider-wp' ) . '</span>';
        if ( isset( $default_columns['title'] ) ) {
            $columns['title'] = __( 'Brand Name', 'logo-slider-wp' );
        }

        $columns['lgx_ls_logo']         = __( 'Logo', 'logo-slider-wp' );
        $columns['lgx_ls_category']     = __( 'Categories', 'logo-slider-wp' );
        $columns['lgx_ls_url']          = __( 'URL', 'logo-slider-wp' );

        if ( isset( $default_columns['date'] ) ) {
            $columns['date'] = $default_columns['date'];
        }

        $known_keys = array( 'cb', 'lgx_ls_reorder', 'title', 'lgx_ls_logo', 'lgx_ls_url', 'lgx_ls_category', 'date' );
        foreach ( $default_columns as $key => $label ) {
            if ( ! in_array( $key, $known_keys, true ) ) {
                $columns[ $key ] = $label;
            }
        }

        return $columns;
    }

    /**
     * Phase E: Render custom fields in the Quick Edit panel
     */
    public function display_quick_edit_custom_box( $column_name, $post_type ) {
        if ( $post_type !== 'logosliderwp' ) {
            return;
        }

        // We only want to output our fields once. We pick an arbitrary custom column.
        if ( $column_name !== 'lgx_ls_url' ) {
            return;
        }

        // Output our required custom nonce so the native save_post hook passes validation
        wp_nonce_field( 'metaboxlogosliderwp', 'metaboxlogosliderwp[nonce]' );
        ?>
<fieldset class="inline-edit-col-right">
    <div class="inline-edit-col">
        <div class="inline-edit-group wp-clearfix">
            <label class="alignleft">
                <span class="title"><?php esc_html_e( 'Custom Brand Name', 'logo-slider-wp' ); ?></span>
                <span class="input-text-wrap">
                    <input type="text" name="metaboxlogosliderwp[company_name]" class="lgx-quick-edit-company-name"
                        value="" autocomplete="off" />
                </span>
            </label>
        </div>
        <div class="inline-edit-group wp-clearfix">
            <label class="alignleft">
                <span class="title"><?php esc_html_e( 'Website URL', 'logo-slider-wp' ); ?></span>
                <span class="input-text-wrap">
                    <input type="url" name="metaboxlogosliderwp[company_url]" class="lgx-quick-edit-company-url"
                        value="" autocomplete="off" />
                </span>
            </label>
        </div>
    </div>
</fieldset>
<?php
    }

    /**
     * Fires for each custom column of a specific post type in the Posts list table.
     * do_action( "manage_{$post->post_type}_posts_custom_column", string $column_name, int $post_id )]
     *
     * @param $column
     * @param $post_id
     */
    public function define_admin_column_value_for_logosliderwp($column, $post_id) {
        switch ($column) {
            case 'lgx_ls_reorder':
                if ( isset( $_GET['paged'] ) && $_GET['paged'] > 1 ) {
                    echo '<span class="lgx-drag-handle-disabled dashicons dashicons-menu" aria-label="' . esc_attr__( 'Drag disabled', 'logo-slider-wp' ) . '" aria-disabled="true" title="' . esc_attr__( 'Drag & drop ordering is available only on the first page.', 'logo-slider-wp' ) . '"></span>';
                } else {
                    echo '<span class="lgx-drag-handle dashicons dashicons-menu" aria-label="' . esc_attr__( 'Drag to reorder', 'logo-slider-wp' ) . '" title="' . esc_attr__( 'Drag to reorder', 'logo-slider-wp' ) . '"></span>';
                }
                break;

            case 'lgx_ls_category':

                    $lgx_logo_categories = get_the_terms( $post_id, 'logosliderwpcat' );

                    if ( ! empty( $lgx_logo_categories ) && ! is_wp_error( $lgx_logo_categories ) ) {

                        foreach ($lgx_logo_categories as $term) {
                            $filter_url = admin_url( 'edit.php?post_type=logosliderwp&logosliderwpcat=' . $term->slug );
                            echo '<a href="' . esc_url( $filter_url ) . '" class="lgx-cat-badge-link" title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</a> ';
                        }

                    }
                    break;

            case 'lgx_ls_url':
                $metavalues = get_post_meta( $post_id, '_logosliderwpmeta', true );
                $url = isset( $metavalues['company_url'] ) ? $metavalues['company_url'] : '';
                $brand_name = isset( $metavalues['company_name'] ) ? $metavalues['company_name'] : '';

                // Hidden div for Quick Edit JS
                echo '<div class="hidden" id="lgx_ls_brand_inline_' . esc_attr( $post_id ) . '">' . esc_html( $brand_name ) . '</div>';

                // Hidden div for Quick Edit JS
                echo '<div class="hidden" id="lgx_ls_url_inline_' . esc_attr( $post_id ) . '">' . esc_attr( $url ) . '</div>';

                if ( ! empty( $url ) ) {
                    echo '<a href="' . esc_url( $url ) . '" target="_blank" class="lgx-list-url" title="' . esc_attr__( 'Visit website', 'logo-slider-wp' ) . '">';
                    echo '<span class="dashicons dashicons-external"></span> ';
                    $display_url = preg_replace( '(^https?://)', '', $url );
                    $display_url = rtrim( $display_url, '/' );
                    echo esc_html( $display_url );
                    echo '</a>';
                } else {
                    echo '<span class="lgx-empty-value" aria-hidden="true" style="color: #a7aaad;">&mdash;</span><span class="screen-reader-text">' . __( 'No URL provided', 'logo-slider-wp' ) . '</span>';
                }
                break;

            case 'lgx_ls_logo':
                echo '<div class="lgx-list-thumbnail">';
                echo '<a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">';
                if( has_post_thumbnail( $post_id ) ) {
                    $post_thumbnail_id = get_post_thumbnail_id( $post_id );
                    $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
                    if ( ! empty( $post_thumbnail_img ) ) {
                        $meta = get_post_meta( $post_id, '_logosliderwpmeta', true );
                        $brand_name = isset( $meta['company_name'] ) ? $meta['company_name'] : '';
                        $alt_text = ! empty( $brand_name ) ? $brand_name : get_the_title( $post_id );
                        if ( empty( $alt_text ) ) {
                            $alt_text = __( 'Logo', 'logo-slider-wp' ) . ' ' . $post_id;
                        }
                        echo '<img src="' . esc_url( $post_thumbnail_img[0] ) . '" alt="' . esc_attr( $alt_text ) . '" />';
                    } else {
                        echo '<span class="lgx-no-logo-placeholder"><span class="dashicons dashicons-format-image"></span><br/>' . esc_html__( 'No Logo', 'logo-slider-wp' ) . '</span>';
                    }
                } else {
                    echo '<span class="lgx-no-logo-placeholder"><span class="dashicons dashicons-format-image"></span><br/>' . esc_html__( 'No Logo', 'logo-slider-wp' ) . '</span>';
                }
                echo '</a>';
                echo '</div>';
                break;

            default:
                break;
        }
    }


    /**
     *  Save post for re ordering
     * @since    2.3.0
     */

    public function save_post_reorder_for_logosliderwp() {
        global $wpdb;
        $result = array(
            'type' => 'error',
            'message' => 'Action required.',
        );

        if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['nonce'] ), 'save_logosliderwp_nonce' ) ) {
            $result['type'] = 'error';
            $result['message'] = 'WP nonce verification failed.';
            wp_send_json( $result );
        }

        try {
            parse_str( wp_unslash( $_POST['post_id_serialize'] ), $post_data );
            //$wpdb->queries( 'START TRANSACTION' );

            if ( ! is_array( $post_data ) || count( $post_data ) < 1 ) {
                $result['message'] = 'Available data not found.';
            } else {
                foreach ( $post_data['post'] as $menu_order => $post_id ) {
                    $post_id = (int) $post_id;
                    if ( ! current_user_can( 'edit_post', $post_id ) ) {
                        continue;
                    }
                    $wpdb->update( $wpdb->posts, array( 'menu_order' => (int)$menu_order ), array( 'ID' => (int)$post_id ) );
                }
            }

            //$wpdb->queries( 'COMMIT' );
            $result['type'] = 'success';
            $result['message'] = 'Reorder has been successful';
        } catch (Exception $exception) {
            //$wpdb->queries( 'ROLLBACK' );
            $result['message'] = $exception->getMessage();
        }

        wp_send_json( $result );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Logo_Slider_WP_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Logo_Slider_WP_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $currentScreen = get_current_screen();
        $is_plugin_screen = (
            $currentScreen->post_type === 'logosliderwp' ||
            $currentScreen->post_type === 'lgx_lsp_shortcodes' ||
            strpos( $currentScreen->id, 'logo-slider' ) !== false ||
            strpos( $currentScreen->id, 'logoslider' ) !== false ||
            strpos( $currentScreen->id, 'lgx_lsp_shortcodes' ) !== false
        );

        // The lgxicon @font-face plus the top-level admin-menu icon override
        // load on every admin screen, so the custom Logo Slider menu icon is
        // persistent site-wide in #adminmenu. All other admin CSS remains
        // scoped to plugin screens below.
        wp_enqueue_style( $this->plugin_name . '-admin-icon', plugin_dir_url( __FILE__ ) . 'css/lgx-icon.css', array(), $this->version, 'all' );
        wp_add_inline_style(
            $this->plugin_name . '-admin-icon',
            "#adminmenu .menu-icon-logosliderwp .wp-menu-image:before{content:\"\\e900\";font-family:'lgxicon',serif !important;font-size:20px !important;}"
        );

        if ( $is_plugin_screen ) {
            // Enqueue dashboard CSS (shared card/button/grid primitives) for
            // the Help & Resources page — same reuse pattern this screen has
            // always used, just under the repurposed slug.
            if ( strpos( $currentScreen->id, 'logosliderwp-help-resources' ) !== false ) {
                wp_enqueue_style(
                    'logosliderwp-dashboard-css',
                    plugin_dir_url( __FILE__ ) . 'modules/dashboard/assets/css/dashboard.css',
                    array(),
                    $this->version
                );
            }

            wp_enqueue_style( $this->plugin_name . '-admin-reset', plugin_dir_url( __FILE__ ) . 'css/logo-slider-wp-admin-reset.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name . '-alertify', plugin_dir_url( __FILE__ ) . 'css/alertify.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name . '-admin-slider', plugin_dir_url( __FILE__ ) . 'css/logo-slider-wp-admin.min.css', array( 'wp-color-picker' ), $this->version, 'all' );
            $custom_css_ver = filemtime( plugin_dir_path( __FILE__ ) . 'css/logo-slider-wp-admin-custom.css' );
            wp_enqueue_style( $this->plugin_name . '-admin-custom', plugin_dir_url( __FILE__ ) . 'css/logo-slider-wp-admin-custom.css', array( $this->plugin_name . '-admin-slider' ), $custom_css_ver, 'all' );

            // Force precise table column widths to bypass WP core specificity and cache
            $custom_inline_css = "
                table.wp-list-table th#lgx_ls_reorder, table.wp-list-table td.lgx_ls_reorder { width: 32px !important; padding: 11px 0 0 0 !important; text-align: center !important; vertical-align: top !important; }
                table.wp-list-table th#cb, table.wp-list-table td.cb, table.wp-list-table th.check-column, table.wp-list-table td.check-column { width: 2.2em !important; vertical-align: top !important; padding: 11px 0 0 3px !important; }
                table.wp-list-table th#title, table.wp-list-table th.column-title, table.wp-list-table td.title { width: auto !important; padding-left: 10px !important; vertical-align: top !important; }
                table.wp-list-table th#lgx_ls_logo, table.wp-list-table th.column-lgx_ls_logo, table.wp-list-table td.column-lgx_ls_logo { width: 12% !important; vertical-align: top !important; padding-top: 11px !important; text-align: center !important; }
                table.wp-list-table th#lgx_ls_category, table.wp-list-table th.column-lgx_ls_category, table.wp-list-table td.column-lgx_ls_category { width: 18% !important; vertical-align: top !important; padding-top: 11px !important; }
                table.wp-list-table th#lgx_ls_url, table.wp-list-table th.column-lgx_ls_url, table.wp-list-table td.column-lgx_ls_url { width: 22% !important; vertical-align: top !important; padding-top: 11px !important; }
                table.wp-list-table td.column-date { vertical-align: top !important; padding-top: 11px !important; }
                table.wp-list-table .lgx-list-thumbnail { display: inline-flex !important; width: auto !important; height: 45px !important; padding: 4px !important; background: #ffffff !important; }
            ";
            wp_add_inline_style( $this->plugin_name . '-admin-custom', $custom_inline_css );
        }

    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Logo_Slider_WP_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Logo_Slider_WP_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */


        $currentScreen = get_current_screen();
        /*   echo '<pre>';
           print_r($currentScreen);
           echo '</pre>';*/
        if( ( $currentScreen->post_type == 'logosliderwp' ) || ( $currentScreen->post_type == 'lgx_lsp_shortcodes' ) ) {

            $post = get_post();
            $post_status = $post ? $post->post_status : 'auto-draft';

            $save_and_add_another_text = __( 'Save & Add Another', 'logo-slider-wp' );
            if ( ! in_array( $post_status, array( 'publish', 'future', 'private' ), true ) || ( $post && 0 === $post->ID ) ) {
                if ( current_user_can( 'publish_posts' ) ) {
                    $save_and_add_another_text = __( 'Publish & Add Another', 'logo-slider-wp' );
                } else {
                    $save_and_add_another_text = __( 'Submit & Add Another', 'logo-slider-wp' );
                }
            } else {
                $save_and_add_another_text = __( 'Update & Add Another', 'logo-slider-wp' );
            }

            $translation_array = array(
                //'add_leftimg_title'  => __('Add Previous Arrow Image', 'logo-slider-wp'),
                //'add_rightimg_title' => __('Add Next Arrow Image', 'logo-slider-wp'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'check_nonce' => wp_create_nonce('save_logosliderwp_nonce'),
                'save_and_add_another_text' => $save_and_add_another_text,
            );


            wp_register_script($this->plugin_name . '-alertify', plugin_dir_url( __FILE__ ) . 'js/alertify.min.js', array(), $this->version, true );

            $is_list_view = ( $currentScreen->base === 'edit' );

            if ( ! $is_list_view ) {
                wp_register_script($this->plugin_name . '-wp-color-picker-alpha' , plugin_dir_url( __FILE__ ) . 'js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), $this->version, true );
                wp_register_script($this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/logo-slider-wp-admin.js', array( 'jquery', 'jquery-ui-sortable', $this->plugin_name . '-wp-color-picker-alpha', $this->plugin_name . '-alertify' ), $this->version, true );
            } else {
                wp_register_script($this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/logo-slider-wp-admin.js', array( 'jquery', 'jquery-ui-sortable', $this->plugin_name . '-alertify' ), $this->version, true );
            }

            wp_localize_script($this->plugin_name . '-admin', 'wpnpaddon', $translation_array);

            wp_enqueue_script( $this->plugin_name . '-admin' );

            if ( ! $is_list_view && ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }

        }

        // Help & Resources screen only: FAQ expand/collapse behavior.
        if ( strpos( $currentScreen->id, 'logosliderwp-help-resources' ) !== false ) {
            wp_enqueue_script(
                $this->plugin_name . '-help-resources',
                plugin_dir_url( __FILE__ ) . 'js/logo-slider-wp-help-resources.js',
                array( 'jquery' ),
                $this->version,
                true
            );
        }
    }


    /**
     * @param $plugin_array
     * @return mixed
     */


    public function lgx_owl_register_tinymce_plugin($plugin_array) {
        $plugin_array['lgx_logo_button'] = plugin_dir_url( __FILE__ ) . 'assets/js/logo-slider-wp-tinymce.js';
        return $plugin_array;
    }

    public function lgx_owl_add_tinymce_button($buttons) {
        $buttons[] = "lgx_logo_button";
        return $buttons;
    }



    /*************************************************************************
     *
     *
     *
     *  Newly Added: 2021
     *
     *
     *
     *
     * **************************************************************************/


    /**
     * Register post type for shortcode Post Type
     *
     *
     */
    public function register_post_type_for_lgx_logo_slider_shortcodes() {

        $labels = array(
            'name'               => _x( 'All Showcases', 'Logo Showcase', 'logo-slider-wp' ),
            'singular_name'      => _x( 'Showcase', 'Showcase Items', 'logo-slider-wp' ),
            'menu_name'          => __( 'Showcases', 'logo-slider-wp' ),
            'all_items'          => __( 'Showcases', 'logo-slider-wp' ),
            'view_item'          => __( 'View Items', 'logo-slider-wp' ),
            'add_new_item'       => __( 'Create Showcase', 'logo-slider-wp' ),
            'add_new'            => __( 'Create Showcase', 'logo-slider-wp' ),
            'edit_item'          => __( 'Edit Showcase', 'logo-slider-wp' ),
            'update_item'        => __( 'Update Showcase', 'logo-slider-wp' ),
            'search_items'       => __( 'Search In Item', 'logo-slider-wp' ),
            'not_found'          => __( 'No Showcase found', 'logo-slider-wp' ),
            'not_found_in_trash' => __( 'No Showcase found in trash', 'logo-slider-wp' )
        );

        $args   = array(
            'label'               => __( 'Logo Slider Shortcode', 'logo-slider-wp' ),
            'description'         => __( 'Generate Shortcode for Logo Slider', 'logo-slider-wp' ),
            'labels'              => $labels,
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => 'edit.php?post_type=logosliderwp',
            'hierarchical'    => false,
            'query_var'       => false,
            'supports'        => array( 'title' ),
            'capability_type' => 'post',
        );

        register_post_type( 'lgx_lsp_shortcodes', $args);
    }

    /**
     * FORCES the Showcase Builder edit screen's Screen Options layout to
     * 1 column on every load — including overriding an existing, explicit
     * prior 2-column choice. Supersedes the original 004 §4 default-only
     * behavior (git history: b58f95f). Product decision: this screen is
     * this plugin's own admin Builder only, no frontend rendering — the
     * sidebar has no product function here. Same hook, same interception
     * point as the prior default-only version — get_user_option() hands
     * this filter the real stored value and this callback discards it
     * unconditionally. Scope is self-limiting: the hook name is built from
     * the screen ID ('get_user_option_' . "screen_layout_$this->id"), so
     * this can only ever fire for the lgx_lsp_shortcodes screen. See
     * docs/autonomous/mistake/reports/005-force-1column-mechanism-report.md.
     *
     * @param mixed $result Existing filtered value (ignored).
     * @return int Always 1.
     */
    public function force_screen_columns_for_lgx_lsp_shortcodes( $result ) {
        return 1;
    }

    /**
     * Caps the Showcase Builder edit screen's Screen Options "Layout" choice
     * to 1 column only, so the UI stops offering a "2 columns" option that
     * force_screen_columns_for_lgx_lsp_shortcodes() would silently override
     * anyway. Uses WordPress core's own documented back-compat filter
     * (wp-admin/includes/class-wp-screen.php:945, "provides back-compat for
     * plugins using the back-compat Filters instead of add_screen_option()").
     * Verified live (report 005): this does not remove the "Layout" fieldset
     * outright — WP_Screen::render_screen_layout() still renders it with
     * exactly one radio ("1 column"), permanently checked, since its render
     * loop runs from 1 to this capped max. The alternative tested — fully
     * unsetting the layout_columns option via WP_Screen::remove_option() —
     * was rejected: it also skips the get_user_option() call that
     * force_screen_columns_for_lgx_lsp_shortcodes() depends on, causing the
     * screen to fall back to native columns-2 rendering (report 005 §2).
     *
     * @param array  $columns   Map of screen ID => max column count.
     * @param string $screen_id Current screen's ID.
     * @return array
     */
    public function cap_screen_columns_for_lgx_lsp_shortcodes( $columns, $screen_id ) {
        if ( 'lgx_lsp_shortcodes' === $screen_id ) {
            $columns[ $screen_id ] = 1;
        }
        return $columns;
    }



    /**
     * Add meta box for custom post type
     *
     * @since    1.0.0
     */
    public function adding_meta_boxes_for_lgx_lsp_shortcodes() {
        add_meta_box(
            'lgx_lsp_shortcodes_meta_box_panel',
            __( 'Logo Slider Shortcode Meta Field Panel', 'logo-slider-wp'),
            array(
                $this,
                'meta_fields_display_for_lgx_lsp_shortcodes' //Pattern --> meta_box_panel_display_for_{post_type}
            ),
            'lgx_lsp_shortcodes',
            'normal',
            'high'
        );
    }



    /**
     * Render Meta Box under logosliderwp
     *
     * logosliderwp meta field
     *
     * @param $post
     *
     * @since 1.0
     *
     */
    public function meta_fields_display_for_lgx_lsp_shortcodes( $post ) {

        require_once plugin_dir_path( __FILE__ ) . 'partials/shortcode_meta_display/meta_fields_display_for_lgx_lsp_shortcodes.php';

    }


    /**
     * Determines whether or not the current user has the ability to save meta data associated with this post.
     *
     * Save lgx_lsp_shortcodes Meta Field
     *
     * @param        int $post_id //The ID of the post being save
     * @param         bool //Whether or not the user has the ability to save this post.
     */
    public function save_post_metadata_of_lgx_lsp_shortcodes( $post_id, $post ) {


        $post_type = 'lgx_lsp_shortcodes';

        // If this isn't a 'book' post, don't update it.
        if ( $post_type != $post->post_type ) {
            return;
        }

        /* Task 014/Commit 4b: server-side blank-title safety net, mirroring
           save_post_metadata_of_logosliderwp()'s existing "Logo #ID" pattern
           above. Client-side auto-generation (initShowcaseNameSync(),
           showcase-builder.js) already populates the native title before
           this handler ever runs, but this closes the gap for the case that
           relies on: JS failing to bind (disabled, blocked, error earlier in
           the page) on a showcase's very first save, which would otherwise
           leave post_title genuinely blank with nothing catching it here. */
        if ( empty( $post->post_title ) && 'auto-draft' !== $post->post_status ) {
            remove_action( 'save_post_lgx_lsp_shortcodes', array( $this, 'save_post_metadata_of_lgx_lsp_shortcodes' ) );
            $new_title = 'Showcase #' . $post_id;
            wp_update_post( array(
                'ID'         => $post_id,
                'post_title' => $new_title,
                'post_name'  => sanitize_title( $new_title ),
            ) );
            add_action( 'save_post_lgx_lsp_shortcodes', array( $this, 'save_post_metadata_of_lgx_lsp_shortcodes' ), 10, 2 );
        }

        if ( ! empty( $_POST['meta_lgx_lsp_shortcodes'] ) ) {

            $postData = wp_unslash( $_POST['meta_lgx_lsp_shortcodes'] );

                //echo '<pre>';  print_r($postData); echo '</pre>'; wp_die();

            $savable_Data = array();


            if ( $this->user_can_save_for_logo_slider_meta( $post_id, 'meta_lgx_lsp_shortcodes', $postData['nonce'] ) ) {

                // RB-4 (B51): seed from the previously-stored value, mirroring
                // save_post_metadata_of_logosliderwp()'s pattern above. $existing_meta
                // is also the retain-on-reject source for the enforcement pass below.
                $existing_meta = get_post_meta( $post_id, '_lgx_lsp_shortcodes_meta', true );
                $existing_meta = is_array( $existing_meta ) ? $existing_meta : array();
                $savable_Data  = $existing_meta;

                $savable_Data['lgx_lswp_showcase_type']             = sanitize_text_field( $postData['lgx_lswp_showcase_type'] );
                // Content source (WP1.4): a submitted registered id (logo_library,
                // woo_product_brand) is stored; an empty or whitespace submit keeps the
                // stored value (logo_library when nothing usable is stored); an absent,
                // non-string or unregistered value keeps the stored value exactly, even an
                // unknown one (ADR-019, ADR-021). No lock applies: the Brand source is Free.
                $savable_Data['lgx_lswp_content_source'] = $this->lgx_resolve_source_value( $postData, $existing_meta );

                // WooCommerce Brand settings (WP1.3): options and defaults come from the
                // registry only. A submitted registered option is stored; an absent or
                // unregistered value keeps the stored value, else the registry default.
                // term_ids is normalised when submitted and otherwise kept as stored.
                $brand_select_fields = array(
                    'lgx_woo_product_brand_sort_order_by' => array( 'id' => 'lgx_woo_product_brand_sort_order_by' ),
                    'lgx_woo_product_brand_no_logo'       => array( 'id' => 'lgx_woo_product_brand_no_logo' ),
                    'lgx_woo_product_brand_empty'         => array( 'id' => 'lgx_woo_product_brand_empty' ),
                    'lgx_woo_product_brand_scope'         => array( 'id' => 'lgx_woo_product_brand_scope' ),
                );
                foreach ( $this->lgx_get_showcase_builder_registry_fields() as $registry_field ) {
                    if ( isset( $registry_field['id'] ) && isset( $brand_select_fields[ $registry_field['id'] ] ) ) {
                        $brand_select_fields[ $registry_field['id'] ] = $registry_field;
                    }
                }

                $savable_Data['lgx_woo_product_brand_sort_order_by'] = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_sort_order_by'], $postData, $existing_meta );
                $savable_Data['lgx_woo_product_brand_no_logo']       = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_no_logo'], $postData, $existing_meta );
                $savable_Data['lgx_woo_product_brand_empty']         = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_empty'], $postData, $existing_meta );
                $savable_Data['lgx_woo_product_brand_scope']         = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_scope'], $postData, $existing_meta );

                if ( isset( $postData['lgx_woo_product_brand_term_ids'] ) ) {
                    $savable_Data['lgx_woo_product_brand_term_ids'] = lgx_ls_normalise_term_ids( $postData['lgx_woo_product_brand_term_ids'] );
                } elseif ( array_key_exists( 'lgx_woo_product_brand_term_ids', $existing_meta ) ) {
                    $savable_Data['lgx_woo_product_brand_term_ids'] = $existing_meta['lgx_woo_product_brand_term_ids'];
                } else {
                    $savable_Data['lgx_woo_product_brand_term_ids'] = '';
                }

                // Basic Settings : ok
                $savable_Data['lgx_brand_name_en']      = (( isset($postData['lgx_brand_name_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_brand_desc_en']      = (( isset($postData['lgx_brand_desc_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_company_url_en']     = (( isset($postData['lgx_company_url_en'])) ? 'yes' : 'no');
               $savable_Data['lgx_preloader_en']       = (( isset($postData['lgx_preloader_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_target_type']        = (( isset($postData['lgx_target_type'])) ? sanitize_text_field(  $postData['lgx_target_type']) : '_self');
                $savable_Data['lgx_nofollow_en']        = (( isset($postData['lgx_nofollow_en'])) ? 'yes' : 'no');
               $savable_Data['lgx_preloader_bg_color'] = (( isset($postData['lgx_preloader_bg_color'])) ? sanitize_text_field( $postData['lgx_preloader_bg_color'])  : '#ffffff');
                $savable_Data['lgx_preloader_icon']     = (( isset($postData['lgx_preloader_icon'])) ? sanitize_text_field( $postData['lgx_preloader_icon'])  : '');
                $savable_Data['lgx_from_category']      = (( isset($postData['lgx_from_category'])) ? sanitize_text_field( $postData['lgx_from_category']) : 'all');
                $savable_Data['lgx_item_limit']         = (( isset($postData['lgx_item_limit'])) ? sanitize_text_field( $postData['lgx_item_limit']) : -1);
                $savable_Data['lgx_item_sort_order']    = (( isset($postData['lgx_item_sort_order'])) ? sanitize_text_field( $postData['lgx_item_sort_order']) : 'ASC');
                $savable_Data['lgx_item_sort_order_by'] = (( isset($postData['lgx_item_sort_order_by'])) ? sanitize_text_field( $postData['lgx_item_sort_order_by']) :'menu_order');
                // Update date : 29.06.2026, By : @VibeLogic
                // Height/Width merge: fallback corrected 'auto' -> 'none' to
                // match config.php's corrected default_text (max-height does
                // not accept 'auto' -- see the live cascade-verification
                // report). This hardcoded fallback only ever fires if a save
                // request somehow omits the field, which the merged UI's
                // form never does in normal use.
                $savable_Data['lgx_logo_height']                 = (( isset($postData['lgx_logo_height'])) ? sanitize_text_field( $postData['lgx_logo_height'] ) : 'none');
                $savable_Data['lgx_logo_height_property']        = (( isset($postData['lgx_logo_height_property'])) ? sanitize_text_field( $postData['lgx_logo_height_property']) : 'max-height');
                $savable_Data['lgx_logo_width']             = (( isset($postData['lgx_logo_width'])) ? sanitize_text_field( $postData['lgx_logo_width'] ) : '100%');
                $savable_Data['lgx_logo_width_property']         = (( isset($postData['lgx_logo_width_property'])) ? sanitize_text_field( $postData['lgx_logo_width_property'])  : 'max-width');
                // lgx_logo_max_height / lgx_logo_max_width removed entirely
                // before 6.0.0 shipped -- introduced mid-development
                // (5427caf), never present in any real shipped release
                // (confirmed against the actual last-shipped v5.5.5 package),
                // so no data to preserve. See docs/architecture/invariants.md
                // #6 and the Height/Width task's ADR entry.
                $savable_Data['lgx_logo_position']         = (( isset($postData['lgx_logo_position'])) ? sanitize_text_field( $postData['lgx_logo_position'])  : 'top');
                $savable_Data['lgx_logo_info_align']         = (( isset($postData['lgx_logo_info_align'])) ? sanitize_text_field( $postData['lgx_logo_info_align'])  : 'center');





                // Responsive Settings : ok
                $savable_Data['lgx_large_desktop_item']   =  (( isset($postData['lgx_large_desktop_item'])) ? sanitize_text_field( $postData['lgx_large_desktop_item'] ): 5);;
                $savable_Data['lgx_desktop_item']         =  (( isset($postData['lgx_desktop_item'])) ? sanitize_text_field( $postData['lgx_desktop_item'] ) : 4);
                $savable_Data['lgx_tablet_item']          =  (( isset($postData['lgx_tablet_item'])) ? sanitize_text_field( $postData['lgx_tablet_item'] ) : 3);
                $savable_Data['lgx_mobile_item']          =  (( isset($postData['lgx_mobile_item'])) ? sanitize_text_field( $postData['lgx_mobile_item'] ) : 2);


                // Style Settings
                $savable_Data['lgx_item_hover_effect']           = (( isset($postData['lgx_item_hover_effect'])) ? sanitize_text_field( $postData['lgx_item_hover_effect'] ) : 'none');
                $savable_Data['lgx_item_hover_anim']            = (( isset($postData['lgx_item_hover_anim'])) ? sanitize_text_field( $postData['lgx_item_hover_anim'] ) : 'default');
                $savable_Data['lgx_item_brand_name_color']      = (( isset($postData['lgx_item_brand_name_color'])) ? sanitize_text_field( $postData['lgx_item_brand_name_color'] ) : '#111111');;
                $savable_Data['lgx_item_brand_name_font_size']  = (( isset($postData['lgx_item_brand_name_font_size'])) ? sanitize_text_field( $postData['lgx_item_brand_name_font_size'] ) : '20px');
                $savable_Data['lgx_item_brand_name_font_weight']= (( isset($postData['lgx_item_brand_name_font_weight'])) ? sanitize_text_field( $postData['lgx_item_brand_name_font_weight'] ) : '600');

                $savable_Data['lgx_item_desc_color']            = (( isset($postData['lgx_item_desc_color'])) ? sanitize_text_field( $postData['lgx_item_desc_color'] ) : '#555555');
                $savable_Data['lgx_item_desc_font_size']        = (( isset($postData['lgx_item_desc_font_size'])) ? sanitize_text_field( $postData['lgx_item_desc_font_size'] ) : '20px');
                $savable_Data['lgx_item_desc_font_weight']      = (( isset($postData['lgx_item_desc_font_weight'])) ? sanitize_text_field( $postData['lgx_item_desc_font_weight'] ) : '400');

                $savable_Data['lgx_img_border_color_en']        = ((isset($postData['lgx_img_border_color_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_img_border_color']           = (( isset($postData['lgx_img_border_color'])) ? sanitize_text_field( $postData['lgx_img_border_color'] ) : '#FF5151');
                $savable_Data['lgx_img_border_color_hover']     = (( isset($postData['lgx_img_border_color_hover'])) ? sanitize_text_field( $postData['lgx_img_border_color_hover'] ) : '#FF9B6A');
                $savable_Data['lgx_img_border_width']           = (( isset($postData['lgx_img_border_width'])) ? sanitize_text_field( $postData['lgx_img_border_width'] ) : '1px');
                $savable_Data['lgx_img_border_radius']          = (( isset($postData['lgx_img_border_radius'])) ? sanitize_text_field( $postData['lgx_img_border_radius'] ) : '4px');

                $savable_Data['lgx_border_color_en']            = ((isset($postData['lgx_border_color_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_item_border_color']          = (( isset($postData['lgx_item_border_color'])) ? sanitize_text_field( $postData['lgx_item_border_color'] ) : '#161E54');
                $savable_Data['lgx_item_border_color_hover']    = (( isset($postData['lgx_item_border_color_hover'])) ? sanitize_text_field( $postData['lgx_item_border_color_hover'] ) : '#161E54');
                $savable_Data['lgx_item_border_width']          = (( isset($postData['lgx_item_border_width'])) ? sanitize_text_field( $postData['lgx_item_border_width'] ) : '4px');
                $savable_Data['lgx_item_border_radius']         = (( isset($postData['lgx_item_border_radius'])) ? sanitize_text_field( $postData['lgx_item_border_radius'] ) : '100');

                $savable_Data['lgx_item_bg_color_en']           = ((isset($postData['lgx_item_bg_color_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_item_bg_color']              = (( isset($postData['lgx_item_bg_color'])) ? sanitize_text_field( $postData['lgx_item_bg_color'] ) : '#f1f1f1');
                $savable_Data['lgx_item_bg_color_hover']        = (( isset($postData['lgx_item_bg_color_hover'])) ? sanitize_text_field( $postData['lgx_item_bg_color_hover'] ) : '#f1f1f1');

                $savable_Data['lgx_item_padding']               = (( isset($postData['lgx_item_padding'])) ? sanitize_text_field( $postData['lgx_item_padding'] ) : '0px');
                $savable_Data['lgx_item_margin']                =(( isset($postData['lgx_item_margin'])) ? sanitize_text_field( $postData['lgx_item_margin'] ) : '0px');

                $savable_Data['lgx_item_top_margin_title']      =(( isset($postData['lgx_item_top_margin_title'])) ? sanitize_text_field( $postData['lgx_item_top_margin_title'] ) : '5px');
                $savable_Data['lgx_item_bottom_margin_title']   =(( isset($postData['lgx_item_bottom_margin_title'])) ? sanitize_text_field( $postData['lgx_item_bottom_margin_title'] ) : '5px');

                $savable_Data['lgx_item_top_margin_desc']       =(( isset($postData['lgx_item_top_margin_desc'])) ? sanitize_text_field( $postData['lgx_item_top_margin_desc'] ) : '0px');
                $savable_Data['lgx_item_bottom_margin_desc']    =(( isset($postData['lgx_item_bottom_margin_desc'])) ? sanitize_text_field( $postData['lgx_item_bottom_margin_desc'] ) : '0px');


                // Tooltip Settings : Ok
                $savable_Data['lgx_tooltip_en']                  = ((isset($postData['lgx_tooltip_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_tooltip_content_type']        = (( isset($postData['lgx_tooltip_content_type'])) ? sanitize_text_field( $postData['lgx_tooltip_content_type'] ) : 'brand_name');
                $savable_Data['lgx_tooltip_position']            = (( isset($postData['lgx_tooltip_position'])) ? sanitize_text_field( $postData['lgx_tooltip_position'] ) : 'top');
                $savable_Data['lgx_tooltip_anim']                = (( isset($postData['lgx_tooltip_anim'])) ? sanitize_text_field( $postData['lgx_tooltip_anim'] ) : 'fade');
                $savable_Data['lgx_tooltip_arrow_en']            = ((isset($postData['lgx_tooltip_arrow_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_tooltip_anim_duration']       = (( isset($postData['lgx_tooltip_anim_duration'])) ? sanitize_text_field( $postData['lgx_tooltip_anim_duration'] ) : 350);
                $savable_Data['lgx_tooltip_anim_delay']          = (( isset($postData['lgx_tooltip_anim_delay'])) ? sanitize_text_field( $postData['lgx_tooltip_anim_delay'] ) : 300);
                $savable_Data['lgx_tooltip_trigger_type']        = (( isset($postData['lgx_tooltip_trigger_type'])) ? sanitize_text_field( $postData['lgx_tooltip_trigger_type'] ) : 'hover');
                $savable_Data['lgx_tooltip_distance']            =(( isset($postData['lgx_tooltip_distance'])) ? sanitize_text_field( $postData['lgx_tooltip_distance'] ) : 6);;
                $savable_Data['lgx_tooltip_min_intersection']    = (( isset($postData['lgx_tooltip_min_intersection'])) ? sanitize_text_field( $postData['lgx_tooltip_min_intersection'] ) : 16);
                $savable_Data['lgx_tooltip_timer']               = (( isset($postData['lgx_tooltip_timer'])) ? sanitize_text_field( $postData['lgx_tooltip_timer'] ) : 0);
                $savable_Data['lgx_tooltip_padding']              = (( isset($postData['lgx_tooltip_padding'])) ? sanitize_text_field( $postData['lgx_tooltip_padding'] ) : '8px');
                $savable_Data['lgx_tooltip_text_color']          = (( isset($postData['lgx_tooltip_text_color'])) ? sanitize_text_field( $postData['lgx_tooltip_text_color'] ) : '#ffffff');
                $savable_Data['lgx_tooltip_bg_color']            = (( isset($postData['lgx_tooltip_bg_color'])) ? sanitize_text_field( $postData['lgx_tooltip_bg_color'] ) : '#d3d3d3');
                $savable_Data['lgx_tooltip_border_color']        = (( isset($postData['lgx_tooltip_border_color'])) ? sanitize_text_field( $postData['lgx_tooltip_border_color'] ) : '#333333');
                $savable_Data['lgx_tooltip_border_radius']       = (( isset($postData['lgx_tooltip_border_radius'])) ? sanitize_text_field( $postData['lgx_tooltip_border_radius'] ) : '4px');
                $savable_Data['lgx_tooltip_border_width']       = (( isset($postData['lgx_tooltip_border_width'])) ? sanitize_text_field( $postData['lgx_tooltip_border_width'] ) : '2px');
                $savable_Data['lgx_tooltip_arrow_bg_color']      = (( isset($postData['lgx_tooltip_arrow_bg_color'])) ? sanitize_text_field( $postData['lgx_tooltip_arrow_bg_color'] ) : '#555555');
                $savable_Data['lgx_tooltip_arrow_border_color']  = (( isset($postData['lgx_tooltip_arrow_border_color'])) ? sanitize_text_field( $postData['lgx_tooltip_arrow_border_color'] ) : '#333333');

                $savable_Data['lgx_tooltip_min_width']    = (( isset($postData['lgx_tooltip_min_width'])) ? sanitize_text_field( $postData['lgx_tooltip_min_width'] ) : 0);
                $savable_Data['lgx_tooltip_max_width']    = (( isset($postData['lgx_tooltip_max_width'])) ? sanitize_text_field( $postData['lgx_tooltip_max_width'] ) : '');


                //Section Settings  : ok
                $savable_Data['lgx_section_bg_img_en']          = ((isset($postData['lgx_section_bg_img_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_section_bg_color_en']        = ((isset($postData['lgx_section_bg_color_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_section_width']              = (( isset($postData['lgx_section_width'])) ? sanitize_text_field( $postData['lgx_section_width'] ) : '100%');
                $savable_Data['lgx_section_container']          = (( isset($postData['lgx_section_container'])) ? sanitize_text_field( $postData['lgx_section_container'] ) : 'container-fluid');
                $savable_Data['lgx_section_bg_img']             = (( isset($postData['lgx_section_bg_img'])) ? sanitize_text_field( $postData['lgx_section_bg_img'] ) : '');
                $savable_Data['lgx_section_bg_img_attachment']  = (( isset($postData['lgx_section_bg_img_attachment'])) ? sanitize_text_field( $postData['lgx_section_bg_img_attachment'] ) : 'initial');
                $savable_Data['lgx_section_bg_img_size']        = (( isset($postData['lgx_section_bg_img_size'])) ? sanitize_text_field( $postData['lgx_section_bg_img_size'] ) : 'cover');
                $savable_Data['lgx_section_bg_color']           = (( isset($postData['lgx_section_bg_color'])) ? sanitize_text_field( $postData['lgx_section_bg_color'] ) : '#b56969');
                $savable_Data['lgx_section_top_margin']         = (( isset($postData['lgx_section_top_margin'])) ? sanitize_text_field( $postData['lgx_section_top_margin'] ) : '0px');
                $savable_Data['lgx_section_bottom_margin']      = (( isset($postData['lgx_section_bottom_margin'])) ? sanitize_text_field( $postData['lgx_section_bottom_margin'] ) : '0px');
                $savable_Data['lgx_section_top_padding']        = (( isset($postData['lgx_section_top_padding'])) ? sanitize_text_field( $postData['lgx_section_top_padding'] ) : '0px');
                $savable_Data['lgx_section_bottom_padding']     = (( isset($postData['lgx_section_bottom_padding'])) ? sanitize_text_field( $postData['lgx_section_bottom_padding'] ) : '0px');

                //Header Settings : Ok
                $savable_Data['lgx_header_en']                             = ((isset($postData['lgx_header_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_header_align']                         = (( isset($postData['lgx_header_align'])) ? sanitize_text_field( $postData['lgx_header_align'] ): 'center');
                $savable_Data['lgx_header_title']                         = (( isset($postData['lgx_header_title'])) ? sanitize_text_field( $postData['lgx_header_title'] ) : '');
                $savable_Data['lgx_header_title_font_size']               = (( isset($postData['lgx_header_title_font_size'])) ? sanitize_text_field( $postData['lgx_header_title_font_size'] ): '42px');
                $savable_Data['lgx_header_title_color']                   = (( isset($postData['lgx_header_title_color'])) ? sanitize_text_field( $postData['lgx_header_title_color'] ): '#2e2841cc');
                $savable_Data['lgx_header_title_font_weight']             = (( isset($postData['lgx_header_title_font_weight'])) ? sanitize_text_field( $postData['lgx_header_title_font_weight'] ): '500');
                $savable_Data['lgx_header_title_bottom_margin']           = (( isset($postData['lgx_header_title_bottom_margin'])) ? sanitize_text_field( $postData['lgx_header_title_bottom_margin'] ): '10px');
                $savable_Data['lgx_header_subtitle']                      = (( isset($postData['lgx_header_subtitle'])) ? sanitize_text_field( $postData['lgx_header_subtitle'] ) : '');
                $savable_Data['lgx_header_subtitle_font_size']            = (( isset($postData['lgx_header_subtitle_font_size'])) ? sanitize_text_field( $postData['lgx_header_subtitle_font_size'] ): '16px');
                $savable_Data['lgx_header_subtitle_color']                = (( isset($postData['lgx_header_subtitle_color'])) ? sanitize_text_field( $postData['lgx_header_subtitle_color'] ): '#888888');
                $savable_Data['lgx_header_subtitle_font_weight']          = (( isset($postData['lgx_header_subtitle_font_weight'])) ? sanitize_text_field( $postData['lgx_header_subtitle_font_weight'] ): '400');
                $savable_Data['lgx_header_subtitle_bottom_margin']        = (( isset($postData['lgx_header_subtitle_bottom_margin'])) ? sanitize_text_field( $postData['lgx_header_subtitle_bottom_margin'] ): '35px');


                //Grid : OK
                $savable_Data['lgx_grid_column_gap']            = (( isset($postData['lgx_grid_column_gap'])) ? sanitize_text_field( $postData['lgx_grid_column_gap'] ) : '15px');
                $savable_Data['lgx_grid_row_gap']               = (( isset($postData['lgx_grid_row_gap'])) ? sanitize_text_field( $postData['lgx_grid_row_gap'] ) : '15px');
                $savable_Data['lgx_grid_item_min_height']       = (( isset($postData['lgx_grid_item_min_height'])) ? sanitize_text_field( $postData['lgx_grid_item_min_height'] ) : 0);
                $savable_Data['lgx_grid_image_vertical_align']  = (( isset($postData['lgx_grid_image_vertical_align'])) ? sanitize_text_field( $postData['lgx_grid_image_vertical_align'] ) : 'center');
                $savable_Data['lgx_grid_style']                 = (( isset($postData['lgx_grid_style'])) ? sanitize_text_field( $postData['lgx_grid_style'] ) : 'simple');
                $savable_Data['lgx_grid_outline_color']         = (( isset($postData['lgx_grid_outline_color'])) ? sanitize_text_field( $postData['lgx_grid_outline_color'] ) : '#e9e9e9');


                //Flex Box
                $savable_Data['lgx_flexbox_column_gap']            = (( isset($postData['lgx_flexbox_column_gap'])) ? sanitize_text_field( $postData['lgx_flexbox_column_gap'] ) : '15px');
                $savable_Data['lgx_flexbox_row_gap']            = (( isset($postData['lgx_flexbox_row_gap'])) ? sanitize_text_field( $postData['lgx_flexbox_row_gap'] ) : '15px');
                $savable_Data['lgx_flexbox_align_items']        = (( isset($postData['lgx_flexbox_align_items'])) ? sanitize_text_field( $postData['lgx_flexbox_align_items'] ) : 'flex-start');
                $savable_Data['lgx_flexbox_justify_content']    = (( isset($postData['lgx_flexbox_justify_content'])) ? sanitize_text_field( $postData['lgx_flexbox_justify_content'] ) : 'flex-start');
                $savable_Data['lgx_flexbox_wrap']               = (( isset($postData['lgx_flexbox_wrap'])) ? sanitize_text_field( $postData['lgx_flexbox_wrap'] ) : 'wrap');
                $savable_Data['lgx_flexbox_direction']               = (( isset($postData['lgx_flexbox_direction'])) ? sanitize_text_field( $postData['lgx_flexbox_direction'] ) : 'row');


                //Carousel Control Settings :ok
                $savable_Data['lgx_carousel_ticker_en']              = ((isset($postData['lgx_carousel_ticker_en'])) ? 'yes' : 'no');
                // Task 014 (B38): found during implementation that this handler is a
                // hand-written per-field allowlist, not driven by config.php's
                // registry — a new config.php field alone is silently dropped on
                // every save without an explicit line here.
                $savable_Data['lgx_carousel_ticker_pause_control_en'] = ((isset($postData['lgx_carousel_ticker_pause_control_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_transition_effect']      = (( isset($postData['lgx_carousel_transition_effect'])) ? sanitize_text_field( $postData['lgx_carousel_transition_effect'] ): 'all');
                $savable_Data['lgx_carousel_infinite_en']            = ((isset($postData['lgx_carousel_infinite_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_carousel_transition_speed']       = (( isset($postData['lgx_carousel_transition_speed'])) ? intval( $postData['lgx_carousel_transition_speed'] ): 450);
                $savable_Data['lgx_carousel_autoplay_en']            = ((isset($postData['lgx_carousel_autoplay_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_autoplay_delay']         = (( isset($postData['lgx_carousel_autoplay_delay'])) ? sanitize_text_field( $postData['lgx_carousel_autoplay_delay'] ): 1500);
                $savable_Data['lgx_carousel_pause_mouse_enter_en']   = ((isset($postData['lgx_carousel_pause_mouse_enter_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_space_between']          = (( isset($postData['lgx_carousel_space_between'])) ? sanitize_text_field( $postData['lgx_carousel_space_between'] ): 10);
                $savable_Data['lgx_carousel_rtl_en']                 = ((isset($postData['lgx_carousel_rtl_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_carousel_nav_en']                = ((isset($postData['lgx_carousel_nav_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_nav_border_en']         = ((isset($postData['lgx_carousel_nav_border_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_nav_hover_en']          = ((isset($postData['lgx_carousel_nav_hover_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_carousel_nav_position']          = (( isset($postData['lgx_carousel_nav_position'])) ? sanitize_text_field( $postData['lgx_carousel_nav_position'] ): 'top_right');
                $savable_Data['lgx_carousel_nav_ver_pos_bottom']    = (( isset($postData['lgx_carousel_nav_ver_pos_bottom'])) ? sanitize_text_field( $postData['lgx_carousel_nav_ver_pos_bottom'] ): '50%');
                $savable_Data['lgx_carousel_nav_ver_pos_left_right']    = (( isset($postData['lgx_carousel_nav_ver_pos_left_right'])) ? sanitize_text_field( $postData['lgx_carousel_nav_ver_pos_left_right'] ): '-55px');


                $savable_Data['lgx_carousel_nav_color']             = (( isset($postData['lgx_carousel_nav_color'])) ? sanitize_text_field( $postData['lgx_carousel_nav_color'] ): '#ffffff');
                $savable_Data['lgx_carousel_nav_color_hover']       = (( isset($postData['lgx_carousel_nav_color_hover'])) ? sanitize_text_field( $postData['lgx_carousel_nav_color_hover'] ): '#ffffff');
                $savable_Data['lgx_carousel_nav_bg_color']          = (( isset($postData['lgx_carousel_nav_bg_color'])) ? sanitize_text_field( $postData['lgx_carousel_nav_bg_color'] ): '#222b30');
                $savable_Data['lgx_carousel_nav_bg_color_hover']    = (( isset($postData['lgx_carousel_nav_bg_color_hover'])) ? sanitize_text_field( $postData['lgx_carousel_nav_bg_color_hover'] ): '#222b30');

                $savable_Data['lgx_carousel_nav_border_color']          = (( isset($postData['lgx_carousel_nav_border_color'])) ? sanitize_text_field( $postData['lgx_carousel_nav_border_color'] ): '#161E54');
                $savable_Data['lgx_carousel_nav_border_color_hover']    = (( isset($postData['lgx_carousel_nav_border_color_hover'])) ? sanitize_text_field( $postData['lgx_carousel_nav_border_color_hover'] ): '#88E0EF');
                $savable_Data['lgx_carousel_nav_border_width']          = (( isset($postData['lgx_carousel_nav_border_width'])) ? sanitize_text_field( $postData['lgx_carousel_nav_border_width'] ): '1px');
                $savable_Data['lgx_carousel_nav_border_radius']         = (( isset($postData['lgx_carousel_nav_border_radius'])) ? sanitize_text_field( $postData['lgx_carousel_nav_border_radius'] ): '4px');
                $savable_Data['lgx_carousel_nav_border_style']          = (( isset($postData['lgx_carousel_nav_border_style'])) ? sanitize_text_field( $postData['lgx_carousel_nav_border_style'] ): 'solid');

                $savable_Data['lgx_carousel_pagination_en']           = ((isset($postData['lgx_carousel_pagination_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_pagination_color']        = (( isset($postData['lgx_carousel_pagination_color'])) ? sanitize_text_field( $postData['lgx_carousel_pagination_color'] ): '#869791');
                $savable_Data['lgx_carousel_pagination_color_active'] = (( isset($postData['lgx_carousel_pagination_color_active'])) ? sanitize_text_field( $postData['lgx_carousel_pagination_color_active'] ): '#222b30');

                $savable_Data['lgx_carousel_auto_height_en']          = ((isset($postData['lgx_carousel_auto_height_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_allow_touch_move_en']     = ((isset($postData['lgx_carousel_allow_touch_move_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_mouse_wheel_en']          = ((isset($postData['lgx_carousel_mouse_wheel_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_simulate_touch_en']       = ((isset($postData['lgx_carousel_simulate_touch_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_grab_cursor_en']         = ((isset($postData['lgx_carousel_grab_cursor_en'])) ? 'yes' : 'no');

                $savable_Data['lgx_carousel_dynamic_bullets_en']       = ((isset($postData['lgx_carousel_dynamic_bullets_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_dynamic_bullets_no']       = (( isset($postData['lgx_carousel_dynamic_bullets_no'])) ? sanitize_text_field( $postData['lgx_carousel_dynamic_bullets_no'] ): 1);

                $savable_Data['lgx_carousel_nav_btn_type']            = (( isset($postData['lgx_carousel_nav_btn_type'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_type'] ): 'icon');
                $savable_Data['lgx_carousel_nav_btn_text_prev']       = (( isset($postData['lgx_carousel_nav_btn_text_prev'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_text_prev'] ): 'Prev');
                $savable_Data['lgx_carousel_nav_btn_text_next']       = (( isset($postData['lgx_carousel_nav_btn_text_next'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_text_next'] ): 'Next');

                $savable_Data['lgx_carousel_nav_btn_margin']        = (( isset($postData['lgx_carousel_nav_btn_margin'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_margin'] ): '5px 0px');//v3.2.7
                $savable_Data['lgx_carousel_nav_btn_padding']        = (( isset($postData['lgx_carousel_nav_btn_padding'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_padding'] ): '0px');
                $savable_Data['lgx_carousel_nav_btn_font_size']      = (( isset($postData['lgx_carousel_nav_btn_font_size'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_font_size'] ): '22px');
               // Removed from v3.2.0 $savable_Data['lgx_carousel_nav_btn_line_height']    = (( isset($postData['lgx_carousel_nav_btn_line_height'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_line_height'] ): '24px');
                $savable_Data['lgx_carousel_nav_btn_width']          = (( isset($postData['lgx_carousel_nav_btn_width'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_width'] ): '30px');
                $savable_Data['lgx_carousel_nav_btn_height']         = (( isset($postData['lgx_carousel_nav_btn_height'])) ? sanitize_text_field( $postData['lgx_carousel_nav_btn_height'] ): '30px');
                $savable_Data['lgx_carousel_item_vertical_align']    = (( isset($postData['lgx_carousel_item_vertical_align'])) ? sanitize_text_field( $postData['lgx_carousel_item_vertical_align'] ): 'mid');
                $savable_Data['lgx_carousel_nav_icon']               = (( isset($postData['lgx_carousel_nav_icon'])) ? sanitize_text_field( $postData['lgx_carousel_nav_icon'] ): 'angle');
                $savable_Data['lgx_carousel_lazy_load_en']           = ((isset($postData['lgx_carousel_lazy_load_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_lazy_spinner_color']     = (( isset($postData['lgx_carousel_lazy_spinner_color'])) ? sanitize_text_field( $postData['lgx_carousel_lazy_spinner_color'] ): 'white');
                $savable_Data['lgx_carousel_pagi_mobile_en']         = ((isset($postData['lgx_carousel_pagi_mobile_en'])) ? 'yes' : 'no');
                $savable_Data['lgx_carousel_nav_mobile_en']          = ((isset($postData['lgx_carousel_nav_mobile_en'])) ? 'yes' : 'no');


                // RB-4 (B51): single validation pass between building $savable_Data
                // and writing it (ADR-011 §5). Rejects a locked field/option
                // submitted from an untrusted Free-mode POST and retains the
                // previously-stored value instead (Invariant 9). `false` means
                // the enforcement mechanism itself is unavailable — refuse the
                // save rather than write anything unenforced.
                $enforced_Data = $this->lgx_enforce_pro_lock_retain_on_reject( $savable_Data, $existing_meta, $postData );

                if ( false === $enforced_Data ) {
                    return;
                }

                $savable_Data = $enforced_Data;

                // echo '<pre>';  print_r($savable_Data); echo '</pre>'; wp_die();
                update_post_meta( $post_id, '_lgx_lsp_shortcodes_meta', $savable_Data );
            }
        }
    }// End  Meta Save

    /**
     * T-216 (Stage 2, S-30): what a save would store for the content source and
     * the four WooCommerce Brand selects and the Brand term ids, for the live
     * preview (filter lgx_ls_preview_clean, see handle_preview_render_ajax()).
     *
     * Applies the save's own rules in the save's order: the source through
     * lgx_resolve_source_value(), the Brand selects through the same registry
     * lookup and lgx_resolve_registry_select_value(), the term ids with the
     * save's inline rule, then the save's Pro lock pass
     * lgx_enforce_pro_lock_retain_on_reject() on these keys only. That pass
     * skips every registry field that is not in the array it gets and decides
     * each key from that key's value, the stored data and the submitted data
     * alone, so each key comes out as in a full save.
     *
     * Reads no post, writes nothing, checks no nonce or capability (the
     * preview handler has done that).
     *
     * @since 6.1.0
     *
     * @param mixed $clean         The filter's default value (an empty array); not used.
     * @param mixed $postData      The submitted data, already unslashed (the save's $postData).
     * @param mixed $existing_meta The stored data.
     * @return array The six keys with the values a save would store, or an empty
     *               array when the lock class is not available (the save refuses then).
     */
    public function lgx_preview_clean_source_and_brand( $clean, $postData, $existing_meta ) {
        if ( ! class_exists( 'Lgx_Pro_Locking' ) ) {
            return array();
        }

        $postData      = is_array( $postData ) ? $postData : array();
        $existing_meta = is_array( $existing_meta ) ? $existing_meta : array();

        $result = array();

        $result['lgx_lswp_content_source'] = $this->lgx_resolve_source_value( $postData, $existing_meta );

        $brand_select_fields = array(
            'lgx_woo_product_brand_sort_order_by' => array( 'id' => 'lgx_woo_product_brand_sort_order_by' ),
            'lgx_woo_product_brand_no_logo'       => array( 'id' => 'lgx_woo_product_brand_no_logo' ),
            'lgx_woo_product_brand_empty'         => array( 'id' => 'lgx_woo_product_brand_empty' ),
            'lgx_woo_product_brand_scope'         => array( 'id' => 'lgx_woo_product_brand_scope' ),
        );
        foreach ( $this->lgx_get_showcase_builder_registry_fields() as $registry_field ) {
            if ( isset( $registry_field['id'] ) && isset( $brand_select_fields[ $registry_field['id'] ] ) ) {
                $brand_select_fields[ $registry_field['id'] ] = $registry_field;
            }
        }

        $result['lgx_woo_product_brand_sort_order_by'] = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_sort_order_by'], $postData, $existing_meta );
        $result['lgx_woo_product_brand_no_logo']       = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_no_logo'], $postData, $existing_meta );
        $result['lgx_woo_product_brand_empty']         = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_empty'], $postData, $existing_meta );
        $result['lgx_woo_product_brand_scope']         = $this->lgx_resolve_registry_select_value( $brand_select_fields['lgx_woo_product_brand_scope'], $postData, $existing_meta );

        if ( isset( $postData['lgx_woo_product_brand_term_ids'] ) ) {
            $result['lgx_woo_product_brand_term_ids'] = lgx_ls_normalise_term_ids( $postData['lgx_woo_product_brand_term_ids'] );
        } elseif ( array_key_exists( 'lgx_woo_product_brand_term_ids', $existing_meta ) ) {
            $result['lgx_woo_product_brand_term_ids'] = $existing_meta['lgx_woo_product_brand_term_ids'];
        } else {
            $result['lgx_woo_product_brand_term_ids'] = '';
        }

        $enforced = $this->lgx_enforce_pro_lock_retain_on_reject( $result, $existing_meta, $postData );

        return is_array( $enforced ) ? $enforced : array();
    }

    /**
     * Resolves the value to store for the content source key (lgx_lswp_content_source).
     *
     * 1. A submitted string identical to a registered id is stored.
     * 2. A submitted empty or whitespace string keeps the stored value; when nothing
     *    usable is stored (no key, null, empty or whitespace) it writes logo_library.
     * 3. Anything else (key absent or null, not a string, not a registered id) keeps the
     *    stored value exactly as stored, even an unknown one; logo_library only when no
     *    key is stored.
     *
     * Whether an id is registered comes from lgx_ls_is_registered_source().
     *
     * @since 6.1.0
     *
     * @param array $postData      The submitted data, already unslashed.
     * @param array $existing_meta The stored data.
     * @return mixed
     */
    private function lgx_resolve_source_value( $postData, $existing_meta ) {
        $key        = 'lgx_lswp_content_source';
        $has_stored = array_key_exists( $key, $existing_meta );
        $stored     = $has_stored ? $existing_meta[ $key ] : null;
        $submitted  = isset( $postData[ $key ] ) ? $postData[ $key ] : null;

        if ( is_string( $submitted ) && lgx_ls_is_registered_source( $submitted ) ) {
            return $submitted;
        }

        if ( is_string( $submitted ) && '' === trim( $submitted ) ) {
            $nothing_usable = ! $has_stored || null === $stored || ( is_string( $stored ) && '' === trim( $stored ) );
            return $nothing_usable ? 'logo_library' : $stored;
        }

        return $has_stored ? $stored : 'logo_library';
    }

    /**
     * Resolves the value to store for one registry select field.
     *
     * 1. A submitted string that is identical to one of the registry options
     *    is the value to store.
     * 2. Otherwise the stored value is kept exactly as stored, even when it
     *    is not a registry option.
     * 3. Otherwise the registry default is used (an empty string when the
     *    field has none).
     *
     * @since 6.1.0
     *
     * @param array $field         The registry field (id, options, default).
     * @param array $postData      The submitted data, already unslashed.
     * @param array $existing_meta The stored data.
     * @return mixed
     */
    private function lgx_resolve_registry_select_value( $field, $postData, $existing_meta ) {
        $id         = isset( $field['id'] ) ? $field['id'] : '';
        $registered = ( isset( $field['options'] ) && is_array( $field['options'] ) )
            ? array_map( 'strval', array_keys( $field['options'] ) )
            : array();

        if ( array_key_exists( $id, $postData ) && is_string( $postData[ $id ] ) && in_array( $postData[ $id ], $registered, true ) ) {
            return $postData[ $id ];
        }

        if ( array_key_exists( $id, $existing_meta ) ) {
            return $existing_meta[ $id ];
        }

        return isset( $field['default'] ) ? $field['default'] : '';
    }

    /**
     * RB-4 (B51): reject a Pro-locked field or option arriving from a
     * Free-mode POST and retain the previously-stored value instead of the
     * hardcoded default (ADR-011 §5-7, Invariant 9). Provenance-based
     * (Invariant 8): only ever changes a key about to be written by *this*
     * save; never strips or rewrites values already sitting in stored meta.
     *
     * Registry-driven and boundary-agnostic: walks whatever config.php
     * currently marks `status => pro` (or per-option via `free_options`),
     * so it requires no edit when B50 changes which fields that is.
     *
     * Fails loud, not open: if `Lgx_Pro_Locking` is unavailable, we cannot
     * determine which fields are locked, so this refuses the save entirely
     * (returns `false`) rather than write $savable_Data unenforced. The
     * caller must check for `false` and skip `update_post_meta()`.
     *
     * @param array $savable_Data  The freshly-built values about to be saved.
     * @param array $existing_meta The value stored before this save (empty array for a new showcase).
     * @param array $postData      The raw (wp_unslash()'d) submitted form data — needed to tell
     *                             "nothing was submitted for this key" apart from "the per-field
     *                             line's own hardcoded absent-default happens to be an allowed
     *                             value" (see the group2SelectText/select branch below).
     * @return array|false Enforced data, or false to signal "refuse this save".
     */
    private function lgx_enforce_pro_lock_retain_on_reject( array $savable_Data, array $existing_meta, array $postData ) {
        if ( ! class_exists( 'Lgx_Pro_Locking' ) ) {
            error_log( 'Logo Slider WP: Lgx_Pro_Locking is unavailable during save_post_metadata_of_lgx_lsp_shortcodes() — refusing to save rather than write Pro/Free values with no enforcement. Check that includes/class-logo-slider-wp-pro-locking.php is loaded.' );
            return false;
        }

        foreach ( $this->lgx_get_showcase_builder_registry_fields() as $field ) {
            $field_id = $field['id'];

            if ( ! array_key_exists( $field_id, $savable_Data ) ) {
                continue;
            }

            $status         = $field['status'] ?? '';
            $has_previous   = array_key_exists( $field_id, $existing_meta );
            $default_value  = $field['default'] ?? '';

            if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
                // Model A: the control itself stays usable; only a specific
                // locked option is rejected (ADR-011 §3) — never the whole field.
                //
                // Confirmed live (RB-4 real-WP verification): when this key is
                // absent from $postData, the per-field line above has already
                // substituted its own hardcoded default before this method ever
                // runs. If that default happens to be a member of the field's
                // own free_options, is_option_locked() correctly reports it as
                // unlocked -- because free_options exists to protect an
                // INTENTIONAL free choice, not to explain away an absent key --
                // and a real prior Pro value gets silently overwritten. So:
                // "nothing submitted" must never reach is_option_locked() at
                // all; it's handled the same value-independent way a whole-field
                // lock already is.
                $was_submitted = array_key_exists( $field_id, $postData );

                if ( ! $was_submitted ) {
                    if ( Lgx_Pro_Locking::is_field_locked( $field_id, $status ) ) {
                        $savable_Data[ $field_id ] = $has_previous ? $existing_meta[ $field_id ] : $default_value;
                    }

                    continue;
                }

                $exempt_value = ( $has_previous && array_key_exists( $existing_meta[ $field_id ], $field['options'] ) )
                    ? $existing_meta[ $field_id ]
                    : $default_value;

                $is_locked = Lgx_Pro_Locking::is_option_locked(
                    $field_id,
                    $savable_Data[ $field_id ],
                    $status,
                    $exempt_value,
                    false,
                    $field['free_options'] ?? array()
                );

                if ( $is_locked ) {
                    $savable_Data[ $field_id ] = $has_previous ? $existing_meta[ $field_id ] : $exempt_value;
                }

                continue;
            }

            // Whole-field lock (switch/text/color/number/upload/group2SelectText's
            // text half). The lock check must run regardless of $has_previous —
            // a new showcase with no prior value still falls back to the
            // registry default (ADR-011 §6), never the submitted value.
            if ( Lgx_Pro_Locking::is_field_locked( $field_id, $status ) ) {
                $savable_Data[ $field_id ] = $has_previous ? $existing_meta[ $field_id ] : $default_value;
            }
        }

        return $savable_Data;
    }

    /**
     * Flatten the Showcase Builder's config.php registry into individual
     * savable-field definitions, one per real meta key. `group2SelectText`
     * entries store two independent keys (a select and a text input) under
     * one config node and are expanded into two synthetic single-key
     * entries so every item this returns maps 1:1 onto a $savable_Data key.
     *
     * @return array[]
     */
    private function lgx_get_showcase_builder_registry_fields() {
        static $fields = null;

        if ( null !== $fields ) {
            return $fields;
        }

        $config_path = plugin_dir_path( __FILE__ ) . 'modules/showcase-builder/config/config.php';
        $registry    = file_exists( $config_path ) ? include $config_path : array();

        $fields = array();
        $this->lgx_flatten_registry_fields( $registry, $fields );

        return $fields;
    }

    /**
     * @param mixed $node
     * @param array $fields Accumulator, passed by reference.
     */
    private function lgx_flatten_registry_fields( $node, array &$fields ) {
        if ( ! is_array( $node ) ) {
            return;
        }

        if ( isset( $node['id'], $node['type'] ) ) {
            if ( 'group2SelectText' === $node['type'] ) {
                if ( isset( $node['id_select'] ) ) {
                    $fields[] = array(
                        'id'           => $node['id_select'],
                        'status'       => $node['status'] ?? '',
                        'options'      => $node['options_select'] ?? array(),
                        'default'      => $node['default_select'] ?? '',
                        // Height/Width merge: was hardcoded array() -- silently
                        // harmless while no group2SelectText field ever used
                        // free_options, but the Width field now does (Max
                        // Width stays Free while Width/Min Width stay Pro).
                        // Passing the node's own value through is required
                        // for the save-time Pro-lock enforcement pass to
                        // actually see the same exemption the render path
                        // (group2SelectText()) already honours.
                        'free_options' => $node['free_options'] ?? array(),
                    );
                }
                if ( isset( $node['id_text'] ) ) {
                    $fields[] = array(
                        'id'      => $node['id_text'],
                        'status'  => $node['status'] ?? '',
                        'default' => $node['default_text'] ?? '',
                    );
                }
            } else {
                $fields[] = $node;
            }

            return;
        }

        foreach ( $node as $child ) {
            if ( is_array( $child ) ) {
                $this->lgx_flatten_registry_fields( $child, $fields );
            }
        }
    }


    /**
     * @param array $classes
     * @return array|mixed
     */


    public function add_meta_box_css_class_for_lgx_lsp_shortcodes($classes = array()) {

        $add_classes = array( 'lgx_logo_slider_meta_box_postbox', 'lgx_logo_slider_meta_box_postbox_free' );

        foreach ( $add_classes as $class ) {
            if ( ! in_array( $class, $classes ) ) {
                $classes[] = sanitize_html_class( $class );
            }
        }

        return $classes;
    }



    public function add_new_column_head_for_lgx_logo_showcase($default_columns) {
        unset( $default_columns['date'] );

        $default_columns['title']            = __( 'Title', 'logo-slider-wp' );
        $default_columns['shortcode']        = __( 'Shortcode', 'logo-slider-wp' );
        $default_columns['layout']           = __( 'Layout', 'logo-slider-wp' );
        $default_columns['source']           = __( 'Source', 'logo-slider-wp' );
        $default_columns['date']             = __( 'Date', 'logo-slider-wp' );

        return $default_columns;
    }

    /**
     * Get a display label for a showcase layout value.
     */
    private function get_layout_label( $value ) {
        $labels = array(
            'carousel' => __( 'Carousel', 'logo-slider-wp' ),
            'grid'     => __( 'Grid', 'logo-slider-wp' ),
            'flexbox'  => __( 'Flexbox', 'logo-slider-wp' ),
        );
        return isset( $labels[ $value ] ) ? $labels[ $value ] : $value;
    }

    /**
     * Get a display label for a content source value.
     */
    private function get_source_label( $value ) {
        $described = lgx_ls_describe_source( $value );
        return $described['label'];
    }

    public function define_admin_column_value_for_lgx_logo_showcase($column, $post_id) {
        if(!empty($post_id)) {
            switch ($column) {
                case 'shortcode_id':
                    echo esc_html( $post_id );
                    break;

                case 'shortcode':
                    $shortcode = '[lgxlogoslider id="' . esc_attr( $post_id ) . '"]';
                    echo '<div class="lgx-copy-group" style="display: flex; gap: 5px; align-items: center; max-width: 250px;">';
                    echo '<input type="text" aria-label="' . esc_attr__( 'Shortcode', 'logo-slider-wp' ) . '" class="lgx-input lgx-copy-input lgx_logo_slider_list_copy_input selectable" readonly="readonly" value="' . esc_attr($shortcode) . '" style="flex-grow: 1;">';
                    echo '<button type="button" class="button lgx-btn lgx-btn-secondary lgx-copy-btn" data-clipboard-text="' . esc_attr($shortcode) . '" aria-label="' . esc_attr__( 'Copy shortcode to clipboard', 'logo-slider-wp' ) . '" title="' . esc_attr__( 'Copy', 'logo-slider-wp' ) . '"><i class="lgxicon lgx-icon-copy"></i></button>';
                    echo '</div>';
                    break;

                case 'layout':
                    $layout = get_post_meta( $post_id, '_lgx_lsp_shortcodes_meta', true );
                    $layout = is_array( $layout ) && isset( $layout['lgx_lswp_showcase_type'] ) ? $layout['lgx_lswp_showcase_type'] : 'carousel';
                    echo esc_html( $this->get_layout_label( $layout ) );
                    break;

                case 'source':
                    $source = get_post_meta( $post_id, '_lgx_lsp_shortcodes_meta', true );
                    $source = is_array( $source ) && isset( $source['lgx_lswp_content_source'] ) ? $source['lgx_lswp_content_source'] : 'logo_library';
                    echo esc_html( $this->get_source_label( $source ) );
                    break;

                case 'php_shortcode':
                    $php_code = '<?php echo do_shortcode( \'[lgxlogoslider id="' . esc_attr( $post_id ) . '"]\' ); ?>';
echo '<div class="lgx-copy-group" style="display: flex; gap: 5px; align-items: center; max-width: 360px;">';
    echo '<input type="text" aria-label="' . esc_attr__( 'PHP Code', 'logo-slider-wp' ) . '"
        class="lgx-input lgx-copy-input lgx_logo_slider_list_copy_input selectable" readonly="readonly"
        value="' . esc_attr($php_code) . '" style="flex-grow: 1; text-align: center;">';
    echo '<button type="button" class="button lgx-btn lgx-btn-secondary lgx-copy-btn"
        data-clipboard-text="' . esc_attr($php_code) . '"
        aria-label="' . esc_attr__( 'Copy PHP code to clipboard', 'logo-slider-wp' ) . '"
        title="' . esc_attr__( 'Copy', 'logo-slider-wp' ) . '"><i class="lgxicon lgx-icon-copy"></i></button>';
    echo '</div>';
break;

default:
break;
}
}
}




/**
* Phase F: Render category filter dropdown (HP-5)
*/
public function render_category_filter( $post_type ) {
if ( 'logosliderwp' !== $post_type ) return;
$selected = isset( $_GET['logosliderwpcat'] ) ? sanitize_text_field( $_GET['logosliderwpcat'] ) : '';
wp_dropdown_categories( array(
'show_option_all' => __( 'All Categories', 'logo-slider-wp' ),
'taxonomy' => 'logosliderwpcat',
'name' => 'logosliderwpcat',
'orderby' => 'name',
'selected' => $selected,
'hide_empty' => false,
'value_field' => 'slug',
) );
}

/**
* Phase F: Render Empty State UX (HP-2)
*/
public function render_empty_state_ux( $which ) {
if ( 'top' !== $which ) {
return;
}

$current_screen = get_current_screen();
if ( ! is_object( $current_screen ) ) {
return;
}

if ( 'edit-logosliderwp' === $current_screen->id ) {
$counts = wp_count_posts( 'logosliderwp' );
$total = isset( $counts->publish ) ? $counts->publish : 0;
$total += isset( $counts->draft ) ? $counts->draft : 0;
$total += isset( $counts->pending ) ? $counts->pending : 0;

if ( $total > 0 || ( isset( $_GET['post_status'] ) && $_GET['post_status'] === 'trash' ) ) {
return;
}

?>
<div class="lgx-empty-state-wrapper"
    style="text-align: center; padding: 50px 20px; background: #fff; border: 1px dashed #c3c4c7; border-radius: 4px; margin-top: 10px;">
    <div class="dashicons dashicons-images-alt2"
        style="font-size: 48px; width: 48px; height: 48px; color: #a7aaad; margin-bottom: 15px;"></div>
    <h2 style="margin: 0 0 10px; font-size: 1.5em; font-weight: 600;">
        <?php esc_html_e( 'No logos found.', 'logo-slider-wp' ); ?></h2>
    <p style="margin: 0 0 20px; color: #646970; font-size: 14px;">
        <?php esc_html_e( 'Create your first logo to start building logo showcases.', 'logo-slider-wp' ); ?></p>
    <div style="margin-top: 15px;">
        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=logosliderwp' ) ); ?>"
            class="button button-primary button-large"><?php esc_html_e( 'Add New Logo', 'logo-slider-wp' ); ?></a>
    </div>
</div>
<style>
.wp-list-table,
.tablenav.bottom,
.search-box {
    display: none !important;
}
</style>
<?php
        } elseif ( 'edit-lgx_lsp_shortcodes' === $current_screen->id ) {
            $counts = wp_count_posts( 'lgx_lsp_shortcodes' );
            $total = isset( $counts->publish ) ? $counts->publish : 0;
            $total += isset( $counts->draft ) ? $counts->draft : 0;
            $total += isset( $counts->pending ) ? $counts->pending : 0;

            if ( $total > 0 || ( isset( $_GET['post_status'] ) && $_GET['post_status'] === 'trash' ) ) {
                return;
            }

            ?>
<div class="lgx-empty-state-wrapper"
    style="text-align: center; padding: 50px 20px; background: #fff; border: 1px dashed #c3c4c7; border-radius: 4px; margin-top: 10px;">
    <div class="dashicons dashicons-welcome-view-site"
        style="font-size: 48px; width: 48px; height: 48px; color: #a7aaad; margin-bottom: 15px;"></div>
    <h2 style="margin: 0 0 10px; font-size: 1.5em; font-weight: 600;">
        <?php esc_html_e( 'No showcases found.', 'logo-slider-wp' ); ?></h2>
    <p style="margin: 0 0 20px; color: #646970; font-size: 14px;">
        <?php esc_html_e( 'Create your first showcase to display your logos.', 'logo-slider-wp' ); ?></p>
    <div style="margin-top: 15px;">
        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ) ); ?>"
            class="button button-primary button-large"><?php esc_html_e( 'Add New Showcase', 'logo-slider-wp' ); ?></a>
    </div>
</div>
<style>
.wp-list-table,
.tablenav.bottom,
.search-box {
    display: none !important;
}
</style>
<?php
        }
    }

    /**
     * Phase F: Move Up / Down Keyboard Reorder (HP-6)
     */
    public function add_move_row_actions( $actions, $post ) {
        if ( 'logosliderwp' !== $post->post_type ) {
            return $actions;
        }

        $post_id = $post->ID;

        $up_nonce = wp_create_nonce( 'lgx_move_up_' . $post_id );
        $down_nonce = wp_create_nonce( 'lgx_move_down_' . $post_id );

        $up_link = '<a href="#" class="lgx-move-row-btn" data-action="up" data-post="' . esc_attr( $post_id ) . '" data-nonce="' . esc_attr( $up_nonce ) . '" title="' . esc_attr__( 'Move Up', 'logo-slider-wp' ) . '">&#8593; ' . esc_html__( 'Move Up', 'logo-slider-wp' ) . '</a>';
        $down_link = '<a href="#" class="lgx-move-row-btn" data-action="down" data-post="' . esc_attr( $post_id ) . '" data-nonce="' . esc_attr( $down_nonce ) . '" title="' . esc_attr__( 'Move Down', 'logo-slider-wp' ) . '">&#8595; ' . esc_html__( 'Move Down', 'logo-slider-wp' ) . '</a>';

        $ordered_actions = array();
        if ( isset( $actions['edit'] ) ) $ordered_actions['edit'] = $actions['edit'];
         if ( isset( $actions['inline hide-if-no-js'] ) ) $ordered_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];

        if ( isset( $actions['duplicate'] ) ) $ordered_actions['duplicate'] = $actions['duplicate'];

        $ordered_actions['lgx_move_up'] = $up_link;
        $ordered_actions['lgx_move_down'] = $down_link;

        if ( isset( $actions['trash'] ) ) $ordered_actions['trash'] = $actions['trash'];
        if ( isset( $actions['view'] ) ) $ordered_actions['view'] = $actions['view'];

        foreach ( $actions as $key => $action ) {
            if ( ! isset( $ordered_actions[$key] ) ) {
                $ordered_actions[$key] = $action;
            }
        }

        return $ordered_actions;
    }

    public function move_row_ajax_handler() {
        if ( empty( $_POST['post_id'] ) || empty( $_POST['direction'] ) || empty( $_POST['nonce'] ) ) {
            wp_send_json_error( array( 'message' => 'Missing parameters.' ) );
        }

        $post_id = absint( $_POST['post_id'] );
        $direction = sanitize_text_field( $_POST['direction'] );
        $nonce_action = 'lgx_move_' . $direction . '_' . $post_id;

        if ( ! wp_verify_nonce( $_POST['nonce'], $nonce_action ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed.' ) );
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }

        $current_post = get_post( $post_id );
        if ( ! $current_post ) {
            wp_send_json_error( array( 'message' => 'Post not found.' ) );
        }

        global $wpdb;
        $current_order = (int) $current_post->menu_order;

        if ( 'up' === $direction ) {
            $adjacent_post = $wpdb->get_row( $wpdb->prepare(
                "SELECT ID, menu_order FROM $wpdb->posts WHERE post_type = 'logosliderwp' AND post_status IN ('publish', 'draft') AND menu_order <= %d AND ID != %d ORDER BY menu_order DESC, ID DESC LIMIT 1",
                $current_order,
                $post_id
            ) );
        } else {
            $adjacent_post = $wpdb->get_row( $wpdb->prepare(
                "SELECT ID, menu_order FROM $wpdb->posts WHERE post_type = 'logosliderwp' AND post_status IN ('publish', 'draft') AND menu_order >= %d AND ID != %d ORDER BY menu_order ASC, ID ASC LIMIT 1",
                $current_order,
                $post_id
            ) );
        }

        if ( $adjacent_post ) {
            $adjacent_order = (int) $adjacent_post->menu_order;
            if ( $current_order === $adjacent_order ) {
                if ( 'up' === $direction ) {
                    $adjacent_order = $current_order - 1;
                } else {
                    $adjacent_order = $current_order + 1;
                }
                $wpdb->update( $wpdb->posts, array( 'menu_order' => $adjacent_order ), array( 'ID' => $adjacent_post->ID ) );
            } else {
                $wpdb->update( $wpdb->posts, array( 'menu_order' => $adjacent_order ), array( 'ID' => $post_id ) );
                $wpdb->update( $wpdb->posts, array( 'menu_order' => $current_order ), array( 'ID' => $adjacent_post->ID ) );
            }
            wp_send_json_success( array( 'message' => 'Row moved successfully.' ) );
        }

        wp_send_json_error( array( 'message' => 'No row to swap with.' ) );
    }

    /**
     * Phase F: Bulk Actions (MP-4)
     */
    public function register_bulk_actions( $bulk_actions ) {
        $bulk_actions['lgx_bulk_duplicate'] = __( 'Duplicate', 'logo-slider-wp' );
        $bulk_actions['lgx_bulk_publish'] = __( 'Publish', 'logo-slider-wp' );
        $bulk_actions['lgx_bulk_draft'] = __( 'Set to Draft', 'logo-slider-wp' );
        $bulk_actions['lgx_bulk_assign_category'] = __( 'Assign Category', 'logo-slider-wp' );
        return $bulk_actions;
    }

    public function handle_bulk_actions( $redirect_to, $doaction, $post_ids ) {
        if ( empty( $post_ids ) ) {
            return $redirect_to;
        }

        if ( 'lgx_bulk_publish' === $doaction ) {
            foreach ( $post_ids as $post_id ) {
                wp_update_post( array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ) );
            }
            $redirect_to = add_query_arg( 'lgx_bulk_published', count( $post_ids ), $redirect_to );
        } elseif ( 'lgx_bulk_draft' === $doaction ) {
            foreach ( $post_ids as $post_id ) {
                wp_update_post( array(
                    'ID' => $post_id,
                    'post_status' => 'draft'
                ) );
            }
            $redirect_to = add_query_arg( 'lgx_bulk_drafted', count( $post_ids ), $redirect_to );
        } elseif ( 'lgx_bulk_duplicate' === $doaction ) {
            $duplicated = 0;
            foreach ( $post_ids as $post_id ) {
                if ( $this->duplicate_post_internal( $post_id ) ) {
                    $duplicated++;
                }
            }
            $redirect_to = add_query_arg( 'lgx_bulk_duplicated', $duplicated, $redirect_to );
        } elseif ( 'lgx_bulk_assign_category' === $doaction ) {
            if ( isset( $_REQUEST['lgx_bulk_cat'] ) && ! empty( $_REQUEST['lgx_bulk_cat'] ) ) {
                $term_id = intval( $_REQUEST['lgx_bulk_cat'] );
                if ( $term_id > 0 ) {
                    foreach ( $post_ids as $post_id ) {
                        wp_set_object_terms( $post_id, array( $term_id ), 'logosliderwpcat', true ); // true appends
                    }
                    $redirect_to = add_query_arg( 'lgx_bulk_cat_assigned', count( $post_ids ), $redirect_to );
                }
            }
        }

        return $redirect_to;
    }

    private function duplicate_post_internal( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post || 'logosliderwp' !== $post->post_type ) {
            return false;
        }

        global $wpdb;
        $title = $post->post_title;
        $new_title = $title . ' ' . __( '(Copy)', 'logo-slider-wp' );
        $copy_number = 2;
        $max_copies = 100;
        while ( $copy_number <= $max_copies ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1", $new_title, 'logosliderwp' ) );
            if ( ! $exists ) break;
            $new_title = $title . ' ' . sprintf( __( '(Copy %d)', 'logo-slider-wp' ), $copy_number );
            $copy_number++;
        }

        $args = array(
            'post_type'      => $post->post_type,
            'post_title'     => $new_title,
            'post_status'    => 'draft',
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'menu_order'     => 0,
            'post_author'    => get_current_user_id()
        );
        $new_post_id = wp_insert_post( $args );

        if ( is_wp_error( $new_post_id ) || 0 === $new_post_id ) {
            return false;
        }

        $post_meta = get_post_meta( $post_id, '_logosliderwpmeta', true );
        if ( ! empty( $post_meta ) && is_array( $post_meta ) ) {
            update_post_meta( $new_post_id, '_logosliderwpmeta', $post_meta );
        }
        $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
        if ( ! empty( $thumbnail_id ) ) {
            update_post_meta( $new_post_id, '_thumbnail_id', $thumbnail_id );
        }
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
            if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
                wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
            }
        }
        return true;
    }

    /**
     * Automatically insert newly created logos at the top of the manual order.
     *
     * @param string  $new_status New post status.
     * @param string  $old_status Old post status.
     * @param WP_Post $post       Post object.
     */
    public function set_new_logo_menu_order( $new_status, $old_status, $post ) {
        // Skip if importing.
        if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
            return;
        }

        if ( 'logosliderwp' !== $post->post_type ) {
            return;
        }

        $non_tangible = array( 'new', 'auto-draft', 'false', '' );
        $tangible = array( 'publish', 'draft', 'pending', 'private', 'future' );

        // If it was already in a tangible status, this is an update, not a creation.
        if ( ! in_array( $old_status, $non_tangible, true ) ) {
            return;
        }

        // If the new status is not tangible, do nothing yet (e.g., just an auto-draft).
        if ( ! in_array( $new_status, $tangible, true ) ) {
            return;
        }

        global $wpdb;

        // Shift all existing logos' menu_order down by 1, except the new post.
        // We do this to make room at the top (menu_order 0) for the new logo.
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->posts} SET menu_order = menu_order + 1 WHERE post_type = %s AND ID != %d",
            'logosliderwp',
            $post->ID
        ) );

        // Ensure this new post is strictly at 0.
        if ( 0 !== (int) $post->menu_order ) {
            $wpdb->update(
                $wpdb->posts,
                array( 'menu_order' => 0 ),
                array( 'ID' => $post->ID ),
                array( '%d' ),
                array( '%d' )
            );
            clean_post_cache( $post->ID );
        }
    }
}
}
