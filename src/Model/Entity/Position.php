<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Position Entity
 *
 * @property int $id
 * @property string $position
 * @property string|null $description
 * @property int $active
 * @property int|null $service_wing_id
 * @property string|null $applicable_educations
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ServiceWing $service_wing
 * @property \App\Model\Entity\Staff[] $staffs
 */
class Position extends Entity
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
        'position' => true,
        'description' => true,
        'active' => true,
        'service_wing_id' => true,
        'applicable_educations' => true,
        'created' => true,
        'modified' => true,
        'service_wing' => true,
        'staffs' => true,
    ];
}
