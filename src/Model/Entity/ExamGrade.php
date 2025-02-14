<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamGrade Entity
 *
 * @property int $id
 * @property string $grade
 * @property int|null $course_registration_id
 * @property int|null $course_add_id
 * @property int|null $course_instructor_assignment_id
 * @property int|null $makeup_exam_id
 * @property int $grade_scale_id
 * @property bool $department_reply
 * @property int|null $department_approval
 * @property string|null $department_reason
 * @property \Cake\I18n\FrozenTime|null $department_approval_date
 * @property string|null $department_approved_by
 * @property int|null $registrar_approval
 * @property string|null $registrar_reason
 * @property \Cake\I18n\FrozenTime|null $registrar_approval_date
 * @property string|null $registrar_approved_by
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\CourseRegistration $course_registration
 * @property \App\Model\Entity\CourseAdd $course_add
 * @property \App\Model\Entity\CourseInstructorAssignment $course_instructor_assignment
 * @property \App\Model\Entity\MakeupExam $makeup_exam
 * @property \App\Model\Entity\GradeScale $grade_scale
 * @property \App\Model\Entity\ExamGradeChange[] $exam_grade_changes
 */
class ExamGrade extends Entity
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
        'grade' => true,
        'course_registration_id' => true,
        'course_add_id' => true,
        'course_instructor_assignment_id' => true,
        'makeup_exam_id' => true,
        'grade_scale_id' => true,
        'department_reply' => true,
        'department_approval' => true,
        'department_reason' => true,
        'department_approval_date' => true,
        'department_approved_by' => true,
        'registrar_approval' => true,
        'registrar_reason' => true,
        'registrar_approval_date' => true,
        'registrar_approved_by' => true,
        'created' => true,
        'modified' => true,
        'course_registration' => true,
        'course_add' => true,
        'course_instructor_assignment' => true,
        'makeup_exam' => true,
        'grade_scale' => true,
        'exam_grade_changes' => true,
    ];
}
