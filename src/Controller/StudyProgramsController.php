<?php
class StudyProgramsController extends AppController
{
	var $name = 'StudyPrograms';
	//var $helpers = array('Xls', 'Media.Media');
	//var $components = array('AcademicYear', 'EthiopicDateTime');

	public $menuOptions = array(
		'parent' => 'curriculums',
		'exclude' => array(
			//'index',
		),
		'alias' => array(
			'index' => 'List Study Programs',
            'add' => 'Add Study Program',
		)
	);

	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow('index');
	}

	public function beforeRender()
	{
		//$thisacademicyear = $this->AcademicYear->current_academicyear();
	}

	public function index()
	{
		//$this->StudyProgram->recursive = 0;

		$this->Paginator->settings =  array('limit' => 100, 'maxLimit' => 1000, 'order' => array('StudyProgram.study_program_name' => 'ASC', 'StudyProgram.code', 'Section.ISCED_band'), 'recursive' => 0);

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
