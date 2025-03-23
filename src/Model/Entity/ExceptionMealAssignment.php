<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExceptionMealAssignment Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $meal_hall_id
 * @property int|null $accept_deny
 * @property \Cake\I18n\FrozenDate $start_date
 * @property \Cake\I18n\FrozenDate $end_date
 * @property string|null $remark
 * @property int $created
 * @property int $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\MealHall $meal_hall
 */
class ExceptionMealAssignment extends Entity
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
        'meal_hall_id' => true,
        'accept_deny' => true,
        'start_date' => true,
        'end_date' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'meal_hall' => true,
    ];
}
