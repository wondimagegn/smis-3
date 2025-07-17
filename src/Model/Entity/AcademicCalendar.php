<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicCalendar extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];


    protected $_virtual = ['full_year'];

    protected function _getFullYear()
    {
        return $this->academic_year . '-' . $this->semester;
    }
}
