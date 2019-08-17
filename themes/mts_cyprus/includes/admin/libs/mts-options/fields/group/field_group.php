<?php
/**
 * Group field type. Ported from Redux.
 * Can only group simple fields like select, input, etc. - without JS
 */
class MTS_Options_group extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->parent = $parent;
		$this->field  = $field;
		$this->value  = $value;
	}

	public function render() {

		if ( empty( $this->value ) || ! is_array( $this->value ) ) {
			$this->value = array(
				array(
					'group_sort'  => '0',
					'group_title' => __( 'New', 'cyprus' ) . ' ' . $this->field['groupname'],
				),
			);
		}
		$groups = $this->value;

		$class = isset( $this->field['class'] ) ? 'class="' . $this->field['class'] . ' ' : '';

		echo '<div class="nhpoptions-group">';
		echo '<input type="hidden" class="mts-opts-dummy-group-count" id="mts-opts-dummy-' . $this->field['id'] . '-count" name="mts-opts-dummy-' . $this->field['id'] . '-count" value="' . count( $groups ) . '" />';
		echo '<div id="mts-opts-groups-accordion">';

		// Create dummy content for the adding new ones.
		echo '<div class="mts-opts-groups-accordion-group mts-opts-dummy" style="display:none" id="mts-opts-dummy-' . $this->field['id'] . '"><h3><span class="mts-opts-groups-header">' . __( 'New ', 'cyprus' ) . $this->field['groupname'] . '</span></h3>';
		echo '<div>'; // according content open.

		echo '<table style="margin-top: 0;" class="mts-opts-groups-accordion mts-opts-group form-table no-border">';
		echo '<fieldset><input type="hidden" id="' . $this->field['id'] . '_group-title" data-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][group_title]" value="" class="regular-text group-title" /></fieldset>';
		echo '<input type="hidden" class="group-sort" data-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][group_sort]" id="' . $this->field['id'] . '-group_sort" value="" />';

		$x              = 0;
		$the_id         = $this->field['id'];
		$field_is_title = true;

		foreach ( $this->field['subfields'] as $field ) {
			$class = '';
			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
			}

			// We will enqueue all CSS/JS for sub fields if it wasn't enqueued.
			$this->enqueue_dependencies( $field['type'] );

			echo "<tr{$class}><td>";

			if ( isset( $field['class'] ) ) {
				$field['class'] .= ' group';
			} else {
				$field['class'] = ' group';
			}

			if ( ! empty( $field['title'] ) ) {
				echo '<h4>' . $field['title'] . '</h4>';
			}

			if ( ! empty( $field['sub_desc'] ) ) {
				echo '<span class="description">' . $field['sub_desc'] . '</span>';
			}

			$value = empty( $this->options[ $field['id'] ][0] ) ? '' : $this->options[ $field['id'] ][0];

			ob_start();
			$val     = $this->_field_input( $field, $the_id, $x );
			$content = ob_get_contents();

			// Adding sorting number to the name of each fields in group.
			$name    = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
			$content = str_replace( $name, $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][@][' . $field['id'] . ']', $content );

			// Remove the name property. asigned by the controller, create new data-name property for js.
			$content = str_replace( 'name=', 'data-id="' . $field['id'] . '" data-name=', $content );

			if ( 'text' === $field['type'] && $field_is_title ) {
				$field_is_title = false;
				$content        = str_replace( 'value=""', 'value="' . ( isset( $field['value'] ) ? $field['value'] : '' ) . '"', $content );
			}

			// We should add $sort to id to fix problem with select field.
			$content = str_replace( ' id="' . $field['id'] . '-select"', ' id="' . $field['id'] . '-select-dummy"', $content );

			// Add $sort to id to fix problem with radio field.
			if ( 'radio' === $field['type'] || 'radio_img' === $field['type'] ) {
				$content = str_replace( 'label for="' . $field['id'], 'label for="' . $field['id'] . '_dummy', $content );
				$content = str_replace( 'type="radio" id="' . $field['id'], 'type="radio" id="' . $field['id'] . '_dummy', $content );
			}

			// Add $sort to id to fix problem with upload field.
			$content = str_replace( 'type="hidden" id="' . $field['id'] . '"', 'type="hidden" id="' . $field['id'] . '-dummy-' . $x . '"', $content );
			$content = str_replace( 'rel-id="' . $field['id'] . '"', 'rel-id="' . $field['id'] . '-dummy-' . $x . '"', $content );

			$_field = $content;

			ob_end_clean();
			echo $_field;

			echo '</td></tr>';
		}

		echo '</table>';
		echo '<a href="javascript:void(0);" class="button button-secondary mts-opts-groups-close">' . __( 'OK', 'cyprus' ) . '</a>';
		echo '<a href="javascript:void(0);" class="button deletion mts-opts-groups-remove">' . __( 'Delete', 'cyprus' ) . ' ' . $this->field['groupname'] . '</a>';
		echo '</div></div>';

		// Create real groups.
		$x = 0;
		// Check if only default fields are present, don't display that.
		if ( empty( $groups[0] ) || count( $groups[0] ) > 2 ) {
			foreach ( $groups as $k => $group ) {
				echo '<div class="mts-opts-groups-accordion-group"><h3><span class="mts-opts-groups-header">' . $group['group_title'] . '</span></h3>';

				// According content open.
				echo '<div>';

				echo '<table style="margin-top: 0;" class="mts-opts-groups-accordion mts-opts-group form-table no-border">';

				echo '<fieldset><input type="hidden" id="' . $this->field['id'] . '-group_title_' . $x . '" name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][' . $x . '][group_title]" value="' . esc_attr( $group['group_title'] ) . '" class="regular-text group-title" /></fieldset>';

				echo '<input type="hidden" class="group-sort" name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][' . $x . '][group_sort]" id="' . $this->field['id'] . '-group_sort_' . $x . '" value="' . $group['group_sort'] . '" />';

				$field_is_title = true;

				foreach ( $this->field['subfields'] as $field ) {
					$class = '';
					if ( ! empty( $field['args']['class'] ) ) {
						$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
					}

					// We will enqueue all CSS/JS for sub fields if it wasn't enqueued.
					$this->enqueue_dependencies( $field['type'] );

					echo "<tr{$class}><td>";

					if ( isset( $field['class'] ) ) {
						$field['class'] .= ' group';
					} else {
						$field['class'] = ' group';
					}

					if ( ! empty( $field['title'] ) ) {
						echo '<h4>' . $field['title'] . '</h4>';
					}

					if ( ! empty( $field['sub_desc'] ) ) {
						echo '<span class="description">' . $field['sub_desc'] . '</span>';
					}

					if ( isset( $group[ $field['id'] ] ) && ! empty( $group[ $field['id'] ] ) ) {
						$value = $group[ $field['id'] ];
					}

					$value = empty( $value ) ? '' : $value;

					ob_start();
					$this->_field_input( $field, $the_id, $k );
					$content = ob_get_contents();

					// Adding sorting number to the name of each fields in group.
					$name    = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
					$content = str_replace( $name, $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][' . $x . '][' . $field['id'] . ']', $content );

					$content = str_replace( 'name=', 'data-id="' . $field['id'] . '" name=', $content );

					// We should add $sort to id to fix problem with select field.
					$content = str_replace( ' id="' . $field['id'] . '-select"', ' id="' . $field['id'] . '-select-' . $x . '"', $content );

					if ( 'radio' === $field['type'] || 'radio_img' === $field['type'] ) {
						$content = str_replace( 'label for="' . $field['id'], 'label for="' . $field['id'] . '_' . $x, $content );
						$content = str_replace( 'type="radio" id="' . $field['id'], 'type="radio" id="' . $field['id'] . '_' . $x, $content );
					}

					// Add $sort to id to fix problem with upload field.
					$content = str_replace( 'type="hidden" id="' . $field['id'] . '"', 'type="hidden" id="' . $field['id'] . '-' . $x . '"', $content );
					$content = str_replace( 'rel-id="' . $field['id'] . '"', 'rel-id="' . $field['id'] . '-' . $x . '"', $content );

					if ( 'text' === $field['type'] && $field_is_title ) {
						$content = str_replace( 'value=""', 'value="' . ( isset( $field['value'] ) ? $field['value'] : '' ) . '"', $content );
						$field_is_title = false;
					}

					$_field = $content;

					ob_end_clean();

					echo $_field;

					echo '</td></tr>';
				}

				echo '</table>';
				echo '<a href="javascript:void(0);" class="button button-secondary mts-opts-groups-close">' . __( 'OK', 'cyprus' ) . '</a>';
				echo '<a href="javascript:void(0);" class="button deletion mts-opts-groups-remove">' . __( 'Delete', 'cyprus' ) . ' ' . $this->field['groupname'] . '</a>';
				echo '</div></div>';
				$x++;
			}
		}

		echo '</div><a href="javascript:void(0);" class="button mts-opts-groups-add button-secondary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][group_title][]">' . __( 'Add', 'cyprus' ) . ' ' . $this->field['groupname'] . '</a><br/>';
		echo '</div>';

	}

	// From Redux
	// Not sure what this is for.
	public function support_multi( $content, $field, $sort ) {

		// Convert name.
		$name = $this->parent->args['opt_name'] . '[' . $field['id'] . ']';
		$content = str_replace( $name, $name . '[' . $sort . ']', $content );

		// We should add $sort to id to fix problem with select field.
		$content = str_replace( ' id="' . $field['id'] . '-select"', ' id="' . $field['id'] . '-select-' . $sort . '"', $content );

		return $content;
	}

	public function enqueue() {

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	public function enqueue_dependencies( $field_type ) {

		$field_class = 'MTS_Options_' . $field_type;

		if ( ! class_exists( $field_class ) ) {

			$class_file = $this->dir . 'fields/' . $field_type . '/field_' . $field_type . '.php';

			if ( $class_file ) {
				require_once( $class_file );
			}
		}

		if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
			$enqueue = new $field_class( '', '', $this );
			$enqueue->enqueue();
		}
	}
}
