<?php 
add_action( 'admin_menu', 'joomdev_wpc_register_menu_page' );
function joomdev_wpc_register_menu_page(){
    add_menu_page( 
        'JoomDev WP Pros & Cons Settings',
        'WP Pros &amp; Cons',
        'manage_options',
        'joomdev-wpc-settings',
        'joomdev_wpc_register_menu_page_callback',
        plugins_url( JOOMDEV_WPC_DIR . '/admin/assets/images/joomdev-wpc.png' )
        // 10
    ); 
}

add_action( 'admin_init', 'joomdev_wpc_register_setting' );
function joomdev_wpc_register_setting() {
    register_setting( 'joomdev_wpc_options', 'joomdev_wpc_options', 'joomdev_wpc_register_setting_callback' ); 
}

function joomdev_wpc_register_setting_callback($a){
	return $a;
}

function joomdev_wpc_register_menu_page_callback(){
    ?>
        <style type="text/css">
            .joomdev-wpc-settings-header{
                background-color: #fff;
                border-top: 4px solid #23282d;
                padding: 20px 25px;
                margin-bottom: 20px;
            }
            .joomdev-wpc-settings-header h1 img{
                float: left;
                width: 60px;
                height: auto;
            }
            .joomdev-wpc-settings-header h1 > span{
                margin-top: 13px;
                display: inline-block;
                margin-left: 10px;
            }
            .column-left{
                float: left;
            }
            .column-right{
                float: right;
            }
            .joomdev-more-themes-plugins-button{
                background-color: #23282d;
                padding: 10px 20px;
                font-size: 13px;
                font-weight: bold;
                text-transform: uppercase;
                color: #fff;
                text-decoration: none;
                border-radius: 3px;
                margin-top: 15px;
                display: inline-block;
            }
            .joomdev-more-themes-plugins-button:hover{
                color: #fff;
            }
            .joomdev-more-themes-plugins-button:before, .joomdev-more-themes-plugins-button:after{
                content: "";
                display: block;
                width: 100%;
                float: none;
                clear: both;
            }
            .joomdev-more-themes-plugins-button img{
                    max-width: 18px;
                    float: left;
                    margin-right: 10px
            }

            .joomdev-wpc-settings-menu-tabs{
                margin-bottom: 20px;
                border-bottom: 4px solid #fff;
            }
            .joomdev-wpc-settings-menu-tabs ul{
                list-style-type: none;
                margin: 0;
                padding: 0;
            }
            .joomdev-wpc-settings-menu-tabs ul li{
                list-style-type: none;
                display: inline-block;
                margin: 0;
                padding: 0;
            }
            .joomdev-wpc-settings-menu-tabs ul li a{
                display: block;
                padding: 10px;
                border-top: 2px solid #dd7e7e;
                text-decoration: none;
                color: #dd7e7e;
                background-color: #fff;
                font-weight: bold;
            }
            .joomdev-wpc-settings-menu-tabs ul li a:hover{
                color: #dd7e7e;
            }
        </style>
    	<div class="wrap">
    		<!-- <h2>JoomDev WP Pros & Cons Settings</h2> -->
            <h2></h2>
            <div class="joomdev-wpc-settings-header">
                <div class="column-left">
                    <div class="clear"></div>
                    <h1><img src="<?php echo plugins_url( JOOMDEV_WPC_DIR . '/admin/assets/images/joomdev-wpc.png' ); ?>"> <span><b>WP Pros &amp; Cons</b> <small><small>by <b><i>JoomDev</i></b></small></small></span></h1>
                    <div class="clear"></div>
                </div>
                <div class="column-right">
                    <a href="<?php echo JOOMDEV_WPC_MORE_THEMES_PLUGINS_URL; ?>" target="_BLANK" class="joomdev-more-themes-plugins-button"><img src="<?php echo plugins_url(JOOMDEV_WPC_DIR . '/admin/assets/images/font-awesome_4-7-0_shopping-cart_256_0_ffffff_none.png'); ?>"> More WP Themes &amp; Plugins</a>
                </div>
                <div class="clear"></div>
            </div>

            <div class="joomdev-wpc-settings-menu-tabs">
                <ul>
                    <li>
                        <a href="javascript:;">Settings</a>
                    </li>
                </ul>
            </div>


    		<?php 
    			if($_GET['settings-updated']){
					?>
						<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
							<p><strong>Settings saved.</strong></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>
					<?php 
				}
    		?>

    		<!-- <img style="max-width:100%;" src="<?php echo plugins_url( JOOMDEV_WPC_DIR . '/admin/assets/images/settings-header.png' ); ?>"> -->

    		<form method="post" action="options.php">

    			<?php 
    				settings_fields('joomdev_wpc_options');
    				// $joomdev_wpc_options = get_option('joomdev_wpc_options', array());
    				$joomdev_wpc_options = get_joomdev_wpc_options();
    			?>

    			<table class="form-table">
    				<tbody>
    					<tr>
    						<th>Box Background Color</th>
    						<td>
    							<input name="joomdev_wpc_options[box_background_color]" type="text" value="<?php echo isset($joomdev_wpc_options['box_background_color']) ? $joomdev_wpc_options['box_background_color'] : ''; ?>" class="regular-text joomdev-color-picker">
    						</td>
    					</tr>
    					<tr>
    						<th>Disable Box Border</th>
    						<td>
    							<input name="joomdev_wpc_options[disable_box_border]" type="hidden" value="no" class="">
    							<input name="joomdev_wpc_options[disable_box_border]" type="checkbox" value="yes" <?php echo (isset($joomdev_wpc_options['disable_box_border']) && $joomdev_wpc_options['disable_box_border'] == 'yes') ? 'checked' : ''; ?> class="">
    						</td>
    					</tr>
    					<tr>
    						<th>Box Border Style</th>
    						<td>
    							<select name="joomdev_wpc_options[box_border_style]">
    								<option <?php echo (isset($joomdev_wpc_options['box_border_style']) && $joomdev_wpc_options['box_border_style'] == 'none') ? 'selected' : ''; ?> value="none">None</option>
    								<option <?php echo (isset($joomdev_wpc_options['box_border_style']) && $joomdev_wpc_options['box_border_style'] == 'dotted') ? 'selected' : ''; ?> value="dotted">Dotted</option>
    								<option <?php echo (isset($joomdev_wpc_options['box_border_style']) && $joomdev_wpc_options['box_border_style'] == 'solid') ? 'selected' : ''; ?> value="solid">Solid</option>
    								<option <?php echo (isset($joomdev_wpc_options['box_border_style']) && $joomdev_wpc_options['box_border_style'] == 'dashed') ? 'selected' : ''; ?> value="dashed">Dashed</option>
    							</select>
    						</td>
    					</tr>
    					<tr>
    						<th>Box Border Color</th>
    						<td>
    							<input name="joomdev_wpc_options[box_border_color]" type="text" value="<?php echo isset($joomdev_wpc_options['box_border_color']) ? $joomdev_wpc_options['box_border_color'] : ''; ?>" class="regular-text joomdev-color-picker">
    						</td>
    					</tr>
    					<tr>
    						<th>Button Color</th>
    						<td>
    							<input name="joomdev_wpc_options[button_color]" type="text" value="<?php echo isset($joomdev_wpc_options['button_color']) ? $joomdev_wpc_options['button_color'] : ''; ?>" class="regular-text joomdev-color-picker">
    						</td>
    					</tr>
    					<tr>
    						<th>Button Text Color</th>
    						<td>
    							<input name="joomdev_wpc_options[button_text_color]" type="text" value="<?php echo isset($joomdev_wpc_options['button_text_color']) ? $joomdev_wpc_options['button_text_color'] : ''; ?>" class="regular-text joomdev-color-picker">
    						</td>
    					</tr>
    				</tbody>
    			</table>

    			<?php submit_button(); ?>
    		</form>
    	</div>
    <?php  
}

add_filter("mce_external_plugins", "joomdev_wpc_enqueue_editor_scripts");
function joomdev_wpc_enqueue_editor_scripts($plugin_array){
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["joomdev_wpc_shortcode"] =  plugins_url( JOOMDEV_WPC_DIR . '/admin/assets/js/admin-visual-editor.js?ver=' . JOOMDEV_WPC_VER );
    return $plugin_array;
}

add_filter("mce_buttons", "joomdev_wpc_register_buttons_editor");
function joomdev_wpc_register_buttons_editor($buttons){
    //register buttons with their id.
    array_push($buttons, "joomdev_wpc_shortcode");
    return $buttons;
}

function joomdev_wpc_editor_button_popup(){

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
    /*$screen = get_current_screen();
    if($screen->base == 'post'){
        global $post;
        $description = $post->post_content;
        $r = joomdev_wpc_extract_shortcode($description);
    }*/

    ?>
        <!-- The modal / dialog box, hidden somewhere near the footer -->
        <!-- <div id="joomdev_wpc_editor_button_popup" class="hidden" style="max-width:800px"> -->
        <div id="joomdev_wpc_editor_button_popup" class="">
            <div id="joomdev_wpc_editor_button_popup_inner">
                <div class="joomdev-wpc-settings-head">
                    <h2>Ưu và nhược điểm</h2>
                    <button type="button" class="joomdev_wpc_cancel_shortcode joomdev-wpc-settings-head-close">&times;</button>
                </div>

                <div class="clear"></div>
                <div class="column">
                    <div class="mb20">
                        <label><h4>Tắt tiêu đề</h4>
                            <input type="hidden" name="joomdev_wpc_disable_title" value="yes">
                            <input type="checkbox" name="joomdev_wpc_disable_title" id="joomdev_wpc_disable_title" value="yes">
                        </label>
                    </div>
                </div>
                <div class="column joomdev_wpc_disable_title_box">
                    <div class="joomdev_wpc_title">
                        <label>
                            <h4>Tiêu đề</h4>
                            <input type="text" name="joomdev_wpc_title" class="regular-text" value="<?php echo $r['title']; ?>">
                        </label>
                    </div>
                </div>
                <div class="clear"></div>

                <div class="clear"></div>
                <div class="column">
                    <h4>Điểm mạnh</h4>
                    <div class="joomdev_wpc_pros">
                        <?php
                            if(!empty($r['pros'])){
                                foreach ($r['pros'] as $value) {
                                    ?>
                                        <div class="joomdev_wpc_pro_single">
                                            <input type="text" class="regular-text" name="joomdev_wpc_pro_single[]" value="<?php echo $value; ?>">
                                            <span class="joomdev_wpc_pro_single_remove">&times;</span>
                                        </div>
                                    <?php 
                                }
                            }
                        ?>
                    </div>
                    <button type="button" class="button button-secondary button-large joomdev_wpc_add_pros">+ Thêm điểm mạnh</button>
                </div>
                <div class="column">
                    <h4>Điểm yếu</h4>
                    <div class="joomdev_wpc_cons">
                        <?php 
                            if(!empty($r['cons'])){
                                foreach ($r['cons'] as $value) {
                                    ?>
                                        <div class="joomdev_wpc_con_single">
                                            <input type="text" class="regular-text" name="joomdev_wpc_con_single[]" value="<?php echo $value; ?>">
                                            <span class="joomdev_wpc_con_single_remove">&times;</span>
                                        </div>
                                    <?php 
                                }
                            }
                        ?>
                    </div>
                    <button type="button" class="button button-secondary button-large joomdev_wpc_add_cons">+ Thêm điểm yếu</button>
                </div>
                <div class="clear"></div>
                <div class="mb20">
                    <label><h4>Tắt Button</h4>
                        <input type="hidden" name="joomdev_wpc_disable_button" value="yes">
                        <input type="checkbox" name="joomdev_wpc_disable_button" id="joomdev_wpc_disable_button" value="yes">
                    </label>
                </div>
                <div class="joomdev_wpc_disable_button_box">
                    <div class="clear"></div>
                    <div class="column">
                        <div class="joomdev_wpc_button_text">
                            <label><h4>Button Text</h4>
                                <input type="text" name="joomdev_wpc_button_text" class="regular-text" value="<?php echo $r['button_text']; ?>">
                            </label>
                        </div>
                    </div>
                    <div class="column">
                        <div class="mb20">
                            <label><h4>Button Link</h4>
                                <input type="text" name="joomdev_wpc_button_link" class="regular-text" value="<?php echo $r['button_link']; ?>">
                            </label>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="clear"></div>
                    <div class="column">
                        <div class="mb20">
                            <label><h4>Button Link Target</h4>
                                <select name="joomdev_wpc_button_link_target">
                                    <option value="_SELF">Open in same tab</option>
                                    <option value="_BLANK" selected>Open in new tab</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="column">
                        <div class="mb20">
                            <label><h4>Button Rel Attribute</h4>
                                <select name="joomdev_wpc_button_rel_attr">
                                    <option value="dofollow">Dofollow</option>
                                    <option value="nofollow" selected>Nofollow</option>
                                    <option value="noreferrer">Noreferrer</option>
                                    <option value="noopener">Noopener</option>
                                    <option value="external">External</option>
                                    <option value="help">Help</option>
                                    <option value="alternate">Alternate</option>
                                    <option value="author">Author</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            
                <div class="joomdev_wpc_save">
                    <button type="button" class="button button-primary button-large joomdev_wpc_save_shortcode">Insert Shortcode</button>
                    <button type="button" class="button button-secondary button-large joomdev_wpc_cancel_shortcode">Cancel</button>
                </div>
            </div>
        </div>

        <style type="text/css">
            #joomdev_wpc_editor_button_popup{
                
            }
            #joomdev_wpc_editor_button_popup.joomdev-wpc-popup-display{
                background-color: rgba(0,0,0,0.5);
                position: fixed;
                z-index: 999999;
                display: block;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
            }
            #joomdev_wpc_editor_button_popup_inner{
                display: block;
                width: 100%;
                max-width: 800px;
                position: absolute;
                margin-bottom: 50px;
                top: -500%;
                left: 50%;
                transform: translateX(-50%);
                transition: all 0.2s linear;
                background-color: #fff;
                border-radius: 2px;
                padding: 0 20px 20px 20px;
            }
            .joomdev-wpc-popup-display #joomdev_wpc_editor_button_popup_inner{
                transition: all 0.2s linear;
                top: 50px;
            }
            .joomdev-wpc-settings-head{
                position: relative;
                border-bottom: 1px solid #ccc;
                margin-bottom: 20px;
                margin-right: -20px;
                margin-left: -20px;
                padding: 0 20px;
            }
            .joomdev-wpc-settings-head-close{
                font-size: 25px;
                width: 50px;
                background: none;
                border: none;
                position: absolute;
                right: 0;
                top: -9px;
                font-weight: bold;
            }
            #joomdev_wpc_editor_button_popup .joomdev-wpc-settings-header{
                background-color: #f1f1f1;
                border-top: 4px solid #23282d;
                padding: 10px 20px;
                margin-bottom: 20px;
            }
            #joomdev_wpc_editor_button_popup .joomdev-wpc-settings-header h3 img{
                float: left;
                width: 40px;
                height: auto;
            }
            #joomdev_wpc_editor_button_popup .joomdev-wpc-settings-header h3 > span{
                margin-top: 10px;
                display: inline-block;
                margin-left: 10px;
            }
            .column-left{
                float: left;
            }
            .column-right{
                float: right;
            }
            #joomdev_wpc_editor_button_popup .joomdev-more-themes-plugins-button{
                background-color: #23282d;
                padding: 10px 20px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
                color: #fff !important;
                box-shadow: none !important;
                text-decoration: none;
                border-radius: 3px;
                margin-top: 14px;
                display: inline-block;
            }
            .joomdev-more-themes-plugins-button:hover{
                color: #fff;
            }
            .joomdev-more-themes-plugins-button:before, .joomdev-more-themes-plugins-button:after{
                content: "";
                display: block;
                width: 100%;
                float: none;
                clear: both;
            }
            #joomdev_wpc_editor_button_popup .joomdev-more-themes-plugins-button img{
                max-width: 16px;
                float: left;
                margin-right: 10px;
            }

            .mb20{
                margin-bottom: 20px;
            }
            .joomdev_wpc_title{
                margin-bottom: 20px;
            }
            .joomdev_wpc_button_text{
                margin-bottom: 10px;
                margin-top: 10px;
            }
            .joomdev_wpc_save{
                border-top: 1px solid #d9d9d9;
                text-align: right;
                padding-top: 20px;
            }
            .column{
                float: left;
                min-width: 380px;
                /*border-right: 1px solid #d9d9d9;*/
            }
            .column:last-child{
                border-right: none;
            }
            .column h4{
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .joomdev_wpc_pro_single_remove, .joomdev_wpc_con_single_remove{
                background-color: #f00;
                color: #fff;
                font-weight: bold;
                width: 26px;
                display: inline-block;
                height: 26px;
                line-height: 20px;
                border-radius: 30px;
                text-align: center;
                font-size: 20px;
                cursor: pointer;
            }
            .joomdev_wpc_pro_single, .joomdev_wpc_con_single{
                margin-bottom: 10px;
            }
        </style>

        <script type="text/javascript" id="joomdev_wpc_add_pros_single">
            var joomdev_wpc_add_pros_single = '<div class="joomdev_wpc_pro_single">' +
                '<input type="text" class="regular-text" name="joomdev_wpc_pro_single[]" value="">' +
                '<span class="joomdev_wpc_pro_single_remove">&times;</span>' +
            '</div>';
        </script>

        <script type="text/javascript" id="joomdev_wpc_add_cons_single">
            var joomdev_wpc_add_cons_single = '<div class="joomdev_wpc_con_single">' +
                '<input type="text" class="regular-text" name="joomdev_wpc_con_single[]" value="">' +
                '<span class="joomdev_wpc_con_single_remove">&times;</span>' +
            '</div>';
        </script>

        <script type="text/javascript">
            jQuery(function($){
                // disable button
                $(document).on('change', '#joomdev_wpc_disable_button', function(){
                    if($(this).is(':checked')){
                        $(document).find('.joomdev_wpc_disable_button_box').slideUp();
                    }
                    else{
                        $(document).find('.joomdev_wpc_disable_button_box').slideDown();
                    }
                });

                // disable title
                $(document).on('change', '#joomdev_wpc_disable_title', function(){
                    if($(this).is(':checked')){
                        $(document).find('.joomdev_wpc_disable_title_box').slideUp();
                    }
                    else{
                        $(document).find('.joomdev_wpc_disable_title_box').slideDown();
                    }
                });

                // pros
                $(document).on('click', '.joomdev_wpc_add_pros', function(e){
                    e.preventDefault();
                    var single_pro_html = joomdev_wpc_add_pros_single; //$('#joomdev_wpc_add_pros_single').html();
                    $(document).find('.joomdev_wpc_pros').append(single_pro_html);
                    setTimeout(function(){
                        $(document).find('.joomdev_wpc_pros .joomdev_wpc_pro_single:last-child [type="text"]').focus();
                    }, 10);
                });

                $(document).on('click', '.joomdev_wpc_pro_single_remove', function(e){
                    e.preventDefault();
                    $(this).closest('.joomdev_wpc_pro_single').remove();
                });

                // cons
                $(document).on('click', '.joomdev_wpc_add_cons', function(e){
                    e.preventDefault();
                    var single_con_html = joomdev_wpc_add_cons_single; //$('#joomdev_wpc_add_cons_single').html();
                    $(document).find('.joomdev_wpc_cons').append(single_con_html);
                    setTimeout(function(){
                        $(document).find('.joomdev_wpc_cons .joomdev_wpc_con_single:last-child [type="text"]').focus();
                    }, 10);
                });

                $(document).on('click', '.joomdev_wpc_con_single_remove', function(e){
                    e.preventDefault();
                    $(this).closest('.joomdev_wpc_con_single').remove();
                });

                // save shortcode joomdev_wpc_save_shortcode
                $(document).on('click', '.joomdev_wpc_save_shortcode', function(e){
                    e.preventDefault();
                    var disable_title = $(document).find('#joomdev_wpc_disable_title').is(':checked') ? 'yes' : 'no';
                    disable_title = $.trim(disable_title);
                    
                    var title = $(document).find('[name="joomdev_wpc_title"]').val();
                    title = $.trim(title);
                    var button_text = $(document).find('[name="joomdev_wpc_button_text"]').val();
                    button_text = $.trim(button_text);
                    var disable_button = $(document).find('#joomdev_wpc_disable_button').is(':checked') ? 'yes' : 'no';
                    disable_button = $.trim(disable_button);

                    var button_link = $(document).find('[name="joomdev_wpc_button_link"]').val();
                    button_link = $.trim(button_link);
                    var button_link_target = $(document).find('[name="joomdev_wpc_button_link_target"]').val();
                    button_link_target = $.trim(button_link_target);
                    var button_rel_attr = $(document).find('[name="joomdev_wpc_button_rel_attr"]').val();
                    button_rel_attr = $.trim(button_rel_attr);

                    var shortcode_string_pros = '';
                    $(document).find('[name="joomdev_wpc_pro_single[]"]').each(function(){
                        var v = $(this).val();
                        v = $.trim(v);
                        // shortcode_string_pros += '[joomdev-wpc-pros]'+v+'[/joomdev-wpc-pros]';
                        shortcode_string_pros += '<li class="joomdev_wpc_pro_single"><i class="fa fa-thumbs-up" aria-hidden="true"></i>'+v+'</li>';
                    });
                    var shortcode_string_pros_list = '[joomdev-wpc-pros]<div class="section-title">Điểm mạnh</div><ul class="joomdev_wpc_pros_list">'+shortcode_string_pros+'</ul>[/joomdev-wpc-pros]';

                    var shortcode_string_cons = '';
                    $(document).find('[name="joomdev_wpc_con_single[]"]').each(function(){
                        var v = $(this).val();
                        v = $.trim(v);
                        // shortcode_string_cons += '[joomdev-wpc-cons]'+v+'[/joomdev-wpc-cons]';
                        shortcode_string_cons += '<li class="joomdev_wpc_con_single"><i class="fa fa-thumbs-down" aria-hidden="true"></i>'+v+'</li>';
                    });
                    var shortcode_string_cons_list = '[joomdev-wpc-cons]<div class="section-title">Điểm yếu</div><ul class="joomdev_wpc_cons_list">'+shortcode_string_cons+'</ul>[/joomdev-wpc-cons]';

                    var shortcode_string = '<br>[joomdev-wpc-pros-cons disable_title="'+disable_title+'" title="'+title+'" button_text="'+button_text+'" disable_button="'+disable_button+'" button_link="'+button_link+'" button_link_target="'+button_link_target+'" button_rel_attr="'+button_rel_attr+'"]'+shortcode_string_pros_list+shortcode_string_cons_list+'[/joomdev-wpc-pros-cons]<br>';
                    window.parent.send_to_editor(shortcode_string);
                    window.parent.tb_remove();
                    
                    $(document).find('.joomdev_wpc_cancel_shortcode').trigger('click');                    
                });

                $(document).on('click', '.joomdev_wpc_cancel_shortcode', function(){
                    $('#joomdev_wpc_editor_button_popup').removeClass('joomdev-wpc-popup-display');
                    $('body').css('overflow', 'auto');
                });
            });
        </script>
    <?php 
}

// file ends here.