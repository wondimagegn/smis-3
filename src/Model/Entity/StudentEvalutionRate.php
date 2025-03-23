<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentEvalutionRate Entity
 *
 * @property int $id
 * @property int $instructor_evalution_question_id
 * @property int $student_id
 * @property int $published_course_id
 * @property int $rating
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\InstructorEvalutionQuestion $instructor_evalution_question
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\PublishedCourse $published_course
 */
class StudentEvalutionRate extends Entity
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
        'instructor_evalution_question_id' => true,
        'student_id' => true,
        'published_course_id' => true,
        'rating' => true,
        'created' => true,
        'modified' => true,
        'instructor_evalution_question' => true,
        'student' => true,
        'published_course' => true,
    ];
}
