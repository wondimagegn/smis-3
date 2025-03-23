<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicRule extends Entity
{
    protected $_virtual = ['cmp_sgpa', 'cmp_cgpa']; // Define virtual fields
    protected $_accessible = [
        'scmo' => true,
        'sgpa' => true,
        'operatorI' => true,
        'ccmo' => true,
        'cgpa' => true,
        'operatorII' => true,
        'tcw' => true,
        'operatorIII' => true,
        'pfw' => true,
        'academic_stand_id' => true,
        'created' => true,
        'modified' => true,
        'academic_stands' => true,
    ];
    protected function _getCmpSgpa()
    {
        return $this->scmo . $this->sgpa; // Concatenating fields
    }

    protected function _getCmpCgpa()
    {
        return $this->ccmo . $this->cgpa; // Concatenating fields
    }
}
