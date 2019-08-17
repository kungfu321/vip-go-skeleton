<?php
class MTS_Options_icon_select extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
		$this->icons = cyprus_get_icons();
	}

	public function render() {

		$class = 'class="mts-opts-iconselect ' . ( isset( $this->field['class'] ) ? $this->field['class'] : '' ) . '"';

		if ( ! empty( $this->field['subset'] ) && isset( $this->icons[ $this->field['subset'] ] ) ) {
			$subset = $this->field['subset'];
			$this->icons = array( $this->icons[ $subset ] );
		}

		// Allow empty
		$allow_empty = true;
		if ( isset( $this->field['allow_empty'] ) && false === $this->field['allow_empty'] ) {
			$allow_empty = false;
		}

		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" ' . $class . ' style="width: 100%; max-width: 240px;">';

		if ( $allow_empty ) {
			echo '<option value=""' . selected( $this->value, '0', false ) . '>' . esc_html__( 'No Icon', 'cyprus' ) . '</option>';
		}

		foreach ( $this->icons as $icon_category => $icons ) {

			if ( ! isset( $subset ) ) {
				echo '<optgroup label="' . $icon_category . '">';
			}

			foreach ( $icons as $icon ) {
				echo '<option value="' . $icon . '"' . selected( $this->value, $icon, false ) . '>' . ucwords( str_replace( '-', ' ', $icon ) ) . '</option>';
			}
			if ( ! isset( $subset ) ) {
				echo '</optgroup>';
			}
		}

		echo '</select>';

		$this->print_description();
	}

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 */
	public function enqueue() {
		wp_enqueue_script(
			'select2',
			$this->url . 'js/select2.min.js',
			array( 'jquery' ),
			cyprus()->get_version(),
			true
		);

		wp_enqueue_style(
			'select2',
			$this->url . 'css/select2.css',
			array(),
			cyprus()->get_version(),
			'all'
		);
	}
}
