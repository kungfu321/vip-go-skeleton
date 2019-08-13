<?php
/**
 * The template for displaying the header.
 *
 * Displays everything from the doctype declaration down to the navigation.
 */
?>
<!DOCTYPE html>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<!--[if IE ]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<?php mts_meta(); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>
<body id="blog" <?php body_class('main'); ?>>
	<div class="main-container">
		<header id="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
		<?php if ( isset( $mts_options['mts_header_layout'] ) && is_array( $mts_options['mts_header_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_header_layout'] ) ) {
            $header_parts = $mts_options['mts_header_layout']['enabled'];
		} else {
		    $header_parts = array( 'primary-nav' => 'primary-nav', 'logo-section' => 'logo-section', 'main-navigation' => 'main-navigation' );
		}
		foreach( $header_parts as $part => $label ) {
		    switch ($part) {
		        case 'primary-nav': ?>
					<div id="primary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<div class="container">
							<?php if ( !array_key_exists('main-navigation', $mts_options['mts_header_layout']['enabled']) ) {?><a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu', 'crypto' ); ?></a><?php } ?>
							<nav class="navigation clearfix<?php if ( !array_key_exists('main-navigation', $mts_options['mts_header_layout']['enabled']) ) echo ' mobile-menu-wrapper'; ?>">
								<?php if ( has_nav_menu( 'primary' ) ) { ?>
									<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
								<?php } else { ?>
									<ul class="menu clearfix">
										<?php wp_list_pages('title_li='); ?>
									</ul>
								<?php } ?>
							</nav>
						</div>
					</div>
				<?php

		    	break;

		        case 'logo-section': ?>
		        <div id="header">
					<div class="container clearfix">
						<div class="inner-header">
							<div class="logo-wrap">
								<?php if ( $mts_options['mts_logo'] != '' && $mts_logo = wp_get_attachment_image_src( $mts_options['mts_logo'], 'full' ) ) { ?>
									<?php if ( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $mts_logo[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="<?php echo esc_attr( $mts_logo[1] ); ?>" height="<?php echo esc_attr( $mts_logo[2] ); ?>"></a>
										</h1><!-- END #logo -->
									<?php } else { ?>
										<h2 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo esc_url( home_url() ); ?>">
												<img src="<?php echo esc_url( $mts_logo[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="<?php echo esc_attr( $mts_logo[1] ); ?>" height="<?php echo esc_attr( $mts_logo[2] ); ?>"></a>
										</h2><!-- END #logo -->
									<?php } ?>

								<?php } else { ?>

									<?php if ( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
										</h1><!-- END #logo -->
									<?php } else { ?>
										<h2 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
										</h2><!-- END #logo -->
									<?php } ?>
									<div class="site-description" itemprop="description">
										<?php bloginfo( 'description' ); ?>
									</div>

								<?php } ?>
							</div>

							<div class="mts-header-button-social">
								<?php if ( !empty($mts_options['mts_header_social']) && is_array($mts_options['mts_header_social']) && !empty($mts_options['mts_social_icon_head'])) { ?>
									<div class="header-social">
										<?php foreach( $mts_options['mts_header_social'] as $header_icons ) : ?>
											<?php if( ! empty( $header_icons['mts_header_icon'] ) && isset( $header_icons['mts_header_icon'] ) && ! empty( $header_icons['mts_header_icon_link'] )) : ?>
												<a href="<?php print $header_icons['mts_header_icon_link'] ?>" class="header-<?php print $header_icons['mts_header_icon'] ?>" target="_blank"><span class="fa fa-<?php print $header_icons['mts_header_icon'] ?>"></span></a>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								<?php } ?>
								<?php if ( $mts_options['mts_nav_button'] == '1' && !empty( $mts_options['mts_nav_button_url'] ) ) { ?>
									<div class="header-button">
										<a href="<?php echo esc_url( $mts_options['mts_nav_button_url'] ); ?>" style="background: <?php echo $mts_options['mts_nav_button_bg']; ?>; color: <?php echo $mts_options['mts_nav_button_color']; ?>"><?php echo $mts_options['mts_nav_button_text']; ?></a>
									</div>
							   	<?php } ?>
						   	</div>

						</div>

					</div><!--#header-->
				</div>
		    <?php break;
		    case 'main-navigation':
				if( $mts_options['mts_sticky_nav'] == '1' ) { ?>
				<div id="catcher" class="clear" ></div>
				<div class="sticky-navigation main-menu" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
				<?php } else { ?>
					<div class="main-menu">
				<?php } ?>
				<div class="container clearfix">
					<div id="secondary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu', 'crypto' ); ?></a>
					<?php if ( has_nav_menu( 'mobile' ) ) { ?>
						<nav class="navigation clearfix">
							<?php if ( has_nav_menu( 'secondary' ) ) { ?>
								<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
							<?php } else { ?>
								<ul class="menu clearfix">
									<?php wp_list_categories('title_li='); ?>
								</ul>
							<?php } ?>
						</nav>
						<nav class="navigation mobile-only clearfix mobile-menu-wrapper">
							<?php wp_nav_menu( array( 'theme_location' => 'mobile', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
						</nav>
					<?php } else { ?>
						<nav class="navigation clearfix mobile-menu-wrapper">
							<?php if ( has_nav_menu( 'secondary' ) ) { ?>
								<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
							<?php } else { ?>
								<ul class="menu clearfix">
									<?php wp_list_categories('title_li='); ?>
								</ul>
							<?php } ?>
						</nav>
					<?php } ?>
					</div>
					<?php if(!empty($mts_options['mts_header_search'])) { ?>
						<div id="search-6" class="widget header-search">
							<?php get_search_form(); ?>
						</div><!-- END #search-6 -->
		  			<?php } ?>
				</div>
			</div><!--.container-->
			<?php break;
		    case 'coin-prices': ?>

		    	<div class="crypto-price clearfix">
		    		<div class="container">
		    			<?php
		    			$header_coin_symbol_list = array();
		    			$header_coin_url_list = array();

		    			foreach( $mts_options['mts_header_coins'] as $header_icon ) :
							$header_coin_symbol_list[] = $header_icon['mts_header_coin_symbol'];
							$header_coin_url_list[] = '/'.$header_icon['mts_header_coin_url'];
						endforeach;

						$header_coin_symbol_list_a_1 = implode(',', array_slice($header_coin_symbol_list, 0, 4));
						$header_coin_url_list_a_1 = implode(',', array_slice($header_coin_url_list, 0, 4));

						$header_coin_symbol_list_a_2 = implode(',', array_slice($header_coin_symbol_list, 4));
						$header_coin_url_list_a_2 = implode(',', array_slice($header_coin_url_list, 4)); ?>

						<?php if ($mts_options['mts_header_coins_layout'] == 'small') { ?>
							<script type="text/javascript">
								baseUrl = "https://widgets.cryptocompare.com/";
								var scripts = document.getElementsByTagName("script");
								var embedder = scripts[ scripts.length - 1 ];
								var cccTheme = {"General":{}};
								(function (){
								var appName = encodeURIComponent(window.location.hostname);
								if(appName==""){appName="local";}
								var s = document.createElement("script");
								s.type = "text/javascript";
								s.async = true;
								var theUrl = baseUrl+'serve/v3/coin/header?fsyms=<?php echo $header_coin_symbol_list_a_1; ?>,<?php echo $header_coin_symbol_list_a_2; ?>&tsyms=USD';
								s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
								embedder.parentNode.appendChild(s);
								})();
							</script>
							<script type="text/javascript">
								jQuery(document).ready(function($) {
									var coins = <?php echo json_encode($header_coin_url_list); ?>;
									jQuery(window).load(function(){
									    jQuery('.ccc-header-v3-ccc-price-container').each(function(i) {
									        jQuery(this).find('a').attr('href', coins[i]);
									    });
								    });
								});
							</script>
						<?php } else { ?>
							<span class="first-price-block">
								<script type="text/javascript">
									baseUrl = "https://widgets.cryptocompare.com/";
									var scripts = document.getElementsByTagName("script");
									var embedder = scripts[ scripts.length - 1 ];
									(function (){
									var appName = encodeURIComponent(window.location.hostname);
									if(appName==""){appName="local";}
									var s = document.createElement("script");
									s.type = "text/javascript";
									s.async = true;
									var theUrl = baseUrl+'serve/v2/coin/header?fsyms=<?php echo $header_coin_symbol_list_a_1; ?>&tsyms=<?php echo $mts_options['mts_header_coin_currencies']; ?>&local_url=<?php echo $header_coin_url_list_a_1; ?>';
									s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
									embedder.parentNode.appendChild(s);
									})();
								</script>
							</span>
							<span class="second-price-block">
								<script type="text/javascript">
									baseUrl = "https://widgets.cryptocompare.com/";
									var scripts = document.getElementsByTagName("script");
									var embedder = scripts[ scripts.length - 1 ];
									(function (){
									var appName = encodeURIComponent(window.location.hostname);
									if(appName==""){appName="local";}
									var s = document.createElement("script");
									s.type = "text/javascript";
									s.async = true;
									var theUrl = baseUrl+'serve/v2/coin/header?fsyms=<?php echo $header_coin_symbol_list_a_2; ?>&tsyms=<?php echo $mts_options['mts_header_coin_currencies']; ?>&local_url=<?php echo $header_coin_url_list_a_2; ?>';
									s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
									embedder.parentNode.appendChild(s);
									})();

    								jQuery(document).ready(function($) {
    									var coins = <?php echo json_encode($header_coin_url_list); ?>;
    									jQuery(window).load(function(){
    									    jQuery('.ccc-coin-container').each(function(i) {
    									        jQuery(this).find('a').attr('href', '<?php echo get_site_url(); ?>'+coins[i]);
    									    });
    								    });
    								});
								</script>
							</span>
						<?php } ?>
					</div>
				</div>

		    <?php break;
		    }
		} ?>
		</header>

		<?php if ( is_active_sidebar( 'widget-header' ) ) { ?>
			<div class="header-banner container clearfix">
				<?php dynamic_sidebar('widget-header'); ?>
			</div>
		<?php } ?>
