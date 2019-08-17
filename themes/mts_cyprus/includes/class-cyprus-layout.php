<?php
/**
 * Set cyprus layout according to options
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Layout manager.
 */
class Cyprus_Layout extends Cyprus_Base {

	/**
	 * The Construct
	 */
	public function __construct() {
		$this->add_action( 'wp', 'set_slider' );
		$this->add_action( 'cyprus_before_wrapper', 'add_header' );
		$this->add_action( 'cyprus_single_top', 'full_single_post_header' );
		$this->add_action( 'cyprus_single_post_header', 'single_post_header' );
	}

	/**
	 * Add header
	 */
	public function add_header() {
		$hash = array(
			'regular_header'     => 'default',
			'logo_in_nav_header' => '2',
		);

		$layout = cyprus_get_settings( 'mts_header_style' );
		get_template_part( 'template-parts/header/header', isset( $hash[ $layout ] ) ? $hash[ $layout ] : $layout );
	}

	/**
	 * Set slider
	 */
	public function set_slider() {
		$action = 'default' !== cyprus_get_settings( 'mts_slider_layout' ) ? 'cyprus_before_wrapper' : 'cyprus_start_content_box';
		if ( is_home() ) {
			$this->add_action( $action, 'add_slider' );
		}
	}

	/**
	 * Add slider
	 */
	public function add_slider() {
		get_template_part( 'template-parts/slider', cyprus_get_settings( 'mts_slider_layout' ) );
	}

	/**
	 * Single post header if fullwidth layout
	 */
	public function full_single_post_header() {
		$img_size = cyprus_get_settings( 'featured_image_size' );

		if ( 'full' === $img_size ) {
		?>
			<header class="single-full-header">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						if ( 1 === cyprus_get_settings( 'mts_show_featured' ) ) {
							the_post_thumbnail( 'full', array( 'class' => 'full-featured-image' ) );
							echo '<div class="overlay"></div>';
						}
						?>
						<div class="content">
							<div class="container">
								<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
								<?php
								// Author gravatar.
								if ( 1 === cyprus_get_settings( 'author_image_on_full' ) && 1 === cyprus_get_settings( 'mts_show_featured' ) && function_exists( 'get_avatar' ) ) {
									echo get_avatar( get_the_author_meta( 'email' ), '70' ); // Gravatar size.
								}
								?>
								<?php cyprus_the_post_meta( 'single' ); ?>
							</div>
						</div>
					<?php
					endwhile;
				endif; /* end loop */
				?>
			</header><!--.headline_area-->
		<?php
		}

	}

	/**
	 * Single post header
	 */
	public function single_post_header() {

		$img_size = cyprus_get_settings( 'featured_image_size' );

		if ( 'default' === $img_size ) {
		?>
			<header>
				<?php
				if ( 1 === cyprus_get_settings( 'mts_show_featured' ) ) {
					the_post_thumbnail( 'full', array( 'class' => 'single-featured-image' ) );
				}
				?>
				<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
				<?php cyprus_the_post_meta( 'single' ); ?>
			</header><!--.headline_area-->
		<?php
		}
	}
}

// Init.
new Cyprus_Layout;
