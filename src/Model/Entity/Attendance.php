<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attendance Entity
 */
class Attendance extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'student_id' => true,
        'published_course_id' => true,
        'attendance_type' => true,
        'attendance_date' => true,
        'attendance' => true,
        'student' => true,
        'published_course' => true
    ];
}
