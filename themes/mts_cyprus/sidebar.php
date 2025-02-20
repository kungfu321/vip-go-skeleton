<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Cyprus
 */

$sidebar = mts_custom_sidebar();

if ( 'mts_nosidebar' === $sidebar ) {
	return;
}
?>
<aside id="sidebar" class="sidebar c-4-12 <?php echo esc_attr( 'mts-sidebar-' . $sidebar ); ?>" role="complementary" itemscope itemtype="http://schema.org/WPSideBar">

	<?php if ( ! dynamic_sidebar( $sidebar ) ) : ?>

		<div id="sidebar-search" class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Search', 'cyprus' ); ?></h3>
			<?php get_search_form(); ?>
		</div>

		<div id="sidebar-archives" class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Archives', 'cyprus' ); ?></h3>
			<ul>
				<?php wp_get_archives( 'type=monthly' ); ?>
			</ul>
		</div>

		<div id="sidebar-meta" class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Meta', 'cyprus' ); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<?php wp_meta(); ?>
			</ul>
		</div>

	<?php endif; ?>

</aside><!--#sidebar-->
