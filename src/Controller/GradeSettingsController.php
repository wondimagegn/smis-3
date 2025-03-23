<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class GradeSettingsController extends AppController
{
    public $name = 'GradeSettings';
    public $uses = array();

    public $menuOptions = array(
        'parent' => 'grades',
        'exclude' => array('index'),
    );

    public $paginate =[];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');

        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
    function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    function index()
    {
    }
}
