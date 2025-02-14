<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Grade Entity
 *
 * @property int $id
 * @property string $grade
 * @property int $grade_type_id
 * @property float $point_value
 * @property bool $pass_grade
 * @property bool $allow_repetition
 * @property bool $active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\GradeType $grade_type
 * @property \App\Model\Entity\GradeScaleDetail[] $grade_scale_details
 */
class Grade extends Entity
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
        'grade' => true,
        'grade_type_id' => true,
        'point_value' => true,
        'pass_grade' => true,
        'allow_repetition' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'grade_type' => true,
        'grade_scale_details' => true,
    ];
}
