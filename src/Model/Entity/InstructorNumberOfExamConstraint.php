<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstructorNumberOfExamConstraint Entity
 *
 * @property int $id
 * @property int|null $staff_id
 * @property int|null $staff_for_exam_id
 * @property int $college_id
 * @property string|null $academic_year
 * @property string|null $semester
 * @property string|null $year_level_id
 * @property int|null $max_number_of_exam
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\StaffForExam $staff_for_exam
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\YearLevel $year_level
 */
class InstructorNumberOfExamConstraint extends Entity
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
        'college_id' => true,
        'academic_year' => true,
        'semester' => true,
        'year_level_id' => true,
        'max_number_of_exam' => true,
        'staff' => true,
        'staff_for_exam' => true,
        'college' => true,
        'year_level' => true,
    ];
}
