<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Otp Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $username
 * @property string $password
 * @property string $studentnumber
 * @property string $service
 * @property string|null $portal
 * @property string|null $exam_center
 * @property int $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class Otp extends Entity
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
        'username' => true,
        'password' => true,
        'studentnumber' => true,
        'service' => true,
        'portal' => true,
        'exam_center' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
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
