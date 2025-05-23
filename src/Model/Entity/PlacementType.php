<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementType Entity
 *
 * @property int $id
 * @property string $placement_type
 * @property string $code
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 */
class PlacementType extends Entity
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
        'placement_type' => true,
        'code' => true,
        'created' => true,
        'modified' => true,
        'accepted_students' => true,
    ];
}
