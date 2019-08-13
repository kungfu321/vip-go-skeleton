<?php

/*

Plugin Name: SM RSS Plugin

Version: 1.21f

Plugin URI: http://www.semanticmastery.com

Description: Take total semantic control of your RSS plugin by inserting your own HTML code, your own anchor text, a customized feed URL and no follow or do follow links 

Author: Semantic Mastery

Author URI: http://semanticmastery.com/

*/





/**

 * Define some useful constants

 **/

define('SM_RSSPLUGIN_VERSION', '1.21');

define('SM_RSSPLUGIN_DIR', plugin_dir_path(__FILE__));

define('SM_RSSPLUGIN_URL', plugin_dir_url(__FILE__));







/**

 * Load files

 * 

 **/

function sm_rssplugin_load(){

		

    if(is_admin()) //load admin files only in admin

        require_once(SM_RSSPLUGIN_DIR.'includes/smrss_admin.php');

        

    require_once(SM_RSSPLUGIN_DIR.'includes/smrss_core.php');

}



sm_rssplugin_load();







/**

 * Activation, Deactivation and Uninstall Functions

 * 

 **/

register_activation_hook(__FILE__, 'sm_rssplugin_activation');

register_deactivation_hook(__FILE__, 'sm_rssplugin_deactivation');





function sm_rssplugin_activation() {

    

	//actions to perform once on plugin activation go here    

    

	

    //register uninstaller

    register_uninstall_hook(__FILE__, 'sm_rssplugin_uninstall');

}



function sm_rssplugin_deactivation() {

    

	// actions to perform once on plugin deactivation go here

	    

}



function sm_rssplugin_uninstall(){

    

    //actions to perform once on plugin uninstall go here

	    

}





?>