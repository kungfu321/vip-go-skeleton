<?php
/*-----------------------------------------------------------------------------------*/
/*	Do not remove these lines, sky will fall on your head.
/*-----------------------------------------------------------------------------------*/
define( 'MTS_THEME_NAME', 'crypto' );
define( 'MTS_THEME_VERSION', '1.1.4' );

require_once( get_theme_file_path( 'theme-options.php' ) );
if ( ! isset( $content_width ) ) {
	$content_width = 730; //article content width without padding
}

/*-----------------------------------------------------------------------------------*/
/*	Load Options
/*-----------------------------------------------------------------------------------*/
$mts_options = get_option( MTS_THEME_NAME );

/**
 * Register supported theme features, image sizes and nav menus.
 * Also loads translated strings.
 */
function mts_after_setup_theme() {
	if ( ! defined( 'MTS_THEME_WHITE_LABEL' ) ) {
		define( 'MTS_THEME_WHITE_LABEL', false );
	}

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );

	load_theme_textdomain( 'crypto', get_template_directory().'/lang' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 374, 264, true );
	add_image_size( 'crypto-featured', 374, 264, true ); // 2 Col Grid
	add_image_size( 'crypto-featuredlist', 236, 157, true ); // List view
	add_image_size( 'crypto-slider', 740, 492, true ); // Featured Slider
	add_image_size( 'crypto-sliderfull', 1120, 600, true ); // Featured Sliderfull
	add_image_size( 'crypto-related', 355, 200, true ); // Related Posts
	add_image_size( 'crypto-smallthumb', 80, 80, true ); // small thumb grid
	add_image_size( 'crypto-widgetthumb', 60, 60, true ); // Widget Small thumbs & Archive ICOs thumbnail
	add_image_size( 'crypto-widgetfull', 355, 200, true ); // Widget Full Width thumbs
	add_image_size( 'crypto-ico-single-thumbnail', 200, 200, true ); // Single ICOs thumbnail & Single ICOs Profile thumb

	register_nav_menus( array(
	  'primary' => __( 'Top Menu', 'crypto' ),
	  'secondary' => __( 'Main Menu', 'crypto' ),
	  'mobile' => __( 'Mobile', 'crypto' )
	) );

	add_action( 'init', 'crypto_wp_review_thumb_size', 11 );

   	function crypto_wp_review_thumb_size() {
	   add_image_size( 'wp_review_small', 60, 60, true );
	   add_image_size( 'wp_review_large', 355, 200, true );
   	}

	if ( mts_is_wc_active() ) {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action('after_setup_theme', 'mts_after_setup_theme' );

/*-----------------------------------------------------------------------------------*/
/*  Create Custom Post Types
/*-----------------------------------------------------------------------------------*/
function mts_posttype_register() {
	$mts_options = get_option( MTS_THEME_NAME );

    $rewrite_ico_slug = !empty($mts_options['mts_single_ico_slug']) ? $mts_options['mts_single_ico_slug'] : 'icos';
    //icos Post type
    $args = array(
        'label' => __('ICOs', 'crypto'),
        'singular_label' => __('ICO', 'crypto'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => false,
        'publicly_queryable' => true,
        'query_var' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-id',
        'has_archive' => true,
        'exclude_from_search' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'rewrite' => array("slug" => $rewrite_ico_slug), // Permalinks format
    );

    register_post_type( 'icos' , $args );

}

add_action('init', 'mts_posttype_register');

function mts_rewrite_flush() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'mts_rewrite_flush' );

// Disable auto-updating the theme.
function mts_disable_auto_update_theme( $update, $item ) {
	if ( $item->slug == MTS_THEME_NAME ) {
		return false;
	}
	return $update;
}
add_filter( 'auto_update_theme', 'mts_disable_auto_update_theme', 10, 2 );

/**
 * Disable Google Typography plugin
 */
function mts_deactivate_google_typography_plugin() {
	if ( in_array( 'google-typography/google-typography.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		deactivate_plugins( 'google-typography/google-typography.php' );
	}
}
add_action( 'admin_init', 'mts_deactivate_google_typography_plugin' );

/**
 * Determines whether the WooCommerce plugin is active or not.
 * @return bool
 */
function mts_is_wc_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * MTS icons for use in nav menus and icon select option.
 *
 * @return array
 */
function mts_get_icons() {
	$icons = array(
		__( 'Web Application Icons', 'crypto' ) => array(
			'address-book', 'address-book-o', 'address-card', 'address-card-o', 'adjust', 'american-sign-language-interpreting', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'asl-interpreting', 'assistive-listening-systems', 'asterisk', 'at', 'audio-description', 'automobile', 'balance-scale', 'ban', 'bank', 'bar-chart', 'bar-chart-o', 'barcode', 'bars', 'bath', 'bathtub', 'battery', 'battery-0', 'battery-1', 'battery-2', 'battery-3', 'battery-4', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'blind', 'bluetooth', 'bluetooth-b', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'braille', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'cab', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'close', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'credit-card-alt', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'dashboard', 'database', 'deaf', 'deafness', 'desktop', 'diamond', 'dot-circle-o', 'download', 'drivers-license', 'drivers-license-o', 'edit', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-open', 'envelope-open-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'feed', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-movie-o', 'file-pdf-o', 'file-photo-o', 'file-picture-o', 'file-powerpoint-o', 'file-sound-o', 'file-video-o', 'file-word-o', 'file-zip-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flash', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gear', 'gears', 'gift', 'glass', 'globe', 'graduation-cap', 'group', 'hand-grab-o', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hand-stop-o', 'handshake-o', 'hard-of-hearing', 'hashtag', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hotel', 'hourglass', 'hourglass-1', 'hourglass-2', 'hourglass-3', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'id-badge', 'id-card', 'id-card-o', 'image', 'inbox', 'industry', 'info', 'info-circle', 'institution', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'legal', 'lemon-o', 'level-down', 'level-up', 'life-bouy', 'life-buoy', 'life-ring', 'life-saver', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'low-vision', 'magic', 'magnet', 'mail-forward', 'mail-reply', 'mail-reply-all', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microchip', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'mobile-phone', 'money', 'moon-o', 'mortar-board', 'motorcycle', 'mouse-pointer', 'music', 'navicon', 'newspaper-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'percent', 'phone', 'phone-square', 'photo', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'podcast', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'question-circle-o', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'remove', 'reorder', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 's15', 'search', 'search-minus', 'search-plus', 'send', 'send-o', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'shower', 'sign-in', 'sign-language', 'sign-out', 'signal', 'signing', 'sitemap', 'sliders', 'smile-o', 'snowflake-o', 'soccer-ball-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-down', 'sort-numeric-asc', 'sort-numeric-desc', 'sort-up', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-empty', 'star-half-full', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'support', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thermometer', 'thermometer-0', 'thermometer-1', 'thermometer-2', 'thermometer-3', 'thermometer-4', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'times-rectangle', 'times-rectangle-o', 'tint', 'toggle-down', 'toggle-left', 'toggle-off', 'toggle-on', 'toggle-right', 'toggle-up', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'tv', 'umbrella', 'universal-access', 'university', 'unlock', 'unlock-alt', 'unsorted', 'upload', 'user', 'user-circle', 'user-circle-o', 'user-o', 'user-plus', 'user-secret', 'user-times', 'users', 'vcard', 'vcard-o', 'video-camera', 'volume-control-phone', 'volume-down', 'volume-off', 'volume-up', 'warning', 'wheelchair', 'wheelchair-alt', 'wifi', 'window-close', 'window-close-o', 'window-maximize', 'window-minimize', 'window-restore', 'wrench'
		),
		__( 'Accessibility Icons', 'crypto' ) => array(
			'american-sign-language-interpreting', 'asl-interpreting', 'assistive-listening-systems', 'audio-description', 'blind', 'braille', 'cc', 'deaf', 'deafness', 'hard-of-hearing', 'low-vision', 'question-circle-o', 'sign-language', 'signing', 'tty', 'universal-access', 'volume-control-phone', 'wheelchair', 'wheelchair-alt'
		),
		__( 'Hand Icons', 'crypto' ) => array(
			'hand-grab-o', 'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hand-stop-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up'
		),
		__( 'Transportation Icons', 'crypto' ) => array(
			'ambulance', 'automobile', 'bicycle', 'bus', 'cab', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair', 'wheelchair-alt'
		),
		__( 'Gender Icons', 'crypto' ) => array(
			'genderless', 'intersex', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars'
		),
		__( 'File Type Icons', 'crypto' ) => array(
			'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-movie-o', 'file-o', 'file-pdf-o', 'file-photo-o', 'file-picture-o', 'file-powerpoint-o', 'file-sound-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o', 'file-zip-o'
		),
		__( 'Spinner Icons', 'crypto' ) => array(
			'circle-o-notch', 'cog', 'gear', 'refresh', 'spinner'
		),
		__( 'Form Control Icons', 'crypto' ) => array(
			'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o'
		),
		__( 'Payment Icons', 'crypto' ) => array(
			'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'credit-card-alt', 'google-wallet', 'paypal'
		),
		__( 'Chart Icons', 'crypto' ) => array(
			'area-chart', 'bar-chart', 'bar-chart-o', 'line-chart', 'pie-chart'
		),
		__( 'Currency Icons', 'crypto' ) => array(
			'bitcoin', 'btc', 'cny', 'dollar', 'eur', 'euro', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rmb', 'rouble', 'rub', 'ruble', 'rupee', 'shekel', 'sheqel', 'try', 'turkish-lira', 'usd', 'won', 'yen'
		),
		__( 'Text Editor Icons', 'crypto' ) => array(
			'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain', 'chain-broken', 'clipboard', 'columns', 'copy', 'cut', 'dedent', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'paste', 'repeat', 'rotate-left', 'rotate-right', 'save', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo', 'unlink'
		),
		__( 'Directional Icons', 'crypto' ) => array(
			'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up', 'toggle-down', 'toggle-left', 'toggle-right', 'toggle-up'
		),
		__( 'Video Player Icons', 'crypto' ) => array(
			'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'pause-circle', 'pause-circle-o', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'stop-circle', 'stop-circle-o', 'youtube-play'
		),
		__( 'Brand Icons', 'crypto' ) => array(
			'500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'bandcamp', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'bitcoin', 'black-tie', 'bluetooth', 'bluetooth-b', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'codiepie', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'edge', 'eercast', 'empire', 'envira', 'etsy', 'expeditedssl', 'fa', 'facebook', 'facebook-f', 'facebook-official', 'facebook-square', 'firefox', 'first-order', 'flickr', 'font-awesome', 'fonticons', 'fort-awesome', 'forumbee', 'foursquare', 'free-code-camp', 'ge', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'gitlab', 'gittip', 'glide', 'glide-g', 'google', 'google-plus', 'google-plus-circle', 'google-plus-official', 'google-plus-square', 'google-wallet', 'gratipay', 'grav', 'hacker-news', 'houzz', 'html5', 'imdb', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linode', 'linux', 'maxcdn', 'meanpath', 'medium', 'meetup', 'mixcloud', 'modx', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pied-piper-pp', 'pinterest', 'pinterest-p', 'pinterest-square', 'product-hunt', 'qq', 'quora', 'ra', 'ravelry', 'rebel', 'reddit', 'reddit-alien', 'reddit-square', 'renren', 'resistance', 'safari', 'scribd', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'superpowers', 'telegram', 'tencent-weibo', 'themeisle', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'usb', 'viacoin', 'viadeo', 'viadeo-square', 'vimeo', 'vimeo-square', 'vine', 'vk', 'wechat', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'wpbeginner', 'wpexplorer', 'wpforms', 'xing', 'xing-square', 'y-combinator', 'y-combinator-square', 'yahoo', 'yc', 'yc-square', 'yelp', 'yoast', 'youtube', 'youtube-play', 'youtube-square'
		),
		__( 'Medical Icons', 'crypto' ) => array(
			'ambulance', 'h-square', 'heart', 'heart-o', 'heartbeat', 'hospital-o', 'medkit', 'plus-square', 'stethoscope', 'user-md', 'wheelchair', 'wheelchair-alt'
		)
	);

	return $icons;
}

/**
 * Create pages on Theme Activation
 */
if ( isset( $_GET['activated'] ) && is_admin() ) {
	$pages_to_add = array(
		array(
			'post_type' => 'page',
			'post_title' => __('Submit News', 'crypto'),
			'post_content' => '',
			'page_template' => 'page-news-submission.php',
			'post_status' => 'publish',
			'post_author' => 1,
		),
	);
	foreach ( $pages_to_add as $page_settings ) {
		$page_check = get_page_by_title( $page_settings['post_title'] );
		if ( !isset( $page_check->ID ) ) {
			$new_page_id = wp_insert_post( $page_settings );
			if ( !empty( $page_settings['page_template'] ) ) {
				update_post_meta( $new_page_id, '_wp_page_template', $page_settings['page_template'] );
			}
			$page_id = $new_page_id;
		} else {
			$page_id = $page_check->ID;
		}
	}
}


/**
 * Get the current post's thumbnail URL.
 *
 * @param string $size
 *
 * @return string
 */
if( !function_exists('mts_get_thumbnail_url')){
	function mts_get_thumbnail_url( $size = 'full' ) {
		$post_id = get_the_ID() ;
		if (has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
			return $image[0];
		}

		// use first attached image
		$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
		if (!empty($images)) {
			$image = reset($images);
			$image_data = wp_get_attachment_image_src( $image->ID, $size );
			return $image_data[0];
		}

		// use no preview fallback
		if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
			return get_template_directory_uri().'/images/nothumb-'.$size.'.png';
		}

		return '';
	}
}
/**
 * Create and show column for featured in portfolio items list admin page.
 * @param $post_ID
 *
 * @return string url
 */
if( !function_exists('mts_get_featured_image')){
	function mts_get_featured_image($post_ID) {
		$post_thumbnail_id = get_post_thumbnail_id($post_ID);
		if ($post_thumbnail_id) {
			$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'crypto-widgetfull');
			return $post_thumbnail_img[0];
		}
	}
}

/**
 * Adds a `Featured Image` column header in the item list admin page.
 *
 * @param array $defaults
 *
 * @return array
 */
function mts_columns_head($defaults) {
	if (get_post_type() == 'post') {
		$defaults['featured_image'] = __('Featured Image', 'crypto' );
	}

	return $defaults;
}
add_filter('manage_posts_columns', 'mts_columns_head');

/**
 * Adds a `Featured Image` column row value in the item list admin page.
 *
 * @param string $column_name The name of the column to display.
 * @param int $post_ID The ID of the current post.
 */
function mts_columns_content($column_name, $post_ID) {
	if ($column_name == 'featured_image') {
		$post_featured_image = mts_get_featured_image($post_ID);
		if ($post_featured_image) {
			echo '<img width="150" height="100" src="' . esc_url( $post_featured_image ) . '" />';
		}
	}
}
add_action('manage_posts_custom_column', 'mts_columns_content', 10, 2);

/**
 * Admin styles
 */
function mts_columns_css() {
	echo '<style type="text/css">.posts .column-featured_image img { max-width: 100%; height: auto }</style>';
}
add_action( 'admin_print_styles', 'mts_columns_css' );

/**
 * Change the HTML markup of the post thumbnail.
 *
 * @param string $html
 * @param int $post_id
 * @param string $post_image_id
 * @param int $size
 * @param string $attr
 *
 * @return string
 */
function mts_post_image_html( $html, $post_id, $post_image_id, $size, $attr ) {
	if ( has_post_thumbnail( $post_id ) || 'shop_thumbnail' === $size )
		return $html;

	// use first attached image
	$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
	if (!empty($images)) {
		$image = reset($images);
		return wp_get_attachment_image( $image->ID, $size, false, $attr );
	}

	// use no preview fallback
	if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
		$placeholder = get_template_directory_uri().'/images/nothumb-'.$size.'.png';
		$mts_options = get_option( MTS_THEME_NAME );
		if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
			$placeholder_src = '';
			$layzr_attr = ' data-layzr="'.esc_attr( $placeholder ).'"';
		} else {
			$placeholder_src = $placeholder;
			$layzr_attr = '';
		}

		$placeholder_classs = 'attachment-'.$size.' wp-post-image';
		return '<img src="'.esc_url( $placeholder_src ).'" class="'.esc_attr( $placeholder_classs ).'" alt="'.esc_attr( get_the_title() ).'"'.$layzr_attr.'>';
	}

	return '';
}
add_filter( 'post_thumbnail_html', 'mts_post_image_html', 10, 5 );

/**
 * Add data-layzr attribute to featured image ( for lazy load )
 *
 * @param array $attr
 * @param WP_Post $attachment
 * @param string|array $size
 *
 * @return array
 */
function mts_image_lazy_load_attr( $attr, $attachment, $size ) {
	if ( is_admin() || is_feed() ) return $attr;
	if ( is_single() && function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) return $attr;
	$mts_options = get_option( MTS_THEME_NAME );
	if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
		$attr['data-layzr'] = $attr['src'];
		$attr['src'] = '';
		if ( isset( $attr['srcset'] ) ) {
			$attr['data-layzr-srcset'] = $attr['srcset'];
			$attr['srcset'] = '';
		}
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'mts_image_lazy_load_attr', 10, 3 );

/**
 * Add data-layzr attribute to post content images ( for lazy load )
 *
 * @param string $content
 *
 * @return string
 */

function mts_content_image_lazy_load_attr( $content ) {
	$mts_options = get_option( MTS_THEME_NAME );
	if ( is_single() && function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return $content;
	}
	if ( ! empty( $mts_options['mts_lazy_load'] )
		 && ! empty( $mts_options['mts_lazy_load_content'] )
		 && ! empty( $content ) ) {
		$content = preg_replace_callback(
			'/<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/',
			'mts_content_image_lazy_load_attr_callback',
			$content
		);
	}

	return $content;
}
add_filter('the_content', 'mts_content_image_lazy_load_attr');

/**
 * Callback to move src to data-src and replace it with a 1x1 tranparent image.
 *
 * @param $matches
 *
 * @return string
 */
function mts_content_image_lazy_load_attr_callback( $matches ) {
	$transparent_img = 'data:image/gif,GIF89a%01%00%01%00%80%00%00%00%00%00%FF%FF%FF%21%F9%04%01%00%00%00%00%2C%00%00%00%00%01%00%01%00%00%02%01D%00%3B';
	if ( preg_match( '/ data-lazy=[\'"]false[\'"]/', $matches[0] ) ) {
		return '<img ' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '>';
	} else {
		return '<img ' . $matches[1] . 'src="' . $transparent_img . '" data-layzr="' . $matches[2] . '"' . str_replace( 'srcset=', 'data-layzr-srcset=', $matches[3]). '>';
	}
}

/**
 * Enable Widgetized sidebar and Footer
 */
function mts_register_sidebars() {
	$mts_options = get_option( MTS_THEME_NAME );

	// Default sidebar
	register_sidebar( array(
		'name' => __('Sidebar', 'crypto'),
		'description'   => __( 'Default sidebar.', 'crypto' ),
		'id' => 'sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Header Ad sidebar
	register_sidebar(array(
		'name' => __('Header Ad', 'crypto'),
		'description'   => __( '728x90 Ad Area', 'crypto' ),
		'id' => 'widget-header',
		'before_widget' => '<div id="%1$s" class="widget-header">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	//Homepage Featured Sidebar
	if ( $mts_options['mts_featured_slider_type'] == 'left-slider' || $mts_options['mts_featured_slider_type'] == 'right-slider' ) {
		register_sidebar( array(
	        'name' => __('Featured Area Sidebar','crypto'),
	        'id' => 'home-featured',
	        'description' => __( 'Featured Area Sidebar. This sidebar will be shown when Left Slider/ Right Slider option is enabled in Homepage Featured Section.', 'crypto' ),
	        'before_widget' => '<div id="%1$s" class="widget %2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h3>',
	        'after_title' => '</h3>'
	    ) );
	}

    //HomePage Posts Sidebar
	if ( !empty( $mts_options['mts_featured_categories'] ) && is_array( $mts_options['mts_featured_categories'] )) {
	    foreach ( $mts_options['mts_featured_categories'] as $section ) {
			$category_id = $section['mts_featured_category'];
			if( 'latest' != $category_id) {
				$category_name = get_cat_name( $category_id );
			} else {
				$category_name = __( 'Latest ', 'crypto' );
			}
			$blog_layout = $section['mts_blog_layout'];
			if( 'list' == $blog_layout ) {
				register_sidebar( array(
			        'name' => __('HomePage ' .$category_name. ' Sidebar','crypto'),
			        'id' => 'posts-sidebar-' .$category_id,
			        'description' => __( 'Appears when List Layout and ' . $category_name . ' category select.', 'crypto' ),
			        'before_widget' => '<div id="%1$s" class="widget %2$s">',
			        'after_widget' => '</div>',
			        'before_title' => '<h3>',
			        'after_title' => '</h3>'
			    ) );
			}
		}
	}

	// Top level footer widget areas
	if ( !empty( $mts_options['mts_first_footer'] )) {
		if ( empty( $mts_options['mts_first_footer_num'] )) $mts_options['mts_first_footer_num'] = 5;
		register_sidebars( $mts_options['mts_first_footer_num'], array(
			'name' => __( 'Footer %d', 'crypto' ),
			'description'   => __( 'Appears in the footer area.', 'crypto' ),
			'id' => 'footer-first',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
	// print_r($mts_options['mts_featured_categories']);

	// Custom sidebars
	if ( !empty( $mts_options['mts_custom_sidebars'] ) && is_array( $mts_options['mts_custom_sidebars'] )) {
		foreach( $mts_options['mts_custom_sidebars'] as $sidebar ) {
			if ( !empty( $sidebar['mts_custom_sidebar_id'] ) && !empty( $sidebar['mts_custom_sidebar_id'] ) && $sidebar['mts_custom_sidebar_id'] != 'sidebar-' ) {
				register_sidebar( array( 'name' => ''.$sidebar['mts_custom_sidebar_name'].'', 'id' => ''.sanitize_title( strtolower( $sidebar['mts_custom_sidebar_id'] )).'', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h3>', 'after_title' => '</h3>' ));
			}
		}
	}

	if ( mts_is_wc_active() ) {
		// Register WooCommerce Shop and Single Product Sidebar
		register_sidebar( array(
			'name' => __('Shop Page Sidebar', 'crypto' ),
			'description'   => __( 'Appears on Shop main page and product archive pages.', 'crypto' ),
			'id' => 'shop-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		register_sidebar( array(
			'name' => __('Single Product Sidebar', 'crypto' ),
			'description'   => __( 'Appears on single product pages.', 'crypto' ),
			'id' => 'product-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
}

add_action( 'widgets_init', 'mts_register_sidebars' );

/**
 * Retrieve the ID of the sidebar to use on the active page.
 *
 * @return string
 */
function mts_custom_sidebar() {
	$mts_options = get_option( MTS_THEME_NAME );

	// Default sidebar.
	$sidebar = 'sidebar';

	if ( is_single() && !empty( $mts_options['mts_sidebar_for_post'] )) $sidebar = $mts_options['mts_sidebar_for_post'];
	if ( is_page() && !empty( $mts_options['mts_sidebar_for_page'] )) $sidebar = $mts_options['mts_sidebar_for_page'];

	// Archives.
	if ( is_archive() && !empty( $mts_options['mts_sidebar_for_archive'] )) $sidebar = $mts_options['mts_sidebar_for_archive'];
	if ( is_category() && !empty( $mts_options['mts_sidebar_for_category'] )) $sidebar = $mts_options['mts_sidebar_for_category'];
	if ( is_tag() && !empty( $mts_options['mts_sidebar_for_tag'] )) $sidebar = $mts_options['mts_sidebar_for_tag'];
	if ( is_date() && !empty( $mts_options['mts_sidebar_for_date'] )) $sidebar = $mts_options['mts_sidebar_for_date'];
	if ( is_author() && !empty( $mts_options['mts_sidebar_for_author'] )) $sidebar = $mts_options['mts_sidebar_for_author'];

	// Other.
	if ( is_search() && !empty( $mts_options['mts_sidebar_for_search'] )) $sidebar = $mts_options['mts_sidebar_for_search'];
	if ( is_404() && !empty( $mts_options['mts_sidebar_for_notfound'] )) $sidebar = $mts_options['mts_sidebar_for_notfound'];

	// Woocommerce.
	if ( mts_is_wc_active() ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$sidebar = 'shop-sidebar';
			if ( !empty( $mts_options['mts_sidebar_for_shop'] )) {
				$sidebar = $mts_options['mts_sidebar_for_shop'];
			}
		}
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$sidebar = 'product-sidebar';
			if ( !empty( $mts_options['mts_sidebar_for_product'] )) {
				$sidebar = $mts_options['mts_sidebar_for_product'];
			}
		}
	}

	// Page/post specific custom sidebar-
	if ( is_page() || is_single() ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_the_ID(), '_mts_custom_sidebar', true );
		if ( !empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	// Posts page
	if ( is_home() && ! is_front_page() && 'page' == get_option( 'show_on_front' ) ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_option( 'page_for_posts' ), '_mts_custom_sidebar', true );
		if ( !empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	return apply_filters( 'mts_custom_sidebar', $sidebar );
}

/*-----------------------------------------------------------------------------------*/
/*  Load Widgets, Actions and Libraries
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad125.php" ) );

// Add the 300x250 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad300.php" ) );

// Add the 728x90 Ad Block Custom Widget.
include_once( get_theme_file_path( "functions/widget-ad728.php" ) );

// Add the Latest Tweets Custom Widget.
include_once( get_theme_file_path( "functions/widget-tweets.php" ) );

// Add Recent Posts Widget.
include_once( get_theme_file_path( "functions/widget-recentposts.php" ) );

// Add Related Posts Widget.
include_once( get_theme_file_path( "functions/widget-relatedposts.php" ) );

// Add Author Posts Widget.
include_once( get_theme_file_path( "functions/widget-authorposts.php" ) );

// Add Popular Posts Widget.
include_once( get_theme_file_path( "functions/widget-popular.php" ) );

// Add Facebook Like box Widget.
include_once( get_theme_file_path( "functions/widget-fblikebox.php" ) );

// Add Social Profile Widget.
include_once( get_theme_file_path( "functions/widget-social.php" ) );

// Add Category Posts Widget.
include_once( get_theme_file_path( "functions/widget-catposts.php" ) );

// Add Category Posts Widget.
include_once( get_theme_file_path( "functions/widget-postslider.php" ) );

// Add Adcode Widget.
include_once( get_theme_file_path( "functions/widget-adcode.php" ) );

// Add Crypto Price Converter
include_once( get_theme_file_path( "functions/widget-crypto-price-converter.php" ) );

// Add Crypto Price Chart
include_once( get_theme_file_path( "functions/widget-crypto-price-chart.php" ) );

// Add Welcome message.
include_once( get_theme_file_path( "functions/welcome-message.php" ) );

// Template Functions.
include_once( get_theme_file_path( "functions/theme-actions.php" ) );

// Post/page editor meta boxes.
include_once( get_theme_file_path( "functions/metaboxes.php" ) );

// TGM Plugin Activation.
include_once( get_theme_file_path( "functions/plugin-activation.php" ) );

// AJAX Contact Form - `mts_contact_form()`.
include_once( get_theme_file_path( 'functions/contact-form.php' ) );

// Custom menu walker.
include_once( get_theme_file_path( 'functions/nav-menu.php' ) );

// Rank Math SEO.
include_once( get_theme_file_path( 'functions/rank-math-notice.php' ) );

/*-----------------------------------------------------------------------------------*/
/* RTL
/*-----------------------------------------------------------------------------------*/
if ( ! empty( $mts_options['mts_rtl'] ) ) {
	/**
	 * RTL language support
	 *
	 * @see mts_load_footer_scripts()
	 */
	function mts_rtl() {
		if ( is_admin() ) {
			return;
		}
		global $wp_locale, $wp_styles;
		$wp_locale->text_direction = 'rtl';
		if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
			$wp_styles = new WP_Styles();
			$wp_styles->text_direction = 'rtl';
		}
	}
	add_action( 'init', 'mts_rtl' );
}

/**
 * Replace `no-js` with `js` from the body's class name.
 */
function mts_nojs_js_class() {
	echo '<script type="text/javascript">document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/,\'js\' );</script>';
}
add_action( 'wp_head', 'mts_nojs_js_class', 1 );

/**
 * Enqueue .js files.
 */
function mts_add_scripts() {
	$mts_options = get_option( MTS_THEME_NAME );

	wp_enqueue_script( 'jquery' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'customscript', get_template_directory_uri() . '/js/customscript.js', array( 'jquery' ), MTS_THEME_VERSION, true );
	if ( array_key_exists('primary-nav', $mts_options['mts_header_layout']['enabled']) && array_key_exists('main-navigation', $mts_options['mts_header_layout']['enabled']) ) {
		$nav_menu = 'both';
	} else {
		$nav_menu = 'none';

		if ( array_key_exists('primary-nav', $mts_options['mts_header_layout']['enabled']) ) {
			$nav_menu = 'primary';
		} elseif ( array_key_exists('main-navigation', $mts_options['mts_header_layout']['enabled']) ) {
			$nav_menu = 'secondary';
		}
	}
	wp_localize_script(
		'customscript',
		'mts_customscript',
		array(
			'responsive' => ( empty( $mts_options['mts_responsive'] ) ? false : true ),
			'nav_menu' => $nav_menu
		)
	);
	wp_enqueue_script( 'customscript' );

	// Slider
	wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), null, true);
	wp_localize_script('owl-carousel', 'slideropts', array( 'rtl_support' => $mts_options['mts_rtl'], 'nav_support' => $mts_options['mts_featured_nav_controls'], 'dots_support' => $mts_options['mts_featured_dots_controls'] ));

	// Animated single post/page header
	if ( is_singular() ) {
		$header_animation = mts_get_post_header_effect();
		if ( 'parallax' == $header_animation ) {
			wp_enqueue_script( 'jquery-parallax', get_template_directory_uri() . '/js/parallax.js', array( 'jquery' ) );
		} else if ( 'zoomout' == $header_animation ) {
			wp_enqueue_script( 'jquery-zoomout', get_template_directory_uri() . '/js/zoomout.js', array( 'jquery' ) );
		}
	}

	//Lightbox
	if ( ! empty( $mts_options['mts_lightbox'] ) ) {
		wp_enqueue_script( 'magnificPopup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), false, true );
	}

	//Sticky Nav
	if ( ! empty( $mts_options['mts_sticky_nav'] ) ) {
		wp_enqueue_script( 'StickyNav', get_template_directory_uri() . '/js/sticky.js', array( 'jquery' ), false, true );
	}

	// Lazy Load
	if ( ! empty( $mts_options['mts_lazy_load'] ) ) {
		if ( ! empty( $mts_options['mts_lazy_load_thumbs'] ) || ( ! empty( $mts_options['mts_lazy_load_content'] ) && is_singular() ) ) {
			wp_enqueue_script( 'layzr', get_template_directory_uri() . '/js/layzr.min.js', array( 'jquery' ), false, true );
		}
	}

	// Ajax Load More and Search Results
	wp_register_script( 'mts_ajax', get_template_directory_uri() . '/js/ajax.js', true );

	if ( is_post_type_archive('icos') ) {
		$ajax_pagination = isset($mts_options['mts_icos_pagenavigation_type']) && !empty( $mts_options['mts_icos_pagenavigation_type'] ) && $mts_options['mts_icos_pagenavigation_type'] >= 2;
		$pagenavigation_type = 'mts_icos_pagenavigation_type';
	} else {
		$ajax_pagination = isset($mts_options['mts_pagenavigation_type']) && !empty( $mts_options['mts_pagenavigation_type'] ) && $mts_options['mts_pagenavigation_type'] >= 2 && !is_singular();
		$pagenavigation_type = 'mts_pagenavigation_type';
	}

	if ( $ajax_pagination ) {
		wp_enqueue_script( 'mts_ajax' );

		wp_enqueue_script( 'historyjs', get_template_directory_uri() . '/js/history.js' );

		// Add parameters for the JS
		global $wp_query;
		$max = $wp_query->max_num_pages;
		$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
		if ( $max == 0 || ( isset($_GET['ico']) && !empty($_GET['ico']) ) ) {
			if ( is_post_type_archive('icos') ) {
				$args = array(
					'post_type' => 'icos',
					'post_status' => 'publish',
					'paged' => $paged,
					'orderby' => 'post_date',
					'icos_template' => 1,
				);

				if(isset($_GET['ico'])) {
					$date_now = date('Y-m-d');
					$args['posts_per_page'] = $mts_options['mts_tab_icos_num'];

					if( 'past' === $_GET['ico'] ) {
						$args['meta_query'] = array(
							'relation' => 'AND',
							array(
								'key'		=> 'mts_crypto_coin_start_date',
								'compare'	=> '<=',
								'value'		=> $date_now,
								'type'    => 'DATE'
							),
							array(
								'key'		=> 'mts_crypto_coin_end_date',
								'compare'	=> '<=',
								'value'		=> $date_now,
								'type'    => 'DATE'
							)
						);
					} elseif( 'upcoming' === $_GET['ico'] ) {
						$num_days = $mts_options['mts_upcoming_days'];
						$num_days_type = $mts_options['mts_upcoming_days_type'];
						$var = '+' .$num_days. ' ' .$num_days_type;

						$date_next_week = date('Y-m-d', strtotime($var));
						$args['meta_key'] = 'mts_crypto_coin_start_date';
						$args['order'] = 'DESC';
						$args['orderby'] = 'meta_value';
						$args['meta_query'] = array(
				        'key'		=> 'mts_crypto_coin_start_date',
				        'compare'	=> 'BETWEEN',
				        'value'	    => array( $date_now, $date_next_week ),
				        'type'      => 'DATE'
				    );
					} elseif( 'ongoing' === $_GET['ico'] ) {
						$args['meta_query'] = array(
							'relation' 			=> 'AND',
							array(
								'key'		=> 'mts_crypto_coin_start_date',
								'compare'	=> '<',
								'value'		=> $date_now,
								'type'		=> 'DATE'
							),
							array(
								'key'		=> 'mts_crypto_coin_end_date',
								'compare'	=> '>=',
								'value'		=> $date_now,
								'type'		=> 'DATE'
							)
						);
					}

				}

				$my_query = new WP_Query($args);

			} else {
				$my_query = new WP_Query(
					array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'paged' => $paged,
						'ignore_sticky_posts'=> 1
					)
				);
			}
			$max = $my_query->max_num_pages;
			wp_reset_postdata();
		}
		$autoload = ( $mts_options[$pagenavigation_type] == 3 );
		wp_localize_script(
			'mts_ajax',
			'mts_ajax_loadposts',
			array(
				'startPage' => $paged,
				'maxPages' => $max,
				'nextLink' => next_posts( $max, false ),
				'autoLoad' => $autoload,
				'i18n_loadmore' => __( 'Load more posts', 'dividend' ),
				'i18n_loading' => __('Loading...', 'dividend' ),
				'i18n_nomore' => __( 'No more posts.', 'dividend' )
			 )
		);
	}
	if ( ! empty( $mts_options['mts_ajax_search'] ) ) {
		wp_enqueue_script( 'mts_ajax' );
		wp_localize_script(
			'mts_ajax',
			'mts_ajax_search',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'ajax_search' => '1',
			 )
		);
	}
	//Archive ICOs tab
	if ( is_post_type_archive( 'icos' ) ) {
		wp_enqueue_script( 'mts_ajax' );
		wp_localize_script(
			'mts_ajax',
			'mts_archive_ajax_tabs',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
			 )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'mts_add_scripts' );

/**
 * Load CSS files.
 */
function mts_enqueue_css() {
	$mts_options = get_option( MTS_THEME_NAME );

	wp_enqueue_style( 'crypto-stylesheet', get_stylesheet_uri() );

	// Slider
	// also enqueued in slider widget
	wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), null);

	$handle = 'crypto-stylesheet';

	// RTL
	if ( ! empty( $mts_options['mts_rtl'] ) ) {
		wp_enqueue_style( 'mts_rtl', get_template_directory_uri() . '/css/rtl.css', array( $handle ) );
	}

	// Responsive
	if ( ! empty( $mts_options['mts_responsive'] ) ) {
		wp_enqueue_style( 'responsive', get_template_directory_uri() . '/css/responsive.css', array( $handle ) );
	}

	// WooCommerce
	if ( mts_is_wc_active() ) {
		if ( empty( $mts_options['mts_optimize_wc'] ) || ( ! empty( $mts_options['mts_optimize_wc'] ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) ) {
			wp_enqueue_style( 'woocommerce', get_template_directory_uri() . '/css/woocommerce2.css' );
			$handle = 'woocommerce';
		}
	}

	// Lightbox
	if ( ! empty( $mts_options['mts_lightbox'] ) ) {
		wp_enqueue_style( 'magnificPopup', get_template_directory_uri() . '/css/magnific-popup.css' );
	}

	// Font Awesome
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css' );

	$mts_sclayout = '';
	$mts_shareit_left = '';
	$mts_shareit_right = '';
	$mts_social_color = '';
	$mts_author = '';
	$mts_header_section = '';
	$mts_single_post = '';
	$mts_sidebar_single = '';
	$mts_sidebar_location = '';

	if ( is_page() || is_single() ) {
		$mts_sidebar_location = get_post_meta( get_the_ID(), '_mts_sidebar_location', true );
	}
	if ( $mts_sidebar_location != 'right' && ( $mts_options['mts_layout'] == 'sclayout' || $mts_sidebar_location == 'left' )) {
		$mts_sclayout = '.article { float: right; } .single-post-wrap .single_post { float: right }
		.sidebar.c-4-12 { float: left; } .single-content-wrapper:after { left: 0; right: auto; border-radius: 5px 0 0 5px; }';
		if( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
			$mts_shareit_right = '.shareit { margin: 0 -100px 0 }';
		}
	}
	if ( empty( $mts_options['mts_header_section2'] ) ) {
		$mts_header_section = '.logo-wrap { display: none; }';
	}
	$check_sidebar = mts_custom_sidebar();
	if ( $check_sidebar == 'mts_nosidebar' ) {
		$mts_sidebar_single = '.ss-full-width .single_post { width: 100%; max-width: 100%; }';
	}
	if ( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
		$mts_shareit_left = '.shareit { width: 38px; bottom: 150px; left: auto; margin: 0 0 0 -63px; position: fixed; z-index: 10; } .shareit-modern { margin: 0 0 0 -100px; } .shareit-default { margin: 0 0 0 -100px; }';
	}
	if ( !empty($mts_options['mts_header_social']) ) {
		foreach( $mts_options['mts_header_social'] as $header_icons ) {
			$mts_social_color .= '.header-social a.header-'. $header_icons['mts_header_icon'] .' { color: '. $header_icons['mts_header_icon_color'] .'; background-color: '. $header_icons['mts_header_icon_bg_color'] .'; }.header-social a.header-'. $header_icons['mts_header_icon'] .':hover { background-color: '. '#'.mts_lighten_color($header_icons['mts_header_icon_bg_color'],4) .'; }';
		}
	}
	if ( ! empty( $mts_options['mts_author_comment'] ) ) {
		$mts_author = '.comment.bypostauthor .fn:after { content: "'.__( 'Author', 'crypto' ).'"; font-size: 15px; padding: 1px 10px; border: 1px solid #000; margin-left: 8px; }';
	}

	$mts_bg = mts_get_background_styles( 'mts_background' );
	$mts_header_background = mts_get_background_styles( 'mts_header_background' );
	$mts_main_menu_background = mts_get_background_styles( 'mts_main_menu_background' );
	$mts_top_menu_background = mts_get_background_styles( 'mts_top_menu_background' );
	$mts_featured_section_bg = mts_get_background_styles( 'mts_featured_section_bg' );
	$mts_small_grid_bg = mts_get_background_styles( 'mts_small_grid_bg' );
	$mts_footer_bg = mts_get_background_styles( 'mts_footer_background' );
	$mts_color_scheme = mts_convert_hex_to_rgb($mts_options['mts_color_scheme']);
	$nav_button_hover_bg = '#'.mts_lighten_color($mts_options['mts_nav_button_bg'],4);

	$custom_css = "
		body {{$mts_bg}}
		#header {{$mts_header_background}}
		.main-menu, .search-open, .mobile-menu-active .navigation.mobile-menu-wrapper, #secondary-navigation .navigation ul ul {{$mts_main_menu_background}}
		#primary-navigation, #primary-navigation .navigation ul ul {{$mts_top_menu_background}}
		.featured-area {{$mts_featured_section_bg}}
		.small-thumb-posts {{$mts_small_grid_bg}}
		#site-footer {{$mts_footer_bg}}

		a, a:hover, .readMore a:hover, .primary-slider .btn-prev-next a:hover, #primary-navigation .navigation .menu a:hover, .copyrights a:hover, body .slide-post-info .thecategory a, .priceTable .base, .mts-team-title, #site-footer .widget li a:hover, .crypto-price .priceName, #secondary-navigation .navigation .menu .sub-menu > li:hover > a, .mts-archive-coin-item-thumb-name:hover .title { color:{$mts_options['mts_color_scheme']}; }

		.latestPost:before, .featured-category-title:after, #move-to-top, .tagcloud a, input[type='submit'], .ball-pulse > div, .pace .pace-progress, .latestPost-review-wrapper, .latestPost .review-type-circle.latestPost-review-wrapper, .widget .review-total-only.large-thumb, #wpmm-megamenu .review-total-only, .owl-controls .owl-dot.active span, .owl-controls .owl-dot:hover span, .widget .wp_review_tab_widget_content .tab_title.selected a, .owl-prev:hover, .owl-next:hover, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt.disabled, .woocommerce #respond input#submit.alt:disabled, .woocommerce #respond input#submit.alt:disabled[disabled], .woocommerce a.button.alt.disabled, .woocommerce a.button.alt:disabled, .woocommerce a.button.alt:disabled[disabled], .woocommerce button.button.alt.disabled, .woocommerce button.button.alt:disabled, .woocommerce button.button.alt:disabled[disabled], .woocommerce input.button.alt:disabled, .woocommerce input.button.alt:disabled[disabled], .woocommerce span.onsale, #commentform input#submit, .woocommerce-account .woocommerce-MyAccount-navigation li.is-active, #wp-calendar td#today, .tags > a, body .owl-prev, body .owl-next, .latestPost .thecategory, .latestPost.grid .views, .widget h3:before, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .postsby:before, .mts-icos-tabs .links .active a, .widget .wp-subscribe-wrap input.submit, .crypto-price .currencyMenuBox .toPriceMenu:hover, .latestPost .thecategory, .latestPost.grid .views, .single_post .thecategory, .pagination a:hover, #load-posts a:hover, .pagination li.nav-previous a:hover, .pagination li.nav-next a:hover, .single_post .pagination a:hover .currenttext, .currenttext, .page-numbers.current, .author-social a:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce #content nav.woocommerce-pagination ul li span.current, .woocommerce-page #content nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover { background-color:{$mts_options['mts_color_scheme']}; color:{$mts_options['mts_color_scheme_font']}; }

		.widget .wpt_widget_content .tab_title.selected a, .woocommerce-product-search button[type='submit'], .woocommerce .woocommerce-widget-layered-nav-dropdown__submit { background-color:{$mts_options['mts_color_scheme']}; }

		.postauthor-inner, .widget .wpt_widget_content .tab_title.selected a, .widget .wpt_widget_content .tab_title a, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .mts-team-title, .widget .wp_review_tab_widget_content .tab_title.selected a { border-color:{$mts_options['mts_color_scheme']}; }

		.ccc-widget.ccc-converter > div { border-color:{$mts_options['mts_color_scheme']}!important; }

		.small-thumb-posts { border-color:{$mts_options['mts_small_grid_border_color']}; }

		.latestPost.grid .latestPost-inner:hover { box-shadow: 0 2px 2px 0 rgba( $mts_color_scheme, 0.75 ); }

		#secondary-navigation .navigation .menu > li:hover a, #secondary-navigation .navigation .menu > li.current-menu-item > a, #secondary-navigation .sub-menu a:hover, #site-header .header-search:hover #s, #site-header .header-search:focus #s, #load-posts a, .pagination li.nav-previous a, .pagination li.nav-next a, .single_post .pagination a .currenttext { background :{$mts_options['mts_dark_color_scheme']}; color :{$mts_options['mts_dark_color_scheme_font']}; }

		.featured-area .widget .post-title, .featured-area .sidebar .widget .entry-title, .featured-area .widget { color :{$mts_options['mts_featured_widget_color']}; }

		.header-button a:hover { background-color: {$nav_button_hover_bg}!important }

		{$mts_sclayout}
		{$mts_shareit_left}
		{$mts_shareit_right}
		{$mts_social_color}
		{$mts_author}
		{$mts_header_section}
		{$mts_single_post}
		{$mts_sidebar_single}
		{$mts_options['mts_custom_css']}
			";
	wp_add_inline_style( $handle, $custom_css );
}
add_action( 'wp_enqueue_scripts', 'mts_enqueue_css', 99 );

/**
 * Wrap videos in .responsive-video div
 *
 * @param $html
 * @param $url
 * @param $attr
 *
 * @return string
 */
function mts_responsive_video( $html, $url, $attr ) {

	// Only video embeds
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

	// Allow user to wrap other embeds
	$providers = apply_filters('mts_responsive_video', $video_providers );

	foreach ( $providers as $provider ) {
		if ( strstr($url, $provider) ) {
			$html = '<div class="flex-video flex-video-' . sanitize_html_class( $provider ) . '">' . $html . '</div>';
			break;// Break if video found
		}
	}

	return $html;
}
add_filter( 'embed_oembed_html', 'mts_responsive_video', 99, 3 );

if ( ! function_exists( 'mts_comments' ) ) {
	/**
	 * Custom comments template.
	 * @param $comment
	 * @param $args
	 * @param $depth
	 */
	function mts_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		$mts_options = get_option( MTS_THEME_NAME ); ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<?php
			switch( $comment->comment_type ) :
				case 'pingback':
				case 'trackback': ?>
					<div id="comment-<?php comment_ID(); ?>" class="comment-list" >
						<div class="comment-author vcard">
							Pingback: <?php comment_author_link(); ?>
							<?php if ( ! empty( $mts_options['mts_comment_date'] ) ) {
								if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
									<span class="ago"><span><?php the_time( 'M d, Y' ); ?></span></span>
								<?php } else { ?>
									<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
									<?php
								}
							} ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'crypto' ), '  ', '' ) ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'crypto' ) ?></em>
							<br />
						<?php endif; ?>
					</div>
				<?php
					break;

				default: ?>
					<div id="comment-<?php comment_ID(); ?>" class="comment-list" itemscope itemtype="http://schema.org/UserComments">
						<div class="comment-author vcard">
							<?php echo get_avatar( $comment->comment_author_email, 70 ); ?>
							<?php printf( '<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person"><span itemprop="name">%s</span></span>', get_comment_author_link() ) ?>
							<?php if ( ! empty( $mts_options['mts_comment_date'] ) ) {
								if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
									<span class="ago"><span><?php the_time( 'M d, Y' ); ?></span></span>
								<?php } else { ?>
									<span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
									<?php
								}
							} ?>
							<span class="comment-meta">
								<?php edit_comment_link( __( '( Edit )', 'crypto' ), '  ', '' ) ?>
							</span>
						</div>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'crypto' ) ?></em>
							<br />
						<?php endif; ?>
						<div class="commentmetadata">
							<div class="commenttext" itemprop="commentText">
								<?php comment_text() ?>
							</div>
							<div class="reply">
								<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] )) ) ?>
							</div>
						</div>
					</div>
				<?php
				   break;
			 endswitch; ?>
		<!-- WP adds </li> -->
	<?php }
}

/**
 * Increase excerpt length to 100.
 *
 * @param $length
 *
 * @return int
 */
function mts_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'mts_excerpt_length', 20 );

/**
 * Remove [...] and shortcodes
 *
 * @param $output
 *
 * @return string
 */
function mts_custom_excerpt( $output ) {
  return preg_replace( '/\[[^\]]*]/', '', $output );
}
add_filter( 'get_the_excerpt', 'mts_custom_excerpt' );

/**
 * Truncate string to x letters/words.
 *
 * @param $str
 * @param int $length
 * @param string $units
 * @param string $ellipsis
 *
 * @return string
 */
function mts_truncate( $str, $length = 40, $units = 'letters', $ellipsis = '&nbsp;&hellip;' ) {
	if ( $units == 'letters' ) {
		if ( mb_strlen( $str ) > $length ) {
			return mb_substr( $str, 0, $length ) . $ellipsis;
		} else {
			return $str;
		}
	} else {
		return wp_trim_words( $str, $length, $ellipsis );
	}
}

if ( ! function_exists( 'mts_excerpt' ) ) {
	/**
	 * Get HTML-escaped excerpt up to the specified length.
	 *
	 * @param int $limit
	 *
	 * @return string
	 */
	function mts_excerpt( $limit = 40 ) {
	  return esc_html( mts_truncate( get_the_excerpt(), $limit, 'words' ) );
	}
}

/**
 * Change the "read more..." link to "".
 * @param $more_link
 * @param $more_link_text
 *
 * @return string
 */
function mts_remove_more_link( $more_link, $more_link_text ) {
	return '';
}
add_filter( 'the_content_more_link', 'mts_remove_more_link', 10, 2 );

if ( ! function_exists( 'mts_post_has_moretag' ) ) {
	/**
	 * Shorthand function to check for more tag in post.
	 *
	 * @return bool|int
	 */
	function mts_post_has_moretag() {
		$post = get_post();
		return preg_match( '/<!--more(.*?)?-->/', $post->post_content );
	}
}

if ( ! function_exists( 'mts_readmore' ) ) {
	/**
	 * Display a "read more" link.
	 */
	function mts_readmore() {
		?>
		<div class="readMore">
			<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
				<?php _e( 'Read More', 'crypto' ); ?> <i class="fa fa-angle-right"></i>
			</a>
		</div>
		<?php
	}
}

/**
 * Exclude trackbacks from the comment count.
 *
 * @param $count
 *
 * @return int
 */
function mts_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
add_filter( 'get_comments_number', 'mts_comment_count', 0 );

/**
 * Add `has_thumb` to the post's class name if it has a thumbnail.
 *
 * @param $classes
 *
 * @return array
 */
function has_thumb_class( $classes ) {
	if( has_post_thumbnail( get_the_ID() ) ) { $classes[] = 'has_thumb'; }
		return $classes;
}
add_filter( 'post_class', 'has_thumb_class' );

/*-----------------------------------------------------------------------------------*/
/* Add the title tag for compability with older WP versions.
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function theme_slug_render_title() { ?>
	   <title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php }
	add_action( 'wp_head', 'theme_slug_render_title' );
}

/**
 * Handle AJAX search queries.
 */
if( ! function_exists( 'ajax_mts_search' ) ) {
	function ajax_mts_search() {
		$query = $_REQUEST['q']; // It goes through esc_sql() in WP_Query
		$args = array('s' => $query, 'posts_per_page' => 3, 'post_status' => 'publish');
		$search_link = esc_url( get_search_link( $query ) );
		if(isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])) {
			$args['post_type'] = $_REQUEST['post_type'];
			$search_link = esc_url( get_search_link( $query ) ).'?post_type=icos';
		}
		$search_query = new WP_Query($args);
		$search_count = $search_query->found_posts;
		if ( !empty( $query ) && $search_query->have_posts() ) :
			//echo '<h5>Results for: '. $query.'</h5>';
			echo '<ul class="ajax-search-results">';
			while ( $search_query->have_posts() ) : $search_query->the_post();
				?><li>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>">
						<?php if ( has_post_thumbnail() ) { ?>
							<?php the_post_thumbnail( 'crypto-widgetthumb', array( 'title' => '' ) ); ?>
						<?php } else { ?>
							<img class="wp-post-image" src="<?php echo get_template_directory_uri() . '/images/nothumb-crypto-widgetthumb.png'; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"/>
						<?php } ?>
						<?php the_title(); ?>
					</a>
					<div class="meta">
						<span class="thetime"><?php the_time( 'F j, Y' ); ?></span>
					</div> <!-- / .meta -->
				</li>
				<?php
			endwhile;
			echo '</ul>';
			echo '<div class="ajax-search-meta"><span class="results-count">'.$search_count.' '.__( 'Results', 'crypto' ).'</span><a href="'.$search_link.'" class="results-link">'.__('Show all results.', 'crypto' ).'</a></div>';
		else:
			echo '<div class="no-results">'.__( 'No results found.', 'crypto' ).'</div>';
		endif;
		wp_reset_postdata();
		exit; // required for AJAX in WP
	}
}
if( !empty( $mts_options['mts_ajax_search'] )) {
	add_action( 'wp_ajax_mts_search', 'ajax_mts_search' );
	add_action( 'wp_ajax_nopriv_mts_search', 'ajax_mts_search' );
}

/**
 *  Filters that allow shortcodes in Text Widgets
 */
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content_rss', 'do_shortcode' );

/**
 * Category Background
 */
// 1. restructure array for easier handling
function mts_category_bg_array() {
	$mts_options = get_option(MTS_THEME_NAME);
	$return = array();
	if (!empty($mts_options['mts_category_colors'])) {
		foreach ($mts_options['mts_category_colors'] as $cc) {
			$return[$cc['mts_cc_category']] = $cc['mts_cc_bg'];
		}
	}
	return $return;
}
// 2. Get category background for given post
function mts_get_category_bg($cat_id = null) {
	$cat_id = intval($cat_id);
	if (empty($cat_id)) {
		$category = get_the_category();
		// prevent error if no category
		if (empty($category)) return false;
		$cat_id = $category[0]->term_id;
	}

	$colors = mts_category_bg_array();
	$return = false;
	if (!empty($colors[$cat_id])) {
		$return = $colors[$cat_id];
	}
	return $return;
}
/**
 * Category Colors
 */
// 1. restructure array for easier handling
function mts_category_colors_array() {
	$mts_options = get_option(MTS_THEME_NAME);
	$return = array();
	if (!empty($mts_options['mts_category_colors'])) {
		foreach ($mts_options['mts_category_colors'] as $cc) {
			$return[$cc['mts_cc_category']] = $cc['mts_cc_color'];
		}
	}
	return $return;
}
// 2. Get category color for given post
function mts_get_category_color($cat_id = null) {
	$cat_id = intval($cat_id);
	if (empty($cat_id)) {
		$category = get_the_category();
		// prevent error if no category
		if (empty($category)) return false;
		$cat_id = $category[0]->term_id;
	}

	$colors = mts_category_colors_array();
	$return = false;
	if (!empty($colors[$cat_id])) {
		$return = $colors[$cat_id];
	}
	return $return;
}

/**
 * Redirect feed to FeedBurner if a FeedBurner URL has been set.
 */
if ( isset( $mts_options['mts_feedburner'] ) && trim( $mts_options['mts_feedburner'] ) !== '' ) {
	/**
	 * Redirect feed to FeedBurner if a FeedBurner URL has been set.
	 */
	function mts_rss_feed_redirect() {
		$mts_options = get_option( MTS_THEME_NAME );
		global $feed;
		$new_feed = $mts_options['mts_feedburner'];
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
	add_action( 'template_redirect', 'mts_rss_feed_redirect' );
}

/**
 * Single Post Pagination - Numbers + Previous/Next.
 *
 * @param $args
 *
 * @return mixed
 */
function mts_wp_link_pages_args( $args ) {
	global $page, $numpages, $more, $pagenow;
	if ( $args['next_or_number'] != 'next_and_number' ) {
		return $args;
	}

	$args['next_or_number'] = 'number';

	if ( !$more ) {
		return $args;
	}

	if( $page-1 ) {
		$args['before'] .= _wp_link_page( $page-1 )
						. $args['link_before']. $args['previouspagelink'] . $args['link_after'] . '</a>';
	}

	if ( $page<$numpages ) {
		$args['after'] = _wp_link_page( $page+1 )
						 . $args['link_before'] . $args['nextpagelink'] . $args['link_after'] . '</a>'
						 . $args['after'];
	}

	return $args;
}
add_filter( 'wp_link_pages_args', 'mts_wp_link_pages_args' );

/**
 * Remove hentry class from pages
 *
 * @param $classes
 *
 * @return array
 */
function mts_remove_hentry( $classes ) {
	if ( is_page() ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
	}
	return $classes;
}
add_filter( 'post_class','mts_remove_hentry' );

/*-----------------------------------------------------------------------------------*/
/* WooCommerce
/*-----------------------------------------------------------------------------------*/
if ( mts_is_wc_active() ) {
	if ( !function_exists( 'mts_loop_columns' )) {
		/**
		 * Change number or products per row to 3
		 *
		 * @return int
		 */
		function mts_loop_columns() {
			return 3; // 3 products per row
		}
	}
	add_filter( 'loop_shop_columns', 'mts_loop_columns' );

	/**
	 * Redefine woocommerce_output_related_products()
	 */
	if( ! function_exists( 'woocommerce_output_related_products' ) ) {
		function woocommerce_output_related_products() {
			$args = array(
				'posts_per_page' => 3,
				'columns' => 3,
			);
			woocommerce_related_products($args); // Display 3 products in rows of 1
		}
	}

	add_filter( 'woocommerce_upsell_display_args', 'mts_woocommerce_upsell_display_args' );
	/**
	 * Redefine woocommerce_upsell_display_args()
	 */
	function mts_woocommerce_upsell_display_args( $args ) {
		$args['posts_per_page'] = 3; // Change this number
		$args['columns']        = 3; // This is the number shown per row.
		return $args;
	}

	global $pagenow;
	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
		/**
		 * Define WooCommerce image sizes.
		 */
		function mts_woocommerce_image_dimensions() {
			$catalog = array(
				'width' 	=> '223',	// px
				'height'	=> '303',	// px
				'crop'		=> 1 		// true
			);
			$single = array(
				'width' 	=> '348',	// px
				'height'	=> '483',	// px
				'crop'		=> 1 		// true
			);
			$thumbnail = array(
				'width' 	=> '87',	// px
				'height'	=> '103',	// px
				'crop'		=> 1 		// false
			);
			// Image sizes
			update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
			update_option( 'shop_single_image_size', $single ); 		// Single product image
			update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
		}
		add_action( 'init', 'mts_woocommerce_image_dimensions', 1 );
	}


	/**
	 * Change the number of product thumbnails to show per row to 4.
	 *
	 * @return int
	 */
	function mts_thumb_cols() {
	 return 4; // .last class applied to every 4th thumbnail
	}
	add_filter( 'woocommerce_product_thumbnails_columns', 'mts_thumb_cols' );

	/**
	 * Change the number of WooCommerce products to show per page.
	 *
	 * @return mixed
	 */
	function mts_products_per_page() {
		$mts_options = get_option( MTS_THEME_NAME );
		return $mts_options['mts_shop_products'];
	}
	add_filter( 'loop_shop_per_page', 'mts_products_per_page', 20 );

	/**
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param $fragments
	 *
	 * @return mixed
	 */
	function mts_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		ob_start();	?>

		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'crypto' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'crypto' ), $woocommerce->cart->cart_contents_count );?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>

		<?php $fragments['a.cart-contents'] = ob_get_clean();
		return $fragments;
	}
	add_filter( 'add_to_cart_fragments', 'mts_header_add_to_cart_fragment' );

	/**
	 * Optimize WooCommerce Scripts
	 * Updated for WooCommerce 2.0+
	 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
	 */
	function mts_child_manage_woocommerce_styles() {
		//remove generator meta tag
		remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

		//first check that woo exists to prevent fatal errors
		if ( function_exists( 'is_woocommerce' ) ) {
			//dequeue scripts and styles
			if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
				wp_dequeue_style( 'woocommerce-layout' );
				wp_dequeue_style( 'woocommerce-smallscreen' );
				wp_dequeue_style( 'woocommerce-general' );
				wp_dequeue_style( 'wc-bto-styles' ); //Composites Styles
				wp_dequeue_script( 'wc-add-to-cart' );
				wp_dequeue_script( 'wc-cart-fragments' );
				wp_dequeue_script( 'woocommerce' );
				wp_dequeue_script( 'jquery-blockui' );
				wp_dequeue_script( 'jquery-placeholder' );
			}
		}
	}
	if ( ! empty( $mts_options['mts_optimize_wc'] ) ) {
		add_action( 'wp_enqueue_scripts', 'mts_child_manage_woocommerce_styles', 99 );
	}

	// Remove WooCommerce generator tag.
	remove_action('wp_head', 'wc_generator_tag');
}

/**
 * Add <!-- next-page --> button to tinymce.
 *
 * @param $mce_buttons
 *
 * @return array
 */
function mts_wysiwyg_editor( $mce_buttons ) {
   $pos = array_search( 'wp_more', $mce_buttons, true );
   if ( $pos !== false ) {
	   $tmp_buttons = array_slice( $mce_buttons, 0, $pos+1 );
	   $tmp_buttons[] = 'wp_page';
	   $mce_buttons = array_merge( $tmp_buttons, array_slice( $mce_buttons, $pos+1 ));
   }
   return $mce_buttons;
}
add_filter( 'mce_buttons', 'mts_wysiwyg_editor' );

/**
 * Get Post header animation.
 *
 * @return string
 */
function mts_get_post_header_effect() {
	$postheader_effect = get_post_meta( get_the_ID(), '_mts_postheader', true );

	return $postheader_effect;
}

/**
 * Add Custom Gravatar Support.
 *
 * @param $avatar_defaults
 *
 * @return mixed
 */
function mts_custom_gravatar( $avatar_defaults ) {
	$mts_avatar = get_template_directory_uri() . '/images/gravatar.png';
	$avatar_defaults[$mts_avatar] = __( 'Custom Gravatar ( /images/gravatar.png )', 'crypto' );
	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'mts_custom_gravatar' );

/**
 * Add `.primary-navigation` the WP Mega Menu's
 * @param $selector
 *
 * @return string
 */
function mts_megamenu_parent_element( $selector ) {
	return '.container';
}
add_filter( 'wpmm_container_selector', 'mts_megamenu_parent_element' );

function menu_item_color( $item_output, $item_color, $item, $depth, $args ) {
	if (!empty($item_color))
		return $item_output.'<style>#menu-item-'. $item->ID . ':hover ul { border-top-color: ' . $item_color . ' !important; }
#menu-item-'. $item->ID . ' a:hover, #wpmm-megamenu.menu-item-'. $item->ID . '-megamenu a:hover, #wpmm-megamenu.menu-item-'. $item->ID . '-megamenu .wpmm-posts .wpmm-entry-title a:hover { color: '.$item_color.' !important; }</style>';
	else
		return $item_output;
}
add_filter( 'wpmm_color_output', 'menu_item_color', 10, 5 );

/**
 * Change the image size of WP Mega Menu's thumbnails.
 *
 * @param $thumbnail_html
 * @param $post_id
 *
 * @return string
 */
if( ! function_exists( 'mts_megamenu_thumbnails' ) ) {
	function mts_megamenu_thumbnails( $thumbnail_html, $post_id ) {
		$thumbnail_html = '<div class="wpmm-thumbnail">';
		$thumbnail_html .= '<a title="'.get_the_title( $post_id ).'" href="'.get_permalink( $post_id ).'">';
		if(has_post_thumbnail($post_id)):
			$thumbnail_html .= get_the_post_thumbnail($post_id, 'crypto-widgetfull', array('title' => ''));
		else:
			$thumbnail_html .= '<img src="'.get_template_directory_uri().'/images/nothumb-crypto-widgetfull.png" alt="'.__('No Preview', 'crypto').'"  class="wp-post-image" />';
		endif;
		$thumbnail_html .= '</a>';

		// WP Review
		$thumbnail_html .= (function_exists('wp_review_show_total') ? wp_review_show_total(false) : '');

		$thumbnail_html .= '</div>';

		return $thumbnail_html;
	}
}
add_filter( 'wpmm_thumbnail_html', 'mts_megamenu_thumbnails', 10, 2 );

/*-----------------------------------------------------------------------------------*/
/*  WP Review Support
/*-----------------------------------------------------------------------------------*/

/**
 * Set default colors for new reviews.
 *
 * @param $colors
 *
 * @return array
 */
function mts_new_default_review_colors( $colors ) {
	$colors = array(
		'color' => '#FFCA00',
		'fontcolor' => '#fff',
		'bgcolor1' => '#242629',
		'bgcolor2' => '#242629',
		'bordercolor' => '#242629'
	);
  return $colors;
}
add_filter( 'wp_review_default_colors', 'mts_new_default_review_colors' );

/**
 * Set default location for new reviews.
 *
 * @param $position
 *
 * @return string
 */
function mts_new_default_review_location( $position ) {
  $position = 'top';
  return $position;
}
add_filter( 'wp_review_default_location', 'mts_new_default_review_location' );


/*-----------------------------------------------------------------------------------*/
/* Post view count
/* AJAX is used to support caching plugins - it is possible to disable with filter
/* It is also possible to exclude admins with another filter
/*-----------------------------------------------------------------------------------*/

/**
 * Append JS to content for AJAX call on single.
 *
 * @param $content
 *
 * @return string
 */
function mts_view_count_js( $content ) {
	$id = get_the_ID();
	$use_ajax = apply_filters( 'mts_view_count_cache_support', true );

	$exclude_admins = apply_filters( 'mts_view_count_exclude_admins', false ); // pass in true or a user capability
	if ($exclude_admins === true) {
		$exclude_admins = 'edit_posts';
	}
	if ($exclude_admins && current_user_can( $exclude_admins )) {
		return $content; // do not count post views here
	}

	if (is_single()) {
		if ($use_ajax) {
			// enqueue jquery
			wp_enqueue_script( 'jquery' );

			$url = admin_url( 'admin-ajax.php' );
			$content .= "
			<script type=\"text/javascript\">
			jQuery(document).ready(function($) {
				$.post('".esc_js($url)."', {action: 'mts_view_count', id: '".esc_js($id)."'});
			});
			</script>";
		}

		if (!$use_ajax) {
			mts_update_view_count($id);
		}
	}

	return $content;
}
add_filter('the_content', 'mts_view_count_js');

/**
 * Call mts_update_view_count on AJAX.
 */
function mts_ajax_mts_view_count() {
	// do count
	$post_id = absint( $_POST['id'] );
	mts_update_view_count( $post_id );
	exit();
}
add_action('wp_ajax_mts_view_count', 'mts_ajax_mts_view_count');
add_action('wp_ajax_nopriv_mts_view_count','mts_ajax_mts_view_count');

/**
 * Update the view count of a post.
 *
 * @param $post_id
 */
function mts_update_view_count( $post_id ) {
	$count = get_post_meta( $post_id, '_mts_view_count', true );
	update_post_meta( $post_id, '_mts_view_count', ++$count );

	do_action( 'mts_view_count_after_update', $post_id, $count );

	return $count;
}

/**
 * Convert color format from HEX to HSL.
 * @param $color
 *
 * @return array
 */
function mts_hex_to_hsl( $color ){

	// Sanity check
	$color = mts_check_hex_color($color);

	// Convert HEX to DEC
	$R = hexdec($color[0].$color[1]);
	$G = hexdec($color[2].$color[3]);
	$B = hexdec($color[4].$color[5]);

	$HSL = array();

	$var_R = ($R / 255);
	$var_G = ($G / 255);
	$var_B = ($B / 255);

	$var_Min = min($var_R, $var_G, $var_B);
	$var_Max = max($var_R, $var_G, $var_B);
	$del_Max = $var_Max - $var_Min;

	$L = ($var_Max + $var_Min)/2;

	if ($del_Max == 0) {
		$H = 0;
		$S = 0;
	} else {
		if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
		else			$S = $del_Max / ( 2 - $var_Max - $var_Min );

		$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

		if	  ($var_R == $var_Max) $H = $del_B - $del_G;
		else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
		else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

		if ($H<0) $H++;
		if ($H>1) $H--;
	}

	$HSL['H'] = ($H*360);
	$HSL['S'] = $S;
	$HSL['L'] = $L;

	return $HSL;
}

/**
 * Convert color format from HSL to HEX.
 *
 * @param array $hsl
 *
 * @return string
 */
function mts_hsl_to_hex( $hsl = array() ){

	list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );

	if( $S == 0 ) {
		$r = $L * 255;
		$g = $L * 255;
		$b = $L * 255;
	} else {

		if($L<0.5) {
			$var_2 = $L*(1+$S);
		} else {
			$var_2 = ($L+$S) - ($S*$L);
		}

		$var_1 = 2 * $L - $var_2;

		$r = round(255 * mts_huetorgb( $var_1, $var_2, $H + (1/3) ));
		$g = round(255 * mts_huetorgb( $var_1, $var_2, $H ));
		$b = round(255 * mts_huetorgb( $var_1, $var_2, $H - (1/3) ));
	}

	// Convert to hex
	$r = dechex($r);
	$g = dechex($g);
	$b = dechex($b);

	// Make sure we get 2 digits for decimals
	$r = (strlen("".$r)===1) ? "0".$r:$r;
	$g = (strlen("".$g)===1) ? "0".$g:$g;
	$b = (strlen("".$b)===1) ? "0".$b:$b;

	return $r.$g.$b;
}

/**
 * Convert color format from Hue to RGB.
 *
 * @param $v1
 * @param $v2
 * @param $vH
 *
 * @return mixed
 */
function mts_huetorgb( $v1,$v2,$vH ) {
	if( $vH < 0 ) {
		$vH += 1;
	}

	if( $vH > 1 ) {
		$vH -= 1;
	}

	if( (6*$vH) < 1 ) {
		   return ($v1 + ($v2 - $v1) * 6 * $vH);
	}

	if( (2*$vH) < 1 ) {
		return $v2;
	}

	if( (3*$vH) < 2 ) {
		return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
	}

	return $v1;

}

/**
 * Get the 6-digit hex color.
 *
 * @param $hex
 *
 * @return mixed|string
 */
function mts_check_hex_color( $hex ) {
	// Strip # sign is present
	$color = str_replace("#", "", $hex);

	// Make sure it's 6 digits
	if( strlen($color) == 3 ) {
		$color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
	}

	return $color;
}

/**
 * Check if color is considered light or not.
 * @param $color
 *
 * @return bool
 */
function mts_is_light_color( $color ){

	$color = mts_check_hex_color( $color );

	// Calculate straight from rbg
	$r = hexdec($color[0].$color[1]);
	$g = hexdec($color[2].$color[3]);
	$b = hexdec($color[4].$color[5]);

	return ( ( $r*299 + $g*587 + $b*114 )/1000 > 130 );
}

/**
 * Darken color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_darken_color( $color, $amount = 10 ) {

	$hsl = mts_hex_to_hsl( $color );

	// Darken
	$hsl['L'] = ( $hsl['L'] * 100 ) - $amount;
	$hsl['L'] = ( $hsl['L'] < 0 ) ? 0 : $hsl['L']/100;

	// Return as HEX
	return mts_hsl_to_hex($hsl);
}

/**
 * Lighten color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_lighten_color( $color, $amount = 10 ) {

	$hsl = mts_hex_to_hsl( $color );

	// Lighten
	$hsl['L'] = ( $hsl['L'] * 100 ) + $amount;
	$hsl['L'] = ( $hsl['L'] > 100 ) ? 1 : $hsl['L']/100;

	// Return as HEX
	return mts_hsl_to_hex($hsl);
}

/**
 * Used to create rgb from hexa decimal value
 * @return string
 */
function mts_convert_hex_to_rgb($hex){
	list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
	return "$r, $g, $b";
}

/**
 * Generate css from background theme option.
 *
 * @param $option_id
 *
 * @return string|void
 */
if( ! function_exists( 'mts_get_background_styles' ) ) {
	function mts_get_background_styles( $option_id ) {

		$mts_options = get_option( MTS_THEME_NAME );

		if ( ! isset( $mts_options[ $option_id ]) ) {
			return;
		}

		$background_option = $mts_options[ $option_id ];
		$output = '';
		$background_image_type = isset( $background_option['use'] ) ? $background_option['use'] : '';

		if ( isset( $background_option['color'] ) && !empty( $background_option['color'] ) && 'gradient' !== $background_image_type ) {
			$output .= 'background-color:'.$background_option['color'].';';
		}

		if ( !empty( $background_image_type ) ) {

			if ( 'upload' == $background_image_type ) {

				if ( isset( $background_option['image_upload'] ) && !empty( $background_option['image_upload'] ) ) {
					$output .= 'background-image:url('.$background_option['image_upload'].');';
				}
				if ( isset( $background_option['repeat'] ) && !empty( $background_option['repeat'] ) ) {
					$output .= 'background-repeat:'.$background_option['repeat'].';';
				}
				if ( isset( $background_option['attachment'] ) && !empty( $background_option['attachment'] ) ) {
					$output .= 'background-attachment:'.$background_option['attachment'].';';
				}
				if ( isset( $background_option['position'] ) && !empty( $background_option['position'] ) ) {
					$output .= 'background-position:'.$background_option['position'].';';
				}
				if ( isset( $background_option['size'] ) && !empty( $background_option['size'] ) ) {
					$output .= 'background-size:'.$background_option['size'].';';
				}

			} else if ( 'gradient' == $background_image_type ) {

				$from	  = $background_option['gradient']['from'];
				$to		= $background_option['gradient']['to'];
				$direction = $background_option['gradient']['direction'];

				if ( !empty( $from ) && !empty( $to ) ) {

					$output .= 'background: '.$background_option['color'].';';

					if ( 'horizontal' == $direction ) {

						$output .= 'background: -moz-linear-gradient(left, '.$from.' 0%, '.$to.' 100%);';
						$output .= 'background: -webkit-gradient(linear, left top, right top, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
						$output .= 'background: -webkit-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -o-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -ms-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: linear-gradient(to right, '.$from.' 0%,'.$to.' 100%);';
						$output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=1 );";

					} else {

						$output .= 'background: -moz-linear-gradient(top, '.$from.' 0%, '.$to.' 100%);';
						$output .= 'background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
						$output .= 'background: -webkit-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -o-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: -ms-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
						$output .= 'background: linear-gradient(to bottom, '.$from.' 0%,'.$to.' 100%);';
						$output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=0 );";
					}
				}

			} else if ( 'pattern' == $background_image_type ) {

				$output .= 'background-image:url('.get_template_directory_uri().'/images/'.$background_option['image_pattern'].'.png'.');';
			}
		}

		return $output;
	}
}

/**
 * Add link to theme options panel inside admin bar
 */
function mts_admin_bar_link() {
	/** @var WP_Admin_bar $wp_admin_bar */
	global $wp_admin_bar;

	if( current_user_can( 'edit_theme_options' ) ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'mts-theme-options',
			'title' => __( 'Theme Options', 'crypto' ),
			'href' => admin_url( 'themes.php?page=theme_options' )
		) );
	}
}
add_action( 'admin_bar_menu', 'mts_admin_bar_link', 65 );


/**
 * Retrieves the attachment ID from the file URL
 *
 * @param $image_url
 *
 * @return string
 */
if( ! function_exists( 'mts_get_image_id_from_url' ) ) {
	function mts_get_image_id_from_url( $image_url ) {
		if ( is_numeric( $image_url ) ) return $image_url;
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		if ( isset( $attachment[0] ) ) {
			return $attachment[0];
		} else {
			return false;
		}
	}
}

/**
 * Remove new line tags from string
 *
 * @param $text
 *
 * @return string
 */
function mts_escape_text_tags( $text ) {
	return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}

/**
 * Remove new line tags from string
 *
 * @return string
 */
if( ! function_exists( 'mts_single_post_schema' ) ) {
	function mts_single_post_schema() {

		if ( is_singular( 'post' ) ) {

			global $post, $mts_options;

			if ( has_post_thumbnail( $post->ID ) && !empty( $mts_options['mts_logo'] ) ) {

				$logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );

				if ( $logo_id ) {

					$images  = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
					$logo	= wp_get_attachment_image_src( $logo_id, 'full' );
					$excerpt = mts_escape_text_tags( $post->post_excerpt );
					$content = $excerpt === "" ? mb_substr( mts_escape_text_tags( $post->post_content ), 0, 110 ) : $excerpt;

					$args = array(
						"@context" => "http://schema.org",
						"@type"	=> "BlogPosting",
						"mainEntityOfPage" => array(
							"@type" => "WebPage",
							"@id"   => get_permalink( $post->ID )
						),
						"headline" => ( function_exists( '_wp_render_title_tag' ) ? wp_get_document_title() : wp_title( '', false, 'right' ) ),
						"image"	=> array(
							"@type"  => "ImageObject",
							"url"	 => $images[0],
							"width"  => $images[1],
							"height" => $images[2]
						),
						"datePublished" => get_the_time( DATE_ISO8601, $post->ID ),
						"dateModified"  => get_post_modified_time(  DATE_ISO8601, __return_false(), $post->ID ),
						"author" => array(
							"@type" => "Person",
							"name"  => mts_escape_text_tags( get_the_author_meta( 'display_name', $post->post_author ) )
						),
						"publisher" => array(
							"@type" => "Organization",
							"name"  => get_bloginfo( 'name' ),
							"logo"  => array(
								"@type"  => "ImageObject",
								"url"	 => $logo[0],
								"width"  => $logo[1],
								"height" => $logo[2]
							)
						),
						"description" => ( class_exists('WPSEO_Meta') ? WPSEO_Meta::get_value( 'metadesc' ) : $content )
					);

					echo '<script type="application/ld+json">' , PHP_EOL;
					echo wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) , PHP_EOL;
					echo '</script>' , PHP_EOL;
				}
			}
		}
	}
}
add_action( 'wp_head', 'mts_single_post_schema' );

if ( ! empty( $mts_options['mts_async_js'] ) ) {
	function mts_js_async_attr($tag){

		if (is_admin())
			return $tag;

		$async_files = apply_filters( 'mts_js_async_files', array(
			get_template_directory_uri() . '/js/ajax.js',
			get_template_directory_uri() . '/js/contact.js',
			get_template_directory_uri() . '/js/customscript.js',
			get_template_directory_uri() . '/js/jquery.magnific-popup.min.js',
			get_template_directory_uri() . '/js/layzr.min.js',
			get_template_directory_uri() . '/js/owl.carousel.min.js',
			get_template_directory_uri() . '/js/parallax.js',
			get_template_directory_uri() . '/js/sticky.js',
			get_template_directory_uri() . '/js/zoomout.js',
		 ) );

		$add_async = false;
		foreach ($async_files as $file) {
			if (strpos($tag, $file) !== false) {
				$add_async = true;
				break;
			}
		}

		if ($add_async)
			$tag = str_replace( ' src', ' async="async" src', $tag );

		return $tag;
	}
	add_filter( 'script_loader_tag', 'mts_js_async_attr', 10 );
}

if ( ! empty( $mts_options['mts_remove_ver_params'] ) ) {
	function mts_remove_script_version( $src ){

		if ( is_admin() )
			return $src;

		$parts = explode( '?ver', $src );
		return $parts[0];
	}
	add_filter( 'script_loader_src', 'mts_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', 'mts_remove_script_version', 15, 1 );
}


/*
 * Check if Latest Posts are being displayed on homepage and set posts_per_page accordingly
 */
function mts_home_posts_per_page($query) {
	global $mts_options;

	if ( ! $query->is_home() || ! $query->is_main_query() )
		return;

	$set_posts_per_page = 0;
	if ( ! empty( $mts_options['mts_featured_categories'] ) ) {
		foreach ( $mts_options['mts_featured_categories'] as $section ) {
			if ( $section['mts_featured_category'] == 'latest' ) {
				$set_posts_per_page = $section['mts_featured_category_postsnum'];
				break;
			}
		}
	}
	if ( ! empty( $set_posts_per_page ) ) {
		$query->set( 'posts_per_page', $set_posts_per_page );
	}
}
add_action( 'pre_get_posts', 'mts_home_posts_per_page' );

// Map images and categories in group field after demo content import
add_filter( 'mts_correct_single_import_option', 'mts_correct_homepage_sections_import', 10, 3 );
function mts_correct_homepage_sections_import( $item, $key, $data ) {

	if ( !in_array( $key, array( 'mts_custom_slider', 'mts_featured_categories' ) ) ) return $item;

	$new_item = $item;

	if ( 'mts_custom_slider' === $key ) {

		foreach( $item as $i => $image ) {

			$id = $image['mts_custom_slider_image'];

			if ( is_numeric( $id ) ) {

				if ( array_key_exists( $id, $data['posts'] ) ) {

					$new_item[ $i ]['mts_custom_slider_image'] = $data['posts'][ $id ];
				}

			} else {

				if ( array_key_exists( $id, $data['image_urls'] ) ) {

					$new_item[ $i ]['mts_custom_slider_image'] = $data['image_urls'][ $id ];
				}
			}
		}

	} else { // mts_featured_categories

		foreach( $item as $i => $category ) {

			$cat_id = $category['mts_featured_category'];

			if ( is_numeric( $cat_id ) && array_key_exists( $cat_id, $data['terms']['category'] ) ) {

				$new_item[ $i ]['mts_featured_category'] = $data['terms']['category'][ $cat_id ];
			}
		}
	}

	return $new_item;
}

/**
 * Get array of layout sidebars in Sidebar ID => Sidebar Name key/value pairs
 *
 * @return array
 */
function mts_get_layout_sidebars() {
	$mts_options = get_option( MTS_THEME_NAME );
	$list = array();
    if( !empty( $mts_options['mts_featured_categories'] ) ) {
        foreach ( $mts_options['mts_featured_categories'] as $post_sidebar ) {
            $style = isset($post_sidebar['mts_blog_layout']) ? $post_sidebar['mts_blog_layout'] : '';

            $category_id = $post_sidebar['mts_featured_category'];

            if ( 'latest' != $category_id ) {
                $cat_name = get_cat_name( $category_id );
            } else {
                $cat_name = __( 'Latest ', 'crypto' );
            }

            if ( in_array( $style, array( 'list' ) ) && !empty( $cat_name ) ) {
            	$list[ sanitize_title( strtolower( 'posts-sidebar-'.$category_id )) ] = strtolower( 'posts-sidebar-'.$category_id );
            }
        }
    }
    return $list;
}

/**
 * Get array of sidebar ids to exclude in options
 *
 * @return array
 */
function mts_get_excluded_sidebars() {
    $layout_sidebars = mts_get_layout_sidebars();
    return array_merge( array_keys( $layout_sidebars ), array( 'sidebar', 'widget-header', 'widget-subscribe', 'widget-single-subscribe', 'widget-icos-subscribe', 'footer-first', 'footer-first-2', 'footer-first-3', 'footer-first-4', 'footer-second', 'footer-second-2', 'footer-second-3', 'footer-second-4', 'widget-header','shop-sidebar', 'product-sidebar', 'home-featured' ) );
}

//CMB2 directory URL reset
function update_cmb2_meta_box_url( $url ) {
	return get_template_directory_uri().'/functions/CMB2' ;
} add_filter( 'cmb2_meta_box_url', 'update_cmb2_meta_box_url' );

/*
 * Archive Icos Tab
 */
add_action('wp_ajax_mts_archive_ico_tabs', 'mts_ajax_archive_ico_tab');
add_action('wp_ajax_nopriv_mts_archive_ico_tabs', 'mts_ajax_archive_ico_tab');
function mts_ajax_archive_ico_tab() {
    mts_archive_ico_tab( $_POST['archive_ico_tab'] );
    die();
}
if ( ! function_exists('mts_archive_ico_tab') ) {
	function mts_archive_ico_tab( $tab = 'ico-ongoing' ) {

		global $mts_options;

		switch ( $tab ) {

			case 'ico-ongoing':
			?>
				<?php if ( get_query_var('paged') && get_query_var('paged') > 1 ){
					$paged = get_query_var('paged');
				} elseif ( get_query_var('page') && get_query_var('page') > 1  ){
					$paged = get_query_var('page');
				} else {
					$paged = 1;
				}
				$today = date('Y-m-d');
				$args = array (
			    'post_type' => 'icos',
			    'post_status' => 'publish',
					'ignore_sticky_posts'=> 1,
					'paged' => $paged,
					'posts_per_page' => $mts_options['mts_tab_icos_num'],
			    'meta_query' => array(
					'relation' 			=> 'AND',
							array(
					        'key'		=> 'mts_crypto_coin_start_date',
					        'compare'	=> '<',
					        'value'		=> $today,
					        'type'		=> 'DATE'
					    ),
					    array(
					        'key'		=> 'mts_crypto_coin_end_date',
					        'compare'	=> '>=',
					        'value'		=> $today,
					        'type'		=> 'DATE'
					    )
				    ),
				    'order'				=> 'DESC',
				    'orderby'			=> 'meta_value',
				);
				$latest_posts_query = new WP_Query( $args );
				global $wp_query;
				// Put default query object in a temp variable
				$tmp_query = $wp_query;
				// Now wipe it out completely
				$wp_query = null;
				// Re-populate the global with our custom query
				$wp_query = $latest_posts_query; ?>

				<div class="mts-archive-ico-headings">
					<div class="mts-archive-ico-title"><?php _e('Name', 'crypto'); ?></div>
					<div class="mts-archive-ico-start"><?php _e('Start', 'crypto'); ?></div>
					<div class="mts-archive-ico-end"><?php _e('End', 'crypto'); ?></div>
				</div>

				<?php
				$j = 0; if ( $latest_posts_query->have_posts() ) : while ( $latest_posts_query->have_posts() ) : $latest_posts_query->the_post(); ?>

					<?php $crypto_coin_tagline = get_post_meta( get_the_ID(), 'mts_crypto_coin_tagline', 1 );
					$crypto_coin_start = get_post_meta( get_the_ID(), 'mts_crypto_coin_start_date', 1 );
					$crypto_coin_end   = get_post_meta( get_the_ID(), 'mts_crypto_coin_end_date', 1 );
					$crypto_coin_button   = get_post_meta( get_the_ID(), 'mts_crypto_coin_more_details', 1 ); ?>

					<div class="mts-archive-ico">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="mts-archive-coin-item-info">
							<div class="mts-archive-coin-item-thumb-name">
								<div class="featured-thumbnail"><?php the_post_thumbnail('crypto-widgetthumb', array('title' => '')); ?></div>
								<div class="mts-archive-ico-name">
									<h2 class="title front-view-title"><?php the_title(); ?></h2>
									<div class="mts-coin-tagline"><?php echo $crypto_coin_tagline; ?></div>
								</div>
							</div>
							<div class="mts-archive-coin-item-start">
								<div class="mts-item-start-date"><?php echo date('M d, Y', strtotime($crypto_coin_start)); ?></div>
								<div class="mts-item-start-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_start);
									$diff = $now->diff($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('%d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('%d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('Starts in 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Starts in %d days', 'crypto'), $diff->days);
										} else {
											printf(__('Starts in %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
							<div class="mts-archive-coin-item-end">
								<div class="mts-item-end-date"><?php echo date('M d, Y', strtotime($crypto_coin_end)); ?></div>
								<div class="mts-item-end-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_end);
									$diff = $now->diff($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('Expired 1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Expired %d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('Expired %d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('In 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('In %d days', 'crypto'), $diff->days);
										} else {
											printf(__('In %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
						</a>
						<div class="mts-archive-coin-button">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo $crypto_coin_button; ?></a>
						</div>
					</div>
				<?php ++$j; endwhile; endif; ?>

				<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
					<?php mts_pagination('', 3, 'mts_icos_pagenavigation_type'); ?>
				<?php }

				$wp_query = $tmp_query;
				// Be kind; rewind
				wp_reset_postdata(); ?>

			<?php

			break;

			case 'ico-upcoming':

				if ( get_query_var('paged') && get_query_var('paged') > 1 ){
					$paged = get_query_var('paged');
				} elseif ( get_query_var('page') && get_query_var('page') > 1  ){
					$paged = get_query_var('page');
				} else {
					$paged = 1;
				}

				$num_days = $mts_options['mts_upcoming_days'];
				$num_days_type = $mts_options['mts_upcoming_days_type'];
				$var = '+' .$num_days. ' ' .$num_days_type;

				$date_now = date('Y-m-d');
				$date_next_week = date('Y-m-d', strtotime($var)); //upto 1 month

				$args = array (
			    'post_type' => 'icos',
			    'post_status' => 'publish',
					'ignore_sticky_posts'=> 1,
					'paged' => $paged,
					'posts_per_page' => $mts_options['mts_tab_icos_num'],
					'meta_key' 			=> 'mts_crypto_coin_start_date',
				    'meta_query' => array(
					    array(
					        'key'		=> 'mts_crypto_coin_start_date',
					        'compare'	=> 'BETWEEN',
					        'value'	    => array( $date_now, $date_next_week ),
					        'type'      => 'DATE'
					    )
				    ),
				    'order'				=> 'DESC',
				    'orderby'			=> 'meta_value'
				);

				$latest_posts_query = new WP_Query( $args );
				global $wp_query;
				// Put default query object in a temp variable
				$tmp_query = $wp_query;
				// Now wipe it out completely
				$wp_query = null;
				// Re-populate the global with our custom query
				$wp_query = $latest_posts_query; ?>

				<div class="mts-archive-ico-headings">
					<div class="mts-archive-ico-title"><?php _e('Name', 'crypto'); ?></div>
					<div class="mts-archive-ico-start"><?php _e('Start', 'crypto'); ?></div>
					<div class="mts-archive-ico-end"><?php _e('End', 'crypto'); ?></div>
				</div>

				<?php
				$j = 0; if ( $latest_posts_query->have_posts() ) : while ( $latest_posts_query->have_posts() ) : $latest_posts_query->the_post(); ?>

					<?php $crypto_coin_tagline = get_post_meta( get_the_ID(), 'mts_crypto_coin_tagline', 1 );
					$crypto_coin_start = get_post_meta( get_the_ID(), 'mts_crypto_coin_start_date', 1 );
					$crypto_coin_end   = get_post_meta( get_the_ID(), 'mts_crypto_coin_end_date', 1 );
					$crypto_coin_button   = get_post_meta( get_the_ID(), 'mts_crypto_coin_more_details', 1 ); ?>

					<div class="mts-archive-ico">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="mts-archive-coin-item-info">
							<div class="mts-archive-coin-item-thumb-name">
								<div class="featured-thumbnail"><?php the_post_thumbnail('crypto-widgetthumb', array('title' => '')); ?></div>
								<div class="mts-archive-ico-name">
									<h2 class="title front-view-title"><?php the_title(); ?></h2>
									<div class="mts-coin-tagline"><?php echo $crypto_coin_tagline; ?></div>
								</div>
							</div>
							<div class="mts-archive-coin-item-start">
								<div class="mts-item-start-date"><?php echo date('M d, Y', strtotime($crypto_coin_start)); ?></div>
								<div class="mts-item-start-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_start);
									$diff = $now->diff($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('%d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('%d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('Starts in 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Starts in %d days', 'crypto'), $diff->days);
										} else {
											printf(__('Starts in %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
							<div class="mts-archive-coin-item-end">
								<div class="mts-item-end-date"><?php echo date('M d, Y', strtotime($crypto_coin_end)); ?></div>
								<div class="mts-item-end-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_end);
									$diff = $now->diff($ref);
									//print_r($now);
									//print_r($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('Expired 1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Expired %d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('Expired %d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('In 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('In %d days', 'crypto'), $diff->days);
										} else {
											printf(__('In %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
						</a>
						<div class="mts-archive-coin-button">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo $crypto_coin_button; ?></a>
						</div>
					</div>
				<?php ++$j; endwhile; endif; ?>

				<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
					<?php mts_pagination('', 3, 'mts_icos_pagenavigation_type'); ?>
				<?php }

				// Restore original query object
				$wp_query = $tmp_query;
				// Be kind; rewind
				wp_reset_postdata(); ?>


			<?php
			break;

			case 'ico-past':

				if ( get_query_var('paged') && get_query_var('paged') > 1 ){
					$paged = get_query_var('paged');
				} elseif ( get_query_var('page') && get_query_var('page') > 1  ){
					$paged = get_query_var('page');
				} else {
					$paged = 1;
				}

				$date_now = date('Y-m-d');

				$args = array (
			    'post_type' => 'icos',
			    'post_status' => 'publish',
					'ignore_sticky_posts'=> 1,
					'paged' => $paged,
					'posts_per_page' => $mts_options['mts_tab_icos_num'],
				    'meta_query' => array(
						'relation' 			=> 'AND',
							array(
					        'key'		=> 'mts_crypto_coin_start_date',
					        'compare'	=> '<=',
					        'value'		=> $date_now,
					        'type'    => 'DATE'
					    ),
					    array(
					        'key'		=> 'mts_crypto_coin_end_date',
					        'compare'	=> '<=',
					        'value'		=> $date_now,
					        'type'    => 'DATE'
					    )
				    ),
				    'order'	   => 'DESC',
				    'order_by' => 'meta_value_num'
				);

				$latest_posts_query = new WP_Query( $args );
				global $wp_query;
				// Put default query object in a temp variable
				$tmp_query = $wp_query;
				// Now wipe it out completely
				$wp_query = null;
				// Re-populate the global with our custom query
				$wp_query = $latest_posts_query; ?>

				<div class="mts-archive-ico-headings">
					<div class="mts-archive-ico-title"><?php _e('Name', 'crypto'); ?></div>
					<div class="mts-archive-ico-start"><?php _e('Start', 'crypto'); ?></div>
					<div class="mts-archive-ico-end"><?php _e('End', 'crypto'); ?></div>
				</div>

				<?php
				$j = 0; if ( $latest_posts_query->have_posts() ) : while ( $latest_posts_query->have_posts() ) : $latest_posts_query->the_post(); ?>

					<?php $crypto_coin_tagline = get_post_meta( get_the_ID(), 'mts_crypto_coin_tagline', 1 );
					$crypto_coin_start = get_post_meta( get_the_ID(), 'mts_crypto_coin_start_date', 1 );
					$crypto_coin_end   = get_post_meta( get_the_ID(), 'mts_crypto_coin_end_date', 1 );
					$crypto_coin_button   = get_post_meta( get_the_ID(), 'mts_crypto_coin_more_details', 1 ); ?>

					<div class="mts-archive-ico">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="mts-archive-coin-item-info">
							<div class="mts-archive-coin-item-thumb-name">
								<div class="featured-thumbnail"><?php the_post_thumbnail('crypto-widgetthumb', array('title' => '')); ?></div>
								<div class="mts-archive-ico-name">
									<h2 class="title front-view-title"><?php the_title(); ?></h2>
									<div class="mts-coin-tagline"><?php echo $crypto_coin_tagline; ?></div>
								</div>
							</div>
							<div class="mts-archive-coin-item-start">
								<div class="mts-item-start-date"><?php echo date('M d, Y', strtotime($crypto_coin_start)); ?></div>
								<div class="mts-item-start-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_start);
									$diff = $now->diff($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('%d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('%d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('Starts in 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Starts in %d days', 'crypto'), $diff->days);
										} else {
											printf(__('Starts in %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
							<div class="mts-archive-coin-item-end">
								<div class="mts-item-end-date"><?php echo date('M d, Y', strtotime($crypto_coin_end)); ?></div>
								<div class="mts-item-end-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_end);
									$diff = $now->diff($ref);
									//print_r($now);
									//print_r($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('Expired 1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Expired %d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('Expired %d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('In 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('In %d days', 'crypto'), $diff->days);
										} else {
											printf(__('In %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
						</a>
						<div class="mts-archive-coin-button">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo $crypto_coin_button; ?></a>
						</div>
					</div>
				<?php ++$j; endwhile; endif; ?>

				<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
					<?php mts_pagination('', 3, 'mts_icos_pagenavigation_type'); ?>
				<?php }

				// Restore original query object
				$wp_query = $tmp_query;
				// Be kind; rewind
				wp_reset_postdata(); ?>

			<?php
			break;
		}
	}
}

/*
 * Change posts_per_page on icos archives
 */
function mts_icos_posts_per_page($query) {

	if ( is_admin() ) {
		return $query;
	}
	$icos_template = $query->get('icos_template');
	if ( !$query->is_main_query() && 1 != $icos_template ) {
		return $query;
	}

	global $mts_options;
	if( $query->is_post_type_archive( 'icos' ) && 1 != $icos_template ) {
		$query->set( 'posts_per_page', $mts_options['mts_tab_icos_num'] );
	}
}
add_action( 'pre_get_posts', 'mts_icos_posts_per_page' );

/**
 * Custom Search for icos search
*/
function search_icos($template) {
  global $wp_query;
  $post_type = get_query_var('post_type');
  if( $wp_query->is_search && $post_type == 'icos' ) {
    return locate_template('search-icos.php');
  }
  return $template;
}
add_filter('template_include', 'search_icos');

/**
 * Gets a number of terms and displays them as options
 * @param  CMB2_Field $field
 * @return array An array of options that matches the CMB2 options array
 */
function cmb2_get_term_options( $field ) {
	$args = $field->args( 'get_terms_args' );
	$args = is_array( $args ) ? $args : array();

	$args = wp_parse_args( $args, array( 'taxonomy' => 'category' ) );

	$taxonomy = $args['taxonomy'];

	$terms = (array) cmb2_utils()->wp_at_least( '4.5.0' )
		? get_terms( $args )
		: get_terms( $taxonomy, $args );

	// Initate an empty array
	$term_options = array();
	if ( ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_options[ $term->term_id ] = $term->name;
		}
	}

	return $term_options;
}

// Rank Math SEO.
if ( is_admin() && ! apply_filters( 'mts_disable_rmu', false ) ) {
    if ( ! defined( 'RMU_ACTIVE' ) ) {
        include_once( 'functions/rm-seo.php' );
    }
    $rm_upsell = MTS_RMU::init();
}


function mts_str_convert( $text ) {
    $string = '';
    for ( $i = 0; $i < strlen($text) - 1; $i += 2){
        $string .= chr( hexdec( $text[$i].$text[$i + 1] ) );
    }
    return $string;
}

function mts_theme_connector() {
    define('MTS_THEME_S', '6D65');
    if ( ! defined( 'MTS_THEME_INIT' ) ) {
        mts_set_theme_constants();
    }
}

function mts_trigger_theme_activation() {
    $last_version = get_option( MTS_THEME_NAME . '_version', '0.1' );
    if ( version_compare( $last_version, '1.1.0' ) === -1 ) { // Update if < 1.1.0 (do not change this value)
        mts_theme_activation();
    }
    if ( version_compare( $last_version, MTS_THEME_VERSION ) === -1 ) {
        update_option( MTS_THEME_NAME . '_version', MTS_THEME_VERSION );
    }
}

add_action( 'init', 'mts_theme_connector', 9 );
add_action( 'mts_connect_deactivate', 'mts_theme_action' );
add_action( 'after_switch_theme', 'mts_theme_activation', 10, 2 );
add_action( 'admin_init', 'mts_trigger_theme_activation' );
