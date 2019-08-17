;(function( $ ) {

	$( document ).ready(function() {

		$( '.mts-opts-sorter' ).each(function() {

			var id = $( this ).attr( 'id' );
			$( this ).find( 'ul' ).sortable({
				items: 'li',
				placeholder: 'placeholder',
				connectWith: '.sortlist_' + id,
				opacity: 0.6,
				update: function() {

					$( this ).find( '.position' ).each(function() {

						var $this = $( this ),
							listID = $this.parent().attr( 'id' ),
							parentID = $this.parent().parent().attr( 'id' );

						parentID = parentID.replace( id + '_', '' );

						var optionID = $this.parent().parent().parent().attr( 'id' ),
							fieldName = $this.attr( 'name' ).replace( /\[.*\]/, '' );

						$this.prop( 'name', fieldName + '[' + optionID + '][' + parentID + '][' + listID + ']' );
					});
				}
			});
		});
	});

})( jQuery );
