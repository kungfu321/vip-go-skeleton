<?php
/**
 * Additional helper functions that the framework or themes may use.
 * The functions in this file are functions that don't really have a home within any other parts of the framework.
 */

/**
 * Debug function.
 */
if ( ! function_exists( 'dd' ) ) {
	function dd() {
		array_map( function ( $x ) {
			print_r( $x );
			echo '<br>';
		}, func_get_args() );
		die( 1 );
	}
}

function cyprus_get_cat_name( $slug ) {
	$category = get_term_by( 'slug', $slug, 'category' );
	return $category->name;
}

/**
 * Check if the string begins with the given value
 *
 * @param  string   $needle   The sub-string to search for
 * @param  string   $haystack The string to search
 *
 * @return bool
 */
function cyprus_str_start_with( $needle, $haystack ) {
	return substr_compare( $haystack, $needle, 0, strlen( $needle ) ) === 0;
}

/**
 * Check if the string contains the given value
 *
 * @param  string   $needle   The sub-string to search for
 * @param  string   $haystack The string to search
 *
 * @return bool
 */
function cyprus_str_contains( $needle, $haystack ) {
	return strpos( $haystack, $needle ) !== false;
}

/**
 * Function for setting the content width of a theme.
 *
 * @param  int    $width
 * @return void
 */
function cyprus_set_content_width( $width = '' ) {
	$GLOBALS['content_width'] = absint( $width );
}

/**
 * Get settings from theme options
 * @param  string  $id
 * @param  mixed $default
 * @return mixed
 */
function cyprus_get_settings( $id, $default = false ) {
	return cyprus()->settings->get( $id, $default );
}

/**
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @static
 * @access public
 * @since 3.8.0
 * @param string $size The size.
 * @return int
 */
function cyprus_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	// @codingStandardsIgnoreStart
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
	}
	// @codingStandardsIgnoreEnd
	return $ret;
}

/**
 * Instantiates the WordPress filesystem for use with cyprus.
 *
 * @return object
 */
function cyprus_init_filesystem() {

	if ( ! defined( 'FS_METHOD' ) ) {
		define( 'FS_METHOD', 'direct' );
	}

	// The WordPress filesystem.
	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	return $wp_filesystem;
}

/**
 * Increase excerpt length to 100.
 *
 * @param int $length
 *
 * @return int
 */
function cyprus_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'cyprus_excerpt_length', 20 );

/**
 * Remove [...] and shortcodes from excerpts
 *
 * @param string $output
 *
 * @return string
 */
function cyprus_custom_excerpt( $output ) {
	return preg_replace( '/\[[^\]]*]/', '', $output );
}
add_filter( 'get_the_excerpt', 'cyprus_custom_excerpt' );

/**
 * Get HTML-escaped excerpt up to the specified length.
 *
 * @param int $limit
 *
 * @return string
 */
function cyprus_excerpt( $limit = 55 ) {
	return esc_html( cyprus_truncate( get_the_excerpt(), $limit, 'words' ) );
}

/**
 * Truncate string to x letters/words.
 *
 * @param string $str
 * @param int $length
 * @param string $units
 * @param string $ellipsis
 *
 * @return string
 */
function cyprus_truncate( $str, $length = 40, $units = 'letters', $ellipsis = '&nbsp;&hellip;' ) {
	if ( 'letters' === $units ) {
		if ( mb_strlen( $str ) > $length ) {
			return mb_substr( $str, 0, $length ) . $ellipsis;
		} else {
			return $str;
		}
	} else {
		return wp_trim_words( $str, $length, $ellipsis );
	}
}

/**
 * Shorthand function to check for more tag in post.
 *
 * @return bool|int
 */
function cyprus_post_has_moretag() {
	return strpos( get_the_content(), '<!--more-->' );
}

/**
 * Change the HTML markup of the post thumbnail.
 *
 * @param string $html    html structure.
 * @param int    $post_id    post ID.
 * @param string $post_image_id    featured image ID.
 * @param int    $size    image size.
 * @param string $attr    image attributes.
 *
 * @return string
 */
function cyprus_post_image_html( $html, $post_id, $post_image_id, $size, $attr ) {

	// use featured image.
	if ( has_post_thumbnail( $post_id ) || 'shop_thumbnail' === $size ) {
		return $html;
	}

	// use first attached image.
	$images = get_children( 'post_type=attachment&post_mime_type=image&suppress_filters=false&post_parent=' . $post_id );
	if ( ! empty( $images ) ) {
		$image = reset( $images );
		return wp_get_attachment_image( $image->ID, $size, false, $attr );
	}

	// use no preview fallback.
	if ( file_exists( get_template_directory() . '/images/nothumb-' . $size . '.png' ) ) {
		$placeholder = get_template_directory_uri() . '/images/nothumb-' . $size . '.png';
		if ( ! empty( cyprus_get_settings( 'mts_lazy_load' ) ) && ! empty( cyprus_get_settings( 'mts_lazy_load_thumbs' ) ) ) {
			$placeholder_src = '';
			$layzr_attr      = ' data-layzr="' . esc_attr( $placeholder ) . '"';
		} else {
			$placeholder_src = $placeholder;
			$layzr_attr      = '';
		}

		$placeholder_classs = 'attachment-' . $size . ' wp-post-image';
		return '<img src="' . esc_url( $placeholder_src ) . '" class="' . esc_attr( $placeholder_classs ) . '" alt="' . esc_attr( get_the_title() ) . '"' . $layzr_attr . '>';
	}
	return '';
}
add_filter( 'post_thumbnail_html', 'cyprus_post_image_html', 10, 5 );

// NAV ------------------------------------------------------------------!
/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @param  string  $location
 * @return string
 */
function cyprus_get_menu_location_name( $location ) {
	$locations = get_registered_nav_menus();
	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}

/**
 * Function for grabbing a WP nav menu name based on theme location.
 *
 * @param  string  $location
 * @return string
 */
function cyprus_get_menu_name( $location ) {
	$locations = get_nav_menu_locations();
	return isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] )->name : '';
}

// SIDEBAR ------------------------------------------------------------------!
/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @global array   $wp_registered_sidebars
 * @param  string  $sidebar_id
 * @return string
 */
function cyprus_get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;
	return isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : '';
}

// Conditional --------------------------------------------------------------!
/**
 * Check if WooCommerce is active or not
 * @return boolean
 */
function cyprus_is_woocommerce_active() {

	return class_exists( 'WooCommerce' ) ? true : false;
}

/**
 * Check if MyComposer is active or not
 * @return boolean
 */
function cyprus_is_composer() {
	return class_exists( 'MyComposer' );
}

/**
 * Check if ipad
 * @return boolean
 */
function cyprus_is_ipad() {
	$is_ipad = (bool) ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) );

	return $is_ipad;
}

// Data ----------------------------------------------------------------------

/**
 * Get post id for slider.
 * @return boolean|int
 */
function cyprus_get_slider_page_id() {

	if ( is_search() ) {
		return false;
	}

	$object_id = get_queried_object_id();

	if ( ! is_home() && ! is_front_page() && ! is_archive() && isset( $object_id ) ) {
		return $object_id;
	}

	if ( ! is_home() && is_front_page() && isset( $object_id ) ) {
		return $object_id;
	}

	if ( is_home() && ! is_front_page() ) {
		return get_option( 'page_for_posts' );
	}

	if ( class_exists( 'WooCommerce' ) && is_shop() ) {
		return get_option( 'woocommerce_shop_page_id' );
	}

	return false;
}

/**
 * MTS icons for use in nav menus and icon select option.
 *
 * @return array
 */
function cyprus_get_icons() {
	// @codingStandardsIgnoreStart
	$icons = array(
		__( 'Web Application Icons', 'cyprus' ) => array(
			'address-book', 'address-book-o', 'address-card', 'address-card-o', 'adjust', 'american-sign-language-interpreting', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'asl-interpreting', 'assistive-listening-systems', 'asterisk', 'at', 'audio-description', 'automobile', 'balance-scale', 'ban', 'bank', 'bar-chart', 'bar-chart-o', 'barcode', 'bars', 'bath', 'bathtub', 'battery', 'battery-0', 'battery-1', 'battery-2', 'battery-3', 'battery-4', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'blind', 'bluetooth', 'bluetooth-b', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'braille', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'cab', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'close', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'credit-card-alt', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'dashboard', 'database', 'deaf', 'deafness', 'desktop', 'diamond', 'dot-circle-o', 'download', 'drivers-license', 'drivers-license-o', 'edit', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-open', 'envelope-open-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'feed', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-movie-o', 'file-pdf-o', 'file-photo-o', 'file-picture-o', 'file-powerpoint-o', 'file-sound-o', 'file-video-o', 'file-word-o', 'file-zip-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flash', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gear', 'gears', 'gift', 'glass', 'globe', 'graduation-cap', 'group', 'hand-grab-o', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hand-stop-o', 'handshake-o', 'hard-of-hearing', 'hashtag', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hotel', 'hourglass', 'hourglass-1', 'hourglass-2', 'hourglass-3', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'id-badge', 'id-card', 'id-card-o', 'image', 'inbox', 'industry', 'info', 'info-circle', 'institution', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'legal', 'lemon-o', 'level-down', 'level-up', 'life-bouy', 'life-buoy', 'life-ring', 'life-saver', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'low-vision', 'magic', 'magnet', 'mail-forward', 'mail-reply', 'mail-reply-all', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microchip', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'mobile-phone', 'money', 'moon-o', 'mortar-board', 'motorcycle', 'mouse-pointer', 'music', 'navicon', 'newspaper-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'percent', 'phone', 'phone-square', 'photo', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'podcast', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'question-circle-o', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'remove', 'reorder', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 's15', 'search', 'search-minus', 'search-plus', 'send', 'send-o', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'shower', 'sign-in', 'sign-language', 'sign-out', 'signal', 'signing', 'sitemap', 'sliders', 'smile-o', 'snowflake-o', 'soccer-ball-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-down', 'sort-numeric-asc', 'sort-numeric-desc', 'sort-up', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-empty', 'star-half-full', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'support', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thermometer', 'thermometer-0', 'thermometer-1', 'thermometer-2', 'thermometer-3', 'thermometer-4', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'times-rectangle', 'times-rectangle-o', 'tint', 'toggle-down', 'toggle-left', 'toggle-off', 'toggle-on', 'toggle-right', 'toggle-up', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'tv', 'umbrella', 'universal-access', 'university', 'unlock', 'unlock-alt', 'unsorted', 'upload', 'user', 'user-circle', 'user-circle-o', 'user-o', 'user-plus', 'user-secret', 'user-times', 'users', 'vcard', 'vcard-o', 'video-camera', 'volume-control-phone', 'volume-down', 'volume-off', 'volume-up', 'warning', 'wheelchair', 'wheelchair-alt', 'wifi', 'window-close', 'window-close-o', 'window-maximize', 'window-minimize', 'window-restore', 'wrench'
		),
		__( 'Accessibility Icons', 'cyprus' ) => array(
			'american-sign-language-interpreting', 'asl-interpreting', 'assistive-listening-systems', 'audio-description', 'blind', 'braille', 'cc', 'deaf', 'deafness', 'hard-of-hearing', 'low-vision', 'question-circle-o', 'sign-language', 'signing', 'tty', 'universal-access', 'volume-control-phone', 'wheelchair', 'wheelchair-alt'
		),
		__( 'Hand Icons', 'cyprus' ) => array(
			'hand-grab-o', 'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hand-stop-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up'
		),
		__( 'Transportation Icons', 'cyprus' ) => array(
			'ambulance', 'automobile', 'bicycle', 'bus', 'cab', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair', 'wheelchair-alt'
		),
		__( 'Gender Icons', 'cyprus' ) => array(
			'genderless', 'intersex', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars'
		),
		__( 'File Type Icons', 'cyprus' ) => array(
			'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-movie-o', 'file-o', 'file-pdf-o', 'file-photo-o', 'file-picture-o', 'file-powerpoint-o', 'file-sound-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o', 'file-zip-o'
		),
		__( 'Spinner Icons', 'cyprus' ) => array(
			'circle-o-notch', 'cog', 'gear', 'refresh', 'spinner'
		),
		__( 'Form Control Icons', 'cyprus' ) => array(
			'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o'
		),
		__( 'Payment Icons', 'cyprus' ) => array(
			'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'credit-card-alt', 'google-wallet', 'paypal'
		),
		__( 'Chart Icons', 'cyprus' ) => array(
			'area-chart', 'bar-chart', 'bar-chart-o', 'line-chart', 'pie-chart'
		),
		__( 'Currency Icons', 'cyprus' ) => array(
			'bitcoin', 'btc', 'cny', 'dollar', 'eur', 'euro', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rmb', 'rouble', 'rub', 'ruble', 'rupee', 'shekel', 'sheqel', 'try', 'turkish-lira', 'usd', 'won', 'yen'
		),
		__( 'Text Editor Icons', 'cyprus' ) => array(
			'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain', 'chain-broken', 'clipboard', 'columns', 'copy', 'cut', 'dedent', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'paste', 'repeat', 'rotate-left', 'rotate-right', 'save', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo', 'unlink'
		),
		__( 'Directional Icons', 'cyprus' ) => array(
			'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up', 'toggle-down', 'toggle-left', 'toggle-right', 'toggle-up'
		),
		__( 'Video Player Icons', 'cyprus' ) => array(
			'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'pause-circle', 'pause-circle-o', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'stop-circle', 'stop-circle-o', 'youtube-play'
		),
		__( 'Brand Icons', 'cyprus' ) => array(
			'500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'bandcamp', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'bitcoin', 'black-tie', 'bluetooth', 'bluetooth-b', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'codiepie', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'edge', 'eercast', 'empire', 'envira', 'etsy', 'expeditedssl', 'fa', 'facebook', 'facebook-f', 'facebook-official', 'facebook-square', 'firefox', 'first-order', 'flickr', 'font-awesome', 'fonticons', 'fort-awesome', 'forumbee', 'foursquare', 'free-code-camp', 'ge', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'gitlab', 'gittip', 'glide', 'glide-g', 'google', 'google-plus', 'google-plus-circle', 'google-plus-official', 'google-plus-square', 'google-wallet', 'gratipay', 'grav', 'hacker-news', 'houzz', 'html5', 'imdb', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linode', 'linux', 'maxcdn', 'meanpath', 'medium', 'meetup', 'mixcloud', 'modx', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pied-piper-pp', 'pinterest', 'pinterest-p', 'pinterest-square', 'product-hunt', 'qq', 'quora', 'ra', 'ravelry', 'rebel', 'reddit', 'reddit-alien', 'reddit-square', 'renren', 'resistance', 'safari', 'scribd', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'superpowers', 'telegram', 'tencent-weibo', 'themeisle', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'usb', 'viacoin', 'viadeo', 'viadeo-square', 'vimeo', 'vimeo-square', 'vine', 'vk', 'wechat', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'wpbeginner', 'wpexplorer', 'wpforms', 'xing', 'xing-square', 'y-combinator', 'y-combinator-square', 'yahoo', 'yc', 'yc-square', 'yelp', 'yoast', 'youtube', 'youtube-play', 'youtube-square'
		),
		__( 'Medical Icons', 'cyprus' ) => array(
			'ambulance', 'h-square', 'heart', 'heart-o', 'heartbeat', 'hospital-o', 'medkit', 'plus-square', 'stethoscope', 'user-md', 'wheelchair', 'wheelchair-alt'
		)
	);
	// @codingStandardsIgnoreEnd

	return $icons;
}

/**
 * Wrap videos in .responsive-video div
 *
 * @param $html
 * @param $url
 * @param $attr
 *
 * @return string
 */
function cyprus_responsive_video( $html, $url, $attr ) {

	// Only video embeds.
	$video_providers = array(
		'youtube',
		'vimeo',
		'dailymotion',
		'wordpress.tv',
		'vine.co',
		'animoto',
		'blip.tv',
		'collegehumor.com',
		'funnyordie.com',
		'hulu.com',
		'revision3.com',
		'ted.com',
	);

	// Allow user to wrap other embeds.
	$providers = apply_filters( 'cyprus_responsive_video', $video_providers );
	foreach ( $providers as $provider ) {
		if ( strstr( $url, $provider ) ) {
			$html = '<div class="flex-video flex-video-' . sanitize_html_class( $provider ) . '">' . $html . '</div>';
			break; // Break if video found.
		}
	}

	return $html;
}
add_filter( 'embed_oembed_html', 'cyprus_responsive_video', 99, 3 );

// Comments ------------------------------------------------------------------!
/**
 * Exclude trackbacks from the comment count.
 *
 * @param $count
 *
 * @return int
 */
function cyprus_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments         = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
add_filter( 'get_comments_number', 'cyprus_comment_count', 0 );

/**
 * Add `has_thumb` to the post's class name if it has a thumbnail.
 *
 * @param $classes
 *
 * @return array
 */
function cyprus_post_has_thumb_class( $classes ) {
	if ( has_post_thumbnail( get_the_ID() ) ) {
		$classes[] = 'has_thumb';
	}
		return $classes;
}
add_filter( 'post_class', 'cyprus_post_has_thumb_class' );

/**
 * Custom `<article>` class name.
 */
if ( ! function_exists( 'cyprus_article_class' ) ) {
	function cyprus_get_article_class() {
		$classes[] = 'article';

		// sidebar or full width.
		if ( 'mts_nosidebar' === mts_custom_sidebar() ) {
			$classes[] = 'ss-full-width';
		}

		return $classes;
	}

	function cyprus_article_class() {
		$classes = cyprus_get_article_class();
		echo esc_html( join( ' ', $classes ) );
	}
}

/**
 * Custom `#page` class name.
 */
if ( ! function_exists( 'cyprus_single_page_class' ) ) {
	function cyprus_single_page_class() {
		$class = '';

		if ( is_single() || is_page() || function_exists( 'is_woocommerce' ) && is_woocommerce() ) {

			$class = 'single';

			if ( is_page() ) {
				$class .= ' single_page';
			}

			if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
				$class .= ' wc_page';
			}

			$header_animation = cyprus_get_post_header_effect();
			if ( ! empty( $header_animation ) ) {
				$class .= ' ' . $header_animation;
			}
		}

		echo esc_attr( $class );
	}
}

/**
 * Single Post Pagination - Numbers + Previous/Next.
 * @param $args
 * @return mixed
 */
function cyprus_wp_link_pages_args( $args ) {
	global $page, $numpages, $more, $pagenow;
	if ( 'next_and_number' != $args['next_or_number'] ) {
		return $args;
	}

	$args['next_or_number'] = 'number';

	if ( ! $more ) {
		return $args;
	}

	if ( $page - 1 ) {
		$args['before'] .= _wp_link_page( $page - 1 ) . $args['link_before'] . $args['previouspagelink'] . $args['link_after'] . '</a>';
	}

	if ( $page < $numpages ) {
		$args['after'] = _wp_link_page( $page + 1 ) . $args['link_before'] . $args['nextpagelink'] . $args['link_after'] . '</a>' . $args['after'];
	}

	return $args;
}
add_filter( 'wp_link_pages_args', 'cyprus_wp_link_pages_args' );

if ( ! function_exists( 'cyprus_get_post_header_effect' ) ) {
	/**
	 * Get Post header animation.
	 *
	 * @return string
	 */
	function cyprus_get_post_header_effect() {
		$postheader_effect = get_post_meta( get_the_ID(), '_mts_postheader', true );

		return $postheader_effect;
	}
}

/**
 * Add Custom Gravatar Support.
 *
 * @param string $avatar_defaults avatar URL.
 *
 * @return mixed
 */
function cyprus_custom_gravatar( $avatar_defaults ) {
	$cyprus_avatar                     = get_template_directory_uri() . '/images/gravatar.png';
	$avatar_defaults[ $cyprus_avatar ] = __( 'Custom Gravatar ( /images/gravatar.png )', 'cyprus' );
	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'cyprus_custom_gravatar' );

/**
 * Add `#primary-navigation` the WP Mega Menu's
 *
 * @param string $selector    container class.
 * @return string
 */
function cyprus_megamenu_parent_element( $selector ) {
	return '.navigation';
}
add_filter( 'wpmm_container_selector', 'cyprus_megamenu_parent_element' );

/**
 * Change the image size of WP Mega Menu's thumbnails.
 *
 * @param string $thumbnail_html thumbnail.
 * @param int    $post_id post ID.
 *
 * @return string
 */
function cyprus_megamenu_thumbnails( $thumbnail_html, $post_id ) {
	$thumbnail_html  = '<div class="wpmm-thumbnail">';
	$thumbnail_html .= '<a title="' . get_the_title( $post_id ) . '" href="' . get_permalink( $post_id ) . '">';
	if ( has_post_thumbnail( $post_id ) ) :
		$thumbnail_html .= get_the_post_thumbnail( $post_id, 'cyprus-widgetfull', array( 'title' => '' ) );
	else :
		$thumbnail_html .= '<img src="' . get_template_directory_uri() . '/images/nothumb-cyprus-widgetfull.png" alt="' . __( 'No Preview', 'cyprus' ) . '"  class="wp-post-image" />';
	endif;
	$thumbnail_html .= '</a>';

	// WP Review.
	$thumbnail_html .= ( function_exists( 'wp_review_show_total' ) ? wp_review_show_total( false ) : '' );
	$thumbnail_html .= '</div>';

	return $thumbnail_html;
}
add_filter( 'wpmm_thumbnail_html', 'cyprus_megamenu_thumbnails', 10, 2 );

/**
 * Generate html attribute string for array.
 *
 * @param  array  $attributes Contains key/value pair to generate a string.
 * @param  string $prefix     If you want to append a prefic before every key.
 * @return string
 */
function cyprus_generate_attributes( $attributes = array(), $prefix = '' ) {

	// If empty return false.
	if ( empty( $attributes ) ) {
		return false;
	}

	$out = '';
	foreach ( $attributes as $key => $value ) {
		if ( true === $value ) {
			$value = 'true';
		}
		if ( false === $value ) {
			$value = 'false';
		}

		$out .= sprintf( ' %s="%s"', esc_html( $prefix . $key ), esc_attr( $value ) );
	}

	return $out;
}

/**
 * Remove 'bypostauthor' class from comment.
 *
 * @param  string $classes default classes.
 * @return string          updated classes.
 */
function cyprus_filter_comment_class( $classes ) {

	if ( cyprus_get_settings( 'mts_author_comment' ) ) {
		return $classes;
	}

	if ( is_array( $classes ) ) {
		if ( count( $classes ) >= 1 ) {
			foreach ( $classes as $key => $class ) {
				if ( 'bypostauthor' === $class ) {
					unset( $classes[ $key ] );
				}
			}
		}
	}
	return $classes;
}
add_filter( 'comment_class', 'cyprus_filter_comment_class' );

/**
 * Get array of sidebar ids to exclude in options
 *
 * @return array
 */
function cyprus_excluded_sidebars() {
	$ids = array( 'sidebar', 'footer-top-1', 'footer-top-2', 'footer-top-3', 'footer-top-4', 'widget-header', 'shop-sidebar', 'product-sidebar' );

	return cyprus_filter( 'exclude_sidebars', $ids );
}

function validate_featured_categories( $field, $values ) {
	foreach ( $values as &$value ) {
		if ( ! isset( $value['unique_id'] ) || empty( $value['unique_id'] ) ) {
			$value['unique_id'] = uniqid();
		}
	}
	return array( 'value' => $values );
}

/**
 * Exclude sticky posts from home page.
 *
 * @param WP_Query $query main query.
 */
function cyprus_ignore_sticky_posts( $query ) {
	if ( is_home() && $query->is_main_query() ) {
		$query->set( 'post__not_in', get_option( 'sticky_posts' ) );
	}
}
add_action( 'pre_get_posts', 'cyprus_ignore_sticky_posts' );
/**
 * Handle AJAX search queries.
 */
function ajax_cyprus_search() {
	$query        = $_REQUEST['q']; // It goes through esc_sql() in WP_Query.
	$search_query = new WP_Query( array( 's' => $query, 'posts_per_page' => 3, 'post_status' => 'publish' ) ); // @codingStandardsIgnoreLine
	$search_count = new WP_Query( array( 's' => $query, 'posts_per_page' => -1, 'post_status' => 'publish' ) ); // @codingStandardsIgnoreLine
	$search_count = $search_count->post_count;
	if ( ! empty( $query ) && $search_query->have_posts() ) :
		echo '<ul class="ajax-search-results">';
		while ( $search_query->have_posts() ) :
			$search_query->the_post();
		?>
			<li>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php the_post_thumbnail( 'cyprus-widgetthumb', array( 'title' => '' ) ); ?>
					<?php the_title(); ?>
				</a>
				<div class="meta">
					<span class="thetime"><?php the_time( 'F j, Y' ); ?></span>
				</div> <!-- / .meta -->
			</li>
			<?php
		endwhile;
		echo '</ul>';
		echo '<div class="ajax-search-meta"><span class="results-count">' . $search_count . ' ' . __( 'Results', 'cyprus' ) . '</span><a href="' . esc_url( get_search_link( $query ) ) . '" class="results-link">' . __( 'Show all results.', 'cyprus' ) . '</a></div>';
	else :
		echo '<div class="no-results">' . __( 'No results found.', 'cyprus' ) . '</div>';
	endif;
	wp_reset_postdata();
	exit; // required for AJAX in WP.
}

add_action( 'wp_ajax_cyprus_search', 'ajax_cyprus_search' );
add_action( 'wp_ajax_nopriv_cyprus_search', 'ajax_cyprus_search' );
