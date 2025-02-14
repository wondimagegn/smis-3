<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseExemption extends Entity
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
        'request_date' => true,
        'reason' => true,
        'taken_course_title' => true,
        'taken_course_code' => true,
        'course_taken_credit' => true,
        'department_accept_reject' => true,
        'department_reason' => true,
        'registrar_confirm_deny' => true,
        'registrar_reason' => true,
        'department_approve_by' => true,
        'registrar_approve_by' => true,
        'course_id' => true,
        'student_id' => true,
        'transfer_from' => true,
        'grade' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'student' => true,
        'excluded_course_from_transcripts' => true,
    ];
}
