/**
 * cyprus Admin
 */

'use strict'
;( function( $ ) {

	// Dom Ready
	$( function() {

		var contents = $( '.demo-importer-content-list' ),
			progressArea = null,
			importWrapper = $( '#mts-opts-import-code-wrapper' ),
			exportWrapper = $( '#mts-opts-export-code' )

		var addImportLog = function( msg, elem ) {
			var currentdate = new Date()
			var text = ( elem.val() ) +
				'[' +
				( 10 > currentdate.getHours() ? '0' : '' ) +
				currentdate.getHours() + ':' +
				( 10 > currentdate.getMinutes() ? '0' : '' ) +
				currentdate.getMinutes() + ':' +
				( 10 > currentdate.getSeconds() ? '0' : '' ) +
				currentdate.getSeconds() +
				'] ' +
				msg + '\n'

			elem.text( text ).scrollTop( elem[0].scrollHeight - elem.height() - 20 )
		}

		var doImportAjax = function( slug, actions, logger, paged, callback ) {
			if ( 0 === actions.length ) {
				addImportLog( 'Import finished.', logger )
				callback()
				return
			}

			var action = actions.shift()
			paged = paged || 1

			addImportLog( 'Importing ' + action, logger )
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'cyprus_import_demo',
					security: contents.data( 'security' ),
					perform: action,
					demoID: slug,
					paged: paged
				}
			}).done( function( result ) {

				var paged = 1
				if ( result && result.page && result.page < result.total_pages ) {
					paged = result.page + 1
					actions.unshift( action )
				}

				addImportLog( result.success ? result.message : result.error, logger )
				doImportAjax( slug, actions, logger, paged, callback )

			}).fail( function( result ) {
				addImportLog( result.statusText, logger )
				doImportAjax( slug, actions, logger, null, callback )
			})
		}

		$( '.demo-list--item' ).on( 'click', '.button-primary', function( event ) {
			event.preventDefault()

			var $item = $( this ).closest( '.demo-list--item' )
			$item.addClass( 'highlighted' ).siblings( '.demo-list--item' ).removeClass( 'highlighted' )
			contents.data( 'demoID', $( this ).data( 'demo-id' ) )
			var title = $item.find( 'h3' ).text()
			contents.find( 'h4 span' ).text( title )
			contents.show()
		})

		contents.on( 'click', '.button-primary', function( event ) {
			event.preventDefault()
			if ( ! confirm( $( this ).data( 'import-confirm' ) ) ) {
				return false
			}

			var button = $( this ),
				actions = $.map( button.closest( 'ul' ).find( 'input:checkbox:checked' ), function( input ) {
					return input.value
				})

			button.prop( 'disabled', true )
			if ( 1 > actions.length ) {
				alert( 'Select data to import.' )
				return
			}

			progressArea = $( '<textarea class="import-progress-area large-text" disabled="disabled" rows="8" style="margin: 20px 0;background: #eee;"></textarea>' )
			contents.append( progressArea )

			addImportLog( 'Import started...', progressArea )
			doImportAjax( contents.data( 'demoID' ), actions, progressArea, null, function() {
				button.prop( 'disabled', false )
			})
		})

		$( '#mts-opts-import-code-button' ).click( function( event ) {
			event.preventDefault()
			exportWrapper.hide()
			importWrapper.toggle().find( '#import-code-value' ).val( '' )
		})

		// Confirm import
		$( '#mts-opts-import' ).click( function() {
			if ( confirm( $( this ).data( 'import-confirm' ) ) ) {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'cyprus_import_code',
						security: contents.data( 'security' ),
						code: $( '#import-code-value' ).val()
					}
				}).done( function( result ) {
					if ( result.message ) {
						alert( result.message )
					} else {
						alert( result.error )
					}
				}).fail( function( result ) {
					alert( 'Something went wrong. Please try again later.' )
				})
			}
			return false
		})

		$( '#mts-opts-export-code-copy' ).click( function( event ) {
			event.preventDefault()
			importWrapper.hide()
			exportWrapper.toggle().select()
		})

		$( '#mts-create-child-theme' ).on( 'click', function( event ) {
			event.preventDefault()

			var button = $( this ),
				themeName = button.prev().val().trim()

			if ( '' === themeName ) {
				alert( 'Enter a theme name.' )
				return
			}

			button.prop( 'disabled', true )

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'cyprus_create_child_theme',
					themeName: themeName
				}
			}).always( function() {
				button.prop( 'disabled', false )
			}).done( function( result ) {
				alert( 'Chlid theme created.' )
			})
		})
	})
}( jQuery ) )
