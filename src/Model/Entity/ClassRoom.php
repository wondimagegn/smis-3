<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClassRoom extends Entity
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
        'class_room_block_id' => true,
        'room_code' => true,
        'available_for_lecture' => true,
        'available_for_exam' => true,
        'lecture_capacity' => true,
        'exam_capacity' => true,
        'class_room_block' => true,
        'class_room_class_period_constraints' => true,
        'class_room_course_constraints' => true,
        'course_schedules' => true,
        'exam_room_constraints' => true,
        'exam_room_course_constraints' => true,
        'exam_room_number_of_invigilators' => true,
        'exam_schedules' => true,
        'program_program_type_class_rooms' => true,
    ];
}
