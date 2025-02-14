<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SenateList Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $minute_number
 * @property \Cake\I18n\FrozenDate $approved_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class SenateList extends Entity
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
        'minute_number' => true,
        'approved_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
