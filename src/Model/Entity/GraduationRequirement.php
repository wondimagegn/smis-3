<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GraduationRequirement Entity
 *
 * @property int $id
 * @property float $cgpa
 * @property int $program_id
 * @property string $academic_year
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 */
class GraduationRequirement extends Entity
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
        'cgpa' => true,
        'program_id' => true,
        'academic_year' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
    ];
}
