<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Securitysetting Entity
 *
 * @property int $id
 * @property int $session_duration
 * @property int $password_strength
 * @property int $minimum_password_length
 * @property int $maximum_password_length
 * @property int $password_duration
 * @property bool $previous_password_use_allowance
 * @property int $number_of_login_attempt
 * @property int $attempt_period
 * @property int $falsify_duration
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Securitysetting extends Entity
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
        'session_duration' => true,
        'password_strength' => true,
        'minimum_password_length' => true,
        'maximum_password_length' => true,
        'password_duration' => true,
        'previous_password_use_allowance' => true,
        'number_of_login_attempt' => true,
        'attempt_period' => true,
        'falsify_duration' => true,
        'created' => true,
        'modified' => true,
    ];
}
