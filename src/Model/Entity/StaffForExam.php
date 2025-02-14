<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StaffForExam Entity
 *
 * @property int $id
 * @property int $college_id
 * @property string|null $academic_year
 * @property string|null $semester
 * @property int $staff_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\InstructorExamExcludeDateConstraint[] $instructor_exam_exclude_date_constraints
 * @property \App\Model\Entity\InstructorNumberOfExamConstraint[] $instructor_number_of_exam_constraints
 * @property \App\Model\Entity\Invigilator[] $invigilators
 */
class StaffForExam extends Entity
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
        'college_id' => true,
        'academic_year' => true,
        'semester' => true,
        'staff_id' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'staff' => true,
        'instructor_exam_exclude_date_constraints' => true,
        'instructor_number_of_exam_constraints' => true,
        'invigilators' => true,
    ];
}
