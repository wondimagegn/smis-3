<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Readmission Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $academic_year
 * @property string $semester
 * @property string|null $minute_number
 * @property int|null $registrar_approval
 * @property \Cake\I18n\FrozenDate|null $registrar_approval_date
 * @property string|null $registrar_approved_by
 * @property int|null $academic_commision_approval
 * @property string|null $academic_commission_approved_by
 * @property \Cake\I18n\FrozenDate|null $academic_commission_approval_date
 * @property string|null $remark
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class Readmission extends Entity
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
        'academic_year' => true,
        'semester' => true,
        'minute_number' => true,
        'registrar_approval' => true,
        'registrar_approval_date' => true,
        'registrar_approved_by' => true,
        'academic_commision_approval' => true,
        'academic_commission_approved_by' => true,
        'academic_commission_approval_date' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
