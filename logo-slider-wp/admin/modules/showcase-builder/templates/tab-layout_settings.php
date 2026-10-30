<?php
/**
 * Tab: Layout Settings
 *
 * Configures the selected Layout type.
 * Contains all layout-specific settings (previously part of the "Layout" tab).
 *
 * Reuses the generic Lgx_Showcase_Renderer to dynamically output groups and fields.
 *
 * Task 008 Phase 2 had added a compact "Current Layout" context row here, as a
 * second way to open the Layout Gallery modal without a round-trip back to the
 * (now-removed) General tab. Removed 2026-09-02: the header status pill
 * (builder-wrapper.php's "Change Layout: <type>" button) is visible from every
 * tab at all times — .lgx-builder__settings scrolls internally while the header
 * sits outside that scroll container — so the in-tab row became a duplicate of
 * an already-always-visible control. The header pill remains the sole layout
 * switcher; both used the same #lgx-layout-gallery modal via initLayoutGallery().
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'Lgx_Showcase_Renderer' ) && isset( $tab_data ) ) {
	Lgx_Showcase_Renderer::render_tab_groups( $tab_data );
}
