<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DormitoryBlock Entity
 *
 * @property int $id
 * @property int $campus_id
 * @property string $block_name
 * @property string $type
 * @property string|null $location
 * @property string|null $telephone_number
 * @property string|null $alt_telephone_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Campus $campus
 * @property \App\Model\Entity\Dormitory[] $dormitories
 * @property \App\Model\Entity\UserDormAssignment[] $user_dorm_assignments
 */
class DormitoryBlock extends Entity
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
        'campus_id' => true,
        'block_name' => true,
        'type' => true,
        'location' => true,
        'telephone_number' => true,
        'alt_telephone_number' => true,
        'created' => true,
        'modified' => true,
        'campus' => true,
        'dormitories' => true,
        'user_dorm_assignments' => true,
    ];
}
