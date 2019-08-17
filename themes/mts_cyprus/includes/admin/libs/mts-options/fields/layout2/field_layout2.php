<?php

/**
 * Alternative layout field ( blocks can have sub fields )
 *
 * Options Sorter Field for Redux Options
 * @author  Yannis - Pastis Glaros <mrpc@pramnoshosting.gr>
 * @url     http://www.pramhost.com
 * @license [http://www.gnu.org/copyleft/gpl.html GPLv3
 *
 * This is actually based on: [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Original Credits:
 * Author		: Syamil MJ
 * Author URI   	: http://aquagraphite.com
 * License		: GPLv3 - http://www.gnu.org/copyleft/gpl.html
 * Credits		: Thematic Options Panel - http://wptheming.com/2010/11/thematic-options-panel-v2/
 * KIA Thematic Options Panel - https://github.com/helgatheviking/thematic-options-KIA
 * Woo Themes - http://woothemes.com/
 * Option Tree - http://wordpress.org/extend/plugins/option-tree/
 * Twitter: http://twitter.com/syamilmj
 * Website: http://aquagraphite.com
 */
class MTS_Options_layout2 extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		$this->parent = $parent;
		$this->field = $field;
		if ( ! isset( $this->field['class'] ) ) {
			$this->field['class'] = '';
		}
		$this->value = $value;
		$this->args = $parent->args;
	}

	public function render() {

		if ( ! is_array( $this->value ) && isset( $this->field['options'] ) ) {
			$this->value = $this->field['options'];
		}

		// Make sure to get list of all the default blocks first
		$all_blocks = ! empty( $this->field['options'] ) ? $this->field['options'] : array();

		$temp = array(); // holds default blocks
		$temp2 = array(); // holds saved blocks

		foreach ( $all_blocks as $blocks ) {
			$temp = array_merge( $temp, $blocks );
		}

		$sortlists = $this->value;

		if ( is_array( $sortlists ) ) {
			foreach ( $sortlists as $sortlist ) {
				$temp2 = array_merge( $temp2, $sortlist );
			}

			// now let's compare if we have anything missing
			foreach ( $temp as $k => $v ) {
				if ( ! array_key_exists( $k, $temp2 ) ) {
					$sortlists['disabled'][ $k ] = $v;
				}
			}

			// now check if saved blocks has blocks not registered under default blocks
			foreach ( $sortlists as $key => $sortlist ) {
				foreach ( $sortlist as $k => $v ) {
					if ( ! array_key_exists( $k, $temp ) ) {
						unset( $sortlist[ $k ] );
					}
				}
				$sortlists[ $key ] = $sortlist;
			}

			// assuming all sync'ed, now get the correct naming for each block
			foreach ( $sortlists as $key => $sortlist ) {
				foreach ( $sortlist as $k => $v ) {
					$sortlist[ $k ] = $temp[ $k ];
				}
				$sortlists[ $key ] = $sortlist;
			}

			if ( $sortlists ) {
				echo '<fieldset id="' . $this->field['id'] . '" class="mts-opts-sorter-container-alt mts-opts-sorter-alt">';

				// go through field[options] and make sure they exist as a group
				foreach ( $all_blocks as $default_block => $val ) {
					if ( empty( $sortlists[ $default_block ] ) ) {
						$sortlists[ $default_block ] = array();
					}
				}

				foreach ( $sortlists as $group => $sortlist ) {
					$filled = '';

					if ( isset( $this->field['limits'][ $group ] ) && count( $sortlist ) >= $this->field['limits'][ $group ] ) {
						$filled = ' filled';
					}

					echo '<div id="' . $this->field['id'] . '_' . $group . '" class="sortlist-alt sortlist_' . $this->field['id'] . $filled . '" data-id="' . $this->field['id'] . '" data-group-id="' . $group . '">';
					echo '<h3>' . $group . '</h3>';

					foreach ( $sortlist as $key => $list ) {

						$has_content = isset( $list['subfields'] ) && ! empty( $list['subfields'] );
						$has_content_class = $has_content ? ' sortee-has-content' : '';

						echo '<div id="' . $key . '" class="sortee-alt' . $has_content_class . '">';
						echo '<input class="position-alt ' . $this->field['class'] . '" type="hidden" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][' . $group . '][' . $key . ']" value="' . $list['label'] . '">';
						echo '<div class="sortee-header">' . $list['label'] . '</div>';

						if ( $has_content ) {

							echo '<div class="sortee-content">';
							echo '<table style="margin-top: 0;" class="mts-opts-sortee-toggle-group mts-opts-group form-table no-border">';

							foreach ( $list['subfields'] as $field ) {
								//we will enqueue all CSS/JS for sub fields if it wasn't enqueued
								$this->enqueue_dependencies( $field['type'] );

								echo '<tr><td>';

								if ( ! empty( $field['title'] ) ) {
									echo '<h4>' . $field['title'] . '</h4>';
								}

								if ( ! empty( $field['sub_desc'] ) ) {
									echo '<span class="description">' . $field['sub_desc'] . '</span>';
								}

								// Had to pull option values this way
								$def_value = isset( $field['std'] ) ? $field['std'] : '';
								$value = cyprus_get_settings( $field['id'], $def_value );

								$field_type = $field['type'];

								$field_class = 'MTS_Options_' . $field_type;

								if ( ! class_exists( $field_class ) ) {

									$class_file = $this->dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

									if ( $class_file ) {
										/** @noinspection PhpIncludeInspection */
										require_once( $class_file );
									}
								}

								if ( class_exists( $field_class ) && method_exists( $field_class, 'render' ) ) {
									$render = new $field_class($field, $value, $this);
									$render->render();
								}

								echo '</td></tr>';
							}
							echo '</table>';
							echo '<a href="javascript:void(0);" class="button button-secondary mts-opts-sortee-toggle-close">' . __( 'OK', 'cyprus' ) . '</a>';

							echo '</div>';
						}
						echo '</div>';

					}

					echo '</div>';
				}
				echo '</fieldset>';
			}
		}
	}

	public function enqueue() {

		wp_enqueue_style( 'mts-opts-jquery-ui-css' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	public function enqueue_dependencies( $field_type ) {
		$field_class = 'MTS_Options_' . $field_type;

		if ( ! class_exists( $field_class ) ) {

			$dir = ! empty( $this->dir ) ? $this->dir : $this->parent->dir;
			$class_file = $dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

			if ( $class_file ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $class_file );
			}
		}

		if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
			$enqueue = new $field_class( '', '', $this );
			$enqueue->enqueue();
		}
	}
}
