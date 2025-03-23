<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * University Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $amharic_name
 * @property string $short_name
 * @property string|null $amharic_short_name
 * @property string $academic_year
 * @property bool $applicable_for_current_student
 * @property string|null $p_o_box
 * @property string|null $telephone
 * @property string|null $fax
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class University extends Entity
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
        'name' => true,
        'amharic_name' => true,
        'short_name' => true,
        'amharic_short_name' => true,
        'academic_year' => true,
        'applicable_for_current_student' => true,
        'p_o_box' => true,
        'telephone' => true,
        'fax' => true,
        'created' => true,
        'modified' => true,
    ];
}
