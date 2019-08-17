<div id="mts-opts-sidebar" class="mts-opts-sidebar">

	<ul id="mts-opts-group-menu" class="mts-opts-group-menu">

	<?php
		$this->render_menu( $this->menus );

		cyprus_action( 'options_after_section_menu_items', $this );
		cyprus_action( 'options_after_section_menu_items_' . $this->args['opt_name'], $this );
	?>

	<?php $this->render_menu( $this->extra_tabs ); ?>

	<?php if ( true === $this->args['dev_mode'] ) : ?>
		<li id="dev_mode">
			<a href="javascript:void(0);">
				<img src="<?php echo $this->url; ?>img/glyphicons/glyphicons_195_circle_info.png">
				<span class="section_title"><?php esc_html_e( 'Dev Mode Info', 'cyprus' ); ?></span>
			</a>
		</li>
	<?php endif; ?>

	</ul>

</div><!-- /sidebar -->
