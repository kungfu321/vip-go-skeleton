<?php
/**
 * Field: Ace Editor
 */
class MTS_Options_ace_editor extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;

		if ( ! isset( $this->field['mode'] ) ) {
			$this->field['mode'] = 'javascript';
		}
		if ( ! isset( $this->field['theme'] ) ) {
			$this->field['theme'] = 'chrome';
		}
	}

	public function render() {

		$params = array(
			'minLines' => 20,
			'maxLines' => 30,
		);

		if ( isset( $this->field['args'] ) && ! empty( $this->field['args'] ) && is_array( $this->field['args'] ) ) {
			$params = wp_parse_args( $this->field['args'], $params );
		}

		$class = isset( $this->field['class'] ) ? $this->field['class'] : '';
		if( ! $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		?>
			<div class="ace-wrapper">
				<input type="hidden"
					class="localize_data"
					value="<?php echo htmlspecialchars( json_encode( $params ) ) ?>"
				/>
				<textarea name="<?php echo esc_attr( $this->args['opt_name'] . '[' . $this->field['id'] . ']' ) ?>" id="<?php echo esc_attr( $this->field['id'] ) ?>-textarea" class="ace-editor hidden <?php echo esc_attr( $class ) ?>" data-editor="<?php echo esc_attr( $this->field['id'] ) ?>-editor" data-mode="<?php echo esc_attr( $this->field['mode'] ) ?>" data-theme="<?php echo esc_attr( $this->field['theme'] ) ?>"><?php echo esc_textarea( $this->value ) ?></textarea>
				<pre id="<?php echo esc_attr( $this->field['id'] ) ?>-editor" class="ace-editor-area"><?php echo htmlspecialchars( $this->value ) ?></pre>
			</div>
		<?php

		$this->print_description( '<br />' );
	}

	/**
	 * Enqueue ace editor script.
	 */
	public function enqueue() {

		if ( ! wp_script_is( 'ace-editor' ) ) {
			wp_enqueue_script(
				'ace-editor',
				'//cdn.jsdelivr.net/ace/1.2.6/min/ace.js',
				array( 'jquery' ),
				'1.1.9',
				true
			);
		}
	}
}
