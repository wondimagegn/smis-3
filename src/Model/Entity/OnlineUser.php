<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OnlineUser Entity
 *
 * @property int $id
 * @property string $user_id
 * @property string $ip
 * @property string $browser
 * @property string $last_page_url
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class OnlineUser extends Entity
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
        'ip' => true,
        'browser' => true,
        'last_page_url' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];
}
