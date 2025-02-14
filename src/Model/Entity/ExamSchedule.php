<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamSchedule Entity
 *
 * @property int $id
 * @property int|null $class_room_id
 * @property int|null $exam_split_section_id
 * @property int|null $published_course_id
 * @property string|null $acadamic_year
 * @property string|null $semester
 * @property \Cake\I18n\FrozenDate|null $exam_date
 * @property int|null $session
 *
 * @property \App\Model\Entity\ClassRoom $class_room
 * @property \App\Model\Entity\ExamSplitSection $exam_split_section
 * @property \App\Model\Entity\PublishedCourse $published_course
 * @property \App\Model\Entity\Invigilator[] $invigilators
 */
class ExamSchedule extends Entity
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
        'class_room_id' => true,
        'exam_split_section_id' => true,
        'published_course_id' => true,
        'acadamic_year' => true,
        'semester' => true,
        'exam_date' => true,
        'session' => true,
        'class_room' => true,
        'exam_split_section' => true,
        'published_course' => true,
        'invigilators' => true,
    ];
}
