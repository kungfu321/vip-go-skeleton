<?php
/**
 * Before footer detect ad blocker notice.
 *
 */

if ( cyprus_get_settings( 'detect_adblocker' ) && ( 'popup' === cyprus_get_settings( 'detect_adblocker_type' ) || 'floating' === cyprus_get_settings( 'detect_adblocker_type' ) ) ) { ?>
	<?php if ( 'popup' === cyprus_get_settings( 'detect_adblocker_type' ) ) { ?>
		<div class="blocker-overlay"></div>
	<?php } ?>
	<?php echo detect_adblocker_notice(); ?>
<?php } ?>
