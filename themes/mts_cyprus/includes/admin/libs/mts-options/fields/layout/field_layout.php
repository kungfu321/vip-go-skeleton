<?php
/**
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
class MTS_Options_layout extends MTS_Options {

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
				echo '<fieldset id="' . $this->field['id'] . '" class="mts-opts-sorter-container mts-opts-sorter">';

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

					echo '<ul id="' . $this->field['id'] . '_' . $group . '" class="sortlist_' . $this->field['id'] . $filled . '" data-id="' . $this->field['id'] . '" data-group-id="' . $group . '">';
					echo '<h3>' . $group . '</h3>';

					foreach ( $sortlist as $key => $list ) {
						echo '<li id="' . $key . '" class="sortee">';
						echo '<input class="position ' . $this->field['class'] . '" type="hidden" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][' . $group . '][' . $key . ']" value="' . $list . '">';
						echo $list;
						echo '</li>';
					}
					echo '</ul>';
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
}
