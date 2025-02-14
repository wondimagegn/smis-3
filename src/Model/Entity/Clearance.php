<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Clearance extends Entity
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
        'type' => true,
        'reason' => true,
        'request_date' => true,
        'acceptance_date' => true,
        'last_class_attended_date' => true,
        'confirmed' => true,
        'forced_withdrawal' => true,
        'minute_number' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
