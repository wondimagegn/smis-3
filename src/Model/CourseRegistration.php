<?php
class CourseRegistration extends AppModel
{
	var $name = 'CourseRegistration';

	//public $actsAs = array('Tools.Logable');
	var $actsAs = array(
		'Tools.Logable' => array(
			'change' => 'full',
			//'change' => 'list',
			'description_ids' => 'true',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	var $validate = array(
		'year_level_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'student_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'course_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
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
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'section_id',
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
		'AcademicCalendar' => array(
			'className' => 'AcademicCalendar',
			'foreignKey' => 'academic_calendar_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ExamResult' => array(
			'className' => 'ExamResult',
			'foreignKey' => 'course_registration_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ExamGrade' => array(
			'className' => 'ExamGrade',
			'foreignKey' => 'course_registration_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ExcludedCourseFromTranscript' => array(
			'className' => 'ExcludedCourseFromTranscript',
			'foreignKey' => 'course_registration_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseDrop' => array(
			'className' => 'CourseDrop',
			'foreignKey' => 'course_registration_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MakeupExam' => array(
			'className' => 'MakeupExam',
			'foreignKey' => 'course_registration_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ResultEntryAssignment' => array(
			'className' => 'ResultEntryAssignment',
			'foreignKey' => 'course_registration_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	// Function to check already registered
	function alreadyRegistred($semester = null, $academic_year = null, $student_id = null)
	{
		$already_registered = $this->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year LIKE' => $academic_year . '%', 
				'CourseRegistration.student_id' => $student_id, 
				'CourseRegistration.semester' => $semester
			)
		));
		return $already_registered;
	}

	function latestCourseRegistrationSemester($academic_year = null, $student_id = null)
	{
		$semester = "I";

		if (!empty($academic_year) && !empty($student_id)) {
			$latestSemester = $this->find('first', array(
				'conditions' => array(
					'CourseRegistration.academic_year like ' => $academic_year . '%',
					'CourseRegistration.student_id' => $student_id
				),
				'fields' => array('CourseRegistration.semester', 'CourseRegistration.academic_year'), 
				'order' => array('CourseRegistration.academic_year DESC', 'CourseRegistration.semester  DESC', 'CourseRegistration.id DESC'), 
			));

			if (empty($latestSemester)) {
				return $semester;
			} else {
				//debug($latestSemester['CourseRegistration']['semester']);
				return $latestSemester['CourseRegistration']['semester'];
			}
		} else if (!empty($academic_year)) {
			$latestSemester = $this->find('first', array(
				'conditions' => array(
					'CourseRegistration.academic_year like ' => $academic_year . '%'
				),
				'fields' => array('CourseRegistration.semester', 'CourseRegistration.academic_year'),
				'order' => array('CourseRegistration.academic_year DESC', 'CourseRegistration.semester  DESC', 'CourseRegistration.id DESC'), 
			));

			if (empty($latestSemester)) {
				return $semester;
			} else {
				return $latestSemester['CourseRegistration']['semester'];
			}
		}

		//to costy for checking latest semester of ac year without student ID, Neway.

		/* if (!empty($latestSemester)) {
			foreach ($latestSemester as $k => &$v) {
				if (strcasecmp($v['CourseRegistration']['semester'], $semester) > 0) {
					$semester = $v['CourseRegistration']['semester'];
				}
			}
		} */

		return $semester;
	}

	function futureAcademicYearCoursePublished()
	{
		$latestSemester = $this->PublishedCourse->find('all', array('fields' => array('PublishedCourse.academic_year')));
	}
	
	function TakenPrequisteCourse($course_id = null, $student_id = null)
	{

		//does the course has prerequist 
		if ($this->isPrerequisteExist($course_id) === true) {
			if ($this->prequisite_taken($student_id, $course_id) === false) {
				return 4; //  no prerquiste 
			}
		}

		/*
	    $courseregistrationdetail = $this->find('all',array(
			'conditions' => array(
	        	'CourseRegistration.student_id' => $student_id,
	        	'CourseRegistration.published_course_id' => $publishedCourseId['PublishedCourse']['id']
			),
	        'contain'=>'ExamResult'
		));
	    if (empty($courseregistrationdetail['ExamResult'])) {
	        return "GRADE NOT SUBMITTED";
	    } else {
	        $doesHeShePassed = $this->ExamResult->calculateGradeAndReturnPassOrFail($courseregistrationdetail['ExamResult']);
	        return $doesHeShePassed;
	    }
	    */
	}

	// Function that computes the maximum semester and academic year
	// return array of latest semester and academic year.
	function latest_academic_year_semester($academic_year = null, $student_program_id = null, $student_program_type_id = null)
	{
		$ac_semester = array();
		
		if ($academic_year) {

			if (!empty($student_program_id) && !empty($student_program_type_id)) {
				$latest_semester = $this->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $academic_year . '%',
						'PublishedCourse.program_id' => $student_program_id,
						'PublishedCourse.program_type_id' => $student_program_type_id
					),
					'contain' => array(),
					'fields' => array("MAX(PublishedCourse.created)", 'PublishedCourse.semester', 'PublishedCourse.academic_year'),
					'group' => 'PublishedCourse.semester',
					'order' => "MAX(PublishedCourse.created) desc",
				));
			} else {
				$latest_semester = $this->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $academic_year . '%'
					), 
					'contain' => array(),
					'fields' => array("MAX(PublishedCourse.created)", 'PublishedCourse.semester', 'PublishedCourse.academic_year'),
					'group' => 'PublishedCourse.semester',
					'order' => "MAX(PublishedCourse.created) desc",
				));
			}
		}

		//$semester=$latest_semester['PublishedCourse']['semester']; 
		if (isset($latest_semester['PublishedCourse']['semester']) && !empty($latest_semester)) {
			$ac_semester['semester'] = $latest_semester['PublishedCourse']['semester'];
			$ac_semester['academic_year'] = $latest_semester['PublishedCourse']['academic_year'];
		}

		return $ac_semester;
	}

	// Function that computes the maximum semester and academic year 
	function latest_semester_of_section($section_id = null, $current_academic_year = null)
	{
		$ac_semester = array();

		$publishedcourses = $this->PublishedCourse->find('all', array(
			'conditions' => array(
				'PublishedCourse.academic_year like ' => $current_academic_year . '%',
				'PublishedCourse.section_id' => $section_id,
				'PublishedCourse.drop=0'
			)
		));

		$published_course_ids = array();

		if (!empty($publishedcourses)) {
			foreach ($publishedcourses as $index => $value) {
				$grade_submitted = $this->ExamGrade->is_grade_submitted($value['PublishedCourse']['id']);
				$registred_count = $this->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $value['PublishedCourse']['id'])));
				
				if ($grade_submitted) {
					//do nothing ;
				} else {
					$published_course_ids[] = $value['PublishedCourse']['id'];
				}
			}
		}

		if (!empty($published_course_ids)) {
			$publishedcourses = $this->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.academic_year like ' => $current_academic_year . '%',
					'PublishedCourse.id' => $published_course_ids,
					'PublishedCourse.drop=0'
				), 'recursive' => -1
			));

			return $publishedcourses['PublishedCourse']['semester'];
		}
		return 2;
	}

	// Check course publication for split section 
	function checkCourseIsPublishedForSection($section_id = null, $current_academic_year = null)
	{
		$ac_semester = array();

		$publishedcourses = $this->PublishedCourse->find('all', array(
			'conditions' => array(
				'PublishedCourse.academic_year' => $current_academic_year,
				'PublishedCourse.section_id' => $section_id
			), 
			'recursive' => -1
		));

		if (empty($publishedcourses)) {
			return 2; // allow split  
		}

		$published_course_ids = array();

		if (!empty($publishedcourses)) {
			foreach ($publishedcourses as $index => $value) {
				$grade_submitted = $this->ExamGrade->is_grade_submitted($value['PublishedCourse']['id']);
				$registred_count = $this->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $value['PublishedCourse']['id'])));
				
				if ($grade_submitted) {
					//do nothing ;
				} else {
					$published_course_ids[] = $value['PublishedCourse']['id'];
				}
			}
		}

		if (!empty($published_course_ids)) {
			$publishedcourses = $this->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.academic_year' => $current_academic_year,
					'PublishedCourse.section_id' => $section_id,
					'PublishedCourse.id' => $published_course_ids
				), 
				'recursive' => -1
			));

			return $publishedcourses['PublishedCourse']['semester'];
		}
		return 2; 
		// allow split 
	}

	function checkMergingIsPossible($new_section_id = null, $section_ids = array(), $current_academic_year = null ) 
	{
		/*
		* 1. Get section history, is the section to be merged first, second, third
		* 2. Does the section has forward year level then deny merging 
		* 3. Check selected section has same courses taken through out section upgrade history
		* 4. If grade is submitted to any of the published course of merge request section, deny  merge 
	   	*/

		$publishedcourses = array();
		
		if (!empty($section_ids)) {
			$this->Student->bindModel(
				array(
					'hasMany' => array(
						'StudentsSection' => array(
							'className' => 'StudentsSection',
						)
					)
				)
			);

			$representativeStudentOfSection = array();
			$representativeSectionStudent = array();
			$sectionEarlierYearLevels = array();

			foreach ($section_ids as $k => $v) {
				$students = $this->Student->StudentsSection->find('first', array(
					'conditions' => array(
						'StudentsSection.section_id' => $v,
						'StudentsSection.student_id not in (select student_id from readmissions) '
					),
					'recursive' => -1
				));
				
				$representativeStudentOfSection[$v] = $students['StudentsSection']['student_id'];
				$representativeSectionStudent[$students['StudentsSection']['student_id']] = $v;
			}

			$studentsSectionHistory = $this->Student->find('all', array(
				'conditions' => array(
					'Student.id' => $representativeStudentOfSection,
					'Student.id not in (select student_id from readmissions) '
				),
				'fields' => array('Student.id'),
				'contain' => array('StudentsSection')
			));

			foreach ($studentsSectionHistory as $k => $v) {
				foreach ($v['StudentsSection'] as $sk => $sv) {
					if ($representativeSectionStudent[$v['Student']['id']] != $sv['section_id']) {
						$EarlierSections[$representativeSectionStudent[$v['Student']['id']]][] = $sv['section_id'];
					}
				}
			}

			// check forward year level, if in case the merge is for earlier year
			// deny the merge to prevent inconsistency 
			$coursesTakenThrughout = array();

			foreach ($section_ids as $k => $v) {
				$yearLevelOfMergerSection = $this->Section->find('first', array('conditions' => array('Section.id' => $v), 'contain' => array('YearLevel')));

				if (isset($EarlierSections[$v])) {
					foreach ($EarlierSections[$v] as $EK => $EV) {
						// check the merged section id has higher year level 
						$yearLevelOfEarlierSection = $this->Section->find('first', array(
							'conditions' => array('Section.id' => $EV), 
							'contain' => array('YearLevel')
						));

						// allow only recent year level of the section for merging 
						if (strcmp($yearLevelOfEarlierSection['YearLevel']['name'], $yearLevelOfMergerSection['YearLevel']['name']) > 0) {

							// Merging of the selected  section is not possible because year level upgrade has been 
							// performed and courses published for the section deny the merge request.

							return 2;
						}
						$coursesTakenThrughout[$v][$EV] = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.section_id' => $EV, 
								'PublishedCourse.drop' => 0
							),
							'fields' => array('PublishedCourse.course_id'), 
							'contain' => array('Course')
						));
					}

					$coursesTakenThrughout[$v][$v] = $this->PublishedCourse->find('all', array(
						'conditions' => array(
							'PublishedCourse.section_id' => $v, 
							'PublishedCourse.drop' => 0
						),
						'fields' => array('PublishedCourse.course_id'), 
						'contain' => array('Course')
					));
				}
			}

			//Check selected section has same courses taken through out section upgrade history
			$takenCourseOrganizedByCourseId = array();
			foreach ($section_ids as $k => $v) {
				if (isset($coursesTakenThrughout[$v])) {
					foreach ($coursesTakenThrughout[$v] as $kk => $vv) {
						foreach ($vv as $kkk => $vvv) {
							$takenCourseOrganizedByCourseId[$v][] = $vvv['Course']['id'];
						}
					}
				}
			}

			//compare the array, if there is difference in array, deny the merge
			foreach ($section_ids as $k => $v) {
				//TODO:  return 3; // course taken different   
				// use compare array, array_diff, to find out  takenCourseOrganizedByCourseId is differ
				foreach ($section_ids as $kk => $vv) {
					if (isset($takenCourseOrganizedByCourseId[$v])) {
						$result = array_diff($takenCourseOrganizedByCourseId[$v], $takenCourseOrganizedByCourseId[$vv]);
						if (!empty($result)) {
							return 3;
						}
					}
				}
			}

			// Now the number of courses taken is similar, we need to check the  published courses in given academic year. 
			// If grade submission of any published course is started, deny the merge request
			// If grade submission is not started, then merge the section , and  update, published_courses section_id , course_registration section_id,  Course_Instructor_Assignment section_id by the new merged section_id 

			foreach ($section_ids as $k => $v) {

				$tmp = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.academic_year' => $current_academic_year,
						'PublishedCourse.section_id' => $v
					),
					'fields' => array('PublishedCourse.id'),
					'recursive' => -1
				));

				foreach ($tmp as $ppin => $ppvalue) {
					$grade_submitted = $this->ExamGrade->is_grade_submitted($ppvalue['PublishedCourse']['id']);
					if ($grade_submitted) {
						return 4; 
						// grade is submitted to one course so merging is not possible right now, you  can merge them in the next year level 
					}
					$publishedcourses[] = $ppvalue['PublishedCourse']['id'];
				}
			}

			return $publishedcourses;
		}
	}

	function updateCourseRegistrationPublishedCourseInstructorAssignmentAfterSectionMerge($publishedCourseIds = array(), $newSectionId) 
	{
		// do sql update, using query() function, it is fast, dont use saveAll in here 
		if (!empty($publishedCourseIds)) {

			$publishedCourseCommaSeparted =  join(', ', $publishedCourseIds);
			
			// update published courses section with the new section  
			$this->query("UPDATE  published_courses SET section_id = " . $newSectionId . " WHERE id in (" . $publishedCourseCommaSeparted . ")");

			// update course registrations section with the new section  
			$this->query("UPDATE course_registrations SET section_id = " . $newSectionId . " WHERE published_course_id in (" . $publishedCourseCommaSeparted . ")");

			// update course_instructor_assignments section with the new section  
			$this->query("UPDATE course_instructor_assignments SET section_id = " . $newSectionId . " WHERE published_course_id in (" . $publishedCourseCommaSeparted . ")");
		}
	}

	function getCourseForPublishedForSectionMerge($new_section_id = null, $selected_sections_for_merge_ids = null, $current_academic_year = null) 
	{
		$publishedcourses = array();

		if (!empty($selected_sections_for_merge_ids)) {
			foreach ($selected_sections_for_merge_ids as $i => $v) {
				$tmp = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $current_academic_year . '%',
						'PublishedCourse.section_id' => $v
					),
					'recursive' => -1
				));

				if (!empty($tmp)) {
					$publishedcourses[$v] = $tmp;
				}
			}
		}

		// if there is no courses published for academic year allow to merge section 
		if (empty($publishedcourses)) {
			return 2; 
			// allow merge happens for the first time or course not published.
		}

		$published_course_ids_not_grade_submitted = array();

		if (!empty($publishedcourses)) {
			foreach ($publishedcourses as $section_id => $value) {
				if (!empty($value)) {
					foreach ($value as $ppin => $ppvalue) {
						$grade_submitted = $this->ExamGrade->is_grade_submitted($ppvalue['PublishedCourse']['id']);
						if ($grade_submitted) {
							//do nothing ;
						} else {
							$published_course_ids_not_grade_submitted[$section_id][$ppvalue['PublishedCourse']['id']] = $ppvalue['PublishedCourse']['course_id'];
						}
					}
				}
			}
		}

		$number_of_course_similar = array();

		if (!empty($published_course_ids_not_grade_submitted)) {
			foreach ($selected_sections_for_merge_ids as $mid => $msection_id) {
				// $number_of_course_similar
				if (!empty($published_course_ids_not_grade_submitted[$msection_id])) {
					foreach ($published_course_ids_not_grade_submitted[$msection_id] as $pub_course_id => $course_id) {
						// compare with other section 
						foreach ($published_course_ids_not_grade_submitted as $sec_id => $publish_course_ids) {
							if ($sec_id != $msection_id) {
								foreach ($publish_course_ids as $other_published_id => $other_course_id) {
									if ($other_course_id == $course_id) {
										$number_of_course_similar[$msection_id][$sec_id] = $course_id;
										break 1;
									}
								}
							}
						}
					}
				}
			}
		}

		return $number_of_course_similar;
	}

	function getPublishedCourseGradeScaleList($published_course_id = null)
	{
		$grade_scale = $this->PublishedCourse->getGradeScaleDetail($published_course_id);

		$grade_scales_formated = array();
		if (isset($grade_scale['GradeScaleDetail'])) {
			$grade_scale_details = $grade_scale['GradeScaleDetail'];
			foreach ($grade_scale_details as $key => $grade_scale_detail) {
				$grade_scales_formated[$grade_scale_detail['grade']] =  $grade_scale_detail['grade'] . ' (' . $grade_scale_detail['minimum_result'] . " - " . $grade_scale_detail['maximum_result'] . ')';
			}
		}
		return $grade_scales_formated;
	}

	function getCourseRegistrationGradeHistory($course_registration_id = null, $reg = 1)
	{
		$grade_history = array();

		if ($reg == 1) {

			$grade_history_row = $this->ExamGrade->find('all', array(
				'conditions' => array(
					'ExamGrade.course_registration_id' => $course_registration_id
				),
				//'order' => array('ExamGrade.created' => 'DESC'), // back dated grade entry affects this, Neway
				'order' => array('ExamGrade.id' => 'DESC'),
				'contain' => array(
					'ExamGradeChange' => array(
						//'order' => array('ExamGradeChange.created' => 'ASC') 
						'order' => array('ExamGradeChange.id' => 'ASC')
					),
					'CourseRegistration' => array(
						'ResultEntryAssignment' => array('order' => array('ResultEntryAssignment.id' => 'DESC'))
					)
				)
			));

			$count = 0;
			$grade_history[$count]['type'] = 'Register';
			$grade_history[$count]['result'] = $this->ExamResult->ExamType->getAssessementDetailType($course_registration_id);
			
			//debug($grade_history_row);
			if (count($grade_history_row) > 1) {
				$skip_first = false;
				foreach ($grade_history_row as $key => &$rejected_grade) {
					if (!$skip_first) {
						$skip_first = true;
						continue;
					}
					$rejected_grade['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['department_approved_by']));
					$rejected_grade['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['registrar_approved_by']));
					$grade_history[$count]['rejected'][] = $rejected_grade['ExamGrade'];
				}
			} else {
				$grade_history[$count]['rejected'] = array();
			}

			//$grade_history[$count]['rejected']=$grade_history_row;
			if (isset($grade_history_row[0]['ExamGrade'])) {
				$grade_history[$count]['ExamGrade'] = $grade_history_row[0]['ExamGrade'];
			} else {
				$grade_history[$count]['ExamGrade'] = array();
			}

			if (isset($grade_history_row[0]['CourseRegistration']['ResultEntryAssignment'][0])) {
				$grade_history[$count]['ResultEntryAssignment'] = $grade_history_row[0]['CourseRegistration']['ResultEntryAssignment'][0];
				$grade_history[$count]['result'] = $grade_history_row[0]['CourseRegistration']['ResultEntryAssignment'][0]['result'];
			}

			if (isset($grade_history_row[0]['ExamGradeChange'])) {
				foreach ($grade_history_row[0]['ExamGradeChange'] as $key => $examGradeChange) {
					$count++;
					$grade_history[$count]['type'] = 'Change';
					$examGradeChange['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['department_approved_by']));
					$examGradeChange['college_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['college_approved_by']));
					$examGradeChange['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['registrar_approved_by']));
					
					//grade scale lately introduce after prerequist bug
					$examGradeChange['grade_scale_id'] = ClassRegistry::init('ExamGrade')->field('grade_scale_id', array('ExamGrade.id' => $examGradeChange['exam_grade_id']));

					if ($examGradeChange['manual_ng_converted_by'] != "") {
						$examGradeChange['manual_ng_converted_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['manual_ng_converted_by']));
					}
					$grade_history[$count]['ExamGrade'] = $examGradeChange;
				}
			}
		} else {

			$grade_history_row = $this->ExamGrade->find('all', array(
				'conditions' => array(
					'ExamGrade.course_add_id' => $course_registration_id
				),
				//'order' => array('ExamGrade.created' => 'DESC'), // back dated grade entry affects this, Neway
				'order' => array('ExamGrade.id' => 'DESC'),
				'contain' => array(
					'ExamGradeChange' => array(
						//'order' => array('ExamGradeChange.created' => 'ASC') 
						'order' => array('ExamGradeChange.id' => 'ASC')
					),
					'CourseAdd' => array(
						'ResultEntryAssignment' => array('order' => array('ResultEntryAssignment.id' => 'DESC'))
					)
				)
			));

			$count = 0;
			$grade_history[$count]['type'] = 'Add';
			$grade_history[$count]['result'] = $this->ExamResult->ExamType->getAssessementDetailType($course_registration_id, 0);
			//debug($grade_history_row);
			
			if (count($grade_history_row) > 1) {
				$skip_first = false;
				foreach ($grade_history_row as $key => &$rejected_grade) {
					if (!$skip_first) {
						$skip_first = true;
						continue;
					}
					$rejected_grade['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['department_approved_by']));
					$rejected_grade['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['registrar_approved_by']));
					$grade_history[$count]['rejected'][] = $rejected_grade['ExamGrade'];
				}
			} else {
				$grade_history[$count]['rejected'] = array();
			}

			//$grade_history[$count]['rejected']=$grade_history_row;
			if (isset($grade_history_row[0]['ExamGrade'])) {
				$grade_history[$count]['ExamGrade'] = $grade_history_row[0]['ExamGrade'];
			} else {
				$grade_history[$count]['ExamGrade'] = array();
			}

			if (isset($grade_history_row[0]['CourseAdd']['ResultEntryAssignment'][0])) {
				$grade_history[$count]['ResultEntryAssignment'] = $grade_history_row[0]['CourseAdd']['ResultEntryAssignment'][0];
				$grade_history[$count]['result'] = $grade_history_row[0]['CourseAdd']['ResultEntryAssignment'][0]['result'];
			}

			if (isset($grade_history_row[0]['ExamGradeChange'])) {
				foreach ($grade_history_row[0]['ExamGradeChange'] as $key => $examGradeChange) {
					$count++;
					$grade_history[$count]['type'] = 'Change';
					$examGradeChange['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['department_approved_by']));
					$examGradeChange['college_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['college_approved_by']));
					$examGradeChange['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['registrar_approved_by']));
					
					//grade scale lately introduce after prerequist bug
					$examGradeChange['grade_scale_id'] = ClassRegistry::init('ExamGrade')->field('grade_scale_id', array('ExamGrade.id' => $examGradeChange['exam_grade_id']));

					if ($examGradeChange['manual_ng_converted_by'] != "") {
						$examGradeChange['manual_ng_converted_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['manual_ng_converted_by']));
					}
					$grade_history[$count]['ExamGrade'] = $examGradeChange;
				}
			}
		}

		return $grade_history;
	}

	//This function is re-defined in the ExamGradeChange model by the name "examGradeChangeStateDescription"
	function getExamGradeChangeStatus($exam_grade_change = null, $type = 'simple')
	{
		if (is_array($exam_grade_change)) {
			if (empty($exam_grade_change)) {
				return 'on-process';
			} else {
				if ($exam_grade_change['manual_ng_conversion'] == 1 || $exam_grade_change['auto_ng_conversion'] == 1) {
					return 'accepted';
				}
				if ($exam_grade_change['initiated_by_department'] == 1 || $exam_grade_change['department_approval'] == 1) {
					if ($exam_grade_change['college_approval'] == 1 || $exam_grade_change['makeup_exam_result'] != null) {
						if ($exam_grade_change['registrar_approval'] == 1) {
							return 'accepted';
						} else if ($exam_grade_change['registrar_approval'] == -1) {
							return 'rejected';
						} else if ($exam_grade_change['registrar_approval'] == null) {
							return 'on-process';
						}
					} else if ($exam_grade_change['college_approval'] == -1) {
						return 'rejected';
					} else if ($exam_grade_change['college_approval'] == null) {
						return 'on-process';
					}
				} else if ($exam_grade_change['department_approval'] == -1) {
					return 'rejected';
				} else if ($exam_grade_change['department_approval'] == null) {
					return 'on-process';
				}
			}
		}
		return 'on-process';
	}

	function getExamGradeStatus($exam_grade = null, $type = 'simple')
	{
		if (is_array($exam_grade)) {
			if (empty($exam_grade))
				return 'on-process';
			else {
				if ($exam_grade['department_approval'] == 1) {
					if ($exam_grade['registrar_approval'] == 1) {
						return 'accepted';
					} else if ($exam_grade['registrar_approval'] == -1) {
						return 'rejected';
					} else if ($exam_grade['registrar_approval'] == null) {
						return 'on-process';
					}
				} else if ($exam_grade['department_approval'] == -1) {
					return 'rejected';
				} else if ($exam_grade['department_approval'] == null) {
					return 'on-process';
				}
			}
		}
		return 'on-process';
	}

	function isAnyGradeOnProcess($course_registration_id = null)
	{
		$grade_histories = $this->getCourseRegistrationGradeHistory($course_registration_id);
		
		if (!empty($grade_histories)) {
			foreach ($grade_histories as $key => $grade_history) {
				if (
					(strcasecmp($grade_history['type'], 'Register') == 0 && strcasecmp($this->getExamGradeStatus($grade_history['ExamGrade']), 'on-process') == 0) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && strcasecmp($this->getExamGradeChangeStatus($grade_history['ExamGrade']), 'on-process') == 0)
				) {
					return true;
				}
			}
		}

		return false;
	}

	// When it return the grade, it doesn't consider the approval of the course registration grade
	// But it evalutes the approval for grade change to return grade
	function getCourseRegistrationLatestGrade($course_registration_id = null)
	{
		$grade_histories = $this->getCourseRegistrationGradeHistory($course_registration_id);
		$latest_grade = "";

		if (!empty($grade_histories)) {
			foreach ($grade_histories as $key => $grade_history) {
				if (
					$grade_history['ExamGrade']['grade'] != $latest_grade &&
					($grade_history['type'] != 'Change' || 
					(($grade_history['ExamGrade']['department_approval'] == 1 || $grade_history['ExamGrade']['initiated_by_department'] == 1) && $grade_history['ExamGrade']['registrar_approval'] == 1 && $grade_history['ExamGrade']['college_approval'] == 1) || 
					($grade_history['ExamGrade']['makeup_exam_result'] != null && ($grade_history['ExamGrade']['department_approval'] == 1 || $grade_history['ExamGrade']['initiated_by_department'] == 1) && $grade_history['ExamGrade']['registrar_approval'] == 1)
					)
				) {
					$latest_grade = $grade_history['ExamGrade']['grade'];
				}
			}
		}
		return $latest_grade;
	}

	// Return grade detail for course registration regardless of its approval state and it may
	// Return grade change detail unless it is not fully rejected.
	function getCourseRegistrationLatestGradeDetail($course_registration_id = null, $reg = 1)
	{
		if ($reg == 1) {
			$grade_histories = $this->getCourseRegistrationGradeHistory($course_registration_id);
		} else {
			$grade_histories = $this->getCourseRegistrationGradeHistory($course_registration_id, 0);
		}

		$latest_grade_detail = array();
		//debug($grade_histories);

		if (!empty($grade_histories)) {
			foreach ($grade_histories as $key => $grade_history) {
				if (
					strcasecmp($grade_history['type'], 'Register') == 0 ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] == null && $grade_history['ExamGrade']['department_approval'] != -1 && $grade_history['ExamGrade']['college_approval'] != -1 && $grade_history['ExamGrade']['registrar_approval'] != -1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 0 && $grade_history['ExamGrade']['department_approval'] != -1 && $grade_history['ExamGrade']['registrar_approval'] != -1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 1 && $grade_history['ExamGrade']['department_approval'] != -1 && $grade_history['ExamGrade']['registrar_approval'] != -1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && ($grade_history['ExamGrade']['auto_ng_conversion'] == 1 || $grade_history['ExamGrade']['manual_ng_conversion'] == 1))
				) {
					$latest_grade_detail = $grade_history;
				}

				if (isset($latest_grade_detail['rejected'])) {
					unset($latest_grade_detail['rejected']);
				}
			}
		}

		return $latest_grade_detail;
	}

	/* 	Return grade detail for course registration only
		1. if it is approved by the department,  college and registrar for grade change
		2. if it is approved by the department and registrar for for normal grade approvals
		It also considers fully approved grade chnages.
	*/
	function getCourseRegistrationLatestApprovedGradeDetail($course_registration_id = null)
	{
		$grade_histories = $this->getCourseRegistrationGradeHistory($course_registration_id);
		$latest_grade_detail = array();

		if(!empty($grade_histories)) {
			foreach ($grade_histories as $key => $grade_history) {
				if ((
					(strcasecmp($grade_history['type'], 'Register') == 0 && !empty($grade_history['ExamGrade']) && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] == null && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['college_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 0 && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 1 && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) || 
					(strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['manual_ng_conversion'] == 1)
					) || (isset($grade_history['ExamGrade']['auto_ng_conversion']) && $grade_history['ExamGrade']['auto_ng_conversion'])) {
					if (!empty($latest_grade_detail)) {
						/* if ($grade_history['ExamGrade']['created'] > $latest_grade_detail['ExamGrade']['created']) {
							$latest_grade_detail = $grade_history;
						} */

						// back dated grade entry affects the above, Neway
						if ($grade_history['ExamGrade']['id'] > $latest_grade_detail['ExamGrade']['id']) {
							$latest_grade_detail = $grade_history;
						}
					} else {
						$latest_grade_detail = $grade_history;
					}
				}

				if (isset($latest_grade_detail['rejected'])) {
					unset($latest_grade_detail['rejected']);
				}
			}
		}
		return $latest_grade_detail;
	}

	function isCourseDroped($course_registration_id = null)
	{
		$course_registration_detail = $this->find('first', array(
			'conditions' => array(
				'CourseRegistration.id' => $course_registration_id
			),
			'contain' => array(
				'PublishedCourse',
				'CourseDrop'
			)
		));

		if (!empty($course_registration_detail['CourseDrop'])) {
			if ($course_registration_detail['CourseDrop'][0]['forced'] == 1 || ($course_registration_detail['CourseDrop'][0]['department_approval'] == 1 && $course_registration_detail['CourseDrop'][0]['registrar_confirmation'] == 1)) {
				return true;
			} else {

				// The following code is commented out later to be completely removed as the published course is updated when it is converted to drop (a new record is not created). 
				// So we are going to check the published course itself. But it has not logical defect, as I saw it, if it is used as it is 

				/* $drop_published_course = $this->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.course_id' => $course_registration_detail['PublishedCourse']['course_id'],
						'PublishedCourse.academic_year' => $course_registration_detail['PublishedCourse']['academic_year'],
						'PublishedCourse.semester' => $course_registration_detail['PublishedCourse']['semester'],
						'PublishedCourse.program_type_id' => $course_registration_detail['PublishedCourse']['program_type_id'],
						'PublishedCourse.program_id' => $course_registration_detail['PublishedCourse']['program_id'],
						'PublishedCourse.department_id' => $course_registration_detail['PublishedCourse']['department_id'],
						'PublishedCourse.section_id' => $course_registration_detail['PublishedCourse']['section_id'],
						'PublishedCourse.drop = 1',
						'PublishedCourse.college_id' => $course_registration_detail['PublishedCourse']['college_id'],
					)
				));

				if(!empty($drop_published_course)) {
					return true;
				} */
				
				if ($course_registration_detail['PublishedCourse']['drop'] == 1) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	function getCourseRegistrations($student_id = null, $ay_and_s_list = array(), $course_id = null, $include_equivalent = 1, $exclude_drop = 1)
	{
		$course_registrations = array();

		if (!empty($student_id)) {

			$options = array();

			if (!empty($ay_and_s_list)) {
				foreach ($ay_and_s_list as $key => $ay_and_s) {
					$options['conditions']['OR'][] = array(
						'CourseRegistration.academic_year' => $ay_and_s['academic_year'],
						'CourseRegistration.semester' => $ay_and_s['semester']
					);
				}
			}

			$options['conditions'][] = array('CourseRegistration.student_id' => $student_id);
			//$matching_courses = array();
			//$matching_courses[] = $course_id;

			//debug($matching_courses);

			if ($include_equivalent == 1) {
				$student_department = $this->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'recursive' => -1
				));

				$course_department = $this->PublishedCourse->Course->find('first', array(
					'conditions' => array(
						'Course.id' => $course_id
					),
					'contain' => 'Curriculum'
				));

				if (!empty($student_department['Student']['department_id'])) {
					/*** If the course is main course for the department.  If it is, then we are going to concentrate on its equivalent. ***/
					if ($student_department['Student']['department_id'] == $course_department['Curriculum']['department_id'] && $student_department['Student']['curriculum_id'] == $course_department['Course']['curriculum_id']) {
						/* $course_be_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array(
							'conditions' => array(
								'EquivalentCourse.course_for_substitued_id' => $course_id
							),
							'recursive' => -1
						));

						if (!empty($course_be_substitueds)) {
							foreach ($course_be_substitueds as $key => $value) {
								$matching_courses[] = $value['EquivalentCourse']['course_be_substitued_id'];
							}
						} */
						$matching_courses[] = $course_id;

					} else {
						/*** If the course is from other department then we are going to look for its equivalent department course ***/
						$course_for_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array(
							'conditions' => array(
								'EquivalentCourse.course_be_substitued_id' => $course_id
							),
							'recursive' => -1
						));

						$matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_id, $student_department['Student']['curriculum_id']);
						debug($matching_courses);
						//debug($course_for_substitueds);

						/* if (!empty($course_for_substitueds)) {
							foreach ($course_for_substitueds as $key => $value) {
								$course_detail = $this->PublishedCourse->Course->find('first', array(
									'conditions' => array(
										'Course.id' => $value['EquivalentCourse']['course_for_substitued_id']
									),
									'contain' => array('Curriculum')
								));

								if ($course_detail['Curriculum']['department_id'] == $student_department['Student']['department_id']) {
									//$matching_courses[] = $value['EquivalentCourse']['course_for_substitued_id'];
								}
							}
						} */
					}
				}
			} else {
				$matching_courses[] = $course_id;
			}

			$options['order'] = array('CourseRegistration.created DESC');
			$options['contain'] = array('PublishedCourse' => array('Course'));

			$course_registrations_raw = $this->find('all', $options);

			if (!empty($course_registrations_raw)) {
				foreach ($course_registrations_raw as $key => $value) {
					if (in_array($value['PublishedCourse']['Course']['id'], $matching_courses)) {
						if ($exclude_drop != 1 || ($exclude_drop == 1 && !$this->isCourseDroped($value['CourseRegistration']['id']))) {
							$course_registrations[] = $value;
						}
					}
				}
			}
		}
		return $course_registrations;
	}

	function alreadyRegistered($student_id = null, $academic_year = null, $semester = null)
	{
		if (!empty($student_id)) {
			if (!empty($semester)) {
				$latestSemester = $semester;
			} else {
				//$latestSemester = $this->latestCourseRegistrationSemester($academic_year);
				
				// check if this also solves double registration, Neway
				$latestSemester = $this->latestCourseRegistrationSemester($academic_year, $student_id);
			}

			$count = $this->find('count', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.academic_year like' => $academic_year . '%',
					'CourseRegistration.semester' => $latestSemester
				)
			));

			if ($count > 0) {
				return true;
			} else {
				return false;
			}
		}
		// prevent any inconsistencies
		return true;
	}

	function not_allow_f_prerequisite($publishedCourses = null, $student_id = null)
	{
		$course_register_reformat = array();
		$count = 0;

		if (!empty($publishedCourses)) {
			foreach ($publishedCourses as $index => $value) {
				if (!empty($value['Course']['Prerequisite'])) {
					$passed_count = 0;

					foreach ($value['Course']['Prerequisite'] as $preindex => $prevalue) {
						$pre_passed = $this->CourseDrop->prequisite_taken($student_id, $prevalue['prerequisite_course_id']);
						if ($pre_passed === true) {
							$passed_count++;
						}
					}
					//debug($passed_count);
					//debug($value['PublishedCourse']['Course']['Prerequisite']);

					if ($passed_count == count($value['Course']['Prerequisite'])) {
						$course_register_reformat[$count] = $value;
						$course_register_reformat[$count]['prequisite_taken_passsed'] = 1;
					} else {
						$course_register_reformat[$count] = $value;
						$course_register_reformat[$count]['prequisite_taken_passsed'] = 0;
					}
				} else {
					$course_register_reformat[$count] = $value;
					$course_register_reformat[$count]['prequisite_taken_passsed'] = 1;
				}
				$count++;
			}
		}

		return $course_register_reformat;
	}

	/**
	 * return list of courses for registration with their type of registration 
	 * 11 on hold because of status
	 * 12 on hold because of prerequiste 
	 * 13 on hold because of prerequise and status 
	*/
	function getRegistrationType($publishedCourses = null, $student_id = null, $status = null)
	{
		/* This function returns student acdamic status at the end but before the given acadamic year and semester
			Return values
				A. 1 = for the first time (pattern not fullfilled)
				B. 2 = status is not generated before the given acadamic year and semester (on hold)
				C. Array = Student status object
		*/

		$course_register_reformat = array();
		$count = 0;
		$ready_registred_course_ids = array();

		if (!empty($publishedCourses)) {

			foreach ($publishedCourses as $p => $v) {
				$ready_registred_course_ids[] = $v['Course']['id'];
			}

			foreach ($publishedCourses as $index => $value) {
				//if the student is requested  exemption, and approved by department do not register her/him for that particuluar course
				if (ClassRegistry::init('CourseExemption')->isCourseExempted($student_id, $value['Course']['id']) > 0) {
					$course_register_reformat[$count] = $value;
					$course_register_reformat[$count]['exemption'] = 1;
					$count++;
					continue;
				}

				if (!empty($value['Course']['Prerequisite'])) {
					//debug($value);
					$passed_count = array();
					$passed_count['passed'] = 0;
					$passed_count['onhold'] = 0;

					foreach ($value['Course']['Prerequisite'] as $preindex => $prevalue) {
						if ($prevalue['co_requisite'] == 1) {
							if (in_array($prevalue['prerequisite_course_id'], $ready_registred_course_ids)) {
								$passed_count['passed'] = $passed_count['passed'] + 1;
							} else {
								$pre_passed = $this->CourseDrop->prequisite_taken($student_id, $prevalue['prerequisite_course_id']);

								if ($pre_passed === true) {
									$passed_count['passed'] = $passed_count['passed'] + 1;
								} else if ($pre_passed == 2) {
									$passed_count['onhold'] = $passed_count['onhold'] + 1;
								}
							}
						} else {
							$pre_passed = $this->CourseDrop->prequisite_taken($student_id, $prevalue['prerequisite_course_id']);
							debug($pre_passed);
							debug($prevalue);

							if ($pre_passed === true) {
								$passed_count['passed'] = $passed_count['passed'] + 1;
							} else if ($pre_passed == 2) {
								$passed_count['onhold'] = $passed_count['onhold'] + 1;
							}
						}
					}

					if ($passed_count['passed'] == count($value['Course']['Prerequisite'])) {
						$course_register_reformat[$count] = $value;
						$course_register_reformat[$count]['prequisite_taken_passsed'] = 1;
					} else if ($passed_count['onhold'] == count($value['Course']['Prerequisite'])) {
						$course_register_reformat[$count] = $value;
						$course_register_reformat[$count]['prequisite_taken_passsed'] = 2;
					} else {
						$course_register_reformat[$count] = $value;
						$course_register_reformat[$count]['prequisite_taken_passsed'] = 0;
					}
				} else {
					$course_register_reformat[$count] = $value;
				}
				debug($status);

				if ($status == 2) {
					$course_register_reformat[$count]['registration_type'] = 2;
				} else if ($status == 1) {
					$course_register_reformat[$count]['registration_type'] = 1;
				} else if ($status == 0) {
					$course_register_reformat[$count]['registration_type'] = 0;
				}

				$count++;
			}
		}
		return $course_register_reformat;
	}

	// Function to allow students to register for the published coures 
	function registerSingleStudent($student_id = null, $academic_year = null, $semester = null, $exclude_elective = 0)
	{
		//check students are allowed to register based on their academic status.
		$published_courses = array();
		$published_courses['passed'] = false;
		$published_courses['register'] = array();
		$getRegistrationDeadLine = false;
		$get_student_acadamic_status = null;
		$latestSemester = null;
		$latest_academic_year = $academic_year;

		$passed_or_failed = $this->Student->StudentExamStatus->get_student_exam_status($student_id, $latest_academic_year);

		if (empty($semester)) {
			$latestAcSemester = $this->getLastestStudentSemesterAndAcademicYear($student_id, $latest_academic_year);
		} else {
			$latestAcSemester['semester'] = $semester;
		}

		debug($latestAcSemester);
		$latestSemester = $latestAcSemester['semester']; 

		$published_course['passed'] = $passed_or_failed;

		$get_student_acadamic_status = $this->Student->StudentExamStatus->getStudentAcadamicStatus($student_id, $latest_academic_year, $latestSemester);

		$student_section = $this->Student->student_academic_detail($student_id, $latest_academic_year);

		if (!empty($student_section)) {
			if (count($student_section['Section']) > 0 && $student_section['Section'][0]['academicyear'] == $academic_year) {
				if (empty($student_section['Student']['department_id'])) {

					if ($exclude_elective) {
						$published_courses = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.department_id is null',
								'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
								//'PublishedCourse.year_level_id' => 0,
								'OR' => array(
									'PublishedCourse.year_level_id IS NULL',
									'PublishedCourse.year_level_id = 0',
									'PublishedCourse.year_level_id = ""',
								),
								'PublishedCourse.drop' => 0,
								'PublishedCourse.add' => 0, 
								'PublishedCourse.published' => 1,
								'PublishedCourse.elective' => 0,
								'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
								'PublishedCourse.semester' => $latestSemester,
								'PublishedCourse.college_id' => $student_section['Student']['college_id'],
							), 
							'contain' => array(
								'Course' => array(
									'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
									'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'Department' => array('id', 'name', 'type'),
								'College' => array('id', 'name', 'type'),
								'Section' => array(
									'fields'=> array('id', 'name','academicyear', 'archive'),
									'YearLevel' => array('id', 'name'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
								),
								'YearLevel' => array('id', 'name'),
							)
						));
					} else {
						$published_courses = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.department_id is null',
								'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
								//'PublishedCourse.year_level_id' => 0,
								'OR' => array(
									'PublishedCourse.year_level_id IS NULL',
									'PublishedCourse.year_level_id = 0',
									'PublishedCourse.year_level_id = ""',
								),
								'PublishedCourse.drop' => 0,
								'PublishedCourse.add' => 0, 
								'PublishedCourse.published' => 1,
								'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
								'PublishedCourse.semester' => $latestSemester,
								'PublishedCourse.college_id' => $student_section['Student']['college_id'],
							), 
							'contain' => array(
								'Course' => array(
									'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
									'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'Department' => array('id', 'name', 'type'),
								'College' => array('id', 'name', 'type'),
								'Section' => array(
									'fields'=> array('id', 'name','academicyear', 'archive'),
									'YearLevel' => array('id', 'name'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
								),
								'YearLevel' => array('id', 'name'),
							)
						));
					}
				} else {

					if ($exclude_elective) {
						$published_courses = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.department_id' => $student_section['Student']['department_id'],
								'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
								'PublishedCourse.year_level_id' => $student_section['Section'][0]['year_level_id'],
								'PublishedCourse.drop' => 0,
								'PublishedCourse.add' => 0,
								'PublishedCourse.published' => 1,
								'PublishedCourse.elective' => 0,
								'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
								'PublishedCourse.semester' => $latestSemester
							),
							'contain' => array(
								'Course' => array(
									'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
									'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'Department' => array('id', 'name', 'type'),
								'College' => array('id', 'name', 'type'),
								'Section' => array(
									'fields'=> array('id', 'name','academicyear', 'archive'),
									'YearLevel' => array('id', 'name'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
								),
								'YearLevel' => array('id', 'name'),
							)
						));
					} else {
						$published_courses = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.department_id' => $student_section['Student']['department_id'],
								'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
								'PublishedCourse.year_level_id' => $student_section['Section'][0]['year_level_id'],
								'PublishedCourse.drop' => 0,
								'PublishedCourse.add' => 0,
								'PublishedCourse.published' => 1,
								'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
								'PublishedCourse.semester' => $latestSemester
							),
							'contain' => array(
								'Course' => array(
									'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
									'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
								),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'Department' => array('id', 'name', 'type'),
								'College' => array('id', 'name', 'type'),
								'Section' => array(
									'fields'=> array('id', 'name','academicyear', 'archive'),
									'YearLevel' => array('id', 'name'),
									'Program' => array('id', 'name'),
									'ProgramType' => array('id', 'name'),
									'Department' => array('id', 'name', 'type'),
									'College' => array('id', 'name', 'type'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
								),
								'YearLevel' => array('id', 'name'),
							)
						));
					}
				}

				$published_courses = $this->getRegistrationType($published_courses, $student_id, $get_student_acadamic_status);
				$published_course['register'] = $published_courses;
				return $published_course;
			}
		}
	}

	function getLastestStudentSemesterAndAcademicYear($student_id = null, $current_academic_year = null, $add_drop = 0) 
	{
		$lastest_semester_academic_year = array();
		$list_of_academic_year_semester_student_attended = $this->ExamGrade->getListOfAyAndSemester($student_id);
		debug($list_of_academic_year_semester_student_attended);

		//fresh 
		if (empty($list_of_academic_year_semester_student_attended)) {
			$lastest_semester_academic_year['semester'] = 'I';
			$lastest_semester_academic_year['academic_year'] = $current_academic_year;
			return $lastest_semester_academic_year;
		} else {

			$last_end_academic_year_semester = end($list_of_academic_year_semester_student_attended);

			if (strcasecmp($current_academic_year, $last_end_academic_year_semester['academic_year']) == 0) {
				if ($add_drop) {
					$lastest_semester_academic_year['semester'] = $last_end_academic_year_semester['semester'];
					$lastest_semester_academic_year['academic_year'] = $current_academic_year;
					return $lastest_semester_academic_year;
				} else {
					$next_semester = $this->Student->StudentExamStatus->getNextSemster($current_academic_year, $last_end_academic_year_semester['semester']);
					$lastest_semester_academic_year['semester'] = $next_semester['semester'];
					$lastest_semester_academic_year['academic_year'] = $current_academic_year;
					return $lastest_semester_academic_year;
				}
			} else if (strcasecmp($current_academic_year, $last_end_academic_year_semester['academic_year']) > 0) {
				//lastest published academic year and semester 

				$student_program_id = ClassRegistry::init('Student')->field('Student.program_id', array('Student.id' => $student_id));
				$student_program_type_id = ClassRegistry::init('Student')->field('Student.program_type_id', array('Student.id' => $student_id));

				$lastest_semester_academic_year = $this->latest_academic_year_semester($current_academic_year, $student_program_id, $student_program_type_id);

				if ($add_drop) {
					// course drop is not working for lagged ACY caused by COVID,  Current ACY lagged by 1 or more years behind
					$lastest_semester_academic_year = array();
					$lastest_semester_academic_year['semester'] = $last_end_academic_year_semester['semester'];
					$lastest_semester_academic_year['academic_year'] = $last_end_academic_year_semester['academic_year'];
				}

				//debug($lastest_semester_academic_year);
				
				return $lastest_semester_academic_year;
			}
		}
	}

	function massRegisterStudent($section_id = null, $academic_yearr = null)
	{

		$validCourseRegistrationLists = array();
		$academic_year = array();
		debug($academic_yearr);

		if (isset($academic_yearr['academicyear']) && !empty($academic_yearr['academicyear'])) {
			$academic_year['academic_year'] = $academic_yearr['academicyear'];
			$academic_year['semester'] = $academic_yearr['semester'];
		} else if (isset($academic_yearr['academic_year']) && !empty($academic_yearr['academic_year'])) {
			$academic_year['academic_year'] = $academic_yearr['academic_year'];
			$academic_year['semester'] = $academic_yearr['semester'];
		}

		$published_courses = $this->PublishedCourse->find('all', array(
			'conditions' => array(
				'PublishedCourse.section_id' => $section_id,
				'PublishedCourse.drop' => 0,
				'PublishedCourse.add' => 0,
				'PublishedCourse.academic_year' => $academic_year['academic_year'],
				'PublishedCourse.semester' => $academic_year['semester'],
				'PublishedCourse.elective' => 0,
				'PublishedCourse.published' => 1,
			), 
			'contain' => array(
				'Course' => array(
					'Prerequisite' => array(
						'id', 
						'prerequisite_course_id', 
						'co_requisite'
					),
					'fields' => array(
						'Course.id', 
						'Course.course_code', 
						'Course.course_title', 
						'Course.lecture_hours',
						'Course.tutorial_hours', 
						'Course.credit'
					)
				)
			)
		));

		$students_list = $this->Section->getSectionActiveStudentsId($section_id);
		debug($students_list);

		if (!empty($students_list) && !empty($published_courses)) {
			foreach ($students_list as $k => $value) {
				//check if s/he has already registred for published course 
				$alreadyRegistered = $this->alreadyRegistered($value, $academic_year['academic_year'], $academic_year['semester']);

				if (!$alreadyRegistered) {
					$get_student_acadamic_status = $this->Student->StudentExamStatus->get_student_exam_status($value, $academic_year['academic_year']);
					
					// $isThereFxInPrevAcademicStatus=$this->Student->StudentExamStatus->checkFxPresenseInStatus($value); 
					if ($get_student_acadamic_status == 1 || $get_student_acadamic_status == 3) {
						$published = $this->getRegistrationType($published_courses, $value, $get_student_acadamic_status);
						$validCourseRegistrationLists[$value]['register'] = $published;
						$validCourseRegistrationLists[$value]['passed'] = $get_student_acadamic_status;
					}
				}
			}
		}

		//check prerequiste, exemption, and onhold registration 
		$formattedSaveAllRegistration = array();
		$count = 0;

		if (!empty($validCourseRegistrationLists)) {
			foreach ($validCourseRegistrationLists as $student_id => $value) {
				foreach ($value['register'] as $k => $publishedvalue) {
					//registration checking
					if (!isset($publishedvalue['prequisite_taken_passsed']) && !isset($publishedvalue['exemption']) || (isset($publishedvalue['prequisite_taken_passsed']) && $publishedvalue['prequisite_taken_passsed'] == 1)) {
						$formattedSaveAllRegistration['CourseRegistration'][$count]['published_course_id'] = $publishedvalue['PublishedCourse']['id'];
						$formattedSaveAllRegistration['CourseRegistration'][$count]['course_id'] = $publishedvalue['PublishedCourse']['course_id'];
						$formattedSaveAllRegistration['CourseRegistration'][$count]['semester'] = $publishedvalue['PublishedCourse']['semester'];
						$formattedSaveAllRegistration['CourseRegistration'][$count]['academic_year'] = $publishedvalue['PublishedCourse']['academic_year'];
						$formattedSaveAllRegistration['CourseRegistration'][$count]['student_id'] = $student_id;
						$formattedSaveAllRegistration['CourseRegistration'][$count]['section_id'] = $publishedvalue['PublishedCourse']['section_id'];
						$formattedSaveAllRegistration['CourseRegistration'][$count]['year_level_id'] = $publishedvalue['PublishedCourse']['year_level_id'];
					}

					// type of registration 
					if ((isset($publishedvalue['registration_type']) && $publishedvalue['registration_type'] == 2 && !isset($publishedvalue['exemption']))) {
						$formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 11;
					} else if (isset($publishedvalue['prequisite_taken_passsed']) && $publishedvalue['prequisite_taken_passsed'] == 2 && !isset($publishedvalue['exemption'])) {
						$formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 11;
					} else if ((isset($publishedvalue['registration_type']) && $publishedvalue['registration_type'] == 2) && (isset($publishedvalue['prequisite_taken_passsed']) && $publishedvalue['prequisite_taken_passsed'] == 2) && !isset($publishedvalue['exemption'])) {
						$formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 13;
					}

					$count++;
				}
			}
		}

		debug($formattedSaveAllRegistration['CourseRegistration']);

		if (isset($formattedSaveAllRegistration['CourseRegistration']) && !empty($formattedSaveAllRegistration['CourseRegistration'])) {
			if ($this->saveAll($formattedSaveAllRegistration['CourseRegistration'], array('validate' => false))) {
				return 1; 
				//registered successfully
			} else {
				return 2;  
				// registeration not successful
			}
		} else if (empty($formattedSaveAllRegistration['CourseRegistration'])) {
			return 2;  
			// registeration not successful
		}

		return 3;  
		// all students are registered except those who doesnt qualify
	}

	function courseRegistered($published_course_id = null, $semester = null, $academic_year = null, $student_id = null)
	{
		$already_registered = $this->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.published_course_id' => $published_course_id,
				'CourseRegistration.semester' => $semester
			)
		));

		// TODO: add a condition to check the course is taken by the student in other semesters and passed prequisite, Neway, alrady exixts in CourseDrop->course_taken()

		return $already_registered;
	}

	function getRegistrationStats($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null, $type = null ) {

		$options = array();

		if (isset($acadamic_year) && isset($semester)) {
			$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations  where academic_year="' . $acadamic_year . '" and semester="' . $semester . '")';
		} else {
			$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations )';
		}

		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				//registered 
				$options['conditions']['Student.college_id'] = $college_id[1];
			} else {
				//registered
				$options['conditions']['Student.department_id'] = $department_id;
			}
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$options['conditions']['Student.program_id'] = $program_ids[1];
			} else {
				$options['conditions']['Student.program_id'] = $program_id;
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$options['conditions']['Student.program_type_id'] = $program_type_ids[1];
			} else {
				$options['conditions']['Student.program_type_id'] = $program_type_id;
			}
		}

		$options['contain'] = array(
			'StudentExamStatus'  => array(
				'conditions' => array(
					'StudentExamStatus.semester' => $semester,
					'StudentExamStatus.academic_year' => $acadamic_year,
				),
				'limit' => 1,
				'order' => array('StudentExamStatus.created DESC')
			),
			'CourseRegistration'  => array(
				'conditions' => array(
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.academic_year' => $acadamic_year
				),
				'order' => array('CourseRegistration.created DESC'),
				'fields' => array(
					'CourseRegistration.academic_year',
					'CourseRegistration.semester'
				),
				'id' => array(
					'Section' => array(
						'YearLevel' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'Department' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'College' => array(
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
					)
				)
			),
		);

		$options['fields'] = array(
			'Student.full_name', 
			'Student.first_name',
			'Student.middle_name', 
			'Student.last_name', 
			'Student.studentnumber', 
			'Student.admissionyear',
			'Student.gender'
		);

		$options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');

		$students = $this->Student->find('all', $options);

		$attraction_rate = array();
		$total_student = count($students);
		$yearLevelCount = array();
		// debug($students);
		debug($total_student);

		if (!empty($students)) {
			foreach ($students as $key => $student) {
				// $section=ClassRegistry::init('Section')->getStudentSectionInGivenAcademicYear($acadamic_year,$student['Student']['id']); 
				$section = $student['CourseRegistration'][0]['Section'];
				// debug($student);

				if (!isset($section) || !isset($section['Program']['name']) || !isset($section['ProgramType']['name'])) {
					continue;
				}

				///////////////////////////////initialization///////////////////////////////
				
				if (!isset($section['YearLevel']['name'])) {
					$yearLevelCount['1st'] = '1st';
				} else {
					$yearLevelCount[$section['YearLevel']['name']] = $section['YearLevel']['name'];
				}

				#############################BEGIN REGISTRATION###################################		
				if ($type['registered'] == 1) {

					//total department registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['Department']['name']) && !empty($section['College']['name'])) {

						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['total'] = 1;
						}
						
						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total college registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {

						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total university registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name'])) {

						if (isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'])) {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total preengineering dept registred summation and initialization  
					if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {

						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['male_total'] = 0;
						}
					}

					//total preengineering college summation and initialization  
					if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {
						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['male_total'] = 0;
						}
						//   debug($section['YearLevel']['name']);
					}

					//total preengineering university summation and initialization  
					if (empty($section['YearLevel']['name'])) {

						if (isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'])) {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] += 1;
						} else {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'])) {
							$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'])) {
							$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] = 0;
						}
					}

					// CHECK NEEDED THERE if else or what starts with curly bracket

					//  debug($attraction_rate);
					///////////////////////////////initialization end ///////////////////////////////
					{

						if (empty($section['department_id'])) {
							if (strcmp($student['Student']['gender'], 'male') == 0) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['male_total'] += 1;
							} else if (strcmp($student['Student']['gender'], 'female') == 0) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Registered']['1st']['female_total'] += 1;
							}
						} else {

							if (strcmp($student['Student']['gender'], 'female') == 0) {
								if (isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['female_total'] += 1;
								}
							} else if (strcmp($student['Student']['gender'], 'male') == 0) {
								if (isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Registered'][$section['YearLevel']['name']]['male_total'] += 1;
								}
							}
						}

						// sum college, university female and male  dismissed 
						if (strcmp($student['Student']['gender'], 'female') == 0) {
							
							//total college level female dismissed 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['female_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['female_total'] += 1;
								}
							}

							//university level total female dismissed  
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] += 1;
								}
							}
						} else if (strcmp($student['Student']['gender'], 'male') == 0) {

							//college level total male 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered'][$section['YearLevel']['name']]['male_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Registered']['1st']['male_total'] += 1;
								}
							}

							//university level total male 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate['University']['Registered'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] += 1;
								}
							}
						}
					}
				}

				#############################END REGISTRATION###################################	

				#############################BEGIN DISMISSED###################################		
				if (isset($student['StudentExamStatus'][0]['academic_status_id']) && $student['StudentExamStatus'][0]['academic_status_id'] == 4 && $type['dismissed'] == 1 ) {
					debug($type);

					//total department registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['Department']['name']) && !empty($section['College']['name'])) {

						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total college registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {
						
						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total university registred summation and initialization  
					if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name'])) {

						if (isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] += 1;
						} else {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] = 0;
						}
					}

					//total preengineering dept registred summation and initialization  
					if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {
						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['male_total'] = 0;
						}
					}

					//total preengineering college summation and initialization  
					if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {
						if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['total'] += 1;
						} else {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['female_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['male_total'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['male_total'] = 0;
						}
						//   debug($section['YearLevel']['name']);
					}

					//total preengineering university summation and initialization  
					if (empty($section['YearLevel']['name'])) {

						if (isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] += 1;
						} else {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] = 1;
						}

						// female initialized 
						if (!isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] = 0;
						}

						// male initialized 
						if (!isset($attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'])) {
							$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] = 0;
						}
					}

					//  debug($attraction_rate);
					///////////////////////////////initialization end ///////////////////////////////

					{

						if (empty($section['department_id'])) {
							if (strcmp($student['Student']['gender'], 'male') == 0) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['male_total'] += 1;
							} else if (strcmp($student['Student']['gender'], 'female') == 0) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['Dismissed']['1st']['female_total'] += 1;
							}
						} else {
							if (strcmp($student['Student']['gender'], 'female') == 0) {
								if (isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['female_total'] += 1;
								}
							} else if (strcmp($student['Student']['gender'], 'male') == 0) {
								if (isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']]['Dismissed'][$section['YearLevel']['name']]['male_total'] += 1;
								}
							}
						}

						// sum college, university female and male  dismissed 
						if (strcmp($student['Student']['gender'], 'female') == 0) {
							
							//total college level female dismissed 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['female_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['female_total'] += 1;
								}
							}

							//university level total female dismissed  
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] += 1;
								}
							}
						} else if (strcmp($student['Student']['gender'], 'male') == 0) {

							//college level total male 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed'][$section['YearLevel']['name']]['male_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['Dismissed']['1st']['male_total'] += 1;
								}
							}

							//university level total male 
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] += 1;
							} else {
								if (!isset($section['YearLevel']['name'])) {
									$attraction_rate['University']['Dismissed'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] += 1;
								}
							}
						}
					}
				}

				#############################END DIMISSED###################################	
			}
		}

		$attrationRate['attractionRate'] = $attraction_rate;
		$attrationRate['YearLevel'] = $yearLevelCount;

		return $attrationRate;
	}

	function getFormattedRegistrationStats()
	{
	}

	function getFormattedDismissedStats()
	{
	}

	function getFormattedDropOutStats()
	{
	}

	function getFormattedTransfered()
	{
	}

	function doesTheCourseRegistrationHaveWithdraw($registrationId)
	{
		$registration_has_withdrawal = $this->find('count', array(
			'conditions' => array(
				'CourseRegistration.id' => $registrationId,
				'CourseRegistration.id in (select course_registration_id from exam_grades where id in (select exam_grade_id from exam_grade_changes where grade="W")) '
			),
			'order' => array('CourseRegistration.created DESC'), 
			'contain' => array('ExamGrade' => array('ExamGradeChange'))
		));
		return $registration_has_withdrawal;
	}


	public function getMostRecentRegisteration($student_id)
	{
		$courseRegisteration = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			),
			'order' => 'CourseRegistration.created DESC',
			'contain' => array(
				'Section' => array('YearLevel', 'Department', 'College')
			)
		));
		return $courseRegisteration;
	}

	public function getRegisteration($student_id, $academic_year, $semester)
	{
		$courseRegisteration = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id, 
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.semester' => $semester
			),
			'contain' => array(
				'Section' => array('YearLevel', 'Department', 'College')
			), 
			'order' => 'CourseRegistration.created DESC'
		));
		return $courseRegisteration;
	}

	function studentYearAndSemesterLevelByRegistration($student_id, $acadamic_year = null, $semester = null)
	{
		$studentStatusPattern = $this->Student->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id,
			),
			'contain' => array('AcceptedStudent')
		));

		$pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
			$studentStatusPattern['Student']['program_id'],
			$studentStatusPattern['Student']['program_type_id'],
			$studentStatusPattern['AcceptedStudent']['academicyear']
		);

		$student_registrations = $this->find('all', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
			),
			'recursive' => -1,
			'order' => array('CourseRegistration.created ASC'),
			'group' => array('CourseRegistration.academic_year', 'CourseRegistration.semester')
		));

		$semester_count = 0;

		if(!empty($student_registrations)){
			foreach ($student_registrations as $key => $student_status) {
				if (strcasecmp($student_status['CourseRegistration']['academic_year'], $acadamic_year) == 0 && strcasecmp($student_status['CourseRegistration']['semester'], $semester) == 0) {
					break;
				} else {
					$semester_count++;
				}
			}
		}

		$year_level = ((int) ($semester_count / 2)) + 1;

		if ($semester_count % 2 > 0) {
			$semster_level = 'II';
		} else {
			$semster_level = 'I';
		}

		$name = '';
		switch ($year_level) {
			case 1:
				$name = $year_level . 'st';
				break;
			case 2:
				$name = $year_level . 'nd';
				break;
			case 3:
				$name = $year_level . 'rd';
				break;
			default:
				$name = $year_level . 'th';
		}

		$status_level['year'] = $name;
		$status_level['semester'] = $semster_level;
		
		return $status_level;
	}

	function listOfCoursesWithFx($department_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $pre = 0, $onlySelectedByStudent = 0)
	{
		debug($onlySelectedByStudent);

		if ($pre == 0) {
			$publishedCourses = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.academic_year' => $acadamic_year,
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and given_by_department_id="' . $department_id . '" )',
					'CourseRegistration.id in (select course_registration_id from exam_grades where grade="Fx" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));

			$publishedCoursesAdds = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.academic_year' => $acadamic_year,
					'CourseAdd.semester' => $semester,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
					'CourseAdd.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and given_by_department_id="' . $department_id . '" )',
					'CourseAdd.id in (select course_add_id from exam_grades where grade="Fx" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));

		} else {

			$publishedCourses = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.academic_year' => $acadamic_year,
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and college_id="' . $department_id . '"  and department_id is null )',
					'CourseRegistration.id in (select course_registration_id from exam_grades where grade="Fx" and department_approval=1 and registrar_approval=1 )'
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));

			$publishedCoursesAdds = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.academic_year' => $acadamic_year,
					'CourseAdd.semester' => $semester,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
					'CourseAdd.published_course_id in (select id from published_courses where program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"   and college_id="' . $department_id . '"  and department_id is null )',
					'CourseAdd.id in (select course_add_id from exam_grades where grade="Fx" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));
		}

		$publishedCoursesM = array_merge($publishedCourses, $publishedCoursesAdds);
		$organized_Published_courses_by_sections = array();

		if (!empty($publishedCoursesM)) {
			foreach ($publishedCoursesM as $key => $published_course) {
				//check 
				if ($onlySelectedByStudent == 0 || ($onlySelectedByStudent && ClassRegistry::init('FxResitRequest')->publishedCourseSelected($published_course['PublishedCourse']['id']))) {
					if (isset($published_course['CourseRegistration']['id'])) {
						$gradee = $this->ExamGrade->getApprovedGrade($published_course['CourseRegistration']['id'], 1);
					} else if (isset($published_course['CourseAdd']['id'])) {
						$gradee = $this->ExamGrade->getApprovedGrade($published_course['CourseAdd']['id'], 0);
					}
					if ($gradee['grade'] == "Fx") {
						if (isset($published_course['CourseRegistration']['id'])) {
							$organized_Published_courses_by_sections[$published_course['PublishedCourse']['Section']['name']][$published_course['CourseRegistration']['published_course_id']] = $published_course['PublishedCourse']['Course']['course_title'] . ' (' . $published_course['PublishedCourse']['Course']['course_code'] . ')';
						} else if (isset($published_course['CourseAdd']['id'])) {
							$organized_Published_courses_by_sections[$published_course['PublishedCourse']['Section']['name']][$published_course['CourseAdd']['published_course_id']] = $published_course['PublishedCourse']['Course']['course_title'] . ' (' . $published_course['PublishedCourse']['Course']['course_code'] . ')';
						}
					}
				}
			}
		}

		return $organized_Published_courses_by_sections;
	}

	function listOfStudentsWithNGToFWithCheating($department_id, $acadamic_year, $semester, $program_id, $program_type_id, $pre = 0)
	{

		if ($pre == 0) {
			$publishedCourses = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.academic_year' => $acadamic_year,
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and given_by_department_id="' . $department_id . '" )',
					'CourseRegistration.id in (select course_registration_id from exam_grades where grade="NG" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'Student', 
					'PublishedCourse' => array('Section', 'YearLevel', 'Course', 'Department')
				)
			));

			$publishedCoursesAdds = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.academic_year' => $acadamic_year,
					'CourseAdd.semester' => $semester,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
					'CourseAdd.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and given_by_department_id="' . $department_id . '" )',
					'CourseAdd.id in (select course_add_id from exam_grades where grade="NG" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'Student', 
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));

		} else {

			$publishedCourses = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.academic_year' => $acadamic_year,
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.published_course_id in (select id from published_courses where program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"  and college_id="' . $department_id . '"  and department_id is null )',
					'CourseRegistration.id in (select course_registration_id from exam_grades where grade="NG" and department_approval=1 and registrar_approval=1 )'
				), 
				'contain' => array(
					'Student', 
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));

			$publishedCoursesAdds = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.academic_year' => $acadamic_year,
					'CourseAdd.semester' => $semester,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
					'CourseAdd.published_course_id in (select id from published_courses where  program_id="' . $program_id . '" and program_type_id="' . $program_type_id . '"  and academic_year="' . $acadamic_year . '" and semester="' . $semester . '"   and college_id="' . $department_id . '"  and department_id is null )',
					'CourseAdd.id in (select course_add_id from exam_grades where grade="NG" and department_approval=1 and registrar_approval=1)'
				), 
				'contain' => array(
					'Student', 
					'PublishedCourse' => array(
						'Section', 'YearLevel', 'Course', 'Department'
					)
				)
			));
		}

		$publishedCoursesM = array_merge($publishedCourses, $publishedCoursesAdds);
		$studentsList = array();

		if (!empty($publishedCoursesM)) {
			foreach ($publishedCoursesM as $key => $published_course) {
				if (isset($published_course['CourseRegistration']) && !empty($published_course['CourseRegistration']) && $published_course['CourseRegistration']['id'] != "") {
					$grade = ClassRegistry::init('ExamGrade')->getApprovedGrade($published_course['CourseRegistration']['id'], 1);
					$course_reg_ids = ClassRegistry::init('CourseRegistration')->find('list', array(
						'conditions' => array(
							'CourseRegistration.student_id' => $published_course['CourseRegistration']['student_id'],
							'CourseRegistration.id <>' => $published_course['CourseRegistration']['id']
						), 
						'fields' => array(
							'CourseRegistration.id',
							'CourseRegistration.id'
						)
					));

					if (isset($course_reg_ids) && !empty($course_reg_ids)) {
						$exam_grade_ids = ClassRegistry::init('ExamGrade')->find('list', array(
							'conditions' => array(
								'ExamGrade.course_registration_id' => $course_reg_ids,
							), 
							'fields' => array(
								'ExamGrade.id',
								'ExamGrade.id'
							)
						));
					}
				} else {

					$grade = ClassRegistry::init('ExamGrade')->getApprovedGrade($published_course['CourseAdd']['id'], 0);

					$course_reg_ids = ClassRegistry::init('CourseAdd')->find('list', array(
						'conditions' => array(
							'CourseAdd.student_id' => $published_course['CourseAdd']['student_id'],
							'CourseAdd.id <>' => $published_course['CourseAdd']['id']
						), 
						'fields' => array(
							'CourseAdd.id',
							'CourseAdd.id'
						)
					));

					if (isset($course_reg_ids) && !empty($course_reg_ids)) {
						$exam_grade_ids = ClassRegistry::init('ExamGrade')->find('list', array(
							'conditions' => array(
								'ExamGrade.course_add_id' => $course_reg_ids,
							), 
							'fields' => array(
								'ExamGrade.id',
								'ExamGrade.id'
							)
						));
					}
				}

				$isCheating = ClassRegistry::init('ExamGradeChange')->find('first', array(
					'conditions' => array(
						'ExamGradeChange.cheating' => 1,
						'ExamGradeChange.exam_grade_id' => $grade['grade_id']
					)
				));

				if (isset($exam_grade_ids) && !empty($exam_grade_ids)) {
					$previousCheatingCount = ClassRegistry::init('ExamGradeChange')->find('count', array(
						'conditions' => array(
							'ExamGradeChange.cheating' => 1,
							'ExamGradeChange.exam_grade_id' => $exam_grade_ids
						)
					));
				} else {
					$previousCheatingCount = 0;
				}

				if (isset($isCheating) && !empty($isCheating)) {
					$index = count($studentsList);
					$studentsList[$index]['full_name'] = $published_course['Student']['full_name'];
					$studentsList[$index]['studentnumber'] = $published_course['Student']['studentnumber'];
					$studentsList[$index]['recentCheatingCourse'] = $published_course['PublishedCourse']['Course']['course_title'] . ' ' . $published_course['PublishedCourse']['Course']['course_code'];
					$studentsList[$index]['previousCheatingCount'] = $previousCheatingCount;
					$studentsList[$index]['grade_id'] = $grade['grade_id'];
					$studentsList[$index]['grade'] = $grade['grade'];
				}

				/* if (isset($published_course['CourseRegistration']['id'])) {
					$studentsList += ClassRegistry::init('ExamGrade')->getStudentsWithNGCheatingChange($published_course['CourseRegistration']['published_course_id']);
				} else if (isset($published_course['CourseAdd']['id'])) {
					$studentsList += ClassRegistry::init('ExamGrade')->getStudentsWithNGCheatingChange($published_course['CourseAdd']['published_course_id']);
				} */
			}
		}
		return $studentsList;
	}

	public function getlistOfPublishedCourseGradeEntryMissed($department_id, $acadamic_year, $semester, $program_id, $program_type_id)
	{
		$publishedCourses = array();

		if (!empty($department_id) && !empty($acadamic_year) && !empty($semester) && !empty($program_id) && !empty($program_type_id)) {

			$publishedCourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.academic_year' => $acadamic_year,
					'PublishedCourse.semester' => $semester,
					'PublishedCourse.drop' => 0,
					'PublishedCourse.add' => 0,
					'PublishedCourse.program_id' => $program_id,
					'PublishedCourse.program_type_id' => $program_type_id,
					'PublishedCourse.department_id' => $department_id,
					'PublishedCourse.id in (select published_course_id from course_registrations where published_course_id is not null)'
				), 
				'contain' => array(
					'Section',
					'YearLevel',
					'Course', 
					'Department',
					'GivenByDepartment',
					'College'
				)
			));
		}

		$organized_Published_courses_by_sections = array();

		if (!empty($publishedCourses)) {
			foreach ($publishedCourses as $key => $published_course) {
				//check
				$isCourseMissingEntry = $this->Student->getStudentIdsNotRegisteredPublishourse($published_course['PublishedCourse']['id']);
				$gradeSubmitted = $this->ExamGrade->is_grade_submitted($published_course['PublishedCourse']['id']);
				
				if (!empty($isCourseMissingEntry) && $gradeSubmitted) {
					$organized_Published_courses_by_sections[$published_course['Section']['name'] . ' ('. (isset($published_course['YearLevel']['id']) ? $published_course['YearLevel']['name'] : ($published_course['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', '. $published_course['Section']['academicyear']. ')'][$published_course['PublishedCourse']['id']] = $published_course['Course']['course_title'] . ' (' . $published_course['Course']['course_code'] . ')';
				}
			}
		}

		return $organized_Published_courses_by_sections;
	}


	function student_list_not_registred($data = null)
	{
		$search_conditions = array();
		$yearLevelId = null;
		
		//$search_conditions['conditions'][] = array('Student.id NOT IN (select student_id from graduate_lists)');  
		
		// the above search condition is expensive and there is a better checking for not graduated right from students table using graduated field, Neway
		$search_conditions['conditions'][] = array('Student.graduated = 0');
		
		$search_conditions['fields'] = array(
			'Student.id',
			'Student.studentnumber', 
			'Student.full_name',
			'Student.gender',
			'Student.department_id',
			'Student.curriculum_id',  
			'Student.college_id',
			'Student.program_id', 
			'Student.program_type_id',
			'Student.graduated'
		);

		$search_conditions['limit'] = 100;

		$search_conditions['recursive'] = -1;

		$search_conditions['order'] = array('Student.full_name');

		$search_conditions['contain'] = array(
			'Department' => array('fields' => array('id', 'name')), 
			'College' => array('fields' => array('id', 'name')), 
			'Program' => array('fields' => array('id', 'name')),
			'ProgramType' => array('fields' => array('id', 'name')),
			'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
		);

		if (!empty($data['Student']['college_id'])) {
			$search_conditions['conditions'][] = array('Student.college_id' => $data['Student']['college_id']);
			$search_conditions['conditions'][] = array('Student.department_id is null');
		}

		if (!empty($data['Student']['department_id'])) {
			$search_conditions['conditions'][] = array('Student.department_id' => $data['Student']['department_id']);
		}

		if (!empty($data['Student']['program_id'])) {
			$search_conditions['conditions'][] = array('Student.program_id' => $data['Student']['program_id']);
		}

		if (!empty($data['Student']['program_type_id'])) {
			$search_conditions['conditions'][] = array('Student.program_type_id' => $data['Student']['program_type_id']);
		}

		$yearLevelId = '';
		
		if (!empty($data['Student']['year_level_id']) && !empty($data['Student']['department_id'])) {
			$yearLevelId = $this->PublishedCourse->YearLevel->field('id', array(
				'YearLevel.department_id' => $data['Student']['department_id'],
				'YearLevel.name' => $data['Student']['year_level_id']
			));
		}

		$sectionId = '';

		if (!empty($data['Student']['section_id'])) {
			$sectionId = $data['Student']['section_id'];
			$studentListIds = $this->Section->getSectionActiveStudentsId($sectionId);
			$search_conditions['conditions'][] = array('Student.id' => $studentListIds);
		}

		if (!empty($data['Student']['semester']) && !empty($data['Student']['academicyear']) && !empty($sectionId)) {
			$search_conditions['conditions'][] = array('Student.id not in (select DISTINCT student_id  from course_registrations where semester="' . $data['Student']['semester'] . '" and academic_year="' . $data['Student']['academicyear'] . '" and section_id=' . $sectionId . ') ');
		} else if (!empty($data['Student']['semester']) && !empty($data['Student']['academicyear']) && !empty($yearLevelId)) {
			$search_conditions['conditions'][] = array('Student.id not in (select DISTINCT student_id  from course_registrations where semester="' . $data['Student']['semester'] . '" and academic_year="' . $data['Student']['academicyear'] . '" and year_level_id="' . $yearLevelId . '") ');
		}

		$students = $this->Student->find('all', $search_conditions);

		$organized_students = array();

		if (!empty($students)){
			foreach ($students as $k => $v) {
				//$studentSectionDetail = $this->Section->getStudentActiveSection($v['Student']['id']);

				$studentSectionDetail = $this->Section->getStudentActiveSection($v['Student']['id'], $data['Student']['academicyear']); // to remove old batches

				if (!empty($studentSectionDetail['Section']['year_level_id'])) {
					if (!empty($yearLevelId) && $yearLevelId == $studentSectionDetail['Section']['year_level_id']) {
						$organized_students[$v['Program']['name'] . '~' . $v['ProgramType']['name'] . '~' . $studentSectionDetail['YearLevel']['name'] . '~' . $studentSectionDetail['Section']['name'] . '~' . $studentSectionDetail['Section']['id']][] = $v;
					} else if (empty($yearLevelId)) {
						$organized_students[$v['Program']['name'] . '~' . $v['ProgramType']['name'] . '~' . $studentSectionDetail['YearLevel']['name'] . '~' . $studentSectionDetail['Section']['name'] . '~' . $studentSectionDetail['Section']['id']][] = $v;
					}
				} else if (!empty($studentSectionDetail['Section']['college_id'])) {
					$organized_students[$v['Program']['name'] . '~' . $v['ProgramType']['name'] . '~' . 'Pre/Fresh' . '~' . $studentSectionDetail['Section']['name'] . '~' . $studentSectionDetail['Section']['id']][] = $v;
				}
			}
		}

		return $organized_students;
	}

	function registerAllSection($department_id, $program_type_id, $academicYear, $semester) {

		$academic_year['academic_year'] = $academicYear;
		$academic_year['semester'] = $semester;

		if (!empty($department_id) && !empty($program_type_id) && !empty($academicYear) && !empty($semester)) {
			$departmentsSection = $this->Section->find('list', array(
				'conditions' => array(
					'Section.department_id' => $department_id,
					'Section.program_type_id' => $program_type_id, 
					'Section.academicyear' => $academic_year['academic_year']
				)
			));

			if (!empty($departmentsSection)) {
				foreach ($departmentsSection as $k => $v) {
					$status = $this->massRegisterStudent($k, $academic_year);
					echo $status;
				}
			}
		}

		return true;
	}

	public function getRegistrationWithoutStudentSectionCreated($department_id = null)
	{
		if (isset($department_id) && !empty($department_id)) {
			$registeredNotInSection = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.section_id not in (select section_id from students_sections where student_id is not null)'
				), 'contain' => array(
					'PublishedCourse' => array('conditions' => array('PublishedCourse.department_id' => $department_id))
				)
			));
		} else {
			$registeredNotInSection = $this->find('all', array(
				'conditions' => array(
					'CourseRegistration.section_id not in (select section_id from students_sections where student_id is not null)'
				), 
				'recursive' => -1
			));
		}

		//perform the update operation 
		debug(count($registeredNotInSection));

		if (!empty($registeredNotInSection)) {
			foreach ($registeredNotInSection as $k => $v) {
				//check if student and section exists in  table ?
				$sectionc = ClassRegistry::init('StudentsSection')->find('count', array(
					'conditions' => array(
						'StudentsSection.student_id' => $v['CourseRegistration']['student_id'],
						'StudentsSection.section_id' => $v['CourseRegistration']['section_id']
					)
				));

				if ($sectionc == 0) {
					//perform creation 
					$secdata['StudentsSection']['section_id'] = $v['CourseRegistration']['section_id'];
					$secdata['StudentsSection']['student_id'] = $v['CourseRegistration']['student_id'];
					$secdata['StudentsSection']['archive'] = 1;

					if (isset($secdata) && !empty($secdata)) {

						ClassRegistry::init('StudentsSection')->create();
						ClassRegistry::init('StudentsSection')->save($secdata['StudentsSection']);

						$this->Section->id = $v['CourseRegistration']['section_id'];
						$this->Section->saveField('archive', '1');
					}
				}
			}
		}
	}

	public function getAllSectionIdsForStudentFromCourseRegistrations($student_id = null)
	{
		if (isset($student_id) && !empty($student_id)) {
			$section_ids_with_reg = ClassRegistry::init('CourseRegistration')->find('list', array(
				'fields' => array(
					'CourseRegistration.section_id',
				),
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
				),
				'group' => array(
					'CourseRegistration.section_id',
				),

			));

			return $section_ids_with_reg;
		} else {
			return array();
		}
	}
}
