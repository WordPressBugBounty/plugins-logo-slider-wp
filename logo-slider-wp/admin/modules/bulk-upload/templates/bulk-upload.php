<?php
/**
 * Bulk Upload page template view.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/bulk-upload/templates
 *
 * @var string   $page_title
 * @var string   $step_indicator_label
 * @var string   $step_1_label
 * @var string   $step_2_label
 * @var string   $step_3_label
 * @var string   $help_text
 * @var string   $select_images_button
 * @var string   $review_heading
 * @var string   $col_image
 * @var string   $col_filename
 * @var string   $col_brand_name
 * @var string   $col_details
 * @var string   $col_remove
 * @var bool     $is_pro
 * @var string   $common_settings_heading
 * @var string   $categories_label
 * @var string   $categories_help
 * @var string   $categories_empty
 * @var string   $status_label
 * @var string   $status_draft_label
 * @var string   $status_publish_label
 * @var string   $status_draft_only_note
 * @var string   $create_logos_button
 * @var string   $stop_button
 * @var string   $progress_label
 * @var string   $results_heading
 * @var string   $retry_failed_button
 * @var string   $select_more_button
 * @var string   $view_all_logos_button
 * @var array    $categories
 * @var bool     $can_publish
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lgx_bulk_all_logos_url = admin_url( 'edit.php?post_type=logosliderwp' );
?>
<div class="wrap lgx-bulk-upload__wrap">
	<p class="lgx-bulk-upload__back">
		<a id="lgx-bulk-upload-back-link" class="lgx-bulk-upload__back-link" href="<?php echo esc_url( $lgx_bulk_all_logos_url ); ?>">&larr; <?php esc_html_e( 'All Logos', 'logo-slider-wp' ); ?></a>
	</p>
	<h1 class="lgx-bulk-upload__title"><?php echo esc_html( $page_title ); ?></h1>

	<nav class="lgx-step-indicator lgx-bulk-upload__steps" aria-label="<?php echo esc_attr( $step_indicator_label ); ?>">
		<ol class="lgx-step-indicator__list lgx-bulk-upload__steps-list">
			<li class="lgx-step-indicator__step lgx-step-indicator__step--current lgx-bulk-upload__step lgx-bulk-upload__step--current" data-step="1" aria-current="step">
				<span class="lgx-step-indicator__badge lgx-bulk-upload__step-badge" aria-hidden="true">1</span>
				<span class="lgx-step-indicator__label lgx-bulk-upload__step-label"><?php echo esc_html( $step_1_label ); ?></span>
			</li>
			<li class="lgx-step-indicator__step lgx-step-indicator__step--pending lgx-bulk-upload__step lgx-bulk-upload__step--pending" data-step="2">
				<span class="lgx-step-indicator__badge lgx-bulk-upload__step-badge" aria-hidden="true">2</span>
				<span class="lgx-step-indicator__label lgx-bulk-upload__step-label"><?php echo esc_html( $step_2_label ); ?></span>
			</li>
			<li class="lgx-step-indicator__step lgx-step-indicator__step--pending lgx-bulk-upload__step lgx-bulk-upload__step--pending" data-step="3">
				<span class="lgx-step-indicator__badge lgx-bulk-upload__step-badge" aria-hidden="true">3</span>
				<span class="lgx-step-indicator__label lgx-bulk-upload__step-label"><?php echo esc_html( $step_3_label ); ?></span>
			</li>
		</ol>
	</nav>

	<div id="lgx-bulk-upload-announcer" class="screen-reader-text lgx-bulk-upload__announcer" aria-live="polite" aria-atomic="true"></div>

	<div id="lgx-bulk-upload-alert" class="lgx-bulk-upload__alert" role="alert" aria-live="assertive" hidden="hidden"></div>

	<!-- Step 1: Select images (large dashed card). -->
	<div id="lgx-bulk-upload-select" class="lgx-bulk-upload__select">
		<div class="lgx-bulk-upload__picker-card">
			<svg class="lgx-bulk-upload__picker-icon" width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
				<rect x="5" y="5" width="38" height="38" rx="4" stroke="currentColor" stroke-width="3"/>
				<circle cx="17" cy="17" r="4" stroke="currentColor" stroke-width="3"/>
				<path d="M6 36l11-11 8 8 6-6 11 11" stroke="currentColor" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
			</svg>
			<h2 class="lgx-bulk-upload__picker-title"><?php esc_html_e( 'Select or upload your logo images', 'logo-slider-wp' ); ?></h2>
			<p class="lgx-bulk-upload__help"><?php echo esc_html( $help_text ); ?></p>
			<button type="button" id="lgx-bulk-upload-select-btn" class="button button-primary lgx-button lgx-button--primary lgx-bulk-upload__select-btn">
				<?php echo esc_html( $select_images_button ); ?>
			</button>
			<p class="lgx-bulk-upload__picker-note"><?php esc_html_e( 'Uses the image types your site allows. No limit on the number of images.', 'logo-slider-wp' ); ?></p>
		</div>
		<p class="lgx-bulk-upload__tip">
			<strong><?php esc_html_e( 'Tip:', 'logo-slider-wp' ); ?></strong>
			<?php
			printf(
				/* translators: 1: example file name, 2: example Brand Name made from that file name */
				esc_html__( 'the file name becomes the Brand Name (for example %1$s → “%2$s”). You can change every name in the next step.', 'logo-slider-wp' ),
				'<code>harbor-and-co.png</code>',
				esc_html__( 'Harbor And Co', 'logo-slider-wp' )
			);
			?>
		</p>
	</div>

	<!-- Step 2: Review. -->
	<div id="lgx-bulk-upload-review" class="lgx-bulk-upload__review" role="region" aria-labelledby="lgx-bulk-upload-review-heading" hidden="hidden">
		<div class="lgx-card lgx-bulk-upload__settings" role="region" aria-labelledby="lgx-bulk-upload-settings-heading">
			<h2 id="lgx-bulk-upload-settings-heading" class="screen-reader-text"><?php echo esc_html( $common_settings_heading ); ?></h2>
			<div class="lgx-bulk-upload__settings-body">
				<div class="lgx-field lgx-bulk-upload__field lgx-bulk-upload__field--categories">
					<?php if ( ! empty( $categories ) ) : ?>
						<fieldset class="lgx-bulk-upload__fieldset" aria-describedby="lgx-bulk-upload-categories-desc">
							<legend class="lgx-field__label lgx-bulk-upload__legend"><?php echo esc_html( $categories_label ); ?></legend>
							<div class="lgx-bulk-upload__term-list">
								<?php foreach ( $categories as $term_item ) : ?>
									<?php
									$term_id_val   = is_object( $term_item ) ? (int) $term_item->term_id : (int) $term_item['term_id'];
									$term_name_val = is_object( $term_item ) ? (string) $term_item->name : (string) $term_item['name'];
									$term_input_id = 'lgx-bulk-upload-term-' . $term_id_val;
									?>
									<label class="lgx-bulk-upload__term-item" for="<?php echo esc_attr( $term_input_id ); ?>">
										<input
											type="checkbox"
											id="<?php echo esc_attr( $term_input_id ); ?>"
											class="lgx-bulk-upload__term-checkbox"
											name="lgx_bulk_terms[]"
											value="<?php echo esc_attr( (string) $term_id_val ); ?>"
										/>
										<span class="lgx-bulk-upload__term-name"><?php echo esc_html( $term_name_val ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
							<p id="lgx-bulk-upload-categories-desc" class="lgx-field__description lgx-bulk-upload__field-desc"><?php echo esc_html( $categories_help ); ?></p>
						</fieldset>
					<?php else : ?>
						<span class="lgx-field__label lgx-bulk-upload__label"><?php echo esc_html( $categories_label ); ?></span>
						<p class="lgx-field__description lgx-bulk-upload__categories-empty"><?php echo esc_html( $categories_empty ); ?></p>
					<?php endif; ?>
				</div>

				<div class="lgx-field lgx-bulk-upload__field lgx-bulk-upload__field--status">
					<fieldset id="lgx-bulk-upload-status" class="lgx-bulk-upload__fieldset">
						<legend class="lgx-field__label lgx-bulk-upload__legend"><?php echo esc_html( $status_label ); ?></legend>
						<div class="lgx-bulk-upload__status-options">
							<label class="lgx-bulk-upload__status-item" for="lgx-bulk-upload-status-draft">
								<input type="radio" id="lgx-bulk-upload-status-draft" class="lgx-bulk-upload__status-radio" name="lgx_bulk_status" value="draft" checked="checked" />
								<span class="lgx-bulk-upload__status-name"><?php echo esc_html( $status_draft_label ); ?></span>
							</label>
							<?php if ( ! empty( $can_publish ) ) : ?>
								<label class="lgx-bulk-upload__status-item" for="lgx-bulk-upload-status-publish">
									<input type="radio" id="lgx-bulk-upload-status-publish" class="lgx-bulk-upload__status-radio" name="lgx_bulk_status" value="publish" />
									<span class="lgx-bulk-upload__status-name"><?php echo esc_html( $status_publish_label ); ?></span>
								</label>
							<?php endif; ?>
						</div>
					</fieldset>
					<?php if ( empty( $can_publish ) ) : ?>
						<p id="lgx-bulk-upload-status-note" class="lgx-field__description lgx-bulk-upload__draft-only-note"><?php echo esc_html( $status_draft_only_note ); ?></p>
					<?php endif; ?>
					<div id="lgx-bulk-upload-publish-notice" class="lgx-bulk-upload__publish-notice" aria-live="polite" aria-atomic="true" hidden="hidden"></div>
				</div>
			</div>
		</div>

		<div class="lgx-bulk-upload__review-header">
			<h2 id="lgx-bulk-upload-review-heading" class="lgx-bulk-upload__review-title"><?php echo esc_html( $review_heading ); ?></h2>
			<button type="button" id="lgx-bulk-upload-add-btn" class="button-link lgx-bulk-upload__add-btn">+ <?php esc_html_e( 'Add more images', 'logo-slider-wp' ); ?></button>
		</div>

		<ul id="lgx-bulk-upload-counts" class="lgx-bulk-upload__counts"></ul>

		<div class="lgx-bulk-upload__review-card">
			<table role="table" class="lgx-table lgx-bulk-upload__table">
				<thead role="rowgroup">
					<tr role="row">
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-number"><?php esc_html_e( '#', 'logo-slider-wp' ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-image"><?php echo esc_html( $col_image ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-filename"><?php echo esc_html( $col_filename ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-brand"><?php echo esc_html( $col_brand_name ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-details"><?php echo esc_html( isset( $col_details ) ? $col_details : __( 'Details', 'logo-slider-wp' ) ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-check"><?php esc_html_e( 'Check', 'logo-slider-wp' ); ?></th>
						<th role="columnheader" scope="col" class="lgx-bulk-upload__col-remove"><span class="screen-reader-text"><?php echo esc_html( $col_remove ); ?></span></th>
					</tr>
				</thead>
				<tbody role="rowgroup" id="lgx-bulk-upload-tbody" class="lgx-bulk-upload__tbody"></tbody>
			</table>
		</div>

		<div id="lgx-bulk-upload-footer" class="lgx-bulk-upload__footer">
			<p id="lgx-bulk-upload-footer-text" class="lgx-bulk-upload__footer-text"></p>
			<div class="lgx-bulk-upload__footer-actions">
				<a id="lgx-bulk-upload-cancel-link" class="button button-secondary lgx-button lgx-button--secondary lgx-bulk-upload__cancel-link" href="<?php echo esc_url( $lgx_bulk_all_logos_url ); ?>"><?php esc_html_e( 'Cancel', 'logo-slider-wp' ); ?></a>
				<button type="button" id="lgx-bulk-upload-create-btn" class="button button-primary lgx-button lgx-button--primary lgx-bulk-upload__create-btn" disabled="disabled">
					<?php echo esc_html( $create_logos_button ); ?>
				</button>
			</div>
		</div>
	</div>

	<!-- Step 3: Creating (progress card, replaces the review area while a run is active). -->
	<div id="lgx-bulk-upload-creating" class="lgx-bulk-upload__creating" role="region" aria-labelledby="lgx-bulk-upload-creating-heading" hidden="hidden">
		<div class="lgx-bulk-upload__creating-header">
			<div class="lgx-bulk-upload__creating-header-row">
				<h2 id="lgx-bulk-upload-creating-heading" class="lgx-bulk-upload__creating-title"><?php esc_html_e( 'Creating logos...', 'logo-slider-wp' ); ?></h2>
				<button type="button" id="lgx-bulk-upload-stop-btn" class="button button-secondary lgx-button lgx-button--secondary lgx-bulk-upload__stop-btn">
					<?php echo esc_html( $stop_button ); ?>
				</button>
			</div>
			<div id="lgx-bulk-upload-progress" class="lgx-bulk-upload__progress-wrap">
				<div
					id="lgx-bulk-upload-progress-bar"
					class="lgx-progress-bar lgx-bulk-upload__progress-bar"
					role="progressbar"
					aria-valuemin="0"
					aria-valuemax="0"
					aria-valuenow="0"
					aria-label="<?php echo esc_attr( $progress_label ); ?>"
				>
					<div class="lgx-progress-bar__indicator lgx-bulk-upload__progress-indicator" style="width: 0%;"></div>
				</div>
			</div>
			<p id="lgx-bulk-upload-progress-text" class="lgx-bulk-upload__progress-text" aria-live="polite" aria-atomic="true"></p>
		</div>
		<p class="lgx-bulk-upload__creating-help"><?php esc_html_e( 'Logos are created a few at a time. Keep this page open until it finishes.', 'logo-slider-wp' ); ?></p>
		<ul id="lgx-bulk-upload-creating-list" class="lgx-bulk-upload__creating-list"></ul>
		<button type="button" id="lgx-bulk-upload-follow-btn" class="button button-primary lgx-button lgx-button--primary lgx-bulk-upload__follow-btn" hidden="hidden">
			<?php esc_html_e( 'Follow progress', 'logo-slider-wp' ); ?>
		</button>
	</div>

	<!-- Step 3: Done (results). -->
	<div id="lgx-bulk-upload-results" class="lgx-bulk-upload__results" role="region" aria-labelledby="lgx-bulk-upload-results-summary" hidden="hidden">
		<p id="lgx-bulk-upload-results-summary" class="lgx-bulk-upload__results-summary" tabindex="-1"></p>
		<div id="lgx-bulk-upload-stop-notice" class="lgx-bulk-upload__stop-notice" hidden="hidden"></div>

		<div class="lgx-bulk-upload__results-grid">
			<div class="lgx-bulk-upload__results-col lgx-bulk-upload__results-col--main">
				<section id="lgx-bulk-upload-created-card" class="lgx-bulk-upload__result-card lgx-bulk-upload__result-card--created" aria-labelledby="lgx-bulk-upload-created-heading" hidden="hidden">
					<h2 id="lgx-bulk-upload-created-heading" class="lgx-bulk-upload__result-card-title"></h2>
					<table class="lgx-bulk-upload__created-table">
						<thead>
							<tr>
								<th scope="col"><?php echo esc_html( $col_brand_name ); ?></th>
								<th scope="col"><?php echo esc_html( $status_label ); ?></th>
								<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Edit link', 'logo-slider-wp' ); ?></span></th>
							</tr>
						</thead>
						<tbody id="lgx-bulk-upload-created-body"></tbody>
					</table>
					<p id="lgx-bulk-upload-draft-note" class="lgx-bulk-upload__draft-note" hidden="hidden"><?php esc_html_e( 'Draft logos do not appear in showcases until you publish them.', 'logo-slider-wp' ); ?></p>
				</section>
			</div>
			<div class="lgx-bulk-upload__results-col lgx-bulk-upload__results-col--side">
				<section id="lgx-bulk-upload-skipped-card" class="lgx-bulk-upload__result-card lgx-bulk-upload__result-card--skipped" aria-labelledby="lgx-bulk-upload-skipped-heading" hidden="hidden">
					<h2 id="lgx-bulk-upload-skipped-heading" class="lgx-bulk-upload__result-card-title"></h2>
					<ul id="lgx-bulk-upload-skipped-list" class="lgx-bulk-upload__result-entries"></ul>
				</section>
				<section id="lgx-bulk-upload-failed-card" class="lgx-bulk-upload__result-card lgx-bulk-upload__result-card--failed" aria-labelledby="lgx-bulk-upload-failed-heading" hidden="hidden">
					<h2 id="lgx-bulk-upload-failed-heading" class="lgx-bulk-upload__result-card-title"></h2>
					<ul id="lgx-bulk-upload-failed-list" class="lgx-bulk-upload__result-entries"></ul>
					<button type="button" id="lgx-bulk-upload-retry-btn" class="button button-secondary lgx-button lgx-button--secondary lgx-bulk-upload__retry-btn" hidden="hidden">
						<?php echo esc_html( $retry_failed_button ); ?>
					</button>
				</section>
			</div>
		</div>

		<div class="lgx-bulk-upload__results-actions">
			<a id="lgx-bulk-upload-all-logos-link" class="button button-primary lgx-button lgx-button--primary lgx-bulk-upload__all-logos-link" href="<?php echo esc_url( $lgx_bulk_all_logos_url ); ?>">
				<?php echo esc_html( $view_all_logos_button ); ?>
			</a>
			<button type="button" id="lgx-bulk-upload-restart-btn" class="button button-secondary lgx-button lgx-button--secondary lgx-bulk-upload__restart-btn">
				<?php echo esc_html( $select_more_button ); ?>
			</button>
			<button type="button" id="lgx-bulk-upload-continue-btn" class="button button-secondary lgx-button lgx-button--secondary lgx-bulk-upload__continue-btn" hidden="hidden"></button>
		</div>
	</div>
</div>
