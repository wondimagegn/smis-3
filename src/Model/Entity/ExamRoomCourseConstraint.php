<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamRoomCourseConstraint Entity
 *
 * @property int $id
 * @property int $class_room_id
 * @property int $published_course_id
 * @property bool|null $active
 *
 * @property \App\Model\Entity\ClassRoom $class_room
 * @property \App\Model\Entity\PublishedCourse $published_course
 */
class ExamRoomCourseConstraint extends Entity
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
        'published_course_id' => true,
        'active' => true,
        'class_room' => true,
        'published_course' => true,
    ];
}
