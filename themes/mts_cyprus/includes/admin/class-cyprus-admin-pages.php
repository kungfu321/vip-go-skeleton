<?php
/**
 * Theme administration pages.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Admin pages
 */
class Cyprus_Admin_Pages extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->add_action( 'admin_init', 'admin_init' );
	}

	/**
	 * Init
	 */
	public function admin_init() {

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Activate Plugin.
		if (
			isset( $_GET['cyprus-activate'] ) &&
			'activate-plugin' === $_GET['cyprus-activate'] &&
			isset( $_GET['plugin'] )
		) {
			check_admin_referer( 'cyprus-activate', 'cyprus-activate-nonce' );

			$plugins = TGM_Plugin_Activation::$instance->plugins;

			foreach ( $plugins as $plugin ) {
				if ( $plugin['slug'] === $_GET['plugin'] ) {
					activate_plugin( $plugin['file_path'] );

					wp_redirect( admin_url( 'admin.php?page=install-required-plugins' ) );
					exit;
				}
			}
		}

		// Deactivate Plugin.
		if (
			isset( $_GET['cyprus-deactivate'] ) &&
			'deactivate-plugin' === $_GET['cyprus-deactivate'] &&
			isset( $_GET['plugin'] )
		) {

			check_admin_referer( 'cyprus-deactivate', 'cyprus-deactivate-nonce' );

			$plugins = TGM_Plugin_Activation::$instance->plugins;

			foreach ( $plugins as $plugin ) {
				if ( $plugin['slug'] == $_GET['plugin'] ) {
					deactivate_plugins( $plugin['file_path'] );
				}
			}
		}
	}

	/**
	 * Get the plugin link.
	 *
	 * @param  array $item         The plugin in question.
	 * @param  array $registration Is registration done.
	 * @return array
	 */
	public function plugin_link( $item, $registration ) {
		$actions                  = array();
		$installed_plugins        = get_plugins();
		$item['sanitized_plugin'] = $item['name'];

		// We have a repo plugin.
		if ( ! $item['version'] ) {
			$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
		}

		$disable_class = '';
		$data_version  = '';
		if ( ( 'LayerSlider' == $item['slug'] || 'revslider' == $item['slug'] ) && empty( $registration ) ) {
			$disable_class = ' disabled cyprus-no-token';
		}

		// We need to display the 'Install' hover link.
		if ( ! isset( $installed_plugins[ $item['file_path'] ] ) ) {
			if ( ! $disable_class ) {
				$url = esc_url( wp_nonce_url(
					add_query_arg(
						array(
							'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
							'plugin'        => urlencode( $item['slug'] ),
							'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
							'tgmpa-install' => 'install-plugin',
							'return_url'    => 'cyprus-plugins',
						),
						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-install',
					'tgmpa-nonce'
				) );
			} else {
				$url = '#';
			}
			$actions = array(
				/* translators: plugin name */
				'install' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '"' . $data_version . ' title="' . sprintf( esc_attr__( 'Install %s', 'cyprus' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Install', 'cyprus' ) . '</a>',
			);
		} elseif ( is_plugin_inactive( $item['file_path'] ) ) {
			// We need to display the 'Activate' hover link.
			if ( ! $disable_class ) {
				$url = esc_url( add_query_arg(
					array(
						'plugin'                => urlencode( $item['slug'] ),
						'plugin_name'           => urlencode( $item['sanitized_plugin'] ),
						'cyprus-activate'       => 'activate-plugin',
						'cyprus-activate-nonce' => wp_create_nonce( 'cyprus-activate' ),
					),
					admin_url( 'admin.php?page=cyprus-plugins' )
				) );
			} else {
				$url = '#';
			}

			$actions = array(
				/* translators: plugin name */
				'activate' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '"' . $data_version . ' title="' . sprintf( esc_attr__( 'Activate %s', 'cyprus' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Activate', 'cyprus' ) . '</a>',
			);
		} elseif ( version_compare( $installed_plugins[ $item['file_path'] ]['Version'], $item['version'], '<' ) ) {
			$disable_class = '';
			// We need to display the 'Update' hover link.
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'page'         => urlencode( TGM_Plugin_Activation::$instance->menu ),
						'plugin'       => urlencode( $item['slug'] ),
						'tgmpa-update' => 'update-plugin',
						'version'      => urlencode( $item['version'] ),
						'return_url'   => 'cyprus-plugins',
					),
					TGM_Plugin_Activation::$instance->get_tgmpa_url()
				),
				'tgmpa-update',
				'tgmpa-nonce'
			);
			if ( ( 'LayerSlider' == $item['slug'] || 'revslider' == $item['slug'] ) && empty( $registration ) ) {
				$disable_class = ' disabled cyprus-no-token';
			}
			$actions = array(
				/* translators: plugin name */
				'update' => '<a href="' . $url . '" class="button button-primary' . $disable_class . '" title="' . sprintf( esc_attr__( 'Update %s', 'cyprus' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Update', 'cyprus' ) . '</a>',
			);
		} elseif ( is_plugin_active( $item['file_path'] ) ) {
			$url     = esc_url( add_query_arg(
				array(
					'plugin'                  => urlencode( $item['slug'] ),
					'plugin_name'             => urlencode( $item['sanitized_plugin'] ),
					'cyprus-deactivate'       => 'deactivate-plugin',
					'cyprus-deactivate-nonce' => wp_create_nonce( 'cyprus-deactivate' ),
				),
				admin_url( 'admin.php?page=cyprus-plugins' )
			) );
			$actions = array(
				/* translators: plugin name */
				'deactivate' => '<a href="' . $url . '" class="button button-primary" title="' . sprintf( esc_attr__( 'Deactivate %s', 'cyprus' ), $item['sanitized_plugin'] ) . '">' . esc_attr__( 'Deactivate', 'cyprus' ) . '</a>',
			);
		}

		return $actions;
	}
}

new Cyprus_Admin_Pages;
