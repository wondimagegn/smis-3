<?php
namespace App\Utility;

use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use DateTime;

trait AcademicYearTrait
{
    /**
     * @var string|null Current academic year (e.g., '2024/25').
     */
    protected ?string $academicYear = null;

    /**
     * @var array Academic year data for dropdowns (e.g., ['2024/25' => '2024/25']).
     */
    protected array $academicYearArrayData = [];

    /**
     * @var array Academic year data with minus-separated keys (e.g., ['2024-25' => '2024/25']).
     */
    protected array $academicYearMinusSeparated = [];

    /**
     * Get the current academic year based on the current date.
     *
     * @return string Academic year in 'YYYY/YY' format (e.g., '2024/25').
     */
    public function currentAcademicYear()
    {
        if ($this->academicYear === null) {
            $thisYear = (int) date('Y');
            $thisMonth = (int) date('m');
            $shortThisYear = substr($thisYear, 2, 2);

            if (in_array($thisMonth, [9, 10, 11, 12])) {
                $this->academicYear = $thisYear . '/' . ($shortThisYear + 1);
            } else {
                $this->academicYear = ($thisYear - 1) . '/' . $shortThisYear;
            }
        }

        return $this->academicYear;
    }

    /**
     * Get the current academic year and semester.
     *
     * @return array Array with 'academic_year' and 'semester' keys.
     */
    public function currentAcyAndSemester()
    {
        // TODO: Check Academic Calendar in database for defined academic year and semester
        $thisYear = (int) date('Y');
        $thisMonth = (int) date('m');
        $shortThisYear = substr($thisYear, 2, 2);

        if (in_array($thisMonth, [9, 10, 11, 12])) {
            $academicYear = $thisYear . '/' . ($shortThisYear + 1);
        } else {
            $academicYear = ($thisYear - 1) . '/' . $shortThisYear;
        }

        $beginDate = $this->getAcademicYearBeginningDate($academicYear);
        $selectedYear = explode('-', $beginDate)[0];

        $semester = 'I';
        if (!empty($selectedYear)) {
            $today = date('Y-m-d');
            $semester1End = ($selectedYear + 1) . '-02-20';
            $semester2End = ($selectedYear + 1) . '-06-20';
            $semester3End = ($selectedYear + 1) . '-09-20';

            if ($beginDate <= $today && $today <= $semester1End) {
                $semester = 'I';
            } elseif ($semester1End < $today && $today <= $semester2End) {
                $semester = 'II';
            } elseif ($semester2End < $today && $today <= $semester3End) {
                $semester = 'III';
            }
        }

        return [
            'academic_year' => $academicYear,
            'semester' => $semester
        ];
    }

    /**
     * Get the academic year for a given month and year.
     *
     * @param string|null $givenMonth Month in 'mm' format (e.g., '09').
     * @param string|null $givenYear Year in 'YYYY' format (e.g., '2024').
     * @return string|null Academic year in 'YYYY/YY' format or null if invalid input.
     */
    public function getAcademicYear(?string $givenMonth = null, ?string $givenYear = null)
    {
        if (empty($givenMonth) || empty($givenYear)) {
            return null;
        }

        $shortGivenYear = substr($givenYear, 2, 2);
        if (in_array($givenMonth, ['09', '10', '11', '12'])) {
            return $givenYear . '/' . ($shortGivenYear + 1);
        }

        return ($givenYear - 1) . '/' . $shortGivenYear;
    }

    /**
     * Get the beginning date of an academic year.
     *
     * @param string $academicYear Academic year in 'YYYY/YY' format.
     * @return string Date in 'YYYY-MM-DD' format.
     */
    public function getAcademicYearBeginningDate(string $academicYear, ?string $semester = null)
    {
        $givenYear = explode('/', $academicYear)[0];
        if (empty($givenYear)) {
            return date('Y-m-d');
        }

        if ($semester === 'I') {
            return $givenYear . '-09-20';
        } elseif ($semester === 'II') {
            return ($givenYear + 1) . '-06-20';
        } elseif ($semester === 'III') {
            return ($givenYear + 1) . '-08-20';
        }

        return $givenYear . '-09-20';
    }

    /**
     * Get the beginning date of the next academic year.
     *
     * @param string $academicYear Academic year in 'YYYY/YY' format.
     * @return string Date in 'YYYY-MM-DD' format.
     */
    public function nextAcademicYearBeginningDate(string $academicYear)
    {
        $givenYear = explode('/', $academicYear)[0];
        if (empty($givenYear)) {
            return date('Y-m-d');
        }

        return ($givenYear + 1) . '-09-20';
    }

    /**
     * Get equivalent program types for a given program type ID.
     *
     * @param int|null $programTypeId Program type ID.
     * @return array|int|null Array of equivalent program type IDs or original ID if none found.
     */
    public function equivalentProgramType(?int $programTypeId = null)
    {
        if (empty($programTypeId)) {
            return $programTypeId;
        }

        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $equivalentIds = unserialize($programTypesTable->field('equivalent_to_id', ['id' => $programTypeId]));

        if (!empty($equivalentIds)) {
            return array_merge([$programTypeId], $equivalentIds);
        }

        return $programTypeId;
    }

    /**
     * Generate an array of academic years for dropdowns.
     *
     * @return array Array of academic years in 'YYYY/YY' format.
     */
    public function academicYearArray()
    {
        $minYear = Configure::read('Calendar.universityEstablishment') ?? Configure::read('Calendar.applicationStartYear') ?? (date('Y') - 5);
        $maxYear = (int) date('Y');
        $this->academicYearArrayData = [];

        $minYearShort = (int) substr($minYear, 2, 2);
        $yearFront = substr($minYear, 0, 2);

        if ($yearFront == '19') {
            for ($i = $minYearShort; $i <= 99; $i++) {
                $key = '19' . sprintf('%02d', $i) . '/' . sprintf('%02d', $i + 1);
                $this->academicYearArrayData[$key] = $key;
            }
            $currentYear = (int) date('Y');
            $currentMonth = (int) date('m');
            if (in_array($currentMonth, [1, 2, 3, 4, 5, 6, 7, 8])) {
                $currentYear--;
            }
            $front2DigitThisYear = substr($currentYear, 0, 2);
            $shortThisYear = (int) substr($currentYear, 2, 2) + 1;

            for ($i = 0; $i <= $shortThisYear; $i++) {
                $key = $front2DigitThisYear . sprintf('%02d', $i) . '/' . sprintf('%02d', $i + 1);
                $this->academicYearArrayData[$key] = $key;
            }
        } elseif ($yearFront == '20') {
            $maxYearShort = (int) substr($maxYear, 2, 2);
            for ($i = $minYearShort; $i <= $maxYearShort; $i++) {
                $key = '20' . sprintf('%02d', $i) . '/' . sprintf('%02d', $i + 1);
                $this->academicYearArrayData[$key] = $key;
            }
        }

        krsort($this->academicYearArrayData);
        return $this->academicYearArrayData;
    }

    /**
     * Generate an array of academic years with minus-separated keys.
     *
     * @param string $beginYear Start year (e.g., '1986').
     * @param string $endYear End year (e.g., '2025').
     * @return array Array of academic years with minus-separated keys.
     */
    public function academicYearMinusSeparated(string $beginYear = '', string $endYear = '')
    {
        $this->academicYearMinusSeparated = [];

        if (empty($beginYear) && empty($endYear)) {
            $minYear = 1986;
            $maxYear = (int) date('Y');
            $currentMonth = (int) date('m');
            if (in_array($currentMonth, [1, 2, 3, 4, 5, 6, 7, 8])) {
                $maxYear--;
            }
        } else {
            $minYear = (int) $beginYear;
            $maxYear = (int) $endYear;
            $currentMonth = (int) date('m');
            if (in_array($currentMonth, [1, 2, 3, 4, 5, 6, 7, 8])) {
                $maxYear--;
            }
        }

        $front2DigitThisYear = substr($maxYear, 0, 2);
        $shortThisYear = (int) substr($maxYear, 2, 2);
        $minYearShort = (int) substr($minYear, 2, 2);

        for ($i = $minYearShort; $i <= $shortThisYear; $i++) {
            $key = $front2DigitThisYear . sprintf('%02d', $i) . '-' . sprintf('%02d', $i + 1);
            $value = $front2DigitThisYear . sprintf('%02d', $i) . '/' . sprintf('%02d', $i + 1);
            $this->academicYearMinusSeparated[$key] = $value;
        }

        krsort($this->academicYearMinusSeparated);
        return $this->academicYearMinusSeparated;
    }

    /**
     * Check if a date is valid and within a year range.
     *
     * @param string $dateString Date string in specified format.
     * @param int|null $minYear Minimum year.
     * @param string|null $maxYear Maximum year or academic year.
     * @param string $format Date format (default: 'Y-m-d').
     * @return bool True if valid and within range, false otherwise.
     */
    public function isValidDateWithinYearRange(
        string $dateString,
        ?int $minYear = null,
        ?string $maxYear = null,
        string $format = 'Y-m-d'
    ) {
        $minYear = $minYear ?? Configure::read('Calendar.universityEstablishment') ?? (date('Y') - 5);
        $maxYear = $maxYear ?? $this->currentAcademicYear();

        $dateTime = DateTime::createFromFormat($format, $dateString);
        if ($dateTime && $dateTime->format($format) === $dateString) {
            $year = (int) $dateTime->format('Y');
            $maxYearInt = (int) explode('/', $maxYear)[0];
            return $year >= $minYear && $year <= $maxYearInt;
        }

        return false;
    }
}
