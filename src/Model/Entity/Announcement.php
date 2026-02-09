<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Announcement extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    // Optional: format dates nicely
    protected function _getStartDate($date)
    {
        return $date ? $date->nice() : '';
    }

    protected function _getEndDate($date)
    {
        return $date ? $date->nice() : '';
    }
}
