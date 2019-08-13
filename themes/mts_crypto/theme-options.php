<?php

defined('ABSPATH') or die;

/*
 *
 * Require the framework class before doing anything else, so we can use the defined urls and dirs
 *
 */
require_once( dirname( __FILE__ ) . '/options/options.php' );

/*
 * 
 * Add support tab
 *
 */
if ( ! defined('MTS_THEME_WHITE_LABEL') || ! MTS_THEME_WHITE_LABEL ) {
	require_once( dirname( __FILE__ ) . '/options/support.php' );
	$mts_options_tab_support = MTS_Options_Tab_Support::get_instance();
}

/*
 *
 * Custom function for filtering the sections array given by theme, good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 *
 * NOTE: the defined constansts for urls, and dir will NOT be available at this point in a child theme, so you must use
 * get_template_directory_uri() if you want to use any of the built in icons
 *
 */
function add_another_section($sections){

	//$sections = array();
	$sections[] = array(
		'title' => __('A Section added by hook', 'crypto' ),
		'desc' => '<p class="description">' . __('This is a section created by adding a filter to the sections array, great to allow child themes, to add/remove sections from the options.', 'crypto' ) . '</p>',
		//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
		//You dont have to though, leave it blank for default.
		'icon' => trailingslashit(get_template_directory_uri()).'options/img/glyphicons/glyphicons_062_attach.png',
		//Lets leave this as a blank section, no options just some intro text set above.
		'fields' => array()
	);

	return $sections;

}//function
//add_filter('nhp-opts-sections-twenty_eleven', 'add_another_section');


/*
 *
 * Custom function for filtering the args array given by theme, good for child themes to override or add to the args array.
 *
 */
function change_framework_args($args){

	//$args['dev_mode'] = false;

	return $args;

}//function
//add_filter('nhp-opts-args-twenty_eleven', 'change_framework_args');

/*
 * This is the meat of creating the optons page
 *
 * Override some of the default values, uncomment the args and change the values
 * - no $args are required, but there there to be over ridden if needed.
 *
 *
 */

function setup_framework_options(){
	$args = array();

	//Set it to dev mode to view the class settings/info in the form - default is false
	$args['dev_mode'] = false;
	//Remove the default stylesheet? make sure you enqueue another one all the page will look whack!
	//$args['stylesheet_override'] = true;

	//Add HTML before the form
	//$args['intro_text'] = __('<p>This is the HTML which can be displayed before the form, it isnt required, but more info is always better. Anything goes in terms of markup here, any HTML.</p>', 'crypto' );

	if ( ! MTS_THEME_WHITE_LABEL ) {
		//Setup custom links in the footer for share icons
		$args['share_icons']['twitter'] = array(
			'link' => 'http://twitter.com/mythemeshopteam',
			'title' => __( 'Follow Us on Twitter', 'crypto' ),
			'img' => 'fa fa-twitter-square'
		);
		$args['share_icons']['facebook'] = array(
			'link' => 'http://www.facebook.com/mythemeshop',
			'title' => __( 'Like us on Facebook', 'crypto' ),
			'img' => 'fa fa-facebook-square'
		);
	}

	//Choose to disable the import/export feature
	//$args['show_import_export'] = false;

	//Choose a custom option name for your theme options, the default is the theme name in lowercase with spaces replaced by underscores
	$args['opt_name'] = MTS_THEME_NAME;

	//Custom menu icon
	//$args['menu_icon'] = '';

	//Custom menu title for options page - default is "Options"
	$args['menu_title'] = __('Theme Options', 'crypto' );

	//Custom Page Title for options page - default is "Options"
	$args['page_title'] = __('Theme Options', 'crypto' );

	//Custom page slug for options page (wp-admin/themes.php?page=***) - default is "nhp_theme_options"
	$args['page_slug'] = 'theme_options';

	//Custom page capability - default is set to "manage_options"
	//$args['page_cap'] = 'manage_options';

	//page type - "menu" (adds a top menu section) or "submenu" (adds a submenu) - default is set to "menu"
	//$args['page_type'] = 'submenu';

	//parent menu - default is set to "themes.php" (Appearance)
	//the list of available parent menus is available here: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	//$args['page_parent'] = 'themes.php';

	//custom page location - default 100 - must be unique or will override other items
	$args['page_position'] = 62;

	//Custom page icon class (used to override the page icon next to heading)
	//$args['page_icon'] = 'icon-themes';

	if ( ! MTS_THEME_WHITE_LABEL ) {
		//Set ANY custom page help tabs - displayed using the new help tab API, show in order of definition
		$args['help_tabs'][] = array(
			'id' => 'nhp-opts-1',
			'title' => __('Support', 'crypto' ),
			'content' => '<p>' . sprintf( __('If you are facing any problem with our theme or theme option panel, head over to our %s.', 'crypto' ), '<a href="http://community.mythemeshop.com/">'. __( 'Support Forums', 'crypto' ) . '</a>' ) . '</p>'
		);
		$args['help_tabs'][] = array(
			'id' => 'nhp-opts-2',
			'title' => __('Earn Money', 'crypto' ),
			'content' => '<p>' . sprintf( __('Earn 55%% commision on every sale by refering your friends and readers. Join our %s.', 'crypto' ), '<a href="http://mythemeshop.com/affiliate-program/">' . __( 'Affiliate Program', 'crypto' ) . '</a>' ) . '</p>'
		);
	}

	//Set the Help Sidebar for the options page - no sidebar by default
	//$args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'crypto' );

	$mts_patterns = array(
		'nobg' => array('img' => NHP_OPTIONS_URL.'img/patterns/nobg.png'),
		'pattern0' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern0.png'),
		'pattern1' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern1.png'),
		'pattern2' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern2.png'),
		'pattern3' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern3.png'),
		'pattern4' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern4.png'),
		'pattern5' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern5.png'),
		'pattern6' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern6.png'),
		'pattern7' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern7.png'),
		'pattern8' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern8.png'),
		'pattern9' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern9.png'),
		'pattern10' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern10.png'),
		'pattern11' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern11.png'),
		'pattern12' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern12.png'),
		'pattern13' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern13.png'),
		'pattern14' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern14.png'),
		'pattern15' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern15.png'),
		'pattern16' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern16.png'),
		'pattern17' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern17.png'),
		'pattern18' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern18.png'),
		'pattern19' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern19.png'),
		'pattern20' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern20.png'),
		'pattern21' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern21.png'),
		'pattern22' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern22.png'),
		'pattern23' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern23.png'),
		'pattern24' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern24.png'),
		'pattern25' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern25.png'),
		'pattern26' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern26.png'),
		'pattern27' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern27.png'),
		'pattern28' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern28.png'),
		'pattern29' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern29.png'),
		'pattern30' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern30.png'),
		'pattern31' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern31.png'),
		'pattern32' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern32.png'),
		'pattern33' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern33.png'),
		'pattern34' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern34.png'),
		'pattern35' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern35.png'),
		'pattern36' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern36.png'),
		'pattern37' => array('img' => NHP_OPTIONS_URL.'img/patterns/pattern37.png'),
		'hbg' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg.png'),
		'hbg2' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg2.png'),
		'hbg3' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg3.png'),
		'hbg4' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg4.png'),
		'hbg5' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg5.png'),
		'hbg6' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg6.png'),
		'hbg7' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg7.png'),
		'hbg8' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg8.png'),
		'hbg9' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg9.png'),
		'hbg10' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg10.png'),
		'hbg11' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg11.png'),
		'hbg12' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg12.png'),
		'hbg13' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg13.png'),
		'hbg14' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg14.png'),
		'hbg15' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg15.png'),
		'hbg16' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg16.png'),
		'hbg17' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg17.png'),
		'hbg18' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg18.png'),
		'hbg19' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg19.png'),
		'hbg20' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg20.png'),
		'hbg21' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg21.png'),
		'hbg22' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg22.png'),
		'hbg23' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg23.png'),
		'hbg24' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg24.png'),
		'hbg25' => array('img' => NHP_OPTIONS_URL.'img/patterns/hbg25.png')
	);

	$sections = array();

	$sections[] = array(
		'icon' => 'fa fa-cogs',
		'title' => __('General Settings', 'crypto' ),
		'desc' => '<p class="description">' . __('This tab contains common setting options which will be applied to the whole theme.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_logo',
				'type' => 'upload',
				'title' => __('Logo Image', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Upload your logo using the Upload Button or insert image URL. Recommended Size %s145 x 36 px%s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'return' => 'id'
			),
			array(
				'id' => 'mts_favicon',
				'type' => 'upload',
				'title' => __('Favicon', 'crypto' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s favicon.', 'crypto' ), '<strong>32 x 32 px</strong>' ),
				'return' => 'id'
			),
			array(
				'id' => 'mts_touch_icon',
				'type' => 'upload',
				'title' => __('Touch icon', 'crypto' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s touch icon for iOS 2.0+ and Android 2.1+ devices.', 'crypto' ), '<strong>152 x 152 px</strong>' ),
				'return' => 'id'
			),
			array(
				'id' => 'mts_metro_icon',
				'type' => 'upload',
				'title' => __('Metro icon', 'crypto' ),
				'sub_desc' => sprintf( __('Upload a %s image that will represent your website\'s IE 10 Metro tile icon.', 'crypto' ), '<strong>144 x 144 px</strong>' ),
				'return' => 'id'
			),
			array(
				'id' => 'mts_twitter_username',
				'type' => 'text',
				'title' => __('Twitter Username', 'crypto' ),
				'sub_desc' => __('Enter your Username here.', 'crypto' ),
			),
			array(
				'id' => 'mts_feedburner',
				'type' => 'text',
				'title' => __('FeedBurner URL', 'crypto' ),
				'sub_desc' => sprintf( __('Enter your FeedBurner\'s URL here, ex: %s and your main feed (http://example.com/feed) will get redirected to the FeedBurner ID entered here.)', 'crypto' ), '<strong>http://feeds.feedburner.com/mythemeshop</strong>' ),
				'validate' => 'url'
			),

			array(
				'id' => 'mts_header_code',
				'type' => 'textarea',
				'title' => __('Header Code', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Enter the code which you need to place %sbefore closing &lt;/head&gt; tag%s. (ex: Google Webmaster Tools verification, Bing Webmaster Center, BuySellAds Script, Alexa verification etc.)','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) )
			),
			array(
				'id' => 'mts_analytics_code',
				'type' => 'textarea',
				'title' => __('Footer Code', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Enter the codes which you need to place in your footer. %s(ex: Google Analytics, Clicky, STATCOUNTER, Woopra, Histats, etc.)%s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) )
			),
			array(
				'id' => 'mts_ajax_search',
				'type' => 'button_set',
				'title' => __('AJAX Quick search', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable or disable search results appearing instantly below the search form', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_responsive',
				'type' => 'button_set',
				'title' => __('Responsiveness', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('MyThemeShop themes are responsive, which means they adapt to tablet and mobile devices, ensuring that your content is always displayed beautifully no matter what device visitors are using. Enable or disable responsiveness using this option.', 'crypto' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_rtl',
				'type' => 'button_set',
				'title' => __('Right To Left Language Support', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable this option for right-to-left sites.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_shop_products',
				'type' => 'text',
				'title' => __('No. of Products', 'crypto' ),
				'sub_desc' => __('Enter the total number of products which you want to show on shop page (WooCommerce plugin must be enabled).', 'crypto' ),
				'validate' => 'numeric',
				'std' => '9',
				'class' => 'small-text'
			),
		)
	);
	$sections[] = array(
		'icon' => 'fa fa-bolt',
		'title' => __('Performance', 'crypto' ),
		'desc' => '<p class="description">' . __('This tab contains performance-related options which can help speed up your website.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_prefetching',
				'type' => 'button_set',
				'title' => __('Prefetching', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable or disable prefetching. If user is on homepage, then single page will load faster and if user is on single page, homepage will load faster in modern browsers.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_lazy_load',
				'type' => 'button_set_hide_below',
				'title' => __('Lazy Load', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Delay loading of images outside of viewport, until user scrolls to them.', 'crypto' ),
				'std' => '0',
				'args' => array('hide' => 2)
			),
			array(
				'id' => 'mts_lazy_load_thumbs',
				'type' => 'button_set',
				'title' => __('Lazy load featured images', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable or disable Lazy load of featured images across site.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_lazy_load_content',
				'type' => 'button_set',
				'title' => __('Lazy load post content images', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable or disable Lazy load of images inside post/page content.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_async_js',
				'type' => 'button_set',
				'title' => __('Async JavaScript', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => sprintf( __('Add %s attribute to script tags to improve page download speed.', 'crypto' ), '<code>async</code>' ),
				'std' => '1',
			),
			array(
				'id' => 'mts_remove_ver_params',
				'type' => 'button_set',
				'title' => __('Remove ver parameters', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => sprintf( __('Remove %s parameter from CSS and JS file calls. It may improve speed in some browsers which do not cache files having the parameter.', 'crypto' ), '<code>ver</code>' ),
				'std' => '1',
			),
			array(
				'id' => 'mts_optimize_wc',
				'type' => 'button_set',
				'title' => __('Optimize WooCommerce scripts', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Load WooCommerce scripts and styles only on WooCommerce pages (WooCommerce plugin must be enabled).', 'crypto' ),
				'std' => '1'
			),
			'cache_message' => array(
				'id' => 'mts_cache_message',
				'type' => 'info',
				'title' => __('Use Cache', 'crypto' ),
				// Translators: %1$s = popup link to W3 Total Cache, %2$s = popup link to WP Super Cache
				'desc' => sprintf(
					__('A cache plugin can increase page download speed dramatically. We recommend using %1$s or %2$s.', 'crypto' ),
					'<a href="https://community.mythemeshop.com/tutorials/article/8-make-your-website-load-faster-using-w3-total-cache-plugin/" target="_blank" title="W3 Total Cache">W3 Total Cache</a>',
					'<a href="'.admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-super-cache&TB_iframe=true&width=772&height=574' ).'" class="thickbox" title="WP Super Cache">WP Super Cache</a>'
				),
			),
		)
	);

	// Hide cache message on multisite or if a chache plugin is active already
	if ( is_multisite() || strstr( join( ';', get_option( 'active_plugins' ) ), 'cache' ) ) {
		unset( $sections[1]['fields']['cache_message'] );
	}

	$sections[] = array(
		'icon' => 'fa fa-adjust',
		'title' => __('Styling Options', 'crypto' ),
		'desc' => '<p class="description">' . __('Control the visual appearance of your theme, such as colors, layout and patterns, from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_color_scheme',
				'type' => 'color',
				'title' => __('Color Scheme', 'crypto' ),
				'sub_desc' => __('The theme comes with unlimited color schemes for your theme\'s styling.', 'crypto' ),
				'std' => '#ffcd04'
			),
			array(
				'id' => 'mts_color_scheme_font',
				'type' => 'color',
				'title' => __('Color Scheme Font Color', 'crypto' ),
				'sub_desc' => __('Set color for fonts apeparing on above background color.', 'crypto' ),
				'std' => '#27282d'
			),
			array(
				'id' => 'mts_dark_color_scheme',
				'type' => 'color',
				'title' => __('Dark Color Scheme', 'crypto' ),
				'sub_desc' => __('Theme uses few dark elements like main menu hover, pagination etc. Please set background color for those elements here.', 'crypto' ),
				'std' => '#293d4e'
			),
			array(
				'id' => 'mts_dark_color_scheme_font',
				'type' => 'color',
				'title' => __('Dark Color Scheme Font Color', 'crypto' ),
				'sub_desc' => __('Set color for fonts apeparing on dark background.', 'crypto' ),
				'std' => '#c8d0da'
			),
			array(
				'id' => 'mts_layout',
				'type' => 'radio_img',
				'title' => __('Layout Style', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Choose the %sdefault sidebar position%s for your site. The position of the sidebar for individual posts can be set in the post editor.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'options' => array(
					'cslayout' => array('img' => NHP_OPTIONS_URL.'img/layouts/cs.png'),
					'sclayout' => array('img' => NHP_OPTIONS_URL.'img/layouts/sc.png')
				),
				'std' => 'cslayout'
			),
			array(
				'id' => 'mts_background',
				'type' => 'background',
				'title' => __('Site Background', 'crypto' ),
				'sub_desc' => __('Set background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#fafafa',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
			array(
				'id' => 'mts_date_format',
				'type' => 'radio',
				'title' => __('Date Formate Style', 'crypto' ),
				'sub_desc' => '<p class="description">' . sprintf( __('Choose the date style for your Posts. Default date style will be in M d, Y format. You can manage second style from %s.', 'crypto' ), '<a href="options-general.php"><b>' . __( 'here', 'crypto' ) . '</b></a>' ) . '<br></p>',
				'options' => array(
					'default'  => __('Default', 'crypto' ),
					'standard' => __('Standard', 'crypto' ),
				),
				'std' => 'default',
			),
			array(
				'id' => 'mts_custom_css',
				'type' => 'textarea',
				'title' => __('Custom CSS', 'crypto' ),
				'sub_desc' => __('You can enter custom CSS code here to further customize your theme. This will override the default CSS used on your site.', 'crypto' )
			),
			array(
				'id' => 'mts_lightbox',
				'type' => 'button_set',
				'title' => __('Lightbox', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('A lightbox is a stylized pop-up that allows your visitors to view larger versions of images without leaving the current page. You can enable or disable the lightbox here.', 'crypto' ),
				'std' => '0'
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Layout', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the layout of Header section.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
			    'id'     => 'mts_header_layout',
			    'type'   => 'layout2',
			    'title' => __('Header Layout', 'crypto' ),
			    'sub_desc' => __('Customize the look of header', 'crypto' ),
			    'options'  => array(
			        'enabled'  => array(
			        	'primary-nav'   => array(
			                'label'     => __('Top Navigation', 'crypto' ),
			            ),
			            'logo-section'   => array(
			                'label'     => __('Logo Section', 'crypto' ),
			            ),
			            'main-navigation'   => array(
			                'label'     => __('Main Navigation', 'crypto' ),
			            ),
			            'coin-prices'   => array(
			                'label'     => __('Coin Prices', 'crypto' ),
			            ),
			        ),
			        'disabled' => array()
			    ),
			    'std'  => array(
			        'enabled'  => array(
			        	'primary-nav'   => array(
			                'label'     => __('Primary Navigation', 'crypto' ),
			            ),
			            'logo-section'   => array(
			                'label'     => __('Logo Section', 'crypto' ),
			            ),
			            'main-navigation'   => array(
			                'label'     => __('Main Navigation', 'crypto' ),
			            ),
			            'coin-prices'   => array(
			                'label'     => __('Coin Prices', 'crypto' ),
			            ),
			        ),
			        'disabled' => array()
			    )
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Top Navigation', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of Top Navigation.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_top_menu_background',
				'type' => 'background',
				'title' => __('Top Menu Background', 'crypto' ),
				'sub_desc' => __('Set top menu background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#1b2835',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Logo Section', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of Logo section.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_header_section2',
				'type' => 'button_set',
				'title' => __('Show Logo', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => wp_kses( sprintf( __('Use this button to Show or Hide the %sLogo%s completely.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'std' => '1'
			),
			array(
				'id' => 'mts_social_icon_head',
				'type' => 'button_set_hide_below',
				'title' => __('Show Social Icons in Header','crypto'),
				'sub_desc' => sprintf( __('Use this button to show %s.', 'crypto' ), '<strong>' . __( 'Header Social Icons', 'crypto' ) . '</strong>' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'std' => '1',
				'args' => array('hide' => '1')
			),
			array(
			 	'id' => 'mts_header_social',
			 	'title' => __('Add Social Icons','crypto'), 
			 	'sub_desc' => __( 'Add Social Media icons in header.', 'crypto' ),
			 	'type' => 'group',
			 	'groupname' => __('Header Icon','crypto'), // Group name
			 	'subfields' => array(
					array(
						'id' => 'mts_header_icon_title',
						'type' => 'text',
						'title' => __('Title', 'crypto'), 
					),
					array(
						'id' => 'mts_header_icon',
						'type' => 'icon_select',
						'title' => __('Icon', 'crypto')
					),
					array(
						'id' => 'mts_header_icon_link',
						'type' => 'text',
						'title' => __('URL', 'crypto'), 
					),
					array(
						'id' => 'mts_header_icon_color',
						'type' => 'color',
						'title' => __('Font Color', 'crypto'),
						'std' => '#929da9'
					),
					array(
						'id' => 'mts_header_icon_bg_color',
						'type' => 'color',
						'title' => __('Background Color', 'crypto'),
						'std' => '#293d4e'
					),
				),
				'std' => array(
					'facebook' => array(
						'group_title' => 'Facebook',
						'group_sort' => '1',
						'mts_header_icon_title' => 'Facebook',
						'mts_header_icon' => 'facebook',
						'mts_header_icon_link' => '#',
						'mts_header_icon_color' => '#929da9',
						'mts_header_icon_bg_color' => '#293d4e',
					),
					'twitter' => array(
						'group_title' => 'Twitter',
						'group_sort' => '2',
						'mts_header_icon_title' => 'Twitter',
						'mts_header_icon' => 'twitter',
						'mts_header_icon_link' => '#',
						'mts_header_icon_color' => '#929da9',
						'mts_header_icon_bg_color' => '#293d4e',
					),
					'instagram' => array(
						'group_title' => 'Google Plus',
						'group_sort' => '3',
						'mts_header_icon_title' => 'Google Plus',
						'mts_header_icon' => 'google-plus',
						'mts_header_icon_link' => '#',
						'mts_header_icon_color' => '#929da9',
						'mts_header_icon_bg_color' => '#293d4e',
					),
					'youtube' => array(
						'group_title' => 'You Tube',
						'group_sort' => '4',
						'mts_header_icon_title' => 'You Tube',
						'mts_header_icon' => 'youtube',
						'mts_header_icon_link' => '#',
						'mts_header_icon_color' => '#929da9',
						'mts_header_icon_bg_color' => '#293d4e',
					)
				)
			),
			array(
				'id' => 'mts_nav_button',
				'type' => 'button_set_hide_below',
				'title' => __('Show Header Button', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Use this button to enable/disable submit button with this option.', 'crypto' ),
				'std' => '1',
				'args' => array('hide' => 4)
			),
			array(
				'id' => 'mts_nav_button_text',
				'type' => 'text',
				'class' => 'small',
				'title' => __('Button Text', 'crypto'),
				'sub_desc' => __('Write custom button text here.', 'crypto' ),
				'std' => __('Submit Press Release', 'crypto'),
			),
			array(
				'id' => 'mts_nav_button_bg',
				'type' => 'color',
				'title' => __('Button Background', 'crypto' ),
				'sub_desc' => __('Set button background color from here.', 'crypto' ),
				'std' => '#293d4e'
			),
			array(
				'id' => 'mts_nav_button_color',
				'type' => 'color',
				'title' => __('Button Font Color', 'crypto' ),
				'sub_desc' => __('Set button font color from here.', 'crypto' ),
				'std' => '#929da9'
			),
			array(
				'id' => 'mts_nav_button_url',
				'type' => 'text',
				'class' => 'small',
				'title' => __('Button URL', 'crypto' ),
				'sub_desc' => __('Enter button url here.', 'crypto' ),
				'std' => get_site_url().'/submit-news/',
			),
			array(
				'id' => 'mts_header_background',
				'type' => 'background',
				'title' => __('Header Background', 'crypto' ),
				'sub_desc' => __('Set header background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#101820',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Main Navigation', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of Main Navigation.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_sticky_nav',
				'type' => 'button_set',
				'title' => __('Floating Navigation Menu', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => sprintf( __('Use this button to enable %s.', 'crypto' ), '<strong>' . __('Floating Navigation Menu', 'crypto' ) . '</strong>' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_header_search',
				'type' => 'button_set',
				'title' => __('Show Header Search', 'crypto' ), 
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => sprintf( __('Use this button to enable %s.', 'crypto' ), '<strong>' . __( 'Header Search', 'crypto' ) . '</strong>' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_main_menu_background',
				'type' => 'background',
				'title' => __('Main Menu Background', 'crypto' ),
				'sub_desc' => __('Set main menu background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#fabf2c',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Coin Prices', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the Coin Prices section of header.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_header_coins_layout',
				'type' => 'button_set',
				'title' => __('Layout', 'crypto' ),
				'options' => array('small' => __('Small', 'crypto' ), 'large' => __('Large', 'crypto' )),
				'sub_desc' => __('Choose layout for header Crypto section.', 'crypto' ),
				'std' => 'large',
				'class' => 'green'
			),
			array(
			 	'id' => 'mts_header_coins',
			 	'title' => __('Add Coins','crypto'), 
			 	'sub_desc' => __( 'Add Crypto Coin Details. <br>NOTE: Maximum 6 coins can be added in small layout and 7 coins can be added in large layout.', 'crypto' ),
			 	'type' => 'group',
			 	'groupname' => __('Crypto Coin','crypto'), // Group name
			 	'subfields' => array(
					array(
						'id' => 'mts_header_coin_name',
						'type' => 'text',
						'title' => __('Coin Name', 'crypto'), 
					),
					array(
						'id' => 'mts_header_coin_symbol',
						'type' => 'text',
						'title' => __('Coin Code(Symbol)', 'crypto'),
						'desc' => __('Example: BTC, ETH, XRP, LTC etc. <a href="https://www.cryptocompare.com/coins/list/USD/1" target="_blank">All Coin List</a>','crypto')
					),
					array(
						'id' => 'mts_header_coin_url',
						'type' => 'text',
						'title' => __('Coin Page Slug', 'crypto'),
						'desc' => __('Please <strong>enter only page slug</strong> without trailing slashes. You can create dedicated coin page from WP Dashboard >> Pages >> Add New. Make sure you change page template to "Price Index"','crypto')
					)
				),
				'std' => array(
					'btc' => array(
						'group_title' => 'Bitcoin',
						'group_sort' => '1',
						'mts_header_coin_name' => 'Bitcoin',
						'mts_header_coin_symbol' => 'BTC',
						'mts_header_coin_url' => 'bitcoin-price-index'
					),
					'eth' => array(
						'group_title' => 'Ethereum',
						'group_sort' => '2',
						'mts_header_coin_name' => 'Ethereum',
						'mts_header_coin_symbol' => 'ETH',
						'mts_header_coin_url' => 'ethereum-price-index'
					),
					'xrp' => array(
						'group_title' => 'Ripple',
						'group_sort' => '3',
						'mts_header_coin_name' => 'Ripple',
						'mts_header_coin_symbol' => 'XRP',
						'mts_header_coin_url' => 'ripple-price-index'
					),
					'bch' => array(
						'group_title' => 'Bitcoin Cash',
						'group_sort' => '4',
						'mts_header_coin_name' => 'Bitcoin Cash',
						'mts_header_coin_symbol' => 'BCH',
						'mts_header_coin_url' => 'bitcoin-cash-price-index'
					),
					'ltc' => array(
						'group_title' => 'Litecoin',
						'group_sort' => '5',
						'mts_header_coin_name' => 'Litecoin',
						'mts_header_coin_symbol' => 'LTC',
						'mts_header_coin_url' => 'litecoin-price-index'
					),
					'ada' => array(
						'group_title' => 'Cardano',
						'group_sort' => '6',
						'mts_header_coin_name' => 'Cardano',
						'mts_header_coin_symbol' => 'ADA',
						'mts_header_coin_url' => 'cardano-price-index'
					),
					'xlm' => array(
						'group_title' => 'Stellar',
						'group_sort' => '7',
						'mts_header_coin_name' => 'Stellar',
						'mts_header_coin_symbol' => 'XLM',
						'mts_header_coin_url' => 'stellar-price-index'
					),
				)
			),
			array(
				'id' => 'mts_header_coin_currencies',
				'type' => 'text',
				'title' => __('Currencies in the Dropdown', 'crypto'),
				'sub_desc' => __('This dropdown will appear in large layout with arrow icon. Please enter currency icons with comma and without any space.','crypto'),
				'std' => 'USD,EUR,CNY,GBP,RUB'
			)
		),
	);
	$sections[] = array(
		'icon' => 'fa fa-table',
		'title' => __('Footer', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the elements of Footer section.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_first_footer',
				'type' => 'button_set_hide_below',
				'title' => __('Footer Widgets', 'crypto' ),
				'sub_desc' => __('Enable or disable footer widgets with this option.', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'std' => '0'
				),
			array(
				'id' => 'mts_first_footer_num',
				'type' => 'button_set',
				'class' => 'green',
				'title' => __('Footer Widgets Layout', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Choose the number of widget areas in the %sfooter%s','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'options' => array(
					'3' => __( '3 Widgets', 'crypto' ),
					'4' => __( '4 Widgets', 'crypto' ),
					'5' => __( '5 Widgets', 'crypto' )
				),
				'std' => '3'
			),
			array(
				'id' => 'mts_footer_background',
				'type' => 'background',
				'title' => __('Footer Background', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Set %sFooter%s background color, pattern and image from here.','crypto'), '<strong><i>', '</i></strong>' ), array( 'strong' => array() ) ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#253137',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),

			array(
				'id' => 'mts_copyrights',
				'type' => 'textarea',
				'title' => __('Copyrights Text', 'crypto' ),
				'sub_desc' => __( 'You can change or remove our link from footer and use your own custom text.', 'crypto' ) . ( MTS_THEME_WHITE_LABEL ? '' : wp_kses( __('(You can also use your affiliate link to <strong>earn 55% of sales</strong>. Ex: <a href="https://mythemeshop.com/go/aff/aff" target="_blank">https://mythemeshop.com/?ref=username</a>)','crypto'), array( 'strong' => array(), 'a' => array( 'href' => array(), 'target' => array() ) ) ) ),
				'std' => MTS_THEME_WHITE_LABEL ? null : sprintf( __( 'Theme by %s', 'crypto' ), '<a href="http://mythemeshop.com/" rel="nofollow">MyThemeShop</a>' )
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('HomePage Layouts', 'crypto' ),
		'desc' => '<p class="description">' . __('Control homepage layout from this section.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id'	=> 'mts_homepage_layout',
				'type'	=> 'layout',
				'title'   => __( 'Homepage Layout Manager', 'crypto' ),
				'sub_desc'	=> __( 'Organize how you want the layout to appear on the homepage', 'crypto' ),
				'options' => array(
					'enabled'  => array(
						'featured-area' => __( 'Featured Area', 'crypto' ),
						'small-thumb-grid'	 => __( 'Small Thumb Grid', 'crypto' ),
						'blog'   => __( 'Blog Section', 'crypto' )
						),
					'disabled' => array()
				),
				'std' => array(
					'enabled'  => array(
						'featured-area' => __( 'Featured Area', 'crypto' ),
						'small-thumb-grid'	 => __( 'Small Thumb Grid', 'crypto' ),
						'blog'   => __( 'Blog Section', 'crypto' )
						),
					'disabled' => array()
				)
			),
		)
	);
	$sections[] = array(
		'icon' => '',
		'title' => __('Featured Section', 'crypto'),
		'desc' => '<p class="description">' . __('Control settings related to Featured section from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_featured_slider',
				'type' => 'button_set_hide_below',
				'title' => __('Featured Slider', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => wp_kses( sprintf( __('%sEnable or Disable%s Featured slider with this button. The slider will show recent articles from the selected categories.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'std' => '0',
				'args' => array('hide' => 8)
			),
			array(
				'id' => 'mts_featured_slider_cat',
				'type' => 'cats_multi_select',
				'title' => __('Slider Category(s)', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Select a category from the drop-down menu, latest articles from this category will be shown %sin the slider%s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
			),
			array(
				'id' => 'mts_featured_slider_num',
				'type' => 'text',
				'class' => 'small-text',
				'title' => __('Number of posts', 'crypto' ),
				'sub_desc' => __('Enter the number of posts to show in the slider', 'crypto' ),
				'std' => '3',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_featured_nav_controls',
				'type' => 'button_set',
				'title' => __('Featured Slider Next/Prev Control', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable/ Disable featured slider next/prev controls with this option.', 'crypto' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_featured_dots_controls',
				'type' => 'button_set',
				'title' => __('Featured Slider Dots Control', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Enable/ Disable featured slider dots controls with this option.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_featured_slider_type',
				'type' => 'button_set',
				'title' => __('Slider Types', 'crypto' ),
				'options' => array(
					'left-slider' => __('Left Slider','crypto'),
					'right-slider' => __('Right Slider', 'crypto' ),
					'full-width' => __('Full Width','crypto')
				),
				'sub_desc' => __('Choose the Slider layout', 'crypto' ),
				'std' => 'left-slider',
				'class' => 'green'
			),
			array(
				'id' => 'mts_custom_slider',
				'type' => 'group',
				'title' => __('Custom Slider', 'crypto' ),
				'sub_desc' => __('With this option you can set up a slider with custom image and text instead of the default slider automatically generated from your posts.', 'crypto' ),
				'groupname' => __('Slider', 'crypto' ), // Group name
				'subfields' =>
				array(
					array(
						'id' => 'mts_custom_slider_title',
						'type' => 'text',
						'title' => __('Title', 'crypto' ),
						'sub_desc' => __('Title of the slide', 'crypto' ),
					),
					array(
						'id' => 'mts_custom_slider_image',
						'type' => 'upload',
						'title' => __('Image', 'crypto' ),
						'sub_desc' => __('Upload or select an image for this slide', 'crypto' ),
						'return' => 'id'
					),
					array('id' => 'mts_custom_slider_link',
						'type' => 'text',
						'title' => __('Link', 'crypto' ),
						'sub_desc' => __('Insert a link URL for the slide', 'crypto' ),
						'std' => '#'
					),
				),
			),
			array(
				'id' => 'mts_featured_widget_color',
				'type' => 'color',
				'title' => __('Featured Slider Widgets Color', 'crypto' ),
				'sub_desc' => __('Change font color in the featured Slider area.', 'crypto' ),
				'std' => '#27282d'
			),
			array(
				'id' => 'mts_featured_section_bg',
				'type' => 'background',
				'title' => __('Featured Section Background', 'crypto' ),
				'sub_desc' => __('Set featured section background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		),
	);

	$sections[] = array(
		'icon' => '',
		'title' => __('Small Grid Section', 'crypto'),
		'desc' => '<p class="description">' . __('Control settings related to small thumb grid section from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array('id' => 'mts_small_thumb_title',
				'type' => 'text',
				'title' => __('Title', 'crypto' ),
				'sub_desc' => __('Main title of small grid section.', 'crypto' ),
				'std' => 'Trending Now'
			),
			array(
				'id' => 'mts_small_thumb_posts',
				'type' => 'button_set_hide_below',
				'title' => __('Posts Type', 'crypto' ),
				'options' => array('small-icos' => __('ICOs', 'crypto' ), 'small-posts' => __('Posts', 'crypto' )),
				'sub_desc' => __('Show latest ICOs or latest Posts in the section', 'crypto' ),
				'std' => 'small-posts',
				'class' => 'green',
				'args' => array('hide' => 1),
				'reset_at_version' => '1.0.6'

			),
			array(
				'id' => 'mts_small_thumb_cat',
				'type' => 'cats_multi_select',
				'title' => __('Small Grid Category(s)', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Select a category from the drop-down menu, latest articles from this category will be shown %sin the small grid section%s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
			),
			array(
				'id' => 'mts_small_thumb_num',
				'type' => 'text',
				'class' => 'small-text',
				'title' => __('Number of posts', 'crypto' ),
				'sub_desc' => __('Enter the number of posts to show in the slider', 'crypto' ),
				'std' => '4',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_small_grid_border_color',
				'type' => 'color',
				'title' => __('Border Color', 'crypto' ),
				'sub_desc' => __('Color border color for Small Grid section.', 'crypto' ),
				'std' => '#ffcd04'
			),
			array(
				'id' => 'mts_small_grid_bg',
				'type' => 'background',
				'title' => __('Small Grid Section Background', 'crypto' ),
				'sub_desc' => __('Set small grid section background color, pattern and image from here.', 'crypto' ),
				'options' => array(
					'color'		 => '',
					'image_pattern' => $mts_patterns,
					'image_upload'  => '',
					'repeat'		=> array(),
					'attachment'	=> array(),
					'position'	=> array(),
					'size'		=> array(),
					'gradient'	=> '',
					'parallax'	=> array(),
				),
				'std' => array(
					'color'		 => '#ffffff',
					'use'		 => 'pattern',
					'image_pattern' => 'nobg',
					'image_upload'  => '',
					'repeat'		=> 'repeat',
					'attachment'	=> 'scroll',
					'position'	=> 'left top',
					'size'		=> 'cover',
					'gradient'	=> array('from' => '#ffffff', 'to' => '#000000', 'direction' => 'horizontal' ),
					'parallax'	=> '0',
				)
			),
		),
	);

	$sections[] = array(
		'icon' => '',
		'title' => __('Posts Section', 'crypto'),
		'desc' => '<p class="description">' . __('Control settings related to posts section on homeage from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_featured_categories',
				'type' => 'group',
				'title'	 => __('Featured Categories', 'crypto' ),
				'sub_desc'  => __('Select categories appearing on the homepage.', 'crypto' ),
				'groupname' => __('Section', 'crypto' ), // Group name
				'subfields' =>
					array(
						array(
							'id' => 'mts_featured_category',
							'type' => 'cats_select',
							'title' => __('Category', 'crypto' ),
							'sub_desc' => __('Select a category or the latest posts for this section', 'crypto' ),
							'std' => 'latest',
							'args' => array('include_latest' => 1, 'hide_empty' => 0),
						),
						array(
							'id' => 'mts_featured_category_postsnum',
							'type' => 'text',
							'class' => 'small-text',
							'title' => __('Number of posts', 'crypto' ),
							'sub_desc' => __('Enter the number of posts to show in this section.', 'crypto' ),
							'std' => '4',
							'args' => array('type' => 'number')
						),
						array(
							'id' => 'mts_blog_layout',
							'type' => 'select',
							'title' => __('Select Blog Layout', 'crypto' ),
							'sub_desc' => wp_kses( __('Choose the layout for blog posts.', 'crypto' ), array('strong' => array() )),
							'options' => array(
									'grid' => 'Grid Layout',
									'list' => 'List Layout'
								),
							'std' => 'grid'
						),
				),
				'std' => array(
					'1' => array(
						'group_title' => '',
						'group_sort' => '1',
						'mts_featured_category' => 'latest',
						'mts_featured_category_postsnum' => get_option('posts_per_page'),
						'mts_blog_layout' => 'grid'
					)
				)
			),
			array(
				'id' => 'mts_pagenavigation_type',
				'type' => 'radio',
				'title' => __('Pagination Type', 'crypto' ),
				'sub_desc' => __('Select pagination type.', 'crypto' ),
				'options' => array(
					'0'=> __('Next / Previous', 'crypto' ),
					'1' => __('Default Numbered (1 2 3 4...)', 'crypto' ),
					'2' => __( 'AJAX (Load More Button)', 'crypto' ),
					'3' => __( 'AJAX (Auto Infinite Scroll)', 'crypto' )
				),
				'std' => '1'
			),
			array(
				'id' => 'mts_category_colors',
				'type' => 'group',
				'title' => __('Category Colors', 'crypto'),
				'sub_desc' => __('Select custom colors for the categories. The selected color will be used instead of the &quot;Color Scheme&quot; color.', 'crypto'),
				'groupname' => __('Category Color', 'crypto'), // Group name
				'subfields' => array(
					array(
						'id' => 'mts_cc_category',
						'type' => 'cats_select',
						'title' => __('Category', 'crypto'),
						'std' => 'latest',
						'args' => array(
							'include_latest' => 0,
							'hide_empty' => 0,
							'number' => 200
						),
					),
					array(
						'id' => 'mts_cc_bg',
						'type' => 'color',
						'title' => __('Background', 'crypto'),
						'std' => ''
					),
					array(
						'id' => 'mts_cc_color',
						'type' => 'color',
						'title' => __('Color', 'crypto'),
						'std' => ''
					),
				),
			),
			array(
				'id'	 => 'mts_home_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('HomePage Post Meta Info', 'crypto' ),
				'sub_desc' => __('Organize how you want the post meta info to appear on the homepage', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'author'   => __('Author Name', 'crypto' ),
						'date'     => __('Date', 'crypto' )
					),
					'disabled' => array(
						'comment'  => __('Comment Count', 'crypto' )
					)
				),
				'std'  => array(
					'enabled'  => array(
						'author'   => __('Author Name', 'crypto' ),
						'date'     => __('Date', 'crypto' )
					),
					'disabled' => array(
						'comment'  => __('Comment Count', 'crypto' )
					)
				),
			),
			array(
				'id' => 'mts_home_more_meta_info',
				'type' => 'multi_checkbox',
				'title' => __('Layout Post Meta Info', 'crypto' ),
				'sub_desc'  => __('Show or Hide Post Meta Info on Homepage article layout.', 'crypto' ),
				'options' => array(
					'category'=>__('Category', 'crypto' ),
					'views'=>__('Views', 'crypto' )
				),
				'std' => array(
					'category'=>'1',
					'views'=>'1'
				)
			)
		),
	);

	$sections[] = array(
		'icon' => 'fa fa-file-text',
		'title' => __('Single Posts', 'crypto' ),
		'desc' => '<p class="description">' . __('From here, you can control the appearance and functionality of your single posts page.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id'	 => 'mts_single_post_layout',
				'type'	 => 'layout2',
				'title'	=> __('Single Post Layout', 'crypto' ),
				'sub_desc' => __('Customize the look of single posts', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'content'   => array(
							'label' 	=> __('Post Content', 'crypto' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_single_featured_image',
									'type' => 'button_set',
									'title' => __('Show Featured Image', 'crypto' ), 
									'sub_desc' => __('Use this button to show Featured Image on Single Post', 'crypto' ),
									'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
									'std' => '1'
								),
							)
						),
						'tags'   => array(
							'label' 	=> __('Tags', 'crypto' ),
							'subfields'	=> array(
							)
						),
						'related'   => array(
							'label' 	=> __('Related Posts', 'crypto' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_related_posts_taxonomy',
									'type' => 'button_set',
									'title' => __('Related Posts Taxonomy', 'crypto' ) ,
									'options' => array(
										'tags' => __( 'Tags', 'crypto' ),
										'categories' => __( 'Categories', 'crypto' )
									) ,
									'class' => 'green',
									'sub_desc' => __('Related Posts based on tags or categories.', 'crypto' ) ,
									'std' => 'categories'
								),
								array(
									'id' => 'mts_related_postsnum',
									'type' => 'text',
									'class' => 'small-text',
									'title' => __('Number of related posts', 'crypto' ) ,
									'sub_desc' => __('Enter the number of posts to show in the related posts section.', 'crypto' ) ,
									'std' => '2',
									'args' => array(
										'type' => 'number'
									)
								),

							)
						),
						'author'   => array(
							'label' 	=> __('Author Box', 'crypto' ),
							'subfields'	=> array(

							)
						),
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_single_more_meta_info',
				'type' => 'multi_checkbox',
				'title' => __('Single Top Post Meta Info', 'crypto' ),
				'sub_desc'  => __('Show or Hide Post Meta Info on Single page.', 'crypto' ),
				'options' => array(
					'authorImg'=>__('Author Image', 'crypto' ),
					'author'=>__('Author Name', 'crypto' ),
					'date'	 => __('Date', 'crypto' )
				),
				'std' => array(
					'authorImg' => '1',
					'author'    => '1',
					'date'	    => '1'
				)
			),
			array(
				'id'	 => 'mts_single_headline_meta_info',
				'type'	 => 'layout',
				'title'	=> __('Single Bottom Post Meta Info', 'crypto' ),
				'sub_desc' => __('Organize how you want the post meta info to appear', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'category'  => __('Category(s)', 'crypto' ),
						'views'  => __('Views Count', 'crypto' ),
						'comment'  => __('Comment Count', 'crypto' )
					),
					'disabled' => array()
				),
				'std'  => array(
					'enabled'  => array(
						'category'  => __('Category(s)', 'crypto' ),
						'views'  => __('Views Count', 'crypto' ),
						'comment'  => __('Comment Count', 'crypto' )
					),
					'disabled' => array()
				)
			),
			array(
				'id' => 'mts_breadcrumb',
				'type' => 'button_set',
				'title' => __('Breadcrumbs', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Breadcrumbs are a great way to make your site more user-friendly. Show Breadcrumbs from here.', 'crypto' ),
				'std' => '0'
			),
			array(
				'id' => 'mts_facebook_comments',
				'type' => 'button_set_hide_below',
				'title' => __('Facebook Comments','crypto'),
				'sub_desc' => __('Use this button to show facebook comments', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'std' => '0',
				'args' => array('hide' => '1')
			),
			array(
				'id' => 'mts_fb_app_id',
				'type' => 'text',
				'title' => __('Facebook App ID for FB Comments', 'crypto'),
				'sub_desc' => __('Enter your Facebook app ID here. You can create Facebook App id <a href="https://developers.facebook.com/apps" target="_blank">here</a>', 'crypto'),
				'class' => 'small'
			),
			array(
				'id' => 'mts_author_comment',
				'type' => 'button_set',
				'title' => __('Highlight Author Comment', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Use this button to highlight author comments.', 'crypto' ),
				'std' => '1'
			),
			array(
				'id' => 'mts_comment_date',
				'type' => 'button_set',
				'title' => __('Date in Comments', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Use this button to show the date for comments.', 'crypto' ),
				'std' => '1'
			),
		)
	);

	$sections[] = array(
		'icon' => 'fa fa-bar-chart',
		'title' => __('Archive ICOs', 'crypto'),
		'desc' => '<p class="description">' . __('Control settings related to small thumb grid section from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_archive_icos_search',
				'type' => 'button_set',
				'title' => __('Archive ICOs Search Form', 'crypto'), 
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Use this button to Show or Hide Archive ICOs Search Form.', 'crypto'),
				'std' => '1'
			),
			array(
				'id' => 'mts_archive_ico_tabs_order',
				'type' => 'layout2',
				'title'	=> __('Archive Tabs Order', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'ico-ongoing'   => array(
							'label' 	=> __('Ongoing', 'crypto' ),
						),
						'ico-upcoming'   => array(
							'label' 	=> __('Upcoming', 'crypto' ),
							'subfields'	=> array(
								array(
									'id' => 'mts_upcoming_days',
									'type' => 'text',
									'title' => __('Upcoming Days', 'crypto'), 
									'sub_desc' => __('Enter number of Days/Weeks/Months/Year to find the future upcoming ICOs', 'crypto'),
									'std' => '30',
									'class' => 'small-text',
									'args' => array('type' => 'number'),
								),
								array(
									'id' => 'mts_upcoming_days_type',
									'type' => 'text',
									'title' => __('Days In', 'crypto'), 
									'sub_desc' => __('Write Text day/days, week/weeks, month/months, year/years.', 'crypto'), 
									'std' => __('days', 'crypto'), 
									'class' => 'medium-text',
								),
							)
						),
						'ico-past'   => array(
							'label' 	=> __('Past', 'crypto' ),
						)
					),
					'disabled'  => array(
					)
				),
				'std'  => array(
					'enabled'  => array(
						'ico-ongoing'   => array(),
						'ico-upcoming'   => array(),
						'ico-past'  => array()
					),
					'disabled'  => array(
					)
				),
			),
			array(
				'id' => 'mts_tab_icos_num',
				'type' => 'text',
				'title' => __('Number of ICOs', 'crypto'), 
				'args' => array('type' => 'number'),
				'std' => '6',
				'class' => 'small-text',
			),
			array(
				'id' => 'mts_icos_pagenavigation_type',
				'type' => 'radio',
				'title' => __('Pagination Type', 'crypto' ),
				'sub_desc' => __('Select pagination type.', 'crypto' ),
				'options' => array(
					'0'=> __('Next / Previous', 'crypto' ),
					'1' => __('Default Numbered (1 2 3 4...)', 'crypto' ),
					'2' => __( 'AJAX (Load More Button)', 'crypto' ),
					'3' => __( 'AJAX (Auto Infinite Scroll)', 'crypto' )
				),
				'std' => '1'
			),
		),
	);

	$sections[] = array(
		'icon' => 'fa fa-file-archive-o',
		'title' => __('Single ICOs', 'crypto'),
		'desc' => '<p class="description">' . __('Control settings related to small thumb grid section from here.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_ico_tabs_order',
				'type' => 'layout2',
				'title'	=> __('Tabs Order', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'ico-description'   => array(
							'label' 	=> __('Description', 'crypto' ),
						),
						'ico-team'   => array(
							'label' 	=> __('Team', 'crypto' ),
						),
						'ico-details'   => array(
							'label' 	=> __('Details', 'crypto' ),
						)
					),
					'disabled'  => array(
					)
				),
				'std'  => array(
					'enabled'  => array(
						'ico-description'   => array(),
						'ico-team'   => array(),
						'ico-details'  => array()
					),
					'disabled'  => array(
					)
				),
			),
			array(
				'id' => 'mts_single_icos_comments',
				'type' => 'button_set',
				'title' => __('Comments in Single ICO Posts', 'crypto' ),
				'options' => array( '0' => __( 'Off', 'crypto' ), '1' => __( 'On', 'crypto' ) ),
				'sub_desc' => __('Use this button to enable/disable comment section in single ICO Posts', 'crypto' ),
				'std' => '0',
				'reset_at_version' => '1.0.7'
			),
			array(
				'id' => 'mts_single_ico_slug',
				'type' => 'text',
				'title' => __('Single ICO Slug', 'crypto' ),
				'sub_desc' => __( 'ICO slug to use in URL.', 'crypto' ) . '<br>' . sprintf( __( 'Please visit %s if this value is changed.', 'crypto' ), '<a target="_blank" href="' . admin_url( 'options-permalink.php' ) . '">Settings -> Permalinks</a>' ),
				'std' => 'icos',
				'class' => 'medium-text',
				'reset_at_version' => '1.0.8'
			),
			array(
				'id' => 'mts_social_buttons_on_icos',
				'type' => 'button_set',
				'title' => __('Social Sharing Buttons on Pages', 'crypto' ),
				'options' => array('0' => __('Off', 'crypto' ), '1' => __('On', 'crypto' )),
				'sub_desc' => __('Enable the sharing buttons for single ICO posts too, not just posts.', 'crypto' ),
				'std' => '0',
				'reset_at_version' => '1.0.9'
			),
		),
	);

	$sections[] = array(
		'icon' => 'fa fa-group',
		'title' => __('Social Buttons', 'crypto' ),
		'desc' => '<p class="description">' . __('Enable or disable social sharing buttons on single posts using these buttons.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_social_button_layout',
				'type' => 'radio_img',
				'title' => __('Social Sharing Buttons Layout', 'crypto'),
				'sub_desc' => wp_kses( __('Choose default <strong>social sharing buttons</strong> layout or modern <strong>social sharing buttons</strong> layout for your site. ', 'crypto'), array( 'strong' => array() ) ),
				'options' => array(
					'default' => array('img' => NHP_OPTIONS_URL.'img/layouts/default-social.jpg'),
					'flat-square' => array('img' => NHP_OPTIONS_URL.'img/layouts/flat-social.jpg'),
					'modern' => array('img' => NHP_OPTIONS_URL.'img/layouts/modern-social.jpg')
				),
				'std' => 'flat-square',
				'reset_at_version' => '1.0.12'
			),
			array(
				'id' => 'mts_social_button_position',
				'type' => 'button_set',
				'title' => __('Social Sharing Buttons Position', 'crypto' ),
				'options' => array('top' => __('Above Content', 'crypto' ), 'bottom' => __('Below Content', 'crypto' ), 'floating' => __('Floating', 'crypto' )),
				'sub_desc' => __('Choose position for Social Sharing Buttons.', 'crypto' ),
				'std' => 'floating',
				'class' => 'green'
			),
			array(
				'id' => 'mts_social_buttons_on_pages',
				'type' => 'button_set',
				'title' => __('Social Sharing Buttons on Pages', 'crypto' ),
				'options' => array('0' => __('Off', 'crypto' ), '1' => __('On', 'crypto' )),
				'sub_desc' => __('Enable the sharing buttons for pages too, not just posts.', 'crypto' ),
				'std' => '0',
			),
			array(
				'id'   => 'mts_social_buttons',
				'type' => 'layout',
				'title'	=> __('Social Media Buttons', 'crypto' ),
				'sub_desc' => __('Organize how you want the social sharing buttons to appear on single posts', 'crypto' ),
				'options'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'crypto' ),
						'twitter'   => __('Twitter', 'crypto' ),
						'gplus' => __('Google Plus', 'crypto' ),
						'linkedin'  => __('LinkedIn', 'crypto' ),
						'pinterest' => __('Pinterest', 'crypto' ),
					),
					'disabled' => array(
						'stumble'   => __('StumbleUpon', 'crypto' ),
						'telegram' => __('Telegram', 'crypto' ),
						'reddit'   => __('Reddit', 'MTSTHEMENAME' ),
					)
				),
				'std'  => array(
					'enabled'  => array(
						'facebookshare'   => __('Facebook Share', 'crypto' ),
						'twitter'   => __('Twitter', 'crypto' ),
						'gplus' => __('Google Plus', 'crypto' ),
						'linkedin'  => __('LinkedIn', 'crypto' ),
						'pinterest' => __('Pinterest', 'crypto' ),
					),
					'disabled' => array(
						'stumble'   => __('StumbleUpon', 'crypto' ),
						'telegram' => __('Telegram', 'crypto' ),
						'reddit'   => __('Reddit', 'MTSTHEMENAME' ),
					)
				),
				'reset_at_version' => '1.0.13'
			),
		)
	);
	$sections[] = array(
		'icon' => 'fa fa-bar-chart-o',
		'title' => __('Ad Management', 'crypto' ),
		'desc' => '<p class="description">' . __('Now, ad management is easy with our options panel. You can control everything from here, without using separate plugins.', 'crypto' ) . '</p>',
		'fields' => array(
			array(
				'id' => 'mts_posttop_adcode',
				'type' => 'textarea',
				'title' => __('Below Post Title', 'crypto' ),
				'sub_desc' => __('Paste your Adsense, BSA or other ad code here to show ads below your article title on single posts.', 'crypto' )
			),
			array(
				'id' => 'mts_posttop_adcode_time',
				'type' => 'text',
				'title' => __('Show After X Days', 'crypto' ),
				'sub_desc' => __('Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'crypto' ),
				'validate' => 'numeric',
				'std' => '0',
				'class' => 'small-text',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_postend_adcode',
				'type' => 'textarea',
				'title' => __('Below Post Content', 'crypto' ),
				'sub_desc' => __('Paste your Adsense, BSA or other ad code here to show ads below the post content on single posts.', 'crypto' )
			),
			array(
				'id' => 'mts_postend_adcode_time',
				'type' => 'text',
				'title' => __('Show After X Days', 'crypto' ),
				'sub_desc' => __('Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'crypto' ),
				'validate' => 'numeric',
				'std' => '0',
				'class' => 'small-text',
				'args' => array('type' => 'number')
			),
			array(
				'id' => 'mts_single_ico_disclaimer',
				'type' => 'textarea',
				'title' => __('Ad Below Single ICOS or Disclaimer', 'crypto' ),
				'sub_desc' => __('Use this text area to show Ad below single ICO content. You can also use this field to show disclaimer.', 'crypto' ),
				'std' => wp_kses( __('<p>All content on this website is based on individual experience and journalistic research. It does not constitute financial advice. <strong><a href=" ' . esc_url( trailingslashit( home_url() ) ). '" title=" ' . get_bloginfo('description') . '">' . get_bloginfo('name') . '</a></strong> is not liable for how tips are used, nor for content and services on external websites. Common sense should never be neglected!. We sometimes use affiliated links which may result in a payment following a visitor taking action (such as a purchase or registration) on an external website. </p>', 'crypto' ), array('a' => array('href' => array() ), 'strong' => array(), 'p' => array() )),
				'reset_at_version' => '1.0.9'
			),
		)
	);
	$sections[] = array(
		'icon' => 'fa fa-columns',
		'title' => __('Sidebars', 'crypto' ),
		'desc' => '<p class="description">' . __('Now you have full control over the sidebars. Here you can manage sidebars and select one for each section of your site, or select a custom sidebar on a per-post basis in the post editor.', 'crypto' ) . '<br></p>',
		'fields' => array(
			array(
				'id' => 'mts_custom_sidebars',
				'type'  => 'group', //doesn't need to be called for callback fields
				'title' => __('Custom Sidebars', 'crypto' ),
				'sub_desc'  => wp_kses( sprintf( __('Add custom sidebars. %s You need to save the changes to use the sidebars in the dropdowns below. %s You can add content to the sidebars in Appearance &gt; Widgets.','crypto'), '<strong style="font-weight: 800;">', '</strong><b />' ), array( 'strong' => array(), 'br' => '' ) ),
				'groupname' => __('Sidebar', 'crypto' ), // Group name
				'subfields' =>
					array(
						array(
							'id' => 'mts_custom_sidebar_name',
							'type' => 'text',
							'title' => __('Name', 'crypto' ),
							'sub_desc' => __('Example: Homepage Sidebar', 'crypto' )
						),
						array(
							'id' => 'mts_custom_sidebar_id',
							'type' => 'text',
							'title' => __('ID', 'crypto' ),
							'sub_desc' => __('Enter a unique ID for the sidebar. Use only alphanumeric characters, underscores (_) and dashes (-), eg. "sidebar-home"', 'crypto' ),
							'std' => 'sidebar-'
						),
					),
			),
			array(
				'id' => 'mts_sidebar_for_post',
				'type' => 'sidebars_select',
				'title' => __('Single Post', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the single posts. If a post has a custom sidebar set, it will override this.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_page',
				'type' => 'sidebars_select',
				'title' => __('Single Page', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the single pages. If a page has a custom sidebar set, it will override this.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_archive',
				'type' => 'sidebars_select',
				'title' => __('Archive', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the archives. Specific archive sidebars will override this setting (see below).', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_category',
				'type' => 'sidebars_select',
				'title' => __('Category Archive', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the category archives.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_tag',
				'type' => 'sidebars_select',
				'title' => __('Tag Archive', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the tag archives.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_date',
				'type' => 'sidebars_select',
				'title' => __('Date Archive', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the date archives.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_author',
				'type' => 'sidebars_select',
				'title' => __('Author Archive', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the author archives.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_search',
				'type' => 'sidebars_select',
				'title' => __('Search', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the search results.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),
			array(
				'id' => 'mts_sidebar_for_notfound',
				'type' => 'sidebars_select',
				'title' => __('404 Error', 'crypto' ),
				'sub_desc' => __('Select a sidebar for the 404 Not found pages.', 'crypto' ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => ''
			),

			array(
				'id' => 'mts_sidebar_for_shop',
				'type' => 'sidebars_select',
				'title' => __('Shop Pages', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Select a sidebar for Shop main page and product archive pages (WooCommerce plugin must be enabled). Default is %s Shop Page Sidebar %s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => 'shop-sidebar'
			),
			array(
				'id' => 'mts_sidebar_for_product',
				'type' => 'sidebars_select',
				'title' => __('Single Product', 'crypto' ),
				'sub_desc' => wp_kses( sprintf( __('Select a sidebar for single products (WooCommerce plugin must be enabled). Default is %s Single Product Sidebar %s.','crypto'), '<strong>', '</strong>' ), array( 'strong' => array() ) ),
				'args' => array('allow_nosidebar' => false, 'exclude' => mts_get_excluded_sidebars()),
				'std' => 'product-sidebar'
			),
		),
	);

	$sections[] = array(
		'icon' => 'fa fa-list-alt',
		'title' => __('Navigation', 'crypto' ),
		'desc' => '<p class="description"><div class="controls">' . sprintf( __('Navigation settings can now be modified from the %s.', 'crypto' ), '<a href="nav-menus.php"><b>' . __( 'Menus Section', 'crypto' ) . '</b></a>' ) . '<br></div></p>'
	);


	$tabs = array();

	$args['presets'] = array();
	$args['show_translate'] = false;
	include('theme-presets.php');

	global $NHP_Options;
	$NHP_Options = new NHP_Options($sections, $args, $tabs);

} //function

add_action('init', 'setup_framework_options', 0);

/*
 *
 * Custom function for the callback referenced above
 *
 */
function my_custom_field($field, $value){
	print_r($field);
	print_r($value);

}//function

/*
 *
 * Custom function for the callback validation referenced above
 *
 */
function validate_callback_function($field, $value, $existing_value){

	$error = false;
	$value =  'just testing';
	$return['value'] = $value;
	if($error == true){
		$return['error'] = $field;
	}
	return $return;

}//function

/*--------------------------------------------------------------------
 *
 * Default Font Settings
 *
 --------------------------------------------------------------------*/
if(function_exists('mts_register_typography')) {
	mts_register_typography( array(
		'logo_font' => array(
			'preview_text' => __( 'Logo Font', 'crypto' ),
			'preview_color' => 'dark',
			'font_family' => 'Roboto',
			'font_variant' => '700',
			'font_size' => '28px',
			'font_color' => '#fabf2c',
			'additional_css' => 'text-transform: uppercase; letter-spacing: 2.20px;',
			'css_selectors' => '#header #logo a, .site-description'
		),
		'primary_navigation_font' => array(
			'preview_text' => __( 'Top Navigation', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#788694',
			'css_selectors' => '#primary-navigation a'
		),
		'secondary_navigation_font' => array(
			'preview_text' => __( 'Main Navigation', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => 'normal',
			'font_size' => '18px',
			'font_color' => '#27282d',
			'css_selectors' => '#secondary-navigation a'
		),
		'primary_slider_font' => array(
			'preview_text' => __( 'Primary Slider Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '28px',
			'font_variant' => '600',
			'font_color' => '#ffffff',
			'css_selectors' => '.primary-slider .slide-title',
			'additional_css' => 'line-height: 1.4;'
		),
		'small_posts_title_font' => array(
			'preview_text' => __( 'Small Thumb Posts Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '16px',
			'font_variant' => '600',
			'font_color' => '#010101',
			'additional_css' => 'line-height: 1.3;',
			'css_selectors' => '.small-thumb-posts .latestPost .title'
		),
		'grid_layout_title_font' => array(
			'preview_text' => __( 'Grid Layout Article Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '16px',
			'font_variant' => '600',
			'font_color' => '#27282d',
			'additional_css' => 'line-height: 1.5;',
			'css_selectors' => '.latestPost .title'
		),
		'list_posts_title_font' => array(
			'preview_text' => __( 'List Layout Article Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '18px',
			'font_variant' => '600',
			'font_color' => '#010101',
			'additional_css' => 'line-height: 1.4;',
			'css_selectors' => '.latestPost.list .title'
		),
		'text_info_links' => array(
			'preview_text' => __( 'Post Info Text', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '14px',
			'font_variant' => 'normal',
			'font_color' => '#000000',
			'css_selectors' => '.post-info, .pagination, .breadcrumb, .post-excerpt, .slide-post-info'
		),
		'single_title_font' => array(
			'preview_text' => __( 'Single Article Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '30px',
			'font_variant' => '600',
			'font_color' => '#010101',
			'css_selectors' => '.single-title'
		),
		'content_font' => array(
			'preview_text' => __( 'Content Font', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_size' => '16px',
			'font_variant' => 'normal',
			'font_color' => '#27282d',
			'additional_css' => 'line-height: 1.8;',
			'css_selectors' => 'body'
		),
		'sidebar_title' => array(
			'preview_text' => __( 'Sidebar Widget Title', 'crypto' ),
			'preview_color' => 'dark',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '20px',
			'font_color' => '#fff',
			'css_selectors' => '.widget h3'
		),
		'sidebar_heading' => array(
			'preview_text' => __( 'Sidebar Post Heading', 'crypto' ),
			'preview_color' => 'dark',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '16px',
			'font_color' => '#ffcd04',
			'css_selectors' => '.widget .post-title, .widget-slider .slide-title, .sidebar .widget .entry-title'
		),
		'sidebar_font' => array(
			'preview_text' => __( 'Sidebar Font', 'crypto' ),
			'preview_color' => 'dark',
			'font_family' => 'Open Sans',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#fff',
			'css_selectors' => '.widget'
		),
		'footer_heading' => array(
			'preview_text' => __( 'Footer Widget Title', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => '500',
			'font_size' => '18px',
			'font_color' => '#ffffff',
			'css_selectors' => '#site-footer .widget h3'
		),
		'footer_title_font' => array(
			'preview_text' => __( 'Footer Post Heading', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => '500',
			'font_size' => '14px',
			'font_color' => '#7d7e81',
			'css_selectors' => '#site-footer .widget .post-title, #site-footer .widget-slider .slide-title, #site-footer .widget .entry-title'
		),
		'footer_font' => array(
			'preview_text' => __( 'Footer Font', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => 'normal',
			'font_size' => '14px',
			'font_color' => '#788694',
			'css_selectors' => '#site-footer, #site-footer .widget, #site-footer .post-info > span, #site-footer .post-excerpt'
		),
		'copyrights_font' => array(
			'preview_text' => __( 'Copyrights Font', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Roboto',
			'font_variant' => '300',
			'font_size' => '14px',
			'font_color' => '#788694',
			'css_selectors' => '.copyrights'
		),
		'h1_headline' => array(
			'preview_text' => __( 'Content H1', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '36px',
			'font_color' => '#27282d',
			'css_selectors' => 'h1'
		),
		'h2_headline' => array(
			'preview_text' => __( 'Content H2', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '32px',
			'font_color' => '#27282d',
			'css_selectors' => 'h2'
		),
		'h3_headline' => array(
			'preview_text' => __( 'Content H3', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '30px',
			'font_color' => '#27282d',
			'css_selectors' => 'h3'
		),
		'h4_headline' => array(
			'preview_text' => __( 'Content H4', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '28px',
			'font_color' => '#27282d',
			'css_selectors' => 'h4'
		),
		'h5_headline' => array(
			'preview_text' => __( 'Content H5', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '24px',
			'font_color' => '#27282d',
			'css_selectors' => 'h5'
		),
		'h6_headline' => array(
			'preview_text' => __( 'Content H6', 'crypto' ),
			'preview_color' => 'light',
			'font_family' => 'Open Sans',
			'font_variant' => '600',
			'font_size' => '20px',
			'font_color' => '#27282d',
			'css_selectors' => 'h6'
		)
	));
}
