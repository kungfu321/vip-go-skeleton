<?php
/**
 * Theme options.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Class settings.
 */
class Cyprus_Settings extends Cyprus_Base {

	/**
	 * Options array.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Current option key.
	 *
	 * @var string
	 */
	public $key = 'cyprus';

	/**
	 * Option url.
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Option dir.
	 *
	 * @var string
	 */
	public $dir = '';

	/**
	 * Raw data for export.
	 *
	 * @var array
	 */
	public $raw = array();

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->url = cyprus()->admin_uri() . 'libs/mts-options/';
		$this->dir = cyprus()->admin_path() . '/libs/mts-options/';

		if ( is_admin() ) {
			$this->add_action( 'after_setup_theme', 'setup', 0 );
		}

		// Hook into the wp feeds for downloading the exported settings.
		$this->add_action( 'do_feed_mts_download_options', 'download_options', 10, 1 );
		$this->add_action( 'admin_menu', 'fix_first_submenu', 999 );
	}

	/**
	 * Setup theme option page
	 */
	public function setup() {

		include_once( cyprus()->admin_path() . 'libs/mts-options/options.php' );

		$args = array(
			'dev_mode'           => false,
			'opt_name'           => $this->get_key(),
			'menu_title'         => esc_html__( 'Theme Options', 'cyprus' ),
			'menu_icon'          => 'dashicons-layout',
			'page_title'         => esc_html__( 'Theme Options', 'cyprus' ),
			'page_slug'          => 'cyprus-options',
			'page_parent'        => 'cyprus-options',
			'presets'            => array(),
			'show_translate'     => false,
			'show_import_export' => false,
			'url'                => $this->url,
			'dir'                => $this->dir,
		);

		// Setup custom links in the footer for share icons.
		if ( ! MTS_THEME_WHITE_LABEL ) {
			$args['share_icons']['twitter'] = array(
				'link'  => 'https://twitter.com/mythemeshopteam',
				'title' => __( 'Follow Us on Twitter', 'cyprus' ),
				'img'   => 'dashicons dashicons-twitter',
			);

			$args['share_icons']['facebook'] = array(
				'link'  => 'https://www.facebook.com/mythemeshop',
				'title' => __( 'Like us on Facebook', 'cyprus' ),
				'img'   => 'dashicons dashicons-facebook-alt',
			);

			// Set ANY custom page help tabs - displayed using the new help tab API, show in order of definition.
			$args['help_tabs'][] = array(
				'id'      => 'mts-opts-1',
				'title'   => __( 'Support', 'cyprus' ),
				/* translators: link to forums */
				'content' => '<p>' . sprintf( __( 'If you are facing any problem with our theme or theme option panel, head over to our %s.', 'cyprus' ), '<a href="https://community.mythemeshop.com/">' . __( 'Support Forums', 'cyprus' ) . '</a>' ) . '</p>',
			);

			$args['help_tabs'][] = array(
				'id'      => 'mts-opts-2',
				'title'   => __( 'Earn Money', 'cyprus' ),
				/* translators: link to affiliate */
				'content' => '<p>' . sprintf( __( 'Earn 70&#37; commision on every sale by refering your friends and readers. Join our %s.', 'cyprus' ), '<a href="https://mythemeshop.com/affiliate-program/">' . __( 'Affiliate Program', 'cyprus' ) . '</a>' ) . '</p>',
			);
		}

		$this->general_settings( $args );
		$this->homepage_settings( $args );
		$this->sidebars_settings( $args );
		$this->single_settings( $args );
		$this->typography_settings( $args );
	}

	/**
	 * Fix first submenu name.
	 */
	public function fix_first_submenu() {
		global $submenu;
		if ( isset( $submenu['cyprus-options'] ) && 'Theme Options' === $submenu['cyprus-options'][0][0] ) {
			$submenu['cyprus-options'][0][0] = esc_html__( 'General', 'cyprus' );
		}
	}

	/**
	 * Register general settings
	 *
	 * @param array $args Array of arguments.
	 */
	private function general_settings( $args ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$tabs = $this->get_sections(array(
			'general',
			'performance',
			'styling',
			'header',
			'header-top-bar',
			'footer',
			'footer-styling',
			'back-to-top',
			'footer-nav',
			'footer-brands',
			'social',
			'social-styling',
			'ad-management',
			'search',
			'advance',
			'woocommerce',
		));

		unset( $args['page_parent'] );
		new MTS_Options( $tabs['sections'], $args, array(), $tabs['menus'] );
	}

	/**
	 * Register homepage settings
	 *
	 * @param array $args Array of arguments.
	 */
	private function homepage_settings( $args ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$tabs = $this->get_sections(array(
			'home',
			'home-posts',
			'home-pagination',
		));

		$args['opt_name']  .= '_homepage';
		$args['page_slug'] .= '-homepage';
		$args['menu_title'] = esc_html__( 'Homepage', 'cyprus' );
		$args['page_title'] = esc_html__( 'Homepage', 'cyprus' );
		new MTS_Options( $tabs['sections'], $args, array(), $tabs['menus'] );
	}

	/**
	 * Register sidebars settings
	 *
	 * @param array $args Array of arguments.
	 */
	private function sidebars_settings( $args ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$tabs = $this->get_sections(array(
			'sidebars',
			'sidebars-styling',
		));

		$args['opt_name']  .= '_sidebars';
		$args['page_slug'] .= '-sidebars';
		$args['menu_title'] = esc_html__( 'Sidebars', 'cyprus' );
		$args['page_title'] = esc_html__( 'Sidebars', 'cyprus' );
		new MTS_Options( $tabs['sections'], $args, array(), $tabs['menus'] );
	}

	/**
	 * Register single settings
	 *
	 * @param array $args Array of arguments.
	 */
	private function single_settings( $args ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$tabs = $this->get_sections(array(
			'single',
			'single-styling',
			'single-related',
			'single-subscribe',
			'single-authorbox',
		));

		$args['opt_name']  .= '_single';
		$args['page_slug'] .= '-single';
		$args['menu_title'] = esc_html__( 'Single', 'cyprus' );
		$args['page_title'] = esc_html__( 'Single', 'cyprus' );
		new MTS_Options( $tabs['sections'], $args, array(), $tabs['menus'] );
	}

	/**
	 * Register typography settings
	 *
	 * @param array $args Array of arguments.
	 */
	private function typography_settings( $args ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$tabs = $this->get_sections(array(
			'typography-collection',
		));

		$args['opt_name']          .= '_typography';
		$args['page_slug']         .= '-typography';
		$args['menu_title']         = esc_html__( 'Typography', 'cyprus' );
		$args['page_title']         = esc_html__( 'Typography', 'cyprus' );
		$args['show_import_export'] = true;
		new MTS_Options( $tabs['sections'], $args, array(), $tabs['menus'] );
	}

	/**
	 * Get sections and menus
	 *
	 * @param  array $tabs Array of tabs to load.
	 * @return array
	 */
	private function get_sections( $tabs ) {
		$menus    = array();
		$sections = array();
		$uri      = cyprus()->admin_uri() . 'assets/img/';

		$mts_patterns = array(
			'nobg' => array( 'img' => $uri . 'bg-patterns/nobg.png' ),
		);
		for ( $i = 0; $i <= 52; $i++ ) {
			$mts_patterns[ 'pattern' . $i ] = array( 'img' => $uri . 'bg-patterns/pattern' . $i . '.png' );
		}

		for ( $i = 1; $i <= 29; $i++ ) {
			$mts_patterns[ 'hbg' . $i ] = array( 'img' => $uri . 'bg-patterns/hbg' . $i . '.png' );
		}

		foreach ( $tabs as $tab ) {
			include_once cyprus()->includes_path() . 'theme-options/' . $tab . '.php';
		}

		$sections = apply_filters( 'mts_options_sections', $sections );
		$menus    = apply_filters( 'mts_options_menus', $menus );

		return compact( 'sections', 'menus' );
	}

	/**
	 * Download the options file, or display it
	 */
	public function download_options() {

		if ( ! isset( $_GET['secret'] ) || md5( AUTH_KEY . SECURE_AUTH_KEY ) != $_GET['secret'] ) {
			wp_die( __( 'Invalid Secret for options use', 'cyprus' ) );
			exit;
		}

		$backup_options = isset( $_GET['option'] ) ? get_option( $_GET['option'] ) : $this->raw;
		$content        = wp_json_encode( $backup_options );

		if ( isset( $_GET['action'] ) && 'download_options' === $_GET['action'] ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-type: application/txt' );
			header( 'Content-Disposition: attachment; filename="' . $this->key . '_options_' . date( 'd-m-Y' ) . '.json"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			echo $content;
			exit;
		} else {
			echo $content;
			exit;
		}
	}

	/**
	 * Get Setting.
	 *
	 * @param  string $field_id ID of field to get.
	 * @param  mixed  $default  (Optional) Default value.
	 * @return mixed
	 */
	public function get( $field_id = '', $default = false ) {

		$opts = $this->get_options();

		if ( 'all' == $field_id ) {
			return $opts;
		} elseif ( isset( $opts[ $field_id ] ) ) {
			return false !== $opts[ $field_id ] ? $opts[ $field_id ] : $default;
		}

		return $default;
	}

	/**
	 * Update setting
	 *
	 * @param string $options Options to update.
	 */
	public function update( $options ) {
		update_option( $this->get_key(), $options );
	}

	/**
	 * Get all settings
	 *
	 * @return array
	 */
	public function all() {
		return $this->get( 'all' );
	}

	/**
	 * Get option key
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Get options once for use throughout the plugin cycle
	 *
	 * @return array
	 */
	private function get_options() {

		if ( empty( $this->options ) && ! empty( $this->key ) ) {

			$keys = array( $this->key, $this->key . '_homepage', $this->key . '_sidebars', $this->key . '_single', $this->key . '_typography' );
			foreach ( $keys as $key ) {
				$options           = get_option( $key, array() );
				$this->raw[ $key ] = $options;
				if ( empty( $options ) ) {
					continue;
				}

				foreach ( $options as $key => $value ) {
					$this->options[ $key ] = $this->normalize_data( $value );
				}
			}
		}

		return $this->options;
	}

	/**
	 * Normalize option value.
	 *
	 * @param mixed $value Value to normalize.
	 * @return mixed
	 */
	private function normalize_data( $value ) {

		if ( 'true' === $value || 'on' === $value ) {
			$value = true;
		} elseif ( 'false' === $value || 'off' === $value ) {
			$value = false;
		} elseif ( '0' === $value || '1' === $value ) {
			$value = intval( $value );
		}

		return $value;
	}
}

/**
 * Init the setting manager
 */
cyprus()->settings = new Cyprus_Settings;
