<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MealAttendance Entity
 *
 * @property int $id
 * @property int $meal_type_id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\MealType $meal_type
 * @property \App\Model\Entity\Student $student
 */
class MealAttendance extends Entity
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
        'meal_type_id' => true,
        'student_id' => true,
        'created' => true,
        'modified' => true,
        'meal_type' => true,
        'student' => true,
    ];
}
