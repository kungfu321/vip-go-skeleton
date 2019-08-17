<?php

/**
 * Site Logo
 *
 * @param array $args
 * @return void
 */
if ( ! function_exists( 'cyprus_logo' ) ) {
	function cyprus_logo( $args = array() ) {

		$cyprus_logo = wp_get_attachment_url( cyprus_get_settings( 'mts_logo' ) );
		$h_tag       = 'h2';
		$logo_type   = 'text-logo';

		if ( is_front_page() || is_home() || is_404() ) {
			$h_tag = 'h1';
		}

		if ( ! empty( $cyprus_logo ) ) {
			$logo_type = 'image-logo';
		}

		$defaults = array(
			'logo'   => $cyprus_logo,
			'url'    => esc_url( home_url() ),
			'title'  => get_bloginfo( 'name' ),
			'before' => '<' . $h_tag . ' id="logo" class="' . $logo_type . ' clearfix" itemprop="headline">',
			'after'  => '</' . $h_tag . '>',
		);

		$args = wp_parse_args( $args, $defaults );

		$output = $args['logo'] ? sprintf( '<img src="' . $args['logo'] . '" alt="' . esc_attr( $args['title'] ) . '">' ) : $args['title'];
		$output = sprintf( '<a href="%1$s">%2$s</a>', $args['url'], $output );

		echo wp_kses_post( $args['before'] ) . $output . wp_kses_post( $args['after'] );
	}
}
/**
 * Template functions that the framework or themes may use.
 */

/**
 * Single Post Header Layout type (Parallax or Zoomout) *
 */
if ( ! function_exists( 'cyprus_single_featured_image_effect' ) ) {
	function cyprus_single_featured_image_effect() {

		$header_animation = cyprus_get_post_header_effect();

		if ( has_post_thumbnail() ) {
			if ( 'parallax' === $header_animation ) {
				echo '<div id="parallax" style="background-image: url(' . get_the_post_thumbnail_url( get_the_ID(), 'full' ) . ');"></div>';
			} elseif ( 'zoomout' === $header_animation ) {
				echo '<div id="zoom-out-effect"><div id="zoom-out-bg" style="background-image: url(' . get_the_post_thumbnail_url( get_the_ID(), 'full' ) . ');"></div></div>';
			}
		}

	}
}
/**
 * Display cyprus-compliant the_tags()
 *
 * @param string $before
 * @param string $sep
 * @param string $after
 */
if ( ! function_exists( 'cyprus_post_tags' ) ) {
	function cyprus_post_tags( $before = '', $sep = ', ', $after = '</div>' ) {
		if ( empty( $before ) ) {
			$before = '<div class="tags border-bottom">' . esc_html__( 'Tags: ', 'cyprus' );
		}
		$tags = get_the_tags();
		if ( empty( $tags ) || is_wp_error( $tags ) ) {
			return;
		}
		$tag_links = array();
		foreach ( $tags as $tag ) {
			$link        = get_tag_link( $tag->term_id );
			$tag_links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $tag->name . '</a>';
		}
		echo $before . join( $sep, $tag_links ) . $after;
	}
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
if ( ! function_exists( 'cyprus_categorized_blog' ) ) {
	function cyprus_categorized_blog() {
		$category_count = get_transient( 'cyprus_categories' );

		if ( false === $category_count ) {
			// Create an array of all the categories that are attached to posts.
			$categories = get_categories( array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$category_count = count( $categories );

			set_transient( 'cyprus_categories', $category_count );
		}

		return $category_count > 1;
	}
}
/**
 * Display the post info block.
 *
 * @param string $section
 */
if ( ! function_exists( 'cyprus_the_post_meta' ) ) {
	function cyprus_the_post_meta( $section = 'home', $groupid = '' ) {

		$enabled_metas = cyprus_get_settings( 'mts_' . $section . '_headline_meta_info' . $groupid );
		$enabled_metas = isset( $enabled_metas['enabled'] ) ? $enabled_metas['enabled'] : array();

		if ( ! empty( $enabled_metas ) ) :
		?>
			<div class="post-info">
				<?php
				foreach ( $enabled_metas as $key => $title ) {
					if ( 'single' === $section ) :
						$pagename = '_single';
						$suffix   = '';
					else :
						$pagename = '';
						$suffix   = '_';
					endif;
						$author_icon    = ( cyprus_get_settings( 'mts' . $pagename . '_meta_info_author_icon' . $suffix . $groupid ) !== 0 ) ? '<i class="fa fa-' . cyprus_get_settings( 'mts' . $pagename . '_meta_info_author_icon' . $suffix . $groupid ) . '"></i>' : '';
						$author_divider = cyprus_get_settings( 'mts' . $pagename . '_meta_info_author_divider' . $suffix . $groupid );
						$author_margin  = cyprus_get_settings( 'mts' . $pagename . '_meta_info_author_margin' . $suffix . $groupid );

						$date_icon    = ( cyprus_get_settings( 'mts' . $pagename . '_meta_info_date_icon' . $suffix . $groupid ) !== 0 ) ? '<i class="fa fa-' . cyprus_get_settings( 'mts' . $pagename . '_meta_info_date_icon' . $suffix . $groupid ) . '"></i>' : '';
						$date_divider = cyprus_get_settings( 'mts' . $pagename . '_meta_info_date_divider' . $suffix . $groupid );
						$date_margin  = cyprus_get_settings( 'mts' . $pagename . '_meta_info_date_margin' . $suffix . $groupid );

						$category_icon    = ( cyprus_get_settings( 'mts' . $pagename . '_meta_info_category_icon' . $suffix . $groupid ) !== 0 ) ? '<i class="fa fa-' . cyprus_get_settings( 'mts' . $pagename . '_meta_info_category_icon' . $suffix . $groupid ) . '"></i>' : '';
						$category_divider = cyprus_get_settings( 'mts' . $pagename . '_meta_info_category_divider' . $suffix . $groupid );
						$category_margin  = cyprus_get_settings( 'mts' . $pagename . '_meta_info_category_margin' . $suffix . $groupid );

						$comment_icon    = ( cyprus_get_settings( 'mts' . $pagename . '_meta_info_comment_icon' . $suffix . $groupid ) !== 0 ) ? '<i class="fa fa-' . cyprus_get_settings( 'mts' . $pagename . '_meta_info_comment_icon' . $suffix . $groupid ) . '"></i>' : '';
						$comment_divider = cyprus_get_settings( 'mts_meta_info_comment_divider' . $suffix . $groupid );
						$comment_margin  = cyprus_get_settings( 'mts_meta_info_comment_margin' . $suffix . $groupid );

					switch ( $key ) {

						case 'author':
							printf( '<span class="theauthor">%1$s <span>%2$s</span></span><span style="margin: 0 %3$s 0 %4$s;">%5$s</span>', $author_icon, get_the_author_posts_link(), $author_margin['right'], $author_margin['left'], $author_divider );
							break;

						case 'date':
							printf( '<span class="thetime date updated">%1$s <span>%2$s</span></span><span style="margin: 0 %3$s 0 %4$s;">%5$s</span>', $date_icon, get_the_time( get_option( 'date_format' ) ), $date_margin['right'], $date_margin['left'], $date_divider );
							break;

						case 'category':
							printf( '<span class="thecategory">%1$s %2$s</span><span style="margin: 0 %3$s 0 %4$s;">%5$s</span>', $category_icon, cyprus_get_the_category( ', ' ), $category_margin['right'], $category_margin['left'], $category_divider );
							break;

						case 'comment':
							printf( '<span class="thecomment">%1$s <a href="%2$s" itemprop="interactionCount">%3$s</a></span><span style="margin: 0 %4$s 0 %5$s;">%6$s</span>', $comment_icon, esc_url( get_comments_link() ), get_comments_number_text(), $comment_margin['right'], $comment_margin['left'], $comment_divider );
							break;
					}
				}
				?>
			</div>
		<?php
		endif;
	}
}
/**
 * Display cyprus-compliant the_category()
 *
 * @param string $separator
 */
if ( ! function_exists( 'cyprus_get_the_category' ) ) {
	function cyprus_get_the_category( $separator = ', ' ) {
		$categories = get_the_category();

		if ( empty( $categories ) ) {
			return;
		}

		global $wp_rewrite;

		$links = array();
		$rel   = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

		foreach ( $categories as $category ) {

			$links[] = sprintf(
				'<a href="%1$s" title="%2$s" %3$s>%4$s</a>',
				esc_url( get_category_link( $category->term_id ) ),
				// translators: category name.
				sprintf( esc_html__( 'View all posts in %s', 'cyprus' ), esc_attr( $category->name ) ),
				$rel,
				esc_html( $category->name )
			);
		}

		return join( $separator, $links );
	}
}
/**
 * Display the pagination.
 *
 * @param string $nav_type
 */
if ( ! function_exists( 'cyprus_pagination' ) ) {
	function cyprus_pagination( $nav_type = '' ) {

		if ( 1 === $GLOBALS['wp_query']->max_num_pages ) {
			return;
		}

		if ( 1 === $nav_type ) { // Numeric pagination.
			the_posts_pagination( array(
				'mid_size'  => 3,
				'prev_text' => esc_html__( 'Previous', 'cyprus' ),
				'next_text' => esc_html__( 'Next', 'cyprus' ),
			) );
		} else { // traditional or ajax pagination.
			?>
			<div class="pagination pagination-previous-next">
				<ul>
					<li class="nav-previous"><?php next_posts_link( '<i class="fa fa-angle-left"></i> ' . esc_html__( 'Previous', 'cyprus' ) ); ?></li>
					<li class="nav-next"><?php previous_posts_link( esc_html__( 'Next', 'cyprus' ) . ' <i class="fa fa-angle-right"></i>' ); ?></li>
				</ul>
			</div>
			<?php
		}
	}
}
/**
 * Display social icons
 * @param  array  $data
 * @return html
 */
if ( ! function_exists( 'cyprus_social_icons' ) ) {
	function cyprus_social_icons( $data = array(), $echo = false ) {

		if ( empty( $data ) ) {
			return;
		}

		$out = '<div class="header-social-icons">';
		foreach ( $data as $item ) {
			$out .= sprintf(
				'<a href="%1$s" title="%2$s" class="header-%3$s" target="_blank"><span class="fa fa-%3$s"></span></a>',
				$item['mts_header_icon_link'],
				$item['mts_header_icon_title'],
				$item['mts_header_icon']
			);
		}

		$out .= '</div>';

		if ( $echo ) {
			echo $out;
		} else {
			return $out;
		}
	}
}
/**
 * Create and show column for featured image in the post list of admin page
 * @param $post_id
 * @return string url
 */
if ( ! function_exists( 'cyprus_get_featured_image' ) ) {
	function cyprus_get_featured_image( $post_id ) {
		if ( $post_thumbnail_id = get_post_thumbnail_id( $post_id ) ) { // @codingStandardsIgnoreLine
			$post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, 'cyprus-widgetfull' );
			return $post_thumbnail_img[0];
		}
	}
}

/**
 * Adds a `Featured Image` column header in the Post list of admin page
 *
 * @param array $defaults
 * @return array
 */
if ( ! function_exists( 'cyprus_columns_head' ) ) {
	function cyprus_columns_head( $defaults ) {
		if ( 'post' === get_post_type() ) {
			$defaults['featured_image'] = __( 'Featured Image', 'cyprus' );
		}

		return $defaults;
	}
}
add_filter( 'manage_posts_columns', 'cyprus_columns_head' );

/**
 * Adds a `Featured Image` column row value in the Post list of admin page
 *
 * @param string $column_name The name of the column to display.
 * @param int $post_id The ID of the current post.
 */
if ( ! function_exists( 'cyprus_columns_content' ) ) {
	function cyprus_columns_content( $column_name, $post_id ) {
		if ( 'featured_image' === $column_name ) {
			if ( $post_featured_image = cyprus_get_featured_image( $post_id ) ) { // @codingStandardsIgnoreLine
				echo '<img width="150" height="100" src="' . esc_url( $post_featured_image ) . '" />';
			}
		}
	}
}
add_action( 'manage_posts_custom_column', 'cyprus_columns_content', 10, 2 );

/**
 * Add data-layzr attribute to featured image ( for lazy load )
 *
 * @param array $attr
 * @param WP_Post $attachment
 * @param string|array $size
 *
 * @return array
 */
if ( ! function_exists( 'cyprus_image_lazy_load_attr' ) ) {
	function cyprus_image_lazy_load_attr( $attr, $attachment, $size ) {
		if ( is_admin() || is_feed() ) {
			return $attr;
		}

		if ( 'cyprus-slider' === $size && is_home() ) {
			return $attr;
		}

		if ( ! empty( cyprus_get_settings( 'cyprus_lazy_load' ) ) && ! empty( cyprus_get_settings( 'cyprus_lazy_load_thumbs' ) ) ) {
			$attr['data-layzr'] = $attr['src'];
			$attr['src']        = '';
			if ( isset( $attr['srcset'] ) ) {
				$attr['data-layzr-srcset'] = $attr['srcset'];
				$attr['srcset']            = '';
			}
		}

		return $attr;
	}
}
add_filter( 'wp_get_attachment_image_attributes', 'cyprus_image_lazy_load_attr', 10, 3 );

/**
 * Add data-layzr attribute to post content images ( for lazy load )
 *
 * @param string $content
 *
 * @return string
 */

function cyprus_content_image_lazy_load_attr( $content ) {
	if (
		! empty( cyprus_get_settings( 'cyprus_lazy_load' ) ) &&
		! empty( cyprus_get_settings( 'cyprus_lazy_load_content' ) ) &&
		! empty( $content )
	) {
		$content = preg_replace_callback(
			'/<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/',
			'cyprus_content_image_lazy_load_attr_callback',
			$content
		);
	}

	return $content;
}
add_filter( 'the_content', 'cyprus_content_image_lazy_load_attr' );

/**
 * Callback to move src to data-src and replace it with a 1x1 tranparent image.
 *
 * @param $matches
 *
 * @return string
 */
if ( ! function_exists( 'cyprus_content_image_lazy_load_attr_callback' ) ) {
	function cyprus_content_image_lazy_load_attr_callback( $matches ) {
		$transparent_img = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
		if ( preg_match( '/ data-lazy *= *"false" */', $matches[0] ) ) {
			return '<img' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '>';
		} else {
			return '<img' . $matches[1] . 'src="' . $transparent_img . '" data-layzr="' . $matches[2] . '"' . str_replace( 'srcset=', 'data-layzr-srcset=', $matches[3] ) . '>';
		}
	}
}

/**
 * Footer Widget Columns
 *
 * @return string
 */
function cyprus_footer_widget_columns() {

	$footer_columns = intval( cyprus_get_settings( 'mts_top_footer_num' ) );

	if ( ! $footer_columns ) {
		return;
	}
	?>

		<div class="footer-widgets first-footer-widgets widgets-num-<?php echo esc_attr( $footer_columns ); ?>">
			<?php
			for ( $i = 1; $i <= $footer_columns; $i++ ) :

				$class = '';
				if ( $i === $footer_columns ) {
					$class = ' last';
				} elseif ( 1 === $i ) {
					$class = ' first';
				}
			?>
				<div class="f-widget f-widget-<?php echo esc_attr( $i ) . esc_attr( $class ); ?>">
					<?php dynamic_sidebar( 'footer-top-' . $i ); ?>
				</div>
			<?php endfor; ?>
		</div><!--.first-footer-widgets-->

	<?php
}

/**
 * Copyrights section
 *
 * @return string
 */
function cyprus_footer_copyrights() {
	$content = cyprus_get_settings( 'mts_copyrights' );

	$copyright_text = '<a href=" ' . esc_url( trailingslashit( home_url() ) ) . '" title=" ' . get_bloginfo( 'description' ) . '">' . get_bloginfo( 'name' ) . '</a> ' . __( 'Copyright', 'cyprus' ) . ' &copy; ' . date( 'Y' ) . '.';
	?>
	<div class="copyrights">
		<div class="container">
			<div class="row" id="copyright-note">
				<span><?php echo $copyright_text; ?></span>
				<div class="to-top"><?php echo $content; ?></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Cyprus display comments
 */
function cyprus_display_comments() {
	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || 0 !== intval( get_comments_number() ) ) :
		comments_template();
	endif;
}

if ( ! function_exists( 'cyprus_comments' ) ) {
	/**
	 * Custom comments template.
	 *
	 * @param array $comment    get comment.
	 * @param array $args
	 * @param int   $depth    child depth.
	 */
	function cyprus_comments( $comment, $args, $depth ) {
	?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<?php
			switch ( $comment->comment_type ) :
				case 'pingback':
				case 'trackback':
				?>
					<div id="comment-<?php comment_ID(); ?>">
						<div class="comment-author vcard">
							Pingback: <?php comment_author_link(); ?>
							<?php if ( ! empty( cyprus_get_settings( 'mts_comment_date' ) ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php } ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'cyprus' ), '  ', '' ); ?>
							</span>
						</div>
						<?php if ( '0' === $comment->comment_approved ) : ?>
							<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'cyprus' ); ?></em>
							<br />
						<?php endif; ?>
					</div>
				<?php
					break;

				default:
				?>
					<div id="comment-<?php comment_ID(); ?>" itemscope itemtype="http://schema.org/UserComments">
						<div class="comment-author vcard">
							<?php echo get_avatar( $comment->comment_author_email, 80 ); ?>
							<?php printf( '<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person"><span itemprop="name">%s</span></span>', get_comment_author_link() ); ?>
							<?php if ( ! empty( cyprus_get_settings( 'mts_comment_date' ) ) ) { ?>
								<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
							<?php } ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'cyprus' ), '  ', '' ); ?>
							</span>
						</div>
						<?php if ( '0' === $comment->comment_approved ) : ?>
							<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'cyprus' ); ?></em>
							<br />
						<?php endif; ?>
						<div class="commentmetadata">
							<div class="commenttext" itemprop="commentText">
								<?php comment_text(); ?>
							</div>
							<div class="reply">
								<?php
								comment_reply_link( array_merge( $args, array(
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
								) ) );
								?>
							</div>
						</div>
					</div>
				<?php
					break;
			endswitch;
			?>
	<!-- WP adds </li> -->
	<?php
	}
}

/**
 * Display a "read more" link.
 */
function cyprus_readmore() {
	?>
	<div class="readMore">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php esc_html_e( 'Read More', 'cyprus' ); ?></a>
	</div>
	<?php
}

if ( ! function_exists( 'cyprus_the_postinfo_item' ) ) {
	/**
	 * Display information of an item.
	 *
	 * @param $item
	*/
	function cyprus_the_postinfo_item( $item ) {

		$hash = array(
			'author'   => '<span class="theauthor"><i class="fa fa-user"></i> <span>' . get_the_author_posts_link() . '</span></span>',
			'date'     => '<span class="thetime date updated"><i class="fa fa-calendar"></i> <span>' . get_the_time( get_option( 'date_format' ) ) . '</span></span>',
			'category' => '<span class="thecategory"><i class="fa fa-tags"></i> ' . cyprus_get_the_category( ', ' ) . '</span>',
			'comment'  => '<span class="thecomment"><i class="fa fa-comments"></i> <a href="' . esc_url( get_comments_link() ) . '" itemprop="interactionCount"><?php comments_number();?></a></span>',
		);

		return isset( $hash[ $item ] ) ? $hash[ $item ] : '';
	}
}

if ( ! function_exists( 'cyprus_breadcrumbs' ) ) {
	/**
	 * Display the breadcrumbs.
	 */
	function cyprus_breadcrumbs() {
		if ( is_front_page() ) {
				return;
		}
		$arrow = cyprus_get_settings( 'breadcrumb_icon' );
		echo '<div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">';
		echo '<div><i class="fa fa-home"></i></div> <div typeof="v:Breadcrumb" class="root"><a rel="v:url" property="v:title" href="';
		echo esc_url( home_url() );
		echo '">' . esc_html( sprintf( __( 'Home', 'cyprus' ) ) );
		echo '</a></div><div><i class="fa fa-' . $arrow . '"></i></div>';
		if ( is_single() ) {
			$categories = get_the_category();
			if ( $categories ) {
				$level         = 0;
				$hierarchy_arr = array();
				foreach ( $categories as $cat ) {
					$anc       = get_ancestors( $cat->term_id, 'category' );
					$count_anc = count( $anc );
					if ( 0 < $count_anc && $level < $count_anc ) {
						$level         = $count_anc;
						$hierarchy_arr = array_reverse( $anc );
						array_push( $hierarchy_arr, $cat->term_id );
					}
				}
				if ( empty( $hierarchy_arr ) ) {
					$category = $categories[0];
					echo '<div typeof="v:Breadcrumb"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="v:url" property="v:title">' . esc_html( $category->name ) . '</a></div><div><i class="fa fa-' . $arrow . '"></i></div>';
				} else {
					foreach ( $hierarchy_arr as $cat_id ) {
						$category = get_term_by( 'id', $cat_id, 'category' );
						echo '<div typeof="v:Breadcrumb"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="v:url" property="v:title">' . esc_html( $category->name ) . '</a></div><div><i class="fa fa-' . $arrow . '"></i></div>';
					}
				}
			}
			echo '<div><span>';
			the_title();
			echo '</span></div></div>';
		} elseif ( is_page() ) {
			$parent_id = wp_get_post_parent_id( get_the_ID() );
			if ( $parent_id ) {
				$breadcrumbs = array();
				while ( $parent_id ) {
					$page          = get_page( $parent_id );
					$breadcrumbs[] = '<div typeof="v:Breadcrumb"><a href="' . esc_url( get_permalink( $page->ID ) ) . '" rel="v:url" property="v:title">' . esc_html( get_the_title( $page->ID ) ) . '</a></div><div><i class="fa fa-' . $arrow . '"></i></div>';
					$parent_id     = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) {
					echo $crumb;
				}
			}
				echo '<div><span>';
				the_title();
				echo '</span></div></div>';
		} elseif ( is_category() ) {
			global $wp_query;
			$cat_obj       = $wp_query->get_queried_object();
			$this_cat_id   = $cat_obj->term_id;
			$hierarchy_arr = get_ancestors( $this_cat_id, 'category' );
			if ( $hierarchy_arr ) {
				$hierarchy_arr = array_reverse( $hierarchy_arr );
				foreach ( $hierarchy_arr as $cat_id ) {
					$category = get_term_by( 'id', $cat_id, 'category' );
					echo '<div typeof="v:Breadcrumb"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="v:url" property="v:title">' . esc_html( $category->name ) . '</a></div><div><i class="fa fa-' . $arrow . '"></i></div>';
				}
			}
			echo '<div><span>';
			single_cat_title();
			echo '</span></div></div>';
		} elseif ( is_author() ) {
			echo '<div><span>';
			if ( get_query_var( 'author_name' ) ) :
				$curauth = get_user_by( 'slug', get_query_var( 'author_name' ) );
			else :
				$curauth = get_userdata( get_query_var( 'author' ) );
			endif;
			echo esc_html( $curauth->nickname );
			echo '</span></div></div>';
		} elseif ( is_search() ) {
			echo '<div><span>';
			the_search_query();
			echo '</span></div></div>';
		} elseif ( is_tag() ) {
			echo '<div><span>';
			single_tag_title();
			echo '</span></div></div>';
		}
	}
}

if ( ! empty( cyprus_get_settings( 'mts_feedburner' ) ) ) {
	/**
	 * Redirect feed to FeedBurner if a FeedBurner URL has been set.
	 */
	function cyprus_rss_feed_redirect() {
		global $feed;
		$new_feed = cyprus_get_settings( 'mts_feedburner' );
		if ( !is_feed() ) {
				return;
		}
		if ( preg_match( '/feedburner/i', $_SERVER['HTTP_USER_AGENT'] )){
				return;
		}
		if ( $feed != 'comments-rss2' ) {
				if ( function_exists( 'status_header' )) status_header( 302 );
				header( "Location:" . $new_feed );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
		}
	}
	add_action( 'template_redirect', 'cyprus_rss_feed_redirect' );
}

if ( ! function_exists( 'cyprus_related_posts' ) ) {
	/**
	 * Display the related posts.
	 */
	function cyprus_related_posts() {
		$post_id = get_the_ID();

		if( ! empty( cyprus_get_settings( 'mts_single_post_layout' )['enabled']['related'] ) ) {
			$empty_taxonomy = false;
			if ( empty( cyprus_get_settings( 'mts_related_posts_taxonomy' ) ) || cyprus_get_settings( 'mts_related_posts_taxonomy' ) == 'tags' ) {
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
						'post__not_in'        => array($post_id),
						'posts_per_page'      => ( null !== cyprus_get_settings( 'mts_related_postsnum' ) ) ? cyprus_get_settings( 'mts_related_postsnum' ) : 3,
						'ignore_sticky_posts' => 1,
						'orderby'             => 'rand'
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
					$args = array(
						'category__in'        => $category_ids,
						'post__not_in'        => array($post_id),
						'posts_per_page'      => cyprus_get_settings( 'mts_related_postsnum' ),
						'ignore_sticky_posts' => 1,
						'orderby'             => 'rand',
					);
				}
			}
			if (!$empty_taxonomy) {
				$my_query = new WP_Query( $args );
				if( $my_query->have_posts() ) {
					$title = ! empty( cyprus_get_settings( 'related_post_title' ) ) ? cyprus_get_settings( 'related_post_title' ) : __('Related Posts', 'cyprus' );
					$related_posts_layouts = ! empty( cyprus_get_settings( 'related_posts_layouts' ) ) ? cyprus_get_settings( 'related_posts_layouts' ) : 'default';
					$grid = ! empty( cyprus_get_settings( 'related_posts_grid' ) ) && 'default' === $related_posts_layouts ? 'flex-grid ' . cyprus_get_settings( 'related_posts_grid' ) : 'flex-grid default';
					$position = ! empty( cyprus_get_settings( 'related_posts_position' ) ) ? cyprus_get_settings( 'related_posts_position' ) : 'default';
					echo '<div class="related-posts ' . $related_posts_layouts . ' position-' . $position . ' ">';
					echo ( 'full' === $position ) ? '<div class="container">' : '';
					echo '<div class="related-posts-title"><h4>' . $title . '</h4></div>';
					echo '<div class="related-posts-container clear">';
					$posts_per_row = 3;
					$j = 0;
					while( $my_query->have_posts() ) { $my_query->the_post();
						$post_meta_info = cyprus_get_settings( 'related_post_meta_info' );
						switch ($related_posts_layouts) {
							case 'default':
							case 'related2':
							?>
								<article class="latestPost excerpt <?php echo $grid; ?>">
									<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" id="featured-thumbnail">
										<div class="featured-thumbnail">
											<?php the_post_thumbnail( 'cyprus-related', array( 'title' => '' ) ); ?>
										</div>
										<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
									</a>
									<header>
										<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<?php
										if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
											<div class="post-info">
												<?php
												if ( isset( $post_meta_info['author'] ) ) :
													printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
												endif;
												if ( isset( $post_meta_info['time'] ) ) :
													printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
												endif;
												if ( isset( $post_meta_info['category'] ) ) :
													printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
												endif;
												if ( isset( $post_meta_info['comment'] ) ) :
													printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
												endif;
												?>
											</div>
										<?php endif; ?>
									</header>
								</article><!--.post.excerpt-->
							<?php
							break;

							case 'related3':
							?>
								<article class="latestPost excerpt <?php echo $grid; ?>">
									<header>
										<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<?php
										if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
											<div class="post-info">
												<?php
												if ( isset( $post_meta_info['author'] ) ) :
													printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
												endif;
												if ( isset( $post_meta_info['time'] ) ) :
													printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
												endif;
												if ( isset( $post_meta_info['category'] ) ) :
													printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
												endif;
												if ( isset( $post_meta_info['comment'] ) ) :
													printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
												endif;
												?>
											</div>
										<?php endif; ?>
									</header>
								</article><!--.post.excerpt-->
							<?php
							break;

							case 'related4':
							?>
							<article class="latestPost excerpt <?php echo $grid . ' '; echo ( 1 === ++$j ) ? 'big' : 'small'; ?>">
								<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" id="featured-thumbnail">
									<div class="featured-thumbnail">
										<?php the_post_thumbnail( 1 === $j ? 'cyprus-layout-4' : 'cyprus-layout-small-2', array( 'title' => '' ) ); ?>
									</div>
									<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
								</a>
								<div class="wrapper">
									<header>
										<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									</header>
									<?php if ( 1 === $j ) : ?>
										<div class="front-view-content">
											<?php $excerpt = ! empty( cyprus_get_settings( 'related_posts_excerpt_length' ) ) ? cyprus_get_settings( 'related_posts_excerpt_length' ) : '45';
											echo cyprus_excerpt( $excerpt ); ?>
										</div>
										<?php if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
											<div class="post-info">
												<?php
												if ( isset( $post_meta_info['author'] ) ) :
													printf( '<span class="theauthor"><span>%s</span></span><span style="margin: 0 5px 0 -12px;">|</span>', get_the_author_posts_link() );
												endif;
												if ( isset( $post_meta_info['time'] ) ) :
													printf( '<span class="thetime date updated"><span>%s</span></span><span style="margin: 0 5px 0 -12px;">|</span>', get_the_time( get_option( 'date_format' ) ) );
												endif;
												if ( isset( $post_meta_info['category'] ) ) :
													printf( '<span class="thecategory">%s</span><span style="margin: 0 5px 0 -12px;">|</span>', cyprus_get_the_category( ' ' ) );
												endif;
												if ( isset( $post_meta_info['comment'] ) ) :
													printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
												endif;
												?>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</article><!--.post.excerpt-->
							<?php
							break;

							case 'related5':
							?>
								<article class="latestPost excerpt flex-grid grid2">
									<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" id="featured-thumbnail">
										<div class="featured-thumbnail">
											<?php the_post_thumbnail( 'cyprus-related', array( 'title' => '' ) ); ?>
										</div>
										<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
									</a>
									<header>
										<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<?php
										if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
											<div class="post-info">
												<?php
												if ( isset( $post_meta_info['author'] ) ) :
													printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
												endif;
												if ( isset( $post_meta_info['time'] ) ) :
													printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
												endif;
												if ( isset( $post_meta_info['category'] ) ) :
													printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
												endif;
												if ( isset( $post_meta_info['comment'] ) ) :
													printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
												endif;
												?>
											</div>
										<?php endif; ?>
									</header>
								</article><!--.post.excerpt-->
								<?php
								if ( 1 === ++$j ) {
									$ad_code = cyprus_get_settings( 'related_posts_adcode' );
									if ( empty( trim( $ad_code ) ) ) {
									  return;
									}
									?>
									<div class="related-posts-ad">
										<?php echo do_shortcode( $ad_code ); ?>
									</div>
								<?php
								}
							break;

							case 'related6':
							?>
							<article class="latestPost excerpt <?php echo $grid; ?>">
								<header>
									<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<?php
									if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
										<div class="post-info">
											<?php
											if ( isset( $post_meta_info['author'] ) ) :
												printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
											endif;
											if ( isset( $post_meta_info['time'] ) ) :
												printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
											endif;
											if ( isset( $post_meta_info['category'] ) ) :
												printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
											endif;
											if ( isset( $post_meta_info['comment'] ) ) :
												printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
											endif;
											?>
										</div>
									<?php endif; ?>
								</header>
							</article><!--.post.excerpt-->
							<?php
							break;

							default:
							?>
							<article class="latestPost excerpt <?php echo $grid; ?>">
								<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" id="featured-thumbnail">
									<div class="featured-thumbnail">
										<?php the_post_thumbnail( 'cyprus-related', array( 'title' => '' ) ); ?>
									</div>
									<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
								</a>
								<header>
									<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<?php
									if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) || isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) : ?>
										<div class="post-info">
											<?php
											if ( isset( $post_meta_info['author'] ) ) :
												printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
											endif;
											if ( isset( $post_meta_info['time'] ) ) :
												printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
											endif;
											if ( isset( $post_meta_info['category'] ) ) :
												printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
											endif;
											if ( isset( $post_meta_info['comment'] ) ) :
												printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
											endif;
											?>
										</div>
									<?php endif; ?>
								</header>
							</article><!--.post.excerpt-->
							<?php
							break;
						}
					}
					echo '</div>';

					if ( 'related6' === cyprus_get_settings( 'related_posts_layouts' ) ) {
						$ad_code = cyprus_get_settings( 'related_posts_adcode' );
						if ( empty( trim( $ad_code ) ) ) {
							return;
						}
						?>
						<div class="related-posts-ad">
							<?php echo do_shortcode( $ad_code ); ?>
						</div>
					<?php
					}

					echo ( 'full' === $position ) ? '</div>' : '';
					echo '</div>';
				}
			}
			wp_reset_postdata();
			?>
		<!-- .related-posts -->
	<?php }
	}
}

/**
 * Display the Social Sharing buttons.
 */
if ( !function_exists( 'cyprus_social_buttons' ) ) {
	function cyprus_social_buttons() {
			$buttons = array();
			$layout = cyprus_get_settings( 'social_button_layout') ? cyprus_get_settings( 'social_button_layout') : 'default';
			if ( null !== cyprus_get_settings( 'mts_social_buttons' ) && is_array( cyprus_get_settings( 'mts_social_buttons' ) ) && array_key_exists( 'enabled', cyprus_get_settings( 'mts_social_buttons' ) ) ) {
					$buttons = cyprus_get_settings( 'mts_social_buttons' )['enabled'];

			}
			if ( ! empty( $buttons ) ) {
				switch ( $layout ) {
					case 'default':
						?>
							<div class="shareit shareit-default <?php echo cyprus_get_settings( 'mts_social_button_position' ); ?>">
								<?php foreach( $buttons as $key => $button ) { cyprus_social_button( $key ); } ?>
							</div>
						<?php
						break;

					case 'rectwithname':
						?>
							<div class="shareit shareit-rectwithname <?php echo cyprus_get_settings( 'mts_social_button_position' ); ?>">
								<?php foreach( $buttons as $key => $button ) { cyprus_social_rectwithname_button( $key ); } ?>
							</div>
						<?php
						break;

					case 'circwithname':
					case 'circular':
					case 'standard':
						?>
							<div class="shareit shareit-circular <?php echo $layout . ' ' . cyprus_get_settings( 'mts_social_button_position' ); ?>">
								<?php foreach( $buttons as $key => $button ) { cyprus_social_circular_button( $key ); } ?>
							</div>
						<?php
						break;

					case 'rectwithcount':
						?>
							<div class="shareit shareit-rectwithcount <?php echo cyprus_get_settings( 'mts_social_button_position' ); ?>">
								<?php foreach( $buttons as $key => $button ) { cyprus_social_rectwithcount_button( $key ); } ?>
							</div>
						<?php
						break;

					default:
						?>
							<div class="shareit shareit-default <?php echo cyprus_get_settings( 'mts_social_button_position' ); ?>">
								<?php foreach( $buttons as $key => $button ) { cyprus_social_button( $key ); } ?>
							</div>
						<?php
						break;
				}
			}
	}
}
/**
 * Display network-independent sharing buttons.
 *
 * @param $button
 */
if ( ! function_exists('cyprus_social_button' ) ) {
	function cyprus_social_button( $button ) {
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
					<a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo esc_attr( cyprus_get_settings( 'mts_twitter_username' ) ); ?>"><?php esc_html_e( 'Tweet', 'cyprus' ); ?></a>
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
			case 'facebook':
			?>
				<!-- Facebook -->
				<span class="share-item facebookbtn">
					<div id="fb-root"></div>
					<div class="fb-like" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div>
				</span>
			<?php
			break;
			case 'pinterest':
			?>
				<!-- Pinterest -->
				<span class="share-item pinbtn">
					<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' ); echo $thumb['0']; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal"><?php esc_html_e( 'Pin It', 'cyprus' ); ?></a>
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
					<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumble" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="stumble-icon"><i class="fa fa-stumbleupon"></i></span><span class="stumble-text"><?php _e('Share', 'cyprus'); ?></span></a>
				</span>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<span class="share-item reddit">
					<a href="//www.reddit.com/submit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"> <img src="<?php echo get_template_directory_uri().'/images/reddit.png' ?>" alt=<?php _e( 'submit to reddit', 'cyprus' ); ?> border="0" /></a>
				</span>
			<?php
			break;
		}
	}
}
/**
 * Display network-independent sharing buttons.
 *
 * @param $button
 */
if ( ! function_exists( 'cyprus_social_rectwithname_button' ) ) {
	function cyprus_social_rectwithname_button( $button ) {
		global $post;
		if( is_single() ){
			$imgUrl = $img = '';
			if ( has_post_thumbnail( $post->ID ) ){
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cyprus-featuredfull' );
				$imgUrl = $img[0];
			}
		}
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook -->
				<a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode(get_permalink()); ?>&p[images][0]=<?php echo urlencode($imgUrl[0]); ?>&p[title]=<?php echo urlencode(get_the_title()); ?>&u=<?php echo urlencode( get_permalink() ); ?>&t=<?php echo urlencode( get_the_title() ); ?>" class="facebooksharebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook"></i><?php _e('Share', 'cyprus'); ?></a>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<?php $via = '';
				if( cyprus_get_settings( 'mts_twitter_username' ) ) {
					$via = '&via='. cyprus_get_settings( 'mts_twitter_username' );
				} ?>
				<a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(get_permalink()); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo urlencode(get_permalink()); ?><?php echo $via; ?>" class="twitterbutton" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-twitter"></i> <?php _e('Tweet', 'cyprus'); ?></a>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<a href="//plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="gplusbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-google-plus"></i><?php _e('Share', 'cyprus'); ?></a>
			<?php
			break;
			case 'facebook':
			?>
				<!-- Facebook -->
				<span class="rectwithname facebookbtn">
					<div id="fb-root"></div>
					<div class="fb-like" data-send="false" data-layout="button" data-width="150" data-show-faces="false"></div>
				</span>
			<?php
			break;
			case 'pinterest':
				global $post;
			?>
				<!-- Pinterest -->
				<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
				<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="share-pinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-pinterest-p"></i><?php _e('Pin', 'cyprus'); ?></a>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<a href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo 'url'; ?>" class="linkedinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-linkedin"></i><?php _e('Share', 'cyprus'); ?></a>
			<?php
			break;
			case 'stumble':
			?>
				<!-- Stumble -->
				<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumblebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-stumbleupon"></i><?php _e('Stumble', 'cyprus'); ?></a>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<a href="//www.reddit.com/submit" class="reddit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-reddit-alien"></i><?php _e('Reddit', 'cyprus'); ?></a>
			<?php
			break;
		}
	}
}
/**
 * Display network-independent sharing buttons.
 *
 * @param $button
 */
if ( ! function_exists( 'cyprus_social_circular_button' ) ) {
	function cyprus_social_circular_button( $button ) {
		global $post;
		if( is_single() ){
			$imgUrl = $img = '';
			if ( has_post_thumbnail( $post->ID ) ){
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cyprus-featuredfull' );
				$imgUrl = $img[0];
			}
		}
		switch ( $button ) {
			case 'facebookshare':
			?>
				<!-- Facebook -->
				<a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode(get_permalink()); ?>&p[images][0]=<?php echo urlencode($imgUrl[0]); ?>&p[title]=<?php echo urlencode(get_the_title()); ?>&u=<?php echo urlencode( get_permalink() ); ?>&t=<?php echo urlencode( get_the_title() ); ?>" class="facebooksharebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-facebook"></i></span><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></a>
			<?php
			break;
			case 'twitter':
			?>
				<!-- Twitter -->
				<?php $via = '';
				if( cyprus_get_settings( 'mts_twitter_username' ) ) {
					$via = '&via='. cyprus_get_settings( 'mts_twitter_username' );
				} ?>
				<a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(get_permalink()); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo urlencode(get_permalink()); ?><?php echo $via; ?>" class="twitterbutton" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-twitter"></i></span> <span class="social-text"><?php _e('Tweet', 'cyprus'); ?></span></a>
			<?php
			break;
			case 'gplus':
			?>
				<!-- GPlus -->
				<a href="//plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="gplusbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-google-plus"></i></span><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></a>
			<?php
			break;

			case 'pinterest':
				global $post;
			?>
				<!-- Pinterest -->
				<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
				<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="share-pinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-pinterest-p"></i></span><span class="social-text"><?php _e('Pin', 'cyprus'); ?></span></a>
			<?php
			break;
			case 'linkedin':
			?>
				<!--Linkedin -->
				<a href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo 'url'; ?>" class="linkedinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-linkedin"></i></span><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></a>
			<?php
			break;
			case 'stumble':
			?>
				<!-- Stumble -->
				<a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumblebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-stumbleupon"></i></span><span class="social-text"><?php _e('Stumble', 'cyprus'); ?></span></a>
			<?php
			break;
			case 'reddit':
			?>
				<!-- Reddit -->
				<a href="//www.reddit.com/submit" class="reddit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-reddit-alien"></i></span><span class="social-text"><?php _e('Reddit', 'cyprus'); ?></span></a>
			<?php
			break;
		}
	}
}
/**
 * Display network-independent sharing buttons.
 *
 * @param $button
 */
 if ( ! function_exists( 'cyprus_social_rectwithcount_button' ) ) {
	 function cyprus_social_rectwithcount_button( $button ) {
		 global $post;
		 if( is_single() ){
			 $imgUrl = $img = '';
			 if ( has_post_thumbnail( $post->ID ) ){
				 $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cyprus-featuredfull' );
				 $imgUrl = $img[0];
			 }
		 }
		 switch ( $button ) {
			 case 'facebookshare':
			 ?>
				 <!-- Facebook -->
				 <a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode(get_permalink()); ?>&p[images][0]=<?php echo urlencode($imgUrl[0]); ?>&p[title]=<?php echo urlencode(get_the_title()); ?>&u=<?php echo urlencode( get_permalink() ); ?>&t=<?php echo urlencode( get_the_title() ); ?>" class="facebooksharebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-facebook"></i><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
			 case 'twitter':
			 ?>
				 <!-- Twitter -->
				 <?php $via = '';
				 if( cyprus_get_settings( 'mts_twitter_username' ) ) {
					 $via = '&via='. cyprus_get_settings( 'mts_twitter_username' );
				 } ?>
				 <a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(get_permalink()); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo urlencode(get_permalink()); ?><?php echo $via; ?>" class="twitterbutton" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-twitter"></i> <span class="social-text"><?php _e('Tweet', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
			 case 'gplus':
			 ?>
				 <!-- GPlus -->
				 <a href="//plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" class="gplusbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-google-plus"></i><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></span></a>
			 <?php
			 break;

			 case 'pinterest':
				 global $post;
			 ?>
			 <!-- Pinterest -->
			 <?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
				 <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $pinterestimage[0]; ?>&description=<?php the_title(); ?>" class="share-pinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-pinterest-p"></i><span class="social-text"><?php _e('Pin', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
			 case 'linkedin':
			 ?>
				 <!--Linkedin -->
				 <a href="//www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo 'url'; ?>" class="linkedinbtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-linkedin"></i><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
			 case 'stumble':
			 ?>
				 <!-- Stumble -->
				 <a href="http://www.stumbleupon.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php the_title(); ?>" class="stumblebtn" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-stumbleupon"></i><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
			 case 'reddit':
			 ?>
				 <!-- Reddit -->
				 <a href="//www.reddit.com/submit" class="reddit" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="social-icon"><i class="fa fa-reddit-alien"></i><span class="social-text"><?php _e('Share', 'cyprus'); ?></span></span></a>
			 <?php
			 break;
		 }
	 }
 }

/**
 * Display a post of specific layout.
 *
 * @param string $layout
 */
 if ( ! function_exists( 'cyprus_blog_articles' ) ) {
	function cyprus_blog_articles( $layout = '' ) {
	?>
		<section id="latest-posts" class="<?php echo esc_attr( $layout ); ?> clearfix">
				<article class="latestPost excerpt">
					<header>
						<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" id="featured-thumbnail" class="post-image post-image-left <?php echo 'layout-default'; ?>">
							<div class="featured-thumbnail">
								<?php the_post_thumbnail( 'cyprus-featured', array( 'title' => '' ) ); ?>
							</div>
							<?php
							if ( function_exists( 'wp_review_show_total' ) ) {
								wp_review_show_total( true, 'latestPost-review-wrapper' );
							}
							?>
						</a>
					<?php endif; ?>

					<div class="front-view-content">
						<?php echo cyprus_excerpt( '29' ); ?>
					</div>
					<?php cyprus_readmore(); ?>
				</article>
		</section><!--#latest-posts-->
	<?php
	}
}

/**
 * Display Instagram feeds.
 *
 * returns a big old hunk of JSON from a non-private IG account page.
 *
 * @param string $layout
 */
function scrape_insta( $username ) {
	$request         = wp_remote_get( 'http://instagram.com/' . $username );
	$insta_source    = wp_remote_retrieve_body( $request );
	$shards          = explode( 'window._sharedData = ', $insta_source );
	$insta_json      = explode( ';</script>', $shards[1] );
	$insta_array     = json_decode( $insta_json[0], TRUE );
	$update_interval = apply_filters( 'cyprus_instagram_update_interval', 3600 );
	$insta_serialize = serialize ( $insta_array );
	set_transient( 'cyprus_instagram_' . $username, $insta_serialize , $update_interval );
	return $insta_array;
}
if( ! function_exists( 'cyprus_instagram' ) ) {

	function cyprus_instagram( $username, $number, $force_refresh = false ) {
		$results_serialized = get_transient( 'cyprus_instagram_' . $username );
		if ( false === $results_serialized ) {
			$results_array = scrape_insta( $username );
		} else {
			$results_array = unserialize( $results_serialized );
		}
		for ($i=0; $i < $number; $i++) {
		  if ( isset( $results_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i] ) ) {
		  $latest_array = $results_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i];
		  if ($latest_array['node']['is_video']) {
			  echo '<a href="http://instagram.com/p/' . $latest_array['node']['shortcode'].'" target="_blank"><div style="background-image:url('.preg_replace('/vp.*\/.{32}\/.{8}\//', '', $latest_array['node']['thumbnail_resources'][2]['src']).');"></div></a>';
		  } else {
			  echo '<a href="http://instagram.com/p/' . $latest_array['node']['shortcode'].'" target="_blank"><div style="background-image:url(' . $latest_array['node']['thumbnail_resources'][2]['src'].');"></div></a>';
		  }
		  }
	  }

	}
}

/**
 *  Array containing all Adsense Ad sizees [Used in Options Panel]
 *
 * @return array
 */
function ad_sizes() {
	$ad_sizes = array(
		'0'=> 'Auto',
		'1' => '120 x 90',
		'2' => '120 x 240',
		'3' => '120 x 600',
		'4' => '125 x 125',
		'5' => '160 x 90',
		'6' => '160 x 600',
		'7' => '180 x 90',
		'8' => '180 x 150',
		'9' => '200 x 90',
		'10' => '200 x 200',
		'11' => '234 x 60',
		'12' => '250 x 250',
		'13' => '320 x 100',
		'14' => '300 x 250',
		'15' => '300 x 600',
		'16' => '300 x 1050',
		'17' => '320 x 50',
		'18' => '336 x 280',
		'19' => '360 x 300',
		'20' => '435 x 300',
		'21' => '468 x 15',
		'22' => '468 x 60',
		'23' => '640 x 165',
		'24' => '640 x 190',
		'25' => '640 x 300',
		'26' => '728 x 15',
		'27' => '728 x 90',
		'28' => '970 x 90',
		'29' => '970 x 250',
		'30' => '240 x 400 - Regional ad sizes',
		'31' => '250 x 360 - Regional ad sizes',
		'32' => '580 x 400 - Regional ad sizes',
		'33' => '750 x 100 - Regional ad sizes',
		'34' => '750 x 200 - Regional ad sizes',
		'35' => '750 x 300 - Regional ad sizes',
		'36' => '980 x 120 - Regional ad sizes',
		'37' => '930 x 180 - Regional ad sizes',
	);

	return $ad_sizes;
}

/**
 *  Returns Ad Width and Height
 *
 * @param $ad_size
 *
 * @return array
 */
function ad_size_value($ad_size = '0') {
	$get_ad_size_array = ad_sizes();

	if ( $get_ad_size_array[$ad_size] != 'Auto' ) {
		$ad_size_parts = explode(' x ', $get_ad_size_array[$ad_size]);
		$ad_size = array(
			'ad_width' => $ad_size_parts[0],
			'ad_height' => $ad_size_parts[1],
		);
		echo 'width:'.$ad_size['ad_width'].'px; height:'.$ad_size['ad_height'].'px;';
	}
}

/**
 * Detect Adblocker Notice
 *
 * @return blocker-notice box
 */
function detect_adblocker_notice() {
?>
	<div class="blocker-notice">
		<i class="fa fa-exclamation"></i>
		<h4><?php echo cyprus_get_settings( 'detect_adblocker_title' ); ?></h4>
		<p><?php echo cyprus_get_settings( 'detect_adblocker_description' ); ?></p>
		<div><a href="" class="refresh-button">Refresh</a></div>
	</div>
<?php
}

function cyprus_featured_layouts() {
	include_once get_parent_theme_file_path( 'includes/class-cyprus-featured-layouts.php' );
	cyprus()->featured_layouts = new Cyprus_Featured_Layouts;
	cyprus()->featured_layouts->render();
}
function cyprus_single_sections() {
	include_once get_parent_theme_file_path( 'includes/class-cyprus-single-sections.php' );
	cyprus()->single_sections = new Cyprus_Single_Sections;
	cyprus()->single_sections->render();
}
