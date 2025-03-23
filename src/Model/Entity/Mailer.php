<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Mailer extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
