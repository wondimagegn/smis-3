<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MoodleCourse Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property string $course_title
 * @property string|null $course_code_pid
 * @property int|null $category_id
 * @property string|null $ac_year
 * @property string|null $semester
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Category $category
 */
class MoodleCourse extends Entity
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
        'published_course_id' => true,
        'course_title' => true,
        'course_code_pid' => true,
        'category_id' => true,
        'ac_year' => true,
        'semester' => true,
        'created' => true,
        'modified' => true,
        'published_course' => true,
        'category' => true,
    ];
}
