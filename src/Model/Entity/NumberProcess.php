<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NumberProcess Entity
 *
 * @property int $id
 * @property string $user_id
 * @property string $initiated_by
 * @property int $number_running_process
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class NumberProcess extends Entity
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
        'initiated_by' => true,
        'number_running_process' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];
}
