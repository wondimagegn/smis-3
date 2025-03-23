<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Course extends Entity
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
        'course_title' => true,
        'course_code' => true,
        'credit' => true,
        'lecture_hours' => true,
        'tutorial_hours' => true,
        'course_description' => true,
        'course_objective' => true,
        'curriculum_id' => true,
        'laboratory_hours' => true,
        'department_id' => true,
        'course_category_id' => true,
        'lecture_attendance_requirement' => true,
        'lab_attendance_requirement' => true,
        'grade_type_id' => true,
        'year_level_id' => true,
        'semester' => true,
        'major' => true,
        'thesis' => true,
        'exit_exam' => true,
        'elective' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'curriculum' => true,
        'department' => true,
        'course_category' => true,
        'grade_type' => true,
        'year_level' => true,
        'books' => true,
        'course_exemptions' => true,
        'exit_exams' => true,
        'graduation_works' => true,
        'journals' => true,
        'prerequisites' => true,
        'published_courses' => true,
        'weblinks' => true,
    ];
    protected $_virtual = ['course_detail_hours', 'course_code_title']; // Define virtual fields
    /**
     * Virtual field replacement - `course_detail_hours`
     */
    protected function _getCourseDetailHours()
    {
        return "{$this->lecture_hours}-{$this->tutorial_hours}-{$this->laboratory_hours}";
    }

    /**
     * Virtual field replacement - `course_code_title`
     */
    protected function _getCourseCodeTitle()
    {
        $title = trim(str_replace(["\t", "  "], " ", $this->course_title));
        $code = trim(str_replace(["\t", "  "], "", $this->course_code));
        return "{$title} ({$code})";
    }
}
