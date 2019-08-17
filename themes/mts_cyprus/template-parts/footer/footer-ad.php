<?php
/**
 * Before footer ad code.
 * @var [type]
 */
$ad_code = cyprus_get_settings( 'mts_postfooter_adcode' );
if ( empty( trim( $ad_code ) ) ) {
  return;
}

$toptime = cyprus_get_settings( 'mts_postfooter_adcode_time' );
if ( strcmp( date( 'Y-m-d', strtotime( -$toptime . ' day' ) ), get_the_time( 'Y-m-d' ) ) < 0 ) {
  return;
}
?>
<div class="footer-ad clearfix">
	<div class="container">
		<?php echo do_shortcode( cyprus_get_settings( 'mts_postfooter_adcode' ) ); ?>
	</div>
</div>
