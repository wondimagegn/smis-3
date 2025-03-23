<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseInstructorAssignment extends Entity
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
        'academic_year' => true,
        'semester' => true,
        'section_id' => true,
        'staff_id' => true,
        'published_course_id' => true,
        'type' => true,
        'created' => true,
        'modified' => true,
        'course_split_section_id' => true,
        'isprimary' => true,
        'evaluation_printed' => true,
        'section' => true,
        'staff' => true,
        'published_course' => true,
        'course_split_section' => true,
        'exam_grades' => true,
    ];
}
