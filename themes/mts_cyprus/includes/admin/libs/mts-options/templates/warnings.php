<?php if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] && get_transient( 'mts-opts-saved' ) == '1' ) :
	if ( isset( $this->options['imported'] ) && 1 == $this->options['imported'] ) :
?>
	<div class="mts-opts-imported">
		<strong><?php esc_html_e( 'Settings Imported!', 'cyprus' ); ?></strong>
	</div>
	<?php else : ?>
	<div class="mts-opts-save">
		<strong><?php esc_html_e( 'Settings Saved!', 'cyprus' ); ?></strong>
	</div>
<?php
		endif;

		delete_transient( 'mts-opts-saved' );
	endif;
?>
<div class="mts-opts-save-warn">
	<?php esc_html_e( 'Settings have changed, you should save them!', 'cyprus' ); ?>
</div>

<div class="mts-opts-field-errors">
	<strong><span></span> <?php esc_html_e( 'error(s) were found!', 'cyprus' ); ?></strong>
</div>

<div class="mts-opts-field-warnings">
	<strong><span></span> <?php esc_html_e( 'warning(s) were found!', 'cyprus' ); ?></strong>
</div>
