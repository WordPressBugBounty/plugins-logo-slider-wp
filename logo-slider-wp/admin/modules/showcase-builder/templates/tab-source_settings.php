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
 * 6.0.0). Since 6.1.0 the source registry knows two sources, Logo Library and
 * WooCommerce Brands. T-213 (Stage 2): a Content Source panel comes first
 * (one radio card per source plus the one hidden source input), and the
 * Logo Source and WooCommerce Brand panels show only while their source is
 * the current one. A stored value that is not a registered source id shows
 * as an extra, disabled "Unknown source" card and neither source panel; the
 * stored value itself is never changed unless the user picks a source.
 *
 * @since 6.0.3 (007R-008)
 * @since 6.0.4 (007R-008 revision: source-dependent panel moved to top; titles made source-specific)
 * @since 6.0.5 (007R-008 revision: Global split into Query Settings / Content & Link Settings / Logo Settings)
 * @since 6.0.6 (B69: Essential moved first; source-dependent panel title reads from config.php)
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

// T-213 (Stage 2, S-01 to S-07, S-10, A1, A2): the Content Source panel and
// the source panels. $current_source_raw is the raw stored source value, set
// by builder-wrapper.php (null when no key is stored).
$lgx_source_raw      = isset( $current_source_raw ) ? $current_source_raw : null;
$lgx_source_registry = lgx_ls_get_registered_sources();
// Missing, empty, whitespace-only and non-text values check Logo Library (S-04).
$lgx_source_is_blank = ( null === $lgx_source_raw ) || ! is_string( $lgx_source_raw ) || '' === trim( $lgx_source_raw );
$lgx_source_unknown  = ! $lgx_source_is_blank && ! lgx_ls_is_registered_source( $lgx_source_raw );

// The checked card: Logo Library for a blank value, the stored id for a
// registered one, the extra unknown card (value '') for anything else (A2).
if ( $lgx_source_is_blank ) {
	$lgx_checked_source = 'logo_library';
} elseif ( $lgx_source_unknown ) {
	$lgx_checked_source = '';
} else {
	$lgx_checked_source = $lgx_source_raw;
}

// A1: the hidden input posts only a non-blank stored string, exactly as stored
// (double encoding keeps a stored entity such as "&#108;" unchanged, B175).
// Missing, blank and non-text values render it disabled, so nothing is posted.
$lgx_source_hidden_on    = is_string( $lgx_source_raw ) && ! $lgx_source_is_blank;
$lgx_source_hidden_value = $lgx_source_hidden_on ? htmlspecialchars( $lgx_source_raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true ) : '';

$lgx_source_descs = array(
	'logo_library'      => __( 'Logos you add to this plugin', 'logo-slider-wp' ),
	'woo_product_brand' => __( 'Brand logos from your shop', 'logo-slider-wp' ),
);

$lgx_brand_unavailable_now = ( 'woo_product_brand' === $lgx_checked_source && empty( $lgx_source_registry['woo_product_brand']['available'] ) );

$lgx_content_source_label = isset( $tab_data['groups']['content_source']['label'] )
	? $tab_data['groups']['content_source']['label']
	: __( 'Content Source', 'logo-slider-wp' );

$lgx_source_dependent_label = isset( $tab_data['groups']['source_dependent']['label'] )
	? $tab_data['groups']['source_dependent']['label']
	: __( 'Logo Source', 'logo-slider-wp' );

$lgx_brand_panel_label = isset( $tab_data['groups']['woo_brand_settings']['label'] )
	? $tab_data['groups']['woo_brand_settings']['label']
	: __( 'WooCommerce Brand', 'logo-slider-wp' );
?>

<div id="lgx-group-source_settings-content_source" class="lgx-ui-panelbody is-open lgx-content-source-panel">
	<div class="lgx-ui-panelbody-header" aria-expanded="true">
		<span class="lgx-ui-panelbody-title"><?php echo esc_html( $lgx_content_source_label ); ?></span>
	</div>
	<div class="lgx-ui-panelbody-content">
		<fieldset class="lgx-source-cards">
			<legend class="screen-reader-text"><?php echo esc_html( $lgx_content_source_label ); ?></legend>
			<?php
			foreach ( $lgx_source_registry as $lgx_card_id => $lgx_card ) :
				$lgx_card_checked  = ( $lgx_card_id === $lgx_checked_source );
				$lgx_card_disabled = empty( $lgx_card['available'] );
				$lgx_card_reason   = '';
				if ( $lgx_card_disabled ) {
					$lgx_card_reason = $lgx_card_checked
						? __( 'WooCommerce or its brand feature is not active. The Brand settings are kept.', 'logo-slider-wp' )
						: __( 'Needs WooCommerce with the Brands feature.', 'logo-slider-wp' );
				}
				$lgx_card_reason_id = 'lgx-source-card-' . sanitize_html_class( $lgx_card_id ) . '-reason';
				?>
				<label class="lgx-source-card<?php echo $lgx_card_checked ? ' is-selected' : ''; ?><?php echo $lgx_card_disabled ? ' is-disabled' : ''; ?>">
					<input type="radio" name="lgx_source_picker" value="<?php echo esc_attr( $lgx_card_id ); ?>"<?php checked( $lgx_card_checked ); ?><?php disabled( $lgx_card_disabled ); ?><?php echo ( '' !== $lgx_card_reason ) ? ' aria-describedby="' . esc_attr( $lgx_card_reason_id ) . '"' : ''; ?>>
					<span class="lgx-source-card__body">
						<span class="lgx-source-card__title">
							<?php echo esc_html( $lgx_card['label'] ); ?>
							<?php if ( $lgx_card_disabled && $lgx_card_checked ) : ?>
								<span class="lgx-source-tag"><?php esc_html_e( 'Unavailable', 'logo-slider-wp' ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( isset( $lgx_source_descs[ $lgx_card_id ] ) ) : ?>
							<span class="lgx-source-card__desc"><?php echo esc_html( $lgx_source_descs[ $lgx_card_id ] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $lgx_card_reason ) : ?>
							<span class="lgx-source-card__reason" id="<?php echo esc_attr( $lgx_card_reason_id ); ?>"><?php echo esc_html( $lgx_card_reason ); ?></span>
						<?php endif; ?>
					</span>
				</label>
			<?php endforeach; ?>
			<?php
			if ( $lgx_source_unknown ) :
				$lgx_unknown_source = lgx_ls_describe_source( $lgx_source_raw );
				?>
				<label class="lgx-source-card is-selected is-disabled">
					<input type="radio" name="lgx_source_picker" value="" checked="checked" disabled="disabled" aria-describedby="lgx-source-card-unknown-reason">
					<span class="lgx-source-card__body">
						<span class="lgx-source-card__title">
							<?php echo esc_html( $lgx_unknown_source['label'] ); ?>
							<span class="lgx-source-tag"><?php esc_html_e( 'Not recognised', 'logo-slider-wp' ); ?></span>
						</span>
						<span class="lgx-source-card__reason" id="lgx-source-card-unknown-reason"><?php esc_html_e( 'Kept as stored until you pick another source.', 'logo-slider-wp' ); ?></span>
					</span>
				</label>
			<?php endif; ?>
		</fieldset>
		<p class="lgx-source-panel__note"><?php esc_html_e( 'The WooCommerce Brand entry appears here when you pick WooCommerce Brands. Switching source keeps every other setting.', 'logo-slider-wp' ); ?></p>
		<?php /* The one posted source value (S-02). The radios above use lgx_source_picker, which the save never reads. */ ?>
		<input type="hidden" id="lgx_lswp_content_source_field" name="meta_lgx_lsp_shortcodes[lgx_lswp_content_source]" value="<?php echo $lgx_source_hidden_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above with htmlspecialchars(). ?>"<?php disabled( ! $lgx_source_hidden_on ); ?>>
	</div>
</div>

<?php
if ( class_exists( 'Lgx_Showcase_Renderer' ) && isset( $tab_data['groups']['content_link_settings'] ) ) {
	Lgx_Showcase_Renderer::render_group( 'content_link_settings', $tab_data['groups']['content_link_settings'], 'source_settings' );
}
?>

<div id="lgx-group-source_settings-source_dependent" class="lgx-ui-panelbody is-open lgx-source-dependent-panel<?php echo ( 'logo_library' !== $lgx_checked_source ) ? ' lgx-panelbody--type-hidden' : ''; ?>" data-source-panel="logo_library">
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
?>

<?php if ( class_exists( 'Lgx_Showcase_Renderer' ) && ! empty( $tab_data['groups']['woo_brand_settings']['fields'] ) ) : ?>
<?php /* T-213 (S-06, S-07, S-10): the WooCommerce Brand panel, shown only while Brand is the current source. Hidden with CSS only, so its fields are always posted. */ ?>
<div id="lgx-group-source_settings-woo_brand_settings" class="lgx-ui-panelbody is-open lgx-source-dependent-panel<?php echo ( 'woo_product_brand' !== $lgx_checked_source ) ? ' lgx-panelbody--type-hidden' : ''; ?>" data-source-panel="woo_product_brand">
	<div class="lgx-ui-panelbody-header" aria-expanded="true">
		<span class="lgx-ui-panelbody-title"><?php echo esc_html( $lgx_brand_panel_label ); ?></span>
	</div>
	<div class="lgx-ui-panelbody-content">
		<?php if ( $lgx_brand_unavailable_now ) : ?>
			<p class="lgx-source-notice"><?php esc_html_e( 'WooCommerce Brands is not available right now. These settings are kept and stay editable. The showcase shows no logos until WooCommerce is active again.', 'logo-slider-wp' ); ?></p>
		<?php endif; ?>
		<?php
		/*
		 * T-215 (S-20 to S-25): the brand list for the Brands picker and the
		 * no-logo note, read once (one term query plus the provider's batch
		 * logo preload). null = taxonomy not available: the Brands field then
		 * keeps its existing text field and no picker is shown.
		 */
		$lgx_brand_list = ( class_exists( 'Logo_Slider_WP_Source_Brand' ) && method_exists( 'Logo_Slider_WP_Source_Brand', 'get_brand_list_for_builder' ) )
			? Logo_Slider_WP_Source_Brand::get_brand_list_for_builder()
			: null;
		$lgx_brand_meta = ( isset( $saved_meta ) && is_array( $saved_meta ) ) ? $saved_meta : array();

		$lgx_brand_no_logo_count = 0;
		if ( is_array( $lgx_brand_list ) ) {
			foreach ( $lgx_brand_list as $lgx_brand_item ) {
				if ( ! $lgx_brand_item['has_logo'] ) {
					$lgx_brand_no_logo_count++;
				}
			}
		}
		?>
		<div class="lgx_form_table">
			<?php foreach ( $tab_data['groups']['woo_brand_settings']['fields'] as $lgx_brand_field ) : ?>
				<?php
				$lgx_brand_field_id = isset( $lgx_brand_field['id'] ) ? $lgx_brand_field['id'] : '';

				if ( 'lgx_woo_product_brand_term_ids' !== $lgx_brand_field_id || ! is_array( $lgx_brand_list ) ) {
					Lgx_Showcase_Renderer::render_field( $lgx_brand_field );

					// S-25: the no-logo note under "Brands without a logo".
					if ( 'lgx_woo_product_brand_no_logo' === $lgx_brand_field_id && is_array( $lgx_brand_list ) && $lgx_brand_no_logo_count > 0 ) {
						echo '<p class="lgx-source-panel__note lgx-brand-nologo-note">' . esc_html(
							sprintf(
								/* translators: 1: number of brands without a logo, 2: number of all brands. */
								_n( '%1$d of your %2$d brands has no logo.', '%1$d of your %2$d brands have no logo.', $lgx_brand_no_logo_count, 'logo-slider-wp' ),
								$lgx_brand_no_logo_count,
								count( $lgx_brand_list )
							)
						) . '</p>';
					}

					// T-217b (spec section 5, Scope note): only with the picker, the row it describes.
					if ( 'lgx_woo_product_brand_scope' === $lgx_brand_field_id && is_array( $lgx_brand_list ) ) {
						echo '<p class="lgx-source-panel__note lgx-brand-scope-note">' . esc_html__( 'With "All brands" the list below is hidden and your selection is kept.', 'logo-slider-wp' ) . '</p>';
					}
					continue;
				}

				/*
				 * S-20 to S-24: the Brands picker. The checkboxes, the search box and the
				 * buttons have no name; the one hidden input below carries the posted value.
				 * It starts with the exact stored string (P2), so an untouched form posts
				 * exactly what is stored; a missing or non-text stored value renders it
				 * disabled (nothing posted, the save keeps the stored value).
				 */
				$lgx_picker_locked   = ! function_exists( 'lgx_ls_is_pro' ) || ! lgx_ls_is_pro();
				$lgx_term_ids_raw    = array_key_exists( 'lgx_woo_product_brand_term_ids', $lgx_brand_meta ) ? $lgx_brand_meta['lgx_woo_product_brand_term_ids'] : null;
				$lgx_term_ids_on     = is_string( $lgx_term_ids_raw );
				$lgx_term_ids_value  = $lgx_term_ids_on ? htmlspecialchars( $lgx_term_ids_raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true ) : '';

				// The stored ids, parsed with the save's own rule (Q15).
				$lgx_stored_ids = array();
				if ( function_exists( 'lgx_ls_normalise_term_ids' ) ) {
					$lgx_term_ids_clean = lgx_ls_normalise_term_ids( $lgx_term_ids_raw );
					if ( '' !== $lgx_term_ids_clean ) {
						$lgx_stored_ids = explode( ',', $lgx_term_ids_clean );
					}
				}
				$lgx_known_ids = array();
				foreach ( $lgx_brand_list as $lgx_brand_item ) {
					$lgx_known_ids[ (string) $lgx_brand_item['term_id'] ] = true;
				}
				$lgx_missing_ids = array();
				foreach ( $lgx_stored_ids as $lgx_stored_id ) {
					if ( ! isset( $lgx_known_ids[ $lgx_stored_id ] ) ) {
						$lgx_missing_ids[] = $lgx_stored_id;
					}
				}
				$lgx_ticked_lookup = array_flip( $lgx_stored_ids );
				$lgx_ticked_count  = count( $lgx_stored_ids ) - count( $lgx_missing_ids );
				$lgx_brand_total   = count( $lgx_brand_list );

				// S-23: "All brands" hides the row (CSS only). A missing scope reads as its default, All brands.
				$lgx_scope_raw     = array_key_exists( 'lgx_woo_product_brand_scope', $lgx_brand_meta ) ? $lgx_brand_meta['lgx_woo_product_brand_scope'] : 'all';
				$lgx_picker_hidden = ( 'all' === $lgx_scope_raw );

				// S-51 (T-217b): one _n() pair. The script fills %d in these two forms after a Remove.
				$lgx_missing_count = count( $lgx_missing_ids );
				/* translators: %d: number of selected brands that were not found. */
				$lgx_missing_one  = _n( '%d selected brand was not found', '%d selected brands were not found', 1, 'logo-slider-wp' );
				/* translators: %d: number of selected brands that were not found. */
				$lgx_missing_many = _n( '%d selected brand was not found', '%d selected brands were not found', 2, 'logo-slider-wp' );
				?>
				<div class="lgx-field lgx-field--row lgx-brand-picker-field<?php echo $lgx_picker_locked ? ' lgx-pro-locked-field' : ''; ?><?php echo $lgx_picker_hidden ? ' lgx-panelbody--type-hidden' : ''; ?>" id="lgx-brand-picker-row">
					<div class="lgx-field__label">
						<label id="lgx-brand-picker-label"<?php echo $lgx_picker_locked ? '' : ' for="lgx-brand-picker-search"'; ?>><?php echo esc_html( isset( $lgx_brand_field['label'] ) ? $lgx_brand_field['label'] : '' ); ?></label>
						<?php if ( ! empty( $lgx_brand_field['desc'] ) ) : ?>
							<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="<?php esc_attr_e( 'More information', 'logo-slider-wp' ); ?>" aria-expanded="false" aria-controls="lgx-help-popover" data-help="<?php echo esc_attr( $lgx_brand_field['desc'] ); ?>"></button>
						<?php endif; ?>
					</div>
					<div class="lgx-field__control">
						<div class="lgx-brand-picker"
							role="group"
							aria-labelledby="lgx-brand-picker-label"
							data-picker-for="lgx_woo_product_brand_term_ids"
							data-locked="<?php echo $lgx_picker_locked ? '1' : '0'; ?>"
							data-text-counter="<?php /* translators: 1: number of selected brands, 2: number of all brands. */ echo esc_attr__( '%1$d of %2$d selected', 'logo-slider-wp' ); ?>"
							data-text-missing-one="<?php echo esc_attr( $lgx_missing_one ); ?>"
							data-text-missing-many="<?php echo esc_attr( $lgx_missing_many ); ?>">
							<?php if ( ! $lgx_picker_locked ) : ?>
								<label class="screen-reader-text" for="lgx-brand-picker-search"><?php esc_html_e( 'Search brands', 'logo-slider-wp' ); ?></label>
								<input type="search" id="lgx-brand-picker-search" class="lgx-input lgx-brand-picker__search" placeholder="<?php esc_attr_e( 'Search brands', 'logo-slider-wp' ); ?>" autocomplete="off">
							<?php endif; ?>
							<div class="lgx-brand-picker__bar">
								<span class="lgx-brand-picker__count" aria-live="polite"><?php echo esc_html( sprintf( /* translators: 1: number of selected brands, 2: number of all brands. */ __( '%1$d of %2$d selected', 'logo-slider-wp' ), $lgx_ticked_count, $lgx_brand_total ) ); ?></span>
								<button type="button" class="button-link lgx-brand-picker__clear"<?php disabled( $lgx_picker_locked ); ?>><?php esc_html_e( 'Clear selection', 'logo-slider-wp' ); ?></button>
							</div>
							<div class="lgx-brand-picker__box">
								<?php foreach ( $lgx_brand_list as $lgx_brand_item ) : ?>
									<label class="lgx-brand-picker__item" data-brand-name="<?php echo esc_attr( function_exists( 'mb_strtolower' ) ? mb_strtolower( $lgx_brand_item['name'], 'UTF-8' ) : strtolower( $lgx_brand_item['name'] ) ); ?>">
										<input type="checkbox" class="lgx-brand-picker__input" value="<?php echo esc_attr( $lgx_brand_item['term_id'] ); ?>"<?php checked( isset( $lgx_ticked_lookup[ (string) $lgx_brand_item['term_id'] ] ) ); ?>>
										<span class="lgx-brand-picker__name"><?php echo esc_html( $lgx_brand_item['name'] ); ?></span>
										<?php if ( ! $lgx_brand_item['has_logo'] ) : ?>
											<span class="lgx-source-tag"><?php esc_html_e( 'no logo', 'logo-slider-wp' ); ?></span>
										<?php endif; ?>
									</label>
								<?php endforeach; ?>
							</div>
							<div class="lgx-brand-picker__missing"<?php echo empty( $lgx_missing_ids ) ? ' hidden' : ''; ?>>
								<p class="lgx-brand-picker__missing-title"><?php echo esc_html( sprintf( /* translators: %d: number of selected brands that were not found. */ _n( '%d selected brand was not found', '%d selected brands were not found', $lgx_missing_count, 'logo-slider-wp' ), $lgx_missing_count ) ); ?></p>
								<ul class="lgx-brand-picker__missing-list">
									<?php foreach ( $lgx_missing_ids as $lgx_missing_id ) : ?>
										<li class="lgx-brand-picker__missing-item" data-brand-id="<?php echo esc_attr( $lgx_missing_id ); ?>">
											<span><?php echo esc_html( sprintf( /* translators: %d: brand id. */ __( 'Brand #%d · not found', 'logo-slider-wp' ), $lgx_missing_id ) ); ?></span>
											<button type="button" class="button-link lgx-brand-picker__remove" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: brand id. */ __( 'Remove brand %d', 'logo-slider-wp' ), $lgx_missing_id ) ); ?>"<?php disabled( $lgx_picker_locked ); ?>><?php esc_html_e( 'Remove', 'logo-slider-wp' ); ?></button>
										</li>
									<?php endforeach; ?>
								</ul>
								<p class="lgx-source-panel__note lgx-brand-picker__missing-note"><?php esc_html_e( 'They are not shown on the page. They stay in the saved list until you remove them.', 'logo-slider-wp' ); ?></p>
							</div>
						</div>
						<input type="hidden" id="lgx_woo_product_brand_term_ids" name="<?php echo esc_attr( $lgx_brand_field['name'] ); ?>" value="<?php echo $lgx_term_ids_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above with htmlspecialchars(). ?>"<?php disabled( ! $lgx_term_ids_on ); ?>>
						<?php if ( $lgx_picker_locked ) : ?>
							<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="<?php esc_attr_e( 'Available in Logo Slider Pro', 'logo-slider-wp' ); ?>"></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php /* T-214 (spec section 5): Company URL hint, both editions. */ ?>
		<p class="lgx-source-panel__note lgx-brand-panel__note"><?php esc_html_e( 'Each brand logo links to its brand page when the Company URL switch in Essential is on.', 'logo-slider-wp' ); ?></p>
		<?php /* T-214 (S-09): Free note, Free edition only (same edition check as builder-wrapper.php). */ ?>
		<?php if ( ! function_exists( 'lgx_ls_is_pro' ) || ! lgx_ls_is_pro() ) : ?>
			<p class="lgx-source-panel__note lgx-brand-panel__note lgx-brand-panel__note--free"><span class="dashicons dashicons-lock lgx-brand-panel__note-icon" aria-hidden="true"></span><?php esc_html_e( 'These settings are part of Logo Slider Pro. Choices saved in Pro are shown and kept when you save here.', 'logo-slider-wp' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
