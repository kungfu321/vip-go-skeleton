<?php



/** Style and css **/



 add_action( 'admin_enqueue_scripts', 'SM_RSSPLUGIN_safely_add_stylesheet_to_admin' );



    /**

     * Add stylesheet to the page

     */

    function SM_RSSPLUGIN_safely_add_stylesheet_to_admin() {

        wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );

    }





/*******/



/**

 * Admin functions SM RSS plugin

 **/

 

add_action('admin_menu',  'SM_RSSPLUGIN_addMenu');





function SM_RSSPLUGIN_mytheme_add_admin() {

  

global $themename, $smshortname, $options;



$smshortname = "ntsmrssar";

	$options = array (

		array( "name" => "feedname",

		    "desc" => "Feedname",

		    "id" => $smshortname."_custom_rss_feedname",

		    "type" => "text",

		    "std" => "semantic"),	

		array( "name" => "Content to be displayed Before the Feed:",

		    "desc" => "You can use HTML in the box above.",

		    "id" => $smshortname."_custom_rss_beforefeed",

		    "type" => "textarea",

		    "std" => ""),

		array( "name" => "Content to be displayed After the Feed:",

		    "desc" => "You can use HTML in the box above.",

		    "id" => $smshortname."_custom_rss_afterfeed",

		    "type" => "textarea",

		    "std" => ""),
		array( "name" => "Nofollow Links? ",
		    "desc" => "nofollow",
		    "id" => $smshortname."_custom_rss_nofollow",
		    "type" => "checkbox",
		    "std" => "false"),				
		array( "name" => "Full Post ",
		    "desc" => "fullpost",
		    "id" => $smshortname."_custom_rss_fulltext",
		    "type" => "checkbox",
		    "std" => "false"),

	array( "type" => "close")

	);

  

//if ( $_GET['page'] == basename(__FILE__) ) {

    if ( 'save' == $_REQUEST['action'] ) {

        foreach ($options as $value) {	//save 

        update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 



	//after saving the ids update the feed

//	print_r($wp_rewrite->feeds); 

	//same code as in core -> todo - should be implemented as a function

			//todo probably remove old existing feedfilter in $wp_rewrite->feeds[]

	

			$feedname=get_option('smrss_custom_rss_feedname');

			if ($feedname == "") {		//if empty use static

				$feedname='semantic';

			}

	        add_feed($feedname, 'customRSSFunc');  //this writes into $wp_rewrite->feeds[]

	 		global $wp_rewrite;

			$wp_rewrite->flush_rules( false );	



}

  

foreach ($options as $value) {

    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

  

   // header("Location: admin.php?page=functions.php&saved=true");

//die;

  

} 

else if( 'reset' == $_REQUEST['action'] ) {

  

    foreach ($options as $value) {

        delete_option( $value['id'] ); }

  

    //header("Location: admin.php?page=functions.php&reset=true");

//die;

  

}

//}

}



function SM_RSSPLUGIN_addMenu(){

//        add_options_page('Your Plugin Options', 'RSS Plugin Options', 'manage_options', 'my-unique-identifier', 'optionPage');



		//$icon_menu = plugin_dir_url ( __FILE__ ) . 'img/semantic-icon.png';

		add_menu_page('Page title', 'RSS Plugin Options', 'manage_options', 'my-top-level-handle', 'SM_RSSPLUGIN_my_magic_function', plugins_url( 'sm_rssplugin/img/icon.png' ));

		//add_submenu_page( 'my-top-level-handle', 'Page title', 'Sub-menu title', 'manage_options', 'my-submenu-handle', 'my_magic_function');

		

		SM_RSSPLUGIN_mytheme_add_admin();

    }



function SM_RSSPLUGIN_optionPage(){

	if ( !current_user_can( 'manage_options' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		}

	    echo "add your option page html here or include another php file";

}

function SM_RSSPLUGIN_my_magic_function(){

	if ( !current_user_can( 'manage_options' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		}

		if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';

		if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

		

	    //echo "<h2>Semantic Rss Custom</h2>";

/*		echo'<div class="wrap rm_wrap">

		    <div class="rm_opts">

		        <form method="post">

		            <div class="rm_section">

		                <div class="rm_title> 

		                    <h3>Title</h3>

		                    <submit button>

		                </div>

		                <div class="rm_input rm_txt etc">

		                    <textarea style="resize:none; height:300px; width:600px;" name="name" cols="num" rows="num"></textarea>

		                    <description>

		                </div>

		            </div>



		        </form>

		    </div>            

		</div>';*/



$smshortname = "ntsmrssar";

	$options = array (

		array( "name" => "Custom URL",

		    "desc" => "mydomain.com/feed/mycustomurl",

		    "id" => $smshortname."_custom_rss_feedname",

		    "type" => "text",

		    "std" => "semantic"),	

		array( "name" => "<h3>Content to be displayed Before the Feed:</h3> ",

		    "desc" => "<p>You can use HTML in the box above.</p>",

		    "id" => $smshortname."_custom_rss_beforefeed",

		    "type" => "textarea",

		    "std" => ""),

		array( "name" => "<h3>Content to be displayed After the Feed:</h3>",

		    "desc" => "<p>You can use HTML in the box above.</p>",

		    "id" => $smshortname."_custom_rss_afterfeed",

		    "type" => "textarea",

		    "std" => ""),

			array( "name" => "<h3>Nofollow Links?<h3>",
			    "desc" => "<p>nofollow</p>",
			    "id" => $smshortname."_custom_rss_nofollow",
			    "type" => "checkbox",
			    "std" => "false"),				
			array( "name" => "<h3>Full Post</h3>",
			    "desc" => "<p>fullpost</p>",
			    "id" => $smshortname."_custom_rss_fulltext",
			    "type" => "checkbox",
			    "std" => "false"),			

	array( "type" => "close")

	);

		

		echo '<form method="post">';

		foreach ($options as $value) {

		switch ( $value['type'] ) {

			case "close":
			echo "</div>

			</div>

			<br />";
			break;
		case 'text': 
		?><?php
$plugindata = simpleXML_load_file('http://semanticmastery.com/plugindata.xml'); 
if($plugindata ===  FALSE) { ?>
	//display standard ad
   <div class="semantic1-adds">
	 		 <img src="http://semanticmastery.com/wp-content/uploads/2014/05/Ads-Semantic-RSS-Control.jpg"></div>
<?php } else { //display new banner
?> 
	   <div class="semantic1-adds">
		 <a href="<?php echo $plugindata->BANNER->LINK;?>"><img src="<?php echo $plugindata->BANNER->IMAGE;?>"></a></div>	
	<?php
} ?>			

		<div class="semantic-wraper postbox">
		
		<div class="smt-header"><div class="logo-smt"><br></div></div>
		<iframe width="1" height="1" src="http://semanticmastery.com/rsspluginp.php" scrolling="no" frameborder="0"></iframe>
		<div class="semantic-content">
		<div class="rm_input rm_textarea semantic_url_custom postbox">

		   <div class="handlediv" title="Click to toggle"><br></div> 
		   <h3 class="hndle"><span>Use A Custom URL:</span></h3>
		   <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
           <input name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?>">

		 &emsp;<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>



		 </div>

		<?php break;

		case 'textarea':

		?>



		<div class="rm_input rm_textarea semantic_before_feed postbox">
            <h3 class="hndle"><span><br></span></h3>
		    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

		    <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows="" style="resize:none; height:100px; width:400px;"><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>

		 &emsp;<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>



		 </div>



		<?php

		break;
		case 'checkbox':
		?>

		<div class="rm_input rm_textarea">
		    <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
			<?php 
			if ( get_settings( $value['id'] ) != "") { $checkboxtick= stripslashes(get_settings( $value['id']) ); } 
			else { $checkboxtick= $value['std']; } ?> &emsp;
			
			<input type="checkbox" name="<?php echo $value['id']; ?>" value="true" <?php if ($checkboxtick == "true") echo "checked"; ?>><?php echo $value['desc']; ?><br>
		 <div class="clearfix"></div>

		 </div>
		<br />
		<?php break;	


		}

		} ?>

		<span class="submit"><input name="saved" type="submit" value="Publish Content" class="button-primary" />

		<input type="hidden" name="action" value="save" />

		</form>

		<form method="post">
<div class="buttom-smt">
		<p class="submit">

		<input name="reset" type="submit" value="Reset" class="button-primary" />

		<input type="hidden" name="action" value="reset" />

		</p></div>

		</form>



		 </div> </div> </div>
		 

	<?php	

}



?>