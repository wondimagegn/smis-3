<?php
class EquivalentCourse extends AppModel
{
	var $name = 'EquivalentCourse';

	var $validate = array(
		'course_be_substitued_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select course to be equivalent, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_for_substitued_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select Equivalent courses, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
		'CourseForSubstitued' => array(
			'className' => 'Course',
			'foreignKey' => 'course_for_substitued_id',
			'conditions' => '',
			'fields' => array(
				'id', 
				'lecture_hours', 
				'tutorial_hours', 
				'laboratory_hours',
				'course_code', 
				'course_title', 
				'credit', 
				'department_id', 
				'curriculum_id'
			),
			'order' => ''
		),
		'CourseBeSubstitued' => array(
			'className' => 'Course',
			'foreignKey' => 'course_be_substitued_id',
			'conditions' => '',
			'fields' => array(
				'id', 
				'lecture_hours', 
				'tutorial_hours', 
				'laboratory_hours',
				'course_code', 
				'course_title', 
				'credit', 
				'department_id', 
				'curriculum_id'
			),
			'order' => ''
		)
	);

	function isSimilarCurriculum($data = null)
	{
		if (empty($data['EquivalentCourse']['curriculum_id']) || empty($data['EquivalentCourse']['curriculum_id'])) {
			return true;
		}
		//other_curriculum_id
		if (!empty($data['EquivalentCourse']['curriculum_id']) && !empty($data['EquivalentCourse']['other_curriculum_id'])) {
			if ($data['EquivalentCourse']['curriculum_id'] == $data['EquivalentCourse']['other_curriculum_id']) {
				$this->invalidate('error', 'You are trying to map similar curriculum courses. You can not map similar curriculum courses.');
				return false;
			}
		}
		return true;
	}

	// Do not allow deletion of mapped course if the equivalent course has
	function checkStudentTakeingEquivalentCourseAndDenyDelete($id = null, $department_id = null)
	{
		$equivalent_course_id = $this->field(
			'course_be_substitued_id',
			array('EquivalentCourse.id' => $id)
		);

		$curriculum_id = ClassRegistry::init('Course')->field('curriculum_id', array('Course.id' => $equivalent_course_id));

		$course_ids = ClassRegistry::init('Course')->find('list', array(
			'conditions' => array('Course.curriculum_id' => $curriculum_id),
			'fields' => array('Course.id', 'Course.id')
		));

		$published_course_ids = ClassRegistry::init('PublishedCourse')->find('list', array(
			'conditions' => array(
				'PublishedCourse.course_id' => $course_ids,
				'PublishedCourse.department_id' => $department_id
			),
			'fields' => array('PublishedCourse.id', 'PublishedCourse.id')
		));

		if (!empty($published_course_ids)) {
			foreach ($published_course_ids as $in => $pu) {
				$grade_submitted = ClassRegistry::init('ExamGrade')->is_grade_submitted($pu);
				if ($grade_submitted > 0) {
					return false;
				}
			}
		}

		return true;
	}

	function equivalentCreditOfCourse($course_id, $studentAttachedCurriculum)
	{
		if ($studentAttachedCurriculum) {
			$courseCredit = ClassRegistry::init('Course')->find('first', array(
				'conditions' => array(
					'Course.curriculum_id' => $studentAttachedCurriculum,
					'Course.id' => $course_id
				)
			));
		} else {
			$courseCredit = array();
		}

		if (!empty($courseCredit)) {
			return $courseCredit['Course']['credit'];
		} else {
			// does it have equivalence
			$equivalentCourseIds = $this->find('list', array(
				'conditions' => array(
					'EquivalentCourse.course_be_substitued_id' => $course_id
				),
				'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
			));

			if (!empty($equivalentCourseIds)) {
				if ($studentAttachedCurriculum) {
					$courseCredit = ClassRegistry::init('Course')->find('first', array(
						'conditions' => array(
							'Course.curriculum_id' => $studentAttachedCurriculum,
							'Course.id' => $equivalentCourseIds
						)
					));
				} else {
					$courseCredit = array();
				}

				if (!empty($courseCredit)) {
					return $courseCredit['Course']['credit'];
				}
			}
		}
		return 0;
	}

	function checkCourseHasEquivalentCourse($course_id, $studentAttachedCurriculum)
	{
		if ($studentAttachedCurriculum) {
			$doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
				'Course.curriculum_id' => $studentAttachedCurriculum,
				'Course.id' => $course_id
			));
		} else {
			$doesItExistInAttachedCurriculum = '';
		}

		if (!empty($doesItExistInAttachedCurriculum)) {
			return true;
		} else {
			// does it have equivalence
			$equivalentCourseIds = $this->find('list', array(
				'conditions' => array(
					'EquivalentCourse.course_be_substitued_id' => $course_id
				),
				'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
			));

			//debug($course_id);
			//debug($equivalentCourseIds);
			
			if (!empty($equivalentCourseIds)) {

				if ($studentAttachedCurriculum) {
					$doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
						'Course.curriculum_id' => $studentAttachedCurriculum,
						'Course.id' => $equivalentCourseIds
					));
				} else {
					$doesItExistInAttachedCurriculum = '';
				}

				if (!empty($doesItExistInAttachedCurriculum)) {
					return true;
				}
				return false;
			}
		}
		return false;
	}

	function validEquivalentCourse($course_id, $studentAttachedCurriculum, $type = 1)
	{
		if ($studentAttachedCurriculum) {
			$doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
				'Course.curriculum_id' => $studentAttachedCurriculum,
				'Course.id' => $course_id
			));
		} else {
			$doesItExistInAttachedCurriculum = 0;
		}
		//debug($doesItExistInAttachedCurriculum);
		
		// does it have equivalence
		if ($doesItExistInAttachedCurriculum) {

			$equivalentCourseIds1 = $this->find('list', array(
				'conditions' => array(
					'EquivalentCourse.course_for_substitued_id' => $course_id
				),
				'fields' => array('course_be_substitued_id', 'course_be_substitued_id')
			));

			$equivalentCourseIds = $equivalentCourseIds1;

			if (!empty($equivalentCourseIds)) {
				$courseLists = ClassRegistry::init('Course')->find('list', array(
					'conditions' => array(
						'Course.id' => $equivalentCourseIds
					), 
					'fields' => array('id', 'id')
				));
				//debug($courseLists);
				return $courseLists;
			}
		} else {

			$equivalentCourseIds1 = $this->find('list', array(
				'conditions' => array(
					'EquivalentCourse.course_be_substitued_id' => $course_id
				),
				'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
			));

			$equivalentCourseIds = $equivalentCourseIds1;

			if (!empty($equivalentCourseIds)) {
				if ($studentAttachedCurriculum) {
					$courseLists = ClassRegistry::init('Course')->find('list', array(
						'conditions' => array(
							'Course.curriculum_id' => $studentAttachedCurriculum, 
							'Course.id' => $equivalentCourseIds
						),
						'fields' => array('id', 'id')
					));
				} else {
					$courseLists = array();
				}

				if (isset($courseLists) && !empty($courseLists)) {
					$equivalentCourseIds1 = $this->find('list', array(
						'conditions' => array(
							'EquivalentCourse.course_for_substitued_id' => $courseLists
						),
						'fields' => array('course_be_substitued_id', 'course_be_substitued_id')
					));
				}

				$merged = $courseLists + $equivalentCourseIds1;
				//debug($merged);
				return $merged;
				// return $equivalentCourseIds; 
			}
		}
		return array();
	}

	function courseEquivalentCategory($course_id, $studentAttachedCurriculum)
	{
		$equivalentCourseIds = $this->find('list', array(
			'conditions' => array(
				'EquivalentCourse.course_be_substitued_id' => $course_id
			),
			'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
		));

		//debug($equivalentCourseIds);
		//debug($course_id);

		if ($studentAttachedCurriculum) {
			$course = ClassRegistry::init('Course')->find('first', array(
				'conditions' => array(
					'Course.curriculum_id' => $studentAttachedCurriculum,
					'Course.id' => $equivalentCourseIds
				),
				'contain' => array('CourseCategory')
			));

			//debug($course['CourseCategory']['name']);
			if (!empty($course)) {
				return $course['CourseCategory']['name'];
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	function isEquivalentCourseMajor($course_id, $studentAttachedCurriculum)
	{
		$equivalentCourseIds = $this->find('list', array(
			'conditions' => array(
				'EquivalentCourse.course_be_substitued_id' => $course_id
			), 
			'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
		));

		if ($studentAttachedCurriculum) {
			$course = ClassRegistry::init('Course')->find('first', array(
				'conditions' => array(
					'Course.curriculum_id' => $studentAttachedCurriculum,
					'Course.id' => $equivalentCourseIds
				),
				'contain' => array('CourseCategory')
			));

			if (isset($course['Course']) && !empty($course['Course']) && $course['Course']['major']) {
				return 1;
			}
		}

		return 0;
	}
}
