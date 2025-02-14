<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Staff extends Entity
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
        'servicewing' => true,
        'staffid' => true,
        'college_id' => true,
        'position_id' => true,
        'education' => true,
        'department_id' => true,
        'title_id' => true,
        'first_name' => true,
        'middle_name' => true,
        'last_name' => true,
        'user_id' => true,
        'ethnicity' => true,
        'birthdate' => true,
        'gender' => true,
        'country_id' => true,
        'region_id' => true,
        'zone_id' => true,
        'woreda_id' => true,
        'city_id' => true,
        'address' => true,
        'email' => true,
        'alternative_email' => true,
        'phone_home' => true,
        'phone_office' => true,
        'phone_mobile' => true,
        'pobox' => true,
        'active' => true,
        'service_wing_id' => true,
        'education_id' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'position' => true,
        'department' => true,
        'title' => true,
        'user' => true,
        'country' => true,
        'region' => true,
        'zone' => true,
        'woreda' => true,
        'city' => true,
        'service_wing' => true,
        'colleague_evalution_rates' => true,
        'contacts' => true,
        'course_instructor_assignments' => true,
        'instructor_class_period_course_constraints' => true,
        'instructor_exam_exclude_date_constraints' => true,
        'instructor_number_of_exam_constraints' => true,
        'invigilators' => true,
        'offices' => true,
        'staff_for_exams' => true,
        'staff_studies' => true,
        'courses' => true,
    ];
    protected $_virtual = ['full_name']; // Define virtual fields
    protected function _getFullName()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
