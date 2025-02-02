<?php
class PlacementPreferencesController extends AppController
{
	public $name = 'PlacementPreferences';
	public $menuOptions = array(
		'parent' => 'placement',
		'exclude' => array(
			'edit_preference',
			'auto_fill_preference',
			'get_selected_participant',
			'get_selected_student',
			'auto_fill_preference',
			'autoSaveResult',
		),
		'alias' => array(
			'index' => 'List Preference',
			'add' => 'Add Preference on Behalf of Student',
			'record_preference' => 'Record Your Preference',
			'view_result_of_placement' => 'View Your Placement Result'
		),
	);

	public $paginate = array();
	public $components = array('AcademicYear', 'RequestHandler');

	public function beforeRender()
	{

		parent::beforeRender();
		
		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		$defaultacademicyear = $current_academicyear = $this->AcademicYear->current_academicyear();

		if (empty($availableAcademicYears)) {
			//$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - 2), (explode('/', $current_academicyear)[0]));
			$acyear_array_data[$current_academicyear] = $current_academicyear;
		} else {
			$acyear_array_data = $availableAcademicYears;
		}
		
		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
		//$this->set('defaultacademicyear', $defaultacademicyear);
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
		
		$this->Auth->Allow(
			'getStudentPreference'
		);

		/* $this->Auth->Allow(
			'record_preference',
			'index',
			'add',
			'get_selected_participant',
			'get_selected_student',
			'auto_fill_preference',
			'autoSaveResult'
		); */
		
	}

	function __init_search_preferences()
	{
		if (!empty($this->request->data['PlacementPreference'])) {
			$search_session = $this->request->data['PlacementPreference'];
			$this->Session->write('search_preferences', $search_session);
		} else {
			if ($this->Session->check('search_preferences')) {
				$search_session = $this->Session->read('search_preferences');
				$this->request->data['PlacementPreference'] = $search_session;
			}
		}
	}

	public function index($academic_year = null, $suffix = null)
	{
		
		$acYear = $selectedAcy = $this->AcademicYear->current_academicyear();
		$selectedRound = 1;
		$selectedCurrentUnit = '';
		$selectedProgID = 1;
		$selectedProgTypeID = 1;
		$selectedLimit = '';
		$preferenceOrderListCount = 0;
		$preferenceOrderList = array();
		$selected_preference_oreder = 1;
		$options = array();
		$page = 1;
		$sort = '';
		$direction = '';
		$participatingUnits = 0;

		$this->__init_search_preferences();

		if (isset($this->passedArgs)) {
			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['PlacementPreference']['page'] = $this->passedArgs['page'];
			}
			if (isset($this->passedArgs['sort'])) {
				$sort = $this->request->data['PlacementPreference']['sort'] = $this->passedArgs['sort'];
			}
			if (isset($this->passedArgs['direction'])) {
				$direction = $this->request->data['PlacementPreference']['direction'] = $this->passedArgs['direction'];
			}
		}

		if (isset($this->request->data['PlacementPreference']['academic_year']) && !empty($this->request->data['PlacementPreference']['academic_year'])) {
			$selectedAcy = $options['conditions'][]['PlacementPreference.academic_year'] = $this->request->data['PlacementPreference']['academic_year'];
		}

		if (isset($this->request->data['PlacementPreference']['limit']) && !empty($this->request->data['PlacementPreference']['limit'])) {
			$selectedLimit = $options['limit'] = $this->request->data['PlacementPreference']['limit'];
		}

		if (isset($this->request->data['PlacementPreference']['preference_order']) && !empty($this->request->data['PlacementPreference']['preference_order'])) {
			$selected_preference_oreder = $options['conditions'][]['PlacementPreference.preference_order'] = $this->request->data['PlacementPreference']['preference_order'];
		}

		if (isset($this->request->data['PlacementPreference']['round']) && !empty($this->request->data['PlacementPreference']['round'])) {
			$selectedRound = $options['conditions'][]['PlacementPreference.round'] = $this->request->data['PlacementPreference']['round'];
		}

		if (isset($this->request->data['PlacementPreference']['placement_round_participant_id'])  && !empty($this->request->data['PlacementPreference']['placement_round_participant_id'])) {
			$options['conditions'][]['PlacementPreference.placement_round_participant_id'] = $this->request->data['PlacementPreference']['placement_round_participant_id'];
		}

		if (isset($this->request->data['PlacementPreference']['program_id']) && !empty($this->request->data['PlacementPreference']['program_id'])) {
			$selectedProgID = $options['conditions'][]['Student.program_id'] = $this->request->data['PlacementPreference']['program_id'];
		}

		if (isset($this->request->data['PlacementPreference']['program_type_id']) && !empty($this->request->data['PlacementPreference']['program_type_id'])) {
			$selectedProgTypeID = $options['conditions'][]['Student.program_type_id'] = $this->request->data['PlacementPreference']['program_type_id'];
		}

		if ($this->role_id == ROLE_STUDENT) {
			
			$acceptedStudentID = $this->PlacementPreference->Student->field('Student.accepted_student_id', array('Student.id' => $this->student_id));
			
			$acceptedStudentdetail = $this->PlacementPreference->AcceptedStudent->find('first', array(
				'conditions' => array(
					'AcceptedStudent.id' => $acceptedStudentID
				), 
				'contain' => array(
					'Student',
					'PlacementEntranceExamResultEntry'
				),
				'recursive' => -1
			));

			//check if student has any participation in the  round ?
			if (empty($acceptedStudentdetail['AcceptedStudent']['specialization_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id']) ) {
				// the student is still in college
				$applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
			} else if (empty($acceptedStudentdetail['AcceptedStudent']['department_id']) || is_null($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
				// the student is still in college
				$applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
			} else if (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
				// the assignment is specialization
				$applied_for = 'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'];
			}

			$lastStudentSection = $this->last_section;
			$deosTheStudentHaveAnySectionAssignment = false;
			debug($lastStudentSection);

			$latestACY = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($applied_for);
			debug($latestACY);

			$roundLebel = '';
			$freshman = false;

			if (!empty($latestACY['round'])) {
				$roundLebel = ($latestACY['round'] == 1 ? '1st' : ($latestACY['round'] == 2 ? '2nd' : '3rd'));
			}

			if (!empty($lastStudentSection) && is_null($acceptedStudentdetail['Student']['department_id'])) {
				if (!$lastStudentSection['archive']) {
					$acYear = $lastStudentSection['academicyear'];
				}
				$deosTheStudentHaveAnySectionAssignment = true;
				$freshman = true;
			} else if (!empty($latestACY)) {
				$selectedAcy = $acYear = $latestACY['academic_year'];
				$selectedRound = $latestACY['round'];
				$selectedCurrentUnit = $latestACY['applied_for'];
			}

			//check preference recording deadline is not passed.
			$preference_deadline = ClassRegistry::init('PlacementDeadline')->find('first', array(
				'conditions' => array(
					'PlacementDeadline.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'], 
					'PlacementDeadline.applied_for' => $applied_for, 
					//'PlacementDeadline.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'],
					'PlacementDeadline.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					//'PlacementDeadline.placement_round' => $selectedRound,
					'PlacementDeadline.academic_year LIKE ' => $acYear . '%', 
					//'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
				), 
				'recursive' => -1,
				'order' => array('PlacementDeadline.academic_year' => 'DESC', 'PlacementDeadline.placement_round' => 'DESC'),
			));

			debug($preference_deadline);


			if (empty($preference_deadline)){
				$currentAcademicYear = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($this->AcademicYear->current_academicyear());
				$acYear = $currentAcademicYear['academic_year'];
				debug($acYear);

				$preference_deadline = ClassRegistry::init('PlacementDeadline')->find('first', array(
					'conditions' => array(
						'PlacementDeadline.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'], 
						'PlacementDeadline.applied_for' => $applied_for, 
						//'PlacementDeadline.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'], 
						'PlacementDeadline.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
						//'PlacementDeadline.placement_round' => $selectedRound,
						'PlacementDeadline.academic_year LIKE ' => $acYear . '%', 
						//'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
					),
					'recursive' => -1,
					'order' => array('PlacementDeadline.academic_year' => 'DESC', 'PlacementDeadline.placement_round' => 'DESC'),
				));
				debug($preference_deadline);
			}

			//debug($preference_deadline);

			$options['conditions'][]['PlacementPreference.student_id'] = $this->student_id;
			debug($options);
			
			$participatingUnitsCount = $pref = $this->PlacementPreference->find('count', array('conditions' => array('PlacementPreference.student_id' => $this->student_id, 'PlacementPreference.academic_year LIKE ' => $acYear . '%')));
			debug($pref);

			$departments = ClassRegistry::init('PlacementRoundParticipant')->participating_unit_name($acceptedStudentdetail, $acYear);
			debug($departments);

			$collegesList = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
			$departmentsList = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));


			// Just for error massege customization when redirected from record_preference page or to eliminate double messages.

				$placementRound = ClassRegistry::init('PlacementParticipatingStudent')->getNextRound($acYear, $acceptedStudentdetail['AcceptedStudent']['id']);
				$app_for = ClassRegistry::init('PlacementRoundParticipant')->appliedFor($acceptedStudentdetail, $acYear);
				$deadLineStatus = ClassRegistry::init('PlacementDeadline')->getDeadlineStatus($acceptedStudentdetail, $app_for, $placementRound, $acYear);
				debug($deadLineStatus);

			// End Just for error massege customization

			
			$this->set(compact('preference_deadline','departments', 'collegesList', 'departmentsList', 'acYear', 'roundLebel', 'freshman', 'deadLineStatus'));
			$this->set('deosTheStudentHaveAnySectionAssignment', $deosTheStudentHaveAnySectionAssignment);

		} else {

			if (empty($this->request->data['PlacementPreference']['applied_for'])) {
				$latestACY = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			} else {
				$latestACY = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementPreference']['applied_for']);
			}
			
			debug($latestACY);

			if (!empty($latestACY)) {
				$selectedAcy = $acYear = $latestACY['academic_year'];
				$selectedRound = $latestACY['round'];
				$selectedCurrentUnit = $latestACY['applied_for'];

				$this->request->data['PlacementPreference']['applied_for'] = $selectedCurrentUnit;
				$this->request->data['PlacementPreference']['academic_year'] = $selectedAcy;
				$this->request->data['PlacementPreference']['round'] = $selectedRound;
				$this->request->data['PlacementPreference']['program_id'] = $selectedProgID;
				$this->request->data['PlacementPreference']['program_type_id'] = $selectedProgTypeID;

				$this->__init_search_preferences();

				$preferredUnits = ClassRegistry::init('PlacementRoundParticipant')->get_selected_participating_unit_name($this->request->data);
				debug($preferredUnits);

				if (count($preferredUnits) > 0) {
					$participatingUnitsCount = count($preferredUnits);
				}
				
			}

			$programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
			$program_types_available_for_placement_preference = Configure::read('program_types_available_for_placement_preference');

			if (isset($this->request->data['listStudentsPreference'])) {
				//check preference recording deadline is not passed.
				$preference_deadline = ClassRegistry::init('PlacementDeadline')->find('first', array(
					'conditions' => array(
						'PlacementDeadline.program_id' => $selectedProgID, 
						'PlacementDeadline.applied_for' => $this->request->data['PlacementPreference']['applied_for'], 
						'PlacementDeadline.program_type_id' => $selectedProgTypeID, 
						'PlacementDeadline.academic_year LIKE' => $selectedAcy . '%', 
						'PlacementDeadline.placement_round' => $selectedRound,
						'PlacementDeadline.deadline > ' => $this->AcademicYear->getAcademicYearBegainingDate($selectedAcy, 'I')
					),
					'order' => array('PlacementDeadline.academic_year' => 'DESC', 'PlacementDeadline.placement_round' => 'DESC'),
					'recursive' => -1
				));
				debug($preference_deadline);
			} else {
				//$currentAcademicYear = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($this->AcademicYear->current_academicyear());
				$preference_deadline = ClassRegistry::init('PlacementDeadline')->find('first', array(
					'conditions' => array(
						'PlacementDeadline.program_id' => $selectedProgID, 
						'PlacementDeadline.applied_for' => 'c~2', 
						'PlacementDeadline.program_type_id' => $selectedProgTypeID, 
						'PlacementDeadline.academic_year LIKE' => $selectedAcy . '%', 
						'PlacementDeadline.placement_round' => $selectedRound,
						'PlacementDeadline.deadline > ' => $this->AcademicYear->getAcademicYearBegainingDate($selectedAcy, 'I')
					),
					'order' => array('PlacementDeadline.academic_year' => 'DESC', 'PlacementDeadline.placement_round' => 'DESC'),
					'recursive' => -1
				));
				debug($preference_deadline);
			}
		}

		//debug($preference_deadline);

		$this->Paginator->settings = array(
			'order' => (empty($sort)  && empty($direction) ? array('PlacementPreference.student_id' => 'ASC', 'PlacementPreference.academic_year' => 'DESC', 'PlacementPreference.round' => 'DESC', 'PlacementPreference.preference_order' => 'ASC') :  array('PlacementPreference.'.$sort.'' => $direction)),
			'maxLimit' => (!empty($selectedLimit) ? $selectedLimit : 100),
			//'limit' => (isset($this->request->data['PlacementPreference']['limit']) ? $this->request->data['PlacementPreference']['limit'] : '100') ,
			'limit' => (!empty($selectedLimit) ? $selectedLimit : 100),
			'contain' => array(
				'AcceptedStudent', 'Student', 'PlacementRoundParticipant'
			),
			'recursive' => -1
		);

		debug($options);

		if (!empty($options['conditions'])) {
			$placement_preferences = $this->paginate($options['conditions']);
		} else {
			$placement_preferences = array();
		}

		if (empty($placement_preferences) && !empty($options['conditions'])) {
			$this->Flash->info('No result found in a given criteria.');
		}

		/* if (empty($placement_preferences) && isset($this->request->data) && !empty($this->request->data)) {
			$this->Flash->info('No result found in a given criteria.');
		} */

		$preferenceOrderListCount = ClassRegistry::init('PlacementRoundParticipant')->find('count', array(
			'conditions' => array(
				'PlacementRoundParticipant.academic_year' => $selectedAcy,
				'PlacementRoundParticipant.placement_round' => $selectedRound,
			),
			'group' => array(
				'PlacementRoundParticipant.placement_round',
				'PlacementRoundParticipant.academic_year',
			)
		));

		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year ASC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
			//$availableAcademicYears = $this->AcademicYear->academicYearInArray(((explode('/', $currACY)[0]) - 2), (explode('/', $currACY)[0]));
		}

		$availablePrograms = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.program_id', 'PlacementRoundParticipant.program_id'),
			'group' => array('PlacementRoundParticipant.program_id')
		));

		$availableProgramTypes = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.program_type_id', 'PlacementRoundParticipant.program_type_id'),
			'group' => array('PlacementRoundParticipant.program_type_id')
		));

		if (!empty($availablePrograms)) {
			$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $availablePrograms)));
		} else {
			$programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
			$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $programs_available_for_placement_preference)));
		}

		if (!empty($availableProgramTypes)) {
			$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $availableProgramTypes)));
		} else {
			$program_types_available_for_placement_preference = Configure::read('program_types_available_for_placement_preference');
			$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference)));
		}
		

		debug($preferenceOrderListCount);
		if ($participatingUnitsCount != 0) {
			$preferenceOrderList = range(0, $participatingUnitsCount);
			unset($preferenceOrderList[0]);
		} else if ($preferenceOrderListCount != 0) {
			$preferenceOrderList = range(0, $preferenceOrderListCount);
			unset($preferenceOrderList[0]);
		} else {
			$preferenceOrderList = range(0, 5);
			unset($preferenceOrderList[0]);
		}

		debug($preferenceOrderList);

		/* $dataaa['PlacementRoundParticipant']['academic_year'] = '2022/23';
		$dataaa['PlacementRoundParticipant']['placement_round'] = '1';
		$dataaa['PlacementRoundParticipant']['applied_for'] = 'c~2';
		$dataaa['PlacementRoundParticipant']['program_id'] = '1';
		$dataaa['PlacementRoundParticipant']['program_type_id'] = '1';

		$kkkk = ClassRegistry::init('PlacementRoundParticipant')->allParticipatingUnitsDefined($dataaa);

		debug($kkkk); */

		if ($this->role_id != ROLE_STUDENT) {

			/* if (empty($latestACY)) {
				$latestACY['academic_year'] = $this->AcademicYear->current_academicyear();
				$selectedRound = $latestACY['round'] = 1;
			} */

			if(isset($this->request->data['PlacementPreference']) && !empty($this->request->data['PlacementPreference'])) {
				$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for($this->request->data['PlacementPreference']);
			} else {
				if (!empty($availableAcademicYears)) {
					$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $availableAcademicYears);
				} else {
					$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $latestACY['academic_year']);
				}
				//$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $latestACY['academic_year']);
			}
			debug($appliedForList);

			if (isset($this->request->data['PlacementPreference']['applied_for']) && !empty($this->request->data['PlacementPreference']['applied_for'])) {
				$participatingUnits = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
					'conditions' => array(
						'PlacementRoundParticipant.applied_for' => $this->request->data['PlacementPreference']['applied_for'],
						'PlacementRoundParticipant.program_id' => $selectedProgID,
						'PlacementRoundParticipant.program_type_id' => $selectedProgTypeID,
						'PlacementRoundParticipant.academic_year' => $selectedAcy,
						'PlacementRoundParticipant.placement_round' => $selectedRound,
					),
					'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.name'),
					'order' => array('PlacementRoundParticipant.name' => 'ASC')
				));
			} else {
				$participatingUnits = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
					'conditions' => array(
						//'PlacementRoundParticipant.applied_for' => 'c~2',
						'PlacementRoundParticipant.program_id' => $selectedProgID,
						'PlacementRoundParticipant.program_type_id' => $selectedProgTypeID,
						'PlacementRoundParticipant.academic_year' => $latestACY['academic_year'],
						'PlacementRoundParticipant.placement_round' => $latestACY['round'],
					),
					'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.name'),
					'order' => array('PlacementRoundParticipant.name' => 'ASC')
				));
			}
			//debug($participatingUnits);
			$this->set(compact('appliedForList','participatingUnits'));
		}
		
		$allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		//$types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization');

		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_SYSADMIN) {
			$allUnits = ClassRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}

		$this->set(compact(
			'selectedAcy',
			'selectedRound',
			'selectedCurrentUnit',
			'selectedLimit',
			'preferenceOrderListCount',
			'preferenceOrderList',
			'currentUnits',
			'preferredUnits',
			//'types',
			'colleges',
			'departments', 
			'programs', 
			'programTypes', 
			'allUnits', 
			'placement_preferences',
			'appliedForList',
			'page',
			'sort',
			'direction'
		));
	}

	//allow students to fill their own preference

	public function record_preference($id = null)
	{
		debug($this->student_id);

		$acceptedStudents = $acceptedStudentdetail = ClassRegistry::init('AcceptedStudent')->find('first', array(
			'conditions' => array('AcceptedStudent.user_id' => $this->Auth->user('id')),
			'contain' => array('Department', 'Department','College', 'Program', 'ProgramType')
		));

		$admittedStudent = ClassRegistry::init('Student')->find('first', array(
			'conditions' => array('Student.user_id' => $this->Auth->user('id')),
			'contain' => array('AcceptedStudent','Department','College','Program','ProgramType')
		));

		if (isset($acceptedStudents['AcceptedStudent']['department_id']) && !empty($acceptedStudents['AcceptedStudent']['department_id'])) {
			//check if any specialization assignment is active and allow otherwise redirect to index page
			$specializationDefined = ClassRegistry::init('PlacementRoundParticipant')->find('count', array(
				'conditions' => array('PlacementRoundParticipant.applied_for' => 'd~' . ($acceptedStudents['AcceptedStudent']['department_id'])),
				'recursive' => -1
			));

			if ($specializationDefined == 0) {
				$this->Flash->info('You are not eligible for placement either you are already in the department or other specialization placement is not defined yet.');
				$this->redirect(array('action' => 'index'));
			}
		}

		$academic_year = $this->AcademicYear->current_academicyear();

		$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($admittedStudent['Student']['id'], null, null);

		//debug($student_section_exam_status);

		debug($academic_year);
		$override_acyear = false;

		if (isset($student_section_exam_status['Section']) && !$student_section_exam_status['Section']['archive']) {
			$selectedAcademicYear = $academic_year = $student_section_exam_status['Section']['academicyear'];
			$override_acyear = true;
		}

		debug($academic_year);

		$departments = ClassRegistry::init('PlacementRoundParticipant')->participating_unit_name($acceptedStudentdetail, $academic_year);
		debug($departments);
		debug(count($departments));

		if (empty($departments)){
			$x = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($academic_year);
			debug($x);
			$academic_year = $x['academic_year'];
			$departments = ClassRegistry::init('PlacementRoundParticipant')->participating_unit_name($acceptedStudentdetail, $academic_year);
		}

		$placementRound = ClassRegistry::init('PlacementParticipatingStudent')->getNextRound($academic_year, $acceptedStudentdetail['AcceptedStudent']['id']);
		$roundlabel = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($placementRound);
		$applied_for = ClassRegistry::init('PlacementRoundParticipant')->appliedFor($acceptedStudentdetail, $academic_year);

		//require_all_selected options
		$require_all_selected_switch = ClassRegistry::init('PlacementRoundParticipant')->field('require_all_selected', 
array(
				'PlacementRoundParticipant.program_id' => Configure::read('programs_available_for_placement_preference'),
				'PlacementRoundParticipant.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				'PlacementRoundParticipant.applied_for' => $applied_for,
				'PlacementRoundParticipant.placement_round' => $placementRound,
				'PlacementRoundParticipant.academic_year LIKE ' => $academic_year . '%',
			));

		debug($require_all_selected_switch);
		
		if (isset($this->request->data['fillPreference']) && !empty($this->request->data)) {
			
			$deadLineStatus = ClassRegistry::init('PlacementDeadline')->getDeadlineStatus($acceptedStudentdetail, $applied_for, $placementRound, $academic_year);

			$isThePlacementRun = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					//'PlacementParticipatingStudent.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'],
					'PlacementParticipatingStudent.program_id' => Configure::read('programs_available_for_placement_preference'), 
					//'PlacementParticipatingStudent.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'],
					'PlacementParticipatingStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementParticipatingStudent.applied_for' => $applied_for,
					'PlacementParticipatingStudent.round' => $placementRound,
					'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
					'PlacementParticipatingStudent.placement_round_participant_id is not null'
				),
				'recursive' => -1
			));
			
			if ($deadLineStatus == 1 && $isThePlacementRun == 0) {
				$this->set($this->request->data);
				if ($this->PlacementPreference->validates($this->request->data)) {
					if (!$this->PlacementPreference->isAlreadyEnteredPreference($this->request->data['PlacementPreference'][1])) {
						if ($this->PlacementPreference->isAllPreferenceSelectedDifferent($this->request->data['PlacementPreference'], $require_all_selected_switch)) {
							if ($this->PlacementPreference->saveAll($this->request->data['PlacementPreference'], array('validate' => 'first'))) {
								$this->Flash->success('Your preferences are saved.');
								$this->redirect(array('action' => 'index'));
							} else {
								$this->Flash->error('The preferences could not be saved. Please, try again.');
							}
						} else {
							$this->Flash->error('Input Error: Please select different program preference for each preference order.');
						}
					} else {
						$this->Flash->error('You have already entered your preference. Please edit your preferences before the deadline.');
						$this->redirect(array('controller' => 'placement_preferences', 'action' => 'index'));
					}
				} else {
					$this->Flash->error('Please enter the input correctly');
				}
			} else {
				if ($isThePlacementRun) {
					$this->Flash->error('The defined placement has already run, and you can not edit your preference at this time.');
				} else {
					if ($deadLineStatus == 2) {
						$this->Flash->error('Preference Deadline is passed. You can not record or change your preferences. Advise the registrar for more information');
					} else {
						$this->Flash->info('Preference Deadline is not defined, please come again after announced by registrar or advise the registrar for more information.');
					}
				}
				$this->redirect(array('action' => 'index'));
			}
		}

		if (isset($id) && !empty($id)) {

			$options = array(
				'conditions' => array('PlacementPreference.id' => $id),
				'contain' => array('PlacementRoundParticipant')
			);

			$firstRow = $this->PlacementPreference->find('first', $options);
			debug($firstRow);

			if (isset($firstRow['PlacementRoundParticipant']['applied_for']) && !empty($firstRow['PlacementRoundParticipant']['applied_for'])) {
				$applied_for = $firstRow['PlacementRoundParticipant']['applied_for'];
			} else {
				$applied_for = ClassRegistry::init('PlacementRoundParticipant')->appliedFor($acceptedStudentdetail, $firstRow['PlacementPreference']['academic_year']);
			}

		    $academic_year = $firstRow['PlacementPreference']['academic_year'];
			$placementRound = $firstRow['PlacementPreference']['round'];

			$isThePlacementRun = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					//'PlacementParticipatingStudent.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'],
					'PlacementParticipatingStudent.program_id' => Configure::read('programs_available_for_placement_preference'), 
					//'PlacementParticipatingStudent.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'],
					'PlacementParticipatingStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementParticipatingStudent.applied_for' => $applied_for,
					'PlacementParticipatingStudent.round' => $firstRow['PlacementPreference']['round'],
					'PlacementParticipatingStudent.academic_year LIKE ' => $firstRow['PlacementPreference']['academic_year'] . '%',
					'PlacementParticipatingStudent.placement_round_participant_id is not null'
				)
			));

			debug($isThePlacementRun);
			
			if ($isThePlacementRun) {
				$this->Flash->error('The defined placement has already run, and you can not edit your preference at this time.');
				$this->redirect(array('action' => 'index'));
			}

			$departments = ClassRegistry::init('PlacementRoundParticipant')->get_participating_unit_for_edit($firstRow['PlacementPreference']['placement_round_participant_id']);

			$option_2 = array(
				'conditions' => array(
					'PlacementPreference.accepted_student_id' => $firstRow['PlacementPreference']['accepted_student_id'],
					'PlacementPreference.student_id' => $firstRow['PlacementPreference']['student_id'],
					'PlacementPreference.academic_year' => $firstRow['PlacementPreference']['academic_year'],
					'PlacementPreference.round' => $firstRow['PlacementPreference']['round'],
				),
				'order' => array('PlacementPreference.preference_order' => 'ASC'),
				'recursive' => -1
			);

			$this->request->data = $this->PlacementPreference->find('all', $option_2);
			//debug($this->request->data);

			$data = array();
			$i = 1;

			if (!empty($this->request->data)) {
				foreach ($this->request->data as $k => $dev) {
					if (/* !is_null($dev['PlacementPreference']['placement_round_participant_id']) && */ ($dev['PlacementPreference']['preference_order']) == $i ){
						$data['PlacementPreference'][$i] = $dev['PlacementPreference'];
						$i++;
					}
				}
			}
			
			$this->request->data = $data;
			
		} else {

			$deadLineStatus = ClassRegistry::init('PlacementDeadline')->getDeadlineStatus($acceptedStudentdetail, $applied_for, $placementRound, $academic_year);
			debug($deadLineStatus); 

			debug($admittedStudent['Student']['id']);


			if ($deadLineStatus == 2) {
				$this->Flash->error('The preference deadline for ' . $academic_year . ' academic year ' . $roundlabel . ' round is passed. You can not record your preference now. Please ask the registrar for more information');
				$this->redirect(array('action' => 'index'));
			} else if ($deadLineStatus == 0) {
				$this->Flash->error('The preference deadline for ' . $academic_year . ' academic year ' . $roundlabel . ' round is not announced yet by the registrar. Please ask the registrar for more information');
				$this->redirect(array('action' => 'index'));
			} else {
				//check if preference is filled, and redirect
				$options = array(
					'conditions' =>  array(
						'PlacementPreference.accepted_student_id' => $acceptedStudents['AcceptedStudent']['id'],
						/* 'OR' => array(
							'PlacementPreference.accepted_student_id' => $acceptedStudents['AcceptedStudent']['id'],
							'PlacementPreference.student_id' => $admittedStudent['Student']['id'],
						), */
						'PlacementPreference.round' => $placementRound,
						'PlacementPreference.academic_year' => $academic_year
					)
				);

				$firstRow = $this->PlacementPreference->find('first', $options);

				if (isset($firstRow) && !empty($firstRow)) {
					$this->redirect(array('action' => 'record_preference', $firstRow['PlacementPreference']['id']));
				} 
			}
		}

		if ($departments) {
			$departmentcount = count($departments);
			$this->set('departments', $departments);
			$this->set('departmentcount', $departmentcount);
		} else {
			$this->Flash->info('There is no a placement preference  setting defined by registrar for now, please come back when registrar announces to fill your preferences.');
			$this->redirect(array('controller' => 'placement_preferences', 'action' => 'index'));
		}

		$roundlabel = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($placementRound);
		$unitFor = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $acceptedStudentdetail['AcceptedStudent']['college_id']), 'recursive' => -1));

		$this->set(compact('roundlabel', 'unitFor', 'academic_year'));

		if (!empty($acceptedStudents)) {
			// foreach($acceptedStudents as $k=>$v){
			$studentname = $acceptedStudents['AcceptedStudent']['full_name'];
			$studentnumber = $acceptedStudents['AcceptedStudent']['studentnumber'];

			if ($override_acyear) {
				$acyear = $selectedAcademicYear;
			} else {
				$acyear = $acceptedStudents['AcceptedStudent']['academicyear'];
			}

			$collegename = $acceptedStudents['College']['name'];
			$college_id = $acceptedStudents['College']['id'];
			$accepted_student_id = $acceptedStudents['AcceptedStudent']['id'];

			
			if (isset($admittedStudent) && !empty($admittedStudent)) {
				$student_id = $admittedStudent['Student']['id'];
			} else {
				$student_id = $this->student_id;
			}


			// to prevent session conflicting student ids
			if ($student_id != $this->student_id && isset($acceptedStudents['AcceptedStudent']['id'])) {
				$student_id = ClassRegistry::init('Student')->field('Student.id', array('Student.accepted_student_id' => $acceptedStudents['AcceptedStudent']['id']));
			}

			$this->set(compact(
				'studentname',
				'studentnumber',
				'collegename',
				'college_id',
				'placementRound',
				'accepted_student_id',
				'student_id',
				'acyear',
				'acceptedStudents',
				'require_all_selected_switch'
			));
		}
	}

	function auto_fill_preference( $academicyear = '2019/20', $targetUnitType = "c", $targetUnitValue = "", $round = 1) 
	{
		die;
		$academicyear = '2019/20';

		if (isset($targetUnitType) && !empty($targetUnitValue) && $targetUnitType == 'c') {
			// the student is still in college
			$round = 1;
			$applied_for = 'c~' . $targetUnitValue;

			$accepted_students = ClassRegistry::init('AcceptedStudent')->find('all', array(
				'recursive' => '-1', 
				'conditions' => array(
					'AcceptedStudent.college_id' => $targetUnitValue, 
					'AcceptedStudent.academicyear LIKE ' => $academicyear
				),
				'contain' => array('Student')
			));

			$detail_of_participating_department = ClassRegistry::init('PlacementRoundParticipant')->find('all', array(
				'recursive' => '-1', 
				'conditions' => array(
					'PlacementRoundParticipant.type' => 'College', 
					'PlacementRoundParticipant.academic_year' => $academicyear,
					'PlacementRoundParticipant.applied_for' => $applied_for,
					'PlacementRoundParticipant.program_id' => 1,
					'PlacementRoundParticipant.program_type_id' => 1,
					'PlacementRoundParticipant.placement_round' => $round
				)
			));
		} else if (isset($targetUnitType) && !empty($targetUnitValue) && $targetUnitType == 'd') {
			// the assignment is specialization
			$round = 2;
			$applied_for = 'd~' . $targetUnitValue;

			$accepted_students = ClassRegistry::init('AcceptedStudent')->find('all', array(
				'recursive' => '-1', 
				'conditions' => array(
					'AcceptedStudent.department_id' => $targetUnitValue, 
					'AcceptedStudent.academicyear LIKE ' => $academicyear
				),
				'contain' => array('Student')
			));

			$detail_of_participating_department = ClassRegistry::init('PlacementRoundParticipant')->find('all', array(
				'recursive' => '-1', 
				'conditions' => array(
					'PlacementRoundParticipant.type' => 'Department', 
					'PlacementRoundParticipant.academic_year' => $academicyear,
					'PlacementRoundParticipant.applied_for' => $applied_for,
					'PlacementRoundParticipant.program_id' => 1,
					'PlacementRoundParticipant.program_type_id' => 1,
					'PlacementRoundParticipant.placement_round' => $round
				)
			));
		}

		$number_of_participating_department = count($detail_of_participating_department);

		$departments = array();

		if (!empty($detail_of_participating_department)) {
			foreach ($detail_of_participating_department as $key => $participating_department) {
				array_push($departments, $participating_department['PlacementRoundParticipant']['id']);
			}
		}

		$count = 0;
		$preference_selection = array();

		if (!empty($accepted_students)) {
			foreach ($accepted_students as $key => $accepted_student) {
				$filled = $this->PlacementPreference->find('count', array(
					'conditions' => array(
						'PlacementPreference.accepted_student_id' => $accepted_student['AcceptedStudent']['id'],
						'PlacementPreference.student_id' => $accepted_student['Student']['id'],
						'PlacementPreference.academic_year' => $academicyear,
						'PlacementPreference.round' => $round
					)
				));

				if ($filled <= 0) {
					shuffle($departments);
					for ($i = 1; $i <= count($departments); $i++) {
						$preference_selection[$count]['accepted_student_id'] = $accepted_student['AcceptedStudent']['id'];
						$preference_selection[$count]['student_id'] = $accepted_student['Student']['id'];
						$preference_selection[$count]['academic_year'] = $academicyear;
						$preference_selection[$count]['user_id'] = $accepted_student['AcceptedStudent']['user_id'];
						$preference_selection[$count]['edited_by'] = $accepted_student['AcceptedStudent']['user_id'];
						$preference_selection[$count]['round'] = $round;
						$preference_selection[$count]['placement_round_participant_id'] = $departments[$i - 1];
						$preference_selection[$count]['preference_order'] = $i;
						$count++;
					}
				}
			}
		}

		$this->PlacementPreference->saveAll($preference_selection);

		return $this->redirect(array('controller' => 'PlacementPreferences', 'action' => 'index'));
	}

	// add student preference on behalf of the students

	public function add()
	{
		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		$types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization' );

		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year ASC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
			//$availableAcademicYears = $this->AcademicYear->academicYearInArray(((explode('/', $currACY)[0]) - 2), (explode('/', $currACY)[0]));
		}

		$availablePrograms = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.program_id', 'PlacementRoundParticipant.program_id'),
			'group' => array('PlacementRoundParticipant.program_id')
		));

		$availableProgramTypes = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.program_type_id', 'PlacementRoundParticipant.program_type_id'),
			'group' => array('PlacementRoundParticipant.program_type_id')
		));

		if (!empty($availablePrograms)) {
			$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $availablePrograms)));
		} else {
			$programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
			$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $programs_available_for_placement_preference)));
		}

		if (!empty($availableProgramTypes)) {
			$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $availableProgramTypes)));
		} else {
			$program_types_available_for_placement_preference = Configure::read('program_types_available_for_placement_preference');
			$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference)));
		}

		if(isset($this->request->data['PlacementPreference']) && !empty($this->request->data['PlacementPreference'])) {
			$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for($this->request->data['PlacementPreference']);
			$latestACY = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementPreference']['applied_for']);
		} else {
			$latestACY = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $latestACY['academic_year']);
			}
			//$appliedForList = $this->PlacementPreference->get_defined_list_of_applied_for(null, $latestACY['academic_year']);
		}
		debug($appliedForList);
		
		$this->set(compact('appliedForList'));
		
		$fieldSetups = 'type, foreign_key, name, edit';

		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$allUnits = ClassRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}


		$sections = array();
		$section_combo_id = null;

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'sections', 'colleges', 'fieldSetups', 'programs', 'currentUnits', 'section_combo_id', 'programTypes', 'latestACY'));
	}

	public function get_selected_participant()
	{
		$this->layout = 'ajax';

		$placementRoundParticipants = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $this->request->data['Search']['applied_for'],
				'PlacementRoundParticipant.program_id' => $this->request->data['Search']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $this->request->data['Search']['program_type_id'],
				'PlacementRoundParticipant.academic_year' => $this->request->data['Search']['academic_year'],
				'PlacementRoundParticipant.placement_round' => $this->request->data['Search']['placement_round'],
			),
			'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.name')
		));

		$preferenceOrders = array();
		$count = 1;

		if (!empty($placementRoundParticipants)) {
			foreach ($placementRoundParticipants as $p => $pv) {
				$preferenceOrders[$count] = $count;
				$count++;
			}
		}

		$this->set(compact('placementRoundParticipants', 'preferenceOrders'));
	}

	public function get_selected_student()
	{
		$this->layout = 'ajax';
		$students =  ClassRegistry::init('PlacementEntranceExamResultEntry')->getStudentForPreferenceEntry($this->request->data);
		$this->set(compact('students'));
	}

	public function autoSaveResult()
	{
		$this->autoRender = false;
		$exam_results = array();
		$save_is_ok = true;
		$do_manipulate = false;
		
		if (isset($this->request->data['PlacementPreference']) && !empty($this->request->data['PlacementPreference'])) {
			foreach ($this->request->data['PlacementPreference']
				as $key => $exam_result) {
				$save_is_ok = true;
				debug($exam_result);

				if (is_array($exam_result)) {
					if (is_numeric($exam_result['preference_order'])) {
						$exam_results = $exam_result;
						if (!is_numeric($exam_result['preference_order'])) {
							$save_is_ok = false;
						}
						if ($save_is_ok) {
							$data['PlacementPreference'] = $exam_results;
							$data['PlacementPreference']['academic_year'] = $this->request->data['Search']['academic_year'];
							$data['PlacementPreference']['user_id'] = $this->Auth->user('id');
							$data['PlacementPreference']['edited_by'] = $this->Auth->user('id');
							$data['PlacementPreference']['round'] = $this->request->data['Search']['placement_round'];
							if (isset($data['PlacementPreference']['id']) && !empty($data['PlacementPreference']['id'])) {
								$alreadyRecored = $this->PlacementPreference->find('first', array(
									'conditions' => array('PlacementPreference.id' => $data['PlacementPreference']['id']),
									'recursive' => -1
								));
							} else {
								$alreadyRecored = $this->PlacementPreference->find('first', array(
									'conditions' => array(
										'PlacementPreference.placement_round_participant_id' => $data['PlacementPreference']['placement_round_participant_id'], 
										'PlacementPreference.accepted_student_id' => $data['PlacementPreference']['accepted_student_id'],
										'PlacementPreference.student_id' => $data['PlacementPreference']['student_id'],
										'PlacementPreference.academic_year' => $data['PlacementPreference']['academic_year'],
										'PlacementPreference.round' => $data['PlacementPreference']['round']
									),
									'recursive' => -1
								));
							}
							if (isset($alreadyRecored) && !empty($alreadyRecored)) {
								$data['PlacementPreference']['id'] = $alreadyRecored['PlacementPreference']['id'];
							} else {
								$this->PlacementPreference->create();
							}

							$this->set($data['PlacementPreference']);
							debug($data);
							if ($this->PlacementPreference->save($data)) {
								//
							} else {
								//
							}
						}
					} else {
						//does their is a record preference, then delete it if empty
						if (isset($exam_result['id']) && empty($exam_result['preference_order'])) {
							$delete = $this->PlacementPreference->find('count', array(
								'conditions' => array(
									'PlacementPreference.placement_round_participant_id' => $exam_result['placement_round_participant_id'],
									'PlacementPreference.id' => $exam_result['id'],
									'PlacementPreference.accepted_student_id' => $exam_result['accepted_student_id'],
									'PlacementPreference.student_id' => $exam_result['student_id']
								),
								'recursive' => -1
							));

							if ($delete) {
								$this->PlacementPreference->id = $exam_result['id'];
								if ($this->PlacementPreference->delete()) {
									//
								}
							}
						}
					}
				}
			}
		}
	}

	// allow students to view the placement result

	/* public function view_result_of_placement()
	{
		$allPreferenceEntryStudentsInterested = $this->PlacementPreference->find('all', array(
			'conditions' => array('PlacementPreference.student_id' => $this->student_id),
			'contain' => array('PlacementRoundParticipant', 'AcceptedStudent', 'Student'),
			'order' => array('PlacementPreference.round DESC'),
		));

		//debug($allPreferenceEntryStudentsInterested);

		if (!empty($allPreferenceEntryStudentsInterested)) {

			foreach ($allPreferenceEntryStudentsInterested as $key => $value) {
				//debug($value['PlacementPreference']);
				if(is_null($value['PlacementPreference']['placement_round_participant_id']) || empty($value['PlacementPreference']['placement_round_participant_id']) || $value['PlacementPreference']['placement_round_participant_id'] == 0) {
					debug($value['PlacementPreference']['id']);
					debug($key);
					unset($allPreferenceEntryStudentsInterested[$key]);
				}
			}
			$allPreferenceEntryStudentsInterested = array_values($allPreferenceEntryStudentsInterested);
		}
		//debug($allPreferenceEntryStudentsInterested);

		$studentList = array();
		$entrance = '';
		$prepartory = '';
		$freshman = '';
		$assigned = '';

		if (!empty($allPreferenceEntryStudentsInterested)) {
			foreach ($allPreferenceEntryStudentsInterested as $k => $v) {
				$placementSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array(
					'conditions' => array(
						'PlacementResultSetting.applied_for' => $v['PlacementRoundParticipant']['applied_for'],
						'PlacementResultSetting.round' => $v['PlacementPreference']['round'],
						'PlacementResultSetting.academic_year' => $v['PlacementPreference']['academic_year'],
						'PlacementResultSetting.program_id' => $v['PlacementRoundParticipant']['program_id'],
						'PlacementResultSetting.program_type_id' => $v['PlacementRoundParticipant']['program_type_id']
					)
				));
				//debug($placementSettings);
				
				$resultType = array();
				$freshmanMaxResultDB = '';
				$prepMaxResultDB = '';
				$entranceMaxResultDB = '';

				if (!empty($placementSettings)) {
					foreach ($placementSettings as $pl => $pv) {
						$resultType[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];
						
						//debug($pv['PlacementResultSetting']);
						if ($pv['PlacementResultSetting']['result_type'] == 'EHEECE_total_results') {
							$prepMaxResultDB = $pv['PlacementResultSetting']['max_result'];
						} else if ($pv['PlacementResultSetting']['result_type'] == 'freshman_result') {
							$freshmanMaxResultDB = $pv['PlacementResultSetting']['max_result'];
						} else if ($pv['PlacementResultSetting']['result_type'] == 'entrance_result') {
							$entranceMaxResultDB = $pv['PlacementResultSetting']['max_result'];
						}
					}
				}

				//debug($freshmanMaxResultDB);
				//debug($prepMaxResultDB);
				//debug($entranceMaxResultDB);

				debug($resultType);
				
				if (in_array('freshman_result', array_keys($resultType))) {
					$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
						'conditions' => array(
							'StudentExamStatus.student_id' => $v['Student']['id'],
							'StudentExamStatus.academic_status_id != 4'
						),
						'fields' => array(
							'StudentExamStatus.sgpa',
							'StudentExamStatus.cgpa'
						),
						'order' => 'StudentExamStatus.created DESC',
						'group' => array('StudentExamStatus.student_id', 'StudentExamStatus.semester', 'StudentExamStatus.academic_year'),
					));

					if (!empty($freshmanMaxResultDB) && $freshmanMaxResultDB != 0) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((($resultType['freshman_result'] * $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMaxResultDB), 2);
						$freshman = round((($resultType['freshman_result'] * $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMaxResultDB), 2);
					} else {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((($resultType['freshman_result'] * $freshManresult['StudentExamStatus']['cgpa']) / FRESHMANMAXIMUM), 2);
						$freshman = round((($resultType['freshman_result'] * $freshManresult['StudentExamStatus']['cgpa']) / FRESHMANMAXIMUM), 2);
					}
					
				}

				if (in_array('entrance_result', array_keys($resultType))) {

					$entranceResult = ClassRegistry::init('PlacementEntranceExamResultEntry')->find('first', array(
						'conditions' => array(
							'PlacementEntranceExamResultEntry.student_id' => $v['Student']['id'],
							'PlacementEntranceExamResultEntry.placement_round_participant_id' => $v['PlacementRoundParticipant']['id']
						),
						'fields' => array(
							'PlacementEntranceExamResultEntry.result',
							'PlacementEntranceExamResultEntry.result'
						),
						'order' => 'PlacementEntranceExamResultEntry.result ASC',
						'recursive' => -1
					));

					if (isset($entranceResult['PlacementEntranceExamResultEntry']['result']) && !empty($entranceResult['PlacementEntranceExamResultEntry']['result'])) {
						if (!empty($entranceMaxResultDB) && $entranceMaxResultDB != 0) {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ($resultType['entrance_result'] * $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
							$entrance = ($resultType['entrance_result'] * $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ($resultType['entrance_result'] * $entranceResult['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
							$entrance = ($resultType['entrance_result'] * $entranceResult['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
						}
					}

				}

				if (in_array('EHEECE_total_results', array_keys($resultType))) {

					if (!empty($prepMaxResultDB) && $prepMaxResultDB != 0) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((($resultType['EHEECE_total_results'] * $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
						$prepartory = round((($resultType['EHEECE_total_results'] * $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
					} else {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((($resultType['EHEECE_total_results'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM), 2);
						$prepartory = round((($resultType['EHEECE_total_results'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM), 2);
					}

				}

				$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.student_id' => $v['Student']['id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $v['PlacementRoundParticipant']['id']
					),
					'contain' => array('PlacementRoundParticipant')
				));

				$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['academic_year'] = $v['PlacementPreference']['academic_year'];
				$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['round'] = $v['PlacementPreference']['round'];
				$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_order'] = $v['PlacementPreference']['preference_order'];
				$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_name'] = $v['PlacementRoundParticipant']['name'];
				$studentList[$v['PlacementPreference']['id']]['AcceptedStudent'] = $v['AcceptedStudent'];
				
				if (isset($assignedTo['PlacementRoundParticipant']['name']) && !empty($assignedTo['PlacementRoundParticipant']['name'])) {
					$studentList[$v['PlacementPreference']['id']]['Assigned'] = $assignedTo['PlacementRoundParticipant']['name'] . '( Prefered as - ' . $v['PlacementPreference']['preference_order'] . ')';
					$assigned = $assignedTo['PlacementRoundParticipant']['name'] . '('. $v['PlacementPreference']['preference_order'] . ')';
				}
			}
		}

		if (empty($assigned)) {
			$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
				'conditions' => array(
					'PlacementParticipatingStudent.student_id' => $this->student_id,
				),
				'contain' => array('PlacementRoundParticipant'),
				'order' => array(
					'PlacementParticipatingStudent.modified DESC',
					'PlacementParticipatingStudent.academic_year DESC',
					'PlacementParticipatingStudent.round DESC',
				)
			));

			if (isset($assignedTo['PlacementRoundParticipant']['name'])) {
				$assigned = $assignedTo['PlacementRoundParticipant']['name'];
			}
			//debug($assignedTo);
		}

		$this->set(compact('studentList','entrance','prepartory', 'freshman', 'assigned'));
	} */

	public function view_result_of_placement() 
	{
		$studentBasic = $this->PlacementPreference->Student->find('first', array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1));
		//debug($studentBasic);

		$allPreferenceEntryStudentsInterested = $this->PlacementPreference->find('all', array(
			'conditions' => array(
				'PlacementPreference.student_id' => $this->student_id
			),
			'contain' => array(
				'PlacementRoundParticipant', 
				'AcceptedStudent', 
				'Student'
			),
			'order' => array('PlacementPreference.academic_year' => 'DESC', 'PlacementPreference.round' => 'DESC', 'PlacementPreference.preference_order' => 'ASC'),
		));

		//debug($allPreferenceEntryStudentsInterested);

		$all_placement_round_participant_ids = array();

		if (!empty($allPreferenceEntryStudentsInterested)) {
			foreach ($allPreferenceEntryStudentsInterested as $key => $value) {
				//debug($value['PlacementPreference']);
				if(is_null($value['PlacementPreference']['placement_round_participant_id']) || empty($value['PlacementPreference']['placement_round_participant_id']) || $value['PlacementPreference']['placement_round_participant_id'] == 0) {
					debug($value['PlacementPreference']['id']);
					debug($key);
					unset($allPreferenceEntryStudentsInterested[$key]);
				} else {
					$all_placement_round_participant_ids[$value['PlacementPreference']['placement_round_participant_id']] = $value['PlacementPreference']['placement_round_participant_id'];
				}
			}
			$allPreferenceEntryStudentsInterested = array_values($allPreferenceEntryStudentsInterested);
		}

		//debug($allPreferenceEntryStudentsInterested);

		$studentList = array();

		$last_placement_round = '';
		$last_placement_academic_year = '';
		$semester_to_use_for_cgpa = '';

		$freshmanResultSet = 0;
		$freshmanResultPercent = 0;

		$prepararoryResultSet = 0;
		$prepararoryResultPercent = 0;

		$entranceResultSet = 0;
		$entranceResultPercent = 0;

		$freshmanMaxResultDB = NULL;
		$prepMaxResultDB = NULL;
		$entranceMaxResultDB = NULL;
		

		$lastPrefereceoftheStudent = $this->PlacementPreference->find('first', array(
			'conditions' => array(
				'PlacementPreference.student_id' => $this->student_id,
				//'PlacementPreference.placement_round_participant_id' => $allPreferenceEntryStudentsInterested,
				'OR' => array(
					'PlacementPreference.preference_order IS NOT NULL',
					'PlacementPreference.preference_order != 0',
					'PlacementPreference.preference_order != ""',
				)
			),
			'contain' => array(
				'PlacementRoundParticipant', 
			),
			'order' => array('PlacementPreference.academic_year' => 'DESC', 'PlacementPreference.round' => 'DESC', 'PlacementPreference.preference_order' => 'ASC'),
			'recursive' => -1
		));

		//debug($lastPrefereceoftheStudent);

		//exit();
		
		if (!empty($lastPrefereceoftheStudent)) {

			$last_placement_round = $lastPrefereceoftheStudent['PlacementPreference']['round'];
			$last_placement_academic_year = $lastPrefereceoftheStudent['PlacementPreference']['academic_year'];
			$semester_to_use_for_cgpa = (!empty($lastPrefereceoftheStudent['PlacementRoundParticipant']['semester']) ? $lastPrefereceoftheStudent['PlacementRoundParticipant']['semester'] : ($lastPrefereceoftheStudent['PlacementPreference']['round'] == 1 ? 'I' : 'II'));


			// debug($lastPrefereceoftheStudent);
			// debug($last_placement_round);
			// debug($last_placement_academic_year);

			$resultType = array();

			$placementSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array(
				'conditions' => array(
					'PlacementResultSetting.applied_for' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['applied_for'],
					'PlacementResultSetting.round' => $lastPrefereceoftheStudent['PlacementPreference']['round'],
					'PlacementResultSetting.academic_year' => $lastPrefereceoftheStudent['PlacementPreference']['academic_year'],
					'PlacementResultSetting.program_id' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['program_id'],
					'PlacementResultSetting.program_type_id' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['program_type_id']
				)
			));

			//debug($placementSettings);

			if (!empty($placementSettings)) {
				foreach ($placementSettings as $pl => $pv) {
					$resultType[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];
					//debug($pv['PlacementResultSetting']);
					if ($pv['PlacementResultSetting']['result_type'] == 'EHEECE_total_results') {
						$prepMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : NULL);
						$prepararoryResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$prepararoryResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					} else if ($pv['PlacementResultSetting']['result_type'] == 'freshman_result') {
						$freshmanMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (float) $pv['PlacementResultSetting']['max_result'] : NULL);
						$freshmanResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$freshmanResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					} else if ($pv['PlacementResultSetting']['result_type'] == 'entrance_result') {
						$entranceMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : NULL);
						$entranceResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$entranceResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					}
				}
			}

			// debug($freshmanResultSet);
			// debug($freshmanMaxResultDB);
			// debug($freshmanResultPercent);

			// debug($prepararoryResultSet);
			// debug($prepMaxResultDB);
			// debug($prepararoryResultPercent);
			
			// debug($entranceResultSet);
			// debug($entranceMaxResultDB);
			// debug($entranceResultPercent);


			if (!empty($allPreferenceEntryStudentsInterested)) {
				foreach ($allPreferenceEntryStudentsInterested as $k => $v) {
		
					$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
						'conditions' => array(
							'StudentExamStatus.student_id' => $this->student_id,
							'StudentExamStatus.academic_status_id != '. DISMISSED_ACADEMIC_STATUS_ID.'',
							'StudentExamStatus.academic_year' => $last_placement_academic_year,
							'StudentExamStatus.semester' => $semester_to_use_for_cgpa,
						),
						'fields' => array(
							'StudentExamStatus.sgpa',
							'StudentExamStatus.cgpa'
						),
						'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
						'group' => array('StudentExamStatus.student_id', 'StudentExamStatus.semester', 'StudentExamStatus.academic_year'),
					));
					
					if (!empty($freshManresult) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && (float) $freshManresult['StudentExamStatus']['cgpa'] > (float) DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT  && $freshmanResultSet) {
						if (!empty($freshmanMaxResultDB) && !empty($freshmanResultPercent)) {
							$freshmanPercent = $freshmanResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((($freshmanResultPercent  * (float) $freshManresult['StudentExamStatus']['cgpa']) /  $freshmanMaxResultDB), 2);
							$freshman = round((($freshmanResultPercent * (float) $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMaxResultDB), 2);
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((float) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
							$freshman = round((((float) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (float)FRESHMANMAXIMUM), 2);
						}
					} else if (!empty($freshManresult) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && (float) $freshManresult['StudentExamStatus']['cgpa'] > (float) DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((int) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
						$freshman = round((((int) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
					} else {
						$freshmanPercent = $freshmanResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = 0;
						$freshman = 0;
					}

					$entranceResult = ClassRegistry::init('PlacementEntranceExamResultEntry')->find('first', array(
						'conditions' => array(
							'PlacementEntranceExamResultEntry.student_id' => $this->student_id,
							'PlacementEntranceExamResultEntry.placement_round_participant_id' => $v['PlacementRoundParticipant']['id'],
							//'PlacementEntranceExamResultEntry.created > ' => date($v['PlacementRoundParticipant']['created'], strtotime("-" . 15 . " day ")),
							'PlacementEntranceExamResultEntry.created > ' => $v['PlacementRoundParticipant']['created'],
							//'PlacementEntranceExamResultEntry.placement_round_participant_id' => $all_placement_round_participant_ids,
						),
						'fields' => array(
							'PlacementEntranceExamResultEntry.result',
							'PlacementEntranceExamResultEntry.result'
						),
						'order' => array('PlacementEntranceExamResultEntry.modified' => 'DESC', 'PlacementEntranceExamResultEntry.created' => 'DESC', 'PlacementEntranceExamResultEntry.result' => 'DESC'),
						'group' => array('PlacementEntranceExamResultEntry.accepted_student_id', 'PlacementEntranceExamResultEntry.student_id', 'PlacementEntranceExamResultEntry.placement_round_participant_id'),
						'recursive' => -1
					));

					debug($entranceResult);
					//debug(array_keys($resultType));

					if (isset($entranceResult['PlacementEntranceExamResultEntry']['result']) && is_numeric($entranceResult['PlacementEntranceExamResultEntry']['result']) && (int) $entranceResult['PlacementEntranceExamResultEntry']['result'] >= 0 && $entranceResultSet) {
						if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
							$entrancePercent = $entranceResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
							$entrance = ($entranceResultPercent * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
							$entrance = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
						}
					} else if (isset($entranceResult['PlacementEntranceExamResultEntry']['result']) && is_numeric($entranceResult['PlacementEntranceExamResultEntry']['result']) && (int) $entranceResult['PlacementEntranceExamResultEntry']['result'] >= 0) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
						$entrance = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
					} else {
						$entrancePercent = $entranceResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = 0;
						$entrance = 0;
					}

					if (is_numeric($v['AcceptedStudent']['EHEECE_total_results']) && (int) $v['AcceptedStudent']['EHEECE_total_results'] > 100 && $prepararoryResultSet ) {
						if (!empty($prepMaxResultDB) && !empty($prepararoryResultPercent)) {
							$preparatoryPercent = $prepararoryResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((($prepararoryResultPercent * (int) $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
							$prepartory = round((($prepararoryResultPercent * (int) $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
							$prepartory = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
						}
					} else if (is_numeric($v['AcceptedStudent']['EHEECE_total_results']) && (int) $v['AcceptedStudent']['EHEECE_total_results'] > 100) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
						$prepartory = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
					} else {
						$preparatoryPercent = $prepararoryResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = 0;
						$prepartory = 0;
					}

					$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
						'conditions' => array(
							'PlacementParticipatingStudent.student_id' => $v['Student']['id'],
							'PlacementParticipatingStudent.placement_round_participant_id' => $v['PlacementRoundParticipant']['id']
						),
						'contain' => array('PlacementRoundParticipant')
					));

					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['academic_year'] = $v['PlacementPreference']['academic_year'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['round'] = $v['PlacementPreference']['round'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_order'] = $v['PlacementPreference']['preference_order'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_name'] = $v['PlacementRoundParticipant']['name'];
					$studentList[$v['PlacementPreference']['id']]['AcceptedStudent'] = $v['AcceptedStudent'];
					
					if (isset($assignedTo['PlacementRoundParticipant']['name']) && !empty($assignedTo['PlacementRoundParticipant']['name'])) {
						$studentList[$v['PlacementPreference']['id']]['Assigned'] = $assignedTo['PlacementRoundParticipant']['name'] . '( Prefered as - ' . $v['PlacementPreference']['preference_order'] . ')';
						$assigned = $assignedTo['PlacementRoundParticipant']['name'] . '('. $v['PlacementPreference']['preference_order'] . ')';
					}
				}
			}

			if (empty($assigned)) {

				$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.student_id' => $this->student_id,
					),
					'contain' => array('PlacementRoundParticipant'),
					'order' => array(
						'PlacementParticipatingStudent.modified' => 'DESC',
						'PlacementParticipatingStudent.academic_year' => 'DESC',
						'PlacementParticipatingStudent.round' => 'DESC',
					)
				));

				if (isset($assignedTo['PlacementRoundParticipant']['name'])) {
					$assigned = $assignedTo['PlacementRoundParticipant']['name'];
				} else if (!empty($studentBasic['Student']['department_id']) && is_numeric($studentBasic['Student']['department_id']) && $studentBasic['Student']['department_id'] > 0) {
					$assigned =  ClassRegistry::init('Department')->field('name', array('Department.id' => $studentBasic['Student']['department_id'])) . ' (Registrar Placed)';
				}
				//debug($assignedTo);
			}

			// debug($studentList);
			// debug($prepartory);
			// debug($freshman);
			// debug($entrance);

			if (empty($freshmanMaxResultDB) || $freshmanResultSet == 0) {
				$freshmanMaxResultDB = FRESHMANMAXIMUM;
				$freshmanPercent = DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
			}

			if (empty($prepMaxResultDB) || $prepararoryResultSet == 0) {
				$prepMaxResultDB = PREPARATORYMAXIMUM;
				$preparatoryPercent = DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
			}

			if (empty($entranceMaxResultDB) || $entranceResultSet == 0) {
				$entranceMaxResultDB = ENTRANCEMAXIMUM;
				$entrancePercent = DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
			}

			$this->set(compact('freshmanMaxResultDB', 'prepMaxResultDB', 'entranceMaxResultDB', 'freshmanResultSet', 'prepararoryResultSet', 'entranceResultSet', 'preparatoryPercent', 'freshmanPercent', 'entrancePercent'));
		}

		$this->set(compact('studentBasic', 'studentList', 'entrance', 'prepartory', 'freshman', 'assigned'));
   	}

	public function getStudentPreference($student_id) 
	{
		$this->layout = 'ajax';
		
		$studentBasic = $this->PlacementPreference->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		//debug($studentBasic);

		$allPreferenceEntryStudentsInterested = $this->PlacementPreference->find('all', array(
			'conditions' => array(
				'PlacementPreference.student_id' => $student_id
			),
			'contain' => array(
				'PlacementRoundParticipant', 
				'AcceptedStudent', 
				'Student'
			),
			'order' => array('PlacementPreference.academic_year' => 'DESC', 'PlacementPreference.round' => 'DESC', 'PlacementPreference.preference_order' => 'ASC'),
		));

		//debug($allPreferenceEntryStudentsInterested);

		$all_placement_round_participant_ids = array();

		if (!empty($allPreferenceEntryStudentsInterested)) {
			foreach ($allPreferenceEntryStudentsInterested as $key => $value) {
				//debug($value['PlacementPreference']);
				if(is_null($value['PlacementPreference']['placement_round_participant_id']) || empty($value['PlacementPreference']['placement_round_participant_id']) || $value['PlacementPreference']['placement_round_participant_id'] == 0) {
					debug($value['PlacementPreference']['id']);
					debug($key);
					unset($allPreferenceEntryStudentsInterested[$key]);
				} else {
					$all_placement_round_participant_ids[$value['PlacementPreference']['placement_round_participant_id']] = $value['PlacementPreference']['placement_round_participant_id'];
				}
			}
			$allPreferenceEntryStudentsInterested = array_values($allPreferenceEntryStudentsInterested);
		}

		//debug($allPreferenceEntryStudentsInterested);

		$studentList = array();

		$last_placement_round = '';
		$last_placement_academic_year = '';
		$semester_to_use_for_cgpa = '';

		$freshmanResultSet = 0;
		$freshmanResultPercent = 0;

		$prepararoryResultSet = 0;
		$prepararoryResultPercent = 0;

		$entranceResultSet = 0;
		$entranceResultPercent = 0;

		$freshmanMaxResultDB = NULL;
		$prepMaxResultDB = NULL;
		$entranceMaxResultDB = NULL;
		

		$lastPrefereceoftheStudent = $this->PlacementPreference->find('first', array(
			'conditions' => array(
				'PlacementPreference.student_id' => $student_id,
				//'PlacementPreference.placement_round_participant_id' => $allPreferenceEntryStudentsInterested,
				'OR' => array(
					'PlacementPreference.preference_order IS NOT NULL',
					'PlacementPreference.preference_order != 0',
					'PlacementPreference.preference_order != ""',
				)
			),
			'contain' => array(
				'PlacementRoundParticipant', 
			),
			'order' => array('PlacementPreference.academic_year' => 'DESC', 'PlacementPreference.round' => 'DESC', 'PlacementPreference.preference_order' => 'ASC'),
			'recursive' => -1
		));

		//debug($lastPrefereceoftheStudent);

		//exit();
		
		if (!empty($lastPrefereceoftheStudent)) {

			$last_placement_round = $lastPrefereceoftheStudent['PlacementPreference']['round'];
			$last_placement_academic_year = $lastPrefereceoftheStudent['PlacementPreference']['academic_year'];
			$semester_to_use_for_cgpa = (!empty($lastPrefereceoftheStudent['PlacementRoundParticipant']['semester']) ? $lastPrefereceoftheStudent['PlacementRoundParticipant']['semester'] : ($lastPrefereceoftheStudent['PlacementPreference']['round'] == 1 ? 'I' : 'II'));


			// debug($lastPrefereceoftheStudent);
			// debug($last_placement_round);
			// debug($last_placement_academic_year);

			$resultType = array();

			$placementSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array(
				'conditions' => array(
					'PlacementResultSetting.applied_for' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['applied_for'],
					'PlacementResultSetting.round' => $lastPrefereceoftheStudent['PlacementPreference']['round'],
					'PlacementResultSetting.academic_year' => $lastPrefereceoftheStudent['PlacementPreference']['academic_year'],
					'PlacementResultSetting.program_id' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['program_id'],
					'PlacementResultSetting.program_type_id' => $lastPrefereceoftheStudent['PlacementRoundParticipant']['program_type_id']
				)
			));

			//debug($placementSettings);

			if (!empty($placementSettings)) {
				foreach ($placementSettings as $pl => $pv) {
					$resultType[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];
					//debug($pv['PlacementResultSetting']);
					if ($pv['PlacementResultSetting']['result_type'] == 'EHEECE_total_results') {
						$prepMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : NULL);
						$prepararoryResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$prepararoryResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					} else if ($pv['PlacementResultSetting']['result_type'] == 'freshman_result') {
						$freshmanMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (float) $pv['PlacementResultSetting']['max_result'] : NULL);
						$freshmanResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$freshmanResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					} else if ($pv['PlacementResultSetting']['result_type'] == 'entrance_result') {
						$entranceMaxResultDB = (!empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && (int) $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : NULL);
						$entranceResultSet = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? 1 : 0);
						$entranceResultPercent = (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && (int) $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : NULL);
					}
				}
			}

			// debug($freshmanResultSet);
			// debug($freshmanMaxResultDB);
			// debug($freshmanResultPercent);

			// debug($prepararoryResultSet);
			// debug($prepMaxResultDB);
			// debug($prepararoryResultPercent);
			
			// debug($entranceResultSet);
			// debug($entranceMaxResultDB);
			// debug($entranceResultPercent);


			if (!empty($allPreferenceEntryStudentsInterested)) {
				foreach ($allPreferenceEntryStudentsInterested as $k => $v) {
		
					$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
						'conditions' => array(
							'StudentExamStatus.student_id' => $student_id,
							'StudentExamStatus.academic_status_id != '. DISMISSED_ACADEMIC_STATUS_ID.'',
							'StudentExamStatus.academic_year' => $last_placement_academic_year,
							'StudentExamStatus.semester' => $semester_to_use_for_cgpa,
						),
						'fields' => array(
							'StudentExamStatus.sgpa',
							'StudentExamStatus.cgpa'
						),
						'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
						'group' => array('StudentExamStatus.student_id', 'StudentExamStatus.semester', 'StudentExamStatus.academic_year'),
					));
					
					if (!empty($freshManresult) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && (float) $freshManresult['StudentExamStatus']['cgpa'] > (float) DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT  && $freshmanResultSet) {
						if (!empty($freshmanMaxResultDB) && !empty($freshmanResultPercent)) {
							$freshmanPercent = $freshmanResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((($freshmanResultPercent  * (float) $freshManresult['StudentExamStatus']['cgpa']) /  $freshmanMaxResultDB), 2);
							$freshman = round((($freshmanResultPercent * (float) $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMaxResultDB), 2);
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((float) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
							$freshman = round((((float) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (float)FRESHMANMAXIMUM), 2);
						}
					} else if (!empty($freshManresult) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && (float) $freshManresult['StudentExamStatus']['cgpa'] > (float) DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((int) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
						$freshman = round((((int) DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float) $freshManresult['StudentExamStatus']['cgpa']) / (int) FRESHMANMAXIMUM), 2);
					} else {
						$freshmanPercent = $freshmanResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = 0;
						$freshman = 0;
					}

					$entranceResult = ClassRegistry::init('PlacementEntranceExamResultEntry')->find('first', array(
						'conditions' => array(
							'PlacementEntranceExamResultEntry.student_id' => $student_id,
							'PlacementEntranceExamResultEntry.placement_round_participant_id' => $v['PlacementRoundParticipant']['id'],
							//'PlacementEntranceExamResultEntry.created > ' => date($v['PlacementRoundParticipant']['created'], strtotime("-" . 15 . " day ")),
							'PlacementEntranceExamResultEntry.created > ' => $v['PlacementRoundParticipant']['created'],
							//'PlacementEntranceExamResultEntry.placement_round_participant_id' => $all_placement_round_participant_ids,
						),
						'fields' => array(
							'PlacementEntranceExamResultEntry.result',
							'PlacementEntranceExamResultEntry.result'
						),
						'order' => array('PlacementEntranceExamResultEntry.modified' => 'DESC', 'PlacementEntranceExamResultEntry.created' => 'DESC', 'PlacementEntranceExamResultEntry.result' => 'DESC'),
						'group' => array('PlacementEntranceExamResultEntry.accepted_student_id', 'PlacementEntranceExamResultEntry.student_id', 'PlacementEntranceExamResultEntry.placement_round_participant_id'),
						'recursive' => -1
					));

					//debug($entranceResult);;
					//debug(array_keys($resultType));

					if (isset($entranceResult['PlacementEntranceExamResultEntry']['result']) && is_numeric($entranceResult['PlacementEntranceExamResultEntry']['result']) && (int) $entranceResult['PlacementEntranceExamResultEntry']['result'] >= 0 && $entranceResultSet) {
						if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
							$entrancePercent = $entranceResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
							$entrance = ($entranceResultPercent * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / $entranceMaxResultDB;
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
							$entrance = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
						}
					} else if (isset($entranceResult['PlacementEntranceExamResultEntry']['result']) && is_numeric($entranceResult['PlacementEntranceExamResultEntry']['result']) && (int) $entranceResult['PlacementEntranceExamResultEntry']['result'] >= 0) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
						$entrance = ((int) DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int) $entranceResult['PlacementEntranceExamResultEntry']['result']) / (int) ENTRANCEMAXIMUM;
					} else {
						$entrancePercent = $entranceResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = 0;
						$entrance = 0;
					}

					if (is_numeric($v['AcceptedStudent']['EHEECE_total_results']) && (int) $v['AcceptedStudent']['EHEECE_total_results'] > 100 && $prepararoryResultSet ) {
						if (!empty($prepMaxResultDB) && !empty($prepararoryResultPercent)) {
							$preparatoryPercent = $prepararoryResultPercent;
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((($prepararoryResultPercent * (int) $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
							$prepartory = round((($prepararoryResultPercent * (int) $v['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
						} else {
							$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
							$prepartory = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
						}
					} else if (is_numeric($v['AcceptedStudent']['EHEECE_total_results']) && (int) $v['AcceptedStudent']['EHEECE_total_results'] > 100) {
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
						$prepartory = round((((int) DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int) $v['AcceptedStudent']['EHEECE_total_results']) / (int) PREPARATORYMAXIMUM), 2);
					} else {
						$preparatoryPercent = $prepararoryResultPercent;
						$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['prepartory'] = 0;
						$prepartory = 0;
					}

					$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
						'conditions' => array(
							'PlacementParticipatingStudent.student_id' => $student_id,
							'PlacementParticipatingStudent.placement_round_participant_id' => $v['PlacementRoundParticipant']['id']
						),
						'contain' => array('PlacementRoundParticipant')
					));

					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['academic_year'] = $v['PlacementPreference']['academic_year'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['round'] = $v['PlacementPreference']['round'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_order'] = $v['PlacementPreference']['preference_order'];
					$studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_name'] = $v['PlacementRoundParticipant']['name'];
					$studentList[$v['PlacementPreference']['id']]['AcceptedStudent'] = $v['AcceptedStudent'];
					
					if (isset($assignedTo['PlacementRoundParticipant']['name']) && !empty($assignedTo['PlacementRoundParticipant']['name'])) {
						$studentList[$v['PlacementPreference']['id']]['Assigned'] = $assignedTo['PlacementRoundParticipant']['name'] . '( Prefered as - ' . $v['PlacementPreference']['preference_order'] . ')';
						$assigned = $assignedTo['PlacementRoundParticipant']['name'] . '('. $v['PlacementPreference']['preference_order'] . ')';
					}
				}
			}

			if (empty($assigned)) {

				$assignedTo = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.student_id' => $student_id,
					),
					'contain' => array('PlacementRoundParticipant'),
					'order' => array(
						'PlacementParticipatingStudent.modified' => 'DESC',
						'PlacementParticipatingStudent.academic_year' => 'DESC',
						'PlacementParticipatingStudent.round' => 'DESC',
					)
				));

				if (isset($assignedTo['PlacementRoundParticipant']['name'])) {
					$assigned = $assignedTo['PlacementRoundParticipant']['name'];
				} else if (!empty($studentBasic['Student']['department_id']) && is_numeric($studentBasic['Student']['department_id']) && $studentBasic['Student']['department_id'] > 0) {
					$assigned =  ClassRegistry::init('Department')->field('name', array('Department.id' => $studentBasic['Student']['department_id'])) . ' (Registrar Placed)';
				}
				//debug($assignedTo);
			}

			// debug($studentList);
			// debug($prepartory);
			// debug($freshman);
			// debug($entrance);

			if (empty($freshmanMaxResultDB) || $freshmanResultSet == 0) {
				$freshmanMaxResultDB = FRESHMANMAXIMUM;
				$freshmanPercent = DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
			}

			if (empty($prepMaxResultDB) || $prepararoryResultSet == 0) {
				$prepMaxResultDB = PREPARATORYMAXIMUM;
				$preparatoryPercent = DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
			}

			if (empty($entranceMaxResultDB) || $entranceResultSet == 0) {
				$entranceMaxResultDB = ENTRANCEMAXIMUM;
				$entrancePercent = DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
			}

			$this->set(compact('freshmanMaxResultDB', 'prepMaxResultDB', 'entranceMaxResultDB', 'freshmanResultSet', 'prepararoryResultSet', 'entranceResultSet', 'preparatoryPercent', 'freshmanPercent', 'entrancePercent'));
		}

		$this->set(compact('studentBasic', 'studentList', 'entrance', 'prepartory', 'freshman', 'assigned'));
   	}

	/*
	public function index() {
		$this->PlacementPreference->recursive = 0;
		$this->set('placementPreferences', $this->Paginator->paginate());
	}

    public function view($id = null) {
		if (!$this->PlacementPreference->exists($id)) {
			throw new NotFoundException(__('Invalid placement preference'));
		}
		$options = array('conditions' => array('PlacementPreference.' . $this->PlacementPreference->primaryKey => $id));
		$this->set('placementPreference', $this->PlacementPreference->find('first', $options));
	}

    public function add() {
		if ($this->request->is('post')) {
			$this->PlacementPreference->create();
			if ($this->PlacementPreference->save($this->request->data)) {
				$this->Flash->success(__('The placement preference has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The placement preference could not be saved. Please, try again.'));
			}
		}
		$acceptedStudents = $this->PlacementPreference->AcceptedStudent->find('list');
		$students = $this->PlacementPreference->Student->find('list');
		$placementRoundParticipants = $this->PlacementPreference->PlacementRoundParticipant->find('list');
		$users = $this->PlacementPreference->User->find('list');
		$this->set(compact('acceptedStudents', 'students', 'placementRoundParticipants', 'users'));
	}

    public function edit($id = null) {
		if (!$this->PlacementPreference->exists($id)) {
			throw new NotFoundException(__('Invalid placement preference'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->PlacementPreference->save($this->request->data)) {
				$this->Flash->success(__('The placement preference has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The placement preference could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('PlacementPreference.' . $this->PlacementPreference->primaryKey => $id));
			$this->request->data = $this->PlacementPreference->find('first', $options);
		}
		$acceptedStudents = $this->PlacementPreference->AcceptedStudent->find('list');
		$students = $this->PlacementPreference->Student->find('list');
		$placementRoundParticipants = $this->PlacementPreference->PlacementRoundParticipant->find('list');
		$users = $this->PlacementPreference->User->find('list');
		$this->set(compact('acceptedStudents', 'students', 'placementRoundParticipants', 'users'));
	}

   	public function delete($id = null) {
		$this->PlacementPreference->id = $id;
		if (!$this->PlacementPreference->exists()) {
			throw new NotFoundException(__('Invalid placement preference'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->PlacementPreference->delete()) {
			$this->Flash->success(__('The placement preference has been deleted.'));
		} else {
			$this->Flash->error(__('The placement preference could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	*/
}
