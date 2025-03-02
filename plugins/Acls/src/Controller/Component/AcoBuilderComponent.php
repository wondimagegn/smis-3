<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;

class AcoBuilderComponent extends Component
{
    public $components = ['Acl'];

    protected $Aco;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->Aco = TableRegistry::getTableLocator()->get('Acos');
    }

    public function buildAcl()
    {
        if (!Configure::read('debug')) {
            return;
        }

        $log = [];
        $aco = $this->Aco;

        $root = $aco->find()->where(['alias' => 'controllers'])->first();

        if (!$root) {
            $root = $aco->newEntity(['parent_id' => null, 'model' => null, 'alias' => 'controllers']);
            $aco->save($root);
            $log[] = 'Created Aco node for controllers';
        }

        $controllers = $this->_getControllerNames();
        $baseMethods = get_class_methods('Cake\Controller\Controller');
        $baseMethods[] = 'buildAcl';

        foreach ($controllers as $ctrlName) {
            $methods = $this->_getClassMethods($ctrlName);

            $controllerNode = $aco->find()->where(['alias' => $ctrlName, 'parent_id' => $root->id])->first();
            if (!$controllerNode) {
                $controllerNode = $aco->newEntity(['parent_id' => $root->id, 'model' => null, 'alias' => $ctrlName]);
                $aco->save($controllerNode);
                $log[] = 'Created Aco node for ' . $ctrlName;
            }

            foreach ($methods as $method) {
                if (strpos($method, '_') === 0 || in_array($method, $baseMethods)) {
                    continue;
                }

                $methodNode = $aco->find()->where(['alias' => $method, 'parent_id' => $controllerNode->id])->first();
                if (!$methodNode) {
                    $methodNode = $aco->newEntity(['parent_id' => $controllerNode->id, 'model' => null, 'alias' => $method]);
                    $aco->save($methodNode);
                    $log[] = 'Created Aco node for ' . $method;
                }
            }
        }

        if (count($log) > 0) {
            debug($log);
        }
    }

    private function _getControllerNames()
    {
        $controllers = [];
        $controllerDir = new Folder(APP . 'Controller');
        $files = $controllerDir->findRecursive('.*Controller\.php');

        foreach ($files as $file) {
            $file = basename($file, '.php');
            if ($file !== 'AppController') {
                $controllers[] = str_replace('Controller', '', $file);
            }
        }

        return $controllers;
    }

    private function _getClassMethods($ctrlName = null)
    {
        $controllerClass = 'App\\Controller\\' . $ctrlName . 'Controller';
        if (!class_exists($controllerClass)) {
            return [];
        }

        $methods = get_class_methods($controllerClass);

        $properties = get_class_vars($controllerClass);
        if (array_key_exists('scaffold', $properties)) {
            $methods = $this->_addScaffoldMethods($properties['scaffold'], $methods);
        }

        return $methods;
    }

    private function _addScaffoldMethods($scaffold, $methods)
    {
        if ($scaffold === 'admin') {
            $methods = array_merge($methods, ['admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete']);
        } else {
            $methods = array_merge($methods, ['add', 'edit', 'index', 'view', 'delete']);
        }
        return $methods;
    }

    private function _getPluginControllerNames()
    {
        $plugins = array_map('basename', glob(ROOT . '/plugins/*', GLOB_ONLYDIR));
        $controllers = [];

        foreach ($plugins as $plugin) {
            $pluginControllerPath = ROOT . '/plugins/' . $plugin . '/src/Controller';
            $folder = new Folder($pluginControllerPath);
            $files = $folder->findRecursive('.*Controller\.php');

            foreach ($files as $file) {
                $file = basename($file, '.php');
                if ($file !== $plugin . 'AppController') {
                    $controllers[] = $plugin . '/' . str_replace('Controller', '', $file);
                }
            }
        }

        return $controllers;
    }
}
