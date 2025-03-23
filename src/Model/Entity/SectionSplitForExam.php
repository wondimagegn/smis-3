<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SectionSplitForExam Entity
 *
 * @property int $id
 * @property int $section_id
 * @property int $published_course_id
 * @property string|null $type
 *
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\ExamSplitSection[] $exam_split_sections
 */
class SectionSplitForExam extends Entity
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
        'section_id' => true,
        'published_course_id' => true,
        'type' => true,
        'section' => true,
        'published_course' => true,
        'exam_split_sections' => true,
    ];
}
