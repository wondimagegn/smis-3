<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TakenProperty Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int|null $office_id
 * @property int|null $college_id
 * @property int|null $department_id
 * @property string $name
 * @property \Cake\I18n\FrozenDate $taken_date
 * @property bool $returned
 * @property \Cake\I18n\FrozenDate|null $return_date
 * @property string|null $remark
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Office $office
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 */
class TakenProperty extends Entity
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
        'student_id' => true,
        'office_id' => true,
        'college_id' => true,
        'department_id' => true,
        'name' => true,
        'taken_date' => true,
        'returned' => true,
        'return_date' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'office' => true,
        'college' => true,
        'department' => true,
    ];
}
