<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApplicablePayment Entity
 */
class ApplicablePayment extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'student_id' => true,
        'sponsor_type' => true,
        'semester' => true,
        'academic_year' => true,
        'student' => true
    ];
}
