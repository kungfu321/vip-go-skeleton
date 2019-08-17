<?php
class MTS_Options_upload extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent = '' ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
		$this->return_field = isset( $this->field['return'] ) && 'id' === $this->field['return'] ? 'id' : 'url';
		$this->parent = $parent;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : 'regular-text';

		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}

		echo '<input type="hidden" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $this->value . '" class="' . $class . '" />';
		if ( 'id' === $this->return_field ) {
			$img_url = wp_get_attachment_url( $this->value );
			echo '<img class="mts-opts-screenshot" id="mts-opts-screenshot-' . $this->field['id'] . '" src="' . $img_url . '" data-return="id" />';
		} else {
			echo '<img class="mts-opts-screenshot" id="mts-opts-screenshot-' . $this->field['id'] . '" src="' . $this->value . '" data-return="url" />';
		}

		if ( '' === $this->value ) {
			$remove = ' style="display:none;"';$upload = '';
		} else {
			$remove = '';$upload = ' style="display:none;"';
		}
		echo ' <a href="javascript:void(0);" class="mts-opts-upload button button-secondary"' . $upload . ' rel-id="' . $this->field['id'] . '">' . __( 'Browse', 'cyprus' ) . '</a>';
		echo ' <a href="javascript:void(0);" class="mts-opts-upload-remove button"' . $remove . ' rel-id="' . $this->field['id'] . '">' . __( 'Remove Upload', 'cyprus' ) . '</a>';

		$this->print_description( '<br /><br />' );
	}

	public function enqueue() {

		wp_enqueue_media();

		if ( ! isset( $this->parent->json['uploadUrl'] ) ) {
			$this->parent->json['uploadUrl'] = $this->url . 'fields/upload/blank.png';
			$this->parent->json['uploadTitle'] = __( 'Select Image', 'cyprus' );
			$this->parent->json['uploadButtonText'] = __( 'Select Image', 'cyprus' );
		}
	}

}
