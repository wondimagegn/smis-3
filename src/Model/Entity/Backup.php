<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Backup extends Entity
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
        'size' => true,
        'mime' => true,
        'operation_type' => true,
        'location' => true,
        'backup_taken' => true,
        'first_backup_taken_date' => true,
        'last_backup_taken_date' => true,
        'created' => true,
        'modified' => true,
    ];
}
