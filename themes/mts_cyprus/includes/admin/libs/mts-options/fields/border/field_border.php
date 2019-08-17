<?php

class MTS_Options_border extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );

		$this->field = $field;
		$this->value = $value;
	}

	public function render() {
		$options = array(
			'none'   => 'None',
			'solid'  => 'Solid',
			'dashed' => 'Dashed',
			'dotted' => 'Dotted',
			'double' => 'Double',
		);
		$name    = $this->args['opt_name'] . '[' . $this->field['id'] . ']';

		$defaults  = array(
			'color'     => '',
			'size'      => '',
			'style'     => 'none',
			'direction' => 'all',
		);
		$direction = array(
			'all'    => 'All',
			'top'    => 'Top',
			'right'  => 'Right',
			'bottom' => 'Bottom',
			'left'   => 'Left',
		);

		$defaults    = isset( $this->field['std'] ) ? wp_parse_args( $this->field['std'], $defaults ) : $defaults;
		$this->value = wp_parse_args( $this->value, $defaults );
		?>
		<div id="border-<?php echo $this->field['id']; ?>">
			<input type="text" id="<?php echo $this->field['id']; ?>" name="<?php echo $name; ?>[color]" value="<?php echo $this->value['color']; ?>" class="popup-colorpicker" data-default-color=""<?php echo isset( $this->field['args']['opacity'] ) ? 'data-alpha="true"' : ''; ?>/>
			<select name="<?php echo $name; ?>[direction]">
				<?php
				foreach ( $direction as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['direction'], $k, false ) . '>' . $v . '</option>';
				}
				?>
			</select>
			<input type="number" name="<?php echo $name; ?>[size]" value="<?php echo $this->value['size']; ?>">
			<select name="<?php echo $name; ?>[style]">
				<?php
				foreach ( $options as $k => $v ) {
					echo '<option value="' . $k . '"' . selected( $this->value['style'], $k, false ) . '>' . $v . '</option>';
				}
				?>
			</select>
		</div>
		<?php
	}

	public function enqueue() {

		wp_enqueue_style( 'wp-color-picker' );

		if ( ! wp_script_is( 'wp-color-picker-alpha-js' ) ) {
			wp_enqueue_script(
				'wp-color-picker-alpha-js',
				$this->url . 'fields/color/wp-color-picker-alpha.min.js',
				array( 'wp-color-picker' ),
				cyprus()->get_version(),
				true
			);
		}
	}
}
