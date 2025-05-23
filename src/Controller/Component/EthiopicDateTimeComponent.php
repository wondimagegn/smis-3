<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

class EthiopicDateTimeComponent extends Component
{

    public $Day = null;
    public $Month = null;
    public $Year = null;
    public $EthTime = null;
    public $ErrorMessage = null;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {

        parent::__construct($registry, $config);
    }

    public function initialize(array $config): void
    {

        parent::initialize($config);
    }


    public function _IsETLeapYear($intEthYear = null)
    {

        if ((abs(1999 - $intEthYear) % 4) == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function GetEthiopicDate($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $this->_SetEThiopicDate($intGCDay, $intGCMonth, $intGCYear);
        return $this->Day . "/" . $this->Month . "/" . $this->Year;
    }


    function GetGregorianDate($intEthDay = null, $intEthMonth = null, $intEthYear = null)
    {

        $this->_SetGCDate($intEthDay, $intEthMonth, $intEthYear);
        return $this->Year . "/" . $this->Month . "/" . $this->Day;
    }

    function GetGregorianDay($intEthDay = null, $intEthMonth = null, $intEthYear = null)
    {

        $this->_SetGCDate($intEthDay, $intEthMonth, $intEthYear);
        return $this->Day;
    }

    function GetGregorianMonth($intEthDay = null, $intEthMonth = null, $intEthYear = null)
    {

        $this->_SetGCDate($intEthDay, $intEthMonth, $intEthYear);
        return $this->Month;
    }

    function GetGregorianYear($intEthDay = null, $intEthMonth = null, $intEthYear = null)
    {

        $this->_SetGCDate($intEthDay, $intEthMonth, $intEthYear);
        return $this->Year;
    }

    function GetEthiopicDay($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $this->_SetEThiopicDate($intGCDay, $intGCMonth, $intGCYear);
        return $this->Day;
    }

    function GetEthiopicMonth($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $this->_SetEThiopicDate($intGCDay, $intGCMonth, $intGCYear);
        return $this->Month;
    }

    function GetEthiopicMonthName($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $e_month = $this->GetEthiopicMonth($intGCDay, $intGCMonth, $intGCYear);
        if ($e_month == 1) {
            return 'መስከረም';
        } elseif ($e_month == 2) {
            return 'ጥቅምት';
        } elseif ($e_month == 3) {
            return 'ህዳር';
        } elseif ($e_month == 4) {
            return 'ታህሳስ';
        } elseif ($e_month == 5) {
            return 'ጥር';
        } elseif ($e_month == 6) {
            return 'የካቲት';
        } elseif ($e_month == 7) {
            return 'መጋቢት';
        } elseif ($e_month == 8) {
            return 'ሚያዝያ';
        } elseif ($e_month == 9) {
            return 'ግንቦት';
        } elseif ($e_month == 10) {
            return 'ሰኔ';
        } elseif ($e_month == 11) {
            return 'ሐምሌ';
        } else {
            return 'ነሐሴ';
        }
    }

    public function GetEthiopicYear($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $this->_SetEThiopicDate($intGCDay, $intGCMonth, $intGCYear);
        return $this->Year;
    }

    public function _SetGCDate($intEthDay = null, $intEthMonth = null, $intEthYear = null)
    {

        $intDayDiff = 0;
        $intGCDay = 0;
        $intGCMonth = 0;
        $intGCYear = 0;
        $intAdd = 0;
        $intMax = 0;
        if ($this->_IsLeapYear($intEthYear + 8)) {
            $intAdd = 1;
        }

        switch ($intEthMonth) {
            case 1:
                $intGCMonth = 9;
                $intDayDiff = 10 + $intAdd;
                $intMax = 30;

                break;
            case 2:
                $intGCMonth = 10;
                $intDayDiff = 10 + $intAdd;
                $intMax = 31;

                break;
            case 3:
                $intGCMonth = 11;
                $intDayDiff = 9 + $intAdd;
                $intMax = 30;

                break;
            case 4:
                $intGCMonth = 12;
                $intDayDiff = 9 + $intAdd;
                $intMax = 31;

                break;
            case 5:
                $intGCMonth = 1;
                $intDayDiff = 8 + $intAdd;
                $intMax = 31;

                break;
            case 6:
                $intGCMonth = 2;
                $intDayDiff = 7 + $intAdd;
                if ($intAdd > 0) {
                    $intMax = 29;
                } else {
                    $intMax = 28;
                }

                break;
            case 7:
                $intGCMonth = 3;
                $intDayDiff = 9;
                $intMax = 31;

                break;
            case 8:
                $intGCMonth = 4;
                $intDayDiff = 8;
                $intMax = 30;

                break;
            case 9:
                $intGCMonth = 5;
                $intDayDiff = 8;
                $intMax = 31;

                break;
            case 10:
                $intGCMonth = 6;
                $intDayDiff = 7;
                $intMax = 30;

                break;
            case 11:
                $intGCMonth = 7;
                $intDayDiff = 7;
                $intMax = 31;

                break;
            case 12:
                $intGCMonth = 8;
                $intDayDiff = 6;
                $intMax = 31;

                break;
            case 13:
                $intGCMonth = 9;
                $intDayDiff = 5;
                $intMax = 30;

                break;
        }

        $intGCDay = $intEthDay + $intDayDiff;
        if ($intGCDay > $intMax) {
            $intGCDay = $intGCDay - $intMax;
            $intGCMonth = $intGCMonth + 1;
            if ($intGCMonth == 13) {
                $intGCMonth = 1;
            }
        }

        $intGCYear = $this->_GetGCYear($intEthMonth, $intEthYear, $intGCMonth);
        $this->Year = $intGCYear;
        $this->Month = $intGCMonth;
        $this->Day = $intGCDay;
    }

    function _SetEThiopicDate($intGCDay = null, $intGCMonth = null, $intGCYear = null)
    {

        $intDayDiff = 0;
        $intPagumen = 0;
        $intECDay = 0;
        $intECMonth = 0;
        $intECYear = 0;
//Get The Starting Month
        if ($intGCMonth > 8) {
            $intECMonth = $intGCMonth - 8;
        } else {
            $intECMonth = $intGCMonth + 4;
        }

        //Get no of days for Pagumen
        if ($this->_IsLeapYear($intGCYear + 1)) {
            $intPagumen = 6;
        } else {
            $intPagumen = 5;
        }

        //Get Date Difference
        $intDayDiff = $this->_GetDateDifference($intGCMonth, $intGCYear);
        if (($intGCMonth == 10) || ($intGCMonth == 11) || ($intGCMonth == 12)) {
            if ($this->_IsLeapYear($intGCYear + 1)) {
                $intDayDiff += 1;
            }

            $intECDay = $intGCDay - $intDayDiff;
            if ($intECDay <= 0) {
                $intECDay += 30;
                $intECMonth -= 1;
            }
        } elseif (($intGCMonth == 1) || ($intGCMonth == 2)) {
            if ($this->_IsLeapYear($intGCYear)) {
                $intDayDiff += 1;
            }

            $intECDay = $intGCDay - $intDayDiff;
            if ($intECDay <= 0) {
                $intECDay += 30;
                $intECMonth -= 1;
            }
        } elseif ($intGCMonth == 9) {
            if ($this->_IsLeapYear($intGCYear + 1)) {
                $intDayDiff = 11;
                $intPagumen = 6;
            } else {
                $intDayDiff = 10;
                $intPagumen = 5;
            }

            $intECDay = $intGCDay - $intDayDiff;
            if ($intECDay <= 0) {
                $intECDay = $intECDay + $intPagumen;
                if ($intECDay <= 0) {
                    $intECDay += 30;
                    $intECMonth = 12;
                } else {
                    $intECMonth = 13;
                }
            }
        } elseif (($intGCMonth == 3) || ($intGCMonth == 4) || ($intGCMonth == 5) || ($intGCMonth == 6) || ($intGCMonth == 7) || ($intGCMonth == 8)) {
            $intECDay = $intGCDay - $intDayDiff;
            if ($intECDay <= 0) {
                $intECDay += 30;
                $intECMonth -= 1;
            }
        }

        //Ethiopian Year
        $intECYear = $this->_GetETYear($intGCMonth, $intGCYear, $intECMonth);
        $this->Year = $intECYear;
        $this->Month = $intECMonth;
        $this->Day = $intECDay;
    }

    function _GetDateDifference($intGCMonth = null, $intGCYear = null)
    {

        $intDayDiff = null;
        switch ($intGCMonth) {
            case 8:
                $intDayDiff = 6;

                break;
            case 2:
            case 6:
            case 7:
                $intDayDiff = 7;

                break;
            case 1:
            case 4:
            case 5:
                $intDayDiff = 8;

                break;
            case 3:
            case 11:
            case 12:
                $intDayDiff = 9;

                break;
            case 10:
                $intDayDiff = 10;

                break;
            case 9:
                if ($this->_IsLeapYear($intGCYear + 1)) {
                    $intDayDiff = 11;
                } else {
                    $intDayDiff = 10;
                }

                break;
        }
        return $intDayDiff;
    }

    function _GetGCYear($intECMonth = null, $intECYear = null, $intGCMonth = null)
    {

        $intYearTemp = null;
        switch ($intGCMonth) {
            case 9:
            case 10:
            case 11:
            case 12:
                $intYearTemp = $intECYear + 7;
                if (($intGCMonth == 9) && (($intECMonth == 12) || ($intECMonth == 13))) {
                    $intYearTemp = $intYearTemp + 1;
                }

                break;
            default:
                $intYearTemp = $intECYear + 8;

                break;
        }
        return $intYearTemp;
    }

    function _GetETYear($intGCMonth = null, $intGCYear = null, $intECMonth = null)
    {

        $intYearTemp = null;
        switch ($intGCMonth) {
            case 9:
            case 10:
            case 11:
            case 12:
                $intYearTemp = $intGCYear - 7;
                if (($intGCMonth == 9) && (($intECMonth == 12) || ($intECMonth == 13))) {
                    $intYearTemp = $intYearTemp - 1;
                }

                break;
            default:
                $intYearTemp = $intGCYear - 8;

                break;
        }
        return $intYearTemp;
    }

    function GetETHTime($GCHour = null)
    {

        switch ($GCHour) {
            case "00":
                $EthTime = "00";

                break;
            case "01":
                $EthTime = "07";

                break;
            case "02":
                $EthTime = "08";

                break;
            case "03":
                $EthTime = "09";

                break;
            case "04":
                $EthTime = "10";

                break;
            case "05":
                $EthTime = "11";

                break;
            case "06":
                $EthTime = "12";

                break;
            case "07":
                $EthTime = "01";

                break;
            case "08":
                $EthTime = "02";

                break;
            case "09":
                $EthTime = "03";

                break;
            case "10":
                $EthTime = "04";

                break;
            case "11":
                $EthTime = "05";

                break;
            case "12":
                $EthTime = "06";

                break;
            case "13":
                $EthTime = "07";

                break;
            case "14":
                $EthTime = "08";

                break;
            case "15":
                $EthTime = "09";

                break;
            case "16":
                $EthTime = "10";

                break;
            case "17":
                $EthTime = "11";

                break;
            case "18":
                $EthTime = "12";

                break;
            case "19":
                $EthTime = "01";

                break;
            case "20":
                $EthTime = "02";

                break;
            case "21":
                $EthTime = "03";

                break;
            case "22":
                $EthTime = "04";

                break;
            case "23":
                $EthTime = "05";

                break;
        }
        return $EthTime;
    }

    function _isLeapYear($Iyear = null)
    {

        return date('L', mktime(0, 0, 0, 1, 1, $Iyear)) == 1 ? true : false;
    }
}
