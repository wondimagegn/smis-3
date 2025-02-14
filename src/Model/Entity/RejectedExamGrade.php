<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RejectedExamGrade Entity
 *
 * @property int $id
 * @property string $grade
 * @property int|null $course_registration_id
 * @property int|null $course_add_id
 * @property int $course_instructor_assignment_id
 * @property int|null $makeup_exam_id
 * @property int $grade_scale_id
 * @property bool|null $department_approval
 * @property string|null $department_reason
 * @property \Cake\I18n\FrozenDate $department_approval_date
 * @property string|null $department_approved_by
 * @property bool|null $registrar_approval
 * @property string|null $registrar_reason
 * @property \Cake\I18n\FrozenDate $registrar_approval_date
 * @property string|null $registrar_approved_by
 * @property \Cake\I18n\FrozenTime $grade_created
 * @property \Cake\I18n\FrozenTime $grade_modified
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\CourseRegistration $course_registration
 * @property \App\Model\Entity\CourseAdd $course_add
 * @property \App\Model\Entity\CourseInstructorAssignment $course_instructor_assignment
 * @property \App\Model\Entity\MakeupExam $makeup_exam
 * @property \App\Model\Entity\GradeScale $grade_scale
 */
class RejectedExamGrade extends Entity
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
        'department_approval' => true,
        'department_reason' => true,
        'department_approval_date' => true,
        'department_approved_by' => true,
        'registrar_approval' => true,
        'registrar_reason' => true,
        'registrar_approval_date' => true,
        'registrar_approved_by' => true,
        'grade_created' => true,
        'grade_modified' => true,
        'created' => true,
        'modified' => true,
        'course_registration' => true,
        'course_add' => true,
        'course_instructor_assignment' => true,
        'makeup_exam' => true,
        'grade_scale' => true,
    ];
}
