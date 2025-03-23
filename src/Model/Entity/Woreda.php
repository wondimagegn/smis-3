<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Woreda Entity
 *
 * @property int $id
 * @property string $name
 * @property int $code
 * @property string|null $woreda
 * @property int $zone_id
 * @property string|null $woreda_2nd_language
 * @property int|null $priority_order
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 * @property \App\Model\Entity\Contact[] $contacts
 * @property \App\Model\Entity\Staff[] $staffs
 * @property \App\Model\Entity\Student[] $students
 */
class Woreda extends Entity
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
        'code' => true,
        'woreda' => true,
        'zone_id' => true,
        'woreda_2nd_language' => true,
        'priority_order' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'zone' => true,
        'accepted_students' => true,
        'contacts' => true,
        'staffs' => true,
        'students' => true,
    ];
}
