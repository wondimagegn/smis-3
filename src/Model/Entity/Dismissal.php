<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Dismissal extends Entity
{
    protected $_accessible = [
        'student_id' => true,
        'reason' => true,
        'request_date' => true,
        'acceptance_date' => true,
        'for_good' => true,
        'dismisal_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
