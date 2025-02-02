<?php
class Readmission extends AppModel
{
	var $name = 'Readmission';

	var $validate = array(
		'minute_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide minute number.',
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'academic_commision_approval' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select accepted or rejected option.',
				'allowEmpty' => false,
				'last' => true, // Stop validation after this rule
			),
		),
	);

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	function is_readmitted($student_id = null, $academic_year = null)
	{
		$check = $this->find('count', array(
			'conditions' => array(
				'Readmission.student_id' => $student_id,
				'Readmission.academic_year like ' => $academic_year . '%',
				'Readmission.registrar_approval = 1',
				'Readmission.academic_commision_approval = 1'
			)
		));

		if ($check > 0) {
			return true;
		} else {
			return false;
		}
	}

	function isEverReadmitted($student_id)
	{
		$check = $this->find('count',
			array('conditions' => array(
				'Readmission.student_id' => $student_id,
				'Readmission.registrar_approval = 1',
				'Readmission.academic_commision_approval = 1'
			)
		));

		if ($check > 0) {
			return true;
		} else {
			return false;
		}
	}

	function isReadmitted($student_id, $academic_year, $semester)
	{
		$check = $this->find('count', array(
			'conditions' => array(
				'Readmission.student_id' => $student_id,
				'Readmission.academic_year' => $academic_year,
				'Readmission.semester' => $semester,
				'Readmission.registrar_approval = 1',
				'Readmission.academic_commision_approval = 1'
			)
		));

		if ($check > 0) {
			return true;
		} else {
			return false;
		}
	}

	function elegible_for_readmission($student_id = null, $current_academic_year = null)
	{
		//Searching for the rule by the acadamic stand
		/* $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
			'conditions' => array(
				'AcademicRule.academic_stand_id' => $as['id']
			),
			'recursive' => -1
		));
		
		//debug($acadamic_rules);
		//If acadamic rule is found
		if (!empty($acadamic_rules)) {
			foreach ($acadamic_rules as $key => $acadamic_rule) {
				if ($acadamic_rule['AcademicRule']['tcw'] == 1) {
					//return true;
					return 1;
				}
			}
		} */
	}

	function organizeListOfReadmissionApplicant($data = null)
	{
		$readmission_applicant_organized_by_program = array();

		if (!empty($data)) {
			foreach ($data as $index => $value) {
				if (empty($value['Student']['Department']['name'])) {
					$readmission_applicant_organized_by_program['Pre/Freshman'][$value['Student']['Program']['name']][$value['Student']['ProgramType']['name']][] = $value;
				} else {
					$readmission_applicant_organized_by_program[$value['Student']['Department']['name']][$value['Student']['Program']['name']][$value['Student']['ProgramType']['name']][] = $value;
				}
			}
		}

		return $readmission_applicant_organized_by_program;
	}


	function getListOfStudentsForReadmission($nonfreshman = 1, $program_id = null, $program_type_id = null, $department_id = null, $academic_year = null, $semester = null, $name = null, $admission_years = null) 
	{
		/***
			1. Get all students in the department/college who are neither in graduation nor senate list	
			2. Get all students who doesnt apply for readmission of selected academic year and semester	
			3. Return the list
		***/
		//$options['conditions']['Student.program_id'] = $program_id;

		if ($admission_years != 0 && !empty($admission_years)) {
			$options['conditions']['Student.academicyear'] = $admission_years;
		}

		if ($program_id != 0 && !empty($program_id)) {
			$options['conditions']['Student.program_id'] = $program_id;
		}

		if ($program_type_id != 0 && !empty($program_type_id)) {
			$options['conditions']['Student.program_type_id'] = $program_type_id;
		}

		if ($nonfreshman == 1) {
			$options['conditions']['Student.department_id'] = $department_id;
		} else {
			$options['conditions']['Student.college_id'] = $department_id;
			$options['conditions'][] = 'Student.department_id IS NULL';
		}

		if (isset($name) && !empty($name)) {
			$options['conditions'][] = 'Student.first_name like "%' . $name . '%"';
		}
		
		// $options['conditions'][] = 'Student.curriculum_id IS NOT NULL';
		// $options['conditions'][] = 'Student.curriculum_id <> 0';

		//$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM graduate_lists)';
		//$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM senate_lists)';

		$options['conditions'][] = 'Student.graduated = 0';

		$request_dateFilter = date('Y-m-d H:i:s', strtotime("-" . DAYS_BACK_READMISSION . " day "));

		debug($request_dateFilter);
		debug($admission_years);
		
		if (isset($academic_year) && isset($semester)) {
			$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM readmissions where  academic_year="' . $academic_year . '" AND semester = "' . $semester . '" AND created >= "' . $request_dateFilter . '")';
		}

		$options['contain'] = array(
			'Curriculum' => array(
				'fields' => array(
					'id', 
					'minimum_credit_points',
					'certificate_name', 
					'amharic_degree_nomenclature',
					'specialization_amharic_degree_nomenclature', 
					'english_degree_nomenclature',
					'specialization_english_degree_nomenclature', 
					'minimum_credit_points', 'name'
				),
				'Department', 
				'CourseCategory' => array('id', 'curriculum_id')
			),
			'Department.name',
			'Program.name',
			'ProgramType.name',
		);

		$students = $this->Student->find('all', $options);
		// debug($students);
		$filtered_students = array();

		$not_dismissal_status_ids = ['0'=> '1','1'=> '2','2'=> '3'];

		if (!empty($students)) {
			foreach ($students as $key => $student) {
				
				$last_status = $this->Student->StudentExamStatus->find('first', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student['Student']['id']
					),
					'order' => array(
						'StudentExamStatus.created DESC'
					),
					'recursive' => -1
				));

				if (!empty($last_status) && !in_array($last_status['StudentExamStatus']['academic_status_id'], $not_dismissal_status_ids) && !is_null($last_status['StudentExamStatus']['academic_status_id'])) {

					$cid = $student['Curriculum']['id'];

					if (!isset($filtered_students[$cid])) {
						$filtered_students[$cid][0]['Curriculum'] = $student['Curriculum'];
						$filtered_students[$cid][0]['Program'] = $student['Program'];
						$filtered_students[$cid][0]['ProgramType'] = $student['ProgramType'];
						$filtered_students[$cid][0]['Department'] = $student['Department'];
					}

					$index = count($filtered_students[$cid]);

					$filtered_students[$cid][$index] = $student;

					if (!empty($last_status)) {
						$filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
						$filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
					} /* else {
						$filtered_students[$cid][$index]['cgpa'] = null;
						$filtered_students[$cid][$index]['mcgpa'] = null;
					} */

					$error = "";

					$elegible = $this->Student->Clearance->elegibleForReadmission($student['Student']['id']);

					if ($elegible == 0 || $elegible == 5) {
						$error = "This student doesn't have clearance/withdraw. <br/>";
					}

					// readmission status is not properly set up for AMU and Not being used.. Neway.

					/* $minimumPointAchieved = $this->Student->Clearance->isAchievedMinimumReadmissionPoint($student['Student']['id']);

					if ($minimumPointAchieved == 3) {
						$error .= "This student have not achieved minimum readmission point for application. <br/>";
					} */

					if (isset($error) && !empty($error)) {
						$filtered_students[$cid][$index]['criteria']['error'] = $error;
					}

				}
			}

			return $filtered_students;
		}

		return array();
	}
}
