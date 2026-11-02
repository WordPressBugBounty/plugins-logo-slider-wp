<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/public
 */

/**
 * M-001 — sanitize a stored colour for inline CSS output.
 *
 * The alpha-enabled colour picker stores #rgb, #rgba, #rrggbb, #rrggbbaa,
 * rgb() and rgba() values; sanitize_hex_color() only accepts #rgb/#rrggbb and
 * so turned every other picker-produced value into an empty declaration.
 * Render-time only: nothing here writes to or normalises stored metadata.
 *
 * Returns the trimmed original value when it is one of the accepted forms
 * with in-range components, otherwise an empty string. Named colours, url(),
 * var(), and anything carrying CSS punctuation are rejected.
 *
 * @since  6.0.0
 * @param  mixed $value Stored colour value.
 * @return string       Safe CSS colour or ''.
 */
if ( ! function_exists( 'lgx_ls_sanitize_css_color' ) ) {
    function lgx_ls_sanitize_css_color( $value ) {
        if ( ! is_string( $value ) ) {
            return '';
        }
        $value = trim( $value );
        if ( '' === $value ) {
            return '';
        }

        if ( preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/iD', $value ) ) {
            return $value;
        }

        $num = '(?:\d+(?:\.\d+)?|\.\d+)';
        if ( preg_match( '/^rgba?\(\s*(' . $num . '%?)\s*,\s*(' . $num . '%?)\s*,\s*(' . $num . '%?)\s*(?:,\s*(' . $num . '%?)\s*)?\)$/iD', $value, $m ) ) {
            $channels = array( $m[1], $m[2], $m[3] );
            $percent  = ( '%' === substr( $channels[0], -1 ) );
            foreach ( $channels as $c ) {
                if ( ( '%' === substr( $c, -1 ) ) !== $percent ) {
                    return '';
                }
                if ( (float) $c > ( $percent ? 100 : 255 ) ) {
                    return '';
                }
            }
            if ( isset( $m[4] ) && '' !== $m[4] ) {
                if ( (float) $m[4] > ( '%' === substr( $m[4], -1 ) ? 100 : 1 ) ) {
                    return '';
                }
            }
            return $value;
        }

        return '';
    }
}

/**
 * M-001 — build one CSS declaration, or nothing when it would be incomplete.
 *
 * lgx_ls_sanitize_css_color() returns '' for an unusable stored colour. The
 * colour render sites emit their declaration through this helper so that an
 * invalid or empty colour results in the whole declaration being omitted,
 * rather than a malformed "property: ;" or a colourless "border: 1px solid ;".
 * It only ever sees the declarations built from the colour fields; other CSS
 * is not passed through it.
 *
 * @since  6.0.0
 * @param  string      $property CSS property name.
 * @param  string      $value    Declaration value (a sanitized colour, or a shorthand ending in one).
 * @param  string|null $required Optional value that must be non-empty for the declaration to be emitted
 *                               (pass the colour for shorthands such as "border").
 * @return string                "property: value;" or ''.
 */
if ( ! function_exists( 'lgx_ls_css_decl' ) ) {
    function lgx_ls_css_decl( $property, $value, $required = null ) {
        if ( null !== $required && '' === trim( (string) $required ) ) {
            return '';
        }
        $value = trim( (string) $value );
        return ( '' === $value ) ? '' : $property . ': ' . $value . ';';
    }
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/public
 * @author     LogicHunt <info.logichunt@gmail.com>
 */
if ( ! class_exists( 'Logo_Slider_WP_Public' ) ) {
class Logo_Slider_WP_Public {


    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version;

    /**
     * @var Lgx_Carousel_Settings_API
     */
    private $settings_api;

    /**
     * Task 011 — the merged (saved + unsaved-preview-submitted) meta array,
     * set by maybe_render_preview() only when a valid transient override
     * exists, read by filter_preview_meta_override().
     *
     * @var array|null
     */
    private $preview_meta_override = null;

    /**
     * Task 011 — the post_id filter_preview_meta_override() is scoped to
     * for the current request. See maybe_render_preview().
     *
     * @var int|null
     */
    private $preview_meta_override_post_id = null;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->settings_api = new Lgx_Carousel_Settings_API($plugin_name, $version);

        if ( ! shortcode_exists( 'logo-slider' ) ) {
            add_shortcode('logo-slider', array($this, 'logo_slider_wp_shortcode_function_legacy' ));
        }
    }




    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {


        /**
         *
         * Deprecated Slider Style
         *
         */

        wp_register_style('lgx-logo-slider-owl', plugin_dir_url( __FILE__ ) . 'assets/libs/owl/assets/owl.carousel.min.css', array(), $this->version, 'all' );
        wp_register_style('lgx-logo-slider-owltheme', plugin_dir_url( __FILE__ ) . 'assets/libs/owl/assets/owl.theme.default.min.css', array('lgx-logo-slider-owl'), $this->version, 'all' );
        wp_register_style( 'lgx-logo-slider-style-legacy', plugin_dir_url( __FILE__ ) . 'assets/css/logosliderwppublic-dep.min.css', array('lgx-logo-slider-owl'), $this->version, 'all' );


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
        wp_register_style('logo-slider-wp-font', plugin_dir_url( __FILE__ ) . 'assets/css/font-awesome.min.css', array(), $this->version );

        //Swiper style
        wp_register_style('logo-slider-wp-swiper-css', plugin_dir_url( __FILE__ ) . 'assets/libs/swiper/swiper-bundle.min.css', array(), $this->version );

        //TooltipStar
        wp_register_style('logo-slider-wp-tooltipster-css', plugin_dir_url( __FILE__ ) . 'assets/libs/tooltipster/css/tooltipster.bundle.min.css', array(), $this->version );

        //Main Style
        wp_register_style( 'lgx-logo-slider-style', plugin_dir_url( __FILE__ ) . 'assets/css/logo-slider-wp-public.min.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {


        // Deprecated Script
        wp_register_script('lgxlogoowljs', plugin_dir_url( __FILE__ ) . 'assets/libs/owl/owl.carousel.js', array( 'jquery' ), $this->version, false );
        wp_register_script('lgxlogoowljs-modified', plugin_dir_url( __FILE__ ) . 'assets/libs/owl/owl-modified.js', array( 'lgxlogoowljs' ), $this->version, false );
        wp_register_script( 'lgx-logo-slider-script-legacy', plugin_dir_url( __FILE__ ) . 'assets/js/logosliderwppublic-dep.js', array( 'lgxlogoowljs' ), $this->version, false );

        // Localize the script
        $translation_array = array(
            'owl_navigationTextL'    => plugin_dir_url( __FILE__ ). 'assets/img/prev.png',
            'owl_navigationTextR'    => plugin_dir_url( __FILE__ ) . 'assets/img/next.png',
        );
        wp_localize_script( 'lgx-logo-slider-script-legacy', 'logosliderwp', $translation_array );

        // wp_enqueue_script('jquery');

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

        // Swiper Script
        wp_register_script('logo-slider-wp-swiper-js', plugin_dir_url( __FILE__ ) . 'assets/libs/swiper/swiper-bundle.min.js', array(), $this->version, true );

        //TooltipStar
        wp_register_script('logo-slider-wp-tooltipster-js', plugin_dir_url( __FILE__ ) . 'assets/libs/tooltipster/js/tooltipster.bundle.min.js', array('jquery'), $this->version, true );

        //Masonry
     //   wp_register_script('logo-slider-wp-masonry-js', plugin_dir_url( __FILE__ ) . 'assets/libs/masonry/masonry.pkgd.min.js', array('jquery'), $this->version, true );

        //Main Script
        wp_register_script('logo-slider-wp-public', plugin_dir_url( __FILE__ ) . 'assets/js/logo-slider-wp-public.js', array('jquery'), $this->version, true );

        // Task 014 (B38): translation-ready strings for the Ticker Mode
        // pause/play button's dynamic aria-label (set in JS, not PHP markup,
        // since wp_kses_post() would strip aria-pressed from a server string).
        wp_localize_script( 'logo-slider-wp-public', 'lgxLsPublicI18n', array(
            'tickerPause'  => __( 'Pause ticker scrolling', 'logo-slider-wp' ),
            'tickerResume' => __( 'Resume ticker scrolling', 'logo-slider-wp' ),
        ) );
    }


    

    /**
     * Plugin Output Function
     * @param $atts
     *
     * @return string
     *  @since 1.0.0
     */

    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public static function lgx_output_function_legacy($params){

        //Query args
        $cats       = esc_attr($params['cat'] );
        $target      = esc_attr( $params['target'] );
        $order      = esc_attr($params['order'] );
        $orderby    = esc_attr( $params['orderby']);
        $limit      = intval($params['limit']);
        $ticker         = esc_attr($params['ticker']);
        $compnayanme_en     = esc_attr($params['companyname']);
        $compnaydesc_en     = esc_attr($params['companydesc']);

        //Carousel Style
        $logo_height 	= intval($params['maxheight']);
        $logo_width  	= intval($params['maxwidth']);
        $border      	= esc_attr($params['border']);
        $bordercolor 	= esc_attr($params['bordercolor']);
        $navposition 	= esc_attr($params['navposition']);
        $enbg     		= esc_attr($params['enbg']);
        $bgcolor    	= esc_attr($params['bgcolor']);
        $hovertype    	= esc_attr($params['hovertype']);


        //Data Attribute

        $data_attr                        = array();

        if($ticker == 'yes') {

            $data_attr['autoplaytimeout']   = 0;
            $data_attr['autoplayspeed']     = 3000;

        } else {
            $data_attr['autoplaytimeout']     = intval($params['autoplay_timeout']);
            $data_attr['autoplayspeed']       = intval($params['autoplay_speed']);

        }

        $data_attr['margin']              = intval($params['margin']);
        $data_attr['loop']                = esc_attr($params['loop'] == 'no') ? 'false' : 'true';
        $data_attr['autoplay']            = esc_attr($params['autoplay'] == 'no') ? 'false' : 'true';


        $data_attr['autoplayhoverpause']  = esc_attr($params['hover_pause'] == 'no') ? 'false' : 'true';
        $data_attr['dots']                = esc_attr($params['dots'] == 'no') ? 'false' : 'true';


        //
        $data_attr['itemlarge']           = intval($params['itemlarge']);
        $data_attr['itemdesk']            = intval($params['itemdesk']);
        $data_attr['itemtablet']          = intval($params['itemtablet']);
        $data_attr['itemmobile']          = intval($params['itemmobile']);


        $data_attr['navlarge']           = esc_attr($params['navlarge'] == 'no') ? 'false' : 'true';
        $data_attr['navdesk']            = esc_attr($params['navdesk'] == 'no') ? 'false' : 'true';
        $data_attr['navtablet']          = esc_attr($params['navtablet'] == 'no') ? 'false' : 'true';
        $data_attr['navmobile']          = esc_attr($params['navmobile'] == 'no') ? 'false' : 'true';


        // Mixing
        $border_color_style = ($border == 'yes') ? 'style="border-color:'.$bordercolor.';"' : '';
        $border_class       = ($border == 'yes') ? 'wp-logo-border' : '';
        $logo_style = 'style="max-width: '.$logo_width.'px;max-height: '.$logo_height.'px;"';
        $bg_style = ($enbg == 'yes') ? 'style=" background-color:'.$bgcolor.';"' : '';



        // Apply Data Attribute
        $data_attr_str = '';
        foreach ($data_attr as $key => $value) {
            $data_attr_str .= ' data-' . $key . '="' . $value . '" ';
        }

        $logo_args = array(
            'post_type'         => array( 'logosliderwp' ),
            'post_status'       => array( 'publish' ),
            'order'             => $order,
            'orderby'           => $orderby,
            'posts_per_page'    => $limit
        );

        // Category to Array Convert
        if( !empty($cats) && $cats != '' ){
            $cats = trim($cats);
            $cats_arr   = explode(',', $cats);

            if(is_array($cats_arr) && sizeof($cats_arr) > 0){
                $logo_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'logosliderwpcat',
                        'field'    => 'slug',
                        'terms'    => $cats_arr
                    )
                );

            }
        }


        // The  Query
        $logo_post = new WP_Query( $logo_args );
        $logo_item    = '';

        // Enqueue Style
        wp_enqueue_style( 'lgx-logo-slider-owl' );
        wp_enqueue_style( 'lgx-logo-slider-owltheme' );
        wp_enqueue_style( 'lgx-logo-slider-style-legacy');


        //Enqueue Script
        wp_enqueue_script( 'lgxlogoowljs' );

        if($ticker == 'yes') {

            wp_enqueue_script( 'lgxlogoowljs-modified' );
        }

        wp_enqueue_script( 'lgx-logo-slider-script-legacy' );

        // The Loop
        if ( $logo_post->have_posts() ) {

            while ( $logo_post->have_posts() ) {

                $logo_post->the_post();
                $post_id            = get_the_ID();
                $post_title         = get_the_title( $post_id );
                $metavalues         = get_post_meta( $post_id, '_logosliderwpmeta', true );
                $company_name       = ! empty( $metavalues['company_name'] ) ? $metavalues['company_name'] : $post_title;
                $company_url        = ! empty( $metavalues['company_url'] ) ? $metavalues['company_url'] : '';
                $company_desc       = ! empty( $metavalues['company_desc'] ) ? $metavalues['company_desc'] : '';

                $logo_img = '';
                if (has_post_thumbnail( $post_id )) {
                    $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id), true );
                    $thumb_url      = $thumb_url[0];
                    $alt_text = get_post_meta( get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true );


                    $logo_img .= '<div class="lgx-logo-item" '.esc_attr($border_color_style).'>';
                    if ( ! empty( $company_url ) ) {
                        $logo_img .= '<a href="' . esc_url( $company_url ) . '" target="' . esc_attr( $target ) . '">';
                    }
                    $logo_img .= '<img class="lgx-logo-img skip-lazy" src="' . esc_url( $thumb_url ) . '" ' . wp_kses_post( $logo_style ) . ' alt="' . esc_attr( $alt_text ) . '" title="' . esc_attr( ! empty( $company_name ) ? $company_name : 'Company' ) . '" />';
                    if ( ! empty( $company_url ) ) {
                        $logo_img .= '</a>';
                    }
                    $logo_img .= ( ! empty( $company_name ) && $compnayanme_en == 'yes' ) ? '<h6 class="logo-company-name">' . esc_html( $company_name ) . '</h6>' : '';
                    $logo_img .= ( ! empty( $company_desc ) && $compnaydesc_en == 'yes' ) ? '<p class="logo-company-desc">' . esc_html( $company_desc ) . '</p>' : '';
                    $logo_img .= '</div>';

                }

                $logo_item .= '<div class="item lgx-log-item" >';
                $logo_item .= $logo_img;
                $logo_item .=  '</div>';

            }
            wp_reset_postdata();// Restore original Post Data

            //Output String
            $output  = '<div  class="lgx_logo_slider_app_wrapper lgx-logo-slider-wp">';
            $output .= '<div class="lgx-logo-wrapper '.$border_class.' nav-position-'.$navposition.' hover-'.$hovertype.'" '.$bg_style.' >';
            $output .= '<div class="owl-carousel lgx-logo-carousel" ' . $data_attr_str . ' >' . $logo_item . '</div>';
            $output .= '</div>';
            $output .= '</div>';

            return $output;

        } // Check post exist
        else {
            _e('There are no logo item. Please add some logo Item', 'logo-slider-wp');
        }



    }


    /**
     * Define Short Code Function
     *
     * @param $atts
     *
     * @return mixed
     * @since 1.0.0
     */

    /* Developer note: no need to modify further inside the code, because we will drop this support very soon. */
    public function logo_slider_wp_shortcode_function_legacy($atts) {

        $cats_set       = $this->settings_api->get_option('logosliderwp_settings_cat', 'logosliderwp_adv', '');

        $order_set      = $this->settings_api->get_option('logosliderwp_settings_order', 'logosliderwp_basic', 'DESC');

        $orderby_set    = $this->settings_api->get_option('logosliderwp_settings_orderby', 'logosliderwp_adv', 'orderby');

        $limit_set      = $this->settings_api->get_option('logosliderwp_settings_limit', 'logosliderwp_adv', -1);


        $max_height          = intval($this->settings_api->get_option('logosliderwp_settings_height', 'logosliderwp_basic', 350));
        $max_width           = intval($this->settings_api->get_option('logosliderwp_settings_width', 'logosliderwp_basic', 350));

        $compnayanme_set     = trim($this->settings_api->get_option('logosliderwp_settings_show_company', 'logosliderwp_basic', 'no'));
        $compnaydesc_set     = trim($this->settings_api->get_option('logosliderwp_settings_show_company_desc', 'logosliderwp_basic', 'no'));



        //Data Attribute
        $margin_set     = trim($this->settings_api->get_option('logosliderwp_settings_margin', 'logosliderwp_config', 10));

        $loop_set       = $this->settings_api->get_option('logosliderwp_settings_loop', 'logosliderwp_config', 'yes');

        $autoplay_set   = trim($this->settings_api->get_option('logosliderwp_settings_autoplay', 'logosliderwp_config', 'yes'));


        $autoplay_timeout_set = intval($this->settings_api->get_option('logosliderwp_settings_autoplay_timeout', 'logosliderwp_config', 2000));

        $autoplay_speed_set  = intval($this->settings_api->get_option('logosliderwp_settings_autoplay_slidespeed', 'logosliderwp_config', 1000));


        $hover_pause_set  = $this->settings_api->get_option('logosliderwp_settings_hover_pause', 'logosliderwp_config', 'no');

        $dots_set         	= trim($this->settings_api->get_option('logosliderwp_settings_dots', 'logosliderwp_config', 'yes'));


        // Responsive
        $item_set_lagedesctop   = $this->settings_api->get_option('logosliderwp_settings_largedesktop_item', 'logosliderwp_responsive', 5);
        $nav_set_lagedesctop   =  $this->settings_api->get_option('logosliderwp_settings_desktop_nav', 'logosliderwp_responsive', 'yes');

        $item_set_desctop    = $this->settings_api->get_option('logosliderwp_settings_desktop_item', 'logosliderwp_responsive', 4);
        $nav_set_desctop     =  $this->settings_api->get_option('logosliderwp_settings_desktop_nav', 'logosliderwp_responsive', 'yes');

        $item_set_tablet    = $this->settings_api->get_option('logosliderwp_settings_tablet_item', 'logosliderwp_responsive', 3);
        $nav_set_tablet    =  $this->settings_api->get_option('logosliderwp_settings_tablet_nav', 'logosliderwp_responsive', 'yes');

        $item_set_mobile   = $this->settings_api->get_option('logosliderwp_settings_mobile_item', 'logosliderwp_responsive', 2);
        $nav_set_mobile    =  $this->settings_api->get_option('logosliderwp_settings_mobile_nav', 'logosliderwp_responsive', 'yes');



        //Style
        $bgcolor_set    	= trim($this->settings_api->get_option('logosliderwp_settings_bgcolor', 'logosliderwp_style', '#f1f1f1'));
        $bordercolor_set     = trim($this->settings_api->get_option('logosliderwp_settings_bordercolor', 'logosliderwp_style', '#f1f1f1'));
        $enbg_set            =  $this->settings_api->get_option('logosliderwp_settings_bgcolor_en', 'logosliderwp_style', 'no');
        $border_set       	 =  $this->settings_api->get_option('logosliderwp_settings_border_en', 'logosliderwp_style', 'no');
        $navposition_set      =  $this->settings_api->get_option('logosliderwp_settings_nav_position', 'logosliderwp_style', 'b-center');
        $hovertype_set        =  $this->settings_api->get_option('logosliderwp_settings_hover_type', 'logosliderwp_style', 'default');


        $atts = shortcode_atts(array(
            'target'            => '_blank',
            'ticker'            => 'no',
            'order'            => esc_attr($order_set),
            'orderby'          => esc_attr($orderby_set),
            'limit'            => esc_attr($limit_set),
            'hovertype'        => esc_attr($hovertype_set),
            'companyname'      => esc_attr($compnayanme_set),
            'companydesc'      => esc_attr($compnaydesc_set),
            'maxheight'        => esc_attr($max_height),
            'maxwidth'         => esc_attr($max_width),
            'enbg'             => esc_attr($enbg_set),
            'border'           => esc_attr($border_set),
            'bordercolor'      => esc_attr($bordercolor_set),
            'navposition'      => esc_attr($navposition_set),
            'cat'              => esc_attr($cats_set),
            'bgcolor'          => esc_attr($bgcolor_set),
            'margin'           => esc_attr($margin_set),
            'loop'             => esc_attr($loop_set),
            'autoplay'         => esc_attr($autoplay_set),
            'autoplay_timeout' => esc_attr($autoplay_timeout_set),
            'autoplay_speed'   => esc_attr($autoplay_speed_set),
            'hover_pause'      => esc_attr($hover_pause_set),
            'dots'             => esc_attr($dots_set),
            'itemlarge'        => esc_attr($item_set_lagedesctop),
            'itemdesk'         => esc_attr($item_set_desctop),
            'itemtablet'       => esc_attr($item_set_tablet),
            'itemmobile'       => esc_attr($item_set_mobile),
            'navlarge'         => esc_attr($nav_set_lagedesctop),
            'navdesk'          => esc_attr($nav_set_desctop),
            'navtablet'        => esc_attr($nav_set_tablet),
            'navmobile'        => esc_attr($nav_set_mobile),
        ), $atts, 'logo-slider');

        $output = $this->lgx_output_function_legacy($atts);

        return $output;
    }


    /**
     *
     *  Version 3 Started
     *
     */

    public function register_lgx_logo_slider_shortcode() {
        // wp_register_style('logo-slider-wp-swiper-css', plugin_dir_url( __FILE__ ) . 'js/swiper-bundle.min.css', array(), $this->version );
        // wp_register_script('logo-slider-wp-swiper-js', plugin_dir_url( __FILE__ ) . 'js/swiper-bundle.min.js', array(), $this->version, true );

        add_shortcode('lgxlogoslider', array( $this, 'display_lgx_logo_slider' ) );
    }

    public function display_lgx_logo_slider($atts) {
        if ( ! isset( $atts['id'] ) ) {
            return '<p style="color: red;">Error: The showcase ID is missing. Please add a Showcase ID.</p>';
        } else {
            $lgx_shortcodes_meta = get_post_meta( $atts['id'], '_lgx_lsp_shortcodes_meta', true );

            if(empty($lgx_shortcodes_meta)) {
                // Task 011 Phase 4b/B03: Avoid throwing an error in the Live Preview if the showcase is completely new
                // and has not been saved yet (which means get_post_meta returns empty).
                if ( ! empty( $_GET['lgx_preview'] ) ) {
                    // Reuses the exact .lgx-preview-spinner markup/CSS/keyframe name from
                    // admin/modules/showcase-builder/assets/css/showcase-builder.css (already
                    // driving the builder page's own preview-loading overlay) — inlined here,
                    // not enqueued, because this document is the iframe's own separate page
                    // load (preview-template.php) and never receives showcase-builder.css.
                    return '<style>.lgx-preview-spinner{width:32px;height:32px;border:3px solid #c3c4c7;border-top-color:#2271b1;border-radius:50%;animation:lgx-preview-spin 700ms linear infinite;}@keyframes lgx-preview-spin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}</style><div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;height:100vh;color:#888;font-family:sans-serif;"><span class="lgx-preview-spinner" aria-hidden="true"></span>' . esc_html__( 'Loading preview...', 'logo-slider-wp' ) . '</div>';
                }
                return '<p style="color: red;">Error: The showcase ID is not valid. Please add a valid Showcase ID.</p>';
            }
            //echo '<pre>';print_r($lgx_shortcodes_meta['showcase_type']);echo '</pre>';
            $lgx_lsw_loading_icon = plugin_dir_url( __FILE__ ). 'assets/img/loader.gif';
            ob_start();
            include('partials/view-controller.php');
            return ob_get_clean();
        }
    }

    /**
     * Task 010 / ADR-009 Decision 5 — Live Preview endpoint.
     *
     * Whitelist the query vars the preview URL uses so get_query_var() reads
     * them reliably regardless of permalink structure ("plain" or otherwise).
     *
     * @since 6.1.0
     * @param array $vars
     * @return array
     */
    public function register_preview_query_vars( $vars ) {
        $vars[] = 'lgx_preview';
        $vars[] = 'post_id';
        $vars[] = 'nonce';
        return $vars;
    }

    /**
     * Task 010 Phase 2 fix — suppress the wp-admin toolbar inside the Live
     * Preview iframe for logged-in users viewing the builder.
     *
     * show_admin_bar() must run before _wp_admin_bar_init(), which core hooks
     * to template_redirect at priority 0 (wp-includes/default-filters.php).
     * maybe_render_preview() itself (below) runs on template_redirect at the
     * default priority 10 — too late; by then the admin bar object already
     * exists and wp_footer()'s back-compat wp_admin_bar_render() call (also
     * core, for themes that don't call wp_body_open()) would still render it.
     * This method is therefore registered on the much earlier `init` hook
     * instead (see includes/class-logo-slider-wp.php).
     *
     * WordPress has not parsed query vars yet this early (that happens during
     * the `wp` action, after `init`), so this checks $_GET directly rather
     * than get_query_var() — a plain boolean gate only, not used for anything
     * security-sensitive. The actual nonce and post validation still happens
     * in maybe_render_preview() on template_redirect, unchanged.
     *
     * @since 6.1.0
     */
    public function maybe_disable_admin_bar_for_preview() {
        if ( ! empty( $_GET['lgx_preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- boolean gate only; real validation happens later in maybe_render_preview().
            show_admin_bar( false );
        }
    }

    /**
     * Task 010 / ADR-009 Decision 5 — Live Preview endpoint handler.
     *
     * Intercepts any front-end request carrying ?lgx_preview=1&post_id=X&nonce=Y
     * (hooked to template_redirect — see includes/class-logo-slider-wp.php).
     * Validates the nonce and the target post, bypasses caching, and renders
     * shortcode-only output via a minimal template (partials/preview-template.php)
     * — no theme header, footer, sidebar, or wp-admin bar (ADR-009 Decision 5
     * "Implementation requirements"). Reuses display_lgx_logo_slider() unchanged
     * for the actual render — no shortcode logic is duplicated.
     *
     * Explicitly registers + enqueues the plugin's styles/scripts itself before
     * wp_head() runs, rather than relying on the normal wp_enqueue_scripts timing
     * a theme's own the_content() pass would provide — this endpoint has no theme
     * around it, so nothing else would trigger that registration in time.
     *
     * @since 6.1.0
     */
    public function maybe_render_preview() {

        if ( empty( get_query_var( 'lgx_preview' ) ) ) {
            return;
        }

        $post_id = absint( get_query_var( 'post_id' ) );
        $nonce   = sanitize_text_field( get_query_var( 'nonce' ) );

        if ( ! $post_id || ! wp_verify_nonce( $nonce, 'lgx_preview_' . $post_id ) ) {
            wp_die( esc_html__( 'Invalid or expired preview link.', 'logo-slider-wp' ), '', array( 'response' => 403 ) );
        }

        if ( 'lgx_lsp_shortcodes' !== get_post_type( $post_id ) ) {
            wp_die( esc_html__( 'Invalid preview target.', 'logo-slider-wp' ), '', array( 'response' => 404 ) );
        }

        // Cache-bypass: WP core's standard helper for exactly this purpose
        // (Cache-Control: no-cache, no-store, must-revalidate + Expires in
        // the past) — ADR-009 Decision 5's "must bypass caching" requirement.
        nocache_headers();

        // Task 011: if handle_preview_render_ajax() stored a merged,
        // unsaved-values override for this post within the last 60s, use it
        // instead of the plain saved meta. Implemented as a get_post_metadata
        // filter, not by touching display_lgx_logo_slider()'s own DB read —
        // that function (and its full call chain) stays byte-for-byte the
        // same code path Task 010 and the real [lgxlogoslider] shortcode
        // both already use. The filter is left attached for the rest of
        // this request rather than removed after one use: display_lgx_logo_slider()
        // reads this meta key exactly once into a local variable and shares
        // that scope with every included partial, so nothing re-queries it —
        // there is nothing later in this request the filter could wrongly affect.
        $override = get_transient( $this->get_preview_transient_key( $post_id ) );
        if ( is_array( $override ) ) {
            $this->preview_meta_override          = $override;
            $this->preview_meta_override_post_id   = $post_id;
            add_filter( 'get_post_metadata', array( $this, 'filter_preview_meta_override' ), 10, 4 );
        }

        // T-283 (B207): keep other plugins' output out of this preview request (front end untouched).
        $this->restrict_preview_output();

        // Register + enqueue before wp_head() runs (see docblock above).
        $this->enqueue_styles();
        $this->enqueue_scripts();
        $preview_html = $this->display_lgx_logo_slider( array( 'id' => $post_id ) );

        include plugin_dir_path( __FILE__ ) . 'partials/preview-template.php';
        exit;
    }

    /**
     * T-283 (B207) — keeps the output of other plugins out of the Live Preview.
     *
     * Called only from maybe_render_preview(), after its nonce and post-type checks and before
     * wp_head()/wp_footer() run, so a visitor's front end is never touched. It removes every
     * callback hooked to wp_enqueue_scripts, wp_head, wp_footer, wp_print_scripts,
     * wp_print_footer_scripts and wp_body_open whose source file is not in WordPress core, the
     * active theme (or its parent) or this plugin, and registers two small actions that dequeue
     * the style/script handles of other plugins just before WordPress prints them. A callback
     * whose source file cannot be determined is kept.
     *
     * Filter logosliderwp_preview_keep_plugin_output: returns an array of plugin folder names
     * (for example 'my-fonts') whose callbacks and handles stay in the preview, for a site that
     * needs one of its plugins there. It is applied once per preview request.
     *
     * @since 6.1.1
     */
    private function restrict_preview_output() {

        static $done = false;
        if ( $done ) {
            return;
        }
        $done = true;

        // Plugin folders that must stay (escape hatch). Folder names only; anything else is ignored.
        $keep_slugs = apply_filters( 'logosliderwp_preview_keep_plugin_output', array() );
        $keep_slugs = is_array( $keep_slugs ) ? $keep_slugs : array();

        $plugin_dir = trailingslashit( wp_normalize_path( WP_PLUGIN_DIR ) );
        $plugin_url = preg_replace( '#^https?:#i', '', trailingslashit( WP_PLUGIN_URL ) );
        $mu_url     = preg_replace( '#^https?:#i', '', trailingslashit( WPMU_PLUGIN_URL ) );
        $own_url    = preg_replace( '#^https?:#i', '', trailingslashit( dirname( untrailingslashit( plugin_dir_url( __FILE__ ) ) ) ) );

        // Source folders whose callbacks stay: core, the active theme and its parent, this plugin.
        $keep_dirs = array(
            trailingslashit( wp_normalize_path( ABSPATH . WPINC ) ),
            trailingslashit( wp_normalize_path( ABSPATH . 'wp-admin' ) ),
            trailingslashit( wp_normalize_path( get_stylesheet_directory() ) ),
            trailingslashit( wp_normalize_path( get_template_directory() ) ),
            trailingslashit( wp_normalize_path( dirname( plugin_dir_path( __FILE__ ) ) ) ),
        );
        $keep_urls = array();
        foreach ( $keep_slugs as $keep_slug ) {
            if ( is_string( $keep_slug ) && preg_match( '/^[A-Za-z0-9._-]+$/', $keep_slug ) && '.' !== $keep_slug && '..' !== $keep_slug ) {
                $keep_dirs[] = $plugin_dir . $keep_slug . '/';
                $keep_urls[] = $plugin_url . $keep_slug . '/';
            }
        }

        // Source file of a callback ('' when it cannot be determined).
        $file_of = function ( $callback ) {
            try {
                if ( $callback instanceof Closure ) {
                    $reflection = new ReflectionFunction( $callback );
                } elseif ( is_string( $callback ) && false !== strpos( $callback, '::' ) ) {
                    $parts      = explode( '::', $callback, 2 );
                    $reflection = new ReflectionMethod( $parts[0], $parts[1] );
                } elseif ( is_string( $callback ) ) {
                    $reflection = new ReflectionFunction( $callback );
                } elseif ( is_array( $callback ) && isset( $callback[0], $callback[1] ) ) {
                    $reflection = new ReflectionMethod( $callback[0], $callback[1] );
                } elseif ( is_object( $callback ) && method_exists( $callback, '__invoke' ) ) {
                    $reflection = new ReflectionMethod( $callback, '__invoke' );
                } else {
                    return '';
                }
                $file = $reflection->getFileName();
            } catch ( Throwable $e ) {
                return '';
            }
            return is_string( $file ) ? wp_normalize_path( $file ) : '';
        };

        // True when a callback stays: unknown source file (fail open) or a kept folder.
        $is_kept = function ( $file ) use ( $keep_dirs ) {
            if ( '' === $file ) {
                return true;
            }
            foreach ( $keep_dirs as $keep_dir ) {
                if ( 0 === stripos( $file, $keep_dir ) ) {
                    return true;
                }
            }
            return false;
        };

        // Remove the other callbacks. Works on a copy of each hook's callbacks array.
        global $wp_filter;
        $hooks = array( 'wp_enqueue_scripts', 'wp_head', 'wp_footer', 'wp_print_scripts', 'wp_print_footer_scripts', 'wp_body_open' );
        foreach ( $hooks as $hook ) {
            if ( empty( $wp_filter[ $hook ] ) || ! isset( $wp_filter[ $hook ]->callbacks ) ) {
                continue;
            }
            $callbacks = $wp_filter[ $hook ]->callbacks;
            foreach ( $callbacks as $priority => $entries ) {
                foreach ( $entries as $entry ) {
                    if ( ! isset( $entry['function'] ) || $is_kept( $file_of( $entry['function'] ) ) ) {
                        continue;
                    }
                    remove_action( $hook, $entry['function'], $priority );
                }
            }
        }

        // Safety net for handles queued by other plugins: dequeue them just before WordPress prints them.
        $dequeue_other_plugin_handles = function () use ( $plugin_url, $mu_url, $own_url, $keep_urls ) {
            $lists = array(
                array( wp_styles(), 'wp_dequeue_style' ),
                array( wp_scripts(), 'wp_dequeue_script' ),
            );
            foreach ( $lists as $list ) {
                $deps = $list[0];
                foreach ( (array) $deps->queue as $handle ) {
                    if ( empty( $deps->registered[ $handle ] ) || ! is_string( $deps->registered[ $handle ]->src ) ) {
                        continue;
                    }
                    $src = preg_replace( '#^https?:#i', '', $deps->registered[ $handle ]->src );
                    if ( 0 !== strpos( $src, $plugin_url ) && 0 !== strpos( $src, $mu_url ) ) {
                        continue; // Core, theme and handles without a plugin source are not touched.
                    }
                    if ( 0 === strpos( $src, $own_url ) ) {
                        continue;
                    }
                    foreach ( $keep_urls as $keep_url ) {
                        if ( 0 === strpos( $src, $keep_url ) ) {
                            continue 2;
                        }
                    }
                    call_user_func( $list[1], $handle );
                }
            }
        };
        add_action( 'wp_head', $dequeue_other_plugin_handles, 7 );
        add_action( 'wp_footer', $dequeue_other_plugin_handles, 19 );
    }

    /**
     * Task 011 — get_post_metadata short-circuit filter. See maybe_render_preview()
     * above for why this exists instead of a change to display_lgx_logo_slider().
     * Only ever active during a request that already confirmed a valid preview
     * override exists for this exact post_id — inert otherwise.
     *
     * @since 6.2.0
     */
    public function filter_preview_meta_override( $value, $object_id, $meta_key, $single ) {
        if ( '_lgx_lsp_shortcodes_meta' !== $meta_key || (int) $object_id !== (int) $this->preview_meta_override_post_id ) {
            return $value;
        }
        // Always return the override wrapped in an array, for BOTH $single
        // states. Core's get_metadata_raw() does
        // `if ( $single && is_array( $check ) ) { return $check[0]; }`
        // (wp-includes/meta.php) — so a $single=true short-circuit whose value
        // is itself an array must be wrapped, or core unwraps element 0 of the
        // settings array instead of returning the settings array. This meta key
        // is always read with $single=true, so returning it unwrapped yielded a
        // missing [0], which get_metadata_default() then turned into '' — the
        // renderer received an empty string and bailed with "The showcase ID is
        // not valid." (Task 011 Phase 4; fixed in Phase 4b.)
        return array( $this->preview_meta_override );
    }

    /**
     * Task 011 Phase 1 — dedicated AJAX action for the real-time preview
     * (ADR-009 Decision A: "prefer a dedicated AJAX action" over extending
     * the GET preview endpoint with POST support, keeping that endpoint
     * clean/GET-only).
     *
     * Collects the unsaved field values the JS layer sends (Phase 2: the
     * whole builder form via `new FormData(...)`), merges them **in memory
     * only** over the post's saved meta, and stores the merged result in a
     * short-lived transient — never written to post meta, never persisted
     * beyond 60 seconds. maybe_render_preview() (above) picks this override
     * up on the very next GET request for the same post, which is exactly
     * what the cache-busted iframe src reload (ADR-009's 2026-07-27 Task 011
     * amendment) triggers moments later.
     *
     * Registered wp_ajax_ only — no wp_ajax_nopriv_ counterpart (the builder
     * itself is admin-only; see includes/class-logo-slider-wp.php).
     *
     * Nonce: reused from the existing `meta_lgx_lsp_shortcodes` save-form
     * nonce, read from its natural nested location
     * ($_POST['meta_lgx_lsp_shortcodes']['nonce']) rather than requiring a
     * second, separately-embedded nonce — since Phase 2's FormData collection
     * captures the whole builder form as-is, that's where this nonce already
     * lives. No new nonce action/field was introduced for this endpoint.
     *
     * @since 6.2.0
     */
    public function handle_preview_render_ajax() {

        $submitted = array();
        if ( isset( $_POST['meta_lgx_lsp_shortcodes'] ) && is_array( $_POST['meta_lgx_lsp_shortcodes'] ) ) {
            $submitted = wp_unslash( $_POST['meta_lgx_lsp_shortcodes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- sanitized field-by-field below, before use.
        }

        $nonce = isset( $submitted['nonce'] ) ? sanitize_text_field( $submitted['nonce'] ) : '';
        if ( ! wp_verify_nonce( $nonce, 'meta_lgx_lsp_shortcodes' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'logo-slider-wp' ) ), 403 );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id || 'lgx_lsp_shortcodes' !== get_post_type( $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid preview target.', 'logo-slider-wp' ) ), 404 );
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'logo-slider-wp' ) ), 403 );
        }

        $saved_meta = get_post_meta( $post_id, '_lgx_lsp_shortcodes_meta', true );
        $saved_meta = is_array( $saved_meta ) ? $saved_meta : array();

        unset( $submitted['nonce'] ); // form-security field, not a showcase meta key.

        // Submitted values win where present. This is correct as a plain
        // array_merge() for every text-like field — they always submit
        // something (even an empty string) as long as the field exists in
        // the DOM, which per ADR-005 it always does (hidden ≠ removed).
        // Toggle/checkbox fields are the one HTML form quirk that breaks a
        // plain merge: an UNCHECKED checkbox is omitted from form
        // submission entirely (standard HTML behaviour), so "toggle just
        // turned off" would otherwise silently keep the old saved "yes"
        // value. Resolved explicitly below for every field the registry
        // marks as type=switch, exactly like the real save handler already
        // does elsewhere in this plugin (isset($postData[...]) ? 'yes' : 'no') —
        // this is a correctness fix, not optional polish, since a preview
        // that can't show "I just turned this off" defeats the feature.
        $merged = array_merge( $saved_meta, $this->sanitize_preview_submission( $submitted ) );

        foreach ( $this->get_toggle_field_ids() as $toggle_id ) {
            $merged[ $toggle_id ] = isset( $submitted[ $toggle_id ] ) ? 'yes' : 'no';
        }

        // T-216 (Stage 2, S-30): the content source and the WooCommerce Brand keys
        // follow the save's own rules (the admin class hooks lgx_ls_preview_clean),
        // computed from the raw submitted values, before sanitize_text_field(), as
        // the save does. Only string or number values are laid over $merged. With
        // nothing hooked the filter returns array() and the preview is unchanged.
        $lgx_preview_clean = apply_filters( 'lgx_ls_preview_clean', array(), $submitted, $saved_meta );
        if ( is_array( $lgx_preview_clean ) ) {
            foreach ( $lgx_preview_clean as $lgx_clean_key => $lgx_clean_value ) {
                if ( is_string( $lgx_clean_key ) && ( is_string( $lgx_clean_value ) || is_int( $lgx_clean_value ) || is_float( $lgx_clean_value ) ) ) {
                    $merged[ $lgx_clean_key ] = $lgx_clean_value;
                }
            }
        }

        // Never written to post meta / the database — set_transient() only.
        // 60s comfortably covers the debounce window + the reload that
        // follows; if it expires unread, maybe_render_preview() simply
        // falls back to plain saved meta, same as before Task 011.
        set_transient( $this->get_preview_transient_key( $post_id ), $merged, 60 );

        wp_send_json_success( array( 'ready' => true ) );
    }

    /**
     * Task 011 — every registry field with type=switch, used by
     * handle_preview_render_ajax() to resolve "checkbox omitted from POST"
     * to 'no' rather than silently falling back to a stale saved 'yes'.
     * Reads config.php directly (plain data, no admin-only calls in it) —
     * safe from a front-end AJAX context. Result cached for the life of
     * the request; the registry doesn't change mid-request.
     *
     * @since 6.2.0
     * @return string[]
     */
    private function get_toggle_field_ids() {
        static $ids = null;
        if ( null !== $ids ) {
            return $ids;
        }
        $ids = array();

        $config_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/modules/showcase-builder/config/config.php';
        if ( ! file_exists( $config_path ) ) {
            return $ids;
        }
        $registry = include $config_path;
        if ( ! is_array( $registry ) ) {
            return $ids;
        }

        foreach ( $registry as $tab ) {
            if ( empty( $tab['groups'] ) || ! is_array( $tab['groups'] ) ) {
                continue;
            }
            foreach ( $tab['groups'] as $group ) {
                if ( empty( $group['fields'] ) || ! is_array( $group['fields'] ) ) {
                    continue;
                }
                foreach ( $group['fields'] as $field ) {
                    if ( isset( $field['type'], $field['id'] ) && 'switch' === $field['type'] ) {
                        $ids[] = $field['id'];
                    }
                }
            }
        }
        return $ids;
    }

    /**
     * Task 011 — recursively sanitize the submitted preview field values.
     * Lighter-touch than the real save handler's per-field-type validation
     * deliberately: these values are never persisted, and still pass
     * through the same output-escaping the real render path already applies
     * (esc_attr()/sanitize_hex_color()/etc. throughout general-style.php and
     * view-controller.php, unchanged). This pass exists so obviously
     * malformed input can't sit in a transient at all, not to duplicate the
     * save handler's full validation.
     *
     * @since 6.2.0
     */
    private function sanitize_preview_submission( $data ) {
        $sanitized = array();
        foreach ( $data as $key => $value ) {
            $key = sanitize_key( $key );
            if ( is_array( $value ) ) {
                $sanitized[ $key ] = $this->sanitize_preview_submission( $value );
            } else {
                $sanitized[ $key ] = sanitize_text_field( $value );
            }
        }
        return $sanitized;
    }

    /**
     * Task 011 — transient key for a post+user's in-progress preview
     * override. Scoped per-user as well as per-post so two admins with the
     * same showcase open can't clobber each other's in-progress preview.
     *
     * @since 6.2.0
     */
    private function get_preview_transient_key( $post_id ) {
        return 'lgx_preview_ovr_' . absint( $post_id ) . '_' . get_current_user_id();
    }




    /**
     *
     *  Version 3 End ***************************************************************************
     *
     */



}
}




