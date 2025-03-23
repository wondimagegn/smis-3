<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Office Entity
 *
 * @property int $id
 * @property string $name
 * @property int $staff_id
 * @property string|null $address
 * @property string|null $telephone
 * @property string|null $alternative_telephone
 * @property string|null $email
 * @property string|null $alternative_email
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\TakenProperty[] $taken_properties
 */
class Office extends Entity
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
        'staff_id' => true,
        'address' => true,
        'telephone' => true,
        'alternative_telephone' => true,
        'email' => true,
        'alternative_email' => true,
        'created' => true,
        'modified' => true,
        'staff' => true,
        'taken_properties' => true,
    ];
}
