<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ParticipatingDepartment Entity
 *
 * @property int $id
 * @property int|null $college_id
 * @property int|null $department_id
 * @property bool|null $other_college_department
 * @property int $number
 * @property int $female
 * @property int $regions
 * @property int $disability
 * @property string|null $developing_regions_id
 * @property string|null $academic_year
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\DevelopingRegion $developing_region
 * @property \App\Model\Entity\ReservedPlace[] $reserved_places
 */
class ParticipatingDepartment extends Entity
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
        'department_id' => true,
        'other_college_department' => true,
        'number' => true,
        'female' => true,
        'regions' => true,
        'disability' => true,
        'developing_regions_id' => true,
        'academic_year' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'department' => true,
        'developing_region' => true,
        'reserved_places' => true,
    ];
}
