<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Cyprus
 */

get_header(); ?>

	<div id="wrapper">

		<div class="container">

			<div class="<?php cyprus_article_class(); ?>">

				<?php cyprus_action( 'before_content' ); ?>

				<div id="content_box">
					<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );

					$j = 0;
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							cyprus_blog_articles( 'default' );
						}
					}

					if ( 0 !== ++$j ) {
						cyprus_pagination( cyprus_get_settings( 'mts_pagenavigation_type' ) );
					}
					?>
				</div>

				<?php cyprus_action( 'after_content' ); ?>

			</div>

			<?php get_sidebar(); ?>

		</div>

<?php
get_footer();
