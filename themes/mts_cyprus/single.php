<?php
/**
 * The template for displaying all single posts.
 *
 * @package Cyprus
 */

get_header(); ?>

	<div id="wrapper" class="<?php cyprus_single_page_class(); ?>">

		<?php cyprus_action( 'single_top' ); ?>

		<div class="container clearfix">

			<?php
			cyprus_single_featured_image_effect();

			cyprus_action( 'before_content' );

			cyprus_single_sections();

			cyprus_action( 'after_content' );

			get_sidebar();
			?>

		</div>

		<?php
		if ( 'full' === cyprus_get_settings( 'related_posts_position' ) ) {
			cyprus_related_posts();
		}
		?>

<?php
get_footer();
