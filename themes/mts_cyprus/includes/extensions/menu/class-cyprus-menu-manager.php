<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once( 'class-cyprus-menu-walker.php' );

class Cyprus_Menu_Manager extends Cyprus_Base {

	/**
	 * Hold our custom fields
	 * @var array
	 */
	protected $fields = array();

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->add_filter( 'wp_setup_nav_menu_item', 'setup_nav_fields' );

		if ( is_admin() ) {
			$this->add_action( 'admin_enqueue_scripts', 'enqueue' );
			$this->add_action( 'wp_update_nav_menu_item', 'save_nav_fields', 10, 2 );
			$this->add_filter( 'wp_edit_nav_menu_walker', 'edit_nav_walker', 10, 2 );
			$this->add_action( 'cyprus_menu_item_custom_fields', 'add_nav_fields', 10, 4 );
		} else {
			$this->add_filter( 'nav_menu_link_attributes', 'add_color_attribute', 10, 2 );
			$this->add_filter( 'wp_nav_menu_items', 'add_extra_items', 10, 2 );
		}

		// Custom fields.
		$this->fields = array(
			array(
				'type'    => 'icon_select',
				'id'      => 'icon',
				'label'   => esc_html__( 'Icon (optional)', 'cyprus' ),
				'options' => cyprus_get_icons(),
			),

			array(
				'type'  => 'color',
				'id'    => 'color',
				'label' => esc_html__( 'Color (optional)', 'cyprus' ),
			),
		);
	}

	public function enqueue( $hook ) {

		if ( 'nav-menus.php' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'select2',
			$this->admin_uri() . 'libs/mts-options/css/select2.css'
		);
		wp_enqueue_script(
			'select2',
			$this->admin_uri() . 'libs/mts-options/js/select2.min.js',
			null,
			null,
			true
		);

		wp_enqueue_script(
			'wp-color-picker-alpha-js',
			$this->admin_uri() . 'libs/mts-options/fields/color/wp-color-picker-alpha.min.js',
			array( 'wp-color-picker' ),
			null,
			true
		);

		wp_enqueue_style(
			'font-awesome',
			get_parent_theme_file_uri() . '/css/font-awesome.min.css',
			array(),
			null,
			'all'
		);

		wp_enqueue_script( 'cyprus-menu', $this->admin_uri() . '/assets/js/cyprus-menu.js', null, null, true );
	}

	/**
	 * Print custom field in order to display on menu management screen
	 *
	 * @param int    $item_id    Nav menu ID.
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 */
	public function add_nav_fields( $item_id, $item, $depth, $args ) {

		foreach ( $this->fields as $field ) :
			$class = sprintf( 'field-%s', $field['id'] );
			$key   = sprintf( 'menu-item-%s', $field['id'] );
			$id    = sprintf( 'edit-%s-%s', $key, $item_id );
			$name  = sprintf( '%s[%s]', $key, $item_id );
			$value = get_post_meta( $item_id, $key, true );
		?>
		<p class="description description-wide <?php echo esc_attr( $class ); ?>">
			<label for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $field['label'] ); ?><br />
				<?php
				if ( method_exists( $this, $field['type'] ) ) {
					$this->{$field['type']}( $id, $name, $value, $field );
				}
				?>
			</label>
		</p>
		<?php
		endforeach;
	}

	/**
	 * Add custom fields data to $item nav object
	 * in order to be used in custom Walker
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */
	public function setup_nav_fields( $menu_item ) {

		foreach ( $this->fields as $field ) {
			$id = 'menu-item-' . $field['id'];

			$menu_item->{$field['id']} = get_post_meta( $menu_item->ID, $id, true );
		}

		return $menu_item;
	}

	/**
	 * Save menu custom fields
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	*/
	public function save_nav_fields( $menu_id, $menu_item_db_id ) {

		foreach ( $this->fields as $field ) {
			$id = 'menu-item-' . $field['id'];

			// Check if element is properly sent
			if ( ! empty( $_REQUEST[ $id ] ) && is_array( $_REQUEST[ $id ] ) ) {
				$value = $_REQUEST[ $id ][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, $id, $value );
			}
		}
	}

	/**
	 * Define new Walker edit
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	*/
	public function edit_nav_walker( $walker, $menu_id ) {

		if ( ! class_exists( 'cyprus_Walker_Nav_Menu' ) ) {
			include_once dirname( __FILE__ ) . '/class-cyprus-walker-edit.php';
		}

		return 'cyprus_Walker_Nav_Menu';
	}

	/**
	 * Add style attribute to link if nav item has color
	 * @param array $atts
	 * @param stdClass $item
	 */
	public function add_color_attribute( $atts, $item ) {

		if ( $item->color ) {
			$atts['style'] = 'color: ' . $item->color;
		}

		return $atts;
	}

	/**
	 * Add search icon to main navigation
	 * @param string $items
	 * @param stdClass $args
	 */
	public function add_extra_items( $items, $args ) {

		// If not main navigation bail early.
		if ( 'main_navigation' !== $args->theme_location ) {
			return $items;
		}

		if ( cyprus_get_settings( 'header_search_form' ) ) {
			$items .= '<li class="menu-item menu-item-search-form"><a href="#"><i class="fa fa-search" aria-hidden="true"></i></a>' . get_search_form( false ) . '</li>';
		}

		return $items;
	}

	// Fields ---------------------------------------------------

	public function select( $id, $name, $value = '', $field ) {
		?>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" style="width:100%">
			<?php foreach ( $field['options'] as $key => $option ) : ?>
				<?php if ( is_array( $option ) ) : ?>
					<optgroup label="<?php echo esc_html( $key ); ?>">
						<?php foreach ( $option as $i_key => $i_option ) : ?>
							<option value="<?php echo $i_key; ?>"<?php selected( $value, $i_key ); ?>><?php echo $i_option; ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php else : ?>
				<option value="<?php echo $key; ?>"<?php selected( $value, $key ); ?>><?php echo $option; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function icon_select( $id, $name, $value = '', $field ) {
		?>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" style="width:100%">
			<option value="">Select Icon</option>
			<?php foreach ( $field['options'] as $key => $option ) : ?>
				<?php if ( is_array( $option ) ) : ?>
					<optgroup label="<?php echo esc_html( $key ); ?>">
						<?php foreach ( $option as $i_option ) : ?>
							<option value="<?php echo $i_option; ?>"<?php selected( $value, $i_option ); ?>><?php echo $i_option; ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php else : ?>
				<option value="<?php echo $option; ?>"<?php selected( $value, $option ); ?>><?php echo $option; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function color( $id, $name, $value = '', $field ) {
		?>
		<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat color-picker" type="text" value="<?php echo $value; ?>" <?php if ( isset( $field['alpha'] ) ) { ?> data-alpha="true" <?php } ?>>
		<?php
	}

	public function text( $id, $name, $value = '', $field ) {
		?>
		<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat" value="<?php echo $value; ?>" />
		<?php
	}
}

new cyprus_Menu_Manager;
