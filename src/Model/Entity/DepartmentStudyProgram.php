<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DepartmentStudyProgram extends Entity
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
        'department_id' => true,
        'study_program_id' => true,
        'program_modality_id' => true,
        'qualification_id' => true,
        'academic_year' => true,
        'apply_for_current_students' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'study_program' => true,
        'program_modality' => true,
        'qualification' => true,
        'curriculums' => true,
    ];
}
