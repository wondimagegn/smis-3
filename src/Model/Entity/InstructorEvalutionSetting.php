<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstructorEvalutionSetting Entity
 *
 * @property int $id
 * @property string $academic_year
 * @property float $head_percent
 * @property float $colleague_percent
 * @property float $student_percent
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class InstructorEvalutionSetting extends Entity
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
        'academic_year' => true,
        'head_percent' => true,
        'colleague_percent' => true,
        'student_percent' => true,
        'created' => true,
        'modified' => true,
    ];
}
