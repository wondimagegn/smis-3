<?php
class GraduationRequirement extends AppModel {
	var $name = 'GraduationRequirement';
	var $validate = array(
		'cgpa' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*'applicable_for_current_student' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function getMinimumGraduationCGPA($program_id = null, $admission_year = null) {
		$gr_detail = $this->find('first', 
			array(
				'conditions' =>
				array(
					'GraduationRequirement.program_id' => $program_id,
					'GraduationRequirement.academic_year <= '.substr($admission_year, 0, 4)
				),
				'order' =>
				array(
					'GraduationRequirement.academic_year DESC'
				),
				'recursive' => -1
			)
		);
		if(isset($gr_detail['GraduationRequirement']['cgpa']))
			return $gr_detail['GraduationRequirement']['cgpa'];
		else
			return 0;
	}

}
