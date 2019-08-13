<?php

/**
 * Add a "Sidebar" selection metabox.
 */
function mts_add_sidebar_metabox() {
	$screens = array('post', 'page');
	foreach ($screens as $screen) {
		add_meta_box(
			'mts_sidebar_metabox',
			__('Sidebar', 'crypto' ),
			'mts_inner_sidebar_metabox',
			$screen,
			'side',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'mts_add_sidebar_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_sidebar_metabox($post) {
	global $wp_registered_sidebars;
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_sidebar_metabox', 'mts_inner_sidebar_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$custom_sidebar = get_post_meta( $post->ID, '_mts_custom_sidebar', true );
	$sidebar_location = get_post_meta( $post->ID, '_mts_sidebar_location', true );

	// Select custom sidebar from dropdown
	echo '<select name="mts_custom_sidebar" id="mts_custom_sidebar" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $custom_sidebar).'>-- '.__('Default', 'crypto' ).' --</option>';
	
	// Exclude built-in sidebars
	$hidden_sidebars = mts_get_excluded_sidebars();	
	
	foreach ($wp_registered_sidebars as $sidebar) {
		if (!in_array($sidebar['id'], $hidden_sidebars)) {
			echo '<option value="'.esc_attr($sidebar['id']).'" '.selected($sidebar['id'], $custom_sidebar, false).'>'.$sidebar['name'].'</option>';
		}
	}
	echo '<option value="mts_nosidebar" '.selected('mts_nosidebar', $custom_sidebar).'>-- '.__('No sidebar --', 'crypto' ).'</option>';
	echo '</select><br />';
	
	// Select single layout (left/right sidebar)
	echo '<div class="mts_sidebar_location_fields">';
	echo '<label for="mts_sidebar_location_default" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_default" value=""'.checked('', $sidebar_location, false).'>'.__('Default side', 'crypto' ).'</label>';
	echo '<label for="mts_sidebar_location_left" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_left" value="left"'.checked('left', $sidebar_location, false).'>'.__('Left', 'crypto' ).'</label>';
	echo '<label for="mts_sidebar_location_right" style="display: inline-block; margin-right: 20px;"><input type="radio" name="mts_sidebar_location" id="mts_sidebar_location_right" value="right"'.checked('right', $sidebar_location, false).'>'.__('Right', 'crypto' ).'</label>';
	echo '</div>';
	
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			function mts_toggle_sidebar_location_fields() {
				$('.mts_sidebar_location_fields').toggle(($('#mts_custom_sidebar').val() != 'mts_nosidebar'));
			}
			mts_toggle_sidebar_location_fields();
			$('#mts_custom_sidebar').change(function() {
				mts_toggle_sidebar_location_fields();
			});
		});
	</script>
	<?php
	//debug
	//global $wp_meta_boxes;
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 */
function mts_save_custom_sidebar( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_sidebar_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_sidebar_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_sidebar_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
	if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	
	} else {
	
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$sidebar_name = sanitize_text_field( $_POST['mts_custom_sidebar'] );
	$sidebar_location = sanitize_text_field( $_POST['mts_sidebar_location'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_custom_sidebar', $sidebar_name );
	update_post_meta( $post_id, '_mts_sidebar_location', $sidebar_location );
}
add_action( 'save_post', 'mts_save_custom_sidebar' );


/**
 * Add "Post Template" selection meta box
 */
function mts_add_posttemplate_metabox() {
	add_meta_box(
		'mts_posttemplate_metabox',		 // id
		__('Template', 'crypto' ),	  // title
		'mts_inner_posttemplate_metabox',   // callback
		'post',							 // post_type
		'side',							 // context (normal, advanced, side)
		'high'							  // priority (high, core, default, low)
	);
}
//add_action('add_meta_boxes', 'mts_add_posttemplate_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_posttemplate_metabox($post) {
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_posttemplate_metabox', 'mts_inner_posttemplate_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$posttemplate = get_post_meta( $post->ID, '_mts_posttemplate', true );

	// Select post template
	echo '<select name="mts_posttemplate" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $posttemplate).'>'.__('Default Post Template', 'crypto' ).'</option>';
	echo '<option value="parallax" '.selected('parallax', $posttemplate).'>'.__('Parallax Template', 'crypto' ).'</option>';
	echo '<option value="zoomout" '.selected('zoomout', $posttemplate).'>'.__('Zoom Out Effect Template', 'crypto' ).'</option>';
	echo '</select><br />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 */
function mts_save_posttemplate( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_posttemplate_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_posttemplate_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_posttemplate_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
	if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	
	} else {
	
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$posttemplate = sanitize_text_field( $_POST['mts_posttemplate'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_posttemplate', $posttemplate );
}
add_action( 'save_post', 'mts_save_posttemplate' );

// Related function: mts_get_posttemplate( $single_template ) in functions.php

/**
 * Add "Page Header Animation" metabox.
 */
function mts_add_postheader_metabox() {
	$screens = array('post', 'page');
	foreach ($screens as $screen) {
		add_meta_box(
			'mts_postheader_metabox',
			__('Header Animation', 'crypto' ),
			'mts_inner_postheader_metabox',
			$screen,
			'side',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'mts_add_postheader_metabox');


/**
 * Print the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function mts_inner_postheader_metabox($post) {
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field('mts_inner_postheader_metabox', 'mts_inner_postheader_metabox_nonce');
	
	/*
	* Use get_post_meta() to retrieve an existing value
	* from the database and use the value for the form.
	*/
	$postheader = get_post_meta( $post->ID, '_mts_postheader', true );

	// Select post header effect
	echo '<select name="mts_postheader" style="margin-bottom: 10px;">';
	echo '<option value="" '.selected('', $postheader).'>'.__('None', 'crypto' ).'</option>';
	echo '<option value="parallax" '.selected('parallax', $postheader).'>'.__('Parallax Effect', 'crypto' ).'</option>';
	echo '<option value="zoomout" '.selected('zoomout', $postheader).'>'.__('Zoom Out Effect', 'crypto' ).'</option>';
	echo '</select><br />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return int
 *
 * @see mts_get_post_header_effect
 */
function mts_save_postheader( $post_id ) {
	
	/*
	* We need to verify this came from our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/
	
	// Check if our nonce is set.
	if ( ! isset( $_POST['mts_inner_postheader_metabox_nonce'] ) )
	return $post_id;
	
	$nonce = $_POST['mts_inner_postheader_metabox_nonce'];
	
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'mts_inner_postheader_metabox' ) )
	  return $post_id;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $post_id;
	
	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
	
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	
	} else {
	
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}
	
	/* OK, its safe for us to save the data now. */
	
	// Sanitize user input.
	$postheader = sanitize_text_field( $_POST['mts_postheader'] );
	
	// Update the meta field in the database.
	update_post_meta( $post_id, '_mts_postheader', $postheader );
}
add_action( 'save_post', 'mts_save_postheader' );

/**
 * Include and setup custom metaboxes and fields for icos post type.
 *
 * @category crypto
 * @package  crypto
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link	 https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_init', 'mts_register_crypto_single_metabox' );
/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function mts_register_crypto_single_metabox() {
	$prefix = 'mts_';

	/**
	 * Repeatable Field Groups
	 * ICO Info
	 */
	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'crypto_info',
		'title'		 => __( 'Custom ICO Info', 'crypto' ),
		'object_types'  => array( 'icos', ), // Post type
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_coin_url',
		'name' => __( 'Coin Website', 'crypto' ),
		'desc' => __( 'Enter coin website here', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_coin_start_date',
		'name' => __( 'Coin Start Date', 'crypto' ),
		'type' => 'text_date',
		'date_format' => 'Y-m-d',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_coin_end_date',
		'name' => __( 'Coin End Date', 'crypto' ),
		'type' => 'text_date',
		'date_format' => 'Y-m-d',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_coin_tagline',
		'name' => __( 'Coin Tagline', 'crypto' ),
		'type' => 'text',
		'desc' => __( 'Enter ICO tagline here. The tagline will be shown on Archive ICOs.', 'crypto' ),
		'default' => __( 'Cash meet digital currencies', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_coin_more_details',
		'name' => __( 'Coin Button Text', 'crypto' ),
		'type' => 'text',
		'desc' => __( 'Enter ICO button text here. The button will be shown on Archive ICOs.', 'crypto' ),
		'default' => __( 'More Details', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_youtube_url',
		'name' => __( 'Video URL', 'crypto' ),
		'desc' => __( 'Enter Video url from here. You can also add audio, images url from here as well.', 'crypto' ),
		'type'	   => 'text_url',
		'default' => 'https://www.youtube.com/watch?v=JqeUdVYmFD0',
	) );

	/*----[Social Share Icons]*/

	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'crypto_social',
		'title'		 => __( 'Social Share', 'crypto' ),
		'object_types'  => array( 'icos', ), // Post type
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_facebook_url',
		'name' => __( 'Facebook', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_twitter_url',
		'name' => __( 'Twitter', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_reddit_url',
		'name' => __( 'Reddit', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_linkedin_url',
		'name' => __( 'LinkedIn', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_email_id',
		'name' => __( 'Email', 'crypto' ),
		'type' => 'text_email',
		'default' => 'name@yourdomain.com',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_medium_url',
		'name' => __( 'Medium', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );
	
	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_github_url',
		'name' => __( 'Github', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'crypto_telegram_url',
		'name' => __( 'Telegram', 'crypto' ),
		'type'	   => 'text_url',
		'default' => '#',
	) );

	/*----[ICO Teams]*/

	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'crypto_team',
		'title'		 => __( 'ICO Teams', 'crypto' ),
		'object_types'  => array( 'icos', ), // Post type
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$group_field_id = $cmb->add_field( array(
		'id'		  => $prefix . 'teams_info_group',
		'type'	  => 'group',
		'options'	=> array(
			'group_title'   => __( 'Team Member {#}', 'crypto' ), // {#} gets replaced by row number
			'add_button'	=> __( 'Add Another Member', 'crypto' ),
			'remove_button' => __( 'Remove Member', 'crypto' ),
			'sortable'	=> true, // beta
			'closed'	 => false, // true to have the groups closed by default
		),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Name', 'crypto' ),
		'id'		 => 'name',
		'type'	 => 'text',
		'desc' => __( 'This Field is Mandatory.*', 'crypto' ),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Member Display Picture', 'crypto' ),
		'id'		 => 'avatar',
		'type'	 => 'file',
		'desc' => __( 'Add Profile Image from here. Recommeded size 200X200px', 'crypto' ),
		'options' => array(
            'url' => false, 
            'add_upload_file_text' => __( 'Add Image', 'crypto' ),
        ),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Position', 'crypto' ),
		'id'		 => 'position',
		'type'	 => 'text',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Facebook', 'crypto' ),
		'id'		  => 'facebook',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Twitter', 'crypto' ),
		'id'		  => 'twitter',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'GooglePlus', 'crypto' ),
		'id'		  => 'gplus',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Instagram', 'crypto' ),
		'id'		  => 'instagram',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'LinkedIn', 'crypto' ),
		'id'		  => 'linkedin',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Github', 'crypto' ),
		'id'		  => 'github',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Bitcoin Wallet Address ', 'crypto' ),
		'id'		  => 'bitcoin',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Telegram ', 'crypto' ),
		'id'		  => 'telegram',
		'type'	  => 'text_url',
	) );

	/*----[ICO Advisors]*/

	$group_field_id = $cmb->add_field( array(
		'id'		  => $prefix . 'advisors_info_group',
		'type'	  => 'group',
		'options'	=> array(
			'group_title'   => __( 'Advisor {#}', 'crypto' ), // {#} gets replaced by row number
			'add_button'	=> __( 'Add Another Advisor', 'crypto' ),
			'remove_button' => __( 'Remove Advisor', 'crypto' ),
			'sortable'	=> true, // beta
			'closed'	 => false, // true to have the groups closed by default
		),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Name', 'crypto' ),
		'id'		 => 'name',
		'type'	 => 'text',
		'desc' => __( 'This Field is Mandatory.*', 'crypto' ),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Advisor Display Picture', 'crypto' ),
		'id'		 => 'adv_avatar',
		'type'	 => 'file',
		'desc' => __( 'Add Profile Image from here. Recommeded size 200X200px', 'crypto' ),
		'options' => array(
            'url' => false, 
            'add_upload_file_text' => __( 'Add Image', 'crypto' ),
        ),
	) );
	$cmb->add_group_field( $group_field_id, array(
		'name'	 => __( 'Advisor Info', 'crypto' ),
		'id'		 => 'adv_info',
		'type'	 => 'textarea',
	) );
	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Facebook', 'crypto' ),
		'id'		  => 'adv_facebook',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Twitter', 'crypto' ),
		'id'		  => 'adv_twitter',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'GooglePlus', 'crypto' ),
		'id'		  => 'adv_gplus',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Instagram', 'crypto' ),
		'id'		  => 'adv_instagram',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'LinkedIn', 'crypto' ),
		'id'		  => 'adv_linkedin',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Github', 'crypto' ),
		'id'		  => 'adv_github',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Bitcoin Wallet Address ', 'crypto' ),
		'id'		  => 'adv_bitcoin',
		'type'	  => 'text_url',
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name'	  => __( 'Telegram ', 'crypto' ),
		'id'		  => 'adv_telegram',
		'type'	  => 'text_url',
	) );

	/*----[ICO Details]*/

	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'crypto_details',
		'title'		 => __( 'ICO Details', 'crypto' ),
		'object_types'  => array( 'icos', ), // Post type
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$cmb->add_field( array(
		'name' => __( 'Details', 'crypto' ),
		'id' => $prefix . 'crypto_coin_details',
		'type'	=> 'wysiwyg',
		'options' => array( 'textarea_rows' => 10, ),
	) );


	/*-[ Price Index Page Options]----*/
	$cmb = new_cmb2_box( array(
		'id'			=> $prefix . 'price_index',
		'title'		 => __( 'Coin Details', 'crypto' ),
		'object_types'  => array( 'page', ), // Post type
		'show_on'      => array( 'key' => 'page-template', 'value' => 'page-price-index.php' ),
		'context'	  => 'normal',
		'priority'	 => 'high',
		'show_names'   => true, // Show field names on the left
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coin_code',
		'name' => __( 'Coin Code(Symbol)', 'crypto' ),
		'type' => 'text',
		'desc' => __( 'Example: BTC, ETH, XRP, LTC etc. <a href="https://www.cryptocompare.com/coins/list/USD/1" target="_blank">All Coin List</a>', 'crypto' ),
		'default' => __( 'BTC', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coin_price_against',
		'name' => __( 'Show Price Against', 'crypto' ),
		'type' => 'text',
		'desc' => __( 'Please enter currency codes separated by comma and without any space. You can also use "BTC" against other coins', 'crypto' ),
		'default' => __( 'USD,EUR,GBP,JPY,RUR', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'coin_historical_data',
		'name' => __( 'Historical Data Currency', 'crypto' ),
		'type' => 'text_small',
		'desc' => __( 'Historical data can be shown against only one currency. You can also use "BTC" against other coins.', 'crypto' ),
		'default' => __( 'USD', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'price_index_article_title',
		'name' => __( 'Articles Section Title', 'crypto' ),
		'type' => 'text',
		'desc' => __( 'Set title for the articles section on this page.', 'crypto' ),
		'default' => __( 'Latest News', 'crypto' ),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'price_index_category',
		'name' => __( 'Show Articles From', 'crypto' ),
		'type'     => 'select',
		'desc' => __( 'Choose category from below list. Articles from this category will appear on this page after graphs.', 'crypto' ),
		'options_cb'     => 'cmb2_get_term_options',
		// Same arguments you would pass to `get_terms`.
		'get_terms_args' => array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
		),
	) );

	$cmb->add_field( array(
		'id'   => $prefix . 'price_index_no_of_posts',
		'name' => __( 'Number of Articles to Show', 'crypto' ),
		'type' => 'text_small',
		'desc' => __( 'From here you can set number of articles to show from above category on this page.', 'crypto' ),
		'attributes' => array(
			'type' => 'number'
		),
		'default' => __( '8', 'crypto' ),
	) );
}

add_action( 'save_post', 'mts_check_icos_expiry', 11 );
function mts_check_icos_expiry( $post_id ) {
	$icos_expiry_date = get_post_meta( $post_id, 'mts_crypto_coin_end_date', true );
	if ( ! empty( $icos_expiry_date ) ) {
		update_post_meta( $post_id, 'mts_icos_expire_timestamp', strtotime( $icos_expiry_date ) );
	}
}
