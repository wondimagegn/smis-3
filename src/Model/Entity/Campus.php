<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Campus extends Entity
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
        'name' => true,
        'description' => true,
        'male_capacity' => true,
        'female_capacity' => true,
        'available_for_college' => true,
        'active' => true,
        'campus_code' => true,
        'created' => true,
        'modified' => true,
        'accepted_students' => true,
        'class_room_blocks' => true,
        'colleges' => true,
        'dormitory_blocks' => true,
        'meal_halls' => true,
    ];
}
