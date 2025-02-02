<?php
class EheeceResult extends AppModel
{
	var $name = 'EheeceResult';
	var $validate = array(
		'subject' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide subject',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mark' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide mark',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Please provide mark greather than or equal to zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'exam_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide examination year',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
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

	function deleteEheeceResultList($student_id = null, $data = null)
	{
		$dontdeleteids = array();
		$deleteids = array();

		$deleteids = $this->find('list', array(
			'conditions' => array('EheeceResult.student_id' => $student_id),
			'fields' => 'id'
		));

		if (!empty($data['EheeceResult'])) {
			foreach ($data['EheeceResult'] as $in => $va) {
				if (!empty($va['id'])) {
					if (in_array($va['id'], $deleteids)) {
						$dontdeleteids[] = $va['id'];
					}
				}
			}

		}

		if (!empty($dontdeleteids)) {
			foreach ($deleteids as $in => &$va) {
				if (in_array($va, $dontdeleteids)) {
					unset($deleteids[$in]);
				}
			}
		}

		if (!empty($deleteids)) {
			$this->deleteAll(array('EheeceResult.id' => $deleteids), false);
		}

	}

	function updateExamTakenDate($college_id, $admissionYear)
	{
		$updateExamTakenDate = array();

		if (empty($college_id) && empty($takenDate)) {
			return 0;
		}

		$studenLists = ClassRegistry::init('Student')->find('all', array(
			'conditions' => array(
				'Student.college_id' => $college_id,
				'Student.admissionyear' => $admissionYear,
				'Student.graduated' => 0
			),
			'contain' => array('EheeceResult')
		));

		$takenDate = explode('-', $admissionYear);
		$count = 0;

		if (!empty($studenLists)) {
			foreach ($studenLists as $vList) {
				foreach ($vList['EheeceResult'] as $eheeResult) {
					$updateExamTakenDate['EheeceResult'][$count]['id'] = $eheeResult['id'];
					$updateExamTakenDate['EheeceResult'][$count]['exam_year'] = $takenDate[0] . '-07-01';
					$count++;
				}

			}
		}

		if (!empty($updateExamTakenDate['EheeceResult'])) {
			if ($this->saveAll($updateExamTakenDate['EheeceResult'], array('validate' => false))) {
			}
		}
	}
}
