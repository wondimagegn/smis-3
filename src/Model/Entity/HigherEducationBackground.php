<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HigherEducationBackground Entity
 *
 * @property int $id
 * @property string $name
 * @property string $field_of_study
 * @property string $diploma_awarded
 * @property \Cake\I18n\FrozenDate $date_graduated
 * @property float $cgpa_at_graduation
 * @property string $city
 * @property int|null $student_id
 * @property int $first_degree_taken
 * @property int $second_degree_taken
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class HigherEducationBackground extends Entity
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
        'field_of_study' => true,
        'diploma_awarded' => true,
        'date_graduated' => true,
        'cgpa_at_graduation' => true,
        'city' => true,
        'student_id' => true,
        'first_degree_taken' => true,
        'second_degree_taken' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
