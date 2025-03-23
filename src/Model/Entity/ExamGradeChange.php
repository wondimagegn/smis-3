<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamGradeChange Entity
 *
 * @property int $id
 * @property int $exam_grade_id
 * @property string $grade
 * @property string $reason
 * @property string $minute_number
 * @property int|null $makeup_exam_id
 * @property float|null $makeup_exam_result
 * @property float|null $result
 * @property bool $manual_ng_conversion
 * @property string|null $manual_ng_converted_by
 * @property bool $auto_ng_conversion
 * @property bool $initiated_by_department
 * @property bool $department_reply
 * @property int|null $department_approval
 * @property string $department_reason
 * @property \Cake\I18n\FrozenTime|null $department_approval_date
 * @property string $department_approved_by
 * @property int|null $registrar_approval
 * @property string $registrar_reason
 * @property \Cake\I18n\FrozenTime|null $registrar_approval_date
 * @property string $registrar_approved_by
 * @property int|null $college_approval
 * @property string $college_reason
 * @property \Cake\I18n\FrozenTime|null $college_approval_date
 * @property string $college_approved_by
 * @property bool $cheating
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ExamGrade $exam_grade
 * @property \App\Model\Entity\MakeupExam $makeup_exam
 */
class ExamGradeChange extends Entity
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
        'exam_grade_id' => true,
        'grade' => true,
        'reason' => true,
        'minute_number' => true,
        'makeup_exam_id' => true,
        'makeup_exam_result' => true,
        'result' => true,
        'manual_ng_conversion' => true,
        'manual_ng_converted_by' => true,
        'auto_ng_conversion' => true,
        'initiated_by_department' => true,
        'department_reply' => true,
        'department_approval' => true,
        'department_reason' => true,
        'department_approval_date' => true,
        'department_approved_by' => true,
        'registrar_approval' => true,
        'registrar_reason' => true,
        'registrar_approval_date' => true,
        'registrar_approved_by' => true,
        'college_approval' => true,
        'college_reason' => true,
        'college_approval_date' => true,
        'college_approved_by' => true,
        'cheating' => true,
        'created' => true,
        'modified' => true,
        'exam_grade' => true,
        'makeup_exam' => true,
    ];
}
