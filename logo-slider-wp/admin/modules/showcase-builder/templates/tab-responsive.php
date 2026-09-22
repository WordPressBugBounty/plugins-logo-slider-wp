<?php
/**
 * Tab: Responsive
 * 
 * Reuses the generic Lgx_Showcase_Renderer to dynamically output groups and fields.
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( class_exists( 'Lgx_Showcase_Renderer' ) && isset( $tab_data ) ) {
    Lgx_Showcase_Renderer::render_tab_groups( $tab_data );
}
