/**
 * cyprus Admin
 */
;(function( $ ) {

	'use strict';

	// Dom Ready
	$(function() {

		$( 'a.debug-report' ).click( function() {

			var report = '';

			$( 'thead, tbody', '.cyprus-system-status table:not(.not-me-plz)' ).each(function() {

				if ( $( this ).is( 'thead' ) ) {

					var label = $( this ).find( 'th:eq(0)' ).data( 'export-label' ) || $( this ).text();
					report = report + "\n### " + $.trim( label ) + " ###\n\n";

				} else {

					$( 'tr', $( this ) ).each(function() {

						var label = $( this ).find( 'td:eq(0)' ).data( 'export-label' ) || $( this ).find( 'td:eq(0)' ).text();
						var theName = $.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML
						var theValueElement = $( this ).find( 'td:eq(2)' );
						var theValue;
						if ( $( theValueElement ).find( 'img' ).length >= 1 ) {
							theValue = $.trim( $( theValueElement ).find( 'img' ).attr( 'alt' ) );
						} else {
							theValue = $.trim( $( this ).find( 'td:eq(2)' ).text() );
						}
						var valueArray = theValue.split( ', ' );

						if ( valueArray.length > 1 ) {

							// If value have a list of plugins ','
							// Split to add new line
							var tempLine = '';
							$.each( valueArray, function( key, line ) {
								tempLine = tempLine + line + '\n';
							});

							theValue = tempLine;
						}

						report = report + '' + theName + ': ' + theValue + "\n";
					});

				}
			});

			try {
				$( '#debug-report' ).slideDown();
				$( '#debug-report-textarea' ).val( report ).focus().select();
				$( this ).parent().fadeOut();
				return false;
			} catch ( e ) {
				console.log( e );
			}

			return false;
		});

		new ClipboardJS( '#copy-for-support' );
	});

})( jQuery );
