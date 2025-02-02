<?php
class AcademicCalendarsController extends AppController
{

	public $name = 'AcademicCalendars';
	public $helpers = array('DatePicker');

	public $menuOptions = array(
		'parent' => 'dashboard',
		'exclude' => array(
			'autoSaveExtension',
			'get_departments_that_have_the_selected_program'
		),
		'alias' => array(
			'index' => 'View All Academic Calendars',
			'add' => 'Set Academic Calendar',
			'extending_calendar' => 'Extend Academic Calendar',
		)
	);

	public $paginate = array();
	public $components = array('EthiopicDateTime', 'Paginator', 'AcademicYear');

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->Auth->allow(
			'autoSaveExtension', 
			'extending_calendar',
			'get_departments_that_have_the_selected_program'
		);
	}

	public function beforeRender()
	{

		//$acyear_array_data = $this->AcademicYear->acyear_array();
		$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 10, date('Y'));
		$defaultacademicyear = $this->AcademicYear->current_academicyear();

		if (!empty($acyear_array_data)) {
			foreach ($acyear_array_data as $k => $v) {
				if ($v == $defaultacademicyear) {
					$defaultacademicyear = $k;
					break;
				}
			}
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
		unset($this->request->data['User']['password']);
	}

	public function index()
	{
		$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();

		$this->__init_search_calendar();

		$this->AcademicCalendar->recursive = 0;
		
		$this->paginate = array(
			'fields' => array(
				'id', 
				'academic_year', 
				'semester', 
				'full_year',
				'department_id', 
				'year_level_id',
				'course_registration_start_date',
				'course_registration_end_date',
				'course_add_start_date',
				'course_add_end_date',
				'course_drop_start_date',
				'course_drop_end_date',
				'grade_submission_start_date',
				'grade_submission_end_date',
				'grade_fx_submission_end_date',
				'senate_meeting_date',
				'graduation_date',
				'created'
			),
			'contain' => array(
				'ExtendingAcademicCalendar' => array(
					'Department', 
					'Program', 
					'ProgramType'
				), 
				'Program' => array('fields' => array('id', 'name')), 
				'ProgramType' => array('id', 'name')
			),
			'order' => array(
				'AcademicCalendar.created' => 'DESC',
				//'AcademicCalendar.full_year' => 'DESC',
				'AcademicCalendar.academic_year' => 'DESC',
				'AcademicCalendar.semester' => 'DESC',
				'AcademicCalendar.program_id' => 'ASC',
				'AcademicCalendar.program_type_id' => 'ASC'
			),
			'limit' => 50
		);

		$options = array();

		if (!empty($this->request->data) /* && isset($this->request->data['viewAcademicCalendar']) */) {

			if (isset($this->request->data['viewAcademicCalendar'])) {
				$this->__init_clear_session_filters();
				$this->__init_search_calendar();
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options[] = array('AcademicCalendar.program_id' => $this->request->data['Search']['program_id']);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options[] = array('AcademicCalendar.program_type_id' => $this->request->data['Search']['program_type_id']);
			}

			if (!empty($this->request->data['Search']['department_id'])) {
				$options[] = array('AcademicCalendar.department_id like ' => '%s:_:"' . $this->request->data['Search']['department_id'] . '"%');
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$options[] = array('AcademicCalendar.academic_year' => $this->request->data['Search']['academic_year']);
			}

			if (!empty($this->request->data['Search']['semester'])) {
				$options[] = array('AcademicCalendar.semester' => $this->request->data['Search']['semester']);
			}

			if (!empty($this->request->data['Search']['year_level_id'])) {
				$options[] = array('AcademicCalendar.year_level_id like ' => '%s:_:"' . $this->request->data['Search']['year_level_id'] . '"%');
			}

			$this->paginate['conditions'] = $options;
			$this->Paginator->settings = $this->paginate;

			$academicCalendars = $this->Paginator->paginate('AcademicCalendar');

			if (empty($academicCalendars)) {
				$this->Flash->info('There is no academic calendar defined in the system in the given criteria.');
			}

		} else {

			$options[] = array('AcademicCalendar.academic_year' => $current_acy_and_semester['academic_year']);

			if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
				$options[] = array('AcademicCalendar.semester' => $current_acy_and_semester['semester']);
			}

			$this->paginate['conditions'] = $options;
			$this->Paginator->settings = $this->paginate;
			$academicCalendars = $this->Paginator->paginate('AcademicCalendar');

			if (empty($academicCalendars)) {
				$options = array();
				$options[] = array('AcademicCalendar.academic_year' => $current_acy_and_semester['academic_year']);
				
				$this->paginate['conditions'] = $options;
				$this->Paginator->settings = $this->paginate;
				$academicCalendars = $this->Paginator->paginate('AcademicCalendar');
			} 

			if (empty($academicCalendars)) {
				$options = array();

				$academic_years = $this->AcademicYear->academicYearInArray(date('Y') - 1 , date('Y'));
				$options[] = array('AcademicCalendar.academic_year' => $academic_years);
				
				$this->paginate['conditions'] = $options;
				$this->Paginator->settings = $this->paginate;
				$academicCalendars = $this->Paginator->paginate('AcademicCalendar');
			} 
		}


		if (!empty($academicCalendars)) {
			foreach ($academicCalendars as $ack => &$ackv) {
				$department_ids = unserialize($ackv['AcademicCalendar']['department_id']);
				//debug($department_ids);

				$year_level_ids = unserialize($ackv['AcademicCalendar']['year_level_id']);
				$found = false;
				$college_ids = array();

				if (!empty($department_ids) && !empty($year_level_ids)) {
					foreach ($year_level_ids as $ykey => $ylvl) {
						if (strpos($ylvl, '1st') !== false) {
							foreach ($department_ids as $dkey => $deppt) {
								if (strpos($deppt, 'pre_') !== false) {
									$collID = explode('pre_', $deppt);
									if (is_numeric($collID[1])) {
										$college_ids[] = $collID[1];
									}
								}
							}
						}
					}

					if (!empty($college_ids)) {
						debug($college_ids);
						$ackv['AcademicCalendar']['college_name'] = implode(", ", $this->AcademicCalendar->College->find('list', array('conditions' => array('College.id' => $college_ids))));
					}

					$ackv['AcademicCalendar']['department_name'] = implode(", ", $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $department_ids))));
					$ackv['AcademicCalendar']['year_name'] = implode("\n", $year_level_ids);
				}
			}
		}

		//$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.active' => 1)));
		//$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.active' => 1)));
		$departments = array();
		$colleges =  array();

		$programs =  $this->AcademicCalendar->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->AcademicCalendar->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$yearLevels = $this->year_levels;


		if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
				$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1)));
				$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.id' => 1, $this->college_id, 'College.active' => 1)));
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
				$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.id' => 1, $this->college_id, 'College.active' => 1)));
			}
		} else {
			if (!empty($this->college_ids)) {
				$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.id' => 1, $this->college_ids, 'College.active' => 1)));
				$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
			} else if (!empty($this->department_ids)) {
				$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			}
		}

		$this->set(compact('departments', 'colleges', 'programs', 'programTypes', 'yearLevels'));
		$this->set('academicCalendars', $academicCalendars);
	}

	public function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid academic calendar ID'));
			return $this->redirect(array('action' => 'index'));
		}

		$academicCalendar = $this->AcademicCalendar->read(null, $id);

		$academicCalendar['AcademicCalendar']['college_id'] = unserialize($academicCalendar['AcademicCalendar']['college_id']);
		$academicCalendar['AcademicCalendar']['department_id'] = unserialize($academicCalendar['AcademicCalendar']['department_id']);
		$academicCalendar['AcademicCalendar']['year_level_id'] = unserialize($academicCalendar['AcademicCalendar']['year_level_id']);
		$clgIds = array();

		if (!empty($academicCalendar['AcademicCalendar'])) {
			foreach ($academicCalendar['AcademicCalendar']['department_id'] as $k => $v) {
				if (strpos($v, 'pre_') !== false) {
					$tmp = explode('pre_', $v);
					$clgIds[$tmp[1]] = $tmp[1];
				}
			}
		}

		debug($clgIds);

		$academicCollegeIds = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $academicCalendar['AcademicCalendar']['department_id'], 'Department.active' => 1), 'fields' => 'college_id'));
		$academic_calandedr_college_ids = array_merge($clgIds, $academicCollegeIds);
		$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.id' => $academic_calandedr_college_ids, 'College.active' => 1)));

		$college_department = array();

		debug($academicCalendar);
		debug($colleges);


		if (!empty($colleges)) {
			foreach ($colleges as $college_id => $college_name) {
				$departments = $this->AcademicCalendar->Department->find('list', array(
					'fields' => array('id', 'name'),
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.active' => 1
					),
					'order' => 'Department.name'
				));

				if (!empty($departments)) {
					foreach ($departments as $department_id => $departmentname) {
						if (in_array($department_id, $academicCalendar['AcademicCalendar']['department_id'])) {
							$college_department[$college_id][$department_id] =  $departmentname;
						}
					}
				}
			}
		}

		$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.active' => 1)));

		$yearLevels = $this->AcademicCalendar->YearLevel->find('list');

		$this->set('academicCalendar', $academicCalendar);

		$this->set(compact(
			'colleges', 
			'departments', 
			'yearLevels', 
			'college_department'
		));
	}

	public function add()
	{
		if (!empty($this->request->data)) {
			$this->AcademicCalendar->create();
			if (!empty($this->request->data['AcademicCalendar']['academic_year']) && !empty($this->request->data['AcademicCalendar']['semester'])) {
				if (!empty($this->request->data['AcademicCalendar']['year_level_id']) && !empty($this->request->data['AcademicCalendar']['department_id'])) {
					debug($this->request->data);
					if ($this->AcademicCalendar->check_duplicate_entry($this->request->data)) {

						$departments_id = serialize($this->request->data['AcademicCalendar']['department_id']);
						$year_level_id = serialize($this->request->data['AcademicCalendar']['year_level_id']);
						
						$this->request->data['AcademicCalendar']['department_id'] = $departments_id;
						$this->request->data['AcademicCalendar']['year_level_id'] = $year_level_id;
						
						debug($this->request->data);

						if ($this->AcademicCalendar->save($this->request->data)) {
							$this->Flash->success('The Academic Calendar has been saved.');
							$this->redirect(array('action' => 'index'));
						} else {
							$this->Flash->error('The Academic Calendar could not be saved. Please, try again.');
							$this->request->data['AcademicCalendar']['department_id'] = unserialize($departments_id);
							$this->request->data['AcademicCalendar']['year_level_id'] = unserialize($year_level_id);
						}
					} else {
						$error = $this->AcademicCalendar->invalidFields();
						if (isset($error['duplicate'])) {
							$this->Flash->error($error['duplicate'][0] . ' Those unchecked red marked department has an academic calendar for the given criteria .');
						}
						$this->set('alreadyexisteddepartment', $error['departmentduplicate']);
						$this->set('alreadyexistedyearlevel', $error['yearlevelduplicate']);
					}
				} else {
					if (empty($this->request->data['AcademicCalendar']['year_level_id'])) {
						$this->Flash->error('Please select the year level you want to set academic calendar.');
					} else if (empty($this->request->data['AcademicCalendar']['deparment_id']) && empty($this->request->data['AcademicCalendar']['deparment_id'])) {
						$this->Flash->error('Please select the department you want to set academic calendar.');
					} else {
						$this->Flash->error('Please select year level and department you want to set academic calendar.');
					}
				}
			} else {
				$this->Flash->error('Please provide academic year and semester.');
			}
		}

		/* $colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.active' => 1)));
		$college_department = array();

		if (!empty($colleges)) {
			foreach ($colleges as $college_id => $college_name) {

				$departments = $this->AcademicCalendar->Department->find('list', array(
					'fields' => array('id', 'name'), 
					'conditions' => array(
						'Department.college_id' => $college_id, 
						'Department.active' => 1
					), 
					'order' => 'Department.name'
				));

				if (!empty($departments)) {
					foreach ($departments as $department_id => $departmentname) {
						$college_department[$college_id][$department_id] =  $departmentname;
					}
				}

				$college_department[$college_id]['pre_' . $college_id] = 'Pre/Freshman';
			}
		} */

		if (!empty($this->year_levels)) {
			$yearLevels = $this->year_levels;
		} else if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
			$yearLevels = $this->AcademicCalendar->YearLevel->distinct_year_level();
		}

		//$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.active' => 1)));
		$programs = $this->AcademicCalendar->Program->find('list', array('conditions' => array('Program.active' => 1)));
		$programTypes = $this->AcademicCalendar->ProgramType->find('list', array('conditions' => array('ProgramType.active' => 1)));

		if (isset($programTypes[PROGRAM_TYPE_PART_TIME])) {
			unset($programTypes[PROGRAM_TYPE_PART_TIME]);
		}

		if (isset($programTypes[PROGRAM_TYPE_ADVANCE_STANDING])) {
			unset($programTypes[PROGRAM_TYPE_ADVANCE_STANDING]);
		}

		$this->set(compact(
			//'colleges', 
			//'departments', 
			'programs', 
			'programTypes', 
			'yearLevels', 
			'college_department', 
			'departments_ids'
		));
	}

	public function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid Academic Calendar.');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			debug($this->request->data);

			if (!empty($this->request->data['AcademicCalendar']['year_level_id']) && !empty($this->request->data['AcademicCalendar']['department_id'])) {
				
				$departments_id = serialize($this->request->data['AcademicCalendar']['department_id']);
				$year_level_id = serialize($this->request->data['AcademicCalendar']['year_level_id']);
				
				$this->request->data['AcademicCalendar']['department_id'] = $departments_id;
				$this->request->data['AcademicCalendar']['year_level_id'] = $year_level_id;
				
				if ($this->AcademicCalendar->check_duplicate_entry($this->request->data)) {
					if ($this->AcademicCalendar->save($this->request->data)) {
						$this->Flash->success('The Academic Calendar has been updated.');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Flash->error('The Academic Calendar could not be saved. Please, try again.');
					}
				} else {
					$error = $this->AcademicCalendar->invalidFields();
					if (isset($error['duplicate'])) {
						$this->Flash->error($error['duplicate'][0] . ' Those unchecked red marked department has an academic calendar for the given criteria .');
					}
					$this->set('alreadyexisteddepartment', $error['departmentduplicate']);
					$this->set('alreadyexistedyearlevel', $error['yearlevelduplicate']);
				}
			} else {
				if (empty($this->request->data['AcademicCalendar']['year_level_id'])) {
					$this->Flash->error('Please select the year level you want to set academic calendar.');
				} else if (empty($this->request->data['AcademicCalendar']['deparment_id']) && empty($this->request->data['AcademicCalendar']['deparment_id'])) {
					$this->Flash->error('Please select the department you want to set academic calendar.');
				} else {
					$this->Flash->error('Please select year level and  department you want to set academic calendar.');
				}
			}

			$departments_id = unserialize($this->request->data['AcademicCalendar']['department_id']);
			//$college_id = unserialize($this->request->data['AcademicCalendar']['college_id']);
			$year_level_id = unserialize($this->request->data['AcademicCalendar']['year_level_id']);
			$this->request->data['AcademicCalendar']['department_id'] = $departments_id;
			$this->request->data['AcademicCalendar']['year_level_id'] = $year_level_id;
		}


		if (empty($this->request->data)) {

			$this->request->data = $this->AcademicCalendar->read(null, $id);
			$this->request->data['AcademicCalendar']['college_id'] = unserialize($this->request->data['AcademicCalendar']['college_id']);
			$this->request->data['AcademicCalendar']['department_id'] = unserialize($this->request->data['AcademicCalendar']['department_id']);
			$this->request->data['AcademicCalendar']['year_level_id'] = unserialize($this->request->data['AcademicCalendar']['year_level_id']);
			
			debug($this->request->data);

			$departments_ids = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $this->request->data['AcademicCalendar']['department_id'], 'Department.active' => 1)));
			//debug($departments_ids);

			$departments_list = $this->AcademicCalendar->Department->find('all', array(
				'conditions' => array(
					'Department.id' => $this->request->data['AcademicCalendar']['department_id'],
					'Department.active' => 1
				),
				'contain' => array('College' => array('id', 'name'))
			));

			if (empty($departments_list)) {
				$deptIds = array();

				if (!empty($this->request->data['AcademicCalendar'])) {
					foreach ($this->request->data['AcademicCalendar']['department_id'] as $k => $v) {
						$tmpDpId = explode('pre_', $v);
						debug($tmpDpId);
						$deptIds[] = $tmpDpId[1];
					}
				}

				$departments_list = $this->AcademicCalendar->Department->find('all', array(
					'conditions' => array(
						'Department.college_id' => $deptIds,
						'Department.active' => 1
					),
					'contain' => array('College' => array('id', 'name'))
				));
			}
		} else {
			$departments_ids = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.id' => $this->request->data['AcademicCalendar']['department_id'], 'Department.active' => 1)));
			$departments_list = $this->AcademicCalendar->Department->find('all', array(
				'conditions' => array(
					'Department.id' => $this->request->data['AcademicCalendar']['department_id'],
					'Department.active' => 1
				),
				'contain' => array('College' => array('id', 'name'))
			));
		}

		/* if ($this->role_id == ROLE_REGISTRAR) {
			$yearLevels = $this->AcademicCalendar->YearLevel->distinct_year_level();
		} */

		if (!empty($this->year_levels)) {
			$yearLevels = $this->year_levels;
		} else if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
			$yearLevels = $this->AcademicCalendar->YearLevel->distinct_year_level();
		}

		$departments = array();

		if (!empty($departments_list)) {
			foreach ($departments_list as $w_key => $dept) {
				$departments[$dept['College']['name']][$dept['Department']['id']] = $dept['Department']['name'];
			}
		}
		
		if (!empty($departments)) {
			foreach ($departments as $college => &$dept_list) {
				$college_id = $this->AcademicCalendar->Department->field('college_id', array('Department.id' => array_keys($dept_list), 'Department.active' => 1));
				//$dept_list['pre_' . $college_id] = 'Pre/Freshman';
				if (isset($this->request->data['AcademicCalendar']['year_level_id']) && !empty($this->request->data['AcademicCalendar']['year_level_id']) && in_array('1st',$this->request->data['AcademicCalendar']['year_level_id']) && ($this->request->data['AcademicCalendar']['program_id'] == PROGRAM_UNDEGRADUATE || $this->request->data['AcademicCalendar']['program_id'] == PROGRAM_REMEDIAL) && $this->request->data['AcademicCalendar']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
					$dept_list['pre_' . $college_id] = 'Pre/Freshman';
				}
				
			}
		}

		//debug($departments);

		$programs = $this->AcademicCalendar->Program->find('list', array('conditions' => array('Program.active' => 1)));
		$programTypes = $this->AcademicCalendar->ProgramType->find('list', array('conditions' => array('ProgramType.active' => 1)));

		if (isset($programTypes[PROGRAM_TYPE_PART_TIME])) {
			unset($programTypes[PROGRAM_TYPE_PART_TIME]);
		}

		if (isset($programTypes[PROGRAM_TYPE_ADVANCE_STANDING])) {
			unset($programTypes[PROGRAM_TYPE_ADVANCE_STANDING]);
		}
		

		$this->set(compact(
			'colleges', 
			'departments', 
			'programs', 
			'programTypes', 
			'yearLevels', 
			'college_department', 
			'departments_ids'
		));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid id for academic calendar');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->AcademicCalendar->delete($id)) {
			$this->Flash->success('Academic calendar deleted');
			return $this->redirect(array('action' => 'index'));
		}

		$this->Flash->error('Academic calendar could not be deleted.');
		return $this->redirect(array('action' => 'index'));
	}

	public function extending_calendar()
	{
		debug($this->request->data);

		if (!empty($this->request->data) && isset($this->request->data['searchbutton'])) {
			
			$options = array();

			if (!empty($this->request->data['Search']['program_id'])) {
				$options[] = array('AcademicCalendar.program_id' => $this->request->data['Search']['program_id']);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options[] = array('AcademicCalendar.program_type_id' => $this->request->data['Search']['program_type_id']);
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$options[] = array('AcademicCalendar.academic_year' => $this->request->data['Search']['academic_year']);
			}

			if (!empty($this->request->data['Search']['semester'])) {
				$options[] = array('AcademicCalendar.semester' => $this->request->data['Search']['semester']);
			}

			$xacademicCalendars = $this->AcademicCalendar->find('all', array('conditions' => $options, 'contain' => array('Program', 'ProgramType')));
			$academicCalendars = array();

			if (!empty($xacademicCalendars)) {
				foreach ($xacademicCalendars as $acK => $acV) {

					$years = unserialize($acV['AcademicCalendar']['year_level_id']);
					$departments = unserialize($acV['AcademicCalendar']['department_id']);
					$list = '';

					if (!empty($departments)) {
						foreach ($departments as $deptk => $deptv) {
							$list .= ' ' . $this->AcademicCalendar->Department->field('Department.name', array('Department.id' => $deptv)) . ' ';
						}
					}

					$academicCalendars[$list][$acV['AcademicCalendar']['id']] = $acV['AcademicCalendar']['full_year'] . ' ' . $acV['Program']['name'] . ' ' . $acV['ProgramType']['name'];
				}
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['extend'])) {

			$saveAllExtention = array();
			$count = 0;

			if (!empty($this->request->data['ExtendingAcademicCalendar'])) {
				foreach ($this->request->data['ExtendingAcademicCalendar']['department_id'] as $dk => $dpv) {
					foreach ($this->request->data['ExtendingAcademicCalendar']['year_level_id'] as $yk => $ylv) {
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['academic_calendar_id'] = $this->request->data['ExtendingAcademicCalendar']['academic_calendar_id'];
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['department_id'] = $dpv;
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['year_level_id'] = $ylv;
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['program_id'] = $this->request->data['Search']['program_id'];
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['program_type_id'] = $this->request->data['Search']['program_id'];
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['activity_type'] = $this->request->data['ExtendingAcademicCalendar']['activity_type'];
						$saveAllExtention['ExtendingAcademicCalendar'][$count]['days'] = $this->request->data['ExtendingAcademicCalendar']['days'];
						$count++;
					}
				}
			}

			if (isset($saveAllExtention) && !empty($saveAllExtention)) {
				if ($this->AcademicCalendar->ExtendingAcademicCalendar->saveAll($saveAllExtention['ExtendingAcademicCalendar'], array('validate' => 'first'))) {
					$this->Flash->success('The Academic Calendar Extension has been updated.');
					$this->redirect(array('action' => 'index'));
				} else {

					$this->Flash->error('The Academic Calendar Extension could not be saved. Please, try again.');
					$options = array();

					if (!empty($this->request->data['Search']['program_id'])) {
						$options[] = array('AcademicCalendar.program_id' => $this->request->data['Search']['program_id']);
					}

					if (!empty($this->request->data['Search']['program_type_id'])) {
						$options[] = array('AcademicCalendar.program_type_id' => $this->request->data['Search']['program_type_id']);
					}

					if (!empty($this->request->data['Search']['academic_year'])) {
						$options[] = array('AcademicCalendar.academic_year' => $this->request->data['Search']['academic_year']);
					}

					if (!empty($this->request->data['Search']['semester'])) {
						$options[] = array('AcademicCalendar.semester' => $this->request->data['Search']['semester']);
					}
					
					$xacademicCalendars = $this->AcademicCalendar->find('all', array('conditions' => $options, 'contain' => array('Program', 'ProgramType')));
					$academicCalendars = array();
					
					if (!empty($xacademicCalendars)) {
						foreach ($xacademicCalendars as $acK => $acV) {

							$years = unserialize($acV['AcademicCalendar']['year_level_id']);
							$departments = unserialize($acV['AcademicCalendar']['department_id']);
							$list = '';
							
							if (!empty($departments)) {
								foreach ($departments as $deptk => $deptv) {
									$list .= ' ' . $this->AcademicCalendar->Department->field('Department.name', array('Department.id' => $deptv)) . ' ';
								}
							}

							$academicCalendars[$list][$acV['AcademicCalendar']['id']] = $acV['AcademicCalendar']['full_year'] . ' ' . $acV['Program']['name'] . ' ' . $acV['ProgramType']['name'];
						}
					}
				}
			}
			debug($saveAllExtention);
		}

		if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
			$yearLevels = $this->AcademicCalendar->YearLevel->distinct_year_level();
		}

		$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.active' => 1)));
		$programs = $this->AcademicCalendar->Program->find('list');
		$programTypes = $this->AcademicCalendar->ProgramType->find('list');
		$activity_types['registration'] = 'Registration';
		$activity_types['add'] = 'Add';
		$activity_types['drop'] = 'Drop';
		$activity_types['grade_submission'] = 'Grade Submission';
		$activity_types['fx_grade_submission'] = 'Fx Grade Submission';
		$activity_types['graduation_date'] = 'Graduation Day';
		$activity_types['senate_meeting'] = 'University Senate Meeting';

		$this->set(compact('departments', 'programs', 'programTypes', 'yearLevels', 'activity_types'));
		$this->set('academicCalendars', $academicCalendars);
	}

	function autoSaveExtension()
	{
		$this->autoRender = false;
		if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
			$academicCalendars = array();
			$save_is_ok = true;
			if (isset($this->request->data['ExtendingAcademicCalendar']) && !empty($this->request->data['ExtendingAcademicCalendar'])) {
				foreach ($this->request->data['ExtendingAcademicCalendar'] as $ek => $ev) {
					$data['ExtendingAcademicCalendar'] = $ev;
					if (isset($data['ExtendingAcademicCalendar']) && !empty($data['ExtendingAcademicCalendar'])) {
						$this->AcademicCalendar->ExtendingAcademicCalendar->set($data['ExtendingAcademicCalendar']);
						if ($this->AcademicCalendar->ExtendingAcademicCalendar->save($data)) {
							debug($data);
						}
					}
				}
			}
		}
	}

	function get_departments_that_have_the_selected_program() 
	{
		$this->layout = 'ajax';

		$colleges = $this->AcademicCalendar->College->find('list', array('conditions' => array('College.active' => 1)));
		$departments = $this->AcademicCalendar->Department->find('list', array('conditions' => array('Department.active' => 1)));

		$college_department = array();

		debug($this->request->data);

		$pre_freshman_remedial_college_ids = Configure::read('preengineering_college_ids') + Configure::read('social_stream_college_ids') + Configure::read('natural_stream_college_ids');

		//debug($pre_freshman_remedial_college_ids);


		if (!empty($colleges) && isset($this->request->data['AcademicCalendar']['program_id']) && !empty($this->request->data['AcademicCalendar']['program_id'])) {

			$departments_that_have_selected_program_curriculum = ClassRegistry::init('Curriculum')->find('list', array(
				'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
				'conditions' => array(
					'Curriculum.program_id' => $this->request->data['AcademicCalendar']['program_id'],
					'Curriculum.active' => 1
				),
				'group' => 'Curriculum.department_id'
			));

			if (CHECK_STUDY_PROGRAMS_FOR_ACADEMIC_CALENDAR_DEFINITION) {
				if (!empty($this->request->data['AcademicCalendar']['program_id']) && isset($this->request->data['AcademicCalendar']['program_type_id']) && !empty($this->request->data['AcademicCalendar']['program_type_id']) && $this->request->data['AcademicCalendar']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
					
					$program_modalities = ClassRegistry::init('ProgramType')->find('list', array(
						'conditions' => array(
							'ProgramType.id' => $this->request->data['AcademicCalendar']['program_type_id']
						),
						'fields' => array('ProgramType.program_modality_id', 'ProgramType.program_modality_id'),
					));

					//debug($program_modalities);


					if (!empty($program_modalities)) {

						$departments_that_have_selected_program_curriculum2 = ClassRegistry::init('Curriculum')->find('list', array(
							'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
							'conditions' => array(
								'Curriculum.department_id' => $departments_that_have_selected_program_curriculum,
								'Curriculum.program_id' => $this->request->data['AcademicCalendar']['program_id'],
								'Curriculum.department_study_program_id IN (SELECT id FROM department_study_programs where department_id IN ('. join(', ', $departments_that_have_selected_program_curriculum).') and  program_modality_id IN ('. join(', ', $program_modalities) .'))',
								'Curriculum.active' => 1
							),
							'group' => 'Curriculum.department_id'
						));

						//debug($departments_that_have_selected_program_curriculum2);

						/* if (!empty($departments_that_have_selected_program_curriculum2)) {
							$departments_that_have_selected_program_curriculum = $departments_that_have_selected_program_curriculum2;
						}  */

						$departments_that_have_selected_program_curriculum = $departments_that_have_selected_program_curriculum2;
						
					}

				}
			}

			//debug($departments_that_have_selected_program_curriculum);

			foreach ($colleges as $college_id => $college_name) {

				$departments = $this->AcademicCalendar->Department->find('list', array(
					'fields' => array('id', 'name'), 
					'conditions' => array(
						'Department.college_id' => $college_id, 
						'Department.id' => $departments_that_have_selected_program_curriculum, 
						'Department.active' => 1
					), 
					'order' => 'Department.name'
				));

				if (!empty($departments)) {
					foreach ($departments as $department_id => $departmentname) {
						$college_department[$college_id][$department_id] =  $departmentname;
					}
				}

				if (($this->request->data['AcademicCalendar']['program_id'] == PROGRAM_UNDEGRADUATE || $this->request->data['AcademicCalendar']['program_id'] == PROGRAM_REMEDIAL) && $this->request->data['AcademicCalendar']['program_type_id'] == PROGRAM_TYPE_REGULAR && !empty($this->request->data['AcademicCalendar']['year_level_id']) && in_array('1st', $this->request->data['AcademicCalendar']['year_level_id'])) {
					if (!empty($pre_freshman_remedial_college_ids) && in_array($college_id, $pre_freshman_remedial_college_ids)) {
						$college_department[$college_id]['pre_' . $college_id] = 'Pre/Freshman';
					}
				}
			}
		}

		/* if (isset($this->request->data['AcademicCalendar']['academic_year']) && !empty($this->request->data['AcademicCalendar']['academic_year']) && isset($error[0])) {
			//$this->layout = 'ajax';
			return $this->redirect(array('controller' => 'academicCalendars', 'action' => 'add'));
		} */

		$error = null;
			
		$this->set(compact('college_department', 'colleges', 'error'));

	}

	function __init_search_define_calendar()
	{
		if (!empty($this->request->data['AcademicCalendar'])) {
			$this->Session->write('search_define_calendar', $this->request->data['AcademicCalendar']);
		} else if ($this->Session->check('search_define_calendar')) {
			$this->request->data['AcademicCalendar'] = $this->Session->read('search_define_calendar');
		}
	}

	function __init_search_calendar()
	{
		if (!empty($this->request->data['Search'])) {
			$this->Session->write('search_calendar', $this->request->data['Search']);
		} else if ($this->Session->check('search_calendar')) {
			$this->request->data['Search'] = $this->Session->read('search_calendar');
		}
	}

	function __init_clear_session_filters()
	{
		if ($this->Session->check('search_calendar')) {
			$this->Session->delete('search_calendar');
		}

		if ($this->Session->check('search_define_calendar')) {
			$this->Session->delete('search_define_calendar');
		}
	}
}
