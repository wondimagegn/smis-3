<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * YearLevel Entity
 *
 * @property int $id
 * @property string $name
 * @property int $department_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\AcademicCalendar[] $academic_calendars
 * @property \App\Model\Entity\AcademicStand[] $academic_stands
 * @property \App\Model\Entity\CourseAdd[] $course_adds
 * @property \App\Model\Entity\CourseDrop[] $course_drops
 * @property \App\Model\Entity\CourseRegistration[] $course_registrations
 * @property \App\Model\Entity\Course[] $courses
 * @property \App\Model\Entity\ExamPeriod[] $exam_periods
 * @property \App\Model\Entity\ExtendingAcademicCalendar[] $extending_academic_calendars
 * @property \App\Model\Entity\InstructorNumberOfExamConstraint[] $instructor_number_of_exam_constraints
 * @property \App\Model\Entity\OtherAcademicRule[] $other_academic_rules
 * @property \App\Model\Entity\PublishedCourse[] $published_courses
 * @property \App\Model\Entity\Section[] $sections
 */
class YearLevel extends Entity
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
        'department_id' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'academic_calendars' => true,
        'academic_stands' => true,
        'course_adds' => true,
        'course_drops' => true,
        'course_registrations' => true,
        'courses' => true,
        'exam_periods' => true,
        'extending_academic_calendars' => true,
        'instructor_number_of_exam_constraints' => true,
        'other_academic_rules' => true,
        'published_courses' => true,
        'sections' => true,
    ];
}
