<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UnschedulePublishedCourse Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property int|null $course_split_section_id
 * @property int $period_length
 * @property string $type
 * @property string|null $description
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\CourseSplitSection $course_split_section
 */
class UnschedulePublishedCourse extends Entity
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
        'course_split_section_id' => true,
        'period_length' => true,
        'type' => true,
        'description' => true,
        'created' => true,
        'modified' => true,
        'published_course' => true,
        'course_split_section' => true,
    ];
}
