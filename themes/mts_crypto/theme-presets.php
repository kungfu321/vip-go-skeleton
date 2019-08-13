<?php
// make sure to not include translations
$args['presets']['default'] = array(
	'title' => 'Default',
	'demo' => 'http://demo.mythemeshop.com/crypto/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/default/thumb.jpg',
	'menus' => array( 'primary' => 'Top', 'secondary' => 'Main' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 9 ),
);

// make sure to not include translations
$args['presets']['demo-2'] = array(
	'title' => 'Demo 2',
	'demo' => 'http://demo.mythemeshop.com/crypto-coins/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/demo-2/thumb.jpg',
	'menus' => array( 'secondary' => 'Main' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 8 ),
);

global $mts_presets;
$mts_presets = $args['presets'];
