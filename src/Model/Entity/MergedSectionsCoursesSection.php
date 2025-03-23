<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MergedSectionsCoursesSection Entity
 *
 * @property int $merged_sections_course_id
 * @property int $section_id
 * @property int $id
 *
 * @property \App\Model\Entity\MergedSectionsCourse $merged_sections_course
 * @property \App\Model\Entity\Section $section
 */
class MergedSectionsCoursesSection extends Entity
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
        'merged_sections_course_id' => true,
        'section_id' => true,
        'merged_sections_course' => true,
        'section' => true,
    ];
}
