<?php
/**
 * Sidebars
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Single sections
 */
class Cyprus_Single_Sections extends Cyprus_Base {

	public function render() {
	?>
	<article class="<?php cyprus_article_class(); ?>">
		<div id="content_box" >
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<div id="post-<?php the_ID(); ?>" <?php post_class( 'g post' ); ?>>
						<?php
						if ( 1 === cyprus_get_settings( 'mts_breadcrumb' ) ) {
							cyprus_breadcrumbs();
						}

						// Single post parts ordering.
						if ( ( null !== cyprus_get_settings( 'mts_single_post_layout' ) ) && is_array( cyprus_get_settings( 'mts_single_post_layout' ) ) && array_key_exists( 'enabled', cyprus_get_settings( 'mts_single_post_layout' ) ) ) {
							$single_post_parts = cyprus_get_settings( 'mts_single_post_layout' )['enabled'];
						} else {
							$single_post_parts = array(
								'content'   => 'content',
								'related'   => 'related',
								'author'    => 'author',
								'subscribe' => 'subscribe',
							);
						}

						foreach ( $single_post_parts as $part => $label ) {
							switch ( $part ) {
								// Content area.
								case 'content':
									cyprus()->single_sections->single_post();
									break;

								// Post tags.
								case 'tags':
									cyprus_post_tags( '<div class="tags"><span class="tagtext">' . esc_html__( 'Tags', 'cyprus' ) . ':</span>', ', ' );
									break;

								// Related Posts.
								case 'related':
									if ( 'default' === cyprus_get_settings( 'related_posts_position' ) ) {
										cyprus_related_posts();
									}
									break;

								// Author box.
								case 'author':
									cyprus()->single_sections->single_author_section();
									break;

								// Subscribe box.
								case 'subscribe':
									cyprus()->single_sections->single_subscribe_box();
									break;
							}
						}
						?>
					</div><!--.g post-->
				<?php
				// Comment area.
				comments_template( '', true );

				endwhile;
			endif; /* end loop */
			?>
		</div>
	</article>
	<?php
	}

	public function single_post() {
		?>
		<div class="single_post">

			<?php cyprus_action( 'single_post_header' ); ?>

			<div class="post-single-content box mark-links entry-content">

				<?php
				$this->single_post_ads( 'above' );

				$this->single_post_social_icons( 'above' );
				?>

				<div class="thecontent">
					<?php the_content(); ?>
				</div>

				<?php
				$this->single_post_pagination();

				$this->single_post_ads( 'below' );

				$this->single_post_social_icons( 'below' );
				?>

			</div><!--.post-single-content-->
		</div><!--.single_post-->
		<?php
	}

	public function single_post_pagination() {
		// Single Post Pagination.
		$args = array(
			'before'           => '<div class="pagination">',
			'after'            => '</div>',
			'link_before'      => '<span class="current"><span class="currenttext">',
			'link_after'       => '</span></span>',
			'next_or_number'   => 'next_and_number',
			'nextpagelink'     => __( 'Next', 'cyprus' ),
			'previouspagelink' => __( 'Previous', 'cyprus' ),
			'pagelink'         => '%',
			'echo'             => 1,
		);
		wp_link_pages( $args );
	}

	public function single_author_section() {
	?>
		<div class="postauthor">
			<h4><?php esc_html_e( 'About The Author', 'cyprus' ); ?></h4>
			<?php
			// Author gravatar.
			if ( function_exists( 'get_avatar' ) ) {
				echo get_avatar( get_the_author_meta( 'email' ), '100' ); // Gravatar size.
			}
			?>
			<h5 class="vcard author">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="fn">
					<?php the_author_meta( 'display_name' ); ?>
				</a>
			</h5>
			<p><?php the_author_meta( 'description' ); ?></p>
		</div>
	<?php
	}

	public function single_subscribe_box() {
		if ( is_active_sidebar( 'single-subscribe' ) && cyprus_get_settings( 'single_subscribe_box' ) ) {
		?>
			<div class="single-subscribe clear">
				<?php dynamic_sidebar( 'single-subscribe' ); ?>
			</div>
		<?php
		}
	}

	public function single_post_ads( $location ) {
		// Above Content Ad Code.
		if ( ! empty( cyprus_get_settings( 'mts_posttop_adcode' ) ) && 'above' == $location ) {
			$toptime = ! empty( cyprus_get_settings( 'mts_posttop_adcode_time' ) ) ? cyprus_get_settings( 'mts_posttop_adcode_time' ) : '0';
			if ( strcmp( date( 'Y-m-d', strtotime( -$toptime . ' day' ) ), get_the_time( 'Y-m-d' ) ) >= 0 ) {
				?>
				<div class="topad">
					<?php echo do_shortcode( cyprus_get_settings( 'mts_posttop_adcode' ) ); ?>
				</div>
				<?php
			}
		}
		// Below Content Ad Code.
		if ( ! empty( cyprus_get_settings( 'mts_postend_adcode' ) ) && 'below' == $location ) {
			$endtime = ! empty( cyprus_get_settings( 'mts_postend_adcode_time' ) ) ? cyprus_get_settings( 'mts_postend_adcode_time' ) : '0';
			if ( strcmp( date( 'Y-m-d', strtotime( -$endtime . ' day' ) ), get_the_time( 'Y-m-d' ) ) >= 0 ) {
				?>
				<div class="bottomad">
					<?php echo do_shortcode( cyprus_get_settings( 'mts_postend_adcode' ) ); ?>
				</div>
				<?php
			}
		}
	}

	public function single_post_social_icons( $location ) {
		// Social Share icons above content.
		if ( null !== cyprus_get_settings( 'mts_social_button_position' ) && 'top' === cyprus_get_settings( 'mts_social_button_position' ) && 'above' == $location ) {
			cyprus_social_buttons();
		}
		// Social Share icons below content and floating.
		if ( ( null !== cyprus_get_settings( 'mts_social_button_position' ) ) && 'top' !== cyprus_get_settings( 'mts_social_button_position' ) && 'below' == $location ) {
			cyprus_social_buttons();
		}
	}

}
