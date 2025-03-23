<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Note Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property int|null $college_id
 * @property int|null $department_id
 * @property \Cake\I18n\FrozenDate|null $published_date
 * @property \Cake\I18n\FrozenTime|null $start_date
 * @property \Cake\I18n\FrozenTime|null $end_date
 * @property string|null $user_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\User $user
 */
class Note extends Entity
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
        'title' => true,
        'content' => true,
        'college_id' => true,
        'department_id' => true,
        'published_date' => true,
        'start_date' => true,
        'end_date' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'department' => true,
        'user' => true,
    ];
}
