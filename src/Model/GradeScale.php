<?php
class GradeScale extends AppModel
{
	var $name = 'GradeScale';
	var $displayField = 'name';

	//We can log all actions by calling this here, but it is also possible to call  the loggable behavior in selected models.

	var $actsAs = array(
		'Logable' => array(
			'change' => 'full',
			'description_ids' => 'false',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide name.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('checkUnique', 'name'),
				'message' => 'This name is already taken, use different name.'
			),
		),

		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Select program type.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
		'GradeType' => array(
			'className' => 'GradeType',
			'foreignKey' => 'grade_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),

		/*
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		*/

		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'GradeScaleDetail' => array(
			'className' => 'GradeScaleDetail',
			'foreignKey' => 'grade_scale_id',
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
		'ExamGrade' => array(
			'className' => 'ExamGrade',
			'foreignKey' => 'grade_scale_id',
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
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'grade_scale_id',
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
	);

	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
		}
		return $valid;
	}

	function allowDelete($grade_id = null)
	{
		if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.grade_scale_id' => $grade_id))) > 0) {
			return false;
		} elseif ($this->GradeScaleDetail->find('count', array('conditions' => array('GradeScaleDetail.grade_scale_id' => $grade_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function check_grade_submitted($id = null)
	{
		$is_scale_attached = $this->PublishedCourse->find('list', array('conditions' => array('PublishedCourse.grade_scale_id' => $id), 'fields' => array('PublishedCourse.id')));
		if (count($is_scale_attached) > 0) {
			$course_registration_ids = $this->PublishedCourse->CourseRegistration->find('list', array('conditions' => array('CourseRegistration.published_course_id' => $is_scale_attached), 'fields' => array('id')));
			if (!empty($course_registration_ids)) {
				$is_grade_submitted = $this->PublishedCourse->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $course_registration_ids)));
				//deny scale editing
				if ($is_grade_submitted > 0) {
					return true;
				}
			}
		} else {
			//check other route
			$is_grade_submitted = $this->PublishedCourse->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.grade_scale_id' => $id)));
			if ($is_grade_submitted > 0) {
				return true;
			}
		}
		false;
	}

	function unset_empty_rows($data = null)
	{
		if (!empty($data)) {
			$skip_first_row = 0;
			foreach ($data['GradeScaleDetail'] as $k => &$v) {
				if ($skip_first_row == 0) {
					//
				} else {
					if (empty($v['minimum_result']) && empty($v['maximum_result'])) {
						unset($data['GradeScaleDetail'][$k]);
					}
				}
				$skip_first_row++;
			}
		}
		return $data;
	}

	function getGradeScaleId($gradeType, $studentDetail)
	{
		if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
			if (!empty($studentDetail['College']['deligate_scale']) && $studentDetail['College']['deligate_scale'] == 1) {

				$gradeScale = $this->find('first', array(
					'conditions' => array(
						'GradeScale.grade_type_id' => $gradeType, 
						'GradeScale.model' => 'Department', 
						'GradeScale.foreign_key' => $studentDetail['Student']['department_id'], 
						'GradeScale.program_id' => $studentDetail['Student']['program_id'], 
						'GradeScale.active' => 1
					),
					'recursive' => -1
				));

				if (empty($gradeScale) && empty($studentDetail['Student']['department_id'])) {
					$gradeScale = $this->find('first', array(
						'conditions' => array(
							'GradeScale.grade_type_id' => $gradeType, 
							'GradeScale.model' => 'College',
							'GradeScale.foreign_key' => $studentDetail['Student']['college_id'], 
							'GradeScale.program_id' => $studentDetail['Student']['program_id'], 
							'GradeScale.active' => 1
						),
						'recursive' => -1
					));
				}
			} else {
				$gradeScale = $this->find('first', array(
					'conditions' => array(
						'GradeScale.grade_type_id' => $gradeType, 
						'GradeScale.model' => 'College', 
						'GradeScale.foreign_key' => $studentDetail['Student']['college_id'], 
						'GradeScale.program_id' => $studentDetail['Student']['program_id'], 
						'GradeScale.active' => 1
					), 
					'recursive' => -1
				));
			}
		} else {
			if (!empty($studentDetail['College']['deligate_for_graduate_study']) && $studentDetail['College']['deligate_for_graduate_study'] == 1) {
				$gradeScale = $this->find('first', array(
					'conditions' => array(
						'GradeScale.grade_type_id' => $gradeType, 
						'GradeScale.model' => 'Department', 
						'GradeScale.foreign_key' => $studentDetail['Student']['department_id'], 
						'GradeScale.program_id' => $studentDetail['Student']['program_id'], 
						'GradeScale.active' => 1
					),
					'recursive' => -1
				));
			} else {
				$gradeScale = $this->find('first', array(
					'conditions' => array(
						'GradeScale.grade_type_id' => $gradeType, 
						'GradeScale.model' => 'College', 
						'GradeScale.foreign_key' => $studentDetail['Student']['college_id'], 
						'GradeScale.program_id' => $studentDetail['Student']['program_id'], 
						'GradeScale.active' => 1
					),
					'recursive' => -1
				));
			}
		}
		//debug($gradeScale);
		return $gradeScale['GradeScale']['id'];
	}

	function getGradeScaleIdGivenPublishedCourse($publishedCourseId)
	{
		$getScale = ClassRegistry::init('PublishedCourse')->find('first', array(
			'conditions' => array(
				'PublishedCourse.id' => $publishedCourseId
			), 
			'contain' => array(
				'Program', 
				'Course', 
				'College',
				'Department'
			)
		));

		if (!empty($getScale)) {

			if (!empty($getScale['College']['deligate_for_graduate_study']) && $getScale['College']['deligate_for_graduate_study'] == 1 && $getScale['PublishedCourse']['program_id'] == PROGRAM_POST_GRADUATE ) {
				if (!empty($getScale['PublishedCourse']['grade_scale_id'])) {
					return $getScale['PublishedCourse']['grade_scale_id'];
				} else {
					$gradeScale = $this->find('first', array(
						'conditions' => array(
							'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
							'GradeScale.model' => 'Department', 
							'GradeScale.foreign_key' => $getScale['PublishedCourse']['department_id'], 
							'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
							'GradeScale.active' => 1
						),
						'recursive' => -1
					));
					return $gradeScale['GradeScale']['id'];
				}
			}

			if (!empty($getScale['College']['deligate_for_graduate_study']) && $getScale['College']['deligate_scale'] == 1 && $getScale['PublishedCourse']['program_id'] == PROGRAM_UNDEGRADUATE) {
				debug($getScale);
				if (!empty($getScale['PublishedCourse']['grade_scale_id'])) {
					return $getScale['PublishedCourse']['grade_scale_id'];
				} else {
					$gradeScale = $this->find('first', array(
						'conditions' => array(
							'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
							'GradeScale.model' => 'Department', 
							'GradeScale.foreign_key' => $getScale['PublishedCourse']['department_id'], 
							'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
							'GradeScale.active' => 1
						),
						'recursive' => -1
					));

					if (!empty($gradeScale)) {
						return $gradeScale['GradeScale']['id'];
					} else {
						$gradeScale = $this->find('first', array(
							'conditions' => array(
								'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
								'GradeScale.model' => 'College',
								'GradeScale.foreign_key' => $getScale['PublishedCourse']['college_id'], 
								'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
								'GradeScale.active' => 1
							),
							'recursive' => -1
						));
						return $gradeScale['GradeScale']['id'];
					}
				}
			}

			$gradeScale = $this->find('first', array(
				'conditions' => array(
					'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
					'GradeScale.model' => 'College',
					'GradeScale.foreign_key' => $getScale['Department']['college_id'], 
					'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
					'GradeScale.active' => 1
				),
				'recursive' => -1
			));

			if (!empty($gradeScale)) {
				return $gradeScale['GradeScale']['id'];
			} else {
				$gradeScale = $this->find('first', array(
					'conditions' => array(
						'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
						'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
						'GradeScale.active' => 1
					),
					'recursive' => -1, 
					'order' => 'GradeScale.created DESC'
				));

				if (!empty($gradeScale)) {
					return $gradeScale['GradeScale']['id'];
				}
			}

		} else {
			
			$gradeScale = $this->find('first', array(
				'conditions' => array(
					'GradeScale.grade_type_id' => $getScale['Course']['grade_type_id'], 
					'GradeScale.program_id' => $getScale['PublishedCourse']['program_id'], 
					'GradeScale.active' => 1
				),
				'recursive' => -1, 
				'order' => 'GradeScale.created DESC'
			));

			if (!empty($gradeScale)) {
				return $gradeScale['GradeScale']['id'];
			}
		}

		return 0;
	}
}
