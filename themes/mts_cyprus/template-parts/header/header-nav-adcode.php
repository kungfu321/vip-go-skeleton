<?php if ( cyprus_get_settings( 'navigation_ad' ) && cyprus_get_settings( 'navigation_adcode' ) ) { ?>
	<div class="navigation-banner">
		<div class="container">
			<div style="<?php ad_size_value( cyprus_get_settings( 'navigation_ad_size' ) ); ?>">
				<?php echo cyprus_get_settings( 'navigation_adcode' ); ?>
			</div>
		</div>
	</div>
<?php } ?>
