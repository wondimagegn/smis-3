<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstructorExamExcludeDateConstraint Entity
 *
 * @property int $id
 * @property int|null $staff_id
 * @property int|null $staff_for_exam_id
 * @property string|null $academic_year
 * @property string|null $semester
 * @property \Cake\I18n\FrozenDate|null $exam_date
 * @property int|null $session
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\StaffForExam $staff_for_exam
 */
class InstructorExamExcludeDateConstraint extends Entity
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
        'staff_id' => true,
        'staff_for_exam_id' => true,
        'academic_year' => true,
        'semester' => true,
        'exam_date' => true,
        'session' => true,
        'staff' => true,
        'staff_for_exam' => true,
    ];
}
