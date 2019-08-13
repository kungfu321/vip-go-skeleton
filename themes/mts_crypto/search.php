<?php
/**
 * The template for displaying search results pages.
 */
$mts_options = get_option(MTS_THEME_NAME);

get_header(); ?>

<div id="page">
	<div class="article">
		<div id="content_box">
			<h1 class="postsby">
				<span><?php _e("Search Results for:", 'crypto' ); ?></span> <?php the_search_query(); ?>
			</h1>

			<?php 
			if ( empty( $mts_options['mts_featured_categories'] ) ) {
				// default
				$class = 'grid';
				$posts_num = 10;
			} else {
				$section = reset( $mts_options['mts_featured_categories'] );
				$category_id = $section['mts_featured_category'];
				$featured_categories[] = $category_id;
				$posts_num = $section['mts_featured_category_postsnum'];
				$blog_layout = $section['mts_blog_layout'];
				
				if ( 'grid' == $blog_layout ) { $class = "grid"; } 
				if ( 'list' == $blog_layout ) { $class = "list"; }
			}

			?>

			<?php if( 'list' == $blog_layout ) : ?>
				<div class="latestpost-list-layout">
			<?php endif; ?>

			<div class="blog-section-<?php echo isset($class) ? $class : '' ?>-posts clearfix">
				<?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<article class="latestPost excerpt <?php echo isset($class) ? $class : '' ?><?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
						<?php mts_archive_post($blog_layout); ?>
					</article>							
				<?php endwhile; else: ?>
					<div class="no-results">
						<h2><?php _e('We apologize for any inconvenience, please hit back on your browser or use the search form below.', 'crypto' ); ?></h2>
						<?php get_search_form(); ?>
					</div><!--noResults-->
				<?php endif; ?>

				<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
					<?php mts_pagination(); ?>
				<?php } ?>
			</div>	

			<?php if( 'list' == $blog_layout ) : ?>
				<aside id="sidebar" class="sidebar c-4-12 posts-sidebar">
			    	<?php dynamic_sidebar('posts-sidebar-'.$category_id); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>
	</div>
	<?php //get_sidebar(); ?>
<?php get_footer(); ?>