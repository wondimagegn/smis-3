<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Curriculum extends Entity
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
        'year_introduced' => true,
        'type_credit' => true,
        'certificate_name' => true,
        'amharic_degree_nomenclature' => true,
        'specialization_amharic_degree_nomenclature' => true,
        'english_degree_nomenclature' => true,
        'specialization_english_degree_nomenclature' => true,
        'minimum_credit_points' => true,
        'department_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'lock' => true,
        'registrar_approved' => true,
        'active' => true,
        'department_study_program_id' => true,
        'curriculum_type' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'program' => true,
        'program_type' => true,
        'department_study_program' => true,
        'accepted_students' => true,
        'course_categories' => true,
        'courses' => true,
        'curriculum_attachments' => true,
        'sections' => true,
        'students' => true,
    ];
    protected $_virtual = ['curriculum_detail']; // Define virtual field

    protected function _getCurriculumDetail()
    {
        return "{$this->name} - {$this->year_introduced}";
    }
}
