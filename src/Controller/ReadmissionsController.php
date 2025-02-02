<?php
class ReadmissionsController extends AppController
{

	var $name = 'Readmissions';
	var $menuOptions = array(
		'exclude' => array('index', 'add'),
		'alias' => array(
			'ac_approve_readmission' => 'Approve Application',
			'process_readmission_application' => 'Process Readmission Applications',
			'index' => 'View Readmission Applications',
			'apply_readmission_for_student' => 'Apply For Readmission'
		)
	);

	var $helpers = array('Media.Media');
	var $components = array('AcademicYear');

	function beforeRender()
	{
		$defaultacademicyear = $this->AcademicYear->current_academicyear();
		$acyearNext = ClassRegistry::init('StudentExamStatus')->getNextSemster($this->AcademicYear->current_academicyear(), null);
		$acyear_array_data[$this->AcademicYear->current_academicyear()] = $this->AcademicYear->current_academicyear();
		$acyear_array_data[$acyearNext['academic_year']] = $acyearNext['academic_year'];
		$acyear_array_data['2022/23'] = '2022/23';
		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
	}

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'ajax_readmitted_year', 
			'readmission_data_entry',
			'index'
		);
	}

	function index()
	{
		$this->Readmission->recursive = 0;

		if (!empty($this->request->data) && isset($this->request->data['viewReadmission'])) {
			
			$options = array();

			if (!empty($this->deparment_ids)) {
				if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array('Student.department_id' => $this->department_ids);
				}
			} else if (!empty($this->college_ids)) {
				if (empty($this->request->data['Search']['college_id'])) {
					$options[] = array(
						'Student.college_id' => $this->college_ids,
						'Student.department_id is null'
					);
				}
			}

			if (!empty($this->request->data['Search']['department_id'])) {
				$options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
			}

			if (!empty($this->request->data['Search']['college_id'])) {
				$options[] = array("Student.college_id" => $this->request->data['Search']['college_id']);
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$options[] = array("Readmission.academic_year like " => $this->request->data['Search']['academic_year'] . '%');
			}

			if (!empty($this->request->data['Search']['semester'])) {
				$options[] = array("Readmission.semester" => $this->request->data['Search']['semester']);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options[] = array("Student.program_id" => $this->request->data['Search']['program_id']);
			}


			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options[] = array("Student.program_type_id" => $this->request->data['Search']['program_type_id']);
			}

			if ($this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array("Readmission.registrar_approval" => -1);
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array("Readmission.registrar_approval" => 1);
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1) {
				$options[] = array("Readmission.registrar_approval is null");
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array("Readmission.registrar_approval" => array(1, -1));
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1 ) {
				$options[] = array(
					'OR' => array(
						"Readmission.registrar_approval" => 1,
						"Readmission.registrar_approval is null"
					)
				);
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 1 ) {
				$options[] = array(
					'OR' => array(
						"Readmission.registrar_approval" => -1,
						"Readmission.registrar_approval is null"
					)
				);
			}

			if (!empty($options)) {
				$readmissions = $this->paginate($options);
			} else {
				$readmissions = array();
			}

			if (empty($readmissions) && !empty($this->request->data) && isset($this->request->data['viewReadmission'])) {
				$this->Flash->info('There is no readmission applicant(s) found in the given criteria.');
			}

		} else {

			if ($this->role_id == ROLE_STUDENT) {
				$conditions = array('Readmission.student_id' => $this->student_id);
				$readmissions = $this->paginate($conditions);
			} else if ($this->role_id == ROLE_COLLEGE) {
				$conditions = array('Student.college_id' => $this->college_id);
				$readmissions = $this->paginate($conditions);
			} else if ($this->role_id == ROLE_DEPARTMENT) {
				$conditions = array('Student.department_id' => $this->department_id);
				$readmissions = $this->paginate($conditions);
			} else {
				$conditions = null;
				if (!empty($this->deparment_ids)) {
					$conditions = array('Student.department_id' => $this->department_ids, 'Student.program_id' => $this->program_id, 'Student.program_type_id' => $this->program_type_id);
				} else if (!empty($this->college_ids)) {
					$conditions = array(
						'Student.college_id' => $this->college_ids,
						'Student.program_id' => $this->program_id, 
						'Student.program_type_id' => $this->program_type_id,
						'Student.department_id is null'
					);
				}
				$readmissions = $this->paginate($conditions);
			}
		}

		$programs = $this->Readmission->Student->Program->find('list');
		$programTypes = $this->Readmission->Student->ProgramType->find('list');

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		} else if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$colleges = $this->Readmission->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
			$programs = $this->Readmission->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$programTypes = $this->Readmission->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		}

		$this->set(compact('programs', 'departments', 'colleges', 'programTypes'));
		$this->set('readmissions', $readmissions);
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid readmission'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->set('readmission', $this->Readmission->read(null, $id));
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->Readmission->create();
			if ($this->Readmission->save($this->request->data)) {
				$this->Flash->success('The readmission application has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The readmission application could not be saved. Please, try again.');
			}
		}
		$students = $this->Readmission->Student->find('list');
		$this->set(compact('students'));
	}

	function apply()
	{

		/**
		 * 0 not cleared       					// redirect to clearance page 
		 * 1 cleared                         
		 * 2 cleared but not have status         // allow readmission application on hold state 
		 * 3 cleared and have status but not achieved readmission point // not elegible  
		 * 4 cleared and have status but achieved readmission point   // elegible 
		 * 5 withdraw not completed  
		 * 6 withdraw properly 
		 */
		
		//TODO: withdraw checks internal if s/he is cleared, and proper withdrawl if s/he cleared
	
		$elegible = $this->Readmission->Student->Clearance->elegibleForReadmission($this->student_id, $this->AcademicYear->current_academicyear());

		if ($elegible == 0 || $elegible == 5) {
			$error = $this->Readmission->Student->Clearance->invalidFields();
			if (isset($error['error'])) {
				$this->Flash->info($error['error'][0]);
			}
			//redirect to clearnce/withdraw page 
			$this->redirect(array('controller' => 'clearances', 'action' => 'add'));
		} else if ($elegible == 3) {
			// not eligible for reaadmission application 
			$error = $this->Readmission->Student->Clearance->invalidFields();

			if (isset($error['error'])) {
				$this->Flash->info($error['error'][0]);
			}

			$this->redirect(array('action' => 'index'));
		} else {

			if (!empty($this->request->data)) {
				$this->Readmission->create();
				//check the user is already applied for the given year
				$checkDuplication = $this->Readmission->find('count', array(
					'conditions' => array(
						'Readmission.student_id' => $this->request->data['Readmission']['student_id'],
						'Readmission.academic_year' => $this->request->data['Readmission']['academic_year'],
						'Readmission.semester' => $this->request->data['Readmission']['semester']
					)
				));

				if ($checkDuplication == 0) {
					if ($this->Readmission->save($this->request->data)) {
						$this->Flash->success('Your readmission application has been saved.');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Flash->error('Your readmission application could not be saved. Please, try again.');
					}
				} else {
					$this->Flash->error('You have already requested readmission application for ' . $this->request->data['Readmission']['academic_year'] . ' of semester ' . $this->request->data['Readmission']['semester'] . '.');
				}
			}
		}

		$student_section_exam_status = $this->Readmission->Student->get_student_section($this->student_id);
		$this->set(compact('student_section_exam_status'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid readmission');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			if ($this->Readmission->save($this->request->data)) {
				$this->Flash->success(__('The readmission application has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The readmission application could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Readmission->read(null, $id);
		}

		$students = $this->Readmission->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid id for readmission.'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->Readmission->delete($id)) {
			$this->Flash->success(__('The Readmission application is deleted.'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->Flash->error(__('The Readmission application was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}

	function ac_approve_readmission()
	{
		$this->paginate = array(
			'contain' => array(
				'Student' => array(
					'fields' => array(
						'id', 
						'program_id', 
						'program_type_id', 
						'full_name', 
						'department_id',
						'gender',
						'studentnumber',
						'graduated'
					), 
					'Clearance' => array(
						'conditions' => array('Clearance.confirmed = 1'),
						'order' => array('Clearance.created DESC'),
						'Attachment'
					),
					'StudentExamStatus' => array(
						'order' => array('StudentExamStatus.created DESC')
					), 
					'Department' => array('id', 'name'),
					'Program' => array('id', 'name'), 
					'ProgramType' => array('id', 'name')
				)
			)
		);

		if ($this->role_id == ROLE_COLLEGE) {
			$department_ids = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
			$department_id = array_keys($department_ids);
			$beginning['pre'] = 'Pre/Fresh';
			$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
			$departments = $beginning + $departments;
			$college_ids = $this->college_id;
		} else if ($this->role_id == ROLE_DEPARTMENT) {
			$department_ids =  $departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$department_ids = $departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$college_ids = $colleges = $this->Readmission->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
			$programs = $this->Readmission->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$programTypes = $this->Readmission->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		}

		if (!empty($this->request->data) && isset($this->request->data['filterReadmission'])) {
			
			$options = array();
			$options[] = array("Readmission.registrar_approval = 1");

			if (!empty($this->request->data['Search']['department_id'])) {
				$options[] = array("Student.department_id" => $this->request->data['Search']['department_id']);
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				if (strcasecmp($this->request->data['Search']['department_id'], 'pre') === 0) {
					$options[] = array(
						'Student.department_id is null',
						'Student.college_id' => $this->college_id,
						"Readmission.academic_year like " => $this->request->data['Search']['academic_year'] . '%',
					);
				} else if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array(
						"Readmission.academic_year like " => $this->request->data['Search']['academic_year'] . '%',
						'Student.department_id' => $department_ids
					);
				} else {
					$options[] = array(
						"Readmission.academic_year like " => $this->request->data['Search']['academic_year'] . '%'
					);
				}
			}

			if (!empty($this->request->data['Search']['semester'])) {
				if (strcasecmp($this->request->data['Search']['department_id'], 'pre') === 0) {
					$options[] = array(
						'Student.department_id is null',
						'Student.college_id' => $this->college_id,
						"Readmission.semester" => $this->request->data['Search']['semester']
					);
				} else if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array(
						"Readmission.semester" => $this->request->data['Search']['semester'],
						'Student.department_id' => $department_ids
					);
				} else {
					$options[] = array("Readmission.semester" => $this->request->data['Search']['semester']);
				}
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				if (strcasecmp($this->request->data['Search']['department_id'], 'pre') === 0) {
					$options[] = array(
						'Student.department_id is null',
						'Student.college_id' => $this->college_id,
						"Student.program_id" => $this->request->data['Search']['program_id']
					);
				} else if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array(
						"Student.program_id" => $this->request->data['Search']['program_id'],
						'Student.department_id' => $department_ids
					);
				} else {
					$options[] = array("Student.program_id" => $this->request->data['Search']['program_id']);
				}
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {

				$options[] = array("Student.program_type_id " => $this->request->data['Search']['program_type_id']);

				if (strcasecmp($this->request->data['Search']['department_id'], 'pre') === 0) {
					$options[] = array(
						'Student.department_id is null',
						'Student.college_id' => $this->college_id,
						"Student.program_type_id" => $this->request->data['Search']['program_type_id']
					);
				} else if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array(
						"Student.program_type_id" => $this->request->data['Search']['program_type_id'],
						'Student.department_id' => $department_ids
					);
				} else {
					$options[] = array("Student.program_type_id" => $this->request->data['Search']['program_type_id']);
				}
			}

			$readmissions = $this->paginate($options);

			if (empty($readmissions)) {
				$this->Flash->inf0('There is no readmission applicant(s) found in the given criteria.');
			} else {
				$readmissions = $this->Readmission->organizeListOfReadmissionApplicant($readmissions);
			}

			$this->set('readmissions', $readmissions);
			$search = true;
			$this->set(compact('search'));
		} else {
			if ($this->role_id == ROLE_COLLEGE) {
				$options[] = array(
					"Readmission.registrar_approval = 1",
					"Readmission.academic_commision_approval is null",
					"Student.college_id " => $this->college_id
				);
			} else if ($this->role_id == ROLE_DEPARTMENT) {
				$options[] = array(
					"Readmission.registrar_approval = 1",
					"Readmission.academic_commision_approval is null",
					"Student.department_id " => $this->department_id
				);
			} else if ($this->role_id == ROLE_REGISTRAR) {
				if (!empty($this->department_ids)) {
					$options[] = array(
						"Readmission.registrar_approval = 1",
						"Readmission.academic_commision_approval is null",
						"Student.department_id " => $this->department_ids,
						"Student.program_id" => $this->program_id,
						"Student.program_type_id" => $this->program_type_id
					);
				} else if (!empty($this->college_ids)) {
					$options[] = array(
						"Readmission.registrar_approval = 1",
						"Readmission.academic_commision_approval is null",
						"Student.department_id is null",
						"Student.college_id " => $this->college_ids,
						"Student.program_id" => $this->program_id,
						"Student.program_type_id" => $this->program_type_id
					);
				}
			}


			$readmissions = $this->paginate($options);

			if (empty($readmissions)) {
				$this->Flash->info('There is no readmission applicant(s) found in the given criteria.');
			} else {
				$readmissions = $this->Readmission->organizeListOfReadmissionApplicant($readmissions);
			}

			$this->set('readmissions', $readmissions);
		}

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			$this->Readmission->create();

			if (!empty($this->request->data['Readmission'])) {
				foreach ($this->request->data['Readmission'] as $in => &$va) {
					if ($va['academic_commision_approval'] != "") {

						$va['academic_commission_approved_by'] = $this->Auth->user('id');
						$va['academic_commission_approval_date'] = date('Y-m-d');

						if ($va['academic_commision_approval'] == 1) {
							$this->Readmission->Student->StudentsSection->id = $this->Readmission->Student->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id' => $va['student_id'], 'StudentsSection.archive' => 0));
							$this->Readmission->Student->StudentsSection->saveField('archive', '1');
						}
					} else {
						unset($this->request->data['Readmission'][$in]);
					}
				}
			}


			if ($this->Readmission->saveAll($this->request->data['Readmission'], array('validate' => 'first'))) {
				$this->Flash->success('The selected readmission applicant(s) request(s) have been approved and students will be notified.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The readmission could not be saved. Please, try again.');
			}
		}

		if ($this->role_id == ROLE_REGISTRAR) {
			$programs = $this->Readmission->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$programTypes = $this->Readmission->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		} else {
			$programs = $this->Readmission->Student->Program->find('list');
			$programTypes = $this->Readmission->Student->ProgramType->find('list');
		}
		
		$this->set(compact('programs', 'departments', 'programTypes'));
	}

	function process_readmission_application()
	{
		$this->paginate = array(
			'contain' => array(
				'Student' => array(
					'fields' => array(
						'id', 
						'program_id', 
						'program_type_id', 
						'full_name', 
						'department_id',
						'gender',
						'studentnumber',
						'graduated'
					), 
					'Clearance' => array(
						'conditions' => array('Clearance.confirmed = 1'),
						'order' => array('Clearance.created' => 'DESC'),
						'Attachment'
					),
					'StudentExamStatus' => array(
						'order' => array('StudentExamStatus.created' => 'DESC')
					), 
					'Department' => array('id', 'name'), 
					'Program' => array('id', 'name'), 
					'ProgramType' => array('id', 'name')
				)
			)
		);

		if (!empty($this->department_ids)) {
			$department_id = $this->department_ids;
			$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $department_id, 'Department.active' => 1)));
			$this->set(compact('departments'));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->Readmission->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			$this->set(compact('colleges'));
		}

		if (!empty($this->request->data) && isset($this->request->data['filterReadmission'])) {
			
			$options = array();
			$options[] = array("Readmission.registrar_approval is null");
			
			if (!empty($this->department_ids)) {
				if (empty($this->request->data['Search']['department_id'])) {
					$options[] = array('Student.department_id' => $this->department_ids);
				}
			} else if (!empty($this->college_ids)) {
				if (empty($this->request->data['Search']['college_id'])) {
					$options[] = array(
						'Student.college_id' => $this->college_ids,
						'Student.department_id is null'
					);
				}
			}

			if (!empty($this->request->data['Search']['department_id'])) {
				$options[] = array("Student.department_id" => $this->request->data['Search']['department_id']);
			}

			if (!empty($this->request->data['Search']['college_id'])) {
				$options[] = array(
					"Student.department_id is null",
					"Student.college_id" => $this->request->data['Search']['college_id']
				);
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$options[] = array("Readmission.academic_year like " => $this->request->data['Search']['academic_year'] . '%');
			}

			if (!empty($this->request->data['Search']['semester'])) {
				$options[] = array("Readmission.semester " => $this->request->data['Search']['semester']);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options[] = array("Student.program_id " => $this->request->data['Search']['program_id']);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options[] = array("Student.program_type_id " => $this->request->data['Search']['program_type_id']);
			}

			$readmissions = $this->paginate($options);

			if (empty($readmissions)) {
				$this->Flash->info('There is no readmission applicant(s) found in the given criteria.');
			} else {
				$readmissions = $this->Readmission->organizeListOfReadmissionApplicant($readmissions);
			}

			$this->set('readmissions', $readmissions);
			$search = true;
			$this->set(compact('search'));
		}

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			$this->Readmission->create();

			if (!empty($this->request->data['Readmission'])) {
				foreach ($this->request->data['Readmission'] as $in => &$va) {
					$va['registrar_approved_by'] = $this->Auth->user('id');
					$va['registrar_approval_date'] = date('Y-m-d');
				}
			}

			if ($this->Readmission->saveAll($this->request->data['Readmission'], array('validate' => 'first'))) {
				$this->Flash->success('The selected readmission applicant(s) request(s) is/are dispathed to academic commission decision.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The readmission applicant(s) request(s) could not be saved. Please, try again.');
			}
		}

		if ($this->role_id == ROLE_REGISTRAR) {
			$programs = $this->Readmission->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$programTypes = $this->Readmission->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		} else {
			$programs = $this->Readmission->Student->Program->find('list');
			$programTypes = $this->Readmission->Student->ProgramType->find('list');
		}

		$departments = $this->Readmission->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
		
		$this->set(compact('programs', 'departments', 'programTypes'));
	}


	/***
		1. Display list of department, program, program type (optional)
		2. After "List Students" button, all students but note in the senate list and graduation list who take all courses will be displayed  (non eligible students will be displayed in red with justification '+')
		3. A check-box to include students in the senate list
	***/
	function apply_readmission_for_student($department_id = null, $program_id = null, $program_type_id = null)
	{

		if ($this->role_id == ROLE_STUDENT) {
			return $this->redirect(array('action' => 'apply'));
		}

		if ($this->role_id == ROLE_REGISTRAR) {
			$programs = $this->Readmission->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$program_types = $programTypes = $this->Readmission->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		} else {
			$programs = $this->Readmission->Student->Program->find('list');
			$program_types = $programTypes = $this->Readmission->Student->ProgramType->find('list');
		}

		$departments = $this->Readmission->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);

		$department_combo_id = null;
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;

		$admission_years = $this->AcademicYear->academicYearInArray(date('Y') - ACY_BACK_FOR_READMISSION , date('Y'));

		//When any of the button is clicked (List students or Add to Senate List)
		if (isset($this->request->data) && !empty($this->request->data['Readmission'])) {
			if (!empty($this->department_ids)) {
				$students_for_readmission_list = $this->Readmission->getListOfStudentsForReadmission(
					1,
					$this->request->data['Readmission']['program_id'],
					$this->request->data['Readmission']['program_type_id'],
					$this->request->data['Readmission']['department_id'],
					$this->request->data['Readmission']['academic_year'],
					$this->request->data['Readmission']['semester'],
					$this->request->data['Readmission']['name'],
					$admission_years
				);
			} else if (!empty($this->college_ids)) {

				$extracted_college = explode('~', $this->request->data['Readmission']['department_id']);

				$students_for_readmission_list = $this->Readmission->getListOfStudentsForReadmission(
					0,
					$this->request->data['Readmission']['program_id'],
					$this->request->data['Readmission']['program_type_id'],
					$extracted_college[1],
					$this->request->data['Readmission']['academic_year'],
					$this->request->data['Readmission']['semester'],
					$this->request->data['Readmission']['name'],
					$admission_years
				);
			}

			$default_department_id = $this->request->data['Readmission']['department_id'];
			$default_program_id = $this->request->data['Readmission']['program_id'];
			$default_program_type_id = $this->request->data['Readmission']['program_type_id'];

		} else if (!empty($department_id) && !empty($program_id)) {
			$students_for_readmission_list = $this->Readmission->getListOfStudentsForReadmission($program_id, $program_type_id, $department_id);
			
			$default_department_id = $department_id;
			$default_program_id = $program_id;
			$default_program_type_id = $program_type_id;
		}

		if (isset($this->request->data) && isset($this->request->data['addStudentToReadmissionList'])) {
			
			$readmission_list = array();
			$clearance_list_by_readmission = array();
			
			if (!empty($this->request->data['Student'])) {
				foreach ($this->request->data['Student'] as $key => $student) {
					if ($student['include_readmission'] == 1) {
						
						$sl_count = $this->Readmission->find('count', array(
							'conditions' => array(
								'Readmission.student_id' => $student['id'],
								'Readmission.semester' => $this->request->data['Readmission']['semester'],
								'Readmission.academic_year' => $this->request->data['Readmission']['academic_year'],
							)
						));
						
						if ($sl_count == 0) {
							$sl_index = count($readmission_list);
							$readmission_list[$sl_index]['student_id'] = $student['id'];
							$readmission_list[$sl_index]['semester'] = $this->request->data['Readmission']['semester'];
							$readmission_list[$sl_index]['academic_year'] = $this->request->data['Readmission']['academic_year'];

							$check_clearance = ClassRegistry::init('Clearance')->find('count', array(
								'conditions' => array(
									'Clearance.student_id' => $student['id'],
									'Clearance.confirmed = 1',
									'Clearance.request_date >= ' => $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['Readmission']['academic_year'], $this->request->data['Readmission']['semester'])
								)
							));

							if ($check_clearance == 0) {
								$clearance_list_by_readmission['Clearance'][$sl_index]['student_id'] = $student['id'];
								$clearance_list_by_readmission['Clearance'][$sl_index]['type'] = 'Clearance';
								$clearance_list_by_readmission['Clearance'][$sl_index]['reason'] = 'Readmission';
								$clearance_list_by_readmission['Clearance'][$sl_index]['request_date'] = date('Y-m-d');
							}
						}
					}
				}
			}

			if (empty($readmission_list)) {
				$this->Flash->error('You are required to select at least one student to be included in the readmission list.');
			} else {
				if ($this->Readmission->saveAll($readmission_list, array('validate' => false))) {
					$this->Flash->success(count($readmission_list) . ' students are included in the readmission list. After registrar filtering and academic commission approval, the student will be readmitted.');
					if (!empty($clearance_list_by_readmission['Clearance'])) {
						debug($clearance_list_by_readmission['Clearance']);
						$this->Readmission->Student->Clearance->create();
						debug($this->Readmission->Student->Clearance->saveAll($clearance_list_by_readmission['Clearance'], array('validate' => false)));
					}
					unset($this->request->data['Student']);
					unset($this->request->data['addStudentToReadmissionList']);
					//return $this->redirect(array('action' => 'apply_readmission_for_student'));
				} else {
					$this->Flash->error('The system unable to include the selected students in the readmission list. Please try again.');
					//return $this->redirect(array('action' => 'apply_readmission_for_student'));
				}
			}
		}

		$this->set(compact(
			'programs',
			'program_types',
			'departments',
			'department_combo_id',
			'students_for_readmission_list',
			'default_department_id',
			'default_program_id',
			'default_program_type_id'
		));
	}


	public function ajax_readmitted_year($student_id)
	{
		$this->layout = 'ajax';

		$student_detail = $this->Readmission->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'contain' => array('AcceptedStudent')));
		$possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($student_detail['AcceptedStudent']['academicyear'], $this->AcademicYear->current_academicyear());

		$student_section_exam_status = $this->Readmission->Student->get_student_section($student_id);
		//debug($student_section_exam_status);

		$pAcYear = array();

		if (!empty($possibleAcademicYears)) {
			foreach ($possibleAcademicYears as $Y) {
				$wasReadmitted = $this->Readmission->find('first', array('conditions' => array('Readmission.student_id' => $student_id, 'Readmission.academic_year' => $Y), 'recursive' => -1));
				if (!empty($wasReadmitted)) {
					$pAcYear[$Y . '~' . $wasReadmitted['Readmission']['id']] = $Y;
				} else {
					$pAcYear[$Y] = $Y;
				}
			}
		}

		$this->set(compact('pAcYear', 'student_detail', 'courses', 'student_section_exam_status'));
	}


	public function readmission_data_entry()
	{
		if (!empty($this->request->data)) {
			//saveReadmission clicked
			if (isset($this->request->data['saveReadmission']) && !empty($this->request->data['saveReadmission'])) {
				
				$readmissionApplication = array();
				$count = 0;
				
				if (!empty($this->request->data['Readmission'])) {
					foreach ($this->request->data['Readmission'] as $key => $readmission) {
						if ($readmission['gp'] == 1 && !empty($readmission['semester'])) {
							if (!empty($readmission['id'])) {
								$readmissionApplication['Readmission'][$count]['id'] = $readmission['id'];
							}

							$readmissionApplication['Readmission'][$count]['academic_year'] = $readmission['academic_year'];
							$readmissionApplication['Readmission'][$count]['semester'] = $readmission['semester'];
							$readmissionApplication['Readmission'][$count]['student_id'] = $readmission['student_id'];
							$readmissionApplication['Readmission'][$count]['minute_number'] = "Via backend data entry interface";
							$readmissionApplication['Readmission'][$count]['registrar_approval'] = 1;
							$readmissionApplication['Readmission'][$count]['registrar_approval_date'] = date('Y-m-d');
							$readmissionApplication['Readmission'][$count]['registrar_approved_by'] = $this->Auth->user('id');
							$readmissionApplication['Readmission'][$count]['academic_commision_approval'] = 1;
							$readmissionApplication['Readmission'][$count]['academic_commission_approved_by'] = $this->Auth->user('id');
							$readmissionApplication['Readmission'][$count]['academic_commission_approval_date'] = date('Y-m-d');
							$readmissionApplication['Readmission'][$count]['remark'] = "Via backend data entry interface";
						}
						$count++;
					}
				}

				if (!empty($readmissionApplication)) {
					if ($this->Readmission->saveAll($readmissionApplication['Readmission'], array('validate' => false))) {
						$this->Flash->success('The readmission has been saved.');
					} else {
						$this->Flash->error('The readmission couldnt be saved.');
					}
				} else {
					$this->Flash->error('Academic year or semeser not selected. Readmission couldnt be saved.');
				}
			}

			//deleteReadmission button is clicked

			if (isset($this->request->data['deleteReadmission']) && !empty($this->request->data['deleteReadmission'])) {
				$readmissionIds = array();

				if (!empty($this->request->data['Readmission'])) {
					foreach ($this->request->data['Readmission'] as $key => $readmission) {
						if ($readmission['gp'] == 1 && !empty($readmission['id'])) {
							$readmissionIds[] = $readmission['id'];
						}
					}
				}

				if (!empty($readmissionIds)) {
					if ($this->Readmission->deleteAll(array('Readmission.id' => $readmissionIds), false)) {
						$this->Flash->success('The readmission application is deleted.');
					} else {
						$this->Flash->error('Readmission was not deleted.');
					}
				}
			}
		}

		$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Search']['student_id']));
	}
}
