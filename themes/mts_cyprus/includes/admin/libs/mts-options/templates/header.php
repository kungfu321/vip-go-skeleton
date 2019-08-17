<div id="mts-opts-header" class="mts-opts-header clearfix">

	<?php if ( ! MTS_THEME_WHITE_LABEL ) : ?>

		<a href="https://mythemeshop.com" class="logo" target="_blank">
			<img src="<?php echo $this->url ?>img/optionpanellogo.png" width="190" height="36" />
		</a>

		<span class="header-text"><?php esc_html_e( 'Welcome to your theme\'s mission control center.', 'cyprus' ) ?></span>

		<a href="https://community.mythemeshop.com/" class="header-community" target="_blank">
			<i class="dashicons dashicons-editor-help"></i> <?php esc_html_e( 'Support', 'cyprus' ) ?>
		</a>

	<?php endif; ?>

	<div class="header-search">

		<input type="text" placeholder="<?php esc_attr_e( 'Search Options', 'cyprus' ) ?>">

		<i class="dashicons dashicons-no"></i>
		<i class="dashicons dashicons-search"></i>

	</div>

</div>
