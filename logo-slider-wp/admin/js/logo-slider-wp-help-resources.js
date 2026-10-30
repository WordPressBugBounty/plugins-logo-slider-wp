/**
 * Help & Resources page JavaScript functionality.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/js
 */

(function($) {
	'use strict';

	/**
	 * Round 3: smooth ~220ms open/close via the CSS grid-template-rows
	 * transition on .lgx-help-faq__answer (see dashboard.css). [hidden] is
	 * removed just before expanding (so the transition has a real 0fr
	 * starting point to animate from) and re-added only once the collapse
	 * transition actually finishes, so the panel is still genuinely out of
	 * layout/the accessibility tree at rest -- exactly as before, just no
	 * longer skipping the animation. Under prefers-reduced-motion, no
	 * transition runs, so [hidden] is applied immediately instead of
	 * waiting for a transitionend that would never fire.
	 */
	function lgxSetFaqExpanded($answer, expand) {
		var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		if (expand) {
			$answer.prop('hidden', false);
			void $answer[0].offsetHeight; // force reflow so 0fr paints before 1fr is applied
			$answer.attr('data-expanded', 'true');
		} else {
			$answer.attr('data-expanded', 'false');
			if (reducedMotion) {
				$answer.prop('hidden', true);
			} else {
				// Native addEventListener, not jQuery's .one('transitionend', ...):
				// jQuery's event normalization does not copy TransitionEvent-only
				// properties (propertyName) onto its wrapped event object, so a
				// condition checked via a jQuery handler's `e.propertyName` is
				// always undefined and silently never matches -- confirmed live
				// (the transition itself fired and completed correctly every
				// time; only the "now add [hidden] back" step never ran).
				var answerEl = $answer[0];
				answerEl.addEventListener('transitionend', function onEnd(e) {
					if (e.target === answerEl && e.propertyName === 'grid-template-rows') {
						answerEl.setAttribute('hidden', '');
						answerEl.removeEventListener('transitionend', onEnd);
					}
				});
			}
		}
	}

	$(document).ready(function() {

		// FAQ expand/collapse. Each question is a real <button> with
		// aria-expanded/aria-controls kept in sync with the actual visual
		// state on every toggle, not a static markup value. Items are
		// independent -- opening one never touches any other.
		$(document).on('click', '.lgx-help-faq__question', function() {
			var $button = $(this);
			var expanded = $button.attr('aria-expanded') === 'true';
			var $answer = $('#' + $button.attr('aria-controls'));

			$button.attr('aria-expanded', expanded ? 'false' : 'true');
			lgxSetFaqExpanded($answer, !expanded);
		});

	});

})(jQuery);
