<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentRank Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $section_rank
 * @property string $batch_rank
 * @property string $college_rank
 * @property string $academicyear
 * @property string $semester
 * @property string $category
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class StudentRank extends Entity
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
        'section_rank' => true,
        'batch_rank' => true,
        'college_rank' => true,
        'academicyear' => true,
        'semester' => true,
        'category' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
