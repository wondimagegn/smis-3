<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EheeceResult Entity
 *
 * @property int $id
 * @property string $subject
 * @property float $mark
 * @property \Cake\I18n\FrozenDate $exam_year
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class EheeceResult extends Entity
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
        'subject' => true,
        'mark' => true,
        'exam_year' => true,
        'student_id' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
