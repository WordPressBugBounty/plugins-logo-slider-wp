/**
 * Bulk Upload module script.
 *
 * @package Logo_Slider_WP
 */
(function( $ ) {
	'use strict';

	$( function() {
		if ( typeof window.lgxBulkUpload === 'undefined' || ! window.lgxBulkUpload.page_url || ! window.lgxBulkUpload.button_label ) {
			return;
		}

		var $actions = $( '.wrap .page-title-action' );
		if ( ! $actions.length || $( '.lgx-bulk-upload__title-action' ).length ) {
			return;
		}

		var $addNewButton = $actions.filter( function() {
			var href = $( this ).attr( 'href' ) || '';
			return $( this ).is( 'a' ) && href.indexOf( 'post-new.php' ) !== -1 && href.indexOf( 'post_type=logosliderwp' ) !== -1;
		} ).first();

		if ( $addNewButton.length ) {
			$addNewButton.addClass( 'lgx-bulk-upload__action lgx-bulk-upload__action--primary' );
		}

		var $insertAfter = $addNewButton.length ? $addNewButton : $actions.first();

		var $bulkButton = $( '<a></a>' )
			.attr( 'href', window.lgxBulkUpload.page_url )
			.addClass( 'page-title-action lgx-bulk-upload__title-action lgx-bulk-upload__action lgx-bulk-upload__action--secondary' )
			.text( window.lgxBulkUpload.button_label );

		$insertAfter.after( $bulkButton );
	} );
})( jQuery );

