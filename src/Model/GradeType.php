<?php
class GradeType extends AppModel
{
	var $name = 'GradeType';
	var $displayField = 'type';

	var $validate = array(
		'type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide grade type name , it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('checkUnique', 'type'),
				'message' => 'You have already setup the given grade type'
			)
		),
	);
	
	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
		}
		return $valid;
	}

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'grade_type_id',
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
		'Grade' => array(
			'className' => 'Grade',
			'foreignKey' => 'grade_type_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		/*
		'GradeScale'=>array(
		    'className' => 'GradeScale',
			'foreignKey' => 'grade_type_id',
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
		*/
	);

	function allowDelete($grade_id = null)
	{
		if ($this->GradeScaleDetail->find('count', array('conditions' => array('GradeType.id' => $grade_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function is_duplicated($grade = null)
	{
		if (!empty($grade['GradeType'])) {
			$conditions['Grade.type'] = $grade['GradeType']['type'];
			$count = $this->find('count', array('conditions' => $conditions));
			if ($count > 0) {
				$this->invalidate('duplicate_entry', 'Duplicate data entry. You have already recorded the grade type.');
				return false;
			} else {
				return true;
			}
		}

		if (!empty($grade['Grade'])) {
			//
		}
		/*
		$conditions['Grade.grade_type_id'] = $grade['grade_type_id'];
		$conditions['Grade.grade'] = $grade['grade'];
		$conditions['Grade.point_value'] = $grade['point_value'];
		$count = $this->find('count', array('conditions' => $conditions));
		
		if($count > 0) {
			return false;
		} else {
			return true;
		}
	    */
	}

	function unset_empty_rows($data = null)
	{
		if (!empty($data['GradeType'])) {
			$skip_first_row = 0;
			foreach ($data['GradeType'] as $k => &$v) {
				if ($skip_first_row == 0) {
					//
				} else {
					if (empty($v['grade']) && empty($v['point_value'])) {
						unset($data['GradeType'][$k]);
					}
				}
				$skip_first_row++;
			}
		}
		return $data;
	}

	function getGradeScaleDetails($grade_type_id = null, $program_id = null, $model = 'College', $foreign_key = null, $active = 1, $own = 0)
	{
		$grade_scale_options = array();

		if ($active !== ""){
			$grade_scale_options['GradeScale.active'] = $active;
		}

		// commented out because it works during college delegation for freshman purpose but now registrat is responsible 
		// no need to say own thing
		// $grade_scale_options['GradeScale.own'] = $own;

		$grade_scale_options['GradeScale.model'] = $model;
		$grade_scale_options['GradeScale.foreign_key'] = $foreign_key;

		$grade_scale_detail = $this->find('first', array(
			'conditions' => array(
				'GradeType.id' => $grade_type_id,
				'GradeType.active' => 1
			),
			'contain' => array(
				'Grade' => array(
					'GradeScaleDetail' => array(
						'GradeScale' => array(
							'conditions' => $grade_scale_options
						)
					)
				)
			)
		));

		$grade_scales = array();

		foreach ($grade_scale_detail['Grade']['0']['GradeScaleDetail'] as $key => $grade_scale_detail_temp) {
			if (isset($grade_scale_detail_temp['GradeScale']) && !empty($grade_scale_detail_temp['GradeScale']) && ($program_id == "" || ($program_id != "" && $program_id == $grade_scale_detail_temp['GradeScale']['program_id']))) {
				$grade_scales[] = $grade_scale_detail_temp['GradeScale'];
			}
		}

		$grade_scale_and_type['GradeType'] = $grade_scale_detail['GradeType'];
		$grade_scale_and_type['GradeScale'] = $grade_scales;

		return $grade_scale_and_type;
	}

	function grade_type_data()
	{
		return $this->data['GradeType'];
	}

	function is_grade_type_attached_to_course($grade_type_id = null)
	{
		$courses = $this->Course->find('count', array('conditions' => array('Course.grade_type_id' => $grade_type_id)));
		if ($courses == 0) {
			return true;
		} else {
			return false;
		}
	}
}
