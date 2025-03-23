<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Attendance extends Entity
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
        'student_id' => true,
        'published_course_id' => true,
        'attendace_type' => true,
        'attendance_date' => true,
        'attendance' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'published_course' => true,
    ];
}
