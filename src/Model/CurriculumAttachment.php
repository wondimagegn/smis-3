<?php
App::uses('AppModel', 'Model');
class CurriculumAttachment extends AppModel
{
	public $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'curriculum_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	// the course given taken from previous curriculum ? 
	public function isEquivalentTakenCoursesFromCurriculum($course_id, $student_id)
	{
		$currentStudentCurriculum = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		
		$curriculumAttachmentHistoryOfStudent = $this->find('list', array('conditions' => array('CurriculumAttachment.student_id' => $student_id, 'CurriculumAttachment.CurriculumAttachment.student_id !=' => $currentStudentCurriculum['Student']['curriculum_id']), 'fields' => array('CurriculumAttachment.student_id', 'CurriculumAttachment.curriculum_id')));
		
		if (!empty($curriculumAttachmentHistoryOfStudent)) {
			foreach ($curriculumAttachmentHistoryOfStudent as $k => $v) {
				$courseDetails = $this->Curriculum->Course->find('first', array('conditions' => array('Course.id' => $course_id, 'Course.curriculum_id' => $v), 'recursive' => -1));
				if (!empty($courseDetails)) {
					return true;
				}
			}
		} else {
			return true;
		}
		return false;
	}

	/* public function readyForPublishingCourses($student_id,$department_id, $taken_courses,$section_id) {	 
		$ready_for_publishing_courses[$section_id]=$this->PublishedCourse->Course->find('all',array('conditions'=>array('Course.curriculum_id'=>$section_curriculum_attachment[$section_id],"NOT"=>array('Course.id '=>$taken_courses)),'contain'=>array('PublishedCourse')));
					
	}
	public function takenCourseOfSection($student_id,$department_id, $taken_courses,$section_id) {
		$taken_courses_allow_to_publishe_it[$section_id]=$this->PublishedCourse->Course->find('all',array('conditions'=>array('Course.curriculum_id'=>$section_curriculum_attachment[$section_id], 'Course.department_id'=>$this->department_id,'Course.id '=>$taken_courses),'contain'=>array('PublishedCourse')));
	} */

}
