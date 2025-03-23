<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class OfficialTranscriptRequest extends Entity
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
        'father_name' => true,
        'grand_father' => true,
        'email' => true,
        'mobile_phone' => true,
        'studentnumber' => true,
        'admissiontype' => true,
        'degreetype' => true,
        'institution_name' => true,
        'institution_address' => true,
        'recipent_country' => true,
        'request_processed' => true,
        'created' => true,
        'modified' => true,
        'official_request_statuses' => true,
    ];
    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {
        return $this->first_name . ' ' . $this->father_name . ' ' . $this->grand_father;
    }
}
