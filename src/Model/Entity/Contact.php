<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Contact extends Entity
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
        'staff_id' => true,
        'first_name' => true,
        'middle_name' => true,
        'last_name' => true,
        'primary_contact' => true,
        'relationship' => true,
        'country_id' => true,
        'region_id' => true,
        'zone_id' => true,
        'woreda_id' => true,
        'city_id' => true,
        'address1' => true,
        'email' => true,
        'alternative_email' => true,
        'phone_home' => true,
        'phone_office' => true,
        'phone_mobile' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'staff' => true,
        'country' => true,
        'region' => true,
        'zone' => true,
        'woreda' => true,
        'city' => true,
    ];
}
