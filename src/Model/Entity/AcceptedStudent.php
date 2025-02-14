<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;


class AcceptedStudent extends Entity
{

    protected $_accessible = [
        'first_name' => true,
        'middle_name' => true,
        'last_name' => true,
        'sex' => true,
        'studentnumber' => true,
        'region_id' => true,
        'zone_id' => true,
        'woreda_id' => true,
        'assignment_type' => true,
        'EHEECE_total_results' => true,
        'freshman_result' => true,
        'college_id' => true,
        'campus_id' => true,
        'original_college_id' => true,
        'department_id' => true,
        'high_school' => true,
        'moeadmissionnumber' => true,
        'benefit_group' => true,
        'curriculum_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'academicyear' => true,
        'specialization_id' => true,
        'Placement_Approved_By_Department' => true,
        'minute_number' => true,
        'applicationstatus' => true,
        'currentstatus' => true,
        'disability' => true,
        'placementtype' => true,
        'placement_type_id' => true,
        'user_id' => true,
        'placement_based' => true,
        'online_applicant_id' => true,
        'disability_id' => true,
        'foreign_program_id' => true,
        'created' => true,
        'modified' => true,
        'region' => true,
        'zone' => true,
        'woreda' => true,
        'college' => true,
        'campus' => true,
        'department' => true,
        'curriculum' => true,
        'program' => true,
        'program_type' => true,
        'specialization' => true,
        'placement_type' => true,
        'user' => true,
        'online_applicant' => true,
        'dormitory_assignments' => true,
        'meal_hall_assignments' => true,
        'placement_entrance_exam_result_entries' => true,
        'placement_participating_students' => true,
        'placement_preferences' => true,
        'preferences' => true,
        'students' => true,
    ];

    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

}
