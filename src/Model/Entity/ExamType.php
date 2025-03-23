<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamType Entity
 *
 * @property int $id
 * @property string $exam_name
 * @property float $percent
 * @property int|null $order
 * @property bool $mandatory
 * @property int $published_course_id
 * @property int $section_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Section $section
 * @property \App\Model\Entity\ExamResult[] $exam_results
 */
class ExamType extends Entity
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
        'exam_name' => true,
        'percent' => true,
        'order' => true,
        'mandatory' => true,
        'published_course_id' => true,
        'section_id' => true,
        'created' => true,
        'modified' => true,
        'published_course' => true,
        'section' => true,
        'exam_results' => true,
    ];
}
