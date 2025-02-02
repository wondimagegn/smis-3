<?php
class ProgramTypeTransfer extends AppModel
{
	var $name = 'ProgramTypeTransfer';
	var $validate = array(
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide academic year',
			),
		),
		'semester' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide semester',
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
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	function getProgramTransferDate($data = null)
	{
		$ordered_item = $this->find('first', array('conditions' => array('ProgramTypeTransfer.student_id' => $data['ProgramTypeTransfer']['student_id']), 'order' => array('ProgramTypeTransfer.transfer_date' => 'DESC')));

		$check1 = $ordered_item['ProgramTypeTransfer']['transfer_date'];
		$check2 = $data['ProgramTypeTransfer']['transfer_date']['year'] . '-' . $data['ProgramTypeTransfer']['transfer_date']['month'] . '-' . $data['ProgramTypeTransfer']['transfer_date']['day'];

		if ($check2 < $check1) {
			$this->invalidate('error', 'The transfer date for ' . $data['ProgramTypeTransfer']['program_type_id'] . ' should greater than the earlier transfer date which is ' . $check1 . ' ');
			return false;
		}
		return true;
	}

	function getStudentProgramType($student_id = null, $acadamic_year = null, $semester = null)
	{
		$student_pt_transfers = $this->find('all', array(
			'conditions' => array('ProgramTypeTransfer.student_id' => $student_id),
			'order' => array('ProgramTypeTransfer.transfer_date' => 'ASC'),
			'recursive' => -1
		));

		$student_detail = $this->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'contain' => array('AcceptedStudent.academicyear')));
		
		if (empty($student_pt_transfers)) {
			return $student_detail['Student']['program_type_id'];
		} else {

			$program_type_id = $student_detail['Student']['program_type_id'];
			$sys_acadamic_year = $student_detail['AcceptedStudent']['academicyear'];
			$sys_semester = 'I';

			do {
				foreach ($student_pt_transfers as $key => $program_type_transfer) {
					if ($sys_acadamic_year == $program_type_transfer['ProgramTypeTransfer']['academic_year'] && $sys_semester == $program_type_transfer['ProgramTypeTransfer']['semester']) {
						$program_type_id = $program_type_transfer['ProgramTypeTransfer']['program_type_id'];
					}
				}

				if (!(strcasecmp($acadamic_year, $sys_acadamic_year) == 0 && strcasecmp($semester, $sys_semester) == 0)) {
					if (strcasecmp($sys_semester, 'I') == 0) {
						$sys_semester = 'II';
					} else if (strcasecmp($sys_semester, 'II') == 0) {
						$sys_semester = 'III';
					} else {
						$sys_semester = 'I';
						$sys_acadamic_year = (substr($sys_acadamic_year, 0, 4) + 1) . '/' . substr((substr($sys_acadamic_year, 0, 4) + 2), 2, 2);
					}
				} else {
					return $program_type_id;
				}
			} while ($sys_acadamic_year != '3000/01');

			return $program_type_id;
		}
	}
}
