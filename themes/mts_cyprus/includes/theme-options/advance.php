<?php
/**
 * Advanced Tab
 *
 * @package Cyprus
 */

$menus['advanced'] = array(
	'icon'  => 'fa-puzzle-piece',
	'title' => esc_html__( 'Advanced', 'cyprus' ),
);

$menus['advanced']['child'] = array(
	'code-fields'    => array( 'title' => esc_html__( 'Code Fields (Tracking etc.)', 'cyprus' ) ),
	'code-css'       => array( 'title' => esc_html__( 'Custom CSS', 'cyprus' ) ),
	'theme-features' => array( 'title' => esc_html__( 'Theme Features', 'cyprus' ) ),
	'dynamic-css'    => array( 'title' => esc_html__( 'Dynamic CSS', 'cyprus' ) ),
);

/**
 * Code fields
 */
$sections['code-fields'] = array(

	array(
		'id'       => 'mts_header_code',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => esc_html__( 'Before &lt;/head&gt;', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Enter the code which you need to place <strong>before closing &lt;/head&gt; tag</strong>. (ex: Google Webmaster Tools verification, Bing Webmaster Center, BuySellAds Script, Alexa verification etc.)', 'cyprus' ), array( 'strong' => '' ) ),
	),
	array(
		'id'       => 'mts_analytics_code',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => __( 'Footer Code', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Enter the codes which you need to place in your footer. <strong>(ex: Google Analytics, Clicky, STATCOUNTER, Woopra, Histats, etc.)</strong>.', 'cyprus' ), array( 'strong' => '' ) ),
	),
);

/**
 * Custom CSS
 */
$sections['code-css'] = array(

	array(
		'id'       => 'mts_custom_css',
		'type'     => 'ace_editor',
		'mode'     => 'css',
		'title'    => esc_html__( 'Custom CSS', 'cyprus' ),
		'sub_desc' => esc_html__( 'You can enter custom CSS code here to further customize your theme. This will override the default CSS used on your site.', 'cyprus' ),
		'args'     => array(
			'minLines' => 40,
			'maxLines' => 70,
		),
	),
);

/**
 * Theme Features
 */
$sections['theme-features'] = array(

	array(
		'id'       => 'retina_images',
		'type'     => 'switch',
		'title'    => esc_html__( 'Retina Images', 'cyprus' ),
		'sub_desc' => esc_html__( 'Use this button to enable/disable automatic creation of retina images.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'       => 'images_responsive',
		'type'     => 'switch',
		'title'    => esc_html__( 'Responsive Images', 'cyprus' ),
		'sub_desc' => esc_html__( 'Turn on for images to change size responsively.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'       => 'status_opengraph',
		'type'     => 'switch',
		'title'    => esc_html__( 'Open Graph Meta Tags', 'cyprus' ),
		'sub_desc' => esc_html__( 'Turn on to enable open graph meta tags which is mainly used when sharing pages on social networking sites like Facebook.', 'cyprus' ),
		'std'      => '1',
	),
);

/**
 * Dynamic CSS
 */
$sections['dynamic-css'] = array(

	array(
		'id'       => 'dynamic_css_compiler',
		'type'     => 'switch',
		'title'    => esc_html__( 'CSS Compiler', 'cyprus' ),
		'sub_desc' => esc_html__( 'Turn on to compile the dynamic CSS into a file. A separate file will be created for each of your pages & posts inside of the uploads/cyprus-styles folder.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'         => 'dynamic_css_db_caching',
		'type'       => 'switch',
		'title'      => esc_html__( 'Database Caching for Dynamic CSS', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Turn on to enable caching the dynamic CSS in your database.', 'cyprus' ),
		'std'        => '0',
		'dependency' => array(
			array(
				'field'      => 'dynamic_css_compiler',
				'value'      => '0',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'cache_server_ip',
		'type'     => 'text',
		'title'    => esc_html__( 'Cache Server IP', 'cyprus' ),
		'sub_desc' => esc_html__( 'For unique cases where you are using cloud flare and a cache server, ex: varnish cache. Enter your cache server IP to clear the theme options dynamic CSS cache. Consult with your server admin for help.', 'cyprus' ),
	),
);
