<?php
App::uses('AppModel', 'Model');
class ColleagueEvalutionRate extends AppModel
{
	public $validate = array(
		'instructor_evalution_question_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'dept_head' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
		'rating' => array(
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

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'InstructorEvalutionQuestion' => array(
			'className' => 'InstructorEvalutionQuestion',
			'foreignKey' => 'instructor_evalution_question_id',
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
		),
		'Evaluator' => array(
			'className' => 'Staff',
			'foreignKey' => 'evaluator_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getNotEvaluatedColleaguesListForHead($data, $evaluator_user_id)
	{
		$evaluatorStaff = $this->Staff->find('first', array('conditions' => array('Staff.user_id' => $evaluator_user_id), 'recursive' => -1));

		if (empty($evaluator_user_id)) {
			return array();
		}

		if(isset($data['Search']['name']) && !empty($data['Search']['name'])){
			$staffs = $this->Staff->find('all', array(
				'conditions' => array(
					'Staff.first_name LIKE ' => (trim($data['Search']['name'])). '%',
					'Staff.id <> ' . $evaluatorStaff['Staff']['id'] . '',
					'Staff.active' => 1,
					'Staff.department_id' => $evaluatorStaff['Staff']['department_id'],
					'Staff.id in (select staff_id from course_instructor_assignments where evaluation_printed = 0 AND academic_year = "' . $data['Search']['acadamic_year'] . '" and semester = "' . $data['Search']['semester'] . '" GROUP BY staff_id, academic_year, semester)',
				),
				'contain' => array(
					'CourseInstructorAssignment' => array(
						'conditions' => array(
							'CourseInstructorAssignment.evaluation_printed' => 0,
							'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
							'CourseInstructorAssignment.semester' => $data['Search']['semester']
						),
						'order' => array('evaluation_printed' => 'DESC', 'academic_year' => 'DESC', 'semester' => 'DESC'),
						'limit' => 1
					),
					'ColleagueEvalutionRate' => array(
						'conditions' => array(
							'dept_head' => 0,
							'academic_year' => $data['Search']['acadamic_year'],
							'semester' => $data['Search']['semester'] 
							//'evaluator_id' =>  $evaluatorStaff['Staff']['id'],
						),
						'order' =>  array('dept_head' => 'DESC'),
						'limit' => 1
					),
					'Position' => array('id', 'position'), 
					'Title' => array('id', 'title'), 
					'Department'
				),
			));
		} else {
			$staffs = $this->Staff->find('all', array(
				'conditions' => array(
					'Staff.active' => 1,
					'Staff.id <> ' . $evaluatorStaff['Staff']['id'] . '',
					'Staff.department_id' => $evaluatorStaff['Staff']['department_id'],
					'Staff.id in (select staff_id from course_instructor_assignments where evaluation_printed = 0 AND academic_year = "' . $data['Search']['acadamic_year'] . '" AND semester = "' . $data['Search']['semester'] . '" GROUP BY staff_id, academic_year, semester)',
				),
				'contain' => array(
					'CourseInstructorAssignment' => array(
						'conditions' => array(
							'CourseInstructorAssignment.evaluation_printed' => 0,
							'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
							'CourseInstructorAssignment.semester' => $data['Search']['semester']
						),
						'order' => array('evaluation_printed' => 'DESC', 'academic_year' => 'DESC', 'semester' => 'DESC'),
						'limit' => 1
					),
					'ColleagueEvalutionRate' => array(
						'conditions' => array(
							'dept_head' => 0,
							'academic_year' => $data['Search']['acadamic_year'],
							'semester' => $data['Search']['semester'] 
							//'evaluator_id' =>  $evaluatorStaff['Staff']['id'],
						),
						'order' =>  array('dept_head' => 'DESC'),
						'limit' => 1
					), 
					'Position' => array('id', 'position'), 
					'Title' => array('id', 'title'),
					'Department'
				),
				
			));
			//debug($staffs);
		}

		$staffList = array();

		if (!empty($staffs)) {
			foreach ($staffs as $key => $value) {
				//debug($value);
				$checkIfEvaluated = $this->find('count', array(
					'conditions' => array(
						'staff_id' => $value['Staff']['id'],
						'academic_year' => $data['Search']['acadamic_year'],
						'semester' => $data['Search']['semester'],
						//'evaluator_id' => $evaluatorStaff['Staff']['id'],
						'dept_head' => 1,
					),
					'recursive' => -1
				));

				//debug($checkIfEvaluated);
				if ($checkIfEvaluated == 0 && !empty($value['ColleagueEvalutionRate'])) {
					$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position']. ')' . (empty($value['CourseInstructorAssignment']) ? ' - No Course Assignment' : '');
				}
			}
		}

		return $staffList;
	}

	public function getNotEvaluatedColleagues($data, $evaluator_user_id)
	{
		$evaluatorStaff = $this->Staff->find('first', array('conditions' => array('Staff.user_id' => $evaluator_user_id), 'recursive' => -1));

		if (empty($evaluator_user_id)) {
			return array();
		}
		//debug($data);

		$staffs = $this->Staff->find('all', array(
			'conditions' => array(
				'Staff.first_name LIKE ' => $data['Search']['name'] . '%',
				'Staff.id <> ' . $evaluatorStaff['Staff']['id'] . '',
				'Staff.active' => 1,
				'Staff.department_id' => $evaluatorStaff['Staff']['department_id'],
				'Staff.id in (select staff_id from course_instructor_assignments where evaluation_printed = 0 AND academic_year = "' . $data['Search']['acadamic_year'] . '" AND semester = "' . $data['Search']['semester'] . '" GROUP BY staff_id, academic_year, semester)',
				'Staff.id not in (select staff_id from colleague_evalution_rates where evaluator_id = "' . $evaluatorStaff['Staff']['id'] . '" AND academic_year = "' . $data['Search']['acadamic_year'] . '" AND semester = "' . $data['Search']['semester'] . '")',
			),
			'contain' => array(
				'CourseInstructorAssignment' => array(
					'conditions' => array(
						'CourseInstructorAssignment.evaluation_printed' => 0,
						'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
						'CourseInstructorAssignment.semester' => $data['Search']['semester']
					),
					'order' => array('evaluation_printed' => 'DESC', 'academic_year' => 'DESC', 'semester' => 'DESC'),
					'limit' => 1
				),
				'Position' => array('id', 'position'),
				'Title' => array('id', 'title'),
				'Department'
			)
		));

		$staffList = array();

		if (!empty($staffs)) {
			foreach ($staffs as $key => $value) {
				$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position'] . ')' . (empty($value['CourseInstructorAssignment']) ? ' - No Course Assignment' : '');
			}
		}

		return $staffList;
	}

	public function getEvaluatedColleaguesListForHeadReport($data, $evaluator_user_id)
	{

		$evaluatorStaff = $this->Staff->find('first', array('conditions' => array('Staff.user_id' => $evaluator_user_id, 'Staff.active' => 1), 'recursive' => -1));

		if (empty($evaluator_user_id)) {
			return array();
		}

		//debug($evaluator_user_id);

		$staffs = $this->Staff->find('all', array(
			'conditions' => array(
				'Staff.first_name LIKE ' => $data['Search']['name'] . '%',
				'Staff.active' => 1,
				'Staff.department_id' => $evaluatorStaff['Staff']['department_id'],
				'Staff.id  in (select staff_id from course_instructor_assignments where academic_year = "' . $data['Search']['acadamic_year'] . '" AND semester = "' . $data['Search']['semester'] . '" GROUP BY staff_id, academic_year, semester)',
				//'Staff.id in (select cia.staff_id from course_instructor_assignments cia JOIN student_evalution_rates ser ON cia.published_course_id = ser.published_course_id where cia.academic_year= "' . $data['Search']['acadamic_year'] . '" and cia.semester = "' . $data['Search']['semester'] . '" GROUP BY cia.academic_year, cia.semester, cia.staff_id, cia.published_course_id, ser.published_course_id)',
				//'Staff.id in (select cia.staff_id from course_instructor_assignments cia JOIN colleague_evalution_rates cer ON cer.dept_head = 1 AND cia.staff_id = cer.staff_id where cia.academic_year = "' . $data['Search']['acadamic_year'] . '" and cia.semester = "' . $data['Search']['semester'] . '" GROUP BY cia.academic_year, cia.semester, cia.staff_id, cia.published_course_id, cer.staff_id)',
			),
			'contain' => array(
				'CourseInstructorAssignment' => array(
					'conditions' => array(
						//'CourseInstructorAssignment.evaluation_printed' => 0,
						'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
						'CourseInstructorAssignment.semester' => $data['Search']['semester']
					),
					'order' => array('evaluation_printed' => 'DESC', 'academic_year' => 'DESC', 'semester' => 'DESC'),
					'limit' => 1
				),
				'Position' => array('id', 'position'),
				'ColleagueEvalutionRate' => array(
					'conditions' => array(
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 1
					),
					'limit' => 1
				),
				'Title' => array('id', 'title'), 
				'Department'
			)
		));

		$staffList = array();

		if (!empty($staffs)) {

			$head_department_id = $evaluatorStaff['Staff']['department_id'];

			debug($head_department_id);
			
			$totalCourseAssignedInstructors = ClassRegistry::init('CourseInstructorAssignment')->find('count', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where active = 1 and department_id = ' . $evaluatorStaff['Staff']['department_id']. ')',
					'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
					'CourseInstructorAssignment.semester' => $data['Search']['semester'],
				),
				'group' => array('CourseInstructorAssignment.staff_id')
			));

			debug($totalCourseAssignedInstructors);

			foreach ($staffs as $key => $value) {
				//filter out staffs who have been evaluated by the department head

				$checkIfEvaluatedByColleagues = $this->find('count', array(
					'conditions' => array(
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 0,
						'ColleagueEvalutionRate.staff_id' => $value['Staff']['id']
					),
					'group' => array('ColleagueEvalutionRate.evaluator_id', 'ColleagueEvalutionRate.academic_year', 'ColleagueEvalutionRate.semester'),
				));

				$published_course_ids = ClassRegistry::init('CourseInstructorAssignment')->find('list', array(
					'conditions' => array(
						'CourseInstructorAssignment.staff_id' => $value['Staff']['id'],
						'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
						'CourseInstructorAssignment.semester' => $data['Search']['semester'],
					),
					'fields' => array('CourseInstructorAssignment.published_course_id', 'CourseInstructorAssignment.published_course_id')
				));
				//debug($published_course_ids);

				$studentsEvaluated = ClassRegistry::init('StudentEvalutionRate')->find('count', array(
					'conditions' => array(
						'StudentEvalutionRate.published_course_id' => $published_course_ids,
					),
					'group' => array('StudentEvalutionRate.student_id')
				));

				debug($studentsEvaluated);

				$headEvaluaated = 0;

				debug($checkIfEvaluatedByColleagues);

				if ($checkIfEvaluatedByColleagues && !empty($value['ColleagueEvalutionRate']) && count($value['ColleagueEvalutionRate']) > 0) {
					$headEvaluaated = 1;
					$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position'] . ')~'.$studentsEvaluated.'~' . ($checkIfEvaluatedByColleagues . '/' . $totalCourseAssignedInstructors) . ' (' . round((( (int) $checkIfEvaluatedByColleagues/$totalCourseAssignedInstructors) * 100) , 2) . '%)~'. $headEvaluaated . '~1';
				} else if ($checkIfEvaluatedByColleagues && empty($value['ColleagueEvalutionRate'])) {
					$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position'] . ')~'.$studentsEvaluated.'~' . ($checkIfEvaluatedByColleagues . '/' . $totalCourseAssignedInstructors) . ' (' . round((( (int) $checkIfEvaluatedByColleagues/$totalCourseAssignedInstructors) * 100) , 2) . '%)~'. $headEvaluaated . '~0';
				} else if ($checkIfEvaluatedByColleagues) {
					$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position'] . ')~'.$studentsEvaluated.'~' . ($checkIfEvaluatedByColleagues . '/' . $totalCourseAssignedInstructors) . ' (' . round((( (int) $checkIfEvaluatedByColleagues/$totalCourseAssignedInstructors) * 100) , 2) . '%)~'. $headEvaluaated . '~0';
				} else {
					$staffList[$value['Staff']['id']] = $value['Title']['title'] . '. ' . $value['Staff']['full_name'] . ' (' . $value['Position']['position'] . ')~'.$studentsEvaluated.'~' . ('0/' . $totalCourseAssignedInstructors) . '(0%)~'. $headEvaluaated . '~0';
				}
			}
		}

		debug($staffList);

		return $staffList;
	}

	public function getInstructorEvaluationResult($data, $department_id)
	{
		debug($department_id);
		$department_ids = '';

		if (is_array($department_id)) {
			$department_ids = join(',', $department_id);
		} else {
			$department_ids = $department_id;
		}

		$staff_ids = array();

		if (count($data['Staff']) > 0) {
			foreach ($data['Staff'] as $kk => $vv) {
				if ($vv['gp'] == 1) {
					$staff_ids[$vv['id']] = $vv['id'];
				}
			}
		}

		$courseInstructorAssignments = array();

		if (!empty($staff_ids)) {
			$courseInstructorAssignments = $this->Staff->CourseInstructorAssignment->find('all', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id' => $staff_ids,
					'CourseInstructorAssignment.academic_year' => $data['Search']['acadamic_year'],
					'CourseInstructorAssignment.semester' => $data['Search']['semester'],
					'CourseInstructorAssignment.staff_id in (select id from staffs where department_id in ("' . $department_ids . '") )'
				),
				'contain' => array(
					'Staff' => array('Position', 'Title', 'Department', 'College'), 
					'PublishedCourse' => array('Course', 'Section', 'YearLevel')
				)
			));
		}


		if (is_numeric(MAXIMUM_STAFF_EVALUATION_RATE) && MAXIMUM_STAFF_EVALUATION_RATE > 0) {
			$maxEvaluationRate = MAXIMUM_STAFF_EVALUATION_RATE;
		} else {
			$maxEvaluationRate = 5;
		}

		$readEvaluationSettings = ClassRegistry::init('InstructorEvalutionSetting')->find('first', array('order' => array('InstructorEvalutionSetting.academic_year' => 'DESC')));

		debug($readEvaluationSettings);

		$evalutionResult = array();

		if (!empty($courseInstructorAssignments)) {

			foreach ($courseInstructorAssignments as $key => $value) {
				
				$totalObjectiveStudentQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->totalObjectiveStudentQuestion($data['Search']['acadamic_year'], $data['Search']['semester']);
				
				$totalEvaluterStudents = ClassRegistry::init('StudentEvalutionRate')->find('count', array(
					'conditions' => array(
						'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
						'StudentEvalutionRate.rating != 0'
					),
					'group' => array('StudentEvalutionRate.student_id')
				));

				//student evaluation
				if ($totalEvaluterStudents) {

					$maximumSumPossibleForInstructor = $totalEvaluterStudents * $totalObjectiveStudentQuestion * $maxEvaluationRate;

					debug('Maximum Possible point for instructor: '. $maximumSumPossibleForInstructor);
					debug('Total Objective Questions for Students: '. $totalObjectiveStudentQuestion);
					debug('Total evaluated Students: '. $totalEvaluterStudents);

					$allStudentEvaluation = ClassRegistry::init('StudentEvalutionRate')->find('all', array(
						'conditions' => array(
							'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
							'StudentEvalutionRate.rating != 0',
							//'StudentEvalutionRate.instructor_evalution_question_id is not null'
						),
						'limit' => $totalEvaluterStudents * $totalObjectiveStudentQuestion * 50, //maximum class size 50, incase there are too many dublicates, Neway
						'recursive' => -1
					));

					debug(count($allStudentEvaluation));


					if (!empty($allStudentEvaluation)) {

						foreach ($allStudentEvaluation as $rd => $rv) {
							//remove duplicate evaluation result for same question of student evaluation if exists
							$allDuplicatedList = ClassRegistry::init('StudentEvalutionRate')->find('list', array(
								'conditions' => array(
									'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
									'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
									'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
								), 
								'fields' => array('StudentEvalutionRate.id', 'StudentEvalutionRate.id')
							));

							if (count($allDuplicatedList) > 1) {
								// perform deletion except one instance
								$firstInstance = ClassRegistry::init('StudentEvalutionRate')->find('first', array(
									'conditions' => array(
										'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
										'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
										'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
									),
									'order' => array('StudentEvalutionRate.id' => 'ASC'),
									'recursive' => -1
								));

								unset($allDuplicatedList[$firstInstance['StudentEvalutionRate']['id']]);

								if (count($allDuplicatedList)) {
									debug($allDuplicatedList);
									ClassRegistry::init('StudentEvalutionRate')->deleteAll(array('StudentEvalutionRate.id' => $allDuplicatedList), false, false);
								}
							}
						}
					}

					$sum = ClassRegistry::init('StudentEvalutionRate')->find('all', array(
						'conditions' => array(
							'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
							'StudentEvalutionRate.rating != 0'
						),
						'fields' => array('sum(StudentEvalutionRate.rating)'),
					));

					debug($sum);

					if (!empty($value['PublishedCourse']['Course']['course_title'])) {
						$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Student'][$value['PublishedCourse']['Course']['course_title'] . '~' . $value['PublishedCourse']['Section']['name'] . '~' . $value['PublishedCourse']['id']]['studentTotalRate'] = $sum[0][0]['sum(`StudentEvalutionRate`.`rating`)'];
						$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Student'][$value['PublishedCourse']['Course']['course_title'] . '~' . $value['PublishedCourse']['Section']['name'] . '~' . $value['PublishedCourse']['id']]['totalEvaluterStudents'] = $totalEvaluterStudents;
						$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Student'][$value['PublishedCourse']['Course']['course_title'] . '~' . $value['PublishedCourse']['Section']['name'] . '~' . $value['PublishedCourse']['id']]['averageRate'] = ($sum[0][0]['sum(`StudentEvalutionRate`.`rating`)'] / $totalEvaluterStudents);
						$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Student'][$value['PublishedCourse']['Course']['course_title'] . '~' . $value['PublishedCourse']['Section']['name'] . '~' . $value['PublishedCourse']['id']]['rateconverted5percent'] = (($maxEvaluationRate * $sum[0][0]['sum(`StudentEvalutionRate`.`rating`)']) / ($totalObjectiveStudentQuestion * $maxEvaluationRate * $totalEvaluterStudents));
					}
				}

				debug($evalutionResult);

				// Colleague Evaluations
				$totalStaffEvalutedInstructor = ClassRegistry::init('ColleagueEvalutionRate')->find('count', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 0
					),
					'group' => array('ColleagueEvalutionRate.evaluator_id')
				));

				//remove duplicate entry of evaluation of staffs and department head

				$allcolleagueEvalution = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						//'ColleagueEvalutionRate.dept_head' => 0
					),
					'contain' => array('InstructorEvalutionQuestion')
				));

				if (!empty($allcolleagueEvalution)) {

					foreach ($allcolleagueEvalution as $key => $value2) {

						$allDuplicatedList = $this->find('list', array(
							'conditions' => array(
								'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
								'ColleagueEvalutionRate.evaluator_id' => $value2['ColleagueEvalutionRate']['evaluator_id'],
								'ColleagueEvalutionRate.academic_year' => $value2['ColleagueEvalutionRate']['academic_year'],
								'ColleagueEvalutionRate.semester' => $value2['ColleagueEvalutionRate']['semester'],
								'ColleagueEvalutionRate.instructor_evalution_question_id' => $value2['ColleagueEvalutionRate']['instructor_evalution_question_id'],
								//'ColleagueEvalutionRate.dept_head' => 0
							),
							'fields' => array('ColleagueEvalutionRate.id', 'ColleagueEvalutionRate.id')
						));

						if (count($allDuplicatedList) > 1) {
							//debug($allDuplicatedList);
							// perform deletion except one instance
							$firstInstance = $this->find('first', array(
								'conditions' => array(
									'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
									'ColleagueEvalutionRate.evaluator_id' => $value2['ColleagueEvalutionRate']['evaluator_id'],
									'ColleagueEvalutionRate.academic_year' => $value2['ColleagueEvalutionRate']['academic_year'],
									'ColleagueEvalutionRate.semester' => $value2['ColleagueEvalutionRate']['semester'],
									'ColleagueEvalutionRate.instructor_evalution_question_id' => $value2['ColleagueEvalutionRate']['instructor_evalution_question_id'],
									//'ColleagueEvalutionRate.dept_head' => 0
								),
								'order' => array('ColleagueEvalutionRate.id' => 'ASC'),
								'recursive' => -1
							));

							unset($allDuplicatedList[$firstInstance['ColleagueEvalutionRate']['id']]);

							if (count($allDuplicatedList)) {
								$this->deleteAll(array('ColleagueEvalutionRate.id' => $allDuplicatedList), false, false);
							}
						}
					}
				}

				//colleague evaluation

				$colleagueEvalution = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 0
					),
					//'group' => array('ColleagueEvalutionRate.evaluator_id')
					'contain' => array('InstructorEvalutionQuestion')
				));

				$sumColleagueEvaluation = 0;
				$totalObjectiveColleagueQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->totalObjectiveColleagueQuestion($data['Search']['acadamic_year'], $data['Search']['semester'], $value['CourseInstructorAssignment']['staff_id']);
				$totalObjectiveQuestionArr = array();
				$sumColleagueEvaluationActive = 0;
				$sumColleagueEvaluationDeactivate = 0;

				if (!empty($colleagueEvalution)) {
					foreach ($colleagueEvalution as $key => $value2) {

						/* $colleagueEvalutionSum = $this->find('all', array(
							'conditions' => array(
								'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
								'ColleagueEvalutionRate.evaluator_id' => $value2['ColleagueEvalutionRate']['evaluator_id'],
								'ColleagueEvalutionRate.academic_year' => $value2['ColleagueEvalutionRate']['academic_year'],
								'ColleagueEvalutionRate.semester' => $value2['ColleagueEvalutionRate']['semester'],
								'ColleagueEvalutionRate.dept_head' => 0
							),
							'fields' => array('sum(ColleagueEvalutionRate.rating)'),
						)); */
						
						if ($value2['InstructorEvalutionQuestion']['active'] == 0) {
							$sumColleagueEvaluationDeactivate += $value2['ColleagueEvalutionRate']['rating'];
						} else if ($value2['InstructorEvalutionQuestion']['active'] == 1) {
							$sumColleagueEvaluationActive += $value2['ColleagueEvalutionRate']['rating'];
						}

						$sumColleagueEvaluation += $value2['ColleagueEvalutionRate']['rating'];
						$totalObjectiveQuestionArr[$value2['InstructorEvalutionQuestion']['active']][$value2['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $value2['ColleagueEvalutionRate']['instructor_evalution_question_id'];
					}
				}

				if ($sumColleagueEvaluationDeactivate >= $sumColleagueEvaluationActive) {
					$totalObjectiveColleagueQuestion =  (isset($totalObjectiveQuestionArr[0]) ? count($totalObjectiveQuestionArr[0]) : $totalObjectiveColleagueQuestion);
					$sumColleagueEvaluation = $sumColleagueEvaluationDeactivate;
				} else if ($sumColleagueEvaluationActive >= $sumColleagueEvaluationDeactivate) {
					$totalObjectiveColleagueQuestion = (isset($totalObjectiveQuestionArr[1]) ? count($totalObjectiveQuestionArr[1]) : $totalObjectiveColleagueQuestion);
					$sumColleagueEvaluation = $sumColleagueEvaluationActive;
				}

				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Colleague']['colleagueTotalRate'] = $sumColleagueEvaluation;
				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Colleague']['averageRate'] = ($totalStaffEvalutedInstructor > 0 ? ($evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Colleague']['colleagueTotalRate'] / $totalStaffEvalutedInstructor) : 0);
				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Colleague']['totalEvaluterStaff'] = $totalStaffEvalutedInstructor;
				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Colleague']['rateconverted5percent'] = ($totalStaffEvalutedInstructor > 0 && $totalObjectiveColleagueQuestion > 0 ? (($sumColleagueEvaluation * $maxEvaluationRate) / ($totalObjectiveColleagueQuestion * $totalStaffEvalutedInstructor * $maxEvaluationRate)) : 0);


				/* $headSum = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 1
					),
					'fields' => array('sum(ColleagueEvalutionRate.rating)'),
				)); */

				/* $headEv = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 1
					),
					//'fields'=>array('sum(ColleagueEvalutionRate.rating)'),
					'contain' => array('InstructorEvalutionQuestion')
				));

				//debug($headEv);
				//remove duplicate entry of evaluation of staffs

				if (!empty($headEv)) {

					foreach ($headEv as $hs => $hv) {

						$allDuplicatedList = $this->find('list', array(
							'conditions' => array(
								'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
								'ColleagueEvalutionRate.evaluator_id' => $hv['ColleagueEvalutionRate']['evaluator_id'],
								'ColleagueEvalutionRate.academic_year' => $hv['ColleagueEvalutionRate']['academic_year'],
								'ColleagueEvalutionRate.semester' => $hv['ColleagueEvalutionRate']['semester'],
								'ColleagueEvalutionRate.instructor_evalution_question_id' => $hv['ColleagueEvalutionRate']['instructor_evalution_question_id'], 				'ColleagueEvalutionRate.dept_head' => 1
							),
							'fields' => array('ColleagueEvalutionRate.id', 'ColleagueEvalutionRate.id')

						));

						if (count($allDuplicatedList) > 1) {
							$firstInstance = $this->find('first', array(
								'conditions' => array(
									'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
									'ColleagueEvalutionRate.evaluator_id' => $hv['ColleagueEvalutionRate']['evaluator_id'],
									'ColleagueEvalutionRate.academic_year' => $hv['ColleagueEvalutionRate']['academic_year'],
									'ColleagueEvalutionRate.semester' => $hv['ColleagueEvalutionRate']['semester'],
									'ColleagueEvalutionRate.instructor_evalution_question_id' => $hv['ColleagueEvalutionRate']['instructor_evalution_question_id'],
									'ColleagueEvalutionRate.dept_head' => 1
								),
								'recursive' => -1
							));

							unset($allDuplicatedList[$firstInstance['ColleagueEvalutionRate']['id']]);

							if (count($allDuplicatedList)) {
								$this->deleteAll(array('ColleagueEvalutionRate.id' => $allDuplicatedList), false,false);
							}
						}
					}
				} */


				// Head Evaluation

				$headEv = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $value['CourseInstructorAssignment']['staff_id'],
						'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
						'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
						'ColleagueEvalutionRate.dept_head' => 1
					),
					//'fields' => array('sum(ColleagueEvalutionRate.rating)'),
					'contain' => array('InstructorEvalutionQuestion')
				));

				//debug($headEv);

				$totalObjectiveHeadQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->totalObjectiveHeadQuestion($data['Search']['acadamic_year'], $data['Search']['semester'], $value['CourseInstructorAssignment']['staff_id']);
				$totalObjectiveQuestionHeadArr = array();
				$headSum = 0;
				$headSumDeactivate = 0;
				$headSumActive = 0;

				debug($totalObjectiveHeadQuestion);

				if (!empty($headEv)) {
					foreach ($headEv as $hs => $hv) {

						$headSum += $hv['ColleagueEvalutionRate']['rating'];

						if ($hv['InstructorEvalutionQuestion']['active'] == 0) {
							$headSumDeactivate += $hv['ColleagueEvalutionRate']['rating'];
						} else if ($hv['InstructorEvalutionQuestion']['active'] == 1) {
							$headSumActive += $hv['ColleagueEvalutionRate']['rating'];
						}
						
						$totalObjectiveQuestionHeadArr[$hv['InstructorEvalutionQuestion']['active']][$hv['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $hv['ColleagueEvalutionRate']['instructor_evalution_question_id'];
					}
				}

				debug($headSumDeactivate);
				debug($headSumActive);
				debug($totalObjectiveHeadQuestion);
				debug($totalObjectiveQuestionHeadArr);

				if ($headSumDeactivate >= $headSumActive) {
					$totalObjectiveHeadQuestion = (isset($totalObjectiveQuestionHeadArr[0]) ? count($totalObjectiveQuestionHeadArr[0]) : $totalObjectiveHeadQuestion);
					$headSum = $headSumDeactivate;
				} else if ($headSumActive >= $headSumDeactivate) {
					$totalObjectiveHeadQuestion = (isset($totalObjectiveQuestionHeadArr[1]) ? count($totalObjectiveQuestionHeadArr[1]) : $totalObjectiveHeadQuestion);
					$headSum = $headSumActive;
				}

				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['EvaluatedStaffDetail'] = $this->Staff->find('first', array(
					'conditions' => array(
						'Staff.id' => $value['CourseInstructorAssignment']['staff_id']
					),
					'contain' => array('Position', 'Title', 'Department', 'College')
				));

				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['EvaluatedStaffDetail']['academic_year'] = $data['Search']['acadamic_year'];
				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['EvaluatedStaffDetail']['semester'] = $data['Search']['semester'];

				if ($totalObjectiveHeadQuestion > 0) {
					$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Head'][0]['headTotalRate'] = $headSum;
					$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['Head'][0]['rateconverted5percent'] = (($maxEvaluationRate * $headSum) / ($totalObjectiveHeadQuestion * $maxEvaluationRate));
				}

				$evalutionResult[$value['CourseInstructorAssignment']['staff_id']]['InstructorEvalutionSetting'] = $readEvaluationSettings['InstructorEvalutionSetting'];
			}
		}

		//debug($evalutionResult);

		return $evalutionResult;


		/* $colleagueEvalution = $this->find('all', array(
			'conditions' => array(
				'ColleagueEvalutionRate.staff_id' => $data['Search']['staff_id'],
				'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
				'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
				'ColleagueEvalutionRate.dept_head' => 0
			),
			//'fields'=>array('sum(ColleagueEvalutionRate.rating)'),
			'group' => array('ColleagueEvalutionRate.evaluator_id')
		));

		$totalStaffEvalutedInstructor = ClassRegistry::init('ColleagueEvalutionRate')->find('count', array(
			'conditions' => array(
				'ColleagueEvalutionRate.staff_id' => $data['Search']['staff_id'],
				'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
				'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
				'ColleagueEvalutionRate.dept_head' => 0
			),
			'group' => array('ColleagueEvalutionRate.evaluator_id')
		));

		foreach ($colleagueEvalution as $key => $value) {

			$colleagueEvalutionSum = $this->find('all', array(
				'conditions' => array(
					'ColleagueEvalutionRate.staff_id' => $value['ColleagueEvalutionRate']['staff_id'],
					'ColleagueEvalutionRate.evaluator_id' => $value['ColleagueEvalutionRate']['evaluator_id'],
					'ColleagueEvalutionRate.academic_year' => $value['ColleagueEvalutionRate']['academic_year'],
					'ColleagueEvalutionRate.semester' => $value['ColleagueEvalutionRate']['semester'],
					'ColleagueEvalutionRate.dept_head' => 0
				),
				'fields' => array('sum(ColleagueEvalutionRate.rating)'),
			));

			$evalutionResult['Colleague']['colleagueTotalRate'] += $colleagueEvalutionSum[0][0]['sum(`ColleagueEvalutionRate`.`rating`)'];

		}

		$evalutionResult['Colleague']['averageRate'] = ($evalutionResult['Colleague']['colleagueTotalRate'] / $totalStaffEvalutedInstructor);
		$evalutionResult['Colleague']['rateconverted5percent'] = (5 * $evalutionResult['Colleague']['colleagueTotalRate']) / ($totalObjectiveColleagueQuestion * 5 * $totalStaffEvalutedInstructor);

		$headSum = $this->find('all', array(
			'conditions' => array(
				'ColleagueEvalutionRate.staff_id' => $data['Search']['staff_id'],
				'ColleagueEvalutionRate.academic_year' => $data['Search']['acadamic_year'],
				'ColleagueEvalutionRate.semester' => $data['Search']['semester'],
				'ColleagueEvalutionRate.dept_head' => 1
			),
			'fields' => array('sum(ColleagueEvalutionRate.rating)'),
		));

		$evalutionResult['EvaluatedStaffDetail'] = $this->Staff->find('first', array('conditions' => array('Staff.id' => $data['Search']['staff_id']), 'contain' => array('Position', 'Title', 'Department', 'College')));

		$evalutionResult['EvaluatedStaffDetail']['academic_year'] = $data['Search']['acadamic_year'];
		$evalutionResult['EvaluatedStaffDetail']['semester'] = $data['Search']['semester'];
		$evalutionResult['Head'][0]['headTotalRate'] = $headSum[0][0]['sum(`ColleagueEvalutionRate`.`rating`)'];
		$evalutionResult['Head'][0]['rateconverted5percent'] = (5 * $headSum[0][0]['sum(`ColleagueEvalutionRate`.`rating`)']) / ($totalObjectiveHeadQuestion * 5);
		$evalutionResult['InstructorEvalutionSetting'] = $readEvaluationSettings['InstructorEvalutionSetting']; */
       
	}
	
	public function remove_duplicate_staff_evaluation($department_id = "All", $academic_year, $semester)
	{
		if (strcasecmp($department_id, "All") == 0) {
			$staffs = $this->Staff->find('all', array(
				'conditions' => array('Staff.user_id in (select id from users where role_id=2)'),
				'recursive' => -1
			));
		} else {
			$staffs = $this->Staff->find('all', array(
				'conditions' => array(
					'Staff.user_id in (select id from users where role_id=2)',
					'Staff.department_id' => $department_id
				),
				'recursive' => -1
			));
		}


		if (!empty($staffs)) {

			foreach ($staffs as $sk => $sv) {

				$allcolleagueEvalution = $this->find('all', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $sv['Staff']['id'],
						'ColleagueEvalutionRate.academic_year' => $academic_year,
						'ColleagueEvalutionRate.semester' => $semester,
						//'ColleagueEvalutionRate.dept_head' => 0
					),
					'contain' => array('InstructorEvalutionQuestion')
				));

				$totalStaffEvalutedInstructor = ClassRegistry::init('ColleagueEvalutionRate')->find('count', array(
					'conditions' => array(
						'ColleagueEvalutionRate.staff_id' => $sv['Staff']['id'],
						'ColleagueEvalutionRate.academic_year' => $academic_year,
						'ColleagueEvalutionRate.semester' => $semester,
						'ColleagueEvalutionRate.dept_head' => 0
					),
					'group' => array('ColleagueEvalutionRate.evaluator_id')
				));

				//remove duplicate entry of evaluation of staffs
				if (!empty($allcolleagueEvalution)) {

					foreach ($allcolleagueEvalution as $key => $value2) {

						$allDuplicatedList = $this->find('list', array(
							'conditions' => array(
								'ColleagueEvalutionRate.staff_id' => $sv['Staff']['id'],
								'ColleagueEvalutionRate.evaluator_id' => $value2['ColleagueEvalutionRate']['evaluator_id'],
								'ColleagueEvalutionRate.academic_year' => $value2['ColleagueEvalutionRate']['academic_year'],
								'ColleagueEvalutionRate.semester' => $value2['ColleagueEvalutionRate']['semester'],
								'ColleagueEvalutionRate.instructor_evalution_question_id' => $value2['ColleagueEvalutionRate']['instructor_evalution_question_id'],
								//'ColleagueEvalutionRate.dept_head' => 0
							), 
							'fields' => array('ColleagueEvalutionRate.id', 'ColleagueEvalutionRate.id')
						));

						debug(count($allDuplicatedList));

						if (count($allDuplicatedList) > 1) {
							// perform deletion except one instance
							$firstInstance = $this->find('first', array(
								'conditions' => array(
									'ColleagueEvalutionRate.staff_id' => $sv['Staff']['id'],
									'ColleagueEvalutionRate.evaluator_id' => $value2['ColleagueEvalutionRate']['evaluator_id'],
									'ColleagueEvalutionRate.academic_year' => $value2['ColleagueEvalutionRate']['academic_year'],
									'ColleagueEvalutionRate.semester' => $value2['ColleagueEvalutionRate']['semester'],
									'ColleagueEvalutionRate.instructor_evalution_question_id' => $value2['ColleagueEvalutionRate']['instructor_evalution_question_id'],
									//'ColleagueEvalutionRate.dept_head' => 0
								),
								'recursive' => -1
							));

							debug($allDuplicatedList[$firstInstance['ColleagueEvalutionRate']['id']]);
							unset($allDuplicatedList[$firstInstance['ColleagueEvalutionRate']['id']]);

							if (count($allDuplicatedList)) {
								//debug($allDuplicatedList);
								//die;
								$this->deleteAll(array('ColleagueEvalutionRate.id' => $allDuplicatedList ), false, false );
							}
						}
					}
				}
			}
		}
	}

	public function remove_duplicate_student_evaluation($department_id = "All", $academic_year, $semester)
	{

		if (strcasecmp($department_id, "All") == 0) {
			$courseInstructorAssignments = $this->Staff->CourseInstructorAssignment->find('all', array(
				'conditions' => array(
					'CourseInstructorAssignment.academic_year' => $academic_year,
					'CourseInstructorAssignment.semester' => $semester,
					'CourseInstructorAssignment.isprimary' => 1,
				), 
				'contain' => array(
					'Staff' => array('Position', 'Title', 'Department', 'College'), 
					'PublishedCourse' => array('Course', 'Section', 'YearLevel')
				)
			));
		} else {
			$courseInstructorAssignments = $this->Staff->CourseInstructorAssignment->find('all', array(
				'conditions' => array(
					'CourseInstructorAssignment.academic_year' => $academic_year,
					'CourseInstructorAssignment.semester' => $semester,
					'CourseInstructorAssignment.isprimary' => 1,
					'CourseInstructorAssignment.staff_id in (select id from staffs where department_id in ("' . $department_id . '") )'
				), 
				'contain' => array(
					'Staff' => array('Position', 'Title', 'Department', 'College'), 
					'PublishedCourse' => array('Course', 'Section', 'YearLevel')
				)
			));
		}

		if (is_numeric(MAXIMUM_STAFF_EVALUATION_RATE) && MAXIMUM_STAFF_EVALUATION_RATE > 0) {
			$maxEvaluationRate = MAXIMUM_STAFF_EVALUATION_RATE;
		} else {
			$maxEvaluationRate = 5;
		}

		if (!empty($courseInstructorAssignments)) {
			
			foreach ($courseInstructorAssignments as $key => $value) {

				$totalObjectiveStudentQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->totalObjectiveStudentQuestion($academic_year, $semester);

				$totalEvaluterStudents = ClassRegistry::init('StudentEvalutionRate')->find('count', array(
					'conditions' => array(
						'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
						'StudentEvalutionRate.rating != 0'
					),
					'group' => array('StudentEvalutionRate.student_id')
				));

				//student evaluation
				if ($totalEvaluterStudents) {
					$maximumSumPossibleForInstructor = $totalEvaluterStudents * $totalObjectiveStudentQuestion * $maxEvaluationRate;
					
					$allStudentEvaluation = ClassRegistry::init('StudentEvalutionRate')->find('all', array(
						'conditions' => array(
							'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
							'StudentEvalutionRate.rating !=0'
						),
						'limit' => $totalEvaluterStudents * $totalObjectiveStudentQuestion * 50,
						'recursive' => -1
					));

					//remove duplicate evaluation result for same question of student evaluation if exists
					if (!empty($allStudentEvaluation)) {

						foreach ($allStudentEvaluation as $rd => $rv) {

							$allDuplicatedList = ClassRegistry::init('StudentEvalutionRate')->find('list', array(
								'conditions' => array(
									'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
									'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
									'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
								),
								'fields' => array('StudentEvalutionRate.id', 'StudentEvalutionRate.id')
							));

							debug(count($allDuplicatedList));

							if (count($allDuplicatedList) > 1) {
								// perform deletion except one instance
								$firstInstance = ClassRegistry::init('StudentEvalutionRate')->find('first', array(
									'conditions' => array(
										'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
										'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
										'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
									),
									'recursive' => -1
								));

								debug($allDuplicatedList[$firstInstance['StudentEvalutionRate']['id']]);
								unset($allDuplicatedList[$firstInstance['StudentEvalutionRate']['id']]);

								if (count($allDuplicatedList)) {
									ClassRegistry::init('StudentEvalutionRate')->deleteAll(array('StudentEvalutionRate.id' => $allDuplicatedList), false, false);
								}
							}
						}
					}
				}
			}
		}
	}

	public function check_student_evaluation_for_staff_and_fix_errors($academic_year, $semester, $staff_id = null)
	{

		$courseInstructorAssignments = array();
		$totalCoursesThought = 0;
		$totalduplicatedEntries = 0;
		$totalduplicatedEntriesDeleted = 0;
		$totalStudentsEvaluatedForAllAssignedCourses = 0;

		if (is_numeric(MAXIMUM_STAFF_EVALUATION_RATE) && MAXIMUM_STAFF_EVALUATION_RATE > 0) {
			$maxEvaluationRate = MAXIMUM_STAFF_EVALUATION_RATE;
		} else {
			$maxEvaluationRate = 5;
		}
		

		if (!isset($staff_id)) {
			return array();
		} else {
			$courseInstructorAssignments = $this->Staff->CourseInstructorAssignment->find('all', array(
				'conditions' => array(
					'CourseInstructorAssignment.academic_year' => $academic_year,
					'CourseInstructorAssignment.semester' => $semester,
					'CourseInstructorAssignment.isprimary' => 1,
					'CourseInstructorAssignment.staff_id' => $staff_id
				), 
				'contain' => array(
					'Staff' => array('Position', 'Title', 'Department', 'College'), 
					'PublishedCourse' => array('Course', 'Section', 'YearLevel')
				)
			));
		}

		if (!empty($courseInstructorAssignments)) {

			foreach ($courseInstructorAssignments as $key => $value) {
				
				$totalCoursesThought ++;

				$totalObjectiveStudentQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->totalObjectiveStudentQuestion($academic_year, $semester);

				$totalEvaluterStudents = ClassRegistry::init('StudentEvalutionRate')->find('count', array(
					'conditions' => array(
						'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
						'StudentEvalutionRate.rating !=0'
					),
					'group' => array('StudentEvalutionRate.student_id')
				));

				$totalStudentsEvaluatedForAllAssignedCourses += $totalEvaluterStudents;

				//student evaluation
				if ($totalEvaluterStudents) {

					$maximumSumPossibleForInstructor = $totalEvaluterStudents * $totalObjectiveStudentQuestion * $maxEvaluationRate;

					$allStudentEvaluation = ClassRegistry::init('StudentEvalutionRate')->find('all', array(
						'conditions' => array(
							'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
							'StudentEvalutionRate.rating !=0'
						),
						'limit' => $totalEvaluterStudents * $totalObjectiveStudentQuestion * 50,
						'recursive' => -1
					));

					//remove duplicate evaluation result for same question of student evaluation if exists

					if (!empty($allStudentEvaluation)) {

						foreach ($allStudentEvaluation as $rd => $rv) {

							$allDuplicatedList = ClassRegistry::init('StudentEvalutionRate')->find('list', array(
								'conditions' => array(
									'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
									'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
									'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
								),
								'fields' => array('StudentEvalutionRate.id', 'StudentEvalutionRate.id')
							));

							//debug(count($allDuplicatedList));

							if (count($allDuplicatedList) > 1) {

								$totalduplicatedEntries += count($allDuplicatedList);

								// perform deletion except one instance
								$firstInstance = ClassRegistry::init('StudentEvalutionRate')->find('first', array(
									'conditions' => array(
										'StudentEvalutionRate.published_course_id' => $value['CourseInstructorAssignment']['published_course_id'],
										'StudentEvalutionRate.student_id' => $rv['StudentEvalutionRate']['student_id'],
										'StudentEvalutionRate.instructor_evalution_question_id' => $rv['StudentEvalutionRate']['instructor_evalution_question_id']
									),
									'recursive' => -1
								));

								debug($allDuplicatedList[$firstInstance['StudentEvalutionRate']['id']]);

								unset($allDuplicatedList[$firstInstance['StudentEvalutionRate']['id']]);

								if (count($allDuplicatedList)) {
									$totalduplicatedEntriesDeleted += count($allDuplicatedList);
									ClassRegistry::init('StudentEvalutionRate')->deleteAll(array('StudentEvalutionRate.id' => $allDuplicatedList), false, false);
								}
							}
						}
					}
				}
			}
		}
	}
}
