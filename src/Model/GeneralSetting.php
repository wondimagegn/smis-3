<?php
App::uses('AppModel', 'Model');
class GeneralSetting extends AppModel
{
	public $validate = array(
		'daysAvaiableForNgToF' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'daysAvaiableForDoToF' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'daysAvailableForFxToF' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'weekCountForAcademicYear' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'minimumCreditForStatus' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allowMealWithoutCostsharing' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'notifyStudentsGradeByEmail' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allowStudentsGradeViewWithouInstructorsEvalution' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
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
		)
	);

	function daysAvaiableForGradeChange($published_course_id)
	{
		$publishedCourses = ClassRegistry::init('PublishedCourse')->find('first', array(
			'conditions' => array(
				'PublishedCourse.id' => $published_course_id
			), 
			'recursive' => -1
		));

		if (!empty($publishedCourses)) {

			$settings = $this->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $publishedCourses['PublishedCourse']['program_id'] . '"%',
					'GeneralSetting.program_type_id like' => '%s:_:"' . $publishedCourses['PublishedCourse']['program_type_id'] . '"%',

				), 
				'recursive' => -1
			));

			if (!empty($settings)) {
				return $settings['GeneralSetting']['daysAvaiableForGradeChange'];
			}
		}

		return DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
	}

	function daysAvaiableForNgToF()
	{
		//Important Note: The maximum allowed days should not be greater than 4 months
		return DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
	}

	function daysAvaiableForDoToF()
	{
		//Important Note: The maximum allowed days should not be greater than 4 months
		return DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
	}

	function daysAvailableForFxToF()
	{
		return DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;
	}

	function allowMealWithoutCostsharing($student_id)
	{
		$studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));

		$settings = $this->find('first', array(
			'conditions' => array(
				'GeneralSetting.program_id like' => '%s:_:"' . $studentDetail['Student']['program_id'] . '"%',
				'GeneralSetting.program_type_id like' => '%s:_:"' . $studentDetail['Student']['program_type_id'] . '"%',
			), 
			'recursive' => -1
		));

		if (!empty($settings)) {
			return $settings['GeneralSetting']['allowMealWithoutCostsharing'];
		}
		return 0;
	}

	function notifyStudentsGradeByEmail($exam_grade_id)
	{
		$gradeDetail = ClassRegistry::init('ExamGrade')->find('first', array(
			'conditions' => array(
				'ExamGrade.id' => $exam_grade_id
			),
			'contain' => array(
				'CourseRegistration' => array('PublishedCourse'),
				'CourseAdd' => array('PublishedCourse'),
				'MakeupExam' => array('PublishedCourse'),
			)
		));

		if (!empty($gradeDetail['CourseRegistration'])) {
			
			$settings = $this->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $gradeDetail['CourseRegistration']['PublishedCourse']['program_id'] . '"%',
					'GeneralSetting.program_type_id like' => '%s:_:"' . $gradeDetail['CourseRegistration']['PublishedCourse']['program_type_id'] . '"%',

				), 
				'recursive' => -1
			));

			if (!empty($settings)) {
				return $settings['GeneralSetting']['notifyStudentsGradeByEmail'];
			}

		} else if (!empty($gradeDetail['CourseAdd'])) {
			
			$settings = $this->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $gradeDetail['CourseAdd']['PublishedCourse']['program_id'] . '"%',
					'GeneralSetting.program_type_id like' => '%s:_:"' . $gradeDetail['CourseAdd']['PublishedCourse']['program_type_id'] . '"%',

				), 
				'recursive' => -1
			));

			if (!empty($settings)) {
				return $settings['GeneralSetting']['notifyStudentsGradeByEmail'];
			}

		} else if (!empty($gradeDetail['MakeupExam'])) {

			$settings = $this->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $gradeDetail['MakeupExam']['PublishedCourse']['program_id'] . '"%',
					'GeneralSetting.program_type_id like' => '%s:_:"' . $gradeDetail['MakeupExam']['PublishedCourse']['program_type_id'] . '"%',

				), 
				'recursive' => -1
			));

			if (!empty($settings)) {
				return $settings['GeneralSetting']['notifyStudentsGradeByEmail'];
			}
		}

		return 0;
	}

	public function allowRegistrationWithoutPayment($student_id)
	{
		$studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));

		$settings = $this->find('first', array(
			'conditions' => array(
				'GeneralSetting.program_id like' => '%s:_:"' . $studentDetail['Student']['program_id'] . '"%',
				'GeneralSetting.program_type_id like' => '%s:_:"' . $studentDetail['Student']['program_type_id'] . '"%',

			), 
			'recursive' => -1
		));

		if (!empty($settings)) {
			return $settings['GeneralSetting']['allowRegistrationWithoutPayment'];
		}

		return 0;
	}

	function allowStudentsGradeViewWithouInstructorsEvalution($student_id)
	{
		$studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));

		$settings = $this->find('first', array('conditions' => array(
			'GeneralSetting.program_id like' => '%s:_:"' . $studentDetail['Student']['program_id'] . '"%',
			'GeneralSetting.program_type_id like' => '%s:_:"' . $studentDetail['Student']['program_type_id'] . '"%',

		), 'recursive' => -1));

		if (!empty($settings)) {
			return $settings['GeneralSetting']['allowStudentsGradeViewWithouInstructorsEvalution'];
		}
		
		return 0;
	}

	function getAllGeneralSettingsByStudentByProgramIdOrBySectionID($student_id = null, $program = null, $program_type = null, $section_id = null)
	{
		$settings = array();

		if (!empty($student_id)) {
			$studentDetail = ClassRegistry::init('Student')->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				), 
				'contain' => array('Curriculum'),
				'recursive' => -1
			));

			if (!empty($studentDetail)){
				$program = $studentDetail['Student']['program_id'];
				$program_type = $studentDetail['Student']['program_type_id'];

				//$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($student_id, null, null);
				//debug($student_section_exam_status);
			}
		}

		if (!empty($section_id)) {
			$sectionDetail = ClassRegistry::init('Section')->find('first', array(
				'conditions' => array(
					'Section.id' => $section_id
				), 
				'contain' => array('Curriculum'),
				'recursive' => -1
			));

			//debug($sectionDetail);

			if (!empty($sectionDetail)) {
				$program = $sectionDetail['Section']['program_id'];
				$program_type = $sectionDetail['Section']['program_type_id'];
				if (!isset($sectionDetail['Curriculum']['id'])) {
					$section_curriculum = ClassRegistry::init('Section')->getSectionCurriculum($section_id);
					//debug($section_curriculum);
					if (!empty($section_curriculum) && is_numeric($section_curriculum)) {
						$curriculumDetail= ClassRegistry::init('Curriculum')->find('first', array(
							'conditions' => array(
								'Curriculum.id' => $section_curriculum
							), 
							'fields' => array('id', 'name', 'type_credit', 'english_degree_nomenclature', 'curriculum_detail'),
							'recursive' => -1
						));
						if (!empty($curriculumDetail)) {
							$sectionDetail['Curriculum'] = $curriculumDetail['Curriculum'];
						}
					}
				}
			}

		}

		if (!empty($program) && !empty($program_type)) {
			$settings = $this->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program . '"%',
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type . '"%',
				), 
				'recursive' => -1
			));
		}
		
		if (!empty($settings)) {
			if (isset($studentDetail['Curriculum']['id']) && is_numeric($studentDetail['Curriculum']['id'])) {
				if (count(explode('ECTS', $studentDetail['Curriculum']['type_credit'])) >= 2) {
					$settings['GeneralSetting']['minimumCreditForStatus'] =  (int) round($settings['GeneralSetting']['minimumCreditForStatus'] * CREDIT_TO_ECTS);
					$settings['GeneralSetting']['maximumCreditPerSemester'] =  (int) round($settings['GeneralSetting']['maximumCreditPerSemester'] * CREDIT_TO_ECTS);
				}
			} else if (isset($sectionDetail['Curriculum']['id']) && is_numeric($sectionDetail['Curriculum']['id'])) {
				//debug($sectionDetail);
				if (count(explode('ECTS', $sectionDetail['Curriculum']['type_credit'])) >= 2) {
					$settings['GeneralSetting']['minimumCreditForStatus'] =  (int) round($settings['GeneralSetting']['minimumCreditForStatus'] * CREDIT_TO_ECTS);
					$settings['GeneralSetting']['maximumCreditPerSemester'] =  (int) round($settings['GeneralSetting']['maximumCreditPerSemester'] * CREDIT_TO_ECTS);
				}
			}

			return $settings;

		} else {

			$settings['GeneralSetting']['daysAvaiableForGradeChange'] = DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
			$settings['GeneralSetting']['daysAvaiableForNgToF'] = DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
			$settings['GeneralSetting']['daysAvaiableForDoToF'] = DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
			$settings['GeneralSetting']['daysAvailableForFxToF'] = DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;

			$settings['GeneralSetting']['weekCountForAcademicYear'] = DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
			$settings['GeneralSetting']['semesterCountForAcademicYear'] = DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
			$settings['GeneralSetting']['weekCountForOneSemester'] = DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
			$settings['GeneralSetting']['daysAvailableForStaffEvaluation'] = DEFAULT_DAYS_AVAILABLE_FOR_STAFF_EVALUATION;
			
			$settings['GeneralSetting']['minimumCreditForStatus'] = DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
			$settings['GeneralSetting']['maximumCreditPerSemester'] = DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;

			$settings['GeneralSetting']['allowStaffEvaluationAfterGradeSubmission'] = 0;
			$settings['GeneralSetting']['allowMealWithoutCostsharing'] = 0;
			$settings['GeneralSetting']['notifyStudentsGradeByEmail'] = 0;
			$settings['GeneralSetting']['allowRegistrationWithoutPayment'] = 0;
			$settings['GeneralSetting']['allowStudentsGradeViewWithouInstructorsEvalution'] = 0;

			return $settings;
		}
	}

	function check_duplicate_entry($data = null)
	{
		$duplicatePrograms = array();

		if (isset($data['GeneralSetting']['id'])) {
			$generalSettings = $this->find('all', array(
				'conditions' => array(
					'GeneralSetting.id <>' => $data['GeneralSetting']['id']
				),
				'recursive' => -1
			));
		} else {
			$generalSettings = $this->find('all', array('recursive' => -1));
		}

		$requestProgramIDs = unserialize($data['GeneralSetting']['program_id']);
		$requestProgramTypeIDs = unserialize($data['GeneralSetting']['program_type_id']);

		if (!empty($generalSettings)) {
			foreach ($generalSettings as $index => $generalSetting) {
				$existing_program_ids = unserialize($generalSetting['GeneralSetting']['program_id']);
				if (!empty($existing_program_ids)) {
					$existing_program_type_ids = unserialize($generalSetting['GeneralSetting']['program_type_id']);
					foreach ($existing_program_ids as $p_key => $ex_prog_id) {
						foreach ($existing_program_type_ids as $pt_key => $ex_prog_type_id) {
							if (in_array($ex_prog_id, $requestProgramIDs) && in_array($ex_prog_type_id, $requestProgramTypeIDs)) {
								$duplicatePrograms[$ex_prog_type_id][] = $ex_prog_id;
							}
						}
					}
				}
			}
		}

		if (!empty($duplicatePrograms)) {
			foreach ($duplicatePrograms as $prty_id => $duplicateProgram) {
				if (!empty($duplicateProgram)) {
					$programName = $this->Program->field('name', array('Program.id' => $duplicateProgram));
					$programTypeName = $this->ProgramType->field('name', array('ProgramType.id' => $prty_id));
					$duplicateEntries[] = 'There is existing general setting for '. $programName. ' with ' . $programTypeName . ' program type.';
					$this->invalidate('duplicateEntries', $duplicateEntries);
					//$this->invalidate('duplicate', 'There is existing general setting for '. $programName. ' with ' . $programTypeName . ' program type.');
					//$duplicateProgramTypes[] = $prty_id;
					//$duplicatePrograms[] = $duplicateProgram;
					//$this->invalidate('duplicatePrograms', $duplicateProgram);
					//$this->invalidate('duplicateProgramTypes', $duplicateProgramTypes);
					return false;
				}
			}
		}

		return true;
	}
}
