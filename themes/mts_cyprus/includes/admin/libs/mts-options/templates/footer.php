<div id="mts-opts-bottom">
	<div id="mts-opts-footer" class="mts-opts-footer clearfix">

		<?php if ( isset( $this->args['share_icons'] ) ) : ?>
			<div class="mts-opts-share">
				<?php foreach ( $this->args['share_icons'] as $link ) : ?>
				<a href="<?php echo $link['link'] ?>" title="<?php echo $link['title'] ?>" target="_blank"><i class="<?php echo $link['img'] ?>"></i></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<input type="submit" name="<?php echo $this->args['opt_name'] ?>[defaults-section]" value="<?php esc_attr_e( 'Reset Section', 'cyprus' ) ?>" class="button-secondary button-default-section" />
		<input type="submit" name="<?php echo $this->args['opt_name'] ?>[defaults]" value="<?php esc_attr_e( 'Reset All', 'cyprus' ) ?>" class="button-secondary" />
		<input type="submit" name="save" id="savechanges" value="<?php esc_attr_e( 'Save Changes', 'cyprus' ) ?>" class="button-primary" disabled="disabled" />

	</div>
</div>
