<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StaffAssigne Entity
 *
 * @property int $id
 * @property string|null $college_id
 * @property string|null $department_id
 * @property string $user_id
 * @property string $program_id
 * @property string|null $program_type_id
 * @property bool $collegepermission
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class StaffAssigne extends Entity
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
        'college_id' => true,
        'department_id' => true,
        'user_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'collegepermission' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'department' => true,
        'user' => true,
        'program' => true,
        'program_type' => true,
    ];
}
