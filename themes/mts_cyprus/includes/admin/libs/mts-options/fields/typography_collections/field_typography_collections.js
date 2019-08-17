/* global initTypography */
;(function( $ ) {

	$( document ).ready(function() {

		var collection = $( '.typography-collections' );

		// Add New
		collection.on( 'click', '.typography-new-collection', function( event ) {
			event.preventDefault();

			var clone = collection.find( '.collection-template' ).clone();
			var count = parseInt( collection.data( 'count' ) ) + 1;

			// Update count
			collection.data( 'count', count );

			clone.find( 'input, select, textarea' ).each(function() {
				var $this = $( this ),
					oldName = $this.attr( 'name' );

				if ( undefined !== oldName ) {
					$this.attr( 'name', oldName.replace( '@', count ) );
				}
			});
			collection.append( clone );
			clone.removeClass( 'collection-template' );
			initTypography( clone );
		});

		// Remove this
		collection.on( 'click', '.typography-remove-collection', function( event ) {
			event.preventDefault();

			$( this ).closest( '.typography-controls' ).remove();
		});

	});

})( jQuery );
