<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SponsorType Entity
 *
 * @property int $id
 * @property string $sponsor
 * @property string $code
 * @property string|null $sponsor_2nd_language
 * @property int $priority_order
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class SponsorType extends Entity
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
        'sponsor' => true,
        'code' => true,
        'sponsor_2nd_language' => true,
        'priority_order' => true,
        'created' => true,
        'modified' => true,
    ];
}
