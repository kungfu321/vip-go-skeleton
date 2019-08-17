<?php
/**
 * A collection of sanitization methods.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * A collection of sanitization methods.
 */
class Cyprus_Sanitize {

	/**
	 * Sanitize values for background field.
	 *
	 * @param   array $value The value to sanitize.
	 * @return  array
	 */
	public static function background( $value ) {

		$bg = array();
		if ( isset( $value['use'] ) && 'pattern' === $value['use'] ) {

			if ( '' !== $value['color'] ) {
				$bg[] = $value['color'];
			}

			if ( 'nobg' !== $value['image_pattern'] ) {
				$bg[] = sprintf( 'url(%s)', get_parent_theme_file_uri() . '/images/patterns/' . $value['image_pattern'] . '.png' );
			}

			return array(
				'background' => join( ' ', $bg ),
			);
		}

		if ( isset( $value['use'] ) && 'upload' === $value['use'] ) {

			if ( '' !== $value['color'] ) {
				$bg[] = self::color( $value['color'] );
			}

			if ( '' !== $value['image_upload'] ) {
				$bg[] = sprintf( 'url(%s)', $value['image_upload'] );
			}

			if ( '' !== $value['repeat'] ) {
				$bg[] = $value['repeat'];
			}

			if ( '' !== $value['attachment'] ) {
				$bg[] = $value['attachment'];
			}

			if ( '' !== $value['position'] ) {
				$bg[] = $value['position'];
			}

			$bg = array(
				'background' => join( ' ', $bg ),
			);

			if ( '' !== $value['size'] ) {
				$bg['background-size'] = $value['size'];
			}

			return $bg;
		}

		if ( isset( $value['use'] ) && 'gradient' === $value['use'] ) {

			$value = $value['gradient'];

			if ( '' === $value['from'] || '' === $value['to'] ) {
				return '';
			}

			return array(
				'background' => array( sprintf( 'linear-gradient(%ddeg, %s, %s) fixed', $value['direction'], $value['from'], $value['to'] ) ),
			);
		}
	}

	/**
	 * Sanitize values for border field.
	 *
	 * @param   array $value The value to sanitize.
	 * @return  array
	 */
	public static function border( $value ) {
		$parts = array();
		if ( isset( $value['size'] ) ) {
			$parts[] = self::size( $value['size'], 'px' );
		}

		if ( isset( $value['style'] ) ) {
			$parts[] = $value['style'];
		}

		if ( isset( $value['color'] ) ) {
			$parts[] = self::color( $value['color'] );
		}

		return array(
			//'direction' => 'all' === $value['direction'] ? 'border' : 'border-' . $value['direction'],
			'direction' => ! isset( $value['direction'] ) || 'all' === $value['direction'] ? 'border' : 'border-' . $value['direction'],
			'value'     => join( ' ', $parts ),
		);
	}

	/**
	 * Sanitize values for padding.
	 *
	 * @param  array $value The value to sanitize.
	 * @return string
	 */
	public static function padding( $value ) {
		return self::padding_margin( $value, 'padding' );
	}

	/**
	 * Sanitize values for margin.
	 *
	 * @param  array $value The value to sanitize.
	 * @return string
	 */
	public static function margin( $value ) {
		return self::padding_margin( $value, 'margin' );
	}

	/**
	 * Sanitize values for margin/padding
	 *
	 * @param  array  $value    The value to sanitize.
	 * @param  string $property Proprty type.
	 * @return string
	 */
	public static function padding_margin( $value, $property = 'padding' ) {
		$new_value = array();
		foreach ( (array) $value as $key => $item ) {
			$new_value[ $property . '-' . $key ] = Cyprus_Sanitize::size( $item, 'px' );
		}

		return $new_value;
	}

	/**
	 * Sanitize values like for example 10px, 30% etc.
	 *
	 * @param  string $value The value to sanitize.
	 * @param  string $unit  The unit for value.
	 * @return string
	 */
	public static function size( $value, $unit = '' ) {

		// Trim the value.
		$value = trim( $value );

		if ( in_array( $value, array( 'auto', 'inherit', 'initial' ) ) ) {
			return $value;
		}

		// Return empty if there are no numbers in the value.
		// Prevents some CSS errors.
		if ( ! preg_match( '#[0-9]#', $value ) ) {
			return;
		}

		return self::number( $value ) . self::get_unit( $value, $unit );
	}

	/**
	 * Return the unit of a given value.
	 *
	 * @param  string $value     A value with unit.
	 * @param  string $unit_used Default unit.
	 * @return string The unit of the given value.
	 */
	public static function get_unit( $value, $unit_used = '' ) {

		// Trim the value.
		$value = trim( $value );

		// The array of valid units.
		$units = array( 'px', 'rem', 'em', '%', 'vmin', 'vmax', 'vh', 'vw', 'ex', 'cm', 'mm', 'in', 'pt', 'pc', 'ch' );

		foreach ( $units as $unit ) {

			// Find what unit we're using.
			if ( false !== strpos( $value, $unit ) ) {
				$unit_used = $unit;
				break;
			}
		}

		return $unit_used;
	}

	/**
	 * Sanitizes a number value.
	 *
	 * @param string|int|float $value The value to sanitize.
	 * @return float|int
	 */
	public static function number( $value ) {
		return filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}

	/**
	 * Sanitize colors.
	 * Determine if the current value is a hex or an rgba color and call the appropriate method.
	 *
	 * @param  string $value   string  hex or rgba color.
	 *
	 * @return string
	 */
	public static function color( $value ) {

		if ( false === $value || '' === $value ) {
			return $value;
		}

		if ( ! class_exists( 'cyprus_Color' ) ) {
			include_once cyprus()->includes_path() . 'class-cyprus-color.php';
		}

		$color_obj = cyprus_Color::new_color( $value );
		$mode      = ( is_array( $value ) ) ? 'rgba' : $color_obj->mode;

		return $color_obj->to_css( $mode );
	}

	/**
	 * Santizie typography.
	 *
	 * @param  array $value The value to sanitize.
	 * @return array
	 */
	public static function typography( $value ) {

		$font = array();

		if ( empty( $value['font-family'] ) ) {
			return $font;
		}

		if ( ! empty( $value['font-family'] ) ) {
			$font['font-family'] = "'" . $value['font-family'] . "'" . ( ! empty( $value['font-backup'] ) ? ', ' . $value['font-backup'] : '' );
		}

		if ( ! empty( $value['color'] ) ) {
			$font['color'] = self::color( $value['color'] );
		}

		$empty = array( 'font-weight', 'font-style', 'css-selectors', 'additional-css' );
		foreach ( $empty as $prop ) {
			if ( ! empty( $value[ $prop ] ) ) {
				$font[ $prop ] = $value[ $prop ];
			}
		}

		$size = array( 'font-size', 'letter-spacing', 'margin-top', 'margin-bottom' );
		foreach ( $size as $prop ) {
			if ( ! empty( $value[ $prop ] ) ) {
				$font[ $prop ] = self::size( $value[ $prop ], 'px' );
			}
		}

		if ( ! empty( $value['line-height'] ) ) {
			$font['line-height'] = self::size( $value['line-height'] );
		}

		return $font;
	}

	/**
	 * Generate calc string for css
	 *
	 * @param  array  $values An array of CSS values.
	 * @param  string $sep    Seperator.
	 * @return string The combined value.
	 */
	public static function calc_css( $values, $sep = '' ) {

		if ( ! is_array( $values ) || empty( $values ) ) {
			return '0';
		}

		$sep = '' !== $sep ? " $sep " : ' ';

		return 'calc(' . join( $sep, $values ) . ')';
	}
}
