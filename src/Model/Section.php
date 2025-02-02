<?php
class Section extends AppModel
{
	var $name = 'Section';
	var $actsAs = array('Containable');
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
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
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'curriculum_id',
			'conditions' => '',
			'fields' => array('id', 'name', 'type_credit', 'english_degree_nomenclature', 'curriculum_detail'),
			'order' => ''
		)
	);
	var $hasMany = array(
		'CourseInstructorAssignment' => array(
			'className' => 'CourseInstructorAssignment',
			'foreignKey' => 'section_id',
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
		'CourseRegistration' => array(
			'className' => 'CourseRegistration',
			'foreignKey' => 'section_id',
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
		'SectionSplitForPublishedCourse' => array(
			'className' => 'SectionSplitForPublishedCourse',
			'foreignKey' => 'section_id',
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
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'section_id',
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
		'MergedSectionsExam' => array(
			'className' => 'MergedSectionsExam',
			'foreignKey' => 'section_id',
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
		'CourseSchedule' => array(
			'className' => 'CourseSchedule',
			'foreignKey' => 'section_id',
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

	);
	var $hasAndBelongsToMany = array(
		'Student' => array(
			'className' => 'Student',
			'joinTable' => 'students_sections',
			'foreignKey' => 'section_id',
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
		),
		'MergedSectionsCourse' => array(
			'className' => 'MergedSectionsCourse',
			'joinTable' => 'merged_sections_courses_sections',
			'foreignKey' => 'section_id',
			'associationForeignKey' => 'merged_sections_course_id',
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

	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Section name is required'
			),
			'unique' => array(
				'rule' => array('checkUnique', 'name'),
				'message' => 'Section name taken. Use another',
				'on' => 'create',
			),
			'unique2' => array(
				'rule'=>'isUnique',
				'message' => 'Section name taken. Please Use another',
				'on' => 'update',
			)
		),
	);

	public function beforeValidate($options = array())
	{
		return parent::beforeValidate($options);
	}


	//section name is unique
	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			if (!empty($this->data['Section']['year_level_id'])) {
				$section = $this->find('first', array(
					'conditions' => array(
						'Section.name' => $this->data['Section']['name'],
						'Section.academicyear' => $this->data['Section']['academicyear'],
						'Section.year_level_id' => $this->data['Section']['year_level_id'],
						'Section.program_type_id' => $this->data['Section']['program_type_id'],
						'Section.program_id' => $this->data['Section']['program_id'],
						'Section.department_id' => $this->data['Section']['department_id']
					)
				));
			} else {
				$section = $this->find('first', array(
					'conditions' => array(
						'Section.name' => $this->data['Section']['name'],
						'Section.academicyear' => $this->data['Section']['academicyear'],
						'Section.year_level_id is null',
						'Section.program_type_id' => $this->data['Section']['program_type_id'],
						'Section.program_id' => $this->data['Section']['program_id'],
						'Section.college_id' => $this->data['Section']['college_id']
					)
				));
			}

			if (!empty($section)) {
				return $valid;
			}
			$valid = true;
		} else {
			/* $sectionCount = $this->find('count', array('conditions' => array('Section.name' => trim($this->data['Section']['name']))));
			if($sectionCount == 1) {
				debug($sectionCount);
				$valid = true;
			}
			debug($this->data['Section']['name']); */
			
		}

		return $valid;
	}

	function countsectionlessstudents($collegeid = null, $role_id = null, $department_id = null, $year = null, $selected_program = null, $selected_program_type = null, $selected_curriculum = null) {

		$students = $this->Student->get_students_for_countsectionlessstudent($collegeid, $role_id, $department_id, $year, $selected_program, $selected_program_type, $selected_curriculum);
		$sectionless_student_count = 0;

		if (!empty($students)) {
			foreach ($students as $k => $v) {
				$check_student_section = count($v['Section']);
				if ($check_student_section == 0) {
					$sectionless_student_count = $sectionless_student_count + 1;
				} else {
					$is_pre_student = 1;
					foreach ($v['Section'] as $sk => $sv) {
						if (!empty($sv['department_id'])) {
							$is_pre_student = 0;
							break;
						} else {
							if ($sv['StudentsSection']['archive'] == 0) {
								$is_pre_student = 0;
								break;
							} else {
								$is_pre_student = 1;
							}
						}
					}
					if ($is_pre_student == 1) {
						$sectionless_student_count = $sectionless_student_count + 1;
					}
				}
			}
		}
		return $sectionless_student_count;
	}

	function getsectionlessstudentsummary($thisacademicyear = null, $college_id = null, $department_id = null, $role_id = null)
	{

		if (!empty($thisacademicyear) && (!empty($college_id) || !empty($department_id))) {

			$programs = $this->Program->find('list');
			$programtypes = $this->ProgramType->find('list');
			
			$data = array();

			foreach ($programs as $kp => $vp) {
				foreach ($programtypes as $kpt => $vpt) {
					if (ROLE_DEPARTMENT == $role_id) {
						$students = $this->Student->find('list', array(
							'conditions' => array(
								'Student.department_id' => $department_id,
								'Student.program_id' => $kp,
								'Student.program_type_id' => $kpt,
								//'Student.accepted_student_id in (select id from accepted_students where academicyear = "' . $thisacademicyear . '" )',
								'Student.academicyear' => $thisacademicyear,
								'Student.graduated = 0',
								"NOT" => array('Student.curriculum_id' => array(0, 'null', ''))
							), 
							'fields' => array('Student.id', 'Student.id')
						));
					} else {
						$students = $this->Student->find('list', array(
							'conditions' => array(
								'Student.academicyear' => $thisacademicyear,
								'Student.graduated = 0',
								'Student.college_id' => $college_id,
								'Student.program_id' => $kp,
								'Student.program_type_id' => $kpt,
								"Student.department_id is null", 
								/* "OR" => array(
									"Student.department_id is null", 
									"Student.department_id is = ''"
								) */
							),
							'fields' => array('Student.id', 'Student.id')
						));
					}

					$studentCountInSection = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.student_id' => $students)));

					if ($studentCountInSection == 0) {
						$sectionless_student_count = count($students);
					} else {
						$sectionless_student_count = ClassRegistry::init('StudentsSection')->find('count', array(
							'conditions' => array(
								'StudentsSection.student_id' => $students, 
								'StudentsSection.archive' => 1,
								'StudentsSection.section_id in (select id from sections where archive = 1)'
							)
						));
					}

					$data[$vp][$vpt] = $sectionless_student_count;
				}
			}
			//debug($data);
			return $data;
		}
	}

	function getcurriculumunattachedstudentsummary($thisacademicyear = null, $college_id = null, $department_id = null, $role_id = null)
	{
		if (!empty($thisacademicyear) && (!empty($college_id) || !empty($department_id))) {
			$students = null;
			if (ROLE_DEPARTMENT == $role_id) {
				$students = $this->Student->find('list', array(
					'conditions' => array(
						'Student.department_id' => $department_id,
						//'Student.accepted_student_id in (select id from accepted_students where academicyear="' . $thisacademicyear . '" )',
						'Student.academicyear' => $thisacademicyear,
						'Student.graduated = 0',
						"OR" => array('Student.curriculum_id' => array(0, 'null', ''))
					), 
					'fields' => array('Student.id', 'Student.id')
				));
			}

			$studentCountInSection = ClassRegistry::init('StudentsSection')->find('count', array(
				'conditions' => array(
					'StudentsSection.student_id' => $students
				), 
				'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
			));

			if ($studentCountInSection == 0) {
				$sectionless_student_count = count($students);
			} else {
				$sectionless_student_count = ClassRegistry::init('StudentsSection')->find('count', array(
					'conditions' => array(
						'StudentsSection.student_id' => $students, 
						'StudentsSection.archive' => 1,
						'StudentsSection.section_id in (select id from sections where archive = 1)'
					),
					'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
				));
			}
			return $sectionless_student_count;
		}
	}

	function getSectionlessStudentCurriculum($thisacademicyear = null, $college_id = null, $department_id = null, $role_id = null, $selected_program = null, $selected_program_type = null) 
	{

		$students = $this->Student->get_students_curriculum_for_section(
			$thisacademicyear,
			$college_id,
			$department_id,
			$role_id,
			$selected_program,
			$selected_program_type
		);

		$sectionless_student_curriculum_array = array();

		if (!empty($students)) {
			foreach ($students as $k => $v) {
				$studentCountInSection = ClassRegistry::init('StudentsSection')->find('count', array(
					'conditions' => array(
						'StudentsSection.student_id' => $v['Student']['id'],
						'StudentsSection.archive' => 0
					),
					'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
				));

				if (!$studentCountInSection) {
					$sectionless_student_curriculum_array[] = $v['Student']['curriculum_id'];
				}
				/* 
				$check_student_section = count($v['Section']);
				if ($check_student_section == 0 || (count($v['Section']) == 1 &&
					empty($v['Section'][0]['department_id']))) {
					//if($v['Student']['curriculum_id'])
					$sectionless_student_curriculum_array[] = $v['Student']['curriculum_id'];
				} */
			}
		}
		debug($students);
		return array_unique($sectionless_student_curriculum_array);
	}

	function getSectionForAssignment($academicyear = null, $college_id = null, $department_id = null, $role_id = null, $selected_program = null, $selected_program_type = null, $yearlevel = null, $selected_curriculum = null)
	{

		$program_type_id = $selected_program_type;
		$find_the_equvilaent_program_type = unserialize($this->ProgramType->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $selected_program_type)));

		if (!empty($find_the_equvilaent_program_type)) {
			$selected_program_type_array = array();
			$selected_program_type_array[] = $selected_program_type;
			$program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
		}
		//Search using by department and year level as well if user role is not college (use role is department)

		if (ROLE_COLLEGE != $role_id) {
			$conditions = array(
				'Section.academicyear LIKE ' => $academicyear . '%',
				//'Section.college_id'=>$college_id,
				'Section.department_id' => $department_id, 
				'Section.program_type_id' => $program_type_id,
				'Section.year_level_id' => $yearlevel, 
				'Section.program_id' => $selected_program,
				'Section.archive' => 0
			);
		} else {
			$conditions = array(
				'Section.academicyear LIKE ' => $academicyear . '%', 
				'Section.college_id' => $college_id, 
				'Section.program_id' => $selected_program, 
				'Section.program_type_id' => $program_type_id,
				'Section.archive' => 0,
				"OR" => array("Section.department_id is null", "Section.department_id" => array(0, ''))
			);
		}

		$sections = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array('Section.id', 'Section.name'),
			'contain' => array(
				'Student' => array(
					'fields' => array(
						'Student.id',
						'Student.studentnumber',
						'Student.full_name',
						'Student.academicyear',
						'Student.gender',
						'Student.graduated'
					),
					'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
				)
			)
		));

		debug($sections);
		//then find empty sections and section that have students in selected_curriculum
		$empty_and_selectedCurriculumStudents_sections = array();
		
		if (!empty($sections)) {
			foreach ($sections as $sk => $sv) {
				//Find section curriculum for one of section students and compare with selected curriculum if it is similar, then hold that section
				if (count($sv['Student']) > 0) {
					$first_student_id = $sv['Student'][0]['id'];
					$student_curriculum_id = $this->Student->field('Student.curriculum_id', array('Student.id' => $first_student_id));
					if ($selected_curriculum == $student_curriculum_id) {
						$empty_and_selectedCurriculumStudents_sections[] = $sv;
					}
				} else {
					//empty section
					$empty_and_selectedCurriculumStudents_sections[] = $sv;
				}
			}
		}
		return $empty_and_selectedCurriculumStudents_sections;
	}

	function currentsectionsoccupation($sections = null)
	{
		if (!empty($sections)) {
			$data = array();
			foreach ($sections as $k => $v) {
				$count = 0;
				foreach ($v['Student'] as $sk => $sv) {
					if ($sv['StudentsSection']['archive'] == 0) {
						$count++;
					}
				}
				$data[$k] = $count;
			}
			//debug($data);
			//debug($sections);
			return $data;
		}
	}

	function sectionscurriculum($studentssections = null)
	{
		if (!empty($studentssections)) {
			//Find section curriculum for one of section students
			$sections_curriculum_name = array();
			foreach ($studentssections as $ssk => $ssv) {
				//debug($ssv['Curriculum']['name']);
				if (isset($ssv['Curriculum']['name']) && !empty($ssv['Curriculum']['name'])) {
					$sections_curriculum_name[$ssk] = $ssv['Curriculum']['name'] . (count(explode('ECTS', $ssv['Curriculum']['type_credit'])) >= 2 ? ' (ECTS)' : ' (Credit)');
				} else {
					if (count($ssv['Student']) > 0) {
						$nonarchivekey = -1;
						foreach ($ssv['Student'] as $key => $value) {
							if ($value['StudentsSection']['archive'] == 0) {
								$nonarchivekey = $key;
								break;
							}
						}
						if ($nonarchivekey != -1) {
							$first_student_id = $ssv['Student'][$nonarchivekey]['id'];
							$student_curriculum_id = $this->Student->field('Student.curriculum_id', array('Student.id' => $first_student_id));
							$sections_curriculum_name[$ssk] =  $this->Student->Curriculum->field('Curriculum.curriculum_detail', array('Curriculum.id' => $student_curriculum_id));
						} else {
							$sections_curriculum_name[$ssk] = "The section is empty";
						}
					} else {
						$sections_curriculum_name[$ssk] = "The section is empty";
					}
				}
			}
			return $sections_curriculum_name;
		}
	}

	function sectionscurriculumID($section_id = null)
	{
		if (isset($section_id) && !empty($section_id)) {
			$studentssections = $this->find('all', array(
				'conditions' => array(
					'Section.id' => $section_id,
					'Section.archive = 0'
				), 
				'contain' => array(
					'Student' => array(
						'conditions' => array(
							'Student.graduated = 0'
						),
						'fields' => array(
							'Student.id', 
							'Student.studentnumber', 
							'Student.full_name', 
							'Student.gender',
							'Student.graduated',
							'Student.curriculum_id'
						),
						'Curriculum' => array(
							'fields' => array(
								'Curriculum.id', 
								'Curriculum.name', 
							)
						)
					),
					'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
				),
				'recursive' => -1
			));

			debug($studentssections);

			if (isset($studentssections) && !empty($studentssections)) {
				
				if (is_null($studentssections[0]['Section']['department_id']) || empty($studentssections[0]['Section']['department_id'])) {
					// freshman 
					return -1;
				}

				debug(count($studentssections[0]['Student']));

				if (isset($studentssections[0]['Curriculum']['id']) && is_numeric($studentssections[0]['Curriculum']['id']) && $studentssections[0]['Curriculum']['id']) {
					return $studentssections['Curriculum']['id'];
				} else if (empty($studentssections[0]['Student']) || (count($studentssections[0]['Student']) == 0)) {
					// empty Section 
					return 0;
				} else {

					// Get A Curriculum ID from Published courses if there is any published course in the name of the section

					$publishedCourseForSection = $this->PublishedCourse->find('first', array(
						'conditions' => array(
							'PublishedCourse.section_id' => $section_id
						),
						'contain' => array(
							'Course' => array(
								'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
							)
						),
						'order' => array('PublishedCourse.id'=> 'DESC'),
						'recursive' => -1
					));

					debug($publishedCourseForSection);

					if (isset($publishedCourseForSection['Course']['Curriculum']['id']) && is_numeric($publishedCourseForSection['Course']['Curriculum']['id']) && $publishedCourseForSection['Course']['Curriculum']['id']) {
						debug($publishedCourseForSection['Course']['Curriculum']['id']);
						return $publishedCourseForSection['Course']['Curriculum']['id'];
					} else {

						//Find section curriculum for one of section students

						$sections_curriculum_ids = array();
						$totalStudentCount = 0;
						$graduatedStudentCount= 0;
						$notGraduatedStudentCount = 0;
						$graduatedStudentCount = 0;

						foreach ($studentssections as $ssk => $ssv) {
							$totalStudentCount = count($ssv['Student']);
							debug($totalStudentCount);
							if (count($ssv['Student']) > 0) {
								$nonarchivekey = -1;
								$graduatedkey = -1;

								foreach ($ssv['Student'] as $key => $value) {
									if ($value['StudentsSection']['archive'] == 0) {
										$nonarchivekey = $key;
										if (isset($value['Curriculum']['id']) && !empty($value['Curriculum']['id'])) {
											//if (!is_null($value['Curriculum']['id']) && ($value['Curriculum']['id'] != '' && $value['Curriculum']['id'] != 0)) {
												$sections_curriculum_ids[$key] = $value['Curriculum']['id'];
											//}
										}
										$notGraduatedStudentCount++;
									}

									if ($value['graduated'] == 1) {
										$graduatedkey = $key;
										unset($sections_curriculum_ids[$key]);
										$graduatedStudentCount++;
									}
								}
								//debug($ssv['Student'][$nonarchivekey]['Curriculum']['id']);

								if ($graduatedStudentCount == $totalStudentCount) {
									$sections_curriculum_ids[0] = -1;
								}
							}
						}

						debug(count($sections_curriculum_ids));

						$uniqueSectionCurrIDs = array_unique($sections_curriculum_ids);

						if (count($uniqueSectionCurrIDs) == 1)  {
							debug($uniqueSectionCurrIDs);
							return $uniqueSectionCurrIDs[0];
							//return $uniqueSectionCurrIDs;
						} else {
							if(!$totalStudentCount && $graduatedStudentCount == 0 ) {
								debug($totalStudentCount);
								$sections_curriculum_ids[0] = -2;
								return $sections_curriculum_ids;
							} else {
								if(empty($uniqueSectionCurrIDs)) {
									debug(count($sections_curriculum_ids));
									$sections_curriculum_ids[0] = -1;
									return $sections_curriculum_ids;
								} else {
									// sort array and return the biggest curr iD
									debug($uniqueSectionCurrIDs);
									return $uniqueSectionCurrIDs;
								}
							}
						}
					}
				}
				
			}
			return array();
		}
		return array();
	}

	function get_section_curriculum($section_id = null)
	{
		if (!empty($section_id)) {
			//$section_data = $this->get_section_data($section_id);
			$section_curriculum_id = $this->getSectionCurriculum($section_id);

			if (!empty($section_curriculum_id)) {
				return $section_curriculum_id;
			} else {
				return "nostudentinsection";
			}
		}
	}

	function get_section_data($section_id = null)
	{
		if (!empty($section_id)) {
			$section_data = $this->find('all', array(
				'conditions' => array('Section.id' => $section_id),
				'fields' => array('Section.id'), 
				'contain' => array(
					'Student' => array('fields' => array('Student.id'))
				)
			));
			if (!empty($section_data)) {
				return $section_data;
			}
		}
		return array();
	}

	function getSectionCurriculum($sectionId)
	{

		if (!empty($sectionId)) {

			$section_data = $this->find('first', array(
				'conditions' => array(
					'Section.id' => $sectionId
				), 
				'fields' => array('Section.id'), 
				'contain' => array(
					'Student' => array(
						'fields' => array(
							'Student.id', 
							'Student.curriculum_id',
							'Student.accepted_student_id'
						),
						'conditions' => array('Student.id in (select student_id from course_registrations WHERE section_id = '. $sectionId. ')'), 
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
						'limit' => 1
					),
					'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
				)
			));

			debug($section_data);

			if (isset($section_data['Curriculum']['id']) && is_numeric($section_data['Curriculum']['id']) && $section_data['Curriculum']['id']) {
				//return $section_data['Curriculum']['id'];
			} else {

				$section_dataa = $this->StudentsSection->find('first', array('conditions' => array('StudentsSection.section_id' => $sectionId), 'recursive' => -1));
				
				if (isset($section_dataa['StudentsSection']['student_id']) && !empty($section_dataa['StudentsSection']['student_id'])) {
					$section_data['Student'][0] = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $section_dataa['StudentsSection']['student_id']), 'recursive' => -1))['Student'];
				}

				if (!empty($section_data['Student'][0]['id'])) {
					$findCurriculumHistorys = ClassRegistry::init('CurriculumAttachment')->find('all', array('conditions' => array('CurriculumAttachment.student_id' => $section_data['Student'][0]['id']), 'recursive' => -1));
				}

				if ((isset($findCurriculumHistorys) && count($findCurriculumHistorys) == 1) || empty($findCurriculumHistorys)) {
					if (isset($section_data['Student'][0]['curriculum_id'])) {
						return $section_data['Student'][0]['curriculum_id'];
					} else {
						return null;
					}
				} else {
					// find one student which has that curriculum and check section belongs to that student , return z curriculum id
					foreach ($findCurriculumHistorys as $k => $v) {

						$findOtherStudents = ClassRegistry::init('Student')->find('first', array(
							'conditions' => array(
								'Student.curriculum_id' => $v['curriculum_id'],
								'Student.id !=' => $v['student_id']
							)
						));

						if (!empty($findOtherStudents)) {
							$belongsToSection = $this->StudentsSection->find('count', array('conditions' => array('StudentsSection.student_id' => $findOtherStudents['Student']['id'], 'StudentsSection.section_id' => $sectionId)));

							if ($belongsToSection) {
								return $v['curriculum_id'];
							}
						}
					}

					if (!empty($section_data['Student'][0]['curriculum_id'])) {
						return $section_data['Student'][0]['curriculum_id'];
					}
				}
			}
		}
		return null;
	}

	function isSectionsHaveTheSameCurriculum($sections_data = null)
	{
		if (!empty($sections_data)) {
			//find selected sections id and theirs curriculum array
			$selected_section_curriculum_array = array();
			foreach ($sections_data['Section']['Sections'] as $k => $v) {
				$selecte_section_id = $sections_data['Section'][$v]['id'];
				$selected_section_data = $this->get_section_data($selecte_section_id);
				foreach ($selected_section_data as $skk => $svv) {
					foreach ($svv['Student'] as $skkk => $svvv) {
						$selectedCurrriculum = $this->Student->field('Student.curriculum_id', array('Student.id' => $svvv['id']));
						if ($selectedCurrriculum) {
							$selected_section_curriculum_array[] = $selectedCurrriculum;
							break 2;
						}
					}
				}
			}

			if (count(array_unique($selected_section_curriculum_array)) > 1) {
				return false;
			} else {
				return true;
			}
		}
	}

	function studentsection($college_id = null, $role_id = null, $department_id = null, $selected_program = null, $program_type_id = null, $academic_year = null, $selected_year_level = null) 
	{
		if ($role_id) {
			//Search using by department as well if user role is not college (use role is department)
			// debug( $this->StudentsSection->find('all',array('conditions'=>array('StudentsSection.archive'=>0))));
			$options = array();
			if ($role_id != ROLE_COLLEGE) {
				$conditions = array(
					'Section.department_id' => $department_id, 
					'Section.archive' => 0,
					'Section.program_id' => $selected_program,
					'Section.program_type_id' => $program_type_id,
					'Section.year_level_id' => $selected_year_level,
					'Section.academicyear ' => $academic_year,
				);
			} else {
				$conditions = array(
					'Section.college_id' => $college_id, 
					'Section.archive' => 0,
					'Section.program_id' => $selected_program,
					'Section.program_type_id' => $program_type_id,
					'Section.academicyear ' => $academic_year,
					"OR" => array(
						"Section.department_id is null", 
						"Section.department_id = ''"
					)
				);
			}

			$section = $this->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
					'Student' => array(
						'fields' => array(
							'Student.id',
							'Student.studentnumber',
							'Student.full_name',
							'Student.gender',
							'Student.graduated',
							'Student.academicyear'
						),
						'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
						/* 'CourseRegistration' => array(
							//'fields'=> array('id', 'year_level_id', 'section_id', 'published_course_id','academic_year', 'semester'),
							'limit' => 1,
							'order' => array('CourseRegistration.id' => 'DESC')
						) */
					),
					'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					'Department' => array('id', 'name', 'type', 'college_id'),
					'College' => array('id', 'name', 'type', 'campus_id', 'stream'),
					'YearLevel' => array('id', 'name'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
				),
				'recursive' => -1,
			));

			return $section;
		}
		return false;
	}

	function studentsSectionById($sectionid = null)
	{
		if ($sectionid) {
			$students = $this->find('all', array(
				'conditions' => array(
					'Section.id' => $sectionid, 
					'Section.id in (select section_id from students_sections where section_id = ' . $sectionid . ' AND student_id is not null )'
				),
				'fields' => array(
					'Section.id', 
					'Section.name', 
					'Section.program_id',
					'Section.program_type_id', 
					'Section.college_id', 
					'Section.department_id', 
					'Section.academicyear'
				),
				'contain' => array(
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'Department' => array('id', 'name', 'type', 'college_id'),
					'College' => array(
						'fields' => array('id', 'name', 'type', 'campus_id', 'stream'),
						'Campus' => array('id', 'name'),
					),
					'YearLevel' => array('id', 'name'),
					'Student' => array(
						'fields' => array(
							'Student.id',
							'Student.studentnumber',
							'Student.full_name',
							'curriculum_id',
							'Student.gender',
							'Student.graduated',
							'Student.academicyear'
						),
						'conditions' => array(
							'Student.id in (select student_id from students_sections where archive = 0  and section_id = "' . $sectionid . '" GROUP BY student_id, section_id)'
						),
						'order' => array('Student.academicyear' => 'DESC', 'Student.id' =>'ASC', 'Student.full_name' => 'ASC'),
						'CourseRegistration' => array(
							'PublishedCourse',
							'ExamGrade' => array('id', 'course_registration_id', 'course_add_id', 'grade'),
							'CourseDrop' => array('id', 'course_registration_id', 'semester', 'student_id', 'academic_year')
						),
						'CourseAdd' => array(
							'fields' => array('id', 'student_id', 'published_course_id'), 
							'PublishedCourse' => array(
								'Course' => array('id')
							)
						)
					),
					'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
				),
			));

			return $students;
			//debug($students);
		}
		return false;
	}

	function isSectionAssignedStudentsEqualToTotalNumberofAvaliableStudents($data = null, $sectionlesstotalstudents = null)
	{
		$AssignedStudentSum = 0;
		if (!empty($data)) {
			unset($data['academicyearSearch']);
			unset($data['year_level_id']);
			unset($data['assignment_type']);
			unset($data['academicyear']);
			unset($data['program_id']);
			unset($data['program_type_id']);
			unset($data['Curriculum']);
			foreach ($data as $value) {
				$AssignedStudentSum += $value['number'];
			}
			if ($sectionlesstotalstudents != $AssignedStudentSum) {
				$this->invalidate('section', 'The sum of assigned students in all section must be equal to the total number of students those are not assigned to any section which is ' . $sectionlesstotalstudents);
				return false;
			}
		}
		return true;
		//debug($AssignedStudentSum);
	}

	/* function isNewSectionNameIsUnique($sections_data=null,$role_id=null){
        if(!empty($sections_data)){
            //find selected section id array
            $count_selected_sections =0;
            $count_selected_sections = count($sections_data['Section']['Sections']);
            $selecte_section_id_array = array();
            foreach($sections_data['Section']['Sections'] as $k=>$v){
                $selecte_section_id_array[] = $sections_data['Section'][$v]['id'];
            }
            $newSectionName = $sections_data['Section']['new_section_name'];
            //find all section id array
            unset($sections_data['Section']['program_id']);
            unset($sections_data['Section']['program_type_id']);
            unset($sections_data['Section']['Sections']);
            unset($sections_data['Section']['new_section_name']);
            if(ROLE_COLLEGE != $role_id ){
            unset($sections_data['Section']['year_level_id']);
            }
            $count_all_selected_sections =0;
            $count_all_selected_sections = count($sections_data['Section']);
            $selecte_all_section_id_array = array();
            for($i=0;$i<$count_all_selected_sections;$i++){
                $selecte_all_section_id_array[] = $sections_data['Section'][$i]['id'];
            }
            //compete unselected section id array
            $unselected_section_id_array = array_diff($selecte_all_section_id_array,$selecte_section_id_array);
            
            //Comparing each unselected section name with new merged class name
            foreach($unselected_section_id_array as $kus=>$vus) {
                $unselected_section_name = $this->field('Section.name',array('Section.id'=>$vus));
                if(strcasecmp(trim($unselected_section_name),trim($newSectionName)) ==0) {
                    return false;
                }
            }
            return true;
        }
        
    } */

	/*function isNewSectionNamesIsUniqueForSplit($sections_data=null,$role_id=null) {
        if(!empty($sections_data)){
            //find selected section id 
            $selected_section_id = $sections_data['Section'][$sections_data['Section']['selectedsection']]['id'];
            $split_into_number_of_section = $sections_data['Section']['number_of_section'];
            $new_section_names_array = array();
            for($i=1;$i<=$split_into_number_of_section;$i++) {
                $new_section_names_array["Section_".$i."_name"] = 
                        $sections_data['Section']["Section_".$i."_name"];
                unset($sections_data['Section']["Section_".$i."_name"]);
            }
            unset($sections_data['Section']['selectedsection']);
            unset($sections_data['Section']['number_of_section']);
            if(ROLE_COLLEGE != $role_id ){
            unset($sections_data['Section']['year_level_id']);
            }
            //find all section id array
            $count_all_sections =0;
            $count_all_sections = count($sections_data['Section']);
            $all_section_id_array = array();
            for($i=0;$i<$count_all_sections;$i++){
                $all_section_id_array[] = $sections_data['Section'][$i]['id'];
            }
            //compete unselected section id array
            $selected_section_id_array = array();
            $selected_section_id_array[] = $selected_section_id;
            $unselected_section_id_array = array_diff($all_section_id_array,$selected_section_id_array);
            
            //find unselected section name from its id from the database
            $unselected_section_name_array = array();
            foreach($unselected_section_id_array as $kus=>$vus) {
                 $unselected_section_name_array[] = $this->field('Section.name',array('Section.id'=>$vus));
            }
            //debug($unselected_section_name_array);
            //Comparing each unselected section name with new split section name
            $similar_name_array = array(); 
            foreach($new_section_names_array as $knsn=>$vnsn){
                foreach($unselected_section_name_array as $kus=>$vus) {
                    if(strcasecmp(trim($vus),trim($vnsn)) ==0) {
                       $similar_name_array []= $vnsn;
                    }
                }
            }
        return $similar_name_array;
        }
    } */

	/* function isSimilarInputsNameWithThemself($sections_data=null) {
        if(!empty($sections_data)){
            //find input new section names 
            $split_into_number_of_section = $sections_data['Section']['number_of_section'];
            $new_section_names_array = array();
            for($i=1;$i<=$split_into_number_of_section;$i++) {
                $new_section_names_array[] = $sections_data['Section']["Section_".$i."_name"];
            }
            //debug($new_section_names_array);
            $duplicate_input_name_array = array();
            //debug($split_into_number_of_section);
            
            for($j=0;$j < ($split_into_number_of_section - 1);$j++) {
                $duplcated =0;
                for($k=$j+1; $k < $split_into_number_of_section;$k++) {
                    if(strcasecmp(trim($new_section_names_array[$j]),trim($new_section_names_array[$k])) == 0) {
                        $duplcated =1;
                        break;
                    }
                }
                if($duplcated >=1){
                    $duplicate_input_name_array[] = $new_section_names_array[$j];
                 }
            }
        //debug($duplicate_input_name_array);
        return $duplicate_input_name_array;
        }
    } */

	/*function Selected_and_Merged_SectionsStudents($sections_data=null) {
        if(!empty($sections_data)){
            //find selected section id array
			debug($sections_data);
            $selecte_section_id_array = array();
            foreach($sections_data['Section']['Sections'] as $k=>$v){
                $selecte_section_id_array[] = $sections_data['Section'][$v]['id'];
            }
            $selected_and_merged_sectionstudents_array = array();
            $merged_sectionstudents_array = array();
            foreach($selecte_section_id_array as $kss=>$vss) {
                $sections = $this->find('all',array('conditions'=>array('Section.id'=>$vss)));
				//debug($sections);
                $selected_sectionstudents_array = array();
                $count_students_per_section = count($sections[0]['Student']);
                for($i=0;$i<$count_students_per_section;$i++) {
                    $selected_sectionstudents_array[] = $sections[0]['Student'][$i]['id']; //student per section
                    $merged_sectionstudents_array []= $sections[0]['Student'][$i]['id']; // all students;
                }
            $selected_and_merged_sectionstudents_array[$vss] = $selected_sectionstudents_array;
            }
            $selected_and_merged_sectionstudents_array['merged_Section'] = $merged_sectionstudents_array;
           return $selected_and_merged_sectionstudents_array;
        }
    }*/

	/* function Split_and_parent_section_students($sections_data=null){
        if(!empty($sections_data)) {
            //find parent section section id and its students
            $parent_section_id = $sections_data['Section'][$sections_data['Section']['selectedsection']]['id'];
            $parent_section_students_array = array();
            $sections = $this->find('all',array('conditions'=>array('Section.id'=>$parent_section_id)));
            $count_students_of_parent_section = count($sections[0]['Student']);
            for($i=0;$i<$count_students_of_parent_section;$i++) {
                $parent_section_students_array[] = $sections[0]['Student'][$i]['id']; //student of parent section
            }
            //distribute parent section student randomly to children
            $number_of_children_section = $sections_data['Section']['number_of_section'];
            $chlidren_sections_students_associate_array = array(); 
            $k =0; //first child section index
            for($j=0;$j<$count_students_of_parent_section;$j++) {
                $chlidren_sections_students_associate_array[$sections_data['Section']['Section_'.($k+1).'_name']][] =
                    $parent_section_students_array[$j];
                $k =$k +1;
                if(($k % $number_of_children_section) == 0) {
                    $k = 0;
                }
            }
            $Splited_and_parent_section_students_associate_array = array();
            $Splited_and_parent_section_students_associate_array['parent'] = $parent_section_students_array;
            $Splited_and_parent_section_students_associate_array['children'] = $chlidren_sections_students_associate_array;
            return $Splited_and_parent_section_students_associate_array;
        }
    }*/
	function isSectionEmpty($section_id = null)
	{
		if (!empty($section_id)) {
			$sections = $this->find('all', array(
				'conditions' => array(
					'Section.id' => $section_id
				),
				'fields' => array('Section.id'),
				'contain' => array('Student'),
				'limit' => 1
			));
			debug($sections);
			if (count($sections[0]['Student']) == 0) {
				return true;
			} else {
				return false;
			}
		}
	}

	function isCoursePublishedInTheSection($section_id)
	{
		if (!empty($section_id)) {
			$sections = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.section_id' => $section_id)));
			if ($sections > 0) {
				return true;
			} else {
				return false;
			}
		}
	}

	function updateSectionCurriculumIDFromPublishedCoursesOfTheSection($section_id = null)
	{
		if (!empty($section_id)) {

			$isset_section_curriculum = $this->field('curriculum_id', array('Section.id' => $section_id));

			debug($isset_section_curriculum);

			if (!isset($isset_section_curriculum) || empty($isset_section_curriculum)) {
				
				$count_published_courses = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.section_id' => $section_id)));
				
				if ($count_published_courses > 0) {

					$publishedCourseForSection = $this->PublishedCourse->find('first', array(
						'conditions' => array(
							'PublishedCourse.section_id' => $section_id
						),
						'fields' => array('id', 'course_id', 'section_id', 'academic_year', 'semester'),
						'contain' => array(
							'Course' => array(
								'fields' => array('id', 'course_title', 'credit', 'curriculum_id', 'active'),
								'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
							)
						),
						'order' => array('PublishedCourse.id'=> 'ASC'),
						'recursive' => -1
					));

					debug($publishedCourseForSection);

					if (isset($publishedCourseForSection['Course']['Curriculum']['id']) && is_numeric($publishedCourseForSection['Course']['Curriculum']['id']) && $publishedCourseForSection['Course']['Curriculum']['id']) {
						debug($publishedCourseForSection['Course']['Curriculum']['id']);
						$currID = $publishedCourseForSection['Course']['Curriculum']['id'];
						$this->id = $section_id;
						$this->saveField('curriculum_id', $currID);
						return 1;
					}
				} 
			}

		} else {

			$update_status =  array();
			$sections_with_null_curriculum = $this->find('list', array('conditions' => array('Section.curriculum_id IS NULL'/* , 'Section.department_id IS NOT NULL' */), 'fields'=> array('Section.id')));

			if (!empty($sections_with_null_curriculum)) {

				//debug($sections_with_null_curriculum);

				$updated_sections_count = 0;
				
				foreach ($sections_with_null_curriculum as $skey => $svalue) {
					
					$count_published_courses = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.section_id' => $skey)));
				
					if ($count_published_courses > 0) {

						$publishedCourseForSection = $this->PublishedCourse->find('first', array(
							'conditions' => array(
								'PublishedCourse.section_id' => $skey
							),
							'fields' => array('id', 'course_id', 'section_id', 'academic_year', 'semester'),
							'contain' => array(
								'Course' => array(
									'fields' => array('id', 'course_title', 'credit', 'curriculum_id', 'active'),
									'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
								)
							),
							'order' => array('PublishedCourse.id'=> 'ASC'),
							'recursive' => -1
						));

						//debug($publishedCourseForSection);

						if (isset($publishedCourseForSection['Course']['Curriculum']['id']) && is_numeric($publishedCourseForSection['Course']['Curriculum']['id']) && $publishedCourseForSection['Course']['Curriculum']['id']) {
							//debug($skey);
							//debug($publishedCourseForSection['Course']['Curriculum']['id']);
							$currID = $publishedCourseForSection['Course']['Curriculum']['id'];
							$this->id = $skey;
							$this->saveField('curriculum_id', $currID);
							$updated_sections_count++;
						}
					} 

				}

				$update_status['sections_with_null_curriculum_count'] = count($sections_with_null_curriculum);
				$update_status['updated_sections_count'] = $updated_sections_count;

				return $update_status;
			}
		}
	}

	/**
		 * Synchronization function that automatically update published course is in the
		 * student registreation table while students are move from section to section
		 * return true if move is allowed and preformated array for updating the course 
		 * registration table, false if move is not allowed to move other section 
	*/

	function isMoveAllowed($orginal_section_id = null, $student_id = null, $selected_section_move_id = null)
	{

		$student_full_name = $this->Student->field('Student.full_name', array('Student.id' => $student_id));
		$new_section_name = $this->field('Section.name', array('Section.id' => $selected_section_move_id));
		$new_section_detail = $this->find('first', array('conditions' => array('Section.id' => $selected_section_move_id), 'contain' => array('YearLevel')));
		$studentYearLevel = $this->Student->StudentExamStatus->studentYearAndSemesterLevel($student_id);
		
		if (!empty($studentYearLevel['year']) && $studentYearLevel['year'] != $new_section_detail['YearLevel']['name']) {
			$this->invalidate('move_not_allowed', '' . $student_full_name . ' will not be to move to section ' . $new_section_name . '. The Target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year.');
			return false;
		}

		$latest_semester_academic_year_published_course = $this->PublishedCourse->find('first', array(
			'conditions' => array('PublishedCourse.section_id' => $orginal_section_id),
			'fields' => array(
				"MAX(PublishedCourse.created)",
				'PublishedCourse.id', 
				'PublishedCourse.course_id', 
				'PublishedCourse.academic_year',
				'PublishedCourse.semester', 
				'PublishedCourse.section_id'
			),
			'group' => 'PublishedCourse.semester', 'order' => "MAX(PublishedCourse.created) desc", 'recursive' => -1
		));

		if (!empty($latest_semester_academic_year_published_course['PublishedCourse']['academic_year']) && !empty($latest_semester_academic_year_published_course['PublishedCourse']['semester'])) {
			
			$count = 0;
			$registration_count_with_latest_publish = 0;
			$registered_courses_ids = array();
			$previous_section_published_course_ids = array();
			$new_section_publish_courses_ids = array();

			//get list published courses for a particular students
			$list_published_courses_students_registered = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.section_id' => $orginal_section_id,
					'PublishedCourse.semester' => $latest_semester_academic_year_published_course['PublishedCourse']['semester'],
					'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year']
				),
				'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id', 'PublishedCourse.academic_year', 'PublishedCourse.semester', 'PublishedCourse.section_id'),
				'contain' => array(
					'CourseRegistration' => array(
						'conditions' => array(
							'CourseRegistration.student_id' => $student_id
						),
						'fields' => array(
							'CourseRegistration.student_id',
							'CourseRegistration.id',
							'CourseRegistration.published_course_id'
						)
					)
				)
			));

			// if there is no published courses 
			$registered_published_course_ids = array();

			if (!empty($list_published_courses_students_registered)) {
				foreach ($list_published_courses_students_registered as $list_published => $registered) {
					if (!empty($registered['CourseRegistration']) && count($registered['CourseRegistration']) > 0) {
						$registration_count_with_latest_publish++;
						$registered_courses_ids[] = $registered['CourseRegistration'][0]['id'];
						$registered_published_course_ids['CourseRegistration'][$count]['id'] = $registered['CourseRegistration'][0]['id'];
					}
					$previous_section_published_course_ids[] = $registered['PublishedCourse']['id'];
				}
			}

			if (empty($registered_published_course_ids)) {
				return 3;
			}

			$new_section_published_course = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.semester' => $latest_semester_academic_year_published_course['PublishedCourse']['semester'], 
					'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year'], 
					'PublishedCourse.section_id' => $selected_section_move_id
				),
				'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
				'recursive' => -1
			));

			// if the new section has not already published courses, and student has already registered for latest published course in his section, don't allow move
			if (empty($new_section_published_course) && $registration_count_with_latest_publish > 0) {
				// dont allow
				$this->invalidate('move_not_allowed', '' . $student_full_name . ' will not be moved to ' . $new_section_name . ' section. S/he has already registred for her/his course of the most recent academic year and semester in the current section.');
				return false;
			}

			// if the new section published course is not empty and students has not registered for his section published course allow move as long it has same curriculum.
			if (!empty($new_section_published_course) && $registration_count_with_latest_publish == 0) {
				return true;
			} else if (!empty($new_section_published_course) && $registration_count_with_latest_publish > 0) {
				// allow but check if the courses he/she registred is same, if different dont allow.
				$own_section_published_course = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.semester' => $latest_semester_academic_year_published_course['PublishedCourse']['semester'], 
						'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year'], 
						'PublishedCourse.section_id' => $orginal_section_id
					),
					'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'), 
					'recursive' => -1
				));

				$isEveryCourseBelongs = true;

				$publish_count_same = 0;

				if (!empty($own_section_published_course)) {
					foreach ($own_section_published_course as $own_published => $own_publishe_value) {
						foreach ($new_section_published_course as $other_published => $other_published_value) {
							if ($other_published_value['PublishedCourse']['course_id'] == $own_publishe_value['PublishedCourse']['course_id']) {
								// $isEveryCourseBelongs = false;
								// break 2;
								$new_section_publish_courses_ids[$own_publishe_value['PublishedCourse']['id']] = $other_published_value['PublishedCourse']['id'];
								$publish_count_same++;
								break 1;
							}
						}

						if ($publish_count_same == 0) {
							$isEveryCourseBelongs = false;
							break 1;
						}

						$publish_count_same = 0;
					}
				}

				if ($isEveryCourseBelongs) {
					//update registration table with the new published course id but check if in case grade is submitted while moving

					$list_of_registered_courses_ids = array();

					$getListOfRegistredCourses = $this->Student->CourseRegistration->find('all', array(
						'conditions' => array(
							'CourseRegistration.student_id' => $student_id, 
							'CourseRegistration.published_course_id' => $previous_section_published_course_ids
						),
						'fields' => array('id', 'student_id', 'published_course_id'),
						'recursive' => -1, 
					));

					$prepartedForUpdate = array();
					$counter = 0;
					$gradeSubmittedRegistrationIds = array();

					if (!empty($getListOfRegistredCourses)) {
						foreach ($getListOfRegistredCourses as $gk => $gc) {
							if (in_array($gc['CourseRegistration']['published_course_id'], $previous_section_published_course_ids)) {
								$prepartedForUpdate['CourseRegistration'][$counter]['id'] = $gc['CourseRegistration']['id'];
								$prepartedForUpdate['CourseRegistration'][$counter]['published_course_id'] = $new_section_publish_courses_ids[$gc['CourseRegistration']['published_course_id']];
								$prepartedForUpdate['CourseRegistration'][$counter]['section_id'] = $selected_section_move_id;
								$gradeSubmittedRegistrationIds[] = $gc['CourseRegistration']['id'];
								$counter++;
							}
						}
					}

					$check_grade_not_submitted = $this->Student->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $gradeSubmittedRegistrationIds)));

					if ($check_grade_not_submitted > 0 && false) {
						// dont allow to to move
						$this->invalidate('move_not_allowed', '' . $student_full_name . ' will not be moved to ' . $new_section_name . ' section. One or more grade for the semester for his/her section is already submitted.');
						return false;
					} else {
						// allow move and update course registration.
						return $prepartedForUpdate;

						//update course registration table if everthing is okay.
						/* if ($this->Section->Student->CourseRegistration->SaveAll($prepartedForUpdate)) {
				            // successful      
				        } else {
				        	// something went wrong    
				        } */
					}
				} else {
					// dont allow
					$this->invalidate('move_not_allowed', '' . $student_full_name . ' will not be  move to ' . $new_section_name . ' section. The targeted section has different courses published for the semester.');
					return false;
				}
			}
		} else {
			return true;
		}
		return true;
	}

	function isSectionMoveAllowed($orginal_section_id = null, $student_id = null, $selected_section_move_id = null)
	{
		$successMoves = array();
		$unsuccessMoves = array();
		$saveSection = false;

		if (!empty($student_id)) {
			foreach ($student_id as $K => $V) {

				$new_section_detail = $this->find('first', array('conditions' => array('Section.id' => $selected_section_move_id), 'contain' => array('YearLevel')));
				$studentYearLevel = $this->Student->CourseRegistration->studentYearAndSemesterLevelByRegistration($V);

				if (!empty($studentYearLevel['year']) && $studentYearLevel['year'] != $new_section_detail['YearLevel']['name'] && 0) {
					/* $this->invalidate('move_not_allowed','The selected student will not able to move to section '.$new_section_detail['Section']['name'].'. Because the target section is '.$new_section_detail['YearLevel']['name'].' year while the student is '.$studentYearLevel['year'].' year.');
						return false; */
					$unsuccessMoves[] = $V;
				} else {
					$successMoves[] = $V;
				}
			}
		}

		$latest_semester_academic_year_published_course = $this->PublishedCourse->find('first', array(
			'conditions' => array(
				'PublishedCourse.section_id' => $orginal_section_id
			),
			'fields' => array(
				"MAX(PublishedCourse.created)",
				'PublishedCourse.id', 
				'PublishedCourse.course_id', 
				'PublishedCourse.academic_year',
				'PublishedCourse.semester', 
				'PublishedCourse.section_id'
			),
			'group' => 'PublishedCourse.semester', 
			'order' => "MAX(PublishedCourse.created) desc", 
			'recursive' => -1
		));

		if (!empty($latest_semester_academic_year_published_course['PublishedCourse']['academic_year']) && !empty($latest_semester_academic_year_published_course['PublishedCourse']['semester'])) {

			$count = 0;
			$registration_count_with_latest_publish = 0;
			$registered_courses_ids = array();
			$previous_section_published_course_ids = array();
			$new_section_publish_courses_ids = array();
			//get list published courses for a particular students

			$list_published_courses_students_registered = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.section_id' => $orginal_section_id,
					//'PublishedCourse.semester'=> $latest_semester_academic_year_published_course['PublishedCourse']['semester'],
					'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year']
				),
				'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id', 'PublishedCourse.academic_year', 'PublishedCourse.semester', 'PublishedCourse.section_id'),
				'contain' => array('CourseRegistration' => array(
					'conditions' => array(
						'CourseRegistration.student_id' => $successMoves
					),
					'fields' => array(
						'CourseRegistration.student_id',
						'CourseRegistration.id',
						'CourseRegistration.published_course_id'
					)
				))
			));

			// if there is not published courses 
			if (!empty($list_published_courses_students_registered)) {
				foreach ($list_published_courses_students_registered as $list_published => $registered) {
					if (!empty($registered['CourseRegistration']) && count($registered['CourseRegistration']) > 0) {
						$registration_count_with_latest_publish++;
						$registered_courses_ids[] = $registered['CourseRegistration'][0]['id'];
						$registered_published_course_ids['CourseRegistration'][$count]['id'] = $registered['CourseRegistration'][0]['id'];
					}
					$previous_section_published_course_ids[] = $registered['PublishedCourse']['id'];
				}
			}

			if (empty($registered_published_course_ids)) {
				$saveSection = $this->saveSectionMove($successMoves, $orginal_section_id, $selected_section_move_id);
				//return true;
			} else {

				$new_section_published_course = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						//'PublishedCourse.semester'=>$latest_semester_academic_year_published_course['PublishedCourse']['semester'],
						'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year'], 
						'PublishedCourse.section_id' => $selected_section_move_id
					),
					'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
					'recursive' => -1
				));

				// if the new section has not already published courses, and student has already registered for latest published course in his section, don't allow move
				if (empty($new_section_published_course) && $registration_count_with_latest_publish > 0) {
					// dont allow
					$this->invalidate('move_not_allowed', 'The selected student will not be moveed to  ' . $new_section_detail['Section']['name'] . ' section. The targeted section has not published semester course but s/he has already registred for his/her course the section targeted for move is not registered for the courses for the given academic year and semester.');
					return false;
				}

				// if the new section published course is not empty and students has not registered  for his section published course allow move as long it has same curriculum.
				if (!empty($new_section_published_course) && $registration_count_with_latest_publish == 0) {
					$saveSection = $this->saveSectionMove($successMoves, $orginal_section_id, $selected_section_move_id);
				} else if (!empty($new_section_published_course) && $registration_count_with_latest_publish > 0) {
					// allow but check if the courses he/she registred is same if different dont allow.

					$own_section_published_course = $this->PublishedCourse->find('all', array(
						'conditions' => array(
							//'PublishedCourse.semester' => $latest_semester_academic_year_published_course['PublishedCourse']['semester'],
							'PublishedCourse.academic_year' => $latest_semester_academic_year_published_course['PublishedCourse']['academic_year'], 
							'PublishedCourse.section_id' => $orginal_section_id
						),
						'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'), 
						'recursive' => -1
					));

					$isEveryCourseBelongs = true;
					$publish_count_same = 0;

					if (!empty($own_section_published_course)) {
						foreach ($own_section_published_course as $own_published => $own_publishe_value) {
							foreach ($new_section_published_course as
								$other_published => $other_published_value) {
								if ($other_published_value['PublishedCourse']['course_id'] == $own_publishe_value['PublishedCourse']['course_id']) {
									$new_section_publish_courses_ids[$own_publishe_value['PublishedCourse']['id']] = $other_published_value['PublishedCourse']['id'];
									$publish_count_same++;
									break 1;
								}
							}

							if ($publish_count_same == 0) {
								$isEveryCourseBelongs = false;
								break 1;
							}
							$publish_count_same = 0;
						}
					}

					if ($isEveryCourseBelongs) {
						//update registration table with the new published course id but
						//check if in case grade is submitted while moving

						$list_of_registered_courses_ids = array();

						$getListOfRegistredCourses = $this->Student->CourseRegistration->find('all', array(
							'conditions' => array('CourseRegistration.student_id' => $successMoves, 
								'CourseRegistration.published_course_id' => $previous_section_published_course_ids
							),
							'recursive' => -1, 
							'fields' => array('id', 'student_id', 'published_course_id')
						));

						$prepartedForUpdate = array();
						$counter = 0;
						$gradeSubmittedRegistrationIds = array();
						$gradeSubmittedUpdate = array();

						if (!empty($getListOfRegistredCourses)) {
							foreach ($getListOfRegistredCourses as $gk => $gc) {
								if (in_array($gc['CourseRegistration']['published_course_id'], $previous_section_published_course_ids)) {
									if (isset($gc['CourseRegistration']['id']) && !empty($gc['CourseRegistration']['id']) && isset($gc['CourseRegistration']['published_course_id']) && !empty($gc['CourseRegistration']['published_course_id'])) {
										$prepartedForUpdate['CourseRegistration'][$counter]['id'] = $gc['CourseRegistration']['id'];
										$prepartedForUpdate['CourseRegistration'][$counter]['published_course_id'] = $new_section_publish_courses_ids[$gc['CourseRegistration']['published_course_id']];
										$prepartedForUpdate['CourseRegistration'][$counter]['section_id'] = $selected_section_move_id;
										$gradeSubmittedRegistrationIds[] = $gc['CourseRegistration']['id'];
									}
									$counter++;
								}
							}
						}


						$check_grade_not_submitted = $this->Student->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $gradeSubmittedRegistrationIds)));

						if ($check_grade_not_submitted > 0 && false) {
							// dont allow to to move
							$this->invalidate('move_not_allowed', 'The selected student will not be moved to ' . $new_section_detail['Section']['name'] . ' section. One or more grade for semester for his/her section  has already submitted.');
							return false;
						} else {
							// allow move and update course registration.
							$saveSection = $this->saveSectionMove($successMoves, $orginal_section_id, $selected_section_move_id, $prepartedForUpdate);
						}
					} else {
						// dont allow
						$this->invalidate('move_not_allowed', 'The selected student will not be moved to ' . $new_section_detail['Section']['name'] . ' section. The targeted section has different courses.');
						return false;
					}
				}
			}
		} else {
			// perform saving and return true
			$saveSection = $this->saveSectionMove($successMoves, $orginal_section_id, $selected_section_move_id);
			// return true;
		}

		if (!empty($unsuccessMoves)) {
			if (!empty($successMoves)) {
				$this->invalidate('move_not_allowed', '' . count($unsuccessMoves) . ' selected student will not be moved to ' . $new_section_detail['Section']['name'] . ' section. The target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year. But ' . count($successMoves) . ' has moved to selected section successfully.');
				return $saveSection;
			} else if (!empty($unsuccessMoves)) {
				$this->invalidate('move_not_allowed', '' . count($unsuccessMoves) . ' selected student will not be moved to ' . $new_section_detail['Section']['name'] . ' section. The target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year.');
				return $saveSection;
			}
		}
		
		return $saveSection;
	}


	function isSectionMoveAllowedM($orginal_section_id = null, $student_id = null, $selected_section_move_id = null)
	{
		$successMoves = array();
		$unsuccessMoves = array();
		$saveSection = false;
		debug($student_id);

		if (!empty($student_id)) {
			foreach ($student_id as $K => $V) {
				$new_section_detail = $this->find('first', array('conditions' => array('Section.id' => $selected_section_move_id), 'contain' => array('YearLevel')));
				$studentYearLevel = $this->Student->CourseRegistration->studentYearAndSemesterLevelByRegistration($V);

				if (!empty($studentYearLevel['year']) && $studentYearLevel['year'] != $new_section_detail['YearLevel']['name'] && 0) {
					/*
					$this->invalidate('move_not_allowed','The selected student will not able to move to section '.$new_section_detail['Section']['name'].'. Because the target section is '.$new_section_detail['YearLevel']['name'].' year while the student is '.$studentYearLevel['year'].' year.');
					return false;
					*/
					$unsuccessMoves[] = $V;
				} else {
					$successMoves[] = $V;
				}

				$count = ClassRegistry::init('StudentsSection')->find('count', array(
					'conditions' => array(
						'StudentsSection.section_id' => $selected_section_move_id,
						'StudentsSection.student_id' => $V,
					),
					'group' => array(
						'StudentsSection.section_id',
						'StudentsSection.student_id'
					)
				));

				if ($count > 0) {
					unset($student_id[$K]);
				}
			}
		}
		debug($successMoves);

		$orginal_section_published_course = $this->PublishedCourse->find('list', array(
			'conditions' => array(
				'PublishedCourse.section_id' => $orginal_section_id
			),
			'fields' => array(
				'PublishedCourse.id',
				'PublishedCourse.course_id'
			)
		));

		debug($orginal_section_published_course);

		$target_section_published_course = $this->PublishedCourse->find('list', array(
			'conditions' => array('PublishedCourse.section_id' => $selected_section_move_id),
			'fields' => array('PublishedCourse.course_id', 'PublishedCourse.id')
		));

		debug($target_section_published_course);
		debug($successMoves);
		debug($orginal_section_published_course);

		$getListOfRegistredCourses = array();

		if (!empty($successMoves)) {
			$getListOfRegistredCourses = $this->CourseRegistration->find('all', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $successMoves,
					'CourseRegistration.published_course_id' => array_keys($orginal_section_published_course)
				), 
				'contain' => array('ExamGrade'), 
				'fields' => array('id', 'student_id', 'published_course_id', 'section_id')
			));
		}

		$prepartedForUpdate = array();
		$counter = 0;
		//debug($getListOfRegistredCourses);

		if (!empty($getListOfRegistredCourses)) {
			foreach ($getListOfRegistredCourses as $gk => $gc) {
				if (isset($gc['CourseRegistration']['published_course_id']) && !empty($gc['CourseRegistration']['published_course_id']) && empty($gc['ExamGrade'])) {
					$courseId = $orginal_section_published_course[$gc['CourseRegistration']['published_course_id']];

					//does that course in target section
					if (!empty($target_section_published_course[$courseId])) {
						$prepartedForUpdate['CourseRegistration'][$counter]['id'] = $gc['CourseRegistration']['id'];
						$prepartedForUpdate['CourseRegistration'][$counter]['published_course_id'] = $target_section_published_course[$courseId];
						$prepartedForUpdate['CourseRegistration'][$counter]['section_id'] = $selected_section_move_id;
						$counter++;
					} else {
						debug($courseId);
					}
				}
			}
		}
		debug($prepartedForUpdate);

		if (!empty($successMoves) && !empty($orginal_section_id)) {
			$saveSection = $this->saveSectionMove($successMoves, $orginal_section_id, $selected_section_move_id, $prepartedForUpdate);
		}

		if (!empty($unsuccessMoves)) {
			if (!empty($successMoves)) {
				$this->invalidate('move_not_allowed', '' . count($unsuccessMoves) . ' selected student will not be moved to ' . $new_section_detail['Section']['name'] . ' section. The target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year. But ' . count($successMoves) . ' has moved to selected section successfully.');
				return $saveSection;
			} else if (!empty($unsuccessMoves)) {
				$this->invalidate('move_not_allowed', '' . count($unsuccessMoves) . ' selected student will not be move to ' . $new_section_detail['Section']['name'] . ' section. The target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year.');
				return $saveSection;
			}
		}
		return $saveSection;
	}

	function saveSectionMove($studentLists, $orginal_section_id, $selected_section_move_id, $moveRegistration = array())
	{
		$transaction = false;

		if (!empty($moveRegistration)) {
			// synchronize the course registration table with published course
			if (ClassRegistry::init('CourseRegistration')->saveAll($moveRegistration['CourseRegistration'])) {
				$transaction = true;
			} else {
				$this->invalidate('move_not_allowed', 'Synchronization problem, students course regisration is not synchronized with published courses.');
				return false;
			}
		}

		//To check whether the record is already there as archive, if so just turnoff the archive is enough
		$sectionMoveSaveAllFormat = array();
		$count = 0;

		if (!empty($studentLists)) {
			foreach ($studentLists as $k => $v) {
				$already_recorded_id = $this->check_the_record_in_archive($selected_section_move_id, $v);
				if (!empty($already_recorded_id)) {
					$sectionMoveSaveAllFormat['StudentsSection'][$count]['id'] = $already_recorded_id;
					$sectionMoveSaveAllFormat['StudentsSection'][$count]['archive'] = 0;
					$sectionMoveSaveAllFormat['StudentsSection'][$count]['student_id'] = $v;
				} else {
					$sectionMoveSaveAllFormat['StudentsSection'][$count]['section_id'] = $selected_section_move_id;
					$sectionMoveSaveAllFormat['StudentsSection'][$count]['student_id'] = $v;
				}
				$count++;
			}
		}

		if (!empty($sectionMoveSaveAllFormat['StudentsSection'])) {
			if ($this->StudentsSection->saveAll($sectionMoveSaveAllFormat['StudentsSection'], array('validate' => false))) {
				
				$archiveSection = array();
				$permanentDeleteSection = array();
				$counter = 0;

				foreach ($sectionMoveSaveAllFormat['StudentsSection'] as $k => $v) {
					
					if (!ClassRegistry::init('ExamGrade')->isCourseGradeSubmitted($v['student_id'], $orginal_section_id)) {
						$permanentDeleteSection[$counter] = $this->StudentsSection->field('StudentsSection.id',array('StudentsSection.student_id' => $v['student_id'], 'StudentsSection.section_id' => $orginal_section_id, 'StudentsSection.archive' => 0));
					} else {
                        $archiveSection['StudentsSection'][$counter]['id'] = $this->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id' => $v['student_id'], 'StudentsSection.section_id' => $orginal_section_id, 'StudentsSection.archive' => 0));
						$archiveSection['StudentsSection'][$counter]['archive'] = 1;
					}
                   
					//$permanentDeleteSection[$counter] = $this->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id' => $v['student_id'], 'StudentsSection.section_id' => $orginal_section_id, 'StudentsSection.archive' => 0));
					$counter++;
				}

				//archive it
				if (!empty($archiveSection)) {
					$this->StudentsSection->saveAll($archiveSection['StudentsSection'], array('validate' => false));
				}

				//permanently delete it 
				if (!empty($permanentDeleteSection)) {
					$this->StudentsSection->deleteAll(array('StudentsSection.id' => $permanentDeleteSection), false);
				}

				return true;
			}
		}
		return true;
	}

	function check_the_record_in_archive($section_id = null, $student_id = null)
	{
		$studentSection_id = $this->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id' => $student_id, 'StudentsSection.section_id' => $section_id, 'StudentsSection.archive' => 1));
		return $studentSection_id;
	}

	function allDepartmentSectionsOrganizedByProgramAndProgramType($department_id = "", $archive = 0, $include_split = false, $include_merge = false)
	{
		$sections_organized = array();

		if ($department_id != "") {
			$sections_data = $this->find('all', array(
				'conditions' => array(
					'Section.department_id' => $department_id,
					'Section.archive' => $archive
				),
				'contain' => array('Program', 'ProgramType', 'YearLevel')
			));

			if (!empty($sections_data)) {
				foreach ($sections_data as $sd_key => $section_row) {
					$p_found = false;
					$pt_found = false;
					foreach ($sections_organized as $p_id => $section_by_program) {
						if (strcasecmp($section_row['Section']['program_id'], $p_id) == 0) {
							$p_found = true;
							foreach ($section_by_program as $Pt_id => $section_by_program_type) {
								if (strcasecmp($section_row['Section']['program_type_id'], $Pt_id) == 0) {
									$pt_found = true;
									$sections_organized[$p_id][$Pt_id][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
									break 2;
								}
							}
							if ($pt_found == false) {
								$sections_organized[$p_id][$section_row['ProgramType']['name']][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
							}
							break 1;
						}
					}
					if ($p_found == false) {
						$sections_organized[$section_row['Program']['name']][$section_row['ProgramType']['name']][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
					}
				}
			}
		}
		return $sections_organized;
	}


	function allDepartmentSectionsOrganizedByProgramType($col_dep_id = "", $department = 1, $program_id = 1, $archive = 0, $include_split = false, $include_merge = false)
	{
		$sections_organized = array();
		$archiveSection = array();

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$yearsInPast = ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM;
		//$currentAcademicYear = $AcademicYear->academicYearInArray(date('Y') - 2, date('Y'));

		if (!empty($yearsInPast)) {
			$currentAcademicYear = $AcademicYear->academicYearInArray(((explode('/', $AcademicYear->current_academicyear())[0]) - $yearsInPast), (explode('/', $AcademicYear->current_academicyear())[0]));
		} else {
			$currentAcademicYear[] = $AcademicYear->current_academicyear();	
		}

		if (($archive == 0 || $archive == 1)) {
			$archiveSection[$archive] = $archive;
		} else {
			$archiveSection[0] = 0;
			$archiveSection[1] = 1;
		}

		if ($col_dep_id != "" && $program_id != "") {
			if ($department == 1) {
				$sections_data = $this->find('all', array(
					'conditions' => array(
						'Section.department_id' => $col_dep_id,
						'Section.archive' => $archiveSection,
						'Section.program_id' => $program_id,
						'Section.academicyear' => $currentAcademicYear
					),
					'contain' => array('ProgramType', 'YearLevel'),
					'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				));
			} else {
				$sections_data = $this->find('all', array(
					'conditions' => array(
						'Section.college_id' => $col_dep_id,
						'Section.department_id IS NULL',
						'Section.archive' => $archive,
						'Section.program_id' => $program_id
					),
					'contain' => array('ProgramType', 'YearLevel'),
					'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				));
			}

			if (!empty($sections_data)) {
				foreach ($sections_data as $sd_key => $section_row) {
					$pt_found = false;
					foreach ($sections_organized as $Pt_name => $section_by_program_type) {
						if (strcasecmp($section_row['ProgramType']['name'], $Pt_name) == 0) {
							$pt_found = true;
							$sections_organized[$Pt_name][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
							break 1;
						}
					}
					if ($pt_found == false) {
						$sections_organized[$section_row['ProgramType']['name']][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
					}
				}
			}
		}
		//array_unshift($sections_organized, array('' => '--- Select Section ---'));
		return $sections_organized;
	}

	function allDepartmentSectionsOrganizedByProgramTypeSuppExam($col_dep_id = "", $department = 1, $program_id = 1, $archive = 0, $include_split = false, $include_merge = false)
	{
		//$yearsInPast = Configure::read('ExamGradeChange.SuppExam.yearsInPast');
		$yearsInPast = ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM;

		$sections_organized = array();
		$archiveSection = array();

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		//$currentAcademicYear = $AcademicYear->academicYearInArray(date('Y') - $yearsInPast, date('Y'));

		if (!empty($yearsInPast)) {
			$currentAcademicYear = $AcademicYear->academicYearInArray(((explode('/', $AcademicYear->current_academicyear())[0]) - $yearsInPast), (explode('/', $AcademicYear->current_academicyear())[0]));
		} else {
			$currentAcademicYear[] = $AcademicYear->current_academicyear();
		}

		if (($archive == 0 || $archive == 1)) {
			$archiveSection[$archive] = $archive;
		} else {
			$archiveSection[0] = 0;
			$archiveSection[1] = 1;
		}

		if (!empty($col_dep_id) && !empty($program_id)) {
			if ($department == 1) {
				$sections_data = $this->find('all', array(
					'conditions' => array(
						'Section.department_id' => $col_dep_id,
						'Section.archive' => $archiveSection,
						'Section.program_id' => $program_id,
						'Section.academicyear' => $currentAcademicYear
					),
					'contain' => array(
						'ProgramType', 
						'YearLevel', 
						'Student' => array(
							'conditions' => array('Student.graduated' => 0),
							'fields' => array('id')
						)
					),
					'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				));
			} else {
				$sections_data = $this->find('all', array(
					'conditions' => array(
						'Section.college_id' => $col_dep_id,
						'Section.department_id IS NULL',
						'Section.archive' => $archive,
						'Section.program_id' => $program_id
					),
					'contain' => array(
						'ProgramType', 
						'YearLevel', 
						'Student' => array(
							'conditions' => array('Student.graduated' => 0),
							'fields' => array('id')
						)
					),
					'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				));
			}

			if (!empty($sections_data)) {
				foreach ($sections_data as $sd_key => $section_row) {
					$pt_found = false;
					if (!empty($sections_organized)) {
						foreach ($sections_organized as $Pt_name => $section_by_program_type) {
							if (strcasecmp($section_row['ProgramType']['name'], $Pt_name) == 0) {
								$pt_found = true;
								$sections_organized[$Pt_name][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
								break 1;
							}
						}
					}
					if ($pt_found == false && count($section_row['Student'])) {
						$sections_organized[$section_row['ProgramType']['name']][$section_row['Section']['id']] = $section_row['Section']['name'] . ' (' . (!empty($section_row['YearLevel']['name']) ? $section_row['YearLevel']['name'] : ($section_row['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section_row['Section']['academicyear'] . ')';
					}
				}
			}
		}
		//array_unshift($sections_organized, array('' => '--- Select Section ---'));
		return $sections_organized;
	}

	function allStudents($section_id = "")
	{
		$students = $this->find('first', array(
			'conditions' => array(
				'Section.id' => $section_id
			),
			'contain' => array(
				'Student' => array(
					'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.gender', 'Student.studentnumber', 'Student.academicyear', 'Student.graduated', 'Student.full_name'),
					'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
				)
			)
		));

		$student_list = array();

		if (isset($students['Student'])) {
			foreach ($students['Student'] as $key => $student) {
				if (!array_key_exists($student['id'], $student_list) && !$student['graduated'] && !ClassRegistry::init('GraduateList')->isGraduated($student['id'])) {
					$student_list[$student['id']] = $student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'] . ' (' . $student['studentnumber'] . ')';
				}
			}
		}
		//debug($student_list);
		//array_unshift($student_list, array('' => '--- Select Student ---'));
		return $student_list;
	}

	function getAllActiveStudents($section_id = "")
	{
		$students = $this->find('first', array(
			'conditions' => array(
				'Section.id' => $section_id,
				'Section.archive' => 0
			),
			'contain' => array(
				'Student' => array(
					'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.gender', 'Student.studentnumber', 'Student.academicyear', 'Student.graduated', 'Student.curriculum_id', 'Student.full_name'),
					'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
				)
			)
		));

		return $students;
	}
	
	function getSectionActiveStudents($section_id = null)
	{
		if (isset($section_id) && !empty($section_id)) {
			$students = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $section_id, 
					'StudentsSection.archive' => 0
				),
				'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
			));
			return $students;
		}
		return array();
	}

	function getSectionActiveStudentsRegistered($section_id = null)
	{
		if (isset($section_id) && !empty($section_id)) {

			$students = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $section_id, 
					'StudentsSection.archive' => 0
				),
				'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
			));
			

			if (!empty($students)) {

				$registered_student_ids = array();

				foreach ($students as $key => $value) {

					$check_registered = $this->Student->CourseRegistration->find('count', array(
						'conditions' => array(
							'CourseRegistration.student_id' => $value['StudentsSection']['student_id'],
							'CourseRegistration.section_id' => $section_id,
						)
					));

					if ($check_registered) {

						$registered_ids = $this->Student->CourseRegistration->find('list', array(
							'conditions' => array(
								'CourseRegistration.student_id' => $value['StudentsSection']['student_id'],
								'CourseRegistration.section_id' => $section_id,
							),
							'fields' => array('CourseRegistration.id', 'CourseRegistration.id')
						));

						//debug($registered_ids);
						//debug(count($registered_ids));

						$dropped_course_count = $this->Student->CourseRegistration->CourseDrop->find('count', array(
							'conditions' => array(
								'CourseDrop.student_id' => $value['StudentsSection']['student_id'],
								'CourseDrop.course_registration_id' => $registered_ids,
								'CourseDrop.registrar_confirmation' => 1,
							)
						));

						//debug($dropped_course_count);

						if ($dropped_course_count != count($registered_ids)) {
							$registered_student_ids[] = $value['StudentsSection']['student_id'];
						}
						
					}
				}

				if (!empty($registered_student_ids)) {

					$students = $this->StudentsSection->find('all', array(
						'conditions' => array(
							'StudentsSection.student_id' => $registered_student_ids,
							'StudentsSection.section_id' => $section_id, 
							'StudentsSection.archive' => 0
						),
						'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
					));

					return $students;
				} 

				return array();

			}

			return array();
		}

		return array();
	}

	function getSectionActiveStudentsId($section_id = null, $acadamic_year = '')
	{
		if (empty($acadamic_year)) {
			if (isset($section_id) && !empty($section_id)) {
				$students = $this->StudentsSection->find('list', array(
					'conditions' => array(
						'StudentsSection.section_id' => $section_id,
						'StudentsSection.archive' => 0
					),
					'fields' => array('StudentsSection.student_id'),
					'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
				));
				return $students;
			}
		} else {
			if (isset($section_id) && !empty($section_id)) {
				$students = $this->StudentsSection->find('list', array(
					'conditions' => array(
						'StudentsSection.section_id' => $section_id,
						'StudentsSection.archive' => 0,
						//'Section.academicyear LIKE ' => $acadamic_year,
					),
					//'contain' => array('Section'),
					'fields' => array('StudentsSection.student_id'),
					'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
				));
				return $students;
			}

		}

		return array();
	}

	function get_sectionless_students_last_sections($sectionlessStudents_ids = null)
	{
		$sectionlessStudents_ids_array = array();
		$sectionlessStudentsSection_ids = array();
		
		if (!empty($sectionlessStudents_ids)) {
			foreach ($sectionlessStudents_ids as $ssik => $ssiv) {
				$sectionlessStudents_ids_array[$ssiv['StudentsSection']['student_id']] = $ssiv['StudentsSection']['student_id'];
				$sectionlessStudentsSection_ids[$ssiv['StudentsSection']['student_id']] = $this->StudentsSection->find('first', array('fields' => array('StudentsSection.section_id'), 'conditions' => array('StudentsSection.student_id' => $ssiv['StudentsSection']['student_id']), 'order' => array('StudentsSection.modified DESC')));
			}
		}

		$sectionlessStudentsSection_details = array();

		if (!empty($sectionlessStudentsSection_ids)) {
			foreach ($sectionlessStudentsSection_ids as $sssik => $sssiv) {
				$sectionlessStudentsSection_details[$sssik] = $this->find('first', array(
					'conditions' => array(
						'Section.id' => $sssiv['StudentsSection']['section_id']
					),
					'fields' => array(
						'Section.id',
						'Section.name',
						'Section.year_level_id',
						'Section.academicyear'
					),
					'contain' => array(
						'YearLevel' => array(
							'fields' => array(
								'YearLevel.name'
							)
						),
						'Student' => array(
							'fields' => array(
								'Student.id',
								'Student.studentnumber',
								'Student.full_name',
								'Student.academicyear',
								'Student.gender',
								'Student.graduated'
							), 
							'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
							'conditions' => array('Student.id' => $sssik)
						)
					)
				));
			}
		}
		return $sectionlessStudentsSection_details;
	}

	function getSectionByExamGradeId($exam_grade_id = null)
	{
		$exam_grade_detail = $this->PublishedCourse->CourseRegistration->ExamGrade->find('first', array(
			'conditions' => array(
				'ExamGrade.id' => $exam_grade_id
			),
			'contain' => array(
				'CourseAdd' => array(
					'PublishedCourse' => array('Section')
				),
				'CourseRegistration' => array(
					'PublishedCourse' => array('Section')
				)
			)
		));

		$course = null;

		if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']['Section']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse']['Section'])) {
			$course = $exam_grade_detail['CourseRegistration']['PublishedCourse']['Section'];
		} else if (isset($exam_grade_detail['CourseAdd']['PublishedCourse']['Section']) && !empty($exam_grade_detail['CourseAdd']['PublishedCourse']['Section'])) {
			$course = $exam_grade_detail['CourseAdd']['PublishedCourse']['Section'];
		}
		
		return $course;
	}

	function getSectionStudents($section_id = null, $name = null)
	{
		$this->StudentsSection->bindModel(array('belongsTo' => array('Student' => array('className' => 'Student'))));
		$this->Student->bindModel(array('hasMany' => array('StudentsSection' => array('className' => 'StudentsSection'))));

		if (!empty($name)) {

			$name = '"'. (trim($name)). '%"';

			$students_in_section = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $section_id,
					'StudentsSection.archive' => 0,
					'StudentsSection.student_id in (select id from students where first_name LIKE ' . $name . ' OR middle_name LIKE ' . $name . ' OR last_name LIKE ' . $name . ' OR studentnumber LIKE ' . $name . ' )'
				),
				'contain' => array(
					'Student' => array(
						//'conditions' => array('Student.graduated' => 0),
						'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
					)
				),
				'group' => array('StudentsSection.section_id', 'StudentsSection.student_id')
			));
		} else {
			$students_in_section = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $section_id,
					'StudentsSection.archive' => 0
				),
				'contain' => array(
					'Student' => array(
						//'conditions' => array('Student.graduated' => 0),
						'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
					)
				),
				'group' => array('StudentsSection.section_id', 'StudentsSection.student_id')
			));
		}

		return $students_in_section;
	}

	function getSectionStudentsForStatus($section_id = null, $admission_year = null)
	{
		$this->StudentsSection->bindModel(array('belongsTo' => array('Student' => array('conditions' => array('Student.graduated' => 0), 'className' => 'Student'))));
		$this->Student->bindModel(array('hasMany' => array('StudentsSection' => array('className' => 'StudentsSection'))));

		$students_in_section = array();

		if (!empty($section_id))  {
			if (isset($admission_year) && !empty($admission_year)) {
				$students_in_section = $this->StudentsSection->find('all', array(
					'conditions' => array(
						'StudentsSection.section_id' => $section_id,
						'Student.academicyear LIKE ' => $admission_year . '%',
						'Student.graduated' => 0,
					),
					'contain' => array(
						'Student' => array(
							'fields' => array(
								'Student.id', 
								'Student.studentnumber', 
								'Student.full_name', 
								'Student.gender',
								'Student.graduated',
								'Student.curriculum_id',
								'Student.department_id',
								'Student.college_id',
								'Student.program_id',
								'Student.program_type_id',
								'Student.academicyear',
							),
							'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
							'StudentExamStatus' => array(
								'fields' => array('StudentExamStatus.academic_year', 'StudentExamStatus.semester', 'StudentExamStatus.modified'),
								'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC'),
								'limit' => 1
							)
						)
					),
					'group' => array('StudentsSection.section_id', 'StudentsSection.student_id')
				));
			} else {
				$students_in_section = $this->StudentsSection->find('all', array(
					'conditions' => array(
						'StudentsSection.section_id' => $section_id,
						'Student.graduated' => 0,
					),
					'contain' => array(
						'Student' => array(
							'fields' => array(
								'Student.id', 
								'Student.studentnumber', 
								'Student.full_name', 
								'Student.gender',
								'Student.graduated',
								'Student.curriculum_id',
								'Student.department_id',
								'Student.college_id',
								'Student.program_id',
								'Student.program_type_id',
								'Student.academicyear',
							),
							'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
							'StudentExamStatus' => array(
								'fields' => array('StudentExamStatus.academic_year', 'StudentExamStatus.semester', 'StudentExamStatus.modified'),
								'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC'),
								'limit' => 1
							)
						)
					),
					'group' => array('StudentsSection.section_id', 'StudentsSection.student_id')
				));
			}
		}

		return $students_in_section;
	}

	function studentsAlreaydTakenCourse($sectionid = null)
	{
		$previous_sections[] = $sectionid;

		if ($sectionid) {
			$student_section = $this->StudentsSection->find('first', array(
				'conditions' => array(
					'StudentsSection.section_id' => $sectionid
				),
				'contain' => array()
			));

			$student_sections = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $sectionid
				),
				'contain' => array()
			));

			$student_idss = array();

			if (!empty($student_sections)) {
				foreach ($student_sections as $ssindex => $ssvalue) {
					$student_idss[] = $ssvalue['StudentsSection']['student_id'];
				}
			}

			$student_previous_section = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.student_id' => $student_idss, 
					'StudentsSection.archive' => 1
				), 
				'contain' => array()
			));

			if (!empty($student_previous_section)) {
				foreach ($student_previous_section as $pindex => $pvalue) {
					if ($pvalue['StudentsSection']['section_id'] != $sectionid) {
						$previous_sections[] = $pvalue['StudentsSection']['section_id'];
					}
				}
			}

			$students = $this->find('all', array(
				'conditions' => array(
					'Section.id' => $previous_sections
				),
				'fields' => array(
					'Section.id', 
					'Section.name'
				),
				'contain' => array(
					'Student' => array(
						'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name', 'Student.curriculum_id', 'Student.gender',' Student.academicyear', 'Student.graduated'),
						'conditions' => array('Student.id' => $student_idss),
						'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
						'CourseRegistration' => array(
							'PublishedCourse',
							'ExamGrade' => array('id', 'course_registration_id', 'course_add_id', 'grade'),
							'CourseDrop' => array('id', 'course_registration_id', 'semester', 'student_id', 'academic_year')
						),
						'CourseAdd' => array(
							'fields' => array('id', 'student_id', 'published_course_id'), 
							'PublishedCourse' => array(
								'Course' => array('id')
							)
						)
					)
				)
			));

			return $students;
		}

		return false;
	}

	function get_sections_by_dept($department_id = null)
	{
		$sections = array();

		$sections_detail = $this->find('all', array(
			'conditions' => array(
				'Section.department_id' => $department_id,
				'Section.archive' => 0
			), 
			'contain' => array(
				'Program' => array('id', 'name'), 
				'YearLevel' => array('id', 'name'), 
				'ProgramType' => array('id', 'name')
			), 
			'fields' => array('id', 'name', 'program_id', 'year_level_id')
		));

		if (!empty($sections_detail)) {
			foreach ($sections_detail as $seindex => $secvalue) {
				$sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . $secvalue['YearLevel']['name'] . ')';
			}
		}
		
		return $sections;
	}

	function get_tottal_active_students_of_the_section($section_id = null)
	{
		$count = $this->StudentsSection->find('count', array(
			'conditions' => array(
				'StudentsSection.section_id' => $section_id, 
				'StudentsSection.archive' => 0
			),
			'group' => array('StudentsSection.section_id', 'StudentsSection.student_id')
		));
		return $count;
	}

	function getSectionsPublishedCoursesForExamSchedule($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_ids = null, $department_ids = null, $year_levels = null)
	{
		$year_level_ids = array();
		$sections = array();
		$publishedCourses = array();

		if (!empty($department_ids) && !empty($year_levels)) {
			foreach ($department_ids as $dep_key => $department_id) {
				foreach ($year_levels as $year_level) {
					if ($year_level == 1) {
						$year_level_name = $year_level . 'st';
					} else if ($year_level == 2) {
						$year_level_name = $year_level . 'nd';
					} else if ($year_level == 3) {
						$year_level_name = $year_level . 'rd';
					} else {
						$year_level_name = $year_level . 'th';
					}

					$yearLevel = $this->YearLevel->find('first', array(
						'conditions' => array(
							'YearLevel.name' => $year_level_name,
							'YearLevel.department_id' => $department_id
						),
						'recursive' => -1
					));

					if ((isset($yearLevel['YearLevel']['id']) || strcasecmp($dep_key, 'FP') == 0) && !empty($yearLevel['YearLevel']['id'])) {
						
						$options = array(
							'conditions' => array(
								'Section.academicyear' => $acadamic_year,
								'Section.program_id' => $program_id,
								'Section.program_type_id' => $program_type_ids,
								'Section.year_level_id' => $yearLevel['YearLevel']['id']
							),
							'contain' => array(
								'YearLevel',
								'PublishedCourse' => array(
									'conditions' => array(
										'PublishedCourse.academic_year' => $acadamic_year,
										'PublishedCourse.semester' => $semester,
										'PublishedCourse.id NOT IN (SELECT published_course_id FROM excluded_published_course_exams)',
										'PublishedCourse.id NOT IN (SELECT published_course_id FROM exam_schedules)',
									),
									'CourseExamGapConstraint',
									'CourseExamConstraint',
									'ExamRoomCourseConstraint',
									'Course'
								)
							)
						);

						if (strcasecmp($dep_key, 'FP') == 0) {
							$options['conditions']['Section.college_id'] = $college_id;
						} else {
							$options['conditions']['Section.department_id'] = $department_id;
						}

						$sections_t = $this->find('all', $options);
						//debug($sections_t);

						if (!empty($sections_t)) {
							foreach ($sections_t as $section_key => $section) {
								$examPeriod = $this->Program->ExamPeriod->find('first', array(
									'conditions' => array(
										'ExamPeriod.college_id' => $college_id,
										'ExamPeriod.program_id' => $program_id,
										'ExamPeriod.program_type_id' => $section['Section']['program_type_id'],
										'ExamPeriod.academic_year' => $acadamic_year,
										'ExamPeriod.semester' => $semester,
										'ExamPeriod.year_level_id' => $year_level_name
									),
									'contain' => array(
										'ExamExcludedDateAndSession'
									)
								));
								//debug($section);

								$datetime1 = new DateTime($examPeriod['ExamPeriod']['start_date']);
								$datetime2 = new DateTime($examPeriod['ExamPeriod']['end_date']);
								$interval = $datetime1->diff($datetime2);
								$number_of_exam_days = $interval->d + 1;
								$exlude_date_matrix = array();

								if (isset($examPeriod['ExamExcludedDateAndSession'])) {
									foreach ($examPeriod['ExamExcludedDateAndSession'] as $examExcludedDateAndSession) {
										$index = count($exlude_date_matrix);
										$found = false;
										foreach ($exlude_date_matrix as $k => $v) {
											if (strcasecmp($examExcludedDateAndSession['excluded_date'], $v['date']) == 0) {
												$exlude_date_matrix[$k]['sc']++;
												$found = true;
												if ($exlude_date_matrix[$k]['sc'] == 3) {
													$number_of_exam_days--;
												}
												break;
											}
										}
										if (!$found) {
											$exlude_date_matrix[$index]['date'] = $examExcludedDateAndSession['excluded_date'];
											$exlude_date_matrix[$index]['sc'] = 1;
										}
									}
								}
								//debug($exlude_date_matrix);

								$eStartDate = date("Y-m-d", strtotime($examPeriod['ExamPeriod']['start_date']));
								$eEndDate = date("Y-m-d", strtotime($examPeriod['ExamPeriod']['end_date']));
								$eDays = array();
								$eCurrentDate = $eStartDate;

								while ($eCurrentDate <= $eEndDate) {
									$found = false;
									if (!empty($exlude_date_matrix)) {
										foreach ($exlude_date_matrix as $exlude_date) {
											if ($exlude_date['date'] == $eCurrentDate && $exlude_date['sc'] == 3) {
												$found = true;
												break;
											}
										}
									}
									if (!$found) {
										$eDays[] = $eCurrentDate;
									}
									$eCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($eCurrentDate)));
								}

								foreach ($section['PublishedCourse'] as $pc_key => $pc) {
									//Filter the exams days based on the CourseExamConstraint (eDays)
									//1. Determine avtive is 1 or 0
									$course_exam_constraint_active = 0;
									foreach ($pc['CourseExamConstraint'] as $courseExamConstraint) {
										if ($courseExamConstraint['active'] == 1) {
											$course_exam_constraint_active = 1;
											break;
										}
									}

									//2. If active is 1, use only the specified dates
									if ($course_exam_constraint_active == 1) {
										$eDays = array();
										foreach ($pc['CourseExamConstraint'] as $courseExamConstraint) {
											if ($courseExamConstraint['active'] == 1) {
												$search_key = array_search($courseExamConstraint['exam_date'], $eDays);
												if ($search_key === false) {
													$eDays[] = $courseExamConstraint['exam_date'];
												}
											}
										}
									} else {
										//3. If active is 0, remove the specified dates from eDays
										foreach ($pc['CourseExamConstraint'] as $courseExamConstraint) {
											if ($courseExamConstraint['active'] == 0) {
												$search_key = array_search($courseExamConstraint['exam_date'], $eDays);
												if ($search_key !== false) {
													unset($eDays[$search_key]);
												}
											}
										}
									}

									$index = count($publishedCourses);
									$publishedCourses[$index]['id'] = $pc['id'];
									$publishedCourses[$index]['course_id'] = $pc['Course']['id'];
									$publishedCourses[$index]['number_of_invigilator'] = $examPeriod['ExamPeriod']['default_number_of_invigilator_per_exam'];
									$publishedCourses[$index]['start_date'] = $examPeriod['ExamPeriod']['start_date'];
									$publishedCourses[$index]['end_date'] = $examPeriod['ExamPeriod']['end_date'];
									$publishedCourses[$index]['exam_days'] = $eDays;
									$publishedCourses[$index]['section_id'] = $section['Section']['id'];
									$publishedCourses[$index]['year_level'] = $section['YearLevel']['name'];
									$publishedCourses[$index]['weight'] = 0;
									//$publishedCourses[$index]['number_of_exam_days'] = $number_of_exam_days;
									//$publishedCourses[$index]['number_of_exam'] = count($section['PublishedCourse']);
									$publishedCourses[$index]['section_average_exam_day'] = floor($number_of_exam_days / count($section['PublishedCourse']));
									//Wait by course exam gap constraint
									
									if (!empty($pc['CourseExamGapConstraint'])) {
										$publishedCourses[$index]['weight'] += 30;
										$publishedCourses[$index]['gap'] = $pc['CourseExamGapConstraint']['gap_before_exam'];
									} else {
										$publishedCourses[$index]['gap'] = 0;
									}
									
									//Wait by course exam constraint
									$active = 0;
									$inactive = 0;
									foreach ($pc['CourseExamConstraint'] as $courseExamConstraint) {
										if ($courseExamConstraint['active'] == 1) {
											$active++;
										} else {
											$inactive++;
										}
									}

									if ($active > 0) {
										$publishedCourses[$index]['weight'] += 30 / $active;
									} else {
										$publishedCourses[$index]['weight'] += $inactive / 30;
									}

									//Wait by course exam room constraint
									$active = 0;
									$inactive = 0;
									foreach ($pc['ExamRoomCourseConstraint'] as $examRoomCourseConstraint) {
										if ($examRoomCourseConstraint['active'] == 1) {
											$active++;
										} else {
											$inactive++;
										}
									}

									if ($active > 0) {
										$publishedCourses[$index]['weight'] += 30 / $active;
									} else {
										$publishedCourses[$index]['weight'] += $inactive / 30;
									}
								}
							}
						}
					}
				}
			}
		}

		shuffle($publishedCourses);

		for ($i = 0; $i < count($publishedCourses); $i++) {
			for ($j = $i + 1; $j < count($publishedCourses); $j++) {
				if ($publishedCourses[$j]['weight'] > $publishedCourses[$i]['weight']) {
					$tmp = $publishedCourses[$j];
					$publishedCourses[$j] = $publishedCourses[$i];
					$publishedCourses[$i] = $tmp;
				}
			}
		}

		return $publishedCourses;
	}

	function getPublishedCoursesForExamScheduleBySection($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_ids = null, $department_ids = null, $year_levels = null)
	{
		$year_level_ids = array();
		$sections = array();
		$publishedCourses = array();

		if (!empty($department_ids) && !empty($year_levels)) {
			foreach ($department_ids as $dep_key => $department_id) {
				foreach ($year_levels as $year_level) {
					if ($year_level == 1) {
						$year_level_name = $year_level . 'st';
					} else if ($year_level == 2) {
						$year_level_name = $year_level . 'nd';
					} else if ($year_level == 3) {
						$year_level_name = $year_level . 'rd';
					} else {
						$year_level_name = $year_level . 'th';
					}

					$yearLevel = $this->YearLevel->find('first', array(
						'conditions' => array(
							'YearLevel.name' => $year_level_name,
							'YearLevel.department_id' => $department_id
						),
						'recursive' => -1
					));

					if ((isset($yearLevel['YearLevel']['id']) || strcasecmp($dep_key, 'FP') == 0) && !empty($yearLevel['YearLevel']['id'])) {
						
						$options = array(
							'conditions' => array(
								'Section.academicyear' => $acadamic_year,
								'Section.program_id' => $program_id,
								'Section.program_type_id' => $program_type_ids,
								'Section.year_level_id' => $yearLevel['YearLevel']['id']
							),
							'contain' => array(
								'PublishedCourse' => array(
									'conditions' => array(
										'PublishedCourse.academic_year' => $acadamic_year,
										'PublishedCourse.semester' => $semester,
										'PublishedCourse.id NOT IN (SELECT published_course_id FROM excluded_published_course_exams)',
										'PublishedCourse.id NOT IN (SELECT published_course_id FROM exam_schedules)',
									)
								)
							)
						);

						if (strcasecmp($dep_key, 'FP') == 0) {
							$options['conditions']['Section.college_id'] = $college_id;
						} else {
							$options['conditions']['Section.department_id'] = $department_id;
						}

						$sections_t = $this->find('all', $options);

						if (!empty($sections_t)) {
							foreach ($sections_t as $section_key => $section) {
								foreach ($section['PublishedCourse'] as $pc_key => $pc) {
									$publishedCourses[$section['Section']['id']][] = $pc['id'];
								}
							}
						}
					}
				}
			}
		}

		return $publishedCourses;
	}

	function mergedSectionIds($data = null)
	{
		$ids = array();

		if (!empty($data)) {
			foreach ($data['Section']['Sections'] as $in => $sec_index) {
				$ids[] = $data['Section'][$sec_index]['id'];
			}
		}

		return $ids;
	}

	public function dropOutWithDrawAfterLastRegistrationNotReadmittedExcludeFromSectionless($student_id = null, $current_academicyear = null) 
	{
		$check_dropout = 0;

		$last_registration_date = $this->Student->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id
			), 
			'order' => array('CourseRegistration.created DESC'), 
			'recursive' => -1
		));

		if (!empty($last_registration_date['CourseRegistration'])) {
			$check_dropout = ClassRegistry::init('DropOut')->find('count', array(
				'conditions' => array(
					'DropOut.student_id' => $student_id,
					'DropOut.drop_date >= ' => $last_registration_date['CourseRegistration']['created']
				)
			));
		}

		if ($check_dropout > 0) {
			// exclude from adding to section 
			return 1;
		} else {
			// Has withdraw confirmed and not readmitted exclude from sectionless 
			$check_withdraw = 0;
			if (!empty($last_registration_date)) {
				$check_withdraw = ClassRegistry::init('Clearance')->find('count', array(
					'conditions' => array(
						'Clearance.student_id' => $student_id,
						'Clearance.type' => 'withdraw',
						'Clearance.confirmed' => 1,
						'Clearance.forced_withdrawal' => 1,
						'Clearance.request_date >= ' => $last_registration_date['CourseRegistration']['created']
					)
				));
			}

			// withdraw valid
			if ($check_withdraw > 0) {
				$check_if_readmitted = ClassRegistry::init('Readmission')->is_readmitted($student_id, $current_academicyear);
				//1 exclude 
				if (!$check_if_readmitted) {
					return 1;
				} else {
					//visible as sectionless
					return 2;
				}
			}

			$check_dismissal = 0;
			$check_dismissal_status = 0;
			$check_no_status_generated = 0;
			$check_no_status = 0;

			// is dismissed and 
			if (!empty($last_registration_date['CourseRegistration'])) {
				$check_dismissal = ClassRegistry::init('Dismissal')->find('count', array(
					'conditions' => array(
						'Dismissal.student_id' => $student_id,
						'Dismissal.dismisal_date >= ' => $last_registration_date['CourseRegistration']['created']
					)
				));

				$check_dismissal_status = ClassRegistry::init('StudentExamStatus')->find('count', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student_id,
						'StudentExamStatus.academic_year' => $last_registration_date['CourseRegistration']['academic_year'],
						'StudentExamStatus.semester' => $last_registration_date['CourseRegistration']['semester'],
						'StudentExamStatus.academic_status_id' => 4,

					)
				));

				$check_no_status_generated = ClassRegistry::init('StudentExamStatus')->find('count', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student_id,
						'StudentExamStatus.academic_year' => $last_registration_date['CourseRegistration']['academic_year'],
						'StudentExamStatus.semester' => $last_registration_date['CourseRegistration']['semester'],
					)
				));

				$check_no_status = ClassRegistry::init('StudentExamStatus')->find('count', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student_id,
						'StudentExamStatus.academic_year' => $last_registration_date['CourseRegistration']['academic_year'],
						'StudentExamStatus.semester' => $last_registration_date['CourseRegistration']['semester'],
						'OR' => array(
							'StudentExamStatus.academic_status_id IS NULL',
							'StudentExamStatus.academic_status_id = 0',
							'StudentExamStatus.academic_status_id = ""',
						)

					)
				));
			}

			if ($check_dismissal > 0 ) {
				$check_if_readmitted = ClassRegistry::init('Readmission')->is_readmitted($student_id, $current_academicyear);
				//1 exclude 
				if (!$check_if_readmitted) {
					return 1;
				} else {
					//visible as sectionless
					return 2;
				}
			} else if ($check_dismissal_status > 0) {
				
				debug($check_dismissal_status);
				debug($student_id);

				$check_if_readmitted = ClassRegistry::init('Readmission')->is_readmitted($student_id, $current_academicyear);
				//1 exclude 
				if (!$check_if_readmitted) {
					return 1;
				} else {
					//visible as sectionless
					return 2;
				}
			} else if (!$check_no_status_generated) {

				debug($check_no_status_generated);
				debug($student_id);
				return 1;

			} else if ($check_no_status) {

				debug($check_no_status);
				debug($student_id);
				return 1;

			} 
		}

		return 2;
	}

	function getStudentSectionInGivenAcademicYear($academic_year = null, $student_id = null) 
	{
		$options = array();
		$sections = array();

		if (!empty($student_id)) {

			/* $stsec = ClassRegistry::init('StudentsSection')->find('first', array(
				'conditions' => array(
					'StudentsSection.student_id' => $student_id,
					//'StudentsSection.section_id IN (SELECT id FROM sections where academicyear = "' . $academic_year . '" ORDER BY id DESC)'
					'StudentsSection.section_id IN (SELECT section_id FROM course_registrations where academic_year = "' . $academic_year . '" AND student_id = ' . $student_id . ' GROUP BY academic_year, semester, student_id  ORDER BY academic_year DESC, semester DESC, section_id DESC, id DESC)'
				),
				'contain' => array(
					'Section' => array(
						'YearLevel' => array(
							'fields' => array('id', 'name')
						),
						'Department' => array(
							'fields' => array('id', 'name', 'type', 'college_id')
						),
						'College' => array(
							'fields' => array('id', 'name', 'type', 'campus_id', 'stream')
						),
						'Program' => array(
							'fields' => array('id', 'name')
						),
						'ProgramType' => array(
							'fields' => array('id', 'name')
						),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					)
				),
				'group' => array(
					'StudentsSection.student_id',
					'StudentsSection.section_id',
				),
				'order' => array(
					'StudentsSection.section_id' => 'DESC',
					'StudentsSection.id' => 'DESC',
					'StudentsSection.created' => 'DESC',
					//'StudentsSection.archive' => 'ASC',
				)
			));

			debug($stsec);

			if (!empty($stsec['Section']) && !empty($stsec['Section']['id'])) {
				$sections = $stsec;
			} else { */

			$options['conditions']['Section.academicyear'] = $academic_year;
			$options['order'] = array('Section.id' => 'DESC');

			$options['contain'] = array(
				'YearLevel' => array(
					'fields' => array('id', 'name')
				),
				'Department' => array(
					'fields' => array('id', 'name', 'type', 'college_id')
				),
				'College' => array(
					'fields' => array('id', 'name', 'type', 'campus_id', 'stream')
				),
				'Program' => array(
					'fields' => array('id', 'name')
				),
				'ProgramType' => array(
					'fields' => array('id', 'name')
				),
				'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
			);

			$options['conditions'][] = 'Section.id IN (SELECT section_id FROM students_sections where student_id = ' . $student_id . ' and archive = 0 GROUP BY student_id, section_id ORDER BY section_id DESC, id DESC)';

			$sections = $this->find('first', $options);

			if (empty($sections)){
				unset($options['conditions'][0]);
				$options['conditions'][] = 'Section.id IN (SELECT section_id FROM students_sections where student_id = ' . $student_id . ' GROUP BY student_id, section_id ORDER BY section_id DESC, id DESC)';
				$sections = $this->find('first', $options);
			}
			//}
		}
		
		return $sections;
	}

	function getStudentSectionHistory($student_id)
	{
		$options = array();

		$options['conditions'][] = 'Section.id  IN (SELECT section_id FROM students_sections where student_id=' . $student_id . ')';

		$options['contain'] = array(
			'YearLevel' => array(
				'fields' => array('id', 'name')
			),
			'Department' => array(
				'fields' => array('id', 'name', 'type', 'college_id')
			),
			'College' => array(
				'fields' => array('id', 'name', 'type', 'campus_id', 'stream')
			),
			'Program' => array(
				'fields' => array('id', 'name')
			),
			'ProgramType' => array(
				'fields' => array('id', 'name')
			),
			'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
			'StudentsSection' => array(
				'order' => array('StudentsSection.section_id' => 'ASC', 'StudentsSection.created' => 'ASC')
			)
		);

		$options['order'] = array('Section.academicyear' => 'ASC', 'Section.id' => 'ASC', 'Section.year_level_id' => 'ASC', 'Section.name' => 'ASC');

		$sections = $this->find('all', $options);

		if (!empty($sections)) {
			foreach ($sections as $k => &$v) {
				$archived = ClassRegistry::init('StudentsSection')->find('first', array(
					'conditions' => array(
						'StudentsSection.section_id' => $v['Section']['id'],
						'StudentsSection.student_id' => $student_id
					)
				));

				if ($archived['StudentsSection']['archive'] == 1) {
					$v['Section']['archive'] = true;
				}
			}
		}
		return $sections;
	}

	function rearrangeSectionList($academic_year, $department_id, $year_level, $program_id, $program_type_id, $type, $pre = 0) 
	{
		$findPublicationofSection = array();
		
		if ($pre == 1) {
			$options['conditions'][] = 'Student.department_id is null and Student.college_id=' . $department_id . '';
		} else {
			$options['conditions']['Section.department_id'] = $department_id;
		}

		$options['conditions']['Section.academicyear'] = $academic_year;
		$options['conditions']['Section.year_level_id'] = $year_level;
		$options['conditions']['Section.program_id'] = $program_id;
		$options['conditions']['Section.program_type_id'] = $program_type_id;
		$options['conditions']['Section.archive'] = 0;
		$options['fields'] = array('Section.name', 'Section.id');
		$options['order'] = array('Section.academicyear' => 'ASC', 'Section.id' => 'ASC', 'Section.year_level_id' => 'ASC', 'Section.name' => 'ASC');

		$sections = $this->find('list', $options);
		//debug(count($sections));

		if (!empty($sections)) {
			foreach ($sections as $v) {
				$findPublicationofSection = ClassRegistry::init('PublishedCourse')->find('count', array('conditions' => array('PublishedCourse.section_id' => $v)));
				if ($findPublicationofSection) {
					return 3;
				}
			}
		}


		$option1['conditions'][] = 'Student.id  IN (SELECT student_id FROM students_sections where section_id in (' . join(',', $sections) . '))';
		$option1['order'] = array('Student.' . $type);
		$option1['recursive'] = -1;

		$studentLists = ClassRegistry::init('Student')->find('all', $option1);

		$count = 0;
		$studentOrganizedFakeSection = array();
		$sectionCount = 0;

		if (!empty($studentLists)) {
			foreach ($studentLists as $k => $value) {
				$count++;
				if (($count % 50) == 0) {
					$sectionCount++;
					// iterate throuw the new section 
					$studentOrganizedFakeSection[$sectionCount][] = $value['Student']['id'];
				} else {
					$studentOrganizedFakeSection[$sectionCount][] = $value['Student']['id'];
				}
			}
		}

		$lastStop = 0;

		if (!empty($sections)) {
			foreach ($sections as $k => $v) {
				$classSize = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.section_id' => $v, 'StudentsSection.archive' => 0)));
				// perform swap of students in section 
				if (!empty($studentOrganizedFakeSection[$lastStop])) {
					foreach ($studentOrganizedFakeSection[$lastStop] as $sk => $sv) {
						//check the student is in the iteration section 
						$belongToSection = ClassRegistry::init('StudentsSection')->find('count', array(
							'conditions' => array(
								'StudentsSection.section_id' => $v, 
								'StudentsSection.student_id' => $sv,
								'StudentsSection.archive' => 0
							)
						));

						$ownSection = ClassRegistry::init('StudentsSection')->find('first', array(
							'conditions' => array(
								'StudentsSection.archive' => 0,
								'StudentsSection.student_id' => $sv
							),
							'recursive' => -1,
							'order' => array('StudentsSection.created' => 'DESC')
						));

						if (!$belongToSection) {
							//update the student section with the new one 
							if (isset($sv) && !empty($sv) && !empty($ownSection['StudentsSection']['id'])) {
								$newSection = array();
								$newSection['StudentsSection']['id'] = $ownSection['StudentsSection']['id'];
								$newSection['StudentsSection']['student_id'] = $sv;
								$newSection['StudentsSection']['section_id'] = $v;
								if (ClassRegistry::init('StudentsSection')->save($newSection)) {
									//debug($belongToSection);
								}
							}
						}
					}
				}
				$lastStop++;
			}
		}
		// debug($lastStop);
	}


	function swampTheWholeStudentInSpecificBatch($bathacademicYear, $academic_year, $semester, $department_id, $year_level, $program_id, $program_type_id, $type, $pre = 0 ) 
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$owsSection = array();
		
		if ($pre == 1) {
			$options['conditions'][] = 'Student.department_id is null and Student.college_id=' . $department_id . '';
			$owsSection['conditions'][] = 'StudentsSection.section_id in (select id from sections where department_id is null and college_id = ' . $department_id . ' and academicyear = "' . $academic_year . '" and year_level_id = ' . $year_level . ' and program_id = ' . $program_id . ' and program_type_id = ' . $program_type_id . ' )';
		} else {
			$options['conditions']['Section.department_id'] = $department_id;
			$owsSection['conditions'][] = 'StudentsSection.section_id in (select id from sections where department_id = ' . $department_id . ' and academicyear = "' . $academic_year . '" and year_level_id = ' . $year_level . ' and program_id = ' . $program_id . ' and program_type_id = ' . $program_type_id . ' )';
		}

		$options['conditions']['Section.academicyear'] = $academic_year;
		$options['conditions']['Section.year_level_id'] = $year_level;
		$options['conditions']['Section.program_id'] = $program_id;
		$options['conditions']['Section.program_type_id'] = $program_type_id;
		$options['fields'] = array('Section.name', 'Section.id');
		$options['order'] = array('Section.name');
		$sections = $this->find('list', $options);

		if ($pre == 1) {
			$option1['conditions'][] = 'Student.department_id is null and Student.college_id = ' . $department_id . ' and Student.graduated = 0';
		} else {
			//$option1['conditions']['Student.department_id'] = $department_id;
			$option1['conditions'][] = 'Student.department_id = ' . $department_id . ' and Student.graduated = 0';
		}

		$option1['conditions']['Student.program_id'] = $program_id;
		$option1['conditions']['Student.program_type_id'] = $program_type_id;
		$option1['conditions']['Student.admissionyear'] = $AcademicYear->get_academicYearBegainingDate($bathacademicYear);
		$option1['order'] = array('Student.' . $type);
		$option1['recursive'] = -1;

		$studentLists = ClassRegistry::init('Student')->find('all', $option1);

		$count = 0;
		$studentOrganizedFakeSection = array();
		$sectionCount = 0;

		if (!empty($studentLists)) {
			foreach ($studentLists as $k => $value) {
				$count++;
				if (($count % 50) == 0) {
					$sectionCount++;
					// iterate throuw the new section 
					$studentOrganizedFakeSection[$sectionCount][] = $value['Student']['id'];
				} else {
					$studentOrganizedFakeSection[$sectionCount][] = $value['Student']['id'];
				}
			}
		}

		$lastStop = 0;

		if (!empty($sections)) {
			foreach ($sections as $k => $v) {
				$classSize = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.section_id' => $v)));
				
				$sectionCoursePublication = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.section_id' => $v,
						'PublishedCourse.semester' => $semester
					), 
					'recursive' => -1
				));

				$courseRegistrationUpdate['CourseRegistration'] = array();
				$studentSectionUpdate['StudentsSection'] = array();
				// perform swap of students in section 
				//for($i=0;$i<50;$i++) {
				//check the student is in the iteration section 

				if (!empty($studentOrganizedFakeSection[$lastStop])) {
					foreach ($studentOrganizedFakeSection[$lastStop] as $sk => $sv) {
						$belongToSection = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.section_id' => $v, 'StudentsSection.student_id' => $sv)));
						if (!$belongToSection) {
							//update the student section with the new one
							//move the first 50 students to first section 
							// find the whole section student attended
							// find the publication course of that section
							// find the target section publication courses
							// if the courses in both section is similar, update course registration with the new section movement, and update students_sections with the new section if different leave that student in the previous section 

							$owsSection['conditions']['StudentsSection.student_id'] = $sv;
							$owsSection['recursive'] = -1;

							$ownSection = ClassRegistry::init('StudentsSection')->find('first', $owsSection);

							if (isset($sv) && !empty($sv) && !empty($ownSection)) {

								$ownSectionPublishedCourse = $this->PublishedCourse->find('all', array('conditions' => array('PublishedCourse.section_id' => $ownSection['StudentsSection']['section_id'], 'PublishedCourse.semester' => $semester), 'recursive' => -1));
								// allow movement if the target section and own section has similar course id and number of publication is same
								
								if (count($ownSectionPublishedCourse) == count($sectionCoursePublication)) {
									// is the course similar
									if ($this->similarCourseInSection($ownSectionPublishedCourse, $sectionCoursePublication)) {
										// update course registration table with the new section published course id and section id 
										$courseRegistrationUpdate['CourseRegistration'] = array_merge($courseRegistrationUpdate['CourseRegistration'], $this->publicationMappingWithCourseRegistration($ownSectionPublishedCourse, $v, $ownSection['StudentsSection']['student_id']));
										// student section ready for update 
										$newSection = array();
										$oldSection = array();
										// $newSection['id'] = $ownSection['StudentsSection']['id'];
										$newSection['student_id'] = $sv;
										$newSection['section_id'] = $v;
										$oldSection['id'] = $ownSection['StudentsSection']['id'];
										$oldSection['archive'] = 1;
										$studentSectionUpdate['StudentsSection'][] = $newSection;
										$studentSectionUpdate['StudentsSection'][] = $oldSection;
									}
								} else if (empty($ownSectionPublishedCourse) && empty($sectionCoursePublication)) {
									// do simple move 
									$newSection = array();
									$newSection['id'] = $ownSection['StudentsSection']['id'];
									$newSection['student_id'] = $sv;
									$newSection['section_id'] = $v;
									$studentSectionUpdate['StudentsSection'][] = $newSection;
								}
							}
						}
					}
				}

				if (!empty($studentSectionUpdate['StudentsSection'])) {
					if (ClassRegistry::init('StudentsSection')->saveAll($studentSectionUpdate['StudentsSection'], array('validate' => false))) {
					}
				}

				if (!empty($courseRegistrationUpdate['CourseRegistration'])) {
					if (ClassRegistry::init('CourseRegistration')->saveAll($courseRegistrationUpdate['CourseRegistration'], array('validate' => false))) {
					}
				}

				$lastStop++;
			}
		}
		// debug($lastStop);
	}


	function swampStudentSection($student_detail, $previouse_section_id, $target_section_id)
	{
		$owsSection['conditions']['StudentsSection.student_id'] = $student_detail['Student']['id'];
		$owsSection['recursive'] = -1;
		
		$ownSection = ClassRegistry::init('StudentsSection')->find('first', $owsSection);

		$previousSection = $this->find('first', array(
			'conditions' => array(
				'Section.id' => $previouse_section_id,
				'Section.archive' => 0
			),
			'contain' => array('YearLevel', 'Department', 'Program', 'ProgramType')
		));

		$targetSection = $this->find('first', array(
			'conditions' => array(
				'Section.id' => $target_section_id,
				'Section.archive' => 0
			),
			'contain' => array('YearLevel', 'Department', 'Program', 'ProgramType')
		));

		$previousSectionPublishedCourse = $this->PublishedCourse->find('all', array(
			'conditions' => array(
				'PublishedCourse.section_id' => $previousSection['Section']['id']
			), 
			'recursive' => -1
		));

		$targetSectionPublishedCourse = $this->PublishedCourse->find('all', array(
			'conditions' => array(
				'PublishedCourse.section_id' => $targetSection['Section']['id']
			), 
			'recursive' => -1
		));

		$courseRegistrationUpdate = array();

		// allow movement if the target section and own section has similar course id and number of publication is same
		if (count($previousSectionPublishedCourse) == count($targetSectionPublishedCourse)) {
			// is the course similar
			if ($this->similarCourseInSection($previousSectionPublishedCourse, $targetSectionPublishedCourse)) {
				// update course registration table with the new section published course id and section id 
				$courseRegistrationUpdate['CourseRegistration'] = array_merge($courseRegistrationUpdate['CourseRegistration'], $this->publicationMappingWithCourseRegistration($previousSectionPublishedCourse, $target_section_id, $student_detail['Student']['id']));
				// student section ready for update 
				$newSection = array();
				$newSection['id'] = $ownSection['StudentsSection']['id'];
				$newSection['student_id'] = $student_detail['Student']['id'];
				$newSection['section_id'] = $target_section_id;
				$studentSectionUpdate['StudentsSection'][] = $newSection;
			}
		} else if (empty($ownSectionPublishedCourse) && empty($sectionCoursePublication)) {
			// do simple move 
			$newSection = array();
			$newSection['id'] = $ownSection['StudentsSection']['id'];
			$newSection['student_id'] = $student_detail['Student']['id'];
			$newSection['section_id'] = $target_section_id;
			$studentSectionUpdate['StudentsSection'][] = $newSection;
		}

		if (!empty($studentSectionUpdate['StudentsSection'])) {
			if (ClassRegistry::init('StudentsSection')->saveAll($studentSectionUpdate['StudentsSection'], array('validate' => false))) {
			}
		}

		if (!empty($courseRegistrationUpdate['CourseRegistration'])) {
			if (ClassRegistry::init('CourseRegistration')->saveAll($courseRegistrationUpdate['CourseRegistration'], array('validate' => false))) {
			}
		}
	}

	function similarCourseInSection($ownSectionPublishedCourse, $sectionCoursePublication)
	{
		if (count($ownSectionPublishedCourse) == count($sectionCoursePublication)) {
			
			$ownCourseLists = array();
			$sectionCourseLists = array();
			
			foreach ($ownSectionPublishedCourse as $k => $v) {
				$ownCourseLists[] = $v['PublishedCourse']['course_id'];
			}

			foreach ($sectionCoursePublication as $k => $v) {
				$sectionCourseLists[] = $v['PublishedCourse']['course_id'];
			}

			$isThereDifference = array_diff($ownCourseLists, $sectionCourseLists);

			if (!empty($isThereDifference)) {
				return false;
			}
			return true;

		} else {
			return false;
		}
	}

	function publicationMappingWithCourseRegistration($ownSectionPublishedCourse, $targetSectionId, $studentId)
	{
		$courseRegistrationForNewSectionUpdate = array();

		if (!empty($ownSectionPublishedCourse)) {
			foreach ($ownSectionPublishedCourse as $k => $v) {
				$courseRegistration = $this->PublishedCourse->CourseRegistration->find('first', array(
					'conditions' => array(
						'CourseRegistration.student_id' => $studentId,
						'CourseRegistration.published_course_id' => $v['PublishedCourse']['id']
					), 
					'recursive' => -1
				));

				$findTargetSectionPid = $this->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.section_id' => $targetSectionId,
						'PublishedCourse.course_id' => $v['PublishedCourse']['course_id']
					), 
					'recursive' => -1
				));

				$courseRegistration['CourseRegistration']['published_course_id'] = $findTargetSectionPid['PublishedCourse']['id'];
				$courseRegistration['CourseRegistration']['section_id'] = $findTargetSectionPid['PublishedCourse']['section_id'];

				if (!empty($courseRegistration['CourseRegistration']['id']) && !empty($findTargetSectionPid)) {
					$courseRegistrationForNewSectionUpdate[$courseRegistration['CourseRegistration']['id']] = $courseRegistration['CourseRegistration'];
				}
			}
		}

		return $courseRegistrationForNewSectionUpdate;
	}

	function automaticDownGradeSection($department_college_id, $academicYear, $pre = 0) {
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$admissionYear = $AcademicYear->get_academicYearBegainingDate($academicYear);

		if ($pre == 1) {
			$studentLists = ClassRegistry::init('Student')->find('list', array(
				'conditions' => array(
					'Student.college_id' => $department_college_id, 
					'Student.department_id is null', 
					//'Student.admissionyear' => $admissionYear
					'Student.academicyear' => $academicYear,
					'Student.graduated' => 0
				), 
				'fields' => array('Student.id', 'Student.id')
			));
		} else {
			$studentLists = ClassRegistry::init('Student')->find('list', array(
				'conditions' => array(
					'Student.department_id' => $department_college_id, 
					//'Student.admissionyear' => $admissionYear
					'Student.academicyear' => $academicYear,
					'Student.graduated' => 0
				), 
				'fields' => array('Student.id', 'Student.id')
			));
		}

		if (!empty($studentLists)) {
			foreach ($studentLists as $k => $student_id) {
				$findAllSections = ClassRegistry::init('StudentsSection')->find('list', array(
					'conditions' => array(
						'StudentsSection.student_id' => $student_id
					),
					'fields' => array('StudentsSection.section_id', 'StudentsSection.id'),
					'order' => array('StudentsSection.created' => 'ASC'),
				));

				if (!empty($findAllSections)) {

					$findCourseRegistration = $this->CourseRegistration->find('all', array(
						'conditions' => array(
							'CourseRegistration.student_id' => $student_id
						),
						'order' => array('CourseRegistration.academic_year' => 'ASC ', 'CourseRegistration.semester' => 'ASC'),
						'recursive' => -1, 
						'group' => array('CourseRegistration.academic_year', 'CourseRegistration.semester')
					));

					if (!empty($findCourseRegistration)) {
						foreach ($findCourseRegistration as $k => $v) {
							if (in_array($v['CourseRegistration']['section_id'], array_keys($findAllSections))) {
								unset($findAllSections[$v['CourseRegistration']['section_id']]);
							}
						}
					}

					if (count($findAllSections) > 1) {
						reset($findAllSections);
						$first_key = key($findAllSections);
						$activeStudentSectionId = $findAllSections[$first_key];
						unset($findAllSections[$first_key]);
						$this->StudentsSection->id = $activeStudentSectionId;
						$this->StudentsSection->saveField('archive', '0');
						$this->StudentsSection->deleteAll(array('StudentsSection.id' => array_values($findAllSections)), false);
					}
				}
			}
		}
	}

	function downgradeSelectedSection($selectedSectionIds)
	{
		$activePreviousSectionStudents = array();
		$deleteUpgradeSection = array();
		$downgradableSection = array();

		if (!empty($selectedSectionIds)) {
			foreach ($selectedSectionIds as $secId => $Id) {
				if (!empty($Id)) {

					$precedingSection = $this->getPrecedingSection($Id);

					if (!empty($precedingSection)) {

						$currentStudentInSection = $this->StudentsSection->find('list', array(
							'conditions' => array(
								'StudentsSection.section_id' => $secId,
								'StudentsSection.archive' => 0
							), 
							'fields' => array('StudentsSection.id', 'StudentsSection.id')
						));

						//do hard deletion
						if (!$this->PublishedCourse->isCoursePublishedInSection($secId)) {
							//permanently delete it 
							if (!empty($currentStudentInSection)) {
								if ($this->StudentsSection->deleteAll(array('StudentsSection.id' => $currentStudentInSection), false)) {
									$this->delete($secId);
									//active the student in the previous section
									if ($this->StudentsSection->updateAll(array('StudentsSection.archive' => 0), array('StudentsSection.section_id' => $precedingSection['Section']['id']))) {
										$this->id = $precedingSection['Section']['id'];
										$this->saveField('archive', '0');
										$downgradableSection['success'][$secId] = $Id;
									}
								}
							}
						}
					}
				} else {
					$downgradableSection['unsuccess'][$secId] = $Id;
				}
			}
		}

		return $downgradableSection;
	}

	function getPrecedingSection($secId)
	{
		$section = $this->find('first', array('conditions' => array('Section.id' => $secId), 'contain' => array('Department', 'YearLevel')));

		if (!empty($section)) {

			$previousAcademicYear = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($section['Section']['academicyear']);

			$previousYearLevelOfSection = $this->YearLevel->find('first', array(
				'conditions' => array(
					'YearLevel.department_id' => $section['Section']['department_id'], 
					'YearLevel.id <' => $section['Section']['year_level_id']
				),
				'recursive' => -1, 
				'order' => array('YearLevel.id' => 'DESC')
			));

			debug($this->getPrecedingSectionName($section['Section']['name']));

			$previousSection = $this->find('first', array(
				'conditions' => array(
					'Section.name' => $this->getPrecedingSectionName($section['Section']['name']),
					'Section.program_id' => $section['Section']['program_id'],
					'Section.program_type_id' => $section['Section']['program_type_id'],
					'Section.year_level_id' => $previousYearLevelOfSection['YearLevel']['id'],
					'Section.department_id' => $section['Section']['department_id'],
					'Section.academicyear' => $previousAcademicYear['academic_year'],
					'Section.archive' => 1
				), 
				'contain' => array('Department', 'YearLevel')
			));

			return $previousSection;
		}
		return array();
	}

	function getPrecedingSectionName($current_section_name)
	{
		//find previous_section_name and id to downgrade to previous section
		$variable_current_sectionname = substr($current_section_name, strrpos($current_section_name, " ") + 1);
		$first_space = strpos($current_section_name, " ");
		$second_space = strrpos($current_section_name, " ");

		$prefix_current_sectionname = substr($current_section_name, 0, $first_space);
		$fixed_current_sectionname =	substr($current_section_name, ($first_space + 1), ($second_space - ($first_space + 1)));
		//$name = $prefixsectionname.$yearlevelnameshort.' '.$fixedsectionname.' '.$variablesectionname; 

		$prefix_current_sectionname_character = substr($prefix_current_sectionname, 0, -1);
		$prefix_current_sectionname_yearlevel = substr($prefix_current_sectionname, -1);

		$previous_section_name = $prefix_current_sectionname_character . ($prefix_current_sectionname_yearlevel - 1) . ' ' . $fixed_current_sectionname . ' ' . $variable_current_sectionname;
		
		return $previous_section_name;
	}

	function upgradeSelectedSection($selectedSectionIds)
	{
		$categorizeSelectedSectionStudents = array();
		$upgradeableSection = array();

		if (!empty($selectedSectionIds)) {
			foreach ($selectedSectionIds as $key => $secId) {
				if (!empty($secId)) {
					//get section active students
					//$selectedSectionStudents = $this->getSectionActiveStudents($secId);
					$selectedSectionStudents = $this->getSectionActiveStudentsRegistered($secId);

					debug($selectedSectionStudents);
					debug($secId);

					if (!empty($selectedSectionStudents)) {
						//create the new upgraded section 
						$upgradeSection = $this->getUpgradableSectionName($secId);

						$section_ac_year = $this->field('Section.academicyear', array('Section.id' => $secId));
						debug($section_ac_year);
						
						if (!empty($upgradeSection)) {
							$this->create();
							$this->save($upgradeSection);
						}

						$count = 0;

						foreach ($selectedSectionStudents as $sssk => $sssv) {
							
							$student_status = ClassRegistry::init('StudentExamStatus')->isStudentPassed($sssv['StudentsSection']['student_id'], $section_ac_year);
							$all_valid_grades = $this->chceck_all_registered_added_courses_are_graded($sssv['StudentsSection']['student_id'], $secId, 1,  '');
							
							if ($student_status == 4 || $student_status == 2 || !$all_valid_grades) {
								$categorizeSelectedSectionStudents['unupgradable'][$count] = $sssv['StudentsSection']['student_id'];
							} else {
								$categorizeSelectedSectionStudents['upgradable'][$count]['student_id'] = $sssv['StudentsSection']['student_id'];
								$categorizeSelectedSectionStudents['upgradable'][$count]['section_id'] = $this->id;
							}

							$count++;
						}

						debug($categorizeSelectedSectionStudents);

						//Save upgradable students to upgraded section in studentssections associate table		
						if (isset($categorizeSelectedSectionStudents['upgradable']) && !empty($categorizeSelectedSectionStudents['upgradable'])) {
							//archive students of the section in associate table 
							if ($this->StudentsSection->updateAll(array('StudentsSection.archive' => 1), array('StudentsSection.section_id' => $secId))) {
								if ($this->StudentsSection->saveAll($categorizeSelectedSectionStudents['upgradable'], array('validate' => false))) {
									$archiveOldSection = array();
									$archiveOldSection['Section']['id'] = $secId;
									$archiveOldSection['Section']['archive'] = 1;

									if ($this->save($archiveOldSection)) {
										//debug($archiveOldSection);
									}
									$upgradeableSection[$this->id] = $upgradeSection['Section']['name'];
								}
							}
						} else if (!empty($this->id)) {
							$this->delete($this->id);
						}
					}
				}
			}
		}

		return $upgradeableSection;
	}

	function getUpgradableSectionName($secId)
	{

		$newUpgradableSection = array();
		$sectionData = $this->find('first', array('conditions' => array('Section.id' => $secId), 'recursive' => -1));

		if (!empty($sectionData)) {
			$doesUpgradableYearLevelExist = $this->YearLevel->getNextYearLevel($sectionData['Section']['year_level_id'], $sectionData['Section']['department_id']);
			if ($doesUpgradableYearLevelExist) {
				//fragment Section name
				$variable_selected_sectionname = substr($sectionData['Section']['name'], strrpos($sectionData['Section']['name'], " ") + 1);
				$first_space = strpos($sectionData['Section']['name'], " ");
				$second_space = strrpos($sectionData['Section']['name'], " ");
				$prefix_selected_sectionname = substr($sectionData['Section']['name'], 0, $first_space);
				$fixed_selected_sectionname =	substr($sectionData['Section']['name'], ($first_space + 1), ($second_space - ($first_space + 1)));

				$prefix_selected_sectionname_character = substr($prefix_selected_sectionname, 0, -1);
				$prefix_selected_sectionname_yearlevel = substr($prefix_selected_sectionname, -1);

				$upgrade_section_name = $prefix_selected_sectionname_character . ($prefix_selected_sectionname_yearlevel + 1) . ' ' . $fixed_selected_sectionname . ' ' . $variable_selected_sectionname;
				$next_academicyear = ClassRegistry::init('StudentExamStatus')->getNextSemster($sectionData['Section']['academicyear']);

				$newUpgradableSection['Section']['college_id'] = $sectionData['Section']['college_id'];
				$newUpgradableSection['Section']['program_id'] = $sectionData['Section']['program_id'];
				$newUpgradableSection['Section']['program_type_id'] = $sectionData['Section']['program_type_id'];
				$newUpgradableSection['Section']['academicyear'] = $next_academicyear['academic_year'];
				$newUpgradableSection['Section']['department_id'] = $sectionData['Section']['department_id'];
				$newUpgradableSection['Section']['year_level_id'] = $doesUpgradableYearLevelExist;

				if (isset($sectionData['Section']['curriculum_id']) && !empty($sectionData['Section']['curriculum_id']) && is_numeric($sectionData['Section']['curriculum_id']) && $sectionData['Section']['curriculum_id'] > 0) {
					$newUpgradableSection['Section']['curriculum_id'] = $sectionData['Section']['curriculum_id'];
				}

				$newUpgradableSection['Section']['previous_section_id'] = (!empty($sectionData['Section']['id']) ? $sectionData['Section']['id'] : $secId);

				$newUpgradableSection['Section']['name'] = $upgrade_section_name;
				return $newUpgradableSection;
			}
		}
		return $newUpgradableSection;
	}

	function findMeRepresentativeStudentInSection($student_detail, $currentAcademicYear, $studentAdmissionYear, $sectionInThisAC)
	{
		$repNotFound = true;
		$counter = 0;

		do {
			if (!empty($student_detail['Student']['department_id']) && !empty($student_detail['Student']['department_id'])) {
				$possibleSectionSql = "SELECT * FROM `students_sections` WHERE `section_id` in (select id from sections where academicyear='" . $currentAcademicYear . "' and department_id=" . $student_detail['Student']['department_id'] . " and program_id=" . $student_detail['Student']['program_id'] . " and program_type_id=" . $student_detail['Student']['program_type_id'] . ") and student_id in (select id from students where department_id=" . $student_detail['Student']['department_id'] . " and admissionYear='" . $studentAdmissionYear . "' and curriculum_id=" . $student_detail['Student']['curriculum_id'] . ") limit 1";
			} else if (!empty($student_detail['Student']['college_id'])) {
				$possibleSectionSql = "SELECT * FROM `students_sections` WHERE `section_id` in (select id from sections where academicyear='" . $currentAcademicYear . "' and college_id=" . $student_detail['Student']['college_id'] . " and program_id=" . $student_detail['Student']['program_id'] . " and program_type_id=" . $student_detail['Student']['program_type_id'] . " and department_id is null) and student_id in (select id from students where department_id=" . $student_detail['Student']['department_id'] . " and admissionYear='" . $studentAdmissionYear . "') limit 1";
			}

			$possibleSection = $this->query($possibleSectionSql);

			if (!empty($possibleSection[0]['students_sections']['student_id'])) {
				$secSql = "SELECT * FROM `students_sections` WHERE `section_id` in (select id from sections where academicyear='" . $sectionInThisAC . "' and department_id=" . $student_detail['Student']['department_id'] . " and program_id=" . $student_detail['Student']['program_id'] . " and program_type_id=" . $student_detail['Student']['program_type_id'] . ") and `student_id`=" . $possibleSection[0]['students_sections']['student_id'] . "  limit 1";
				$secSqlResult = $this->query($secSql);
				if (!empty($secSqlResult[0]['students_sections']['student_id'])) {
					return $secSqlResult[0]['students_sections']['section_id'];
				}
			}

			if ($counter > 100) {
				return 0;
			}

			$counter++;

		} while (true);

		return 0;
	}

	function getStudentActiveSection($student_id, $academicyear = '')
	{
		$sectionDetail = array();

		if (!empty($student_id)) {

			$students = $this->StudentsSection->find('first', array(
				'conditions' => array(
					'StudentsSection.student_id' => $student_id, 
					'StudentsSection.archive' => 0
				), 
				'order' => array('StudentsSection.created' => 'DESC')
			));

			if (!empty($students['StudentsSection']) && !empty($academicyear)) {
				$section = $this->find('first', array(
					'conditions' => array(
						'Section.id' => $students['StudentsSection']['section_id'],
						'Section.academicyear LIKE ' => $academicyear . '%'
					), 
					'contain' => array('YearLevel', 'Department', 'College', 'Program', 'ProgramType')
				));
				return $section;
			} else if (!empty($students['StudentsSection'])) {
				$section = $this->find('first', array(
					'conditions' => array(
						'Section.id' => $students['StudentsSection']['section_id']
					), 
					'contain' => array('YearLevel', 'Department', 'College', 'Program', 'ProgramType')
				));
				return $section;
			}
		}
		return $sectionDetail;
	}

	function getMostRepresntiveTakenCourse($sectionid)
	{
		$taken_ids = array();
		$section_already_taken_courses = array();
		$selected_students_ids = array();
		$previous_sections[] = $sectionid;

		if ($sectionid) {
			$student_section = $this->StudentsSection->find('first', array(
				'conditions' => array(
					'StudentsSection.section_id' => $sectionid
				),
				'contain' => array()
			));

			$sectionDetail = $this->find('first', array(
				'conditions' => array(
					'Section.id' => $sectionid
				),
				'contain' => array('YearLevel')
			));

			$yearLevels[] = 0;

			if (!empty($sectionDetail['Section']['department_id'])) {

				$yearLevels = $this->YearLevel->find('list', array(
					'conditions' => array(
						'YearLevel.id < ' => $sectionDetail['Section']['year_level_id'],
						'YearLevel.department_id' => $sectionDetail['Section']['department_id']
					),
					'fields' => array('YearLevel.id', 'YearLevel.id')
				));

				//  $yearLevels[$sectionDetail['Section']['year_level_id']] = $sectionDetail['Section']['year_level_id'];
				// find earlier academic year
				$previousAcademicYears = array();
				$previouseAc = array();

				if (!empty($yearLevels)) {
					foreach ($yearLevels as $key => $value) {
						if (empty($previouseAc)) {
							$previouseAc = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($sectionDetail['Section']['academicyear']);
						} else {
							$previouseAc = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($previouseAc['academic_year']);
						}
						$previousAcademicYears[$previouseAc['academic_year']] = $previouseAc['academic_year'];
					}
				}
			}

			/////// ////////////////////////////////////////////////////////////////
			$student_sections = $this->StudentsSection->find('all', array(
				'conditions' => array(
					'StudentsSection.section_id' => $sectionid,
				),
				'contain' => array()
			));
			
			$student_idss = array();

			if (!empty($student_sections)) {
				foreach ($student_sections as $ssindex => $ssvalue) {
					if (!ClassRegistry::init('Readmission')->isEverReadmitted($ssvalue['StudentsSection']['student_id'], $sectionDetail['Section']['academicyear'])) {
						$student_idss[] = $ssvalue['StudentsSection']['student_id'];
						$selected_students_ids[$sectionid][] = $ssvalue['StudentsSection']['student_id'];
					}
				}
			}

			$totalStudentsInSection = count($student_idss);

			if (!empty($yearLevels)) {
				$student_previous_section = $this->StudentsSection->find('all', array(
					'conditions' => array(
						'StudentsSection.student_id' => $student_idss,
						//'StudentsSection.archive' => 1,
						'StudentsSection.section_id in (select id from sections where year_level_id in (' . join(',', $yearLevels) . ') )'
					), 'contain' => array()
				));
			} else {
               /*  $student_previous_section = $this->StudentsSection->find('all', array(
					'conditions' => array(
						'StudentsSection.student_id' => $student_idss,
                   		//'StudentsSection.archive' => 1,
					),
					'contain' => array()
				)); */
			}

			debug($student_previous_section);

			if (!empty($student_previous_section)) {
				foreach ($student_previous_section as $pindex => $pvalue) {
					//AcademicYear
					$academicYear = $this->field('Section.academicyear', array('Section.id' => $pvalue['StudentsSection']['section_id']));
					$currentSectionYearLevelId = $this->field('Section.year_level_id', array('Section.id' => $sectionid));
					$previousSectionYearLevelId = $this->field('Section.year_level_id', array('Section.id' => $pvalue['StudentsSection']['section_id']));

					if ($pvalue['StudentsSection']['section_id'] != $sectionid && in_array($academicYear, $previousAcademicYears) && $currentSectionYearLevelId != $previousSectionYearLevelId) {
						$previous_sections[$pvalue['StudentsSection']['section_id']] = $pvalue['StudentsSection']['section_id'];
					}
				}
			}

			$previous_section[$sectionid] = $sectionid;

			if (isset($student_idss) && !empty($student_idss)) {
				$published_courses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.section_id' => $previous_sections,
						'PublishedCourse.department_id' => $sectionDetail['Section']['department_id'],
						'PublishedCourse.drop' => 0,
						'PublishedCourse.academic_year' => $previousAcademicYears,
						'PublishedCourse.id in (select published_course_id from course_registrations where student_id in (' . join(',', $student_idss) . '))'
					), 
					'recursive' => -1
				));
			} else {
				$published_courses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.section_id' => $previous_sections,
						'PublishedCourse.department_id' => $sectionDetail['Section']['department_id'],
						'PublishedCourse.drop' => 0,
						'PublishedCourse.academic_year' => $previousAcademicYears
					),
					'recursive' => -1
				));
			}

			//half of the students will be considered  to put on taken course list 
			if (!empty($published_courses)) {
				foreach ($published_courses as $pk => $pv) {
					$majorityNumber = ($totalStudentsInSection / 2 + 1);
					if ($this->CourseRegistration->ExamGrade->is_grade_submitted($pv['PublishedCourse']['id'], $student_idss) > ($majorityNumber) && isset($pv['PublishedCourse']['course_id'])) {
						$section_already_taken_courses[$sectionid][] = $pv['PublishedCourse']['course_id'];
					}
				}
			}

			if (empty($section_already_taken_courses)) {
				$section_already_taken_courses[$sectionid][] = 0;
			}
		}

		debug($section_already_taken_courses);
		$taken_ids['taken'] = $section_already_taken_courses;
		$taken_ids['selected_student'] = $selected_students_ids;

		return $taken_ids;
	}

	function updateCourseRegistrationAndSection($admissionYear, $departmentId, $academicYear, $semester, $pre = 0) {

		$updateSectionFiled = array();
		App::import('Component', 'AcademicYear');

		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		if ($pre == 1) {
			$studenLists = ClassRegistry::init('Student')->find('all', array(
				'conditions' => array(
					'Student.college_id' => $departmentId, 
					'Student.department_id IS NULL', 
					//'Student.admissionyear' => $AcademicYear->get_academicYearBegainingDate($admissionYear),
					'Student.academicyear LIKE ' => $admissionYear .'%',
					'Student.graduated' => 0, 
				), 
				'recursive' => -1
			));
		} else {
			$studenLists = ClassRegistry::init('Student')->find('all', array(
				'conditions' => array(
					'Student.department_id' => $departmentId,
					//'Student.admissionyear' => $AcademicYear->get_academicYearBegainingDate($admissionYear),
					'Student.academicyear LIKE ' => $admissionYear . '%',
					'Student.graduated' => 0,
				),
				'recursive' => -1
			));
		}

		$count = 0;
		$sectionUpdate = array();
		$section_count = 0;

		if (!empty($studenLists)) {
			foreach ($studenLists as $kkk => $vv) {
				$currentsections = ClassRegistry::init('StudentsSection')->find('first', array(
					'conditions' => array(
						'StudentsSection.student_id' => $vv['Student']['id'],
						'StudentsSection.archive' => 0,
						'StudentsSection.section_id in (select id from sections where academicyear="' . $academicYear . '")'
					), 
					'recursive' => -1
				));

				$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
					'conditions' => array(
						'CourseRegistration.student_id' => $vv['Student']['id'], 
						'CourseRegistration.academic_year' => $academicYear,
						'CourseRegistration.semester' => $semester
					), 
					'contain' => array('PublishedCourse')
				));

				if (!empty($courseRegistration)) {
					foreach ($courseRegistration as $k => $v) {
						
						$currentSectionPublished = ClassRegistry::init('PublishedCourse')->find('first', array(
							'conditions' => array(
								'PublishedCourse.section_id' => $currentsections['StudentsSection']['section_id'], 
								'PublishedCourse.academic_year' => $academicYear, 
								'PublishedCourse.semester' => $semester,
								'PublishedCourse.drop' => 0,
								'PublishedCourse.course_id' => $v['PublishedCourse']['course_id']
							), 
							'recursive' => -1
						));

						if ($v['CourseRegistration']['section_id'] != $currentSectionPublished['PublishedCourse']['section_id']) {
							$updateSectionFiled['CourseRegistration'][$count]['id'] = $v['CourseRegistration']['id'];
							$updateSectionFiled['CourseRegistration'][$count]['section_id'] = $currentsections['StudentsSection']['section_id'];
							$updateSectionFiled['CourseRegistration'][$count]['student_id'] = $v['CourseRegistration']['student_id'];
							$updateSectionFiled['CourseRegistration'][$count]['published_course_id'] = $currentSectionPublished['PublishedCourse']['id'];
							
							/* $sectionUpdate['StudentsSection'][$section_count]['id'] = $currentsection['StudentsSection']['id'];
							$sectionUpdate['StudentsSection'][$section_count]['section_id'] = $currentsection['StudentsSection']['section_id'];
							$sectionUpdate['StudentsSection'][$section_count]['student_id'] = $currentsection['StudentsSection']['student_id'];
							$sectionUpdate['StudentsSection'][$section_count]['archive'] = 0; */
						}
						$count++;
					}
				}

				$section_count++;
			}
		}

		if (!empty($updateSectionFiled['CourseRegistration'])) {
			if (ClassRegistry::init('CourseRegistration')->saveAll($updateSectionFiled['CourseRegistration'], array('validate' => false))) {
			}
		}
	}


	function studentYearAndSemesterLevelOfStatus($student_id, $acadamic_year, $semester)
	{
		$student_registration = ClassRegistry::init('CourseRegistration')->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.academic_year' => $acadamic_year,
				'CourseRegistration.semester' => $semester
			),
			'contain' => array('YearLevel')
		));

		if (empty($student_registration['YearLevel'])) {
			return 1;
		} elseif (!empty($student_registration['YearLevel'])) {
			$number = filter_var($student_registration['YearLevel']['name'], FILTER_SANITIZE_NUMBER_INT);
			return $number;
		}
		return 0;
	}

	public function getStudentYearLevel($student_id)
	{
		$yearAcademic = array();

		$stsec = $this->CourseRegistration->find('first', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.section_id in (select id from sections where archive = 0)'
			),
			'order' => array(
				'CourseRegistration.created' => 'DESC',
				'CourseRegistration.academic_year' => 'DESC'
			),
			'recursive' => -1
		));

		if (!empty($stsec)) {
			$studentSection = $this->find('first', array(
				'conditions' => array(
					'Section.id' => $stsec['CourseRegistration']['section_id']
				),
				'contain' => array('YearLevel'),
				'order' => array('Section.academicyear' => 'DESC')
			));
		} else {
			$studentSection = $this->find('first', array(
				'conditions' => array(
					'Section.id in (select section_id from students_sections where student_id = ' . $student_id . ') '
				),
				'contain' => array('YearLevel'),
				'order' => array('Section.academicyear' => 'DESC')
			));
		}

		$yearAcademic['academicyear'] = $studentSection['Section']['academicyear'];

		if (empty($studentSection['YearLevel'])) {
			$yearAcademic['year'] = '1st';
		} elseif (!empty($studentSection['YearLevel'])) {
			$yearAcademic['year'] = $studentSection['YearLevel']['name'];
		}
		
		return $yearAcademic;
	}

	public function autoSectionUpgrade($acadmicYear, $department_id = null)
	{
		$options = array();
		$options['recursive'] = -1;
		$options['fields'] = array('Section.id', 'Section.name');
		$options['conditions']['Section.archive'] = 0;

		if (!empty($department_id)) {
			$options['conditions']['Section.department_id'] = $department_id;
		}

		if (!empty($acadmicYear)) {
			$options['conditions']['Section.academicyear'] = $acadmicYear;
		}

		$sections = $this->find('all', $options);

		$sections_lastpublishedcourses_list = array();

		if (!empty($sections)) {
			foreach ($sections as $section) {
				$sections_lastpublishedcourses_list[$section['Section']['id']] = $this->PublishedCourse->lastPublishedCoursesForSection($section['Section']['id']);
			}
		}

		$upgradable_sections = array();
		$unupgradable_sections = array();

		if (!empty($sections_lastpublishedcourses_list)) {
			foreach ($sections_lastpublishedcourses_list as $sk => $sv) {

				$is_submited_grade = 1;

				foreach ($sv as $pk => $vk) {
					$is_submited_grade = $is_submited_grade * $this->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted($pk);
				}

				if ($is_submited_grade != 0) {
					$upgradable_sections[$sk] = $sk;
				} else {
					$unupgradable_sections[] = $sk;
				}
			}
		}

		$this->upgradeSelectedSection($upgradable_sections);
	}

	function getEquivalentProgramTypes($program_type_id = 0) 
	{
		$program_types_to_look = array();

		$equivalentProgramType = unserialize(ClassRegistry::init('ProgramType')->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $program_type_id)));
		
		if (!empty($equivalentProgramType)) {
			$selected_program_type_array = array();
			$selected_program_type_array[] = $program_type_id;
			$program_types_to_look = array_merge($selected_program_type_array, $equivalentProgramType);
		} else {
			$program_types_to_look[] = $program_type_id;
		}

		//debug($program_types_to_look);
		return $program_types_to_look;
	}

	function remove_duplicate_student_sections($section_id = null) {

		if (!empty($section_id) && is_numeric($section_id) && $section_id) {
			$count_duplicated = $this->query("SELECT `a`.* FROM `students_sections` `a` INNER JOIN `students_sections` `a2` WHERE `a`.`id` < `a2`.`id` AND `a`.`student_id` = `a2`.`student_id` AND `a`.`section_id` = `a2`.`section_id` AND `a`.`archive` = 0 AND `a`.`section_id` = $section_id");
			if ($count_duplicated) {
				$deleteDuplicateStudentsInSectionSQL = "DELETE `a` FROM `students_sections` `a` INNER JOIN `students_sections` `a2` WHERE `a`.`id` < `a2`.`id` AND `a`.`student_id` = `a2`.`student_id` AND `a`.`section_id` = `a2`.`section_id` AND `a`.`archive` = 0 AND `a`.`section_id` = $section_id";
			}
		} else {
			$count_duplicated = $this->query("SELECT `a`.* FROM `students_sections` `a` INNER JOIN `students_sections` `a2` WHERE `a`.`id` < `a2`.`id` AND `a`.`student_id` = `a2`.`student_id` AND `a`.`section_id` = `a2`.`section_id` AND `a`.`archive` = 0");
			if ($count_duplicated) {
				$deleteDuplicateStudentsInSectionSQL = "DELETE `a` FROM `students_sections` `a` INNER JOIN `students_sections` `a2` WHERE `a`.`id` < `a2`.`id` AND `a`.`student_id` = `a2`.`student_id` AND `a`.`section_id` = `a2`.`section_id` AND `a`.`archive` = 0";
			}
		}
		//debug(count($count_duplicated));
		
		if (count($count_duplicated) && !empty($deleteDuplicateStudentsInSectionSQL)) {
			$delete_duplicates = $this->query($deleteDuplicateStudentsInSectionSQL);
			return (count($count_duplicated));
		}
	}

	function chceck_all_registered_added_courses_are_graded($student_id = null, $section_id = null, $check_for_invalid_grades = 0,  $from_student = '', $skip_f_grade = 0, $get_error_message = 0)
	{
		
		if (!empty($student_id) && !is_array($student_id) && is_numeric($student_id) && $student_id) {
			$validStudentID = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id)));
		}

		$validSection = true;
		$selectedSectionDetails = array();

		if (!empty($section_id)) {
			$validSection = $this->find('count', array('conditions' => array('Section.id' => $section_id)));
		}

		if ($validSection && !empty($section_id)) {
			$selectedSectionDetails = $this->find('first', array('conditions' => array('Section.id' => $section_id), 'recursive' => -1));
		}

		$academic_year = '';
		$semester = '';
		
		//debug($selectedSectionDetails);

		if (!empty($student_id) && ($validStudentID || is_array($student_id))) {
			$options['conditions'][] = array('Student.id' => $student_id, 'Student.graduated' => 0);
		} else {
			return 0;
		}

		if ($selectedSectionDetails) {
			$academic_year = $selectedSectionDetails['Section']['academicyear'];
		}

		//debug($options);
	
		$options['contain'] = array(
			'Curriculum' => array(
				'fields' => array('id', 'type_credit', 'minimum_credit_points', 'certificate_name', 'amharic_degree_nomenclature', 'specialization_amharic_degree_nomenclature', 'english_degree_nomenclature', 'specialization_english_degree_nomenclature', 'minimum_credit_points', 'name', 'year_introduced'), 
				'Department', 
				'CourseCategory' => array('id', 'curriculum_id')
			),
			'Department.name',
			'Program.name',
			'ProgramType.name',
			'CourseRegistration.id' => array(
				'PublishedCourse' => array(
					'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id', 'PublishedCourse.drop', 'PublishedCourse.academic_year', 'PublishedCourse.semester'),
					'Course.course_title',
					'Course.credit', //=> array('CourseCategory'),
					'Course.curriculum_id',
					'Course.course_code',
				),
				'fields' => array('id', 'student_id', 'section_id', 'academic_year', 'semester', 'published_course_id'),
			),
			'CourseAdd.id' => array(
				//'fields' => array('registrar_confirmation'),
				'fields' => array('id', 'student_id', 'academic_year', 'semester', 'published_course_id', 'registrar_confirmation'),
				'PublishedCourse' => array(
					'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id', 'PublishedCourse.drop', 'PublishedCourse.academic_year', 'PublishedCourse.semester'),
					'Course.credit', // => array('CourseCategory'),
					'Course.course_title',
					'Course.curriculum_id',
					'Course.course_code',
				),
			)
		);

		$options['fields'] = array('Student.id','Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.student_national_id');
		$options['order'] = array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC');
		
		$students = $this->Student->find('all', $options);

		//debug($students);

		$filtered_students = array();

		if (!empty($students)) {
			foreach ($students as $stkey => $student) {
				//debug($student['Student']['id']);
				//Check: 1) All registered course grade is submitted and 2) A valid grade for each registration
				if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
					foreach ($student['CourseRegistration'] as $key => $course_registration) {

						//debug($course_registration['academic_year']);
						if (!empty($section_id) && $academic_year != $course_registration['academic_year']) {
							continue;
						}

						//debug($course_registration);

						$semester = $course_registration['semester'];

						if (!$this->Student->CourseRegistration->isCourseDroped($course_registration['id']) && $course_registration['PublishedCourse']['drop'] == 0) {
							$grade_detail = $this->Student->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration['id']);
							$courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_registration['id'], $course_registration['student_id'], 1);
							//debug($courseRepeated);
							
							if ($courseRepeated['repeated_old']) {
								continue;
							}

							if (!empty($grade_detail) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade'])) {

								$latestApprovedGrade = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade($course_registration['id'], 1);

								// fix invalid grades NG, I, F, Fx, Fail in exam_grades if they have a valid grade changes
								if (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) {
									if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
										//debug($grade_detail);
										//debug($latestApprovedGrade);
										$grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
									}
									//debug($grade_detail);
								} else if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
									// for repeated courses that have a grade change through supplementary exam grade change
									//debug($grade_detail);
									//debug($latestApprovedGrade);
									$grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
								}
							}


							if (empty($grade_detail) /* && !$incomplete_grade  */) {
								$check_for_duplicate_grade_entry = $this->Student->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $course_registration['id'], 'ExamGrade.registrar_approval' => 1)));
								if (!$check_for_duplicate_grade_entry) {
									$filtered_students[$student['Student']['id']]['disqualification'][] = 'Incomplete grade: ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
								}
								//$filtered_students[$student['Student']['id']]['disqualification'][] = 'Incomplete grade: ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
								$incomplete_grade = true;
							} else if ($check_for_invalid_grades /* && !$invalid_grade  */&& isset($grade_detail['ExamGrade']['grade']) && ((strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'W') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) || isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id'])) {
								//debug($grade_detail['ExamGrade']);
								//$filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';

								if ($skip_f_grade && ((strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) || isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id'])) {
									continue;
								}

								if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['pass_grade']) && !$latestApprovedGrade['pass_grade'] && empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
									$filtered_students[$student['Student']['id']]['disqualification'][] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
								} else if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
									$filtered_students[$student['Student']['id']]['disqualification'][] = 'Failed Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
								} else if (isset($grade_detail['ExamGrade']['grade']) && (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'W') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0)) {
									$filtered_students[$student['Student']['id']]['disqualification'][] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
								}

								/* if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id']) {
									$filtered_students[$student['Student']['id']]['disqualification'][] = (empty($from_student) ? 'The Student has ': ' You have ') .' invalid grade (' . $grade_detail['ExamGrade']['grade'] . ') in '. $course_registration['PublishedCourse']['academic_year'] .' semester ' . $course_registration['PublishedCourse']['semester'] . ' for ' . $course_registration['PublishedCourse']['Course']['course_title'] .' (' . $course_registration['PublishedCourse']['Course']['course_code'] .'). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
								} else {
									$filtered_students[$student['Student']['id']]['disqualification'][] = (empty($from_student) ? 'The Student has ': ' You have ') .' invalid grade. Any  of '. (empty($from_student) ? 'the student': 'your') . ' grade should not contain NG, I, DO, W, FAIL, Fx/F.';
								} */
								$invalid_grade = true;
							}
						}
					}
				}

				//Check: 1) All added course grade is submitted and 2) A valid grade for each add

				debug($academic_year);
				debug($semester);

				if (isset($student['CourseAdd']) && !empty($student['CourseAdd'])) {
					foreach ($student['CourseAdd'] as $key => $course_add) {
						if ($course_add['registrar_confirmation'] == false || empty($course_add['id'])) {
							continue;
						}

						if (!empty($section_id) && $academic_year != $course_add['academic_year'] /* && $semester != $course_add['semester'] */) {
							continue;
						}

						debug($course_add);

						$grade_detail = $this->Student->CourseAdd->getCourseAddLatestApprovedGradeDetail($course_add['id']);
						$courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_add['id'], $course_add['student_id'], 0);
						//debug($courseRepeated);

						if (isset($courseRepeated['repeated_old']) && $courseRepeated['repeated_old']) {
							continue;
						}

						if (!empty($grade_detail) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade'])) {

							$latestApprovedGrade = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade($course_add['id'], 0);

							// fix invalid grades NG, I, F, Fx, Fail in exam_grades if they have a valid grade changes
							if (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) {
								if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
									//debug($grade_detail);
									//debug($latestApprovedGrade);
									$grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
								}
								//debug($grade_detail);
							} else if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
								// for repeated courses that have a grade change through supplementary exam grade change
								//debug($grade_detail);
								//debug($latestApprovedGrade);
								$grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
							}
						}


						if (empty($grade_detail) /* && !$incomplete_grade */) {
							$check_for_duplicate_grade_entry = $this->Student->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_add_id' => $course_add['id'], 'ExamGrade.registrar_approval' => 1)));
							if (!$check_for_duplicate_grade_entry && isset($course_add['PublishedCourse']['id'])) {
								$filtered_students[$student['Student']['id']]['disqualification'][]  = 'Incomplete grade: ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st': ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester. (Course Add)';
							}
							//$filtered_students[$student['Student']['id']]['disqualification'][]  = 'Incomplete grade: ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st': ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester. (Course Add)';
							$incomplete_grade = true;
						} else if ($check_for_invalid_grades  /* && !$invalid_grade */ && isset($grade_detail['ExamGrade']['grade']) && ((strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'W') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) || isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['point_value']) && !empty($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id'])) {

							if ($skip_f_grade && ((strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) || isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['point_value']) && !empty($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id'])) {
								continue;
							}

							if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['pass_grade']) && !$latestApprovedGrade['pass_grade'] && empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
								$filtered_students[$student['Student']['id']]['disqualification'][] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st': ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester. (Course Add)';
							} else if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['point_value']) && !empty($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
								$filtered_students[$student['Student']['id']]['disqualification'][] = 'Failed Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st': ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester. (Course Add)';
							} else if (isset($grade_detail['ExamGrade']['grade']) && (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'W') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0)) {
								$filtered_students[$student['Student']['id']]['disqualification'][] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st': ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester. (Course Add)';
							}
							
							/* if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id']) {
								$filtered_students[$student['Student']['id']]['disqualification'][]  = (empty($from_student) ? 'The Student has ': ' You have ') .' invalid grade(' . $grade_detail['ExamGrade']['grade'] . ') in '. $course_add['PublishedCourse']['academic_year'] .' semester ' . $course_add['PublishedCourse']['semester'] . ' for ' . $course_add['PublishedCourse']['Course']['course_title'] .' (' . $course_add['PublishedCourse']['Course']['course_code'] .'). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
							} else {
								$filtered_students[$student['Student']['id']]['disqualification'][]  = (empty($from_student) ? 'The Student has ': ' You have ') .' invalid grade. Any of '. (empty($from_student) ? 'the student': 'your') . ' grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
							} */
							$invalid_grade = true;
						}
					}
				}
			}
		}

		debug($filtered_students);

		if (empty($filtered_students)) {
			return 1;
		} else {
			if ($get_error_message) {
				return $filtered_students;
			}
			return 0;
		}
	}

	function get_section_detailed_name($id = null, $all = 0, $include_curriculum_name = 0) {

		if (!empty($id) && is_numeric($id) && $id > 0) {

			if ($all) {
				$section_detals = $this->find('first', array(
					'conditions' => array('Section.id' => $id),
					'contain' => array(
						'YearLevel',
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'College' => array('id', 'name'),
						'Department' => array('id', 'name'),
						'Curriculum' => array('id', 'name', 'year_introduced'),
					)
				));

				if (!empty($section_detals)) {

					$yearLevelName = !empty($section_detals['YearLevel']) ? $section_detals['YearLevel']['name'] : ($section_detals['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

					$curriculum_name = 'Not attached';

					if (!empty($section_detals['Curriculum']['id'])) {
						$curriculum_name = $section_detals['Curriculum']['name'] . ' - ' . $section_detals['Curriculum']['year_introduced'];
					}

					$name_to_return = $section_detals['College']['name'] . '~' . $section_detals['Department']['name'] . '~' . $section_detals['Program']['name'] . '~' . $section_detals['ProgramType']['name'] . '~' . $yearLevelName . '~'. (str_replace('  ', ' ', trim($section_detals['Section']['name']))) . ' (' . $yearLevelName . ', ' .  $section_detals['Section']['academicyear']. ')';

					if ($include_curriculum_name) {
						$name_to_return .= '~'. $curriculum_name;
					}

					return $name_to_return;
				}

			} else {

				$section_detals = $this->find('first', array('conditions' => array('Section.id' => $id), 'contain' => array('YearLevel')));
				
				if (!empty($section_detals)) {
					$yearLevelName = !empty($section_detals['YearLevel']) ? $section_detals['YearLevel']['name'] : ($section_detals['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');
					return (str_replace('  ', ' ', trim($section_detals['Section']['name']))) . ' (' . $yearLevelName . ', ' .  $section_detals['Section']['academicyear']. ')';
				}
			}
		}

		return 0;
	}
}
