<?php
namespace Acls\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Exception\Exception;

class AclsAppController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Validate required components are loaded
        $required = ['Auth', 'Acl', 'Session'];
        foreach ($required as $component) {
            if (!$this->components()->has($component)) {
                throw new Exception("The {$component}Component is not enabled in your AppController.");
            }
        }

        // Ensure root ACO node exists
        $actionPath = $this->Auth->getConfig('actionPath') ?? 'controllers/';
        $rootAlias = rtrim($actionPath, '/');

        $AcoTable = $this->fetchTable('ArosAcos.Acos'); // Adjust this if not using plugin alias
        $rootNode = $AcoTable->find()
            ->where(['parent_id IS' => null, 'alias' => $rootAlias])
            ->first();

        if (!$rootNode) {
            $newAco = $AcoTable->newEntity(['parent_id' => null, 'alias' => $rootAlias]);
            $AcoTable->save($newAco);
        }

        // Optionally call: $this->_authorizeAdmins();
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setLayout('default');
    }
}
