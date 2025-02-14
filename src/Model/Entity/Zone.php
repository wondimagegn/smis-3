<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Zone Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $short
 * @property int|null $region_id
 * @property string|null $zone
 * @property string|null $zone_2nd_language
 * @property int|null $priority_order
 * @property int $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Region $region
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 * @property \App\Model\Entity\City[] $cities
 * @property \App\Model\Entity\Contact[] $contacts
 * @property \App\Model\Entity\Staff[] $staffs
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Woreda[] $woredas
 */
class Zone extends Entity
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
        'region_id' => true,
        'zone' => true,
        'zone_2nd_language' => true,
        'priority_order' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'region' => true,
        'accepted_students' => true,
        'cities' => true,
        'contacts' => true,
        'staffs' => true,
        'students' => true,
        'woredas' => true,
    ];
}
