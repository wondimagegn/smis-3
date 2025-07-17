<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Alumnus Entity
 */
class Alumnus extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'student_id' => true,
        'full_name' => true,
        'father_name' => true,
        'region' => true,
        'woreda' => true,
        'housenumber' => true,
        'email' => true,
        'mobile' => true,
        'sex' => true,
        'placeofbirthregion' => true,
        'fieldofstudy' => true,
        'age' => true,
        'student' => true,
        'alumni_responses' => true
    ];
}
