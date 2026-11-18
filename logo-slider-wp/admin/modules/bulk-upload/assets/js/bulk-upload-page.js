/**
 * Logo Slider WP - Bulk Upload Page Client (Select, Review & Create).
 *
 * Handles Media Library image selection, client-side Brand Name derivation (D8),
 * sequential chunked pre-checks against lgx_ls_bulk_check (D3, D15), row notices,
 * drag-and-drop / keyboard row reordering, row removal, step indicator state,
 * common settings interactions, sequential create run against lgx_ls_bulk_create (D7),
 * creating progress card, stop (D17), done results grid, network retry with re-check (D16),
 * and retry failed items (D9).
 *
 * @package Logo_Slider_WP
 * @since   6.2.0
 */
(function( $ ) {
	'use strict';

	var i18n = ( window.wp && window.wp.i18n ) ? window.wp.i18n : null;

	function __( text, domain ) {
		if ( i18n && typeof i18n.__ === 'function' ) {
			return i18n.__( text, domain );
		}
		return text;
	}

	function _n( single, plural, number, domain ) {
		if ( i18n && typeof i18n._n === 'function' ) {
			return i18n._n( single, plural, number, domain );
		}
		return number === 1 ? single : plural;
	}

	function sprintf( format ) {
		var args = Array.prototype.slice.call( arguments, 1 );
		if ( i18n && typeof i18n.sprintf === 'function' ) {
			return i18n.sprintf.apply( i18n, arguments );
		}
		var index = 0;
		return String( format ).replace( /%([ds%]|(\d+\$[ds]))/g, function( match, specifier ) {
			if ( specifier === '%' ) {
				return '%';
			}
			var posMatch = specifier.match( /^(\d+)\$/ );
			if ( posMatch ) {
				var pos = parseInt( posMatch[ 1 ], 10 ) - 1;
				return String( args[ pos ] !== undefined ? args[ pos ] : '' );
			}
			var val = index < args.length ? args[ index++ ] : '';
			return String( val );
		} );
	}

	/**
	 * Derive the default Brand Name from an attachment title or filename (D8).
	 *
	 * - If title is empty (or only whitespace/separators), strips extension from filename.
	 * - Replaces each run of `-` or `_` with a single space.
	 * - Trims and collapses whitespace runs to a single space.
	 * - Upper-cases the first character of each space-separated word using
	 *   String.prototype.toLocaleUpperCase() and preserves the rest of the word.
	 *
	 * @param {string} title    Attachment title.
	 * @param {string} filename Attachment file name.
	 * @return {string} Derived brand name.
	 */
	function deriveBrandName( title, filename ) {
		var raw = ( typeof title === 'string' ) ? title : '';
		var trimmedTitle = raw.replace( /^\s+|\s+$/g, '' );

		if ( trimmedTitle === '' ) {
			var fallbackFile = ( typeof filename === 'string' ) ? filename : '';
			raw = fallbackFile.replace( /\.[^/.]+$/, '' );
		}

		var normalized = raw
			.replace( /[-_]+/g, ' ' )
			.replace( /\s+/g, ' ' )
			.replace( /^\s+|\s+$/g, '' );

		if ( normalized === '' ) {
			return '';
		}

		var words = normalized.split( ' ' );
		var out = [];
		for ( var i = 0; i < words.length; i++ ) {
			var word = words[ i ];
			if ( word.length > 0 ) {
				out.push( word.charAt( 0 ).toLocaleUpperCase() + word.slice( 1 ) );
			}
		}

		return out.join( ' ' );
	}

	/**
	 * Validate whether a server-supplied edit link is a safe, same-origin admin URL.
	 *
	 * @param {string} url Candidate URL.
	 * @return {boolean} True if safe same-site admin URL.
	 */
	function isSafeAdminUrl( url ) {
		if ( typeof url !== 'string' || ! url ) {
			return false;
		}

		var trimmed = url.replace( /^\s+|\s+$/g, '' );
		if ( /^javascript:/i.test( trimmed ) || /^data:/i.test( trimmed ) ) {
			return false;
		}

		try {
			var base = ( window.location && window.location.href ) ? window.location.href : 'http://localhost/';
			var parsed = new URL( trimmed, base );
			if ( window.location && window.location.origin ) {
				if ( parsed.origin !== window.location.origin ) {
					return false;
				}
			}
			if (
				parsed.pathname.indexOf( '/wp-admin/' ) === -1 &&
				parsed.pathname.indexOf( 'wp-admin' ) === -1 &&
				parsed.pathname.indexOf( 'post.php' ) === -1
			) {
				return false;
			}
			return true;
		} catch ( e ) {
			return false;
		}
	}

	$( function() {
		var config = window.lgxBulkUploadConfig || null;
		if ( ! config || ! config.ajax_url || ! config.nonce ) {
			return;
		}

		var $wrap            = $( '.lgx-bulk-upload__wrap' );
		var $selectBtn       = $( '#lgx-bulk-upload-select-btn' );
		var $addBtn          = $( '#lgx-bulk-upload-add-btn' );
		var $announcer       = $( '#lgx-bulk-upload-announcer' );
		var $selectPanel     = $( '#lgx-bulk-upload-select' );
		var $reviewRegion    = $( '#lgx-bulk-upload-review' );
		var $reviewHeading   = $( '#lgx-bulk-upload-review-heading' );
		var $backLink        = $( '.lgx-bulk-upload__back' );
		var $alertBox        = $( '#lgx-bulk-upload-alert' );
		var $countsList      = $( '#lgx-bulk-upload-counts' );
		var $summaryLine     = $( '#lgx-bulk-upload-summary' );
		var $footerText      = $( '#lgx-bulk-upload-footer-text' );
		var $tbody           = $( '#lgx-bulk-upload-tbody' );
		var $statusRadios    = $wrap.find( '.lgx-bulk-upload__status-radio' );
		var $publishNotice   = $( '#lgx-bulk-upload-publish-notice' );

		// Step 3 DOM elements (Creating card and Done screen).
		var $createBtn       = $( '#lgx-bulk-upload-create-btn' );
		var $stopBtn         = $( '#lgx-bulk-upload-stop-btn' );
		var $creatingCard    = $( '#lgx-bulk-upload-creating' );
		var $creatingList    = $( '#lgx-bulk-upload-creating-list' );
		var $progressWrap    = $( '#lgx-bulk-upload-progress' );
		var $progressBar     = $( '#lgx-bulk-upload-progress-bar' );
		var $progressInd     = $progressBar.find( '.lgx-bulk-upload__progress-indicator' );
		var $progressText    = $( '#lgx-bulk-upload-progress-text' );
		var $resultsCard     = $( '#lgx-bulk-upload-results' );
		var $resultsSummary  = $( '#lgx-bulk-upload-results-summary' );
		var $stopNotice      = $( '#lgx-bulk-upload-stop-notice' );
		var $createdCard     = $( '#lgx-bulk-upload-created-card' );
		var $createdHeading  = $( '#lgx-bulk-upload-created-heading' );
		var $createdBody     = $( '#lgx-bulk-upload-created-body' );
		var $draftNote       = $( '#lgx-bulk-upload-draft-note' );
		var $skippedCard     = $( '#lgx-bulk-upload-skipped-card' );
		var $skippedHeading  = $( '#lgx-bulk-upload-skipped-heading' );
		var $skippedList     = $( '#lgx-bulk-upload-skipped-list' );
		var $failedCard      = $( '#lgx-bulk-upload-failed-card' );
		var $failedHeading   = $( '#lgx-bulk-upload-failed-heading' );
		var $failedList      = $( '#lgx-bulk-upload-failed-list' );
		var $retryBtn        = $( '#lgx-bulk-upload-retry-btn' );
		var $restartBtn      = $( '#lgx-bulk-upload-restart-btn' );
		var $continueBtn     = $( '#lgx-bulk-upload-continue-btn' );
		var $followBtn       = $( '#lgx-bulk-upload-follow-btn' );

		if ( ! $wrap.length || ! $selectBtn.length || ! $tbody.length ) {
			return;
		}

		var checkCap = parseInt( config.check_cap, 10 );
		if ( isNaN( checkCap ) || checkCap < 1 ) {
			checkCap = 100;
		}

		var batchSize = parseInt( config.batch_size, 10 );
		if ( isNaN( batchSize ) || batchSize < 1 ) {
			batchSize = 5;
		}

		// Request timeouts (ms): 60s for batch logo creation, 30s for duplicate checking.
		var AJAX_CREATE_TIMEOUT = 60000;
		var AJAX_CHECK_TIMEOUT  = 30000;

		var isPro = ( config.is_pro === true || config.is_pro === '1' || config.is_pro === 1 );
		var DETAIL_KEYS = [ 'company_url', 'company_name', 'tooltip_text', 'alt_text', 'company_desc' ];

		var $theadRow = $wrap.find( '.lgx-bulk-upload__table thead tr' );
		if ( $theadRow.length && ! $theadRow.find( '.lgx-bulk-upload__col-details' ).length ) {
			var $colBrand = $theadRow.find( '.lgx-bulk-upload__col-brand' );
			var $colDetails = $( '<th/>' )
				.attr( 'scope', 'col' )
				.addClass( 'lgx-bulk-upload__col-details' )
				.text( __( 'Details', 'logo-slider-wp' ) );
			if ( $colBrand.length ) {
				$colBrand.after( $colDetails );
			}
		}

		/*
		 * `items` is the single source of truth for the row order: it is always kept in the
		 * same order as the table rows. The create run reads it top to bottom (D7 then sends
		 * the reverse of that order), so reordering rows never needs the server.
		 */
		var items          = [];
		var checkQueue     = [];
		var isChecking     = false;
		var sessionExpired = false;
		var mediaFrame     = null;
		var lastOpener     = null;
		var sortableReady  = false;
		var currentView    = 'select';

		// Create run state
		var isRunActive         = false;
		var activeRemovePopover = null;
		var stopRequested  = false;
		var runStatus      = 'draft';
		var runTerms       = [];
		var runResults     = [];
		var runList        = [];
		var runTotal       = 0;
		var runProcessed   = 0;
		var followProgress = true;
		var isProgrammaticScroll = false;

		/**
		 * Append text with %N$s placeholders to a jQuery element, swapping each placeholder
		 * for a node (jQuery object or DOM node) or a plain string. Strings are always added
		 * as text nodes, never parsed as markup.
		 *
		 * @param {jQuery} $el    Target element.
		 * @param {string} format Translated string with %1$s, %2$s, ... placeholders.
		 * @param {Object} map    Placeholder number to node or string.
		 */
		function appendRich( $el, format, map ) {
			var parts = String( format ).split( /(%\d+\$s)/ );
			for ( var p = 0; p < parts.length; p++ ) {
				var token = parts[ p ].match( /^%(\d+)\$s$/ );
				if ( token && typeof map[ token[ 1 ] ] !== 'undefined' ) {
					var value = map[ token[ 1 ] ];
					$el.append( typeof value === 'string' ? document.createTextNode( value ) : value );
				} else if ( parts[ p ] !== '' ) {
					$el.append( document.createTextNode( parts[ p ] ) );
				}
			}
		}

		/**
		 * Whether the current user may publish logos.
		 *
		 * @return {boolean} True when the Publish choice is allowed.
		 */
		function canPublishNow() {
			return ( config.can_publish === '1' || config.can_publish === 1 || config.can_publish === true );
		}

		/**
		 * Read the chosen status radio (Draft unless Publish is allowed and chosen).
		 *
		 * @return {string} 'draft' or 'publish'.
		 */
		function getChosenStatus() {
			var chosen = $statusRadios.filter( ':checked' ).val();
			return ( chosen === 'publish' && canPublishNow() ) ? 'publish' : 'draft';
		}

		/**
		 * Visible label for a status value.
		 *
		 * @param {string} status 'draft' or 'publish'.
		 * @return {string} Translated label.
		 */
		function getStatusLabel( status ) {
			return status === 'publish' ? __( 'Published', 'logo-slider-wp' ) : __( 'Draft', 'logo-slider-wp' );
		}

		/**
		 * Build the same-site admin edit URL for a logo post id.
		 *
		 * @param {number} postId Logo post id.
		 * @return {string} Edit URL or an empty string when no safe URL can be built.
		 */
		function buildEditUrl( postId ) {
			var id = parseInt( postId, 10 );
			if ( isNaN( id ) || id <= 0 || typeof config.ajax_url !== 'string' ) {
				return '';
			}
			var url = config.ajax_url.replace( /admin-ajax\.php.*$/, '' ) + 'post.php?post=' + id + '&action=edit';
			return isSafeAdminUrl( url ) ? url : '';
		}

		/**
		 * Show or hide an element with the hidden attribute.
		 *
		 * @param {jQuery}  $el  Element.
		 * @param {boolean} hide True to hide.
		 */
		function setHidden( $el, hide ) {
			if ( hide ) {
				$el.attr( 'hidden', 'hidden' );
			} else {
				$el.removeAttr( 'hidden' );
			}
		}

		/**
		 * Show exactly one of the four screens: select, review, creating or done.
		 *
		 * @param {string} view Screen name.
		 */
		function showView( view ) {
			currentView = view;
			setHidden( $selectPanel, view !== 'select' );
			setHidden( $reviewRegion, view !== 'review' );
			setHidden( $creatingCard, view !== 'creating' );
			setHidden( $resultsCard, view !== 'done' );
			setHidden( $backLink, view === 'creating' || view === 'done' );
			updateReviewBeforeUnload();
		}

		var reviewBeforeUnloadAttached = false;

		/**
		 * beforeunload event listener attached while the Review screen shows at least one row.
		 *
		 * @param {BeforeUnloadEvent} e Event object.
		 */
		function onReviewBeforeUnload( e ) {
			e.preventDefault();
			e.returnValue = '';
			return '';
		}

		/**
		 * Keep the review-screen leave warning attached only while on Review with >= 1 rows and not running.
		 */
		function updateReviewBeforeUnload() {
			var shouldAttach = ( currentView === 'review' && items.length > 0 && ! isRunActive );
			if ( shouldAttach && ! reviewBeforeUnloadAttached ) {
				window.addEventListener( 'beforeunload', onReviewBeforeUnload );
				reviewBeforeUnloadAttached = true;
			} else if ( ! shouldAttach && reviewBeforeUnloadAttached ) {
				window.removeEventListener( 'beforeunload', onReviewBeforeUnload );
				reviewBeforeUnloadAttached = false;
			}
		}

		/**
		 * beforeunload event listener attached only during active runs.
		 *
		 * @param {BeforeUnloadEvent} e Event object.
		 */
		function onBeforeUnload( e ) {
			e.preventDefault();
			e.returnValue = '';
			return '';
		}

		/**
		 * Find an item in state by attachment ID.
		 *
		 * @param {number} id Attachment ID.
		 * @return {Object|null} Item state object or null.
		 */
		function findItemById( id ) {
			var parsedId = parseInt( id, 10 );
			for ( var i = 0; i < items.length; i++ ) {
				if ( items[ i ].id === parsedId ) {
					return items[ i ];
				}
			}
			return null;
		}

		/**
		 * Index of an item in the ordered items array.
		 *
		 * @param {number} id Attachment ID.
		 * @return {number} Index or -1.
		 */
		function indexOfItem( id ) {
			var parsedId = parseInt( id, 10 );
			for ( var i = 0; i < items.length; i++ ) {
				if ( items[ i ].id === parsedId ) {
					return i;
				}
			}
			return -1;
		}

		/**
		 * Table row element of an item.
		 *
		 * @param {number} id Attachment ID.
		 * @return {jQuery} Row element (may be empty).
		 */
		function rowFor( id ) {
			return $tbody.find( 'tr.lgx-bulk-upload__row[data-attachment-id="' + id + '"]' );
		}

		/**
		 * Companion details drawer table row for an item (Pro only).
		 *
		 * @param {number} id Attachment ID.
		 * @return {jQuery} Drawer row element (may be empty).
		 */
		function drawerRowFor( id ) {
			return $tbody.find( 'tr.lgx-bulk-upload__details-row[data-details-for="' + id + '"]' );
		}

		/**
		 * Count how many of the five detail fields on an item are non-empty after trimming.
		 *
		 * @param {Object} item Item state object.
		 * @return {number} Count between 0 and 5.
		 */
		function countFilledDetails( item ) {
			if ( ! item || ! item.details ) {
				return 0;
			}
			var count = 0;
			for ( var i = 0; i < DETAIL_KEYS.length; i++ ) {
				var key = DETAIL_KEYS[ i ];
				if ( ( key === 'tooltip_text' && item.tooltipAuto !== false ) || ( key === 'alt_text' && item.altTextAuto !== false ) ) {
					continue;
				}
				var val = typeof item.details[ key ] === 'string' ? item.details[ key ].replace( /^\s+|\s+$/g, '' ) : '';
				if ( val !== '' ) {
					count++;
				}
			}
			return count;
		}

		/**
		 * Build the trimmed five-key details payload for an item when in Pro and at least
		 * one field is non-empty; otherwise return null.
		 *
		 * @param {Object} item Item state object.
		 * @return {Object|null} Five-key details object or null.
		 */
		function buildItemDetailsPayload( item ) {
			if ( ! isPro || ! item || ! item.details ) {
				return null;
			}
			var out    = {};
			var hasAny = false;
			for ( var i = 0; i < DETAIL_KEYS.length; i++ ) {
				var key = DETAIL_KEYS[ i ];
				var val = typeof item.details[ key ] === 'string' ? item.details[ key ].replace( /^\s+|\s+$/g, '' ) : '';
				if ( ( key === 'tooltip_text' && item.tooltipAuto !== false ) || ( key === 'alt_text' && item.altTextAuto !== false ) ) {
					val = '';
				}
				out[ key ] = val;
				if ( val !== '' ) {
					hasAny = true;
				}
			}
			return hasAny ? out : null;
		}

		/**
		 * Whether a row's Details control and drawer inputs are currently interactive.
		 * Disabled during a run and for rows that are done, failed, invalid, demo, or
		 * duplicate-skipped (unticked duplicate).
		 *
		 * @param {Object} item Item state object.
		 * @return {boolean} True when details can be opened and edited.
		 */
		function isDetailsEditable( item ) {
			if ( ! item || isRunActive ) {
				return false;
			}
			if ( item.state === 'done' || item.state === 'failed' || item.state === 'invalid' || item.state === 'demo' ) {
				return false;
			}
			if ( item.state === 'duplicate' && ! item.force ) {
				return false;
			}
			return true;
		}

		/**
		 * Refresh the Details toggle button label, aria-expanded, disabled state and
		 * companion drawer visibility for an item (Pro only).
		 *
		 * @param {Object} item Item state object.
		 */
		function updateDetailsControl( item ) {
			if ( ! item ) {
				return;
			}
			var $row       = rowFor( item.id );
			var $drawerRow = drawerRowFor( item.id );
			var $btn       = $row.find( '.lgx-bulk-upload__details-toggle' );
			if ( ! $btn.length ) {
				return;
			}

			var editable = isDetailsEditable( item );
			if ( ! editable ) {
				item.detailsOpen = false;
			}

			var isOpen      = Boolean( editable && item.detailsOpen );
			var filledCount = isPro ? countFilledDetails( item ) : 0;
			var labelText   = config.details_add || __( 'Add details', 'logo-slider-wp' );

			if ( isOpen ) {
				labelText = config.details_hide || __( 'Hide details', 'logo-slider-wp' );
			} else if ( isPro && filledCount > 0 ) {
				labelText = sprintf(
					/* translators: %d: number of filled detail fields */
					config.details_count || __( 'Details (%d)', 'logo-slider-wp' ),
					filledCount
				);
			}

			$btn.find( '.lgx-bulk-upload__details-toggle-text' ).text( labelText );
			$btn.attr( 'aria-expanded', isOpen ? 'true' : 'false' );
			$btn.toggleClass( 'lgx-bulk-upload__details-toggle--open', isOpen );
			$btn.toggleClass( 'lgx-bulk-upload__details-toggle--filled', isPro && ! isOpen && filledCount > 0 );

			$row.toggleClass( 'lgx-bulk-upload__row--details-open', isOpen );

			if ( editable ) {
				$btn.prop( 'disabled', false ).removeAttr( 'aria-disabled' );
			} else {
				$btn.prop( 'disabled', true ).attr( 'aria-disabled', 'true' );
			}

			if ( $drawerRow.length ) {
				$drawerRow
					.toggleClass( 'lgx-bulk-upload__details-row--open', isOpen )
					.toggleClass( 'lgx-bulk-upload__details-row--duplicate', item.state === 'duplicate' )
					.attr( 'aria-hidden', isOpen ? 'false' : 'true' );
				if ( isPro ) {
					$drawerRow.find( '.lgx-bulk-upload__detail-input' ).prop( 'disabled', ! editable );
				} else {
					$drawerRow.find( '.lgx-bulk-upload__detail-input' ).prop( 'disabled', true );
				}
			}
		}

		/**
		 * Set text in the polite screen-reader live region.
		 *
		 * @param {string} message Announcement text.
		 */
		function announce( message ) {
			$announcer.text( message );
		}

		/**
		 * Show an alert/warning banner under the step indicator.
		 *
		 * @param {string} message Banner text.
		 * @param {string} level   'error' or 'warning'.
		 */
		function showAlert( message, level ) {
			$alertBox
				.removeClass( 'lgx-bulk-upload__alert--error lgx-bulk-upload__alert--warning' )
				.addClass( level === 'error' ? 'lgx-bulk-upload__alert--error' : 'lgx-bulk-upload__alert--warning' )
				.text( message )
				.removeAttr( 'hidden' );
		}

		/**
		 * Hide and clear the alert banner.
		 */
		function clearAlert() {
			$alertBox
				.removeClass( 'lgx-bulk-upload__alert--error lgx-bulk-upload__alert--warning' )
				.text( '' )
				.attr( 'hidden', 'hidden' );
		}

		/**
		 * Update the three-step indicator (Select images, Review, Create).
		 *
		 * @param {number|string} [overrideStep] Optional override: 3 (Create is current) or 'done' (all three done).
		 */
		function updateStepIndicator( overrideStep ) {
			var $step1 = $wrap.find( '.lgx-step-indicator__step[data-step="1"]' );
			var $step2 = $wrap.find( '.lgx-step-indicator__step[data-step="2"]' );
			var $step3 = $wrap.find( '.lgx-step-indicator__step[data-step="3"]' );

			var allClasses = 'lgx-step-indicator__step--current lgx-step-indicator__step--done lgx-step-indicator__step--pending ' +
				'lgx-bulk-upload__step--current lgx-bulk-upload__step--done lgx-bulk-upload__step--pending';

			$step1.removeClass( allClasses ).removeAttr( 'aria-current' );
			$step2.removeClass( allClasses ).removeAttr( 'aria-current' );
			$step3.removeClass( allClasses ).removeAttr( 'aria-current' );

			if ( overrideStep === 'done' ) {
				$step1.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step1.find( '.lgx-step-indicator__badge' ).text( '\u2713' );

				$step2.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step2.find( '.lgx-step-indicator__badge' ).text( '\u2713' );

				$step3.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step3.find( '.lgx-step-indicator__badge' ).text( '\u2713' );
				return;
			}

			if ( overrideStep === 3 ) {
				$step1.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step1.find( '.lgx-step-indicator__badge' ).text( '\u2713' );

				$step2.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step2.find( '.lgx-step-indicator__badge' ).text( '\u2713' );

				$step3
					.addClass( 'lgx-step-indicator__step--current lgx-bulk-upload__step--current' )
					.attr( 'aria-current', 'step' );
				$step3.find( '.lgx-step-indicator__badge' ).text( '3' );
				return;
			}

			if ( items.length === 0 ) {
				$step1
					.addClass( 'lgx-step-indicator__step--current lgx-bulk-upload__step--current' )
					.attr( 'aria-current', 'step' );
				$step1.find( '.lgx-step-indicator__badge' ).text( '1' );

				$step2.addClass( 'lgx-step-indicator__step--pending lgx-bulk-upload__step--pending' );
				$step2.find( '.lgx-step-indicator__badge' ).text( '2' );
			} else {
				$step1.addClass( 'lgx-step-indicator__step--done lgx-bulk-upload__step--done' );
				$step1.find( '.lgx-step-indicator__badge' ).text( '\u2713' );

				$step2
					.addClass( 'lgx-step-indicator__step--current lgx-bulk-upload__step--current' )
					.attr( 'aria-current', 'step' );
				$step2.find( '.lgx-step-indicator__badge' ).text( '2' );
			}

			$step3.addClass( 'lgx-step-indicator__step--pending lgx-bulk-upload__step--pending' );
			$step3.find( '.lgx-step-indicator__badge' ).text( '3' );
		}

		/**
		 * Show the Select screen when no images are chosen and the Review screen as soon as
		 * images exist (the Creating and Done screens are left alone while they are showing).
		 */
		function updateReviewRegionVisibility() {
			if ( items.length === 0 ) {
				showView( 'select' );
				clearAlert();
				if ( $countsList.length ) {
					$countsList.empty();
				}
				$summaryLine.text( '' );
				$footerText.empty();
			} else if ( currentView === 'select' ) {
				showView( 'review' );
			}
			updateReviewBeforeUnload();
		}

		/**
		 * Calculate ready items count (ready rows + forced duplicate rows).
		 *
		 * @return {number} Count of ready items.
		 */
		function getReadyCount() {
			var count = 0;
			for ( var i = 0; i < items.length; i++ ) {
				var item = items[ i ];
				if ( item.state === 'ready' ) {
					count++;
				} else if ( item.state === 'duplicate' && item.force ) {
					count++;
				}
			}
			return count;
		}

		/**
		 * Count rows that will not be sent: unticked duplicates, demo images, unusable files.
		 *
		 * @return {number} Count of skipped rows.
		 */
		function getSkippedCount() {
			var count = 0;
			for ( var i = 0; i < items.length; i++ ) {
				var item = items[ i ];
				if ( ( item.state === 'duplicate' && ! item.force ) || item.state === 'demo' || item.state === 'invalid' ) {
					count++;
				}
			}
			return count;
		}

		/**
		 * Update the "Create logos" button state and pluralized label.
		 */
		function updateCreateButtonState() {
			if ( isRunActive ) {
				$createBtn.prop( 'disabled', true ).attr( 'aria-disabled', 'true' );
				return;
			}

			var readyCount = getReadyCount();
			if ( readyCount > 0 ) {
				$createBtn
					.prop( 'disabled', false )
					.removeAttr( 'aria-disabled' )
					.text(
						sprintf(
							/* translators: %d: number of logos to create */
							_n( 'Create %d logo', 'Create %d logos', readyCount, 'logo-slider-wp' ),
							readyCount
						)
					);
			} else {
				$createBtn
					.prop( 'disabled', true )
					.attr( 'aria-disabled', 'true' )
					.text( __( 'Create logos', 'logo-slider-wp' ) );
			}
		}

		/**
		 * Update the footer bar text ("N logos will be created as Draft · M skipped").
		 */
		function updateFooterText() {
			$footerText.empty();
			if ( items.length === 0 ) {
				return;
			}

			var readyCount   = getReadyCount();
			var skippedCount = getSkippedCount();

			appendRich(
				$footerText,
				/* translators: 1: number of logos that will be created, 2: Draft or Published */
				_n( '%1$s logo will be created as %2$s', '%1$s logos will be created as %2$s', readyCount, 'logo-slider-wp' ),
				{
					1: $( '<strong/>' ).text( String( readyCount ) ),
					2: $( '<strong/>' ).text( getStatusLabel( getChosenStatus() ) )
				}
			);

			if ( skippedCount > 0 ) {
				$footerText.append(
					document.createTextNode(
						' \u00b7 ' +
						sprintf(
							/* translators: %d: number of images that will be skipped */
							_n( '%d skipped', '%d skipped', skippedCount, 'logo-slider-wp' ),
							skippedCount
						)
					)
				);
			}
		}

		/**
		 * Update the review heading, the footer bar and the visually hidden live summary
		 * (pluralized counts of row states).
		 */
		/**
		 * Scroll the active (in-flight "creating") row into view within the Creating list.
		 * Uses block: 'nearest' to minimize jumpiness. If user manually scrolled,
		 * followProgress is false and this does not scroll.
		 */
		function scrollToActiveRow() {
			if ( ! followProgress ) {
				return;
			}
			var $active = $creatingList.find( '.lgx-bulk-upload__creating-item--creating' ).first();
			if ( ! $active.length ) {
				return;
			}
			var el = $active[ 0 ];
			if ( el && typeof el.scrollIntoView === 'function' ) {
				isProgrammaticScroll = true;
				el.scrollIntoView( { block: 'nearest' } );
				setTimeout( function() {
					isProgrammaticScroll = false;
				}, 150 );
			}
		}

		function updateSummaryLine() {
			if ( items.length === 0 ) {
				$summaryLine.text( '' );
				$footerText.empty();
				updateCreateButtonState();
				return;
			}

			var readyCount     = 0;
			var duplicateCount = 0;
			var demoCount      = 0;
			var invalidCount   = 0;
			var checkingCount  = 0;

			for ( var i = 0; i < items.length; i++ ) {
				var item = items[ i ];
				if ( item.state === 'checking' ) {
					checkingCount++;
				} else if ( item.state === 'ready' ) {
					readyCount++;
				} else if ( item.state === 'duplicate' ) {
					if ( item.force ) {
						readyCount++;
					} else {
						duplicateCount++;
					}
				} else if ( item.state === 'demo' ) {
					demoCount++;
				} else if ( item.state === 'invalid' ) {
					invalidCount++;
				}
			}

			var parts = [];
			parts.push(
				sprintf(
					/* translators: %d: number of images ready to create logos */
					_n( '%d ready', '%d ready', readyCount, 'logo-slider-wp' ),
					readyCount
				)
			);
			parts.push(
				sprintf(
					/* translators: %d: number of images already used by an existing logo */
					_n( '%d already used', '%d already used', duplicateCount, 'logo-slider-wp' ),
					duplicateCount
				)
			);
			parts.push(
				sprintf(
					/* translators: %d: number of demo images */
					_n( '%d demo', '%d demo', demoCount, 'logo-slider-wp' ),
					demoCount
				)
			);
			parts.push(
				sprintf(
					/* translators: %d: number of files that cannot be used as a logo image */
					_n( '%d not usable', '%d not usable', invalidCount, 'logo-slider-wp' ),
					invalidCount
				)
			);

			if ( checkingCount > 0 ) {
				parts.push(
					sprintf(
						/* translators: %d: number of images currently being checked */
						_n( '%d checking', '%d checking', checkingCount, 'logo-slider-wp' ),
						checkingCount
					)
				);
			}

			var notUsableCount = invalidCount + demoCount;

			if ( $countsList.length ) {
				$countsList.empty();

				// 1. Ready (always shown)
				var readyText = sprintf(
					/* translators: %d: number of images ready to create logos */
					_n( '%d ready', '%d ready', readyCount, 'logo-slider-wp' ),
					readyCount
				);
				var $readyLi = $( '<li/>' )
					.append( $( '<span class="lgx-bulk-upload__count-icon lgx-bulk-upload__count-icon--ok" aria-hidden="true">&#10003;</span>' ) )
					.append( document.createTextNode( readyText ) );
				$countsList.append( $readyLi );

				// 2. Already used (skipped) - only when > 0
				if ( duplicateCount > 0 ) {
					var dupText = sprintf(
						/* translators: %d: number of images already used by an existing logo (skipped) */
						_n( '%d already used (skipped)', '%d already used (skipped)', duplicateCount, 'logo-slider-wp' ),
						duplicateCount
					);
					var $dupLi = $( '<li/>' )
					.append( $( '<span class="lgx-bulk-upload__count-icon lgx-bulk-upload__count-icon--warn" aria-hidden="true">!</span>' ) )
					.append( document.createTextNode( dupText ) );
				$countsList.append( $dupLi );
				}

				// 3. Not usable (skipped) - only when > 0
				if ( notUsableCount > 0 ) {
					var notUsableText = sprintf(
						/* translators: %d: number of images that cannot be used as a logo (skipped) */
						_n( '%d not usable (skipped)', '%d not usable (skipped)', notUsableCount, 'logo-slider-wp' ),
						notUsableCount
					);
					var $notUsableLi = $( '<li/>' )
					.append( $( '<span class="lgx-bulk-upload__count-icon lgx-bulk-upload__count-icon--bad" aria-hidden="true">&times;</span>' ) )
					.append( document.createTextNode( notUsableText ) );
				$countsList.append( $notUsableLi );
				}
			}

			if ( $summaryLine.length ) {
				$summaryLine.text( parts.join( ', ' ) );
			}

			$reviewHeading.text(
				sprintf(
					/* translators: %d: number of images in the review table */
					_n( '%d image', '%d images', items.length, 'logo-slider-wp' ),
					items.length
				)
			);

			updateFooterText();
			refreshOrderControls();
			updateCreateButtonState();
		}

		/**
		 * Render or update the Check cell of a row (status text, notice or action).
		 *
		 * @param {Object} item Item state object.
		 */
		function renderRowNotice( item ) {
			var $row = rowFor( item.id );
			if ( ! $row.length ) {
				return;
			}

			var $slot  = $row.find( '.lgx-bulk-upload__row-notice-slot' );
			var $input = $row.find( '.lgx-bulk-upload__brand-input' );
			$slot.empty();

			$row
				.removeClass(
					'lgx-bulk-upload__row--checking lgx-bulk-upload__row--ready ' +
					'lgx-bulk-upload__row--duplicate lgx-bulk-upload__row--demo lgx-bulk-upload__row--invalid ' +
					'lgx-bulk-upload__row--done lgx-bulk-upload__row--failed'
				)
				.addClass( 'lgx-bulk-upload__row--' + item.state );

			var $drawerRowNotice = drawerRowFor( item.id );
			if ( $drawerRowNotice.length ) {
				$drawerRowNotice.toggleClass( 'lgx-bulk-upload__details-row--duplicate', item.state === 'duplicate' );
			}

			var noticeId = 'lgx-bulk-upload-row-notice-' + item.id;

			if ( item.state === 'done' ) {
				var isCreated = ( item.resultStatus === 'created' );
				var doneClass = isCreated
					? 'lgx-row-notice lgx-row-notice--success lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--done lgx-bulk-upload__row-notice--created'
					: 'lgx-row-notice lgx-row-notice--muted lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--done lgx-bulk-upload__row-notice--skipped';
				var doneSymbol = isCreated ? '\u2713 ' : '\u2014 ';
				var doneText   = isCreated
					? __( 'Logo created.', 'logo-slider-wp' )
					: ( item.resultMsg || __( 'Skipped', 'logo-slider-wp' ) );

				var $doneNotice = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( doneClass );

				var $doneText = $( '<span/>' )
					.addClass( 'lgx-row-notice__text lgx-bulk-upload__row-notice-text' )
					.text( doneSymbol + doneText );

				$doneNotice.append( $doneText );
				$slot.append( $doneNotice );
				$input.attr( 'aria-describedby', noticeId );
				$input.prop( 'disabled', true );
				$row.find( '.lgx-bulk-upload__force-checkbox' ).prop( 'disabled', true );
				updateDetailsControl( item );
				return;
			}

			if ( item.state === 'failed' ) {
				var $failNotice = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( 'lgx-row-notice lgx-row-notice--danger lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--failed' );

				var failMsg = item.resultMsg || __( 'Failed', 'logo-slider-wp' );
				var $failText = $( '<span/>' )
					.addClass( 'lgx-row-notice__text lgx-bulk-upload__row-notice-text' )
					.text( '\u2715 ' + failMsg );

				$failNotice.append( $failText );
				$slot.append( $failNotice );
				$input.attr( 'aria-describedby', noticeId );
				$input.prop( 'disabled', true );
				$row.find( '.lgx-bulk-upload__force-checkbox' ).prop( 'disabled', true );
				updateDetailsControl( item );
				return;
			}

			if ( item.state === 'checking' ) {
				var $checking = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( 'lgx-bulk-upload__row-checking' )
					.text( __( 'Checking...', 'logo-slider-wp' ) );
				$slot.append( $checking );
				$input.attr( 'aria-describedby', noticeId );
				updateDetailsControl( item );
				return;
			}

			if ( item.state === 'duplicate' ) {
				var dupPostId = ( item.duplicateOf && item.duplicateOf.post_id ) ? parseInt( item.duplicateOf.post_id, 10 ) : 0;
				var forceId   = 'lgx-bulk-upload-force-' + item.id;

				var $dupNotice = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( 'lgx-row-notice lgx-row-notice--warning lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--duplicate' );

				var $dupText = $( '<span/>' ).addClass( 'lgx-row-notice__text lgx-bulk-upload__row-notice-text' );

				if ( dupPostId > 0 ) {
					var dupTitle = ( item.duplicateOf && typeof item.duplicateOf.title === 'string' ) ? item.duplicateOf.title : '';
					var dupLinkText = dupTitle !== ''
						? ( dupTitle + ' (#' + dupPostId + ')' )
						: sprintf(
							/* translators: %d: existing logo post ID */
							__( 'Logo #%d', 'logo-slider-wp' ),
							dupPostId
						);
					var dupUrl = buildEditUrl( dupPostId );
					var $dupLink = dupUrl !== ''
						? $( '<a/>' ).addClass( 'lgx-bulk-upload__dup-link' ).attr( 'href', dupUrl ).text( dupLinkText )
						: $( '<span/>' ).addClass( 'lgx-bulk-upload__dup-link' ).text( dupLinkText );

					appendRich(
						$dupText,
						/* translators: 1: link to existing logo with its title and ID, 2: "Already used" bold prefix */
						__( '%2$s by the logo %1$s. It will be skipped.', 'logo-slider-wp' ),
						{
							1: $dupLink,
							2: $( '<strong/>' ).text( __( 'Already used', 'logo-slider-wp' ) )
						}
					);
				} else {
					$dupText.text(
						config.duplicate_no_title || __( 'Already used by another logo. It will be skipped.', 'logo-slider-wp' )
					);
				}

				var $forceLabel = $( '<label/>' )
					.addClass( 'lgx-row-notice__action lgx-bulk-upload__row-notice-action' )
					.attr( 'for', forceId );

				var $forceCheckbox = $( '<input/>' )
					.attr( 'type', 'checkbox' )
					.attr( 'id', forceId )
					.addClass( 'lgx-row-notice__checkbox lgx-bulk-upload__force-checkbox' )
					.prop( 'checked', Boolean( item.force ) );

				$forceCheckbox.on( 'change', function() {
					item.force = $( this ).prop( 'checked' );
					updateDetailsControl( item );
					updateSummaryLine();
				} );

				var $forceText = $( '<span/>' )
					.addClass( 'lgx-row-notice__action-label lgx-bulk-upload__force-label' )
					.text( __( 'Create anyway', 'logo-slider-wp' ) );

				$forceLabel.append( $forceCheckbox ).append( $forceText );
				$dupNotice.append( $dupText ).append( $forceLabel );
				$slot.append( $dupNotice );
				$input.attr( 'aria-describedby', noticeId );
				updateDetailsControl( item );
				return;
			}

			if ( item.state === 'demo' ) {
				var $demoNotice = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( 'lgx-row-notice lgx-row-notice--muted lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--demo' );

				var $demoText = $( '<span/>' )
					.addClass( 'lgx-row-notice__text lgx-bulk-upload__row-notice-text' )
					.text( __( 'This is a demo image. Upload your own logo.', 'logo-slider-wp' ) );

				$demoNotice.append( $demoText );
				$slot.append( $demoNotice );
				$input.attr( 'aria-describedby', noticeId );
				updateDetailsControl( item );
				return;
			}

			if ( item.state === 'invalid' ) {
				var $invalidNotice = $( '<div/>' )
					.attr( 'id', noticeId )
					.addClass( 'lgx-row-notice lgx-row-notice--muted lgx-bulk-upload__row-notice lgx-bulk-upload__row-notice--invalid' );

				var $invalidText = $( '<span/>' )
					.addClass( 'lgx-row-notice__text lgx-bulk-upload__row-notice-text' )
					.text( __( "This file can't be used as a logo image.", 'logo-slider-wp' ) );

				$invalidNotice.append( $invalidText );
				$slot.append( $invalidNotice );
				$input.attr( 'aria-describedby', noticeId );
				updateDetailsControl( item );
				return;
			}

			// Ready row: plain "Ready" text (not a notice).
			$slot.append(
				$( '<span/>' )
					.addClass( 'lgx-bulk-upload__row-ready' )
					.text( __( 'Ready', 'logo-slider-wp' ) )
			);
			$input.removeAttr( 'aria-describedby' );
			updateDetailsControl( item );
		}

		/**
		 * Mark an item's row with its run result, update the DOM row and the Creating list entry.
		 *
		 * @param {number} id      Attachment ID.
		 * @param {string} state   'done' or 'failed'.
		 * @param {string} msg     Server or client result message.
		 * @param {string} [subType] 'created', 'skipped', or 'failed'.
		 */
		function markItemRowResult( id, state, msg, subType ) {
			var it = findItemById( id );
			if ( ! it ) {
				return;
			}
			it.state        = state;
			it.resultStatus = subType || ( state === 'done' ? 'created' : 'failed' );
			it.resultMsg    = msg || '';
			it.runStatus    = runStatus;
			renderRowNotice( it );

			for ( var e = 0; e < runList.length; e++ ) {
				if ( runList[ e ].id === it.id ) {
					runList[ e ].state  = ( it.resultStatus === 'created' ) ? 'created' : ( it.resultStatus === 'skipped' ? 'skipped' : 'failed' );
					runList[ e ].status = runStatus;
					if ( runList[ e ].state === 'skipped' ) {
						runList[ e ].reason = it.duplicateOf ? 'duplicate' : 'other';
						runList[ e ].msg    = it.resultMsg;
					}
				}
			}
		}

		/**
		 * A row that was already processed (created, skipped or failed) never moves.
		 *
		 * @param {Object} item Item state object.
		 * @return {boolean} True when the row is fixed in place.
		 */
		function isFixedRow( item ) {
			return item.state === 'done' || item.state === 'failed';
		}

		/**
		 * Re-append the table rows in the order of the items array.
		 */
		function applyDomOrder() {
			for ( var i = 0; i < items.length; i++ ) {
				$tbody.append( rowFor( items[ i ].id ) );
				var $drawerRow = drawerRowFor( items[ i ].id );
				if ( $drawerRow.length ) {
					$tbody.append( $drawerRow );
				}
			}
		}

		/**
		 * Renumber the # column and refresh the drag handle and Move up / Move down states.
		 * Handle and buttons are disabled during a run and for rows that are done or failed;
		 * the first movable row cannot move up and the last movable row cannot move down.
		 */
		function refreshOrderControls() {
			var movableIdx = [];
			for ( var i = 0; i < items.length; i++ ) {
				if ( ! isFixedRow( items[ i ] ) ) {
					movableIdx.push( i );
				}
			}

			for ( var k = 0; k < items.length; k++ ) {
				var $row    = rowFor( items[ k ].id );
				var pos     = movableIdx.indexOf( k );
				var canMove = ! isRunActive && pos !== -1;

				$row.find( '.lgx-bulk-upload__row-number' ).text( String( k + 1 ) );
				$row.find( '.lgx-bulk-upload__drag-handle' )
					.toggleClass( 'lgx-bulk-upload__drag-handle--disabled', ! canMove )
					.attr( 'data-disabled', canMove ? 'false' : 'true' );
				$row.find( '.lgx-bulk-upload__move-btn--up' ).prop( 'disabled', ! canMove || pos === 0 );
				$row.find( '.lgx-bulk-upload__move-btn--down' ).prop( 'disabled', ! canMove || pos === movableIdx.length - 1 );
			}

			if ( sortableReady && typeof $tbody.sortable === 'function' ) {
				$tbody.sortable( 'option', 'disabled', isRunActive );
			}
		}

		/**
		 * Announce where a moved row ended up.
		 *
		 * @param {Object} item Moved item.
		 */
		function announceMove( item ) {
			announce(
				sprintf(
					/* translators: 1: image file name, 2: new position, 3: total number of rows */
					__( '%1$s moved to position %2$d of %3$d', 'logo-slider-wp' ),
					item.filename,
					indexOfItem( item.id ) + 1,
					items.length
				)
			);
		}

		/**
		 * Move a row up or down with the keyboard-friendly buttons.
		 *
		 * @param {number} id  Attachment ID.
		 * @param {number} dir -1 for up, 1 for down.
		 */
		function moveItem( id, dir ) {
			if ( isRunActive ) {
				return;
			}

			var idx = indexOfItem( id );
			if ( idx < 0 || isFixedRow( items[ idx ] ) ) {
				return;
			}

			var target = idx + dir;
			while ( target >= 0 && target < items.length && isFixedRow( items[ target ] ) ) {
				target += dir;
			}
			if ( target < 0 || target >= items.length ) {
				return;
			}

			var moved       = items[ idx ];
			items[ idx ]    = items[ target ];
			items[ target ] = moved;

			applyDomOrder();
			refreshOrderControls();
			announceMove( moved );

			var $rowMoved = rowFor( moved.id );
			var $focusBtn = $rowMoved.find( dir < 0 ? '.lgx-bulk-upload__move-btn--up' : '.lgx-bulk-upload__move-btn--down' );
			if ( $focusBtn.prop( 'disabled' ) ) {
				$focusBtn = $rowMoved.find( dir < 0 ? '.lgx-bulk-upload__move-btn--down' : '.lgx-bulk-upload__move-btn--up' );
			}
			$focusBtn.trigger( 'focus' );
			if ( $focusBtn[ 0 ] && typeof $focusBtn[ 0 ].focus === 'function' ) {
				$focusBtn[ 0 ].focus();
			}
		}

		/**
		 * Rebuild the items order from the table rows after a drag and drop. Rows that are done
		 * or failed keep their place; only the other rows take the new order.
		 *
		 * @param {number} movedId Attachment ID of the dragged row (0 when unknown).
		 */
		function syncOrderFromDom( movedId ) {
			if ( isRunActive ) {
				applyDomOrder();
				return;
			}

			var domIds = [];
			$tbody.children( 'tr[data-attachment-id]' ).each( function() {
				domIds.push( parseInt( $( this ).attr( 'data-attachment-id' ), 10 ) );
			} );

			var movableIdx = [];
			for ( var i = 0; i < items.length; i++ ) {
				if ( ! isFixedRow( items[ i ] ) ) {
					movableIdx.push( i );
				}
			}

			var domMovable = [];
			for ( var d = 0; d < domIds.length; d++ ) {
				var domItem = findItemById( domIds[ d ] );
				if ( domItem && ! isFixedRow( domItem ) ) {
					domMovable.push( domItem );
				}
			}

			if ( domMovable.length !== movableIdx.length ) {
				applyDomOrder();
				return;
			}

			var next = items.slice();
			for ( var m = 0; m < movableIdx.length; m++ ) {
				next[ movableIdx[ m ] ] = domMovable[ m ];
			}
			items = next;

			applyDomOrder();
			refreshOrderControls();

			var movedItem = movedId ? findItemById( movedId ) : null;
			if ( movedItem ) {
				announceMove( movedItem );
			}
		}

		/**
		 * Turn the review table body into a jQuery UI sortable (WordPress core script) when available.
		 */
		function initSortable() {
			if ( typeof $tbody.sortable !== 'function' ) {
				return;
			}

			$tbody.sortable( {
				items:                '> tr.lgx-bulk-upload__row:not(.lgx-bulk-upload__row--done):not(.lgx-bulk-upload__row--failed)',
				handle:               '.lgx-bulk-upload__drag-handle',
				axis:                 'y',
				tolerance:            'pointer',
				cursor:               'grabbing',
				placeholder:          'lgx-bulk-upload__sort-placeholder',
				forcePlaceholderSize: false,
				helper:               function( event, $tr ) {
					var $clone = $tr.clone();
					$clone.addClass( 'lgx-bulk-upload__sort-helper' );
					$clone.children().each( function( index ) {
						$( this ).css( 'width', $tr.children().eq( index ).outerWidth() + 'px' );
					} );
					return $clone;
				},
				update:               function( event, ui ) {
					var movedId = ( ui && ui.item ) ? parseInt( ui.item.attr( 'data-attachment-id' ), 10 ) : 0;
					syncOrderFromDom( isNaN( movedId ) ? 0 : movedId );
				}
			} );
			sortableReady = true;
		}

		/**
		 * Build a Move up / Move down icon button for a row.
		 *
		 * @param {Object} item Item state object.
		 * @param {number} dir  -1 for up, 1 for down.
		 * @return {jQuery} Button element.
		 */
		function buildMoveButton( item, dir ) {
			var label = sprintf(
				dir < 0
					/* translators: %s: image file name */
					? __( 'Move %s up', 'logo-slider-wp' )
					/* translators: %s: image file name */
					: __( 'Move %s down', 'logo-slider-wp' ),
				item.filename
			);

			var $btn = $( '<button/>' )
				.attr( 'type', 'button' )
				.addClass( 'lgx-bulk-upload__move-btn ' + ( dir < 0 ? 'lgx-bulk-upload__move-btn--up' : 'lgx-bulk-upload__move-btn--down' ) )
				.attr( 'aria-label', label )
				.attr( 'title', dir < 0 ? __( 'Move up', 'logo-slider-wp' ) : __( 'Move down', 'logo-slider-wp' ) )
				.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__move-icon' ).attr( 'aria-hidden', 'true' ) );

			$btn.on( 'click', function() {
				moveItem( item.id, dir );
			} );

			return $btn;
		}

		/**
		 * Build and append a single review table row using safe DOM methods only.
		 *
		 * @param {Object} item Item state object.
		 */
		function appendRowDom( item ) {
			var $tr = $( '<tr/>' )
				.attr( 'role', 'row' )
				.addClass( 'lgx-bulk-upload__row lgx-bulk-upload__row--' + item.state )
				.attr( 'data-attachment-id', String( item.id ) );

			// 0. Number column: drag handle, row number, Move up and Move down.
			var $tdNumber = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-number' );
			var $order    = $( '<div/>' ).addClass( 'lgx-bulk-upload__order' );
			var $handle   = $( '<span/>' )
				.addClass( 'lgx-bulk-upload__drag-handle' )
				.attr( 'aria-hidden', 'true' )
				.attr( 'title', __( 'Drag to change the order', 'logo-slider-wp' ) );
			var $number   = $( '<span/>' ).addClass( 'lgx-bulk-upload__row-number' );
			$order
				.append( $handle )
				.append( $number )
				.append( buildMoveButton( item, -1 ) )
				.append( buildMoveButton( item, 1 ) );
			$tdNumber.append( $order );

			// 1. Logo column (thumbnail, empty alt).
			var $tdImage = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-image' );
			if ( item.thumb ) {
				var $img = $( '<img/>' )
					.addClass( 'lgx-bulk-upload__thumb' )
					.attr( 'src', item.thumb )
					.attr( 'alt', '' );
				$tdImage.append( $img );
			} else {
				var $placeholder = $( '<span/>' )
					.addClass( 'lgx-bulk-upload__thumb-placeholder' )
					.attr( 'aria-hidden', 'true' );
				$tdImage.append( $placeholder );
			}

			// 2. File name column.
			var $tdFile = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-filename' );
			var $fileSpan = $( '<span/>' )
				.addClass( 'lgx-bulk-upload__filename' )
				.attr( 'title', item.filename )
				.text( item.filename );
			$tdFile.append( $fileSpan );

			// 3. Brand name column (input).
			var $tdBrand = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-brand' );
			var inputId  = 'lgx-bulk-upload-brand-' + item.id;
			var labelStr = sprintf(
				/* translators: %s: image file name */
				__( 'Brand name for %s', 'logo-slider-wp' ),
				item.filename
			);

			var $fieldWrap = $( '<div/>' ).addClass( 'lgx-field lgx-bulk-upload__brand-field' );
			var $srLabel   = $( '<label/>' )
				.addClass( 'screen-reader-text' )
				.attr( 'for', inputId )
				.text( labelStr );

			var brandLabelText = ( typeof bulkUploadSettings !== 'undefined' && bulkUploadSettings.col_brand_name )
				? bulkUploadSettings.col_brand_name
				: ( config.col_brand_name || __( 'Brand Name', 'logo-slider-wp' ) );
			var $brandLabel = $( '<span class="lgx-bulk-upload__brand-label" aria-hidden="true"/>' ).text( brandLabelText );

			var $brandInput = $( '<input/>' )
				.attr( 'type', 'text' )
				.attr( 'id', inputId )
				.addClass( 'lgx-field__control lgx-bulk-upload__brand-input' )
				.attr( 'aria-label', labelStr )
				.val( item.brandName );

			$brandInput.on( 'input change', function() {
				item.brandName = $( this ).val();
				item.edited    = true;
				if ( isPro ) {
					var rawBrand = item.brandName || '';
					var trimmedBrand = rawBrand.replace( /^\s+|\s+$/g, '' );
					var brandDisplayVal = ( trimmedBrand !== '' ) ? rawBrand : '';
					var $drawer = drawerRowFor( item.id );
					if ( $drawer.length ) {
						if ( item.tooltipAuto !== false ) {
							var $tooltipInput = $drawer.find( '[data-detail-key="tooltip_text"]' );
							$tooltipInput.val( brandDisplayVal );
							$tooltipInput.addClass( 'lgx-bulk-upload__detail-input--auto lgx-bulk-upload__detail-input--synced' );
						}
						if ( item.altTextAuto !== false ) {
							var $altInput = $drawer.find( '[data-detail-key="alt_text"]' );
							$altInput.val( brandDisplayVal );
							$altInput.addClass( 'lgx-bulk-upload__detail-input--auto lgx-bulk-upload__detail-input--synced' );
						}
					}
				}
			} );

			$fieldWrap.append( $srLabel ).append( $brandLabel ).append( $brandInput );
			$tdBrand.append( $fieldWrap );

			// 4. Details column and companion drawer row (ADR-023 Decision 6 & 7).
			var drawerId = 'lgx-bulk-upload-details-drawer-' + item.id;
			var toggleId = 'lgx-bulk-upload-details-toggle-' + item.id;

			var $tdDetails = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-details' );
			var $detailsBtn = $( '<button/>' )
				.attr( 'type', 'button' )
				.attr( 'id', toggleId )
				.addClass( 'lgx-bulk-upload__details-toggle' )
				.attr( 'aria-expanded', 'false' )
				.attr( 'aria-controls', drawerId );

			if ( ! isPro ) {
				$detailsBtn.addClass( 'lgx-bulk-upload__details-toggle--locked' );
				$detailsBtn.append(
					$( '<span/>' )
						.addClass( 'dashicons dashicons-lock lgx-bulk-upload__lock-icon' )
						.attr( 'aria-hidden', 'true' )
				);
			}

			$detailsBtn.append(
				$( '<span/>' )
					.addClass( 'lgx-bulk-upload__details-toggle-text' )
					.text( config.details_add || __( 'Add details', 'logo-slider-wp' ) )
			);

			$detailsBtn.append(
				$( '<span/>' )
					.addClass( 'lgx-bulk-upload__details-chevron dashicons dashicons-arrow-down-alt2' )
					.attr( 'aria-hidden', 'true' )
			);

			$detailsBtn.on( 'click', function() {
				if ( ! isDetailsEditable( item ) ) {
					return;
				}
				item.detailsOpen = ! item.detailsOpen;
				updateDetailsControl( item );
			} );

			$tdDetails.append( $detailsBtn );

			var $drawerTr = $( '<tr/>' )
				.attr( 'role', 'row' )
				.attr( 'id', drawerId )
				.addClass( 'lgx-bulk-upload__details-row' )
				.attr( 'data-details-for', String( item.id ) )
				.attr( 'aria-hidden', 'true' );

			var $drawerTd       = $( '<td/>' ).attr( 'role', 'cell' ).attr( 'colspan', '7' ).addClass( 'lgx-bulk-upload__details-cell' );
			var $drawerCollapse = $( '<div/>' ).addClass( 'lgx-bulk-upload__details-collapse' );

			if ( isPro ) {
				var $drawerBox      = $( '<div/>' ).addClass( 'lgx-bulk-upload__details-drawer' );

				var prefix = ( config.detail_caption_prefix || __( 'Details for', 'logo-slider-wp' ) ).replace( /\s+$/, '' );
				var note   = ( config.detail_caption_note || __( 'Tooltip and Alt Text follow the Brand Name until you change them.', 'logo-slider-wp' ) ).replace( /^\s+/, '' );
				var $caption = $( '<p/>' ).addClass( 'lgx-bulk-upload__details-caption' );
				$caption.append( document.createTextNode( prefix + ' ' ) );
				var $strong = $( '<strong/>' ).text( item.filename || '' );
				$caption.append( $strong );
				$caption.append( document.createTextNode( '. ' + note ) );
				$drawerBox.append( $caption );

				var fieldDefs = [
					{
						key:         'company_url',
						idSuffix:    'url',
						label:       config.detail_url_label || __( 'Website URL', 'logo-slider-wp' ),
						placeholder: config.detail_url_placeholder || __( 'https://...', 'logo-slider-wp' ),
						type:        'url',
						inputmode:   'url',
						full:        false
					},
					{
						key:         'company_name',
						idSuffix:    'name',
						label:       config.detail_name_label || __( 'Custom Brand Name', 'logo-slider-wp' ),
						placeholder: config.detail_name_placeholder || __( 'Leave empty to use the Brand Name', 'logo-slider-wp' ),
						type:        'text',
						inputmode:   '',
						full:        false
					},
					{
						key:         'tooltip_text',
						idSuffix:    'tooltip',
						label:       config.detail_tooltip_label || __( 'Tooltip', 'logo-slider-wp' ),
						placeholder: config.detail_tooltip_placeholder || __( 'Uses Brand Name by default', 'logo-slider-wp' ),
						type:        'text',
						inputmode:   '',
						full:        false
					},
					{
						key:         'alt_text',
						idSuffix:    'alt',
						label:       config.detail_alt_label || __( 'Alt Text', 'logo-slider-wp' ),
						placeholder: config.detail_alt_placeholder || __( 'Uses Brand Name by default', 'logo-slider-wp' ),
						type:        'text',
						inputmode:   '',
						full:        false
					},
					{
						key:         'company_desc',
						idSuffix:    'desc',
						label:       config.detail_desc_label || __( 'Description', 'logo-slider-wp' ),
						placeholder: config.detail_desc_placeholder || __( 'Add a short description about this brand...', 'logo-slider-wp' ),
						type:        'textarea',
						inputmode:   '',
						full:        true
					}
				];

				for ( var fIdx = 0; fIdx < fieldDefs.length; fIdx++ ) {
					(function( def ) {
						var controlId = 'lgx-bulk-upload-detail-' + def.idSuffix + '-' + item.id;
						var $fWrap    = $( '<div/>' ).addClass(
							'lgx-field lgx-bulk-upload__detail-field' + ( def.full ? ' lgx-bulk-upload__detail-field--full' : '' )
						);
						var $fLabel   = $( '<label/>' )
							.addClass( 'lgx-field__label lgx-bulk-upload__detail-label' )
							.attr( 'for', controlId )
							.text( def.label );

						var $fControl;
						if ( def.type === 'textarea' ) {
							$fControl = $( '<textarea/>' )
								.attr( 'rows', '2' )
								.attr( 'id', controlId )
								.addClass( 'lgx-field__control lgx-bulk-upload__detail-input lgx-bulk-upload__detail-textarea' )
								.attr( 'placeholder', def.placeholder )
								.attr( 'data-detail-key', def.key );
						} else {
							$fControl = $( '<input/>' )
								.attr( 'type', def.type )
								.attr( 'id', controlId )
								.addClass( 'lgx-field__control lgx-bulk-upload__detail-input' )
								.attr( 'placeholder', def.placeholder )
								.attr( 'data-detail-key', def.key );
							if ( def.inputmode ) {
								$fControl.attr( 'inputmode', def.inputmode );
							}
						}

						var initialVal = '';
						var isAutoField = false;
						if ( def.key === 'tooltip_text' ) {
							if ( item.tooltipAuto !== false ) {
								item.tooltipAuto = true;
								isAutoField = true;
								var trimmedBrand = ( item.brandName || '' ).replace( /^\s+|\s+$/g, '' );
								initialVal = trimmedBrand !== '' ? item.brandName : '';
							} else {
								initialVal = ( item.details && item.details.tooltip_text ) ? item.details.tooltip_text : '';
							}
						} else if ( def.key === 'alt_text' ) {
							if ( item.altTextAuto !== false ) {
								item.altTextAuto = true;
								isAutoField = true;
								var trimmedBrand = ( item.brandName || '' ).replace( /^\s+|\s+$/g, '' );
								initialVal = trimmedBrand !== '' ? item.brandName : '';
							} else {
								initialVal = ( item.details && item.details.alt_text ) ? item.details.alt_text : '';
							}
						} else {
							initialVal = ( item.details && item.details[ def.key ] ) ? item.details[ def.key ] : '';
						}

						$fControl.val( initialVal );
						if ( isAutoField ) {
							$fControl.addClass( 'lgx-bulk-upload__detail-input--auto lgx-bulk-upload__detail-input--synced' );
						}

						$fControl.on( 'input change', function() {
							if ( ! item.details ) {
								item.details = {};
							}
							if ( def.key === 'tooltip_text' ) {
								item.tooltipAuto = false;
								$( this ).removeClass( 'lgx-bulk-upload__detail-input--auto lgx-bulk-upload__detail-input--synced' );
							} else if ( def.key === 'alt_text' ) {
								item.altTextAuto = false;
								$( this ).removeClass( 'lgx-bulk-upload__detail-input--auto lgx-bulk-upload__detail-input--synced' );
							}
							item.details[ def.key ] = $( this ).val();
							updateDetailsControl( item );
						} );

						$fWrap.append( $fLabel ).append( $fControl );
						$drawerBox.append( $fWrap );
					})( fieldDefs[ fIdx ] );
				}

				$drawerCollapse.append( $drawerBox );
			} else {
				var $lockedDrawerBox = $( '<div/>' ).addClass( 'lgx-bulk-upload__details-drawer lgx-bulk-upload__details-drawer--locked' );
				var $stack           = $( '<div/>' ).addClass( 'lgx-bulk-upload__locked-stack' );

				// Layer 1: Locked fields (disabled + inert + aria-hidden).
				var $lockedFields = $( '<div/>' )
					.addClass( 'lgx-bulk-upload__locked-fields' )
					.attr( 'inert', '' )
					.attr( 'aria-hidden', 'true' );

				var lockedFieldDefs = [
					{
						label:       config.detail_url_label || __( 'Website URL', 'logo-slider-wp' ),
						placeholder: config.detail_url_placeholder || __( 'https://...', 'logo-slider-wp' ),
						type:        'url',
						full:        false
					},
					{
						label:       config.detail_name_label || __( 'Custom Brand Name', 'logo-slider-wp' ),
						placeholder: config.detail_name_placeholder || __( 'Leave empty to use the Brand Name', 'logo-slider-wp' ),
						type:        'text',
						full:        false
					},
					{
						label:       config.detail_tooltip_label || __( 'Tooltip', 'logo-slider-wp' ),
						placeholder: config.detail_tooltip_placeholder || __( 'Uses Brand Name by default', 'logo-slider-wp' ),
						type:        'text',
						full:        false
					},
					{
						label:       config.detail_alt_label || __( 'Alt Text', 'logo-slider-wp' ),
						placeholder: config.detail_alt_placeholder || __( 'Uses Brand Name by default', 'logo-slider-wp' ),
						type:        'text',
						full:        false
					},
					{
						label:       config.detail_desc_label || __( 'Description', 'logo-slider-wp' ),
						placeholder: config.detail_desc_placeholder || __( 'Add a short description about this brand...', 'logo-slider-wp' ),
						type:        'textarea',
						full:        true
					}
				];

				for ( var lfIdx = 0; lfIdx < lockedFieldDefs.length; lfIdx++ ) {
					var lDef = lockedFieldDefs[ lfIdx ];
					var $lfWrap = $( '<div/>' ).addClass(
						'lgx-field lgx-bulk-upload__detail-field' + ( lDef.full ? ' lgx-bulk-upload__detail-field--full' : '' )
					);
					var $lfLabel = $( '<label/>' )
						.addClass( 'lgx-field__label lgx-bulk-upload__detail-label' )
						.text( lDef.label );

					var $lfControl;
					if ( lDef.type === 'textarea' ) {
						$lfControl = $( '<textarea/>' )
							.attr( 'rows', '2' )
							.attr( 'disabled', 'disabled' )
							.addClass( 'lgx-field__control lgx-bulk-upload__detail-input lgx-bulk-upload__detail-textarea' )
							.attr( 'placeholder', lDef.placeholder );
					} else {
						$lfControl = $( '<input/>' )
							.attr( 'type', lDef.type )
							.attr( 'disabled', 'disabled' )
							.addClass( 'lgx-field__control lgx-bulk-upload__detail-input' )
							.attr( 'placeholder', lDef.placeholder );
					}
					$lfWrap.append( $lfLabel ).append( $lfControl );
					$lockedFields.append( $lfWrap );
				}
				$stack.append( $lockedFields );

				// Layer 2: Message card layer.
				var cardTitleId = 'lgx-locked-title-' + item.id;
				var cardStepsId = 'lgx-locked-steps-' + item.id;

				var $card = $( '<div/>' )
					.addClass( 'lgx-bulk-upload__locked-card' )
					.attr( 'role', 'group' )
					.attr( 'aria-labelledby', cardTitleId );

				var $cardTitle = $( '<p/>' )
					.attr( 'id', cardTitleId )
					.addClass( 'lgx-bulk-upload__locked-title' )
					.append(
						$( '<span/>' )
							.addClass( 'dashicons dashicons-lock lgx-bulk-upload__lock-icon' )
							.attr( 'aria-hidden', 'true' )
					)
					.append(
						$( '<span/>' ).text( config.locked_details_title || __( 'Filling in details for Bulk Upload is a Pro Feature', 'logo-slider-wp' ) )
					);

				var $cardText = $( '<p/>' )
					.addClass( 'lgx-bulk-upload__locked-text' )
					.text( ( config.locked_details_text || __( 'Website URL and Description are still free. You can add them to each logo after it is created.', 'logo-slider-wp' ) ) + ' ' );

				var $howBtn = $( '<button/>' )
					.attr( 'type', 'button' )
					.addClass( 'lgx-bulk-upload__locked-how' )
					.attr( 'aria-expanded', 'false' )
					.attr( 'aria-controls', cardStepsId )
					.append( $( '<span/>' ).text( config.locked_show_how || __( 'Show me how', 'logo-slider-wp' ) ) )
					.append(
						$( '<span/>' )
							.addClass( 'lgx-bulk-upload__locked-how-chev' )
							.attr( 'aria-hidden', 'true' )
							.text( '\u203A' )
					);

				$cardText.append( $howBtn );

				var $stepsBox = $( '<div/>' )
					.attr( 'id', cardStepsId )
					.attr( 'hidden', '' )
					.addClass( 'lgx-bulk-upload__locked-steps' );

				var $ol = $( '<ol/>' );
				$ol.append( $( '<li/>' ).text( config.locked_step_1 || __( 'Click Create to add your logos.', 'logo-slider-wp' ) ) );

				var step2Format = config.locked_step_2 || __( 'On the next screen, click %s next to a logo. It opens in a new tab.', 'logo-slider-wp' );
				var step2Bold   = config.locked_step_2_bold || __( 'Edit in single view', 'logo-slider-wp' );
				var step2Parts  = step2Format.split( '%s' );
				var $step2Li    = $( '<li/>' );
				if ( step2Parts.length === 2 ) {
					$step2Li.append( document.createTextNode( step2Parts[ 0 ] ) );
					$step2Li.append( $( '<strong/>' ).text( step2Bold ) );
					$step2Li.append( document.createTextNode( step2Parts[ 1 ] ) );
				} else {
					$step2Li.text( sprintf( step2Format, step2Bold ) );
				}
				$ol.append( $step2Li );

				$ol.append( $( '<li/>' ).text( config.locked_step_3 || __( 'Add the Website URL and Description, then click Update.', 'logo-slider-wp' ) ) );
				$stepsBox.append( $ol );

				// Tip line.
				var $tipP = $( '<p/>' ).addClass( 'lgx-bulk-upload__locked-tip' );
				$tipP.append(
					$( '<span/>' )
						.addClass( 'dashicons dashicons-lightbulb lgx-bulk-upload__tip-icon' )
						.attr( 'aria-hidden', 'true' )
				);
				var $tipSpan = $( '<span/>' );
				var tipPrefix = config.locked_tip_prefix || __( 'Tip:', 'logo-slider-wp' );
				$tipSpan.append( $( '<strong/>' ).text( tipPrefix ) ).append( document.createTextNode( ' ' ) );

				var tipFormat = config.locked_tip_text || __( 'Adding only a few logos? %s lets you add each logo and its details on one page.', 'logo-slider-wp' );
				var tipLinkText = config.locked_tip_link || __( 'Add New Logo', 'logo-slider-wp' );
				var tipUrl = config.add_new_logo_url || 'post-new.php?post_type=logosliderwp';

				var tipParts = tipFormat.split( '%s' );
				if ( tipParts.length === 2 ) {
					$tipSpan.append( document.createTextNode( tipParts[ 0 ] ) );
					$tipSpan.append(
						$( '<a/>' )
							.attr( 'href', tipUrl )
							.attr( 'target', '_blank' )
							.attr( 'rel', 'noopener noreferrer' )
							.text( tipLinkText )
					);
					$tipSpan.append( document.createTextNode( tipParts[ 1 ] ) );
				} else {
					$tipSpan.append( document.createTextNode( sprintf( tipFormat, tipLinkText ) ) );
				}
				$tipP.append( $tipSpan );
				$stepsBox.append( $tipP );

				// Upgrade CTA.
				var upgradeUrl   = config.locked_upgrade_url || 'https://logichunt.com/product/wordpress-logo-slider/';
				var upgradeLabel = config.locked_upgrade_label || __( 'Upgrade to Pro', 'logo-slider-wp' );
				var $actionsP    = $( '<p/>' ).addClass( 'lgx-bulk-upload__locked-actions' );
				var $upgradeBtn  = $( '<a/>' )
					.addClass( 'lgx-bulk-upload__upgrade-btn' )
					.attr( 'href', upgradeUrl )
					.attr( 'target', '_blank' )
					.attr( 'rel', 'noopener noreferrer' )
					.text( upgradeLabel );
				$actionsP.append( $upgradeBtn );

				$howBtn.on( 'click', function() {
					var expanded = $( this ).attr( 'aria-expanded' ) === 'true';
					var nextState = ! expanded;
					$( this ).attr( 'aria-expanded', nextState ? 'true' : 'false' );
					if ( nextState ) {
						$stepsBox.removeAttr( 'hidden' );
					} else {
						$stepsBox.attr( 'hidden', '' );
					}
				} );

				$card.append( $cardTitle ).append( $cardText ).append( $stepsBox ).append( $actionsP );
				$stack.append( $card );
				$lockedDrawerBox.append( $stack );
				$drawerCollapse.append( $lockedDrawerBox );
			}

			$drawerTd.append( $drawerCollapse );
			$drawerTr.append( $drawerTd );

			// 5. Check column (status text or notice slot).
			var $tdCheck    = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-check' );
			var $noticeSlot = $( '<div/>' ).addClass( 'lgx-bulk-upload__row-notice-slot' );
			$tdCheck.append( $noticeSlot );

			// 6. Remove column (32px trash button with confirm popover).
			var $tdRemove   = $( '<td/>' ).attr( 'role', 'cell' ).addClass( 'lgx-bulk-upload__cell-remove' );
			var $removeWrap = $( '<div/>' ).addClass( 'lgx-bulk-upload__remove-wrap' );
			var removeAria  = sprintf(
				/* translators: %s: image file name */
				config.remove_aria_label || __( 'Remove %s', 'logo-slider-wp' ),
				item.filename
			);

			var $removeBtn = $( '<button/>' )
				.attr( 'type', 'button' )
				.addClass( 'lgx-bulk-upload__remove-btn' )
				.attr( 'aria-label', removeAria )
				.attr( 'aria-haspopup', 'dialog' )
				.attr( 'aria-expanded', 'false' )
				.append(
					$( '<span/>' )
						.addClass( 'dashicons dashicons-trash' )
						.attr( 'aria-hidden', 'true' )
				);

			$removeBtn.on( 'click', function( e ) {
				e.stopPropagation();
				openRemovePopover( item, $removeBtn, $tr, $removeWrap );
			} );

			$removeBtn.on( 'keydown', function( e ) {
				if ( ( e.key === 'Escape' || e.keyCode === 27 ) && activeRemovePopover && activeRemovePopover.$btn[ 0 ] === this ) {
					e.preventDefault();
					e.stopPropagation();
					closeRemovePopover();
				}
			} );

			$removeWrap.append( $removeBtn );
			$tdRemove.append( $removeWrap );

			$tr
				.append( $tdNumber )
				.append( $tdImage )
				.append( $tdFile )
				.append( $tdBrand )
				.append( $tdDetails )
				.append( $tdCheck )
				.append( $tdRemove );
			$tbody.append( $tr );
			$tbody.append( $drawerTr );

			renderRowNotice( item );
		}

		/**
		 * Close any currently open remove-confirmation popover.
		 */
		function closeRemovePopover() {
			if ( ! activeRemovePopover ) {
				return;
			}
			var $popover = activeRemovePopover.$popover;
			var $btn     = activeRemovePopover.$btn;
			var $row     = activeRemovePopover.$row;

			$popover.remove();
			$btn.attr( 'aria-expanded', 'false' );
			$row.removeClass( 'lgx-bulk-upload__row--popover-open' );
			activeRemovePopover = null;
			$( document ).off( 'click.lgxRemovePopover mousedown.lgxRemovePopover' );
		}

		/**
		 * Open the remove-confirmation popover for a specific row item.
		 *
		 * @param {Object} item Row item object.
		 * @param {jQuery} $btn Trash button element.
		 * @param {jQuery} $row Table row element.
		 * @param {jQuery} $wrap Container element (.lgx-bulk-upload__remove-wrap).
		 */
		function openRemovePopover( item, $btn, $row, $wrap ) {
			if ( isRunActive || $btn.prop( 'disabled' ) ) {
				return;
			}

			// If already open on THIS button, clicking toggles it closed.
			if ( activeRemovePopover && activeRemovePopover.$btn[ 0 ] === $btn[ 0 ] ) {
				closeRemovePopover();
				$btn.trigger( 'focus' );
				return;
			}

			// Close any open popover on another row first.
			closeRemovePopover();

			var popTitle    = config.remove_confirm_title || __( 'Remove this logo?', 'logo-slider-wp' );
			var popTextTmpl = config.remove_confirm_text || __( '%s will not be created.', 'logo-slider-wp' );
			var popText     = sprintf( popTextTmpl, item.filename );
			var keepLabel   = config.remove_confirm_keep || __( 'Keep it', 'logo-slider-wp' );
			var removeLabel = config.remove_confirm_remove || __( 'Remove', 'logo-slider-wp' );

			var $popover = $( '<div/>' )
				.addClass( 'lgx-bulk-upload__remove-popover' )
				.attr( 'role', 'alertdialog' )
				.attr( 'aria-label', popTitle );

			var $text = $( '<p/>' )
				.addClass( 'lgx-bulk-upload__remove-popover-text' )
				.append( $( '<strong/>' ).text( popTitle ) )
				.append( $( '<br/>' ) )
				.append( document.createTextNode( popText ) );

			var $actions = $( '<div/>' ).addClass( 'lgx-bulk-upload__remove-popover-actions' );

			var $cancelBtn = $( '<button/>' )
				.attr( 'type', 'button' )
				.addClass( 'lgx-bulk-upload__remove-cancel' )
				.text( keepLabel );

			var $confirmBtn = $( '<button/>' )
				.attr( 'type', 'button' )
				.addClass( 'lgx-bulk-upload__remove-confirm' )
				.text( removeLabel );

			$actions.append( $cancelBtn ).append( $confirmBtn );
			$popover.append( $text ).append( $actions );

			$wrap.append( $popover );
			$btn.attr( 'aria-expanded', 'true' );
			$row.addClass( 'lgx-bulk-upload__row--popover-open' );

			activeRemovePopover = {
				$popover: $popover,
				$btn: $btn,
				$row: $row,
				id: item.id
			};

			// Focus moves to "Keep it" when the popover opens (the safe default).
			$cancelBtn.trigger( 'focus' );

			$cancelBtn.on( 'click', function( e ) {
				e.stopPropagation();
				closeRemovePopover();
				$btn.trigger( 'focus' );
			} );

			$confirmBtn.on( 'click', function( e ) {
				e.stopPropagation();
				closeRemovePopover();
				removeItem( item.id );
			} );

			// Trap Tab within the 2 buttons and close on Escape.
			$popover.on( 'keydown', function( e ) {
				if ( e.key === 'Escape' || e.keyCode === 27 ) {
					e.preventDefault();
					e.stopPropagation();
					closeRemovePopover();
					$btn.trigger( 'focus' );
					return;
				}

				if ( e.key === 'Tab' || e.keyCode === 9 ) {
					if ( e.shiftKey ) {
						if ( document.activeElement === $cancelBtn[ 0 ] ) {
							e.preventDefault();
							$confirmBtn.trigger( 'focus' );
						}
					} else {
						if ( document.activeElement === $confirmBtn[ 0 ] ) {
							e.preventDefault();
							$cancelBtn.trigger( 'focus' );
						}
					}
				}
			} );

			// Clicking outside closes it without removing and without stealing focus.
			setTimeout( function() {
				$( document ).on( 'click.lgxRemovePopover mousedown.lgxRemovePopover', function( e ) {
					if ( ! activeRemovePopover ) {
						return;
					}
					var target = e.target;
					if ( $popover[ 0 ].contains( target ) || $btn[ 0 ].contains( target ) ) {
						return;
					}
					closeRemovePopover();
				} );
			}, 0 );
		}

		/**
		 * Remove an item by attachment ID and update page state.
		 *
		 * @param {number} id Attachment ID.
		 */
		function removeItem( id ) {
			if ( isRunActive ) {
				return;
			}

			closeRemovePopover();

			var removedFilename = '';
			var nextItems       = [];
			var removeIdx       = -1;

			for ( var i = 0; i < items.length; i++ ) {
				if ( items[ i ].id === id ) {
					removedFilename = items[ i ].filename;
					removeIdx       = i;
				} else {
					nextItems.push( items[ i ] );
				}
			}

			var $nextFocusTarget = null;
			if ( removeIdx !== -1 ) {
				if ( removeIdx + 1 < items.length ) {
					var nextId = items[ removeIdx + 1 ].id;
					$nextFocusTarget = rowFor( nextId ).find( '.lgx-bulk-upload__remove-btn' );
				} else if ( removeIdx - 1 >= 0 ) {
					var prevId = items[ removeIdx - 1 ].id;
					$nextFocusTarget = rowFor( prevId ).find( '.lgx-bulk-upload__remove-btn' );
				}
			}

			items = nextItems;

			// Also prune from any pending check chunks.
			for ( var c = checkQueue.length - 1; c >= 0; c-- ) {
				var filteredChunk = [];
				for ( var k = 0; k < checkQueue[ c ].length; k++ ) {
					if ( checkQueue[ c ][ k ] !== id ) {
						filteredChunk.push( checkQueue[ c ][ k ] );
					}
				}
				if ( filteredChunk.length === 0 ) {
					checkQueue.splice( c, 1 );
				} else {
					checkQueue[ c ] = filteredChunk;
				}
			}

			rowFor( id ).remove();
			drawerRowFor( id ).remove();

			announce( config.remove_announced || __( 'Logo removed.', 'logo-slider-wp' ) );

			refreshOrderControls();
			updateStepIndicator();
			updateReviewRegionVisibility();
			updateSummaryLine();

			if ( $nextFocusTarget && $nextFocusTarget.length && $nextFocusTarget.is( ':visible' ) ) {
				$nextFocusTarget.trigger( 'focus' );
			} else {
				var $addCtrl = $( '#lgx-bulk-upload-select-btn:visible, #lgx-bulk-upload-add-btn:visible' ).first();
				if ( $addCtrl.length ) {
					$addCtrl.trigger( 'focus' );
				}
			}
		}

		/**
		 * Handle session/nonce expiry (403 or non-JSON response).
		 */
		function handleSessionExpired() {
			sessionExpired = true;
			checkQueue     = [];
			isChecking     = false;

			showAlert(
				__( 'Your session has expired. Reload the page and try again.', 'logo-slider-wp' ),
				'error'
			);
			updateSummaryLine();
		}

		/**
		 * Handle soft failure (500 or network error) during pre-check.
		 *
		 * @param {Array<number>} chunkIds IDs in the failed chunk.
		 */
		function handleSoftCheckFailure( chunkIds ) {
			var idsToMark = chunkIds.slice();
			while ( checkQueue.length > 0 ) {
				var rest = checkQueue.shift();
				for ( var r = 0; r < rest.length; r++ ) {
					idsToMark.push( rest[ r ] );
				}
			}

			for ( var i = 0; i < idsToMark.length; i++ ) {
				var item = findItemById( idsToMark[ i ] );
				if ( item && item.state === 'checking' ) {
					item.state       = 'ready';
					item.duplicateOf = null;
					item.force       = false;
					renderRowNotice( item );
				}
			}

			isChecking = false;

			showAlert(
				__( "Couldn't check for duplicates right now. The server checks again when you create.", 'logo-slider-wp' ),
				'warning'
			);
			updateSummaryLine();
		}

		/**
		 * Process the next chunk in checkQueue sequentially (at most checkCap IDs).
		 */
		function runNextCheckChunk() {
			if ( sessionExpired || isChecking ) {
				return;
			}
			if ( checkQueue.length === 0 ) {
				updateSummaryLine();
				return;
			}

			isChecking = true;
			var chunk  = checkQueue.shift();

			$.ajax( {
				url:      config.ajax_url,
				method:   'POST',
				type:     'POST',
				timeout:  AJAX_CHECK_TIMEOUT,
				dataType: 'json',
				data:     {
					action: config.check_action || 'lgx_ls_bulk_check',
					nonce:  config.nonce,
					ids:    chunk
				},
				success: function( response, textStatus, jqXHR ) {
					var parsed = response;
					if ( typeof parsed === 'string' ) {
						try {
							parsed = JSON.parse( parsed );
						} catch ( e ) {
							handleSessionExpired();
							return;
						}
					}

					if ( jqXHR && jqXHR.status === 403 ) {
						handleSessionExpired();
						return;
					}

					if ( ! parsed || typeof parsed !== 'object' || typeof parsed.success !== 'boolean' ) {
						handleSessionExpired();
						return;
					}

					if ( parsed.success !== true || ! parsed.data || ! Array.isArray( parsed.data.items ) ) {
						handleSoftCheckFailure( chunk );
						return;
					}

					var returnedMap = {};
					var serverItems = parsed.data.items;
					for ( var i = 0; i < serverItems.length; i++ ) {
						var entry = serverItems[ i ];
						if ( entry && typeof entry.attachment_id !== 'undefined' ) {
							returnedMap[ parseInt( entry.attachment_id, 10 ) ] = entry;
						}
					}

					for ( var k = 0; k < chunk.length; k++ ) {
						var attId = chunk[ k ];
						var item  = findItemById( attId );
						if ( ! item ) {
							continue;
						}

						var info = returnedMap[ attId ];
						if ( info && info.valid === false ) {
							item.state       = 'invalid';
							item.duplicateOf = null;
							item.force       = false;
						} else if ( info && info.demo === true ) {
							item.state       = 'demo';
							item.duplicateOf = null;
							item.force       = false;
						} else if ( info && info.duplicate && typeof info.duplicate === 'object' ) {
							item.state       = 'duplicate';
							item.duplicateOf = {
								post_id: ( info.duplicate.post_id ) ? parseInt( info.duplicate.post_id, 10 ) : 0,
								title:   typeof info.duplicate.title === 'string' ? info.duplicate.title : ''
							};
							item.force       = false;
						} else {
							item.state       = 'ready';
							item.duplicateOf = null;
							item.force       = false;
						}

						renderRowNotice( item );
					}

					isChecking = false;
					updateSummaryLine();
					runNextCheckChunk();
				},
				error: function( jqXHR, textStatus ) {
					var status    = ( jqXHR && typeof jqXHR.status === 'number' ) ? jqXHR.status : 0;
					var isNonJson = ( textStatus === 'parsererror' );

					if ( ! isNonJson && jqXHR && typeof jqXHR.responseText === 'string' && status >= 200 && status < 300 ) {
						try {
							JSON.parse( jqXHR.responseText );
						} catch ( e ) {
							isNonJson = true;
						}
					}

					var rawBody = ( jqXHR && typeof jqXHR.responseText === 'string' ) ? jqXHR.responseText.replace( /^\s+|\s+$/g, '' ) : '';
					if ( status === 403 || isNonJson || ( ( status === 400 || status === 403 ) && ( rawBody === '0' || rawBody === '-1' ) ) ) {
						handleSessionExpired();
						return;
					}

					handleSoftCheckFailure( chunk );
				}
			} );
		}

		/**
		 * Queue attachment IDs for sequential lgx_ls_bulk_check requests in chunks of checkCap.
		 *
		 * @param {Array<number>} newIds Newly added attachment IDs.
		 */
		function enqueueCheckIds( newIds ) {
			if ( ! newIds || newIds.length === 0 ) {
				return;
			}

			sessionExpired = false;
			clearAlert();

			for ( var i = 0; i < newIds.length; i += checkCap ) {
				checkQueue.push( newIds.slice( i, i + checkCap ) );
			}

			runNextCheckChunk();
		}

		/**
		 * Normalize a wp.media selection into an array of plain attachment objects.
		 *
		 * @param {Object|Array} selection Backbone collection or array of models/objects.
		 * @return {Array<Object>} Normalized attachment objects.
		 */
		function normalizeSelection( selection ) {
			var rawList = [];
			if ( ! selection ) {
				return rawList;
			}

			if ( typeof selection.each === 'function' ) {
				selection.each( function( model ) {
					rawList.push( model );
				} );
			} else if ( Array.isArray( selection ) ) {
				rawList = selection;
			} else if ( typeof selection.toArray === 'function' ) {
				rawList = selection.toArray();
			}

			var out = [];
			for ( var i = 0; i < rawList.length; i++ ) {
				var entry = rawList[ i ];
				if ( ! entry ) {
					continue;
				}
				var data = ( typeof entry.toJSON === 'function' ) ? entry.toJSON() : entry;
				var id   = parseInt( data && data.id ? data.id : ( typeof entry.get === 'function' ? entry.get( 'id' ) : 0 ), 10 );
				if ( isNaN( id ) || id <= 0 ) {
					continue;
				}

				var filename = '';
				if ( data && typeof data.filename === 'string' && data.filename !== '' ) {
					filename = data.filename;
				} else if ( data && typeof data.url === 'string' && data.url !== '' ) {
					filename = data.url.split( '/' ).pop().split( '?' )[ 0 ];
				} else {
					filename = 'image-' + id;
				}

				var title = ( data && typeof data.title === 'string' ) ? data.title : '';
				var thumb = '';
				if ( data && data.sizes && data.sizes.thumbnail && data.sizes.thumbnail.url ) {
					thumb = data.sizes.thumbnail.url;
				} else if ( data && typeof data.thumb === 'string' ) {
					thumb = data.thumb;
				} else if ( data && typeof data.url === 'string' ) {
					thumb = data.url;
				}

				out.push( {
					id:       id,
					filename: filename,
					title:    title,
					thumb:    thumb
				} );
			}

			return out;
		}

		/**
		 * Handle attachments selected from the WordPress Media frame.
		 *
		 * @param {Array<Object>} chosen Normalized attachment list.
		 */
		function handleAttachmentsSelected( chosen ) {
			var addedIds = [];

			for ( var i = 0; i < chosen.length; i++ ) {
				var att = chosen[ i ];
				if ( findItemById( att.id ) !== null ) {
					continue;
				}

				var newItem = {
					id:          att.id,
					filename:    att.filename,
					title:       att.title,
					thumb:       att.thumb,
					brandName:   deriveBrandName( att.title, att.filename ),
					edited:      false,
					state:       'checking',
					duplicateOf: null,
					force:       false,
					detailsOpen: false,
					tooltipAuto: true,
					altTextAuto: true,
					details:     {
						company_url:  '',
						company_name: '',
						tooltip_text: '',
						alt_text:     '',
						company_desc: ''
					}
				};

				items.push( newItem );
				addedIds.push( newItem.id );
				appendRowDom( newItem );
			}

			updateStepIndicator();
			updateReviewRegionVisibility();
			updateSummaryLine();

			if ( addedIds.length > 0 ) {
				announce(
					sprintf(
						/* translators: %d: number of images added to the review table */
						_n( '%d image added.', '%d images added.', addedIds.length, 'logo-slider-wp' ),
						addedIds.length
					)
				);
				enqueueCheckIds( addedIds );
			}
		}

		/**
		 * Open the WordPress Media frame; focus returns to the control that opened it.
		 *
		 * @param {jQuery} $opener Button that was clicked.
		 */
		function openMediaFrame( $opener ) {
			if ( isRunActive ) {
				return;
			}

			if ( ! window.wp || typeof window.wp.media !== 'function' ) {
				return;
			}

			lastOpener = $opener;

			if ( ! mediaFrame ) {
				mediaFrame = window.wp.media( {
					title:    __( 'Select Logo Images', 'logo-slider-wp' ),
					button:   {
						text: __( 'Add to list', 'logo-slider-wp' )
					},
					multiple: 'add',
					library:  {
						type: 'image'
					}
				} );

				mediaFrame.on( 'select', function() {
					var state     = mediaFrame.state();
					var selection = state && typeof state.get === 'function' ? state.get( 'selection' ) : null;
					var chosen    = normalizeSelection( selection );
					handleAttachmentsSelected( chosen );
				} );

				mediaFrame.on( 'close', function() {
					var $target = ( lastOpener && lastOpener.length ) ? lastOpener : $selectBtn;
					$target.trigger( 'focus' );
					if ( $target[ 0 ] && typeof $target[ 0 ].focus === 'function' ) {
						$target[ 0 ].focus();
					}
				} );
			}

			mediaFrame.open();
		}

		// "Select images" (Select screen) and "+ Add more images" (Review screen) open the same Media frame.
		$selectBtn.on( 'click', function( event ) {
			event.preventDefault();
			openMediaFrame( $selectBtn );
		} );

		$addBtn.on( 'click', function( event ) {
			event.preventDefault();
			openMediaFrame( $addBtn );
		} );

		// Status radios -> update the footer text and show or hide the polite Publish notice.
		$statusRadios.on( 'change', function() {
			if ( getChosenStatus() === 'publish' ) {
				$publishNotice
					.text(
						__( 'Published logos appear first in showcases that use Drag & Drop order and can push older logos out of a showcase with an Item Limit.', 'logo-slider-wp' )
					)
					.removeAttr( 'hidden' );
			} else {
				$publishNotice
					.text( '' )
					.attr( 'hidden', 'hidden' );
			}
			updateFooterText();
		} );

		initSortable();

		/* ==========================================================================
		   Step 3: Create Run, Progress, Stop, Results & Retry
		   ========================================================================== */

		/**
		 * Redraw the single Creating list (one row per item of the run, in row order).
		 */
		function renderCreatingList() {
			$creatingList.empty();

			for ( var i = 0; i < runList.length; i++ ) {
				var entry = runList[ i ];
				var mark  = '\u2022';
				var text  = __( 'Waiting', 'logo-slider-wp' );

				if ( entry.state === 'created' ) {
					mark = '\u2713';
					text = sprintf(
						/* translators: %s: Draft or Published */
						__( 'Created (%s)', 'logo-slider-wp' ),
						getStatusLabel( entry.status )
					);
				} else if ( entry.state === 'creating' ) {
					mark = '\u21bb';
					text = __( 'Creating...', 'logo-slider-wp' );
				} else if ( entry.state === 'skipped' ) {
					mark = '\u2013';
					if ( entry.reason === 'duplicate' ) {
						text = __( 'Skipped \u2014 image already used', 'logo-slider-wp' );
					} else if ( entry.reason === 'demo' ) {
						text = __( 'Skipped \u2014 demo image', 'logo-slider-wp' );
					} else if ( entry.reason === 'invalid' ) {
						text = __( 'Skipped \u2014 file not usable', 'logo-slider-wp' );
					} else {
						text = entry.msg ? ( __( 'Skipped', 'logo-slider-wp' ) + ' \u2014 ' + entry.msg ) : __( 'Skipped', 'logo-slider-wp' );
					}
				} else if ( entry.state === 'failed' ) {
					mark = '\u2715';
					text = __( 'Failed', 'logo-slider-wp' );
				}

				var $li = $( '<li/>' )
					.addClass( 'lgx-bulk-upload__creating-item lgx-bulk-upload__creating-item--' + entry.state )
					.attr( 'data-attachment-id', String( entry.id ) );

				$li.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__creating-mark' ).attr( 'aria-hidden', 'true' ).text( mark ) );
				$li.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__creating-name' ).text( entry.name ) );
				$li.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__creating-state' ).text( text ) );
				$creatingList.append( $li );
			}
		}

		/**
		 * Update progress bar attributes, width, the "X of Y done" text and the Creating list.
		 *
		 * @param {number} processed Processed items count.
		 * @param {number} total     Total items count.
		 */
		function updateProgress( processed, total ) {
			$progressWrap.removeAttr( 'hidden' );
			$progressBar.attr( 'aria-valuemin', '0' );
			$progressBar.attr( 'aria-valuemax', String( total ) );
			$progressBar.attr( 'aria-valuenow', String( processed ) );

			var pct = total > 0 ? Math.min( 100, Math.round( ( processed / total ) * 100 ) ) : 0;
			$progressInd.css( 'width', pct + '%' );

			$progressText.empty();
			appendRich(
				$progressText,
				/* translators: 1: processed items count, 2: total items count */
				__( '%1$s of %2$s done', 'logo-slider-wp' ),
				{
					1: $( '<strong/>' ).text( String( processed ) ),
					2: $( '<strong/>' ).text( String( total ) )
				}
			);

			renderCreatingList();
		}

		/**
		 * Build the "Skipped" entry text for a row that was never sent.
		 *
		 * @param {Object} item Item state object.
		 * @return {jQuery} Message element.
		 */
		function buildSkippedMessage( item ) {
			var $msg = $( '<span/>' ).addClass( 'lgx-bulk-upload__result-msg' );

			if ( item.state === 'duplicate' && item.duplicateOf && item.duplicateOf.post_id ) {
				var dupPostId = parseInt( item.duplicateOf.post_id, 10 );
				var dupTitle  = ( typeof item.duplicateOf.title === 'string' && item.duplicateOf.title !== '' )
					? item.duplicateOf.title
					: sprintf(
						/* translators: %d: existing logo post ID */
						__( 'Logo #%d', 'logo-slider-wp' ),
						dupPostId
					);
				var dupUrl = buildEditUrl( dupPostId );
				var $link  = dupUrl !== ''
					? $( '<a/>' ).addClass( 'lgx-bulk-upload__dup-link' ).attr( 'href', dupUrl ).text( dupTitle )
					: $( '<span/>' ).addClass( 'lgx-bulk-upload__dup-link' ).text( dupTitle );

				appendRich(
					$msg,
					/* translators: 1: link to the existing logo (its title), 2: existing logo post ID */
					__( 'This image is already used by the logo %1$s (#%2$s).', 'logo-slider-wp' ),
					{ 1: $link, 2: String( dupPostId ) }
				);
			} else if ( item.state === 'duplicate' ) {
				$msg.text( config.duplicate_no_title || __( 'Already used by another logo. It will be skipped.', 'logo-slider-wp' ) );
			} else if ( item.state === 'demo' ) {
				$msg.text( __( 'This is a demo image. Upload your own logo.', 'logo-slider-wp' ) );
			} else {
				$msg.text( __( "This file can't be used as a logo image.", 'logo-slider-wp' ) );
			}

			return $msg;
		}

		/**
		 * Build one entry of the Skipped or Failed card (brand name in bold, then the message).
		 *
		 * @param {string} name Brand name or file name.
		 * @return {jQuery} List item.
		 */
		function buildResultEntry( name ) {
			return $( '<li/>' )
				.addClass( 'lgx-bulk-upload__result-entry' )
				.append( $( '<strong/>' ).addClass( 'lgx-bulk-upload__result-name' ).text( name ) );
		}

		/**
		 * Render the Done screen with safe DOM APIs only.
		 */
		function renderDone() {
			var created = [];
			var skipped = [];
			var failed  = [];
			var i;

			for ( i = 0; i < runResults.length; i++ ) {
				var res = runResults[ i ];
				if ( res.result === 'created' ) {
					created.push( res );
				} else if ( res.result === 'skipped' ) {
					skipped.push( { kind: 'server', res: res } );
				} else if ( res.result === 'failed' ) {
					failed.push( res );
				}
			}

			// Rows that were never sent (unticked duplicates, demo images, unusable files) are skipped.
			for ( i = 0; i < items.length; i++ ) {
				var it = items[ i ];
				if ( ( it.state === 'duplicate' && ! it.force ) || it.state === 'demo' || it.state === 'invalid' ) {
					skipped.push( { kind: 'client', item: it } );
				}
			}

			var byRowOrder = function( a, b ) {
				var idA = a.kind ? ( a.kind === 'client' ? a.item.id : a.res.attachment_id ) : a.attachment_id;
				var idB = b.kind ? ( b.kind === 'client' ? b.item.id : b.res.attachment_id ) : b.attachment_id;
				return indexOfItem( idA ) - indexOfItem( idB );
			};
			created.sort( byRowOrder );
			skipped.sort( byRowOrder );
			failed.sort( byRowOrder );

			// Done notice (counts and status come from the run).
			var draftCreated     = 0;
			var publishedCreated = 0;
			for ( i = 0; i < created.length; i++ ) {
				var createdItem = findItemById( created[ i ].attachment_id );
				if ( createdItem && createdItem.runStatus === 'publish' ) {
					publishedCreated++;
				} else {
					draftCreated++;
				}
			}

			$resultsSummary.empty();
			$resultsSummary.append( $( '<strong/>' ).text( __( 'Done.', 'logo-slider-wp' ) ) );

			var parts = [];
			if ( created.length > 0 ) {
				if ( draftCreated > 0 && publishedCreated > 0 ) {
					parts.push(
						sprintf(
							/* translators: %d: number of created logos */
							_n( '%d logo created', '%d logos created', created.length, 'logo-slider-wp' ),
							created.length
						)
					);
				} else {
					parts.push(
						sprintf(
							/* translators: 1: number of created logos, 2: Draft or Published */
							_n( '%1$d logo created as %2$s', '%1$d logos created as %2$s', created.length, 'logo-slider-wp' ),
							created.length,
							getStatusLabel( publishedCreated > 0 ? 'publish' : 'draft' )
						)
					);
				}
			}
			if ( skipped.length > 0 ) {
				parts.push(
					sprintf(
						/* translators: %d: number of skipped items */
						_n( '%d skipped', '%d skipped', skipped.length, 'logo-slider-wp' ),
						skipped.length
					)
				);
			}
			if ( failed.length > 0 ) {
				parts.push(
					sprintf(
						/* translators: %d: number of failed items */
						_n( '%d failed', '%d failed', failed.length, 'logo-slider-wp' ),
						failed.length
					)
				);
			}
			$resultsSummary.append( document.createTextNode( ' ' + parts.join( ' \u00b7 ' ) + '.' ) );

			// Created card: Brand Name (link) | Status | Edit.
			$createdBody.empty();
			for ( i = 0; i < created.length; i++ ) {
				var cRes   = created[ i ];
				var cItem  = findItemById( cRes.attachment_id );
				var cName  = cRes.brand_name || cRes.filename || ( 'attachment-' + cRes.attachment_id );
				var cSafe  = cRes.edit_link && isSafeAdminUrl( cRes.edit_link );
				var $cTr   = $( '<tr/>' ).attr( 'data-attachment-id', String( cRes.attachment_id ) );
				var $cName = $( '<td/>' ).addClass( 'lgx-bulk-upload__created-name' );

				if ( cSafe ) {
					$cName.append( $( '<a/>' ).addClass( 'lgx-bulk-upload__result-name-link' ).attr( 'href', cRes.edit_link ).text( cName ) );
				} else {
					$cName.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__result-name' ).text( cName ) );
				}
				if ( cRes.warning ) {
					$cName.append( $( '<span/>' ).addClass( 'lgx-bulk-upload__result-warning' ).text( cRes.warning ) );
				}

				var $cStatus = $( '<td/>' ).addClass( 'lgx-bulk-upload__created-status' )
					.text( getStatusLabel( cItem ? cItem.runStatus : runStatus ) );
				var $cEdit   = $( '<td/>' ).addClass( 'lgx-bulk-upload__created-edit' );
				if ( cSafe ) {
					var $editAnchor = $( '<a/>' )
						.addClass( 'lgx-bulk-upload__result-edit-link' )
						.attr( 'href', cRes.edit_link );
					if ( isPro ) {
						$editAnchor
							.attr( 'aria-label', sprintf(
								/* translators: %s: brand name */
								__( 'Edit %s', 'logo-slider-wp' ),
								cName
							) )
							.text( __( 'Edit', 'logo-slider-wp' ) );
					} else {
						$editAnchor
							.attr( 'target', '_blank' )
							.attr( 'rel', 'noopener noreferrer' )
							.attr( 'aria-label', sprintf(
								/* translators: %s: brand name */
								config.result_edit_aria_free || __( 'Edit %s in single view', 'logo-slider-wp' ),
								cName
							) )
							.text( config.result_edit_link_free || __( 'Edit in single view', 'logo-slider-wp' ) );
					}
					$cEdit.append( $editAnchor );
				}

				$cTr.append( $cName ).append( $cStatus ).append( $cEdit );
				$createdBody.append( $cTr );
			}
			$createdHeading.text(
				sprintf(
					/* translators: %d: number of created logos */
					__( 'Created (%d)', 'logo-slider-wp' ),
					created.length
				)
			);
			setHidden( $createdCard, created.length === 0 );
			setHidden( $draftNote, draftCreated === 0 );

			if ( ! isPro ) {
				var $freeNote = $createdCard.find( '.lgx-bulk-upload__free-note' );
				if ( ! $freeNote.length ) {
					$freeNote = $( '<p/>' )
						.addClass( 'lgx-bulk-upload__free-note' )
						.text( config.result_free_note || __( 'Logos without a Website URL are not clickable. Add one in the single edit view.', 'logo-slider-wp' ) );
					$createdCard.append( $freeNote );
				}
				setHidden( $freeNote, created.length === 0 );
			}

			// Skipped card.
			$skippedList.empty();
			for ( i = 0; i < skipped.length; i++ ) {
				var sEntry = skipped[ i ];
				var sAttId = ( sEntry.kind === 'client' ) ? sEntry.item.id : sEntry.res.attachment_id;
				var $sLi;
				if ( sEntry.kind === 'client' ) {
					$sLi = buildResultEntry( sEntry.item.brandName || sEntry.item.filename );
					$sLi.append( buildSkippedMessage( sEntry.item ) );
				} else {
					$sLi = buildResultEntry( sEntry.res.brand_name || sEntry.res.filename || ( 'attachment-' + sEntry.res.attachment_id ) );
					$sLi.append(
						$( '<span/>' )
							.addClass( 'lgx-bulk-upload__result-msg' )
							.text( sEntry.res.warning || sEntry.res.message || __( 'Skipped', 'logo-slider-wp' ) )
					);
				}
				$sLi.addClass( 'lgx-bulk-upload__result-item' ).attr( 'data-attachment-id', String( sAttId ) );
				$skippedList.append( $sLi );
			}
			$skippedHeading.text(
				sprintf(
					/* translators: %d: number of skipped items */
					__( 'Skipped (%d)', 'logo-slider-wp' ),
					skipped.length
				)
			);
			setHidden( $skippedCard, skipped.length === 0 );

			// Failed card with "Retry failed" (D9). Unconfirmed items (D16) are never retried here.
			$failedList.empty();
			var retryableFailedCount = 0;
			for ( i = 0; i < failed.length; i++ ) {
				var fRes = failed[ i ];
				var $fLi = buildResultEntry( fRes.brand_name || fRes.filename || ( 'attachment-' + fRes.attachment_id ) );
				$fLi.append(
					$( '<span/>' )
						.addClass( 'lgx-bulk-upload__result-msg' )
						.text( fRes.message || __( 'Failed', 'logo-slider-wp' ) )
				);
				$fLi.addClass( 'lgx-bulk-upload__result-item' ).attr( 'data-attachment-id', String( fRes.attachment_id ) );
				$failedList.append( $fLi );
				if ( ! fRes.unconfirmed ) {
					retryableFailedCount++;
				}
			}
			$failedHeading.text(
				sprintf(
					/* translators: %d: number of failed items */
					__( 'Failed (%d)', 'logo-slider-wp' ),
					failed.length
				)
			);
			setHidden( $failedCard, failed.length === 0 );

			if ( retryableFailedCount > 0 && ! isRunActive ) {
				$retryBtn.removeAttr( 'hidden' ).prop( 'disabled', false );
			} else {
				$retryBtn.attr( 'hidden', 'hidden' );
			}

			// Rows that were stopped before they were sent can still be created from the review screen.
			var remaining = getReadyCount();
			if ( remaining > 0 && ! isRunActive ) {
				$continueBtn
					.text(
						sprintf(
							/* translators: %d: number of logos not created yet */
							_n( 'Back to review (%d logo left)', 'Back to review (%d logos left)', remaining, 'logo-slider-wp' ),
							remaining
						)
					)
					.removeAttr( 'hidden' );
			} else {
				$continueBtn.attr( 'hidden', 'hidden' ).text( '' );
			}
		}

		/**
		 * Redraw the Creating list while a run is active, or the Done screen once it has ended.
		 */
		function renderResultsList() {
			if ( isRunActive ) {
				renderCreatingList();
			} else {
				renderDone();
			}
		}

		/**
		 * Enable or disable editing controls on the page during a run.
		 *
		 * @param {boolean} disabled True to disable controls, false to re-enable.
		 */
		function setControlsDisabled( disabled ) {
			if ( disabled ) {
				closeRemovePopover();
			}
			$selectBtn.prop( 'disabled', disabled );
			$addBtn.prop( 'disabled', disabled );
			$wrap.find( '.lgx-bulk-upload__remove-btn' ).prop( 'disabled', disabled );
			$wrap.find( '.lgx-bulk-upload__brand-input' ).each( function() {
				var $inp = $( this );
				var attId = parseInt( $inp.closest( 'tr' ).attr( 'data-attachment-id' ), 10 );
				var it = findItemById( attId );
				if ( it && ( it.state === 'done' || it.state === 'failed' ) ) {
					$inp.prop( 'disabled', true );
				} else {
					$inp.prop( 'disabled', disabled );
				}
			} );
			$wrap.find( '.lgx-bulk-upload__force-checkbox' ).each( function() {
				var $cb = $( this );
				var attId = parseInt( $cb.closest( 'tr' ).attr( 'data-attachment-id' ), 10 );
				var it = findItemById( attId );
				if ( it && ( it.state === 'done' || it.state === 'failed' ) ) {
					$cb.prop( 'disabled', true );
				} else {
					$cb.prop( 'disabled', disabled );
				}
			} );
			$wrap.find( '.lgx-bulk-upload__term-checkbox' ).prop( 'disabled', disabled );
			$statusRadios.prop( 'disabled', disabled );
			$retryBtn.prop( 'disabled', disabled );
			$continueBtn.prop( 'disabled', disabled );
			for ( var dIdx = 0; dIdx < items.length; dIdx++ ) {
				updateDetailsControl( items[ dIdx ] );
			}
			refreshOrderControls();
		}

		/**
		 * Finalize a finished, stopped, or error-halted run.
		 *
		 * @param {string} [endReason] 'complete', 'stopped', 'error', 'expired'.
		 */
		function finalizeRun( endReason ) {
			isRunActive = false;
			window.removeEventListener( 'beforeunload', onBeforeUnload );

			$stopBtn.attr( 'hidden', 'hidden' ).prop( 'disabled', false );
			setControlsDisabled( false );
			updateCreateButtonState();
			updateSummaryLine();

			if ( runResults.length === 0 ) {
				// Nothing was processed (for example the session expired on the first request):
				// go back to the review screen where the alert is shown.
				showView( 'review' );
				updateStepIndicator();
				return;
			}

			renderDone();
			showView( 'done' );
			updateStepIndicator( getReadyCount() > 0 ? 3 : 'done' );

			followProgress       = true;
			isProgrammaticScroll = false;
			$followBtn.attr( 'hidden', 'hidden' );

			if ( endReason === 'stopped' ) {
				var createdCount = 0;
				var hasFailed    = false;
				for ( var ri = 0; ri < runResults.length; ri++ ) {
					if ( runResults[ ri ].result === 'created' ) {
						createdCount++;
					} else if ( runResults[ ri ].result === 'failed' ) {
						hasFailed = true;
					}
				}
				var remaining = getReadyCount();

				var stopSentence = '';
				if ( createdCount === 1 ) {
					if ( remaining === 1 ) {
						/* translators: 1: number of created logos (1), 2: number of remaining images (1) */
						stopSentence = sprintf( __( 'Stopped. %1$d logo was created. The other %2$d image was not sent. Click \u201cBack to review\u201d to create it.', 'logo-slider-wp' ), createdCount, remaining );
					} else {
						/* translators: 1: number of created logos (1), 2: number of remaining images (plural) */
						stopSentence = sprintf( __( 'Stopped. %1$d logo was created. The other %2$d images were not sent. Click \u201cBack to review\u201d to create them.', 'logo-slider-wp' ), createdCount, remaining );
					}
				} else {
					if ( remaining === 1 ) {
						/* translators: 1: number of created logos (plural/0), 2: number of remaining images (1) */
						stopSentence = sprintf( __( 'Stopped. %1$d logos were created. The other %2$d image was not sent. Click \u201cBack to review\u201d to create it.', 'logo-slider-wp' ), createdCount, remaining );
					} else {
						/* translators: 1: number of created logos (plural/0), 2: number of remaining images (plural) */
						stopSentence = sprintf( __( 'Stopped. %1$d logos were created. The other %2$d images were not sent. Click \u201cBack to review\u201d to create them.', 'logo-slider-wp' ), createdCount, remaining );
					}
				}

				if ( hasFailed ) {
					stopSentence += ' ' + __( 'Logos created by \u201cRetry failed\u201d appear above the others in your Logos list.', 'logo-slider-wp' );
				}

				$stopNotice.text( stopSentence ).removeAttr( 'hidden' );
			}

			// Move focus to the Done notice for accessibility.
			if ( $resultsSummary[ 0 ] && typeof $resultsSummary[ 0 ].focus === 'function' ) {
				$resultsSummary[ 0 ].focus();
			}
		}

		/**
		 * Execute batches sequentially against lgx_ls_bulk_create.
		 *
		 * @param {Array<Array<Object>>} batches Array of item batches.
		 * @param {number}               batchIdx Current batch index.
		 */
		function executeNextBatch( batches, batchIdx ) {
			if ( batchIdx >= batches.length ) {
				finalizeRun( 'complete' );
				return;
			}

			if ( stopRequested ) {
				finalizeRun( 'stopped' );
				return;
			}

			var batch = batches[ batchIdx ];

			// Mark this in-flight batch as "creating" in the Creating list.
			for ( var b0 = 0; b0 < batch.length; b0++ ) {
				for ( var l0 = 0; l0 < runList.length; l0++ ) {
					if ( runList[ l0 ].id === batch[ b0 ].id ) {
						runList[ l0 ].state = 'creating';
					}
				}
			}
			renderCreatingList();
			scrollToActiveRow();

			var postItems = [];
			for ( var i = 0; i < batch.length; i++ ) {
				var reqEntry = {
					attachment_id: batch[ i ].id,
					brand_name:    batch[ i ].brand_name,
					force:         batch[ i ].force ? 1 : 0
				};
				var srcItem = findItemById( batch[ i ].id );
				var detPayload = srcItem ? buildItemDetailsPayload( srcItem ) : ( batch[ i ].details || null );
				if ( isPro && detPayload ) {
					reqEntry.details = detPayload;
				}
				postItems.push( reqEntry );
			}

			$.ajax( {
				url:      config.ajax_url,
				method:   'POST',
				type:     'POST',
				timeout:  AJAX_CREATE_TIMEOUT,
				dataType: 'json',
				data:     {
					action: config.create_action || 'lgx_ls_bulk_create',
					nonce:  config.nonce,
					status: runStatus,
					terms:  runTerms,
					items:  postItems
				},
				success: function( response, textStatus, jqXHR ) {
					var parsed = response;
					if ( typeof parsed === 'string' ) {
						try {
							parsed = JSON.parse( parsed );
						} catch ( e ) {
							var unconfMsg = __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' );
							for ( var fi = 0; fi < batch.length; fi++ ) {
								runResults.push( {
									attachment_id: batch[ fi ].id,
									filename:      batch[ fi ].filename,
									brand_name:    batch[ fi ].brand_name,
									result:        'failed',
									unconfirmed:   true,
									post_id:       0,
									message:       unconfMsg,
									warning:       ''
								} );
								markItemRowResult( batch[ fi ].id, 'failed', unconfMsg );
							}
							runProcessed += batch.length;
							updateProgress( runProcessed, runTotal );
							handleSessionExpired();
							finalizeRun( 'expired' );
							return;
						}
					}

					if ( jqXHR && jqXHR.status === 403 ) {
						handleSessionExpired();
						finalizeRun( 'expired' );
						return;
					}

					if ( ! parsed || typeof parsed !== 'object' || typeof parsed.success !== 'boolean' ) {
						handleSessionExpired();
						finalizeRun( 'expired' );
						return;
					}

					// HTTP 400 whole-request error
					if ( parsed.success !== true ) {
						var wholeError = ( parsed.data && parsed.data.message )
							? parsed.data.message
							: __( 'The create request could not be completed.', 'logo-slider-wp' );
						showAlert( wholeError, 'error' );
						finalizeRun( 'error' );
						return;
					}

					if ( ! parsed.data || ! Array.isArray( parsed.data.items ) ) {
						handleSessionExpired();
						finalizeRun( 'expired' );
						return;
					}

					var replyMap = {};
					for ( var r = 0; r < parsed.data.items.length; r++ ) {
						var entry = parsed.data.items[ r ];
						if ( entry && typeof entry.attachment_id !== 'undefined' ) {
							replyMap[ parseInt( entry.attachment_id, 10 ) ] = entry;
						}
					}

					for ( var b = 0; b < batch.length; b++ ) {
						var itemObj   = batch[ b ];
						var serverRes = replyMap[ itemObj.id ];
						var resultVal = serverRes && serverRes.result ? serverRes.result : 'failed';
						var postIdVal = serverRes && serverRes.post_id ? parseInt( serverRes.post_id, 10 ) : 0;
						var msgVal    = serverRes && serverRes.message ? serverRes.message : '';
						var warnVal   = serverRes && serverRes.warning ? serverRes.warning : '';
						var editVal   = serverRes && serverRes.edit_link ? serverRes.edit_link : '';

						runResults.push( {
							attachment_id: itemObj.id,
							filename:      itemObj.filename,
							brand_name:    itemObj.brand_name,
							result:        resultVal,
							unconfirmed:   false,
							post_id:       postIdVal,
							message:       msgVal,
							warning:       warnVal,
							edit_link:     editVal,
							force:         itemObj.force
						} );

						if ( resultVal === 'created' || resultVal === 'skipped' ) {
							markItemRowResult( itemObj.id, 'done', msgVal, resultVal );
						} else {
							markItemRowResult( itemObj.id, 'failed', msgVal, 'failed' );
						}
					}

					runProcessed += batch.length;
					updateProgress( runProcessed, runTotal );
					renderResultsList();
					updateSummaryLine();
					updateCreateButtonState();

					executeNextBatch( batches, batchIdx + 1 );
				},
				error: function( jqXHR, textStatus ) {
					var status    = ( jqXHR && typeof jqXHR.status === 'number' ) ? jqXHR.status : 0;
					var isNonJson = ( textStatus === 'parsererror' );

					if ( ! isNonJson && jqXHR && typeof jqXHR.responseText === 'string' && status >= 200 && status < 300 ) {
						try {
							JSON.parse( jqXHR.responseText );
						} catch ( e ) {
							isNonJson = true;
						}
					}

					var rawBody = ( jqXHR && typeof jqXHR.responseText === 'string' ) ? jqXHR.responseText.replace( /^\s+|\s+$/g, '' ) : '';

					if ( isNonJson ) {
						var unconfMsgErr = __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' );
						for ( var fErr = 0; fErr < batch.length; fErr++ ) {
							runResults.push( {
								attachment_id: batch[ fErr ].id,
								filename:      batch[ fErr ].filename,
								brand_name:    batch[ fErr ].brand_name,
								result:        'failed',
								unconfirmed:   true,
								post_id:       0,
								message:       unconfMsgErr,
								warning:       ''
							} );
							markItemRowResult( batch[ fErr ].id, 'failed', unconfMsgErr );
						}
						runProcessed += batch.length;
						updateProgress( runProcessed, runTotal );
						handleSessionExpired();
						finalizeRun( 'expired' );
						return;
					}

					var isSessionExpired = ( status === 403 || ( ( status === 400 || status === 403 ) && ( rawBody === '0' || rawBody === '-1' ) ) );
					if ( isSessionExpired ) {
						handleSessionExpired();
						finalizeRun( 'expired' );
						return;
					}

					// HTTP 400 error
					if ( status === 400 && jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message ) {
						showAlert( jqXHR.responseJSON.data.message, 'error' );
						finalizeRun( 'error' );
						return;
					}

					// Network error or timeout (D16):
					// First call lgx_ls_bulk_check for this batch's attachment IDs.
					var batchIds = [];
					for ( var bi = 0; bi < batch.length; bi++ ) {
						batchIds.push( batch[ bi ].id );
					}

					$.ajax( {
						url:      config.ajax_url,
						method:   'POST',
						type:     'POST',
						timeout:  AJAX_CHECK_TIMEOUT,
						dataType: 'json',
						data:     {
							action: config.check_action || 'lgx_ls_bulk_check',
							nonce:  config.nonce,
							ids:    batchIds
						},
						success: function( checkResponse ) {
							var checkParsed = checkResponse;
							if ( typeof checkParsed === 'string' ) {
								try {
									checkParsed = JSON.parse( checkParsed );
								} catch ( e ) {
									handleSessionExpired();
									finalizeRun( 'expired' );
									return;
								}
							}

							if ( ! checkParsed || checkParsed.success !== true || ! checkParsed.data || ! Array.isArray( checkParsed.data.items ) ) {
								// Check failed -> stop run, mark batch items failed (forced items unconfirmed)
								for ( var f1 = 0; f1 < batch.length; f1++ ) {
									var isForcedF1 = !! batch[ f1 ].force;
									var failMsgF1  = isForcedF1
										? __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' )
										: __( 'Network error while creating logos.', 'logo-slider-wp' );
									runResults.push( {
										attachment_id: batch[ f1 ].id,
										filename:      batch[ f1 ].filename,
										brand_name:    batch[ f1 ].brand_name,
										result:        'failed',
										unconfirmed:   isForcedF1,
										post_id:       0,
										message:       failMsgF1,
										warning:       '',
										edit_link:     '',
										force:         batch[ f1 ].force
									} );
									markItemRowResult( batch[ f1 ].id, 'failed', failMsgF1 );
								}
								runProcessed += batch.length;
								updateProgress( runProcessed, runTotal );
								updateSummaryLine();
								updateCreateButtonState();
								showAlert(
									__( 'Network error while creating logos. The run has stopped. You can retry the remaining items.', 'logo-slider-wp' ),
									'warning'
								);
								finalizeRun( 'error' );
								return;
							}

							var checkMap = {};
							for ( var c = 0; c < checkParsed.data.items.length; c++ ) {
								var checkItem = checkParsed.data.items[ c ];
								if ( checkItem && typeof checkItem.attachment_id !== 'undefined' ) {
									checkMap[ parseInt( checkItem.attachment_id, 10 ) ] = checkItem;
								}
							}

							var missingBatch = [];
							for ( var m = 0; m < batch.length; m++ ) {
								var curItem = batch[ m ];
								if ( curItem.force ) {
									// Forced item in a batch whose create request failed at network level:
									// Do NOT decide from check reply and do NOT resend it automatically!
									var unconfMsg = __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' );
									runResults.push( {
										attachment_id: curItem.id,
										filename:      curItem.filename,
										brand_name:    curItem.brand_name,
										result:        'failed',
										unconfirmed:   true,
										post_id:       0,
										message:       unconfMsg,
										warning:       '',
										edit_link:     '',
										force:         true
									} );
									markItemRowResult( curItem.id, 'failed', unconfMsg );
								} else {
									var chk = checkMap[ curItem.id ];
									if ( chk && chk.duplicate && chk.duplicate.post_id ) {
										// Non-forced item: check says duplicate -> Created with that post_id
										var createdMsg = __( 'Logo created.', 'logo-slider-wp' );
										runResults.push( {
											attachment_id: curItem.id,
											filename:      curItem.filename,
											brand_name:    curItem.brand_name,
											result:        'created',
											unconfirmed:   false,
											post_id:       parseInt( chk.duplicate.post_id, 10 ),
											message:       createdMsg,
											warning:       '',
											edit_link:     '',
											force:         false
										} );
										markItemRowResult( curItem.id, 'done', createdMsg, 'created' );
									} else {
										missingBatch.push( curItem );
									}
								}
							}

							// If none missing, all succeeded or were marked unconfirmed
							if ( missingBatch.length === 0 ) {
								runProcessed += batch.length;
								updateProgress( runProcessed, runTotal );
								renderResultsList();
								updateSummaryLine();
								updateCreateButtonState();
								executeNextBatch( batches, batchIdx + 1 );
								return;
							}

							// Resend ONLY missing items ONCE (D16)
							var resendItems = [];
							for ( var rs = 0; rs < missingBatch.length; rs++ ) {
								var resendEntry = {
									attachment_id: missingBatch[ rs ].id,
									brand_name:    missingBatch[ rs ].brand_name,
									force:         missingBatch[ rs ].force ? 1 : 0
								};
								var rsRowItem = findItemById( missingBatch[ rs ].id );
								var rsDetails = rsRowItem ? buildItemDetailsPayload( rsRowItem ) : ( missingBatch[ rs ].details || null );
								if ( isPro && rsDetails ) {
									resendEntry.details = rsDetails;
								}
								resendItems.push( resendEntry );
							}

							$.ajax( {
								url:      config.ajax_url,
								method:   'POST',
								type:     'POST',
								timeout:  AJAX_CREATE_TIMEOUT,
								dataType: 'json',
								data:     {
									action: config.create_action || 'lgx_ls_bulk_create',
									nonce:  config.nonce,
									status: runStatus,
									terms:  runTerms,
									items:  resendItems
								},
								success: function( retryRes ) {
									var parsedRetry = retryRes;
									if ( typeof parsedRetry === 'string' ) {
										try {
											parsedRetry = JSON.parse( parsedRetry );
										} catch ( e ) {
											handleSessionExpired();
											finalizeRun( 'expired' );
											return;
										}
									}

									if ( parsedRetry && parsedRetry.success === true && parsedRetry.data && Array.isArray( parsedRetry.data.items ) ) {
										var retryMap = {};
										for ( var rti = 0; rti < parsedRetry.data.items.length; rti++ ) {
											var rEntry = parsedRetry.data.items[ rti ];
											if ( rEntry && typeof rEntry.attachment_id !== 'undefined' ) {
												retryMap[ parseInt( rEntry.attachment_id, 10 ) ] = rEntry;
											}
										}

										for ( var ms = 0; ms < missingBatch.length; ms++ ) {
											var msItem  = missingBatch[ ms ];
											var msRes   = retryMap[ msItem.id ];
											var resType = msRes && msRes.result ? msRes.result : 'failed';
											var rPostId = msRes && msRes.post_id ? parseInt( msRes.post_id, 10 ) : 0;
											var rMsg    = msRes && msRes.message ? msRes.message : '';
											var rWarn   = msRes && msRes.warning ? msRes.warning : '';
											var rEdit   = msRes && msRes.edit_link ? msRes.edit_link : '';

											runResults.push( {
												attachment_id: msItem.id,
												filename:      msItem.filename,
												brand_name:    msItem.brand_name,
												result:        resType,
												unconfirmed:   false,
												post_id:       rPostId,
												message:       rMsg,
												warning:       rWarn,
												edit_link:     rEdit,
												force:         msItem.force
											} );

											if ( resType === 'created' || resType === 'skipped' ) {
												markItemRowResult( msItem.id, 'done', rMsg, resType );
											} else {
												markItemRowResult( msItem.id, 'failed', rMsg, 'failed' );
											}
										}

										runProcessed += batch.length;
										updateProgress( runProcessed, runTotal );
										renderResultsList();
										updateSummaryLine();
										updateCreateButtonState();
										executeNextBatch( batches, batchIdx + 1 );
									} else {
										// Resend failed -> stop run, mark missing items failed
										for ( var rf = 0; rf < missingBatch.length; rf++ ) {
											var failResendMsg = __( 'Network error while creating logos.', 'logo-slider-wp' );
											runResults.push( {
												attachment_id: missingBatch[ rf ].id,
												filename:      missingBatch[ rf ].filename,
												brand_name:    missingBatch[ rf ].brand_name,
												result:        'failed',
												unconfirmed:   false,
												post_id:       0,
												message:       failResendMsg,
												warning:       '',
												edit_link:     '',
												force:         missingBatch[ rf ].force
											} );
											markItemRowResult( missingBatch[ rf ].id, 'failed', failResendMsg );
										}
										runProcessed += batch.length;
										updateProgress( runProcessed, runTotal );
										updateSummaryLine();
										updateCreateButtonState();
										showAlert(
											__( 'Network error while creating logos. The run has stopped. You can retry the remaining items.', 'logo-slider-wp' ),
											'warning'
										);
										finalizeRun( 'error' );
									}
								},
								error: function( retryXHR, retryStatus ) {
									var rtStatus  = ( retryXHR && typeof retryXHR.status === 'number' ) ? retryXHR.status : 0;
									var rtBody    = ( retryXHR && typeof retryXHR.responseText === 'string' ) ? retryXHR.responseText.replace( /^\s+|\s+$/g, '' ) : '';
									var rtNonJson = ( retryStatus === 'parsererror' );
									if ( rtStatus === 403 || rtNonJson || ( ( rtStatus === 400 || rtStatus === 403 ) && ( rtBody === '0' || rtBody === '-1' ) ) ) {
										var unconfMsgRt = __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' );
										for ( var rfExp = 0; rfExp < missingBatch.length; rfExp++ ) {
											runResults.push( {
												attachment_id: missingBatch[ rfExp ].id,
												filename:      missingBatch[ rfExp ].filename,
												brand_name:    missingBatch[ rfExp ].brand_name,
												result:        'failed',
												unconfirmed:   true,
												post_id:       0,
												message:       unconfMsgRt,
												warning:       '',
												edit_link:     '',
												force:         missingBatch[ rfExp ].force
											} );
											markItemRowResult( missingBatch[ rfExp ].id, 'failed', unconfMsgRt );
										}
										runProcessed += batch.length;
										updateProgress( runProcessed, runTotal );
										handleSessionExpired();
										finalizeRun( 'expired' );
										return;
									}
									for ( var rf2 = 0; rf2 < missingBatch.length; rf2++ ) {
										var failResendMsg2 = __( 'Network error while creating logos.', 'logo-slider-wp' );
										runResults.push( {
											attachment_id: missingBatch[ rf2 ].id,
											filename:      missingBatch[ rf2 ].filename,
											brand_name:    missingBatch[ rf2 ].brand_name,
											result:        'failed',
											unconfirmed:   false,
											post_id:       0,
											message:       failResendMsg2,
											warning:       '',
											edit_link:     '',
											force:         missingBatch[ rf2 ].force
										} );
										markItemRowResult( missingBatch[ rf2 ].id, 'failed', failResendMsg2 );
									}
									runProcessed += batch.length;
									updateProgress( runProcessed, runTotal );
									updateSummaryLine();
									updateCreateButtonState();
									showAlert(
										__( 'Network error while creating logos. The run has stopped. You can retry the remaining items.', 'logo-slider-wp' ),
										'warning'
									);
									finalizeRun( 'error' );
								}
							} );
						},
						error: function( recheckXHR, recheckStatus ) {
							var rcStatus  = ( recheckXHR && typeof recheckXHR.status === 'number' ) ? recheckXHR.status : 0;
							var rcBody    = ( recheckXHR && typeof recheckXHR.responseText === 'string' ) ? recheckXHR.responseText.replace( /^\s+|\s+$/g, '' ) : '';
							var rcNonJson = ( recheckStatus === 'parsererror' );
							if ( rcStatus === 403 || rcNonJson || ( ( rcStatus === 400 || rcStatus === 403 ) && ( rcBody === '0' || rcBody === '-1' ) ) ) {
								var unconfMsgRc = __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' );
								for ( var feExp = 0; feExp < batch.length; feExp++ ) {
									runResults.push( {
										attachment_id: batch[ feExp ].id,
										filename:      batch[ feExp ].filename,
										brand_name:    batch[ feExp ].brand_name,
										result:        'failed',
										unconfirmed:   true,
										post_id:       0,
										message:       unconfMsgRc,
										warning:       '',
										edit_link:     '',
										force:         batch[ feExp ].force
									} );
									markItemRowResult( batch[ feExp ].id, 'failed', unconfMsgRc );
								}
								runProcessed += batch.length;
								updateProgress( runProcessed, runTotal );
								handleSessionExpired();
								finalizeRun( 'expired' );
								return;
							}
							for ( var fe = 0; fe < batch.length; fe++ ) {
								var isForcedFe = !! batch[ fe ].force;
								var failMsgFe  = isForcedFe
									? __( 'Could not confirm whether this logo was created. Check All Logos before retrying.', 'logo-slider-wp' )
									: __( 'Network error while creating logos.', 'logo-slider-wp' );
								runResults.push( {
									attachment_id: batch[ fe ].id,
									filename:      batch[ fe ].filename,
									brand_name:    batch[ fe ].brand_name,
									result:        'failed',
									unconfirmed:   isForcedFe,
									post_id:       0,
									message:       failMsgFe,
									warning:       '',
									edit_link:     '',
									force:         batch[ fe ].force
								} );
								markItemRowResult( batch[ fe ].id, 'failed', failMsgFe );
							}
							runProcessed += batch.length;
							updateProgress( runProcessed, runTotal );
							updateSummaryLine();
							updateCreateButtonState();
							showAlert(
								__( 'Network error while creating logos. The run has stopped. You can retry the remaining items.', 'logo-slider-wp' ),
								'warning'
							);
							finalizeRun( 'error' );
						}
					} );
				}
			} );
		}

		/**
		 * Start a new bulk creation run with the eligible items.
		 *
		 * The items array is kept in the same order as the table rows, so "eligible" is in row
		 * order (top to bottom). D7: the run then sends the reverse of that order, which makes the
		 * top row the first logo in All Logos whatever reordering the user did.
		 *
		 * @param {Array<Object>} [itemsToRun] Optional custom items array in row order (used by retry).
		 */
		function startCreateRun( itemsToRun ) {
			if ( isRunActive ) {
				return;
			}

			var eligible = [];
			var skippedRows = [];
			if ( Array.isArray( itemsToRun ) ) {
				eligible = itemsToRun;
			} else {
				for ( var i = 0; i < items.length; i++ ) {
					var item = items[ i ];
					if ( item.state === 'ready' ) {
						eligible.push( {
							id:         item.id,
							brand_name: item.brandName,
							filename:   item.filename,
							force:      false,
							details:    buildItemDetailsPayload( item )
						} );
					} else if ( item.state === 'duplicate' && item.force ) {
						eligible.push( {
							id:         item.id,
							brand_name: item.brandName,
							filename:   item.filename,
							force:      true,
							details:    buildItemDetailsPayload( item )
						} );
					} else if ( item.state === 'duplicate' ) {
						skippedRows.push( { item: item, reason: 'duplicate' } );
					} else if ( item.state === 'demo' ) {
						skippedRows.push( { item: item, reason: 'demo' } );
					} else if ( item.state === 'invalid' ) {
						skippedRows.push( { item: item, reason: 'invalid' } );
					}
				}
			}

			if ( eligible.length === 0 ) {
				return;
			}

			closeRemovePopover();
			isRunActive   = true;
			stopRequested = false;
			clearAlert();
			$stopNotice.attr( 'hidden', 'hidden' ).text( '' );

			// Read common settings once at start (status and categories).
			runStatus = getChosenStatus();

			runTerms = [];
			$wrap.find( '.lgx-bulk-upload__term-checkbox:checked' ).each( function() {
				var tid = parseInt( $( this ).val(), 10 );
				if ( ! isNaN( tid ) && tid > 0 ) {
					runTerms.push( tid );
				}
			} );

			// Creating list: one row per item of the run in row order (waiting), plus skipped rows.
			var eligibleById = {};
			for ( var e = 0; e < eligible.length; e++ ) {
				eligibleById[ eligible[ e ].id ] = eligible[ e ];
			}
			runList = [];
			for ( var r = 0; r < items.length; r++ ) {
				var rowItem = items[ r ];
				if ( eligibleById[ rowItem.id ] ) {
					runList.push( {
						id:     rowItem.id,
						name:   eligibleById[ rowItem.id ].brand_name || rowItem.filename,
						state:  'waiting',
						status: runStatus
					} );
				} else {
					for ( var s = 0; s < skippedRows.length; s++ ) {
						if ( skippedRows[ s ].item.id === rowItem.id ) {
							runList.push( {
								id:     rowItem.id,
								name:   rowItem.brandName || rowItem.filename,
								state:  'skipped',
								reason: skippedRows[ s ].reason,
								status: runStatus
							} );
						}
					}
				}
			}

			// D7: Items are sent in REVERSE of the current row order.
			var queue = eligible.slice().reverse();
			var batches = [];
			for ( var b = 0; b < queue.length; b += batchSize ) {
				batches.push( queue.slice( b, b + batchSize ) );
			}

			runTotal     = queue.length;
			runProcessed = 0;

			followProgress       = true;
			isProgrammaticScroll = false;
			$followBtn.attr( 'hidden', 'hidden' );

			setControlsDisabled( true );
			$createBtn.prop( 'disabled', true ).attr( 'aria-disabled', 'true' );
			$stopBtn.removeAttr( 'hidden' ).prop( 'disabled', false ).text( __( 'Stop after this batch', 'logo-slider-wp' ) );

			window.addEventListener( 'beforeunload', onBeforeUnload );

			showView( 'creating' );
			updateStepIndicator( 3 );
			updateProgress( 0, runTotal );

			executeNextBatch( batches, 0 );
		}

		// "Create logos" primary button click.
		$createBtn.on( 'click', function( event ) {
			event.preventDefault();
			startCreateRun();
		} );

		// "Stop after this batch" button click (D17).
		$stopBtn.on( 'click', function( event ) {
			event.preventDefault();
			if ( ! isRunActive || stopRequested ) {
				return;
			}
			stopRequested = true;
			$stopBtn.prop( 'disabled', true ).text( __( 'Stopping...', 'logo-slider-wp' ) );
		} );

		// "Retry failed" button click (D9).
		$retryBtn.on( 'click', function( event ) {
			event.preventDefault();
			if ( isRunActive ) {
				return;
			}

			var retryById   = {};
			var keptResults = [];
			for ( var i = 0; i < runResults.length; i++ ) {
				var resObj = runResults[ i ];
				if ( resObj.result === 'failed' && ! resObj.unconfirmed ) {
					retryById[ resObj.attachment_id ] = resObj;
				} else {
					keptResults.push( resObj );
				}
			}

			// Build the retry list in row order so the run keeps the D7 rule (it sends the reverse).
			var failedItems = [];
			for ( var r = 0; r < items.length; r++ ) {
				var failedRes = retryById[ items[ r ].id ];
				if ( failedRes ) {
					failedItems.push( {
						id:         failedRes.attachment_id,
						brand_name: failedRes.brand_name,
						filename:   failedRes.filename,
						force:      failedRes.force,
						details:    buildItemDetailsPayload( items[ r ] )
					} );
				}
			}

			if ( failedItems.length === 0 ) {
				return;
			}

			runResults = keptResults;

			// "Retry failed" sets the retried rows to the run state again.
			for ( var fi = 0; fi < failedItems.length; fi++ ) {
				var it = findItemById( failedItems[ fi ].id );
				if ( it ) {
					it.state = 'checking';
					renderRowNotice( it );
				}
			}

			startCreateRun( failedItems );
		} );

		// "Upload more" (restart) button click: back to the Select screen with an empty list.
		$restartBtn.on( 'click', function( event ) {
			event.preventDefault();
			if ( isRunActive ) {
				return;
			}

			items        = [];
			checkQueue   = [];
			runResults   = [];
			runList      = [];
			runTotal     = 0;
			runProcessed = 0;

			$tbody.empty();
			$creatingList.empty();
			$createdBody.empty();
			$skippedList.empty();
			$failedList.empty();
			$progressText.empty();
			$stopNotice.attr( 'hidden', 'hidden' ).text( '' );
			$continueBtn.attr( 'hidden', 'hidden' ).text( '' );
			clearAlert();
			followProgress       = true;
			isProgrammaticScroll = false;
			$followBtn.attr( 'hidden', 'hidden' );
			if ( $countsList.length ) {
				$countsList.empty();
			}
			$summaryLine.text( '' );
			$footerText.empty();

			showView( 'select' );
			updateStepIndicator( 1 );
			updateReviewRegionVisibility();
			updateCreateButtonState();
			setControlsDisabled( false );

			$selectBtn.trigger( 'focus' );
			if ( $selectBtn[ 0 ] && typeof $selectBtn[ 0 ].focus === 'function' ) {
				$selectBtn[ 0 ].focus();
			}
		} );

		// "Back to review" button click: continue with the rows that were not sent yet.
		$continueBtn.on( 'click', function( event ) {
			event.preventDefault();
			if ( isRunActive ) {
				return;
			}

			showView( 'review' );
			updateStepIndicator();
			updateSummaryLine();

			$createBtn.trigger( 'focus' );
			if ( $createBtn[ 0 ] && typeof $createBtn[ 0 ].focus === 'function' ) {
				$createBtn[ 0 ].focus();
			}
		} );

		// Follow progress scroll listener: pauses following if user scrolls manually.
		$( window ).on( 'scroll.lgxBulkCreating', function() {
			if ( ! isProgrammaticScroll && isRunActive && followProgress ) {
				followProgress = false;
				$followBtn.removeAttr( 'hidden' );
			}
		} );

		// Follow progress button: resumes automatic following when clicked.
		$followBtn.on( 'click', function( event ) {
			event.preventDefault();
			followProgress = true;
			$followBtn.attr( 'hidden', 'hidden' );
			scrollToActiveRow();
		} );
	} );
})( jQuery );
