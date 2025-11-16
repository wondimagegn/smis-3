<?php

namespace App\Utility;
/**
 * Utility class for array operations
 */
class ArrayHelper
{

    /**
     * Sort associative array with ordinal keys (1st, 2nd, 3rd, 10th, etc.)
     *
     * @param array $array Input array
     * @return array Sorted array
     */
    public static function sortOrdinals(array $array): array
    {

        uksort($array, function ($a, $b) {

            $numA = (int)filter_var($a, FILTER_SANITIZE_NUMBER_INT);
            $numB = (int)filter_var($b, FILTER_SANITIZE_NUMBER_INT);
            return $numA <=> $numB;
        });

        return $array;
    }
}
