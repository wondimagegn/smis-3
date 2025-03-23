<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class City extends Entity
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
        'region_id' => true,
        'zone_id' => true,
        'city' => true,
        'short' => true,
        'city_2nd_language' => true,
        'priority_order' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'region' => true,
        'zone' => true,
        'contacts' => true,
        'staffs' => true,
        'students' => true,
    ];
}
