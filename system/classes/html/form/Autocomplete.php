<?php namespace Html\Form;

/**
 * A custom Html\Form element to create an autocomplete using jQueryUI
 * This class is slightly different from convential elements as it's
 * specification doesn't come from W3C.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Autocomplete extends \Html\Element {
	
	use \Html\GlobalAttributes;
	
	public $minlength = 3;
	public $name;
	public $options = [];
	public $required;
	public $value;
	
	public static $_excludeFromOutput = [
		"id", "name", "required", "value", "minlength", "class", "style",
		"options"
	];
	
	/**
	 * Sets the minlength attribute for the autocomplete. The minlength 
	 * determines how many characters are needed to be typed before suggestions
	 * start appearing.
	 * 
	 * Defaults to 3.
	 * 
	 * @param Mixed $minlength
	 * @return \Html\Form\Autocomplete this
	 */
	function setMinlength($minlength) {
		$this->minlength = $minlength;
		
		return $this;
	}
	
	/**
	 * Sets the required string 'name' attribute.
	 * WARNING: Not setting this will break the autocomplete!
	 * 
	 * @param string $name
	 * @return \Html\Form\Autocomplete this
	 */
	public function setName($name) {
		$this->name = $name;
		$this->setId($name);
		
		return $this;
	}
	
	/**
	 * Sets the options for the autocomplete, allowable array formats are:
	 * 1. Array<DbObject>
	 * 2. Array(
	 *		Array("id" => "<id>", "value" => "<value>")
	 *	  )
	 * 3. Array(
	 *		Array([0] => "<value>", [1] => "<id>")
	 *	  )
	 *    (Not recommended, is to support older formats)
	 * 4. Array("<value>")
	 * 
	 * If options given do not match an above format IT WILL BE IGNORED (since 
	 * there is no access to the Log mechanism and echo-ing to screen is too
	 * intrusive)
	 * 
	 * @param array $options
	 * @return \Html\Form\Autocomplete this
	 */
	public function setOptions($options = []) {
		if (is_array($options) && count($options) > 0) {
			
			foreach($options as $option) {
				// Check for option 1
				if (is_a($option, "DbObject")) {
					array_push($this->options, ["id" => $option->getSelectOptionValue(), "value" => $option->getSelectOptionTitle()]);
				} else if (count($option) == 2) {
					// Check for option 2
					if (array_key_exists("id", $option) && array_key_exists("value", $option)) {
						array_push($this->options, $option);
					} else {
						// Option 3 is given
						array_push($this->options, ["id" => $option[1], "value" => $option[0]]);
					}
				} else if (is_scalar ($option)) {
					// Option 4 is given
					array_push($this->options, ["id" => $option, "value" => $option]);
				} else {
					// Doesn't match a required format, is ignored
				}
			}
		}
		
		return $this;
	}

	/**
	 * Sets the boolean required attribute 
	 * 
	 * @param string $required
	 * @return \Html\Form\Autocomplete this
	 */
	public function setRequired($required) {
		$this->required = $required;
		
		return $this;
	}
	
	/**
	 * Sets the default value for the autocomplete
	 * 
	 * @param string $value
	 * @return \Html\Form\Autocomplete this
	 */
	public function setValue($value) {
		$this->value = $value;
		
		return $this;
	}
	
	/**
	 * To string override to print element to screen
	 * 
	 * @return string
	 */
	public function __toString() {
		
		// Get necessary fields for HTML
		$required = !is_null($this->required) ? 'required="required"' : '';
		$source = json_encode($this->options);
			
		$attribute_buffer = '';
		foreach(get_object_vars($this) as $field => $value) {
			if (!is_null($value) && !in_array($field, static::$_excludeFromOutput)) {
				$attribute_buffer .= $field . '=\'' . $this->{$field} . '\' ';
			}
		}
		
		return <<<BUFFER
<input type="text" style="display: none;" id="{$this->id}"  name="{$this->name}" value="{$this->value}" {$attribute_buffer} />
<input type="text" id="acp_{$this->id}"  name="acp_{$this->name}" value="{$this->value}" class="{$this->class}" style="{$this->style}" {$required} />
<script type='text/javascript'>
	(function() {
		$("#acp_{$this->name}").keyup(function(e){
			if (e.which != 13) { 	
				$("#{$this->name}").val("");
			}
		});
		
		$("#acp_{$this->name}").autocomplete({
			minLength: {$this->minlength}, 
			source: {$source},
			select: function(event,ui) {
				$("#{$this->name}").val(ui.item.id);
				selectAutocompleteCallback(event, ui);
			}
		});
	})();
</script>
BUFFER;
	}
	
}