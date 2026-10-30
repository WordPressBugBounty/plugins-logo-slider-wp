<?php
/**
 * Dashboard template view.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/dashboard/templates
 *
 * @var array  $config
 * @var int    $logos_total
 * @var int    $logos_published
 * @var int    $logos_draft
 * @var int    $showcases_count
 * @var array  $diagnostics
 * @var string $version
 * @var string $screen_hook_suffix
 * @var array  $onboarding_steps
 * @var int    $onboarding_percentage
 * @var bool   $onboarding_collapsed
 * @var string $onboarding_status_message
 * @var array  $recent_logos
 * @var array  $recent_showcases
 * @var bool   $has_demo_content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap lgx-dashboard">
	<!-- Page Header -->
	<header class="lgx-dashboard__header">
		<div class="lgx-dashboard__hero-content">
			<div class="lgx-dashboard__brand-row">
				<a class="lgx-dashboard__brand-link" href="https://logichunt.com/" target="_blank" rel="noopener noreferrer">
					<img class="lgx-dashboard__brand-logo" src="<?php echo esc_url( LGX_LS_PLUGIN_ROOT_URL . 'admin/modules/img/logichunt-dashborad-logo.png' ); ?>" alt="LogicHunt">
				</a>
				<div class="lgx-dashboard__edition-status <?php echo ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) ? 'lgx-dashboard__edition-status--pro' : 'lgx-dashboard__edition-status--free'; ?>">
					<?php if ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) : ?>
						<span class="lgx-dashboard__edition-mark" aria-hidden="true">&#10003;</span>
						<span><?php echo esc_html( 'Activated' ); ?></span>
					<?php else : ?>
						<span class="lgx-dashboard__edition-mark" aria-hidden="true">&#9679;</span>
						<span><?php echo esc_html( 'Limited Edition' ); ?></span>
					<?php endif; ?>
				</div>
			</div>
			<h1 class="lgx-dashboard__title">
				<?php
				/*
				 * Finding A fix (display labeling only — no capability logic
				 * changes here): use the existing edition helper so Free
				 * shows "Logo Slider WP" and Pro shows "Logo Slider WP Pro",
				 * instead of always hardcoding the Pro name regardless of
				 * which edition is actually running.
				 */
				if ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) {
					esc_html_e( 'Logo Slider WP Pro', 'logo-slider-wp' );
				} else {
					esc_html_e( 'Logo Slider WP', 'logo-slider-wp' );
				}
				?>
				<span class="lgx-dashboard__version">v<?php echo esc_html( $version ); ?></span>
			</h1>
			<p class="lgx-dashboard__subtitle">
				<?php esc_html_e( 'Welcome to your Logo Slider administration dashboard. Easily manage company logos, design grid layouts, and construct responsive brand carousels.', 'logo-slider-wp' ); ?>
			</p>
		</div>
	</header>

	<!-- Page Content Wrapper -->
	<div class="lgx-dashboard__content">
		<div class="lgx-grid">
			<!-- Column: Quick Actions & Help (Two-Thirds Column) -->
			<div class="lgx-grid__column lgx-grid__column--two-thirds">
				<!-- Getting Started Card -->
				<section class="lgx-card lgx-card--onboarding" role="region" aria-labelledby="lgx-card-onboarding-title">
					<div class="lgx-card__header lgx-card__header--flex">
						<h2 id="lgx-card-onboarding-title" class="lgx-card__title">
							<?php esc_html_e( 'Getting Started', 'logo-slider-wp' ); ?>
						</h2>
						<div class="lgx-onboarding__progress">
							<span class="lgx-onboarding__percentage"><?php echo esc_html( $onboarding_percentage ); ?>%</span>
							<div class="lgx-progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $onboarding_percentage ); ?>" aria-valuemin="0" aria-valuemax="100">
								<div class="lgx-progress-bar__fill" style="width: <?php echo esc_attr( $onboarding_percentage ); ?>%;"></div>
							</div>
							<button type="button" id="lgx-dashboard-onboarding-toggle" class="lgx-button lgx-button--secondary lgx-dashboard-onboarding-toggle" aria-expanded="<?php echo esc_attr( $onboarding_collapsed ? 'false' : 'true' ); ?>" aria-controls="lgx-dashboard-onboarding-steps" data-collapsed="<?php echo esc_attr( $onboarding_collapsed ? 'yes' : 'no' ); ?>">
								<?php echo esc_html( $onboarding_collapsed ? __( 'Show steps', 'logo-slider-wp' ) : __( 'Hide steps', 'logo-slider-wp' ) ); ?>
							</button>
						</div>
					</div>
					<div class="lgx-card__body">
						<p id="lgx-dashboard-onboarding-status" class="lgx-onboarding-status"<?php echo $onboarding_collapsed ? '' : ' hidden'; ?>>
							<?php echo esc_html( $onboarding_status_message ); ?>
						</p>
						<div id="lgx-dashboard-onboarding-steps" class="lgx-onboarding-steps-wrapper" data-expanded="<?php echo esc_attr( $onboarding_collapsed ? 'false' : 'true' ); ?>"<?php echo $onboarding_collapsed ? ' hidden' : ''; ?>>
						<div class="lgx-onboarding-steps-inner">
						<div class="lgx-onboarding-steps">
							<?php if ( ! empty( $onboarding_steps ) ) : ?>
								<?php foreach ( $onboarding_steps as $index => $step ) : ?>
									<div class="lgx-onboarding-step lgx-onboarding-step--<?php echo esc_attr( $step['status'] ); ?>">
										<div class="lgx-onboarding-step__header">
											<?php if ( 'completed' === $step['status'] ) : ?>
												<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
												<span class="screen-reader-text"><?php esc_html_e( 'Completed', 'logo-slider-wp' ); ?></span>
											<?php elseif ( 'current' === $step['status'] ) : ?>
												<span class="lgx-onboarding-step__dot lgx-onboarding-step__dot--current" aria-hidden="true"></span>
												<span class="screen-reader-text"><?php esc_html_e( 'Current', 'logo-slider-wp' ); ?></span>
											<?php else : ?>
												<span class="lgx-onboarding-step__dot lgx-onboarding-step__dot--pending" aria-hidden="true"></span>
												<span class="screen-reader-text"><?php esc_html_e( 'Pending', 'logo-slider-wp' ); ?></span>
											<?php endif; ?>
											<span class="lgx-onboarding-step__label"><?php esc_html_e( 'Step', 'logo-slider-wp' ); ?> <?php echo (int) ( $index + 1 ); ?></span>
										</div>
										<h3 class="lgx-onboarding-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
										<p class="lgx-onboarding-step__desc"><?php echo esc_html( $step['description'] ); ?></p>

										<?php
										$btn_class = ( 'completed' === $step['status'] ) ? 'lgx-button lgx-button--secondary' : 'lgx-button lgx-button--primary';
										if ( 'pending' === $step['status'] ) {
											$btn_class .= ' lgx-button--disabled';
										}
										$is_display_step = ( 'display' === $step['id'] );
										?>
										<div class="lgx-onboarding-step__actions">
											<a href="<?php echo esc_url( $step['url'] ); ?>" class="<?php echo esc_attr( $btn_class ); ?>"<?php if ( $is_display_step ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
												<?php echo esc_html( $step['action_label'] ); ?>
											</a>
											<?php if ( $is_display_step && 'completed' !== $step['status'] ) : ?>
												<button type="button" class="lgx-button lgx-button--secondary lgx-onboarding-mark-done" data-step-id="<?php echo esc_attr( $step['id'] ); ?>">
													<?php esc_html_e( 'Mark as Done', 'logo-slider-wp' ); ?>
												</button>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
						</div>
						</div>
					</div>
				</section>

				<!-- Quick Actions Card -->
				<section class="lgx-card lgx-card--actions" role="region" aria-labelledby="lgx-card-actions-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-actions-title" class="lgx-card__title">
							<?php esc_html_e( 'Quick Actions', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<div class="lgx-grid lgx-grid--actions">
							<?php if ( ! empty( $config['quick_actions'] ) ) : ?>
								<?php foreach ( $config['quick_actions'] as $action ) : ?>
									<?php
									$action_type = isset( $action['type'] ) ? $action['type'] : 'secondary';
									?>
									<div class="lgx-grid__column lgx-grid__column--one-third">
										<a href="<?php echo esc_url( $action['url'] ); ?>" class="lgx-button--action lgx-button--action--<?php echo esc_attr( $action_type ); ?>" target="<?php echo esc_attr( $action['target'] ); ?>">
											<span class="dashicons <?php echo esc_attr( $action['icon'] ); ?>" aria-hidden="true"></span>
											<div class="lgx-button__action-text">
												<h3><?php echo esc_html( $action['title'] ); ?></h3>
												<p><?php echo esc_html( $action['description'] ); ?></p>
											</div>
										</a>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</section>

				<?php if ( ! empty( $has_demo_content ) ) : ?>
				<!-- Demo Content Card (B87; B90 extends the copy to cover the demo showcase) —
				     rendered only while a seeded demo logo or the seeded demo showcase remains -->
				<section class="lgx-card lgx-card--demo-content" role="region" aria-labelledby="lgx-card-demo-content-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-demo-content-title" class="lgx-card__title">
							<?php esc_html_e( 'Demo Content', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<p class="lgx-demo-content__desc">
							<?php esc_html_e( 'Your site was set up with a few example logos and a demo showcase so you could see how a showcase looks straight away. Remove them whenever you are ready to use your own.', 'logo-slider-wp' ); ?>
						</p>
						<p class="lgx-demo-content__note">
							<?php esc_html_e( 'Anything you have edited — a demo logo or the demo showcase — is treated as your own content and will be kept.', 'logo-slider-wp' ); ?>
						</p>
						<div class="lgx-demo-content__actions">
							<button type="button" class="lgx-button lgx-button--secondary lgx-remove-demo-content">
								<?php esc_html_e( 'Remove Demo Content', 'logo-slider-wp' ); ?>
							</button>
							<span class="lgx-demo-content__feedback" role="status" aria-live="polite"></span>
						</div>
					</div>
				</section>
				<?php endif; ?>

				<!-- Recent Logos Card -->
				<section class="lgx-card lgx-card--recent-logos" role="region" aria-labelledby="lgx-card-recent-logos-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-recent-logos-title" class="lgx-card__title">
							<?php esc_html_e( 'Recent Logos', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body lgx-card__body--no-padding">
						<?php if ( empty( $recent_logos ) ) : ?>
							<div class="lgx-empty-state">
								<p><?php esc_html_e( 'No logos have been created yet.', 'logo-slider-wp' ); ?></p>
								<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=logosliderwp' ) ); ?>" class="lgx-button lgx-button--primary">
									<?php esc_html_e( 'Add Your First Logo', 'logo-slider-wp' ); ?>
								</a>
							</div>
						<?php else : ?>
							<table class="lgx-table lgx-table--recent">
								<tbody>
									<?php foreach ( $recent_logos as $logo ) : ?>
										<tr>
											<td class="lgx-table__col-thumb">
												<?php if ( $logo['thumbnail_url'] ) : ?>
													<img src="<?php echo esc_url( $logo['thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $logo['title'] ); ?>" class="lgx-recent-thumb" />
												<?php else : ?>
													<div class="lgx-recent-thumb-placeholder">
														<span class="dashicons dashicons-format-image" aria-hidden="true"></span>
													</div>
												<?php endif; ?>
											</td>
											<td class="lgx-table__col-title">
												<strong><?php echo esc_html( $logo['title'] ); ?></strong>
											</td>
											<td class="lgx-table__col-status">
												<span class="lgx-badge lgx-badge--<?php echo 'publish' === $logo['status'] ? 'success' : 'warning'; ?>">
													<?php
													if ( 'publish' === $logo['status'] ) {
														esc_html_e( 'Published', 'logo-slider-wp' );
													} else {
														esc_html_e( 'Draft', 'logo-slider-wp' );
													}
													?>
												</span>
											</td>
											<td class="lgx-table__col-actions">
												<a href="<?php echo esc_url( $logo['edit_url'] ); ?>" class="lgx-button lgx-button--secondary">
													<?php esc_html_e( 'Edit', 'logo-slider-wp' ); ?>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</section>

				<!-- Recent Showcases Card -->
				<section class="lgx-card lgx-card--recent-showcases" role="region" aria-labelledby="lgx-card-recent-showcases-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-recent-showcases-title" class="lgx-card__title">
							<?php esc_html_e( 'Recent Showcases', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body lgx-card__body--no-padding">
						<?php if ( empty( $recent_showcases ) ) : ?>
							<div class="lgx-empty-state">
								<p><?php esc_html_e( 'No showcases have been created yet.', 'logo-slider-wp' ); ?></p>
								<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=lgx_lsp_shortcodes' ) ); ?>" class="lgx-button lgx-button--primary">
									<?php esc_html_e( 'Create Your First Showcase', 'logo-slider-wp' ); ?>
								</a>
							</div>
						<?php else : ?>
							<table class="lgx-table lgx-table--recent">
								<tbody>
									<?php foreach ( $recent_showcases as $showcase ) : ?>
										<tr>
											<td class="lgx-table__col-title">
												<strong><?php echo esc_html( $showcase['title'] ); ?></strong>
											</td>
											<td class="lgx-table__col-shortcode">
												<input type="text" class="lgx_logo_slider_list_copy_input lgx-input--shortcode" readonly="readonly" value="<?php echo esc_attr( $showcase['shortcode'] ); ?>" title="<?php esc_attr_e( 'Click to copy shortcode', 'logo-slider-wp' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Copy shortcode for %s', 'logo-slider-wp' ), $showcase['title'] ) ); ?>">
											</td>
											<td class="lgx-table__col-status">
												<span class="lgx-badge lgx-badge--<?php echo 'publish' === $showcase['status'] ? 'success' : 'warning'; ?>">
													<?php
													if ( 'publish' === $showcase['status'] ) {
														esc_html_e( 'Published', 'logo-slider-wp' );
													} else {
														esc_html_e( 'Draft', 'logo-slider-wp' );
													}
													?>
												</span>
											</td>
											<td class="lgx-table__col-actions">
												<a href="<?php echo esc_url( $showcase['edit_url'] ); ?>" class="lgx-button lgx-button--secondary">
													<?php esc_html_e( 'Edit', 'logo-slider-wp' ); ?>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</section>

			</div>

			<!-- Column: Status & Stats (One-Third Column) -->
			<div class="lgx-grid__column lgx-grid__column--one-third">
				<!-- Statistics Card -->
				<section class="lgx-card lgx-card--stats" role="region" aria-labelledby="lgx-card-stats-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-stats-title" class="lgx-card__title">
							<?php esc_html_e( 'Usage Overview', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<div class="lgx-grid">
							<div class="lgx-grid__column lgx-grid__column--half">
								<div class="lgx-stat">
									<span class="lgx-stat__number"><?php echo esc_html( $logos_total ); ?></span>
									<span class="lgx-stat__label"><?php esc_html_e( 'Total Logos', 'logo-slider-wp' ); ?></span>
								</div>
							</div>
							<div class="lgx-grid__column lgx-grid__column--half">
								<div class="lgx-stat">
									<span class="lgx-stat__number"><?php echo esc_html( $logos_published ); ?></span>
									<span class="lgx-stat__label"><?php esc_html_e( 'Published Logos', 'logo-slider-wp' ); ?></span>
								</div>
							</div>
							<div class="lgx-grid__column lgx-grid__column--half">
								<div class="lgx-stat">
									<span class="lgx-stat__number"><?php echo esc_html( $logos_draft ); ?></span>
									<span class="lgx-stat__label"><?php esc_html_e( 'Draft Logos', 'logo-slider-wp' ); ?></span>
								</div>
							</div>
							<div class="lgx-grid__column lgx-grid__column--half">
								<div class="lgx-stat">
									<span class="lgx-stat__number"><?php echo esc_html( $showcases_count ); ?></span>
									<span class="lgx-stat__label"><?php esc_html_e( 'Logo Showcases', 'logo-slider-wp' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Diagnostics Status Card -->
				<section class="lgx-card lgx-card--diagnostics" role="region" aria-labelledby="lgx-card-diagnostics-title">
					<div class="lgx-card__header">
						<h2 id="lgx-card-diagnostics-title" class="lgx-card__title">
							<?php esc_html_e( 'System Status', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<table class="lgx-table--diagnostics">
							<tbody>
								<?php foreach ( $diagnostics as $key => $check ) : ?>
									<tr>
										<td><strong><?php echo esc_html( $check['label'] ); ?></strong></td>
										<td><?php echo esc_html( $check['current'] ); ?></td>
										<td class="lgx-table__status-cell">
											<span class="lgx-badge lgx-badge--<?php echo esc_attr( $check['status'] ); ?>">
												<?php if ( 'success' === $check['status'] ) : ?>
													<?php esc_html_e( 'OK', 'logo-slider-wp' ); ?>
												<?php elseif ( 'warning' === $check['status'] ) : ?>
													<?php esc_html_e( 'Warning', 'logo-slider-wp' ); ?>
												<?php else : ?>
													<?php esc_html_e( 'Fail', 'logo-slider-wp' ); ?>
												<?php endif; ?>
											</span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>
