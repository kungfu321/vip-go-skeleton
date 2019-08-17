;(function( $ ) {

	$( document ).ready(function() {

		// Select
		$( '.description-wide select' ).select2({
			minimumResultsForSearch: 10,
			templateResult: function( item ) {
	            if ( ! item.id ) {
					return item.text;
				}

	            return '<i class="fa fa-' + item.id + '"></i>&nbsp;&nbsp;' + item.text;
	        },
	        templateSelection: function( item ) {
	            if ( ! item.id ) {
					return item.text;
				}

	            return '<i class="fa fa-' + item.id + '"></i>&nbsp;&nbsp;' + item.text;
	        },
	        escapeMarkup: function( m ) {
				return m;
			}
		});

	});

})( jQuery );
