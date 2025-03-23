<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Dormitory Entity
 *
 * @property int $id
 * @property int $dormitory_block_id
 * @property int $dorm_number
 * @property int $floor
 * @property int|null $capacity
 * @property bool $available
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\DormitoryBlock $dormitory_block
 * @property \App\Model\Entity\DormitoryAssignment[] $dormitory_assignments
 */
class Dormitory extends Entity
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
        'dormitory_block_id' => true,
        'dorm_number' => true,
        'floor' => true,
        'capacity' => true,
        'available' => true,
        'created' => true,
        'modified' => true,
        'dormitory_block' => true,
        'dormitory_assignments' => true,
    ];
}
