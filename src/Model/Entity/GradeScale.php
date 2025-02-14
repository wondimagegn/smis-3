<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GradeScale Entity
 *
 * @property int $id
 * @property string $name
 * @property int $grade_type_id
 * @property string $model
 * @property int $foreign_key
 * @property int $program_id
 * @property bool $own
 * @property bool $one_time
 * @property bool $active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\GradeType $grade_type
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ExamGrade[] $exam_grades
 * @property \App\Model\Entity\GradeScaleDetail[] $grade_scale_details
 * @property \App\Model\Entity\GradeScalePublishedCourse[] $grade_scale_published_courses
 * @property \App\Model\Entity\PublishedCourse[] $published_courses
 * @property \App\Model\Entity\RejectedExamGrade[] $rejected_exam_grades
 */
class GradeScale extends Entity
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
        'grade_type_id' => true,
        'model' => true,
        'foreign_key' => true,
        'program_id' => true,
        'own' => true,
        'one_time' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'grade_type' => true,
        'program' => true,
        'exam_grades' => true,
        'grade_scale_details' => true,
        'grade_scale_published_courses' => true,
        'published_courses' => true,
        'rejected_exam_grades' => true,
    ];
}
