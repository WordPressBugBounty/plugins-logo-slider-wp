<?php
/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    logosliderwpcarousel
 * @subpackage logosliderwpcarousel/admin/partials
 */
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php esc_html_e('Logo Slider WP: Usage & Help', 'logo-slider-wp'); ?></h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">

                    <?php
                    /*
                     * Add Header File
                     */
                    include_once plugin_dir_path( __FILE__ ) . '/shortcode_meta_display/__meta_fields_lsp_shortcodes_header.php';
                    ?>

                    <div class="postbox">
                        <div class="inside lgx-settings-inside">

                            <!-- Get Pro block — TOP -->
                            <div style="margin-left: -5%;">
                                <?php include plugin_dir_path( __FILE__ ) . '/shortcode_meta_display/__meta_fields_lsp_shortcodes_get_pro.php'; ?>
                            </div>

                            <hr>

                            <!-- =============================================
                                 SECTION 1 — QUICK START
                            ============================================= -->
                            <h3 class="clear"><?php esc_html_e('Quick Start Guide', 'logo-slider-wp'); ?></h3>

                            <p
                                style="margin: 15px 0; background:#f0f9ff; border-left:4px solid #2196F3; padding:12px 16px; border-radius:4px;">
                                <strong><?php esc_html_e('Good news: Logo Slider WP displays every logo you add — with no front-end limit in the free version. Add 5 logos or 500, all of them appear. No upgrade required.', 'logo-slider-wp'); ?></strong>
                            </p>

                            <h4 style="margin: 20px 0 12px 0;">
                                <?php esc_html_e('Follow these steps to create your first logo showcase:', 'logo-slider-wp'); ?>
                            </h4>

                            <ol style="line-height:2;">
                                <li>
                                    <strong><?php esc_html_e('Add your logos', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Go to Logo Slider → Add New Logo. For each logo, provide: brand image, company name, website URL, description, and tooltip text.', 'logo-slider-wp'); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Organise with categories (optional but recommended)', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Go to Logo Slider → Categories to create category groups (e.g. "Clients", "Partners", "Sponsors"). Assign each logo to a category. This lets you display different logo groups on different pages using a single filter setting.', 'logo-slider-wp'); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Create a showcase', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Go to Logo Slider → Shortcode Generator and click Add New.', 'logo-slider-wp'); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Choose your showcase type', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Select from three types:', 'logo-slider-wp'); ?>
                                    <ul style="margin:6px 0 6px 20px; list-style:disc;">
                                        <li><strong><?php esc_html_e('Carousel', 'logo-slider-wp'); ?></strong> —
                                            <?php esc_html_e('Smooth sliding showcase with autoplay, infinite loop, swipe, and navigation. Enable Ticker Mode inside the Carousel tab for a continuously scrolling strip.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><strong><?php esc_html_e('Grid', 'logo-slider-wp'); ?></strong> —
                                            <?php esc_html_e('Static responsive grid where all logos are visible at once. Best for client walls and sponsor pages.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><strong><?php esc_html_e('Flexbox (New)', 'logo-slider-wp'); ?></strong> —
                                            <?php esc_html_e('Fluid CSS flexbox layout. Logos wrap naturally without rigid columns — ideal for mixed-width brand marks.', 'logo-slider-wp'); ?>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Configure your settings', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Each showcase type reveals its own dedicated settings tab (Carousel / Grid / Flexbox). Work through the tabs: layout, styling, responsive breakpoints, tooltip, header, and preloader. Every tab shows a description beneath each field to explain what it does.', 'logo-slider-wp'); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Set your responsive columns', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— Open the Responsive tab and set how many logos appear on large desktop, desktop, tablet, and mobile independently.', 'logo-slider-wp'); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e('Copy and embed the shortcode', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('— The generated shortcode appears at the top of the settings panel. Copy it and paste it into any page, post, widget area, or page builder.', 'logo-slider-wp'); ?>
                                    <ul style="margin:6px 0 6px 20px; list-style:disc;">
                                        <li><?php esc_html_e('Elementor: add a Shortcode widget and paste inside it.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><?php esc_html_e('Gutenberg: add a Shortcode block and paste inside it.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><?php esc_html_e('Divi / WPBakery / Beaver Builder: use the built-in shortcode module.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><?php esc_html_e('Classic Editor / Widget: paste the shortcode directly.', 'logo-slider-wp'); ?>
                                        </li>
                                        <li><?php esc_html_e('PHP theme file: ', 'logo-slider-wp'); ?><code>&lt;?php echo do_shortcode('[lgxlogoslider id="39"]'); ?&gt;</code>
                                        </li>
                                    </ul>
                                </li>
                            </ol>

                            <ul
                                style="margin: 10px 0 20px 0; background:#fffbea; border-left:4px solid #f6c90e; padding:12px 16px; border-radius:4px; list-style:none;">
                                <li>💡 <strong><?php esc_html_e('Tip:', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('Use logos with consistent dimensions (same height and width) for the most visually balanced result.', 'logo-slider-wp'); ?>
                                </li>
                                <li style="margin-top:6px;">💡
                                    <strong><?php esc_html_e('Tip:', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('You can place multiple showcases on the same page — each shortcode is fully independent with its own settings.', 'logo-slider-wp'); ?>
                                </li>
                                <li style="margin-top:6px;">💡
                                    <strong><?php esc_html_e('Tip:', 'logo-slider-wp'); ?></strong>
                                    <?php esc_html_e('Set Item Limit to 0 in Query Settings to display all logos with no cap.', 'logo-slider-wp'); ?>
                                </li>
                            </ul>

                            <p style="margin-top: 10px;">
                                <?php esc_html_e('For detailed instructions, see the full documentation:', 'logo-slider-wp'); ?>
                                <a class="button button-primary" href="https://docs.logichunt.com/logo-slider-wp"
                                    target="_blank">
                                    <?php esc_html_e('View Documentation', 'logo-slider-wp'); ?>
                                </a>
                                &nbsp;
                                <a class="button" href="https://demo.logichunt.com/logo-slider-wp/" target="_blank">
                                    <?php esc_html_e('Live Demo', 'logo-slider-wp'); ?>
                                </a>
                            </p>

                            <br>
                            <hr>

                            <!-- =============================================
                                 SECTION 2 — SHOWCASE TYPES EXPLAINED
                            ============================================= -->
                            <h3 class="clear"><?php esc_html_e('Three Showcase Types Explained', 'logo-slider-wp'); ?>
                            </h3>

                            <table style="width:100%; border-collapse:collapse; margin:16px 0; font-size:13px;">
                                <thead>
                                    <tr style="background:#f1f1f1;">
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Type', 'logo-slider-wp'); ?></th>
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Best for', 'logo-slider-wp'); ?></th>
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Key settings tab', 'logo-slider-wp'); ?></th>
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Ticker mode', 'logo-slider-wp'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><strong>🎠
                                                <?php esc_html_e('Carousel', 'logo-slider-wp'); ?></strong></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Homepage strips, partner showcases, sliding brand rows', 'logo-slider-wp'); ?>
                                        </td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <strong><?php esc_html_e('Carousel tab', 'logo-slider-wp'); ?></strong></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">✅
                                            <?php esc_html_e('Enable inside Carousel tab', 'logo-slider-wp'); ?></td>
                                    </tr>
                                    <tr style="background:#fafafa;">
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><strong>🖼️
                                                <?php esc_html_e('Grid', 'logo-slider-wp'); ?></strong></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Client walls, sponsor pages, all-logos-visible layouts', 'logo-slider-wp'); ?>
                                        </td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <strong><?php esc_html_e('Grid tab', 'logo-slider-wp'); ?></strong></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">—</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><strong>⚡
                                                <?php esc_html_e('Flexbox', 'logo-slider-wp'); ?></strong> <span
                                                style="background:#2196F3;color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;margin-left:4px;"><?php esc_html_e('New', 'logo-slider-wp'); ?></span>
                                        </td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Mixed-width logos, variable brand mark sizes, fluid wrapping layouts', 'logo-slider-wp'); ?>
                                        </td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <strong><?php esc_html_e('Flexbox tab', 'logo-slider-wp'); ?></strong></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">—</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p style="font-size:13px; color:#555;">
                                <?php esc_html_e('Note: Ticker is not a separate showcase type. It is a mode inside the Carousel type. Select Carousel, then enable Ticker Mode in the Carousel settings tab for a continuously scrolling strip.', 'logo-slider-wp'); ?>
                            </p>

                            <br>
                            <hr>

                            <!-- =============================================
                                 SECTION 3 — UPGRADE TO PRO (NO DATA LOSS)
                            ============================================= -->
                            <h3 class="clear"><?php esc_html_e('Upgrading to Pro — No Data Loss', 'logo-slider-wp'); ?>
                            </h3>

                            <p
                                style="margin: 15px 0; background:#f0fff4; border-left:4px solid #48bb78; padding:12px 16px; border-radius:4px;">
                                <strong><?php esc_html_e('All your logos, categories, and showcase shortcodes carry over to Pro automatically. Nothing is lost. Your existing shortcodes continue to work on your live site without any changes.', 'logo-slider-wp'); ?></strong>
                            </p>

                            <h4 style="margin: 20px 0 12px 0;">
                                <?php esc_html_e('Follow these steps to upgrade:', 'logo-slider-wp'); ?>
                            </h4>

                            <ol style="line-height:2;">
                                <li>
                                    <?php
                                    printf(
                                        esc_html__('Purchase Logo Slider Pro from: %s', 'logo-slider-wp'),
                                        '<a href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank"><strong>' . esc_html__('logichunt.com/product/wordpress-logo-slider', 'logo-slider-wp') . '</strong></a>'
                                    );
                                    ?>
                                </li>
                                <li><?php esc_html_e('After purchase, go to My Account → Dashboard → Downloads on our website, or use the download link in your confirmation email.', 'logo-slider-wp'); ?>
                                </li>
                                <li><?php esc_html_e('Download the Pro plugin ZIP file.', 'logo-slider-wp'); ?></li>
                                <li><?php esc_html_e('In your WordPress admin, go to Plugins → Add New → Upload Plugin, select the Pro ZIP file, and click Install Now.', 'logo-slider-wp'); ?>
                                </li>
                                <li><?php esc_html_e('Activate the Pro version. The free version will be automatically deactivated — no manual removal needed.', 'logo-slider-wp'); ?>
                                </li>
                                <li><?php esc_html_e('All your logos, categories, and existing shortcodes are preserved. Your live pages continue to display correctly.', 'logo-slider-wp'); ?>
                                </li>
                                <li><?php esc_html_e('You now have access to all Pro features: 100+ Animate.css hover animations, Coverflow 3D transition, grayscale effects, hover overlays, RTL direction, advanced typography, background images, branded preloader, and priority support.', 'logo-slider-wp'); ?>
                                </li>
                            </ol>

                            <p style="margin-top:10px;">
                                <a class="button button-primary"
                                    href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank">
                                    <?php esc_html_e('Get Pro Now', 'logo-slider-wp'); ?>
                                </a>
                            </p>

                            <br>
                            <hr>

                            <!-- =============================================
                                 SECTION 4 — SUPPORT
                            ============================================= -->
                            <h3 class="clear"><?php esc_html_e('Get Support', 'logo-slider-wp'); ?></h3>

                            <p style="margin-bottom:12px;">
                                <?php esc_html_e('Need help? Visit our support page at logichunt.com — our core developer team responds to every question directly. For the fastest response, use the channels below:', 'logo-slider-wp'); ?>
                            </p>

                            <table style="width:100%; border-collapse:collapse; margin:0 0 16px 0; font-size:13px;">
                                <thead>
                                    <tr style="background:#f1f1f1;">
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Support channel', 'logo-slider-wp'); ?></th>
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Who can use it', 'logo-slider-wp'); ?></th>
                                        <th style="padding:10px 14px; text-align:left; border:1px solid #ddd;">
                                            <?php esc_html_e('Link', 'logo-slider-wp'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background:#fafafa;">
                                        <td style="padding:10px 14px; border:1px solid #ddd;">📧
                                            <?php esc_html_e('Priority email support', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Pro users', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><a
                                                href="https://logichunt.com/support/"
                                                target="_blank"><?php esc_html_e('Submit ticket', 'logo-slider-wp'); ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">📞
                                            <?php esc_html_e('Phone support', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Pro users', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><a
                                                href="https://logichunt.com/contact-us/"
                                                target="_blank"><?php esc_html_e('Contact us', 'logo-slider-wp'); ?></a>
                                        </td>
                                    </tr>
                                    <tr style="background:#fafafa;">
                                        <td style="padding:10px 14px; border:1px solid #ddd;">💬
                                            <?php esc_html_e('WhatsApp support', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Pro users', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><a
                                                href="https://logichunt.com/contact-us/"
                                                target="_blank"><?php esc_html_e('Contact us', 'logo-slider-wp'); ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">🛠️
                                            <?php esc_html_e('Free installation help', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;">
                                            <?php esc_html_e('Pro users', 'logo-slider-wp'); ?></td>
                                        <td style="padding:10px 14px; border:1px solid #ddd;"><a
                                                href="https://logichunt.com/support/"
                                                target="_blank"><?php esc_html_e('Request help', 'logo-slider-wp'); ?></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <p>
                                <a class="button button-primary" href="https://logichunt.com/support/" target="_blank">
                                    <?php esc_html_e('Get Support', 'logo-slider-wp'); ?>
                                </a>
                                &nbsp;
                                <a class="button" href="https://logichunt.com/contact-us/" target="_blank">
                                    <?php esc_html_e('Contact Us', 'logo-slider-wp'); ?>
                                </a>
                            </p>

                            <br>
                            <hr>

                            <!-- Get Pro block — BOTTOM -->
                            <div style="margin-left: -5%;">
                                <?php include plugin_dir_path( __FILE__ ) . '/shortcode_meta_display/__meta_fields_lsp_shortcodes_get_pro.php'; ?>
                            </div>

                            <hr>
                            <br>

                        </div><!-- .inside -->
                    </div><!-- .postbox -->

                </div><!-- .meta-box-sortables .ui-sortable -->
            </div><!-- #post-body-content -->

            <?php include('sidebar.php'); ?>

        </div><!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div><!-- #poststuff -->

</div><!-- .wrap -->