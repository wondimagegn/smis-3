<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GradeScalePublishedCourse Entity
 *
 * @property int $id
 * @property int $grade_scale_id
 * @property int $published_course_id
 * @property string $academic_year
 * @property string $semester
 *
 * @property \App\Model\Entity\GradeScale $grade_scale
 * @property \App\Model\Entity\PublishedCourse $published_course
 */
class GradeScalePublishedCourse extends Entity
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
        'grade_scale_id' => true,
        'published_course_id' => true,
        'academic_year' => true,
        'semester' => true,
        'grade_scale' => true,
        'published_course' => true,
    ];
}
