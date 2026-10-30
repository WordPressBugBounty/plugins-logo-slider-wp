<?php

/**
 *
 * @link              http://logichunt.com
 * @since             1.0.0
 * @package           Logo_Slider_WP
 *
 * @wordpress-plugin
 * Plugin Name:       Logo Slider WP
 * Plugin URI:        https://logichunt.com/product/wordpress-logo-slider/
 * Description:       Ultimate & Most Popular Responsive Logo Showcase Slider. Display Unlimited Client, Supporter, Partner, Sponsor, or Brand Logo with Infinite Slides Loop.
 * Version:           6.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            LogicHunt
 * Author URI:        http://logichunt.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       logo-slider-wp
 * Domain Path:       /languages
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*
*
logo-slider-wp is free version slug not needed now
*
*/

//defined( 'LGX_LS_WP_PLUGIN' ) or define( 'LGX_LS_WP_PLUGIN', 'logo-slider-wp-pro' );


//plugin definition specific constants
defined( 'LGX_LS_PLUGIN_VERSION' )        or define( 'LGX_LS_PLUGIN_VERSION', '6.1.0' );
defined( 'LGX_LS_PLUGIN_BASE' )           or define( 'LGX_LS_PLUGIN_BASE', plugin_basename( __FILE__ ) );
defined( 'LGX_LS_PLUGIN_ROOT_PATH' )      or define( 'LGX_LS_PLUGIN_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'LGX_LS_PLUGIN_ROOT_URL' )       or define( 'LGX_LS_PLUGIN_ROOT_URL', plugin_dir_url( __FILE__ ) );
defined( 'LGX_LS_PLUGIN_TEXT_DOMAIN')     or define( 'LGX_LS_PLUGIN_TEXT_DOMAIN', 'logo-slider-wp');



/**
 * Plugin edition flag.
 *
 * Single source of truth for determining whether this build is the
 * Pro or Free edition. This value is defined explicitly in each
 * edition's bootstrap file and must not be inferred from the plugin
 * slug, folder name, or file path.
 *
 * Pro:  true
 * Free: false
 */
defined( 'LGX_LS_IS_PRO' ) || define( 'LGX_LS_IS_PRO', false ); // true in Pro

if ( ! function_exists( 'lgx_ls_is_pro' ) ) {
    /**
     * Determine whether the current plugin build is the Pro edition.
     *
     * @return bool True for Pro, false for Free.
     */
    function lgx_ls_is_pro(): bool {
        return LGX_LS_IS_PRO;
    }
}

/**
 * Dashboard video tutorials readiness flag.
 *
 * The Dashboard's video tutorials block currently has no real video
 * content — flip to true once real YouTube URLs/durations are in place.
 */
defined( 'LGX_LS_VIDEOS_READY' ) || define( 'LGX_LS_VIDEOS_READY', false );

/**
 * Backward compatibility.
 *
 * Legacy code still references LGX_LS_PLUGIN_META_FIELD_PRO.
 * New code should use LGX_LS_IS_PRO or lgx_ls_is_pro().
 *
 * This constant should be removed once all legacy references have
 * been migrated.
 */
defined( 'LGX_LS_PLUGIN_META_FIELD_PRO' ) || define(
    'LGX_LS_PLUGIN_META_FIELD_PRO',
    LGX_LS_IS_PRO ? 'enabled' : 'disabled'
);




register_activation_hook( __FILE__, function ( $network_wide ) {

    $should_activate = lgx_ls_handle_edition_activation( plugin_basename( __FILE__ ), $network_wide );

    if ( ! $should_activate ) {
        // This edition's activation was cancelled due to a conflict —
        // skip the normal Activator logic below.
        return;
    }

    require_once plugin_dir_path( __FILE__ ) . 'includes/class-logo-slider-wp-activator.php';
    Logo_Slider_WP_Activator::activate();
} );

register_deactivation_hook( __FILE__, function () {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-logo-slider-wp-deactivator.php';
    Logo_Slider_WP_Deactivator::deactivate();
} );

add_action( 'admin_notices', 'lgx_ls_edition_conflict_notice' );
add_action( 'activated_plugin', 'lgx_ls_handle_free_activated_over_pro', 10, 1 );

if ( ! function_exists( 'lgx_ls_handle_edition_activation' ) ) {
    /**
     * Returns true if this edition's normal activation logic should proceed,
     * false if this edition's activation was just cancelled due to a conflict.
     *
     * For the Free-blocked case (Direction A), this only decides whether to
     * skip Logo_Slider_WP_Activator::activate() below. It deliberately does
     * NOT call deactivate_plugins() here: WordPress core fires this hook
     * (activate_{$plugin}, via register_activation_hook()) BEFORE adding the
     * just-activated plugin to the active_plugins option, so deactivate_plugins()
     * at this point would have nothing to remove yet. The actual removal +
     * notice happens in lgx_ls_handle_free_activated_over_pro(), hooked to
     * WordPress's 'activated_plugin', which core fires AFTER that option
     * update.
     */
    function lgx_ls_handle_edition_activation( $activated_base, $network_wide ) {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $free_base = 'logo-slider-wp/logo-slider-wp.php';
        $pro_base  = 'logo-slider-wp-pro/logo-slider-wp-pro.php';

        if ( $pro_base === $activated_base ) {
            // Pro was just activated — auto-deactivate Free if present.
            if ( is_plugin_active( $free_base ) ) {
                deactivate_plugins( $free_base );
                set_transient( 'lgx_ls_edition_notice', 'pro_activated', 60 );
            }
            return true;
        }

        if ( $free_base === $activated_base ) {
            // Free was just activated — block it if Pro is already active.
            // Only decides whether to skip this edition's own Activator
            // below. The deactivate-and-notify step happens on
            // 'activated_plugin' (see lgx_ls_handle_free_activated_over_pro),
            // once Free genuinely exists in active_plugins to remove.
            $pro_active = is_plugin_active( $pro_base ) || is_plugin_active_for_network( $pro_base );
            if ( $pro_active ) {
                return false;
            }
            return true;
        }

        return true;
    }
}

if ( ! function_exists( 'lgx_ls_handle_free_activated_over_pro' ) ) {
    /**
     * Fires on WordPress core's 'activated_plugin' hook, which runs AFTER
     * core adds the just-activated plugin to the active_plugins option
     * (unlike activate_{$plugin}, which runs before). This is a global
     * hook — it fires once for every plugin activation on the whole site,
     * not just this plugin's — so it must check $plugin itself before
     * doing anything.
     */
    function lgx_ls_handle_free_activated_over_pro( $plugin ) {

        $free_base = 'logo-slider-wp/logo-slider-wp.php';

        if ( $free_base !== $plugin ) {
            return;
        }

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $pro_base   = 'logo-slider-wp-pro/logo-slider-wp-pro.php';
        $pro_active = is_plugin_active( $pro_base ) || is_plugin_active_for_network( $pro_base );

        if ( $pro_active ) {
            deactivate_plugins( $free_base );
            set_transient( 'lgx_ls_edition_notice', 'pro_blocks_free', 60 );
        }
    }
}

if ( ! function_exists( 'lgx_ls_edition_conflict_notice' ) ) {
    function lgx_ls_edition_conflict_notice() {
        $notice = get_transient( 'lgx_ls_edition_notice' );
        if ( ! $notice ) {
            return;
        }
        delete_transient( 'lgx_ls_edition_notice' );

        if ( 'pro_activated' === $notice ) {
            $class   = 'notice notice-success is-dismissible';
            $message = __( 'Logo Slider Pro has been activated. The Free edition has been automatically deactivated because only one edition can be active at a time.', 'logo-slider-wp' );
        } elseif ( 'pro_blocks_free' === $notice ) {
            $class   = 'notice notice-warning is-dismissible';
            $message = __( 'Logo Slider Pro is already active. To switch to the Free edition, first deactivate Logo Slider Pro, then activate Logo Slider Free.', 'logo-slider-wp' );
        } else {
            return;
        }

        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr( $class ),
            wp_kses_post( $message )
        );
    }
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-logo-slider-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( ! function_exists( 'run_logo_slider_wp' ) ) {
    function run_logo_slider_wp() {

        static $ran = false;

        if ( $ran ) {
            return;
        }

        $ran = true;

        $plugin = new Logo_Slider_WP();
        $plugin->run();
    }
}

run_logo_slider_wp();
