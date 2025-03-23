<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseAdd extends Entity
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
        'semester' => true,
        'academic_year' => true,
        'department_approval' => true,
        'reason' => true,
        'department_approved_by' => true,
        'registrar_confirmation' => true,
        'registrar_confirmed_by' => true,
        'student_id' => true,
        'published_course_id' => true,
        'auto_rejected' => true,
        'cron_job' => true,
        'created' => true,
        'modified' => true,
        'year_level' => true,
        'student' => true,
        'published_course' => true,
        'exam_grades' => true,
        'exam_results' => true,
        'fx_resit_request' => true,
        'historical_student_course_grade_excludes' => true,
        'makeup_exams' => true,
        'rejected_exam_grades' => true,
        'result_entry_assignments' => true,
    ];
}
