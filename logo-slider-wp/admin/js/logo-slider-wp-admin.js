if ( window.jQuery ) {
	jQuery( document ).ready( function ($) {
		'use strict';

		// Unified copy to clipboard handler
		function copyToClipboard(text, successMsg) {
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(text).then(function() {
					if (typeof alertify !== 'undefined' && successMsg) {
						alertify.success(successMsg);
					}
				}).catch(function(err) {
					fallbackCopyTextToClipboard(text, successMsg);
				});
			} else {
				fallbackCopyTextToClipboard(text, successMsg);
			}
		}

		function fallbackCopyTextToClipboard(text, successMsg) {
			var textArea = document.createElement("textarea");
			textArea.value = text;
			
			// Avoid scrolling to bottom
			textArea.style.top = "0";
			textArea.style.left = "0";
			textArea.style.position = "fixed";

			document.body.appendChild(textArea);
			textArea.focus();
			textArea.select();

			try {
				var successful = document.execCommand('copy');
				if (successful && typeof alertify !== 'undefined' && successMsg) {
					alertify.success(successMsg);
				}
			} catch (err) {
				console.error('Fallback: Oops, unable to copy', err);
			}
			document.body.removeChild(textArea);
		}

		$('.lgx_color_picker').each(function(){
			$(this).wpColorPicker();
		});




		function oldlgxupbtn(clickBtn) {
			console.log('outSide' );
			if ( lgxLogoShowcaseWpMedia === undefined ) {
				console.log('inSide' );
				lgxLogoShowcaseWpMedia = wp.media(
					{
						title: 'Choose Icon/Image',
						library: {
							type: [ 'icon', 'image' ]
						},
						multiple: false,
						button: {
							text: 'Choose'
						}
					}
				);

				lgxLogoShowcaseWpMedia.on( 'select', function () {
					let mediaFile = lgxLogoShowcaseWpMedia.state().get( 'selection' );

					/*console.log( mediaFile );
					console.log( mediaFile.toArray() );
					console.log( mediaFile.first() );*/

					mediaFile.toArray().forEach( function (item) {
						let iconFieldName = clickBtn.data( 'iconFieldName' );
						let iconImgId = clickBtn.data( 'iconImgId' );
						
						let itemInfo = item.attributes;
						//let itemUrl = ( itemInfo.sizes.hasOwnProperty('medium') ) ? itemInfo.sizes.medium.url : itemInfo.sizes.full.url;
						$( 'input[name="' + iconFieldName + '"]' ).val( itemInfo.sizes.full.url );
						$( '#' + iconImgId ).attr( 'src', itemInfo.sizes.full.url );
					} );



				} );

				lgxLogoShowcaseWpMedia.open(); //old_file
			}
		}
		

		

		function lgxLogoSliderImageUploadButton(clickBtn) {
			   //var LgxLogoUploadFrame;
			
				//console.log('Out 1')
				// If the media frame already exists, reopen it.
				//if ( LgxLogoUploadFrame ) {
					//LgxLogoUploadFrame.open();
					//return;
				  //}
				  //console.log('Out 2')

				// Create a new media frame
				var LgxLogoUploadFrame = wp.media({
					title: 'Select or Upload Media Of Your Chosen Persuasion',
					button: {
					text: 'Use this media'
					},
					multiple: false  // Set to true to allow multiple files to be selected
				});


				// When an image is selected in the media frame...
				LgxLogoUploadFrame.on( 'select', function() {
				
					// Get media attachment details from the frame state
					var attachment = LgxLogoUploadFrame	.state().get('selection').first().toJSON();

					let iconFieldName = clickBtn.data( 'iconFieldName' );
					let iconImgId = clickBtn.data( 'iconImgId' );


					$( 'input[name="' + iconFieldName + '"]' ).val( attachment.url);
					$( '#' + iconImgId ).attr( 'src', attachment.url );

					// Visual Consistency Pass: the modern Showcase Builder markup
					// wraps the image + its "x" remove badge in one preview box
					// (.lgx-upload-preview), a sibling of the Select button - show
					// that instead of the old standalone Remove button. Fall back
					// to the pre-rebuild behavior (Remove button as a direct
					// sibling, no wrapper) for the legacy table-layout markup this
					// same handler also serves (kept working, not currently
					// reachable in the live builder). Select and Remove/the
					// preview box are always siblings within the same field's
					// control wrapper, so a plain sibling lookup is enough (no
					// need to match the field name, which can contain "[" "]"
					// characters that are unsafe to interpolate into a CSS
					// attribute-selector string).
					let $previewWrap = clickBtn.siblings( '.lgx-upload-preview' );
					if ( $previewWrap.length ) {
						$previewWrap.show();
					} else {
						clickBtn.siblings( '.lgx_icon_image_button_clear' ).show();
					}
				});
			
				// Finally, open the modal on click
				LgxLogoUploadFrame.open();
			
		}



		function lgxNavigationTab(parentSelector) {
			let $lgx_logo_slider_nav_tab_wrapper = $( parentSelector ).find( '.lgx_logo_slider_nav_tab_wrapper' );
			$lgx_logo_slider_nav_tab_wrapper.each( function ( index, element ) {
				//console.log( index );
				//console.log( element );
				$( element ).find( '.lgx_logo_slider_nav_tab' ).on( 'click', function (event) {
					event.preventDefault();
					let $this = $( this );
					let dataValue = $this.data();


					//console.log( 'I am clicked' )

					//if the button already active just return
					if ( $this.hasClass( 'lgx_active' ) ) {
						return;
					}

					//Make active tabbed button
					$this.parent().find( '.lgx_logo_slider_nav_tab.lgx_active' ).removeClass( 'lgx_active' );
					$this.addClass( 'lgx_active' );

					$( element ).siblings( '.lgx_logo_slider_tab_content_wrapper' ).find( '.lgx_logo_slider_tab_content' ).removeClass( 'lgx_active' );
					$( element ).siblings( '.lgx_logo_slider_tab_content_wrapper' ).find( '[data-tab-id="' + dataValue.activeTab + '"]' ).addClass( 'lgx_active' );

					//lgx_logo_slider_tab_content
					//console.log( $this );
				} );
			} );//End Logo Slider Navigation Tab
		}



		let lgxLogoShowcaseWpMedia;
		let $lgx_logo_slider_copy_button = $( '.lgx_logo_slider_copy_button' );
		let $lgx_icon_image_button     = $( '.lgx_icon_image_button' );
		let $lgx_icon_image_button_clear = $( '.lgx_icon_image_button_clear' );
		let $lgx_logo_slider_list_copy_input = $( '.lgx_logo_slider_list_copy_input' );


		$lgx_logo_slider_list_copy_input.on( 'click', function (event) {

			event.preventDefault();
			let $this = $( this );

			$this.select();
			copyToClipboard($this.val(), '<i class="lgxicon lgx-icon-copy"></i> Copy to clipboard successfully');

		} );

		$lgx_logo_slider_copy_button.on( 'click', function (event) {
			event.preventDefault();
			let $this = $( this );

			let shortCodeString = $this.siblings( '.lgx_logo_slider_short_code' ).text();
			copyToClipboard(shortCodeString, '<i class="lgxicon lgx-icon-copy"></i> Copy to clipboard successfully');

			$this.html( '<i class="lgxicon lgx-icon-copy"></i> Copied' );

		} );


		$lgx_icon_image_button.on( 'click', function (event) {
			event.preventDefault();
			let $this = $( this );
			lgxLogoSliderImageUploadButton($this);
		} );//End Logo Slider Insert Image Button

		$lgx_icon_image_button_clear.on( 'click', function (event) {
			event.preventDefault();
			let $this = $( this );
			let iconFieldName = $this.data( 'iconFieldName' );
			let iconImgId = $this.data( 'iconImgId' );

			$( 'input[name="' + iconFieldName + '"]' ).val( '' );
			// removeAttr, not attr('src', '') - an empty src="" still resolves
			// against the current page URL and browsers will try to load the
			// admin page itself as an image (a real broken-image state, not a
			// blank one).
			$( '#' + iconImgId ).removeAttr( 'src' );

			// Visual Consistency Pass: this button now lives inside the shared
			// image + remove-badge preview box (.lgx-upload-preview) in the
			// modern Showcase Builder markup - hide the whole box, not just
			// the badge, or the now-src-less <img> would be left visible as an
			// empty frame. Legacy table-layout markup (this button as a plain
			// standalone sibling, no wrapper) keeps its original behavior.
			let $previewWrap = $this.closest( '.lgx-upload-preview' );
			if ( $previewWrap.length ) {
				$previewWrap.hide();
			} else {
				$this.hide();
			}
		} );



		lgxNavigationTab( '.lgx_logo_slider_info_box' );


		// Phase E: Task 3.3 - Item Sorting
		let $wpListTable = $( 'table.wp-list-table tbody' );
		let urlParams = new URLSearchParams(window.location.search);
		let paged = parseInt(urlParams.get('paged')) || 1;

		if ( paged === 1 ) {
			$wpListTable.sortable( {
				handle: '.lgx-drag-handle',
				placeholder: 'lgx-sortable-placeholder',
				start: function(event, ui) {
				},
				sort: function(event, ui) {
				},
				stop: function(event, ui) {
					$wpListTable.closest('table').addClass('lgx-reorder-saving');
					$.ajax( {
						type : 'post',
						dataType : 'json',
						url : wpnpaddon.ajax_url,
						data : {
							action: 'lgx_ls_admin_lswp_reorder',
							post_id_serialize : $( '#the-list' ).sortable( 'serialize' ),
							nonce: wpnpaddon.check_nonce
						},
						success: function(response) {
							$wpListTable.closest('table').removeClass('lgx-reorder-saving');
							if( response.type === 'success' ) {
								$wpListTable.addClass('lgx-reorder-success');
								setTimeout(function(){ $wpListTable.removeClass('lgx-reorder-success'); }, 1000);
								if ( typeof alertify !== 'undefined' ) {
									alertify.success( 'Logo order saved successfully.' );
								} else {
									alert( 'Logo order saved successfully.' );
								}
							}
							else {
								$wpListTable.addClass('lgx-reorder-error');
								setTimeout(function(){ $wpListTable.removeClass('lgx-reorder-error'); }, 1000);
								if ( typeof alertify !== 'undefined' ) {
									alertify.error( response.message );
								} else {
									alert( response.message );
								}
							}
						},
						error: function() {
							$wpListTable.closest('table').removeClass('lgx-reorder-saving');
							$wpListTable.addClass('lgx-reorder-error');
							setTimeout(function(){ $wpListTable.removeClass('lgx-reorder-error'); }, 1000);
						}
					} );
				}
			} );
		}

		$( '.lgx_logo_slider_showcase_type' ).on( 'change', function (event) {
			event.preventDefault();
			let $this = $( this );
			let logoSliderLayout = $this.find( 'option:selected' ).data( 'logo-slider-layout' );
			let $lgx_logo_slider_tab_inner = $( '.lgx_logo_slider_tab_inner' );
			let $lgx_logo_slider_layout = $( '.lgx_logo_slider_layout' );
			$lgx_logo_slider_tab_inner.hide();
			$( 'div.lgx_logo_slider_tab_inner_layout_' + logoSliderLayout ).fadeIn(1000);

			switch (logoSliderLayout) {
				case 'carousel':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-images-alt2"></i> Carousel' );
					break;

				case 'grid':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-grid-view"></i> Grid' );
					break;

				case 'flexbox':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-screenoptions"></i> Flexbox' );
					break;
	
				case 'masonry':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-tagcloud"></i> Masonry' );
					break;

				case 'vertical':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-filter"></i> Vertical' );
					break;

				case 'filter':
					$lgx_logo_slider_layout.html( '<i class="dashicons dashicons-filter"></i> Filter' );
					break;

				default:
					break
			}
		} );

		// Phase E: Logo Editor UI Modernization (Advanced Section & Title Relocation)
		if ( $('body').hasClass('post-type-logosliderwp') ) {
			var $titleInput = $('#title');

			// --- Title Inheritance (Option C: Smart Values) ---
			var $tooltipInput = $('#lgx_tooltip_text');
			var $altTextInput = $('#lgx_alt_text');
			var $customBrandInput = $('#lgx_custom_brand_name');

			// Determine if fields are empty on load (meaning they have no custom DB value)
			var isUsingTitleDefault_tooltip = $tooltipInput.length && $tooltipInput.val().trim() === '';
			var isUsingTitleDefault_altText = $altTextInput.length && $altTextInput.val().trim() === '';

			function updateSmartCue() {
				if ( $tooltipInput.length && $tooltipInput.attr('data-pro-locked-input') !== 'true' ) {
					$tooltipInput.toggleClass( 'lgx-field__control--auto lgx-field__control--synced', Boolean( isUsingTitleDefault_tooltip ) );
				}
				if ( $altTextInput.length && $altTextInput.attr('data-pro-locked-input') !== 'true' ) {
					$altTextInput.toggleClass( 'lgx-field__control--auto lgx-field__control--synced', Boolean( isUsingTitleDefault_altText ) );
				}
			}

			function updateSmartValues() {
				var titleVal = $titleInput.val().trim();
				
				if ( isUsingTitleDefault_tooltip && $tooltipInput.length ) {
					$tooltipInput.val(titleVal);
				}
				if ( isUsingTitleDefault_altText && $altTextInput.length ) {
					// Alt Text prefills with the effective Brand Name (Custom Brand Name
					// when set, otherwise the Title) - same order the frontend renderer uses.
					var customBrandVal = $customBrandInput.length ? $customBrandInput.val().trim() : '';
					$altTextInput.val(customBrandVal !== '' ? customBrandVal : titleVal);
				}
				updateSmartCue();
			}

			if ( $titleInput.length ) {
				// If user manually edits the fields, they are no longer auto-generated.
				if ( $tooltipInput.length ) {
					$tooltipInput.on('input', function() {
						isUsingTitleDefault_tooltip = false;
						updateSmartCue();
					});
				}
				if ( $altTextInput.length ) {
					$altTextInput.on('input', function() {
						isUsingTitleDefault_altText = false;
						updateSmartCue();
					});
				}
				
				// Sync title changes to auto fields in real-time
				$titleInput.on('input', updateSmartValues);
				if ( $customBrandInput.length ) {
					$customBrandInput.on('input', updateSmartValues);
				}
				
				// Run on load to populate any empty fields
				updateSmartValues();
				
				// CRITICAL ARCHITECTURE RULE: 
				// Clear auto-populated fields right before form submission so they don't save to the DB!
				// This preserves ADR-001 (no data duplication) while giving the user Option C UX.
				// Locked Tooltip and Alt Text are always cleared on submit.
				$('#post').on('submit', function() {
					if ( $tooltipInput.length && ( isUsingTitleDefault_tooltip || $tooltipInput.attr('data-pro-locked-input') === 'true' ) ) {
						$tooltipInput.val('');
					}
					if ( $altTextInput.length && ( isUsingTitleDefault_altText || $altTextInput.attr('data-pro-locked-input') === 'true' ) ) {
						$altTextInput.val('');
					}
				});
			}

			// --- Pro Locked Fields & Upgrade Dialog (ADR-023, T-338) ---
			var $proModal = $('#lgx-logo-pro-modal');
			var $proModalOverlay = $('#lgx-logo-pro-modal-overlay');
			var $proModalTitleText = $('#lgx-logo-pro-modal-title-text');
			var $proModalText = $('#lgx-logo-pro-modal-text');
			var $proModalClose = $('#lgx-logo-pro-modal-close');
			var $proModalUpgrade = $('#lgx-logo-pro-modal-upgrade');

			if ( $proModal.length ) {
				var lastFocusedElement = null;

				var defaultStrings = {
					tooltip_text: {
						title: 'Custom Tooltip is a Pro Feature',
						text: 'Your logo already uses the Brand Name as its tooltip. With Pro, you can write your own text for each logo.'
					},
					alt_text: {
						title: 'Custom Alt Text is a Pro Feature',
						text: 'Your logo already uses the Brand Name as its alt text. With Pro, you can write your own alt text for each logo.'
					},
					company_name: {
						title: 'Custom Brand Name is a Pro Feature',
						text: 'Your logo already shows its Brand Name. With Pro, you can show a different name without renaming the logo.'
					}
				};

				function openProModal( fieldKey, triggerElement ) {
					lastFocusedElement = triggerElement;

					var strings = defaultStrings[fieldKey] || {};
					if ( typeof wpnpaddon !== 'undefined' && wpnpaddon.pro_field_strings && wpnpaddon.pro_field_strings[fieldKey] ) {
						strings = wpnpaddon.pro_field_strings[fieldKey];
					}

					$proModalTitleText.text( strings.title || '' );
					$proModalText.text( strings.text || '' );

					$proModalOverlay.show().attr('aria-hidden', 'false');
					$proModal.show().attr('aria-hidden', 'false');

					// Move focus into the dialog on open
					if ( $proModalUpgrade.length ) {
						$proModalUpgrade.trigger('focus');
					}
				}

				function closeProModal() {
					$proModal.hide().attr('aria-hidden', 'true');
					$proModalOverlay.hide().attr('aria-hidden', 'true');

					if ( lastFocusedElement && typeof lastFocusedElement.focus === 'function' ) {
						lastFocusedElement.focus();
					}
					lastFocusedElement = null;
				}

				// Close on 'Maybe later' button
				$proModalClose.on('click', function( e ) {
					e.preventDefault();
					closeProModal();
				});

				// Close on backdrop click
				$proModalOverlay.on('click', function( e ) {
					e.preventDefault();
					closeProModal();
				});

				// Close on Escape & trap Tab key
				$(document).on('keydown', function( e ) {
					if ( $proModal.css('display') === 'none' ) {
						return;
					}

					if ( e.key === 'Escape' || e.keyCode === 27 ) {
						e.preventDefault();
						closeProModal();
						return;
					}

					if ( e.key === 'Tab' || e.keyCode === 9 ) {
						var focusable = $proModal.find('a[href], button:not([disabled])').filter(function() {
							return $(this).css('display') !== 'none';
						});
						if ( ! focusable.length ) {
							return;
						}
						var first = focusable.first()[0];
						var last = focusable.last()[0];

						if ( e.shiftKey ) {
							if ( document.activeElement === first ) {
								e.preventDefault();
								last.focus();
							}
						} else {
							if ( document.activeElement === last ) {
								e.preventDefault();
								first.focus();
							}
						}
					}
				});

				// Wire up locked fields
				var lockedFieldConfigs = [
					{ selector: '#lgx_tooltip_text', key: 'tooltip_text' },
					{ selector: '#lgx_alt_text', key: 'alt_text' },
					{ selector: '#lgx_custom_brand_name', key: 'company_name' }
				];

				$.each( lockedFieldConfigs, function( idx, cfg ) {
					var $input = $( cfg.selector );
					if ( ! $input.length || $input.attr('data-pro-locked-input') !== 'true' ) {
						return;
					}

					var $wrapper = $input.closest('.lgx-pro-locked-field');
					var $indicator = $wrapper.find('.lgx-pro-locked-indicator');

					// Click on input opens dialog
					$input.on('click', function( e ) {
						e.preventDefault();
						openProModal( cfg.key, this );
					});

					// Mousedown on input opens dialog
					$input.on('mousedown', function( e ) {
						e.preventDefault();
						openProModal( cfg.key, this );
					});

					// Enter or Space keydown opens dialog
					$input.on('keydown', function( e ) {
						if ( e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32 ) {
							e.preventDefault();
							openProModal( cfg.key, this );
						}
					});

					if ( $indicator.length ) {
						$indicator.on('click', function( e ) {
							e.preventDefault();
							openProModal( cfg.key, $input[0] );
						});
					}
				});
			}

			// T-339: Accessible "i" info disclosure notes for Additional Details
			var $logoMetaWrapper = $( '.lgx_meta_boxes_fields_logo_item' );
			if ( $logoMetaWrapper.length ) {
				function toggleFieldInfoNote( btn ) {
					var $btn = $( btn );
					var noteId = $btn.attr( 'aria-controls' );
					if ( ! noteId ) {
						return;
					}
					var $note = $( '#' + noteId );
					if ( ! $note.length ) {
						return;
					}
					var isOpen = $btn.attr( 'aria-expanded' ) === 'true';
					$btn.attr( 'aria-expanded', isOpen ? 'false' : 'true' );
					if ( isOpen ) {
						$note.attr( 'hidden', '' ).prop( 'hidden', true );
					} else {
						$note.removeAttr( 'hidden' ).prop( 'hidden', false );
					}
				}

				$logoMetaWrapper.on( 'click', '.lgx-field__info', function() {
					toggleFieldInfoNote( this );
				} );

				$logoMetaWrapper.on( 'keydown', function( e ) {
					if ( e.key === 'Escape' || e.keyCode === 27 ) {
						var $row = $( e.target ).closest( '.lgx-field-row' );
						if ( ! $row.length ) {
							return;
						}
						var $btn = $row.find( '.lgx-field__info' );
						if ( $btn.length && $btn.attr( 'aria-expanded' ) === 'true' ) {
							e.preventDefault();
							var noteId = $btn.attr( 'aria-controls' );
							$btn.attr( 'aria-expanded', 'false' );
							if ( noteId ) {
								$( '#' + noteId ).attr( 'hidden', '' ).prop( 'hidden', true );
							}
							$btn.trigger( 'focus' );
						}
					}
				} );
			}


			// Phase E: Task 2.1 - Save & Add Another Workflow
			var $majorPublishingActions = $('#major-publishing-actions');
			var $publishBtn = $('#publish');
			
			if ( $majorPublishingActions.length && $publishBtn.length ) {
				// Use the robust translated string generated by PHP based on post status
				var customBtnText = (typeof wpnpaddon !== 'undefined' && wpnpaddon.save_and_add_another_text) 
					? wpnpaddon.save_and_add_another_text 
					: 'Save & Add Another';

				// Inject the secondary submit button as a new row ABOVE the native actions to preserve native alignment
				var addAnotherBtnHtml = '<div id="lgx_save_and_add_wrapper" style="width: 100%; text-align: right; padding-bottom: 10px; clear: both;">' +
					'<button type="button" id="lgx_save_and_add_another" class="button button-secondary button-large">' + customBtnText + '</button>' +
					'</div>';
				
				$majorPublishingActions.prepend(addAnotherBtnHtml);

				$('#lgx_save_and_add_another').on('click', function(e) {
					e.preventDefault();
					// Inject a hidden flag to signal the redirect hook, then trigger the native publish
					$('<input>').attr({
						type: 'hidden',
						name: 'lgx_save_and_add_another_flag',
						value: '1'
					}).appendTo('form#post');
					$publishBtn.click();
				});
			}
			
			// Initial Focus if redirected from Save & Add Another
			if ( window.location.search.indexOf('lgx_saved=1') > -1 ) {
				if ( $titleInput.length ) {
					// Small timeout ensures WordPress has fully initialized the DOM
					setTimeout(function() {
						$titleInput.focus();
					}, 100);
				}
			}
		}

		// Phase F: Move Up / Down Reorder
		$(document).on('click', '.lgx-move-row-btn', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var post_id = $btn.data('post');
			var direction = $btn.data('action');
			var nonce = $btn.data('nonce');
			
			$wpListTable.closest('table').addClass('lgx-reorder-saving');
			
			$.ajax({
				url: wpnpaddon.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'lgx_ls_move_row',
					post_id: post_id,
					direction: direction,
					nonce: nonce
				},
				success: function(response) {
					$wpListTable.closest('table').removeClass('lgx-reorder-saving');
					if (response.success) {
						$wpListTable.addClass('lgx-reorder-success');
						setTimeout(function(){ window.location.reload(); }, 500);
					} else {
						$wpListTable.addClass('lgx-reorder-error');
						setTimeout(function(){ $wpListTable.removeClass('lgx-reorder-error'); }, 1000);
						if ( typeof alertify !== 'undefined' ) {
							alertify.error(response.data.message);
						} else {
							alert(response.data.message);
						}
					}
				},
				error: function() {
					$wpListTable.closest('table').removeClass('lgx-reorder-saving');
					$wpListTable.addClass('lgx-reorder-error');
					setTimeout(function(){ $wpListTable.removeClass('lgx-reorder-error'); }, 1000);
				}
			});
		});

		// Phase F: Bulk Assign Category Select UI
		$('select[name="action"], select[name="action2"]').on('change', function() {
			var $select = $(this);
			var action = $select.val();
			var $wrapper = $select.parent();
			
			$wrapper.find('.lgx-bulk-cat-select').remove();
			
			if (action === 'lgx_bulk_assign_category') {
				var $catFilter = $('select[name="logosliderwpcat"]').first();
				if ($catFilter.length) {
					var $clone = $catFilter.clone();
					$clone.attr('name', 'lgx_bulk_cat').addClass('lgx-bulk-cat-select').removeClass('postform');
					$clone.find('option[value=""]').text('— Select Category —');
					$select.after($clone);
					$clone.css({
						'margin-left': '5px',
						'vertical-align': 'middle'
					});
				}
			}
		});

		// Item Sorting End

		// Phase E: Task 3.1B - Quick Edit Enhancements
		// Intercept the native inlineEditPost.edit function to populate our custom fields
		if ( typeof inlineEditPost !== 'undefined' ) {
			var wp_inline_edit = inlineEditPost.edit;
			inlineEditPost.edit = function( id ) {
				// Call the original WP function
				wp_inline_edit.apply( this, arguments );

				// Get the post ID
				var post_id = 0;
				if ( typeof( id ) === 'object' ) {
					post_id = parseInt( this.getId( id ) );
				}

				if ( post_id > 0 ) {
					// Get the edit row and the data from the hidden divs
					var $edit_row = $( '#edit-' + post_id );
					var $brand_inline = $( '#lgx_ls_brand_inline_' + post_id );
					
					var brand_name = $brand_inline.text();
					var brand_url  = $( '#lgx_ls_url_inline_' + post_id ).text();
					var is_locked  = $brand_inline.attr( 'data-pro-locked' ) === 'true';

					var $company_input = $edit_row.find( '.lgx-quick-edit-company-name' );
					var $company_group = $edit_row.find( '.lgx-quick-edit-company-group' );
					var $lock_icon     = $edit_row.find( '.lgx-quick-edit-lock-icon' );
					var $pro_note      = $edit_row.find( '.lgx-quick-edit-pro-note' );

					// Always hide the inline Pro note when opening a Quick Edit row
					$pro_note.hide();

					if ( is_locked ) {
						var locked_ph = $company_input.attr( 'data-locked-placeholder' ) || 'Uses the Brand Name';
						$company_input
							.val( '' )
							.attr( 'readonly', 'readonly' )
							.attr( 'data-pro-locked-input', 'true' )
							.attr( 'placeholder', locked_ph )
							.attr( 'aria-describedby', 'lgx_qe_company_name_pro_desc' );
						$company_group.addClass( 'lgx-pro-locked-field' );
						$lock_icon.show();
					} else {
						var unlocked_ph = $company_input.attr( 'data-unlocked-placeholder' ) || '';
						$company_input
							.val( brand_name )
							.removeAttr( 'readonly' )
							.removeAttr( 'data-pro-locked-input' )
							.attr( 'placeholder', unlocked_ph )
							.removeAttr( 'aria-describedby' );
						$company_group.removeClass( 'lgx-pro-locked-field' );
						$lock_icon.hide();
					}

					// Populate Website URL (always Free)
					$edit_row.find( '.lgx-quick-edit-company-url' ).val( brand_url );
				}
			};

			// Show inline Pro note when clicking or pressing Enter/Space on locked Quick Edit input or lock icon
			$( document ).on( 'click', '.lgx-quick-edit-company-name[data-pro-locked-input="true"], .lgx-quick-edit-lock-icon', function( e ) {
				e.preventDefault();
				$( this ).closest( '.lgx-quick-edit-company-group' ).find( '.lgx-quick-edit-pro-note' ).show();
			} );

			$( document ).on( 'keydown', '.lgx-quick-edit-company-name[data-pro-locked-input="true"]', function( e ) {
				if ( e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32 ) {
					e.preventDefault();
					$( this ).closest( '.lgx-quick-edit-company-group' ).find( '.lgx-quick-edit-pro-note' ).show();
				}
			} );
		}

		// Phase E: Task 4.1 - Shortcode Copy UX
		$(document).on('click', '.lgx-copy-btn', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var textToCopy = $btn.attr('data-clipboard-text');

			if (!textToCopy) {
				// Try to find a sibling input if data attribute is missing
				var $input = $btn.siblings('.lgx-copy-input');
				if ($input.length > 0) {
					textToCopy = $input.val();
				}
			}

			if (!textToCopy) {
				return;
			}

			// Try Clipboard API
			copyToClipboard(textToCopy, 'Copied to clipboard!');
		});



	} );// Init jQuery
} else {
	console.log( 'jQuery not loaded.' )
}
