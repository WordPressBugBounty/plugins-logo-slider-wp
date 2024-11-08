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

$company_url         = isset( $fieldValues['company_url'] ) ? esc_url($fieldValues['company_url'] ): '';
$company_name = (! empty( $fieldValues['company_name']) ? esc_html(esc_js($fieldValues['company_name'])) : '');
//$company_name        = isset( $fieldValues['company_name'] ) ? esc_attr($fieldValues['company_name']) : '';
//$tooltip_text        = isset( $fieldValues['tooltip_text'] ) ? esc_attr($fieldValues['tooltip_text'] ): '';
$tooltip_text = (! empty( $fieldValues['tooltip_text']) ? esc_html(esc_js($fieldValues['tooltip_text'])) : '');
$company_desc        = isset( $fieldValues['company_desc'] ) ? esc_attr($fieldValues['company_desc'] ): '';


       

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="lgx_meta_box_wrapper lgx_meta_boxes_fields_logo_item ">
    <div class="lgx_meta_box_wrapper_inner">
        <table class="form-table lgx_form_table lgx_form_table_main">
            <tbody>

            <?php do_action( 'lgx_ls_logo_meta_fields_before', $fieldValues );  ?>

            <tr>
                <th valign="top">
                    <h4 class="lgx_app_meta_label"><label for="lgx_target_type"><?php _e( 'Brand Name', $this->plugin_name ) ?></label></h4>
                    <p class="lgx_input_desc lgx_app_meta_desc"><?php _e( 'Add Company/ Brand/ Client\'s name here.', $this->plugin_name ) ?></p>
                </th>
                <td>
                    <input type="text" name="metaboxlogosliderwp[company_name]"  style="width: 25%;" value='<?php echo esc_attr($company_name); ?>'/>
                </td>
            </tr>


            <tr valign="top">
                <th valign="top">
                    <h4 class="lgx_app_meta_label"><label for="lgx_target_type"><?php _e( 'Brand URL', $this->plugin_name ) ?></label></h4>
                    <p class="lgx_input_desc lgx_app_meta_desc"><?php _e( 'Add a Company/ Brand/ Client\'s URL to link up the item.', $this->plugin_name ) ?></p>
                </th>
                <td>
                    <input type="url" name="metaboxlogosliderwp[company_url]" style="width: 25%;" value='<?php echo esc_url($company_url); ?>'/>
                </td>
            </tr>

            <tr valign="top">
                <th valign="top">
                    <h4 class="lgx_app_meta_label"><label for="lgx_target_type"><?php _e( 'Description', $this->plugin_name ) ?></label></h4>
                    <p class="lgx_input_desc lgx_app_meta_desc"><?php _e( 'Add company or brand details here.', $this->plugin_name ) ?></p>
                </th>
                <td>
                    <textarea rows="5" cols="45"  name="metaboxlogosliderwp[company_desc]" style="width: 25%;" placeholder="Brand description"><?php echo esc_attr($company_desc); ?></textarea>
                </td>
            </tr>

            <tr valign="top">
                <th valign="top">
                    <h4 class="lgx_app_meta_label"><label for="lgx_target_type"><?php _e( 'Tooltip Text', $this->plugin_name ) ?></label></h4>
                    <p class="lgx_input_desc lgx_app_meta_desc"><?php _e( 'You can use either tooltip text or brand name as tooltip content.', $this->plugin_name ) ?></p>
                </th>
                <td>
                    <input type="text" name="metaboxlogosliderwp[tooltip_text]" style="width: 25%;" value='<?php echo esc_attr($tooltip_text); ?>'/>
                </td>
            </tr>

            <?php do_action( 'lgx_ls_logo_meta_fields_after', $fieldValues );  ?>

            </tbody>
        </table>
    </div>
</div>