<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExitExam Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $course_id
 * @property string $type
 * @property float $result
 * @property \Cake\I18n\FrozenDate|null $exam_date
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Course $course
 */
class ExitExam extends Entity
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
        'course_id' => true,
        'type' => true,
        'result' => true,
        'exam_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'course' => true,
    ];
}
