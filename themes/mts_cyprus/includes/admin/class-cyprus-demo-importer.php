<?php
/**
 * Theme demo importer
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Demo Importer
 */
class Cyprus_Demo_Importer extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {
		$this->add_action( 'admin_menu', 'register_page', 25 );
		$this->add_action( 'wp_ajax_cyprus_import_demo', 'import_demo' );
		$this->add_action( 'wp_ajax_cyprus_import_code', 'import_code' );
		$this->add_action( 'wp_ajax_cyprus_create_child_theme', 'create_child_theme' );
	}

	/**
	 * Register importer page
	 */
	public function register_page() {
		add_submenu_page( 'cyprus-options', esc_html__( 'Import / Export', 'cyprus' ), esc_html__( 'Import / Export', 'cyprus' ), 'manage_options', 'cyprus_demos', array( $this, 'render' ) );
	}

	/**
	 * Render page
	 */
	public function render() {
		$theme_version = wp_get_theme()->version;
		$thumbnail_url = cyprus()->admin_uri( 'demo/%1$s/%1$s_thumbnail.jpg' );
		$url           = add_query_arg(
			array(
				'feed'   => 'mts_download_options',
				'secret' => md5( AUTH_KEY . SECURE_AUTH_KEY ),
				'action' => 'download_options',
			),
			site_url()
		);
		?>
		<div class="wrap">

			<div style="overflow: hidden">

				<div style="float:left;width:45%;margin-right:5%">
					<h1><?php esc_html_e( 'Import / Export Settings', 'cyprus' ); ?></h1>

					<?php include_once trailingslashit( dirname( __FILE__ ) ) . 'libs/mts-options/templates/import-export.php'; ?>
				</div>

				<div style="float:left;width:49%">
					<h1><?php esc_html_e( 'Child Theme Generator', 'cyprus' ); ?></h1>

					<div class="mts-opts-field-wrapper" style="margin-top:10px;">
						<input id="child-theme-name" class="regular-text">
						<input type="submit" id="mts-create-child-theme" class="button-primary" value="<?php esc_attr_e( 'Create', 'cyprus' ); ?>" data-import-confirm="<?php echo esc_html__( 'Are you sure you want to import options? All current options will be lost.', 'cyprus' ); ?>">
					</div>

				</div>

			</div>

			<div class="demo-list">
				<h2><?php esc_html_e( 'Demo Importer', 'cyprus' ); ?></h2>
				<?php foreach ( $this->get_demos() as $id => $demo_details ) : ?>
					<?php
					// Make sure we don't show demos that can't be applied to this version.
					if ( isset( $demo_details['min_version'] ) ) {
						if ( version_compare( $theme_version, $demo_details['min_version'] ) < 0 ) {
							continue;
						}
					}
					?>
					<div class="demo-list--item">

						<div class="demo-list--item-wrapper">

							<h3><?php echo $demo_details['name']; ?></h3>

							<a target="_blank" href="<?php echo $demo_details['preview_url']; ?>" class="demo-list--item-screenshot">
								<img src="<?php printf( $thumbnail_url, $id ); ?>">
							</a>

							<div class="demo-list--item-actions wp-core-ui">
								<a class="button button-primary" href="#" data-demo-id="<?php echo $id; ?>"><?php esc_html_e( 'Install', 'cyprus' ); ?> <span class="dashicons dashicons-arrow-down-alt2"></span></a>
								<a class="button button-secondary" target="_blank" href="<?php echo $demo_details['preview_url']; ?>"><?php esc_html_e( 'Preview', 'cyprus' ); ?></a>
							</div>

						</div>

					</div>
				<?php endforeach; ?>
			</div>

			<div class="demo-importer-content-list" data-security=<?php echo wp_create_nonce( 'cyprus-demo-importer' ); ?>>
				<h4><span id="selected-demo-title"></span></h4>
				<h5><?php esc_html_e( 'Content to import:', 'cyprus' ); ?></h5>
				<ul>
					<li><input type="checkbox" name="content[]" id="importer-content" value="content" checked="checked"> <label for="importer-content"><?php esc_html_e( 'Content', 'cyprus' ); ?></label></li>
					<li><input type="checkbox" name="content[]" id="importer-options" value="options" checked="checked"> <label for="importer-options"><?php esc_html_e( 'Theme Options', 'cyprus' ); ?></label></li>
					<li><input type="checkbox" name="content[]" id="importer-widgets" value="widgets" checked="checked"> <label for="importer-widgets"><?php esc_html_e( 'Widgets', 'cyprus' ); ?></label></li>
					<li><button class="button button-primary" data-import-confirm="<?php echo esc_html__( 'Are you sure you want to import?', 'cyprus' ); ?>"><?php esc_html_e( 'Start importing', 'cyprus' ); ?></button></li>
				</ul>
			</div>

		</div>
		<?php
	}

	/**
	 * Create child theme.
	 */
	public function create_child_theme() {

		// Return if this is child theme
		if ( is_child_theme() ) {
			$this->error( 'You are already using a child theme.' );
		}

		$child_name = stripslashes( $_POST['themeName'] );
		if ( empty( $child_name ) ) {
			$this->error( 'No child theme name entered.' );
		}

		$wp_filesystem         = cyprus_init_filesystem();
		$parent_theme          = wp_get_theme();
		$parent_stylesheet     = $parent_theme->get_stylesheet();
		$child_theme_directory = trailingslashit( get_theme_root() ) . sanitize_file_name( strtolower( $child_name ) );

		if ( $wp_filesystem->is_dir( $child_theme_directory ) ) {
			$this->error( 'Child theme with same name already exists.' );
		}

		$wp_filesystem->mkdir( $child_theme_directory );
		$child_stylesheet = trailingslashit( $child_theme_directory ) . 'style.css';
		ob_start();
		require __DIR__ . '/child-theme/stylesheet.php';
		$child_stylesheet_contents = ob_get_clean();
		$wp_filesystem->put_contents( $child_stylesheet, $child_stylesheet_contents );
		$wp_filesystem->copy(
			__DIR__ . '/child-theme/functions.php',
			trailingslashit( $child_theme_directory ) . 'functions.php'
		);
		$wp_filesystem->copy(
			__DIR__ . '/child-theme/screenshot.png',
			trailingslashit( $child_theme_directory ) . 'screenshot.png'
		);

		$this->success( 'Child theme created.' );
	}

	/**
	 * Get demos
	 *
	 * @return array
	 */
	private function get_demos() {
		$demos = array();

		$demos['default'] = array(
			'name'        => esc_html__( 'Cyprus Default', 'cyprus' ),
			'preview_url' => 'https://demo.mythemeshop.com/cyprus/',
			'min_version' => '1.0.0',
			'menus'       => array(
				'secondary-menu' => 'Main Menu',
				'footer-menu'    => 'Footer',
			),
		);

		$demos['blog'] = array(
			'name'        => esc_html__( 'Cyprus Blog', 'cyprus' ),
			'preview_url' => 'https://demo.mythemeshop.com/cyprus-traditional/',
			'min_version' => '1.0.0',
			'menus'       => array(
				'secondary-menu' => 'Main Menu',
				'footer-menu'    => 'Footer',
			),
		);

		return $demos;
	}

	/**
	 * Import demo
	 */
	public function import_demo() {
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'cyprus-demo-importer' ) ) {
			$this->error( esc_html__( 'You are not authorized to perform this action.', 'cyprus' ) );
		}

		$perform = isset( $_POST['perform'] ) ? $_POST['perform'] : false;
		if ( ! $perform || ! in_array( $perform, array( 'options', 'content', 'widgets' ) ) ) {
			$this->error( esc_html__( 'Action not allowed.', 'cyprus' ) );
		}

		$action = 'run_' . $perform;
		if ( ! method_exists( $this, $action ) ) {
			$this->error( esc_html__( 'Unable to perform action this time.', 'cyprus' ) );
		}

		$hash_ok = array(
			'options' => esc_html__( 'Settings imported successfully.', 'cyprus' ),
			'widgets' => esc_html__( 'Widgets imported successfully.', 'cyprus' ),
			'content' => esc_html__( 'Content imported successfully. ', 'cyprus' ),
		);

		$hash_failed = array(
			'options' => esc_html__( 'Settings import failed.', 'cyprus' ),
			'widgets' => esc_html__( 'Widgets import failed.', 'cyprus' ),
			'content' => esc_html__( 'Content import failed.', 'cyprus' ),
		);

		$result = $this->$action( $_POST['demoID'] );
		if ( is_array( $result ) ) {
			$message = $hash_ok[ $perform ];
			if ( 'content' === $perform ) {
				$result['message'] = sprintf( $message, $result['start'], $result['end'], $result['total_items'] );
			}
			$this->success( $result );
		} elseif ( true === $result ) {
			$this->success( $hash_ok[ $perform ] );
		} else {
			$this->error( $hash_failed[ $perform ] );
		}
	}

	/**
	 * Import code
	 */
	public function import_code() {
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'cyprus-demo-importer' ) ) {
			$this->error( esc_html__( 'You are not authorized to perform this action.', 'cyprus' ) );
		}

		if ( empty( $_POST['code'] ) ) {
			$this->error( esc_html__( 'Please add code to import.', 'cyprus' ) );
		}

		$settings = json_decode( stripslashes( $_POST['code'] ), true );

		if ( empty( $settings ) || ! isset( $settings['cyprus'] ) ) {
			$this->error( esc_html__( 'Invalid data.', 'cyprus' ) );
		}

		// Only if there is data.
		if ( ! empty( $settings ) && is_array( $settings ) ) {
			foreach ( $settings as $option => $setting ) {
				update_option( $option, $setting );
			}
			$this->success( esc_html__( 'Settings imported successfully.', 'cyprus' ) );
		}

		$this->error( esc_html__( 'Settings import failed.', 'cyprus' ) );
	}

	/**
	 * Import settings of theme.
	 *
	 * @param string $demo_id Demo ID.
	 * @return bool
	 */
	protected function run_options( $demo_id ) {
		$file = cyprus()->admin_path() . 'demo/' . $demo_id . '/theme_options.json';
		$this->backup_options();
		return $this->set_options( $file );
	}

	/**
	 * Backup all options before first import
	 */
	private function backup_options() {
		$options = get_option( cyprus()->settings->get_key() );
		if ( $options ) {
			update_option( cyprus()->settings->get_key() . '_options_backup', $options );
		}
	}

	/**
	 * Set theme options
	 *
	 * @param string $file Theme option file path.
	 */
	private function set_options( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		// Get file contents and decode.
		$data = file_get_contents( $file );
		$data = json_decode( $data, true );

		// Only if there is data.
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $option => $settings ) {
				update_option( $option, $settings );
			}
			return true;
		}

		return false;
	}

	/**
	 * Import widgets of theme.
	 *
	 * @param string $demo_id Demo ID.
	 * @return bool
	 */
	protected function run_widgets( $demo_id ) {
		$file = cyprus()->admin_path() . 'demo/' . $demo_id . '/widgets.json';
		$this->backup_widgets();
		return $this->set_widgets( $file );
	}

	/**
	 * Backup all widgets before first import
	 */
	private function backup_widgets() {
		$options = get_option( 'sidebars_widgets' );
		if ( $options ) {
			update_option( cyprus()->settings->get_key() . '_sidebars_widgets_backup', $options );
		}
	}

	/**
	 * Set widgets options
	 *
	 * @param string $file Widgets option file path.
	 */
	private function set_widgets( $file ) {
		global $wp_registered_sidebars;

		if ( ! file_exists( $file ) ) {
			return false;
		}

		$this->make_widgets_inactive();

		// Get file contents and decode.
		$data = file_get_contents( $file );
		$data = json_decode( $data );

		// Have valid data?
		if ( empty( $data ) || ! is_object( $data ) ) {
			return false;
		}

		// Make sure that footer and custom sidebars from options panel are registered, so dirty...
		do_action( 'widgets_init' );

		$widget_instances  = array();
		$available_widgets = $this->get_available_widgets();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		$results = array();
		foreach ( $data as $sidebar_id => $widgets ) {

			// Skip inactive widgets.
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}

			// Check if sidebar is available on this site
			// Otherwise add widgets to inactive, and say so.
			if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
				$sidebar_available    = true;
				$use_sidebar_id       = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message      = '';
			} else {
				$sidebar_available    = false;
				$use_sidebar_id       = 'wp_inactive_widgets';
				$sidebar_message_type = 'error';
				$sidebar_message      = __( 'Sidebar does not exist in theme (using Inactive)', 'cyprus' );
			}

			// Result for sidebar.
			$results[ $sidebar_id ]['name']         = ! empty( $wp_registered_sidebars[ $sidebar_id ]['name'] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id;
			$results[ $sidebar_id ]['message_type'] = $sidebar_message_type;
			$results[ $sidebar_id ]['message']      = $sidebar_message;
			$results[ $sidebar_id ]['widgets']      = array();

			// Loop widgets.
			foreach ( $widgets as $widget_instance_id => $widget ) {
				$fail = false;

				// Get id_base (remove -# from end) and instance ID number.
				$id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

				// Does site support this widget?.
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail                = true;
					$widget_message_type = 'error';
					$widget_message      = __( 'Site does not support widget', 'cyprus' );
				}

				// Does widget with identical settings already exist in same sidebar?.
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {
					// Get existing widgets in this sidebar.
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array();

					// Loop widgets with ID base.
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {
						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
							$fail                = true;
							$widget_message_type = 'warning';
							$widget_message      = __( 'Widget already exists', 'cyprus' );
							break;
						}
					}
				}

				// No failure.
				if ( ! $fail ) {
					// Add widget instance.
					$single_widget_instances   = get_option( 'widget_' . $id_base );
					$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 );
					$single_widget_instances[] = json_decode( json_encode( $widget ), true );

					// Get the key it was given.
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1.
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number                             = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity.
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget.
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar.
					$sidebars_widgets                      = get_option( 'sidebars_widgets' );
					$new_instance_id                       = $id_base . '-' . $new_instance_id_number;
					$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;
					update_option( 'sidebars_widgets', $sidebars_widgets );

					// Success message.
					if ( $sidebar_available ) {
						$widget_message_type = 'success';
						$widget_message      = __( 'Imported', 'cyprus' );
					} else {
						$widget_message_type = 'warning';
						$widget_message      = __( 'Imported to Inactive', 'cyprus' );
					}
				}

				// Result for widget instance.
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['name']         = isset( $available_widgets[ $id_base ]['name'] ) ? $available_widgets[ $id_base ]['name'] : $id_base;
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['title']        = property_exists( $widget, 'title' ) && ! empty( $widget->title ) ? $widget->title : __( 'No Title', 'cyprus' );
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message_type'] = $widget_message_type;
				$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message']      = $widget_message;
			}
		}

		return true;
	}

	/**
	 * Available widgets
	 *
	 * Gather site's widgets into array with ID base, name, etc.
	 * Used by export and import functions.
	 *
	 * @global array $wp_registered_widget_updates
	 *
	 * @return array Widget information
	 */
	private function get_available_widgets() {
		global $wp_registered_widget_controls;
		$available_widgets = array();
		$widget_controls   = $wp_registered_widget_controls;

		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
			}
		}

		return $available_widgets;
	}

	/**
	 * Move all widgets to Inactive Widgets sidebar
	 */
	private function make_widgets_inactive() {
		global $wp_registered_sidebars;
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$widgets_to_move  = array();

		if ( $sidebars_widgets && is_array( $sidebars_widgets ) && ! empty( $sidebars_widgets ) ) {
			foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
				if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) && is_array( $widgets ) ) {
					$widgets_to_move = $widgets_to_move + $widgets;
					unset( $sidebars_widgets[ $sidebar_id ] );
				}
			}

			if ( ! empty( $widgets_to_move ) ) {
				$wp_inactive_widgets                     = isset( $sidebars_widgets['wp_inactive_widgets'] ) ? $sidebars_widgets['wp_inactive_widgets'] : array();
				$sidebars_widgets['wp_inactive_widgets'] = $wp_inactive_widgets + $widgets_to_move;
				update_option( 'sidebars_widgets', $sidebars_widgets );
			}
		}
	}

	/**
	 * Import content of theme.
	 *
	 * @param string $demo_id Demo ID.
	 * @return bool
	 */
	protected function run_content( $demo_id ) {
		$file = cyprus()->admin_path() . 'demo/' . $demo_id . '/content.xml';
		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		require_once ABSPATH . 'wp-admin/includes/import.php';
		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( ! file_exists( $class_wp_importer ) ) {
				return false;
			}

			require_once $class_wp_importer;
		}

		if ( ! class_exists( 'WP_Import' ) ) {
			$class_wp_import = cyprus()->admin_path() . 'libs/wordpress-importer/wordpress-importer.php';
			if ( ! file_exists( $class_wp_import ) ) {
				return false;
			}

			require_once $class_wp_import;
		}

		$wp_import = new WP_Import();

		ob_start();
		$wp_import->fetch_attachments = true;
		$wp_import->import( $file );
		$this->set_menus( $demo_id );

		$error = strtolower( ob_get_clean() );

		return true;
	}

	/**
	 * Add menus - the menus listed here largely depend on the ones registered in the theme
	 */
	private function set_menus( $demo_id ) {
		$demos = $this->get_demos();

		if ( ! isset( $demos[ $demo_id ]['menus'] ) || empty( $demos[ $demo_id ]['menus'] ) ) {
			return;
		}

		$new_nav_menu_locations = array();
		foreach ( $demos[ $demo_id ]['menus'] as $location => $menu_name ) {
			if ( ! empty( $menu_name ) ) {
				$menu_term = get_term_by( 'name', $menu_name, 'nav_menu' );
				if ( ! is_wp_error( $menu_term ) && $menu_term ) {
					$new_nav_menu_locations[ $location ] = $menu_term->term_id;
				}
			} else {
				$new_nav_menu_locations[ $location ] = '';
			}
		}

		if ( ! empty( $new_nav_menu_locations ) ) {
			set_theme_mod( 'nav_menu_locations', $new_nav_menu_locations );
		}
	}

	/**
	 * Wrapper function for sending success response
	 *
	 * @param mixed $data Data to send to response.
	 */
	public function success( $data = null ) {
		$this->send( $data );
	}

	/**
	 * Wrapper function for sending error
	 *
	 * @param mixed $data Data to send to response.
	 */
	public function error( $data = null ) {
		$this->send( $data, false );
	}

	/**
	 * Send AJAX response.
	 *
	 * @param array   $data    Data to send using ajax.
	 * @param boolean $success Optional. If this is an error. Defaults: true.
	 */
	private function send( $data, $success = true ) {

		if ( is_string( $data ) ) {
			$data = $success ? [ 'message' => $data ] : [ 'error' => $data ];
		}
		$data['success'] = isset( $data['success'] ) ? $data['success'] : $success;

		wp_send_json( $data );
	}
}

new Cyprus_Demo_Importer;
