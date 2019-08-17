<?php
/**
 * Tweaks for the <head> of the document.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Tweaks for the <head> of the document.
 */
class Cyprus_Head extends Cyprus_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->add_action( 'init', 'disable_emojis' );
		$this->add_filter( 'language_attributes', 'add_opengraph_doctype' );
		$this->add_action( 'wp_head', 'essential_meta', 1 );
		$this->add_action( 'wp_head', 'insert_favicons', 2 );
		$this->add_action( 'wp_head', 'insert_og_meta', 5 );
		$this->add_action( 'wp_head', 'custom_footer_code', 999 );

		if ( ! function_exists( '_wp_render_title_tag' ) ) {
			$this->add_action( 'wp_head', 'render_title' );
		}

		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
	}

	/**
	 * Disable WordPress emojis functionality
	 */
	public function disable_emojis() {

		if ( ! cyprus_get_settings( 'disable_emojis', false ) ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );

	}

	/**
	 * Adding the Open Graph in the Language Attributes
	 *
	 * @param  string $output The output we want to process/filter.
	 * @return string The altered doctype
	 */
	public function add_opengraph_doctype( $output ) {

		// Early exit if we don't need to continue any further.
		if ( function_exists( 'rank_math' ) || ! cyprus_get_settings( 'status_opengraph' ) ) {
			return $output;
		}

		return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
	}

	/**
	 * Add essential metas
	 */
	public function essential_meta() {

		$metas = array();

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) ) ) {
			$metas[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
		}

		$metas[] = sprintf( '<meta charset="%s">', esc_attr( get_bloginfo( 'charset' ) ) );
		$metas[] = '<link rel="profile" href="http://gmpg.org/xfn/11">';

		if ( 'open' === get_option( 'default_ping_status' ) ) {
			$metas[] = sprintf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}

		if ( cyprus_get_settings( 'prefetching' ) ) {
			if ( is_front_page() ) {

				$front_url = esc_url( get_permalink() );
				$metas[]   = sprintf( '<link rel="prefetch" href="%s">', $front_url );
				$metas[]   = sprintf( '<link rel="prerender" href="%s">', $front_url );

			} elseif ( is_singular() ) {

				$home_url = esc_url( home_url() );
				$metas[]  = sprintf( '<link rel="prefetch" href="%s">', $home_url );
				$metas[]  = sprintf( '<link rel="prerender" href="%s">', $home_url );

			}
		}

		$is_ipad       = cyprus_is_ipad();
		$is_responsive = cyprus_get_settings( 'mts_responsive' );

		if ( $is_responsive && $is_ipad ) {
			$metas[] = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
		} elseif ( $is_responsive ) {
			$metas[] = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		}

		$metas[] = sprintf( '<meta itemprop="name" content="%s">', get_bloginfo( 'name' ) );
		$metas[] = sprintf( '<meta itemprop="url" content="%s">', esc_url( site_url() ) );

		if ( is_singular() ) {
			global $post;
			$user_info = get_userdata( $post->post_author );

			if ( $user_info && ! empty( $user_info->first_name ) && ! empty( $user_info->last_name ) ) {
				$metas[] = sprintf( '<meta itemprop="creator accountablePerson" content="%s">', $user_info->first_name . ' ' . $user_info->last_name );
			}
		}

		echo join( "\n", $metas );
	}

	/**
	 * Cyprus favicon as set in theme options
	 * These are added to the <head> of the page using the 'wp_head' action.
	 *
	 * @access  public
	 * @since   4.0
	 * @return  void
	 */
	public function insert_favicons() {

		if ( ! empty( cyprus_get_settings( 'mts_favicon' ) ) ) : ?>
			<link rel="shortcut icon" href="<?php echo esc_url( wp_get_attachment_url( cyprus_get_settings( 'mts_favicon' ) ) ); ?>" type="image/x-icon" />
		<?php elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) : ?>
			<link rel="icon" href="<?php echo esc_url( get_site_icon_url( 32 ) ); ?>" sizes="32x32" />
			<link rel="icon" href="<?php echo esc_url( get_site_icon_url( 192 ) ); ?>" sizes="192x192" />
		<?php endif; ?>

		<?php if ( ! empty( cyprus_get_settings( 'mts_touch_icon' ) ) ) : ?>
			<link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( wp_get_attachment_url( cyprus_get_settings( 'mts_touch_icon' ) ) ); ?>">
		<?php elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) : ?>
			<link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( get_site_icon_url( 57 ) ); ?>">
		<?php endif; ?>

		<?php if ( ! empty( cyprus_get_settings( 'mts_metro_icon' ) ) ) : ?>
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-TileImage" content="<?php echo esc_url( wp_get_attachment_url( cyprus_get_settings( 'mts_metro_icon' ) ) ); ?>">
		<?php elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) : ?>
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-TileImage" content="<?php echo esc_url( get_site_icon_url( 270 ) ); ?>">
		<?php
		endif;
	}

	/**
	 * Cyprus extra OpenGraph tags
	 * These are added to the <head> of the page using the 'wp_head' action.
	 *
	 * @access  public
	 * @return void
	 */
	public function insert_og_meta() {

		// Early exit if we don't need to continue any further.
		if ( ! cyprus_get_settings( 'status_opengraph' ) ) {
			return;
		}

		// Early exit if this is not a singular post/page/cpt.
		if ( ! is_singular() ) {
			return;
		}

		global $post;

		$image = '';
		if ( ! has_post_thumbnail( $post->ID ) ) {
			$logo = cyprus_get_settings( 'mts_logo' );
			if ( $logo ) {
				$image = $logo;
			}
		} else {
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			$image         = esc_attr( $thumbnail_src[0] );
		}
		?>

		<meta property="og:title" content="<?php echo strip_tags( str_replace( array( '"', "'" ), array( '&quot;', '&#39;' ), $post->post_title ) ); ?>"/>
		<meta property="og:type" content="article"/>
		<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>"/>
		<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
		<meta property="og:description" content="<?php the_excerpt(); ?>"/>

		<?php if ( ! empty( $image ) ) : ?>
			<meta property="og:image" content="<?php echo esc_url( $image ); ?>"/>
		<?php
		endif;
	}

	/**
	 * Renders the title.
	 */
	public function render_title() {
		echo '<title>';
		wp_title( '' );
		echo '</title>';
	}

	/**
	 * Add tracking field code
	 */
	public function custom_footer_code() {
		echo cyprus_get_settings( 'mts_header_code' );
	}
}

/**
 * Init
 */
new Cyprus_Head;
