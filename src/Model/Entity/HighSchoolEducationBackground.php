<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HighSchoolEducationBackground Entity
 *
 * @property int $id
 * @property string $name
 * @property string $town
 * @property string $zone
 * @property string $region_id
 * @property string $school_level
 * @property int|null $student_id
 * @property bool $national_exam_taken
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Region $region
 * @property \App\Model\Entity\Student $student
 */
class HighSchoolEducationBackground extends Entity
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
        'town' => true,
        'zone' => true,
        'region_id' => true,
        'school_level' => true,
        'student_id' => true,
        'national_exam_taken' => true,
        'created' => true,
        'modified' => true,
        'region' => true,
        'student' => true,
    ];
}
