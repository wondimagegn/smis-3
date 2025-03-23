<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentsSection Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $section_id
 * @property bool $archive
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Section $section
 */
class StudentsSection extends Entity
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
        'section_id' => true,
        'archive' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'section' => true,
    ];
}
