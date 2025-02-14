<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamRoomNumberOfInvigilator Entity
 *
 * @property int $id
 * @property int $class_room_id
 * @property string $academic_year
 * @property string $semester
 * @property int $number_of_invigilator
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\ClassRoom $class_room
 */
class ExamRoomNumberOfInvigilator extends Entity
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
        'number_of_invigilator' => true,
        'created' => true,
        'modified' => true,
        'class_room' => true,
    ];
}
