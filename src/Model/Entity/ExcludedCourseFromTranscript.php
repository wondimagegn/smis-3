<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExcludedCourseFromTranscript Entity
 *
 * @property int $id
 * @property int $course_registration_id
 * @property int $course_exemption_id
 * @property string $minute_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\CourseRegistration $course_registration
 * @property \App\Model\Entity\CourseExemption $course_exemption
 */
class ExcludedCourseFromTranscript extends Entity
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
        'course_registration_id' => true,
        'course_exemption_id' => true,
        'minute_number' => true,
        'created' => true,
        'modified' => true,
        'course_registration' => true,
        'course_exemption' => true,
    ];
}
