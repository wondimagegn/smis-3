<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class PlacementSettingsController extends AppController
{
	public $name = 'PlacementSettings';
	public $uses = array();

	public $menuOptions = array(
		'parent' => 'placement',
		'alias' => array(
			'quota' => 'Add Placement Quota',
			'placement_result_setting' => 'Set Placement Result Settings',
			'placement_additional_point' => 'Set Placement Additional Points',
			'prepare' => 'Prepare Students for Placement',
			'run_placement' => 'Run Auto Placement',
			'direct_placement' => 'Direct Department Placement',
			'cancel_auto_placement' => 'Cancel Auto Placement',
			'auto_report' => 'Auto Report',
			'approve_placement' => 'Approve Placement',
			'move_student_to_originalcollege_for_placement' => 'Move Student To Their Original College for Placement'

		),
		'exclude' => array(
			'get_preference_applied_student',
			'get_placement_statistics_summary',
			'print_autoplaced_pdf',
			'export_autoplaced_xls',
			'index'
		),
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
        $this->Auth->Allow('placement_identifier');
    }

	public function beforeRender(Event $event)
	{
		parent::beforeRender($event);
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 2, date('Y') - 1);

		////////////////////////////// BLOCK: DONT REMOVE ANY VARIABLE /////////////////////////////////////

		$defaultacademicyear = $this->AcademicYear->currentAcademicYear();

		if (is_numeric(ACY_BACK_FOR_PLACEMENT) && ACY_BACK_FOR_PLACEMENT) {
			$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_PLACEMENT), (explode('/', $defaultacademicyear)[0]));
		} else {
			$acyear_array_data[$defaultacademicyear] = $defaultacademicyear;
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear'));

		//////////////////////////////////// END BLOCK ///////////////////////////////////////////////////
	}


	function __init_search()
	{
		if (!empty($this->request->data['PlacementSetting'])) {
			$this->Session->write('prepare_search_data', $this->request->data['PlacementSetting']);
		} else if ($this->Session->check('prepare_search_data')) {
			$this->request->data['PlacementSetting'] = $this->Session->read('prepare_search_data');
		}
	}

	public function index()
	{
	}

	public function quota($groupIdentifier = null)
	{
		if (empty($groupIdentifier)) {
			$this->Flash->error('Inorder to see the placement quota, please add the placement round participants.');
			return $this->redirect(array('controller' => 'placementRoundParticipants', 'action' => 'index'));
			//placementRoundParticipants/index
		}

		if (isset($this->request->data['quota']) && !empty($this->request->data['quota'])) {
			$reformat = classRegistry::init('PlacementRoundParticipant')->reformatDevRegion($this->request->data);
			if (classRegistry::init('PlacementRoundParticipant')->saveAll($reformat['PlacementSetting'], array('validate' => 'first'))) {
				$this->Flash->success('The placement round participant  quota has been saved, and will be used in the computation.');
				//redirect to quota management where can fill the quota for the involved units
				//groupIdentifier
				return $this->redirect(array('controller' => 'placement_settings', 'action' => 'quota', $groupIdentifier));
			} else {
				$this->Flash->error('The placement round participant could not be saved. Please, try again.');
			}
		}

		$placementRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('all', array('conditions' => array('PlacementRoundParticipant.group_identifier' => $groupIdentifier), 'recursive' => -1));
		$firstRowRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('first', array('conditions' => array('PlacementRoundParticipant.group_identifier' => $groupIdentifier), 'recursive' => -1));

		$lockEditing = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
			'conditions' => array(
				'PlacementParticipatingStudent.academic_year' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'],
				'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
				'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
				'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']
			),
			'recursive' => -1
		));

		$selectedDevelopingRegions = explode(',', $firstRowRoundParticipants['PlacementRoundParticipant']['developing_region']);
		$forcollege = explode('c~', $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']);
		$fordepartment = explode('d~', $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']);

		if (isset($forcollege[1]) && !empty($forcollege[1])) {

			$active_sections_in_the_given_acy_semester = ClassRegistry::init('Section')->find('list', array(
				'conditions' => array(
					'Section.college_id' => $forcollege[1],
					'Section.department_id IS NULL',
					'Section.academicyear' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'],
					'Section.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'Section.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'Section.archive' => 0
				),
				'fields' => array('Section.id', 'Section.id'),
			));

			//debug($active_sections_in_the_given_acy_semester );

			$active_students_in_sections = ClassRegistry::init('StudentsSection')->find('list', array(
				'conditions' => array(
					'StudentsSection.section_id' => $active_sections_in_the_given_acy_semester,
					'StudentsSection.archive' => 0
				),
				'group' => array('StudentsSection.section_id', 'StudentsSection.student_id'),
				'order' => array('StudentsSection.section_id' => 'DESC', 'StudentsSection.id' => 'DESC'),
				'fields' => array('StudentsSection.student_id', 'StudentsSection.student_id'),
			));

			//debug($active_students_in_sections);

			$accepted_students_id_from_students_table =  ClassRegistry::init('Student')->find('list', array(
				'conditions' => array(
					'Student.id' => $active_students_in_sections,
				),
				'fields' => array('Student.accepted_student_id', 'Student.accepted_student_id'),
			));

			//debug($accepted_students_id_from_students_table);

			$totalStudentsForPlacement = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.department_id IS NULL',
					'AcceptedStudent.college_id' => $forcollege[1],
					'AcceptedStudent.academicyear LIKE ' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			$quota_sum['disable'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' =>  $accepted_students_id_from_students_table,
					'AcceptedStudent.college_id' => $forcollege[1],
					'AcceptedStudent.disability is not null',
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			$quota_sum['region'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.college_id' => $forcollege[1],
					'AcceptedStudent.region_id' => $selectedDevelopingRegions,
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			//'AcceptedStudent.sex'=>'female',
			$quota_sum['female'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.college_id' => $forcollege[1],
					'AcceptedStudent.sex' => 'female',
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

		} else if (isset($fordepartment[1]) && !empty($fordepartment[1])) {


			$active_sections_in_the_given_acy_semester = ClassRegistry::init('Section')->find('list', array(
				'conditions' => array(
					'Section.department_id' => $fordepartment[1],
					'Section.academicyear' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'],
					'Section.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'Section.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'Section.archive' => 0
				),
				'fields' => array('Section.id', 'Section.id'),
			));

			$active_students_in_sections = ClassRegistry::init('StudentsSection')->find('list', array(
				'conditions' => array(
					'StudentsSection.section_id' => $active_sections_in_the_given_acy_semester,
					'StudentsSection.archive' => 0
				),
				'group' => array('StudentsSection.section_id', 'StudentsSection.student_id'),
				'order' => array('StudentsSection.section_id' => 'DESC', 'StudentsSection.id' => 'DESC'),
				'fields' => array('StudentsSection.student_id', 'StudentsSection.student_id'),
			));

			$accepted_students_id_from_students_table =  ClassRegistry::init('Student')->find('list', array(
				'conditions' => array(
					'Student.id' => $active_students_in_sections,
				),
				'fields' => array('Student.accepted_student_id', 'Student.accepted_student_id'),
			));

			$totalStudentsForPlacement = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.department_id' => $fordepartment[1],
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			$quota_sum['disable'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.department_id' => $fordepartment[1],
					'AcceptedStudent.disability is not null',
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			$quota_sum['region'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table ,
					'AcceptedStudent.department_id' => $fordepartment[1],
					'AcceptedStudent.region_id' => $selectedDevelopingRegions,
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));

			//'AcceptedStudent.sex'=>'female',
			$quota_sum['female'] = ClassRegistry::init('AcceptedStudent')->find('count', array(
				'conditions' => array(
					'AcceptedStudent.id' => $accepted_students_id_from_students_table,
					'AcceptedStudent.department_id' => $fordepartment[1],
					'AcceptedStudent.sex' => 'female',
					'AcceptedStudent.academicyear LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					//'AcceptedStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					//'AcceptedStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
					'AcceptedStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'AcceptedStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
				),
				'recursive' => -1
			));
		}

		$totalStudentsPreparedForPlacement = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
			'conditions' => array(
				'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for'],
				'PlacementParticipatingStudent.academic_year LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
				'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
				'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']
			),
			'recursive' => -1
		));

		if ($totalStudentsPreparedForPlacement) {

			$quota_sum['pdisable'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for'],
					'PlacementParticipatingStudent.academic_year LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
					'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'PlacementParticipatingStudent.accepted_student_id IN (SELECT id FROM accepted_students WHERE id in (' . join(',', $accepted_students_id_from_students_table) . ') AND disability IS NOT NULL AND disability <> \'\' )'
				),
				'recursive' => -1
			));

			$quota_sum['pregion'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for'],
					'PlacementParticipatingStudent.academic_year LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
					'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'PlacementParticipatingStudent.accepted_student_id IN (SELECT id FROM accepted_students WHERE id in (' . join(',', $accepted_students_id_from_students_table) . ') AND region_id IN (\'' . $firstRowRoundParticipants['PlacementRoundParticipant']['developing_region'] . '\'))'
				),
				'recursive' => -1
			));

			//'AcceptedStudent.sex'=>'female',
			$quota_sum['pfemale'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for'],
					'PlacementParticipatingStudent.academic_year LIKE' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'] . '%',
					'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
					'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'PlacementParticipatingStudent.accepted_student_id IN (SELECT id FROM  accepted_students WHERE id in (' . join(',', $accepted_students_id_from_students_table) . ') AND (sex = \'female\' OR sex = \'f\') )'
				),
				//'contain' => array('AcceptedStudent')
			));


			//Female, developing regions and disability stat
			// $stat['female'] = ClassRegistry::init('PlacementPreference')->getPreferenceStat($firstRowRoundParticipants, 'female');
			// $stat['region'] = ClassRegistry::init('PlacementPreference')->getPreferenceStat($firstRowRoundParticipants, 'region');
			// $stat['disable'] = ClassRegistry::init('PlacementPreference')->getPreferenceStat($firstRowRoundParticipants, 'disable');
			// $stat['all'] = ClassRegistry::init('PlacementPreference')->getPreferenceStat($firstRowRoundParticipants);

			//prepared preference statistics
			$stat['pfemale'] = ClassRegistry::init('PlacementPreference')->getPreparedPreferenceStat($firstRowRoundParticipants, 'female');
			$stat['pregion'] = ClassRegistry::init('PlacementPreference')->getPreparedPreferenceStat($firstRowRoundParticipants, 'region');
			$stat['pdisable'] = ClassRegistry::init('PlacementPreference')->getPreparedPreferenceStat($firstRowRoundParticipants, 'disable');
			$stat['pall'] = ClassRegistry::init('PlacementPreference')->getPreparedPreferenceStat($firstRowRoundParticipants);

			debug($stat);
			debug($quota_sum);
		}

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		$types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization');
		// $programs = classRegistry::init('Program')->find('list');
		// $programTypes = classRegistry::init('ProgramType')->find('list');

		$programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
		$program_types_available_for_placement_preference = Configure::read('program_types_available_for_placement_preference');

		$programs = classRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $programs_available_for_placement_preference)));
		$programTypes = classRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference)));

		$fieldSetups = 'type,foreign_key,name,edit';

		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}

		//$allUnits = classRegistry::init('Department')->allUnits();
		$developingRegions = classRegistry::init('Region')->find('list');

		$this->set(compact(
			'colleges',
			'stat',
			'developingRegions',
			'types',
			'quota_sum',
			'allUnits',
			'departments',
			'totalStudentsForPlacement',
			'colleges',
			'fieldSetups',
			'programs',
			'programTypes',
			'firstRowRoundParticipants',
			'placementRoundParticipants',
			'selectedDevelopingRegions',
			'totalStudentsPreparedForPlacement'
		));
	}

	public function placement_result_setting($groupIdentifier = null)
	{
		if ($this->request->is('post')) {

			debug($this->request->data['PlacementResultSetting']);

			if (isset($this->request->data['PlacementResultSetting']) && !empty($this->request->data['PlacementResultSetting'])) {

				$reformated = classRegistry::init('PlacementResultSetting')->reformat($this->request->data);
				debug($reformated);
				// check duplication
				$checkDuplication = classRegistry::init('PlacementResultSetting')->isDuplicated($this->request->data);

				if ($reformated != false && $checkDuplication == false) {
					$groupIdentifier = $reformated['PlacementResultSetting'][1]['group_identifier'];

					ClassRegistry::init('PlacementResultSetting')->deleteAll(array('PlacementResultSetting.group_identifier' => $groupIdentifier), false);


					classRegistry::init('PlacementResultSetting')->create();

					if (classRegistry::init('PlacementResultSetting')->saveAll($reformated['PlacementResultSetting'], array('validate' => 'first'))) {
						$this->Flash->success('The placement result settings has been saved. You can edit the settings before auto placement is run.');
						//redirect to quota management where can fill the quota for the involved units
						//groupIdentifier
						return $this->redirect(array('controller' => 'placement_settings', 'action' => 'placement_result_setting', $groupIdentifier));
					} else {
						$this->Flash->error('The placement result settings  could not be saved. Please, try again.');
					}
				} else {
					$error = classRegistry::init('PlacementResultSetting')->invalidFields();

					if (isset($error['result_type'])) {
						$this->Flash->error($error['result_type'][0]);
					} else if (isset($error['percent'])) {
						$this->Flash->error($error['percent'][0]);
					} else if (isset($checkDuplication)) {
						$this->Flash->error('We are unable to create placement result settings since it  has already created earlier.');
						return $this->redirect(array('controller' => 'placement_settings', 'action' => 'placement_result_setting', $checkDuplication));
					}
				}
			}
		}

		if (empty($groupIdentifier) && isset($this->request->data['PlacementResultSetting'][1]['group_identifier'])) {
			$group_identifier = $this->request->data['PlacementResultSetting'][1]['group_identifier'];
		}

		$placementResultSettings = classRegistry::init('PlacementResultSetting')->find('all', array('conditions' => array('PlacementResultSetting.group_identifier' => $groupIdentifier), 'recursive' => -1));


		if (!empty($placementResultSettings)) {
			$data = array();
			$count = 1;
			foreach ($placementResultSettings as $k => $p) {
				$data['PlacementResultSetting'][$count] = $p['PlacementResultSetting'];
				$count++;
			}
			$this->request->data = $data;
			debug($this->request->data);
		}

		$firstRowRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('first', array('conditions' => array('PlacementRoundParticipant.group_identifier' => $groupIdentifier), 'recursive' => -1));

		if (!empty($firstRowRoundParticipants)) {
			$lockEditing = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.academic_year' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'],
					'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
					'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']
				),
				'recursive' => -1
			));
		} else {
			$lockEditing = 0;
		}

		$this->set(compact('lockEditing', 'placementResultSettings'));


		$types = array('freshman_result' => 'Freshman Result', 'EHEECE_total_results' => 'Preparatory Exam Result',  'entrance_result' => 'Entrance Exam Result For the Field');
		$fieldSetups = 'result_type,percent,max_result,edit';

		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
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

		$allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

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

		if (isset($this->request->data['PlacementResultSetting']) && !empty($this->request->data['PlacementResultSetting'][1])) {
			$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for($this->request->data['PlacementResultSetting'][1]);
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementResultSetting'][1]['applied_for']);
		} else {
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACYRoundAppliedFor['academic_year']);
			}
			//$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACY['academic_year']);
		}
		//debug($appliedForList);

		//debug($latestACYRoundAppliedFor);

		$this->set(compact('appliedForList'));

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes', 'availableAcademicYears', 'latestACYRoundAppliedFor', 'latestACY'));
	}

	public function placement_additional_point($groupIdentifier = null)
	{
		if ($this->request->is('post')) {

			if (isset($this->request->data['PlacementAdditionalPoint']) && !empty($this->request->data['PlacementAdditionalPoint'])) {

				$reformated = classRegistry::init('PlacementAdditionalPoint')->reformat($this->request->data);
				// check duplication
				$checkDuplication = classRegistry::init('PlacementAdditionalPoint')->isDuplicated($this->request->data);

				if ($reformated != false && $checkDuplication == false) {
					$groupIdentifier = $reformated['PlacementAdditionalPoint'][1]['group_identifier'];

					classRegistry::init('PlacementAdditionalPoint')->create();

					if (classRegistry::init('PlacementAdditionalPoint')->saveAll($reformated['PlacementAdditionalPoint'], array('validate' => 'first'))) {
						$this->Flash->success('The placement additional points settings has been saved, and please edit the ratio before the deadline.');
						//redirect to quota management where can fill the quota for the involved units
						//groupIdentifier
						return $this->redirect(array('controller' => 'placement_settings', 'action' => 'placement_additional_point', $groupIdentifier));
					} else {
						$this->Flash->error('The placement additional point  settings  could not be saved. Please, try again.');
					}
				} else {
					$error = classRegistry::init('PlacementAdditionalPoint')->invalidFields();

					if (isset($error['result_type'])) {
						$this->Flash->error( $error['type'][0]);
					} else if (isset($error['percent'])) {
						$this->Flash->error($error['percent'][0]);
					} else if (isset($checkDuplication)) {
						$this->Flash->error('We are unable to create placement additional point  since it  has already created earlier.');
						return $this->redirect(array('controller' => 'placement_settings', 'action' => 'placement_additional_point', $checkDuplication));
					}
				}
			}
		}

		if (isset($groupIdentifier) && !empty($groupIdentifier)) {

			$placementAdditionalPoints = classRegistry::init('PlacementAdditionalPoint')->find('all', array('conditions' => array('PlacementAdditionalPoint.group_identifier' => $groupIdentifier), 'recursive' => -1));

			if (isset($placementAdditionalPoints) && !empty($placementAdditionalPoints)) {
				$data = array();
				$count = 1;
				foreach ($placementAdditionalPoints as $k => $p) {
					$data['PlacementAdditionalPoint'][$count] = $p['PlacementAdditionalPoint'];
					$count++;
				}
				$this->request->data = $data;
			}

			$firstRowRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('first', array('conditions' => array('PlacementRoundParticipant.group_identifier' => $groupIdentifier), 'recursive' => -1));

			$lockEditing = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.academic_year' => $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year'],
					'PlacementParticipatingStudent.program_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id'],
					'PlacementParticipatingStudent.round' => $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round'],
					'PlacementParticipatingStudent.applied_for' => $firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']
				),
				'recursive' => -1
			));
		}

		$this->set(compact('lockEditing', 'placementAdditionalPoints'));
		$colleges = classRegistry::init('College')->find('list');
		$departments = classRegistry::init('Department')->find('list');
		$types = array('female' => 'Female Point', 'disability' => 'Disability Point', 'developing_region' => 'Developing Region Point');
		$programs = classRegistry::init('Program')->find('list');
		$programTypes = classRegistry::init('ProgramType')->find('list');
		$fieldSetups = 'type,point,edit';
		//$allUnits = classRegistry::init('Department')->allUnits();

		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes'));
	}

	public function prepare()
	{

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

		if ($this->request->is('post')) {

			//debug($this->request->data);

			$r = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($this->request->data['PlacementSetting']['round']);

			if (isset($this->request->data['readyForPlacement']) && !empty($this->request->data['readyForPlacement'])) {
				//debug($this->request->data);
				$reformated = classRegistry::init('PlacementParticipatingStudent')->reformat($this->request->data);
				debug($reformated);

				if ($reformated == 1) {
					$this->Flash->error('Placement is already run for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. You can not add selected students.');
					$this->__init_search();
				} else if ($reformated == false) {
					$this->Flash->info('No selected students to to add.');
				} else {
					if (!empty($reformated['PlacementParticipatingStudent'])) {
						classRegistry::init('PlacementParticipatingStudent')->create();
						if (classRegistry::init('PlacementParticipatingStudent')->saveAll($reformated['PlacementParticipatingStudent'], array('validate' => 'first'))) {
							$this->Flash->success('The selected '. (count($reformated['PlacementParticipatingStudent'])) . ' students are saved and ready to be placed for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round.');
							$this->__init_search();
						} else {
							$this->Flash->error('The selected students could not be saved for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round . Please, try again.');
						}
					}
				}
			} else if (isset($this->request->data['deleteFormPlacementReady']) && !empty($this->request->data['deleteFormPlacementReady'])) {

				$reformated = classRegistry::init('PlacementParticipatingStudent')->reformatForDelete($this->request->data);
				debug($reformated);

				if ($reformated == 1) {
					$this->Flash->error('Placement is already run for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. You can not delete selected students.');
					$this->__init_search();
				} else if ($reformated == false) {
					$this->Flash->info('No students are selected to delete.');
				} else {
					if (!empty($reformated['PlacementParticipatingStudent']) && $reformated != false) {
						if (ClassRegistry::init('PlacementParticipatingStudent')->deleteAll(array('PlacementParticipatingStudent.id' => $reformated['PlacementParticipatingStudent']), false)) {
							$this->Flash->success('The selected '. (count($reformated['PlacementParticipatingStudent'])) . ' students are now deleted from ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. You can Add them again if required');
							$this->__init_search();
						} else {
							$this->Flash->error('Could not delete the selected students from ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. Please, try again.');
						}
					}
				}
			}
		}


		$types = array('freshman_result' => 'Freshman Result', 'EHEECE_total_results' => 'Preparatory Exam Result',  'entrance_result' => 'Entrance Exam Result For the Field');
		$fieldSetups = 'result_type,percent,max_result,edit';


		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
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

		// $allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));


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

		if (isset($this->request->data['PlacementSetting']) && !empty($this->request->data['PlacementSetting'])) {
			$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for($this->request->data['PlacementSetting']);
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementSetting']['applied_for']);
		} else {
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACYRoundAppliedFor['academic_year']);
			}
		}
		debug($appliedForList);
		debug($latestACYRoundAppliedFor);

		$this->__init_search();

		$selectedLimit = (isset($this->request->data['PlacementSetting']['limit']) ? $this->request->data['PlacementSetting']['limit'] : 50);


		$this->set(compact('colleges', 'types', 'selectedLimit', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes', 'appliedForList', 'availableAcademicYears', 'latestACYRoundAppliedFor'));
	}


	public function run_placement()
	{
		debug($this->request->data);

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

		if (isset($this->request->data['runAutomPlacement']) && !empty($this->request->data['runAutomPlacement'])) {

			$isPlacementAlreadyRun = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
					'OR' => array(
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
						'PlacementParticipatingStudent.status' => 1
					)
				)
			));

			debug($isPlacementAlreadyRun);

			if ($isPlacementAlreadyRun == 0) {
				$checkplacementsetting = ClassRegistry::init('PlacementRoundParticipant')->placementSettingDefined($this->request->data['PlacementSetting']);
				if ($checkplacementsetting) {
					//check if the deadline is not passed
					$preferenceDeadline = classRegistry::init('PlacementDeadline')->find('count', array(
						'conditions' => array(
							'PlacementDeadline.program_id' => $this->request->data['PlacementSetting']['program_id'],
							'PlacementDeadline.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
							'PlacementDeadline.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
							'PlacementDeadline.academic_year LIKE' => $this->request->data['PlacementSetting']['academic_year'] . '%',
							//	'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
						)
					));

					$r = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($this->request->data['PlacementSetting']['round']);

					if ($preferenceDeadline['PlacementDeadline']['deadline'] < date("Y-m-d H:i:s")) {
						//preference deadline is passed, start processing the algorithm
						if (isset($this->request->data['runAutomPlacement']) && !empty($this->request->data['runAutomPlacement'])) {
							debug($this->request->data);
							$autoplacedstudents = ClassRegistry::init('PlacementPreference')->auto_placement_algorithm($this->request->data);
						}

						if (!empty($autoplacedstudents)) {
							$this->Flash->success('The auto placement of ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round is run successfully. In order to run again you have to cancel the previous auto placement first.');
							//record the auto placement to the lock database
							$this->set(compact('autoplacedstudents'));
							$this->Session->write('autoplacedstudents', $autoplacedstudents);
							$this->Session->write('searchdata', $this->request->data);
						}
					} else {
						$this->Flash->error('The deadline for filling the preference  for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round  is not passed. Please wait till the deadline to run auto placement.');
					}
				} else {
					$error = ClassRegistry::init('PlacementRoundParticipant')->invalidFields();

					if (isset($error['placement_round_participant'][0])) {
						$this->Flash->error($error['placement_round_participant'][0]);
						$this->redirect(array('controller' => 'PlacementRoundParticipants', 'action' => 'add'));
					} elseif (isset($error['placement_result_setting'][0])) {
						$this->Flash->error($error['placement_result_setting'][0]);
						$this->redirect(array('controller' => 'placement_settings', 'action' => 'placement_result_setting'));
					} elseif (isset($error['placement_participating_student'][0])) {
						$this->Flash->error($error['placement_participating_student'][0]);
						$this->redirect(array('controller' => 'placement_settings','action' => 'prepare'));
					}
				}
			} else {
				$r = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($this->request->data['PlacementSetting']['round']);
				$this->Flash->error('You have already run an auto placement for ' . $this->request->data['PlacementSetting']['academic_year'] . ' academic year for round ' . $r . '. In order to run again you have to cancell the previous auto placement first ');
			}
		}


		$types = array('freshman_result' => 'Freshman Result', 'EHEECE_total_results' => 'Preparatory Exam Result',  'entrance_result' => 'Entrance Exam Result For the Field');
		$fieldSetups = 'result_type,percent,max_result,edit';


		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
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

		// $allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

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

		if (isset($this->request->data['PlacementSetting']) && !empty($this->request->data['PlacementSetting'])) {
			$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for($this->request->data['PlacementSetting']);
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementSetting']['applied_for']);
		} else {
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACYRoundAppliedFor['academic_year']);
			}
		}
		debug($appliedForList);
		debug($latestACYRoundAppliedFor);

		//$this->__init_search();

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes', 'appliedForList', 'availableAcademicYears', 'latestACYRoundAppliedFor'));

	}

	// function to view pdf
	public function print_autoplaced_pdf()
	{
		$autoplacedstudents = $this->Session->read('autoplacedstudents');
		$this->set(compact('autoplacedstudents', 'searchdata'));
		$this->layout = 'pdf';
		$this->render();
	}

	// function to export
	public function export_autoplaced_xls()
	{
		$autoplacedstudents = $this->Session->read('autoplacedstudents');
		$this->set('autoplacedstudents', $autoplacedstudents);
	}

	public function cancel_auto_placement()
	{

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

		if (isset($this->request->data['cancelPlacement']) && !empty($this->request->data['cancelPlacement'])) {

			$isPlacementAlreadyRun = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
					"OR" => array(
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
						'PlacementParticipatingStudent.placement_round_participant_id != 0',
						'PlacementParticipatingStudent.placement_round_participant_id !=""',
						'PlacementParticipatingStudent.status' => 1
					)
				)
			));

			$isPlacementAppoved = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
					'PlacementParticipatingStudent.status' => 1,
				),
			));

			debug($isPlacementAlreadyRun);

			$r = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($this->request->data['PlacementSetting']['round']);

			if ($isPlacementAlreadyRun == 0 && $isPlacementAppoved == 0) {
				// cancel the placement if not approved
				$cancelled = ClassRegistry::init('PlacementPreference')->cancel_placement_algorithm($this->request->data);
				if ($cancelled) {
					$this->Flash->success('You have cancelled auto placement of ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. Please run again to see different result.');
				}
			} else {
				// cancel the placement if not approved
				if ($isPlacementAppoved) {
					$this->Flash->error('The auto placement is approved for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. Cancellation is possible only if not approved.');
				} else {
					$this->Flash->error('There is no autoplacement which needs cancellation for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). '  for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. In order to cancel auto placement,you need to run Auto Placement first.');
				}
			}
		}

		$types = array('freshman_result' => 'Freshman Result', 'EHEECE_total_results' => 'Preparatory Exam Result',  'entrance_result' => 'Entrance Exam Result For the Field');
		$fieldSetups = 'result_type,percent,max_result,edit';


		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
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

		// $allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

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

		if (isset($this->request->data['PlacementSetting']) && !empty($this->request->data['PlacementSetting'])) {
			$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for($this->request->data['PlacementSetting']);
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementSetting']['applied_for']);
		} else {
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACYRoundAppliedFor['academic_year']);
			}
		}
		//debug($appliedForList);
		//debug($latestACYRoundAppliedFor);

		//$this->__init_search();

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes', 'appliedForList', 'availableAcademicYears', 'latestACYRoundAppliedFor'));
	}

	public function approve_placement()
	{

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
		$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));


		if (isset($this->request->data['approvePlacement']) && !empty($this->request->data['approvePlacement'])) {

			$isAutoPlacementRun = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
					"OR" => array(
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
						'PlacementParticipatingStudent.placement_round_participant_id != 0',
						'PlacementParticipatingStudent.placement_round_participant_id !=""',
					)
				)
			));

			$isPlacementAppoved = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
					'PlacementParticipatingStudent.status' => 1,
				),
			));

			$check_prepared = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $this->request->data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.academic_year' => $this->request->data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.round' => $this->request->data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
				)
			));

			$r = ClassRegistry::init('PlacementRoundParticipant')->roundLabel($this->request->data['PlacementSetting']['round']);

			//debug($isPlacementAppoved);

			//debug($isAutoPlacementRun);

			if ($check_prepared != 0 && $isAutoPlacementRun != 0 && $isPlacementAppoved == 0) {
				// approve the placement
				$approved = ClassRegistry::init('PlacementPreference')->approve_placement($this->request->data);

				if ($approved == 1) {
					$this->Flash->success('You have approved Auto Placement of ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round result.');
				} else if ($approved == 2) {
					$this->Flash->warning('Auto placement of ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round result was already approved earlier, and students are dispatched to their assigned units.');
				}
			} else {
				if ($isPlacementAppoved) {
					$this->Flash->warning('Auto placement of ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ' academic year for ' . $r . ' round result was already approved earlier, and students are dispatched to their assigned units.');
				} else if ($check_prepared == 0) {
					$this->Flash->error('There is no prepared student found under ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ', ' . $r . ' round. In order to approve Auto Placement here, you need to prepare the students first and run Auto Placement.');
				} else {
					$this->Flash->error('You need to run Auto Placement first for ' . (count(explode('c~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $colleges[explode('c~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  (count(explode('d~', $this->request->data['PlacementSetting']['applied_for'])) > 1 ? $departments[explode('d~', $this->request->data['PlacementSetting']['applied_for'])[1]] :  '' )). ' for ' . $this->request->data['PlacementSetting']['academic_year'] . ',  ' . $r . ' round in order to approve Auto Placement.');
				}
			}
		}

		$types = array('freshman_result' => 'Freshman Result', 'EHEECE_total_results' => 'Preparatory Exam Result',  'entrance_result' => 'Entrance Exam Result For the Field');
		$fieldSetups = 'result_type,percent,max_result,edit';


		$availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
			'group' => array('PlacementRoundParticipant.academic_year'),
			'order' => array('PlacementRoundParticipant.academic_year DESC')
		));

		if (empty($availableAcademicYears)) {
			$currACY = $this->AcademicYear->current_academicyear();
			$availableAcademicYears[$currACY] = $currACY;
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

		// $allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

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

		if (isset($this->request->data['PlacementSetting']) && !empty($this->request->data['PlacementSetting'])) {
			$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for($this->request->data['PlacementSetting']);
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round($this->request->data['PlacementSetting']['applied_for']);
		} else {
			$latestACYRoundAppliedFor = ClassRegistry::init('PlacementRoundParticipant')->latest_defined_academic_year_and_round();
			if (!empty($availableAcademicYears)) {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $availableAcademicYears);
			} else {
				$appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(null, $latestACYRoundAppliedFor['academic_year']);
			}
		}
		//debug($appliedForList);
		//debug($latestACYRoundAppliedFor);

		//$this->__init_search();


		$this->set(compact('types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes', 'appliedForList', 'availableAcademicYears', 'latestACYRoundAppliedFor'));
	}

	public function auto_report()
	{
		$this->__view_placement_report();
	}

	public function direct_placement()
	{
		$this->__transfer_placement();
	}

	function __transfer_placement()
	{
		if (isset($this->request->data['Search']) && !empty($this->request->data['Search'])) {
			$acceptedStudents = classRegistry::init('PlacementPreference')->getAssignedStudentsForDirectPlacement($this->request->data);
			$units = classRegistry::init('PlacementRoundParticipant')->getParticipatingUnitForDirectPlacement($this->request->data["PlacementSetting"]);
			$this->set(compact('acceptedStudents', 'units'));
		}

		if ((isset($this->request->data['assigndirectly']) && !empty($this->request->data['assigndirectly']))) {
			debug($this->request->data);
			if (isset($this->request->data['PlacementDirectly']['placement_round_participant_id']) && !empty($this->request->data['PlacementDirectly']['placement_round_participant_id'])) {
				//debug($this->request->data);
				$directlyPlaced = ClassRegistry::init('PlacementPreference')->direct_placement($this->request->data, "DIRECT PLACED");
				if ($directlyPlaced) {
					$this->Flash->success('The selected students has been transfered to the selected unit.');
				}
			} else {
				$this->Flash->error('Please select the unit you want to assign.');
			}
		}

		$colleges = classRegistry::init('College')->find('list');
		$departments = classRegistry::init('Department')->find('list');
		$programs = classRegistry::init('Program')->find('list');
		$programTypes = classRegistry::init('ProgramType')->find('list');
		$fieldSetups = 'result_type,percent,edit';
		//$allUnits = classRegistry::init('Department')->allUnits();

		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes'));
		$this->render('direct_placement');
	}

	function __view_placement_report()
	{

		if (isset($this->request->data['Search']) && !empty($this->request->data['Search'])) {
			debug($this->request->data);
			$autoplacedstudents = classRegistry::init('PlacementPreference')->getAssignedStudents($this->request->data);
			$this->set(compact('autoplacedstudents'));
		}

		if ((isset($this->request->data['generatePlacedList']) && !empty($this->request->data['generatePlacedList']))) {

			$selected_academic_year = $this->request->data['Search']['academic_year'];
			$university = ClassRegistry::init('University')->find('first', array('order' => array('University.created DESC')));
			$autoplacedstudents = classRegistry::init('PlacementPreference')->getAssignedStudents($this->request->data);

			$this->set(compact('autoplacedstudents', 'selected_academic_year', 'university'));
			$this->response->type('application/pdf');
			$this->layout = '/pdf/default';
			$this->render('print_autoplaced_pdf');
			return;
		}

		$colleges = classRegistry::init('College')->find('list');
		$departments = classRegistry::init('Department')->find('list');
		$programs = classRegistry::init('Program')->find('list');
		$programTypes = classRegistry::init('ProgramType')->find('list');
		$fieldSetups = 'result_type,percent,edit';

		//$allUnits = classRegistry::init('Department')->allUnits();
		if ($this->role_id == ROLE_COLLEGE) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
		} elseif ($this->role_id == ROLE_DEPARTMENT) {
			$allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
			$currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
			$currentUnits = $allUnits;
		}

		$this->set(compact('colleges', 'types', 'allUnits', 'departments', 'colleges', 'fieldSetups', 'programs', 'programTypes'));
		$this->render('auto_report');
	}

	public function get_preference_applied_student()
	{
		$this->layout = 'ajax';
		$students = classRegistry::init('PlacementPreference')->getStudentWhoToPrepareForPlacement($this->request->data);
		//getStudentWhoTookEntranceExam

		$error = classRegistry::init('PlacementPreference')->invalidFields();

		if (isset($this->request->data['PlacementSetting']) && !empty($this->request->data['PlacementSetting']['applied_for']) && isset($error['NO_PLACEMENT_SETTING_FOUND'][0])) {
			$this->layout = 'ajax';
			return $this->redirect(array('controller' => 'placementSettings', 'action' => 'placement_result_setting'));
		}

		$this->__init_search();

		$this->set(compact('students', 'error'));
	}

	public function get_placement_statistics_summary()
	{
		$this->layout = 'ajax';
		$placementSummary = array();

		if (isset($this->request->data['PlacementSetting']['applied_for']) && !empty($this->request->data['PlacementSetting']['applied_for'])) {
			$placementSummary = classRegistry::init('PlacementPreference')->getPlacementCriteriaSummary($this->request->data);
			//debug($placementSummary);
		}
		$this->set(compact('placementSummary'));
	}

	//Move Student To Original College for Placement
	public function move_student_to_originalcollege_for_placement()
	{
		if (!empty($this->request->data) && !empty($this->request->data['moveSelectedStudent'])) {

			$selectedSections = array();
			$done = 0;

			$targetCollegeDetail = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $this->request->data['PlacementSetting']['target_college_id']), 'recursive' => -1));
			$sourceCollegeDetail = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $this->request->data['PlacementSetting']['college_id']), 'recursive' => -1));


			foreach ($this->request->data['PlacementSetting']['selected_section'] as $k => $secId) {
				if ($secId) {
					//$selectedSections[$secId]=$secId;
					$secDetail = ClassRegistry::init('Section')->find('first', array('conditions' => array('Section.id' => $secId), 'contain' => array('YearLevel')));
					$studentListsInTheSection = ClassRegistry::init('StudentsSection')->find('list', array('conditions' => array('StudentsSection.section_id' => $secId, 'StudentsSection.archive' => 0), 'fields' => array('student_id', 'student_id')));
					$acceptedStudentsList = ClassRegistry::init('Student')->find('list', array('conditions' => array('Student.id' => $studentListsInTheSection), 'fields' => array('accepted_student_id', 'accepted_student_id')));

					//update admitted student, and accepted student
					if (isset($studentListsInTheSection) && !empty($studentListsInTheSection)) {
						// sourceDepartmentDetail
						if (ClassRegistry::init('Student')->updateAll(array('Student.college_id' => $targetCollegeDetail['College']['id']), array('Student.id' => $studentListsInTheSection))) {
							//targetDepartmentDetail sourceDepartmentDetail
							if (ClassRegistry::init('AcceptedStudent')->updateAll(array('AcceptedStudent.college_id' => $targetCollegeDetail['College']['id']), array('AcceptedStudent.id' => $acceptedStudentsList))) {
								$done++;
							}
						}
					}
				}
			}

			if ($done) {
				$this->Flash->success('The selected section students has successfully moved from ' . $sourceCollegeDetail['College']['name'] . ' college to ' . $targetCollegeDetail['College']['name'] . ' college.');
			} else {
				$this->Flash->error('No section is selected to move the students to the original college. ');
			}
		}

		if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudentsection'])) {
			// do validation
			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['PlacementSetting']['academicyear']):
					$this->Flash->error('Please select the academic year of the batch admitted.');
					break;
				case empty($this->request->data['PlacementSetting']['college_id']):
					$this->Flash->error('Please select the current hosted college you want to move. ');
					break;
				case empty($this->request->data['PlacementSetting']['target_college_id']):
					$this->Flash->error('Please select the original college where the students was accepted. ');
					break;
				case empty($this->request->data['PlacementSetting']['program_id']):
					$this->Flash->error('Please select the program you want to  transfer. ');
					break;
				case empty($this->request->data['PlacementSetting']['program_type_id']):
					$this->Flash->error('Please select the program type you want to transfer. ');
					break;
				case $this->request->data['PlacementSetting']['college_id'] == $this->request->data['PlacementSetting']['target_college_id']:
					$this->Flash->error('You have selected the same college for moving, please select a different original college. ');
					break;
				default:
					$availableCampusForCollege = ClassRegistry::init('Campus')->find('list', array('conditions' => array('Campus.available_for_college' => $this->request->data['PlacementSetting']['target_college_id']), 'fields' => array('Campus.id', 'Campus.id')));
					$availableCollegeInCampus = ClassRegistry::init('College')->find('list', array('conditions' => array('College.campus_id' => $availableCampusForCollege), 'fields' => array('College.id', 'College.id')));

					if (in_array($this->request->data['PlacementSetting']['college_id'], $availableCollegeInCampus)) {
						$everythingfine = true;
					} else {
						$this->Flash->error('The selected current college does not host the original college the student accepted.');
						break;
					}
			}

			if ($everythingfine) {

				$acceptedStudent = ClassRegistry::init('AcceptedStudent')->find('list', array(
					'conditions' => array(
						'AcceptedStudent.college_id' => $this->request->data['PlacementSetting']['college_id'],
						'AcceptedStudent.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
						'AcceptedStudent.program_id' => $this->request->data['PlacementSetting']['program_id'],
						'AcceptedStudent.department_id is null',
						'AcceptedStudent.academicyear' => $this->request->data['PlacementSetting']['academicyear']
					),
					'recursive' => -1,
					'field' => array('id', 'id')
				));

				$admittedStudent = ClassRegistry::init('Student')->find('list', array(
					'conditions' => array(
						'Student.accepted_student_id' => $acceptedStudent,
						'Student.department_id is null',
					),
					'recursive' => -1
				));

				if (isset($acceptedStudent) && !empty($acceptedStudent)) {

					$sectionLists = ClassRegistry::init('Section')->find('all', array(
						'conditions' => array(
							'Section.college_id' => $this->request->data['PlacementSetting']['college_id'],
							'OR' => array(
								'Section.year_level_id is null',
								'Section.year_level_id = 0',
								'Section.year_level_id = ""'
							),
							'Section.department_id is null',
							'Section.program_id' => $this->request->data['PlacementSetting']['program_id'],
							'Section.academicyear' => $this->request->data['PlacementSetting']['academicyear'],
							'Section.program_type_id' => $this->request->data['PlacementSetting']['program_type_id'],
							'Section.id in (select section_id from students_sections where archive = 0 and student_id in (' . implode(',', $admittedStudent) . '))'
						),
						'contain' => array(
							'YearLevel',
							'Student' => array(
								'fields' => array('Student.id'),
								'StudentsSection'
							)
						),
						'order' => array('Section.academicyear asc')
					));

					if (!empty($sectionLists)) {
						foreach ($sectionLists as &$pk) {
							foreach ($pk['Student'] as $sk => &$pss) {
								if ($pss['StudentsSection']['archive']) {
									unset($pk['Student'][$sk]);
								}
							}
						}
					}
					$this->set(compact('sectionLists'));

				} else {
					$this->Flash->error('No students in the selected college which needs moving too their orginal college. ');
				}
			}
		}

		$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 2, date('Y') - 1);
		//exclude those college from current selection if they were participated earlier in the placement

		$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.available_for_placement' => 1)));
		$originalcolleges = ClassRegistry::init('College')->find('list'/* , array('conditions' => array('College.id' => array(2, 6, 11, 1))) */);

		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');

		$this->set(compact('colleges', 'originalcolleges', 'departments', 'programs', 'programTypes', 'acyear_list'));
	}

	public function placement_identifier()
	{
		$this->autoRender = false;
		$this->layout = false;
		//	$this->layout = 'ajax';
		if (isset($this->request->data['PlacementResultSetting'][1]['applied_for']) && !empty($this->request->data['PlacementResultSetting'][1]['applied_for'])) {
			$placementSetting = classRegistry::init('PlacementResultSetting')->find('first', array(
				'conditions' => array(
					'PlacementResultSetting.applied_for' => $this->request->data['PlacementResultSetting'][1]['applied_for'],
					'PlacementResultSetting.round' => $this->request->data['PlacementResultSetting'][1]['round'],
					'PlacementResultSetting.academic_year' => $this->request->data['PlacementResultSetting'][1]['academic_year'],
					'PlacementResultSetting.program_id' => $this->request->data['PlacementResultSetting'][1]['program_id'],
					'PlacementResultSetting.program_type_id' => $this->request->data['PlacementResultSetting'][1]['program_type_id'],
				),
				'recursive' => -1
			));

			$groupIdentifier = $placementSetting['PlacementResultSetting']['group_identifier'];

			echo json_encode($groupIdentifier);
			exit();
		}
	}
}
