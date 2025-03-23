<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GradeType Entity
 *
 * @property int $id
 * @property string $type
 * @property bool $used_in_gpa
 * @property bool $scale_required
 * @property bool $active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Course[] $courses
 * @property \App\Model\Entity\GradeScale[] $grade_scales
 * @property \App\Model\Entity\Grade[] $grades
 */
class GradeType extends Entity
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
        'type' => true,
        'used_in_gpa' => true,
        'scale_required' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'courses' => true,
        'grade_scales' => true,
        'grades' => true,
    ];
}
