<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EquivalentCourse Entity
 *
 * @property int $id
 * @property int $course_for_substitued_id
 * @property int $course_be_substitued_id
 * @property \Cake\I18n\FrozenDate $created
 * @property \Cake\I18n\FrozenDate $modified
 *
 * @property \App\Model\Entity\CourseForSubstitued $course_for_substitued
 * @property \App\Model\Entity\CourseBeSubstitued $course_be_substitued
 */
class EquivalentCourse extends Entity
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
        'course_for_substitued_id' => true,
        'course_be_substitued_id' => true,
        'created' => true,
        'modified' => true,
        'course_for_substitued' => true,
        'course_be_substitued' => true,
    ];
}
