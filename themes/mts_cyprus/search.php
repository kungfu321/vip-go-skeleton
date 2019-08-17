<?php
/**
 * The template for displaying search results pages.
 *
 * @package Cyprus
 */

get_header(); ?>

	<div id="wrapper">

		<div class="container">

			<div class="article">

				<?php cyprus_action( 'before_content' ); ?>

				<div id="content_box">

					<?php if ( have_posts() ) : ?>

						<h1 class="page-title">
							<?php
							// translators: search query.
							printf( esc_html__( 'Search Results for: %s', 'cyprus' ), '<span>' . get_search_query() . '</span>' );
							?>
						</h1>
					<?php else : ?>
						<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'cyprus' ); ?></h1>
					<?php endif; ?>

					<?php
					if ( 'above' === cyprus_get_settings( 'search_position' ) ) {
						get_search_form();
					}

					$j = 0;
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							cyprus_blog_articles( 'default' );
						}
					} else {
						get_template_part( 'template-parts/no', 'results' );
					}

					if ( 0 !== $j ) {
						cyprus_pagination( cyprus_get_settings( 'mts_pagenavigation_type' ) );
					}

					if ( 'below' === cyprus_get_settings( 'search_position' ) ) {
						get_search_form();
					}
					?>
				</div>

				<?php cyprus_action( 'after_content' ); ?>

			</div>

			<?php get_sidebar(); ?>

		</div>

<?php
get_footer();
