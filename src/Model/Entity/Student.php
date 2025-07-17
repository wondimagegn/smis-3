<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Student extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
    protected $_virtual = ['full_name','full_am_name','full_name_studentnumber']; // Define virtual fields


    protected function _getFullName()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    protected function _getFullAmName()
    {
        return trim($this->amharic_first_name . ' ' . $this->amharic_middle_name . ' ' . $this->amharic_last_name);
    }

    protected function _getFullNameStudentnumber()
    {
        return trim($this->amharic_first_name . ' ' . $this->amharic_middle_name . ' ' .
            $this->amharic_last_name.'('.$this->studentnumber.')');
    }
}
