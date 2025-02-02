<?php
class ExamGradeChange extends AppModel
{
	public $name = 'ExamGradeChange';
	/* We can log all actions by calling this here, but it is also possible to call
    the loggable behavior in selected models.
       */
	var $actsAs = array(
		'Tools.Logable' => array(
			'change' => 'full',
			//'change' => 'list',
			'description_ids' => 'true',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	public $validate = array(
		'grade' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter makeup exam scored grade.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'minute_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter makeup exam minute number.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'ExamGrade' => array(
			'className' => 'ExamGrade',
			'foreignKey' => 'exam_grade_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MakeupExam' => array(
			'className' => 'MakeupExam',
			'foreignKey' => 'makeup_exam_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	function canItBeDeleted($id = "")
	{
		if ($id != "") {
			$exam_grade_change = $this->find('first', array(
				'conditions' => array(
					'ExamGradeChange.id' => $id
				),
				'contain' => array()
			));

			if ($exam_grade_change['ExamGradeChange']['initiated_by_department'] == 1 && $exam_grade_change['ExamGradeChange']['registrar_approval'] == null) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	function examGradeChangeStateDescription($exam_grade_change = null)
	{
		$status = array();

		if (is_array($exam_grade_change)) {
			if (empty($exam_grade_change)) {
				$status['state'] = 'on-process';
				$status['description'] = 'Grade is not yet submitted.';
			} else {
				if ($exam_grade_change['initiated_by_department'] == 1 || $exam_grade_change['department_approval'] == 1) {
					if ($exam_grade_change['college_approval'] == 1 || $exam_grade_change['makeup_exam_result'] != null) {
						if ($exam_grade_change['registrar_approval'] == 1) {
							$status['state'] = 'accepted';
							$status['description'] = 'Accepted';
						} else if ($exam_grade_change['registrar_approval'] == -1) {
							$status['state'] = 'rejected';
							if ($exam_grade_change['college_approval'] == 1) {
								$status['description'] = 'Accepted by both department and college but rejected by registrar.';
							} else {
								$status['description'] = 'Accepted by department but rejected by registrar.';
							}
						} else if ($exam_grade_change['registrar_approval'] == null) {
							$status['state'] = 'on-process';
							if ($exam_grade_change['college_approval'] == 1) {
								$status['description'] = 'Accepted by both department and college and waiting for registrar approval.';
							} else {
								$status['description'] = 'Accepted by department and waiting for registrar approval.';
							}
						}
					} else if ($exam_grade_change['college_approval'] == -1) {
						$status['state'] = 'rejected';
						$status['description'] = 'Accepted by department but rejected by college.';
					} else if ($exam_grade_change['college_approval'] == null) {
						$status['state'] = 'on-process';
						$status['description'] = 'Accepted by department and waiting for college approval.';
					}
				} else if ($exam_grade_change['department_approval'] == -1) {
					$status['state'] = 'rejected';
					$status['description'] = 'Rejected by the department.';
				} else if ($exam_grade_change['department_approval'] == null) {
					$status['state'] = 'on-process';
					$status['description'] = 'Waiting for department approval.';
				}
			}
		}
		return $status;
	}

	//Department grade change approval
	function getListOfGradeChangeForDepartmentApproval($col_dpt_id = null, $department = 1, $departmentIDs = array())
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		//debug($years_to_look_list);

		if (empty($col_dpt_id) && empty($departmentIDs)) {
			return array();
		}

		if (!empty($departmentIDs)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $departmentIDs,
			);
		} else if (empty($department)) {

			$department_idss = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $col_dpt_id, 'Department.active' => 1), 'fields' => array('Department.id', 'Department.id')));

			debug($department_idss);

			if (empty($department_idss)) {
				return array();
			}

			$departmentIDs = $department_idss;

			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $department_idss,
			);
			
		} else {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $col_dpt_id,
			);
		}

		debug($conditions);

		$department_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS null',
				'ExamGradeChange.department_approval IS null',
				'ExamGradeChange.manual_ng_conversion = 0',
				'ExamGradeChange.auto_ng_conversion = 0'
			),
			'contain' => array(
				'MakeupExam' => array(
					'PublishedCourse' => array(
						'Course',
						'Section' => array('Program', 'ProgramType', 'YearLevel'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array('Title', 'Position')
						),
						'conditions' => $conditions
					),
					'Student' => array(
						'conditions' => array(
							'Student.graduated' => 0
						),
					)
				),
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
		));

		$exam_grade_changes_summery = array();

		if (!empty($department_action_required_list)) {
			//debug($department_action_required_list);
			foreach ($department_action_required_list as $key => $grade_change_detail) {
				//Grade change for student course registration
				isset($grade_change_detail['MakeupExam']['Student']['id']) ? debug($grade_change_detail['MakeupExam']['Student']['graduated']) : '';
				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseRegistration']['id']) && (($department == 1 &&  isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'] == $col_dpt_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] == $col_dpt_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {

					$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
					debug($program);
					$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$program][$program_type])) {
						$exam_grade_changes_summery[$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$program][$program_type]);
					$exam_grade_changes_summery[$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
					$exam_grade_changes_summery[$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$program][$program_type][$index]['Staff'] = (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'] : array());
					$exam_grade_changes_summery[$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
					
					if (!empty($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}

				} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']) && isset($grade_change_detail['ExamGrade']['CourseAdd']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseAdd']['id']) && (($department == 1 && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'] == $col_dpt_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'] == $col_dpt_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {
					//Grade change for student course add
					$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$program][$program_type])) {
						$exam_grade_changes_summery[$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$program][$program_type]);
					$exam_grade_changes_summery[$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
					$exam_grade_changes_summery[$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
					
					if (!empty($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}
				} 
			}
		}

		//debug($exam_grade_changes_summery);
		return $exam_grade_changes_summery;
	}

	function getListOfMakeupGradeChangeForDepartmentApproval($col_dep_id = null, $registrar_rejected = 0, $department = 1, $departmentIDs = array())
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		//debug($years_to_look_list);

		/* if (!empty($departmentIDs)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $departmentIDs,
			);
		} else {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
			);
		} */

		if (empty($col_dep_id) && empty($departmentIDs)) {
			return array();
		}

		if (!empty($departmentIDs)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $departmentIDs,
			);
		} else if (empty($department)) {

			$department_idss = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $col_dep_id, 'Department.active' => 1), 'fields' => array('Department.id', 'Department.id')));

			debug($department_idss);

			if (empty($department_idss)) {
				return array();
			}

			$departmentIDs = $department_idss;

			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $department_idss,
			);
			
		} else {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $col_dep_id,
			);
		}

		debug($conditions);

		if (!$registrar_rejected) {
			
			$department_action_required_list = $this->find('all', array(
				'conditions' => array(
					'ExamGradeChange.makeup_exam_result IS NOT null',
					'ExamGradeChange.department_approval IS null',
				),
				'contain' => array(
					'MakeupExam' => array(
						'PublishedCourse' => array(
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'ExamGrade' => array(
						'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
						'CourseRegistration' => array(
							'PublishedCourse' => array(
								'Course',
								'Section' => array('Program', 'ProgramType', 'YearLevel'),
								'CourseInstructorAssignment' => array(
									'conditions' => array(
										'CourseInstructorAssignment.isprimary' => 1
									),
									'Staff' => array('Title', 'Position')
								),
								'conditions' => $conditions
							),
							'Student' => array(
								'conditions' => array(
									'Student.graduated' => 0
								),
							)
						),
						'CourseAdd' => array(
							'PublishedCourse' => array(
								'Course',
								'Section' => array('Program', 'ProgramType', 'YearLevel'),
								'CourseInstructorAssignment' => array(
									'conditions' => array(
										'CourseInstructorAssignment.isprimary' => 1
									),
									'Staff' => array('Title', 'Position')
								),
								'conditions' => $conditions
							),
							'Student' => array(
								'conditions' => array(
									'Student.graduated' => 0
								),
							)
						),
					)
				),
				'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
			));

		} else {

			$department_action_required_list = $this->find('all', array(
				'conditions' => array(
					'ExamGradeChange.makeup_exam_result IS NOT null',
					'ExamGradeChange.initiated_by_department = 0',
					'ExamGradeChange.department_approval = 1',
					'ExamGradeChange.registrar_approval = -1'
				),
				'contain' => array(
					'MakeupExam' => array(
						'PublishedCourse' => array(
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'ExamGrade' => array(
						'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
						'CourseRegistration' => array(
							'PublishedCourse' => array(
								'Course',
								'Section' => array('Program', 'ProgramType', 'YearLevel'),
								'CourseInstructorAssignment' => array(
									'conditions' => array(
										'CourseInstructorAssignment.isprimary' => 1
									),
									'Staff' => array('Title', 'Position')
								),
								'conditions' => $conditions
							),
							'Student' => array(
								'conditions' => array(
									'Student.graduated' => 0
								),
							)
						),
						'CourseAdd' => array(
							'PublishedCourse' => array(
								'Course',
								'Section' => array('Program', 'ProgramType', 'YearLevel'),
								'CourseInstructorAssignment' => array(
									'conditions' => array(
										'CourseInstructorAssignment.isprimary' => 1
									),
									'Staff' => array('Title', 'Position')
								),
								'conditions' => $conditions
							),
							'Student' => array(
								'conditions' => array(
									'Student.graduated' => 0
								),
							)
						),
					)
				),
				'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
			));

		}

		$exam_grade_changes_summery = array();
		$processed_makeup_grade_changes = array();

		if (!empty($department_action_required_list)) {
			foreach ($department_action_required_list as $key => $grade_change_detail) {

				isset($grade_change_detail['MakeupExam']['Student']['id']) ? debug($grade_change_detail['MakeupExam']['Student']['graduated']) : '';

				if (isset($grade_change_detail['MakeupExam']['Student']['id']) && $grade_change_detail['MakeupExam']['Student']['graduated'] == 0) {

					//debug($grade_change_detail['MakeupExam']['Student']['graduated']);

					$grade_change_detail2 = $this->find('first', array(
						'conditions' => array('ExamGradeChange.exam_grade_id' => $grade_change_detail['ExamGradeChange']['exam_grade_id']),
						'order' => array('ExamGradeChange.created' => 'DESC'),
						'recursive' => -1
					));

					if ($registrar_rejected == 1) {
						if ($grade_change_detail2['ExamGradeChange']['registrar_approval'] != -1 || in_array($grade_change_detail2['ExamGradeChange']['exam_grade_id'], $processed_makeup_grade_changes)) {
							continue;
						} else {
							$processed_makeup_grade_changes[] = $grade_change_detail2['ExamGradeChange']['exam_grade_id'];
						}
					}

					//Grade change for student course registration
					if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseRegistration']['id']) && (($department == 1 &&  isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'] == $col_dep_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] == $col_dep_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {
						
						$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];
						
						if (!isset($exam_grade_changes_summery[$program][$program_type])) {
							$exam_grade_changes_summery[$program][$program_type] = array();
						}
						
						$index = count($exam_grade_changes_summery[$program][$program_type]);
						$exam_grade_changes_summery[$program][$program_type][$index]['Staff'] = $grade_change_detail['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamCourse'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamSection'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$program][$program_type][$index]['MakeupExam'] = $grade_change_detail['MakeupExam'];
						
						unset($exam_grade_changes_summery[$program][$program_type][$index]['MakeupExam']['PublishedCourse']);
						
						$exam_grade_changes_summery[$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
						$exam_grade_changes_summery[$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}

					} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseAdd']['id']) && (($department == 1 && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'] == $col_dep_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'] == $col_dep_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {
						
						$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];

						if (!isset($exam_grade_changes_summery[$program][$program_type])) {
							$exam_grade_changes_summery[$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$program][$program_type]);
						$exam_grade_changes_summery[$program][$program_type][$index]['Staff'] = $grade_change_detail['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamCourse'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamSection'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$program][$program_type][$index]['MakeupExam'] = $grade_change_detail['MakeupExam'];
						
						unset($exam_grade_changes_summery[$program][$program_type][$index]['MakeupExam']['PublishedCourse']);
						
						$exam_grade_changes_summery[$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
						$exam_grade_changes_summery[$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					} 
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	function getMakeupGradesAskedByDepartmentRejectedByRegistrar($col_dep_id = null, $department = 1, $departmentIDs = array())
	{

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		//debug($years_to_look_list);

		if (!empty($departmentIDs)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $departmentIDs,
			);
		} else {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
			);
		}

		debug($conditions);

		$department_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS NOT null',
				'ExamGradeChange.initiated_by_department = 1',
				'ExamGradeChange.department_approval = 1',
				'ExamGradeChange.registrar_approval = -1'
			),
			'contain' => array(
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'College',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'College',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
		));

		$exam_grade_changes_summery = array();
		$processed_makeup_grade_changes = array();

		if (!empty($department_action_required_list)) {
			foreach ($department_action_required_list as $key => $grade_change_detail) {
				
				$grade_change_detail2 = $this->find('first', array(
					'conditions' => array('ExamGradeChange.exam_grade_id' => $grade_change_detail['ExamGradeChange']['exam_grade_id']),
					'order' => array('ExamGradeChange.created' => 'DESC'),
					'recursive' => -1
				));

				if ($grade_change_detail2['ExamGradeChange']['registrar_approval'] != -1 || in_array($grade_change_detail2['ExamGradeChange']['exam_grade_id'], $processed_makeup_grade_changes)) {
					continue;
				} else {
					$processed_makeup_grade_changes[] = $grade_change_detail2['ExamGradeChange']['exam_grade_id'];
				}

				//Grade change for student course registration
				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']['id']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']['id']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseRegistration']['id']) && (($department == 1 &&  isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'] == $col_dep_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] == $col_dep_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {
					
					$college = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['College']['name']);
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

					$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'] :  array());
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];

					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));

					if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}

				} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['id']) && !empty($grade_change_detail['ExamGrade']['CourseAdd']['id']) && isset($grade_change_detail['ExamGrade']['CourseAdd']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && is_numeric($grade_change_detail['ExamGrade']['CourseAdd']['id']) && (($department == 1 && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'] == $col_dep_id || (!empty($departmentIDs) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))) || ($department == 0  && ((isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'] == $col_dep_id) || (!empty($departmentIDs) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'], $departmentIDs)))))) {
					
					$college = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['College']['name']);
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

					$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];
					
					if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];

					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseAddLatestGradeDetail($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));

					if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	//COLLEGE
	function getListOfGradeChangeForCollegeApproval($college_id = null)
	{

		if (empty($college_id)) {
			return array();
		}

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$department_idss = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $college_id, 'Department.active' => 1), 'fields' => array('Department.id', 'Department.id')));

		debug($department_idss);

		if (empty($department_idss)) {
			return array();
		}
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		debug($years_to_look_list);

		$college_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS null',
				'ExamGradeChange.college_approval IS NULL',
				'ExamGradeChange.registrar_approval IS NULL',
				'ExamGradeChange.department_approval = 1',
			),
			'contain' => array(
				'MakeupExam' => array(
					'PublishedCourse' => array(
						'Department',
						'GivenByDepartment',
						'Course',
						'Section' => array('Program', 'ProgramType', 'YearLevel'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array('Title', 'Position')
						),
						'conditions' => array(
							'PublishedCourse.academic_year' => $years_to_look_list,
							'PublishedCourse.given_by_department_id' => $department_idss,
						)
					),
					'Student' => array(
						'conditions' => array(
							'Student.graduated' => 0
						),
					)
				),
				'ExamGrade' => array(
					'order' => array('ExamGrade.id ' => 'DESC', 'ExamGrade.created ' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Department',
							'GivenByDepartment',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
								'PublishedCourse.given_by_department_id' => $department_idss,
							)
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Department',
							'GivenByDepartment',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
								'PublishedCourse.given_by_department_id' => $department_idss,
							)
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC'),
		));

		//debug(count($college_action_required_list));

		$exam_grade_changes_summery = array();
		$countNotFound = 0;

		if (!empty($college_action_required_list)) {
			foreach ($college_action_required_list as $key => $grade_change_detail) {
				//Grade change for student course registration
				//check the given by college dean

				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'])) {
					$given_by_college_id = ClassRegistry::init('Department')->field('college_id', array('Department.id' => $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']));
				} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']) && isset($grade_change_detail['ExamGrade']['CourseAdd']['Student']['id']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'])) {
					$given_by_college_id = ClassRegistry::init('Department')->field('college_id', array('Department.id' => $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']));
				} else {
					continue;
				}

				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && ($grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && ((isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id']) && strcasecmp($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'], $college_id) == 0) || (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']) && strcasecmp($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'], $college_id) == 0) || strcasecmp($given_by_college_id, $college_id) == 0)) {
					
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

					$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$departement][$program][$program_type]);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
					
					if (!empty($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}

				} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['id']) && ($grade_change_detail['ExamGrade']['CourseAdd']['id'] != "") && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && ((isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && strcasecmp($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'], $college_id) == 0) || (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']) && strcasecmp($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'], $college_id) == 0) || strcasecmp($given_by_college_id, $college_id) == 0)) {
					
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));
					
					$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$departement][$program][$program_type]);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));

					if (!empty($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}

				} else {
					$countNotFound++;
					/* debug($college_id);
					debug($countNotFound);

					if (strcasecmp($college_id,  $given_by_college_id) == 0) {
						debug($grade_change_detail);
					} */
				}
			}
		}

		return $exam_grade_changes_summery;
	}

	function getListOfGradeChangeOnWaitingCollegeApproval($exam_grade_id = null, $college_id = null)
	{

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		debug($years_to_look_list);

		$college_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS null',
				'ExamGradeChange.college_approval IS NULL',
				'ExamGradeChange.registrar_approval IS NULL',
				'ExamGradeChange.department_approval=1',
				'ExamGradeChange.exam_grade_id' => $exam_grade_id
			),
			'contain' => array(
				'MakeupExam' => array(
					'PublishedCourse' => array(
						'Department',
						'Course',
						'Section' => array('Program', 'ProgramType', 'YearLevel'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array('Title', 'Position')
						),
						'conditions' => array(
							'PublishedCourse.academic_year' => $years_to_look_list,
						)
					)
				),
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Department',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							)
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Department',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							)
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC'),
		));

		debug(count($college_action_required_list));

		$exam_grade_changes_summery = array();
		$countNotFound = 0;

		if (!empty($college_action_required_list)) {
			foreach ($college_action_required_list as $key => $grade_change_detail) {
				//Grade change for student course registration
				//check the given by college dean

				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse'])) {
					$given_by_college_id = ClassRegistry::init('Department')->field('college_id', array('Department.id' => $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id']));
				}

				if (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse'])) {
					$given_by_college_id = ClassRegistry::init('Department')->field('college_id', array('Department.id' => $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id']));
				}

				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && ($grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") && ((isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && strcasecmp($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'], $college_id) == 0) || (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']) && strcasecmp($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'], $college_id) == 0) || strcasecmp($given_by_college_id, $college_id) == 0)) {
					
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

					$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$departement][$program][$program_type]);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
					
					if (!empty($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}
				} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']['id']) && ($grade_change_detail['ExamGrade']['CourseAdd']['id'] != "") && ((isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && strcasecmp($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'], $college_id) == 0) || (isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']) && strcasecmp($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'], $college_id) == 0) || strcasecmp($given_by_college_id, $college_id) == 0)) {
					
					$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));
					
					$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
					$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];

					if (!isset($exam_grade_changes_summery[$departement][$program][$program_type])) {
						$exam_grade_changes_summery[$departement][$program][$program_type] = array();
					}

					$index = count($exam_grade_changes_summery[$departement][$program][$program_type]);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
					$exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
					
					if (!empty($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'])) {
						foreach ($exam_grade_changes_summery[$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
							$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
							$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
						}
					}

				} else {
					$countNotFound++;
					debug($college_id);
					if (strcasecmp($college_id,  $given_by_college_id) == 0) {
						debug($grade_change_detail);
					}
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	//Registrar grade change approval
	function getListOfGradeChangeForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null) 
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		//debug($years_to_look_list);

		/* if (!empty($department_ids)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.given_by_department_id' => $department_ids,
			);
		} else if (!empty($college_ids)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.college_id' => $college_ids,
			);
		} else {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list
			);
		} */

		if (!empty($department_ids)) {
			if (!is_array($department_ids)) {
				$dpt_id = $department_ids;
				$department_ids = array();
				$department_ids[] = $dpt_id;
			}
		}

		if (!empty($college_ids)) {
			if (!is_array($college_ids)) {
				$col_id = $college_ids;
				$college_ids = array();
				$college_ids[] = $col_id;
			}
		}

		if (!empty($program_id)) {
			if (!is_array($program_id)) {
				$pr_id = $program_id;
				$program_id = array();
				$program_id[] = $pr_id;
			}
		}

		if (!empty($program_type_id)) {
			if (!is_array($program_type_id)) {
				$pt_id = $program_type_id;
				$program_type_id = array();
				$program_type_id[] = $pt_id;
			}
		}

		if (!empty($department_ids)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.program_id' => $program_id,
				'PublishedCourse.program_type_id' => $program_type_id,
				'PublishedCourse.department_id' => $department_ids,
				'PublishedCourse.year_level_id IS NOT NULL AND PublishedCourse.year_level_id != "" AND PublishedCourse.year_level_id != 0',
				//'NOT' => array('PublishedCourse.year_level_id' => array(NULL, '', 0)),
				/* 'OR' => array(
					'PublishedCourse.department_id' => $department_ids,
					'PublishedCourse.given_by_department_id' => $department_ids
				) */
			);
		} else if (!empty($college_ids)) {
			$conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.program_id' => $program_id,
				'PublishedCourse.program_type_id' => $program_type_id,
				'PublishedCourse.department_id IS NULL',
				'PublishedCourse.college_id' => $college_ids
			);
		} else {
			return array();
		}

		debug($conditions);

		$registrar_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS null',
				'ExamGradeChange.registrar_approval IS null',
				'ExamGradeChange.college_approval = 1',
				'ExamGradeChange.department_approval = 1',
				/* 'OR' => array(
					'ExamGradeChange.college_approval = 1',
					'ExamGradeChange.department_approval = 1',
				), */
				'ExamGradeChange.manual_ng_conversion = 0',
				'ExamGradeChange.auto_ng_conversion = 0'
			),
			'contain' => array(
				'MakeupExam' => array(
					'PublishedCourse' => array(
						'GivenByDepartment',
						'Department' => array('College'),
						'Course',
						'Section' => array('Program', 'ProgramType', 'YearLevel'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array('Title', 'Position')
						),
						'conditions' => $conditions
					)
				),
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'GivenByDepartment',
							'Department' => array('College'),
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'GivenByDepartment',
							'Department' => array('College'),
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							'conditions' => $conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
		));

		$exam_grade_changes_summery = array();

		if (!empty($registrar_action_required_list)) {
			foreach ($registrar_action_required_list as $key => $grade_change_detail) {
				if (isset($program_id) && !empty($program_id)) {

					if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) || isset($grade_change_detail['ExamGrade']['course_registration_id']) && !empty($grade_change_detail['ExamGrade']['course_registration_id']) ) {
						$type = 'CourseRegistration';
					} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']) && !empty($grade_change_detail['ExamGrade']['CourseAdd']) || isset($grade_change_detail['ExamGrade']['course_add_id']) && !empty($grade_change_detail['ExamGrade']['course_add_id'])) {
						$type = 'CourseAdd';
					} else {
						continue;
					}

					if (isset($type) && !empty($type)) {
						if (isset($grade_change_detail['ExamGrade'][$type]['Student']) && !$grade_change_detail['ExamGrade'][$type]['Student']['graduated']) {
							if ( isset($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_id']) && !in_array($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_id'], $program_id)) {
								continue;
							} else {
								if (isset($program_type_id) && !empty($program_type_id)) {
									if (isset($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_type_id']) && !in_array($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_type_id'], $program_type_id)) {
										continue;
									}
								}
							}
						} else {
							continue;
						}
					}
				}

				//Grade change for student course registration
				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") {
					
					if ((!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id'], $department_ids)) || (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'], $college_ids))) {
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

						$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];

						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'] : array());
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}

				} else {

					if ((!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id'], $department_ids)) || (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'], $college_ids))) {
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

						$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];

						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}
						
						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));

						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	//REGISTRAR MAKEUP
	function getListOfMakeupGradeChangeForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null ) 
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		debug($years_to_look_list);

		if (!empty($department_ids)) {
			if (!is_array($department_ids)) {
				$dpt_id = $department_ids;
				$department_ids = array();
				$department_ids[] = $dpt_id;
			}
		}

		if (!empty($college_ids)) {
			if (!is_array($college_ids)) {
				$col_id = $college_ids;
				$college_ids = array();
				$college_ids[] = $col_id;
			}
		}

		if (!empty($program_id)) {
			if (!is_array($program_id)) {
				$pr_id = $program_id;
				$program_id = array();
				$program_id[] = $pr_id;
			}
		}

		if (!empty($program_type_id)) {
			if (!is_array($program_type_id)) {
				$pt_id = $program_type_id;
				$program_type_id = array();
				$program_type_id[] = $pt_id;
			}
		}

		if (!empty($department_ids)) {
			$pc_conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.program_id' => $program_id,
				'PublishedCourse.program_type_id' => $program_type_id,
				'PublishedCourse.department_id' => $department_ids,
				'PublishedCourse.year_level_id IS NOT NULL AND PublishedCourse.year_level_id != "" AND PublishedCourse.year_level_id != 0',
				//'NOT' => array('PublishedCourse.year_level_id' => array(NULL, '', 0)),
				/* 'OR' => array(
					'PublishedCourse.department_id' => $department_ids,
					'PublishedCourse.given_by_department_id' => $department_ids
				) */
			);
		} else if (!empty($college_ids)) {
			$pc_conditions = array(
				'PublishedCourse.academic_year' => $years_to_look_list,
				'PublishedCourse.program_id' => $program_id,
				'PublishedCourse.program_type_id' => $program_type_id,
				'PublishedCourse.department_id IS NULL',
				'PublishedCourse.college_id' => $college_ids
			);
		} else {
			return array();
		}

		$department_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS NOT null',
				'ExamGradeChange.initiated_by_department = 0',
				'ExamGradeChange.department_approval = 1',
				'ExamGradeChange.registrar_approval IS NULL',
			),
			'contain' => array(
				'MakeupExam' => array(
					'PublishedCourse' => array(
						'Department' => array('College'),
						'Course',
						'Section' => array('Program', 'ProgramType', 'YearLevel'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array('Title', 'Position')
						),
						/* 'conditions' => array(
							'PublishedCourse.academic_year' => $years_to_look_list,
							'PublishedCourse.program_id' => $years_to_look_list,
						) */
						'conditions' => $pc_conditions
					),
					'Student' => array(
						'conditions' => array(
							'Student.graduated' => 0
						),
					)
				),
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							/* 'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							) */
							'conditions' => $pc_conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							/* 'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							) */
							'conditions' => $pc_conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
		));

		$exam_grade_changes_summery = array();

		if (!empty($department_action_required_list)) {
			foreach ($department_action_required_list as $key => $grade_change_detail) {
				if (isset($program_id) && !empty($program_id)) {

					if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0 && isset($grade_change_detail['ExamGrade']['course_registration_id']) && !empty($grade_change_detail['ExamGrade']['course_registration_id']) ) {
						$type = 'CourseRegistration';
					} else if (isset($grade_change_detail['ExamGrade']['CourseAdd']) && !empty($grade_change_detail['ExamGrade']['CourseAdd']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && isset($grade_change_detail['ExamGrade']['course_add_id']) && !empty($grade_change_detail['ExamGrade']['course_add_id'])) {
						$type = 'CourseAdd';
					} else {
						continue;
					}

					if (isset($type) && !empty($type)) {
						if (!in_array($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_id'], $program_id)) {
							continue;
						} else {
							if (isset($program_type_id) && !empty($program_type_id)) {
								if (!in_array($grade_change_detail['ExamGrade'][$type]['PublishedCourse']['program_type_id'], $program_type_id)) {
									continue;
								}
							}
						}
					}
				}

				//Grade change for student course registration
				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") {
					//Illegibility checking
					if ((!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id'], $department_ids)) || (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'], $college_ids))) {
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));
						
						$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];
						
						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamCourse'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamSection'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['MakeupExam'] = $grade_change_detail['MakeupExam'];
						
						unset($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['MakeupExam']['PublishedCourse']);
						
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}

				} else {
					
					if ((!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id'], $department_ids)) || (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'], $college_ids))) {
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));
						
						$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];
						
						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamCourse'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamSection'] = $grade_change_detail['MakeupExam']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['MakeupExam'] = $grade_change_detail['MakeupExam'];
						
						unset($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['MakeupExam']['PublishedCourse']);
						
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_add_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	//Makeup by registrar
	function getListOfMakeupGradeChangeByDepartmentForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null) 
	{

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		
		$currentAcademicYear = $AcademicYear->current_academicyear();
		$years_to_look_list = $AcademicYear->academicYearInArray(((explode('/', $currentAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) , (explode('/', $currentAcademicYear)[0]));

		debug($years_to_look_list);

		if (!empty($department_ids)) {
			if (!is_array($department_ids)) {
				$dpt_id = $department_ids;
				$department_ids = array();
				$department_ids[] = $dpt_id;
			}
		}

		if (!empty($college_ids)) {
			if (!is_array($college_ids)) {
				$col_id = $college_ids;
				$college_ids = array();
				$college_ids[] = $col_id;
			}
		}

		if (!empty($program_id)) {
			if (!is_array($program_id)) {
				$pr_id = $program_id;
				$program_id = array();
				$program_id[] = $pr_id;
			}
		}

		if (!empty($program_type_id)) {
			if (!is_array($program_type_id)) {
				$pt_id = $program_type_id;
				$program_type_id = array();
				$program_type_id[] = $pt_id;
			}
		}


		if (!empty($program_id) && !empty($program_type_id)) {
			if (!empty($department_ids)) {
				$pc_conditions = array(
					'PublishedCourse.academic_year' => $years_to_look_list,
					'PublishedCourse.program_id' => $program_id,
					'PublishedCourse.program_type_id' => $program_type_id,
					'PublishedCourse.department_id' => $department_ids,
					'PublishedCourse.year_level_id IS NOT NULL AND PublishedCourse.year_level_id != "" AND PublishedCourse.year_level_id != 0',
					//'NOT' => array('PublishedCourse.year_level_id' => array(NULL, '', 0)),
					/* 'OR' => array(
						'PublishedCourse.department_id' => $department_ids,
						'PublishedCourse.given_by_department_id' => $department_ids
					) */
				);
			} else if (!empty($college_ids)) {
				$pc_conditions = array(
					'PublishedCourse.academic_year' => $years_to_look_list,
					'PublishedCourse.program_id' => $program_id,
					'PublishedCourse.program_type_id' => $program_type_id,
					'PublishedCourse.department_id IS NULL',
					'PublishedCourse.college_id' => $college_ids
				);
			} else {
				return array();
			}
		} else {
			if (!empty($department_ids)) {
				$pc_conditions = array(
					'PublishedCourse.academic_year' => $years_to_look_list,
					'PublishedCourse.department_id' => $department_ids,
					'PublishedCourse.year_level_id IS NOT NULL AND PublishedCourse.year_level_id != "" AND PublishedCourse.year_level_id != 0',
					//'NOT' => array('PublishedCourse.year_level_id' => array(NULL, '', 0)),
					/* 'OR' => array(
						'PublishedCourse.department_id' => $department_ids,
						'PublishedCourse.given_by_department_id' => $department_ids
					) */
				);
			} else if (!empty($college_ids)) {
				$pc_conditions = array(
					'PublishedCourse.academic_year' => $years_to_look_list,
					'PublishedCourse.department_id IS NULL',
					'PublishedCourse.college_id' => $college_ids
				);
			} else {
				return array();
			}
		}

		$registrar_action_required_list = $this->find('all', array(
			'conditions' => array(
				'ExamGradeChange.makeup_exam_result IS NOT null',
				'ExamGradeChange.initiated_by_department = 1',
				'ExamGradeChange.registrar_approval IS NULL',
				//'ExamGradeChange.department_approval <> -1',
				'ExamGradeChange.department_approval = 1'
			),
			'contain' => array(
				'ExamGrade' => array(
					'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'College',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							/* 'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							) */
							'conditions' => $pc_conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
					'CourseAdd' => array(
						'PublishedCourse' => array(
							'Department' => array('College'),
							'College',
							'Course',
							'Section' => array('Program', 'ProgramType', 'YearLevel'),
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.isprimary' => 1
								),
								'Staff' => array('Title', 'Position')
							),
							/* 'conditions' => array(
								'PublishedCourse.academic_year' => $years_to_look_list,
							) */
							'conditions' => $pc_conditions
						),
						'Student' => array(
							'conditions' => array(
								'Student.graduated' => 0
							),
						)
					),
				)
			),
			'order' => array('ExamGradeChange.id' => 'DESC', 'ExamGradeChange.created' => 'DESC')
		));

		// debug($registrar_action_required_list);
		$exam_grade_changes_summery = array();
		$published_by_college_asked_by_department = false;

		if (!empty($registrar_action_required_list)) {
			foreach ($registrar_action_required_list as $key => $grade_change_detail) {
				//Grade change for student course registration
				if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && $grade_change_detail['ExamGrade']['CourseRegistration']['Student']['graduated'] == 0) {
					//the published course was by college by the student has department
					//now and supplementary asked by the department
					if (empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id']) && !empty($department_ids)) {
						$published_by_college_asked_by_department = true;
					} else {
						$published_by_college_asked_by_department = false;
					}

					if ((!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['id'], $department_ids)) 
						/* || $published_by_college_asked_by_department  */
						|| (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'], $college_ids))
					) {

						//  debug($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));

						$program = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section']['ProgramType']['name'];
						
						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = (isset($grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) ? $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'] : array());
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Section'];

						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory($grade_change_detail['ExamGrade']['CourseRegistration']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseRegistration']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}
				} else {

					if (isset($grade_change_detail['ExamGrade']['CourseAdd']['Student']) && $grade_change_detail['ExamGrade']['CourseAdd']['Student']['graduated'] == 0 && (!empty($department_ids) && isset($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['id'], $department_ids)) 
						|| (!empty($college_ids) && !empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id']) && in_array($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'], $college_ids))
					) {
						
						$college = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['College']['name'] : $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['College']['name']);
						$departement = (!empty($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name']) ? $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']['name'] : ($grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program' : 'Freshman Program'));
						
						$program = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['Program']['name'];
						$program_type = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section']['ProgramType']['name'];
						
						if (!isset($exam_grade_changes_summery[$college][$departement][$program][$program_type])) {
							$exam_grade_changes_summery[$college][$departement][$program][$program_type] = array();
						}

						$index = count($exam_grade_changes_summery[$college][$departement][$program][$program_type]);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Staff'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Section'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Section'];

						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Student'] = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['Course'] = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Course'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['latest_grade'] = $this->ExamGrade->CourseAdd->getCourseRegistrationLatestGrade($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeChange'] = $grade_change_detail['ExamGradeChange'];
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGradeHistory'] = $this->ExamGrade->CourseAdd->getCourseAddGradeHistory($grade_change_detail['ExamGrade']['CourseAdd']['id']);
						$exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] = $this->ExamGrade->find('all', array('conditions' => array('ExamGrade.course_registration_id 	' => $grade_change_detail['ExamGrade']['CourseAdd']['id']), 'recursive' => -1, 'order' => array('ExamGrade.created DESC')));
						
						if (!empty($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'])) {
							foreach ($exam_grade_changes_summery[$college][$departement][$program][$program_type][$index]['ExamGrade'] as $eg_key => &$exam_grade_detail) {
								$exam_grade_detail['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['department_approved_by']));
								$exam_grade_detail['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $exam_grade_detail['ExamGrade']['registrar_approved_by']));
							}
						}
					}
				}
			}
		}
		return $exam_grade_changes_summery;
	}

	function applyManualNgConversion($exam_grade_changes = null, $minute_number = null, $login_user = null, $privilaged_registrar = array(), $converted_by_full_name = '')
	{
		$new_exam_grade = array();

		if (!empty($exam_grade_changes)) {
			foreach ($exam_grade_changes as $key => $exam_grade_change) {

				debug($exam_grade_change['grade_id']);
				debug($exam_grade_change['id']);

				$exam_grade_change_detail = $this->ExamGrade->find('first', array(
					'conditions' => array(
						'ExamGrade.id' => (isset($exam_grade_change['grade_id']) ? $exam_grade_change['grade_id'] : $exam_grade_change['id'])
					),
					'contain' => array(
						'CourseRegistration' => array(
							'PublishedCourse'
						),
						'CourseAdd' => array(
							'PublishedCourse'
						),
					),
					'order' => array('ExamGrade.id' => 'ASC')
				));

				//debug($exam_grade_change_detail['ExamGrade']['id']);

				$grade = array();

				if (isset($exam_grade_change_detail['CourseRegistration']) && !empty($exam_grade_change_detail['CourseRegistration']) && is_numeric($exam_grade_change_detail['CourseRegistration']['id']) && $exam_grade_change_detail['CourseRegistration']['id'] > 0) {
					$grade = $this->ExamGrade->getApprovedGrade($exam_grade_change_detail['CourseRegistration']['id'], 1);
				} else if (isset($exam_grade_change_detail['CourseAdd']) && !empty($exam_grade_change_detail['CourseAdd']) && is_numeric($exam_grade_change_detail['CourseAdd']['id']) && $exam_grade_change_detail['CourseAdd']['id'] > 0) {
					$grade = $this->ExamGrade->getApprovedGrade($exam_grade_change_detail['CourseAdd']['id'], 0);
				}

				if (!empty($exam_grade_change_detail) && !empty($grade['grade']) && strcasecmp($grade['grade'], 'NG') == 0 && isset($exam_grade_change['grade']) && !empty($exam_grade_change['grade']) && isset($exam_grade_change['grade_id']) && $exam_grade_change['grade_id'] == $exam_grade_change_detail['ExamGrade']['id']) {
					
					$index = count($new_exam_grade);

					$new_exam_grade[$index]['exam_grade_id'] = $exam_grade_change['id'];
					$new_exam_grade[$index]['minute_number'] = $minute_number;
					$new_exam_grade[$index]['grade'] = $exam_grade_change['grade'];
					$new_exam_grade[$index]['cheating'] = $exam_grade_change['cheating'];
					$new_exam_grade[$index]['reason'] = 'Manual NG Conversion';
					$new_exam_grade[$index]['department_reason'] = '';
					$new_exam_grade[$index]['college_reason'] = '';
					$new_exam_grade[$index]['registrar_reason'] = '';
					$new_exam_grade[$index]['manual_ng_conversion'] = 1;
					$new_exam_grade[$index]['manual_ng_converted_by'] = $login_user;
					$new_exam_grade[$index]['department_approved_by'] = $login_user;
					$new_exam_grade[$index]['registrar_approved_by'] = $login_user;
					$new_exam_grade[$index]['college_approved_by'] = $login_user;
					

					if (isset($exam_grade_change_detail['CourseRegistration']) && !empty($exam_grade_change_detail['CourseRegistration']) && $exam_grade_change_detail['CourseRegistration']['id'] != "") {
						$new_exam_grade[$index]['p_c_id'] = $exam_grade_change_detail['CourseRegistration']['PublishedCourse']['id'];
						$new_exam_grade[$index]['stdnt_id'] = $exam_grade_change_detail['CourseRegistration']['student_id'];
					} else {
						$new_exam_grade[$index]['p_c_id'] = $exam_grade_change_detail['CourseAdd']['PublishedCourse']['id'];
						$new_exam_grade[$index]['stdnt_id'] = $exam_grade_change_detail['CourseAdd']['student_id'];
					}
				}
				//debug($grade);
			}
		}

		//debug($new_exam_grade);

		//exit();

		if (!empty($new_exam_grade) && ($this->saveAll($new_exam_grade, array('validate' => false)))) {
			foreach ($new_exam_grade as $key => $value) {
				/* if (strcasecmp($value['grade'], 'I') == 0) {
					$this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($value['p_c_id']);
				} */
				if (!empty($value['stdnt_id'])) {
					if ($this->ExamGrade->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id($value['stdnt_id']) == 3) {
						$this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($value['p_c_id']);
					}
				} else {
					$this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($value['p_c_id']);
				}
			}
			ClassRegistry::init('AutoMessage')->sendNotificationOnAutoAndManualGradeChange($new_exam_grade, $privilaged_registrar, $use_the_new_format = 1, $converted_by_full_name);
			return true;
		} else {
			return false;
		}
	}

	function autoNgAndDoConversion($privilaged_registrar, $excludeYearLevel = array(), $program_id = null, $program_type_id = null) 
	{
		//Make the date before 4 months and after N days
		//debug($days_available_for_ng_to_f);
		//debug($days_available_for_do_to_f);
		//DO to F: The counting start from the date the student get DO grade
		//NG to F: The counting start from the date the student get NG
		//To avoid data traffic, retrieve all NG which are created before N days but not older than 4 months.
		//To avoid data traffic, retrieve all Do which are created before N days but not older than 4 months.
		//Do filtering out is also not yet done

		//NG grades which has been more than N days

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$currentAcademicCalendar = $AcademicYear->current_academicyear();

		$days_available_for_ng_to_f = ClassRegistry::init('AcademicCalendar')->daysAvaiableForNgToF($program_id, $program_type_id);

		$days_available_for_do_to_f = ClassRegistry::init('AcademicCalendar')->daysAvaiableForDoToF($program_id, $program_type_id);

		$days_available_for_fx_to_f = ClassRegistry::init('AcademicCalendar')->daysAvailableForFxToF($program_id, $program_type_id);
		
		$academicCalendarDetail = ClassRegistry::init('AcademicCalendar')->getAcademicCalender($currentAcademicCalendar);

		//debug($academicCalendarDetail);
		//die;

		if (!empty($academicCalendarDetail)) {
			foreach ($academicCalendarDetail as $ack => $calendardetail) {
				$grades_before = '';
				$auto_grade_change = array();
				$previousAcademicYear = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($calendardetail['calendarDetail']['AcademicCalendar']['academic_year'], $calendardetail['calendarDetail']['AcademicCalendar']['semester']);
				$courseRegStartDate = $calendardetail['calendarDetail']['AcademicCalendar']['course_registration_start_date'];
				$courseRegStartDate = '';

				if (isset($courseRegStartDate) && !empty($courseRegStartDate)) {
					$ng_to_f_change_deadline = date('Y-m-d', strtotime($courseRegStartDate . ' + ' . $days_available_for_ng_to_f . ' days'));
				} else {
					$courseRegStartDate = date('Y-m-d');
				}

				$ng_to_f_change_deadline = date('Y-m-d', strtotime($courseRegStartDate . ' + ' . $days_available_for_ng_to_f . ' days'));
				$fx_to_f_change_deadline = date('Y-m-d', strtotime($courseRegStartDate . ' + ' . $days_available_for_fx_to_f . ' days'));
				$makeup_ng_to_f_change_deadline = date('Y-m-d', strtotime($courseRegStartDate . ' + ' . $days_available_for_fx_to_f . ' days'));
				$departmentId = ClassRegistry::init('Department')->field('id', array('Department.name' => $calendardetail['departmentname']));


				if (isset($previousAcademicYear['academic_year']) && !empty($previousAcademicYear['semester']) && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && isset($departmentId) && !empty($departmentId)  && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_id'])) {
					$regListIds = ClassRegistry::init('CourseRegistration')->find('list', array(
						'conditions' => array(
							'CourseRegistration.academic_year' => $previousAcademicYear['academic_year'],
							'CourseRegistration.semester' => $previousAcademicYear['semester'],

							'CourseRegistration.published_course_id in (select id from published_courses where program_type_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_type_id'] . ' and program_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_id'] . ' and department_id=' . $departmentId . ' )',

							'CourseRegistration.id in (select course_registration_id from exam_grades where course_registration_id is not null and registrar_approval=1 and department_approval=1 and (grade="NG" OR grade="Fx") )'
						),
						'fields' => array('CourseRegistration.id', 'CourseRegistration.id')
					));
				}

				if (isset($previousAcademicYear['academic_year']) && !empty($previousAcademicYear['semester']) && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && isset($departmentId) && !empty($departmentId)  && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_id'])) {
					$addListIds = ClassRegistry::init('CourseAdd')->find('list', array(
						'conditions' => array(
							'CourseAdd.academic_year' => $previousAcademicYear['academic_year'],
							'CourseAdd.semester' => $previousAcademicYear['semester'],
							'CourseAdd.published_course_id in (select id from published_courses where program_type_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_type_id'] . ' and program_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_id'] . ' and department_id=' . $departmentId . ' )',
							'CourseAdd.id in (select course_add_id from exam_grades where course_add_id is not null and  registrar_approval=1 and department_approval=1 and (grade="NG" OR grade="Fx" ))'
						),
						'fields' => array('CourseAdd.id', 'CourseAdd.id')
					));
				}

				if (isset($previousAcademicYear['academic_year']) && !empty($previousAcademicYear['semester']) && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_type_id']) && isset($departmentId) && !empty($departmentId)  && isset($calendardetail['calendarDetail']['AcademicCalendar']['program_id']) && !empty($calendardetail['calendarDetail']['AcademicCalendar']['program_id'])) {
					$makeListIds = ClassRegistry::init('MakeupExam')->find('list', array(
							'conditions' => array(
								'MakeupExam.published_course_id in (select id from published_courses where program_type_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_type_id'] . ' and program_id=' . $calendardetail['calendarDetail']['AcademicCalendar']['program_id'] . ' and department_id=' . $departmentId . ' )',
							),
							'fields' => array('MakeupExam.id', 'MakeupExam.id')
						)
					);
				}

				//It is for course registration and add
				if ((isset($regListIds) && !empty($regListIds)) || (isset($addListIds) &&  !empty($addListIds))) {
					$ng_grades = $this->ExamGrade->find('all', array(
						'conditions' => array(
							'ExamGrade.grade' => 'NG',
							'ExamGrade.registrar_approval = 1',
							'ExamGrade.department_approval = 1',
							'OR' => array(
								'ExamGrade.course_registration_id' => $regListIds,
								'ExamGrade.course_add_id' => $addListIds
							),
							'ExamGrade.id not in (select exam_grade_id from exam_grade_changes where exam_grade_id is not null )',
						),
						'contain' => array(
							'CourseRegistration' => array('YearLevel'),
							'CourseAdd' => array('YearLevel')
						)
					));
				}

				if ((isset($regListIds) && !empty($regListIds)) || (isset($addListIds) &&  !empty($addListIds))) {
					$fx_grades = $this->ExamGrade->find('all', array(
						'conditions' => array(
							'ExamGrade.grade' => 'Fx',
							'ExamGrade.registrar_approval = 1',
							'ExamGrade.department_approval = 1',
							'OR' => array(
								'ExamGrade.course_registration_id' => $regListIds,
								'ExamGrade.course_add_id' => $addListIds
							),
							'ExamGrade.id not in (select exam_grade_id from exam_grade_changes where exam_grade_id is not null )',
						),

						'contain' => array(
							'CourseRegistration' => array('YearLevel'),
							'CourseAdd' => array('YearLevel')
						)
					));
				}


				//It is if there is a makeup exam with NG
				if (isset($makeListIds) && !empty($makeListIds)) {
					$ng_grade_changes = $this->find('all', array(
						'conditions' => array(
							'ExamGradeChange.grade' => 'NG',
							'ExamGradeChange.makeup_exam_result IS NOT NULL',
							'ExamGradeChange.registrar_approval = 1',
							'ExamGradeChange.department_approval = 1',
							'ExamGradeChange.makeup_exam_id' => $makeListIds
						),
						'contain' => array('ExamGrade' => array(
							'CourseRegistration' => array('YearLevel'),
							'CourseAdd' => array('YearLevel')
						))
					));
				}

				//If there is grade change which is to DO
				/* $do_grade_changes = $this->find('all', array(
					'conditions' => array(
						'ExamGradeChange.grade' => 'DO',
						'ExamGradeChange.created < \'' . $do_to_f_change_deadline_from . '\'',
						'ExamGradeChange.created > \'' . $do_to_f_change_deadline_to . '\'',
					),
					'contain' => array(
						'ExamGrade' => array(
							'CourseRegistration' => array(
								'PublishedCourse',
								'YearLevel'
							),
							'CourseAdd' => array(
								'PublishedCourse',
								'YearLevel'
							)
						)
					)
				)); */
					
				//If the grade is NG and not yet converted after the given time, then change it to F

				if (!empty($ng_grades)) {
					foreach ($ng_grades as $key => $ng_grade) {
						if (date('Y-m-d H:i:s') < $ng_to_f_change_deadline) {
							continue;
						}

						if (isset($ng_grade['CourseRegistration']) && !empty($ng_grade['CourseRegistration']) && $ng_grade['CourseRegistration']['id'] != "") {
							$recent_grade = $this->ExamGrade->getApprovedGrade($ng_grade['CourseRegistration']['id'], 1);
						} else {
							$recent_grade = $this->ExamGrade->getApprovedGrade($ng_grade['CourseAdd']['id'], 0);
						}

						$include = true;

						if (isset($ng_grade['CourseRegistration']['YearLevel']['name']) && !empty($ng_grade['CourseRegistration']['YearLevel']['name'])) {
							if (in_array($ng_grade['CourseRegistration']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						} else if (isset($ng_grade['CourseAdd']['YearLevel']['name']) && !empty($ng_grade['CourseAdd']['YearLevel']['name'])) {
							if (in_array($ng_grade['CourseAdd']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						}

						if (strcasecmp($recent_grade['grade'], 'NG') == 0 && $include) {
							//Apply the auto change here
							$index = count($auto_grade_change);

							if (isset($ng_grade['CourseRegistration']) && !empty($ng_grade['CourseRegistration']) && $ng_grade['CourseRegistration']['id'] != "") {
								$auto_grade_change[$index]['reg_or_add_id'] = $ng_grade['CourseRegistration']['id'];
								$auto_grade_change[$index]['is_add'] = 0;
							} else {
								$auto_grade_change[$index]['reg_or_add_id'] = $ng_grade['CourseAdd']['id'];
								$auto_grade_change[$index]['is_add'] = 1;
							}

							$auto_grade_change[$index]['exam_grade_id'] = $ng_grade['ExamGrade']['id'];
							$auto_grade_change[$index]['grade'] = 'F';
							$auto_grade_change[$index]['auto_ng_conversion'] = 1;
							//debug($recent_grade);
						}
					}
				}

				//Makeup exams with NG
				if (!empty($ng_grade_changes)) {
					foreach ($ng_grade_changes as $key => $ng_grade_change) {

						if (date('Y-m-d H:i:s') < $makeup_ng_to_f_change_deadline) {
							continue;
						}

						if (isset($ng_grade_change['ExamGrade']['CourseRegistration']) && !empty($ng_grade_change['ExamGrade']['CourseRegistration']) && $ng_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
							$recent_grade = $this->ExamGrade->getApprovedGrade($ng_grade_change['ExamGrade']['CourseRegistration']['id'], 1);
						} else {
							$recent_grade = $this->ExamGrade->getApprovedGrade($ng_grade_change['ExamGrade']['CourseAdd']['id'], 0);
						}

						$include = true;

						if (isset($ng_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name']) && !empty($ng_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'])) {
							if (in_array($ng_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						} else if (isset($ng_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name']) && !empty($ng_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'])) {
							if (in_array($ng_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						}

						if (strcasecmp($recent_grade['grade'], 'NG') == 0  && $include) {
							//Apply the auto change here for makeup exam
							$index = count($auto_grade_change);
							
							if (isset($ng_grade_change['ExamGrade']['CourseRegistration']) && !empty($ng_grade_change['ExamGrade']['CourseRegistration']) && $ng_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
								$auto_grade_change[$index]['reg_or_add_id'] = $ng_grade_change['ExamGrade']['CourseRegistration']['id'];
								$auto_grade_change[$index]['is_add'] = 0;
							} else {
								$auto_grade_change[$index]['reg_or_add_id'] = $ng_grade_change['ExamGrade']['CourseAdd']['id'];
								$auto_grade_change[$index]['is_add'] = 1;
							}

							$auto_grade_change[$index]['exam_grade_id'] = $ng_grade_change['ExamGradeChange']['exam_grade_id'];
							$auto_grade_change[$index]['grade'] = 'F';
							$auto_grade_change[$index]['auto_ng_conversion'] = 1;
							//debug($recent_grade);
						}
					}
				}

				//If the grade is Fx and not yet converted after the given time, then change it to F
				if (!empty($fx_grades)) {
					foreach ($fx_grades as $key => $fx_grade) {
						if (date('Y-m-d H:i:s') < $fx_to_f_change_deadline) {
							continue;
						}

						//skip those who applied in
						if (isset($fx_grade['CourseRegistration']) && $fx_grade['CourseRegistration']['id'] != "") {
							$applied = ClassRegistry::init('FxResitRequest')->doesStudentAppliedFxSit($fx_grade['CourseRegistration']['id'], 1);
							
							$fxDeadline = ClassRegistry::init('AcademicCalendar')->isFxConversionDate(
								$fx_grade['CourseRegistration']['academic_year'],
								$fx_grade['PublishedCourse']['department_id'],
								$fx_grade['PublishedCourse']
							);

						} else {
							$applied = ClassRegistry::init('FxResitRequest')->doesStudentAppliedFxSit($fx_grade['CourseAdd']['id'], 0);
							$fxDeadline = ClassRegistry::init('AcademicCalendar')->isFxConversionDate($fx_grade['CourseAdd']['academic_year'], $fx_grade['PublishedCourse']['department_id']);
						}

						$gradeChangeOnProgress = $this->getListOfGradeChangeOnWaitingCollegeApproval($fx_grade['ExamGrade']['id']);
						debug($gradeChangeOnProgress);
						
						if (isset($gradeChangeOnProgress) && !empty($gradeChangeOnProgress)) {
							debug($gradeChangeOnProgress);
							echo 'In Progress';
						}

						if ($applied || !$fxDeadline) {
							continue;
						}

						if (isset($fx_grade['CourseRegistration']) && !empty($fx_grade['CourseRegistration']) && $fx_grade['CourseRegistration']['id'] != "") {
							$recent_grade = $this->ExamGrade->getApprovedGrade($fx_grade['CourseRegistration']['id'], 1);
						} else {
							$recent_grade = $this->ExamGrade->getApprovedGrade($fx_grade['CourseAdd']['id'], 0);
						}

						$include = true;

						if (isset($fx_grade['CourseRegistration']['YearLevel']['name']) && !empty($fx_grade['CourseRegistration']['YearLevel']['name'])) {
							if (in_array($fx_grade['CourseRegistration']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						} else if (isset($fx_grade['CourseAdd']['YearLevel']['name']) && !empty($fx_grade['CourseAdd']['YearLevel']['name'])) {
							if (in_array($fx_grade['CourseAdd']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						}

						if (strcasecmp($recent_grade['grade'], 'Fx') == 0 && $include) {
							//Apply the auto change here
							$index = count($auto_grade_change);

							if (isset($fx_grade['CourseRegistration']) && !empty($fx_grade['CourseRegistration']) && $fx_grade['CourseRegistration']['id'] != "") {
								$auto_grade_change[$index]['reg_or_add_id'] = $fx_grade['CourseRegistration']['id'];
								$auto_grade_change[$index]['is_add'] = 0;
							} else {
								$auto_grade_change[$index]['reg_or_add_id'] = $fx_grade['CourseAdd']['id'];
								$auto_grade_change[$index]['is_add'] = 1;
							}

							$auto_grade_change[$index]['exam_grade_id'] = $fx_grade['ExamGrade']['id'];
							$auto_grade_change[$index]['grade'] = 'F';
							$auto_grade_change[$index]['auto_ng_conversion'] = 1;
						}
					}
				}

				//Makeup exams with Fx
				/* if (!empty($fx_grade_changes)) {
					foreach ($fx_grade_changes as $key => $fx_grade_change) {
						if (date('Y-m-d H:i:s') < $fx_to_f_change_deadline) {
							continue;
						}

						if (isset($fx_grade_change['ExamGrade']['CourseRegistration']) && !empty($fx_grade_change['ExamGrade']['CourseRegistration']) && $fx_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
							$recent_grade = $this->ExamGrade->getApprovedGrade($fx_grade_change['ExamGrade']['CourseRegistration']['id'], 1);
						} else {
							$recent_grade = $this->ExamGrade->getApprovedGrade($fx_grade_change['ExamGrade']['CourseAdd']['id'], 0);
						}

						$include = true;

						if (isset($fx_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name']) && !empty($fx_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'])) {
							if (in_array($fx_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						} else if (isset($fx_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name']) && !empty($fx_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'])) {
							if (in_array($fx_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						}

						if (strcasecmp($recent_grade['grade'], 'Fx') == 0  && $include) {
							//Apply the auto change here for makeup exam
							
							$index = count($auto_grade_change);

							if (isset($fx_grade_change['ExamGrade']['CourseRegistration']) && !empty($fx_grade_change['ExamGrade']['CourseRegistration']) && $fx_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
								$auto_grade_change[$index]['reg_or_add_id'] = $fx_grade_change['ExamGrade']['CourseRegistration']['id'];
								$auto_grade_change[$index]['is_add'] = 0;
							} else {
								$auto_grade_change[$index]['reg_or_add_id'] = $fx_grade_change['ExamGrade']['CourseAdd']['id'];
								$auto_grade_change[$index]['is_add'] = 1;
							}

							$auto_grade_change[$index]['exam_grade_id'] = $fx_grade_change['ExamGradeChange']['exam_grade_id'];
							$auto_grade_change[$index]['grade'] = 'F';
							$auto_grade_change[$index]['auto_ng_conversion'] = 1;
							//debug($recent_grade);
						}
					}
				} */

				//Grades which are changed to DO
				/* if (!empty($do_grade_changes)) {
					foreach ($do_grade_changes as $key => $do_grade_change) {
						if (isset($do_grade_change['ExamGrade']['CourseRegistration']) && !empty($do_grade_change['ExamGrade']['CourseRegistration']) && $do_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
							$recent_grade = $this->ExamGrade->getApprovedGrade($do_grade_change['ExamGrade']['CourseRegistration']['id'], 1);
						} else {
							$recent_grade = $this->ExamGrade->getApprovedGrade($do_grade_change['ExamGrade']['CourseAdd']['id'], 0);
						}

						$include = true;

						if (isset($do_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name']) && !empty($do_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'])) {
							if (in_array($do_grade_change['ExamGrade']['CourseRegistration']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						} else if (isset($do_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name']) && !empty($do_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'])) {
							if (in_array($do_grade_change['ExamGrade']['CourseAdd']['YearLevel']['name'], $excludeYearLevel)) {
								$include = false;
							}
						}

						if (strcasecmp($recent_grade['grade'], 'DO') == 0 && $include) {
							
							$index = count($auto_grade_change);
							
							if (isset($do_grade_change['ExamGrade']['CourseRegistration']) && !empty($do_grade_change['ExamGrade']['CourseRegistration']) && $do_grade_change['ExamGrade']['CourseRegistration']['id'] != "") {
								$auto_grade_change[$index]['reg_or_add_id'] = $do_grade_change['ExamGrade']['CourseRegistration']['id'];
								$auto_grade_change[$index]['is_add'] = 0;
								$auto_grade_change[$index]['p_c_id'] = $do_grade_change['ExamGrade']['CourseRegistration']['PublishedCourse']['id'];
							} else {
								$auto_grade_change[$index]['reg_or_add_id'] = $do_grade_change['ExamGrade']['CourseAdd']['id'];
								$auto_grade_change[$index]['is_add'] = 1;
								$auto_grade_change[$index]['p_c_id'] = $do_grade_change['ExamGrade']['CourseAdd']['PublishedCourse']['id'];
							}

							$auto_grade_change[$index]['exam_grade_id'] = $do_grade_change['ExamGradeChange']['exam_grade_id'];
							$auto_grade_change[$index]['grade'] = 'F';
							$auto_grade_change[$index]['auto_ng_conversion'] = 1;
							//debug($recent_grade);
						}
					}
				} */

				$conversion_sucess = null;

				if (!empty($auto_grade_change) && ($this->saveAll($auto_grade_change, array('validate' => false)))) {
					$conversion_sucess = true;
				} else {
					$conversion_sucess = false;
				}

				if ($conversion_sucess == true) {
					foreach ($auto_grade_change as $key => $value) {
						if ($value['p_c_id']) {
							//$this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($value['p_c_id']);
						}
					}
					ClassRegistry::init('AutoMessage')->sendNotificationOnAutoAndManualGradeChange($auto_grade_change, $privilaged_registrar);
				}

				debug($conversion_sucess);
				//debug($fx_grade_changes);
				debug($previousAcademicYear);
			}
		}
	}

	function getGradeChangeStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null) 
	{
		$registrationOptions = array();
		$addOptions = array();
	        
		//$registrationOptions['conditions'][] = 'PublishedCourse.id  IN (SELECT published_course_id FROM course_registrations as cr where cr.academic_year = "' . $acadamic_year.'" and cr.semester = "'.$semester.'" and cr.id in (select course_registration_id from exam_grades as eg where eg.course_registration_id is not null and eg.id in (select exam_grade_id from exam_grade_changes as egc where egc.exam_grade_id is not null and egc.department_approval = 1 and egc.college_approval = 1 and egc.registrar_approval = 1)))';
		//debug($registrationOptions);

		$registrationOptions['conditions'][] = 'PublishedCourse.id  IN (SELECT published_course_id FROM course_registrations as cr where cr.academic_year="' . $acadamic_year . '" and cr.semester="' . $semester . '" )';

		if (isset($acadamic_year) && isset($semester)) {
			$registrationOptions['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
			$registrationOptions['conditions']['PublishedCourse.semester'] = $semester;
		}

		if ($program_type_id != 0 && !empty($program_type_id)) {
			$registrationOptions['conditions']['PublishedCourse.program_type_id'] = $program_type_id;
		}

		if ($program_id != 0 && !empty($program_id)) {
			$registrationOptions['conditions']['PublishedCourse.program_id'] = $program_id;
		}

		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departmentList = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $college_id), 'fields' => array('id')));
				$registrationOptions['conditions']['PublishedCourse.given_by_department_id'] = $departmentList;
			} else {
				$registrationOptions['conditions']['PublishedCourse.given_by_department_id'] = $department_id;
			}
		}

		$registrationOptions['contain'] = array(
			'Department' => array(
				'fields' => array('id', 'name')
			),
			'College' => array(
				'fields' => array('id', 'name')
			),
			'Program' => array(
				'fields' => array('id', 'name')
			),
			'CourseRegistration' => array(
				'ExamGrade' => array('ExamGradeChange')
			),
			'ProgramType' => array(
				'fields' => array('id', 'name')
			),
		);
		//debug($registrationOptions);
		$registration = ClassRegistry::init('PublishedCourse')->find('all', $registrationOptions);

		return $registration;
	}


	function getInstGradeChangeStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null) 
	{
		$query = "";
		$published_ids = array();
		$options = array();

		/* if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$query .= ' and ps.college_id=' . $college_id[1] . '';
			} else {
				$query .= ' and ps.department_id=' . $department_id . '';
			}
		} */
          
		if (isset($department_id) && !empty($department_id)) {
			
			$college_id = explode('~', $department_id);
			
			if (count($college_id) > 1) {
				$department_ids = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $college_id[1], 'Department.active' => 1), 'fields' => array('id', 'id')));
				$query .= ' and ps.department_id in (' . join(',', $department_ids) . ')';
			} else {
				$query .= ' and ps.department_id=' . $department_id . '';
			}

			if (isset($year_level_id) && !empty($year_level_id) && count($college_id) > 1) {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id in (select id from departments where college_id="' . $college_id[1] . '"', 'YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
				$query .= ' and ps.year_level_id  (' . join(',', $yearLevels) . ')';
			} else if (isset($year_level_id) && !empty($year_level_id)) {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array(
					'conditions' => array('YearLevel.department_id' => $department_id, 'YearLevel.name' => $year_level_id),
					'fields' => array('id', 'id')
				));
				$query .= ' and ps.year_level_id  (' . join(',', $yearLevels) . ')';
			}
		}

		if (isset($year_level_id) && !empty($year_level_id) && empty($department_id)) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
			$yearLevels[0] = 0;
			$query .= ' and ps.year_level_id  (' . join(',', $yearLevels) . ')';
		}

		if (isset($program_id) && !empty($program_id)) {
			
			$program_ids = explode('~', $program_id);
			
			if (count($program_ids) > 1) {
				$query .= ' and ps.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and ps.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			
			$program_type_ids = explode('~', $program_type_id);
			
			if (count($program_type_ids) > 1) {
				$query .= ' and ps.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and ps.program_type_id=' . $program_type_id . '';
			}
		}


		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$options['conditions']['CourseInstructorAssignment.academic_year'] = $acadamic_year;
			// $query .= ' and ps.academic_year="'.$acadamic_year.'"';
			$query .= ' and cr.academic_year="' . $acadamic_year . '"';
		}

		if (isset($semester) && !empty($semester)) {
			$options['conditions']['CourseInstructorAssignment.semester'] = $semester;
			// $query .= ' and ps.semester="'.$acadamic_year.'"';
			$query .= ' and cr.semester="' . $semester . '"';
		}

		$options['contain'] = array(
			'PublishedCourse' => array(
				'Course' => array(
					'fields' => array(
						'id',
						'course_title',
						'course_code',
						'credit'
					)
				),
				'Section' => array(
					'fields' => array(
						'id',
						'name'
					)
				),
				'YearLevel' => array(
					'fields' => array(
						'id',
						'name'
					)
				),
				'Program' => array(
					'fields' => array(
						'id',
						'name'
					)
				),
				'ProgramType' => array(
					'fields' => array(
						'id',
						'name'
					)
				),

			),
			'Staff' => array('Position', 'Title', 'Department', 'College')
		);


		$gradeChangeStat = "SELECT eg.id, ps.id, ps.course_id FROM  `exam_grade_changes` AS ch, exam_grades AS eg, course_registrations AS cr, published_courses AS ps
		WHERE ch.exam_grade_id = eg.id AND cr.published_course_id = ps.id AND ps.id IN ( SELECT published_course_id FROM course_instructor_assignments ) AND cr.id = eg.course_registration_id AND ch.registrar_approval = 1 $query ";

		$gradeChangeStatResult = $this->query($gradeChangeStat);

		if (!empty($gradeChangeStatResult)) {
			
			foreach ($gradeChangeStatResult as $k => $value) {
				$published_ids[] = $value['ps']['id'];
			}

			$options['order'] = array('CourseInstructorAssignment.academic_year' => 'DESC');
			$options['conditions']['CourseInstructorAssignment.published_course_id'] = $published_ids;
			$instructors = ClassRegistry::init('CourseInstructorAssignment')->find('all', $options);

			$formattedInstructorList = array();

			if (!empty($instructors)) {
				foreach ($instructors as $key => &$inst) {
					$inst['PublishedCourse']['numberofgradechange'] = $this->getNumberofGradeChange($inst['PublishedCourse']['id']);
					$formattedInstructorList[$inst['Staff']['Department']['name'] . '~' . $inst['PublishedCourse']['Program']['name'] . '~' . $inst['PublishedCourse']['ProgramType']['name']][$inst['Staff']['id']][] = $inst;
				}
			}

			return $formattedInstructorList;
		}

		return array();
		
	}

	function getNumberofGradeChange($publishedCourseId)
	{
		$registeredLists = ClassRegistry::init('CourseRegistration')->find('list', array(
			'conditions' => array('CourseRegistration.published_course_id' => $publishedCourseId),
			'fields' => array('CourseRegistration.id', 'CourseRegistration.id')
		));

		$addedList = ClassRegistry::init('CourseAdd')->find('list', array(
			'conditions' => array('CourseAdd.published_course_id' => $publishedCourseId),
			'fields' => array('CourseAdd.id', 'CourseAdd.id')
		));

		if (count($registeredLists) && count($addedList)) {
			$examGradeChange = $this->find('count', array('conditions' => array('ExamGradeChange.exam_grade_id in (select id from exam_grades where course_registration_id in (' . join(',', $registeredLists) . ') or course_add_id in (' . join(',', $addedList) . ') )', 'ExamGradeChange.registrar_approval' => 1)));
		} else if (count($registeredLists)) {
			$examGradeChange = $this->find('count', array('conditions' => array('ExamGradeChange.exam_grade_id in (select id from exam_grades where course_registration_id in (' . join(',', $registeredLists) . ') )', 'ExamGradeChange.registrar_approval' => 1)));
		} else {
			$examGradeChange = 0;
		}

		return $examGradeChange;
	}

	function applyManualFxConversion($exam_grade_changes = null, $minute_number = null, $login_user = null, $privilaged_registrar)
	{
		$new_exam_grade = array();

		if (!empty($exam_grade_changes)) {
			foreach ($exam_grade_changes as $key => $exam_grade_change) {
				$exam_grade_change_detail = $this->ExamGrade->find('first', array(
					'conditions' => array(
						'ExamGrade.id' => $exam_grade_change['id']
					),
					'contain' => array(
						'CourseRegistration' => array(
							'PublishedCourse'
						),
						'CourseAdd' => array(
							'PublishedCourse'
						),
					)
				));

				if (isset($exam_grade_change_detail['CourseRegistration']) && !empty($exam_grade_change_detail['CourseRegistration']) && $exam_grade_change_detail['CourseRegistration']['id'] != "") {
					$grade = $this->ExamGrade->getApprovedGrade($exam_grade_change_detail['CourseRegistration']['id'], 1);
				} else {
					$grade = $this->ExamGrade->getApprovedGrade($exam_grade_change_detail['CourseAdd']['id'], 0);
				}

				if (strcasecmp($grade['grade'], 'Fx') == 0) {

					$index = count($new_exam_grade);
					$new_exam_grade[$index]['exam_grade_id'] = $exam_grade_change['id'];
					$new_exam_grade[$index]['minute_number'] = $minute_number;
					$new_exam_grade[$index]['grade'] = $exam_grade_change['grade'];
					$new_exam_grade[$index]['manual_ng_conversion'] = 1;
					$new_exam_grade[$index]['registrar_approval'] = 1;
					$new_exam_grade[$index]['college_approval'] = 1;
					$new_exam_grade[$index]['department_approval'] = 1;
					$new_exam_grade[$index]['manual_ng_converted_by'] = $login_user;

					if (isset($exam_grade_change_detail['CourseRegistration']) && !empty($exam_grade_change_detail['CourseRegistration']) && $exam_grade_change_detail['CourseRegistration']['id'] != "") {
						$new_exam_grade[$index]['p_c_id'] = $exam_grade_change_detail['CourseRegistration']['PublishedCourse']['id'];
					} else {
						$new_exam_grade[$index]['p_c_id'] = $exam_grade_change_detail['CourseAdd']['PublishedCourse']['id'];
					}
				}
				//debug($grade);
			}
		}

		if (!empty($new_exam_grade) && ($this->saveAll($new_exam_grade, array('validate' => false)))) {
			foreach ($new_exam_grade as $key => $value) {
				if (strcasecmp($value['grade'], 'I') == 0) {
					$this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($value['p_c_id']);
				}
			}
			ClassRegistry::init('AutoMessage')->sendNotificationOnAutoAndManualGradeChange($new_exam_grade, $privilaged_registrar);
			return true;
		} else {
			return false;
		}
	}

	//Automatically converted
	function getListOfGradeAutomaticallyConverted($academicyear, $semester, $department_id, $program_id, $program_type_id, $gradeConverted, $type = 0) 
	{
		if ($type == 1) {
			$publishedCourseLists = ClassRegistry::init('PublishedCourse')->find('all', array(
				'conditions' => array(
					'PublishedCourse.semester' => $semester,
					'PublishedCourse.academic_year' => $academicyear,
					'PublishedCourse.program_id' => $program_id,
					'PublishedCourse.program_type_id' => $program_type_id,
					'PublishedCourse.college_id' => $department_id
				),
				'contain' => array(
					'Course', 
					'Program', 
					'ProgramType', 
					'Department' => array('College'), 
					'CourseAdd' => array('Student'), 
					'CourseRegistration' => array('Student')
				)
			));

		} else {

			$publishedCourseLists = ClassRegistry::init('PublishedCourse')->find('all', array(
				'conditions' => array(
					'PublishedCourse.semester' => $semester,
					'PublishedCourse.academic_year' => $academicyear,
					'PublishedCourse.program_id' => $program_id,
					'PublishedCourse.program_type_id' => $program_type_id,
					'PublishedCourse.department_id' => $department_id
				),
				'contain' => array(
					'Course',
					'Program',
					'ProgramType',
					'Department' => array('College'),
					'CourseAdd' => array('Student'),
					'CourseRegistration' => array('Student')
				)
			));
		}

		$autoConvertedGradeLists = array();

		if (!empty($publishedCourseLists)) {
			foreach ($publishedCourseLists as $pk => $pv) {
				//check for course registration auto conversion
				foreach ($pv['CourseRegistration'] as $crk => $crv) {

					$autoChange = $this->find('first', array(
						'conditions' => array(
							'ExamGradeChange.auto_ng_conversion' => 1,
							'ExamGradeChange.exam_grade_id in (select id from exam_grades where course_registration_id=' . $crv['id'] . ' and grade="' . $gradeConverted . '")'
						),
						'contain' => array('ExamGrade')
					));

					if (isset($autoChange) && !empty($autoChange)) {
						$autoChange['Course'] = $pv['Course'];
						$autoChange['Student'] = $crv['Student'];
						$autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . $pv['Department']['name'] . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
					}
				}

				foreach ($pv['CourseAdd'] as $cadk => $cadv) {

					$autoChange = $this->find('first', array(
						'conditions' => array(
							'ExamGradeChange.auto_ng_conversion' => 1,
							'ExamGradeChange.exam_grade_id in (select id from exam_grades where course_add_id=' . $cadv['id'] . ' and grade="' . $gradeConverted . '")'
						),
						'contain' => array('ExamGrade')
					));

					if (isset($autoChange) && !empty($autoChange)) {
						$autoChange['Course'] = $pv['Course'];
						$autoChange['Student'] = $cadv['Student'];
						$autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . $pv['Department']['name'] . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
					}
				}
			}
		}

		return $autoConvertedGradeLists;
	}

	function possibleStudentsForSup($section_id = "")
	{
		$student_list = array();

		if (!empty($section_id)) {

			$students = ClassRegistry::init('Section')->find('first', array(
				'conditions' => array(
					'Section.id' => $section_id
				),
				'contain' => array(
					'Student' => array(
						'order' => array('first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'),
						'fields' => array('id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'graduated')
					)
				)
			));

			$section_ids = array('0', '0');

			if (isset($students['Section']['department_id']) && !empty($students['Section']['department_id']) && $students['Section']['department_id'] > 0) {
				$section_ids = ClassRegistry::init('Section')->find('list', array(
					'conditions' => array(
						'Section.department_id' => $students['Section']['department_id'],
						'Section.program_id' => $students['Section']['program_id'],
					),
					'fields' => array('Section.id', 'Section.id')
				));
			} else if (isset($students['Section']['college_id']) && !empty($students['Section']['college_id']) && empty($students['Section']['department_id'])) {
				$section_ids = ClassRegistry::init('Section')->find('list', array(
					'conditions' => array(
						'Section.college_id' => $students['Section']['college_id'],
						'Section.program_id' => $students['Section']['program_id'],
						'Section.academicyear' => $students['Section']['academicyear'],
					),
					'fields' => array('Section.id', 'Section.id')
				));
			}

			$possibleAllowedRepetitionGrade = array();

			if ($students['Section']['program_id'] == PROGRAM_POST_GRADUATE) {
				$possibleAllowedRepetitionGrade['C'] = 'C';
				$possibleAllowedRepetitionGrade['C+'] = 'C+';
				$possibleAllowedRepetitionGrade['D'] = 'D';
				$possibleAllowedRepetitionGrade['F'] = 'F';
				$possibleAllowedRepetitionGrade['NG'] = 'NG';
				$possibleAllowedRepetitionGrade['FAIL'] = 'FAIL';
				$possibleAllowedRepetitionGrade['I'] = 'I';
			} else {
				$possibleAllowedRepetitionGrade['C-'] = 'C-';
				$possibleAllowedRepetitionGrade['D'] = 'D';
				$possibleAllowedRepetitionGrade['F'] = 'F';
				$possibleAllowedRepetitionGrade['NG'] = 'NG';
				$possibleAllowedRepetitionGrade['FAIL'] = 'FAIL';
				$possibleAllowedRepetitionGrade['I'] = 'I';
			}

			if (isset($students['Student']) && !empty($students['Student'])) {
				foreach ($students['Student'] as $key => $student) {
					if (!$student['graduated']) {
						$courseRegistered = ClassRegistry::init('CourseRegistration')->find('list', array(
							'conditions' => array(
								'CourseRegistration.student_id' => $student['id'],
								//'CourseRegistration.academic_year' => $students['Section']['academicyear']
								'CourseRegistration.section_id' => $section_ids
							), 
							'fields' => array('CourseRegistration.id', 'CourseRegistration.id')
						));

						$graded = ClassRegistry::init('ExamGrade')->getApprovedGradeForMakeUpExam($courseRegistered, 1);

						if (!ClassRegistry::init('GraduateList')->isGraduated($student['id']) && isset($graded) && !empty($graded)) {
							if (!empty($graded) && ((isset($graded['allow_repetition']) && $graded['allow_repetition']) || (!empty($possibleAllowedRepetitionGrade) && in_array($graded['grade'], $possibleAllowedRepetitionGrade)))) {
								//debug($graded);
								$student_list[$student['id']] = $student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'] . ' (' . $student['studentnumber'] . ')';
							}
						}
					}
				}
			}
		}

		//debug($student_list);
		return $student_list;
	}
}