<?php
class GraduationController extends AppController
{
	var $name = "Graduation";
	var $uses = array();
	var $menuOptions = array(
		'exclude' => array('index'),
	);
	public $components = array('EthiopicDateTime', 'AcademicYear');

	function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->Allow('course_check_list');
	}

	function index()
	{
	}

	public function course_check_list($student_id = null)
	{

		if (isset($student_id) && !empty($student_id)) {
			$student_id = trim($student_id);
			$this->request->data['student']['id'] = $student_id;
			$this->request->data['continue'] = 1;
			debug($student_id);
			debug($this->request->data);
		} else if (!empty($this->request->data['Student']['studentID'])) {
			$student_id = trim($this->request->data['Student']['studentID']);
		}

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			if (!empty($this->request->data['Student']['studentID']) || $this->request->data['student']['id']) {

				if (!is_numeric($student_id)) {
					$student_id_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.studentnumber' => $student_id)));
				} else {
					$student_id_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.id' => $student_id)));
				}

				if (!$student_id_valid) {
					$this->Flash->warning('The provided student number is not valid. Check for Typo Errors and try again.');
					return $this->redirect(array('action' => 'course_check_list'));
				} else {
					$check_id_is_valid = 0;
					if (!empty($student_id) && is_numeric($student_id)) {
						if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
							if (!empty($this->department_ids)) {
								$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
									'conditions' => array(
										'Student.id' => $student_id,
										'Student.program_type_id' => $this->program_type_id,
										'Student.program_id' => $this->program_id,
										'Student.department_id' => $this->department_ids
									)
								));
							} else if (!empty($this->college_ids)) {
								$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
									'conditions' => array(
										'Student.id' => $student_id,
										'Student.program_type_id' => $this->program_type_id,
										'Student.program_id' => $this->program_id,
										'Student.college_id' => $this->college_ids
									)
								));
							}
						} else if ($this->role_id == ROLE_DEPARTMENT) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.department_id' => $this->department_id)));
						} else if ($this->role_id == ROLE_COLLEGE) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.college_id' => $this->college_id), 'recursive' => -1));
						} else if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1)) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.id' => $student_id)));
						}
					} else if (!empty($student_id) && !is_numeric($student_id)) {
						if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
							if (!empty($this->department_ids)) {
								$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
									'conditions' => array(
										'Student.studentnumber' => $student_id,
										'Student.program_type_id' => $this->program_type_ids,
										'Student.program_id' => $this->program_ids,
										'Student.department_id' => $this->department_ids
									)
								));
							} else if (!empty($this->college_ids)) {
								$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
									'conditions' => array(
										'Student.studentnumber' => $student_id,
										'Student.program_type_id' => $this->program_type_ids,
										'Student.program_id' => $this->program_ids,
										'Student.college_id' => $this->college_ids
									)
								));
							}
						} else if ($this->role_id == ROLE_DEPARTMENT) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.studentnumber' => $student_id, 'Student.department_id' => $this->department_id)));
						} else if ($this->role_id == ROLE_COLLEGE) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.studentnumber' => $student_id, 'Student.college_id' => $this->college_id)));
						} else if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1)) {
							$check_id_is_valid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.studentnumber' => $student_id)));
						}
					}

					if (!$check_id_is_valid) {
						$this->Flash->error('You dont have the privilage to view the selected students profile.');
						return $this->redirect(array('action' => 'course_check_list'));
					} else {

						if (!is_numeric($student_id)) {
							$student_id = ClassRegistry::init('Student')->field('id', array('studentnumber' => $student_id));
						}

						//$student_academic_profile = ClassRegistry::init('Student')->getStudentRegisteredAddDropCurriculumResult($student_id, $this->AcademicYear->current_academicyear());
						// Checking if the grade hide thing will be removed if $current_academic_year and $current_semester is is set to null, Neway
						
						$student_academic_profile = ClassRegistry::init('Student')->getStudentRegisteredAddDropCurriculumResult($student_id, null, 1);
						debug($student_academic_profile);
						
						$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);
						debug($studentAttendedSections);
						$this->set(compact('student_academic_profile', 'studentAttendedSections'));
					}
					
				}
			} else {
				$this->Flash->error('Please provide student number to view profile.');
			}
		}
	}
}
