<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Quota Entity
 *
 * @property int $id
 * @property int|null $college_id
 * @property int|null $female
 * @property int|null $regions
 * @property string|null $developing_regions_id
 * @property string|null $academicyear
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\DevelopingRegion $developing_region
 */
class Quota extends Entity
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
        'college_id' => true,
        'female' => true,
        'regions' => true,
        'developing_regions_id' => true,
        'academicyear' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'developing_region' => true,
    ];
}
