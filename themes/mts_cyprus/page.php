<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Cyprus
 */

get_header(); ?>

	<div id="wrapper" class="<?php cyprus_single_page_class(); ?>">

		<?php cyprus_single_featured_image_effect(); ?>

		<div class="container clearfix">

			<article class="<?php cyprus_article_class(); ?>">

				<?php cyprus_action( 'before_content' ); ?>

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
							?>
							<div class="single_page">

								<header>
									<h1 class="title entry-title"><?php the_title(); ?></h1>
								</header>

								<div class="post-content box mark-links entry-content">
									<?php
									if ( ! empty( cyprus_get_settings( 'mts_social_buttons_on_pages' ) ) && null !== cyprus_get_settings( 'mts_social_button_position' ) && 'top' === cyprus_get_settings( 'mts_social_button_position' ) ) {
										cyprus_social_buttons();
									}

									the_content();

									// Single Page Pagination.
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

									if ( ( ! empty( cyprus_get_settings( 'mts_social_buttons_on_pages' ) ) && null !== cyprus_get_settings( 'mts_social_button_position' ) ) && 'top' !== cyprus_get_settings( 'mts_social_button_position' ) ) {
										cyprus_social_buttons();
									}
									?>

								</div><!--.post-content box mark-links-->

							</div>

						</div>
						<?php
						// Comment area.
						comments_template( '', true );

					endwhile;

				endif;
				?>

				</div>

			</article>

		<?php get_sidebar(); ?>

	</div>

<?php
get_footer();
