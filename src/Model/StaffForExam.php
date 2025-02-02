<?php
class StaffForExam extends AppModel {
	var $name = 'StaffForExam';
	var $displayField = 'staff_id';
	//var $virtualFields = array('full_name' => 'CONCAT(StaffForExam.first_name, " ",StaffForExam.middle_name," ",StaffForExam.last_name)');

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Invigilator' => array(
			'className' => 'Invigilator',
			'foreignKey' => 'staff_for_exam_id',
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
		'InstructorExamExcludeDateConstraint' => array(
			'className' => 'InstructorExamExcludeDateConstraint',
			'foreignKey' => 'staff_for_exam_id',
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
		'InstructorNumberOfExamConstraint' => array(
			'className' => 'InstructorNumberOfExamConstraint',
			'foreignKey' => 'staff_for_exam_id',
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
		/*'Invigilator' => array(
			'className' => 'Invigilator',
			'foreignKey' => 'staff_for_exam_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)*/
	);
	
	function getInvigilators($college_id = null, $acadamic_year = null, $semester = null, $exam_date = null, $session = null, $year_level = null) {
		$staffsForExam = array();
		$staffs = $this->find('all',
			array(
				'conditions' =>
				array(
					'StaffForExam.college_id' => $college_id,
					'StaffForExam.academic_year' => $acadamic_year,
					'StaffForExam.semester' => $semester,
					'StaffForExam.id NOT IN (SELECT staff_for_exam_id FROM instructor_exam_exclude_date_constraints WHERE exam_date = \''.$exam_date.'\' AND session = \''.$session.'\')',
					//Exclude already assigned invigilators
					'StaffForExam.id NOT IN (SELECT staff_for_exam_id FROM invigilators WHERE exam_schedule_id IN (SELECT id FROM exam_schedules WHERE exam_date = \''.$exam_date.'\' AND session = \''.$session.'\'))',
				),
				'contain' =>
				array(
					'Staff',
					'InstructorNumberOfExamConstraint' =>
					array(
						'conditions' =>
						array(
							'InstructorNumberOfExamConstraint.academic_year' => $acadamic_year,
							'InstructorNumberOfExamConstraint.semester' => $semester,
							'InstructorNumberOfExamConstraint.year_level_id' => $year_level
						)
					)
				)
			)
		);
		//debug($staffs);
		$i = 0;
		foreach($staffs as $staff) {
			$staffsForExam[$i]['id'] = $staff['StaffForExam']['id'];
			if(!empty($staff['InstructorNumberOfExamConstraint'])) {
				$staffsForExam[$i]['max_number_of_exam'] = $staff['InstructorNumberOfExamConstraint'][0]['max_number_of_exam'];
			}
			else {
				$staffsForExam[$i]['max_number_of_exam'] = 0;
			}
			$staffsForExam[$i]['assigned_exam'] = 0;
			$assigned_exams = $this->Invigilator->ExamSchedule->find('all',
				array(
					'conditions' =>
					array(
						'ExamSchedule.acadamic_year' => $acadamic_year,
						'ExamSchedule.semester' => $semester,
						'ExamSchedule.id IN (SELECT exam_schedule_id FROM invigilators WHERE staff_for_exam_id = \''.$staff['StaffForExam']['id'].'\')'
					),
					'contain' =>
					array(
						'PublishedCourse' =>
						array(
							'Section' =>
							array(
								'YearLevel'
							)
						),
					)
				)
			);
			foreach($assigned_exams as $assigned_exam) {
				if(strcasecmp($assigned_exam['PublishedCourse']['Section']['YearLevel']['name'], $year_level) == 0)
					$staffsForExam[$i]['assigned_exam']++;
			}
			//debug($assigned_exams);
			$i++;
		}
		//debug($staffsForExam);
		return $staffsForExam;
	}

}
