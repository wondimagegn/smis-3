<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseRegistration extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'year_level_id' => true,
        'section_id' => true,
        'semester' => true,
        'academic_year' => true,
        'student_id' => true,
        'published_course_id' => true,
        'type' => true,
        'academic_calendar_id' => true,
        'cafeteria_consumer' => true,
        'created' => true,
        'modified' => true,
        'year_level' => true,
        'section' => true,
        'student' => true,
        'published_course' => true,
        'academic_calendar' => true,
        'course_drops' => true,
        'exam_grades' => true,
        'exam_results' => true,
        'excluded_course_from_transcripts' => true,
        'fx_resit_request' => true,
        'historical_student_course_grade_excludes' => true,
        'makeup_exams' => true,
        'result_entry_assignments' => true,
    ];
}
