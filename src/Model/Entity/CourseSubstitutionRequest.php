<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseSubstitutionRequest extends Entity
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
        'student_id' => true,
        'course_for_substitued_id' => true,
        'course_be_substitued_id' => true,
        'created' => true,
        'modified' => true,
        'department_approve' => true,
        'department_approve_by' => true,
        'remark' => true,
        'student' => true,
        'course_for_substitued' => true,
        'course_be_substitued' => true,
    ];
}
