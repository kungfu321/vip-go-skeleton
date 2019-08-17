;(function( $ ) {

	$( document ).ready(function() {

		if ( 'undefined' !== typeof $.fn.buttonset ) {
			$( '.buttonset' ).buttonset();
		}

		$( '.buttonset-tabs' ).each(function() {
			var $this = $( this ),
				target = '#' + $this.find( 'input[type=radio]:checked' ).attr( 'id' ) + '_tab';

			$this.closest( '.bg-opt-wrapper' ).find( target ).addClass( 'active-tab' );
		});

		$( '.buttonset-tab' ).on( 'click', function() {
			var $this = $( this ),
				target = '#' + $this.prev().attr( 'id' ) + '_tab';

			$this.closest( '.bg-opt-wrapper' ).find( '.active-tab' ).removeClass( 'active-tab' );
			$this.closest( '.bg-opt-wrapper' ).find( target ).addClass( 'active-tab' );
		});

	});

})( jQuery );
