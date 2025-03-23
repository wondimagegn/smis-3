<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\I18n\Time;
use Cake\Utility\Inflector;

/**
 * FormatHelper for date formatting and menu utilities in CakePHP 3.7
 */
class FormatHelper extends Helper
{
    /**
     * Other helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];
/**
     * Constructor
     *
     * @param \Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     */
    public function __construct(\Cake\View\View $view, array $config = [])
    {
        parent::__construct($view, $config);
    }

    /**
     * Formats a date into "Month Day, Year Hour:Minute:Second AM/PM"
     *
     * @param string|null $date Date string (e.g., "YYYY-MM-DD HH:MM:SS")
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDate($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMMM d, yyyy h:mm:ss a');
    }

    /**
     * Formats a date into "Mon Day, Year Hour:Minute:Second AM/PM"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDateShort2($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMM d, yy h:mm:ss a');
    }

    /**
     * Formats a date into "Mon Day, Year"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDateShort($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMM j, yy');
    }

    /**
     * Formats a date into "Mon Day, Year"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDateShortExtended($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMM d, yyyy');
    }

    /**
     * Formats a date into "Mon, Year"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDateShortExtendedTestDate($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMM, yyyy');
    }

    /**
     * Formats a date into "Month Day, Year"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function humanizeDateShortExtendedAll($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMMM d, yyyy');
    }

    /**
     * Formats a time into "Hour:Minute AM/PM"
     *
     * @param string|null $time Time string
     * @return string Formatted time or empty string if invalid
     */
    public function humanizeHour($time = null)
    {
        if (!$time || !strtotime($time)) {
            return '';
        }
        return Time::parse($time)->format('h:mm a');
    }

    /**
     * Formats a date into "Mon Day, Year"
     *
     * @param string|null $date Date string
     * @return string Formatted date or empty string if invalid
     */
    public function shortDate($date = null)
    {
        if (!$this->isValidDate($date)) {
            return '';
        }
        return Time::parse($date)->format('MMM d, yyyy');
    }

    /**
     * Checks if a menu item or its children is active
     *
     * @param array|null $submenu Submenu data
     * @param string|null $controller Current controller
     * @param string|null $action Current action
     * @return bool True if active, false otherwise
     */
    public function isActiveMenu($submenu = null, $controller = null, $action = null)
    {
        if (empty($submenu)) {
            return false;
        }

        $keys = array_keys($submenu);
        if (strcasecmp($keys[0], '0') !== 0) {
            $urlController = $submenu['url']['controller'] ?? '';
            $urlAction = $submenu['url']['action'] ?? '';
            if (
                (strcasecmp($urlController, $controller) === 0 ||
                    strcasecmp(Inflector::variable($urlController), $controller) === 0) &&
                strcasecmp($urlAction, $action) === 0
            ) {
                return true;
            } elseif (!empty($submenu['children'])) {
                return $this->isActiveMenu($submenu['children'], $controller, $action);
            }
            return false;
        }

        foreach ($submenu as $sv) {
            if ($this->isActiveMenu($sv, $controller, $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Renames menu titles based on a mapping array
     *
     * @param array|null &$menuoptimized Menu array (passed by reference)
     * @param array|null $renameMenuTitle Mapping of IDs to new titles
     * @return void
     */
    public function renameControllerTitle(&$menuoptimized = null, $renameMenuTitle = null)
    {
        if (empty($menuoptimized) || empty($renameMenuTitle)) {
            return;
        }

        foreach ($menuoptimized as $key => $subMenu) {
            if (array_key_exists($subMenu['id'], $renameMenuTitle)) {
                $menuoptimized[$key]['title'] = $renameMenuTitle[$subMenu['id']];
            }
            if (!empty($subMenu['children'])) {
                $this->renameControllerTitle($menuoptimized[$key]['children'], $renameMenuTitle);
            }
        }
    }

    /**
     * Generates HTML links for child menu items
     *
     * @param array $children Child menu items
     * @param bool $html Whether to return HTML (unused, kept for compatibility)
     * @return string|null HTML string of links or null if no children
     */
    public function getChildren($children, $html = true)
    {
        if (empty($children)) {
            return null;
        }

        $str = '';
        foreach ($children as $v) {
            $url = sprintf('/%s/%s', $v['url']['controller'], $v['url']['action']);
            $str .= $this->Html->link($v['url']['controller'], $url);
        }
        return $str;
    }

    /**
     * Returns a human-readable time difference (e.g., "2 hours")
     *
     * @param string $date Date string
     * @return string Human-readable time difference
     */
    public function humanTiming($date)
    {
        if (!$date || !strtotime($date)) {
            return '';
        }

        $convertedTime = strtotime($date);
        $time = max(time() - $convertedTime, 1);
        $tokens = [
            60 * 60 * 24 * 365 => 'year',
            60 * 60 * 24 * 30 => 'month',
            60 * 60 * 24 * 7 => 'week',
            60 * 60 * 24 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text;
        }
        return '1 second';
// Fallback
    }

    /**
     * Checks if the user has permission for a controller/action URL
     *
     * @param string $controllerActionUrl URL to check (e.g., "controller/action")
     * @return bool True if permitted, false otherwise
     */
    public function checkIfHasPermission($controllerActionUrl)
    {
        $permissionLists = $this->getView()->getRequest()->getSession()->read('permissionLists');
        return !empty($permissionLists) && in_array($controllerActionUrl, $permissionLists, true);
    }

    /**
     * Validates a date string
     *
     * @param string|null $date Date string
     * @return bool True if valid, false otherwise
     */
    protected function isValidDate($date)
    {
        return $date && Time::parse($date) !== null;
    }
}
