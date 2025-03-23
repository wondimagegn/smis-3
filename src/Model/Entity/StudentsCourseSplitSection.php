<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentsCourseSplitSection Entity
 *
 * @property int $course_split_section_id
 * @property int $student_id
 * @property int $id
 *
 * @property \App\Model\Entity\CourseSplitSection $course_split_section
 * @property \App\Model\Entity\Student $student
 */
class StudentsCourseSplitSection extends Entity
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
        'course_split_section_id' => true,
        'student_id' => true,
        'course_split_section' => true,
        'student' => true,
    ];
}
