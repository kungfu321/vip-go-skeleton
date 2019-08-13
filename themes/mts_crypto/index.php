<?php
/**
 * The main template file.
 *
 * Used to display the homepage when home.php doesn't exist.
 */
$mts_options = get_option(MTS_THEME_NAME);
if ( is_array( $mts_options['mts_homepage_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_homepage_layout'] ) ) {
    $homepage_layout = $mts_options['mts_homepage_layout']['enabled'];
} else {
    $homepage_layout = array();
}
get_header();
?>

<div id="page" class="clearfix">
	<div id="content_box" class="home-sections">
		<?php foreach( $homepage_layout as $key => $section ) { get_template_part( 'template-parts/section', $key ); } ?>
	</div>
<?php get_footer(); ?>