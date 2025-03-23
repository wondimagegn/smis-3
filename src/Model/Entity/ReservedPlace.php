<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReservedPlace Entity
 *
 * @property int $id
 * @property int $placements_results_criteria_id
 * @property int|null $participating_department_id
 * @property int|null $college_id
 * @property int $number
 * @property string|null $description
 * @property string|null $academicyear
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\PlacementsResultsCriteria $placements_results_criteria
 * @property \App\Model\Entity\ParticipatingDepartment $participating_department
 * @property \App\Model\Entity\College $college
 */
class ReservedPlace extends Entity
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
        'placements_results_criteria_id' => true,
        'participating_department_id' => true,
        'college_id' => true,
        'number' => true,
        'description' => true,
        'academicyear' => true,
        'created' => true,
        'modified' => true,
        'placements_results_criteria' => true,
        'participating_department' => true,
        'college' => true,
    ];
}
