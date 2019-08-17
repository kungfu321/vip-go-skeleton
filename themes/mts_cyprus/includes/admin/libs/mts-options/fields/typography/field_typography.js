/* global mtsOptions, typographyPreview, initTypography */
;( function( $ ) {

	$( document ).ready( function() {

		if ( 'undefined' === typeof mtsOptions.mtsGoogleFonts || 'undefined' === typeof mtsOptions.mtsGoogleFonts.generatePreview ) {
			return;
		}

		window.typographyPreview = function( selector ) {

			// Main id for selected field
			var parent			= selector.closest( '.typography-controls' ),
				family			= $( '.typography-family', parent ).val(),
				familyBackup	= $( '.typography-family-backup', parent ).val(),
				weight			= $( '.typography-font-weight', parent ).val(),
				style			= $( '.typography-font-style', parent ).val(),
				additional      = $( '.typography-additional-css', parent ).val(),
				dropdown		= parent.find( '.typography-variant' ),
				google			= true,
				variants		= null,
				html			= '<option value=""></option>',
				selected		= '';

			if ( 'normal' === weight ) {
				weight = '400';
			}

			var variant = weight + style;

			if ( null === family || undefined === family ) {
				family = familyBackup;
				google = false;
			}

			// Get font details
			if ( family in mtsOptions.mtsGoogleFonts.googleFonts ) {
				variants = mtsOptions.mtsGoogleFonts.googleFonts[ family ].variants;
			} else {
				variants = [
					{ id: '400', name: 'Normal 400' },
					{ id: '400italic', name: 'Normal 400 Italic' },
					{ id: '700', name: 'Bold 700' },
					{ id: '700italic', name: 'Bold 700 Italic' }
				];
			}

			$.each( variants, function( index, item ) {
				if ( variant === item.id ) {
					selected = ' selected="selected"';
					variant = item.id;
				} else {
					selected = '';
				}

				html += '<option value="' + item.id + '"' + selected + '>' + item.name.replace( /\+/g, ' ' ) + '</option>';
			});

			// Re-create select2
			dropdown.select2( 'destroy' );
			dropdown.html( html );
			dropdown.select2({ allowClear: true });

			// Show more preview stuff -------------------------------

			// Do preview
			if ( ! parent.hasClass( 'do-preview' ) ) {
				return;
			}

			var preview			= $( '.preview_text', parent ),
				color			= $( '.typography-color', parent ).val(),
				size			= $( '.typography-font-size', parent ).val(),
				height			= $( '.typography-line-height', parent ).val(),
				letter			= $( '.typography-letter-spacing', parent ).val(),
				marginTop		= $( '.typography-margin-top', parent ).val(),
				marginBottom	= $( '.typography-margin-bottom', parent ).val();

			if ( null !== family && 'inherit' !== family ) {

				// Replace spaces with "+" sign
				var theFont = family.replace( /\s+/g, '+' );
				if ( google ) {

					if ( variant && '' !== variant ) {
						theFont += ':' + variant.replace( /\-/g, ' ' );
					}

					$( 'head' ).append( '<link href="//fonts.googleapis.com/css?family=' + theFont + '" rel="stylesheet">' );
				}
			}

			preview.removeAttr( 'style' );
			if ( color ) {
				preview.css( 'color', color );
			}
			if ( size ) {
				preview.css( 'font-size', size );
			}
			if ( weight ) {
				preview.css( 'font-weight', weight );
			}
			if ( style ) {
				preview.css( 'font-style', style );
			}
			if ( height ) {
				preview.css( 'line-height', height );
			}
			if ( letter ) {
				preview.css( 'letter-spacing', letter );
			}
			if ( marginTop ) {
				preview.css( 'margin-top', marginTop );
			}
			if ( marginBottom ) {
				preview.css( 'margin-bottom', marginBottom );
			}
			if ( additional ) {
				additional = preview.attr( 'style' ) + additional;
				preview.attr( 'style', additional );
			}

			// Properly format the family.
			if ( null === family ) {
				family = '';
			}

			if ( -1 !== family.indexOf( ',' ) ) {

				// This contains multiple font-families, we must separate them
				// and process them individually before re-combining them.
				family = family.split( ',' );
				for ( var i = 0; i < family.length; i++ ) {

					// Remove extra spaces.
					family[ i ] = family[ i ].trim();

					// Remove quotes and double quotes.
					family[ i ] = family[ i ].split( '"' ).join( '' );
					family[ i ] = family[ i ].split( '\'' ).join( '' );

					// Add doublequotes if needed.
					if ( -1 !== family[ i ].indexOf( ' ' ) ) {
						family[ i ] = '"' + family[ i ] + '"';
					}
				}
				family = family.join( ', ' );
			} else {
				family = family.trim();
				family = family.split( '"' ).join( '' );
				family = family.split( '\'' ).join( '' );
				if ( -1 !== family.indexOf( ' ' ) ) {
					family = '"' + family + '"';
				}
			}

			preview.css( 'font_family', family + ', sans-serif' );

			if ( 'none' === family && '' === family ) {

				// If selected is not a font remove style "font_family" at preview box
				preview.css( 'font_family', 'inherit' );
			}
		};

		window.initTypography = function( parent ) {

			parent.find( '.typography-color' ).wpColorPicker({
				change: function( e, ui ) {
					$( this ).val( ui.color.toString() );
					typographyPreview( $( this ) );
				}
			});

			parent.find( '.typography-font-size, .typography-line-height, .typography-letter-spacing, .typography-margin-top, .typography-margin-bottom, .typography-additional-css' ).keyup( function() {
				typographyPreview( $( this ) );
			});

			parent.find( '.typography-family-backup' ).select2({ allowClear: true }).on( 'select2:select', function() {
				typographyPreview( $( this ) );
			});

			parent.find( '.typography-variant' ).select2().on( 'select2:select', function() {

				var $this = $( this ),
					value = $this.val();

				$( '.typography-font-style', parent ).val( ( value.includes( 'italic' ) ? 'italic' : '' ) );
				$( '.typography-font-weight', parent ).val( parseInt( value ) );

				typographyPreview( $this );
			});

			// Select2 magic, to load font_family dynamically
			$.fn.select2.amd.require([
				'select2/data/array',
				'select2/utils'
			], function( ArrayAdapter, Utils ) {

				function LazyAdapter( $element, options ) {
					this.lazyOptions = $.extend({}, options.get( 'lazy' ), true );
					this.lazyOptions.pageSize = this.lazyOptions.pageSize || 30;
					this.lazyOptions.data = this.lazyOptions.data || [];
					ArrayAdapter.__super__.constructor.call( this, $element, options );
				}

				Utils.Extend( LazyAdapter, ArrayAdapter );

				LazyAdapter.prototype._genPaginatedData = function( data, params ) {

					var page = params.page || 1;
					var term = params.term || '';
					var pageSize = this.lazyOptions.pageSize;
					var _items = [];

					try {
						_items = data.filter( function( item ) {
							return ( 0 <= item.text.toLowerCase().indexOf( term.toLowerCase() ) );
						});
					} catch ( e ) {}

					return {
						items: _items.slice( ( page - 1 ) * pageSize, page * pageSize ),
						totalCount: _items.length
					};
				};

				LazyAdapter.prototype._formatPaginatedResult = function( data, params ) {
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination: {
							more: ( params.page * this.lazyOptions.pageSize ) < data.totalCount
						},
						more: ( params.page * this.lazyOptions.pageSize ) < data.totalCount
					};
				};

				LazyAdapter.prototype.query = function( params, callback ) {

					var data = this._genPaginatedData( this.lazyOptions.data, params );
					var results = this._formatPaginatedResult( data, params );

					callback( results );
				};

				parent.find( '.typography-family' ).select2({
					allowClear: true,
					ajax: {},
					lazy: {
						data: mtsOptions.mtsGoogleFonts.fonts,
						pageSize: 20
					},
					dataAdapter: LazyAdapter,
					dropdownCssClass: 'typography-family-select2',

					templateResult: function( item ) {

						if ( undefined === item.id || ! item.hasPreview ) {
							return $( '<span class="no-preview">' + item.text + '</span>' );
						}

						return $( '<span class="has-preview" style="background-image: url(' + mtsOptions.mtsGoogleFonts.previewUrl + item.hasPreview + '.png)"></span>' );
					}

				}).on( 'select2:select', function() {

					typographyPreview( $( this ) );

				});

			});

			typographyPreview( parent.find( '.typography-family' ) );
		};

		$( '.typography-preview-color' ).on( 'click', 'a', function( event ) {
			event.preventDefault();

			var $this = $( this ),
				parent = $this.parent().parent();

			if ( $this.hasClass( 'dark' ) ) {
				parent.addClass( 'dark' );
			} else {
				parent.removeClass( 'dark' );
			}
			parent.prev( 'input' ).val( $this.attr( 'class' ) );
		});

		$( '.typography-controls:not(.collection-template)' ).each( function() {
			initTypography( $( this ) );
		});

		// Async Preview Generation
		$( window ).on( 'load', function() {

			if ( '' === mtsOptions.mtsGoogleFonts.generatePreview ) {
				return false;
			}

			setTimeout( function() {

				var count = 0,
					fonts = {};

				$.each( mtsOptions.mtsGoogleFonts.fonts, function() {

					if ( 10 === count ) {

						$.ajax({
							url: ajaxurl,
							method: 'post',
							dataType: 'json',
							data: {
								action: 'mts_generate_font_preview',
								fonts: fonts
							}
						});

						return false;
					}

					if ( ! this.hasPreview ) {
						var id = this.id.toLowerCase().replace( / /g, '' );
						fonts[ id ] = this.text;
						count++;
					}
				});

				if ( Object.keys( fonts ).length ) {
					$.ajax({
						url: ajaxurl,
						method: 'post',
						dataType: 'json',
						data: {
							action: 'mts_generate_font_preview',
							fonts: fonts
						}
					});
				}

			}, 3000 );

		});
	});

}( jQuery ) )
