<?php
/**
 * Header Layout - Default
 *
 * @package Cyprus
 */

?>

<header id="site-header" class="main-header <?php echo esc_attr( cyprus_get_settings( 'mts_header_style' ) ); ?> clearfix" role="banner" itemscope itemtype="http://schema.org/WPHeader">
	<?php if ( cyprus_get_settings( 'mts_show_primary_nav' ) === 1 ) { ?>
		<div id="primary-nav">
			<div class="container clearfix">
				<div id="primary-navigation" class="primary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<nav class="navigation clearfix">
						<?php
						// Pirmary Navigation.
						if ( has_nav_menu( 'primary-menu' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'primary-menu',
								'menu_class'     => 'menu clearfix',
								'container'      => '',
								'walker'         => new cyprus_menu_walker(),
							));
						}

						// Header Social Icons.
						if ( cyprus_get_settings( 'mts_header_social_icons' ) === 1 && ! empty( cyprus_get_settings( 'mts_header_social' ) ) && is_array( cyprus_get_settings( 'mts_header_social' ) ) ) {
							$header_icons = cyprus_get_settings( 'mts_header_social' );
							cyprus_social_icons( $header_icons, true );
						}
						?>
					</nav>
				</div>
			</div>
		</div>
	<?php } ?>

	<div id="regular-header">
		<div class="container">
			<div class="logo-wrap">
					<?php cyprus_logo(); ?>
			</div>

			<?php get_template_part( 'template-parts/header/header', 'adcode' ); ?>

		</div>
	</div>

	<?php if ( cyprus_get_settings( 'mts_sticky_nav' ) === 1 ) { ?>
	<div class="clear" id="catcher"></div>
	<div id="header" class="sticky-navigation">
	<?php } else { ?>
	<div id="header">
	<?php } ?>
		<div class="container">
			<div id="secondary-navigation" class="secondary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
				<a href="#" id="pull" class="toggle-mobile-menu"><?php _e( 'Menu', 'cyprus' ); ?></a>
				<?php if ( has_nav_menu( 'mobile' ) ) { ?>

					<nav class="navigation clearfix">
						<?php
						// Secondary Navigation.
						if ( has_nav_menu( 'secondary-menu' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'secondary-menu',
								'menu_class'     => 'menu clearfix',
								'container'      => '',
								'walker'         => new cyprus_menu_walker(),
							));
						}
						?>
					</nav>
					<nav class="navigation mobile-only clearfix mobile-menu-wrapper">
						<?php
						// Mobile Menu.
						if ( has_nav_menu( 'mobile' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'mobile',
								'menu_class'     => 'menu clearfix',
								'container'      => '',
								'walker'         => new cyprus_menu_walker(),
							));
						}
						?>
					</nav>

				<?php } else { ?>

					<nav class="navigation clearfix mobile-menu-wrapper">
						<?php
						// Secondary Navigation.
						if ( has_nav_menu( 'secondary-menu' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'secondary-menu',
								'menu_class'     => 'menu clearfix',
								'container'      => '',
								'walker'         => new cyprus_menu_walker(),
							));
						}
						?>
					</nav>

				<?php } ?>
			</div>
		</div><!--.container-->
	</div>

	<?php get_template_part( 'template-parts/header/header', 'nav-adcode' ); ?>

</header>
