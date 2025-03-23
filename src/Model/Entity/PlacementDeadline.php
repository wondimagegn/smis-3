<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementDeadline Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $deadline
 * @property int $program_id
 * @property int $program_type_id
 * @property string $academic_year
 * @property int $group_identifier
 * @property string $applied_for
 * @property int $placement_round
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class PlacementDeadline extends Entity
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
        'deadline' => true,
        'program_id' => true,
        'program_type_id' => true,
        'academic_year' => true,
        'group_identifier' => true,
        'applied_for' => true,
        'placement_round' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}
