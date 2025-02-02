<?php
class CourseExemptionsController extends AppController
{

	var $name = 'CourseExemptions';

	var $menuOptions = array(
		'parent' => 'registrations',
		'exclude' => array(
			'approve_request', 
			'index',
			'add_student_exempted_course',
			'add_student_exemption'
		),
		'alias' => array(
			'list_exemption_request' => 'Approve Exemption Requests',
			'add' => 'Add Course Exemption Request',
			'list_approved' => 'View Exemption'
		)
	);

	
	var $helpers = array('Media.Media');
	var $components = array('AcademicYear');

	function beforeRender()
	{
		$acyear_array_data = $this->AcademicYear->acyear_array();
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
	
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow('invalid');
	}

	function list_exemption_request()
	{

		$conditions = array();
		$limit = 100;

		if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) {

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				
				$conditions[] = array(
					"CourseExemption.department_accept_reject is null",
					"Student.department_id" => $this->department_id,
					"Student.graduated" => 0
				);

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

				if (isset($this->college_ids) && !empty($this->college_ids)) {
					
					$conditions[] = array(
						"Student.department_id is null",
						"Student.college_id " => $this->college_ids,
						"Student.graduated" => 0,
						"CourseExemption.registrar_confirm_deny is null",
						"CourseExemption.department_accept_reject" => 1,
					);

				} else if (isset($this->department_ids) && !empty($this->department_ids)) {

					$conditions[] = array(
						"Student.department_id" => $this->department_ids,
						"Student.graduated" => 0,
						"CourseExemption.registrar_confirm_deny is null",
						"CourseExemption.department_accept_reject" => 1,
					);

				}
			}
		}

		//debug($conditions);

		$courseExemptions = array();

		if (!empty($conditions)) {
			$this->Paginator->settings = array(
				'contain' => array(
					'Course' => array(
						'id', 
						'course_code_title', 
						'credit'
					),
					'Student' => array(
						'fields' => array(
							'id', 
							'full_name', 
							'program_id', 
							'program_type_id', 
							'graduated',
							'gender'
						), 
						'Program' => array('id', 'name'), 
						'ProgramType' => array('id', 'name'), 
						'Department' => array('id', 'name'))
				),
				'order' => array('CourseExemption.request_date desc'),
				'limit' => $limit,
				'maxLimit' => $limit, 
			);

			$courseExemptions = $this->paginate($conditions);
		}

		if (empty($courseExemptions)) {
			$this->Flash->info('There is no course exemptions requests that need your approval for now.');
		}

		$this->set('courseExemptions', $courseExemptions);
		$this->set(compact('limit', $limit));

	}

	function index()
	{
		/*  $conditions = array();
		if ($this->role_id != ROLE_STUDENT) {
			$this->paginate = array(
				'contain' => array(
					'Course' => array('id', 'course_code_title', 'credit'),
					'Student' => array(
						'fields' => array('id', 'full_name', 'program_id', 'program_type_id'), 
						'Program' => array('id', 'name'), 
						'ProgramType' => array('id', 'name'), 
						'Department' => array('id', 'name')
					)
				),
				'limit' => 100, 'order' => array('CourseExemption.request_date desc')
			);


			$department_ids = array();

			if (!empty($this->department_ids)) {
				$department_ids = $this->department_ids;
			} elseif (!empty($this->department_id)) {
				$department_ids = $this->department_id;
			}

			if ($this->role_id == ROLE_DEPARTMENT) {
				$conditions[] = array(
					"CourseExemption.department_accept_reject is null",
					"Student.department_id" => $department_ids,
				);
			}
			if ($this->role_id == ROLE_REGISTRAR) {
				// display only approved exemption
				$conditions[] = array(
					"CourseExemption.request_date <= " => date("Y-m-d"),
					"Student.department_id " => $department_ids,
					"CourseExemption.registrar_confirm_deny is null",
					"CourseExemption.department_accept_reject" => 1,
				);
			}
		} */
		  
		if (!empty($this->request->data) && isset($this->request->data['viewExemption'])) {
			$options = array();

			if (!empty($this->request->data['Search']['program_id'])) {
				$curriculums = $this->CourseSubstitutionRequest->CourseBeSubstitued->Curriculum->find('list', array(
						'fields' => array('id'),
						'conditions' => array(
							'Curriculum.department_id' => $this->department_id,
							'Curriculum.program_id' => $this->request->data['Search']['program_id']
						)
					));
				if (!empty($options)) {
				} else {
					$options[] = array(
						'CourseForSubstitued.curriculum_id' => $curriculums
					);
				}
			}

			///////////////////////////////////////////////////


			if ($this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array(
					"CourseSubstitutionRequest.department_approve" => 0,
					'Student.department_id' => $this->department_id,
					"Student.graduated" => 0,
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array(
					"CourseSubstitutionRequest.department_approve" => 1,
					'Student.department_id' => $this->department_id,
					"Student.graduated" => 0,
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1) {
				$options[] = array(
					"CourseSubstitutionRequest.department_approve is null",
					'Student.department_id' => $this->department_id,
					"Student.graduated" => 0,
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 0) {
				$options[] = array(
					"CourseSubstitutionRequest.department_approve" => array(0, 1),
					'Student.department_id' => $this->department_id,
					"Student.graduated" => 0,
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1) {
				$options[] = array(
					'OR' => array(
						"CourseSubstitutionRequest.department_approve" => 1,
						"CourseSubstitutionRequest.department_approve is null "
					),
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
					"Student.graduated" => 0,
				);
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 1 ) {
				$options[] = array(
					'OR' => array(
						"CourseSubstitutionRequest.department_approve" => 0,
						"CourseSubstitutionRequest.department_approve is null",
					),
					'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
					"Student.graduated" => 0,
				);
			}

			if (!empty($options)) {
				$courseExemptions = $this->paginate($options);
			}

			if (empty($courseExemptions)) {
				$this->Flash->info('There is no course exemption requests found in the given criteria.');
			}
			
		} else {
			if ($this->role_id == ROLE_STUDENT) {
				$this->paginate = array(
					'contain' => array(
						'Course' => array(
							'id', 
							'course_code_title', 
							'credit'
						),
						'Student' => array(
							'fields' => array(
								'id', 
								'full_name',
								'studentnumber'
							), 
							'Department' => array('id', 'name')
						)
					),
					'order' => array('CourseExemption.request_date desc'),
					'limit' => 100, 
				);

				$options[] = array(
					"CourseExemption.student_id" => $this->student_id,
					'Student.graduated' => 0,
					'CourseExemption.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}
			$courseExemptions = $this->paginate($options);
		}


		if (empty($courseExemptions)) {
			$this->Flash->info('There is no course exemptions requests.');
		}

		$this->set('courseExemptions', $courseExemptions);
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid course exemption'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('courseExemption', $this->CourseExemption->read(null, $id));
	}

	function add()
	{
		if (!empty($this->request->data)) {
			//check duplicate entry
			$duplicated = $this->CourseExemption->find('count', array('conditions' => $this->request->data['CourseExemption']));

			if ($duplicated == 0) {
				$this->CourseExemption->create();
				$this->request->data['CourseExemption']['request_date'] = date('Y-m-d');

				if ($this->CourseExemption->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success('The course exemption has been saved');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The course exemption could not be saved. Please, try again.');
				}
			} else {
				$this->Flash->warning('The course exemption could not be saved. You have already requested course exemptions for the selected courses.');
				$this->redirect(array('action' => 'index'));
			}
		}

		$current_academic_year = $this->AcademicYear->current_academicyear();

		$student_section_exam_status = $this->CourseExemption->Student->get_student_section($this->student_id, $current_academic_year);

		$courses = $this->CourseExemption->Course->find('list', array(
			'conditions' => array(
				'Course.curriculum_id' => $student_section_exam_status['StudentBasicInfo']['curriculum_id']
			),
			'fields' => array('id', 'course_code_title')
		));

		$previous_exemption_accepted = $this->CourseExemption->find('all', array(
			'conditions' => array(
				'CourseExemption.student_id' => $student_section_exam_status['StudentBasicInfo']['id'], 
				'CourseExemption.department_accept_reject' => 1,
				'CourseExemption.registrar_confirm_deny' => 1,
				'CourseExemption.department_approve_by is not null'
			)
		));

		//$students = $this->CourseExemption->Student->find('list');
		$this->set(compact('courses', 'previous_exemption_accepted', 'student_section_exam_status'));
	}

	function edit($id = null)
	{
		$this->CourseExemption->id = $id;

		if (!$this->CourseExemption->exists()) {
			$this->Session->setFlash(__('Invalid course exemption'));
		}

		if (!empty($this->request->data)) {
			if ($this->CourseExemption->save($this->request->data)) {
				$this->Flash->success(__('The course exemption has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The course exemption could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->CourseExemption->read(null, $id);
		}

		$courses = $this->CourseExemption->Course->find('list');
		$students = $this->CourseExemption->Student->find('list');

		$this->set(compact('courses', 'students'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid id for course exemption'));
			return $this->redirect(array('action' => 'index'));
		}

		// dont allow deletion if the students request is accepted or reject by department
		$is_deletion_allowed = $this->CourseExemption->find('count', array(
			'conditions' => array(
				'CourseExemption.id' => $id, 
				"OR" => array(
					'CourseExemption.department_approve_by is null',
					'CourseExemption.department_approve_by' => array('')
				),
				'CourseExemption.student_id' => $this->student_id
			)
		));

		if ($is_deletion_allowed > 0) {
			if ($this->CourseExemption->delete($id)) {
				$this->Flash->success( __('Course exemption request is cancelled.'));
			} else {
				$this->Flash->error(__('Course exemption could not be cancelled. Please try again.'));
			}
		} else {
			$this->Flash->error(__('Course exemption could not be cancelled. You request has been approved/rejected by your department.'));
		}

		return $this->redirect(array('action' => 'index'));
	}
	
	function approve_request($id = null)
	{

		if (!empty($this->request->data)) {

			$department_ids = array();

			if (!empty($this->department_ids)) {
				$department_ids = $this->department_ids;
			} elseif (!empty($this->department_id)) {
				$department_ids = $this->department_id;
			}

			$elgibile_to_approve = $this->CourseExemption->Student->find('count', array(
				'conditions' => array(
					'Student.department_id' => $department_ids,
					'Student.id' => $this->request->data['CourseExemption']['student_id']
				)
			));

			if ($elgibile_to_approve > 0) {

				if ($this->role_id == ROLE_DEPARTMENT) {
					$this->request->data['CourseExemption']['department_approve_by'] = $this->Auth->user('full_name');
				} else if ($this->role_id == ROLE_REGISTRAR) {
					$this->request->data['CourseExemption']['registrar_approve_by'] = $this->Auth->user('full_name');
				}

				if ($this->CourseExemption->save($this->request->data)) {
					
					$this->Flash->success(__('The course exemption request has been saved'));
					//registrar

					if ($this->role_id == ROLE_REGISTRAR) {
						$count = $this->CourseExemption->find('count', array(
							'conditions' => array(
								'Student.department_id' => $department_ids,
								'CourseExemption.department_approve_by is not null', 
								"OR" => array(
									'CourseExemption.registrar_approve_by is null', 
									'CourseExemption.registrar_approve_by' => array('')
								)
							)
						));
					} else {
						$count = $this->CourseExemption->find('count', array(
							'conditions' => array(
								'Student.department_id' => $department_ids,
								"OR" => array(
									'CourseExemption.department_approve_by is null', 
									'CourseExemption.department_approve_by' => array('')
								)
							)
						));
					}

					if ($count == 0) {
						$this->redirect(array('action' => 'list_approved'));
					} else {
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$this->Flash->error(__('The course exemption request could not be saved. Please, try again.'));
					$this->request->data = $this->CourseExemption->read(null, $id);
				}
			} else {
				$this->Flash->error(__('You are not elgible to approve the exemption request.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->CourseExemption->read(null, $id);
		}

		$current_academic_year = $this->AcademicYear->current_academicyear();

		$student_section_exam_status = $this->CourseExemption->Student->get_student_section($this->request->data['CourseExemption']['student_id'], $current_academic_year);

		$courseForSubstitueds = $this->CourseExemption->Course->find('list', array(
			'conditions' => array(
				'Course.curriculum_id' => $student_section_exam_status['StudentBasicInfo']['curriculum_id']
			),
			'fields' => array('id', 'course_title')
		));

		$previous_exemption_accepted = $this->CourseExemption->find('all', array(
			'conditions' => array(
				'CourseExemption.student_id' => $this->request->data['CourseExemption']['student_id'], 
				'CourseExemption.department_accept_reject' => 1,
				'CourseExemption.registrar_confirm_deny' => 1,
				'CourseExemption.department_approve_by is not null'
			))
		);

		$courses = $this->CourseExemption->Course->find('list', array('fields' => array('id', 'course_title')));

		$this->set(compact(
			'students',
			'courses',
			'student_section_exam_status',
			'previous_exemption_accepted'
		));
	}

	function list_approved()
	{
		$this->paginate = array('limit' => 200);
		$options = array();
		$department_id = array();
		if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$department_id = $this->department_ids;
			}
		} else if ($this->role_id == ROLE_DEPARTMENT) {
			$department_id = $this->department_id;
		}

		if (!empty($this->request->data) && isset($this->request->data['viewExemption'])) {

			if (!empty($this->request->data['Search']['department_id'])) {

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
				}

				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
				}
			}

			if (!empty($this->request->data['Search']['year_approved']['year'])) {

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						' CourseExemption.request_date LIKE ' => '%' . $this->request->data['Search']['year_approved']['year'] . '%',
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						' CourseExemption.request_date LIKE ' => '%' . $this->request->data['Search']['year_approved']['year'] . '%',
						'Student.department_id' => $department_id
					);
				}
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						'Student.program_id' => $this->request->data['Search']['program_id'],
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						'Student.program_id' => $this->request->data['Search']['program_id'],
						'Student.department_id' => $department_id
					);
				}
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						'Student.program_type_id' => $this->request->data['Search']['program_type_id'],
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						'Student.program_type_id' => $this->request->data['Search']['program_type_id'],
						'Student.department_id' => $department_id
					);
				}
			}


			if (!empty($this->request->data['Student']['studentnumber'])) {
				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						'Student.studentnumber LIKE ' => $this->request->data['Search']['studentnumber'] . '%',
						'Student.department_id' => $department_id
					);
				} else if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						'Student.studentnumber LIKE ' => $this->request->data['Search']['studentnumber'] . '%',
						'Student.department_id' => $department_id
					);
				}
			}


			if ($this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						"CourseExemption.department_accept_reject" => 0,
						'Student.department_id' => $department_id
					);
				}
				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						"CourseExemption.registrar_confirm_deny" => 0,
						'Student.department_id' => $department_id
					);
				}
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 0) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						"CourseExemption.department_accept_reject" => 1,
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						"CourseExemption.registrar_confirm_deny" => 1,
						'Student.department_id' => $department_id
					);
				}
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						"CourseExemption.department_accept_reject is null",
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						"CourseExemption.registrar_confirm_deny is null",
						'Student.department_id' => $department_id
					);
				}
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 0) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						"CourseExemption.department_accept_reject" => array(0, 1),
						'Student.department_id' => $department_id
					);
				}

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						"CourseExemption.registrar_confirm_deny" => array(0, 1),
						'Student.department_id' => $department_id
					);
				}
			}

			if ($this->request->data['Search']['accepted'] == 1 && $this->request->data['Search']['rejected'] == 0 && $this->request->data['Search']['notprocessed'] == 1) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						'Student.department_id' => $department_id,
						'OR' => array(
							"CourseExemption.department_accept_reject" => 1,
							"CourseExemption.department_accept_reject is null"
						)
					);
				}

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						'Student.department_id' => $department_id,
						'OR' => array(
							"CourseExemption.registrar_confirm_deny" => 1,
							"CourseExemption.registrar_confirm_deny is null"
						)
					);
				}
			}

			if ($this->request->data['Search']['accepted'] == 0 && $this->request->data['Search']['rejected'] == 1 && $this->request->data['Search']['notprocessed'] == 1) {
				if ($this->role_id == ROLE_DEPARTMENT) {
					$options[] = array(
						'Student.department_id' => $department_id,
						'OR' => array(
							"CourseExemption.department_accept_reject" => 0,
							"CourseExemption.department_accept_reject is null"
						)
					);
				}

				if ($this->role_id == ROLE_REGISTRAR) {
					$options[] = array(
						'Student.department_id' => $department_id,
						'OR' => array(
							"CourseExemption.registrar_confirm_deny" => 0,
							"CourseExemption.registrar_confirm_deny is null"
						)
					);
				}
			}
		}


		if ($this->role_id == ROLE_STUDENT) {
			$options[] = array("CourseExemption.student_id" => $this->student_id);
		} else {
			if (empty($options)) {
				$options[] = array("Student.department_id" => $department_id);
			}
		}

		/*
		   if (!empty($this->request->data['Student']['studentnumber'])) {
		     $student_number=$this->request->data['Student']['studentnumber'];
		   }
	       if ($this->role_id != ROLE_STUDENT) {
	       
	    	  $this->paginate = array('contain'=>array('Student'=>array('Department'),'Course'));
	    	  if ($this->role_id == ROLE_REGISTRAR) {
	    	      $department_ids=array();
	    	      $college_ids = array();
	    	      
	    	      if (!empty($this->department_ids)) {
	    	         if (!empty($this->request->data['Student']['department_id'])) {
	    	            $department_ids=$this->request->data['Student']['department_id'];
	    	         } else {
	    	            $department_ids=$this->department_ids;
	    	         }
	    	      } else if (!empty($this->college_ids)) {
	    	         $college_ids=$this->college_ids;
	    	         
	    	      } else {
	    	        $department_ids=$this->department_id;
	    	        $college_ids = $this->college_id;
	    	      }
	    	     
	        	      if (!empty($department_ids)) {
	            	      $conditions = array(
                            "CourseExemption.request_date <= " => date("Y-m-d"),
                            
                            'Student.department_id'=>$department_ids     
                        );
			             
			          } else if (!empty($college_ids)) {
			            $conditions = array(
                            "CourseExemption.request_date <= " => date("Y-m-d"),
                           
                            'Student.college_id'=>$college_ids
                            );
			          
			          } 
			   
			    
	    	  } else if ($this->role_id == ROLE_DEPARTMENT)  {
	    	        if (!empty($student_number)) {
	    	             $studentnumber_valide=$this->CourseExemption->Student->find('count',
	    	             array('conditions'=>array('Student.studentnumber LIKE '=>$student_number.'%')));
	    	         
	    	             if ($studentnumber_valide) {
	            	         $conditions = array(
                                 "CourseExemption.request_date <= " => date("Y-m-d"),
                                
                                 "Student.department_id"=>$this->department_id,
                                 "Student.studentnumber LIKE "=>trim($student_number).'%',
                               
			                );
			             } else {
			                 $this->Session->setFlash('<span></span>'.__('There student number is not valid.'));
			                  $conditions = array(
                                 "CourseExemption.request_date <= " => date("Y-m-d"),
                               
                                 "Student.department_id"=>$this->department_id
			                );
			             }
			        } else {
			             $conditions = array(
                             "CourseExemption.request_date <= " => date("Y-m-d"),
                           
                             "Student.department_id"=>$this->department_id                             
			            );
			        }
	    	  }
	         
	       }
	       
	       if ($this->role_id == ROLE_STUDENT) {
	       
	          $conditions = array(
                    "CourseExemption.request_date <= " => date("Y-m-d"),
                   
                    "CourseExemption.student_id"=>$this->student_id
			     );
			   
		     
	        }
	        */
		//$this->paginate($conditions)
		$courseExemptions = $this->paginate($options);

		if (empty($courseExemptions)) {
			$this->Flash->info( __('There is no course exemptions request is found with the given search criteria.'));
		} else {
			$this->set('courseExemptions', $courseExemptions);
			$search_visible = true;
			$this->set('search_visible', $search_visible);
		}

		if ($this->role_id == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$departments = $this->CourseExemption->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
			}
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			if (!empty($this->department_id)) {
				$departments = $this->CourseExemption->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
			}
		}

		$programs = $this->CourseExemption->Student->Program->find('list');
		$programTypes = $this->CourseExemption->Student->ProgramType->find('list');

		$this->set(compact('departments', 'programs', 'programTypes'));
	}
	
	function invalid()
	{
		//$this->cakeError('youSuck');
	}

	public function add_student_exempted_course($student_id)
	{
		$this->layout = 'ajax';

		$student_detail = $this->CourseExemption->Student->find('first', array(
			'conditions' => array('Student.id' => $student_id),
			'contain' => array(
				'AcceptedStudent',
				'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced'),
				'CurriculumAttachment'
			)
		));

		debug($student_detail);

		$student_attached_curriculums_count = count($student_detail['CurriculumAttachment']);

		debug($student_attached_curriculums_count);

		$student_attached_curriculum_ids = array();

		if ($student_attached_curriculums_count > 1) {

			foreach ($student_detail['CurriculumAttachment'] as $key => $cattachments) {
				$student_attached_curriculum_ids[$cattachments['curriculum_id']] = $cattachments['curriculum_id'];
			}

			$student_attached_curriculum_ids = array_unique($student_attached_curriculum_ids);

			$student_attached_curriculums_count = count($student_attached_curriculum_ids);

		} else {
			$student_attached_curriculum_ids[$student_detail['Student']['curriculum_id']] = $student_detail['Student']['curriculum_id'];
		}

		debug($student_attached_curriculums_count);
		debug($student_attached_curriculum_ids);

		//TO DO: Exclude Registered Added Courses, Neway
		$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($student_id, null, null);
		//debug($student_section_exam_status);

		$takenCourses = $this->CourseExemption->Student->getStudentRegisteredAddDropCurriculumResult($student_id,null, /* $for_document =  */1, /* $includeBasicProfile = */ 0, /* $includeResult = */ 0, /* $includeExemption = */ 1);

		//debug($takenCourses);
		
		$excludeCoursesList = array();
		
		if (!empty($takenCourses)) {
			foreach ($takenCourses as $key => $courseRegAddExempt) {
				foreach ($courseRegAddExempt as $key => $course) {
					if (isset($course['course_id']) && !empty($course['course_id'])) {
						$excludeCoursesList[] = $course['course_id'];
						$equivalentCourses = ClassRegistry::init('EquivalentCourse')->find('list', array('conditions' => array('EquivalentCourse.course_be_substitued_id' => $course['course_id']), 'fields' => array('EquivalentCourse.course_for_substitued_id', 'EquivalentCourse.course_for_substitued_id')));
						if (!empty($equivalentCourses)) {
							//debug($equivalentCourses);
							foreach ($equivalentCourses as $ec_key => $ec_value) {
								$excludeCoursesList[] = $ec_value;
							}
						}
					}
				}
			}
		}

		/* $alreadyExemptedCourses = $this->CourseExemption->find('list', array('conditions' => array('CourseExemption.registrar_confirm_deny' => 1, 'CourseExemption.student_id' => $student_id), 'fields' => array('CourseExemption.course_id', 'CourseExemption.course_id')));
		debug($alreadyExemptedCourses);
		
		if (!empty($alreadyExemptedCourses)) {
			foreach ($alreadyExemptedCourses as $key => $ex_value) {
				$excludeCoursesList[] = $ex_value;
			}
		} */
		// check and remove already exempted courses while updating existing Exemptions exemptions, Neway

		//debug($excludeCoursesList);
		$studentHaveSection = 0;
		$takenCoursesCount = 0;

		if (isset($student_section_exam_status['Section']['id']) && isset($student_section_exam_status['Section']['YearLevel']['id']) /* && !$student_section_exam_status['Section']['archive'] */) {
			$studentHaveSection = 1;
		}

		//debug(count($takenCourses['Course Registered']));
		//debug(count($takenCourses['Course Added']));

		if (!empty($takenCourses['Course Registered'])) {
			$takenCoursesCount = count($takenCourses['Course Registered']);
		}

		if (!empty($takenCourses['Course Added'])) {
			$takenCoursesCount += count($takenCourses['Course Added']);
		}

		//debug($takenCourses);

		if (!empty($excludeCoursesList)) {

			$yearLevel = 0;

			if (isset($student_section_exam_status['Section']['id']) && isset($student_section_exam_status['Section']['YearLevel']['id'])) {
				$yearLevel = $student_section_exam_status['Section']['YearLevel']['id'];
				//$studentHaveSection = 1;
			}

			if ($yearLevel) {
				$courses = $this->CourseExemption->Course->find('list', array(
					'conditions' => array(
						'NOT' => array(
							'Course.id' => $excludeCoursesList,
						),
						'Course.curriculum_id' => $student_detail['Student']['curriculum_id'],
						'Course.year_level_id <=' => $yearLevel,
						'Course.active' => 1,
					), 
					'fields' => array('id', 'course_title'),
				));
			} else {
				$courses = $this->CourseExemption->Course->find('list', array(
					'conditions' => array(
						'NOT' => array(
							'Course.id' => $excludeCoursesList,
						),
						'Course.curriculum_id' => $student_detail['Student']['curriculum_id'],
						'Course.active' => 1,
					), 
					'fields' => array('id', 'course_title'),
				));
			}
		} else {
			$courses = $this->CourseExemption->Course->find('list', array('conditions' => array('Course.curriculum_id' => $student_detail['Student']['curriculum_id']), 'fields' => array('id', 'course_title')));
		}


		$exemptedCourseLists = $this->CourseExemption->find('all', array(
			'conditions' => array(
				'CourseExemption.student_id' => $student_id,
				'CourseExemption.department_accept_reject' => 1,
				'CourseExemption.registrar_confirm_deny' => 1,
			), 
			'recursive' => -1
		));

		// uncomment this if it is required to show all curricullum courses for already exempted courses for drop down menu and comnment the following if else block 
		//$coursesForList = $this->CourseExemption->Course->find('list', array('conditions' => array('Course.curriculum_id' => $student_attached_curriculum_ids), 'fields' => array('id', 'course_title')));
		
		if (!empty($exemptedCourseLists)) {
			$already_exempted_course_ids = $this->CourseExemption->find('list', array(
				'conditions' => array(
					'CourseExemption.student_id' => $student_id,
					'CourseExemption.department_accept_reject' => 1,
					'CourseExemption.registrar_confirm_deny' => 1,
				), 
				'fields' => array('CourseExemption.course_id','CourseExemption.course_id')
			));
			$coursesForList = $this->CourseExemption->Course->find('list', array('conditions' => array('Course.curriculum_id' => $student_attached_curriculum_ids, 'Course.id' => $already_exempted_course_ids), 'fields' => array('id', 'course_title'))); 
		} else {
			$coursesForList = $this->CourseExemption->Course->find('list', array('conditions' => array('Course.curriculum_id' => $student_attached_curriculum_ids), 'fields' => array('id', 'course_title')));
		}

		//uncommet up to this line if you comment the above to show only exempted courses in the drop down


		// Keep this for students that are attached to more than one curriculum DONT COMMENT THIS to list all courses from all curriculum attached plus with out excluding already exempted courses.
		if ($student_attached_curriculums_count > 1) {
			$coursesForList = $this->CourseExemption->Course->find('list', array('conditions' => array('Course.curriculum_id' => $student_attached_curriculum_ids), 'fields' => array('id', 'course_title')));
		}

		$this->set(compact(
			'sectionOrganized',
			'student_detail',
			'courses',
			'coursesForList',
			'exemptedCourseLists',
			'studentHaveSection',
			'takenCoursesCount',
			'student_section_exam_status',
			'student_attached_curriculums_count'
		));
	}

	public function add_student_exemption()
	{

		if (isset($this->request->data) && !empty($this->request->data)) {
			if (!empty($this->request->data['CourseExemption'])) {
				$formattedCourseExemption = array();
				$count = 0;
				reset($this->request->data['CourseExemption']);

				$student_id = $this->request->data['CourseExemption'][0]['student_id'];
				$transfer_from = $this->request->data['CourseExemption'][0]['transfer_from'];
				
				$allExemptedIds = $this->CourseExemption->find('list', array(
					'conditions' => array(
						'CourseExemption.student_id' => $student_id,
						//'CourseExemption.department_accept_reject' => 1,
						//'CourseExemption.registrar_confirm_deny' => 1,
					),
					'fields' => array('CourseExemption.id', 'CourseExemption.id'),
					'recursive' => -1, 
				));

				debug($this->request->data);


				foreach ($this->request->data['CourseExemption'] as $k => $v) {
					if (isset($student_id) && isset($v['course_id'])) {

						if (!empty($formattedCourseExemption['CourseExemption'][$count]['id'])) {
							$formattedCourseExemption['CourseExemption'][$count]['id'] = $v['id'];
							unset($allExemptedIds[$v['id']]);
						}

						$formattedCourseExemption['CourseExemption'][$count]['request_date'] = date('Y-m-d h:i:s');
						$formattedCourseExemption['CourseExemption'][$count]['reason'] = 'data entry via registrar';
						$formattedCourseExemption['CourseExemption'][$count]['taken_course_title'] = $v['taken_course_title'];
						$formattedCourseExemption['CourseExemption'][$count]['taken_course_code'] = $v['taken_course_code'];
						$formattedCourseExemption['CourseExemption'][$count]['course_taken_credit'] = $v['course_taken_credit'];

						$formattedCourseExemption['CourseExemption'][$count]['department_accept_reject'] = 1;


						$formattedCourseExemption['CourseExemption'][$count]['department_reason'] = 'data entry via registrar';
						$formattedCourseExemption['CourseExemption'][$count]['registrar_confirm_deny'] = 1;
						$formattedCourseExemption['CourseExemption'][$count]['registrar_reason'] = 'data entry via registrar';

						$formattedCourseExemption['CourseExemption'][$count]['department_approve_by'] = $this->Auth->user('full_name');
						$formattedCourseExemption['CourseExemption'][$count]['registrar_approve_by'] = $this->Auth->user('full_name');

						$formattedCourseExemption['CourseExemption'][$count]['course_id'] = $v['course_id'];
						$formattedCourseExemption['CourseExemption'][$count]['student_id'] = $student_id;
						$formattedCourseExemption['CourseExemption'][$count]['transfer_from'] = $transfer_from;
						$formattedCourseExemption['CourseExemption'][$count]['grade'] = $v['grade'];


						$count++;
					}
				}
				

				if (!empty($allExemptedIds)) {
					if ($this->CourseExemption->deleteAll(array('CourseExemption.id' => $allExemptedIds), false)) {
					}
				}

				debug($formattedCourseExemption);

				if (!empty($formattedCourseExemption)) {
					if ($this->CourseExemption->saveAll($formattedCourseExemption['CourseExemption'], array('validate' => 'first'))) {
						$this->Flash->success('The course exemption has been saved');
					} else {
						$this->Flash->error('The exempted courses lists coudnt be saved. Please, try again.');
					}
				}
			} else {
				$this->Flash->error('The exempted courses lists coudnt be saved. Please, try again.');
			}
		}

		$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['CourseExemption'][0]['student_id']));
	}
}
