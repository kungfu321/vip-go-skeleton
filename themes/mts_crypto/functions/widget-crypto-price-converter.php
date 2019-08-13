<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Price Converter
	Description: A Widget to show price converter for Crypto Currencies
	Version: 1.0

-----------------------------------------------------------------------------------*/


// Load widget.
add_action( 'widgets_init', 'mts_crypto_price_converter' );

// Register widget.
function mts_crypto_price_converter() {
	register_widget( 'mts_crypto_price_converter_widget' );
}

// Widget class.
class mts_crypto_price_converter_widget extends WP_Widget {


/**
 * Widget setup.
 */
function __construct() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mts_crypto_price_converter_widget'
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mts_crypto_price_converter_widget'
	);

	// Create the widget
	parent::__construct( 'mts_crypto_price_converter_widget', sprintf( __('%sCrypto Price Converter', 'crypto' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ), $widget_ops, $control_ops );
	
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
	$currencies = $instance['currencies'];

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
		var cccTheme = {"General":{"headerText":"Price Converter"},"Form":{}};
		(function (){
		var appName = encodeURIComponent(window.location.hostname);
		if(appName==""){appName="local";}
		var s = document.createElement("script");
		s.type = "text/javascript";
		s.async = true;
		var theUrl = baseUrl+'serve/v1/coin/converter?fsym=<?php echo $coin; ?>&tsyms=<?php echo $currencies; ?>';
		s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
		embedder.parentNode.appendChild(s);
		})();
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
	$instance['currencies'] = $new_instance['currencies'];

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
		'currencies' => 'USD,EUR,CNY,GBP,RUB',
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
	
	<!-- Currencies: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'currencies' ); ?>"><?php _e('Currencies:', 'crypto' ) ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'currencies' ); ?>" name="<?php echo $this->get_field_name( 'currencies' ); ?>" value="<?php echo sanitize_text_field( $instance['currencies'] ); ?>" />
	</p>
	
	<?php
	}
}
?>