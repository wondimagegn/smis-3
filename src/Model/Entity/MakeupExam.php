<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MakeupExam Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $minute_number
 * @property int $published_course_id
 * @property int|null $course_registration_id
 * @property int|null $course_add_id
 * @property float|null $result
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\CourseRegistration $course_registration
 * @property \App\Model\Entity\CourseAdd $course_add
 * @property \App\Model\Entity\ExamGradeChange[] $exam_grade_changes
 * @property \App\Model\Entity\ExamGrade[] $exam_grades
 * @property \App\Model\Entity\ExamResult[] $exam_results
 * @property \App\Model\Entity\RejectedExamGrade[] $rejected_exam_grades
 */
class MakeupExam extends Entity
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
        'student_id' => true,
        'minute_number' => true,
        'published_course_id' => true,
        'course_registration_id' => true,
        'course_add_id' => true,
        'result' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'published_course' => true,
        'course_registration' => true,
        'course_add' => true,
        'exam_grade_changes' => true,
        'exam_grades' => true,
        'exam_results' => true,
        'rejected_exam_grades' => true,
    ];
}
