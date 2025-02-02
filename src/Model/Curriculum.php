<?php
class Curriculum extends AppModel
{
	public $name = 'Curriculum';
	public $virtualFields = array('curriculum_detail' => 'CONCAT(Curriculum.name, " - ",Curriculum.year_introduced)');
	public $displayField = 'name';

	//public $actsAs = array('Tools.Logable');
	var $actsAs = array(
		'Tools.Logable' => array(
			'change' => 'full',
			'description_ids' => 'true',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide curriculum name, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'certificate_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide certificate name, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'year_introduced' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide curriculum introduced year, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'type_credit' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide type of credit, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amharic_degree_nomenclature' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide amharic degree nomenclature, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'english_degree_nomenclature' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide english degree nomenclature, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select program, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'minimum_credit_points' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide minimum credit points, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide valide minimum credit point , greater than or equal to zero.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'sumCreditCurriculum' => array(
				'rule' => array('sumCreditCurriculum'),
				'message' => 'The minimum credit point should be less than or equal to the sum of the course category total credit,please adjust.'
			),
			'sumMandatoryCredit' => array(
				'rule' => array('sumMandatoryCredit'),
				'message' => 'The sum of the mandatory credit should be equal to minimum credit points,please adjust.'
			)
		),

	);
	
	function sumCreditCurriculum()
	{
		$sum_course_category = 0;
		
		if (!empty($this->data['CourseCategory'])) {
			foreach ($this->data['CourseCategory'] as $ck => $cv) {
				$sum_course_category += $cv['total_credit'];
			}
		}

		if ($sum_course_category >= $this->data['Curriculum']['minimum_credit_points']) {
			return true;
		}

		return false;
	}

	function sumMandatoryCredit()
	{
		$sum_course_category = 0;

		if (!empty($this->data['CourseCategory'])) {
			foreach ($this->data['CourseCategory'] as $ck => $cv) {
				$sum_course_category += $cv['mandatory_credit'];
			}
		}

		if ($sum_course_category == $this->data['Curriculum']['minimum_credit_points']) {
			return true;
		}

		return false;
	}
	
	var $belongsTo = array(
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
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
		'DepartmentStudyProgram' => array(
			'className' => 'DepartmentStudyProgram',
			'foreignKey' => 'department_study_program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'curriculum_id',
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
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'curriculum_id',
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
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions'  => array('model' => 'Curriculum'),
			'order' => array('Attachment.created' => 'DESC'),
			'dependent' => true,
		),
		'CourseCategory' => array(
			'className' => 'CourseCategory',
			'foreignKey' => 'curriculum_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	function canItBeDeleted($curriculum_id = null)
	{
		if ($this->Course->find('count', array('conditions' => array('Course.curriculum_id' => $curriculum_id))) > 0) {
			return false;
		} else if ($this->Student->find('count', array('conditions' => array('Student.curriculum_id' => $curriculum_id))) > 0) {
			return false;
		}
		/* else if ($this->Attachment->find('count', array('conditions' => array('Attachment.model' => 'Curriculum', 'Attachment.foreign_key' => $curriculum_id))) > 0) {
			return false;
		} else if ($this->CourseCategory->find('count', array('conditions' => array('CourseCategory.curriculum_id' => $curriculum_id))) > 0) {
			return false; 
		} */
		else {
			return true;
		}
	}

	function organized_course_of_curriculum_by_year_semester($data = null)
	{
		$courses_organized_by_year = array();

		if (!empty($data['id'])) {
			foreach ($data['Course'] as $index => $value) {
				if (empty($value['Course'])) {
					$courses_organized_by_year[$value['YearLevel']['name']][$value['semester']][] = $value;
					// $value['hasEquivalentMap']=ClassRegistry::init('EquivalentCourse')->checkCourseHasEquivalentCourse($value['id'],$studentAttachedCurriculumID);
				}
			}
			$data['Course'] = $courses_organized_by_year;
		}
		return $data;
	}

	function preparedAttachment($data = null)
	{
		if (!empty($data['Attachment'])) {
			foreach ($data['Attachment'] as $in =>  &$dv) {
				if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
					unset($data['Attachment'][$in]);
				} else {
					$dv['model'] = 'Curriculum';
					$dv['group'] = 'attachment';
				}
			}
			return $data;
		}
	}

	function getDepartmentStudyProgramDetails($department_id = null, $program_modality_id = null, $qualification_id = null)
	{
		$conditions = array();

		if ($department_id) {
			$conditions[] = array('DepartmentStudyProgram.department_id' => $department_id);
		}

		if ($program_modality_id) {
			$conditions[] = array('DepartmentStudyProgram.program_modality_id' => $program_modality_id);
		}

		if ($qualification_id) {
			$conditions[] = array('DepartmentStudyProgram.qualification_id' => $qualification_id);
		}

		//debug($conditions);

		$departmentStudyProgramDetails = array();
		$departmentStudyProgramListForSelect = array();


		if (!empty($conditions)) {
			$departmentStudyProgramDetails = $this->DepartmentStudyProgram->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
					'StudyProgram' => array('fields' => array('id', 'study_program_name', 'code')),
					'ProgramModality' => array('fields' => array('id', 'modality', 'code')),
					'Qualification'  => array('fields' => array('id', 'qualification', 'code')),
				),
				'fields' => array('DepartmentStudyProgram.id', 'DepartmentStudyProgram.study_program_id')
			));
		}

		if (!empty($departmentStudyProgramDetails)) {
			foreach ($departmentStudyProgramDetails as $dspkey => $dspval) {
				//debug($dspval);
				$departmentStudyProgramListForSelect[$dspval['DepartmentStudyProgram']['id']] =  $dspval['StudyProgram']['study_program_name'] . '(' . $dspval['StudyProgram']['code'] .') => ' . $dspval['ProgramModality']['modality'] . '(' . $dspval['ProgramModality']['code'] . ') => ' . $dspval['Qualification']['qualification'] . '(' . $dspval['Qualification']['code'] . ')';
			}
		}

		return $departmentStudyProgramListForSelect;
	}
}
