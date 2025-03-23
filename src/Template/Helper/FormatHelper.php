<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;

class FormatHelper extends Helper
{
    public $helpers = ['Session', 'Html'];

    public function humanizeDate($date = null)
    {
        return (new FrozenTime($date))->format("F d, Y h:i:s A");
    }

    public function humanizeDateShort2($date = null)
    {
        return (new FrozenTime($date))->format("M d, y h:i:s A");
    }

    public function humanizeDateShort($date = null)
    {
        return (new FrozenTime($date))->format("M j, y");
    }

    public function humanizeDateShortExtended($date = null)
    {
        return (new FrozenTime($date))->format("M d, Y");
    }

    public function humanizeDateShortExtendedTestDate($date = null)
    {
        return (new FrozenTime($date))->format("M, Y");
    }

    public function humanizeDateShortExtendedAll($date = null)
    {
        return (new FrozenTime($date))->format("F d, Y");
    }

    public function humanizeHour($time = null)
    {
        return (new FrozenTime($time))->format("h:i A");
    }

    public function shortDate($date = null)
    {
        return (new FrozenTime($date))->format("M d, Y");
    }

    public function isActiveMenu($submenu = null, $controller = null, $action = null)
    {
        if (!empty($submenu)) {
            foreach ($submenu as $key => $item) {
                if (isset($item['url']['controller'], $item['url']['action']) &&
                    strcasecmp($item['url']['controller'], $controller) == 0 &&
                    strcasecmp($item['url']['action'], $action) == 0) {
                    return true;
                }
                if (!empty($item['children']) && $this->isActiveMenu($item['children'], $controller, $action)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function renameControllerTitle(&$menuOptimized, $renameMenuTitle)
    {
        foreach ($menuOptimized as &$subMenu) {
            if (isset($renameMenuTitle[$subMenu['id']])) {
                $subMenu['title'] = $renameMenuTitle[$subMenu['id']];
            }
            if (!empty($subMenu['children'])) {
                $this->renameControllerTitle($subMenu['children'], $renameMenuTitle);
            }
        }
    }

    public function getChildren($children)
    {
        $str = '';
        foreach ($children as $child) {
            $str .= $this->Html->link($child['url']['controller'], '/' . $child['url']['controller'] . '/' . $child['url']['action']);
        }
        return $str;
    }

    public function humanTiming($date)
    {
        $convertedTime = strtotime($date);
        $time = time() - $convertedTime;
        $time = max($time, 1);

        $tokens = [
            60 * 60 * 24 * 365 => 'year',
            60 * 60 * 24 * 30  => 'month',
            60 * 60 * 24 * 7   => 'week',
            60 * 60 * 24       => 'day',
            60 * 60            => 'hour',
            60                 => 'minute',
            1                  => 'second',
        ];

        foreach ($tokens as $unit => $text) {
            if ($time >= $unit) {
                $numberOfUnits = floor($time / $unit);
                return $numberOfUnits . ' ' . $text . ($numberOfUnits > 1 ? 's' : '');
            }
        }
    }

    public function checkIfHasPermission($controllerActionUrl)
    {
        $permissionLists = $this->getView()->getRequest()->getSession()->read('permissionLists');
        return in_array($controllerActionUrl, (array) $permissionLists, true);
    }
}
