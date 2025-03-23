<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ServiceWing Entity
 *
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Position[] $positions
 * @property \App\Model\Entity\Staff[] $staffs
 */
class ServiceWing extends Entity
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
        'active' => true,
        'created' => true,
        'modified' => true,
        'positions' => true,
        'staffs' => true,
    ];
}
