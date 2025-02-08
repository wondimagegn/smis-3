<?php


/* /app/views/helpers/date_picker.php */

class DateHelper extends AppHelper {

    var $datePickerClasses = 
			'highlight-days-67 split-date no-transparency';
    
    var $field_names = //to fill the name= attribute according to the marker 
    		array('m' => '[month]', 'M' => '[month]', 'd' => '[day]', 'y' => '[year]');

    ////////////////
    //creates the date input fields
    //
    //@name - mandatory! "name" of the fields (Model.field)
    //    
    //@date - date string retrieved from DB (YYYY-MM-DD)
    //
    //@optins:
    //
    //'format' - 'd-y-m', 'y-M-d' etc. M = words for months, m = numbers for months
    //				BEWARE! the calendar always appears after the year box!
    //				thus it's recommended to place the year box at the end always
    //TODO: change $format default to a configuration constant
    //'label' - field label
    //'disabled'
    //'class'
    function input($name = null, $date = null, $options = null) {        
        
        //checking name
        if ($name == null) {
            throw new Exception('missing name! check the /app/views/helpers/date_picker.php');
            return;
        }

        //changing name to data[Model][0][something]
        $name = $this->_calculateName($name);
        
      	//checking & preparing date
        if(is_array($date)){
            $date = $this -> _dateFromArray($date);   
        }
        
        if($date == null)
        	$date = $this -> _getDateFromData($name);
        
        $regexp = '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/';
        if (!preg_match($regexp, $date)) {
            throw new Exception('wrong date! check the /app/views/helpers/date_picker.php');
            return;
        }
       
        //dealing with the rest of the attributes

        $label = $disabled = $class = $format = null;
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                switch ($option) {
                    case 'format' :
                        $format = $value;
                        break;
                    case 'label' :
                        $label = $value;
                        break;
                    case 'disabled' :
                        $disabled = $value;
                        break;
                    case 'class' :
                        $class = $value;
                } //switch
            } //foreach
        } //if

        $output_string = '';

        //checking & preparing date format
        $format = $this->_prepareFormat($format);

        $id = $format[0];

        $id = strtolower($id);
        $id .= $id;

        //writing label
        if ($label != null) {
            $output_string = '<label for="' . $name . '-' . $id . '">' . $label . '</label>';
        }

        //creating fields

        foreach ($format as $field) {
			
			$output_string .= '<select id="'. $name;
			
            if ($field != 'y') //for year the id must be without suffix (this means that the box is always next to year!)
                $output_string .= '-' . strtolower($field) . strtolower($field);
            
            $output_string .= '" name="' . $name . $this -> field_names[$field] . '" ';
            
            if($disabled)
            	$output_string .= 'disabled="disabled"';


            if ($class != null || $field == 'y') { //adding classes
                $output_string .= 'class="';
                if ($class != null) //if some class is set by the user
                    $output_string .= $class;
                if ($field == 'y' && !$disabled) //if year (disabled <=> don't show picker)
                    $output_string .= ' ' . $this->datePickerClasses;
                $output_string .= '"';
            }

            $output_string .= '>';

            $this->_createInputs($field, $date, $disabled, /* &  */$output_string);
            $output_string .= '</select>' . "\n";
        }

        return $this->output($output_string);

    }

	function show($date) {
		$format = Configure::read('SMISdateFormat');
		$format = str_replace('-', ' ', $format);
		$format = str_replace('M', 'F', $format);
		$format = str_replace('y', 'Y', $format);
		return date($format, strtotime($date));
	}

	function _getDateFromData($name) {
		$name = str_replace(']','\']',$name);
		$name = str_replace('[','[\'', $name);
		$evalString = 'if (!empty($this->'.$name.')) return $this->' . $name . ';';
		$result = eval($evalString);
		
		if ($result == null)//in case the data is empty
		    return date('Y').'-'.date('m').'-'.date('d');
		
		if (is_array($result)) // if the data is in an array form
			$result = $this->_dateFromArray($result);
			
		return $result;
	}
	
	function _dateFromArray($date) {
		return $date['year'].'-'.$date['month'].'-'.$date['day']; 
	}

    function _calculateName($str) { //Model.0.field_name -> data[Model][0][field_name]
        $result = str_replace('.', '][', $str);
        $result = 'data[' . $result . ']';
        return $result;
    }

    function _calculateId($str) { //Model.0.field_name -> Model0FieldName
        $pieces = str_split('[\._]', $str);
        $result = '';
        foreach ($pieces as $piece) {
            $piece = ucfirst($piece);
            $result .= $piece;
        }
        return $result;
    }

    function _prepareFormat($format_str) {
        if ($format_str == null)
            $format_str = Configure::read('AZAdateFormat');;
        return explode('-', $format_str);
    }

    function _createInputs($type, $default, $disabled = null, & $str) { //$default like YYYY-MM-DD

        if ($default != null)
            $default = explode('-', $default);
        else
            $default = array (
                date('Y'),
                '01',
                '01'
            );
		
        $months = Array (
            _('January'),
            _('February'),
            _('March'),
            _('April'),
            _('May'),
            _('June'),
            _('July'),
            _('August'),
            _('September'),
            _('October'),
            _('November'),
            _('December'),
        );

        if ($disabled) { //when the form is disabled:
            switch ($type) {
                case 'm' :
                    $str .= '<option value="' .
                    $default[1] . '" selected="selected">' .
                    (int) $default[1] . '</option>' . "\n";
                    break;
                case 'M' :
                    $str .= '<option value="' .
                    $default[1] . '" selected="selected">' .
                    $months[$default[1]-1] . '</option>' . "\n";
                    break;
                case 'd' :
                    $str .= '<option value="' .
                    $default[2] . '" selected="selected">' .
                    (int) $default[2] . '</option>' . "\n";
                    break;
                case 'y' :
                    $str .= '<option value="' .
                    $default[0] . '" selected="selected">' .
                    $default[0] . '</option>' . "\n";
            } //switch
        } else { //when the field is active
            switch ($type) {
                case 'm' :
                    for ($i = 1; $i <= 12; $i++) {

                        $str .= '<option value="';
                        if ($i < 10)
                            $str .= '0';
                        $str .= $i . '"';
                        if ($default != null && $default[1] == $i)
                            $str .= ' selected="selected"';
                        $str .= '>' . $i . '</option>' . "\n";
                    }
                    break;
                case 'M' :
                    for ($i = 1; $i <= 12; $i++) {
                        $str .= '<option value="';
                        if ($i < 10)
                            $str .= '0';
                        $str .= $i . '"';
                        if ($default != null && $default[1] == $i)
                            $str .= ' selected="selected"';
                        $str .= '>' . $months[$i -1] . '</option>' . "\n";
                    }
                    break;
                case 'd' :
                    for ($i = 1; $i <= 31; $i++) {
                        $str .= '<option value="';
                        if ($i < 10)
                            $str .= '0';
                        $str .= $i . '"';
                        if ($default != null && $default[2] == $i)
                            $str .= ' selected="selected"';
                        $str .= '>' . $i . '</option>' . "\n";
                    }
                    break;
                case 'y' :
                    for ($i = date('Y'); $i <= 5 + date('Y'); $i++) {
                        $str .= '<option';
                        if ($default != null && $default[0] == $i)
                            $str .= ' selected="selected"';
                        $str .= ' value="' . $i . '">' . $i . '</option>' . "\n";
                    } //for
            } //switch
        }//else
    } //create inputs
} //class
?>
