<?php
/**
 * Showcase Builder Main Wrapper Template
 * 
 * This file serves as the semantic HTML shell for the Showcase Builder.
 * It dynamically generates the navigation rail and panel wrappers from the registry.
 * 
 * @since 6.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

global $post;

// Fetch the declarative settings registry
$config_path = plugin_dir_path( dirname( __FILE__ ) ) . 'config/config.php';
$registry    = file_exists( $config_path ) ? include $config_path : array();

/*
 * B53 — populate 'lgx_from_category's placeholder options with real
 * logosliderwpcat terms. Same get_terms() args as the legacy
 * _meta_tab_basic.php (orderby=id, hide_empty=true) for continuity with
 * the pre-rebuild behaviour. Mutated in place before the renderer ever
 * sees the registry, so no renderer/meta-form change is needed.
 */
foreach ( $registry as &$lgx_b53_tab_data ) {
    if ( empty( $lgx_b53_tab_data['groups'] ) || ! is_array( $lgx_b53_tab_data['groups'] ) ) {
        continue;
    }
    foreach ( $lgx_b53_tab_data['groups'] as &$lgx_b53_group_data ) {
        if ( empty( $lgx_b53_group_data['fields'] ) || ! is_array( $lgx_b53_group_data['fields'] ) ) {
            continue;
        }
        foreach ( $lgx_b53_group_data['fields'] as &$lgx_b53_field ) {
            if ( 'lgx_from_category' !== ( $lgx_b53_field['id'] ?? '' ) ) {
                continue;
            }
            $lgx_b53_terms = get_terms(
                array(
                    'taxonomy'   => 'logosliderwpcat',
                    'orderby'    => 'id',
                    'hide_empty' => true,
                )
            );
            $lgx_b53_options = array( 'all' => __( 'All', 'logo-slider-wp' ) );
            if ( $lgx_b53_terms && ! is_wp_error( $lgx_b53_terms ) ) {
                foreach ( $lgx_b53_terms as $lgx_b53_term ) {
                    $lgx_b53_options[ $lgx_b53_term->term_id ] = $lgx_b53_term->name;
                }
            }
            $lgx_b53_field['options'] = $lgx_b53_options;
        }
        unset( $lgx_b53_field );
    }
    unset( $lgx_b53_group_data );
}
unset( $lgx_b53_tab_data );

/*
 * Task 013 Phase 4, Item 1 — per-tab "configured" indicator dot.
 * The renderer only ever uses a field's 'default' as a value fallback, it
 * never exposes it separately (see task013 reports/001), so there is no
 * client-side signal for "is this field still at its default". Localizing
 * the registry's defaults here (Option B) lets JS diff each field's live
 * DOM value against them without touching the renderer/meta-form. Keyed by
 * the same id used as the field's DOM id (id_select/id_text for
 * group2SelectText, which renders two independent sub-fields).
 */
$lgx_field_defaults = array();
foreach ( $registry as $tab_data ) {
    if ( empty( $tab_data['groups'] ) || ! is_array( $tab_data['groups'] ) ) {
        continue;
    }
    foreach ( $tab_data['groups'] as $group_data ) {
        if ( empty( $group_data['fields'] ) || ! is_array( $group_data['fields'] ) ) {
            continue;
        }
        foreach ( $group_data['fields'] as $field ) {
            if ( isset( $field['id_select'] ) ) {
                $lgx_field_defaults[ $field['id_select'] ] = array(
                    'default' => $field['default_select'] ?? '',
                    'name'    => $field['name_select'] ?? '',
                    // Height/Width merge: Type-follows-Value JS reads these
                    // two off the SELECT's own entry (not the paired text
                    // field's) since it listens for the select's 'change'.
                    'valueDefaultsByOption' => $field['value_defaults_by_option'] ?? array(),
                    'pairedValueFieldId'    => $field['id_text'] ?? '',
                );
                $lgx_field_defaults[ $field['id_text'] ] = array(
                    'default' => $field['default_text'] ?? '',
                    'name'    => $field['name_text'] ?? '',
                );
            } elseif ( isset( $field['id'] ) ) {
                $lgx_field_defaults[ $field['id'] ] = array(
                    'default' => $field['default'] ?? '',
                    'name'    => $field['name'] ?? '',
                    // Logo Height/Width un-merge: the two Type dropdowns are
                    // now plain 'select' fields (no more id_select), so this
                    // is where Type-follows-Value's lookup data has to live.
                    // Harmless empty array/string for every other plain
                    // field, which never declares these two keys.
                    'valueDefaultsByOption' => $field['value_defaults_by_option'] ?? array(),
                    'pairedValueFieldId'    => $field['paired_value_field_id'] ?? '',
                );
            }
        }
    }
}
/*
 * RB-1 Part C — lets JS know, without a second AJAX round trip, whether this
 * showcase has no saved meta yet (new/auto-draft). get_post_meta() is cached
 * per-request by WP core, so this second call costs nothing extra — it's not
 * worth restructuring the $saved_meta read further down in this file just to
 * reuse it here.
 */
$lgx_is_empty_meta = $post ? empty( get_post_meta( absint( $post->ID ), '_lgx_lsp_shortcodes_meta', true ) ) : true;
wp_localize_script( 'logo-slider-wp-showcase-builder', 'lgxShowcaseBuilderDefaults', array(
    'fields'              => $lgx_field_defaults,
    'isEmptyMeta'         => $lgx_is_empty_meta,
    'publishText'         => __( 'Publish' ),
    'sources'             => lgx_ls_get_registered_sources(),
    'unknownSourceFormat' => __( 'Unknown source: %s', 'logo-slider-wp' ),
    // T-226b (B189): texts of showcase-builder.js; the script keeps the English text as fallback.
    'layoutUpdated'       => __( 'Layout updated', 'logo-slider-wp' ),
    'showcaseSaved'       => __( 'Showcase saved', 'logo-slider-wp' ),
    'shortcodeCopied'     => __( 'Shortcode copied', 'logo-slider-wp' ),
    'copiedLabel'         => __( 'Copied!', 'logo-slider-wp' ),
    'copiedToClipboard'   => __( 'Copied to clipboard.', 'logo-slider-wp' ),
    'previewFailed'       => __( 'Preview update failed — showing the last successful preview.', 'logo-slider-wp' ),
    'proFeature'          => __( 'Pro Feature', 'logo-slider-wp' ),
    'moreInfo'            => __( 'More information', 'logo-slider-wp' ),
    'showcaseNameHelp'    => __( 'Internal showcase name for admin reference only.', 'logo-slider-wp' ),
    /* translators: shown in place of a stored source value that is not text (for example a number). */
    'notText'             => __( '(not text)', 'logo-slider-wp' ),
    /* translators: %s: the name of the setting that must be switched on first. Keep the <strong> tag. */
    'depLockNote'         => __( 'Enable <strong>%s</strong> to use this setting.', 'logo-slider-wp' ),
) );

// Load the Generic Renderer Class
$renderer_path = plugin_dir_path( dirname( __FILE__ ) ) . 'class-logo-slider-wp-showcase-renderer.php';
if ( file_exists( $renderer_path ) ) {
    require_once $renderer_path;
}

// Read saved meta for header status initial values
$saved_meta      = $post ? get_post_meta( absint( $post->ID ), '_lgx_lsp_shortcodes_meta', true ) : array();
$saved_meta      = is_array( $saved_meta ) ? $saved_meta : array();
$current_type    = isset( $saved_meta['lgx_lswp_showcase_type'] ) ? sanitize_text_field( $saved_meta['lgx_lswp_showcase_type'] ) : 'carousel';
// The raw stored source value, exactly as PHP and the front end read it (escaped for the attribute, never cleaned), so the page script and
// lgx_ls_describe_source() below work from the same value. A missing key sends no attribute (the script reads that as Logo Library);
// a stored value that is not text sends a flag instead of a value (the script reads that as "not text").
$current_source_raw   = isset( $saved_meta['lgx_lswp_content_source'] ) ? $saved_meta['lgx_lswp_content_source'] : null;
$current_source_attrs = '';
if ( is_string( $current_source_raw ) ) {
    $current_source_attrs = ' data-current-source="' . esc_attr( $current_source_raw ) . '"';
} elseif ( null !== $current_source_raw ) {
    $current_source_attrs = ' data-current-source-nontext="1"';
}

// Layout map for status display
$status_layout_map = array(
    'carousel' => array(
        'label' => __( 'Carousel', 'logo-slider-wp' ),
        'icon'  => 'dashicons-layout',
    ),
    'grid' => array(
        'label' => __( 'Grid', 'logo-slider-wp' ),
        'icon'  => 'dashicons-grid-view',
    ),
    'flexbox' => array(
        'label' => __( 'Flexbox', 'logo-slider-wp' ),
        'icon'  => 'dashicons-columns',
    ),
);

$status_layout = isset( $status_layout_map[ $current_type ] ) ? $status_layout_map[ $current_type ] : $status_layout_map['carousel'];
// Content source for status display: the one source registry (raw stored value, null when no key).
$status_source = lgx_ls_describe_source( isset( $saved_meta['lgx_lswp_content_source'] ) ? $saved_meta['lgx_lswp_content_source'] : null );

/* Task 014/Commit 4a: Layout Gallery card data, relocated verbatim from
   tab-general_settings.php (which used the name $layout_map). Kept as its
   own distinct variable rather than merged into $status_layout_map above —
   that map only carries label+icon for the header pill's compact display;
   this one also carries desc for the gallery cards themselves. Do not
   consolidate the two without a separately scoped task (014 Phase 2 plan
   §4a: "relocate, don't refactor"). */
$lgx_layout_gallery_map = array(
    'carousel' => array(
        'label' => __( 'Carousel', 'logo-slider-wp' ),
        'icon'  => 'dashicons-slides',
        'desc'  => __( 'Scrolling carousel with navigation and autoplay.', 'logo-slider-wp' ),
    ),
    'grid' => array(
        'label' => __( 'Grid', 'logo-slider-wp' ),
        'icon'  => 'dashicons-grid-view',
        'desc'  => __( 'Static grid with configurable columns and gap.', 'logo-slider-wp' ),
    ),
    'flexbox' => array(
        'label' => __( 'Flexbox', 'logo-slider-wp' ),
        'icon'  => 'dashicons-columns',
        'desc'  => __( 'Flexible row layout with wrap and alignment controls.', 'logo-slider-wp' ),
    ),
);

// 004 §2: same shortcode formula as tab-general_settings.php / embed-metabox.php.
$header_shortcode = '[lgxlogoslider id="' . absint( $post->ID ) . '"]';

// Task 014/Commit 1: PHP-snippet formula for the Usage modal's PHP/Template
// card — same construction as tab-general_settings.php's own usage selector.
$header_php_snippet = '<?php echo do_shortcode( \'' . $header_shortcode . '\' ); ?>';

/* Task 020/Task 6: docs URL finalized by the Owner — resolves the
   Commit 1 placeholder/TODO. Same URL for both editions, no UTM
   parameters (per a documented WordPress.org moderator statement that
   tracking parameters on outbound plugin links may fall under Guideline
   7's consent requirement). Used by both the Usage modal footer link and
   the header Usage link's href. */
if ( ! defined( 'LGX_LS_USAGE_DOCS_URL' ) ) {
    define( 'LGX_LS_USAGE_DOCS_URL', 'https://docs.logichunt.com/logo-slider-wp/builder/' );
}

// Unrelated to LGX_LS_USAGE_DOCS_URL above -- a separate feature-readiness
// flag, not a related setting. Template system not built for 6.0.0; flip to
// true and this pill (below) reappears with no other changes needed, per
// 010 Part C's original design.
if ( ! defined( 'LGX_LS_SHOWCASE_TEMPLATE_ENABLED' ) ) {
    define( 'LGX_LS_SHOWCASE_TEMPLATE_ENABLED', false );
}

?>

<?php /* RB-2/B52: exposes the true stored content-source value for showcase-builder.js's initBuilderStatus() to fall back to when no Content Source radio is checked (a legacy stored value with no matching radio left in the UI) — see 003-fix-plan.md §1d. */ ?>
<div class="lgx-builder" data-builder-version="6.0.0"<?php echo $current_source_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above with esc_attr(). ?>>
    <script>
    (function() {
        try {
            var pid = <?php echo (int) $post->ID; ?>;
            if (window.innerWidth >= 1440 && sessionStorage.getItem('lgx_focus_mode_' + pid) === '1') {
                document.currentScript.parentElement.classList.add('is-focus-mode');
            }
        } catch(e) {}
    })();
    </script>
    <?php wp_nonce_field( 'meta_lgx_lsp_shortcodes', 'meta_lgx_lsp_shortcodes[nonce]' ); ?>
    
    <!-- Task 004/007R-003/008/011: Builder Header (utility strip + main content row) -->
    <header class="lgx-builder__header" role="banner">
        <?php /* 011 §2: Top row contains Identity (Logo + Title) and Utility links */ ?>
        <div class="lgx-builder__header-top">
            <div class="lgx-builder__header-identity">
                <img src="<?php echo esc_url( LGX_LS_PLUGIN_ROOT_URL . 'admin/img/lgx-brand-icon.svg' ); ?>" alt="LogicHunt" class="lgx-builder__brand-icon">
                <h2><?php esc_html_e( 'Showcase Builder', 'logo-slider-wp' ); ?></h2>
            </div>
            
            <?php /* Task 014/Commit 2 (Owner Ruling 1 on the 014 Phase 1 report):
                    Usage is now unconditional in both editions — the previous
                    if/else swapped it out entirely in Free mode, which
                    conflicted with the ruling. Get Pro! moves to the end of
                    the row, Free-mode only, with its own distinct treatment
                    so it reads as promotional rather than a fifth utility
                    link.
                    Task 020/Task 2: Help removed entirely — BACKLOG B79
                    (Help and Support resolved to the same page) is closed by
                    dropping the duplicate, not by merging the two. Current
                    row: Usage · Demo · Support (+ Get Pro! in Free). */ ?>
            <div class="lgx-builder__header-links">
                <?php /* target="_blank" kept deliberately, restored after Commit 2 review:
                        preventDefault() in initUsageModal() always wins on the working
                        path (target is inert there), but it's the progressive-enhancement
                        fallback if JS never binds — opens the docs page in a new tab
                        instead of navigating the admin session away mid-edit. */ ?>
                <a href="<?php echo esc_url( LGX_LS_USAGE_DOCS_URL ); ?>" id="lgx-header-usage-trigger" target="_blank" aria-haspopup="dialog" aria-expanded="false">
                    <span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
                    <span class="lgx-builder__header-link-text"><?php esc_html_e( 'Usage', 'logo-slider-wp' ); ?></span>
                    <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                </a>
                <span class="lgx-builder__header-link-sep">&bull;</span>
                <a href="https://demo.logichunt.com/logo-slider-wp/" target="_blank">
                    <span class="dashicons dashicons-desktop" aria-hidden="true"></span>
                    <span class="lgx-builder__header-link-text"><?php esc_html_e( 'Demo', 'logo-slider-wp' ); ?></span>
                </a>
                <?php /* Task 020/Task 2: Help removed — its href
                        (https://logichunt.com/support/) resolved to the same
                        page as Support (https://logichunt.com/support, a
                        301 redirect to the exact same URL), confirmed via
                        real HTTP requests during Commit 2's investigation
                        and logged as BACKLOG B79. Resolved now by dropping
                        the duplicate link rather than merging the two —
                        Support's href is untouched. */ ?>
                <span class="lgx-builder__header-link-sep">&bull;</span>
                <a href="https://logichunt.com/support/" target="_blank">
                    <span class="dashicons dashicons-sos" aria-hidden="true"></span>
                    <span class="lgx-builder__header-link-text"><?php esc_html_e( 'Support', 'logo-slider-wp' ); ?></span>
                </a>
                <?php if ( ! function_exists( 'lgx_ls_is_pro' ) || ! lgx_ls_is_pro() ): ?>
                    <span class="lgx-builder__header-link-sep">&bull;</span>
                    <a href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank" class="lgx-builder__header-link--upgrade">
                        <span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
                        <span class="lgx-builder__header-link-text"><?php esc_html_e( 'Get Pro!', 'logo-slider-wp' ); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="lgx-builder__header-main">
            <div class="lgx-builder-status" id="lgx-builder-status-summary" aria-live="polite">
                    <span class="lgx-builder-status__title"><?php esc_html_e( 'Configure Showcase:', 'logo-slider-wp' ); ?></span>
                    <?php /* 008 §4: jump-to-tab only while Source has a single option;
                            becomes a true picker when multi-source support ships. Do not
                            build a selection UI here for one option — see 008 §4. */ ?>
                    <button type="button"
                            class="lgx-builder-status__btn lgx-builder-status__btn--config lgx-source-trigger"
                            id="lgx-header-source-btn"
                            title="<?php esc_attr_e( 'View Content Source Settings', 'logo-slider-wp' ); ?>">
                        <span class="dashicons <?php echo esc_attr( $status_source['icon'] ); ?>"></span>
                        <span class="lgx-builder-status__btn-label"><?php esc_html_e( 'Change Source:', 'logo-slider-wp' ); ?></span>
                        <strong><?php echo esc_html( $status_source['label'] ); ?></strong>
                        <span class="dashicons dashicons-arrow-down-alt2 lgx-status-chevron" aria-hidden="true"></span>
                    </button>
                    <span class="lgx-builder-status__sep">&bull;</span>
                    <button type="button"
                            class="lgx-builder-status__btn lgx-builder-status__btn--config lgx-layout-trigger"
                            aria-haspopup="dialog"
                            aria-expanded="false"
                            title="<?php esc_attr_e( 'Change Showcase Layout', 'logo-slider-wp' ); ?>">
                        <span class="lgx-layout-trigger-icon lgx-current-layout-icon dashicons <?php echo esc_attr( $status_layout['icon'] ); ?>"></span>
                        <span class="lgx-builder-status__btn-label"><?php esc_html_e( 'Change Layout:', 'logo-slider-wp' ); ?></span>
                        <strong class="lgx-layout-trigger-label"><?php echo esc_html( $status_layout['label'] ); ?></strong>
                        <span class="dashicons dashicons-arrow-down-alt2 lgx-status-chevron" aria-hidden="true"></span>
                    </button>
                    <?php if ( LGX_LS_SHOWCASE_TEMPLATE_ENABLED ) : ?>
                    <span class="lgx-builder-status__sep">&bull;</span>
                    <?php /* 008 §4/§5: Template pill — rendering only, this brief does not
                            own its value or behavior (010 Part C does). Identical pill
                            treatment to Layout/Source per the control taxonomy (§2), a real
                            <button> so a future click handler is a behavior swap, not a
                            markup change — but intentionally unwired here (§8 item 10: does
                            nothing when clicked). Plain flex sibling, no reserved width, so
                            hiding it later via a single class/attribute toggle needs no
                            other header changes. */ ?>
                    <button type="button"
                            class="lgx-builder-status__btn lgx-builder-status__btn--config"
                            id="lgx-header-template-btn"
                            title="<?php esc_attr_e( 'Template', 'logo-slider-wp' ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <span class="lgx-builder-status__btn-label"><?php esc_html_e( 'Template:', 'logo-slider-wp' ); ?></span>
                        <strong><?php esc_html_e( 'Default', 'logo-slider-wp' ); ?></strong>
                    </button>
                    <?php endif; ?>
                </div>
            <div class="lgx-builder__header-actions">
                <?php /* 011 §1: Copy Shortcode moved to actions row. */ ?>
                <button type="button"
                        class="lgx-builder-status__btn lgx-builder-status__btn--action"
                        id="lgx-header-copy-shortcode-btn"
                        data-shortcode="<?php echo esc_attr( $header_shortcode ); ?>"
                        title="<?php esc_attr_e( 'Copy Shortcode', 'logo-slider-wp' ); ?>">
                    <span class="dashicons dashicons-shortcode"></span>
                    <span class="lgx-builder-status__btn-label"><?php esc_html_e( 'Copy Shortcode', 'logo-slider-wp' ); ?></span>
                </button>
                <?php /* 004 §3/008 §6: Sticky header Save/Update button + status chip.
                        Native #publish stays in the DOM (visually hidden via CSS, see
                        showcase-builder.css) and does the real submission — this button
                        only triggers it (initStickySaveButton(), showcase-builder.js).
                        Label text is overwritten on init to mirror the native button's
                        own value ("Publish"/"Update"/"Schedule"/"Submit for Review"), so
                        wording stays correct for every post status without duplicating
                        that logic here. Save Draft (below) is the same pattern against
                        #save-post specifically — #publish is no longer used as its
                        fallback target (008 §6: the old `||` fallback never actually
                        fired, since #publish always exists). */ ?>
                <div class="lgx-builder__header-save" id="lgx-builder-header-save">


                    <button type="button" id="lgx-builder-header-save-btn" class="button button-primary lgx-builder__header-save-btn">
                        <span class="lgx-builder__header-save-btn-label"><?php esc_html_e( 'Save', 'logo-slider-wp' ); ?></span>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <div class="lgx-builder__workspace">
        
        <!-- Task 005 / Task 012B: Navigation Rail (Vertical Tabs / Mobile Select Fallback) -->
        <aside class="lgx-builder__navigation" aria-label="<?php esc_attr_e( 'Builder Navigation', 'logo-slider-wp' ); ?>">
            <div class="lgx-builder__nav-select-wrap">
                <label for="lgx-builder-nav-select" class="screen-reader-text"><?php esc_html_e( 'Select Settings Section', 'logo-slider-wp' ); ?></label>
                <select id="lgx-builder-nav-select" class="lgx-builder__nav-select">
                    <?php foreach ( $registry as $tab_key => $tab_data ) : 
                        $tab_id    = esc_attr( $tab_data['id'] );
                        $tab_label = esc_html( $tab_data['label'] );
                    ?>
                        <?php /* Task 014/Commit 4b: default tab is now 'source_settings'
                                (labelled "Content"). General tab removed — a stored
                                'general' active-tab value (never actually persisted
                                anywhere, confirmed in the 014 Phase 1 report §D.11)
                                would simply not match any option here and fall through
                                to no pre-selection, same as any other stale value. */ ?>
                        <option value="<?php echo $tab_id; ?>" <?php selected( $tab_key, 'source_settings' ); ?>>
                            <?php echo $tab_label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lgx-ui-tablist" role="tablist" aria-orientation="vertical">
                <?php 
                foreach ( $registry as $tab_key => $tab_data ) : 
                    $tab_id     = esc_attr( $tab_data['id'] );
                    $tab_label  = esc_html( $tab_data['label'] );
                    $tab_icon   = esc_attr( isset( $tab_data['icon'] ) ? $tab_data['icon'] : 'dashicons-admin-generic' );
                    $is_active  = ( $tab_key === 'source_settings' ) ? 'true' : 'false';
                    /* Task 014/Commit 4b: the General-tab exclusion here is gone —
                       General had no real subgroups (its one registry field was
                       never rendered), so it was hardcoded out of subgroup nav.
                       'source_settings' (Content, the new default) DOES have real
                       subgroups and must get normal subgroup nav, so this is a
                       deletion of the special case, not a rename of it. */
                    $has_groups = ! empty( $tab_data['groups'] );
                ?>
                    <div class="lgx-builder-nav__item<?php echo ! $has_groups ? ' is-childless' : ''; ?>" data-tab-id="<?php echo $tab_id; ?>">
                        <button type="button"
                                class="lgx-ui-tab" 
                                role="tab" 
                                id="lgx-tab-<?php echo $tab_id; ?>" 
                                aria-controls="lgx-panel-<?php echo $tab_id; ?>" 
                                aria-selected="<?php echo $is_active; ?>"
                                <?php if ( $has_groups ) : ?>
                                aria-expanded="<?php echo ( $is_active === 'true' ) ? 'true' : 'false'; ?>"
                                <?php endif; ?>>
                            <span class="dashicons <?php echo $tab_icon; ?>"></span>
                            <span class="lgx-ui-tab-label"><?php echo $tab_label; ?></span>
                            <span class="lgx-ui-tab-indicator" hidden aria-hidden="true" title="<?php esc_attr_e( 'Configured', 'logo-slider-wp' ); ?>"></span>
                            <?php if ( $has_groups ) : ?>
                                <span class="lgx-builder-nav__chevron-icon dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                            <?php endif; ?>
                        </button>
                        <?php if ( $has_groups ) : ?>
                            <ul class="lgx-builder-nav__subgroups" id="lgx-subgroups-<?php echo $tab_id; ?>" aria-hidden="<?php echo ( $is_active === 'true' ) ? 'false' : 'true'; ?>">
                                <?php foreach ( $tab_data['groups'] as $group_key => $group_data ) :
                                    // B69: sidebar label now always reads from config.php's group
                                    // 'label' — the former source_dependent hardcode ("Logo Library")
                                    // is gone; that group's config.php label is "Logo Source" so the
                                    // sidebar and panel heading can never say two different things.
                                    $group_label = isset( $group_data['label'] ) ? $group_data['label'] : $group_key;
                                    $target_id = 'lgx-group-' . $tab_id . '-' . $group_key;
                                ?>
                                    <li class="lgx-builder-nav__subgroup-item" data-group-key="<?php echo esc_attr( $group_key ); ?>">
                                        <button type="button" 
                                                class="lgx-builder-nav__subgroup-btn" 
                                                data-target-group="<?php echo esc_attr( $target_id ); ?>">
                                            <?php echo esc_html( $group_label ); ?>
                                        </button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ( ! function_exists( 'lgx_ls_is_pro' ) || ! lgx_ls_is_pro() ): ?>
            <div class="lgx-builder__upgrade-sidebar">
                <span class="dashicons dashicons-star-filled lgx-upgrade-icon"></span>
                <h4><?php esc_html_e( 'Unlock More Features', 'logo-slider-wp' ); ?></h4>
                <p><?php esc_html_e( 'Get advanced layouts, tooltips, and premium styles with Pro.', 'logo-slider-wp' ); ?></p>
                <a href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank" class="lgx-btn lgx-btn-primary lgx-btn-block">
                    <?php esc_html_e( 'Upgrade to Pro', 'logo-slider-wp' ); ?>
                </a>
            </div>
            <?php endif; ?>
        </aside>
        
        <!-- Main Work Area -->
        <main class="lgx-builder__content" role="main">
            
            <!-- Task 007: Settings Rendering (Form fields based on config.php) -->
            <section id="lgx-builder-settings" class="lgx-builder__settings" aria-label="<?php esc_attr_e( 'Settings Panel', 'logo-slider-wp' ); ?>">
                <?php 
                foreach ( $registry as $tab_key => $tab_data ) : 
                    $tab_id = esc_attr( $tab_data['id'] );
                    $active_class = ( $tab_key === 'source_settings' ) ? ' is-active' : '';
                ?>
                    <div class="lgx-ui-tabpanel<?php echo $active_class; ?>"
                         role="tabpanel"
                         id="lgx-panel-<?php echo $tab_id; ?>"
                         aria-labelledby="lgx-tab-<?php echo $tab_id; ?>"
                         tabindex="0">
                        
                        <?php 
                        $template_file = plugin_dir_path( __FILE__ ) . 'tab-' . $tab_id . '.php';
                        if ( file_exists( $template_file ) ) {
                            include $template_file;
                        } else {
                            echo '<div class="lgx-builder-error"><p>' . sprintf( esc_html__( 'Error: Template %s not found.', 'logo-slider-wp' ), 'tab-' . esc_html( $tab_id ) . '.php' ) . '</p></div>';
                        }
                        ?>

                    </div>
                <?php endforeach; ?>
            </section>
            
            <?php
            /* Task 010 Phase 2 — Live Preview iframe.
               ADR-009 Decision 5: the nonce is generated server-side, right here,
               on page load — never client-side/JS. A JS-generated nonce would not
               carry the request context (user, action string, time window) the
               endpoint's wp_verify_nonce() check requires, and every load would
               403. This section is a sibling of .lgx-builder__settings, both
               children of .lgx-builder__content — outside every [role="tabpanel"]
               region already (confirmed by reading this file's structure), so
               ADR-009 Decision 6 ("must survive tab switching without reloading")
               requires no structural change, only NOT placing the iframe inside
               a tab panel, which this markup already doesn't do.
               Loads eagerly on builder open, not deferred to first interaction:
               the preview column is always inside the initial viewport of this
               three-pane layout (never scrolled-to or revealed by a click), so
               lazy-loading would add a visible blank-then-load step for zero
               benefit — documented choice, not an oversight. */
            ?>
            <section id="lgx-builder-preview-panel" class="lgx-builder__preview lgx-builder__preview-wrapper" aria-label="<?php esc_attr_e( 'Live Preview', 'logo-slider-wp' ); ?>">
                <?php if ( $post && $post->ID ) :
                    $lgx_preview_nonce = wp_create_nonce( 'lgx_preview_' . $post->ID );
                    $lgx_preview_src   = add_query_arg(
                        array(
                            'lgx_preview' => 1,
                            'post_id'     => absint( $post->ID ),
                            'nonce'       => $lgx_preview_nonce,
                        ),
                        home_url( '/' )
                    );
                    ?>
                    <?php /* Task 010 Phase 3: refresh button lives in the preview column's
                            own header, never inside .lgx-builder__settings — it only ever
                            touches the iframe (via JS, see initLivePreview()), never the
                            settings form.
                            Task 010 Phase 4 (ADR-009 Decision 4): device toggle changes the
                            iframe CONTAINER's CSS width only — no viewport meta, no media-
                            query manipulation, no browser emulation API. Buttons carry
                            data-device, consumed by initLivePreview() to toggle a class on
                            .lgx-preview-frame-wrap; the breakpoint values themselves live as
                            CSS custom properties on .lgx-builder__preview, not as magic
                            numbers in this markup or in JS. The iframe itself has no width
                            attribute — width:100% in CSS is relative to whatever width the
                            (possibly now-constrained) frame-wrap resolves to. */ ?>
                    <?php /* Task 009: Focus Mode Reload Discoverability Cue (Notice shown only when restored from sessionStorage on page load) */ ?>
                    <div class="lgx-builder__focus-cue" id="lgx-builder-focus-cue" role="status" aria-live="polite" style="display: none;">
                        <div class="lgx-builder__focus-cue-content">
                            <span class="dashicons dashicons-info lgx-builder__focus-cue-icon" aria-hidden="true"></span>
                            <span class="lgx-builder__focus-cue-text">
                                <?php esc_html_e( 'Focus Mode is active &mdash; settings are hidden.', 'logo-slider-wp' ); ?>
                            </span>
                            <button type="button" class="lgx-builder__focus-cue-action" id="lgx-builder-focus-cue-action">
                                <?php esc_html_e( 'Show settings', 'logo-slider-wp' ); ?> &rarr;
                            </button>
                        </div>
                        <button type="button" class="lgx-builder__focus-cue-dismiss" id="lgx-builder-focus-cue-dismiss" aria-label="<?php esc_attr_e( 'Dismiss notice', 'logo-slider-wp' ); ?>">
                            <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
                        </button>
                    </div>

                    <div class="lgx-preview-header">
                        <div class="lgx-preview-device-toggle" role="group" aria-label="<?php esc_attr_e( 'Preview device width', 'logo-slider-wp' ); ?>">
                            <button type="button" class="lgx-preview-device-btn is-active" data-device="desktop" aria-pressed="true" aria-label="<?php esc_attr_e( 'Desktop', 'logo-slider-wp' ); ?>">
                                <span class="dashicons dashicons-desktop" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="lgx-preview-device-btn" data-device="tablet" aria-pressed="false" aria-label="<?php esc_attr_e( 'Tablet', 'logo-slider-wp' ); ?>">
                                <span class="dashicons dashicons-tablet" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="lgx-preview-device-btn" data-device="mobile" aria-pressed="false" aria-label="<?php esc_attr_e( 'Mobile', 'logo-slider-wp' ); ?>">
                                <span class="dashicons dashicons-smartphone" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="lgx-preview-header__actions">
                            <button type="button"
                                    class="lgx-builder__focus-toggle"
                                    id="lgx-builder-focus-toggle"
                                    aria-pressed="false"
                                    title="<?php esc_attr_e( 'Toggle Focus Mode (Full Canvas)', 'logo-slider-wp' ); ?>">
                                <span class="dashicons dashicons-fullscreen-alt" aria-hidden="true"></span>
                                <span class="lgx-builder__focus-toggle-label"><?php esc_html_e( 'Focus Mode', 'logo-slider-wp' ); ?></span>
                            </button>
                            <button type="button" id="lgx-preview-refresh" class="lgx-preview-refresh-btn">
                                <span class="dashicons dashicons-update lgx-preview-refresh-btn__icon" aria-hidden="true"></span>
                                <span class="lgx-preview-refresh-btn__label"><?php esc_html_e( 'Refresh Preview', 'logo-slider-wp' ); ?></span>
                            </button>
                        </div>
                    </div>
                    <div class="lgx-preview-frame-wrap" id="lgx-preview-frame-wrap">
                        <iframe
                            id="lgx-live-preview-iframe"
                            class="lgx-live-preview-iframe"
                            src="<?php echo esc_url( $lgx_preview_src ); ?>"
                            title="<?php esc_attr_e( 'Live Preview', 'logo-slider-wp' ); ?>"
                            scrolling="no"
                        ></iframe>
                        <div class="lgx-preview-loading-overlay" id="lgx-preview-loading-overlay" hidden>
                            <span class="lgx-preview-spinner" aria-hidden="true"></span>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="lgx-builder__placeholder">
                        <span class="dashicons dashicons-visibility lgx-builder__placeholder-icon"></span>
                        <p class="lgx-builder__placeholder-title"><?php esc_html_e( 'Live Preview', 'logo-slider-wp' ); ?></p>
                        <p class="lgx-builder__placeholder-desc"><?php esc_html_e( 'Save the showcase to see a preview.', 'logo-slider-wp' ); ?></p>
                    </div>
                <?php endif; ?>
            </section>

        </main>

    </div>

    <?php
    /* Task 014/Commit 1 — Usage modal. Informational cards only, no selection
       state, nothing persisted (see 014 Phase 1 report §B). Reuses the
       .lgx-layout-gallery/.lgx-layout-gallery-overlay dialog chrome already
       established by the Layout Gallery and Pro Upgrade modals rather than
       inventing new modal CSS — same extend-with-a-second-class pattern
       .lgx-pro-modal uses. Moved to document.body by initUsageModal()
       (showcase-builder.js), so — like those two modals — it sits outside
       .lgx-builder and is unaffected by Focus Mode's .lgx-builder__settings
       collapse (confirmed: Focus Mode CSS only targets
       .lgx-builder.is-focus-mode .lgx-builder__navigation/__settings). */
    ?>
    <div class="lgx-layout-gallery-overlay" id="lgx-usage-modal-overlay"></div>

    <div
        class="lgx-layout-gallery lgx-usage-modal"
        id="lgx-usage-modal"
        role="dialog"
        aria-labelledby="lgx-usage-modal-title"
        aria-modal="true"
    >
        <div class="lgx-layout-gallery__header">
            <h3 class="lgx-layout-gallery__title" id="lgx-usage-modal-title">
                <?php esc_html_e( 'Usage', 'logo-slider-wp' ); ?>
            </h3>
            <button
                type="button"
                class="lgx-layout-gallery__close"
                id="lgx-usage-modal-close"
                aria-label="<?php esc_attr_e( 'Close Usage Dialog', 'logo-slider-wp' ); ?>"
            >&times;</button>
        </div>

        <div class="lgx-layout-gallery__body lgx-usage-modal__body">

            <p class="lgx-usage-modal__id">
                <?php
                printf(
                    /* translators: %d: showcase post ID */
                    esc_html__( 'Showcase ID: %d', 'logo-slider-wp' ),
                    (int) $post->ID
                );
                ?>
            </p>

            <?php if ( $lgx_is_empty_meta ) : ?>
                <p class="lgx-usage-modal__unsaved-notice">
                    <?php esc_html_e( 'Save this showcase first to get a working shortcode.', 'logo-slider-wp' ); ?>
                </p>
            <?php endif; ?>

            <div class="lgx-usage-modal__cards">

                <div class="lgx-usage-modal-card">
                    <h4 class="lgx-usage-modal-card__title"><?php esc_html_e( 'Shortcode', 'logo-slider-wp' ); ?></h4>
                    <div class="lgx-embed-panel__row">
                        <input
                            type="text"
                            id="lgx-usage-modal-shortcode-field"
                            class="large-text code selectable lgx-embed-panel__field"
                            value="<?php echo esc_attr( $header_shortcode ); ?>"
                            readonly
                        >
                        <button
                            type="button"
                            class="button lgx-btn lgx-btn-secondary lgx-usage-modal-copy-btn"
                            data-copy-target="lgx-usage-modal-shortcode-field"
                            data-copy-success-msg="<?php esc_attr_e( 'Shortcode copied', 'logo-slider-wp' ); ?>"
                            title="<?php esc_attr_e( 'Copy', 'logo-slider-wp' ); ?>"
                            <?php disabled( $lgx_is_empty_meta ); ?>
                        >
                            <span class="dashicons dashicons-admin-page lgx-embed-panel__copy-icon"></span>
                        </button>
                    </div>
                    <p class="lgx-usage-modal-card__desc"><?php esc_html_e( 'Paste into any post, page, or widget area.', 'logo-slider-wp' ); ?></p>
                </div>

                <div class="lgx-usage-modal-card">
                    <h4 class="lgx-usage-modal-card__title"><?php esc_html_e( 'Gutenberg Block', 'logo-slider-wp' ); ?></h4>
                    <div class="lgx-embed-panel__row">
                        <input
                            type="text"
                            id="lgx-usage-modal-block-field"
                            class="large-text code selectable lgx-embed-panel__field"
                            value="logo-slider-wp/logo-slider-showcase"
                            readonly
                        >
                        <button
                            type="button"
                            class="button lgx-btn lgx-btn-secondary lgx-usage-modal-copy-btn"
                            data-copy-target="lgx-usage-modal-block-field"
                            data-copy-success-msg="<?php esc_attr_e( 'Block name copied', 'logo-slider-wp' ); ?>"
                            title="<?php esc_attr_e( 'Copy', 'logo-slider-wp' ); ?>"
                        >
                            <span class="dashicons dashicons-admin-page lgx-embed-panel__copy-icon"></span>
                        </button>
                    </div>
                    <p class="lgx-usage-modal-card__desc">
                        <?php esc_html_e( 'In the Block Editor, search for "Logo Slider WP" and insert the block.', 'logo-slider-wp' ); ?>
                    </p>
                </div>

                <div class="lgx-usage-modal-card">
                    <h4 class="lgx-usage-modal-card__title"><?php esc_html_e( 'PHP / Template', 'logo-slider-wp' ); ?></h4>
                    <div class="lgx-embed-panel__row">
                        <input
                            type="text"
                            id="lgx-usage-modal-php-field"
                            class="large-text code selectable lgx-embed-panel__field"
                            value="<?php echo esc_attr( $header_php_snippet ); ?>"
                            readonly
                        >
                        <button
                            type="button"
                            class="button lgx-btn lgx-btn-secondary lgx-usage-modal-copy-btn"
                            data-copy-target="lgx-usage-modal-php-field"
                            data-copy-success-msg="<?php esc_attr_e( 'PHP snippet copied', 'logo-slider-wp' ); ?>"
                            title="<?php esc_attr_e( 'Copy', 'logo-slider-wp' ); ?>"
                            <?php disabled( $lgx_is_empty_meta ); ?>
                        >
                            <span class="dashicons dashicons-admin-page lgx-embed-panel__copy-icon"></span>
                        </button>
                    </div>
                    <p class="lgx-usage-modal-card__desc"><?php esc_html_e( 'For use in theme template files.', 'logo-slider-wp' ); ?></p>
                </div>

                <div class="lgx-usage-modal-card">
                    <h4 class="lgx-usage-modal-card__title"><?php esc_html_e( 'Page Builders', 'logo-slider-wp' ); ?></h4>
                    <p class="lgx-usage-modal-card__desc">
                        <?php esc_html_e( 'Add it with any page builder\'s Shortcode widget or element.', 'logo-slider-wp' ); ?>
                    </p>
                </div>

            </div>

            <p
                id="lgx-usage-modal-copy-status"
                class="screen-reader-text"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            ></p>
        </div>

        <div class="lgx-usage-modal__footer">
            <a href="<?php echo esc_url( LGX_LS_USAGE_DOCS_URL ); ?>" target="_blank" rel="noopener" class="lgx-usage-modal__doc-link">
                <span class="dashicons dashicons-media-document" aria-hidden="true"></span>
                <?php esc_html_e( 'Need more help? Read the documentation', 'logo-slider-wp' ); ?> &rarr;
            </a>
        </div>
    </div>

    <?php
    /* Task 014/Commit 4a: hidden Layout input, Layout Gallery, and Pro
       Upgrade modal — relocated verbatim from tab-general_settings.php
       (General tab, removed in Commit 4b) into this shared/page-global
       section, alongside the Usage modal and help popover. Both were
       already page-global in practice (moved to document.body at runtime
       by initLayoutGallery()/initProUpgradeModal(), showcase-builder.js —
       neither function assumes DOM parentage), so this move changes their
       location in the source, not their behavior. See the 014 Phase 2 plan
       §4a and its named Layout-save checkpoint for the verification this
       relocation requires before Commit 4b begins. */
    ?>
    <?php /* Hidden input for form saving — name must be nested under meta_lgx_lsp_shortcodes[] to reach the save handler's $_POST array. Layout Gallery selection (any ".lgx-layout-trigger" instance — header pill, Layout tab context row) writes to this same id. */ ?>
    <input
        type="hidden"
        id="lgx_lswp_showcase_type_field"
        name="meta_lgx_lsp_shortcodes[lgx_lswp_showcase_type]"
        value="<?php echo esc_attr( $current_type ); ?>"
    >

    <div class="lgx-layout-gallery-overlay" id="lgx-layout-gallery-overlay"></div>

    <div
        class="lgx-layout-gallery"
        id="lgx-layout-gallery"
        role="dialog"
        aria-labelledby="lgx-layout-gallery-title"
        aria-modal="true"
    >
        <div class="lgx-layout-gallery__header">
            <h3 class="lgx-layout-gallery__title" id="lgx-layout-gallery-title">
                <?php esc_html_e( 'Choose a Layout', 'logo-slider-wp' ); ?>
            </h3>
            <button
                type="button"
                class="lgx-layout-gallery__close"
                id="lgx-layout-gallery-close"
                aria-label="<?php esc_attr_e( 'Close Layout Gallery', 'logo-slider-wp' ); ?>"
            >&times;</button>
        </div>

        <div class="lgx-layout-gallery__body">
            <div class="lgx-layout-gallery__cards">
                <?php foreach ( $lgx_layout_gallery_map as $value => $layout ) :
                    $is_selected = ( $value === $current_type );
                ?>
                <button
                    type="button"
                    class="lgx-layout-gallery-card<?php echo $is_selected ? ' is-selected' : ''; ?>"
                    data-layout-value="<?php echo esc_attr( $value ); ?>"
                    data-layout-label="<?php echo esc_attr( $layout['label'] ); ?>"
                    data-layout-icon="<?php echo esc_attr( $layout['icon'] ); ?>"
                    data-layout-desc="<?php echo esc_attr( isset( $layout['desc'] ) ? $layout['desc'] : '' ); ?>"
                    aria-pressed="<?php echo $is_selected ? 'true' : 'false'; ?>"
                >
                    <span class="dashicons dashicons-saved lgx-layout-gallery-card__tick"></span>
                    <span class="dashicons <?php echo esc_attr( $layout['icon'] ); ?> lgx-layout-gallery-card__icon"></span>
                    <span class="lgx-layout-gallery-card__label"><?php echo esc_html( $layout['label'] ); ?></span>
                    <?php if ( ! empty( $layout['desc'] ) ) : ?>
                        <span class="lgx-layout-gallery-card__desc"><?php echo esc_html( $layout['desc'] ); ?></span>
                    <?php endif; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="lgx-layout-gallery-overlay" id="lgx-pro-modal-overlay"></div>

    <div
        class="lgx-layout-gallery lgx-pro-modal"
        id="lgx-pro-modal"
        role="dialog"
        aria-labelledby="lgx-pro-modal-title"
        aria-modal="true"
    >
        <div class="lgx-layout-gallery__header">
            <h3 class="lgx-layout-gallery__title" id="lgx-pro-modal-title">
                <span class="dashicons dashicons-star-filled lgx-pro-star"></span>
                <span id="lgx-pro-modal-feature-name"><?php esc_html_e( 'Pro Feature', 'logo-slider-wp' ); ?></span>
            </h3>
            <button
                type="button"
                class="lgx-layout-gallery__close"
                id="lgx-pro-modal-close"
                aria-label="<?php esc_attr_e( 'Close Modal', 'logo-slider-wp' ); ?>"
            >&times;</button>
        </div>

        <div class="lgx-layout-gallery__body lgx-pro-modal__body">
            <div class="lgx-pro-modal-content">
                <p class="lgx-pro-modal-benefit" id="lgx-pro-modal-benefit-text">
                    <?php esc_html_e( 'Unlock advanced configuration options and premium features.', 'logo-slider-wp' ); ?>
                </p>

                <ul class="lgx-pro-modal-features">
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Your existing showcases keep working', 'logo-slider-wp' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Your settings and data stay intact', 'logo-slider-wp' ); ?></li>
                    <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Just install and activate — nothing to migrate', 'logo-slider-wp' ); ?></li>
                </ul>

                <div class="lgx-pro-modal-actions">
                    <a href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank" class="lgx-btn lgx-btn-primary lgx-btn-lg">
                        <?php esc_html_e( 'Get Logo Slider Pro', 'logo-slider-wp' ); ?>
                    </a>
                    <button type="button" class="lgx-btn lgx-btn-secondary lgx-btn-lg" id="lgx-pro-modal-maybe-later">
                        <?php esc_html_e( 'Maybe Later', 'logo-slider-wp' ); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php
    /* 007R-d: single shared field-help popover, reused for every ⓘ icon across
       all tabs. Moved to document.body by initFieldTooltips() so position:fixed
       positioning is never affected by the settings column's own scroll
       container (same escape-the-overflow pattern already used for the
       Layout Gallery). role="status" + aria-live so screen readers announce
       the content when it opens; focus deliberately stays on the triggering
       ⓘ button throughout (never moves into the popover, so it can't be left
       stranded). */
    ?>
    <div
        id="lgx-help-popover"
        class="lgx-help-popover"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    ></div>

</div>
