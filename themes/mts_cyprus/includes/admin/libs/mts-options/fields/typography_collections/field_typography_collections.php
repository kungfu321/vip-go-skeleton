<?php
/**
 * Field: Typography Collections
 */
class MTS_Options_typography_collections extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->parent = $parent;
		$this->field  = $field;
		$defaults     = isset( $this->field['std'] ) ? $this->field['std'] : '';
		$this->value  = wp_parse_args( $value, $defaults );

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
		</td></tr></table>

		<div class="typography-collections" data-count="<?php echo count( $this->value ); ?>">

			<h2>
				<?php esc_html_e( 'Theme Typography', 'cyprus' ); ?>
				<a href="javascript:;" class="add-new-h2 typography-new-collection"><?php esc_html_e( 'Add New Collection', 'cyprus' ); ?></a>
				<?php if ( 1 == 2 && ! empty( $this->value ) ) : ?>
					<a href="javascript:;" class="add-new-h2 reset_collections"><?php esc_html_e( 'Reset Collections', 'cyprus' ); ?></a>
				<?php endif; ?>
			</h2>

			<?php
			if ( isset( $this->field['desc'] ) && ! empty( $this->field['desc'] ) ) {
				printf( '<div class="mts-opts-section-desc">%s</div>', $this->field['desc'] );
			}

			$count    = 0;
			$groups   = $this->value;
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
			);

			foreach ( $groups as $group ) {
				$value = wp_parse_args( $group, $defaults );
				$this->template( $value, $count );
				$count++;
			}

			$this->template( $defaults );
			?>

		</div>

		<table class="form-table no-border typography-character-sets"><tbody><tr><th></th><td>
		<?php
	}

	public function template( $value = array(), $count = '@' ) {

		$prefix = $this->args['opt_name'] . '[' . $this->field['id'] . '][' . $count . ']';

		?>
		<div class="typography-controls do-preview<?php echo '@' === $count ? ' collection-template' : ''; ?>">

			<div class="typography-preview">
				<div class="preview-text" contenteditable="true">
					The quick brown fox jumps over the lazy dog
				</div>
				<div class="typography-preview-color" title="<?php esc_attr_e( 'Preview Background Color', 'cyprus' ); ?>">
					<a href="javascript:;" class="light"></a>
					<a href="javascript:;" class="dark"></a>
				</div>
			</div>

			<div class="typography-controls-option clearfix">

				<button type="button" class="typography-remove-collection">Remove</button>

				<div class="control-wrapper">
					<label><?php esc_html_e( 'Font Family', 'cyprus' ); ?></label>
					<select class="typography-family" name="<?php echo $prefix; ?>[font-family]" data-placeholder="<?php echo esc_attr( $value['font-family'] ); ?>">
						<?php if ( ! empty( $value['font-family'] ) ) : ?>
						<option value="<?php echo esc_attr( $value['font-family'] ); ?>" selected="selected"><?php echo $value['font-family'];  ?></option>
						<?php endif; ?>
					</select>
				</div>

				<div class="control-wrapper">
					<label><?php esc_html_e( 'Backup Font Family', 'cyprus' ); ?></label>
					<select data-placeholder="<?php esc_html_e( 'Backup Font Family', 'cyprus' ); ?>" name="<?php echo $prefix; ?>[font-backup]" class="typography-family-backup">
						<option value=""></option>
						<?php foreach ( $this->std_fonts as $font ) : ?>
							<option value="<?php echo $font; ?>"<?php selected( $value['font-backup'], $font ); ?>><?php echo $font; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="control-wrapper">
					<label><?php esc_html_e( 'Font Weight &amp; Style', 'cyprus' ); ?></label>
					<input type="hidden" class="typography-font-weight" name="<?php echo $prefix; ?>[font-weight]" value="<?php echo esc_attr( $value['font-weight'] ); ?>">
					<input type="hidden" class="typography-font-style" name="<?php echo $prefix; ?>[font-style]" value="<?php echo esc_attr( $value['font-style'] ); ?>">
					<select data-placeholder="<?php esc_html_e( 'Style', 'cyprus' ); ?>" class="typography-variant">
						<option value=""></option>
						<option value="100" >Thin</option>
						<option value="100i" >Thin Italic</option>
						<option value="200" >Lighter</option>
						<option value="400" selected='selected'>Normal</option>
						<option value="700" >Bold</option>
						<option value="900" >Bolder</option>
					</select>
				</div>

				<div class="control-wrapper">
					<label><?php esc_html_e( 'Font Color', 'cyprus' ); ?></label>
					<input type="text" class="typography-color" name="<?php echo $prefix; ?>[color]" placeholder="<?php esc_html_e( 'Font Color', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['color'] ); ?>">
				</div>

				<div class="clearfix"></div>

				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Font Size', 'cyprus' ); ?></label>
					<input type="text" class="typography-font-size" name="<?php echo $prefix; ?>[font-size]" placeholder="<?php esc_html_e( 'Size', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['font-size'] ); ?>">
				</div>

				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Line Height', 'cyprus' ); ?></label>
					<input type="text" class="typography-line-height" name="<?php echo $prefix; ?>[line-height]" placeholder="<?php esc_html_e( 'Line Height', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['line-height'] ); ?>">
				</div>

				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Letter Spacing', 'cyprus' ); ?></label>
					<input type="text" class="typography-letter-spacing" name="<?php echo $prefix; ?>[letter-spacing]" placeholder="<?php esc_html_e( 'Letter Spacing', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['letter-spacing'] ); ?>">
				</div>

				<div class="clearfix"></div>

				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Margin Top', 'cyprus' ); ?></label>
					<input type="text" class="typography-margin-top" name="<?php echo $prefix; ?>[margin-top]" placeholder="<?php esc_html_e( 'Margin Top', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['margin-top'] ); ?>">
				</div>

				<div class="control-wrapper mini">
					<label><?php esc_html_e( 'Margin Bottom', 'cyprus' ); ?></label>
					<input type="text" class="typography-margin-bottom" name="<?php echo $prefix; ?>[margin-bottom]" placeholder="<?php esc_html_e( 'Margin Bottom', 'cyprus' ); ?>" value="<?php echo esc_attr( $value['margin-bottom'] ); ?>">
				</div>

				<div class="clearfix"></div>

				<div class="control-wrapper auto-height">
					<label><?php esc_html_e( 'CSS Selectors', 'cyprus' ); ?></label>
					<textarea class="typography-css-selectors" name="<?php echo $prefix; ?>[css-selectors]"><?php echo esc_textarea( $value['css-selectors'] ); ?></textarea>
				</div>

				<div class="control-wrapper auto-height">
					<label><?php esc_html_e( 'Additional CSS', 'cyprus' ); ?></label>
					<textarea class="typography-additional-css" name="<?php echo $prefix; ?>[additional-css]"><?php echo esc_textarea( $value['additional-css'] ); ?></textarea>
				</div>

				<div class="clearfix"></div>

			</div>

		</div>
		<?php
	}

	public function enqueue() {

		// Styles & Scripts for reused fields.
		$existing_fields = array( 'typography' );

		foreach ( $existing_fields as $key => $field_type ) {

			$field_class = 'MTS_Options_' . $field_type;

			if ( ! class_exists( $field_class ) ) {

				$class_file = $this->dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

				if ( $class_file ) {
					require_once $class_file;
				}
			}

			if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
				$enqueue = new $field_class( '', '', $this->parent );
				$enqueue->enqueue();
			}
		}
	}
}
