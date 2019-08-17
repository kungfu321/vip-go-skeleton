jQuery(document).ready(function($) {
  function onChangeLayout() {
    $('.post-box-layout select').on('change', function () {
      $('.post-border-radius').css('display', (this.value == 'horizontal-small') ? 'block' : 'none');
    });
  }

  $( document ).on( 'widget-added widget-updated', onChangeLayout );

  $( document ).ready( function() {
    onChangeLayout();
  } );
});
