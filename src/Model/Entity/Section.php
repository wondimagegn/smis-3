<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Section Entity
 *
 * @property int $id
 * @property string $name
 * @property int $college_id
 * @property int|null $department_id
 * @property string|null $year_level_id
 * @property string $academicyear
 * @property int|null $program_id
 * @property int|null $program_type_id
 * @property int|null $curriculum_id
 * @property bool $archive
 * @property int|null $previous_section_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\YearLevel $year_level
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\Curriculum $curriculum
 * @property \App\Model\Entity\PreviousSection $previous_section
 * @property \App\Model\Entity\CourseInstructorAssignment[] $course_instructor_assignments
 * @property \App\Model\Entity\CourseRegistration[] $course_registrations
 * @property \App\Model\Entity\CourseSchedule[] $course_schedules
 * @property \App\Model\Entity\ExamType[] $exam_types
 * @property \App\Model\Entity\MergedSectionsExam[] $merged_sections_exams
 * @property \App\Model\Entity\PublishedCourse[] $published_courses
 * @property \App\Model\Entity\SectionSplitForExam[] $section_split_for_exams
 * @property \App\Model\Entity\SectionSplitForPublishedCourse[] $section_split_for_published_courses
 * @property \App\Model\Entity\Student[] $students
 */
class Section extends Entity
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
        'college_id' => true,
        'department_id' => true,
        'year_level_id' => true,
        'academicyear' => true,
        'program_id' => true,
        'program_type_id' => true,
        'curriculum_id' => true,
        'archive' => true,
        'previous_section_id' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'department' => true,
        'year_level' => true,
        'program' => true,
        'program_type' => true,
        'curriculum' => true,
        'previous_section' => true,
        'course_instructor_assignments' => true,
        'course_registrations' => true,
        'course_schedules' => true,
        'exam_types' => true,
        'merged_sections_exams' => true,
        'published_courses' => true,
        'section_split_for_exams' => true,
        'section_split_for_published_courses' => true,
        'students' => true,
    ];
}
