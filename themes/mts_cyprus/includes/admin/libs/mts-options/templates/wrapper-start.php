<div class="wrap">
	<h2></h2>
	<?php
	if ( isset( $this->args['intro_text'] ) ) {
		echo $this->args['intro_text'];
	}

	cyprus_action( 'options_page_before_form' );
	cyprus_action( 'options_page_before_form_' . $this->args['opt_name'] );
	?>

	<form method="post" action="options.php" enctype="multipart/form-data" id="mts-opts-form-wrapper" class="mts-opts-form-wrapper">
		<?php settings_fields( $this->args['opt_name'] . '_group' ); ?>
		<input type="hidden" id="last_tab" name="<?php echo $this->args['opt_name']; ?>[last_tab]" value="<?php echo $this->options['last_tab']; ?>">
