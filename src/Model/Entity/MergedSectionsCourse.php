<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MergedSectionsCourse Entity
 *
 * @property int $id
 * @property int $published_course_id
 * @property string|null $section_name
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Section[] $sections
 */
class MergedSectionsCourse extends Entity
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
        'section_name' => true,
        'published_course' => true,
        'sections' => true,
    ];
}
