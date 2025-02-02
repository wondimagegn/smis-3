<?php
class Course extends AppModel
{
	var $name = 'Course';

	var $virtualFields = array(
		'course_detail_hours' => 'CONCAT(Course.lecture_hours, "-",Course.tutorial_hours,"-", Course.laboratory_hours)',
		//'course_code_title' => 'CONCAT(Course.course_title ," (", Course.course_code,")")'
		//'course_code_title' => 'CONCAT(TRIM(REPLACE(Course.course_title, "\t", "")), " (", TRIM(REPLACE(Course.course_code, "\t", "")), ")")',
		'course_code_title' => 'CONCAT(TRIM(REPLACE(REPLACE(Course.course_title, "\t", ""), "  ", " ")), " (", TRIM(REPLACE(REPLACE(Course.course_code, "\t", ""), "  ", "")), ")")'
	);

	var $validate = array(
		'course_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide course title, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide course code , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'courseCodeUnique' => array(
				'rule' => array('courseCodeUnique'),
				'message' => 'Please provide a unique course code, the course code you entered has already recored for other
				 courses. Please change this course code or change the already recorded courses code.',
				'last' => true,
			),
			'courseCodeSeparatedByMinus' => array(
				'rule' => array('courseCodeSeparatedByMinus'),
				'message' => 'The course code should be separated with -.eg comp-200.',
				'last' => true,

			)
		),
		'credit' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide credit , it is required.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide valide credit, greater than zero.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'lecture_hours' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide lecture hours , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide lecture hours , greater than or equal to zero.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'tutorial_hours' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide tutorial hours , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide tutorial hours , greater than or equal to zero.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide course status , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'curriculum_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please attache course to curriculum , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'year_level_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select course year level , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'grade_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select course grade type , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select course category , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'semester' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select course semester , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'laboratory_hours' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide laboratory hours, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide laboratory hours , greater than or equal to zero.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*'lecture_attendance_requirement' => array(
			'checkAttendanceRequirementValue' => array(
				'rule' => array('checkAttendanceRequirementValue'),
				'message' => 'The  provided value is not valide, please enter valid value.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
		 ),
		),
		'lab_attendance_requirement' => array(
			'checkAttendanceRequirementValue' => array(
				'rule' => array('checkAttendanceRequirementValue'),
				'message' => 'The  provided value is not valide, please enter valid value.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
	);

	function courseCodeUnique()
	{
		// check user has enter a valid course code
		$is_course_code_exist = null;
		if (!empty($this->data['Course']['id'])) {
			$is_course_code_exist = $this->find('count', array(
				'conditions' => array(
					'Course.course_code' => trim($this->data['Course']['course_code']), 
					'Course.id <> ' => $this->data['Course']['id'], 
					'Course.curriculum_id' => $this->data['Course']['curriculum_id']
				)
			));
		} else {
			$is_course_code_exist = $this->find('count', array(
				'conditions' => array(
					'Course.course_code' => trim($this->data['Course']['course_code']), 
					'Course.curriculum_id' => $this->data['Course']['curriculum_id']
				)
			));
		}

		if ($is_course_code_exist > 0) {
			return false;
		}

		return true;
	}

	function courseCodeSeparatedByMinus()
	{
		$findme   = '-';
		$pos = strpos($this->data['Course']['course_code'], $findme);
		// Note our use of ===.  Simply == would not work as expected because the position of 'a' was the 0th (first) character.
		if ($pos === false) {
			return false;
		} else {
			return true;
		}
	}

	function checkAttendanceRequirementValue($data = null)
	{
		if (!empty($data['lecture_attendance_requirement'])) {
			if (isset($data['lecture_attendance_requirement'])) {
				if (strlen($data['lecture_attendance_requirement']) > 4) {
					$this->invalidate('attendance', 'The maximum character allowed in lecture attendance requirement is 4. Please adjust like, X% or XX% or XXX% .');
					return false;
				} else if (is_numeric(substr($data['lecture_attendance_requirement'], 0, -1)) != 1) {
					$this->invalidate('attendance', 'The lecture attendance requirement should be numeric. Please provide a numeric value. The value you provided ' . substr($data['lecture_attendance_requirement'], 0, -1) . ' is invalid.');
					return false;
				} else if (substr($data['lecture_attendance_requirement'], -1) != '%') {
					$this->invalidate('attendance', 'Please provide the number in percent in lecture attendance requirement, The value you provided is missing % at the end.');
					return false;
				}
			}
		}
		
		if (!empty($data['lab_attendance_requirement'])) {
			if (isset($data['lab_attendance_requirement'])) {
				if (strlen($data['lab_attendance_requirement']) > 4) {
					$this->invalidate('attendance', 'The maximum character allowed in lab attendance requirement is 4. Please adjust like, X% or XX% or XXX% .');
					return false;
				} else if (is_numeric(substr($data['lab_attendance_requirement'], 0, -1)) != 1) {
					$this->invalidate('attendance', 'The lab attendance requirement should be numeric. Please provide a numeric value. The value you provided ' . substr($data['lab_attendance_requirement'], 0, -1) . ' is invalid.');
					return false;
				} else if (substr($data['lab_attendance_requirement'], -1) != '%') {
					$this->invalidate('attendance', 'Please provide the number in percent in lab attendance requirement, The value you provided is missing % at the end.');
					return false;
				}
			}
		}
		return true;
	}

	var $belongsTo = array(
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'curriculum_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseCategory' => array(
			'className' => 'CourseCategory',
			'foreignKey' => 'course_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GradeType' => array(
			'className' => 'GradeType',
			'foreignKey' => 'grade_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasAndBelongsToMany = array(
		'Staff' => array(
			'className' => 'Staff',
			'joinTable' => 'courses_staffs',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'staff_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Student' => array(
			'className' => 'Student',
			'joinTable' => 'courses_students',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'student_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	var $hasMany = array(
		'CourseForSubstitued' => array(
			'className' => 'EquivalentCourse',
			'foreignKey' => 'course_for_substitued_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CourseBeSubstitued' => array(
			'className' => 'EquivalentCourse',
			'foreignKey' => 'course_be_substitued_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'GraduationWork' => array(
			'className' => 'GraduationWork',
			'foreignKey' => 'course_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Prerequisite' => array(
			'className' => 'Prerequisite',
			'foreignKey' => 'course_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Book' => array(
			'className' => 'Book',
			'foreignKey' => 'course_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Journal' => array(
			'className' => 'Journal',
			'foreignKey' => 'course_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Weblink' => array(
			'className' => 'Weblink',
			'foreignKey' => 'course_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'course_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
		/*'CourseInstructorAssignment' => array(
		    'className' => 'CourseInstructorAssignment',
			'foreignKey' => 'published_course_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),*/

	);

	function canItBeDeleted($course_id = null)
	{
		if ($this->Prerequisite->find('count', array('conditions' => array('Prerequisite.course_id' => $course_id))) > 0) {
			return false;
		} else if ($this->Book->find('count', array('conditions' => array('Book.course_id' => $course_id))) > 0) {
			return false;
		} else if ($this->Journal->find('count', array('conditions' => array('Journal.course_id' => $course_id))) > 0) {
			return false;
		} else if ($this->Weblink->find('count', array('conditions' => array('Weblink.course_id' => $course_id))) > 0) {
			return false;
		} else if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.course_id' => $course_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function not_allow_more_than_one_thesis($data = null)
	{
		if (isset($data['Course']) && $data['Course']['thesis'] == 1) {
			if (!empty($data['Course']['id'])) {
				$check_other_thesis_defined_for_curriculum = $this->find('count', array(
					'conditions' => array(
						'Course.thesis' => 1,
						'Course.curriculum_id' => $data['Course']['curriculum_id'],
						'Course.semester' => $data['Course']['semester'],
						'Course.id !=' . $data['Course']['id'] . ''
					)
				));

			} else {
				$check_other_thesis_defined_for_curriculum = $this->find('count', array(
					'conditions' => array(
						'Course.thesis' => 1,
						'Course.curriculum_id' => $data['Course']['curriculum_id'],
						'Course.semester' => $data['Course']['semester']
					)
				));
			}

			$curriculums = $this->Curriculum->find('list', array('fields' => array('Curriculum.curriculum_detail'), 'conditions' => array('Curriculum.id' => $data['Course']['curriculum_id'])));

			if ($check_other_thesis_defined_for_curriculum) {
				$this->invalidate('thesis_error', 'You can not define more than one thesis for ' . $curriculums[$data['Course']['curriculum_id']] . ' curriculum. Please uncheck thesis.');
				return false;
			}
		}
		return true;
	}

	function not_allow_more_than_one_exit_exam($data = null)
	{
		if (isset($data['Course']) && $data['Course']['exit_exam'] == 1) {
			if (!empty($data['Course']['id'])) {
				$check_other_exit_exam_defined_for_curriculum = $this->find('count', array(
					'conditions' => array(
						'Course.exit_exam' => 1,
						'Course.curriculum_id' => $data['Course']['curriculum_id'],
						'Course.semester' => $data['Course']['semester'],
						'Course.id !=' . $data['Course']['id'] . ''
					)
				));
				
			} else {
				$check_other_exit_exam_defined_for_curriculum = $this->find('count', array(
					'conditions' => array(
						'Course.exit_exam' => 1,
						'Course.curriculum_id' => $data['Course']['curriculum_id'],
						//'Course.semester' => $data['Course']['semester']
					)
				));
			}

			$curriculums = $this->Curriculum->find('list', array('fields' => array('Curriculum.curriculum_detail'), 'conditions' => array('Curriculum.id' => $data['Course']['curriculum_id'])));

			if ($check_other_exit_exam_defined_for_curriculum) {
				$this->invalidate('exit_exam_error', 'You can not define more than one Exit Exam for ' . $curriculums[$data['Course']['curriculum_id']] . ' curriculum. Please uncheck Exit Exam.');
				return false;
			}
		}
		return true;
	}

	function unset_empty($data = null)
	{
		if (!empty($data)) {
			// prerequise prepared for save all
			if (!empty($data['Prerequisite'])) {
				$extra_prerequisite_field = false;
				foreach ($data['Prerequisite'] as $pk => &$pv) {
					if (!isset($pv['prerequisite_course_id']) || strcasecmp($pv['prerequisite_course_id'], 'none') == 0 || empty($pv['prerequisite_course_id'])) {
						unset($data['Prerequisite'][$pk]);
					} else {
						$extra_prerequisite_field = true;
					}
				}

				if (!$extra_prerequisite_field) {
					unset($data['Prerequisite']);
				}
			}
			//book prepared for save all

			if (!empty($data['Book'])) {
				$extra_book_field = false;
				foreach ($data['Book'] as $bk => &$bv) {
					if (empty($bv['ISBN']) && empty($bv['title']) && empty($bv['publisher']) && empty($bv['edition']) && empty($bv['author']) && empty($bv['place_of_publication']) && empty($bv['year_of_publication'])) {
						unset($data['Book'][$bk]);
					} else {
						$extra_book_field = true;
					}
				}
				if (!$extra_book_field) {
					unset($data['Book']);
				}
			}

			//Journal prepared for save all
			if (!empty($data['Journal'])) {
				$extra_journal_field = false;
				foreach ($data['Journal'] as $jk => &$jv) {
					if (empty($jv['journal_title']) && empty($jv['article_title']) && empty($jv['author']) && empty($jv['url_address']) && empty($jv['volume']) && empty($jv['issue']) && empty($jv['page_number']) && empty($jv['ISBN'])) {
						unset($data['Journal'][$jk]);
					} else {
						$extra_journal_field = true;
					}
				}
				if (!$extra_journal_field) {
					unset($data['Journal']);
				}
			}

			//Weblink prepared for save all
			if (!empty($data['Weblink'])) {
				$extra_weblink_field = false;
				foreach ($data['Weblink'] as $wk => &$wv) {
					if (empty($wv['title']) && empty($wv['url_address']) && empty($wv['author']) && empty($wv['year'])) {
						unset($data['Weblink'][$wk]);
					} else {
						$extra_weblink_field = true;
					}
				}
				if (!$extra_weblink_field) {
					unset($data['Weblink']);
				}
			}
			return $data;
		}
	}

	function unset_empty_for_copy($data = null)
	{
		if (!empty($data)) {
			// prepared prerequisite/book/journaland weblink for save all
			foreach ($data['Course'] as &$each_data) {

				if (count($each_data['Prerequisite']) == 0) {
					unset($each_data['Prerequisite']);
				} else {
					foreach ($each_data['Prerequisite'] as &$each_prerequisite) {
						unset($each_prerequisite['id']);
						unset($each_prerequisite['course_id']);
						unset($each_prerequisite['created']);
						unset($each_prerequisite['modified']);
					}
				}

				if (count($each_data['Book']) == 0) {
					unset($each_data['Book']);
				} else {
					foreach ($each_data['Book'] as &$each_book) {
						unset($each_book['id']);
						unset($each_book['course_id']);
						unset($each_book['created']);
						unset($each_book['modified']);
					}
				}

				if (count($each_data['Journal']) == 0) {
					unset($each_data['Journal']);
				} else {
					foreach ($each_data['Journal'] as &$each_journal) {
						unset($each_journal['id']);
						unset($each_journal['course_id']);
						unset($each_journal['created']);
						unset($each_journal['modified']);
					}
				}

				if (count($each_data['Weblink']) == 0) {
					unset($each_data['Weblink']);
				} else {
					foreach ($each_data['Weblink'] as &$each_weblink) {
						unset($each_weblink['id']);
						unset($each_weblink['course_id']);
						unset($each_weblink['created']);
						unset($each_weblink['modified']);
					}
				}
			}
			return $data;
		}
	}

	function saveAllFormatCopyCourse($data = null)
	{
		$dataFormate = array();

		if (!empty($data)) {
			$dataFormate = $data;
			// prepared prerequisite/book/journaland weblink for save all
			foreach ($dataFormate as &$each_data) {
				unset($each_data['Course']['id']);
				unset($each_data['Course']['created']);
				unset($each_data['Course']['modified']);

				if (count($each_data['Prerequisite']) == 0) {
					unset($each_data['Prerequisite']);
				} else {
					foreach ($each_data['Prerequisite'] as &$each_prerequisite) {
						unset($each_prerequisite['id']);
						unset($each_prerequisite['course_id']);
						unset($each_prerequisite['created']);
						unset($each_prerequisite['modified']);
					}
				}

				if (count($each_data['Book']) == 0) {
					unset($each_data['Book']);
				} else {
					foreach ($each_data['Book'] as &$each_book) {
						unset($each_book['id']);
						unset($each_book['course_id']);
						unset($each_book['created']);
						unset($each_book['modified']);
					}
				}

				if (count($each_data['Journal']) == 0) {
					unset($each_data['Journal']);
				} else {
					foreach ($each_data['Journal'] as &$each_journal) {
						unset($each_journal['id']);
						unset($each_journal['course_id']);
						unset($each_journal['created']);
						unset($each_journal['modified']);
					}
				}

				if (count($each_data['Weblink']) == 0) {
					unset($each_data['Weblink']);
				} else {
					foreach ($each_data['Weblink'] as &$each_weblink) {
						unset($each_weblink['id']);
						unset($each_weblink['course_id']);
						unset($each_weblink['created']);
						unset($each_weblink['modified']);
					}
				}
			}
			return $dataFormate;
		}
		return $dataFormate;
	}

	function getGradeScaleDetails($course_id = null, $college_id = null, $active = 1, $own = 0)
	{
		$course_detail = $this->find('first', array('conditions' => array('Course.id' => $course_id), 'contain' => array('Curriculum')));

		if ($course_detail['Course']['grade_type_id'] == 0 || $course_detail['Course']['grade_type_id'] == "") {
			return false;
		} else {
			return $this->GradeType->getGradeScaleDetails($course_detail['Course']['grade_type_id'], $course_detail['Curriculum']['program_id'], 'College', $college_id, $active, $own);
		}
	}

	function denyEditDeleteCredit($course_id = null)
	{
		$published_course_ids = $this->PublishedCourse->find('list', array('conditions' => array('PublishedCourse.course_id' => $course_id), 'fields' => 'id'));

		if (empty($published_course_ids)) {
			return 0;
		}

		$course_registration_ids = $this->PublishedCourse->CourseRegistration->find('list', array('conditions' => array('CourseRegistration.published_course_id' => $published_course_ids), 'fields' => 'id'));

		if (!empty($course_registration_ids)) {
			$isExamGradeSubmitted = $this->PublishedCourse->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $course_registration_ids)));
		} else {
			$isExamGradeSubmitted = 0;
		}

		if ($isExamGradeSubmitted > 0) {
			return 1;
		} else {
			$course_add_ids = $this->PublishedCourse->CourseAdd->find('list', array('conditions' => array('CourseAdd.published_course_id' => $published_course_ids)));
			if (!empty($course_add_ids)) {
				$isExamGradeSubmitted = $this->PublishedCourse->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_add_id' => $course_add_ids)));
			} else {
				$isExamGradeSubmitted = 0;
			}
			if ($isExamGradeSubmitted > 0) {
				return 1;
			}
			return 0;
		}
	}

	function denyEditDeleteCourseBasicDetailChange($course_id = null)
	{
		$published_course_ids = $this->PublishedCourse->find('list', array('conditions' => array('PublishedCourse.course_id' => $course_id), 'fields' => 'id'));

		if (empty($published_course_ids)) {
			return 0;
		}

		$registration_ids = $this->PublishedCourse->CourseRegistration->find('list', array('conditions' => array('CourseRegistration.published_course_id' => $published_course_ids), 'fields' => 'id'));
		$course_add_ids = $this->PublishedCourse->CourseAdd->find('list', array('conditions' => array('CourseAdd.published_course_id' => $published_course_ids)));

		if (empty($registration_ids) && empty($course_add_ids)) {
			return 0;
		}

		$student_registered_ids = $this->PublishedCourse->CourseRegistration->find('list', array('conditions' => array('CourseRegistration.id' => $registration_ids), 'fields' => 'student_id'));
		//debug($student_registered_ids);

		$student_add_ids = $this->PublishedCourse->CourseAdd->find('list', array('conditions' => array('CourseAdd.id' => $course_add_ids), 'fields' => 'student_id'));

		$isGraduatedRegistred = $this->PublishedCourse->CourseRegistration->Student->GraduateList->find('count', array('conditions' => array('GraduateList.student_id' => $student_add_ids)));
		$isGraduatedAdd = $this->PublishedCourse->CourseAdd->Student->GraduateList->find('count', array('conditions' => array('GraduateList.student_id' => $student_registered_ids)));

		if ($isGraduatedRegistred > 0 || $isGraduatedAdd > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function getCourseByExamGradeId($exam_grade_id = null)
	{
		$exam_grade_detail = $this->PublishedCourse->CourseRegistration->ExamGrade->find('first', array(
			'conditions' => array(
				'ExamGrade.id' => $exam_grade_id
			),
			'contain' => array(
				'CourseAdd' => array(
					'PublishedCourse' => array('Course')
				),
				'CourseRegistration' => array(
					'PublishedCourse' => array('Course')
				)
			)
		));

		$course = null;

		if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']['Course']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse']['Course'])) {
			$course = $exam_grade_detail['CourseRegistration']['PublishedCourse']['Course'];
		} else if (isset($exam_grade_detail['CourseAdd']['PublishedCourse']['Course']) && !empty($exam_grade_detail['CourseAdd']['PublishedCourse']['Course'])) {
			$course = $exam_grade_detail['CourseAdd']['PublishedCourse']['Course'];
		}
		return $course;
	}

	function getTakenEquivalentCourses($student_id = null, $course_id = null, $ay_and_s_list = array())
	{
		$matching_courses[] = $course_id;

		$options = array();

		if (!empty($ay_and_s_list)) {
			foreach ($ay_and_s_list as $key => $ay_and_s) {
				$options['conditions']['OR'][] = array(
					'CourseRegistration.academic_year' => $ay_and_s['academic_year'],
					'CourseRegistration.semester' => $ay_and_s['semester']
				);
			}
		}

		$student_department = $this->PublishedCourse->CourseAdd->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		$course_department = $this->find('first', array('conditions' => array('Course.id' => $course_id ), 'recursive' => -1 ));

		if (!empty($student_department['Student']['department_id']) && isset($course_department['Course']['curriculum_id'])) {
			// If the course is main course for the department. If it is, then we are going to concentrate on its equivalent.
			if ($student_department['Student']['department_id'] == $course_department['Course']['department_id'] && $student_department['Student']['curriculum_id'] == $course_department['Course']['curriculum_id']) {

				$course_be_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array('conditions' => array('EquivalentCourse.course_for_substitued_id' => $course_id), 'recursive' => -1));

				/* $course_for_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array(
					'conditions' => array(
						'EquivalentCourse.course_be_substitued_id' => $course_id
					),
					'recursive' => -1
				)); */
				
				//debug($course_for_substitueds);

				if (!empty($course_be_substitueds)) {
					foreach ($course_be_substitueds as $key => $value) {
						$matching_courses[] = $value['EquivalentCourse']['course_be_substitued_id'];
					}
				}

				//debug($matching_courses);

			} else {
				// If the course is from other department then we are going to look for its equivalent department course 
				$course_for_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array('conditions' => array('EquivalentCourse.course_be_substitued_id' => $course_id), 'recursive' => -1));

				if (!empty($course_for_substitueds)) {
					foreach ($course_for_substitueds as $key => $value) {
						$course_detail = $this->find('first', array('conditions' => array('Course.id' => $value['EquivalentCourse']['course_for_substitued_id']), 'contain' => array('Curriculum')));
						if ($course_detail['Curriculum']['department_id'] == $student_department['Student']['department_id']) {
							$matching_courses[] = $value['EquivalentCourse']['course_for_substitued_id'];
						}
					}
				}
			}
		}

		//find out those courses student has registered of the matching courses and add to matching courses list
		$registeredMatched = $this->PublishedCourse->CourseRegistration->find('all', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'contain' => array('PublishedCourse'),
			'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC')
		));

		$addedMatched = $this->PublishedCourse->CourseAdd->find('all', array(
			'conditions' => array(
				'CourseAdd.student_id' => $student_id,
				'CourseAdd.registrar_confirmation' => 1
			),
			'contain' => array('PublishedCourse'),
			'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
		));

		if (!empty($registeredMatched)) {
			foreach ($registeredMatched as $rkey => $rvalue) {
				# check if the given course and matching found is in equivalent mood
				$equivalenceMapped = ClassRegistry::init('EquivalentCourse')->find('count', array(
					'conditions' => array(
						'EquivalentCourse.course_be_substitued_id' => $rvalue['PublishedCourse']['course_id'],
						'EquivalentCourse.course_for_substitued_id' => $course_id
					),
					'recursive' => -1
				));

				if ($equivalenceMapped) {
					$matching_courses[] = $rvalue['PublishedCourse']['course_id'];
				}
			}
		}

		if (!empty($addedMatched)) {
			//debug($addedMatched);
			foreach ($addedMatched as $akey => $avalue) {
				# check if the given course and matching found is in equivalent mood
				$equivalenceMapped = ClassRegistry::init('EquivalentCourse')->find('count', array(
					'conditions' => array(
						'EquivalentCourse.course_be_substitued_id' => $avalue['PublishedCourse']['course_id'],
						'EquivalentCourse.course_for_substitued_id' => $course_id
					),
					'recursive' => -1
				));

				if ($equivalenceMapped) {
					$matching_courses[] = $rvalue['PublishedCourse']['course_id'];
				}
			}
		}

		//debug($matching_courses);
		return $matching_courses;
	}

	function getCourseTitle($title = null)
	{
		$courses = $this->find('list', array('conditions' => array('Course.course_title LIKE ' => trim($title) . '%'), 'fields' => array('Course.course_title')));
		return $courses;
	}

	function isEquivalenCourseTakenHaveRecentGrade($course_id, $student_id)
	{
		$studentDetail = $this->PublishedCourse->CourseRegistration->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));

		$matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_id, $studentDetail['Student']['curriculum_id']);

		if (empty($matching_courses)) {
			return false;
		} else {
			foreach ($matching_courses as $mk => $mv) {
				if ($this->isCourseTakenHaveRecentGrade($student_id, $mv)) {
					return true;
				}
			}
		}
		return false;
	}

	function isCourseTakenHaveRecentPassGrade($student_id, $course_id)
	{
		$gradePass = null;

		$publishedUnderRegistration = $this->PublishedCourse->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.published_course_id in (select id from published_courses where course_id="' . $course_id . '")'
			),
			//'order' => 'CourseRegistration.created DESC' // backdated grades affect this as they save the date to the begning of the academic year semester, Neway
			'order' => array('CourseRegistration.id' => 'DESC')
		));

		$publishedUnderAdds = $this->PublishedCourse->CourseAdd->find('first', array(
			'conditions' => array(
				'CourseAdd.student_id' => $student_id,
				'CourseAdd.published_course_id in (select id from published_courses where course_id="' . $course_id . '")'
			),
			//'order' => 'CourseAdd.created DESC' // backdated grades affect this as they save the date to the begning of the academic year semester, Neway
			'order' => array('CourseAdd.id' => 'DESC') 
		));

		if (!empty($publishedUnderRegistration)) {
			$gradePass = $this->PublishedCourse->CourseRegistration->ExamGrade->getApprovedGrade($publishedUnderRegistration['CourseRegistration']['id'], 1);
			if (!empty($gradePass) && $gradePass['pass_grade'] != 0) {
				return true;
			}
		}

		if (!empty($publishedUnderAdds)) {
			$gradePass = $this->PublishedCourse->CourseAdd->ExamGrade->getApprovedGrade($publishedUnderAdds['CourseAdd']['id'], 0);
			if (!empty($gradePass) && $gradePass['pass_grade'] != 0) {
				return true;
			}
		}
		//debug($gradePass);

		return false;
	}

	function isCourseTakenHaveRecentGrade($student_id, $course_id)
	{
		$register_and_add_freq = array();
		$studentDetail = $this->PublishedCourse->CourseRegistration->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		$matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_id, $studentDetail['Student']['curriculum_id']);
		$courses_separated_by_coma = join(', ', $matching_courses);

		//debug($courses_separated_by_coma);
		//debug($course_id);

		if (!empty($matching_courses) && !empty($courses_separated_by_coma)) {

			$registration_freq = $this->PublishedCourse->CourseRegistration->find('all', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.published_course_id in (select id from published_courses where course_id in (' . $courses_separated_by_coma . '))'
				),
				'contain' => array('PublishedCourse'),
				'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC')
			));

			//debug($registration_freq);

			$add_freq = $this->PublishedCourse->CourseAdd->find('all', array(
				'conditions' => array(
					'CourseAdd.student_id' => $student_id,
					'CourseAdd.department_approval = 1',
					'CourseAdd.registrar_confirmation = 1',
					'CourseAdd.published_course_id in (select id from published_courses where course_id in (' . $courses_separated_by_coma . '))'
				),
				'contain' => array('PublishedCourse'),
				'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
			));

			//debug($add_freq);

			//merging course registration and add

			if (!empty($registration_freq)) {
				foreach ($registration_freq as $key2 => $value2) {
					if (!$this->PublishedCourse->CourseRegistration->isCourseDroped($value2['CourseRegistration']['id']) && !empty($value2['PublishedCourse']['id'])) {
						$m_index = count($register_and_add_freq);
						$register_and_add_freq[$m_index]['id'] = $value2['CourseRegistration']['id'];
						$register_and_add_freq[$m_index]['type'] = 'register';
						$register_and_add_freq[$m_index]['created'] = $value2['CourseRegistration']['created'];
					}
				}
			}

			if (!empty($add_freq)) {
				foreach ($add_freq as $key2 => $value2) {
					if (!empty($value2['PublishedCourse']['id'])) {
						$m_index = count($register_and_add_freq);
						$register_and_add_freq[$m_index]['id'] = $value2['CourseAdd']['id'];
						$register_and_add_freq[$m_index]['type'] = 'add';
						$register_and_add_freq[$m_index]['created'] = $value2['CourseAdd']['created'];
					}
				}
			}

			//Sorting by date
			if (!empty($register_and_add_freq)) {
				for ($i = 0; $i < count($register_and_add_freq); $i++) {
					for ($j = $i + 1; $j < count($register_and_add_freq); $j++) {
						if ($register_and_add_freq[$i]['created'] > $register_and_add_freq[$j]['created']) {
							$tmp = $register_and_add_freq[$i];
							$register_and_add_freq[$i] = $register_and_add_freq[$j];
							$register_and_add_freq[$j] = $tmp;
						}
					}
				}
			}
		}

		if (count($register_and_add_freq) > 0) {
			return true;
		}

		return false;
	}

	function getRepeatableGradeGivenPublishedCourse($pid = null)
	{
		$course = $this->PublishedCourse->find('first', array(
			'conditions' => array(
				'PublishedCourse.id' => $pid
			),
			'contain' => array('Course' => array('GradeType' => array('Grade')))
		));

		debug($course);
		$repeatableGrades = array();
		$repeatableGrades['I'] = 'I';
		$repeatableGrades['NG'] = 'NG';
		$repeatableGrades['W'] = 'W';
		$repeatableGrades['DO'] = 'DO';
		$repeatableGrades['F'] = 'F';

		if (!empty($course)) {
			foreach ($course['Course']['GradeType']['Grade'] as $k => $v) {
				if ($v['allow_repetition']) {
					$repeatableGrades[$v['grade']] = $v['grade'];
				}
			}
		}
		return $repeatableGrades;
	}
}
