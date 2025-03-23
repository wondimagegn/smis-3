<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MoodleCourseEnrollment Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property string $username
 * @property string|null $user_role
 * @property string|null $academicyear
 * @property string|null $semester
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 */
class MoodleCourseEnrollment extends Entity
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
        'username' => true,
        'user_role' => true,
        'academicyear' => true,
        'semester' => true,
        'created' => true,
        'modified' => true,
        'published_course' => true,
    ];
}
