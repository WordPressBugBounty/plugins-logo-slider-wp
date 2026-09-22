<?php
/**
 * Showcase Builder Controller
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/showcase-builder
 * @since      6.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The core controller for the Showcase Builder module.
 *
 * Responsible for rendering the wrapper layout and enqueueing required assets.
 */
class Logo_Slider_WP_Showcase_Builder {

    /**
     * The ID of this plugin.
     *
     * @since    6.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    6.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    6.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    /**
     * Map the legacy metabox to our new Showcase Builder shell.
     * Also register the Embed sidebar metabox.
     */
    public function setup_metaboxes() {
        // Map the main edit area to our shell
        add_meta_box(
            'lgx_lsp_shortcodes_meta_box_panel',
            __( 'Showcase Builder', 'logo-slider-wp' ),
            array( $this, 'render_metabox' ),
            'lgx_lsp_shortcodes',
            'normal',
            'high'
        );
        


    }
    


    /**
     * Enqueue module-specific styles and scripts.
     *
     * @since    6.0.0
     * @param    string    $hook    The current admin page.
     */
    public function enqueue_scripts( $hook ) {

        // Only load on the lgx_lsp_shortcodes Add/Edit screen — not the
        // Showcases list, Trash, Search, or other screens of the same post type.
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        global $post_type;
        if ( 'lgx_lsp_shortcodes' !== $post_type ) {
            return;
        }

        // Enqueue the UI kit CSS
        wp_enqueue_style( $this->plugin_name . '-admin-ui', plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'admin/css/lgx-admin-ui.css', array(), $this->version, 'all' );
        
        // Enqueue Module Specific CSS
        wp_enqueue_style( $this->plugin_name . '-showcase-builder', plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'admin/modules/showcase-builder/assets/css/showcase-builder.css', array(), $this->version, 'all' );
        
        // Enqueue the UI kit JS
        wp_enqueue_script( $this->plugin_name . '-admin-ui', plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'admin/js/lgx-admin-ui.js', array(), $this->version, true );
        
        // Enqueue Module Specific JS
        wp_enqueue_script( $this->plugin_name . '-showcase-builder', plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'admin/modules/showcase-builder/assets/js/showcase-builder.js', array( $this->plugin_name . '-admin-ui' ), $this->version, true );
    }

    /**
     * Render the metabox containing the Showcase Builder wrapper.
     *
     * @since    6.0.0
     * @param    WP_Post    $post    The current post object.
     */
    public function render_metabox( $post ) {
        // Output the semantic HTML wrapper for the Builder
        require_once plugin_dir_path( __FILE__ ) . 'templates/builder-wrapper.php';
    }

}
