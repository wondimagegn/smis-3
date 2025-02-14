<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MedicalHistory Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $user_id
 * @property string|null $record_type
 * @property string|null $details
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\User $user
 */
class MedicalHistory extends Entity
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
        'user_id' => true,
        'record_type' => true,
        'details' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'user' => true,
    ];
}
