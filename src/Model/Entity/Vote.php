<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Vote Entity
 *
 * @property int $id
 * @property string $task
 * @property string $requester_user_id
 * @property string $applicable_on_user_id
 * @property string $data
 * @property bool $confirmation
 * @property \Cake\I18n\FrozenTime|null $confirmation_date
 * @property string|null $confirmed_by
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\RequesterUser $requester_user
 * @property \App\Model\Entity\ApplicableOnUser $applicable_on_user
 */
class Vote extends Entity
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
        'task' => true,
        'requester_user_id' => true,
        'applicable_on_user_id' => true,
        'data' => true,
        'confirmation' => true,
        'confirmation_date' => true,
        'confirmed_by' => true,
        'created' => true,
        'modified' => true,
        'requester_user' => true,
        'applicable_on_user' => true,
    ];
}
