<?php
class MTS_Options_button_set_hide_below extends MTS_Options{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since MTS_Options 1.0
	*/
	function __construct($field = array(), $value ='', $parent){

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();

	}//function



	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since MTS_Options 1.0
	*/
	function render(){
		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		$class = (isset($this->field['class']))?$this->field['class'].' ':'';

		// Hide a number of fields below
		if (!isset($this->field['args'])) $this->field['args'] = array();
		$args = wp_parse_args($this->field['args'], array('hide' => 1));

		echo '<fieldset class="'.$class.'buttonset buttonset-hide" data-hide="'.$args['hide'].'">';

			$i = 1; foreach($this->field['options'] as $k => $v){
				if($i == '1'){
					echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.'mts-opts-button-hide-below" value="'.$k.'" '.checked($this->value, $k, false).'/>';
					echo '<label id="mts-opts-button-hide-below" for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'"'.checked($this->value, $k, false).'>'.$v.'</label>';
				}else{
					echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.'mts-opts-button-show-below" value="'.$k.'" '.checked($this->value, $k, false).'/>';
					echo '<label id="mts-opts-button-show-below" for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'">'.$v.'</label>';
				}
				$i++;
			}//foreach

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<span class="description">'.$this->field['desc'].'</span>':'';

		echo '</fieldset>';

	}//function



	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since MTS_Options 1.0
	*/
	function enqueue(){

		wp_enqueue_style('mts-opts-jquery-ui-css');

		wp_enqueue_script(
			'mts-opts-field-button_set_hide_below-js',
			$this->url .'fields/button_set_hide_below/field_button_set_hide_below.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
			cyprus()->get_version(),
			true
		);


	}//function

}//class
?>
