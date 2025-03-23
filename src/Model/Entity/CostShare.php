<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CostShare extends Entity
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
        'student_id' => true,
        'academic_year' => true,
        'sharing_cycle' => true,
        'education_fee' => true,
        'accomodation_fee' => true,
        'cafeteria_fee' => true,
        'medical_fee' => true,
        'cost_sharing_sign_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
