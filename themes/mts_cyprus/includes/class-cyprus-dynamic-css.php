<?php
/**
 * Handle generating the dynamic CSS.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Class dynamic CSS.
 *
 * @link    http://aristath.github.io/css/wordpress/2015/04/05/optimize-inline-css.html
 * @link    http://aristath.github.io/blog/avoid-dynamic-css-in-head
 */
class Cyprus_Dynamic_CSS extends Cyprus_Base {

	/**
	 * Dynamic CSS output mode.
	 *
	 * @var string
	 */
	public $mode = '';

	/**
	 * Dynamic CSS Posts option key.
	 *
	 * @var string
	 */
	private $posts_key = 'cyprus_dynamic_css_posts';

	/**
	 * Dynamic CSS time option key.
	 *
	 * @var string
	 */
	private $time_key = 'cyprus_dynamic_css_time';

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->add_options();

		// Set mode.
		$this->add_action( 'wp', 'set_mode', 20 );

		// When a post is saved, reset its caches to force-regenerate the CSS.
		$this->add_action( 'save_post', 'reset_post_transient' );
		$this->add_action( 'save_post', 'post_update_option' );

		// When we change the options, reset all caches so that all CSS can be re-generated.
		$keys = array( '', '_homepage', '_sidebars', '_single', '_typography' );
		foreach ( $keys as $key ) {
			$this->add_action( 'update_option_' . cyprus()->settings->key . $key, 'reset_all_caches' );
		}
		$this->add_action( 'customize_save_after', 'reset_all_caches' );

		// Add the CSS.
		$this->add_action( 'wp_enqueue_scripts', 'enqueue_dynamic_css', 10000 );
		$this->add_action( 'wp_head', 'add_inline_css', 999 );
	}

	/**
	 * Enqueue the dynamic CSS.
	 */
	public function enqueue_dynamic_css() {
		$ver = cyprus()->get_version();

		// Yay! we're using a file for our CSS, so enqueue it.
		if ( 'file' === $this->mode ) {
			wp_enqueue_style( 'cyprus-dynamic-css', $this->file( 'uri' ), null, $ver );
		}
	}

	/**
	 * Add Inline CSS.
	 */
	public function add_inline_css() {
		global $wp_customize;

		// Inline Dynamic CSS.
		// This is here because we need it after all cyprus CSS
		// and W3TC can combine it incorrectly.
		if ( 'inline' === $this->mode || $wp_customize ) {
			echo "<style id='cyprus-stylesheet-inline-css' type='text/css'>" . $this->make_dynamic_css() . '</style>';
		}
	}

	/**
	 * Determine if we're using file mode or inline mode.
	 *
	 * @return string file/inline.
	 */
	public function set_mode() {
		global $wp_customize;

		$mode = cyprus_get_settings( 'dynamic_css_compiler' ) ? 'file' : 'inline';

		// ALWAYS use 'inline' mode when in the customizer.
		if ( $wp_customize ) {
			$this->mode = 'inline';
			return;
		}

		// Additional checks for file mode.
		if ( 'file' == $mode && $this->needs_update() ) {

			// Only allow processing 1 file every 5 seconds.
			$current_time = (int) time();
			$last_time    = (int) get_option( $this->time_key );

			if ( 5 <= ( $current_time - $last_time ) ) {

				// If it's been more than 5 seconds since we last compiled a file
				// then attempt to write to the file.
				// If the file-write succeeds then set mode to 'file'.
				// If the file-write fails then set mode to 'inline'.
				$mode = ( $this->can_write() && $this->make_css() ) ? 'file' : 'inline';

				// If the file exists then set mode to 'file'
				// If it does not exist then set mode to 'inline'.
				if ( 'file' == $mode ) {
					$mode = ( file_exists( $this->file( 'path' ) ) ) ? 'file' : 'inline';
				}
			} else {

				// It's been less than 5 seconds since we last compiled a CSS file.
				// In order to prevent server meltdowns on weak servers we'll use inline mode instead.
				$mode = 'inline';
			}
		}

		$this->mode = $mode;
	}

	/**
	 * Do we need to update the CSS file?
	 *
	 * @param string $mode The compiling mode we're using.
	 * @return bool
	 */
	public function needs_update( $mode = 'file' ) {

		// Get the $posts_key option from the DB.
		$option = get_option( $this->posts_key, array() );

		// Get the current page ID.
		$page_id = ( cyprus()->get_page_id() ) ? cyprus()->get_page_id() : 'global';

		// Get the current page file path.
		$file_path = $this->file( 'path' );

		// If the CSS file does not exist then we definitely need to regenerate the CSS.
		if ( 'file' === $mode && ! file_exists( $file_path ) ) {
			return true;
		}

		// Check if the time of the dynamic-css.php file is newer than the css file itself.
		// If yes, then we need to update the css.
		// This is primarily added here for development purposes.
		$dynamic_css_script = $this->includes_path() . 'dynamic-css.php';
		if ( 'file' === $mode && filemtime( $dynamic_css_script ) > filemtime( $file_path ) ) {
			return true;
		}

		// If the current page ID exists in the array of pages defined in the $this->posts_key option
		// then the page has already been compiled and we don't need to re-compile it.
		// If it's not in the array then it has not been compiled before so we need to update it.
		return ( ! isset( $option[ $page_id ] ) || ! $option[ $page_id ] ) ? true : false;
	}

	/**
	 * Gets the css path or url to the stylesheet.
	 *
	 * @param string $target path/url.
	 * @return string Path or url to the file depending on the $target var.
	 */
	private function file( $target = 'path' ) {

		// Get the blog ID.
		$blog_id = 1;
		if ( is_multisite() ) {
			$current_site = get_blog_details();
			$blog_id      = $current_site->blog_id;
		}

		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();

		// If this is a multisite installation, append the blogid to the filename.
		$blog_id = ( is_multisite() && $blog_id > 1 ) ? '-blog-' . $blog_id : null;
		$page_id = ( cyprus()->get_page_id() ) ? cyprus()->get_page_id() : 'global';

		$file_name   = 'cyprus' . $blog_id . '-' . $page_id . '.css';
		$folder_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'cyprus-styles';

		// The complete path to the file.
		$file_path = $folder_path . DIRECTORY_SEPARATOR . $file_name;

		// Get the URL directory of the stylesheet.
		$css_uri_folder = $upload_dir['baseurl'];

		// Build the URL of the file.
		$css_uri = trailingslashit( $css_uri_folder ) . 'cyprus-styles/' . $file_name;

		// Take care of domain mapping.
		// When using domain mapping we have to make sure that the URL to the file
		// does not include the original domain but instead the mapped domain.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {
			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {
				$mapped_domain   = domain_mapping_siteurl( false );
				$original_domain = get_original_url( 'siteurl' );
				$css_uri         = str_replace( $original_domain, $mapped_domain, $css_uri );
			}
		}

		// Strip protocols from the URL.
		// Make sure we don't have any issues with sites using HTTPS/SSL.
		$css_uri = str_replace( 'https://', '//', $css_uri );
		$css_uri = str_replace( 'http://', '//', $css_uri );

		// Return the path or the URL
		// depending on the $target we have defined when calling this method.
		if ( 'path' === $target ) {
			return $file_path;
		}

		if ( 'url' === $target || 'uri' === $target ) {
			$timestamp = ( file_exists( $file_path ) ) ? '?timestamp=' . filemtime( $file_path ) : '';
			return $css_uri . $timestamp;
		}
	}

	/**
	 * Determines if the CSS file is writable.
	 *
	 * @return bool
	 */
	private function can_write() {

		// Get the blog ID.
		$blog_id = 1;
		if ( is_multisite() ) {
			$current_site = get_blog_details();
			$blog_id      = $current_site->blog_id;
		}

		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();

		// If this is a multisite installation, append the blogid to the filename.
		$blog_id = ( is_multisite() && $blog_id > 1 ) ? '-blog-' . $blog_id : null;
		$page_id = ( cyprus()->get_page_id() ) ? cyprus()->get_page_id() : 'global';

		$file_name   = '/cyprus' . $blog_id . '-' . $page_id . '.css';
		$folder_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'cyprus-styles';

		// Does the folder exist?
		if ( file_exists( $folder_path ) ) {

			// Folder exists, but is it actually writable?
			if ( ! is_writable( $folder_path ) ) {

				// Folder is not writable.
				// Does the file exist?
				if ( ! file_exists( $folder_path . $file_name ) ) {

					// If the file does not exist, then we can't create it
					// since its parent folder is not writable.
					return false;
				} else {

					// The file exists. Is it writable?
					if ( ! is_writable( $folder_path . $file_name ) ) {

						// Nope, it's not writable.
						return false;
					}
				}
			} else {

				// The folder is writable.
				// Does the file exist?
				if ( file_exists( $folder_path . $file_name ) ) {

					// File exists. Is it writable?
					if ( ! is_writable( $folder_path . $file_name ) ) {

						// Nope, it's not writable.
						return false;
					}
				}
			}
		} else {

			// Can we create the folder?
			// returns true if yes and false if not.
			return wp_mkdir_p( $folder_path );
		}

		// If we passed all of the above tests
		// then the file is writable.
		return true;
	}

	/**
	 * This function takes care of creating the CSS.
	 *
	 * @return bool true/false depending on whether the file is successfully created or not.
	 */
	private function make_css() {
		$wp_filesystem = cyprus_init_filesystem();

		// Creates the content of the CSS file.
		// We're adding a warning at the top of the file to prevent users from editing it.
		// The warning is then followed by the actual CSS content.
		$content = $this->make_dynamic_css();

		// When using domain-mapping plugins we have to make sure that any references to the original domain
		// are replaced with references to the mapped domain.
		// We're also stripping protocols from these domains so that there are no issues with SSL certificates.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {

			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {

				// The mapped domain of the site.
				$mapped_domain = domain_mapping_siteurl( false );
				$mapped_domain = str_replace( [ 'https://', 'http://' ], '//', $mapped_domain );

				// The original domain of the site.
				$original_domain = get_original_url( 'siteurl' );
				$original_domain = str_replace( [ 'https://', 'http://' ], '//', $original_domain );

				// Replace original domain with mapped domain.
				$content = str_replace( $original_domain, $mapped_domain, $content );
			}
		}

		// Strip protocols. This helps avoid any issues with https sites.
		$content = str_replace( [ 'https://', 'http://' ], '//', $content );

		// Since we've already checked if the file is writable in the can_write() method (called by the mode() method)
		// it's safe to continue without any additional checks as to the validity of the file.
		if ( ! $wp_filesystem->put_contents( $this->file( 'path' ), $content ) ) {

			// Writing to the file failed.
			return false;
		} else {

			// Writing to the file succeeded.
			// Update the opion in the db so that we know the css for this post has been successfully generated
			// and then return true.
			$page_id            = cyprus()->get_page_id() ? cyprus()->get_page_id() : 'global';
			$option             = get_option( $this->posts_key, array() );
			$option[ $page_id ] = true;
			update_option( $this->posts_key, $option );

			// Update the 'dynamic_css_time' option.
			$this->update_saved_time();

			return true;
		}
	}

	/**
	 * Create settings.
	 */
	private function add_options() {
		/**
		 * The $posts_key option will hold an array of posts that have had their css generated.
		 * We can use that to keep track of which pages need their CSS to be recreated and which don't.
		 */
		add_option( $this->posts_key, array() );
		/**
		 * The $time_key option holds the time the file writer was last used.
		 */
		add_option( $this->time_key, time() );
	}

	/**
	 * Reset the dynamic CSS transient for a post.
	 *
	 * @param int $post_id The ID of the post that's being reset.
	 */
	public function reset_post_transient( $post_id ) {
		delete_transient( 'cyprus_dynamic_css_' . $post_id );
	}

	/**
	 * Update the $posts_key option when a post is saved.
	 * This adds the current post's ID in the array of IDs that the $posts_key option has.
	 *
	 * @param int $post_id The post ID.
	 */
	public function post_update_option( $post_id ) {
		$option             = get_option( $this->posts_key, array() );
		$option[ $post_id ] = false;
		update_option( $this->posts_key, $option );
	}

	/**
	 * Update the $time_key option.
	 * This will save in the db the last time that the compiler has run.
	 *
	 * @return void
	 */
	private function update_saved_time() {
		update_option( $this->time_key, time() );
	}

	/**
	 * This is just a facilitator that will allow us to reset everything.
	 * Its only job is calling the other methods from this class and reset parts of our caches.
	 */
	public function reset_all_caches() {
		$this->reset_all_transients();
		$this->clear_cache();
		$this->global_reset_option();
	}

	/**
	 * Reset ALL CSS transient caches.
	 */
	private function reset_all_transients() {

		global $wpdb;

		// Build the query to delete all cyprus transients and execute the required SQL.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_cyprus_dynamic_css_%'" );
	}

	/**
	 * Clear cache from:
	 *  - W3TC,
	 *  - WordPress Total Cache
	 *  - WPEngine
	 *  - Varnish
	 *
	 * @access public
	 */
	private function clear_cache() {

		// If W3 Total Cache is being used, clear the cache.
		if ( function_exists( 'w3tc_pgcache_flush' ) ) {
			w3tc_pgcache_flush();
		}

		// if WP Super Cache is being used, clear the cache.
		if ( function_exists( 'wp_cache_clean_cache' ) ) {
			global $file_prefix;
			wp_cache_clean_cache( $file_prefix );
		}
		// If SG CachePress is installed, rese its caches.
		if ( class_exists( 'SG_CachePress_Supercacher' ) ) {
			if ( is_callable( array( 'SG_CachePress_Supercacher', 'purge_cache' ) ) ) {
				SG_CachePress_Supercacher::purge_cache();
			}
		}

		// Clear caches on WPEngine-hosted sites.
		if ( class_exists( 'WpeCommon' ) ) {
			WpeCommon::purge_memcached();
			WpeCommon::clear_maxcdn_cache();
			WpeCommon::purge_varnish_cache();
		}

		// Clear Varnish caches.
		if ( cyprus_get_settings( 'dynamic_css_compiler' ) && cyprus_get_settings( 'cache_server_ip' ) ) {
			$this->clear_varnish_cache( $this->file( 'url' ) );
		}
	}

	/**
	 * Clear varnish cache for the dynamic CSS file.
	 *
	 * @param string $url The URL of the file whose cache we want to reset.
	 */
	public function clear_varnish_cache( $url ) {

		// Parse the URL for proxy proxies.
		$p = wp_parse_url( $url );

		$varnish_x_purgemethod = ( isset( $p['query'] ) && ( 'vhp=regex' == $p['query'] ) ) ? 'regex' : 'default';

		// Build a varniship.
		$varniship = get_option( 'vhp_varnish_ip' );
		if ( cyprus_get_settings( 'cache_server_ip' ) ) {
			$varniship = cyprus_get_settings( 'cache_server_ip' );
		} elseif ( defined( 'VHP_VARNISH_IP' ) && VHP_VARNISH_IP != false ) {
			$varniship = VHP_VARNISH_IP;
		}

		// If we made varniship, let it sail.
		$purgeme = ( isset( $varniship ) && null != $varniship ) ? $varniship : $p['host'];

		wp_remote_request( 'http://' . $purgeme,
			array(
				'method'  => 'PURGE',
				'headers' => array(
					'host'           => $p['host'],
					'X-Purge-Method' => $varnish_x_purgemethod,
				),
			)
		);
	}

	/**
	 * Update the $posts_key option when the theme options are saved.
	 */
	public function global_reset_option() {
		update_option( $this->posts_key, array() );
		delete_transient( 'cyprus_dynamic_css_typography_link' );
	}

	/**
	 * Dynamic CSS Generator -------------------------------------------------
	 */

	/**
	 * Return the dynamic CSS.
	 * If possible, it also caches the CSS using WordPress transients.
	 *
	 * @return string the dynamically generated CSS.
	 */
	private function make_dynamic_css() {
		// Get the page ID.
		$c_page_id = cyprus()->get_page_id();

		/**
		 * Do we have WP_DEBUG set to true?
		 * If yes, then do not cache.
		 */
		$cache = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? false : true;

		/**
		 * If the dynamic_css_db_caching option is not set.
		 * or set to off, then do not cache.
		 */
		$cache = ( $cache && ( null == cyprus_get_settings( 'dynamic_css_db_caching' ) || ! cyprus_get_settings( 'dynamic_css_db_caching' ) ) ) ? false : $cache;

		// If we're compiling to file, then do not use transients for caching.
		$cache = ( $cache && 'file' === $this->mode ) ? false : $cache;

		if ( $cache ) {

			// Build the transient name.
			$transient_name = ( $c_page_id ) ? 'cyprus_dynamic_css_' . $c_page_id : 'cyprus_dynamic_css_global';

			/**
			 * Check if the dynamic CSS needs updating.
			 * If it does, then calculate the CSS and then update the transient.
			 */
			if ( $this->needs_update( $this->mode ) ) {

				$dynamic_css = $this->calculate_dynamic_css( true );

				// Set the transient for an hour.
				set_transient( $transient_name, $dynamic_css, 60 * 60 );

				if ( 'inline' === $mode ) {
					$page_id            = ( $c_page_id > 0 ) ? $c_page_id : 'global';
					$option             = get_option( $this->posts_key, array() );
					$option[ $page_id ] = true;
					update_option( $this->posts_key, $option );
				}
			} else {

				/**
				 * Check if the transient exists.
				 * If it does not exist, then generate the CSS and update the transient.
				 */
				$dynamic_css = get_transient( $transient_name );
				if ( false === $dynamic_css ) {

					$dynamic_css = $this->calculate_dynamic_css( true );

					// Set the transient for an hour.
					set_transient( $transient_name, $dynamic_css, 60 * 60 );
				}
			}
		} else {

			$dynamic_css = $this->calculate_dynamic_css( false );
		}

		return $dynamic_css;
	}

	/**
	 * Calculate CSS
	 *
	 * @param  boolean $cached Cache the CSS or not.
	 * @return string
	 */
	private function calculate_dynamic_css( $cached = false ) {

		include_once( $this->includes_path() . 'dynamic-css.php' );

		$dynamic_css = "/********* Compiled - Do not edit *********/\n";

		// Calculate the dynamic CSS.
		$dynamic_css .= $this->dynamic_css_parser( cyprus_dynamic_css_array() );

		// Append Typography Collection to dynamic CSS.
		$dynamic_css .= wp_strip_all_tags( $this->typography_collection_css() );

		// Append the user-entered dynamic CSS.
		$dynamic_css .= wp_strip_all_tags( cyprus_get_settings( 'mts_custom_css' ) );

		if ( $cached ) {
			$dynamic_css .= '/* cached */';
		}

		return $dynamic_css;
	}

	/**
	 * Generate CSS for typography collections
	 *
	 * @return string
	 */
	private function typography_collection_css() {

		$css = '';

		$collection = cyprus_get_settings( 'typography-collections', array() );

		if ( empty( $collection ) ) {
			return $css;
		}

		foreach ( $collection as $font ) {

			if ( empty( trim( $font['css-selectors'] ) ) ) {
				continue;
			}

			if ( empty( $font['font-family'] ) && empty( $font['font-backup'] ) ) {
				continue;
			}

			$out = trim( $font['css-selectors'] ) . '{ ';

			$out .= 'font-family: ';

			if ( ! empty( $font['font-family'] ) ) {
				$out .= "'" . $font['font-family'] . "'" . ( ! empty( $font['font-backup'] ) ? ', ' : '' );
			}

			if ( ! empty( $font['font-backup'] ) ) {
				$out .= $font['font-backup'];
			}

			$out .= ';';

			if ( ! empty( $font['font-weight'] ) ) {
				$out .= 'font-weight: ' . $font['font-weight'] . ';';
			}

			if ( ! empty( $font['font-style'] ) ) {
				$out .= 'font-style: ' . $font['font-style'] . ';';
			}

			if ( ! empty( $font['color'] ) ) {
				$out .= 'color: ' . $font['color'] . ';';
			}

			if ( ! empty( $font['font-size'] ) ) {
				$out .= 'font-size: ' . $font['font-size'] . ';';
			}

			if ( ! empty( $font['line-height'] ) ) {
				$out .= 'line-height: ' . $font['line-height'] . ';';
			}

			if ( ! empty( $font['letter-spacing'] ) ) {
				$out .= 'letter-spacing: ' . $font['letter-spacing'] . ';';
			}

			if ( ! empty( $font['margin-top'] ) ) {
				$out .= 'margin-top: ' . $font['margin-top'] . ';';
			}

			if ( ! empty( $font['margin-bottom'] ) ) {
				$out .= 'margin-bottom: ' . $font['margin-bottom'] . ';';
			}

			if ( ! empty( trim( $font['additional-css'] ) ) ) {
				$out .= trim( $font['additional-css'] );
			}

			$out .= '}';

			$css .= $out;
		}

		return $css;
	}

	/**
	 * Get the array of dynamically-generated CSS and convert it to a string.
	 * Parses the array and adds quotation marks to font families and prefixes for browser-support.
	 *
	 * @param  array $css The CSS array.
	 * @return  string
	 */
	function dynamic_css_parser( $css ) {

		// Prefixes.
		foreach ( $css as $media_query => $elements ) {

			foreach ( $elements as $element => $style_array ) {

				foreach ( (array) $style_array as $property => $value ) {

					if ( empty( $value ) ) {
						continue;
					}

					// @codingStandardsIgnoreStart

					// Font family.
					if ( 'font-family' === $property ) {

						if ( false === strpos( $value, ',' ) && false === strpos( $value, "'" ) && false === strpos( $value, '"' ) ) {
							$value = "'" . $value . "'";
						}
						$css[ $media_query ][ $element ]['font-family'] = $value;
					}

					// Transform.
					elseif ( 'transform' == $property ) {
						//$css[ $media_query ][ $element ]['-webkit-transform'] = $value;
						//$css[ $media_query ][ $element ]['-ms-transform']     = $value;
					}

					// Transition.
					elseif ( 'transition' == $property ) {
						$css[ $media_query ][ $element ]['-webkit-transition'] = $value;
					}

					// Transition-property.
					elseif ( 'transition-property' == $property ) {
						$css[ $media_query ][ $element ]['-webkit-transition-property'] = $value;
					}
					// Linear-gradient.
					elseif ( is_array( $value ) ) {
						foreach ( $value as $subvalue ) {
							if ( false !== strpos( $subvalue, 'linear-gradient' ) ) {
								$css[ $media_query ][ $element ][ $property ][] = '-webkit-' . $subvalue;
							} // calc.
							elseif ( 0 === stripos( $subvalue, 'calc' ) ) {
								$css[ $media_query ][ $element ][ $property ][] = '-webkit-' . $subvalue;
							}
						}
					}

					// @codingStandardsIgnoreEnd
				}
			}
		}

		/**
		 * Process the array of CSS properties and produce the final CSS.
		 */
		$final_css = '';
		foreach ( $css as $media_query => $styles ) {

			$final_css .= ( 'global' != $media_query ) ? $media_query . '{' : '';

			foreach ( $styles as $style => $style_array ) {

				if ( empty( $style_array ) ) {
					continue;
				}

				$final_css .= $style . '{';
				foreach ( (array) $style_array as $property => $value ) {

					if ( $value ) {
						if ( 'additional-css' === $property ) {
							$final_css .= $value;
						} elseif ( is_array( $value ) && ! empty( $value ) ) {
							foreach ( $value as $sub_value ) {
								$final_css .= $property . ':' . $sub_value . ';';
							}
						} else {
							$final_css .= $property . ':' . $value . ';';
						}
					}
				}
				$final_css .= '}';
			}

			$final_css .= ( 'global' != $media_query ) ? '}' : '';

		}

		return apply_filters( 'cyprus_dynamic_css', $final_css );
	}
}

/**
 * Init
 */
new Cyprus_Dynamic_CSS;
