<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class AcademicCalendar extends Entity
{

    protected $_accessible = [
        'academic_year' => true,
        'semester' => true,
        'college_id' => true,
        'department_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'year_level_id' => true,
        'course_registration_start_date' => true,
        'course_registration_end_date' => true,
        'course_add_start_date' => true,
        'course_add_end_date' => true,
        'course_drop_start_date' => true,
        'course_drop_end_date' => true,
        'grade_submission_start_date' => true,
        'grade_submission_end_date' => true,
        'grade_fx_submission_end_date' => true,
        'senate_meeting_date' => true,
        'graduation_date' => true,
        'online_admission_start_date' => true,
        'online_admission_end_date' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
        'extending_academic_calendar' => true,
    ];


    protected $_virtual = ['full_year'];

    protected function _getFullYear()
    {
        return $this->academic_year . '-' . $this->semester;
    }


}
