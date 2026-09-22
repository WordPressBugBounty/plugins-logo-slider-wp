<?php
/**
 * Settings template view.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/settings/templates
 *
 * @var string $version
 * @var string $active_tab
 * @var array  $tabs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap lgx-dashboard lgx-settings">
	<!-- Page Header -->
	<header class="lgx-dashboard__header">
		<div class="lgx-dashboard__hero-content">
			<span class="lgx-dashboard__brand-badge">
				<span class="lgx-dashboard__brand-dot"></span>
				<?php esc_html_e( 'LogicHunt Suite', 'logo-slider-wp' ); ?>
			</span>
			<h1 class="lgx-dashboard__title">
				<?php esc_html_e( 'Settings', 'logo-slider-wp' ); ?>
				<span class="lgx-dashboard__version">v<?php echo esc_html( $version ); ?></span>
			</h1>
			<p class="lgx-dashboard__subtitle">
				<?php esc_html_e( 'Configure global options for Logo Slider WP Pro. Select a tab below to manage different aspects of your sliders.', 'logo-slider-wp' ); ?>
			</p>
		</div>
	</header>

	<!-- Page Content Wrapper -->
	<div class="lgx-dashboard__content">
		<div class="lgx-grid lgx-settings-grid">
			
			<!-- Left Navigation Column -->
			<div class="lgx-grid__column lgx-grid__column--one-third lgx-settings-sidebar">
				<section class="lgx-card" role="navigation" aria-label="<?php esc_attr_e( 'Settings Navigation', 'logo-slider-wp' ); ?>">
					<div class="lgx-card__body lgx-card__body--no-padding">
						<ul class="lgx-settings-tabs">
							<?php foreach ( $tabs as $tab_id => $tab_name ) : ?>
								<?php
								$is_active = ( $active_tab === $tab_id );
								$tab_url   = add_query_arg(
									array(
										'page' => 'logosliderwp-settings',
										'tab'  => $tab_id,
									),
									admin_url( 'edit.php?post_type=logosliderwp' )
								);
								$tab_class = $is_active ? 'lgx-settings-tabs__link lgx-settings-tabs__link--active' : 'lgx-settings-tabs__link';
								?>
								<li class="lgx-settings-tabs__item">
									<a href="<?php echo esc_url( $tab_url ); ?>" class="<?php echo esc_attr( $tab_class ); ?>">
										<?php echo esc_html( $tab_name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
			</div>

			<!-- Main Content Column -->
			<div class="lgx-grid__column lgx-grid__column--two-thirds lgx-settings-main">
				<section class="lgx-card lgx-card--settings-content">
					<div class="lgx-card__header">
						<h2 class="lgx-card__title">
							<?php echo esc_html( $tabs[ $active_tab ] ); ?> <?php esc_html_e( 'Settings', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						
						<?php if ( 'general' === $active_tab ) : ?>
							<p class="lgx-settings-placeholder">
								<?php esc_html_e( 'General settings will be available in a future update.', 'logo-slider-wp' ); ?>
							</p>
						<?php elseif ( 'performance' === $active_tab ) : ?>
							<p class="lgx-settings-placeholder">
								<?php esc_html_e( 'Performance settings will be available in a future update.', 'logo-slider-wp' ); ?>
							</p>
						<?php elseif ( 'import-export' === $active_tab ) : ?>
							<p class="lgx-settings-placeholder">
								<?php esc_html_e( 'Import / Export settings will be available in a future update.', 'logo-slider-wp' ); ?>
							</p>
						<?php elseif ( 'advanced' === $active_tab ) : ?>
							<p class="lgx-settings-placeholder">
								<?php esc_html_e( 'Advanced settings will be available in a future update.', 'logo-slider-wp' ); ?>
							</p>
						<?php elseif ( 'pro' === $active_tab ) : ?>
							<p class="lgx-settings-placeholder">
								<?php esc_html_e( 'Pro settings will be available in a future update.', 'logo-slider-wp' ); ?>
							</p>
						<?php endif; ?>

					</div>
				</section>
			</div>

		</div>
	</div>
</div>
