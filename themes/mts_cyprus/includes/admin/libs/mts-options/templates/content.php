<div id="mts-opts-main" class="mts-opts-main">

	<?php foreach ( $this->sections as $id => $section ) : ?>
		<div id="<?php echo $id; ?>_section_group" class="mts-opts-group-tab<?php echo isset( $this->flatten_menus[ $id ]['hide_title'] ) && $this->flatten_menus[ $id ]['hide_title'] ? ' mts-opts-hide-title' : ''; ?>">
			<?php do_settings_sections( $id . '_section_group' ); ?>
		</div><!-- /<?php echo $id; ?>_section_group -->
	<?php endforeach; ?>

	<?php foreach ( $this->extra_tabs as $id => $section ) : ?>
		<div id="<?php echo $id; ?>_section_group" class="mts-opts-group-tab">

			<h2><?php echo $tab['title']; ?></h2>

			<?php echo $tab['content']; ?>

		</div>
	<?php endforeach; ?>

	<?php
	if ( true === $this->args['dev_mode'] ) {
		include_once $this->dir . 'templates/dev-mode.php';
	}
	?>

	<?php
		cyprus_action( 'options_after_section_items', $this );
		cyprus_action( 'options_after_section_items_' . $this->args['opt_name'], $this );
	?>

	<div id="options-search-no-results"><?php esc_html_e( 'No options found, please refine your query . ', 'cyprus' ); ?></div><!--search: no results-->

	<div class="clear"></div><!--clearfix-->

</div><!-- /main -->

<div class="clear"></div><!--clearfix-->
