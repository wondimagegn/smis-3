<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prerequisite Entity
 *
 * @property int $id
 * @property int $course_id
 * @property int|null $prerequisite_course_id
 * @property bool $co_requisite
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\PrerequisiteCourse $prerequisite_course
 */
class Prerequisite extends Entity
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
        'course_id' => true,
        'prerequisite_course_id' => true,
        'co_requisite' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'prerequisite_course' => true,
    ];
}
