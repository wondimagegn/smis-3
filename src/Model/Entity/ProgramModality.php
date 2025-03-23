<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgramModality Entity
 *
 * @property int $id
 * @property string $modality
 * @property string $code
 * @property string $modality_category
 * @property string|null $modality_2nd_language
 * @property string|null $modality_category_2nd_language
 * @property int $priority_order
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DepartmentStudyProgram[] $department_study_programs
 * @property \App\Model\Entity\ProgramType[] $program_types
 */
class ProgramModality extends Entity
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
        'modality' => true,
        'code' => true,
        'modality_category' => true,
        'modality_2nd_language' => true,
        'modality_category_2nd_language' => true,
        'priority_order' => true,
        'created' => true,
        'modified' => true,
        'department_study_programs' => true,
        'program_types' => true,
    ];
}
