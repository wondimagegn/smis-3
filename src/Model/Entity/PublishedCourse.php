<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PublishedCourse Entity
 *
 * @property int $id
 * @property int|null $year_level_id
 * @property string $semester
 * @property int $course_id
 * @property int $program_type_id
 * @property int $program_id
 * @property int|null $department_id
 * @property int|null $given_by_department_id
 * @property int $section_id
 * @property string|null $academic_year
 * @property bool $published
 * @property bool|null $drop
 * @property bool $add
 * @property bool $elective
 * @property \Cake\I18n\FrozenTime|null $published_up
 * @property \Cake\I18n\FrozenTime|null $published_down
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $college_id
 * @property int|null $grade_scale_id
 * @property int $lecture_number_of_session
 * @property int $lab_number_of_session
 * @property int $tutorial_number_of_session
 * @property bool $enable_for_moodle
 *
 * @property \App\Model\Entity\YearLevel $year_level
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\GivenByDepartment $given_by_department
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\GradeScale $grade_scale
 * @property \App\Model\Entity\Attendance[] $attendances
 * @property \App\Model\Entity\ClassPeriodCourseConstraint[] $class_period_course_constraints
 * @property \App\Model\Entity\ClassRoomCourseConstraint[] $class_room_course_constraints
 * @property \App\Model\Entity\CourseAdd[] $course_adds
 * @property \App\Model\Entity\CourseExamConstraint[] $course_exam_constraints
 * @property \App\Model\Entity\CourseExamGapConstraint[] $course_exam_gap_constraints
 * @property \App\Model\Entity\CourseInstructorAssignment[] $course_instructor_assignments
 * @property \App\Model\Entity\CourseRegistration[] $course_registrations
 * @property \App\Model\Entity\CourseSchedule[] $course_schedules
 * @property \App\Model\Entity\ExamRoomCourseConstraint[] $exam_room_course_constraints
 * @property \App\Model\Entity\ExamSchedule[] $exam_schedules
 * @property \App\Model\Entity\ExamType[] $exam_types
 * @property \App\Model\Entity\ExcludedPublishedCourseExam[] $excluded_published_course_exams
 * @property \App\Model\Entity\FxResitRequest[] $fx_resit_request
 * @property \App\Model\Entity\GradeScalePublishedCourse[] $grade_scale_published_courses
 * @property \App\Model\Entity\HistoricalStudentCourseGradeExclude[] $historical_student_course_grade_excludes
 * @property \App\Model\Entity\MakeupExam[] $makeup_exams
 * @property \App\Model\Entity\MergedSectionsCourse[] $merged_sections_courses
 * @property \App\Model\Entity\MergedSectionsExam[] $merged_sections_exams
 * @property \App\Model\Entity\MoodleCourseEnrollment[] $moodle_course_enrollments
 * @property \App\Model\Entity\MoodleCourse[] $moodle_courses
 * @property \App\Model\Entity\ResultEntryAssignment[] $result_entry_assignments
 * @property \App\Model\Entity\SectionSplitForExam[] $section_split_for_exams
 * @property \App\Model\Entity\SectionSplitForPublishedCourse[] $section_split_for_published_courses
 * @property \App\Model\Entity\StudentEvalutionComment[] $student_evalution_comments
 * @property \App\Model\Entity\StudentEvalutionRate[] $student_evalution_rates
 * @property \App\Model\Entity\UnschedulePublishedCourse[] $unschedule_published_courses
 */
class PublishedCourse extends Entity
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
        'year_level_id' => true,
        'semester' => true,
        'course_id' => true,
        'program_type_id' => true,
        'program_id' => true,
        'department_id' => true,
        'given_by_department_id' => true,
        'section_id' => true,
        'academic_year' => true,
        'published' => true,
        'drop' => true,
        'add' => true,
        'elective' => true,
        'published_up' => true,
        'published_down' => true,
        'created' => true,
        'modified' => true,
        'college_id' => true,
        'grade_scale_id' => true,
        'lecture_number_of_session' => true,
        'lab_number_of_session' => true,
        'tutorial_number_of_session' => true,
        'enable_for_moodle' => true,
        'year_level' => true,
        'course' => true,
        'program_type' => true,
        'program' => true,
        'department' => true,
        'given_by_department' => true,
        'section' => true,
        'college' => true,
        'grade_scale' => true,
        'attendances' => true,
        'class_period_course_constraints' => true,
        'class_room_course_constraints' => true,
        'course_adds' => true,
        'course_exam_constraints' => true,
        'course_exam_gap_constraints' => true,
        'course_instructor_assignments' => true,
        'course_registrations' => true,
        'course_schedules' => true,
        'exam_room_course_constraints' => true,
        'exam_schedules' => true,
        'exam_types' => true,
        'excluded_published_course_exams' => true,
        'fx_resit_request' => true,
        'grade_scale_published_courses' => true,
        'historical_student_course_grade_excludes' => true,
        'makeup_exams' => true,
        'merged_sections_courses' => true,
        'merged_sections_exams' => true,
        'moodle_course_enrollments' => true,
        'moodle_courses' => true,
        'result_entry_assignments' => true,
        'section_split_for_exams' => true,
        'section_split_for_published_courses' => true,
        'student_evalution_comments' => true,
        'student_evalution_rates' => true,
        'unschedule_published_courses' => true,
    ];
}
