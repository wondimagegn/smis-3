<?php
class ExamSchedule extends AppModel {
	var $name = 'ExamSchedule';
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ExamSplitSection' => array(
			'className' => 'ExamSplitSection',
			'foreignKey' => 'exam_split_section_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Invigilator' => array(
			'className' => 'Invigilator',
			'foreignKey' => 'exam_schedule_id',
			'dependent' => true,
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

	function getExamSchedule($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_ids = null, $department_ids = null, $year_levels = null, $organize_by_departments = null, $organize_by_year_levels = null) {
		$year_level_ids = array();
		$sections = array();
		$publishedCourses = array();
		foreach($department_ids as $dep_key => $department_id) {
			foreach($year_levels as $year_level) {
				if($year_level == 1) {
					$year_level_name = $year_level.'st';
				}
				else if($year_level == 2) {
					$year_level_name = $year_level.'nd';
				}
				else if($year_level == 3) {
					$year_level_name = $year_level.'rd';
				}
				else {
					$year_level_name = $year_level.'th';
				}
			
				$yearLevel = ClassRegistry::init('YearLevel')->find('first',
					array(
						'conditions' =>
						array(
							'YearLevel.name' => $year_level_name,
							'YearLevel.department_id' => $department_id
						),
						'recursive' => -1
					)
				);
				if((isset($yearLevel['YearLevel']['id']) || strcasecmp($dep_key, 'FP') == 0) && !empty($yearLevel['YearLevel']['id'])) {
					$options = array(
						'conditions' =>
						array(
							'Section.academicyear' => $acadamic_year,
							'Section.program_id' => $program_id,
							'Section.program_type_id' => $program_type_ids,
							'Section.year_level_id' => $yearLevel['YearLevel']['id']
						),
						'contain' =>
						array(
							'PublishedCourse' => 
							array(
								'conditions' =>
								array(
									'PublishedCourse.academic_year' => $acadamic_year,
									'PublishedCourse.semester' => $semester,
								)
							)
						)
					);
					if(strcasecmp($dep_key, 'FP') == 0) {
						$options['conditions']['Section.college_id'] = $college_id;
					}
					else {
						$options['conditions']['Section.department_id'] = $department_id;
					}
					$sections_t = $this->PublishedCourse->Section->find('all', $options);
					
					foreach($sections_t as $section_key => $section) {
						foreach($section['PublishedCourse'] as $pc_key => $pc) {
							$publishedCourses[] = $pc['id'];
						}
					}
				}
			}
		}
		
		$examSchedules = $this->find('all',
			array(
				'conditions' =>
				array(
					'ExamSchedule.acadamic_year' => $acadamic_year,
					'ExamSchedule.semester' => $semester,
					'ExamSchedule.published_course_id' => $publishedCourses
				),
				'contain' =>
				array(
					'PublishedCourse' =>
					array(
						'Section' =>
						array(
							'fields' =>
							array(
								'name'
							)
						),
						'Course' =>
						array(
							'fields' =>
							array(
								'course_title',
								'course_code'
							)
						),
						'fields' =>
						array(
							'id'
						)
					),
					'Invigilator' =>
					array(
						'Staff' =>
						array(
							'fields' =>
							array(
								'full_name'
							)
						),
						'StaffForExam' =>
						array(
							'Staff' =>
							array(
								'fields' =>
								array(
									'full_name'
								)
							),
						),
					),
					'ClassRoom' =>
					array(
						'ClassRoomBlock' =>
						array(
							'fields' =>
							array(
								'block_code'
							)
						),
						'ExamRoomNumberOfInvigilator' =>
						array(
							'conditions' =>
							array(
								'ExamRoomNumberOfInvigilator.academic_year' => $acadamic_year,
								'ExamRoomNumberOfInvigilator.semester' => $semester,
							),
							'fields' =>
							array(
								'number_of_invigilator'
							)
						),
						'fields' =>
						array(
							'room_code'
						)
					),
					'ExamSplitSection'
				),
				'order' =>
				array(
					'ExamSchedule.exam_date ASC',
					'ExamSchedule.session ASC'
				)
			)
		);
		
		//debug($publishedCourses);
		//debug($examSchedules);
		return $examSchedules;
	}
	
	function cancelExamSchedule($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_ids = null, $department_ids = null, $year_levels = null) {
		$year_level_ids = array();
		$sections = array();
		$publishedCourses = array();
		foreach($department_ids as $dep_key => $department_id) {
			foreach($year_levels as $year_level) {
				if($year_level == 1) {
					$year_level_name = $year_level.'st';
				}
				else if($year_level == 2) {
					$year_level_name = $year_level.'nd';
				}
				else if($year_level == 3) {
					$year_level_name = $year_level.'rd';
				}
				else {
					$year_level_name = $year_level.'th';
				}
				
				$yearLevel = $this->PublishedCourse->Section->YearLevel->find('first',
					array(
						'conditions' =>
						array(
							'YearLevel.name' => $year_level_name,
							'YearLevel.department_id' => $department_id
						),
						'recursive' => -1
					)
				);
				if((isset($yearLevel['YearLevel']['id']) || strcasecmp($dep_key, 'FP') == 0) && !empty($yearLevel['YearLevel']['id'])) {
					$options = array(
						'conditions' =>
						array(
							'Section.academicyear' => $acadamic_year,
							'Section.program_id' => $program_id,
							'Section.program_type_id' => $program_type_ids,
							'Section.year_level_id' => $yearLevel['YearLevel']['id']
						),
						'contain' =>
						array(
							'YearLevel',
							'PublishedCourse' => 
							array(
								'conditions' =>
								array(
									'PublishedCourse.academic_year' => $acadamic_year,
									'PublishedCourse.semester' => $semester,
									'PublishedCourse.id IN (SELECT published_course_id FROM exam_schedules)',
								),
							)
						)
					);
					if(strcasecmp($dep_key, 'FP') == 0) {
						$options['conditions']['Section.college_id'] = $college_id;
					}
					else {
						$options['conditions']['Section.department_id'] = $department_id;
					}
					$sections_t = $this->PublishedCourse->Section->find('all', $options);
					//debug($sections_t);
					foreach($sections_t as $section_key => $section) {
						foreach($section['PublishedCourse'] as $pc_key => $pc) {
							$publishedCourses[] = $pc['id'];
						}
					}
				}
			}//End of each year level
		}//End of each department
		if(empty($publishedCourses)) {
			return 0;
		}
		if($this->deleteAll(array('ExamSchedule.published_course_id' => $publishedCourses))) {
			return 1;
		}
		else {
			return 2;
		}
	}
	
}
