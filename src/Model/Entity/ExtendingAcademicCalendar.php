<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExtendingAcademicCalendar Entity
 *
 * @property int $id
 * @property int $academic_calendar_id
 * @property string $year_level_id
 * @property string $department_id
 * @property int $program_id
 * @property int $program_type_id
 * @property string $activity_type
 * @property int $days
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\AcademicCalendar $academic_calendar
 * @property \App\Model\Entity\YearLevel $year_level
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class ExtendingAcademicCalendar extends Entity
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
        'academic_calendar_id' => true,
        'year_level_id' => true,
        'department_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'activity_type' => true,
        'days' => true,
        'created' => true,
        'modified' => true,
        'academic_calendar' => true,
        'year_level' => true,
        'department' => true,
        'program' => true,
        'program_type' => true,
    ];
}
