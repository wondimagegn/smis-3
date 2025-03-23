<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HistoricalStudentExamStatus Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $semester
 * @property string $academic_year
 * @property float $grade_point_sum
 * @property float $credit_hour_sum
 * @property float $m_grade_point_sum
 * @property float $m_credit_hour_sum
 * @property float $sgpa
 * @property float|null $cgpa
 * @property float $mcgpa
 * @property int|null $academic_status_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\AcademicStatus $academic_status
 */
class HistoricalStudentExamStatus extends Entity
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
        'semester' => true,
        'academic_year' => true,
        'grade_point_sum' => true,
        'credit_hour_sum' => true,
        'm_grade_point_sum' => true,
        'm_credit_hour_sum' => true,
        'sgpa' => true,
        'cgpa' => true,
        'mcgpa' => true,
        'academic_status_id' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'academic_status' => true,
    ];
}
