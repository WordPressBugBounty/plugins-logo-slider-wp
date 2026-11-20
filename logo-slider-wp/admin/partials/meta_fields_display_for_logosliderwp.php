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

$is_pro = class_exists( 'Lgx_Pro_Locking' ) && Lgx_Pro_Locking::is_pro_ready();

$fieldValues = get_post_meta( $post->ID, '_logosliderwpmeta', true );

wp_nonce_field( 'metaboxlogosliderwp', 'metaboxlogosliderwp[nonce]' );

$company_url         = isset( $fieldValues['company_url'] ) ? $fieldValues['company_url'] : '';
$company_name        = isset( $fieldValues['company_name'] ) ? $fieldValues['company_name'] : '';
$tooltip_text        = isset( $fieldValues['tooltip_text'] ) ? $fieldValues['tooltip_text'] : '';
$company_desc        = isset( $fieldValues['company_desc'] ) ? $fieldValues['company_desc'] : '';
$alt_text            = isset( $fieldValues['alt_text'] ) ? $fieldValues['alt_text'] : '';

$tooltip_locked = class_exists( 'Lgx_Logo_Field_Lock' ) ? Lgx_Logo_Field_Lock::is_locked( $is_pro, isset( $fieldValues['tooltip_text'] ) ? $fieldValues['tooltip_text'] : null ) : false;
$alt_locked     = class_exists( 'Lgx_Logo_Field_Lock' ) ? Lgx_Logo_Field_Lock::is_locked( $is_pro, isset( $fieldValues['alt_text'] ) ? $fieldValues['alt_text'] : null ) : false;
$company_locked = class_exists( 'Lgx_Logo_Field_Lock' ) ? Lgx_Logo_Field_Lock::is_locked( $is_pro, isset( $fieldValues['company_name'] ) ? $fieldValues['company_name'] : null ) : false;

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

        <p class="lgx-field__intro description">
            <?php esc_html_e( 'Only the Brand Name and Logo Image are required. The fields below are optional.', 'logo-slider-wp' ); ?>
        </p>

        <!-- PRIMARY FIELDS -->
        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <span class="lgx-field__labelwrap">
                    <label class="lgx-field__label" for="lgx_company_url"><?php esc_html_e( 'Website URL', 'logo-slider-wp' ); ?></label>
                    <button type="button" class="lgx-field__info" aria-expanded="false" aria-controls="lgx_company_url_desc">
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.25" fill="none" stroke="currentColor" stroke-width="1.5"/><rect x="9.1" y="8.6" width="1.8" height="5.6" rx=".9" fill="currentColor"/><circle cx="10" cy="6.1" r="1.1" fill="currentColor"/></svg>
                        <span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'More information: %s', 'logo-slider-wp' ), __( 'Website URL', 'logo-slider-wp' ) ) ); ?></span>
                    </button>
                </span>
            </div>
            <div class="lgx-field-right">
                <input type="url" id="lgx_company_url" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_url]" value="<?php echo esc_url( $company_url ); ?>" aria-describedby="lgx_company_url_desc" placeholder="https://..." />
                <p id="lgx_company_url_desc" class="lgx-field__description description lgx-info-note" hidden><?php esc_html_e( 'Enter the destination URL. Leave empty to make the logo non-clickable.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row<?php echo $tooltip_locked ? ' lgx-pro-locked-field' : ''; ?>">
            <div class="lgx-field-left">
                <span class="lgx-field__labelwrap">
                    <label class="lgx-field__label" for="lgx_tooltip_text"><?php esc_html_e( 'Tooltip', 'logo-slider-wp' ); ?></label>
                    <button type="button" class="lgx-field__info" aria-expanded="false" aria-controls="lgx_tooltip_text_desc">
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.25" fill="none" stroke="currentColor" stroke-width="1.5"/><rect x="9.1" y="8.6" width="1.8" height="5.6" rx=".9" fill="currentColor"/><circle cx="10" cy="6.1" r="1.1" fill="currentColor"/></svg>
                        <span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'More information: %s', 'logo-slider-wp' ), __( 'Tooltip', 'logo-slider-wp' ) ) ); ?></span>
                    </button>
                </span>
            </div>
            <div class="lgx-field-right">
<?php if ( $tooltip_locked ) : ?>
                <input type="text" id="lgx_tooltip_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[tooltip_text]" value="<?php echo esc_attr( $tooltip_text ); ?>" aria-describedby="lgx_tooltip_text_desc lgx_tooltip_text_pro_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" readonly data-pro-locked-input="true" />
                <span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="<?php esc_attr_e( 'Available in Logo Slider Pro', 'logo-slider-wp' ); ?>"></span>
                <span id="lgx_tooltip_text_pro_desc" class="screen-reader-text"><?php esc_html_e( 'Pro feature. Uses the Brand Name.', 'logo-slider-wp' ); ?></span>
<?php else : ?>
                <input type="text" id="lgx_tooltip_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[tooltip_text]" value="<?php echo esc_attr( $tooltip_text ); ?>" aria-describedby="lgx_tooltip_text_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" />
<?php endif; ?>
                <p id="lgx_tooltip_text_desc" class="lgx-field__description description lgx-info-note" hidden><?php esc_html_e( 'Automatically uses the Brand Name until you customize it. Displayed when visitors hover over the logo.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row<?php echo $alt_locked ? ' lgx-pro-locked-field' : ''; ?>">
            <div class="lgx-field-left">
                <span class="lgx-field__labelwrap">
                    <label class="lgx-field__label" for="lgx_alt_text"><?php esc_html_e( 'Alt Text', 'logo-slider-wp' ); ?></label>
                    <button type="button" class="lgx-field__info" aria-expanded="false" aria-controls="lgx_alt_text_desc">
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.25" fill="none" stroke="currentColor" stroke-width="1.5"/><rect x="9.1" y="8.6" width="1.8" height="5.6" rx=".9" fill="currentColor"/><circle cx="10" cy="6.1" r="1.1" fill="currentColor"/></svg>
                        <span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'More information: %s', 'logo-slider-wp' ), __( 'Alt Text', 'logo-slider-wp' ) ) ); ?></span>
                    </button>
                </span>
            </div>
            <div class="lgx-field-right">
<?php if ( $alt_locked ) : ?>
                <input type="text" id="lgx_alt_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[alt_text]" value="<?php echo esc_attr( $alt_text ); ?>" aria-describedby="lgx_alt_text_desc lgx_alt_text_pro_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" readonly data-pro-locked-input="true" />
                <span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="<?php esc_attr_e( 'Available in Logo Slider Pro', 'logo-slider-wp' ); ?>"></span>
                <span id="lgx_alt_text_pro_desc" class="screen-reader-text"><?php esc_html_e( 'Pro feature. Uses the Brand Name.', 'logo-slider-wp' ); ?></span>
<?php else : ?>
                <input type="text" id="lgx_alt_text" class="lgx-field__control regular-text" name="metaboxlogosliderwp[alt_text]" value="<?php echo esc_attr( $alt_text ); ?>" aria-describedby="lgx_alt_text_desc" placeholder="<?php esc_attr_e( 'Uses Brand Name by default', 'logo-slider-wp' ); ?>" />
<?php endif; ?>
                <p id="lgx_alt_text_desc" class="lgx-field__description description lgx-info-note" hidden><?php esc_html_e( 'Automatically uses the Brand Name until you customize it. Helps improve accessibility and SEO.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row<?php echo $company_locked ? ' lgx-pro-locked-field' : ''; ?>">
            <div class="lgx-field-left">
                <span class="lgx-field__labelwrap">
                    <label class="lgx-field__label" for="lgx_custom_brand_name"><?php esc_html_e( 'Custom Brand Name', 'logo-slider-wp' ); ?></label>
                    <button type="button" class="lgx-field__info" aria-expanded="false" aria-controls="lgx_custom_brand_name_desc">
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.25" fill="none" stroke="currentColor" stroke-width="1.5"/><rect x="9.1" y="8.6" width="1.8" height="5.6" rx=".9" fill="currentColor"/><circle cx="10" cy="6.1" r="1.1" fill="currentColor"/></svg>
                        <span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'More information: %s', 'logo-slider-wp' ), __( 'Custom Brand Name', 'logo-slider-wp' ) ) ); ?></span>
                    </button>
                </span>
            </div>
            <div class="lgx-field-right">
<?php if ( $company_locked ) : ?>
                <input type="text" id="lgx_custom_brand_name" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_name]" value="" aria-describedby="lgx_custom_brand_name_desc lgx_custom_brand_name_pro_desc" placeholder="<?php esc_attr_e( 'Uses the Brand Name', 'logo-slider-wp' ); ?>" readonly data-pro-locked-input="true" />
                <span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="<?php esc_attr_e( 'Available in Logo Slider Pro', 'logo-slider-wp' ); ?>"></span>
                <span id="lgx_custom_brand_name_pro_desc" class="screen-reader-text"><?php esc_html_e( 'Pro feature. Uses the Brand Name.', 'logo-slider-wp' ); ?></span>
<?php else : ?>
                <input type="text" id="lgx_custom_brand_name" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_name]" value="<?php echo esc_attr( $company_name ); ?>" aria-describedby="lgx_custom_brand_name_desc" placeholder="<?php esc_attr_e( 'Leave empty to use the Brand Name', 'logo-slider-wp' ); ?>" />
<?php endif; ?>
                <p id="lgx_custom_brand_name_desc" class="lgx-field__description description lgx-info-note" hidden><?php esc_html_e( 'Enter a different brand name to display instead of the original Brand Name.', 'logo-slider-wp' ); ?></p>
            </div>
        </div>

        <div class="lgx-field lgx-field-row">
            <div class="lgx-field-left">
                <span class="lgx-field__labelwrap">
                    <label class="lgx-field__label" for="lgx_company_desc"><?php esc_html_e( 'Description (Optional)', 'logo-slider-wp' ); ?></label>
                    <button type="button" class="lgx-field__info" aria-expanded="false" aria-controls="lgx_company_desc_desc">
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.25" fill="none" stroke="currentColor" stroke-width="1.5"/><rect x="9.1" y="8.6" width="1.8" height="5.6" rx=".9" fill="currentColor"/><circle cx="10" cy="6.1" r="1.1" fill="currentColor"/></svg>
                        <span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'More information: %s', 'logo-slider-wp' ), __( 'Description (Optional)', 'logo-slider-wp' ) ) ); ?></span>
                    </button>
                </span>
            </div>
            <div class="lgx-field-right">
                <textarea rows="3" id="lgx_company_desc" class="lgx-field__control regular-text" name="metaboxlogosliderwp[company_desc]" aria-describedby="lgx_company_desc_desc" placeholder="<?php esc_attr_e( 'Add a short description about this brand...', 'logo-slider-wp' ); ?>"><?php echo esc_textarea( $company_desc ); ?></textarea>
                <p id="lgx_company_desc_desc" class="lgx-field__description description lgx-info-note" hidden><?php esc_html_e( 'Displayed only in layouts that support logo descriptions.', 'logo-slider-wp' ); ?></p>
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
</div><?php if ( ! $is_pro ) : ?>

<div class="lgx-logo-pro-modal-overlay" id="lgx-logo-pro-modal-overlay" style="display:none;" aria-hidden="true"></div>
<div class="lgx-logo-pro-modal" id="lgx-logo-pro-modal" role="dialog" aria-modal="true" aria-labelledby="lgx-logo-pro-modal-title" style="display:none;" aria-hidden="true">
    <div class="lgx-logo-pro-modal__content">
        <h3 class="lgx-logo-pro-modal__title" id="lgx-logo-pro-modal-title">
            <span class="dashicons dashicons-star-filled lgx-logo-pro-star" aria-hidden="true"></span>
            <span id="lgx-logo-pro-modal-title-text"><?php esc_html_e( 'Pro Feature', 'logo-slider-wp' ); ?></span>
        </h3>
        <p class="lgx-logo-pro-modal__text" id="lgx-logo-pro-modal-text"></p>
        <div class="lgx-logo-pro-modal__actions">
            <a href="<?php echo esc_url( 'https://logichunt.com/product/wordpress-logo-slider/' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary lgx-btn-upgrade" id="lgx-logo-pro-modal-upgrade">
                <?php esc_html_e( 'Get Logo Slider Pro', 'logo-slider-wp' ); ?>
            </a>
            <button type="button" class="button-link lgx-logo-pro-modal__cancel" id="lgx-logo-pro-modal-close">
                <?php esc_html_e( 'Maybe later', 'logo-slider-wp' ); ?>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>