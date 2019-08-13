<?php
/**
 * Template Name: Price Index
 */
$mts_options = get_option(MTS_THEME_NAME);
$coin = get_post_meta( get_the_ID(), 'mts_coin_code', true );
$coin_price_against = get_post_meta( get_the_ID(), 'mts_coin_price_against', true );
$coin_historical_data = get_post_meta( get_the_ID(), 'mts_coin_historical_data', true );

get_header(); ?>

<div id="page" class="<?php mts_single_page_class(); ?>">
	<div class="single-content-wrapper <?php if ( mts_custom_sidebar() == 'mts_nosidebar' ) { echo 'no-sidebar'; }?>">
		<div class="<?php mts_article_class(); ?>">
			<div id="content_box" >
				<header>
					<h1 class="title entry-title"><?php the_title(); ?></h1>
				</header>
				<div class="post-content box mark-links entry-content">
					<?php the_content(); ?>
					<div class="coin-summary">
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
							var theUrl = baseUrl+'serve/v1/coin/summary?fsym=<?php echo $coin; ?>&tsyms=<?php echo $coin_price_against; ?>';
							s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
							embedder.parentNode.appendChild(s);
							})();
						</script>
					</div>
					<div class="coin-graph">
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
							var theUrl = baseUrl+'serve/v3/coin/chart?fsym=<?php echo $coin; ?>&tsyms=<?php echo $coin_price_against; ?>';
							s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
							embedder.parentNode.appendChild(s);
							})();
						</script>
					</div>
					<div class="price-history">
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
							var theUrl = baseUrl+'serve/v1/coin/histo_week?fsym=<?php echo $coin; ?>&tsym=<?php echo $coin_historical_data; ?>';
							s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
							embedder.parentNode.appendChild(s);
							})();
						</script>
					</div>
				</div><!--.post-content box mark-links-->

				<div class="price-index-posts">
					<h2 class="postsby"><?php echo get_post_meta( get_the_ID(), 'mts_price_index_article_title', true ); ?></h2>
					<?php if ( get_query_var('paged') && get_query_var('paged') > 1 ){
						$paged = get_query_var('paged');
					} elseif (  get_query_var('page') && get_query_var('page') > 1  ){
						$paged = get_query_var('page');
					} else {
						$paged = 1;
					}
					$args = array(
						'posts_per_page' => get_post_meta( get_the_ID(), 'mts_price_index_no_of_posts', true ),
						'post_type' => 'post',
						'post_status' => 'publish',
						'paged' => $paged,
						'cat' => get_post_meta( get_the_ID(), 'mts_price_index_category', true ),
						'ignore_sticky_posts'=> 1,
					);
					$latest_posts_query = new WP_Query( $args );
					global $wp_query;
					// Put default query object in a temp variable
					$tmp_query = $wp_query;
					// Now wipe it out completely
					$wp_query = null;
					// Re-populate the global with our custom query
					$wp_query = $latest_posts_query;
						if ( $latest_posts_query->have_posts() ) : while ( $latest_posts_query->have_posts() ) : $latest_posts_query->the_post(); ?>
							<article class="latestPost excerpt list">
								<?php mts_archive_post('list'); ?>
							</article>
						<?php endwhile; endif;
					// Restore original query object
					$wp_query = $tmp_query;
					// Be kind; rewind
					wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>