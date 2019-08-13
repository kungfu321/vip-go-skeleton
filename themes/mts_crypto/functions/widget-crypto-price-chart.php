<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Crypto Price Chart
	Description: A Widget to show price chart for specific coin
	Version: 1.0

-----------------------------------------------------------------------------------*/


// Load widget.
add_action( 'widgets_init', 'mts_crypto_price_chart' );

// Register widget.
function mts_crypto_price_chart() {
	register_widget( 'mts_crypto_price_chart_widget' );
}

// Widget class.
class mts_crypto_price_chart_widget extends WP_Widget {


/**
 * Widget setup.
 */
function __construct() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mts_crypto_price_chart_widget'
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mts_crypto_price_chart_widget'
	);

	// Create the widget
	parent::__construct( 'mts_crypto_price_chart_widget', sprintf( __('%sCrypto Price Chart', 'crypto' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ), $widget_ops, $control_ops );
	
}

/**
 * Display Widget.
 *
 * @param array $args
 * @param array $instance
 */
function widget( $args, $instance ) {
	extract( $args );

	// Variables from the widget settings
	$title = apply_filters('widget_title', $instance['title'] );
	$coin = $instance['coin'];
	$coin_url = $instance['coin_url'];
	$price_against = $instance['price_against'];

	// Before widget (defined by theme functions file)
	echo $before_widget;

	// Display the widget title if one was input
	if ( $title )
		echo $before_title . $title . $after_title;
		
	// Display a containing div
	echo '<div class="crypto-price-converter">'; ?>

	<script type="text/javascript">
		baseUrl = "https://widgets.cryptocompare.com/";
		var scripts = document.getElementsByTagName("script");
		var embedder = scripts[ scripts.length - 1 ];
		var cccTheme = {"Followers":{"background":"transparent","color":"transparent","borderColor":"transparent","counterBorderColor":"transparent","counterColor":"transparent"}};
		(function (){
		var appName = encodeURIComponent(window.location.hostname);
		if(appName==""){appName="local";}
		var s = document.createElement("script");
		s.type = "text/javascript";
		s.async = true;
		var theUrl = baseUrl+'serve/v1/coin/chart?fsym=<?php echo $coin; ?>&tsym=<?php echo $price_against; ?>&links=/test';
		s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
		embedder.parentNode.appendChild(s);
		})();

		jQuery(document).ready(function($) {
			var coin = <?php echo json_encode($coin_url); ?>;
			jQuery(window).load(function(){
			    jQuery('.ccc-widget.ccc-chart').each(function(i) {
			        jQuery(this).find('a').attr('href', coin);
			    });
		    });
		});
	</script>


	<?php echo '</div>';

	// After widget (defined by theme functions file)
	echo $after_widget;
}


/**
 * Update Widget
 *
 * @param array $new_instance
 * @param array $old_instance
 *
 * @return array
 */
function update( $new_instance, $old_instance ) {
	$instance = $old_instance;

	// Strip tags to remove HTML (important for text inputs)
	$instance['title'] = strip_tags( $new_instance['title'] );

	// No need to strip tags
	$instance['coin'] = $new_instance['coin'];
	$instance['coin_url'] = $new_instance['coin_url'];
	$instance['price_against'] = $new_instance['price_against'];

	return $instance;
}

/**
 * Widget Settings (Displays the widget settings controls on the widget panel).
 *
 * @param array $instance
 *
 * @return string|void
 */
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array(
		'title' => '',
		'coin' => 'BTC',
		'coin_url' => get_site_url().'/bitcoin-price-index/',
		'price_against' => 'USD',
	);
	
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

	<!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'crypto' ) ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>

	<!-- Coin: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'coin' ); ?>"><?php _e('Coin Code(Symbol)', 'crypto' ) ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'coin' ); ?>" name="<?php echo $this->get_field_name( 'coin' ); ?>" value="<?php echo sanitize_text_field( $instance['coin'] ); ?>" />
	</p>

	<!-- Coin URL: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'coin_url' ); ?>"><?php _e('Coin Page URL', 'crypto' ) ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'coin_url' ); ?>" name="<?php echo $this->get_field_name( 'coin_url' ); ?>" value="<?php echo sanitize_text_field( $instance['coin_url'] ); ?>" />
	</p>
	
	<!-- Price Against: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'price_against' ); ?>"><?php _e('Price Against:', 'crypto' ) ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'price_against' ); ?>" name="<?php echo $this->get_field_name( 'price_against' ); ?>" value="<?php echo sanitize_text_field( $instance['price_against'] ); ?>" />
	</p>
	
	<?php
	}
}
?>