/* global mtsOptions, confirm, alert */
;( function( $ ) {

	$.fn.isOnScreen = function() {

		if ( ! window ) {
			return;
		}

		var win = $( window ),
			viewport = {
				top: win.scrollTop(),
				left: win.scrollLeft()
			},
			bounds = this.offset();

		viewport.right = viewport.left + win.width();
		viewport.bottom = viewport.top + win.height();

		bounds.right = bounds.left + this.outerWidth();
		bounds.bottom = bounds.top + this.outerHeight();

		return ( ! ( viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom ) );
	};

	// Reverse jQuery plugin
	$.fn.reverse = [].reverse;

	var MTSOptions = {

		changeWarn: false,
		searched: false,
		history: window.History,
		blue: {
			'mts_primary_color': '#2196F3'
		},
		green: {
			'mts_primary_color': '#a0ce4e'
		},
		red: {
			'mts_primary_color': '#e10707'
		},
		light: {
			'mts_header_bg_color': '#ffffff',
			'mts_header_border_color': '#e8e8e8',
			'mts_background': '#ffffff',
			'mts_content_bg_color': '#ffffff',
			'mts_sidebar_bg_color': '#ffffff',
			'mts_footer_bg_color': '#263033',
			'mts_footer_border_color': '#e0e1e5',
			'mts_copyrights_border_color': '#e0e1e5',
			'mts_form_bg_color': '#ffffff',
			'mts_form_text_color': '#a8a6a6',
			'mts_form_border_color': '#d2d2d2',
			'mts_main_menu_bg_color': '#ffffff',
			'mts_main_menu_border_color': '#e2e2e2',
			'mts_mobile_menu_bg_color': '#f7f7f7'
		},
		dark: {
			'mts_header_bg_color': '#29292a',
			'mts_header_border_color': '#3e3e3e',
			'mts_background': '#29292a',
			'mts_content_bg_color': '#29292a',
			'mts_sidebar_bg_color': '#29292a',
			'mts_footer_bg_color': '#2d2d2d',
			'mts_footer_border_color': '#403f3f',
			'mts_copyrights_border_color': '#4b4c4d',
			'mts_form_bg_color': '#3e3e3e',
			'mts_form_text_color': '#cccccc',
			'mts_form_border_color': '#212122',
			'mts_main_menu_bg_color': '#ffffff',
			'mts_main_menu_border_color': '#29292A',
			'mts_mobile_menu_bg_color': '#3e3e3e'
		},

		init: function() {

			this.tabs();
			this.loadLastTab();
			this.footer();
			this.searching();
			this.colorScheme();
			this.dependencyManager();
			this.misc();
			this.multiselect();
			this.sidebars_helper();
		},

		dependencyManager: function() {

			function checkDependency( $currentValue, $desiredValue, $comparison ) {
				var $passed = false;
				if ( '==' === $comparison ) {
					if ( $currentValue == $desiredValue ) {
						$passed = true;
					}
				}
				if ( '=' === $comparison ) {
					if ( $currentValue = $desiredValue ) {
						$passed = true;
					}
				}
				if ( '>=' === $comparison ) {
					if ( $currentValue >= $desiredValue ) {
						$passed = true;
					}
				}
				if ( '<=' === $comparison ) {
					if ( $currentValue <= $desiredValue ) {
						$passed = true;
					}
				}
				if ( '>' === $comparison ) {
					if ( $currentValue > $desiredValue ) {
						$passed = true;
					}
				}
				if ( '<' === $comparison ) {
					if ( $currentValue < $desiredValue ) {
						$passed = true;
					}
				}
				if ( '!=' === $comparison ) {
					if ( $currentValue != $desiredValue ) {
						$passed = true;
					}
				}

				return $passed;
			}

			function loopDependencies( $container ) {
				var passed,
					relation = $container.data( 'relation' );

				var $table = $container.closest( '.form-table' );
				$container.find( 'span' ).each( function() {

					var $this = $( this ),
						$value = $this.data( 'value' ),
						$field = $this.data( 'field' ),
						$comparison = $this.data( 'comparison' );

					$field = $table.hasClass( 'mts-opts-group' ) ? $( "[name*='" + $field + "']", $table ) : $( "[name*='" + $field + "']" );
					var $fieldValue = $field.val();

					if ( $field.is( ':radio' ) ) {
						$fieldValue = $field.filter( ':checked' ).val();
					}

					var result = checkDependency( $fieldValue, $value, $comparison );
					if ( 'or' === relation && result ) {
						passed = true;
						return false;
					} else if ( 'and' === relation ) {
						if ( undefined === passed ) {
							passed = result;
						} else {
							passed = passed && result;
						}
					}
				});

				if ( passed ) {
					 $container.closest( 'tr' ).fadeIn( 300 );
				} else {
					 $container.closest( 'tr' ).hide();
				}
			}

			$( '.cyprus-option-dependency' ).each( function() {
				loopDependencies( $( this ) );
			});

			var $nots = $( 'input, select', '.typography-controls' );
			$( 'input, select', '.mts-opts-group-tab' ).not( $nots ).on( 'change', function() {

				var $this = $( this );
				var $table = $this.closest( '.form-table' );
				var $field = $this.data( 'id' ) || $this.attr( 'name' );

				$field = $field.replace( mtsOptions.opt_name + '[', '' ).replace( ']', '' );

				var selector = $table.hasClass( 'mts-opts-group-tab' ) ? $( 'span[data-field="' + $field + '"]', $table ) : $( 'span[data-field="' + $field + '"]' );
				selector.each( function() {
					loopDependencies( $( this ).closest( '.cyprus-option-dependency' ) );
				});

			});
		},

		colorScheme: function() {

			var self = this;

			$( '.buttonset #mts_theme_skin_0, .buttonset #mts_theme_skin_1' ).on( 'click', function() {

				var skin = $( this ).val();
				skin = ( 'dark' === skin ) ? self.dark : self.light;

				for ( var id in skin ) {
					self.updateColor( id, skin[id] );
				}
			});

			$( '.mts-radio-img #mts_color_scheme_0, .mts-radio-img #mts_color_scheme_1, .mts-radio-img #mts_color_scheme_2' ).on( 'click', function() {

				var scheme = $( this ).val();

				if ( 'blue' === scheme ) {
					scheme = self.blue;
				} else if ( 'green' === scheme  ) {
					scheme = self.green;
				}else if ( 'red' === scheme ) {
					scheme = self.red;
				}

				for ( var id in scheme ) {
					self.updateColor( id, scheme[id] );
				}
			});
		},

		updateColor: function( id, hex ) {
			$( '#colorpicker-' + id + ' .wp-color-result' ).css( 'background-color', hex );
			$( '#colorpicker-' + id + ' .wp-color-picker' ).val( hex );
		},

		searching: function() {

			var self = this,
				div = $( '.header-search' ),
				input = div.find( 'input' );

			input.on( 'keyup', function( event ) {

				var value = input.val().trim();

				div.addClass( 'active' );

				// If Esc press clear search
				// or empty value
				if ( 27 === event.keyCode || '' === value ) {
					self.clearSearch( true );
					input.val( '' );

					return false;
		}

		event.preventDefault();

		self.searchOptions( value );

				return false;

			}).on( 'blur', function() {

				var value = input.val().trim();
				if ( '' === value ) {
					div.removeClass( 'active' );
				}
			});

			div.find( '.dashicons-no' ).on( 'click', function( event ) {

				event.preventDefault();

				self.clearSearch( true );
				input.val( '' );
				div.removeClass( 'active' );
			});
		},

		searchOptions: function( query ) {

			var self = this,
				noresults = true;

			self.searched = true;
			query = query.toLowerCase();

			// De-select tabs
			$( '.mts-opts-group-menu li.active' ).removeClass( 'active' );
			$( '.mts-opts-info-field' ).hide();

			$( '#mts-opts-main' ).children( 'div' ).hide().each(function() {

				if ( 'typography_section_group' === this.id ) {
					return true;
				}

				var $contents = $( this );
				$contents.children( 'table' ).children( 'tbody' ).children( 'tr' ).each(function() {

					var $row = $( this );
					$row.hide();

					if ( $row.find( 'th' ).text().toLowerCase().indexOf( query ) !== -1 ) {
						$contents.show();
						$row.show();
						noresults = false;
					}

				}).reverse().each(function() {

					if ( $( this ).is( ':visible' ) && $( this ).find( '.buttonset-hide' ).length ) {

						$( this ).find( '#mts-opts-button-show-below' ).each(function() {

							var num = $( this ).parent().data( 'hide' );

							if ( $( this ).hasClass( 'ui-state-active' ) ) {
								$( this ).closest( 'tr' ).nextAll( 'tr:lt(' + num + ')' ).show();
							} else {
								$( this ).closest( 'tr' ).nextAll( 'tr:lt(' + num + ')' ).hide();
							}
						});
					}
				});
			});

			if ( noresults ) {
				$( '#options-search-no-results' ).show();
			}
		},

		clearSearch: function( clickTab ) {

			var self = this;

			if ( ! self.searched ) {
				return false;
			}

			$( '#mts-opts-main' ).children().each(function() {

				$( this ).find( 'tr' ).show().reverse().each(function() {

					if ( $( this ).find( '.buttonset-hide' ).length ) {

						$( this ).find( '#mts-opts-button-show-below' ).each(function() {

							var num = $( this ).parent().data( 'hide' );

							if ( $( this ).hasClass( 'ui-state-active' ) ) {
								$( this ).closest( 'tr' ).nextAll( 'tr:lt(' + num + ')' ).show();
							} else {
								$( this ).closest( 'tr' ).nextAll( 'tr:lt(' + num + ')' ).hide();
							}
						});
					}
				});
			});

			$( '.mts-opts-info-field' ).show();

			if ( clickTab ) {

				var lastTab = $( '#last_tab' ).val();
				if ( '0' === lastTab ) {
					$( 'li:first', '#mts-opts-group-menu' ).trigger( 'click' );
				} else {
					$( '#' + lastTab ).trigger( 'click' );
				}
			} else {
				$( 'input', '.header-search' ).val( '' ).parent().removeClass( 'active' );
			}

			self.searched = false;
		},

		footer: function() {

			// Floating footer
			var $footer = $( '#mts-opts-footer' );

			// Set Width
			var footerPadding = $footer.innerWidth() - $footer.width();
			$footer.width( $( '.mts-opts-form-wrapper' ).width() - footerPadding );

			var stickMe = function() {
				if ( ! $( '#mts-opts-bottom' ).isOnScreen() ) {
					$footer.addClass( 'floating' );
				} else {
					$footer.removeClass( 'floating' );
				}
			};
			stickMe();

			// On scroll
			$( window ).on( 'scroll', function() {
				stickMe();
			}).on( 'resize', function() {
				$footer.width( $( '.mts-opts-form-wrapper' ).width() - footerPadding );
			});
		},

		misc: function() {

			var self = this;

			$( 'td', '.mts-type-heading' ).attr( 'colspan', '2' );

			$( '#mts-opts-form-wrapper' ).submit(function() {
				var currentTab = $( '#mts-opts-group-menu li.active:last' ).attr( 'id' );
				$( '#last_tab' ).val( currentTab );

				$( '.typography-controls.collection-template' ).remove();

				self.changeWarn = false;
		});

			var optsSave = $( '.mts-opts-save' );
			if ( optsSave.is( ':visible' ) ) {
		optsSave.delay( 4000 ).slideUp( 'slow' );
		}

			var optsImported = $( '.mts-opts-imported' );
			if ( optsImported.is( ':visible' ) ) {
		optsImported.delay( 4000 ).slideUp( 'slow' );
		}

		$( 'input, textarea, select', '#mts-opts-form-wrapper' ).on( 'change', function() {
				if ( ! self.changeWarn ) {
		  $( '.mts-opts-save-warn' ).slideDown( 'slow' );
			  self.changeWarn = true;
		}
		});

			window.onbeforeunload = function() {
		if ( self.changeWarn ) {
		  return mtsOptions.leave_page_confirm;
		}
		};

		// Confirm reset
		$( '#mts-opts-footer .button-secondary' ).click(function() {
		return confirm( mtsOptions.reset_confirm );
		});

			// Disallow submission by enter key
		$( '#mts-opts-form-wrapper' ).find( 'input' ).keydown(function( event ) {
		if ( 13 === event.keyCode ) {
		  event.preventDefault();
		  }
		});
		},

		multiselect: function() {

			$( '.search_select' ).select2();

			$(document).on( 'click', '.select_all', function( event ) {
			var $this   = $(event.target);
			var $select = $this.closest('td').find('select');
			if ( ! $this.data( 'checked' ) ) {
					$select.find( 'option' ).prop( 'selected', 'selected' ); // Select All Options
					$select.trigger( 'change' ); // Trigger change select2
			$this.val( 'Clear all' );
			$this.data( 'checked', 1 );
				} else {
					$select.find( 'option' ).removeAttr( 'selected' );
					$select.trigger( 'change' ); // Trigger change select2
			$this.val( 'Select all' );
			$this.data( 'checked', 0 );
			}
			});

		},

		tabs: function() {

			var self = this,
				lis = $( 'li', '#mts-opts-group-menu' );

			lis.click( function() {
				var $this = $( this ),
					target = $this.attr( 'id' );

				if ( $this.hasClass( 'has-child' ) ) {
					$this.find( '.submenu a:first' ).trigger( 'click' );
					return;
				}

				self.clearSearch();
				$( '#last_tab' ).val( target );

				lis.removeClass( 'active' );
				$this.closest( '.has-child' ).addClass( 'active' );
				$this.addClass( 'active' );

				$( '.mts-opts-group-tab' ).hide();
				$( '#' + target + '_section_group' ).show();

				var pageURL = $( '[name="_wp_http_referer"]' ).val();
				var newURL = pageURL.indexOf( 'tab=' ) === -1 ? pageURL + '&tab=' + target : pageURL;
				self.history.pushState({
					tab: target
				}, document.title, newURL );

				return false;
			});
		},

		loadLastTab: function() {

			var self = this;

			var lis = $( 'li', '#mts-opts-group-menu' );

			lis.removeClass( 'active' );
			$( '.mts-opts-group-tab' ).hide();

			var lastTab = $( '#last_tab' ).val();
			if ( '0' === lastTab ) {
				lis.eq( 0 ).trigger( 'click' );
			} else {
				$( '#' + lastTab ).trigger( 'click' );
			}

			self.history.Adapter.bind( window, 'load statechange', function() {

				var state = self.history.getState(),
					tab = state.data.tab;

				if ( 'undefined' !== typeof tab ) {
					$( '#' + tab ).trigger( 'click' );
				}
			});
		},

		scrollImportLogToBottom: function() {
	  var element = document.getElementById( 'importing-modal-content' );
	  element.scrollTop = element.scrollHeight;
		},

		removeURLParameter: function( url, parameter ) {

	  var urlparts = url.split( '?' );

		if ( urlparts.length >= 2 ) {

			var prefix = encodeURIComponent( parameter ) + '=',
					parts = urlparts[1].split( /[&;]/g );

			// Reverse iteration as may be destructive
			for ( var i = parts.length; i-- > 0; ) {

				if ( parts[i].lastIndexOf( prefix, 0 ) !== -1 ) {
					parts.splice( i, 1 );
				}
			}

			url = urlparts[0] + '?' + parts.join( '&' );

		}
			return url;
		},
		sidebars_helper: function() {
			if ( ! $('#sidebars-general_section_group').length ) {
				return;
			}
			$('#sidebars-general_section_group').on( 'input', '#mts_custom_sidebar_name', function() {
				var $id_field = $(this).closest('tr').next('tr').find('#mts_custom_sidebar_id');
				if ( $id_field.data('value_changed') != '1' ) {
					$id_field.val( MTSOptions.slugify( $(this).val() ) );
				}
			}).on( 'input', '#mts_custom_sidebar_id', function() {
				$(this).data('value_changed', '1');
			});
		},
		slugify: function( text ) {
			return text.toString().toLowerCase()
				.replace(/\s+/g, '-')           // Replace spaces with -
				.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
				.replace(/\-\-+/g, '-')         // Replace multiple - with single -
				.replace(/^-+/, '')             // Trim - from start of text
				.replace(/-+$/, '');            // Trim - from end of text
		}
	};

	$( document ).ready( function() {
		MTSOptions.init();
	});

	$( window ).on( 'load', function() {
		$( '#savechanges' ).prop( 'disabled', false );
	});

}( jQuery ) );
