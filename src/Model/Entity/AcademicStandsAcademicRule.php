<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicStandsAcademicRule extends Entity
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
        'academic_stand_id' => true,
        'academic_rule_id' => true,
        'archive' => true,
        'created' => true,
        'modified' => true,
        'academic_stand' => true,
        'academic_rule' => true,
    ];
}
