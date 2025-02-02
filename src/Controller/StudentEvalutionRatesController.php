<?php
App::uses('AppController', 'Controller');
class StudentEvalutionRatesController extends AppController
{
	public $menuOptions = array(
		'parent' => 'evalution',
		'exclude' => array('index'),
		'alias' => array(
			'add' => 'Evaluate Your Instructors',
		)
	);

	
	public $components = array('Paginator');

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->Allow();
	}


	public function index()
	{
		return $this->redirect(array('action' => 'add'));
	}

	public function view($id = null)
	{
		if (!$this->StudentEvalutionRate->exists($id)) {
			throw new NotFoundException(__('Invalid student evalution rate'));
		}

		$options = array('conditions' => array('StudentEvalutionRate.' . $this->StudentEvalutionRate->primaryKey => $id));
		$this->set('studentEvalutionRate', $this->StudentEvalutionRate->find('first', $options));
	}

	public function add()
	{
		$this->layout = 'default_nobackrefresh';

		if ($this->request->is('post')) {

			$isInstructorEvaluated = $this->StudentEvalutionRate->find('count', array(
				'conditions' => array(
					'StudentEvalutionRate.student_id' => $this->request->data['StudentEvalutionRate'][1]['student_id'],
					'StudentEvalutionRate.published_course_id' => $this->request->data['StudentEvalutionRate'][1]['published_course_id'],
				)
			));

			if ($isInstructorEvaluated == 0) {
				$this->StudentEvalutionRate->create();
				//debug($this->request->data['StudentEvalutionRate']);
				if ($this->StudentEvalutionRate->saveAll($this->request->data['StudentEvalutionRate'])) {
					if ($this->StudentEvalutionRate->InstructorEvalutionQuestion->StudentEvalutionComment->saveAll($this->request->data['StudentEvalutionComment'])) {
						// nothig to process here.
					}
					$this->Flash->success( __('Thank you!,  You have evaluated '. $this->request->data['Instructor']['full_name'].'. Please fill the next instructor evaluation.'));
					return $this->redirect(array('action' => 'add'));
				} else {
					$this->Flash->error( __('The student evalution rate could not be saved. Please, try again.'));
				}
			} else {
				$this->Flash->warning(__("You have already evaluated " . $this->request->data['Instructor']['full_name']. ", you don not need to evaluate again."));
			}
		}

		$courseList = $this->StudentEvalutionRate->getNotEvaluatedRegisteredCourse($this->student_id);

		//debug($courseList);
		
		if (empty($courseList)) {
			
			$this->Flash->success(__('Thank you for completing instructor evaluation. You can view your grade now.'));
			$ac = $this->StudentEvalutionRate->getACSem($this->student_id);

			if (!empty($ac['academicYear'])) {
				$academicYear = str_replace("/", "-", $ac['academicYear']);
				$semester = $ac['semester'];
				return $this->redirect(array('controller' => 'exam_grades', 'action' => "student_grade_view/$academicYear/$academicYear/$semester"));
			}

			return $this->redirect(array('controller' => 'exam_grades', 'action' => "student_grade_view"));
		}

		$instructorEvalutionQuestionsObjective = $this->StudentEvalutionRate->InstructorEvalutionQuestion->find('all', array(
			'conditions' => array(
				'InstructorEvalutionQuestion.type' => 'objective',
				'InstructorEvalutionQuestion.for' => 'student',
				'InstructorEvalutionQuestion.active' => 1

			), 
			'fields' => array(
				'InstructorEvalutionQuestion.id',
				'InstructorEvalutionQuestion.question',
				'InstructorEvalutionQuestion.question_amharic'
			),
			'recursive' => -1
		));

		$instructorEvalutionQuestionsOpenEnded = $this->StudentEvalutionRate->InstructorEvalutionQuestion->find('all', array(
			'conditions' => array(
				'InstructorEvalutionQuestion.type' => 'open-ended',
				'InstructorEvalutionQuestion.for' => 'student',
				'InstructorEvalutionQuestion.active' => 1

			),
			'fields' => array(
				'InstructorEvalutionQuestion.id',
				'InstructorEvalutionQuestion.question',
				'InstructorEvalutionQuestion.question_amharic',
			),
			'recursive' => -1,
		));

		$this->set(compact('instructorEvalutionQuestionsObjective', 'instructorEvalutionQuestionsOpenEnded', 'courseList'));
	}
}
