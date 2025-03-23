<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GradeScaleDetail Entity
 *
 * @property int $id
 * @property float $minimum_result
 * @property float $maximum_result
 * @property int $grade_scale_id
 * @property int $grade_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\GradeScale $grade_scale
 * @property \App\Model\Entity\Grade $grade
 */
class GradeScaleDetail extends Entity
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
        'minimum_result' => true,
        'maximum_result' => true,
        'grade_scale_id' => true,
        'grade_id' => true,
        'created' => true,
        'modified' => true,
        'grade_scale' => true,
        'grade' => true,
    ];
}
