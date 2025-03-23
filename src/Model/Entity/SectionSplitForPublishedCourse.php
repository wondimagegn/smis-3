<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SectionSplitForPublishedCourse Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property int $section_id
 * @property string|null $type
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\CourseSplitSection[] $course_split_sections
 */
class SectionSplitForPublishedCourse extends Entity
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
        'section_id' => true,
        'type' => true,
        'published_course' => true,
        'section' => true,
        'course_split_sections' => true,
    ];
}
