<?php

namespace App\Utility;

use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use DateTime;

class AcademicYear
{
    public static function currentAcademicYear(): string
    {
        $thisYear = date('Y');
        $thisMonth = date('m');
        $shortThisYear = substr($thisYear, 2, 2);

        return in_array($thisMonth, ["09", "10", "11", "12"])
            ? $thisYear . '/' . ($shortThisYear + 1)
            : ($thisYear - 1) . '/' . $shortThisYear;
    }

    public static function currentAcyAndSemester(): array
    {
        $thisYear = date('Y');
        $thisMonth = date('m');
        $shortThisYear = substr($thisYear, 2, 2);
        $semester = 'I';

        $acYear = in_array($thisMonth, ['09', '10', '11', '12'])
            ? $thisYear . '/' . ($shortThisYear + 1)
            : ($thisYear - 1) . '/' . $shortThisYear;

        $result = ['academic_year' => $acYear];
        $startDate = self::getAcademicYearBeginningDate($acYear);
        $parts = explode('-', $startDate);

        if (!empty($parts[0])) {
            $today = date('Y-m-d');
            $s1End = ($parts[0] + 1) . '-02-20';
            $s2End = ($parts[0] + 1) . '-06-20';
            $s3End = ($parts[0] + 1) . '-09-20';

            if ($startDate <= $today && $today <= $s1End) $semester = 'I';
            elseif ($s1End < $today && $today <= $s2End) $semester = 'II';
            elseif ($s2End < $today && $today <= $s3End) $semester = 'III';

            $result['semester'] = $semester;
        }

        return $result;
    }

    public static function getAcademicYear($month = null, $year = null): ?string
    {
        if ($month && $year) {
            $short = substr($year, 2, 2);
            return in_array($month, ["09", "10", "11", "12"])
                ? $year . '/' . ($short + 1)
                : ($year - 1) . '/' . $short;
        }
        return null;
    }

    public static function getAcademicYearBeginningDate($academicYear): string
    {
        $parts = explode('/', $academicYear);
        return !empty($parts[0]) ? $parts[0] . '-09-20' : date('Y-m-d');
    }

    public static function nextAcademicYearBeginningDate($academicYear): string
    {
        $parts = explode('/', $academicYear);
        return !empty($parts[0]) ? ($parts[0] + 1) . '-09-20' : date('Y-m-d');
    }

    public static function getAcademicYearBeginningDateBySemester($academicYear, $semester): string
    {
        $parts = explode('/', $academicYear);
        if (!empty($parts[0])) {
            switch ($semester) {
                case 'I': return $parts[0] . '-09-20';
                case 'II': return ($parts[0] + 1) . '-06-20';
                case 'III': return ($parts[0] + 1) . '-08-20';
            }
        }
        return date('Y-m-d');
    }

    public static function equivalentProgramType($programTypeId = null): array
    {
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $raw = $programTypeTable->find()
            ->select(['equivalent_to_id'])
            ->where(['id' => $programTypeId])
            ->first()
            ->equivalent_to_id ?? '';

        $list = unserialize($raw);
        return !empty($list) ? array_merge([$programTypeId], $list) : [$programTypeId];
    }

    public static function acyearArray(): array
    {
        $data = [];
        $minYear = Configure::read('Calendar.universityEstablishement')
            ?? Configure::read('Calendar.applicationStartYear')
            ?? date('Y') - 5;

        $minShort = substr($minYear, 2, 2);
        $prefix = substr($minYear, 0, 2);

        $thisYear = date('Y');
        $month = date('m');
        if (in_array($month, range(1, 8))) $thisYear--;

        $shortMax = substr($thisYear, 2, 2) + 1;
        $front = substr($thisYear, 0, 2);

        for ($i = $minShort; $i <= $shortMax; $i++) {
            $data["{$front}{$i}/" . ($i + 1)] = "{$front}{$i}/" . ($i + 1);
        }

        arsort($data);
        return $data;
    }

    public static function acYearMinuSeparated($beginYear = '', $endYear = ''): array
    {
        $data = [];

        if (!$beginYear && !$endYear) {
            for ($i = 86; $i <= 99; $i++) {
                $data["19{$i}-" . sprintf('%02d', $i + 1)] = "19{$i}/" . sprintf('%02d', $i + 1);
            }
            $endYear = date('Y');
            if (in_array(date('m'), range(1, 8))) $endYear--;
        }

        $front = substr($endYear, 0, 2);
        $start = substr($beginYear ?: $endYear, 2, 2);
        $stop = substr($endYear, 2, 2);

        for ($i = $start; $i <= $stop; $i++) {
            $s = sprintf('%02d', $i);
            $data["{$front}{$s}-" . sprintf('%02d', $i + 1)] = "{$front}{$s}/" . sprintf('%02d', $i + 1);
        }

        arsort($data);
        return $data;
    }

    public static function academicYearInArray($beginYear, $endYear): array
    {
        $data = [];
        $thisYear = $endYear;
        if (in_array(date('m'), range(1, 8))) $thisYear--;

        $prefix = substr($thisYear, 0, 2);
        $start = substr($beginYear, 2, 2);
        $stop = substr($endYear, 2, 2);

        for ($i = $start; $i <= $stop; $i++) {
            $s = sprintf('%02d', $i);
            $data["{$prefix}{$s}/" . sprintf('%02d', $i + 1)] = "{$prefix}{$s}/" . sprintf('%02d', $i + 1);
        }

        arsort($data);
        return $data;
    }

    public static function isValidDateWithinYearRange($dateString, $minYear = null, $maxYear = null, $format = 'Y-m-d'): bool
    {
        $minYear = $minYear ?? Configure::read('Calendar.universityEstablishement');
        $maxYear = $maxYear ?? self::currentAcademicYear();

        $dt = DateTime::createFromFormat($format, $dateString);

        if ($dt && $dt->format($format) === $dateString) {
            $year = (int)$dt->format('Y');
            return $year >= $minYear && $year <= $maxYear;
        }

        return false;
    }
}
