<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class StudyProgramsController extends AppController
{

    public $name = 'StudyPrograms';
    //var $helpers = array('Xls', 'Media.Media');
    //var $components = array('AcademicYear', 'EthiopicDateTime');

    public $menuOptions = array(
        'parent' => 'curriculums',
        'exclude' => array(//'index',
        ),
        'alias' => array(
            'index' => 'List Study Programs',
            'add' => 'Add Study Program',
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow('index');
    }


    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$thisacademicyear = $this->AcademicYear->current_academicyear();
    }

    public function index()
    {

        //$this->StudyProgram->recursive = 0;

        $this->Paginator->settings = array(
            'limit' => 100,
            'maxLimit' => 1000,
            'order' => array('StudyProgram.study_program_name' => 'ASC', 'StudyProgram.code', 'Section.ISCED_band'),
            'recursive' => 0
        );

        $this->set('studyPrograms', $this->paginate('StudyProgram'));
    }

    public function view($id = null)
    {
    }

    public function add()
    {
    }

    public function edit($id = null)
    {
    }

    public function delete($id = null)
    {
    }
}
