<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class ApplicablePayment extends Entity
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
        'semester' => true,
        'tutition_fee' => true,
        'meal' => true,
        'accomodation' => true,
        'health' => true,
        'sponsor_type' => true,
        'sponsor_name' => true,
        'sponsor_address' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
