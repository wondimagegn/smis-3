<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClassRoomBlock extends Entity
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
        'college_id' => true,
        'campus_id' => true,
        'block_code' => true,
        'college' => true,
        'campus' => true,
        'class_rooms' => true,
    ];
}
