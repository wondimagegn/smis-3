<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ForeignProgram Entity
 *
 * @property int $id
 * @property string $program
 * @property string $code
 * @property string|null $program_2nd_language
 * @property int $priority_order
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 */
class ForeignProgram extends Entity
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
        'program' => true,
        'code' => true,
        'program_2nd_language' => true,
        'priority_order' => true,
        'created' => true,
        'modified' => true,
        'accepted_students' => true,
    ];
}
