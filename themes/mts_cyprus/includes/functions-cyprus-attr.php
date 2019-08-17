<?php
/**
 * HTML attribute functions and filters.  The purposes of this is to provide a way for theme/plugin devs
 * to hook into the attributes for specific HTML elements and create new or modify existing attributes.
 *
 * @package Cyprus
 */

/**
 * HOOKS ------------------------------------------------------------
 */

// Attributes for major structural elements.
add_filter( 'cyprus_attr_html', 'cyprus_attr_html', 5 );
add_filter( 'cyprus_attr_head', 'cyprus_attr_head', 5 );
add_filter( 'cyprus_attr_body', 'cyprus_attr_body', 5 );
add_filter( 'cyprus_attr_main', 'cyprus_attr_main', 5 );
add_filter( 'cyprus_attr_header', 'cyprus_attr_header', 5 );
add_filter( 'cyprus_attr_footer', 'cyprus_attr_footer', 5 );
add_filter( 'cyprus_attr_content', 'cyprus_attr_content', 5 );
add_filter( 'cyprus_attr_sidebar', 'cyprus_attr_sidebar', 5, 2 );
add_filter( 'cyprus_attr_menu', 'cyprus_attr_menu', 5, 2 );

// Header attributes.
add_filter( 'cyprus_attr_branding', 'cyprus_attr_branding', 5 );
add_filter( 'cyprus_attr_site-title', 'cyprus_attr_site_title', 5 );
add_filter( 'cyprus_attr_site-description', 'cyprus_attr_site_description', 5 );

// Archive page header attributes.
add_filter( 'cyprus_attr_archive-header', 'cyprus_attr_archive_header', 5 );
add_filter( 'cyprus_attr_archive-title', 'cyprus_attr_archive_title', 5 );
add_filter( 'cyprus_attr_archive-description', 'cyprus_attr_archive_description', 5 );

// Post-specific attributes.
add_filter( 'cyprus_attr_post', 'cyprus_attr_post', 5 );
add_filter( 'cyprus_attr_entry', 'cyprus_attr_post', 5 ); // Alternate for "post".
add_filter( 'cyprus_attr_entry-title', 'cyprus_attr_entry_title', 5 );
add_filter( 'cyprus_attr_entry-author', 'cyprus_attr_entry_author', 5 );
add_filter( 'cyprus_attr_entry-published', 'cyprus_attr_entry_published', 5 );
add_filter( 'cyprus_attr_entry-content', 'cyprus_attr_entry_content', 5 );
add_filter( 'cyprus_attr_entry-summary', 'cyprus_attr_entry_summary', 5 );
add_filter( 'cyprus_attr_entry-terms', 'cyprus_attr_entry_terms', 5, 2 );

// Comment specific attributes.
add_filter( 'cyprus_attr_comment', 'cyprus_attr_comment', 5 );
add_filter( 'cyprus_attr_comment-author', 'cyprus_attr_comment_author', 5 );
add_filter( 'cyprus_attr_comment-published', 'cyprus_attr_comment_published', 5 );
add_filter( 'cyprus_attr_comment-permalink', 'cyprus_attr_comment_permalink', 5 );
add_filter( 'cyprus_attr_comment-content', 'cyprus_attr_comment_content', 5 );

/**
 * FUNCTIONS ------------------------------------------------------------
 */

/**
 * Outputs an HTML element's attributes.
 *
 * @param  string $slug    The slug/ID of the element (e.g., 'sidebar').
 * @param  array  $attr    Array of attributes to pass in (overwrites filters).
 * @param  string $context A specific context (e.g., 'primary').
 * @return void
 */
function cyprus_attr( $slug, $attr = array(), $context = '' ) {
	echo cyprus_get_attr( $slug, $context, $attr );
}

/**
 * Gets an HTML element's attributes.  This function is actually meant to be filtered by theme authors, plugins,
 * or advanced child theme users.  The purpose is to allow folks to modify, remove, or add any attributes they
 * want without having to edit every template file in the theme.  So, one could support microformats instead
 * of microdata, if desired.
 *
 * @param  string $slug    The slug/ID of the element (e.g., 'sidebar').
 * @param  array  $attr    Array of attributes to pass in (overwrites filters).
 * @param  string $context A specific context (e.g., 'primary').
 * @return string
 */
function cyprus_get_attr( $slug, $attr = array(), $context = '' ) {

	$out  = '';
	$attr = wp_parse_args( $attr, cyprus_filter( "attr_{$slug}", array(), $context ) );

	if ( empty( $attr ) ) {
		$attr['class'] = $slug;
	}

	foreach ( $attr as $name => $value ) {
		if ( 'null' === $name ) {
			$out .= ' ' . $value;
		} else {
			$out .= $value ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
		}
	}

	return $out;
}

/* === Structural === */

/**
 * <html> attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_html( $attr ) {

	$attr['class'] = 'no-js';
	$attr['null']  = get_language_attributes( 'html' );

	return $attr;
}

/**
 * <head> attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_head( $attr ) {

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebSite';

	return $attr;
}

/**
 * <body> element attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_body( $attr ) {

	$attr['id']    = 'blog';
	$attr['class'] = join( ' ', get_body_class() ) . ' main';
	$attr['dir']   = is_rtl() ? 'rtl' : 'ltr';

	if ( ! defined( 'RANK_MATH_FILE' ) && ! defined( 'RANKMATH_SCHEMA_FILE' ) && ! defined( 'WPSEO_FILE' ) ) {
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WebPage';

		if ( is_singular( 'post' ) || is_home() || is_archive() ) {
			$attr['itemtype'] = 'http://schema.org/Blog';
		} elseif ( is_search() ) {
			$attr['itemtype'] = 'http://schema.org/SearchResultsPage';
		}
	}

	return $attr;
}

/**
 * Page <main> container attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_main( $attr ) {
	$adclass = '';
	if ( cyprus_get_settings( 'detect_adblocker' ) ) {
		$adclass = ' blocker-enabled-check ' . cyprus_get_settings( 'detect_adblocker_type' );
	}
	$attr['class'] = 'main-container' . $adclass;

	return $attr;
}

/**
 * Page <header> element attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_header( $attr ) {

	$attr['id']        = 'header';
	$attr['class']     = 'site-header wb-header';
	$attr['role']      = 'banner';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPHeader';

	return $attr;
}

/**
 * Page <footer> element attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_footer( $attr ) {

	$attr['id']        = 'site-footer';
	$attr['class']     = 'site-footer';
	$attr['role']      = 'contentinfo';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPFooter';

	return $attr;
}

/**
 * Main content container of the page attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_content( $attr ) {

	$attr['id']    = 'content';
	$attr['class'] = 'content';
	$attr['role']  = 'main';

	if ( ! is_singular( 'post' ) && ! is_home() && ! is_archive() ) {
		$attr['itemprop'] = 'mainContentOfPage';
	}

	return $attr;
}

/**
 * Sidebar attributes.
 *
 * @param  array  $attr    Array of attributes to pass in (overwrites filters).
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function cyprus_attr_sidebar( $attr, $context ) {

	$attr['class'] = 'sidebar';
	$attr['role']  = 'complementary';

	if ( $context ) {

		$attr['class'] .= " sidebar-{$context}";
		$attr['id']     = "sidebar-{$context}";

		$sidebar_name = cyprus_get_sidebar_name( $context );

		if ( $sidebar_name ) {
			// Translators: The %s is the sidebar name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Sidebar', 'sidebar aria label', 'cyprus' ), $sidebar_name ) );
		}
	}

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPSideBar';

	return $attr;
}

/**
 * Nav menu attributes.
 *
 * @param  array  $attr    Array of attributes to pass in (overwrites filters).
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function cyprus_attr_menu( $attr, $context ) {

	$attr['class'] = 'menu';
	$attr['role']  = 'navigation';

	if ( $context ) {

		$attr['class'] .= " menu-{$context}";
		$attr['id']     = "menu-{$context}";

		$menu_name = cyprus_get_menu_location_name( $context );

		if ( $menu_name ) {
			// Translators: The %s is the menu name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Menu', 'nav menu aria label', 'cyprus' ), $menu_name ) );
		}
	}

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/SiteNavigationElement';

	return $attr;
}

/* === header === */

/**
 * Branding (usually a wrapper for title and tagline) attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_branding( $attr ) {

	$attr['id']    = 'branding';
	$attr['class'] = 'site-branding';

	return $attr;
}

/**
 * Site title attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_site_title( $attr ) {

	$attr['id']       = 'site-title';
	$attr['class']    = 'site-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Site description attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_site_description( $attr ) {

	$attr['id']       = 'site-description';
	$attr['class']    = 'site-description';
	$attr['itemprop'] = 'description';

	return $attr;
}

/* === loop === */

/**
 * Archive header attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_archive_header( $attr ) {

	$attr['class']     = 'archive-header';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPageElement';

	return $attr;
}

/**
 * Archive title attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_archive_title( $attr ) {

	$attr['class']    = 'archive-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Archive description attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_archive_description( $attr ) {

	$attr['class']    = 'archive-description';
	$attr['itemprop'] = 'text';

	return $attr;
}

/* === posts === */

/**
 * Post <article> element attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_post( $attr ) {

	$post = get_post();

	// Make sure we have a real post first.
	if ( ! empty( $post ) ) {

		$attr['id']        = 'post-' . get_the_ID();
		$attr['class']     = join( ' ', get_post_class() );
		$attr['itemscope'] = 'itemscope';

		if ( 'post' === get_post_type() ) {

			if ( ! defined( 'RANK_MATH_FILE' ) && ! defined( 'RANKMATH_SCHEMA_FILE' ) && ! defined( 'WPSEO_FILE' ) ) {
				$attr['itemtype'] = 'http://schema.org/BlogPosting';
			}

			/* Add itemprop if within the main query. */
			if ( is_main_query() && ! is_search() ) {
				$attr['itemprop'] = 'blogPost';
			}
		} elseif ( 'attachment' === get_post_type() && wp_attachment_is( 'image' ) ) {
			$attr['itemtype'] = 'http://schema.org/ImageObject';
		} elseif ( 'attachment' === get_post_type() && wp_attachment_is( 'audio' ) ) {
			$attr['itemtype'] = 'http://schema.org/AudioObject';
		} elseif ( 'attachment' === get_post_type() && wp_attachment_is( 'video' ) ) {
			$attr['itemtype'] = 'http://schema.org/VideoObject';
		} else {
			$attr['itemtype'] = 'http://schema.org/CreativeWork';
		}
	} else {

		$attr['id']    = 'post-0';
		$attr['class'] = join( ' ', get_post_class() );
	}

	return $attr;
}

/**
 * Post title attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_entry_title( $attr ) {

	$attr['class']    = 'entry-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Post author attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_entry_author( $attr ) {

	$attr['class']     = 'entry-author';
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Post time/published attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_entry_published( $attr ) {

	$attr['class']    = 'entry-published updated';
	$attr['datetime'] = get_the_time( 'Y-m-d\TH:i:sP' );
	$attr['itemprop'] = 'datePublished';

	// Translators: Post date/time "title" attribute.
	$attr['title'] = get_the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'cyprus' ) );

	return $attr;
}

/**
 * Post content (not excerpt) attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_entry_content( $attr ) {

	$attr['class']    = 'entry-content';
	$attr['itemprop'] = ( 'post' === get_post_type() ) ? 'articleBody' : 'text';

	return $attr;
}

/**
 * Post summary/excerpt attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_entry_summary( $attr ) {

	$attr['class']    = 'entry-summary';
	$attr['itemprop'] = 'description';

	return $attr;
}

/**
 * Post terms (tags, categories, etc.) attributes.
 *
 * @param  array  $attr    Array of attributes to pass in (overwrites filters).
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function cyprus_attr_entry_terms( $attr, $context ) {

	if ( ! empty( $context ) ) {

		$attr['class'] = 'entry-terms ' . sanitize_html_class( $context );

		if ( 'category' === $context ) {
			$attr['itemprop'] = 'articleSection';
		} elseif ( 'post_tag' === $context ) {
			$attr['itemprop'] = 'keywords';
		}
	}

	return $attr;
}


/* === Comment elements === */


/**
 * Comment wrapper attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_comment( $attr ) {

	$attr['id']    = 'comment-' . get_comment_ID();
	$attr['class'] = join( ' ', get_comment_class() );

	if ( in_array( get_comment_type(), array( '', 'comment' ) ) ) {

		$attr['itemprop']  = 'comment';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Comment';
	}

	return $attr;
}

/**
 * Comment author attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_comment_author( $attr ) {

	$attr['class']     = 'comment-author';
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Comment time/published attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_comment_published( $attr ) {

	$attr['class']    = 'comment-published';
	$attr['datetime'] = get_comment_time( 'Y-m-d\TH:i:sP' );

	// Translators: Comment date/time "title" attribute.
	$attr['title']    = get_comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'cyprus' ) );
	$attr['itemprop'] = 'datePublished';

	return $attr;
}

/**
 * Comment permalink attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_comment_permalink( $attr ) {

	$attr['class']    = 'comment-permalink';
	$attr['href']     = get_comment_link();
	$attr['itemprop'] = 'url';

	return $attr;
}

/**
 * Comment content/text attributes.
 *
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return array
 */
function cyprus_attr_comment_content( $attr ) {

	$attr['class']    = 'comment-content';
	$attr['itemprop'] = 'text';

	return $attr;
}
