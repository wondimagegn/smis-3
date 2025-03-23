<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserMealAssignment Entity
 *
 * @property int $id
 * @property string $user_id
 * @property int $meal_hall_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\MealHall $meal_hall
 */
class UserMealAssignment extends Entity
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
        'user_id' => true,
        'meal_hall_id' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'meal_hall' => true,
    ];
}
