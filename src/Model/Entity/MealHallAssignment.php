<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MealHallAssignment Entity
 *
 * @property int $id
 * @property int $meal_hall_id
 * @property int|null $student_id
 * @property int|null $accepted_student_id
 * @property string $academic_year
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\MealHall $meal_hall
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\AcceptedStudent $accepted_student
 */
class MealHallAssignment extends Entity
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
        'meal_hall_id' => true,
        'student_id' => true,
        'accepted_student_id' => true,
        'academic_year' => true,
        'created' => true,
        'modified' => true,
        'meal_hall' => true,
        'student' => true,
        'accepted_student' => true,
    ];
}
