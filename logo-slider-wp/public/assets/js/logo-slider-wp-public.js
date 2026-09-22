(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	/*
		try {
			$('.lgx_app_item_link').tooltipster();
		} catch(e) {
			//console.log('not start');
		}
	*/

	// Update date : 05.07.2026, By : @VibeLogic
	function lgxLogoSliderInit( scope ) {
		var $ = window.jQuery;
		var $scope = typeof scope !== 'undefined' ? $( scope ) : $( document );

		if ( $scope.filter( '.lgx_logo_carousel' ).add( $scope.find( '.lgx_logo_carousel' ) ).length ) {
			$scope.filter( '.lgx_logo_carousel' ).add( $scope.find( '.lgx_logo_carousel' ) ).each(function (index) {
				if ( this.swiper ) { return; }


				var lgx_logo_carousel_item = $(this);
				var lgxLogoCarouselDataAttr = lgx_logo_carousel_item.data();
				var lgx_logo_carousel_id = lgx_logo_carousel_item.attr('id');
				
				var lgxLogoPagination = lgxLogoCarouselDataAttr.pagination;
				var lgxLogoNavigation = lgxLogoCarouselDataAttr.nav;
				// Task 011b (2026-07-27): was reading .infinite (Loop) instead of
				// .autoplay — Autoplay and Infinite Loop are independent settings;
				// this variable gates Swiper's autoplay config and must read the
				// Autoplay setting, not Loop. See docs/ui/showcase/task011b/.
				var lgxLogoAutoplay = lgxLogoCarouselDataAttr.autoplay;
				var lgxLogoTicker = lgxLogoCarouselDataAttr.ticker;
				// B39: Ticker's continuous scroll is driven entirely by Swiper's JS
				// autoplay loop (delay: 0), not a CSS animation — a prefers-reduced-motion
				// media query alone can't stop it, so the OS preference is checked here
				// and autoplay is skipped for ticker mode only, matching B38's pause/stop
				// accessibility stance. Non-ticker autoplay is untouched.
				var lgxTickerReducedMotion = lgxLogoTicker == true &&
					window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

				if (lgx_logo_carousel_item.length > 0) {
					var $appArea = lgx_logo_carousel_item.closest('.lgx_app_content_area');
					var $nextBtn = $appArea.find('.lgx_lsw_nav_button_next');
					var $prevBtn = $appArea.find('.lgx_lsw_nav_button_prev');
					var $pagination = $appArea.find('.swiper-pagination');

					// Pass the raw DOM node (this) to Swiper instead of string selector for iframe compatibility
					var lgxLogoSwiper = new Swiper(this, {
						lazy: lgxLogoCarouselDataAttr.lazy,
						loop: lgxLogoTicker == true ? true : lgxLogoCarouselDataAttr.infinite,
						speed: parseInt(lgxLogoCarouselDataAttr.speed),
						autoplay:
							lgxLogoAutoplay == true
								? {
									delay: lgxLogoTicker == true ? 0 : parseInt(lgxLogoCarouselDataAttr.delay),
									disableOnInteraction: false,
									// Task 014 (B38) Item 3: was `lgxLogoCarouselDataAttr.pause`.
									// Found during live verification that Swiper's OWN internal
									// pauseOnMouseEnter mechanism (Autoplay module's onMouseLeave,
									// confirmed by reading swiper-bundle.js) calls run() directly,
									// bypassing the public start()/stop() API entirely — meaning it
									// cannot be intercepted by the manual-pause guard below, and it
									// left swiper's internal `timeout` in a state that made a later
									// .autoplay.start() silently no-op. Hardcoded false here — the
									// plugin's own redundant jQuery hover handlers just below
									// already fully replicate this exact behavior (confirmed
									// identical in report 001 §2) and are the ones actually guarded,
									// so disabling Swiper's internal copy changes no observable
									// behavior, it just removes an unguardable duplicate mechanism.
									pauseOnMouseEnter: false,
									// Task 014 (B38) Item 5, Option A: pass a real object with
									// enabled:false instead of the whole option becoming the
									// literal boolean false. Behavior-preserving for B39's own
									// outcome (Swiper's Autoplay module only auto-starts on
									// init when params.autoplay.enabled is true — confirmed by
									// reading swiper-bundle.js's on('init', ...) hook), but it
									// also means swiper.params.autoplay.delay/pauseOnMouseEnter
									// keep their real configured values instead of Swiper's
									// module defaults (3000ms) — required so a later manual
									// .autoplay.start() (hover-pause resuming, or the new
									// pause/play button) restarts at the correct Ticker speed
									// instead of the wrong default one.
									enabled: !lgxTickerReducedMotion,
								}
								: false,
						spaceBetween: parseInt(lgxLogoCarouselDataAttr.space),
						slidesPerView: parseInt(lgxLogoCarouselDataAttr.item_mobile),
						allowTouchMove: lgxLogoCarouselDataAttr.move,
						simulateTouch: lgxLogoCarouselDataAttr.simulate,
						grabCursor: lgxLogoCarouselDataAttr.grab,
						mousewheel: lgxLogoCarouselDataAttr.wheel,
						autoHeight: lgxLogoCarouselDataAttr.height,
						freeMode: lgxLogoTicker == true ? true : false,
						effect: lgxLogoCarouselDataAttr.effect,
						coverflowEffect: {
							rotate: 30,
							slideShadows: false,
						},
						pagination:
							lgxLogoPagination == true
								? {
									el: $pagination.length ? $pagination[0] : '.swiper-pagination',
									type: 'bullets',
									clickable: true,
									dynamicBullets: lgxLogoCarouselDataAttr.dynamic,
									dynamicMainBullets: parseInt(lgxLogoCarouselDataAttr.bullets_no),
								}
								: false,
						navigation:
							lgxLogoNavigation == true
								? {
									nextEl: $nextBtn.length ? $nextBtn[0] : '.lgx_lsw_nav_button_next',
									prevEl: $prevBtn.length ? $prevBtn[0] : '.lgx_lsw_nav_button_prev'
								}
								: false,

						breakpoints: {
							320: {
								slidesPerView: parseInt(lgxLogoCarouselDataAttr.item_mobile),
							},
							768: {
								slidesPerView: parseInt(lgxLogoCarouselDataAttr.item_tablet),
							},
							992: {
								slidesPerView: parseInt(lgxLogoCarouselDataAttr.item_desk),
							},
							1200: {
								slidesPerView: parseInt(lgxLogoCarouselDataAttr.item_large),
							},
						},
						loopFillGroupWithBlank: true,
						fadeEffect: {
							crossFade: true
						},
						keyboard: {
							enabled: true
						},
					})
					if (lgxLogoCarouselDataAttr.autoplay === false) {
						lgxLogoSwiper.autoplay.stop()
					}
					// Task 014 (B38) Item 3: per-instance flag so a deliberate manual
					// pause (from the new pause/play button below) isn't silently
					// overridden by Pause-on-Hover's own mouseleave handler, which
					// otherwise calls .autoplay.start() unconditionally.
					var lgxTickerManuallyPaused = !lgxLogoSwiper.autoplay.running;
					if (lgxLogoCarouselDataAttr.pause && lgxLogoCarouselDataAttr.autoplay) {
						lgx_logo_carousel_item.hover(
							function () {
								lgxLogoSwiper.autoplay.stop()
							},
							function () {
								if (!lgxTickerManuallyPaused) {
									lgxLogoSwiper.autoplay.start()
								}
							}
						)
						lgx_logo_carousel_item.mouseenter(function () {
							lgxLogoSwiper.autoplay.stop();
						});
						lgx_logo_carousel_item.mouseleave(function () {
							if (!lgxTickerManuallyPaused) {
								lgxLogoSwiper.autoplay.start();
							}
						});
					}

					// Task 014 (B38) Item 4: keyboard/touch-accessible pause/play
					// control for Ticker Mode (WCAG 2.2.2). A native <button> needs no
					// custom keydown handling for Enter/Space — the browser already
					// activates it natively. Swiper's own Keyboard module (enabled
					// above) only listens for arrow/PageUp/PageDown keys, confirmed by
					// reading swiper-bundle.js, so there is no key-binding collision.
					// aria-pressed is set here, never in the PHP-rendered markup,
					// because wp_kses_post()'s allowed-attributes list doesn't include
					// aria-pressed and would silently strip it.
					var $tickerPauseBtn = $appArea.find('.lgx_lsw_ticker_pause_btn');
					if ($tickerPauseBtn.length) {
						var $tickerPauseIcon = $tickerPauseBtn.find('i');

						function updateTickerPauseBtnState() {
							if (lgxTickerManuallyPaused) {
								$tickerPauseIcon.removeClass('fa-pause').addClass('fa-play');
								$tickerPauseBtn.attr('aria-pressed', 'true');
								$tickerPauseBtn.attr('aria-label', lgxLsPublicI18n.tickerResume);
							} else {
								$tickerPauseIcon.removeClass('fa-play').addClass('fa-pause');
								$tickerPauseBtn.attr('aria-pressed', 'false');
								$tickerPauseBtn.attr('aria-label', lgxLsPublicI18n.tickerPause);
							}
						}

						updateTickerPauseBtnState();

						$tickerPauseBtn.on('click', function () {
							lgxTickerManuallyPaused = !lgxTickerManuallyPaused;
							if (lgxTickerManuallyPaused) {
								lgxLogoSwiper.autoplay.stop();
							} else {
								lgxLogoSwiper.autoplay.start();
							}
							updateTickerPauseBtnState();
						});
					}
					/*Final End*/

					$(window).off('resize.lgx_' + lgx_logo_carousel_id).on('resize.lgx_' + lgx_logo_carousel_id, function () {
						lgxLogoSwiper.update()
					})
					$(window).trigger('resize.lgx_' + lgx_logo_carousel_id)
				}

			}); //Swiper Each
		}


		if ( $scope.filter( '.lgx_app_item_tooltip' ).add( $scope.find( '.lgx_app_item_tooltip' ) ).length ) {
			$scope.filter( '.lgx_app_content_wrapper' ).add( $scope.find( '.lgx_app_content_wrapper' ) ).each(function (index) {
				if ( $(this).data('lgx-tooltip-initialized') ) { return; }
				$(this).data('lgx-tooltip-initialized', true);

				var lgx_ls_tt_item = $(this);
				var lgx_showcase_id = lgx_ls_tt_item.attr('id');
				var lgx_tooltip_data_row = lgx_ls_tt_item.find('.lgx_app_item_row');
				var lgxTtDataAttr = lgx_tooltip_data_row.data();
				
				lgx_ls_tt_item.find('.lgx_app_item_tooltip').tooltipster({
					//position:'top',
					side: lgxTtDataAttr.tt_position,
					animation: lgxTtDataAttr.tt_anim,
					animationDuration: lgxTtDataAttr.tt_duration,
					arrow: lgxTtDataAttr.tt_arrow,
					delay: lgxTtDataAttr.tt_delay,
					distance: lgxTtDataAttr.tt_distance,
					trigger: lgxTtDataAttr.tt_trigger,
					minIntersection: lgxTtDataAttr.tt_intersection,
					timer: lgxTtDataAttr.tt_timer,
					theme: 'lgx_lsw_tooltipster_' + lgxTtDataAttr.tt_id,
					maxWidth: lgxTtDataAttr.tt_maxwidth,
					minWidth: lgxTtDataAttr.tt_minwidth
				});
			}); //each
		}

		/*	$('.lgx_app_item_row').masonry({
				// options
				itemSelector: '.lgx_app_item',
			   columnWidth: 200,
				gutter: 10,
				//fitWidth: true
			});
	*/


		if ( $scope.filter( '.lgx_lsw_preloader' ).add( $scope.find( '.lgx_lsw_preloader' ) ).length ) {
			$scope.filter( '.lgx_logo_slider_app' ).add( $scope.find( '.lgx_logo_slider_app' ) ).each(function () {
				var lgx_lsw_preloader_item = $(this).children('.lgx_lsw_preloader');
				$(lgx_lsw_preloader_item).animate({ opacity: 0 }, 600).remove();
			})
		}
	}

	// Update date : 05.07.2026, By : @VibeLogic
	jQuery(document).ready(function () {
		lgxLogoSliderInit();
	});//DOM

	// Update date : 05.07.2026, By : @VibeLogic
	window.LgxLogoSlider = window.LgxLogoSlider || {};
	window.LgxLogoSlider.init = lgxLogoSliderInit;

})(jQuery);
