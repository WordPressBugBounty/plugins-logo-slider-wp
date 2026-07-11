<?php
/**
 * Gutenberg block integration: Logo Slider Showcase picker.
 *
 * Registers a dynamic block that lets editors insert an existing
 * `lgx_lsp_shortcodes` entry by ID. Rendering is delegated entirely to the
 * existing [lgxlogoslider id="N"] shortcode pipeline — this class never
 * duplicates rendering logic.
 *
 * Create date : 02.07.2026, By : @VibeLogic
 *
 * @link       http://logichunt.com
 * @since      5.5.5
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/includes/integrations/gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Logo_Slider_WP_Gutenberg {

    /**
     * The ID of this plugin.
     *
     * @since    5.5.5
     * @access   private
     * @var      string    $plugin_name
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    5.5.5
     * @access   private
     * @var      string    $version
     */
    private $version;

    /**
     * Editor script handle. Registered explicitly here (rather than relying
     * on the handle WordPress would auto-derive from block.json) so that
     * wp_localize_script() in enqueue_block_editor_assets() always targets a
     * handle this class controls by name.
     */
    const SCRIPT_HANDLE = 'logo-slider-wp-gutenberg-block';

    /**
     * @since 5.5.5
     * Create date : 02.07.2026, By : @VibeLogic
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the dynamic block on the server. Hooked to `init`.
     *
     * Create date : 02.07.2026, By : @VibeLogic
     * @since 5.5.5
     */
    public function register_block() {

        $block_dir  = LGX_LS_PLUGIN_ROOT_PATH . 'includes/integrations/gutenberg/logo-slider-showcase';
        $asset_file = $block_dir . '/index.asset.php';

        if ( ! file_exists( $block_dir . '/block.json' ) || ! file_exists( $asset_file ) ) {
            // Build hasn't been run / copied yet. Fail quietly rather than
            // fataling a production site — the block simply won't appear
            // in the inserter until the compiled files are in place.
            return;
        }

        $asset = require $asset_file;

        wp_register_script(
            self::SCRIPT_HANDLE,
            LGX_LS_PLUGIN_ROOT_URL . 'includes/integrations/gutenberg/logo-slider-showcase/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_set_script_translations( self::SCRIPT_HANDLE, 'logo-slider-wp' );

        register_block_type(
            $block_dir,
            array(
                'editor_script'   => self::SCRIPT_HANDLE,
                'render_callback' => array( $this, 'render_callback' ),
            )
        );

        // Register RTL stylesheet variant for the editor style.
        wp_style_add_data( 'logo-slider-wp-logo-slider-showcase-editor-style', 'rtl', 'replace' );

    }

    /**
     * Enqueue editor-only assets and localize the slider picker data.
     * Hooked to `enqueue_block_editor_assets`.
     *
     * This method has two distinct responsibilities:
     *   1. Localize the slider-picker data (wp_localize_script).
     *   2. Enqueue the slider's own CSS/JS so the ServerSideRender preview
     *      is visually styled. Both are required — see §7 for why.
     *
     * Create date : 02.07.2026, By : @VibeLogic
     * @since 5.5.5
     */
    public function enqueue_block_editor_assets() {

        if ( ! wp_script_is( self::SCRIPT_HANDLE, 'registered' ) ) {
            return;
        }

        // --- Part 1: Localize slider picker data ---
        wp_localize_script(
            self::SCRIPT_HANDLE,
            'lgxLogoSliderBlock',
            array(
                'sliders'      => $this->get_slider_list(),
                'createNewUrl' => admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ),
                'editBaseUrl'  => admin_url( 'post.php?action=edit&post=' ),
            )
        );

        // --- Part 2: Enqueue slider preview assets (REQUIRED for styled SSR preview) ---
        // wp_enqueue_scripts never fires on edit-post.php, and the
        // ServerSideRender preview is fetched via a separate REST request
        // whose own wp_enqueue_* calls never reach this page's <head> or
        // footer — see GUTENBERG_BLOCK_PROMPT.md §7. The preview will be
        // completely unstyled (no layout, no fonts, no carousel) unless
        // these handles are explicitly enqueued here too.
        if ( class_exists( 'Logo_Slider_WP_Public' ) ) {
            $plugin_public = new Logo_Slider_WP_Public( $this->plugin_name, $this->version );
            $plugin_public->enqueue_scripts();
        }

        wp_enqueue_script( 'logo-slider-wp-swiper-js' );
        wp_enqueue_script( 'logo-slider-wp-tooltipster-js' );
        wp_enqueue_script( 'logo-slider-wp-public' );

    }

    /**
     * Enqueue assets for the editor iframe.
     * Hooked to `enqueue_block_assets`.
     *
     * Create date : 06.07.2026, By : @VibeLogic
     * @since 4.1.1
     */
    public function enqueue_block_assets() {
        if ( is_admin() ) {
            if ( class_exists( 'Logo_Slider_WP_Public' ) ) {
                $plugin_public = new Logo_Slider_WP_Public( $this->plugin_name, $this->version );
                $plugin_public->enqueue_styles();
            }

            wp_enqueue_style( 'logo-slider-wp-font' );
            wp_enqueue_style( 'logo-slider-wp-swiper-css' );
            wp_enqueue_style( 'logo-slider-wp-tooltipster-css' );
            wp_enqueue_style( 'lgx-logo-slider-style' );
        }
    }

    /**
     * Build the lightweight {id, title} list used by the SelectControl.
     * Exposes only title + ID — no post content or meta is ever sent to
     * the browser.
     *
     * Create date : 02.07.2026, By : @VibeLogic
     * @since 5.5.5
     * @return array<int, array{id:int, title:string}>
     */
    private function get_slider_list() {

        if ( ! current_user_can( 'edit_posts' ) ) {
            return array();
        }

        $posts = get_posts(
            array(
                'post_type'      => 'lgx_lsp_shortcodes',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'no_found_rows'  => true,
            )
        );

        return array_map(
            function ( $post ) {
                return array(
                    'id'    => absint( $post->ID ),
                    'title' => esc_html( get_the_title( $post ) ),
                );
            },
            $posts
        );

    }

    /**
     * Server-side render. Reuses the existing [lgxlogoslider] shortcode
     * pipeline identically — no rendering logic is duplicated here.
     *
     * Create date : 02.07.2026, By : @VibeLogic
     * @since 5.5.5
     * @param array $attributes Block attributes.
     * @return string
     */
    public function render_callback( $attributes ) {

        $shortcode_id = isset( $attributes['shortcodeId'] ) ? absint( $attributes['shortcodeId'] ) : 0;

        if ( ! $shortcode_id || 'lgx_lsp_shortcodes' !== get_post_type( $shortcode_id ) ) {
            if ( defined( 'REST_REQUEST' ) && REST_REQUEST && current_user_can( 'edit_posts' ) ) {
                return sprintf(
                    '<p style="padding:20px;background:#f0f0f0;border:1px dashed #ccc;text-align:center;color:#666;">%s</p>',
                    esc_html__( 'This Logo Slider block has no valid slider selected.', 'logo-slider-wp' )
                );
            }
            return '';
        }

        return sprintf(
            '<div %1$s>%2$s</div>',
            get_block_wrapper_attributes(),
            do_shortcode( '[lgxlogoslider id="' . $shortcode_id . '"]' )
        );

    }

}
