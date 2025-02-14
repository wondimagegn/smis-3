<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Alumnus extends Entity
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
        'full_name' => true,
        'father_name' => true,
        'region' => true,
        'woreda' => true,
        'kebele' => true,
        'housenumber' => true,
        'mobile' => true,
        'home_second_phone' => true,
        'email' => true,
        'facebookaddress' => true,
        'studentnumber' => true,
        'sex' => true,
        'placeofbirthregion' => true,
        'placeofbirthworeda' => true,
        'fieldofstudy' => true,
        'age' => true,
        'gradution_academic_year' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
