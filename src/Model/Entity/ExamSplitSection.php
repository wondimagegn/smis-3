<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamSplitSection Entity
 *
 * @property int $id
 * @property int $section_split_for_exam_id
 * @property string $section_name
 *
 * @property \App\Model\Entity\SectionSplitForExam $section_split_for_exam
 * @property \App\Model\Entity\ExamSchedule[] $exam_schedules
 * @property \App\Model\Entity\Student[] $students
 */
class ExamSplitSection extends Entity
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
        'section_split_for_exam_id' => true,
        'section_name' => true,
        'section_split_for_exam' => true,
        'exam_schedules' => true,
        'students' => true,
    ];
}
