<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentsExamSplitSection Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_split_section_id
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\ExamSplitSection $exam_split_section
 */
class StudentsExamSplitSection extends Entity
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
        'exam_split_section_id' => true,
        'student' => true,
        'exam_split_section' => true,
    ];
}
