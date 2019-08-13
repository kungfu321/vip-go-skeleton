<?php
/**
 * The template for displaying archive pages.
 *
 * Used for displaying archive-type pages. These views can be further customized by
 * creating a separate template for each one.
 *
 * - author.php (Author archive)
 * - category.php (Category archive)
 * - date.php (Date archive)
 * - tag.php (Tag archive)
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
?>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<?php get_header(); ?>
<div id="page">
	<div class="<?php mts_article_class(); ?>">
		<div id="content_box">
			<h1 class="postsby">
				<span><?php the_archive_title(); ?></span>
			</h1>
			<p><?php the_archive_description(); ?></p>
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
				<?php if ( is_author() ) { ?>
					<div class="postauthor">
						<div class="postauthor-inner">
							<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '150' );  } ?>
							<h5 class="vcard author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="fn"><?php the_author_meta( 'display_name' ); ?></a></h5>
							<div class="author-description">
								<?php
								$userID = get_current_user_id();
								$facebook = get_the_author_meta( 'facebook', $userID );
								$twitter = get_the_author_meta( 'twitter', $userID );
								$google = get_the_author_meta( 'google', $userID );
								$pinterest = get_the_author_meta( 'pinterest', $userID );
								$stumbleupon = get_the_author_meta( 'stumbleupon', $userID );
								$linkedin = get_the_author_meta( 'linkedin', $userID );

								if(!empty($facebook) || !empty($twitter) || !empty($google) || !empty($pinterest) || !empty($stumbleupon) || !empty($linkedin)){
									echo '<div class="author-social">';
										if(!empty($facebook)){
											echo '<a href="'.$facebook.'" class="facebook"><i class="fa fa-facebook"></i></a>';
										}
										if(!empty($twitter)){
											echo '<a href="'.$twitter.'" class="twitter"><i class="fa fa-twitter"></i></a>';
										}
										if(!empty($google)){
											echo '<a href="'.$google.'" class="google-plus"><i class="fa fa-google-plus"></i></a>';
										}
										if(!empty($pinterest)){
											echo '<a href="'.$pinterest.'" class="pinterest"><i class="fa fa-pinterest"></i></a>';
										}
										if(!empty($stumbleupon)){
											echo '<a href="'.$stumbleupon.'" class="stumble"><i class="fa fa-stumbleupon"></i></a>';
										}
										if(!empty($linkedin)){
											echo '<a href="'.$linkedin.'" class="linkedin"><i class="fa fa-linkedin"></i></a>';
										}
									echo '</div>';
								}
								?>
								<p><?php the_author_meta('description') ?></p>
							</div>
						</div>	
					</div>
				<?php } ?>
				<?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<article class="latestPost excerpt <?php echo isset($class) ? $class : '' ?><?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
						<?php mts_archive_post($blog_layout); ?>
					</article>							
				<?php endwhile; endif; ?>
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
<?php get_footer(); ?>