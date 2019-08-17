<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Cyprus
 */

get_template_part( 'template-parts/footer/footer', 'ad' );
?>
	</div><!--#wrapper-->

	<footer<?php cyprus_attr( 'footer' ); ?>>

		<div class="container">
			<?php
			if ( 'bottom' !== cyprus_get_settings( 'footer_sections_position' ) ) {
				get_template_part( 'template-parts/footer/footer', 'sections' );
			}

			if ( cyprus_get_settings( 'mts_top_footer' ) ) {
				cyprus_footer_widget_columns();
			}

			if ( 'bottom' === cyprus_get_settings( 'footer_sections_position' ) ) {
				get_template_part( 'template-parts/footer/footer', 'sections' );
			}
			?>
		</div>

		<?php cyprus_footer_copyrights(); ?>

	</footer><!--#site-footer-->

</div><!--.main-container-->

<?php get_template_part( 'template-parts/footer/footer', 'detect-adblocker' ); ?>

<?php wp_footer(); ?>

</body>
</html>
