<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use DateTime;

use Cake\I18n\FrozenTime;

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

    public function currentAcademicYear()
    {

        $thisYear = date('Y');
        $thisMonth = date('m');
        $shortThisYear = substr($thisYear, 2, 2);

        if (in_array($thisMonth, ["09", "10", "11", "12"])) {
            $this->acyear = $thisYear . '/' . ($shortThisYear + 1);
        } else {
            $this->acyear = ($thisYear - 1) . '/' . $shortThisYear;
        }

        return $this->acyear;
    }

    public function currentAcyAndSemester()
    {

        $currentAcyAndSemester = [];

        $thisYear = date('Y');
        $thisMonth = date('m');
        $shortThisYear = substr($thisYear, 2, 2);
        $semester = 'I';

        if (in_array($thisMonth, ['09', '10', '11', '12'])) {
            $acYear = $thisYear . '/' . ($shortThisYear + 1);
        } else {
            $acYear = ($thisYear - 1) . '/' . $shortThisYear;
        }

        $currentAcyAndSemester['academic_year'] = $acYear;
        $acYearBeginningDate = $this->getAcademicYearBeginningDate($acYear);
        $selectedYear = explode('-', $acYearBeginningDate);

        if (!empty($selectedYear[0])) {
            $today = date('Y-m-d');
            $semester1End = ($selectedYear[0] + 1) . '-02-20';
            $semester2End = ($selectedYear[0] + 1) . '-06-20';
            $semester3End = ($selectedYear[0] + 1) . '-09-20';

            if ($acYearBeginningDate <= $today && $today <= $semester1End) {
                $semester = 'I';
            } elseif ($semester1End < $today && $today <= $semester2End) {
                $semester = 'II';
            } elseif ($semester2End < $today && $today <= $semester3End) {
                $semester = 'III';
            }

            $currentAcyAndSemester['semester'] = $semester;
        }

        return $currentAcyAndSemester;
    }

    public function getAcademicYear($givenMonth = null, $givenYear = null)
    {

        if (!empty($givenMonth) && !empty($givenYear)) {
            $shortGivenYear = substr($givenYear, 2, 2);

            if (in_array($givenMonth, ["09", "10", "11", "12"])) {
                return $givenYear . '/' . ($shortGivenYear + 1);
            } else {
                return ($givenYear - 1) . '/' . $shortGivenYear;
            }
        }

        return null;
    }

    public function getAcademicYearBeginningDate($academicYear)
    {

        $date = null;
        $givenYear = explode("/", $academicYear);

        if (!empty($givenYear[0])) {
            return $givenYear[0] . '-09-20';
        }

        return date('Y-m-d');
    }

    public function nextAcademicYearBeginningDate($academicYear)
    {

        $givenYear = explode("/", $academicYear);
        return !empty($givenYear[0]) ? ($givenYear[0] + 1) . '-09-20' : date('Y-m-d');
    }

    public function getAcademicYearBeginningDateBySemester($academicYear, $semester)
    {

        $givenYear = explode("/", $academicYear);
        if (!empty($givenYear[0])) {
            switch ($semester) {
                case "I":
                    return $givenYear[0] . '-09-20';
                case "II":
                    return ($givenYear[0] + 1) . '-06-20';
                case "III":
                    return ($givenYear[0] + 1) . '-08-20';
            }
        }
        return date('Y-m-d');
    }

    public function equivalentProgramType($programTypeId = null)
    {

        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $equivalentProgramType = unserialize(
            $programTypeTable->find()
                ->select(['equivalent_to_id'])
                ->where(['id' => $programTypeId])
                ->first()
                ->equivalent_to_id ?? ''
        );

        if (!empty($equivalentProgramType)) {
            return array_merge([$programTypeId], $equivalentProgramType);
        }

        return [$programTypeId];
    }

    public function acyearArray()
    {

        // Retrieve minimum academic year
        $minYear = Configure::read('Calendar.universityEstablishement') ??
            Configure::read('Calendar.applicationStartYear') ??
            date('Y') - 5;

        $minYearShort = substr($minYear, 2, 2);
        $yearFront = substr($minYear, 0, 2);
        $thisYear = date('Y');
        $thisMonth = date('m');

        // Adjust year based on the current month
        if (in_array($thisMonth, ["01", "02", "03", "04", "05", "06", "07", "08"])) {
            $thisYear -= 1;
        }

        $front2digitThisYear = substr($thisYear, 0, 2);
        $shortThisYear = substr($thisYear, 2, 2) + 1;

        // Generate academic years
        for ($i = $minYearShort; $i <= $shortThisYear; $i++) {
            $this->acyear_array_data["{$front2digitThisYear}{$i}/" . ($i + 1)] = "{$front2digitThisYear}{$i}/" . ($i + 1);
        }

        arsort($this->acyear_array_data);
        return $this->acyear_array_data;
    }

    public function acYearMinuSeparated($beginYear = '', $endYear = '')
    {

        $result = [];

        if (empty($beginYear) && empty($endYear)) {
            for ($i = 86; $i <= 99; $i++) {
                $result["19{$i}-" . sprintf('%02d', $i + 1)] = "19{$i}/" . sprintf('%02d', $i + 1);
            }

            $thisYear = date('Y');
            $thisMonth = date('m');

            if (in_array($thisMonth, range(1, 8))) {
                $thisYear--;
            }

            $front2digit = substr($thisYear, 0, 2);
            $shortYear = substr($thisYear, 2, 2) + 1;

            for ($i = 0; $i <= $shortYear; $i++) {
                $iStr = sprintf('%02d', $i);
                $result["{$front2digit}{$iStr}-" . sprintf('%02d', $i + 1)] = "{$front2digit}{$iStr}/" . sprintf('%02d', $i + 1);
            }

        } else {
            $thisYear = $endYear;
            $thisMonth = date('m');

            if (in_array($thisMonth, range(1, 8))) {
                $thisYear--;
            }

            $front2digit = substr($thisYear, 0, 2);
            $shortYear = substr($thisYear, 2, 2);

            for ($i = substr($beginYear, 2, 2); $i <= $shortYear; $i++) {
                $iStr = sprintf('%02d', $i);
                $result["{$front2digit}{$iStr}-" . sprintf('%02d', $i + 1)] = "{$front2digit}{$iStr}/" . sprintf('%02d', $i + 1);
            }
        }

        arsort($result);
        $this->acyear_minu_separated = $result;
        return $result;
    }

    public function academicYearInArray($beginYear, $endYear)
    {
        $academicYears = [];

        $thisYear = $endYear;
        $thisMonth = FrozenTime::now()->month;

        if (in_array($thisMonth, [1, 2, 3, 4, 5, 6, 7, 8])) {
            $thisYear = $thisYear;
        }

        $frontTwoDigits = substr($thisYear, 0, 2);
        $shortThisYear = (int) substr($thisYear, 2, 2);

        for ($i = (int) substr($beginYear, 2, 2); $i <= $shortThisYear; $i++) {
            $nextYear = $i + 1;
            $yearKey = sprintf(
                '%s%02d/%02d',
                $frontTwoDigits,
                $i,
                $nextYear
            );
            $academicYears[$yearKey] = $yearKey;
        }

        krsort($academicYears);
        return $academicYears;
    }


    public function isValidDateWithinYearRange($dateString, $minYear = null, $maxYear = null, $format = 'Y-m-d')
    {

        $minYear = $minYear ?? Configure::read('Calendar.universityEstablishement');
        $maxYear = $maxYear ?? $this->currentAcademicYear();

        $dateTime = DateTime::createFromFormat($format, $dateString);

        if ($dateTime && $dateTime->format($format) === $dateString) {
            $year = (int)$dateTime->format('Y');

            if ($year >= $minYear && $year <= $maxYear) {
                return true;
            }
        }

        return false;
    }
}
