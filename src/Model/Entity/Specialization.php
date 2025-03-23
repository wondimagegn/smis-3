<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Specialization Entity
 *
 * @property int $id
 * @property int $department_id
 * @property int $name
 * @property int $active
 * @property int $created
 * @property int $modified
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 * @property \App\Model\Entity\Student[] $students
 */
class Specialization extends Entity
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
        'department_id' => true,
        'name' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'accepted_students' => true,
        'students' => true,
    ];
}
