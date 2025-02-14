<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseDrop extends Entity
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
        'minute_number' => true,
        'forced' => true,
        'student_id' => true,
        'course_registration_id' => true,
        'created' => true,
        'modified' => true,
        'year_level' => true,
        'student' => true,
        'course_registration' => true,
    ];
}
