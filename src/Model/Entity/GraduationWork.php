<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GraduationWork Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $type
 * @property string $title
 * @property int $course_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Course $course
 */
class GraduationWork extends Entity
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
        'type' => true,
        'title' => true,
        'course_id' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'course' => true,
    ];
}
