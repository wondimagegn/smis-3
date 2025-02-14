<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class OnlineApplicant extends Entity
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
        'applicationnumber' => true,
        'college_id' => true,
        'department_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'academic_year' => true,
        'semester' => true,
        'undergraduate_university_name' => true,
        'undergraduate_university_cgpa' => true,
        'undergraduate_university_field_of_study' => true,
        'postgraduate_university_name' => true,
        'postgraduate_university_cgpa' => true,
        'postgraduate_university_field_of_study' => true,
        'financial_support' => true,
        'name_of_sponsor' => true,
        'year_of_experience' => true,
        'disability' => true,
        'first_name' => true,
        'father_name' => true,
        'grand_father_name' => true,
        'date_of_birth' => true,
        'gender' => true,
        'mobile_phone' => true,
        'email' => true,
        'application_status' => true,
        'approved_by' => true,
        'document_submitted' => true,
        'entrance_result' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'department' => true,
        'program' => true,
        'program_type' => true,
        'accepted_students' => true,
        'online_applicant_statuses' => true,
    ];

    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {
        return $this->first_name . ' ' . $this->father_name . ' ' . $this->grand_father_name;
    }
}
