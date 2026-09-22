<?php
/**
 * Showcase Builder Settings Registry
 *
 * This file defines the complete declarative Settings Matrix for the Showcase Builder.
 * It strictly separates configuration from presentation.
 * 
 * Fields defined here must match existing meta keys perfectly to guarantee 
 * backward compatibility with the legacy save handlers and shortcodes.
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

$config = array(

    // ==========================================================================
    // Task 014/Commit 4b: the 'general_settings' tab (General) is removed —
    // its one field, lgx_lswp_content_source, was already commented "not
    // read for rendering, kept for registry bookkeeping only" (RB-2/B52
    // removed its actual UI). Confirmed nothing else in the registry or
    // renderer reads this key before removing the entry. Content is now the
    // first/default tab — see builder-wrapper.php and showcase-builder.js
    // for the corresponding fallback-string updates.
    // ==========================================================================

    // ==========================================================================
    // 1b. FORMER CONTENT TAB — General Settings
    // (007R-001: These fields remain in the registry for save-handler backward
    //  compatibility, now surfaced under Source Settings or a future dedicated tab.)
    // NOTE: The 'content' key below is intentionally NOT part of the tab navigation.
    //       These fields are referenced by their meta keys in legacy save handlers.
    //       They are kept here as a record but are not surfaced in the new navigation.
    // --------------------------------------------------------------------------
    // DEVELOPER NOTE: The General Settings fields (lgx_brand_name_en, lgx_logo_height, etc.)
    // were previously under the 'content' tab. They now live under 'source_settings'
    // within the 'content_display' group for continued user access.
    // ==========================================================================

    // ==========================================================================
    // 2. SOURCE SETTINGS TAB (007R-001: Replaces the former 'query' tab)
    // Configures the selected Content Source.
    // ==========================================================================
    'source_settings' => array(
        'id'    => 'source_settings',
        'label' => __( 'Content', 'logo-slider-wp' ),
        'icon'  => 'dashicons-database',
        'groups' => array(
            'content_link_settings' => array(
                'label'    => __( 'Essential', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_brand_name_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_brand_name_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Brand Name', 'logo-slider-wp' ),
                        'desc'         => __( "Show each logo's company name as a caption below it.", 'logo-slider-wp' ),
                        'default'      => 'no',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_brand_desc_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_brand_desc_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Brand Description', 'logo-slider-wp' ),
                        'desc'         => __( "Show each logo's short description text below it.", 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'default'      => 'no',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_company_url_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_company_url_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Company URL', 'logo-slider-wp' ),
                        'desc'         => __( "Make each logo clickable, linking to its Company URL when one is set.", 'logo-slider-wp' ),
                        // 'yes' since 2026-09-06: a new showcase opens with
                        // Company URL already on, so logos that carry a URL are
                        // clickable without the user having to find this toggle.
                        // Existing showcases are unaffected — the renderer reads
                        // this key straight from saved meta with no config
                        // fallback (public/partials/template/_item.php:10), and
                        // the presence-based save handler has written an explicit
                        // 'yes'/'no' for every showcase ever saved.
                        'default'      => 'yes',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_target_type',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_target_type]',
                        'type'         => 'select',
                        'label'        => __( 'URL Target Type', 'logo-slider-wp' ),
                        'desc'         => __( 'Choose whether logo links open in the same tab or a new one.', 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'options'      => array(
                            '_self'  => __( 'Same Tab', 'logo-slider-wp' ),
                            '_blank' => __( 'New Tab', 'logo-slider-wp' ),
                        ),
                        'default'      => '_self',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_nofollow_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_nofollow_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Add Nofollow to URL', 'logo-slider-wp' ),
                        'desc'         => __( 'Adds rel="nofollow" to logo links, telling search engines not to follow them.', 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'default'      => 'no',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                ),
            ),
            'source_dependent' => array(
                'label'    => __( 'Logo Source', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_from_category',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_from_category]',
                        'type'         => 'checkbox_list',
                        'label'        => __( 'Filter by Category', 'logo-slider-wp' ),
                        // Free since 2026-09-07 (ADR-016) -- reclassified from Pro,
                        // reversing part of ADR-012. See that ADR for the reasoning
                        // (a major competitor now offers category filtering free) and
                        // docs/architecture/invariants.md #9 for why this field's own
                        // retain-on-reject dependency is retired, not the rule itself.
                        'options'      => array( 'all' => __( 'All', 'logo-slider-wp' ) ), // Overwritten with real terms by builder-wrapper.php (B53)
                        'default'      => 'all',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                ),
            ),
            'query_settings' => array(
                'label'    => __( 'Query', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_item_limit',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_limit]',
                        'type'         => 'number',
                        'label'        => __( 'Item Limit', 'logo-slider-wp' ),
                        'desc'         => __( 'Maximum number of logos to display. Use -1 (or 0) for unlimited.', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'default'      => -1,
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_item_sort_order_by',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_sort_order_by]',
                        'type'         => 'select',
                        'label'        => __( 'Order By', 'logo-slider-wp' ),
                        'desc'         => __( 'Choose how logos are sorted: your custom Drag & Drop order, alphabetically by Title, by Date added, or Random.', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'options'      => array(
                            'menu_order' => __( 'Drag & Drop', 'logo-slider-wp' ),
                            'title'      => __( 'Title', 'logo-slider-wp' ),
                            'date'       => __( 'Date', 'logo-slider-wp' ),
                            'rand'       => __( 'Random', 'logo-slider-wp' ),
                        ),
                        'default'      => 'menu_order',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_item_sort_order',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_sort_order]',
                        'type'         => 'select',
                        'label'        => __( 'Ordering', 'logo-slider-wp' ),
                        'desc'         => __( 'Sort direction for the chosen Order By method. Has no effect when Order By is set to Random.', 'logo-slider-wp' ),
                        'options'      => array(
                            'ASC'  => __( 'Ascending', 'logo-slider-wp' ),
                            'DESC' => __( 'Descending', 'logo-slider-wp' ),
                        ),
                        'default'      => 'ASC',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                ),
            ),
            'logo_settings' => array(
                'label'    => __( 'Logo Image', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    // lgx_logo_max_height / lgx_logo_max_width: introduced
                    // mid-development (5427caf), merged into compound fields
                    // (31667b6), then un-merged back into 4 independent fields
                    // (this task's predecessor). Removed entirely before
                    // 6.0.0 shipped -- confirmed never present in any real
                    // shipped release (see docs/architecture/invariants.md
                    // #6). No trace of either key remains anywhere in this
                    // registry, the save handler, or the frontend renderers.
                    //
                    // Un-merge (predecessor task): the four fields below were briefly
                    // two group2SelectText compound fields (31667b6); restored
                    // to four fully independent standalone fields per Owner
                    // decision. Order matters -- Height Type, Height Value,
                    // Width Type, Width Value render in exactly this sequence
                    // with nothing interleaved (confirmed: render_group() in
                    // class-logo-slider-wp-showcase-renderer.php is a plain
                    // foreach over this array, no sorting/reordering anywhere
                    // in the render path).
                    array(
                        'id'           => 'lgx_logo_height_property',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_height_property]',
                        'type'         => 'select',
                        'layout'       => 'modern',
                        'label'        => __( 'Logo Height Type', 'logo-slider-wp' ),
                        'desc'         => __( 'Which CSS height property this logo image uses.', 'logo-slider-wp' ),
                        // No 'status' key -- fully Free. No independent
                        // justification for Pro-gating Height was ever found
                        // in this project's history (pure pattern-mirroring
                        // from Width); Height also sits on the default,
                        // most-visited tab, where a visibly-free control
                        // improves the free-tier impression.
                        'options'      => array(
                            'max-height' => __( 'Max Height', 'logo-slider-wp' ),
                            'height'     => __( 'Height', 'logo-slider-wp' ),
                            'min-height' => __( 'Min Height', 'logo-slider-wp' ),
                        ),
                        'default'      => 'max-height',
                        'value_defaults_by_option' => array( // drives the Type-follows-Value JS
                            'max-height' => 'none',
                            'height'     => 'auto',
                            'min-height' => '0',
                        ),
                        'paired_value_field_id' => 'lgx_logo_height',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_logo_height',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_height]',
                        'type'         => 'text',
                        'label'        => __( 'Set Logo Height', 'logo-slider-wp' ),
                        'desc'         => __( 'Height value, e.g. 100px, 10rem, or auto — must match the selected Height Type.', 'logo-slider-wp' ),
                        // No 'status' key -- fully Free, unchanged from 31667b6.
                        'default'      => 'none', // corrected -- max-height does not accept 'auto' (see f0b062a's finding)
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_logo_width_property',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_width_property]',
                        'type'         => 'select',
                        'layout'       => 'modern',
                        'label'        => __( 'Logo Width Type', 'logo-slider-wp' ),
                        'desc'         => __( 'Which CSS width property this logo image uses.', 'logo-slider-wp' ),
                        'status'       => 'pro', // Model A -- see free_options below; the field
                                                  // itself is not locked, only Width/Min Width are.
                        'free_options' => array( 'max-width' ), // unconditionally Free, matches lgx_flexbox_justify_content's pattern
                        'options'      => array(
                            'max-width' => __( 'Max Width', 'logo-slider-wp' ),
                            'width'     => __( 'Width', 'logo-slider-wp' ),
                            'min-width' => __( 'Min Width', 'logo-slider-wp' ),
                        ),
                        'default'      => 'max-width',
                        'value_defaults_by_option' => array(
                            'max-width' => '100%',
                            'width'     => 'auto',
                            'min-width' => '0',
                        ),
                        'paired_value_field_id' => 'lgx_logo_width',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_logo_width',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_width]',
                        'type'         => 'text',
                        'label'        => __( 'Set Logo Width', 'logo-slider-wp' ),
                        'desc'         => __( 'Width value, e.g. 100px or 100% — must match the selected Width Type. Recommended: 100%.', 'logo-slider-wp' ),
                        // No 'status' key -- fully Free, unchanged from 31667b6.
                        'default'      => '100%', // unchanged, already correct
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_logo_position',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_position]',
                        'type'         => 'select',
                        'label'        => __( 'Logo Position', 'logo-slider-wp' ),
                        'desc'         => __( 'Where the logo image sits relative to the brand name and description.', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'options'      => array(
                            'top'    => __( 'Top', 'logo-slider-wp' ),
                            'left'   => __( 'Left', 'logo-slider-wp' ),
                            'right'  => __( 'Right', 'logo-slider-wp' ),
                            'bottom' => __( 'Bottom', 'logo-slider-wp' ),
                        ),
                        'default'      => 'top',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_logo_info_align',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_logo_info_align]',
                        'type'         => 'select',
                        'label'        => __( 'Logo Info Alignment', 'logo-slider-wp' ),
                        'desc'         => __( 'Text alignment for the brand name and description, independent of where the logo image sits.', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'options'      => array(
                            'center' => __( 'Center', 'logo-slider-wp' ),
                            'left'   => __( 'Left', 'logo-slider-wp' ),
                            'right'  => __( 'Right', 'logo-slider-wp' ),
                        ),
                        'default'      => 'center',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                ),
            ),
        ),
    ),

    // ==========================================================================
    // 3. LAYOUT SETTINGS TAB (007R-001: Replaces the former 'layout' tab)
    // Configures the selected Layout type.
    // The Showcase Type selector has moved to the 'content_layout' tab.
    // ==========================================================================
    'layout_settings' => array(
        'id'    => 'layout_settings',
        'label' => __( 'Layout', 'logo-slider-wp' ),
        'icon'  => 'dashicons-layout',
        'groups' => array(
            'carousel_general' => array(
                'label'    => __( 'Playback', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_carousel_autoplay_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_autoplay_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Autoplay', 'logo-slider-wp' ),
                        'default'      => 'yes',
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label' => __( 'Ticker Mode', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc' => __( 'Ticker Mode automatically uses continuous, non-stop scrolling (freeMode) with no pause between logos. This behavior is required for Ticker Mode and cannot be changed.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_ticker_en]',
        'id' => 'lgx_carousel_ticker_en',
        'status'  => 'pro',
        'default' => 'no'

                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label' => __( 'Ticker Pause Control', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc' => __( 'Adds a visible, keyboard- and touch-operable button so visitors can pause or resume the ticker\'s continuous scroll (WCAG 2.2.2). Only applies while Ticker Mode is on.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_ticker_pause_control_en]',
        'id' => 'lgx_carousel_ticker_pause_control_en',
        'default' => 'yes'

                    ),
                    array(
                        'id'           => 'lgx_carousel_autoplay_delay',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_autoplay_delay]',
                        'type'         => 'number',
                        'label'        => __( 'Autoplay Delay (ms)', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'default'      => 1500,
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_carousel_transition_speed',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_transition_speed]',
                        'type'         => 'number',
                        'label'        => __( 'Transition Speed (ms)', 'logo-slider-wp' ),
                        'default'      => 450,
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_carousel_infinite_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_infinite_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Infinite Loop', 'logo-slider-wp' ),
                        'default'      => 'yes',
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Pause on Hover', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'    => __( 'When enabled autoplay will be paused on mouse enter over Swiper container. ', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_pause_mouse_enter_en]',
        'id'      => 'lgx_carousel_pause_mouse_enter_en',
        //'status' => 'pro',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Sliding Effect', 'logo-slider-wp' ),
        'desc'  => __( 'Slide transition effect will apply as selected.', 'logo-slider-wp' ),
        'name'  => 'meta_lgx_lsp_shortcodes[lgx_carousel_transition_effect]',
        'id'    => 'lgx_carousel_transition_effect',
        'default'   => 'slide',
        'status'  => 'pro',
        'options'   => array(
            'slide' => __( 'Slide', 'logo-slider-wp' ),
            'coverflow' => __( 'Coverflow', 'logo-slider-wp' )
        )
    
                    ),
                ),
            ),
            'carousel_interaction' => array(
                'label'    => __( 'Touch & Mouse Interaction', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Touch Swipe', 'logo-slider-wp' ),
        'desc'    => __( 'Allows changing slides with a touch swipe.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_allow_touch_move_en]',
        'id'      => 'lgx_carousel_allow_touch_move_en',
        'default' => 'yes'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Mouse Wheel', 'logo-slider-wp' ),
        'desc'    => __( 'Enables navigation through slides using mouse wheel.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_mouse_wheel_en]',
        'status'  => 'pro',
        'id'      => 'lgx_carousel_mouse_wheel_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Mouse Draggable', 'logo-slider-wp' ),
        'desc'    => __( 'Click and drag with the mouse to change slides, like a touch swipe.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_simulate_touch_en]',
        'id'      => 'lgx_carousel_simulate_touch_en',
        'default' => 'yes'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Grab Cursor', 'logo-slider-wp' ),
        'desc'    => __( 'Changes the cursor to a hand icon when hovering over an item.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_grab_cursor_en]',
        'id'      => 'lgx_carousel_grab_cursor_en',
        'status'  => 'pro',
        'default' => 'no'

                    ),
                ),
            ),
            'carousel_slide_behaviour' => array(
                'label'    => __( 'Slide Behaviour', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Space Between', 'logo-slider-wp' ),
        'desc'      => __( 'Pixel spacing between slides. Recommended: 30.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_space_between]',
        'id'        => 'lgx_carousel_space_between',
        'default'   => 30
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Item Image Vertical Align', 'logo-slider-wp' ),
        'desc' => __( 'Vertical alignment of items. Useful when image sizes vary.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_item_vertical_align]',
        'id' => 'lgx_carousel_item_vertical_align',
        'status' => 'pro',
        'default'   => 'mid',
        'options'   => array(
            'mid' => __( 'Vertically Middle', 'logo-slider-wp' ),
            'top' => __( 'Top', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Auto Height', 'logo-slider-wp' ),
        'desc'    => __( 'If the option is mark as checked, then auto height support will be enabled.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_auto_height_en]',
        'id'      => 'lgx_carousel_auto_height_en',
        'status'  => 'pro',
        'default' => 'no'

                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'RTL Support', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'    => __( 'If the option is mark as checked, then Right to Left (rtl) support will be enabled.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_rtl_en]',
        'id'      => 'lgx_carousel_rtl_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label' => __( 'Lazy load', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc' => __( 'If the option is enabled, images lazy loading will be enabled with default settings', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_lazy_load_en]',
        'id' => 'lgx_carousel_lazy_load_en',
        'status'  => 'pro',
        'default' => 'no'

                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Lazy Spinner Type', 'logo-slider-wp' ),
        'desc' => __( 'Select Lazy Load Spinner color type for animated preloader spinner', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_lazy_spinner_color]',
        'id' => 'lgx_carousel_lazy_spinner_color',
        'default'   => 'white',
        'status'  => 'pro',
        'options'   => array(
            'white' => __( 'White', 'logo-slider-wp' ),
            'blue' => __( 'Blue', 'logo-slider-wp' ),
        )
    
                    ),
                ),
            ),
            'carousel_nav_content' => array(
                'label'    => __( 'Navigation', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_carousel_nav_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Navigation Arrows', 'logo-slider-wp' ),
                        'default'      => 'yes',
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Nav Button Type', 'logo-slider-wp' ),
        'desc' => __( 'Select content type for navigation button.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_type]',
        'id' => 'lgx_carousel_nav_btn_type',
        'default'   => 'icon',
        'status'  => 'pro',
        'options'   => array(
            'icon' => __( 'Icon', 'logo-slider-wp' ),
            'text' => __( 'Text', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Select Icon', 'logo-slider-wp' ),
        'desc' => __( 'Select content type for navigation button.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_icon]',
        'id' => 'lgx_carousel_nav_icon',
        'default'   => 'angle',
        'options'   => array(
            'angle' => __( 'Angle', 'logo-slider-wp' ),
            'angle-double' => __( 'Angle Double', 'logo-slider-wp' ),
            'arrow' => __( 'Arrow', 'logo-slider-wp' ),
            'arrow-circle-o' => __( 'Arrow Circle - O', 'logo-slider-wp' ),
            'arrow-circle' => __( 'Arrow Circle', 'logo-slider-wp' ),
            'long-arrow' => __( 'Long  Arrow', 'logo-slider-wp' ),
            'caret' => __( 'Caret', 'logo-slider-wp' ),
            'chevron' => __( 'Chevron', 'logo-slider-wp' ),
            'chevron-circle' => __( 'Chevron Circle', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Left Nav Text', 'logo-slider-wp' ),
        'desc'      => __( 'Custom text for the previous button. Requires Nav Button Type set to Text.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_text_prev]',
        'id'        => 'lgx_carousel_nav_btn_text_prev',
        'status'  => 'pro',
        'default'   => 'Prev'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Right Nav Text', 'logo-slider-wp' ),
        'desc'      => __( 'Custom text for the next button. Requires Nav Button Type set to Text.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_text_next]',
        'id'        => 'lgx_carousel_nav_btn_text_next',
        'status'  => 'pro',
        'default'   => 'Next'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Yes', 'logo-slider-wp' ),
        'no_label' => __( 'No', 'logo-slider-wp' ),
        'label'   => __( 'Visible On Over', 'logo-slider-wp' ),
        'desc'    => __( 'If the options is mark as checked then navigation will show only on hover.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_hover_en]',
        'status'  => 'pro',
        'id'      => 'lgx_carousel_nav_hover_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Nav Hide on Mobile', 'logo-slider-wp' ),
        'yes_label' => __( 'Yes', 'logo-slider-wp' ),
        'no_label' => __( 'No', 'logo-slider-wp' ),
        'desc'    => __( 'If the options is mark as checked then navigation will hide on mobile view (767px).', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_mobile_en]',
        'id'      => 'lgx_carousel_nav_mobile_en',
        'status'  => 'pro',
        'default' => 'no'
    
                    ),
                ),
            ),
            'carousel_navigation' => array(
                'label'    => __( 'Navigation Style', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Navigation Position', 'logo-slider-wp' ),
        'desc' => __( 'On which position navigation will display.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_position]',
        'id' => 'lgx_carousel_nav_position',
        'status'  => 'pro',
        'default'   => 'bottom_center',
        'options'   => array(
            'top_right' => __( 'Top Right', 'logo-slider-wp' ),
            'top_center' => __( 'Top Center', 'logo-slider-wp' ),
            'top_left' => __( 'Top Left', 'logo-slider-wp' ),
            'vertical_center' => __( 'Vertical Middle', 'logo-slider-wp' ),
            'bottom_center' => __( 'Bottom Center', 'logo-slider-wp' ),
            'bottom_left' => __( 'Bottom Left', 'logo-slider-wp' ),
            'bottom_right' => __( 'Bottom Right', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Vertical Nav Bottom Position', 'logo-slider-wp' ),
        'desc'      => __( 'Set top bottom position for Vertical Middle button.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_ver_pos_bottom]',
        'id'        => 'lgx_carousel_nav_ver_pos_bottom',
        'status'  => 'pro',
        'default'   => '50%'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Vertical Nav Left Right Position', 'logo-slider-wp' ),
        'desc'      => __( 'Set left and right side position for Vertical Middle button.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_ver_pos_left_right]',
        'id'        => 'lgx_carousel_nav_ver_pos_left_right',
        'status'  => 'pro',
        'default'   => '-55px'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Nav Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select  carousel navigation color for icon or text.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_color]',
        'id'        => 'lgx_carousel_nav_color',
        'default'   => '#ffffff',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Nav Hover Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select carousel navigation hover color for icon or text.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_color_hover]',
        'id'        => 'lgx_carousel_nav_color_hover',
        'default'   => '#ffffff',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Background Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select carousel navigation background color.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_bg_color]',
        'id'        => 'lgx_carousel_nav_bg_color',
        'default'   => '#222b30',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Background Hover Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select carousel navigation hover background color.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_bg_color_hover]',
        'id'        => 'lgx_carousel_nav_bg_color_hover',
        'default'   => '#222b30',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Nav Button Font Size', 'logo-slider-wp' ),
        'desc'      => __( 'Set Font Size for navigation button.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_font_size]',
        'id'        => 'lgx_carousel_nav_btn_font_size',
        'status'   => 'pro',
        'default'   => '22px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Nav Button Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set Width for navigation button. You can also set the Width as: auto', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_width]',
        'id'        => 'lgx_carousel_nav_btn_width',
        'status'  => 'pro',
        'default'   => '30px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Nav Button Height', 'logo-slider-wp' ),
        'desc'      => __( 'Set Height for navigation button. You can also set the Height as: auto', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_height]',
        'id'        => 'lgx_carousel_nav_btn_height',
        'status'  => 'pro',
        'default'   => '30px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Nav Button Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Margin around the navigation button, e.g. 10px 0px 5px 0px.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_margin]',
        'id'        => 'lgx_carousel_nav_btn_margin',
        'default'   => '5px 0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Nav Button Padding', 'logo-slider-wp' ),
        'desc'      => __( 'Padding inside the navigation button, e.g. 3px 12px.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_padding]',
        'id'        => 'lgx_carousel_nav_btn_padding',
        'status'    => 'pro',
        'default'   => '0px'

                    ),
                ),
            ),
            'carousel_nav_border' => array(
                'label'    => __( 'Navigation Border', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'     => __( 'Nav Border', 'logo-slider-wp' ),
        'desc'      => __( 'Enable Border for carousel navigation', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_en]',
        'id'        => 'lgx_carousel_nav_border_en',
        'default'   => 'no'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Border Color', 'logo-slider-wp' ),
        'desc'      => __( 'Choose border color carousel navigation.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_color]',
        'id'        => 'lgx_carousel_nav_border_color',
        'default'   => '#161E54',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Border Hover Color', 'logo-slider-wp' ),
        'desc'      => __( 'Choose border hover color carousel navigation.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_color_hover]',
        'id'        => 'lgx_carousel_nav_border_color_hover',
        'default'   => '#88E0EF',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Border Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set Border Width for carousel navigation', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_width]',
        'id'        => 'lgx_carousel_nav_border_width',
        'default'   => '1px'
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Border Style', 'logo-slider-wp' ),
        'desc' => __( 'Select carousel navigation button border style.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_style]',
        'id' => 'lgx_carousel_nav_border_style',
        'default'   => 'solid',
        'options'   => array(
            'solid' => __( 'Solid', 'logo-slider-wp' ),
            'dotted' => __( 'Dotted', 'logo-slider-wp' ),
            'dashed' => __( 'Dashed', 'logo-slider-wp' ),
            'double' => __( 'Double', 'logo-slider-wp' ),
            'groove' => __( 'Groove', 'logo-slider-wp' ),
            'ridge' => __( 'Ridge', 'logo-slider-wp' ),
            'inset' => __( 'Inset', 'logo-slider-wp' ),
            'outset' => __( 'Outset', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Border Radius', 'logo-slider-wp' ),
        'desc'      => __( 'Border radius of the navigation button. Recommended: 50% for a circular button.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_radius]',
        'id'        => 'lgx_carousel_nav_border_radius',
        'status'  => 'pro',
        'default'   => '4px'

                    ),
                ),
            ),
            'carousel_pagination' => array(
                'label'    => __( 'Pagination Settings', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_carousel_pagination_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Pagination Dots', 'logo-slider-wp' ),
                        'default'      => 'yes',
                        'condition'    => 'carousel',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Pagination Hide on Mobile', 'logo-slider-wp' ),
        'yes_label' => __( 'Yes', 'logo-slider-wp' ),
        'no_label' => __( 'No', 'logo-slider-wp' ),
        'desc'    => __( 'If the options is mark as checked then pagination will hide on mobile view (767px).', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagi_mobile_en]',
        'id'      => 'lgx_carousel_pagi_mobile_en',
        'status'  => 'pro',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Pagination Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select carousel pagination dot Color.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_color]',
        'id'        => 'lgx_carousel_pagination_color',
        'default'   => '#869791',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Active Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select carousel pagination active dot Color..', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_color_active]',
        'id'        => 'lgx_carousel_pagination_color_active',
        'default'   => '#222b30',
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Dynamic Bullets', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'    => __( 'Keeps only a few pagination dots visible at once. Useful with many slides.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_dynamic_bullets_en]',
        'status'  => 'pro',
        'id'      => 'lgx_carousel_dynamic_bullets_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Dynamic Bullets Count', 'logo-slider-wp' ),
        'desc'      => __( 'The number of main bullets visible when Dynamic Bullets enabled.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_dynamic_bullets_no]',
        'status'  => 'pro',
        'id'        => 'lgx_carousel_dynamic_bullets_no',
        'default'   => '1'
    
                    ),
                ),
            ),
            'grid_settings' => array(
                'label'    => __( 'Grid Settings', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Grid Style', 'logo-slider-wp' ),
        'desc'      => __( 'Select grid style for showcase item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_style]',
        'id'        => 'lgx_grid_style',
        'status'  => 'pro',
        'default'   => 'simple',
        'options'   => array(
            'simple' => __( 'Simple', 'logo-slider-wp' ),
            'outline' => __( 'Outline', 'logo-slider-wp' ),
            'inner_outline' => __( 'Inner Outline', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Outline Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select color for the outline or inner outline style.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_outline_color]',
        'id'        => 'lgx_grid_outline_color',
        'default'   => '#e9e9e9',
    
                    ),
                    array(
                        'id'           => 'lgx_grid_column_gap',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_grid_column_gap]',
                        'type'         => 'text',
                        'label'        => __( 'Column Gap', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'default'      => '30px',
                        'condition'    => 'grid',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_grid_row_gap',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_grid_row_gap]',
                        'type'         => 'text',
                        'label'        => __( 'Row Gap', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'default'      => '30px',
                        'condition'    => 'grid',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Item Min Height', 'logo-slider-wp' ),
        'desc'      => __( 'Minimum item height, e.g. 100px. Set to 0 to disable.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_item_min_height]',
        'id'        => 'lgx_grid_item_min_height',
        'status'  => 'pro',
        'default'   => 0
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Item Image Vertical Align', 'logo-slider-wp' ),
        'desc' => __( 'Vertical alignment of items. Useful when image sizes vary.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_grid_image_vertical_align]',
        'id' => 'lgx_grid_image_vertical_align',
        'default'   => 'center',
        'status'  => 'pro',
        'options'   => array(
            'start' => __( 'Top', 'logo-slider-wp' ),
            'center' => __( 'Vertically Middle', 'logo-slider-wp' ),
            'end' => __( 'Bottom', 'logo-slider-wp' ),
        )
    
                    ),
                ),
            ),
            'flexbox_settings' => array(
                'label'    => __( 'Flexbox Settings', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Column Gap', 'logo-slider-wp' ),
        'desc'      => __( 'Gap between columns, e.g. 15px or 1.5rem.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_flexbox_column_gap]',
        'id'        => 'lgx_flexbox_column_gap',
        //'status' => 'pro',
        'default'   => '15px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Row Gap', 'logo-slider-wp' ),
        'desc'      => __( 'Gap between rows, e.g. 15px or 1.5rem.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_flexbox_row_gap]',
        'id'        => 'lgx_flexbox_row_gap',
        //'status' => 'pro',
        'default'   => '15px'
    
                    ),
                    array(
                        'id'           => 'lgx_flexbox_align_items',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_flexbox_align_items]',
                        'type'         => 'select',
                        'label'        => __( 'Align Items', 'logo-slider-wp' ),
                        //'status'     => 'pro',
                        'options'      => array(
                            'flex-start' => 'Top',
                            'center'     => 'Center',
                            'flex-end'   => 'Bottom',
                        ),
                        'default'      => 'flex-start',
                        'condition'    => 'flexbox',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_flexbox_justify_content',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_flexbox_justify_content]',
                        'type'         => 'select',
                        'label'        => __( 'Justify Content', 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'free_options' => array( 'flex-start', 'center' ),
                        'options'      => array(
                            'flex-start'    => 'Left',
                            'center'        => 'Center',
                            'flex-end'      => 'Right',
                            'space-between' => 'Space Between',
                        ),
                        'default'      => 'flex-start',
                        'condition'    => 'flexbox',
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Flex Wrap', 'logo-slider-wp' ),
        'desc' => __( 'Make the flexible items single or multi-line.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_wrap]',
        'id' => 'lgx_flexbox_wrap',
        'default'   => 'wrap',
        'status'  => 'pro',
        'options'   => array(
            'wrap' => __( 'Wrap', 'logo-slider-wp' ),
            'wrap-reverse' => __( 'Wrap Reverse', 'logo-slider-wp' ),
            'nowrap' => __( 'No wrap', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label' => __( 'Flex Direction', 'logo-slider-wp' ),
        'desc' => __( 'Set the direction of the flexible items.', 'logo-slider-wp' ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_direction]',
        'id' => 'lgx_flexbox_direction',
        'default'   => 'row',
        'status'  => 'pro',
        'options'   => array(
            'row' => __( 'Row', 'logo-slider-wp' ),
            'row-reverse' => __( 'Row Reverse', 'logo-slider-wp' ),
            'column' => __( 'Column', 'logo-slider-wp' ),
            'column-reverse' => __( 'Column Reverse', 'logo-slider-wp' ),
        )
    
                    ),
                ),
            ),
        ),
    ),

    // ==========================================================================
    // 4. STYLE TAB
    // ==========================================================================
    'style' => array(
        'id'    => 'style',
        'label' => __( 'Style', 'logo-slider-wp' ),
        'icon'  => 'dashicons-admin-appearance',
        'groups' => array(
            'typography' => array(
                'label'    => __( 'Brand Name', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_item_brand_name_color',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_color]',
                        'type'         => 'color',
                        'label'        => __( 'Brand Name Color', 'logo-slider-wp' ),
                        'default'      => '#111111',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Font Size', 'logo-slider-wp' ),
        'desc'      => __( 'Add Item Brand Name Font Size.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_font_size]',
        'id'        => 'lgx_item_brand_name_font_size',
        'default'   => '20px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Font Weight', 'logo-slider-wp' ),
        'desc'      => __( 'Set Item Brand Name Font Weight.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_font_weight]',
        'id'        => 'lgx_item_brand_name_font_weight',
        'status'  => 'pro',
        'default'   => '600'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Top Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Set Top margin for showcase title.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_top_margin_title]',
        'id'        => 'lgx_item_top_margin_title',
        //'status' => 'pro',
        'default'   => '5px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Bottom Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Set bottom margin for showcase title.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bottom_margin_title]',
        'id'        => 'lgx_item_bottom_margin_title',
        'status'  => 'pro',
        'default'   => '5px'
    
                    ),
                ),
            ),
            'description' => array(
                'label'    => __( 'Description', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_item_desc_color',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_desc_color]',
                        'type'         => 'color',
                        'label'        => __( 'Description Color', 'logo-slider-wp' ),
                        'default'      => '#555555',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Description Font Size', 'logo-slider-wp' ),
        'desc'      => __( 'Add Item Description Font Size.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_desc_font_size]',
        'id'        => 'lgx_item_desc_font_size',
        'default'   => '20px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Description Font Weight', 'logo-slider-wp' ),
        'desc'      => __( 'Set Item Description Font Weight.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_desc_font_weight]',
        'id'        => 'lgx_item_desc_font_weight',
        'status'  => 'pro',
        'default'   => '400'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Description Top Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Set Top margin for showcase description.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_top_margin_desc]',
        'id'        => 'lgx_item_top_margin_desc',
        //'status' => 'pro',
        'default'   => '0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Description Bottom Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Set bottom margin for showcase description.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bottom_margin_desc]',
        'id'        => 'lgx_item_bottom_margin_desc',
        'status'  => 'pro',
        'default'   => '0px'

                    ),
                ),
            ),
            'image_border' => array(
                'label'    => __( 'Image Border', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'     => __( 'Image Border', 'logo-slider-wp' ),
        'desc'      => __( 'Enable Border for all logo Image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color_en]',
        'id'        => 'lgx_img_border_color_en',
        'default'   => 'no'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Image Border Color', 'logo-slider-wp' ),
        'desc'      => __( 'Choose border color for logo image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color]',
        'id'        => 'lgx_img_border_color',
        'default'   => '#FF5151',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Image Border Hover Color', 'logo-slider-wp' ),
        'desc'      => __( 'Choose on hover. border color for logo Image', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color_hover]',
        'id'        => 'lgx_img_border_color_hover',
        'default'   => '#FF9B6A',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Image Border Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set Border Width for Showcase Logo Image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_width]',
        'id'        => 'lgx_img_border_width',
        //'status'  => 'pro',
        'default'   => '1px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Image Border Radius', 'logo-slider-wp' ),
        'desc'      => __( 'Set Border Radius for showcase logo Image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_radius]',
        'id'        => 'lgx_img_border_radius',
        //'status' => 'pro',
        'default'   => '4px'

                    ),
                ),
            ),
            'item_border' => array(
                'label'    => __( 'Item Border', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'     => __( 'Item Border', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'      => __( 'Enable Border for all logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_border_color_en]',
        'id'        => 'lgx_border_color_en',
        'status'  => 'pro',
        'default'   => 'no'
    
                    ),
                    array(
                        'id'           => 'lgx_item_border_color',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_border_color]',
                        'type'         => 'color',
                        'label'        => __( 'Item Border Color', 'logo-slider-wp' ),
                        // Legacy source (_meta_tab_style.php) uses 'id' => 'lgx_border_color'
                        // but its actual meta-storage key is already
                        // meta_lgx_lsp_shortcodes[lgx_item_border_color] — this field's
                        // 'name' above — so this entry is the correct match, not a new
                        // field. See docs/release/task020/reports/003, §2.4.
                        'status'       => 'pro',
                        'default'      => '#161E54',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Item Hover Border Color', 'logo-slider-wp' ),
        'desc'      => __( 'Choose on hover. border color for full item area', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_color_hover]',
        'id'        => 'lgx_item_border_color_hover',
        'status'  => 'pro',
        'default'   => '#161E54',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Item Border Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set Border Width for showcase logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_width]',
        'id'        => 'lgx_item_border_width',
        'status'  => 'pro',
        'default'   => '1px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Item Border Radius', 'logo-slider-wp' ),
        'desc'      => __( 'Set Border Radius for showcase logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_radius]',
        'id'        => 'lgx_item_border_radius',
        'status'  => 'pro',
        'default'   => '4px'

                    ),
                ),
            ),
            'item_background' => array(
                'label'    => __( 'Item Background', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'     => __( 'Item Background Color', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'      => __( 'Enable Background Color for all logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color_en]',
        'id'        => 'lgx_item_bg_color_en',
        'status'  => 'pro',
        'default'   => 'no'
    
                    ),
                    array(
                        'id'           => 'lgx_item_bg_color',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color]',
                        'type'         => 'color',
                        'label'        => __( 'Item Background Color', 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'default'      => '#f1f1f1',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Item Background Color on Hover', 'logo-slider-wp' ),
        'desc'      => __( 'Please select logo item background color on hover.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color_hover]',
        'id'        => 'lgx_item_bg_color_hover',
        'status'  => 'pro',
        'default'   => '#f1f1f1',

                    ),
                ),
            ),
            'hover_effects' => array(
                'label'    => __( 'Hover Effects', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_item_hover_effect',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_item_hover_effect]',
                        'type'         => 'select',
                        'label'        => __( 'Hover Effect', 'logo-slider-wp' ),
                        'status'       => 'pro',
                        'free_options' => array( 'none' ),
                        'options'      => array(
                            'none'               => __( 'None', 'logo-slider-wp' ),
                            'grayscale'          => __( 'Grayscale On Hover', 'logo-slider-wp' ),
                            'gray_remove'        => __( 'Grayscale Remove On Over', 'logo-slider-wp' ),
                            'gray_always'        => __( 'Grayscale Always', 'logo-slider-wp' ),
                            'box_shadow'         => __( 'Box Shadow', 'logo-slider-wp' ),
                            'box_shadow_always'  => __( 'Box Shadow Always', 'logo-slider-wp' ),
                            'overlay_link'       => __( 'Hover Overlay Link', 'logo-slider-wp' ),
                            'overlay_title'      => __( 'Hover Overlay Link with Title', 'logo-slider-wp' ),
                        ),
                        'default'      => 'none',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Hover Animation', 'logo-slider-wp' ),
        'desc'      => __( 'Select hover animation for showcase logo image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_hover_anim]',
        'status'    => 'pro',
        'id'        => 'lgx_item_hover_anim',
        'default'   => 'default',
        'free_options' => array( 'default', 'none' ),
        'options'   => array(
            'default'       => __( 'Default', 'logo-slider-wp' ),
            'none'          => __( 'None', 'logo-slider-wp' ),
            'scaleup'       => __( 'Scale Up', 'logo-slider-wp' ),
            'bounce'        => __( 'Bounce', 'logo-slider-wp' ),
            'flash'         => __( 'Flash', 'logo-slider-wp' ),
            'pulse'         => __( 'Pulse', 'logo-slider-wp' ),
            'rubberBand'    => __( 'Rubber Band', 'logo-slider-wp' ),
            'shakeX'        => __( 'ShakeX', 'logo-slider-wp' ),
            'shakeY'        => __( 'ShakeY', 'logo-slider-wp' ),
            'headShake'     => __( 'Head Shake', 'logo-slider-wp' ),
            'swing'         => __( 'Swing', 'logo-slider-wp' ),
            'tada'          => __( 'Tada', 'logo-slider-wp' ),
            'wobble'        => __( 'Wobble', 'logo-slider-wp' ),
            'jello'         => __( 'Jello', 'logo-slider-wp' ),
            'heartBeat'     => __( 'Heart Beat', 'logo-slider-wp' ),
            'backInDown'    => __( 'Back In Down', 'logo-slider-wp' ),
            'backInLeft'    => __( 'Back In Left', 'logo-slider-wp' ),
            'backInRight'   => __( 'Back In Right', 'logo-slider-wp' ),
            'backInUp'      => __( 'Back In Up ', 'logo-slider-wp' ),
            'bounceIn'      => __( 'Bounce In ', 'logo-slider-wp' ),
            'bounceInDown'  => __( 'Bounce In Down ', 'logo-slider-wp' ),
            'bounceInLeft'  => __( 'Bounce In Left ', 'logo-slider-wp' ),
            'bounceInRight' => __( 'Bounce In Right ', 'logo-slider-wp' ),
            'bounceInUp'    => __( 'Bounce In Up ', 'logo-slider-wp' ),
            'fadeIn'        => __( 'Fade In ', 'logo-slider-wp' ),
            'fadeInDown'    => __( 'Fade In Down ', 'logo-slider-wp' ),
            'fadeInDownBig' => __( 'Fade In Down Big ', 'logo-slider-wp' ),
            'fadeInLeft'    => __( 'Fade In Left ', 'logo-slider-wp' ),
            'fadeInLeftBig' => __( 'Fade In Left Big ', 'logo-slider-wp' ),
            'fadeInRight'   => __( 'Fade In Right ', 'logo-slider-wp' ),
            'fadeInRightBig'=> __( 'Fade In Right Big ', 'logo-slider-wp' ),
            'fadeInUp'      => __( 'Fade In Up ', 'logo-slider-wp' ),
            'fadeInUpBig'   => __( 'Fade In Up Big ', 'logo-slider-wp' ),
            'fadeInTopLeft' => __( 'Fade In Top Left ', 'logo-slider-wp' ),
            'fadeInTopRight'=> __( 'Fade In Top Right ', 'logo-slider-wp' ),
            'fadeInBottomLeft'  => __( 'Fade In Bottom Left ', 'logo-slider-wp' ),
            'fadeInBottomRight' => __( 'Fade In Bottom Right ', 'logo-slider-wp' ),
            'flip'              => __( 'Flip', 'logo-slider-wp' ),
            'flipInX'           => __( 'Flip InX', 'logo-slider-wp' ),
            'lightSpeedInRight' => __( 'Light Speed In Right', 'logo-slider-wp' ),
            'lightSpeedInLeft'  => __( 'Light Speed In Left', 'logo-slider-wp' ),
            'rotateIn'          => __( 'Rotate In', 'logo-slider-wp' ),
            'rotateInDownLeft'  => __( 'Rotate In Down Left', 'logo-slider-wp' ),
            'rotateInDownRight' => __( 'Rotate In Down Right', 'logo-slider-wp' ),
            'rotateInUpLeft'    => __( 'Rotate In Up Left', 'logo-slider-wp' ),
            'rotateInUpRight'   => __( 'Rotate In Up Right', 'logo-slider-wp' ),
            'hinge'             => __( 'Hinge', 'logo-slider-wp' ),
            'jackInTheBox'      => __( 'Jack In TheBox', 'logo-slider-wp' ),
            'rollIn'            => __( 'Roll In', 'logo-slider-wp' ),
            'zoomIn'            => __( 'Zoom In', 'logo-slider-wp' ),
            'zoomInDown'        => __( 'Zoom In Down', 'logo-slider-wp' ),
            'zoomInLeft'        => __( 'Zoom In Left', 'logo-slider-wp' ),
            'zoomInRight'       => __( 'Zoom In Right', 'logo-slider-wp' ),
            'zoomInUp'          => __( 'Zoom In Up', 'logo-slider-wp' ),
            'slideInDown'       => __( 'Slide In Down', 'logo-slider-wp' ),
            'slideInLeft'       => __( 'Slide In Left', 'logo-slider-wp' ),
            'slideInRight'      => __( 'Slide In Right', 'logo-slider-wp' ),
            'slideInUp'         => __( 'Slide In Up', 'logo-slider-wp' ),
        )

                    ),
                ),
            ),
            'spacing' => array(
                'label'    => __( 'Spacing', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Logo Padding', 'logo-slider-wp' ),
        'desc'      => __( 'Set item inner padding for showcase logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_padding]',
        'id'        => 'lgx_item_padding',
        //'status' => 'pro',
        'default'   => '0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Logo Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Set Margin for showcase logo item.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_margin]',
        'id'        => 'lgx_item_margin',
        //'status'  => 'pro',
        'default'   => '0px'
    
                    ),
                ),
            ),
        ),
    ),

    // ==========================================================================
    // 5. RESPONSIVE TAB
    // ==========================================================================
    'responsive' => array(
        'id'    => 'responsive',
        'label' => __( 'Responsive', 'logo-slider-wp' ),
        'icon'  => 'dashicons-smartphone',
        'groups' => array(
            'columns' => array(
                'label'  => __( 'Item Columns', 'logo-slider-wp' ),
                'expanded' => true,
                'fields' => array(
                    array(
                        'id'           => 'lgx_large_desktop_item',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_large_desktop_item]',
                        'type'         => 'number',
                        'label'        => __( 'Large Desktop Items', 'logo-slider-wp' ),
                        'desc'         => __( 'Item columns on large screens (1200px and up).', 'logo-slider-wp' ),
                        'default'      => 5,
                        'condition'    => null,
                        'responsive'   => true,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_desktop_item',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_desktop_item]',
                        'type'         => 'number',
                        'label'        => __( 'Desktop Items', 'logo-slider-wp' ),
                        'desc'         => __( 'Item columns on standard desktop screens (992px to 1199px).', 'logo-slider-wp' ),
                        'default'      => 4,
                        'condition'    => null,
                        'responsive'   => true,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_tablet_item',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_tablet_item]',
                        'type'         => 'number',
                        'label'        => __( 'Tablet Items', 'logo-slider-wp' ),
                        'desc'         => __( 'Item columns on tablet screens (768px to 991px).', 'logo-slider-wp' ),
                        'default'      => 3,
                        'condition'    => null,
                        'responsive'   => true,
                        'live_preview' => true,
                    ),
                    array(
                        'id'           => 'lgx_mobile_item',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_mobile_item]',
                        'type'         => 'number',
                        'label'        => __( 'Mobile Items', 'logo-slider-wp' ),
                        'desc'         => __( 'Item columns on mobile screens (below 768px).', 'logo-slider-wp' ),
                        'default'      => 2,
                        'condition'    => null,
                        'responsive'   => true,
                        'live_preview' => true,
                    ),

                ),
            ),
        ),
    ),

    // ==========================================================================
    // 6. SECTION TAB
    // ==========================================================================
    'section' => array(
        'id'    => 'section',
        'label' => __( 'Section', 'logo-slider-wp' ),
        'icon'  => 'dashicons-editor-insertmore',
        'groups' => array(
            'header' => array(
                'label'    => __( 'Header', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_header_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_header_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Enable Header', 'logo-slider-wp' ),
                        'default'      => 'no',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Header Alignment', 'logo-slider-wp' ),
        'desc'      => __( 'Section Header Alignment.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_align]',
        'id'        => 'lgx_header_align',
        'status'  => 'pro',
        'default'   => 'center',
        'options'   => array(
            'center' => __( 'Center', 'logo-slider-wp' ),
            'left' => __( 'Left', 'logo-slider-wp' ),
            'right' => __( 'Right', 'logo-slider-wp' )
        )
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Title', 'logo-slider-wp' ),
        'desc'      => __( 'Add your section header title.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title]',
        'id'        => 'lgx_header_title',
        //'status' => 'pro',
        'default'   => ''
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Title Font Size', 'logo-slider-wp' ),
        'desc'      => __( 'Add Title Font Size.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_font_size]',
        'id'        => 'lgx_header_title_font_size',
        'status'  => 'pro',
        'default'   => '42px'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Title Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select title color.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_color]',
        'status'  => 'pro',
        'id'        => 'lgx_header_title_color',
        'default'   => '#2e2841cc',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Title Font Weight', 'logo-slider-wp' ),
        'desc'      => __( 'Set Title Font Weight.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_font_weight]',
        'id'        => 'lgx_header_title_font_weight',
        'status'  => 'pro',
        'default'   => '500'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Title Bottom Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Add Title Bottom Margin.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_bottom_margin]',
        'id'        => 'lgx_header_title_bottom_margin',
        'status'  => 'pro',
        'default'   => '10px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Sub Title', 'logo-slider-wp' ),
        'desc'      => __( 'Add your section header Sub title.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle]',
        'id'        => 'lgx_header_subtitle',
        //'status' => 'pro',
        'default'   => ''
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Sub Title Font Size', 'logo-slider-wp' ),
        'desc'      => __( 'Add Sub Title Font Size.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_font_size]',
        'id'        => 'lgx_header_subtitle_font_size',
        'status'  => 'pro',
        'default'   => '16px'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Sub Title Color', 'logo-slider-wp' ),
        'desc'      => __( 'Please select Sub title color.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_color]',
        'id'        => 'lgx_header_subtitle_color',
        'status'  => 'pro',
        'default'   => '#888888',
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Sub Title Font Weight', 'logo-slider-wp' ),
        'desc'      => __( 'Set Sub Title Font Weight.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_font_weight]',
        'id'        => 'lgx_header_subtitle_font_weight',
        'status'  => 'pro',
        'default'   => '400'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Subtitle Bottom Margin', 'logo-slider-wp' ),
        'desc'      => __( 'Add Sub Title Font Size.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_bottom_margin]',
        'id'        => 'lgx_header_subtitle_bottom_margin',
        'status'  => 'pro',
        'default'   => '35px'
    
                    ),
                ),
            ),
            'general' => array(
                'label'    => __( 'Container', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_section_width',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_section_width]',
                        'type'         => 'text',
                        'label'        => __( 'Section Width', 'logo-slider-wp' ),
                        'default'      => '100%',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => true,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Showcase Container Type', 'logo-slider-wp' ),
        'desc'      => __( 'Select Showcase Container Type.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_section_container]',
        'id'        => 'lgx_section_container',
        'default'   => 'container-fluid',
        'options'   => array(
            'container-fluid' => __( 'Container Fluid', 'logo-slider-wp' ),
            'container' => __( 'Container', 'logo-slider-wp' ),
        )
    
                    ),
                ),
            ),
            'section_background' => array(
                'label'    => __( 'Background', 'logo-slider-wp' ),
                'expanded' => true,
                'fields'   => array(
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'label'   => __( 'Background Image', 'logo-slider-wp' ),
        'desc'    => __( 'Enable background image for showcase section.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bg_img_en]',
        'status'  => 'pro',
        'id'      => 'lgx_section_bg_img_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'upload',

        'layout'    => 'modern',
        'label'   => __( 'Upload Background Image', 'logo-slider-wp' ),
        'desc'    => __( 'Upload Background Image for Slider section.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bg_img]',
        'status'  => 'pro',
        'id'      => 'lgx_section_bg_img',
        //'default' => 'no'
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Background Attachment Type', 'logo-slider-wp' ),
        'desc'      => __( 'Select Background Attachment Type.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_section_bg_img_attachment]',
        'id'        => 'lgx_section_bg_img_attachment',
        'status'  => 'pro',
        'default'   => 'initial',
        'options'   => array(
            'initial' => __( 'Initial', 'logo-slider-wp' ),
            'fixed' => __( 'Fixed', 'logo-slider-wp' )
        )
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Background Size Type', 'logo-slider-wp' ),
        'desc'      => __( 'Set Background Size Type for background image.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_section_bg_img_size]',
        'id'        => 'lgx_section_bg_img_size',
        'status'  => 'pro',
        'default'   => 'cover',
        'options'   => array(
            'cover' => __( 'Cover', 'logo-slider-wp' ),
            'contain' => __( 'Contain', 'logo-slider-wp' ),
            'auto' => __( 'Auto', 'logo-slider-wp' )
        )
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Background/Overlay Color', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'    => __( 'Enable background or image Overlay Color for showcase section.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bg_color_en]',
        'id'      => 'lgx_section_bg_color_en',
        'default' => 'no'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'   => __( 'Background Color', 'logo-slider-wp' ),
        'desc'    => __( 'Choose background/ overlay Color for showcase section.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bg_color]',
        'id'      => 'lgx_section_bg_color',
        'default' => '#b56969'

                    ),
                ),
            ),
            'section_spacing' => array(
                'label'    => __( 'Spacing', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'   => __( 'Section Top Margin', 'logo-slider-wp' ),
        'desc'    => __( 'Section top margin, e.g. 15px or 15rem.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_top_margin]',
        'id'      => 'lgx_section_top_margin',
        'default' => '0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'   => __( 'Section Bottom Margin', 'logo-slider-wp' ),
        'desc'    => __( 'Section bottom margin, e.g. 15px or 15rem.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bottom_margin]',
        'id'      => 'lgx_section_bottom_margin',
        'default' => '0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'   => __( 'Section Top Padding', 'logo-slider-wp' ),
        'desc'    => __( 'Section top padding, e.g. 15px or 15rem.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_top_padding]',
        'id'      => 'lgx_section_top_padding',
        'status'  => 'pro',
        'default' => '0px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'   => __( 'Section Bottom Padding', 'logo-slider-wp' ),
        'desc'    => __( 'Section bottom padding, e.g. 15px or 15rem.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_section_bottom_padding]',
        'id'      => 'lgx_section_bottom_padding',
        'status'  => 'pro',
        'default' => '0px'
    
                    ),
                ),
            ),
        ),
    ),

    // ==========================================================================
    // 7. ADVANCED TAB
    // ==========================================================================
    'advanced' => array(
        'id'    => 'advanced',
        'label' => __( 'Advanced', 'logo-slider-wp' ),
        'icon'  => 'dashicons-admin-generic', // Fallback for settings
        'groups' => array(
            'preloader' => array(
                'label'    => __( 'Preloader', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_preloader_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_preloader_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Enable Preloader', 'logo-slider-wp' ),
                        'default'      => 'yes',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => false,
                    ),
                    array(
                        'type' => 'upload',

        'layout'    => 'modern',
        'label'   => __( 'Preloader Icon', 'logo-slider-wp' ),
        'desc'    => __( 'Upload Background Icon for Preloader.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_preloader_icon]',
        'status'  => 'pro',
        'id'      => 'lgx_preloader_icon',
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Preloader Background', 'logo-slider-wp' ),
        'desc'      => __( 'Please select background color for Preloader.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_preloader_bg_color]',
        'id'        => 'lgx_preloader_bg_color',
        'status'  => 'pro',
        'default'   => '#ffffff',
    
                    ),
                ),
            ),
            'tooltip' => array(
                'label'    => __( 'Tooltip', 'logo-slider-wp' ),
                'expanded' => false,
                'fields'   => array(
                    array(
                        'id'           => 'lgx_tooltip_en',
                        'name'         => 'meta_lgx_lsp_shortcodes[lgx_tooltip_en]',
                        'type'         => 'switch',
                        'label'        => __( 'Enable Tooltip', 'logo-slider-wp' ),
                        'default'      => 'no',
                        'condition'    => null,
                        'responsive'   => false,
                        'live_preview' => false,
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Content Type', 'logo-slider-wp' ),
        'desc'      => __( 'Select which content you want to show in tooltip.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_content_type]',
        'id'        => 'lgx_tooltip_content_type',
        'default'   => 'brand_name',
        'status'  => 'pro',
        'options'   => array(
            'brand_name' => __( 'Brand Name', 'logo-slider-wp' ),
            'tooltip_text' => __( 'Tooltip Text', 'logo-slider-wp' )
        )
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Position', 'logo-slider-wp' ),
        'desc'      => __( 'Select the position where you want to show tooltip.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_position]',
        'id'        => 'lgx_tooltip_position',
        'default'   => 'top',
     //  'status'  => 'pro',
        'options'   => array(
            'top' => __( 'Top', 'logo-slider-wp' ),
            'bottom' => __( 'Bottom', 'logo-slider-wp' ),
            'right' => __( 'Right', 'logo-slider-wp' ),
            'left' => __( 'Left', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Min Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set a minimum width for the tooltip. Default: 0 (auto width)', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_min_width]',
        'id'        => 'lgx_tooltip_min_width',
        //'status' => 'pro',
        'default'   => 0
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Max Width', 'logo-slider-wp' ),
        'desc'      => __( 'Set a maximum width for the tooltip. Default: null (no max width)', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_max_width]',
        'id'        => 'lgx_tooltip_max_width',
        //'status' => 'pro',
        'default'   => ''
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Animation', 'logo-slider-wp' ),
        'desc'      => __( 'Determines how the tooltip will animate in and out.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim]',
        'id'        => 'lgx_tooltip_anim',
        'default'   => 'fade',
        'status'  => 'pro',
        'options'   => array(
            'fade'   => __( 'Fade', 'logo-slider-wp' ),
            'grow' => __( 'Grow', 'logo-slider-wp' ),
            'swing'  => __( 'Swing', 'logo-slider-wp' ),
            'slide'  => __( 'Slide', 'logo-slider-wp' ),
            'fall'  => __( 'Fall', 'logo-slider-wp' ),
        )
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Animation Duration', 'logo-slider-wp' ),
        'desc'      => __( 'Sets the duration of the animation, in milliseconds.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim_duration]',
        'id'        => 'lgx_tooltip_anim_duration',
        'status'  => 'pro',
        'default'   => 350
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Animation Delay', 'logo-slider-wp' ),
        'desc'      => __( 'Delay in milliseconds before the tooltip opens/closes on hover.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim_delay]',
        'id'        => 'lgx_tooltip_anim_delay',
        'status'  => 'pro',
        'default'   => 300
    
                    ),
                    array(
                        'type' => 'select',

        'layout'    => 'modern',
        'label'     => __( 'Trigger Type', 'logo-slider-wp' ),
        'desc'      => __( 'When the tooltip opens and closes. The Click trigger requires Company URL to be disabled.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_trigger_type]',
        'id'        => 'lgx_tooltip_trigger_type',
        'default'   => 'hover',
        'status'  => 'pro',
        'options'   => array(
            'hover' => __( 'Hover', 'logo-slider-wp' ),
            'click' => __( 'Click', 'logo-slider-wp' )
        )
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Distance', 'logo-slider-wp' ),
        'desc'      => __( 'Distance between the item and the tooltip, in pixels.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_distance]',
        'id'        => 'lgx_tooltip_distance',
        'status'  => 'pro',
        'default'   => 6
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Text Color', 'logo-slider-wp' ),
        'desc'      => __( 'Select tooltip text color', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_text_color]',
        'id'        => 'lgx_tooltip_text_color',
        'status'  => 'pro',
        'default'   => '#ffffff'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Padding', 'logo-slider-wp' ),
        'desc'      => __( 'Add Tooltip inner padding.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_padding]',
        'id'        => 'lgx_tooltip_padding',
        'status'  => 'pro',
        'default'   => '8px'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Background Color', 'logo-slider-wp' ),
        'desc'      => __( 'Select tooltip Background color', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_bg_color]',
        'id'        => 'lgx_tooltip_bg_color',
        'status'  => 'pro',
        'default'   => '#d3d3d3'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Border Color', 'logo-slider-wp' ),
        'desc'      => __( 'Select tooltip border color', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_color]',
        'id'        => 'lgx_tooltip_border_color',
        'status'  => 'pro',
        'default'   => '#333333'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Border Width', 'logo-slider-wp' ),
        'desc'      => __( 'Add Tooltip Width.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_width]',
        'id'        => 'lgx_tooltip_border_width',
        'status'  => 'pro',
        'default'   => '2px'
    
                    ),
                    array(
                        'type' => 'text',

        'layout'    => 'modern',
        'label'     => __( 'Border Radius', 'logo-slider-wp' ),
        'desc'      => __( 'Add Tooltip Radius.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_radius]',
        'id'        => 'lgx_tooltip_border_radius',
        'status'  => 'pro',
        'default'   => '4px',
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Minimum Intersection', 'logo-slider-wp' ),
        'desc'      => __( 'Minimum space between the arrow and the tooltip edges.', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_min_intersection]',
        'id'        => 'lgx_tooltip_min_intersection',
        'status'  => 'pro',
        'default'   => 16
    
                    ),
                    array(
                        'type' => 'number',

        'layout'    => 'modern',
        'label'     => __( 'Timer', 'logo-slider-wp' ),
        'desc'      => __( 'How long the tooltip should be allowed to live before hiding. Default: 0 (disabled)', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_timer]',
        'status'  => 'pro',
        'id'        => 'lgx_tooltip_timer',
        'default'   => 0
    
                    ),
                    array(
                        'type' => 'switch',

        'layout'    => 'modern',
        'label'   => __( 'Arrow', 'logo-slider-wp' ),
        'yes_label' => __( 'Enabled', 'logo-slider-wp' ),
        'no_label' => __( 'Disabled', 'logo-slider-wp' ),
        'desc'    => __( 'Add a speech bubble arrow to the tooltip.', 'logo-slider-wp' ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_en]',
        'id'      => 'lgx_tooltip_arrow_en',
        'status'  => 'pro',
        'default' => 'yes'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Arrow Background Color', 'logo-slider-wp' ),
        'desc'      => __( 'Select arrow background color', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_bg_color]',
        'id'        => 'lgx_tooltip_arrow_bg_color',
        'status'    => 'pro',
        'default'   => '#555555'
    
                    ),
                    array(
                        'type' => 'color',

        'layout'    => 'modern',
        'label'     => __( 'Arrow Border Color', 'logo-slider-wp' ),
        'desc'      => __( 'Select arrow border color', 'logo-slider-wp' ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_border_color]',
        'id'        => 'lgx_tooltip_arrow_border_color',
        'status'    => 'pro',
        'default'   => '#333333'
    
                    ),
                ),
            ),
        ),
    ),
);

return apply_filters( 'logosliderwp_showcase_builder_config', $config );
