;(function( $ ) {

	$( document ).ready(function() {

		$( '.mts-opts-sorter-alt' ).each(function() {

	        var id = $( this ).attr( 'id' );

			$( this ).find( '.sortlist-alt' ).sortable({
	            items: '> .sortee-alt',
	            handle: '.sortee-header',
	            placeholder: 'placeholder',
	            connectWith: '.sortlist_' + id,
	            opacity: 0.6,
	            update: function() {

					$( this ).find( '.position-alt' ).each(function() {

						var $this = $( this ),
							listID = $this.parent().attr( 'id' ),
							parentID = $this.parent().parent().attr( 'id' );

						parentID = parentID.replace( id + '_', '' );

						var optionID = $this.parent().parent().parent().attr( 'id' ),
							fieldName = $this.attr( 'name' ).replace( /\[.*\]/, '' );

						$this.prop( 'name', fieldName + '[' + optionID + '][' + parentID + '][' + listID + ']' );
					});
	            },
	            start: function( event, ui ) {

	                if ( 'block' === ui.item.find( '.sortee-content' ).css( 'display' ) ) {
	                    ui.item.css( 'height', '40px' );
	                }
	                $( this ).sortable( 'refreshPositions' );
	            }
	        });
	    });

		var header = $( '.sortee-has-content .sortee-header' );
	    header.prepend( '<span class="ui-icon ui-icon-plus sortee-toggle"></span>' );

	    header.on( 'click', function() {
			var $this = $( this );
	        $this.next( '.sortee-content' ).slideToggle();
	        $this.find( '.sortee-toggle' ).toggleClass( 'ui-icon-minus ui-icon-plus' );
	    });

	    $( '.mts-opts-sortee-toggle-close' ).on( 'click', function() {
	        $( this ).closest( '.sortee-alt' ).find( '.sortee-header' ).trigger( 'click' );
	    });

	});

})( jQuery );
