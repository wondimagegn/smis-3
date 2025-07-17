<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Attachment extends Entity
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
        'model' => true,
        'foreign_key' => true,
        'file' => true,
        'file_dir' => true,
        'file_size' => true,
        'file_type' => true,
        'size' => true,
        'checksum' => true,
        'group' => true,
        'alternative' => true,
        'created' => true,
        'modified' => true,
    ];
}
