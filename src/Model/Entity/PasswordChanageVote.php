<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PasswordChanageVote Entity
 *
 * @property int $id
 * @property string $user_id
 * @property int $role_id
 * @property bool $is_voted
 * @property \Cake\I18n\FrozenDate $chanage_password_request_date
 * @property string|null $password
 * @property bool $done
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Role $role
 */
class PasswordChanageVote extends Entity
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
        'user_id' => true,
        'role_id' => true,
        'is_voted' => true,
        'chanage_password_request_date' => true,
        'password' => true,
        'done' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'role' => true,
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
