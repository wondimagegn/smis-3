<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property string|null $model
 * @property string|null $foreign_key
 * @property string|null $user_id
 * @property string|null $ip
 * @property string|null $description
 * @property string|null $action
 * @property string|null $change
 *
 * @property \App\Model\Entity\User $user
 */
class Log extends Entity
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
        'created' => true,
        'model' => true,
        'foreign_key' => true,
        'user_id' => true,
        'ip' => true,
        'description' => true,
        'action' => true,
        'change' => true,
        'user' => true,
    ];
}
