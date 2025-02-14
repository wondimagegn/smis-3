<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentStatusPattern Entity
 *
 * @property int $id
 * @property int $program_id
 * @property int $program_type_id
 * @property string $acadamic_year
 * @property \Cake\I18n\FrozenDate $application_date
 * @property int $pattern
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class StudentStatusPattern extends Entity
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
        'program_id' => true,
        'program_type_id' => true,
        'acadamic_year' => true,
        'application_date' => true,
        'pattern' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}
