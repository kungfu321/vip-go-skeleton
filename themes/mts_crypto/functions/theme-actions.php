<?php
$mts_options = get_option(MTS_THEME_NAME);
if ( ! function_exists( 'mts_meta' ) ) {
	/**
	 * Display necessary tags in the <head> section.
	 */
	function mts_meta(){
		global $mts_options, $post;
		?>

		<?php if ( ! empty( $mts_options['mts_favicon'] ) && $mts_favicon = wp_get_attachment_url( $mts_options['mts_favicon'] ) ) { ?>
			<link rel="icon" href="<?php echo esc_url( $mts_favicon ); ?>" type="image/x-icon" />
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) { ?>
			<?php printf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( get_site_icon_url( 32 ) ) ); ?>
			<?php sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( get_site_icon_url( 192 ) ) ); ?>
		<?php } ?>

		<?php if ( !empty( $mts_options['mts_metro_icon'] ) && $mts_metro_icon = wp_get_attachment_url( $mts_options['mts_metro_icon'] ) ) { ?>
			<!-- IE10 Tile.-->
			<meta name="msapplication-TileColor" content="#FFFFFF">
			<meta name="msapplication-TileImage" content="<?php echo esc_url( $mts_metro_icon ); ?>">
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon( ) ) { ?>
			<?php printf( '<meta name="msapplication-TileImage" content="%s">', esc_url( get_site_icon_url( 270 ) ) ); ?>
		<?php } ?>

		<?php if ( ! empty( $mts_options['mts_touch_icon'] ) && $mts_touch_icon = wp_get_attachment_url( $mts_options['mts_touch_icon'] ) ) { ?>
			<!--iOS/android/handheld specific -->
			<link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( $mts_touch_icon ); ?>" />
		<?php } elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) { ?>
			<?php printf( '<link rel="apple-touch-icon-precomposed" href="%s">', esc_url( get_site_icon_url( 180 ) ) ); ?>
		<?php } ?>

		<?php if ( ! empty( $mts_options['mts_responsive'] ) ) { ?>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php } ?>

		<?php if($mts_options['mts_prefetching'] == '1') { ?>
			<?php if (is_front_page()) { ?>
				<?php $my_query = new WP_Query('posts_per_page=1'); while ($my_query->have_posts()) : $my_query->the_post(); ?>
				<link rel="prefetch" href="<?php the_permalink(); ?>">
				<link rel="prerender" href="<?php the_permalink(); ?>">
				<?php endwhile; wp_reset_postdata(); ?>
			<?php } elseif (is_singular()) { ?>
				<link rel="prefetch" href="<?php echo esc_url( home_url() ); ?>">
				<link rel="prerender" href="<?php echo esc_url( home_url() ); ?>">
			<?php } ?>
		<?php } ?>
<?php
	}
}

if ( ! function_exists( 'mts_head' ) ){
	/**
	 * Display header code from Theme Options.
	 */
	function mts_head() {
	global $mts_options;
?>
<?php echo $mts_options['mts_header_code']; ?>
<?php }
}
add_action('wp_head', 'mts_head');

if ( ! function_exists( 'mts_copyrights_credit' ) ) {
	/**
	 * Display the footer copyright.
	 */
	function mts_copyrights_credit() {
	global $mts_options;
?>
<!--start copyrights-->
<div class="row" id="copyright-note">
<?php $copyright_text = '<a href=" ' . esc_url( trailingslashit( home_url() ) ). '" title=" ' . get_bloginfo('description') . '">' . get_bloginfo('name') . '</a> Copyright &copy; ' . date("Y") . '.'; ?>
<div><?php echo apply_filters( 'mts_copyright_content', $copyright_text ); ?></div>
<div class="to-top"><?php echo $mts_options['mts_copyrights']; ?>&nbsp;</div>
</div>
<!--end copyrights-->
<?php }
}

if ( ! function_exists( 'mts_footer' ) ) {
	/**
	 * Display the analytics code in the footer.
	 */
	function mts_footer() {
	global $mts_options;
?>
	<?php if ($mts_options['mts_analytics_code'] != '') { ?>
	<!--start footer code-->
		<?php echo $mts_options['mts_analytics_code']; ?>
	<!--end footer code-->
	<?php }
	}
}

if ( ! function_exists('mts_the_breadcrumb')) {
	/**
	 * Display the breadcrumbs.
	 */
	function mts_the_breadcrumb() {
		if ( is_front_page() ) {
			return;
		}
		echo '<div typeof="v:Breadcrumb" class="root"><a rel="v:url" property="v:title" href="';
		echo esc_url( home_url() );
		echo '">'.esc_html__( "Home", 'crypto' );
		echo '</a></div><div>\</div>';
		if (is_single()) {
			$categories = get_the_category();
			if ( $categories ) {
				$level = 0;
				$hierarchy_arr = array();
				foreach ( $categories as $cat ) {
					$anc = get_ancestors( $cat->term_id, 'category' );
					$count_anc = count( $anc );
					if (  0 < $count_anc && $level < $count_anc ) {
						$level = $count_anc;
						$hierarchy_arr = array_reverse( $anc );
						array_push( $hierarchy_arr, $cat->term_id );
					}
				}
				if ( empty( $hierarchy_arr ) ) {
					$category = $categories[0];
					echo '<div typeof="v:Breadcrumb"><a href="'. esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div>\</div>';
				} else {
					foreach ( $hierarchy_arr as $cat_id ) {
						$category = get_term_by( 'id', $cat_id, 'category' );
						echo '<div typeof="v:Breadcrumb"><a href="'. esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div>\</div>';
					}
				}
			}
			echo "<div><span>";
			the_title();
			echo "</span></div>";
		} elseif (is_page()) {
			$parent_id  = wp_get_post_parent_id( get_the_ID() );
			if ( $parent_id ) {
				$breadcrumbs = array();
				while ( $parent_id ) {
					$page = get_page( $parent_id );
					$breadcrumbs[] = '<div typeof="v:Breadcrumb"><a href="'.esc_url( get_permalink( $page->ID ) ).'" rel="v:url" property="v:title">'.esc_html( get_the_title($page->ID) ). '</a></div><div>\</div>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) { echo $crumb; }
			}
			echo "<div><span>";
			the_title();
			echo "</span></div>";
		} elseif (is_category()) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$this_cat_id = $cat_obj->term_id;
			$hierarchy_arr = get_ancestors( $this_cat_id, 'category' );
			if ( $hierarchy_arr ) {
				$hierarchy_arr = array_reverse( $hierarchy_arr );
				foreach ( $hierarchy_arr as $cat_id ) {
					$category = get_term_by( 'id', $cat_id, 'category' );
					echo '<div typeof="v:Breadcrumb"><a href="'.esc_url( get_category_link( $category->term_id ) ).'" rel="v:url" property="v:title">'.esc_html( $category->name ).'</a></div><div>\</div>';
				}
			}
			echo "<div><span>";
			single_cat_title();
			echo "</span></div>";
		} elseif (is_author()) {
			echo "<div><span>";
			if(get_query_var('author_name')) :
				$curauth = get_user_by('slug', get_query_var('author_name'));
			else :
				$curauth = get_userdata(get_query_var('author'));
			endif;
			echo esc_html( $curauth->nickname );
			echo "</span></div>";
		} elseif (is_search()) {
			echo "<div><span>";
			the_search_query();
			echo "</span></div>";
		} elseif (is_tag()) {
			echo "<div><span>";
			single_tag_title();
			echo "</span></div>";
		}
	}
}
if ( ! function_exists( 'mts_the_category' ) ) {
/**
 * Display schema-compliant the_category()
 *
 * @param string $separator
 */
	function mts_the_category( $separator = ', ' ) {
		$categories = get_the_category();
		$count = count($categories);
		foreach ( $categories as $i => $category ) {
			echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . sprintf( __( "View all posts in %s", 'crypto' ), esc_attr( $category->name ) ) . '">' . esc_html( $category->name ).'</a>';
			if ( $i < $count - 1 )
				echo $separator;
		}
	}
}	
if ( ! function_exists( 'mts_the_tags' ) ) {
/**
 * Display schema-compliant the_tags()
 *
 * @param string $before
 * @param string $sep
 * @param string $after
 */
	function mts_the_tags($before = '', $sep = ' ', $after = '</div>') {
		if ( empty( $before ) ) {
			$before = '<div class="tags border-bottom">'.__('Tags: ', 'crypto' );
		}

		$tags = get_the_tags();
		if (empty( $tags ) || is_wp_error( $tags ) ) {
			return;
		}
		$tag_links = array();
		foreach ($tags as $tag) {
			$link = get_tag_link($tag->term_id);
			$tag_links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $tag->name . '</a>';
		}
		echo $before.join($sep, $tag_links).$after;
	}
}

if (!function_exists('mts_pagination')) {
	/**
	 * Display the pagination.
	 *
	 * @param string $pages
	 * @param int $range
	 */
	function mts_pagination( $pages = '', $range = 3, $pagenavigation_type = 'mts_pagenavigation_type' ) {
		$mts_options = get_option(MTS_THEME_NAME);
		if (isset($mts_options[$pagenavigation_type]) && $mts_options[$pagenavigation_type] == '1' ) { // numeric pagination
			$args = array(
				'mid_size' => 3,
				'prev_text' => '<i class="fa fa-angle-left"></i> ',
				'next_text' => '<i class="fa fa-angle-right"></i>',
			);
			if(isset($_GET['ico']) && !empty($_GET['ico'])) {
				$args['add_args'] = array('ico' => $_GET['ico']);
			}
			the_posts_pagination($args);
		} else { // traditional or ajax pagination
			?>
			<div class="pagination pagination-previous-next">
			<ul>
				<li class="nav-previous"><?php next_posts_link( '<i class="fa fa-angle-left"></i> '. __( 'Previous', 'crypto' ) ); ?></li>
				<li class="nav-next"><?php previous_posts_link( __( 'Next', 'crypto' ).' <i class="fa fa-angle-right"></i>' ); ?></li>
			</ul>
			</div>
			<?php
		}
	}
}

if (!function_exists('mts_related_posts')) {
	/**
	 * Display the related posts.
	 */
	function mts_related_posts() {
		$post_id = get_the_ID();
		$mts_options = get_option(MTS_THEME_NAME);
		//if(!empty($mts_options['mts_related_posts'])) { ?>
			<!-- Start Related Posts -->
			<?php
			$empty_taxonomy = false;
			if (empty($mts_options['mts_related_posts_taxonomy']) || $mts_options['mts_related_posts_taxonomy'] == 'tags') {
				// related posts based on tags
				$tags = get_the_tags($post_id);
				if (empty($tags)) {
					$empty_taxonomy = true;
				} else {
					$tag_ids = array();
					foreach($tags as $individual_tag) {
						$tag_ids[] = $individual_tag->term_id;
					}
					$args = array( 'tag__in' => $tag_ids,
						'post__not_in' => array($post_id),
						'posts_per_page' => isset( $mts_options['mts_related_postsnum'] ) ? $mts_options['mts_related_postsnum'] : 3,
						'ignore_sticky_posts' => 1,
						'orderby' => 'rand'
					);
				}
			 } else {
				// related posts based on categories
				$categories = get_the_category($post_id);
				if (empty($categories)) {
					$empty_taxonomy = true;
				} else {
					$category_ids = array();
					foreach($categories as $individual_category)
						$category_ids[] = $individual_category->term_id;
					$args = array( 'category__in' => $category_ids,
						'post__not_in' => array($post_id),
						'posts_per_page' => $mts_options['mts_related_postsnum'],
						'ignore_sticky_posts' => 1,
						'orderby' => 'rand'
					);
				}
			 }
			if (!$empty_taxonomy) {
			$my_query = new WP_Query( apply_filters( 'mts_related_posts_query_args', $args, $mts_options['mts_related_posts_taxonomy'] ) ); if( $my_query->have_posts() ) {
				echo '<div class="related-posts">';
				echo '<h4>'.__('Related Posts', 'crypto' ).'</h4>';
				echo '<div class="related-posts-wrapper">';
				$posts_per_row = 2;
				$j = 0;
				while( $my_query->have_posts() ) { $my_query->the_post(); ?>
				<article class="latestPost excerpt  <?php echo (++$j % $posts_per_row == 0) ? 'last' : ''; ?>">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" id="featured-thumbnail">
						<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('crypto-related',array('title' => '')); echo '</div>'; ?>
						<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					</a>
					<header>
						<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
					</header>
				</article><!--.post.excerpt-->

			<?php } echo '</div></div>'; }} wp_reset_postdata(); ?>
			<!-- .related-posts -->
		<?php //}
	}
}

/*------------[ Post Meta Info ]-------------*/
if ( ! function_exists('mts_the_postinfo' ) ) {
	/**
	 * Display the post info block.
	 *
	 * @param string $section
	 */
	function mts_the_postinfo( $section = 'home' ) {
		$mts_options = get_option( MTS_THEME_NAME );
		$opt_key = 'mts_'.$section.'_headline_meta_info';

		if ( isset( $mts_options[ $opt_key ] ) && is_array( $mts_options[ $opt_key ] ) && array_key_exists( 'enabled', $mts_options[ $opt_key ] ) ) {
			$headline_meta_info = $mts_options[ $opt_key ]['enabled'];
		} else {
			$headline_meta_info = array();
		}
		if ( ! empty( $headline_meta_info ) ) { ?>
			<div class="post-info">
				<?php foreach( $headline_meta_info as $key => $meta ) { mts_the_postinfo_item( $key ); } ?>
			</div>
		<?php }
	}
}
if ( ! function_exists('mts_the_postinfo_item' ) ) {
	/**
	 * Display information of an item.
	 * @param $item
	 */
	function mts_the_postinfo_item( $item ) {
		$mts_options = get_option(MTS_THEME_NAME);
		switch ( $item ) {
			case 'author':
			?>
				<span class="theauthor"><span><?php _e('By ', 'crypto'); ?><?php the_author_posts_link(); ?></span></span>
			<?php
			break;
			case 'comment':
			?>
				<span class="thecomment"><a href="<?php echo esc_url( get_comments_link() ); ?>" itemprop="interactionCount"><?php comments_number(__('<span class="comment-text">No comments</span>','crypto'), __('1 <span class="comment-text">Comment</span>','crypto'),  __(' % <span class="comment-text">Comments</span>','crypto') );?></a></span>
			<?php
			break;
			case 'date':
				if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
					<span class="thetime date updated"><span><?php the_time( 'M d, Y' ); ?></span></span>
				<?php } else { ?>
					<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
					<?php
				}
			break;
			case 'category':
			?>
				<span class="thecategory"><i class="fa fa-tags"></i> <?php mts_the_category(', ') ?></span>
			<?php
			break;
			case 'views':
				$count = get_post_meta( get_the_id(), '_mts_view_count', true );
				if ( $count > 0 ) { ?>
					<span class="views" title="<?php _e('Views','crypto'); ?>">                              
						<?php echo __(  $count . ' ' . '<span class="view-text">'.__('Total views', 'crypto' ).'</span>' ) ?>
					</span>
				<?php }	?>
			<?php
			break;
		}
	}
}

if (!function_exists('mts_social_buttons')) {
	/**
	 * Display the social sharing buttons.
	 */
	function mts_social_buttons() {
		$mts_options = get_option( MTS_THEME_NAME );
		$buttons = array();

		if ( isset( $mts_options['mts_social_buttons'] ) && is_array( $mts_options['mts_social_buttons'] ) && array_key_exists( 'enabled', $mts_options['mts_social_buttons'] ) ) {
			$buttons = $mts_options['mts_social_buttons']['enabled'];
		}

		if ( ! empty( $buttons ) && isset( $mts_options['mts_social_button_layout'] ) ) {
			if( $mts_options['mts_social_button_layout'] == 'modern' ) { ?>
				<div class="shareit shareit-modern <?php echo $mts_options['mts_social_button_position']; ?>">
					<?php foreach( $buttons as $key => $button ) { mts_social_button( $key ); } ?>
				</div>
			<?php }	else if( $mts_options['mts_social_button_layout'] == 'flat-square' ) { ?>
				<div class="shareit shareit-flat <?php echo $mts_options['mts_social_button_position']; ?>">
					<?php foreach( $buttons as $key => $button ) { mts_social_button( $key ); } ?>
				</div>
			<?php } else { ?>
				<div class="shareit shareit-default <?php echo $mts_options['mts_social_button_position']; ?>">
					<?php foreach( $buttons as $key => $button ) { mts_social_default_button( $key ); } ?>
				</div>
			<?php }
		}
	}
}

if ( ! function_exists('mts_social_button' ) ) {
	/**
	 * Display network-independent sharing buttons.
	 *
	 * @param $button
	 */
	function mts_social_button( $button ) {
		$mts_options = get_option( MTS_THEME_NAME );
		global $post;
		if( is_single() ){
			$imgUrl = $img = '';
			if ( has_post_thumbnail( $post->ID ) ){
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'crypto-sliderfull' );
				$imgUrl = $img[0];
			}
		}
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook Share-->
				<div class="share-item facebooksharebtn">
					<a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode(get_permalink()); ?>&p[images][0]=<?php echo $imgUrl; ?>&p[title]=<?php echo get_the_title(); ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Share', 'crypto'); } ?></a>

				</div>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<div class="share-item twitterbtn">
					<?php 
					$via = '';
					if( $mts_options['mts_twitter_username'] ) {
						$via = '&via='. $mts_options['mts_twitter_username'];
					}
					?> 
					<a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(get_permalink()); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo urlencode(get_permalink()); ?><?php echo $via; ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-twitter"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Tweet', 'crypto'); }?></a>
				</div>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<div class="share-item gplusbtn">
						<!-- <g:plusone size="medium"></g:plusone> -->
					<a href="//plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-google-plus"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Share', 'crypto'); }?></a>
				</div>
			<?php
			break;
			case 'pinterest':
				global $post;
			?>
				<!-- Pinterest -->
				<div class="share-item pinbtn">
					<a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?> + '&media=<?php echo $imgUrl; ?>&description=<?php the_title(); ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-pinterest-p"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Pin it', 'crypto'); }?></a>

				</div>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<div class="share-item linkedinbtn">				 
					<a href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo 'url'; ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-linkedin"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Share', 'crypto'); }?></a>
				</div>
			<?php
			break;
			case 'stumble':
				?>
				<!-- Stumble -->
				<div class="share-item stumblebtn">				
					<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="single-social" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-stumbleupon"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Stumble', 'crypto'); }?></a>
				</div>
			<?php
			break;
			case 'telegram':
				?>
				<!-- Telegram -->
				<div class="share-item telegram">				
					<a class="single-social" href="javascript:window.open('https://telegram.me/share/url?url='+encodeURIComponent(window.location.href), '_blank')"><i class="fa fa-paper-plane"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Share', 'crypto'); } ?></a>
				</div>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<span class="share-item reddit">
					<a href="//www.reddit.com/submit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-reddit-alien"></i><?php if( $mts_options['mts_social_button_layout'] == 'modern' ) { _e('Reddit', 'crypto'); }?></a>
				</span>
			<?php
			break;
		}
	}
}

if ( ! function_exists('mts_social_default_button' ) ) {
	/**
	 * Display network-independent sharing buttons.
	 *
	 * @param $button
	 */
	function mts_social_default_button( $button ) {
		$mts_options = get_option( MTS_THEME_NAME );
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook Share-->
				<span class="share-item facebooksharebtn">
					<div class="fb-share-button" data-layout="button_count"></div>
				</span>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<span class="share-item twitterbtn">
					<a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo esc_attr( $mts_options['mts_twitter_username'] ); ?>"><?php esc_html_e( 'Tweet', 'crypto' ); ?></a>
				</span>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<span class="share-item gplusbtn">
					<g:plusone size="medium"></g:plusone>
				</span>
			<?php
			break;
			case 'pinterest':
			?>
				<!-- Pinterest -->
				<span class="share-item pinbtn">
					<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' ); echo $thumb['0']; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal"><?php esc_html_e( 'Pin It', 'crypto' ); ?></a>
				</span>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<span class="share-item linkedinbtn">
					<script type="IN/Share" data-url="<?php echo esc_url( get_the_permalink() ); ?>"></script>
				</span>
			<?php
			break;
			case 'stumble':
			?>
				<!-- Stumble -->
				<span class="share-item stumblebtn">
					<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumble" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="stumble-icon"><i class="fa fa-stumbleupon"></i></span><span class="stumble-text"><?php _e('Share', 'MTSTHEMENAME'); ?></span></a>
				</span>
			<?php
			break;
			case 'telegram':
				?>
				<!-- Telegram -->
				<span class="share-item telegram">				
					<a class="single-social" href="javascript:window.open('https://telegram.me/share/url?url='+encodeURIComponent(window.location.href), '_blank')"><i class="fa fa-paper-plane"></i><?php _e('Share', 'crypto'); ?></a>
				</span>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<span class="share-item reddit">
					<a href="//www.reddit.com/submit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"> <img src="<?php echo get_template_directory_uri().'/images/reddit.png' ?>" alt=<?php _e( 'submit to reddit', 'crypto' ); ?> border="0" /></a>
				</span>
			<?php
			break;
		}
	}
}

if ( ! function_exists( 'mts_article_class' ) ) {
	/**
	 * Custom `<article>` class name.
	 */
	function mts_article_class() {
		$mts_options = get_option( MTS_THEME_NAME );
		$class = 'article';

		// sidebar or full width
		if ( mts_custom_sidebar() == 'mts_nosidebar' ) {
			$class = 'ss-full-width';
		}

		echo $class;
	}
}

if ( ! function_exists( 'mts_single_page_class' ) ) {
	/**
	 * Custom `#page` class name.
	 */
	function mts_single_page_class() {
		$class = '';

		if ( is_single() || is_page() ) {

			$class = 'single';

			$header_animation = mts_get_post_header_effect();
			if ( !empty( $header_animation )) $class .= ' '.$header_animation;
		}

		echo $class;
	}
}

if ( ! function_exists( 'mts_archive_post' ) ) {
	/**
	 * Display a post of specific layout.
	 *
	 * @param string $layout
	 */
	function mts_archive_post( $layout = 'grid' ) {

		$mts_options = get_option(MTS_THEME_NAME);

		if ( 'grid' == $layout ) { ?>
			<div class="latestPost-inner">	
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="post-image post-image-left">
					<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('crypto-featured', array('title' => '')); echo '</div>'; ?>
					<?php if(function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					<?php if( isset( $mts_options['mts_home_more_meta_info']['views'] ) && $mts_options['mts_home_more_meta_info']['views'] == '1' ) : 
						$count = get_post_meta( get_the_id(), '_mts_view_count', true );
						if ( $count > 0 ) { ?>
							<span class="views" title="<?php _e('Views','crypto'); ?>">              
								<?php echo __('<i class="fa fa-eye"></i>', MTS_THEME_NAME) . ' ' . $count;  ?>
							</span>
						<?php } ?>	
					<?php endif; ?>
					<?php $category = get_the_category();  
					if( !empty($category) && isset( $mts_options['mts_home_more_meta_info']['category'] ) && $mts_options['mts_home_more_meta_info']['category'] == '1' ){ ?>
						<div class="thecategory"<?php if ( mts_get_category_color() || mts_get_category_bg() ) { echo ' style="color: '.mts_get_category_color().'; background: '.mts_get_category_bg().';"'; } ?>><?php echo $category[0]->cat_name; ?></div>
					<?php } ?>
				</a>
				<header>
					<?php mts_the_postinfo(); ?>
					<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
				</header>
			</div>	
		<?php } elseif( 'list' == $layout ) { ?>
			<div class="latestPost-inner">		
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="post-image post-image-left">
					<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('crypto-featuredlist', array('title' => '')); echo '</div>'; ?>
					<?php if(function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
					<?php $category = get_the_category();  
					if( !empty($category) && isset( $mts_options['mts_home_more_meta_info']['category'] ) && $mts_options['mts_home_more_meta_info']['category'] == '1' ){ ?>
						<div class="thecategory"<?php if ( mts_get_category_color() || mts_get_category_bg() ) { echo ' style="color: '.mts_get_category_color().'; background: '.mts_get_category_bg().';"'; } ?>><?php echo $category[0]->cat_name; ?></div>
					<?php } ?>
				</a>
				<header>
					<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
					<?php mts_the_postinfo(); ?>
					<?php if( isset( $mts_options['mts_home_more_meta_info']['views'] ) && $mts_options['mts_home_more_meta_info']['views'] == '1' ) :
					$count = get_post_meta( get_the_id(), '_mts_view_count', true );
					if ( $count > 0 ) { ?>
						<div class="views" title="<?php _e('Views','crypto'); ?>">                              
							<?php echo __('<i class="fa fa-eye"></i>', MTS_THEME_NAME) . ' ' . $count .' '. __('Views','crypto')  ?>
						</div>
					<?php } ?>	
				<?php endif; ?>
				</header>
				<div class="front-view-content">
					<?php echo mts_excerpt(20); ?>
				</div>
			</div>	
		<?php } ?>
		
	<?php }
}

/*Author Social-Icons*/
add_action( 'show_user_profile', 'add_extra_social_links' );
add_action( 'edit_user_profile', 'add_extra_social_links' );
// Author Social Buttons
function add_extra_social_links( $user )
{
	?>
		<h3><?php _e('Social Links','crypto'); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="facebook"><?php _e('Facebook Profile','crypto'); ?></label></th>
				<td><input type="text" name="facebook" value="<?php echo esc_attr(get_the_author_meta( 'facebook', $user->ID )); ?>" class="regular-text" /></td>
			</tr>

			<tr>
				<th><label for="twitter"><?php _e('Twitter Profile','crypto'); ?></label></th>
				<td><input type="text" name="twitter" value="<?php echo esc_attr(get_the_author_meta( 'twitter', $user->ID )); ?>" class="regular-text" /></td>
			</tr>

			<tr>
				<th><label for="google"><?php _e('Google+ Profile','crypto'); ?></label></th>
				<td><input type="text" name="google" value="<?php echo esc_attr(get_the_author_meta( 'google', $user->ID )); ?>" class="regular-text" /></td>
			</tr>

			<tr>
				<th><label for="pinterest"><?php _e('Pinterest','crypto'); ?></label></th>
				<td><input type="text" name="pinterest" value="<?php echo esc_attr(get_the_author_meta( 'pinterest', $user->ID )); ?>" class="regular-text" /></td>
			</tr>

			<tr>
				<th><label for="stumbleupon"><?php _e('StumbleUpon','crypto'); ?></label></th>
				<td><input type="text" name="stumbleupon" value="<?php echo esc_attr(get_the_author_meta( 'stumbleupon', $user->ID )); ?>" class="regular-text" /></td>
			</tr>

			<tr>
				<th><label for="linkedin"><?php _e('Linkedin','crypto'); ?></label></th>
				<td><input type="text" name="linkedin" value="<?php echo esc_attr(get_the_author_meta( 'linkedin', $user->ID )); ?>" class="regular-text" /></td>
			</tr>
		</table>
	<?php
}

add_action( 'personal_options_update', 'save_extra_social_links' );
add_action( 'edit_user_profile_update', 'save_extra_social_links' );

function save_extra_social_links( $user_id )
{
	update_user_meta( $user_id,'facebook', sanitize_text_field( $_POST['facebook'] ) );
	update_user_meta( $user_id,'twitter', sanitize_text_field( $_POST['twitter'] ) );
	update_user_meta( $user_id,'google', sanitize_text_field( $_POST['google'] ) );
	update_user_meta( $user_id,'pinterest', sanitize_text_field( $_POST['pinterest'] ) );
	update_user_meta( $user_id,'stumbleupon', sanitize_text_field( $_POST['stumbleupon'] ) );
	update_user_meta( $user_id,'linkedin', sanitize_text_field( $_POST['linkedin'] ) );
	update_user_meta( $user_id,'author_box_link', sanitize_text_field( $_POST['author_box_link'] ) );
}


function mts_theme_action( $action = null ) {
    update_option( 'mts__thl', '1' );
    update_option( 'mts__pl', '1' );
}

function mts_theme_activation( $oldtheme_name = null, $oldtheme = null ) {
    // Check for Connect plugin version > 1.4
    if ( class_exists('mts_connection') && defined('MTS_CONNECT_ACTIVE') && MTS_CONNECT_ACTIVE ) {
        return;
    }
     $plugin_path = 'mythemeshop-connect/mythemeshop-connect.php';
    
    // Check if plugin exists
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    if ( ! array_key_exists( $plugin_path, $plugins ) ) {
        // auto-install it
        include_once( ABSPATH . 'wp-admin/includes/misc.php' );
        include_once( ABSPATH . 'wp-admin/includes/file.php' );
        include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';
        $result = $upgrader->install( $plugin_file );
        // If install fails then revert to previous theme
        if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
            switch_theme( $oldtheme->stylesheet );
            return false;
        }
    } else {
        // Plugin is already installed, check version
        $ver = isset( $plugins[$plugin_path]['Version'] ) ? $plugins[$plugin_path]['Version'] : '1.0';
         if ( version_compare( $ver, '2.0.5' ) === -1 ) { 
            include_once( ABSPATH . 'wp-admin/includes/misc.php' );
            include_once( ABSPATH . 'wp-admin/includes/file.php' );
            include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            $skin     = new Automatic_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );
            
            add_filter( 'pre_site_transient_update_plugins',  'mts_inject_connect_repo', 10, 2 );
            $result = $upgrader->upgrade( $plugin_path );
            remove_filter( 'pre_site_transient_update_plugins', 'mts_inject_connect_repo' );
            
            // If update fails then revert to previous theme
            if ( is_null( $result ) || is_wp_error( $result ) || is_wp_error( $skin->result ) ) {
                switch_theme( $oldtheme->stylesheet );
                return false;
            }
        }
    }
    $activate = activate_plugin( $plugin_path );
}

function mts_inject_connect_repo( $pre, $transient ) {
    $plugin_file = 'https://www.mythemeshop.com/mythemeshop-connect.zip';
    
    $return = new stdClass();
    $return->response = array();
    $return->response['mythemeshop-connect/mythemeshop-connect.php'] = new stdClass();
    $return->response['mythemeshop-connect/mythemeshop-connect.php']->package = $plugin_file;
    
    return $return;
}

add_action( 'wp_loaded', 'mts_maybe_set_constants' );
function mts_maybe_set_constants() {
    if ( ! defined( 'MTS_THEME_S' ) ) {
        mts_set_theme_constants();
    }
}

add_action( 'init', 'mts_nhp_sections_override', -11 );
function mts_nhp_sections_override() {
    define( 'MTS_THEME_INIT', 1 );
    if ( class_exists('mts_connection') && defined('MTS_CONNECT_ACTIVE') && MTS_CONNECT_ACTIVE ) {
        return;
    }
    if ( ! get_option( MTS_THEME_NAME, false ) ) {
    	return;
    }
    add_filter( 'nhp-opts-sections', '__return_empty_array' );
    add_filter( 'nhp-opts-sections', 'mts_nhp_section_placeholder' );
    add_filter( 'nhp-opts-args', 'mts_nhp_opts_override' );
    add_filter( 'nhp-opts-extra-tabs', '__return_empty_array', 11, 1 );
}

function mts_nhp_section_placeholder( $sections ) {
    $sections[] = array(
        'icon' => 'fa fa-cogs',
        'title' => __('Not Connected', 'crypto' ),
        'desc' => '<p class="description">' . __('You will find all the theme options here after connecting with your MyThemeShop account.', 'crypto' ) . '</p>',
        'fields' => array()
    );
    return $sections;
}

function mts_nhp_opts_override( $opts ) {
    $opts['show_import_export'] = false;
    $opts['show_typography'] = false;
    $opts['show_translate'] = false;
    $opts['show_child_theme_opts'] = false;
    $opts['last_tab'] = 0;
    
    return $opts;
}
