<?php
namespace Acls\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Hash;

class AclsAppController extends AppController
{
    /**
     * Initialization method
     */
    public function initialize(): void
    {
        parent::initialize();

        // ✅ Load Components
        $this->loadComponent('Auth', [
            'authorize' => 'Controller', // Optional: 'Actions' can be used
            'loginAction' => ['controller' => 'Users', 'action' => 'login'],
            'logoutRedirect' => ['controller' => 'Users', 'action' => 'login'],
            'authError' => 'You are not authorized to access that location.'
        ]);

        $this->loadComponent('CakeDC/ACL.Acl');
        $this->loadComponent('Flash');
        $this->loadComponent('RequestHandler');

        // ✅ Load Helpers
        $this->loadHelper('Html');
        $this->loadHelper('Form');
        $this->loadHelper('Session');
    }

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // ✅ Collect Loaded Components
        $components = Hash::normalize($this->components()->loaded());

        // ✅ Check if Required Components Are Loaded
        if (!array_key_exists('Auth', $components)) {
            die('The AuthComponent is not enabled in your AppController.');
        }

        if (!array_key_exists('Acl', $components)) {
            die('The AclComponent is not enabled in your AppController.');
        }

        if (!array_key_exists('Flash', $components)) {
            die('The FlashComponent is not enabled in your AppController.');
        }

        // ✅ Verify ACL Root Node Exists
        $rootNodeAlias = 'controllers';
        $rootNode = $this->Acl->Aco->find()->where(['parent_id IS' => null, 'alias' => $rootNodeAlias])->first();

        if (empty($rootNode)) {
            $aco = $this->Acl->Aco->newEntity(['parent_id' => null, 'alias' => $rootNodeAlias]);
            $this->Acl->Aco->save($aco);
        }
    }
}
