<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Utility\Inflector;

/**
 * LinkHelper for handling menu links and active states in CakePHP 3.7
 */
class LinkHelper extends Helper
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
            // Non-numeric keys (associative array)
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

        // Numeric keys (indexed array)
        foreach ($submenu as $sv) {
            if ($this->isActiveMenu($sv, $controller, $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generates HTML links for child menu items
     *
     * @param array $children Child menu items
     * @param bool $html Whether to return HTML (unused in original, kept for compatibility)
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
}
