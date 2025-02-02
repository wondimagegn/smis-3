<?php
App::import('Xml');
//App::import('Vendor', 'xmlrpc'); 
App::import('Vendor', 'xmlrpc');
class ClearancesController extends AppController
{
	var $name = 'Clearances';

	var $menuOptions = array(
		'exclude' => array('index'),
		'alias' => array(
			'index' => 'View Clearance/Withdraw',
			'approve_clearance' => 'Approve Clearance',
			'add' => 'Apply for Clearance/Withdraw',
		)
	);

	var $components = array('AcademicYear');
	var $helpers = array('DatePicker', 'Media.Media');

	function beforeRender()
	{
		$current_academicyear = $defaultacademicyear = $this->AcademicYear->current_academicyear();

		$acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_academicyear)[0]));

		$this->set(compact('acyear_array_data', 'defaultacademicyear'));

		if (isset($this->request->data['User']['password'])) {
			unset($this->request->data['User']['password']);
		}
		
	}

	function beforeFilter()
	{
		parent::beforeFilter();
	}

	function __init_search_index()
	{
		if (!empty($this->request->data['Search'])) {
			$this->Session->write('search_data_clearnace_index', $this->request->data['Search']);
		} else if ($this->Session->check('search_data_clearnace_index')) {
			$this->request->data['Search'] = $this->Session->read('search_data_clearnace_index');
		}
	}

	function __init_clear_session_filters()
	{
		if ($this->Session->check('search_data_clearnace_index')) {
			$this->Session->delete('search_data_clearnace_index');
		}
	}

	function index()
	{
		$limit = 100;
		$name = '';
		$selected_academic_year = '';
		$page = 1;
		$sort = 'clearnace.created';
		$direction = 'desc';

		$options = array();
		
		if (!empty($this->passedArgs)) {

			//debug($this->passedArgs);

			if (!empty($this->passedArgs['Search.limit'])) {
				$limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
			}

			if (isset($this->passedArgs['Search.name']) && !empty($this->passedArgs['Search.name'])) {
				$name = $this->request->data['Search']['name'] = str_replace('-', '/', $this->passedArgs['Search.name']);
			}

			if (!empty($this->passedArgs['Search.department_id'])) {
				$this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
			}

			if (!empty($this->passedArgs['Search.college_id'])) {
				$this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
			}

			if (!empty($this->passedArgs['Search.academic_year'])) {
				$this->request->data['Search']['academic_year'] = $selected_academic_year = str_replace('-', '/', $this->passedArgs['Search.academic_year']);
			}

			if (isset($this->passedArgs['Search.clear'])) {
				$this->request->data['Search']['clear'] = $this->passedArgs['Search.clear'];
			}

			if (isset($this->passedArgs['Search.program_id'])) {
				$this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
			}

			if (isset($this->passedArgs['Search.program_type_id'])) {
				$this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
			}


			////////////////////

			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$sort = $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$direction = $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
			}

			////////////////////

			$this->__init_search_index();
			//$this->request->data['search'] = true;
		}

		if (isset($data) && !empty($data['Search'])) {
			$this->request->data['Search'] = $data['Search'];
			$this->__init_search_index();
		}

		if (isset($this->request->data['viewClearance'])) {
			unset($this->passedArgs);
			$this->__init_clear_session_filters();
			$this->__init_search_index();
		}


		if (!empty($this->request->data) /* && isset($this->request->data['viewClearance']) */) {


			$options = array();

			if (!empty($this->request->data['Search']['department_id'])) {
				$options['conditions'] = array('Student.department_id' => $this->request->data['Search']['department_id']);
			} else if (!empty($this->request->data['Search']['college_id'])) {
				$options['conditions'] = array(
					"Student.college_id" => $this->request->data['Search']['college_id'],
					'Student.department_id is null'
				);
			} else {
				if (!empty($this->department_ids)) {
					if (empty($this->request->data['Search']['department_id'])) {
						$options['conditions'] = array('Student.department_id' => $this->department_ids);
					}
				} else if (!empty($this->college_ids)) {
					if (empty($this->request->data['Search']['college_id'])) {
						$options['conditions'] = array('Student.college_id' => $this->college_ids, 'Student.department_id is null');
					}
				}
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academic_year']);
				//$options['conditions'] = array("YEAR(Clearance.request_date) >=  " => $this->request->data['Search']['academic_year'] . '%' );
				$options['conditions'] = array(
					'OR' => array(
						"Clearance.request_date >= " => $clearance_start_date . '%',
						"Clearance.last_class_attended_date >= " => $clearance_start_date . '%'
					)
				);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions'] = array("Student.program_id" => $this->request->data['Search']['program_id']);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions'] = array("Student.program_type_id" => $this->request->data['Search']['program_type_id']);
			}

			if ($this->request->data['Search']['clear'] == 1 && $this->request->data['Search']['withdrawl'] == 0) {
				$options['conditions'] = array("Clearance.type" => 'clearance');
			}

			if ($this->request->data['Search']['withdrawl'] == 1 && $this->request->data['Search']['clear'] == 0) {
				$options['conditions'] = array("Clearance.type" => 'withdraw');
			}

			
		} else {

			if ($this->role_id == ROLE_STUDENT) {
				$options['conditions'] = array('Clearance.student_id' => $this->student_id);
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				$departments = array();
				if (!$this->onlyPre) {
					$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
				}
				$options['conditions'][] = array('Student.college_id' => $this->college_id);
				$this->request->data['Search']['college_id'] = $this->college_id;
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
				$options['conditions'][] = array('Student.department_id' => $this->department_id);
				$this->request->data['Search']['department_id'] = $this->department_id;
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				if (!empty($this->department_ids)) {
					$colleges = array();
					$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
					if (!empty($departments)) {
						$options['conditions'][] = array('Student.department_id' => $this->department_ids, 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
					}
				} else if (!empty($this->college_ids)) {
					$departments = array();
					$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));

					if (!empty($colleges)) {
						$options['conditions'][] = array('Student.college_id' => array_keys($colleges), 'Student.department_id IS NULL', 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
					}
				}

			} else {

				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($colleges) && !empty($departments)) {
					$options['conditions'][] = array(
						'OR' => array(
							'Student.department_id' => array_keys($departments),
							'Student.college_id' => array_keys($colleges)
						)
					);
				} else if (!empty($departments)) {
					$options['conditions'][] = array('Student.department_id' => array_keys($departments));
				} else if (!empty($colleges)) {
					$options['conditions'][] = array('Student.college_id' => array_keys($colleges));
				}
			}

			if (!empty($options['conditions'])) {
				$options['conditions'][] = array('Student.id IS NOT NULL');
			}

			if ($this->role_id != ROLE_STUDENT) {
				$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->AcademicYear->current_academicyear());
				$options['conditions'] = array(
					'OR' => array(
						"Clearance.request_date >= " => $clearance_start_date . '%',
						"Clearance.last_class_attended_date >= " => $clearance_start_date . '%'
					)
				);
			} 
		}


		$clearances = array();

		if (!empty($options['conditions'])) {
			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Student' => array(
						'fields' => array(
							'id', 
							'program_id',
							'program_type_id', 
							'full_name', 
							'gender',
							'studentnumber',
							'department_id',
							'academicyear',
							'graduated', 
						),
						'StudentExamStatus' => array(
							'order' => array('StudentExamStatus.academic_status_id' => 'DESC', 'StudentExamStatus.sgpa' => 'DESC', 'StudentExamStatus.cgpa' => 'DESC')
						), 
						'Department' => array('id', 'name'), 
						'Program' => array('id', 'name'), 
						'ProgramType' => array('id', 'name')
					)
				),
				'order' => array($sort => $direction),
				'limit' => (!empty($limit) ? $limit : 100),
				'maxLimit' => (!empty($limit) ? $limit : 100),
				'recursive'=> -1,
				'page' => $page
			);

			try {
				$clearances = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('clearances'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index'));
			}

		}


		if (empty($clearances)) {
			$this->Flash->info('There is no clearance request found in the given criteria.');
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		} else if ($this->role_id != ROLE_STUDENT) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		}

		//if ($this->role_id != ROLE_DEPARTMENT || $this->role_id != ROLE_STUDENT || $this->role_id != ROLE_COLLEGE) {
			if (!empty($departments) && count($departments) > 1) {
				$departments = array('' => 'All Departmments') + $departments;
			} else if (!empty($colleges) && count($colleges) > 1) {
				$colleges = array('' => 'All Colleges') + $colleges;
			}
		//}

		$this->__init_search_index();

		$programs =  $this->Clearance->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Clearance->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact('programs', 'departments', 'colleges', 'programTypes'));

		$this->set(compact('clearances'));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid clearance'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('clearance', $this->Clearance->read(null, $id));
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->Clearance->create();
			$this->request->data['Clearance']['student_id'] = $this->student_id;

			if ($this->Clearance->validateStudentClearance($this->student_id)) {
				//check if student has received dorm before the clearance date and returned ?
				$checkDorm = ClassRegistry::init('DormitoryAssignment')->takeNDormAndReturn($this->student_id, $this->request->data['Clearance']['request_date']);
				
				if ($checkDorm == 0) {
					if ($this->Clearance->checkDuplication($this->request->data)) {
						if (isset($this->request->data['Attachment']) && !empty($this->request->data['Attachment'])) {
							$this->request->data = $this->Clearance->preparedAttachment($this->request->data);
						}

						if ($this->Clearance->saveAll($this->request->data, array('validate' => 'first'))) {
							$this->Flash->success('Your clearance request has been sent to registrar for approval and you  can cancel your clearance request before the registrar confirms it.');
							$this->redirect(array('action' => 'index'));
						} else {
							$this->Flash->error('Your clearance request could not be saved. Please, try again.');
						}
					} else {
						$this->Flash->warning('You have already requested clearance for the selected year.');
					}
				} else {
					$this->Flash->error('You can not cleared before returning dormitory. Please return, and come back again.');
				}
			} else {
				$error = $this->Clearance->invalidFields();

				if (isset($error['not_returned_item'])) {
					$string = '';

					foreach ($error['not_returned_item'] as $index => $value) {
						$string .= ' From <ul> ';
						foreach ($value as $k => $v) {
							$string .= ' <li>' . $k . '<ul>';
							foreach ($v as $pk => $pv) {
								$string .= ' <li> ' . $pv . ' taken but not returned. </li>';
							}
							$string .= '</ul> </li>';
						}
						$string .= '</ul>';
					}
					$this->Session->setFlash('<span></span> &nbsp;&nbsp;' . __('You can not be cleared before returning taken properties from the university, please return the properties and come back for clearance request.' . $string . ''), 'default', array('class' => 'error-box error-message'));
				}
			}
		}

		$there_is_pending_approval = 0;

		$recentClearance = $this->Clearance->checkRecentPendingApproval($this->student_id);

		if (!empty($recentClearance)) {
			$there_is_pending_approval = 1;
			$this->request->data = $recentClearance;
		}

		$current_academic_year = $this->AcademicYear->current_academicyear();
		$student_section_exam_status = $this->Clearance->Student->get_student_section($this->student_id, $current_academic_year);
		//$students = $this->Clearance->Student->find('list');
		$this->set(compact('student_section_exam_status', 'there_is_pending_approval' ));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid clearance');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			if ($this->Clearance->save($this->request->data)) {
				$this->Flash->success('The clearance has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The clearance could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Clearance->read(null, $id);
		}

		//$students = $this->Clearance->Student->find('list');
		//$this->set(compact('students'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid id for clearance');
			return $this->redirect(array('action' => 'index'));
		}

		$is_deletion_allowed = $this->Clearance->find('count', array(
			'conditions' => array(
				'Clearance.id' => $id, 'Clearance.confirmed is null',
				'Clearance.student_id' => $this->student_id
			)
		));

		if ($is_deletion_allowed) {
			if ($this->Clearance->delete($id)) {
				$this->Flash->success('Your Clearance request is now cancelled.');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error('Your Clearance request is not cancelled since it was approved by the registrar.');
		}

		return $this->redirect(array('action' => 'index'));
	}

	function approve_clearance()
	{

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			if (!empty($this->request->data['Clearance'])) {
				$this->Clearance->create();
				
				$this->request->data['Clearance'] = array_values($this->request->data['Clearance']);

				foreach ($this->request->data['Clearance'] as $data => &$value) {
					if ($value['confirmed'] == '') {
						//debug($this->request->data['Clearance'][$data]);
						unset($this->request->data['Clearance'][$data]);
					} else {
						$value['acceptance_date'] = date('Y-m-d');
					}
				}

				debug($this->request->data['Clearance']);

				if (count($this->request->data['Clearance']) > 0){
					if ($this->Clearance->saveAll($this->request->data['Clearance'], array('validate' => 'first'))) {
						$this->Flash->success('The selected clearance applicant(s) request has been approved and applicant(s) will be notified.');
						$this->redirect(array('action' => 'index'));
						unset($this->request->data['Clearance']);
					} else {
						$this->Flash->error('The clearance could not be saved. Please, try again.');
					}
				} else {
					$this->Flash->error('You need to select at least one student clearance to save.');
				}
			}
		}

		$options = array();

		if (!empty($this->request->data) && isset($this->request->data['filterClearnce'])) {

			$this->__init_clear_session_filters();
			$this->__init_search_index();

			$options['conditions'][] = array("Clearance.confirmed is null");

			if (!empty($this->request->data['Search']['department_id'])) {
				$options['conditions'][] = array("Student.department_id" => $this->request->data['Search']['department_id']);
			} else {
				if (!empty($this->department_ids)) {
					$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				} else if (!empty($this->request->data['Search']['college_id'])) {
					$options['conditions'][] = array("Student.college_id" => $this->request->data['Search']['college_id']);
				} else if (!empty($this->college_ids)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids, 'Student.department_id is null');
				}
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				// $year = explode('/', $this->request->data['Search']['academic_year']);
				// $options['conditions'][] = array(" YEAR(Clearance.request_date) >= " => $year[0] . '%');

				$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academic_year']);

				$options['conditions'][] = array(
					'OR' => array(
						"Clearance.request_date >= " => $clearance_start_date,
						"Clearance.last_class_attended_date >= " => $clearance_start_date
					)
				);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions'][] = array("Student.program_id" => $this->request->data['Search']['program_id']);
			} else {
				$options['conditions'][] = array("Student.program_id" => $this->program_ids);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions'][] = array("Student.program_type_id" => $this->request->data['Search']['program_type_id']);
			} else {
				$options['conditions'][] = array("Student.program_type_id" => $this->program_type_ids);
			}

			if ($this->request->data['Search']['clear'] == 1) {
				$options['conditions'][] = array("Clearance.type" => 'clearance');
			}

			if ($this->request->data['Search']['withdrawl'] == 1) {
				$options['conditions'][] = array("Clearance.type" => 'withdraw');
			}

			$search = true;

			$this->set(compact('search'));

		} else {

			$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->AcademicYear->current_academicyear());

			$options['conditions'][] = array(
				"Clearance.confirmed is null",
				"Student.program_id" => $this->program_ids,
				"Student.program_type_id" => $this->program_type_ids,
				'OR' => array(
					"Clearance.request_date >= " => $clearance_start_date,
					"Clearance.last_class_attended_date >= " => $clearance_start_date
				)
			);

			if ($this->role_id == ROLE_DEPARTMENT) {
				$options['conditions'][] = array("Student.department_id" => $this->department_id);
			} else if ($this->role_id == ROLE_COLLEGE) {
				$options['conditions'][] = array("Student.college_id" => $this->college_id);
			} else {
				if (!empty($this->department_ids)) {
					$options['conditions'][] = array("Student.department_id" => $this->department_ids);
				} else if (!empty($this->college_ids)) {
					$options['conditions'][] = array("Student.department_id is null", "Student.college_id" => $this->college_ids);
				} else {
					$options = array();
				}
			}
			
		}

		$clearances = array();

		//debug($options);

		if (!empty($options['conditions'])) {
			$clearances = $this->Clearance->find('all', array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Attachment', 
					'Student' => array(
						'fields' => array(
							'id', 
							'program_id',
							'program_type_id', 
							'full_name', 
							'gender',
							'studentnumber',
							'department_id',
							'academicyear',
							'graduated', 
						),
						'StudentExamStatus' => array(
							'order' => array('StudentExamStatus.created DESC')
						), 
						'Department' => array('id', 'name'), 
						'Program' => array('id', 'name'), 
						'ProgramType' => array('id', 'name')
					)
				),
				// 'limit' => 1000,
				// 'maxLimit' => 1000,
				'recursive' => -1
			));
		}


		if (empty($clearances)) {
			$this->Flash->info('No Clearance/withdrawal request is found with the search given criteria.');
			//$this->redirect(array('action' => 'approve_clearance'));
		} else {
			$clearances = $this->Clearance->organizeListOfClearanceApplicant($clearances);
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		} else if ($this->role_id != ROLE_STUDENT) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		}

		if (!empty($departments) && count($departments) > 1) {
			$departments = array('' => 'All Departmments') + $departments;
		} else if (!empty($colleges) && count($colleges) > 1) {
			$colleges = array('' => 'All Colleges') + $colleges;
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$this->request->data['Search']['department_id'] = $this->department_id;
		} else if ($this->role_id == ROLE_COLLEGE) {
			$this->request->data['Search']['college_id'] = $this->college_id;
		}

		$programs =  $this->Clearance->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Clearance->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact('programs', 'departments', 'colleges', 'programTypes', 'clearances', 'filterByDate'));

	}

	function withdraw_management()
	{
		/* get the list of approved clearnce and allow the registrar to feed proper withdrawl
	    if the student withdraw is approved and accepted by the party show in the 
	    readmission application the decision of the withdrawal
	    */

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			
			$this->Clearance->create();

			if (!empty($this->request->data['Clearance'])) {
				foreach ($this->request->data['Clearance'] as $in => &$va) {
					if (isset($va['forced_withdrawal']) && $va['forced_withdrawal'] == '') {
						unset($this->request->data['Clearance'][$in]);
					}
				}
			}

			if (!empty($this->request->data['Clearance'])) {
				if ($this->Clearance->saveAll($this->request->data['Clearance'], array('validate' => 'first'))) {
					$this->Flash->success('The selected withdrawal applicants request has been approved.');

					// if the withdraw is final make the student sectionless 
					if (!empty($this->request->data['Clearance'])) {
						foreach ($this->request->data['Clearance'] as $seindex => $secvalue) {
							if ($secvalue['forced_withdrawal'] == 1) {
								$this->Clearance->Student->StudentsSection->id = $this->Clearance->Student->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id' => $secvalue['student_id'],  'StudentsSection.archive' => 0));
								if ($this->Clearance->Student->StudentsSection->saveField('archive', '1'));
							}
						}
					}
					//$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The withdrawal could not be saved. Please, try again.');
					$this->request->data['filterClearnce'] = true;
				}
			} else {
				$this->Flash->info('You have not selected withdrawal student for approval.');
				$this->request->data['filterClearnce'] = true;
			}
		}


		$options = array();

		if (!empty($this->request->data) && isset($this->request->data['filterClearnce'])) {

			$this->__init_clear_session_filters();
			$this->__init_search_index();

			//$options['conditions'][] = array("Clearance.confirmed is null");

			if (!empty($this->request->data['Search']['department_id'])) {
				$options['conditions'][] = array("Student.department_id" => $this->request->data['Search']['department_id']);
			} else {
				if (!empty($this->department_ids)) {
					$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				} else if (!empty($this->request->data['Search']['college_id'])) {
					$options['conditions'][] = array("Student.college_id" => $this->request->data['Search']['college_id']);
				} else if (!empty($this->college_ids)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids, 'Student.department_id is null');
				}
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				// $year = explode('/', $this->request->data['Search']['academic_year']);
				// $options['conditions'][] = array(" YEAR(Clearance.request_date) >= " => $year[0] . '%');

				$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academic_year']);

				$options['conditions'][] = array(
					'OR' => array(
						"Clearance.request_date >= " => $clearance_start_date,
						"Clearance.last_class_attended_date >= " => $clearance_start_date
					)
				);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions'][] = array("Student.program_id" => $this->request->data['Search']['program_id']);
			} else {
				$options['conditions'][] = array("Student.program_id" => $this->program_ids);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions'][] = array("Student.program_type_id" => $this->request->data['Search']['program_type_id']);
			} else {
				$options['conditions'][] = array("Student.program_type_id" => $this->program_type_ids);
			}

			/* if ($this->request->data['Search']['clear'] == 1) {
				$options['conditions'][] = array("Clearance.type" => 'clearance');
			}

			if ($this->request->data['Search']['withdrawl'] == 1) {
				$options['conditions'][] = array("Clearance.type" => 'withdraw');
			} */

			$search = true;

			$this->set(compact('search'));

		} else {

			$clearance_start_date =  $this->AcademicYear->get_academicYearBegainingDate($this->AcademicYear->current_academicyear());

			$options['conditions'][] = array(
				"Student.program_id" => $this->program_ids,
				"Student.program_type_id" => $this->program_type_ids,
				'OR' => array(
					"Clearance.request_date >= " => $clearance_start_date,
					"Clearance.last_class_attended_date >= " => $clearance_start_date
				)
			);

			if ($this->role_id == ROLE_DEPARTMENT) {
				$options['conditions'][] = array("Student.department_id" => $this->department_id);
			} else if ($this->role_id == ROLE_COLLEGE) {
				$options['conditions'][] = array("Student.college_id" => $this->college_id);
			} else {
				if (!empty($this->department_ids)) {
					$options['conditions'][] = array("Student.department_id" => $this->department_ids);
				} else if (!empty($this->college_ids)) {
					$options['conditions'][] = array("Student.department_id is null", "Student.college_id" => $this->college_ids);
				} else {
					$options = array();
				}
			}
		}

		$clearances = array();

		if (!empty($options['conditions'])) {

			$options['conditions'][] = array(
				'Clearance.confirmed' => 1,
				'Clearance.type' => 'withdraw',
				'Clearance.forced_withdrawal is null',
				'Student.graduated' => 0
			); 

			debug($options);

			$clearances = $this->Clearance->find('all', array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Attachment', 
					'Student' => array(
						'fields' => array(
							'id', 
							'program_id',
							'program_type_id', 
							'full_name', 
							'gender',
							'studentnumber',
							'department_id',
							'academicyear',
							'graduated', 
						),
						'StudentExamStatus' => array(
							'order' => array('StudentExamStatus.created DESC')
						), 
						'Department' => array('id', 'name'), 
						'Program' => array('id', 'name'), 
						'ProgramType' => array('id', 'name')
					)
				),
				// 'limit' => 1000,
				// 'maxLimit' => 1000,
				'recursive' => -1
			));
		}


		if (empty($clearances)) {
			$this->Flash->info('No withdrawal request is found in the given criteria.');
			//$this->redirect(array('action' => 'approve_clearance'));
		} else {
			$clearances = $this->Clearance->organizeListOfClearanceApplicant($clearances);
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		} else if ($this->role_id != ROLE_STUDENT) {
			if (!empty($this->department_ids)) {
				$departments = $this->Clearance->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Clearance->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		}

		if (!empty($departments) && count($departments) > 1) {
			$departments = array('' => 'All Departmments') + $departments;
		} else if (!empty($colleges) && count($colleges) > 1) {
			$colleges = array('' => 'All Colleges') + $colleges;
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$this->request->data['Search']['department_id'] = $this->department_id;
		} else if ($this->role_id == ROLE_COLLEGE) {
			$this->request->data['Search']['college_id'] = $this->college_id;
		}

		$programs =  $this->Clearance->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Clearance->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact('programs', 'departments', 'colleges', 'programTypes', 'clearances', 'filterByDate'));
	}
}
