<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MergedSectionsExam Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property int $section_id
 * @property string $section_name
 * @property int $merge_key
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Section $section
 */
class MergedSectionsExam extends Entity
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
        'section_name' => true,
        'merge_key' => true,
        'published_course' => true,
        'section' => true,
    ];
}
