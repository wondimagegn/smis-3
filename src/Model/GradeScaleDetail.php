<?php
class GradeScaleDetail extends AppModel
{
	var $name = 'GradeScaleDetail';

	// The Associations below have been created with all possible keys, those that are not needed can be removed
	// We can log all actions by calling this here, but it is also possible to call  the loggable behavior in selected models.

	var $actsAs = array(
		'Logable' => array(
			'change' => 'full',
			'description_ids' => 'false',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	var $validate = array(
		'minimum_result' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Numeric value required.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Point value must be greater than or equal zero.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('field_comparison', '<', 'maximum_result'),
				'message' => 'Minimum result should be less than maximum result',
			),
			/*  
			'unique' => array (
                'rule' => array('checkUnique', 'minimum_result'),
                'message' => 'Duplicate point value.'
            )
            */
		),
		'maximum_result' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Numeric value required.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Maximum value must be greater than or equal zero.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '<=', 100),
				'message' => 'Maximum value must be less than or equal 100.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'minimum_result'),
				'message' => 'Maximum result should be greather than minimum result',
			),
			/*
			'unique' => array (
                'rule' => array('checkUnique', 'maximum_result'),
                'message' => 'Duplicate point value.'
            )
            */
		),
		'grade_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'This field can\'t be left blank',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			/*
			'unique' => array (
                'rule' => array('checkUnique', 'grade_id'),
                'message' => 'Duplicate grade.'
            ),*/
		)
	);

	function checkUnique($data, $fieldName)
	{
		debug($this->data);
		debug($fieldName);
		$already = array();
		$already[] = $this->data['GradeScaleDetail']['grade_id'];

		if (in_array($data['grade_id'], $already)) {
			debug($data);
			debug($already);
			return false;
		}
		return true;

		/*
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $repeated = false;
            $previous = null;
            $count = 0;

            foreach ($data['GradeScaleDetail'] as $scale_detail => $grade) {
                if($count == 0) {
                	$previous = $grade['grade_id'];
                } else {
                    if ($previous == $grade['grade_id']) {
                        return false;
                    } else {
                        $previous = $grade['grade_id'];
                    }
                }
                $count++;
            }
        }
        */

		/* 
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
			if (!$valid) {
				$check = $this->find('count', array(
					'conditions' => array(
						'grade_id' => $this->data['GradeScaleDetail']['grade_id'],
						'point_value' => $this->data['GradeScale']['grade_type_id']
					)
				));
				if ($check == 0) {
					return true;
				}
			}
		}
		return $valid; 
		*/
            
	}

	function field_comparison($check1, $operator, $field2)
	{
		foreach ($check1 as $key => $value1) {
			$value2 = $this->data[$this->alias][$field2];
			if (!Validation::comparison($value1, $operator, $value2)){
				return false;
			}
		}
		return true;
	}

	function check_scale_execlusiveness($data = null, $role = null)
	{
		if ($role == ROLE_COLLEGE) {
			$already_recorded_range = $this->find('all', array(
				'conditions' => array(
					'model' => 'GradeScale.College', 
					'GradeScale.active' => 1, 
					'GradeScale.foreign_key' => $data['GradeScale']['foreign_key'], 
					'GradeScale.program_id' => $data['GradeScale']['program_id']
				)
			));
		} else if ($role == ROLE_DEPARTMENT) {
			$already_recorded_range = $this->GradeScale->find('all', array(
				'conditions' => array(
					'GradeScale.model' => 'Department', 
					'GradeScale.active' => 1, 
					'GradeScale.foreign_key' => $data['GradeScale']['foreign_key'],
					'GradeScale.program_id' => $data['GradeScale']['program_id']
				)
			));
		}

		if (!empty($data['GradeScaleDetail'])) {
			foreach ($data['GradeScaleDetail'] as $kk => $vv) {
				if (empty($vv['minimum_result']) && empty($vv['maximum_result'])) {
					return true;
				}
			}
		}

		if (!empty($already_recorded_range)) {
			foreach ($already_recorded_range as $ar => $sr) {
				foreach ($sr['GradeScaleDetail'] as $kkkk => $vvvv) {
					foreach ($data['GradeScaleDetail'] as $k => $v) {
						if (!empty($v['minimum_result']) && !empty($v['maximum_result'])) {
							if (($v['minimum_result'] <= $vvvv['minimum_result'] && $vvvv['minimum_result'] <= $v['maximum_result']) ||
								($v['minimum_result'] <= $vvvv['maximum_result'] && $vvvv['maximum_result'] <= $v['maximum_result']) ||
								($vvvv['minimum_result'] <= $v['minimum_result'] && $v['minimum_result'] <= $vvvv['maximum_result'])
							) {
								$this->invalidate('minimum_maximum_result', 'The given grade range is not unique. Please make sure that "Minimum result" and/or "Maximum" is  already recorded.');
								return false;
							}
						}
					}
				}
			}
		}
		return true;
	}

	// Model validation against continutiy

	function gradeRangeContinuty($data = null)
	{
		if (!empty($data)) {

			//find the grade based on grade type and sort by point value
			$grades = $this->Grade->find('all', array(
				'conditions' => array(
					'Grade.grade_type_id' => $data['GradeScale']['grade_type_id']
				), 
				'order' => 'Grade.point_value desc', 
				'contain' => array()
			));

			//exit(1);
			$count = 0;
			$next_maximum = 0;
			$previous_minimum = 0;

			foreach ($grades as $grade_key => $grade_value) {
				// find grade 
				foreach ($data['GradeScaleDetail'] as
					$grade_scale => $grade_scale_detail) {
					if (!empty($grade_scale_detail['maximum_result']) && !empty($grade_scale_detail['minimum_result'])) {
						if ($grade_value['Grade']['id'] == $grade_scale_detail['grade_id']) {
							if ($count == 0) {
								if ($grade_scale_detail['maximum_result'] == 100) {
									$next_maximum = $grade_scale_detail['minimum_result'];
									$count++;
									break 1;
								} else {
									$this->invalidate('grade_range_continuty', 'The maximum result  for ' . $grade_value['Grade']['grade'] . ' is 100');
									return false;
								}
							} else {
								if ($grade_scale_detail['maximum_result'] == ($next_maximum - 0.01)) {
									$next_maximum = $grade_scale_detail['minimum_result'];
									$count++;
									break 1;
								} else {
									$this->invalidate('grade_range_continuty', 'The next maximum  for ' . $grade_value['Grade']['grade'] . ' is should be ' . ($next_maximum - 0.01) . '');
									return false;
								}
							}
						}
					} else {
						return true;
					}
				}
				$count++;
			}
		}
		return true;
	}

	function checkGradeIsUnique($data = null)
	{
		if (!empty($data)) {

			$already_defined = null;

			$grades = $this->Grade->find('list', array(
				'conditions' => array(
					'Grade.grade_type_id' => $data['GradeScale']['grade_type_id']
				), 
				'order' => 'Grade.point_value desc', 
				'contain' => array(), 
				'fields' => array('id', 'grade')
			));

			$frequencey_count = array();

			// Count the frequency of grade repeation and display invalidation message if grade is duplicated
			if (!empty($data['GradeScaleDetail'])) {

				foreach ($data['GradeScaleDetail'] as $grade_id => $grade_value) {
					$frequencey_count[] = $grade_value['grade_id'];
				}

				$how_many_times = array_count_values($frequencey_count);

				if (count($how_many_times) > 0) {
					foreach ($how_many_times as $grade_id => $frequency) {
						if ($frequency > 1) {
							$this->invalidate('checkGradeIsUnique', 'Grade ' . $grades[$grade_id] . ' is duplicated ' . $frequency . ' times. Please change the grade.');
							return false;
						}
					}
					return true;
				} else {
					return true;
				}
			}
			return true;
			/*
            $count = 0;
            foreach ($data['GradeScaleDetail'] as $grade_id=>$grade_value) {
            	if ($count == 0) {
                    $already_defined = $grade_value['grade_id'];
                } else {
                    if ($already_defined == $grade_value['grade_id']) {
                        $this->invalidate('checkGradeIsUnique', 'Grade '.$grades[$grade_value['grade_id']].' is duplicated. Please change the grade.');
                        return false;
                    } else {
                    	$already_defined = $grade_value['grade_id'];
                    }    
                } 
                $count++;
            }
            return true;
            */
		}
		return false;
	}

	var $belongsTo = array(
		'GradeScale' => array(
			'className' => 'GradeScale',
			'foreignKey' => 'grade_scale_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Grade' => array(
			'className' => 'Grade',
			'foreignKey' => 'grade_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
