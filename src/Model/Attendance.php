<?php
class Attendance extends AppModel {
	var $name = 'Attendance';
	var $validate = array(
		'student_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'published_course_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'attendace_type' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'attendance_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'attendance' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
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
		)
	);
	
	function getListOfDateAttendanceTaken($published_course_id = null) {
		$attendance_dates = $this->find('list',
			array(
				'conditions' =>
				array(
					'Attendance.published_course_id' => $published_course_id
				),
				'fields' => array('attendance_date'),
				'recursive' => -1,
				'order' => array('Attendance.attendance_date ASC')
			)
		);
		$attendance_dates = array_unique($attendance_dates);
		$attendance_dates_formatted = array();
		foreach($attendance_dates as $key => $value) {
			$attendance_dates_formatted[$value] = date('D M d, Y', mktime (substr($value,11 ,2), 
			substr($value,14 ,2), 
			substr($value,17 ,2), 
			substr($value,5 ,2), 
			substr($value,8 ,2), 
			substr($value,0 ,4)));
		}
		return $attendance_dates_formatted;
	}
	
	function getCourseAttendanceDetail($published_course_id = null, $attendance_start_date = null, $attendance_end_date = null, $student_registers, $student_adds) {
		foreach($student_registers as $key => $student_register) {
			$attendance_detail = $this->find('all',
				array(
					'conditions' =>
					array(
						'Attendance.published_course_id' => $published_course_id,
						'Attendance.attendance_date >=' => $attendance_start_date,
						'Attendance.attendance_date <=' => $attendance_end_date,
						'Attendance.student_id' => $student_register['Student']['id']
					),
					'order' => array('Attendance.attendance_date ASC'),
					'recursive' => -1
				)
			);
			if(!empty($attendance_detail)) {
				$student_registers[$key]['Attendance'] = $attendance_detail;
				}
			else {
				$student_registers[$key]['Attendance'] = array();
			}
		}
		foreach($student_adds as $key => $student_add) {
			$attendance_detail = $this->find('all',
				array(
					'conditions' =>
					array(
						'Attendance.published_course_id' => $published_course_id,
						'Attendance.attendance_date >=' => $attendance_start_date,
						'Attendance.attendance_date <=' => $attendance_end_date,
						'Attendance.student_id' => $student_add['Student']['id']
					),
					'order' => array('Attendance.attendance_date ASC'),
					'recursive' => -1
				)
			);
			if(!empty($attendance_detail)) {
				$student_adds[$key]['Attendance'] = $attendance_detail;
				}
			else {
				$student_adds[$key]['Attendance'] = array();
			}
		}
		
		$course_attendance['register'] = $student_registers;
		$course_attendance['add'] = $student_adds;
		
		return $course_attendance;
	}
	
	// Check whether the course schedule used in attendance or not
	function is_course_schedule_uesd_in_attendance($course_schedule_published_course_ids=null){
		$count = $this->find('count', array('conditions'=>array('Attendance.published_course_id'=>$course_schedule_published_course_ids), 'limit'=>2));
		if($count >0){
			return true;
		} else {
			return false;
		}
	}
}
