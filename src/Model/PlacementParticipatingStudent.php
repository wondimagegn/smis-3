<?php
class PlacementParticipatingStudent extends AppModel
{
	public $validate = array(
		'accepted_student_id' => array(
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
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'program_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'original_college_department' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
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
		'round' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'total_placement_weight' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'female_placement_weight' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'disability_weight' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'developing_region_weight' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'placement_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
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
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'accepted_student_id',
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
		'PlacementRoundParticipant' => array(
			'className' => 'PlacementRoundParticipant',
			'foreignKey' => 'placement_round_participant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function reformat($data = array())
	{
		if (isset($data) && !empty($data)) {

			$reformatedData = array();

			$checkedBoxStudents = $data['PlacementParticipatingStudent']['approve'];
			$selectedStudents = array_keys($checkedBoxStudents, 1);

			//debug($selectedStudents);
			unset($data['PlacementParticipatingStudent']['approve']);

			$dataa['PlacementParticipatingStudent'] = $data['PlacementParticipatingStudent'];
			//debug($dataa);

			$isThePlacementRun = $this->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'PlacementParticipatingStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
					'OR' => array(
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
						'PlacementParticipatingStudent.status' => 1
					)
				)
			));

			debug($isThePlacementRun);

			if ($isThePlacementRun) {
				return 1;
			} else {

				if (isset($dataa['PlacementParticipatingStudent']) && !empty($dataa['PlacementParticipatingStudent'])) {
					foreach ($dataa['PlacementParticipatingStudent'] as $dk => $dv) {
						//check if there is entry already and exclude
						$alreadyPrepared = $this->find('count', array(
								'conditions' => array(
									'PlacementParticipatingStudent.accepted_student_id' => $dv['accepted_student_id'],
									'PlacementParticipatingStudent.student_id' => $dv['student_id'],
									'PlacementParticipatingStudent.program_id' => $dv['program_id'],
									'PlacementParticipatingStudent.program_type_id' => $dv['program_type_id'],
									'PlacementParticipatingStudent.academic_year' => $dv['academic_year'],
									'PlacementParticipatingStudent.round' => $dv['round'],
								),
								'recursive' => -1
							)
						);

						if (in_array($dv['accepted_student_id'], $selectedStudents)) {
							$reformatedData['PlacementParticipatingStudent'][$dk] = $dv;
						} 

						/* if (!$alreadyPrepared && in_array($dv['accepted_student_id'], $selectedStudents)) {
							$reformatedData['PlacementParticipatingStudent'][$dk] = $dv;
						} */
					}
				}
				
				return $reformatedData;
				
			}
		}

		return 0;
	}

	public function reformatForDelete($data = array())
	{
		if (isset($data) && !empty($data)) {

			$reformatedData = array();

			$checkedBoxStudents = $data['PlacementParticipatingStudent']['approve'];
			$selectedStudents = array_keys($checkedBoxStudents, 1);

			//debug($selectedStudents);
			unset($data['PlacementParticipatingStudent']['approve']);

			$dataa['PlacementParticipatingStudent'] = $data['PlacementParticipatingStudent'];
			//debug($dataa);

			//debug($data);

			$isThePlacementRun = $this->find('count', array(
				'conditions' => array(
					'PlacementParticipatingStudent.program_id' => Configure::read('programs_available_for_placement_preference'),
					'PlacementParticipatingStudent.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
					'OR' => array(
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
						'PlacementParticipatingStudent.status' => 1
					)
				)
			));

			debug($isThePlacementRun);

			if ($isThePlacementRun) {
				return 1;
			} else {

				if (isset($dataa['PlacementParticipatingStudent']) && !empty($dataa['PlacementParticipatingStudent'])) {
					foreach ($dataa['PlacementParticipatingStudent'] as $dk => $dv) {
						//check if there is entry already and exclude
						$alreadyPrepared = $this->find('count', array(
								'conditions' => array(
									'PlacementParticipatingStudent.accepted_student_id' => $dv['accepted_student_id'],
									'PlacementParticipatingStudent.student_id' => $dv['student_id'],
									'PlacementParticipatingStudent.program_id' => $dv['program_id'],
									'PlacementParticipatingStudent.program_type_id' => $dv['program_type_id'],
									'PlacementParticipatingStudent.academic_year' => $dv['academic_year'],
									'PlacementParticipatingStudent.round' => $dv['round'],
								),
								'recursive' => -1
							)
						);

						if ($alreadyPrepared && in_array($dv['accepted_student_id'], $selectedStudents)) {
							//debug($dv);
							if (isset($dv['id']) && !empty($dv['id'])) {
								$reformatedData['PlacementParticipatingStudent'][$dk] = $dv['id'];
							}
						}
					}
				}
			}

			return $reformatedData;
		}
		
		return 0;
	}

	public function getNextRound($academic_year, $accepted_student_id) 
	{
		$acceptedStudentdetail = $this->AcceptedStudent->find('first', array('conditions' => array('AcceptedStudent.id' => $accepted_student_id), 'contain' => array('Student')));
       
		$previousRound = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
			'conditions' => array(
				'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
				'PlacementParticipatingStudent.accepted_student_id' =>  $acceptedStudentdetail['AcceptedStudent']['id']
			),
			'order' => array(
				'PlacementParticipatingStudent.academic_year' => 'DESC',
				'PlacementParticipatingStudent.round' => 'DESC'
			)
		));

		if (isset($previousRound) && !empty($previousRound)){
            $applied_for = $previousRound['PlacementParticipatingStudent']['applied_for'];
		} else {

			$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($acceptedStudentdetail['Student']['id'], null, null);
			//debug($student_section_exam_status);

			// for readmitted students/withdrawed students
			if (isset($student_section_exam_status['Section']) && $student_section_exam_status['Section']['academicyear'] == $academic_year && !$student_section_exam_status['Section']['archive']) {
				
				$nonBatchStudentlist = $this->Student->find('list', array(
					'conditions' => array(
						'Student.college_id' => $acceptedStudentdetail['Student']['college_id'],
						'Student.program_id' => Configure::read('programs_available_for_placement_preference'),
						'Student.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
						'Student.department_id IS NULL',
						'Student.academicyear' => $academic_year,
						'Student.id IN (select student_id from students_sections where section_id = ' . $student_section_exam_status['Section']['id'] . ' GROUP BY student_id, section_id )',
					), 
					'fields' => array('Student.id','Student.id')
				));


				debug($nonBatchStudentlist);

				$previousRound = $this->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
						'PlacementParticipatingStudent.student_id' =>  $nonBatchStudentlist,
					),
					'order' => array(
						'PlacementParticipatingStudent.academic_year' => 'DESC',
						'PlacementParticipatingStudent.round' => 'DESC',
					)
				));

				debug($previousRound);

			} else {

				$batchAcceptedStudentlist = $this->AcceptedStudent->find('list', array(
					'conditions' => array(
						'AcceptedStudent.academicyear' => $acceptedStudentdetail['AcceptedStudent']['academicyear'],
						'AcceptedStudent.college_id' => $acceptedStudentdetail['AcceptedStudent']['college_id']
					), 
					'fields' => array('AcceptedStudent.id','AcceptedStudent.id')
				));

				$previousRound = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
						'PlacementParticipatingStudent.accepted_student_id' =>  $batchAcceptedStudentlist
					),
					'order' => array(
						'PlacementParticipatingStudent.academic_year' => 'DESC',
						'PlacementParticipatingStudent.round' => 'DESC'
					)
				));
			}

		    if (isset($previousRound) && !empty($previousRound)) {
                $applied_for = $previousRound['PlacementParticipatingStudent']['applied_for'];
		    } else {
				// if there is no participation 
                if (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
					// the assignment is specialization 
					$applied_for = 'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'];
				} else if (isset($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
					// the student is still in college, and what was the previous college assigment if exist
                    $applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
		        }
            }
		}

		if(isset($applied_for) && !empty($applied_for)){
			$participatingRound = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
				'conditions' => array(
					'PlacementParticipatingStudent.applied_for' => $applied_for,
					'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
				),
				'order' => array(
					'PlacementParticipatingStudent.academic_year' => 'DESC',
					'PlacementParticipatingStudent.round' => 'DESC'
				)
			));
		} else {
			$participatingRound = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
				'conditions' => array(
					//'PlacementParticipatingStudent.applied_for' => $applied_for,
					'PlacementParticipatingStudent.academic_year LIKE ' => $academic_year . '%',
					'PlacementParticipatingStudent.placement_round_participant_id is not null '
				),
				'order' => array(
					'PlacementParticipatingStudent.academic_year' => 'DESC',
					'PlacementParticipatingStudent.round' => 'DESC'
				)
			));
		}
		
		if (isset($participatingRound) && !empty($participatingRound)) {
			return $participatingRound['PlacementParticipatingStudent']['round'] + 1;
		}

		return 1;
	}

	public function isCurrentPlacementRoundDefined($data = array())
	{
		$firstData = $data['PlacementRoundParticipant'][1];

		$participatingRound = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
			'conditions' => array(
				'PlacementParticipatingStudent.applied_for' => $firstData['applied_for'],
				'PlacementParticipatingStudent.academic_year LIKE ' => $firstData['academic_year'] . '%',
				'PlacementParticipatingStudent.round' => $firstData['placement_round'],
				'PlacementParticipatingStudent.program_id' => $firstData['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $firstData['program_type_id']
				//	'PlacementParticipatingStudent.placement_round_participant_id is not null '
			),
			'order' => array(
				'PlacementParticipatingStudent.academic_year' => 'DESC',
				'PlacementParticipatingStudent.round' => 'DESC'
			)
		));
		
		if (isset($participatingRound['PlacementParticipatingStudent']) && !empty($participatingRound['PlacementParticipatingStudent'])) {
			return $participatingRound['PlacementParticipatingStudent']['round'];
		}
		return 0;
	}
}
