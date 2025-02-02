<?php
class Clearance extends AppModel
{
	var $name = 'Clearance';

	var $validate = array(
		'minute_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide the minute number the decision is made.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array('model' => 'Clearance'),
			'dependent' => true,
		),
	);

	function validateStudentClearance($student_id = null)
	{
		$check_student_returned_properties = $this->Student->TakenProperty->find('count', array(
			'conditions' => array(
				'TakenProperty.returned ' => 0,
				'TakenProperty.student_id' => $student_id
			)
		));

		if ($check_student_returned_properties > 0) {

			$details = $this->Student->TakenProperty->find('all', array(
				'conditions' => array(
					'TakenProperty.returned ' => 0,
					'TakenProperty.student_id' => $student_id
				)
			));

			$loan_item = array();

			if (!empty($details)) {
				foreach ($details as $index => $value) {
					if (!empty($value['TakenProperty']['office_id'])) {
						$loan_item[$value['Office']['name']][] = $value['TakenProperty']['name'];
					} else if (!empty($value['TakenProperty']['college_id'])) {
						$loan_item[$value['College']['name']][] = $value['TakenProperty']['name'];
					} else if (!empty($value['Department']['name'])) {
						$loan_item[$value['Department']['name']][] = $value['TakenProperty']['name'];
					}
				}
			}

			$this->invalidate('not_returned_item', $loan_item);
			return false;
		}

		return true;
	}

	function checkDuplication($data = null)
	{
		debug($data);

		$lastregistredDate = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $data['Clearance']['student_id']
			),
			'order' => array(
				'CourseRegistration.academic_year' => 'DESC', 
				'CourseRegistration.semester' => 'DESC', 
				'CourseRegistration.created' => 'DESC'
			),
			'recursive' => -1
		));

		// allow multiple application in case of denied clearnce
		$check1 = $this->find('count', array(
			'conditions' => array(
				'Clearance.student_id' => $data['Clearance']['student_id'],
				'Clearance.confirmed' => -1,
				'OR' => array(
					'Clearance.request_date >= ' => $lastregistredDate['CourseRegistration']['created'],
					'Clearance.last_class_attended_date >= ' => $lastregistredDate['CourseRegistration']['created'],
				)
			)
		));

		if ($check1) {
			return true;
		}

		// dont allow reapplication for those on progress and accepted
		$check = $this->find('count', array(
			'conditions' => array(
				'Clearance.student_id' => $data['Clearance']['student_id'],
				'OR' => array(
					'Clearance.request_date >= ' => $lastregistredDate['CourseRegistration']['created'],
					'Clearance.last_class_attended_date >= ' => $lastregistredDate['CourseRegistration']['created'],
				)
			)
		));

		if ($check) {
			return false;
		}
		return true;
	}

	function preparedAttachment($data = null)
	{
		if (!empty($data['Attachment'])) {
			foreach ($data['Attachment'] as $in =>  &$dv) {
				if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
					unset($data['Attachment'][$in]);
				} else {
					/* if ($data['Clearance']['type'] == 'clearance') {
						$dv['model'] = 'Clearance';
						$dv['group'] = 'attachment';
					} else {
						$dv['model'] = 'Student';
						$dv['foreign_key'] = $data['Clearance']['student_id'];
						$dv['group'] = 'attachment';
					} */

					$dv['model'] = 'Clearance';
					$dv['group'] = 'attachment';
				}
			}
			return $data;
		}
	}

	function checkRecentPendingApproval($student_id = null)
	{

		$lastregistredDate = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'order' => array(
				'CourseRegistration.academic_year' => 'DESC', 
				'CourseRegistration.semester' => 'DESC', 
				'CourseRegistration.created' => 'DESC'
			),
			'recursive' => -1
		));

		// no recent submitted 
		$check = $this->find('first', array(
			'conditions' => array(
				'Clearance.student_id' => $student_id,
				'Clearance.confirmed IS NULL',
				'OR' => array(
					'Clearance.request_date >= ' => $lastregistredDate['CourseRegistration']['created'],
					'Clearance.last_class_attended_date >= ' => $lastregistredDate['CourseRegistration']['created'],
				)
			)
		));

		if ($check) {
			return $check;
		}
		return false;
	}

	function elegibleForReadmission($student_id = null, $academic_year = null)
	{
		/**
		 * 0 not cleared                         // redirect to clearance page 
		 * 1 cleared                         
		 * 2 cleared but not have status         // allow readmission application on hold state 
		 * 3 cleared and have status but not achieved readmission point // not elegible  
		 * 4 cleared and have status and achieved readmission point   // elegible 
		 * 5 withdraw not completed  
		 * 6 withdraw properly 
		 */


		// isCleared ?
		// take student last registred academic year for clearance 

		$lastregistredDate = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'order' => array(
				'CourseRegistration.academic_year' => 'DESC', 
				'CourseRegistration.semester' => 'DESC', 
				'CourseRegistration.created' => 'DESC'
			),
			'recursive' => -1
		));

		// does it clear till requested academic year ?
		$clearances = $this->find('all', array(
			'conditions' => array(
				'Clearance.student_id' => $student_id,
				'Clearance.confirmed = 1'
			), 
			'recursive' => -1, 
			'order' => array('Clearance.request_date' => 'DESC')
		));

		if (empty($clearances)) {
			$this->invalidate('error', 'You need to request clearance and it should get approved before applying for readmission application.');
			return 0; // not allow 
		} else {
			// find the latest class attended and is s/he cleared ?        
			debug($lastregistredDate['CourseRegistration']['created']);

			$check_clearance = $this->find('count', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.confirmed = 1',
					'OR' => array(
						'Clearance.request_date >= ' => $lastregistredDate['CourseRegistration']['created'],
						'Clearance.last_class_attended_date >= ' => $lastregistredDate['CourseRegistration']['created'],
					)
				)
			));

			if ($check_clearance) {
				$readmissionAchieved = $this->readmissionPoint($student_id);
				if ($readmissionAchieved == 5) {
					// is proper withdraw ?
					$this->invalidate('error', 'You need to fill clearance/withdraw before applying for readmission.');
					return 0; // not allow
				} else if ($readmissionAchieved == 3) {
					$this->invalidate('error', 'You have not achieved the minimum readmission point for readmission application. Please advice registrar for further information.');
					return 3; // not allow 
				} else if ($readmissionAchieved == 4 || $readmissionAchieved == 6) {
					return 4;  // allow 
				}
			} else {   
				$this->invalidate('error', 'You need to reqeuest clearance/withdraw before applying for readmission application.');
				return 0; // not allow 
			}
		}
	}

	function readmissionPoint($student_id = null, $year = null)
	{

		/**
		 * 0 not cleared                         // redirect to clearance page 
		 * 1 cleared                         
		 * 2 cleared but not have status         // allow readmission application on hold state 
		 * 3 cleared and have status but not achieved readmission point 	// not elegible  
		 * 4 cleared and have status but achieved readmission point   	// elegible 
		 * 5 withdraw not completed  
		 * 6 withdraw properly 
		 */


		$student_status = $this->Student->StudentExamStatus->find('first', array(
			'conditions' => array(
				'StudentExamStatus.student_id' => $student_id
			),
			'recursive' => -1,
			'order' => array(
				'StudentExamStatus.academic_year' => 'DESC',
				'StudentExamStatus.semester' => 'DESC',
				'StudentExamStatus.created' => 'DESC'
			)
		));

		if (empty($student_status)) {

			$lastregistredDate = ClassRegistry::init('CourseRegistration')->find('first', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id
				),
				'recursive' => -1,
				'order' => array(
					'CourseRegistration.academic_year' => 'DESC', 
					'CourseRegistration.semester' => 'DESC', 
					'CourseRegistration.created' => 'DESC'
				),
			));

			// check student is properly withdraw
			$withdraw = $this->find('all', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.confirmed = 1'
				),
				'recursive' => -1,
				'order' => 'Clearance.acceptance_date DESC'
			));

			if (empty($withdraw)) {
				return 5;
			} else {
				$check_withdraw = $this->find('count', array(
					'conditions' => array(
						'Clearance.student_id' => $student_id,
						'Clearance.confirmed = 1',
						'OR' => array(
							'Clearance.request_date >= ' => $lastregistredDate['CourseRegistration']['created'],
							'Clearance.last_class_attended_date >= ' => $lastregistredDate['CourseRegistration']['created'],
						)
					)
				));

				if ($check_withdraw) {
					return 6;
				} else {
					return 5;
				}
			}
		} else {
			// check minimum point 
			$minimum_point_achieved = $this->isAchievedMinimumReadmissionPoint($student_id);

			if ($minimum_point_achieved == 99) {
				return 2;
			}
			return $minimum_point_achieved;
		}
	}

	function latestCourseRegistrationSemesterYearLevel($student_id = null)
	{

		$semester_year_level['semester'] = "I";

		$student_detail = $this->Student->find('first', array(
			'conditions' => array('Student.id' => $student_id),
			'contain' => array()
		));

		if (!empty($student_id)) {

			$latestSemester = $this->Student->CourseRegistration->find('all', array(
				'fields' => array(
					'CourseRegistration.semester',
					'CourseRegistration.year_level_id',
					'CourseRegistration.academic_year'
				),
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id
				),
				'order' => array(
					'CourseRegistration.academic_year' => 'DESC', 
					'CourseRegistration.semester' => 'DESC', 
					'CourseRegistration.created' => 'DESC'
				),
				'recursive' => -1
			));

			if (empty($latestSemester)) {
				$semester_year_level['semester'] = "I";
				$semester_year_level['year_level'] = "1st";
				$semester_year_level['program_id'] = $student_detail['Student']['program_id'];
				$semester_year_level['admissionyear'] = $student_detail['Student']['admissionyear'];
				return $semester_year_level;
			}
		}

		if (!empty($latestSemester)) {
			foreach ($latestSemester as $k => $v) {
				if (strcasecmp($v['CourseRegistration']['semester'], $semester_year_level['semester']) > 0) {
					$semester_year_level['semester'] = $v['CourseRegistration']['semester'];
				}
				//debug($student_detail['Student']['department_id']);
				if (!empty($student_detail['Student']['department_id'])) {
					$semester_year_level['year_level'] = $this->Student->Department->YearLevel->field('name', array(
						'YearLevel.department_id' => $student_detail['Student']['department_id'],
						'YearLevel.id' => $v['CourseRegistration']['year_level_id']
					));
				} else if (empty($student_detail['Student']['department_id']) && !empty($student_detail['Student']['college_id'])) {
					$semester_year_level['year_level'] = "1st";
				}
			}
		}

		$semester_year_level['program_id'] = $student_detail['Student']['program_id'];
		$semester_year_level['admissionyear'] = $student_detail['Student']['admissionyear'];

		return $semester_year_level;
	}

	/** if the student last status is dismissed **/
	function isAchievedMinimumReadmissionPoint($student_id = null)
	{

		$student_detail = $this->latestCourseRegistrationSemesterYearLevel($student_id);

		$academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
			'conditions' => array(
				'AcademicStand.academic_status_id = 5',
				'AcademicStand.program_id' => $student_detail['program_id']
			),
			'order' => array('AcademicStand.academic_year_from' => 'ASC'),
			'recursive' => -1
		));

		$as = null;

		if (!empty($academic_stands)) {
			foreach ($academic_stands as $key => $academic_stand) {
				$stand_year_levels = unserialize($academic_stand['AcademicStand']['year_level_id']);
				$stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);
				if (in_array($student_detail['year_level'], $stand_year_levels) && in_array($student_detail['semester'], $stand_semesters)) {
					if ((substr($student_detail['admissionyear'], 0, 4) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1)) {
						$as = $academic_stand['AcademicStand'];
						break;
					}
				}
			}
		}

		debug($as);

		if (!empty($as)) {
			//Searching for the rule by the acadamic stand
			$acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
				'conditions' => array(
					'AcademicRule.academic_stand_id' => $as['id']
				),
				'recursive' => -1
			));

			$student_status = $this->Student->StudentExamStatus->find('first', array(
				'conditions' => array(
					'StudentExamStatus.student_id' => $student_id
				),
				'recursive' => -1,
				'order' => array(
					'StudentExamStatus.academic_year' => 'DESC',
					'StudentExamStatus.semester' => 'DESC',
					'StudentExamStatus.created' => 'DESC'
				)
			));

			if (!empty($acadamic_rules)) {
				foreach ($acadamic_rules as $key => $acadamic_rule) {
					debug($acadamic_rule);
					if ($acadamic_rule['AcademicRule']['sgpa'] != 0) {
						if ($student_status['StudentExamStatus']['sgpa'] > $acadamic_rule['AcademicRule']['sgpa']) {
							return 4;
						}
					} else if ($acadamic_rule['AcademicRule']['cgpa'] != 0) {
						if ($student_status['StudentExamStatus']['cgpa'] > $acadamic_rule['AcademicRule']['cgpa']) {
							//return true;
							return 4;
						}
					}
				}
			}
		}

		if (empty($academic_stand)) {
			// academic stand for readmission is not defined
			return 99; // allow application  
		}

		return 3;
	}

	function organizeListOfClearanceApplicant($data = null)
	{
		$clearance_request_organized_by_program = array();

		if(!empty($data)) {
			foreach ($data as $index => $value) {
				if (!empty($value['Student']['Department']['name'])) {
					if (!empty($value['Student']['id'])) {
						$clearance_request_organized_by_program[$value['Student']['Department']['name']][$value['Student']['Program']['name']][$value['Student']['ProgramType']['name']][] = $value;
					}
				} else {
					if (!empty($value['Student']['id'])) {
						$clearance_request_organized_by_program['Pre/Freshman'][$value['Student']['Program']['name']][$value['Student']['ProgramType']['name']][] = $value;
					}
				}
			}
		}

		return $clearance_request_organized_by_program;
	}


	//count clearance request not approved
	function count_clearnce_request($department_ids = null, $college_ids = null, $days_back = '', $current_academic_year_start_date = '')
	{
		$options = array();

		if (isset($days_back) && !empty($days_back)){
			$request_date = date('Y-m-d ', strtotime("-" . $days_back . " day "));
		} else {
			$request_date = date('Y-m-d ', strtotime("-" . DAYS_BACK_CLEARANCE . " day "));
		}
		

		$options['recursive'] = -1;
		$options['contain'] = array('Student');

		if (!empty($department_ids)) {
			$options['conditions'] = array(
				'Student.department_id' => $department_ids,
				'Clearance.confirmed is null',
				'OR' => array(
					'Clearance.request_date >= ' => (!empty($current_academic_year_start_date) ? $current_academic_year_start_date : $request_date),
					'Clearance.last_class_attended_date >= ' => (!empty($current_academic_year_start_date) ? $current_academic_year_start_date : $request_date),
				)
			);
		}
		
		if (!empty($college_ids)) {
			$options['conditions'] = array(
				'Student.department_id is null ',
				'Student.college_id ' => $college_ids,
				'Clearance.confirmed is null',
				'OR' => array(
					'Clearance.request_date >= ' => (!empty($current_academic_year_start_date) ? $current_academic_year_start_date : $request_date),
					'Clearance.last_class_attended_date >= ' => (!empty($current_academic_year_start_date) ? $current_academic_year_start_date : $request_date),
				)
			);
		}

		if (isset($options['conditions']) && !empty($options['conditions'])) {
			$clearanceCount = $this->find('count', $options);
		} else {
			$clearanceCount = 0;
		}

		//debug($clearanceCount );
		return  $clearanceCount;
	}

	function clearedAfterRegistration($student_id = null)
	{
		$last_registration_date = $this->Student->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'order' => array(
				'CourseRegistration.academic_year' => 'DESC', 
				'CourseRegistration.semester' => 'DESC', 
				'CourseRegistration.created' => 'DESC'
			),
		));

		if (!empty($last_registration_date)) {

			$check_clearance = $this->find('count', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.confirmed=1',
					'OR' => array(
						'Clearance.request_date >= ' =>  $last_registration_date['CourseRegistration']['created'],
						'Clearance.last_class_attended_date >= ' =>  $last_registration_date['CourseRegistration']['created'],
					)
				)
			));

			if ($check_clearance > 0) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	function withDrawAfterLastStatusButNotReadmitted($student_id = null, $current_academicyear = null)
	{
		$last_status_date = $this->Student->StudentExamStatus->find('first', array(
			'conditions' => array(
				'StudentExamStatus.student_id' => $student_id
			), 
			'order' => array(
				'StudentExamStatus.academic_year' => 'DESC',
				'StudentExamStatus.semester' => 'DESC',
				'StudentExamStatus.created' => 'DESC'
			)
		));

		if (!empty($last_status_date)) {
			$check_withdraw = $this->find('count', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.type' => 'withdraw',
					'OR' => array(
						'Clearance.request_date >= ' => $last_status_date['StudentExamStatus']['created'],
						'Clearance.last_class_attended_date >= ' => $last_status_date['StudentExamStatus']['created'],
					)
				)
			));

			if ($check_withdraw > 0) {
				if (!($this->Student->Readmission->is_readmitted($student_id, $current_academicyear))) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		return false;
	}


	function withDrawaAfterFirstTimeRegistration($student_id = null, $academic_year = null, $semester = null)
	{
		if (!empty($academic_year) && !empty($semester)) {
			$last_registration_date = $this->Student->CourseRegistration->find('first', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.academic_year' => $academic_year,
					'CourseRegistration.semester' => $semester, 
					'CourseRegistration.id in (select course_registration_id from exam_grades where id in (select exam_grade_id from exam_grade_changes where grade="W")) '
				),
				'order' => array(
					'CourseRegistration.academic_year' => 'DESC', 
					'CourseRegistration.semester' => 'DESC', 
					'CourseRegistration.created' => 'DESC'
				), 
				'contain' => array(
					'ExamGrade' => array('ExamGradeChange')
				)
			));
		} else {
			$last_registration_date = $this->Student->CourseRegistration->find('first', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id, 
					'CourseRegistration.id in (select course_registration_id from exam_grades where id in (select exam_grade_id from exam_grade_changes where grade="W")) '
				),
				'order' => array(
					'CourseRegistration.academic_year' => 'DESC', 
					'CourseRegistration.semester' => 'DESC', 
					'CourseRegistration.created' => 'DESC'
				),
				'contain' => array(
					'ExamGrade' => array('ExamGradeChange')
				)
			));
		}

		//check proper withdrawal                      
		if (isset($last_registration_date['CourseRegistration']['created'])) {
			$check_withdraw = $this->find('count', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.confirmed=1',
					'Clearance.type' => 'withdraw',
					'Clearance.acceptance_date >=' => $last_registration_date['CourseRegistration']['created']
				)
			));
		}

		$previousAcademicYear = ClassRegistry::init('StudentExamStatus')->getStudentFirstAyAndSemester($student_id);

		if (isset($last_registration_date['CourseRegistration']['academic_year'])) {
			if (isset($previousAcademicYear['academic_year']) && $previousAcademicYear['academic_year'] == $last_registration_date['CourseRegistration']['academic_year'] && $previousAcademicYear['semester'] == $last_registration_date['CourseRegistration']['semester']) {
				return true;
			}
		}
		return false;
	}

	function withDrawaAfterRegistration($student_id = null, $academic_year = null, $semester = null)
	{
		$last_registration_date = $this->Student->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.semester' => $semester, 
				'CourseRegistration.id in (select course_registration_id from exam_grades where id in (select exam_grade_id from exam_grade_changes where grade="W")) '
			),
			'order' => array(
				'CourseRegistration.academic_year' => 'DESC', 
				'CourseRegistration.semester' => 'DESC', 
				'CourseRegistration.created' => 'DESC'
			),
			'contain' => array('ExamGrade' => array('ExamGradeChange'))
		));

		//check proper withdrawal                      
		if (isset($last_registration_date['CourseRegistration']['created']) && !empty($last_registration_date['CourseRegistration']['created'])) {
			$check_withdraw = $this->find('count', array(
				'conditions' => array(
					'Clearance.student_id' => $student_id,
					'Clearance.confirmed = 1',
					'Clearance.type' => 'withdraw',
					'Clearance.acceptance_date >=' => $last_registration_date['CourseRegistration']['created']
				)
			));
		}

		if (isset($check_withdraw) && $check_withdraw > 0) {
			return true;
		} else {
			return false;
		}
	}
}
