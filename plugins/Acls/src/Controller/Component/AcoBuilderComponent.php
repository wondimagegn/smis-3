<?php
namespace Acls\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;

class AcoBuilderComponent extends Component
{
    public $components = ['Acl'];

    public function buildAcl()
    {
        if (!Configure::read('debug')) {
            return $this->_stop();
        }

        $log = [];
        $aco = $this->Acl->Aco; // Reference to AclComponent's Aco table

        $root = $aco->node('controllers')->first();
        if (!$root) {
            $acoEntity = $aco->newEntity([
                'parent_id' => null,
                'model' => null,
                'alias' => 'controllers',
            ]);
            $aco->save($acoEntity);
            $root = $aco->get($acoEntity->id);
            $log[] = 'Created Aco node for controllers';
        }

        $controllers = $this->_getControllerNames();
        $baseMethods = get_class_methods(\Cake\Controller\Controller::class);
        $baseMethods[] = 'buildAcl';

        $pluginControllers = $this->_getPluginControllerNames();
        $controllers = array_merge($controllers, $pluginControllers);

        foreach ($controllers as $ctrlName) {
            $methods = $this->_getClassMethods($this->_getPluginControllerPath($ctrlName));

            if ($this->_isPlugin($ctrlName)) {
                $pluginNode = $aco->node('controllers/' . $this->_getPluginName($ctrlName))->first();
                if (!$pluginNode) {
                    $pluginEntity = $aco->newEntity([
                        'parent_id' => $root->id,
                        'model' => null,
                        'alias' => $this->_getPluginName($ctrlName),
                    ]);
                    $aco->save($pluginEntity);
                    $pluginNode = $aco->get($pluginEntity->id);
                    $log[] = 'Created Aco node for ' . $this->_getPluginName($ctrlName) . ' Plugin';
                }
            }

            $controllerNode = $aco->node('controllers/' . $ctrlName)->first();
            if (!$controllerNode) {
                if ($this->_isPlugin($ctrlName)) {
                    $pluginNode = $aco->node('controllers/' . $this->_getPluginName($ctrlName))->first();
                    $controllerEntity = $aco->newEntity([
                        'parent_id' => $pluginNode->id,
                        'model' => null,
                        'alias' => $this->_getPluginControllerName($ctrlName),
                    ]);
                    $aco->save($controllerEntity);
                    $controllerNode = $aco->get($controllerEntity->id);
                    $log[] = 'Created Aco node for ' . $this->_getPluginControllerName($ctrlName) . ' ' . $this->_getPluginName($ctrlName) . ' Plugin Controller';
                } else {
                    $controllerEntity = $aco->newEntity([
                        'parent_id' => $root->id,
                        'model' => null,
                        'alias' => $ctrlName,
                    ]);
                    $aco->save($controllerEntity);
                    $controllerNode = $aco->get($controllerEntity->id);
                    $log[] = 'Created Aco node for ' . $ctrlName;
                }
            }

            foreach ($methods as $k => $method) {
                if (strpos($method, '_', 0) === 0 || in_array($method, $baseMethods)) {
                    unset($methods[$k]);
                    continue;
                }
                $methodNode = $aco->node('controllers/' . $ctrlName . '/' . $method)->first();
                if (!$methodNode) {
                    $methodEntity = $aco->newEntity([
                        'parent_id' => $controllerNode->id,
                        'model' => null,
                        'alias' => $method,
                    ]);
                    $aco->save($methodEntity);
                    $log[] = 'Created Aco node for ' . $method;
                }
            }
        }

        if (!empty($log)) {
            // Log or debug $log as needed, e.g., \Cake\Log\Log::debug($log);
        }
    }

    protected function _getClassMethods($ctrlName = null)
    {
        [$plugin, $ctrl] = pluginSplit($ctrlName);
        $className = $ctrl . 'Controller';
        if ($plugin) {
            $className = $plugin . '.' . $className;
        }

        if (!class_exists($className)) {
            try {
                $this->getController()->loadController($className);
            } catch (\Exception $e) {
                return [];
            }
        }

        $methods = get_class_methods($className);
        if (!$methods) {
            return [];
        }

        $properties = get_class_vars($className);
        if (isset($properties['scaffold'])) {
            if ($properties['scaffold'] === 'admin') {
                $methods = array_merge($methods, ['admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete']);
            } else {
                $methods = array_merge($methods, ['add', 'edit', 'index', 'view', 'delete']);
            }
        }

        return $methods;
    }

    protected function _isPlugin($ctrlName = null)
    {
        return strpos($ctrlName, '/') !== false;
    }

    protected function _getPluginControllerPath($ctrlName = null)
    {
        [$plugin, $ctrl] = pluginSplit($ctrlName, true);
        return $plugin . $ctrl;
    }

    protected function _getPluginName($ctrlName = null)
    {
        [$plugin] = pluginSplit($ctrlName);
        return $plugin ?: false;
    }

    protected function _getPluginControllerName($ctrlName = null)
    {
        [, $ctrl] = pluginSplit($ctrlName);
        return $ctrl ?: false;
    }

    protected function _getControllerNames()
    {
        $controllers = [];
        $folder = new Folder(APP . 'Controller');
        $files = $folder->find('.*Controller\.php');
        foreach ($files as $file) {
            $controller = str_replace('Controller.php', '', $file);
            if ($controller !== 'App') {
                $controllers[] = $controller;
            }
        }
        return $controllers;
    }

    protected function _getPluginControllerNames()
    {
        $plugins = [];
        $folder = new Folder(APP . 'plugins');
        $pluginDirs = $folder->read()[0];

        foreach ($pluginDirs as $pluginName) {
            $controllerFolder = new Folder(APP . 'plugins' . DS . $pluginName . DS . 'Controller');
            $files = $controllerFolder->find('.*Controller\.php');
            foreach ($files as $file) {
                $controller = str_replace('Controller.php', '', $file);
                if (!preg_match('/^' . Inflector::humanize($pluginName) . 'App/', $controller)) {
                    $plugins[] = Inflector::humanize($pluginName) . '/' . $controller;
                }
            }
        }
        return $plugins;
    }

    protected function _stop()
    {
        $this->getController()->response = $this->getController()->response->withStatus(403);
        throw new \Cake\Http\Exception\ForbiddenException('Debug mode required');
    }
}
