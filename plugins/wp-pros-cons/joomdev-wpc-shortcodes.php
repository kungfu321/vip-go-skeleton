<?php 
// add_action('the_content', 'do_shortcode', 10);
// add_filter('the_content', 'do_shortcode', 10);
add_shortcode('joomdev-wpc-pros-cons', 'joomdev_wpc_pros_cons');
function joomdev_wpc_pros_cons($atts, $content){

	global $JoomDev_wpc_options;

	$default = array(
					'disable_title' => 'no',
                    'title' => 'Title Here',
                    'button_text' => 'Get it now',
                    'disable_button' => 'no',
					'button_link' => '',
					'button_link_target' => '_SELF',
					'button_rel_attr' => 'nofollow',
				);
	$atts = shortcode_atts(
						$default,
						$atts,
						'joomdev-wpc-pros-cons'
					);

	extract($atts);

	// get pros and cons
	$p1 = get_shortcode_regex(array('joomdev-wpc-pros'));
    $p2 = get_shortcode_regex(array('joomdev-wpc-cons'));

    $pros = '';
    if ( preg_match_all( '/'. $p1 .'/s', $content, $m1 ) && array_key_exists( 2, $m1 ) && in_array( 'joomdev-wpc-pros', $m1[2] )){
    	$pros = $m1[5][0];
    }

    $cons = '';
    if ( preg_match_all( '/'. $p2 .'/s', $content, $m2 ) && array_key_exists( 2, $m2 ) && in_array( 'joomdev-wpc-cons', $m2[2] )){
    	$cons = $m2[5][0];
    }

	ob_start();

	?>
<style type="text/css">
    .wp-pros-cons {
        background: <?php echo $JoomDev_wpc_options['box_background_color'];
        ?>;
        <?php if($JoomDev_wpc_options['disable_box_border']=='yes') {
            echo 'border: none;';
        }
        else {
            ?>border: <?php echo $JoomDev_wpc_options['box_border_style'] . ' 2px ' . $JoomDev_wpc_options['box_border_color'];
            ?>;
            <?php
        }
        ?>
    }

    .wp-pros-cons a {
        color: <?php echo $JoomDev_wpc_options['button_text_color'];
        ?>;
        background: <?php echo $JoomDev_wpc_options['button_color'];
        ?>;
    }

</style>
<div class="wp-pros-cons">
    <?php 
        if($disable_title == 'yes'){
            // do nothing
        }
        else{
            ?>
                <h3 class="wp-pros-cons-title">
                    <?php echo $title; ?>
                </h3>
            <?php 
        }
    ?>
    <div class="wp-pros-cons-sections">
        <div class="wp-pros-cons-col">
            <div class="pros-section section">
                <?php echo $pros; ?>
            </div>
        </div>
        <div class="wp-pros-cons-col">
            <div class="cons-section section">
                <?php echo $cons; ?>
            </div>
        </div>

    </div>
    <div class="wp-pros-cons-btn-wrap">
        <?php 
	        		if($disable_button == 'yes'){
	        			// do nothing
	        		}
	        		else{
                        ?>

        <a href="<?php echo $button_link; ?>" target="<?php echo $button_link_target; ?>" rel="<?php echo $button_rel_attr; ?>" style="text-align: center;">
            <i class="fa fa-amazon" aria-hidden="true" style="margin-right: 10px;"></i>
            <?php echo $button_text; ?>
            <i class="fa fa-arrow-right" aria-hidden="true" style="margin-left: 10px;"></i>
        </a>
        <?php 
	        		}
	        	?>
    </div>
</div>

<?php 

	return ob_get_clean();
}

// add_shortcode('joomdev-wpc-pros', 'joomdev_wpc_pros');
function joomdev_wpc_pros($atts, $content){

}

// add_shortcode('joomdev-wpc-cons', 'joomdev_wpc_cons');
function joomdev_wpc_cons($atts, $content){

}

// file ends here...
