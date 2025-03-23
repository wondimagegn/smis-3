<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClassPeriod extends Entity
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
        'week_day' => true,
        'period_setting_id' => true,
        'college_id' => true,
        'program_type_id' => true,
        'program_id' => true,
        'period_setting' => true,
        'college' => true,
        'program_type' => true,
        'program' => true,
        'class_period_course_constraints' => true,
        'class_room_class_period_constraints' => true,
        'instructor_class_period_course_constraints' => true,
        'course_schedules' => true,
    ];
}
