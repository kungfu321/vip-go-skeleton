/* global mtsOptions */
;(function( $ ) {

	$( document ).ready(function() {

		if ( 'undefined' === typeof wp.media ) {
			return
		}

		var fileFrame, imageData, relid, $context;

		fileFrame = wp.media.frames.fileFrame = wp.media({
			title: mtsOptions.uploadTitle,
			button: { text: mtsOptions.uploadButtonText },
			multiple: false
		});

		fileFrame.on( 'select', function() {

			var $field = $( '#' + relid, $context ),
				$img = $field.next();

			imageData = fileFrame.state().get( 'selection' ).first().toJSON();
			$field.val( 'id' === $img.data( 'return' ) ? imageData.id : imageData.url ).trigger( 'change' );
			$img.attr( 'src', imageData.url );
			$field.next().next().hide();
			$field.next().next().next().show();
		});

		$( 'img[src=""]' ).attr( 'src', mtsOptions.uploadUrl );

		$( '#mts-opts-form-wrapper' ).on( 'click', '.mts-opts-upload', function( event ) {
			event.preventDefault();

			var $this = $( this );

			$context = $this.parent();
			relid = $this.attr( 'rel-id' );
			fileFrame.open();
		});

		$( '#mts-opts-form-wrapper' ).on( 'click', '.mts-opts-upload-remove', function() {
			var $this = $( this ),
				relid = $this.attr( 'rel-id' );

			$( '#mts-opts-screenshot-' + relid ).attr( 'src', mtsOptions.uploadUrl ) ;
			$( '#' + relid ).val( '' );
			$( '.mts-opts-upload[rel-id=' + relid + ']' ).show();
			$this.hide();
			$this.prev().prev().attr( 'src', mtsOptions.uploadUrl );
		});

	});

})( jQuery );
