;( function( $ ) {

	$( document ).ready(function( $ ) {

		$( '.mts-radio-img' ).on( 'click', 'label', function() {

			var $this = $( this );

			$this.siblings().removeClass( 'mts-radio-img-selected' );
			$this.addClass( 'mts-radio-img-selected' );
		});
	});

})( jQuery );
