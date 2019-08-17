<?php
/**
 * Template Name: Contact Page
 * The template for displaying the page with a slug of `contact`.
 */
get_header(); ?>

<div id="wrapper" class="<?php cyprus_single_page_class(); ?>">

	<?php cyprus_single_featured_image_effect(); ?>

	<div class="container clearfix">

		<article class="<?php cyprus_article_class(); ?>">
			<div id="content_box" >
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<div id="post-<?php the_ID(); ?>" <?php post_class( 'g post' ); ?>>

						<div class="single_page">
							<?php
							if ( cyprus_get_settings( 'mts_breadcrumb' ) ) {
								if ( function_exists( 'rank_math' ) && rank_math()->breadcrumbs ) {
									rank_math_the_breadcrumbs();
								} else {
									cyprus_breadcrumbs();
								}
							}
							?>
							<header>
								<h1 class="title entry-title"><?php the_title(); ?></h1>
							</header>
							<div class="post-content box mark-links entry-content">
								<?php the_content(); ?>
								<?php
									wp_link_pages(
										array(
											'before'       => '<div class="pagination">',
											'after'        => '</div>',
											'link_before'  => '<span class="current"><span class="currenttext">',
											'link_after'   => '</span></span>',
											'next_or_number' => 'next_and_number',
											'nextpagelink' => __( 'Next', 'cyprus' ),
											'previouspagelink' => __( 'Previous', 'cyprus' ),
											'pagelink'     => '%',
											'echo'         => 1,
										)
									);
									?>
								<?php mts_contact_form(); ?>
							</div><!--.post-content box mark-links-->
						</div>
					</div>
					<?php //comments_template( '', true ); ?>
				<?php endwhile; ?>
			<?php endif; ?>
			</div>
		</article>
		<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>
