<?php
/**
 * Theme Engine
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include base class
 */
include_once get_parent_theme_file_path( 'includes/class-cyprus-base.php' );

if ( ! class_exists( 'cyprus' ) ) {

	final class Cyprus extends Cyprus_Base {

		/**
		 * Version
		 * @var string
		 */
		private $version = '1.0.14';

		/**
		 * Hold an instance of cyprus class.
		 * @var cyprus
		 */
		protected static $instance = null;

		/**
		 * Hold an instance of MP_Settings class.
		 * @var MP_Settings
		 */
		public $settings = null;

		/**
		 * The current page ID.
		 * @var bool|int
		 */
		private $current_page_id = false;

		/**
		 * Possible error message.
		 * @var null|WP_Error
		 */
		protected $error = null;

		/**
		 * WordPress data holder.
		 * @var array
		 */
		public $wp_data = array();

		public $featured_layouts = null;

		/**
		 * Main cyprus instance.
		 * @return cyprus - Main instance.
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new cyprus;

				// For developers to hook.
				cyprus_action( 'loaded' );
			}

			return self::$instance;
		}

		/**
		 * You cannot clone this class.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cyprus' ), $this->version );
		}

		/**
		 * You cannot unserialize instances of this class.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cyprus' ), $this->version );
		}

		/**
		 * The Constructor
		 */
		private function __construct() {

			$this->add_action( 'after_setup_theme', 'core', -95 );
			$this->add_action( 'after_setup_theme', 'load_textdomain' );
			$this->add_action( 'after_setup_theme', 'theme_support', 12 );
			$this->add_action( 'after_setup_theme', 'includes', 13 );
			$this->add_action( 'after_setup_theme', 'frontend', 15 );
			$this->add_action( 'after_setup_theme', 'admin', 15 );
			$this->add_action( 'wp', 'set_page_id' );
			$this->add_action( 'after_switch_theme', 'theme_activation', 10, 2 );
			$this->add_action( 'after_setup_theme', 'opts_sections_override', -11 );

			add_action( 'admin_notices', array( $this, 'display_error' ), 10 );
			$this->rank_math();
		}

		/**
		 * Loads the core framework files.
		 */
		public function core() {
			include_once $this->includes_path() . 'functions-cyprus-utility.php';
			include_once $this->includes_path() . 'class-cyprus-settings.php';
			include_once $this->includes_path() . 'class-cyprus-sanitize.php';
			include_once $this->includes_path() . 'class-cyprus-dynamic-css.php';
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 */
		public function load_textdomain() {

			// Loads wp-content/languages/themes/cyprus-it_IT.mo.
			$loaded = load_theme_textdomain( 'cyprus', trailingslashit( WP_LANG_DIR ) . 'themes/' );

			// Loads wp-content/themes/child-theme-name/languages/it_IT.mo.
			$loaded = load_theme_textdomain( 'cyprus', get_stylesheet_directory() . '/languages' );

			// Path: wp-content/themes/theme-name/languages/it_IT.mo.
			// Path: wp-content/languages/themes/cyprus-it_IT.mo.
			$loaded = load_theme_textdomain( 'cyprus', get_template_directory() . '/languages' );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 */
		public function theme_support() {

			/**
			 * Add default posts and comments RSS feed links to head.
			 */
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
			 */
			add_theme_support( 'post-thumbnails' );
			set_post_thumbnail_size( 223, 137, true );
			add_image_size( 'cyprus-featured', 680, 350, true ); // featured.
			add_image_size( 'cyprus-related', 564, 401, true ); // related.
			add_image_size( 'cyprus-widgetthumb', 81, 58, true ); // widget.
			add_image_size( 'cyprus-widgetfull', 300, 200, true ); // sidebar full width.
			add_image_size( 'cyprus-layout-1', 1600, 650, true ); // Layout 1.
			add_image_size( 'cyprus-layout-2', 322, 243, true ); // Layout 2.
			add_image_size( 'cyprus-layout-small-2', 81, 58, true ); // Layout 2.
			add_image_size( 'cyprus-layout-3', 750, 650, true ); // Layout 3.
			add_image_size( 'cyprus-layout-4', 294, 273, true ); // Layout 4.

			// Navigation Menu.
			register_nav_menus( array(
				'primary-menu'   => esc_html__( 'Primary', 'cyprus' ),
				'secondary-menu' => esc_html__( 'Secondary', 'cyprus' ),
				'footer-menu'    => esc_html__( 'Footer', 'cyprus' ),
				'mobile'         => esc_html__( 'Mobile', 'cyprus' ),
			) );

			/*
			 * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
			 * to output valid HTML5.
			 */
			add_theme_support( 'html5', array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'widgets',
			) );

			// Handle content width for embeds and images.
			cyprus_set_content_width( 680 );

			// Declare WooCommerce support.
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

			// Declare support for title theme feature.
			add_theme_support( 'title-tag' );

			// Filters that allow shortcodes in Text Widgets.
			add_filter( 'widget_text', 'shortcode_unautop' );
			add_filter( 'widget_text', 'do_shortcode' );
			add_filter( 'the_content_rss', 'do_shortcode' );
		}

		/**
		 * Loads the framework files supported by themes.
		 * Functionality in these files should not be expected within the theme setup function.
		 */
		public function includes() {

			include_once $this->includes_path() . 'class-cyprus-images.php';
			include_once $this->includes_path() . 'extensions/menu/class-cyprus-menu-manager.php';
			include_once $this->includes_path() . 'class-cyprus-sidebars.php';
			include_once $this->includes_path() . 'contact-form.php';

			// Widgets.
			include_once $this->includes_path() . 'widgets/widget-aboutme.php';
			include_once $this->includes_path() . 'widgets/widget-adcode.php';
			include_once $this->includes_path() . 'widgets/widget-ad125.php';
			include_once $this->includes_path() . 'widgets/widget-ad300.php';
			include_once $this->includes_path() . 'widgets/widget-tweets.php';
			include_once $this->includes_path() . 'widgets/widget-instagram.php';
			include_once $this->includes_path() . 'widgets/widget-recentposts.php';
			include_once $this->includes_path() . 'widgets/widget-relatedposts.php';
			include_once $this->includes_path() . 'widgets/widget-authorposts.php';
			include_once $this->includes_path() . 'widgets/widget-popular.php';
			include_once $this->includes_path() . 'widgets/widget-fblikebox.php';
			include_once $this->includes_path() . 'widgets/widget-social.php';
			include_once $this->includes_path() . 'widgets/widget-catposts.php';
			include_once $this->includes_path() . 'widgets/widget-postslider.php';

			// Load WooCommerce.
			require_if_theme_supports( 'woocommerce', $this->includes_path() . 'vendors/woocommerce/woocommerce-init.php' );
		}

		/**
		 * Load files required on the frontend only
		 */
		public function frontend() {

			if ( is_admin() ) {
				return;
			}

			include_once $this->includes_path() . 'class-mobile-detect.php';
			$this->mobile = new cyprus_Mobile_Detect();

			include_once $this->includes_path() . 'functions-cyprus-attr.php';
			include_once $this->includes_path() . 'functions-cyprus-template-tags.php';
			include_once $this->includes_path() . 'class-cyprus-layout.php';
			include_once $this->includes_path() . 'class-cyprus-scripts.php';
			include_once $this->includes_path() . 'class-cyprus-head.php';
			include_once $this->includes_path() . 'class-cyprus-footer.php';

			$this->add_filter( 'pre_get_posts', 'modify_search_filter' );
			$this->add_filter( 'pre_get_posts', 'homepage_ignore_sticky' );
		}

		/**
		 * Load files required on the admin only
		 */
		public function admin() {

			if ( ! is_admin() ) {
				return;
			}

			require_once $this->includes_path() . 'admin/class-cyprus-admin.php';
		}


		/**
		 * Loads files related to Rank Math installer.
		 */
		public function rank_math() {

			if ( ! is_admin() ) {
				return;
			}

			if ( ! apply_filters( 'mts_disable_rmu', false ) ) {
				if ( ! defined( 'RMU_ACTIVE' ) ) {
					include_once $this->includes_path() . 'admin/class-mts-rmu.php';
					include_once $this->includes_path() . 'admin/class-rank-math-notice.php';
				}
				$mts_rmu = MTS_RMU::init();
			}
		}

		/**
		 * Sets the current page ID.
		 */
		public function set_page_id() {

			$page_id   = false;
			$object_id = get_queried_object_id();

			if ( get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) && is_home() ) {
				$page_id = get_option( 'page_for_posts' );
			} else {

				// Use the $object_id if available.
				if ( isset( $object_id ) ) {
					$page_id = $object_id;
				}

				// If we're not on a singular post, set to false.
				if ( ! is_singular() ) {
					$page_id = false;
				}

				// Front page is the posts page.
				if ( isset( $object_id ) && 'posts' == get_option( 'show_on_front' ) && is_home() ) {
					$page_id = $object_id;
				}

				// The woocommerce shop page.
				if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
					$page_id = get_option( 'woocommerce_shop_page_id' );
				}
			}

			$this->current_page_id = $page_id;

			return $page_id;
		}

		/**
		 * Add error.
		 *
		 * Add a new error to the WP_Error object
		 * and create object if it doesn't exists.
		 *
		 * @param string $message
		 * @param string $code
		 */
		public function add_error( $message, $code = 'error' ) {

			if ( is_null( $this->error ) && ! ( $this->error instanceof WP_Error ) ) {
				$this->error = new WP_Error();
			}

			$this->error->add( $code, $message );
		}

		/**
		 * Display error.
		 *
		 * Get all the error messages and display them in the admin notice.
		 * @return void
		 */
		public function display_error() {

			if ( is_null( $this->error ) || ! ( $this->error instanceof WP_Error ) ) {
				return;
			}

			$messages = $this->error->get_error_messages( 'success' );
			if ( $messages ) : ?>
			<div class="notice notice-success" style="display: block !important">
				<ul>
					<li><?php echo join( '</li><li>', $messages ); ?></li>
				</ul>
			</div>
			<?php
			endif;

			$messages = $this->error->get_error_messages( 'error' );
			if ( $messages ) :
			?>
			<div class="error" style="display: block !important">
				<ul>
					<li><?php echo join( '</li><li>', $messages ); ?></li>
				</ul>
			</div>
			<?php
			endif;

			$messages = $this->error->get_error_messages( 'info' );
			if ( $messages ) :
			?>
			<div class="notice notice-info" style="display: block !important">
				<ul>
					<li><?php echo join( '</li><li>', $messages ); ?></li>
				</ul>
			</div>
			<?php
			endif;
		}

		/**
		 * Modifies the search filter.
		 *
		 * @param object $query The search query.
		 *
		 * @return object $query The modified search query.
		 */
		public function modify_search_filter( $query ) {

			if ( $query->is_main_query() && $query->is_search() && cyprus_get_settings( 'search_results_per_page' ) ) {
				$query->set( 'posts_per_page', cyprus_get_settings( 'search_results_per_page' ) );
			}

			if ( is_search() && $query->is_search ) {

				if ( isset( $_GET ) && ( 2 < count( $_GET ) || ( 2 == count( $_GET ) && ! isset( $_GET['lang'] ) ) ) ) {
					return $query;
				}

				$search_content = cyprus_get_settings( 'search_content' );

				if ( 'all-no-pages' === $search_content ) {
					$query->set( 'post_type', array( 'post', 'product', 'tribe_events' ) );
				} elseif ( 'posts' === $search_content ) {
					$query->set( 'post_type', 'post' );
				} elseif ( 'pages' === $search_content ) {
					$query->set( 'post_type', 'page' );
				} elseif ( 'woocommerce' == $search_content ) {
					$query->set( 'post_type', 'product' );
				}
			}

			return $query;
		}

		/**
		 * Gets the current page ID.
		 *
		 * @return void
		 */
		public function homepage_ignore_sticky( $query ) {
			if ( $query->is_front_page() && $query->is_main_query() ) {
				$query->set( 'ignore_sticky_posts', 1 );
			}
		}

		/**
		 * Ignore sticky posts on homepage
		 *
		 * @return string The current page ID.
		 */
		public function get_page_id() {
			return $this->current_page_id;
		}

		/**
		 * Get theme version
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		public function theme_activation( $oldtheme_name = null, $oldtheme = null ) {
			// Check for Connect plugin version > 1.4
			if ( class_exists( 'mts_connection' ) && defined( 'MTS_CONNECT_ACTIVE' ) && MTS_CONNECT_ACTIVE ) {
				return;
			}
			$plugin_path = 'mythemeshop-connect/mythemeshop-connect.php';

			// Check if plugin exists
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins = get_plugins();
			if ( ! array_key_exists( $plugin_path, $plugins ) ) {
				// auto-install it
				include_once( ABSPATH . 'wp-admin/includes/misc.php' );
				include_once( ABSPATH . 'wp-admin/includes/file.php' );
				include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
				include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
				$skin        = new Automatic_Upgrader_Skin();
				$upgrader    = new Plugin_Upgrader( $skin );
				$plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';
				$result      = $upgrader->install( $plugin_file );
				// If install fails then revert to previous theme
				if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
					switch_theme( $oldtheme->stylesheet );
					return false;
				}
			} else {
				// Plugin is already installed, check version
				$ver = isset( $plugins[ $plugin_path ]['Version'] ) ? $plugins[ $plugin_path ]['Version'] : '1.0';
				if ( version_compare( $ver, '2.0.5' ) === -1 ) {
					include_once( ABSPATH . 'wp-admin/includes/misc.php' );
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
					include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
					$skin     = new Automatic_Upgrader_Skin();
					$upgrader = new Plugin_Upgrader( $skin );

					add_filter( 'pre_site_transient_update_plugins', array( $this, 'inject_connect_repo' ), 10, 2 );
					$result = $upgrader->upgrade( $plugin_path );
					remove_filter( 'pre_site_transient_update_plugins', array( $this, 'inject_connect_repo' ) );

					// If update fails then revert to previous theme
					if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
						switch_theme( $oldtheme->stylesheet );
						return false;
					}
				}
			}
			$activate = activate_plugin( $plugin_path );
		}

		public function inject_connect_repo( $pre, $transient ) {
			$plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';

			$return           = new stdClass();
			$return->response = array();
			$return->response['mythemeshop-connect/mythemeshop-connect.php']          = new stdClass();
			$return->response['mythemeshop-connect/mythemeshop-connect.php']->package = $plugin_file;

			return $return;
		}

		public function opts_sections_override() {
			define( 'MTS_THEME_INIT', 1 );
			if ( class_exists( 'mts_connection' ) && defined( 'MTS_CONNECT_ACTIVE' ) && MTS_CONNECT_ACTIVE ) {
				return;
			}
			if ( ! get_option( 'cyprus', false ) ) {
				return;
			}
			add_filter( 'mts_options_sections', array( $this, 'opts_sections_placeholder' ) );
			add_filter( 'mts_options_menus', array( $this, 'opts_menu_placeholder' ) );
		}

		public function opts_menu_placeholder( $menus ) {
			foreach ( $menus as $menu_key => $menu_data ) {
				if ( ! empty( $menu_data['child'] ) ) {
					foreach ( $menu_data['child'] as $child_menu_key => $child_menu_data ) {
						$menus[ $menu_key ]['child'][ $child_menu_key ]['desc'] = __( 'You will find all the theme options here after connecting with your MyThemeShop account.', 'cyprus' );
					}
				}
				$menus[ $menu_key ]['desc'] = __( 'You will find all the theme options here after connecting with your MyThemeShop account.', 'cyprus' );
			}
			return $menus;
		}

		public function opts_sections_placeholder( $sections ) {
			foreach ( $sections as $key => $value ) {
				$sections[ $key ] = array();
			}
			return $sections;
		}
	}

	/**
	 * Main instance of cyprus.
	 *
	 * Returns the main instance of cyprus to prevent the need to use globals.
	 *
	 * @return cyprus
	 */

	function cyprus() {
		return cyprus::get_instance();
	}

	cyprus(); // Init it
}
