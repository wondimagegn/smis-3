<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Qualification Entity
 *
 * @property int $id
 * @property string $qualification
 * @property string $code
 * @property string $default_ISCED_level
 * @property string|null $qualification_2nd_language
 * @property int $priority_order
 * @property int|null $program_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\DepartmentStudyProgram[] $department_study_programs
 */
class Qualification extends Entity
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
        'qualification' => true,
        'code' => true,
        'default_ISCED_level' => true,
        'qualification_2nd_language' => true,
        'priority_order' => true,
        'program_id' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'department_study_programs' => true,
    ];
}
