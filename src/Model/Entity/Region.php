<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Region Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $short
 * @property string $description
 * @property int $country_id
 * @property string|null $region_type
 * @property string|null $region_2nd_language
 * @property int|null $priority_order
 * @property int $active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Country $country
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 * @property \App\Model\Entity\City[] $cities
 * @property \App\Model\Entity\Contact[] $contacts
 * @property \App\Model\Entity\HighSchoolEducationBackground[] $high_school_education_backgrounds
 * @property \App\Model\Entity\Staff[] $staffs
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Zone[] $zones
 */
class Region extends Entity
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
        'name' => true,
        'short' => true,
        'description' => true,
        'country_id' => true,
        'region_type' => true,
        'region_2nd_language' => true,
        'priority_order' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'country' => true,
        'accepted_students' => true,
        'cities' => true,
        'contacts' => true,
        'high_school_education_backgrounds' => true,
        'staffs' => true,
        'students' => true,
        'zones' => true,
    ];
}
