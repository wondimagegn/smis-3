<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MealHall Entity
 *
 * @property int $id
 * @property int $campus_id
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Campus $campus
 * @property \App\Model\Entity\ExceptionMealAssignment[] $exception_meal_assignments
 * @property \App\Model\Entity\MealHallAssignment[] $meal_hall_assignments
 * @property \App\Model\Entity\UserMealAssignment[] $user_meal_assignments
 */
class MealHall extends Entity
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
        'campus_id' => true,
        'name' => true,
        'created' => true,
        'modified' => true,
        'campus' => true,
        'exception_meal_assignments' => true,
        'meal_hall_assignments' => true,
        'user_meal_assignments' => true,
    ];
}
