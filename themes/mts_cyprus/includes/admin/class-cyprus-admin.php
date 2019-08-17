<?php
/**
 * Theme administration functions used with other components of the framework admin.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Admin
 */
class Cyprus_Admin extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {
		$this->includes();
		$this->add_action( 'admin_init', 'theme_activated' );
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
		$this->add_action( 'admin_bar_menu', 'admin_bar_menu', 99 );
		$this->add_action( 'admin_menu', 'add_theme_option_submenu', 999 );
	}

	/**
	 * Include core
	 */
	private function includes() {
		include_once $this->admin_path() . 'class-cyprus-admin-pages.php';
		include_once $this->admin_path() . 'class-cyprus-admin-metabox.php';
		include_once $this->admin_path() . 'libs/class-tgm-plugin-activation.php';
		include_once $this->admin_path() . 'cyprus-tgm-functions.php';
		include_once $this->admin_path() . 'class-cyprus-demo-importer.php';
	}

	/**
	 * Theme activated
	 *
	 * @param string $stylesheet Theme name.
	 */
	public function theme_activated( $stylesheet ) {

		if ( isset( $_GET['activated'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=install-required-plugins' ) );
		}

	}

	/**
	 * Fix first submenu name.
	 */
	public function add_theme_option_submenu() {
		global $submenu;

		$submenu['themes.php'][] = array(
			esc_html__( 'Theme Options', 'cyprus' ),
			'manage_options',
			admin_url( 'admin.php?page=cyprus-options' ),
			'cyprus-options',
		);
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		$pages  = array(
			'toplevel_page_cyprus',
			'cyprus_page_cyprus-support',
			'cyprus_page_cyprus-plugins',
			'cyprus_page_cyprus-system-status',
			'theme-options_page_cyprus_demos',
		);

		if ( in_array( $screen->base, $pages ) ) {
			wp_enqueue_style( 'cyprus-admin', $this->admin_uri() . 'assets/css/cyprus-admin.css' );
		}

		if ( 'theme-options_page_cyprus_demos' === $screen->base ) {
			wp_enqueue_style( 'cyprus-demo', $this->admin_uri() . 'assets/css/cyprus-demo.css' );
			wp_enqueue_script( 'cyprus-importer', $this->admin_uri() . 'assets/js/cyprus-importer.js', array( 'jquery' ), null, true );
		}

		wp_enqueue_script( 'clipboard', $this->admin_uri() . 'assets/vendors/clipboard.min.js', null, null, true );
		wp_enqueue_script( 'cyprus-admin', $this->admin_uri() . 'assets/js/cyprus-admin.js', array( 'clipboard' ), null, true );
	}

	/**
	 * Admin node
	 */
	public function admin_bar_menu() {
		global $menu, $submenu, $wp_admin_bar;

		if ( ! is_super_admin() || ! is_admin_bar_showing() || ! $submenu ) {
			return;
		}

		// Parent node.
		$parent = false;
		foreach ( $menu as $m ) {
			if ( 'cyprus' === $m[2] ) {
				$parent = $m;
				break;
			}
		}

		if ( ! $parent ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'    => $parent[2],
			'title' => '<span class="ab-icon"></span>' . $parent[0],
			'href'  => admin_url( 'admin.php?page=' . $parent[2] ),
		) );

		// Submenu.
		$submenus = $submenu['cyprus'];
		unset( $submenus[0] );
		foreach ( $submenus as $item ) {
			$wp_admin_bar->add_menu( array(
				'id'     => $item[2],
				'title'  => $item[0],
				'parent' => $parent[2],
				'href'   => admin_url( 'admin.php?page=' . $item[2] ),
			) );
		}
	}
}

/**
 * Init
 */
new Cyprus_Admin;
