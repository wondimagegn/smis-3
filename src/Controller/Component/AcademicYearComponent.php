<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\I18n\Time;
use DateTime;

class AcademicYearComponent extends Component
{
    public $acyear;
    public $academicyear;
    public $acyear_array_data = [];
    public $acyear_minu_separated = [];

    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function current_academicyear()
    {
        $thisyear = date('Y');
        $thismonth = date('m');
        $shortthisyear = substr($thisyear, 2, 2);

        if (in_array($thismonth, ['09', '10', '11', '12'])) {
            $this->acyear = $thisyear . '/' . ($shortthisyear + 1);
        } else {
            $this->acyear = ($thisyear - 1) . '/' . $shortthisyear;
        }
        return $this->acyear;
    }

    public function currentAcyAndSemester()
    {
        $currentAcYandSemester = [];
        $thisyear = date('Y');
        $thismonth = date('m');
        $shortthisyear = substr($thisyear, 2, 2);
        $semester = 'I';

        if (in_array($thismonth, ['09', '10', '11', '12'])) {
            $ac_year = $thisyear . '/' . ($shortthisyear + 1);
        } else {
            $ac_year = ($thisyear - 1) . '/' . $shortthisyear;
        }

        $currentAcYandSemester['academic_year'] = $ac_year;
        $acYearBeginingDate = $this->get_academicYearBegainingDate($ac_year);
        $selected_year = explode('-', $acYearBeginingDate);

        if (!empty($selected_year[0])) {
            $today = date('Y-m-d');
            $semester1end = ($selected_year[0] + 1) . '-02-20';
            $semester2end = ($selected_year[0] + 1) . '-06-20';
            $semester3end = ($selected_year[0] + 1) . '-09-20';

            if ($acYearBeginingDate <= $today && $today <= $semester1end) {
                $semester = 'I';
            } elseif ($semester1end < $today && $today <= $semester2end) {
                $semester = 'II';
            } elseif ($semester2end < $today && $today <= $semester3end) {
                $semester = 'III';
            }

            $currentAcYandSemester['semester'] = $semester;
        }
        return $currentAcYandSemester;
    }

    public function get_academicYearBegainingDate($academic_year)
    {
        $given_year = explode("/", $academic_year);
        return !empty($given_year[0]) ? $given_year[0] . '-09-20' : date('Y-m-d');
    }

    public function academicYearInArray($beginYear, $endYear)
    {
        $this->acyear_array_data = [];

        for ($i = substr($beginYear, 2, 2); $i <= substr($endYear, 2, 2); $i++) {
            $this->acyear_array_data['20' . $i . '/' . ($i + 1)] = '20' . $i . '/' . ($i + 1);
        }

        arsort($this->acyear_array_data);
        return $this->acyear_array_data;
    }

    public function isValidDateWithinYearRange($dateString, $minYear = null, $maxYear = null, $format = 'Y-m-d')
    {
        if (empty($minYear)) {
            $minYear = Configure::read('Calendar.universityEstablishement');
        }

        if (empty($maxYear)) {
            $maxYear = $this->current_academicyear();
        }

        $dateTime = DateTime::createFromFormat($format, $dateString);

        if ($dateTime && $dateTime->format($format) === $dateString) {
            $year = (int) $dateTime->format('Y');
            return $year >= $minYear && $year <= $maxYear;
        }
        return false;
    }
}
