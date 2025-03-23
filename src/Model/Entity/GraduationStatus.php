<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GraduationStatus Entity
 *
 * @property int $id
 * @property int $program_id
 * @property float $cgpa
 * @property string $status
 * @property string $academic_year
 * @property bool $applicable_for_current_student
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 */
class GraduationStatus extends Entity
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
        'program_id' => true,
        'cgpa' => true,
        'status' => true,
        'academic_year' => true,
        'applicable_for_current_student' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
    ];
}
