<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseSchedule extends Entity
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
        'class_room_id' => true,
        'section_id' => true,
        'published_course_id' => true,
        'course_split_section_id' => true,
        'academic_year' => true,
        'semester' => true,
        'type' => true,
        'is_auto' => true,
        'created' => true,
        'modified' => true,
        'class_room' => true,
        'section' => true,
        'published_course' => true,
        'course_split_section' => true,
        'class_periods' => true,
    ];
}
