;(function( $ ) {

	$( document ).ready(function() {

		var $nots = $( '.mts-opts-iconselect', '.mts-opts-dummy ' );
		$( '.mts-opts-iconselect' ).not( $nots ).select2({
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
