<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Withdrawal Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $reason
 * @property \Cake\I18n\FrozenDate $acceptance_date
 * @property int $forced_withdrawal
 * @property string $minute_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class Withdrawal extends Entity
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
        'student_id' => true,
        'reason' => true,
        'acceptance_date' => true,
        'forced_withdrawal' => true,
        'minute_number' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
