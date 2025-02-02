<?php
class ClassRoom extends AppModel {
	var $name = 'ClassRoom';
	var $validate = array(
		'room_code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Room code should not be empty, Please provide valid room code.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				
			),
			'unique'=>array(
				 'rule'=>array('checkUnique','room_code'),
				 'message'=>'You have already entered room code. Please provided
				 unique name.'
			)
		),
		'lecture_capacity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Lecture capacity should only numeric. Please Provide class room lecture capacity in number.',
				'allowEmpty' => true,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'exam_capacity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Exam capacity should only numeric. Please Provide class room Exam capacity in number.',
				'allowEmpty' => true,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	function checkUnique ($data, $fieldName) {
			$valid=true;
			//debug($this);
			if(!isset($this->data['ClassRoom']['id'])){
			if(isset($fieldName) && $this->hasField($fieldName)) {
					$class_room_block_data=$this->ClassRoomBlock->send_class_room_block_data();
					$class_room_block_id=$this->ClassRoomBlock->find('first',array('conditions'=>array('ClassRoomBlock.campus_id'=>$class_room_block_data['ClassRoomBlock']['campus_id'],'ClassRoomBlock.college_id'=>$class_room_block_data['ClassRoomBlock']['college_id'],
					'ClassRoomBlock.block_code'=>$class_room_block_data['ClassRoomBlock']['block_code']),
					'recursive'=>-1));
				   if(!empty($class_room_block_id['ClassRoomBlock']['id'])) {
					$check=$this->find('count',array('conditions'=>array('ClassRoom.class_room_block_id'=>$class_room_block_id['ClassRoomBlock']['id'],'ClassRoom.room_code'=>$data['room_code'])));
				   }

					if($check>0) {
						$valid=false;
				    }
				    
			}
			}
			return $valid;
	}
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ClassRoomBlock' => array(
			'className' => 'ClassRoomBlock',
			'foreignKey' => 'class_room_block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
  
	var $hasMany = array(
		'ProgramProgramTypeClassRoom' => array(
			'className' => 'ProgramProgramTypeClassRoom',
			'foreignKey' => 'class_room_id',
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
		'ExamSchedule'=>array(
		    'className' => 'ExamSchedule',
			'foreignKey' => 'class_room_id',
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
		'ClassRoomClassPeriodConstraint' => array(
			'className' => 'ClassRoomClassPeriodConstraint',
			'foreignKey' => 'class_room_id',
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
		'ClassRoomCourseConstraint' => array(
			'className' => 'ClassRoomCourseConstraint',
			'foreignKey' => 'class_room_id',
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
		'ExamRoomConstraint' => array(
			'className' => 'ExamRoomConstraint',
			'foreignKey' => 'class_room_id',
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
		'ExamRoomCourseConstraint' => array(
			'className' => 'ExamRoomCourseConstraint',
			'foreignKey' => 'class_room_id',
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
		'ExamRoomNumberOfInvigilator' => array(
			'className' => 'ExamRoomNumberOfInvigilator',
			'foreignKey' => 'class_room_id',
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
		'CourseSchedule' => array(
			'className' => 'CourseSchedule',
			'foreignKey' => 'class_room_id',
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
		'ExamSchedule' => array(
			'className' => 'ExamSchedule',
			'foreignKey' => 'class_room_id',
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
	function is_this_class_room_used_in_others_related_table($id=null){
		$count_from_class_room_class_period = $this->ClassRoomClassPeriodConstraint->is_class_room_used($id);
		if($count_from_class_room_class_period > 0){
			$this->invalidate('delete_class_rom','the class room is used in class room class period constraints.');
			return true;
		}
		
		$count_from_class_room_course = $this->ClassRoomCourseConstraint->is_class_room_used($id);
		if($count_from_class_room_course > 0){
			$this->invalidate('delete_class_rom','the class room is used in class room course constraints.');
			return true;
		}
		$count_from_exam_room_course = $this->ExamRoomCourseConstraint->is_class_room_used($id);
		if($count_from_exam_room_course > 0){
			$this->invalidate('delete_class_rom','the class room is used in exam room course constraints.');
			return true;
		}
		$count_from_exam_room_session = $this->ExamRoomConstraint->is_class_room_used($id);
		if($count_from_exam_room_session > 0){
			$this->invalidate('delete_class_rom','the class room is used in exam room session constraints.');
			return true;
		}
		$count_from_exam_room_number_of_invigilator = $this->ExamRoomNumberOfInvigilator->is_class_room_used($id);
		if($count_from_exam_room_number_of_invigilator > 0){
			$this->invalidate('delete_class_rom','the class room is used in exam room number of invigilator.');
			return true;
		}
		$count_from_course_schedule = $this->CourseSchedule->is_class_room_used($id);
		if($count_from_course_schedule > 0){
			$this->invalidate('delete_class_rom','the class room is used in course schedule.');
			return true;
		}
		//TODO : for exam schedule as well when the exam schedule model bake
		
		return false;
	}
	
	function getClassRoomsForExam($college_id = null, $published_course_ids = null, $section_id = null, $exam_date = null, $session = null, $acadamic_year = null, $semester = null) {
		$examRooms = array();
		$examRoomsAll = array();
		$section_ids = array();
		//Check if it is splitted
		$check_split = ClassRegistry::init('SectionSplitForExam')->find('first',
			array(
				'conditions' =>
				array(
					'SectionSplitForExam.published_course_id' => $published_course_ids[0]
				),
				'contain' =>
				array(
					'ExamSplitSection' =>
					array(
						'StudentsExamSplitSection'
					)
				)
			)
		);
		if(count($published_course_ids) <= 1 && !empty($check_split)) {
			foreach($check_split['ExamSplitSection'] as $k => $v) {
				$index = count($section_ids);
				$section_ids[$index]['id'] = $v['id'];
				$section_ids[$index]['type'] = 2;
			}
			//debug($check_split);
		}
		else if(count($published_course_ids) <= 1){
			$section_ids[0]['id'] = $section_id;
			$section_ids[0]['type'] = 1;
		}
		//If it is merge
		else {
			$section_ids_tmp = ClassRegistry::init('PublishedCourse')->find('all',
				array(
					'conditions' =>
					array(
						'PublishedCourse.id' => $published_course_ids
					),
					'recursive' => -1
				)
			);
			$section_ids[0]['id'] = array();
			$section_ids[0]['type'] = 1;
			foreach($section_ids_tmp as $k => $v) {
				$section_ids[0]['id'][] = $v['PublishedCourse']['section_id'];
			}
		}
		
		foreach($section_ids as $section_id) {
			$examRooms = array();
			if($section_id['type'] == 1) {
				$number_of_students = ClassRegistry::init('StudentsSection')->find('count',
					array(
						'conditions' =>
						array(
							'StudentsSection.section_id' => $section_id['id']
						)
					)
				);
			}
			else if($section_id['type'] == 2) {
				$number_of_students = ClassRegistry::init('StudentsExamSplitSection')->find('count',
					array(
						'conditions' =>
						array(
							'StudentsExamSplitSection.exam_split_section_id' => $section_id['id']
						)
					)
				);
			}
			$examRoomCourseConstraints = $this->ExamRoomCourseConstraint->find('all',
				array(
					'conditions' =>
					array(
						//$published_course_ids[0] is with the assumption that on the merge all published courses will have the same defined or inforced constraint
						'ExamRoomCourseConstraint.published_course_id' => $published_course_ids[0]
					),
					'contain' => 
					array(
						'ClassRoom' =>
						array(
							'ExamRoomNumberOfInvigilator' =>
							array(
								'conditions' =>
								array(
									'ExamRoomNumberOfInvigilator.academic_year' => $acadamic_year,
									'ExamRoomNumberOfInvigilator.semester' => $semester,
								)
							)
						)
					)
				)
			);
			
			//1. Determine avtive is 1 or 0
			$exam_room_course_constraint_active = 0;
			foreach($examRoomCourseConstraints as $examRoomCourseConstraint) {
				if($examRoomCourseConstraint['ExamRoomCourseConstraint']['active'] == 1) {
					$exam_room_course_constraint_active = 1;
					break;
				}
			}
			//2. If active is 1, use only the specified rooms
			if($exam_room_course_constraint_active == 1) {
				$examRooms = array();
				foreach($examRoomCourseConstraints as $examRoomCourseConstraint) {
					if($examRoomCourseConstraint['ExamRoomCourseConstraint']['active'] == 1) {
						$search_key = array_search($examRoomCourseConstraint['ExamRoomCourseConstraint']['class_room_id'], $examRooms);
						if($search_key === false) {
							$index = count($examRooms);
							$examRooms[$index]['id'] = $examRoomCourseConstraint['ExamRoomCourseConstraint']['class_room_id'];
							$examRooms[$index]['capacity'] = $examRoomCourseConstraint['ClassRoom']['exam_capacity'];
							if(!empty($examRoomCourseConstraint['ClassRoom']['ExamRoomNumberOfInvigilator'])) {
								$examRooms[$index]['number_of_invigilator'] = $examRoomCourseConstraint['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'];
							}
							else {
								$examRooms[$index]['number_of_invigilator'] = 0;
							}
						}
					}
				}
			}
			//3. If active is 0, remove the specified rooms from examRooms
			else {
				/*
				1. Retrive class rooms
				2. Exclude rooms based on exam_room_constraints
				*/
				//debug($number_of_students);
				$classRooms = $this->find('all', 
					array(
						'conditions' =>
						array(
							'ClassRoom.exam_capacity IS NOT NULL',
							'ClassRoom.exam_capacity >= ' => $number_of_students,
							'ClassRoomBlock.college_id' => $college_id,
							'ClassRoom.available_for_exam' => 1,
							'ClassRoom.id NOT IN (SELECT class_room_id FROM exam_schedules WHERE exam_date = \''.$exam_date.'\' AND session = \''.$session.'\')',
						),
						'order' =>
						array(
							'ClassRoom.exam_capacity ASC'
						),
						'contain' =>
						array(
							'ClassRoomBlock',
							'ExamRoomNumberOfInvigilator' =>
							array(
								'conditions' =>
								array(
									'ExamRoomNumberOfInvigilator.academic_year' => $acadamic_year,
									'ExamRoomNumberOfInvigilator.semester' => $semester,
								)
							)
						)
					)
				);
				
				foreach($classRooms as $classRoom) {
					$index = count($examRooms);
					$examRooms[$index]['id'] = $classRoom['ClassRoom']['id'];
					$examRooms[$index]['capacity'] = $classRoom['ClassRoom']['exam_capacity'];
					if(!empty($classRoom['ExamRoomNumberOfInvigilator'])) {
						$examRooms[$index]['number_of_invigilator'] = $classRoom['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'];
					}
					else {
						$examRooms[$index]['number_of_invigilator'] = 0;
					}
				}
				
				foreach($examRoomCourseConstraints as $examRoomCourseConstraint) {
					if($examRoomCourseConstraint['ExamRoomCourseConstraint']['active'] == 0) {
						$search_key = array_search($examRoomCourseConstraint['ExamRoomCourseConstraint']['class_room_id'], $examRooms);
						if($search_key !== false) {
							unset($examRooms[$search_key]);
						}
					}
				}
			}
			
			//Descending sorting
			for($i = 0; $i < count($examRooms); $i++) {
				for($j = $i+1; $j < count($examRooms); $j++) {
					if($examRooms[$i]['capacity'] > $examRooms[$j]['capacity']) {
						$tmp = $examRooms[$i];
						$examRooms[$i] = $examRooms[$j];
						$examRooms[$j] = $tmp;
					}
				}
			}
			$index = count($examRoomsAll);
			$examRoomsAll[$index]['section_id'] = $section_id['id'];
			$examRoomsAll[$index]['exam_rooms'] = $examRooms;
		}
		return $examRoomsAll;
	}
	
	

}
?>
