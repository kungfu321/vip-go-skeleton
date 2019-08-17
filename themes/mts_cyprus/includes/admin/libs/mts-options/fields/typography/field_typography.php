<?php
/**
 * Field: Typography
 *
 * @package Cyprus
 */

class MTS_Options_typography extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->parent = $parent;

		$defaults    = array(
			'font-backup'    => true,
			'font-variant'   => true,
			'color'          => true,
			'font-size'      => true,
			'line-height'    => true,
			'letter-spacing' => true,
			'margin-top'     => true,
			'margin-bottom'  => true,
			'additional-css' => true,
			'preview'        => true,
			'css-selectors'  => true,
		);
		$this->field = wp_parse_args( $field, $defaults );

		$defaults = array(
			'font-family'    => '',
			'font-backup'    => '',
			'font-weight'    => '',
			'font-style'     => '',
			'color'          => '',
			'font-size'      => '',
			'line-height'    => '',
			'letter-spacing' => '',
			'margin-top'     => '',
			'margin-bottom'  => '',
			'additional-css' => '',
			'css-selectors'  => '',
			'preview-color'  => 'light',
			'preview-text'   => 'The quick brown fox jumps over the lazy dog',
		);

		$defaults    = isset( $this->field['std'] ) ? wp_parse_args( $this->field['std'], $defaults ) : $defaults;
		$this->value = wp_parse_args( $value, $defaults );

		$this->std_fonts = array(
			'Helvetica, Arial, sans-serif',
			"'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif",
			"'Comic Sans MS', cursive",
			'Courier, monospace',
			'Garamond, serif',
			'Georgia, serif',
			'Impact, Charcoal, sans-serif',
			"'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			'Tahoma,Geneva, sans-serif',
			"'Times New Roman', Times,serif",
			"'Trebuchet MS', Helvetica, sans-serif",
			'Verdana, Geneva, sans-serif',
		);
	}

	public function render() {
		?>
		<div class="typography-controls<?php echo $this->field['preview'] ? ' do-preview' : ''; ?>" data-id="<?php echo $this->field['id']; ?>">

			<?php if ( $this->field['preview'] ) : ?>
			<input type="hidden" class="typography-preview-color" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][preview-color]" value="<?php echo esc_attr( $this->value['preview-color'] ); ?>">
			<div class="typography-preview <?php echo $this->value['preview-color']  ?>">
				<div class="preview_text" contenteditable="true">
					<?php echo $this->value['preview-text']; ?>
				</div>
				<div class="typography-preview-color" title="<?php esc_attr_e( 'Preview Background Color', 'cyprus' ); ?>">
					<a href="javascript:;" class="light"></a>
					<a href="javascript:;" class="dark"></a>
				</div>
			</div>
			<?php endif; ?>

			<div class="typography-controls-option clearfix">

				<div class="control-wrapper">
					<label><?php esc_html_e( 'Font Family', 'cyprus' ); ?></label>
					<select class="typography-family" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][font-family]" id="<?php echo $this->field['id']; ?>-family" data-placeholder="<?php echo esc_attr( $this->value['font-family'] ); ?>" data-value="">
						<option value=""></option>
						<?php if ( ! empty( $this->value['font-family'] ) ) : ?>
						<option value="<?php echo esc_attr( $this->value['font-family'] ); ?>" selected="selected"><?php echo $this->value['font-family']; ?></option>
						<?php endif; ?>
					</select>
				</div>

				<?php if ( $this->field['font-backup'] ) : ?>
				<div class="control-wrapper">
					<label><?php esc_html_e( 'Backup Font Family', 'cyprus' ); ?></label>
					<select data-placeholder="<?php esc_html_e( 'Backup Font Family', 'cyprus' ); ?>" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][font-backup]" class="typography-family-backup" id="<?php echo $this->field['id']; ?>-family-backup">
						<option value=""></option>
						<?php foreach ( $this->std_fonts as $font ) : ?>
							<option value="<?php echo $font; ?>"<?php selected( $this->value['font-backup'], $font ); ?>><?php echo $font; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>

				<?php if ( $this->field['font-variant'] ) : ?>
				<div class="control-wrapper">
					<label><?php esc_html_e( 'Font Weight &amp; Style', 'cyprus' ); ?></label>
					<input type="hidden" class="typography-font-weight" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][font-weight]" value="<?php echo esc_attr( $this->value['font-weight'] ); ?>">
					<input type="hidden" class="typography-font-style" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][font-style]" value="<?php echo esc_attr( $this->value['font-style'] ); ?>">
					<select data-placeholder="<?php esc_html_e( 'Style', 'cyprus' ); ?>" class="typography-variant" id="<?php echo $this->field['id']; ?>-style">
						<option value=""></option>
						<option value="200" >Lighter</option>
						<option value="400"  selected='selected'>Normal</option>
						<option value="700" >Bold</option>
						<option value="900" >Bolder</option>
					</select>
				</div>
				<?php endif; ?>

				<?php if ( $this->field['color'] ) : ?>
				<div class="control-wrapper typography-color-wrapper">
					<label><?php esc_html_e( 'Font Color', 'cyprus' ); ?></label>
					<input type="text" class="typography-color" id="<?php echo $this->field['id']; ?>-color" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][color]" placeholder="<?php esc_html_e( 'Font Color', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['color'] ); ?>">
				</div>
				<?php endif; ?>

				<div class="clearfix"></div>

				<?php if ( $this->field['font-size'] ) : ?>
				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Font Size', 'cyprus' ); ?></label>
					<input type="text" class="typography-font-size" id="<?php echo $this->field['id']; ?>-size" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][font-size]" placeholder="<?php esc_html_e( 'Size', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['font-size'] ); ?>">
				</div>
				<?php endif; ?>

				<?php if ( $this->field['line-height'] ) : ?>
				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Line Height', 'cyprus' ); ?></label>
					<input type="text" class="typography-line-height" id="<?php echo $this->field['id']; ?>-height" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][line-height]" placeholder="<?php esc_html_e( 'Line Height', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['line-height'] ); ?>">
				</div>
				<?php endif; ?>

				<?php if ( $this->field['letter-spacing'] ) : ?>
				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Letter Spacing', 'cyprus' ); ?></label>
					<input type="text" class="typography-letter-spacing" id="<?php echo $this->field['id']; ?>-spacing" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][letter-spacing]" placeholder="<?php esc_html_e( 'Letter Spacing', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['letter-spacing'] ); ?>">
				</div>
				<?php endif; ?>

				<div class="clearfix"></div>

				<?php if ( $this->field['margin-top'] ) : ?>
				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Margin Top', 'cyprus' ); ?></label>
					<input type="text" class="typography-margin-top" id="<?php echo $this->field['id']; ?>-margin-top" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][margin-top]" placeholder="<?php esc_html_e( 'Margin Top', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['margin-top'] ); ?>">
				</div>
				<?php endif; ?>

				<?php if ( $this->field['margin-bottom'] ) : ?>
				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Margin Bottom', 'cyprus' ); ?></label>
					<input type="text" class="typography-margin-bottom" id="<?php echo $this->field['id']; ?>-margin-bottom" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][margin-bottom]" placeholder="<?php esc_html_e( 'Margin Bottom', 'cyprus' ); ?>" value="<?php echo esc_attr( $this->value['margin-bottom'] ); ?>">
				</div>
				<?php endif; ?>

				<div class="clearfix"></div>

				<?php if ( $this->field['css-selectors'] ) : ?>
				<div class="control-wrapper auto-height">
					<label><?php esc_html_e( 'CSS Selectors', 'cyprus' ); ?></label>
					<textarea class="typography-css-selectors" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][css-selectors]"><?php echo esc_textarea( $this->value['css-selectors'] ); ?></textarea>
				</div>
				<?php endif; ?>

				<?php if ( $this->field['additional-css'] ) : ?>
				<div class="control-wrapper">
					<label><?php esc_html_e( 'Additional CSS', 'cyprus' ); ?></label>
					<textarea class="typography-additional-css" name="<?php echo $this->args['opt_name']; ?>[<?php echo $this->field['id']; ?>][additional-css]"><?php echo esc_textarea( $this->value['additional-css'] ); ?></textarea>
				</div>
				<?php endif; ?>

			</div>

		</div>
		<?php
	}

	public function enqueue() {

		// Styles & Scripts for reused fields
		$dir             = ! empty( $this->dir ) ? $this->dir : $this->parent->dir;
		$existing_fields = array( 'color', 'select' );

		foreach ( $existing_fields as $key => $field_type ) {

			$field_class = 'MTS_Options_' . $field_type;

			if ( ! class_exists( $field_class ) ) {

				$class_file = $dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

				if ( $class_file ) {
					require_once( $class_file );
				}
			}

			if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
				$enqueue = new $field_class( '', '', $this );
				$enqueue->enqueue();
			}
		}

		// Styles & Scripts for this field
		if ( ! isset( $this->parent->json['mtsGoogleFonts'] ) ) {
			$google         = array();
			$maybe_generate = false;
			$fonts          = include_once dirname( __FILE__ ) . '/google-fonts.php';
			foreach ( $fonts as $font => $extra ) {
				$google[] = array(
					'id'         => $font,
					'text'       => $font,
					'hasPreview' => $extra['has-preview'],
				);
				if ( ! $maybe_generate && ! $extra['has-preview'] ) {
					$maybe_generate = true;
					delete_transient( 'cyprus_options_googlefonts' );
				}
			}

			$this->parent->json['mtsGoogleFonts'] = array(
				'fonts'           => $google,
				'googleFonts'     => $fonts,
				'previewUrl'      => get_template_directory_uri() . '/includes/admin/libs/mts-options/img/fonts/',
				'generatePreview' => $maybe_generate,
			);
		}
	}
}
