<?php
/**
 * Configuration and translatable strings for the Bulk Upload module.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/bulk-upload
 * @since      6.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'parent_slug'              => 'edit.php?post_type=logosliderwp',
	'page_slug'                => 'logosliderwp-bulk-upload',
	'capability'               => 'upload_files',
	'edit_capability'          => 'edit_posts',
	'nonce_action'             => 'lgx_ls_bulk_upload',
	'batch_size'               => 5,
	'check_cap'                => 100,
	'check_action'             => 'lgx_ls_bulk_check',
	'create_action'            => 'lgx_ls_bulk_create',
	/*
	 * Plain string per Owner decision D-A (ADMIN_MODULE_STANDARD.md section 7
	 * item 5: No Cross-Module Imports). Matches the demo content module's
	 * attachment meta key constant.
	 */
	'demo_attachment_meta'     => '_lgx_demo_attachment',
	'page_title'               => __( 'Bulk Upload Logos', 'logo-slider-wp' ),
	'menu_title'               => __( 'Bulk Upload Logos', 'logo-slider-wp' ),
	'button_label'             => __( 'Bulk Upload', 'logo-slider-wp' ),
	'add_new_prompt'           => __( 'Adding many logos?', 'logo-slider-wp' ),
	'add_new_link'             => __( 'Use Bulk Upload.', 'logo-slider-wp' ),
	'permission_denied'        => __( 'You do not have permission to access this page.', 'logo-slider-wp' ),
	'post_type'                => 'logosliderwp',
	'taxonomy'                 => 'logosliderwpcat',
	'publish_capability'       => 'publish_posts',
	'step_indicator_label'     => __( 'Bulk upload steps', 'logo-slider-wp' ),
	'step_1_label'             => __( 'Select images', 'logo-slider-wp' ),
	'step_2_label'             => __( 'Review', 'logo-slider-wp' ),
	'step_3_label'             => __( 'Create', 'logo-slider-wp' ),
	'help_text'                => __( 'Choose many images from the Media Library, or drag new files into the window that opens. Each image becomes one logo. Nothing is created until you review and confirm.', 'logo-slider-wp' ),
	'select_images_button'     => __( 'Select images', 'logo-slider-wp' ),
	'review_heading'           => __( 'Review selected images', 'logo-slider-wp' ),
	'col_image'                => __( 'Logo', 'logo-slider-wp' ),
	'col_filename'             => __( 'File', 'logo-slider-wp' ),
	'col_brand_name'           => __( 'Brand Name', 'logo-slider-wp' ),
	'col_details'              => __( 'Details', 'logo-slider-wp' ),
	'col_remove'               => __( 'Remove', 'logo-slider-wp' ),
	/* translators: %s: image file name */
	'remove_aria_label'        => __( 'Remove %s', 'logo-slider-wp' ),
	'remove_confirm_title'     => __( 'Remove this logo?', 'logo-slider-wp' ),
	/* translators: %s: image file name */
	'remove_confirm_text'      => __( '%s will not be created.', 'logo-slider-wp' ),
	'remove_confirm_keep'      => __( 'Keep it', 'logo-slider-wp' ),
	'remove_confirm_remove'    => __( 'Remove', 'logo-slider-wp' ),
	'remove_announced'         => __( 'Logo removed.', 'logo-slider-wp' ),
	'details_add'              => __( 'Add details', 'logo-slider-wp' ),
	'details_hide'             => __( 'Hide details', 'logo-slider-wp' ),
	/* translators: %d: number of filled detail fields */
	'details_count'            => __( 'Details (%d)', 'logo-slider-wp' ),
	'detail_url_label'         => __( 'Website URL', 'logo-slider-wp' ),
	'detail_name_label'        => __( 'Custom Brand Name', 'logo-slider-wp' ),
	'detail_tooltip_label'     => __( 'Tooltip', 'logo-slider-wp' ),
	'detail_alt_label'         => __( 'Alt Text', 'logo-slider-wp' ),
	'detail_desc_label'        => __( 'Description', 'logo-slider-wp' ),
	'detail_url_placeholder'     => __( 'https://...', 'logo-slider-wp' ),
	'detail_name_placeholder'    => __( 'Leave empty to use the Brand Name', 'logo-slider-wp' ),
	'detail_tooltip_placeholder' => __( 'Uses Brand Name by default', 'logo-slider-wp' ),
	'detail_alt_placeholder'     => __( 'Uses Brand Name by default', 'logo-slider-wp' ),
	'detail_desc_placeholder'    => __( 'Add a short description about this brand...', 'logo-slider-wp' ),
	'detail_caption_prefix'      => __( 'Details for', 'logo-slider-wp' ),
	'detail_caption_note'        => __( 'Tooltip and Alt Text follow the Brand Name until you change them.', 'logo-slider-wp' ),
	'locked_details_title'       => __( 'Filling in details for Bulk Upload is a Pro Feature', 'logo-slider-wp' ),
	'locked_details_text'        => __( 'Website URL and Description are still free. You can add them to each logo after it is created.', 'logo-slider-wp' ),
	'locked_show_how'            => __( 'Show me how', 'logo-slider-wp' ),
	'locked_step_1'              => __( 'Click Create to add your logos.', 'logo-slider-wp' ),
	/* translators: %s: 'Edit in single view' formatted as strong */
	'locked_step_2'              => __( 'On the next screen, click %s next to a logo. It opens in a new tab.', 'logo-slider-wp' ),
	'locked_step_2_bold'         => __( 'Edit in single view', 'logo-slider-wp' ),
	'locked_step_3'              => __( 'Add the Website URL and Description, then click Update.', 'logo-slider-wp' ),
	'locked_tip_prefix'          => __( 'Tip:', 'logo-slider-wp' ),
	/* translators: %s: 'Add New Logo' formatted as a link */
	'locked_tip_text'            => __( 'Adding only a few logos? %s lets you add each logo and its details on one page.', 'logo-slider-wp' ),
	'locked_tip_link'            => __( 'Add New Logo', 'logo-slider-wp' ),
	'locked_upgrade_label'       => __( 'Upgrade to Pro', 'logo-slider-wp' ),
	'locked_upgrade_url'         => 'https://logichunt.com/product/wordpress-logo-slider/',
	'result_edit_link_free'      => __( 'Edit in single view', 'logo-slider-wp' ),
	/* translators: %s: brand name */
	'result_edit_aria_free'      => __( 'Edit %s in single view', 'logo-slider-wp' ),
	'result_free_note'           => __( 'Logos without a Website URL are not clickable. Add one in the single edit view.', 'logo-slider-wp' ),
	'common_settings_heading'  => __( 'Common settings', 'logo-slider-wp' ),
	'categories_label'         => __( 'Categories (for all logos)', 'logo-slider-wp' ),
	'categories_help'          => __( 'Optional. You can change categories per logo later.', 'logo-slider-wp' ),
	'categories_empty'         => __( 'No logo categories exist yet.', 'logo-slider-wp' ),
	'status_label'             => __( 'Status', 'logo-slider-wp' ),
	'status_draft_label'       => __( 'Draft — not shown in showcases yet', 'logo-slider-wp' ),
	'status_publish_label'     => __( 'Publish now', 'logo-slider-wp' ),
	'status_draft_only_note'   => __( 'Your account can save logos as drafts only.', 'logo-slider-wp' ),
	'create_logos_button'      => __( 'Create logos', 'logo-slider-wp' ),
	'stop_button'              => __( 'Stop after this batch', 'logo-slider-wp' ),
	'progress_label'           => __( 'Bulk logo creation progress', 'logo-slider-wp' ),
	'results_heading'          => __( 'Results', 'logo-slider-wp' ),
	'retry_failed_button'      => __( 'Retry failed', 'logo-slider-wp' ),
	'select_more_button'       => __( 'Upload more', 'logo-slider-wp' ),
	'view_all_logos_button'    => __( 'View all logos', 'logo-slider-wp' ),
	'invalid_nonce'            => __( 'Security check failed. Please reload the page and try again.', 'logo-slider-wp' ),
	'publish_denied'           => __( 'You do not have permission to publish logos.', 'logo-slider-wp' ),
	'assign_terms_denied'      => __( 'You do not have permission to assign logo categories.', 'logo-slider-wp' ),
	'invalid_ids'              => __( 'Please provide between 1 and 100 attachment IDs to check.', 'logo-slider-wp' ),
	'invalid_items'            => __( 'Please provide between 1 and 5 valid logo items per request.', 'logo-slider-wp' ),
	'invalid_status'           => __( 'Invalid post status parameter.', 'logo-slider-wp' ),
	'invalid_terms'            => __( 'One or more selected categories do not exist.', 'logo-slider-wp' ),
	'item_invalid_id'          => __( 'Invalid or missing attachment ID.', 'logo-slider-wp' ),
	'item_not_attachment'      => __( 'The selected item is not a Media Library attachment.', 'logo-slider-wp' ),
	'item_not_image'           => __( 'The selected attachment is not a supported image.', 'logo-slider-wp' ),
	'item_unreadable'          => __( 'You do not have permission to use this attachment.', 'logo-slider-wp' ),
	'item_demo_skipped'        => __( 'This is a demo image. Upload your own logo.', 'logo-slider-wp' ),
	'duplicate_no_title'       => __( 'Already used by another logo. It will be skipped.', 'logo-slider-wp' ),
	/* translators: %d: existing logo post ID */
	'item_duplicate_skipped'   => __( 'Skipped: already used by logo #%d.', 'logo-slider-wp' ),
	'item_insert_failed'       => __( 'Could not create the logo post.', 'logo-slider-wp' ),
	'item_thumbnail_failed'    => __( 'Could not set the logo image.', 'logo-slider-wp' ),
	'item_details_warning'     => __( 'Created, but the details could not be saved. Add them in the logo editor.', 'logo-slider-wp' ),
	'item_terms_warning'       => __( 'Logo created, but categories could not be assigned.', 'logo-slider-wp' ),
	'item_created'             => __( 'Logo created.', 'logo-slider-wp' ),
);
