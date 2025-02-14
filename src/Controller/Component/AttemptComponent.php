<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;

class AttemptComponent extends Component
{
    protected $_defaultConfig = [];
    protected $AttemptsTable;
    public $request;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->AttemptsTable = TableRegistry::getTableLocator()->get('Attempts');
        $this->request = $this->getController()->getRequest();
    }

    public function count($username, $action)
    {
        return $this->AttemptsTable->countAttempts($this->getClientIP(), $username, $action);
    }

    public function limit($username, $action, $limit = 5)
    {
        return $this->AttemptsTable->isLimitReached($this->getClientIP(), $username, $action, $limit);
    }

    public function fail($username, $action, $duration = '+10 minutes')
    {
        return $this->AttemptsTable->recordFailure($this->getClientIP(), $username, $action, $duration);
    }

    public function reset($username, $action)
    {
        return $this->AttemptsTable->resetAttempts($this->getClientIP(), $username, $action);
    }

    public function cleanup()
    {
        return $this->AttemptsTable->cleanupOldAttempts();
    }

    private function getClientIP()
    {
        return $this->request->clientIp();
    }
}
