<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementAdditionalPoint Entity
 *
 * @property int $id
 * @property string $type
 * @property float $point
 * @property int $round
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
class PlacementAdditionalPoint extends Entity
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
        'type' => true,
        'point' => true,
        'round' => true,
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
