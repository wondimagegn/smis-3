<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentNameHistory Entity
 *
 * @property int $id
 * @property string $from_first_name
 * @property string $from_middle_name
 * @property string $from_last_name
 * @property string|null $from_amharic_first_name
 * @property string|null $from_amharic_middle_name
 * @property string|null $from_amharic_last_name
 * @property string $to_first_name
 * @property string $to_middle_name
 * @property string $to_last_name
 * @property string|null $to_amharic_first_name
 * @property string|null $to_amharic_middle_name
 * @property string|null $to_amharic_last_name
 * @property int $student_id
 * @property string $minute_number
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class StudentNameHistory extends Entity
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
        'from_first_name' => true,
        'from_middle_name' => true,
        'from_last_name' => true,
        'from_amharic_first_name' => true,
        'from_amharic_middle_name' => true,
        'from_amharic_last_name' => true,
        'to_first_name' => true,
        'to_middle_name' => true,
        'to_last_name' => true,
        'to_amharic_first_name' => true,
        'to_amharic_middle_name' => true,
        'to_amharic_last_name' => true,
        'student_id' => true,
        'minute_number' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
