<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StaffStudy Entity
 *
 * @property int $id
 * @property int $staff_id
 * @property string $education
 * @property \Cake\I18n\FrozenDate $leave_date
 * @property \Cake\I18n\FrozenDate $return_date
 * @property bool $committement_signed
 * @property string $specialization
 * @property int $country_id
 * @property string $university_joined
 * @property bool $study_completed
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\Country $country
 */
class StaffStudy extends Entity
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
        'staff_id' => true,
        'education' => true,
        'leave_date' => true,
        'return_date' => true,
        'committement_signed' => true,
        'specialization' => true,
        'country_id' => true,
        'university_joined' => true,
        'study_completed' => true,
        'created' => true,
        'modified' => true,
        'staff' => true,
        'country' => true,
    ];
}
