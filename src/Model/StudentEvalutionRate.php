<?php
App::uses('AppModel', 'Model');
class StudentEvalutionRate extends AppModel
{
	public $validate = array(
		'instructor_evalution_question_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'rating' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
			)
		),
	);

	public $belongsTo = array(
		'InstructorEvalutionQuestion' => array(
			'className' => 'InstructorEvalutionQuestion',
			'foreignKey' => 'instructor_evalution_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
		)
	);

	public function getNotEvaluatedRegisteredCourse($student_id)
	{
		$course = array();
		$type = 1; // Course Registration
		$section_id = NULL;

		$mostRecentReg = $this->Student->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC'),
			'contain' => array(
				'Section' => array(
					'Department' => array('id', 'name', 'type', 'active', 'college_id'),
					'College' => array('id', 'name', 'type', 'active', 'stream', 'campus_id'),
					'YearLevel' => array('id', 'name'),
				),
				'YearLevel' => array('id', 'name'),
			),
			'recursive' => -1
		));

		if (!empty($mostRecentReg)) {

			$section_id = $mostRecentReg['CourseRegistration']['section_id'];

			$course = $this->Student->CourseRegistration->find('first', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.semester' => $mostRecentReg['CourseRegistration']['semester'],
					'CourseRegistration.academic_year' => $mostRecentReg['CourseRegistration']['academic_year'],
					'CourseRegistration.published_course_id not in (select published_course_id from student_evalution_rates where student_id=' . $student_id . ' and published_course_id is not null )',
					'CourseRegistration.published_course_id  in (select published_course_id from course_instructor_assignments where isprimary = 1 and published_course_id is not null)'
				),
				'contain' => array(
					'PublishedCourse' => array(
						'Course',
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.evaluation_printed' => 0
							),
							'Staff' => array(
								'conditions' => array(
									'Staff.active' => 1
								),
								'Title',
								'Position'
							)
						),
						'Section' => array(
							'Department' => array('id', 'name', 'type', 'active', 'college_id'),
							'College' => array('id', 'name', 'type', 'active', 'stream', 'campus_id'),
							'YearLevel' => array('id', 'name'),
						),
						'YearLevel' => array('id', 'name'),
					)
				),
				'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC'),
			));

			if (empty($course)) {
				$course = $this->Student->CourseAdd->find('first', array(
					'conditions' => array(
						'CourseAdd.student_id' => $student_id,
						'CourseAdd.semester' => $mostRecentReg['CourseRegistration']['semester'],
						'CourseAdd.academic_year' => $mostRecentReg['CourseRegistration']['academic_year'],
						'CourseAdd.department_approval' => 1,
						'CourseAdd.registrar_confirmation' => 1,
						'CourseAdd.published_course_id not in (select published_course_id from student_evalution_rates where student_id=' . $student_id . ' and published_course_id is not null )',
						'CourseAdd.published_course_id  in (select published_course_id from course_instructor_assignments where isprimary = 1 and published_course_id is not null)'
					),
					'contain' => array(
						'PublishedCourse' => array(
							'Course',
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.evaluation_printed' => 0
								),
								'Staff' => array(
									'conditions' => array(
										'Staff.active' => 1
									),
									'Title',
									'Position'
								)
							),
							'Section' => array(
								'Department' => array('id', 'name', 'type', 'active', 'college_id'),
								'College' => array('id', 'name', 'type', 'active', 'stream', 'campus_id'),
								'YearLevel' => array('id', 'name'),
							),
							'YearLevel' => array('id', 'name'),
						)
					),
					'order' => array('CourseAdd.academic_year' => 'DESC', 'CourseAdd.semester' => 'DESC', 'CourseAdd.id' => 'DESC'),
				));

				if (!empty($course)) {
					$type = 2; // Course Add
				}
			}
		} else {

			// worst case senario, students with only Course adds without any Course Registration for the semester

			$mostRecentAdd = $this->Student->CourseAdd->find('first', array(
				'conditions' => array(
					'CourseAdd.student_id' => $student_id,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
				),
				'order' => array('CourseAdd.academic_year' => 'DESC', 'CourseAdd.semester' => 'DESC', 'CourseAdd.id' => 'DESC'),
				'recursive' => -1
			));

			debug($mostRecentAdd);

			if (empty($course)) {
				$course = $this->Student->CourseAdd->find('first', array(
					'conditions' => array(
						'CourseAdd.student_id' => $student_id,
						'CourseAdd.semester' => $mostRecentAdd['CourseAdd']['semester'],
						'CourseAdd.academic_year' => $mostRecentAdd['CourseAdd']['academic_year'],
						'CourseAdd.department_approval' => 1,
						'CourseAdd.registrar_confirmation' => 1,
						'CourseAdd.published_course_id not in (select published_course_id from student_evalution_rates where student_id=' . $student_id . ' and published_course_id is not null )',
						'CourseAdd.published_course_id  in (select published_course_id from course_instructor_assignments where isprimary = 1 and published_course_id is not null)'
					),
					'contain' => array(
						'PublishedCourse' => array(
							'Course',
							'CourseInstructorAssignment' => array(
								'conditions' => array(
									'CourseInstructorAssignment.evaluation_printed' => 0
								),
								'Staff' => array(
									'conditions' => array(
										'Staff.active' => 1
									),
									'Title',
									'Position'
								)
							),
							'Section' => array(
								'Department' => array('id', 'name', 'type', 'active', 'college_id'),
								'College' => array('id', 'name', 'type', 'active', 'stream', 'campus_id'),
								'YearLevel' => array('id', 'name'),
							),
							'YearLevel' => array('id', 'name'),
						)
					),
					'order' => array('CourseAdd.academic_year' => 'DESC', 'CourseAdd.semester' => 'DESC', 'CourseAdd.id' => 'DESC'),
				));

				if (!empty($course)) {
					$type = 2; // Course Add
				}
			}
		}

		if (!empty($course)) {

			//debug($course);

			if (!ALLOW_STAFF_EVALUATION_AFTER_GRADE_SUBMISSION) {

				isset($course['CourseRegistration']) ? debug($course['CourseRegistration']['id']) : '';
				isset($course['CourseAdd']) ? debug($course['CourseAdd']['id']) : '';

				if ($type == 1) {
					$gradeSubmitted = classRegistry::init('ExamGrade')->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $course['CourseRegistration']['id']), 'recursive' => -1));
				} else {
					$gradeSubmitted = classRegistry::init('ExamGrade')->find('count', array('conditions' => array('ExamGrade.course_add_id' => $course['CourseAdd']['id']), 'recursive' => -1));
				}
				
				debug($gradeSubmitted);

				if ($gradeSubmitted) {
					// empty courses so that the evaluation is skipped
					//$course = array();
				}
			}

		}

		return $course;
	}

	public function getACSem($student_id)
	{
		$getAS = array();

		$course = $this->Student->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
			),
			'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC'),
		));

		if (!empty($course)) {
			$getAS['academicYear'] = $course['CourseRegistration']['academic_year'];
			$getAS['semester'] = $course['CourseRegistration']['semester'];
		}

		return $getAS;
	}
}
