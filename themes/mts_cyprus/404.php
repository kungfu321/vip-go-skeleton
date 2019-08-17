<?php
/**
 * The template for displaying 404 (Not Found) pages.
 *
 * @package Cyprus
 */

get_header(); ?>

	<div id="wrapper">

		<div class="container">

			<article class="article">

				<div id="content_box" >

					<header>
						<div class="title">
							<h1><?php esc_html_e( 'Error 404 Not Found', 'cyprus' ); ?></h1>
						</div>
					</header>

					<div class="post-content">
						<p><?php esc_html_e( 'Oops! We couldn\'t find this Page.', 'cyprus' ); ?></p>
						<p><?php esc_html_e( 'Please check your URL or use the search form below.', 'cyprus' ); ?></p>
						<?php get_search_form(); ?>
					</div><!--.post-content--><!--#error404 .post-->

				</div><!--#content-->

			</article>

			<?php get_sidebar(); ?>

		</div>
<?php
get_footer();
