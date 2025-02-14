<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class DepartmentTransfer extends Entity
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
        'department_id' => true,
        'from_department_id' => true,
        'to_college_id' => true,
        'student_id' => true,
        'minute_number' => true,
        'sender_department_approval' => true,
        'sender_department_remark' => true,
        'sender_department_approval_date' => true,
        'sender_department_approval_by' => true,
        'transfer_request_date' => true,
        'receiver_department_approval' => true,
        'receiver_department_approval_date' => true,
        'receiver_department_remark' => true,
        'receiver_department_approval_by' => true,
        'sender_college_approval' => true,
        'sender_college_approval_date' => true,
        'sender_college_remark' => true,
        'sender_college_approval_by' => true,
        'receiver_college_approval' => true,
        'receiver_college_approval_date' => true,
        'receiver_college_remark' => true,
        'receiver_college_approval_by' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'student' => true,
    ];
}
