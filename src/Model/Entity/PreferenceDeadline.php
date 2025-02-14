<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PreferenceDeadline Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $deadline
 * @property string|null $academicyear
 * @property string|null $user_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $college_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\College $college
 */
class PreferenceDeadline extends Entity
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
        'deadline' => true,
        'academicyear' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'college_id' => true,
        'user' => true,
        'college' => true,
    ];
}
