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


$fieldValues = get_post_meta( $post->ID, '_logosliderwpmeta', true );

wp_nonce_field( 'metaboxlogosliderwp', 'metaboxlogosliderwp[nonce]' );

$company_url         = isset( $fieldValues['company_url'] ) ? $fieldValues['company_url'] : '';
$company_name        = isset( $fieldValues['company_name'] ) ? $fieldValues['company_name'] : '';
$tooltip_text        = isset( $fieldValues['tooltip_text'] ) ? $fieldValues['tooltip_text'] : '';
$company_desc        = isset( $fieldValues['company_desc'] ) ? $fieldValues['company_desc'] : '';
$alt_text            = isset( $fieldValues['alt_text'] ) ? $fieldValues['alt_text'] : '';


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->



<div id="lgx_meta_box_wrapper" class="lgx_meta_boxes_fields_logo_item">
    <div class="lgx_meta_box_wrapper_inner lgx-form-container">

        <!-- Backward compatible wrapper for injected rows -->
        <?php if ( has_action( 'lgx_ls_logo_meta_fields_before' ) ) : ?>
        <table class="form-table lgx_form_table" style="margin:0; padding:0;">
            <tbody>
                <?php do_action( 'lgx_ls_logo_meta_fields_before', $fieldValues );  ?>
            </tbody>
        </table>
        <?php endif; ?>

        <p class="lgx-field__intro description" style="margin-bottom: 28px;">
            <?php esc_html_e( 'Only the Brand Name and Logo Image are required. The fields below are optional.', 'logo-slider-wp' ); ?>
        </p>

        <!-- PRIMARY FIELDS -->
        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <label class="lgx-field__label" for="lgx_company_url"><?php esc_html_e( 'Website URL', 'logo-slider-wp' ); ?></label>
            </div>
            <div class="lgx-field-right">
                <input type="url" id="lgx_company_url" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_url]" value="<?php echo esc_url( $company_url ); ?>" aria-describedby="lgx_company_url_desc" placeholder="https://..." />
                <p id="lgx_company_url_desc" class="lgx-field__description description"><?php esc_html_e( 'Enter the destination URL. Leave empty to make the logo non-clickable.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <label class="lgx-field__label" for="lgx_tooltip_text"><?php esc_html_e( 'Tooltip', 'logo-slider-wp' ); ?></label>
            </div>
            <div class="lgx-field-right">
                <input type="text" id="lgx_tooltip_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[tooltip_text]" value="<?php echo esc_attr( $tooltip_text ); ?>" aria-describedby="lgx_tooltip_text_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" />
                <p id="lgx_tooltip_text_desc" class="lgx-field__description description"><?php esc_html_e( 'Automatically uses the Brand Name until you customize it. Displayed when visitors hover over the logo.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <label class="lgx-field__label" for="lgx_alt_text"><?php esc_html_e( 'Alt Text', 'logo-slider-wp' ); ?></label>
            </div>
            <div class="lgx-field-right">
                <input type="text" id="lgx_alt_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[alt_text]" value="<?php echo esc_attr( $alt_text ); ?>" aria-describedby="lgx_alt_text_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" />
                <p id="lgx_alt_text_desc" class="lgx-field__description description"><?php esc_html_e( 'Automatically uses the Brand Name until you customize it. Helps improve accessibility and SEO.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <label class="lgx-field__label" for="lgx_custom_brand_name"><?php esc_html_e( 'Custom Brand Name', 'logo-slider-wp' ); ?></label>
            </div>
            <div class="lgx-field-right">
                <input type="text" id="lgx_custom_brand_name" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_name]" value="<?php echo esc_attr( $company_name ); ?>" aria-describedby="lgx_custom_brand_name_desc" placeholder="<?php esc_attr_e( 'Leave empty to use the Brand Name', 'logo-slider-wp' ); ?>" />
                <p id="lgx_custom_brand_name_desc" class="lgx-field__description description"><?php esc_html_e( 'Enter a different brand name to display instead of the original Brand Name.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <label class="lgx-field__label" for="lgx_company_desc"><?php esc_html_e( 'Description (Optional)', 'logo-slider-wp' ); ?></label>
            </div>
            <div class="lgx-field-right">
                <textarea rows="4" id="lgx_company_desc" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_desc]" aria-describedby="lgx_company_desc_desc" placeholder="<?php esc_attr_e( 'Add a short description about this brand...', 'logo-slider-wp' ); ?>"><?php echo esc_textarea( $company_desc ); ?></textarea>
                <p id="lgx_company_desc_desc" class="lgx-field__description description"><?php esc_html_e( 'Displayed only in layouts that support logo descriptions.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <!-- Backward compatible wrapper for injected rows -->
        <?php if ( has_action( 'lgx_ls_logo_meta_fields_after' ) ) : ?>
        <table class="form-table lgx_form_table" style="margin:0; padding:0;">
            <tbody>
                <?php do_action( 'lgx_ls_logo_meta_fields_after', $fieldValues );  ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div>
</div>