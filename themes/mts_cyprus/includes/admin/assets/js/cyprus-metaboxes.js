/**
 * cyprus Metaboxes
 */
;(function( $ ) {

	'use strict';

	// Dom Ready
	$(function() {

		// Metabox tabs
		$( 'a', '.cyprus_metabox_tabs' ).on( 'click', function() {

			var $this = $( this ),
				target = $( '#cyprus_tab_' + $this.attr( 'href' ) );

			$( '.cyprus_metabox_tab.active' ).removeClass( 'active' );

			$this.parent().siblings().removeClass( 'active' );
			$this.parent().addClass( 'active' );
			target.addClass( 'active' );
			target.fadeIn( 800 );

			return false;
		});

		// Select
		$( '.cyprus_field select' ).select2({
			minimumResultsForSearch: 10,
			dropdownCssClass: 'cyprus-select2'
		});

		// Upload file
		var uploads = $( '.cyprus_upload_button' ),
			frame;
		if ( uploads.length ) {
			uploads.on( 'click', function( event ) {
				event.preventDefault();

				var $this = $( this );

				// If the media frame already exists, reopen it.
				if ( frame ) {
					frame.open();
					return;
				}

				// Create the media frame.
				frame = wp.media({
					multiple: false
				});

				// When an image is selected, run a callback.
				frame.on( 'select', function() {

					// Grab the selected attachment.
					var attachment = frame.state().get( 'selection' ).first();
					frame.close();

					$this.prev().val( attachment.attributes.url );
				});

				// Finally, open the modal.
				frame.open();
			});
		}

		// Buttonset
		$( '.cyprus_field.cyprus-buttonset a' ).on( 'click', function( event ) {
			event.preventDefault();

			var $radiosetcontainer = $( this ).closest( '.cyprus-buttonset' );
			$radiosetcontainer.find( '.buttonset-state-active' ).removeClass( 'buttonset-state-active' );

			$( this ).addClass( 'buttonset-state-active' );
			$radiosetcontainer.find( '.button-set-value' ).val( $radiosetcontainer.find( '.buttonset-state-active' ).data( 'value' ) ).trigger( 'change' );
		});

		// Dependency
		function cyprusCheckDependency( $currentValue, $desiredValue, $comparison ) {
			var $passed = false;
			if ( '==' === $comparison ) {
				if ( $currentValue == $desiredValue ) {
					$passed = true;
				}
			}
			if ( '=' === $comparison ) {
				if ( $currentValue = $desiredValue ) {
					$passed = true;
				}
			}
			if ( '>=' === $comparison ) {
				if ( $currentValue >= $desiredValue ) {
					$passed = true;
				}
			}
			if ( '<=' === $comparison ) {
				if ( $currentValue <= $desiredValue ) {
					$passed = true;
				}
			}
			if ( '>' === $comparison ) {
				if ( $currentValue > $desiredValue ) {
					$passed = true;
				}
			}
			if ( '<' === $comparison ) {
				if ( $currentValue < $desiredValue ) {
					$passed = true;
				}
			}
			if ( '!=' === $comparison ) {
				if ( $currentValue != $desiredValue ) {
					$passed = true;
				}
			}

			return $passed;
		}

		function cyprusLoopDependencies( $container ) {
			var $passed = false;
			$container.find( 'span' ).each( function() {

				var $value = $( this ).data( 'value' ),
					$comparison = $( this ).data( 'comparison' ),
					$field = $( this ).data( 'field' );

				$passed = cyprusCheckDependency( $( '#cyprus_' + $field ).val(), $value, $comparison );
				return $passed;
			});

			if ( $passed ) {
				 $container.parents( '.cyprus_metabox_field' ).fadeIn( 300 );
			} else {
				 $container.parents( '.cyprus_metabox_field' ).hide();
			}
		}

		$( '.cyprus-dependency' ).each( function() {
			cyprusLoopDependencies( $( this ) );
		});

		$( '[id*="cyprus"]' ).on( 'change', function() {
			var $id = $( this ).attr( 'id' ),
				$field = $id.replace( 'cyprus_', '' );
			$( 'span[data-field="' + $field + '"]' ).each( function() {
				cyprusLoopDependencies( $( this ).parents( '.cyprus-dependency' ) );
			});
		});
	});

})( jQuery );
