/**
 * Dashboard module JavaScript functionality.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/dashboard/assets/js
 */

(function($) {
	'use strict';

	/**
	 * Announce a message to screen readers via a shared, lazily-created
	 * live region — avoids adding a new aria-live element to every
	 * markup location that needs one.
	 */
	function lgxDashboardAnnounce(message) {
		var $live = $('#lgx-dashboard-live-region');
		if (!$live.length) {
			$live = $('<div id="lgx-dashboard-live-region" class="screen-reader-text" role="status" aria-live="polite"></div>').appendTo('body');
		}
		$live.text('');
		window.setTimeout(function() {
			$live.text(message);
		}, 50);
	}

	/**
	 * Round 3 fix: the wrapper's actual visual collapse/expand is driven by
	 * a CSS grid-template-rows transition (see dashboard.css) between
	 * data-expanded="true"/"false" -- the [hidden] attribute is still what
	 * makes the collapsed state genuinely removed from layout/accessibility
	 * at rest, but toggling it immediately (the old behavior) skipped the
	 * animation and, combined with a pre-existing CSS specificity bug, left
	 * the content visually shown regardless of [hidden]. Expand: remove
	 * [hidden] first, force a reflow, then flip data-expanded so the
	 * transition actually plays from a real 0fr starting point. Collapse:
	 * flip data-expanded first (plays the transition down to 0fr), then
	 * re-add [hidden] only once that transition finishes (or immediately,
	 * under prefers-reduced-motion, since no transition will run to wait for).
	 */
	function lgxSetOnboardingExpanded(expand) {
		var $wrapper = $('#lgx-dashboard-onboarding-steps');
		var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		if (expand) {
			$wrapper.prop('hidden', false);
			void $wrapper[0].offsetHeight; // force reflow so 0fr paints before 1fr is applied
			$wrapper.attr('data-expanded', 'true');
		} else {
			$wrapper.attr('data-expanded', 'false');
			if (reducedMotion) {
				$wrapper.prop('hidden', true);
			} else {
				// Native addEventListener, not jQuery's .one('transitionend', ...):
				// jQuery's event normalization does not copy TransitionEvent-only
				// properties (propertyName) onto its wrapped event object, so a
				// condition checked via a jQuery handler's `e.propertyName` is
				// always undefined and silently never matches -- confirmed live
				// (the transition itself fired and completed correctly every
				// time; only the "now add [hidden] back" step never ran).
				var wrapperEl = $wrapper[0];
				wrapperEl.addEventListener('transitionend', function onEnd(e) {
					if (e.target === wrapperEl && e.propertyName === 'grid-template-rows') {
						wrapperEl.setAttribute('hidden', '');
						wrapperEl.removeEventListener('transitionend', onEnd);
					}
				});
			}
		}
	}

	$(document).ready(function() {

		// Onboarding card collapse/expand toggle. Completion state and
		// visibility state are independent (brief §6) — this only ever
		// changes display + the stored preference, never step completion.
		$(document).on('click', '#lgx-dashboard-onboarding-toggle', function(e) {
			e.preventDefault();

			var $button = $(this);
			var collapsing = $button.data('collapsed') !== 'yes';

			lgxSetOnboardingExpanded(!collapsing);
			$button.attr('aria-expanded', collapsing ? 'false' : 'true');
			$button.data('collapsed', collapsing ? 'yes' : 'no');
			$button.text(collapsing ? lgxDashboardOnboarding.show_steps_label : lgxDashboardOnboarding.hide_steps_label);

			// Round 3b: the compact collapsed-state status line is the
			// inverse of the steps wrapper's visibility -- shown only while
			// collapsed. No animation on this element (it's a single line of
			// text, not worth the complexity), so it can toggle immediately.
			$('#lgx-dashboard-onboarding-status').prop('hidden', !collapsing);

			$.ajax({
				url: lgxDashboardOnboarding.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'lgx_ls_toggle_onboarding_visibility',
					collapsed: collapsing ? 'yes' : 'no',
					nonce: lgxDashboardOnboarding.visibility_nonce
				}
			});
		});

		// Shortcode "click to copy" inputs (Recent Showcases table, Your
		// Showcase card). Self-contained here because this screen does not
		// load admin/js/logo-slider-wp-admin.js (that script — and its own
		// copyToClipboard()/alertify dependency — is only enqueued on the
		// Logos/Showcases post-type screens, confirmed via the source audit).
		$(document).on('click', '.lgx_logo_slider_list_copy_input', function(e) {
			e.preventDefault();

			var $input = $(this);
			$input.select();

			var text = $input.val();

			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(text).then(function() {
					lgxDashboardAnnounce(lgxDashboardOnboarding.shortcode_copied_label);
				});
			} else {
				try {
					document.execCommand('copy');
					lgxDashboardAnnounce(lgxDashboardOnboarding.shortcode_copied_label);
				} catch (err) {
					// Silently no-op — the text is still selected for a manual Ctrl+C.
				}
			}
		});

		// B57: "Mark as Done" control for the Getting Started card's Step 4
		// (Display on your website) — server can't detect this step itself,
		// so the user confirms it explicitly.
		$(document).on('click', '.lgx-onboarding-mark-done', function(e) {
			e.preventDefault();

			var $button = $(this);

			if ($button.prop('disabled')) {
				return;
			}

			var originalLabel = $button.text();
			$button.prop('disabled', true).text(lgxDashboardOnboarding.marking_label);

			$.ajax({
				url: lgxDashboardOnboarding.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'lgx_ls_dismiss_onboarding_step',
					step_id: $button.data('step-id'),
					nonce: lgxDashboardOnboarding.nonce
				},
				success: function(response) {
					if (response && response.success) {
						var $step = $button.closest('.lgx-onboarding-step');

						$step
							.removeClass('lgx-onboarding-step--current lgx-onboarding-step--pending')
							.addClass('lgx-onboarding-step--completed');

						$step.find('.lgx-onboarding-step__header .lgx-onboarding-step__dot').remove();
						$step.find('.lgx-onboarding-step__header .screen-reader-text').remove();
						$step.find('.lgx-onboarding-step__header').prepend(
							'<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>' +
							'<span class="screen-reader-text">' + lgxDashboardOnboarding.completed_label + '</span>'
						);

						$button.remove();

						if (response.data && typeof response.data.percentage !== 'undefined') {
							$('.lgx-onboarding__percentage').text(response.data.percentage + '%');
							$('.lgx-progress-bar__fill').css('width', response.data.percentage + '%');
							$('.lgx-progress-bar').attr('aria-valuenow', response.data.percentage);
						}
					} else {
						$button.prop('disabled', false).text(originalLabel);
						alert(response && response.data && response.data.message ? response.data.message : lgxDashboardOnboarding.error_message);
					}
				},
				error: function() {
					$button.prop('disabled', false).text(originalLabel);
					alert(lgxDashboardOnboarding.error_message);
				}
			});
		});

		// B87: "Remove Demo Content" — deletes the seeded demo logos and their
		// images. Items the user has edited have already graduated server-side
		// and are never touched.
		$(document).on('click', '.lgx-remove-demo-content', function(e) {
			e.preventDefault();

			var $button = $(this);

			if ($button.prop('disabled')) {
				return;
			}

			if (!window.confirm(lgxDashboardDemoContent.confirm_message)) {
				return;
			}

			var originalLabel = $button.text();
			var $feedback = $button.closest('.lgx-demo-content__actions').find('.lgx-demo-content__feedback');

			$button.prop('disabled', true).text(lgxDashboardDemoContent.removing_label);
			$feedback.text('');

			$.ajax({
				url: lgxDashboardDemoContent.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'lgx_ls_remove_demo_content',
					nonce: lgxDashboardDemoContent.nonce
				},
				success: function(response) {
					if (response && response.success) {
						// The whole card disappears — matching the server-side
						// rule that it renders only while demo content exists.
						$button.closest('.lgx-card--demo-content').remove();
					} else {
						$button.prop('disabled', false).text(originalLabel);
						$feedback.text(response && response.data && response.data.message ? response.data.message : lgxDashboardDemoContent.error_message);
					}
				},
				error: function() {
					$button.prop('disabled', false).text(originalLabel);
					$feedback.text(lgxDashboardDemoContent.error_message);
				}
			});
		});

	});

})(jQuery);
