<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class College extends Entity
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
        'campus_id' => true,
        'name' => true,
        'shortname' => true,
        'amharic_name' => true,
        'amharic_short_name' => true,
        'description' => true,
        'phone' => true,
        'type' => true,
        'type_amharic' => true,
        'name_start_date' => true,
        'name_end_date' => true,
        'applay' => true,
        'deligate_scale' => true,
        'deligate_for_graduate_study' => true,
        'available_for_placement' => true,
        'active' => true,
        'institution_code' => true,
        'idnumber_prefix' => true,
        'stream' => true,
        'moodle_category_id' => true,
        'created' => true,
        'modified' => true,
        'campus' => true,
        'moodle_category' => true,
        'academic_calendars' => true,
        'accepted_students' => true,
        'class_periods' => true,
        'class_room_blocks' => true,
        'departments' => true,
        'exam_periods' => true,
        'instructor_class_period_course_constraints' => true,
        'instructor_number_of_exam_constraints' => true,
        'notes' => true,
        'online_applicants' => true,
        'participating_departments' => true,
        'period_settings' => true,
        'placement_locks' => true,
        'placements_results_criterias' => true,
        'preference_deadlines' => true,
        'preferences' => true,
        'published_courses' => true,
        'quotas' => true,
        'reserved_places' => true,
        'sections' => true,
        'staff_assignes' => true,
        'staff_for_exams' => true,
        'staffs' => true,
        'students' => true,
        'taken_properties' => true,
    ];
}
