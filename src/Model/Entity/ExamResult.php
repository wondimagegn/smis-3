<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamResult Entity
 *
 * @property int $id
 * @property float $result
 * @property int $exam_type_id
 * @property int|null $course_registration_id
 * @property int|null $course_add_id
 * @property int|null $makeup_exam_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\CourseAdd $course_add
 * @property \App\Model\Entity\ExamType $exam_type
 * @property \App\Model\Entity\CourseRegistration $course_registration
 * @property \App\Model\Entity\MakeupExam $makeup_exam
 */
class ExamResult extends Entity
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
        'result' => true,
        'exam_type_id' => true,
        'course_registration_id' => true,
        'course_add_id' => true,
        'makeup_exam_id' => true,
        'course_add' => true,
        'created' => true,
        'modified' => true,
        'exam_type' => true,
        'course_registration' => true,
        'makeup_exam' => true,
    ];
}
