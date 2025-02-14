<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudyProgram Entity
 *
 * @property int $id
 * @property string $study_program_name
 * @property string $code
 * @property string $local_band
 * @property string $ISCED_band
 * @property string $study_field
 * @property string $sub_study_field
 * @property string|null $study_program_2nd_language
 * @property int $priority_order
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DepartmentStudyProgram[] $department_study_programs
 */
class StudyProgram extends Entity
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
        'study_program_name' => true,
        'code' => true,
        'local_band' => true,
        'ISCED_band' => true,
        'study_field' => true,
        'sub_study_field' => true,
        'study_program_2nd_language' => true,
        'priority_order' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'department_study_programs' => true,
    ];
}
