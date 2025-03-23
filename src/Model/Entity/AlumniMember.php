<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AlumniMember extends Entity
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
        'trackingnumber' => true,
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'gender' => true,
        'date_of_birth' => true,
        'gradution' => true,
        'institute_college' => true,
        'department' => true,
        'program' => true,
        'country' => true,
        'city' => true,
        'current_position' => true,
        'name_of_employer' => true,
        'phone' => true,
        'work_telephone' => true,
        'home_telephone' => true,
        'remarks' => true,
        'created' => true,
        'modified' => true,
    ];


    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
