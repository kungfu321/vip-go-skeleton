/*global jQuery, document, ace*/

;(function( $ ) {
    'use strict';

	if ( ! ( 'ace' in window ) ) {
		return;
	}

	var selector = $( document ).find( '.ace-wrapper' );

    $( selector ).each(
        function() {

    			var el = $( this );
          el.find( '.ace-editor' ).each(
              function( index, element ) {
                  var area = element;
                  var params = JSON.parse( $( this ).parent().find( '.localize_data' ).val() );
                  var editor = $( element ).attr( 'data-editor' );

                  var aceeditor = ace.edit( editor );
                  aceeditor.setTheme( 'ace/theme/' + jQuery( element ).attr( 'data-theme' ) );
                  aceeditor.getSession().setMode( 'ace/mode/' + $( element ).attr( 'data-mode' ) );
                  aceeditor.setOptions( params );
                  aceeditor.setShowPrintMargin( false );
                  aceeditor.on(
                      'change', function() {
                          $( '#' + area.id ).val( aceeditor.getSession().getValue() );
                          aceeditor.resize();
                      }
                  );
              }
          );
        }
    );

})( jQuery );
