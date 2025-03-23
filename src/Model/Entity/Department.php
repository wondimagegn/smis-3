<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Department extends Entity
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
        'name' => true,
        'shortname' => true,
        'name_start_date' => true,
        'name_end_date' => true,
        'college_id' => true,
        'amharic_name' => true,
        'amharic_short_name' => true,
        'applay' => true,
        'active' => true,
        'type' => true,
        'type_amharic' => true,
        'description' => true,
        'phone' => true,
        'institution_code' => true,
        'allow_year_based_curriculums' => true,
        'moodle_category_id' => true,
        'accept_course_dispatch' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'moodle_category' => true,
        'academic_calendars' => true,
        'accepted_students' => true,
        'courses' => true,
        'curriculums' => true,
        'department_study_programs' => true,
        'department_transfers' => true,
        'extending_academic_calendars' => true,
        'notes' => true,
        'offers' => true,
        'online_applicants' => true,
        'other_academic_rules' => true,
        'participating_departments' => true,
        'preferences' => true,
        'published_courses' => true,
        'sections' => true,
        'specializations' => true,
        'staff_assignes' => true,
        'staffs' => true,
        'students' => true,
        'taken_properties' => true,
        'type_credits' => true,
        'year_levels' => true,
    ];
}
