<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgramProgramTypeClassRoom Entity
 *
 * @property int $id
 * @property int $program_id
 * @property int $program_type_id
 * @property int $class_room_id
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\ClassRoom $class_room
 */
class ProgramProgramTypeClassRoom extends Entity
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
        'program_id' => true,
        'program_type_id' => true,
        'class_room_id' => true,
        'program' => true,
        'program_type' => true,
        'class_room' => true,
    ];
}
