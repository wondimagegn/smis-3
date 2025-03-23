<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicStand extends Entity
{
    protected $_accessible = [
        'program_id' => true,
        'year_level_id' => true,
        'semester' => true,
        'academic_year_from' => true,
        'academic_year_to' => true,
        'academic_status_id' => true,
        'sort_order' => true,
        'status_visible' => true,
        'applicable_for_all_current_student' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'academic_status' => true,
        'academic_rules' => true,
    ];
}
