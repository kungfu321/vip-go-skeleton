<?php
/**
 * Field: Margin
 */
class MTS_Options_margin extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = wp_parse_args( $field, array(
			'top'    => true,
			'right'  => true,
			'bottom' => true,
			'left'   => true,
		));
		$this->value = $value;
	}

	public function render() {

		$defaults = array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
		);

		$defaults = isset( $this->field['std'] ) ? wp_parse_args( $this->field['std'], $defaults ) : $defaults;
		$this->value = wp_parse_args( $this->value, $defaults );

		$sides = array(
			'top' => array(
				'title' => esc_html__( 'Top', 'cyprus' ),
				'icon' => 'dashicons dashicons-arrow-up-alt',
			),
			'right' => array(
				'title' => esc_html__( 'Right', 'cyprus' ),
				'icon' => 'dashicons dashicons-arrow-right-alt',
			),
			'bottom' => array(
				'title' => esc_html__( 'Bottom', 'cyprus' ),
				'icon' => 'dashicons dashicons-arrow-down-alt',
			),
			'left' => array(
				'title' => esc_html__( 'Left', 'cyprus' ),
				'icon' => 'dashicons dashicons-arrow-left-alt',
			),
		);

		echo '<div class="nhpoptions-margin" id="' . $this->field['id'] . '">';

		foreach ( $sides as $id => $val ) {

			if ( ! $this->field[ $id ] ) {
				continue;
			}

			printf(
				'<div><span class="mg-side">%1$s</span><span class="%2$s"></span><input type="text" id="%3$s-%4$s" name="%5$s[%3$s][%4$s]" value="%6$s"></div>',
				$val['title'],
				$val['icon'],
				$this->field['id'],
				$id,
				$this->args['opt_name'],
				esc_attr( $this->value[ $id ] )
			);
		}

		echo '</div>';

		$this->print_description();
	}
}
