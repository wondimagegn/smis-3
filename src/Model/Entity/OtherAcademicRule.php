<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OtherAcademicRule Entity
 *
 * @property int $id
 * @property string $department_id
 * @property int $program_id
 * @property int $program_type_id
 * @property string $year_level_id
 * @property int $curriculum_id
 * @property int $course_category_id
 * @property int $academic_status_id
 * @property string $grade
 * @property int $number_courses
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\YearLevel $year_level
 * @property \App\Model\Entity\Curriculum $curriculum
 * @property \App\Model\Entity\CourseCategory $course_category
 * @property \App\Model\Entity\AcademicStatus $academic_status
 */
class OtherAcademicRule extends Entity
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
        'program_id' => true,
        'program_type_id' => true,
        'year_level_id' => true,
        'curriculum_id' => true,
        'course_category_id' => true,
        'academic_status_id' => true,
        'grade' => true,
        'number_courses' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'program' => true,
        'program_type' => true,
        'year_level' => true,
        'curriculum' => true,
        'course_category' => true,
        'academic_status' => true,
    ];
}
