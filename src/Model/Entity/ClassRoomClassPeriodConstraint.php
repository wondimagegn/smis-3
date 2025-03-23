<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClassRoomClassPeriodConstraint Entity
 *
 * @property int $id
 * @property int $class_room_id
 * @property int $class_period_id
 * @property string $academic_year
 * @property string $semester
 * @property bool|null $active
 *
 * @property \App\Model\Entity\ClassRoom $class_room
 * @property \App\Model\Entity\ClassPeriod $class_period
 */
class ClassRoomClassPeriodConstraint extends Entity
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
        'class_period_id' => true,
        'academic_year' => true,
        'semester' => true,
        'active' => true,
        'class_room' => true,
        'class_period' => true,
    ];
}
