<?php
/**
 * Theme Options Machine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'MTS_Options' ) ) {

	class MTS_Options {

		public $framework_url     = 'https://mythemeshop.com/';
		public $framework_version = '1.0.6';
		public $url               = '';
		public $dir               = '';
		public $page              = '';
		public $args              = array();
		public $menus             = array();
		public $sections          = array();
		public $extra_tabs        = array();
		public $errors            = array();
		public $warnings          = array();
		public $options           = array();

		/**
		 * Class Constructor. Defines the args for the theme options class
		 *
		 * @param array $sections   Section to render.
		 * @param array $args       Arguments. Class constructor arguments.
		 * @param array $extra_tabs Extra tabs.
		 * @param array $menus      Menus.
		 */
		function __construct( $sections = array(), $args = array(), $extra_tabs = array(), $menus = array() ) {

			$defaults  = array();
			$this->url = isset( $args['url'] ) ? $args['url'] : '';
			$this->dir = isset( $args['dir'] ) ? $args['dir'] : trailingslashit( dirname( __FILE__ ) );

			$defaults['opt_name']              = ''; // Must be defined by theme/plugin.
			$defaults['menu_title']            = __( 'Options', 'cyprus' );
			$defaults['menu_icon']             = '';
			$defaults['page_title']            = __( 'Options', 'cyprus' );
			$defaults['page_slug']             = '_options';
			$defaults['page_cap']              = 'manage_options';
			$defaults['page_parent']           = '';
			$defaults['show_translate']        = true;
			$defaults['show_child_theme_opts'] = true;
			$defaults['dev_mode']              = true;
			$defaults['stylesheet_override']   = false;
			$defaults['help_tabs']             = array();
			$defaults['help_sidebar']          = '';

			// Get args
			$this->args = wp_parse_args( $args, $defaults );
			$this->args = cyprus_filter( 'options_args', $this->args );
			$this->args = cyprus_filter( 'options_args_' . $this->args['opt_name'], $this->args );

			// Get menus
			$this->menus = cyprus_filter( 'options_menus', $menus );
			$this->menus = cyprus_filter( 'options_menus_' . $this->args['opt_name'], $this->menus );

			// Get sections
			$this->sections = cyprus_filter( 'options_sections', $sections );
			$this->sections = cyprus_filter( 'options_sections_' . $this->args['opt_name'], $this->sections );

			//get extra tabs
			$this->extra_tabs = cyprus_filter( 'options_extra-tabs', $extra_tabs );
			$this->extra_tabs = cyprus_filter( 'options_extra-tabs_' . $this->args['opt_name'], $this->extra_tabs );

			//set option with defaults
			add_action( 'init', array( $this, '_set_default_options' ) );

			// Theme Options Update
			// Add new fields with default values
			add_action( 'admin_init', array( $this, 'check_theme_options_version' ) );

			//options page
			add_action( 'admin_menu', array( $this, '_options_page' ), 25 );

			//register setting
			add_action( 'admin_init', array( $this, '_register_setting' ) );

			//add the js for the error handling before the form
			add_action( 'cyprus_options_page_before_form', array( $this, '_errors_js' ), 1 );

			//add the js for the warning handling before the form
			add_action( 'cyprus_options_page_before_form', array( $this, '_warnings_js' ), 2 );

			// Generate Font Preview
			add_action( 'wp_ajax_mts_generate_font_preview', array( $this, 'generate_font_preview' ) );

			//get the options for use later on
			$this->options = get_option( $this->args['opt_name'] );
		}

		protected function html_attributes( $attr = array() ) {

			$out = '';
			foreach ( $attr as $name => $value ) {
				$out .= '' !== $value ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
			}

			return trim( $out );
		}

		protected function print_description( $before = '' ) {

			if ( isset( $this->field['desc'] ) && ! empty( $this->field['desc'] ) ) {
				echo $before . '<span class="description">' . $this->field['desc'] . '</span>';
			}
		}

		function check_theme_options_version() {

			$theme         = wp_get_theme();
			$theme_version = $theme->get( 'Version' );

			$options_version = get_option( $this->args['opt_name'] . '_version', '0' );
			if ( version_compare( $theme_version, $options_version, '>' ) ) {
				$this->update_theme_options( $options_version, $theme_version );
				update_option( $this->args['opt_name'] . '_version', $theme_version );
			}
		}

		/**
		 * Check for new theme options fields, add them with default values
		 *
		 * @param integer $from_version From version.
		 * @param integer $to_version   To version.
		 */
		function update_theme_options( $from_version = 0, $to_version = 0 ) {

			$options = get_option( $this->args['opt_name'] );

			foreach ( $this->sections as $section ) {

				if ( empty( $section ) ) {
					continue;
				}

				foreach ( $section as $field ) {

					if ( ! isset( $options[ $field['id'] ] ) && isset( $field['std'] ) ) {
						$options[ $field['id'] ] = $field['std'];
					}

					// Reset after specific version update
					if ( isset( $field['reset_at_version'] ) && version_compare( $from_version, $field['reset_at_version'], '<' ) ) {

						$options[ $field['id'] ] = isset( $field['std'] ) ? $field['std'] : '';
					}
				}
			}

			update_option( $this->args['opt_name'], $options );
			$this->options = $options;
		}

		/**
		 * This is used to return and option value from the options array
		 *
		 * @param array $args    Option name to get.
		 * @param array $default Default value.
		 */
		function get( $opt_name, $default = null ) {
			return ( ! empty( $this->options[ $opt_name ] ) ) ? $this->options[ $opt_name ] : $default;
		}

		/**
		 * ->set(); This is used to set an arbitrary option in the options array
		 *
		 * @since MTS_Options 1.0.1
		 *
		 * @param string $opt_name the name of the option being added
		 * @param mixed $value the value of the option being added
		 */
		function set( $opt_name, $value ) {
			$this->options[ $opt_name ] = $value;
			update_option( $this->args['opt_name'], $this->options );
		}

		/**
		 * ->show(); This is used to echo and option value from the options array
		 *
		 * @since MTS_Options 1.0.1
		 *
		 * @param $array $args Arguments. Class constructor arguments.
		 */
		function show( $opt_name ) {

			$option = $this->get( $opt_name );
			if ( ! is_array( $option ) ) {
				echo $option;
			}
		}

		/**
		 * Get default options into an array suitable for the settings API
		 *
		 * @since MTS_Options 1.0
		 */
		function _default_values() {

			$defaults = array();

			foreach ( $this->sections as $section ) {

				if ( empty( $section ) ) {
					continue;
				}

				foreach ( $section as $field ) {

					if ( empty( $field ) ) {
						continue;
					}

					if ( ! isset( $field['std'] ) ) {
						$field['std'] = '';
					}

					$defaults[ $field['id'] ] = $field['std'];
				}
			}

			// Fix for notice on first page load
			$defaults['last_tab'] = 0;

			return $defaults;
		}

		/**
		 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
		 *
		 * @since MTS_Options 1.0
		 */
		function _set_default_options() {
			if ( ! get_option( $this->args['opt_name'] ) ) {
				add_option( $this->args['opt_name'], $this->_default_values() );
			}
			$this->options = get_option( $this->args['opt_name'] );
		}

		/**
		 * Class Theme Options Page Function, creates main options page.
		 *
		 * @since MTS_Options 1.0
		 */
		function _options_page() {
			if ( empty( $this->args['page_parent'] ) ) {
				$this->page = add_menu_page(
					$this->args['page_title'],
					$this->args['menu_title'],
					$this->args['page_cap'],
					$this->args['page_slug'],
					array( $this, '_options_page_html' ),
					$this->args['menu_icon'],
					10
				);
			} else {
				$this->page = add_submenu_page(
					$this->args['page_parent'],
					$this->args['page_title'],
					$this->args['menu_title'],
					$this->args['page_cap'],
					$this->args['page_slug'],
					array( $this, '_options_page_html' ),
					'',
					10
				);
			}

			add_action( 'admin_print_styles-' . $this->page, array( $this, '_enqueue' ), 25 );
			add_action( 'load-' . $this->page, array( $this, '_load_page' ) );
		}

		/**
		 * enqueue styles/js for theme page
		 *
		 * @since MTS_Options 1.0
		 */
		function _enqueue() {

			// Loads in the required media files for the plugin update modal.
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script( 'plugin-install' );

			wp_register_style(
				'mts-opts-css',
				$this->url . 'css/options.css',
				null,
				cyprus()->get_version(),
				'all'
			);

			wp_register_style(
				'mts-opts-jquery-ui-css',
				cyprus_filter( 'options_ui_theme', $this->url . 'css/aristo.css' ),
				null,
				cyprus()->get_version(),
				'all'
			);

			wp_register_style(
				'font-awesome',
				get_template_directory_uri() . '/css/font-awesome.min.css',
				null,
				cyprus()->get_version(),
				'all'
			);

			if ( false === $this->args['stylesheet_override'] ) {
				wp_enqueue_style( 'mts-opts-css' );
				wp_enqueue_style( 'font-awesome' );
			}

			wp_register_script(
				'mts-history-js',
				$this->url . 'js/history.js',
				array( 'jquery' ),
				cyprus()->get_version(),
				true
			);

			wp_register_script(
				'mts-opts-js',
				$this->url . 'js/cyprus-options.js',
				array( 'jquery', 'mts-history-js' ),
				cyprus()->get_version(),
				true
			);

			$this->json = array(
				'opt_name'               => $this->args['opt_name'],
				'reset_confirm'          => esc_html__( 'Are you sure you want to reset options to default?', 'cyprus' ),
				'leave_page_confirm'     => esc_html__( 'Settings have changed, you should save them! Are you sure you want to leave this page?', 'cyprus' ),
				'child_theme_name_empty' => esc_html__( 'Please enter desired child theme name . ', 'cyprus' ),
				'import_done'            => esc_html__( 'Importing proccess finished!', 'cyprus' ),
				'import_fail'            => esc_html__( 'Importing proccess failed! Please try again . ', 'cyprus' ),
				'remove_done'            => esc_html__( 'Removal proccess finished!', 'cyprus' ),
				'remove_fail'            => esc_html__( 'Removal proccess failed! Please try again . ', 'cyprus' ),
				'reloading_page'         => esc_html__( 'Reloading page.. . ', 'cyprus' ),
				'import_opt_confirm'     => esc_html__( 'Are you sure you want to import demo options? All current options will be lost . ', 'cyprus' ),
				'import_widget_confirm'  => esc_html__( 'Are you sure you want to import demo options and widgets? All current options will be lost and existing widgets deactivated . ', 'cyprus' ),
				'import_all_confirm'     => esc_html__( 'Are you sure you want to import demo?', 'cyprus' ),
				'remove_all_confirm'     => esc_html__( 'Are you sure you want to remove demo options, content, menus and widgets? Please note that all modifications you\'ve made to imported content will be lost', 'cyprus' ),
			);

			wp_deregister_script( 'select2' );
			foreach ( $this->sections as $section ) {

				if ( empty( $section ) ) {
					continue;
				}

				foreach ( $section as $field ) {

					if ( isset( $field['type'] ) ) {

						$field_class = 'MTS_Options_' . $field['type'];

						if ( ! class_exists( $field_class ) ) {
							require_once( $this->dir . 'fields/' . $field['type'] . '/field_' . $field['type'] . '.php' );
						}

						if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
							$enqueue = new $field_class( $field, '', $this );
							$enqueue->enqueue();
						}
					}
				}
			}

			wp_localize_script( 'mts-opts-js', 'mtsOptions', $this->json );
			wp_enqueue_script( 'mts-opts-js' );

			cyprus_action( 'options_enqueue' );
			cyprus_action( 'options_enqueue_' . $this->args['opt_name'] );
		}

		/**
		 * show page help
		 *
		 * @since MTS_Options 1.0
		 */
		function _load_page() {

			// do admin head action for this page
			add_action( 'admin_head', array( $this, 'admin_head' ) );

			$screen = get_current_screen();

			if ( is_array( $this->args['help_tabs'] ) ) {
				foreach ( $this->args['help_tabs'] as $tab ) {
					$screen->add_help_tab( $tab );
				}
			}

			if ( '' !== $this->args['help_sidebar'] ) {
				$screen->set_help_sidebar( $this->args['help_sidebar'] );
			}

			cyprus_action( 'options_load_page', $screen );
			cyprus_action( 'options_load_page_' . $this->args['opt_name'], $screen );
		}

		/**
		 * do action mts-opts-admin-head for theme options page
		 *
		 * @since MTS_Options 1.0
		 */
		function admin_head() {

			cyprus_action( 'options_admin_head', $this );
			cyprus_action( 'options_admin_head_' . $this->args['opt_name'], $this );
		}

		/**
		 * Register Option for use
		 *
		 * @since MTS_Options 1.0
		 */
		function _register_setting() {

			register_setting( $this->args['opt_name'] . '_group', $this->args['opt_name'], array( $this, '_validate_options' ) );

			$this->flatten_menus = array();

			foreach ( $this->menus as $id => $menu ) {

				$this->flatten_menus[ $id ] = $menu;
				$this->_add_setting( $id, $menu );

				if ( isset( $menu['child'] ) ) {

					foreach ( $menu['child'] as $child_id => $child ) {

						$this->flatten_menus[ $child_id ] = $child;
						$this->_add_setting( $child_id, $child );
					}
				}
			}

			cyprus_action( 'options_register_settings' );
			cyprus_action( 'options_register_settings_' . $this->args['opt_name'] );
		}

		function _add_setting( $id, $menu ) {

			add_settings_section( $id . '_section', $menu['title'], array( $this, '_section_desc' ), $id . '_section_group' );

			if ( isset( $this->sections[ $id ] ) && ! empty( $this->sections[ $id ] ) ) {
				$fields = $this->sections[ $id ];

				foreach ( $fields as $fieldk => $field ) {

					if ( is_null( $field ) || empty( $field ) ) {
						continue;
					}

					if ( isset( $field['title'] ) && ! in_array( $field['type'], array( 'heading', 'typography_collections' ) ) ) {

						$th = isset( $field['sub_desc'] ) ? '<span class="field_title">' . $field['title'] . '</span><span class="description">' . $field['sub_desc'] . '</span>' : '<span class="field_title">' . $field['title'] . '</span>';
					} else {
						$th = '';
					}

					if ( ! isset( $field['class'] ) ) {
						$field['class'] = 'mts-type-' . sanitize_title( $field['type'] );
					}

					add_settings_field( $fieldk . '_field', $th, array( $this, '_field_input' ), $id . '_section_group', $id . '_section', $field );
				}
			}
		}

		/**
		 * Validate the Options options before insertion
		 *
		 * @since MTS_Options 1.0
		 */
		function _validate_options( $plugin_options ) {

			set_transient( 'mts-opts-saved', '1', 1000 );

			if ( ! empty( $plugin_options['import'] ) ) {

				if ( '' !== $plugin_options['import_code'] ) {
					$import = $plugin_options['import_code'];
				} elseif ( '' !== $plugin_options['import_link'] ) {
					$import = wp_remote_retrieve_body( wp_remote_get( $plugin_options['import_link'] ) );
				}

				$imported_options = json_decode( trim( $import, '# ' ), true );
				if (
					is_array( $imported_options ) &&
					isset( $imported_options['mts-opts-backup'] ) &&
					'1' == $imported_options['mts-opts-backup']
				) {
					unset( $imported_options['mts-opts-backup'] );
					foreach ( $imported_options as $option => $settings ) {
						update_option( $option, $settings );
					}

					$imported_options = $imported_options[ $this->args['opt_name'] ];
					$imported_options['imported'] = 1;
					return $imported_options;
				}
			}

			$defaults = $this->_default_values();

			// reset settings
			if ( ! empty( $plugin_options['defaults'] ) ) {
				$plugin_options = $defaults;

				return $plugin_options;
			}

			// Reset to section defaults
			if ( ! empty( $plugin_options['defaults-section'] ) ) {

				if ( isset( $plugin_options['last_tab'] ) && isset( $this->sections[ $plugin_options['last_tab'] ] ) ) {

					foreach ( $this->sections[ $plugin_options['last_tab'] ] as $field ) {

						if ( isset( $defaults[ $field['id'] ] ) ) {
							$plugin_options[ $field['id'] ] = $defaults[ $field['id'] ];

							if ( isset( $field['options'] ) && isset( $field['options']['enabled'] ) ) {
								foreach( $field['options']['enabled'] as $enabled ){
									if( isset( $enabled['subfields'] ) ){
										$subfields = $enabled['subfields'];

										foreach ( $subfields as $sub_field ) {
											if( isset( $sub_field['std'] ) ){
												$plugin_options[ $sub_field['id'] ] =  $sub_field['std'];
											} else {
												$plugin_options[ $sub_field['id'] ] = '';
											}
										}
									}
								}
							 }
							 if ( isset( $field['options'] ) && isset( $field['options']['disabled'] ) ) {
								foreach( $field['options']['disabled'] as $disabled ){
									if( isset( $disabled['subfields'] ) ){
										$subfields = $disabled['subfields'];

										foreach ( $subfields as $sub_field ) {
											if( isset( $sub_field['std'] ) ){
												$plugin_options[ $sub_field['id'] ] =  $sub_field['std'];
											} else {
												$plugin_options[ $sub_field['id'] ] = '';
											}
										}
									}
								}
							 }
						} else {
							$plugin_options[ $field['id'] ] = '';
						}
					}
				}
				return $plugin_options;
			}

			// Validate fields (if needed)
			$plugin_options = $this->_validate_values( $plugin_options, $this->options );

			if ( $this->errors ) {
				set_transient( 'mts-opts-errors', $this->errors, 1000 );
			}

			if ( $this->warnings ) {
				set_transient( 'mts-opts-warnings', $this->warnings, 1000 );
			}

			cyprus_action( 'options_options_validate', $plugin_options, $this->options );
			cyprus_action( 'options_options_validate_' . $this->args['opt_name'], $plugin_options, $this->options );

			// no need to store these
			unset( $plugin_options['defaults'] );
			unset( $plugin_options['import'] );
			unset( $plugin_options['import_code'] );
			unset( $plugin_options['import_link'] );

			return $plugin_options;
		}


		/**
		 * Validate values from options form (used in settings api validate function)
		 * calls the custom validation class for the field so authors can override with custom classes
		 *
		 * @since MTS_Options 1.0
		 */
		function _validate_values( $plugin_options, $options ) {
			foreach ( $this->sections as $id => $section ) {

				if ( empty( $section ) ) {
					continue;
				}

				foreach ( $section as $field ) {
					if ( empty( $field ) ) {
						continue;
					}
					$field['section_id'] = $id;

					if ( isset( $field['type'] ) && 'multi_text' === $field['type'] ) {
						continue;
					} // We cant validate this yet

					if ( ! isset( $plugin_options[ $field['id'] ] ) || '' === $plugin_options[ $field['id'] ] ) {
						continue;
					}

					if ( isset( $field['type'] ) && 'typography_collections' === $field['type'] ) {
						$value = $plugin_options[ $field['id'] ];
						unset( $value['@'] );
						$value                          = array_merge( $value );
						$plugin_options[ $field['id'] ] = $value;
					}

					// Force validate of custom filed types
					if ( isset( $field['type'] ) && ! isset( $field['validate'] ) ) {

						switch ( $field['type'] ) {
							case 'color':
							case 'color_gradient':
								$field['validate'] = 'color';
								break;

							case 'date':
								$field['validate'] = 'date';
								break;

						}
					}

					if ( isset( $field['validate'] ) ) {
						$validate = 'NHP_Validation_' . $field['validate'];

						if ( ! class_exists( $validate ) ) {
							require_once( $this->dir . 'validation/' . $field['validate'] . '/validation_' . $field['validate'] . '.php' );
						}

						if ( class_exists( $validate ) ) {

							$validation                     = new $validate( $field, $plugin_options[ $field['id'] ], $options[ $field['id'] ] );
							$plugin_options[ $field['id'] ] = $validation->value;
							if ( isset( $validation->error ) ) {
								$this->errors[] = $validation->error;
							}
							if ( isset( $validation->warning ) ) {
								$this->warnings[] = $validation->warning;
							}
							continue;
						}
					}

					if ( isset( $field['validate_callback'] ) && function_exists( $field['validate_callback'] ) ) {

						$callbackvalues                 = call_user_func( $field['validate_callback'], $field, $plugin_options[ $field['id'] ], $options[ $field['id'] ] );
						$plugin_options[ $field['id'] ] = $callbackvalues['value'];
						if ( isset( $callbackvalues['error'] ) ) {
							$this->errors[] = $callbackvalues['error'];
						}
						if ( isset( $callbackvalues['warning'] ) ) {
							$this->warnings[] = $callbackvalues['warning'];
						}
					}
				}
			}

			return $plugin_options;
		}

		/**
		 * HTML OUTPUT.
		 *
		 * @since MTS_Options 1.0
		 */
		function _options_page_html() {

			include_once $this->dir . 'templates/wrapper-start.php';

				include_once $this->dir . 'templates/header.php';

				include_once $this->dir . 'templates/warnings.php';

					include_once $this->dir . 'templates/sidebar.php';

					include_once $this->dir . 'templates/content.php';

				include_once $this->dir . 'templates/footer.php';

			include_once $this->dir . 'templates/wrapper-end.php';
		}

		public function render_menu( $menus ) {

			foreach ( $menus as $id => $menu ) :
			?>
				<li id="<?php echo $id; ?>"<?php echo ! empty( $menu['child'] ) ? 'class="has-child"' : ''; ?>>

					<a href="javascript:void(0);" title="<?php echo $menu['title']; ?>">
						<i class="fa <?php echo ! empty( $menu['icon'] ) ? $menu['icon'] : 'fa-cogs'; ?>"></i>
						<span class="section_title"><?php echo $menu['title']; ?></span>
					</a>

					<?php if ( ! empty( $menu['child'] ) ) : ?>
						<ul class="submenu">
							<?php $this->render_menu( $menu['child'] ); ?>
						</ul>
					<?php endif; ?>
				</li>

			<?php
			endforeach;
		}

		/**
		 * JS to display the errors on the page
		 *
		 * @since MTS_Options 1.0
		 */
		function _errors_js() {

			if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] && get_transient( 'mts-opts-errors' ) ) {

				$section_errors = array();
				$errors         = get_transient( 'mts-opts-errors' );

				foreach ( $errors as $error ) {
					$section_errors[ $error['section_id'] ] = isset( $section_errors[ $error['section_id'] ] ) ? $section_errors[ $error['section_id'] ] : 0;
					$section_errors[ $error['section_id'] ]++;
				}
				?>
				<script type="text/javascript">
					;(function( $ ) {

						$( document ).ready( function() {

							var errors = $( '.mts-opts-field-errors' );
							errors.find( 'span' ).html( '<?php echo count( $errors ); ?>' );
							errors.show();

							<?php foreach ( $section_errors as $sectionkey => $section_error ) : ?>
								$( '#<?php echo $sectionkey; ?> > a' ).append( '<span class="mts-opts-menu-error"><?php echo $section_error; ?></span>' );
							<?php endforeach; ?>

							<?php foreach ( $errors as $error ) : ?>
								$( '#<?php echo $error['id']; ?>' ).addClass( 'mts-opts-field-error' );
								$( '#<?php echo $error['id']; ?>' ).closest( 'td' ).append( '<span class="mts-opts-th-error"><?php echo $error['msg']; ?></span>' );
							<?php endforeach; ?>
						});

					})( jQuery );
				</script>
				<?php
				delete_transient( 'mts-opts-errors' );
			}
		}

		/**
		 * JS to display the warnings on the page
		 *
		 * @since MTS_Options 1.0.3
		 */
		function _warnings_js() {

			if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] && get_transient( 'mts-opts-warnings' ) ) {

				$section_warnings = array();
				$warnings         = get_transient( 'mts-opts-warnings' );
				foreach ( $warnings as $warning ) {
					$section_warnings[ $warning['section_id'] ] = isset( $section_warnings[ $warning['section_id'] ] ) ? $section_warnings[ $warning['section_id'] ] : 0;
				}
				$section_warnings[ $warning['section_id'] ]++;

				?>
				<script type="text/javascript">
					;(function( $ ) {

						$( document ).ready( function() {

							var warnings = $( '.mts-opts-field-warnings' );
							warnings.find( 'span' ).html( '<?php echo count( $warnings ); ?>' );
							warnings.show();

							<?php foreach ( $section_warnings as $sectionkey => $section_warning ) : ?>
								$( '#<?php echo $sectionkey; ?> > a' ).append( '<span class="mts-opts-menu-warning"><?php echo $section_warning; ?></span>' );
							<?php endforeach; ?>

							<?php foreach ( $warnings as $warning ) : ?>
								$( '#<?php echo $warning['id']; ?>' ).addClass( 'mts-opts-field-warning' );
								$( '#<?php echo $warning['id']; ?>' ).closest( 'td' ).append( '<span class="mts-opts-th-warning"><?php echo $warning['msg']; ?></span>' );
							<?php endforeach; ?>
						});

					})( jQuery );
				</script>
				<?php
				delete_transient( 'mts-opts-warnings' );
			}
		}

		/**
		 * Section description output
		 */
		function _section_desc( $section ) {

			$id   = str_replace( '_section', '', $section['id'] );
			$menu = $this->flatten_menus[ $id ];

			if ( isset( $menu['desc'] ) && ! empty( $menu['desc'] ) ) {
				echo '<div class="mts-opts-section-desc">' . $menu['desc'] . '</div>';
			}
		}

		/**
		 * Field HTML OUTPUT.
		 *
		 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
		 *
		 * @since MTS_Options 1.0
		 */
		function _field_input( $field, $group_id = '', $index = 0 ) {

			if ( isset( $field['callback'] ) && function_exists( $field['callback'] ) ) {

				$value = isset( $this->options[ $field['id'] ] ) ? $this->options[ $field['id'] ] : '';

				cyprus_action( 'options_before_field', $field, $value );
				cyprus_action( 'options_before_field_' . $this->args['opt_name'], $field, $value );

				call_user_func( $field['callback'], $field, $value );

				cyprus_action( 'options_after_field', $field, $value );
				cyprus_action( 'options_after_field_' . $this->args['opt_name'], $field, $value );

				return;
			}

			if ( isset( $field['type'] ) ) {

				$field_class = 'MTS_Options_' . $field['type'];

				if ( class_exists( $field_class ) ) {
					require_once( $this->dir . 'fields/' . $field['type'] . '/field_' . $field['type'] . '.php' );
				}

				if ( class_exists( $field_class ) ) {

					$value = isset( $this->options[ $field['id'] ] ) ? $this->options[ $field['id'] ] : '';

					if ( ! empty( $group_id ) ) {
						$value = isset( $this->options[ $group_id ][ $index ][ $field['id'] ] ) ? $this->options[ $group_id ][ $index ][ $field['id'] ] : '';
					}

					cyprus_action( 'options_before_field', $field, $value );
					cyprus_action( 'options_before_field_' . $this->args['opt_name'], $field, $value );

					$field  = $this->_field_get_data( $field );
					$render = new $field_class( $field, $value, $this );
					$this->_field_dependency( $field );
					$render->render();

					cyprus_action( 'options_after_field', $field, $value );
					cyprus_action( 'options_after_field_' . $this->args['opt_name'], $field, $value );
				}
			}
		}

		private function _field_dependency( $field ) {

			if ( ! isset( $field['dependency'] ) || empty( $field['dependency'] ) ) {
				return;
			}

			$data_dependency = '';
			$relation        = key( $field['dependency'] );

			if ( 'relation' === $relation ) {
				$relation = current( $field['dependency'] );
				unset( $field['dependency']['relation'] );
			} else {
				$relation = 'OR';
			}
			foreach ( $field['dependency'] as $key => $dependence ) {
				$data_dependency .= '<span class="hidden" data-value="' . $dependence['value'] . '" data-field="' . $dependence['field'] . '" data-comparison="' . $dependence['comparison'] . '"></span>';
			}

			echo '<div class="cyprus-option-dependency" data-relation="' . strtolower( $relation ) . '">' . $data_dependency . '</div>';
		}

		private function _field_get_data( $field ) {

			// setData
			if ( ! empty( $field['data'] ) && empty( $field['options'] ) ) {
				if ( empty( $field['args'] ) ) {
					$field['args'] = array();
				}

				$field['options'] = $this->get_wordpress_data( $field['data'], $field['args'] );
			}

			return $field;
		}

		// Generate Font Preview -----------------------------

		public function generate_font_preview() {

			$generate_preview = ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) && function_exists( 'imagettftext' ) );

			if ( ! $generate_preview ) {
				return 'GD library not found.';
			}

			$fonts = isset( $_POST['fonts'] ) ? $_POST['fonts'] : false;

			if ( empty( $fonts ) ) {
				return;
			}

			$this->font_dir = $this->dir . 'img/fonts/';

			// Preview Attributes
			$width       = 400;
			$height      = 64;
			$font_size   = 28;
			$left_margin = 3;

			$path              = $this->dir . 'fields/typography/googlefonts-array.php';
			$this->fonts_array = include $path;
			$this->fonts_array = $this->fonts_array['items'];

			// Generate image
			foreach ( $fonts as $id => $name ) {
				$exists = file_exists( $this->font_dir . $id . '.png' );

				if ( $exists ) {
					continue;
				}

				$ttf_path = $this->maybe_get_remote_ttf( $id, $name );
				if ( ! $ttf_path || ! file_exists( $ttf_path ) ) {
					error_log( 'MyThemeShop/Typography: Could not load $ttf_path: ' . $ttf_path );
					return;
				}

				// Text Mask
				$mask       = imageCreate( $width, $height );
				$background = imageColorAllocate( $mask, 255, 255, 255 );
				$foreground = imageColorAllocate( $mask, 0, 0, 0 );

				// Text
				$y = $this->get_centered_y_coordinate( $font_size, $ttf_path, $name, $height );
				imagettftext( $mask, $font_size, 0, $left_margin, $y, $foreground, $ttf_path, $name );

				// White fill
				$white      = imageCreate( $width, $height );
				$background = imageColorAllocate( $white, 255, 255, 255 );

				// Image
				$image = imagecreatetruecolor( $width, $height );
				imagesavealpha( $image, true );
				imagefill( $image, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );

				// Apply Mask to Image
				for ( $x = 0; $x < $width; $x++ ) {
					for ( $y = 0; $y < $height; $y++ ) {
						$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
						$alpha = 127 - floor( $alpha['red'] / 2 );
						$color = imagecolorsforindex( $white, imagecolorat( $white, $x, $y ) );
						imagesetpixel( $image, $x, $y, imagecolorallocatealpha( $image, $color['red'], $color['green'], $color['blue'], $alpha ) );
					}
				}

				ob_start();
				imagePNG( $image );
				$image = ob_get_clean();

				$this->save_image( $image, $id, $ttf_path );
			}
		}

		/**
		 * Path to the cached or downloaded TTF file
		 * @return string
		 */
		public function maybe_get_remote_ttf( $slug, $name ) {

			$ttf_path = $this->font_dir . 'ttf/' . $slug . '.ttf';

			return file_exists( $ttf_path ) ? $ttf_path : $this->get_remote_ttf( $slug, $name );
		}

		/**
		 * Path to the cached TTF file received from remote request.
		 * @return string
		 */
		public function get_remote_ttf( $slug, $name ) {

			// Load filesystem
			global $wp_filesystem;
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require ABSPATH . 'wp-admin/includes/file.php';
			}
			WP_Filesystem();

			if ( ! defined( 'FS_CHMOD_FILE' ) ) {
				define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
			}

			// Create cache directory
			$dir = $this->font_dir . 'ttf';
			if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
				error_log( 'MyThemeShop/Typography: Please check permissions. Could not create directory ' . $dir . '.' );
				return false;
			}

			// Cache remote TTF to filesystem
			$ttf_url      = $this->get_font_ttf_url( $name );
			$file_content = $this->get_remote_ttf_contents( $ttf_url );
			if ( ! $file_content ) {
				return false;
			}

			$ttf_file_path = $wp_filesystem->put_contents(
				$this->font_dir . 'ttf/' . $slug . '.ttf',
				$file_content,
				FS_CHMOD_FILE // predefined mode settings for WP files
			);

			// Check file saved
			if ( ! $ttf_file_path ) {
				error_log( 'MyThemeShop/Typography: Please check permissions. Could not write font to ' . $dir . '.' );
				return false;
			}

			return $this->font_dir . 'ttf/' . $slug . '.ttf';
		}

		/**
		 * Get font ttf url
		 * @param  [type] $name [description]
		 * @return [type]       [description]
		 */
		public function get_font_ttf_url( $name ) {

			foreach ( $this->fonts_array as $font ) {

				if ( $name === $font['family'] ) {
					return isset( $font['files']['regular'] ) ? $font['files']['regular'] : reset( $font['files'] );
				}
			}
		}

		/**
		 * The active variant's TTF file contents
		 * @return file
		 */
		public function get_remote_ttf_contents( $url ) {

			if ( empty( $url ) ) {
				error_log( 'MyThemeShop/Typography: Font URL not set.' );
				return false;
			}

			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) ) {
				error_log( 'MyThemeShop/Typography: Attempt to get remote font returned an error.<br/>' . $url );
				return false;
			}

			return $response['body'];
		}

		/**
		 * Calculate y-coordinate for centering text vertically.
		 *
		 * @link http://stackoverflow.com/a/15001168
		 * @return int  y-coordinate
		 */
		public function get_centered_y_coordinate( $fontsize, $font, $text, $image_height ) {

			$dims = imagettfbbox( $fontsize, 0, $font, $text );

			$ascent  = absint( $dims[7] );
			$descent = absint( $dims[1] );
			$height  = $ascent + $descent;

			$y = ( ( $image_height / 2 ) - ( $height / 2 ) ) + $ascent;

			return $y;
		}

		/**
		 * Save preview image file.
		 */
		public function save_image( $image, $slug, $ttf_path ) {

			global $wp_filesystem;
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require ABSPATH . 'wp-admin/includes/file.php';
			}
			WP_Filesystem();

			$dir = $this->font_dir;
			if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
				error_log( 'MyThemeShop/Typography: Please check permissions. Could not create directory ' . $dir . '.' );
				return;
			}

			$image_file = $wp_filesystem->put_contents(
				$this->font_dir . $slug . '.png',
				$image,
				FS_CHMOD_FILE
			);

			if ( ! $image_file ) {
				error_log( 'MyThemeShop/Typography: Please check permissions. Could not write image to ' . $dir . '.' );
				return;
			}

			$result = $wp_filesystem->delete( $ttf_path );
		}

		protected function get_wordpress_data( $type = false, $args = array() ) {

			if ( empty( $type ) ) {
				return $data;
			}

			$data     = '';
			$args_key = '';

			foreach ( $args as $key => $value ) {
				$args_key .= ! is_array( $value ) ? $value . '-' : implode( '-', $value );
			}

			if ( empty( $data ) && isset( cyprus()->wp_data[ $type . $args_key ] ) ) {
				return cyprus()->wp_data[ $type . $args_key ];
			}

			$data = array();

			if ( 'categories_slug' === $type || 'category_slug' === $type ) {
				$cats = get_categories( $args );
				if ( ! empty( $cats ) ) {
					$data = wp_list_pluck( $cats, 'name', 'slug' );
				}
			} elseif ( 'categories' === $type || 'category' === $type ) {
				$cats = get_categories( $args );
				if ( ! empty( $cats ) ) {
					$data = wp_list_pluck( $cats, 'name', 'term_id' );
				}
			} elseif ( 'menus' == $type || 'menu' == $type ) {
				$menus = wp_get_nav_menus( $args );
				if ( ! empty( $menus ) ) {
					$data = wp_list_pluck( $menus, 'name', 'term_id' );
				}
			} elseif ( 'pages' == $type || 'page' == $type ) {
				if ( ! isset( $args['posts_per_page'] ) ) {
					$args['posts_per_page'] = 20;
				}
				$pages = get_pages( $args );
				if ( ! empty( $pages ) ) {
					$data = wp_list_pluck( $pages, 'post_title', 'ID' );
				}
			} elseif ( 'posts' == $type || 'post' == $type ) {
				if ( ! isset( $args['posts_per_page'] ) ) {
					$args['posts_per_page'] = 20;
				}
				$posts = get_posts( $args );
				if ( ! empty( $posts ) ) {
					$data = wp_list_pluck( $posts, 'post_title', 'ID' );
				}
			} elseif ( 'terms' == $type || 'term' == $type ) {
				$taxonomies = $args['taxonomies'];
				unset( $args['taxonomies'] );
				$terms = get_terms( $taxonomies, $args );
				if ( ! empty( $terms ) && ! is_a( $terms, 'WP_Error' ) ) {
					$data = wp_list_pluck( $terms, 'name', 'term_id' );
				}
			} elseif ( 'taxonomies' == $type || 'taxonomy' == $type ) {
				$taxonomies = get_taxonomies( $args );
				if ( ! empty( $taxonomies ) ) {
					foreach ( $taxonomies as $key => $taxonomy ) {
						$data[ $key ] = $taxonomy;
					}
				}
			} elseif ( 'post_types' == $type || 'post_type' == $type ) {
				global $wp_post_types;
				$args       = wp_parse_args( $args, array(
					'public'              => true,
					'exclude_from_search' => false,
				) );
				$output     = 'names';
				$operator   = 'and';
				$post_types = get_post_types( $args, $output, $operator );
				ksort( $post_types );
				foreach ( $post_types as $name => $title ) {
					if ( isset( $wp_post_types[ $name ]->labels->menu_name ) ) {
						$data[ $wp_post_types[ $name ]->labels->menu_name ] = $name;
					} else {
						$data[ ucfirst( $name ) ] = $name;
					}
				}
			} elseif ( 'tags' == $type || 'tag' == $type ) {
				$tags = get_tags( $args );
				if ( ! empty( $tags ) ) {
					$data = wp_list_pluck( $tags, 'name', 'term_id' );
				}
			} elseif ( 'menu_location' == $type || 'menu_locations' == $type ) {
				global $_wp_registered_nav_menus;
				foreach ( $_wp_registered_nav_menus as $k => $v ) {
					$data[ $k ] = $v;
				}
			} elseif ( 'sidebars' == $type || 'sidebar' == $type ) {
				global $wp_registered_sidebars;
				$allow_nosidebar = ( ! isset( $args['allow_nosidebar'] ) || $args['allow_nosidebar'] ) ? true : false; // true by deault.
				$hidden_sidebars = ( isset( $args['exclude'] ) && is_array( $args['exclude'] ) ) ? $args['exclude'] : array();

				$data['mts_defaultsidebar'] = esc_html__( 'Default sidebar', 'cyprus' );
				if ( $allow_nosidebar ) {
					$data['mts_nosidebar'] = esc_html__( 'No Sidebar (Full Width)', 'cyprus' );
				}

				foreach ( $wp_registered_sidebars as $key => $value ) {
					if ( ! in_array( $value['id'], $hidden_sidebars, false ) ) {
						$data[ $key ] = $value['name'];
					}
				}
			} elseif ( 'callback' == $type ) {
				if ( ! is_array( $args ) ) {
					$args = array( $args );
				}
				$data = call_user_func( $args[0] );
			}

			cyprus()->wp_data[ $type . $args_key ] = $data;

			return $data;
		}
	}
}
