<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgramTypeTransfer Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $program_type_id
 * @property string $academic_year
 * @property string $semester
 * @property \Cake\I18n\FrozenDate $transfer_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\ProgramType $program_type
 */
class ProgramTypeTransfer extends Entity
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
        'program_type_id' => true,
        'academic_year' => true,
        'semester' => true,
        'transfer_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'program_type' => true,
    ];
}
