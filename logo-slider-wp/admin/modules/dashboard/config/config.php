<?php
/**
 * Dashboard configuration settings.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/dashboard/config
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'quick_actions' => array(
		array(
			'title'       => __( 'Add New Logo', 'logo-slider-wp' ),
			'description' => __( 'Upload and configure a new company logo brand item.', 'logo-slider-wp' ),
			'url'         => admin_url( 'post-new.php?post_type=logosliderwp' ),
			'icon'        => 'dashicons-plus',
			'target'      => '_self',
			'type'        => 'primary',
		),
		array(
			'title'       => __( 'Build Showcase', 'logo-slider-wp' ),
			'description' => __( 'Create a new slider or grid logo showcase configuration.', 'logo-slider-wp' ),
			'url'         => admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ),
			'icon'        => 'dashicons-images-alt2',
			'target'      => '_self',
			'type'        => 'secondary',
		),
		array(
			'title'       => __( 'Help & Documentation', 'logo-slider-wp' ),
			'description' => __( 'Read detailed guides on shortcodes, block settings, and customization.', 'logo-slider-wp' ),
			'url'         => admin_url( 'edit.php?post_type=logosliderwp&page=logosliderwp-help-resources' ),
			'icon'        => 'dashicons-editor-help',
			'target'      => '_self',
			'type'        => 'secondary',
		),
	),
	/**
	 * Single shared source for Dashboard "Quick Tips" and Help & Resources
	 * "Recommendations" (brief §8: Quick Tips is a curated subset, never a
	 * second independently-authored list). Each entry's 'quick_tip' flag
	 * marks whether it also surfaces on the Dashboard.
	 */
	'recommendations' => array(
		array(
			'id'          => 'image-formats-sizing',
			'title'       => __( 'Use the right image formats and sizes', 'logo-slider-wp' ),
			'description' => __( 'PNG or SVG logos provide the best quality and transparency. Use appropriately sized images for better website performance.', 'logo-slider-wp' ),
			'icon'        => 'dashicons-format-image',
			'quick_tip'   => true,
		),
		array(
			'id'          => 'categories',
			'title'       => __( 'Organize logos with Categories', 'logo-slider-wp' ),
			'description' => __( 'Create categories under Logo Slider → Categories, assign them to your logos, then use "Filter by Category" in the Builder\'s Content tab to show only specific categories in a showcase.', 'logo-slider-wp' ),
			'icon'        => 'dashicons-category',
			'quick_tip'   => true,
		),
		array(
			'id'          => 'pro-locked-settings',
			'title'       => __( 'Understand locked Pro settings', 'logo-slider-wp' ),
			'description' => __( 'A lock icon means a field is Pro-only — Free users can see it but not change it. If you previously had Pro and downgraded, any values you had already saved stay exactly as they were.', 'logo-slider-wp' ),
			'icon'        => 'dashicons-lock',
			'quick_tip'   => true,
		),
		array(
			'id'          => 'drag-drop-reorder',
			'title'       => __( 'Reorder logos with drag & drop', 'logo-slider-wp' ),
			'description' => __( 'Drag & drop logos on the Logos screen to change their display order.', 'logo-slider-wp' ),
			'icon'        => 'dashicons-move',
			'quick_tip'   => false,
		),
	),
	'video_tutorials' => ( defined( 'LGX_LS_VIDEOS_READY' ) && LGX_LS_VIDEOS_READY ) ? array(
		array(
			'title'    => __( 'How to create your first logo slider', 'logo-slider-wp' ),
			'duration' => '3:45',
			'url'      => 'https://www.youtube.com/watch?v=example1',
		),
		array(
			'title'    => __( 'Using categories to filter logos', 'logo-slider-wp' ),
			'duration' => '2:15',
			'url'      => 'https://www.youtube.com/watch?v=example2',
		),
	) : array(),
	'getting_started' => array(
		array(
			'id'          => 'logos',
			'title'       => __( 'Add your logos', 'logo-slider-wp' ),
			'description' => __( 'Upload the logos you want to display.', 'logo-slider-wp' ),
			'action_label'=> __( 'Add New Logo', 'logo-slider-wp' ),
			'url'         => admin_url( 'post-new.php?post_type=logosliderwp' ),
		),
		array(
			'id'          => 'showcases',
			'title'       => __( 'Create a showcase', 'logo-slider-wp' ),
			'description' => __( 'Group your logos into a beautiful grid or slider.', 'logo-slider-wp' ),
			'action_label'=> __( 'Create Showcase', 'logo-slider-wp' ),
			'url'         => admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ),
		),
		array(
			'id'          => 'shortcode',
			'title'       => __( 'Copy shortcode', 'logo-slider-wp' ),
			'description' => __( 'Grab the shortcode for the showcase you just created.', 'logo-slider-wp' ),
			'action_label'=> __( 'View Showcases', 'logo-slider-wp' ),
			'url'         => admin_url( 'edit.php?post_type=lgx_lsp_shortcodes' ),
		),
		array(
			'id'          => 'display',
			'title'       => __( 'Display on your website', 'logo-slider-wp' ),
			'description' => __( 'Paste the shortcode into any page or post.', 'logo-slider-wp' ),
			'action_label'=> __( 'Read Guide', 'logo-slider-wp' ),
			'url'         => 'https://docs.logichunt.com/logo-slider-wp/embed/',
		),
	),
);
