<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class YearLevel extends Entity
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
        'name' => true,
        'department_id' => true,
        'department' => true,
        'academic_calendars' => true,
        'academic_stands' => true,
        'course_adds' => true,
        'course_drops' => true,
        'course_registrations' => true,
        'courses' => true,
        'exam_periods' => true,
        'extending_academic_calendars' => true,
        'instructor_number_of_exam_constraints' => true,
        'other_academic_rules' => true,
        'published_courses' => true,
        'sections' => true,
        'created' => true,
        'modified' => true,
    ];
}
