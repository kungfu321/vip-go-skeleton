;(function( $ ) {

	$( document ).ready(function() {

		if ( 'undefined' === typeof $.fn.accordion ) {
			return;
		}

		// Prevent accordion open when draggin in FF
		var dragged = false;

		$( 'div[id=mts-opts-groups-accordion] > div > h3' ).click(function( event ) {
			if ( dragged ) {
				event.stopImmediatePropagation();
				event.preventDefault();
				dragged = false;
			}
		});

		$( '.mts-opts-groups-accordion-group' ) .each(function() {

			var $this = $( this );
			if ( ! $this.is( '.mts-opts-dummy' ) ) {

				var $header = $( 'h3 > span', $this ),
					$firstField = $( 'textarea, input[type=text], select', this ).first(),
					groupHeader = '';

				// Get group title from first field value
				// Or first SELECT OPTION text
				if ( 'undefined' === typeof $firstField ) {
					groupHeader = '';
				} else if ( $firstField.is( 'select' ) ) {
					groupHeader = $firstField.find( 'option:selected' ).text();
				} else {
					groupHeader = $firstField.val();
				}

				if ( groupHeader.length > 0 ) {
					groupHeader = groupHeader.substring( 0, 32 );

				// First field empty - get dummy title
				} else {
					groupHeader = $this.siblings( '.mts-opts-dummy' ).find( '.mts-opts-groups-header' ).text();
				}

				$header.text( groupHeader );
			}
		});

		$( 'div[id=mts-opts-groups-accordion]' ).accordion({
			header: '> div > h3',
			collapsible: true,
			active: false,
			heightStyle: 'content',
			icons: {
				'header': 'ui-icon-plus',
				'activeHeader': 'ui-icon-minus'
			},
			activate: function( event, ui ) {

				// Refresh title
				var $firstField = ui.oldPanel.find( 'textarea, input[type=text], select' ).first(),
					groupHeader = '';

				if ( 'undefined' === typeof $firstField ) {
					groupHeader = '';
				} else if ( $firstField.is( 'select' ) ) {
					groupHeader = $firstField.find( 'option:selected' ).text();
				} else {
					groupHeader = $firstField.val();
				}

				if ( 'undefined' !== typeof groupHeader && groupHeader.length > 0 ) {
					groupHeader = groupHeader.substring( 0, 32 );
					ui.oldHeader.find( '.mts-opts-groups-header' ).text( groupHeader );
				}
			}
		})
		.sortable({
			axis: 'y',
			handle: 'h3',
			stop: function( event, ui ) {

				// IE doesn't register the blur when sorting
				// so trigger focusout handlers to remove .ui-state-focus
				ui.item.children( 'h3' ).triggerHandler( 'focusout' );
				var inputs = $( 'input.group-sort' );
				inputs.each(function( idx ) {
					$( this ).val( idx );
				});

				// Prevent accordion open when dragging in FF
				dragged = true;
				setTimeout(function() {
					dragged = false;
				}, 100 );
			}
		});

		$( '.mts-opts-groups-remove' ).on( 'click', function() {

			var $this = $( this );

			$this.parent().parent().slideUp( 'medium', function() {
				$( this ).remove();
			});
		});

		$( '.mts-opts-groups-close' ).on( 'click', function() {
			var $group = $( this ).closest( '.mts-opts-groups-accordion-group' );
			$group.find( 'h3' ).trigger( 'click' );
		});

		$( '.mts-opts-groups-add' ).on( 'click', function() {

			var $this = $( this ),
				newGroup = $this.prev().find( '.mts-opts-dummy' ).clone( true ).show(),
				groupCounter = $this.parent().find( '.mts-opts-dummy-group-count' ),
				groupCount = groupCounter.val();

			// Update the groupCounter
			groupCounter.val( parseInt( groupCount ) + 1 );
			$this.prev().append( newGroup );

			// Remove dummy classes from newGroup
			newGroup = $( newGroup );
			newGroup.removeClass( 'mts-opts-dummy' );

			// Deal with radio input
			newGroup.find( 'input[type="radio"]' ).each(function() {

				var $that = $( this ),
					attrName = $that.data( 'name' ),
					attrID = $that.attr( 'id' );

				if ( 'undefined' !== typeof attrID && false !== attrID ) {
					$that.attr( 'id', $that.attr( 'id' ).replace( 'dummy', groupCount ) );
				}
				if ( 'undefined' !== typeof attrName && false !== attrName ) {
					$that.attr( 'name', attrName.replace( '@', groupCount ) );
				}

				var label = $that.parent( 'label' );
				label.attr( 'for', label.attr( 'for' ).replace( 'dummy', groupCount ) );
			});

			// Other inputs
			newGroup.find( 'input[type="text"], input[type="number"], input[type="hidden"], textarea , select' ).each(function() {

				var $that = $( this ),
					attrName = $that.data( 'name' ),
					attrID = $that.attr( 'id' ),
					stdVal = $that.data( 'std' );

				// For some browsers, `attr` is undefined; for others,
				// `attr` is false.  Check for both.
				if ( 'undefined' !== typeof attrID && false !== attrID ) {
					 $that.attr( 'id',  $that.attr( 'id' ).replace( '@', groupCount ) );
				}
				if ( 'undefined' !== typeof attrName && false !== attrName ) {
					 $that.attr( 'name', attrName.replace( '@', groupCount ) );
				}

				if ( 'SELECT' === $that.prop( 'tagName' ) ) {
					// We clean select2 first
					$that.select2( 'destroy' );
					$that.next( '.select2-container' ).remove();
					$that.removeClass( 'select2-hidden-accessible' );
					$that.off( 'select2:select' );

					// Std
					$that.find( 'option[value="' + stdVal + '"]' ).prop( 'selected', true );
				} else {
					 $that.val( stdVal );
				}

				if ( $that.hasClass( 'popup-colorpicker' ) ) {
					 $that.wpColorPicker();
				}

				if ( $that.hasClass( 'mts-opts-iconselect' ) ) {
					 $that.select2({
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
				}
				if ( $that.hasClass( 'mts-opts-cats_multi_select' ) || $that.hasClass( 'search_select' ) ) {
					$that.select2();
				}

				if ( $that.hasClass( 'group-sort' ) ) {
					 $that.val( groupCount );
				}
			});

		   $( newGroup ).find( 'h3' ).trigger( 'click' );
		});

		// Fix "upload" field type issue
		$( '.mts-opts-dummy .mts-opts-upload-remove' ).trigger( 'click' );
	});

})( jQuery );
