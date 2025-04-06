<?php
namespace Acls\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
class AppController extends Controller
{
    public $helpers = [
        'Html',
        'Form',
        'Flash', // Replaces Session helper for flash messages
    ];

    public function initialize(): void
    {
        parent::initialize();

        // Load required components
        $this->loadComponent('Auth');
        $this->loadComponent('Acls.Acl'); // Custom AclComponent from the plugin
        $this->loadComponent('Flash'); // Replaces SessionComponent for flash messages

        // Optionally configure Auth (uncomment and adjust as needed)
        // $this->Auth->setConfig('authorize', ['Actions']);
        // $this->Auth->setConfig('actionPath', 'controllers/');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Debug components (for development, remove in production)
        $components = array_keys($this->components()->loaded());
        // \Cake\Log\Log::debug($components); // Uncomment to log instead of debug()

        // Check for required components
        if (!in_array('Auth', $components)) {
            throw new \RuntimeException('The AuthComponent is not enabled in your AppController.');
        }
        if (!in_array('Acl', $components)) {
            throw new \RuntimeException('The AclComponent is not enabled in your AppController.');
        }
        if (!in_array('Flash', $components)) {
            throw new \RuntimeException('The FlashComponent is not enabled in your AppController.');
        }

        // Ensure root ACO node exists
        $rootNode = $this->Acl->Aco->find()
            ->where([
                'parent_id IS' => null,
                'alias' => rtrim($this->Auth->getConfig('actionPath', 'controllers/'), '/'),
            ])
            ->first();

        if (empty($rootNode)) {
            $acoEntity = $this->Acl->Aco->newEntity([
                'parent_id' => null,
                'alias' => rtrim($this->Auth->getConfig('actionPath', 'controllers/'), '/'),
            ]);
            $this->Acl->Aco->save($acoEntity);
        }
    }
}
