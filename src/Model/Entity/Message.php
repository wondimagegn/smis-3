<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity
 *
 * @property string $id
 * @property string|null $from
 * @property string|null $user_id
 * @property string|null $subject
 * @property string|null $content
 * @property string|null $model
 * @property string|null $foreign_key
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Message extends Entity
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
        'from' => true,
        'user_id' => true,
        'subject' => true,
        'content' => true,
        'model' => true,
        'foreign_key' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];
}
