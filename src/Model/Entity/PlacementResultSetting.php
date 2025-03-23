<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementResultSetting Entity
 *
 * @property int $id
 * @property string $result_type
 * @property float $percent
 * @property int $round
 * @property int $max_result
 * @property string $applied_for
 * @property int $group_identifier
 * @property string $academic_year
 * @property int $program_id
 * @property int $program_type_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class PlacementResultSetting extends Entity
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
        'result_type' => true,
        'percent' => true,
        'round' => true,
        'max_result' => true,
        'applied_for' => true,
        'group_identifier' => true,
        'academic_year' => true,
        'program_id' => true,
        'program_type_id' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}
