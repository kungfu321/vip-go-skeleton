<?php
/**
 * The featured layouts
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Class for featured layouts.
 */
class Cyprus_Featured_Layouts extends Cyprus_Base {

	/**
	 * Current layout.
	 *
	 * @var string
	 */
	public $current = null;

	/**
	 * Hold sections.
	 *
	 * @var array
	 */
	private $sections = null;

	/**
	 * The Construct
	 */
	public function __construct() {
		$this->sections = cyprus_get_settings( 'mts_featured_categories' );
	}

	/**
	 * Render layout
	 */
	public function render() {

		if ( empty( $this->sections ) ) {
			$this->current = array(
				'layout'         => 'default',
				'posts_layout'   => 'layout-default',
				'excerpt_length' => 29,
				'unique_id'      => 'nothing',
				'is_latest'      => true,
			);

			get_template_part( 'template-parts/posts', 'default' );
			return;
		}

		if ( is_paged() && 'latest' !== $section['mts_featured_category'] ) {
			get_template_part( 'template-parts/posts', 'default' );
			return;
		}

		foreach ( $this->sections as $section ) :

			$section = wp_parse_args( array_filter( $section ), array(
				'mts_thumb_layout'                 => '',
				'mts_featured_category_postsnum'   => 3,
				'mts_featured_category_excerpt'    => 29,
				'mts_featured_category_background' => '',
				'mts_featured_category_margin'     => '',
				'mts_featured_category_padding'    => '',
				'unique_id'                        => '',
			) );

			$category     = $section['mts_featured_category'];
			$posts_layout = $section['mts_thumb_layout'];

			if ( 'latest' === $category && ( 'layout-3' === $posts_layout || 'layout-1' === $posts_layout ) ) {
				global $wp_query;
				$wp_query = new WP_Query( array(
					'posts_per_page'      => 1,
					'ignore_sticky_posts' => 1,
				) );
			}
			if ( 'latest' !== $category ) {
				if ( 'layout-3' === $posts_layout || 'layout-1' === $posts_layout ) {
					$section['mts_featured_category_postsnum'] = 1;
				}
				if ( 'layout-2' !== $posts_layout ) {
					$this->category_posts( $category, $section['mts_featured_category_postsnum'] );
				}
			}

			$layout = $posts_layout;
			if ( 'layout-default' === $posts_layout ) {
				$layout = 'default';
			}

			$this->current = array(
				'layout'           => $layout,
				'category'         => $category,
				'unique_id'        => $section['unique_id'],
				'is_latest'        => 'latest' !== $category,
				'posts_layout'     => $posts_layout,
				'posts_count'      => $section['mts_featured_category_postsnum'],
				'excerpt_length'   => $section['mts_featured_category_excerpt'],
				'posts_background' => $section['mts_featured_category_background'],
				'posts_margin'     => $section['mts_featured_category_margin'],
				'posts_padding'    => $section['mts_featured_category_padding'],
			);

			if ( have_posts() ) {
				get_template_part( 'template-parts/posts', $layout );
			}

		endforeach;
	}

	/**
	 * Genrate article classes
	 */
	public function get_article_class() {
		$classes   = cyprus_get_article_class();
		$classes[] = 'layout-' . $this->current['unique_id'];
		$classes[] = $this->current['layout'] . '-container';
		$classes[] = 'clearfix';

		echo join( ' ', $classes );
	}

	/**
	 * Set category in main query
	 *
	 * @param  int $category    Category ID.
	 * @param  int $posts_count Number of posts.
	 */
	public function category_posts( $category, $posts_count ) {
		global $wp_query;

		$paged    = max( get_query_var( 'paged' ), 1 );
		$wp_query = new WP_Query( 'ignore_sticky_posts=1&category_name=' . $category . '&posts_per_page=' . $posts_count );
	}

	/**
	 * Get section title.
	 *
	 * @param int $category Category id or name.
	 */
	public function get_section_title( $category = false ) {
		if ( 1 === cyprus_get_settings( 'mts_featured_category_title_' . $this->current['unique_id'] ) ) :
			$category = ! $category ? $this->current['category'] : $category;
		?>
		<div class="title-container title-id-<?php echo $this->current['unique_id']; ?>">
			<?php
			if ( ! $this->current['is_latest'] && 'latest' === $category ) {
				echo '<h3 class="featured-category-title">';
				_e( 'Latest', 'cyprus' );
				echo '</div>';
				return;
			}

			$category = get_category_by_slug( $category );
			?>
			<h3 class="featured-category-title"><a href="<?php echo esc_url( get_category_link( $category ) ); ?>" title="<?php echo esc_attr( $category->name ); ?>"><?php echo esc_html( $category->name ); ?></a></h3>
		</div>
		<?php
		endif;
	}

	/**
	 * Get post title
	 *
	 * @param  boolean $meta     Display meta.
	 * @param  string  $position Position to display meta at.
	 */
	public function get_post_title( $meta = true, $position = 'bottom' ) {

		if ( $meta && 'top' === $position ) {
			cyprus_the_post_meta( 'home', $this->current['unique_id'] );
		}
		?>
		<header>
			<h2 class="title front-view-title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php echo the_title(); ?></a>
			</h2>
			<?php
			if ( $meta && 'bottom' === $position ) {
				cyprus_the_post_meta( 'home', $this->current['unique_id'] );
			}
			?>
		</header>
		<?php
	}

	/**
	 * Get post thumbnail according to layout
	 *
	 * @param  integer $j      Even or Odd.
	 * @param  boolean $avatar Display avatar.
	 */
	public function get_post_thumbnail( $j = 0, $avatar = false ) {
		$default_thumb = 'layout-default' === $this->current['posts_layout'] ? 'cyprus-featured' : 'cyprus-widgetfull';
		$thumbs        = array(
			'layout-2' => 1 === $j ? 'cyprus-layout-2' : 'cyprus-layout-small-2',
			'layout-4' => 1 === $j ? 'cyprus-layout-4' : 'cyprus-layout-small-2',
		);
		$size          = isset( $thumbs[ $this->current['posts_layout'] ] ) ? $thumbs[ $this->current['posts_layout'] ] : $default_thumb;
		?>
		<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" id="featured-thumbnail" class="post-image post-image-left <?php echo $size; ?>">
			<div class="featured-thumbnail">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( $size, array( 'title' => '' ) );
				} else {
					echo '<img src="' . get_template_directory_uri() . '/images/nothumb-' . $size . '.png" alt="' . __( 'No Preview', 'cyprus' ) . '"  class="wp-post-image" />';
				}
				?>
			</div>
			<?php
			if ( function_exists( 'wp_review_show_total' ) ) {
				wp_review_show_total( true, 'latestPost-review-wrapper' );
			}

			// Author gravatar.
			if ( $avatar && function_exists( 'get_avatar' ) ) {
				echo get_avatar( get_the_author_meta( 'email' ), '57' ); // Gravatar size.
			}
			?>
		</a>
		<?php
	}

	/**
	 * Get post content
	 *
	 * @param  boolean $meta Display meta.
	 */
	public function get_post_content( $meta = true ) {
		?>

		<div class="front-view-content">
			<?php echo cyprus_excerpt( $this->current['excerpt_length'] ); ?>
		</div>

		<?php
		if ( $meta ) {
			cyprus_the_post_meta( 'home', $this->current['unique_id'] );
		}

		if ( 1 === cyprus_get_settings( 'readmore_' . $this->current['unique_id'] ) ) :
			cyprus_readmore();
		endif;
	}

	/**
	 * Get sidebar
	 */
	public function get_sidebar() {
		$cat_name = __( 'Latest ', 'cyprus' );
		if ( 'latest' !== $this->current['category'] ) {
			$cat_name = ucwords( cyprus_get_cat_name( $this->current['category'] ) );
		}
		?>
		<aside id="sidebar" class="sidebar c-4-12 post-<?php echo $this->current['layout']; ?>" role="complementary" itemscope itemtype="http://schema.org/WPSideBar">
			<?php
			dynamic_sidebar( sanitize_title( strtolower( 'post-' . $this->current['layout'] . $cat_name ) ) );
			?>
		</aside>
		<?php
	}

	/**
	 * Get post pagination
	 */
	public function get_post_pagination() {
		if ( ! $this->current['is_latest'] ) {
			cyprus_pagination( cyprus_get_settings( 'mts_pagenavigation_type' ) );
		}
		wp_reset_query();
	}
}
