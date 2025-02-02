<?php
class DepartmentStudyProgram extends AppModel
{
	var $name = 'DepartmentStudyProgram';

	var $belongsTo = array(
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StudyProgram' => array(
			'className' => 'StudyProgram',
			'foreignKey' => 'study_program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        'ProgramModality' => array(
			'className' => 'ProgramModality',
			'foreignKey' => 'program_modality_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        'Qualification' => array(
			'className' => 'Qualification',
			'foreignKey' => 'qualification_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	var $hasMany = array(
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'department_study_program_id',
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

	function isUniqueDepartmentStudyProgram($data = null)
	{
		$count = 0;

		debug($data);

		if (!empty($data['DepartmentStudyProgram']['id'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'DepartmentStudyProgram.department_id' => $data['DepartmentStudyProgram']['department_id'], 
					'DepartmentStudyProgram.study_program_id' => $data['DepartmentStudyProgram']['study_program_id'],
					'DepartmentStudyProgram.program_modality_id' => $data['DepartmentStudyProgram']['program_modality_id'],
					'DepartmentStudyProgram.qualification_id' => $data['DepartmentStudyProgram']['qualification_id'],
					'DepartmentStudyProgram.academic_year' => $data['DepartmentStudyProgram']['academic_year'],
					'DepartmentStudyProgram.id <> ' => $data['DepartmentStudyProgram']['id']
				)
			));
		} else if (!empty($data['DepartmentStudyProgram'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'DepartmentStudyProgram.department_id' => $data['DepartmentStudyProgram']['department_id'], 
					'DepartmentStudyProgram.study_program_id' => $data['DepartmentStudyProgram']['study_program_id'],
					'DepartmentStudyProgram.program_modality_id' => $data['DepartmentStudyProgram']['program_modality_id'],
					'DepartmentStudyProgram.academic_year' => $data['DepartmentStudyProgram']['academic_year'],
					'DepartmentStudyProgram.qualification_id' => $data['DepartmentStudyProgram']['qualification_id']
				)
			));
		}

		debug($count);

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function canItBeDeleted($department_study_program_id = null)
	{
		if (ClassRegistry::init('Curriculum')->find('count', array('conditions' => array('Curriculum.department_study_program_id' => $department_study_program_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
