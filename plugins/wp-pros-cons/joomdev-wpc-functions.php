<?php 

function get_joomdev_wpc_options(){
	global $JoomDev_wpc_options;
	$JoomDev_wpc_options = array(
								'box_background_color' => '#F9F9F9',
								'disable_box_border' => 'no',
								'box_border_style' => 'dashed',
								'box_border_color' => '#27C110',
								'button_color' => 'red',
								'button_text_color' => '#fff',
							);

	$_joomdev_wpc_options = get_option('joomdev_wpc_options', array());

	$JoomDev_wpc_options = shortcode_atts($JoomDev_wpc_options, $_joomdev_wpc_options);
	return $JoomDev_wpc_options;
}

function joomdev_wpc_extract_shortcode($content){
	// $pattern = get_shortcode_regex(array('joomdev-wpc-pros-cons', 'joomdev-wpc-pros', 'joomdev-wpc-cons'));
	$pattern = get_shortcode_regex(array('joomdev-wpc-pros-cons'));
	$r = array(
				'disable_title' => 'no',
				'title' => 'Title Here',
				'button_text' => 'Get it now',
				'pros' => array(),
				'cons' => array(),
				'disable_button' => 'no',
				'button_link' => '',
				'button_link_target' => '_SELF',
				'button_rel_attr' => 'nofollow',
			);

	return $r;
}


// file ends here...