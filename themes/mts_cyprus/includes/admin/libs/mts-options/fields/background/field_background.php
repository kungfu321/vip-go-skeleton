<?php
/**
 * Field: Background
 */
class MTS_Options_background extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$css_options = array(

			'repeat' => array(
				''          => esc_html__( 'Default', 'cyprus' ),
				'no-repeat' => esc_html__( 'No Repeat', 'cyprus' ),
				'repeat'    => esc_html__( 'Repeat All', 'cyprus' ),
				'repeat-x'  => esc_html__( 'Repeat Horizontally', 'cyprus' ),
				'repeat-y'  => esc_html__( 'Repeat Vertically', 'cyprus' ),
			),

			'attachment' => array(
				''        => esc_html__( 'Default', 'cyprus' ),
				'fixed'   => esc_html__( 'Fixed', 'cyprus' ),
				'scroll'  => esc_html__( 'Scroll', 'cyprus' ),
			),

			'position' => array(
				''              => esc_html__( 'Default', 'cyprus' ),
				'left top'      => esc_html__( 'Left Top', 'cyprus' ),
				'left center'   => esc_html__( 'Left center', 'cyprus' ),
				'left bottom'   => esc_html__( 'Left Bottom', 'cyprus' ),
				'center top'    => esc_html__( 'Center Top', 'cyprus' ),
				'center center' => esc_html__( 'Center Center', 'cyprus' ),
				'center bottom' => esc_html__( 'Center Bottom', 'cyprus' ),
				'right top'     => esc_html__( 'Right Top', 'cyprus' ),
				'right center'  => esc_html__( 'Right center', 'cyprus' ),
				'right bottom'  => esc_html__( 'Right Bottom', 'cyprus' ),
			),

			'size' => array(
				''        => esc_html__( 'Default', 'cyprus' ),
				'cover'   => esc_html__( 'Cover', 'cyprus' ),
				'contain' => esc_html__( 'Contain', 'cyprus' ),
			),

			'parallax' => array(
				'0' => esc_html__( 'Off', 'cyprus' ),
				'1' => esc_html__( 'On', 'cyprus' ),
			),
		);

		// Replace options if provided
		foreach ( $this->field['options'] as $key => $options ) {
			if ( array_key_exists( $key, $css_options ) && is_array( $options ) && ! empty( $options ) ) {
				$css_options[ $key ] = $options;
			}
		}

		$defaults = array(
			'color'         => '',
			'use'           => 'pattern',
			'image_upload'  => '',
			'image_pattern' => 'nobg',
			'gradient'      => array(
				'from'      => '',
				'to'        => '',
				'direction' => 'horizontal',
			),
			'repeat'        => '',
			'attachment'    => '',
			'position'      => '',
			'size'          => '',
			'parallax'      => '0',
		);

		$defaults = isset( $this->field['std'] ) ? wp_parse_args( $this->field['std'], $defaults ) : $defaults;
		$this->value = wp_parse_args( $this->value, $defaults );

		$field_name = $this->args['opt_name'] . '[' . $this->field['id'] . ']';
		?>
		<div class="bg-opt-wrapper">

			<?php if ( false !== $this->field['options']['color'] ) : ?>
				<div class="bg-opt-input-label"><?php esc_html_e( 'Background Color:', 'cyprus' ) ?></div>
				<input type="text" id="<?php echo $this->field['id'] ?>_color" name="<?php echo $field_name ?>[color]" value="<?php echo $this->value['color'] ?>" class="popup-colorpicker" data-alpha="true" data-default-color="<?php echo $defaults['color'] ?>" />
			<?php endif; ?>

			<div class="bg-opt-input-label"><?php esc_html_e( 'Background Image:', 'cyprus' ) ?></div>
			<fieldset class="green buttonset buttonset-tabs">

				<?php if ( false !== $this->field['options']['image_pattern'] ) : ?>
					<input type="radio" id="<?php echo $this->field['id'] ?>_pattern" name="<?php echo $field_name ?>[use]" class="mts-opts-button" value="pattern" <?php checked( $this->value['use'], 'pattern' ) ?> />
					<label id="mts-opts-button" for="<?php echo $this->field['id'] ?>_pattern" class="buttonset-tab"><?php esc_html_e( 'Pattern', 'cyprus' ) ?></label>
				<?php endif; ?>
				<?php if ( false !== $this->field['options']['image_upload'] ) : ?>
					<input type="radio" id="<?php echo $this->field['id'] ?>_upload" name="<?php echo $field_name ?>[use]" class="mts-opts-button" value="upload" <?php checked( $this->value['use'], 'upload' ) ?> />
					<label id="mts-opts-button" for="<?php echo $this->field['id'] ?>_upload" class="buttonset-tab"><?php esc_html_e( 'Upload', 'cyprus' ) ?></label>
				<?php endif; ?>
				<?php if ( false !== $this->field['options']['gradient'] ) : ?>
					<input type="radio" id="<?php echo $this->field['id'] ?>_gradient" name="<?php echo $field_name ?>[use]" class="mts-opts-button" value="gradient" <?php checked( $this->value['use'], 'gradient' ) ?> />
					<label id="mts-opts-button" for="<?php echo $this->field['id'] ?>_gradient" class="buttonset-tab"><?php esc_html_e( 'Gradient', 'cyprus' ) ?></label>
				<?php endif; ?>

			</fieldset>

			<?php if ( false !== $this->field['options']['image_pattern'] ) : $counter = 0; // Pattern ?>
				<div id="<?php echo $this->field['id'] ?>_pattern_tab" class="buttonset-tab-content">
					<fieldset class="mts-radio-img">
					<?php
					foreach ( $this->field['options']['image_pattern'] as $k => $v ) :
						$selected = '' !== checked( $this->value['image_pattern'], $k, false ) ? ' class="mts-radio-img-selected"' : '';
						$for = $this->field['id'] . '_' . $counter;
					?>
					<label<?php echo $selected  ?> for="<?php echo $for ?>">

						<input type="radio" id="<?php echo $for ?>" name="<?php echo $field_name ?>[image_pattern]" value="<?php echo $k ?>" <?php checked( $this->value['image_pattern'], $k ) ?> />
						<img src="<?php echo $v['img'] ?>" />

					</label>
					<?php
						$counter++;
					endforeach;
					?>
					</fieldset>
				</div>
			<?php endif; ?>

			<?php
			if ( false !== $this->field['options']['image_upload'] ) :

				if ( '' === $this->value['image_upload'] ) {
					$remove = ' style="display:none;"';
					$upload = '';
				} else {
					$remove = '';
					$upload = ' style="display:none;"';
				}
			?>
				<div id="<?php echo $this->field['id'] ?>_upload_tab" class="buttonset-tab-content">

					<fieldset>

						<input type="hidden" id="<?php echo $this->field['id'] ?>_image_upload" name="<?php echo $field_name ?>[image_upload]" value="<?php echo $this->value['image_upload'] ?>" />

						<img class="mts-opts-screenshot" id="mts-opts-screenshot-<?php echo $this->field['id'] ?>" src="<?php echo $this->value['image_upload'] ?>" data-return="url" />

						<a href="javascript:void(0);" class="mts-opts-upload button-secondary"<?php echo $upload ?> rel-id="<?php echo $this->field['id'] ?>_image_upload"><?php esc_html_e( 'Browse', 'cyprus' ) ?></a>

						<a href="javascript:void(0);" class="mts-opts-upload-remove"<?php echo $remove ?> rel-id="<?php echo $this->field['id'] ?>_image_upload"><?php esc_html_e( 'Remove Upload', 'cyprus' ) ?></a>

					</fieldset>

					<div class="bg-upload-selects">

						<?php
						if ( false !== $this->field['options']['repeat'] ) :
							$array = $css_options['repeat'];
						?>

							<div class="bg-upload-select">

								<label for="<?php echo $this->field['id'] ?>_repeat" class="bg-opt-input-label"><?php esc_html_e( 'Background Repeat:', 'cyprus' ) ?></label>

								<select id="<?php echo $this->field['id'] ?>_repeat" name="<?php echo $field_name ?>[repeat]">
									<?php foreach ( $array as $k => $v ) : ?>
									<option value="<?php echo $k ?>"<?php selected( $this->value['repeat'], $k ) ?>><?php echo $v ?></option>
									<?php endforeach; ?>
								</select>

							</div>

						<?php endif; ?>

						<?php
						if ( false !== $this->field['options']['attachment'] ) :
							$array = $css_options['attachment'];
						?>

							<div class="bg-upload-select">

								<label for="<?php echo $this->field['id'] ?>_attachment" class="bg-opt-input-label"><?php esc_html_e( 'Background Attachment:', 'cyprus' ) ?></label>

								<select id="<?php echo $this->field['id'] ?>_attachment" name="<?php echo $field_name ?>[attachment]">
									<?php foreach ( $array as $k => $v ) : ?>
									<option value="<?php echo $k ?>"<?php selected( $this->value['attachment'], $k ) ?>><?php echo $v ?></option>
									<?php endforeach; ?>
								</select>

							</div>

						<?php endif; ?>

						<?php
						if ( false !== $this->field['options']['position'] ) :
							$array = $css_options['position'];
						?>

							<div class="bg-upload-select">

								<label for="<?php echo $this->field['id'] ?>_position" class="bg-opt-input-label"><?php esc_html_e( 'Background Position:', 'cyprus' ) ?></label>

								<select id="<?php echo $this->field['id'] ?>_position" name="<?php echo $field_name ?>[position]">
									<?php foreach ( $array as $k => $v ) : ?>
									<option value="<?php echo $k ?>"<?php selected( $this->value['position'], $k ) ?>><?php echo $v ?></option>
									<?php endforeach; ?>
								</select>

							</div>

						<?php endif; ?>

						<?php
						if ( false !== $this->field['options']['size'] ) :
							$array = $css_options['size'];
						?>

							<div class="bg-upload-select">

								<label for="<?php echo $this->field['id'] ?>_size" class="bg-opt-input-label"><?php esc_html_e( 'Background Size:', 'cyprus' ) ?></label>

								<select id="<?php echo $this->field['id'] ?>_size" name="<?php echo $field_name ?>[size]">
									<?php foreach ( $array as $k => $v ) : ?>
									<option value="<?php echo $k ?>"<?php selected( $this->value['size'], $k ) ?>><?php echo $v ?></option>
									<?php endforeach; ?>
								</select>

							</div>

						<?php endif; ?>

					</div>

				</div>
			<?php endif; ?>

			<?php if ( false !== $this->field['options']['gradient'] ) : ?>

				<div id="<?php echo $this->field['id'] ?>_gradient_tab" class="buttonset-tab-content">
					<div class="color-gradient-wrapper">

						<div class="color-gradient-step-wrapper">
							<div class="bg-opt-input-label"><?php esc_html_e( 'From:', 'cyprus' ) ?></div>
							<input type="text" id="<?php echo $this->field['id'] ?>_gradient_from" name="<?php echo $field_name ?>[gradient][from]" value="<?php echo $this->value['gradient']['from'] ?>" data-alpha="true" class="popup-colorpicker" data-default-color="<?php echo $defaults['gradient']['from'] ?>" style="width:70px;"/>
						</div>

						<div class="color-gradient-step-wrapper">
							<div class="bg-opt-input-label"><?php esc_html_e( 'To:', 'cyprus' ) ?></div>
							<input type="text" id="<?php echo $this->field['id'] ?>_gradient_to" name="<?php echo $field_name ?>[gradient][to]" value="<?php echo $this->value['gradient']['to'] ?>" data-alpha="true" class="popup-colorpicker" data-default-color="<?php echo $defaults['gradient']['to'] ?>" style="width:70px;"/>
						</div>

						<div class="color-gradient-direction-wrapper">
							<label for="<?php echo $this->field['id'] ?>_gradient_direction" class="bg-opt-input-label"><?php esc_html_e( 'Direction (enter deg for gradient direction):', 'cyprus' ) ?></label>
							<input type="text" id="<?php echo $this->field['id'] ?>_gradient_direction" name="<?php echo $field_name ?>[gradient][direction]" value="<?php echo $this->value['gradient']['direction'] ?>"/>
						</div>

					</div>
				</div>

			<?php endif; ?>

			<?php
			if ( false !== $this->field['options']['parallax'] ) :
				$array = $css_options['parallax'];
				$counter = 0;
			?>
				<div class="bg-opt-input-label"><?php esc_html_e( 'Parallax Effect:', 'cyprus' ) ?></div>
				<fieldset class="buttonset parallax-buttonset">
					<?php
					foreach ( $array as $k => $v ) :
						$for = $this->field['id'] . '_parallax_' . $counter;
					?>
						<input type="radio" id="<?php echo $for ?>" name="<?php echo $field_name ?>[parallax]" class="mts-opts-button" value="<?php echo $k ?>" <?php checked( $this->value['parallax'], $k ) ?>>
						<label id="mts-opts-button" for="<?php echo $for ?>"><?php echo $v ?></label>
					<?php
					$counter++;
					endforeach;
					?>
				</fieldset>
			<?php endif; ?>

		</div><!-- /end-background -->
		<?php
	}

	public function enqueue() {

		// Styles & Scripts for reused fields
		$existing_fields = array( 'color', 'upload', 'radio_img' );

		foreach ( $existing_fields as $key => $field_type ) {

			$field_class = 'MTS_Options_' . $field_type;

			if ( ! class_exists( $field_class ) ) {

				$class_file = $this->dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

				/** @noinspection PhpIncludeInspection */
				if ( $class_file ) {
					require_once $class_file;
				}
			}

			if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
				$enqueue = new $field_class( '', '', $this );
				$enqueue->enqueue();
			}
		}

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-button' );
	}
}
