<?php
App::uses('AppModel', 'Model');
class ResultEntryAssignment extends AppModel {

	public $validate = array(
		'student_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'minute_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'published_course_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseRegistration' => array(
			'className' => 'CourseRegistration',
			'foreignKey' => 'course_registration_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseAdd' => array(
			'className' => 'CourseAdd',
			'foreignKey' => 'course_add_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function isRegisteredAndAddedCourse($published_course_id = null, $student_id = null)
	{
		if (!empty($published_course_id) && !empty($student_id)) {
			
			$registered = $this->CourseRegistration->find("count", array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.published_course_id' => $published_course_id
				)
			));

			if ($registered) {
				return $registered;
			} else {
				$added = $this->CourseAdd->find("count", array(
					'conditions' => array(
						'CourseAdd.student_id' => $student_id,
						'CourseAdd.published_course_id' => $published_course_id
					)
				));

				return $added;
			}
		}
	}

	public function assignedResultEntry($published_course_id = null)
	{
		$assigned = 0;

		if (!empty($published_course_id)) {
			$assigned = $this->find('count', array(
				'conditions' => array(
					'ResultEntryAssignment.published_course_id' => $published_course_id,
				),
				'recursive' => -1
			));
		}

		return $assigned;
	}

	function getExamResultEntry($department_id = "", $acadamic_year = "", $program_id = null, $program_type_id = null, $semester = "0")
	{
		$makeup_exams_formated = array();

		if ($department_id != "" && $acadamic_year != "") {
			
			$conditions['PublishedCourse.given_by_department_id'] = $department_id;
			$conditions['PublishedCourse.academic_year'] = $acadamic_year;
			
			if (isset($program_id) && (!empty($program_id) || is_array($program_id))) {
				$conditions['PublishedCourse.program_id'] = $program_id;
			}

			if ($program_type_id != "0" || is_array($program_type_id)) {
				$conditions['PublishedCourse.program_type_id'] = $program_type_id;
			}

			if ($semester != "0") {
				$conditions['PublishedCourse.semester'] = $semester;
			}

			//result entry assingment published course exams which are assigned to the instructor
			debug($conditions);

			$all_makeup_exams = $this->PublishedCourse->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
					'Section' => array(
						'Department' => array('id', 'name', 'type'),
						'College' => array('id', 'name', 'type', 'stream'),
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'YearLevel' => array('id', 'name')
					),
					'Course' => array(
						'Prerequisite' => array(
							'Course',
							'PrerequisiteCourse'
						)
					),
					'ResultEntryAssignment' => array(
						'CourseRegistration' => array(
							'PublishedCourse' => array(
								'Section' => array(
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'YearLevel' => array('id', 'name')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'YearLevel' => array('order' => array('YearLevel.name')),
								'College' => array('id', 'name', 'type', 'stream'),
								'Department' => array(
									'fields'=> array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream')
								),
								'GivenByDepartment' => array(
									'fields'=> array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream')
								),
								'CourseInstructorAssignment' => array(
									'fields' => array(
										'CourseInstructorAssignment.id',
										'CourseInstructorAssignment.staff_id',
										'CourseInstructorAssignment.type',
										'CourseInstructorAssignment.isprimary',
										'CourseInstructorAssignment.course_split_section_id'
									),
									'Staff' => array(
										'fields' => array('Staff.full_name', 'phone_mobile'),
										'Department' => array('id', 'name', 'type'),
										//'conditions' => array('Staff.active' => 1),
										'Title' => array('fields' => array('id', 'title')),
										'Position' => array('fields' => array('id', 'position'))
									),
									'order' => array('CourseInstructorAssignment.isprimary' => 'DESC', 'CourseInstructorAssignment.id' => 'ASC')
								),
								'Course' => array(
									'Prerequisite' => array(
										'Course',
										'PrerequisiteCourse'
									)
								)
							),
							'Student',
							'ExamGrade',
							'ExamResult'
						),
						'CourseAdd' => array(
							'PublishedCourse' => array(
								'Section' => array(
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'YearLevel' => array('id', 'name')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'YearLevel' => array('order' => array('YearLevel.name')),
								'College' => array('id', 'name', 'type', 'stream'),
								'Department' => array(
									'fields'=> array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream')
								),
								'GivenByDepartment' => array(
									'fields'=> array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type', 'stream')
								),
								'CourseInstructorAssignment' => array(
									'fields' => array(
										'CourseInstructorAssignment.id',
										'CourseInstructorAssignment.staff_id',
										'CourseInstructorAssignment.type',
										'CourseInstructorAssignment.isprimary',
										'CourseInstructorAssignment.course_split_section_id'
									),
									'Staff' => array(
										'fields' => array('Staff.full_name', 'phone_mobile'),
										'Department' => array('id', 'name', 'type'),
										//'conditions' => array('Staff.active' => 1),
										'Title' => array('fields' => array('id', 'title')),
										'Position' => array('fields' => array('id', 'position'))
									),
									'order' => array('CourseInstructorAssignment.isprimary' => 'DESC', 'CourseInstructorAssignment.id' => 'ASC')
								),
								'Course' => array(
									'Prerequisite' => array(
										'Course',
										'PrerequisiteCourse'
									)
								)
							),
							'Student',
							'ExamGrade',
							'ExamResult'
						)
					),
				)
			));

			//debug($all_makeup_exams);

			$count = 0;

			if (!empty($all_makeup_exams)) {
				foreach ($all_makeup_exams as $key => $makeup_exams) {
					if (isset($makeup_exams['ResultEntryAssignment']) && !empty($makeup_exams['ResultEntryAssignment'])) {
						foreach ($makeup_exams['ResultEntryAssignment'] as $me_key => $makeup_exam) {//debug($makeup_exam);
							if (!empty($makeup_exam['CourseRegistration'])) {
								$makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseRegistration']['Student']['first_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['middle_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['last_name'];
								$makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseRegistration']['Student']['studentnumber'];
								$makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ') [Registered]';
								$makeup_exams_formated[$count]['ExamGrade'] = (isset($makeup_exam['CourseRegistration']['ExamGrade'][0]) ? $makeup_exam['CourseRegistration']['ExamGrade'][0] : array());
								$makeup_exams_formated[$count]['assigned_instructor'] = (isset($makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]) ? $makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')' : '');
								$makeup_exams_formated[$count]['assigned_instructor_contact'] = (isset($makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]) ? $makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Department']['name'] .  ', ' . (isset($makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['phone_mobile']) ? ' (Mobile: '.$makeup_exam['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['phone_mobile'] . ')' : '')  : '');
							} else {
								$makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseAdd']['Student']['first_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['middle_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['last_name'];
								$makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseAdd']['Student']['studentnumber'];
								$makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_code'] . ') [Added]';
								$makeup_exams_formated[$count]['ExamGrade'] = (isset($makeup_exam['CourseAdd']['ExamGrade'][0]) ? $makeup_exam['CourseAdd']['ExamGrade'][0] : array());
								$makeup_exams_formated[$count]['assigned_instructor'] = (isset($makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]) ? $makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')' : '');
								$makeup_exams_formated[$count]['assigned_instructor_contact'] = (isset($makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]) ? $makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Department']['name'] .  ', ' . (isset($makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['phone_mobile']) ? ' (Mobile: '.$makeup_exam['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['phone_mobile'] . ')' : '')  : '');
							}

							$makeup_exams_formated[$count]['minute_number'] = $makeup_exam['minute_number'];
							$makeup_exams_formated[$count]['taken_exam'] = $makeup_exams['Course']['course_title'] . ' (' . $makeup_exams['Course']['course_code'] . ')';
							$makeup_exams_formated[$count]['section_exam_taken'] = $makeup_exams['Section']['name'] . ' (' . (isset($makeup_exams['Section']['YearLevel']['id']) ? $makeup_exams['Section']['YearLevel']['name'] : ($makeup_exams['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial': 'Pre/1st')). ', ' . $makeup_exams['Section']['academicyear']. ')';
							$makeup_exams_formated[$count]['created'] = $makeup_exam['created'];
							$makeup_exams_formated[$count]['modified'] = $makeup_exam['modified'];
							$makeup_exams_formated[$count]['result'] = (!empty($makeup_exam['result']) ? $makeup_exam['result'] : '');
							$makeup_exams_formated[$count]['id'] = $makeup_exam['id'];
							$count++;
						}
					}
				}
			}
		}

		return $makeup_exams_formated;
	}
}
