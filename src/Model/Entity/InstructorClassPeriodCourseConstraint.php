<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstructorClassPeriodCourseConstraint Entity
 *
 * @property int $id
 * @property int $staff_id
 * @property int $class_period_id
 * @property int $college_id
 * @property string $academic_year
 * @property string $semester
 * @property bool|null $active
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\ClassPeriod $class_period
 * @property \App\Model\Entity\College $college
 */
class InstructorClassPeriodCourseConstraint extends Entity
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
        'staff_id' => true,
        'class_period_id' => true,
        'college_id' => true,
        'academic_year' => true,
        'semester' => true,
        'active' => true,
        'staff' => true,
        'class_period' => true,
        'college' => true,
    ];
}
