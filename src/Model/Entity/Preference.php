<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Preference Entity
 *
 * @property int $id
 * @property int $accepted_student_id
 * @property string|null $academicyear
 * @property int|null $college_id
 * @property int $department_id
 * @property int|null $preferences_order
 * @property string|null $user_id
 * @property string|null $edited_by
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\AcceptedStudent $accepted_student
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\User $user
 */
class Preference extends Entity
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
        'accepted_student_id' => true,
        'academicyear' => true,
        'college_id' => true,
        'department_id' => true,
        'preferences_order' => true,
        'user_id' => true,
        'edited_by' => true,
        'created' => true,
        'modified' => true,
        'accepted_student' => true,
        'college' => true,
        'department' => true,
        'user' => true,
    ];
}
