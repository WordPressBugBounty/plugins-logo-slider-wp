<?php
/**
 * Tab: Source Settings
 *
 * B69: render order is now Essential -> Logo Source -> Query -> Logo Image,
 * matching config.php's group order and the sidebar nav (Task 68 §1.2). The
 * source-dependent ("Logo Source") panel is still custom-rendered here
 * instead of through the generic loop, because it needs its own conditional
 * field (Filter by Category) — but its title now reads from config.php's
 * group label instead of a hardcoded string, so it can never drift from the
 * sidebar nav's label again.
 *
 * RB-2/B52 (2026-08-15): the WooCommerce Brands "Coming Soon" panel this
 * comment used to describe has been removed (ADR-012 — UI removed for
 * 6.0.0). Logo Library is the only source now, so its panel is always
 * open — see $is_logo_library_active below. A showcase with a preserved
 * legacy 'woocommerce_brands' stored value (data untouched, per RB-2's
 * hard constraint) still gets this panel, since there is nothing else to
 * show in its place.
 *
 * @since 6.0.3 (007R-008)
 * @since 6.0.4 (007R-008 revision: source-dependent panel moved to top; titles made source-specific)
 * @since 6.0.5 (007R-008 revision: Global split into Query Settings / Content & Link Settings / Logo Settings)
 * @since 6.0.6 (B69: Essential moved first; source-dependent panel title reads from config.php)
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Only remaining source since the WooCommerce Brands panel was removed
// (RB-2/B52) — nothing left to toggle against. A showcase with a legacy
// non-'logo_library' stored value (e.g. 'woocommerce_brands') still gets
// this panel open; the underlying meta value itself is untouched.
$is_logo_library_active = true;

$lgx_source_dependent_label = isset( $tab_data['groups']['source_dependent']['label'] )
	? $tab_data['groups']['source_dependent']['label']
	: __( 'Logo Source', 'logo-slider-wp' );
?>

<?php
if ( class_exists( 'Lgx_Showcase_Renderer' ) && isset( $tab_data['groups']['content_link_settings'] ) ) {
	Lgx_Showcase_Renderer::render_group( 'content_link_settings', $tab_data['groups']['content_link_settings'], 'source_settings' );
}
?>

<div id="lgx-group-source_settings-source_dependent" class="lgx-ui-panelbody is-open lgx-source-dependent-panel" data-source-panel="logo_library">
	<div class="lgx-ui-panelbody-header" aria-expanded="true">
		<span class="lgx-ui-panelbody-title"><?php echo esc_html( $lgx_source_dependent_label ); ?></span>
	</div>
	<div class="lgx-ui-panelbody-content">
		<div class="lgx_form_table">
			<div style="display: flex; flex-direction: column; gap: 16px;">
				<?php
				if ( class_exists( 'Lgx_Showcase_Renderer' ) && isset( $tab_data['groups']['source_dependent']['fields'][0] ) ) {
					Lgx_Showcase_Renderer::render_field( $tab_data['groups']['source_dependent']['fields'][0] );
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php
if ( class_exists( 'Lgx_Showcase_Renderer' ) ) {
	foreach ( array( 'query_settings', 'logo_settings' ) as $group_id ) {
		if ( isset( $tab_data['groups'][ $group_id ] ) ) {
			Lgx_Showcase_Renderer::render_group( $group_id, $tab_data['groups'][ $group_id ], 'source_settings' );
		}
	}
}
