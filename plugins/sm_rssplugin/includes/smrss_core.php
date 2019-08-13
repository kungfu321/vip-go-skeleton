<?php
/**
 * Main functionality
 **/
 
 /**flus url*/
 
// global $wp_rewrite;
//$wp_rewrite->flush_rules();
/*****************/

add_action('init', 'SM_RSSPLUGIN_customRSS');
function SM_RSSPLUGIN_customRSS(){
		//load feedname from database
		$smshortname = "ntsmrssar";		
		$feedname=get_option($smshortname.'_custom_rss_feedname');
		if ($feedname == "") {		//if empty use static
			$feedname='semantic';
		}
        add_feed($feedname, 'SM_RSSPLUGIN_customRSSFunc');  //this writes into $wp_rewrite->feeds[]
 		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
}

function SM_RSSPLUGIN_customRSSFunc(){
        //get_template_part('talentorss', 'feedname');
		require_once(SM_RSSPLUGIN_DIR.'includes/rss-feednameFull.php');
}

?>