<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DormitoryAssignment Entity
 *
 * @property int $id
 * @property int $dormitory_id
 * @property int|null $student_id
 * @property int|null $accepted_student_id
 * @property \Cake\I18n\FrozenDate|null $leave_date
 * @property bool $received
 * @property \Cake\I18n\FrozenDate|null $received_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Dormitory $dormitory
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\AcceptedStudent $accepted_student
 */
class DormitoryAssignment extends Entity
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
        'dormitory_id' => true,
        'student_id' => true,
        'accepted_student_id' => true,
        'leave_date' => true,
        'received' => true,
        'received_date' => true,
        'created' => true,
        'modified' => true,
        'dormitory' => true,
        'student' => true,
        'accepted_student' => true,
    ];
}
