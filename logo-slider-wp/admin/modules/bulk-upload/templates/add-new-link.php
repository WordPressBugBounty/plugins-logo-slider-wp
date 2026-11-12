<?php
/**
 * Bulk Upload link on the Add New Logo screen (Exception E2 / D10).
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/bulk-upload/templates
 *
 * @var string $add_new_prompt
 * @var string $add_new_link
 * @var string $page_url
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="lgx-bulk-upload__add-new-hint">
	<?php echo esc_html( $add_new_prompt ); ?>
	<a class="lgx-bulk-upload__add-new-link" href="<?php echo esc_url( $page_url ); ?>"><?php echo esc_html( $add_new_link ); ?></a>
</p>

