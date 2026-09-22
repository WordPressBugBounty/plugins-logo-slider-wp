<?php
/**
 * Help & Resources page template.
 *
 * Repurposed from the former "Getting Started" destination (BACKLOG B21).
 * Dashboard remains the sole onboarding surface; this page is "what else
 * can help me" — Recommendations, FAQ, Documentation, Get Help — never a
 * second onboarding checklist.
 *
 * @package Logo_Slider_WP
 *
 * @var array $plugin_data Set by Logo_Slider_WP_Admin::display_plugin_help_resources_page().
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * TODO(help-resources-doc-urls): the docs site's real per-page URLs
 * (builder.md, embed.md, logos.md, pro.md, support.md, getting-started.md)
 * are not yet confirmed live. Every "Learn more" link below intentionally
 * points at the single Owner-confirmed Documentation base URL until those
 * per-page routes are confirmed — see 002-implementation-plan.md §7/§8 and
 * the implementation report's TODO list. Do not replace with a guessed path.
 */
$lgx_help_docs_base_url    = 'https://docs.logichunt.com/logo-slider-wp/';
$lgx_help_support_url      = 'https://logichunt.com/support/';

// Single shared source (config.php's 'recommendations' key) — Quick Tips on
// the Dashboard reads the same array filtered to 'quick_tip' => true.
$lgx_help_recommendations  = method_exists( $this, 'get_dashboard' ) ? $this->get_dashboard()->get_recommendations() : array();
$lgx_help_video_tutorials  = method_exists( $this, 'get_dashboard' ) ? $this->get_dashboard()->get_video_tutorials() : array();

/**
 * FAQ content — sourced verbatim from
 * docs/ui/dashboard-help-resources/plans/faq-content-inventory.md (the
 * authoritative FAQ source for this task). Answers are the inventory's own
 * "Answer" text; no fact here was invented beyond that document. #7 keeps
 * exactly its three verified causes and no others (the inventory's own
 * documentation gap is not filled in here). #9 is a redirect, not a
 * restated Free/Pro list. #10 deliberately omits phone/WhatsApp per the
 * inventory's own reasoning.
 */
$lgx_help_faq = array(
	array(
		'question'  => __( 'How do I create my first showcase?', 'logo-slider-wp' ),
		'answer'    => __( 'Logo Slider → Showcases → Create Showcase. Choose a layout — Carousel, Grid, or Flexbox — configure your logo source, and save.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/builder/',
	),
	array(
		'question'  => __( 'How do I display a showcase?', 'logo-slider-wp' ),
		'answer'    => __( 'Insert the native Gutenberg block and select your showcase, or copy the shortcode and paste it anywhere shortcodes render.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/embed/',
	),
	array(
		'question'  => __( 'Where do I find my shortcode?', 'logo-slider-wp' ),
		'answer'    => __( 'Logo Slider → Showcases lists the shortcode for every showcase with a copy action, or use the Copy Shortcode button inside the Builder itself.', 'logo-slider-wp' ),
		'doc_link'  => false,
	),
	array(
		'question'  => __( 'How do I change logo order?', 'logo-slider-wp' ),
		'answer'    => __( 'Logo Slider → Logos — drag and drop rows into the order you want.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/logos/',
	),
	array(
		'question'  => __( 'How do Categories work?', 'logo-slider-wp' ),
		'answer'    => __( 'Create categories under Logo Slider → Categories, assign them to logos, then use "Filter by Category" in the Builder\'s Content tab to show only specific categories in a given showcase.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/categories/',
	),
	array(
		'question'  => __( 'How do I configure responsive behavior?', 'logo-slider-wp' ),
		'answer'    => __( 'In the Builder\'s Responsive tab, set how many logos display per row at each breakpoint.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/builder/#responsive-tab',
	),
	array(
		'question'  => __( "Why isn't a logo showing?", 'logo-slider-wp' ),
		'answer'    => __( 'Three confirmed causes: the showcase\'s Item Limit is set lower than your logo count, a category filter excludes that logo, or the logo is still a Draft rather than Published. Still not showing? Reach out via Get Support below and we\'ll help you track it down.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/support/',
	),
	array(
		'question'  => __( 'How do I use the Gutenberg Block?', 'logo-slider-wp' ),
		'answer'    => __( 'Add the "Logo Slider WP" block from the inserter, choose a showcase, and the live preview appears immediately in the editor.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/gutenberg/',
	),
	array(
		'question'  => __( 'What is available in Free vs Pro?', 'logo-slider-wp' ),
		'answer'    => __( 'See our full Free vs Pro comparison.', 'logo-slider-wp' ),
		'doc_link'  => true,
		'doc_url'   => 'https://docs.logichunt.com/logo-slider-wp/pro/',
	),
	array(
		'question'  => __( 'Where do I get support?', 'logo-slider-wp' ),
		'answer'    => __( 'Contact the LogicHunt Support Center — the same team that built the plugin handles support for both Free and Pro users.', 'logo-slider-wp' ),
		'doc_link'  => false,
		'anchor'    => 'lgx-help-get-help',
	),
	array(
		'question'  => __( 'What image formats/sizes work best?', 'logo-slider-wp' ),
		'answer'    => __( 'PNG or SVG logos provide the best quality and transparency. Use appropriately sized images for better website performance.', 'logo-slider-wp' ),
		'doc_link'  => false,
	),
	array(
		'question'  => __( 'Why is a setting locked, or what happens to my settings if I downgrade from Pro?', 'logo-slider-wp' ),
		'answer'    => __( 'A lock icon means that field is Pro-only — Free users can see it but not change it. If you previously had Pro and downgraded, any values you had already saved stay exactly as they were; they are never reset, even though the field is now locked.', 'logo-slider-wp' ),
		'doc_link'  => false,
	),
);
?>

<div class="wrap lgx-dashboard lgx-help-resources">
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
				<?php esc_html_e( 'Help & Resources', 'logo-slider-wp' ); ?>
			</h1>
			<p class="lgx-dashboard__subtitle">
				<?php esc_html_e( 'Recommendations, answers to common questions, and where to go for documentation or direct help.', 'logo-slider-wp' ); ?>
			</p>
		</div>
	</header>

	<!-- Page Content Wrapper -->
	<div class="lgx-dashboard__content">
		<div class="lgx-grid">

			<!-- Left Column: Recommendations + FAQ -->
			<div class="lgx-grid__column lgx-grid__column--two-thirds">

				<!-- Recommendations -->
				<section class="lgx-card lgx-help-recommendations" role="region" aria-labelledby="lgx-help-recommendations-title">
					<div class="lgx-card__header">
						<h2 id="lgx-help-recommendations-title" class="lgx-card__title">
							<?php esc_html_e( 'Recommendations', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<?php if ( ! empty( $lgx_help_recommendations ) ) : ?>
							<div class="lgx-help-recommendations__list">
								<?php foreach ( $lgx_help_recommendations as $lgx_tip ) : ?>
									<div class="lgx-help-recommendations__item">
										<span class="lgx-help-recommendations__icon dashicons <?php echo esc_attr( isset( $lgx_tip['icon'] ) ? $lgx_tip['icon'] : 'dashicons-lightbulb' ); ?>" aria-hidden="true"></span>
										<div class="lgx-help-recommendations__text">
											<h3><?php echo esc_html( $lgx_tip['title'] ); ?></h3>
											<p><?php echo esc_html( $lgx_tip['description'] ); ?></p>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- FAQ -->
				<section class="lgx-card lgx-help-faq" role="region" aria-labelledby="lgx-help-faq-title">
					<div class="lgx-card__header">
						<h2 id="lgx-help-faq-title" class="lgx-card__title">
							<?php esc_html_e( 'FAQ', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<div class="lgx-help-faq__list">
							<?php foreach ( $lgx_help_faq as $lgx_faq_index => $lgx_faq_item ) : ?>
								<?php $lgx_faq_panel_id = 'lgx-help-faq-answer-' . (int) $lgx_faq_index; ?>
								<div class="lgx-help-faq__item">
									<h3 class="lgx-help-faq__heading">
										<button type="button" class="lgx-help-faq__question" aria-expanded="false" aria-controls="<?php echo esc_attr( $lgx_faq_panel_id ); ?>">
											<?php echo esc_html( $lgx_faq_item['question'] ); ?>
											<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
										</button>
									</h3>
									<div id="<?php echo esc_attr( $lgx_faq_panel_id ); ?>" class="lgx-help-faq__answer" data-expanded="false" hidden>
										<div class="lgx-help-faq__answer-inner">
											<p><?php echo esc_html( $lgx_faq_item['answer'] ); ?></p>
											<?php if ( ! empty( $lgx_faq_item['doc_link'] ) ) : ?>
												<a href="<?php echo esc_url( isset( $lgx_faq_item['doc_url'] ) ? $lgx_faq_item['doc_url'] : $lgx_help_docs_base_url ); ?>" target="_blank" rel="noopener noreferrer" class="lgx-help-faq__doc-link">
													<?php esc_html_e( 'Learn more', 'logo-slider-wp' ); ?>
													<span class="dashicons dashicons-external" aria-hidden="true"></span>
												</a>
											<?php elseif ( ! empty( $lgx_faq_item['anchor'] ) ) : ?>
												<a href="#<?php echo esc_attr( $lgx_faq_item['anchor'] ); ?>" class="lgx-help-faq__doc-link">
													<?php esc_html_e( 'See Get Help below', 'logo-slider-wp' ); ?>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</section>

			</div>

			<!-- Right Column: Documentation + Get Help -->
			<div class="lgx-grid__column lgx-grid__column--one-third">

				<!-- Documentation -->
				<section class="lgx-card lgx-help-documentation" role="region" aria-labelledby="lgx-help-documentation-title">
					<div class="lgx-card__header">
						<h2 id="lgx-help-documentation-title" class="lgx-card__title">
							<?php esc_html_e( 'Documentation', 'logo-slider-wp' ); ?>
						</h2>
					</div>
					<div class="lgx-card__body">
						<p class="lgx-help-documentation__desc">
							<?php esc_html_e( 'Full guides for getting set up, using the Builder, and displaying your showcase.', 'logo-slider-wp' ); ?>
						</p>
						<a href="<?php echo esc_url( $lgx_help_docs_base_url ); ?>" target="_blank" rel="noopener noreferrer" class="lgx-button lgx-button--primary">
							<?php esc_html_e( 'Visit Documentation', 'logo-slider-wp' ); ?>
						</a>
					</div>
				</section>

				<!-- Get Help — strengthened visual hierarchy (Round 3): the
				     support CTA is the primary visual element, everything
				     else (Rate Plugin, video list) is secondary. -->
				<section id="lgx-help-get-help" class="lgx-card lgx-help-get-help" role="region" aria-labelledby="lgx-help-get-help-title">
					<div class="lgx-card__body">
						<div class="lgx-help-get-help__cta">
							<span class="dashicons dashicons-sos lgx-help-get-help__cta-icon" aria-hidden="true"></span>
							<h2 id="lgx-help-get-help-title" class="lgx-help-get-help__cta-title">
								<?php esc_html_e( 'Need help?', 'logo-slider-wp' ); ?>
							</h2>
							<p class="lgx-help-get-help__cta-desc">
								<?php esc_html_e( "Can't find the answer you're looking for? Our support team can help you troubleshoot Logo Slider WP.", 'logo-slider-wp' ); ?>
							</p>
							<a href="<?php echo esc_url( $lgx_help_support_url ); ?>" target="_blank" rel="noopener noreferrer" class="lgx-button lgx-button--primary lgx-help-get-help__cta-button">
								<?php esc_html_e( 'Get Support', 'logo-slider-wp' ); ?>
							</a>
						</div>
						<ul class="lgx-help-get-help__list">
							<li>
								<a href="https://wordpress.org/support/plugin/logo-slider-wp/reviews/#new-post" target="_blank" rel="noopener noreferrer">
									<span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
									<?php esc_html_e( 'Rate Plugin', 'logo-slider-wp' ); ?>
								</a>
							</li>
							<?php if ( ! empty( $lgx_help_video_tutorials ) ) : ?>
								<?php foreach ( $lgx_help_video_tutorials as $lgx_video ) : ?>
									<li>
										<a href="<?php echo esc_url( $lgx_video['url'] ); ?>" target="_blank" rel="noopener noreferrer">
											<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
											<?php echo esc_html( $lgx_video['title'] ); ?>
											<span class="lgx-video-duration">(<?php echo esc_html( $lgx_video['duration'] ); ?>)</span>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</section>

				<?php if ( ! ( function_exists( 'lgx_ls_is_pro' ) && lgx_ls_is_pro() ) ) : ?>
				<!-- Free-user upgrade CTA (Round 3) — presentation only, not a
				     capability-gating system. Copy/URL reused verbatim from the
				     Showcase Builder's own existing sidebar upgrade CTA
				     (admin/modules/showcase-builder/templates/builder-wrapper.php,
				     read-only reference — not imported/modified), the
				     established authoritative destination for this exact
				     message in this product. Never rendered for Pro. -->
				<section class="lgx-card lgx-help-upgrade" role="region" aria-labelledby="lgx-help-upgrade-title">
					<div class="lgx-card__body">
						<span class="dashicons dashicons-star-filled lgx-help-upgrade__icon" aria-hidden="true"></span>
						<h2 id="lgx-help-upgrade-title" class="lgx-help-upgrade__title">
							<?php esc_html_e( 'Unlock More Features', 'logo-slider-wp' ); ?>
						</h2>
						<p class="lgx-help-upgrade__desc">
							<?php esc_html_e( 'Get advanced layouts, tooltips, and premium styles with Pro.', 'logo-slider-wp' ); ?>
						</p>
						<a href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank" rel="noopener noreferrer" class="lgx-button lgx-button--upgrade lgx-help-upgrade__button">
							<?php esc_html_e( 'Upgrade to Pro', 'logo-slider-wp' ); ?>
						</a>
					</div>
				</section>
				<?php endif; ?>

			</div>

		</div>
	</div>
</div>
