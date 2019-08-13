<?php
/**
 * The template for displaying 404 (Not Found) pages.
 */
get_header(); ?>

<div id="page">
	<div class="single-content-wrapper <?php if ( mts_custom_sidebar() == 'mts_nosidebar' ) { echo 'no-sidebar'; }?>">
		<article class="article">
			<div id="content_box" >
				<header>
					<div class="title">
						<h1><?php _e('Error 404 Not Found', 'crypto' ); ?></h1>
					</div>
				</header>
				<div class="post-content">
					<p><?php _e('Oops! We couldn\'t find this Page.', 'crypto' ); ?></p>
					<p><?php _e('Please check your URL or use the search form below.', 'crypto' ); ?></p>
					<?php get_search_form();?>
				</div><!--.post-content--><!--#error404 .post-->
			</div><!--#content-->
		</article>
		<?php get_sidebar(); ?>
	</div><!--.single-post-wrap-->	
<?php get_footer(); ?>