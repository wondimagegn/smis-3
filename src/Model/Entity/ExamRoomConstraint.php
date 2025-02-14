<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamRoomConstraint Entity
 *
 * @property int $id
 * @property int $class_room_id
 * @property string|null $academic_year
 * @property string|null $semester
 * @property \Cake\I18n\FrozenDate|null $exam_date
 * @property int|null $session
 * @property bool|null $active
 *
 * @property \App\Model\Entity\ClassRoom $class_room
 */
class ExamRoomConstraint extends Entity
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
        'academic_year' => true,
        'semester' => true,
        'exam_date' => true,
        'session' => true,
        'active' => true,
        'class_room' => true,
    ];
}
