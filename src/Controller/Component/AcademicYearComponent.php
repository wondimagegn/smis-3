<?php
namespace app\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
class AcademicYearComponent extends Component
{
    var $acyear;
    var $academicyear;
    //public $ac_year = array();	
    public $acyear_array_data = array();
    public $acyear_minu_separted = array();

    // Called after the Controller::beforeFilter() and before the controller action
    function startup(Controller $Controller)
    {
    }

    public function __construct(ComponentRegistry $collection = null, $settings = array())
    {
        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $Controller)
    {
        if (!empty($settings)) {
            $this->_set($settings);
        }
        return true;
    }

    function current_academicyear()
    {
        $thisyear = date('Y');
        $thismonth = date('m');
        $shortthisyear = substr($thisyear, 2, 2);

        if ($thismonth == "09" or $thismonth == "10" or $thismonth == "11" or  $thismonth == "12") {
            $this->acyear = $thisyear . '/' . ($shortthisyear + 1);
        } else {
            $this->acyear = ($thisyear - 1) . '/' . $shortthisyear;
        }
        return $this->acyear;
    }

    function current_acy_and_semester()
    {
        //To DO: check any defiend ACY and Semeter presence in Academic Calendar(DB), before using these default dates.
        $currentAcYandSemester = array();

        $thisyear = date('Y');
        $thismonth = date('m');
        $shortthisyear = substr($thisyear, 2, 2);
        $semester = 'I';

        if ($thismonth == '09' or $thismonth == '10' or $thismonth == '11' or  $thismonth == '12') {
            $ac_year = $thisyear . '/' . ($shortthisyear + 1);
        } else {
            $ac_year = ($thisyear - 1) . '/' . $shortthisyear;
        }
        //debug($ac_year);

        $currentAcYandSemester['academic_year'] = $ac_year;
        $acYearBeginingDate = $this->get_academicYearBegainingDate($ac_year);
        $selected_year = explode('-', $acYearBeginingDate);
        //debug($acYearBeginingDate);

        if (!empty($selected_year[0])) {

            $today = date('Y-m-d');
            $semester1end = ($selected_year[0] + 1) . '-' . '02' . '-' . '20';
            $semester2end = ($selected_year[0] + 1) . '-' . '06' . '-' . '20';
            $semester3end = ($selected_year[0] + 1) . '-' . '09' . '-' . '20';

            if ($acYearBeginingDate <= $today  && $today  <= $semester1end) {
                $semester = 'I';
            }
            if ($semester1end < $today  && $today  <= $semester2end) {
                $semester = 'II';
            }
            if ($semester2end < $today  && $today  <= $semester3end) {
                $semester = 'III';
            }
            //debug($semester);
            $currentAcYandSemester['semester'] = $semester;
        }
        return $currentAcYandSemester;
    }

    // Given month in the format of(m:-Numeric representation of a month, with leading zeros) and 
    // Given year in the format of (Y:-A full numeric representation of a year, 4 digits) and return academic year
    function get_academicyear($given_month = null, $given_year = null)
    {
        if (!empty($given_month) && !empty($given_year)) {
            $short_given_year = substr($given_year, 2, 2);
            if (
                $given_month == "09" or $given_month == "10" or $given_month == "11" or
                $given_month == "12"
            ) {
                $this->academicyear = $given_year . '/' . ($short_given_year + 1);
            } else {
                $this->academicyear = ($given_year - 1) . '/' . $short_given_year;
            }
            return $this->academicyear;
        }
    }

    // Given academic year return the begaining of the academic year date 
    function get_academicYearBegainingDate($academic_year)
    {
        $date = null;
        $given_year = explode("/", $academic_year);
        if (!empty($given_year[0])) {
            $date = $given_year[0] . '-' . '09' . '-' . '20';
            return $date;
        }
        return  date('Y-m-d');
    }

    function nextAcademicYearBeginingDate($academic_year)
    {
        $date = null;
        $given_year = explode("/", $academic_year);
        if (!empty($given_year[0])) {
            $date = ($given_year[0] + 1) . '-' . '09' . '-' . '20';
            return $date;
        }
        return  date('Y-m-d');
    }

    function getAcademicYearBegainingDate($academic_year, $semester)
    {
        $date = null;
        $given_year = explode("/", $academic_year);
        if (!empty($given_year[0])) {
            if ($semester == "I") {
                $date = $given_year[0] . '-' . '09' . '-' . '20';
            } else if ($semester == "II") {
                $date = ($given_year[0] + 1) . '-' . '06' . '-' . '20';
            } else if ($semester == "III") {
                $date = ($given_year[0] + 1) . '-' . '08' . '-' . '20';
            }
            return $date;
        }
        return  date('Y-m-d');
    }

    // Given program type and find the equivalent program type and  return array of equivalent program type
    function equivalent_program_type($program_type_id = null)
    {
        $find_the_equvilaent_program_type = unserialize(ClassRegistry::init('ProgramType')->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $program_type_id)));
        
        if (!empty($find_the_equvilaent_program_type)) {
            $selected_program_type_array = array();
            $selected_program_type_array[] = $program_type_id;
            $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            return $program_type_id;
        }
        return $program_type_id;
    }

    function acyear_array()
    {
        // To prepare academicyear dropdown list data

        if (Configure::read('Calendar.universityEstablishement')) {
            $minYear = Configure::read('Calendar.universityEstablishement');
        } else if (Configure::read('Calendar.applicationStartYear')) {
            $minYear = Configure::read('Calendar.applicationStartYear');
        } else {
            $minYear = date('Y')-5;
        }

        $minYearShort = substr($minYear, 2, 2);
        $yearFront = substr($minYear, 0, 2);

        if ($yearFront == 19) {
            for ($i = $minYearShort; $i <= 99; $i++) {
                if ($i == 99) {
                    $this->acyear_array_data['19' . $i . '/' . (0) . (0)] = '19' . $i . '/' . (0) . (0);
                } else {
                    $this->acyear_array_data['19' . $i . '/' . ($i + 1)] = '19' . $i . '/' . ($i + 1);
                }
            }
        
            $thisyear = date('Y');
            $thismonth = date('m');

            if ($thismonth == "01" || $thismonth == "02" || $thismonth == "03" || $thismonth == "04" || $thismonth == "05" || $thismonth == "06" || $thismonth == "07" || $thismonth == "08") {
                $thisyear = $thisyear - 1;
            }

            $front2digitthisyear = substr($thisyear, 0, 2);
            $shortthisyear = substr($thisyear, 2, 2);
            $shortthisyear = $shortthisyear + 1;

            for ($i = 0; $i <= $shortthisyear; $i++) {
                if ($i < 9) {
                    $this->acyear_array_data[$front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1);
                } else if ($i == 9) {
                    $this->acyear_array_data[$front2digitthisyear . '0' . $i . '/' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . ($i + 1);
                } else {
                    $this->acyear_array_data[$front2digitthisyear . $i . '/' . ($i + 1)] = $front2digitthisyear . $i . '/' . ($i + 1);
                }
            }
        } else if ($yearFront == 20) {
            $maxYear = substr(date('Y'), 2, 2);
            for ($i = $minYearShort; $i <= $maxYear; $i++) {
                if ($i == 99) {
                    $this->acyear_array_data['20' . $i . '/' . (0) . (0)] = '20' . $i . '/' . (0) . (0);
                } else {
                    $this->acyear_array_data['20' . $i . '/' . ($i + 1)] = '20' . $i . '/' . ($i + 1);
                }
            }
        }

        arsort($this->acyear_array_data);
        return $this->acyear_array_data;
    }

    public function acYearMinuSeparated($beginYear = '', $endYear = '')
    {

        if (empty($beginYear) && empty($endYear)) {

            // To prepare academicyear dropdown list data
            for ($i = 86; $i <= 99; $i++) {
                if ($i == 99) {
                    $this->acyear_minu_separted['19' . $i . '-' . (0) . (0)] = '19' . $i . '/' . (0) . (0);
                } else {
                    $this->acyear_minu_separted['19' . $i . '-' . ($i + 1)] = '19' . $i . '/' . ($i + 1);
                }
            }

            $thisyear = date('Y');
            $thismonth = date('m');

            if ($thismonth == "01" || $thismonth == "02" || $thismonth == "03" || $thismonth == "04" || $thismonth == "05" || $thismonth == "06" || $thismonth == "07" || $thismonth == "08") {
                $thisyear = $thisyear - 1;
            }

            $front2digitthisyear = substr($thisyear, 0, 2);
            $shortthisyear = substr($thisyear, 2, 2);
            $shortthisyear = $shortthisyear + 1;

            for ($i = 0; $i <= $shortthisyear; $i++) {
                if ($i < 9) {
                    $this->acyear_minu_separted[$front2digitthisyear . '0' . $i . '-' . '0' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1);
                } else if ($i == 9) {
                    $this->acyear_minu_separted[$front2digitthisyear . '0' . $i . '-' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . ($i + 1);
                } else {
                    $this->acyear_minu_separted[$front2digitthisyear . $i . '-' . ($i + 1)] = $front2digitthisyear . $i . '/' . ($i + 1);
                }
            }

            //arsort($this->acyear_minu_separted);
            //return $this->acyear_minu_separted;

        } else {

            $thisyear = $endYear;
            $thismonth = date('m');

            if ($thismonth == "01" || $thismonth == "02" || $thismonth == "03" || $thismonth == "04" || $thismonth == "05" || $thismonth == "06" || $thismonth == "07" || $thismonth == "08") {
                $thisyear = $thisyear -1;
            }

            $front2digitthisyear = substr($thisyear, 0, 2);
            $shortthisyear = substr($thisyear, 2, 2);
            $shortthisyear = $shortthisyear;

            for ($i = substr($beginYear, 2, 2); $i <= $shortthisyear; $i++) {
                if ($i < 9) {
                    $this->acyear_minu_separted[$front2digitthisyear . '0' . $i . '-' . '0' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1);
                } else if ($i == 9) {
                    $this->acyear_minu_separted[$front2digitthisyear . '0' . $i . '-' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . ($i + 1);
                } else {
                    $this->acyear_minu_separted[$front2digitthisyear . $i . '-' . ($i + 1)] = $front2digitthisyear . $i . '/' . ($i + 1);
                }
            }
            
            //arsort($this->acyear_minu_separted);
            //return $this->acyear_minu_separted;
        }

        arsort($this->acyear_minu_separted);
        return $this->acyear_minu_separted;
    }


    public function beforeRender(Controller $controller)
    {
        //$controller->set('ac_year', $this->ac_year);
    }

    function academicYearInArray($beginYear, $endYear)
    {
        $thisyear = $endYear;
        $thismonth = date('m');

        if ($thismonth == "01" || $thismonth == "02" || $thismonth == "03" || $thismonth == "04" || $thismonth == "05" || $thismonth == "06" || $thismonth == "07" || $thismonth == "08") {
            $thisyear = $thisyear;
        }

        $front2digitthisyear = substr($thisyear, 0, 2);
        $shortthisyear = substr($thisyear, 2, 2);
        $shortthisyear = $shortthisyear;

        for ($i = substr($beginYear, 2, 2); $i <= $shortthisyear; $i++) {
            if ($i < 9) {
                $this->acyear_array_data[$front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . '0' . ($i + 1);
            } else if ($i == 9) {
                $this->acyear_array_data[$front2digitthisyear . '0' . $i . '/' . ($i + 1)] = $front2digitthisyear . '0' . $i . '/' . ($i + 1);
            } else {
                $this->acyear_array_data[$front2digitthisyear . $i . '/' . ($i + 1)] = $front2digitthisyear . $i . '/' . ($i + 1);
            }
        }

        arsort($this->acyear_array_data);
        return $this->acyear_array_data;
    }

    function isValidDateWithinYearRange($dateString, $minYear = null, $maxYear = null, $format = 'Y-m-d') 
	{
		if (empty($minYear)) {
			$minYear = Configure::read('Calendar.universityEstablishement');
		}

		if (empty($maxYear)) {
			$maxYear = $this->current_academicyear();
		}
		
		// Create a DateTime object from the string using the defined format
		$dateTime = DateTime::createFromFormat($format, $dateString);
		
		// Check if the format matches and if there are no errors
		if ($dateTime && $dateTime->format($format) === $dateString) {
			// Extract the year from the date
			$year = (int) $dateTime->format('Y');
			
			// Check if the year is within the specified range
			if ($year >= $minYear && $year <= $maxYear) {
				return true;
			}
		}
		return false;
	}
}
