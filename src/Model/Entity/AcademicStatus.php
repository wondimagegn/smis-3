<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicStatus extends Entity
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
        'order' => true,
        'computable' => true,
        'created' => true,
        'modified' => true,
        'academic_stands' => true,
        'historical_student_exam_statuses' => true,
        'other_academic_rules' => true,
        'student_exam_statuses' => true,
    ];
}
