<?php
/**
 *	@author: James Montalvo
 *	@email: jamesmontalvo3@gmail.com
 *	@copyright: Copyright (C) 2010, James Montalvo
 *	@license: http://opensource.org/licenses/mit-license MIT License
 **/
 



//require_once "InlineFormat.php";

/*

FormEntities (inputType, value, valueOptArray=array(), separator='')
name/id handled by FieldInfoArray key
databaseLink handled by object


jQuery validate:

invalidHandler : callback for custom code when invalid form submitted.
submitHandler : callback for custom code when valid form submitted.
errorClass : sets error class, default is "error"`

errorPlacement : customize the placement of error messages
$("#myform").validate({
  errorPlacement: function(error, element) {
     error.appendTo( element.parent("td").next("td") );
   },
   debug:true
 })




*/


/* 
 *  Manages display/input HTML entities for database values, and retrieval of
 *  submitted form info.
 *
 */
class FormEntity {

    public $fetype;

    public $value;
    public $htmlId;
    public $htmlName;
    public $styleArray;
    public $format; //this isn't just formatting...hmmm..

    public $sqlname;
    public $label;
    public $longlabel;
    public $validation;

    public function __construct (//was: $sqlname, $htmlname, $label, $longlabel,
        $names, $styleArray=array(), $validation=array() ) {
    
        # Basic FormEntity sets ID and name the same.
        //$this->htmlId     = $htmlname;
        //$this->htmlName   = $htmlname;

        // standard form of $names:
        //$names = array(sqlname, htmlname, label, longlabel);

        $this->fetype = $this->getPHPClassName(); //get_class(); //sets value for FormEntity type...required for better javascript.
        
        $arr = is_array($names);
        
        $this->sqlname = !$arr ? $names : $names[0];

        $this->htmlName = ($arr && $names[1]) ? $names[1] : $this->sqlname;
        $this->htmlId = $this->htmlName;

        $this->label = ($arr && $names[2]) ? $names[2] : $this->htmlName;

        $this->longlabel = ($arr && $names[3]) ? $names[3] : $this->label;

        $this->styleArray = $styleArray;

        $this->validation = $validation;

        $this->format = new InlineFormat($this->styleArray);

        // assume submittable...remove if necessary using format->removeClass
        $this->format->addClass("submittable-field");

        $this->add_validation_strings($validation);

        
        /*
        echo $this->sqlname . "<br />";
        echo $this->htmlName . "<br />";
        echo $this->htmlId . "<br />";
        echo $this->label . "<br />";
        echo $this->longlabel . "<br />";
        */  

        //$this->sqlname = $sqlname;
        //$this->label   = $label;
        //$this->longlabel = $longlabel;

        #   Misleading name, also includes classes and other attributes
        $this->styleArray = $styleArray;

    }

    public function getPHPClassName() {
        return get_class();
    }

    #   Get/set raw database values
    public function setValue ($val) { $this->value = $val; }
    public function getValue () { return $this->value; }

    #   Get/set ID and name
    public function getHtmlName ($x="") { return $this->htmlName . $x; }
    public function getHtmlId   ($x="") { return $this->htmlId . $x; }
    public function setHtmlName ($x) { $this->htmlName = $x; }
    public function setHtmlId   ($x) { $this->htmlId = $x; }

    public function getSqlName () { return $this->sqlname; }
    public function getLabel () { return $this->label; }
    public function getLongLabel () { return $this->longlabel; }

    public function getStyleArray () { return $this->styleArray; }

    #   Generic name and ID string
    protected function getNameIdString($suffix="") {
        return "name='{$this->htmlName}$suffix' id='{$this->htmlId}$suffix'";
    }

    #
    #   Each FormEntity requires a method for data input. Defaults to hidden.
    #
    public function getEditorHTML () { return $this->createHidden(); }
    public function E() { return $this->getEditorHTML(); }

    #
    #   Each FormEntity requires a method for standard data display.
    #
    #   Default this is the same as getValue, but for certain fields (pulldowns,
    #   for example) the displayed output is different from the actual value.
    #   Some types are much more complex (datetimes, table inputs, etc)
    #
    public function getDisplayValue () {
        return htmlspecialchars( $this->value ); 
    }
    public function D() { return $this->getDisplayValue(); }

    public function get_error_container () {

        return "<span id='{$this->htmlId}-error-container'></span>";

    }

	public function set_model_val_from_form($type='POST') {
		
		$val = $this->form_val($type);
		$this->setValue($val);
		
	}
	
    public function form_val($type="POST") {
        if ($type == "POST") 
            $val = $_POST[$this->htmlName];
        else if ($type == "GET") 
            $val = $_GET[$this->htmlName];
        else
            return null;
    
		
        $val = stripslashes($val);
        
        $errors = $this->validate_input($val);

        $val = $this->sanitize_input($val);

        /*
		if (count($errors) > 0)
			return "ERROR: " . $val;
		else*/
			return $val;

		/*
		FROM getValFromForm()
        $return = array(
            "value" => $val, 
            "sqlname" => $this->sqlname, 
            "errors" => $errors);*/
    	
	}

    #   Retrieves value from form and returns in database-ready form
    public function getValFromForm ($type="POST", $forcedVal=false) {

        #   Set input type
        //if ($type == "POST") $inputArray = $_POST;
        //else if ($type == "GET") $inputArray = $_GET;
        
        if ($type == "POST" && isset($_POST[$this->htmlName])) 
            $val = $_POST[$this->htmlName];
        else if ($type == "GET" && isset($_GET[$this->htmlName])) 
            $val = $_GET[$this->htmlName];
        else if ($type == "FORCED" && $forcedVal !== null)
            $val = $forcedVal;
        else
            return null;  //return null if val not found...

        #   Retrieve, scrub, and return value if it exists
      /*  if ( isset($inputArray[$this->htmlName]) ) 
            return mysql_real_escape_string(  
                stripslashes($inputArray[$this->htmlName])  );
        else 
            return null; */

        //handles composite FormEntity types...allows passing of sqlname and val
        // CURRENTLY DISABLED...NEED TO DETERMINE HOW TO HANDLE SUB-ERRORS
        /*if (is_array($val)) {
            $info = $val;
            $val = $val[value];
            echo "WHAT THE?!?!?!<br /><br />";
        }*/
        		
		//echo "original: $val<br /><br />";
        
		//$val = stripslashes($val);
		//echo "stripslashes: $val<br /><br />";
		
        $errors = $this->validate_input($val);
		//echo "errors: $errors<br /><br />";
		
        $val = mysql_real_escape_string( $this->sanitize_input($val) );
		//echo "real escape string: $val<br /><br />";

        // if composite FormEntity being used...
        /*if ($info) {
            array_unshift($errors, $info[errors]);
        }*/

        $return = array(
            "value" => $val, 
            "sqlname" => $this->sqlname, 
            "errors" => $errors);

        /*echo "<strong>" . $this->label . "</strong>: ";
        var_dump($return);
        echo "<br /><br />";*/
    
        return $return;
        

/*
        if ($type == "POST") {

            if ( isset($_POST[$this->htmlName]) ) 
                return mysql_real_escape_string(  
                    stripslashes($_POST[$this->htmlName])  );
            else 
                return null;

        }
        else if ($type == "GET") {

            if ( isset($_GET[$this->htmlName]) ) 
                return mysql_real_escape_string(  
                    stripslashes($_GET[$this->htmlName])  );
            else 
                return null;

        }

        else return "Something went very, very wrong...";
*/
    }

    #   Used fairly often, so put here...may not be correct place
    protected function getMonthArray () {
        return array(
            "January"=>1, "February"=>2, "March"=>3, "April"=>4, "May"=>5, 
            "June"=>6, "July"=>7, "August"=>8, "September"=>9, "October"=>10, 
            "November"=>11, "December"=>12);
    }
    
    #   Special hidden input value
    public function createHidden($v=false) {

        #   If value not forced, get nominal value
        if (  ! $v  )
            $v = $this->getValue();

        $v = htmlspecialchars($v, ENT_QUOTES );

        if ( in_array("submittable-field", $this->format->classNames) )
            $submit = "class='submittable-field'";
        else
            $submit = "";
        
        return 
            "<input type='hidden' $submit {$this->getNameIdString()} fetype='Hidden' value='$v' />";
            //fetype='{$this->fetype}'

    }

    /*
    public function get_js_validation_script () {
    
        $numvalidations = count($this->validation);
        $c = 1;
        $obj = "";
                
        foreach($this->validation as $type => $restriction) {
            $obj .= "$type : \"$restriction\"";
            if ( ! ($c == $numvalidations) )
                $obj .= ", ";
            $c++;
        }
    
        return
            "<script type='text/javascript'>
                eos.fields.{$this->htmlId} = { 
                    validation : { $obj },
                    names : { label : \"{$this->label}\", longlabel : \"{$this->longlabel}\", somethingelse : \"$numvalidations\" }
                };
            </script>";
        
    }*/
    // temporary so I don't go delete everything for no reason.
    public function get_js_validation_script () { return ""; }


    public function add_validation_strings ($reqs=array()) {

        foreach ($reqs as $name => $req) {

            if ($name == "is_type") {

                //if multiple is_type requirements
                if (is_array($req))
                    foreach ($req as $type) {

                        $this->format->addClass($type);

                    }

                //if only one is_type requirement
                else
                    $this->format->addClass($req);
            }

            else {

                $this->format->addOther($name,$req);

            }


        }

    }
    
    public function validate_input ($input) {
         
        $val_failures = array();


        if( isset($this->validation[is_type]) ) {

            foreach ($this->validation[is_type] as $type) {

                if ($type == "number" && !is_numeric($input) ) {
                
                    $val_failures[is_type][number] = "input was non-numeric.";

                }

                //idea for integer validation from post at:
                //http://bytes.com/topic/php/answers/636575-checking-if-var-integer-string-int
                else if ($type == "digits" && 
                    !((string)(int)$input === (string)$input) ) {

                    $val_failures[is_type][digits] = "input was not an integer.";
                    
                }

                else if ($type == "required" && ($input == "" || !isset($input))) {

                    $val_failures[is_type][required] = "This field requires input";

                }

            }
        
        }

        /*  Greater and Less removed for now. Not part of jQuery validate...
        if( isset($this->validation[greater]) &&
            !(floatval($input) > floatval($this->validation[greater])) ) {
            
            $val_failures[greater] == "input must be greater than " . $this->validation[greater];

        }
        
        if( isset($this->validation[less]) &&
            !(floatval($input) < floatval($this->validation[less])) ) {
            
            $val_failures[less] == "input must be less than " . $this->validation[less];

        }*/
        
        if( isset($this->validation[min]) &&
            !(floatval($input) >= floatval($this->validation[min])) ) {
            
            $val_failures[min] = "input must be greater than or equal to " . $this->validation[min];

        }
        
        if( isset($this->validation[max]) &&
            !(floatval($input) <= floatval($this->validation[max])) ) {
            
            $val_failures[max] = "input must be less than or equal to " . $this->validation[max];

        }
        
        if( isset($this->validation[maxlength]) &&
            (strlen($input) > $this->validation[maxlength]) ) {
        
            $val_failures[maxlength] = "number of characters cannot exceed " . $this->validation[maxlength];

        }
        
        if( isset($this->validation[minlength]) &&
            (strlen($input) < $this->validation[minlength]) ) {
        
            $val_failures[minlength] = "number of characters must be greater than " . $this->validation[minlength];

        }
        
        return $val_failures;
        
    }
    
    public function sanitize_input ($input) {
    
        if( isset($this->sanitize[str_replace]) ) {
        
            foreach ($this->sanitize[str_replace] as $original => $new)
                $input = str_replace($original, $new, $input);
        
        }
        
        return $input;
    
    }

	public function append_to_form_val ($content) {
	
		$_POST[$this->htmlName] .= $content;
	
	}
	
}





/*
 *  
 *
 */
class Textarea extends FormEntity {

    // no special properties
    // no special constructor

    /*public function __construct ($names, $styleArray=array(), $validation=array()){

        parent::__construct($names, $styleArray, $validation);

        $this->fetype = get_class();

    }*/

    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML() {

        $this->format->addClass("form_entity_standard_textarea");

        return 
            "<textarea {$this->format->getStrings()} {$this->getNameIdString()} ".

            //" onblur='eos.validate(\"{$this->htmlId}\",true)'".

            "fetype='{$this->fetype}'>".
            "{$this->value}</textarea>" . $this->get_js_validation_script();
            
         
    }

    public function getDisplayValue () { 


        if ($this->value == "") 
            $output = ''; // was: <br /><br /><br /><br />...hopefully removing this doesn't uglify anything
        else                    
            $output = nl2br(  htmlspecialchars( $this->value, ENT_QUOTES )  );

        #
        #   REQUIRED: make hitting "tab" key add a "\t" char to textarea
        #
        #   Replaces "\t" with tab-like span element
		$output = str_replace("\t", "<span style='margin-right:30px;'></span>", 
		    $output);

		#   replaces two spaces with non-breaking spaces so all sets of spaces
		#   do not display as a single space (HTML reduces all spaces).
		$output = str_replace("  ", "&nbsp;&nbsp;", $output);

        return $output;

    }

}

class RichTextarea extends Textarea {

    // no special properties
    // no special constructor
	public $plugins = "style,advhr,advlink,directionality,visualchars,nonbreaking,xhtmlxtras,template";
	public $buttons1 = "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,fontsizeselect,|,cleanup";
	public $buttons2 = "bullist,numlist,|,outdent,indent,|,link,unlink,|,forecolor,backcolor,|,sub,sup,|,charmap";
	public $buttons3 = "";
	
    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML() {

		$this->format->addClass("richtext");
		return parent::getEditorHTML() . 
			"<script type='text/javascript'>
				$(document).ready(function(){
					
					var taWidth = $('#{$this->htmlId}').css('width');
					var taHeight = $('#{$this->htmlId}').css('height');

					tinyMCE.init({
						mode : 'exact',
						elements : '{$this->htmlId}',
						theme : 'advanced',    //(n.b. no trailing comma, this will be critical as you experiment later)

						//plugins : 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
						plugins : '{$this->plugins}',
						
						// Theme options
						theme_advanced_buttons1 : '{$this->buttons1}',
						theme_advanced_buttons2 : '{$this->buttons2}',
						theme_advanced_buttons3 : '{$this->buttons3}',
						theme_advanced_toolbar_location : 'top',
						theme_advanced_toolbar_align : 'center',
						theme_advanced_statusbar_location : 'bottom',
						theme_advanced_resizing : false,
						
						width: '100%',
						height: taHeight
					});
				});
			</script>";    
         
    }

    public function getDisplayValue () { 

        return $this->value;

    }

}



class Textbox extends FormEntity {

    // no special properties
    // no special constructor
    
    public function getPHPClassName () {
        return get_class();
    }
    
    public function getEditorHTML() {

        $this->format->addClass("form_entity_standard_text_field");

        $outputValue = htmlspecialchars( $this->getValue(), ENT_QUOTES );

        return "<input type='text' {$this->format->getStrings()} ".
            "{$this->getNameIdString()} value='$outputValue' ".

            //"onblur='eos.validate(\"{$this->htmlId}\",true)' ".

            //prevents hitting return/enter key from submit form.
            "onkeypress='return event.keyCode!=13' fetype='{$this->fetype}' />" . 
            $this->get_js_validation_script();
        
    }

}



class Dropdown extends FormEntity {

    // in form array( displayValue1=>value1, displayValue2=>value2, ... );
    public $valueOptArray;  

    public function __construct ($names, //was: $sqlname, $htmlname, $label, $longlabel,
         $styleArray=array(), $valueOptArray=array(), $validation=array() ) {

        parent::__construct ($names, //$sqlname, $htmlname, $label, $longlabel,
            $styleArray, $validation );

        $this->valueOptArray = $valueOptArray;

    }

    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML() {

        $this->format->addClass("form_entity_standard_dropdown");

        $output .= 
            "<select {$this->format->getStrings()} {$this->getNameIdString()} ".

           // "onblur='eos.validate(\"{$this->htmlId}\",true)' ".

            "fetype='{$this->fetype}'>";


        foreach ($this->valueOptArray as $display => $value) {

            if ($this->getValue() == $value) $select = " selected='selected'";
            else $select = "";

            $output .= "<option $select value='$value' >$display</option>";

        }

        $output .= '</select>' . $this->get_js_validation_script();

        return $output;

    }

    public function getDisplayValue() {

        foreach ($this->valueOptArray as $display => $value) {

            if ($this->getValue() == $value) return $display;

        }

    }
	
	public function getDropdownValues () {
		return $this->valueOptArray;
	}

}



#
#   I think this may be old...
#
#   DROPDOWN WITH OTHER REQUIRES SIGNIFICANT REVISION
#
#   $sqlname, $htmlname, $label, $longlabel, $styleArray=array()
#
#   $sqlname, $htmlname, $label, $longlabel, $styleArray=array(), $valueOptArray=array()
#
class DropdownOtherInput extends Dropdown {

    private $otherSeparator;

    public function __construct (
        //$sqlname, $htmlname, $label, $longlabel,
        $names,
        $styleArray=array(), 
        $valueOptArray=array(), 
        $otherSeparator="&nbsp;&nbsp;",
        $validation=array() ) {

        parent::__construct (
            $names, //$sqlname, $htmlname, $label, $longlabel, 
            $styleArray, $valueOptArray, $validation);

        $this->otherSeparator = $otherSeparator;

    }

    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML() {

        $this->format->addClass("form_entity_standard_dropdown");

		// I don't love this method...seems hackish
		if ($this->format->hasClass("submittable-field")) {
			$this->format->removeClass("submittable-field");
			$remove_submittable_from_textbox = false; // has submittable class, eosFieldValue recognized through textbox
		}
		else {
			$remove_submittable_from_textbox = true; // is not submittable...remove from textbox
		}
		
        //  append "_select" to dropdown since textbox present, too.
        $output .= "<select {$this->format->getStrings()} ".
            "{$this->getNameIdString('_select')} ".

          //  "onblur='eos.validate(\"{$this->htmlId}\",true)' ".

            " fetype='{$this->fetype}' >";

		$selectFlag = false;
	   
        foreach ($this->valueOptArray as $display => $value) {

            if ($value != "Other" && $value != "other") {

                if ($this->getValue() == $value) {
			
				    $select = " selected='selected'";
				    $selectFlag = true;
				    //echo "<br />SELECTED $value";
				
			    } else {
			
				    $select = "";
				    //echo "<br />NOT SELECTED $value";
			    }
			
                $output .= "<option $select value='$value' >$display</option>";

            }

        }

        if ( $selectFlag ) {

			$textboxValue = "";
			$style = "none";

            $output .= "<option value='Other' >Other</option>";

        } else {

            if ($this->getValue()) {

			    $select = " selected='selected'";

                $textboxValue = $this->getValue();
			    $style = "inline";

            } else {

                $select = "";
    
			    $textboxValue = "";
			    $style = "none";

            }

            $output .= "<option $select value='Other' >Other</option>";
        }


        $output .= '</select>';

		/*if (  $this->getValue() == "Other" || $this->getValue() == "other" ) {
			$textboxValue = "";
			$style = "inline";
		} else */

        /*if (  ! $selectFlag  ) {
			//echo "<br /><br />WHAT THE?";
			$textboxValue = $this->getValue();
			$style = "inline";
		} else {
			$textboxValue = "";
			$style = "none";
		}*/



		#
		#
		#
		#   REPLACE THIS INPUT WITH FormTextbox entity...maybe
		#
		#
		#
		if ($remove_submittable_from_textbox)
			$submittable_class = "";
		else
			$submittable_class = "class='submittable-field'";
			
		$output .= $this->otherSeparator . "<input style='display:$style;' type='text' $submittable_class ".
			"{$this->getNameIdString()} value='$textboxValue' onkeypress='return event.keyCode!=13' fetype='{$this->fetype}' />";
		
		$script =
			"
			<script type='text/javascript'>
				var drop = document.getElementById('{$this->getHtmlId()}_select');
				
				drop.onchange = function () {
					var text = document.getElementById('{$this->getHtmlId()}');
				
					if (this.options[this.selectedIndex].value == 'Other' || 
					this.options[this.selectedIndex].value == 'other') {
					
						text.style.display = 'inline';
						text.value = '';
						
					} else {
					
						text.style.display = 'none';
						text.value = this.options[this.selectedIndex].value;

					}
					
				}
			</script>";
		
		if ($this->mvc_view)
			$this->mvc_view->add_inline_script("name", $script, false);
		else
			$output .= $script;
			
		// remove these from everything
		// . $this->get_js_validation_script();
		
        return $output;

    }

    public function getDisplayValue() {

        foreach ($this->valueOptArray as $display => $value) {

            if ($this->getValue() == $value) $return = $display;

        }

		if (  ! $return  )
			return $this->getValue();
        else
            return $return;
        #
        #
        #   ELSE RETURN $return? How could this be working, otherwise?
        #
        #
        #
        
		
    }


    /* NO LONGER NECESSARY, JAVASCRIPT HANDLES LOGIC
	public function getValFromForm ($type="POST") {

        #   Set input type
        if ($type == "POST") $inputArray = $_POST;
        else if ($type == "GET") $inputArray = $_GET;
        else return "input type must be GET or POST";

        #   Retrieve, scrub, and return value if it exists
       // if ( isset($inputArray[$this->htmlName]) ) 
       //     return mysql_real_escape_string(  
       //         stripslashes($inputArray[$this->htmlName])  );
       // else 
       //    return null; 
        
    
            
        // select element isn't present, return null.
        if (  ! isset($inputArray[$this->htmlName . "_select"])  )
            return null;
            
        else if ( strtolower($inputArray[$this->htmlName . "_select"]) == "other" ) {
        
            // "other" is selected, choose input from textbox...
            $val = $inputArray[$this->htmlName];
            //return mysql_real_escape_string(  stripslashes($inputArray[$this->htmlName])  );

        }
        
        else {
        
            // "other" not selected, take dropdown menu input.
            $val = $inputArray[$this->htmlName . "_select"];
            //return mysql_real_escape_string(  stripslashes(   $inputArray[$this->htmlName . "_select"]   )  );
            
        }
        
        return parent::getValFromForm("FORCED", $val);
        
    }*/

}

#   FormEntity args:
#   
class Checkbox extends FormEntity {

    public $trueValue;
    public $trueDisplay;
    public $falseValue;
    public $falseDisplay;

    public function __construct (
        //$sqlname, $htmlname, $label, $longlabel, 
        $names,
        $styleArray=array(),
        // true-display, true-value, false-display, false-value
        $displayValueArray=array("yes", 1, "no", 0),
        $validation=array() ) {
    
        parent::__construct ($names, //$sqlname, $htmlname, $label, $longlabel, 
            $styleArray=array(), $validation);

        $this->trueDisplay  = $displayValueArray[0];;
        $this->trueValue    = $displayValueArray[1];
        $this->falseDisplay = $displayValueArray[2];
        $this->falseValue   = $displayValueArray[3];

    }

    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML () {

        if ($this->getValue() == $this->trueValue) $checked = " checked='yes'";
        else $checked = "";

        return "<input type=checkbox {$this->format->getStrings()} 
            {$this->getNameIdString()} value='{$this->trueValue}' $checked fetype='{$this->fetype}' >".
            "<input type='hidden' {$this->getNameIdString('_marker')}
            value='1' >" . $this->get_js_validation_script();

    }

    public function getDisplayValue () {

        if ($this->getValue() == $this->trueValue)
            return $this->trueDisplay;
        else
            return $this->falseDisplay;

    }

    public function getValFromForm ($type="POST") {

        #   Set input type
        if ($type == "POST") $inputArray = $_POST;
        else if ($type == "GET") $inputArray = $_GET;
        else return "input type must be GET or POST";

        #   Retrieve, scrub, and return value if it exists
        /*if ( isset($inputArray[$this->htmlName . "_marker"]) ) { 
            if (  $inputArray[$this->htmlName] == $this->trueValue  )
                $val = $this->trueValue;
            else
                $val = $this->falseValue;

            return parent::getValFromForm("FORCED", $val);

        }
        else
            return null;*/

        //MODIFIED FOR JAVASCRIPT-MANAGED SUBMIT
        if ( isset($inputArray[$this->htmlName]) ) { 
            if (  $inputArray[$this->htmlName] == $this->trueValue  )
                $val = $this->trueValue;
            else
                $val = $this->falseValue;

            return parent::getValFromForm("FORCED", $val);

        }
        else
            return null;


    }
    
    public function is_true () {
    
        if ($this->trueValue == $this->value)
            return true;
        else
            return false;
    
    }

}


class Radio extends Dropdown {

    public function __construct (
        //$sqlname, $htmlname, $label, $longlabel,
        $names,
        $styleArray=array(), 
        $valueOptArray=array(), 
        $groupDelimiter="&nbsp;&nbsp;&nbsp;", 
        $labelDelimiter="&nbsp;",
        $labelFirst=true,
        $validation=array() ) {

        parent::__construct ($names, //$sqlname, $htmlname, $label, $longlabel,
            $styleArray, $valueOptArray, $validation);

        $this->groupDelimiter = $groupDelimiter;
        $this->labelDelimiter = $labelDelimiter;
        $this->labelFirst     = $labelFirst;

    }

    public function getPHPClassName () {
        return get_class();
    }

    public function getEditorHTML() {

        $this->format->addClass("form_entity_standard_radio");

        $c = 0;

        foreach ($this->valueOptArray as $display => $value) {

            if ($this->getValue() == $value) $checked = " checked ";
            else $checked = "";

            // requires separate ID from name. htmlName is same for all radio
            // buttons in the group. ID is unique for each radio button.
            // first radio gets standard ID, so at least one is normal
            // (sort of required for jQuery form validation)
            if ($c > 0)
                $radioId = $this->htmlId . "_" . $c;
            else
                $radioId = $this->htmlId;

                
            $label = "<label for='$radioId'>$display</label>";

            if ($this->labelFirst)
                $output .= $label . $this->labelDelimiter;
            
            $output .= "<input type='radio' {$this->format->getStrings()} 
                name='{$this->htmlName}' id='$radioId' value='$value' 
                $checked ";

            //only the first radio button carries the fetype.
            if ($c == 0) {

                // tells jQuery how to handle input field
                $output .= "fetype='{$this->fetype}' >";

                // keeps jQuery from looking at remaining radio fields, since all handled by first.
                $this->format->removeClass("submittable-field");
                
            }
            else
                $output .= ">";

            if ( ! $this->labelFirst )
                $output .= $this->labelDelimiter . $label;


            // incremement counter... used for ID and group delimiter
            $c++;

            if ($c != count($this->valueOptArray) )
                $output .= $this->groupDelimiter;

        }

        return $output . 
            "<input type='hidden' {$this->getNameIdString('_marker')} 
            value='1'>" .
            $this->get_js_validation_script();

    }

    
    //IDENTICAL TO CHECKBOX>>>FIND WAY TO INHERIT/SHARE ... take that back, modified section, changed what is sent to parent::
    // HANDLED BY JAVASCFIPT NOW
    /*public function getValFromForm ($type="POST") {

        #   Set input type
        if ($type == "POST") $inputArray = $_POST;
        else if ($type == "GET") $inputArray = $_GET;
        else return "input type must be GET or POST";

        #   Retrieve, scrub, and return value if it exists
        if ( isset($inputArray[$this->htmlName . "_marker"]) ) { 
            if ( ! $inputArray[$this->htmlName]  ) {
                $val = "";
            } else
                $val = $inputArray[$this->htmlName];

            return parent::getValFromForm("FORCED", $val);

        }
        else
            return null;

    }*/

}


/*
 *  Manages classes, styles and other attributes for FormEntities.
 *
 *
 */
class InlineFormat {

    public $classNames = array();
    public $styles = array();
    public $others = array();  //other items like "size" or "cols"

    public function __construct ($infoArray=array()) {

        //if ( ! $infoArray[0] ) echo $infoArray;

        $this->addAttributes($infoArray);

    }

    public function addAttributes ($infoArray=array()) {

        foreach ($infoArray as $attr => $value) {
            /*
            if ($attr == "class") $this->classNames[] = $value;
            else $this->styles[$attr] = $value;
            */
            switch ($attr){
                case "class":
	                $this->classNames[] = $value;
	                break;
                case "size":
	                $this->others[$attr] = $value;
	                break;
             //HANDLED BY VALIDATION NOW
             //   case "maxlength":
	         //       $this->others[$attr] = $value;
	         //       break;
                default:
	                $this->styles[$attr] = $value;
                    break;
            }
        }
    }
	
	public function css ($attr, $value=false) {
	
		if (is_array($attr))
			foreach($attr as $a => $val)
				$this->styles[$a] = $val;
		else if( $value === false )
			die("InlineFormat does not yet support css value return"); 
		else
			$this->styles[$attr] = $value;
	
	}

    public function getAttribute($attr) {

        switch ($attr){
            case "class":
                return $classNames;
                break;
            case "size":
                return $this->others["size"];
                break;
            case "maxlength":
                $this->others[$attr] = $value;
                break;
            default:
                return $this->styles[$attr];
                break;
        }
    }

    public function getStrings () {

        return $this->getClassString() . ' ' . 
            $this->getStyleString() . ' ' . 
            $this->getOthersString();

    }

    //wholeString means with class='...', not just the class names
    public function getClassString ($wholeString=true, $skipped_class="") {  

        $total = count($this->classNames);
        $i = 1;
        foreach ($this->classNames as $name) {

            // allows to make one class not show up (added to remove submittable-field from certain items)
            if ( $name != $skipped_class ) {

                $output .= $name;
                if ($i != $total) $output .= ' ';

            }
            
            $i++;

        }

        if ($output == '' || $output == null) return '';
        else {
            if ($wholeString) $output = "class='$output'";
            return $output;
        }

    }

    //wholeString means with style='...', not just the style attr:vals
    public function getStyleString ($wholeString=true) { 

        $total = count($this->styles);

        foreach ($this->styles as $attr => $value) {

            $output .= "$attr:$value;";

        }

        if ($output == '' || $output == null) return '';
        else {
            if ($wholeString) $output = "style='$output'";
            return $output;
        }

    }

    //wholeString means with style='...', not just the style attr:vals
    public function getOthersString () { 

        $total = count($this->others);

        foreach ($this->others as $attr => $value) {

            $output .= "$attr='$value' ";

        }

        if ($output == '' || $output == null) return '';
        else return $output;

    }

    public function addClass ($class) {

        if ( ! in_array($class, $this->classNames))
            $this->classNames[] = $class;

    }
	
	public function hasClass ($class) {
        return in_array($class, $this->classNames);
	}

    public function removeClass ($class) {

        if (in_array($class, $this->classNames)) {
            
            $key = array_search($class, $this->classNames);            
            array_splice($this->classNames, $key, 1);

        }
        
    }

    public function addStyle ($attr, $value) {

        $this->styles[$attr] = $value;

    }

    public function addOther ($attr, $value) {

        $this->others[$attr] = $value;
        
    }

}

?>
