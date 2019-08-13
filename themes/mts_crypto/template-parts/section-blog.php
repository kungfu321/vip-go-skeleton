<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<section class="blog-section">
	<?php if ( !is_paged() ) {
		$featured_categories = array();
		if ( !empty( $mts_options['mts_featured_categories'] ) ) {
			foreach ( $mts_options['mts_featured_categories'] as $section ) {
				$category_id = $section['mts_featured_category'];
				$featured_categories[] = $category_id;
				$posts_num = $section['mts_featured_category_postsnum'];
				$blog_layout = $section['mts_blog_layout'];

				if ( 'grid' == $blog_layout ) { $class = "grid"; } 
				if ( 'list' == $blog_layout ) { $class = "list"; }

				if ( 'latest' == $category_id ) { ?>

					<?php if( 'list' == $blog_layout ) : ?>
						<div class="latestpost-list-layout">
					<?php endif; ?>

					<div class="blog-section-<?php echo isset($class) ? $class : '' ?>-posts clearfix">
						<?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
							<article class="latestPost excerpt <?php echo isset($class) ? $class : '' ?><?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
								<?php mts_archive_post($blog_layout); ?>
							</article>							
						<?php endwhile; endif; ?>
						<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
							<?php mts_pagination(); ?>
						<?php } ?>
					</div>

				<?php } else { // if $category_id != 'latest': ?>

					<?php if( 'list' == $blog_layout ) : ?>
						<div class="latestpost-list-layout">
					<?php endif; ?>

					<div class="blog-section-<?php echo isset($class) ? $class : '' ?>-posts clearfix">
						<h3 class="featured-category-title"><a href="<?php echo esc_url( get_category_link( $category_id ) ); ?>" title="<?php echo esc_attr( get_cat_name( $category_id ) ); ?>"><?php echo esc_html( get_cat_name( $category_id ) ); ?></a></h3>
						<?php $j = 0;
						$cat_query = new WP_Query('cat='.$category_id.'&posts_per_page='.$posts_num);
						if ( $cat_query->have_posts() ) : while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
							<article class="latestPost excerpt <?php echo isset($class) ? $class : '' ?><?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
								<?php mts_archive_post($blog_layout); ?>
							</article>
						<?php
						endwhile; endif; $j++; wp_reset_postdata(); ?>
					</div>

				<?php }

				if( 'list' == $blog_layout ) : ?>
						<aside id="sidebar" class="sidebar c-4-12 posts-sidebar">
					    	<?php dynamic_sidebar('posts-sidebar-'.$category_id); ?>
						</aside>
					</div>
				<?php endif;

			}
		}

	} else { //Paged 
		foreach ( $mts_options['mts_featured_categories'] as $section ) {
			$category_id = $section['mts_featured_category'];
			$blog_layout = $section['mts_blog_layout'];

			if ( 'grid' == $blog_layout ) { $class = "grid"; } 
			if( 'list' == $blog_layout ) { $class = "list"; }

			if ( 'latest' == $category_id ) { ?>

				<?php if( 'list' == $blog_layout ) : ?>
					<div class="latestpost-list-layout">
				<?php endif; ?>

				<div class="blog-section-<?php echo isset($class) ? $class : '' ?>-posts clearfix">
					<?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<article class="latestPost excerpt <?php echo isset($class) ? $class : '' ?><?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
							<?php mts_archive_post($blog_layout); ?>
						</article>
					<?php endwhile; endif; $j++; ?>
					<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
						<?php mts_pagination(); ?>
					<?php } ?>
				</div>

				<?php if( 'list' == $blog_layout ) : ?>
					<aside id="sidebar" class="sidebar c-4-12 posts-sidebar">
				    	<?php dynamic_sidebar('posts-sidebar-'.$category_id); ?>
					</aside>
				<?php endif; ?>

			<?php } ?>
		<?php } ?>

	<?php } ?>
</section>