<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MoodleUser Entity
 *
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string|null $middlename
 * @property string|null $institution
 * @property string|null $department
 * @property string|null $idnumber
 * @property string|null $password
 * @property string|null $email
 * @property string|null $user_id
 * @property string|null $description
 * @property string|null $mobile
 * @property string|null $phone
 * @property string|null $address
 * @property int|null $role_id
 * @property int|null $table_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Table $table
 */
class MoodleUser extends Entity
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
        'firstname' => true,
        'lastname' => true,
        'middlename' => true,
        'institution' => true,
        'department' => true,
        'idnumber' => true,
        'password' => true,
        'email' => true,
        'user_id' => true,
        'description' => true,
        'mobile' => true,
        'phone' => true,
        'address' => true,
        'role_id' => true,
        'table_id' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'role' => true,
        'table' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];
}
