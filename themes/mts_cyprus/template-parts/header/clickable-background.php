<?php
/**
 * Clickable Background
 *
 */

if ( cyprus_get_settings( 'background_clickable' ) && cyprus_get_settings( 'background_link' ) ) {
	$target = ( 1 === cyprus_get_settings( 'background_link_new_tab' ) ) ? 'target="_blank"' : '';
	printf( '<a href="%1$s" rel="nofollow" class="clickable-background" %2$s>', cyprus_get_settings( 'background_link' ), $target );
}
