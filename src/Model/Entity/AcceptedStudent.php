<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcceptedStudent extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
