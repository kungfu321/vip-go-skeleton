<?php
/**
 * The WP Subscribe Pro KC Element
 */

class Cyprus_KC_WPS extends Cyprus_KC_Base {

	/**
	 * [__construct description]
	 */
	public function __construct() {

		$this->slug   = 'wp_subscribe_pro';
		$this->name   = 'WP Subscribe Pro';
		$this->icon   = 'kc-icon-box-alert';
		$this->assets = array(
			'scripts' => array(
				'wp-subscribe' => '',
			),
			'styles'  => array(
				'wp-subscribe-el' => $this->includes_uri() . 'vendors/wp-subscribe-pro/wp-subscribe-pro.css',
			),
		);

		parent::__construct();

		$this->register();
		$this->param_types();

		$this->add_filter( 'wp_subscribe_service_options', 'service_options', 10, 3 );
	}

	public function service_options( $options, $data, $service ) {

		if ( isset( $data['form_type'] ) && 'kc_shortcode' === $data['form_type'] ) {
			return $data;
		}

		return $options;
	}

	/**
	 * [get_params description]
	 * @return [type] [description]
	 */
	public function get_params() {

		$widget   = new wp_subscribe();
		$instance = $widget->get_defaults();
		$services = wps_get_mailing_services( 'options' );
		$params   = array();

		$params['general'][] = array(
			'name'    => 'style',
			'label'   => 'Style',
			'type'    => 'select',
			'options' => array(
				'block'  => __( 'Block Style', 'cyprus' ),
				'inline' => __( 'Inline Style', 'cyprus' ),
			),
			'default' => 'block',
		);

		$params['general'][] = array(
			'name'    => 'service',
			'label'   => 'Service',
			'type'    => 'select',
			'options' => $services,
		);

		foreach ( $services as $service_id => $service_name ) {
			$service           = wps_get_subscription_service( $service_id );
			$service->instance = $instance;
			$fields            = $service->get_fields();

			// Fields
			foreach ( $fields as $field ) {

				// Set label
				if ( isset( $field['title'] ) ) {
					$field['label'] = $field['title'];
				}

				// Check for some fixes
				if ( 'checkbox' === $field['type'] ) {
					$field['type'] = 'toggle';
				}

				if ( 'select' === $field['type'] ) {
					$field['type'] = 'select_with_button';
				}

				if ( 'raw' === $field['type'] ) {
					if ( is_callable( $field['content'] ) ) {
						ob_start();
							call_user_func( $field['content'] );
						$field['options'] = htmlentities( ob_get_clean() );
						//$field['value'] = str_replace( '"', "'", $field['value'] );
					} else {
						$field['value'] = '';
					}
				}

				// Set relation
				$field['relation'] = array(
					'parent'    => 'service',
					'show_when' => $service_id,
				);

				unset( $field['id'], $field['title'], $field['content'] );
				$params['general'][] = $field;
			}
		}

		$params['general'][] = array(
			'name'     => 'include_name_field',
			'label'    => 'Include <strong>Name</strong> field',
			'type'     => 'toggle',
			'relation' => array(
				'parent'    => 'service',
				'hide_when' => 'feedburner',
			),
		);

		$params['general'][] = array(
			'name'     => 'thanks_page',
			'label'    => 'Show Thank You Page after successful subscription',
			'type'     => 'toggle',
			'relation' => array(
				'parent'    => 'service',
				'show_when' => 'mailchimp,getresponse,mailerlite,benchmark,constantcontact,mailrelay,activecampaign',
			),
		);

		$params['general'][] = array(
			'name'     => 'thanks_page_url',
			'label'    => 'Thank You Page URL',
			'type'     => 'text',
			'relation' => array(
				'parent'    => 'thanks_page',
				'show_when' => 'yes',
			),
		);

		$params['general'][] = array(
			'name'     => 'thanks_page_new_window',
			'label'    => 'Open in new window',
			'type'     => 'toggle',
			'relation' => array(
				'parent'    => 'thanks_page',
				'show_when' => 'yes',
			),
		);

		$params['labels'] = array(
			array(
				'name'  => 'title',
				'label' => 'Title',
				'type'  => 'text',
			),

			array(
				'name'  => 'text',
				'label' => 'Text',
				'type'  => 'text',
			),

			array(
				'name'  => 'name_placeholder',
				'label' => 'Name Placeholder',
				'type'  => 'text',
			),

			array(
				'name'  => 'email_placeholder',
				'label' => 'Email Placeholder',
				'type'  => 'text',
			),

			array(
				'name'  => 'button_text',
				'label' => 'Button Text',
				'type'  => 'text',
			),

			array(
				'name'  => 'success_message',
				'label' => 'Success Message',
				'type'  => 'text',
			),

			array(
				'name'  => 'error_message',
				'label' => 'Error Message',
				'type'  => 'text',
			),

			array(
				'name'  => 'footer_text',
				'label' => 'Footer Text',
				'type'  => 'text',
			),
		);

		$params['styling'] = array(
			array(
				'name'    => 'wps-css',
				'label'   => 'Stylying',
				'type'    => 'css',
				'options' => array(
					array(
						'screens'       => 'any',
						'Title'         => array(
							array( 'property' => 'color', 'label' => 'Color', 'selector' => 'h4' ),
							array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => 'h4' ),
							array( 'property' => 'font-weight', 'label' => 'Font Weight', 'selector' => 'h4' ),
							array( 'property' => 'font-style', 'label' => 'Font Style', 'selector' => 'h4' ),
							array( 'property' => 'font-family', 'label' => 'Font Family', 'selector' => 'h4' ),
						),

						'Text'          => array(
							array( 'property' => 'color', 'label' => 'Color', 'selector' => 'p.text' ),
							array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => 'p.text' ),
							array( 'property' => 'font-weight', 'label' => 'Font Weight', 'selector' => 'p.text' ),
							array( 'property' => 'font-style', 'label' => 'Font Style', 'selector' => 'p.text' ),
							array( 'property' => 'font-family', 'label' => 'Font Family', 'selector' => 'p.text' ),
						),

						'Submit Button' => array(
							array( 'property' => 'background-color', 'label' => 'Background Color', 'selector' => '.submit' ),
							array( 'property' => 'color', 'label' => 'Color', 'selector' => '.submit' ),
							array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => '.submit' ),
							array( 'property' => 'font-weight', 'label' => 'Font Weight', 'selector' => '.submit' ),
							array( 'property' => 'font-style', 'label' => 'Font Style', 'selector' => '.submit' ),
							array( 'property' => 'font-family', 'label' => 'Font Family', 'selector' => '.submit' ),
						),

						'Footer'        => array(
							array( 'property' => 'background-color', 'label' => 'Background Color', 'selector' => '.footer-text' ),
							array( 'property' => 'color', 'label' => 'Color', 'selector' => '.footer-text' ),
							array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => '.footer-text' ),
							array( 'property' => 'font-weight', 'label' => 'Font Weight', 'selector' => '.footer-text' ),
							array( 'property' => 'font-style', 'label' => 'Font Style', 'selector' => '.footer-text' ),
							array( 'property' => 'font-family', 'label' => 'Font Family', 'selector' => '.footer-text' ),
						),

						'Notices'       => array(
							array( 'property' => 'color', 'label' => 'Success', 'selector' => '.thanks' ),
							array( 'property' => 'color', 'label' => 'Error', 'selector' => '.error' ),
						),

						//Custom code css
						'Custom'        => array(
							array( 'property' => 'custom', 'label' => 'Custom CSS' ),
						),
					),
				),
			),
		);

		return $params;
	}

	public function param_types() {
		global $kc;

		$kc->add_param_type( 'raw', array( $this, 'param_type_raw' ) );
		$kc->add_param_type( 'select_with_button', array( $this, 'param_type_select_with_button' ) );
	}

	public function param_type_raw() {
		?>
		{{{ kc.tools.unesc(data.options) }}}
		<?php
	}

	public function param_type_select_with_button() {

		ob_start();
			kc_param_type_select();
		$select = ob_get_clean();
		echo str_replace( 'kc-param', 'kc-param kc-param-inline', $select );
		?>
		<button class="button button-large button-inline"><i class="sl-check"></i> Get List</button>
		<#
			data.callback = function( wrp, $ ) {

				var wrp_parent = wrp.closest('.fields-edit-form');

				// Aweber Autorize Code
				$( 'button.aweber_authorization', wrp_parent ).off('click').on( 'click', function( event ) {
					event.preventDefault();

					var $this= $( this ),
						parent = $this.parent(),
						code = parent.find( 'textarea' ).val().trim();

					if( '' === code ) {
						alert( 'No authorization code found.' );
						return false;
					}

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'connect_aweber',
							aweber_code: code
						}

					}).done(function(response) {

						if ( response && ! response.success && response.error ) {
							alert( response.error );
							return false;
						}

						var details = parent.parent();
						for( key in response.data ) {
							details.find( '[name$="_' + key + '"]' ).val( response.data[ key ] );
						}

						parent.hide();
						parent.next().show();
					});
				});

				// Disconnect Aweber
				$( 'a.aweber_disconnect', wrp_parent ).off('click').on( 'click', function() {
					var $this= $( this ),
						parent = $this.closest( '.alert-hint' );

					parent.hide();
					parent.prev().show();

					//parent.parent().find( 'input[type="hidden"]' ).val( '' );
				});

				wrp.find('button').on('click', function( event ) {
					event.preventDefault();

					var button  = $(this),
						select  = button.prev('select'),
						fields  = wrp_parent.find('.kc-param-row:not(.relation-hidden)').find('input, textarea'),
						service = wrp_parent.find('.field-base-service select').val();

					var args = {};
					fields.each(function(){
						var f = $(this);
						var key = f.attr( 'name' ).replace(service+'_', '').replace(service, '');
						args[key] = f.val();
					});

					$.ajax({
						url: ajaxurl,
						method: 'post',
						data: {
							action: 'wps_get_service_list',
							service: service,
							args: args
						},

						success: function( response ) {

							if( response.success && response.lists ) {
								var sel = select.val();
								select.html( '<option value="none">Select List</option>' );
								$.each( response.lists, function( key, val ){
									select.append('<option value="'+ key +'">'+ val +'</option>');
								});
								select.val(sel);
							}
							else {
								console.log( response.error );
							}
						}
					});

				});
			}
		#>
		<?php
	}
}

/**
 * Init
 */

if ( class_exists( 'MTS_WP_Subscribe' ) ) {
	new cyprus_KC_WPS;
}
